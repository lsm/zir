<?php
global $first_name;
?>
<div class="main">
<div class="appHeader">
	<div class="welcome">
		Welcome <?=$first_name[0]['first_name']?>
	</div>

	<div class="invite">
		<a href="invite.php">invite your Friends</a>
	</div>
</div>

<div id="searchResults" class="searchResults">
<?php
//default
if (!isset($_REQUEST['page']))
{
	$_REQUEST['title'] = 0;
	$_REQUEST['engine'] = $_REQUEST['engine'] ? $_REQUEST['engine'] : 'baidu';
	$_REQUEST['keyword'] = $_REQUEST['keyword'] ? $_REQUEST['keyword'] : '诺基亚';
}
include_once('search.php');
?>
</div>
</div>