<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();
define("ZIPPASSWORD", '20W!seWork@r12');
define("TEXTDESTINATIONPATH", '../Import');
//Header Secttion for include and objects 

$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId']))));
$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
$deviceType = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['device']))));

$sync_url = $db->curPageURL($_POST);

define("IMPORTFILEPATH", '../sync/Import/'.$userId);


$data = array();
if(isset($lastModifiedDate) && !empty($lastModifiedDate)){
	if($db->validateMySqlDate($lastModifiedDate)){}else{
		$output = array(
			'status' => false,
			'message' => 'Modified Date is not Valid !',
			'data' => ''
		);
		echo '['.json_encode($output).']';
	}
}

if(isset($_REQUEST['upload_sql'])){
	if(file_exists(IMPORTFILEPATH.'/wise_worker.sqlite'))
		unlink(IMPORTFILEPATH.'/wise_worker.sqlite');

	if (! is_dir ('../sqlite_files/'))
		@mkdir('../sqlite_files/', 0777);
		
	if (! is_dir ('../sqlite_files/'.$userId))
		@mkdir('../sqlite_files/'.$userId, 0777);
		
	if($_FILES["uploadSQL"]["error"] > 0){
		$output = array(
			'status' => false,
			'message' => 'File Uploading Failed',
			'data' => ''
		);
		echo '['.json_encode($output).']';
		die;
	}else{
		$extension = end(explode(".", $_FILES["uploadSQL"]["name"]));
		$zipName = $db->updateImportTable_updated(IMPORTFILEPATH.'/', $_REQUEST['userId'], $deviceType, 'sqliteFile', $sync_url);
		if(move_uploaded_file($_FILES["uploadSQL"]["tmp_name"], IMPORTFILEPATH .'/'. $zipName.'.zip')){
			if($db->extractZip(IMPORTFILEPATH .'/'. $zipName.'.zip', IMPORTFILEPATH)){
				if(copy(IMPORTFILEPATH.'/wise_worker.sqlite', '../sqlite_files/'.$userId.'/'.$zipName.'_'.date('Y-m-d H-i-s').'.sqlite')){
					$output = array(
						'status' => true,
						'message' => 'Import Successfully Done !',
						'last_modified_date' => date("Y-m-d H:i:s"),
						'data' => ''
					);
					echo '['.json_encode($output).']';
					die;
				}else{
					$output = array(
						'status' => false,
						'message' => 'File Not copy in respective folder !',
						'data' => ''
					);
					echo '['.json_encode($output).']';
					die;
				}
			}else{
				$output = array(
					'status' => false,
					'message' => 'Unable to extract zip !',
					'data' => ''
				);
				echo '['.json_encode($output).']';
				die;
			}
		}else{
			$output = array(
				'status' => false,
				'message' => 'File Uploading Failed',
				'data' => ''
			);
			echo '['.json_encode($output).']';
			die;
		}
	}
}else{
	$output = array(
		'status' => false,
		'message' => 'Invalid Url !',
		'data' => ''
	);
	echo '['.json_encode($output).']';
}
?>