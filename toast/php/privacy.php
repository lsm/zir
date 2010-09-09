<?php

require_once('facebook_init.php');
require_once('conn.php');
include_once('nav.php');

//TODO the logic is not good

if (!$_POST['verify']){
	//form not posted yet.

	if ($uid = $facebook->require_login() )
	{
		if (!isset($facebook->fb_params['in_canvas']) || $facebook->fb_params['in_canvas'] != '1')
		{//This will only work in facebook canvas.
			$facebook->redirect($appurl . preg_replace('/(\/.*\/)/','',$_SERVER['SCRIPT_NAME']));
		}

		$sql = "SELECT facebook_uid, last_name, facebook_session FROM users WHERE facebook_uid ='$uid';";
		$user = $facebook->api_client->users_getInfo($uid, 'first_name, last_name, has_added_app');
		////Alternative FQL method
		//$user = $facebook->api_client->fql_query("SELECT first_name, last_name, has_added_app FROM user WHERE uid = '$uid' ;");

		if ($user[0][has_added_app] == 1)
		{
			$result = mysql_query($sql) or die(mysql_error());

			if (mysql_num_rows($result) != 1)
			{//User has not registered in ENF, show registration form.
				var_dump($facebook->fb_params);
				show_reg_form('', $user);
			}
			else
			{
				$row = mysql_fetch_object($result);
				if ($facebook->fb_params['expires'] == '0')
				{//User has a infinite session.
					$update_db = empty($row->facebook_session) ? true : false;

					if(!empty($row->facebook_session) && $row->facebook_session != $facebook->fb_params['session_key'])
					{
						$update_db = true;
					}

					if ($update_db)
					{//Update db.
						var_dump($facebook->fb_params);
						$facebook_session = $facebook->fb_params['session_key'];
						$sql = "UPDATE users SET facebook_session = '$facebook_session' WHERE facebook_uid = '$uid' LIMIT 1 ;";
						$result = mysql_query($sql) or die(mysql_error());
						echo 'Your session key saved';
					}
					else 
					{//We have the key already. Greeting!
						
						var_dump($facebook->fb_params);
						echo 'Hi, you can make news feed in your locker!';
					}
				}
				else
				{//Probably, use has to generate a new one.
					var_dump($facebook->fb_params);
					show_session_form($facebook->api_key, $facebook->fb_params['expires']);
				}
			}
		}
		else
		{//ENF not installed, redirect~.
			$facebook->redirect($appurl);
		}
	}
}
elseif($_POST['type'] == 'reg')
{//reg form posted. Do db stuff
	include_once('conn.php');
	$uid = $_POST['fb_sig_user'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$email = $_POST['email'];
	$created = date('Y:m:d H:i:s', time());
	$sql = "INSERT INTO users VALUES( '', '$uid', '', '', '', '$email', '$first_name', '$last_name', '$created');";
	if ($result = mysql_query($sql))
	{
		echo 'Sign up successfully!';
	}
	else
	{
		if ($GLOBALS['facebook_config']['debug'] == 1)
		{
			echo mysql_error();
			echo $sql;
		}
	}
}
elseif ($_POST['type'] == 'session')
{//reg form posted. Do db stuff
	require_once('conn.php');
	require_once('facebook_init.php');

	$_GET['auth_token'] = $_POST['auth_token'];

	$uid = $facebook->require_login();

	if ($_POST['fb_sig_user'] == $uid)
	{
		$facebook_session = $facebook->api_client->session_key;
		$sql = "UPDATE users SET facebook_session = '$facebook_session' WHERE facebook_uid = '$uid' LIMIT 1 ;";
		if ($result = mysql_query($sql))
		{
			echo 'Session generated successfully!';
		}
		else
		{//TODO if debug on
			echo mysql_error();
			echo $sql;
		}
	}
}
else
{//Unexpected
	if ($GLOBALS['facebook_config']['debug'] == 1)
	var_dump($_POST);
}



//dashboard
//http://www.facebook.com/code_gen.php?v=1.0&api_key=YOUR_API_KEY
//http://www.facebook.com/login.php?api_key=YOUR_API_KEY&v=1.0&callback_url=
?>