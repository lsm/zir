<?php
//TODO: remove


$url = $appcallbackurl. 'profile.php?keyword='.$query['keyword'].'&title='.$query['title'].'&engine='.$query['engine'];
echo $url;
$facebook->api_client->profile_setFBML( '<fb:ref url="'.$url.'"/>', $uid );
$facebook->api_client->fbml_refreshRefUrl($url);
echo '<p>Your profile updated. <a href="http://www.facebook.com/profile.php">My profile.</a></p>';

?>