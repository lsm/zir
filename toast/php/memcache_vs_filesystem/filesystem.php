<?php
require 'include.php';

// prepare directory
$hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
$dirs = array();
$dir = '/path/to';
foreach($hex as $v) {
    foreach($hex as $v2) {
        if (!is_dir($dir . "/$v/$v2")) {
            mkdir($dir . "/$v/$v2");
        }
    }
}

timer();
// save in filesystem
foreach ($keys as $v) {
    file_put_contents(getPath($dir, $v), $content);
}
echo 'Save  ' . $cycle . ' files in filesystem spent: ' . timer() . "\n";

timer();
// get from system
foreach ($keys as $v) {
    file_get_contents(getPath($dir, $v));
}
echo 'Get  ' . $cycle . ' files from filesystem spent: ' . timer() . "\n";














function getPath($path, $key)
{
    return $path . '/' . substr($key, 0, 1) . substr($key, 1, 2);
}


