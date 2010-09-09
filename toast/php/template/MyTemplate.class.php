<?php
/**
 *	Template engine.
 *
 * Features:
 *	
 *	1. Nested templates:
 *	This engine allow include templates directly in another template file
 *	and place his content in any other templates.
 * Not necessary to parse nested templates specialy. 
 * When you call parse method for template - all nested templates loadded from this one will be parsed.
 * To use nested templates include line such this:
 * <!-- LOAD template_handler file.name -->
 * Note: If you include file by relative path, keep in mind 
 * that path to file calculate from dir where your script is running,
 * NOT from dir where template, contains nested files, placed !
 * This only loads template file and make his content available to ANY other templates.
 * His content may be included as usual var - {template_handler}.
 * Note: Its a good idia to use some prefix for nested template handler. 
 * Its avoid from overwriting value by assignment variable with the same name.
 *  
 *	2. "Automagicaly" :) parsing of dinamic blocks.
 *	MyTemplate allow to use trees of dinamic blocks.
 *	No more parsing blocks 'by hand'. 
 *	Only set contents for blocks and get a result! 
 * For more details see:	MyTemplate::assignBlockVars(), Block::assignVars();
 * 
 * 3. Global PHP variables(scalar) and constants parsed into template w/o any assignments.
 * 
 * 4. Multiple template directories.
 * 
 * 5. Other features same as in other template engines (and maybe I forget some features :) ).
 * 
 * @package	MyTemplate
 * @version	1.0 10/12/02
 * @author	Dmitry Levashov	<dvl@scancode.ru> <dima@glagol.ru>
 * @copyright	lisence GPL
 */

class MyTemplate
{
	/**
	 * array of templates dirs
	 * @var	array		
	 */
	var $Dirs = array();
	/**
	 *	array of template file names
	 * @var	array	
	 */
	var $Files = array();
	/**
	 *	array of blocks (objects)
	 * @var	array	
	 */
	var $Blocks = array();
	/**
	 * array of templates content
	 * @var	array
	 */
	var $Templ = array();
	/**
	 * array of assigned vars names
	 * @var	array
	 */
	var $Vars = array();
	/**
	 * array of values of assigned vars
	 * @var	array
	 */
	var $Vals = array();
	/**
	 * string for undefined vars replacement
	 * @var	string
	 */
	var $_unassigned = '';
	/**
	 * Do we need to parse nested templates 
	 * any time we found his entrance
	 * @var	bool
	 */
	var $_repeat = false;
	
	/**
	 * The class ontructor. 
	 *
	 * By default use current dir as template dir
	 * 
	 * @param	string	$dir	templates dirname
	 */
	function MyTemplate($dir='.')
	{
		$this->setDir($dir);
	} 
	
	/**
	 * Set value for all unassigned variables.
	 *
	 * Usefull for debug.
	 * To see variable name use back reference,
	 * for example $Templ->setUnassign("! undefine \\1 !")
	 * 
	 * @param	string	$str
	 */
	function setUnassign($str = '')
	{
		$this->_unassigned = $str;
	}
	
	/**
	 * Set mode of parsing nested templates.
	 *
	 * If set to true - nested templates will be parsed 
	 * for each their entrances in other templates.
	 * By default this flag is set to false.
	 *
	 * @param	bool	$repeat
	 */
	function repeatNestedTempl($repeat=true)
	{
		$this->_repeat = $repeat;
	}
	/**
	 * Add dir to array of templates dirs. 
	 *
	 * dir can be directory name aor array of dirnames
	 * 
	 * @param	mixed	$dir	templates dirname
	 */
	function setDir($dir)
	{
		if (!$dir) return;
		
		if (!is_array($dir)) {
			if (is_dir($dir))
				$this->Dirs[] = realpath($dir);
			else
				$this->_error("setDir: $dir is not a dir");
		} else {
			foreach ($dir as $d) {
				if (is_dir($d))
					$this->Dirs[] = realpath($d);
				else
					$this->_error("setDir: $d is not a dir");
			}
		}
	} 

