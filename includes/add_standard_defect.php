<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php }

include_once("commanfunction.php");
$obj = new COMMAN_Class();

$builder_id = $_SESSION['ww_builder_id'];

if(isset($_POST['button_x'])){
	if(isset($_POST['description']) && !empty($_POST['description'])){
		$description = $_POST['description'];	
	}else{
		$description_err='<div class="error-edit-profile">The description field is required</div>';
	}
	if(isset($_POST['tag']) && !empty($_POST['tag'])){
		$tag=$_POST['tag'];	
		$tag=trim($tag, ";");
		$tag = implode(";", array_map('trim', explode(";", $tag)));
		if ($tag != ""){
			$tag = trim($tag) . ";";
		}
	}else{
		$tag='';
	}
	if($_POST['otherIssueTo'] != ''){
		$issueData = $obj->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');
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
	
	if(isset($description)){
		if(trim($issueTo) == ''){
			$fixedDays = 0;
		}else{
			$fixedDays = 3;
		}
		$standard_insert = "INSERT INTO standard_defects SET
								description = '".addslashes(trim($description))."',
								tag = '".addslashes(trim($tag))."',
								issued_to = '".addslashes(trim($issueTo))."',
								fix_by_days = ".$fixedDays.",
								last_modified_date = NOW(),
								last_modified_by = '".$builder_id."',
								created_date = NOW(),
								created_by = '".$builder_id."',
								project_id = '".$_SESSION['idp']."'";
		mysql_query($standard_insert);
		$_SESSION['standard_defect_add'] = 'Standard defect added successfully.';
		header('location:?sect=standard_defect');
	}else{
		$_SESSION['standard_defect_add_err'] = 'Standard defect not added.';
	}
}

$issueData = $obj->selQRYMultiple('issue_to_name, company_name', 'inspection_issue_to', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0 ORDER BY issue_to_name');
$issueToData = array();
foreach($issueData as $isData){
	if($isData['issue_to_name'] != ''){
		if($isData['company_name'] != ''){
			$issueToData[] = $isData['issue_to_name']." (".$isData['company_name'].")";
		}else{
			$issueToData[] = $isData['issue_to_name'];
		}
	}
} ?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/add_edit_standard_defect_csv.js"></script>
<!-- Ajax Post -->
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
</style>
<link href="../style.css" rel="stylesheet" type="text/css" />
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form action="?sect=add_standard_defect" method="post"  enctype="multipart/form-data" name="add_edit_standard_defectFrm" id="add_edit_standard_defectFrm" >
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/hd_add_standard_defect.png);margin-top:0px\9;"></div>
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-60px;">
							<tr>
								<td valign="top">Description <span class="req">*</span></td>
								<td>
									<textarea name="description" id="description" class="text_area"></textarea>
								</td>
							</tr>
							<tr>
								<td valign="top">Issue To</td>
								<td>
									<select name="issueTo" id="issueTo" class="select_box" style="margin-left:0px;" />
										<option value="">Select</option>
										<?php for($i=0; $i<sizeof($issueToData); $i++){?>
											<option value="<?php echo $issueToData[$i];?>"><?php echo $issueToData[$i];?></option>	
										<?php }?>
											<option value="otherIssue">other</option>
									</select>
									<span id="otherIssueTo" style="display:none;" ><input name="otherIssueTo" type="text" class="input_small" id="otherIssueTo1"  /><img src="images/redCross.png" id="otherIssueTo2" onclick="closeThis();" /></span>
								</td>
							</tr>
							<tr>
								<td valign="top">Tags</td>
								<td>
									<textarea name="tag" id="tag" class="text_area"></textarea>
									<br/>
									Please seperate location by semicolon(;).
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<input name="button" type="image" class="green_small" id="button" value="Save" style="cursor: pointer;" />
									<!-- <input name="button" type="image" class="submit_btn" id="button" value="submit" src="images/save.png" style="border:none; width:111px;" /> -->
									
									<a href="javascript:void();" onclick="history.back();">
										<input name="back" type="image" class="green_small" id="button" value="Back">
									</a>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div><br />
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