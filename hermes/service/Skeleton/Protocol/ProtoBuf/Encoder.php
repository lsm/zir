<?php

class Skeleton_Protocol_ProtoBuf_Encoder
{
    const NUMERIC     = 0;
    const DOUBLE      = 1;
    const STRING      = 2;
    const STARTGROUP  = 3;
    const ENDGROUP    = 4;
    const FLOAT       = 5;
    const MAX_TYPE    = 6;
    
    protected $_buff = array();
    
    public function getBuff()
    {
        return $this->_buff;
    }
    
    public function put8($v)
    {
        if ($v < 0 || $v >= (1<<8)) {
            throw new Exception('u8 too big');
        }
        $this->_buff[] = ($v & 255);
    }
    
    public function put16($v)
    {
        if ($v < 0 || $v >= (1<<16)) {
            throw new Exception('u16 too big');
        }
        $this->_buff[] = (($v >> 0) & 255);
        $this->_buff[] = (($v >> 8) & 255);
    }
    
    public function putRawString($v)
    {
        $this->_buff[] = pack('c', $v);
    }
}

$en = new Skeleton_Protocol_ProtoBuf_Encoder();

//echo $en->put8(120);
//$en->put16(3120);
$en->putRawString('h');

var_dump($en->getBuff());

//echo (1 << 64);


?>