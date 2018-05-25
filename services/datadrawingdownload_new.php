<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');
define("DRAWINGSOURCEPATH", '../inspections/drawing');
//Header Secttion for include and objects 

if(isset($_REQUEST['data_drawing'])){
$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId'])))); 
$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));

$noofFiles = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['noofFiles']))));
$imageLimit = isset($_REQUEST['imageLimit']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['imageLimit'])))) : 100;
$startIndex = isset($_REQUEST['startIndex']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['startIndex'])))) : 0;


define("EXPORTFILEPATH", '../sync/Export/' . $userId);

	if(isset($lastModifiedDate) && !empty($lastModifiedDate)){
		if($db->validateMySqlDate($lastModifiedDate)){}else{
			$output = array(
				'status' => false,
				'message' => 'Modified Date is not Valid !',
				'data' => ''
			);
			echo '['.json_encode($output).']';
			die;
		}
	}
	/*if($db->hashAuth($globalId, $authHash)){}else{
		$output = array(
			'status' => false,
			'message' => 'User Authentication Fail',
			'data' => ''
		);
		echo '['.json_encode($output).']';
		die;
	}*/
		$rsInspectionInspectedBy = mysql_query("SELECT NOW() as date");
	if(mysql_num_rows($rsInspectionInspectedBy) > 0){
		$iPadQueryInspectionInspectedBy = '';
		if($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy))
		{
			$date = $rowInspectionInspectedBy["date"];
		}
	}
	if(!is_dir(EXPORTFILEPATH)){
		@mkdir(EXPORTFILEPATH, 0777);
	}
//Remove Previous Files
	$db->recursive_remove_directory(EXPORTFILEPATH.'/drawing');
#		$db->recursive_remove_directory(EXPORTFILEPATH.'/'.$globalId);
//Add New Files
#	@mkdir(EXPORTFILEPATH.'/drawing/', 0777);
	@mkdir(EXPORTFILEPATH.'/drawing/', 0777);
	
	if(is_dir('../sync/Export/'.$globalId)){
		if(empty($lastModifiedDate)){
			$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			$projId = '';
			foreach($projectId as $pId){
				if($projId == ''){
					$projId = $pId['project_id'];
				}else{
					$projId .= ', '.$pId['project_id'];
				}
			}
			
			if(!empty($noofFiles)){
				$imageCountQry = $db->selQRY('count(*) AS imageCount', 'inspection_graphics', 'project_id IN ('.$projId.') AND graphic_type = "drawing" AND is_deleted = 0');
				$imageCount = $imageCountQry['imageCount'];
				if($imageCount > 0){
					$output = array(
						'status' => true,
						'message' => 'Total images are',
						'data' => $imageCount
					);
					echo '['.json_encode($output).']';
					die;
				}else{
					$output = array(
						'status' => false,
						'message' => 'No One Images Found',
						'data' => $imageCount
					);
					echo '['.json_encode($output).']';
					die;
				}
			}else{
				$imageName = $db->selQRYMultiple('graphic_name', 'inspection_graphics', 'project_id IN ('.$projId.') AND graphic_type = "drawing"  AND is_deleted = 0 ORDER BY inspection_id ASC LIMIT '.$startIndex.', '.$imageLimit);
//				print_r($imageName);die;
				$folder = opendir(DRAWINGSOURCEPATH);
				$pic_types = array("jpg", "jpeg", "gif", "png");
				$index = array();
				while ($file = readdir ($folder)) {
					foreach($imageName as $imgName){
						if($file == $imgName['graphic_name']){
							copy(DRAWINGSOURCEPATH.'/'.$file, EXPORTFILEPATH.'/drawing/'.$file);
						}
					}
				}
				$zipSource = EXPORTFILEPATH.'/drawing/';
				$db->createFile('last_modified_date.txt', $date, $zipSource);//Write File Here
				if($db->emptyDirectory($zipSource)){
					$db->compress($zipSource, 'drawing', EXPORTFILEPATH);
					$filename = 'drawing.zip';
					header("Content-type: application/zip;\n");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".filesize(EXPORTFILEPATH.'/'.$filename)."\n");
					header("Content-Disposition: attachment; filename=".$filename);
					ob_end_flush();
					@readfile(EXPORTFILEPATH.'/'.$filename);
				}else{
					$output = array(
						'status' => false,
						'message' => 'No One Images Found',
						'data' => ''
					);
					echo '['.json_encode($output).']';
					die;
				}
				closedir($folder);			
			}
		}else{
			$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			$projId = '';
			foreach($projectId as $pId){
				if($projId == ''){
					$projId = $pId['project_id'];
				}else{
					$projId .= ', '.$pId['project_id'];
				}
			}
			if(!empty($noofFiles)){
				$imageCountQry = $db->selQRY('count(*) AS imageCount', 'inspection_graphics', 'project_id IN ('.$projId.') AND graphic_type = "drawing" AND last_modified_date >= "'.$lastModifiedDate.'" AND is_deleted = 0');
				$imageCount = $imageCountQry['imageCount'];
				if($imageCount > 0){
					$output = array(
						'status' => true,
						'message' => 'Total images are',
						'data' => $imageCount
					);
					echo '['.json_encode($output).']';
					die;
				}else{
					$output = array(
						'status' => false,
						'message' => 'No One Images Found',
						'data' => $imageCount
					);
					echo '['.json_encode($output).']';
					die;
				}
			}else{
				$imageName = $db->selQRYMultiple('graphic_name', 'inspection_graphics', 'project_id IN ('.$projId.') AND graphic_type = "drawing"  AND last_modified_date >= "'.$lastModifiedDate.'" AND is_deleted = 0 ORDER BY inspection_id ASC LIMIT '.$startIndex.', '.$imageLimit);
				$folder = opendir(DRAWINGSOURCEPATH);
				$pic_types = array("jpg", "jpeg", "gif", "png");
				$index = array();
				while ($file = readdir ($folder)) {
					foreach($imageName as $imgName){
						if($file == $imgName['graphic_name']){
							copy(DRAWINGSOURCEPATH.'/'.$file, EXPORTFILEPATH.'/drawing/'.$file);
						}
					}
				}
				$zipSource = EXPORTFILEPATH.'/drawing/';
				$db->createFile('last_modified_date.txt', $date, $zipSource);//Write File Here
				if($db->emptyDirectory($zipSource)){
					$db->compress($zipSource, 'drawing', EXPORTFILEPATH);
					$filename = 'drawing.zip';
					header("Content-type: application/zip;\n");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: ".filesize(EXPORTFILEPATH.'/'.$filename)."\n");
					header("Content-Disposition: attachment; filename=".$filename);
					ob_end_flush();
					@readfile(EXPORTFILEPATH.'/'.$filename);
				}else{
					$output = array(
						'status' => false,
						'message' => 'No One Images Found',
						'data' => ''
					);
					echo '['.json_encode($output).']';
					die;
				}
				closedir($folder);			
			}
		}
	}
}
?>