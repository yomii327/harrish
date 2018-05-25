<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
 if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php }
require_once'includes/commanfunction.php';
$object = new COMMAN_Class();

$builder_id = $_SESSION['ww_builder_id'];

$row = $object->selQRY('qa_num_sublocations', 'user_projects','project_id = "'.$_SESSION['idp'].'"');
$qa_num_sublocations = $row["qa_num_sublocations"];


if(isset($_POST['button_x'])){
	$parentId = 0;
	$locTree = '';
	$locTreeArray = array();
	if(isset($_POST['task']) and $_POST['task'] != ""){
		$task = $_POST['task'];
	}
	if(isset($_POST['otherloc']) and $_POST['otherloc'] != ""){
		$otherloc = $_POST['otherloc'];
		$locTreeArray[] = $_POST['otherloc'];
	}else{
		if(isset($_POST['loaction']) and $_POST['loaction'] != ""){
			$parentId = $_POST['loaction'];
			$loaction = $_POST['loaction'];
			if($locTree == ''){
				$locTree = $_POST['loaction'];
			}else{
				$locTree .= ' > '.$_POST['loaction'];
			}
		}
	}
	if(isset($_POST['txt_sublocation1']) and $_POST['txt_sublocation1'] != ""){
		$txt_sublocation1 = $_POST['txt_sublocation1'];
		$locTreeArray[] = $_POST['txt_sublocation1'];
	}else{
		if(isset($_POST['sublocation1']) and $_POST['sublocation1'] != ""){
			$parentId = $_POST['sublocation1'];
			$sublocation1 = $_POST['sublocation1'];
			if($locTree == ''){
				$locTree = $_POST['sublocation1'];
			}else{
				$locTree .= ' > '.$_POST['sublocation1'];
			}
		}
	}
	if(isset($_POST['txt_sublocation2']) and $_POST['txt_sublocation2'] != ""){
		$txt_sublocation2 = $_POST['txt_sublocation2'];
		$locTreeArray[] = $_POST['txt_sublocation2'];
	}else{
		if(isset($_POST['sublocation2']) and $_POST['sublocation2'] != ""){
			$parentId = $_POST['sublocation2'];
			$sublocation2 = $_POST['sublocation2'];
			if($locTree == ''){
				$locTree = $_POST['sublocation2'];
			}else{
				$locTree .= ' > '.$_POST['sublocation2'];
			}
		}
	}
	if(isset($_POST['txt_sublocation3']) and $_POST['txt_sublocation3'] != ""){
		$txt_sublocation3 = $_POST['txt_sublocation3'];
		$locTreeArray[] = $_POST['txt_sublocation3'];
	}else{
		if(isset($_POST['sublocation3']) and $_POST['sublocation3'] != ""){
			$parentId = $_POST['sublocation3'];
			$sublocation3 = $_POST['sublocation3'];
			if($locTree == ''){
				$locTree = $_POST['sublocation3'];
			}else{
				$locTree .= ' > '.$_POST['sublocation3'];
			}
		}
	}
	if(isset($_POST['status']) and $_POST['status'] != ""){
		$status = $_POST['status'];
	}
	if(isset($_POST['comment']) and $_POST['comment'] != ""){
		$comment = $_POST['comment'];
	}
	
	if(empty($locTreeArray)){
	}else{
		$locTree = $object->QArecursiveInsertLocation($locTreeArray, $parentId, $_SESSION['idp'], $builder_id, $locTree);	
	}
	$locArray = explode(' > ', $locTree);
	$locID = $locArray[0];
	$subLocID = end($locArray);
	
	if(isset($task)){
		$task_insert = "INSERT INTO qa_task_monitoring SET
						project_id = '".$_SESSION['idp']."',
						location_id = '".$locID."',
						sub_location_id = '".$subLocID."',
						task = '".addslashes(trim($task))."',
						status = '".$status."',
						comments = '".addslashes(trim($comment))."',
						created_by = '".$builder_id."',
						created_date = NOW(),
						last_modified_by = '".$builder_id."',
						last_modified_date = NOW(),
						location_tree = '".$locTree."'";
		mysql_query($task_insert);
		$_SESSION['progress_add']='QA task added successfully.';
		header('location:?sect=qa_task_monitoring');
	}else{
		$_SESSION['issue_add_err']='Error while inserting record';
	}
}?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/add_edit_qa_task.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<style type="text/css">
@import "css/jquery.datepick.css";
table td{padding:5px;}
table{ margin-left:10px;}
.error-edit-profile { width: 220px; }
</style>
<script language="javascript" type="text/javascript">
function SubLoc(val){
	if(val==""){
		//document.getElementById('div_sublocation1').style.display ='none';
		alert("Please select location");
		return false;
	}
	if(val!='show'){
		document.getElementById('div_otherloc_txt').style.display ='none';		
		document.getElementById('div_sublocation1').style.display ='block';
		document.getElementById('div_sublocation2').style.display ='block';
		document.getElementById('div_sublocation3').style.display ='block';		
		document.getElementById('div_txt_sublocation1').style.display ='none';
		document.getElementById('div_txt_sublocation2').style.display ='none';
		document.getElementById('div_txt_sublocation3').style.display ='none';
		AjaxShow("POST","progress_location.php?type=qa_task_subLocation_sub1 && proID="+val,"div_sublocation1");
	}else{
		document.getElementById('div_otherloc_txt').style.display ='block';
		document.getElementById('div_txt_sublocation1').style.display ='block';
		document.getElementById('div_txt_sublocation2').style.display ='block';
		document.getElementById('div_txt_sublocation3').style.display ='block';
		document.getElementById('div_sublocation1').style.display ='none';
		document.getElementById('div_sublocation2').style.display ='none';
		document.getElementById('div_sublocation3').style.display ='none';
	}
}

