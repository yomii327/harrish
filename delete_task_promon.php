<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_POST['antiqueID'])){
	$insQRY = "UPDATE progress_monitoring SET
					is_deleted = 1,
					last_modified_date = NOW(),
					last_modified_by = ".$_SESSION['ww_builder_id'].",
					original_modified_date = NOW()
				WHERE
					is_deleted = 0 AND
					progress_id = '".$_POST['progress_id']."' AND
					project_id = ".$_SESSION['idp'];
	mysql_query($insQRY);
	if(mysql_affected_rows() > 0){
		echo 1;
	}else{
		echo 0;
	}
}
?>