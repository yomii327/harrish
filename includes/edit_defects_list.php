<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
?>

<?php	
$builder_id=$_SESSION['ww_builder_id'];
$id=base64_decode($_GET['id']);

$q="SELECT * FROM ".DEFECTSLIST." 
	WHERE fk_b_id='$builder_id' AND dl_id='$id' ";
			  		  
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
	var title=document.getElementById('title').value;
	var dl_id=document.getElementById('dl_id').value;
	
	if(dl_id==''){
		var err = '<span class="sign_emsg">Oops invalid operation!<\/span><br/><br/>';
		document.getElementById('sign_in_response').innerHTML = err;
		return false;
	}else if(title!=''){
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
		result = '<span class="sign_emsg">Defect not exists!<\/span><br/><br/>';
	}else if(success == 2){
		result = '<span class="sign_emsg">Already exists!<\/span><br/><br/>';
	}else if(success == 3){
		result = '<span class="sign_msg">Defect updated successfully!<\/span><br/><br/>';
	}else if(success == 4){
		result = '<span class="sign_msg">Defect removed successfully!<\/span><br/><br/>';
		
		// clear all fields
		document.getElementById('title').value='';
		document.getElementById('dl_id').value='';
		
	}else if(success == 5){
		result = '<span class="sign_msg">Oops invalid operation!<\/span><br/><br/>';
	}
	
	document.getElementById('sign_in_process').style.visibility = 'hidden';
	document.getElementById('sign_in_response').innerHTML = result;
	document.getElementById('sign_in_response').style.visibility = 'visible';
	
	return true;
}
</script>
<!-- Ajax Post -->
<div class="content_center">
	<form action="ajax_reply.php" method="post" id="e_r_frm" name="e_r_frm" enctype="multipart/form-data" target="edit_sub_loc_target" onsubmit="return startAjax();" >
		<iframe id="edit_sub_loc_target" name="edit_sub_loc_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
		<div class="content_hd" style="background-image:url(images/edit_remove_defect_hd.png);"></div>
		<div id="sign_in_process"><br />Adding sub location ...<br/>
			<img src="images/loader.gif" /><br/>
		</div>
		<div id="sign_in_response"></div>
		<div class="signin_form">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td width="134" nowrap="nowrap">Defect Title <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="title" type="text" class="input_small" id="title" value="<?=stripslashes($f['dl_title'])?>" /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td nowrap="nowrap">
					<input type="hidden" value="<?=$f['dl_id']?>" name="dl_id" id="dl_id" />
					<input type="hidden" value="edit_remove_defect" name="sect" id="sect" />
					<input name="edit" type="submit" class="submit_btn" id="edit" value="" style="background-image:url(images/update.png); font-size:0px;" />					
					</td>
					<td align="right">
					&nbsp;
<!--<input name="remove" type="submit" class="submit_btn" id="remove" value="" style="background-image:url(images/remove_btn.png); font-size:0px;" />-->
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td align="center" colspan="2"><a href="?sect=show_defects_list"><img src="images/show_defects_list.png" style="width:175px; height:43px; border:none;" /></a></td>
				</tr>
			</table>
		</div>
	</form>
</div>
