<?php
include_once('conn.php');

$sql = 'SELECT facebook_uid, facebook_session '
.'FROM users; ';
$result = mysql_query($sql);

$enf = new NewsFeed($rss_op, $facebook_op);

while ($row = mysql_fetch_assoc($result))
{
	$content = $enf->get_profile($row['facebook_uid']) . date('Y:m:d H:i:s', time());
	$enf->set_profile($row['facebook_uid'], $row['facebook_session'], $content);
/*	$facebook->api_client->session_key = $row['facebook_session'];
	$content = get_profile($row['facebook_uid']) . date('Y:m:d H:i:s', time());
	$facebook->api_client->profile_setFBML( $content, $row['facebook_uid'] );*/
}
echo time();




/*$content = $row[0] . '<br />' . date('Y:m:d H:i:s', time());
$content = addslashes($content);
mysql_query("UPDATE profile_box SET fbml = '$content' WHERE id =1 LIMIT 1 ;");
$content = get_profile($_REQUEST['uid']) . date('Y:m:d H:i:s', time());
$facebook->api_client->profile_setFBML( $content, $uid );
$facebook->api_client->session_key = 'f9a156b515d1767e56ac66d3-524704611';
//$facebook->api_client->auth_getSession('386f68de69f4bdec0c1962fe796642bd');
//$facebook->api_client->feed_publishStoryToUser('posted from "infinite session" to news feed', $content);
//$facebook->api_client->feed_publishActionOfUser('posted from "infinite session" to mini-feed', $content);
$facebook->api_client->profile_setFBML( $content, '527144602' );
$facebook->api_client->profile_setFBML( $content, '524704611' );
$facebook->api_client->profile_setFBML( $content, '508269226' );*/

?>