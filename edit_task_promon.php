<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_GET['uniqueId'])){
	$taskData = $obj->getRecordByQuery('SELECT
		pm.progress_id AS progressID,
		pml.location_title AS location,
		pm.sub_location_id AS sublocation,
		pm.task AS task,
		pm.start_date AS startDate,
		pm.end_date AS endDate,
		pm.holding_point AS holdingPoint,
		group_concat(pmi.issued_to_name) AS issueTo
	FROM
		progress_monitoring AS pm
	LEFT JOIN
		issued_to_for_progress_monitoring as pmi on pm.progress_id = pmi.progress_id AND pmi.is_deleted = 0
	INNER JOIN
		project_monitoring_locations AS pml ON pm.location_id = pml.location_id
	WHERE
		pml.is_deleted = 0 AND pm.is_deleted = 0 AND pm.project_id = '.$_SESSION['idp'].' AND pm.progress_id = '.$_GET['progress_id'].' GROUP BY pm.progress_id');
	if(!empty($taskData)){?>
		<fieldset class="roundCorner">
			<legend style="color:#000000;">Edit Task</legend>
			<form action="" name="editTaskForm" id="editTaskForm">
			<table border="0" width="100%" class="simpleTable">
				<tr>
					<td width="30%">Task <span class="req">*</span></td>
					<td width="70%" align="left">
						<input type="text" name="task" id="task" value="<?=$taskData[0]['task']?>" />
						<div class="error-edit-profile-red" style="width:100px;display:none;"  id="taskError">Task require field</div>
					</td>
				</tr>
				<tr>
					<td>Location</td>
					<td align="left"><?=$taskData[0]['location']?></td>
				</tr>
				<tr>
					<td>Sub Location</td>
					<td align="left"><?=$obj->subLocationsProgressMonitoring_update($taskData[0]['sublocation'], ' > ')?></td>
				</tr>
				<tr>
					<td>Start Date <span class="req">*</span></td>
					<td align="left">
						<input type="text" name="startDate" id="startDate" value="<?=date('d/m/Y', strtotime($taskData[0]['startDate']))?>" readonly />
						<div class="error-edit-profile-red" style="width:100px;display:none;"  id="taskError">Task require field</div>
					</td>
				</tr>
				<tr>
					<td>End Date <span class="req">*</span></td>
					<td align="left">
						<input type="text" name="endDate" id="endDate" value="<?=date('d/m/Y', strtotime($taskData[0]['endDate']))?>"  readonly="readonly"  />
						<div class="error-edit-profile-red" style="width:100px;display:none;"  id="taskError">Task require field</div>
					</td>
				</tr>
				<tr>
					<td>Issued To</td>
					<td align="left"><input type="text" name="issueTo" id="issueTo" value="<?=$taskData[0]['issueTo']?>" /></td>
				</tr>
				<tr>
					<td>Hold Point</td>
					<td align="left">
						<select name="holdingPoint" id="holdingPoint">
							<option <?php if($taskData[0]['holdingPoint'] == 'Yes'){echo 'selected="selected"';}?> value="Yes">Yes</option>
							<option <?php if($taskData[0]['holdingPoint'] == 'No'){echo 'selected="selected"';}?> value="No">No</option>
						</select>
					</td>
				</tr>	
				<tr>
					<td colspan="3">
						<input type="hidden" name="progressID" id="progressID" value="<?=$taskData[0]['progressID']?>" />
						<input type="hidden" name="subLocationID" id="subLocationID" value="<?=$taskData[0]['subLocationID']?>" />
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center"><input type="button" onclick="editTaskSubmit();" id="submitEditTask" value="submit"  /></td>
				</tr>
			</table>
			</form>
		</fieldset>	
<?php }
}

if(isset($_GET['antiqueID'])){
#print_r($_POST);die;
	$taskData = array();
	$taskData = $obj->selQRYMultiple('task', 'progress_monitoring', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0 AND sub_location_id = '.$_POST['subLocationID']);
	$taskCheckArr = array();
	foreach($taskData as $tData){
		$taskCheckArr[] = $tData['task'];
	}
	if(!in_array($_POST['task'], $taskCheckArr)){
		$insQRY = "UPDATE progress_monitoring SET
						task = '".trim(addslashes($_POST['task']))."',
						start_date = '".$obj->dateChanger('/', '-', $_POST['startDate'])."',
						end_date = '".$obj->dateChanger('/', '-', $_POST['endDate'])."',
						last_modified_by = ".$_SESSION['ww_builder']['user_id'].",
						last_modified_date = NOW(),
						original_modified_date = NOW(),
						holding_point = '".$_POST['holdingPoint']."'
					WHERE
						progress_id = ".$_POST['progressID'];
		mysql_query($insQRY);
	
		$update_issue = "UPDATE issued_to_for_progress_monitoring SET is_deleted = 1, last_modified_date = NOW() WHERE progress_id = ".$_POST['progressID']."";
		mysql_query($update_issue);
	
		$issueto = $_POST['issueTo'];	
		if($issueto != ''){
			$issueToArr = array_map('trim', explode(",", $issueto));
			for($i=0;$i<count($issueToArr);$i++){
				$issuedToInsert = "INSERT INTO issued_to_for_progress_monitoring SET
										project_id = ".$_SESSION['idp'].",
										progress_id = '".$_POST['progressID']."',
										issued_to_name = '".addslashes(trim($issueToArr[$i]))."',
										last_modified_date = NOW(),
										last_modified_by = ".$_SESSION['ww_builder']['user_id'].",
										created_date = NOW(),
										created_by = ".$_SESSION['ww_builder']['user_id'];
				mysql_query($issuedToInsert); 
				
				$select_isseu = "SELECT * FROM inspection_issue_to WHERE issue_to_name = '".$issueToArr[$i]."' AND project_id = ".$_SESSION['idp']." AND is_deleted = 0";
				$result_issue = mysql_query($select_isseu);
				$issue_row = mysql_num_rows($result_issue);
				if($issue_row == 0){
					$issue_insert = "INSERT INTO inspection_issue_to SET
										issue_to_name = '".addslashes(trim($issueToArr[$i]))."',
										last_modified_date = NOW(),
										last_modified_by = ".$_SESSION['ww_builder']['user_id'].",
										created_date = NOW(),
										created_by = ".$_SESSION['ww_builder']['user_id'].",
										project_id = ".$_SESSION['idp'];
					mysql_query($issue_insert);
				}		 
			}
		}
		$data = array('progress_id' => $_POST['progressID'], 'progress_task' => $_POST['task']);
		echo json_encode($data);
	}else{
		echo 'Duplicate task';
	}
	
}
?>