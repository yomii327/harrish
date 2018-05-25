<?php session_start();

$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

if(isset($_REQUEST["antiqueID"])){
	if(isset($_POST['name'])){
		$select = "SELECT chli_id, chli_name FROM c_check_list_items WHERE chli_name = '".addslashes(trim($_POST['name']))."' AND chli_is_deleted = 0";
		$issue = mysql_query($select);
		//$row_data = mysql_num_rows($issue);
		$row_data = mysql_fetch_row($issue);
		if($row_data > 0 && $row_data[0]!=$_POST['checklistId']){
			$_SESSION['checklist_add_err']='Duplicate record.';
			$outputArr = array('status'=> true, 'error'=> true, 'msg'=> 'Checklist already exists!');
		}else{
			$checklist_to_query = "chli_name = '".addslashes(trim($_POST['name']))."',
								   chli_type = '".addslashes(trim($_POST['checklistType']))."',
								   chli_modified = NOW(),
								   chli_modified_by = ".$builder_id;

			mysql_query("UPDATE c_check_list_items SET ".$checklist_to_query." WHERE chli_id='".trim($_POST['checklistId'])."'" );
			// mysql_query("UPDATE master_issue_to_contact SET issue_to_name = '".addslashes(trim($_POST['company_name']))."' WHERE master_issue_id='".trim($_POST['checklistId'])."'" );
			// mysql_query("UPDATE inspection_issue_to SET issue_to_name = '".addslashes(trim($_POST['company_name']))."' WHERE master_issue_id='".trim($_POST['checklistId'])."'" );	
										
			$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Checklist updated successfully!');
			$_SESSION['successMsg'] = $outputArr['msg'];
		}
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

.btn-mini {    border-radius: 6px;    font-size: 14px;    padding: 5px 10px;}
.btn-default {    background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #b6ff00 1%, #95cf06 7%, #5b8704 99%, #74a104 100%) repeat scroll 0 0;    border: 1px solid #74a104;    border-radius: 10px;    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.75);    color: #fff;    cursor: pointer;    display: inline-block;    font-family: "MyriadPro-SemiboldIt";    font-size: 18px;    font-weight: bold;    line-height: normal;    padding: 8px 20px;    text-decoration: none;    text-shadow: 0 1px 1px #000;}

</style>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Edit Checklist</legend>
		<div align="right">
			<input type="button" name="task_button" id="task_button" style="background-image:url(images/add_task_big.png);font-size:0px; border:none; height: 29px; width:77px;float:right;" onclick="popupChecklistTask(<?php echo $checklistData['chli_id']; ?>);" />
		</div>
		<form name="editChecklistForm" id="editChecklistForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr style="display:none;">
				<td width="197" valign="top"> Type <span class="req">*</span></td>
				<td width="217" valign="top">
					<select name="type" id="type" class="select_box" style="margin-left: -2px; width: 280px;">
						<option value="">Please select </option>
						<option value="Electrical" <?php if($checklistData['chli_type'] == 'Electrical'){echo 'selected="selected"';} ?> >Electrical</option>
						<option value="Hydraulics" <?php if($checklistData['chli_type'] == 'Hydraulics'){echo 'selected="selected"';} ?> >Hydraulics</option>
						<option value="Mechanical" <?php if($checklistData['chli_type'] == 'Mechanical'){echo 'selected="selected"';} ?> >Mechanical</option>
						<option value="Wall Framing &amp; Plasterboard" <?php if($checklistData['chli_type'] == 'Wall Framing &amp; Plasterboard'){echo 'selected="selected"';} ?> >Wall Framing &amp; Plasterboard</option>
						<option value="Fit-Off Inspection" <?php if($checklistData['chli_type'] == 'Fit-Off Inspection'){echo 'selected="selected"';} ?> >Fit-Off Inspection</option>
						<option value="Rough-in Inspection" <?php if($checklistData['chli_type'] == 'Rough-in Inspection'){echo 'selected="selected"';} ?> >Rough-in Inspection</option>
						<option value="Fire Services - Wet" <?php if($checklistData['chli_type'] == 'Fire Services - Wet'){echo 'selected="selected"';} ?> >Fire Services - Wet</option>
					</select>
					<lable for="type" id="errorChlType" generated="true" class="error" style="display:none;">
						<div class="error-edit-profile">The checklist type field is required</div>
					</lable>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">Name <span class="req">*</span></td>
				<td align="left">
					<textarea name="name" id="name" class="text_area" ><?php echo $checklistData['chli_name']; ?></textarea>
					<lable for="name" id="errorChlName" generated="true" class="error" style="display:none;">
					<div class="error-edit-profile">The checklist name field is required</div>
				</lable>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">Checklist Type</td>
				<td align="left">
					<select style="width:165px;" name="checklistType" id="checklistType" class="select_box">
						<option value="ITP" <?php if($checklistData['chli_type'] == 'ITP'){echo 'selected="selected"';} ?>>ITP</option>
						<option value="Ad Hoc Checklist" <?php if($checklistData['chli_type'] == 'Ad Hoc Checklist'){echo 'selected="selected"';} ?>>Ad Hoc Checklist</option>
					</select>
				</td>
			</tr>

			<br/>
			<div>
				<!-- button class="btn-mini btn-default" onclick="changeOrderTaskList(< ?php echo $checklistData['chli_id']; ?>)">Change Order</button -->
				<input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/change_order.png);font-size:0px; border:none; width:111px;float:right;margin-top: 110px;margin-right: -83px;" onclick="changeOrderTaskList(<?php echo $checklistData['chli_id']; ?>);" />
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
    			<input type="hidden" name="checklistId" value="<?php echo $checklistData['chli_id']; ?>" />
                <input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/update.png);font-size:0px; border:none; width:111px;float:right;" onclick="updateChecklistData(<?php echo $checklistData['chli_id']; ?>);" />
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>