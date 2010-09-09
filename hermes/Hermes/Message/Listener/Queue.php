<?php
require_once 'Hermes/Message/Listener/Interface.php';

/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Listener
 */
class Hermes_Message_Listener_Queue implements Hermes_Message_Listener_Interface
{
    
    public function __construct($res)
    {
        $this->_queueId = $res;
    }
    
    public function onMessage($msg)
    {
        if ($msg instanceof Hermes_Message_Data) {
            if (msg_send($this->_queueId, 1, $msg->getBody()->getContent(), true, true, $err) === true) {
                echo "Queued message: " . $msg->getBody()->getContent();
            }
            var_dump(msg_stat_queue($this->_queueId));
        }
        
    }
}
?>