<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');
define("IMPORTFILEPATH", '../sync/Import');

//Header Secttion for include and objects 
if(isset($_REQUEST['data_text'])){

$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId'])))); 
$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
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
		if(empty($lastModifiedDate)){
//Data for User Project Table
			$selUserProject = 'project_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';

			$rsUserProject = mysql_query("SELECT ".$selUserProject." FROM user_projects WHERE user_id = '".$globalId."' and is_deleted=0");
			if(mysql_num_rows($rsUserProject) > 0){
				$iPadQueryUserProject = array();
				while($rowUserProject = mysql_fetch_assoc($rsUserProject)){
					$rowUserProject['global_id'] = $rowUserProject['project_id'];

					$iPadQueryUserProject[] = "SELECT '".$db->dataFilter($rowUserProject['global_id'])."','".$db->dataFilter($rowUserProject['global_id'])."','".$db->dataFilter($rowUserProject['user_id'])."', '".$db->dataFilter($rowUserProject['project_name'])."', '".$db->dataFilter($rowUserProject['project_type'])."', '".$db->dataFilter($rowUserProject['project_address_line1'])."', '".$db->dataFilter($rowUserProject['project_address_line2'])."', '".$db->dataFilter($rowUserProject['project_suburb'])."', '".$db->dataFilter($rowUserProject['project_state'])."', '".$db->dataFilter($rowUserProject['project_postcode'])."', '".$db->dataFilter($rowUserProject['project_country'])."', '".$db->dataFilter($rowUserProject['last_modified_date'])."', '".$db->dataFilter($rowUserProject['last_modified_by'])."', '".$db->dataFilter($rowUserProject['created_date'])."', '".$db->dataFilter($rowUserProject['created_by'])."', '".$db->dataFilter($rowUserProject['resource_type'])."', '".$db->dataFilter($rowUserProject['is_deleted'])."'";
				}
				createJsonFile($db,EXPORTFILEPATH.'/download/', "user_projects", 'project_id, global_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $iPadQueryUserProject);
				//$db->createFile('user_projects_add.txt', $iPadQueryUserProject, EXPORTFILEPATH.'/download/');//Write File Here
			}

//Data for Standard Defect Table
			$iPadQueryStandardDefect = array();
			$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			foreach($projectId as $project){
				$selStandardDefect = 'standard_defect_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted,tag';
				$rsStandardDefect = mysql_query("SELECT ".$selStandardDefect." FROM standard_defects WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsStandardDefect) > 0){
					while($rowStandardDefect = mysql_fetch_assoc($rsStandardDefect)){
						$rowStandardDefect['global_id'] = $rowStandardDefect['standard_defect_id'];

						$iPadQueryStandardDefect[]= " SELECT '".$db->dataFilter($rowStandardDefect['standard_defect_id'])."', '".$db->dataFilter($rowStandardDefect['project_id'])."', '".$db->dataFilter($rowStandardDefect['description'])."', '".$db->dataFilter($rowStandardDefect['last_modified_date'])."', '".$db->dataFilter($rowStandardDefect['last_modified_by'])."', '".$db->dataFilter($rowStandardDefect['created_date'])."', '".$db->dataFilter($rowStandardDefect['created_by'])."', '".$db->dataFilter($rowStandardDefect['resource_type'])."', '".$db->dataFilter($rowStandardDefect['is_deleted'])."', '".$db->dataFilter($rowStandardDefect['tag'])."'";

					}
				//$db->createFile('standard_deffects_add.txt', $iPadQueryStandardDefect, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryStandardDefect) > 0)
				createJsonFile($db,EXPORTFILEPATH.'/download/', "standard_defects", 'global_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted,tag', $iPadQueryStandardDefect);

//Data for Project Location Table			
					$iPadQueryProjectLocation = array();
			foreach($projectId as $project){
				$selProjectLocation = 'DISTINCT location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_locations WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsProjectLocation) > 0){
					while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
						$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];

						$iPadQueryProjectLocation[] = " SELECT '".$db->dataFilter($rowProjectLocation['global_id'])."', '".$db->dataFilter($rowProjectLocation['global_id'])."','".$db->dataFilter($rowProjectLocation['project_id'])."', '".$db->dataFilter($rowProjectLocation['location_parent_id'])."', '".$db->dataFilter($rowProjectLocation['location_title'])."', '".$db->dataFilter($rowProjectLocation['last_modified_date'])."', '".$db->dataFilter($rowProjectLocation['last_modified_by'])."', '".$db->dataFilter($rowProjectLocation['created_date'])."', '".$db->dataFilter($rowProjectLocation['created_by'])."', '".$db->dataFilter($rowProjectLocation['resource_type'])."', '".$db->dataFilter($rowProjectLocation['is_deleted'])."'";

					}
					//$db->createFile('project_locations_add.txt', $iPadQueryProjectLocation, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryProjectLocation) > 0)
			{
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

						$iPadQueryProjectLocation [] = " SELECT '".$db->dataFilter($rowProjectLocation['global_id'])."','".$db->dataFilter($rowProjectLocation['global_id'])."','".$db->dataFilter($rowProjectLocation['project_id'])."', '".$db->dataFilter($rowProjectLocation['location_parent_id'])."', '".$db->dataFilter($rowProjectLocation['location_title'])."', '".$db->dataFilter($rowProjectLocation['last_modified_date'])."', '".$db->dataFilter($rowProjectLocation['last_modified_by'])."', '".$db->dataFilter($rowProjectLocation['created_date'])."', '".$db->dataFilter($rowProjectLocation['created_by'])."', '".$db->dataFilter($rowProjectLocation['resource_type'])."', '".$db->dataFilter($rowProjectLocation['is_deleted'])."'";

					}
					//$db->createFile('project_monitoring_locations_add.txt', $iPadQueryProjectLocation, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryProjectLocation) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "project_monitoring_locations", 'location_id,global_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $iPadQueryProjectLocation);
			}

//Data for Project Inspections Table			
					$iPadQueryrsProjectInspections = array();
			foreach($projectId as $project){
				$selProjectInspections = 'DISTINCT inspection_id, project_id, location_id, inspection_date_raised, inspection_status, inspection_inspected_by, inspection_fixed_by_date, inspection_type, inspection_map_address, inspection_description, inspection_priority, inspection_notes, inspection_sign_image, inspection_location, cost_attribute, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections WHERE project_id = '".$project['project_id']."' and is_deleted=0 group by inspection_id");
				if(mysql_num_rows($rsProjectInspections) > 0){
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

						$iPadQueryrsProjectInspections[] = " SELECT '".$db->dataFilter($rowProjectInspections['global_id'])."', '".$db->dataFilter($rowProjectInspections['global_id'])."', '".$db->dataFilter($rowProjectInspections['project_id'])."', '".$db->dataFilter($rowProjectInspections['location_id'])."', '".$db->dataFilter($date_raised)."', '".$db->dataFilter($rowProjectInspections['inspection_status'])."', '".$db->dataFilter($rowProjectInspections['inspection_inspected_by'])."', '".$db->dataFilter($inspection_fixed_by_date)."', '".$db->dataFilter($rowProjectInspections['inspection_type'])."', '".$db->dataFilter($rowProjectInspections['inspection_map_address'])."', '".$db->dataFilter($rowProjectInspections['inspection_description'])."', '".$db->dataFilter($rowProjectInspections['inspection_priority'])."', '".$db->dataFilter($rowProjectInspections['inspection_notes'])."', '".$db->dataFilter($rowProjectInspections['inspection_sign_image'])."', '".$db->dataFilter($rowProjectInspections['inspection_location'])."', '".$db->dataFilter($rowProjectInspections['cost_attribute'])."', '".$db->dataFilter($rowProjectInspections['last_modified_date'])."', '".$db->dataFilter($rowProjectInspections['last_modified_by'])."', '".$db->dataFilter($rowProjectInspections['created_date'])."', '".$db->dataFilter($rowProjectInspections['created_by'])."', '".$db->dataFilter($rowProjectInspections['resource_type'])."', '".$db->dataFilter($rowProjectInspections['is_deleted'])."'";

					}
					//$db->createFile('project_inspection_add.txt', $iPadQueryrsProjectInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryrsProjectInspections) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "project_inspections", 'inspection_id,global_id, project_id, location_id, inspection_date_raised, inspection_status, inspection_inspected_by, inspection_fixed_by_date, inspection_type, inspection_map_address, inspection_description, inspection_priority, inspection_notes, inspection_sign_image, inspection_location, cost_attribute, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $iPadQueryrsProjectInspections);
			}
//Data for Progress Monitoring Table			
			$iPadQueryProgressMonitoring = array();
			foreach($projectId as $project){
				$selProgressMonitoring = 'progress_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, status, percentage';
				$rsProgressMonitoring = mysql_query("SELECT ".$selProgressMonitoring." FROM progress_monitoring WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsProgressMonitoring) > 0){
					while($rowProgressMonitoring = mysql_fetch_assoc($rsProgressMonitoring)){
						$rowProgressMonitoring['global_id'] = $rowProgressMonitoring['progress_id'];

						$iPadQueryProgressMonitoring[]  = " SELECT '".$db->dataFilter($rowProgressMonitoring['progress_id'])."', '".$db->dataFilter($rowProgressMonitoring['project_id'])."', '".$db->dataFilter($rowProgressMonitoring['location_id'])."', '".$db->dataFilter($rowProgressMonitoring['sub_location_id'])."', '".$db->dataFilter($rowProgressMonitoring['task'])."', '".$db->dataFilter($rowProgressMonitoring['start_date'])."', '".$db->dataFilter($rowProgressMonitoring['end_date'])."', '".$db->dataFilter($rowProgressMonitoring['last_modified_date'])."', '".$db->dataFilter($rowProgressMonitoring['last_modified_by'])."', '".$db->dataFilter($rowProgressMonitoring['created_date'])."', '".$db->dataFilter($rowProgressMonitoring['created_by'])."', '".$db->dataFilter($rowProgressMonitoring['resource_type'])."', '".$db->dataFilter($rowProgressMonitoring['is_deleted'])."', '".$db->dataFilter($rowProgressMonitoring['status'])."', '".$db->dataFilter($rowProgressMonitoring['percentage'])."'";
					}
					//$db->createFile('progress_monitoring_add.txt', $iPadQueryProgressMonitoring, EXPORTFILEPATH.'/download/');//Write File Here  
				}
			}
			if(count($iPadQueryProgressMonitoring) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "progress_monitoring", 'global_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, status, percentage', $iPadQueryProgressMonitoring);
			}

//Data for Progress Monitoring Update Table/no requirement to send this file.
			/*foreach($projectId as $project){
				$selProgressMonitoringUpdate = 'update_id, project_id, progress_id, percentage, status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProgressMonitoringUpdate = mysql_query("SELECT ".$selProgressMonitoringUpdate." FROM progress_monitoring_update WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsProgressMonitoringUpdate) > 0){
					$iPadQueryProgressMonitoringUpdate = '';
					while($rowProgressMonitoringUpdate = mysql_fetch_assoc($rsProgressMonitoringUpdate)){
						$rowProgressMonitoringUpdate['global_id'] = $rowProgressMonitoringUpdate['update_id'];

						$iPadQueryProgressMonitoringUpdate  .= "VALUES('".$db->dataFilter($rowProgressMonitoringUpdate['progress_id'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['project_id'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['percentage'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['status'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['last_modified_date'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['last_modified_by'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['created_date'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['created_by'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['resource_type'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['global_id'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['is_deleted'])."');\r\n";

					}
					//createJsonFile($db,EXPORTFILEPATH.'/download/', "progress_monitoring", 'global_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, status, percentage', $iPadQueryProgressMonitoring);
					//$db->createFile('progress_monitoring_updates_add.txt', $iPadQueryProgressMonitoringUpdate, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}*/

//Data for Issued to for Inspections Table			
					$iPadQueryIssuetoInspections = array();
			foreach($projectId as $project){
				$selIssuetoInspections = 'issued_to_inspections_id, project_id, inspection_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, inspection_status, cost_attribute, inspection_fixed_by_date,closed_date';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_inspections_id'];
						
						$iPadQueryIssuetoInspections[]  = " SELECT '".$db->dataFilter($rowIssuetoInspections['issued_to_inspections_id'])."', '".$db->dataFilter($rowIssuetoInspections['project_id'])."', '".$db->dataFilter($rowIssuetoInspections['inspection_id'])."', '".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."', '".$db->dataFilter($rowIssuetoInspections['created_date'])."', '".$db->dataFilter($rowIssuetoInspections['created_by'])."', '".$db->dataFilter($rowIssuetoInspections['resource_type'])."', '".$db->dataFilter($rowIssuetoInspections['is_deleted'])."', '".$db->dataFilter($rowIssuetoInspections['inspection_fixed_by_date'])."', '".$db->dataFilter($rowIssuetoInspections['cost_attribute'])."', '".$db->dataFilter($rowIssuetoInspections['inspection_status'])."', '".$db->dataFilter($rowIssuetoInspections['closed_date'])."'";

					}
					//$db->createFile('issued_to_for_inspections_add.txt', $iPadQueryIssuetoInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryIssuetoInspections) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "issued_to_for_inspections", 'global_id, project_id, inspection_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, inspection_fixed_by_date, cost_attribute,inspection_status, closed_date', $iPadQueryIssuetoInspections);
			}

//Data for Issued to for Progres Monitoring Table			
					$iPadQueryIssuetoInspections = array();
			foreach($projectId as $project){
				$selIssuetoInspections = 'issued_to_progress_monitoring_id,progress_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_progress_monitoring WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_progress_monitoring_id'];

						$iPadQueryIssuetoInspections []= " SELECT '".$db->dataFilter($rowIssuetoInspections['global_id'])."', '".$db->dataFilter($rowIssuetoInspections['progress_id'])."', '".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."', '".$db->dataFilter($rowIssuetoInspections['created_date'])."', '".$db->dataFilter($rowIssuetoInspections['created_by'])."', '".$db->dataFilter($rowIssuetoInspections['resource_type'])."', '".$db->dataFilter($rowIssuetoInspections['project_id'])."', '".$db->dataFilter($rowIssuetoInspections['is_deleted'])."'";

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
				$selInspectionIssueTo = 'issue_to_id, project_id, issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionIssueTo = mysql_query("SELECT ".$selInspectionIssueTo." FROM inspection_issue_to WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsInspectionIssueTo) > 0){
					while($rowInspectionIssueTo = mysql_fetch_assoc($rsInspectionIssueTo)){
						$rowInspectionIssueTo['global_id'] = $rowInspectionIssueTo['issue_to_id'];

						$iPadQueryInspectionIssueTo [] = " SELECT '".$db->dataFilter($rowInspectionIssueTo['global_id'])."', '".$db->dataFilter($rowInspectionIssueTo['project_id'])."', '".$db->dataFilter($rowInspectionIssueTo['issue_to_name'])."', '".$db->dataFilter($rowInspectionIssueTo['last_modified_date'])."', '".$db->dataFilter($rowInspectionIssueTo['last_modified_by'])."', '".$db->dataFilter($rowInspectionIssueTo['created_date'])."', '".$db->dataFilter($rowInspectionIssueTo['created_by'])."', '".$db->dataFilter($rowInspectionIssueTo['resource_type'])."',  '".$db->dataFilter($rowInspectionIssueTo['is_deleted'])."'";

					}
					//$db->createFile('inspection_issue_to_add.txt', $iPadQueryInspectionIssueTo, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryInspectionIssueTo) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "inspection_issue_to", 'global_id, project_id, issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $iPadQueryInspectionIssueTo);
			}

