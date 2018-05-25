<?php
require_once'includes/functions.php';
include_once("includes/commanfunction.php");
$object = new COMMAN_Class();

if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php } ?>

<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script language="javascript" type="text/javascript">
function startAjax(type,val,result){//alert(val);
	AjaxShow("POST","ajaxFunctions.php?type=1 && "+type+"="+val,result);
}

$(document).ready(function() {
	var validator = $("#CreatBuild").validate({
	rules:
	{ 
	   fname:
	   {
	   		required: true
	   },
	   'compname[]':
	   {
	   		required: true
	   },
	   username:
	   {
	   		required: true,
			minlength:4,
			//maxlength:12
	   },
	   email:
	   {
	   		required: true,
			email:true
	   },
	   mobile:
	   {
	   		required: true,
			digits: true,
			minlength:10
	   },
	   password:
	   {
	   		required: true,
			minlength:6,
			maxlength:12			
	   },
	    rePassword:
	   {
   	   		required: true,
	   		equalTo: "#password"			
	   },
	},
	messages:
	{
		fname:
		{
			required: '<div class="error-edit-profile">The full name field is required</div>',
			email: '<div class="error-edit-profile">The email is not valid format</div>'
			
		},
		'compname[]':
		{
			required: '<div class="error-edit-profile">The company name field is required</div>'
			
		},
		username:
		{
			required: '<div class="error-edit-profile">The username field is required</div>',
			minlength: '<div class="error-edit-profile">Please enter at least 4 characters</div>'
			//maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'
		},
		email:
		{
			required: '<div class="error-edit-profile">The email field is required</div>',
			email: '<div class="error-edit-profile">Invalide email format</div>'
		},	
		mobile:
		{
			required: '<div class="error-edit-profile">The mobile field is required</div>',
			digits: '<div class="error-edit-profile">Please enter only digits.</div>',
			minlength: '<div class="error-edit-profile">Please enter at least 10 digits</div>'
			
		},			
		password:
		{
			required: '<div class="error-edit-profile">The password field is required</div>',
			minlength: '<div class="error-edit-profile">Please enter at least 6 characters</div>',
			maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'
		},
		rePassword:
		{
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
// JavaScript Document
</script>

<script language="javascript" type="text/javascript">
/*function startAjax(){
	var fname=document.getElementById('fname').value;
	var compname=document.getElementById('compname').value;
	var email=document.getElementById('email').value;
	var mobile=document.getElementById('mobile').value;
	var username=document.getElementById('username').value;
	var password=document.getElementById('password').value;
	
	
	if(fname!='' && compname!='' && email!='' && mobile!='' && username!='' && password!=''){
		document.getElementById('apply_now_process').style.visibility = 'visible';
		document.getElementById('apply_now_response').style.visibility = 'hidden';
		return true;
	}else{
		var err = '<span class="emsg">* represent required fileds!<\/span><br/><br/>';
		document.getElementById('apply_now_response').innerHTML = err;
		return false;
	}
	
	document.getElementById('apply_now_process').style.visibility = 'visible';
	document.getElementById('apply_now_response').style.visibility = 'hidden';
	return true;
}
*/
function stopAjax(success){
	var result = '';
	
	if(success == 1){
		result = '<span class="emsg">* represent required fileds!<\/span><br/><br/>';
	}else if(success == 2){
		result = '<span class="emsg">Invalid email address!<\/span><br/><br/>';
	}else if(success == 3){
		result = '<span class="emsg">Invalid mobile no.!<\/span><br/><br/>';
	}else if(success == 4){
		result = '<span class="emsg">Password must be greater than 6 characters!<\/span><br/><br/>';
	}else if(success == 5){
		result = '<span class="emsg">Username already exist!<\/span><br/><br/>';
	}else if(success == 6){
		result = '<span class="emsg">Email id already exist!<\/span><br/><br/>';
	}else if(success == 7){
		result = '<p><span class="msg">Manager created successfully!<\/span><br/><br/><\/p>';
		/*document.getElementById('apply_now').style.display = 'none';
		document.getElementById('request_send').style.display = 'block';*/
	}
	document.getElementById('apply_now_process').style.visibility = 'hidden';
	document.getElementById('apply_now_response').innerHTML = result;
	document.getElementById('apply_now_response').style.visibility = 'visible';
	
	return true;
}
</script>
<!-- Ajax Post -->
<link href="../style.css" rel="stylesheet" type="text/css" />
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form action="wwConroller.php" method="post" id="CreatBuild"  enctype="multipart/form-data">
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/create_builder_hd.png);margin-top:0px\9;"></div>
					<div class="signin_form">
						<table width="450" border="0" align="left" cellpadding="0" cellspacing="15">
							<tr>
								<td colspan="2"><strong>Personal Information</strong></td>
							</tr>
							<tr>
								<td valign="top">User Type<span class="req">*</span></td>
								<td valign="top">
									<select name="userType" id="userType" class="select_box" style="margin-left:0px;" >
										<option value="manager" <?php if(isset($_SESSION['post_array']['userType'])){if($_SESSION['post_array']['userType'] == 'manager'){ echo 'selected="selected"';}}?> >Manager</option>
										<option value="inspector" <?php if(isset($_SESSION['post_array']['userType'])){if($_SESSION['post_array']['userType'] == 'inspector'){ echo 'selected="selected"';}}?> >Inspector</option>
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top">Full Name <span class="req">*</span></td>
								<td valign="top"><input name="fname" type="text" class="input_small" id="fname" value="<?php echo isset($_SESSION['post_array']['fname'])?$_SESSION['post_array']['fname']:'';?>" />
								<?php if(isset($_SESSION['post_array']['fname']) && $_SESSION['post_array']['fname']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The fullname field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top" nowrap="nowrap">Company Name <span class="req">*</span></td>
								<td valign="top">
									<?php $compname = isset($_SESSION['post_array']['compname'])?$_SESSION['post_array']['compname']:array();
									$getCompanyData = $object->selQRYMultiple('id, company_name', 'organisations', 'is_deleted = 0 '); ?>
									<select name="compname[]" id="compname" class="select_box" style="margin-left:0px; width:289px;height:76px;background: url(images/text_detail.png);" multiple="multiple" >
										<!--option value="">Select</option-->
										<?php if(isset($getCompanyData)){
											foreach($getCompanyData as $company){?>
												<option value="<?=$company['id']?>"
												<?php if(in_array($company['company_name'], $compname)){ ?>
												selected="selected"
												<?php } ?> >
												<?=$company['company_name']?></option>
												<?php
											}
										} ?>
									</select>
									<?php if(isset($_SESSION['post_array']['compname']) && $_SESSION['post_array']['compname']==""){?>
										<lable htmlfor="fname" generated="true" class="error">
											<div class="error-edit-profile">The company name field is required</div>
										</lable>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td valign="top" nowrap="nowrap">Username <span class="req">*</span></td>
								<td valign="top"><input name="username" type="text" class="input_small" id="username" value="<?php echo isset($_SESSION['post_array']['username'])?$_SESSION['post_array']['username']:'';?>" />
                     <div id="unameError"> <?php if(isset($_SESSION['post_array']['username']) && $_SESSION['post_array']['username']==""){?> <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile">The username field is required</div></lable><?php unset($_SESSION['error']['username']); } ?>
                    <?php if(isset($_SESSION['error']['username']) && $_SESSION['error']['username']!=""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['username'];?></div></lable><?php } ?> </div></td>
							</tr>
							
						</table>
					</div>
				</div>
				<div class="content_right">
					<div class="signin_form1">
						<table width="450" border="0" align="left" cellpadding="0" cellspacing="15">
							<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
							<tr>
									<td valign="top">Email <span class="req">*</span></td>
									<td valign="top"><input name="email" type="text" class="input_small" id="email" value="<?php echo isset($_SESSION['post_array']['email'])?$_SESSION['post_array']['email']:'';?>" />
				 <div id="emailError">
						<?php if(isset($_SESSION['post_array']['email']) && $_SESSION['post_array']['email']==""){?>
					  <lable htmlfor="fname" generated="true" class="error">
						<div class="error-edit-profile" style="z-index:9999px; position:relative">The email field is required</div></lable><?php unset($_SESSION['error']['email']);} ?>
						<?php if(isset($_SESSION['error']['email']) && $_SESSION['error']['email']!=""){?>                <lable htmlfor="fname" generated="true" class="error">
						<div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['email'];?></div></lable><?php } ?>                  
						</div></td>
								</tr>
							<tr>
									<td valign="top">Mobile <span class="req">*</span></td>
									<td valign="top"><input name="mobile" type="text" class="input_small" id="mobile" value="<?php echo isset($_SESSION['post_array']['mobile'])?$_SESSION['post_array']['mobile']:''; ?>" />
					<?php if(isset($_SESSION['post_array']['mobile']) && $_SESSION['post_array']['mobile']==""){?>
					  <lable htmlfor="fname" generated="true" class="error">
						<div class="error-edit-profile">The mobile field is required</div></lable><?php unset($_SESSION['error']['mobile']);} ?>
						<?php if(isset($_SESSION['error']['mobile']) && $_SESSION['error']['mobile']!=""){?>
					  <lable htmlfor="fname" generated="true" class="error">
						<div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['mobile'];?></div></lable><?php } ?> </td>
								</tr>
							<tr>
								<td valign="top" nowrap="nowrap">Password <span class="req">*</span></td>
								<td valign="top">
									<input name="password" type="password" class="input_small" id="password" />
	<?php if(isset($_SESSION['post_array']['password']) && $_SESSION['post_array']['password']==""){?>
					  <lable htmlfor="fname" generated="true" class="error">
						<div class="error-edit-profile">The password field is required</div></lable><?php } ?>
						<?php if(isset($_SESSION['error']['password']) && $_SESSION['error']['password']!=""){?>
					  <lable htmlfor="fname" generated="true" class="error">
						<div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['password'];?></div></lable><?php unset($_SESSION['error']['password']); } ?>
								</td>
							</tr>
							<tr>
								<td>Re Password <span class="req">*</span></td>
								<td>
									<input name="rePassword" type="password" class="input_small" id="rePassword" />
	<?php if(isset($_SESSION['post_array']['rePassword']) && $_SESSION['post_array']['rePassword']==""){?>
					<lable htmlfor="fname" generated="true" class="error">
						<div class="error-edit-profile">The re password field is equal to password</div>
					</lable>
	<?php } ?>
					<?php if(isset($_SESSION['error']['rePassword']) && $_SESSION['error']['rePassword']!=""){?>
						<lable htmlfor="fname" generated="true" class="error">
							<div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['rePassword'];?></div>
						</lable>
						<?php unset($_SESSION['error']['rePassword']); } ?>
						<?php if(isset($_SESSION['post_array'])){unset($_SESSION['post_array']);}
						if(isset($_SESSION['error'])){unset($_SESSION['error']);}	?>
								</td>
							</tr>
							
							
						</table>
					</div>
				</div>
				<table width="900" border="0" align="left" cellpadding="0" cellspacing="15">
					<tr>
						<td colspan="2"><strong>Permission</strong></td>
					</tr>
					<tr id="addProjRow">

						<td align="left" valign="top"><input type="checkbox" name="addProject" id="addProject" value="1" checked="checked" />User can add projects</td>
						<!-- <td valign="top" style="width: 130px;">User can add projects</td> -->

	              		<span id="emailReceiveRow" class="tabPermission" <?php if(stripslashes($f['user_type']) == 'inspector'){echo 'style="display:none;"';}?>>
		              		<td align="left" valign="top">
			                	<input type="checkbox" name="emailReceive" id="emailReceive" value="1" <?php if($f['recieve_email'] == 1){ echo 'checked="checked"';}?> />
			                	Receive Email
			                </td>	
			               <!--  <td valign="top">Receive Email</td> -->
					  </span>

						<span id="addProjRow" <?php if(stripslashes($f['user_type']) == 'inspector'){echo 'style="display:none;"';}?>>
							<td align="left" valign="top">
								<input type="checkbox" name="messageBoard" id="messageBoard" value="1" <?php if($perArray['web_message_board'] == 1){ echo 'checked="checked"';}?> />
								Message Board
							</td>
							<!-- <td valign="top">Message Board</td> -->
					  </span>	

						<span id="addProjRow" <?php if(stripslashes($f['user_type']) == 'inspector'){echo 'style="display:none;"';}?>>
							<td align="left" valign="top">
								<input type="checkbox" name="menuProgressMonitoring" id="menuProgressMonitoring" value="1" <?php if($perArray['web_menu_progress_monitoring'] == 1){ echo 'checked="checked"';}?> />
								Progress Monitoring
							</td>
							<!-- <td valign="top" style="width: 170px;">Progress Monitoring</td> -->
					  </span>

						<span id="addProjRow" <?php if(stripslashes($f['user_type']) == 'inspector'){echo 'style="display:none;"';}?>>
							<td align="left" valign="top">
								<input type="checkbox" name="menuQualityChecklist" id="menuQualityChecklist" value="1" <?php if($perArray['web_menu_quality_checklist'] == 1){ echo 'checked="checked"';}?> />
								Quality Checklist
							</td>
							<!-- <td valign="top" style="width: 170px;">Quality Checklist</td> -->
					   </span>
				  </tr>
					<tr>
						<td colspan="2" align="right">
							<input type="hidden" value="b_apply_now" name="sect" id="sect" />
							<input name="submit" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/submit.png); width:111px; border:none;"/>
						</td>
						<td colspan="3" align="left">	
							<a href="javascript:void();" onclick="history.back();"><input name="passwordUpdate" type="button" class="submit_btn" id="button" value="" style="background-image:url(images/back_btn.png); width:111px; border:none;"></a>
						</td>
					</tr>
			</table>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$('#userType').change(function(){
	if($(this).val() == 'manager'){
		$('#addProjRow, #emailReceiveRow').show();
	}else{
		$('#addProjRow, #emailReceiveRow').hide();
	}
});
</script>