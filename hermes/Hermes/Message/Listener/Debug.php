<?php

require_once 'Hermes/Message/Listener/Interface.php';

/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Listener
 */
class Hermes_Message_Listener_Debug implements Hermes_Message_Listener_Interface
{
    public function onMessage($msg)
    {
        if ($msg instanceof Hermes_Message_Data) {
            var_dump($msg->getBody()->getContent());
            var_dump($msg->getMetadata());
        }
    }
}

?>