<?php

/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Listener
 */
interface Hermes_Message_Listener_Interface{
    
    /**
     * Action need to take, when some message received
     *
     * @param mixed $message
     */
    public function onMessage($message);
}
?>