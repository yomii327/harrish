<?php session_start();

$builder_id=$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 
if(isset($_REQUEST["uniqueID"])){
	$fullName = trim(addslashes($_POST['fullName']));
	$userEmail = trim(addslashes($_POST['userEmail']));
	$companyName = trim(addslashes($_POST['companyName']));
	$userPhone = trim(addslashes($_POST['userPhone']));	
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

	$inssertQRY = "INSERT INTO pmb_address_book SET
		project_id = '".$_SESSION['idp']."',
		full_name = '".$fullName."',
		company_name = '".$companyName."',
		user_phone = '".$userPhone."',
		user_email = '".$userEmail."',
		activity = '".$activity."',		
		physical_address = '".$physicalAdd."',
		attachment_noti_type = '".$attachment_noti_type."',		
		last_modified_date = NOW(),
		last_modified_by = '".$builder_id."',
		created_date = NOW(),
		created_by = '".$builder_id."'";
	mysql_query($inssertQRY);
	$newUserId = mysql_insert_id();
	if($newUserId > 0){
		$outputArr = array('status'=> true, 'msg'=> 'Record added successfully', 'dataId'=> $newUserId, 'dataString'=> $fullName);
	}
	echo json_encode($outputArr);
}

if(isset($_REQUEST["name"])){?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Add contact detail</legend>
		<form name="addUserForm" id="addUserForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top" align="left">Full&nbsp;Name <span class="req">*</span></td>
				<td valign="top">:</td>
				<td align="left" valign="top">
					<input type="text" name="fullName" id="fullName" class="input_small" value="" />
					<lable for="multiUpload" id="errorFullName" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The person name field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Email Address<span class="req">*</span></td>
				<td valign="top">:</td>
				<td align="left" valign="top">
					<input type="text" name="userEmail" id="userEmail" class="input_small" value="" />
					<lable for="drawingRevision" id="errorUserEmail" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The email address field is required</div></lable>
					<lable for="drawingRevision" id="errorUserEmailValid" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The email address is not valid</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Company&nbsp;Name</td>
				<td valign="top">:</td>
				<td align="left" valign="top">
					<input type="text" name="companyName" id="companyName" class="input_small" value="" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Contact&nbsp;Number</td>
				<td valign="top">:</td>
				<td align="left" valign="top">
					<input type="text" name="userPhone" id="userPhone" class="input_small" value="" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Activity</td>
				<td valign="top">:</td>
				<td align="left" valign="top">
					<textarea name="activity" id="activity" class="text_area"></textarea>
					<br/>
					Please seperate location by semicolon(;).
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Physical&nbsp;address</td>
				<td valign="top">:</td>
				<td align="left" valign="top">
					<textarea name="physicalAdd" id="physicalAdd" class="text_area"></textarea>
				</td>
			</tr>
			
			<tr>
				<td valign="top" align="left">Attachment Notifictaion Type</td>
				<td valign="top">:</td>
				<td align="left" valign="top">
					<input type="radio" name="attachment_noti_type" id="attachment_noti_type" value="0"/>Attachments &nbsp;&nbsp;&nbsp;
					<input type="radio" name="attachment_noti_type" id="attachment_noti_type" value="1"/>Link
				</td>
			</tr>
			
			<tr>
				<td valign="top" align="left">&nbsp;</td>
				<td valign="top" align="left">&nbsp;</td>
				<td align="center">
					<input type="button" name="button" class="green_small" value="Submit" id="button" style="float:left;" onclick="saveAddressBookUser();" />
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>
