<?php

require_once 'Hermes/Message/Data/Selector/Interface.php';

/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Data
 */
class Hermes_Message_Data_Selector_Mock implements Hermes_Message_Data_Selector_Interface
{
    protected $_return;
    
    public function __construct($return=true)
    {
        $this->_return = $return;
    }
    
    public function matchMetadata($msg)
    {
        return $this->_return;
    }
    
    public function matchBody($msg)
    {
        return $this->_return;
    }
}
?>