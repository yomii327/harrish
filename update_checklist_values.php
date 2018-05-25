<?php
session_start();

include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
if($_POST['task'] == 'checkListForm'){
	if(isset($_POST['uniqueId'])){
		$projectId = $_POST['projectId'];
		$inspectionId = $_POST['inspectionId'];
		$checkListAddData = isset($_POST['checkListAddData']) ? $_POST['checkListAddData'] : '';
		$checkListUpdateData = isset($_POST['checkListUpdateData']) ? $_POST['checkListUpdateData'] : '';
		if(!empty($checkListAddData)){
			$checklist = explode(',', $checkListAddData);
			for($i=0; $i<sizeof($checklist); $i++){
				if($checklist[$i] != ''){
					$checkListData = explode('@@@@@@@@@', $checklist[$i]);
					$checkListData_data = explode('#########', $checkListData[1]);
					$insertQRY = "INSERT INTO inspection_check_list SET
									project_id = '".$projectId."',
									inspection_id = '".$inspectionId."',
									check_list_items_id = '".$checkListData[0]."',
									check_list_items_status = '".$checkListData_data[1]."',
									created_date = NOW(),
									created_by = '".$_SESSION['ww_builder_id']."',
									created_date = NOW(),
									created_by = '".$_SESSION['ww_builder_id']."'";
									
					mysql_query($insertQRY);
				}
			}
		}
		if(!empty($checkListUpdateData)){
			$checklist = explode(',', $checkListUpdateData);
			for($i=0; $i<sizeof($checklist); $i++){
				$checkListData = explode('@@@@@@@@@', $checklist[$i]);
				$checkListData_data = explode('#########', $checkListData[1]);
				$updateQRY = "UPDATE inspection_check_list SET
								check_list_items_status = '".$checkListData_data[1]."',
								last_modified_by = '".$_SESSION['ww_builder_id']."',
								last_modified_date = NOW()
							WHERE 
								project_id = '".$projectId."' AND 
								inspection_id = '".$inspectionId."' AND
								check_list_items_id = '".$checkListData[0]."'";
				mysql_query($updateQRY);
			}
		}
		echo 'Checklist Updated !';
	}
}else if($_POST['task'] == 'changeButton'){
	$checkListStatus = $obj->checklistStatus($_POST['projectId'], $_POST['inspectionId']);
	if($checkListStatus){
		$updateIssueto = "UPDATE issued_to_for_inspections SET inspection_status = 'Draft', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = '".$_POST['inspectionId']."' AND project_id = '".$_POST['projectId']."'";
		mysql_query($updateIssueto);
		echo '<img src="images/checklist_btn2_red.png" id="checklist" style="border:none; width:111px;" onclick="EditChecklist('.$_POST['projectId'].', '.$_POST['inspectionId'].');" />';
	}else{
		$updateIssueto = "UPDATE issued_to_for_inspections SET inspection_status = 'Open', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = '".$_POST['inspectionId']."' AND project_id = '".$_POST['projectId']."'";
		mysql_query($updateIssueto);
		echo '<img src="images/checklist_btn2.png" id="checklist" style="border:none; width:111px;" onclick="EditChecklist('.$_POST['projectId'].', '.$_POST['inspectionId'].');" />';
	}
}else if($_POST['task'] == 'addCheckListData'){
	if(isset($_POST['uniqueId'])){
		$checkListAddData = isset($_POST['checkListAddData']) ? $_POST['checkListAddData'] : '';
		$checkListUpdateData = isset($_POST['checkListUpdateData']) ? $_POST['checkListUpdateData'] : '';
		if(!empty($checkListAddData)){
			$checklist = explode(',', $checkListAddData);
			$checkBoxVal = array();
			for($i=0; $i<sizeof($checklist); $i++){
				$checkListData = explode('@@@@@@@@@', $checklist[$i]);
				$_SESSION['checkList'][$checkListData[0]] = $checkListData[1];
			}
		}
		if(in_array('NA', $_SESSION['checkList'])){
			echo '<img src="images/checklist_btn2_red.png" id="checklist" style="border:none; width:111px;"  onclick="addChecklist('.$_SESSION['idp'].')" />';
		}else{
			echo '<img src="images/checklist_btn2.png" id="checklist" style="border:none; width:111px;"  onclick="addChecklist('.$_SESSION['idp'].')" />';
		}
	}
}
?>