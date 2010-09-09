<?php
require_once 'Hermes/Message/Adapter/Abstract.php';

/**
 * Abstract class of adapters
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Adapter
 */
class Hermes_Message_Adapter_Spread extends Hermes_Message_Adapter_Abstract
{
    
    public function __construct($config = array())
    {
        if (!function_exists('spread_connect')) {
            throw new Exception('Spread extension is not loaded/installed.');
        }
        
        $this->_conn = $config['port'] . '@' . $config['host'];
        $this->_user = $config['user'];
        $this->connect($this->_user);
    }
    
    public function connect($userPrefix)
    {
        // @todo Find a better way to make connection name unique
        $count = 0;
        do {
            $this->_connection = spread_connect($this->_conn, $userPrefix . substr(uniqid(rand()), 0, 7));
            $count++;
        } while (!is_resource($this->getConnection()) && $count < 3);
        
        if (!is_resource($this->getConnection())) {
            throw new Exception('Cannot connect to spread!');
        }
    }
    
    public function reconnect()
    {
        spread_disconnect($this->getConnection());
        $this->connect($this->_user);
        if (!is_resource($this->getConnection())) {
            throw new Exception('Cannot connect to spread!');
        }
    }
    
    public function createParticipator($name, $channel)
    {
        spread_join($this->getConnection(), $channel);
        return parent::createParticipator($name, $channel);
    }
    
    public function send(Hermes_Message_Data $message)
    {
        $m = $message->getBody()->getContent();
        if (!is_null($m)) {
            return spread_multicast($this->getConnection(), $this->getAllChannels(), $m);    
        }
        return false;
    }
    
    /**
     *
     * @return array|Hermes_Message_Data
     */
    public function receive()
    {
        $m = spread_receive($this->getConnection(), $this->_timeout_receive);
        
        if (is_array($m)) {
            if ($this->getFormatter()) {
                $m = call_user_func(array($this->getFormatter(), 'format'), $m);
            }
            return $m;
        }
        return null;
    }
}
?>