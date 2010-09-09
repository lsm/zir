<?php

require_once('config.php');
if (array_key_exists('title', $_REQUEST) && array_key_exists('content', $_REQUEST)) {
        $link = mysql_connect($host, $user, $pass);
        mysql_select_db('ng_portal', $link);
        $title = $_REQUEST['title'];
        $content = $_REQUEST['content'];
        $result = mysql_query("select title from `cms_contentindex` where `title` = '$title'");
        $num = mysql_num_rows($result);

        if ($num === 0) {

define('VC_PATH', '/home/workspace/webroot/navgame/www/');
//define('IN_ADMIN', true);


require_once VC_PATH . 'global.php';

// intial env for adding new post
//$_POST['tags'] = 'tag1,tag2,tag3,tag4,tag5';
//$_POST['title'] = 'Articel title!';
//$_POST['digest'] = 0; // 0-3
//$_POST['content'] = 'content!';
//$_POST['imagetolocal'] = 1;
//$_POST['selectimage'] = 1;
//$_POST['autofpage'] = 1;
//$_POST['intro'] = 'The intro!';
//$_POST['author'] = 'The author!';

$author = array('小柯','阿瑪','Pheobe','麦多','老布','西米花','Yamii');
shuffle($author);
$_POST['author'] = array_shift($author);
$cid = $_POST['cid'];
$mid = $_POST['mid'];


require_once D_P . 'require/class_content.php';

class RemoteAdd extends Content {
    
    function __construct($cid, $mid) {
        global $catedb;
        $this->mid = $mid;
        $this->cid = $cid;
        $this->catedb = $catedb;
    }
    
    function addContent() {
        parent::__construct($this->mid);
				$cid = $this->cid;
        
        empty($_POST['title']) && die('No title supplied!');
        $_POST['tagsid'] = $this->tags();
        if($_POST['postdate']) {
                $_POST['postdate'] = PwStrtoTime($_POST['postdate']);
        }else {
                $_POST['postdate'] = $GLOBALS['timestamp'];
        }
        $this->InsertData($_POST,$this->cid);
    }
    
    function tags(){
            global $db;
            $tags = GetGP('tags');
            $tags = Char_cv($tags);
            $tags = explode(',',$tags);
            array_splice($tags,5);
            $tagid = array();
            foreach($tags as $tag){
                    $tag = trim($tag);
                    if(!$tag){
                            continue;
                    }
                    $rs = $db->get_one("SELECT tagid FROM cms_tags WHERE tagname='$tag'");
                    if($rs){
                            $tagid[] = $rs['tagid'];
                    }else{
                            $db->update("INSERT INTO cms_tags SET tagname='$tag',num=0");
                            $tagid[] = $db->insert_id();
                    }
            }
            return $tagid;
    }
}


$ra = new RemoteAdd($cid, $mid);
$ra->addContent();
echo 'ok';
        }
}
exit;
?>
