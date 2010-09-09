<?php
//TODO: remove

$GLOBALS['facebook_config']['debug'] = 0;
include_once('facebook_init.php');
include_once('conn.php');

$sql = 'SELECT users.facebook_uid, users.facebook_session, feeds.user_id, feeds.engine, feeds.keyword '
.'FROM users, feeds '
.'WHERE users.facebook_uid = feeds.user_id;';
$result = mysql_query($sql);

$uid = array();

while ($row = mysql_fetch_assoc($result))
{//Write to the profile widget.
	$facebook->api_client->session_key = $row['facebook_session'];
	$content = get_profile($row['facebook_uid']) . date('Y:m:d H:i:s', time());
	$facebook->api_client->profile_setFBML( $content, $row['facebook_uid'] );

	if ( !in_array( $row['facebook_uid'],	$uid))
	{//One time per user.
		$uid[] = $row['facebook_uid'];
		$facebook->api_client->feed_publishStoryToUser('posted a story from ENF(cron)', date('Y:m:d H:i:s', time()));
		$facebook->api_client->feed_publishActionOfUser('posted a action from ENF(cron)', date('Y:m:d H:i:s', time()));
	}
}







/*
$GLOBALS['facebook_config']['debug'] = 0;
include_once('facebook_init.php');
include_once('conn.php');

$result = mysql_query('SELECT fbml FROM profile_box WHERE id=1;');
$row = mysql_fetch_row($result);
$content = $row[0] . '<br />' . date('Y:m:d H:i:s', time());
$content = addslashes($content);
mysql_query("UPDATE profile_box SET fbml = '$content' WHERE id =1 LIMIT 1 ;");
$facebook->api_client->session_key = 'f9a156b515d1767e56ac66d3-524704611';
//$facebook->api_client->auth_getSession('386f68de69f4bdec0c1962fe796642bd');
//$facebook->api_client->feed_publishStoryToUser('posted from "infinite session" to news feed', $content);
//$facebook->api_client->feed_publishActionOfUser('posted from "infinite session" to mini-feed', $content);
$facebook->api_client->profile_setFBML( $content, '527144602' );
$facebook->api_client->profile_setFBML( $content, '524704611' );
$facebook->api_client->profile_setFBML( $content, '508269226' );*/
?>