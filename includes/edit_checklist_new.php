<style type="text/css">
@import "css/jquery.datepick.css";
table td{padding:5px;}
table{ margin-left:10px;}
#DRF, #DRT, #FBDF, #FBDT{
	background:#FFF;
	cursor:default;
	height:20px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	border-radius: 6px;
}
.upload-image { height: auto; }

</style>
<style>
	.innerTable { width: 100%;background-color: #fff;
	    	border: thin solid;
	    	color: #000;
	    	margin: 0px; }
	table.innerTable tr td,
	table.innerTable tr td:nth-child(3) { text-align: center; }
	table.innerTable tr td:first-child { text-align: left; }
	.btn-submit { background: url("images/update.png") repeat scroll 0 0 rgba(0, 0, 0, 0); border: 0 none; cursor: pointer; height: 43px; text-indent: -999px; width: 111px; }

	.evedence_button{
		background-image:url(images/task_detail_small.png);font-size:0px; border:none; height: 24px; width:74px;float:right;background-repeat: no-repeat;
	}
	.nonconf_button{
		background-image:url(images/negative_quality_item_1.png);font-size:0px; border:none; height: 24px; width:108px;float:right;background-repeat: no-repeat;
	}

	table.qachklist{width: 100%; }
	table.qachklist tr:nth-child(even) td {
		background-color: #e1e1e1 !important;
	}
	table.qachklist tr:nth-child(odd) td {
		background-color: #d2d2d2 !important;
	}
	table.qachklist tr:nth-last-child(1) td,
	table.qachklist tr:nth-last-child(2) td,
	table.qachklist tr:nth-last-child(3) td {
		background-color: #FFF !important;
	}

	table.qachklist tr.bg-none > td {
		background-color: #FFF !important;
		text-align: left;
	}

	table.qachklist tr:nth-last-child(2) td .upload-image,
	table.qachklist tr:nth-last-child(3) td .upload-image {
		height: 120px;
	}
</style>
<?php
$version = rand();
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
//session_start();
include_once("commanfunction.php");
$obj = new COMMAN_Class();
#print_r($_REQUEST);
	// $selectedProId 				= $_SESSION['qaChecklistProId'];
	// $qaChecklistId 				= $_SESSION['qaChecklistId'];
	// $qaChecklistLocationId 		= $_SESSION['qaChecklistLocationId'];
	// $qaChecklistSubLocationId 	= $_SESSION['qaChecklistSubLocationId'];
	// $qaChecklistSubLocationId1 	= $_SESSION['qaChecklistSubLocationId1'];
	// $qaChecklistSubLocationId2 	= $_SESSION['qaChecklistSubLocationId2'];


