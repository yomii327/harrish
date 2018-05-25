<?php ob_start();
require_once'includes/functions.php';
include_once("includes/commanfunction.php");
$object = new COMMAN_Class();
if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){
?>
	<script language="javascript" type="text/javascript">
		window.location.href="<?=HOME_SCREEN?>";
	</script>
<?php } ?>
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/c_manager_edit.js"></script>
<script language="javascript" type="text/javascript">
function startAjax(type,val,result){//alert(val);
	AjaxShow("POST","ajaxFunctions.php?type=1 && "+type+"="+val,result);
}
$(document).ready(function() {
	var validator = $("#managerEdit").validate({
		rules:{ 
		   fullname:{
				required: true
		   },
		   'compname[]':{
				required: true
		   },
		   username:{
				required: true,
				minlength:4,
				maxlength:12
		   },
		   memail:{
				required: true,
				email:true
		   },
		   mobile:{
				required: true,
				digits: true,
				minlength:10
		   },
		   pwd:{
				required: true,
				minlength:6,
				maxlength:12			
		   },
		   rePwd:{
		   		required: true,
				equalTo: "#pwd"			
		   },
		},
		messages:{
			fullname:{
				required: '<div class="error-edit-profile">The full name field is required</div>',
				email: '<div class="error-edit-profile">The email is not valid format</div>'
			},
			'compname[]':{
				required: '<div class="error-edit-profile">The company name field is required</div>'
			},
			username:{
				required: '<div class="error-edit-profile">The username field is required</div>',
				minlength: '<div class="error-edit-profile">Please enter at least 4 characters</div>',
				maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'
			},
			memail:{
				required: '<div class="error-edit-profile">The email field is required</div>',
				email: '<div class="error-edit-profile">Invalide email format</div>'
			},	
			mobile:{
				required: '<div class="error-edit-profile">The mobile field is required</div>',
				digits: '<div class="error-edit-profile">Please enter only digits.</div>',
				minlength: '<div class="error-edit-profile">Please enter at least 10 digits</div>'
			},			
			pwd:{
				required: '<div class="error-edit-profile">The password field is required</div>',
				minlength: '<div class="error-edit-profile">Please enter at least 6 characters</div>',
				maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'
			},
			rePwd:{
				required: '<div class="error-edit-profile">The re password field is required</div>',
				equalTo: '<div class="error-edit-profile">The passwords you entered do not match. Please try again.</div>',
			},
			debug:true
		}
	});
	jQuery.validator.addMethod("alpha", function( value, element ) {
		return this.optional(element) || /^[a-zA-Z ]+$/.test(value);
	}, "Please use only alphabets (a-z or A-Z).");
	jQuery.validator.addMethod("numeric", function( value, element ) {
		return this.optional(element) || /^[0-9]+$/.test(value);
	}, "Please use only numeric values (0-9).");
	jQuery.validator.addMethod("alphanumeric", function( value, element ) {
		return this.optional(element) || /^[a-z A-Z0-9]+$/.test(value);
	}, "You can use only a-z A-Z 0-9 characters.");
	jQuery.validator.addMethod("mobile", function( value, element ) {
		return this.optional(element) || /^[ 0-9+-]+$/.test(value);
	}, "You can use only 0-9 - + characters.");
	jQuery.validator.addMethod("login", function( value, element ) {
		return this.optional(element) || /^[A-Za-z0-9_.]+$/.test(value);
	}, "You can use only a-z A-Z 0-9 _ and . characters.");
});
</script>
<?php 
if(isset($_POST['update_x'])){
	$emailReceive = 0;
	if($_POST['userType'] != 'inspector'){
		$emailReceive = isset($_POST['emailReceive']) ? 1 : 0; 
	}
	if($_POST['userType'] != 'inspector'){
		$addProj = isset($_POST['addProject']) ? 1 : 0; 
	}
	$companyId = implode(', ', $_POST['compname']);
	$getCompanyName = $object->selQRYMultiple("group_concat(company_name SEPARATOR ', ') AS compname", 'organisations', 'is_deleted = 0 AND id IN('.$companyId.') GROUP BY is_deleted ');
	$update = "UPDATE user SET 
						user_name='".addslashes(trim($_POST['username']))."',
						user_fullname='".addslashes(trim($_POST['fullname']))."',
						company_id='".addslashes(trim($companyId))."',
						company_name='".addslashes(trim($getCompanyName[0]['compname']))."',
						user_email='".addslashes(trim($_POST['memail']))."',
						user_phone_no='".addslashes(trim($_POST['mobile']))."',
						user_password='".md5(trim($_POST['pwd']))."',
						user_plainpassword='".addslashes(trim($_POST['pwd']))."',
						user_type='".addslashes(trim($_POST['userType']))."',
						last_modified_date = NOW(),
						last_modified_by = ".base64_decode($_POST['b_id']).",
						is_deleted='".trim($_POST['status'])."',
						recieve_email = '".addslashes(trim($emailReceive))."'
					WHERE user_id='".base64_decode($_POST['b_id'])."'";
	#echo $update;die;
	mysql_query($update);
	#echo $_POST['userType'];die;
	//if($_POST['userType'] == 'manager'){
		$permissionQry_porj = "UPDATE user_permission SET is_allow = '".$addProj."', last_modified_date = NOW(), last_modified_by = ".base64_decode($_POST['b_id'])." WHERE user_id = '".base64_decode($_POST['b_id'])."' AND permission_name = 'web_add_project'";
		mysql_query($permissionQry_porj);

		if(isset($_POST['messageBoard'])){
			$permissionQry1 = "UPDATE user_permission SET is_allow = '".$_POST['messageBoard']."', last_modified_date = NOW(), last_modified_by = ".base64_decode($_POST['b_id'])." WHERE user_id = '".base64_decode($_POST['b_id'])."' AND permission_name = 'web_message_board'";
			mysql_query($permissionQry1);
		}else{
			$permissionQry1 = "UPDATE user_permission SET is_allow = 0, last_modified_date = NOW(), last_modified_by = ".base64_decode($_POST['b_id'])." WHERE user_id = '".base64_decode($_POST['b_id'])."' AND permission_name = 'web_message_board'";
			mysql_query($permissionQry1);
		}
		if(isset($_POST['menuProgressMonitoring'])){
			$permissionQry1 = "UPDATE user_permission SET is_allow = '".$_POST['menuProgressMonitoring']."', last_modified_date = NOW(), last_modified_by = ".base64_decode($_POST['b_id'])." WHERE user_id = '".base64_decode($_POST['b_id'])."' AND permission_name = 'web_menu_progress_monitoring'";
			mysql_query($permissionQry1);
		}else{
			$permissionQry1 = "UPDATE user_permission SET is_allow = 0, last_modified_date = NOW(), last_modified_by = ".base64_decode($_POST['b_id'])." WHERE user_id = '".base64_decode($_POST['b_id'])."' AND permission_name = 'web_menu_progress_monitoring'";
			mysql_query($permissionQry1);
		}
		if(isset($_POST['menuQualityChecklist'])){
			$permissionQry1 = "UPDATE user_permission SET is_allow = '".$_POST['menuQualityChecklist']."', last_modified_date = NOW(), last_modified_by = ".base64_decode($_POST['b_id'])." WHERE user_id = '".base64_decode($_POST['b_id'])."' AND permission_name = 'web_menu_quality_checklist'";
			mysql_query($permissionQry1);
		}else{
			$permissionQry1 = "UPDATE user_permission SET is_allow = 0, last_modified_date = NOW(), last_modified_by = ".base64_decode($_POST['b_id'])." WHERE user_id = '".base64_decode($_POST['b_id'])."' AND permission_name = 'web_menu_quality_checklist'";
			mysql_query($permissionQry1);
		}
	//}

	if ($_POST["oldUserType"] != $_POST['userType']){
		$userType = $_POST['userType'];
		//update permissions according to new user type
		if($userType == 'manager'){
			////// Update user level and project level permissions
			$keyManagerPermissionArray = array_keys($managerPermissionArray);
			$projectWisePermissions = array('web_edit_inspection','web_delete_inspection','web_close_inspection','iPad_add_inspection','iPad_edit_inspection','iPad_delete_inspection','iPad_close_inspection','iPhone_add_inspection','iPhone_close_inspection', 'web_add_project');
			
			for($i=0;$i<sizeof($managerPermissionArray);$i++){
				if(in_array($keyManagerPermissionArray[$i], $projectWisePermissions)){
				}else{
					$permissionQry = "UPDATE user_permission SET is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."', last_modified_date = NOW(), last_modified_by = ".base64_decode($_POST['b_id'])." WHERE user_id = '".base64_decode($_POST['b_id'])."' AND permission_name = '".$keyManagerPermissionArray[$i]."'";
					mysql_query($permissionQry);
				}
			}
		}elseif($userType == 'inspector'){
			////// Update user level and project level permissions
			$keyManagerPermissionArray = array_keys($inspectorPermissionArray);
		
			$projectWisePermissions = array('web_edit_inspection','web_delete_inspection','web_close_inspection','iPad_add_inspection','iPad_edit_inspection','iPad_delete_inspection','iPad_close_inspection','iPhone_add_inspection','iPhone_close_inspection');
		
			for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
				if(in_array($keyManagerPermissionArray[$i], $projectWisePermissions)){
				}else{
					$permissionQry = "UPDATE user_permission SET is_allow = '".$inspectorPermissionArray[$keyManagerPermissionArray[$i]]."', last_modified_date = NOW(), last_modified_by = ".base64_decode($_POST['b_id'])." WHERE user_id = '".base64_decode($_POST['b_id'])."' AND permission_name = '".$keyManagerPermissionArray[$i]."'";
					mysql_query($permissionQry);
				}
			}
		}
	}
	$_SESSION['user_update']='User updated successfully.';
	//header('location:?sect=c_builder');
	echo '<script>window.location.href="?sect=c_builder"</script>';
}

