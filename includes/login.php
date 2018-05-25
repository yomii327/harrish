<?php /*if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1){?>
<script language="javascript" type="text/javascript">window.location.href="<?='?sect=completion&id='.base64_encode(190);?>";</script>
<?php }*/?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	var validator = $("#builderFrm").validate({
	rules:
	{
	   username:
	   {
	   		required: true
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
			//email: '<div class="error-edit-profile">The email is not valid format.</div>'
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

<main id="login">
	<div class="container">
		<div class="row">
			<div class="col-sm-offset-2 col-md-offset-3 col-sm-8 col-md-6">
				<div class="title">
					<h2><span class="orange-text">Log</span> in</h2>
				</div>
				<form action="wwConroller.php" method="post" enctype="multipart/form-data" >
					<div id="sign_in_response">
	        	<?php $cookieData = (isset($_COOKIE['remHammarProbSWId']) && !empty($_COOKIE['remHammarProbSWId']))?@unserialize($_COOKIE['remHammarProbSWId']):'';
	        		//echo "<pre>";print_r($cookieData);
	        		if(isset($_SESSION['error']['message'])&&$_SESSION['error']['message']!=""){
	        			echo '<div class="failure_r"><p>'.$_SESSION['error']['message'].'</p></div>'; unset($_SESSION['error']);}
	        	?>
					</div>
					<div class="signin_form">
						<div class="row no-margin">
							<label><h6>Username <span class="req">*</span></h6></label>
							<!--<input name="username" type="text" class="input_big" id="username" value="<?php if(isset($_SESSION['post_username']) && $_SESSION['post_username']!=""){echo $_SESSION['post_username']; unset($_SESSION['post_username']);}
						if(!empty($_COOKIE['remember_me']) && $_SESSION['post_username']==""){ echo $_COOKIE['remember_me'];}?>"/>-->
							<input name="username" type="text" class="form-control" id="username" value="<?php echo isset($cookieData['uname'])?$cookieData['uname']:'';?>" />
							<?php if(isset($_SESSION['error']['username']) && $_SESSION['error']['username']!=""){?>
								<label for="username" generated="true" class="error">
									<div class="error-edit-profile">The username field is required</div>
								</label>
							<?php $_SESSION['error']['username']="";} ?>
						</div>

						<div class="row no-margin">
							<label><h6>Password <span class="req">*</span></h6></label>
							<!--<input name="password" type="password" class="input_big" id="password" />-->
							<input name="password" type="password" class="form-control" id="password" value="<?php echo isset($cookieData['pass'])?$cookieData['pass']:'';?>" />
							<?php if(isset($_SESSION['error']['password']) && $_SESSION['error']['password']!=""){?>
								<label for="username" generated="true" class="error">
									<div class="error-edit-profile">The password field is required</div>
								</label>
							<?php //unset($_SESSION['error']);
							unset($_SESSION['error']);  } ?>
						</div>

						<div class="row no-margin">
							<div class="remember">
								<!--<label style="float:left; cursor:pointer;" for="remember">-->
								<input name="remember" id="remember" type="checkbox" value="1" <?php echo isset($cookieData['uname'])?'checked="checked"':'';?> />
		            <!--<input type="checkbox" value="1" name="remember">-->Remember password &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<a href="?sect=forgot_password&amp;type=1">Forgot Password</a>
							</div>
						</div>
						<!-- <div class="row no-margin">
							<td valign="top" colspan="2"><input type="checkbox" name="remember" value="1">Remember my password</td>
							<td valign="top"><a href="?sect=forgot_password&type=1">Forgot Password</a></td>
						</div> -->

						<div class="row no-margin">
						  <input type="hidden" value="builder_sign_in" name="sect" id="sect" />
							<button name="button" type="submit" class="btn btn-default" id="button">Login</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</main>
