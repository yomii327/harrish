<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

set_time_limit(0);
include_once("commanfunction.php");
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);
include('func.php');

if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }

function croCHARtoURL($text){
	$convert_from = array("ˆ", "‡", "‰", "‹", "Š", "Œ", "¾", "", "", "Ž", "", "‘", "“", "’", "”", "•", "–", "˜", "—", "™", "›", "š", "¿", "", "œ", "ž", "Ÿ", "Ë", "ç", "å", "Ì", "€", "", "®", "‚", "é", "ƒ", "æ", "è", "í", "ê", "ë", "ì", "„", "ñ", "î", "ï", "Í", "…", "¯", "ô", "ò", "ó", "†"); 
	$convert_to = array("a", "a", "a", "a", "a", "a", "a", "c", "e", "e", "e", "e", "i", "i", "i", "i", "n", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "A", "A", "A", "A", "A", "A", "A", "C", "E", "E", "E", "E", "I", "I", "I", "I", "N", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U"); 
	return str_replace($convert_from, $convert_to, $text);
}
function normalise($string) {
	$string = str_replace("\r", "\n", $string);
	return $string;	
}
$builder_id=$_SESSION['ww_builder_id'];
if(isset($_REQUEST['id'])){
	$update = 'UPDATE qa_task_monitoring SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = "'.$builder_id .'" WHERE task_id = "'.base64_decode($_REQUEST['id']).'"';
	mysql_query($update);
	$_SESSION['progress_task_del'] = 'QA task deleted successfully.';
	header('loaction:?sect=qa_task_monitoring');
}
if(isset($_FILES['csvFile']['tmp_name'])){ // Location/ subloaction import CSV file.
	if (isset($_POST["updateFile"])){
		$updateFile = $_POST["updateFile"];
		$updateCount = 0;
	}else{
		$updateFile = '';
		$updateCount = '';
	}
	$success='';
	if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name'])){
		$filename = $_FILES['csvFile']['name']; // Csv File name
		$file_ext = explode('.',$filename);
		$ext = end($file_ext);//Update for . in file name
		if($ext == 'csv' || $ext == 'CSV'){
			$files = $_FILES['csvFile']['tmp_name'];	
			$databasetable = "progress_monitoring"; // database table name
			$fieldseparator = ","; // CSV file comma format
			$lineseparator = "\n";
			$csvfile = $files; //CSV file name
			$addauto = 1;
			$save = 1;
			$file = fopen($csvfile,"r");
			$size = filesize($csvfile); //check file record
			if(!$size) {
				echo "File is empty.\n";
				exit;
			}
			$lines = 0;
			$queries = "";
			$linearray = array();
			$fieldarray= array();
			$record='';
			
			//while( ($line = fgets($file)) != FALSE) 
			while(($data =  fgetcsv($file, 1000, ",")) != FALSE){
				$numOfCols = count($data);
				for($index = 0; $index < $numOfCols; $index++){
					$data[$index] = trim( stripslashes(normalise($data[$index])));
				}
				$fieldarray[] = $data;
			}

			$exData = $obj->selQRYMultiple('task_id, task, sub_location_id', 'qa_task_monitoring', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');
			$qaTaskData = array();
			foreach($exData as $exDataRow){
				if (empty ($qaTaskData[$exDataRow["sub_location_id"]]))
					$qaTaskData[$exDataRow["sub_location_id"]] = array();
				$qaTaskData[$exDataRow["sub_location_id"]][] = $exDataRow['task'];
			}

			//end foreach
			fclose($file);
			$totalCol=count($fieldarray);
//Find Special Character in CSV dated : 04/10/2012
			$err_msg = '';
			$legalCharArray = array('0', '10', '13', '32', '34', '38', '39', '40', '41', '44', '45', '46', '47', '63', '60', '62', '58', '124', '125', '123', '61', '43', '95', '42', '94', '37', '36', '35', '33', '126', '96', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '59', '64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80','81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93', '97', '98', '99', '100', '101', '102', '103', '104', '105', '106', '107', '108', '109', '110', '111', '112', '113', '114', '115', '116', '117', '118', '119', '120', '121', '122');
			$unicode_linenumbers = array();
			for($g=1; $g<$totalCol; $g++){
				$subCount = count($fieldarray[$g]);
				for($m=0;$m<$subCount; $m++){
					$string = $fieldarray[$g][$m];
					$strArray = str_split($string);
					$subSubCount  = count($strArray);
					for($b=0;$b<$subSubCount;$b++){
						$asciiVal = ord($strArray[$b]);
						if(!in_array($asciiVal, $legalCharArray)){
							$unicode_linenumbers[] = $g;
						}
					}
				}
			}
			if (!empty($unicode_linenumbers)){
				$err_msg = 'CSV file contains UNICODE characters at line no ' . join(',',$unicode_linenumbers) .', please remove them and try again.';
			}
//Finding Special Charcter Stop Here
			if($err_msg != ''){ }else{
				$totalCol = $totalCol;
				$num = count($fieldarray); //count no of records
				$farr = array(); // set array for parent id
				$rowdata = array(); // set array for parent id
				$task = array();
				$location = array();
				$sublocation = array();
				$holdPoint = array();
				$imageFlagArr = array();
				$collist = $fieldarray[0];
				for ($i=2; $i<count($collist); $i++){
					if ($collist[$i] == "Task"){
						break;
					}
				}
				//Sublocations count will be "Tasks" position - 1
				$num_sublocations = $i-1;
				for($k=1; $k<$totalCol; $k++){
					@$rowdata[$k] = $fieldarray[$k];
				}

				foreach($rowdata as $value){
					$numA = count($value);
					$location[] = croCHARtoURL(trim($value[0]));
					for ($i=0; $i<$num_sublocations; $i++){
						if (!is_array($sublocation[$i])){
							$sublocation[$i] = array();
						}
						$sublocation[$i][] = croCHARtoURL(trim($value[(1+$i)]));
					}
					$task[] = croCHARtoURL(trim($value[2+$i-1]));
					$holdPoint[] = croCHARtoURL(trim($value[5]));
					$imageFlagArr[] = croCHARtoURL(trim($value[6]));
				}
				$record = count($task);
				$count = 0;
				$location_id_array = array();
				$sub_location_id_array = array();
				$insertArray = array();
				$rowLocationID = '';
				$tempParentId = '';
				$rowSubLocationID = '';
				$pidCount = $num_sublocations;
				for($h=0; $h<$record; $h++){
					$locArray = array();
					if(!empty($location[$h])){//Root Location Tree
						$rootLocation = '';
						$rootLocation = $obj->QAcheckInsetIfNotExistLoc('qa_task_locations', 'location_parent_id', 0, 'location_title', $location[$h], 'location_id', $builder_id, $_SESSION['idp'], '');
						for($g=0; $g<$num_sublocations; $g++){
						      if(!empty($sublocation[$g][$h]))
							       $locArray[] = $sublocation[$g][$h];
						      else//in case of there is empty sublocation, give same name as Parent Location
							       $locArray[] = $locArray[sizeof($locArray) - 1];
						}
						$rowLocationTree = $obj->QArecursiveInsertLocation($locArray, $rootLocation, $_SESSION['idp'], $builder_id, '');
						$locArrayExp = explode(' > ', $rowLocationTree);
						$rowLocationID = $rootLocation;//LocationID to be insert while 
						$rowSubLocationID = end($locArrayExp);//LocationID to be insert
						$rowLocationTreeInsert = $rootLocation.' > '.$rowLocationTree;
//Create Row Array Here
						if(in_array($task[$h], $qaTaskData[$rowSubLocationID])){
							$count++;
						}else{
							$lines++;
							if (!empty($task[$h])){//Insert Data Here
								//unique taskID generator Dated 30-05-2013
								$preTaskID = $obj->getDataByKey('unique_qa_taskid', 'is_deleted', '0', 'MAX(unique_qa_taskid)');
								if($preTaskID){
									$rs = mysql_query("INSERT INTO unique_qa_taskid SET task_id='".++$preTaskID."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder']['user_id']."', last_modified_by='".$_SESSION['ww_builder']['user_id']."', created_date=NOW(), last_modified_date=NOW(), created_by='".$_SESSION['ww_builder']['user_id']."'");
									if($rs){
										$newTaskID = mysql_insert_id();
									}
								}else{
									$taskID = $obj->selQRY('MAX(task_id) as newTaskid', 'qa_task_monitoring', 'is_deleted = 1');
									$rs = mysql_query("INSERT INTO unique_qa_taskid SET task_id='".$taskID['newTaskid']."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder']['user_id']."', last_modified_by='".$_SESSION['ww_builder']['user_id']."', created_date=NOW(), last_modified_date=NOW(), created_by='".$_SESSION['ww_builder']['user_id']."'");
									if($rs){
										$newTaskID = mysql_insert_id();
									}
								}
								$holdPointVal = 'No';
								if($holdPoint[$h] != "")
									$holdPointVal = $holdPoint[$h];
								$imageFlagVal = 0;
								if($imageFlagArr[$h] == "Yes")
									$imageFlagVal = 1;
								$insertQryTask = "INSERT INTO qa_task_monitoring SET
														task_id = '".$newTaskID."',
														project_id = '".$_SESSION['idp']."',
														location_id = '".$rowLocationID."',
														sub_location_id = '".$holdPointVal."',
														task = '".$task[$h]."',
														hold_point = '".$holdPointVal."',
														is_image_require = '".$imageFlagVal."',
														created_by = '".$builder_id."',
														created_date = NOW(),
														last_modified_by = '".$builder_id."',
														last_modified_date = NOW()";
								mysql_query($insertQryTask);
								//$rowArray = array($_SESSION['idp'], $rowLocationID, $rowSubLocationID, $task[$h], $builder_id, 'Now()', $builder_id, 'Now()', '', '', $rowLocationTreeInsert);
							}
						}
					}else{
						$subLocArray = array(); //rowLocationtree = 1 > 2 > 3 > 4 > 5 > 6
						$currentLocationArray = explode(' > ', $rowLocationTree);
						$removeLocArray = array();
						for($i = 0; $i<$num_sublocations; $i++){
							if(!empty($sublocation[$i][$h])){//just have new array till this point
							    for ($j=0; $j<$i; $j++){
							       $removeLocArray[] = $currentLocationArray[$j];
							    }
							    break;
							}
						}
						$parentLocationId = end($removeLocArray);
						if(empty($removeLocArray)){
							$parentLocationId = $rowLocationID;
						}	
						for($g=$i; $g<$num_sublocations; $g++){
							if(!empty($sublocation[$g][$h])){
								$subLocArray[] = $sublocation[$g][$h];
							}else{//in case of there is empty sublocation, give same name as Parent Location
								$subLocArray[] = $subLocArray[sizeof($subLocArray) - 1];
							}
						}///creating same name of sub locations, incase of empty sub locations
						if(!empty($subLocArray)){
							$rowLocationTree = $obj->QArecursiveInsertLocation($subLocArray, $parentLocationId, $_SESSION['idp'], $builder_id, join (' > ', $removeLocArray));
						}
						$locArrayExp = explode(' > ', $rowLocationTree);
						$rowSubLocationID = end($locArrayExp);//LocationID to be insert
						$rowLocationTreeInsert = $rowLocationID.' > '.$rowLocationTree;
//Create Row Array Here
						if(in_array($task[$h], $qaTaskData[$rowSubLocationID])){
							$count++;
						}else{
							$lines++;
							if (!empty($task[$h])){
								$preTaskID = $obj->getDataByKey('unique_qa_taskid', 'is_deleted', '0', 'MAX(unique_qa_taskid)');
								if($preTaskID){
									$rs = mysql_query("INSERT INTO unique_qa_taskid SET task_id='".++$preTaskID."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder']['user_id']."', last_modified_by='".$_SESSION['ww_builder']['user_id']."', created_date=NOW(), last_modified_date=NOW(), created_by='".$_SESSION['ww_builder']['user_id']."'");
									if($rs){
										$newTaskID = mysql_insert_id();
									}
								}else{
									$taskID = $obj->selQRY('MAX(task_id) as newTaskid', 'qa_task_monitoring', 'is_deleted = 1');
									$rs = mysql_query("INSERT INTO unique_qa_taskid SET task_id='".$taskID['newTaskid']."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder']['user_id']."', last_modified_by='".$_SESSION['ww_builder']['user_id']."', created_date=NOW(), last_modified_date=NOW(), created_by='".$_SESSION['ww_builder']['user_id']."'");
									if($rs){
										$newTaskID = mysql_insert_id();
									}
								}
								$holdPointVal = 'No';
								if($holdPoint[$h] != "")
									$holdPointVal = $holdPoint[$h];
								$imageFlagVal = 0;
								if($imageFlagArr[$h] == "Yes")
									$imageFlagVal = 1;
								$insertQryTask = "INSERT INTO qa_task_monitoring SET
														task_id = '".$newTaskID."',
														project_id = '".$_SESSION['idp']."',
														location_id = '".$rowLocationID."',
														sub_location_id = '".$rowSubLocationID."',
														task = '".$task[$h]."',
														hold_point = '".$holdPointVal."',
														is_image_require = '".$imageFlagVal."',
														created_by = '".$builder_id."',
														created_date = NOW(),
														last_modified_by = '".$builder_id."',
														last_modified_date = NOW()";
								mysql_query($insertQryTask);
								//$rowArray = array($_SESSION['idp'], $rowLocationID, $rowSubLocationID, $task[$h], $builder_id, 'Now()', $builder_id, 'Now()', '', '', $rowLocationTreeInsert);
							}
						}
					}
					if (!empty ($rowArray)){
						$insertArray[] = $rowArray;
						$rowArray = array();
					}
				}
//Insert Data Here
				$inserted = $obj->bulkInsert($insertArray, 'project_id, location_id, sub_location_id, task, created_by, created_date, last_modified_by, last_modified_date, status, comments, location_tree', 'qa_task_monitoring');
				
				$upUpdateQuery = "update user_projects set qa_num_sublocations = ".$num_sublocations.", last_modified_by = '".$builder_id."', last_modified_date = NOW() where project_id = ".$_SESSION["idp"];
				mysql_query($upUpdateQuery);

//Update Location Tree start here
				$locData = $obj->selQRYMultiple('location_id, location_title, location_parent_id', 'qa_task_locations', 'is_deleted = 0 AND project_id = '.$_SESSION["idp"]);

				foreach($locData as $lData){
					$locNameTree = $obj->qa_sublocationParent($lData['location_id'], ' > ');
					$query = 'UPDATE qa_task_locations SET location_tree_name = "'.$locNameTree.'", last_modified_by = "'.$builder_id.'", last_modified_date = NOW(), qrcode = "'.$locNameTree.'" WHERE location_id = '.$lData['location_id'];
					mysql_query($query);
				}
//Update Location Tree end here
//Update location tree in qa_task_monitoring start here
				$qaTaskDataEx = $obj->selQRYMultiple('sub_location_id, task_id', 'qa_task_monitoring', 'is_deleted IN (0) and project_id = '.$_SESSION["idp"]);
				if(!empty($qaTaskDataEx)){
					foreach($qaTaskDataEx as $row){
						$location_id = $row["sub_location_id"];
						$progress_id = $row["task_id"];
						$locationsStr = $obj->qa_sublocationParentID($location_id, ' > ');
						$query = "UPDATE qa_task_monitoring SET location_tree = '".$locationsStr."', last_modified_date = NOW() WHERE task_id = ".$progress_id." AND sub_location_id = ".$location_id;
						mysql_query ($query);
					}
				}
//Update location tree in qa_task_monitoring end here

				if($inserted){
					$success = 'Total '.$lines.' record(s) inserted.<br />Total '.$count.' Duplicate Records';
				}else{
					if(isset($count) && !empty($count)){
						$success = "Total ".$count." Duplicate Records";
					}
				}
			}
		}else{
			$err_msg= 'Please select .csv file.';
		}	
	}else{
		$err_msg= 'Please select file.';
	}
}
$id = base64_encode($_SESSION['idp']);
$hb = base64_encode($_SESSION['hb']); ?>
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript" src="js/jquery.tree_qa.js"></script>
<script type="text/javascript" src="js/jquery.contextmenu.r2.js"></script>
<script type="text/javascript" src="js/qa_location_functions.js"></script>
<script type="text/javascript">
var deadLock = false;//To stop multiple ajax call

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
function showProgress() {if (!spinnerVisible) {$("div#spinner").fadeIn("fast");spinnerVisible = true;}};
function hideProgress() {if (spinnerVisible) {var spinner = $("div#spinner");spinner.stop();spinner.fadeOut("fast");spinnerVisible = false;}};
$(document).ready(function() {
	$('ul.telefilms').tree({default_expanded_paths_string : '0/0/0,0/0/2,0/2/4'});
	console.log('werdsf');
	$('span.demo1').contextMenu('myMenu2', {
		bindings: {
			'add': function(t) {
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add_qa.php?location_id='+t.id, loadingImage);
			},
			'edit': function(t) {
				var parentId = $('#'+t.id).parent().parent().parent().get(0);
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_qa.php?location_id='+t.id+'&parent_id='+parentId.id, loadingImage);
			},
			'delete': function(t) {
				var r = jConfirm('You are about to delete this location and it\'s task, inspections and QA inspection &minus; are you sure you want to do this ?', null, function(r){
					if (r==true){
						showProgress();
						$.post("location_tree_delete_qa.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
							hideProgress();
							if(data){
								$('#li_'+t.id).hide('slow');
								//jAlert('Location Deleted Successfully !');
							}else{
								jAlert(data);
							}
						});
					}
				});
			},
			'addTask': function(t) {
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_qa.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
			},
			'qrCode': function(t) {
				console.log(t);
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'generate_qr_code.php?locationID='+t.id+'&tableName=qa_task_locations&uniqueId='+Math.random(), loadingImage);
			},
		}
	});
	$('span.demo2').contextMenu('myMenu1', {
		bindings: {
			'edit': function(t) {
								var parentId = $('#'+t.id).parent().parent().parent().get(0);
								modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_qa.php?location_id='+t.id+'&parent_id='+parentId.id, loadingImage);
							},
			'delete': function(t) {
				var r = jConfirm('You are about to delete this location and it\'s task, inspections and QA inspection &minus; are you sure you want to do this ?', null, function(r){
					if (r==true){
						showProgress();
						$.post("location_tree_delete_qa.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
							hideProgress();
							if(data){
								$('#li_'+t.id).hide('slow');
								//jAlert('Location Deleted Successfully !');
							}else{
								jAlert(data);
							}
						});
					}
				});
			},
			'addTask': function(t) {
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_qa.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
			},
			'qrCode': function(t) {
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'generate_qr_code.php?locationID='+t.id+'&tableName=qa_task_locations&uniqueId='+Math.random(), loadingImage);
			},
		}
	});
	$('span.demo3').contextMenu('myMenu3', {
		bindings: {
			'viewTask': function(t) {
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'view_task_qa.php?task_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
			},
			'editTask': function(t) {
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_task_qa.php?task_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
			},
			'deleteTask': function(t) {
				var r = jConfirm('Do you want to delete task ?', null, function(r){
					if (r==true){
						showProgress();
						$.post("delete_task_qa.php", {task_id:t.id, antiqueID:Math.random()}).done(function(data) {
							hideProgress();
							if(data == 1){
								$('#li_'+t.id).hide('slow');
								//jAlert('Task Deleted Successfully !');
							}else{
								jAlert(data);
							}
						});
					}
				});
			},
		}
	});
	$('span.demo4').contextMenu('myMenu4', {
		bindings: {
			'add': function(t) {
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add_qa.php?location_id='+t.id, loadingImage);
			},
			'edit': function(t) {
				var parentId = $('#'+t.id).parent().parent().parent().get(0);
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_qa.php?location_id='+t.id+'&parent_id='+parentId.id, loadingImage);
			},
			'delete': function(t) {
				var r = jConfirm('You are about to delete this location and it\'s task, inspections and QA inspection &minus; are you sure you want to do this ?', null, function(r){
					if (r==true){
						showProgress();
						$.post("location_tree_delete_qa.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
							hideProgress();
							if(data){
								$('#li_'+t.id).hide('slow');
								//jAlert('Location Deleted Successfully !');
							}else{
								jAlert(data);
							}
						});
					}
				});
			},
			'addTask': function(t) {
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_qa.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
			},
			'qrCode': function(t) {
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'generate_qr_code.php?locationID='+t.id+'&tableName=qa_task_locations&uniqueId='+Math.random(), loadingImage);
			},
			'adHocITR': function(t) {
				modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'generate_qr_code.php?locationID='+t.id+'&tableName=qa_task_locations&uniqueId='+Math.random(), loadingImage);
			},
		}
	});
});

