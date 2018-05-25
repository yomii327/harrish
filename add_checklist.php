<style>
	table.innerTable{width:100%}table.innerTable tr td,table.innerTable tr td:nth-child(3){text-align:center}table.innerTable tr td:first-child{text-align:left}.btn-submit{background:url(images/submit.png) rgba(0,0,0,0);border:0;cursor:pointer;height:43px;text-indent:-999px;width:111px}
</style>
<?php


error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
#print_r($_REQUEST);

if(isset($_REQUEST['uniqueId'])){
	#print_r($_REQUEST);
	$qaitpData = array();
	//$qaitpData = $obj->selQRYMultiple('id, checklist_name', 'check_list_items_project', 'is_deleted=0 and id="'. $_REQUEST['checklistId'] .'"');
	#print_r($qaitpData);
	$qaitpStatusDataArr = array();
	$q = "SELECT clip.id as checklist_id, clip.checklist_name, clip.project_id, qa_pclt.id as task_id, qa_pclt.task_name, qa_pclt.task_comment, qa_pclt.comment_mandatory, qa_pclt.task_status FROM check_list_items_project AS clip JOIN qa_project_checklist_task AS qa_pclt ON qa_pclt.project_checklist_id = clip.id WHERE clip.id = '".$_REQUEST['checklistId']."' AND clip.is_deleted = 0 AND  qa_pclt.is_deleted = 0";
	$res = mysql_query($q);
	$flag = 0;
	$checklist_name = '';
	$checklist_id = $_REQUEST['checklistId'];

	$location_id = $_REQUEST['location'];
	$subLocation_id = $_REQUEST['subLocation'];
	$sub_subLocation_id = $_REQUEST['sub_subLocation'];
	$subSubLocation3_id = $_REQUEST['subSubLocation3'];
	$taskIdForFlag = 0;
	while($qData = mysql_fetch_array($res)){
		$checklist_name = $qData['checklist_name'];
		$qaitpStatusDataArr[ $qData['checklist_name'] ][] = array(
			'task_id' => $qData['task_id'],
			'task_name' => $qData['task_name'],
			'comment_mandatory' => $qData['comment_mandatory'],
			'comment' => $qData['task_comment'],
			'status' => $qData['task_status'],
		);
		if($qData['task_status'] == 'Yes'){
			$taskIdForFlag = $qData['task_id'];
		}
		$flag = 1;
	}
	//echo "<pre>"; print_r($qaitpStatusDataArr); die();
	
	// $qaitpStatusDataArr = array();
	// foreach($qaitpData as $qaStatusRow) {
	// 	$qaitpStatusDataArr[ $qaStatusRow['id'] ][] = array(
	// 		'itemId' => $qaStatusRow['id'],
	// 		'itemName' => $qaStatusRow['checklist_name'],
	// 		'itemStatus' => '',
	// 	);
	// }
	
	#Get parent location
	$locationData = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'is_deleted = 0 AND location_parent_id = 0 AND project_id = '.$_GET['projID']);

	if(!empty($location_id)){
		$subLocationData = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'is_deleted = 0 AND location_parent_id = '.$location_id.' AND project_id = '.$_GET['projID']);
	}

	if(!empty($subLocation_id)){
		$sub_subLocationData = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'is_deleted = 0 AND location_parent_id = '.$subLocation_id.' AND project_id = '.$_GET['projID']);
	}

	if(!empty($sub_subLocation_id)){
		$subSubLocationData3 = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'is_deleted = 0 AND location_parent_id = '.$sub_subLocation_id.' AND project_id = '.$_GET['projID']);
	}
	
	#Status Array
	$statusArr = array('Open', 'Closed', 'Fixed');
	if($flag == 1){?>
		<fieldset class="roundCorner">
			<legend style="color:#000000;">Add <?php echo $checklist_name; ?></legend>
				<form action="" method="post" name="editQaCheckListForm" id="editQaCheckListForm">
					<input type="hidden" name="project_id" value="<?=$_GET['projID']?>" />
					<input type="hidden" name="checklist_name" value="<?php echo $checklist_name; ?>" />
					<input type="hidden" name="checklist_id" value="<?php echo $checklist_id; ?>" />
					<table border="0" width="100%" class="simpleTable editTables" style="margin:0">
						<tr>
							<td>Location</td>
							<td>&nbsp;:&nbsp;</td>
							<td>
								<select style="width:220px;background-image:url(images/selectSpl.png);" class="select_box margin-class" onchange="editShowLocation(this.value, <?=$_GET['projID']?>);" id="location" name="location">
									<option value="">Select</option>
									<?php foreach($locationData as $location) { ?>
										<option value="<?=$location['location_id']?>" <?php if($location['location_id'] == $location_id){echo 'selected="selected"';} ?>><?=$location['location_title']?></option>
									<?php } ?>
								</select>
							</td>
							<td>Sub Location</td>
							<td>&nbsp;:&nbsp;</td>
							<td>
								<div id="editShowSubLocation">
									<select style="width:220px;background-image:url(images/selectSpl.png);" class="select_box margin-class" onchange="editShowSubLocation(this.value, <?=$_GET['projID']?>);" id="addShowSubLocation" name="addShowSubLocation">
										<option value="">Select</option>
										<?php foreach($subLocationData as $subLocation) { ?>
											<option value="<?=$subLocation['location_id']?>" <?php if($subLocation['location_id'] == $subLocation_id){echo 'selected="selected"';} ?>><?=$subLocation['location_title']?></option>
										<?php } ?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td>Sub Location1</td>
							<td>&nbsp;:&nbsp;</td>
							<td>
								<div id="editShowSubLocation1">
									<select style="width:220px;background-image:url(images/selectSpl.png);" class="select_box margin-class"  onchange="editShowSubLocation1(this.value, <?=$_GET['projID']?>);" id="addShowSubLocation1" name="addShowSubLocation1">
										<option value="">Select</option>
										<?php foreach($sub_subLocationData as $subSubLocation) { ?>
											<option value="<?=$subSubLocation['location_id']?>" <?php if($subSubLocation['location_id'] == $sub_subLocation_id){echo 'selected="selected"';} ?>><?=$subSubLocation['location_title']?></option>
										<?php } ?>
									</select>
								</div>
							</td>
							<td>Sub Location2</td>
							<td>&nbsp;:&nbsp;</td>
							<td>
								<div id="editShowSubLocation2">
									<select style="width:220px;background-image:url(images/selectSpl.png);" class="select_box margin-class" id="addShowSubLocation2" name="addShowSubLocation2">
										<option value="">Select</option>
										<?php foreach($subSubLocationData3 as $subSubLocation3) { ?>
											<option value="<?=$subSubLocation3['location_id']?>" <?php if($subSubLocation3['location_id'] == $subSubLocation3_id){echo 'selected="selected"';} ?>><?=$subSubLocation3['location_title']?></option>
										<?php } ?>
									</select>
								</div>
							</td>
						</tr>
						<tr>
							<td>Status</td>
							<td>&nbsp;:&nbsp;</td>
							<td>
								<select style="width:220px;background-image:url(images/selectSpl.png);" class="select_box margin-class" id="status" name="status">
									<option value="">Select</option>
									<?php foreach($statusArr as $val) { ?>
										<option value="<?=$val?>" <?php if($val === strtolower($qaitpData[0]['status'])){ echo 'selected="selected"'; } ?>><?=ucwords($val)?></option>
									<?php } ?>
								</select>
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?php foreach($qaitpStatusDataArr as $key => $qasRow) { ?>
							<tr style="background-color:#ddd;">
								<td colspan="6"><div style="text-align:left"><?=$key?></div></td>
							</tr>						
							<tr>
								<td colspan="6">
									<table class="innerTable">
										<?php $disable='';
										foreach($qasRow as $rows) { $task_id = $rows['task_id']; 
										$evdID = $obj->selQRY('task_id', 'qa_ncr_task_detail', 'is_deleted = 0 AND task_id = '.$task_id);
										?>
											<tr>
												<td width="45%" <?php if($rows['status'] == 'Yes'){echo 'style="color:red"';} ?> >
													<input type="hidden" name="checklist_arr[<?=$key?>][]" value="<?=$rows['task_id'].'__'.$rows['task_name']?>" />
													<input type="hidden" name="checklist_comment_arr[<?=$task_id?>]" value="<?=$rows['comment']?>" />

													<input type="hidden" class="taskId" name="task_id" value="<?=$task_id;?>">
													<input type="hidden" name="userRoll" id="userRoll" class="input_small" value="<?=$_SESSION['userRole'];?>" >
													<?=$rows['task_name']?>
												</td>

												<td width="10%">
													<input type="radio" class="evedence_pop <?php if($rows['status'] == 'Yes'){echo 'hold_point_yes';}else{echo 'hold_point_no';} ?>" name="status_arr[<?=$rows['task_id']?>]" value="Yes" <?php echo $disable; ?> /> Yes
													<?php if($evdID){ ?>
													<!-- button class="evedence_pop" style="padding:5px;color:#fff;background:green" />evedence</button -->
													<input  class="evedence_pop" style="background-image:url(images/task_detail_small.png);font-size:0px; border:none; height: 29px; width:77px;float:right;" type="button">
													<? }// endif?>
												</td>
												<td width="10%">
													<input type="radio" class="nonconf_pop <?php if($rows['status'] == 'Yes'){echo 'radio_button_no';} ?>" name="status_arr[<?=$rows['task_id']?>]" value="No" /> No
												</td>
												<td width="10%">
													<input type="radio" class="<?php if($rows['status'] == 'Yes'){echo 'radio_button_no';} ?>" name="status_arr[<?=$rows['task_id']?>]" value="NA" /> NA
												</td>

												<td width="25%"><textarea type="text" class="textAreaVal <?php if($rows['comment_mandatory'] == 'Yes'){ echo 'mandatory'; }?>" name="checklist_comment_arr_new[<?=$task_id?>]"><?=$rows['comment']?></textarea>
													<?php if($rows['comment_mandatory'] == 'Yes'){ ?>
														<lable for="checklist_comment_arr_new[<?=$task_id?>]" id="checklist_comment_arr_new[<?=$task_id?>]" generated="true" class="error" style="display:none;">
															<div class="error-edit-profile">The comment field is required</div>
														</lable>
													<?php } ?>
												</td>
												
											</tr>
											<?php 
											 //<?php if($rows['status'] == 'Yes'){echo 'checked="checked"';} ? >
											if($rows['status'] == 'Yes'){ $disable='disabled=true';}
											//if($taskIdForFlag == $task_id){ $disable='disabled=true';}
										} ?>
									</table>
								</td>
							</tr>
						<?php } ?>
						<tr>
							<td colspan="6">
								<div style="text-align:center;">
									<input type="button" onclick="addNewQaChecklistData();" id="submitEditTask" value="submit" class="btn-submit sub-btn" />
								</div>
							</td>
						</tr>
					</table>
				</form>
		</fieldset>
<?php
	} else {
		echo '<span style="color:#000;">Records Not Found!</span>';
	}
}

