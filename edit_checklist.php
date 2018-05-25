<style>
table.innerTable { width: 100%; }
table.innerTable tr td,
table.innerTable tr td:nth-child(3) { text-align: center; }
table.innerTable tr td:first-child { text-align: left; }
.btn-submit { background: url("images/update.png") repeat scroll 0 0 rgba(0, 0, 0, 0); border: 0 none; cursor: pointer; height: 43px; text-indent: -999px; width: 111px; }
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
	$q = "SELECT qc.project_checklist_id, qc.location_id, qc.sub_location_id, qc.sub_location1_id, qc.sub_location2_id, qc.location_tree, qc.status as checklist_status, qcTask.task_name, qcTask.task_comment, qcTask.status, qcTask.completion_date, qcTask.task_status_id, qcTask.checklist_task_id FROM qa_checklist AS qc JOIN qa_checklist_task_status AS qcTask ON qcTask.qa_checklist_id = qc.qa_checklist_id WHERE qc.qa_checklist_id = '".$_REQUEST['qaChecklistId']."' AND qc.is_deleted = 0 AND  qcTask.is_deleted = 0";
	$res = mysql_query($q);
	$flag = 0;
	$checklist_name = '';
	$checklist_id = $_REQUEST['qaChecklistId'];

	// $location_id = $_REQUEST['location'];
	// $subLocation_id = $_REQUEST['subLocation'];
	// $sub_subLocation_id = $_REQUEST['sub_subLocation'];
	// $subSubLocation3_id = $_REQUEST['subSubLocation3'];
	$task_name_arr = array();
	$task_name_str = '';
	$taskIdForFlag = 0;
	$allTaskIds = '';
	while($qData = mysql_fetch_array($res)){
		$checklist_name = $qData['checklist_name'];
		$location_id = $qData['location_id'];
		$subLocation_id = $qData['sub_location_id'];
		$sub_subLocation_id = $qData['sub_location1_id'];
		$subSubLocation3_id = $qData['sub_location2_id'];
		$checklist_status = $qData['checklist_status'];

		$project_checklist_id = $qData['project_checklist_id'];
		if(!empty($task_name_str)){
			$task_name_str .= ',"'.$qData['task_name'].'"';
			$allTaskIds .= ','.$qData['checklist_task_id'];
		}else{
			$task_name_str = '"'.$qData['task_name'].'"';
			$allTaskIds = $qData['checklist_task_id'];
		}

		$qaitpStatusDataArr[ $qData['checklist_name'] ][] = array(
			'task_id' => $qData['task_status_id'],
			'task_name' => $qData['task_name'],
			//'comment_mandatory' => $qData['comment_mandatory'],
			'comment' => $qData['task_comment'],
			'status' => $qData['status'],
			'checklist_task_id' => $qData['checklist_task_id']
		);
		if($qData['status'] == 'Yes'){
			$taskIdForFlag = $qData['task_status_id'];
		}
		$flag = 1;
	}

	//echo $task_name_str . "<<==="; die();
		//SELECT * FROM `qa_project_checklist_task` WHERE `project_checklist_id` = 20
	$q1 = "SELECT id, task_name, task_status, task_comment, comment_mandatory FROM qa_project_checklist_task WHERE project_checklist_id = '".$project_checklist_id."' AND is_deleted = 0 AND task_name NOT IN(".$task_name_str.")";
	$res1 = mysql_query($q1);
	while($qData1 = mysql_fetch_array($res1)){
		 $qaitpStatusDataArr[ $checklist_name ][] = array(
			'task_id' => $qData1['id'],
			'task_name' => $qData1['task_name'],
			'comment_mandatory' => $qData1['comment_mandatory'],
			'comment' => $qData1['task_comment'],
			'status' => $qData1['task_status'],
			'checklist_task_id' => $qData1['id']
		 );

		if(!empty($allTaskIds)){
			$allTaskIds .= ','.$qData1['id'];
		}else{
			$allTaskIds = $qData1['id'];
		}

		if($qData1['task_status'] == 'Yes'){
			$taskIdForFlag = $qData1['id'];
		}
		$flag = 1;
	}

	$allStatusIdData = array(
		);
	$q2 = "SELECT id, task_status, comment_mandatory FROM qa_project_checklist_task WHERE project_checklist_id = '".$project_checklist_id."' AND is_deleted = 0 AND id IN(".$allTaskIds.")";
	$res2 = mysql_query($q2);
	while($qData2 = mysql_fetch_array($res2)){
		$allStatusIdData[ $qData2['id'] ]= array(
			'commentMandatory' => $qData2['comment_mandatory'],
			'checkPoint' => $qData2['task_status']
		);
	}

	//print_r($qaitpStatusDataArr); echo "<<=allStatusIdData="; die();

	//echo $checklist_name.'<<=checklist=='.$location_id.'<<=l1=='.$subLocation_id .'<<=l2=='. $sub_subLocation_id.'<<=l3=='.$subSubLocation3_id. " <<=l4== <pre>"; print_r($qaitpStatusDataArr); die();

	$project_id = $_REQUEST['projID'];
	$locationData = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'is_deleted = 0 AND location_parent_id = 0 AND project_id = '.$project_id);

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
			<legend style="color:#000000;">Edit <?php echo $checklist_name; ?></legend>
				<form action="" method="post" name="editCheckListForm" id="editCheckListForm">
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
									<select style="width:220px;background-image:url(images/selectSpl.png);" class="select_box margin-class" onchange="editShowSubLocation(this.value, <?=$_GET['projID']?>);" id="editShowSubLocation" name="editShowSubLocation">
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
									<select style="width:220px;background-image:url(images/selectSpl.png);" class="select_box margin-class"  onchange="editShowSubLocation1(this.value, <?=$_GET['projID']?>);" id="editShowSubLocation1" name="editShowSubLocation1">
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
									<select style="width:220px;background-image:url(images/selectSpl.png);" class="select_box margin-class" id="editShowSubLocation2" name="editShowSubLocation2">
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
										<option value="<?=$val?>" <?php if($val == $checklist_status){ echo 'selected="selected"'; } ?>><?=ucwords($val)?></option>
									<?php } ?>
								</select>
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?php foreach($qaitpStatusDataArr as $key => $qasRow) { $checked = 'checked="checked"'; ?>
							<tr style="background-color:#ddd;">
								<td colspan="6"><div style="text-align:left"><?=$key?></div></td>
							</tr>						
							<tr>
								<td colspan="6">
									<table class="innerTable">
										<?php $disable='';
										foreach($qasRow as $rows) { $task_id = $rows['task_id']; $checklist_task_id = $rows['checklist_task_id'];
										$evdID = $obj->selQRY('task_id', 'qa_ncr_task_detail', 'is_deleted = 0 AND task_id = '.$task_id);
										?>
											<tr>
												<td width="45%" <?php if($allStatusIdData[$checklist_task_id]['checkPoint'] == 'Yes'){echo 'style="color:red"';} ?> >
													<input type="hidden" name="checklist_arr[<?=$rows['task_id']?>][]" value="<?=$rows['task_name']?>" />
													<input type="hidden" name="checklist_comment_arr[<?=$task_id?>]" value="<?=$rows['comment']?>" />
													
													<input type="hidden" class="taskId" name="task_id" value="<?=$task_id;?>">
													<input type="hidden" name="userRoll" id="userRoll" class="input_small" value="<?=$_SESSION['userRole'];?>" >
													<?=$rows['task_name']?>
												</td>

												<td width="10%">
													<input type="radio" class="evedence_pop <?php if($allStatusIdData[$checklist_task_id]['checkPoint'] == 'Yes' && $rows['status'] !== 'Yes'){echo 'hold_point_yes';}else{echo 'hold_point_no';} ?>" name="status_arr[<?=$rows['task_id']?>]" value="Yes" <?php if($rows['status'] == 'Yes'){echo $checked;} ?> <?php echo $disable; ?> /> Yes

													<?php if($evdID){ ?>
													<!-- button class="evedence_pop" style="padding:5px;color:#fff;background:green" />evedence</button -->
													<input  class="evedence_pop" style="background-image:url(images/task_detail_small.png);font-size:0px; border:none; height: 29px; width:77px;float:right;" type="button">
													<? }// endif?>
												</td>
												<td width="10%">
													<input type="radio" class="nonconf_pop <?php if($allStatusIdData[$checklist_task_id]['checkPoint'] == 'Yes' && $rows['status'] !== 'Yes'){echo 'radio_button_no';} ?>" name="status_arr[<?=$rows['task_id']?>]" value="No" <?php if($rows['status'] == 'No'){echo 'checked="checked"';} ?> /> No
												</td>
												<td width="10%">
													<input type="radio" class="<?php if($allStatusIdData[$checklist_task_id]['checkPoint'] == 'Yes' && $rows['status'] !== 'Yes'){echo 'radio_button_no';} ?>" name="status_arr[<?=$rows['task_id']?>]" value="NA" <?php if($rows['status'] == 'NA'){echo 'checked="checked"';} ?> /> NA
												</td>

												<td width="25%">
													<textarea type="text" class="textAreaVal <?php if($allStatusIdData[$checklist_task_id]['commentMandatory'] == 'Yes'){ echo 'mandatory'; }?>" name="checklist_comment_arr_new[<?=$task_id?>]"><?=$rows['comment']?></textarea>
													<?php if($allStatusIdData[$checklist_task_id]['commentMandatory'] == 'Yes'){ ?>
														<lable for="checklist_comment_arr_new[<?=$task_id?>]" id="checklist_comment_arr_new[<?=$task_id?>]" generated="true" class="error" style="display:none;">
															<div class="error-edit-profile">The comment field is required</div>
														</lable>
													<?php } ?>
												</td>
											</tr>
											<?php 
											if($allStatusIdData[$checklist_task_id]['checkPoint'] == 'Yes' && $rows['status'] !== 'Yes'){ $disable='disabled=true';}
											//if($taskIdForFlag == $task_id){ $disable='disabled=true';}
										} ?>
									</table>
								</td>
							</tr>
						<?php } ?>
						<tr>
							<td colspan="6">
								<div style="text-align:center;">
									<input type="button" onclick="editCheckListSubmit();" id="submitEditTask" value="submit" class="btn-submit sub-btn" />
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

