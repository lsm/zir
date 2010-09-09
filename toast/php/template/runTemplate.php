<?php
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Chongqing');

$start = utime();

require 'Template.php';

$t = new Myngle_Template();
$t->setSuffix('html');

$t->assign('today', date('m/d/Y '));
$t->assign('tableHeader', 'Here is test table header');
$t->assign('pageTitle', 'MyTemplate test page');
$t->assign('phpself', $_SERVER['PHP_SELF']);
//
//$grandChildBlock = array(
//		array('k'=>'grandChild1'),
//		array('k'=>'grandChild2'),
//		array('k'=>'grandChild3'),
//		array('k'=>'grandChild4')
//		);
//

//$blockTr = array(
//		array('id'=>'home', 'point'=>'Home page'),
//		array('id'=>'about', 'point'=>'About us'),
//		array('id'=>'download', 'point'=>'Download page'),
//		array('id'=>'contacts', 'point'=>'Contacts'),
//		'child' => array('childBlock' => $childBlock)
//		);

$menu = array(
		array('id'=>'home', 'point'=>'Home page'),
		array('id'=>'about', 'point'=>'About us'),
		array('id'=>'download', 'point'=>'Download page'),
		array('id'=>'contacts', 'point'=>'Contacts'),
		);
//$childBlock = array(
//		array('cell'=>'child1','childsd'=> $menu),
//		array('cell'=>'child2'),
//		array('cell'=>'child3'),
//		array('cell'=>'child4'),
//		);
//		var_dump($childBlock);

for ($i = 0; $i < 20; $i++) {
        $blockTr[$i][] = $i;
        for ($j = 1; $j < 6; $j++) {
            $v = $i*5 + $j;
            $blockTr[$i]['childBlock'][$j]['cell'] = 'childBlock' . $v; 
        }
}

//$blockTr['child']['childBlock'] = $childBlock;
//$blockTr['child']['childBlock'] = $menu;

//var_dump($blockTr);
////$t->tplBasePath = '';
//
////$t->tplScriptPath = 'template/';
$t->assign('blockTr', $blockTr);
//$t->assign('blockTr.childBlock', $childBlock);
$t->assign('menu', $menu);


//echo $t->parse('testT.html');
echo $t->parse('testTemplate.php');

//var_dump($t->templateTags);
//var_dump($t->templateTags);
//var_dump($t->segments);
//var_dump($t->mainContent);

echo "<br>Page create in: ".(utime()-$start)." sec.<br>";

function utime ()
{
    $time = explode( " ", microtime());
    $usec = (double)$time[0];
    $sec = (double)$time[1];
    return $sec + $usec;
}

?>