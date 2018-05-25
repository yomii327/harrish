<?php
session_start();
error_reporting(0);

include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_GET['uniqueId'])){
	$subLocationId = $_GET['sub_location_id'];
	$locNameTree = $obj->qa_sublocationParent($subLocationId, ' > ');
	$locIdTree = $obj->qa_sublocationParentID($subLocationId, ' > ');
	$locationId = array_shift(array_values(explode(' > ', $locIdTree)));?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Add Task</legend>
			<form action="" name="addTaskForm" id="addTaskForm">
			<table border="0" width="100%" class="simpleTable">
			<tr>
				<td width="60%">Task Location</td>
				<td width="40%" align="left"><?=$locNameTree?></td>
			</tr>
			<tr>
				<td width="60%">Task</td>
				<td width="40%" align="left">
					<textarea name="task" id="task"></textarea>
					<div class="error-edit-profile-red" style="width:100px;display:none;"  id="taskError">Task require field</div>
				</td>
			</tr>
			<tr>
				<td>Status</td>
				<td align="left">
					<select name="status" id="status">
						<option value="Yes">Yes</option>
						<option value="No">No</option>
						<option value="NA" selected="selected">NA</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Comments</td>
				<td align="left"><textarea name="comments" id="comments"></textarea></td>
			</tr>	
			<tr>
				<td colspan="3">
					<input type="hidden" name="locationTree" id="locationTree" value="<?php echo $locNameTree;?>" />
					<input type="hidden" name="locationid" id="locationid" value="<?php echo $locationId;?>" />
					<input type="hidden" name="sublocationid" id="sublocationid" value="<?php echo $subLocationId;?>" />
					<input type="hidden" name="locationTreeID" id="locationTreeID" value="<?php echo $locIdTree;?>" />
				</td>
			</tr>
			<tr>
				<td colspan="3" align="center">
					<input type="button" name="submit" id="submit" value="Submit" onclick="addTaskSubmit()"  />
				</td>
			</tr>
		</table>
			</form>
		</fieldset>	
<?php
}
if(isset($_GET['antiqueID'])){
	$taskData = $obj->selQRYMultiple('task', 'qa_task_monitoring', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0 AND sub_location_id = '.$_POST['sublocationid']);
	$taskCheckArr = array();
	foreach($taskData as $tData){
		$taskCheckArr[] = $tData['task'];
	}
	$taskAdded = trim(addslashes($_POST['task']));
	if(!in_array($taskAdded, $taskCheckArr)){
		$insQRY = "INSERT INTO qa_task_monitoring SET
						project_id = ".$_SESSION['idp'].",
						location_id = ".$_POST['locationid'].",
						sub_location_id = ".$_POST['sublocationid'].",
						task = '".$taskAdded."',
						status = '".$_POST['status']."',
						comments = '".trim(addslashes($_POST['comments']))."',
						created_by = ".$_SESSION['ww_builder_id'].",
						created_date = NOW(),
						last_modified_by = ".$_SESSION['ww_builder_id'].",
						last_modified_date = NOW(),
						location_tree = '".$_POST['locationTreeID']."'";
		mysql_query($insQRY);
		$task_id = mysql_insert_id();

		if($task_id > 0){
			$data[$_POST['sublocationid']] = '<li id="li_'.$task_id.'" class="taskList"><span class="jtree-button demo3" id="'.$task_id.'"><img src="images/task_simbol.png">&nbsp;'.$_POST['task'].'</span></li>';
			echo json_encode($data);
		}
	}else{
		echo 'Duplicate task';
	}
}
?>