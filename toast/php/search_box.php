<?php
global $appcallbackurl;
?>

<div class="appHeader">
<div class="title">Baidu News:</div>
<div class="form">
<form id="searchBaidu">
<input name="keyword" type="text" value="search" style="font-size:100%;width:100px;">
<input name="engine" type="hidden" value="baidu" >
Title: <input type="image"  class="input" src="http://www.oscan.org/gnews/images/facebook_go.gif" alt="submit search" clickrewriteurl="<?=$appcallbackurl.'search.php?title=1';?>" clickrewriteid="searchResults" clickrewriteform="searchBaidu">
Fulltext: <input type="image"  class="input" src="http://www.oscan.org/gnews/images/facebook_go.gif" alt="submit search" clickrewriteurl="<?=$appcallbackurl.'search.php?title=0';?>" clickrewriteid="searchResults" clickrewriteform="searchBaidu">
</form>
</div>
</div>

<div class="appHeader">
<div class="title">Yahoo! News:</div>
<div class="form">
<form id="searchYahoo">
<input name="keyword" type="text" value="search" style="font-size:100%;width:100px;">
<input name="engine" type="hidden" value="yahoo" >
Title: <input type="image"  class="input" src="http://www.oscan.org/gnews/images/facebook_go.gif" alt="submit search" clickrewriteurl="<?=$appcallbackurl.'search.php?title=1';?>" clickrewriteid="searchResults" clickrewriteform="searchYahoo">
Fulltext: <input type="image"  class="input" src="http://www.oscan.org/gnews/images/facebook_go.gif" alt="submit search" clickrewriteurl="<?=$appcallbackurl.'search.php?title=0';?>" clickrewriteid="searchResults" clickrewriteform="searchYahoo">
</form>
</div>
</div>

<br clear="all" />