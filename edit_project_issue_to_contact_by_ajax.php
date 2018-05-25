<?php session_start();

$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

if(isset($_REQUEST["antiqueID"])){
	$issue_to_tags = '';
	if(isset($_POST['tag']) && !empty($_POST['tag'])){
		$issue_to_tags=$_POST['tag'];	
		$issue_to_tags = trim($issue_to_tags, ";");
		$issue_to_tags = implode(";", array_map('trim', explode(";", $issue_to_tags)));
		if ($issue_to_tags != "")
			$issue_to_tags = trim($issue_to_tags) . ";";
	}
	$_POST['tag'] = $issue_to_tags;
	if(isset($_POST['company_name'])){
		$select = "SELECT contact_id, company_name FROM master_issue_to_contact WHERE company_name = '".addslashes(trim($_POST['contact_name']))."' AND master_issue_id = '".addslashes(trim($_POST['masterIssueToId']))."' AND is_deleted = 0";
		$issue = mysql_query($select);
		//$row_data = mysql_num_rows($issue);
		$row_data = mysql_fetch_row($issue);
		if($row_data > 0 && $row_data[0]!=$_POST['contactId']){
			$_SESSION['issue_add_err']='Duplicate record.';
			$outputArr = array('status'=> true, 'error'=> true, 'msg'=> 'Contact name already exists!');
		}else{
			$issue_to_update_query = " issue_to_name = '".addslashes(trim($_POST['company_name']))."',
								company_name = '".addslashes(trim($_POST['contact_name']))."',
								user_title = '".addslashes(trim($_POST['user_title']))."',
								issue_to_phone = '".addslashes(trim($_POST['phone']))."',
								issue_to_email = '".addslashes(trim($_POST['emailid']))."',
								tag = '".addslashes(trim($_POST['tag']))."',
								activity = '".addslashes(trim($_POST['activity']))."',
								last_modified_date = NOW(),
								last_modified_by = ".$builder_id;
								
			mysql_query("UPDATE master_issue_to_contact SET ".$issue_to_update_query." WHERE contact_id='".trim($_POST['contactId'])."'");
			
			if(isset($_SESSION['idp']) && $_SESSION['idp']>0){
				mysql_query("UPDATE inspection_issue_to SET ".$issue_to_update_query." WHERE master_contact_id='".trim($_POST['contactId'])."' AND project_id=".$_SESSION['idp']);
			}	
			
			if(isset($_POST['isDefault']) && $_POST['isDefault']==1){
				mysql_query("UPDATE master_issue_to SET ".$issue_to_update_query." WHERE id='".trim($_POST['masterIssueToId'])."'");
			}
			$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Issued to contact updated successfully!');
			$_SESSION['successMsg'] = $outputArr['msg'];
		}
		echo json_encode($outputArr);			
	}
}


// Load HTML form 
if(isset($_REQUEST["name"]) && isset($_REQUEST["contactId"])){
	
	$issueToContactData = $obj->selQRYMultiple('*', "master_issue_to_contact", " is_deleted = '0' AND contact_id= '".$_REQUEST["contactId"]."'");	
	$contData = $issueToContactData[0];
?>
<style>
body{
	color:#000000;
}
</style>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Edit Issue To Contact</legend>
		<form name="editContactForm" id="editContactForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top" align="left" width="50%">Company Name</td>
				<td align="left" width="50%">
					<input name="name" type="text" class="input_small" id=""  value="<?php echo $contData['issue_to_name']; ?>" disabled="disabled"/>
                    <input name="company_name" type="hidden" class="input_small" id="company_name"  value="<?php echo $contData['issue_to_name']; ?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Contact Name <span class="req">*</span></td>
				<td align="left">
					<input name="contact_name" type="text" class="input_small" id="contact_name"  value="<?php echo $contData['company_name']; ?>"/>
                    <lable for="contact_name" id="errorContactName" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Contact Name field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">User Title <span class="req">*</span></td>
				<td align="left">
					<input name="user_title" type="text" class="input_small" id="user_title"  value="<?php echo $contData['user_title']; ?>"/>
                    <lable for="user_title" id="errorUserTitle" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The User Title field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Phone</td>
				<td align="left">
					<input name="phone" type="text" class="input_small" id="phone" value="<?php echo $contData['issue_to_phone']; ?>"/>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Email  <span class="req">*</span></td>
				<td align="left">
					<input name="emailid" type="text" class="input_small" id="emailid" value="<?php echo $contData['issue_to_email']; ?>"/>
					<lable for="email_id" id="errorEmailId" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Email Id field is required</div></lable>
					<lable for="email_id" id="errorEmailIdValid" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Email Id field is not Valid</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Tags</td>
				<td align="left">
					<textarea name="tag" id="tag" class="text_area" style="padding-left:10px; width: 280px;"><?php echo $contData['tag']; ?></textarea>
				</td>
			</tr>   
			<tr>
				<td valign="top" align="left">Type</td>
				<td align="left">
				<?php if($contData['activity'] == ""){ $contData['activity'] = 'Other';} ?>
					<select name="activity" class="input_small" id="activity"> 
						<option value="Client" <?=($contData['activity'] == "Client")?'selected':'';?>>Client</option>
						<option value="Consultant" <?=($contData['activity'] == "Consultant") ? 'selected':'';?>>Consultant</option>
						<option value="Contractor" <?=($contData['activity'] == "Contractor") ? 'selected' :''?>>Contractor</option>
						<option value="Sub Contractor" <?=($contData['activity'] == "Sub Contractor") ? 'selected' :''?>>Sub Contractor</option>
						<option value="General Suppliers" <?=($contData['activity'] == "General Suppliers") ? 'selected' :''?>>General Suppliers</option>
						<option value="Other" <?=($contData['activity'] == "Other") ? 'selected' :''?>>Other</option>
					</select>
				</td>
			</tr>                                    
			<tr>
				<td>&nbsp;</td>
				<td>
                <input type="hidden" name="contactId" value="<?php echo $contData['contact_id']; ?>" />
    			<input type="hidden" name="masterIssueToId" value="<?php echo $contData['master_issue_id']; ?>" />
    			<input type="hidden" name="isDefault" id="isDefault" value="<?php echo $contData['is_default']; ?>" />                
                <input type="button" name="button" class="green_small" id="button" style="float:left;" onclick="updateIssueToContactData(<?=$contData['master_issue_id']?>);" value="Update" />
					&nbsp;&nbsp;&nbsp;
					<a id="ancor" href="javascript:showIssueTo(<?php echo $contData['master_issue_id']; ?>);" onclick="yes" class="green_small">Back</a>
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>
