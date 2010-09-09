<?php
/* $Id$ */

error_reporting(E_ALL);
function dd($var, $msg = 'Debug:')
{
	echo '<pre>' . $msg . '<br />';
	var_dump($var);
	echo '</pre>';
}

/*$url = "http://news.sina.com.cn/w/2007-05-13/040012973194.shtml";
$news = new getByTag($url, '<div class="artibody" id="artibody">', '</div>');
$news->cut();
dd(trim($news->content));*/

$url = 'http://news.163.com/07/0507/10/3DSRG1I2000120GU.html';
$url ='http://news.google.com/news?ned=us&topic=h&output=rss';

$context =
array('http' =>
array('method' => 'GET',
	'header' => "Accept-language: en\r\n".
	"User-Agent: Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.0)\r\n"
));
//ini_set('user_agent', "PHP\r\nX-MyCustomHeader: Foo");
$contextid=stream_context_create($context);
echo file_get_contents($url, false, $contextid);

$news = new getByTag($url, '<div id="endText">', '</div>');
$news->cut();
dd(trim($news->content));


/*$news = new DOMDocument('','gb2312');
$news->loadHTML($content);
echo $news->validate();
$atrr = $news->getElementById("artibody");*/
class getByTag{
	var $startTag;
	var $endTag;
	var $source;
	var $content;

	function __construct($source, $startTag, $endTage)
	{
		$this->source = $source;
		$this->startTag = $startTag;
		$this->endTag = $endTage;
	}

	function cut()
	{
		$this->content = file_get_contents($this->source);
		$length = strlen($this->content);
		$tagLen = strlen($this->startTag);
		$from = strpos($this->content,$this->startTag);
		$end = strpos(substr($this->content, $from, $length), $this->endTag);
		$this->content =  strip_tags(substr($this->content, $from+$tagLen, $end-$tagLen), '<p>');
		$this->content = $this->strToUTF8($this->content);
	}

	function strToUTF8($str)
	{
		$encode = mb_detect_encoding($str, 'gb2312,utf-8,gbk,ascii');
		if ($encode == 'UTF-8')
		{
			return $str;
		}
		else
		{
			return  iconv($encode, 'utf-8', $str);
		}
	}
}
?> 