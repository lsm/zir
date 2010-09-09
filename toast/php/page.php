<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Sample 2_1</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="loadpage.js"></script>
</head>
<body onload="makerequest ('trains.php','show')">
	<div align="center">
		<h1>My Webpage</h1>
		  <a href="trains" onclick="makerequest('trains.php','show'); return false;">Trains</a> 
		| <a href="safe" onclick="makerequest('safe.php','show'); return false;">Safe</a> 
		| <a href="content3.html" onclick="makerequest('content3.html','show'); return false;">Page 3</a> 
		| <a href="content4.html" onclick="makerequest('content4.html','show'); return false;">Page 4</a>
		| <a href="yahoo" onclick="makerequest('http://yahoo.com','show'); return false;">yahoo</a>
		<div id="show"></div>
	</div>
</body>
</html>
