<?php session_start();
set_time_limit(3000);
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

function get_cats($cat){
	$i=1;
	$remain = array();
	$all = array();
	$insertArray = array();
	$parentArray = array();
	
	$remain[0] = $cat;
	$all[0] = $cat;	
	while(sizeof($remain)>0){
		$curr = $remain[0];
		$res = mysql_query("SELECT location_id FROM qa_task_locations WHERE project_id = ".$_SESSION['idp']." and location_parent_id = ".$curr." and is_deleted = 0");
		while($row = mysql_fetch_array($res)){
			$all[$i++]=$row['location_id'];
			$remain[sizeof($remain)]=$row['location_id'];
		}
		unset($remain[0]);
		$remain=array_values($remain);
	}
	return $all;
}
 
$all_categories = array();

//$all_categories = get_cats();

if(isset($_REQUEST['confirm'])){
	if($_REQUEST['location_id']){
		$deleteCount = 0;
		$tmpArr = $obj->QASubLocationIds($_REQUEST['location_id']);
		if (!empty($tmpArr))
			$locationList = $deleteId . ", " . join(", ", $tmpArr);
		else
			$locationList = $deleteId;
			
		$query = "UPDATE qa_task_locations SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE project_id = ".$_SESSION['idp']." and location_id in (".$locationList.")";
		$res = mysql_query($query) or die(mysql_error());
		if(mysql_affected_rows()>0){
			$deleteCount++;
			$queryPM = "UPDATE qa_task_monitoring SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE project_id = ".$_SESSION['idp']." and sub_location_id in (".$locationList.")";
			$res = mysql_query($queryPM) or die(mysql_error());

		}else{}
		if($deleteCount > 0){
			echo 'Location Deleted Successfully !';	
		}else{
			echo 'Location Not Deleted !';
		}
	}
}
?>