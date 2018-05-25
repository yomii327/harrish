<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php }
require_once'includes/commanfunction.php';

$object = new COMMAN_Class();
function get_loc_name($subloc1){
	$s = "SELECT location_id, location_title from qa_task_locations where location_id ='".$subloc1."' ";
	$s = mysql_query($s);
	$r = mysql_fetch_array($s);
	return $r['location_title'];
}
$builder_id=$_SESSION['ww_builder_id'];
if(isset($_REQUEST['id'])) {
	$id=base64_decode($_REQUEST['id']);
	
}
$row = $object->selQRY('qa_num_sublocations', 'user_projects','project_id = "'.$_SESSION['idp'].'"');
$pr_num_sublocations = $row["qa_num_sublocations"];


if(isset($_POST['button_x'])){
	$parentId = 0;
	$locTree = '';
	$locTreeArray = array();
	if(isset($_POST['task']) and $_POST['task'] != ""){
		$task = $_POST['task'];
	}
/*	if(isset($_POST['otherloc']) and $_POST['otherloc'] != ""){
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
	}*/
	if(isset($_POST['status']) and $_POST['status'] != ""){
		$status = $_POST['status'];
	}
	if(isset($_POST['comment']) and $_POST['comment'] != ""){
		$comment = $_POST['comment'];
	}
	
/*	if(empty($locTreeArray)){
	}else{
		$locTree = $object->QArecursiveInsertLocation($locTreeArray, $parentId, $_SESSION['idp'], $builder_id, $locTree);	
	}
	$locArray = explode(' > ', $locTree);
	$locID = $locArray[0];
	$subLocID = end($locArray);*/
	if(isset($task)){
		$task_update = "UPDATE qa_task_monitoring SET
				task = '".addslashes(trim($_POST['task']))."',
				status = '".$_POST['status']."',
				comments = '".addslashes(trim($_POST['comment']))."',
				last_modified_date = NOW(),
				last_modified_by = ".$_SESSION['ww_builder_id']."
			WHERE task_id = '".$id."'";
		mysql_query($task_update) ;
		$_SESSION['progress_add'] = 'QA task updated successfully.';
		header('location:?sect=qa_task_monitoring');
	}else{
		$_SESSION['issue_add_err']='Progress task not added.';
	}
}
 
$q_sql = "SELECT * FROM qa_task_monitoring WHERE task_id = '".$id."'";

$q_issue = mysql_query($q_sql);
$q_rows = mysql_fetch_array($q_issue);

$rowTree_array =  explode(">",$q_rows['location_tree']);	
$loc=$rowTree_array[0];

$subloc1=$rowTree_array[1];
$subloc2=$rowTree_array[2];
$subloc3=$rowTree_array[3];
?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/add_edit_progress_task.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"sdate",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"edate",
			dateFormat:"%d-%m-%Y"
		});
		
	};
</script>
<style type="text/css">
@import "css/jquery.datepick.css";
table td{padding:5px;}
table{ margin-left:10px;}
#DRF, #DRT, #FBDF, #FBDT, #DRFPM, #DRTPM, #FBDFPM, #FBDTPM{
	background:#FFF;
	cursor:default;
	height:20px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	border-radius: 6px;
}
.error-edit-profile { width: 220px; }
</style>
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
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
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form  method="post"  enctype="multipart/form-data" name="add_editProgress" id="add_editProgress"  >
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/edit_qa_task.png);margin-top:-50px\9;"></div>
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-60px;">
							<tr>
								<td valign="top">Task <span class="req">*</span></td>
								<td><input name="task" type="text" class="input_small" id="task" value="<?php if(isset($q_rows['task_id']) && !empty($q_rows['task_id'])) echo $q_rows['task'] ; ?>" />
								</td>
							</tr>
							<tr>
								<td width="133" valign="top">Status</td>
								<td width="252"><select name="status" id="status"  class="select_box" style="margin-left:0px;" onchange="subLocate_sub(this.value);">
										<option value="">Select</option>
										<option <?php  if($q_rows['status']=='Yes'){ ?>  selected="selected" <?php   } ?> value="Yes">Yes</option>
										<option <?php  if($q_rows['status']=='No'){ ?>  selected="selected" <?php   } ?> value="No">No</option>
										<option <?php  if($q_rows['status']=='NA'){ ?>  selected="selected" <?php   } ?> value="NA">N/A</option>
									</select>
								</td>
							</tr>
							<tr>
								<td valign="top">Comment</td>
								<td><textarea name="comment" id="comment" class="text_area"><?php if(isset($q_rows['task_id']) && !empty($q_rows['task_id'])) echo $q_rows['comments'] ; ?>
</textarea>
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><input type="hidden" value="add_project" name="sect" id="sect" />
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