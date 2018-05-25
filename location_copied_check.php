<?php
session_start();
set_time_limit(3000);
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_REQUEST['uniqueId'])){
	$copyLocation = $_REQUEST['copyLocation'];
	$_SESSION['pasteCount'] = 1;//Set Copy Session
	$res_test = mysql_query("select location_id from project_locations where project_id = '".$_SESSION['idp']."' and location_parent_id = '".$copyLocation."' and is_deleted = 0");
	if(mysql_num_rows($res_test) > 0){
		echo 'Success';
	}else{
		echo 'Error';
	}
}
?>