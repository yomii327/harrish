<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');
define("IMAGESOURCEPATH", '../inspections/photo');
//Header Secttion for include and objects 

if(isset($_REQUEST['data_images'])){

$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId'])))); 
$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));

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
	if($db->hashAuth($globalId)){}else{
		$output = array(
			'status' => false,
			'message' => 'User Authentication Fail',
			'data' => ''
		);
		echo '['.json_encode($output).']';
		die;
	}
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
	$db->recursive_remove_directory(EXPORTFILEPATH.'/photo');
//Add New Files
	@mkdir(EXPORTFILEPATH.'/photo/', 0777);
	
	if(is_dir('../sync/Export/')){
		if(empty($lastModifiedDate)){
//Images
			$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			foreach($projectId as $pId){
				$folder = opendir(IMAGESOURCEPATH);
				$pic_types = array("jpg", "jpeg", "gif", "png");
				$index = array();
				while ($file = readdir ($folder)) {
					if(in_array(substr(strtolower($file), strrpos($file,".") + 1), $pic_types)){
						if(preg_match("/".$pId['project_id']."_/", $file)){
							copy(IMAGESOURCEPATH.'/'.$file, EXPORTFILEPATH.'/photo/'.$file);
						}
						#array_push($index, $file);
					}
				}
			}
			$zipSource = EXPORTFILEPATH.'/photo/';
			$db->createFile('last_modified_date.txt', $date, $zipSource);//Write File Here
			if($db->emptyDirectory($zipSource)){
				#$zipFileName = $db->updateExportTable(EXPORTFILEPATH);
				$db->compress($zipSource, 'photo', EXPORTFILEPATH);
//Code for Download the zip file
				$filename = 'photo.zip';
			
				header("Content-type: application/zip;\n");
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".filesize(EXPORTFILEPATH.'/'.$filename)."\n");
				header("Content-Disposition: attachment; filename=".$filename);
				ob_end_flush();
				@readfile(EXPORTFILEPATH.'/'.$filename);
//Code for Download the zip file
			}else{
				$output = array(
					'status' => false,
					'message' => 'No One Images Found',
					'last_modified_date' => $date,
					'data' => ''
				);
				echo '['.json_encode($output).']';
				die;
			}
			closedir($folder);			
		}else{//Modified Date is comes
			// get user projects from user_projects table
			// get graphichs name from inspection_graphics table
			// make zip of all files
			// in case of no data, send below written JSON
			
			$projectId = $db->getRecordByKey('user_projects', 'user_id', $userId, 'project_id');
			$folder = opendir(IMAGESOURCEPATH);
			$pic_types = array("jpg", "jpeg", "gif", "png");
			$index = array();
			$projectIds = '';
			foreach($projectId as $pId){
				if($projectIds == ''){ $projectIds .= $pId['project_id']; }else{ $projectIds .= ', '.$pId['project_id']; }
			}
			$imagesId = $db->selQRYMultiple('graphic_name', 'inspection_graphics', 'project_id In ('.$projectIds.') and last_modified_date >= "'.$lastModifiedDate.'" and is_deleted = 0');
			$images = array();
			foreach($imagesId as $image){
				array_push($images, $image['graphic_name']);
			}
			if(!empty($images)){
				while($file = readdir ($folder)){
					if(in_array(substr(strtolower($file), strrpos($file,".") + 1), $pic_types)){
						if(in_array($file, $images)){
#							if(date("Y-m-d H:i:s", filemtime(IMAGESOURCEPATH.'/'.$file)) > $lastModifiedDate){
								copy(IMAGESOURCEPATH.'/'.$file, EXPORTFILEPATH.'/photo/'.$file);
#							}
						}
					}
				}
			}else{
				$output = array(
					'status' => true,
					'message' => 'No Update',
					'last_modified_date' => $date,
					'data' => ''
				);
				echo '['.json_encode($output).']';
				die;
			}
			$zipSource = EXPORTFILEPATH.'/photo/';
			$db->createFile('last_modified_date.txt', $date, $zipSource);//Write File Here
			if($db->emptyDirectory($zipSource)){
				#$zipFileName = $db->updateExportTable(EXPORTFILEPATH);
				$db->compress($zipSource, 'photo', EXPORTFILEPATH);
//Code for Download the zip file
				$filename = 'photo.zip';
			
				header("Content-type: application/zip;\n");
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".filesize(EXPORTFILEPATH.'/'.$filename)."\n");
				header("Content-Disposition: attachment; filename=".$filename);
				ob_end_flush();
				@readfile(EXPORTFILEPATH.'/'.$filename);
//Code for Download the zip file
			}else{
				$output = array(
					'status' => true,
					'message' => 'No One Images Found',
					'last_modified_date' => $date,
					'data' => ''
				);
				echo '['.json_encode($output).']';
				die;
			}
			closedir($folder);
		}
	}else{
		@mkdir('../sync/Export/',0777);
		die;
	}
}
?>