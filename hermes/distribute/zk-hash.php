<?php

$numServers = 10;
$servers = array();

for ($i = 0; $i < $numServers; $i++) {
    $servers[] = 1;     
}

unset($servers[5]);

$numKeys = 80000;
$cacheSet1 = array();
for ($i = 0; $i < $numKeys; $i++) {
    $cacheSet1[selectServer($i, $servers)][] = $i;
}

foreach ($cacheSet1 as $k => $v) {
    echo 'Items in ' . $k . ' ' . count($v) . "\n";
}

$cacheSet2 = array();
$servers[5] = 1;
for ($i = 0; $i < $numKeys; $i++) {
    $cacheSet2[selectServer($i, $servers)][] = $i;
}

// compare with original separation
foreach ($cacheSet1 as $k => $v) {
    echo 'Differences after slot added ' . count(array_diff($v, $cacheSet2[$k])) . " for $k\n";
}

foreach ($cacheSet2 as $k => $v) {
    echo 'Items in ' . $k . ' ' . count($v) . "\n";
}


function selectServer($sKey, $servers)
{
	if (count($servers) == 1) {
            return 0;
	}

    $iMax = array_sum($servers);

		// ??
    $fRate = sprintf("%u", crc32($sKey)) / 4294967295;

		// ????
	$fRateNow = 0;
	foreach ($servers as $iServer => $iWeight) {
		$fRateNow += $iWeight;
		if (($fRateNow / $iMax) > $fRate) {
			break;
		}
	}
    return $iServer;
}
?>