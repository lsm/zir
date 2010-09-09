<?php

include 'DemoClass.php';

$d = new DemoClass();
$d->publicFunction();
$fp = fopen('demo_class.serialized', 'w+');
fwrite($fp, serialize($d));
fclose($fp);
