<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_GET['uniqueId'])){
	$taskData = $obj->getRecordByQuery('SELECT
			pm.task_id AS progressID,
			pml.location_title AS location,
			pm.sub_location_id AS sublocation,
			pm.task AS task,
			pm.status AS status,
			pm.comments AS comments
		FROM
			qa_task_locations AS pml 
		INNER JOIN
			qa_task_monitoring as pm ON pm.location_id = pml.location_id
		WHERE
			pml.is_deleted = 0 AND pm.is_deleted = 0 AND pm.project_id = '.$_SESSION['idp'].' AND pm.task_id = '.$_GET['task_id'].'
		GROUP BY pm.task_id');
	if(!empty($taskData)){?>
		<fieldset class="roundCorner">
			<legend style="color:#000000;">Edit Task</legend>
				<form action="" name="editTaskForm" id="editTaskForm">
					<table border="0" width="100%" class="simpleTable">
						<tr>
							<td width="60%">Task <span class="req">*</span></td>
							<td width="40%" align="left">
								<textarea name="task" id="task"><?=$taskData[0]['task']?></textarea>
								<div class="error-edit-profile-red" style="width:100px;display:none;"  id="taskError">Task require field</div>
							</td>
						</tr>
						<tr>
							<td>Location</td>
							<td align="left"><?=$taskData[0]['location']?></td>
						</tr>
						<tr>
							<td>Sub Location</td>
							<td align="left"><?=$obj->QAsubLocationsProgressMonitoring($taskData[0]['sublocation'], ' > ')?></td>
						</tr>
						<tr>
							<td>Status</td>
							<td align="left">
								<select name="status" id="status">
									<option <?php if($taskData[0]['status'] == 'Yes'){echo 'selected="selected"';}?> value="Yes">Yes</option>
									<option <?php if($taskData[0]['status'] == 'No'){echo 'selected="selected"';}?> value="No">No</option>
									<option <?php if($taskData[0]['status'] == 'NA'){echo 'selected="selected"';}?> value="NA">NA</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>Comments</td>
							<td align="left"><textarea name="comments" id="comments"><?=$taskData[0]['comments']?></textarea></td>
						</tr>	
						<tr>
							<td colspan="3">
								<input type="hidden" name="progressID" id="progressID" value="<?=$taskData[0]['progressID']?>" />
								<input type="hidden" name="subLocationID" id="subLocationID" value="<?=$taskData[0]['sublocation']?>" />
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
	$taskData = array();
	$taskData = $obj->selQRYMultiple('task', 'qa_task_monitoring', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0 AND sub_location_id = '.$_POST['subLocationID']);
	$taskCheckArr = array();
	foreach($taskData as $tData){
		$taskCheckArr[] = $tData['task'];
	}
	$taskUpdate = trim(addslashes($_POST['task']));
	if(!in_array($taskUpdate, $taskCheckArr)){
		$insQRY = "UPDATE qa_task_monitoring SET
						task = '".$taskUpdate."',
						last_modified_by = ".$_SESSION['ww_builder_id'].",
						last_modified_date = NOW(),
						status = '".$_POST['status']."',
						comments = '".trim(addslashes($_POST['comments']))."'
					WHERE
						task_id = '".$_POST['progressID']."'";
		mysql_query($insQRY);

		$data = array('task_id' => $_POST['progressID'], 'task' => $_POST['task']);
		echo json_encode($data);
	}else{
		echo 'Duplicate task';
	}
}
?>