<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

include 'includes/commanfunction.php';
$object = new COMMAN_Class();
//Code for Set Permissions start here
if($_REQUEST['task'] == 'permission'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	
	foreach($ids as $id){
		for($i=0;$i<sizeof($managerPermissionArray);$i++){
			echo $permissionQry = "INSERT INTO user_permission SET
										user_id = '".$id['user_id']."',
										permission_name = '".$keyManagerPermissionArray[$i]."',
										is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."',
										created_by = '0',
										created_date = now()";
			mysql_query($permissionQry);
		}
	}
	
	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	
	foreach($ids as $id){
		for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
			echo $permissionQry = "INSERT INTO user_permission SET
										user_id = '".$id['user_id']."',
										permission_name = '".$keyInspectorPermissionArray[$i]."',
										is_allow = '".$inspectorPermissionArray[$keyInspectorPermissionArray[$i]]."',
										created_by = '0',
										created_date = now()";
			mysql_query($permissionQry);
		}
	}
}

//Code for Set Permissions end here

//Code for Insert row in projects table start here

if($_REQUEST['task'] == 'projects'){
	$projects = $object->selQRYMultiple('project_id, pro_code, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, created_date, created_by, resource_type, is_deleted', 'user_projects', 'is_deleted>=0');
	
#	print_r($projects);die;
	
	foreach($projects as $proj){
		echo $projectQry = "INSERT INTO projects SET
								pro_code = '".$proj['pro_code']."',
								project_id = '".$proj['project_id']."',
								project_name = '".$proj['project_name']."',
								project_type = '".$proj['project_type']."',
								project_address_line1 = '".$proj['project_address_line1']."',
								project_address_line2 = '".$proj['project_address_line2']."',
								project_suburb = '".$proj['project_suburb']."',
								project_state = '".$proj['project_state']."',
								project_postcode = '".$proj['project_postcode']."',
								project_country = '".$proj['project_country']."',
								is_deleted = '".$proj['is_deleted']."',
								created_date = now(),
								created_by = 0";
				mysql_query($projectQry);
	}
}

if($_REQUEST['task'] == 'default_issue_to'){
	$projects = $object->selQRYMultiple('project_id', 'projects', 'is_deleted>=0');
	
#	print_r($projects);die;
	
	foreach($projects as $proj){
		echo $projectQry = "INSERT INTO inspection_issue_to SET
								project_id = '".$proj['project_id']."',
								issue_to_name = 'NA',
								last_modified_date = now(),
								last_modified_by = 0,
								created_date = now(),
								is_deleted=0,
								created_by = 0";
				mysql_query($projectQry);
	}
}

if($_REQUEST['task'] == 'set_project_permision'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	
	foreach($ids as $id){
		$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
		foreach($project_ids as $pId){
			for($i=0;$i<sizeof($managerPermissionArray);$i++){
				echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyManagerPermissionArray[$i]."',
											is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."',
											created_by = '0',
											project_id = '".$pId['project_id']."',
											created_date = now()";
				mysql_query($permissionQry);
			}
		}
		$project_ids = array();
	}
	
	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	
	foreach($ids as $id){
		$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
		foreach($project_ids as $pId){
			for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
				echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyInspectorPermissionArray[$i]."',
											is_allow = '".$inspectorPermissionArray[$keyInspectorPermissionArray[$i]]."',
											created_by = '0',
											project_id = '".$pId['project_id']."',
											created_date = now()";
				mysql_query($permissionQry);
			}
		}
	}
}

//Code for Set Permissions start here
if($_REQUEST['task'] == 'permission_project'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);	
	$projectWisePermissions = array(
		'web_edit_inspection',
		'web_delete_inspection',
		'web_close_inspection',
		'iPad_add_inspection',
		'iPad_edit_inspection',
		'iPad_delete_inspection',
		'iPad_close_inspection',
		'iPhone_add_inspection',
		'iPhone_close_inspection',
		'web_checklist'
	);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	
	foreach($ids as $id){
		for($i=0;$i<sizeof($managerPermissionArray);$i++){
			if(in_array($keyManagerPermissionArray[$i], $projectWisePermissions)){
				$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
				if($project_ids[0]['project_id'] != ''){
					foreach($project_ids as $pId){
						echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyManagerPermissionArray[$i]."',
											is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."',
											created_by = '0',
											project_id = '".$pId['project_id']."',
											created_date = now()";
	echo '<br />';
							mysql_query($permissionQry);
					}
				}
			}else{
				echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyManagerPermissionArray[$i]."',
											is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."',
											created_by = '0',
											created_date = now()";
echo '<br />';
#				mysql_query($permissionQry);
			}
		}
	}

	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	
	foreach($ids as $id){
		for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
			if(in_array($keyInspectorPermissionArray[$i], $projectWisePermissions)){
				$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
				if($project_ids[0]['project_id'] != ''){
					foreach($project_ids as $pId){
						echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyInspectorPermissionArray[$i]."',
											is_allow = '".$inspectorPermissionArray[$keyInspectorPermissionArray[$i]]."',
											created_by = '0',
											project_id = '".$pId['project_id']."',
											created_date = now()";
	echo '<br />';
						mysql_query($permissionQry);
					}
				}
			}else{
				echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyInspectorPermissionArray[$i]."',
											is_allow = '".$inspectorPermissionArray[$keyInspectorPermissionArray[$i]]."',
											created_by = '0',
											created_date = now()";
echo '<br />';
#				mysql_query($permissionQry);
			}
		}
	}
}

if($_REQUEST['task'] == 'set_userid_export'){
	$exp = $object->selQRYMultiple('export_files_id, path', 'exportData', 'created_date >= "1970-01-01 00:00:00"');
	foreach($exp as $exportData){
		$pathData = explode('/', $exportData['path']);
		echo $exportData['path'].'<br />';
echo 		$updateQry = 'UPDATE exportData SET userid = "'.$pathData[3].'" WHERE export_files_id = "'.$exportData['export_files_id'].'"';
	mysql_query($updateQry);
	}
}


if($_REQUEST['task'] == 'set_single_permission'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	foreach($ids as $id){
		echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$_REQUEST['permission_name']."',
											is_allow = '".$_REQUEST['permission_value']."',
											created_by = '0',
											created_date = now()";
echo '<br />';
				mysql_query($permissionQry);
	}

	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	
	foreach($ids as $id){
		echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$_REQUEST['permission_name']."',
											is_allow = '".$_REQUEST['permission_value']."',
											created_by = '0',
											created_date = now()";
echo '<br />';
				mysql_query($permissionQry);
	}
}

?>