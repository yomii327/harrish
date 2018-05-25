<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
$f=$obj->db_fetch_assoc($obj->db_query("SELECT * FROM ".BUILDERS." WHERE user_id = '".$_SESSION['ww_builder_id']."' "));
?>
<link href="style.css" rel="stylesheet" type="text/css" />
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	var validator = $("#EditBuild").validate({
	rules:
	{ 
	   fname:
	   {
	   		required: true
	   },
	   compname:
	   {
	   		required: true
	   },
	   username:
	   {
	   		required: true,
			//minlength:6,
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
			//digits: true,
			//minlength:10
	   },
	   password:
	   {
	   		//required: true,
			//minlength:6,
			//maxlength:12			
	   }
	},
	messages:
	{
		fname:
		{
			required: '<div class="error-edit-profile">The full name field is required.</div>'
			//email: '<div class="error-edit-profile">The email is not valid format.</div>'
			
		},
		compname:
		{
			required: '<div class="error-edit-profile">The comapny name field is required.</div>'
			
		},
		username:
		{
			required: '<div class="error-edit-profile">The username field is required.</div>'
		},
		email:
		{
			required: '<div class="error-edit-profile">The email field is required.</div>',
			email: '<div class="error-edit-profile">Invalid email format.</div>'
		},	
		mobile:
		{
			required: '<div class="error-edit-profile">The mobile field is required.</div>',
			minlength: '<div class="error-edit-profile">Please enter at least 10 digits.</div>'
			
		},			
		password:
		{
			required: '<div class="error-edit-profile">The password field is required.</div>'
			
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

<script language="javascript" type="text/javascript">/*
function startAjax(){
	var fname=document.getElementById('fname').value;
	var username=document.getElementById('username').value;
	var compname=document.getElementById('compname').value;
	var email=document.getElementById('email').value;
	var mobile=document.getElementById('mobile').value;
	
	if(fname!='' && username!='' && compname!='' && email!='' && mobile!=''){
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

function stopAjax(success){
	var result = '';
	if(success == 1){
		result = '<span class="emsg">* represent required fileds!<\/span><br/><br/>';
	}else if(success == 2){
		result = '<span class="emsg">Invalid email address!<\/span><br/><br/>';
	}else if(success == 3){
		result = '<span class="emsg">Invalid mobile no.!<\/span><br/><br/>';
	}else if(success == 4){
		result = '<span class="sign_emsg">Please select different username!<\/span><br/><br/>';
	}else if(success == 0){
		result = '<p><span class="msg">Update successfully!<\/span><br/><br/><\/p>';
	}
	
	document.getElementById('apply_now_process').style.visibility = 'hidden';
	document.getElementById('apply_now_response').innerHTML = result;
	document.getElementById('apply_now_response').style.visibility = 'visible';
	
	return true;
}
*/
</script>
<!-- Ajax Post -->
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">	
		<form action="ajax_reply.php" method="post" id="EditBuild"  enctype="multipart/form-data">
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/edit_profile.png);"></div>
					<div id="apply_now_process">Sending request...<br/>
						<img src="images/loader.gif" /><br/>
					</div>
					<div id="apply_now_response"></div>
					<div class="signin_form">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
							<tr>
								<td colspan="2"><strong>Personal Information</strong></td>
							</tr>
							<tr>
								<td valign="top">Full Name <span class="req">*</span></td>
							  <td valign="top"><input name="fname" type="text" class="input_small" id="fname" value="<?=stripslashes($f['user_fullname'])?>" /></td>
							</tr>
							<tr>
								<td valign="top" nowrap="nowrap">Company Name <span class="req">*</span></td>
							  <td valign="top"><input name="compname" type="text" class="input_small" id="compname" value="<?=stripslashes($f['company_name'])?>" /></td>
							</tr>
							<tr>
								<td valign="top" nowrap="nowrap">Username <span class="req">*</span></td>
								<td valign="top"><input name="username" type="text" class="input_small" id="username" value="<?=stripslashes($f['user_name'])?>" />
                                
                                <?php if(isset($_SESSION['builder_invalid_user'])) { echo $_SESSION['builder_invalid_user'];  unset($_SESSION['builder_invalid_user']);} ?>
                                </td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="content_right">
					<div class="signin_form1">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td valign="top">Email <span class="req">*</span></td>
							  <td valign="top"><input name="email" type="text" class="input_small" id="email" value="<?=stripslashes($f['user_email'])?>" /></td>
							</tr>
							<tr>
								<td valign="top">Mobile <span class="req">*</span></td>
							  <td valign="top"><input name="mobile" type="text" class="input_small" id="mobile" value="<?=stripslashes($f['user_phone_no'])?>" /></td>
							</tr>
							<!--<tr>
								<td>Password</td>
								<td><input name="password" type="password" class="input_small" id="password" /></td>
							</tr>-->
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><input type="hidden" value="b_dashboard_edit" name="sect" id="sect" />
									<input name="submit" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/update.png); width:160px;"/>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
