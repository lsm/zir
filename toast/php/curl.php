<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
ul,ol,li {list-style-type:none;}
.title{ width:100px;margin:0px auto;float: left;}
.data{width:100px;margin:0px auto;float:left;}
</style>
</head>
<body>
<form action="" method="POST">
http://<input name="url" size="30" value="<?=$_POST['url'] ? $_POST['url']:'';?>"/>
循环：<input name="count" size="10" value="<?=$_POST['count'] ? $_POST['count']:'';?>"/>
<input name="sumbit" type="submit"/>
</form>
<?php

/*$url=array( 'http://www.chtopnet.com','http://money.chtopnet.com:8080/topnet/MainCtrl?page=ForwardRobMoneyIndexPage', 'http://pub.chtopnet.com', 'http://talk.chtopnet.com',
'http://qybk.chtopnet.com:8080/topnet/MainCtrl?page=DefaultPage', 'http://app.chtopnet.com:8080/topnet/MainCtrl?page=VideoUploadPage&perform=ent',
'http://app.chtopnet.com:8080/topnet/MainCtrl?page=ForwardCollectIndexPage', 'http://app.chtopnet.com:8080/topnet/MainCtrl?page=ToPrizePage', 'http://game.chtopnet.com/',
'http://bbs.chtopnet.com/', 'http://user.chtopnet.com:8080/topnet/MainCtrl?page=LoginPage');*/

if (isset($_POST['url']))
{
	$url = $_POST['url'];

	echo '<div style="padding:10px;color:blue;clear:both;">'. $url . ':</div>';
	echo '<div><ul><li class="data">TCP</li><li class="data">Transfer</li><li class="data">Total</li></ul></div>';
	echo '<div style="clear:both;"></div>';
	$avg[0]=$avg[1]=$avg[2]=0;
	$count = $_POST['count'] ? $_POST['count']: 15;
	for($i=0;$i<$count;$i++)
	{
		$curl =`curl -o /dev/null -s -w %{time_connect}:%{time_starttransfer}:%{time_total} http://$url `;
		$res = explode(':',$curl);
		echo '<div style="clear:both;"><ul>';
		foreach($res as $k => $v)
		{
			$avg[$k] += $v;
			echo '<li class="data">';
			echo  (float)$v > 0.4 ? ('<font style="color:red;">' . $v . '</font>  '): $v.'  ';
			echo '</li>';
		}
		echo '</ul></div>';
	}
	echo '<div style="clear:both;"><ul style="text-decoration:underline;">';
	//reset($avg);
	for($n =0 ;$n<3;$n++)
	{
		echo '<li class="data">';
		printf('%.3f', (float) $avg[$n] / ($count));
		echo '</li>';
	}
	echo '</ul></div>';
	$j++;
}
//$time_start = microtime(true);
//$ctn = file_get_contents('http://www.chtopnet.com');
//$time_end = microtime(true);
//$time = $time_end - $time_start;

?>

</body></html>