<?php session_start();

$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

# Non Compliances List
/*public function showNonCompliances($meterScoreId=0, $msType=0){
	$this->loginExists();
	$data['message']= $this->session->userdata('message');
	$this->session->set_userdata('message','');
	$this->load->view('recordOfNonCompliancesSearchByAjax', $data);		
}*/

if(isset($_REQUEST["antiqueID"])){
	if(isset($_POST['task'])){
		$select = "SELECT checklist_id, task_name FROM qa_checklist_task WHERE id != ".$_POST['taskId']." AND task_name = '".addslashes(trim($_POST['task']))."' AND checklist_id = '". $_POST['checklistId'] ."' AND is_deleted = 0";
		$task = mysql_query($select);
		//$row_data = mysql_num_rows($issue);
		$row_data = mysql_fetch_row($task);
		// if($row_data > 0){
		// 	$_SESSION['checklist_add_err']='Duplicate record.';
		// 	$outputArr = array('status'=> true, 'error'=> true, 'msg'=> 'Task already exists!');
		// }else{
			$task_query = "task_name = '".addslashes(trim($_POST['task']))."',
							task_status = '".addslashes(trim($_POST['status']))."',
							task_image = '',
							task_comment = '".addslashes(trim($_POST['comment']))."',
							comment_mandatory = '".addslashes(trim($_POST['comment_mandatory']))."',
							last_modified_date = NOW(),
							original_modified_date = NOW(),
							last_modified_by = ".$builder_id;
			//echo "UPDATE qa_checklist_task SET ".$task_query." WHERE id=".$_POST['taskId']; die;
			mysql_query("UPDATE qa_checklist_task SET ".$task_query." WHERE id=".$_POST['taskId']);
			
			if(mysql_affected_rows() > 0){
				$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Task added successfully!','data'=>$_POST['checklistId']);
				$_SESSION['successMsg'] = $outputArr['msg'];
			}
		//}
		echo json_encode($outputArr);	die();
	}
}

if(isset($_REQUEST["antiqueID"]) && isset($_REQUEST["deletetaskId"])){
	$update_task_query = "is_deleted = 1,
						last_modified_date = NOW(),
						original_modified_date = NOW(),
						last_modified_by = ".$builder_id;
		//echo "UPDATE qa_checklist_task SET ".$task_query." WHERE id=".$_POST['taskId']; die;
		mysql_query("UPDATE qa_checklist_task SET ".$update_task_query." WHERE id=".$_REQUEST["deletetaskId"]);
		
		if(mysql_affected_rows() > 0){
			$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Task deleted successfully.','data'=>$_REQUEST["checklistId"]);
			$_SESSION['successMsg'] = $outputArr['msg'];
		}
	echo json_encode($outputArr);	die();
}

// Load HTML form 
if(isset($_REQUEST["antiqueID"]) && isset($_REQUEST["taskId"])){
	
	$taskData = $obj->selQRYMultiple('*', "qa_checklist_task", " is_deleted = '0' AND id = '".$_REQUEST["taskId"]."'");
	$taskData = $taskData[0];
	//echo "<pre>"; print_r($checklistData['chli_name']); die;
?>
<style>
	body{
		color:#000000;
	}
</style>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Task</legend>
		<form name="addTaskForm" id="addTaskForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			
			<tr>
				<td valign="top" align="left">Task <span class="req"><b>*</b></span></td>
				<td align="left">
					<input type="text" name="task" id="task" value="<?php echo $taskData['task_name']; ?>" >
					<lable for="task" id="errorTask" generated="true" class="error" style="display:none;">
						<div class="error-edit-profile">Task field is required</div>
					</lable>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">Hold Point?</td>
				<td align="left">
					<input type="radio" name="status" value="Yes" <?php if($taskData['task_status'] == 'Yes'){echo 'checked="checked"'; }?> > Yes
					<input type="radio" name="status" value="No" <?php if($taskData['task_status'] != 'Yes'){echo 'checked="checked"'; }?> > No
					<!-- input type="radio" name="status" value="NA" < ?php if($taskData['task_status'] == 'NA'){echo 'checked="checked"'; }?> > NA -->
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">Comment Mandatory?</td>
				<td align="left">
					<input type="radio" name="comment_mandatory" value="Yes" <?php if($taskData['comment_mandatory'] == 'Yes'){echo 'checked="checked"'; }?> > Yes
					<input type="radio" name="comment_mandatory" value="No" <?php if($taskData['comment_mandatory'] != 'Yes'){echo 'checked="checked"'; }?> > No
				</td>
			</tr>

			<tr id="commentMandatory">
				<td valign="top" align="left">Instructions <span class="req" id="commentStar"><?php if($taskData['comment_mandatory'] == 'Yes'){echo '<b>*</b>'; }?></span></td>
				<td align="left">
					<textarea name="comment" id="comment" class="text_area" ><?php echo $taskData['task_comment']; ?></textarea>
					<lable for="comment" id="errorComment" generated="true" class="error" style="display:none;">
						<div class="error-edit-profile">Instructions field is required</div>
					</lable>
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td>
    			<input type="hidden" name="checklistId" value="<?php echo $taskData['checklist_id']; ?>" />
    			<input type="hidden" name="taskId" value="<?php echo $taskData['id']; ?>" />
                <input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/submit.png);font-size:0px; border:none; width:111px;float:left;" onclick="updateTaskData(<?php echo $taskData['id']; ?>);" />
                &nbsp;&nbsp;&nbsp; <a id="ancor" href="javascript:editChecklistData(<?php echo $taskData['checklist_id']; ?>);" onclick="yes"> <img src="images/back_btn.png" style="border:none; width:111px;" /> </a>
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>