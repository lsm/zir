<?php
include_once('functions.php');
$url ='http://news.baidu.com/ns?tn=newsrss&sr=0&cl=2&rn=10&ct=0&word=';
//$url = 'http://news.search.yahoo.com/news/rss?ei=UTF-8&fl=0&x=wrt&p=';
$result = get_rss('facebook', $url);
//$xml = new SimpleXMLElement($result);
?>
<div style="margin:2px;padding:2px;width:40%;float:left;">
<div style="border:1px dotted silver;margin:10px;padding:6px;">
Here is the hotest news about 'facebook' from Yahoo! News.
</div>
<div style="border:1px dotted silver;margin:10px;padding:6px;">
Want have news feed in your profile with your own keyword?
</div>
<div style="border:1px dotted silver;margin:10px;padding:6px;">
<div style="background-color:#245879;margin:20px;padding:10px;text-align:center;">
<a href="http://www.facebook.com/add.php?api_key=2812abca767244fe28cd9d46cc1d0961" style="color:#fff;">Install Ethos News Feed</a>
</div>
</div>
</div>

	<div style="border:1px dotted silver;margin:2px;padding:2px;width:56%;float:left;">
		<div style="border:1px dotted silver;margin:2px;padding:2px;position:relative;float:left;">
			News feed for your keyword: 'facebook'
		</div>
	<div style="clear:both;"></div>
	<div style="background-color:#F2F4F7;margin:5px;padding:2px;">
	<?php
	for ($i=0; $i < count($result['items']) && $i <10 ; $i++)
	{
		echo '<ul style="list-style-type:none;padding:2px;margin:5px;">'."\n";
/*		$title = iconv('gb2312', 'utf-8', strtr($result['items'][$i]['title'], array('<![CDATA['=>'', ']]>'=>'')));
		$link =  iconv('gb2312', 'utf-8', strtr($result['items'][$i]['link'], array('<![CDATA['=>'', ']]>'=>'')));
		$des = strip_tags(html_entity_decode(iconv('gb2312', 'utf-8', strtr($result['items'][$i]['description'], array('<![CDATA['=>'', ']]>'=>'')))), '<p>,<a>,<br>');*/
		$title = $result['items'][$i]['title'];
		$link =  $result['items'][$i]['link'];
		$des = strip_tags(html_entity_decode($result['items'][$i]['description']), '<p>,<a>,<br>');
		echo '
		<div style="float:left;margin:0px;padding:0px;">
			<fb:share-button class="meta">
				<meta name="title" content=" '.str_replace('"', '”',strip_tags($des)).' " />
				<meta name="description" content=" '.str_replace('"', '”',strip_tags($des)).' " />
				<link rel="target_url" href="'.$link.'"/>
			</fb:share-button>
		</div>
		<li style="font-size:12px;margin-left:10px;"><a href="#" clicktoshow="des_'.$i.'" value="show">'.$title.'</a>
		</li>
		'."\n";
		echo '<li id="des_'.$i.'" style="padding-left:15px;display:none;"><a href="#" clicktohide="des_'.$i.'" value="hide">hide</a><br />'.$des.'<a href="'.$link.'" target="_blank">  read more</a></li>'."\n";
		echo '</ul>';
	}
	?>
	</div>
	</div>

