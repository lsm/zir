<?php
error_reporting(E_ALL|E_STRICT);
require '../Message.php';
$m = Hermes_Message_Channel_Spread::getInstance('4803');
$m->subscribe('test');
while (true) {
    $msg = $m->listen();
    // A message filter/selector
    if ($msg) {
        var_dump($msg);
    }
    sleep(1);
}
