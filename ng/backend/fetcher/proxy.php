<?php
error_reporting(E_ALL | E_STRICT);
require_once 'Zend/Http/Client.php';


$client = new Zend_Http_Client($_GET['dest']);
foreach($_POST as $k => $v) {
    $client->setParameterPost($k, $v);
}

$response = $client->request('POST');
echo $response->asString();

?>
