<?php

/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Data
 */
interface Hermes_Message_Data_Formatter_Interface{
    
    /**
     * Format various kinds of data into object, which can be used by message adapter
     *
     * @return Hermes_Message_Data
     */
    public static function format($data);
}

?>