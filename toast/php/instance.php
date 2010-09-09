<?php

abstract class father
{
	public function openCase()
	{
		$c = new cCase();
		$c->setWho($this);
	}
}

class child extends father
{
	public function openCase()
	{
		parent::openCase();
	}
}

class cCase
{

	public function setWho($who)
	{
		if ($who instanceof father) {
			echo 'ok;';
		}
	}
}


$x = new child();
$x->openCase();

?>
