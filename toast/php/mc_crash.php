<?php

$mc = new Memcache();
$mc->addServer("mc1.host.tiger", 11211);
$mc->addServer("mc1.host.tiger", 11212);

// for($i = 0; $i < 10; $i++){
//     $mc->set("k" . $i, date("Y-m-d H:i:s") . "@" . $i);
// }

$keys = array(
    "k0",
    "k1",
    "k2",
    "k3",
    "k4",
    "k5",
    "k6",
    "k7",
    "k8",
    "k9",
);

$res = $mc->get($keys);
// foreach($keys as $k){
    // $res = $mc->get($k);
// }

foreach($keys as $key){
    echo $res[$key] . "\n";
}

echo "\n---\n";

