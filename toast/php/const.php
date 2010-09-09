<?php
class test 
{
	const t_const = 1111111;
	public static $t_static = 22222;
}
define('TEST', 123423523);
var_dump(TEST);
var_dump(TEST . '= x');
echo "test::t_const";
echo test::$t_static;

