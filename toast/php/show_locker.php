<?php
include_once('config.php');
include_once('conn.php');
$appcallbackurl = 'http://www.oscan.org/gnews/';

if ($_POST['verify'])
{
	$uid = $_POST['fb_sig_user'];
}
else
{
	global $uid;
}
$sql = 'SELECT id, user_id, engine, keyword '
.'From feeds '
."WHERE user_id = '$uid'; ";

$result = mysql_query($sql) or die(mysql_error());

?>


<ol>
<?php
while ($row = mysql_fetch_object($result))
{//Display | keyword| engine| share| delete| show
	$title = "Try search '$row->keyword' in '$row->engine'.";
	$des = 'Ethos News Feed';
	$link = 'http://apps.facebook.com/enewsfeed/search.php?keyword=' 
		. $row->keyword .'&engine='.$row->engine;
		?>
		<li>
		<div class="locker">
		
			<div class="item">
				<?=$row->keyword?>
			</div>
			
			<div class="item">
				<?=$row->engine?>
			</div>
			
			<div class="item">
				<fb:share-button class="meta">
					<meta name="title" content="<?=$title?>" />
					<meta name="description" content="<?=$des?>" />
					<link rel="target_url" href="<?=$link?>"/>
				</fb:share-button>
			</div>
			
			<div class="item">
				<form id="delKeyword_<?=$row->id?>">
					<input type="hidden" name="verify" value="1" />
					<input type="hidden" name="feed_id" value="<?=$row->id?>" />
					<input type="image"  class="input" src="http://www.oscan.org/gnews/images/b_drop.png" alt="submit delete" 
					clickrewriteurl="http://www.oscan.org/gnews/del_keyword.php" 
					clickrewriteid="lockerList" clickrewriteform="delKeyword_<?=$row->id?>">
				</form>
			</div>
			
			<div class="item">
				<form id="searchRss_<?=$row->id?>">
					<input name="keyword" type="hidden" value="<?=$row->keyword?>" >
					<input name="engine" type="hidden" value="<?=$row->engine?>" >
					Show: <input type="image"  class="input" src="http://www.oscan.org/gnews/images/facebook_go.gif" alt="submit search" 
					clickrewriteurl="<?=$appcallbackurl.'search.php?title=0&add_profile=0';?>" 
					clickrewriteid="searchResults" clickrewriteform="searchRss_<?=$row->id?>">
				</form>
			</div>
		</div>
		</li>
		<?php
}
?>
</ol>



