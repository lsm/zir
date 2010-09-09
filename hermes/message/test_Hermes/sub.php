<?php
$path = get_include_path();
set_include_path('/home/workspace/webroot/hermes:' . $path);

require '../../Hermes/Message.php';
require 'Hermes/Message/Adapter/Spread.php';
require 'Hermes/Message/Participator/Subscriber.php';
require 'Hermes/Message/Listener/Debug.php';
require 'Hermes/Message/Listener/Queue.php';
require 'Hermes/Message/Data/Selector/Mock.php';
require 'Hermes/Message/Data/Formatter/Spread.php';

$spread = Hermes_Message::factory('Spread', array('host'=>'localhost', 'port'=>'4803', 'user'=>'Tsb'));
$sub = $spread->createParticipator('Subscriber', array('1', '2', '3'));
$spread->setFormatter(Hermes_Message_Data_Formatter_Spread);

//$id = ftok(__FILE__, 'a');
//$res = msg_get_queue($id);

$sub->setMessageListener(new Hermes_Message_Listener_Debug());
//$sub->setMessageListener(new Hermes_Message_Listener_Queue($res));
$sl = new Hermes_Message_Data_Selector_Mock();

while(true) {
    $sub->subscribe($sl);
    //usleep(20000);
}
?>