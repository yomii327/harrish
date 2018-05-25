<?php
session_start();
set_time_limit(3000);
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_REQUEST['uniqueId'])){
	$copyLocation = $_REQUEST['copyLocation'];
	$pasteLocation = $_REQUEST['pasteLocation'];

	function get_cats($cat){
		$i=1;
		$remain = array();
		$all = array();
		$insertArray = array();
		$parentArray = array();
		
		$remain[0] = $cat;
		$all[0] = $cat;	
		while(sizeof($remain)>0){
			$curr = $remain[0];
			
			$res = mysql_query("SELECT location_id FROM project_locations WHERE location_parent_id = ".$curr." and is_deleted = 0");
			while($row = mysql_fetch_array($res)){
				$all[$i++]=$row['location_id'];
				$remain[sizeof($remain)]=$row['location_id'];
			}
			unset($remain[0]);
			$remain=array_values($remain);
		}
		return $all;
	}
 
	function pasteLocations($cat, $pId){
		global $obj;
		$i=1;
		$remain = array();
		$all = array();
		$insertArray = array();
		$parentArray = array();
		
		$remain[0] = $cat;
		$title_select = "SELECT location_title FROM project_locations WHERE location_id = '".$cat."' and is_deleted = 0";
		$title_res = mysql_query($title_select);
		if(mysql_num_rows($title_res) > 0){
			$title_obj = mysql_fetch_object($title_res);
			$title = $title_obj->location_title;
			$insert_query = "INSERT INTO project_locations SET project_id = '".$_SESSION['idp']."', location_title = '".$title." (Copy ".$_SESSION['pasteCount'].")', location_parent_id = '".$pId."', created_date = now(), created_by = '".$_SESSION['ww_is_builder']."', last_modified_date = now(), last_modified_by = '".$_SESSION['ww_is_builder']."'";
			mysql_query($insert_query);
			$insertedVauleRoot = mysql_insert_id();
			$parentArray[0] = $insertedVauleRoot;
			//Update Location Tree and Location Name Tree
			$locIdTree = $obj->subLocationsIDS($insertedVauleRoot, ' > ');
			$locNameTree = $obj->subLocations($insertedVauleRoot, ' > ');
			$updateQRY = "UPDATE project_locations SET location_id_tree = '".$locIdTree."', location_name_tree = '".$locNameTree."' WHERE location_id = ".$insertedVauleRoot;
			mysql_query($updateQRY);
		}
		
		
		while(sizeof($remain)>0){
			$curr = $remain[0];
			$qSelect = "select location_id from project_locations where location_parent_id = ".$curr." and is_deleted = 0";
			$res = mysql_query($qSelect);
			while($row = mysql_fetch_array($res)){
				$all[$i++]=$row['location_id'];
				$remain[sizeof($remain)]=$row['location_id'];
			}
			//GS
			$newValues = array_diff($all, $insertArray);
			if(!empty($newValues)){
				foreach($newValues as $insertValues){
					$title_select = "SELECT location_title FROM project_locations WHERE location_id = '".$insertValues."' and is_deleted = 0";
					$title_res = mysql_query($title_select);
					if(mysql_num_rows($title_res) > 0){
						$title_obj = mysql_fetch_object($title_res);
						$title = $title_obj->location_title;
						
						$insert_query = "INSERT INTO project_locations SET project_id = '".$_SESSION['idp']."', location_title = '".$title."', location_parent_id = '".$parentArray[0]."', created_date = now(), created_by = '".$_SESSION['ww_is_builder']."', last_modified_date = now(), last_modified_by = '".$_SESSION['ww_is_builder']."'";
						mysql_query($insert_query);
						$insertedVaule = mysql_insert_id();
						$parentArray[sizeof($parentArray)] = $insertedVaule;
						//Update Location Tree and Location Name Tree
						$locIdTree = $obj->subLocationsIDS($insertedVaule, ' > ');
						$locNameTree = $obj->subLocations($insertedVaule, ' > ');
						$updateQRY = "UPDATE project_locations SET location_id_tree = '".$locIdTree."', location_name_tree = '".$locNameTree."' WHERE location_id = ".$insertedVaule;
						mysql_query($updateQRY);
						$insertArray[] = $insertValues;
					}
				}
			}else{
				#die('Execusion Done');
			}
			//GS
			array_shift($parentArray);
			array_shift($remain);
		}
		return $all;
	}

	$allCategories = array();
	$allCategories = get_cats($copyLocation);
	if(in_array($pasteLocation, $allCategories)){
		echo 'Error';
	}else{
		$result = pasteLocations($copyLocation, $pasteLocation);
		if(is_array($result)){
			$_SESSION['pasteCount']++;
			echo 'Location Paste';
		}else{
			echo 'Error in Pasing';
		}
	}
	
}
?>