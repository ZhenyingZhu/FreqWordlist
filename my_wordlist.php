<!DOCTYPE table PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
session_start();
if(isset($_SESSION["session_user_name"]) && isset($_SESSION["session_user_id"])){
	$session_user_name=$_SESSION["session_user_name"];
	$session_user_id=$_SESSION["session_user_id"];
}else{
	$login_url="user_log_in.php";
	header("Location: ". $login_url);
}
$login_mess="You are logged in as ".$session_user_name.".";
ini_set('display_errors', 'On');
require_once 'connection.php';

// Default
$sql_order=" ORDER BY L.degree ASC";
if(isset($_POST["post_show_order"])){
	$post_show_order=$_POST["post_show_order"];
	switch ($post_show_order){
		case 0: 
			$sql_order=" ORDER BY L.degree ASC";
			break;
		case 1:
			$sql_order=" ORDER BY L.degree DESC";
			break;
		case 2:
			$sql_order=" ORDER BY L.spell ASC";
			break;
		case 3:
			$sql_order=" ORDER BY L.spell DESC";
			break;
	}
}
?>
<html>
<head>
<title>My Word List</title>
</head>
<body>

<!-- The form decides the order -->
<form action="my_wordlist.php" method="post">
	<select name="post_show_order">
		<?php
		$order_method=array("Increase By Degree", "Decrease By Degree", 
			"Increase By alphabetic", "Decrease By alphabetic");
		for($i=0;$i<4;$i++){
			// Default
			if(isset($post_show_order) && $post_show_order==$i){
				echo "<option value=".$i." selected>".$order_method[$i]."</option>";
			}else{
				echo "<option value=".$i.">".$order_method[$i]."</option>";
			}
		} 
		?>
		</select> <input type="submit" value="arrange" />
</form>

<!-- The form decides the rate. -->
<form action="my_wordlist.php" method="post">
	<table border="1">
		<tr><td></td><td>Choose a subject you are interested in from left and view its word list. </td>
		<td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>delete</td></tr>
		<?php 
		$count_word=0;
		// Select user wordlist and output it in a decrease order. 
		$sql_sel_myword="SELECT L.spell, L.degree FROM Learn L 
			WHERE L.userid=".$session_user_id.$sql_order;
		$stmt_sel_myword = oci_parse($conn, $sql_sel_myword);
		oci_execute($stmt_sel_myword, OCI_DEFAULT);
		$err=oci_error($stmt_sel_myword);
		if($err){
			$err_message="Some unknown error occured: ".$err['message']."<br />";
			echo $err_message;
		}else{
			while ($word=oci_fetch_row($stmt_sel_myword)){
				$count_word++;
				$spell=$word[0];
				$degree=$word[1];
				if(isset($_POST["post_".$spell."_rate"])){
					$post_rate_value=$_POST["post_".$spell."_rate"];
					if($post_rate_value!=$degree){
						//Update the degree of the word if it is not same as original.
						if($post_rate_value==6){
							// Delete from the learn table. 
							$sql_del_word="DELETE FROM Learn WHERE spell='".$spell."'";
							$stmt_del_word = oci_parse($conn, $sql_del_word);
							oci_execute($stmt_del_word, OCI_DEFAULT);
							$err3=oci_error($stmt_del_word);
							if($err3){
								oci_rollback($conn);
								$err_message3="Some unknown error occured: ".$err3['message']."<br />";
								echo $err_message3;
							}else{
								oci_commit($conn);
							}
						}else{
							// Update rate. 
							$sql_upd_degree="UPDATE Learn SET degree=".$post_rate_value." WHERE spell='".$spell."' ";
							$stmt_upd_degree = oci_parse($conn, $sql_upd_degree);
							oci_execute($stmt_upd_degree, OCI_DEFAULT);
							$err2=oci_error($stmt_upd_degree);
							if($err2){
								oci_rollback($conn);
								$err_message2="Some unknown error occured: ".$err2['message']."<br />";
								echo $err_message2;
							}else{
								oci_commit($conn);
							}
						}
						
					}
					// Need refresh the page to show the change.
					Header("Location: my_wordlist.php");
				}
				echo "<td>".$count_word."</td><td>".$spell."</td>";
				// Create 5 level rating.
				for($rate_level=1;$rate_level<=6;$rate_level++){
					echo "<td><input type=\"radio\" name=\"post_".$spell."_rate\" value=\"".$rate_level."\" ";
					// Default
					if($rate_level==$degree){
						echo "checked /></td>";
					}else{
						echo "/></td>";
					}
				}
				echo "<td><input type=\"submit\" value=\"change\" /></td>";
				echo "</tr>";
			}
		}

		if($count_word==0){
			echo "<tr><td> </td></tr><tr><td>You haven't added any word to your list. </td></tr>";
		}

		oci_close($conn);
		?>
	</table>
</form>
</body>
</html>
