<?php
include_once('class.inputfilter.php');
/**
 * Utility function to return a value from a named array or a specified default
 * @param array A named array
 * @param string The key to search for
 * @param mixed The default value to give if no key found
 * @param int An options mask: _MOS_NOTRIM prevents trim, _MOS_ALLOWHTML allows safe html, _MOS_ALLOWRAW allows raw input
 */
define( "_MOS_NOTRIM", 0x0001 );
define( "_MOS_ALLOWHTML", 0x0002 );
define( "_MOS_ALLOWRAW", 0x0004 );
function mosGetParam( &$arr, $name, $def=null, $mask=0 ) {
	static $noHtmlFilter 	= null;
	static $safeHtmlFilter 	= null;

	$return = null;
	if (isset( $arr[$name] )) {
		$return = $arr[$name];

		if (is_string( $return )) {
			// trim data
			if (!($mask&_MOS_NOTRIM)) {
				$return = trim( $return );
			}

			if ($mask&_MOS_ALLOWRAW) {
				// do nothing
			} else if ($mask&_MOS_ALLOWHTML) {
				// do nothing - compatibility mode
			} else {
				// send to inputfilter
				if (is_null( $noHtmlFilter )) {
					$noHtmlFilter = new InputFilter( /* $tags, $attr, $tag_method, $attr_method, $xss_auto */ );
				}
				$return = $noHtmlFilter->process( $return );

				if (empty($return) && is_numeric($def)) {
					// if value is defined and default value is numeric set variable type to integer
					$return = intval($return);
				}
			}

			// account for magic quotes setting
			if (!get_magic_quotes_gpc()) {
				$return = addslashes( $return );
			}
		}

		return $return;
	} else {
		return $def;
	}
}

function dd($var, $msg = 'Debug:')
{
	echo '<pre>' . $msg . '<br />';
	var_dump($var);
	echo '</pre>';
}

function action($action)
{
	include_once($action.'.php');
}

function getRSS($query, $queryURL)
{
	include_once('rss.php');
	$rss = new lastRSS();
	$rss->cache_dir = 'cache/';
	$rss->cache_time = 0;
	$rss->cp = 'UTF-8';
	$rss->date_format = 'l';

	$rssurl=$queryURL . $query;
	if ($result = $rss->Get($rssurl)) return $result;
	
/*	if ($rs)
	{
		for ($i=0; $i < count($rs['items']); $i++)
		{
			// strip google parameters
			$end = strpos($rs['items'][$i]['title'], '-');
			$pos1 = strpos($rs['items'][$i]['link'], 'url=') + 4;
			$pos2 = strlen($rs['items'][$i]['link']);

			$url = substr($rs['items'][$i]['link'], $pos1, $pos2);
			$pos3 = strpos($url, 'cid=') - 5;
			$url = substr($url, 0, $pos3);

			$title = html_entity_decode(substr($rs['items'][$i]['title'], 0, $end));
			$title = iconv_substr($title, 0, 16);
			echo '<li><a href="' . $url . '" >' . $title . '...</a></li>' . "\n";
			
		}
	}
	else
	{
		echo "Error: It's not possible to get $rssurl...";
	}*/
}

function myheader($url, $time = 0)
{
	echo "<meta http-equiv=\"refresh\" content=\"$time;url=$url\">";
}



/**
 * Provides a secure hash based on a seed
 * @param string Seed string
 * @return string
 */
function mosHash( $seed ) {
	return md5( $GLOBALS['mosConfig_secret'] . md5( $seed ) );
}

function josSpoofCheck( $header=NULL, $alt=NULL ) {
	$validate 	= mosGetParam( $_POST, josSpoofValue($alt), 0 );

	// probably a spoofing attack
	if (!$validate) {
		header( 'HTTP/1.0 403 Forbidden' );
		mosErrorAlert( _NOT_AUTH );
		return;
	}

	// First, make sure the form was posted from a browser.
	// For basic web-forms, we don't care about anything
	// other than requests from a browser:
	if (!isset( $_SERVER['HTTP_USER_AGENT'] )) {
		header( 'HTTP/1.0 403 Forbidden' );
		mosErrorAlert( _NOT_AUTH );
		return;
	}

	// Make sure the form was indeed POST'ed:
	//  (requires your html form to use: action="post")
	if (!$_SERVER['REQUEST_METHOD'] == 'POST' ) {
		header( 'HTTP/1.0 403 Forbidden' );
		mosErrorAlert( _NOT_AUTH );
		return;
	}

	if ($header) {
		// Attempt to defend against header injections:
		$badStrings = array(
		'Content-Type:',
		'MIME-Version:',
		'Content-Transfer-Encoding:',
		'bcc:',
		'cc:'
		);

		// Loop through each POST'ed value and test if it contains
		// one of the $badStrings:
		_josSpoofCheck( $_POST, $badStrings );
	}
}

function _josSpoofCheck( $array, $badStrings ) {
	// Loop through each $array value and test if it contains
	// one of the $badStrings
	foreach( $array as $v ) {
		if (is_array( $v )) {
			_josSpoofCheck( $v, $badStrings );
		} else {
			foreach ( $badStrings as $v2 ) {
				if ( stripos( $v, $v2 ) !== false ) {
					header( 'HTTP/1.0 403 Forbidden' );
					mosErrorAlert( _NOT_AUTH );
					exit(); // mosErrorAlert dies anyway, double check just to make sure
				}
			}
		}
	}
}

/**
 * Method to determine a hash for anti-spoofing variable names
 *
 * @return	string	Hashed var name
 * @static
 */
function josSpoofValue($alt=NULL) {
	global $mainframe;

	if ($alt) {
		if ( $alt == 1 ) {
			$random		= date( 'Ymd' );
		} else {
			$random		= $alt . date( 'Ymd' );
		}
	} else {
		$random		= date( 'dmY' );
	}
	// the prefix ensures that the hash is non-numeric
	// otherwise it will be intercepted by globals.php
	$validate 	= 'j' . mosHash( $mainframe->getCfg( 'db' ) . $random );

	return $validate;
}
?>