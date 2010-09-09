<?php

error_reporting(E_ALL | E_STRICT);

class Save{
	
	function __construct(){
		$link = mysql_connect('127.0.0.1', 'root', '123qwe');
		mysql_select_db('ng', $link);
	}
	
	function save_item($item) {
        foreach($item as $k => $v) {
            ${$k} = $v;
        }
        $sql = "INSERT INTO `ng`.`items` (`id` ,`url` ,`channel` ,`type` ,
        `rule_name` ,`parent` ,`title`, `content` ,`created`) VALUES (NULL , ";
        
        if ($type == 'list') {
            foreach(json_decode($_REQUEST['links']) as $v) {
                if (count(split(',', $v)) > 1) {
                    $t = 'paging';
                } else {
                    $t = 'content';
                }
                $query = $sql . "'$v', '$channel', '$t', '$rule_name', '0', '', '', NOW( ));";
                mysql_query($query);
            }            
        }
        
        if ($type == 'content') {
						$content = mysql_escape_string($content);
            //$query = $sql . "'$url', '$channel', 'got', '$rule_name', '', '$title', '$content', NOW( ));";
            $query = "UPDATE `items` SET `content` = '$content', `title` = '$title',
            `type` = 'got', `created` = NOW( ) WHERE `items`.`url` = '$url' LIMIT 1 ;";
            if(!mysql_query($query)){
            	echo mysql_error();	
            }
        }
        
        if ($type == 'paging') {            
            $query = "SELECT id FROM items WHERE url='$url' LIMIT 1;";
            $res = mysql_query($query);
            $obj = mysql_fetch_object($res);
            foreach(json_decode($_REQUEST['links']) as $v) {
                $query = $sql . "'$v', '$channel', 'content', '$rule_name', '$obj->id', '', '', NOW( ));";
                mysql_query($query);
            }
            $query = "UPDATE `items` SET `parent` = '$obj->id', `type` = 'content', `created` = NOW( ) WHERE `items`.`url` = '$url' LIMIT 1 ;";
            mysql_query($query);
        }
	}	
}

$s = new Save();
$s->save_item($_POST);


?>
