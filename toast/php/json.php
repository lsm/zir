<?php

// Parsing Yahoo! REST Web Service results using
// either PHP-JSON, a PHP extension available
// here: http://www.aurore.net/projects/php-json
// or using the JSON-PHP parser written in PHP, available
// here: http://pear.php.net/pepr/pepr-proposal-show.php?id=198
// Both methods are illustrated below
// Author: Jason Levitt
// February 1, 2006

error_reporting(E_ALL);

$request =  'http://api.search.yahoo.com/ImageSearchService/V1/imageSearch?appid=YahooDemo&query=Madonna&results=4&output=json';

$response = file_get_contents($request);

if ($response === false) {
	die('Request failed');
}

// Parsing using the PHP extension, PHP-JSON

$phpobj1 = json_decode($response);

echo '<pre>';
print_r($phpobj1);
echo '</pre>';

// Parsing using the PEAR library, JSON-PHP

require_once('JSON.php');
$json = new Services_JSON();

$phpobj2 = $json->decode($response);

echo '<pre>';
print_r($phpobj2);
echo '</pre>';

?>