function addLocation(){ 
	var params = '';
	var location = $('#subLocation').val();
	var locationId = $('#locationId').val();
	var checkProject = $('#checkProject').val();
	if (location==""){$('#locationError').show('slow'); return false;}else{$('#locationError').hide('slow');}
	showProgress();
	if(checkProject == 'Yes'){ params = {location:location,locationId:0, uniqueId:Math.random()} }
	if(checkProject == 'No'){ params = {location:location,locationId:locationId, uniqueId:Math.random()} }

	$.post("location_tree_add_qa.php", params).done(function(data) {
		hideProgress();
		if(data != 'Duplicate location'){
			if(checkProject == 'No'){	jQuery("#li_"+locationId).append(data);	}
			if(checkProject == 'Yes'){ 	jQuery("#projectId_"+locationId).append(data);	}
			closePopup(fadeOutTime);
			$('span.demo1').contextMenu('myMenu2', {
				bindings: {
					'add': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add_qa.php?location_id='+t.id, loadingImage);
					},
					'edit': function(t) {
								var parentId = $('#'+t.id).parent().parent().parent().get(0);
								modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_qa.php?location_id='+t.id+'&parent_id='+parentId.id, loadingImage);
							},
					'delete': function(t) {
						var r = jConfirm('You are about to delete this location and it\'s task, inspections and QA inspection &minus; are you sure you want to do this ?', null, function(r){
							if (r==true){
								showProgress();
								$.post("location_tree_delete_qa.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
									hideProgress();
									if(data){
										$('#li_'+t.id).hide('slow');
										//jAlert('Location Deleted Successfully !');
									}else{
										jAlert(data);
									}
								});
							}
						});
					},
					'addTask': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_qa.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
					},
					'qrCode': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'generate_qr_code.php?locationID='+t.id+'&tableName=qa_task_locations&uniqueId='+Math.random(), loadingImage);
					},
				}
			});
			$('span.demo2').contextMenu('myMenu1', {
				bindings: {
					'edit': function(t) {
								var parentId = $('#'+t.id).parent().parent().parent().get(0);
								modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_qa.php?location_id='+t.id+'&parent_id='+parentId.id, loadingImage);
							},
					'delete': function(t) {
						var r = jConfirm('You are about to delete this location and it\'s task, inspections and QA inspection &minus; are you sure you want to do this ?', null, function(r){
							if (r==true){
								showProgress();
								$.post("location_tree_delete_qa.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
									hideProgress();
									if(data){
										$('#li_'+t.id).hide('slow');
										//jAlert('Location Deleted Successfully !');
									}else{
										jAlert(data);
									}
								});
							}
						});
					},
					'addTask': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_qa.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
					},
					'qrCode': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'generate_qr_code.php?locationID='+t.id+'&tableName=qa_task_locations&uniqueId='+Math.random(), loadingImage);
					},
				}
			});
			$('span.demo3').contextMenu('myMenu3', {
				bindings: {
					'viewTask': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'view_task_qa.php?task_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
					},
					'editTask': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_task_qa.php?task_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
					},
					'deleteTask': function(t) {
						var r = jConfirm('Do you want to delete task ?', null, function(r){
							if (r==true){
								showProgress();
								$.post("delete_task_qa.php", {task_id:t.id, antiqueID:Math.random()}).done(function(data) {
									hideProgress();
									if(data == 1){
										$('#li_'+t.id).hide('slow');
										//jAlert('Task Deleted Successfully !');
									}else{
										jAlert(data);
									}
								});
							}
						});
					},
				}
			});
		}else{
			jAlert('Duplicate location name, location not added');
		}
	});
}

