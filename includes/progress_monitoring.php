<?php
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
	$update = 'UPDATE progress_monitoring SET is_deleted = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = "'.$builder_id .'" WHERE progress_id = "'.base64_decode($_REQUEST['id']).'"';
	mysql_query($update);
	$_SESSION['progress_task_del'] = 'Progress monitoring task deleted successfully.';
	header('loaction:?sect=progress_monitoring');
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
					$data[$index] = trim(stripslashes(normalise($data[$index])));
				}
				$fieldarray[] = $data;
			}
#print_r($fieldarray);die;
			//end foreach
			fclose($file);
			$totalCol = count($fieldarray);
//Find Special Character in CSV dated : 04/10/2012
			$err_msg = '';
			$legalCharArray = array('0', '10', '13', '32', '34', '38', '39', '40', '41', '44', '45', '46', '47', '63', '60', '62', '58', '124', '125', '123', '61', '43', '95', '42', '94', '37', '36', '35', '33', '126', '96', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '59', '64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80','81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93', '97', '98', '99', '100', '101', '102', '103', '104', '105', '106', '107', '108', '109', '110', '111', '112', '113', '114', '115', '116', '117', '118', '119', '120', '121', '122');

			for($g=1; $g<$totalCol; $g++){
				$subCount = count($fieldarray[$g]);
				for($m=0;$m<$subCount; $m++){
					$string = $fieldarray[$g][$m];
					$strArray = str_split($string);
					$subSubCount  = count($strArray);
					for($b=0;$b<$subSubCount;$b++){
						$asciiVal = ord($strArray[$b]);
						if(!in_array($asciiVal, $legalCharArray)){
							$lineNoArray[] = $g+1;
						}
					}
				}
			}
			
			if(!empty($lineNoArray)){
				$err_msg = "Line no's ".join(', ', array_unique($lineNoArray))." contains some UNICODE characters. Please correct the CSV file and try again.";
			}