	/**
	 * Add template file. 
	 *
	 * Can add one file or array of files.
	 * File name can be set in 3ways - by absolute path, 
	 * by relative path from current dir or simply by file name, 
	 * in this case file will be searched in all template dirs ($this->Dirs)
	 * 
	 * @param	mixed   $handle  template handler or array(handle=>file)
	 * @param	string  $file    filename
	 */
	function setFile($handle, $file='')
	{
		if (!is_array($handle)) {
			$this->Files[$handle] = $this->_getFileName($file);
			$this->_loadFile($handle);				
		} else {
			foreach ($handle as $h=>$file) {
				$this->Files[$h] = $this->_getFileName($file);
				$this->_loadFile($h);
			}
		}
	} 

	/**
	 * Variable assignment. 
	 * 
	 * @param	mixed		$var	variable name or array of variables
	 * @param	string	$val	variable value
	 * @param	bool		$append append new value to var value
	 */
	function assignVars($var, $val='', $append=false) 
	{
		if (!is_array($var)) {
			if (!$var)	$this->_error("setVar: variable without name");
			$this->Vars[$var] = '{'.$var.'}';
			$this->Vals[$var] = (!$append) ? $val : $this->Vals[$var] . $val;
		} else {
			foreach ($var as $name=>$val) {
				$this->Vars[$name] = '{'.$name.'}';
				$this->Vals[$name] = (!$append) ? $val : $this->Vals[$var] . $val;
			}
		}
	} 

	/**
	 * Clean variable value.
	 * 
	 * @param	string	$var	var name
	 */
	function cleanVar($var) 
	{ 
		if (isset($this->Vals[$var]))
			$this->Vals[$var] = '';
	} 

	/**
	 * Assign variables for block.
	 * 
	 * Param path contain path to nested block in format "PARENT.CHILD1.CHILD2" or top-level block name.
	 * Level - level of nested blocks.
	 * This param tells all blocks with level >= $level to create new iteration.
	 * So you can set variables for one block iteration several times.
	 * Top-level block has level = 1.
	 *  
	 * @param	string	$path	path to block
	 * @param	array		$vars	variables 
	 * @param	int		$level	level
	 */
	function assignBlockVars($path, $vars, $level=0)
	{
		$block = (($pos = strpos($path, '.')) !== false) 
				? substr($path,0,$pos)	: $path;
		
		if (!$this->Blocks[$block])
			$this->_error("setBlockVar: Block $block does not exists");
		
		$this->Blocks[$block]->assignVars($path, $vars, $level);
	} 
	
	/**
	 * Parse template into var. 
	 *
	 * If target is not set, parse into variable with name of template handler.
	 * 
	 * @param	string	$handle	template handler
	 * @param	string	$target	variable name to parse in
	 * @param	bool		$append	append parsed string to variable	 
	 */
	function parse($handle, $target = '', $append = false)
	{	
		if (!isset($this->Templ[$handle]))
			$this->_error("parse: no template $handle to parse");

		// search for nested teplates
		foreach ($this->Files as $h=>$file) {
			if ($h != $handle && strstr($this->Templ[$handle], '{'.$h.'}')) {
				if (!$this->Vals[$h] || $this->_repeat) {
					$this->parse($h);
				}
			}
		}
	
		$parsed = str_replace($this->Vars, $this->Vals, $this->Templ[$handle]);

		if ($this->FileBlocks[$handle]) {
			foreach ($this->FileBlocks[$handle] as $block) {
				$parsed = str_replace('{BLOCK.'.$block.'}', $this->Blocks[$block]->parse(), $parsed);
			}
		}

		if (!$target) $target = $handle;
		$this->assignVars($target, $parsed, $append);
	} 

	/**
	 * print value of variable 
	 * 
	 * @param	string	var name
	 */
	function fprint($handle)
	{
		if (!isset($this->Vals[$handle])) {
			if ($this->Templ[$handle])
				$this->parse($handle);
			else 
				$this->_error("fprint: Guess you miss? :)<br>You forget to define $handle");
		}
		$this->_complite($handle);
		echo $this->Vals[$handle];
	} 