function editLocation(){
	var location = $('#locationName').val();
	var locationId = $('#locationIdEdit').val();
	var locationParentID = $('#locationParentID').val();
	if (location==""){$('#locationError').show('slow'); return false;}else{$('#locationError').hide('slow');}
	showProgress();
	$.post("location_tree_edit_qa.php", {location:location, locationId:locationId, parent_id:locationParentID, uniqueId:Math.random()}).done(function(data) {
		hideProgress();
		if(data != 'Duplicate location'){
			document.getElementById(locationId).innerHTML=data;
			closePopup(fadeOutTime);
		}else{
			jAlert('Duplicate location name, location not updated');
		}
	});
}

function mapInspectionId(){
	showProgress();
	$.post('map_inspection_location.php?antiqueID='+Math.random(), $('#mapInspectionLocationForm').serialize()).done(function(data) {
		hideProgress();
		jAlert("Inspection location mapped");
		$('span.demo2').contextMenu('myMenu1', {
			bindings: {
				'mapInspectionLocation': function(t) {
					modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'map_inspection_location.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage, addDatePicker);
				},
				'qrCode': function(t) {
					modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'generate_qr_code.php?locationID='+t.id+'&tableName=qa_task_locations&uniqueId='+Math.random(), loadingImage);
				},
			}
		});
		closePopup(300);
	});
	return false;
}
function addTaskSubmit(){
	if($.trim($('#task').val()) == ''){$('#taskError').show('slow'); return false;}else{$('#taskError').hide('slow');}
	showProgress();
	$.post('add_task_qa.php?antiqueID='+Math.random(), $('#addTaskForm').serialize()).done(function(data) {
		hideProgress();
		console.log(data);
		if(data != 'Duplicate task'){
			var jsonResult = $.parseJSON(data);
			$.each(jsonResult, function(i, item) {
				$('#connectedSortable_'+i).append(item);
			});
			$('span.demo1').contextMenu('myMenu2', {
				bindings: {
					'add': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add_qa.php?location_id='+t.id, loadingImage);
					},
					'edit': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_qa.php?location_id='+t.id, loadingImage);
					},
					'delete': function(t) {
						var r = jConfirm('You are about to delete this location and it\'s task, inspections and QA inspection &minus; are you sure you want to do this ?', null, function(r){
							if (r==true){
								showProgress();
								$.post("location_tree_delete_qa.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
									hideProgress();
									if(data){
										$('#li_'+t.id).hide('slow');
										//jAlert('Location Deleted Successfully !');
									}else{
										jAlert(data);
									}
								});
							}
						});
					},
					'addTask': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_qa.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
					},
					'qrCode': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'generate_qr_code.php?locationID='+t.id+'&tableName=qa_task_locations&uniqueId='+Math.random(), loadingImage);
					},
				}
			});
			$('span.demo2').contextMenu('myMenu1', {
				bindings: {
					'edit': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_qa.php?location_id='+t.id, loadingImage);
					},
					'delete': function(t) {
						var r = jConfirm('You are about to delete this location and it\'s task, inspections and QA inspection &minus; are you sure you want to do this ?', null, function(r){
							if (r==true){
								showProgress();
								$.post("location_tree_delete_qa.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
									hideProgress();
									if(data){
										$('#li_'+t.id).hide('slow');
										//jAlert('Location Deleted Successfully !');
									}else{
										jAlert(data);
									}
								});
							}
						});
					},
					'addTask': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_qa.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
					},
					'qrCode': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'generate_qr_code.php?locationID='+t.id+'&tableName=qa_task_locations&uniqueId='+Math.random(), loadingImage);
					},
				}
			});
			$('span.demo3').contextMenu('myMenu3', {
				bindings: {
					'viewTask': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'view_task_qa.php?task_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
					},
					'editTask': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_task_qa.php?task_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
					},
					'deleteTask': function(t) {
						var r = jConfirm('Do you want to delete task ?', null, function(r){
							if (r==true){
								showProgress();
								$.post("delete_task_qa.php", {task_id:t.id, antiqueID:Math.random()}).done(function(data) {
									hideProgress();
									if(data == 1){
										$('#li_'+t.id).hide('slow');
										//jAlert('Task Deleted Successfully !');
									}else{
										jAlert(data);
									}
								});
							}
						});
					},
				}
			});
			closePopup(300);
		}else{
			jAlert('Duplicate task, task not added');
		}
	});
}