if(isset($_GET['deleteId'])){
	$update="update user set is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".base64_decode($_GET['deleteId'])." WHERE user_id='".base64_decode($_GET['deleteId'])."'";
	
	$update_up="update user_projects set is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".base64_decode($_GET['deleteId'])." WHERE user_id='".base64_decode($_GET['deleteId'])."'";
	
	mysql_query($update);
	mysql_query($update_up);

	$_SESSION['user_remove'] = 'User removed successfully.';
	header('location:?sect=c_builder');
}

$b_id = base64_decode($_GET['id']);

//$q = "SELECT is_allow FROM user_permission WHERE user_id='".$b_id."' AND permission_name='web_add_project' and is_deleted = 0";
$q = "SELECT permission_name,is_allow FROM user_permission WHERE user_id='".$b_id."' AND is_deleted = 0";
$r = $obj->db_query($q);
$perArray = array();
while ($row=$obj->db_fetch_assoc($r)) {
	$perArray[$row["permission_name"]] = $row['is_allow'];
}
// echo "<pre>"; print_r($perArray['web_add_project']); die;
// $temp = $obj->db_fetch_assoc($r);
// $addPerm = $temp['is_allow'];

$q = "SELECT * FROM user WHERE user_id='".$b_id."' and is_deleted IN (0,2)";
$r=$obj->db_query($q);
$f=$obj->db_fetch_assoc($r);
if(empty($f)){?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php }

