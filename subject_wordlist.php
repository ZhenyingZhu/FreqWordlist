<!DOCTYPE table PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
ini_set('display_errors', 'On');
session_start();
// If not chose subject but has already log in, jump to log in page will go to my list. 
if(isset($_SESSION["session_user_id"]) && isset($_SESSION["session_chose_sub"])){
	$session_user_id=$_SESSION["session_user_id"];
	$session_chose_sub=$_SESSION["session_chose_sub"];
}else{
	$login_url="user_log_in.php";
	header("Location: ".$login_url);
}

require_once 'connection.php';
// Chose add all words to list. 
if(isset($_POST["post_all"]) && $_POST["post_all"]=="yes"){
	//Insert all words into learn
	$sql_ins_allword="INSERT INTO Learn (userid, spell, degree) 
	SELECT ".$session_user_id.", n.spell, 3
	FROM NotionalWords_relate n, Words w
	WHERE n.sname='".$session_chose_sub."' AND n.spell=w.spell 
	AND n.spell NOT IN (SELECT l.spell FROM Learn l WHERE l.userid=".$session_user_id.") 
	ORDER BY (n.weight+5*w.searchTime) DESC";
	$stmt_ins_allword = oci_parse($conn, $sql_ins_allword);
	oci_execute($stmt_ins_allword, OCI_DEFAULT);
	$err=oci_error($stmt_ins_allword);
	if($err){
		oci_rollback($conn);
		$err_message="Some unknown error occured: ".$err['message']."<br />";
	}else{
		oci_commit($conn);
	}
}
if(isset($_POST["post_single_word"])){
	$post_single_word=$_POST["post_single_word"];
	// An array of single words that chose by user
	if(!empty($post_single_word)){
		$N=count($post_single_word);
		for ($i=0; $i<$N; $i++){
			$sql_sel_exist="SELECT * FROM Learn WHERE spell='".$post_single_word[$i]."' AND userid=".$session_user_id;
			$stmt_sel_exist = oci_parse($conn, $sql_sel_exist);
			oci_execute($stmt_sel_exist, OCI_DEFAULT);
			if(!oci_fetch_row($stmt_sel_exist)){
				// Add into Learn
				$sql_ins_single="INSERT INTO Learn (userid, spell, degree) 
					VALUES (".$session_user_id.", '".$post_single_word[$i]."', 5)";
				$stmt_ins_single = oci_parse($conn, $sql_ins_single);
				oci_execute($stmt_ins_single, OCI_DEFAULT);
				$err2=oci_error($stmt_ins_single);
				if($err2){
					oci_rollback($conn);
					$err_message2="Some unknown error occured: ".$err2['message']."<br />";
				}else{
					oci_commit($conn);
				}
			}
		}
	}
}
?>
<html>
<head>
<title>Subject List</title>
</head>
<body>
<form action="<?php $_SERVER['PHP_SELF'];?>" method="post">
	<!-- A table to show specific word lists -->
	<table border="1">
	<tr><th colspan="3">Add the chose word to my list: <input type="submit" value="add" /></th></tr>
		<?php 
		if(isset($err_message)){
			echo $err_message;
		}
		if(isset($err_message2)){
			echo $err_message2;
		}
		echo "<tr><td>all</td>";
		echo "<td><input type=\"checkbox\" name=\"post_all\" value=\"yes\" /></td>";
		echo "<td>You chose ".$session_chose_sub.". Here is the word list. </td><tr>";
		// Select word in decrease order of weight
		$sql_sel_wordlist="SELECT n.spell FROM NotionalWords_relate n, Words w 
			WHERE n.sname='".$session_chose_sub."' AND n.spell=w.spell AND n.spell NOT IN
				(SELECT l.spell FROM Learn l WHERE l.userid=".$session_user_id.") 
			ORDER BY (n.weight+5*w.searchTime) DESC";
		$stmt_sel_wordlist = oci_parse($conn, $sql_sel_wordlist);
		oci_execute($stmt_sel_wordlist, OCI_DEFAULT);
		$err3=oci_error($stmt_sel_wordlist);
		if($err3){
			$err_message3="Some unknown error occured: ".$err['message']."<br />";
			echo $err_message3;
		}else{
			$count_word=0;
			while ($word=oci_fetch_row($stmt_sel_wordlist))
			{
				$count_word++;
				$spell=$word[0];
				echo "<tr><td>".$count_word."</td>
					<td><input type=\"checkbox\" name=\"post_single_word[]\" value=\"".$spell."\" /></td><td>".$spell."</td></tr>";
			}
			if($count_word==0){
				echo "<tr><td> </td></tr><tr><td>Sorry, there is no new words under this subject right now. </td></tr>";
			}else{
				echo "<tr><th colspan=\"3\">Add the chose word to my list: <input type=\"submit\" value=\"add\" /></th></tr>";
			}
		}
		
		oci_close($conn);
		?>
	</table>
</form>
</body>
</html>