//Finding Special Charcter Stop Here
			if($err_msg != ''){ }else{
//Exist Issue to Data
				$issueData = $obj->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');
				$issueToData = array();
				if(!empty($issueData)){
					foreach($issueData as $isData){
						$issueToData[] = $isData['issue_to_name'];
					}
				}
//Location Array
				$locExistData = $obj->selQRYMultiple('location_id, location_title', 'project_monitoring_locations', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');
				$locKeyData = array();
				if(!empty($locExistData)){
					foreach($locExistData as $locData){
						$locKeyData[$locData['location_id']] = $locData['location_title'];
					}
				}
//Exist Task Data
				$taskExistData = $obj->selQRYMultiple('progress_id, location_id, sub_location_id, task', 'progress_monitoring', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');
				$taskData = array();
				$taskKeyData = array();
				if(!empty($taskExistData)){
					foreach($taskExistData as $tData){
						$taskData[] = array($locKeyData[$tData['location_id']], $locKeyData[$tData['sub_location_id']], $tData['task']);
						$taskKeyData[$tData['progress_id']] = $tData['task'];
					}
				}
//Exist Issue to Data
				$totalCol = $totalCol;
				$num = count($fieldarray); //count no of records
				$farr = array(); // set array for parent id
				$rowdata = array(); // set array for parent id
				$task = array();
				$location = array();
				$sublocation = array();
				$start_date = array();
				$end_date = array();
				$colIsuues = array();
				$holdPoint = array();//Add on dated 28/02/2013
				$collist = $fieldarray[0];
				for ($i=1; $i<count($collist); $i++){
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
					@$start_date[] = trim($value[3+$i-1]);
					@$end_date[] = trim($value[4+$i-1]);
					@$colIsuues[]=trim($value[5+$i-1]);
					@$holdPoint[]=trim($value[6+$i-1]);
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
#					$flagVar = true;
					$locArray = array();
/*					if($location[$h] != ''){	$LOCID = $location[$h];	}
					if($sublocation[$pidCount-1][$h] != ''){	$SUBLOCID = $sublocation[$pidCount-1][$h];	}
					$testArray = array($LOCID, $SUBLOCID, $task[$h]);
					foreach($taskData as $tData){
						$result = array_diff($tData, $testArray);
						if(sizeof($result) == 0){
							$flagVar = false;
							$count++;
						}
					}*/
#					if($flagVar){
					if(!empty($location[$h])){//Root Location Tree
						$rootLocation = '';
						$rootLocation = $obj->ProMonrecursiveInsertLocation(array($location[$h]), 0, $_SESSION['idp'], $builder_id, '');
						$rowLocationTree = '';
						for($g=0; $g<$num_sublocations; $g++){
							  if(!empty($sublocation[$g][$h]))
								   $locArray[] = $sublocation[$g][$h];
							  else//in case of there is empty sublocation, give same name as Parent Location
								   $locArray[] = $locArray[sizeof($locArray) - 1];
						}
						$rowLocationTree = $obj->ProMonrecursiveInsertLocation($locArray, $rootLocation, $_SESSION['idp'], $builder_id, '');
						$locArrayExp = explode(' > ', $rowLocationTree);
						$rowLocationID = $rootLocation;//LocationID to be insert while 
						$rowSubLocationID = end($locArrayExp);//LocationID to be insert
						$rowLocationTreeInsert = $rootLocation.' > '.$rowLocationTree;
						$rowLocationTreeNameInsert = $obj->locId2LocName($rowLocationTreeInsert);
//Start and End Date Store Here
						if($obj->validateMySqlDate($end_date[$h], 'HALF')){
							@$endDate = $end_date[$h];
						}else{
							$end_date[$h] = str_replace(array('-', '.'), array('/', '/'), $end_date[$h]);
							$endd = explode('/', $end_date[$h]);
							if(strlen($endd[2])==2){
								$endd[2]='20'.$endd[2];
							}
							@$endDate = $endd[2].'-'.$endd[1].'-'.$endd[0];
						}
						
						if($obj->validateMySqlDate($start_date[$h], 'HALF')){
							@$startDate = $start_date[$h];
						}else{
							$start_date[$h] = str_replace(array('-', '.'), array('/', '/'), $start_date[$h]);
							$startd = explode('/', $start_date[$h]);	
							if(strlen($startd[2])==2){
								$startd[2]='20'.$startd[2];
							}
							@$startDate = $startd[2].'-'.$startd[1].'-'.$startd[0];
						}
						if($holdPoint[$h] == ''){
							$holdPointCurr = 'No';
						}else{
							$holdPointCurr = $holdPoint[$h];
						}
//Insert Task Here
//Check Duplicate task here
						if($task[$h] != ''){
						$seleTask = "SELECT progress_id FROM progress_monitoring WHERE location_id = '".$rowLocationID."' AND sub_location_id = '".$rowSubLocationID."'AND task='".$task[$h]."' AND is_deleted = 0";
						$resultTask = mysql_query($seleTask);
						$rowTask = mysql_num_rows($resultTask);
						if($rowTask > 0){
							$count++;
//Update condition here
							if($updateFile == 1){
								while($rowP = mysql_fetch_assoc($resultTask)){
									$updateQry = "UPDATE progress_monitoring SET start_date = '".$startDate."', end_date = '".$endDate."', last_modified_date = NOW(), original_modified_date = NOW(), holding_point = '".$holdPointCurr."', last_modified_by = ".$_SESSION['ww_builder_id']." WHERE progress_id = '".$rowP['progress_id']."'";
									$rs = mysql_query($updateQry);
									if(mysql_affected_rows() > 0){
										$updateCount++;
									}
									$update_issue = "UPDATE issued_to_for_progress_monitoring SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE progress_id = '".$existProgressID."' AND project_id = '".$_SESSION['idp']."' AND is_deleted = 0";			
									$issue_result = mysql_query($update_issue);
									if($colIsuues[$h] != ''){
										$issueToArray = explode(';', $colIsuues[$h]);
										for($g=0; $g<count($issueToArray); $g++){
											if(!in_array(trim($issueToArray[$g]), $issueToData) && $colIsuues[$h] !=''){
												$inserQRY = "INSERT INTO inspection_issue_to SET 
																issue_to_name = '".addslashes(trim($issueToArray[$g]))."',
																project_id = '".$_SESSION['idp']."',
																created_by = '".$builder_id."',
																created_date = NOW(),
																last_modified_by = '".$builder_id."',
																last_modified_date = NOW()";
												mysql_query($inserQRY);
												$issueToData[] = addslashes(trim($issueToArray[$g]));
											}
											$inserQRY = "INSERT INTO issued_to_for_progress_monitoring SET 
														progress_id = '".$existProgressID."',
														issued_to_name = '".addslashes(trim($issueToArray[$g]))."',
														project_id = '".$_SESSION['idp']."',
														created_by = '".$builder_id."',
														created_date = NOW(),
														last_modified_by = '".$builder_id."',
														last_modified_date = NOW()";
											mysql_query($inserQRY);
										}
									}
								}
							}
						}else{
							$lines++;
							$inserQRY = "INSERT INTO progress_monitoring SET 
											project_id = '".$_SESSION['idp']."',
											location_id = '".$rowLocationID."',
											sub_location_id = '".$rowSubLocationID."',
											task = '".addslashes($task[$h])."',
											start_date = '".addslashes($startDate)."',
											end_date = '".addslashes($endDate)."',
											status = '',
											percentage = '0%',
											location_tree = '".addslashes(trim($rowLocationTreeInsert))."',
											location_tree_name = '".addslashes(trim($rowLocationTreeNameInsert))."',
											created_by = '".$builder_id."',
											created_date = NOW(),
											last_modified_by = '".$builder_id."',
											last_modified_date = NOW(),
											original_modified_date = NOW(),
											holding_point = '".$holdPointCurr."'";
							mysql_query($inserQRY);
							$progress_id = mysql_insert_id();
							if($colIsuues[$h] != ''){
								$issueToArray = explode(';', $colIsuues[$h]);
								for($g=0; $g<count($issueToArray); $g++){
									if(!in_array(trim($issueToArray[$g]), $issueToData) && $colIsuues[$h] !=''){
										$inserQRY = "INSERT INTO inspection_issue_to SET 
														issue_to_name = '".addslashes(trim($issueToArray[$g]))."',
														project_id = '".$_SESSION['idp']."',
														created_by = '".$builder_id."',
														created_date = NOW(),
														last_modified_by = '".$builder_id."',
														last_modified_date = NOW()";
										mysql_query($inserQRY);
										$issueToData[] = addslashes(trim($issueToArray[$g]));
									}
									$inserQRY = "INSERT INTO issued_to_for_progress_monitoring SET 
												progress_id = '".$progress_id."',
												issued_to_name = '".addslashes(trim($issueToArray[$g]))."',
												project_id = '".$_SESSION['idp']."',
												created_by = '".$builder_id."',
												created_date = NOW(),
												last_modified_by = '".$builder_id."',
												last_modified_date = NOW()";
									mysql_query($inserQRY);
								}
							}
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
							$rowLocationTree = $obj->ProMonrecursiveInsertLocation($subLocArray, $parentLocationId, $_SESSION['idp'], $builder_id, join (' > ', $removeLocArray));
						}
						$locArrayExp = explode(' > ', $rowLocationTree);
						$rowSubLocationID = end($locArrayExp);//LocationID to be insert
						$rowLocationTreeInsert = $rowLocationID.' > '.$rowLocationTree;
						$rowLocationTreeNameInsert = $obj->locId2LocName($rowLocationTreeInsert);
//Start and End Date Store Here
						if($obj->validateMySqlDate($end_date[$h], 'HALF')){
							@$endDate = $end_date[$h];
						}else{
							$end_date[$h] = str_replace(array('-', '.'), array('/', '/'), $end_date[$h]);
							$endd = explode('/', $end_date[$h]);
							if(strlen($endd[2])==2){
								$endd[2]='20'.$endd[2];
							}
							@$endDate = $endd[2].'-'.$endd[1].'-'.$endd[0];
						}
						
						if($obj->validateMySqlDate($start_date[$h], 'HALF')){
							@$startDate = $start_date[$h];
						}else{
							$start_date[$h] = str_replace(array('-', '.'), array('/', '/'), $start_date[$h]);
							$startd = explode('/', $start_date[$h]);	
							if(strlen($startd[2])==2){
								$startd[2]='20'.$startd[2];
							}
							@$startDate = $startd[2].'-'.$startd[1].'-'.$startd[0];
						}
						
						if($holdPoint[$h] == ''){
							$holdPointCurr = 'No';
						}else{
							$holdPointCurr = $holdPoint[$h];
						}
//Insert Task Here
//Check Duplicate task here
						if($task[$h] != ''){
						$seleTask = "SELECT progress_id FROM progress_monitoring WHERE location_id = '".$rowLocationID."' AND sub_location_id = '".$rowSubLocationID."'AND task='".$task[$h]."' AND is_deleted = 0";
						$resultTask = mysql_query($seleTask);
						$rowTask = mysql_num_rows($resultTask);
						if($rowTask > 0){
							$count++;
//Update condition here
							if($updateFile == 1){
								while($rowP = mysql_fetch_assoc($resultTask)){
									$updateQry = "UPDATE progress_monitoring SET start_date = '".$startDate."', end_date = '".$endDate."', last_modified_date = NOW(), original_modified_date = NOW(), holding_point = '".$holdPointCurr."', last_modified_by = ".$_SESSION['ww_builder_id']." WHERE progress_id = '".$rowP['progress_id']."'";
									$rs = mysql_query($updateQry);
									if(mysql_affected_rows() > 0){
										$updateCount++;
									}
									$update_issue = "UPDATE issued_to_for_progress_monitoring SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE progress_id = '".$existProgressID."' AND project_id = '".$_SESSION['idp']."' AND is_deleted = 0";			
									$issue_result = mysql_query($update_issue);
									if($colIsuues[$h] != ''){
										$issueToArray = explode(';', $colIsuues[$h]);
										for($g=0; $g<count($issueToArray); $g++){
											if(!in_array(trim($issueToArray[$g]), $issueToData) && $colIsuues[$h] !=''){
												$inserQRY = "INSERT INTO inspection_issue_to SET 
																issue_to_name = '".addslashes(trim($issueToArray[$g]))."',
																project_id = '".$_SESSION['idp']."',
																created_by = '".$builder_id."',
																created_date = NOW(),
																last_modified_by = '".$builder_id."',
																last_modified_date = NOW()";
												mysql_query($inserQRY);
												$issueToData[] = addslashes(trim($issueToArray[$g]));
											}
											$inserQRY = "INSERT INTO issued_to_for_progress_monitoring SET 
														progress_id = '".$existProgressID."',
														issued_to_name = '".addslashes(trim($issueToArray[$g]))."',
														project_id = '".$_SESSION['idp']."',
														created_by = '".$builder_id."',
														created_date = NOW(),
														last_modified_by = '".$builder_id."',
														last_modified_date = NOW()";
											mysql_query($inserQRY);
										}
									}
								}
							}
						}else{
							$lines++;
							$inserQRY = "INSERT INTO progress_monitoring SET 
											project_id = '".$_SESSION['idp']."',
											location_id = '".$rowLocationID."',
											sub_location_id = '".$rowSubLocationID."',
											task = '".addslashes($task[$h])."',
											start_date = '".addslashes($startDate)."',
											end_date = '".addslashes($endDate)."',
											status = '',
											percentage = '0%',
											location_tree = '".addslashes(trim($rowLocationTreeInsert))."',
											location_tree_name = '".addslashes(trim($rowLocationTreeNameInsert))."',
											created_by = '".$builder_id."',
											created_date = NOW(),
											last_modified_by = '".$builder_id."',
											last_modified_date = NOW(),
											original_modified_date = NOW(),
											holding_point = '".$holdPointCurr."'";
							mysql_query($inserQRY);
							$progress_id = mysql_insert_id();
							if($colIsuues[$h] != ''){
								$issueToArray = explode(';', $colIsuues[$h]);
								for($g=0; $g<count($issueToArray); $g++){
									if(!in_array(trim($issueToArray[$g]), $issueToData) && $colIsuues[$h] !=''){
										$inserQRY = "INSERT INTO inspection_issue_to SET 
														issue_to_name = '".addslashes(trim($issueToArray[$g]))."',
														project_id = '".$_SESSION['idp']."',
														created_by = '".$builder_id."',
														created_date = NOW(),
														last_modified_by = '".$builder_id."',
														last_modified_date = NOW()";
										mysql_query($inserQRY);
										$issueToData[] = addslashes(trim($issueToArray[$g]));
									}
									$inserQRY = "INSERT INTO issued_to_for_progress_monitoring SET 
												progress_id = '".$progress_id."',
												issued_to_name = '".addslashes(trim($issueToArray[$g]))."',
												project_id = '".$_SESSION['idp']."',
												created_by = '".$builder_id."',
												created_date = NOW(),
												last_modified_by = '".$builder_id."',
												last_modified_date = NOW()";
									mysql_query($inserQRY);
								}
							}
						}
						}
					}
					#}
				}
				$success = 'File uploaded successfully.';
//updating project record, to have no. of sub-locations get inserted
				$query = "UPDATE user_projects SET pr_num_sublocations = ".$num_sublocations.", last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE project_id = ".$_SESSION["idp"];
				mysql_query($query);
				
				$locData = $obj->selQRYMultiple('location_id, location_title, location_parent_id', 'project_monitoring_locations', 'is_deleted = 0 AND project_id = '.$_SESSION["idp"]);

				foreach($locData as $lData){
					$locNameTree = $obj->promon_sublocationParent($lData['location_id'], ' > ');
					$query = 'UPDATE project_monitoring_locations SET location_tree_name = "'.$locNameTree.'", last_modified_date = NOW(), last_modified_by = '.$_SESSION['ww_builder_id'].' WHERE location_id = '.$lData['location_id'];
					mysql_query($query);
				}
//Message Part
				@mysql_close($con); //close db connection
				if(!empty($updateCount)){
					if($updateCount > 0){
						$success="$updateCount Records Updated Successfully !";				
					}
				}else{
					if($count > 0){
						$success="$count Duplicate Records";
					}
				}
			}
		}else{
			$err_msg = 'Please select .csv file.';
		}	
	}else{
		$err_msg = 'Please select file.';
	}
}
$id = base64_encode($_SESSION['idp']);
$hb = base64_encode($_SESSION['hb']); ?>
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" /> 

