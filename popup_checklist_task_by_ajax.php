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
		$select = "SELECT id, task_name FROM qa_checklist_task WHERE task_name = '".addslashes(trim($_POST['task']))."' AND checklist_id = '". $_POST['checklistId'] ."' AND is_deleted = 0";
		$issue = mysql_query($select);
		//$row_data = mysql_num_rows($issue);
		$row_data = mysql_fetch_row($issue);
		// if($row_data > 0 && $row_data[0]!=$_POST['checklistId']){
		// 	$_SESSION['checklist_add_err']='Duplicate record.';
		// 	$outputArr = array('status'=> true, 'error'=> true, 'msg'=> 'Task already exists!');
		// }else{
			$task_query = "checklist_id = '".addslashes(trim($_POST['checklistId']))."',
							task_name = '".addslashes(trim($_POST['task']))."',
							task_status = '".addslashes(trim($_POST['status']))."',
							task_image = '',
							task_comment = '".addslashes(trim($_POST['comment']))."',
							comment_mandatory = '".addslashes(trim($_POST['comment_mandatory']))."',
							last_modified_date = NOW(),
							original_modified_date = NOW(),
							last_modified_by = ".$builder_id.",
							created_date = NOW(),
							created_by = ".$builder_id;
			
			mysql_query("INSERT INTO qa_checklist_task SET ".$task_query);
			
			if(mysql_affected_rows() > 0){
				$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Task added successfully!');
				$_SESSION['successMsg'] = $outputArr['msg'];
			}
		//}
		echo json_encode($outputArr);	die();
	}
}


// Load HTML form 
if(isset($_REQUEST["name"]) && isset($_REQUEST["checklistId"])){
	
	$checklistData = $obj->selQRYMultiple('*', "c_check_list_items", " chli_is_deleted = '0' AND chli_id = '".$_REQUEST["checklistId"]."'");	
	$checklistData = $checklistData[0];
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
				<td valign="top" align="left">Task <span class="req">*</span></td>
				<td align="left">
					<input type="text" name="task" id="task" value="">
					<lable for="task" id="errorTask" generated="true" class="error" style="display:none;">
						<div class="error-edit-profile">Task field is required</div>
					</lable>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">Hold Point?</td>
				<td align="left">
					<input type="radio" name="status" value="Yes"> Yes
					<input type="radio" name="status" value="No" checked> No
					<!--input type="radio" name="status" value="NA"> NA -->
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">Comment Mandatory?</td>
				<td align="left">
					<input type="radio" name="comment_mandatory" value="Yes"> Yes
					<input type="radio" name="comment_mandatory" value="No" checked> No
				</td>
			</tr>

			<tr id="commentMandatory">
				<td valign="top" align="left">Instructions <span class="req" id="commentStar"></span></td>
				<td align="left">
					<textarea name="comment" id="comment" class="text_area" ></textarea>
					<lable for="comment" id="errorComment" generated="true" class="error" style="display:none;">
						<div class="error-edit-profile">Instructions field is required</div>
					</lable>
				</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td>
    			<input type="hidden" name="checklistId" value="<?php echo $checklistData['chli_id']; ?>" />
                <input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/submit.png);font-size:0px; border:none; width:111px;float:left;" onclick="addTaskData(<?php echo $checklistData['chli_id']; ?>);" />
                &nbsp;&nbsp;&nbsp; <a id="ancor" href="javascript:editChecklistData(<?php echo $checklistData['chli_id']; ?>);" onclick="yes"> <img src="images/back_btn.png" style="border:none; width:111px;" /> </a>
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>