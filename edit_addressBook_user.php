<?php session_start();

$builder_id=$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 
if(isset($_REQUEST["uniqueID"])){
	$fullName = trim(addslashes($_POST['fullName']));
	$userEmail = trim(addslashes($_POST['userEmail']));
	$companyName = trim(addslashes($_POST['companyName']));
	$userPhone = trim(addslashes($_POST['userPhone']));	
	$updateID = trim(addslashes($_POST['updateID']));	
	$physicalAdd = trim(addslashes($_POST['physicalAdd']));	
	$attachment_noti_type = trim(addslashes($_POST['attachment_noti_type']));	
	
	if(isset($_POST['activity']) && !empty($_POST['activity'])){
		$activity=$_POST['activity'];	
		$activity=trim($activity, ";");
		$activity = implode(";", array_map('trim', explode(";", $activity)));
		if ($activity != ""){
			$activity = trim($activity) . ";";
		}
	}else{
		$activity='';
	}
	
	if($_POST["updateType"] == 'adHoc'){
		$updateQRY = "UPDATE pmb_address_book SET
			full_name = '".$fullName."',
			company_name = '".$companyName."',
			user_phone = '".$userPhone."',
			user_email = '".$userEmail."',
			activity = '".$activity."',	
			attachment_noti_type = '".$attachment_noti_type."',	
			physical_address = '".$physicalAdd."',		
			last_modified_date = NOW(),
			last_modified_by = '".$builder_id."'  
		WHERE 
			id = ".$updateID;
	}else if($_POST["updateType"] == 'issuedTo'){
		$updateQRY = "UPDATE inspection_issue_to SET
			issue_to_name = '".$companyName."',
			company_name = '".$fullName."',
			issue_to_phone = '".$userPhone."',
			issue_to_email = '".$userEmail."',
			activity = '".$activity."',		
			physical_address = '".$physicalAdd."',	
			attachment_noti_type = '".$attachment_noti_type."',			
			last_modified_date = NOW(),
			last_modified_by = '".$builder_id."'
		WHERE
			issue_to_id = ".$updateID;
	}else if($_POST["updateType"] == 'projectUser'){
		$updateQRY = "UPDATE user SET
			user_fullname = '".$fullName."',
			company_name = '".$companyName."',
			user_phone_no = '".$userPhone."',
			user_email = '".$userEmail."',
			activity = '".$activity."',		
			physical_address = '".$physicalAdd."',
			attachment_noti_type = '".$attachment_noti_type."',				
			last_modified_date = NOW(),
			last_modified_by = '".$builder_id."'
		WHERE
			user_id = ".$updateID;
	}
	mysql_query($updateQRY);

	if(mysql_affected_rows() > 0){	
		$outputArr = array('status'=> true, 'msg'=> 'Record updated successfully', 'dataId'=> $updateID, 'dataString'=> $fullName);
	}
	echo json_encode($outputArr);
}


$projectUsers = mysql_query('select  from  Where '); 
				

if(isset($_REQUEST["name"])){
	if($_REQUEST["usertype"] == 'adHoc'){
		$addressBookUserData = $obj->selQRYMultiple('id, full_name, company_name, user_phone, user_email, activity, physical_address, attachment_noti_type', 'pmb_address_book', 'is_deleted = 0 AND project_id = '.$_SESSION['idp'].' AND id = '.$_GET['userid']);
	}else if($_REQUEST["usertype"] == 'issuedTo'){
		$addressBookUserData = $obj->selQRYMultiple('issue_to_id AS id, issue_to_name AS company_name, company_name AS full_name, issue_to_phone AS user_phone, issue_to_email AS user_email, activity, physical_address, attachment_noti_type', 'inspection_issue_to', 'is_deleted = 0 AND project_id = '.$_SESSION['idp'].' AND issue_to_id = '.$_GET['userid']);
	}else if($_REQUEST["usertype"] == 'projectUser'){
		$addressBookUserData = $obj->selQRYMultiple('u.user_id AS id, u.user_fullname AS full_name, u.company_name AS company_name, u.user_phone_no AS user_phone, u.user_email AS user_email, activity, physical_address, attachment_noti_type' , 'user AS u LEFT JOIN user_projects AS up ON u.user_id = up.user_id AND up.is_deleted=0', 'u.user_id = "'.$_GET['userid'].'" AND u.user_id != "'.$_SESSION['ww_builder_id'].'" AND u.is_deleted = 0 AND up.project_id = "'.$_SESSION['idp'].'" ORDER BY u.user_name');
	} ?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Edit contact detail</legend>
		<form name="addUserForm" id="addUserForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top" align="left">Full&nbsp;Name <span class="req">*</span></td>
				<td>:</td>
				<td align="left">
					<input type="text" name="fullName" id="fullName" class="input_small" value="<?=$addressBookUserData[0]['full_name']?>" />
					<lable for="multiUpload" id="errorFullName" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing Title field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Email Address<span class="req">*</span></td>
				<td>:</td>
				<td align="left">
					<input type="text" name="userEmail" id="userEmail" class="input_small" value="<?=$addressBookUserData[0]['user_email']?>" />
					<lable for="drawingRevision" id="errorUserEmail" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing Revision field is required</div></lable>
					<lable for="drawingRevision" id="errorUserEmailValid" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The email address is not valid</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Company&nbsp;Name</td>
				<td>:</td>
				<td align="left">
					<input type="text" name="companyName" id="companyName" class="input_small" value="<?=$addressBookUserData[0]['company_name']?>" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Contact&nbsp;Number</td>
				<td>:</td>
				<td align="left">
					<input type="text" name="userPhone" id="userPhone" class="input_small" value="<?=$addressBookUserData[0]['user_phone']?>" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Activity</td>
				<td valign="top">:</td>
				<td align="left" valign="top">
					<textarea name="activity" id="activity" class="text_area"><?=$addressBookUserData[0]['activity']?></textarea>
					<br/>
					Please seperate location by semicolon(;).
				</td>
			</tr>            
			<tr>
				<td valign="top" align="left">Physical&nbsp;address</td>
				<td valign="top">:</td>
				<td align="left" valign="top">
					<textarea name="physicalAdd" id="physicalAdd" class="text_area"><?=$addressBookUserData[0]['physical_address']?></textarea>
					<br/>
					Please seperate location by semicolon(;).
				</td>
			</tr>
				<tr>
				<td valign="top" align="left">Attachment Notifictaion Type</td>
				<td valign="top">:</td>
				<td align="left" valign="top">
					<input type="radio" name="attachment_noti_type" id="attachment_noti_type" value="0" <?php if($addressBookUserData[0]['attachment_noti_type']==0){?> checked <?php } ?>/>Attachments &nbsp;&nbsp;&nbsp;
					<input type="radio" name="attachment_noti_type" id="attachment_noti_type" value="1" <?php if($addressBookUserData[0]['attachment_noti_type']==1){?> checked <?php } ?> />Link
				</td>
			</tr>            
			<tr>
				<td valign="top" align="left">&nbsp;</td>
				<td valign="top" align="left">&nbsp;</td>
				<td align="center">
					<input type="hidden" name="updateID" id="updateID" value="<?=$addressBookUserData[0]['id']?>" />
					<input type="hidden" name="updateType" id="updateType" value="<?=$_REQUEST["usertype"]?>" />
					<input type="button" name="button" class="green_small" id="button" value="Update" style="float:left;" onclick="updateAddressBookUser();" />
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>
