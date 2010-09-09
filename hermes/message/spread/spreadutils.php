<?php

class Message {

    var $headers;
    var $content;

    function __construct() {
        $num_args = func_num_args();
        switch ($num_args) {
            case '0': 
                $this->headers = array();
                $this->content = '';
                break;
            case '2':
                $this->headers = func_get_arg(0);
                $this->set_content(func_get_arg(1));
                break;
        }
    }
    
    function set_header($name, $val) {
        $this->headers[$name] = $val;   
    }
    
    function del_header($name) {
        
    }
    
    function get_header($name) {
        return $this->headers[$name];   
    }
    
    function set_content($content) {
        $this->content = $content;
    }
    
    function get_content() {
        return $this->content;
    }
    
    function str() {
        $rtn = '';
        foreach ($this->headers as $key=>$value) {
            $rtn = $rtn . $key . ': ' . $value . '
';   
        }
        return $rtn . '
' . $this->content;
    }
}

?>
