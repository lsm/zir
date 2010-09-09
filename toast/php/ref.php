<?php
function x()
{
    return 'x';
}
$a = "hello";
$c = 'c';
$b = & $a;
$b = & x();
//unset($b);
//echo $b;
//echo $a;
echo $b;
//var_dump($b);
//$b = "world";
//echo $a;
?>