	/**
	 * Parse constants and global variables defined in template
	 * and replace any rest unassigned vars with $this->undef string
	 * 
	 * Note: constants must define in template as {PHP.CONSTANT_NAME},
	 * global variables - as {PHP.$var_name}.
	 *
	 * @param	string	$handle	template handler
	 */
	function _complite($handle)
	{
					
		$search = array('/{PHP.(\w+)}/e', '/{PHP.\\$(\w+)}/e', '/{([a-z0-9_]+)}/i'); 
		$replace = array("\\1", "\$GLOBALS[\\1]", $this->_unassigned);
		$this->Vals[$handle] = preg_replace($search, $replace, $this->Vals[$handle]);
	}
	
	/**
	 * Return absolute path to file, if file exists.
	 * 
	 * @param	string	$file	filename
	 * @return	string
	 */
	function _getFileName($file)
	{
		if (!$file)		$this->_error("_getFileName: file $file does not exists");
			
		if (substr($file,0,1) =='/') { // absolute path

			if (is_file($file)) return $file;
			
		} elseif (substr($file,0,1) == '.') { // relative path
			
			$rfile = realpath($file); 
			if (is_file($rfile))	return $rfile;
			
		} else { // search file in template dirs

			foreach ($this->Dirs as $dir) {
				if (is_file($dir.'/'.$file)) return $dir . '/' . $file;
			}
		}

		$this->_error("_getFileName: file $file does not exists");
	} 

	/**
	 * Load template content into $Templ
	 * 
	 * @param	string	$handle	template file handler
	 */
	function _loadFile($handle)
	{
		if (!$this->Files[$handle]) 
			$this->_error("loadFile: File with handle $handle does not exists");
		
		if ($this->Templ[$handle]) // file always loaded
			return;
		
		if ( ($this->Templ[$handle] = implode('', @file($this->Files[$handle]))) == '' )
			$this->_error("loadFile: File ".$this->Files[$handle] ." is empty");
	
		$this->_loadInclude($handle); // search included templates
		$this->_cutBlocks($handle);	// search blocks
	} 

	/**
	 * Load nested templates.
	 * 
	 * Search template content for nested templates and load its.
	 * 
	 * @param	string	$handle	template handler
	 * @acces	private
	 */
	function _loadInclude($handle)
	{	
		$reg = '/<!--\\s+LOAD\\s+([a-z0-9_]+)\\s+([a-z0-9_\.\/]+)\\s+-->/si';
		
		if (preg_match_all($reg, $this->Templ[$handle], $m)) {
			
			foreach ($m[1] as $i=>$h) {
				$this->setFile($h, $m[2][$i]);
			}
			
		}
		
	} 
	
	/**
	 * Scan template content for dinamic blocks.
	 *
	 * Block defines as "<!-- BEGIN block_name --> here {block} content <!-- END block_name -->".
	 * If any one found, for each top-level block create object Block and give him his content.
	 * Block scan his content for nested blocks and create childs objects 
	 * for each his top-level block, and so on so on...
	 * @param	string	$handle	template handler	 
	 */
	function _cutBlocks($handle)
	{
		$reg = "/<!--\\s+BEGIN\\s+([a-z0-9_]+)\\s+-->(.*)\n\s*<!--\\s+END\\s+(\\1)\\s+-->/smi";
		if (preg_match_all($reg, $this->Templ[$handle], $m)) {

			for ($i=0; $i<sizeof($m[1]); $i++) {
				$this->Blocks[$m[1][$i]] = new Block($m[1][$i], $m[2][$i]);
				$this->FileBlocks[$handle][] = $m[1][$i];	// bind block name to file hadler
				$this->Templ = str_replace($m[0][$i], '{BLOCK.'.$m[1][$i].'}', $this->Templ);
			}	
		}
	} 
	
	/**
	 * Print error message [and halt script]
	 * @param	string	$msg	eroor message
	 * @param	bool		$halt	halt script or not?
	 */
	function _error($msg, $halt=true) {
		if ($halt)
			die('MyTemplate ' . $msg);
		else 
			echo ('MyTemplate: '.$msg.'<br>');
	} 

}

/**
 * Auxiliary class for MyTemplate for dinamic block manipulations. 
 * Dont use it directly.
 * @access	privare
 */

