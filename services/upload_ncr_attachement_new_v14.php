<?php
//Header Secttion for include and objects 
#error_reporting(E_ALL);
#ini_set('display_errors', '1');

include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');
define("NCRDESTINATIONPATH", '../inspections/ncr_files');
//Header Secttion for include and objects 

if(isset($_REQUEST['upload_ncr_files'])){
	$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId']))));
	$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
	$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
	$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
	$deviceType = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['device']))));
	$requestFor = isset($_REQUEST['requestFor']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['requestFor'])))) : 'inspections';

	$sync_url = $db->curPageURL($_POST);
	
	if(!is_dir('../sync/Import/'.$globalId)){
		@mkdir('../sync/Import/'.$globalId, 0777);
	}

	define("IMPORTFILEPATH", '../sync/Import'.'/'.$globalId);

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
	
#	$db->recursive_remove_directory(IMPORTFILEPATH.'/ncr_files');
	@mkdir(IMPORTFILEPATH.'/ncr_files/', 0777);
	$extension = array('application/octet-stream');
	if($_FILES["uploadZip"]["error"] > 0){
		$output = array(
			'status' => false,
			'message' => 'Files Uploading Failed !',
			'data' => ''
		);
		echo '['.json_encode($output).']';
	}else{
		if (file_exists(IMPORTFILEPATH .'/'. $_FILES["uploadZip"]["name"])){
			$output = array(
				'status' => true,
				'message' => $_FILES["uploadZip"]["name"] . 'already exists. !',
				'data' => ''
			);
			echo '['.json_encode($output).']';
		}else{
			$zipName = $db->updateImportTable_updated(IMPORTFILEPATH, $globalId, $deviceType, 'ncr_files', $sync_url);

			move_uploaded_file($_FILES["uploadZip"]["tmp_name"], IMPORTFILEPATH .'/'. $zipName.'.zip');

			if($db->extractZip(IMPORTFILEPATH .'/'. $zipName.'.zip', IMPORTFILEPATH)){
				if($db->copyFilestoFolder(IMPORTFILEPATH.'/ncr_files', NCRDESTINATIONPATH, 799, 799)){
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
}else{
	$output = array(
		'status' => false,
		'message' => 'Files Uploading Failed !',
		'data' => ''
	);
	echo '['.json_encode($output).']';
}
?>