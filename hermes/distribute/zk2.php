<?php


$numServers = 9;
$servers = array();


for ($i = 0; $i < $numServers; $i++) {
   $servers[] = 1;
}


// unset($servers[5]);


$numKeys = 80000;
$cacheSet1 = array();
for ($i = 0; $i < $numKeys; $i++) {
   $cacheSet1[selectServer($i, $servers)][] = $i;
}
ksort($cacheSet1);


foreach ($cacheSet1 as $k => $v) {
   echo 'Items in ' . $k . ' ' . count($v) . "\n";
}


$cacheSet2 = array();
$servers[9] = 1;
for ($i = 0; $i < $numKeys; $i++) {
   $cacheSet2[selectServer($i, $servers)][] = $i;
}

$j = 0;
// compare with original separation
foreach ($cacheSet1 as $k => $v) {
	$iCount = count(array_diff($v, $cacheSet2[($k <= ceil(($numServers +
1) / 2) ? $k : $k + 1)]));
	$j += $iCount;
   echo 'Differences after slot added ' . $iCount . " for $k\n";
}

echo "\n [ ".$j." ] \n";


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