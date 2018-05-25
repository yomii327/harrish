<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');

define("IMAGEDESTINATIONPATH", '../inspections/photo');
//Header Secttion for include and objects 

if(isset($_REQUEST['upload_image'])){
	$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId']))));
	$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
	$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
	$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
	$deviceType = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['device']))));
	
	define("IMPORTFILEPATH", '../sync/Import/'.$globalId);
	
	if(!is_dir(IMPORTFILEPATH)){
		@mkdir(IMPORTFILEPATH, 0777);
	}

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
	$db->recursive_remove_directory(IMPORTFILEPATH.'/photo');
	@mkdir(IMPORTFILEPATH.'/photo/', 0777);
	$extension = array('application/octet-stream');
	if($_FILES["uploadZip"]["error"] > 0){
		$output = array(
			'status' => false,
			'message' => 'Files Uploading Failed !',
			'data' => ''
		);
		echo '['.json_encode($output).']';
	}else{
		$zipName = $db->updateImportTable_new(IMPORTFILEPATH, $globalId, $deviceType, 'images');
		move_uploaded_file($_FILES["uploadZip"]["tmp_name"], IMPORTFILEPATH .'/'. $zipName.'.zip');
		if($db->extractZip(IMPORTFILEPATH .'/'. $zipName.'.zip', IMPORTFILEPATH)){
			if($db->copyFilestoFolder(IMPORTFILEPATH.'/photo', IMAGEDESTINATIONPATH, 799, 799)){
				$output = array(
					'status' => true,
					'message' => 'Files Uploaded Successfully !',
					'last_modified_date' => date("Y-m-d H-i-s"),
					'data' => ''
				);
				echo '['.json_encode($output).']';
			}else{
				$output = array(
					'status' => false,
					'message' => 'Files Uploading Failed !',
					'data' => ''
				);
				echo '['.json_encode($output).']';
			}
		}
	}
}
?>