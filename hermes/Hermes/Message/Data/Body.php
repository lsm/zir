<?php

/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Data
 */
class Hermes_Message_Data_Body
{
    protected $_data = null;
    
    public function __construct($data = null)
    {
        if ($data !== null) {
            $this->setContent($data);
        }
    }
    
    /**
     *
     * @param string $data
     */
    public function setContent($data)
    {
        $this->_data = $data;
    }
    
    /**
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->_data;
    }
}
?>