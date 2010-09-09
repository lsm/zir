<?php
$GLOBALS['facebook_config']['debug'] = 0;
error_reporting(E_ALL);
include_once('css.php');
require_once('functions.php');
require_once('news_feed.php');

//rss setting
$rss_op['CDATA'] = 'strip';
$rss_op['cache_dir'] = 'cache/';
$rss_op['cache_time'] = 3600;
$rss_op['code_page'] = 'utf-8';

//facebook setting
$facebook_op['api_key'] = '2812abca767244fe28cd9d46cc1d0961';
$facebook_op['secret'] = '15bf54813ea6392a7ce97c8b0a968f6c';
$facebook_op['callbackurl'] = 'http://www.oscan.org/gnews/';
//$appurl = 'http://apps.facebook.com/enewsfeed/';

//db
$hostname = 'localhost';
$db_user = 'system';
$db_pass = '12q\=]vbn';
$db_name = 'zai_newsfeed';
?>
