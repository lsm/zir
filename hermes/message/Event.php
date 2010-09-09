<?php

require_once './Message.php';

abstract class Event {
    
    public function listener() {
        
    }
    
    public function terminater() {
        
    }
    
    public function trigger($data) {
        
    }
    
    public function operator($data) {
        
    }
    
    public function sleep($time) {
        
    }
}

class Event_Cache extends Event {
    
    public function listener() {
        $m = Message_Spread::getInstance('4803');
        
        while(!$this->terminater()) {
            $msg = new Message_Decorator_Cache($m->subscribe('Cache'));
            if ($this->filter($msg)) {
                $this->operator($msg);
            }
            self::sleep(1000);
        }
    }
    
    public static function trigger($eventId) {
        
    }
    
    public static function sleep($time) {
        usleep($time);
    }
}

class Event_Broker {
    
    public function set
    
    public function setEvent(Event $event) {
        
    }
}

class Hermes_Event_Client_Publisher {
    
}

// Provider
class Event_Producer{}

// Consumer
class Event_Handler{}

class Event_Performer{}





?>