function editTaskSubmit(){
	if($.trim($('#task').val()) == ''){$('#taskError').show('slow'); return false;}else{$('#taskError').hide('slow');}
	showProgress();
	$.post('edit_task_qa.php?antiqueID='+Math.random(), $('#editTaskForm').serialize()).done(function(data) {
		hideProgress();
		if(data != 'Duplicate task'){
			var jsonResult = JSON.parse(data);	
			$('#'+jsonResult.task_id).html('<img src="images/task_simbol.png">&nbsp;'+jsonResult.task);
				if(jsonResult.holdPoint == 'Yes')
					$('#'+jsonResult.task_id).html('<img src="images/task_simbol.png">&nbsp;'+jsonResult.task+'&nbsp;<img src="images/tap_and_hold.png" title="hold point">');
			jAlert('Task Edit Successfully');
			closePopup(300);
		}else{
			jAlert('Duplicate task, task not updated');
		}
	});
}

function addDatePicker(){
	new JsDatePick({ useMode:2, target:"startDate", dateFormat:"%d-%m-%Y" });
	new JsDatePick({ useMode:2, target:"endDate", dateFormat:"%d-%m-%Y" });
}

function checkDates(obj, obj1){
	if(obj!='' || obj1!=''){
		if(obj=='' && obj1!=''){
			jAlert('Please Select Form Date First !');
			return false;		
		}else{
			var fromDate = new Date(obj.substr(6,4), obj.substr(3,2), obj.substr(0,2));
			var toDate = new Date(obj1.substr(6,4), obj1.substr(3,2), obj1.substr(0,2));
			if((toDate.getTime() - fromDate.getTime()) < 0){
				jAlert('End Date in Not Less Than Start Date !');return false;
			}else{
				return true;		
			}
		}
	}
}
</script>
<div id="middle" style="padding-top:10px;">
	<div id="leftNav" style="width:250px;float:left;"><?php include 'side_menu.php';?></div>
	<div id="rightCont" style="float:left;width:700px;">
		<div class="content_hd1" style="width:500px;margin-top:12px;">
			<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font>
			<a style="float:left;margin-top:-25px; width:87px;margin-left:590px;" href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>"><img src="images/back_btn2.png" style="border:none;" /></a>
		</div><br clear="all" />
		<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top:-15px;margin-top:0px\9;">
		<?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { ?>
			<div class="success_r" style="height:45px;width:235px;"><p><?php echo $_SESSION['add_project'] ; ?></p></div>
		<?php unset($_SESSION['add_project']);} ?><?php if((isset($success)) && (!empty($success))) { ?>
			<div class="success_r" style="height:45px;width:235px;"><p><?php echo $success; ?></p></div>
		<?php }
			if((isset($err_msg)) && (!empty($err_msg))) { ?>
			<div class="failure_r" style="height:45px;width:500px;"><p><?php echo $err_msg; ?></p></div>
		<?php } ?>
		</div>
		<div class="content_container" style="float:left;width:690px;border:1px solid; margin-bottom:10px;text-align:center;margin-left:10px;margin-right:10px;height:130px;">
			<div style="width:722px; height:50px; float:left; margin-top:5px;">
				<form method="post" name="csvLocation" id="csvLocation" enctype="multipart/form-data" onSubmit="return validateSubmit()">
					<table width="690px" border="0" cellspacing="0" cellpadding="3">
						<tr>
							<td colspan="3" align="left">
								<a href="/csv/QA_Task_Monitoring_Template.csv" style="text-decoration:none;color:#FFF;"><strong style="font-size:16px;">Click <u>here</u> to download CSV template</strong></a>
							</td>
							<td>
								<input type="button"  class="submit_btn" onclick=location.href="qa_task_export.php"  style="background:none;background-image:url(images/export_csv_btn.png); width:87px; height:30px;border:none;margin-left:15px;" />
							</td>
						</tr>
						<tr>
							<td width="185px;" align="left">&nbsp;</td>
							<td width="130px;">Upload&nbsp;CSV&nbsp;File&nbsp;:</td>
							<td width="240px;" align="left">
								<input type="file" name="csvFile" id="csvFile" value="" />
							</td>
							<td width="120px;" height="40px">
								<input  class="submit_btn" type="button" style="background: url('images/import_csv_btn.png') repeat scroll 0 0 transparent;border: medium none;height: 30px;margin-left:12px;width: 87px;color:transparent;font-size:0px;"  name="location_csv" id="location_csv" value="Import CSV" onclick="validateSubmit();" />
							</td>
						</tr>
						<tr>
							<td colspan="3" align="left">
								<input type="hidden" id="updateFile" name="updateFile" value="1" />
							</td>
							<td width="120px;" height="40px">
