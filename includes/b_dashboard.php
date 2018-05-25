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
<div class="content_center">
	<div class="content_hd" style="background-image:url(images/dashboard_hd.png);"></div>
	<div class="signin_form">
		<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
			<?php if(isset($_SESSION['builder_success'])) { ?>
            <tr>
            	<td colspan="2" align="center"><div class="success">Update successfully!</div>
                </td>
             </tr>   
            <?php unset($_SESSION['builder_success']); } ?>
            <tr>
				<td width="134" valign="top">Full Name</td>
			  <td width="312" colspan="2" valign="top" style="font-weight:bold;"><?=stripslashes($f['user_fullname'])?></td>
			</tr>
			<tr>
				<td width="134" valign="top" nowrap="nowrap">Company Name</td>
			  <td width="312" colspan="2" valign="top" style="font-weight:bold;"><?=stripslashes($f['company_name'])?></td>
			</tr>
			<tr>
				<td width="134" valign="top">Email</td>
			  <td width="312" colspan="2" valign="top" style="font-weight:bold;"><?=stripslashes($f['user_email'])?></td>
			</tr>
			<tr>
				<td width="134" valign="top">Mobile</td>
			  <td width="312" colspan="2" valign="top" style="font-weight:bold;"><?=stripslashes($f['user_phone_no'])?></td>
			</tr>
			<tr>
				<td width="134" valign="top">Username</td>
			  <td width="312" colspan="2" valign="top" style="font-weight:bold;"><?=stripslashes($f['user_name'])?></td>
			</tr>
			<tr>
				<td valign="top">Password</td>
			  <td colspan="2" valign="top" style="font-weight:bold;"><?=stripslashes($f['user_plainpassword'])?></td>
			</tr>			
			<tr style="display:none;">
				<td valign="top">&nbsp;</td>
				<td valign="top"><a href="?sect=b_dashboard_edit"><img src="images/edit_btn.png" border="0" /></a></td>
			</tr>
		</table>
	</div>
</div>