//if(isset($_REQUEST['uniqueId'])){
	#print_r($_REQUEST);
	$qaitpData = array();
	//$qaitpData = $obj->selQRYMultiple('id, checklist_name', 'check_list_items_project', 'is_deleted=0 and id="'. $_REQUEST['checklistId'] .'"');
	#print_r($qaitpData);
	$qaitpStatusDataArr = array();
	$q = "SELECT qc.project_checklist_id, qc.location_id, qc.sub_location_id, qc.sub_location1_id, qc.sub_location2_id, qc.location_tree, qc.status as checklist_status, qc.sc_sign, qc.contractor_sign, qcTask.task_name, qcTask.task_comment, qcTask.status, qcTask.completion_date, qcTask.task_status_id, qcTask.checklist_task_id,qc.sub_contractor_name FROM qa_checklist AS qc JOIN qa_checklist_task_status AS qcTask ON qcTask.qa_checklist_id = qc.qa_checklist_id WHERE qc.qa_checklist_id = '".$_REQUEST['selectedChecklistId']."' AND qc.is_deleted = 0 AND  qcTask.is_deleted = 0";
	$res = mysql_query($q);
	$flag = 0;
	$checklist_name = '';
	$checklist_id = $_REQUEST['selectedChecklistId'];
	$project_id = $_SESSION['qaChecklistProId'];
	// $location_id = $_REQUEST['location'];
	// $subLocation_id = $_REQUEST['subLocation'];
	// $sub_subLocation_id = $_REQUEST['sub_subLocation'];
	// $subSubLocation3_id = $_REQUEST['subSubLocation3'];
	$task_name_arr = array();
	$task_name_str = '';
	$taskIdForFlag = 0;
	$allTaskIds = '';
	$sc_sign = '';
	$contractor_sign = '';
	$subContractorName = '';

	while($qData = mysql_fetch_array($res)){
		$checklist_name = $qData['checklist_name'];
		$location_id = $qData['location_id'];
		$subLocation_id = $qData['sub_location_id'];
		$sub_subLocation_id = $qData['sub_location1_id'];
		$subSubLocation3_id = $qData['sub_location2_id'];
		$checklist_status = $qData['checklist_status'];
		$subContractorName = $qData['sub_contractor_name'];

		$sc_sign = $qData['sc_sign'];
		$contractor_sign = $qData['contractor_sign'];

		$project_checklist_id = $qData['project_checklist_id'];
		if(!empty($task_name_str)){
			$task_name_str .= ',"'.$qData['task_name'].'"';
			$allTaskIds .= ','.$qData['checklist_task_id'];
		}else{
			$task_name_str = '"'.$qData['task_name'].'"';
			$allTaskIds = $qData['checklist_task_id'];
		}

		$qaitpStatusDataArr[ $qData['checklist_name'] ][] = array(
			'task_status_id_new' => $qData['task_status_id'],
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
	//echo $sc_sign .'<<=sc_sign=='. $contractor_sign . '<<=contractor_sign=='; die();
	$sub_contractor_image = '';
	$contractor_image = '';
	if(!empty($sc_sign)){
		$sc_sign_img = "inspections/ncr_files/" . $sc_sign;
		if(file_exists($sc_sign_img)){
			$sub_contractor_image = $sc_sign_img;
		}
	}
	if(!empty($contractor_sign)){
		$c_sign_img = "inspections/ncr_files/" . $contractor_sign;
		if(file_exists($c_sign_img)){
			$contractor_image = $c_sign_img;
		}
	}
	//echo $sub_contractor_image .'<==AAAA=='. $contractor_image.'<==BBBB=='; die;

	//echo $task_name_str . "<<==="; die();
		//SELECT * FROM `qa_project_checklist_task` WHERE `project_checklist_id` = 20
	$q1 = "SELECT id, task_name, task_status, task_comment, comment_mandatory FROM qa_project_checklist_task WHERE project_checklist_id = '".$project_checklist_id."' AND is_deleted = 0 AND task_name NOT IN(".$task_name_str.")";
	$res1 = mysql_query($q1);
	while($qData1 = mysql_fetch_array($res1)){
		$task_status_id = substr(time(), 0, 3) . substr(time(), 5) . rand(0, 999) . $_SESSION['ww_builder_id'];
		 $qaitpStatusDataArr[ $checklist_name ][] = array(
		 	'task_status_id_new' => $task_status_id,
			'task_id' => $qData1['id'],
			'task_name' => $qData1['task_name'],
			'comment_mandatory' => $qData1['comment_mandatory'],
			'comment' => $qData1['task_comment'],
			'status' => '',//$qData1['task_status'],
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

	$q3 = "SELECT id, checklist_name FROM check_list_items_project WHERE id = '".$project_checklist_id."' AND is_deleted = 0";
	$res3 = mysql_query($q3);
	while($qData3 = mysql_fetch_array($res3)){
		$checklistName= $qData3['checklist_name'];
	}

	//print_r($qaitpStatusDataArr); echo "<<=allStatusIdData="; die();

	//echo $checklist_name.'<<=checklist=='.$location_id.'<<=l1=='.$subLocation_id .'<<=l2=='. $sub_subLocation_id.'<<=l3=='.$subSubLocation3_id. " <<=l4== <pre>"; print_r($qaitpStatusDataArr); die();

	//$project_id = $_REQUEST['projID'];
	$locationData = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'is_deleted = 0 AND location_parent_id = 0 AND project_id = '.$project_id);

	if(!empty($location_id)){
		$subLocationData = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'is_deleted = 0 AND location_parent_id = '.$location_id.' AND project_id = '.$project_id);
	}

	if(!empty($subLocation_id)){
		$sub_subLocationData = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'is_deleted = 0 AND location_parent_id = '.$subLocation_id.' AND project_id = '.$project_id);
	}

	if(!empty($sub_subLocation_id)){
		$subSubLocationData3 = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'is_deleted = 0 AND location_parent_id = '.$sub_subLocation_id.' AND project_id = '.$project_id);
	}
	
	#Status Array
	$statusArr = array('Open', 'Closed');
	if($flag == 1){?>
		<div id="middle" style="padding-bottom:80px;">
		    <div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
		    <div id="apply_now">
			<legend style="color: #fff;font-size: 25px;margin-bottom: 10px;">Edit CheckList</legend>
			<div class="search_multiple" style="border:1px solid; text-align:center;width:960px;margin-left: 20px;">
				<form action="" method="post" name="editCheckListForm" id="editCheckListForm">
					<input type="hidden" id="selected_project_id" name="project_id" value="<?=$project_id?>" />
					<input type="hidden" name="checklist_name" value="<?php echo $checklist_name; ?>" />
					<input type="hidden" name="checklist_id" value="<?php echo $checklist_id; ?>" />
					<table border="0" width="100%" class="simpleTable editTables" style="margin:0">
						<tr>
							<td>Location</td>
							<td>&nbsp;:&nbsp;</td>
							<td>
								<select style="width:220px;background-image:url(images/selectSpl.png);" class="select_box margin-class con_location" onchange="c_editShowLocation(this.value, <?=$project_id?>);" id="location" name="location">
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
									<select style="width:220px;background-image:url(images/selectSpl.png);" class="select_box margin-class con_showSubLocation" onchange="subLocatoin(this.value, <?=$project_id?>);" id="c_editShowSubLocation" name="editShowSubLocation">
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
									<select style="width:220px;background-image:url(images/selectSpl.png);" class="select_box margin-class con_showSubLocation1"  onchange="subLocatoin2(this.value, <?=$project_id?>);" id="c_editShowSubLocation1" name="editShowSubLocation1">
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
									<select style="width:220px;background-image:url(images/selectSpl.png);" class="select_box margin-class con_showSubLocation2" id="c_editShowSubLocation2" name="editShowSubLocation2">
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
							<tr style="background-color:#ddd;color:#000;">
								<td colspan="6"><div style="font-size: medium;text-align: center;"><?php echo $checklistName; ?></div></td>
							</tr>
							<tr>
								<td colspan="6">
									<table class="innerTable qachklist" width="100%">
										<?php $disable='';
										foreach($qasRow as $rows) { $task_id = $rows['task_id']; $checklist_task_id = $rows['checklist_task_id'];
										// $evdID = $obj->selQRY('task_id', 'qa_ncr_task_detail', 'is_deleted = 0 AND task_id = '.$task_id);
										// $nonEvdID = $obj->selQRY('task_id', 'qa_inspections', 'is_deleted = 0 AND task_id = '.$task_id);
										?>
											<tr class="this_class_<?=$rows['task_status_id_new'];?>">
												<td width="45%" <?php if($allStatusIdData[$checklist_task_id]['checkPoint'] == 'Yes'){echo 'style="color:red"';} ?> >
													<input type="hidden" name="checklist_arr[<?=$rows['task_id']?>][]" value="<?=$rows['task_name']?>" />
													<input type="hidden" name="checklist_comment_arr[<?=$task_id?>]" value="<?=$rows['comment']?>" />
													<input type="hidden" name="task_status_id_arr[<?=$task_id?>]" value="<?=$rows['task_status_id_new']?>" />
													
													<input type="hidden" class="taskId" name="task_id" value="<?=$rows['task_status_id_new'];?>">
													<input type="hidden" name="userRoll" id="userRoll" class="input_small" value="<?=$_SESSION['userRole'];?>" >
													<?=$rows['task_name']?>
												</td>

												<td width="5%">
													<input type="radio" class="evedence_pop <?php if($allStatusIdData[$checklist_task_id]['checkPoint'] == 'Yes'){echo 'hold_point_yes';}else{echo 'hold_point_no';} ?>" name="status_arr[<?=$rows['task_id']?>]" value="Yes" <?php if($rows['status'] == 'Yes'){echo $checked;} ?> <?php echo $disable; ?> /> Yes
													<?php if($rows['status'] == 'Yes'){ ?>
														<input  id="evedence_button_main_<?=$task_id;?>" class="evedence_pop evedence_button green_small" value="Task Detail" type="button" style="display:block;font-size:8px;">
													<?php }// endif?>
													<input  id="evedence_button_<?=$task_id;?>" class="evedence_pop evedence_button green_small" value="Task Detail" type="button" style="display:none;font-size:8px;">
												</td>
												<td width="5%">
													<input type="radio" class="nonconf_pop <?php if($allStatusIdData[$checklist_task_id]['checkPoint'] == 'Yes'){echo 'radio_button_no';} ?>" name="status_arr[<?=$rows['task_id']?>]" value="No"<?php if($rows['status'] == 'No'){echo 'checked="checked"';} ?> /> No
													<?php if($rows['status'] == 'No'){ ?>
														<input  id="nonconf_button_main_<?=$task_id;?>" class="nonconf_pop nonconf_button green_small" value="Non Conformance" type="button" style="display:block;font-size:8px;">
													<?php }// endif?>
													<input  id="nonconf_button_<?=$task_id;?>" class="nonconf_pop nonconf_button green_small" value="Non Conformance" type="button" style="display:none;font-size:8px;">
												</td>
												<td width="5%">
													<input type="radio" class="hide_no_and_yes <?php if($allStatusIdData[$checklist_task_id]['checkPoint'] == 'Yes'){echo 'radio_button_no';} ?>" name="status_arr[<?=$rows['task_id']?>]" value="NA" <?php if($rows['status'] == 'NA'){echo 'checked="checked"';} ?> /> NA
												</td>

												<td width="30%">
													<textarea type="text" rows="5" class="textAreaVal <?php if($allStatusIdData[$checklist_task_id]['commentMandatory'] == 'Yes'){ echo 'mandatory'; }?>" name="checklist_comment_arr_new[<?=$task_id?>]"><?=$rows['comment']?></textarea>
													<?php if($allStatusIdData[$checklist_task_id]['commentMandatory'] == 'Yes'){ ?>
														<lable for="checklist_comment_arr_new[<?=$task_id?>]" id="checklist_comment_arr_new[<?=$task_id?>]" generated="true" class="error" style="display:none;">
															<div class="error-edit-profile">The comment field is required</div>
														</lable>
													<?php } ?>
												</td>

												<td width="10%">
													<div class="upload-image">
														<div id="innerDiv_2" class="innerDiv" style="width:80px; height:80px;" onClick="view_attachment_files('this_class_<?=$rows['task_status_id_new'];?>',<?=$rows['task_status_id_new'];?>)">
															<label style="display:block">
															<span></br>View / Browse Images</span>
															</label>
														</div>
													</div>
												</td>
											</tr>
											<?php 
											if($allStatusIdData[$checklist_task_id]['checkPoint'] == 'Yes' && $rows['status'] !== 'Yes'){ $disable='disabled=true';}
											//if($taskIdForFlag == $task_id){ $disable='disabled=true';}
										} ?>

										<tr class="bg-none" >
									        <td colspan="1" align="left">Sub Contractor Name</td>
									        <td colspan="5" align="left">
									            <input name="subContractorName" id="subContractorName" class="" style="width: 420px;margin-left:0px;height: 24px;" type="text" value="<?php echo $subContractorName;?>">
											</td>
									    </tr>

										<!-- start signature -->
										<tr class="" >
									        <td colspan="1" align="left">Sub Contractor Sign</td>
									        <td colspan="2" align="left">
									        	<div class="upload-image">
													<?php $label='style="display:block"';
													$delimg='style="display:none"';
													if(file_exists($sub_contractor_image) && !empty($sub_contractor_image)){
														$label='style="display:none"';
														$delimg='style="display:block"';
													}
													?>
													<div id="innerDiv_0" class="innerDiv">
														<label <?=$label?>>
															<span>Browse Image</span>
															<input type="file" name="image0" id="image0" />
														</label>
														<div class="response" id="responseProjectManagerImage0">
															<?php if(file_exists($sub_contractor_image) && !empty($sub_contractor_image)){ ?>
																<img alt="image" src="<?php echo $sub_contractor_image.'?'.rand(0, 1000);?>">
																<input type="hidden" value="<?php echo $sc_sign;?>" name="image_name[0]" >
															<?php } ?>
														</div>
													</div>
													<img id="removeProjectManagerImage0" class="del-image" onClick="deleteImage('innerDiv_0', this.id)" src="images/remove.png" alt="delete image" <?=$delimg?> />
												</div>
									        </td>
									        <td colspan="1">
									        	<img class="signIcon" src="images/drawing_pen.png"  onclick="showSignatureBox('', 0)" style="width:65px; height:65px;" />
									        </td>
									     </tr>

									     <tr class="" >
									        <td colspan="1" align="left">Contractor Sign</td>
									        <td colspan="2" align="left">
									        	<div class="upload-image">
													<?php $label='style="display:block"';
													$delimg='style="display:none"';
													if(file_exists($contractor_image) && !empty($contractor_image)){
														$label='style="display:none"';
														$delimg='style="display:block"';
													}
													?>
													<div id="innerDiv_1" class="innerDiv">
														<label <?=$label?>>
															<span>Browse Image</span>
															<input type="file" name="image1" id="image1" />
														</label>
														<div class="response" id="responseProjectManagerImage1">
															<?php if(file_exists($contractor_image) && !empty($contractor_image)){ ?>
																<img alt="image" src="<?php echo $contractor_image.'?'.rand(0, 1000);?>">
																<input type="hidden" value="<?php echo $contractor_sign;?>" name="image_name[1]" >
															<?php } ?>
														</div>
													</div>
													<img id="removeProjectManagerImage1" class="del-image" onClick="deleteImage('innerDiv_1', this.id)" src="images/remove.png" alt="delete image" <?=$delimg?> />
												</div>
									        </td>
									        <td colspan="1">
									        	<img class="signIcon" src="images/drawing_pen.png"  onclick="showSignatureBox('', 1)" style="width:65px; height:65px;" />
									        </td>
									     </tr>

										<tr>
											<td colspan="6">
												<hr>
												<div style="text-align:center;">
													<!-- <button class="button"  class="btn-submit sub-btn"><a href="http://localhost/safeworkid/plantInduction/searchResult/1">Back</a></button> -->

													<!-- <a href="?sect=qc_task_search"><img src="images/back_btn.png" align="absmiddle" width="108" hspace="5" height="42"></a> -->
													<a href="?sect=qc_task_search" class="green_small" style="height: 42px;white-space: 5">Back</a>

													<input type="button" onclick="editCheckListSubmit();" id="submitEditTask" value="Update" class="green_small" />
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						<?php } ?>
						<!-- <tr>
							<td colspan="6">
								<div style="text-align:center;">
									<input type="button" onclick="editCheckListSubmit();" id="submitEditTask" value="submit" class="btn-submit sub-btn" />
								</div>
							</td>
						</tr> -->
					</table>
				</form>
			</div>
			</div>
		</div>
