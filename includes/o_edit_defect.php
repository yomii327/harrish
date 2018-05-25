<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 0){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php }
$df_id=base64_decode($_GET['did']);
$pid=base64_decode($_GET['pid']);

$owner_id = $_SESSION['ww_owner_id'];

include('includes/commanfunction.php');
$object= new COMMAN_Class();


$q = "SELECT *, p.id as project_id FROM ".OWNERS." o 
	  LEFT JOIN ".PROJECTS." p ON p.id = o.ow_project_id 
	  WHERE o.id = '$owner_id' AND o.ow_project_id = '$pid'";
if($obj->db_num_rows($obj->db_query($q)) == 0){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=ACCESS_DENIED_SCREEN?>";
</script>
<?php
}
$f=$obj->db_fetch_assoc($obj->db_query($q));

// get defect history
$q="SELECT * FROM ".DEFECTS." WHERE df_id = '$df_id'";
$fd=$obj->db_fetch_assoc($obj->db_query($q));

// change create_date format
if($fd['create_date']!='0000-00-00'){
	$create_date = $fd['create_date'];
	$create_date = date("d/m/Y", strtotime($create_date));
}else{
	$create_date = '';
}

// change fixed_by_date format
if($fd['fixed_by_date']!='0000-00-00'){
	$fixed_by_date = $fd['fixed_by_date'];
}else{
	$fixed_by_date = '';
}
$priority=stripslashes($fd['priority']);
$d_typ_id=$fd['defect_type_id'];
$d_resp_id=$fd['resp_id'];
?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
function startAjax(){
	var fixed_by_date=document.getElementById('fixed_by_date').value;
	var inspected_by=document.getElementById('inspected_by').value;
	var area_room=document.getElementById('area_room').value;
	var defect_desc=document.getElementById('defect_desc').value;
	var filename = document.getElementById('photo').value;
	var extention = ( (/[.]/.exec(filename)) ? /[^.]+$/.exec(filename) : undefined);

	if(fixed_by_date!='' && inspected_by!='' && area_room!='' && defect_desc!=''){
		if(filename!=''){
			if(extention=='png' || extention=='gif' || extention=='jpg' || extention=='jpeg' ||
			   extention=='PNG' || extention=='GIF' || extention=='JPG' || extention=='JPEG'){
				document.getElementById('sign_in_process').style.visibility = 'visible';
				document.getElementById('sign_in_response').style.visibility = 'hidden';
				return true;
			}else{				
				var err = '<span class="sign_emsg">Invalid photo format!<\/span><br/><br/>';
				document.getElementById('sign_in_response').innerHTML = err;
				return false;
			}
		}else{
			document.getElementById('sign_in_process').style.visibility = 'visible';
			document.getElementById('sign_in_response').style.visibility = 'hidden';
			return true;
		}
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
		result = '<span class="sign_emsg">* represent required fileds---2 !<\/span><br/><br/>';
	}else if(success == 1){
		result = '<span class="sign_msg">Issue submitted successfully!<\/span><br/><br/>';
	}
	document.getElementById('sign_in_process').style.visibility = 'hidden';
	document.getElementById('sign_in_response').innerHTML = result;
	document.getElementById('sign_in_response').style.visibility = 'visible';	
	
	return true;
}
</script>
<!-- Ajax Post -->
<!-- Date Picker Starts -->
<style type="text/css" title="currentStyle">
@import "datatable/examples_support/themes/smoothness/jquery-ui-1.8.17.custom.css";
</style>
<script src="datepicker/jquery-ui.min.js"></script>
<script language="javascript">
$(function() {
	$( "#fixed_by_date" ).datepicker();
});
</script>
<!-- Date Picker Ends -->
<div class="content_center">
	<form action="ajax_reply.php" method="post"  enctype="multipart/form-data" target="add_defect_target" onsubmit="return startAjax();" >
		<iframe id="add_defect_target" name="add_defect_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
		<div class="content_hd" style="background-image:url(images/edit_defect_hd.png);"></div>
		<div id="sign_in_process">
			Sending request...<br/>
			<img src="images/loader.gif" /><br/>
		</div>
		<div id="sign_in_response"></div>
		<div class="signin_form1" style="margin-top:15px;">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td width="134" nowrap="nowrap">Project </td>
					<td width="312" colspan="2"><input type="text" readonly="readonly" value="<?=stripslashes($f['pro_name'])?>" class="input_small" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Date Raised<span class="req"></span></td>
					<td width="312" colspan="2"><input type="text" name="create_date" class="input_small" readonly="readonly" value="<?=$create_date?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Status<span class="req"></span></td>
					<td width="312" colspan="2"><input type="text" class="input_small" readonly="readonly" value="<?=stripslashes($fd['status'])?>" /></td>
				</tr> 
				
				<tr>
					<td width="134" nowrap="nowrap">Location <span class="req">*</span></td>
					<td width="312" colspan="2"><input type="text" id="area_room" name="area_room" class="input_small" value="<?=stripslashes($object->getDataByKey('pms_projects', 'id', $fd['area_room'], 'pro_name'))?>" /></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Inspected By <span class="req">*</span></td>
					<td width="312" colspan="2"><input type="text" class="input_small" name="inspected_by" id="inspected_by" value="<?=stripslashes($fd['inspected_by'])?>" /></td>
				</tr>
				
				<tr>
					<td nowrap="nowrap">Issued To </td>
					<td>
					<select class="select_box" id="repairer" name="repairer" style="margin-left:0px;" style="margin-left:0px;">
					<?php
						echo $qr="SELECT resp_id,resp_comp_name FROM ".RESPONSIBLES."  
							  WHERE project_id = '$pid'";
						$r=mysql_query($qr);
						while($fr=mysql_fetch_assoc($r)){
						$resp_id=$fr['resp_id'];
						?>
							<option value="<?=$fr['resp_id']?>" <?=$resp_id==$d_resp_id?"selected='selected'":''?>><?=stripslashes($fr['resp_comp_name'])?></option>
						<?php
						}
					?>
					</select>
					</td>
				</tr>
				
				<tr>
					<td nowrap="nowrap">Fixed by Date <span class="req">*</span></td>
					<td><input type="text" id="fixed_by_date" class="input_small" name="fixed_by_date" readonly="readonly" value="<?=$fixed_by_date?>" /></td>
				</tr>
					
				<tr>
					<td width="134" nowrap="nowrap">Type</td>
					<td width="312" colspan="2">
						<select class="select_box" id="defect_type" name="defect_type" style="margin-left:0px;" style="margin-left:0px;">
						<?php
						$qd=$obj->db_query("SELECT * FROM ".PROJECTDEFECTS." pd 
											LEFT JOIN ".DEFECTSLIST." dl ON pd.fk_dl_id=dl.dl_id  
											WHERE pd.fk_p_id='$pid'");
						if($obj->db_num_rows($qd)>0){					
							while($fdl=$obj->db_fetch_assoc($qd)){
								$dl_id=$fdl['dl_id'];
							?>
								<option value="<?=$fdl['dl_id']?>" <?=$dl_id==$d_typ_id?"selected='selected'":''?>><?=stripslashes($fdl['dl_title'])?></option>
							<?php	
							}
						}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap">Priority</td>
					<td width="312" colspan="2"><select class="select_box" id="priority" name="priority" style="margin-left:0px;">
							<option <?=$priority=='Low'?"selected='selected'":''?>>Low</option>
							<option <?=$priority=='Medium'?"selected='selected'":''?>>Medium</option>
							<option <?=$priority=='High'?"selected='selected'":''?>>High</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Description <span class="req">*</span></td>
					<td width="312" colspan="2"><textarea class="text_area" id="defect_desc" name="defect_desc"><?=stripslashes($fd['defect_desc'])?></textarea></td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top">Note &amp; Sign Off</td>
					<td><textarea class="text_area" id="defect_note" name="defect_note"><?=stripslashes($fd['defect_note'])?></textarea></td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top">Picture</td>
					<td colspan="2">
					<div style="background-image:url(images/photo_2_bg.png); background-repeat:no-repeat; width:293px; height:210px; text-align:center;">
					<a href="?sect=issue_photo&photo=<?=$fd['photo']?>" title="Click for large view">
					<img src="<?=$fd['photo']?>" class="issue_img" style="width:280px; height:200px; border:none; margin-top:5px;" />
					</a>
					</div>
					</td>
				</tr>
				
				<tr>
					<td width="134" nowrap="nowrap">Photo<span class="req"> ( gif, png, jpg )</span></td>
					<td width="312" colspan="2"><input type="file" id="photo" name="photo" size="32" class="file" />
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td colspan="2"><input type="hidden" value="o_edit_defect" name="sect" id="sect" />
						<input type="hidden" value="<?=$fd['df_id']?>" name="df_id" id="df_id" />
						<input type="hidden" value="<?=$f['project_id']?>" name="project_id" id="project_id" />
						<input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/submit.png); border:none; width:111px;" />
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>
