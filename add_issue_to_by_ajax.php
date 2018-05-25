<?php session_start();
$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

// Add new contact
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
		$select = "SELECT id FROM master_issue_to WHERE issue_to_name = '".addslashes(trim($_POST['company_name']))."' AND is_deleted=0";
		$issue = mysql_query($select);
		$row_data = mysql_num_rows($issue);
		if($row_data > 0){
			$_SESSION['issue_add_err']='Duplicate record.';
			$outputArr = array('status'=> true, 'error'=> true, 'msg'=> 'Company name already exists!');
		}else{
			$issue_to_query = " issue_to_name = '".addslashes(trim($_POST['company_name']))."',
								company_name = '".addslashes(trim($_POST['contact_name']))."',
								issue_to_phone = '".addslashes(trim($_POST['phone']))."',
								issue_to_email = '".addslashes(trim($_POST['emailid']))."',
								tag = '".addslashes(trim($_POST['tag']))."',
								last_modified_date = NOW(),
								last_modified_by = ".$builder_id.",
								created_date = NOW(),
								created_by = ".$builder_id;

			mysql_query("INSERT INTO master_issue_to SET ".$issue_to_query);
			$masterIssueToId = mysql_insert_id();
			
			mysql_query("INSERT INTO master_issue_to_contact SET is_default = 1, master_issue_id = '".$masterIssueToId."', ".$issue_to_query);
			$masterIssueToContactId = mysql_insert_id();
			if(isset($_POST['projectId']) && !empty($_POST['projectId'])){
				mysql_query("INSERT INTO inspection_issue_to SET is_default = 1, project_id = '".addslashes(trim($_POST['projectId']))."', master_issue_id = '".$masterIssueToId."', master_contact_id = '".$masterIssueToContactId."', ".$issue_to_query);
				$issueToId = mysql_insert_id();
				
			/*	mysql_query("INSERT INTO inspection_issue_to_contact SET is_default = 1, project_id = '".addslashes(trim($_POST['projectId']))."', master_issue_id = '".$masterIssueToId."', issue_to_id = '".$issueToId."', ".$issue_to_query);
				$masterIssueToId = mysql_insert_id();
			*/
			}
			if(mysql_affected_rows() > 0){
				$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Issued to added successfully!');
				$_SESSION['successMsg'] = $outputArr['msg'];
			}
					
			//$_SESSION['issue_add'] = 'Issued to added successfully.';
		}
	}else{
		//$_SESSION['issue_add_err']='Issued to not added.';
		$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Issued to added successfully!');
	}
	echo json_encode($outputArr);	
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
		<legend style="color:#000000;">Add Issue To</legend>
		<form name="addIssueToForm" id="addIssueToForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top" align="left" width="50%">Company Name <span class="req">*</span></td>
				<td align="left" width="50%">
					<input name="company_name" type="text" class="input_small" id="company_name"  value="" />
                    <lable for="contact_name" id="errorCompanyName" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Company Name field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Contact Name <span class="req">*</span></td>
				<td align="left">
					<input name="contact_name" type="text" class="input_small" id="contact_name"  value=""/>
                    <lable for="contact_name" id="errorContactName" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Contact Name field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Phone <span class="req">*</span></td>
				<td align="left">
					<input name="phone" type="text" class="input_small" id="phone" value=""/>
					<lable for="phone_number" id="errorPhone" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Phone Number field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Email <span class="req">*</span></td>
				<td align="left">
					<input name="emailid" type="text" class="input_small" id="emailid" value=""/>
					<lable for="email_id" id="errorEmailId" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Email Id field is required</div></lable>
					<lable for="email_id" id="errorEmailIdValid" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Email Id field is not Valid</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Tags</td>
				<td align="left">
					<textarea name="tag" id="tag" class="text_area"></textarea>
				</td>
			</tr>                                    
			<tr>
				<td>&nbsp;</td>
				<td>
                <input type="hidden" name="projectId" value="<?php echo $_SESSION['idp'];?>" />
                <!-- <input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/save.png);font-size:0px; border:none; width:111px;float:left;" onclick="addNewIssueToData();" />
					&nbsp;&nbsp;&nbsp; <a id="ancor" href="javascript:closePopup(300);" onclick="yes"> <img src="images/back_btn.png" style="border:none; width:111px;" /> </a>
				</td> -->
				<input type="button" name="button" class="green_small" id="button" style="float:left;cursor: pointer;" value="Save" onclick="addNewIssueToData();" />
					&nbsp;&nbsp;&nbsp; 
					<a id="ancor" href="javascript:closePopup(300);" class="green_small" onclick="yes">Back</a>
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>
