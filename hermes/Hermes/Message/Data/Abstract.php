<?php
/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Data
 */
abstract class Hermes_Message_Data_Abstract
{
    /**
     * @var Hermes_Message_Data_Metadata
     */
    protected $_metadata;
    
    /**
     * @var Hermes_Message_Data_Body
     */
    protected $_body;
    
    /**
     *
     * @param mixed|Hermes_Message_Data_Metadata $metadata Instance of metadata object
     */
    public function setMetadata($metadata)
    {
        $this->_metadata = $metadata;
    }
    
    /**
     *
     * @return Hermes_Message_Data_Metadata_Abstract
     */
    public function getMetadata()
    {
        if (!$this->_metadata instanceof Hermes_Message_Data_Metadata) {
            $this->_metadata = new Hermes_Message_Data_Metadata();
        }
        return $this->_metadata;
    }
    
    /**
     *
     * @var string|Hermes_Message_Data_Body $body
     */    
    public function setBody($body)
    {
        $this->_body = $body;
    }
    
    /**
     *
     * @return Hermes_Message_Data_Body_Abstract
     */    
    public function getBody()
    {
        if (!$this->_body instanceof Hermes_Message_Data_Body) {
            $this->_body = new Hermes_Message_Data_Body();
        }
        return $this->_body;
    }
}
?>