<?php
include_once('facebook_init.php');

if (!$_POST['commit'])
{
	$friends = $facebook->api_client->friends_get();
	$i=0;
?>
<div style="border:1px dotted silver;margin:20px;padding:2px;">
Invite your friend:
<form action="" method="POST">
<?php
while ($friends[$i]) {
	$user_info = $facebook->api_client->users_getInfo($friends[$i], 'first_name, has_added_app');
	if (!$user_info[0]['has_added_app']){
?>
<select name="invite">
	<option value="<?=$friends[$i]?>"><?=$user_info[0]['first_name']?></option>
</select>	
<?php
	}
	$i++;
}
?>
<input type="submit" value="Invite" name="commit" class="fb_button" />
</form>
</div>
<?php
}
else {
	$content = 'Your friend invite you to add <a href="http://apps.facebook.com/enewsfeed/">Ethos News Feed</a>';
	$facebook->api_client->notifications_sendRequest($_POST['invite'], 'event', $content,'http://profile.ak.facebook.com/profile5/989/67/n524704611_9884.jpg', 1);
	echo 'You have invited '.$_POST['invite'].'!';
}


?>