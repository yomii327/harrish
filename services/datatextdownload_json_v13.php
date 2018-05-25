<?php
//Header Secttion for include and objects 
/*error_reporting(E_ALL);
ini_set('display_errors', '1');
*/
set_time_limit(360000);
include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');
define("IMPORTFILEPATH", '../sync/Import');

//Header Secttion for include and objects 
if(isset($_REQUEST['data_text'])){

$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId'])))); 
$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
//Upadted Dated : 04-09-2012
$projectIDs = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['projectIDs']))));
//Upadted Dated : 04-09-2012
$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
$deviceType = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['device']))));

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
	if($db->hashAuth($globalId)){}else{
		$output = array(
			'status' => false,
			'message' => 'User Authentication Fail',
			'data' => ''
		);
		echo '['.json_encode($output).']';
		die;
	}
//Remove Previous Files
//Add New Files
	$rsInspectionInspectedBy = mysql_query("SELECT NOW() as date");
	if(mysql_num_rows($rsInspectionInspectedBy) > 0){
		$iPadQueryInspectionInspectedBy = '';
		if($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy))
		{
			$date_lmd = $rowInspectionInspectedBy["date"];
		}
	}
	define("EXPORTFILEPATH", '../sync/Export/'.$_REQUEST['userId']);
	if(!is_dir(EXPORTFILEPATH)){
		@mkdir(EXPORTFILEPATH, 0777);
	}
	$db->recursive_remove_directory(EXPORTFILEPATH.'/download');
	@mkdir(EXPORTFILEPATH.'/download/', 0777);
	
	if(is_dir('../sync/Export/')){
	//Add New Files
		$roleProjectId = array();
		$user_roles = $db->selQRYMultiple('user_role, project_id', 'user_projects', 'user_id = "'.$userId.'" AND project_id IN ('.$projectIDs.') AND is_deleted = 0');
		foreach($user_roles as $user_role){
			if($user_role['user_role'] != 'All Defect'){
				$spCondition = ' AND inspection_raised_by = "'.$user_role['user_role'].'"';
			}else{
				$spCondition = '';
			}
			$roleProjectId[$user_role['project_id']] = $spCondition;
		}
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
//Data for User Project Table
			$selUserProject = 'DISTINCT project_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, user_role';
			$rsUserProject = mysql_query("SELECT ".$selUserProject." FROM user_projects WHERE user_id = '".$_REQUEST['userId']."' and is_deleted=0");
			
			if(mysql_num_rows($rsUserProject) > 0){
				$iPadUserProject = array();
				while($rowUserProject = mysql_fetch_assoc($rsUserProject)){
					$rowUserProject['global_id'] = $rowUserProject['project_id'];

					$iPadUserProject[] = $rowUserProject['global_id'] . "##VALUES(\"".$db->dataFilter($rowUserProject['global_id'])."\", \"".$db->dataFilter($rowUserProject['user_id'])."\", \"".$db->dataFilter($rowUserProject['project_name'])."\", \"".$db->dataFilter($rowUserProject['project_type'])."\", \"".$db->dataFilter($rowUserProject['project_address_line1'])."\", \"".$db->dataFilter($rowUserProject['project_address_line2'])."\", \"".$db->dataFilter($rowUserProject['project_suburb'])."\", \"".$db->dataFilter($rowUserProject['project_state'])."\", \"".$db->dataFilter($rowUserProject['project_postcode'])."\", \"".$db->dataFilter($rowUserProject['project_country'])."\", \"".$db->dataFilter($rowUserProject['last_modified_date'])."\", \"".$db->dataFilter($rowUserProject['last_modified_by'])."\", \"".$db->dataFilter($rowUserProject['created_date'])."\", \"".$db->dataFilter($rowUserProject['created_by'])."\", \"".$db->dataFilter($rowUserProject['resource_type'])."\", \"".$db->dataFilter($rowUserProject['global_id'])."\", \"".$db->dataFilter($rowUserProject['is_deleted'])."\", \"0\", \"".$db->dataFilter($rowUserProject['user_role'])."\");\r\n";
				}
				$db->createFile('user_projects_add.txt', join('', $iPadUserProject), EXPORTFILEPATH.'/download/');//Write File Here 
			}
			$projectId = array_map('unserialize', array_unique(array_map('serialize', $projectId)));
//Data for Standard Defect Table
			$iPadQueryStandardDefect = array();
#			$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'DISTINCT project_id');
			foreach($projectId as $project){
				$selStandardDefect = 'standard_defect_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted,tag';
				$rsStandardDefect = mysql_query("SELECT ".$selStandardDefect." FROM standard_defects WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsStandardDefect) > 0){
					while($rowStandardDefect = mysql_fetch_assoc($rsStandardDefect)){
						$rowStandardDefect['global_id'] = $rowStandardDefect['standard_defect_id'];

						$iPadQueryStandardDefect[]= " SELECT \"".$db->dataFilter($rowStandardDefect['standard_defect_id'])."\", \"".$db->dataFilter($rowStandardDefect['project_id'])."\", \"".$db->dataFilter($rowStandardDefect['description'])."\", \"".$db->dataFilter($rowStandardDefect['last_modified_date'])."\", \"".$db->dataFilter($rowStandardDefect['last_modified_by'])."\", \"".$db->dataFilter($rowStandardDefect['created_date'])."\", \"".$db->dataFilter($rowStandardDefect['created_by'])."\", \"".$db->dataFilter($rowStandardDefect['resource_type'])."\", \"".$db->dataFilter($rowStandardDefect['is_deleted'])."\", \"".$db->dataFilter($rowStandardDefect['tag'])."\"";

					}
				//$db->createFile('standard_deffects_add.txt', $iPadQueryStandardDefect, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryStandardDefect) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "standard_defects", 'global_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted,tag', $iPadQueryStandardDefect);
			}

//Data for Project Location Table			
			$iPadQueryProjectLocation = array();
			foreach($projectId as $project){
				$selProjectLocation = 'DISTINCT location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_locations WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsProjectLocation) > 0){
					while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
						$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];

						$iPadQueryProjectLocation[] = " SELECT \"".$db->dataFilter($rowProjectLocation['global_id'])."\", \"".$db->dataFilter($rowProjectLocation['global_id'])."\",\"".$db->dataFilter($rowProjectLocation['project_id'])."\", \"".$db->dataFilter($rowProjectLocation['location_parent_id'])."\", \"".$db->dataFilter($rowProjectLocation['location_title'])."\", \"".$db->dataFilter($rowProjectLocation['last_modified_date'])."\", \"".$db->dataFilter($rowProjectLocation['last_modified_by'])."\", \"".$db->dataFilter($rowProjectLocation['created_date'])."\", \"".$db->dataFilter($rowProjectLocation['created_by'])."\", \"".$db->dataFilter($rowProjectLocation['resource_type'])."\", \"".$db->dataFilter($rowProjectLocation['is_deleted'])."\"";

					}
					//$db->createFile('project_locations_add.txt', $iPadQueryProjectLocation, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryProjectLocation) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "project_locations", 'location_id, global_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $iPadQueryProjectLocation);
			}

//Data for Progress Monitoring Location Table			
			$iPadQueryProjectLocation = array();
			foreach($projectId as $project){
				$selProjectLocation = 'DISTINCT location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_monitoring_locations WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsProjectLocation) > 0){
					while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
						$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];

						$iPadQueryProjectLocation [] = " SELECT \"".$db->dataFilter($rowProjectLocation['global_id'])."\",\"".$db->dataFilter($rowProjectLocation['global_id'])."\",\"".$db->dataFilter($rowProjectLocation['project_id'])."\", \"".$db->dataFilter($rowProjectLocation['location_parent_id'])."\", \"".$db->dataFilter($rowProjectLocation['location_title'])."\", \"".$db->dataFilter($rowProjectLocation['last_modified_date'])."\", \"".$db->dataFilter($rowProjectLocation['last_modified_by'])."\", \"".$db->dataFilter($rowProjectLocation['created_date'])."\", \"".$db->dataFilter($rowProjectLocation['created_by'])."\", \"".$db->dataFilter($rowProjectLocation['resource_type'])."\", \"".$db->dataFilter($rowProjectLocation['is_deleted'])."\"";

					}
					//$db->createFile('project_monitoring_locations_add.txt', $iPadQueryProjectLocation, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryProjectLocation) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "project_monitoring_locations", 'location_id,global_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $iPadQueryProjectLocation);
			}

//Data for Progress Monitoring Table			
			$iPadQueryProgressMonitoring = array();
			foreach($projectId as $project){
				$selProgressMonitoring = 'progress_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, status, percentage';
				$rsProgressMonitoring = mysql_query("SELECT ".$selProgressMonitoring." FROM progress_monitoring WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsProgressMonitoring) > 0){
					while($rowProgressMonitoring = mysql_fetch_assoc($rsProgressMonitoring)){
						$rowProgressMonitoring['global_id'] = $rowProgressMonitoring['progress_id'];

						$iPadQueryProgressMonitoring[]  = " SELECT \"".$db->dataFilter($rowProgressMonitoring['progress_id'])."\", \"".$db->dataFilter($rowProgressMonitoring['project_id'])."\", \"".$db->dataFilter($rowProgressMonitoring['location_id'])."\", \"".$db->dataFilter($rowProgressMonitoring['sub_location_id'])."\", \"".$db->dataFilter($rowProgressMonitoring['task'])."\", \"".$db->dataFilter($rowProgressMonitoring['start_date'])."\", \"".$db->dataFilter($rowProgressMonitoring['end_date'])."\", \"".$db->dataFilter($rowProgressMonitoring['last_modified_date'])."\", \"".$db->dataFilter($rowProgressMonitoring['last_modified_by'])."\", \"".$db->dataFilter($rowProgressMonitoring['created_date'])."\", \"".$db->dataFilter($rowProgressMonitoring['created_by'])."\", \"".$db->dataFilter($rowProgressMonitoring['resource_type'])."\", \"".$db->dataFilter($rowProgressMonitoring['is_deleted'])."\", \"".$db->dataFilter($rowProgressMonitoring['status'])."\", \"".$db->dataFilter($rowProgressMonitoring['percentage'])."\"";
					}
					//$db->createFile('progress_monitoring_add.txt', $iPadQueryProgressMonitoring, EXPORTFILEPATH.'/download/');//Write File Here  
				}
			}
			if(count($iPadQueryProgressMonitoring) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "progress_monitoring", 'global_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, status, percentage', $iPadQueryProgressMonitoring);
			}

