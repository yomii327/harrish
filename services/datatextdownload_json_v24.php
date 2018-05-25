<?php
//Header Secttion for include and objects Start Here
/*error_reporting(E_ALL);
ini_set('display_errors', '1');
*/
set_time_limit(360000);
include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');
define("IMPORTFILEPATH", '../sync/Import');

//Header Secttion for include and objects End Here
if(isset($_REQUEST['data_text'])){
	$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId'])))); 
	$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
	$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
	$projectIDs = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['projectIDs']))));
	$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
	$deviceType = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['device']))));

//Store Incoming data to create requested URL
	$sync_url = $db->curPageURL($_POST);
	
//Check mysql date is correct or not.
	if(isset($lastModifiedDate) && !empty($lastModifiedDate)){
		if($db->validateMySqlDate($lastModifiedDate)){}else{
			$output = array(
				'status' => false,
				'message' => 'Modified Date is not Valid !',
				'data' => ''
			);
			echo '['.json_encode($output).']';
			die;
		}
	}
//Check authenticated user is correct or not.
	if($db->hashAuth($globalId)){}else{
		$output = array(
			'status' => false,
			'message' => 'User Authentication Fail',
			'data' => ''
		);
		echo '['.json_encode($output).']';
		die;
	}

//Get Current date to send as last_modified_date
	$date_lmd = $db->getCurrentDateTime();

//Remove Previous Files	
	define("EXPORTFILEPATH", '../sync/Export/'.$_REQUEST['userId']);
	if(!is_dir(EXPORTFILEPATH)){
		@mkdir(EXPORTFILEPATH, 0777);
	}
	$db->recursive_remove_directory(EXPORTFILEPATH.'/download');
	
//Create New Folder and Files
	@mkdir(EXPORTFILEPATH.'/download/', 0777);
	
	if(is_dir('../sync/Export/')){
//Get projectids, userRole, issueTo in case sub contractor
		$userRoleData = getUserRoleData($userId);
		$roleProjectId = $userRoleData[0];
		$projectId = $userRoleData[1];
		$issuedToNameProjectId = $userRoleData[2];
		$projectIDs = '';
		foreach($projectId as $pID){#$pID['project_id']
			if($projectIDs == ''){
				$projectIDs = $pID['project_id'];
			}else{
				$projectIDs .= ','.$pID['project_id'];
			}
		}
		
//Files crate Here
		if(empty($lastModifiedDate)){//Modified Date not comes form device
			include('first_sync_v24.php');
		}else{//Modified Date comes form device
			include('sub_sequent_sync_v24.php');
		}
		$zipSource = EXPORTFILEPATH.'/download/';
		$db->createFile('last_modified_date.txt', $date_lmd, EXPORTFILEPATH.'/download/', 'w');//Write File Here 

		$zipFileName = $db->updateExportTable_updated(EXPORTFILEPATH . '/download/' . $zipFileName, $_REQUEST['userId'], $deviceType, 'tableData', $sync_url);
		
		$db->compress($zipSource, 'text_'.$zipFileName, EXPORTFILEPATH);

//Code for Download the zip file
		$filename = 'text_'.$zipFileName.'.zip';
		header("Content-type: application/zip;\n");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize(EXPORTFILEPATH.'/'.$filename)."\n");
		header("Content-Disposition: attachment; filename=".$filename);
		ob_end_flush();
		@readfile(EXPORTFILEPATH.'/'.$filename);
