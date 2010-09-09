<?php

if($_POST['verify'])
{
	include_once('config.php');
	include_once('conn.php');

	$uid = $_POST['fb_sig_user'];
	$engine = $_POST['engine'];
	$keyword = $_POST['keyword'];
	$created = date('Y:m:d H:i:s', time());

	$sql = "SELECT facebook_uid, id, facebook_session FROM users WHERE facebook_uid='$uid';";
	$result = mysql_query($sql) or die(mysql_error());
	
	if (mysql_num_rows($result) != 1)
	{
		echo 'Please <a href="privacy.php">register</a> first!';
		exit();
	}
	else
	{
		$row = mysql_fetch_object($result);
		if (!empty($row->facebook_session) && $row->facebook_session == $_POST['fb_sig_session_key'])
		{
			$sql = "INSERT INTO `feeds` ( `id` , `user_id` , `engine` , `keyword` , `created` , `modified` )"
			. " VALUES (NULL , '$uid', '$engine', '$keyword', '$created' , '');";
			mysql_query($sql) or die(mysql_error());
			include('show_locker.php');
		}
		else
		{
			echo  'Please <a href="privacy.php">get a \'infinite session\'</a> first!';
			exit();
		}
	}
}



?>