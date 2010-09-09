<?php
$t = microtime(true);
for ($i=0;$i<1000;$i++) {
	str_len('aaa');
}
echo microtime(true) - $t . "\n";

$t = microtime(true);
for ($i=0;$i<1000;$i++) {
	strlen('aaa');
}
echo microtime(true) - $t . "\n";

function mbstrlen($str) {
	mb_strlen($str);
}

function str_len($str) {
	strlen($str);
}