<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php } ?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
function startAjax(){
	var title=document.getElementById('title').value;
	
	if(title!=''){
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
		result = '<span class="sign_emsg">Already exists!<\/span><br/><br/>';
	}else if(success == 2){
		result = '<span class="sign_msg">Defect added successfully!<\/span><br/><br/>';
	}else if(success == 3){
		result = '<span class="sign_msg">Defect added successfully!<\/span><br/><br/>';

		// reset form		
		document.getElementById('title').value='';		
	}
	document.getElementById('sign_in_process').style.visibility = 'hidden';
	document.getElementById('sign_in_response').innerHTML = result;
	document.getElementById('sign_in_response').style.visibility = 'visible';
	
	return true;
}
</script>
<!-- Ajax Post -->
<div class="content_center">
	<form action="ajax_reply.php" method="post"  enctype="multipart/form-data" target="add_sub_loc_target" onsubmit="return startAjax();" >
		<iframe id="add_sub_loc_target" name="add_sub_loc_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
		<div class="content_hd" style="background-image:url(images/add_new_defect.png);"></div>
		<div id="sign_in_process"><br />
			Adding sub location ...<br/>
			<img src="images/loader.gif" /><br/>
		</div>
		<div id="sign_in_response"></div>
		<div class="signin_form">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td width="134" nowrap="nowrap">Defect Title <span class="req">*</span></td>
					<td width="312" colspan="2"><input name="title" type="text" class="input_small" id="title" /></td>
				</tr>				
				<tr>
					<td><input type="hidden" value="add_defects_list" name="sect" id="sect" /></td>
					<td nowrap="nowrap" align="left"><input name="save" value="" type="submit" class="submit_btn" style="background-image:url(images/save.png); border:none; width:111px;" />
					</td>
					<td nowrap="nowrap" align="right"><input name="save_n_new" value="" type="submit" class="submit_btn" style="background-image:url(images/save_n_new.png); width:131px; border:none;" />
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
