<?php
if (isset($_POST['action']))
{
	include_once('include/functions.php');
	include_once('include/conn.php');
	
	$action = stripslashes(strval(mosGetParam($_POST, 'action')));
	
	if (isset($_POST['query']) && isset($_POST['title']) && $action == 'add')
	{
		$query = strval(mosGetParam($_POST, 'query', 0));
		$source = strval(mosGetParam($_POST, 'source', 'google'));
		$title = strval(mosGetParam($_POST, 'title', 0));
		$sql = "INSERT INTO news VALUES ('' ,'$query', '$source', '$title');";
	}
	elseif ($action == 'del' && isset($_POST['news_id']))
	{
		$news_id = strval(mosGetParam($_POST, 'news_id'));
		$sql = "DELETE FROM `news` WHERE `news`.`id` = '$news_id' LIMIT 1;";
	}
	else
	{
		exit('error');
	}
	mysql_query($sql);
}
?>
