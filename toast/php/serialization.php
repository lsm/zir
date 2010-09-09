<?php
include('rss.php');
$rss = new lastRSS();

$s = serialize($rss);
var_dump($s);
var_dump($rss);

?>