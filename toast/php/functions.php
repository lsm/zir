<?php

/**
   * Create news list.
   * @param string keyword 
   * @param string query url (search engine)
   * @return array news list ( title, description......see rss.php for more info.)
   */
function get_rss($query, $queryURL)
{
	include_once('rss.php');
	$rss = new lastRSS();
	$rss->CDATA = 'strip';
	$rss->cache_dir = 'cache/';
	$rss->cache_time = 3600;
	$rss->cp = 'UTF-8';

	$rssurl=$queryURL . $query;
	try {
		$result = $rss->Get($rssurl);
		return $result;
	}
	catch (Exception $ex)
	{
		echo "Error: It's not possible to get $rssurl...";
		$ex->getMessage();
	}
	return false;
}

/**
   * Create news list.
   * @param array search result return from get_rss() 
   * @param array query options.
   * @return string html
   */
function show_news($result, $query='')
{
	$content = '';
	$content .= '<div class="news">';
	if (!empty($query['keyword']))
	{
		$keyword = $query['keyword'];
		$content .= '<div class="keyword">';
		$content .= 'Your keyword: '.$keyword;
		$content .= '</div>';

		if ($query['add_profile'] == 1 && count($result['items']) > 0)
		{
			$q = '';
			foreach ($query as $k => $v)
			$q .= '&'.$k.'='.$v;

			$content .= '<div class="add">';
			$content .= '<a href="?action=update'.$q.' " title="Add your search results to your profile page.">Add to my Profile</a>';
			$content .= '</div>';
		}
	}
	$content .= '<div class="clear"></div>';
	$content .= '<div class="newsList">';

	for ($i=0; $i < count($result['items']) && $i < 5 ; $i++)
	{//TODO: iconv strtr only for baidu.
		$content .= '<ul style="list-style-type:none;padding:2px;margin:5px;">'."\n";
		$title = iconv('gb2312', 'utf-8', strtr($result['items'][$i]['title'], array('<![CDATA['=>'', ']]>'=>'')));
		$link = iconv('gb2312', 'utf-8', strtr($result['items'][$i]['link'], array('<![CDATA['=>'', ']]>'=>'')));
		$des = strip_tags(html_entity_decode(iconv('gb2312', 'utf-8', strtr($result['items'][$i]['description'], array('<![CDATA['=>'', ']]>'=>'')))), '<p>,<a>,<br>');
		$content .= '
		<div style="float:left;margin:0px;padding:0px;">
			<fb:share-button class="meta">
				<meta name="title" content=" '.str_replace('"', '”',strip_tags($title)).' " />
				<meta name="description" content=" '.str_replace('"', '”',strip_tags($des)).' " />
				<link rel="target_url" href="'.$link.'"/>
			</fb:share-button>
		</div>
		<li style="font-size:12px;margin-left:10px;"><a href="#" clicktoshow="des_'.$keyword . '_' . $i.'" value="show">'.$title.'</a>
		</li>
		'."\n";
		$content .= '<li id="des_'.$keyword . '_' . $i.'" style="padding-left:15px;display:none;"><a href="#" clicktohide="des_'.$keyword . '_' . $i.'" value="hide">hide</a><br />'.$des.'<a href="'.$link.'" target="_blank">  read more</a></li>'."\n";
		$content .= '</ul>';
	}

	$content .= '<div class="poweredBy">Powered by '.strtoupper($query['engine']).'! News</div>';
	$content .= '</div></div>';
	return $content;
}

/**
   * Show registration form.
   * @param string script name to receive post data
   * @param array facabook user info.
   * @param string call back url.
   */

