<?php
if(isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company'] == 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=COMPANY_DASHBOARD?>";
</script>
<?php
}
?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	var validator = $("#CompLogin").validate({
	rules:
	{  
	   username:
	   {
	   		required: true
			//email:true
			
	   },
	   password:
	   {
	   		required: true
			
	   }
	},
	messages:
	{
		username:
		{
			required: '<div class="error-edit-profile">The username field is required</div>'//,
		//	email: '<div class="error-edit-profile">The email is not valid format.</div>'
			
		},
		password:
		{
			required: '<div class="error-edit-profile">The password field is required</div>'
			
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
/*
function startAjax(){
	var username=document.getElementById('username').value;
	var password=document.getElementById('password').value;
	if(username!='' && password!=''){
		document.getElementById('sign_in_process').style.visibility = 'visible';
		document.getElementById('sign_in_response').style.visibility = 'hidden';
		return true;
	}else{
		var err = '<span class="sign_emsg">* represent required fileds!<\/span><br/><br/>';
		document.getElementById('sign_in_response').innerHTML = err;
		return false;
	}
	
	document.getElementById('sign_in_process').style.visibility = 'visible';
	document.getElementById('sign_in_response').style.visibility = 'hidden';
	return true;
}

function stopAjax(success){
	var result = '';
	if(success == 0){
		result = '<span class="sign_emsg">Access denied!<\/span><br/><br/>';
	}else if(success == 1){
		result = '<span class="sign_emsg">* represent required fileds!<\/span><br/><br/>';
	}else if(success == 2){
		window.location.href="<?=COMPANY_ANALYSIS?>";
	}
	document.getElementById('sign_in_process').style.visibility = 'hidden';
	document.getElementById('sign_in_response').innerHTML = result;
	document.getElementById('sign_in_response').style.visibility = 'visible';
	
	return true;
}*/
</script>
<style>

</style>
<!-- Ajax Post -->
<div class="content_center">
	<form action="wwConroller.php" id="CompLogin" method="post"  enctype="multipart/form-data" >
		<div class="content_hd" style="background-image:url(images/company_hd.png);"></div>
		<div id="sign_in_response">
        <?php if(isset($_SESSION['error']['message'])&&$_SESSION['error']['message']!=""){echo '<div class="failure_r"><p>'.$_SESSION['error']['message'].'</p></div>'; unset($_SESSION['error']);}?></div>
		<div class="signin_form">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td width="134" valign="top" nowrap="nowrap">Username <span class="req">*</span></td>
					<td width="312" colspan="2" valign="top"><input name="username" type="text" class="input_big" id="username" value="<?php if(isset($_SESSION['post_username']) && $_SESSION['post_username']!=""){echo $_SESSION['post_username']; unset($_SESSION['post_username']);}?>"/>
             <?php if(isset($_SESSION['error']['username']) && $_SESSION['error']['username']!=""){?>
                  <lable htmlfor="username" generated="true" class="error"><div class="error-edit-profile">The username field is required</div></lable><?php $_SESSION['error']['username']="";} ?>
                    </td>
				</tr>
				<tr>
					<td valign="top" nowrap="nowrap">Password <span class="req">*</span></td>
					<td colspan="2" valign="top"><input name="password" type="password" class="input_big" id="password" />
             <?php if(isset($_SESSION['error']['password']) && $_SESSION['error']['password']!=""){?>
                  <lable htmlfor="username" generated="true" class="error"><div class="error-edit-profile">The password field is required</div></lable><?php //unset($_SESSION['error']); 
				unset($_SESSION['error']);  } ?>                    
                    </td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2"><!--a href="?sect=forgot_password&type=0">Forgot Password</a--></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="hidden" value="company_sign_in" name="sect" id="sect" />
						<input name="submit" type="submit" class="submit_btn" id="button" value="" />
					</td>
					<td align="right"><!--<a href="create_account.php"><input name="button" type="button" class="submit_btn" id="button" value="" onClick="goto('create_account.php')" style="background-image:url(images/create_acount.png); width:161px;" /></a>
					--></td>
				</tr>
			</table>
		</div>
	</form>
</div>
