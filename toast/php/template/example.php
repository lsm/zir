<?php
error_reporting(0);
$start = utime();

include './MyTemplate.class.php';

$T = new MyTemplate();	

/* set template which contains nested template, which will be shown at the top and bottom of the page */
$T->setFile('example', 'templ.html');

$T->assignVars('page_title', 'MyTemplate test page');

$menu = array(
		array('id'=>'home', 'point'=>'Home page'),
		array('id'=>'about', 'point'=>'About us'),
		array('id'=>'download', 'point'=>'Download page'),
		array('id'=>'contacts', 'point'=>'Contacts')
		);

/* 
 * to process dinamic block we need only assign his variables. Not need to parse it
 */
for($i=0; $i< sizeof($menu); $i++) {
	$T->assignBlockVars('MENU.POINT', $menu[$i],1);
}

$T->assignVars('today', date('m/d/Y '));
$T->assignVars('today', date('l'), true);

$T->assignBlockVars('TABLE.THEAD', array('table_header'=>'Here is test table header'));

for ($i=1; $i<=100; $i++) {
	/*
	 * when we have to begin new row?
	 */
	$level = ($i==1 || $i%5 == 1) ? 1 : 2;
	$T->assignBlockVars('TABLE.ROW.CELL', array('cell_text'=>'Cell number '.$i), $level);
}

// that is all :)
$T->fprint('example');

echo "<br>Page create in: ".(utime()-$start)." sec.<br>";

function utime ()
{
    $time = explode( " ", microtime());
    $usec = (double)$time[0];
    $sec = (double)$time[1];
    return $sec + $usec;
}

?>
