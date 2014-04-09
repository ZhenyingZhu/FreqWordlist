<?php 
session_start();
if(isset($_SESSION["session_user_id"])){
	// Reset array
	$_SESSION=array();
	if(isset($_COOKIE[session_name()])){
		setcookie(session_name(),'',time()-3600);
	}
	session_destroy();
	$home_url = "welcome.php";
	header('Location:'.$home_url);
}
?>
<html>
<head>
<title>Log Out</title>
</head>
<body>
You have already log out! Please click <a href="welcome.php">Home</a> to return. 
</body>
</html>