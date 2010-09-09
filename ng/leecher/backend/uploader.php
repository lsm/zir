<?php
//@todo add tried times checking

error_reporting(E_ALL | E_STRICT);
mysql_connect('127.0.0.1', 'root', '123qwe');
mysql_select_db('ng');

$rules = array(
'sina-games-0' => 7,
'sina-games-1' => 7,
'17173-news-0' => 10,
'17173-news-1' => 9,
'17173-news-2' => 8,
);

$query = "SELECT * FROM items WHERE type = 'got' LIMIT 5;";
$res = mysql_query($query);

while ($obj = mysql_fetch_object($res)) {

		$content = '';
		$title = $obj->title;

			$opts['digest'] = rand(0, 3);
			$opts['imagetolocal'] = 1;
      $opts['selectimage'] = 1;
      $opts['autofpage'] = 1;
  		$opts['cid'] = $rules[$obj->rule_name . '-' . $obj->channel];
			$opts['mid'] = 1;

	if ($obj->parent == 0) {
			echo "start uploading $obj->title <br />";
			if (post($title, $obj->content, $opts)) {
         update($title);
			}
	} else {
		$query = "SELECT * FROM items WHERE parent = '$obj->parent'";
		$r = mysql_query($query);
		$query = "SELECT * FROM items WHERE parent = '$obj->parent' AND type ='got'";
		$rn = mysql_query($query);

		if (mysql_num_rows($r) == mysql_num_rows($rn)) {
				$tmp = array();
				while ($o = mysql_fetch_object($rn)) {
					if (preg_match('#^.*,(\d+).shtml$#', $o->url, $matches)) {
							$tmp[$matches[1]] = $o->content;
						}
					}
					if (count($tmp) > 0) {
						ksort($tmp);
						foreach($tmp as $v) {
							$content .= $v;
						}
					}
		}

		if ($title && $content) {
			echo "start uploading $obj->title <br />";
			if (post($title, $content, $opts)) {
        update($title);
			}
		}
	}
    
}

function update($title) {
        $query = "UPDATE `items` SET `type` = 'sent', `created` = NOW( ) WHERE `items`.`title` = '$title';";
				mysql_query($query);
				echo "uploaded $title <br />";
}

function post($title, $content, $opts) {
	require_once 'Zend/Http/Client.php';
	$url = 'http://admintools.navgame.com/glee442460deef2b9c340f73/add.php';
	$client = new Zend_Http_Client($url);
	$client->setParameterPost('title', $title);
	$client->setParameterPost('content', $content);
	$client->setParameterPost($opts);		
	$response = $client->request('POST');
	return $response->getBody() == 'ok';
}

?>
