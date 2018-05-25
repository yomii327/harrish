<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_GET['uniqueId'])){
	$subLocationId = $_GET['sub_location_id'];
	$locNameTree = $obj->promon_sublocationParent($subLocationId, ' > ');
	$locIdTree = $obj->promon_sublocationParentID($subLocationId, ' > ');
	$locationId = array_shift(array_values(explode(' > ', $locIdTree)));?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Add Task</legend>
			<form action="" name="addTaskForm" id="addTaskForm">
			<table border="0" width="100%" class="simpleTable">
				<tr>
					<td width="30%" style="color:#000000;">Task Location</td>
					<td width="70%"><?php echo $locNameTree;?></td>
				</tr>
				<tr>
					<td style="color:#000000;">Task <span class="req">*</span></td>
					<td>
						<input type="text" name="task" id="task" value="" autocomplete="off" />
						<div class="error-edit-profile-red" style="width:100px;display:none;"  id="taskError">Task require field</div>
					</td>
				</tr>
				<tr>
					<td style="color:#000000;">Start Date<span class="req">*</span></td>
					<td>
						<input type="text" name="startDate" id="startDate" value=""  autocomplete="off"  readonly="readonly"   />
						<div class="error-edit-profile-red" style="width:100px;display:none;"  id="startDateError">Start Date require field</div>
					</td>
				</tr>
				<tr>
					<td style="color:#000000;">End Date<span class="req">*</span></td>
					<td>
						<input type="text" name="endDate" id="endDate" value=""  autocomplete="off"  readonly="readonly"   />
						<div class="error-edit-profile-red" style="width:100px;display:none;"  id="endDateError">End Date require field</div>
					</td>
				</tr>
				<tr>
					<td style="color:#000000;">Issue To</td>
					<td>
						<input type="text" name="issueTo" id="issueTo" value=""  autocomplete="off"  />
					</td>
				</tr>
				<tr>
					<td style="color:#000000;">Hold Point</td>
					<td>
						<select name="holdingPoint" id="holdingPoint">
							<option value="Yes">Yes</option>
							<option value="No">N0</option>
						</select>
						<input type="hidden" name="locationTree" id="locationTree" value="<?php echo $locNameTree;?>" />
						<input type="hidden" name="locationid" id="locationid" value="<?php echo $locationId;?>" />
						<input type="hidden" name="sublocationid" id="sublocationid" value="<?php echo $subLocationId;?>" />
						<input type="hidden" name="locationTreeID" id="locationTreeID" value="<?php echo $locIdTree;?>" />
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2" style="padding-top:20px;">
						<input type="button" name="submit" id="submit" value="Submit" onclick="addTaskSubmit()" class="green_small"/>
                    </td>
				</tr>
			</table>
			</form>
		</fieldset>	
<?php
}
if(isset($_GET['antiqueID'])){
$data = array();
//condition for same name of location insertion in location table start here
	$locationDepth = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'pr_num_sublocations');
	$locArr = explode(' > ', $_POST['locationTree']);
	$currentDepth = sizeof($locArr);
	$parentId = end(explode(' > ', $_POST['locationTreeID']));
	$cheQRy == '';//Flag for insetion or not
	if($currentDepth < $locationDepth){
		$insertLocName = end($locArr);
		$lupCount = $locationDepth - $currentDepth;
		$secondTree = array();
		for($i=0;$i<$lupCount;$i++){
			$secondTree[] = $insertLocName;
		}
		$checkLocStr = $_POST['locationTree'].' > '.join(" > ", $secondTree);
		
		$cheQRy = $obj->getDataByKey('progress_monitoring', 'location_tree_name', $checkLocStr, 'sub_location_id');
		if($cheQRy == ''){
			for($i=0;$i<$lupCount;$i++){
				$dataArrKey = $parentId;
				$insQRY = "INSERT INTO project_monitoring_locations SET
								project_id = ".$_SESSION['idp'].",
								location_title = '".addslashes(trim($insertLocName))."',
								location_parent_id = '".$parentId."',
								created_date = NOW(),
								created_by = '".$_SESSION['ww_builder']['user_id']."', 
								last_modified_date = NOW(),
								last_modified_by = '".$_SESSION['ww_builder']['user_id']."'";
				mysql_query($insQRY);
				$parentId = mysql_insert_id();
				
				$locNameTree = $obj->promon_sublocationParent($parentId, ' > ');
				$isSpecial = sizeof(explode(" > ", $locNameTree));
				if($isSpecial == $locationDepth){
					$menu = "demo2";
					$nextStatus = false;
				}else{
					$menu = "demo1";
				}
				$query = 'UPDATE project_monitoring_locations SET location_tree_name = "'.$locNameTree.'", last_modified_date = NOW(), last_modified_by = '.$_SESSION['ww_builder_id'].' WHERE location_id = '.$parentId;
				mysql_query($query);
	//Prepare Ouptput Array
				$data[$dataArrKey] = '<ul ><li id="li_'.$parentId.'"><span class="jtree-button '.$menu.'" id="'.$parentId.'">'.addslashes(trim($insertLocName)).'</span></li></ul>';
			}		
		}else{
			$taskData = $obj->selQRYMultiple('task', 'progress_monitoring', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0 AND sub_location_id = '.$cheQRy);
			$taskCheckArr = array();
			foreach($taskData as $tData){
				$taskCheckArr[] = $tData['task'];
			}
			if(!in_array($_POST['task'], $taskCheckArr)){
				$insQRY = "INSERT INTO progress_monitoring SET
								project_id = ".$_SESSION['idp'].",
								location_id = ".$_POST['locationid'].",
								sub_location_id = ".$cheQRy.",
								task = '".trim(addslashes($_POST['task']))."',
								start_date = '".$obj->dateChanger('/', '-', $_POST['startDate'])."',
								end_date = '".$obj->dateChanger('/', '-', $_POST['endDate'])."',
								created_by = ".$_SESSION['ww_builder']['user_id'].",
								created_date = NOW(),
								last_modified_by = ".$_SESSION['ww_builder']['user_id'].",
								last_modified_date = NOW(),
								original_modified_date = NOW(),
								location_tree = '".$_POST['locationTreeID']."',
								location_tree_name = '".trim($checkLocStr)."',
								holding_point = '".$_POST['holdingPoint']."'";
				mysql_query($insQRY);
				$progress_id = mysql_insert_id();
			
				$issueto = $_POST['issueTo'];	
				if($issueto != ''){
					$issueToArr = array_map('trim', explode(",", $issueto));
					for($i=0;$i<count($issueToArr);$i++){
						$issuedToInsert = "INSERT INTO issued_to_for_progress_monitoring SET
												project_id = ".$_SESSION['idp'].",
												progress_id = '".$progress_id."',
												issued_to_name = '".addslashes(trim($issueToArr[$i]))."',
												last_modified_date = NOW(),
												last_modified_by = ".$_SESSION['ww_builder']['user_id'].",
												created_date = NOW(),
												created_by = ".$_SESSION['ww_builder']['user_id'];
						mysql_query($issuedToInsert); 
						
						$select_isseu = "SELECT * FROM inspection_issue_to WHERE issue_to_name = '".$issueToArr[$i]."' AND project_id = ".$_SESSION['idp']." AND is_deleted = 0";
						$result_issue = mysql_query($select_isseu);
						$issue_row = mysql_num_rows($result_issue);
						if($issue_row == 0){
							$issue_insert = "INSERT INTO inspection_issue_to SET
												issue_to_name = '".addslashes(trim($issueToArr[$i]))."',
												last_modified_date = NOW(),
												last_modified_by = ".$_SESSION['ww_builder']['user_id'].",
												created_date = NOW(),
												created_by = ".$_SESSION['ww_builder']['user_id'].",
												project_id = ".$_SESSION['idp'];
							mysql_query($issue_insert);
						}		 
					}
				}
				if($progress_id > 0){
					$data[$cheQRy] = '<li id="li_'.$progress_id.'" class="taskList"><span class="jtree-button demo3" id="'.$progress_id.'"><img src="images/task_simbol.png">&nbsp;'.$_POST['task'].'</span></li>';
					echo json_encode($data);
				}
			}else{
				echo 'Duplicate task';
			}
		}
	}else{
		$checkLocStr = $_POST['locationTree'];
	}
//condition for same name of location insertion in location table end here
	if($cheQRy == ''){
		$taskData = $obj->selQRYMultiple('task', 'progress_monitoring', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0 AND sub_location_id = '.$parentId);
		$taskCheckArr = array();
		foreach($taskData as $tData){
			$taskCheckArr[] = $tData['task'];
		}
		if(!in_array($_POST['task'], $taskCheckArr)){
			$insQRY = "INSERT INTO progress_monitoring SET
							project_id = ".$_SESSION['idp'].",
							location_id = ".$_POST['locationid'].",
							sub_location_id = ".$parentId.",
							task = '".trim(addslashes($_POST['task']))."',
							start_date = '".$obj->dateChanger('/', '-', $_POST['startDate'])."',
							end_date = '".$obj->dateChanger('/', '-', $_POST['endDate'])."',
							created_by = ".$_SESSION['ww_builder']['user_id'].",
							created_date = NOW(),
							last_modified_by = ".$_SESSION['ww_builder']['user_id'].",
							last_modified_date = NOW(),
							original_modified_date = NOW(),
							location_tree = '".$_POST['locationTreeID']."',
							location_tree_name = '".trim($checkLocStr)."',
							holding_point = '".$_POST['holdingPoint']."'";
			mysql_query($insQRY);
			$progress_id = mysql_insert_id();
	
			$issueto = $_POST['issueTo'];		
			if($issueto != ''){
				$issueToArr = array_map('trim', explode(",", $issueto));
				for($i=0;$i<count($issueToArr);$i++){
					$issuedToInsert = "INSERT INTO issued_to_for_progress_monitoring SET
											project_id = ".$_SESSION['idp'].",
											progress_id = '".$progress_id."',
											issued_to_name = '".addslashes(trim($issueToArr[$i]))."',
											last_modified_date = NOW(),
											last_modified_by = ".$_SESSION['ww_builder']['user_id'].",
											created_date = NOW(),
											created_by = ".$_SESSION['ww_builder']['user_id'];
					mysql_query($issuedToInsert); 
					
					$select_isseu = "SELECT * FROM inspection_issue_to WHERE issue_to_name = '".$issueToArr[$i]."' AND project_id = ".$_SESSION['idp']." AND is_deleted = 0";
					$result_issue = mysql_query($select_isseu);
					$issue_row = mysql_num_rows($result_issue);
					if($issue_row == 0){
						$issue_insert = "INSERT INTO inspection_issue_to SET
											issue_to_name = '".addslashes(trim($issueToArr[$i]))."',
											last_modified_date = NOW(),
											last_modified_by = ".$_SESSION['ww_builder']['user_id'].",
											created_date = NOW(),
											created_by = ".$_SESSION['ww_builder']['user_id'].",
											project_id = ".$_SESSION['idp'];
						mysql_query($issue_insert);
					}		 
				}
			}
	
			if($progress_id > 0){
				$data[$parentId] = '<li id="li_'.$progress_id.'" class="taskList"><span class="jtree-button demo3" id="'.$progress_id.'"><img src="images/task_simbol.png">&nbsp;'.$_POST['task'].'</span></li>';
				echo json_encode($data);
			}
		}else{
			echo 'Duplicate task';
		}
	}
}
?>