<?php

mysql_connect('127.0.0.1', 'root', '123qwe');
mysql_select_db('ng');

$leech = "http://jaxer/zir/ng/leecher/leech.php?";

$task = $_REQUEST['task'];

if ($task == 'paging') {
	$query = "SELECT * FROM items WHERE type = 'paging' LIMIT 5;";
	query($query, $leech);
}

if ($task == 'content') {
	$query = "SELECT * FROM items WHERE type = 'content' LIMIT 5;";
	query($query, $leech);
}

if ($task == 'list') {
	$channel = $_REQUEST['channel'];
	$rule_name = $_REQUEST['rule_name'];
	$type = 'list';
	$url = $leech . "channel=$channel&rule_name=$rule_name&type=$type";
	echo file_get_contents($url);
}


function query($query, $leech) {
$res = mysql_query($query);

while ($obj = mysql_fetch_object($res)) {
    $url = $leech . "url=$obj->url&channel=$obj->channel&rule_name=$obj->rule_name&type=$obj->type";
		var_dump($url);
    echo file_get_contents($url);
}	
}

?>
