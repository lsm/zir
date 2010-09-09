<?php

/** Hermes_Message_Data_Formatter_Interface */
require_once 'Hermes/Message/Data/Formatter/Interface.php';

/** Hermes_Message_Data */
require_once 'Hermes/Message/Data.php';

/** Hermes_Message_Data_Metadata */
require_once 'Hermes/Message/Data/Metadata.php';

/** Hermes_Message_Data_Body */
require_once 'Hermes/Message/Data/Body.php';

/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Data
 */
class Hermes_Message_Data_Formatter_Spread implements Hermes_Message_Data_Formatter_Interface
{
    /**
     *
     * @return Hermes_Message_Data
     */
    public static function format($data)
    {
        $d = new Hermes_Message_Data();
        $m = new Hermes_Message_Data_Metadata();
        $b = new Hermes_Message_Data_Body($data['message']);
        
        // Set group info
        array_map(array($m, 'addChannel'), $data['groups']);
        
        // Set message type
        $m->setType($data['message_type']);
        
        // Set sender
        $m->setSender($data['sender']);
        
        // Set message length
        $m->setLength(strlen($data['message']));
        
        $d->setMetadata($m);
        $d->setBody($b);
        return $d;
    }
}
?>