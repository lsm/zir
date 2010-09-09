<?php

require_once('facebook_init.php');
include_once('nav.php');
$uid = $facebook->require_login();
?>

<div class="appHeader">
<div class="title">Add keywords</div>
<div class="form" style="width:50%;">
<form id="addKeyword">
<input name="keyword" type="text" value="keyword" style="font-size:100%;width:100px;">
<input type="hidden" name="verify" value="1" />
<select name="engine">
<option  value="yahoo" >Yahoo</option>
<option  value="baidu" >Baidu</option>
</select>
Fulltext: <input type="image"  class="input" src="http://www.oscan.org/gnews/images/facebook_go.gif" alt="submit add" clickrewriteurl="<?=$appcallbackurl.'add_keyword.php';?>" clickrewriteid="lockerList" clickrewriteform="addKeyword">
</form>
</div>

<div class="form" style="width:20%;">
<form id="updateProfile">
UpdateProfile: <input type="image"  class="input" src="http://www.oscan.org/gnews/images/facebook_go.gif" alt="submit update" clickrewriteurl="<?=$appcallbackurl.'profile.php?uid='.$uid;?>" clickrewriteid="updateResults" clickrewriteform="updateProfile">
</form>


</div>
</div>

<div id="updateResults" class="searchResults">
</div>

<div id="lockerList" class="lockerList">
<?php
include('show_locker.php');

?>

</div>

<div id="searchResults" class="searchResults">
</div>