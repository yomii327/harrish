<?php
//Header Secttion for include and objects 
error_reporting(0);
ini_set('display_errors', '0');

include_once("servicesQurey.php");
$db = new QRY_Class();
set_time_limit(360000);
define("ZIPPASSWORD", '20W!seWork@r12');
define("EXPORTFILEPATH", '../sync/Export');
define("IMAGESOURCEPATH", '../project_drawings');
//Header Secttion for include and objects 

if(isset($_REQUEST['drawing_management'])){

$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId'])))); 
$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
$projectIDs = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['projectIDs']))));
$deviceType = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['device']))));
$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
$isThumbnail = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['isThumbnail']))));
$noofFiles = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['noofFiles']))));
$imageLimit = isset($_REQUEST['imageLimit']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['imageLimit'])))) : 100;
$startIndex = isset($_REQUEST['startIndex']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['startIndex'])))) : 0;
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
//Remove Previous Files
	$db->recursive_remove_directory(EXPORTFILEPATH.'/'.$globalId.'/project_drawings');
//Add New Files
	@mkdir(EXPORTFILEPATH.'/'.$globalId, 0777);
	@mkdir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/', 0777);
	if(is_dir('../sync/Export/'.$globalId)){
		$drawPath = '';
		$zipName = 'project_drawings';
		if($isThumbnail == 'Y'){
			$drawPath = 'thumbnail';
			$zipName = 'project_drawings_thumbnail';
		}
		$projectId = explode(',', $projectIDs);
		$loopCount = sizeof($projectId);
		
		if(empty($lastModifiedDate)){
			if(!empty($noofFiles)){
				$countDrawImage = $db->selQRY('count(*) AS imageCount', 'draw_mgmt_images', ' project_id IN ('.$projectIDs.') AND is_deleted = 0 ORDER BY draw_mgmt_images_id');
				$imageCount = $countDrawImage['imageCount'];
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
				for($i=0;$i<$loopCount;$i++){
					$imageName = $db->selQRYMultiple('project_id, draw_mgmt_images_id, draw_mgmt_images_name, draw_mgmt_images_thumbnail', 'draw_mgmt_images', 'project_id = "'.$projectId[$i].'" AND is_deleted = 0 ORDER BY draw_mgmt_images_id ASC LIMIT '.$startIndex.', '.$imageLimit);
					if(!is_dir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$projectId[$i])){
						@mkdir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$projectId[$i], 0777);
					}
					if($isThumbnail == 'Y'){
						$imgSource = IMAGESOURCEPATH.'/'.$projectId[$i].'/thumbnail';
						$folder = opendir($imgSource);
						$pic_types = array("jpg", "jpeg", "png", "JPG", "JPEG", "PNG");
						foreach($imageName as $imgName){
							if(file_exists($imgSource.'/'.$imgName['draw_mgmt_images_thumbnail'])){
								copy($imgSource.'/'.$imgName['draw_mgmt_images_thumbnail'], EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$projectId[$i].'/'.$imgName['draw_mgmt_images_thumbnail']);
							}
						}
					}else{
						$imgSource = IMAGESOURCEPATH.'/'.$projectId[$i];
						$folder = opendir($imgSource);
						$pic_types = array("jpg", "jpeg", "png", "JPG", "JPEG", "PNG");
						foreach($imageName as $imgName){
							if(file_exists($imgSource.'/'.$imgName['draw_mgmt_images_name'])){
								copy($imgSource.'/'.$imgName['draw_mgmt_images_name'], EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$projectId[$i].'/'.$imgName['draw_mgmt_images_name']);
							}
						}
					}
				}
			}
		}else{//Last Modified Date comes
			if(!empty($noofFiles)){
				$countDrawImage = $db->selQRY('count(*) AS imageCount', 'draw_mgmt_images', ' project_id IN ('.$projectIDs.') AND last_modified_date >= "'.$lastModifiedDate.'" ORDER BY draw_mgmt_images_id');
				$imageCount = $countDrawImage['imageCount'];
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
				for($i=0;$i<$loopCount;$i++){
					$imageName = $db->selQRYMultiple('project_id, draw_mgmt_images_id, draw_mgmt_images_name, draw_mgmt_images_thumbnail', 'draw_mgmt_images', 'project_id = "'.$projectId[$i].'" AND last_modified_date >= "'.$lastModifiedDate.'" ORDER BY draw_mgmt_images_id ASC LIMIT '.$startIndex.', '.$imageLimit);
					if(!is_dir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$projectId[$i])){
						@mkdir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$projectId[$i], 0777);
					}
					if($isThumbnail == 'Y'){
						$imgSource = IMAGESOURCEPATH.'/'.$projectId[$i].'/thumbnail';
						$folder = opendir($imgSource);
						$pic_types = array("jpg", "jpeg", "png", "JPG", "JPEG", "PNG");
						foreach($imageName as $imgName){
							if(file_exists($imgSource.'/'.$imgName['draw_mgmt_images_thumbnail'])){
								copy($imgSource.'/'.$imgName['draw_mgmt_images_thumbnail'], EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$projectId[$i].'/'.$imgName['draw_mgmt_images_thumbnail']);
							}
						}
					}else{
						$imgSource = IMAGESOURCEPATH.'/'.$projectId[$i];
						$folder = opendir($imgSource);
						$pic_types = array("jpg", "jpeg", "png", "JPG", "JPEG", "PNG");
						foreach($imageName as $imgName){
							if(file_exists($imgSource.'/'.$imgName['draw_mgmt_images_name'])){
								copy($imgSource.'/'.$imgName['draw_mgmt_images_name'], EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$projectId[$i].'/'.$imgName['draw_mgmt_images_name']);
							}
						}
					}
				}
			}	
		}
		
		$zipSource = EXPORTFILEPATH.'/'.$globalId.'/project_drawings/';
		$db->createFile('last_modified_date.txt', $date, $zipSource);//Write File Here
		if($db->emptyDirectory($zipSource)){
			$db->compress($zipSource, $zipName, EXPORTFILEPATH.'/'.$globalId);
			$filename = $zipName.'.zip';
			header("Content-type: application/zip;\n");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: ".filesize(EXPORTFILEPATH.'/'.$globalId.'/'.$filename)."\n");
			header("Content-Disposition: attachment; filename=".$filename);
			ob_end_flush();
			@readfile(EXPORTFILEPATH.'/'.$globalId.'/'.$filename);
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
}?>