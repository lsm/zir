<?php

require ('./include.php');

$mconn = memcache_connect("localhost", "11211");

timer();
// add
foreach($keys as $v) {
	memcache_add($mconn, $v, $content);
}
echo 'Save ' . $cycle . ' items in memcache spent: ' . timer() . "\n";

timer();
// get
foreach($keys as $v) {
	memcache_get($mconn, $v);
}
echo 'Get ' . $cycle . ' items from memcache spent: ' . timer() . "\n";


