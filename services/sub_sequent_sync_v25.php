<?php 
//Create New added project Array and user_projects_add.txt here
	$new_project = array();
	$new_project = createUserProjectsFile('user_projects', 'project_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted, is_deleted, user_role, issued_to', $_REQUEST['userId'], 'project_id');

$newProjInspIdsArr = first_sync_acceptedInpsectionIDS($userRoleData, $deviceType);
$newProjInspIds = array();
foreach($newProjInspIdsArr as $key=>$value){
	if ($new_project[$key] != "")
	{
		$newProjInspIds[$key] = join(',', $value);
	}
}
//Get Accepted Ids	
$acceptedInspectionIds = subsequent_sync_acceptedInpsectionIDS($userRoleData, $deviceType);
#print_r($acceptedInspectionIds);die;
//Get Refused Ids
$refusedInspectionIds = subsequent_sync_refusedInpsectionIDS($projectId, $userId, $deviceType);
#print_r($refusedInspectionIds);die;
//inspection related table file write here
//First write inspections file and store selected inspections in an Array for next files
$inspArray = array();
	foreach($projectId as $project){
		$acceptCon = '';
		$acceptConNewProj = '';
		$refuseCon = '';
		$inspList4Role = '';//store inspection ids
		if($acceptedInspectionIds[$project['project_id']] != ''){
			$acceptCon = " AND inspection_id IN (".$acceptedInspectionIds[$project['project_id']].")";
		}else{
			$acceptCon = '';
		}
		if($newProjInspIds[$project['project_id']] != ''){
			$acceptConNewProj = " AND inspection_id IN (".$newProjInspIds[$project['project_id']].")";
		}else{
			$acceptConNewProj = '';
		}
		if($refusedInspectionIds[$project['project_id']] != ''){
			$refuseCon = " AND inspection_id NOT IN (".$refusedInspectionIds[$project['project_id']].")";
		}else{
			$refuseCon = '';
		}
		if ($acceptCon == "" && $acceptConNewProj == "")
		{
			continue;
		}
		$selProjectInspections = 'inspection_id, project_id, location_id, inspection_date_raised, inspection_status, inspection_inspected_by, inspection_fixed_by_date, inspection_type, inspection_map_address, inspection_description, inspection_priority, inspection_notes, inspection_sign_image, inspection_location, inspection_raised_by, cost_attribute, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, original_modified_date, check_list_item_id, check_list_location_id';
		
		if ($new_project[$project['project_id']] == 1){
			$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections  WHERE project_id = '".$project['project_id']."' ".$acceptConNewProj);
		}else{
			$rsProjectInspections = mysql_query("SELECT ".$selProjectInspections." FROM project_inspections  WHERE project_id = '".$project['project_id']."' ".$acceptCon." ".$refuseCon);
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

				$iPadQueryrsProjectInspections[] = $rowProjectInspections['global_id'] . "##VALUES(\"".$db->dataFilter($rowProjectInspections['global_id'])."\",\"".$db->dataFilter($rowProjectInspections['location_id'])."\", \"".$db->dataFilter($date_raised)."\", \"".$db->dataFilter($rowProjectInspections['inspection_status'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_inspected_by'])."\", \"".$db->dataFilter($inspection_fixed_by_date)."\", \"".$db->dataFilter($rowProjectInspections['inspection_type'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_map_address'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_description'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_priority'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_notes'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_sign_image'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_location'])."\", \"".$db->dataFilter($rowProjectInspections['cost_attribute'])."\", \"".$db->dataFilter($rowProjectInspections['last_modified_date'])."\", \"".$db->dataFilter($rowProjectInspections['last_modified_by'])."\", \"".$db->dataFilter($rowProjectInspections['created_date'])."\", \"".$db->dataFilter($rowProjectInspections['created_by'])."\", \"".$db->dataFilter($rowProjectInspections['resource_type'])."\", \"".$db->dataFilter($rowProjectInspections['global_id'])."\", \"".$db->dataFilter($rowProjectInspections['is_deleted'])."\", \"".$db->dataFilter($rowProjectInspections['project_id'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_raised_by'])."\", \"".$db->dataFilter($rowProjectInspections['original_modified_date'])."\", \"".$db->dataFilter($rowProjectInspections['check_list_item_id'])."\", \"".$db->dataFilter($rowProjectInspections['check_list_location_id'])."\");\r\n";
			}
			$db->createFile('project_inspection_add.txt', join('', $iPadQueryrsProjectInspections), EXPORTFILEPATH.'/download/');//Write File Here 
		}
		$inspArray[$project['project_id']] = $inspList4Role;
	}
