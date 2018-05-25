<?php if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php }
$f=$obj->db_fetch_assoc($obj->db_query("SELECT * FROM ".COMPANIES." WHERE c_id = '".$_SESSION['ww_c_id']."' ")); ?>
<div class="content_center">
	<div class="content_hd" style="background-image:url(images/company_profile_hd.png);" ></div>
	<div class="signin_form" style="margin-top:-10px;">
		<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
 			<tr>
				<th colspan="2" align="left" valign="top">
    <?php if(isset($_SESSION['success'])&&$_SESSION['success']!=""){echo '<div class="success_r" style="text-shadow:none; margin-left:10px;"><p>'.$_SESSION['success'].'</p></div><br>'; unset($_SESSION['success']);}?>                
                </th>
			</tr>           
            <tr>
				<td width="134" valign="top">Full Name</td>
			  <td width="312" colspan="2" valign="top">
              <input type="text" class="input_big" value="<?=stripslashes($f['comp_fullname'])?>" readonly="readonly" /></td>
			</tr>
			<tr>
				<td width="134" valign="top" nowrap="nowrap">Company Name</td>
			  <td width="312" colspan="2" valign="top"><input type="text" class="input_big" value="<?=stripslashes($f['comp_name'])?>" readonly="readonly" /></td>
			</tr>
			<tr>
				<td width="134" valign="top">Username</td>
			  <td width="312" colspan="2" valign="top"><input type="text" class="input_big" value="<?=stripslashes($f['comp_userName'])?>" readonly="readonly" /></td>
			</tr>
			<tr>
				<td valign="top">Password</td>
			  <td colspan="2" valign="top"><input class="input_big" value="<?=stripslashes($f['comp_plainpassword'])?>" readonly="readonly"  type="password"/></td>
			</tr>
			<tr>
				<td width="134" valign="top">Email</td>
			  <td width="312" colspan="2" valign="top"><input type="text" class="input_big" value="<?=stripslashes($f['comp_email'])?>" readonly="readonly" /></td>
			</tr>
            
            <?php //if(isset($f['company_logo']) && !empty($f['company_logo'])) { ?>
           <!-- <tr>
				<td width="134" valign="top">Logo</td>
			  <td width="312" colspan="2" valign="top">
              
              <img src="<?php //echo 'http://'.$_SERVER['SERVER_NAME'].'/company_logo/'.$f['company_logo']; ?>" height="100px" width="90px"/></td>
			</tr>-->
            <?php // } ?>
			<tr>
				<td valign="top">&nbsp;</td>
				<td valign="top">
<?php if($_SESSION['web_edit_profile'] == 1){?>
					<a href="?sect=c_dashboard_edit"><img src="images/edit_btn.png" border="0" /></a>&nbsp;&nbsp;<a href="?sect=c_changepassword"><img src="images/changePassword.png" border="0" /></a>
<?php }?>
				</td>
					
			</tr>
		</table>
	</div>
</div>