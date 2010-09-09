<?php

/**
 * Abstract class of adapters
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Adapter
 */
abstract class Hermes_Message_Adapter_Abstract
{
    protected $_connection = null;
    
    protected $_formatter = null;
    
    protected $_channel = array();
    
    protected $_timeout_receive = 10;
    
    /**
     * Contructor
     */
    public function __construct($config = array())
    {
        // Parsing configuration here
    }
    
    /**
     *
     * @return mixed connection resource
     */
    public function getConnection()
    {
        return $this->_connection;
    }
    
    /**
     * Factory method for getting a new message participator instance
     *
     * @param string $name Name of the participator
     * @param string|array $channel Name of the channel(s) need to join in.
     * @return Hermes_Message_Participator_Abstract
     */
    public function createParticipator($name, $channel)
    {
        if (is_array($channel)) {
            array_map(array($this, 'addChannel'), $channel);
        } else {
            $this->addChannel($channel);    
        }
        
        $memberName = 'Hermes_Message_Participator_' . $name;
        $member = new $memberName();
        $member->setAdapter($this);
        return $member;
    }
    
    /**
     * Join a channel 
     *
     * @param string Name of the channel
     * @return void
     */
    public function addChannel($channel)
    {
        $this->_channel[$channel] = $channel;
    }
    
    /**
     * Get all joined channels
     *
     * @return array 
     */
    public function getAllChannels()
    {
        return $this->_channel;
    }
    
    /**
     * Set a data formatter for adapter
     *
     * @param string $formatter name of the formatter class
     */
    public function setFormatter($formatter)
    {
        $this->_formatter = $formatter;   
    }
    
    /**
     * Get the name of the setted formatter of current adapter
     *
     * @return string|null name of the formatter class
     */
    public function getFormatter()
    {
        return $this->_formatter;    
    }
    
    /**
     * Function use to send message, need to be implemented in particular adapter 
     *
     * @param Hermes_Message_Data $message message need to send
     * @return boolean true on success, otherwise false 
     */
    public abstract function send(Hermes_Message_Data $message);
    
    /**
     * Abstract function use to receive message
     *
     * @return array|Hermes_Message_Data
     */
    public abstract function receive();
}
?>