<?php
include_once('facebook_init.php');

//$added = $facebook->api_client->users_getInfo($facebook->user, 'has_added_app');
//$facebook->redirect('http://www.facebook.com/login.php?api_key=2812abca767244fe28cd9d46cc1d0961&v=1.0');

if (!$facebook->fb_params['added'])
{
	include_once('install.php');
}
elseif($uid = $facebook->require_login())
{
	$first_name = $facebook->api_client->users_getInfo($uid, 'first_name');

	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'update')
	{
		$keyword = ($_REQUEST['title'] == 1) ? 'title:'.$_REQUEST['keyword'] : $_REQUEST['keyword'];
		$query['keyword'] = $_REQUEST['keyword'];
		$query['engine'] = $_REQUEST['engine'];
		$query['title'] = $_REQUEST['title'];
		$query['type'] = 1;

		update_profile($uid, $query);

		//include_once('update.php');
	}
	else
	{
		include_once('nav.php');
		include_once('search_box.php');
		include_once('main.php');
	}
}

?>

