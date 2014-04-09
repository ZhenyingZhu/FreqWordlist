<?php 
ini_set('display_errors','On');
require_once 'connection.php';
$err_message="";
if(isset($_POST["post_new_username"]) && isset($_POST["post_new_password1"]) && isset($_POST["post_new_password2"])){
	$post_new_username=$_POST["post_new_username"];
	$post_new_password1=$_POST["post_new_password1"];
	$post_new_password2=$_POST["post_new_password2"];
	if(!empty($post_new_username) && !empty($post_new_password1) && !empty($post_new_password2)){
		if($post_new_password1 != $post_new_password2){
			$err_message="The passwords you input are not same. ";
		}else{
			// Check if the username has already been used. 
			$sql_sel_user="SELECT * FROM Users WHERE uname='".$post_new_username."'";
			$stmt_sel_user = oci_parse($conn, $sql_sel_user);
			oci_execute($stmt_sel_user, OCI_DEFAULT);
			$err=oci_error($stmt_sel_user);
			if($err){
				$err_message="Some unknown error occured: ".$err['message']."<br />";
			}else{
				if (oci_fetch_row($stmt_sel_user))
				{
					$err_message="This username has been used. ";
				}else{
					// Select proper userid
					$sql_sel_lastid="SELECT MAX(userid) FROM Users";
					$stmt_sel_lastid=oci_parse($conn, $sql_sel_lastid);
					oci_execute($stmt_sel_lastid, OCI_DEFAULT);
					$err2=oci_error($stmt_sel_lastid);
					if($err2){
						$err_message="Some unknown error occured: ".$err2['message']."<br />";
					}else{
						$last_id=oci_fetch_row($stmt_sel_lastid);
						$create_id=$last_id[0]+1;
						// Insert new user
						$sql_ins_newuser="INSERT INTO Users (userid, uname, password)
						VALUES (".$create_id.", '".$post_new_username."', '".$post_new_password1."')";
						$stmt_ins_newuser=oci_parse($conn, $sql_ins_newuser);
						oci_execute($stmt_ins_newuser, OCI_DEFAULT);
						$err3=oci_error($stmt_ins_newuser);
						if($err3){
							oci_rollback($conn);
							$err_message="Some unknown error occured: ".$err3['message']."<br />";
						}else{
							oci_commit($conn);
							$success_url = "user_sign_success.php";
							header('Location: '.$success_url);
						}
					}
				}
			}
		}
	}else{
		$err_message="Please input all fileds. ";
	}
}
oci_close($conn);		
?>
<html>
<head>
<title>Sign In</title>
</head>
<body>
	<table>
	<tr><td><a href="user_log_in.php">Log in</a></td>
	<td><a href="welcome.php">Home</a></td></tr>
	</table>
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
		Username:<input type="text" name="post_new_username" /><br />
		Password:<input type="password" name="post_new_password1" /><br />
		Comfirm:<input type="password" name="post_new_password2" /><br />
		<input type="submit" value="sign in" />
	</form>
	<?php echo $err_message;?>
</body>
</html>
