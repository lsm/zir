<html>
<head></head>
<body>

<form action='button.php' method="post">
<input type="submit" name="button" value="bad"/>
<input type="submit" name="button" value="ok"/>
</form>
<a href="button.php?button=bad">bad</a>
<?php echo $_REQUEST['button'];?>
</body>
</html>