if(isset($_GET['antiqueID'])){
	#print_r($_REQUEST);die;
	#Get location tree name.
	$locationId = $locTree = '';
	
	if(!empty($_POST['location'])) {
		if(!empty($_POST['editShowSubLocation'])) {
			if(!empty($_POST['editShowSubLocation1'])) {
				if(!empty($_POST['editShowSubLocation2'])) {
					$locationId = $_POST['editShowSubLocation2'];
				} else {
					$locationId = $_POST['editShowSubLocation1'];
				}
			} else {
				$locationId = $_POST['editShowSubLocation'];
			}			
		} else {
			$locationId = $_POST['location'];
		}		
	}

	//echo "<pre>"; print_r($_POST); echo "<=========="; die();
	if(!empty($locationId)) {
		$locationData = $obj->selQRYMultiple('location_name_tree', 'project_locations', 'is_deleted = 0 AND location_id = '. $locationId);
		$locTree = $locationData[0]['location_name_tree'];
	}

	if(!empty($locTree)) {
		$locDepth = explode('>', $locTree);
		$locDepth = count($locDepth);
	} else { $locDepth = ''; }

	$checklist_id = $_POST['checklist_id'];

	//echo $locationId."<<=locationId=" . $checklist_id.'<<=checklist_id='; die();
	//echo "<pre>"; print_r($_POST['image_name']); die();
	$signature1 = '';
	$signature2 = '';
	if(isset($_POST['image_name'][0]) && !empty($_POST['image_name'][0])){
		$signature1 = $_POST['image_name'][0];
	}

	if(isset($_POST['image_name'][1]) && !empty($_POST['image_name'][1])){
		$signature2 = $_POST['image_name'][1];
	}

	#Update qa_itp_checklist	
	$checklistQuery = "UPDATE qa_checklist SET
			location_id = '".$_POST['location']."',
			sub_location_id = '".$_POST['editShowSubLocation']."',
			sub_location1_id = '".$_POST['editShowSubLocation1']."',
			sub_location2_id = '".$_POST['editShowSubLocation2']."',
			location_tree = '". $locTree ."',
			location_depth = '".$locDepth."',
			status = '".$_POST['status']."',
			sc_sign = '".$signature1."',
			contractor_sign = '".$signature2."',
			last_modified_by = '".$_SESSION['ww_builder_id']."',
			sub_contractor_name = '".$_POST['subContractorName']."',
			last_modified_date = NOW()
		WHERE
			is_deleted = 0 AND qa_checklist_id = '".$checklist_id."'";
			
	#echo $ChecklistQuery;die;
	mysql_query($checklistQuery);

	//update signature start
	$attchdata = "attachment_file_name = '".$signature1."',
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				last_modified_date = NOW(),
				original_modified_date = NOW()";
	$attsql = "UPDATE  qa_ncr_attachments SET ".$attchdata. " WHERE attachment_type = 'image_sign_sub_con' AND task_detail_id = ".$checklist_id;
	mysql_query($attsql);

	$attchdata2 = "attachment_file_name = '".$signature2."',
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				last_modified_date = NOW(),
				original_modified_date = NOW()";
	$attsql2 = "UPDATE  qa_ncr_attachments SET ".$attchdata2. " WHERE attachment_type = 'image_sign_con' AND task_detail_id = ".$checklist_id;
	mysql_query($attsql2);
	//update signature end
	
	#Update qa_itp_checklist_status.
	$status_arr = $_POST['status_arr'];
	$completed_date = '';
	
	//echo "======>> <pre>"; print_r($_POST['checklist_arr']); die();
	$qaChkStatus = '';
	foreach($_POST['checklist_arr'] as $key => $val) {
		//foreach($val as $row) {
			$dat = "''";
			$qaChkStatus = '';
			if(array_key_exists($key, $_POST['status_arr'])) {
				$qaChkStatus = $_POST['status_arr'][$key];
				if($qaChkStatus == 'Yes'){
					$dat = 'NOW()';
				}
			}

			$qaChkComment = '';
			if(array_key_exists($key, $_POST['checklist_comment_arr_new'])) {
				$qaChkComment = $_POST['checklist_comment_arr_new'][$key];
			}

			$completed_date = ($qaChkStatus == 'Yes') ? 'completion_date = NOW(),' : 'completion_date = "",';

			//SELECT 14898577719123
			$qCheckData = "SELECT id FROM qa_checklist_task_status WHERE task_status_id = '".$key."' AND is_deleted = 0";
			$qCheckDataRes = mysql_query($qCheckData);
			if(mysql_num_rows($qCheckDataRes) > 0){
				$checklistStatusQuery = 'UPDATE qa_checklist_task_status SET status = "'. $qaChkStatus .'",task_comment = "'.$qaChkComment.'", '. $completed_date .' last_modified_by = "'.$_SESSION['ww_builder_id'].'", last_modified_date = NOW() WHERE task_status_id = '. $key;
				//echo $checklistStatusQuery.'<br/>';
				mysql_query($checklistStatusQuery);
				$completed_date = '';
			}else{
				//$task_status_id = substr(time(), 0, 3) . substr(time(), 5) . rand(0, 999) . $_SESSION['ww_builder_id'];
				$task_status_id = '';
				if(array_key_exists($key, $_POST['task_status_id_arr'])) {
					$task_status_id = $_POST['task_status_id_arr'][$key];
				}

				$newChecklistTaskData = "task_status_id = '".$task_status_id."',
				qa_checklist_id = '".$checklist_id."',
				checklist_task_id = '".$key."',
				project_id = '".$_POST['project_id']."',
				status = '".$qaChkStatus."',
				task_comment = '".$qaChkComment."',
				task_name = '".$val[0]."',
				created_by = '".$_SESSION['ww_builder_id']."',
				created_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				completion_date = ".$dat.",
				last_modified_date = NOW(),
				original_modified_date = NOW()";
			
			//echo "INSERT INTO qa_checklist_task_status SET ".$newChecklistTaskData . '<===insert=='; die();
			mysql_query("INSERT INTO qa_checklist_task_status SET ".$newChecklistTaskData);
			}
		//}//End of inner foreach
	}//End of outer foreach

	$data = array('task_id' => $checklist_id, 'projectId' => $_POST['project_id']);
	echo json_encode($data);
}
?>
