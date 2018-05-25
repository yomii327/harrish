<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

$output = array('status'=>false, 'message'=>'');
if(isset($_REQUEST['antiqueID'])){	
	$percentage = $_REQUEST['percentage'];
	$progress_id = $_REQUEST['progress_id'];
	$status = $_REQUEST['status'];
	
	#Update percentage data into progress_monitoring table.
	$query = 'UPDATE progress_monitoring set percentage="'. $percentage .'%", status="'. $status .'", last_modified_by="'. $_SESSION['ww_builder_id'] .'", last_modified_date=NOW(), original_modified_date=NOW() WHERE progress_id="'.$progress_id.'"';
	$result = mysql_query($query) or die (mysql_errno());
	if($result){
		$output = array('status'=>true, 'message'=>'Task percentage updated successfully.');
	} else {
		$output = array('status'=>false, 'message'=>$result);
	}
}
echo json_encode($output);
/* Omit PHP closing tag to help avoid accidental output. */