?>
<div id="middle" style="padding-bottom:80px;">
	<div id="apply_now">
	<form method="post" enctype="multipart/form-data" name="managerEdit" id="managerEdit">
		<div class="content_container">
			<div class="content_left">
				<div class="content_hd1" style="background-image:url(images/builder_account_info_hd.png); width:550px;margin-top:-50px\9;"></div>
				<div class="signin_form">
					<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
						<tr>
							<td colspan="2"><strong>Personal Information</strong></td>
						</tr>
						<tr>
							<td valign="top">User Type</td>
						 	<td valign="top">
								<!--<input  type="text" class="input_small" readonly="readonly" value="<?=stripslashes($f['user_type'])?>" name="userType" id="userType" />-->
								<input id="oldUserType" name="oldUserType" type="hidden" value="<?=stripslashes($f['user_type'])?>"/>
								<select name="userType" id="userType" class="select_box" style="margin-left:0px;" >
									<option value="manager" <?php if(stripslashes($f['user_type']) == 'manager'){echo 'selected="selected"';}?> >Manager</option>
									<option value="inspector" <?php if(stripslashes($f['user_type']) == 'inspector'){echo 'selected="selected"';}?> >Inspector</option>
								</select>
							</td>
						</tr>
						<tr>
							<td valign="top">Full Name <span class="req">*</span></td>
							<td valign="top">
								<input  type="text" class="input_small" value="<?=stripslashes($f['user_fullname'])?>" name="fullname" id="fullname" />
							</td>
						</tr>
						<tr>
		                    <td valign="top" nowrap="nowrap">Company Name <span class="req">*</span></td>
		                    <td valign="top">
		                     <?php $compId = explode(', ', $f['company_id']);
		                    $getCompanyData = $object->selQRYMultiple('id, company_name', 'organisations', 'is_deleted = 0 '); ?>
		                        <select name="compname[]" id="compname" class="select_box" style="margin-left:0px; width:289px;height:76px;background: url(images/text_detail.png);" multiple="multiple" >
		                            <!--option value="">Select</option-->
		                	<?php if(isset($getCompanyData)){ 
		                            foreach($getCompanyData as $company){	?>
		                                <option value="<?=$company['id']?>"
											<?php if(in_array($company['id'], $compId)){ ?>
													selected="selected"
											<?php } ?> >
										<?=$company['company_name']?></option>
		                    <?php 	}
		                        }
							?>
		                        </select></td>
		                  </tr>
						<tr>
							<td valign="top" nowrap="nowrap">Username<span class="req">*</span></td>
							<td valign="top">
								<input  type="text" class="input_small" readonly="readonly" value="<?=stripslashes($f['user_name'])?>" name="username" id="username"/>
							</td>
						</tr>
						
					</table>
				</div>
			</div>
			<div class="content_right">
				<div class="signin_form1" style="margin-top:112px;margin-top:117px\9;">
					<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
						<tr>
							<td valign="top">Email <span class="req">*</span></td>
							<td valign="top"><input  type="text" class="input_small" value="<?=stripslashes($f['user_email'])?>"  name="memail" id="memail"/></td>
						</tr>
						<tr>
							<td valign="top">Mobile <span class="req">*</span></td>
							<td valign="top"><input  type="text" class="input_small" value="<?=stripslashes($f['user_phone_no'])?>" name="mobile" id="mobile" /></td>
						</tr>
						<tr>
							<td valign="top">Password <span class="req">*</span></td>
							<td valign="top"><input type="password" class="input_small" value="<?=stripslashes($f['user_plainpassword'])?>"  name="pwd" id="pwd"/></td>
						</tr>
						<tr>
							<td valign="top">Re Password <span class="req">*</span></td>
							<td valign="top"><input type="password" class="input_small" value="<?=stripslashes($f['user_plainpassword'])?>"  name="rePwd" id="rePwd"/></td>
						</tr>
						
						<tr>
							<td valign="top" nowrap="nowrap">User Status </td>
							<td valign="top">
								<select name="status" id="status" class="input_small">
									<option <?php echo (isset($f['is_deleted']) && $f['is_deleted']==0)?'selected="selected"':'';?> value="0">Active</option>
									<option <?php echo (isset($f['is_deleted']) && $f['is_deleted']==2)?'selected="selected"':'';?> value="2">Deactive</option>
								</select>
							</td>
						</tr>

					</table>
				</div>
			</div>
			<table width="940" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td colspan="2"><strong>Permission</strong></td>
				</tr>
				<tr>

					<td align="left"><input type="checkbox" name="addProject" id="addProject" value="1" checked="checked" />User can add projects</td>
					<!-- <td style="width: 120px">User can add projects</td> -->

              		<span id="emailReceiveRow" class="tabPermission" <?php if(stripslashes($f['user_type']) == 'inspector'){echo 'style="display:none;"';}?>>
	              		<td align="left">
		                	<input type="checkbox" name="emailReceive" id="emailReceive" value="1" <?php if($f['recieve_email'] == 1){ echo 'checked="checked"';}?> />
		                	Receive Email
		                </td>	
		                <!-- <td  style="width: 90px;">Receive Email</td> -->
					</span>

					<span id="addProjRow" <?php if(stripslashes($f['user_type']) == 'inspector'){echo 'style="display:none;"';}?>>
						<td align="left">
							<input type="checkbox" name="messageBoard" id="messageBoard" value="1" <?php if($perArray['web_message_board'] == 1){ echo 'checked="checked"';}?> />
							Message Board
						</td>
						<!-- <td>Message Board</td> -->
					</span>	

					<span id="addProjRow" <?php if(stripslashes($f['user_type']) == 'inspector'){echo 'style="display:none;"';}?>>
						<td align="left">
							<input type="checkbox" name="menuProgressMonitoring" id="menuProgressMonitoring" value="1" <?php if($perArray['web_menu_progress_monitoring'] == 1){ echo 'checked="checked"';}?> />
							Progress Monitoring
						</td>
						<!-- <td>Progress Monitoring</td> -->
					</span>

					<span id="addProjRow" <?php if(stripslashes($f['user_type']) == 'inspector'){echo 'style="display:none;"';}?>>
						<td align="left">
							<input type="checkbox" name="menuQualityChecklist" id="menuQualityChecklist" value="1" <?php if($perArray['web_menu_quality_checklist'] == 1){ echo 'checked="checked"';}?> />
							Quality Checklist
						</td>
						<!-- <td>Quality Checklist</td> -->
					</span>
				</tr>

				<tr>
					<td colspan="2" align="right">
						<input type="hidden" value="remove_builder" name="sect" id="sect" />
						<input type="hidden" value="<?=base64_encode($f['user_id'])?>" name="b_id" id="b_id" />
						<input type="submit"  value="Update" name="update_x" id="update" style="width:111px; height:45px; border:none; background:url(images/update_btn.png);text-indent: -9999px; cursor: pointer;" />
					</td>
					<td style="width: 10px">
						<input type="button" class="submit_btn" id="remove" name="remove" onclick="deletechecked('?sect=c_remove_builder&deleteId=<?=base64_encode($f['user_id'])?>')" style="border:none;background-image:url('images/remove_btn.png');color:transparent;" />
					</td>
					<td colspan="2" align="left">
						<a href="javascript:history.back();"><img src="images/back_btn.png" style="border:none; width:111px;" /></a>
					</td>
				</tr>
			</table>
		</div>
	</form>
	</div>
</div>
<script type="text/javascript">
function deletechecked(redirectURL){
	var r = jConfirm('Do you want to Delete this user ?', null, function(r){ if(r==true){ window.location = redirectURL; } });
}

$('#userType').change(function(){
	/*if($(this).val() == 'manager'){
		$('#addProjRow').show();
	}else{
		$('#addProjRow').hide();
	}*/
	if($(this).val() == 'manager'){
		$('#addProjRow, #emailReceiveRow').show();
	}else{
		$('#addProjRow, #emailReceiveRow').hide();
	}
});
</script>