class Block
{
	/**
	 * Block name
	 * @var	string
	 */
	var $Name;
	/**
	 * Block content.
	 * @var	string
	 */
	var $Content;
	/**
	 * Array of nested top-levels blocks
	 * @var	array
	 */
	var $Childs = array();
	/**
	 * Block variables array.
	 * Var[key][inum] = array('vars'=>array(), 'vals'=>array())
	 * key - key of parents iteration.
	 * inum - number of iteration in current parent iteration
	 * vars - array of variables names
	 * vals - array of variable values
	 * @var	array
	 */
	var $Vars = array();

	/**
	 * Class contructor.
	 * Search content for nested blocks and create childs object for each top-level block.
	 * &#1050;&#1086;&#1085;&#1089;&#1090;&#1088;&#1091;&#1082;&#1090;&#1086;&#1088;. &#1059;&#1089;&#1090;&#1072;&#1085;&#1072;&#1074;&#1083;&#1080;&#1074;&#1072;&#1077;&#1090; &#1080;&#1084;&#1103; &#1073;&#1083;&#1086;&#1082;&#1072; &#1080; &#1082;&#1086;&#1085;&#1090;&#1077;&#1085;&#1090;. 
	 * 
	 * @param	string	$name	block name
	 * @param	string	$cont	block content
	 */
	function Block($name, &$cont)
	{	
		$this->Name = $name;
		$this->Content = &$cont;

		$reg = "/<!--\\s+BEGIN\\s+([a-z0-9_]+)\\s+-->(.*)\n\s*<!--\\s+END\\s+(\\1)\\s+-->/smi";
		if (preg_match_all($reg, $this->Content, $m)) {
			for ($i=0; $i<sizeof($m[1]); $i++) {
				$this->Childs[$m[1][$i]] = new Block($m[1][$i], $m[2][$i]);
				$this->Content = str_replace($m[0][$i], '{BLOCK.'.$m[1][$i].'}', $this->Content);
			}	
		}
	} 

	/**
	 * Set block variables.
	 *
	 * If variables addressed to this block - set its.
	 * Otherwise pass data to child block.
	 * While passed data to child, decrease level by 1 
	 * and add to parent key number of his iteration.
	 * 
	 * @param	string	$name	block name or path to block
	 * @param	array		$data	block variables
	 * @param	int		$level	on wich level create new iteration
	 * @param	string	$key	parent iteration key
	 */
	function assignVars($name, &$data, $level=0, $key='0') 
	{
		// Iteration number 'inside' parent iteration 
		// level <= 0 means this block must create new iteration
		// level > 0 - data should be addded to previous iteration,
		// if there is no previous iteration - create it
		if ($level <= 0 || !sizeof($this->Vars[$key])) {
			$this->Vars[$key][] = array();
		}
		$inum = sizeof($this->Vars[$key])-1;
		
		if ($name == $this->Name) { // data addressed to this block
			if (is_array($data)) {
				foreach ($data as $var=>$val) {
					$this->Vars[$key][$inum]['vars'][$var] = '{'.$var.'}';
					$this->Vars[$key][$inum]['vals'][$var] = $val;
				}

			}
			return;
		}
		// data addressed to child block
		$name = explode('.', $name);
		unset($name[0]); // delete this block name from path

		if (!$this->Childs[$name[1]]) { // child block not exists - show warning
			return MyTemplate::_error("BLOCK $this->Name: ERROR Cant find child ".$name[1]."<br>", 0);
		}
		// pass data to child
		$this->Childs[$name[1]]->assignVars(implode('.', $name), $data, $level-1, $key.'_'.$inum);
	} 		
	
	/**
	 * Parse content and return it.
	 * 
	 * Replace childs blocks declarations to childs parsed content
	 * 
	 * @param	string	$key parent iteration key
	 * @return	string
	 */
	function parse($key='0') 
	{
		$res = '';
		
		if (!isset($this->Vars[$key])) { // we have not iterations for this key
			return $res;
		}
		
		foreach ($this->Vars[$key] as $i=>$vars) {
			
			$str = str_replace($vars['vars'], $vars['vals'], $this->Content);
			
			foreach ($this->Childs as $name=>$Child) {
				$str = str_replace('{BLOCK.'.$name.'}', $this->Childs[$name]->parse($key .'_'.$i), $str);
			}
			
			$res .= $str;
		}	
		
		return $res;	
	}
}

?>
