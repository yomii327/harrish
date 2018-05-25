<?php
session_start();
require_once'includes/functions.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=SITE_NAME?></title>
<link rel="icon" href="images/pms_favicon.gif" type="image/gif" >
<link href="style.css" rel="stylesheet" type="text/css" />
<link href="menu_style.css" rel="stylesheet" type="text/css" />
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/create_account.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script language="javascript" type="text/javascript">
function startAjax(type,val,result){//alert(val);
	//AjaxShow("POST","ajaxFunctions.php?type=1 && "+type+"="+val,result);
}
/*
function startAjax(){
	var bus_l1=document.getElementById('bus_line1').value;
	var fname=document.getElementById('fname').value;
	var compname=document.getElementById('compname').value;
	var email=document.getElementById('email').value;
	var mobile=document.getElementById('mobile').value;
	
	var bus_l1=document.getElementById('bus_line1').value;
	var bus_l2=document.getElementById('bus_line2').value;
	var bus_suburb=document.getElementById('bus_suburb').value;
	var bus_state=document.getElementById('bus_state').value;
	var bus_post=document.getElementById('bus_post').value;
	var bus_country=document.getElementById('bus_country').value;
	
	var bil_l1=document.getElementById('bil_line1').value;
	var bil_l2=document.getElementById('bil_line2').value;
	var bil_suburb=document.getElementById('bil_suburb').value;
	var bil_state=document.getElementById('bil_state').value;
	var bil_post=document.getElementById('bil_post').value;
	var bil_country=document.getElementById('bil_country').value;
	
	if(fname!='' && compname!='' && email!='' && mobile!='' && bus_l1!='' && bus_suburb!='' && bus_state!='' && bus_post!='' &&
	   bus_country!='' && bil_l1!='' && bil_suburb!='' && bil_state!='' && bil_post!='' && bil_country!=''){
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
	if(success == 0){
		
		result = '<span class="emsg">Email id already exist!<\/span><br/><br/>';
		
	}else if(success == 1){
		result = '<span class="emsg">Invalid email address!<\/span><br/><br/>';
	}else if(success == 2){
		result = '<span class="emsg">Invalid mobile no.!<\/span><br/><br/>';
	}else if(success == 3){
		result = '<p><span class="emsg">Invalid post code!<\/span><br/><br/><\/p>';
	}else if(success == 4){
		result = '<p><span class="msg">Request send successfully!<\/span><br/><br/><\/p>';
		document.getElementById('apply_now').style.display = 'none';
		document.getElementById('request_send').style.display = 'block';
	}
	document.getElementById('apply_now_process').style.visibility = 'hidden';
	document.getElementById('apply_now_response').innerHTML = result;
	document.getElementById('apply_now_response').style.visibility = 'visible';
	
	return true;
}

function billAddress(){
	if(document.getElementById('bil_address').checked == true){
		document.getElementById('bil_line1').value=document.getElementById('bus_line1').value;
		document.getElementById('bil_line2').value=document.getElementById('bus_line2').value;
		document.getElementById('bil_suburb').value=document.getElementById('bus_suburb').value;
		document.getElementById('bil_state').value=document.getElementById('bus_state').value;
		document.getElementById('bil_post').value=document.getElementById('bus_post').value;
		//document.getElementById('bil_country').value=document.getElementById('bus_country').value;
		document.getElementById('bil_line1').readOnly="readonly";
		document.getElementById('bil_line2').readOnly="readonly";
		document.getElementById('bil_suburb').readOnly="readonly";
		document.getElementById('bil_state').readOnly="readonly";
		document.getElementById('bil_post').readOnly="readonly";
	}else if(document.getElementById('bil_address').checked == false){
		document.getElementById('bil_line1').value='';
		document.getElementById('bil_line2').value='';
		document.getElementById('bil_suburb').value='';
		document.getElementById('bil_state').value='';
		document.getElementById('bil_post').value='';
		document.getElementById('bil_country').value='';
	}
}
</script>
<!-- Ajax Post -->
<style type="text/css">
.msg_class{
	display:block;
	}
.msg_er_class{
	display:none;
	}	
</style>
</head>
<body>
<?php if(isset($_SESSION['success'])) { $class='msg_class'; } else { $class='msg_er_class';  } ?>

<?php include'includes/header.php';

?>
<div id="middle" >
    <div id="apply_now"  <?php if(isset($_SESSION['success'])) { $class='msg_er_class'; } ?>>
		<form  method="post" action="wwConroller.php" enctype="multipart/form-data" name="accountFrm"  id="accountFrm">
			<!--<iframe id="apply_now_target" name="apply_now_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>-->
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1"></div>
					<div id="apply_now_process">Sending request...<br/>
						<img src="images/loader.gif" /><br/>
					</div>
					<div class="signin_form">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
                            <tr>
								<td colspan="2"><strong>Personal Information</strong></td>
							</tr>
							<tr>
								<td valign="top">Full Name <span class="req">*</span></td>
								<td valign="top"><input name="fname"  type="text" class="input_small" id="fname" value="<?php if(isset($_SESSION['post_array']['fname']) && $_SESSION['post_array']['fname']!='' ) { echo $_SESSION['post_array']['fname']; }?>" />
             <?php if(isset($_SESSION['post_array']['fname']) && $_SESSION['post_array']['fname']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The fullname field is required</div></lable><?php } ?></td>
							</tr> 
							<tr>
								<td valign="top" nowrap="nowrap">Company Name <span class="req">*</span></td>
								<td valign="top"><input name="compname" type="text" class="input_small" id="compname" value="<?php if(isset($_SESSION['post_array']['compname']) && $_SESSION['post_array']['compname']!='' ) { echo $_SESSION['post_array']['compname']; } ?>"  />
             <?php if(isset($_SESSION['post_array']['compname']) && $_SESSION['post_array']['compname']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The company name field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td colspan="2" valign="top">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="2" valign="top"><strong>Business Address</strong></td>
							</tr>
							<tr>
								<td width="133" valign="top">Address Line 1 <span class="req">*</span></td>
								<td width="252" valign="top"><input name="bus_line1" type="text" class="input_small" id="bus_line1"  value="<?php if(isset($_SESSION['post_array']['bus_line1']) && $_SESSION['post_array']['bus_line1']!='' ) { echo $_SESSION['post_array']['bus_line1']; } ?>" />
             <?php if(isset($_SESSION['post_array']['bus_line1']) && $_SESSION['post_array']['bus_line1']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The business address field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top">Address Line 2</td>
							  <td valign="top"><input name="bus_line2" type="text" class="input_small" id="bus_line2" value="<?php if(isset($_SESSION['post_array']['bus_line2']) && $_SESSION['post_array']['bus_line2']!='' ) { echo $_SESSION['post_array']['bus_line2']; } ?>"  /></td>
							</tr>
							<tr>
								<td valign="top">Suburb <span class="req">*</span></td>
								<td valign="top"><input name="bus_suburb" type="text" class="input_small" id="bus_suburb"  value="<?php if(isset($_SESSION['post_array']['bus_suburb']) && $_SESSION['post_array']['bus_suburb']!='' ) { echo $_SESSION['post_array']['bus_suburb']; } ?>" />
             <?php if(isset($_SESSION['post_array']['bus_suburb']) && $_SESSION['post_array']['bus_suburb']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The suburb field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top">State <span class="req">*</span></td>
								<td valign="top"><input name="bus_state" type="text" class="input_small" id="bus_state" value="<?php if(isset($_SESSION['post_array']['bus_state']) && $_SESSION['post_array']['bus_state']!='' ) { echo $_SESSION['post_array']['bus_state']; } ?>"  />
             <?php if(isset($_SESSION['post_array']['bus_state']) && $_SESSION['post_array']['bus_state']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The state field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top" nowrap="nowrap">Post Code <span class="req">*</span></td>
								<td valign="top"><input name="bus_post" type="text" class="input_small" id="bus_post" value="<?php if(isset($_SESSION['post_array']['bus_post']) && $_SESSION['post_array']['bus_post']!='' ) { echo $_SESSION['post_array']['bus_post']; } ?>"  />
             <?php if(isset($_SESSION['post_array']['bus_post']) && $_SESSION['post_array']['bus_post']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The post code field is required</div></lable><?php unset($_SESSION['error']['bus_post']); } ?>
<?php if(isset($_SESSION['error']['bus_post']) && $_SESSION['error']['bus_post']!=""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['bus_post'];?></div></lable><?php unset($_SESSION['error']['bus_post']); } ?>                     
                  </td>
							</tr>
							<tr>
								<td valign="top">Country <span class="req">*</span></td>
								<td valign="top"><select name="bil_country" id="bil_country" class="select_box" style="margin-left:2px;">
                              		<option value="Australia">Australia</option>
                              </select></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="content_right">
					<div class="signin_form1">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
							<tr>
								<td colspan="2" valign="top">&nbsp;</td>
							</tr>
							<tr>
								<td valign="top">Email <span class="req">*</span></td>
								<td valign="top"><input name="email" type="text" class="input_small" id="email"  value="<?php if(isset($_SESSION['post_array']['email']) && $_SESSION['post_array']['email']!='' ) { echo $_SESSION['post_array']['email']; } ?>" onkeyup="startAjax('checkEmail',this.value,'emailError');" onblur="startAjax('checkEmail',this.value,'emailError');" /><div id="emailError">
             <?php if(isset($_SESSION['post_array']['email']) && $_SESSION['post_array']['email']==""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile" style="z-index:9999px; position:relative">The email field is required</div></lable><?php unset($_SESSION['error']['email']); } ?>
<?php if(isset($_SESSION['error']['email']) && $_SESSION['error']['email']!=""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['email'];?></div></lable><?php unset($_SESSION['error']['email']); } ?>                  
                    </div>
                    </td>
							</tr>
							<tr>
								<td valign="top">Mobile <span class="req">*</span></td>
								<td valign="top"><input name="mobile" type="text" class="input_small" id="mobile"  value="<?php if(isset($_SESSION['post_array']['mobile']) && $_SESSION['post_array']['mobile']!='' ) { echo $_SESSION['post_array']['mobile']; } ?>" />
             <?php if(isset($_SESSION['post_array']['mobile']) && $_SESSION['post_array']['mobile']==""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile">The mobile field is required</div></lable><?php unset($_SESSION['error']['mobile']); } ?>
<?php if(isset($_SESSION['error']['mobile']) && $_SESSION['error']['mobile']!=""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['mobile'];?></div></lable><?php unset($_SESSION['error']['mobile']); } ?>    
                    </td>
							</tr>
							<tr>
								<td colspan="2" valign="top">&nbsp;</td>
							</tr>
							<tr>
								<td valign="top" nowrap="nowrap"><strong>Billing Address</strong></td>
							  <td valign="top"><input name="bil_address" id="bil_address" type="checkbox" onclick="billAddress()" <?php if(isset($_SESSION['post_array']['bil_address'])) { ?> checked="checked" <?php }?> />
									Check if same as Business Address</td>
							</tr>
							<tr>
								<td width="133" valign="top">Address Line 1 <span class="req">*</span></td>
								<td width="252" valign="top"><input name="bil_line1" type="text" class="input_small" id="bil_line1"  value="<?php if(isset($_SESSION['post_array']['bil_line1']) && $_SESSION['post_array']['bil_line1']!='' ) { echo $_SESSION['post_array']['bil_line1']; } ?>" />
             <?php if(isset($_SESSION['post_array']['bil_line1']) && $_SESSION['post_array']['bil_line1']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The business address field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top">Address Line 2</td>
							  <td valign="top"><input name="bil_line2" type="text" class="input_small" id="bil_line2"  value="<?php if(isset($_SESSION['post_array']['bil_line2']) && $_SESSION['post_array']['bil_line2']!='' ) { echo $_SESSION['post_array']['bil_line2']; } ?>" /></td>
							</tr>
							<tr>
								<td valign="top">Suburb <span class="req">*</span></td>
								<td valign="top"><input name="bil_suburb" type="text" class="input_small" id="bil_suburb"  value="<?php if(isset($_SESSION['post_array']['bil_suburb']) && $_SESSION['post_array']['bil_suburb']!='' ) { echo $_SESSION['post_array']['bil_suburb']; } ?>" />
             <?php if(isset($_SESSION['post_array']['bil_suburb']) && $_SESSION['post_array']['bil_suburb']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The suburb field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top">State <span class="req">*</span></td>
								<td valign="top"><input name="bil_state" type="text" class="input_small" id="bil_state"  value="<?php if(isset($_SESSION['post_array']['bil_state']) && $_SESSION['post_array']['bil_state']!='' ) { echo $_SESSION['post_array']['bil_state']; } ?>" />
             <?php if(isset($_SESSION['post_array']['bil_state']) && $_SESSION['post_array']['bil_state']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The state field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top" nowrap="nowrap">Post Code <span class="req">*</span></td>
								<td valign="top"><input name="bil_post" type="text" class="input_small" id="bil_post"  value="<?php if(isset($_SESSION['post_array']['bil_post']) && $_SESSION['post_array']['bil_post']!='' ) { echo $_SESSION['post_array']['bil_post']; } ?>" />
             <?php if(isset($_SESSION['post_array']['bil_post']) && $_SESSION['post_array']['bil_post']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The post code field is required</div></lable><?php unset($_SESSION['error']['bil_post']); } ?>
<?php if(isset($_SESSION['error']['bil_post']) && $_SESSION['error']['bil_post']!=""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['bil_post'];?></div></lable><?php unset($_SESSION['error']['bil_post']); } ?>                      
                  </td>
							</tr>
							<tr>
								<td valign="top">Country <span class="req">*</span></td>
								<td valign="top">
                                   <select name="bil_country" id="bil_country" class="select_box" style="margin-left:2px;">
                              		<option value="Australia">Australia</option>
                              </select>
                                
                                
                  
                  </td>
							</tr>
                              <!--<tr>
								<td valign="top">Upload Logo </td>
							  <td valign="top"><input type="file" name="c_logo" id="c_logo" value="" class=""/>
                              <?php //if(isset($_SESSION['error']['c_logo'])) { echo '<div class="error-edit-profile">'.$_SESSION['error']['c_logo'].'</div>'; unset($_SESSION['error']['c_logo']); } ?> 
                              </td>
							</tr>-->
                            
							<tr>
								<td valign="top">&nbsp;</td>
								<td valign="top">
									<input type="hidden" value="apply_now" name="sect" id="sect" />
<input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/apply_now.png); width:112px; height:44px;"/>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<?php include'includes/footer.php';?>
</body>
</html>