//Remain files start here
	foreach($projectId as $project){
//project inspection graphic table data here
		if ($inspArray[$project['project_id']] == "")
		{
			continue;
		}
		$selInspectionInspectedBy = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, original_modified_date';
		
		if ($new_project[$project['project_id']] == 1){
			$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND inspection_id IN (".$inspArray[$project['project_id']].")");
		}else{
			$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND inspection_id IN (".$inspArray[$project['project_id']].")");
		}
		
		if(mysql_num_rows($rsInspectionInspectedBy) > 0){
			$iPadQueryInspectionInspectedBy = array();
			while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
				$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['graphic_id'];
				
				$iPadQueryInspectionInspectedBy[] = $rowInspectionInspectedBy['global_id'] . "##VALUES(\"".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['graphic_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['graphic_title'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['graphic_name'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['original_modified_date'])."\");\r\n";
			}
			$db->createFile('inspection_graphics_add.txt', join('', $iPadQueryInspectionInspectedBy), EXPORTFILEPATH.'/download/');//Write File Here 
		}

//inspection graphic table data here
		$selInspectionInspectedBy = 'insepection_check_list_id, project_id, inspection_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted';
		
		if ($new_project[$project['project_id']] == 1){
			$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_check_list WHERE project_id = '".$project['project_id']."' AND inspection_id IN (".$inspArray[$project['project_id']].")");
		}else{
			$rsInspectionInspectedBy = mysql_query("SELECT ".$selInspectionInspectedBy." FROM inspection_check_list WHERE project_id = '".$project['project_id']."' AND inspection_id IN (".$inspArray[$project['project_id']].")");
		}
		
		if(mysql_num_rows($rsInspectionInspectedBy) > 0){
			$iPadQueryInspectionInspectedBy = array();
			while($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
				$rowInspectionInspectedBy['global_id'] = $rowInspectionInspectedBy['insepection_check_list_id'];
				
				$iPadQueryInspectionInspectedBy[] = $rowInspectionInspectedBy['global_id'] . "##VALUES(\"".$db->dataFilter($rowInspectionInspectedBy['project_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['inspection_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['check_list_items_status'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_date'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['created_by'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['resource_type'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['global_id'])."\", \"".$db->dataFilter($rowInspectionInspectedBy['is_deleted'])."\");\r\n";
			}
			$db->createFile('inspection_check_list_add.txt', join('', $iPadQueryInspectionInspectedBy), EXPORTFILEPATH.'/download/');//Write File Here 
		}

//Issued to for inspection table data here
		$selIssuetoInspections = 'issued_to_inspections_id, project_id, inspection_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, inspection_status, cost_attribute, inspection_fixed_by_date, cost_impact_type, original_modified_date, time_impact';
		
		if ($new_project[$project['project_id']] == 1){
			$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."' AND inspection_id IN (".$inspArray[$project['project_id']].")");
		}else{
			$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."' AND inspection_id IN (".$inspArray[$project['project_id']].")");
		}
		
		if(mysql_num_rows($rsIssuetoInspections) > 0){
			$iPadQueryIssuetoInspections = array();
			while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
				$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_inspections_id'];
				
				$iPadQueryIssuetoInspections[] = $rowIssuetoInspections['global_id'] . "##VALUES(\"".$db->dataFilter($rowIssuetoInspections['project_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['inspection_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['resource_type'])."\", \"".$db->dataFilter($rowIssuetoInspections['global_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['is_deleted'])."\", \"".$db->dataFilter($rowIssuetoInspections['inspection_fixed_by_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['cost_attribute'])."\", \"".$db->dataFilter($rowIssuetoInspections['inspection_status'])."\", \"".$db->dataFilter($rowIssuetoInspections['closed_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['cost_impact_type'])."\", \"".$db->dataFilter($rowIssuetoInspections['original_modified_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['time_impact'])."\");\r\n";
			}
			$db->createFile('issued_to_for_inspections_add.txt', join('', $iPadQueryIssuetoInspections), EXPORTFILEPATH.'/download/');//Write File Here 
		}
//Inspection related Data Here 
	}
//user_projects_update.txt Create Here			
	subsequent_sync_userProject_update('user_projects', 'project_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, user_role, issued_to', $userId);
	
//MetaData Files-------------------------------------- Start Here -----------------------------------------*/
//standard_defects_add.txt Create Here
	subsequent_sync_metaData_add('standard_defects', 'project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, standard_defect_id, is_deleted, tag, issued_to, fix_by_days', $projectId, $new_project,'standard_defect_id');
	
//standard_defects_update.txt Create Here
	subsequent_sync_metaData_update('standard_defects', 'standard_defect_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, tag, issued_to, fix_by_days', $projectId);
	

//project_locations_add.txt Create Here
	subsequent_sync_metaData_add('project_locations', 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, location_id, is_deleted', $projectId, $new_project, 'location_id');
	
//project_locations_update.txt Create Here
	subsequent_sync_metaData_update('project_locations', 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $projectId);

//project_monitoring_locations_add.txt Create Here
	subsequent_sync_metaData_add('project_monitoring_locations', 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, location_id, is_deleted', $projectId, $new_project, 'location_id');
	
//project_monitoring_locations_update.txt Create Here
	subsequent_sync_metaData_update('project_monitoring_locations', 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $projectId);
	

//inspection_issue_to_add.txt Create Here
	subsequent_sync_metaData_add('inspection_issue_to', 'issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, issue_to_id, project_id, is_deleted, tag', $projectId, $new_project,"issue_to_id");
	
//inspection_issue_to_update.txt Create Here
	subsequent_sync_metaData_update('inspection_issue_to', 'issue_to_id, project_id, issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, tag', $projectId);


//user_permission_add.txt Create Here
	subsequent_sync_metaData_add('user_permission', 'user_id, permission_name, is_allow, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted, id', $projectId, $new_project,'id');
	
//user_permission_update.txt Create Here
	subsequent_sync_metaData_update('user_permission', 'id, user_id, permission_name, is_allow, created_by, created_date, last_modified_date, last_modified_by, resource_type, is_deleted, project_id', $projectId, 'id');
	

//draw_mgmt_images_add.txt Create Here
	subsequent_sync_metaData_add('draw_mgmt_images', 'project_id, draw_mgmt_images_title, draw_mgmt_images_name, draw_mgmt_images_thumbnail, draw_mgmt_images_description, draw_mgmt_images_tags, last_modified_date, last_modified_by, created_date, created_by, resource_type, draw_mgmt_images_id, is_deleted', $projectId, $new_project,'draw_mgmt_images_id');
	
//draw_mgmt_images_update.txt Create Here
	subsequent_sync_metaData_update('draw_mgmt_images', 'draw_mgmt_images_id, project_id, draw_mgmt_images_title, draw_mgmt_images_name, draw_mgmt_images_thumbnail, draw_mgmt_images_description, draw_mgmt_images_tags, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $projectId);
//MetaData Files-------------------------------------- End Here -----------------------------------------*/


//QAData Files-------------------------------------- Start Here -----------------------------------------*/
//qa_task_locations_add.txt Create Here
	subsequent_sync_qaData_add('qa_task_locations', 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, location_id, is_deleted', $projectId, $new_project, 'location_id');
	
//qa_task_locations_update.txt Create Here
	subsequent_sync_qaData_update('qa_task_locations', 'location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $projectId);
	

//qa_task_monitoring_add.txt Create Here
	subsequent_sync_qaData_add('qa_task_monitoring', 'project_id, location_id, sub_location_id, task, status, comments, last_modified_date, last_modified_by, created_date, created_by, resource_type, task_id, is_deleted, location_tree, signoff_image', $projectId, $new_project, 'task_id');
	
//qa_task_monitoring_update.txt Create Here
	subsequent_sync_qaData_update('qa_task_monitoring', 'task_id, project_id, location_id, sub_location_id, task, status, comments, created_by, created_date, resource_type, is_deleted, last_modified_by, last_modified_date, location_tree,signoff_image', $projectId);
	

//qa_task_monitoring_update_add.txt Create Here
	subsequent_sync_qaData_add('qa_task_monitoring_update', 'task_id, project_id, status, comments, last_modified_date, last_modified_by, created_date, created_by, resource_type, update_id, is_deleted', $projectId, $new_project, 'task_id');
	

//qa_task_signoff_add.txt Create Here
	subsequent_sync_qaData_add('qa_task_signoff', 'id, project_id, task_id, image_name, created_by, created_date, resource_type, is_deleted, last_modified_by, last_modified_date', $projectId, $new_project, 'id');
	
//qa_task_signoff_update.txt Create Here
	subsequent_sync_qaData_update('qa_task_signoff', 'id, project_id, task_id, image_name, created_by, created_date, resource_type, is_deleted, last_modified_by, last_modified_date', $projectId);
	

//qa_graphics_add.txt Create Here
	subsequent_sync_qaData_add('qa_graphics', 'non_conformance_id, task_id, project_id, qa_graphic_type, qa_graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, qa_graphic_id, is_deleted', $projectId, $new_project, 'qa_graphic_id');
	
//qa_graphics_update.txt Create Here
	subsequent_sync_qaData_update('qa_graphics', 'qa_graphic_id, non_conformance_id, task_id, qa_graphic_type, qa_graphic_name, created_by, created_date, last_modified_by, last_modified_date, resource_type, project_id, is_deleted', $projectId);

	
//qa_issued_to_inspections_add.txt Create Here
	subsequent_sync_qaData_add('qa_issued_to_inspections', 'non_conformance_id, task_id, project_id, qa_issued_to_name, qa_inspection_fixed_by_date, qa_cost_attribute, qa_inspection_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, qa_issued_to_id, is_deleted', $projectId, $new_project, 'qa_issued_to_id');
	
//qa_issued_to_inspections_update.txt Create Here
	subsequent_sync_qaData_update('qa_issued_to_inspections', 'qa_issued_to_id, non_conformance_id, task_id, qa_issued_to_name, qa_inspection_fixed_by_date, qa_cost_attribute, qa_inspection_status, created_by, created_date, last_modified_by, last_modified_date, resource_type, project_id, is_deleted', $projectId);
	
	
//qa_issued_to_inspections_add.txt Create Here
	subsequent_sync_qaData_add('qa_inspections', 'non_conformance_id, task_id, project_id, location_id, qa_inspection_date_raised, qa_inspection_raised_by, qa_inspection_inspected_by, qa_inspection_description, qa_inspection_location, last_modified_date, last_modified_by, created_date, created_by, resource_type, non_conformance_id, is_deleted', $projectId, $new_project, 'non_conformance_id');
	
//qa_issued_to_inspections_update.txt Create Here
	subsequent_sync_qaData_update('qa_inspections', 'non_conformance_id, task_id, location_id, qa_inspection_date_raised, qa_inspection_raised_by, qa_inspection_inspected_by, qa_inspection_description, qa_inspection_location, created_by, created_date, last_modified_by, last_modified_date, resource_type, project_id, is_deleted', $projectId);	
//QAData Files-------------------------------------- End Here -----------------------------------------*/


//PMData Files-------------------------------------- Start Here -----------------------------------------*/
//progress_monitoring_add.txt Create Here
	subsequent_sync_pmData_add('progress_monitoring', 'project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, progress_id, is_deleted, status, percentage, original_modified_date, location_tree_name', $projectId, $new_project, 'progress_id');
	
//progress_monitoring_update.txt Create Here
	subsequent_sync_pmData_update('progress_monitoring', 'progress_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, status, percentage, original_modified_date, location_tree_name', $projectId);	
	
	
//progress_monitoring_update_add.txt Create Here
//	subsequent_sync_pmData_add('progress_monitoring_update', 'project_id, progress_id, percentage, status, last_modified_date, last_modified_by, created_date, created_by, resource_type, update_id, is_deleted', $projectId, $new_project);
	
	
//progress_monitoring_add.txt Create Here
	subsequent_sync_pmData_add('issued_to_for_progress_monitoring', 'progress_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted, issued_to_progress_monitoring_id', $projectId, $new_project, 'issued_to_progress_monitoring_id');
	
//progress_monitoring_update.txt Create Here
	subsequent_sync_pmData_update('issued_to_for_progress_monitoring', 'issued_to_progress_monitoring_id,progress_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted', $projectId);	
//QAData Files-------------------------------------- End Here -----------------------------------------*/


//Inspections Files-------------------------------------- Start Here -----------------------------------------*/
//location_check_list_add.txt Create Here
	subsequent_sync_metaData_add('location_check_list', 'project_id, location_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, location_check_list_id, is_deleted, original_modified_date', $projectId, $new_project,'location_check_list_id');
	
//location_check_list_update.txt Create Here
	subsequent_sync_metaData_update('location_check_list', 'location_check_list_id, project_id, location_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, original_modified_date', $projectId);	
	
//location_check_list_add.txt Create Here
	subsequent_sync_metaData_add('check_list_items', 'project_id, check_list_items_name, check_list_items_tags, last_modified_date, last_modified_by, created_date, created_by, resource_type, check_list_items_id, is_deleted, check_list_items_option, issued_to, fix_by_days, checklist_type, holding_point', $projectId, $new_project, 'check_list_items_id');
	
//location_check_list_update.txt Create Here
	subsequent_sync_metaData_update('check_list_items', 'check_list_items_id, project_id, check_list_items_name, check_list_items_tags, check_list_items_option, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, issued_to, fix_by_days, checklist_type, holding_point', $projectId);	
	
//Inspections Files-------------------------------------- End Here -----------------------------------------*/

//Calender Files-------------------------------------- Start Here -----------------------------------------*/

//project_leave.txt Create Here
	subsequent_sync_metaData_add('project_leave', 'prleave_id, project_id, date, leave_type, reason, is_leave, created_date, created_by, original_modified_date, last_modified_date, last_modified_by, resource_type, is_deleted', $projectId, $new_project, 'prleave_id');
	
//project_leave.txt Create Here
	subsequent_sync_metaData_update('project_leave', 'prleave_id, project_id, date, leave_type, reason, is_leave, created_date, created_by, original_modified_date, last_modified_date, last_modified_by, resource_type, is_deleted', $projectId);	


//Calender Files-------------------------------------- Start Here -----------------------------------------*/

?>