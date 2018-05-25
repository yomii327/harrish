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
	
	$noofFiles = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['noofFiles']))));
	$imageLimit = isset($_REQUEST['imageLimit']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['imageLimit'])))) : 100;
	$startIndex = isset($_REQUEST['startIndex']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['startIndex'])))) : 0;
	
	define("EXPORTFILEPATH", '../sync/Export/' . $userId);

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

	$rsInspectionInspectedBy = mysql_query("SELECT NOW() as date");
	if(mysql_num_rows($rsInspectionInspectedBy) > 0){
		$iPadQueryInspectionInspectedBy = '';
		if($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
			$date = $rowInspectionInspectedBy["date"];
		}
	}
	if(!is_dir(EXPORTFILEPATH)){
		@mkdir(EXPORTFILEPATH, 0777);
	}
//Remove Previous Files
	$db->recursive_remove_directory(EXPORTFILEPATH.'/drawing');
#		$db->recursive_remove_directory(EXPORTFILEPATH.'/'.$globalId);
//Add New Files
#	@mkdir(EXPORTFILEPATH.'/drawing/', 0777);
	@mkdir(EXPORTFILEPATH.'/drawing/', 0777);
	
	if(is_dir('../sync/Export/'.$globalId)){
		$roleProjectId = array();
		$inspArray = array();

		if(empty($lastModifiedDate)){
//Only Selected Projects Data comes		
			$dateRange = array();
			$statsRange = array();
			$projectId = array();
			$inspectionIds = array();
			$syncPermissionData = $db->selQRYMultiple('no_of_days, status, project_id, device_type', 'sync_permission', 'project_id IN ('.$projectIDs.') AND is_deleted = 0 AND device_type = "'.$deviceType.'"');
			foreach($syncPermissionData as $syncData){
				$dateRange[$syncData['project_id']] = $syncData['no_of_days'];
				$statsRange[$syncData['project_id']] = $syncData['status'];
				$projectId[] = array('project_id' => $syncData['project_id']);
			}

			$issuedToNameProjectId = array();//To have Issued name in case of Sub Contractor Role.
			$user_roles = $db->selQRYMultiple('user_role, project_id, issued_to', 'user_projects', 'user_id = "'.$userId.'" AND project_id IN ('.$projectIDs.') AND is_deleted = 0');
			foreach($user_roles as $user_role){
				if($user_role['user_role'] != 'All Defect' && $user_role['user_role'] != 'Sub Contractor'){
					$spCondition = ' AND inspection_raised_by = "'.$user_role['user_role'].'"';
					$issuedToNameProjectId[$user_role['project_id']] = '';
				}else{
					
					$spCondition = '';
					if ($user_role['user_role'] == 'Sub Contractor')
					{
						$issuedToNameProjectId[$user_role['project_id']] =  ' and isi.issued_to_name="' . $user_role["issued_to"] . '"';
					}
				}
				$roleProjectId[$user_role['project_id']] = $spCondition;
			}

			foreach($projectId as $project){
				$inspList4Role = '';
				$selProjectInspections = 'inspection_id, project_id';
				//NEED TO ADD CODE FOR SUBCONTRACTOR
				if (!empty($issuedToNameProjectId[$project['project_id']]))
				{
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0".$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']] );
				}else{				
					$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections WHERE project_id = '".$project['project_id']."' and is_deleted=0".$roleProjectId[$project['project_id']]);
				}
				if(mysql_num_rows($rsProjectInspections) > 0){
					while($rowProjectInspections = mysql_fetch_assoc($rsProjectInspections)){
						if($inspList4Role == ''){
							$inspList4Role = $rowProjectInspections['inspection_id'];
						}else{
							$inspList4Role .= ', '.$rowProjectInspections['inspection_id'];
						}
					}
				}
				$inspArray[$project['project_id']] = $inspList4Role;
			}
//Only Selected Projects Data comes		
			#$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			$projId = '';
			foreach($projectId as $pId){
				if($projId == ''){
					$projId = $pId['project_id'];
				}else{
					$projId .= ', '.$pId['project_id'];
				}
			}
			foreach($projectId as $project){
				$inspId = array();
				$inspId1 = array();		
				$inspId2 = array();
				$selIssuetoInspections = 'distinct pi.inspection_id, pi.project_id';
				if($statsRange[$project['project_id']] == "'ALL'" && $dateRange[$project['project_id']] == 100000){
					$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND graphic_type = 'drawing' AND is_deleted=0 AND inspection_id IN (".$inspArray[$project['project_id']].") ORDER BY inspection_id");
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}else{
					if($statsRange[$project['project_id']] == "'ALL'"){
						if (!empty($issuedToNameProjectId[$project['project_id']])){
							$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE  pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date".$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']] ."  ORDER BY pi.inspection_id");
						}else{
							$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date".$roleProjectId[$project['project_id']]."  ORDER BY pi.inspection_id");
						}
						$inspId = array();		
						if(mysql_num_rows($rsIssuetoInspections) > 0){
							while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
								$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
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
							$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND isi.inspection_status IN (".$forAll.") AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0".$roleProjectId[$project['project_id']]. $issuedToNameProjectId[$project['project_id']]."  ORDER BY pi.inspection_id");
							if(mysql_num_rows($rsIssuetoInspections) > 0){
								while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
									$inspId1[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
								}
							}
						}
						if($forStatus != ''){
							$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date AND isi.inspection_status IN (".$forStatus.") AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0".$roleProjectId[$project['project_id']]. $issuedToNameProjectId[$project['project_id']]."  ORDER BY pi.inspection_id");
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
				$inspectionIds[$project['project_id']] = $inspId;
			}
			if(!empty($noofFiles)){
				$imageCount = 0;
				foreach($projectId as $project){
					if($statsRange[$project['project_id']] == "'ALL'" && $dateRange[$project['project_id']] == 100000){
						$whereCon = '';	
					}else{
						$insSelect = '';
						$lupCount = count($inspectionIds[$project['project_id']]);
						for($s=0;$s<$lupCount;$s++){
							if($insSelect == ''){
								$insSelect = $inspectionIds[$project['project_id']][$s];
							}else{
								$insSelect .= ', '.$inspectionIds[$project['project_id']][$s];
							}
						}
						$whereCon = 'inspection_id IN ('.$insSelect.') AND ';
					}
				
					$imageCountQry = $db->selQRY('count(*) AS imageCount', 'inspection_graphics', ' project_id = "'.$project['project_id'].'" AND '.$whereCon.'  graphic_type = "drawing" AND is_deleted = 0 ORDER BY inspection_id');
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
				$last_modified_graphic_date = "";
				foreach($imageName as $imgName){
					if(file_exists(DRAWINGSOURCEPATH.'/'.$imgName['graphic_name'])){
						copy(DRAWINGSOURCEPATH.'/'.$imgName['graphic_name'], EXPORTFILEPATH.'/drawing/'.$imgName['graphic_name']);
					}
					$last_modified_graphic_date = $imgName['last_modified_date'];
				}
				$zipSource = EXPORTFILEPATH.'/drawing/';
				if($last_modified_graphic_date == ''){
					$last_modified_graphic_date = $date;
				}
				$db->createFile('last_modified_date.txt', $last_modified_graphic_date, $zipSource);//Write File Here
				if($db->emptyDirectory($zipSource)){
					$db->compress($zipSource, 'drawing', EXPORTFILEPATH);
					
					$zipFileName = $db->updateExportTable_new(EXPORTFILEPATH.'/drawing/', $_REQUEST['userId'], $deviceType, 'drawingImages');
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
		}else{///IF MODIFIED DATE PROVIDED
			$curTime = $db->selQRY("MAX(created_date) as curTime", "importData", "userid = ".$userId." AND device = '".$deviceType."' AND importDataType = 'tableData'");

			$subQuery = "SELECT inspection_id FROM project_inspections WHERE last_modified_by = ".$userId." AND resource_type = '".$deviceType."' AND last_modified_date BETWEEN '".$curTime['curTime']."' AND ('".$curTime['curTime']."' + INTERVAL 1 MINUTE)";

			$projectId = array();
			$syncPermissionData = $db->selQRYMultiple('no_of_days, status, project_id, device_type', 'sync_permission', 'project_id IN ('.$projectIDs.') AND is_deleted = 0 AND device_type = "'.$deviceType.'"');
			foreach($syncPermissionData as $syncData){
				$projectId[] = array('project_id' => $syncData['project_id']);
			}
			$issuedToNameProjectId = array();//To have Issued name in case of Sub Contractor Role.
			$user_roles = $db->selQRYMultiple('user_role, project_id, issued_to', 'user_projects', 'user_id = "'.$userId.'" AND project_id IN ('.$projectIDs.') AND is_deleted = 0');
			foreach($user_roles as $user_role){
				if($user_role['user_role'] != 'All Defect' && $user_role['user_role'] != 'Sub Contractor'){
					$spCondition = ' AND inspection_raised_by = "'.$user_role['user_role'].'"';
					$issuedToNameProjectId[$user_role['project_id']] = '';
				}else{
					
					$spCondition = '';
					if ($user_role['user_role'] == 'Sub Contractor')
					{
						$issuedToNameProjectId[$user_role['project_id']] =  ' and isi.issued_to_name="' . $user_role["issued_to"] . '"';
					}
				}
				$roleProjectId[$user_role['project_id']] = $spCondition;
			}

			foreach($projectId as $project){
				$inspList4Role = '';
				$selProjectInspections = 'inspection_id, project_id';
				$selProjectInspections1 = 'pi.inspection_id, pi.project_id';
				if (!empty($issuedToNameProjectId[$project['project_id']]))
				{
					$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections1." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0".$roleProjectId[$project['project_id']] . $issuedToNameProjectId[$project['project_id']] ." AND last_modified_date >= '".$lastModifiedDate."' AND pi.inspection_id NOT IN(".$subQuery.")");
				}else{				
					$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections WHERE project_id = '".$project['project_id']."' and is_deleted=0".$roleProjectId[$project['project_id']]." AND last_modified_date >= '".$lastModifiedDate."' AND inspection_id NOT IN(".$subQuery.")");
				}
				
				if(mysql_num_rows($rsProjectInspections) > 0){
					while($rowProjectInspections = mysql_fetch_assoc($rsProjectInspections)){
						if($inspList4Role == ''){
							$inspList4Role = $rowProjectInspections['inspection_id'];
						}else{
							if (!empty ($rowProjectInspections['inspection_id']))
								$inspList4Role .= ', '.$rowProjectInspections['inspection_id'];
						}
					}
				}
				$inspArray[$project['project_id']] = $inspList4Role;
			}
#			$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			$projId = '';
			$inspSelect = '';
			foreach($projectId as $pId){
				if($projId == ''){
					$projId = $pId['project_id'];
				}else{
					$projId .= ', '.$pId['project_id'];
				}
				if($inspArray[$pId['project_id']] != ''){
					if($inspSelect == ''){
						$inspSelect = $inspArray[$pId['project_id']];
					}else{
						if (!empty ($inspArray[$pId['project_id']]))
							$inspSelect .= ', '.$inspArray[$pId['project_id']];
					}
				}
			}
			$rsProjectInspections = $db->selQRYMultiple('inspection_id', 'inspection_graphics', ' inspection_id IN ('.$inspSelect.') AND graphic_type = "drawing" AND last_modified_date >= "'.$lastModifiedDate.'" AND is_deleted = 0');
			$inspSelect = "";
			$imageCount = 0;
			foreach($rsProjectInspections as $rowProjectInspections){
				$imageCount++;
				if($inspSelect == ''){
					$inspSelect = $rowProjectInspections['inspection_id'];
				}else{
					if (!empty ($rowProjectInspections['inspection_id']))
						$inspSelect .= ', '.$rowProjectInspections['inspection_id'];
				}
			}
			if(!empty($noofFiles)){
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
				$imageName = $db->selQRYMultiple('graphic_name, last_modified_date', 'inspection_graphics', 'project_id IN ('.$projId.')  AND inspection_id IN ('.$inspSelect.') AND graphic_type = "drawing"  AND last_modified_date >= "'.$lastModifiedDate.'" AND is_deleted = 0 ORDER BY last_modified_date, inspection_id ASC LIMIT '.$startIndex.', '.$imageLimit);
				$folder = opendir(DRAWINGSOURCEPATH);
				$pic_types = array("jpg", "jpeg", "gif", "png");
				$index = array();
				$last_modified_graphic_date = "";
				foreach($imageName as $imgName){
					if(file_exists(DRAWINGSOURCEPATH.'/'.$imgName['graphic_name'])){
						copy(DRAWINGSOURCEPATH.'/'.$imgName['graphic_name'], EXPORTFILEPATH.'/drawing/'.$imgName['graphic_name']);
					}
					$last_modified_graphic_date = $imgName ["last_modified_date"];
				}
				$zipSource = EXPORTFILEPATH.'/drawing/';
				if($last_modified_graphic_date == ''){
					$last_modified_graphic_date = $date;
				}
				$db->createFile('last_modified_date.txt', $last_modified_graphic_date, $zipSource);//Write File Here
				if($db->emptyDirectory($zipSource)){
					$db->compress($zipSource, 'drawing', EXPORTFILEPATH);
					
					$zipFileName = $db->updateExportTable_new(EXPORTFILEPATH.'/drawing/', $_REQUEST['userId'], $deviceType, 'drawingImages');
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
?>