<?php

/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Data
 */
class Hermes_Message_Data_Metadata
{
    protected $_type = null;
    
    protected $_sender = null;
    
    protected $_length;
    
    protected $_channel;
    
    public function all()
    {
        $t['sender'] = $this->getSender();
        $t['channel'] = $this->_channel;
        $t['type'] = $this->getType();
        $t['length'] = $this->getLength();
        return $t;
    }
    
    public function addChannel($channel)
    {
        $this->_channel[] = $channel;
    }
    
    public function setType($type)
    {
        $this->_type = $type;
    }
    
    public function getType()
    {
        return $this->_type;
    }
    
    public function setSender($sender)
    {
        $this->_sender = $sender;
    }
    
    public function getSender()
    {
        return $this->_sender;
    }
    
    
    public function setLength($length)
    {
        $this->_length = $length;
    }
    
    public function getLength()
    {
        return $this->_length;
    }
}

?>