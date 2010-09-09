<?php

require_once 'Hermes/Message/Listener/Interface.php';

/**
 *
 * @category Hermes
 * @package Hermes_Message
 * @subpackage Listener
 */
class Hermes_Message_Listener_Logger implements Hermes_Message_Listener_Interface
{
    protected $_dbConnection;
    
    protected $_tableName;

    public function __construct($options)
    {
        $this->_dbConnection = mysql_connect($options['host'], $options['username'], $options['password']);
        $this->_tableName = $options['tableName'];
    }

    public function onMessage($msg)
    {
        if ($msg instanceof Hermes_Message_Data) {
            mysql_selectdb('hermes', $this->_dbConnection);
            $q = "INSERT INTO `". $this->_tableName ."` (`id`, `meta`, `body`) VALUES
            ( NULL , '". json_encode($msg->getMetadata()->all()) ."', '". $msg->getBody()->getContent() ."');";
            if (mysql_query($q, $this->_dbConnection)) {
                //echo "Message logged:\n " . $msg->getBody()->getContent() . "\n";
                //var_dump($msg->getMetadata());
            } else {
                echo mysql_error($this->_dbConnection), "\n";
            }
        }
    }
}
?>