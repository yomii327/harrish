<?php
//ini_set("display_errors", 1);
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();

require_once'includes/commanfunction.php';
$object = new COMMAN_Class();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>
<?=SITE_NAME?>
</title>
<link rel="icon" href="images/ww_favicon.gif" type="image/gif" >
<link href="style.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="style_ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<link href="menu_style.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function() {
	var validator = $("#changePasswordForm").validate({
	rules:
	{ 
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
		newPassword:
		{
			required: '<div class="error-edit-profile">The new password field is required.</div>',
			minlength: '<div class="error-edit-profile">Please enter at least 6 characters</div>',
			maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'	
			
		},
		renewpassword:
		{
			required: '<div class="error-edit-profile">The re-enter password field is required.</div>',
			minlength: '<div class="error-edit-profile">Please enter at least 6 characters</div>',
			maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>',
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
<style>
.failure_r{
}
</style>
</head>
<body>
<?php include'includes/header.php';?>
<div id="middle">
  <div class="content_container">
    <div class="content_center">
      <div class="signin_form" style="margin-top:20px;">
		<?php $error = ''; $renewpassword=''; $oldPassword=''; $newPassword='';
            if(isset($_POST['passwordUpdate'])){
                
                if(!empty($_POST['newPassword'])){$newPassword = $_POST['newPassword'];}else{$error .= '<div class="failure_r" style="height:35px;width:460px"><p>Enter New Password</p></div>';}
                if(!empty($_POST['renewpassword'])){$renewpassword = $_POST['renewpassword'];}else{$error .= '<div class="failure_r" style="height:35px;width:460px"><p>Enter Re Enter Password</p></div>';}
                if($renewpassword == $newPassword){
					$type = $_POST['type'];
					$id = $_POST['id'];
					$tbl=COMPANIES;
					$whereCond="c_id = '$id'";
					if($type=='1'){
						$tbl=BUILDERS; 
						$whereCond="user_id='$id' AND user_type='manager' AND is_deleted = 0";
						
					}elseif($type>1){
						$tbl="user";
						$whereCond="user_id='$id' AND user_type!='manager' AND is_deleted = 0";
						
					}
					
					$q="SELECT * FROM $tbl WHERE $whereCond";					
					if($obj->db_num_rows($obj->db_query($q)) > 0){
						$f=$obj->db_fetch_assoc($obj->db_query($q));
					// Update section
						if($type=='1'){
							$fullname = $f['user_fullname'];
							$username = $f['user_name'];
							$update_query = "update user set user_password = '".md5($newPassword)."', user_plainpassword = '".$newPassword."' where user_id = '".$_POST['id']."'";				
							
						}elseif($type=='2'){
							$fullname = $f['user_fullname'];
							$username = $f['user_name'];
							$update_query = "update user set user_password = '".md5($newPassword)."', user_plainpassword = '".$newPassword."' where user_id = '".$_POST['id']."'";				
							
						}elseif($type=='3'){
							$fullname = $f['user_fullname'];
							$username = $f['user_name'];
							$update_query = "update user set user_password = '".md5($newPassword)."', user_plainpassword = '".$newPassword."' where user_id = '".$_POST['id']."'";				
											
						}else{
							$fullname = $f['comp_fullname'];
							$username = $f['comp_userName'];
							$update_query = "update pms_companies set comp_password = '".md5($newPassword)."', comp_plainpassword = '".$newPassword."' where c_id = '".$_POST['id']."'";				
						}
						
						$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
						mysql_query($update_query);
					  	echo $error .= '<div class="success_r" style="text-shadow:none; margin-left:10px;"><p>Update Successful!
						<a href='.$path.' style="color:#0469AD;">Continue to login</a></p></div>';	
					}
                }

            }else if(isset($_GET['ct']) && isset($_GET['token']) && !empty($_GET['ct']) && !empty($_GET['token'])){
				
			    $today = new DateTime(date('Y-m-d h:i:s'));
			    $pastDate = $today->diff(new DateTime(base64_decode($_GET['ct'])));
				$h = $pastDate->h; //return the difference in Hour(s).
				$key = explode("_", $_GET['token']);
               
			   if($h>=2){
					echo '<div class="failure_r"><p>Your link has been expired. please re-generate new link!</p></div>';
				}else
				 if(is_array($key) && count($key)==3){
		?>
		         <div class="content_hd" style="background-image:url(images/change_password.png);" ></div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" name="changePassword" method="post" id="changePasswordForm" >
          <table width="573" border="0" align="left" cellpadding="0" cellspacing="15">
            <tr>
              <th colspan="2" align="left" valign="top"><?php echo $error;?></th>
            </tr>
            <tr>
              <td width="243" valign="top" nowrap="nowrap">New Password <span class="req">*</span></td>
              <td width="285" colspan="2" valign="top"><input type="password" name="newPassword" id="newPassword" class="input_big" value="" /></td>
            </tr>
            <tr>
              <td width="243" valign="top">Re&nbsp;Enter&nbsp;Password <span class="req">*</span></td>
              <td width="285" colspan="2" valign="top"><input type="password" name="renewpassword"  id="renewpassword" class="input_big" value="" /></td>
            </tr>
            <tr>
              <td valign="top">&nbsp;</td>
              <td valign="top">
              <input name="id" type="hidden" value="<?php echo $key[1]; ?>" />
              <input name="type" type="hidden" value="<?php echo $key[2]; ?>" />
              <input name="passwordUpdate" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/update.png); width:111px; border:none;">
				</td>
            </tr>
          </table>
        </form>
       <?php				 	
				}else{
					echo '<div class="failure_r"><p>Something wrong in your link. please try again!</p></div>';
				}
             }else{
		   		echo '<div class="failure_r"><p>Something wrong in your link. please try again!</p></div>';
	   }   ?>
      </div>
    </div>
  </div>
</div>
<?php include'includes/footer.php';?>
</body>
</html>
