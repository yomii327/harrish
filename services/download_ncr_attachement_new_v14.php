<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();
set_time_limit(360000);

define("ZIPPASSWORD", '20W!seWork@r12');
define("EXPORTFILEPATH", '../sync/Export');
define("IMAGESOURCEPATH", '../inspections/ncr_files');
//Header Secttion for include and objects 
#error_reporting(E_ALL);ini_set('display_errors', '1');

if(isset($_REQUEST['data_ncr_files'])){
#echo '<pre>';print_r($_REQUEST);die;
	$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId'])))); 
	$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
	$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
	//Upadted Dated : 04-09-2012
	$projectIDs = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['projectIDs']))));
	$deviceType = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['device']))));
	//Upadted Dated : 04-09-2012
	
	$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
	
	$noofFiles = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['noofFiles']))));
	$imageLimit = isset($_REQUEST['imageLimit']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['imageLimit'])))) : 70;
	$startIndex = isset($_REQUEST['startIndex']) ? $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['startIndex'])))) : 0;

	$sync_url = $db->curPageURL($_POST);

//Check mysql date is correct or not.
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
	$date = $db->getCurrentDateTime();
	
	$last_modified_graphic_date = $date;

//Remove Previous Files
	$db->recursive_remove_directory(EXPORTFILEPATH.'/'.$globalId.'/ncr_files');

