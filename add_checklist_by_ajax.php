<?php session_start();
$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

// Add new contact
if(isset($_REQUEST["antiqueID"])){
	$outputArr = array();
	if(isset($_POST['name'])){
		$select = "SELECT chli_id FROM c_check_list_items WHERE chli_name = '".addslashes(trim($_POST['name']))."' AND chli_is_deleted=0";
		$issue = mysql_query($select);
		$row_data = mysql_num_rows($issue);
		if($row_data > 0){
			$_SESSION['issue_add_err']='Duplicate record.';
			$outputArr = array('status'=> true, 'error'=> true, 'msg'=> 'Checklist already exists!');
		}else{
			//$_POST['checklistType']
			$checklist_to_query = "project_id = 0,
								chli_name = '".addslashes(trim($_POST['name']))."',
								chli_type = '".addslashes(trim($_POST['checklistType']))."',
								chli_created = NOW(),
								chli_created_by = ".$builder_id.",
								chli_modified = NOW(),
								chli_original_modified = NOW(),
								chli_modified_by = ".$builder_id.",
								chli_resource_type = 'web'";

			mysql_query("INSERT INTO c_check_list_items SET ".$checklist_to_query);
			//$checklistId = mysql_insert_id();
			if(mysql_affected_rows() > 0){
				$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Checklistpp added successfully!');
				$_SESSION['successMsg'] = $outputArr['msg'];
			}
			//$_SESSION['issue_add'] = 'Issued to added successfully.';
		}
	}else{
		//$_SESSION['issue_add_err']='Issued to not added.';
		$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Checklist to added successfully!');
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
		<legend style="color:#000000;">Add Checklist</legend>
		<form name="addChecklistForm" id="addChecklistForm">
			<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr style="display:none;">
					<td width="197" valign="top"> Type <span class="req">*</span></td>
					<td width="217" valign="top">
						<select name="type" id="type" class="select_box" style="margin-left: -2px; width: 280px;">
							<option value="">Please select </option>
							<option value="Electrical">Electrical</option>
							<option value="Hydraulics">Hydraulics</option>
							<option value="Mechanical">Mechanical</option>
							<option value="Wall Framing &amp; Plasterboard">Wall Framing &amp; Plasterboard</option>
							<option value="Fit-Off Inspection">Fit-Off Inspection</option>
							<option value="Rough-in Inspection">Rough-in Inspection</option>
							<option value="Fire Services - Wet">Fire Services - Wet</option>
						</select>
						<lable for="type" id="errorChlType" generated="true" class="error" style="display:none;">
							<div class="error-edit-profile">The checklist type field is required</div>
						</lable>
					</td>
				</tr>

				<tr>
					<td valign="top" align="left">Checklist Name</td>
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
						<input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/save.png);font-size:0px; border:none; width:111px;float:left;" onclick="addNewIssueToData();" />
					</td>
				</tr>
			</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>