<!--								<input  class="submit_btn" type="button" style="background: url('images/bulk_update_btn.png') repeat scroll 0 0 transparent;border: medium none;height: 30px;margin-left:12px;width: 87px;color:transparent;font-size:0px;"  name="location_csv" id="location_csv" value="Import CSV" onclick="validateUpdateSubmit();" />-->
							</td>
						</tr>
					</table>
				</form>
		
			<br clear="all" />
			</div>
		</div>
		<br clear="all" />
		<div id="viewSwither">
			<label for="locationView"><input type="radio" id="locationView" name="viewSelector" onclick="changeScreen('locationViewScreen');" checked="checked" /> Tree View</label>
			<label for="tableView"><input type="radio" id="tableView" name="viewSelector" onclick="changeScreen('tableViewScreen');
" /> Table View</label>
		</div>
		<br /><br /><br clear="all" />
		<div class="big_container" id="tableViewScreen" style="width:722px;margin-left:9px;display:none;"><?php include'qa_task_csv_table.php';?></div>
		<div class="big_container" id="locationViewScreen" style="width:712px;margin-left:9px;">
		<img class="setOrderImg" src="images/order_wallchart.png" style="margin-bottom:15px;cursor:pointer;" title="Set order for Wallchart Report" />
			<div id="locationsContainer">
				<span id="projectId_<?php echo $_SESSION['idp']?>">
				<span class="jtree-button" id="projectId_<?php echo $_SESSION['idp']?>" style="background-image: url('images/project.png');background-position: 0 15px;background-repeat: no-repeat;display: block;height: 30px;padding-left: 40px;padding-top: 9px;width: 90%;font-size:26px;cursor: pointer;"><?php echo $projectName?></span>
				<?php $q = "select location_id, location_title from qa_task_locations where project_id = ".$_SESSION['idp']." and location_parent_id = '0' and is_deleted = '0' order by location_id";
					$re = mysql_query($q);
					if(mysql_num_rows($re) > 0){
						echo '<ul class="telefilms">';
						while($locations = mysql_fetch_array($re)){
							if($locations['location_title'] != 'History'){
								echo '<li id="li_'.$locations['location_id'].'">';
								$data = $obj->recurtionQA($locations['location_id'], $_SESSION['idp']);
								$taskData = $obj->selQRYMultiple('task_id, task', 'qa_task_monitoring', 'sub_location_id = '.$locations['location_id'].' AND is_deleted= 0 AND project_id = '.$_SESSION['idp']);
								if($data!=''){
									echo '<span style="cursor:pointer;" class="jtree-arrow close"><img src="images/plus-icon.png"></span>';
								}
								echo '<span class="jtree-button demo1" id="'.$locations['location_id'].'">'.stripslashes($locations['location_title']).'</span></li>';
								$task = '';
								if(!empty($taskData)){
									foreach($taskData as $tData){
										$task .= '<li id="li_'.$tData['task_id'].'" class="taskList"><span class="jtree-button demo3" id="'.$tData['task_id'].'"><img src="images/task_simbol.png">&nbsp;'.$tData['task'].'</span></li>';
									}
								}
								echo $task;
							}else{
								$historyLoc = '<li id="li_'.$locations['location_id'].'">';
								$data = $obj->recurtionQA($locations['location_id'], $_SESSION['idp']);
								$taskData = $obj->selQRYMultiple('task_id, task', 'qa_task_monitoring', 'sub_location_id = '.$locations['location_id'].' AND is_deleted= 0 AND project_id = '.$_SESSION['idp']);
								if($data!=''){
									$historyLoc .= '<span style="cursor:pointer;" class="jtree-arrow close"><img src="images/plus-icon.png"></span>';
								}
								$historyLoc .= '<span class="jtree-button demo1" id="'.$locations['location_id'].'">'.stripslashes($locations['location_title']).'</span></li>';
								$task = '';
								if(!empty($taskData)){
									foreach($taskData as $tData){
										$task .= '<li id="li_'.$tData['task_id'].'" class="taskList"><span class="jtree-button demo3" id="'.$tData['task_id'].'"><img src="images/task_simbol.png">&nbsp;'.$tData['task'].'</span></li>';
									}
								}
								$historyLoc .= $task;
							}
						}
						echo $historyLoc;
						echo '</ul>';
					}?>
				</span>
			</div>
			<div class="contextMenu" id="myMenu2">
				<ul style="width:110px !important;">
					<li id="add"><img src="images/add.png" align="absmiddle" width="14"  height="14"/> Add</li>
					<li id="edit"><img src="images/edit_right.png"  align="absmiddle" width="16" height="16" /> Edit</li>
					<li id="delete"><img src="images/delete.png"  align="absmiddle" width="14" height="15" /> Delete</li>
					<li id="qrCode"><img src="images/qr_code.png"  align="absmiddle" width="14" height="15" /> Generat QR Code</li>
				</ul>
			</div>
			<div class="contextMenu" id="myMenu1">
				<ul style="width:150px !important;">
					<li id="edit"><img src="images/edit_right.png"  align="absmiddle" width="16" height="16" /> Edit</li>
					<li id="delete"><img src="images/delete.png"  align="absmiddle" width="14" height="15" /> Delete</li>
					<li id="addTask"><img src="images/add_task_icon.png" align="absmiddle" width="14"  height="14"/> Add Task</li>
					<li id="mapInspectionLocation"><img src="images/location_map.png" align="absmiddle" width="14"  height="14"/> Map to Inspection Location</li>
					<li id="qrCode"><img src="images/qr_code.png"  align="absmiddle" width="14" height="15" /> Generat QR Code</li>
				</ul>
			</div>
			<div class="contextMenu" id="myMenu3">
				<ul style="width:110px !important;">
					<li id="viewTask"><img src="images/edit_task_icon.png" align="absmiddle" width="14"  height="14"/> View Task</li>
					<li id="editTask"><img src="images/view_task_icon.png" align="absmiddle" width="14"  height="14"/> Edit Task</li>
					<li id="deleteTask"><img src="images/delete_task_icon.png" align="absmiddle" width="14"  height="14"/> Delete Task</li>
				</ul>
			</div>
			<div class="contextMenu" id="myMenu4">
				<ul style="width:150px !important;">
					<li id="add"><img src="images/add.png" align="absmiddle" width="14"  height="14"/> Add</li>
					<li id="edit"><img src="images/edit_right.png"  align="absmiddle" width="16" height="16" /> Edit</li>
					<li id="delete"><img src="images/delete.png"  align="absmiddle" width="14" height="15" /> Delete</li>
					<li id="qrCode"><img src="images/qr_code.png"  align="absmiddle" width="14" height="15" /> Generat QR Code</li>
					<li id="adHocITR"><img src="images/add.png"  align="absmiddle" width="14" height="15" /> Adhoc ITR</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