function show_reg_form($action = '', $user_info = '', $callback_url = '' )
{
	?>
	<div class="reg">
	<p>You have the Ethos News Feed installed in Facebook, but you don't have ENF account. Please signup first!</p>
	<div class="regForm">
	<form action="<?=$action?>" method="POST">
		<div class="label">First name :</div><input type="text" name="first_name" value="<?php if (!empty($user_info)) echo $user_info[0]['first_name'];?>" size="15" /> <br />
		<div class="label">Last name:</div><input type="text" name="last_name" value="<?php if (!empty($user_info)) echo $user_info[0]['last_name'];?>" size="15" /> <br />
		<div class="label">Email:</div><input type="text" name="email" value="" size="15" /> <br />
		<input type="hidden" name="verify" value="1" />
		<input type="hidden" name="type" value="reg" />
		<input type="hidden" name="callback_url" value="<?php if (!empty($callback_url)) echo $callback_url;?>" />
		<input type="submit" name="submit" value="Sign Up" />
	</form>
	</div>
	
	<div class="regIntro">
	<p>Why register?</p>
	<p>Save your keywords in you locker.</p>
	<p>Update your profile box with your keywords, so your news will be fresh!</p>
	</div>
	</div>
	<?php
}

/**
   * Show form to get infinite session key.
   * @param string facebook api key
   * @param string user's session expire time
   */
function show_session_form($api_key, $expires)
{
	?>
		<div class="main">
		<a href="http://www.facebook.com/code_gen.php?v=1.0&api_key=<?=$api_key?>" target="_blank">Generate a "one time" code</a>
			<div class="regForm">
		<form action="" method="POST">
		<div class="label">Your CODE:</div><input type="text" name="auth_token" value="" size="15" /> <br />
		<input type="hidden" name="verify" value="1" />
		<input type="hidden" name="type" value="session" />
		<input type="submit" name="submit" value="Generate" />
		</form>
		</div>
		
		<div class="regIntro">
			<p>You session will expire in <?=date('Y:m:d H:i:s', $expires)?></p>
			<p>Why you need a infinite session?</p>
			<p>1.Post to mini/news feed when your locker changed.</p>
			<p>2.Update your profile box when your locker changed.</p>
		</div>
		</div>
	<?php
}

function show_locker()
{

}

/**
   * Fetch keywords from db, and create profile page.
   * @param string user's facebook uid.
   * @return string html
   */

function get_profile($uid)
{
	global $engine_url;

	$sql = 'SELECT user_id, engine, keyword '
	.'From feeds '
	."WHERE user_id = '$uid'; ";

	$result = mysql_query($sql) or die(mysql_error());

	$static_profile = '';

	if (mysql_num_rows($result) < 1)
	{
		echo 'You don\'t have any items in your locker.';
		exit();
	}
	
	$static_profile = file_get_contents('css.php');

	while ($row = mysql_fetch_object($result))
	{//Get user's keywords from db, and generate content.
		$query['keyword'] = $row->keyword;
		$query['engine'] = $row->engine;
		$query['add_profile'] = 0;
		$rss = get_rss($row->keyword, $engine_url[$row->engine]);
		$static_profile .= show_news($rss, $query);
	}
	return $static_profile;
}


/**
   * Update profile page.
   * @param string user's facebook uid.
   * @param string query options
   */
function update_profile($uid, $query = '')
{
	global $appcallbackurl, $facebook;

	if ($query['type'] == 1)
	{//Will set current page to your profile page.
		$url = $appcallbackurl. 'profile.php?keyword='.$query['keyword'].'&title='.$query['title'].'&engine='.$query['engine'];
		$facebook->api_client->profile_setFBML( '<fb:ref url="'.$url.'"/>', $uid );
		$facebook->api_client->fbml_refreshRefUrl($url);
		echo '<p>Your profile updated. <a href="http://www.facebook.com/profile.php">My profile.</a> '.date('Y:m:d H:i:s', time()).'</p>';
	}
	elseif ($query['type'] == 2)
	{//Use your keywords in db to update your profile.
		$url = $appcallbackurl. 'profile.php?uid='.$uid;
		$facebook->api_client->profile_setFBML( '<fb:ref url="'.$url.'"/>', $uid );
		$facebook->api_client->fbml_refreshRefUrl($url);
		echo '<p>Your profile updated. <a href="http://www.facebook.com/profile.php">My profile.</a> '.date('Y:m:d H:i:s', time()).'</p>';
	}
	else
	{//Oops
		echo 'update profile error';
	}
}





?>