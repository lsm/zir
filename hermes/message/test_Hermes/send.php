<?php
set_time_limit(0);

require 'Hermes/Message.php';
require 'Hermes/Message/Adapter/Spread.php';
require 'Hermes/Message/Participator/Publisher.php';
require 'Hermes/Message/Data.php';
require 'Hermes/Message/Data/Body.php';

$spread = Hermes_Message::factory('Spread', array('host'=>'localhost', 'port'=>'4803', 'user'=>'Ses'));
$pub = $spread->createParticipator('Publisher', array('1', '2', '3', '4', '5'));

$data = new Hermes_Message_Data();
$body = new Hermes_Message_Data_Body();

$y = 1000000;
while($y > 0) {
    $content = '';
    $x = 2;
    while($x > 0) {
        $content .= md5(rand(1, 10000));
        $x--;
    }
    
    $body->setContent($content);
    $data->setBody($body);
    $pub->publish($data);
    usleep(1000);    
    $y--;
    
    if ($y % 1000 == 0) {
        $spread->reconnect();
        $pub = $spread->createParticipator('Publisher', array('1', '2', '3', '4', '5'));
        echo "Reconnected! \n";
    }
}




?>
