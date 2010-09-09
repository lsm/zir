<?php
$cmd = 'ls -1 /home/www/myngle/static/upload/slides/ea1858719f030814292288122c5140f21190694286*.jpg | /usr/bin/wc -l';
$r = exec($cmd);
var_dump($r);
$r = `$cmd`;
var_dump($r);
$r = shell_exec($cmd);
var_dump($r);
$r = system($cmd);
var_dump($r);
?>