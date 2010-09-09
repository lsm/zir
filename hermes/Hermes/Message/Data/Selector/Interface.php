<?php

/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Data
 */
interface Hermes_Message_Data_Selector_Interface {
    
    /**
     * Define rules to match metadata
     * 
     * @var Hermes_Message_Data_Metadata $metadata
     * @return boolean true when mached
     */
    public function matchMetadata($metadata);
    
    /**
     * Define rules to match message body
     * 
     * @var Hermes_Message_Data_Body $body
     * @return boolean true when mached
     */
    public function matchBody($body);
}
?>