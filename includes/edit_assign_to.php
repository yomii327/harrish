<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 2){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}

if(isset($_GET['id'])){
	$assign_id=base64_decode($_GET['id']);
	$resp_id = $_SESSION['ww_resp_id'];
	$q = "SELECT * FROM ".ASSIGN." WHERE resp_id = '$resp_id' AND assign_id = '$assign_id' ";
	if($obj->db_num_rows($obj->db_query($q)) > 0){
		$f = $obj->db_fetch_assoc($obj->db_query($q));
	}else{
	?>
		<script>window.location.href="<?=ACCESS_DENIED_SCREEN?>";</script>
	<?php
	}
}else{
?>
<script>window.location.href="<?=SHOW_ASSIGN_TO?>";</script>
<?php
}
?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
function startAjax(){
	var assign_to_comp_name=document.getElementById('assign_to_comp_name').value;
	var assign_to_full_name=document.getElementById('assign_to_full_name').value;
	var assign_to_phone=document.getElementById('assign_to_phone').value;
	var assign_to_email=document.getElementById('assign_to_email').value;
	
	if(assign_to_comp_name!='' && assign_to_full_name!='' && assign_to_phone!='' && assign_to_email!=''){
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
		result = '<span class="sign_emsg">* represent required fileds!<\/span><br/><br/>';
	}else if(success == 1){
		result = '<span class="sign_emsg">Invalid phone number. Only numbers are allow!<\/span><br/><br/>';
	}else if(success == 2){
		result = '<span class="sign_emsg">Invalid email id!<\/span><br/><br/>';
	}else if(success == 3){
		result = '<span class="sign_emsg">Email id already exist!<\/span><br/><br/>';
	}else if(success == 4){
		result = '<span class="sign_msg">Updated successfully!<\/span><br/><br/>';
	}
	document.getElementById('sign_in_process').style.visibility = 'hidden';
	document.getElementById('sign_in_response').innerHTML = result;
	document.getElementById('sign_in_response').style.visibility = 'visible';
	
	return true;
}
</script>
<!-- Ajax Post -->
<div class="content_center">
	<form action="ajax_reply.php" method="post"  enctype="multipart/form-data" target="add_responsible_target" onsubmit="return startAjax();" >
		<iframe id="add_responsible_target" name="add_responsible_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
		<div class="content_hd" style="background-image:url(images/edit_repairer.png);"></div>
		<div id="sign_in_process"><br />Adding responsible ...<br/>
			<img src="images/loader.gif" /><br/>
		</div>
		<div id="sign_in_response"></div>
		<div class="signin_form">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td width="134" nowrap="nowrap">Full Name <span class="req">*</span></td>
					<td width="312" colspan="2">
					<input name="assign_to_full_name" type="text" class="input_small" id="assign_to_full_name" value="<?=stripslashes($f['assign_full_name'])?>" />
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Company Name <span class="req">*</span></td>
					<td width="312" colspan="2">
					<input name="assign_to_comp_name" type="text" class="input_small" id="assign_to_comp_name" value="<?=stripslashes($f['assign_comp_name'])?>" />
					</td>
				</tr>				
				<tr>
					<td width="134" nowrap="nowrap">Phone <span class="req">*</span></td>
					<td width="312" colspan="2">
					<input name="assign_to_phone" type="text" class="input_small" id="assign_to_phone" value="<?=stripslashes($f['assign_phone'])?>" />
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Email <span class="req">*</span></td>
					<td width="312" colspan="2">
					<input name="assign_to_email" type="text" class="input_small" id="assign_to_email" value="<?=stripslashes($f['assign_email'])?>" />
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2" nowrap="nowrap">
					<input type="hidden" value="edit_assign_to" name="sect" id="sect" />
					<input type="hidden" value="<?=$f['assign_id']?>" name="assign_id" id="assign_id" />
					<input name="button" type="submit" class="submit_btn" id="button" value="save" style="background-image:url(images/update.png); font-size:0px;" />
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>
