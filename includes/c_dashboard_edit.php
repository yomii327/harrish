<?php
if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
$f=$obj->db_fetch_assoc($obj->db_query("SELECT * FROM ".COMPANIES." WHERE c_id = '".$_SESSION['ww_c_id']."' "));
?>
<?php
/*if(isset($_GET['del']) && !empty($_GET['del']))
{
		$f=$obj->db_fetch_assoc($obj->db_query("UPDATE pms_companies SET company_logo = '' WHERE c_id ='".$_SESSION['ww_c_id']."'"));
		
		header("location:pms.php?sect=c_dashboard_edit");
	}*/

?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/create_account.js"></script>

<script language="javascript" type="text/javascript">
function billAddress(){
	if(document.getElementById('bil_address').checked == true){
		document.getElementById('bil_line1').value=document.getElementById('bus_line1').value;
		document.getElementById('bil_line2').value=document.getElementById('bus_line2').value;
		document.getElementById('bil_suburb').value=document.getElementById('bus_suburb').value;
		document.getElementById('bil_state').value=document.getElementById('bus_state').value;
		document.getElementById('bil_post').value=document.getElementById('bus_post').value;
		document.getElementById('bil_country').value=document.getElementById('bus_country').value;
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
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form action="wwConroller.php" method="post"  enctype="multipart/form-data" name="accountFrm"  id="accountFrm" enctype="multipart/form-data">
			<iframe id="apply_now_target" name="apply_now_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/edit_profile.png);margin-top:-50px\9;"></div>
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
								<td valign="top"><input name="fname" type="text" class="input_small" id="fname" value="<?= isset($_SESSION['post_array']['fname'])?$_SESSION['post_array']['fname']:stripslashes($f['comp_fullname']);?>" />
                                 <?php if(isset($_SESSION['post_array']['fname']) && $_SESSION['post_array']['fname']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The fullname field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top" nowrap="nowrap">Company Name <span class="req">*</span></td>
								<td valign="top"><input name="compname" type="text" class="input_small" id="compname" value="<?=isset($_SESSION['post_array']['compname'])?$_SESSION['post_array']['compname']:stripslashes($f['comp_name']);?>" />
                           <?php if(isset($_SESSION['post_array']['compname']) && $_SESSION['post_array']['compname']==""){?>                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The company name field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top" nowrap="nowrap">Username <span class="req">*</span></td>
								<td valign="top"><input name="username" type="text" class="input_small" id="username" value="<?=isset($_SESSION['post_array']['username'])?$_SESSION['post_array']['username']:stripslashes($f['comp_userName']);?>"  readonly="readonly"/>
                  <?php if(isset($_SESSION['post_array']['username']) && $_SESSION['post_array']['username']==""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile">The username field is required</div></lable><?php unset($_SESSION['error']['username']); } ?>
                    <?php if(isset($_SESSION['error']['username']) && $_SESSION['error']['username']!=""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['username'];?></div></lable><?php unset($_SESSION['error']['username']); } ?> 
                                </td>
							</tr>
							<tr>
								<td colspan="2" valign="top">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="2" valign="top"><strong>Business Address</strong></td>
							</tr>
							<tr>
								<td width="133" valign="top">Address Line 1 <span class="req">*</span></td>
								<td width="252" valign="top"><input name="bus_line1" type="text" class="input_small" id="bus_line1" value="<?=isset($_SESSION['post_array']['bus_line1'])?$_SESSION['post_array']['bus_line1']:stripslashes($f['comp_businessadd1']);?>" />            
                                <?php if(isset($_SESSION['post_array']['bus_line1']) && $_SESSION['post_array']['bus_line1']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The business address field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top">Address Line 2</td>
							  <td valign="top"><input name="bus_line2" type="text" class="input_small" id="bus_line2" value="<?=isset($_SESSION['post_array']['bus_line2'])?$_SESSION['post_array']['bus_line2']:stripslashes($f['comp_businessadd2']);?>" /></td>
							</tr>
							<tr>
								<td valign="top">Suburb <span class="req">*</span></td>
							   <td valign="top"><input name="bus_suburb" type="text" class="input_small" id="bus_suburb" value="<?=isset($_SESSION['post_array']['bus_suburb'])?$_SESSION['post_array']['bus_suburb']:stripslashes($f['comp_bussuburb']);?>" /> 
                                   <?php if(isset($_SESSION['post_array']['bus_suburb']) && $_SESSION['post_array']['bus_suburb']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The suburb field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top">State <span class="req">*</span></td>
								<td valign="top"><input name="bus_state" type="text" class="input_small" id="bus_state" value="<?=isset($_SESSION['post_array']['bus_state'])?$_SESSION['post_array']['bus_state']:stripslashes($f['comp_businessstate']);?>" />
                                <?php if(isset($_SESSION['post_array']['bus_state']) && $_SESSION['post_array']['bus_state']==""){?>                 <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The state field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top" nowrap="nowrap">Post Code <span class="req">*</span></td>
								<td valign="top"><input name="bus_post" type="text" class="input_small" id="bus_post" value="<?=isset($_SESSION['post_array']['bus_post'])?$_SESSION['post_array']['bus_post']:stripslashes($f['comp_businesspost']);?>" /><?php if(isset($_SESSION['post_array']['bus_post']) && $_SESSION['post_array']['bus_post']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The post code field is required</div></lable><?php unset($_SESSION['error']['bus_post']); } ?>
                  <?php if(isset($_SESSION['error']['bus_post']) && $_SESSION['error']['bus_post']!=""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['bus_post'];?></div></lable><?php unset($_SESSION['error']['bus_post']); } ?> 
                    </td>
							</tr>
							<tr>
								<td valign="top">Country <span class="req">*</span></td>
							  <td valign="top">
                               <select name="bus_country" id="bus_country" class="select_box" style="margin-left:2px;">
                              		<option value="Australia">Australia</option>
                              </select>
                              
                              
                              
                            </td>
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
								<td valign="top"><input name="email" type="text" class="input_small" id="email" value="<?=isset($_SESSION['post_array']['email'])?$_SESSION['post_array']['email']:stripslashes($f['comp_email']);?>" /> <div id="emailError">
                    <?php if(isset($_SESSION['post_array']['email']) && $_SESSION['post_array']['email']==""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile" style="z-index:9999px; position:relative">The email field is required</div></lable><?php unset($_SESSION['error']['email']); } ?>
					<?php if(isset($_SESSION['error']['email']) && $_SESSION['error']['email']!=""){?>                <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['email'];?></div></lable><?php unset($_SESSION['error']['email']); } ?>                  
                    </div></td>
							</tr>
							<tr>
								<td valign="top">Mobile <span class="req">*</span></td>
								<td valign="top"><input name="mobile" type="text" class="input_small" id="mobile" value="<?=isset($_SESSION['post_array']['mobile'])?$_SESSION['post_array']['mobile']:stripslashes($f['comp_mobile']);?>" /> <?php if(isset($_SESSION['post_array']['mobile']) && $_SESSION['post_array']['mobile']==""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile">The mobile field is required</div></lable><?php unset($_SESSION['error']['mobile']); } ?>
                    <?php if(isset($_SESSION['error']['mobile']) && $_SESSION['error']['mobile']!=""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['mobile'];?></div></lable><?php unset($_SESSION['error']['mobile']); } ?>  </td>
							</tr>
							
							<tr>
								<td colspan="2" valign="top">&nbsp;</td>
							</tr>
							<tr>
								<td valign="top" nowrap="nowrap"><strong>Billing Address</strong></td>
							  <td valign="top"><input name="bil_address" id="bil_address" type="checkbox" onClick="billAddress()" />
									Check if same as Business Address</td>
							</tr>
							<tr>
								<td width="133" valign="top">Address Line 1 <span class="req">*</span></td>
								<td width="252" valign="top"><input name="bil_line1" type="text" class="input_small" id="bil_line1" value="<?=isset($_SESSION['post_array']['bil_line1'])?$_SESSION['post_array']['bil_line1']:stripslashes($f['comp_billadd1']);?>" /><?php if(isset($_SESSION['post_array']['bil_line1']) && $_SESSION['post_array']['bil_line1']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The business address field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top">Address Line 2</td>
							  <td valign="top"><input name="bil_line2" type="text" class="input_small" id="bil_line2" value="<?=isset($_SESSION['post_array']['fname'])?$_SESSION['post_array']['fname']:stripslashes($f['comp_billadd2'])?>" /></td>
							</tr>
							<tr>
								<td valign="top">Suburb <span class="req">*</span></td>
								<td valign="top"><input name="bil_suburb" type="text" class="input_small" id="bil_suburb" value="<?=isset($_SESSION['post_array']['bil_suburb'])?$_SESSION['post_array']['bil_suburb']:stripslashes($f['comp_billsuburb']);?>" /><?php if(isset($_SESSION['post_array']['bil_suburb']) && $_SESSION['post_array']['bil_suburb']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The suburb field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top">State <span class="req">*</span></td>
								<td valign="top"><input name="bil_state" type="text" class="input_small" id="bil_state" value="<?=isset($_SESSION['post_array']['bil_state'])?$_SESSION['post_array']['bil_state']:stripslashes($f['comp_billstate']);?>" /><?php if(isset($_SESSION['post_array']['bil_state']) && $_SESSION['post_array']['bil_state']==""){?>
                  <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The state field is required</div></lable><?php } ?></td>
							</tr>
							<tr>
								<td valign="top" nowrap="nowrap">Post Code <span class="req">*</span></td>
								<td valign="top"><input name="bil_post" type="text" class="input_small" id="bil_post" value="<?=isset($_SESSION['post_array']['bil_post'])?$_SESSION['post_array']['bil_post']:stripslashes($f['comp_billpost']);?>" /><?php if(isset($_SESSION['post_array']['bil_post']) && $_SESSION['post_array']['bil_post']==""){?>                 <lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The post code field is required</div></lable><?php unset($_SESSION['error']['bil_post']); } ?>
                                <?php if(isset($_SESSION['error']['bil_post']) && $_SESSION['error']['bil_post']!=""){?>
                  <lable htmlfor="fname" generated="true" class="error">
                    <div class="error-edit-profile" style="z-index:9999px; position:relative"><?php echo $_SESSION['error']['bil_post'];?></div></lable><?php unset($_SESSION['error']['bil_post']); } ?>   </td>
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
								<td valign="top">Upload Logo <span class="req">*</span></td>
							  <td valign="top">
                              <br />
								<?php //if(isset($f['company_logo']) && !empty($f['company_logo'])) { ?> <img src="<?php //echo 'http://'.$_SERVER['SERVER_NAME'].'/company_logo/'.$f['company_logo']; ?>" height="100px" width="90px"/> <a href="pms.php?sect=c_dashboard_edit&del=1" style="text-shadow:none;padding-bottom:10px;">Remove</a><br /> <?php //} ?> 
                              <input type="file" name="c_logo" id="c_logo" value="" class=""/>
                              </td>
							</tr>-->
                            
                            
							<tr>
								<td>&nbsp;</td>
								<td>
                                <input type="hidden" name="company_logo" id="company_logo" value="<?=stripslashes($f['company_logo'])?>" />
                                <input type="hidden" value="c_dashboard_edit" name="sect" id="sect" />
									<input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/update.png); width:111px; border:none;"/>
                                    
                                    <a href="javascript:void();" onclick="history.back();"><input name="passwordUpdate" type="button" class="submit_btn" id="button" value="" style="background-image:url(images/back_btn.png); width:111px; border:none;"></a>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
