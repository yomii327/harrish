<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(300);
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_REQUEST['uniqueId'])){
	$copyLocation = $_REQUEST['copyLocation'];
	$pasteLocation = $_REQUEST['pasteLocation'];

	function pasteLocations($cat, $pId){
		$i=1;
		$remain = array();
		$all = array();
		$insertArray = array();
		$parentArray = array();
		
		$remain[0] = $cat;
		$parentArray[0] = $pId;
		
		while(sizeof($remain)>0){
			$curr = $remain[0];
			
			$res = mysql_query("select location_id from project_locations where location_parent_id = $curr");
			while($row = mysql_fetch_array($res)){
				$all[$i++]=$row['location_id'];
				$remain[sizeof($remain)]=$row['location_id'];
			}
			$newValues = array_diff($all, $insertArray);
	
			if(!empty($newValues)){
				foreach($newValues as $insertValues){
					$title_select = "SELECT location_title FROM project_locations WHERE location_id = '".$insertValues."'";
					$title_res = mysql_query($title_select);
					if(mysql_num_rows($title_res) > 0){
						$title_obj = mysql_fetch_object($title_res);
						$title = $title_obj->location_title;
						
						$insert_query = "INSERT INTO project_locations SET project_id = '".$_SESSION['idp']."', location_title = '".$title."', location_parent_id = '".$parentArray[0]."', created_date = now(), created_by = '".$_SESSION['ww_builder_id']."', last_modified_date = NOW(), last_modified_by = '".$_SESSION['ww_builder_id']."'";
						mysql_query($insert_query);
						
						$parentArray[sizeof($parentArray)] = mysql_insert_id();
						
						$insertArray[] = $insertValues;
					}
				}
			}else{die('Execusion Done');}
			unset($remain[0]);
			array_shift($parentArray);
			$remain=array_values($remain);
		}
		return $all;
	}
	
	$res_test = mysql_query("select location_id from project_locations where location_parent_id = '".$copyLocation."'");
	if(mysql_num_rows($res_test) > 0){
		$result = pasteLocations($copyLocation, $pasteLocation);
		if($result == 'Execusion Done'){
			echo 1;
		}else{
			echo 0;
		}
	}else{
		echo 'Error';
	}
}
?>