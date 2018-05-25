<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');
define("EXPORTFILEPATH", '../sync/Export');
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
	if($db->hashAuth($globalId, $authHash)){}else{
		$output = array(
			'status' => false,
			'message' => 'User Authentication Fail',
			'data' => ''
		);
		echo '['.json_encode($output).']';
		die;
	}
//Remove Previous Files
	$db->recursive_remove_directory(EXPORTFILEPATH.'/download');
//Add New Files
	@mkdir(EXPORTFILEPATH.'/download/', 0777);
	
	if(is_dir('../sync/Export/')){
	//Add New Files
		if(empty($lastModifiedDate)){
//Data for User Project Table
			$selUserProject = 'project_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';

			$rsUserProject = mysql_query("SELECT ".$selUserProject." FROM user_projects WHERE user_id = '".$globalId."'");
			if(mysql_num_rows($rsUserProject) > 0){
				$iPadQueryUserProject = '';
				while($rowUserProject = mysql_fetch_assoc($rsUserProject)){
					$rowUserProject['global_id'] = $rowUserProject['project_id'];

					$iPadQueryUserProject .= "VALUES('".$db->dataFilter($rowUserProject['global_id'])."','".$db->dataFilter($rowUserProject['user_id'])."', '".$db->dataFilter($rowUserProject['project_name'])."', '".$db->dataFilter($rowUserProject['project_type'])."', '".$db->dataFilter($rowUserProject['project_address_line1'])."', '".$db->dataFilter($rowUserProject['project_address_line2'])."', '".$db->dataFilter($rowUserProject['project_suburb'])."', '".$db->dataFilter($rowUserProject['project_state'])."', '".$db->dataFilter($rowUserProject['project_postcode'])."', '".$db->dataFilter($rowUserProject['project_country'])."', '".$db->dataFilter($rowUserProject['last_modified_date'])."', '".$db->dataFilter($rowUserProject['last_modified_by'])."', '".$db->dataFilter($rowUserProject['created_date'])."', '".$db->dataFilter($rowUserProject['created_by'])."', '".$db->dataFilter($rowUserProject['resource_type'])."', '".$db->dataFilter($rowUserProject['global_id'])."', '".$db->dataFilter($rowUserProject['is_deleted'])."');\r\n";

				}
				$db->createFile('user_projects_add.txt', $iPadQueryUserProject, EXPORTFILEPATH.'/download/');//Write File Here 
			}

//Data for Standard Defect Table
			$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			foreach($projectId as $project){
				$selStandardDefect = 'standard_defect_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsStandardDefect = mysql_query("SELECT ".$selStandardDefect." FROM standard_defects WHERE project_id = '".$project['project_id']."'");
				if(mysql_num_rows($rsStandardDefect) > 0){
					$iPadQueryStandardDefect = '';
					while($rowStandardDefect = mysql_fetch_assoc($rsStandardDefect)){
						$rowStandardDefect['global_id'] = $rowStandardDefect['standard_defect_id'];

						$iPadQueryStandardDefect .= "VALUES('".$db->dataFilter($rowStandardDefect['project_id'])."', '".$db->dataFilter($rowStandardDefect['description'])."', '".$db->dataFilter($rowStandardDefect['last_modified_date'])."', '".$db->dataFilter($rowStandardDefect['last_modified_by'])."', '".$db->dataFilter($rowStandardDefect['created_date'])."', '".$db->dataFilter($rowStandardDefect['created_by'])."', '".$db->dataFilter($rowStandardDefect['resource_type'])."', '".$db->dataFilter($rowStandardDefect['global_id'])."', '".$db->dataFilter($rowStandardDefect['is_deleted'])."');\r\n";

					}
				$db->createFile('standard_deffects_add.txt', $iPadQueryStandardDefect, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for Project Location Table			
			foreach($projectId as $project){
				$selProjectLocation = 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_locations WHERE project_id = '".$project['project_id']."'");
				if(mysql_num_rows($rsProjectLocation) > 0){
					$iPadQueryProjectLocation = '';
					while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
						$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];

						$iPadQueryProjectLocation  .= "VALUES('".$db->dataFilter($rowProjectLocation['global_id'])."','".$db->dataFilter($rowProjectLocation['project_id'])."', '".$db->dataFilter($rowProjectLocation['location_parent_id'])."', '".$db->dataFilter($rowProjectLocation['location_title'])."', '".$db->dataFilter($rowProjectLocation['last_modified_date'])."', '".$db->dataFilter($rowProjectLocation['last_modified_by'])."', '".$db->dataFilter($rowProjectLocation['created_date'])."', '".$db->dataFilter($rowProjectLocation['created_by'])."', '".$db->dataFilter($rowProjectLocation['resource_type'])."', '".$db->dataFilter($rowProjectLocation['global_id'])."', '".$db->dataFilter($rowProjectLocation['is_deleted'])."');\r\n";

					}
					$db->createFile('project_locations_add.txt', $iPadQueryProjectLocation, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for Project Inspections Table			
			foreach($projectId as $project){
				$selProjectInspections = 'inspection_id, project_id, location_id, inspection_date_raised, inspection_status, inspection_inspected_by, inspection_fixed_by_date, inspection_type, inspection_map_address, inspection_description, inspection_priority, inspection_notes, inspection_sign_image, inspection_location, cost_attribute, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections WHERE project_id = '".$project['project_id']."'");
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

						$iPadQueryrsProjectInspections  .= "VALUES('".$db->dataFilter($rowProjectInspections['global_id'])."', '".$db->dataFilter($rowProjectInspections['location_id'])."', '".$db->dataFilter($date_raised)."', '".$db->dataFilter($rowProjectInspections['inspection_status'])."', '".$db->dataFilter($rowProjectInspections['inspection_inspected_by'])."', '".$db->dataFilter($inspection_fixed_by_date)."', '".$db->dataFilter($rowProjectInspections['inspection_type'])."', '".$db->dataFilter($rowProjectInspections['inspection_map_address'])."', '".$db->dataFilter($rowProjectInspections['inspection_description'])."', '".$db->dataFilter($rowProjectInspections['inspection_priority'])."', '".$db->dataFilter($rowProjectInspections['inspection_notes'])."', '".$db->dataFilter($rowProjectInspections['inspection_sign_image'])."', '".$db->dataFilter($rowProjectInspections['inspection_location'])."', '".$db->dataFilter($rowProjectInspections['cost_attribute'])."', '".$db->dataFilter($rowProjectInspections['last_modified_date'])."', '".$db->dataFilter($rowProjectInspections['last_modified_by'])."', '".$db->dataFilter($rowProjectInspections['created_date'])."', '".$db->dataFilter($rowProjectInspections['created_by'])."', '".$db->dataFilter($rowProjectInspections['resource_type'])."', '".$db->dataFilter($rowProjectInspections['global_id'])."', '".$db->dataFilter($rowProjectInspections['is_deleted'])."', '".$db->dataFilter($rowProjectInspections['project_id'])."');\r\n";

					}
					$db->createFile('project_inspection_add.txt', $iPadQueryrsProjectInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for Progress Monitoring Table			
			foreach($projectId as $project){
				$selProgressMonitoring = 'progress_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProgressMonitoring = mysql_query("SELECT ".$selProgressMonitoring." FROM progress_monitoring WHERE project_id = '".$project['project_id']."'");
				if(mysql_num_rows($rsProgressMonitoring) > 0){
					$iPadQueryProgressMonitoring = '';
					while($rowProgressMonitoring = mysql_fetch_assoc($rsProgressMonitoring)){
						$rowProgressMonitoring['global_id'] = $rowProgressMonitoring['progress_id'];

						$iPadQueryProgressMonitoring  .= "VALUES('".$db->dataFilter($rowProgressMonitoring['project_id'])."', '".$db->dataFilter($rowProgressMonitoring['location_id'])."', '".$db->dataFilter($rowProgressMonitoring['sub_location_id'])."', '".$db->dataFilter($rowProgressMonitoring['task'])."', '".$db->dataFilter($rowProgressMonitoring['start_date'])."', '".$db->dataFilter($rowProgressMonitoring['end_date'])."', '".$db->dataFilter($rowProgressMonitoring['last_modified_date'])."', '".$db->dataFilter($rowProgressMonitoring['last_modified_by'])."', '".$db->dataFilter($rowProgressMonitoring['created_date'])."', '".$db->dataFilter($rowProgressMonitoring['created_by'])."', '".$db->dataFilter($rowProgressMonitoring['resource_type'])."', '".$db->dataFilter($rowProgressMonitoring['global_id'])."', '".$db->dataFilter($rowProgressMonitoring['is_deleted'])."', '".$db->dataFilter($rowProgressMonitoring['status'])."', '".$db->dataFilter($rowProgressMonitoring['percentage'])."');\r\n";
					}
					$db->createFile('progress_monitoring_add.txt', $iPadQueryProgressMonitoring, EXPORTFILEPATH.'/download/');//Write File Here  
				}
			}

//Data for Progress Monitoring Update Table			
			foreach($projectId as $project){
				$selProgressMonitoringUpdate = 'update_id, project_id, progress_id, percentage, status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProgressMonitoringUpdate = mysql_query("SELECT ".$selProgressMonitoringUpdate." FROM progress_monitoring_update WHERE project_id = '".$project['project_id']."'");
				if(mysql_num_rows($rsProgressMonitoringUpdate) > 0){
					$iPadQueryProgressMonitoringUpdate = '';
					while($rowProgressMonitoringUpdate = mysql_fetch_assoc($rsProgressMonitoringUpdate)){
						$rowProgressMonitoringUpdate['global_id'] = $rowProgressMonitoringUpdate['update_id'];

						$iPadQueryProgressMonitoringUpdate  .= "VALUES('".$db->dataFilter($rowProgressMonitoringUpdate['progress_id'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['project_id'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['percentage'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['status'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['last_modified_date'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['last_modified_by'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['created_date'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['created_by'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['resource_type'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['global_id'])."', '".$db->dataFilter($rowProgressMonitoringUpdate['is_deleted'])."');\r\n";

					}
					$db->createFile('progress_monitoring_updates_add.txt', $iPadQueryProgressMonitoringUpdate, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for Issued to for Inspections Table			
			foreach($projectId as $project){
				$selIssuetoInspections = 'issued_to_inspections_id, project_id, inspection_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."'");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					$iPadQueryIssuetoInspections = '';
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_inspections_id'];

						$iPadQueryIssuetoInspections  .= "VALUES('".$db->dataFilter($rowIssuetoInspections['project_id'])."', '".$db->dataFilter($rowIssuetoInspections['inspection_id'])."', '".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."', '".$db->dataFilter($rowIssuetoInspections['created_date'])."', '".$db->dataFilter($rowIssuetoInspections['created_by'])."', '".$db->dataFilter($rowIssuetoInspections['resource_type'])."', '".$db->dataFilter($rowIssuetoInspections['global_id'])."', '".$db->dataFilter($rowIssuetoInspections['is_deleted'])."');\r\n";

					}
					$db->createFile('issued_to_for_inspections_add.txt', $iPadQueryIssuetoInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for Inspection Issue Table			
			foreach($projectId as $project){
				$selInspectionIssueTo = 'issue_to_id, project_id, issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionIssueTo = mysql_query("SELECT ".$selInspectionIssueTo." FROM inspection_issue_to WHERE project_id = '".$project['project_id']."'");
				if(mysql_num_rows($rsInspectionIssueTo) > 0){
					$iPadQueryInspectionIssueTo = '';
					while($rowInspectionIssueTo = mysql_fetch_assoc($rsInspectionIssueTo)){
						$rowInspectionIssueTo['global_id'] = $rowInspectionIssueTo['issue_to_id'];

						$iPadQueryInspectionIssueTo  .= "VALUES('".$db->dataFilter($rowInspectionIssueTo['issue_to_name'])."', '".$db->dataFilter($rowInspectionIssueTo['last_modified_date'])."', '".$db->dataFilter($rowInspectionIssueTo['last_modified_by'])."', '".$db->dataFilter($rowInspectionIssueTo['created_date'])."', '".$db->dataFilter($rowInspectionIssueTo['created_by'])."', '".$db->dataFilter($rowInspectionIssueTo['resource_type'])."', '".$db->dataFilter($rowInspectionIssueTo['global_id'])."', '".$db->dataFilter($rowInspectionIssueTo['project_id'])."', '".$db->dataFilter($rowInspectionIssueTo['is_deleted'])."');\r\n";

					}
					$db->createFile('inspection_issue_to_add.txt', $iPadQueryInspectionIssueTo, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for Inspection Graphics Table			
			foreach($projectId as $project){
				$selInspectionInspectedBy = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."'");
				if(mysql_num_rows($rsInspectionInspectedBy) > 0){
					$iPadQueryInspectionInspectedBy = '';
					while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
						$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['graphic_id'];

						$iPadQueryInspectionInspectedBy  .= "VALUES('".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_type'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_title'])."', '".$db->dataFilter($rowInspectionInspectedBy['graphic_name'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_date'])."', '".$db->dataFilter($rowInspectionInspectedBy['created_by'])."', '".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."', '".$db->dataFilter($rowInspectionInspectedBy['global_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['project_id'])."', '".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."');\r\n";

					}
					$db->createFile('inspection_graphics_add.txt', $iPadQueryInspectionInspectedBy, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

		}else{//Modified Date is comes
//Data for add User Project Table
			$selUserProject = 'project_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
			$rsUserProject = mysql_query("SELECT ".$selUserProject." FROM user_projects WHERE user_id = '".$globalId."' and created_date > '".$lastModifiedDate."'");
			if(mysql_num_rows($rsUserProject) > 0){
				$iPadQueryUserProject = '';
				while($rowUserProject = mysql_fetch_assoc($rsUserProject)){
					#$rowUserProject['user_id'] = $userId;
					$rowUserProject['global_id'] = $rowUserProject['project_id'];

					$iPadQueryUserProject .= "VALUES('".$db->dataFilter($rowUserProject['user_id'])."', '".$db->dataFilter($rowUserProject['project_name'])."', '".$db->dataFilter($rowUserProject['project_type'])."', '".$db->dataFilter($rowUserProject['project_address_line1'])."', '".$db->dataFilter($rowUserProject['project_address_line2'])."', '".$db->dataFilter($rowUserProject['project_suburb'])."', '".$db->dataFilter($rowUserProject['project_state'])."', '".$db->dataFilter($rowUserProject['project_postcode'])."', '".$db->dataFilter($rowUserProject['project_country'])."', '".$db->dataFilter($rowUserProject['last_modified_date'])."', '".$db->dataFilter($rowUserProject['last_modified_by'])."', '".$db->dataFilter($rowUserProject['created_date'])."', '".$db->dataFilter($rowUserProject['created_by'])."', '".$db->dataFilter($rowUserProject['resource_type'])."', '".$db->dataFilter($rowUserProject['global_id'])."', '".$db->dataFilter($rowUserProject['is_deleted'])."');\r\n";
				}
				$db->createFile('user_projects_add.txt', $iPadQueryUserProject, EXPORTFILEPATH.'/download/');//Write File Here 
			}

//Data for UpdateQuery User Project Table
			$selUserProject = 'project_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
			$rsUserProject = mysql_query("SELECT ".$selUserProject." FROM user_projects WHERE user_id = '".$globalId."' and last_modified_date > '".$lastModifiedDate."'");
			if(mysql_num_rows($rsUserProject) > 0){
				$iPadQueryUserProject = '';
				while($rowUserProject = mysql_fetch_assoc($rsUserProject)){
					#$rowUserProject['user_id'] = $userId;
					$rowUserProject['global_id'] = $rowUserProject['project_id'];

					$iPadQueryUserProject .= "VALUES('".$db->dataFilter($rowUserProject['global_id'])."','".$db->dataFilter($rowUserProject['user_id'])."', '".$db->dataFilter($rowUserProject['project_name'])."', '".$db->dataFilter($rowUserProject['project_type'])."', '".$db->dataFilter($rowUserProject['project_address_line1'])."', '".$db->dataFilter($rowUserProject['project_address_line2'])."', '".$db->dataFilter($rowUserProject['project_suburb'])."', '".$db->dataFilter($rowUserProject['project_state'])."', '".$db->dataFilter($rowUserProject['project_postcode'])."', '".$db->dataFilter($rowUserProject['project_country'])."', '".$db->dataFilter($rowUserProject['last_modified_date'])."', '".$db->dataFilter($rowUserProject['last_modified_by'])."', '".$db->dataFilter($rowUserProject['created_date'])."', '".$db->dataFilter($rowUserProject['created_by'])."', '".$db->dataFilter($rowUserProject['resource_type'])."', '".$db->dataFilter($rowUserProject['global_id'])."', '".$db->dataFilter($rowUserProject['is_deleted'])."');\r\n";

				}
				$db->createFile('user_projects_update.txt', $iPadQueryUserProject, EXPORTFILEPATH.'/download/');//Write File Here 
			}

//Data for add Standard Defect Table
			$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			foreach($projectId as $project){
				$selStandardDefect = 'standard_defect_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsStandardDefect = mysql_query("SELECT ".$selStandardDefect." FROM standard_defects WHERE project_id = '".$project['project_id']."' and created_date > '".$lastModifiedDate."'");
				if(mysql_num_rows($rsStandardDefect) > 0){
					$iPadQueryStandardDefect = '';
					while($rowStandardDefect = mysql_fetch_assoc($rsStandardDefect)){
						$rowStandardDefect['global_id'] = $rowStandardDefect['standard_defect_id'];

						$iPadQueryStandardDefect .= "VALUES('".$db->dataFilter($rowStandardDefect['project_id'])."', '".$db->dataFilter($rowStandardDefect['description'])."', '".$db->dataFilter($rowStandardDefect['last_modified_date'])."', '".$db->dataFilter($rowStandardDefect['last_modified_by'])."', '".$db->dataFilter($rowStandardDefect['created_date'])."', '".$db->dataFilter($rowStandardDefect['created_by'])."', '".$db->dataFilter($rowStandardDefect['resource_type'])."', '".$db->dataFilter($rowStandardDefect['global_id'])."', '".$db->dataFilter($rowStandardDefect['is_deleted'])."');\r\n";

					}
					$db->createFile('standard_deffects_add.txt', $iPadQueryStandardDefect, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Standard Defect Table
			#$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			foreach($projectId as $project){
				$selStandardDefect = 'standard_defect_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsStandardDefect = mysql_query("SELECT ".$selStandardDefect." FROM standard_defects WHERE project_id = '".$project['project_id']."' and last_modified_date > '".$lastModifiedDate."'");
				if(mysql_num_rows($rsStandardDefect) > 0){
					$iPadQueryStandardDefect = '';
					while($rowStandardDefect = mysql_fetch_assoc($rsStandardDefect)){
						$rowStandardDefect['global_id'] = $rowStandardDefect['standard_defect_id'];

						$iPadQueryStandardDefect .= "VALUES('".$db->dataFilter($rowStandardDefect['project_id'])."', '".$db->dataFilter($rowStandardDefect['description'])."', '".$db->dataFilter($rowStandardDefect['last_modified_date'])."', '".$db->dataFilter($rowStandardDefect['last_modified_by'])."', '".$db->dataFilter($rowStandardDefect['created_date'])."', '".$db->dataFilter($rowStandardDefect['created_by'])."', '".$db->dataFilter($rowStandardDefect['resource_type'])."', '".$db->dataFilter($rowStandardDefect['global_id'])."', '".$db->dataFilter($rowStandardDefect['is_deleted'])."');\r\n";

					}
					$db->createFile('standard_deffects_update.txt', $iPadQueryStandardDefect, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for add Project Location Table			
			foreach($projectId as $project){
				$selProjectLocation = 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_locations WHERE project_id = '".$project['project_id']."' and created_date > '".$lastModifiedDate."'");
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
				$rsProjectLocation = mysql_query("SELECT ".$selProjectLocation." FROM project_locations WHERE project_id = '".$project['project_id']."' and last_modified_date > '".$lastModifiedDate."'");
				if(mysql_num_rows($rsProjectLocation) > 0){
					$iPadQueryProjectLocation = '';
					while($rowProjectLocation = mysql_fetch_assoc($rsProjectLocation)){
						$rowProjectLocation['global_id'] = $rowProjectLocation['location_id'];

						$iPadQueryProjectLocation  .= "VALUES('".$db->dataFilter($rowProjectLocation['global_id'])."','".$db->dataFilter($rowProjectLocation['project_id'])."', '".$db->dataFilter($rowProjectLocation['location_parent_id'])."', '".$db->dataFilter($rowProjectLocation['location_title'])."', '".$db->dataFilter($rowProjectLocation['last_modified_date'])."', '".$db->dataFilter($rowProjectLocation['last_modified_by'])."', '".$db->dataFilter($rowProjectLocation['created_date'])."', '".$db->dataFilter($rowProjectLocation['created_by'])."', '".$db->dataFilter($rowProjectLocation['resource_type'])."', '".$db->dataFilter($rowProjectLocation['global_id'])."', '".$db->dataFilter($rowProjectLocation['is_deleted'])."');\r\n";

					}
					$db->createFile('project_locations_update.txt', $iPadQueryProjectLocation, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for add Project Inspections Table			
			foreach($projectId as $project){
				$selProjectInspections = 'inspection_id, project_id, location_id, inspection_date_raised, inspection_status, inspection_inspected_by, inspection_fixed_by_date, inspection_type, inspection_map_address, inspection_description, inspection_priority, inspection_notes, inspection_sign_image, inspection_location, cost_attribute, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections WHERE project_id = '".$project['project_id']."' and created_date > '".$lastModifiedDate."'");
				if(mysql_num_rows($rsProjectInspections) > 0){
					$iPadQueryrsProjectInspections = '';
					while($rowProjectInspections = mysql_fetch_assoc($rsProjectInspections)){
						$rowProjectInspections['global_id'] = $rowProjectInspections['inspection_id'];

						$iPadQueryrsProjectInspections  .= "VALUES('".$db->dataFilter($rowProjectInspections['global_id'])."','".$db->dataFilter($rowProjectInspections['location_id'])."', '".$db->dataFilter($rowProjectInspections['inspection_date_raised'])."', '".$db->dataFilter($rowProjectInspections['inspection_status'])."', '".$db->dataFilter($rowProjectInspections['inspection_inspected_by'])."', '".$db->dataFilter($rowProjectInspections['inspection_fixed_by_date'])."', '".$db->dataFilter($rowProjectInspections['inspection_type'])."', '".$db->dataFilter($rowProjectInspections['inspection_map_address'])."', '".$db->dataFilter($rowProjectInspections['inspection_description'])."', '".$db->dataFilter($rowProjectInspections['inspection_priority'])."', '".$db->dataFilter($rowProjectInspections['inspection_notes'])."', '".$db->dataFilter($rowProjectInspections['inspection_sign_image'])."', '".$db->dataFilter($rowProjectInspections['inspection_location'])."', '".$db->dataFilter($rowProjectInspections['cost_attribute'])."', '".$db->dataFilter($rowProjectInspections['last_modified_date'])."', '".$db->dataFilter($rowProjectInspections['last_modified_by'])."', '".$db->dataFilter($rowProjectInspections['created_date'])."', '".$db->dataFilter($rowProjectInspections['created_by'])."', '".$db->dataFilter($rowProjectInspections['resource_type'])."', '".$db->dataFilter($rowProjectInspections['global_id'])."', '".$db->dataFilter($rowProjectInspections['is_deleted'])."', '".$db->dataFilter($rowProjectInspections['project_id'])."');\r\n";

					}
					$db->createFile('project_inspection_add.txt', $iPadQueryrsProjectInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for UpdateQuery Project Inspections Table			
			foreach($projectId as $project){
				$selProjectInspections = 'inspection_id, project_id, location_id, inspection_date_raised, inspection_status, inspection_inspected_by, inspection_fixed_by_date, inspection_type, inspection_map_address, inspection_description, inspection_priority, inspection_notes, inspection_sign_image, inspection_location, cost_attribute, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections WHERE project_id = '".$project['project_id']."' and last_modified_date > '".$lastModifiedDate."'");
				if(mysql_num_rows($rsProjectInspections) > 0){
					$iPadQueryrsProjectInspections = '';
					while($rowProjectInspections = mysql_fetch_assoc($rsProjectInspections)){
						$rowProjectInspections['global_id'] = $rowProjectInspections['inspection_id'];

						$iPadQueryrsProjectInspections  .= "VALUES('".$db->dataFilter($rowProjectInspections['global_id'])."','".$db->dataFilter($rowProjectInspections['location_id'])."', '".$db->dataFilter($rowProjectInspections['inspection_date_raised'])."', '".$db->dataFilter($rowProjectInspections['inspection_status'])."', '".$db->dataFilter($rowProjectInspections['inspection_inspected_by'])."', '".$db->dataFilter($rowProjectInspections['inspection_fixed_by_date'])."', '".$db->dataFilter($rowProjectInspections['inspection_type'])."', '".$db->dataFilter($rowProjectInspections['inspection_map_address'])."', '".$db->dataFilter($rowProjectInspections['inspection_description'])."', '".$db->dataFilter($rowProjectInspections['inspection_priority'])."', '".$db->dataFilter($rowProjectInspections['inspection_notes'])."', '".$db->dataFilter($rowProjectInspections['inspection_sign_image'])."', '".$db->dataFilter($rowProjectInspections['inspection_location'])."', '".$db->dataFilter($rowProjectInspections['cost_attribute'])."', '".$db->dataFilter($rowProjectInspections['last_modified_date'])."', '".$db->dataFilter($rowProjectInspections['last_modified_by'])."', '".$db->dataFilter($rowProjectInspections['created_date'])."', '".$db->dataFilter($rowProjectInspections['created_by'])."', '".$db->dataFilter($rowProjectInspections['resource_type'])."', '".$db->dataFilter($rowProjectInspections['global_id'])."', '".$db->dataFilter($rowProjectInspections['is_deleted'])."', '".$db->dataFilter($rowProjectInspections['project_id'])."');\r\n";

					}
					$db->createFile('project_inspection_update.txt', $iPadQueryrsProjectInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for add Progress Monitoring Table			
			foreach($projectId as $project){
				$selProgressMonitoring = 'progress_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProgressMonitoring = mysql_query("SELECT ".$selProgressMonitoring." FROM progress_monitoring WHERE project_id = '".$project['project_id']."' and created_date > '".$lastModifiedDate."'");
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
				$selProgressMonitoring = 'progress_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProgressMonitoring = mysql_query("SELECT ".$selProgressMonitoring." FROM progress_monitoring WHERE project_id = '".$project['project_id']."' and last_modified_date > '".$lastModifiedDate."'");
				if(mysql_num_rows($rsProgressMonitoring) > 0){
					$iPadQueryProgressMonitoring = '';
					while($rowProgressMonitoring = mysql_fetch_assoc($rsProgressMonitoring)){
						$rowProgressMonitoring['global_id'] = $rowProgressMonitoring['progress_id'];

						$iPadQueryProgressMonitoring  .= "VALUES('".$db->dataFilter($rowProgressMonitoring['project_id'])."', '".$db->dataFilter($rowProgressMonitoring['location_id'])."', '".$db->dataFilter($rowProgressMonitoring['sub_location_id'])."', '".$db->dataFilter($rowProgressMonitoring['task'])."', '".$db->dataFilter($rowProgressMonitoring['start_date'])."', '".$db->dataFilter($rowProgressMonitoring['end_date'])."', '".$db->dataFilter($rowProgressMonitoring['last_modified_date'])."', '".$db->dataFilter($rowProgressMonitoring['last_modified_by'])."', '".$db->dataFilter($rowProgressMonitoring['created_date'])."', '".$db->dataFilter($rowProgressMonitoring['created_by'])."', '".$db->dataFilter($rowProgressMonitoring['resource_type'])."', '".$db->dataFilter($rowProgressMonitoring['global_id'])."', '".$db->dataFilter($rowProgressMonitoring['is_deleted'])."', '".$db->dataFilter($rowProgressMonitoring['status'])."', '".$db->dataFilter($rowProgressMonitoring['percentage'])."');\r\n";

					}
					$db->createFile('progress_monitoring_update.txt', $iPadQueryProgressMonitoring, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for add Progress Monitoring Update Table			
			foreach($projectId as $project){
				$selProgressMonitoringUpdate = 'update_id, project_id, progress_id, percentage, status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsProgressMonitoringUpdate = mysql_query("SELECT ".$selProgressMonitoringUpdate." FROM progress_monitoring_update WHERE project_id = '".$project['project_id']."' and created_date > '".$lastModifiedDate."'");
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
				$rsProgressMonitoringUpdate = mysql_query("SELECT ".$selProgressMonitoringUpdate." FROM progress_monitoring_update WHERE project_id = '".$project['project_id']."' and last_modified_date > '".$lastModifiedDate."'");
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

//Data for add Issued to for Inspections Table			
			foreach($projectId as $project){
				$selIssuetoInspections = 'issued_to_inspections_id, inspection_id, project_id, issued_to_id, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."' and created_date > '".$lastModifiedDate."'");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					$iPadQueryIssuetoInspections = '';
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_inspections_id'];

						$iPadQueryIssuetoInspections  .= "VALUES('".$db->dataFilter($rowIssuetoInspections['project_id'])."', '".$db->dataFilter($rowIssuetoInspections['inspection_id'])."', '".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."', '".$db->dataFilter($rowIssuetoInspections['created_date'])."', '".$db->dataFilter($rowIssuetoInspections['created_by'])."', '".$db->dataFilter($rowIssuetoInspections['resource_type'])."', '".$db->dataFilter($rowIssuetoInspections['global_id'])."', '".$db->dataFilter($rowIssuetoInspections['is_deleted'])."');\r\n";

					}
					$db->createFile('issued_to_for_inspections_add.txt', $iPadQueryIssuetoInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
				#echo $iPadQueryIssuetoInspections;
			}

//Data for UpdateQuery Issued to for Inspections Table			
			foreach($projectId as $project){
				$selIssuetoInspections = 'issued_to_inspections_id, inspection_id, project_id, issued_to_id, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."' and last_modified_date > '".$lastModifiedDate."'");
				if(mysql_num_rows($rsIssuetoInspections) > 0){
					$iPadQueryIssuetoInspections = '';
					while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
						$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_inspections_id'];

						$iPadQueryIssuetoInspections  .= "VALUES('".$db->dataFilter($rowIssuetoInspections['project_id'])."', '".$db->dataFilter($rowIssuetoInspections['inspection_id'])."', '".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."', '".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."', '".$db->dataFilter($rowIssuetoInspections['created_date'])."', '".$db->dataFilter($rowIssuetoInspections['created_by'])."', '".$db->dataFilter($rowIssuetoInspections['resource_type'])."', '".$db->dataFilter($rowIssuetoInspections['global_id'])."', '".$db->dataFilter($rowIssuetoInspections['is_deleted'])."');\r\n";

					}
					$db->createFile('issued_to_for_inspections_update.txt', $iPadQueryIssuetoInspections, EXPORTFILEPATH.'/download/');//Write File Here 
				}
				#echo $iPadQueryIssuetoInspections;
			}

//Data for add Inspection Issue Table			
			foreach($projectId as $project){
				$selInspectionIssueTo = 'issue_to_id, project_id, issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
				$rsInspectionIssueTo = mysql_query("SELECT ".$selInspectionIssueTo." FROM inspection_issue_to WHERE project_id = '".$project['project_id']."' and created_date > '".$lastModifiedDate."'");
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
				$rsInspectionIssueTo = mysql_query("SELECT ".$selInspectionIssueTo." FROM inspection_issue_to WHERE project_id = '".$project['project_id']."' and last_modified_date > '".$lastModifiedDate."'");
				if(mysql_num_rows($rsInspectionIssueTo) > 0){
					$iPadQueryInspectionIssueTo = '';
					while($rowInspectionIssueTo = mysql_fetch_assoc($rsInspectionIssueTo)){
						$rowInspectionIssueTo['global_id'] = $rowInspectionIssueTo['issue_to_id'];

						$iPadQueryInspectionIssueTo  .= "VALUES('".$db->dataFilter($rowInspectionIssueTo['issue_to_name'])."', '".$db->dataFilter($rowInspectionIssueTo['last_modified_date'])."', '".$db->dataFilter($rowInspectionIssueTo['last_modified_by'])."', '".$db->dataFilter($rowInspectionIssueTo['created_date'])."', '".$db->dataFilter($rowInspectionIssueTo['created_by'])."', '".$db->dataFilter($rowInspectionIssueTo['resource_type'])."', '".$db->dataFilter($rowInspectionIssueTo['global_id'])."', '".$db->dataFilter($rowInspectionIssueTo['project_id'])."', '".$db->dataFilter($rowInspectionIssueTo['is_deleted'])."');\r\n";

					}
					$db->createFile('inspection_issue_to_update.txt', $iPadQueryInspectionIssueTo, EXPORTFILEPATH.'/download/');//Write File Here 
				}
			}

//Data for add Inspection Graphics Table			
			foreach($projectId as $project){
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
			}
		}
		$zipSource = EXPORTFILEPATH.'/download/';

		$zipFileName = $db->updateExportTable(EXPORTFILEPATH);
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
?>