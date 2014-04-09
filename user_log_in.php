<?php 
ini_set('display_errors','On');
require_once 'connection.php';
session_start();
// Check if log in
if(!isset($_SESSION["session_user_id"])){
	$fail_mess="";
	if(isset($_POST["post_username"]) && isset($_POST["post_password"])){
		$post_username=$_POST["post_username"];
		$post_password=$_POST["post_password"];
		if (!empty($post_username) && !empty($post_password)){
			// Check if the combination of name and password are right
			$sql_sel_userpass="SELECT userid, uname FROM Users 
					WHERE uname='".$post_username."' AND password='".$post_password."'";
			$stmt_sel_userpass = oci_parse($conn, $sql_sel_userpass);
			oci_execute($stmt_sel_userpass, OCI_DEFAULT);
			$err=oci_error($stmt_sel_userpass);
			if($err){
				$fail_mess="Some unknown error occured: ".$err['message']."<br />";
			}else{
				if ($user_info=oci_fetch_row($stmt_sel_userpass)){ 
					// Log in successful
					$_SESSION["session_user_id"]=$user_info[0];
					$_SESSION["session_user_name"]=$user_info[1];
					$wordlist_url = "frame_view_wordlist.php";
					header('Location: '.$wordlist_url);
				}else{
					$fail_mess="Invalid username and password combination. ";
				}
			}
		}else{
			$fail_mess="Invalid username and password combination. ";
		}
	}	
}else{
	// Already log in
	$wordlist_url = "frame_view_wordlist.php";
	header('Location: '.$wordlist_url);
}
oci_close($conn);
?>
<html>
<head>
<title>Log In</title>
</head>
<body>
	<table>
	<tr><td><a href="user_sign_in.php">Sign in</a></td>
	<td><a href="welcome.php">Home</a></td></tr>
	</table>
	<!-- A form to input username and password -->
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		Username:<input type="text" name="post_username" /><br />
		Password:<input type="password" name="post_password" /><br />
		<input type="submit" value="log in" />
	</form>
	<?php echo $fail_mess;?><br />
	If you don't have an account, please sign in. 
</body>
</html>
