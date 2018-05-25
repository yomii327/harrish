<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();
set_time_limit(360000);
define("ZIPPASSWORD", '20W!seWork@r12');
define("EXPORTFILEPATH", '../sync/Export');
define("IMAGESOURCEPATH", '../project_drawing_register_v1');
//Header Secttion for include and objects 

if(isset($_REQUEST['drawing_register'])){

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
	$db->recursive_remove_directory(EXPORTFILEPATH.'/'.$globalId.'/project_drawing_register');
//Add New Files
	@mkdir(EXPORTFILEPATH.'/'.$globalId, 0777);
	@mkdir(EXPORTFILEPATH.'/'.$globalId.'/project_drawing_register/', 0777);
	if(is_dir('../sync/Export/'.$globalId)){
		$drawPath = '';
		$zipName = 'project_drawing_register';

		$projectId = explode(',', $projectIDs);
		$projectId = array_map('trim', $projectId);
		$loopCount = sizeof($projectId);
//Fetch Data for attribute data start here
		$attributePemissionArr = array();
		$attributeData = $db->selQRYMultiple('project_id, user_id', 'user_projects', 'project_id IN ('.$projectIDs.') AND user_id = '.$globalId.' AND is_deleted = 0');
		foreach($attributeData as $attrData){
/*			$attr1Val = '';
			if($attrData['dr_attribute_1'] != ''){$attr1Val = $attrData['dr_attribute_1'];}
			$attr2Val = '';
			if($attrData['dr_attribute_2'] != ''){$attr2Val = $attrData['dr_attribute_2'];}
			$attributePemissionArr[$attrData['project_id']] = array($attr1Val, $attr2Val);*/
		}
#		print_r($attributePemissionArr);die;
//Fetch Data for attribute data end here
		if(empty($lastModifiedDate)){
			$imageCount = 0;
			if(!empty($noofFiles)){
				foreach($projectId as $key=>$value){
					$whereCon = "";
					$attr1Con = '';
/*					if($attributePemissionArr[$value][0] != "")
						$attr1Con = 'attribute1 IN ('.$attributePemissionArr[$value][0].') ';
					$attr2Con = '';
					if($attributePemissionArr[$value][1] != "")
						$attr2Con = 'attribute1 IN ('.$attributePemissionArr[$value][1].') ';
						
					if($attr1Con !="")
						$whereCon = ' AND '.$attr1Con;
				
					if($attr2Con !="")
						$whereCon = ' AND '.$attr2Con;
						
					if($attr1Con !="" && $attr2Con !="")
						$whereCon = ' AND ('.$attr1Con.' OR '.$attr2Con.')';
					
					if($whereCon == ""){
						$whereCon = "attribute1 = ''";
					}*/

					$countDrawImage = $db->selQRY('count(*) AS imageCount', 'drawing_register_module_one', 'project_id = '.$value.' '.$whereCon.' AND is_approved = 1 AND is_deleted = 0 ORDER BY id');				
					$imageCount += $countDrawImage['imageCount'];
				}
#				print_r($countDrawImage);echo $imageCount;die;
				if($imageCount > 0){
					$output = array(
						'status' => true,
						'message' => 'Total PDFs are',
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
				$drawingRegID = array();
				foreach($projectId as $key=>$value){
					$whereCon = "";
					$attr1Con = '';
/*					if($attributePemissionArr[$value][0] != "")
						$attr1Con = 'attribute1 IN ('.$attributePemissionArr[$value][0].') ';
					$attr2Con = '';
					if($attributePemissionArr[$value][1] != "")
						$attr2Con = 'attribute1 IN ('.$attributePemissionArr[$value][1].') ';
						
					if($attr1Con !="")
						$whereCon = ' AND '.$attr1Con;
				
					if($attr2Con !="")
						$whereCon = ' AND '.$attr2Con;
						
					if($attr1Con !="" && $attr2Con !="")
						$whereCon = ' AND ('.$attr1Con.' OR '.$attr2Con.')';
					
					if($whereCon == ""){
						$whereCon = "attribute1 = ''";
					}*/

					$countDrawImage = $db->selQRYMultiple('GROUP_CONCAT(id) as drawingids', 'drawing_register_module_one', 'project_id = '.$value.' '.$whereCon.' AND is_approved = 1 AND is_deleted = 0 ORDER BY id');

					$drawingids = 0;
					if($countDrawImage[0]['drawingids'] != ''){
						$drawingids = $countDrawImage[0]['drawingids'];
					}
					$drawingRegID[] = $drawingids;	
				}				
				$imageName = $db->selQRYMultiple('project_id, id, pdf_name, last_modified_date', 'drawing_register_module_one', 'project_id IN ('.$projectIDs.') AND id IN ('.join(",", $drawingRegID).') AND is_approved = 1 AND is_deleted = 0 ORDER BY last_modified_date, id ASC LIMIT '.$startIndex.', '.($imageLimit + 1), 'Yes');
				#print_r($imageName);die;
				$last_modified_graphic_date = "";
				$count = 0;

				$folder = opendir($imgSource);
				foreach($imageName as $imgName){
					$count ++;
					if ($count > $imageLimit){
						break;
					}
					$imgSource = IMAGESOURCEPATH.'/'.$imgName['project_id'];
					if(!is_dir(EXPORTFILEPATH.'/'.$globalId.'/project_drawing_register/'.$imgName['project_id'])){
						@mkdir(EXPORTFILEPATH.'/'.$globalId.'/project_drawing_register/'.$imgName['project_id'], 0777);
					}
					$imgNam = explode('.', $imgName['pdf_name']);
					if(file_exists($imgSource.'/'.$imgName['pdf_name'])){
						copy($imgSource.'/'.$imgName['pdf_name'], EXPORTFILEPATH.'/'.$globalId.'/project_drawing_register/'.$imgName['project_id'].'/'.$imgName['pdf_name']);

						#copy($imgSource.'/'.$imgNam[0].'.png', EXPORTFILEPATH.'/'.$globalId.'/project_drawing_register/'.$imgName['project_id'].'/'.$imgNam[0].'.png');
					}
					$last_modified_graphic_date = $imgName['last_modified_date'];
				}
				if ($count <= $imageLimit){
					$last_modified_graphic_date = $date;
				}
			}
		}else{//Last Modified Date comes
			if(!empty($noofFiles)){
				$imageCount = 0;
				foreach($projectId as $key=>$value){
					$whereCon = "";
					$attr1Con = '';
/*					if($attributePemissionArr[$value][0] != "")
						$attr1Con = 'attribute1 IN ('.$attributePemissionArr[$value][0].') ';
					$attr2Con = '';
					if($attributePemissionArr[$value][1] != "")
						$attr2Con = 'attribute1 IN ('.$attributePemissionArr[$value][1].') ';
						
					if($attr1Con !="")
						$whereCon = ' AND '.$attr1Con;
				
					if($attr2Con !="")
						$whereCon = ' AND '.$attr2Con;
						
					if($attr1Con !="" && $attr2Con !="")
						$whereCon = ' AND ('.$attr1Con.' OR '.$attr2Con.')';
		
					if($whereCon == ""){
						$whereCon = "attribute1 = ''";
					}*/
					$countDrawImage = $db->selQRY('count(*) AS imageCount', 'drawing_register_module_one', 'project_id = '.$value.' '.$whereCon.' AND is_approved = 1 AND is_deleted = 0 AND last_modified_date >= "'.$lastModifiedDate.'" ORDER BY id');				
					$imageCount += $countDrawImage['imageCount'];
				}
				
				if($imageCount > 0){
					$output = array(
						'status' => true,
						'message' => 'Total PDFs are',
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
				$drawingRegID = array();
				foreach($projectId as $key=>$value){
					$whereCon = "";
					$attr1Con = '';
/*					if($attributePemissionArr[$value][0] != "")
						$attr1Con = 'attribute1 IN ('.$attributePemissionArr[$value][0].') ';
					$attr2Con = '';
					if($attributePemissionArr[$value][1] != "")
						$attr2Con = 'attribute1 IN ('.$attributePemissionArr[$value][1].') ';
						
					if($attr1Con !="")
						$whereCon = ' AND '.$attr1Con;
				
					if($attr2Con !="")
						$whereCon = ' AND '.$attr2Con;
						
					if($attr1Con !="" && $attr2Con !="")
						$whereCon = ' AND ('.$attr1Con.' OR '.$attr2Con.')';
		
					if($whereCon == ""){
						$whereCon = "attribute1 = ''";
					}*/
			
					$countDrawImage = $db->selQRYMultiple('GROUP_CONCAT(id) as drawingids', 'drawing_register_module_one', 'project_id = '.$value.' '.$whereCon.' AND is_approved = 1 AND is_deleted = 0 AND last_modified_date >= "'.$lastModifiedDate.'" ORDER BY id');
					$drawingids = 0;
					if($countDrawImage[0]['drawingids'] != ''){
						$drawingids = $countDrawImage[0]['drawingids'];
					}
					$drawingRegID[] = $drawingids;	
				}
				$imageName = $db->selQRYMultiple('project_id, id, pdf_name, last_modified_date', 'drawing_register_module_one', 'project_id IN ('.$projectIDs.') AND id IN ('.join(",", $drawingRegID).') AND is_approved = 1 AND is_deleted = 0 AND last_modified_date >= "'.$lastModifiedDate.'" ORDER BY last_modified_date, id ASC LIMIT '.$startIndex.', '. ($imageLimit+1));

				$last_modified_graphic_date = "";
				$count = 0;
				$folder = opendir($imgSource);
				
				foreach($imageName as $imgName){
					$count ++;
					if ($count > $imageLimit){
						break;
					}
					$imgSource = IMAGESOURCEPATH.'/'.$imgName['project_id'];
					if(!is_dir(EXPORTFILEPATH.'/'.$globalId.'/project_drawing_register/'.$imgName['project_id'])){
						@mkdir(EXPORTFILEPATH.'/'.$globalId.'/project_drawing_register/'.$imgName['project_id'], 0777);
					}
					$imgNam = explode('.', $imgName['pdf_name']);
					if(file_exists($imgSource.'/'.$imgName['pdf_name'])){
						copy($imgSource.'/'.$imgName['pdf_name'], EXPORTFILEPATH.'/'.$globalId.'/project_drawing_register/'.$imgName['project_id'].'/'.$imgName['pdf_name']);

						#copy($imgSource.'/'.$imgNam[0].'.png', EXPORTFILEPATH.'/'.$globalId.'/project_drawing_register/'.$imgName['project_id'].'/'.$imgNam[0].'.png');
					}
					$last_modified_graphic_date = $imgName['last_modified_date'];
				}
				if ($count <= $imageLimit){
					$last_modified_graphic_date = $date;
				}
			}	
		}
		
		$zipSource = EXPORTFILEPATH.'/'.$globalId.'/project_drawing_register/';
		if($last_modified_graphic_date == ''){
			$last_modified_graphic_date = $date;
		}
		$db->createFile('last_modified_date.txt', $last_modified_graphic_date, $zipSource, 'w');//Write File Here
		
		if($db->emptyDirectory($zipSource)){
			
			$zipFileName = $db->updateExportTable_updated(EXPORTFILEPATH.'/'.$globalId.'/project_drawing_register/', $_REQUEST['userId'], $deviceType, 'DrawingRegister', $sync_url);
			
			$db->compress($zipSource, 'drawing_register_'.$zipFileName, EXPORTFILEPATH.'/'.$globalId);
			
			#copy(EXPORTFILEPATH.'/'.$globalId.'/'.$zipName.'.zip', EXPORTFILEPATH.'/'.$globalId.'/drawingmgmt_'.$zipFileName.'.zip');
#			$filename = $zipName.'.zip';
			$filename = 'drawing_register_'.$zipFileName.'.zip';
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