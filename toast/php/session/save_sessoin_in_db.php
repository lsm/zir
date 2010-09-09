<?php
error_reporting(E_ALL | E_STRICT);


$sql = 'CREATE TABLE `sessions` (
`id` varchar(32) NOT NULL,
`modified` int(11) default NULL,
`lifetime` int(11) default NULL,
`data` text,
PRIMARY KEY (`id`)
)';

$sess_save_path = '/home/www/toast/session';
//ini_set('session.save_path', $sess_save_path);

//$link = mysqli_connect('localhost', 'root', '', 'toast');
//mysqli_query($link, 'SELECT * FROM sessions');

mysql_connect("localhost","root","");
mysql_select_db("toast");

function open($save_path, $session_name)
{
	error_log($session_name . " ". session_id());
}

function close()
{
    return(true);
}

function read($id)
{
	error_log($id);
	$stmt = "select data from sessions ";
	$stmt .= "where id ='$id' ";
//	$stmt .= "and unix_timestamp(lifetime) > unix_timestamp(date_add(now(),interval 1 hour))";
	$sth = mysql_query($stmt);

	if($sth)
	{
		$row = mysql_fetch_array($sth);
		return($row['data']);
	}
	else
	{
		return $sth;
	}
}

function write($id, $sess_data)
{
	error_log("$id = $sess_data");
	$sess_data = serialize($sess_data);
	$insert_stmt  = "insert into sessions values('$id', ";
	$insert_stmt .= "unix_timestamp(date_add(now(), interval 1 hour)), '', '$sess_data')";
	$time = time();
	$update_stmt  = "update sessions set session_data ='$sess_data', ";
	$update_stmt .= "modified = $time";
	$update_stmt .= "where id ='$id'";
	
	// First we try to insert, if that doesn't succeed, it means
	// session is already in the table and we try to update
	
	
	mysql_query($insert_stmt);
	
	$err = mysql_error();
	
	if ($err != 0)
	{
		error_log( mysql_error());
		mysql_query($update_stmt);
	}
}

function destroy($id)
{
	mysql_query("delete from sessions where id = '$id'");
}

function gc($maxlifetime)
{
	mysql_query("delete from sessions where unix_timestamp(modified) < unix_timestamp(now())");
    
}


session_set_save_handler("open", "close", "read", "write", "destroy", "gc");

session_start();

$_SESSION['test'] = 'Tsesting!';
// proceed to use sessions normally
?>
