<?php

set_time_limit(0);
require_once('flexihash-0.1.9.php');

//$hash = new Flexihash(new Flexihash_Md5Hasher);
//$hash = new Flexihash(new Flexihash_Sha1);
$hash = new Flexihash(new Flexihash_Crc32Hasher);

$numSlots = 9;
// bulk add servers/slots
for($i = 0; $i < $numSlots; $i++) {
    $hash->addTarget("slot-$i");    
}


// simple lookup
$numKeys = 80000;
$cacheSet1 = array();
for ($i = 0; $i < $numKeys; $i++) {
    $cacheSet1[$hash->lookup($i)][] = $i;
}

foreach ($cacheSet1 as $k => $v) {
    echo 'Items in ' . $k . ' ' . count($v) . "\n";
}

// add another slot
$hash->addTarget("slot-$numSlots");
$cacheSet2 = array();
for ($i = 0; $i < $numKeys; $i++) {
    $cacheSet2[$hash->lookup($i)][] = $i;
}

// compare with original separation
foreach ($cacheSet1 as $k => $v) {
    echo 'Differences after slot added ' . count(array_diff($v, $cacheSet2[$k])) . " for $k\n";
}

foreach ($cacheSet2 as $k => $v) {
    echo 'Items in ' . $k . ' ' . count($v) . "\n";
}