//Code for Download the zip file
	}else{
		@mkdir('../sync/Export/',0777);
		die;
	}
}
//------------------------------------------- Comman Functions Start Here ---------------------------------------------
//Function to create json files
function createJsonFile($path, $tablename, $column_names, $dataarray){
	global $db;
	$count = 0;
	$data_string = 'INSERT INTO '.$tablename .' ('.$column_names.') '.$dataarray[0];
	$data = array();
	for ($i=1;$i<count ($dataarray);$i++){
		$count++;
		if ($count==500){
			$data[] = array("sqlData"=> $data_string);
			$data_string = 'INSERT INTO '.$tablename.' ('.$column_names.') '.$dataarray[$i];
			$i++;
			$count = 1;
		}
		if (isset ($dataarray[$i])){
			$data_string .= " UNION ALL " . $dataarray[$i];
		}
	}
	if ($count != 1 || count ($dataarray) < 500){
		$data[] = array("sqlData"=> $data_string);
	}
	$json = json_encode ($data);
	$db->createFile($tablename . '_add.json', $json, $path);//Write File Here 	
}
//Function for projectids, userRole, issueTo in case sub contractor
function getUserRoleData($userID){
	global $db;
	$projectId = array();
	$roleProjectId = array();
	$issuedToNameProjectId = array();//To have Issued name in case of Sub Contractor Role.
	$user_roles = $db->selQRYMultiple('user_role, project_id, issued_to', 'user_projects', 'user_id = "'.$userID.'" AND is_deleted = 0');
	foreach($user_roles as $user_role){
		if($user_role['user_role'] != 'All Defect' && $user_role['user_role'] != 'Sub Contractor'){
			$spCondition = ' AND inspection_raised_by = "'.$user_role['user_role'].'"';
			$issuedToNameProjectId[$user_role['project_id']] = '';
		}else{
			$spCondition = '';
			if ($user_role['user_role'] == 'Sub Contractor'){
				$issuedToNameProjectId[$user_role['project_id']] =  ' and isi.issued_to_name="' . $user_role["issued_to"] . '"';
			}
		}
		$roleProjectId[$user_role['project_id']] = $spCondition;
		$projectId[] = array('project_id' => $user_role['project_id']);				
	}
	$userRoleData = array($roleProjectId, $projectId, $issuedToNameProjectId);
	return $userRoleData;
}
//Create New added project Array and user_projects_add.txt here
function createUserProjectsFile($table, $colString, $userID, $global_id){
	global $lastModifiedDate;
	global $db;
	$queryArray = array();
	$new_project = array();
	$selStr = $colString;
	$colArray = explode(',', $colString);
	$colArray = array_map('trim', $colArray);
	$loopCount = $recCount = sizeof($colArray);

	$rs = mysql_query("SELECT ".$selStr." FROM ".$table." WHERE user_id = '".$userID."' AND created_date >= '".$lastModifiedDate."' AND is_deleted = 0");
	
	$valString = '';
	if(mysql_num_rows($rs) > 0){
		while($row = mysql_fetch_assoc($rs)){
			$new_project[$row[$colArray[0]]] = 1;
			$valStr = '';
			for($i=0; $i<$loopCount; $i++){
				if($valStr == ''){
					$valStr = "\"".$db->dataFilter($row[$colArray[$i]])."\"";
				}else{
					$valStr .= ", \"".$db->dataFilter($row[$colArray[$i]])."\"";
				}
			}
			$valString .= $row[$global_id]."##VALUES(".$valStr.");\r\n";
			$queryArray[] = $valString;
			$valString = '';
		}
	}
	if(count($queryArray) > 0){
		$db->createFile($table.'_add.txt', join('', $queryArray), EXPORTFILEPATH.'/download/');
	}
	return $new_project;
}
//refuse
//Function that retrun accepte inspectionIDS filtered by diffrent conditions on first sync
function first_sync_acceptedInpsectionIDS($userRoleData, $deviceType){
	global $db;//Global Object for database query and predefine function
	$roleProjectId = $userRoleData[0];
	$projectId = $userRoleData[1];
	$issuedToNameProjectId = $userRoleData[2];
	$projectIDs = '';
	$dateRange = array();//To store days according to projectwise
	$statsRange = array();//To store status according to projectwise
	$parentLocIDS = array();//To store Parent Locations id according to projectwise
	$inspectionIds = array();//To inspectionids according to projectwise

	foreach($projectId as $pID){#$pID['project_id']
		if($projectIDs == ''){
			$projectIDs = $pID['project_id'];
		}else{
			$projectIDs .= ','.$pID['project_id'];
		}
	}
	$syncPermissionData = $db->selQRYMultiple('no_of_days, status, project_id, device_type, location_ids', 'sync_permission', 'project_id IN ('.$projectIDs.') AND is_deleted = 0 AND device_type = "'.$deviceType.'"');
	foreach($syncPermissionData as $syncData){
		$dateRange[$syncData['project_id']] = $syncData['no_of_days'];
		$statsRange[$syncData['project_id']] = $syncData['status'];
		$locationIDsArray[$syncData['project_id']] = $syncData['location_ids'];
	}
	
#	$locationIDsArray = getLoctionidsforSync($parentLocIDS);
	
	foreach($projectId as $project){
		$inspId = array();
		$selIssuetoInspections = 'distinct pi.inspection_id, pi.project_id';
//Location Condition here
		if($locationIDsArray[$project['project_id']] != ''){
			$locCondition = ' AND pi.location_id IN ('.$locationIDsArray[$project['project_id']].') ';		
		}else{
			$locCondition = '';
		}
		if($locationIDsArray[$project['project_id']] == 'Select All'){
			$locCondition = '';
		}		
		
		if($statsRange[$project['project_id']] == "'ALL'" && $dateRange[$project['project_id']] == 100000){
			//In case of Sub Contractor need to send, data of Sub Contractor only.
			if (!empty($issuedToNameProjectId[$project['project_id']])){
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0".$locCondition.$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']] );
				$inspId = array();		
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
					}
				}
			}else{
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 ".$locCondition.$roleProjectId[$project['project_id']]);
				$inspId = array();		
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
					}
				}
			}
		}else{
			if($statsRange[$project['project_id']] == "'ALL'"){
				//In case of Sub Contractor need to send, data of Sub Contractor only.
				if (!empty($issuedToNameProjectId[$project['project_id']])){
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 ".$locCondition." AND pi.inspection_id=isi.inspection_id AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date".$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']] );
					$inspId = array();
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}else{
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 ".$locCondition." AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date".$roleProjectId[$project['project_id']]);
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}
			}else{
				$forAll = '';
				$forStatus = '';
				$inspId1 = array();	
				$inspId2 = array();
				$statusPermission = explode(', ', $statsRange[$project['project_id']]);
				for($g=0; $g<count($statusPermission); $g++){
					if(preg_match("/^'All /", $statusPermission[$g])){
						if($forAll == ''){
							$forAll = str_replace("All ", "", $statusPermission[$g]);
						}else{
							$forAll .= ', '.str_replace("All ", "", $statusPermission[$g]);
						}
					}else{
						if($forStatus == ''){
							$forStatus = $statusPermission[$g];
						}else{
							$forStatus .= ', '.$statusPermission[$g];
						}
					}
				}
				if($forAll != ''){
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND isi.inspection_status IN (".$forAll.") AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0 ".$locCondition.$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']]);
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId1[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}
				if($forStatus != ''){
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 ".$locCondition." AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date AND isi.inspection_status IN (".$forStatus.") AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0".$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']]);
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId2[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}
				$inspId = array_merge($inspId1, $inspId2);
			}
		}
		$inspectionIds[$project['project_id']] = array_unique($inspId);
	}
	return $inspectionIds;	
}
//Function that retrun accepte inspectionIDS filtered by diffrent conditions on subsequent sync
function subsequent_sync_acceptedInpsectionIDS($userRoleData, $deviceType){
	global $db;//Global Object for database query and predefine function
	global $lastModifiedDate;//Global Object for Last Modified Date
	$roleProjectId = $userRoleData[0];
	$projectId = $userRoleData[1];
	$issuedToNameProjectId = $userRoleData[2];
	$projectIDs = '';
	$dateRange = array();//To store days according to projectwise
	$statsRange = array();//To store status according to projectwise
	$changeStatus = array();//To store modified status according to projectwise
	$parentLocIDS = array();//To store Parent Locations id according to projectwise
	$inspectionIds = array();//To inspectionids according to projectwise

	foreach($projectId as $pID){#$pID['project_id']
		if($projectIDs == ''){
			$projectIDs = $pID['project_id'];
		}else{
			$projectIDs .= ','.$pID['project_id'];
		}
	}
//check sync permission updated or not.
	$syncPermissionData = $db->selQRYMultiple('no_of_days, status, project_id, device_type, location_ids, last_modified_date', 'sync_permission', 'project_id IN ('.$projectIDs.') AND is_deleted = 0 AND device_type = "'.$deviceType.'"');
	foreach($syncPermissionData as $syncData){
		if($syncData['last_modified_date'] > $lastModifiedDate){
			$changeStatus[$syncData['project_id']] = 1;
		}else{
			$changeStatus[$syncData['project_id']] = 0;
		}
		$dateRange[$syncData['project_id']] = $syncData['no_of_days'];
		$statsRange[$syncData['project_id']] = $syncData['status'];
		$locationIDsArray[$syncData['project_id']] = $syncData['location_ids'];
	}
	
#	$locationIDsArray = getLoctionidsforSync($parentLocIDS);

	foreach($projectId as $project){
		$inspId = array();
		$inspeIdsString = '';
		$selIssuetoInspections = 'distinct pi.inspection_id, pi.project_id';
//Location Condition here
		if($locationIDsArray[$project['project_id']] != ''){
			$locCondition = ' AND pi.location_id IN ('.$locationIDsArray[$project['project_id']].') ';		
		}else{
			$locCondition = '';
		}
		if($locationIDsArray[$project['project_id']] == 'Select All'){
			$locCondition = '';
		}		
//New sync permission logic start here		
		if($changeStatus[$project['project_id']] == 1){//Last modified date condition not affected here like first sync
			if($statsRange[$project['project_id']] == "'ALL'" && $dateRange[$project['project_id']] == 100000){
				//In case of Sub Contractor need to send, data of Sub Contractor only.
				if (!empty($issuedToNameProjectId[$project['project_id']])){
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.inspection_id = isi.inspection_id ".$locCondition.$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']] );
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}else{
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi WHERE pi.project_id = '".$project['project_id']."' ".$locCondition.$roleProjectId[$project['project_id']]);
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}
			}else{
				if($statsRange[$project['project_id']] == "'ALL'"){
					if (!empty($issuedToNameProjectId[$project['project_id']])){
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' ".$locCondition." AND pi.inspection_id=isi.inspection_id AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date".$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']] );
						$inspId = array();
						if(mysql_num_rows($rsIssuetoInspections) > 0){
							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
							}
						}
					}else{
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi WHERE pi.project_id = '".$project['project_id']."' ".$locCondition." AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date".$roleProjectId[$project['project_id']]);
						$inspId = array();		
						if(mysql_num_rows($rsIssuetoInspections) > 0){
							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
							}
						}
					}
				}else{
					$forAll = '';
					$forStatus = '';
					$inspId1 = array();	
					$inspId2 = array();
					$statusPermission = explode(', ', $statsRange[$project['project_id']]);
					for($g=0; $g<count($statusPermission); $g++){
						if(preg_match("/^'All /", $statusPermission[$g])){
							if($forAll == ''){
								$forAll = str_replace("All ", "", $statusPermission[$g]);
							}else{
								$forAll .= ', '.str_replace("All ", "", $statusPermission[$g]);
							}
						}else{
							if($forStatus == ''){
								$forStatus = $statusPermission[$g];
							}else{
								$forStatus .= ', '.$statusPermission[$g];
							}
						}
					}
					if($forAll != ''){
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND isi.inspection_status IN (".$forAll.") AND pi.inspection_id = isi.inspection_id ".$locCondition.$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']]);
						if(mysql_num_rows($rsIssuetoInspections) > 0){
							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId1[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
							}
						}
					}
					if($forStatus != ''){
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' ".$locCondition." AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date AND isi.inspection_status IN (".$forStatus.") AND pi.inspection_id = isi.inspection_id ".$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']]);
						if(mysql_num_rows($rsIssuetoInspections) > 0){
							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId2[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
							}
						}
					}
					$inspId = array_merge($inspId1, $inspId2);
				}
			}	
		}else{//Last modified date affected here so days refuse here
			if($statsRange[$project['project_id']] == "'ALL'" && $dateRange[$project['project_id']] == 100000){
				//In case of Sub Contractor need to send, data of Sub Contractor only.
				if (!empty($issuedToNameProjectId[$project['project_id']])){
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.inspection_id = isi.inspection_id ".$locCondition.$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']]." AND last_modified_date >= '".$lastModifiedDate."'");
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}else{
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi WHERE pi.project_id = '".$project['project_id']."' ".$locCondition.$roleProjectId[$project['project_id']]." AND last_modified_date >= '".$lastModifiedDate."'");
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}
			}else{
				if($statsRange[$project['project_id']] == "'ALL'"){
					//In case of Sub Contractor need to send, data of Sub Contractor only.
					if (!empty($issuedToNameProjectId[$project['project_id']])){
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' ".$locCondition." AND pi.inspection_id=isi.inspection_id AND pi.last_modified_date >= '".$lastModifiedDate."' ".$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']] );
						$inspId = array();
						if(mysql_num_rows($rsIssuetoInspections) > 0){
							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
							}
						}
					}else{
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi WHERE pi.project_id = '".$project['project_id']."' ".$locCondition." AND pi.last_modified_date >= '".$lastModifiedDate."' ".$roleProjectId[$project['project_id']]);
						$inspId = array();		
						if(mysql_num_rows($rsIssuetoInspections) > 0){
							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
							}
						}
					}
				}else{
					$forAll = '';
					$forStatus = '';
					$inspId1 = array();	
					$inspId2 = array();
					$statusPermission = explode(', ', $statsRange[$project['project_id']]);
					for($g=0; $g<count($statusPermission); $g++){
						if(preg_match("/^'All /", $statusPermission[$g])){
							if($forAll == ''){
								$forAll = str_replace("All ", "", $statusPermission[$g]);
							}else{
								$forAll .= ', '.str_replace("All ", "", $statusPermission[$g]);
							}
						}else{
							if($forStatus == ''){
								$forStatus = $statusPermission[$g];
							}else{
								$forStatus .= ', '.$statusPermission[$g];
							}
						}
					}
					if($forAll != ''){
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND isi.inspection_status IN (".$forAll.") AND pi.inspection_id = isi.inspection_id ".$locCondition.$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']]." AND last_modified_date >= '".$lastModifiedDate."'");
						if(mysql_num_rows($rsIssuetoInspections) > 0){
							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId1[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
							}
						}
					}
					if($forStatus != ''){
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' ".$locCondition." AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date AND isi.inspection_status IN (".$forStatus.") AND pi.inspection_id = isi.inspection_id ".$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']]." AND last_modified_date >= '".$lastModifiedDate."'");
						if(mysql_num_rows($rsIssuetoInspections) > 0){
							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId2[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
							}
						}
					}
					$inspId = array_merge($inspId1, $inspId2);
				}
			}
		}
