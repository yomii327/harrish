<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}

$_SESSION['inpector_id']=base64_decode($_GET['id']); 
if(isset($_GET['id'])){
	$id=base64_decode($_GET['id']);
	$_SESSION['edit_sub_loc']=$id;
}else{
?>
<script>window.location.href="<?=SHOW_PROJECTS?>";</script>

<?php	
}
$builder_id = $_SESSION['ww_builder_id'];
	  
$q = "SELECT *, o.id as ow_id, p.project_id as project_id FROM ".OWNERS." o 
		LEFT JOIN ".PROJECTS." p ON p.project_id = o.ow_project_id 
		LEFT JOIN ".BUILDERS." b ON p.user_id = b.user_id 
		WHERE o.id = '$id' AND p.user_id = '$builder_id'";
//echo $q; die;			  		  
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
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/edit_sub_loc.js"></script>
<script language="javascript" type="text/javascript">
function startAjax(){
	var ownerName=document.getElementById('ownerName').value;
	var userName=document.getElementById('userName').value;
	var password=document.getElementById('password').value;	
	var phone=document.getElementById('phone').value;
	var email=document.getElementById('email').value;
	var owner_id=document.getElementById('owner_id').value;
	
	if(owner_id==''){
		var err = '<span class="sign_emsg">Oops invalid operation!<\/span><br/><br/>';
		document.getElementById('sign_in_response').innerHTML = err;
		return false;
	}else if(ownerName!='' && userName!='' && password!='' && phone!='' && email!=''){
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
		result = '<span class="sign_emsg">Email Id already exist!<\/span><br/><br/>';
	}else if(success == 6){
		result = '<span class="sign_emsg">Already inspector of that project!<\/span><br/><br/>';
	}else if(success == 7){
		result = '<span class="sign_msg">Inspector edited successfully!<\/span><br/><br/>';
	}else if(success == 8){
		result = '<span class="sign_msg">Inspector removed successfully!<\/span><br/><br/>';
		
		// clear all fields
		document.getElementById('ownerName').value='';
		document.getElementById('userName').value='';
		document.getElementById('password').value='';
		document.getElementById('phone').value='';
		document.getElementById('email').value='';	
		document.getElementById('owner_id').value='';
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
	<form action="ajax_reply.php" method="post" id="e_r_frm" name="e_r_frm" enctype="multipart/form-data"  >
		<iframe id="edit_sub_loc_target" name="edit_sub_loc_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
		<div class="content_hd" style="background-image:url(images/edit_sub.png);"></div>
		<div id="sign_in_process"><br />Adding sub location ...<br/>
			<img src="images/loader.gif" /><br/>
		</div>
		<div id="sign_in_response"></div>
		<div class="signin_form">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
				<?php if(isset($_SESSION['edit_remove_inspector']['already'])) { ?>
                <tr height="50">
                	<td colspan="2"><div class="failure_r"><p><?php echo $_SESSION['edit_remove_inspector']['already']; ?></p></div></td>
                </tr>
                <?php } ?>    
                
                
                <tr>
					<td width="134" nowrap="nowrap">Project Id</td>
					<td width="312" colspan="2"><input type="text" class="input_small" readonly="readonly" value="<?=$f['project_id']?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Project Name</td>
					<td width="312" colspan="2"><input type="text" class="input_small" readonly="readonly" value="<?=stripslashes($f['project_name'])?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Full Name <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="ownerName" type="text" class="input_small" id="ownerName" value="<?=stripslashes($f['owner_full_name'])?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Username <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="userName" type="text" class="input_small" id="userName" value="<?=stripslashes($f['user_name'])?>" />
                    <?php if(isset($_SESSION['edit_remove_inspector']['username'])) { echo $_SESSION['edit_remove_inspector']['username']; unset($_SESSION['edit_remove_inspector']['username']); }?> 
                    
                    </td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Password <span class="req">*</span><div style="color:#FFF;font-size:9px;">(greater than 8 characters)</div></td>
					<td width="312" colspan="2"><input name="password" type="text" class="input_small" id="password" value="<?=stripslashes($f['plain_pswd'])?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Phone No. <span class="req">*</span>
                   <div style="color:#FFF;font-size:9px;">(only numbers)</div>
                    </td>
					<td width="312" colspan="2"><input name="phone" type="text" class="input_small" id="phone" value="<?=stripslashes($f['phone'])?>" />
                     <?php if(isset($_SESSION['edit_remove_inspector']['phone'])) { echo $_SESSION['edit_remove_inspector']['phone']; unset($_SESSION['edit_remove_inspector']['phone']); }?>
                    
                    </td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Email Id <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="email" type="text" class="input_small" id="email" value="<?=stripslashes($f['email'])?>" />
                     <?php if(isset($_SESSION['edit_remove_inspector']['email'])) { echo $_SESSION['edit_remove_inspector']['email']; unset($_SESSION['edit_remove_inspector']['email']); }?>
                    
                    </td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td nowrap="nowrap">
					<input type="hidden" value="<?=$f['ow_id']?>" name="owner_id" id="owner_id" />
					<input type="hidden" value="<?=$f['project_id']?>" name="proId" id="proId" />
                    <input type="hidden" value="<?=$f['project_id']?>" name="id" id="id" />
					<input type="hidden" value="edit_remove_inspector" name="sect" id="sect" />
					<input name="edit" type="submit" class="submit_btn" id="edit" value="" style="background-image:url(images/update.png); font-size:0px;" />					
					</td>
					<td align="right">
<input name="remove" type="submit" class="submit_btn" id="remove" value="" style="background-image:url(images/remove_btn.png); font-size:0px;" />
					</td>
				</tr>
			</table>
		</div>
	</form>
	<form method="post" action="?sect=show_sub_loc">
		<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td width="30%">&nbsp;<input type="hidden" value="<?=$f['project_id']?>" name="id" id="id" /></td>
				<td align="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name="button2" type="submit" class="submit_btn" id="button2" value="" style="background-image:url(images/show_inspactors.png); width:177px; height:44px; background-repeat:no-repeat;" /></td>
			</tr>
		</table>
	</form>
</div>
