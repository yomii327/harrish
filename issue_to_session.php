<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();
$builder_id=$_SESSION['ww_builder_id'];
$project_id=$_REQUEST['project_id'];
$_SESSION['project_id_issue']=$project_id;

	$issue="SELECT count(*) as count FROM `issued_to_for_inspections` as i, `project_inspections` as pi, user_projects as up where up.project_id=".$project_id." and i.project_id=".$project_id." and i.is_deleted=0 and up.is_deleted=0 and pi.is_deleted=0 and pi.inspection_id=i.inspection_id and user_id=$builder_id group by i.inspection_id";

 	$i_rs=$obj->db_query($issue);
	if($issu=$obj->db_fetch_assoc($i_rs)){
		$it_total = $issu["count"];
	}
echo $it_total = mysql_num_rows($i_rs);

?>
