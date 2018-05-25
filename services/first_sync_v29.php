<?php 
//user_projects.json Create Here
first_sync_txtFile('user_projects', 'project_id, user_id, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted, is_deleted, user_role, issued_to', $userId);

//Get Accepted inspection ids 
$inspectionIds = first_sync_acceptedInpsectionIDS($userRoleData, $deviceType);

//write inspections related files
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
		$selProjectInspections = 'DISTINCT inspection_id, project_id, location_id, inspection_date_raised, inspection_status, inspection_inspected_by, inspection_fixed_by_date, inspection_type, inspection_map_address, inspection_description, inspection_priority, inspection_notes, inspection_sign_image, inspection_location, cost_attribute, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, inspection_raised_by, original_modified_date, check_list_item_id, check_list_location_id';
if($whereCon != ''){
#echo "SELECT ".$selProjectInspections." FROM project_inspections WHERE project_id = '".$project['project_id']."' AND ".$whereCon." is_deleted=0 group by inspection_id";die;

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
			$iPadQueryrsProjectInspections[] = $rowProjectInspections['global_id'] . "##VALUES(\"".$db->dataFilter($rowProjectInspections['global_id'])."\",\"".$db->dataFilter($rowProjectInspections['location_id'])."\", \"".$db->dataFilter($date_raised)."\", \"".$db->dataFilter($rowProjectInspections['inspection_status'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_inspected_by'])."\", \"".$db->dataFilter($inspection_fixed_by_date)."\", \"".$db->dataFilter($rowProjectInspections['inspection_type'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_map_address'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_description'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_priority'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_notes'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_sign_image'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_location'])."\", \"".$db->dataFilter($rowProjectInspections['cost_attribute'])."\", \"".$db->dataFilter($rowProjectInspections['last_modified_date'])."\", \"".$db->dataFilter($rowProjectInspections['last_modified_by'])."\", \"".$db->dataFilter($rowProjectInspections['created_date'])."\", \"".$db->dataFilter($rowProjectInspections['created_by'])."\", \"".$db->dataFilter($rowProjectInspections['resource_type'])."\", \"".$db->dataFilter($rowProjectInspections['global_id'])."\", \"".$db->dataFilter($rowProjectInspections['is_deleted'])."\", \"".$db->dataFilter($rowProjectInspections['project_id'])."\", \"".$db->dataFilter($rowProjectInspections['inspection_raised_by'])."\", \"".$db->dataFilter($rowProjectInspections['original_modified_date'])."\", \"".$db->dataFilter($rowProjectInspections['check_list_item_id'])."\", \"".$db->dataFilter($rowProjectInspections['check_list_location_id'])."\");\r\n";
		}
		$db->createFile('project_inspection_add.txt', join('', $iPadQueryrsProjectInspections), EXPORTFILEPATH.'/download/');//Write File Here 
	}
}

	$selIssuetoInspections = 'issued_to_inspections_id, project_id, inspection_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, inspection_status, cost_attribute, inspection_fixed_by_date, closed_date, cost_impact_type, original_modified_date, time_impact';
