<?php
class EchoTemplate
{
	public	$segments;
	public	$mainContent;
	public 	$templateTags = array();

	const SEGMENT_TYPE_SELF = 0;
	const SEGMENT_TYPE_FILE = 1;
	const SEGMENT_TYPE_BLOCK = 2;
	const SEGMENT_TYPE_VAR = 3;

	const FILE_PATH_BASE = 0;
	const FILE_PATH_THIS = 1;

	public $tplBasePath = '';
	public $tplScriptPath = '';
	public $tplSuffix = 'phtml';


	public function parse($file)
	{

		$this->segments = $this->_preParse($file, $this->mainContent);

		foreach ($this->segments as $idx => $segment)
		{
			if($segment['type'] === self::SEGMENT_TYPE_FILE)
			{
				if($segment['path'] === self::FILE_PATH_BASE) {
					$fileToParse = $this->tplBasePath
					. $segment['name'] . '.' . $this->tplSuffix;
				} else {
					$fileToParse = $this->tplBasePath . $this->tplScriptPath
					. $segment['name'] . '.' . $this->tplSuffix;
				}
				$segment['content'] = $this->_fileParse($fileToParse);
				$this->_saveTags($segment['real_name']);
			}
			elseif($segment['type'] === self::SEGMENT_TYPE_VAR)
			{
				$this->mainContent = $this->_varParse($segment, $this->mainContent);
			}
			elseif($segment['type'] === self::SEGMENT_TYPE_BLOCK)
			{
				$this->mainContent = $this->_blockParse($segment, $this->mainContent);
			}
			$this->segments[$idx] = $segment;
		}

		$this->_postParse();

		return $this->mainContent;

	}

	private function _fileParse($name)
	{

		$segments = $this->_preParse($name, & $content);

		if(is_array($segments))
		{
			foreach ($segments as $idx => $segment)
			{
				if($segment['type'] === self::SEGMENT_TYPE_VAR)
				{
					$content = $this->_varParse($segment, $content);
				}
				elseif($segment['type'] === self::SEGMENT_TYPE_BLOCK)
				{
					$content = $this->_blockParse($segment, $content);
				}
			}
		}
		return $content;
	}

	private function _varParse($segment, $content)
	{
		if(isset($this->$segment['name']))
		$content = str_replace($segment['real_name'], $this->$segment['name'], $content);
		$this->_saveTags($segment['real_name']);
		return $content;
	}

	private function _preParse($name, & $content)
	{
		$file = & file_get_contents($name, true);
		$content = $file;

		preg_match_all('/{[$_\[\]a-zA-Z0-9]+}/', $file, $j);

		if(is_array($j[0]) && !empty($j[0])) {

			for ($i = 0 ; $i < count($j[0]) ; $i++) {

				if(preg_match('/^{[a-z]+}$/', $j[0][$i])) {
					$segments[$i]['name'] = preg_replace('/(^{)|(}$)/','',$j[0][$i]);
					$segments[$i]['type'] = self::SEGMENT_TYPE_FILE;
					$segments[$i]['path'] = self::FILE_PATH_BASE;
					$segments[$i]['real_name'] = $j[0][$i];
				}

				if(preg_match('/^{_[a-z]+}$/', $j[0][$i])) {
					$segments[$i]['name'] = preg_replace('/(^{)|(}$)/','',$j[0][$i]);
					$segments[$i]['type'] = self::SEGMENT_TYPE_FILE;
					$segments[$i]['path'] = self::FILE_PATH_THIS;
					$segments[$i]['real_name'] = $j[0][$i];
				}

				if(preg_match('/^{\[_[a-z]+\]}$/', $j[0][$i], $type)) {
					$segments[$i]['name'] = preg_replace('/(^{\[_)|(\]}$)/','',$j[0][$i]);
					$segments[$i]['type'] = self::SEGMENT_TYPE_BLOCK;
					$segments[$i]['open'] = $j[0][$i];

					$cur = $i;
					$name = $segments[$i]['name'];

					for($n = ++$i; preg_match('/^{\['.$name.'_\]}$/', $j[0][$n]) == false; $n++, $i++) {
						if(preg_match('/^{\$[a-zA-Z0-9]+}$/', $j[0][$n])) {
							$keys[] = preg_replace('/(^{\$)|(}$)/','',$j[0][$i]);
						}
					}
					$segments[$cur]['close'] = $j[0][$n];
					$segments[$cur]['keys'] = $keys;
					$keys = null;
				}

				if(preg_match('/^{\$[a-zA-Z0-9]+}$/', $j[0][$i])) {
					$segments[$i]['name'] = preg_replace('/(^{\$)|(}$)/','',$j[0][$i]);
					$segments[$i]['type'] = self::SEGMENT_TYPE_VAR;
					$segments[$i]['real_name'] = $j[0][$i];
				}

			}

			return $segments;
		}
	}

	private function _saveTags($tag)
	{
		if(!in_array($tag, $this->templateTags))
		$this->templateTags[] = $tag;
	}

	private function _blockParse($segment, $content)
	{
		//@todo:multiple nest blocks
		$open = strpos($content, $segment['open']);
		$close = strpos($content, $segment['close']);
		$tagLen = strlen($segment['open']);

		//$blockPattern = substr($content, $open, $close - $open + $tagLen);
		$blockPatternWithoutTag = substr($content, $open + $tagLen, $close - $open - $tagLen);

		$replace = '';
		if(isset($this->$segment['name']) && is_array($this->$segment['name']))
		{
			foreach ($this->$segment['name'] as $block)
			{
				$tmp = $blockPatternWithoutTag;
				if(is_array($segment['keys'])) {
					foreach($segment['keys'] as $key)
					{
						if(array_key_exists($key, $block))
						{
							$tmp = str_replace('{$' . $key . '}', $block["$key"], $tmp);
						}
						else
						{
							//@todo: throw exceptions, or replace.
							$tmp = str_replace('{$' . $key . '}', 'VALUE_NOT_DEFINED', $tmp);
						}
					}
				}
				$replace .= $tmp;
			}
		}
		$content = substr_replace($content, $replace, $open, $close - $open + $tagLen);
		$this->_saveTags($segment['open']);
		$this->_saveTags($segment['close']);
		return $content;
	}

	private function _postParse()
	{
		foreach($this->segments as $segment)
		{
			if(isset($segment['content']))
			{
				$this->mainContent = str_replace($segment['real_name'], $segment['content'], $this->mainContent);
			}
		}

		foreach($this->templateTags as $tag)
		{
			$this->mainContent = str_replace($tag, '', $this->mainContent);
		}
	}
	
}
?>