<?php session_start();

include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
if(isset($_REQUEST['uniqueId'])){
	$inspectionIDs = $_REQUEST['inspectionIDs'];
	$projectID = $_REQUEST['projectID'];
	//$issueIds = $obj->selQRYMultiple('issued_to_inspections_id, project_id, inspection_id', 'issued_to_for_inspections', 'inspection_id IN('.$inspectionIDs.') AND project_id = '.$projectID.' AND is_deleted =0');
		$issueIds = $obj->selQRYMultiple('issued_to_inspections_id, project_id, inspection_id', 'issued_to_for_inspections', 'inspection_id IN('.$inspectionIDs.') AND project_id IN('.$projectID.') AND is_deleted =0');

	$issueIDS = '';
	foreach($issueIds as $issueid){
		if($issueIDS == ''){
			$issueIDS = $issueid['issued_to_inspections_id'];
		}else{
			$issueIDS .= ', '.$issueid['issued_to_inspections_id'];
		}
	}
	//$updateQRY = "UPDATE issued_to_for_inspections SET closed_date = NOW(), inspection_status = 'Closed', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE issued_to_inspections_id IN (".$issueIDS.") AND project_id = ".$projectID;
	
		$updateQRY = "UPDATE issued_to_for_inspections SET closed_date = NOW(), inspection_status = 'Closed', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE issued_to_inspections_id IN (".$issueIDS.") AND project_id IN(".$projectID.")";


	mysql_query($updateQRY);
	foreach($issueIds as $issueid){
		$imageName = $projectID.'_sign_'.$issueid['inspection_id'].'.png';
		copy('./images/master_signoff.png', './inspections/signoff/'.$imageName);
		
		//$secUpdateQRY = "UPDATE project_inspections SET inspection_sign_image = '".$imageName."', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = ".$issueid['inspection_id']." AND project_id = ".$projectID;
		$secUpdateQRY = "UPDATE project_inspections SET inspection_sign_image = '".$imageName."', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = ".$issueid['inspection_id']." AND project_id IN(".$projectID.")";
		
		mysql_query($secUpdateQRY);
	}
	
	$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Added Successfully !');
	echo json_encode($outputArr);
}

if(isset($_POST['strangeID'])){
	$taskIDs = $_REQUEST['taskIDs'];
	$projectID = $_REQUEST['projectID'];
	$updateQRY = "UPDATE qa_task_monitoring SET status = 'Yes', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE task_id IN (".$taskIDs.") AND project_id = ".$projectID;
	mysql_query($updateQRY);
	$taskArray = explode(',', $taskIDs);
	for($i=0; $i<sizeof($taskArray); $i++){
		$imageName = $projectID.'_qa_sign_'.$taskArray[$i].'.png';
		copy('./images/master_signoff.png', './inspections/signoff/'.$imageName);
		$secUpdateQRY = "UPDATE qa_task_monitoring SET signoff_image = '".$imageName."', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE task_id = ".$taskArray[$i]." AND project_id = ".$projectID;
		mysql_query($secUpdateQRY);
		$insertQRY = "INSERT INTO qa_task_monitoring_update SET
							task_id = '".$taskArray[$i]."',
							status = 'Yes',
							created_by = '".$_SESSION['ww_builder_id']."',
							created_date = NOW(),
							last_modified_by = '".$_SESSION['ww_builder_id']."',
							last_modified_date = NOW(),
							project_id = '".$projectID."'";
		mysql_query($insertQRY);
	}
	echo 'Done';	
}

if(isset($_POST['antiqueID'])){
	$taskIDs = $_REQUEST['taskIDs'];
	$projectID = $_REQUEST['projectID'];
	$setStatus = $_REQUEST['setStatus'];
	$taskArray = explode(',', $taskIDs);
	$statusData = $obj->selQRYMultiple('progress_id, status, percentage', 'progress_monitoring', 'progress_id IN ('.$taskIDs.') AND project_id = '.$projectID);
	$sttArray = array();
	$perArray = array();
	foreach($statusData as $sData){
		$sttArray[$sData['progress_id']]= $sData['status'];
		$perArray[$sData['progress_id']]= $sData['percentage'];
	}

	for($i=0; $i<sizeof($taskArray); $i++){
		if($setStatus == 'In progress'){
			if($perArray[$taskArray[$i]] == '0%'){
				$Percentage = '25%';
			}else{
				$Percentage = $perArray[$taskArray[$i]];
				if($perArray[$taskArray[$i]] == ''){
					$Percentage = '25%';
				}
			}
			$Status = $setStatus;
		}
		if($setStatus == 'Behind'){
			if($perArray[$taskArray[$i]] == '95%' || $perArray[$taskArray[$i]] == '100%'){
				$Percentage = '25%';
			}else{
				$Percentage = $perArray[$taskArray[$i]];
				if($perArray[$taskArray[$i]] == ''){
					$Percentage = '0%';
				}
			}
			$Status = $setStatus;
		}
		if($setStatus == 'Complete'){
			$Percentage = '95%';
			$Status = $setStatus;
		}
		if($setStatus == 'Signed off'){
			$Percentage = '100%';
			$Status = $setStatus;
		}
		if($setStatus == 'NA'){
			$Percentage = '';
			$Status = $setStatus;
		}
//Kept Record in updated
		$insertQRY = "INSERT INTO progress_monitoring_update SET
							progress_id = '".$taskArray[$i]."',
							percentage = '".$Percentage."',
							status = '".$Status."',
							created_by = '".$_SESSION['ww_builder_id']."',
							created_date = NOW(),
							last_modified_by = '".$_SESSION['ww_builder_id']."',
							last_modified_date = NOW(),
							project_id = '".$projectID."'";
		mysql_query($insertQRY);
//Update Progress Monitoring
		$updateQRY = "UPDATE progress_monitoring SET status = '".$Status."', percentage = '".$Percentage."', last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE progress_id = ".$taskArray[$i]." AND project_id = ".$projectID;
		mysql_query($updateQRY);
	}
	echo 'Done';
}
?>