//Data for User Permissions Table			
			$iPadQueryInspectionInspectedBy = array();
			$selInspectionInspectedBy = '`id`, `user_id`, `permission_name`, `is_allow`, `created_by`, `created_date`, `last_modified_date`, `last_modified_by`, `resource_type`, `is_deleted`, `project_id`';
			$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM user_permission WHERE user_id = '".$userId."' and is_deleted=0");
			//echo "SELECT ".$selInspectionInspectedBy." FROM user_permission WHERE user_id = '".$userId."' and is_deleted=0"
			if(mysql_num_rows($rsInspectionInspectedBy) > 0){
				while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
					$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['id'];

					$iPadQueryInspectionInspectedBy[]  = " SELECT \"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['user_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['permission_name'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['is_allow'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\"";

				}
				//$db->createFile('inspection_graphics_add.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
			}
			if(count($iPadQueryInspectionInspectedBy) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "user_permission", 'global_id, user_id, permission_name, is_allow, last_modified_date, last_modified_by, created_date, created_by, project_id, resource_type, is_deleted', $iPadQueryInspectionInspectedBy);
			}

//Data for Checklist Name Table		
			$iPadQueryInspectionInspectedBy = array();
			foreach($projectId as $project){
				$selInspectionInspectedBy = 'check_list_items_id, project_id, check_list_items_name, check_list_items_tags, check_list_items_option, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM check_list_items WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['check_list_items_id'];

						$iPadQueryInspectionInspectedBy[]  = " SELECT \"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_name'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_tags'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_option'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\"";

					}
					//$db->createFile('inspection_graphics_add.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryInspectionInspectedBy) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "check_list_items", 'global_id, project_id, check_list_items_name, check_list_items_tags, check_list_items_option, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $iPadQueryInspectionInspectedBy);
			}

