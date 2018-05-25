<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');
define("IMPORTFILEPATH", '../sync/Import');
define("SIGNDESTINATIONPATH", '../inspections/qa_signoff');
//Header Secttion for include and objects 

if(isset($_REQUEST['upload_sign'])){
	$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId']))));
	$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
	$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
	$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
	$deviceType = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['device']))));
	
	if(!is_dir(IMPORTFILEPATH .'/'.$globalId)){
		@mkdir(IMPORTFILEPATH .'/'.$globalId, 0777);
	}

	$IMPORTFILEPATH = IMPORTFILEPATH .'/'.$globalId;
	
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
			'message' => 'User Authentication Fail !',
			'data' => ''
		);
		echo '['.json_encode($output).']';
		die;
	}*/
$db->recursive_remove_directory(IMPORTFILEPATH.'/qa_signoff');
	@mkdir(IMPORTFILEPATH.'/qa_signoff/', 0777);
	$extension = array('application/octet-stream');
#	if((in_array($_FILES["uploadZip"]["type"], $extension)) && ($_FILES["uploadZip"]["size"] < 200000)){
		if($_FILES["uploadZip"]["error"] > 0){
					$output = array(
						'status' => false,
						'message' => 'Files Uploading Failed !',
						'data' => ''
					);
					echo '['.json_encode($output).']';
		}else{
			$zipName = $db->updateImportTable_new(IMPORTFILEPATH, $globalId, $deviceType, 'qasignImages');
			move_uploaded_file($_FILES["uploadZip"]["tmp_name"], IMPORTFILEPATH .'/'. $zipName.'.zip');
			if($db->extractZip(IMPORTFILEPATH .'/'. $zipName.'.zip', IMPORTFILEPATH)){
				if($db->copyFilestoFolder(IMPORTFILEPATH.'/qa_signoff', SIGNDESTINATIONPATH)){
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
#	}else{$output = array('status' => true,'message' => 'Invalid Uploaded Zip ','data' => '');echo '['.json_encode($output).']';}
}else{
	$output = array(
		'status' => false,
		'message' => 'Invalid Url !',
		'data' => ''
	);
	echo '['.json_encode($output).']';
}?>