function SubLoc2(val){
	if(val==""){
		alert("Please select location");
		return false;
	}
	if(val!='other'){
		document.getElementById('div_otherloc_txt').style.display ='none';		
		document.getElementById('div_sublocation1').style.display ='block';
		document.getElementById('div_sublocation2').style.display ='block';
		document.getElementById('div_sublocation3').style.display ='block';		
		document.getElementById('div_txt_sublocation1').style.display ='none';
		document.getElementById('div_txt_sublocation2').style.display ='none';
		document.getElementById('div_txt_sublocation3').style.display ='none';
		AjaxShow("POST","progress_location.php?type=qa_task_subLocation_sub2 && proID="+val,"div_sublocation2");
	}else{
		document.getElementById('div_txt_sublocation1').style.display ='block';
		document.getElementById('div_txt_sublocation2').style.display ='block';
		document.getElementById('div_txt_sublocation3').style.display ='block';
		document.getElementById('div_sublocation2').style.display ='none';
		document.getElementById('div_sublocation3').style.display ='none';
	}
}

function SubLoc3(val){
	if(val==""){
		//document.getElementById('div_sublocation3').style.display ='block';		
		alert("Please select location");
		return false;
	}
	if(val!='other'){
		document.getElementById('div_otherloc_txt').style.display ='none';		
		document.getElementById('div_sublocation1').style.display ='block';
		document.getElementById('div_sublocation2').style.display ='block';
		document.getElementById('div_sublocation3').style.display ='block';		
		document.getElementById('div_txt_sublocation1').style.display ='none';
		document.getElementById('div_txt_sublocation2').style.display ='none';
		document.getElementById('div_txt_sublocation3').style.display ='none';
		AjaxShow("POST","progress_location.php?type=qa_task_subLocation_sub3 && proID="+val,"div_sublocation3");
	}else{
		document.getElementById('div_txt_sublocation3').style.display ='block';	
		document.getElementById('div_sublocation3').style.display ='none';		
		document.getElementById('div_txt_sublocation2').style.display ='block';
    }
}

