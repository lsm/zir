<?php
error_reporting(E_ALL);
require_once('config.php');

require_once('news_feed.php');

$enf = new NewsFeed($rss_op, $facebook_op);

$query_op['engine'] = 'baidu';
$query_op['num'] = 20;
$query_op['title'] = 1;

$enf->show_news('公开信', $query_op);



?>