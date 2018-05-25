<?php session_start();
$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 
$builder_id = $_SESSION['ww_is_company'];

// Add new record
if(isset($_REQUEST["antiqueID"])){
	$subcon_database = 'No';
	if(isset($_POST['subcontractor_database']) && $_POST['subcontractor_database'] == 'Yes'){
		$subcon_database = $_POST['subcontractor_database'];
	}
	$comanQuery = "company_name = '".addslashes(trim($_POST['company_name']))."',
		address = '".addslashes(trim($_POST['address']))."',
		phone_number = '".addslashes(trim($_POST['phone']))."',
		primary_contact = '".addslashes(trim($_POST['primary_contact']))."',
		website_url = '".addslashes(trim($_POST['website_url']))."',
		logo = '".addslashes(trim($_POST['logo']))."',
		subcontractor_database = '". $subcon_database ."',
		last_modified_date = NOW(),
		last_modified_by = ".$builder_id;
	
	if($_POST['id']==0){
		$outputArr = array('status'=> false, 'msg'=> 'User not added please try again');
		$insertQRY = "INSERT INTO organisations SET
					created_date = NOW(),
					created_by = ".$builder_id.",".$comanQuery;
		$query = mysql_query($insertQRY);
		$last_id = mysql_insert_id();

		if(mysql_affected_rows() > 0 && $last_id != ''){
			$insertQRY = "INSERT INTO organisations_theme_settings SET
					company_id = ".$last_id.",
					header_bg_color = 'cccccc,cccccc,cccccc',
					header_text_color = 'ffffff',
					navigation_bg_color = 'fcd43a,daaf0a',
					button_bg_colour = '98d207,5c8904',
					header_font_size = '15',
					button_font_size = '15',
					button_text_colour = 'FFF',
					created_date = NOW(),
					created_by = ".$builder_id."";
			$query = mysql_query($insertQRY);
			$outputArr = array('status'=> true, 'msg'=> 'Record added successfully!');
		}			
		
	}elseif($_POST['id']>0){
		$outputArr = array('status'=> false, 'msg'=> 'User not updated please try again');
		$updateQRY = "UPDATE organisations SET ".$comanQuery."
					WHERE id = ". $_POST['id'];
		mysql_query($updateQRY);
		if(mysql_affected_rows() > 0){
			$outputArr = array('status'=> true, 'msg'=> 'User updated successfully!');
		}
	}
	echo json_encode($outputArr);					
}

// Delete record by id
if(isset($_REQUEST["deleteID"])){
	$outputArr = array('status'=> false, 'msg'=> 'User not updated please try again');
	
	$insertQRY = "UPDATE organisations SET
						is_deleted = 1,
						last_modified_date = NOW(),
						last_modified_by = ".$builder_id."
					WHERE 
						id = ". $_POST['formId'];
	mysql_query($insertQRY);

	if(mysql_affected_rows() > 0){
		$outputArr = array('status'=> true, 'msg'=> 'User Deleted successfully!');
	}
	echo json_encode($outputArr); die;
}

// Load HTML form 
if(isset($_REQUEST["name"])){
	// get form data by id
	$getData = $obj->selQRYMultiple('*', 'organisations', 'is_deleted = 0 AND id = '.$_REQUEST["formId"]);	
	$formData = isset($getData[0])?$getData[0]:'';
?>
<style>
.input_small{ padding: 0 5px 0 15px; }
.innerDiv{ float:left; border:1px solid red; width:120px; height:120px; margin:0px 0px 20px 3px; }
label.filebutton{ width:120px; height:40px; overflow:hidden; position:relative; }
label input { cursor: pointer; font-size: 7px; left: -1px; line-height: 0; margin: 0; opacity: 0; padding: 0; position: absolute; top: -2px; z-index: 999; filter:alpha(opacity=0);}
</style>
<fieldset class="roundCorner">
	<legend style="color:#000000; font-weight: bold;"><?php echo ($_REQUEST["formId"]==0)?'Add':'Edit'; ?> Company</legend>
	<form name="submitForm" id="submitForm">
	<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
		<tr>
			<td valign="top" align="left" width="50%">Company Name<span class="req">*</span></td>
			<td align="left">
				<input name="company_name" type="text" class="input_small" id="company_name"  value="<?php echo isset($formData['company_name'])?$formData['company_name']:''?>" />
				<lable for="contact_name" id="errorCompanyName" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Company Name field is required</div></lable>
			</td>
		</tr>
		<tr>
			<td valign="top" align="left">Address</td>
			<td align="left">
				<textarea name="address" id="address" class="text_area"><?php echo isset($formData['address'])?$formData['address']:''?></textarea>
			</td>
		</tr>        
		<tr>
			<td valign="top" align="left">Phone Number</td>
			<td align="left">
				<input name="phone" type="text" class="input_small" id="phone" value="<?php echo isset($formData['phone_number'])?$formData['phone_number']:''?>"/>
				<lable for="contact_name" id="errorPhone" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Phone number field should be number only.</div></lable>
			</td>
		</tr>  
		<tr>
			<td valign="top" align="left">Primary Contact</td>
			<td align="left">
				<input name="primary_contact" type="text" class="input_small" id="primary_contact" value="<?php echo isset($formData['primary_contact'])?$formData['primary_contact']:''?>"/>
				<lable for="primary_contact" id="errorPhoneContact" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Primary contact field should be number only.</div></lable>
			</td>
		</tr>                
		<tr>
			<td valign="top" align="left">Logo</td>
			<td align="left" width="50%">
	            <div class="innerDiv"  align="center" >
                    <div style="height:120px;overflow:hidden;">
                      <label class="filebutton" align="center"> &nbsp;Browse...
                        <input type="file" id="image1" name="image1" style="width:120px;height:120px;" />
                      </label>
                        <div style="width:120px;" id="response_image_1">
                        <?php if(isset($formData['logo']) && !empty($formData['logo'])){?>
                            <img width="100" height="90" id="photoImage1" style="margin-left:2px;margin-top:8px;" src="company_logo/<?php echo $formData['logo']; ?>?">
                        <?php } ?>
                        <input type="hidden" value="<?php echo $formData['logo']; ?>" name="logo">
                        </div>                      
                    </div>
                    <!--img id="removeImg1" src="images/remove_img_btn.png" style="margin-top:10px;cursor:pointer;display:none;" onclick="removeImages('response_image_1', this.id);" /--> 
                  </div>
			</td>
		</tr>
		<tr>
			<td valign="top" align="left">Website URL</td>
			<td align="left" width="50%">
				<input name="website_url" type="text" class="input_small" id="website_url"  value="<?php echo isset($formData['website_url'])?$formData['website_url']:''?>" />
			</td>
		</tr>
		<tr>
			<td valign="top" align="left">Subcontractor database</td>
			<td align="left" width="50%">
				<input name="subcontractor_database" type="checkbox" id="subcontractor_database"  value="Yes" <?php if(isset($formData['subcontractor_database']) && $formData['subcontractor_database'] == 'Yes'){ echo 'checked="checked"'; }?> />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
            <input type="hidden" name="id" id="id" value="<?php echo isset($formData['id'])?$formData['id']:0; ?>" />
			<input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/save.png);font-size:0px; border:none; width:111px;float:left;" onclick="addEditRecordData();" />
				&nbsp;&nbsp;&nbsp; <a id="ancor" href="javascript:closePopup(300);" onclick="yes"> <img src="images/back_btn.png" style="border:none; width:111px;" /> </a>
			</td>
		</tr>
	</table>
	</form>
	<br clear="all" />
</fieldset>
<?php } ?>
