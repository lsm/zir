<?php
error_reporting(E_ALL | E_STRICT);
require_once('spreadutils.php');

$id = spread_connect('4803');

if ($id != null) {
    $msg = new Message();
    
    $msg->set_header('test1', 'test2');
    $msg->set_header('test2', 'test3');
    $msg->set_content('this is a test php message');
    
    $json = json_encode($msg);
    spread_join($id, 'test2');
    
    for ($i = 0; $i < 1; $i++) {
        spread_multicast($id, array('test'), $json);
    }
    spread_disconnect($id);
}
else {
    echo "<p>Failed to connect</p>";
}
?>
