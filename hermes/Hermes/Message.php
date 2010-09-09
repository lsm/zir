<?php

/**
 * Helper class for Message subpackage
 *
 * @category Hermes
 * @package Hermes_Message
 */
class Hermes_Message
{
    /**
     * Factory function use to get the instance of various kinds of addpters.
     *
     * @param string $adapter Name of message adapter (Spread, Jabber etc)
     * @param array $config settings need to initialize the adapter
     * @return Hermes_Message_Adapter_Abstract
     */
    public static function factory($adapter, $config = array()) 
    {
        //@todo Find & check adapter, prepare configuration
        $adapter = 'Hermes_Message_Adapter_' . $adapter;
        return new $adapter($config);
    }
}
?>