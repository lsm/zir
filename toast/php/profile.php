<?php
require_once('config.php');

if (isset($_REQUEST['keyword']))
{
	if ($_REQUEST['engine'] == 'baidu')
	{
		$url ='http://news.baidu.com/ns?tn=newsrss&sr=0&cl=2&rn=5&ct=0&word=';
	}
	else
	{
		$url = 'http://news.search.yahoo.com/news/rss?ei=UTF-8&fl=0&x=wrt&p=';
	}

	$keyword = ($_REQUEST['title'] == 1) ? 'title:'.$_REQUEST['keyword'] : $_REQUEST['keyword'];
	$query['keyword'] = $_REQUEST['keyword'];
	$query['engine'] = $_REQUEST['engine'];
	$query['title'] = $_REQUEST['title'];
	$query['add_profile'] = 0;

	$result = get_rss($keyword, $url);
	echo show_news($result, $query);
	echo date('Y:m:d H:i:s', time());
}
elseif (isset($_REQUEST['uid']))
{
	require_once('facebook_init.php');
	require_once('conn.php');
	$content = get_profile($_REQUEST['uid']) . date('Y:m:d H:i:s', time());
	$facebook->api_client->profile_setFBML( $content, $uid );
	echo '<p>Your profile updated. <a href="http://www.facebook.com/profile.php">My profile.</a> '.date('Y:m:d H:i:s', time()).'</p>';
}
else
{
	var_dump($_REQUEST);
}
?>