<?php
session_start();
include('includes/commanfunction.php'); 
$obj = new COMMAN_Class(); 
$projID = $_SESSION['idp'];
$locId = $_POST['locId'];
$locVal = $_POST['locVal'];
$excludedLocVal = $_POST['excludedLocVal'];

if(isset($_POST['name'])){
	for($i=0; $i<sizeof($locId); $i++){
		$query = "UPDATE qa_task_monitoring SET subloc_order_wall_chart_report = ".($i+1).", excluded_location = '".$excludedLocVal[$i]."', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE project_id = ".$projID." AND is_deleted = 0 AND sub_location_id in (".$locId[$i].")";
		mysql_query($query);
	}
	echo 1;
}

?>

