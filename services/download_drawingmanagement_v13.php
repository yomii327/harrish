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

$sync_url = $db->curPageURL($_POST);

	if(isset($lastModifiedDate) && !empty($lastModifiedDate)){
		if(strlen($lastModifiedDate) > 19){
			$lastModifiedDate = substr($lastModifiedDate, -19);
			$dateArray = explode('-', $lastModifiedDate);
			if(substr($dateArray[0], 0, 2) != 20){
				$dateArray[0] = '20'.substr($dateArray[0], 2, 2);
				$lastModifiedDate = implode('-', $dateArray);
			}
		}
		if($db->validateMySqlDate($lastModifiedDate)){}else{
			$output = array(
				'status' => false,
				'message' => 'Modified Date is not Valid ! 1',
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
				//for($i=0;$i<$loopCount;$i++){
					$imageName = $db->selQRYMultiple('project_id, draw_mgmt_images_id, draw_mgmt_images_name, draw_mgmt_images_thumbnail,last_modified_date', 'draw_mgmt_images', 'project_id IN ('.$projectIDs.') AND is_deleted = 0 ORDER BY draw_mgmt_images_id ASC LIMIT '.$startIndex.', '.($imageLimit + 1));
					$last_modified_graphic_date = "";
					$count = 0;
					if($isThumbnail == 'Y'){
						$folder = opendir($imgSource);
						$pic_types = array("jpg", "jpeg", "png", "JPG", "JPEG", "PNG");
						foreach($imageName as $imgName){
							$count ++;
							if ($count > $imageLimit)
							{
								break;
							}
							$imgSource = IMAGESOURCEPATH.'/'.$imgName['project_id'].'/thumbnail';
							if(!is_dir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$imgName['project_id'])){
								@mkdir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$imgName['project_id'], 0777);
							}
							if(file_exists($imgSource.'/'.$imgName['draw_mgmt_images_thumbnail'])){
								copy($imgSource.'/'.$imgName['draw_mgmt_images_thumbnail'], EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$imgName['project_id'].'/'.$imgName['draw_mgmt_images_thumbnail']);
							}
							$last_modified_graphic_date = $imgName['last_modified_date'];
						}
						if ($count <= $imageLimit)
						{
							$last_modified_graphic_date = $date;
						}
					}else{
						$folder = opendir($imgSource);
						$pic_types = array("jpg", "jpeg", "png", "JPG", "JPEG", "PNG");
						foreach($imageName as $imgName){
							$count ++;
							if ($count > $imageLimit)
							{
								break;
							}
							$imgSource = IMAGESOURCEPATH.'/'.$imgName['project_id'];
							if(!is_dir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$imgName['project_id'])){
								@mkdir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$imgName['project_id'], 0777);
							}
							if(file_exists($imgSource.'/'.$imgName['draw_mgmt_images_name'])){
								copy($imgSource.'/'.$imgName['draw_mgmt_images_name'], EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$imgName['project_id'].'/'.$imgName['draw_mgmt_images_name']);
							}
							$last_modified_graphic_date = $imgName['last_modified_date'];
						}
						if ($count <= $imageLimit)
						{
							$last_modified_graphic_date = $date;
						}
					}
				//}
			}
		}else{//Last Modified Date comes
			if(!empty($noofFiles)){
				$countDrawImage = $db->selQRY('count(*) AS imageCount', 'draw_mgmt_images', ' project_id IN ('.$projectIDs.') AND is_deleted = 0 AND last_modified_date >= "'.$lastModifiedDate.'" ORDER BY draw_mgmt_images_id');
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
				//for($i=0;$i<$loopCount;$i++){
					$imageName = $db->selQRYMultiple('project_id, draw_mgmt_images_id, draw_mgmt_images_name, draw_mgmt_images_thumbnail, last_modified_date', 'draw_mgmt_images', 'project_id IN ('.$projectIDs.') AND is_deleted = 0 AND last_modified_date >= "'.$lastModifiedDate.'" ORDER BY draw_mgmt_images_id ASC LIMIT '.$startIndex.', '. ($imageLimit+1));
					$last_modified_graphic_date = "";
					$count = 0;
					if($isThumbnail == 'Y'){
						$folder = opendir($imgSource);
						$pic_types = array("jpg", "jpeg", "png", "JPG", "JPEG", "PNG");
						foreach($imageName as $imgName){
							$count ++;
							if ($count > $imageLimit)
							{
								break;
							}
							$imgSource = IMAGESOURCEPATH.'/'.$imgName['project_id'].'/thumbnail';
							if(!is_dir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$imgName['project_id'])){
								@mkdir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$imgName['project_id'], 0777);
							}
							if(file_exists($imgSource.'/'.$imgName['draw_mgmt_images_thumbnail'])){
								copy($imgSource.'/'.$imgName['draw_mgmt_images_thumbnail'], EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$imgName['project_id'].'/'.$imgName['draw_mgmt_images_thumbnail']);
							}
							$last_modified_graphic_date = $imgName['last_modified_date'];
						}
						if ($count <= $imageLimit)
						{
							$last_modified_graphic_date = $date;
						}
					}else{
						$folder = opendir($imgSource);
						$pic_types = array("jpg", "jpeg", "png", "JPG", "JPEG", "PNG");
						foreach($imageName as $imgName){
							$count ++;
							if ($count > $imageLimit)
							{
								break;
							}
							$imgSource = IMAGESOURCEPATH.'/'.$imgName['project_id'];
							if(!is_dir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$imgName['project_id'])){
								@mkdir(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$imgName['project_id'], 0777);
							}
							if(file_exists($imgSource.'/'.$imgName['draw_mgmt_images_name'])){
								copy($imgSource.'/'.$imgName['draw_mgmt_images_name'], EXPORTFILEPATH.'/'.$globalId.'/project_drawings/'.$imgName['project_id'].'/'.$imgName['draw_mgmt_images_name']);
							}
							$last_modified_graphic_date = $imgName['last_modified_date'];
						}
						if ($count <= $imageLimit)
						{
							$last_modified_graphic_date = $date;
						}
					}
				//}
			}	
		}
		
		$zipSource = EXPORTFILEPATH.'/'.$globalId.'/project_drawings/';
		if($last_modified_graphic_date == ''){
			$last_modified_graphic_date = $date;
		}
		$db->createFile('last_modified_date.txt', $last_modified_graphic_date, $zipSource, 'w');//Write File Here
		
		if($db->emptyDirectory($zipSource)){
			$db->compress($zipSource, $zipName, EXPORTFILEPATH.'/'.$globalId);
			
			$zipFileName = $db->updateExportTable_updated(EXPORTFILEPATH.'/'.$globalId.'/project_drawings/', $_REQUEST['userId'], $deviceType, 'DrawingMgmt', $sync_url);
			copy(EXPORTFILEPATH.'/'.$globalId.'/'.$zipName.'.zip', EXPORTFILEPATH.'/'.$globalId.'/drawingmgmt_'.$zipFileName.'.zip');
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