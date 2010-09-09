<?php

var_dump($_POST);
$s = fopen("php://input", "r");
while ($d = fread($s, 100)) {
	var_dump($d);
}
phpinfo();
?>

<form action="chkbox.php" name="chkbox" method="POST">
<input name="sel" type="checkbox" value="1">1</input>
<input name="sel" type="checkbox" value="2">2</input>
<input name="sel" type="checkbox" value="3">3</input>
<input name="sel" type="checkbox" value="4">4</input>
<input type="submit"></input>
</form>