<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();

define("ZIPPASSWORD", '20W!seWork@r12');
define("IMPORTFILEPATH", '../sync/Import');
define("DRAWINGDESTINATIONPATH", '../inspections/drawing');
//Header Secttion for include and objects 

if(isset($_REQUEST['upload_drawing'])){
	$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId']))));
	$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
	$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
	$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
	$deviceType = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['device']))));
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
	/*if($db->hashAuth($globalId, $authHash)){
	}else{
		$output = array(
			'status' => false,
			'message' => 'User Authentication Fail !',
			'data' => ''
		);
		echo '['.json_encode($output).']';
		die;
	}*/
#	print_r($_FILES);
$db->recursive_remove_directory(IMPORTFILEPATH.'/drawing');
	@mkdir(IMPORTFILEPATH.'/drawing/', 0777);
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
			#echo "Upload: " . $_FILES["uploadZip"]["name"] . "<br />Type: " . $_FILES["uploadZip"]["type"] . "<br />Size: " . ($_FILES["uploadZip"]["size"] / 1024) . " Kb<br />Temp file: " . $_FILES["uploadZip"]["tmp_name"] . "<br />";
			if (file_exists(IMPORTFILEPATH .'/'. $_FILES["uploadZip"]["name"])){
				$output = array(
					'status' => true,
					'message' => $_FILES["uploadZip"]["name"] . 'already exists. !',
					'data' => ''
				);
				echo '['.json_encode($output).']';
			}else{
				$zipName = $db->updateImportTable_new(IMPORTFILEPATH, $globalId, $deviceType, 'drawingImages');
				move_uploaded_file($_FILES["uploadZip"]["tmp_name"], IMPORTFILEPATH .'/'. $zipName.'.zip');
				#echo "Stored in: " . IMPORTFILEPATH .'/'. $_FILES["uploadZip"]["name"];
				if($db->extractZip(IMPORTFILEPATH .'/'. $zipName.'.zip', IMPORTFILEPATH)){
					if($db->copyFilestoFolder(IMPORTFILEPATH.'/drawing', DRAWINGDESTINATIONPATH, 1600, 1600)){
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
#	}else{$output = array('status' => true,'message' => 'Invalid Uploaded Zip ','data' => '');echo '['.json_encode($output).']';}
}?>