<?php if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){ ?>
<script language="javascript" type="text/javascript"> window.location.href="<?=HOME_SCREEN?>"; </script>
<?php } 
$error = '';$renewpassword='';$oldPassword='';$newPassword='';
if(isset($_POST['passwordUpdate'])){
	if(!empty($_POST['oldPassword'])){$oldPassword = $_POST['oldPassword'];}else{$error .= '<div class="failure_r" style="height:35px;width:460px"><p>Enter Old Password</p></div>';}
	if(!empty($_POST['newPassword'])){$newPassword = $_POST['newPassword'];}else{$error .= '<div class="failure_r" style="height:35px;width:460px"><p>Enter New Password</p></div>';}
	if(!empty($_POST['renewpassword'])){$renewpassword = $_POST['renewpassword'];}else{$error .= '<div class="failure_r" style="height:35px;width:460px"><p>Enter Re Enter Password</p></div>';}
	if($renewpassword == $newPassword){
		$qry = "select comp_password from ".COMPANIES." where c_id = '".$_SESSION['ww_c_id']."'";
		$rs = mysql_query($qry);
		while($fi = mysql_fetch_array($rs)){
			if(md5($oldPassword) == $fi['comp_password']){
				$qry = "update ".COMPANIES." set comp_password = '".md5($newPassword)."', comp_plainpassword = '".$newPassword."' where c_id = '".$_SESSION['ww_c_id']."'";
				$rs1 = mysql_query($qry);
				$error .= '<div class="success_r" style="text-shadow:none; margin-left:10px;"><p>Update Successfull !</p></div>';	
			}else{
				$error .= '<div class="failure_r" style="height:35px;width:460px"><p>Old Password Does Not Match</p></div>';	
			}
		}
	}else{
		$error .= '<div class="failure_r" style="height:35px;width:460px"><p>Enter Password and Re Enter Password Does Not Match</p></div>';
	}
	$oldPwd = mysql_query("select * from user '".$_SESSION['ww_c_id']."'");
#echo mysql_num_rows($rs);
}
?>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	var validator = $("#changePasswordForm").validate({
	rules:
	{ 
	   oldPassword:
	   {
	   		required: true,
			minlength:6,
			//maxlength:12			
	   },
	   newPassword:
	   {
	   		required: true,
			minlength:6,
			//maxlength:12			
	   },
	   renewpassword:
	   {
	   		required: true,
			minlength:6,
			//maxlength:12,
			equalTo: "#newPassword"			
	   }
	},
	messages:
	{
		oldPassword:
		{
			required: '<div class="error-edit-profile">The old password  field is required.</div>',
			minlength: '<div class="error-edit-profile">Please enter at least 6 characters</div>',
			//maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'	
			
			
		},
		newPassword:
		{
			required: '<div class="error-edit-profile">The new password field is required.</div>',
			minlength: '<div class="error-edit-profile">Please enter at least 6 characters</div>',
			//maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'	
			
		},
		renewpassword:
		{
			required: '<div class="error-edit-profile">The re-enter password field is required.</div>',
			minlength: '<div class="error-edit-profile">Please enter at least 6 characters</div>',
			//maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>',
			equalTo:'<div class="error-edit-profile">The re-enter password field does not match the Password field.</div>'
			
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

<div class="content_center">
	<div class="content_hd" style="background-image:url(images/company_profile_hd.png);" ></div>
	<div class="signin_form" style="margin-top:-10px;">
		<form action="" name="changePassword" method="post" id="changePasswordForm" >
			<table width="573" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<th colspan="2" align="left" valign="top"><?php echo $error;?></th>
				</tr>           
				<tr>
					<td width="243" valign="top">Old Password <span class="req">*</span></td>
					<td width="285" colspan="2" valign="top">
						<input type="password" class="input_big" name="oldPassword" value=""  />
					</td>
				</tr>
				<tr>
					<td width="243" valign="top" nowrap="nowrap">New Password <span class="req">*</span></td>
					<td width="285" colspan="2" valign="top">
						<input type="password" name="newPassword" id="newPassword" class="input_big" value="" />
					</td>
				</tr>
				<tr>
					<td width="243" valign="top">Re&nbsp;Enter&nbsp;Password <span class="req">*</span></td>
					<td width="285" colspan="2" valign="top">
						<input type="password" name="renewpassword"  id="renewpassword" class="input_big" value="" />
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top"><input name="passwordUpdate" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/update.png); width:111px; border:none;">
                    
                    <a href="javascript:void();" onclick="history.back();"><input name="passwordUpdate" type="button" class="submit_btn" id="button" value="" style="background-image:url(images/back_btn.png); width:111px; border:none;"></a>
                    </td>
				</tr>
			</table>
		</form>
	</div>
</div>