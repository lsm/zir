<?php

$seed = crc32(1);
srand($seed);
// Increase the array subsequently a,b,c,d,e,f ....
$a = array('a', 'b', 'c');
shuffle($a);
var_dump($a);

