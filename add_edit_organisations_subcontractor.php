<?php session_start();
$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 
$builder_id = $_SESSION['ww_is_company'];
$userId = isset($_SESSION['ww_builder_id'])?$_SESSION['ww_builder_id']:0;

#Get Subcontractor database company details.
$subconCompanyId = $_SESSION['companyId'];
$getData = $obj->selQRYMultiple('*', 'organisations', 'is_deleted = 0 AND id IN('. $subconCompanyId .')');
$subconCompany = '';
if(!empty($getData)){	
	$subconCompany = $getData[0];
	#echo '<pre>';print_r($subconCompany);
}

// Add new record
if(isset($_POST["subcontractor_id"])){
	$trade = mysql_real_escape_string(trim(implode(',', $_POST['trade'])));
	$compliance = mysql_real_escape_string(trim($_POST['compliance']));
	$company_name=mysql_real_escape_string(trim($_POST['company_name']));
	$company_phone = mysql_real_escape_string(trim($_POST['company_phone']));
	$company_fax = mysql_real_escape_string(trim($_POST['company_fax']));	
	$contact_name=mysql_real_escape_string(trim($_POST['contact_name']));	
	$contact_position = mysql_real_escape_string(trim($_POST['contact_position']));
	$email_address = mysql_real_escape_string(trim($_POST['email_address']));
	$oldEmail = mysql_real_escape_string(trim($_POST['oldEmail']));
	$phone = mysql_real_escape_string(trim($_POST['phone']));
	$street_address = mysql_real_escape_string(trim($_POST['street_address']));
	$suburb = mysql_real_escape_string(trim($_POST['suburb']));
	$city = mysql_real_escape_string(trim($_POST['city']));
	$state = mysql_real_escape_string(trim($_POST['state']));
	$country = mysql_real_escape_string(trim($_POST['country']));
	$postcode = mysql_real_escape_string(trim($_POST['postcode']));
	$strategicAgreement = mysql_real_escape_string(trim($_POST['strategicAgreement']));	
	
	
	$creatdate = date('Y-m-d H:i:s');	

	if($_POST['subcontractor_id'] > 0) {
		$getData = $obj->selQRY("id", "organisations_subcontractor_database", "is_deleted = 0 AND company_name = '$company_name' AND contact_name = '$contact_name' AND id != ".$_POST["subcontractor_id"]);	
	} else {
		$getData = $obj->selQRY("id", "organisations_subcontractor_database", "is_deleted = 0 AND company_name = '$company_name' AND contact_name = '$contact_name' ");	
	}

	if($getData){
		$output = array('status'=> 'false', 'msg'=> 'This contact name is already associated with that company.');
	} else {
		$formQuery = "organisations_subcontractor_database SET
				trade = '".$trade."',
				compliance = '".$compliance."',
				company_name = '".$company_name."',
				company_phone = '".$company_phone."',
				company_fax = '".$company_fax."',
				contact_name = '".$contact_name."',	
				contact_position = '".$contact_position."',
				email_address = '".$email_address."',						
				phone = '".$phone."',
				street_address = '".$street_address."',
				suburb = '".$suburb."',
				city = '".$city."',
				state = '".$state."',
				postcode = '".$postcode."',
				country = '".$country."',
				strategic_agreement = '".$strategicAgreement."', ";
	
		if($_POST['subcontractor_id'] > 0) {
			$qData = "UPDATE ".$formQuery."
						last_modified_date = NOW(),
						last_modified_by = ".$userId." 
						WHERE 
						id = ". $_POST['subcontractor_id'];
			mysql_query($qData);
			if(mysql_affected_rows() > 0) {
				$result = true;
				$_SESSION['message'] = 'Subcontractor updated successfully.';
				$output = array('resId'=> $_POST['subcontractor_id'], 'status'=> 'success', 'msg'=> 'Subcontractor updated successfully!');
			}				
			$_SESSION['formId'] = $_POST['subcontractor_id'];
		} else {
			$qData = "INSERT INTO ".$formQuery."
							company_id = '".$_SESSION['companyId']."',
							created_date = NOW(),
							created_by = ".$userId;
			mysql_query($qData);
			$resId = mysql_insert_id();
			$_SESSION['message'] = 'Subcontractor added successfully.';
			$output = array('resId'=> $resId, 'status'=> 'success', 'msg'=> 'Subcontractor added successfully!');
			$_SESSION['formId'] = $resId;
		}
		
		# Start:- Email section
		 if($_SESSION['companyId'] == 3 && empty($oldEmail) && $oldEmail != $email_address){
			 
			require_once('includes/class.phpmailer.php');
			$mail = new PHPMailer(true);
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->SMTPSecure = 'tls'; //"ssl";	// sets the prefix to the servier
			$mail->Host = smtpHost; 	//pod51022.outlook.com      // sets GMAIL as the SMTP server
			$mail->SMTPDebug = 0;				// enables SMTP debug information (for testing)
			$mail->SMTPAuth = true;        		// enable SMTP authentication
			$smtpPort = smtpPort;
			if(!empty($smtpPort)){
				$mail->Port =  smtpPort; //587;
			}
			$mail->Username = smtpUsername; //"wiseworkingsales@gmail.com"; // SMTP account username
			$mail->Password = smtpPassword; //"Wiseworking123";   // SMTP account password

	
#			$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
	
#			$mail->AddReplyTo("WiseworkingSystems@wiseworking.com.au", "DefectID");
			$mail->SetFrom('WiseworkingSystems@wiseworking.com.au', "DefectID");
			
			$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
			$date = base64_encode(date('Y-m-d'));
			$link = $path.'/pms.php?sect=qwc_form&cId='.$_SESSION['companyId'].'&dmy='.$date.'&byEmail='.$_SESSION['formId'];
							
			$emailBody = "Hello, <br><br>
				<p>Congratulations, you have been added to the list of Subcontractors / Suppliers for Liberty builders. <br>
				To have you company approved for work please complete the following questionnaire by clicking the below link:</p> 
				<a href='{$link}' target='_blank' >{$link}</a>
				<br><br><br><br>
				Thanks,<br>
				Liberty Builders <br><br>";
				
			$mail->Subject = 'Subcontractors / Suppliers notification';
			$mail->IsHTML(true);
			
			$mail->MsgHTML($emailBody);			
	
			$mail->AddAddress($email_address, $contact_name); // To
			//$mail->AddCC('pat@libertybuilders.com.au');
			
			$mail->Send();
			$mail->ClearAddresses();
			$mail->ClearAllRecipients();
			//Send Mail End Here
		 }
		# End:- Email section		
		
	}	
	echo json_encode($output);die;
}

