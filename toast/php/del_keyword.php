<?php

if($_POST['verify'])
{
	include_once('config.php');
	include_once('conn.php');
	$uid = $_POST['fb_sig_user'];
	$feed_id = $_POST['feed_id'];
	$sql = "DELETE FROM feeds WHERE id = '$feed_id' LIMIT 1 ;";
	mysql_query($sql) or die(mysql_error());
	include_once('show_locker.php');
}
	
	
	
?>