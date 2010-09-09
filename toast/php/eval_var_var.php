<?php

class echoTemplate
{
	public $_this = '$this';
	

	function _this()
	{
		$this->docRoot = $_SERVER['DOCUMENT_ROOT'];
		var_dump($this->_this);
		eval('echo '.$this->_this.'->docRoot;');
	}
}


$a = new echoTemplate();
$a->_this();

//$a->_this
eval('echo $a->docRoot;');








?>