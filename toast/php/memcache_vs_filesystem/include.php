<?php
$content = file_get_contents("./content.html");

// Start saving in memcache

$cycle = 10000;
$keys = array();

// prepare keys
for ($i = 0; $i < $cycle; $i++) {
	$keys[] = md5($i);
}

function timer()
{
    global $start;
    if ($start == null) {
        $start = microtime(true);
    } else {
        $t = microtime(true) - $start;
        $start = null;
        return $t;
    }
}