//Data for Issued to for Progres Monitoring Table			
			$iPadQueryIssuetoInspections = array();
			foreach($projectId as $project){
				$selIssuetoInspections = 'issued_to_progress_monitoring_id,progress_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_progress_monitoring WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_progress_monitoring_id'];

						$iPadQueryIssuetoInspections []= " SELECT \"".$db->dataFilter($rowIssuetoInspections['global_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['progress_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['resource_type'])."\", \"".$db->dataFilter($rowIssuetoInspections['project_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['is_deleted'])."\"";

					}
					//$db->createFile('issued_to_for_progress_monitoring_add.txt', $iPadQueryIssuetoInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryIssuetoInspections) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "issued_to_for_progress_monitoring", 'global_id, progress_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted', $iPadQueryIssuetoInspections);
			}

//Data for Inspection Issue Table			
			$iPadQueryInspectionIssueTo = array();
			foreach($projectId as $project){
				$selInspectionIssueTo = 'issue_to_id, project_id, issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, tag';
				$rsInspectionIssueTo = mysql_query("SELECT ".$selInspectionIssueTo." FROM inspection_issue_to WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsInspectionIssueTo) > 0){
					while($rowInspectionIssueTo = mysql_fetch_assoc($rsInspectionIssueTo)){
						$rowInspectionIssueTo['global_id'] = $rowInspectionIssueTo['issue_to_id'];

						$iPadQueryInspectionIssueTo [] = " SELECT \"".$db->dataFilter($rowInspectionIssueTo['global_id'])."\", \"".$db->dataFilter($rowInspectionIssueTo['project_id'])."\", \"".$db->dataFilter($rowInspectionIssueTo['issue_to_name'])."\", \"".$db->dataFilter($rowInspectionIssueTo['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionIssueTo['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionIssueTo['created_date'])."\", \"".$db->dataFilter($rowInspectionIssueTo['created_by'])."\", \"".$db->dataFilter($rowInspectionIssueTo['resource_type'])."\",  \"".$db->dataFilter($rowInspectionIssueTo['is_deleted'])."\",  \"".$db->dataFilter($rowInspectionIssueTo['tag'])."\"";

					}
					//$db->createFile('inspection_issue_to_add.txt', $iPadQueryInspectionIssueTo, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryInspectionIssueTo) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "inspection_issue_to", 'global_id, project_id, issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, tag', $iPadQueryInspectionIssueTo);
			}

//Update accourind to new permission Synch DATA DAted : 04*09*2012
//Data for Issued to for Inspections Table			
			foreach($projectId as $project){
				$inspId = array();
				$selIssuetoInspections = 'distinct pi.inspection_id, pi.project_id';
				if($statsRange[$project['project_id']] == "'ALL'" && $dateRange[$project['project_id']] == 100000){
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 ".$roleProjectId[$project['project_id']]);
					$inspId = array();		
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$inspId[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
						}
					}
				}else{
					if($statsRange[$project['project_id']] == "'ALL'"){
						$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date".$roleProjectId[$project['project_id']]);
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
							$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND isi.inspection_status IN (".$forAll.") AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0".$roleProjectId[$project['project_id']]);
							if(mysql_num_rows($rsIssuetoInspections) > 0){
								while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
									$inspId1[] = $db->dataFilter($rowIssuetoInspections['inspection_id']);
								}
							}
						}
						if($forStatus != ''){
							$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM project_inspections as pi, issued_to_for_inspections as isi WHERE pi.project_id = '".$project['project_id']."' AND pi.is_deleted=0 AND DATE_SUB(CURDATE(), INTERVAL ".$dateRange[$project['project_id']]." DAY) <= pi.last_modified_date AND isi.inspection_status IN (".$forStatus.") AND pi.inspection_id = isi.inspection_id AND isi.is_deleted = 0".$roleProjectId[$project['project_id']]);
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
			##$iPadQueryIssuetoInspections = array();
			foreach($projectId as $project){
				$insSelect = '';
				$whereCon = '';	
				$lupCount = count($inspectionIds[$project['project_id']]);
				for($s=0;$s<$lupCount;$s++){
					if($insSelect == ''){
						$insSelect = $inspectionIds[$project['project_id']][$s];
					}else{
						$insSelect .= ', '.$inspectionIds[$project['project_id']][$s];
					}
				}
				if($insSelect != '')
					$whereCon = 'inspection_id IN ('.$insSelect.') AND ';
				$selProjectInspections = 'DISTINCT inspection_id, project_id, location_id, inspection_date_raised, inspection_status, inspection_inspected_by, inspection_fixed_by_date, inspection_type, inspection_map_address, inspection_description, inspection_priority, inspection_notes, inspection_sign_image, inspection_location, cost_attribute, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, inspection_raised_by';
			if($whereCon != ''){
				$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections WHERE project_id = '".$project['project_id']."' AND ".$whereCon." is_deleted=0 group by inspection_id");
				if(mysql_num_rows($rsProjectInspections) > 0){
					$iPadQueryrsProjectInspections = array();
					while($rowProjectInspections = mysql_fetch_assoc($rsProjectInspections)){
						$rowProjectInspections['global_id'] = $rowProjectInspections['inspection_id'];
						$date_raised = $rowProjectInspections['inspection_date_raised'];
						if ($date_raised != ""){
							$arr = explode("-", $date_raised);
							if (isset($arr[1]))
								$date_raised = $arr[1] . "/" . $arr[2] . "/" . $arr[0];
						}
						$inspection_fixed_by_date = $rowProjectInspections['inspection_fixed_by_date'];
						if ($inspection_fixed_by_date != ""){
							$arr = explode("-", $inspection_fixed_by_date);
							if (isset($arr[1]))
								$inspection_fixed_by_date = $arr[1] . "/" . $arr[2] . "/" . $arr[0];
						}
						$iPadQueryrsProjectInspections[] = $rowProjectInspections['global_id'] . "##VALUES(\"".$db->dataFilter($rowProjectInspections['global_id'])."\",\"".$db->dataFilter($rowProjectInspections['location_id'])."\", \"".$db->dataFilter($date_raised)."\", \"".$db->dataFilter($rowProjectInspections['inspection_status'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_inspected_by'])."\", \"".$db->dataFilter($inspection_fixed_by_date)."\", \"".$db->dataFilter($rowProjectInspections['inspection_type'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_map_address'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_description'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_priority'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_notes'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_sign_image'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_location'])."\", \"".$db->dataFilter($rowProjectInspections['cost_attribute'])."\", \"".$db->dataFilter($rowProjectInspections['last_modified_date'])."\", \"".$db->dataFilter($rowProjectInspections['last_modified_by'])."\", \"".$db->dataFilter($rowProjectInspections['created_date'])."\", \"".$db->dataFilter($rowProjectInspections['created_by'])."\", \"".$db->dataFilter($rowProjectInspections['resource_type'])."\", \"".$db->dataFilter($rowProjectInspections['global_id'])."\", \"".$db->dataFilter($rowProjectInspections['is_deleted'])."\", \"".$db->dataFilter($rowProjectInspections['project_id'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_raised_by'])."\");\r\n";
					}
					$db->createFile('project_inspection_add.txt', join('', $iPadQueryrsProjectInspections), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

				$selIssuetoInspections = 'issued_to_inspections_id, project_id, inspection_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, inspection_status, cost_attribute, inspection_fixed_by_date, closed_date';
			if($whereCon != ''){	
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."' AND ".$whereCon." is_deleted=0");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					$iPadQueryIssuetoInspections = array();
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_inspections_id'];

						$iPadQueryIssuetoInspections[] = $rowIssuetoInspections['global_id'] . "##VALUES(\"".$db->dataFilter($rowIssuetoInspections['project_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['inspection_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['resource_type'])."\", \"".$db->dataFilter($rowIssuetoInspections['global_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['is_deleted'])."\", \"".$db->dataFilter($rowIssuetoInspections['inspection_fixed_by_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['cost_attribute'])."\", \"".$db->dataFilter($rowIssuetoInspections['inspection_status'])."\", \"".$db->dataFilter($rowIssuetoInspections['closed_date'])."\");\r\n";
					}
					$db->createFile('issued_to_for_inspections_add.txt', join('', $iPadQueryIssuetoInspections), EXPORTFILEPATH.'/download/');//Write File Here 
					
				}
			}
					
				$selInspectionChecklist = 'insepection_check_list_id, project_id, inspection_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
			if($whereCon != ''){
				$rsInspectionChecklist = mysql_query("SELECT ".$selInspectionChecklist." FROM inspection_check_list WHERE project_id = '".$project['project_id']."' AND ".$whereCon." is_deleted=0");
				if(mysql_num_rows($rsInspectionChecklist) > 0){
					$iPadQueryInspectionChecklist = array();
					while($rowInspectionChecklist = mysql_fetch_assoc($rsInspectionChecklist)){
						$rowInspectionChecklist['global_id'] = $rowInspectionChecklist['insepection_check_list_id'];

						$iPadQueryInspectionChecklist[] = $rowInspectionChecklist['global_id'] . "##VALUES(\"".$db->dataFilter($rowInspectionChecklist['project_id'])."\", \"".$db->dataFilter($rowInspectionChecklist['inspection_id'])."\", \"".$db->dataFilter($rowInspectionChecklist['check_list_items_id'])."\", \"".$db->dataFilter($rowInspectionChecklist['check_list_items_status'])."\", \"".$db->dataFilter($rowInspectionChecklist['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionChecklist['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionChecklist['created_date'])."\", \"".$db->dataFilter($rowInspectionChecklist['created_by'])."\", \"".$db->dataFilter($rowInspectionChecklist['resource_type'])."\", \"".$db->dataFilter($rowInspectionChecklist['global_id'])."\", \"".$db->dataFilter($rowInspectionChecklist['is_deleted'])."\");\r\n";

					}
					$db->createFile('inspection_check_list_add.txt', join('', $iPadQueryInspectionChecklist), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
				
				$selInspectionGraphic = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				if($whereCon != ''){
					$rsInspectionGraphic = mysql_query("SELECT ".$selInspectionGraphic." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND ".$whereCon." is_deleted=0");
					if(mysql_num_rows($rsInspectionGraphic) > 0){
						$iPadQueryInspectionGraphic = array();
						while($rowInspectionGraphic = mysql_fetch_assoc($rsInspectionGraphic)){
							$rowInspectionGraphic['global_id'] = $rowInspectionGraphic['graphic_id'];
	
							$iPadQueryInspectionGraphic[] = $rowInspectionGraphic['global_id'] . "##VALUES(\"".$db->dataFilter($rowInspectionGraphic['inspection_id'])."\", \"".$db->dataFilter($rowInspectionGraphic['graphic_type'])."\", \"".$db->dataFilter($rowInspectionGraphic['graphic_title'])."\", \"".$db->dataFilter($rowInspectionGraphic['graphic_name'])."\", \"".$db->dataFilter($rowInspectionGraphic['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionGraphic['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionGraphic['created_date'])."\", \"".$db->dataFilter($rowInspectionGraphic['created_by'])."\", \"".$db->dataFilter($rowInspectionGraphic['resource_type'])."\", \"".$db->dataFilter($rowInspectionGraphic['global_id'])."\", \"".$db->dataFilter($rowInspectionGraphic['project_id'])."\", \"".$db->dataFilter($rowInspectionGraphic['is_deleted'])."\");\r\n";
	
						}
						$db->createFile('inspection_graphics_add.txt', join('', $iPadQueryInspectionGraphic), EXPORTFILEPATH.'/download/');//Write File Here 
					}
				}
			}
			
			$pmChecklistData = array();
			foreach($projectId as $project){
				$selIssuetoInspections = 'progress_monitoring_check_list_id, project_id, progress_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM progress_monitoring_check_list WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['progress_monitoring_check_list_id'];

						$pmChecklistData []= " SELECT \"".$db->dataFilter($rowIssuetoInspections['global_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['project_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['progress_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['check_list_items_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['check_list_items_status'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['resource_type'])."\", \"".$db->dataFilter($rowIssuetoInspections['is_deleted'])."\"";

					}
					//$db->createFile('issued_to_for_progress_monitoring_add.txt', $iPadQueryIssuetoInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($pmChecklistData) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "progress_monitoring_check_list", 'global_id, project_id, progress_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $pmChecklistData);
			}

			$locationChecklistData = array();
			foreach($projectId as $project){
				$selIssuetoInspections = 'location_check_list_id, project_id, location_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM location_check_list WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['location_check_list_id'];

						$locationChecklistData []= " SELECT \"".$db->dataFilter($rowIssuetoInspections['global_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['project_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['location_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['check_list_items_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['check_list_items_status'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['resource_type'])."\", \"".$db->dataFilter($rowIssuetoInspections['is_deleted'])."\"";

					}
					//$db->createFile('issued_to_for_progress_monitoring_add.txt', $iPadQueryIssuetoInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($locationChecklistData) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "location_check_list", 'global_id, project_id, location_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $locationChecklistData);
			}		
		
//Data for Drawing Manatemnet Table			
			$iPadQueryDrawingManatement = array();
			foreach($projectId as $project){
				$selDrawingManatement = 'DISTINCT draw_mgmt_images_id, project_id, draw_mgmt_images_title, draw_mgmt_images_name, draw_mgmt_images_thumbnail, draw_mgmt_images_description, draw_mgmt_images_tags, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				
				$rsDrawingManatement = mysql_query("SELECT ".$selDrawingManatement." FROM draw_mgmt_images WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsDrawingManatement) > 0){
					while($rowDrawingManatement = mysql_fetch_assoc($rsDrawingManatement)){
						$rowDrawingManatement['global_id'] = $rowDrawingManatement['draw_mgmt_images_id'];

						$iPadQueryDrawingManatement[] = " SELECT \"".$db->dataFilter($rowDrawingManatement['global_id'])."\",\"".$db->dataFilter($rowDrawingManatement['project_id'])."\", \"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_title'])."\", \"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_name'])."\",\"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_thumbnail'])."\", \"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_description'])."\", \"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_tags'])."\", \"".$db->dataFilter($rowDrawingManatement['last_modified_date'])."\", \"".$db->dataFilter($rowDrawingManatement['last_modified_by'])."\", \"".$db->dataFilter($rowDrawingManatement['created_date'])."\", \"".$db->dataFilter($rowDrawingManatement['created_by'])."\", \"".$db->dataFilter($rowDrawingManatement['resource_type'])."\", \"".$db->dataFilter($rowDrawingManatement['is_deleted'])."\"";
					
					}
					//$db->createFile('project_locations_add.txt', $iPadQueryProjectLocation, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			
			if(count($iPadQueryDrawingManatement) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "draw_mgmt_images", 'global_id, project_id, draw_mgmt_images_title, draw_mgmt_images_name, draw_mgmt_images_thumbnail, draw_mgmt_images_description, draw_mgmt_images_tags, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $iPadQueryDrawingManatement);
			}

		}else{//Modified Date is comes
			
//Data for add User Project Table
			$new_project = array();
			$selUserProject = 'DISTINCT project_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, user_role';
			$rsUserProject = mysql_query("SELECT ".$selUserProject." FROM user_projects WHERE user_id = '".$_REQUEST['userId']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
			if(mysql_num_rows($rsUserProject) > 0){
				$iPadQueryUserProject = array();
				while($rowUserProject = mysql_fetch_assoc($rsUserProject)){
					#$rowUserProject['user_id'] = $userId;
					$rowUserProject['global_id'] = $rowUserProject['project_id'];
					$new_project[$rowUserProject['global_id']] = 1;
					$iPadQueryUserProject[] = $rowUserProject['global_id']."##VALUES(\"".$db->dataFilter($rowUserProject['global_id'])."\",\"".$db->dataFilter($rowUserProject['user_id'])."\", \"".$db->dataFilter($rowUserProject['project_name'])."\", \"".$db->dataFilter($rowUserProject['project_type'])."\", \"".$db->dataFilter($rowUserProject['project_address_line1'])."\", \"".$db->dataFilter($rowUserProject['project_address_line2'])."\", \"".$db->dataFilter($rowUserProject['project_suburb'])."\", \"".$db->dataFilter($rowUserProject['project_state'])."\", \"".$db->dataFilter($rowUserProject['project_postcode'])."\", \"".$db->dataFilter($rowUserProject['project_country'])."\", \"".$db->dataFilter($rowUserProject['last_modified_date'])."\", \"".$db->dataFilter($rowUserProject['last_modified_by'])."\", \"".$db->dataFilter($rowUserProject['created_date'])."\", \"".$db->dataFilter($rowUserProject['created_by'])."\", \"".$db->dataFilter($rowUserProject['resource_type'])."\", \"".$db->dataFilter($rowUserProject['global_id'])."\", \"".$db->dataFilter($rowUserProject['is_deleted'])."\", \"0\", \"".$db->dataFilter($rowUserProject['user_role'])."\");\r\n";
				}
				$db->createFile('user_projects_add.txt', join('', $iPadQueryUserProject), EXPORTFILEPATH.'/download/');//Write File Here 
			}

//Data for UpdateQuery User Project Table
			$selUserProject = 'DISTINCT project_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, user_role';
			$rsUserProject = mysql_query("SELECT ".$selUserProject." FROM user_projects WHERE user_id = '".$_REQUEST['userId']."' and last_modified_date >= '".$lastModifiedDate."'");
			if(mysql_num_rows($rsUserProject) > 0){
				$iPadQueryUserProject = array();
				while($rowUserProject = mysql_fetch_assoc($rsUserProject)){
					#$rowUserProject['user_id'] = $userId;
					$rowUserProject['global_id'] = $rowUserProject['project_id'];

					$iPadQueryUserProject[] = "update user_projects set project_name=\"".$db->dataFilter($rowUserProject['project_name'])."\", project_type=\"".$db->dataFilter($rowUserProject['project_type'])."\", project_address_line1=\"".$db->dataFilter($rowUserProject['project_address_line1'])."\", project_address_line2=\"".$db->dataFilter($rowUserProject['project_address_line2'])."\", project_suburb=\"".$db->dataFilter($rowUserProject['project_suburb'])."\", project_state=\"".$db->dataFilter($rowUserProject['project_state'])."\", project_postcode=\"".$db->dataFilter($rowUserProject['project_postcode'])."\", project_country=\"".$db->dataFilter($rowUserProject['project_country'])."\", last_modified_date=\"".$db->dataFilter($rowUserProject['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowUserProject['last_modified_by'])."\", resource_type=\"".$db->dataFilter($rowUserProject['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowUserProject['is_deleted'])."\", user_role=\"".$db->dataFilter($rowUserProject['user_role'])."\" where global_id=".$rowUserProject['global_id'].";\r\n";

				}
				$db->createFile('user_projects_update.txt', join('', $iPadQueryUserProject), EXPORTFILEPATH.'/download/');//Write File Here 
			}

//Data for add Standard Defect Table
#			$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			$projectId = array();
			$pIDS = explode(',', $projectIDs);
			for($k=0;$k<count($pIDS);$k++){
				$projectId[] = array('project_id' => $pIDS[$k]);
			}
			
			foreach($projectId as $project){
				$selStandardDefect = 'standard_defect_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted,tag';
				$rsStandardDefect = mysql_query("SELECT ".$selStandardDefect." FROM standard_defects WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if ($new_project[$project['project_id']] == 1)
				{
					$rsStandardDefect = mysql_query("SELECT ".$selStandardDefect." FROM standard_defects WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				}
				if(mysql_num_rows($rsStandardDefect) > 0){
					$iPadQueryStandardDefect = array();
					while($rowStandardDefect = mysql_fetch_assoc($rsStandardDefect)){
						$rowStandardDefect['global_id'] = $rowStandardDefect['standard_defect_id'];

						$iPadQueryStandardDefect[] = $rowStandardDefect['global_id']."##VALUES(\"".$db->dataFilter($rowStandardDefect['project_id'])."\", \"".$db->dataFilter($rowStandardDefect['description'])."\", \"".$db->dataFilter($rowStandardDefect['last_modified_date'])."\", \"".$db->dataFilter($rowStandardDefect['last_modified_by'])."\", \"".$db->dataFilter($rowStandardDefect['created_date'])."\", \"".$db->dataFilter($rowStandardDefect['created_by'])."\", \"".$db->dataFilter($rowStandardDefect['resource_type'])."\", \"".$db->dataFilter($rowStandardDefect['global_id'])."\", \"".$db->dataFilter($rowStandardDefect['is_deleted'])."\", \"".$db->dataFilter($rowStandardDefect['tag'])."\");\r\n";

					}
					$db->createFile('standard_deffects_add.txt', join('', $iPadQueryStandardDefect), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Standard Defect Table
			#$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']]))
				{
					$selStandardDefect = 'standard_defect_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, tag';
					$rsStandardDefect = mysql_query("SELECT ".$selStandardDefect." FROM standard_defects WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
					if(mysql_num_rows($rsStandardDefect) > 0){
						$iPadQueryStandardDefect = array();
						while($rowStandardDefect = mysql_fetch_assoc($rsStandardDefect)){
							$rowStandardDefect['global_id'] = $rowStandardDefect['standard_defect_id'];
	
							$iPadQueryStandardDefect[] = "update standard_defects set description=\"".$db->dataFilter($rowStandardDefect['description'])."\", last_modified_date=\"".$db->dataFilter($rowStandardDefect['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowStandardDefect['last_modified_by'])."\", resource_type=\"".$db->dataFilter($rowStandardDefect['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowStandardDefect['is_deleted'])."\", tag=\"".$db->dataFilter($rowStandardDefect['tag'])."\" where global_id=\"".$db->dataFilter($rowStandardDefect['global_id'])."\";\r\n";
	
						}
						$db->createFile('standard_deffects_update.txt', join('', $iPadQueryStandardDefect), EXPORTFILEPATH.'/download/');//Write File Here 
					}
				}
			}

//Data for add Project Location Table			
			foreach($projectId as $project){
				$selProjectLocation = 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_locations WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if ($new_project[$project['project_id']] == 1)
				{
					$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_locations WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				}
				if(mysql_num_rows($rsProjectLocation) > 0){
					$iPadQueryProjectLocation = array();
					while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
						$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];

						$iPadQueryProjectLocation[] = $rowProjectLocation['global_id'] . "##VALUES(\"".$db->dataFilter($rowProjectLocation['global_id'])."\",\"".$db->dataFilter($rowProjectLocation['project_id'])."\", \"".$db->dataFilter($rowProjectLocation['location_parent_id'])."\", \"".$db->dataFilter($rowProjectLocation['location_title'])."\", \"".$db->dataFilter($rowProjectLocation['last_modified_date'])."\", \"".$db->dataFilter($rowProjectLocation['last_modified_by'])."\", \"".$db->dataFilter($rowProjectLocation['created_date'])."\", \"".$db->dataFilter($rowProjectLocation['created_by'])."\", \"".$db->dataFilter($rowProjectLocation['resource_type'])."\", \"".$db->dataFilter($rowProjectLocation['global_id'])."\", \"".$db->dataFilter($rowProjectLocation['is_deleted'])."\");\r\n";

					}
					$db->createFile('project_locations_add.txt', join('', $iPadQueryProjectLocation), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Project Location Table			
			foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']])){
					$selProjectLocation = 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
					$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_locations WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
					if(mysql_num_rows($rsProjectLocation) > 0){
						$iPadQueryProjectLocation = array();
						while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
							$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];
	
							$iPadQueryProjectLocation[] = "update project_locations set location_parent_id=\"".$db->dataFilter($rowProjectLocation['location_parent_id'])."\", location_title=\"".$db->dataFilter($rowProjectLocation['location_title'])."\", last_modified_date=\"".$db->dataFilter($rowProjectLocation['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowProjectLocation['last_modified_by'])."\", resource_type=\"".$db->dataFilter($rowProjectLocation['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowProjectLocation['is_deleted'])."\" where global_id=\"".$db->dataFilter($rowProjectLocation['global_id'])."\";\r\n";
	
						}
						$db->createFile('project_locations_update.txt', join('', $iPadQueryProjectLocation), EXPORTFILEPATH.'/download/');//Write File Here 
					}
				}
			}

//Data for add Progress Monitoring Location Table			
			foreach($projectId as $project){
				$selProjectLocation = 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_monitoring_locations WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if ($new_project[$project['project_id']] == 1)
				{
					$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_monitoring_locations WHERE project_id = '".$project['project_id']."'and is_deleted=0");
				}
				if(mysql_num_rows($rsProjectLocation) > 0){
					$iPadQueryProjectLocation = array();
					while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
						$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];

						$iPadQueryProjectLocation[] = $rowProjectLocation['global_id'] . "##VALUES(\"".$db->dataFilter($rowProjectLocation['global_id'])."\",\"".$db->dataFilter($rowProjectLocation['project_id'])."\", \"".$db->dataFilter($rowProjectLocation['location_parent_id'])."\", \"".$db->dataFilter($rowProjectLocation['location_title'])."\", \"".$db->dataFilter($rowProjectLocation['last_modified_date'])."\", \"".$db->dataFilter($rowProjectLocation['last_modified_by'])."\", \"".$db->dataFilter($rowProjectLocation['created_date'])."\", \"".$db->dataFilter($rowProjectLocation['created_by'])."\", \"".$db->dataFilter($rowProjectLocation['resource_type'])."\", \"".$db->dataFilter($rowProjectLocation['global_id'])."\", \"".$db->dataFilter($rowProjectLocation['is_deleted'])."\");\r\n";

					}
					$db->createFile('project_monitoring_locations_add.txt', join('', $iPadQueryProjectLocation), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Project Location Table			
			foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']]))
				{
					$selProjectLocation = 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
					$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_monitoring_locations WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
					if(mysql_num_rows($rsProjectLocation) > 0){
						$iPadQueryProjectLocation = array();
						while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
							$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];
	
							$iPadQueryProjectLocation[] = "update project_monitoring_locations set location_parent_id=\"".$db->dataFilter($rowProjectLocation['location_parent_id'])."\", location_title=\"".$db->dataFilter($rowProjectLocation['location_title'])."\", last_modified_date=\"".$db->dataFilter($rowProjectLocation['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowProjectLocation['last_modified_by'])."\", resource_type=\"".$db->dataFilter($rowProjectLocation['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowProjectLocation['is_deleted'])."\" where global_id=\"".$db->dataFilter($rowProjectLocation['global_id'])."\";\r\n";
	
						}
						$db->createFile('project_monitoring_locations_update.txt', join('', $iPadQueryProjectLocation), EXPORTFILEPATH.'/download/');//Write File Here 
					}
				}
			}

//Data for add Project Inspections Table
			$curTime = $db->selQRY("MAX(created_date) as curTime", "importData", "userid = ".$userId." AND device = '".$deviceType."' AND importDataType = 'tableData'");
			$subQuery = "SELECT inspection_id FROM project_inspections WHERE last_modified_by = ".$userId." AND resource_type = '".$deviceType."' AND last_modified_date BETWEEN '".$curTime['curTime']."' AND ('".$curTime['curTime']."' + INTERVAL 1 MINUTE)";
			$inspArray = array();
			foreach($projectId as $project){
				$inspList4Role = '';
				
				$selProjectInspections = 'inspection_id, project_id, location_id, inspection_date_raised, inspection_status, inspection_inspected_by, inspection_fixed_by_date, inspection_type, inspection_map_address, inspection_description, inspection_priority, inspection_notes, inspection_sign_image, inspection_location, inspection_raised_by, cost_attribute, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
			
				$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and is_deleted=0".$roleProjectId[$project['project_id']]." AND inspection_id NOT IN(".$subQuery.")");
				
#." AND inspection_id NOT IN(".$subQuery.")"
				if ($new_project[$project['project_id']] == 1){
					$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections WHERE project_id = '".$project['project_id']."'and is_deleted=0".$roleProjectId[$project['project_id']]);
				}
				if(mysql_num_rows($rsProjectInspections) > 0){
					$iPadQueryrsProjectInspections = array();
					while($rowProjectInspections = mysql_fetch_assoc($rsProjectInspections)){
						if($inspList4Role == ''){
							$inspList4Role = $rowProjectInspections['inspection_id'];
						}else{
							$inspList4Role .= ', '.$rowProjectInspections['inspection_id'];
						}
					
						$rowProjectInspections['global_id'] = $rowProjectInspections['inspection_id'];
						$date_raised = $rowProjectInspections['inspection_date_raised'];
						if ($date_raised != ""){
							$arr = explode("-", $date_raised);
							if (isset($arr[1]))
								$date_raised = $arr[1] . "/" . $arr[2] . "/" . $arr[0];
						}
						$inspection_fixed_by_date = $rowProjectInspections['inspection_fixed_by_date'];
						if ($inspection_fixed_by_date != ""){
							$arr = explode("-", $inspection_fixed_by_date);
							if (isset($arr[1]))
								$inspection_fixed_by_date = $arr[1] . "/" . $arr[2] . "/" . $arr[0];
						}

						$iPadQueryrsProjectInspections[] = $rowProjectInspections['global_id'] . "##VALUES(\"".$db->dataFilter($rowProjectInspections['global_id'])."\",\"".$db->dataFilter($rowProjectInspections['location_id'])."\", \"".$db->dataFilter($date_raised)."\", \"".$db->dataFilter($rowProjectInspections['inspection_status'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_inspected_by'])."\", \"".$db->dataFilter($inspection_fixed_by_date)."\", \"".$db->dataFilter($rowProjectInspections['inspection_type'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_map_address'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_description'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_priority'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_notes'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_sign_image'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_location'])."\", \"".$db->dataFilter($rowProjectInspections['cost_attribute'])."\", \"".$db->dataFilter($rowProjectInspections['last_modified_date'])."\", \"".$db->dataFilter($rowProjectInspections['last_modified_by'])."\", \"".$db->dataFilter($rowProjectInspections['created_date'])."\", \"".$db->dataFilter($rowProjectInspections['created_by'])."\", \"".$db->dataFilter($rowProjectInspections['resource_type'])."\", \"".$db->dataFilter($rowProjectInspections['global_id'])."\", \"".$db->dataFilter($rowProjectInspections['is_deleted'])."\", \"".$db->dataFilter($rowProjectInspections['project_id'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_raised_by'])."\");\r\n";

					}
					$db->createFile('project_inspection_add.txt', join('', $iPadQueryrsProjectInspections), EXPORTFILEPATH.'/download/');//Write File Here 
				}
				$inspArray[$project['project_id']] = $inspList4Role;
			}
//Data for UpdateQuery Project Inspections Table			
		/*	foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']]))
				{
					$selProjectInspections = 'inspection_id, project_id, location_id, inspection_date_raised, inspection_status, inspection_inspected_by, inspection_fixed_by_date, inspection_type, inspection_map_address, inspection_description, inspection_priority, inspection_notes, inspection_sign_image, inspection_location, inspection_raised_by, cost_attribute, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
					$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
					if(mysql_num_rows($rsProjectInspections) > 0){
						$iPadQueryrsProjectInspections = '';
						while($rowProjectInspections = mysql_fetch_assoc($rsProjectInspections)){
							$rowProjectInspections['global_id'] = $rowProjectInspections['inspection_id'];
							$date_raised = $rowProjectInspections['inspection_date_raised'];
							if ($date_raised != ""){
								$arr = explode("-", $date_raised);
								if (isset($arr[1]))
									$date_raised = $arr[1] . "/" . $arr[2] . "/" . $arr[0];
							}
							$inspection_fixed_by_date = $rowProjectInspections['inspection_fixed_by_date'];
							if ($inspection_fixed_by_date != ""){
								$arr = explode("-", $inspection_fixed_by_date);
								if (isset($arr[1]))
									$inspection_fixed_by_date = $arr[1] . "/" . $arr[2] . "/" . $arr[0];
							}
	
							$iPadQueryrsProjectInspections  .= "update project_inspections set location_id=\"".$db->dataFilter($rowProjectInspections['location_id'])."\", inspection_date_raised=\"".$db->dataFilter($date_raised)."\", inspection_status=\"".$db->dataFilter($rowProjectInspections['inspection_status'])."\", inspection_inspected_by=\"".$db->dataFilter($rowProjectInspections['inspection_inspected_by'])."\", inspection_fixed_by_date=\"".$db->dataFilter($inspection_fixed_by_date)."\", inspection_type=\"".$db->dataFilter($rowProjectInspections['inspection_type'])."\", inspection_map_address=\"".$db->dataFilter($rowProjectInspections['inspection_map_address'])."\", inspection_description=\"".$db->dataFilter($rowProjectInspections['inspection_description'])."\", inspection_priority=\"".$db->dataFilter($rowProjectInspections['inspection_priority'])."\", inspection_notes=\"".$db->dataFilter($rowProjectInspections['inspection_notes'])."\", inspection_sign_image=\"".$db->dataFilter($rowProjectInspections['inspection_sign_image'])."\", inspection_location=\"".$db->dataFilter($rowProjectInspections['inspection_location'])."\", inspection_raised_by=\"".$db->dataFilter($rowProjectInspections['inspection_raised_by'])."\", cost_attribute=\"".$db->dataFilter($rowProjectInspections['cost_attribute'])."\", last_modified_date=\"".$db->dataFilter($rowProjectInspections['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowProjectInspections['last_modified_by'])."\", created_date=\"".$db->dataFilter($rowProjectInspections['created_date'])."\", created_by=\"".$db->dataFilter($rowProjectInspections['created_by'])."\", resource_type=\"".$db->dataFilter($rowProjectInspections['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowProjectInspections['is_deleted'])."\", project_id=\"".$db->dataFilter($rowProjectInspections['project_id'])."\" where global_id=\"".$db->dataFilter($rowProjectInspections['global_id'])."\";\r\n";
						}
						$db->createFile('project_inspection_update.txt', $iPadQueryrsProjectInspections, EXPORTFILEPATH.'/download/');//Write File Here 
					}
				}
			}*/

//Data for add Progress Monitoring Table			
			foreach($projectId as $project){
				$selProgressMonitoring = 'progress_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, status, percentage';
				$rsProgressMonitoring = mysql_query("SELECT ".$selProgressMonitoring." FROM progress_monitoring WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if ($new_project[$project['project_id']] == 1)
				{
					$rsProgressMonitoring = mysql_query("SELECT ".$selProgressMonitoring." FROM progress_monitoring WHERE project_id = '".$project['project_id']."'and is_deleted=0");
				}
				if(mysql_num_rows($rsProgressMonitoring) > 0){
					$iPadQueryProgressMonitoring = array();
					while($rowProgressMonitoring = mysql_fetch_assoc($rsProgressMonitoring)){
						$rowProgressMonitoring['global_id'] = $rowProgressMonitoring['progress_id'];

						$iPadQueryProgressMonitoring[] = $rowProgressMonitoring['global_id'] . "##VALUES(\"".$db->dataFilter($rowProgressMonitoring['project_id'])."\", \"".$db->dataFilter($rowProgressMonitoring['location_id'])."\", \"".$db->dataFilter($rowProgressMonitoring['sub_location_id'])."\", \"".$db->dataFilter($rowProgressMonitoring['task'])."\", \"".$db->dataFilter($rowProgressMonitoring['start_date'])."\", \"".$db->dataFilter($rowProgressMonitoring['end_date'])."\", \"".$db->dataFilter($rowProgressMonitoring['last_modified_date'])."\", \"".$db->dataFilter($rowProgressMonitoring['last_modified_by'])."\", \"".$db->dataFilter($rowProgressMonitoring['created_date'])."\", \"".$db->dataFilter($rowProgressMonitoring['created_by'])."\", \"".$db->dataFilter($rowProgressMonitoring['resource_type'])."\", \"".$db->dataFilter($rowProgressMonitoring['global_id'])."\", \"".$db->dataFilter($rowProgressMonitoring['is_deleted'])."\", \"".$db->dataFilter($rowProgressMonitoring['status'])."\", \"".$db->dataFilter($rowProgressMonitoring['percentage'])."\");\r\n";

					}
					$db->createFile('progress_monitoring_add.txt', join('', $iPadQueryProgressMonitoring), EXPORTFILEPATH.'/download/');//Write File Here  
				}
			}

//Data for UpdateQuery Progress Monitoring Table			
			foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']]))
				{
					$selProgressMonitoring = 'progress_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, status, percentage';
					$rsProgressMonitoring = mysql_query("SELECT ".$selProgressMonitoring." FROM progress_monitoring WHERE project_id = '".$project['project_id']."' and last_modified_date >=\"".$lastModifiedDate."\" and last_modified_date!=created_date");
					if(mysql_num_rows($rsProgressMonitoring) > 0){
						$iPadQueryProgressMonitoring = array();
						while($rowProgressMonitoring = mysql_fetch_assoc($rsProgressMonitoring)){
							$rowProgressMonitoring['global_id'] = $rowProgressMonitoring['progress_id'];
	
							$iPadQueryProgressMonitoring[] = "update progress_monitoring set location_id=\"".$db->dataFilter($rowProgressMonitoring['location_id'])."\", sub_location_id=\"".$db->dataFilter($rowProgressMonitoring['sub_location_id'])."\", task=\"".$db->dataFilter($rowProgressMonitoring['task'])."\", start_date=\"".$db->dataFilter($rowProgressMonitoring['start_date'])."\", end_date=\"".$db->dataFilter($rowProgressMonitoring['end_date'])."\", last_modified_date=\"".$db->dataFilter($rowProgressMonitoring['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowProgressMonitoring['last_modified_by'])."\", resource_type=\"".$db->dataFilter($rowProgressMonitoring['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowProgressMonitoring['is_deleted'])."\", status=\"".$db->dataFilter($rowProgressMonitoring['status'])."\", percentage=\"".$db->dataFilter($rowProgressMonitoring['percentage'])."\" where global_id=\"".$db->dataFilter($rowProgressMonitoring['global_id'])."\";\r\n";
	
						}
						$db->createFile('progress_monitoring_update.txt', join('', $iPadQueryProgressMonitoring), EXPORTFILEPATH.'/download/');//Write File Here 
					}
				}
			}

//Data for add Progress Monitoring Update Table			
			foreach($projectId as $project){
				$selProgressMonitoringUpdate = 'update_id, project_id, progress_id, percentage, status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProgressMonitoringUpdate = mysql_query("SELECT ".$selProgressMonitoringUpdate." FROM progress_monitoring_update WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if ($new_project[$project['project_id']] == 1)
				{
					$rsProgressMonitoringUpdate = mysql_query("SELECT ".$selProgressMonitoringUpdate." FROM progress_monitoring_update WHERE project_id = '".$project['project_id']."'and is_deleted=0");
				}
				if(mysql_num_rows($rsProgressMonitoringUpdate) > 0){
					$iPadQueryProgressMonitoringUpdate = array();
					while($rowProgressMonitoringUpdate = mysql_fetch_assoc($rsProgressMonitoringUpdate)){
						$rowProgressMonitoringUpdate['global_id'] = $rowProgressMonitoringUpdate['update_id'];

						$iPadQueryProgressMonitoringUpdate[] = $rowProgressMonitoringUpdate['global_id'] . "##VALUES(\"".$db->dataFilter($rowProgressMonitoringUpdate['progress_id'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['project_id'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['percentage'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['status'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['last_modified_date'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['last_modified_by'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['created_date'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['created_by'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['resource_type'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['global_id'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['is_deleted'])."\");\r\n";

					}
					$db->createFile('progress_monitoring_updates_add.txt', join('', $iPadQueryProgressMonitoringUpdate), EXPORTFILEPATH.'/download/');//Write File Here 
					#echo $iPadQueryProgressMonitoringUpdate; 
				}
			}

//Data for UpdateQuery Progress Monitoring Update Table			
			foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']]))
				{
					$selProgressMonitoringUpdate = 'update_id, project_id, progress_id, percentage, status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
					$rsProgressMonitoringUpdate = mysql_query("SELECT ".$selProgressMonitoringUpdate." FROM progress_monitoring_update WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
					if(mysql_num_rows($rsProgressMonitoringUpdate) > 0){
						$iPadQueryProgressMonitoringUpdate = array();
						while($rowProgressMonitoringUpdate = mysql_fetch_assoc($rsProgressMonitoringUpdate)){
							$rowProgressMonitoringUpdate['global_id'] = $rowProgressMonitoringUpdate['update_id'];
	
							$iPadQueryProgressMonitoringUpdate[] = $rowProgressMonitoringUpdate['global_id'] . "##VALUES(\"".$db->dataFilter($rowProgressMonitoringUpdate['progress_id'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['project_id'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['percentage'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['status'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['last_modified_date'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['last_modified_by'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['created_date'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['created_by'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['resource_type'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['global_id'])."\", \"".$db->dataFilter($rowProgressMonitoringUpdate['is_deleted'])."\");\r\n";
	
						}
						$db->createFile('progress_monitoring_updates_upadte.txt', join('', $iPadQueryProgressMonitoringUpdate), EXPORTFILEPATH.'/download/');//Write File Here 
	#					echo $iPadQueryProgressMonitoringUpdate; 
					}
				}
			}

//Data for Issued to for Progres Monitoring Table			
			foreach($projectId as $project){
				$selIssuetoInspections = 'issued_to_progress_monitoring_id,progress_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_progress_monitoring WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if ($new_project[$project['project_id']] == 1)
				{
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_progress_monitoring WHERE project_id = '".$project['project_id']."'and is_deleted=0");
				}
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					$iPadQueryIssuetoInspections = array();
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_progress_monitoring_id'];

						$iPadQueryIssuetoInspections[] = $rowIssuetoInspections['global_id'] . "##VALUES(\"".$db->dataFilter($rowIssuetoInspections['progress_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['resource_type'])."\", \"".$db->dataFilter($rowIssuetoInspections['project_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['is_deleted'])."\", \"".$db->dataFilter($rowIssuetoInspections['global_id'])."\");\r\n";

					}
					$db->createFile('issued_to_for_progress_monitoring_add.txt', join('', $iPadQueryIssuetoInspections), EXPORTFILEPATH.'/download/');//Write File Here 
				}
				#echo $iPadQueryIssuetoInspections;
			}

//Data for UpdateQuery Issued to for Inspections Table			
			foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']]))
				{
					$selIssuetoInspections = 'issued_to_progress_monitoring_id,progress_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted';
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_progress_monitoring WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and created_date!=last_modified_date");
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						$iPadQueryIssuetoInspections = array();
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_progress_monitoring_id'];
	
							$iPadQueryIssuetoInspections[] = "update issued_to_for_progress_monitoring set progress_id=\"".$db->dataFilter($rowIssuetoInspections['progress_id'])."\", issued_to_name=\"".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."\", last_modified_date=\"".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."\", created_date=\"".$db->dataFilter($rowIssuetoInspections['created_date'])."\", created_by=\"".$db->dataFilter($rowIssuetoInspections['created_by'])."\", resource_type=\"".$db->dataFilter($rowIssuetoInspections['resource_type'])."\", project_id=\"".$db->dataFilter($rowIssuetoInspections['project_id'])."\", is_deleted=\"".$db->dataFilter($rowIssuetoInspections['is_deleted'])."\" where global_id=\"".$db->dataFilter($rowIssuetoInspections['global_id'])."\";\r\n";
	
						}
						$db->createFile('issued_to_for_progress_monitoring_update.txt', join('', $iPadQueryIssuetoInspections), EXPORTFILEPATH.'/download/');//Write File Here 
					}
					#echo $iPadQueryIssuetoInspections;
				}
			}

//Data for add Inspection Issue Table			
			foreach($projectId as $project){
				$selInspectionIssueTo = 'issue_to_id, project_id, issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, tag';
				$rsInspectionIssueTo = mysql_query("SELECT ".$selInspectionIssueTo." FROM inspection_issue_to WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if ($new_project[$project['project_id']] == 1)
				{
					$rsInspectionIssueTo = mysql_query("SELECT ".$selInspectionIssueTo." FROM inspection_issue_to WHERE project_id = '".$project['project_id']."'and is_deleted=0");
				}
				if(mysql_num_rows($rsInspectionIssueTo) > 0){
					$iPadQueryInspectionIssueTo = array();
					while($rowInspectionIssueTo = mysql_fetch_assoc($rsInspectionIssueTo)){
						$rowInspectionIssueTo['global_id'] = $rowInspectionIssueTo['issue_to_id'];

						$iPadQueryInspectionIssueTo[] = $rowInspectionIssueTo['global_id'] . "##VALUES(\"".$db->dataFilter($rowInspectionIssueTo['issue_to_name'])."\", \"".$db->dataFilter($rowInspectionIssueTo['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionIssueTo['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionIssueTo['created_date'])."\", \"".$db->dataFilter($rowInspectionIssueTo['created_by'])."\", \"".$db->dataFilter($rowInspectionIssueTo['resource_type'])."\", \"".$db->dataFilter($rowInspectionIssueTo['global_id'])."\", \"".$db->dataFilter($rowInspectionIssueTo['project_id'])."\", \"".$db->dataFilter($rowInspectionIssueTo['is_deleted'])."\", \"".$db->dataFilter($rowInspectionIssueTo['tag'])."\");\r\n";

					}
					$db->createFile('inspection_issue_to_add.txt', join('', $iPadQueryInspectionIssueTo), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Inspection Issue Table			
			foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']]))
				{
					$selInspectionIssueTo = 'issue_to_id, project_id, issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, tag';
					$rsInspectionIssueTo = mysql_query("SELECT ".$selInspectionIssueTo." FROM inspection_issue_to WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
					if(mysql_num_rows($rsInspectionIssueTo) > 0){
						$iPadQueryInspectionIssueTo = array();
						while($rowInspectionIssueTo = mysql_fetch_assoc($rsInspectionIssueTo)){
							$rowInspectionIssueTo['global_id'] = $rowInspectionIssueTo['issue_to_id'];
	
							$iPadQueryInspectionIssueTo[] = "update inspection_issue_to set issue_to_name=\"".$db->dataFilter($rowInspectionIssueTo['issue_to_name'])."\", last_modified_date=\"".$db->dataFilter($rowInspectionIssueTo['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowInspectionIssueTo['last_modified_by'])."\", resource_type=\"".$db->dataFilter($rowInspectionIssueTo['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowInspectionIssueTo['is_deleted'])."\", tag=\"".$db->dataFilter($rowInspectionIssueTo['tag'])."\" where global_id=\"".$db->dataFilter($rowInspectionIssueTo['global_id'])."\";\r\n";
	
						}
						$db->createFile('inspection_issue_to_update.txt', join('', $iPadQueryInspectionIssueTo), EXPORTFILEPATH.'/download/');//Write File Here 
					}
				}
			}

//Data for Issued to for Inspections Table			
			foreach($projectId as $project){
				$selIssuetoInspections = 'issued_to_inspections_id, project_id, inspection_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, inspection_status, cost_attribute, inspection_fixed_by_date';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and is_deleted=0 AND inspection_id IN (".$inspArray[$project['project_id']].")");
				
				if ($new_project[$project['project_id']] == 1){
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."'and is_deleted=0 AND inspection_id IN (".$inspArray[$project['project_id']].")");
				}
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					$iPadQueryIssuetoInspections = array();
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_inspections_id'];

						$iPadQueryIssuetoInspections[] = $rowIssuetoInspections['global_id'] . "##VALUES(\"".$db->dataFilter($rowIssuetoInspections['project_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['inspection_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['resource_type'])."\", \"".$db->dataFilter($rowIssuetoInspections['global_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['is_deleted'])."\", \"".$db->dataFilter($rowIssuetoInspections['inspection_fixed_by_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['cost_attribute'])."\", \"".$db->dataFilter($rowIssuetoInspections['inspection_status'])."\", \"".$db->dataFilter($rowIssuetoInspections['closed_date'])."\");\r\n";

					}
					$db->createFile('issued_to_for_inspections_add.txt', join('', $iPadQueryIssuetoInspections), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for Issued to for Inspections Table			
			/*foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']]))
				{
					$selIssuetoInspections = 'issued_to_inspections_id, project_id, inspection_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, inspection_status, cost_attribute, inspection_fixed_by_date';
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						$iPadQueryIssuetoInspections = '';
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_inspections_id'];
	
							$iPadQueryIssuetoInspections  .= "update issued_to_for_inspections set project_id=\"".$db->dataFilter($rowIssuetoInspections['project_id'])."\", inspection_id=\"".$db->dataFilter($rowIssuetoInspections['inspection_id'])."\", issued_to_name=\"".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."\", last_modified_date=\"".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."\", created_date=\"".$db->dataFilter($rowIssuetoInspections['created_date'])."\", created_by=\"".$db->dataFilter($rowIssuetoInspections['created_by'])."\", resource_type=\"".$db->dataFilter($rowIssuetoInspections['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowIssuetoInspections['is_deleted'])."\", inspection_fixed_by_date=\"".$db->dataFilter($rowIssuetoInspections['inspection_fixed_by_date'])."\", cost_attribute=\"".$db->dataFilter($rowIssuetoInspections['cost_attribute'])."\", inspection_status=\"".$db->dataFilter($rowIssuetoInspections['inspection_status'])."\", closed_date=\"".$db->dataFilter($rowIssuetoInspections['closed_date'])."\" where global_id=\"".$db->dataFilter($rowIssuetoInspections['global_id'])."\";\r\n";
						}
						$db->createFile('issued_to_for_inspections_update.txt', $iPadQueryIssuetoInspections, EXPORTFILEPATH.'/download/');//Write File Here 
					}
				}
			}*/

//Data for Inspection Graphics Table			
			foreach($projectId as $project){
				$selInspectionInspectedBy = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and is_deleted=0 AND inspection_id IN (".$inspArray[$project['project_id']].")");
				if ($new_project[$project['project_id']] == 1){
					$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."'and is_deleted=0 AND inspection_id IN (".$inspArray[$project['project_id']].")");
				}
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					$iPadQueryInspectionInspectedBy = array();
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['graphic_id'];

						$iPadQueryInspectionInspectedBy[] = $rowInspectionInspectedBy['global_id'] . "##VALUES(\"".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['graphic_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['graphic_title'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['graphic_name'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\");\r\n";

					}
					$db->createFile('inspection_graphics_add.txt', join('', $iPadQueryInspectionInspectedBy), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

			/*foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']])){
					$selInspectionInspectedBy = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
					$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
					if(mysql_num_rows($rsInspectionInspectedBy) > 0){
						$iPadQueryInspectionInspectedBy = '';
						while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
							$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['graphic_id'];
	
							$iPadQueryInspectionInspectedBy  .= "update inspection_graphics set inspection_id=\"".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."\", graphic_type=\"".$db->dataFilter($rowInspectionInspectedBy['graphic_type'])."\", graphic_title=\"".$db->dataFilter($rowInspectionInspectedBy['graphic_title'])."\", graphic_name=\"".$db->dataFilter($rowInspectionInspectedBy['graphic_name'])."\", last_modified_date=\"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", created_date=\"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", created_by=\"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", resource_type=\"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", project_id=\"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", is_deleted=\"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\" where global_id=\"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\";\r\n";
						}
						$db->createFile('inspection_graphics_update.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
					}
				}
			}*/

//Data for User Permissions Table			
			foreach($projectId as $project){
				$iPadQueryInspectionInspectedBy = array();
				$selInspectionInspectedBy = '`id`, `user_id`, `permission_name`, `is_allow`, `created_by`, `created_date`, `last_modified_date`, `last_modified_by`, `resource_type`, `is_deleted`, `project_id`';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM user_permission WHERE user_id = '".$userId."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if ($new_project[$project['project_id']] == 1)
				{
					$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM user_permission WHERE user_id = '".$userId."' and is_deleted=0");
					#echo "SELECT ".$selInspectionInspectedBy." FROM user_permission WHERE project_id = '".$project['project_id']."' and user_id = '".$userId."' and is_deleted=0";
				}
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['id'];

						$iPadQueryInspectionInspectedBy[] = $rowInspectionInspectedBy['global_id'] . "##VALUES( \"".$db->dataFilter($rowInspectionInspectedBy['user_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['permission_name'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['is_allow'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\");\r\n";

					}
					$db->createFile('user_permission_add.txt', join('', $iPadQueryInspectionInspectedBy), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
				
			foreach($projectId as $project){	
				if (!isset($new_project [$projectId['project_id']])){
					$iPadQueryInspectionInspectedBy = array();
					$selInspectionInspectedBy = '`id`, `user_id`, `permission_name`, `is_allow`, `created_by`, `created_date`, `last_modified_date`, `last_modified_by`, `resource_type`, `is_deleted`, `project_id`';
					$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM user_permission WHERE user_id = '".$userId."'  and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
				#	echo "SELECT ".$selInspectionInspectedBy." FROM user_permission WHERE  project_id = '".$project['project_id']."' and user_id = '".$userId."'  and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date";
					if(mysql_num_rows($rsInspectionInspectedBy) > 0){
						while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
							$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['id'];
	
							$iPadQueryInspectionInspectedBy[] = " update user_permission set user_id=\"".$db->dataFilter($rowInspectionInspectedBy['user_id'])."\", permission_name=\"".$db->dataFilter($rowInspectionInspectedBy['permission_name'])."\", is_allow=\"".$db->dataFilter($rowInspectionInspectedBy['is_allow'])."\", last_modified_date=\"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", created_date=\"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", created_by=\"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", resource_type=\"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", project_id=\"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", is_deleted=\"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\" where global_id=\"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\"\r\n";
	
						}
						$db->createFile('user_permission_update.txt', join('', $iPadQueryInspectionInspectedBy), EXPORTFILEPATH.'/download/');//Write File Here 
					}
				}
			}

//Data for Checklist Data Table			
			foreach($projectId as $project){
				$selInspectionInspectedBy = 'check_list_items_id, project_id, check_list_items_name, check_list_items_tags, check_list_items_option, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM check_list_items WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if ($new_project[$project['project_id']] == 1)
				{
					$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM check_list_items WHERE project_id = '".$project['project_id']."'and is_deleted=0");
				}
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					$iPadQueryInspectionInspectedBy = array();
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['check_list_items_id'];

						$iPadQueryInspectionInspectedBy[] = $rowInspectionInspectedBy['global_id'] . "##VALUES(\"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_name'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_tags'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_option'])."\");\r\n";

					}
					$db->createFile('check_list_items_add.txt', join('', $iPadQueryInspectionInspectedBy), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

			foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']])){
					$selInspectionInspectedBy = 'check_list_items_id, project_id, check_list_items_name, check_list_items_tags, check_list_items_option, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
					$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM check_list_items WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
					if(mysql_num_rows($rsInspectionInspectedBy) > 0){
						$iPadQueryInspectionInspectedBy = array();
						while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
							$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['check_list_items_id'];
	
							$iPadQueryInspectionInspectedBy[] = "update check_list_items set project_id=\"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", check_list_items_name=\"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_name'])."\", check_list_items_tags=\"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_tags'])."\", check_list_items_option=\"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_option'])."\", last_modified_date=\"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", created_date=\"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", created_by=\"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", resource_type=\"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\" where global_id=\"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\";\r\n";
						}
						$db->createFile('check_list_items_update.txt', join('', $iPadQueryInspectionInspectedBy), EXPORTFILEPATH.'/download/');//Write File Here 
					}
				}
			}

//Data for Inspection Checklist Data Table			
//Data for Inspection Graphics Table			
			foreach($projectId as $project){
				$selInspectionInspectedBy = 'insepection_check_list_id, project_id, inspection_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_check_list WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and is_deleted=0 AND inspection_id IN (".$inspArray[$project['project_id']].")");
				if ($new_project[$project['project_id']] == 1)
				{
					$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_check_list WHERE project_id = '".$project['project_id']."'and is_deleted=0 AND inspection_id IN (".$inspArray[$project['project_id']].")");
				}
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					$iPadQueryInspectionInspectedBy = array();
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['insepection_check_list_id'];

						$iPadQueryInspectionInspectedBy[] = $rowInspectionInspectedBy['global_id'] . "##VALUES(\"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_status'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\");\r\n";

					}
					$db->createFile('inspection_check_list_add.txt', join('', $iPadQueryInspectionInspectedBy), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for Insert Progres Monitoring Table			
			foreach($projectId as $project){
				$selIssuetoInspections = 'progress_monitoring_check_list_id, project_id, progress_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM progress_monitoring_check_list WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if ($new_project[$project['project_id']] == 1)
				{
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM progress_monitoring_check_list WHERE project_id = '".$project['project_id']."'and is_deleted=0");
				}
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					$iPadQueryIssuetoInspections = array();
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['progress_monitoring_check_list_id'];

						$iPadQueryIssuetoInspections[] = $rowIssuetoInspections['global_id'] . "##VALUES(\"".$db->dataFilter($rowIssuetoInspections['project_id'])."\",\"".$db->dataFilter($rowIssuetoInspections['progress_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['check_list_items_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['check_list_items_status'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['resource_type'])."\", \"".$db->dataFilter($rowIssuetoInspections['global_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['is_deleted'])."\");\r\n";

					}
					$db->createFile('progress_monitoring_check_list_add.txt', join('', $iPadQueryIssuetoInspections), EXPORTFILEPATH.'/download/');//Write File Here 
				}
				#echo $iPadQueryIssuetoInspections;
			}

//Data for Update Progres Monitoring Table				
			foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']]))
				{
					$selIssuetoInspections = 'progress_monitoring_check_list_id, project_id, progress_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM progress_monitoring_check_list WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and created_date!=last_modified_date");
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						$iPadQueryIssuetoInspections = array();
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['progress_monitoring_check_list_id'];
	
							$iPadQueryInspectionInspectedBy[] = "update progress_monitoring_check_list set project_id=\"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", progress_id=\"".$db->dataFilter($rowInspectionInspectedBy['progress_id'])."\", check_list_items_id=\"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_id'])."\", check_list_items_status=\"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_status'])."\", last_modified_date=\"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", created_date=\"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", created_by=\"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", resource_type=\"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\" where global_id=\"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\";\r\n";
	
						}
						$db->createFile('progress_monitoring_check_list_update.txt', join('', $iPadQueryInspectionInspectedBy), EXPORTFILEPATH.'/download/');//Write File Here 
					}
					#echo $iPadQueryIssuetoInspections;
				}
			}

//Data for Insert Progres Monitoring Table			
			foreach($projectId as $project){
				$selIssuetoInspections = 'location_check_list_id, project_id, location_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM location_check_list WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if ($new_project[$project['project_id']] == 1)
				{
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM location_check_list WHERE project_id = '".$project['project_id']."'and is_deleted=0");
				}
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					$iPadQueryIssuetoInspections = array();
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['location_check_list_id'];

						$iPadQueryIssuetoInspections[] = $rowIssuetoInspections['global_id'] . "##VALUES(\"".$db->dataFilter($rowIssuetoInspections['project_id'])."\",\"".$db->dataFilter($rowIssuetoInspections['location_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['check_list_items_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['check_list_items_status'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['resource_type'])."\", \"".$db->dataFilter($rowIssuetoInspections['global_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['is_deleted'])."\");\r\n";

					}
					$db->createFile('location_check_list_add.txt', join('', $iPadQueryIssuetoInspections), EXPORTFILEPATH.'/download/');//Write File Here 
				}
				#echo $iPadQueryIssuetoInspections;
			}

//Data for Update Progres Monitoring Table				
			foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']]))
				{
					$selIssuetoInspections = 'location_check_list_id, project_id, location_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
					$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM location_check_list WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and created_date!=last_modified_date");
					if(mysql_num_rows($rsIssuetoInspections) > 0){
						$iPadQueryIssuetoInspections = array();
						while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
							$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['location_check_list_id'];
	
							$iPadQueryInspectionInspectedBy[] = "update progress_monitoring_check_list set project_id=\"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", location_id=\"".$db->dataFilter($rowInspectionInspectedBy['location_id'])."\", check_list_items_id=\"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_id'])."\", check_list_items_status=\"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_status'])."\", last_modified_date=\"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", created_date=\"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", created_by=\"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", resource_type=\"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\" where global_id=\"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\";\r\n";
	
						}
						$db->createFile('location_check_list_update.txt', join('', $iPadQueryInspectionInspectedBy), EXPORTFILEPATH.'/download/');//Write File Here 
					}
					#echo $iPadQueryIssuetoInspections;
				}
			}

			/*foreach($projectId as $project){
				if (!isset($new_project [$project['project_id']])){
					$selInspectionInspectedBy = 'insepection_check_list_id, project_id, inspection_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
					$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_check_list WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
					if(mysql_num_rows($rsInspectionInspectedBy) > 0){
						$iPadQueryInspectionInspectedBy = '';
						while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
							$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['insepection_check_list_id'];
	
							$iPadQueryInspectionInspectedBy  .= "update inspection_check_list set project_id=\"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", inspection_id=\"".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."\", check_list_items_id=\"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_id'])."\", check_list_items_status=\"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_status'])."\", last_modified_date=\"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", created_date=\"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", created_by=\"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", resource_type=\"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\" where global_id=\"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\";\r\n";
						}
						$db->createFile('inspection_check_list_update.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
					}
				}
			}*/

//Data for add Inspection Graphics Table			
			/*foreach($projectId as $project){
				$selInspectionInspectedBy = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' and created_date > '".$lastModifiedDate."\"");
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					$iPadQueryInspectionInspectedBy = '';
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['graphic_id'];

						$iPadQueryInspectionInspectedBy  .= "VALUES(\"".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['graphic_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['graphic_title'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['graphic_name'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\");\r\n";

					}
					$db->createFile('inspection_graphics_add.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Inspection Graphics Table			
			foreach($projectId as $project){
				$selInspectionInspectedBy = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' and last_modified_date > '".$lastModifiedDate."\"");
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					$iPadQueryInspectionInspectedBy = '';
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['graphic_id'];

						$iPadQueryInspectionInspectedBy  .= "VALUES(\"".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['graphic_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['graphic_title'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['graphic_name'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\");\r\n";

					}
					$db->createFile('inspection_graphics_update.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}*/
			
//Data for add Drawing Management
			foreach($projectId as $project){
				$selDrawingManatement = 'DISTINCT draw_mgmt_images_id, project_id, draw_mgmt_images_title, draw_mgmt_images_name, draw_mgmt_images_thumbnail, draw_mgmt_images_description, draw_mgmt_images_tags, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsDrawingManatement = mysql_query("SELECT ".$selDrawingManatement." FROM draw_mgmt_images WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."'");
				if ($new_project[$project['project_id']] == 1){
					$rsDrawingManatement = mysql_query("SELECT ".$selDrawingManatement." FROM draw_mgmt_images WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				}
				if(mysql_num_rows($rsDrawingManatement) > 0){
					$iPadQueryDrawingManatement = array();
					while($rowDrawingManatement = mysql_fetch_assoc($rsDrawingManatement)){
						$rowDrawingManatement['global_id'] = $rowDrawingManatement['draw_mgmt_images_id'];

						$iPadQueryDrawingManatement[] = $rowDrawingManatement['global_id'] . "##VALUES(\"".$db->dataFilter($rowDrawingManatement['project_id'])."\", \"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_title'])."\", \"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_name'])."\", \"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_thumbnail'])."\",\"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_description'])."\", \"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_tags'])."\", \"".$db->dataFilter($rowDrawingManatement['last_modified_date'])."\", \"".$db->dataFilter($rowDrawingManatement['last_modified_by'])."\", \"".$db->dataFilter($rowDrawingManatement['created_date'])."\", \"".$db->dataFilter($rowDrawingManatement['created_by'])."\", \"".$db->dataFilter($rowDrawingManatement['resource_type'])."\", \"".$db->dataFilter($rowDrawingManatement['global_id'])."\", \"".$db->dataFilter($rowDrawingManatement['is_deleted'])."\");\r\n";

					}
					$db->createFile('draw_mgmt_images_add.txt', join('', $iPadQueryDrawingManatement), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for Update Drawing Management
			foreach($projectId as $project){
				$selDrawingManatement = 'DISTINCT draw_mgmt_images_id, project_id, draw_mgmt_images_title, draw_mgmt_images_name, draw_mgmt_images_thumbnail, draw_mgmt_images_description, draw_mgmt_images_tags, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsDrawingManatement = mysql_query("SELECT ".$selDrawingManatement." FROM draw_mgmt_images WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."'");
				if(mysql_num_rows($rsDrawingManatement) > 0){
					$iPadQueryDrawingManatement = array();
					while($rowDrawingManatement = mysql_fetch_assoc($rsDrawingManatement)){
						$rowDrawingManatement['global_id'] = $rowDrawingManatement['draw_mgmt_images_id'];

						$iPadQueryDrawingManatement[] = "update draw_mgmt_images set project_id=\"".$db->dataFilter($rowDrawingManatement['project_id'])."\", draw_mgmt_images_title=\"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_title'])."\", draw_mgmt_images_name=\"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_name'])."\", draw_mgmt_images_thumbnail=\"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_thumbnail'])."\", draw_mgmt_images_description=\"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_description'])."\", draw_mgmt_images_tags=\"".$db->dataFilter($rowDrawingManatement['draw_mgmt_images_tags'])."\", last_modified_date=\"".$db->dataFilter($rowDrawingManatement['last_modified_date'])."\", last_modified_by=\"".$db->dataFilter($rowDrawingManatement['last_modified_by'])."\", created_date=\"".$db->dataFilter($rowDrawingManatement['created_date'])."\", created_by=\"".$db->dataFilter($rowDrawingManatement['created_by'])."\", resource_type=\"".$db->dataFilter($rowDrawingManatement['resource_type'])."\", is_deleted=\"".$db->dataFilter($rowDrawingManatement['is_deleted'])."\" where global_id=\"".$db->dataFilter($rowDrawingManatement['global_id'])."\";\r\n";

					}
					$db->createFile('draw_mgmt_images_update.txt', join('', $iPadQueryDrawingManatement), EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
		}
		$zipSource = EXPORTFILEPATH.'/download/';
		$db->createFile('last_modified_date.txt', $date_lmd, EXPORTFILEPATH.'/download/');//Write File Here 

		$zipFileName = $db->updateExportTable(EXPORTFILEPATH . '/download/' . $zipFileName, $_REQUEST['userId'], $deviceType);
		$db->compress($zipSource, 'text_'.$zipFileName, EXPORTFILEPATH);

//Code for Download the zip file
	$filename = 'text_'.$zipFileName.'.zip';
#	flush();
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

function createJsonFile($db, $path, $tablename, $column_names, $dataarray){
	//echo "$path, $tablename, $column_names, $dataarray";
	$count = 0;
	$data_string = 'INSERT INTO '.$tablename .' ('.$column_names.') '.$dataarray[0];
	$data = array();
	for ($i=1;$i<count ($dataarray);$i++)
	{
		$count++;
		if ($count==500){
			$data[] = array("sqlData"=> $data_string);
			$data_string = 'INSERT INTO '.$tablename.' ('.$column_names.') '.$dataarray[$i];
			$i++;
			$count = 1;
		}
		if (isset ($dataarray[$i]))
		{
			$data_string .= " UNION ALL " . $dataarray[$i];
		}
	}
	if ($count != 1 || count ($dataarray) < 500)
	{
		$data[] = array("sqlData"=> $data_string);
	}
	$json = json_encode ($data);
	//echo "<br/>";
	//echo $data_string;
	/*if ($tablename == "project_inspections")
	{
		print_r ($dataarray);
		print_r ($data);
		echo $json;
		die;
	}*/
	$db->createFile($tablename . '_add.json', $json, $path);//Write File Here 	
}
?>