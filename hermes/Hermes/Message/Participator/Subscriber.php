<?php

require_once 'Hermes/Message/Participator/Abstract.php';

/**
 *
 * @package Hermes_Message
 * @subpackage Participator
 */
class Hermes_Message_Participator_Subscriber extends Hermes_Message_Participator_Abstract
{
    public function subscribe(Hermes_Message_Data_Selector_Interface $messageSelector)
    {
        $msg = self::getAdapter()->receive();
        
        if (!is_null($msg)) {
            // Dealing with message metadata (format, version, time etc.)
            if ($messageSelector->matchMetadata($msg)) {
                // Dealing with particular message body
                if ($messageSelector->matchBody($msg)) {
                    $this->getMessageListener()->onMessage($msg);
                }
            }
        }
    }
    
    public function setMessageListener(Hermes_Message_Listener_Interface $messageListener)
    {
        $this->_listener = $messageListener;
    }
    
    public function getMessageListener()
    {
        return $this->_listener;
    }
}
?>