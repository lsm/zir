<?php defined(_ZIR) or die('<meta http-equiv="refresh" content="0;url=login.php">')?>

<div class="main">
<script type="text/javascript">
function sumbit(id)
{
	if($(id).title.value == '' || $(id).query.value == '' )
	{
		alert("要把标题和查询都添上哦～！");
		return false;
	}
	else
	{
		var num = 0;
		$(id).send({onComplete: alert('加入 '+$(id).query.value+' 成功！')});
/*		$ES('div','container').each(function(el){num++;});
		alert(num);*/
	}
}

function move()
{
	$('move').effects({
		duration: 1000,
		transition: Fx.Transitions.bounceOut
	}).start({
	'width':[0,100],
	'height':[0,100]
	});
}

function opacity()
{
	$('fxTarget').effect('opacity').start(0,.5);
}

function del(id)
{
	if(confirm('确定要删除吗？') == true)
	{
		var form_id = 'del_form_' + id;
		var container_id = 'container_' + id;
		$(form_id).send(
		{
			onComplete:
			function(){
				alert('删除成功！');
			}
		});
		$(container_id).parentNode.removeChild($(container_id));
	}
	else
	{
		return false;
	}
}
</script>
<form id="add_news" action="news_setting.php" style="margin-left:200px;">
	显示标题: <input name="title" type="text" value="">
	查询内容: <input name="query" type="text" value="">
	<input name="action" type="hidden" value="add"/>
	<span onclick="sumbit('add_news')" style="cursor: pointer;border: 1px dotted #fff;">加入</span>
<!--	<input name="source" type="text" value="搜索引擎">-->
</form>
</div>

<div class="main">
<?php
include_once('include/functions.php');
include_once('include/conn.php');
$sql = "SELECT * FROM news;";
$res = mysql_query($sql);

if (mysql_num_rows($res) > 0)
{
	for ($j=0; $j < mysql_num_rows($res); $j++)
	{
		$row = mysql_fetch_object($res);
?>
	<div class="container" id="container_<?=$j?>">
  		<div style="cursor: move;" class="dragger"><div class="title"><?=$row->title.'    [RSS源：'.strtoupper($row->source).']'?></div><a class="del" onclick="del('<?=$j?>')">删除</a></div>
  		<form action="news_setting.php" method="POST" id="del_form_<?=$j?>">
  		<input name="action" type="hidden" value="del"/>
  		<input name="news_id" type="hidden" value="<?=$row->id?>">
  		</form>
		<?php news($row->query, $row->source);?>
	</div>
<?php
	}
}
?>

</div>

