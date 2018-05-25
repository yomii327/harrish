<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');
define("SIGNSOURCEPATH", '../inspections/signoff');
//Header Secttion for include and objects 

if(isset($_REQUEST['data_sign'])){

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

define("EXPORTFILEPATH", '../sync/Export/' . $globalId);
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
//Remove Previous Files
	if(!is_dir(EXPORTFILEPATH)){
		@mkdir(EXPORTFILEPATH, 0777);
	}
	$db->recursive_remove_directory(EXPORTFILEPATH.'/signoff');
//Add New Files
	@mkdir(EXPORTFILEPATH.'/signoff/', 0777);
	if(is_dir('../sync/Export/')){
		if(empty($lastModifiedDate)){
//Images
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
					$rsIssuetoInspections = mysql_query("SELECT inspection_id FROM project_inspections WHERE project_id = '".$project['project_id']."' AND inspection_sign_image != '' AND is_deleted=0 ORDER BY inspection_id");
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}else{
					if($statsRange[$project['project_id']] == "'ALL'"){
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date ORDER BY pi.inspection_id");
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
							$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND isi.inspection_status IN (".$forAll.") AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0 ORDER BY pi.inspection_id");
							if(mysql_num_rows($rsIssuetoInspections) > 0){
								while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
									$inspId1[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
								}
							}
						}
						if($forStatus != ''){
							$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date AND isi.inspection_status IN (".$forStatus.") AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0 ORDER BY pi.inspection_id");
							if(mysql_num_rows($rsIssuetoInspections) > 0){
								while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
									$inspId2[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
								}
							}
						}
						$inspId = array_merge($inspId1, $inspId2);
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
				
					$imageCountQry = $db->selQRY('count(*) AS imageCount', 'project_inspections', ' project_id = "'.$project['project_id'].'" AND '.$whereCon.' inspection_sign_image != "" AND is_deleted = 0 ORDER BY inspection_id');
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
							$insSelect .= ', '.$insIdes[($startIndex+$j)];
						}
					}
				}else{
					$insSelect = '';
					for($j=0;$j<$imageLimit;$j++){
						if($insSelect == ''){
							$insSelect = $insIdes[($startIndex+$j)];
						}else{
							$insSelect .= ', '.$insIdes[($startIndex+$j)];
						}
					}
				}
				$imageName = $db->selQRYMultiple('inspection_sign_image', 'project_inspections', 'inspection_id IN ('.$insSelect.') AND inspection_sign_image != ""  AND is_deleted = 0 ORDER BY inspection_id');
				$folder = opendir(SIGNSOURCEPATH);
				$pic_types = array("jpg", "jpeg", "gif", "png");
				$index = array();
				foreach($imageName as $imgName){
					if(file_exists(SIGNSOURCEPATH.'/'.$imgName['inspection_sign_image'])){
						copy(SIGNSOURCEPATH.'/'.$imgName['inspection_sign_image'], EXPORTFILEPATH.'/signoff/'.$imgName['inspection_sign_image']);
					}
				}
				$zipSource = EXPORTFILEPATH.'/signoff/';
				$db->createFile('last_modified_date.txt', $date, $zipSource);//Write File Here 
				
				if($db->emptyDirectory($zipSource)){
					$db->compress($zipSource, 'signoff', EXPORTFILEPATH);
//Code for Download the zip file
					$filename = 'signoff.zip';
					header("Content-type: application/zip;\n");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".filesize(EXPORTFILEPATH.'/'.$filename)."\n");
					header("Content-Disposition: attachment; filename=".$filename);
					ob_end_flush();
					@readfile(EXPORTFILEPATH.'/'.$filename);
//Code for Download the zip file
				}else{
					$output = array(
						'status' => true,
						'message' => 'No One Sign Found',
						'last_modified_date' => $date,
						'data' => ''
					);
					echo '['.json_encode($output).']';
					die;
				}
				closedir($folder);
			}			
		}else{
			$projectId = array();
			$syncPermissionData = $db->selQRYMultiple('no_of_days, status, project_id, device_type', 'sync_permission', 'project_id IN ('.$projectIDs.') AND is_deleted = 0 AND device_type = "'.$deviceType.'"');
			foreach($syncPermissionData as $syncData){
				$projectId[] = array('project_id' => $syncData['project_id']);
			}
#			$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			$projId = '';
			foreach($projectId as $pId){
				if($projId == ''){
					$projId = $pId['project_id'];
				}else{
					$projId .= ', '.$pId['project_id'];
				}
			}
			if(!empty($noofFiles)){
				$imageCountQry = $db->selQRY('count(*) AS imageCount', 'project_inspections', 'project_id IN ('.$projId.') AND inspection_sign_image != "" AND last_modified_date >= "'.$lastModifiedDate.'" AND is_deleted = 0');
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
				$imageName = $db->selQRYMultiple('inspection_sign_image', 'project_inspections', 'project_id IN ('.$projId.') AND inspection_sign_image != "" AND last_modified_date >= "'.$lastModifiedDate.'" AND is_deleted = 0 ORDER BY inspection_id ASC LIMIT '.$startIndex.', '.$imageLimit);
				$folder = opendir(SIGNSOURCEPATH);
				$pic_types = array("jpg", "jpeg", "gif", "png");
				$index = array();
				foreach($imageName as $imgName){
					if(file_exists(SIGNSOURCEPATH.'/'.$imgName['inspection_sign_image'])){
						copy(SIGNSOURCEPATH.'/'.$imgName['inspection_sign_image'], EXPORTFILEPATH.'/signoff/'.$imgName['inspection_sign_image']);
					}
				}
				$zipSource = EXPORTFILEPATH.'/signoff/';
				$db->createFile('last_modified_date.txt', $date, $zipSource);//Write File Here 
				
				if($db->emptyDirectory($zipSource)){
					$db->compress($zipSource, 'signoff', EXPORTFILEPATH);
//Code for Download the zip file
					$filename = 'signoff.zip';
					header("Content-type: application/zip;\n");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".filesize(EXPORTFILEPATH.'/'.$filename)."\n");
					header("Content-Disposition: attachment; filename=".$filename);
					ob_end_flush();
					@readfile(EXPORTFILEPATH.'/'.$filename);
//Code for Download the zip file
				}else{
					$output = array(
						'status' => true,
						'message' => 'No One Sign Found',
						'last_modified_date' => $date,
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