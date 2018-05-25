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
		$res = mysql_query("SELECT location_id FROM project_locations WHERE project_id = '".$_SESSION['idp']."' and location_parent_id = ".$curr." and is_deleted = 0");
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

$all_categories = get_cats($_REQUEST['location_id']);

if(isset($_REQUEST['confirm'])){
	if(!empty($all_categories)){
		$deleteCount = 0;
		foreach($all_categories as $deleteId){
			$query = "UPDATE project_locations SET is_deleted = '1' WHERE project_id = '".$_SESSION['idp']."' and location_id = '".$deleteId."'";
			$res = mysql_query($query) or die(mysql_error());
			if(mysql_affected_rows()>0){
				$deleteCount++;
			}else{}
		}
		if($deleteCount > 0){
			if(!empty($all_categories)){
				$categoryList = '';
				foreach($all_categories as $catId){
					if($categoryList == ''){
						$categoryList .= $catId;
					}else{
						$categoryList .= ', '.$catId;
					}
				}
				$locIDs = $obj->selQRYMultiple('GROUP_CONCAT(inspection_id) AS inspectionids', 'project_inspections', 'project_id = "'.$_SESSION['idp'].'" AND location_id IN ('.$categoryList.') AND is_deleted = 0');
				
				$deleteQry = "UPDATE project_inspections SET is_deleted = '1', original_modified_date = NOW() WHERE project_id = '".$_SESSION['idp']."' AND inspection_id IN (".$locIDs[0]['inspectionids'].")";
				mysql_query($deleteQry);
			
				$deleteIssueQry = "UPDATE issued_to_for_inspections SET is_deleted = '1', original_modified_date = NOW() WHERE project_id = '".$_SESSION['idp']."' AND inspection_id IN (".$locIDs[0]['inspectionids'].")";
				mysql_query($deleteIssueQry);
			
				$deleteGraphicQry = "UPDATE inspection_graphics SET is_deleted = '1', original_modified_date = NOW() WHERE project_id = '".$_SESSION['idp']."' AND inspection_id IN (".$locIDs[0]['inspectionids'].")";
				mysql_query($deleteGraphicQry);
			}
			echo 'Location Deleted Successfully !';	
		}else{
			echo 'Location Not Deleted !';
		}
	}
}else{
	if(!empty($all_categories)){
		$categoryList = '';
		foreach($all_categories as $catId){
			if($categoryList == ''){
				$categoryList .= $catId;
			}else{
				$categoryList .= ', '.$catId;
			}
		}
		$checkInsQry = "SELECT inspection_id FROM project_inspections WHERE project_id = '".$_SESSION['idp']."' and location_id IN (".$categoryList.")";	
		$checkInsRes = mysql_query($checkInsQry) or die(mysql_error());
		if(mysql_num_rows($checkInsRes)>0){
			echo 'Inspection Exist';
		}else{
			$deleteCount = 0;
			foreach($all_categories as $deleteId){
				$query = "UPDATE project_locations SET is_deleted = '1' WHERE project_id = '".$_SESSION['idp']."' and location_id = '".$deleteId."'";
				$res = mysql_query($query) or die(mysql_error());
				if(mysql_affected_rows()>0){
					$deleteCount++;
				}else{}
			}
			if($deleteCount > 0){
				echo 'Location Deleted Successfully !';	
			}else{
				echo 'Location Not Deleted !';
			}
		}
	}
}
//$q="UPDATE project_locations SET is_deleted = '1' WHERE location_id = '".$_GET['location_id']."'";
//$res = mysql_query($q);
//echo 'Location Deleted Successfully !';


?>