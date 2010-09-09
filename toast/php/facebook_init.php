<?php
require_once 'facebook.php';
require_once 'config.php';

$appapikey = '2812abca767244fe28cd9d46cc1d0961';
$appsecret = '15bf54813ea6392a7ce97c8b0a968f6c';

$facebook = new Facebook($appapikey, $appsecret);
$appcallbackurl = 'http://www.oscan.org/gnews/';
$appurl = 'http://apps.facebook.com/enewsfeed/';



//dashboard
//$_GET['auth_token'] = 'LHWD82';
//$uid = $facebook->require_login();
//echo $facebook->api_client->server_addr;
//$x = $facebook->api_client->fql_query("SELECT first_name, last_name, has_added_app FROM user WHERE uid = '$uid' ;");

//catch the exception that gets thrown if the cookie has an invalid session_key in it
/*try {
	if (!$facebook->api_client->users_isAppAdded()) {
		$facebook->redirect($facebook->get_add_url());
	}
} catch (Exception $ex) {
	//this will clear cookies for your application and redirect them to a login prompt
	$facebook->set_user(null, null);
	$facebook->redirect($appcallbackurl);
}*/

?>