<?php

class Test
{

	const I_AM_CONST = 'Value of const memeber.';
}

$ref = 'Test::I_AM_CONST';
var_dump($ref);
$className = 'Test';
$constName = 'I_AM_CONST';
var_dump(Test::I_AM_CONST);
var_dump(Test::$constName);
?>
