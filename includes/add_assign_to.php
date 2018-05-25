<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 2){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}

if(isset($_POST['id'])){
	$id=$_POST['id'];
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
		result = '<span class="sign_msg">Repairer added successfully & login information has sent to the Repairer!<\/span><br/><br/>';
	}else if(success == 5){
		result = '<span class="sign_msg">Repairer added successfully & login information has sent to the Repairer!<\/span><br/><br/>';
		// reset form		
		document.getElementById('assign_to_comp_name').value='';
		document.getElementById('assign_to_full_name').value='';
		document.getElementById('assign_to_phone').value='';
		document.getElementById('assign_to_email').value='';
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
		<div class="content_hd" style="background-image:url(images/ad_repairer.png);"></div>
		<div id="sign_in_process"><br />Adding responsible ...<br/>
			<img src="images/loader.gif" /><br/>
		</div>
		<div id="sign_in_response"></div>
		<div class="signin_form">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td width="134" nowrap="nowrap">Full Name <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="assign_to_full_name" type="text" class="input_small" id="assign_to_full_name" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Company Name <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="assign_to_comp_name" type="text" class="input_small" id="assign_to_comp_name" /></td>
				</tr>				
				<tr>
					<td width="134" nowrap="nowrap">Phone <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="assign_to_phone" type="text" class="input_small" id="assign_to_phone" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Email <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="assign_to_email" type="text" class="input_small" id="assign_to_email" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2" nowrap="nowrap" align="center">
					<input type="hidden" value="add_assign_to" name="sect" id="sect" />
					<input name="button" type="submit" class="submit_btn" id="button" value="save" style="background-image:url(images/save.png); font-size:0px;" />
					<input name="button" type="submit" class="submit_btn" id="button" value="save_n_new" style="background-image:url(images/save_n_new.png); font-size:0px; width:130px;" />			</td>
				</tr>
			</table>
		</div>
	</form>
	<form method="post" action="?sect=assign_to">
		<table width="430" border="0" align="right" cellpadding="0" cellspacing="15" style="margin-top:-30px;">
			<tr>
				<td colspan="3"><input type="hidden" value="<?=$id?>" name="id" id="id" /></td>
			</tr>
			<tr>
				<td width="100">&nbsp;</td>
				<td align="center">
<input name="button2" type="submit" class="submit_btn" id="button2" value="" style="background-image:url(images/repairer_btn.png); width:275px; height:46px; border:none;" /></td>
			</tr>
		</table>
	</form>
</div>
