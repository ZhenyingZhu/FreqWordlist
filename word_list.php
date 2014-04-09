<!DOCTYPE table PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
session_start();
// Judge if user is log in. 
if(isset($_SESSION["session_user_name"]) && isset($_SESSION["session_user_id"])){
	$session_user_name=$_SESSION["session_user_name"];
	$session_user_id=$_SESSION["session_user_id"];
	$login_mess="You are logged in as ".$session_user_name.".";
}else{
	$login_url="user_log_in.php";
	header("Location: ".$login_url);
}
if(isset($_POST["post_chose_sub"])){
	$post_chose_sub=$_POST["post_chose_sub"];
}
ini_set('display_errors', 'On');
require_once 'connection.php';
?>
<html>
<head>
<title>World List</title>
</head>
<body>
<table>
<tr>
<td><a href="welcome.php" target="_top">Home</a><td>
<td><?php echo $login_mess?></td>
<td><a href="user_log_out.php" target="_top">Log Out</a><td>
</table>

<!-- A form to select subject -->
<form name="list" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
<!-- Left cell show the subject list, right cell show the word list in an iframe-->
<table><tr><td style="vertical-align:top">
	<fieldset>
		<table>
		<tr><td><a href="my_wordlist.php" target="iframe_list">My Word List</a></td></tr>
		<?php 
		// Select subject list. 
		$sql_sel_sub="SELECT sname FROM Subjects";
		$stmt_sel_sub = oci_parse($conn, $sql_sel_sub);
		oci_execute($stmt_sel_sub, OCI_DEFAULT);
		$err=oci_error($stmt_sel_sub);
		if($err){
			$err_message="Some unknown error occured: ".$err['message']."<br />";
			echo $err_message;
		}else{
			// Create radio group and use the last choise as default. 
			while ($sub=oci_fetch_row($stmt_sel_sub))
			{
				$subject_name=$sub[0];
				echo "<tr><td><input type=\"radio\" name=\"post_chose_sub\" value=\"".$subject_name."\"";
				if(isset($post_chose_sub)){
					if($subject_name==$post_chose_sub)
						echo "checked></td>";
					else echo "></td>";
				}else{
					echo "></td>";
				}
				echo "<td>".$subject_name."</td></tr>";
			}
		}
		?>
		</table>
		<input type="submit" value="submit" />
	</fieldset></td>
	
	<td>
		<?php 
		if(isset($post_chose_sub)){
			$iframe_url="subject_wordlist.php";
			$_SESSION["session_chose_sub"]=$_POST["post_chose_sub"];
		}else{
			// Default. 
			$iframe_url="my_wordlist.php";
		} 
		
		oci_close($conn);
		?>
		<iframe width="500" height="400" src="<?php echo $iframe_url;?>" name="iframe_list"></iframe>
</td></tr></table>	
</form>

</body>
</html>
