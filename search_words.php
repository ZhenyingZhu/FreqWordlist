<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php 
ini_set('display_errors','On');
require_once 'connection.php';
?>
<html>
<head>
<title>Search Words</title>
</head>
<body>
	Notice: All the explanation come from 
	<a href="http://dictionary.reference.com/" target="_blank">dictionary.reference.com</a><br />
	<!-- Search box -->
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
		If you want to search some words, please type here: <br />
		<input type="text" name="post_word" />
		<input type="submit" value="search" />
	</form>
	
	<!-- Show result -->
	<table border="1">
	<?php 
	if(isset($_POST["post_word"])){
		$post_word=$_POST["post_word"];
		echo "<tr>".$post_word.":<tr>";
		// Select explanation from database. 
		$sql_sel_word="SELECT partOfSpeech, meanings FROM Definition_canMean WHERE spell='".$post_word."'";
		$stmt_sel_word = oci_parse($conn, $sql_sel_word);
		oci_execute($stmt_sel_word, OCI_DEFAULT);
		$err=oci_error($stmt_sel_word);
		if($err){
			$err_message="Some unknown error occured: ".$err['message']."<br />";
		}else{
			$count_meaning=0;
			while ($mean=oci_fetch_row($stmt_sel_word))
			{
				$count_meaning++;
				$partOfSpeech=$mean[0];
				$meanings=$mean[1];
				echo "<tr><td>".$partOfSpeech."</td><td>".$meanings."</td></tr>";
			}
			if($count_meaning==0){
				echo "<tr><td> </td></tr><tr><td>Sorry, This word is not contained in our database. </td></tr>";
			}else{
				//Select searth time from database.
				$sql_sel_searchTime="SELECT searchTime FROM Words WHERE spell='".$post_word."'";
				$stmt_sel_searchTime = oci_parse($conn, $sql_sel_searchTime);
				oci_execute($stmt_sel_searchTime, OCI_DEFAULT);
				$err2=oci_error($stmt_sel_searchTime);
				if($err2){
					$err_message2="Some unknown error occured: ".$err2['message']."<br />";
					echo $err_message2;
				}else{
					$time_arr=oci_fetch_row($stmt_sel_searchTime);
					$searchTime=$time_arr[0]+1;
				}

				// Update search time to database.
				$sql_upd_word="UPDATE Words SET searchTime=".$searchTime." WHERE spell='".$post_word."'";
				$stmt_upd_word = oci_parse($conn, $sql_upd_word);
				oci_execute($stmt_upd_word, OCI_DEFAULT);
				$err3=oci_error($stmt_upd_word);
				if($err3){
					oci_rollback($conn);
					$err_message3="Some unknown error occured: ".$err3['message']."<br />";
					echo $err_message3;
				}else{
					oci_commit($conn);
				}
				
			}
		}
		if(isset($err_message)){
			echo $err_message;
		}

	}
	oci_close($conn);		
	?>
	</table>
	
</body>
</html>
