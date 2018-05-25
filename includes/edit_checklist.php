<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
<script language="javascript" type="text/javascript">
	window.location.href="<?=HOME_SCREEN?>";
</script>
<?php }

include_once("commanfunction.php");
$object = new COMMAN_Class();


$builder_id=$_SESSION['ww_builder_id'];

if(isset($_POST['button_x'])){
	if(isset($_POST['check_list_items_tags']) && !empty($_POST['check_list_items_tags'])){
		$check_list_items_tags = $_POST['check_list_items_tags'];
		$check_list_items_tags = trim($check_list_items_tags, ";");
		$check_list_items_tags = implode(";", array_map('trim', explode(";", $check_list_items_tags)));
		if ($check_list_items_tags != ""){
			$check_list_items_tags = $check_list_items_tags . ";";
		}else{
			$check_list_items_tags = '';
		}
	}else{
		$check_list_items_tags = '';
	}
	if(isset($_POST['check_list_items_name']) && !empty($_POST['check_list_items_name'])){
		$check_list_items_name=$_POST['check_list_items_name'];	
	}else{
		$check_list_items_name_err='<div class="error-edit-profile">The company name field is required</div>';
	}
	if($_POST['otherIssueTo'] != ''){
		$issueTo = $_POST['otherIssueTo'];
		$issueData = $object->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');
		$issueToData = array();
		foreach($issueData as $isData){
			if($isData['issue_to_name'] != ''){
				$issueToData[] = $isData['issue_to_name'];
			}
		} 
		$issueTo = $_POST['otherIssueTo'];	
		if(!in_array($issueTo, $issueToData)){
			$issueTo_insert = "INSERT INTO inspection_issue_to SET
								issue_to_name = '".addslashes(trim($issueTo))."',
								last_modified_date = NOW(),
								last_modified_by = '".$builder_id."',
								created_date = NOW(),
								created_by = '".$builder_id."',
								project_id = '".$_SESSION['idp']."'";
			mysql_query($issueTo_insert);
		}
	}else if(isset($_POST['issueTo']) && !empty($_POST['issueTo']) && $_POST['issueTo'] != 'otherIssue'){
		$issueTo = $_POST['issueTo'];	
	}
	
	if(isset($check_list_items_name)){
		if(trim($issueTo) == ''){
			$fixedDays = 0;
		}else{
			$fixedDays = 3;
		}
		$checklist_update = "UPDATE check_list_items SET
						check_list_items_name = '".addslashes(trim($check_list_items_name))."',
						check_list_items_tags = '".addslashes(trim($check_list_items_tags))."',
						issued_to = '".addslashes(trim($issueTo))."',
						fix_by_days = ".$fixedDays.",
						checklist_type = '".addslashes(trim($_POST['checklistType']))."',
						holding_point = '".addslashes(trim($_POST['holdePoint']))."',
						last_modified_date = NOW(),
						last_modified_by = ".$builder_id."
					WHERE
						check_list_items_id = '".base64_decode($_REQUEST['id'])."'";
		mysql_query($checklist_update);
		$_SESSION['checklist_edit'] = 'Checklist item updated successfully.';
		header('location:?sect=checklist');
	}else{
		$_SESSION['checklist_add_err']='Checklist item updated.';
	}
}

$qs=$obj->db_query("SELECT * FROM check_list_items WHERE check_list_items_id ='".base64_decode($_REQUEST['id'])."'"); 
$row=mysql_fetch_array($qs);

$issueData = $object->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0 ORDER BY issue_to_name');
$issueToData = array();
foreach($issueData as $isData){
	if($isData['issue_to_name'] != ''){
		$issueToData[] = $isData['issue_to_name'];
	}
} ?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/edit_checklist.js"></script>

<!-- Ajax Post -->
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
</style>
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form  method="post"  enctype="multipart/form-data" name="edit_checklist" id="edit_checklist" >
		
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/checklist_item_edit.png);margin-top:-50px\9;"></div>
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form">
						<table width="700" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-40px;">
							<?php if(isset($_SESSION['checklist_add_err'])) { ?><tr><td colspan="2" align="center"><div class="failure_r" style="width:250px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['checklist_add_err'];?></p><?php unset($_SESSION['checklist_add_err']);  ?></div></td></tr> <?php }?>
                            <tr>
								<td valign="top">Checklist&nbsp;Items&nbsp;Name <span class="req">*</span></td>
								<td><input name="check_list_items_name"  value="<?php echo trim(stripslashes($row['check_list_items_name']));?>"  type="text" class="input_small" id="check_list_items_name" /></td>
							</tr>
                            <tr>
								<td valign="top">Issue To</td>
								<td>
									<select name="issueTo" id="issueTo" class="select_box" style="margin-left:0px;" />
										<option value="">Select</option>
										<?php for($i=0; $i<sizeof($issueToData); $i++){?>
											<option value="<?php echo stripslashes($issueToData[$i]);?>" <?php if(stripslashes($row['issued_to']) == $issueToData[$i]){ echo 'selected="selected"';}?> ><?php echo stripslashes($issueToData[$i]);?></option>	
										<?php }?>
											<option value="otherIssue">other</option>
									</select>
									<span id="otherIssueTo" style="display:none;" ><input name="otherIssueTo" type="text" class="input_small" id="issueTo1" /><img src="images/redCross.png" id="issueTo2" onclick="closeThis();" /></span>
								</td>
							</tr>
							<tr>
								<td valign="top">Checklist Type</td>
								<td>
									<select name="checklistType" id="checklistType" class="select_box" style="margin-left:0px;" />
										<option value="Defect" <?php if($row['checklist_type'] == 'Defect'){ echo 'selected="selected"';}?>>Defect</option>	
										<option value="Incomplete Works" <?php if($row['checklist_type'] == 'Incomplete Works'){ echo 'selected="selected"';}?>>Incomplete Works</option>
										<option value="quality" <?php if($row['checklist_type'] == 'quality'){ echo 'selected="selected"';}?>>Quality</option>
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top">Hold Point</td>
								<td>
									<select name="holdePoint" id="holdePointYes" class="select_box" style="margin-left:0px;" >
										<option value="Yes" <?php if($row['holding_point'] == 'Yes'){ echo 'selected="selected"';}?> >Yes</option>
										<option value="No" <?php if($row['holding_point'] == 'No'){ echo 'selected="selected"';}?>>No</option>
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top">Location&nbsp;Items&nbsp;Tags</td>
								<td>
									<textarea name="check_list_items_tags" id="check_list_items_tags" class="text_area"><?php echo trim(stripslashes($row['check_list_items_tags']));?></textarea><br />
									Please seperate location by semicolon(;)
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<input type="hidden" value="add_project" name="sect" id="sect" />
									<input name="button" type="image" class="submit_btn" id="button" value="submit" src="images/update.png" style="border:none; width:111px;" />
									<a href="javascript:history.back();">
										<img src="images/back_btn.png" style="border:none; width:111px;" />
									</a>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
$("#issueTo").change(function(){
	if($(this).val() == 'otherIssue'){
		$(this).hide();
		$('#otherIssueTo').show();
		$('#issueTo1').show();
		$('#issueTo2').show();
	}
});
function closeThis(){
	$('#issueTo').show();
	$('#otherIssueTo').hide();
	$('#issueTo1').hide();
	$('#issueTo2').hide();
	$('#issueTo').val('');
}
</script>
