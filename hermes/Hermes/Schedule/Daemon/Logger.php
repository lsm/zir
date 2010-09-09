<?php

require_once 'Hermes/Schedule/Daemon/Abstract.php';

class Hermes_Schedule_Daemon_Logger extends Hermes_Schedule_Daemon_Abstract {
    
    public function init($options)
    {
        $this->_options = $options;
    }
    
    public function getNext($slot)
    {
        $this->lock();
        $channel = $this->getVar('channel');
        if ($channel == null) {
            $channel = 0;
        }
        $this->setVar('channel', $channel + 1);
        $this->unlock();
        return $this->getVar('channel');
    }
    
    public function run($next, $slot)
    {
        require_once 'Hermes/Message.php';
        require_once 'Hermes/Message/Adapter/Spread.php';
        require_once 'Hermes/Message/Participator/Subscriber.php';
        require_once 'Hermes/Message/Listener/Debug.php';
        require_once 'Hermes/Message/Listener/Logger.php';
        require_once 'Hermes/Message/Listener/Queue.php';
        require_once 'Hermes/Message/Data/Selector/Mock.php';
        require_once 'Hermes/Message/Data/Formatter/Spread.php';
        
        $spread = Hermes_Message::factory('Spread', array('host'=>'localhost', 'port'=>'4803', 'user'=>'LOG'));
        $sub = $spread->createParticipator('Subscriber', "$next");
        $spread->setFormatter('Hermes_Message_Data_Formatter_Spread');
        
        //$id = ftok(__FILE__, 'a');
        //$res = msg_get_queue($id);
        $this->_options['tableName'] = $this->_options['tableName'] . $next;

        $listener = new Hermes_Message_Listener_Logger($this->_options);
        
        $sub->setMessageListener($listener);
        //$sub->setMessageListener(new Hermes_Message_Listener_Queue($res));
        $sl = new Hermes_Message_Data_Selector_Mock();
        
        while(true) {
            $sub->subscribe($sl);
        }
        return 0;
    }
}
