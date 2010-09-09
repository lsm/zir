<?php

$key = 121212;
srand($key);
$servers = array('server1', 'server2', 'server3', 'server4', 'server5');

$server_set1 = $servers;

echo 'Test 1: Adding servers', "\n";
echo 'Before new servers added, we chose server "', shuffle($server_set1), '" for key "', $key . "\"\n";
var_dump($server_set1);
echo 'Adding server6, server7, server8', "\n";
$servers[] = 'server6';
$server_set2 = $servers;
$servers[] = 'server7';
$server_set3 = $servers;
$servers[] = 'server8';
$server_set4 = $servers;
$servers[] = 'server9';
$server_set5 = $servers;
$servers[] = 'server10';
$server_set6 = $servers;

shuffle($server_set2);
shuffle($server_set3);
shuffle($server_set4);
shuffle($server_set5);
shuffle($server_set6);

var_dump($server_set2, $server_set3, $server_set4, $server_set5, $server_set6);

echo "Removing server \n";
//var_dump(rm_server($servers, 7));

//var_dump(rm_server($servers, 6));
//var_dump(rm_server($servers, 5));
//var_dump(rm_server($servers, 4));
//var_dump(rm_server($servers, 3));









function rm_server($list, $num)
{
    unset($list[$num]);
    return $list;
}


?>