<?php
	} else {
		echo '<span style="color:#000;">Records Not Found!</span>';
	}
//}

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

	#Update qa_itp_checklist	
	$checklistQuery = "UPDATE qa_checklist SET
			location_id = '".$_POST['location']."',
			sub_location_id = '".$_POST['editShowSubLocation']."',
			sub_location1_id = '".$_POST['editShowSubLocation1']."',
			sub_location2_id = '".$_POST['editShowSubLocation2']."',
			location_tree = '". $locTree ."',
			location_depth = '".$locDepth."',
			status = '".$_POST['status']."',
			last_modified_by = '".$_SESSION['ww_builder_id']."',
			last_modified_date = NOW()
		WHERE
			is_deleted = 0 AND qa_checklist_id = '".$checklist_id."'";
			
	#echo $ChecklistQuery;die;
	mysql_query($checklistQuery);
	
	#Update qa_itp_checklist_status.
	$status_arr = $_POST['status_arr'];
	$completed_date = '';
	
	//echo "======>> <pre>"; print_r($_POST['checklist_arr']); die();
	$qaChkStatus = '';
	foreach($_POST['checklist_arr'] as $key => $val) {
		//foreach($val as $row) {
			$qaChkStatus = '';
			if(array_key_exists($key, $_POST['status_arr'])) {
				$qaChkStatus = $_POST['status_arr'][$key];
			}

			$qaChkComment = '';
			if(array_key_exists($key, $_POST['checklist_comment_arr_new'])) {
				$qaChkComment = $_POST['checklist_comment_arr_new'][$key];
			}

			$completed_date = ($qaChkStatus == 'Yes') ? 'completion_date = NOW(),' : '';

			//SELECT 14898577719123
			$qCheckData = "SELECT id FROM qa_checklist_task_status WHERE task_status_id = '".$key."' AND is_deleted = 0";
			$qCheckDataRes = mysql_query($qCheckData);
			if(mysql_num_rows($qCheckDataRes) > 0){
				$checklistStatusQuery = 'UPDATE qa_checklist_task_status SET status = "'. $qaChkStatus .'",task_comment = "'.$qaChkComment.'", '. $completed_date .' last_modified_by = "'.$_SESSION['ww_builder_id'].'", last_modified_date = NOW() WHERE task_status_id = '. $key;
				#echo $checklistStatusQuery.'<br/>';
				mysql_query($checklistStatusQuery);
				$completed_date = '';
			}else{
				$task_status_id = substr(time(), 0, 3) . substr(time(), 5) . rand(0, 999) . $_SESSION['ww_builder_id'];
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
				completion_date = NOW(),
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

<script type="text/javascript" src="draw_on_canvas/JsCode.js"></script>
<script type="text/javascript" src="js/ajaxupload.3.5.js"></script>
<script type="text/javascript" src="js/multiupload_attachment.js"></script>
<script type="text/javascript">
	var align = 'center';
	var top1 = 100;
	var width = 500;
	var padding = 10;
	var backgroundColor = '#FFFFFF';
	var borderColor = '#333333';
	var borderWeight = 4;
	var borderRadius = 5;
	var fadeOutTime = 300;
	var disableColor = '#666666';
	var disableOpacity = 40;
	var loadingImage = 'images/loadingAnimation.gif';
	var spinnerVisible = false;
	$( document ).ready(function() {
    	/*$( ".hold_point_yes" ).click(function(){
	    	var flagForDisable = 0;
	    	$('.evedence_pop').each(function(index) {
	    		if(flagForDisable<2 && !$(this).hasClass('call_for_hold_point')){
					if($(this).hasClass('hold_point_yes')){
					  	if(flagForDisable == 1){
					  		$(this).removeAttr('disabled');
					  	}else{
					  		$(this).addClass('call_for_hold_point');
					  	}
					  	flagForDisable += 1;
					}else{
					  	$(this).removeAttr('disabled');
					  	$(this).addClass('call_for_hold_point');
					}
				}
			});
	    });*/

	    $( ".hold_point_yes" ).click(function(){
	    	var taskIdVal = $(this).closest('tr').find('.taskId').val();
	    	var flag = 1;
	    	var flag1 = 0;
	    	$('.evedence_pop').each(function(index) {
	    		if(flag == 1){
	    			$(this).removeAttr('disabled');
	    		}
	    		var selTaskIdVal = $(this).closest('tr').find('.taskId').val();
	    		if($(this).hasClass('hold_point_yes')){
	    			if(flag1 == 1){
	    				flag = 0;
	    			}
	    			if(taskIdVal == selTaskIdVal){
	    				flag1 = 1;
	    			}
	    		}
	    		
			});
	    });
		
		$(".radio_button_no").click(function(){
	    	$disableFlag = 0;
	    	var statusName = $(this).attr('name');
	    	var flag=0;
	     	$(this).closest('tr').find('input').removeClass('call_for_hold_point');
	    	$('.evedence_pop').each(function(index) {
	    		if(flag == 1){
	    			$(this).prop('checked',false);
	    			$(this).prop('disabled',true);
					$(this).removeClass('call_for_hold_point');
					if($(this).hasClass('evedence_button')){
						$(this).css('display','none');
					}
					//$(this).removeClass('hold_point_yes');
	    		}
	    		if(statusName == $(this).attr('name'))
	    		{
	    			flag =1;
	    			$(this).removeClass('call_for_hold_point');
	    		}
			});
	    });
	});

	function editCheckListSubmit() {
		var returnFlaf = 0;
		$('.textAreaVal').each(function(index) {
		  	if($(this).hasClass('mandatory')){
		  		if($(this).val().trim() == ''){$(this).siblings('lable').show('slow'); returnFlaf=1; return false;}else{$(this).siblings('lable').hide('slow');}
		  	}
		});
		if(returnFlaf == 1){
			return false;
		}
		
		showProgress();
		$.post('edit_checklist.php?antiqueID='+Math.random(), $('#editCheckListForm').serialize()).done(function(data) {
			hideProgress();		
			//closePopup(300);
			jAlert('Checklist Data Edit Successfully');
			window.location.href="?sect=qc_task_search";
		});
	}

	function c_editShowLocation(locationId){
		$.post('ajaxFunctions.php?type=getAllSublocation&locationId='+locationId).done(function(data) {
			var jsonResult = JSON.parse(data);
			$selectOpt = '<option value="">Select</option>';
			$("#c_editShowSubLocation1").html($selectOpt);
			$("#c_editShowSubLocation2").html($selectOpt);
			$("#c_editShowSubLocation").html(jsonResult.data);
		});
	}

	function subLocatoin(locationId){
		$.post('ajaxFunctions.php?type=getAllSublocation&locationId='+locationId).done(function(data) {
			var jsonResult = JSON.parse(data);
			$selectOpt = '<option value="">Select</option>';
			$("#c_editShowSubLocation2").html($selectOpt);
			$("#c_editShowSubLocation1").html(jsonResult.data);
		});
	}

	function subLocatoin2(locationId,proj){
		$.post('ajaxFunctions.php?type=getAllSublocation&locationId='+locationId).done(function(data) {
			var jsonResult = JSON.parse(data);
			$("#c_editShowSubLocation2").html(jsonResult.data);
		});
	}

	var signatureImageAjaxUrl = '';
	function showSignatureBox(idText, idNo){
		postData = ''; //{postid:1};
		//TINY.box.show({url:'< ?php echo base_url();?>taskObservation/taskObservationSignature/',post:postData,width:550,height:310,opacity:20,topsplit:3,animate:true,openjs:function(){InitThis(idText, idNo);}})
		//TINY.box.show({url:'checklist_signature.php',post:postData,width:550,height:310,opacity:20,topsplit:3,animate:true,openjs:function(){InitThis(idText, idNo);}})

		var callUrl = 'checklist_signature.php?sign='+idNo;
		modalPopup(align, 100, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, callUrl, loadingImage, InitThis);
	}

	function removeImages(divId, imgId, removeButtonId){
		//console.log(divId,'<<====', imgId,'<<====', removeButtonId,'<<====');
		$("#"+divId).html('');
		$("#"+removeButtonId).css("display","none");
	}

	//Start:- Upload project image
	//image0
	$(function(){
		var btnUpload=$('#image0');
		var status=$('#responseProjectManagerImage0');
		new AjaxUpload(btnUpload, {
			action: 'auto_file_upload.php?action=image0&proid=<?php echo $selectedProId; ?>&uniqueID='+Math.random(),
			name: 'image0',
			onSubmit: function(file, ext){
				if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
					// extension is not allowed
					$('.innerDiv > label').hide();
					$('img.del-image').show('fast');
					status.html('<p>Only JPG, PNG or GIF files are allowed</p>');
					return false;
				}
				status.text('Uploading...');
				showProgress();
			},
			onComplete: function(file, response){
				hideProgress();
				$('#innerDiv_0 > label').hide();
				$('#removeProjectManagerImage0').show('fast');
				status.html(response);
			}
		});
	});
	//image1
	$(function(){
		var btnUpload=$('#image1');
		var status=$('#responseProjectManagerImage1');
		new AjaxUpload(btnUpload, {
			action: 'auto_file_upload.php?action=image1&proid=<?php echo $selectedProId; ?>&uniqueID='+Math.random(),
			name: 'image1',
			onSubmit: function(file, ext){
				if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
					// extension is not allowed
					$('.innerDiv > label').hide();
					$('img.del-image').show('fast');
					status.html('<p>Only JPG, PNG or GIF files are allowed</p>');
					return false;
				}
				status.text('Uploading...');
				showProgress();
			},
			onComplete: function(file, response){
				hideProgress();
				$('#innerDiv_1 > label').hide();
				$('#removeProjectManagerImage1').show('fast');
				status.html(response);
			}
		});
	});
	function deleteImage(innerDiv, delimg) {
		console.log(delimg);
		$('#'+innerDiv+' label').show('fast');
		$('#'+innerDiv+' .response').html('');
		$('#'+delimg).hide('fast');
	}
	//End:- Upload project image

	function view_attachment_files(thisVal,task_id){
		var checked_val = $("."+thisVal).find("input:checked").val();
		if(checked_val == undefined){
			checked_val = '';
		}
		width = 1000;

		var callUrl = 'task_images_attachments.php?task_id='+task_id+'&checked_val='+checked_val;
		modalPopup(align, 100, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, callUrl, loadingImage,uploadMultiAttachment);
	}

	function deleteAttachmentImage(thisId, tableName, imgId){
		$('#'+thisId).closest('li').remove();
		$.post('task_images_attachments.php?type=delete&tableName='+tableName+'&imgId='+imgId).done(function(data) {
			var jsonResult = JSON.parse(data);
		});
	}

	function updateAttachmentSubmit(){
		$.post('task_images_attachments.php?type=updateAttachment').done(function(data) {
			//var jsonResult = JSON.parse(data);
			closePopup(300);
		});
	}

	function uploadMultiAttachment(){
		//Multi upload image
		$("span#btnMultiUpload").click(function(){
			$("input[type='file']#multiUpload").trigger('click');
		});

		var config = {
			support : "image/jpg,image/png,image/bmp,image/jpeg,image/gif",		// Valid file formats
			form: "demoFiler1",					// Form ID
			dragArea: "dragAndDropFiles1",		// Upload Area ID
			uploadUrl: "includes/upload_attachment.php"	// Server side upload url
		}

		$(document).ready(function(){
			initMultiUploaderNew(config);
		});
	}

</script>

<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript" src="js/checklist_popup.js?version=<?=$version?>"></script>
