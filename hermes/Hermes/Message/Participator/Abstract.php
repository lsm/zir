<?php

/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Participator
 */
abstract class Hermes_Message_Participator_Abstract
{
    /**
     *
     * @var Hermes_Message_Adapter_Abastract $_adapter
     */
    protected $_adapter = null;
    
    /**
     * Set the adapter instance for participator
     *
     * @param Hermes_Message_Adapter_Abstract $adapter Instance of adapter
     */
    final public function setAdapter(Hermes_Message_Adapter_Abstract $adapter)
    {
        $this->_adapter = $adapter;
    }
    
    /**
     * Get the instance of adapter
     * 
     * @return null|Hermes_Message_Adapter_Abstract
     */
    final public function getAdapter()
    {
        return $this->_adapter;
    }
}
?>