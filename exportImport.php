<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

include 'services/servicesQurey.php';
$db = new QRY_Class();
if($_REQUEST['task'] == 'resizeImage'){
	@mkdir('./inspections/photoTest/', 0777);
	echo $db->copyFilestoFolder('./inspections/photo', 'inspections/photoTest', 799, 799);
}
if($_REQUEST['task'] == 'resizeDrawing'){
	@mkdir('./inspections/drawingTest/', 0777);
	echo $db->copyFilestoFolder('./inspections/drawing', 'inspections/drawingTest', 1599, 1599);
}
if($_REQUEST['task'] == 'resizeSignoff'){
	@mkdir('./inspections/drawingTest/', 0777);
	echo $db->copyFilestoFolder('./inspections/signoff', 'inspections/signoffTest', 799, 799);
}
/*
if($_REQUEST['task'] == 'test'){
	$paths = $object->selQRYMultiple('export_files_id, path', 'exportData', 'device = "ipad"');

	foreach($paths as $p){
		$path = explode('/', $p['path']);
	#	echo $path[sizeof($path) - 3];
	echo $updatQry = "UPDATE exportData SET userid = '".$path[sizeof($path) - 3]."' WHERE export_files_id = '".$p['export_files_id']."'";
		mysql_query($updatQry);
		
	}
}	

if($_REQUEST['task'] == 'test1'){
	$folder = opendir(IMAGESOURCEPATH);
	$pic_types = array("jpg", "jpeg", "gif", "png");
	$index = array();
	while ($file = readdir ($folder)) {
		if(in_array(substr(strtolower($file), strrpos($file,".") + 1), $pic_types)){
			copy (IMAGESOURCEPATH.'/'.$file, EXPORTFILEPATH.'/'.$file);
		}
	}
}

///Hacer Database
$con1 = mysql_connect('localhost', 'wisework_fxbytes', 'fxbytes!@#');
$db1 = mysql_select_db('hacer', $con1);

///Fxdev Database
$pLocation = selQRYMultiple('location_id, location_parent_id, location_title', 'project_locations', 'project_id = 3 and is_deleted = 0', $con1);
#print_r($pLocation);die;
$con2 = mysql_connect('localhost', 'wisework_fxbytes', 'fxbytes!@#');
$db2 = mysql_select_db('fxdev', $con2);
#pasteLocations($pLocation[0]['location_id'], 0, $con1, $con2);
foreach($pLocation as $projLoc){
#	pasteLocations($projLoc['location_id'], 0, $con1, $con2);
#	$extd = explode('_', $projLoc['inspection_sign_image']);
echo	$insertQry = "INSERT INTO  project_locations SET 
						location_title = '".$projLoc['location_title']."',
						location_parent_id = '".$projLoc['location_parent_id']."',
						project_id = '168',
						created_by = '98'";
					
		mysql_query($insertQry, $con2) or die(mysql_error());
}

function pasteLocations($cat, $pId, $con_1, $con_2){
		$i=1;
		$remain = array();
		$all = array();
		$insertArray = array();
		$parentArray = array();
		
		$remain[0] = $cat;
echo		$title_select = "SELECT location_title FROM project_locations WHERE location_id = '".$cat."' and is_deleted = 0";
		$title_res = mysql_query($title_select, $con_1);
		if(mysql_num_rows($title_res) > 0){
			$title_obj = mysql_fetch_object($title_res);
			$title = $title_obj->location_title;
			$insert_query = "INSERT INTO project_locations SET
								project_id = '168',
								location_title = '".addslashes($title)."',
								location_parent_id = '".$pId."',
								created_date = now(),
								created_by = '98'";
			mysql_query($insert_query, $con_2);
			$parentArray[0] = mysql_insert_id();
		}
		
		while(sizeof($remain)>0){
			$curr = $remain[0];
			$qSelect = "select location_id from project_locations where location_parent_id = ".$curr." and is_deleted = 0";
			$res = mysql_query($qSelect, $con_1);
			while($row = mysql_fetch_array($res)){
				$all[$i++]=$row['location_id'];
				$remain[sizeof($remain)]=$row['location_id'];
			}
			//GS
			$newValues = array_diff($all, $insertArray);
			if(!empty($newValues)){
				foreach($newValues as $insertValues){
					$title_select = "SELECT location_title FROM project_locations WHERE location_id = '".$insertValues."' and is_deleted = 0";
					$title_res = mysql_query($title_select, $con_1);
					if(mysql_num_rows($title_res) > 0){
						$title_obj = mysql_fetch_object($title_res);
						$title = $title_obj->location_title;
						
						$insert_query = "INSERT INTO project_locations SET
											project_id = '168',
											location_title = '".addslashes($title)."',
											location_parent_id = '".$parentArray[0]."',
											created_date = now(),
											created_by = '98'";
						mysql_query($insert_query, $con_2);
						
						$parentArray[sizeof($parentArray)] = mysql_insert_id();
						
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
function selQRYMultiple($select, $table, $where, $conId){
//	echo "SELECT ".$select." FROM ".$table." WHERE ".$where;
	$RS = mysql_query("SELECT ".$select." FROM ".$table." WHERE ".$where, $conId);
	if(mysql_num_rows($RS) > 0){
		while($ROW = mysql_fetch_assoc($RS)){
			$values[]= $ROW;
		}
		return $values;
	}else{
		return false;
	}
}*/?>