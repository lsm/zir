<?php
require_once 'Hermes/Message/Participator/Abstract.php';

/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Participator
 */
class Hermes_Message_Participator_Publisher extends Hermes_Message_Participator_Abstract
{
    public function publish(Hermes_Message_Data $message) 
    {
        self::getAdapter()->send($message);
    }
}
?>