<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript" src="js/location_tree_jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.tree_promon.js"></script>
<script type="text/javascript" src="js/jquery.contextmenu.r2.js"></script>
<script type="text/javascript">
var deadLock = false;//To stop multiple ajax call

var align = 'center';
var top = 100;
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
	$('span.demo1').contextMenu('myMenu2', {
		bindings: {
			'add': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add_promon.php?location_id='+t.id, loadingImage);
			},
			'edit': function(t) {
				var parentId = $('#'+t.id).parent().parent().parent().get(0);
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_promon.php?location_id='+t.id+'&parent_id='+parentId.id, loadingImage);
			},
			'delete': function(t) {
				var r = jConfirm('Do you want to delete location ?, task added on this location also deleted !', null, function(r){
					if (r==true){
						showProgress();
						$.post("location_tree_delete_promon.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
							hideProgress();
							if(data){
								$('#li_'+t.id).hide('slow');
								jAlert('Location Deleted Successfully !');
							}else{
								jAlert(data);
							}
						});
					}
				});
			},
			'addTask': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_promon.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage, addDatePicker);
			},
		}
	});
	$('span.demo2').contextMenu('myMenu1', {
		bindings: {
			'edit': function(t) {
								var parentId = $('#'+t.id).parent().parent().parent().get(0);
								modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_promon.php?location_id='+t.id+'&parent_id='+parentId.id, loadingImage);
							},
			'delete': function(t) {
				var r = jConfirm('Do you want to delete location ?, task added on this location also deleted !', null, function(r){
					if (r==true){
						showProgress();
						$.post("location_tree_delete_promon.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
							hideProgress();
							if(data){
								$('#li_'+t.id).hide('slow');
								jAlert('Location Deleted Successfully !');
							}else{
								jAlert(data);
							}
						});
					}
				});
			},
			'addTask': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_promon.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage, addDatePicker);
			},
		}
	});
	$('span.demo3').contextMenu('myMenu3', {
		bindings: {
			'viewTask': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'view_task_promon.php?progress_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
			},
			'editTask': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_task_promon.php?progress_id='+t.id+'&uniqueId='+Math.random(), loadingImage, addDatePicker);
			},
			'deleteTask': function(t) {
				var r = jConfirm('Do you want to delete task ?', null, function(r){
					if (r==true){
						showProgress();
						$.post("delete_task_promon.php", {progress_id:t.id, antiqueID:Math.random()}).done(function(data) {
							hideProgress();
							if(data == 1){
								$('#li_'+t.id).hide('slow');
								jAlert('Task Deleted Successfully !');
							}else{
								jAlert(data);
							}
						});
					}
				});
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

	$.post("location_tree_add_promon.php", params).done(function(data) {
		hideProgress();
		if(data != 'Duplicate location'){
			if(checkProject == 'No'){	jQuery("#li_"+locationId).append(data);	}
			if(checkProject == 'Yes'){ 	jQuery("#projectId_"+locationId).append(data);	}
			closePopup(fadeOutTime);
			$('span.demo1').contextMenu('myMenu2', {
				bindings: {
					'add': function(t) {
						modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add_promon.php?location_id='+t.id, loadingImage);
					},
					'edit': function(t) {
								var parentId = $('#'+t.id).parent().parent().parent().get(0);
								modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_promon.php?location_id='+t.id+'&parent_id='+parentId.id, loadingImage);
							},
					'delete': function(t) {
						var r = jConfirm('Do you want to delete location ?, task added on this location also deleted !', null, function(r){
							if (r==true){
								showProgress();
								$.post("location_tree_delete_promon.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
									hideProgress();
									if(data){
										$('#li_'+t.id).hide('slow');
										jAlert('Location Deleted Successfully !');
									}else{
										jAlert(data);
									}
								});
							}
						});
					},
					'addTask': function(t) {
						modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_promon.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage, addDatePicker);
					},
				}
			});
			$('span.demo2').contextMenu('myMenu1', {
				bindings: {
					'edit': function(t) {
								var parentId = $('#'+t.id).parent().parent().parent().get(0);
								modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_promon.php?location_id='+t.id+'&parent_id='+parentId.id, loadingImage);
							},
					'delete': function(t) {
						var r = jConfirm('Do you want to delete location ?, task added on this location also deleted !', null, function(r){
							if (r==true){
								showProgress();
								$.post("location_tree_delete_promon.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
									hideProgress();
									if(data){
										$('#li_'+t.id).hide('slow');
										jAlert('Location Deleted Successfully !');
									}else{
										jAlert(data);
									}
								});
							}
						});
					},
					'addTask': function(t) {
						modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_promon.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage, addDatePicker);
					},
				}
			});
			$('span.demo3').contextMenu('myMenu3', {
				bindings: {
					'viewTask': function(t) {
						modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'view_task_promon.php?progress_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
					},
					'editTask': function(t) {
						modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_task_promon.php?progress_id='+t.id+'&uniqueId='+Math.random(), loadingImage, addDatePicker);
					},
					'deleteTask': function(t) {
						var r = jConfirm('Do you want to delete task ?', null, function(r){
							if (r==true){
								showProgress();
								$.post("delete_task_promon.php", {progress_id:t.id, antiqueID:Math.random()}).done(function(data) {
									hideProgress();
									if(data == 1){
										$('#li_'+t.id).hide('slow');
										jAlert('Task Deleted Successfully !');
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
	$.post("location_tree_edit_promon.php", {location:location, locationId:locationId, parent_id:locationParentID, uniqueId:Math.random()}).done(function(data) {
		hideProgress();
		if(data != 'Duplicate location'){
			document.getElementById(locationId).innerHTML=data;
			closePopup(fadeOutTime);
		}else{
			jAlert('Duplicate location name, location not updated');
		}
	});
}

function addTaskSubmit(){
	if($.trim($('#task').val()) == ''){$('#taskError').show('slow'); return false;}else{$('#taskError').hide('slow');}
	if($.trim($('#startDate').val()) == ''){$('#startDateError').show('slow'); return false;}else{$('#startDateError').hide('slow');}
	if($.trim($('#endDate').val()) == ''){$('#endDateError').show('slow'); return false;}else{$('#endDateError').hide('slow');}
	var datecheck = checkDates($('#startDate').val(), $('#endDate').val());
	if(datecheck){
		showProgress();
		$.post('add_task_promon.php?antiqueID='+Math.random(), $('#addTaskForm').serialize()).done(function(data) {
			hideProgress();
			if(data != 'Duplicate task'){
				var jsonResult = $.parseJSON(data);
				$.each(jsonResult, function(i, item) {
					$('#li_'+i).append(item);
				});
				$('span.demo1').contextMenu('myMenu2', {
					bindings: {
						'add': function(t) {
							modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add_promon.php?location_id='+t.id, loadingImage);
						},
						'edit': function(t) {
							modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_promon.php?location_id='+t.id, loadingImage);
						},
						'delete': function(t) {
							var r = jConfirm('Do you want to delete location ?, task added on this location also deleted !', null, function(r){
								if (r==true){
									showProgress();
									$.post("location_tree_delete_promon.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
										hideProgress();
										if(data){
											$('#li_'+t.id).hide('slow');
											jAlert('Location Deleted Successfully !');
										}else{
											jAlert(data);
										}
									});
								}
							});
						},
						'addTask': function(t) {
							modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_promon.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage, addDatePicker);
						},
					}
				});
				$('span.demo2').contextMenu('myMenu1', {
					bindings: {
						'edit': function(t) {
							modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit_promon.php?location_id='+t.id, loadingImage);
						},
						'delete': function(t) {
							var r = jConfirm('Do you want to delete location ?, task added on this location also deleted !', null, function(r){
								if (r==true){
									showProgress();
									$.post("location_tree_delete_promon.php", {location_id:t.id, confirm:'Y', uniqueId:Math.random()}).done(function(data) {
										hideProgress();
										if(data){
											$('#li_'+t.id).hide('slow');
											jAlert('Location Deleted Successfully !');
										}else{
											jAlert(data);
										}
									});
								}
							});
						},
						'addTask': function(t) {
							modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_promon.php?sub_location_id='+t.id+'&uniqueId='+Math.random(), loadingImage, addDatePicker);
						},
					}
				});
				$('span.demo3').contextMenu('myMenu3', {
					bindings: {
						'viewTask': function(t) {
							modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'view_task_promon.php?progress_id='+t.id+'&uniqueId='+Math.random(), loadingImage);
						},
						'editTask': function(t) {
							modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_task_promon.php?progress_id='+t.id+'&uniqueId='+Math.random(), loadingImage, addDatePicker);
						},
						'deleteTask': function(t) {
							var r = jConfirm('Do you want to delete task ?', null, function(r){
								if (r==true){
									showProgress();
									$.post("delete_task_promon.php", {progress_id:t.id, antiqueID:Math.random()}).done(function(data) {
										hideProgress();
										if(data == 1){
											$('#li_'+t.id).hide('slow');
											jAlert('Task Deleted Successfully !');
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
}

function editTaskSubmit(){
	if($.trim($('#task').val()) == ''){$('#taskError').show('slow'); return false;}else{$('#taskError').hide('slow');}
	if($.trim($('#startDate').val()) == ''){$('#startDateError').show('slow'); return false;}else{$('#startDateError').hide('slow');}
	if($.trim($('#endDate').val()) == ''){$('#endDateError').show('slow'); return false;}else{$('#endDateError').hide('slow');}
	var datecheck = pmCheckDates($('#startDate').val(), $('#endDate').val());
	if(datecheck){
		showProgress();
		$.post('edit_task_promon.php?antiqueID='+Math.random(), $('#editTaskForm').serialize()).done(function(data) {
			hideProgress();
			if(data != 'Duplicate task'){
				var jsonResult = JSON.parse(data);	
				$('#'+jsonResult.progress_id).html('<img src="images/task_simbol.png">&nbsp;'+jsonResult.progress_task);
				jAlert('Task Edit Successfully');
				closePopup(300);
			}else{
				jAlert('Duplicate task, task not updated');
			}
		});
	}
}

function pmCheckDates(obj, obj1){
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

function addDatePicker(){
	new JsDatePick({ useMode:2, target:"startDate", dateFormat:"%d/%m/%Y" });
	new JsDatePick({ useMode:2, target:"endDate", dateFormat:"%d/%m/%Y" });
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
			<!-- <a style="float:left;margin-top:-25px; width:87px;margin-left:590px;" href="?sect=add_project_detail&id=<?php //echo $id;?>&hb=<?php //echo $hb;?>"><img src="images/back_btn2.png" style="border:none;" /></a> -->
			<a style="float:left;margin-top:-25px;margin-left:590px;" href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>" class="green_small">Back</a>
		</div><br clear="all" />
		<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top:-15px;margin-top:0px\9;">
		<?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { ?>
			<div class="success_r" style="height:35px;width:235px;"><p><?php echo $_SESSION['add_project'] ; ?></p></div>
		<?php unset($_SESSION['add_project']);} ?><?php if((isset($success)) && (!empty($success))) { ?>
			<div class="success_r" style="height:35px;width:235px;"><p><?php echo $success; ?></p></div>
		<?php }
			if((isset($err_msg)) && (!empty($err_msg))) { ?>
			<div class="failure_r" style="height:35px;width:500px;"><p><?php echo $err_msg; ?></p></div>
		<?php } ?>
		</div>
		<div class="content_container" style="float:left;width:690px;border:1px solid; margin-bottom:10px;text-align:center;margin-left:10px;margin-right:10px;height:130px;">
			<div style="width:722px; height:50px; float:left; margin-top:5px;">
				<form action="?sect=progress_monitoring" method="post" name="csvLocation" id="csvLocation" enctype="multipart/form-data" onSubmit="return validateSubmit()">
					<table width="690px" border="0" cellspacing="0" cellpadding="3">
						<tr>
							<td colspan="3" align="left">
								<a href="/csv/Progress_Monitoring_Template.csv" style="text-decoration:none;color:#FFF;"><strong style="font-size:16px;">Click <u>here</u> to download CSV template</strong></a>
							</td>
							<td>
								<!-- <input type="button"  class="submit_btn" onclick=location.href="progress_monitoring_export.php"  style="background:none;background-image:url(images/export_csv_btn.png); width:87px; height:30px;border:none;margin-left:15px;" /> -->
								<input type="button" class="green_small" onclick=location.href="progress_monitoring_export.php"  style="cursor:pointer;margin-left:15px;" value="Export CSV" />
							</td>
						</tr>
						<tr>
							<td width="185px;" align="left">&nbsp;</td>
							<td width="130px;">Upload&nbsp;CSV&nbsp;File&nbsp;:</td>
							<td width="240px;" align="left">
								<input type="file" name="csvFile" id="csvFile" value="" />
							</td>
							<td width="120px;" height="40px">
								<!-- <input  class="submit_btn" type="button" style="background: url('images/import_csv_btn.png') repeat scroll 0 0 transparent;border: medium none;height: 30px;margin-left:12px;width: 87px;color:transparent;font-size:0px;"  name="location_csv" id="location_csv" value="Import CSV" onclick="validateSubmit();" /> -->
								<input type="submit" class="green_small" style="cursor:pointer;margin-left:15px;" name="location_csv" id="location_csv" value="Import CSV" onclick="validateSubmit();"/>

							</td>
						</tr>
						<tr>
							<td colspan="3" align="left">
								<input type="hidden" id="updateFile" name="updateFile" value="1" />
							</td>
							<td width="120px;" height="40px">
								<!-- <input  class="submit_btn" type="button" style="background: url('images/bulk_update_btn.png') repeat scroll 0 0 transparent;border: medium none;height: 30px;margin-left:12px;width: 87px;color:transparent;font-size:0px;"  name="location_csv" id="location_csv" value="Import CSV" onclick="validateUpdateSubmit();" /> -->
								<input type="submit" class="green_small" style="cursor:pointer;margin-left:15px;" name="location_csv" id="location_csv" value="Bulk Update" onclick="validateUpdateSubmit();"/>
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
		<br clear="all" />
		<div class="big_container" id="tableViewScreen" style="width:722px;margin-left:9px;display:none;"><?php include'progress_csv_table.php';?></div>
		<div class="big_container" id="locationViewScreen" style="width:722px;margin-left:9px;" >
			<div id="locationsContainer">
				<span id="projectId_<?php echo $_SESSION['idp']?>">
				<span class="jtree-button" id="projectId_<?php echo $_SESSION['idp']?>" style="background-image: url('images/project.png');background-position: 0 15px;background-repeat: no-repeat;display: block;height: 30px;padding-left: 40px;padding-top: 9px;width: 90%;font-size:26px;cursor: pointer;"><?php echo $projectName?></span>
				<?php $q = "select location_id, location_title from project_monitoring_locations where project_id = ".$_SESSION['idp']." and location_parent_id = '0' and is_deleted = '0' order by location_title";
					$re = mysql_query($q);
					if(mysql_num_rows($re) > 0){
						echo '<ul class="telefilms">';
						while($locations = mysql_fetch_array($re)){
							echo '<li id="li_'.$locations['location_id'].'">';
							$data = $obj->recurtionProMon($locations['location_id'], $_SESSION['idp']);
							$taskData = $obj->selQRYMultiple('progress_id, task, start_date, end_date', 'progress_monitoring', 'sub_location_id = '.$locations['location_id'].' AND is_deleted= 0 AND project_id = '.$_SESSION['idp']);
							if($data!=''){
								echo '<span style="cursor:pointer;" class="jtree-arrow close"><img src="images/plus-icon.png"></span>';
							}
							echo '<span class="jtree-button demo1" id="'.$locations['location_id'].'">'.stripslashes($locations['location_title']).'</span></li>';
							$task = '';
							if(!empty($taskData)){
								foreach($taskData as $tData){
									$task .= '<li id="li_'.$tData['progress_id'].'" class="taskList"><span class="jtree-button demo3" id="'.$tData['progress_id'].'"><img src="images/task_simbol.png">&nbsp;'.$tData['task'].'</span></li>';
								}
							}
							echo $task;
						}
						echo '</ul>';
					}?>
				</span>
			</div>
			<div class="contextMenu" id="myMenu2" style="width:110px;">
				<ul style="width:110px !important;">
					<li id="add"><img src="images/add.png" align="absmiddle" width="14"  height="14"/> Add</li>
					<li id="edit"><img src="images/edit_right.png"  align="absmiddle" width="16" height="16" /> Edit</li>
					<li id="delete"><img src="images/delete.png"  align="absmiddle" width="14" height="15" /> Delete</li>
					<li id="addTask"><img src="images/add_task_icon.png" align="absmiddle" width="14"  height="14"/> Add Task</li>
				</ul>
			</div>
			<div class="contextMenu" id="myMenu1" style="width:110px;">
				<ul style="width:110px !important;">
					<li id="edit"><img src="images/edit_right.png"  align="absmiddle" width="16" height="16" /> Edit</li>
					<li id="delete"><img src="images/delete.png"  align="absmiddle" width="14" height="15" /> Delete</li>
					<li id="addTask"><img src="images/add_task_icon.png" align="absmiddle" width="14"  height="14"/> Add Task</li>
				</ul>
			</div>
			<div class="contextMenu" id="myMenu3">
				<ul style="width:110px !important;">
					<li id="viewTask"><img src="images/edit_task_icon.png" align="absmiddle" width="14"  height="14"/> View Task</li>
					<li id="editTask"><img src="images/view_task_icon.png" align="absmiddle" width="14"  height="14"/> Edit Task</li>
					<li id="deleteTask"><img src="images/delete_task_icon.png" align="absmiddle" width="14"  height="14"/>&nbsp;Delete&nbsp;Task</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
function validateSubmit(){
	 document.getElementById("updateFile").value = 0;
	var r = jConfirm('Do you want to upload "Progress Monitoring CSV" ?', null, function(r){
		if (r === true){
			document.forms["csvLocation"].submit();	
		}else{
			return false;
		}
	});
}
function validateUpdateSubmit(){
	 document.getElementById("updateFile").value = 1;
	var r = jConfirm('Do you want to upload "Progress Monitoring CSV" for Update ?', null, function(r){
		if (r === true){
			document.forms["csvLocation"].submit();	
		}else{
			return false;
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
</style>
