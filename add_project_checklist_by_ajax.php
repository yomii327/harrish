<?php session_start();
$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

// Add new contact
$project_id = $_SESSION['idp'];
if(isset($_REQUEST["antiqueID"])){
	//echo "<pre>"; print_r($_REQUEST);
	$outputArr = array();
	if(isset($_POST['name'])){
		$select = "SELECT id FROM check_list_items_project WHERE checklist_name = '".addslashes(trim($_POST['name']))."' AND project_id = '".$project_id."' AND is_deleted=0";
		$issue = mysql_query($select);
		$row_data = mysql_num_rows($issue);
		if($row_data > 0){
			$_SESSION['issue_add_err']='Duplicate record.';
			$outputArr = array('status'=> false, 'error'=> true, 'msg'=> 'Project Checklist already exists!');
		}else{
			$checklist_to_query = "project_id = ".$project_id.",
								checklist_name = '".addslashes(trim($_POST['name']))."',
								type = '".addslashes(trim($_POST['checklistType']))."',
								last_modified_date = NOW(),
								original_modified_date = NOW(),
								last_modified_by = ".$builder_id.",
								created_date = NOW(),
								created_by = ".$builder_id;
			mysql_query("INSERT INTO check_list_items_project SET ".$checklist_to_query);
			//$checklistId = mysql_insert_id();
			if(mysql_affected_rows() > 0){
				$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Project Checklistpp added successfully!');
				$_SESSION['successMsg'] = $outputArr['msg'];
			}
			//$_SESSION['issue_add'] = 'Issued to added successfully.';
		}
	}else{
		//$_SESSION['issue_add_err']='Issued to not added.';
		$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Project Checklist to added successfully!');
	}
	echo json_encode($outputArr); die();	
}

// Load HTML form 
if(isset($_REQUEST["name"])){
?>
<style>
body{
	color:#000000;
}
</style>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Add Project Checklist</legend>
		<form name="addChecklistForm" id="addChecklistForm">
			<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
				
				<tr>
					<td valign="top" align="left">Name</td>
					<td align="left">
						<textarea name="name" id="name" class="text_area"></textarea>
						<lable for="name" id="errorChlName" generated="true" class="error" style="display:none;">
							<div class="error-edit-profile">The checklist name field is required</div>
						</lable>
					</td>
				</tr>

				<tr>
					<td valign="top" align="left">Checklist Type</td>
					<td align="left">
						<select style="width:165px;" name="checklistType" id="checklistType" class="select_box">
							<option value="ITP">ITP</option>
							<option value="Ad Hoc Checklist">Ad Hoc Checklist</option>
						</select>
					</td>
				</tr>

				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="hidden" name="projectId" value="<?php echo $_SESSION['idp'];?>" />
						<input type="button" name="button" class="green_small" id="button" style="float:left;" onclick="addNewProjectChecklistData();" value="Save"/>
					</td>
				</tr>
			</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>
