<?php
require_once('rss.php');
require_once 'facebook.php';

class NewsFeed
{
	private $_fb;
	private $_rss;

	public $callbackurl;

	public  function __construct($rss_op, $facebook_op) {
		$this->_set_rss($rss_op);
		$this->_set_facebook($facebook_op);
	}

	private function _set_rss($options){
		$this->_rss = new lastRSS();
		$this->_rss->CDATA = $options['CDATA'];
		$this->_rss->cache_dir = $options['cache_dir'];
		$this->_rss->cache_time = $options['cache_time'];
		$this->_rss->cp = $options['code_page'];
	}

	private function _set_facebook($options){
		$this->_fb = new Facebook($options['api_key'], $options['secret']);
		$this->callbackurl = $options['callbackurl'];
	}

	public function get_rss($keyword, $engine='', $num=''){
		switch ($engine){
			case 'baidu':
				$url = 'http://news.baidu.com/ns?tn=newsrss&sr=0&cl=2&ct=0&rn=5&word='
					. urlencode(iconv('utf-8', 'gb2312', $keyword));
				break;
			case 'yahoo':
				$url = 'http://news.search.yahoo.com/news/rss?ei=UTF-8&fl=0&x=wrt&n=5&p='
					. $keyword;
				break;
			default:
				$url = 'http://news.search.yahoo.com/news/rss?ei=UTF-8&fl=0&x=wrt&n=5&p='
					. $keyword;
				break;
		}
		
		if (is_numeric($num) && $num != 5) 
			$url = str_replace('n=5&', "n=$num&", $url);

		$result = $this->_rss->Get($url);

		if (isset($result['items']) && count($result['items']) > 0){
			return $result;
		}
		else{
			echo "<p>Error: It's not possible to get $url.</p>";
			return false;
		}
	}

	public function get_news($result, $options){
		if (count($result['items']) > 0){
			$content = '';
			$content .= '<div class="news">';
			$content .= '<div class="keyword">';
			$content .= 'Your keyword: '.$options['keyword'];
			$content .= '</div>';

			if (isset($options['add_profile']) && $options['add_profile'] == 1){
				$q = '';
				foreach ($options as $k => $v)
				$q .= '&'.$k.'='.$v;

				$content .= '<div class="add">';
				$content .= '<a href="?action=update'.$q.' " title="Add your search results to your profile page.">Add to my Profile</a>';
				$content .= '</div>';
			}
			$content .= '<div class="clear"></div>';
			$content .= '<div class="newsList">';

			for ($i=0; $i < count($result['items']); $i++){
				$content .= '<ul style="list-style-type:none;padding:2px;margin:5px;">'."\n";
				$title = $result['items'][$i]['title'];
				$link = $result['items'][$i]['link'];
				$des = strip_tags(html_entity_decode($result['items'][$i]['description']), '<p>,<a>,<br>');
				$des_id = 'des_' . $options['engine'] . '_' . mt_rand(1000000, 9000000) . '_' . $i;
				$content .= '
				<div style="float:left;margin:0px;padding:0px;">
					<fb:share-button class="meta">
						<meta name="title" content=" '.str_replace('"', '”',strip_tags($title)).' " />
						<meta name="description" content=" '.str_replace('"', '”',strip_tags($des)).' " />
						<link rel="target_url" href="'.$link.'"/>
					</fb:share-button>
				</div>
				<li style="font-size:12px;margin-left:10px;"><a href="#" clicktoshow="' . $des_id .'" value="show">'.$title.'</a>
				</li>
				'."\n";
				$content .= '<li id="' . $des_id .'" style="padding-left:15px;display:none;">'
				.'<a href="#" clicktohide="' . $des_id .'" value="hide">hide</a><br />'
				.$des.'<a href="'.$link.'" target="_blank">  read more</a></li>'."\n";
				$content .= '</ul>';
			}

			$content .= '<div class="poweredBy">Powered by '.strtoupper($options['engine']).'! News</div>';
			$content .= '</div></div>';
			return $content;
		}
		else{
			echo 'No results.';
			return false;
		}
	}

	public function show_news($keyword, $options){
		$options['keyword'] = ($options['title'] == 1) ? 'title:'.$keyword : $keyword;
		echo $this->get_news($this->get_rss($options['keyword'], $options['engine'], $options['num']), $options);
	}

	/**
	* Show registration form.
	* @param string script name to receive post data
	* @param array facabook user info.
	* @param string call back url.
	*/
	function show_reg_form($action = '', $user_info = '', $callback_url = '' ){?>
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
	function show_session_form($api_key, $expires){?>
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


	/**
	* Fetch keywords from db, and create profile page.
	* @param string user's facebook uid.
	* @return string html
	*/
	public function get_profile($uid) {
		$sql = 'SELECT user_id, engine, keyword, num '
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
			$rss = $this->get_rss($row->keyword, $row->engine, $row->num);
			$static_profile .= $this->get_news($rss, $query);
		}
		return $static_profile;
	}


	/**
	* Update profile page.
	* @param string user's facebook uid.
	* @param string a 'infinite' session key.
	* @param string query options
	* @todo if <fb:ref url=""/> setted, refresh only. 
	* @return boolean TRUE if success.
	*/
	function set_profile($uid, $session_key, $options = '') {
		$this->_fb->api_client->session_key = $session_key;

		if (is_array($options)){//Will set current page to your profile page.
			$url = $this->callbackurl . 'profile.php?keyword='.$options['keyword'].'&title='.$options['title'].'&engine='.$options['engine'];
			$this->_fb->api_client->profile_setFBML( '<fb:ref url="'.$url.'"/>', $uid );
			$this->_fb->api_client->fbml_refreshRefUrl($url);
			echo '<p>Hi, '.$uid.'! Your profile updated. <a href="http://www.facebook.com/profile.php">My profile.</a> '.date('Y:m:d H:i:s', time()).'</p>';
			return true;
		}
		elseif (is_string($options)){//Set profile directly from a fbml string.
			$this->_fb->api_client->profile_setFBML( $options, $uid );
			echo '<p>Hi, '.$uid.'! Your profile updated. <a href="http://www.facebook.com/profile.php">My profile.</a> '.date('Y:m:d H:i:s', time()).'</p>';
			return true;
		}
		else {//Use your keywords in db to update your profile.
			$url = $this->callbackurl . 'profile.php?uid='.$uid;
			$this->_fb->api_client->profile_setFBML( '<fb:ref url="'.$url.'"/>', $uid );
			$this->_fb->api_client->fbml_refreshRefUrl($url);
			echo '<p>Hi, '.$uid.'! Your profile updated. <a href="http://www.facebook.com/profile.php">My profile.</a> '.date('Y:m:d H:i:s', time()).'</p>';
			return true;
		}
		return false;
	}


}

















?>