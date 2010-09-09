<?php
session_start();
if (isset($_POST['username']) && isset($_POST['password']))
{
	include_once('include/functions.php');
	include_once('include/conn.php');

	$username = stripslashes(strval(mosGetParam($_POST, 'username', 0)));
	$password = stripslashes(strval(mosGetParam($_POST, 'password', 0)));
	$sql = "SELECT * FROM user WHERE user_name = '$username';";
	$res = mysql_query($sql);
	$row = mysql_fetch_object($res);

	$time = time()+3600*7*24;
	$cookie = base64_encode($row->id .';'. $username .';'. $time .';'. md5($time.$username.$row->id.$post_key.$row->password));

	if($row->password == md5($password))
	{
		setcookie('zir', $cookie, $time, '', '', '', 1);
		$_SESSION['user_name'] = $username;
		$_SESSION['user_id'] = $row->id;
		myheader("index.php");
	}
	else
	{
		myheader("login.php");
	}
}
else
{
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml"><head><title>News</title>
	
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="STYLESHEET" type="text/css" href="css/style_global.css">
	<script language="javascript">
	function mouseOverImg(ObjId, img)
	{
		var obj = document.getElementById(ObjId);
		obj.src=img;
	}
	function mouseOutImg(ObjId, img)
	{
		var obj = document.getElementById(ObjId);
		obj.src=img;
	}
	</script>
	</head>
	<body>
	<div id="login">
	        <div class="tb1">
	          <div class="tb2">
	            <div class="tb3">
	              <div class="tb4">
	                <div class="tb5">
	                  <div class="tb6">
	                    <div class="tb7">
			<form action="" method="POST">
				<div class="input" ><div class="label" >Login:</div><input name="username" type="text" size="10"/></div>
				<div class="input"  ><div class="label" >Password:</div><input name="password" type="password" size="10"/></div>
				<div class="input">
				<input id="login_button" name="sumbit" type="image" src="images/login.png" />
				<!--	<input id="login_button" name="sumbit" type="image" 
						src="images/login_b1.png" 
						onmouseover="mouseOverImg('login_button', 'images/login_b2.png')" 
						onmouseout="mouseOutImg('login_button', 'images/login_b1.png')"/>-->
				</div>
			</form>
			</div>
		        </div>
	          </div>
	       </div>
	    </div>
	  </div>
	</div>
      </div>
	
	<?php
	include_once('footer.php');

}

?>
