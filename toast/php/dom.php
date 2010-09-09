<?php

// Parsing Yahoo! REST Web Service results using
// DOM extension. PHP5 only.
// Traversal code by Rasmus Lerdorf
// February 1, 2006

error_reporting(E_ALL);

// The web services request
//$request =  'http://api.search.yahoo.com/ImageSearchService/V1/imageSearch?appid=YahooDemo&query=Madonna&results=4';
$request = 'news-atom.xhtml';
// Fetch it
$response = file_get_contents($request);

if ($response === false) {
	die('The request failed');
}

// Create a new DOM object
$dom = new DOMDocument('1.0', 'UTF-8');

// Load the XML into the DOM
if ($dom->loadHTML($response) === false) {
    die('Parsing failed');
}

var_dump($response);

// Now traverse the DOM with this function.
// This traversal function is suitable for the Yahoo! Maps and
// Search web services, and any other Yahoo! Web Services that
// return the number of results as attributes in the root tag and
// have no other attributes in the results. For other Yahoo! Web 
// Services, such as Flickr web services, this routine will
// have to be altered.
function xml_to_result($dom) {
	$root = $dom->firstChild;
	foreach($root->attributes as $attr) $res[$attr->name] = $attr->value;
	$node = $root->firstChild;
	$i = 0;
	while($node) {
		switch($node->nodeName) {
			case 'Result':
				$subnode = $node->firstChild;
				while($subnode) {
					$subnodes = $subnode->childNodes;
					foreach($subnodes as $n) {
						if($n->hasChildNodes()) {
							foreach($n->childNodes as $cn) $res[$i][$subnode->nodeName][$n->nodeName]=trim($cn->nodeValue);
						} else $res[$i][$subnode->nodeName]=trim($n->nodeValue);
					}
					$subnode = $subnode->nextSibling;
				}
				break;
			default:
				$res[$node->nodeName] = trim($node->nodeValue);
				$i--;
				break;
		}
		$i++;
		$node = $node->nextSibling;
	}
	return $res;
}

$res = xml_to_result($dom);

echo '<pre>';
print_r($res);
echo '</pre>';


?>