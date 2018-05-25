<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 2){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
$id=base64_decode($_GET['id']);
$resp_id = $_SESSION['ww_resp_id'];
		  
$q="SELECT * FROM ".DEFECTS." d 
	  LEFT JOIN ".PROJECTS." p ON d.project_id = p.id 
	  LEFT JOIN ".DEFECTSLIST." dl ON dl.dl_id = d.defect_type_id 
	  WHERE d.df_id = '$id' AND d.resp_id = '$resp_id'";
		  
if($obj->db_num_rows($obj->db_query($q)) == 0){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=ACCESS_DENIED_SCREEN?>";
</script>
<?php
}
$f=$obj->db_fetch_assoc($obj->db_query($q));

$s=stripslashes($f['status']);
$p=stripslashes($f['priority']);
$d=$f['defect_type_id'];
$proId=$f['project_id'];
$resp_id=$f['resp_id'];
$fk_b_id=$f['builder_id'];

// change date format for create_date
$create_date = $f['create_date'];
$created_on = date("d/m/Y", strtotime($create_date));

// change date format for fixed_date
if($f['fixed_date']!='0000-00-00'){
	$fixed_on = $f['fixed_date'];
}else{
	$fixed_on = '';
}

// change date format for fixed_by_date
if($f['fixed_by_date']!='0000-00-00'){
	$fixed_by_date = $f['fixed_by_date'];
}else{
	$fixed_by_date = '';
}

// get all assign_to under this responsible
$assign_to_name="<option>Select</option>";
$qa = "SELECT * FROM ".ASSIGN." WHERE resp_id='$resp_id'";
$ra=mysql_query($qa);

while($fa=mysql_fetch_assoc($ra)){
	if($f['assign_id']==$fa['assign_id']){
		$assign_to_name.="<option selected='selected' value='".$fa['assign_id']."'>".stripslashes($fa['assign_full_name'])."</option>";
		$assign_comp_name = stripslashes($fa['assign_comp_name']);
		$assign_phone = stripslashes($fa['assign_phone']);
		$assign_email = stripslashes($fa['assign_email']);
	}else{
		$assign_to_name.="<option value='".$fa['assign_id']."'>".stripslashes($fa['assign_full_name'])."</option>";
	}	
}
?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
function startAjax(){
	var fixed_by_date=document.getElementById('fixed_by_date').value;
	var assign_to_name=document.getElementById('assign_to_id').value;
	
	document.getElementById('edit_defect_response').innerHTML = '';
	
	if(fixed_by_date!='' && assign_to_name!='Select'){
		document.getElementById('edit_defect_process').style.visibility = 'visible';
		document.getElementById('edit_defect_response').style.visibility = 'hidden';
		return true;
	}else{
		var err = '<span class="edit_defect_emsg">* represent required fileds!<\/span><br/><br/>';
		document.getElementById('edit_defect_response').innerHTML = err;
		return false;
	}
	
	document.getElementById('edit_defect_process').style.visibility = 'visible';
	document.getElementById('edit_defect_response').style.visibility = 'hidden';
	return true;
}

function stopAjax(success){
	var result = '';
	if(success == 0){
		result = '<span class="edit_defect_emsg">* represent required fileds!<\/span><br/><br/>';
	}else if(success == 1){
		result = '<span class="edit_defect_msg">Updated successfully!<\/span><br/><br/>';
	}
	document.getElementById('edit_defect_process').style.visibility = 'hidden';
	document.getElementById('edit_defect_response').innerHTML = result;
	document.getElementById('edit_defect_response').style.visibility = 'visible';	
	
	return true;
}

function getAssignToInfo(){
	var assign_to_id = document.getElementById('assign_to_id').value;
	if(assign_to_id=='Select'){
		document.getElementById('assign_to_ph').value='';
		document.getElementById('assign_to_email').value='';
		document.getElementById('assign_to_comp').value='';		
		return false;
	}else{
		// get company
		$("#assign_to_comp_div").load('send_response.php?key='+assign_to_id+'&field=comp&type=assign&user=responsible');
		// get phone
		$("#assign_to_ph_div").load('send_response.php?key='+assign_to_id+'&field=ph&type=assign&user=responsible');
		// get email
		$("#assign_to_email_div").load('send_response.php?key='+assign_to_id+'&field=email&type=assign&user=responsible');
	}	
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

$(function() {
	$( "#fixed_date" ).datepicker();
});
</script>
<!-- Date Picker Ends -->
<div class="content_container">
	<div class="content_left">
		<div class="content_hd1"style="background-image:url(images/fix_defect.png);"></div>
		<div class="signin_form">
			<table width="100%" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td nowrap="nowrap">Raised on</td>
					<td><input type="text" class="input_small" readonly="readonly" value="<?=$created_on?>" /></td>
				</tr>
				<tr>
					<td nowrap="nowrap">Project Name</td>
					<td><input readonly="readonly" type="text" class="input_small" value="<?=stripslashes($f['project_name'])?>" /></td>
				</tr>
				<tr>
					<td nowrap="nowrap">Location</td>
					<td><input readonly="readonly" type="text" class="input_small" value="<?=stripslashes($f['area_room'])?>" /></td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top">Description</td>
					<td><textarea readonly="readonly" class="text_area"><?=stripslashes($f['defect_desc'])?>
</textarea></td>
				</tr>
				<tr>
					<td nowrap="nowrap">Priority</td>
					<td><input type="text" class="input_small" readonly="readonly" value="<?=stripslashes($f['priority'])?>" /></td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top">Picture</td>
					<td colspan="2">
					<div style="background-image:url(images/photo_2_bg.png); background-repeat:no-repeat; width:293px; height:210px; text-align:center;">
					<a href="?sect=issue_photo&photo=<?=$f['photo']?>" title="Click for large view">
					<img src="<?=$f['photo']?>" class="issue_img" style="width:280px; height:200px; border:none; margin-top:5px;" />
					</a>
					</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="content_right" style="margin-top:132px; margin-left:0px;">
		<form action="ajax_reply.php" method="post"  enctype="multipart/form-data" target="edit_defect_target" onsubmit="return startAjax();" >
			<iframe id="edit_defect_target" name="edit_defect_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
			<div id="edit_defect_process"><br />
				Sending request...<br/>
				<img src="images/loader.gif" /><br/>
			</div>
			<div id="edit_defect_response"></div>
			<div class="signin_form">
				<table width="100%" border="0" align="left" cellpadding="0" cellspacing="15">
					<tr>
						<td nowrap="nowrap">Fixed by Date <span class="req">*</span></td>
						<td><input type="text" id="fixed_by_date" class="input_small" name="fixed_by_date" readonly="readonly" value="<?=$fixed_by_date?>" /></td>
					</tr>
					<tr>
						<td nowrap="nowrap">Fixed Date</td>
						<td><input type="text" id="fixed_date" class="input_small" name="fixed_date" value="<?=$fixed_on?>" /></td>
					</tr>
					<tr>
						<td nowrap="nowrap">Type</td>
						<td>
							<select class="select_box" id="defect_type" name="defect_type">
							<?php
							$qd=$obj->db_query("SELECT * FROM ".PROJECTDEFECTS." pd 
												LEFT JOIN ".DEFECTSLIST." dl ON pd.fk_dl_id=dl.dl_id  
												WHERE pd.fk_p_id='$proId'");
							if($obj->db_num_rows($qd)>0){					
								while($fd=$obj->db_fetch_assoc($qd)){
								?>
									<option value="<?=$fd['dl_id']?>" <? if($d==$fd['dl_id']){?> selected="selected"<? }?>><?=stripslashes($fd['dl_title'])?></option>
								<?php	
								}
							}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap">Status</td>
						<td><select class="select_box" id="status" name="status">
								<option <? if($s=='Open'){?> selected="selected"<? }?>>Open</option>
								<option <? if($s=='Pending'){?> selected="selected"<? }?>>Pending</option>
								<option <? if($s=='In Progress'){?> selected="selected"<? }?>>In Progress</option>
								<option <? if($s=='Closed'){?> selected="selected"<? }?>>Closed</option>
							</select>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap">Assign To Contact Name <span class="req">*</span></td>
						<td>
						<select class="select_box" id="assign_to_id" name="assign_to_id" onchange="getAssignToInfo()">
							<?=$assign_to_name?>
						</select>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap">Assign To Company</td>
						<td>
						<div id="assign_to_comp_div" style="width:290px;">
<input id="assign_to_comp" name="assign_to_comp" type="text" class="input_small" readonly="readonly" value="<?=isset($assign_comp_name)?$assign_comp_name:''?>" />
						</div>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap">Assign To Phone</td>
						<td>
						<div id="assign_to_ph_div" style="width:290px;">
<input id="assign_to_ph" name="assign_to_ph" type="text" class="input_small" readonly="readonly" value="<?=isset($assign_phone)?$assign_phone:''?>" />
						</div>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap">Assign To Email</td>
						<td>
						<div id="assign_to_email_div" style="width:290px;">
<input id="assign_to_email" name="assign_to_email" type="text" class="input_small" readonly="readonly" value="<?=isset($assign_email)?$assign_email:''?>" />
						</div>
						</td>
					</tr>
					<tr>
						<td nowrap="nowrap" valign="top">Note &amp; Sign Off</td>
						<td><textarea class="text_area" id="defect_note" name="defect_note"><?=$f['defect_note']?>
</textarea></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="2"><input type="hidden" value="edit_defect_responsible" name="sect" id="sect" />
							<input type="hidden" value="<?=$f['df_id']?>" name="df_id" id="df_id" />
							<input type="hidden" value="<?=$f['project_id']?>" name="proId" id="proId" />							
							<input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/submit.png);" />
						</td>
					</tr>
				</table>
			</div>
		</form>
	</div>
</div>