if($whereCon != ''){	
	$rsIssuetoInspections = mysql_query("SELECT ".$selIssuetoInspections." FROM issued_to_for_inspections WHERE project_id = '".$project['project_id']."' AND ".$whereCon." is_deleted=0");
	if(mysql_num_rows($rsIssuetoInspections) > 0){
		$iPadQueryIssuetoInspections = array();
		while($rowIssuetoInspections = mysql_fetch_assoc($rsIssuetoInspections)){
			$rowIssuetoInspections['global_id'] = $rowIssuetoInspections['issued_to_inspections_id'];

			$iPadQueryIssuetoInspections[] = $rowIssuetoInspections['global_id'] . "##VALUES(\"".$db->dataFilter($rowIssuetoInspections['project_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['inspection_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['issued_to_name'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['last_modified_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['created_by'])."\", \"".$db->dataFilter($rowIssuetoInspections['resource_type'])."\", \"".$db->dataFilter($rowIssuetoInspections['global_id'])."\", \"".$db->dataFilter($rowIssuetoInspections['is_deleted'])."\", \"".$db->dataFilter($rowIssuetoInspections['inspection_fixed_by_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['cost_attribute'])."\", \"".$db->dataFilter($rowIssuetoInspections['inspection_status'])."\", \"".$db->dataFilter($rowIssuetoInspections['closed_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['cost_impact_type'])."\", \"".$db->dataFilter($rowIssuetoInspections['original_modified_date'])."\", \"".$db->dataFilter($rowIssuetoInspections['time_impact'])."\");\r\n";
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
	
	$selInspectionGraphic = 'graphic_id, project_id, inspection_id, graphic_type, graphic_title, graphic_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, original_modified_date';
	if($whereCon != ''){
		$rsInspectionGraphic = mysql_query("SELECT ".$selInspectionGraphic." FROM inspection_graphics WHERE project_id = '".$project['project_id']."' AND ".$whereCon." is_deleted=0");
		if(mysql_num_rows($rsInspectionGraphic) > 0){
			$iPadQueryInspectionGraphic = array();
			while($rowInspectionGraphic = mysql_fetch_assoc($rsInspectionGraphic)){
				$rowInspectionGraphic['global_id'] = $rowInspectionGraphic['graphic_id'];

				$iPadQueryInspectionGraphic[] = $rowInspectionGraphic['global_id'] . "##VALUES(\"".$db->dataFilter($rowInspectionGraphic['inspection_id'])."\", \"".$db->dataFilter($rowInspectionGraphic['graphic_type'])."\", \"".$db->dataFilter($rowInspectionGraphic['graphic_title'])."\", \"".$db->dataFilter($rowInspectionGraphic['graphic_name'])."\", \"".$db->dataFilter($rowInspectionGraphic['last_modified_date'])."\", \"".$db->dataFilter($rowInspectionGraphic['last_modified_by'])."\", \"".$db->dataFilter($rowInspectionGraphic['created_date'])."\", \"".$db->dataFilter($rowInspectionGraphic['created_by'])."\", \"".$db->dataFilter($rowInspectionGraphic['resource_type'])."\", \"".$db->dataFilter($rowInspectionGraphic['global_id'])."\", \"".$db->dataFilter($rowInspectionGraphic['project_id'])."\", \"".$db->dataFilter($rowInspectionGraphic['is_deleted'])."\", \"".$db->dataFilter($rowInspectionGraphic['original_modified_date'])."\");\r\n";

			}
			$db->createFile('inspection_graphics_add.txt', join('', $iPadQueryInspectionGraphic), EXPORTFILEPATH.'/download/');//Write File Here 
		}
	}
}

//To Create Multidimension Array as Unique Array
$projectId = array_map('unserialize', array_unique(array_map('serialize', $projectId)));

//MetaData Files-------------------------------------- Start Here -----------------------------------------*/
//standard_defects_add.json Create Here
first_sync_metaData('standard_defects', 'standard_defect_id, project_id, description, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, tag, issued_to, fix_by_days', $projectId);

//project_locations_add.json Create Here
first_sync_metaData('project_locations', 'location_id, location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $projectId);

//project_monitoring_locations_add.json Create Here
first_sync_metaData('project_monitoring_locations', 'location_id, location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $projectId);

//user_permission_add.json Create Here
first_sync_metaData('user_permission', 'id, user_id, permission_name, is_allow, created_by, created_date, last_modified_date, last_modified_by, resource_type, is_deleted, project_id', $projectId);

//check_list_items_add.json Create Here
first_sync_metaData('check_list_items', 'check_list_items_id, project_id, check_list_items_name, check_list_items_tags, check_list_items_option, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, issued_to, fix_by_days, checklist_type, holding_point', $projectId);

//inspection_issue_to_add.json Create Here
first_sync_metaData('inspection_issue_to', 'issue_to_id, project_id, issue_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, tag, issue_to_email, company_name', $projectId);

//draw_mgmt_images_add.json Create Here
first_sync_metaData('draw_mgmt_images', 'draw_mgmt_images_id, project_id, draw_mgmt_images_title, draw_mgmt_images_name, draw_mgmt_images_thumbnail, draw_mgmt_images_description, draw_mgmt_images_tags, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $projectId);

//drawing_register_add.json Create Here
first_sync_metaData('drawing_register', 'id, project_id, title, pdf_name, number, revision, comments, tag, attribute1, attribute2, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $projectId);
//MetaData Files-------------------------------------- End Here -----------------------------------------*/


//PMData Files-------------------------------------- Start Here -----------------------------------------*/
//progress_monitoring_add.json Create Here
first_sync_pmData('progress_monitoring', 'progress_id, project_id, location_id, sub_location_id, task, start_date, end_date, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, status, percentage, original_modified_date, location_tree_name', $projectId);

//issued_to_for_progress_monitoring_add.json Create Here
first_sync_pmData('issued_to_for_progress_monitoring', 'issued_to_progress_monitoring_id,progress_id, issued_to_name, last_modified_date, last_modified_by, created_date, created_by, resource_type, project_id, is_deleted', $projectId);

//progress_monitoring_check_list_add.json Create Here
first_sync_pmData('progress_monitoring_check_list', 'progress_monitoring_check_list_id, project_id, progress_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $projectId);

//progress_monitoring_check_list_add.json Create Here
first_sync_pmData('qa_task_locations', 'location_id, location_id, project_id, location_parent_id, location_title, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted', $projectId);
//PMData Files-------------------------------------- End Here -----------------------------------------*/


//QAData Files-------------------------------------- Start Here -----------------------------------------*/
//qa_task_monitoring_add.json Create Here
first_sync_qaData('qa_task_monitoring', 'task_id, project_id, location_id, sub_location_id, task, status, comments, created_by, created_date, resource_type, is_deleted, last_modified_by, last_modified_date, location_tree,signoff_image', $projectId);

//qa_task_monitoring_update_add.json Create Here
first_sync_qaData('qa_task_monitoring_update', 'update_id, task_id, status, comments, created_by, created_date, last_modified_by, last_modified_date, project_id, is_deleted, resource_type', $projectId);

//qa_issued_to_inspections_add.json Create Here
first_sync_qaData('qa_graphics', 'qa_graphic_id, non_conformance_id, task_id, qa_graphic_type, qa_graphic_name, created_by, created_date, last_modified_by, last_modified_date, resource_type, project_id, is_deleted, original_modified_date', $projectId);

//qa_issued_to_inspections_add.json Create Here
first_sync_qaData('qa_issued_to_inspections', 'qa_issued_to_id, non_conformance_id, task_id, qa_issued_to_name, qa_inspection_fixed_by_date, qa_cost_attribute, qa_inspection_status, created_by, created_date, last_modified_by, last_modified_date, resource_type, project_id, is_deleted, original_modified_date', $projectId);

//qa_inspections_add.json Create Here
first_sync_qaData('qa_inspections', 'non_conformance_id, non_conformance_id, task_id, location_id, qa_inspection_date_raised, qa_inspection_raised_by, qa_inspection_inspected_by, qa_inspection_description, qa_inspection_location, created_by, created_date, last_modified_by, last_modified_date, resource_type, project_id, is_deleted, original_modified_date', $projectId);
//QAData Files-------------------------------------- End Here -----------------------------------------*/


//Inspections Files-------------------------------------- Start Here -----------------------------------------*/
//location_check_list_add.json Create Here
first_sync_metaData('location_check_list', 'location_check_list_id, project_id, location_id, check_list_items_id, check_list_items_status, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, original_modified_date', $projectId);


//Inspections Files-------------------------------------- Start Here -----------------------------------------*/


//Calender Files-------------------------------------- Start Here -----------------------------------------*/
//project_leave.json Create Here
first_sync_metaData('project_leave', 'prleave_id, project_id, date, leave_type, reason, is_leave, created_date, created_by, original_modified_date, last_modified_date, last_modified_by, resource_type, is_deleted', $projectId);
//Calender Files-------------------------------------- Start Here -----------------------------------------*/

?>