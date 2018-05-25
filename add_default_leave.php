<?php session_start();
set_time_limit(200000);
require_once'includes/functions.php';
include_once("includes/commanfunction.php");
$obj = new DB_Class();
$object = new COMMAN_Class(); 

#Add default leave in project
	$select = "date, leave_type, reason, is_leave";
	$table = "public_holidays";
	$where = "is_deleted ='0'";
	$getDefaultLeave = $object->selQRYMultiple($select, $table, $where);

	$select = "project_id, project_name";
	$table = "projects";
	$where = "is_deleted ='0'";
	$getProjectList = $object->selQRYMultiple($select, $table, $where);
//print_r($getProjectList); die;	
	foreach($getProjectList as $project){
		// Check project_id in project_leave table
		$select = "project_id";
		$table = "project_leave";
		$where = "project_id ='".$project['project_id']."'";
		$checkProject = $object->selQRY($select, $table, $where);

		if(empty($checkProject['project_id'])){

			$getProjectList = $object->selQRYMultiple($select, $table, $where);
			// Add project default leave in project_leave table
			foreach($getDefaultLeave as $val){
				$insertQry = "INSERT INTO project_leave SET
										project_id = '".$project['project_id']."',
										date = '".$val['date']."',
										leave_type = '".$val['leave_type']."',
										reason = '".$val['reason']."',
										is_leave = '".$val['is_leave']."',
										created_date = NOW(),
										created_by = '".$_SESSION['ww_builder_id']."',
										last_modified_date = NOW(),
										last_modified_by = '".$_SESSION['ww_builder_id']."'";
				$r=$obj->db_query($insertQry);
			}
		}
		echo "Leave added in '".$project['project_name']."' project<br>";

	}
?>