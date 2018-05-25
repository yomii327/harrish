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
		pm.project_id AS projectID,
		pm.start_date AS startDate,
		pm.end_date AS endDate,
		pm.holding_point AS holdingPoint,
		pm.status AS status,
		pm.percentage AS percentage,
		group_concat(pmi.issued_to_name) AS issueTo
	FROM
		progress_monitoring AS pm
	LEFT JOIN
		issued_to_for_progress_monitoring as pmi on pm.progress_id = pmi.progress_id AND pmi.is_deleted = 0
	INNER JOIN
		project_monitoring_locations AS pml ON pm.location_id = pml.location_id
	WHERE
		pml.is_deleted = 0 AND pm.is_deleted = 0 AND pm.progress_id = '.$_GET['progress_id'].' GROUP BY pm.progress_id');
	if(!empty($taskData)){?>
		<fieldset class="roundCorner">
			<legend style="color:#000000;">Edit Task</legend>
			<form action="" name="editTaskForm" id="editTaskForm">
			<table border="0" width="100%" class="simpleTable">
				<tr>
					<td width="30%">Task <span class="req">*</span></td>
					<td width="70%" align="left">
						<input type="text" id="task" value="<?=$taskData[0]['task']?>" name="task" />
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
						<input type="text" readonly="readonly" id="startDate" size="10" value="<?=date('d/m/Y', strtotime($taskData[0]['startDate']))?>" name="startDate" />
					</td>
				</tr>
				<tr>
					<td>End Date <span class="req">*</span></td>
					<td align="left">
						<input type="text" readonly="readonly" id="endDate" size="10" value="<?=date('d/m/Y', strtotime($taskData[0]['endDate']))?>" name="endDate" />
					</td>
				</tr>
				<tr>
					<td>Issued To</td>
					<td align="left"><?=$taskData[0]['issueTo']?></td>
				</tr>
				<tr>
					<td>Hold Point</td>
					<td align="left"><?=$taskData[0]['holdingPoint']?></td>
				</tr>	
				<tr>
					<td>% Completed</td>
					<td align="left">
						<select name="pechange" id="pechange" onchange="setStatus(this.value);">
							<option value="">Select</option>
							<option value="0" <?php if($taskData[0]['percentage'] == '0%'){echo 'selected="selected"';}?> >0 %</option>
							<option value="5" <?php if($taskData[0]['percentage'] == '5%'){echo 'selected="selected"';}?> >5 %</option>
							<option value="15" <?php if($taskData[0]['percentage'] == '15%'){echo 'selected="selected"';}?> >15 %</option>
							<option value="25" <?php if($taskData[0]['percentage'] == '25%'){echo 'selected="selected"';}?> >25 %</option>
							<option value="35" <?php if($taskData[0]['percentage'] == '35%'){echo 'selected="selected"';}?> >35 %</option>
							<option value="45" <?php if($taskData[0]['percentage'] == '45%'){echo 'selected="selected"';}?> >45 %</option>
							<option value="55" <?php if($taskData[0]['percentage'] == '55%'){echo 'selected="selected"';}?> >55 %</option>
							<option value="65" <?php if($taskData[0]['percentage'] == '65%'){echo 'selected="selected"';}?> >65 %</option>
							<option value="75" <?php if($taskData[0]['percentage'] == '75%'){echo 'selected="selected"';}?> >75 %</option>
							<option value="85" <?php if($taskData[0]['percentage'] == '85%'){echo 'selected="selected"';}?> >85 %</option>
							<option value="95" <?php if($taskData[0]['percentage'] == '95%'){echo 'selected="selected"';}?> >95 %</option>
							<option value="100" <?php if($taskData[0]['percentage'] == '100%'){echo 'selected="selected"';}?> >100 %</option>
						</select>
					</td>
				</tr>	
				<tr>
					<td>Status</td>
					<td align="left">
						<select  name="statusEdit" id="statusEdit" onchange="setPetchange(this.value);">
							<option value="">Select</option>
							<option value="NA" <?php if($taskData[0]['status'] == 'NA'){echo 'selected="selected"';}?> >NA</option>
							<option value="In progress" <?php if($taskData[0]['status'] == 'In progress'){echo 'selected="selected"';}?> >In progress</option>
							<option value="Behind" <?php if($taskData[0]['status'] == 'Behind'){echo 'selected="selected"';}?> >Behind</option>
							<option value="Complete" <?php if($taskData[0]['status'] == 'Complete'){echo 'selected="selected"';}?> >Complete</option>
							<option value="Signed off" <?php if($taskData[0]['status'] == 'Signed off'){echo 'selected="selected"';}?> >Signed off</option>
						</select>
					</td>
				</tr>	
				<tr>
					<td colspan="3">
						<input type="hidden" name="progressID" id="progressID" value="<?=$taskData[0]['progressID']?>" />
						<input type="hidden" name="projectID" id="projectID" value="<?=$taskData[0]['projectID']?>" />
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
	$pechange = '';
	if($_POST['pechange'] != "") $pechange = $_POST['pechange'].'%';
	$status = $_POST['statusEdit'];
	$task = addslashes(trim($_POST['task']));
	$startDate = implode('-', array_reverse(explode('/', $_POST['startDate'])));
	$endDate = implode('-', array_reverse(explode('/', $_POST['endDate'])));

	$startDateError = "";$endDateError = "";$errorFlag = false;
	if($_REQUEST['bypass'] != 'Y'){
		$leaveData = $obj->selQRYMultiple('date, leave_type', 'project_leave', 'is_deleted = 0 AND project_id = '.$_REQUEST['projectID']);
		$leaveDateArr = array();
		foreach($leaveData as $lData){
			$leaveDateArr[$lData['date']] = $lData['leave_type'];
		}
		
		//Check the Start Date and End date have Holidays or not Start Here
		if(array_key_exists($startDate.' 00:00:00', $leaveDateArr)){//Check for Start Date
			$startDateError = "Selected Start Date have ".$leaveDateArr[$startDate.' 00:00:00'];
			$errorFlag = true;
		}
		if(array_key_exists($endDate.' 00:00:00', $leaveDateArr)){//Check for End Date
			$endDateError = "Selected End Date have ".$leaveDateArr[$endDate.' 00:00:00'];
			$errorFlag = true;
		}
		if($errorFlag){
			$data = array('startDateError' => $startDateError, 'endDateError' => $endDateError, 'errorFlag' => $errorFlag);
			
			echo json_encode($data);die;
		}
		//Check the Start Date and End date have Holidays or not End Here
	}
	$progressID = $_POST['progressID'];
	switch($status){
		case "Complete" : 
		$row_color = "#00FF00";
		break;

		case "Behind" : 
		$row_color = "#FF0000";
		break;

		case "Signed off" : 
		$row_color = "#3399FF";
		break;

		case "In progress" : 
		$row_color = "#FFFF00";
		break;
		
		default :
		$row_color = "#CCCCCC";
	}

	$insertQRY = "INSERT INTO progress_monitoring_update SET
						progress_id = '".$progressID."',
						percentage = '".$pechange."',
						task = '".$task."',
						status = '".$status."',
						created_by = '".$_SESSION['ww_builder_id']."',
						created_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						last_modified_date = NOW(),
						project_id = '".$_SESSION['projIdPM']."'";
	mysql_query($insertQRY);
//Update Progress Monitoring
	
	$updateQRY = "UPDATE progress_monitoring SET
						status = '".$status."',
						task = '".$task."',
						percentage = '".$pechange."',
						start_date = '".$startDate."',
						end_date = '".$endDate."',
						last_modified_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						original_modified_date = NOW()
					WHERE
						progress_id = ".$progressID." AND
						project_id = ".$_SESSION['projIdPM'];
	mysql_query($updateQRY);

	$data = array('progress_id' => $progressID, 'row_color' => $row_color, 'task' => $task, 'startDate' => $startDate, 'endDate' => $endDate, 'status' => $status, 'percentage' => $pechange, 'startDateError' => $startDateError, 'endDateError' => $endDateError, 'errorFlag' => false);
	
	echo json_encode($data);
}
?>