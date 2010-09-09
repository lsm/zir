<?php

class DemoClass {

	public $publicVar = 1;
	protected $_protectedVar = 2;

	public function publicFunction() {
		echo "I'm a public function, and the value of \$publicVar is $this->publicVar.\n";
		echo "Call protected function! \n";
		$this->_protectedFunction();
	}

	protected function _protectedFunction() {
		echo "I'm a protected function, and the value of \$_protectedVar is $this->_protectedVar.";
	}
}
