<?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1){?>
<script language="javascript" type="text/javascript">window.location.href="<?=BUILDER_DASHBOARD?>";</script>
<?php } ?>
<!-- Ajax Post -->
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	var validator = $("#ForgotPass").validate({
		rules:{
			email:{
				required: true,
				email:true
		   },
		},
		messages:{
			email:{
				required: '<div class="error-edit-profile">The email id field is required.</div>',
				email: '<div class="error-edit-profile">Invalid email format.</div>'
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
// style="width:100%;height:25px;;-webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;border:1px solid #000000;background-color:#EBEBEB;"
</script>
<style>
.success_r p {	margin-top: 1px !important;	}
</style>
<!-- Ajax Post -->
<div class="content_center">
	<form action="wwConroller.php" id="ForgotPass" method="post"  enctype="multipart/form-data">
		<div class="content_hd" style="background-image:url(images/forgot_pass.png)"></div>
		<div id="sign_in_response">
        <?php if(isset($_SESSION['error']['message'])&&$_SESSION['error']['message']!=""){echo '<div class="'.$_SESSION['error']['type'].'"><p>'.$_SESSION['error']['message'].'</p></div>'; unset($_SESSION['error']);}?></div>        
		<div class="signin_form">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td width="134" valign="top" nowrap="nowrap">Email Id <span class="req">*</span></td>
					<td width="312" colspan="2" valign="top"><input name="email" type="text" class="input_big" id="email" value="<?php if(isset($_SESSION['error']['email']) && $_SESSION['error']['email']!=""){echo $_SESSION['error']['email'];}?>"/><br />
             <?php if(isset($_SESSION['error']['email']) && $_SESSION['error']['email']!=""){?>
                  <lable htmlfor="username" generated="true" class="error"><div class="error-edit-profile">The email id field is required</div></lable><?php //unset($_SESSION['error']); 
				unset($_SESSION['error']);  } ?>                            
                    </td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">
					  <input type="hidden" value="forgot_password" name="sect" id="sect" />
						<input type="hidden" value="<?=$_GET['type']?>" name="type" id="type" />
						<input name="submit" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/submit.png);" />
					</td>					
				</tr>
			</table>
		</div>
	</form>
</div>