//Data for Inspection Graphics Table			
					$iPadQueryInspectionInspectedBy = array();
			foreach($projectId as $project){
				$selInspectionInspectedBy = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' and is_deleted=0");
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['graphic_id'];

						$iPadQueryInspectionInspectedBy[]  = " SELECT '".$db->dataFilter($rowInspectionInspectedBy['graphic_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['project_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_type'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_title'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_name'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."', '".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."'";

					}
					//$db->createFile('inspection_graphics_add.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}
			if(count($iPadQueryInspectionInspectedBy) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "inspection_graphics", 'global_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $iPadQueryInspectionInspectedBy);
			}

//Data for User Permissions Table			
			$iPadQueryInspectionInspectedBy = array();
			$selInspectionInspectedBy = '`id`, `user_id`, `permission_name`, `is_allow`, `created_by`, `created_date`, `last_modified_date`, `last_modified_by`, `resource_type`, `is_deleted`';
			$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM user_permission WHERE user_id = '".$userId."' and is_deleted=0");
			//echo "SELECT ".$selInspectionInspectedBy." FROM user_permission WHERE user_id = '".$userId."' and is_deleted=0"
			if(mysql_num_rows($rsInspectionInspectedBy) > 0){
				while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
					$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['id'];

					$iPadQueryInspectionInspectedBy[]  = " SELECT '".$db->dataFilter($rowInspectionInspectedBy['global_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['user_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['permission_name'])."', '".$db->dataFilter($rowInspectionInspectedBy['is_allow'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."', '".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."'";

				}
				//$db->createFile('inspection_graphics_add.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
			}
			if(count($iPadQueryInspectionInspectedBy) > 0){
				createJsonFile($db,EXPORTFILEPATH.'/download/', "user_permission", 'global_id, user_id, permission_name, is_allow, created_by, created_date, last_modified_date, last_modified_by, resource_type, is_deleted', $iPadQueryInspectionInspectedBy);
			}

		}else{//Modified Date is comes
//Data for add User Project Table
			$selUserProject = 'project_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
			$rsUserProject = mysql_query("SELECT ".$selUserProject." FROM user_projects WHERE user_id = '".$globalId."' and created_date >= '".$lastModifiedDate."' and is_deleted=0 and last_modified_date=created_date");
			if(mysql_num_rows($rsUserProject) > 0){
				$iPadQueryUserProject = '';
				while($rowUserProject = mysql_fetch_assoc($rsUserProject)){
					#$rowUserProject['user_id'] = $userId;
					$rowUserProject['global_id'] = $rowUserProject['project_id'];

					$iPadQueryUserProject .= "VALUES('".$db->dataFilter($rowUserProject['global_id'])."','".$db->dataFilter($rowUserProject['user_id'])."', '".$db->dataFilter($rowUserProject['project_name'])."', '".$db->dataFilter($rowUserProject['project_type'])."', '".$db->dataFilter($rowUserProject['project_address_line1'])."', '".$db->dataFilter($rowUserProject['project_address_line2'])."', '".$db->dataFilter($rowUserProject['project_suburb'])."', '".$db->dataFilter($rowUserProject['project_state'])."', '".$db->dataFilter($rowUserProject['project_postcode'])."', '".$db->dataFilter($rowUserProject['project_country'])."', '".$db->dataFilter($rowUserProject['last_modified_date'])."', '".$db->dataFilter($rowUserProject['last_modified_by'])."', '".$db->dataFilter($rowUserProject['created_date'])."', '".$db->dataFilter($rowUserProject['created_by'])."', '".$db->dataFilter($rowUserProject['resource_type'])."', '".$db->dataFilter($rowUserProject['global_id'])."', '".$db->dataFilter($rowUserProject['is_deleted'])."');\r\n";
				}
				$db->createFile('user_projects_add.txt', $iPadQueryUserProject, EXPORTFILEPATH.'/download/');//Write File Here 
			}

//Data for UpdateQuery User Project Table
			$selUserProject = 'project_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
			$rsUserProject = mysql_query("SELECT ".$selUserProject." FROM user_projects WHERE user_id = '".$globalId."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
			if(mysql_num_rows($rsUserProject) > 0){
				$iPadQueryUserProject = '';
				while($rowUserProject = mysql_fetch_assoc($rsUserProject)){
					#$rowUserProject['user_id'] = $userId;
					$rowUserProject['global_id'] = $rowUserProject['project_id'];

					$iPadQueryUserProject .= "update user_projects set project_name='".$db->dataFilter($rowUserProject['project_name'])."', project_type='".$db->dataFilter($rowUserProject['project_type'])."', project_address_line1='".$db->dataFilter($rowUserProject['project_address_line1'])."', project_address_line2='".$db->dataFilter($rowUserProject['project_address_line2'])."', project_suburb='".$db->dataFilter($rowUserProject['project_suburb'])."', project_state='".$db->dataFilter($rowUserProject['project_state'])."', project_postcode='".$db->dataFilter($rowUserProject['project_postcode'])."', project_country='".$db->dataFilter($rowUserProject['project_country'])."', last_modified_date='".$db->dataFilter($rowUserProject['last_modified_date'])."', last_modified_by='".$db->dataFilter($rowUserProject['last_modified_by'])."', resource_type='".$db->dataFilter($rowUserProject['resource_type'])."', is_deleted='".$db->dataFilter($rowUserProject['is_deleted'])."' where global_id=".$rowUserProject['global_id'].";\r\n";

				}
				$db->createFile('user_projects_update.txt', $iPadQueryUserProject, EXPORTFILEPATH.'/download/');//Write File Here 
			}

//Data for add Standard Defect Table
			$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			foreach($projectId as $project){
				$selStandardDefect = 'standard_defect_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted,tag';
				$rsStandardDefect = mysql_query("SELECT ".$selStandardDefect." FROM standard_defects WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if(mysql_num_rows($rsStandardDefect) > 0){
					$iPadQueryStandardDefect = '';
					while($rowStandardDefect = mysql_fetch_assoc($rsStandardDefect)){
						$rowStandardDefect['global_id'] = $rowStandardDefect['standard_defect_id'];

						$iPadQueryStandardDefect .= "VALUES('".$db->dataFilter($rowStandardDefect['project_id'])."', '".$db->dataFilter($rowStandardDefect['description'])."', '".$db->dataFilter($rowStandardDefect['last_modified_date'])."', '".$db->dataFilter($rowStandardDefect['last_modified_by'])."', '".$db->dataFilter($rowStandardDefect['created_date'])."', '".$db->dataFilter($rowStandardDefect['created_by'])."', '".$db->dataFilter($rowStandardDefect['resource_type'])."', '".$db->dataFilter($rowStandardDefect['global_id'])."', '".$db->dataFilter($rowStandardDefect['is_deleted'])."', '".$db->dataFilter($rowStandardDefect['tag'])."');\r\n";

					}
					$db->createFile('standard_deffects_add.txt', $iPadQueryStandardDefect, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Standard Defect Table
			#$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			foreach($projectId as $project){
				$selStandardDefect = 'standard_defect_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, tag';
				$rsStandardDefect = mysql_query("SELECT ".$selStandardDefect." FROM standard_defects WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
				if(mysql_num_rows($rsStandardDefect) > 0){
					$iPadQueryStandardDefect = '';
					while($rowStandardDefect = mysql_fetch_assoc($rsStandardDefect)){
						$rowStandardDefect['global_id'] = $rowStandardDefect['standard_defect_id'];

						$iPadQueryStandardDefect .= "update standard_defects set description='".$db->dataFilter($rowStandardDefect['description'])."', last_modified_date='".$db->dataFilter($rowStandardDefect['last_modified_date'])."', last_modified_by='".$db->dataFilter($rowStandardDefect['last_modified_by'])."', resource_type='".$db->dataFilter($rowStandardDefect['resource_type'])."', is_deleted='".$db->dataFilter($rowStandardDefect['is_deleted'])."', tag='".$db->dataFilter($rowStandardDefect['tag'])."' where global_id='".$db->dataFilter($rowStandardDefect['global_id'])."';\r\n";

					}
					$db->createFile('standard_deffects_update.txt', $iPadQueryStandardDefect, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for add Project Location Table			
			foreach($projectId as $project){
				$selProjectLocation = 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_locations WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if(mysql_num_rows($rsProjectLocation) > 0){
					$iPadQueryProjectLocation = '';
					while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
						$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];

						$iPadQueryProjectLocation  .= "VALUES('".$db->dataFilter($rowProjectLocation['global_id'])."','".$db->dataFilter($rowProjectLocation['project_id'])."', '".$db->dataFilter($rowProjectLocation['location_parent_id'])."', '".$db->dataFilter($rowProjectLocation['location_title'])."', '".$db->dataFilter($rowProjectLocation['last_modified_date'])."', '".$db->dataFilter($rowProjectLocation['last_modified_by'])."', '".$db->dataFilter($rowProjectLocation['created_date'])."', '".$db->dataFilter($rowProjectLocation['created_by'])."', '".$db->dataFilter($rowProjectLocation['resource_type'])."', '".$db->dataFilter($rowProjectLocation['global_id'])."', '".$db->dataFilter($rowProjectLocation['is_deleted'])."');\r\n";

					}
					$db->createFile('project_locations_add.txt', $iPadQueryProjectLocation, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Project Location Table			
			foreach($projectId as $project){
				$selProjectLocation = 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_locations WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
				if(mysql_num_rows($rsProjectLocation) > 0){
					$iPadQueryProjectLocation = '';
					while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
						$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];

						$iPadQueryProjectLocation  .= "update project_locations set location_parent_id='".$db->dataFilter($rowProjectLocation['location_parent_id'])."', location_title='".$db->dataFilter($rowProjectLocation['location_title'])."', last_modified_date='".$db->dataFilter($rowProjectLocation['last_modified_date'])."', last_modified_by='".$db->dataFilter($rowProjectLocation['last_modified_by'])."', resource_type='".$db->dataFilter($rowProjectLocation['resource_type'])."', is_deleted='".$db->dataFilter($rowProjectLocation['is_deleted'])."' where global_id='".$db->dataFilter($rowProjectLocation['global_id'])."';\r\n";

					}
					$db->createFile('project_locations_update.txt', $iPadQueryProjectLocation, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for add Progress Monitoring Location Table			
			foreach($projectId as $project){
				$selProjectLocation = 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_monitoring_locations WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if(mysql_num_rows($rsProjectLocation) > 0){
					$iPadQueryProjectLocation = '';
					while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
						$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];

						$iPadQueryProjectLocation  .= "VALUES('".$db->dataFilter($rowProjectLocation['global_id'])."','".$db->dataFilter($rowProjectLocation['project_id'])."', '".$db->dataFilter($rowProjectLocation['location_parent_id'])."', '".$db->dataFilter($rowProjectLocation['location_title'])."', '".$db->dataFilter($rowProjectLocation['last_modified_date'])."', '".$db->dataFilter($rowProjectLocation['last_modified_by'])."', '".$db->dataFilter($rowProjectLocation['created_date'])."', '".$db->dataFilter($rowProjectLocation['created_by'])."', '".$db->dataFilter($rowProjectLocation['resource_type'])."', '".$db->dataFilter($rowProjectLocation['global_id'])."', '".$db->dataFilter($rowProjectLocation['is_deleted'])."');\r\n";

					}
					$db->createFile('project_monitoring_locations_add.txt', $iPadQueryProjectLocation, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Project Location Table			
			foreach($projectId as $project){
				$selProjectLocation = 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_monitoring_locations WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
				if(mysql_num_rows($rsProjectLocation) > 0){
					$iPadQueryProjectLocation = '';
					while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
						$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];

						$iPadQueryProjectLocation  .= "update project_monitoring_locations set location_parent_id='".$db->dataFilter($rowProjectLocation['location_parent_id'])."', location_title='".$db->dataFilter($rowProjectLocation['location_title'])."', last_modified_date='".$db->dataFilter($rowProjectLocation['last_modified_date'])."', last_modified_by='".$db->dataFilter($rowProjectLocation['last_modified_by'])."', resource_type='".$db->dataFilter($rowProjectLocation['resource_type'])."', is_deleted='".$db->dataFilter($rowProjectLocation['is_deleted'])."' where global_id='".$db->dataFilter($rowProjectLocation['global_id'])."';\r\n";

					}
					$db->createFile('project_monitoring_locations_update.txt', $iPadQueryProjectLocation, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for add Project Inspections Table			
			foreach($projectId as $project){
				$selProjectInspections = 'inspection_id, project_id, location_id, inspection_date_raised, inspection_status, inspection_inspected_by, inspection_fixed_by_date, inspection_type, inspection_map_address, inspection_description, inspection_priority, inspection_notes, inspection_sign_image, inspection_location, cost_attribute, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
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


						$iPadQueryrsProjectInspections  .= "VALUES('".$db->dataFilter($rowProjectInspections['global_id'])."','".$db->dataFilter($rowProjectInspections['location_id'])."', '".$db->dataFilter($date_raised)."', '".$db->dataFilter($rowProjectInspections['inspection_status'])."', '".$db->dataFilter($rowProjectInspections['inspection_inspected_by'])."', '".$db->dataFilter($inspection_fixed_by_date)."', '".$db->dataFilter($rowProjectInspections['inspection_type'])."', '".$db->dataFilter($rowProjectInspections['inspection_map_address'])."', '".$db->dataFilter($rowProjectInspections['inspection_description'])."', '".$db->dataFilter($rowProjectInspections['inspection_priority'])."', '".$db->dataFilter($rowProjectInspections['inspection_notes'])."', '".$db->dataFilter($rowProjectInspections['inspection_sign_image'])."', '".$db->dataFilter($rowProjectInspections['inspection_location'])."', '".$db->dataFilter($rowProjectInspections['cost_attribute'])."', '".$db->dataFilter($rowProjectInspections['last_modified_date'])."', '".$db->dataFilter($rowProjectInspections['last_modified_by'])."', '".$db->dataFilter($rowProjectInspections['created_date'])."', '".$db->dataFilter($rowProjectInspections['created_by'])."', '".$db->dataFilter($rowProjectInspections['resource_type'])."', '".$db->dataFilter($rowProjectInspections['global_id'])."', '".$db->dataFilter($rowProjectInspections['is_deleted'])."', '".$db->dataFilter($rowProjectInspections['project_id'])."');\r\n";

					}
					$db->createFile('project_inspection_add.txt', $iPadQueryrsProjectInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Project Inspections Table			
			foreach($projectId as $project){
				$selProjectInspections = 'inspection_id, project_id, location_id, inspection_date_raised, inspection_status, inspection_inspected_by, inspection_fixed_by_date, inspection_type, inspection_map_address, inspection_description, inspection_priority, inspection_notes, inspection_sign_image, inspection_location, cost_attribute, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
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

						$iPadQueryrsProjectInspections  .= "update project_inspections set location_id='".$db->dataFilter($rowProjectInspections['location_id'])."', inspection_date_raised='".$db->dataFilter($date_raised)."', inspection_status='".$db->dataFilter($rowProjectInspections['inspection_status'])."', inspection_inspected_by='".$db->dataFilter($rowProjectInspections['inspection_inspected_by'])."', inspection_fixed_by_date='".$db->dataFilter($inspection_fixed_by_date)."', inspection_type='".$db->dataFilter($rowProjectInspections['inspection_type'])."', inspection_map_address='".$db->dataFilter($rowProjectInspections['inspection_map_address'])."', inspection_description='".$db->dataFilter($rowProjectInspections['inspection_description'])."', inspection_priority='".$db->dataFilter($rowProjectInspections['inspection_priority'])."', inspection_notes='".$db->dataFilter($rowProjectInspections['inspection_notes'])."', inspection_sign_image='".$db->dataFilter($rowProjectInspections['inspection_sign_image'])."', inspection_location='".$db->dataFilter($rowProjectInspections['inspection_location'])."', cost_attribute='".$db->dataFilter($rowProjectInspections['cost_attribute'])."', last_modified_date='".$db->dataFilter($rowProjectInspections['last_modified_date'])."', last_modified_by='".$db->dataFilter($rowProjectInspections['last_modified_by'])."', created_date='".$db->dataFilter($rowProjectInspections['created_date'])."', created_by='".$db->dataFilter($rowProjectInspections['created_by'])."', resource_type='".$db->dataFilter($rowProjectInspections['resource_type'])."', is_deleted='".$db->dataFilter($rowProjectInspections['is_deleted'])."', project_id='".$db->dataFilter($rowProjectInspections['project_id'])."' where global_id='".$db->dataFilter($rowProjectInspections['global_id'])."';\r\n";
					}
					$db->createFile('project_inspection_update.txt', $iPadQueryrsProjectInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for add Progress Monitoring Table			
			foreach($projectId as $project){
				$selProgressMonitoring = 'progress_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, status, percentage';
				$rsProgressMonitoring = mysql_query("SELECT ".$selProgressMonitoring." FROM progress_monitoring WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if(mysql_num_rows($rsProgressMonitoring) > 0){
					$iPadQueryProgressMonitoring = '';
					while($rowProgressMonitoring = mysql_fetch_assoc($rsProgressMonitoring)){
						$rowProgressMonitoring['global_id'] = $rowProgressMonitoring['progress_id'];

						$iPadQueryProgressMonitoring  .= "VALUES('".$db->dataFilter($rowProgressMonitoring['project_id'])."', '".$db->dataFilter($rowProgressMonitoring['location_id'])."', '".$db->dataFilter($rowProgressMonitoring['sub_location_id'])."', '".$db->dataFilter($rowProgressMonitoring['task'])."', '".$db->dataFilter($rowProgressMonitoring['start_date'])."', '".$db->dataFilter($rowProgressMonitoring['end_date'])."', '".$db->dataFilter($rowProgressMonitoring['last_modified_date'])."', '".$db->dataFilter($rowProgressMonitoring['last_modified_by'])."', '".$db->dataFilter($rowProgressMonitoring['created_date'])."', '".$db->dataFilter($rowProgressMonitoring['created_by'])."', '".$db->dataFilter($rowProgressMonitoring['resource_type'])."', '".$db->dataFilter($rowProgressMonitoring['global_id'])."', '".$db->dataFilter($rowProgressMonitoring['is_deleted'])."', '".$db->dataFilter($rowProgressMonitoring['status'])."', '".$db->dataFilter($rowProgressMonitoring['percentage'])."');\r\n";

					}
					$db->createFile('progress_monitoring_add.txt', $iPadQueryProgressMonitoring, EXPORTFILEPATH.'/download/');//Write File Here  
				}
			}

//Data for UpdateQuery Progress Monitoring Table			
			foreach($projectId as $project){
				$selProgressMonitoring = 'progress_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, status, percentage';
				$rsProgressMonitoring = mysql_query("SELECT ".$selProgressMonitoring." FROM progress_monitoring WHERE project_id = '".$project['project_id']."' and last_modified_date >='".$lastModifiedDate."' and last_modified_date!=created_date");
				if(mysql_num_rows($rsProgressMonitoring) > 0){
					$iPadQueryProgressMonitoring = '';
					while($rowProgressMonitoring = mysql_fetch_assoc($rsProgressMonitoring)){
						$rowProgressMonitoring['global_id'] = $rowProgressMonitoring['progress_id'];

						$iPadQueryProgressMonitoring  .= "update progress_monitoring set location_id='".$db->dataFilter($rowProgressMonitoring['location_id'])."', sub_location_id='".$db->dataFilter($rowProgressMonitoring['sub_location_id'])."', task='".$db->dataFilter($rowProgressMonitoring['task'])."', start_date='".$db->dataFilter($rowProgressMonitoring['start_date'])."', end_date='".$db->dataFilter($rowProgressMonitoring['end_date'])."', last_modified_date='".$db->dataFilter($rowProgressMonitoring['last_modified_date'])."', last_modified_by='".$db->dataFilter($rowProgressMonitoring['last_modified_by'])."', resource_type='".$db->dataFilter($rowProgressMonitoring['resource_type'])."', is_deleted='".$db->dataFilter($rowProgressMonitoring['is_deleted'])."', status='".$db->dataFilter($rowProgressMonitoring['status'])."', percentage='".$db->dataFilter($rowProgressMonitoring['percentage'])."' where global_id='".$db->dataFilter($rowProgressMonitoring['global_id'])."';\r\n";

					}
					$db->createFile('progress_monitoring_update.txt', $iPadQueryProgressMonitoring, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for add Progress Monitoring Update Table			
			foreach($projectId as $project){
				$selProgressMonitoringUpdate = 'update_id, project_id, progress_id, percentage, status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProgressMonitoringUpdate = mysql_query("SELECT ".$selProgressMonitoringUpdate." FROM progress_monitoring_update WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if(mysql_num_rows($rsProgressMonitoringUpdate) > 0){
					$iPadQueryProgressMonitoringUpdate = '';
					while($rowProgressMonitoringUpdate = mysql_fetch_assoc($rsProgressMonitoringUpdate)){
						$rowProgressMonitoringUpdate['global_id'] = $rowProgressMonitoringUpdate['update_id'];

						$iPadQueryProgressMonitoringUpdate  .= "VALUES('".$db->dataFilter($rowProgressMonitoringUpdate['progress_id'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['project_id'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['percentage'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['status'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['last_modified_date'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['last_modified_by'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['created_date'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['created_by'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['resource_type'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['global_id'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['is_deleted'])."');\r\n";

					}
					$db->createFile('progress_monitoring_updates_add.txt', $iPadQueryProgressMonitoringUpdate, EXPORTFILEPATH.'/download/');//Write File Here 
					#echo $iPadQueryProgressMonitoringUpdate; 
				}
			}

//Data for UpdateQuery Progress Monitoring Update Table			
			foreach($projectId as $project){
				$selProgressMonitoringUpdate = 'update_id, project_id, progress_id, percentage, status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProgressMonitoringUpdate = mysql_query("SELECT ".$selProgressMonitoringUpdate." FROM progress_monitoring_update WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
				if(mysql_num_rows($rsProgressMonitoringUpdate) > 0){
					$iPadQueryProgressMonitoringUpdate = '';
					while($rowProgressMonitoringUpdate = mysql_fetch_assoc($rsProgressMonitoringUpdate)){
						$rowProgressMonitoringUpdate['global_id'] = $rowProgressMonitoringUpdate['update_id'];

						$iPadQueryProgressMonitoringUpdate  .= "VALUES('".$db->dataFilter($rowProgressMonitoringUpdate['progress_id'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['project_id'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['percentage'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['status'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['last_modified_date'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['last_modified_by'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['created_date'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['created_by'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['resource_type'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['global_id'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['is_deleted'])."');\r\n";

					}
					$db->createFile('progress_monitoring_updates_upadte.txt', $iPadQueryProgressMonitoringUpdate, EXPORTFILEPATH.'/download/');//Write File Here 
#					echo $iPadQueryProgressMonitoringUpdate; 
				}
			}


//Data for Issued to for Progres Monitoring Table			
			foreach($projectId as $project){
				$selIssuetoInspections = 'issued_to_progress_monitoring_id,progress_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_progress_monitoring WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					$iPadQueryIssuetoInspections = '';
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_progress_monitoring_id'];

						$iPadQueryIssuetoInspections  .= "VALUES('".$db->dataFilter($rowIssuetoInspections['progress_id'])."', '".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."', '".$db->dataFilter($rowIssuetoInspections['created_date'])."', '".$db->dataFilter($rowIssuetoInspections['created_by'])."', '".$db->dataFilter($rowIssuetoInspections['resource_type'])."', '".$db->dataFilter($rowIssuetoInspections['project_id'])."', '".$db->dataFilter($rowIssuetoInspections['is_deleted'])."', '".$db->dataFilter($rowIssuetoInspections['global_id'])."');\r\n";

					}
					$db->createFile('issued_to_for_progress_monitoring_add.txt', $iPadQueryIssuetoInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
				#echo $iPadQueryIssuetoInspections;
			}

//Data for UpdateQuery Issued to for Inspections Table			
			foreach($projectId as $project){
				$selIssuetoInspections = 'issued_to_progress_monitoring_id,progress_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_progress_monitoring WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and created_date!=last_modified_date");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					$iPadQueryIssuetoInspections = '';
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_progress_monitoring_id'];

						$iPadQueryIssuetoInspections  .= "update issued_to_for_progress_monitoring set progress_id='".$db->dataFilter($rowIssuetoInspections['progress_id'])."', issued_to_name='".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."', last_modified_date='".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."', last_modified_by='".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."', created_date='".$db->dataFilter($rowIssuetoInspections['created_date'])."', created_by='".$db->dataFilter($rowIssuetoInspections['created_by'])."', resource_type='".$db->dataFilter($rowIssuetoInspections['resource_type'])."', project_id='".$db->dataFilter($rowIssuetoInspections['project_id'])."', is_deleted='".$db->dataFilter($rowIssuetoInspections['is_deleted'])."' where global_id='".$db->dataFilter($rowIssuetoInspections['global_id'])."';\r\n";

					}
					$db->createFile('issued_to_for_progress_monitoring_update.txt', $iPadQueryIssuetoInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
				#echo $iPadQueryIssuetoInspections;
			}

//Data for add Inspection Issue Table			
			foreach($projectId as $project){
				$selInspectionIssueTo = 'issue_to_id, project_id, issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionIssueTo = mysql_query("SELECT ".$selInspectionIssueTo." FROM inspection_issue_to WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if(mysql_num_rows($rsInspectionIssueTo) > 0){
					$iPadQueryInspectionIssueTo = '';
					while($rowInspectionIssueTo = mysql_fetch_assoc($rsInspectionIssueTo)){
						$rowInspectionIssueTo['global_id'] = $rowInspectionIssueTo['issue_to_id'];

						$iPadQueryInspectionIssueTo  .= "VALUES('".$db->dataFilter($rowInspectionIssueTo['issue_to_name'])."', '".$db->dataFilter($rowInspectionIssueTo['last_modified_date'])."', '".$db->dataFilter($rowInspectionIssueTo['last_modified_by'])."', '".$db->dataFilter($rowInspectionIssueTo['created_date'])."', '".$db->dataFilter($rowInspectionIssueTo['created_by'])."', '".$db->dataFilter($rowInspectionIssueTo['resource_type'])."', '".$db->dataFilter($rowInspectionIssueTo['global_id'])."', '".$db->dataFilter($rowInspectionIssueTo['project_id'])."', '".$db->dataFilter($rowInspectionIssueTo['is_deleted'])."');\r\n";

					}
					$db->createFile('inspection_issue_to_add.txt', $iPadQueryInspectionIssueTo, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Inspection Issue Table			
			foreach($projectId as $project){
				$selInspectionIssueTo = 'issue_to_id, project_id, issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionIssueTo = mysql_query("SELECT ".$selInspectionIssueTo." FROM inspection_issue_to WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
				if(mysql_num_rows($rsInspectionIssueTo) > 0){
					$iPadQueryInspectionIssueTo = '';
					while($rowInspectionIssueTo = mysql_fetch_assoc($rsInspectionIssueTo)){
						$rowInspectionIssueTo['global_id'] = $rowInspectionIssueTo['issue_to_id'];

						$iPadQueryInspectionIssueTo  .= "update inspection_issue_to set issue_to_name='".$db->dataFilter($rowInspectionIssueTo['issue_to_name'])."', last_modified_date='".$db->dataFilter($rowInspectionIssueTo['last_modified_date'])."', last_modified_by='".$db->dataFilter($rowInspectionIssueTo['last_modified_by'])."', resource_type='".$db->dataFilter($rowInspectionIssueTo['resource_type'])."', is_deleted='".$db->dataFilter($rowInspectionIssueTo['is_deleted'])."' where global_id='".$db->dataFilter($rowInspectionIssueTo['global_id'])."';\r\n";

					}
					$db->createFile('inspection_issue_to_update.txt', $iPadQueryInspectionIssueTo, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for Issued to for Inspections Table			
			foreach($projectId as $project){
				$selIssuetoInspections = 'issued_to_inspections_id, project_id, inspection_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, inspection_status, cost_attribute, inspection_fixed_by_date';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					$iPadQueryIssuetoInspections = '';
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_inspections_id'];

						$iPadQueryIssuetoInspections  .= "VALUES('".$db->dataFilter($rowIssuetoInspections['project_id'])."', '".$db->dataFilter($rowIssuetoInspections['inspection_id'])."', '".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."', '".$db->dataFilter($rowIssuetoInspections['created_date'])."', '".$db->dataFilter($rowIssuetoInspections['created_by'])."', '".$db->dataFilter($rowIssuetoInspections['resource_type'])."', '".$db->dataFilter($rowIssuetoInspections['global_id'])."', '".$db->dataFilter($rowIssuetoInspections['is_deleted'])."', '".$db->dataFilter($rowIssuetoInspections['inspection_fixed_by_date'])."', '".$db->dataFilter($rowIssuetoInspections['cost_attribute'])."', '".$db->dataFilter($rowIssuetoInspections['inspection_status'])."', '".$db->dataFilter($rowIssuetoInspections['closed_date'])."');\r\n";

					}
					$db->createFile('issued_to_for_inspections_add.txt', $iPadQueryIssuetoInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for Issued to for Inspections Table			
			foreach($projectId as $project){
				$selIssuetoInspections = 'issued_to_inspections_id, project_id, inspection_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, inspection_status, cost_attribute, inspection_fixed_by_date';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					$iPadQueryIssuetoInspections = '';
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_inspections_id'];

						$iPadQueryIssuetoInspections  .= "update issued_to_for_inspections set project_id='".$db->dataFilter($rowIssuetoInspections['project_id'])."', inspection_id='".$db->dataFilter($rowIssuetoInspections['inspection_id'])."', issued_to_name='".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."', last_modified_date='".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."', last_modified_by='".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."', created_date='".$db->dataFilter($rowIssuetoInspections['created_date'])."', created_by='".$db->dataFilter($rowIssuetoInspections['created_by'])."', resource_type='".$db->dataFilter($rowIssuetoInspections['resource_type'])."', is_deleted='".$db->dataFilter($rowIssuetoInspections['is_deleted'])."', inspection_fixed_by_date='".$db->dataFilter($rowIssuetoInspections['inspection_fixed_by_date'])."', cost_attribute='".$db->dataFilter($rowIssuetoInspections['cost_attribute'])."', inspection_status='".$db->dataFilter($rowIssuetoInspections['inspection_status'])."', closed_date='".$db->dataFilter($rowIssuetoInspections['closed_date'])."' where global_id='".$db->dataFilter($rowIssuetoInspections['global_id'])."';\r\n";
					}
					$db->createFile('issued_to_for_inspections_update.txt', $iPadQueryIssuetoInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for Inspection Graphics Table			
			foreach($projectId as $project){
				$selInspectionInspectedBy = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					$iPadQueryInspectionInspectedBy = '';
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['graphic_id'];

						$iPadQueryInspectionInspectedBy  .= "VALUES('".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_type'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_title'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_name'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."', '".$db->dataFilter($rowInspectionInspectedBy['global_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['project_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."');\r\n";

					}
					$db->createFile('inspection_graphics_add.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

			foreach($projectId as $project){
				$selInspectionInspectedBy = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					$iPadQueryInspectionInspectedBy = '';
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['graphic_id'];

						$iPadQueryInspectionInspectedBy  .= "update inspection_graphics set inspection_id='".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."', graphic_type='".$db->dataFilter($rowInspectionInspectedBy['graphic_type'])."', graphic_title='".$db->dataFilter($rowInspectionInspectedBy['graphic_title'])."', graphic_name='".$db->dataFilter($rowInspectionInspectedBy['graphic_name'])."', last_modified_date='".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."', last_modified_by='".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."', created_date='".$db->dataFilter($rowInspectionInspectedBy['created_date'])."', created_by='".$db->dataFilter($rowInspectionInspectedBy['created_by'])."', resource_type='".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."', project_id='".$db->dataFilter($rowInspectionInspectedBy['project_id'])."', is_deleted='".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."' where global_id='".$db->dataFilter($rowInspectionInspectedBy['global_id'])."';\r\n";
					}
					$db->createFile('inspection_graphics_update.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for User Permissions Table			
				$iPadQueryInspectionInspectedBy = '';
				$selInspectionInspectedBy = '`id`, `user_id`, `permission_name`, `is_allow`, `created_by`, `created_date`, `last_modified_date`, `last_modified_by`, `resource_type`, `is_deleted`';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM user_permission WHERE user_id = '".$userId."'  and created_date >= '".$lastModifiedDate."' and is_deleted=0");
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['id'];

						$iPadQueryInspectionInspectedBy  .= " VALUES( '".$db->dataFilter($rowInspectionInspectedBy['user_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['permission_name'])."', '".$db->dataFilter($rowInspectionInspectedBy['is_allow'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."', '".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."', '".$db->dataFilter($rowInspectionInspectedBy['global_id'])."');\r\n";

					}
					$db->createFile('user_permission_add.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
				}

				$iPadQueryInspectionInspectedBy = '';
				$selInspectionInspectedBy = '`id`, `user_id`, `permission_name`, `is_allow`, `created_by`, `created_date`, `last_modified_date`, `last_modified_by`, `resource_type`, `is_deleted`';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM user_permission WHERE user_id = '".$userId."'  and last_modified_date >= '".$lastModifiedDate."' and last_modified_date!=created_date");
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['id'];

						$iPadQueryInspectionInspectedBy  .= " update user_permission set user_id='".$db->dataFilter($rowInspectionInspectedBy['user_id'])."', permission_name='".$db->dataFilter($rowInspectionInspectedBy['permission_name'])."', is_allow='".$db->dataFilter($rowInspectionInspectedBy['is_allow'])."', last_modified_date='".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."', last_modified_by='".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."', created_date='".$db->dataFilter($rowInspectionInspectedBy['created_date'])."', created_by='".$db->dataFilter($rowInspectionInspectedBy['created_by'])."', resource_type='".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."', is_deleted='".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."' where global_id='".$db->dataFilter($rowInspectionInspectedBy['global_id'])."'\r\n";

					}
					$db->createFile('user_permission_update.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
				}

//Data for add Inspection Graphics Table			
			/*foreach($projectId as $project){
				$selInspectionInspectedBy = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' and created_date > '".$lastModifiedDate."'");
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					$iPadQueryInspectionInspectedBy = '';
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['graphic_id'];

						$iPadQueryInspectionInspectedBy  .= "VALUES('".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_type'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_title'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_name'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."', '".$db->dataFilter($rowInspectionInspectedBy['global_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['project_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."');\r\n";

					}
					$db->createFile('inspection_graphics_add.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Inspection Graphics Table			
			foreach($projectId as $project){
				$selInspectionInspectedBy = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' and last_modified_date > '".$lastModifiedDate."'");
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					$iPadQueryInspectionInspectedBy = '';
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['graphic_id'];

						$iPadQueryInspectionInspectedBy  .= "VALUES('".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_type'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_title'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_name'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."', '".$db->dataFilter($rowInspectionInspectedBy['global_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['project_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."');\r\n";

					}
					$db->createFile('inspection_graphics_update.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}*/
		}
		$zipSource = EXPORTFILEPATH.'/download/';
		$db->createFile('last_modified_date.txt', $date_lmd, EXPORTFILEPATH.'/download/');//Write File Here 

		$zipFileName = $db->updateExportTable(EXPORTFILEPATH . '/download/' . $zipFileName);
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

function createJsonFile($db, $path, $tablename, $column_names, $dataarray)
{
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