//Add New Files
	@mkdir(EXPORTFILEPATH.'/'.$globalId, 0777);
	@mkdir(EXPORTFILEPATH.'/'.$globalId.'/ncr_files/', 0777);
	if(is_dir('../sync/Export/'.$globalId)){
	//Get projectids, userRole, issueTo in case sub contractor
		if(empty($lastModifiedDate)){
//Only Selected Projects Data comes		
//Get Accepted Ids	
			mysql_query('SET SESSION group_concat_max_len = 4294967295');
			$taskData = array();
			$taskData = $db->getRecordByQry('SELECT * FROM (SELECT task_detail_id AS taskdetailids FROM qa_ncr_task_detail WHERE project_id IN ('.$projectIDs.') AND resource_type = "iPad" AND is_deleted = 0 
							UNION ALL
								SELECT qa_itp_checklist_id AS taskdetailids FROM qa_itp_checklist WHERE project_id IN ('.$projectIDs.') AND is_deleted = 0) AS a ORDER BY taskdetailids', 'G@urav');
			$taskIdArr = array();
			foreach($taskData as $tData){
				$taskIdArr[] = $tData['taskdetailids'];
			}

			if(!empty($noofFiles)){
				$attachCount = 0;

				$attachCountQry = $db->selQRY('count(*) AS attachCount', 'qa_ncr_attachments', 'project_id IN ('.$projectIDs.') AND is_deleted = 0 AND task_detail_id IN ('.join(',', $taskIdArr).') AND resource_type = "iPad" ORDER BY ncr_attachment_id');

				$attachCount = $attachCountQry['attachCount'];

				if($attachCount > 0){
					$output = array(
						'status' => true,
						'message' => 'Total images are',
						'data' => $attachCount
					);
					echo '['.json_encode($output).']';
					die;
				}else{
					$output = array(
						'status' => false,
						'message' => 'No One Images Found',
						'data' => $attachCount
					);
					echo '['.json_encode($output).']';
					die;
				}
			}else{
				$insIdes = $taskIdArr;

				$loopCount = count($insIdes);
				$insSelect = join(',', $taskIdArr);
				/*if($loopCount <= $imageLimit){
					$insSelect = '';
					for($j=0;$j<($loopCount-$startIndex);$j++){
						if($insSelect == ''){
							$insSelect = $insIdes[($startIndex+$j)];
						}else{
							if (!empty ($insIdes[($startIndex+$j)]))
								$insSelect .= ', '.$insIdes[($startIndex+$j)];
						}
					}
				}else{
					$insSelect = '';
					for($j=0;$j<$imageLimit;$j++){
						if($insSelect == ''){
							$insSelect = $insIdes[($startIndex+$j)];
						}else{
							if (!empty ($insIdes[($startIndex+$j)]))
								$insSelect .= ', '.$insIdes[($startIndex+$j)];
						}
					}
				}*/

				$imageName = $db->selQRYMultiple('attachment_file_name, last_modified_date', 'qa_ncr_attachments', 'task_detail_id IN ('.$insSelect.') AND is_deleted = 0 AND project_id IN ('.$projectIDs.') ORDER BY ncr_attachment_id ASC LIMIT '.$startIndex.', '. ($imageLimit + 1));
#print_r($imageName);die($insSelect);				
				$folder = opendir(IMAGESOURCEPATH);
				$index = array();
				foreach($imageName as $imgName){
					if(file_exists(IMAGESOURCEPATH.'/'.$imgName['attachment_file_name'])){
						copy(IMAGESOURCEPATH.'/'.$imgName['attachment_file_name'], EXPORTFILEPATH.'/'.$globalId.'/ncr_files/'.$imgName['attachment_file_name']);
					}
					$last_modified_graphic_date = $imgName['last_modified_date'];
				}
				
				$zipSource = EXPORTFILEPATH.'/'.$globalId.'/ncr_files/';
				if(empty($insIdes[($startIndex+$j+1)])){
					$last_modified_graphic_date = $date;
				}
				$db->createFile('last_modified_date.txt', $last_modified_graphic_date, $zipSource, 'w');//Write File Here
				if($db->emptyDirectory($zipSource)){
					$db->compress($zipSource, 'ncr_files', EXPORTFILEPATH.'/'.$globalId);
									
					$zipFileName = $db->updateExportTable_updated(EXPORTFILEPATH.'/'.$globalId.'/photo/', $_REQUEST['userId'], $deviceType, 'images', $sync_url);
					copy(EXPORTFILEPATH.'/'.$globalId.'/ncr_files.zip', EXPORTFILEPATH.'/'.$globalId.'/ncr_files_'.$zipFileName.'.zip');
				
					$filename = 'ncr_files_'.$zipFileName.'.zip';
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
		}else{//Last modified date comes so sub sequent sync
//new Project accepted condition GS Dated : 22-03-2013
			mysql_query('SET SESSION group_concat_max_len = 4294967295');
			$taskIds = $db->selQRY('GROUP_CONCAT(task_detail_id) AS taskdetailids', 'qa_ncr_task_detail', 'project_id IN ('.$projectIDs.') AND resource_type = "iPad" AND is_deleted = 0 AND last_modified_date >= "'.$lastModifiedDate.'" ORDER BY task_detail_id');

			if(!empty($noofFiles)){
				$attachCount = 0;
				
				$attachCountQry = $db->selQRY('count(*) AS attachCount', 'qa_ncr_attachments', 'project_id IN ('.$projectIDs.') AND is_deleted = 0 AND task_detail_id IN ('.$taskIds["taskdetailids"].') AND resource_type = "iPad" AND last_modified_date >= "'.$lastModifiedDate.'" ORDER BY ncr_attachment_id');
				$attachCount = $attachCountQry['attachCount'];

				if($attachCount > 0){
					$output = array(
						'status' => true,
						'message' => 'Total images are',
						'data' => $attachCount
					);
					echo '['.json_encode($output).']';
					die;
				}else{
					$output = array(
						'status' => false,
						'message' => 'No One Images Found',
						'data' => $attachCount
					);
					echo '['.json_encode($output).']';
					die;
				}
			}else{
				$insIdes = array();
				$insIdes = explode(',', $taskIds['taskdetailids']);
				
				$loopCount = count($insIdes);
				if($loopCount <= $imageLimit){
					$insSelect = '';
					for($j=0;$j<($loopCount-$startIndex);$j++){
						if($insSelect == ''){
							$insSelect = $insIdes[($startIndex+$j)];
						}else{
							if (!empty ($insIdes[($startIndex+$j)]))
								$insSelect .= ', '.$insIdes[($startIndex+$j)];
						}
					}
				}else{
					$insSelect = '';
					for($j=0;$j<$imageLimit;$j++){
						if($insSelect == ''){
							$insSelect = $insIdes[($startIndex+$j)];
						}else{
							if (!empty ($insIdes[($startIndex+$j)]))
								$insSelect .= ', '.$insIdes[($startIndex+$j)];
						}
					}
				}
				$imageName = $db->selQRYMultiple('attachment_file_name, last_modified_date', 'qa_ncr_attachments', 'task_detail_id IN ('.$insSelect.') AND resource_type = "iPad" AND is_deleted = 0 AND project_id IN ('.$projectIDs.') AND last_modified_date >= "'.$lastModifiedDate.'" ORDER BY ncr_attachment_id ASC LIMIT '.$startIndex.', '. ($imageLimit + 1));
				
				$folder = opendir(IMAGESOURCEPATH);
				$index = array();
				$count = 0;
				foreach($imageName as $imgName){
					$count ++;
					if ($count > $imageLimit){
						break;
					}
					if(file_exists(IMAGESOURCEPATH.'/'.$imgName['attachment_file_name'])){
						copy(IMAGESOURCEPATH.'/'.$imgName['attachment_file_name'], EXPORTFILEPATH.'/'.$globalId.'/photo/'.$imgName['graphic_name']);
					}
					$last_modified_graphic_date = $imgName['last_modified_date'];
				}
				if ($count <= $imageLimit){
					$last_modified_graphic_date = $date;
				}
				$zipSource = EXPORTFILEPATH.'/'.$globalId.'/ncr_files/';
				$db->createFile('last_modified_date.txt',$last_modified_graphic_date, $zipSource, 'w');//Write File Here
				if($db->emptyDirectory($zipSource)){
					$db->compress($zipSource, 'ncr_files', EXPORTFILEPATH.'/'.$globalId);
									
					$zipFileName = $db->updateExportTable_updated(EXPORTFILEPATH.'/'.$globalId.'/photo/', $_REQUEST['userId'], $deviceType, 'images', $sync_url);
					copy(EXPORTFILEPATH.'/'.$globalId.'/ncr_files.zip', EXPORTFILEPATH.'/'.$globalId.'/ncr_files_'.$zipFileName.'.zip');
				
					$filename = 'ncr_files_'.$zipFileName.'.zip';
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
		}
	}
}
?>