<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');
define("DRAWINGSOURCEPATH", '../inspections/drawing');
//Header Secttion for include and objects 

if(isset($_REQUEST['data_drawing'])){
	$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId'])))); 
	$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
	$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
	//Upadted Dated : 04-09-2012
	$projectIDs = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['projectIDs']))));
	$deviceType = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['device']))));
	//Upadted Dated : 04-09-2012
	
	$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
	$storeLastModifiedDate = $lastModifiedDate;
	$noofFiles = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['noofFiles']))));
	$imageLimit = isset($_REQUEST['imageLimit']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['imageLimit'])))) : 100;
	$startIndex = isset($_REQUEST['startIndex']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['startIndex'])))) : 0;

	$sync_url = $db->curPageURL($_POST);

	define("EXPORTFILEPATH", '../sync/Export/' . $userId);

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
	$date = $db->getCurrentDateTime();
	
	$last_modified_graphic_date = $date;
//Remove Previous Files

	$db->recursive_remove_directory(EXPORTFILEPATH.'/drawing');
#		$db->recursive_remove_directory(EXPORTFILEPATH.'/'.$globalId);
//Add New Files
#	@mkdir(EXPORTFILEPATH.'/drawing/', 0777);
	@mkdir(EXPORTFILEPATH.'/drawing/', 0777);
	
	if(is_dir('../sync/Export/'.$globalId)){
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

		if(empty($lastModifiedDate)){
//Images
//Get Accepted Ids	
			$inspectionIds = first_sync_acceptedInpsectionIDS($userRoleData, $deviceType);
			if(!empty($noofFiles)){
				foreach($inspectionIds as $key=>$value){
					if($value == ''){
						$conD[$key] = 0;
					}else{
						$conD[$key] = join(',', $value);
					}
				}
				$imageCount = 0;
				foreach($projectId as $project){
					$imageCountQry = $db->selQRY('count(*) AS imageCount', 'inspection_graphics', ' project_id = "'.$project['project_id'].'" AND graphic_type = "drawing" AND is_deleted = 0 AND inspection_id IN ('.$conD[$project["project_id"]].') ORDER BY inspection_id');
					$imageCount += $imageCountQry['imageCount'];	
				}
			
				if($imageCount > 0){
					$output = array(
						'status' => true,
						'message' => 'Total images are',
						'data' => $imageCount
					);
					echo '['.json_encode($output).']';
					die;
				}else{
					$output = array(
						'status' => false,
						'message' => 'No One Images Found',
						'data' => $imageCount
					);
					echo '['.json_encode($output).']';
					die;
				}
			}else{
				$insIdes = array();
				foreach($inspectionIds as $idInsp){
					$insIdes = array_merge($insIdes, $idInsp);
				}
				$loopCount = count($insIdes);
				if($loopCount <= $imageLimit){
					$insSelect = '';
					for($j=0;$j<($loopCount-$startIndex);$j++){
						if($insSelect == ''){
							$insSelect = $insIdes[($startIndex+$j)];
						}else{
							if (!empty ($insIdes[($startIndex+$j)]))
								$insSelect .= ', '.$insIdes[($startIndex+$j)];
						}
					}
				}else{
					$insSelect = '';
					for($j=0;$j<$imageLimit;$j++){
						if($insSelect == ''){
							$insSelect = $insIdes[($startIndex+$j)];
						}else{
							if (!empty($insIdes[($startIndex+$j)]))
								$insSelect .= ', '.$insIdes[($startIndex+$j)];
						}
					}
				}
				$imageName = $db->selQRYMultiple('graphic_name, last_modified_date', 'inspection_graphics', 'inspection_id IN ('.$insSelect.') AND graphic_type = "drawing"  AND is_deleted = 0 ORDER BY inspection_id');
				$folder = opendir(DRAWINGSOURCEPATH);
				$pic_types = array("jpg", "jpeg", "gif", "png");
				$index = array();
				foreach($imageName as $imgName){
					if(file_exists(DRAWINGSOURCEPATH.'/'.$imgName['graphic_name'])){
						copy(DRAWINGSOURCEPATH.'/'.$imgName['graphic_name'], EXPORTFILEPATH.'/drawing/'.$imgName['graphic_name']);
					}
					$last_modified_graphic_date = $imgName['last_modified_date'];
				}
				$zipSource = EXPORTFILEPATH.'/drawing/';
				
				if(empty($insIdes[($startIndex+$j+1)])){
					$last_modified_graphic_date = $date;
				}
				
				$db->createFile('last_modified_date.txt', $last_modified_graphic_date, $zipSource, 'w');//Write File Here
				if($db->emptyDirectory($zipSource)){
					$db->compress($zipSource, 'drawing', EXPORTFILEPATH);
					
					$zipFileName = $db->updateExportTable_updated(EXPORTFILEPATH.'/drawing/', $_REQUEST['userId'], $deviceType, 'drawingImages', $sync_url);
					copy(EXPORTFILEPATH.'/drawing.zip', EXPORTFILEPATH.'/drawing_'.$zipFileName.'.zip');
				
					$filename = 'drawing.zip';
					header("Content-type: application/zip;\n");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".filesize(EXPORTFILEPATH.'/'.$filename)."\n");
					header("Content-Disposition: attachment; filename=".$filename);
					ob_end_flush();
					@readfile(EXPORTFILEPATH.'/'.$filename);
				}else{
					$output = array(
						'status' => false,
						'message' => 'No One Images Found',
						'data' => ''
					);
					echo '['.json_encode($output).']';
					die;
				}
				closedir($folder);			
			}
		}else{//Last modified date comes so sub sequent sync
//new Project accepted condition GS Dated : 22-03-2013
			$new_project = array();
			$rs = mysql_query("SELECT project_id FROM user_projects WHERE user_id = '".$userId."' AND created_date >= '".$lastModifiedDate."' AND is_deleted = 0");
			if(mysql_num_rows($rs) > 0){
				while($row = mysql_fetch_assoc($rs)){
					$new_project[$row['project_id']] = 1;
				}
			}
			//Fetch Only new projects Data
			$newUserRoleData = array();
			$newRolePro = array();
			$newIssueTo = array();
			$newProj = array();
					
			foreach($userRoleData[1] as $pro){
				if(array_key_exists($pro['project_id'], $new_project)){
					$newRoleData[$pro['project_id']] = $userRoleData[0][$pro['project_id']];		
					$newProj[] = array('project_id' => $pro['project_id']);
					$newIssueTo[$pro['project_id']] = $userRoleData[1][$pro['project_id']];		
				}
			}
			$newUserRoleData = array($newRoleData, $newProj, $newIssueTo);
			//Fetch Only new projects Data
			$newProjInspIdsArr = first_sync_acceptedInpsectionIDS($newUserRoleData, $deviceType);
			$newProjInspIds = array();
			foreach($newProjInspIdsArr as $key=>$value){
				if ($new_project[$key] != ""){
					$newProjInspIds[$key] = join(',', $value);
				}
			}
//new Project accepted condition GS Dated : 22-03-2013

			//Get Accepted Ids	
			$acceptedInspectionIds = subsequent_sync_acceptedInpsectionIDS($userRoleData, $deviceType);
			
			//Get Refused Ids
			$refusedInspectionIds = subsequent_sync_refusedInpsectionIDS($projectId, $userId, $deviceType);
			
			$inspSelect = '';
			$projId = '';
			$inspDeSelect = '';
			foreach($acceptedInspectionIds as $key=>$value){
				if(array_key_exists($key, $newProjInspIds)){
					if($inspSelect == ''){
						$inspSelect = $newProjInspIds[$key];
					}else{
						$inspSelect .= ','.$newProjInspIds[$key];
					}	
				}else{
					if($value != ''){
						if($inspSelect == ''){
							$inspSelect = $value;
						}else{
							$inspSelect .= ','.$value;
						}	
					}
				}
				if($projId == ''){
					$projId = $key;
				}else{
					$projId .= ','.$key;
				}
			}
			
			foreach($refusedInspectionIds as $key=>$value){
				if($value != ''){
					if($inspDeSelect == ''){
						$inspDeSelect = $value;
					}else{
						$inspDeSelect .= ','.$value;
					}	
				}
			}

			$inspSelect = trim($inspSelect, ', ');
			if($inspSelect == ''){$inspSelect = '0';}
			$inspDeSelect = trim($inspDeSelect, ', '); 
			if($inspDeSelect == ''){$inspDeSelect = '0';}

			$imageCount = 0;

			if(!empty($noofFiles)){
				$imageCountQry = $db->selQRY('count(*) AS imageCount', 'inspection_graphics', 'project_id IN ('.$projId.') AND inspection_id IN ('.$inspSelect.') AND inspection_id NOT IN ('.$inspDeSelect.') AND graphic_type = "drawing" AND is_deleted = 0');
				$imageCount = $imageCountQry['imageCount'];
			
				if($imageCount > 0){
					$output = array(
						'status' => true,
						'message' => 'Total images are',
						'data' => $imageCount
					);
					echo '['.json_encode($output).']';
					die;
				}else{
					$output = array(
						'status' => false,
						'message' => 'No One Images Found',
						'data' => $imageCount
					);
					echo '['.json_encode($output).']';
					die;
				}
			}else{
			
				$imageName = $db->selQRYMultiple('graphic_name, last_modified_date', 'inspection_graphics', 'project_id IN ('.$projId.')  AND inspection_id IN ('.$inspSelect.') AND inspection_id NOT IN ('.$inspDeSelect.') AND graphic_type = "drawing" AND is_deleted = 0 ORDER BY last_modified_date, inspection_id ASC LIMIT '.$startIndex.', '.($imageLimit+1));
				
				
				$folder = opendir(DRAWINGSOURCEPATH);
				$pic_types = array("jpg", "jpeg", "gif", "png");
				$index = array();
				$count = 0;
				foreach($imageName as $imgName){
					$count ++;
					if ($count > $imageLimit){
						break;
					}
					if(file_exists(DRAWINGSOURCEPATH.'/'.$imgName['graphic_name'])){
						copy(DRAWINGSOURCEPATH.'/'.$imgName['graphic_name'], EXPORTFILEPATH.'/drawing/'.$imgName['graphic_name']);
					}
					$last_modified_graphic_date = $imgName ["last_modified_date"];
				}
				if ($count <= $imageLimit){
					$last_modified_graphic_date = $date;
				}
				$zipSource = EXPORTFILEPATH.'/drawing/';
				$db->createFile('last_modified_date.txt', $last_modified_graphic_date, $zipSource, 'w');//Write File Here
				if($db->emptyDirectory($zipSource)){
					$db->compress($zipSource, 'drawing', EXPORTFILEPATH);
					
					$zipFileName = $db->updateExportTable_updated(EXPORTFILEPATH.'/drawing/', $_REQUEST['userId'], $deviceType, 'drawingImages', $sync_url);
					copy(EXPORTFILEPATH.'/drawing.zip', EXPORTFILEPATH.'/drawing_'.$zipFileName.'.zip');
				
					$filename = 'drawing.zip';
					header("Content-type: application/zip;\n");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".filesize(EXPORTFILEPATH.'/'.$filename)."\n");
					header("Content-Disposition: attachment; filename=".$filename);
					ob_end_flush();
					@readfile(EXPORTFILEPATH.'/'.$filename);
				}else{
					$output = array(
						'status' => false,
						'message' => 'No One Images Found',
						'data' => ''
					);
					echo '['.json_encode($output).']';
					die;
				}
				closedir($folder);			
			}
		}
	}
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
				$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".join(',', $inspId).") ORDER BY inspection_id");
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
			$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM inspection_graphics WHERE graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".join(",", $inspId).")");
			$inspId = array();		
			if(mysql_num_rows($rsIssuetoInspections) > 0){
				while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
					$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
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
				$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM inspection_graphics WHERE graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".join(",", $inspId).")");
				$inspId = array();		
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
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
				// Added code, so that we can get only inspections who have Images.
				$rsIssuetoInspections = mysql_query("SELECT DISTINCT inspection_id FROM inspection_graphics WHERE graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".join(",", $inspId).")");
				$inspId = array();		
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
					}
				}
			}
		}
		$inspectionIds[$project['project_id']] = array_unique($inspId);
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
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0".$locCondition.$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']] );
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
					$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".join(',', $inspId).") ORDER BY inspection_id");
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
					$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".join(',', $inspId).") ORDER BY inspection_id");
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
					$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".join(',', $inspId).") ORDER BY inspection_id");
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
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
					$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".join(',', $inspId).") ORDER BY inspection_id");
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}
			}	
		}else{//Last modified date affected here so days refuse here
			if($statsRange[$project['project_id']] == "'ALL'" && $dateRange[$project['project_id']] == 100000){
				//In case of Sub Contractor need to send, data of Sub Contractor only.
				if (!empty($issuedToNameProjectId[$project['project_id']])){
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0".$locCondition.$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']]." AND last_modified_date >= '".$lastModifiedDate."'");
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
					$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".join(',', $inspId).") ORDER BY inspection_id");
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}else{
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 ".$locCondition.$roleProjectId[$project['project_id']]." AND last_modified_date >= '".$lastModifiedDate."'");
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
					$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".join(',', $inspId).") ORDER BY inspection_id");
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
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 ".$locCondition." AND pi.inspection_id=isi.inspection_id AND pi.last_modified_date >= '".$lastModifiedDate."' ".$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']] );
						$inspId = array();
						if(mysql_num_rows($rsIssuetoInspections) > 0){
							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
							}
						}
					}else{
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 ".$locCondition." AND pi.last_modified_date >= '".$lastModifiedDate."' ".$roleProjectId[$project['project_id']]);
						$inspId = array();		
						if(mysql_num_rows($rsIssuetoInspections) > 0){
							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
							}
						}
					}
					$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".join(',', $inspId).") ORDER BY inspection_id");
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
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
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND isi.inspection_status IN (".$forAll.") AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0 ".$locCondition.$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']]." AND last_modified_date >= '".$lastModifiedDate."'");
						if(mysql_num_rows($rsIssuetoInspections) > 0){


							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId1[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
							}
						}
					}
					if($forStatus != ''){
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 ".$locCondition." AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date AND isi.inspection_status IN (".$forStatus.") AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0".$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']]." AND last_modified_date >= '".$lastModifiedDate."'");
						if(mysql_num_rows($rsIssuetoInspections) > 0){
							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId2[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
							}
						}
					}
					$inspId = array_merge($inspId1, $inspId2);
					$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".join(',', $inspId).") ORDER BY inspection_id");
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
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

?>