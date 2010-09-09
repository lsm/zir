<?php

require_once('config.php');

if (isset($_REQUEST['keyword']) && isset($_REQUEST['engine']))
{
/*	if ($_REQUEST['engine'] == 'baidu')
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
	$query['add_profile'] = ($_REQUEST['add_profile'] == 0) ? 0 : 1;

	$result = get_rss($keyword, $url);
	echo show_news($result, $query);*/

	$enf = new NewsFeed($rss_op, $facebook_op);

	$query_op['engine'] = $_REQUEST['engine'];
	$query_op['num'] = 10;
	$query_op['title'] = $_REQUEST['title'];
	$query_op['add_profile'] = $_REQUEST['add_profile'];

	$enf->show_news($_REQUEST['keyword'], $query_op);
}
elseif (isset($_REQUEST['page']))
{//TODO: remove
	$url = 'http://news.baidu.com/n?cmd=1&tn=rss&sub=0&class=';
	$result = get_rss($_REQUEST['page'], $url);
	echo show_news($result);
}
else
{//Unexpected
	if ($GLOBALS['facebook_config']['debug'] == 1)
	var_dump($_REQUEST);
}
?>
