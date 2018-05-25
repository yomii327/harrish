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
		$select = "SELECT id, company_name FROM master_issue_to WHERE issue_to_name = '".addslashes(trim($_POST['company_name']))."' AND is_deleted = 0";
		$issue = mysql_query($select);
		//$row_data = mysql_num_rows($issue);
		$row_data = mysql_fetch_row($issue);
		if($row_data > 0 && $row_data[0]!=$_POST['masterIssueToId']){
			$_SESSION['issue_add_err']='Duplicate record.';
			$outputArr = array('status'=> true, 'error'=> true, 'msg'=> 'Company name already exists!');
		}else{
			$issue_to_update_query = "issue_to_name = '".addslashes(trim($_POST['company_name']))."',
								company_name = '".addslashes(trim($_POST['contact_name']))."',
								issue_to_phone = '".addslashes(trim($_POST['phone']))."',
								issue_to_email = '".addslashes(trim($_POST['emailid']))."',
								tag = '".addslashes(trim($_POST['tag']))."',
								last_modified_date = NOW(),
								last_modified_by = ".$builder_id;
				mysql_query("UPDATE master_issue_to SET ".$issue_to_update_query." WHERE id='".trim($_POST['masterIssueToId'])."'" );
				mysql_query("UPDATE master_issue_to_contact SET issue_to_name = '".addslashes(trim($_POST['company_name']))."' WHERE master_issue_id='".trim($_POST['masterIssueToId'])."'" );
				mysql_query("UPDATE inspection_issue_to SET issue_to_name = '".addslashes(trim($_POST['company_name']))."' WHERE master_issue_id='".trim($_POST['masterIssueToId'])."'" );	
										
			$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Issued to updated successfully!');
			$_SESSION['successMsg'] = $outputArr['msg'];
		}
		echo json_encode($outputArr);			
	}
}


// Load HTML form 
if(isset($_REQUEST["name"]) && isset($_REQUEST["issueToId"])){
	
	$issueToData = $obj->selQRYMultiple('*', "master_issue_to", " is_deleted = '0' AND id = '".$_REQUEST["issueToId"]."'");	
	$issueToData = $issueToData[0];
?>
<style>
body{
	color:#000000;
}
</style>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Edit Issue To		</legend>
		<form name="editCompanyForm" id="editCompanyForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top" align="left" width="50%">Company Name <span class="req">*</span></td>
				<td align="left" width="50%">
					<input name="company_name" type="text" class="input_small" id="company_name"  value="<?php echo $issueToData['issue_to_name']; ?>" />
                    <lable for="contact_name" id="errorCompanyName" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Company Name field is required</div></lable>
				</td>
			</tr>
			<tr style="display:none;">
				<td valign="top" align="left">Contact Name</td>
				<td align="left">
					<input name="contact_name" type="text" class="input_small" id="contact_name"  value="<?php echo $issueToData['company_name']; ?>"/>
                    <lable for="contact_name" id="errorContactName" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Contact Name field is required</div></lable>
				</td>
			</tr>
			<tr style="display:none;">
				<td valign="top" align="left">Phone</td>
				<td align="left">
					<input name="phone" type="text" class="input_small" id="phone" value="<?php echo $issueToData['issue_to_phone']; ?>"/>
				</td>
			</tr>
			<tr style="display:none;">
				<td valign="top" align="left">Email</td>
				<td align="left">
					<input name="emailid" type="text" class="input_small" id="emailid" value="<?php echo $issueToData['issue_to_email']; ?>"/>
				</td>
			</tr>
			<tr style="display:none;">
				<td valign="top" align="left">Tags</td>
				<td align="left">
					<textarea name="tag" id="tag" class="text_area" style="padding-left:10px; width: 280px;"><?php echo $issueToData['tag']; ?></textarea>
				</td>
			</tr>                                    
			<tr>
				<td>&nbsp;</td>
				<td>
    			<input type="hidden" name="masterIssueToId" value="<?php echo $issueToData['id']; ?>" />
                <input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/update.png);font-size:0px; border:none; width:111px;float:left;" onclick="updateIssueToData(<?php echo $issueToData['id']; ?>);" />
					&nbsp;&nbsp;&nbsp; <a id="ancor" href="javascript:showIssueTo(<?php echo $issueToData['id']; ?>);" onclick="yes"> <img src="images/back_btn.png" style="border:none; width:111px;" /> </a>
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>