<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
</head>


<?php
error_reporting(E_ALL | E_STRICT);
require_once 'Zend/Http/Client.php';

$google_bot = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
$uas['safari'] = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_5; en-us) AppleWebKit/525.18 (KHTML, like Gecko) Version/3.1.2 Safari/525.20.1';
$uas['firefox'] = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.0.3) Gecko/2008092414 Firefox/3.0.3';

$ff_full = '
(Request-Line)	GET /news/wlyx/ HTTP/1.1
Host	games.sina.com.cn
User-Agent	Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; en-US; rv:1.9.0.3) Gecko/2008092414 Firefox/3.0.3
Accept	text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
Accept-Language	en-us,en;q=0.5
Accept-Encoding	gzip,deflate
Accept-Charset	ISO-8859-1,utf-8;q=0.7,*;q=0.7
Keep-Alive	300
Connection	keep-alive';

if (empty($_REQUEST['url'])) exit;

$url = $_REQUEST['url'];
$client = new Zend_Http_Client($url);
$ua = $uas['firefox'];
$client->setHeaders(array(
    'User-Agent' => $ua,
    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
    'Accept-Language' => 'en-us,en;q=0.5',
    'Accept-Encoding' => 'deflate',
    'Accept-Charset' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
    'Keep-Alive' => '300',
    'Connection' => 'keep-alive'));

//var_dump($client);
$response = $client->request('GET');
//var_dump($response);
$body = $response->getBody();
$body = iconv('gb2312', 'utf-8', $body);
$headers = $response->getHeaders();
if (isset($headers['Transfer-encoding']) && $headers['Transfer-encoding'] == 'chunked') {
    $body = $response->decodeChunkedBody($body);
}

if (isset($headers['Content-encoding'])) {
    switch(strtolower($headers['Content-encoding'])) {
        case 'gzip':
            $body = $response->decodeGzip($body);
            break;
        case 'deflate':
            $body = $response->decodeDeflate($body);
            break;
    }
}

$body = preg_replace('#<script\b([^>])+>(.|\n)*?</script>#', '', $body);
//$body = preg_match('#<body\b([^>])+>(.|\n)*?</body>#', $body);
$s = array('<body', '</body>');
$r = array('<div', '</div>');
$body = str_replace($s, $r, $body);
echo $body;
?>
