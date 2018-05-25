<?php session_start();

$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

$project_id = $_SESSION['idp'];
if(isset($_REQUEST["antiqueID"])){
	if(isset($_POST['name'])){
		$select = "SELECT id, checklist_name FROM check_list_items_project WHERE checklist_name = '".addslashes(trim($_POST['name']))."' AND AND project_id = '".$project_id."' is_deleted = 0";
		$issue = mysql_query($select);
		//$row_data = mysql_num_rows($issue);
		$row_data = mysql_fetch_row($issue);
		if($row_data > 0 && $row_data[0]!=$_POST['checklistId']){
			$_SESSION['checklist_add_err']='Duplicate record.';
			$outputArr = array('status'=> true, 'error'=> true, 'msg'=> 'Project Checklist already exists!');
		}else{
			$checklist_to_query = "checklist_name = '".addslashes(trim($_POST['name']))."',
									type = '".addslashes(trim($_POST['checklistType']))."',
								   	last_modified_date = NOW(),
								 	original_modified_date = NOW(),
									last_modified_by = ".$builder_id;
			//echo "UPDATE check_list_items_project SET ".$checklist_to_query." WHERE id='".trim($_POST['checklistId']) . '<<==='; die();
			mysql_query("UPDATE check_list_items_project SET ".$checklist_to_query." WHERE id='".trim($_POST['checklistId'])."'" );
			// mysql_query("UPDATE master_issue_to_contact SET issue_to_name = '".addslashes(trim($_POST['company_name']))."' WHERE master_issue_id='".trim($_POST['checklistId'])."'" );
			// mysql_query("UPDATE inspection_issue_to SET issue_to_name = '".addslashes(trim($_POST['company_name']))."' WHERE master_issue_id='".trim($_POST['checklistId'])."'" );	
										
			$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Project Checklist updated successfully!');
			$_SESSION['successMsg'] = $outputArr['msg'];
		}
		echo json_encode($outputArr);	die();
	}
}


// Load HTML form 
if(isset($_REQUEST["checklistId"])){
	
	$checklistData = $obj->selQRYMultiple('*', "check_list_items_project", " project_id = '".$project_id."' AND is_deleted = '0' AND id = '".$_REQUEST["checklistId"]."'");	
	$checklistData = $checklistData[0];
	//echo "<pre>"; print_r($checklistData['checklist_name']); die;
?>
<style>
body{
	color:#000000;
}

.btn-mini {    border-radius: 6px;    font-size: 14px;    padding: 5px 10px;}
.btn-default {    background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #b6ff00 1%, #95cf06 7%, #5b8704 99%, #74a104 100%) repeat scroll 0 0;    border: 1px solid #74a104;    border-radius: 10px;    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.75);    color: #fff;    cursor: pointer;    display: inline-block;    font-family: "MyriadPro-SemiboldIt";    font-size: 18px;    font-weight: bold;    line-height: normal;    padding: 8px 20px;    text-decoration: none;    text-shadow: 0 1px 1px #000;}

</style>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Edit Checklist</legend>
		<div align="right">
			<!-- <input type="button" name="task_button" id="task_button" style="background-image:url(images/add_task_big.png);font-size:0px; border:none; height: 29px; width:77px;float:right;" onclick="addProjectChecklistTask(<?php //echo $checklistData['id']; ?>);" /> -->
			<input type="button" name="task_button" id="task_button" style="height: 29px;float:right;" class="green_small" value="Add Task" onclick="addProjectChecklistTask(<?php echo $checklistData['id']; ?>);" />
		</div>
		<form name="editChecklistForm" id="editChecklistForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			
			<tr>
				<td valign="top" align="left" style="color:#000000;">Name <span class="req">*</span></td>
				<td align="left">
					<textarea name="name" id="name" class="text_area" ><?php echo $checklistData['checklist_name']; ?></textarea>
					<lable for="name" id="errorChlName" generated="true" class="error" style="display:none;">
					<div class="error-edit-profile">The checklist name field is required</div>
				</lable>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">Checklist Type</td>
				<td align="left">
					<select style="width:165px;" name="checklistType" id="checklistType" class="select_box">
						<option value="ITP" <?php if($checklistData['type'] == 'ITP'){echo 'selected="selected"';} ?>>ITP</option>
						<option value="Ad Hoc Checklist" <?php if($checklistData['type'] == 'Ad Hoc Checklist'){echo 'selected="selected"';} ?>>Ad Hoc Checklist</option>
					</select>
				</td>
			</tr>

			<br/>
			<div>
				<!-- button class="btn-mini btn-default" onclick="changeOrderTaskList(< ?php echo $checklistData['id']; ?>)">Change Order</button -->
				<!-- <input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/change_order.png);font-size:0px; border:none; width:111px;float:right;margin-top: 110px;margin-right: -83px;" onclick="changeOrderProjectTaskList(<?php echo $checklistData['id']; ?>);" /> -->
				<input type="button" name="button" class="green_small" id="button" style="float:right;margin-top: 155px;margin-right: -113px;" value="Change Order" onclick="changeOrderProjectTaskList(<?php echo $checklistData['id']; ?>);" />
			</div>
			<div class="demo_jui" style="width:99%" >
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="task_server" width="100%" style="color:#000000;">
					<thead>
						<tr>
							<th width="85%" nowrap="nowrap">Task</th>
							<!-- <th width="35%" nowrap="nowrap">Comment</th> -->
							<th width="15%">Action</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="5" class="dataTables_empty">Loading data from server</td>
						</tr>
					</tbody>
				</table>
			</div>

			<br/> <br/>

			<tr>
				<td>&nbsp;</td>
				<td>
    			<input type="hidden" name="checklistId" value="<?php echo $checklistData['id']; ?>" />
                <input type="button" name="button" class="green_small" value="Update" id="button" style="float:right;" onclick="updateProjectChecklistData(<?php echo $checklistData['id']; ?>);" />
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>