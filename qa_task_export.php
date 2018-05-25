<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

session_start();
require_once'includes/functions.php';
$object= new DB_Class();
require_once'includes/commanfunction.php';
$obj = new COMMAN_Class();

function echocsv($fields){
	$fields["task"] = normalize_t ($fields["task"]);
	$fields["location"] = normalize_t ($fields["location"]);
	$fields["sublocation1"] = normalize_t ($fields["sublocation1"]);
	$fields["sublocation2"] = normalize_t ($fields["sublocation2"]);
	$fields["sublocation3"] = normalize_t ($fields["sublocation3"]);
	$op = $fields["location"] . "," . $fields["sublocation1"] . "," . $fields["sublocation2"] . "," . $fields["sublocation3"]. "," . $fields["task"];
	$op .= "\r\n";
	return $op;
}

function normalize_t($field){
	if(preg_match('/\\r|\\n|,|"/', $field)){
		$field = '"' . str_replace( '"', '""', $field ) . '"';
	}
	return $field;
}
$fileName = 'qa_task_export.csv';

header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=".$fileName);
header("Pragma: no-cache");
header("Expires: 0");
	
	$standard_query = "SELECT location_id, sub_location_id, task,task_id, location_tree FROM qa_task_monitoring WHERE project_id = ".$_SESSION['idp']." AND is_deleted = 0 ORDER BY location_id, sub_location_id";
	
	$export = mysql_query($standard_query);
	$header = array('Location', 'Sublocation 1','Sublocation 2','Sublocation 3', 'Task');
	
	$output = join(",", $header) . "\r\n";//echocsv($header);
	$loc_id = ''; $sub_loc_id = ''; $exit_id = ''; $exit_sub_loc = ''; $issue_name_data = ''; $issUname_db = '';
	$location_array = array();
	$actual_loc = array('','','','');
	$old_location_tree = array();
	$a_actual_loc = array('','','','');
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
				$select_location="select location_title,location_id from qa_task_locations where location_id IN (".$locationids.") and is_deleted=0";
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
		$fields["sublocation1"] = $a_actual_loc[1];
		$fields["sublocation2"] = $a_actual_loc[2];
		$fields["sublocation3"] = $a_actual_loc[3];
		$output .= echocsv($fields);
	}
	print ($output);
?> 