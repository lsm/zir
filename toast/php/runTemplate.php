<?php

require 'echoTemplate.php';

$t = new EchoTemplate();
$t->testVar = 98700007;
$t->headerVar = 'From header';
$t->navVar = 'From nav';

$t->x = array(
'0' => array(
'y' => 'I\'m Y1',
'z' => 'I\'m Z1'
	),
'1' => array(
'y' => 'I\'m Y2',
'z' => 'I\'m Z2'
	)
);

$t->users = array(
'0' => array(
'name' => 'I\'m name1',
'id' => 'I\'m id1'
	),
'1' => array(
'name' => 'I\'m name2',
'id' => 'I\'m id2'
	)
);

$t->as = array(
'0' => array(
'username' => 'I\'m username1',
'email' => 'I\'m email1',
'id' => 'I\'m id1'
	),
'0' => array(
'username' => 'I\'m username2',
'email' => 'I\'m email2',
'id' => 'I\'m id2'
	)
);
$t->tplBasePath = 'template/';
//$t->tplScriptPath = 'template/';
$t->languages = $t->as;
$t->parse('testTemplate.php');

echo $t->mainContent;
//var_dump($t->templateTags);
var_dump($t->templateTags);
var_dump($t->segments);
var_dump($t->mainContent);

?>