# Add new checklist item.
if(isset($_GET['antiqueID'])) {
	#Get location tree name.
	$locationId = $locTree = '';
	if(!empty($_POST['location'])) {
		if(!empty($_POST['addShowSubLocation'])) {
			if(!empty($_POST['addShowSubLocation1'])) {
				if(!empty($_POST['addShowSubLocation2'])) {
					$locationId = $_POST['addShowSubLocation2'];
				} else {
					$locationId = $_POST['addShowSubLocation1'];
				}
			} else {
				$locationId = $_POST['addShowSubLocation'];
			}			
		} else {
			$locationId = $_POST['location'];
		}		
	}

	//echo "<pre>"; print_r($_POST['task_status_id_arr']); echo "<=========="; die();
	if(!empty($locationId)) {
		$locationData = $obj->selQRYMultiple('location_name_tree', 'project_locations', 'is_deleted = 0 AND location_id = '. $locationId);
		$locTree = $locationData[0]['location_name_tree'];
	}

	//echo "====="; print_r($locTree); die();

	if(!empty($locTree)) {
		$locDepth = explode('>', $locTree);
		$locDepth = count($locDepth);
	} else { $locDepth = ''; }
	//echo "===fff=="; print_r($locTree); die();

	//echo "====="; print_r($locationData); die();
	# insert qa_itp_checklist data.

	$insrted_id = substr(time(), 0, 3) . substr(time(), 5) . rand(0, 999) . $_SESSION['ww_builder_id'];
	$signature1 = '';
	$signature2 = '';
	if(isset($_POST['image_name'][0]) && !empty($_POST['image_name'][0])){
		$signature1 = $_POST['image_name'][0];
	}

	if(isset($_POST['image_name'][1]) && !empty($_POST['image_name'][1])){
		$signature2 = $_POST['image_name'][1];
	}

	$qaChecklistData = "qa_checklist_id = '".$insrted_id."',
		project_checklist_id = '".$_POST['checklist_id']."',
		project_id = '".$_POST['project_id']."',
		location_id = '".$_POST['location']."',
		sub_location_id = '".$_POST['addShowSubLocation']."',
		sub_location1_id = '".$_POST['addShowSubLocation1']."',
		sub_location2_id = '".$_POST['addShowSubLocation2']."',
		location_tree = '".$locTree."',
		location_depth = '".$locDepth."',
		status = '".$_POST['status']."',
		sc_sign = '".$signature1."',
		contractor_sign = '".$signature2."',
		created_by = '".$_SESSION['ww_builder_id']."',
		created_date = NOW(),
		last_modified_by = '".$_SESSION['ww_builder_id']."',
		last_modified_date = NOW(),
		sub_contractor_name = '".$_POST['subContractorName']."',
		original_modified_date = NOW()";

	//echo "INSERT INTO qa_checklist SET ".$qaItpChecklistData . '<===insert=='; die();
	mysql_query("INSERT INTO qa_checklist SET ".$qaChecklistData);

	if(!empty($signature1)){
		$attchdata = "task_detail_id = '".$insrted_id."',
		attachment_title = 'Web_image',
		attachment_description = '',
		attachment_file_name = '".$signature1."',
		attachment_type = 'image_sign_sub_con',
		project_id = '".$_POST['project_id']."',
		created_by = '".$_SESSION['ww_builder_id']."',
		created_date = NOW(),
		last_modified_by = '".$_SESSION['ww_builder_id']."',
		last_modified_date = NOW(),
		original_modified_date = NOW()";
		$attsql = "INSERT INTO qa_ncr_attachments SET ".$attchdata; 

		mysql_query($attsql);
	}

	if(!empty($signature2)){
		$attchdata1 = "task_detail_id = '".$insrted_id."',
		attachment_title = 'Web_image',
		attachment_description = '',
		attachment_file_name = '".$signature2."',
		attachment_type = 'image_sign_con',
		project_id = '".$_POST['project_id']."',
		created_by = '".$_SESSION['ww_builder_id']."',
		created_date = NOW(),
		last_modified_by = '".$_SESSION['ww_builder_id']."',
		last_modified_date = NOW(),
		original_modified_date = NOW()";
		$attsql1 = "INSERT INTO qa_ncr_attachments SET ".$attchdata1; 

		mysql_query($attsql1);
	}
	//$insrted_id = mysql_insert_id();

	//echo $insrted_id . '<=pp=='; die();
	//$qa_itp_checklist_ins = $obj->insert('qa_checklist', $qaItpChecklistData);
	
	# Get last inserted id.
	// $chkData = mysql_query('SELECT max(`qa_checklist_id`) AS id FROM `qa_checklist`');
	// $chkData = mysql_fetch_array($chkData);
	// $qa_checklist_id = $chkData['id'];
	
	# Insert data into qa_itp_checklist_status
	$qaChkStatus = '';
	foreach($_POST['checklist_arr'] as $key => $val) {
		foreach($val as $row) {
			$chkVal = explode('__', $row);
			$dat = "''";
			if(array_key_exists($chkVal[0], $_POST['status_arr'])) {
				$qaChkStatus = $_POST['status_arr'][$chkVal[0]];
				if($qaChkStatus == 'Yes'){
					$dat = 'NOW()';
				}
			} else {
				$qaChkStatus = '';
			}

			$qaChkComment = '';
			if(array_key_exists($chkVal[0], $_POST['checklist_comment_arr_new'])) {
				$qaChkComment = $_POST['checklist_comment_arr_new'][$chkVal[0]];
			}

			$task_status_id = '';
			if(array_key_exists($chkVal[0], $_POST['task_status_id_arr'])) {
				$task_status_id = $_POST['task_status_id_arr'][$chkVal[0]];
			}
			//$task_status_id = substr(time(), 0, 3) . substr(time(), 5) . rand(0, 999) . $_SESSION['ww_builder_id'];

			$qaChecklistStatusData = "task_status_id = '".$task_status_id."',
				qa_checklist_id = '".$insrted_id."',
				checklist_task_id = '".$chkVal[0]."',
				project_id = '".$_POST['project_id']."',
				status = '".$qaChkStatus."',
				task_comment = '".$qaChkComment."',
				task_name = '".$chkVal[1]."',
				created_by = '".$_SESSION['ww_builder_id']."',
				created_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				completion_date = ".$dat.",
				last_modified_date = NOW(),
				original_modified_date = NOW()";
			
			//echo "INSERT INTO qa_checklist_task_status SET ".$qaChecklistStatusData . '<===insert=='; 
			mysql_query("INSERT INTO qa_checklist_task_status SET ".$qaChecklistStatusData);
		}//End of inner foreach
	}//End of outer foreach
}
?>
