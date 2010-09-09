<?php
include_once('config.php');
$conn = mysql_connect($hostname,$db_user, $db_pass);
mysql_query("SET NAMES utf8");
mysql_select_db($db_name, $conn);
?>
