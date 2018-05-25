<?php 
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_REQUEST['InsertEvedenceId'])){
	 //print_r($_FILES); die;
	 //print_r($_REQUEST); 
	 //die;
	
	$locationId = '';	
	if(!empty($_POST['location'])) {		
		if(!empty($_POST['addShowSubLocation'])) {			
			if(!empty($_POST['addShowSubLocation1'])) {				
				if(!empty($_POST['addShowSubLocation2'])) {					
					$$locationId = $_POST['addShowSubLocation2'];				
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
	
	if(!empty($locationId)) {		
		$locationData = $obj->selQRYMultiple('location_name_tree', 'project_locations', 'is_deleted = 0 AND location_id = '. $locationId);		
		$locTree = $locationData[0]['location_name_tree'];	
	}

	$time = time();

	if(isset($_REQUEST['non_conformance_id']) && !empty($_REQUEST['non_conformance_id']) && $_REQUEST['non_conformance_id'] > 0){
		//$insrted_id = substr(time(), 0, 3) . substr(time(), 5) . rand(0, 999) . $_SESSION['ww_builder_id'];
		$updateQry = "UPDATE qa_inspections SET
						qa_inspection_description = '".$_REQUEST['description']."',
						qa_inspection_raised_by = '".$_REQUEST['raisedBy']."',
						qa_inspection_inspected_by = '".$_SESSION['user_name']."',
						qa_inspection_location = '".$locTree."',
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						last_modified_date = NOW(),
						original_modified_date = NOW()
					WHERE
						non_conformance_id = ".$_REQUEST['non_conformance_id']; 

		mysql_query($updateQry) or die(mysql_error());		


		$issueToArray = $_REQUEST['issuedto'];
		$issueToA = array_filter($issueToArray);
		// echo "<pre>";
		// print_r($_REQUEST);
		// die;
		if(isset($issueToA) && !empty($issueToA))
		{				
		$del_query = "delete from qa_issued_to_inspections where non_conformance_id=".$_REQUEST['non_conformance_id'];
		mysql_query($del_query) or mysql_error();
		for($i = 0; $i < sizeof($issueToArray); $i++) {
			if(isset($_REQUEST['issuedto'][$i]) && !empty($_REQUEST['issuedto'][$i]))
			{
				$newDate = '';
				if(!empty($_REQUEST['fixbydate'][$i])){
					$originalDate = $_REQUEST['fixbydate'][$i];
					$newDate = date("Y-m-d", strtotime($originalDate));
				}
				$attchdata = "non_conformance_id = '".$_REQUEST['non_conformance_id']."',
				task_id = '".$_REQUEST['taskId']."',
				qa_issued_to_name = '".$_REQUEST['issuedto'][$i]."',
				qa_inspection_fixed_by_date = '".$newDate."',
				qa_inspection_status = '".$_REQUEST['status'][$i]."',
				project_id = '".$_REQUEST['projID']."',
				created_by = '".$_SESSION['ww_builder_id']."',
				created_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				last_modified_date = NOW(),
				original_modified_date = NOW()";
				
				$Issuesql = "INSERT INTO qa_issued_to_inspections SET ".$attchdata; 
				mysql_query($Issuesql) or mysql_error();
			}	
		}
	}
		if(isset($_FILES) && !empty($_FILES)){

			$uid = time().$_SESSION['ww_builder_id'];
			$attchment_name1 = basename($_FILES["images"]["name"]);
			$attchment_name2 = basename($_FILES["drawing"]["name"]);
			$image1 = $uid.'_images_'.$attchment_name1;
			$image2 = $uid.'_drawing_'.$attchment_name2;
			

			$target_dir1 = './inspections/photo/';
			$target_dir2 = './inspections/drawing/';
			
			$img_id_arr = array();
			$img_id_arr[] = $_REQUEST['imageId1'];
			$img_id_arr[] = $_REQUEST['imageId2'];
			$id_img = 0;
			
			foreach ($_FILES as $key => $files) {
				if($files["name"]){
					
					/*********************qa graphics********************/
					$attchdata = "non_conformance_id = '".$_REQUEST['non_conformance_id']."',
					task_id = '".$_REQUEST['taskId']."',
					qa_graphic_type = '".$key."',
					qa_graphic_name = '".$uid."_".$key."_".$files['name']."',					
					project_id = '".$_REQUEST['projID']."',
					created_by = '".$_SESSION['ww_builder_id']."',
					resource_type = 'Webserver',
					created_date = NOW(),
					last_modified_by = '".$_SESSION['ww_builder_id']."',
					last_modified_date = NOW(),
					original_modified_date = NOW()";

					//$sql = "select * from qa_graphics WHERE non_conformance_id = ".$_REQUEST['non_conformance_id']." AND task_id = ".$_REQUEST['taskId']." and qa_graphic_type = '".$key."'";
					$sql = "SELECT * from qa_graphics WHERE qa_graphic_id = ".$img_id_arr[$id_img];
					$res = mysql_query($sql);
					$result = mysql_fetch_array($res);					
					if(isset($result) and !empty($result))
					{
						//$attsql = "UPDATE qa_graphics SET ".$attchdata. "WHERE non_conformance_id = ".$_REQUEST['non_conformance_id']." AND task_id = ".$_REQUEST['taskId']." and qa_graphic_type = '".$key."'";
						$attsql = "UPDATE qa_graphics SET ".$attchdata. "WHERE qa_graphic_id = ".$img_id_arr[$id_img];
						mysql_query($attsql);
					}else{
						$attsql = "INSERT INTO qa_graphics SET ".$attchdata; 
						mysql_query($attsql);
					}					
					/*********************************************/
					if($key == 'images'){
						move_uploaded_file($_FILES[$key]['tmp_name'], $target_dir1.$image1);	
					}else{
						move_uploaded_file($_FILES[$key]['tmp_name'], $target_dir2.$image2);
					}
				}
				$id_img++;
			}			
		}

		$output = array('status' => true, 'msg' => 'Non Conformance updated Sucessfully', 'non_conformance_id' => $_REQUEST['non_conformance_id'], 'taskId' => $_REQUEST['taskId'], 'updateid' => $_REQUEST['task_detail_id'] );

	}else{
		//print_r($_REQUEST);
		$insrted_id = substr(time(), 0, 3) . substr(time(), 5) . rand(0, 999) . $_SESSION['ww_builder_id'];
		$inssertQRY = "INSERT INTO qa_inspections SET
						non_conformance_id = '".$insrted_id."',
						task_id = '".$_REQUEST['taskId']."',
						location_id = '".$locationId."',
						qa_inspection_date_raised = NOW(),
						qa_inspection_raised_by = '".$_REQUEST['raisedBy']."',
						qa_inspection_inspected_by = '".$_SESSION['ww_builder_id']."',
						qa_inspection_description = '".$_REQUEST['description']."',
						qa_inspection_location = '".$locTree."',
						project_id = '".$_REQUEST['projID']."',
						created_by = '".$_SESSION['ww_builder_id']."',
						created_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						last_modified_date = NOW(),
						original_modified_date = NOW()";

		mysql_query($inssertQRY) or mysql_error();	
		
		$issueToArray = $_REQUEST['issuedto'];
		$issueToA = array_filter($issueToArray);

		for($i = 0; $i < sizeof($issueToA); $i++) {
			if(isset($_REQUEST['issuedto'][$i]) && !empty($_REQUEST['issuedto'][$i])){
				$newDate = '';
				if(!empty($_REQUEST['fixbydate'][$i])){
					$originalDate = $_REQUEST['fixbydate'][$i];
					$newDate = date("Y-m-d", strtotime($originalDate));
				}

				$attchdata = "non_conformance_id = '".$insrted_id."',
				task_id = '".$_REQUEST['taskId']."',
				qa_issued_to_name = '".$_REQUEST['issuedto'][$i]."',
				qa_inspection_fixed_by_date = '".$newDate."',
				qa_inspection_status = '".$_REQUEST['status'][$i]."',
				project_id = '".$_REQUEST['projID']."',
				created_by = '".$_SESSION['ww_builder_id']."',
				created_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				last_modified_date = NOW(),
				original_modified_date = NOW()";
				
				$Issuesql = "INSERT INTO qa_issued_to_inspections SET ".$attchdata; 
				mysql_query($Issuesql) or mysql_error();	
			}
		}
		
		if(isset($_FILES) && !empty($_FILES)){

			$uid = time().$_SESSION['ww_builder_id'];
			$attchment_name1 = basename($_FILES["images"]["name"]);
			$attchment_name2 = basename($_FILES["drawing"]["name"]);
			$image1 = $uid.'_images_'.$attchment_name1;
			$image2 = $uid.'_drawing_'.$attchment_name2;
			

			$target_dir1 = './inspections/photo/';
			$target_dir2 = './inspections/drawing/';

			
			foreach ($_FILES as $key => $files) {
				
				if($files["name"]){
					

					/*********************qa graphics********************/
					$attchdata = "non_conformance_id = '".$insrted_id."',
					task_id = '".$_REQUEST['taskId']."',
					qa_graphic_type = '".$key."',
					qa_graphic_name = '".$uid."_".$key."_".$files['name']."',					
					project_id = '".$_REQUEST['projID']."',
					created_by = '".$_SESSION['ww_builder_id']."',
					resource_type = 'Webserver',
					created_date = NOW(),
					last_modified_by = '".$_SESSION['ww_builder_id']."',
					last_modified_date = NOW(),
					original_modified_date = NOW()";

					$attsql = "INSERT INTO qa_graphics SET ".$attchdata; 
			
					mysql_query($attsql);

					/*********************************************/

					if($key == 'images'){
						move_uploaded_file($_FILES[$key]['tmp_name'], $target_dir1.$image1);	
					}else{
						move_uploaded_file($_FILES[$key]['tmp_name'], $target_dir2.$image2);
					}
				}
				
			}

						    
		}

		$output = array('status' => true, 'msg' => 'Non Conformance Added Sucessfully', 'non_conformance_id' => $insrted_id, 'taskId' => $_REQUEST['taskId'] );
		
	}
	
	echo json_encode($output);
			
}

if(isset($_REQUEST['deleteEvedenceId'])){
	$isseu_id = $_REQUEST['isseu_id'];
	$delete = "delete from qa_issued_to_inspections where qa_issued_to_id=".$isseu_id;
	mysql_query($delete) or mysql_error();
	$arr['msg'] = "This Issue to deleted";
	$arr['status'] = 1;
	echo json_encode($arr);
}

if(isset($_REQUEST['uniqueId'])){ 
	
	$evdenceData = $obj->selQRY('*', 'qa_inspections', 'is_deleted = 0 AND task_id = '.$_REQUEST['taskId']);

	$atdata = $obj->selQRYMultiple('qa_graphic_name,qa_graphic_id', 'qa_graphics', 'is_deleted = 0 AND non_conformance_id = '.$evdenceData['non_conformance_id']);
	//$issueToData = $obj->selQRYMultiple('issued_to_inspections_id, issued_to_name', 'issued_to_for_inspections', 'project_id = '.$_REQUEST['projID'].' and is_deleted=0  group by issued_to_name order by issued_to_name');

	$issueToData = $obj->selQRYMultiple('issue_to_id, issue_to_name, company_name', 'inspection_issue_to', 'project_id = '.$_REQUEST['projID'].' and is_deleted=0  group by issue_to_name order by issue_to_name');

	//print_r($issueToData);
	$imageId1 = '';
	$imageId2 = '';
	$non_conformance_id = 0;
	$image1 = '';
	$image2 = '';
	$raisedBy = '';
	if($evdenceData){
		$raisedBy = $evdenceData['qa_inspection_raised_by'];
		$non_conformance_id = $evdenceData['non_conformance_id'];
		if(!empty($atdata[0]['qa_graphic_name'])){
			$image1 = 'inspections/photo/'.$atdata[0]['qa_graphic_name'];
			if(!file_exists($image1)){
				$image1 = '';
			}
		}

		if(!empty($atdata[1]['qa_graphic_name'])){
			$image2 = 'inspections/drawing/'.$atdata[1]['qa_graphic_name'];
			if(!file_exists($image2)){
				$image2 = '';
			}
		}

		$imageId1 = $atdata[0]['qa_graphic_id'];
		$imageId2 = $atdata[1]['qa_graphic_id'];
	}

	$qa_issued = $obj->selQRYMultiple('distinct(qa_issued_to_name) as qa_issued_to_name,qa_inspection_fixed_by_date,qa_inspection_status,qa_issued_to_id', 'qa_issued_to_inspections', 'project_id = '.$_REQUEST['projID'].' and  non_conformance_id = '.$non_conformance_id.' and is_deleted=0 ORDER BY qa_issued_to_id ASC');	
	#print_r($qa_issued); die;
	#echo $non_conformance_id;
if(isset($_REQUEST['view'])){
	$nonRead = 'disabled';
	$display = 'display:none;';
}else{
	$nonRead = '';
	$display = 'block';
}

?>

<style type="text/css">
.nonselect{
	width:100%;
	background-color:#FFFFFF;
	background-repeat: no-repeat;
	color: #333;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 14px;
	height: 25px;
	border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;
	border-color: #bbb;
}

input[type='file']#image1,
input[type='file']#image2 {
	display: block;
	width: 255px;
}
</style>
<fieldset class="Noncwindow">
	<legend style="color:#000000;"> Non Conformance </legend>
	<form name="addEvedenceForm" id="addEvedenceForm">
		<table width="100%" border="0" align="left" cellpadding="0" cellspacing="15" style="color:#000;">
			
			<tr>
				<td valign="top" align="left">Raised By<span class="req">*</span></td>
				<td align="left">
					<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;" <?php echo $nonRead; ?>>
						<!--option value="">All Defect</option-->
						<option value="Builder" <?php if($raisedBy  == 'Builder'){ echo 'selected="selected"';}?> >Builder</option>
						<option value="Architect" <?php if($raisedBy  == 'Architect'){ echo 'selected="selected"';}?>>Architect</option>
						<option value="Structural Engineer" <?php if($raisedBy  == 'Structural Engineer'){ echo 'selected="selected"';}?>>Structural Engineer</option>
						<option value="Services Engineer" <?php if($raisedBy  == 'Services Engineer'){ echo 'selected="selected"';}?>>Services Engineer</option>
						<option value="Accreditation" <?php if($raisedBy  == 'Accreditation'){ echo 'selected="selected"';}?>>Accreditation</option>
						<option value="Consultant" <?php if($raisedBy  == 'Consultant'){ echo 'selected="selected"';}?>>Consultant</option>
						<option value="Independent Reviewer" <?php if($raisedBy  == 'Independent Reviewer'){ echo 'selected="selected"';}?>>Independent Reviewer</option>
						<option value="Stakeholders" <?php if($raisedBy == 'Stakeholders'){ echo 'selected="selected"';}?>>Stakeholders</option>
					</select>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">Description <span class="req" id="commentStar"></span></td>
				<td align="left">
					<textarea name="description" id="description" class="text_area" <?php echo $nonRead; ?>><?php if($evdenceData) {echo $evdenceData['qa_inspection_description']; }?></textarea>
					<lable for="comment" id="errorComment" generated="true" class="error" style="display:none;">
						<div class="error-edit-profile">Instructions field is required</div>
					</lable>
				</td>
			</tr>

			<tr height="150">
				<td align="left">&nbsp; Attachment  </td>
				<td align="left">
					<label class="filebutton" align="center" style="<?php echo $display; ?>">&nbsp;Browse Image 1
						<input id="image1" name="images" type="file" onchange="showImage1(this)">
					</label>
					<input type="hidden" name="imageId1" value="<?=$imageId1?>" />
					<div class="upload-image">
						<div id="innerDiv0" class="innerDiv">
							<?php if(!empty($image1)){ ?>
								<img id="response_1" src="<?=$image1;?>" width="120" height="120">
							<?php } else{ ?>
								<span>Browse Image</span>
								<img id="response_1" class="imageClass1" width="120" height="120" style="display:none;">
							<?php } ?>
						</div>
					</div>

				</td>
				<td align="left">
					<?php if($nonRead != 'view'){ ?>
					<label class="filebutton" align="center" style="<?php echo $display; ?>">&nbsp;Browse Image 2
						<input id="image2" name="drawing" type="file" onchange="showImage2(this)">
					</label>
					<?php } ?>
					<input type="hidden" name="imageId2" value="<?=$imageId2?>" />
					<div class="upload-image">
						<div id="innerDiv1" class="innerDiv">
							<?php if(!empty($image2)){ ?>
								<img id="response_2" src="<?=$image2;?>" width="120" height="120">
							<?php } else{ ?>
								<span>Browse Image</span>
								<img id="response_2"  class="imageClass2" width="120" height="120" style="display:none;">
							<?php } ?>
						</div>
					</div>
				</td>
			</tr>

			<tr>
			     <td align="left" width="100">Issued To Detail </td>
			</tr>
			<?php
			if(isset($qa_issued) && !empty($qa_issued))
			{
				$i=1;
				foreach ($qa_issued as $key => $value) {					
				?>
				<tr id="hide_<?php echo $value['qa_issued_to_id'];?>" class="issueToPluse">
					<td width="10%" style="<?php echo $display; ?>">
					<?php 
						if($i == 1)
						{
							?>
							<img style="cursor:pointer;" onclick="AddItem();" src="images/inspectin_add.png" align="absmiddle">
							<?php	
						}
						else{
							?>
							<img style="cursor:pointer;" onclick="removeIssue('<?php echo $value['qa_issued_to_id'];?>',<?php echo $i;?>);" src="images/inspectin_delete.png" align="absmiddle">
							<?php
						}
					?>
						
					</td>
					<td width="30%">
						<select name="issuedto[]" type="text" id="Issuedto<?php echo $i;?>" class="nonselect" <?php echo $nonRead; ?>>	
							<option selected="selected" value="">Select Issue To</option>
							<?php foreach($issueToData as $isData) { if(empty($isData['company_name'])){$isData['company_name'] = 'NA';} ?>
								<option  <?php if($value['qa_issued_to_name']==$isData['issue_to_name'].' ('.$isData['company_name'] . ')'){?>selected="selected"<?php }
								?>value="<?=$isData['issue_to_name'].' ('.$isData['company_name'] . ')'?>"><?=$isData['issue_to_name'].' ('.$isData['company_name'] . ')'?></option>
							<?php } ?>	
						</select>
						<div class="error-edit-nonconf" id="errorIssueTo<?php echo $i;?>" style="display:none;">Select Issue To</div>
					</td>
					<td width="30%">
						<input name="fixbydate[]" value="<?php $newDate = ""; if(!empty($value['qa_inspection_fixed_by_date']) && $value['qa_inspection_fixed_by_date']>0){ $originalDate = $value['qa_inspection_fixed_by_date'];
					$newDate = date("d-m-Y", strtotime($originalDate)); echo $newDate; }else{ echo "";} ?>" type="text" id="fixedByDate_<?php echo $i-1;?>" class="nonselect" <?php echo $nonRead; ?>>	
						<div class="error-edit-nonconf" id="errorDate<?php echo $i;?>" style="display:none;">Select Date</div>
					</td>
					<td width="30%">
						<select name="status[]" type="text" id="Status<?php echo $i;?>" class="nonselect" <?php echo $nonRead; ?>>	
							<option value="">Select</option>
							<option  <?php if($value['qa_inspection_status']== "Open"){?>selected="selected"<?php }
								?> value="Open">Open</option>
							<option  <?php if($value['qa_inspection_status']== "Close"){?>selected="selected"<?php }
								?> value="Close">Close</option>
							<option  <?php if($value['qa_inspection_status']== "Fixed"){?>selected="selected"<?php }
								?> value="Fixed">Fixed</option>
						</select>
						<div class="error-edit-nonconf" id="errorStatus<?php echo $i;?>" style="display:none;">Select Status</div>
					</td>			
				</tr>
				<?php
				$i++;
				}
				?>
				<input type="hidden" name="hid" id="hid" value="<?php echo $i-1;?>">
				<?php
			}
			else{
				?>
				<tr id="hide_0" class="issueToPluse">
					<td width="10%">
						<img style="cursor:pointer;" onclick="AddItem();" src="images/inspectin_add.png" align="absmiddle">
					</td>
					<td width="30%">
						<select name="issuedto[]" type="text" id="Issuedto1" class="nonselect">	
							<option selected="selected" value="">Select Issue To</option>
							<?php foreach($issueToData as $isData) { if(empty($isData['company_name'])){$isData['company_name'] = 'NA';} ?>
								<option  value="<?=$isData['issue_to_name'].' ('.$isData['company_name'] . ')'?>"><?=$isData['issue_to_name'].' ('.$isData['company_name'] . ')'?></option>
							<?php } ?>	
						</select>
						<div class="error-edit-nonconf" id="errorIssueTo1" style="display:none;">Select Issue To</div>
					</td>
					<td width="30%">
						<input name="fixbydate[]" type="text" id="fixedByDate_0" class="nonselect">
						<div class="error-edit-nonconf" id="errorDate1" style="display:none;">Select Date</div>	
					</td>
					<td width="30%">
						<select name="status[]" type="text" id="Status1" class="nonselect">	
							<option value="">Select</option>
							<option value="Open">Open</option>
							<option value="Close">Close</option>
							<option value="Fixed">Fixed</option>
						</select>
						<div class="error-edit-nonconf" id="errorStatus1" style="display:none;">Select Status</div>
					</td>			
				</tr>
				

<?php
			}

			?>
			

			<tr id="hide_1" class="issueToPluse" style="display:none;">
				<td width="10%">
						<img style="cursor:pointer;" onclick="removeElement('hide_1');" src="images/inspectin_delete.png" align="absmiddle">
				</td>
				<td width="30%">
					<select name="issuedto[]" type="text" id="Issuedto2" class="nonselect">	
						<option selected="selected" value="">Select Issue To</option>
						<?php foreach($issueToData as $isData) { if(empty($isData['company_name'])){$isData['company_name'] = 'NA';} ?>
							<option  value="<?=$isData['issue_to_name'].' ('.$isData['company_name'] . ')'?>"><?=$isData['issue_to_name'].' ('.$isData['company_name'] . ')'?></option>
						<?php } ?>	
					</select>
					<div class="error-edit-nonconf" id="errorIssueTo2" style="display:none;">Select Issue To</div>
				</td>
				<td width="20%">
					<input name="fixbydate[]" type="text" id="fixedByDate_1" class="nonselect">	
					<div class="error-edit-nonconf" id="errorDate2" style="display:none;">Select Date</div>
				</td>
				<td width="30%">
					<select name="status[]" type="text" id="Status2" class="nonselect">	
						<option value="">Select</option>
						<option value="Open">Open</option>
						<option value="Close">Close</option>
						<option value="Fixed">Fixed</option>
					</select>
					<div class="error-edit-nonconf" id="errorStatus2" style="display:none;">Select Status</div>
				</td>
			
			</tr>

			<tr id="hide_2" class="issueToPluse" style="display:none;">
				<td width="10%">
					<img style="cursor:pointer;" onclick="removeElement('hide_2');" src="images/inspectin_delete.png" align="absmiddle">
				</td>
				<td width="30%">
					<select name="issuedto[]" type="text" id="Issuedto3" class="nonselect">	
						<option selected="selected" value="">Select Issue To</option>
						<?php foreach($issueToData as $isData) { if(empty($isData['company_name'])){$isData['company_name'] = 'NA';} ?>
							<option  value="<?=$isData['issue_to_name'].' ('.$isData['company_name'] . ')'?>"><?=$isData['issue_to_name'].' ('.$isData['company_name'] . ')'?></option>
						<?php } ?>	
					</select>
					<div class="error-edit-nonconf" id="errorIssueTo3" style="display:none;">Select Issue To</div>
				</td>
				<td width="30%">
					<input name="fixbydate[]" type="text" id="fixedByDate_2" class="nonselect">	
					<div class="error-edit-nonconf" id="errorDate3" style="display:none;">Select Date</div>
				</td>
				<td width="30%">
					<select name="status[]" type="text" id="Status3" class="nonselect">	
						<option value="">Select</option>
						<option value="Open">Open</option>
						<option value="Close">Close</option>
						<option value="Fixed">Fixed</option>
					</select>
					<div class="error-edit-nonconf" id="errorStatus3" style="display:none;">Select Status</div>
				</td>
			
			</tr>


			<tr>
				<td></td>
				<td style="<?php echo $display; ?>">
				
	            	<input type="button" name="button" class="green_small" id="button" style="float:left;" onclick="addTaskNoconfirmance();" value="Submit"/>
	           
	            </td>
	            <td>
	            	<a id="ancor" onclick="closePopup(300);" class="green_small">Back </a>
				</td>
			</tr>
				<input type="hidden" id="non_conformance_id" name="non_conformance_id" value="<?=$non_conformance_id;?>">
				<input type="hidden" id="taskId" name="taskId" value="<?=$_REQUEST['taskId'];?>">
				<input type="hidden" id="projID" name="projID" value="<?=$_REQUEST['projID'];?>">
				<input type="hidden" id="userRoll" name="userRoll" value="<?=$_REQUEST['userRoll'];?>">
				<input type="hidden" id="location" name="location" value="<?=$_REQUEST['location'];?>">
				<input type="hidden" id="addShowSubLocation" name="addShowSubLocation" value="<?=$_REQUEST['addShowSubLocation'];?>">
				<input type="hidden" id="addShowSubLocation1" name="addShowSubLocation1" value="<?=$_REQUEST['addShowSubLocation1'];?>">
				<input type="hidden" id="addShowSubLocation2" name="addShowSubLocation2" value="<?=$_REQUEST['addShowSubLocation2'];?>">

		</table>
	</form>
	<br clear="all" />
</fieldset>

<?php } ?>
