<?php
error_reporting(1);
session_start();
require_once'includes/functions.php';
$object= new DB_Class();
require_once'includes/commanfunction.php';
$obj = new COMMAN_Class();

function echocsv($fields, $pr_num_sublocations){
	$fields["task"] = normalize_t ($fields["task"]);
	$fields["location"] = normalize_t ($fields["location"]);
	for($i=1; $i<=$pr_num_sublocations; $i++){
		$fields["sublocation".$i] = normalize_t($fields["sublocation".$i]);
	}
	$fields["startDate"] = normalize_t ($fields["startDate"]);
	$fields["endDate"] = normalize_t ($fields["endDate"]);
	$fields["issueTo"] = normalize_t ($fields["issueTo"]);
	
	$op = $fields["location"];
	for($i=1; $i<=$pr_num_sublocations; $i++){
		$op .= ",".$fields["sublocation".$i];
	}
	$op .= "," . $fields["task"]. "," . $fields["startDate"]. "," . $fields["endDate"]. "," . $fields["issueTo"];
	$op .= "\r\n";
	return $op;
}

function normalize_t($field){
	if(preg_match('/\\r|\\n|,|"/', $field)){
		$field = '"' . str_replace( '"', '""', $field ) . '"';
	}
	return $field;
}

	$fileName = 'Progress_monitoring_template_export.csv';
	header("Content-type: application/csv");
	header("Content-Disposition: attachment; filename=".$fileName);
	header("Pragma: no-cache");
	header("Expires: 0");

//SubLocDepth count
	$issueData = $obj->selQRYMultiple('pr_num_sublocations', 'user_projects', 'project_id = "'.$_SESSION["idp"].'" AND is_deleted = 0 AND user_id = "'.$_SESSION['ww_builder_id'].'"');
	$subLocDepth = $issueData[0]['pr_num_sublocations'];
//Issue to Data progressid wise
//SELECT progress_id, GROUP_CONCAT(issued_to_name SEPARATOR ";") as issueName FROM `issued_to_for_progress_monitoring` WHERE `project_id` = 266 and `is_deleted` = 0 group by `progress_id`
	$issueData = $obj->selQRYMultiple('progress_id, GROUP_CONCAT(issued_to_name SEPARATOR ";") as issueName', 'issued_to_for_progress_monitoring', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0 GROUP BY progress_id ORDER BY progress_id');
	$issueToData = array();
	if(!empty($issueData)){
		foreach($issueData as $isData){
			$issueToData[$isData['progress_id']] = $isData['issueName'];
		}
	}
	
	$standard_query = "SELECT location_id, sub_location_id, task, start_date, end_date, progress_id, location_tree, location_tree_name FROM progress_monitoring WHERE project_id = ".$_SESSION['idp']." AND is_deleted = 0 ORDER BY location_id, sub_location_id";
	
	$export = mysql_query($standard_query);
	$header = array('Location');
	for($i=1; $i<=$subLocDepth; $i++){
		array_push($header, 'Sublocation '.$i);
	}
	array_push($header, 'Task', 'Start Date', 'End Date', 'Trade');
	$output = join(",", $header)."\r\n";//echocsv($header);
	$loc_id = ''; $sub_loc_id = ''; $exit_id = ''; $exit_sub_loc = ''; $issue_name_data = ''; $issUname_db = '';
	$location_array = array();
	$actual_loc = array('', '', '', '');
	$old_location_tree = array();
	$a_actual_loc = array('', '', '', '');
	while($row = mysql_fetch_assoc($export)){
		$exit_id=$row['location_id'];
		$exit_sub_loc=$row['sub_location_id'];
		$location_tree = $row["location_tree"];
		$task = $row["task"];
		
		if($sub_loc_id != $exit_sub_loc){
			$tmp = explode(" > ", $location_tree);
			$locationids = "";
			if ($sub_loc_id != ""){
				array_pop($old_location_tree);
				for ($i=sizeof($old_location_tree)-1; $i>0; $i--){
					if ($old_location_tree[$i] != $tmp[$i-1])
						break;
					else
						unset($tmp[$i-1]);
				}
			}
			for ($i=0;$i<sizeof($tmp); $i++){
				if (empty($location_array[$tmp[$i]])){
					if ($locationids == ""){
						$locationids = $tmp[$i];
					}else{
						$locationids .= ", " . $tmp[$i]; 
					}
				}
			}
			if ($locationids != ""){
				$select_location = "SELECT location_title, location_id FROM project_monitoring_locations WHERE location_id IN (".$locationids.") AND is_deleted=0";
				$loc=mysql_query($select_location);
				while($row1 = mysql_fetch_assoc($loc)){
					$location_array[$row1['location_id']] = $row1['location_title'];
				}
			}
			$sub_loc_id = $exit_sub_loc;
		}
		$tmp = explode(" > ", $location_tree);
		$old_location_tree = $tmp;
		for ($i=0;$i<sizeof($tmp); $i++){
			if ($actual_loc[$i] == $location_array[$tmp[$i]]){
				$a_actual_loc[$i] = "";
			}else{
				$a_actual_loc[$i] = $location_array[$tmp[$i]];
			}
			$actual_loc[$i] = $location_array[$tmp[$i]];
		}
		$fields = array();
		$fields["task"] = $row["task"];
		$fields["location"] = $a_actual_loc[0];
		for($i=1; $i<=$subLocDepth; $i++){
			$fields["sublocation".$i] = $a_actual_loc[$i];
		}
		$fields["startDate"] = date('d/m/Y', strtotime($row["start_date"]));
		$fields["endDate"] = date('d/m/Y', strtotime($row["end_date"]));
		$fields["issueTo"] = $issueToData[$row['progress_id']];
		$output .= echocsv($fields, $subLocDepth);
	}
	print (trim($output));
?> 