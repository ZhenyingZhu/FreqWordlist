<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
ini_set('display_errors', 'On');
require_once 'connection.php';
// Have already chosen a subject, show it as default. 
if(isset($_POST["post_sel_sub"])){
	$post_sel_sub=$_POST["post_sel_sub"];
}
?>
<html>
<head>
<title>Article List</title>
</head>
<body>
<a href="welcome.php" target="_top">Home</a>

<br />Notice: All of the articles are gathered from internet. Copyright belong to their authors.<br />  
<!-- A form using drop down list to choose articles. -->
<form name="list" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	<fieldset>
		<select name="post_sel_sub">
			<?php 
			// SQL: Select subject list from Database. 
			$sql_sel_sub="SELECT sname FROM Subjects";
			$stmt_sel_sub = oci_parse($conn, $sql_sel_sub);
			oci_execute($stmt_sel_sub, OCI_DEFAULT);
			$err=oci_error($stmt_sel_sub);
			if($err){
				$err_message="Some unknown error occured: ".$err['message']."<br />";
			}else{
				while ($sub=oci_fetch_row($stmt_sel_sub))
				{
					$subject_name=$sub[0];
					echo "<option value=\"".$subject_name."\"";
					//Make default choice
					if($subject_name==$post_sel_sub){
						echo " selected>".$subject_name."</option>";
					}else{
						echo ">".$subject_name."</option>";
					}
				}
			}
			// Show error message. 
			if(isset($err_message)){
				echo $err_message;
			}
			?>
		</select> <input type="submit" value="submit" />
	</fieldset>
</form>

<!-- A table show all the articles under selected subject -->
<table>
<?php 
if(isset($post_sel_sub)){
	echo "<tr><td>You chose ".$post_sel_sub.".</td></tr> <tr><td>Here is the article list: </td></tr>";
	// SQL: Select article names from Database. 
	$sql_sel_art="SELECT aname,aid FROM Articles_BelongTo WHERE sname='".$post_sel_sub."'";
	$stmt_sel_art = oci_parse($conn, $sql_sel_art);
	oci_execute($stmt_sel_art, OCI_DEFAULT);
	$err2=oci_error($stmt_sel_art);
	if($err2){
		$err_message2="Some unknown error occured: ".$err2['message']."<br />";
	}else{	
		$count_art=0;
		while ($art=oci_fetch_row($stmt_sel_art)){
			$count_art++;
			$art_name=$art[0];
			$art_id=$art[1];
			// Articles are saved under thier id.html. 
			echo "<tr><td><a href=source/articles/".$art_id.".html target=articleFrame>".$art_name."</a></td></tr>";
		}
		if($count_art==0){
			echo "<tr></tr><tr><td>Sorry, there is no articles under this subject right now. </td></tr>";
		}
	}
	if(isset($err_message2)){
		echo $err_message2;
	}
}
oci_close($conn);
?>
</table>
</body>
</html>