function SubLoc4_other(val){
	if(val==""){
		document.getElementById('div_txt_sublocation3').style.display ='none';				
		alert("Please select location");
		return false;
	}
	if(val=='other'){	
		document.getElementById('div_txt_sublocation3').style.display ='block';				
	}
}
</script>
<style type="text/css">
#text_show {display:none;}
#sub_location_show{display:block;}
#sub_loc_hide{display:none;}
#sub_loc_hide_sub{display:none;}
</style>
<!-- Ajax Post -->
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
</style>
<link href="../style.css" rel="stylesheet" type="text/css" />
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form  method="post"  enctype="multipart/form-data" name="add_editProgress" id="add_editProgress" >
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/add_qa_task.png);margin-top:-50px\9;"></div>
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-60px;">
							<tr>
								<td valign="top">Task <span class="req">*</span></td>
								<td><input name="task"  type="text" class="input_small" id="task" />
								</td>
							</tr>
							<tr>
								<td nowrap="nowrap" valign="top">Location <span class="req">*</span></td>
								<td>
									<select name="loaction" id="loaction" class="select_box" style="margin-left:0px;" onchange="SubLoc(this.value);" >
										<option value="" >Select</option>
										<?php $sql="SELECT location_id, location_title FROM qa_task_locations WHERE project_id = ".$_SESSION['idp']." AND is_deleted=0 AND location_parent_id = 0";
$rs_m=mysql_query($sql);
while($row=mysql_fetch_array($rs_m)){ ?>
										<option value="<?php echo $row['location_id'];?>"> <?php echo $row['location_title'];?> </option>
										<?php } ?>
										<option value="show" id="other" >Other</option>
									</select>
									<div id="div_otherloc_txt" style="margin-top:10px; display:none;">
										<input name="otherloc" type="text" class="input_small" id="otherloc" />
									</div></td>
							</tr>
							<tr>
								<td nowrap="nowrap" valign="top" >Sub Location 1 <span class="req">*</span></td>
								<td>
									<div id="div_sublocation1" style="margin-top:10px;">
										<select name="sublocation1" id="sublocation1"  class="select_box" style="margin-left:0px;" onchange="subLocate_sub(this.value);">
											<option value="">Select</option>
										</select>
									</div>
									<div id="div_txt_sublocation1" style="margin-top:10px; display:none;">
										<input name="txt_sublocation1" id="txt_sublocation1" type="text" class="input_small"  />
									</div>
								</td>
							</tr>
							<tr>
								<td nowrap="nowrap" valign="top" >Sub Location 2 <span class="req">*</span></td>
								<td>
									<div id="div_sublocation2" style="margin-top:10px;">
										<select name="sublocation2" id="sublocation2"  class="select_box" style="margin-left:0px;" onchange="subLocate_sub(this.value);">
											<option value="">Select</option>
										</select>
									</div>
									<div id="div_txt_sublocation2" style="margin-top:10px; display:none;">
										<input name="txt_sublocation2" id="txt_sublocation2" type="text" class="input_small"  />
									</div>
								</td>
							</tr>
							<tr>
								<td nowrap="nowrap" valign="top" >Sub Location 3 <span class="req">*</span></td>
								<td>
									<div id="div_sublocation3" style="margin-top:10px;">
										<select name="sublocation3" id="sublocation3"  class="select_box" style="margin-left:0px;" onchange="subLocate_sub(this.value);">
											<option value="">Select</option>
										</select>
									</div>
									<div id="div_txt_sublocation3" style="margin-top:10px; display:none;">
										<input name="txt_sublocation3" id="txt_sublocation3" type="text" class="input_small"  />
									</div>
								</td>
							</tr>
							<tr>
								<td width="133" valign="top">Status</td>
								<td width="252">
									<select name="status" id="status"  class="select_box" style="margin-left:0px;">
										<option value="">Select</option>
										<option value="Yes">Yes</option>
										<option value="No">No</option>
										<option value="NA">N/A</option>
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top">Comment</td>
								<td>
									<textarea name="comment" id="comment" class="text_area" ></textarea>
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<input type="hidden" value="add_project" name="sect" id="sect" />
									<input name="button" type="image" class="submit_btn" id="button" value="submit"  src="images/save.png" style="border:none; width:111px;" />
									<a href="<?php echo $_SERVER['HTTP_REFERER'];?>"><img src="images/back_btn.png" style="border:none; width:111px;" /></a> </td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
