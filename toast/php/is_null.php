<?php
echo '<p>is_null()</p>';
echo '\'\'';
var_dump(is_null(''));
echo '0';
var_dump(is_null(0));
echo 'null';
var_dump(is_null(null));
echo 'false';
var_dump(is_null(false));
echo 'true';
var_dump(is_null(true));

echo '<p>empty()</p>';
echo '\'\'';
$t = '';
var_dump(empty($t));
echo '0';
$t = 0;
var_dump(empty($t));
echo 'null';
$t = null;
var_dump(empty($t));
echo 'false';
$t = false;
var_dump(empty($t));
echo 'true';
$t = true;
var_dump(empty($t));

?>