function validateSubmit(){
	 document.getElementById("updateFile").value = 0;
	var r = jConfirm('Do you want to upload "QA Task Monitoring CSV" ?', null, function(r){
		if (r === true){
			document.forms["csvLocation"].submit();	
		}else{
			return false;
		}
	});
}
function validateUpdateSubmit(){
	 document.getElementById("updateFile").value = 1;
	var r = jConfirm('Do you want to upload "QA Task Monitoring CSV" for Update ?', null, function(r){
		if (r === true){
			document.forms["csvLocation"].submit();	
		}else{
			return false;
		}
	});
}
$('.setOrderImg').click(function(){
	var align = 'center';
	var top = 30;
	var width = 680;
	var padding = 10;
	var backgroundColor = '#FFFFFF';
	var borderColor = '#333333';
	var borderWeight = 4;
	var borderRadius = 5;
	var fadeOutTime = 300;
	var disableColor = '#666666';
	var disableOpacity = 40;
	
	var projectID = <?=$_SESSION['idp'];?>;
	
	var loadingImage = 'images/loadingAnimation.gif';		//Use relative path from this page
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'qa_location_set_order.php?projectID='+projectID, loadingImage, temp);
});
function temp(){$("#sortable1").sortable({connectWith: ".connectedSortable"}).disableSelection();}
function saveLocationOrder(){
	showProgress();
	var subLocid = new Array;
	var subLocVal = new Array;
	var exLocVal = new Array;
	var i=0;
	$("#sortable1").find('li').each(function() {
		subLocid[i] = this.id;
		subLocVal[i] = $(this).text();
		if($(this).children().is(':checked')){
			exLocVal[i] = 'YES';
		}else{
			exLocVal[i] = 'NO';
		}
		i++;
	});
	$.post("qa_location_save_order.php", {locId:subLocid, locVal:subLocVal, excludedLocVal:exLocVal, name:Math.random()}).done(function(data) {
		hideProgress();
		if(data){
			$('#msgHolderDiv').show();
			$('#msgHolder').text('Location order updated successfully');
		}
	});

}
function changeScreen(screenID){
	if(screenID == 'tableViewScreen'){
		$('#tableViewScreen').show('slow');		
		$('#locationViewScreen').hide('slow');		
	}
	if(screenID == 'locationViewScreen'){
		$('#locationViewScreen').show('slow');		
		$('#tableViewScreen').hide('slow');		
	}
}
function getSubLocation(sourceId, destId){
	if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	var selectId = document.getElementById(sourceId);
	var location_id = selectId.options[selectId.options.selectedIndex].value;
	if (location_id==0)
	{
		document.getElementById(destId).innerHTML = "<option value=0>Select</option>";
		return;
	}
	params = "location_id="+location_id +"&uniqueId="+Math.random()+"&getSubLocations=1";
	xmlhttp.open("GET", "ajaxFunctions.php?" + params, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			if(xmlhttp.responseText != ''){
				var resString = xmlhttp.responseText;
				document.getElementById(destId).innerHTML = resString;
			}
		}
	}
	xmlhttp.send(params);
}
function saveImages(fileDownloaded){
	window.location="download_image.php?file="+fileDownloaded;
}
function saveTaskOrder(taskULid){
	showProgress();
	var jsonObj = []; //declare object
	var i=0;
	$("#"+taskULid).find('li').each(function() {
		var currLiID =  this.id;
		var idArr = currLiID.split('_');
		var currOrID = parseInt(idArr[1]);
		jsonObj.push({id : currOrID, idRel : $('li#'+currLiID).attr('rel')});
		i++;
	});
	$.post("save_qa_task_order.php", {idArr:jsonObj, name:Math.random()}).done(function(data) {
		hideProgress();
		if(data){
			jAlert('Task order updated successfully');
		}
	});
}
</script>
<style>
.list{ border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto; }
.box1 { background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
.link1 { background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
a.link1:hover { background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
.txt13 { border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }

table.collapse { border-collapse: collapse; border: 1pt solid black;}
table.collapse td { border: 1pt solid black; padding: 2px; font-family:Arial, Helvetica, sans-serif; color:#000000;}
.list{ border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto; }
.box1 { background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
.link1 { background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
a.link1:hover { background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
.txt13 { border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }
.error-edit-profile-red{ background: url("images/bg-error-edit-profile-red.png") no-repeat scroll 0 0 transparent; color: #000; font-size: 11px; margin: 1px 0 2px 3px; padding: 10px 3px 8px 4px; width: 240px; text-shadow:none; }
.roundCorner{ border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; }
ul.telefilms{list-style:none; cursor:pointer; font-size:15px;}
ul.telefilms li ul{list-style:none; line-height:30px;}
ul.telefilms li{list-style:none; line-height:30px;}
.jtree-arrow { padding-right: 5px; font-size: 15px; }
li.taskList{padding-left:15px;}
table.simpleTable td { color:#000000;}

ul#sortable1 li { line-height:30px; padding-left:3px; margin:5px 0px 0px 0px;}
ul#sortable2 li { line-height:30px; padding-left:3px; margin:5px 0px 0px 0px;}
ul#sortable1 li:hover{ cursor:move;}
ul#sortable2 li:hover{ cursor:move;}
ul#sortable1 li input { position:absolute; right:18px; margin-top:9px;}
ul#sortable2 li input { position:absolute; right:18px; margin-top:9px;}
/*Remove after work*/
div#innerModalPopupDiv{text-align:center;}
.roundCorner table tr td:nth-child(1){ text-align:right; }
.roundCorner table tr td:nth-child(2){ text-align:center; }
/*.roundCorner table tr td:nth-child(3){ text-align:left; }*/
</style>