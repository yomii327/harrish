<?php 

session_start();
require_once'includes/functions.php';

/* Construction calendar */
# Add / Edit Project Leave
$projectLeave = isset($_POST['projectLeave']) ? $_POST['projectLeave'] : '';

if(isset($projectLeave) && !empty($projectLeave)){
	$prLeaveId = (isset($_POST['prLeaveId']) && !empty($_POST['prLeaveId']))?$_POST['prLeaveId']:0;
	projectLeave($prLeaveId);
}

# Delete Project Leave
$projectLeave = isset($_POST['deleteProjectLeave']) ? $_POST['deleteProjectLeave'] : '';

if(isset($projectLeave) && !empty($projectLeave)){
	$prLeaveId = (isset($_POST['prLeaveId']) && !empty($_POST['prLeaveId']))?$_POST['prLeaveId']:0;
	deleteProjectLeave($prLeaveId);
}

# Add / Edit Project Leave by ajax
function projectLeave($prLeaveId=0){
	$obj = new DB_Class();
	$date = $_POST["ccDate"];
	$leave_type = $_POST["ccLeave"];
	$reason = $_POST["ccReason"];
	$is_leave = $_POST["ccIsLeave"];
	$project_id = $_SESSION['idp'];

	if($prLeaveId==0){
		$created_date = date('Y-m-d H:i:s');
		$created_by = $_SESSION['ww_builder_id'];	
	//	$lastId = $this->adminmodel->insertDetails('project_leave', $projData);
		$insertQry = "INSERT INTO project_leave SET
								date = '".$date."',
								leave_type = '".$leave_type."',
								reason = '".$reason."',
								is_leave = '".$is_leave."',
								project_id = '".$project_id."',
								created_date = NOW(),
								created_by = '".$created_by."',
								last_modified_date = NOW(),
								last_modified_by = '".$created_by."'";
		$r=$obj->db_query($insertQry);
	}else{
		$original_modified_date = date('Y-m-d H:i:s');
		$last_modified_by = $_SESSION['ww_builder_id'];	
		$updateQry = "UPDATE project_leave SET
							date = '".$date."',
							leave_type = '".$leave_type."',
							reason = '".$reason."',
							is_leave = '".$is_leave."',
							project_id = '".$project_id."',
							last_modified_date = NOW(),
							original_modified_date = NOW(),
							last_modified_by = '".$last_modified_by."' WHERE prleave_id = ".$prLeaveId;
		$r=$obj->db_query($updateQry);
	}
		
	$select = "auto_increment";
	$table ="information_schema.TABLES";
	$where = "TABLE_NAME ='project_leave'";
	$result = selQRY($select, $table, $where);
	echo $plNewId = $result['auto_increment'];
}	

# Delete Project Leave by ajax
function deleteProjectLeave($prLeaveId=0){
	$obj = new DB_Class();
	$date = $_POST["ccDate"];
	$project_id = $_SESSION['idp'];

	if($prLeaveId!=0){
		$original_modified_date = date('Y-m-d H:i:s');
		$last_modified_by = $_SESSION['ww_builder_id'];	
		$updateQry = "UPDATE project_leave SET
							is_deleted = 1,
							original_modified_date = NOW(),
							last_modified_by = '".$last_modified_by."',
							last_modified_date = NOW(),
							last_modified_by = ".$_SESSION['ww_builder_id']."
					WHERE
						prleave_id=".$prLeaveId." and date='".$date."' and project_id='".$project_id."'";
		$r=$obj->db_query($updateQry);
	}
		
	$select = "auto_increment";
	$table ="information_schema.TABLES";
	$where = "TABLE_NAME ='project_leave'";
	$result = selQRY($select, $table, $where);
	echo $plNewId = $result['auto_increment'];
}	

# Get result
function selQRY($select, $table, $where){
	//echo "SELECT ".$select." FROM ".$table." WHERE ".$where;
	$RS = mysql_query("SELECT ".$select." FROM ".$table." WHERE ".$where);
	if(mysql_num_rows($RS) > 0){
		while($ROW = mysql_fetch_assoc($RS)){
			return $ROW;
		}
	}else{
		return false;
	}
}

?>						