//New sync permission logic end here
		$inspeIdsString = join(', ', array_unique($inspId));
		$inspectionIds[$project['project_id']] = $inspeIdsString;
	}
	return $inspectionIds;	
}
//Function that retrun refused inspectionIDS for uploaded data on subsequent sync
function subsequent_sync_refusedInpsectionIDS($projectId, $userId, $deviceType){
	global $db;//Global Object for database query and predefine function
	$inspectionIds = array();//To inspectionids according to projectwise

	$curTime = $db->selQRY("MAX(created_date) as curTime", "importData", "userid = ".$userId." AND device = '".$deviceType."' AND importDataType = 'tableData'");
	
	foreach($projectId as $project){
		$refuseIdsStr = '';
		$inspArray = $db->selQRYMultiple("inspection_id", "project_inspections", "last_modified_by = ".$userId." AND resource_type = '".$deviceType."' AND last_modified_date BETWEEN '".$curTime['curTime']."' AND ('".$curTime['curTime']."' + INTERVAL 1 MINUTE) AND project_id = ".$project['project_id']);
		foreach($inspArray as $inspArr){
			if($refuseIdsStr == ''){
				$refuseIdsStr = $inspArr['inspection_id'];
			}else{
				$refuseIdsStr = ', '.$inspArr['inspection_id'];
			}
		}
		$inspectionIds[$project['project_id']] = $refuseIdsStr;
	}
	return $inspectionIds;	
}
//Function to fetch the data for project wist location ids
/*function getLoctionidsforSync($locationIDsArr){
	global $db;
	$subIdsProj = array();
	foreach($locationIDsArr as $key=>$value){
		if($value == 'Select All'){
			$subIdsProj[$key] = '';
		}else{
			$subIds = '';
			if($value != ''){
				$locid = explode(',', $value);
				$locid = array_map('trim', $locid);
				if(!empty($locid)){
					for($i=0;$i<count($locid);$i++){
						if($subIds == ''){
							$subIds = $db->subLocationsId($locid[$i], ', ');
						}else{
							$subIds .= ', '.$db->subLocationsId($locid[$i], ', ');
						}
					}
					$subIdsProj[$key] = $subIds;
				}
			}
		}
	}
	return $subIdsProj;
}*/
//------------------------------------------- Comman Functions End Here ---------------------------------------------
//Function to create meta data files
//In colString primary key always on first location
//Project Array must be a array of array
//------------------------------------------- First Sync Start Here ---------------------------------------------
//Functin for Meta Data JSON
function first_sync_metaData($table, $colString, $projectArray){
	global $db;
	$queryArray = array();
	foreach($projectArray as $project){
		$selStr = $colString;
		$colArray = explode(',', $colString);
		$colArray = array_map('trim', $colArray);
		$loopCount = $recCount = sizeof($colArray);
		
		$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE project_id = '".$project['project_id']."' AND is_deleted = 0");
		$valString = '';
		if(mysql_num_rows($rs) > 0){
			while($row = mysql_fetch_assoc($rs)){
				$valStr = '';
				for($i=0; $i<$loopCount; $i++){
					if($valStr == ''){
						$valStr = "\"".$db->dataFilter($row[$colArray[$i]])."\"";
					}else{
						$valStr .= ", \"".$db->dataFilter($row[$colArray[$i]])."\"";
					}
				}
				$queryArray[] = 'SELECT '.$valStr;
			}
			
		}
	}
	$colArray[0] = 'global_id';
#	if($table == 'project_locations'){$colArray = array_merge(array('location_id'), $colArray);}
	if(count($queryArray) > 0){
		createJsonFile(EXPORTFILEPATH.'/download/', $table, join(', ', $colArray), $queryArray);
	}
}
//Function for Quality Assurence Data JSON
function first_sync_qaData($table, $colString, $projectArray){
	global $db;
	$queryArray = array();
	foreach($projectArray as $project){
		$selStr = $colString;
		$colArray = explode(',', $colString);
		$colArray = array_map('trim', $colArray);
		$loopCount = $recCount = sizeof($colArray);
		
		$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE project_id = '".$project['project_id']."' AND is_deleted = 0");
		$valString = '';
		if(mysql_num_rows($rs) > 0){
			while($row = mysql_fetch_assoc($rs)){
				$valStr = '';
				for($i=0; $i<$loopCount; $i++){
					if($valStr == ''){
						$valStr = "\"".$db->dataFilter($row[$colArray[$i]])."\"";
					}else{
						$valStr .= ", \"".$db->dataFilter($row[$colArray[$i]])."\"";
					}
				}
				$queryArray[] = 'SELECT '.$valStr;
			}
			
		}
	}
	$colArray[0] = 'global_id';
	if(count($queryArray) > 0){
		createJsonFile(EXPORTFILEPATH.'/download/', $table, join(', ', $colArray), $queryArray);
	}
}
//Function fo Progress Monitoring Data JSON
function first_sync_pmData($table, $colString, $projectArray){
	global $db;
	$queryArray = array();
	foreach($projectArray as $project){
		$selStr = $colString;
		$colArray = explode(',', $colString);
		$colArray = array_map('trim', $colArray);
		$loopCount = $recCount = sizeof($colArray);
		
		$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE project_id = '".$project['project_id']."' AND is_deleted = 0");
		$valString = '';
		if(mysql_num_rows($rs) > 0){
			while($row = mysql_fetch_assoc($rs)){
				$valStr = '';
				for($i=0; $i<$loopCount; $i++){
					if($valStr == ''){
						$valStr = "\"".$db->dataFilter($row[$colArray[$i]])."\"";
					}else{
						$valStr .= ", \"".$db->dataFilter($row[$colArray[$i]])."\"";
					}
				}
				$queryArray[] = 'SELECT '.$valStr;
			}
			
		}
	}
	$colArray[0] = 'global_id';
	if(count($queryArray) > 0){
		createJsonFile(EXPORTFILEPATH.'/download/', $table, join(', ', $colArray), $queryArray);
	}
}
//Function to create Data for User Project Table
//Function for creating txt file 
function first_sync_txtFile($table, $colString, $userID){
	global $db;
	$queryArray = array();
	$selStr = $colString;
	$colArray = explode(',', $colString);
	$colArray = array_map('trim', $colArray);
	$loopCount = $recCount = sizeof($colArray);
	$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE user_id = '".$userID."' AND is_deleted = 0");
	$valString = '';
	if(mysql_num_rows($rs) > 0){
		while($row = mysql_fetch_assoc($rs)){
			$valStr = '';
			for($i=0; $i<$loopCount; $i++){
				if($valStr == ''){
					$valStr = "\"".$db->dataFilter($row[$colArray[$i]])."\"";
				}else{
					$valStr .= ", \"".$db->dataFilter($row[$colArray[$i]])."\"";
				}
			}
			$valString .= $row[$colArray[0]]."##VALUES(".$valStr.");\r\n";
			$queryArray[] = $valString;
			$valString = '';
		}
	}
	if(count($queryArray) > 0){
		$db->createFile($table.'_add.txt', join('', $queryArray), EXPORTFILEPATH.'/download/');//Write File Here 
	}
}
//------------------------------------------- First Sync End Here -------------------------------------------------
//------------------------------------------- Sub Sequent Sync Start Here -----------------------------------------
//Functin for Meta Data Add
function subsequent_sync_metaData_add($table, $colString, $projectArray, $new_project, $global_id){
	global $lastModifiedDate;
	global $db;
	$queryArray = array();
	foreach($projectArray as $project){
		$selStr = $colString;
		$colArray = explode(',', $colString);
		$colArray = array_map('trim', $colArray);
		$loopCount = $recCount = sizeof($colArray);
		
		if($new_project[$project['project_id']] == 1){
			$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE project_id = '".$project['project_id']."' AND is_deleted = 0");
		}else{
			$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE project_id = '".$project['project_id']."' AND created_date >= '".$lastModifiedDate."' and is_deleted = 0");
		}
		$valString = '';
		if(mysql_num_rows($rs) > 0){
			while($row = mysql_fetch_assoc($rs)){
				$valStr = '';
				for($i=0; $i<$loopCount; $i++){
					if($valStr == ''){
						$valStr = "\"".$db->dataFilter($row[$colArray[$i]])."\"";
					}else{
						$valStr .= ", \"".$db->dataFilter($row[$colArray[$i]])."\"";
					}
				}
				$valString .= $row[$global_id]."##VALUES(".$valStr.");\r\n";
				$queryArray[] = $valString;
				$valString = '';
			}
		}
	}
	if(count($queryArray) > 0){
		$db->createFile($table.'_add.txt', join('', $queryArray), EXPORTFILEPATH.'/download/');
	}
}
//Functin for Meta Data Update
function subsequent_sync_metaData_update($table, $colString, $projectArray){
	global $lastModifiedDate;
	global $db;
	$queryArray = array();
	foreach($projectArray as $project){
		$selStr = $colString;
		$colArray = explode(',', $colString);
		$updateID = $colArray[0];
		array_shift($colArray);
		$colArray = array_map('trim', $colArray);
		$loopCount = $recCount = sizeof($colArray);

		$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE last_modified_date >= '".$lastModifiedDate."' AND  project_id = '".$project['project_id']."'");

		$valString = '';
		if(mysql_num_rows($rs) > 0){
			while($row = mysql_fetch_assoc($rs)){
				$valStr = '';
				for($i=0; $i<$loopCount; $i++){
					if($valStr == ''){
						$valStr = $colArray[$i]." = \"".$db->dataFilter($row[$colArray[$i]])."\"";
					}else{
						$valStr .= ", ".$colArray[$i]." = \"".$db->dataFilter($row[$colArray[$i]])."\"";
					}
				}
				$valString .= "UPDATE ".$table." SET ".$valStr." WHERE global_id = ".$row[$updateID].";\r\n";
				$queryArray[] = $valString;
				$valString = '';
			}
		}
	}
	if(count($queryArray) > 0){
		$db->createFile($table.'_update.txt', join('', $queryArray), EXPORTFILEPATH.'/download/');
	}
}
//Functin for Quality Assurence Data Add
function subsequent_sync_qaData_add($table, $colString, $projectArray, $new_project, $global_id){
	global $lastModifiedDate;
	global $db;
	$queryArray = array();
	foreach($projectArray as $project){
		$selStr = $colString;
		$colArray = explode(',', $colString);
		$colArray = array_map('trim', $colArray);
		$loopCount = $recCount = sizeof($colArray);
		
		if($new_project[$project['project_id']] == 1){
			$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE project_id = '".$project['project_id']."' AND is_deleted = 0");
		}else{
			$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE project_id = '".$project['project_id']."' AND created_date >= '".$lastModifiedDate."' and is_deleted = 0");
		}
		$valString = '';
		if(mysql_num_rows($rs) > 0){
			while($row = mysql_fetch_assoc($rs)){
				$valStr = '';
				for($i=0; $i<$loopCount; $i++){
					if($valStr == ''){
						$valStr = "\"".$db->dataFilter($row[$colArray[$i]])."\"";
					}else{
						$valStr .= ", \"".$db->dataFilter($row[$colArray[$i]])."\"";
					}
				}
				$valString .= $row[$global_id]."##VALUES(".$valStr.");\r\n";
				$queryArray[] = $valString;
				$valString = '';
			}
		}
	}
	if(count($queryArray) > 0){
		$db->createFile($table.'_add.txt', join('', $queryArray), EXPORTFILEPATH.'/download/');
	}
}
//Functin for Quality Assurence Data Update
function subsequent_sync_qaData_update($table, $colString, $projectArray){
	global $lastModifiedDate;
	global $db;
	$queryArray = array();
	foreach($projectArray as $project){
		$selStr = $colString;
		$colArray = explode(',', $colString);
		$updateID = $colArray[0];
		array_shift($colArray);
		$colArray = array_map('trim', $colArray);
		$loopCount = $recCount = sizeof($colArray);

		$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE last_modified_date >= '".$lastModifiedDate."' AND  project_id = '".$project['project_id']."'");

		$valString = '';
		if(mysql_num_rows($rs) > 0){
			while($row = mysql_fetch_assoc($rs)){
				$valStr = '';
				for($i=0; $i<$loopCount; $i++){
					if($valStr == ''){
						$valStr = $colArray[$i]." = \"".$db->dataFilter($row[$colArray[$i]])."\"";
					}else{
						$valStr .= ", ".$colArray[$i]." = \"".$db->dataFilter($row[$colArray[$i]])."\"";
					}
				}
				$valString .= "UPDATE ".$table." SET ".$valStr." WHERE global_id = ".$row[$updateID].";\r\n";
				$queryArray[] = $valString;
				$valString = '';
			}
		}
	}
	if(count($queryArray) > 0){
		$db->createFile($table.'_update.txt', join('', $queryArray), EXPORTFILEPATH.'/download/');
	}
}
//Functin for Progress Monitoring Data Update
function subsequent_sync_pmData_add($table, $colString, $projectArray, $new_project, $global_id){
	global $lastModifiedDate;
	global $db;
	$queryArray = array();
	foreach($projectArray as $project){
		$selStr = $colString;
		$colArray = explode(',', $colString);
		$colArray = array_map('trim', $colArray);
		$loopCount = $recCount = sizeof($colArray);
		
		if($new_project[$project['project_id']] == 1){
			$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE project_id = '".$project['project_id']."' AND is_deleted = 0");
		}else{
			$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE project_id = '".$project['project_id']."' AND created_date >= '".$lastModifiedDate."' and is_deleted = 0");
		}
		$valString = '';
		if(mysql_num_rows($rs) > 0){
			while($row = mysql_fetch_assoc($rs)){
				$valStr = '';
				for($i=0; $i<$loopCount; $i++){
					if($valStr == ''){
						$valStr = "\"".$db->dataFilter($row[$colArray[$i]])."\"";
					}else{
						$valStr .= ", \"".$db->dataFilter($row[$colArray[$i]])."\"";
					}
				}
				$valString .= $row[$global_id]."##VALUES(".$valStr.");\r\n";
				$queryArray[] = $valString;
				$valString = '';
			}
		}
	}
	if(count($queryArray) > 0){
		$db->createFile($table.'_add.txt', join('', $queryArray), EXPORTFILEPATH.'/download/');
	}
}
//Functin for Progress Monitoring Data Update
function subsequent_sync_pmData_update($table, $colString, $projectArray){
	global $lastModifiedDate;
	global $db;
	$queryArray = array();
	foreach($projectArray as $project){
		$selStr = $colString;
		$colArray = explode(',', $colString);
		$updateID = $colArray[0];
		array_shift($colArray);
		$colArray = array_map('trim', $colArray);
		$loopCount = $recCount = sizeof($colArray);

		$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE last_modified_date >= '".$lastModifiedDate."' AND  project_id = '".$project['project_id']."'");

		$valString = '';
		if(mysql_num_rows($rs) > 0){
			while($row = mysql_fetch_assoc($rs)){
				$valStr = '';
				for($i=0; $i<$loopCount; $i++){
					if($valStr == ''){
						$valStr = $colArray[$i]." = \"".$db->dataFilter($row[$colArray[$i]])."\"";
					}else{
						$valStr .= ", ".$colArray[$i]." = \"".$db->dataFilter($row[$colArray[$i]])."\"";
					}
				}
				$valString .= "UPDATE ".$table." SET ".$valStr." WHERE global_id = ".$row[$updateID].";\r\n";
				$queryArray[] = $valString;
				$valString = '';
			}
		}
	}
	if(count($queryArray) > 0){
		$db->createFile($table.'_update.txt', join('', $queryArray), EXPORTFILEPATH.'/download/');
	}
}
//Function for user project udpate data
function subsequent_sync_userProject_update($table, $colString, $userID){
	global $lastModifiedDate;
	global $db;
	$queryArray = array();
	$selStr = $colString;
	$colArray = explode(',', $colString);
	$updateID = $colArray[0];
	array_shift($colArray);
	$colArray = array_map('trim', $colArray);
	$loopCount = $recCount = sizeof($colArray);

	$rs = mysql_query("SELECT DISTINCT ".$selStr." FROM ".$table." WHERE last_modified_date >= '".$lastModifiedDate."' AND  user_id = '".$userID."'");

	$valString = '';
	if(mysql_num_rows($rs) > 0){
		while($row = mysql_fetch_assoc($rs)){
			$valStr = '';
			for($i=0; $i<$loopCount; $i++){
				if($valStr == ''){
					$valStr = $colArray[$i]." = \"".$db->dataFilter($row[$colArray[$i]])."\"";
				}else{
					$valStr .= ", ".$colArray[$i]." = \"".$db->dataFilter($row[$colArray[$i]])."\"";
				}
			}
			$valString .= "UPDATE ".$table." SET ".$valStr." WHERE global_id = ".$row[$updateID].";\r\n";
			$queryArray[] = $valString;
			$valString = '';
		}
	}
	if(count($queryArray) > 0){
		$db->createFile($table.'_update.txt', join('', $queryArray), EXPORTFILEPATH.'/download/');
	}
}
//------------------------------------------- Sub Sequent Sync End Here -------------------------------------------
?>