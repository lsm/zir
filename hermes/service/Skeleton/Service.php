<?php


final class Skeleton_Service
{

    const TRANSPORT_LOCAL = 'Skeleton_Transport_Local';
    const TRANSPORT_HTTP = 'Skeleton_Transport_Http';
    const TRANSPORT_SOCKET = 'Skeleton_Transport_Socket';
    
    const PROTOCOL_NATIVE = 'Skeleton_Protocol_Native';
    const PROTOCOL_PROTOBUF = 'Skeleton_Protocol_ProtoBuf';
    const PROTOCOL_JSON = 'Skeleton_Protocol_Native';
    
    protected $_transport;
    
    protected $_protocol;
    
    public static function getService($component,
                                      $transport = self::TRANSPORT_LOCAL,
                                      $protocol = self::PROTOCOL_NATIVE)
    {
        if ($transport === self::TRANSPORT_LOCAL && $protocol === self::PROTOCOL_NATIVE) {
            return new $component;
        }
        $this->_protocol = new $protocol;
        $this->_transport = new $transport;
        $this->_component = $component;
        $this->_transport->setProtocol($this->_protocol);
    }
    
    public function __call($name, $args)
    {
        /**
        foreach ($args as $k => $arg) {
            if ($arg instanceof Skeleton_Message_Abstract) {
                $args[$k] = $arg->serializeToString();
            }
        }
        **/
        $this->_transport->call($component, $name, $args);
    }
}

?>