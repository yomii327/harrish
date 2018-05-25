<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}

if(isset($_GET['id'])){
	$id=base64_decode($_GET['id']);
}else{
?>
<script>window.location.href="<?=SHOW_PROJECTS?>";</script>
<?php	
}
$builder_id = $_SESSION['ww_builder_id'];
$q = "SELECT * FROM ".RESPONSIBLES." r 
	  JOIN ".PROJECTS." p ON p.id = r.project_id 
	  WHERE r.resp_id = '$id' AND r.builder_id = '$builder_id' ";
if($obj->db_num_rows($obj->db_query($q)) == 0){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=ACCESS_DENIED_SCREEN?>";
</script>
<?php
}
$f=$obj->db_fetch_assoc($obj->db_query($q));
?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
function startAjax(){
	var resp_comp_name=document.getElementById('resp_comp_name').value;
	var resp_full_name=document.getElementById('resp_full_name').value;
	var userName=document.getElementById('userName').value;
	var password=document.getElementById('password').value;
	var resp_phone=document.getElementById('resp_phone').value;
	var resp_email=document.getElementById('resp_email').value;
	var resp_id=document.getElementById('resp_id').value;
	
	if(resp_id==''){
		var err = '<span class="sign_emsg">Oops invalid operation!<\/span><br/><br/>';
		document.getElementById('sign_in_response').innerHTML = err;
		return false;
	}else if(resp_comp_name!='' && resp_full_name!='' && userName!='' && password!='' && resp_phone!='' && resp_email!=''){
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
		result = '<span class="sign_emsg">Password must be greater than 8 characters!<\/span><br/><br/>';
	}else if(success == 4){
		result = '<span class="sign_emsg">Username already exists!<\/span><br/><br/>';
	}else if(success == 5){
		result = '<span class="sign_emsg">Responsible already exists!<\/span><br/><br/>';
	}else if(success == 6){
		result = '<span class="sign_msg">Responsible edited successfully!<\/span><br/><br/>';
	}else if(success == 7){
		result = '<span class="sign_emsg">Responsible assigned Issue(s)<br/>Frist retain Issue(s) & then come back!<\/span><br/><br/><br/>';
	}else if(success == 8){
		result = '<span class="sign_msg">Responsible removed successfully!<\/span><br/><br/>';
		
		// clear all fields
		document.getElementById('resp_comp_name').value='';
		document.getElementById('resp_full_name').value='';
		document.getElementById('userName').value='';
		document.getElementById('password').value='';
		document.getElementById('resp_phone').value='';
		document.getElementById('resp_email').value='';
		document.getElementById('resp_id').value='';
	}else if(success == 9){
		result = '<span class="sign_emsg">Oops invalid operation!<\/span><br/><br/>';
	}
	
	document.getElementById('sign_in_process').style.visibility = 'hidden';
	document.getElementById('sign_in_response').innerHTML = result;
	document.getElementById('sign_in_response').style.visibility = 'visible';
	
	return true;
}
</script>
<!-- Ajax Post -->
<div class="content_center">
	<form action="ajax_reply.php" method="post"  enctype="multipart/form-data" target="edit_responsible_target" onsubmit="return startAjax();" >
		<iframe id="edit_responsible_target" name="edit_responsible_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
		<div class="content_hd" style="background-image:url(images/edit_responsible.png);"></div>
		<div id="sign_in_process"><br />Adding sub location ...<br/>
			<img src="images/loader.gif" /><br/>
		</div>
		<div id="sign_in_response"></div>
		<div class="signin_form">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td width="134" nowrap="nowrap">Project Id</td>
					<td width="312" colspan="2"><input type="text" class="input_small" readonly="readonly" value="<?=$f['pro_code']?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Project Name</td>
					<td width="312" colspan="2"><input type="text" class="input_small" readonly="readonly" value="<?=stripslashes($f['project_name'])?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Full Name <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="resp_full_name" type="text" class="input_small" id="resp_full_name" value="<?=stripslashes($f['resp_full_name'])?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Company <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="resp_comp_name" type="text" class="input_small" id="resp_comp_name" value="<?=stripslashes($f['resp_comp_name'])?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Username <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="userName" type="text" class="input_small" id="userName" value="<?=stripslashes($f['resp_user_name'])?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Password <span class="req">* <br />(greater than 8 characters)</span></td>
					<td width="312" colspan="2"><input name="password" type="text" class="input_small" id="password" value="<?=stripslashes($f['plain_pswd'])?>" /></td>
				</tr>			
				<tr>
					<td width="134" nowrap="nowrap">Phone No. <span class="req">* <br />(only numbers)</span></td>
					<td width="312" colspan="2"><input name="resp_phone" type="text" class="input_small" id="resp_phone" value="<?=stripslashes($f['resp_phone'])?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Email Id <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="resp_email" type="text" class="input_small" id="resp_email" value="<?=stripslashes($f['resp_email'])?>" /></td>
				</tr>				
				<tr>
					<td>&nbsp;</td>
					<td nowrap="nowrap">
					<input type="hidden" value="<?=$f['resp_id']?>" name="resp_id" id="resp_id" />
					<input type="hidden" value="<?=$f['project_id']?>" name="project_id" id="project_id" />
					<input type="hidden" value="edit_remove_responsible" name="sect" id="sect" />
					<input name="edit" type="submit" class="submit_btn" id="edit" value="" style="background-image:url(images/update.png); font-size:0px;" />
					</td>
					<td align="right">
<input name="remove" type="submit" class="submit_btn" id="remove" value="" style="background-image:url(images/remove_btn.png); font-size:0px;" />
					</td>
				</tr>
			</table>
		</div>
	</form>
	<form method="post" action="?sect=show_responsible">
		<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td width="30%"><input type="hidden" value="<?=$f['project_id']?>" name="id" id="id" /></td>
				<td align="right"><input name="button2" type="submit" class="submit_btn" id="button2" value="" style="background-image:url(images/show_traders.png); width:275px; height:43px; background-repeat:no-repeat;" /></td>
			</tr>
		</table>
	</form>
</div>
