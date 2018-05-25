<?php
include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');
define("DRAWINGSOURCEPATH", '../inspections/drawing');
define("IMAGESOURCEPATH", '../inspections/photo');

if(isset($_REQUEST['non_conf_images'])){
	$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId'])))); 
	$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
	$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
	$projectIDs = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['projectIDs']))));
	$deviceType = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['device']))));
	$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
	$noofFiles = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['noofFiles']))));
	$imageLimit = isset($_REQUEST['imageLimit']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['imageLimit'])))) : 100;
	$startIndex = isset($_REQUEST['startIndex']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['startIndex'])))) : 0;
	
	define("EXPORTFILEPATH", '../sync/Export/'.$userId);
	
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
	$rsInspectionInspectedBy = mysql_query("SELECT NOW() as date");
	if(mysql_num_rows($rsInspectionInspectedBy) > 0){
		$iPadQueryInspectionInspectedBy = '';
		if($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
			$date = $rowInspectionInspectedBy["date"];
		}
	}
	if(!is_dir(EXPORTFILEPATH)){
		@mkdir(EXPORTFILEPATH, 0777);
	}
	$db->recursive_remove_directory(EXPORTFILEPATH.'/conform');
	@mkdir(EXPORTFILEPATH.'/conform/', 0777);
	@mkdir(EXPORTFILEPATH.'/conform/drawing/', 0777);
	@mkdir(EXPORTFILEPATH.'/conform/photo/', 0777);
	if(is_dir('../sync/Export/'.$globalId)){
		if(empty($lastModifiedDate)){
			$projectId = array();
			$projectId = explode(',', $projectIDs);
			if($noofFiles == 'Y'){
				$imageCountData = $db->selQRYMultiple('COUNT(qa_graphic_id) as imageCount', 'qa_graphics', 'project_id IN ('.$projectIDs.') AND is_deleted = 0');
				$imageCount = 0;
				$imageCount = $imageCountData[0]['imageCount'];
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
				$imageName = $db->selQRYMultiple('qa_graphic_id, qa_graphic_type, qa_graphic_name, last_modified_date', 'qa_graphics', 'project_id IN ('.$projectIDs.') AND is_deleted = 0 ORDER BY qa_graphic_id, qa_graphic_type LIMIT '.$startIndex.' , '.$imageLimit);
				
				$folderDrawing = opendir(DRAWINGSOURCEPATH);
				$folderImages = opendir(IMAGESOURCEPATH);
				$pic_types = array("jpg", "jpeg", "gif", "png");
				$index = array();
				$last_modified_graphic_date = "";
				foreach($imageName as $imgName){
					if($imgName['qa_graphic_type'] == 'images'){
						if(file_exists(IMAGESOURCEPATH.'/'.$imgName['qa_graphic_name'])){
							copy(IMAGESOURCEPATH.'/'.$imgName['qa_graphic_name'], EXPORTFILEPATH.'/conform/photo/'.$imgName['qa_graphic_name']);
							$last_modified_graphic_date = $imgName['last_modified_date'];
						}
					}else if($imgName['qa_graphic_type'] == 'drawing'){
						if(file_exists(DRAWINGSOURCEPATH.'/'.$imgName['qa_graphic_name'])){
							copy(DRAWINGSOURCEPATH.'/'.$imgName['qa_graphic_name'], EXPORTFILEPATH.'/conform/drawing/'.$imgName['qa_graphic_name']);
							$last_modified_graphic_date = $imgName['last_modified_date'];
						}
					}
				}
				$zipSource = EXPORTFILEPATH.'/conform/';
				if($last_modified_graphic_date == ''){
					$last_modified_graphic_date = $date;
				}
				$db->createFile('last_modified_date.txt', $last_modified_graphic_date, $zipSource);//Write File Here
				if($db->emptyDirectory($zipSource)){
					$db->compress($zipSource, 'conform', EXPORTFILEPATH);
					$zipFileName = $db->updateExportTable_new(EXPORTFILEPATH.'/conform/', $_REQUEST['userId'], $deviceType, 'conformImages');
					copy(EXPORTFILEPATH.'/conform.zip', EXPORTFILEPATH.'/conform_'.$zipFileName.'.zip');
				
					$filename = 'conform.zip';
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
		}else{//Last Modified Date comes
			$projectId = array();
			$projectId = explode(',', $projectIDs);
			if($noofFiles == 'Y'){
				$imageCountData = $db->selQRYMultiple('COUNT(qa_graphic_id) as imageCount', 'qa_graphics', 'project_id IN ('.$projectIDs.') AND is_deleted = 0 AND last_modified_date >= "'.$lastModifiedDate.'"');
				$imageCount = 0;
				$imageCount = $imageCountData[0]['imageCount'];
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
				$imageName = $db->selQRYMultiple('qa_graphic_id, qa_graphic_type, qa_graphic_name, last_modified_date', 'qa_graphics', 'project_id IN ('.$projectIDs.') AND is_deleted = 0 AND last_modified_date >= "'.$lastModifiedDate.'" ORDER BY qa_graphic_id, qa_graphic_type LIMIT '.$startIndex.' , '.$imageLimit);

				$folderDrawing = opendir(DRAWINGSOURCEPATH);
				$folderImages = opendir(IMAGESOURCEPATH);
				$pic_types = array("jpg", "jpeg", "gif", "png");
				$index = array();
				$last_modified_graphic_date = "";
				foreach($imageName as $imgName){
					if($imgName['qa_graphic_type'] == 'images'){
						if(file_exists(IMAGESOURCEPATH.'/'.$imgName['qa_graphic_name'])){
							copy(IMAGESOURCEPATH.'/'.$imgName['qa_graphic_name'], EXPORTFILEPATH.'/conform/photo/'.$imgName['qa_graphic_name']);
							$last_modified_graphic_date = $imgName['last_modified_date'];
						}
					}else if($imgName['qa_graphic_type'] == 'drawing'){
						if(file_exists(DRAWINGSOURCEPATH.'/'.$imgName['qa_graphic_name'])){
							copy(DRAWINGSOURCEPATH.'/'.$imgName['qa_graphic_name'], EXPORTFILEPATH.'/conform/drawing/'.$imgName['qa_graphic_name']);
							$last_modified_graphic_date = $imgName['last_modified_date'];
						}
					}
				}
				$zipSource = EXPORTFILEPATH.'/conform/';
				if($last_modified_graphic_date == ''){
					$last_modified_graphic_date = $date;
				}
				$db->createFile('last_modified_date.txt', $last_modified_graphic_date, $zipSource);//Write File Here
				if($db->emptyDirectory($zipSource)){
					$db->compress($zipSource, 'conform', EXPORTFILEPATH);
					$zipFileName = $db->updateExportTable_new(EXPORTFILEPATH.'/conform/', $_REQUEST['userId'], $deviceType, 'conformImages');
					copy(EXPORTFILEPATH.'/conform.zip', EXPORTFILEPATH.'/conform_'.$zipFileName.'.zip');
				
					$filename = 'conform.zip';
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