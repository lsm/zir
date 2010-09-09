<?php

interface Hermes_Message_Channel_PubSub_Interface{}

class Hermes_Message_Channel_PubSub_Spread implements Hermes_Message_Channel_PubSub_Interface{}

class Hermes_Message_Channel_PubSub_JMS implements Hermes_Message_Channel_PubSub_Interface{}


interface Hermes_Message_Channel_P2P_Interface{}

interface Hermes_Message_Channel_Interface {
    
    public function publish($topic, $content);
    
    public function subscribe($topic);
    
    public function listen();
}

interface Message_Transport_Interface{}

interface Message_Network_Interface{}

class Message_Network_Spread implements Message_Network_Interface{}

interface Message_Model_P2P{}

class Message_Provider{}
class Message_Consumer{}
class Message_Listener{}

class Message_Channel_Topic{}

class Message_Connection_Factory{}
class Message_Connection_Spread{}


class Hermes_Message_Channel_Spread implements Hermes_Message_Channel_Interface {
    
    protected static $_connection = null;
    
    protected static $_instance = null;
    
    protected function __construct($conn) {
        self::$_connection = spread_connect($conn);
    }
    
    protected static function _getConnection() {
            return self::$_connection;
   }
    
    public static function getInstance($conn) {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self($conn);
            if (!is_resource(self::_getConnection())) {
                return false;
            }
        }
        return self::$_instance;
    }
    
    public function publish($topic, $content) {
        if (spread_multicast(self::_getConnection(), $topic, $content)) {
                spread_disconnect(self::_getConnection());
                return true;
            }
        return false;
    }
    
    public function subscribe($topic) {
        return spread_join(self::_getConnection(), $topic);
    }
    
    public function listen($decorator = null) {
        // Null decorator is Message_Decorator_Raw
            return
                $decorator === null
                ? spread_receive(self::_getConnection())
                : call_user_func(array($decorator, 'decorate'), spread_receive(self::_getConnection()));
    }
}

class Message_Selector {
    
}

class Message_Decorator {
    
    public function decorate($message) {
        
    }
}

?>