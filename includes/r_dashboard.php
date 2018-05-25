<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 2){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
$f=$obj->db_fetch_assoc($obj->db_query("SELECT * FROM ".RESPONSIBLES." WHERE resp_id = '".$_SESSION['ww_resp_id']."' "));
?>

<div class="content_center">
	<div class="content_hd" style="background-image:url(images/dashboard_hd.png);"></div>
	<div class="signin_form">
		<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td width="134">Full Name</td>
				<td width="312" colspan="2"><input type="text" class="input_big" value="<?=stripslashes($f['resp_full_name'])?>" readonly="readonly" /></td>
			</tr>
			<tr>
				<td width="134" nowrap="nowrap">Company Name</td>
				<td width="312" colspan="2"><input type="text" class="input_big" value="<?=stripslashes($f['resp_comp_name'])?>" readonly="readonly" /></td>
			</tr>
			<tr>
				<td width="134">Username</td>
				<td width="312" colspan="2"><input type="text" class="input_big" value="<?=stripslashes($f['resp_user_name'])?>" readonly="readonly" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td colspan="2"><input class="input_big" value="<?=stripslashes($f['plain_pswd'])?>" readonly="readonly" /></td>
			</tr>
			<tr>
				<td width="134">Email</td>
				<td width="312" colspan="2"><input type="text" class="input_big" value="<?=stripslashes($f['resp_email'])?>" readonly="readonly" /></td>
			</tr>
		</table>
	</div>
</div>