// Delete record by id
if($_GET['sect'] == 'delete_subcontractor') {
	if(isset($_REQUEST["formId"])){
		$outputArr = array('status'=> false, 'msg'=> 'Subcontractor not updated please try again');
		$insertQRY = "UPDATE organisations_subcontractor_database SET
							is_deleted = 1,
							last_modified_date = NOW(),
							last_modified_by = ".$userId."
						WHERE 
							id = ". $_REQUEST["formId"];
		mysql_query($insertQRY);

		if(mysql_affected_rows() > 0){
			$outputArr = array('status'=> true, 'msg'=> 'Subcontractor Deleted successfully!');
		}
		echo json_encode($outputArr); die;
	}
}

// Load HTML form 
if(isset($_REQUEST["name"])){
// get form data by id
$getData = $obj->selQRYMultiple('*', 'organisations_subcontractor_database', 'is_deleted = 0 AND id = '.$_REQUEST["formId"]);	
$formData = isset($getData[0])?$getData[0]:'';
	
// get form data by id
//	$checkList = $obj->selQRYMultiple("*", "organisations_subcontractor_checklist_items", "is_deleted = 0 AND type = 'subcontractor_supplier_sub_groups' AND company_id = ".$_SESSION['companyId']);
$_SESSION['formId'] = $_REQUEST["formId"];
?>
<style>
.input_small {
	padding: 0 5px 0 15px;
}
.innerDiv {
	float: left;
	border: 1px solid red;
	width: 120px;
	height: 120px;
	margin: 0px 0px 20px 3px;
}
label.filebutton {
	width: 120px;
	height: 40px;
	overflow: hidden;
	position: relative;
}
label input {
	cursor: pointer;
	font-size: 7px;
	left: -1px;
	line-height: 0;
	margin: 0;
	opacity: 0;
	padding: 0;
	position: absolute;
	top: -2px;
	z-index: 999;
	filter: alpha(opacity=0);
}

#inputBox{
	widht:95% !important;
}
</style>
<fieldset class="roundCorner">
  <legend style="color:#000000; font-weight: bold; "><?php echo ($_REQUEST["formId"]==0)?'Add New':'Edit'; ?> Subcontractor</legend>
    <div class="failure_r" id="messageDisplayDiv" style="display:none; float:left; width:500px; margin-left:20px; height:30px; margin-bottom:5px;">
        <p id="message"></p>
    </div>
  <form action="add_edit_organisations_subcontractor.php" method="post" name="editsubContForm" id="editsubContForm" enctype="multipart/form-data" >
    <table width="100%" border="0" align="left" cellpadding="0" cellspacing="7">
      <tr>
        <td valign="top">Company Name <span class="req">*</span></td>
        <td valign="top"><input name="company_name" type="text" class="input_small" id="company_name" value="<?php echo isset($subconCompany['company_name'])?$subconCompany['company_name']:''?>" readonly="readonly" /></td>        
        <td valign="top">Class <span class="req">*</span></td>
        <?php if($_SESSION['companyId'] == 3){
			$tradeArr = isset($formData['trade'])?explode(",", $formData['trade']):array();
			//$tradeArr = array_flip($tradeArr);
		?>
        <td valign="top"><select name="trade[]" id="trade" class="inputBox " style="margin-left:2px; height:60px;" multiple="multiple">
<option value="">Select</option>
		<?php  $classArr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I');
            foreach($classArr as $key=>$class){?>
                <option value="<?php echo $class; ?>" <?php if(in_array($class, $tradeArr)){ echo ' selected="selected"';}?>>
                <?php echo $class; ?>
                </option>
         <?php } ?>
          </select><br />
          (hold CRTL to select multiple)</td>
        <?php }else{ ?>          
        <td><input name="trade[]" type="text" class="input_small chzn-select" id="trade" value="<?php echo isset($formData['trade'])?$formData['trade']:''?>"/></td>
        <?php } ?>                  
          
       </tr>
      <tr valign="top" style="display:none1; /* This filed removed.*/">
        <td>Company Phone <span class="req">*</span></td>
        <td><input name="company_phone" type="text" class="input_small" id="company_phone" value="<?php echo isset($subconCompany['phone_number'])?$subconCompany['phone_number']:''?>" /></td>
        <td>Contact Name <span class="req">*</span></td>
        <td><input name="contact_name" type="text" class="input_small" id="contact_name" value="<?php echo isset($formData['contact_name'])?$formData['contact_name']:''?>" /></td>
      </tr>
      <tr valign="top">
		<td width="">Company Fax</td>
        <td><input name="company_fax" type="text" class="input_small" id="company_fax" value="<?php echo isset($subconCompany['primary_contact'])?$subconCompany['primary_contact']:''?>" /></td>        
        
        <td>Contact Position <span class="req">*</span></td>
        <td><input name="contact_position" type="text" class="input_small" id="contact_position" value="<?php echo isset($formData['contact_position'])?$formData['contact_position']:''?>" /></td>
      </tr>
      <tr valign="top">
        <td>Email Address <span class="req">*</span></td>
        <td><input name="email_address" type="text" class="input_small" id="email_address" value="<?php echo isset($formData['email_address'])?$formData['email_address']:''?>" />
	        <input name="oldEmail" type="hidden" class="input_small" id="oldEmail" value="<?php echo isset($formData['email_address'])?$formData['email_address']:''?>" />
        </td>      
        <td width="">Phone</td>
        <td width=""><input name="phone" type="text" class="input_small" id="phone" value="<?php echo isset($formData['phone'])?$formData['phone']:''?>" /></td>
      </tr>
      <tr valign="top">
        <td width="">Street Address</td>
        <td width=""><input name="street_address" type="text" class="input_small" id="street_address" value="<?php echo isset($formData['street_address'])?$formData['street_address']:''?>" /></td>
        <td width="">Suburb</td>
        <td width=""><input name="suburb" type="text" class="input_small" id="suburb" value="<?php echo isset($formData['suburb'])?$formData['suburb']:''?>" /></td>
      </tr>
      <tr valign="top">
        <td width="">City</td>
        <td width=""><input name="city" type="text" class="input_small" id="city" value="<?php echo isset($formData['city'])?$formData['city']:''?>" /></td>
        <td width="">Postcode <span class="req">*</span></td>
        <td width=""><input name="postcode" type="text" class="input_small" id="postcode" value="<?php echo isset($formData['postcode'])?$formData['postcode']:''?>" /></td>
      </tr>
      <tr valign="top">
        <td nowrap="nowrap">State</td>
        <td><select name="state" id="state" class="select_box" style="margin-left:2px;">
          <?php $stateArr = array("VIC", "NSW", "WA", "QLD", "ACT", "TAS");
				$state = isset($formData['state'])?$formData['state']:'';
				foreach($stateArr as $val){
					$selected = ($state == $val)?' selected="selected"':'';
					echo '<option value="'.$val.'" '.$selected.' >'.$val.'</option>';
				}
            ?>
        </select></td>
        <td>Country</td>
        <td><select name="country" id="country" class="select_box" style="margin-left:2px;">
          <option value="Australia" <?php if(isset($formData['country']) && $formData['country'] == 'Australia'){ echo 'selected';}?>>Australia</option>
          <option value="Singapore" <?php if(isset($formData['country']) && $formData['country'] == 'Singapore'){ echo 'selected';}?>>Singapore</option>
        </select></td>
      </tr>
      <tr valign="top">
        <td nowrap="nowrap">Strategic Agreement</td>
        <td><select name="strategicAgreement" id="strategicAgreement" class="select_box" style="margin-left:0px;">
            <option value="">Select</option>
	<?php $strategicArr = array('Yes', 'No');
        foreach($strategicArr as $key=>$strategic){?>
            <option value="<?=$strategic?>" <?php if(isset($formData['strategic_agreement']) && $formData['strategic_agreement'] == $strategic){ echo ' selected="selected"';}?>>
            <?=$strategic?>
            </option>
         <?php } ?>
          </select></td>
        <td nowrap="">Compliance</td>
        <td><select name="compliance" id="compliance" class="select_box" style="margin-left:0px; <?php  echo ($_SESSION['companyId'] == 3)?'display:none;':'';?>">
            <option value="">Select</option>
	<?php $complianceArr = array('Yes', 'No');
        foreach($complianceArr as $key=>$complianceType){?>
            <option value="<?=$complianceType?>" <?php if(isset($formData['compliance']) && $formData['compliance'] == $complianceType){ echo ' selected="selected"';}?>>
            <?=$complianceType?>
            </option>
         <?php } ?>
          </select>
          <a href="javascript:void(0);" id="loadChecklist" style="float:left; margin:0; <?php  echo ($_SESSION['companyId'] != 3)?'display:none;':'';?>" class="actionButton green_small">Checklist </a></td>
      </tr>
      <tr>
        <td colspan="4" style="border-bottom:1px solid #999999;" >&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td colspan="3"><input type="hidden" value="add_edit_subcontractor" name="sect" id="sect" />
          <input type="hidden" value="<?php echo isset($formData['id'])?$formData['id']:0?>" name="subcontractor_id"  id="subContId"  />
          <input name="bakcBtn" type="button" class="green_small actionButton" value="Back" onclick="closePopup_gs(300, 1); closePopup_gs(300, 2); closePopup_gs(300, 3);" style=" float:left; margin-right:15px;margin-left: 34%;">           
          <input name="button" id="submitBtnSubconForm" type="submit" class="green_small actionButton" value="<?php echo ($_REQUEST["formId"]==0)?'Submit':'Update'; ?>"  style=" float:left;">
          <img id="loadBtnSubconForm" align="left" alt="View Image" src="images/preload.gif" style="display:none;">
          
		</td>
      </tr>
    </table>
  </form>
  <br clear="all" />
</fieldset>
<?php } ?>
