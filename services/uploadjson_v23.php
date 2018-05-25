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

if(isset($_REQUEST['upload_zip'])){
	$db->recursive_remove_directory(IMPORTFILEPATH.'/json');
	if (! is_dir (IMPORTFILEPATH))
		@mkdir(IMPORTFILEPATH, 0777);
		
	@mkdir(IMPORTFILEPATH.'/json/', 0777);
	$extension = array('application/octet-stream');
	if($_FILES["uploadZip"]["error"] > 0){
		$output = array(
			'status' => false,
			'message' => 'Zip Download Failed',
			'data' => ''
		);
		echo '['.json_encode($output).']';
		die;
	}else{
		$zipName = $db->updateImportTable_updated(IMPORTFILEPATH, $_REQUEST['userId'], $deviceType, 'tableData', $sync_url);
		if(move_uploaded_file($_FILES["uploadZip"]["tmp_name"], IMPORTFILEPATH .'/'. $zipName.'.zip')){
			if($db->extractZip(IMPORTFILEPATH .'/'. $zipName.'.zip', IMPORTFILEPATH)){
				$folder = opendir(IMPORTFILEPATH.'/json/');
				$file_types = array("json");
				$insertArray = array();
				while($file = readdir($folder)){
					if(in_array(substr(strtolower($file), strrpos($file,".") + 1), $file_types)){
						if($file == 'inspection_graphics.json'){
							$jsonFileGraphic = IMPORTFILEPATH.'/json/'.$file;
							$stringGraphic = file_get_contents($jsonFileGraphic);
							$jsonDataGraphic = json_decode($stringGraphic, true);
							$updateCount = 0;
							foreach($jsonDataGraphic as $jDataGraphic){
								$rowArray = array();
								if($jDataGraphic['global_id'] == 0){//Add Query Here
									$tbNameGraphic = explode('.', $file);
									$tableNameGraphic = $tbNameGraphic[0];
//Condition to stop duplicate records Dated : 22-03-2013	
									$checkData = array();
									$checkData = $db->selQRYMultiple('graphic_id', 'inspection_graphics', 'project_id = "'.$jDataGraphic['project_id'].'" AND inspection_id = "'.$jDataGraphic['inspection_id'].'" AND graphic_name= "'.$jDataGraphic['graphic_name'].'" AND is_deleted=0');
									if(empty($checkData)){
										$query = "INSERT INTO ".$tableNameGraphic." SET `graphic_id` = '',
													`inspection_id` = '".$jDataGraphic['inspection_id']."',
													`graphic_type` = '".$jDataGraphic['graphic_type']."',
													`graphic_title` = '".$jDataGraphic['graphic_title']."',
													`graphic_name` = '".$jDataGraphic['graphic_name']."',
													`last_modified_date` = NOW(),
													`original_modified_date` = '".$jDataGraphic['original_modified_date']."',
													`last_modified_by` = '".$jDataGraphic['last_modified_by']."',
													`created_date` = NOW(),
													`created_by` = '".$jDataGraphic['created_by']."',
													`resource_type` = '".$jDataGraphic['resource_type']."',
													`project_id` = '".$jDataGraphic['project_id']."',
													`is_deleted` = '".$jDataGraphic['is_deleted']."';";
										if(mysql_query($query)){
											$arr['id'] = $jDataGraphic['graphic_id'];
											$arr['global_id'] = mysql_insert_id() ;
											$data[$tableNameGraphic][] = $arr; 
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}else{
											$arr['id'] = $jDataGraphic['graphic_id'];
											$arr['global_id'] = $checkData[0]["graphic_id"];
											$data[$tableNameGraphic][] = $arr; 
									}
//Condition to stop duplicate records Dated : 22-03-2013	
								}else{//Update Query Here
									$orignalDate = $db->getRecordByKey('inspection_graphics', 'graphic_id', $jDataGraphic['global_id'], 'original_modified_date');
									if($orignalDate[0]['original_modified_date'] < $jDataGraphic['original_modified_date']){
										$tbNameGraphic = explode('.', $file);
										$tableNameGraphic = $tbNameGraphic[0];
											$updateQuery = "UPDATE ".$tableNameGraphic." SET
														`inspection_id` = '".$jDataGraphic['inspection_id']."',
														`graphic_type` = '".$jDataGraphic['graphic_type']."',
														`graphic_title` = '".$jDataGraphic['graphic_title']."',
														`graphic_name` = '".$jDataGraphic['graphic_name']."',
														`last_modified_date` = NOW(),
														`original_modified_date` = '".$jDataGraphic['original_modified_date']."',
														`last_modified_by` = '".$jDataGraphic['last_modified_by']."',
														`created_by` = '".$jDataGraphic['created_by']."',
														`resource_type` = '".$jDataGraphic['resource_type']."',
														`project_id` = '".$jDataGraphic['project_id']."',
														`is_deleted` = '".$jDataGraphic['is_deleted']."'
												WHERE `graphic_id` = '".$jDataGraphic['global_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'issued_to_for_inspections.json'){
							$jsonFileInspection = IMPORTFILEPATH.'/json/'.$file;
							$stringInspection = file_get_contents($jsonFileInspection);
							$jsonDataInspection = json_decode($stringInspection, true);
							$updateCount = 0;
							foreach($jsonDataInspection as $jDataInspection){
								$rowArray = array();
								if($jDataInspection['global_id'] == 0){//Add Query Here
									if($jDataInspection['cost_impact_type'] == 'None'){
										$costImpactPrice = 0.00;
									}elseif($jDataInspection['cost_impact_type'] == 'Low'){
										$costImpactPrice = 100.00;
									}elseif($jDataInspection['cost_impact_type'] == 'Medium'){
										$costImpactPrice = 1000.00;
									}elseif($jDataInspection['cost_impact_type'] == 'High'){
										$costImpactPrice = 10000.00;
									}
								
									$tbNameInspection = explode('.', $file);
									$tableNameInspection = $tbNameInspection[0];
//Condition to stop duplicate records Dated : 22-03-2013	
									$checkData = array();
									$checkData = $db->selQRYMultiple('issued_to_inspections_id', 'issued_to_for_inspections', 'project_id = "'.$jDataInspection['project_id'].'" AND inspection_id = "'.$jDataInspection['inspection_id'].'" AND issued_to_name = "'.$jDataInspection['issued_to_name'].'" AND is_deleted=0');
									if(empty($checkData)){
										$query = "INSERT INTO ".$tableNameInspection." SET `issued_to_inspections_id` ='',
											`inspection_id` = '".$jDataInspection['inspection_id']."',
											`issued_to_name` = \"".addslashes($jDataInspection['issued_to_name'])."\",
											`inspection_fixed_by_date` = '".$jDataInspection['inspection_fixed_by_date']."',
											`cost_attribute` = '".$jDataInspection['cost_attribute']."',
											`inspection_status` = '".$jDataInspection['inspection_status']."',
											`closed_date` = '".$jDataInspection['closed_date']."',
											`last_modified_date` = NOW(),
											`original_modified_date` = '".$jDataInspection['original_modified_date']."',
											`last_modified_by` = '".$jDataInspection['last_modified_by']."',
											`created_date` = NOW(),
											`cost_impact_type` = '".$jDataInspection['cost_impact_type']."',
											`cost_impact_price` = '".$costImpactPrice."',
											`created_by` = '".$jDataInspection['created_by']."',
											`resource_type` = '".$jDataInspection['resource_type']."',
											`time_impact` = '".$jDataInspection['time_impact']."',
											`project_id` = '".$jDataInspection['project_id']."',
											`is_deleted` = '".$jDataInspection['is_deleted']."';";
										if(mysql_query($query)){
											$arr['id'] = $jDataInspection['id'];
											$arr['global_id'] = mysql_insert_id() ;
											$data[$tableNameInspection][] = $arr; 
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}else{
										$arr['id'] = $jDataInspection['id'];
										$arr['global_id'] = $checkData[0]["issued_to_inspections_id"] ;
										$data[$tableNameInspection][] = $arr; 
									}
//Condition to stop duplicate records Dated : 22-03-2013	
								}else{//Update Query Here
									$orignalDate = $db->getRecordByKey('issued_to_for_inspections', 'issued_to_inspections_id', $jDataInspection['global_id'], 'original_modified_date');
									if($orignalDate[0]['original_modified_date'] < $jDataInspection['original_modified_date']){
										$tbNameInspection = explode('.', $file);
										$tableNameInspection = $tbNameInspection[0];
										$updateQuery = "UPDATE ".$tableNameInspection." SET
											`inspection_id` = '".$jDataInspection['inspection_id']."',
											`issued_to_name` = \"".addslashes($jDataInspection['issued_to_name'])."\",
											`inspection_fixed_by_date` = '".$jDataInspection['inspection_fixed_by_date']."',
											`cost_attribute` = '".$jDataInspection['cost_attribute']."',
											`inspection_status` = '".$jDataInspection['inspection_status']."',
											`closed_date` = '".$jDataInspection['closed_date']."',
											`last_modified_date` = NOW(),
											`cost_impact_type` = '".$jDataInspection['cost_impact_type']."',
											`original_modified_date` = '".$jDataInspection['original_modified_date']."',
											`last_modified_by` = '".$jDataInspection['last_modified_by']."',
											`resource_type` = '".$jDataInspection['resource_type']."',
											`time_impact` = '".$jDataInspection['time_impact']."',
											`project_id` = '".$jDataInspection['project_id']."',
											`is_deleted` = '".$jDataInspection['is_deleted']."'
										WHERE `issued_to_inspections_id` = '".$jDataInspection['global_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'project_inspections.json'){
							$jsonFilePinspections = IMPORTFILEPATH.'/json/'.$file;
							$stringPinspections = file_get_contents($jsonFilePinspections);
							$jsonDataPinspections = json_decode($stringPinspections, true);
							$updateCount = 0;
							foreach($jsonDataPinspections as $jDataPinspections){
								$rowArray = array();
								if ($jDataPinspections["inspection_date_raised"] != ""){
									$tmp = explode ("/", $jDataPinspections["inspection_date_raised"]);
									$jDataPinspections["inspection_date_raised"] = $tmp[2] . "-" . $tmp[0] . "-" . $tmp[1];
								}
								if ($jDataPinspections["inspection_fixed_by_date"] != ""){
									$tmp = explode ("/", $jDataPinspections["inspection_fixed_by_date"]);
									$jDataPinspections["inspection_fixed_by_date"] = $tmp[2] . "-" . $tmp[0] . "-" . $tmp[1];
								}
								if($jDataPinspections['global_id'] == 0){//Add Query Here
									$tbNamePinspections = explode('.', $file);
									$tableNamePinspections = $tbNamePinspections[0];
//Condition to stop duplicate records Dated : 22-03-2013	
									$checkData = array();
									$checkData = $db->selQRYMultiple('inspection_id', 'project_inspections', 'project_id = "'.$jDataPinspections['project_id'].'" AND inspection_id = "'.$jDataPinspections['inspection_id'].'" AND is_deleted=0');
									if(empty($checkData)){
										$query = "INSERT INTO ".$tableNamePinspections." SET `inspection_id` = '".$jDataPinspections['inspection_id']."',
												`project_id` = '".$jDataPinspections['project_id']."',
												`location_id` = '".$jDataPinspections['location_id']."',
												`inspection_date_raised` = '".$jDataPinspections['inspection_date_raised']."',
												`inspection_status` = '".$jDataPinspections['inspection_status']."',
												`inspection_inspected_by` = \"".addslashes($jDataPinspections['inspection_inspected_by'])."\",
												`inspection_raised_by` = \"".addslashes($jDataPinspections['inspection_raised_by'])."\",
												`inspection_fixed_by_date` = '".$jDataPinspections['inspection_fixed_by_date']."',
												`inspection_type` = '".$jDataPinspections['inspection_type']."',
												`inspection_map_address` = '".$jDataPinspections['inspection_map_address']."',
												`inspection_description` = \"". addslashes($jDataPinspections['inspection_description'])."\",
												`inspection_priority` = '".$jDataPinspections['inspection_priority']."',
												`inspection_notes` = \"".addslashes($jDataPinspections['inspection_notes'])."\",
												`inspection_sign_image` = '".$jDataPinspections['inspection_sign_image']."',
												`inspection_location` = '".$jDataPinspections['inspection_location']."',
												`cost_attribute` = '".$jDataPinspections['cost_attribute']."',
												`last_modified_date` = NOW(),
												`original_modified_date` = '".$jDataPinspections['original_modified_date']."',
												`check_list_item_id` = '".$jDataPinspections['check_list_item_id']."',
												`check_list_location_id` = '".$jDataPinspections['check_list_location_id']."',
												`last_modified_by` = '".$jDataPinspections['last_modified_by']."',
												`created_date` = NOW(),
												`created_by` = '".$jDataPinspections['created_by']."',
												`resource_type` = '".$jDataPinspections['resource_type']."',
												`is_deleted` = '".$jDataPinspections['is_deleted']."';";
										if(mysql_query($query)){
											$arr['id'] = $jDataPinspections['inspection_id'];
											$arr['global_id'] = $jDataPinspections['inspection_id'] ;
											$data[$tableNamePinspections][] = $arr; 
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}else{
										$arr['id'] = $jDataPinspections['inspection_id'];
										$arr['global_id'] = $jDataPinspections['inspection_id'];
										$data[$tableNamePinspections][] = $arr; 
									}
//Condition to stop duplicate records Dated : 22-03-2013	
								}else{//Update Query Here
									$orignalDate = $db->getRecordByKey('project_inspections', 'inspection_id', $jDataPinspections['global_id'], 'original_modified_date');
									if($orignalDate[0]['original_modified_date'] < $jDataPinspections['original_modified_date']){
										$tbNamePinspections = explode('.', $file);
										$tableNamePinspections = $tbNamePinspections[0];
										/*$updateQuery = "UPDATE ".$tableNamePinspections." SET
												`project_id` = '".$jDataPinspections['project_id']."',
												`location_id` = '".$jDataPinspections['location_id']."',
												`inspection_date_raised` = '".$jDataPinspections['inspection_date_raised']."',
												`inspection_status` = '".$jDataPinspections['inspection_status']."',
												`inspection_inspected_by` = \"".addslashes($jDataPinspections['inspection_inspected_by'])."\",
												`inspection_raised_by` = \"".addslashes($jDataPinspections['inspection_raised_by'])."\",
												`inspection_fixed_by_date` = '".$jDataPinspections['inspection_fixed_by_date']."',
												`inspection_type` = '".$jDataPinspections['inspection_type']."',
												`inspection_map_address` = \"".$jDataPinspections['inspection_map_address']."\",
												`inspection_description` = \"".addslashes($jDataPinspections['inspection_description'])."\",
												`inspection_priority` = '".$jDataPinspections['inspection_priority']."',
												`inspection_notes` = \"".addslashes($jDataPinspections['inspection_notes'])."\",
												`inspection_sign_image` = '".$jDataPinspections['inspection_sign_image']."',
												`inspection_location` = '".$jDataPinspections['inspection_location']."',
												`cost_attribute` = '".$jDataPinspections['cost_attribute']."',
												`last_modified_date` = NOW(),
												`original_modified_date` = '".$jDataPinspections['original_modified_date']."',
												`check_list_item_id` = '".$jDataPinspections['check_list_item_id']."',
												`check_list_location_id` = '".$jDataPinspections['check_list_location_id']."',
												`last_modified_by` = '".$jDataPinspections['last_modified_by']."',
												`resource_type` = '".$jDataPinspections['resource_type']."',
												`is_deleted` = '".$jDataPinspections['is_deleted']."'
												WHERE `inspection_id` = '".$jDataPinspections['global_id']."';";*/
												$updateQuery = "UPDATE ".$tableNamePinspections." SET
												`project_id` = '".$jDataPinspections['project_id']."',
												`location_id` = '".$jDataPinspections['location_id']."',
												`inspection_date_raised` = '".$jDataPinspections['inspection_date_raised']."',
												`inspection_status` = '".$jDataPinspections['inspection_status']."',
												`inspection_raised_by` = \"".addslashes($jDataPinspections['inspection_raised_by'])."\",
												`inspection_fixed_by_date` = '".$jDataPinspections['inspection_fixed_by_date']."',
												`inspection_type` = '".$jDataPinspections['inspection_type']."',
												`inspection_map_address` = \"".$jDataPinspections['inspection_map_address']."\",
												`inspection_description` = \"".addslashes($jDataPinspections['inspection_description'])."\",
												`inspection_priority` = '".$jDataPinspections['inspection_priority']."',
												`inspection_notes` = \"".addslashes($jDataPinspections['inspection_notes'])."\",
												`inspection_sign_image` = '".$jDataPinspections['inspection_sign_image']."',
												`inspection_location` = '".$jDataPinspections['inspection_location']."',
												`cost_attribute` = '".$jDataPinspections['cost_attribute']."',
												`last_modified_date` = NOW(),
												`original_modified_date` = '".$jDataPinspections['original_modified_date']."',
												`check_list_item_id` = '".$jDataPinspections['check_list_item_id']."',
												`check_list_location_id` = '".$jDataPinspections['check_list_location_id']."',
												`last_modified_by` = '".$jDataPinspections['last_modified_by']."',
												`resource_type` = '".$jDataPinspections['resource_type']."',
												`is_deleted` = '".$jDataPinspections['is_deleted']."'
												WHERE `inspection_id` = '".$jDataPinspections['global_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'location_check_list.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								$checkData = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$checkData = $db->selQRYMultiple('location_check_list_id', 'location_check_list', 'project_id = "'.$jDataProjects['project_id'].'" AND location_id = "'.$jDataProjects['location_id'].'" AND check_list_items_id = "'.$jDataProjects['check_list_items_id'].'" AND is_deleted=0');
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									if(empty($checkData)){//Add Query Here
										#$tableNameProjects = 'insepection_check_list';
										$query = "INSERT INTO ".$tableNameProjects." SET `project_id` = '".$jDataProjects['project_id']."',
													`location_id` = '".$jDataProjects['location_id']."',
													`check_list_items_id` = '".$jDataProjects['check_list_items_id']."',
													`check_list_items_status` = '".$jDataProjects['check_list_items_status']."',
													`last_modified_date` = NOW(),
													`last_modified_by` = '".$jDataProjects['last_modified_by']."',
													`created_date` = NOW(),
													`created_by` = '".$jDataProjects['created_by']."',
													`resource_type` = '".$jDataProjects['resource_type']."',
													`original_modified_date` = '".$jDataProjects['original_modified_date']."',
													`is_deleted` = '".$jDataProjects['is_deleted']."';";
										if(mysql_query($query)){
											$arr['id'] = $jDataProjects['rowid'];
											$arr['global_id'] = mysql_insert_id() ;
											$data[$tableNameProjects][] = $arr; 
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}else{
										$arr['id'] = $jDataProjects['rowid'];
										$arr['global_id'] = $checkData[0]['location_check_list_id'];
										$data[$tableNameProjects][] = $arr; 
									}
								}else{//Update Query Here
									$arr['id'] = $jDataProjects['rowid'];
									$arr['global_id'] = $jDataProjects['global_id'];
									$data[$tableNameProjects][] = $arr; 
									$orignalDate = $db->getRecordByKey('location_check_list', 'location_check_list_id', $jDataProjects['global_id'], 'original_modified_date');
									if($orignalDate[0]['original_modified_date'] < $jDataProjects['original_modified_date']){
										$tbNameProjects = explode('.', $file);
										$tableNameProjects = $tbNameProjects[0];
										$updateQuery = "UPDATE ".$tableNameProjects." SET
													`project_id` = '".$jDataProjects['project_id']."',
													`location_id` = '".$jDataProjects['location_id']."',
													`check_list_items_id` = '".$jDataProjects['check_list_items_id']."',
													`check_list_items_status` = '".$jDataProjects['check_list_items_status']."',
													`last_modified_date` = NOW(),
													`last_modified_by` = '".$jDataProjects['last_modified_by']."',
													`original_modified_date` = '".$jDataProjects['original_modified_date']."',
													`resource_type` = '".$jDataProjects['resource_type']."'
												WHERE `location_check_list_id` = '".$jDataProjects['global_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
/*								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									#$tableNameProjects = 'insepection_check_list';
									$query = "INSERT INTO ".$tableNameProjects." SET `project_id` = '".$jDataProjects['project_id']."',
												`location_id` = '".$jDataProjects['location_id']."',
												`check_list_items_id` = '".$jDataProjects['check_list_items_id']."',
												`check_list_items_status` = '".$jDataProjects['check_list_items_status']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`created_date` = NOW(),
												`created_by` = '".$jDataProjects['created_by']."',
												`resource_type` = '".$jDataProjects['resource_type']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataProjects['rowid'];
										$arr['global_id'] = mysql_insert_id() ;
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$updateQuery = "UPDATE ".$tableNameProjects." SET
												`project_id` = '".$jDataProjects['project_id']."',
												`location_id` = '".$jDataProjects['location_id']."',
												`check_list_items_id` = '".$jDataProjects['check_list_items_id']."',
												`check_list_items_status` = '".$jDataProjects['check_list_items_status']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`created_date` = NOW(),
												`created_by` = '".$jDataProjects['created_by']."',
												`resource_type` = '".$jDataProjects['resource_type']."'
											WHERE `location_check_list_id` = '".$jDataProjects['global_id']."';";
									if(mysql_query($updateQuery)){
										$updateCount++;
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}*/
							}
							$insertArray[] = $rowArray;
						}
//Location Checklist Data
						if($file == 'progress_monitoring_update.json'){
							$jsonFileMonitoring = IMPORTFILEPATH.'/json/'.$file;
							$stringMonitoring = file_get_contents($jsonFileMonitoring);
							$jsonDataMonitoring = json_decode($stringMonitoring, true);
							$updateCount = 0;
							foreach($jsonDataMonitoring as $jDataMonitoring){
								$rowArray = array();
								if($jDataMonitoring['global_id'] == 0){//Add Query Here
									$tbNameMonitoring = explode('.', $file);
									$tableNameMonitoring = $tbNameMonitoring[0];
									$query = "INSERT INTO ".$tableNameMonitoring." SET `update_id` = '',
												`progress_id` = '".$jDataMonitoring['progress_id']."',
												`percentage` = '".$jDataMonitoring['percentage']."',
												`status` = '".$jDataMonitoring['status']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataMonitoring['last_modified_by']."',
												`created_date` = NOW(),
												`created_by` = '".$jDataMonitoring['created_by']."',
												`resource_type` = '".$jDataMonitoring['resource_type']."',
												`project_id` = '".$jDataMonitoring['project_id']."',
												`is_deleted` = '".$jDataMonitoring['is_deleted']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataMonitoring['id'];
										$arr['global_id'] = mysql_insert_id() ;
										$data[$tableNameMonitoring][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameMonitoring = explode('.', $file);
									$tableNameMonitoring = $tbNameMonitoring[0];
									$updateQuery = "UPDATE ".$tableNameMonitoring." SET
												`progress_id` = '".$jDataMonitoring['progress_id']."',
												`percentage` = '".$jDataMonitoring['percentage']."',
												`status` = '".$jDataMonitoring['status']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataMonitoring['last_modified_by']."',
												`resource_type` = '".$jDataMonitoring['resource_type']."',
												`project_id` = '".$jDataMonitoring['project_id']."',
												`is_deleted` = '".$jDataMonitoring['is_deleted']."'
											WHERE `update_id` = '".$jDataMonitoring['global_id']."';";
									if(mysql_query($updateQuery)){
										$updateCount++;
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'progress_monitoring.json'){
							$jsonFileMonitoring = IMPORTFILEPATH.'/json/'.$file;
							$stringMonitoring = file_get_contents($jsonFileMonitoring);
							$jsonDataMonitoring = json_decode($stringMonitoring, true);
							$updateCount = 0;
							foreach($jsonDataMonitoring as $jDataMonitoring){
								$rowArray = array();
								if($jDataMonitoring['global_id'] == 0){//Add Query Here
								}else{//Update Query Here
									$orignalDate = $db->getRecordByKey('progress_monitoring', 'progress_id', $jDataMonitoring['global_id'], 'original_modified_date');
									if($orignalDate[0]['original_modified_date'] < $jDataMonitoring['original_modified_date']){
										$tbNameMonitoring = explode('.', $file);
										$tableNameMonitoring = $tbNameMonitoring[0];
										$updateQuery = "UPDATE ".$tableNameMonitoring." SET
													`project_id` = '".$jDataMonitoring['project_id']."',
													`last_modified_date` = NOW(),
													`last_modified_by` = '".$jDataMonitoring['last_modified_by']."',
													`resource_type` = '".$jDataMonitoring['resource_type']."',
													`percentage` = '".$jDataMonitoring['percentage']."',
													`status` = '".$jDataMonitoring['status']."',
													`original_modified_date` = '".$jDataMonitoring['original_modified_date']."'
												WHERE `progress_id` = ".$jDataMonitoring['global_id'].";";
										//Kuldip: No requirement to updated task		
										//`task` = '".addslashes($jDataMonitoring['task'])."',
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'project_locations.json'){
							$jsonFilePlocations = IMPORTFILEPATH.'/json/'.$file;
							$stringPlocations = file_get_contents($jsonFilePlocations);
							$jsonDataPlocations = json_decode($stringPlocations, true);
							$updateCount = 0;
							foreach($jsonDataPlocations as $jDataPlocations){
								$rowArray = array();
								if($jDataPlocations['global_id'] == 0){//Add Query Here
									$tbNamePlocations = explode('.', $file);
									$tableNamePlocations = $tbNamePlocations[0];
									$query = "INSERT INTO ".$tableNamePlocations." SET
												`location_id` = '".$jDataPlocations['location_id']."',
												`project_id` = '".$jDataPlocations['project_id']."',
												`location_parent_id` = '".$jDataPlocations['location_parent_id']."',
												`location_title` = '".$jDataPlocations['location_title']."',
												`location_id_tree` = '".$jDataPlocations['location_id_tree']."',
												`location_name_tree` = '".$jDataPlocations['location_name_tree']."',
												`qrcode` = '".$jDataPlocations['qrcode']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataPlocations['last_modified_by']."',
												`created_date` = NOW(),
												`created_by` = '".$jDataPlocations['created_by']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataPlocations['location_id'];
										$arr['global_id'] = $jDataPlocations['location_id'];
										$data[$tableNamePlocations][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNamePlocations = explode('.', $file);
									$tableNamePlocations = $tbNamePlocations[0];
									$updateQuery = "UPDATE ".$tableNamePlocations." SET
												`location_parent_id` = '".$jDataPlocations['location_parent_id']."',
												`location_title` = \"".$jDataPlocations['location_title']."\",
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataPlocations['last_modified_by']."',
												`resource_type` = '".$jDataPlocations['resource_type']."',
												`qrcode` = '".$jDataPlocations['qrcode']."',
												`project_id` = '".$jDataPlocations['project_id']."'
											WHERE `location_id` = '".$jDataPlocations['global_id']."';";
									if(mysql_query($updateQuery)){
										$updateCount++;
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}		
								}
							}
							$insertArray[] = $rowArray;
						}
//progress monitoring locations
						if($file == 'project_monitoring_locations.json'){
							$jsonFilePlocations = IMPORTFILEPATH.'/json/'.$file;
							$stringPlocations = file_get_contents($jsonFilePlocations);
							$jsonDataPlocations = json_decode($stringPlocations, true);
							$updateCount = 0;
							foreach($jsonDataPlocations as $jDataPlocations){
								$rowArray = array();
								if($jDataPlocations['global_id'] == 0){//Add Query Here
								}else{//Update Query Here
									$tbNamePlocations = explode('.', $file);
									$tableNamePlocations = $tbNamePlocations[0];
									$updateQuery = "UPDATE ".$tableNamePlocations." SET
												`location_parent_id` = '".$jDataPlocations['location_parent_id']."',
												`location_title` = \"".$jDataPlocations['location_title']."\",
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataPlocations['last_modified_by']."',
												`resource_type` = '".$jDataPlocations['resource_type']."',
												`project_id` = '".$jDataPlocations['project_id']."',
												`qrcode` = '".$jDataPlocations['qrcode']."',
												`is_deleted` = '".$jDataPlocations['is_deleted']."'
											WHERE `location_id` = '".$jDataPlocations['global_id']."';";
									//mysql_query($updateQuery);
									$updateCount++;
								}
							}
							//$arr['updatecount'] = $updateCount;
							//$data[$tableNamePlocations][] = $arr;
						}
						if($file == 'user_projects.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$updateQuery = "UPDATE ".$tableNameProjects." SET
												`user_id` = '".$jDataProjects['user_id']."',
												`pro_code` = '',
												`project_name` = \"".$jDataProjects['project_name']."\",
												`project_type` = '".$jDataProjects['project_type']."',
												`project_address_line1` = \"".$jDataProjects['project_address_line1']."\",
												`project_address_line2` = \"".$jDataProjects['project_address_line2']."\",
												`project_suburb` = '".$jDataProjects['project_suburb']."',
												`project_state` = '".$jDataProjects['project_state']."',
												`project_postcode` = '".$jDataProjects['project_postcode']."',
												`project_country` = '".$jDataProjects['project_country']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`resource_type` = '".$jDataProjects['resource_type']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."'
											WHERE `project_id` = '".$jDataProjects['global_id']."';";
									//mysql_query($updateQuery);
									$updateCount++;
								}
							}
							//$arr['updatecount'] = $updateCount;
							//$data[$tableNameProjects][] = $arr;
						}
						if($file == 'inspection_check_list.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									#$tableNameProjects = 'insepection_check_list';
									$query = "INSERT INTO ".$tableNameProjects." SET `project_id` = '".$jDataProjects['project_id']."',
												`inspection_id` = '".$jDataProjects['inspection_id']."',
												`check_list_items_id` = '".$jDataProjects['check_list_items_id']."',
												`check_list_items_status` = '".$jDataProjects['check_list_items_status']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`created_date` = NOW(),
												`created_by` = '".$jDataProjects['created_by']."',
												`resource_type` = '".$jDataProjects['resource_type']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataProjects['rowid'];
										$arr['global_id'] = mysql_insert_id() ;
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$updateQuery = "UPDATE ".$tableNameProjects." SET
												`project_id` = '".$jDataProjects['project_id']."',
												`inspection_id` = '".$jDataProjects['inspection_id']."',
												`check_list_items_id` = '".$jDataProjects['check_list_items_id']."',
												`check_list_items_status` = '".$jDataProjects['check_list_items_status']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`resource_type` = '".$jDataProjects['resource_type']."'
											WHERE `insepection_check_list_id` = '".$jDataProjects['global_id']."';";
									if(mysql_query($updateQuery)){
										$updateCount++;
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}
							}
							$insertArray[] = $rowArray;
						}
//ProgressMonitoring Checklist Data
						if($file == 'progress_monitoring_check_list.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									#$tableNameProjects = 'insepection_check_list';
									$query = "INSERT INTO ".$tableNameProjects." SET `project_id` = '".$jDataProjects['project_id']."',
												`progress_id` = '".$jDataProjects['progress_id']."',
												`check_list_items_id` = '".$jDataProjects['check_list_items_id']."',
												`check_list_items_status` = '".$jDataProjects['check_list_items_status']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`created_date` = NOW(),
												`created_by` = '".$jDataProjects['created_by']."',
												`resource_type` = '".$jDataProjects['resource_type']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataProjects['rowid'];
										$arr['global_id'] = mysql_insert_id() ;
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$updateQuery = "UPDATE ".$tableNameProjects." SET
												`project_id` = '".$jDataProjects['project_id']."',
												`progress_id` = '".$jDataProjects['progress_id']."',
												`check_list_items_id` = '".$jDataProjects['check_list_items_id']."',
												`check_list_items_status` = '".$jDataProjects['check_list_items_status']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`resource_type` = '".$jDataProjects['resource_type']."'
											WHERE `progress_monitoring_check_list_id` = '".$jDataProjects['global_id']."';";
									if(mysql_query($updateQuery)){
										$updateCount++;
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'qa_task_monitoring.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];

									$checkData = array();
									$checkData = $db->selQRYMultiple('task_id', 'qa_task_monitoring', 'project_id = "'.$jDataProjects['project_id'].'" AND task_id = "'.$jDataProjects['task_id'].'" AND is_deleted = 0');
									if(empty($checkData)){
									#$tableNameProjects = 'insepection_check_list';
										$query = "INSERT INTO ".$tableNameProjects." SET
													`task_id` = '".$jDataProjects['task_id']."',
													`project_id` = '".$jDataProjects['project_id']."',
													`location_id` = '".$jDataProjects['location_id']."',
													`sub_location_id` = '".$jDataProjects['sub_location_id']."',
													`task` = '".$jDataProjects['task']."',
													`status` = '".$jDataProjects['status']."',
													`comments` = \"".$jDataProjects['comments']."\",
													`resource_type` = '".$jDataProjects['resource_type']."',
													`created_by` = '".$jDataProjects['created_by']."',
													`created_date` = NOW(),
													`last_modified_by` = '".$jDataProjects['last_modified_by']."',
													`last_modified_date` = NOW(),
													`qrcode` = '".$jDataProjects['qrcode']."',
													`location_tree` = '".$jDataProjects['location_tree']."';";
										if(mysql_query($query)){
											$arr['id'] = $jDataProjects['task_id'];
											$arr['global_id'] = $jDataProjects['task_id'];
											$data[$tableNameProjects][] = $arr; 
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}else{
										$arr['id'] = $jDataProjects['task_id'];
										$arr['global_id'] = $jDataProjects['task_id'];
										$data[$tableNameProjects][] = $arr; 
									}
								}else{
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$updateQuery = "UPDATE ".$tableNameProjects." SET
												`status` = '".$jDataProjects['status']."',
												`comments` = \"".$jDataProjects['comments']."\",
												`signoff_image` = '".$jDataProjects['signoff_image']."',
												`resource_type` = '".$jDataProjects['resource_type']."',
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`qrcode` = '".$jDataProjects['qrcode']."',
												`last_modified_date` = NOW()
											WHERE `task_id` = '".$jDataProjects['global_id']."';";
									if(mysql_query($updateQuery)){
										$updateCount++;
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}
							}
							//$arr['updatecount'] = $updateCount;
							//$data[$tableNameProjects][] = $arr;
						}
						if($file == 'qa_task_locations.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];

									$checkData = array();
									$checkData = $db->selQRYMultiple('location_id', 'qa_task_locations', 'project_id = "'.$jDataProjects['project_id'].'" AND location_id = "'.$jDataProjects['location_id'].'" AND is_deleted = 0');
									if(empty($checkData)){
										$query = "INSERT INTO ".$tableNameProjects." SET
													`location_id` = '".$jDataProjects['location_id']."',
													`project_id` = '".$jDataProjects['project_id']."',
													`location_parent_id` = '".$jDataProjects['location_parent_id']."',
													`location_title` = '".$jDataProjects['location_title']."',
													`resource_type` = '".$jDataProjects['resource_type']."',
													`created_by` = '".$jDataProjects['created_by']."',
													`created_date` = NOW(),
													`last_modified_by` = '".$jDataProjects['last_modified_by']."',
													`last_modified_date` = NOW(),
													`is_adhoc_location` = '".$jDataProjects['is_adhoc_location']."',
													`location_tree_name` = '".$jDataProjects['location_tree_name']."',
													`qrcode` = '".$jDataProjects['qrcode']."';";
										if(mysql_query($query)){
											$arr['id'] = $jDataProjects['location_id'];
											$arr['global_id'] = $jDataProjects['location_id'];
											$data[$tableNameProjects][] = $arr; 
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}else{
										$arr['id'] = $jDataProjects['location_id'];
										$arr['global_id'] = $jDataProjects['location_id'];
										$data[$tableNameProjects][] = $arr; 
									}
								}else{
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$updateQuery = "UPDATE ".$tableNameProjects." SET
												`qrcode` = '".$jDataProjects['qrcode']."',
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW()
											WHERE `location_id` = '".$jDataProjects['global_id']."';";
									if(mysql_query($updateQuery)){
										$updateCount++;
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}
							}
							//$arr['updatecount'] = $updateCount;
							//$data[$tableNameProjects][] = $arr;
						}
						if($file == 'qa_task_monitoring_update.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									#$tableNameProjects = 'insepection_check_list';
									$query = "INSERT INTO ".$tableNameProjects." SET
												`task_id` = '".$jDataProjects['task_id']."',
												`status` = '".$jDataProjects['status']."',
												`comments` = \"".$jDataProjects['comments']."\",
												`created_by` = '".$jDataProjects['created_by']."',
												`created_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`last_modified_date` = NOW(),
												`is_deleted` = '".$jDataProjects['is_deleted']."',
												`resource_type` = '".$jDataProjects['resource_type']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataProjects['update_id'];
										$arr['global_id'] = mysql_insert_id() ;
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$updateQuery = "UPDATE ".$tableNameProjects." SET
												`task_id` = '".$jDataProjects['task_id']."',
												`status` = '".$jDataProjects['status']."',
												`comments` = \"".$jDataProjects['comments']."\",
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW(),
												`project_id` = '".$jDataProjects['project_id']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."',
												`resource_type` = '".$jDataProjects['resource_type']."'
											WHERE `update_id` = '".$jDataProjects['global_id']."';";
									if(mysql_query($updateQuery)){
										$updateCount++;
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'qa_task_signoff.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									#$tableNameProjects = 'insepection_check_list';
									$query = "INSERT INTO ".$tableNameProjects." SET
												`project_id` = '".$jDataProjects['project_id']."',
												`task_id` = '".$jDataProjects['task_id']."',
												`image_name` = '".$jDataProjects['image_name']."',
												`created_by` = '".$jDataProjects['created_by']."',
												`created_date` = NOW(),
												`resource_type` = '".$jDataProjects['resource_type']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."',
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW(),
												`created_by` = '".$jDataProjects['created_by']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataProjects['id'];
										$arr['global_id'] = mysql_insert_id() ;
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$updateQuery = "UPDATE ".$tableNameProjects." SET
												`project_id` = '".$jDataProjects['project_id']."',
												`task_id` = '".$jDataProjects['task_id']."',
												`image_name` = '".$jDataProjects['image_name']."',
												`resource_type` = '".$jDataProjects['resource_type']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."',
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW(),
												`created_by` = '".$jDataProjects['created_by']."'
											WHERE `insepection_check_list_id` = '".$jDataProjects['global_id']."';";
									if(mysql_query($updateQuery)){
										$updateCount++;
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'qa_graphics.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									#$tableNameProjects = 'insepection_check_list';
									$query = "INSERT INTO ".$tableNameProjects." SET
												`non_conformance_id` = '".$jDataProjects['non_conformance_id']."',
												`task_id` = '".$jDataProjects['task_id']."',
												`qa_graphic_type` = '".$jDataProjects['qa_graphic_type']."',
												`qa_graphic_name` = '".$jDataProjects['qa_graphic_name']."',
												`created_by` = '".$jDataProjects['created_by']."',
												`created_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW(),
												`resource_type` = '".$jDataProjects['resource_type']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataProjects['qa_graphic_id'];
										$arr['global_id'] = mysql_insert_id();
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$orignalDate = $db->getRecordByKey($tableNameProjects, 'qa_graphic_id', $jDataProjects['global_id'], 'original_modified_date');
									if($orignalDate[0]['original_modified_date'] < $jDataProjects['original_modified_date']){
										$updateQuery = "UPDATE ".$tableNameProjects." SET
													`non_conformance_id` = '".$jDataProjects['non_conformance_id']."',
													`task_id` = '".$jDataProjects['task_id']."',
													`qa_graphic_type` = '".$jDataProjects['qa_graphic_type']."',
													`qa_graphic_name` = '".$jDataProjects['qa_graphic_name']."',
													`last_modified_by` = '".$jDataProjects['last_modified_by']."',
													`original_modified_date` = '".$jDataProjects['original_modified_date']."',
													`last_modified_date` = NOW(),
													`resource_type` = '".$jDataProjects['resource_type']."',
													`project_id` = '".$jDataProjects['project_id']."',
													`is_deleted` = '".$jDataProjects['is_deleted']."'
												WHERE `qa_graphic_id` = '".$jDataProjects['global_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'qa_issued_to_inspections.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									#$tableNameProjects = 'insepection_check_list';
									$query = "INSERT INTO ".$tableNameProjects." SET
												`non_conformance_id` = '".$jDataProjects['non_conformance_id']."',
												`task_id` = '".$jDataProjects['task_id']."',
												`qa_issued_to_name` = \"".$jDataProjects['qa_issued_to_name']."\",
												`qa_inspection_fixed_by_date` = '".$jDataProjects['qa_inspection_fixed_by_date']."',
												`qa_cost_attribute` = '".$jDataProjects['qa_cost_attribute']."',
												`qa_inspection_status` = '".$jDataProjects['qa_inspection_status']."',
												`created_by` = '".$jDataProjects['created_by']."',
												`created_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW(),
												`resource_type` = '".$jDataProjects['resource_type']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataProjects['qa_issued_to_id'];
										$arr['global_id'] = mysql_insert_id() ;
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$orignalDate = $db->getRecordByKey($tableNameProjects, 'qa_issued_to_id', $jDataProjects['global_id'], 'original_modified_date');
									if($orignalDate[0]['original_modified_date'] < $jDataProjects['original_modified_date']){
										$updateQuery = "UPDATE ".$tableNameProjects." SET
													`non_conformance_id` = '".$jDataProjects['non_conformance_id']."',
													`task_id` = '".$jDataProjects['task_id']."',
													`qa_issued_to_name` = \"".$jDataProjects['qa_issued_to_name']."\",
													`qa_inspection_fixed_by_date` = '".$jDataProjects['qa_inspection_fixed_by_date']."',
													`qa_cost_attribute` = '".$jDataProjects['qa_cost_attribute']."',
													`qa_inspection_status` = '".$jDataProjects['qa_inspection_status']."',
													`last_modified_by` = '".$jDataProjects['last_modified_by']."',
													`original_modified_date` = '".$jDataProjects['original_modified_date']."',
													`last_modified_date` = NOW(),
													`resource_type` = '".$jDataProjects['resource_type']."',
													`project_id` = '".$jDataProjects['project_id']."',
													`is_deleted` = '".$jDataProjects['is_deleted']."'
												WHERE `qa_issued_to_id` = '".$jDataProjects['global_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'qa_inspections.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								/*if ($jDataProjects["qa_inspection_date_raised"] != ""){
									$tmp = explode ("/", $jDataProjects["qa_inspection_date_raised"]);
									$jDataProjects["qa_inspection_date_raised"] = $tmp[2] . "-" . $tmp[0] . "-" . $tmp[1];
								}*/
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									#$tableNameProjects = 'insepection_check_list';
									$query = "INSERT INTO ".$tableNameProjects." SET
												`non_conformance_id` = '".$jDataProjects['non_conformance_id']."',
												`task_id` = '".$jDataProjects['task_id']."',
												`location_id` = '".$jDataProjects['location_id']."',
												`qa_inspection_date_raised` = '".$jDataProjects['qa_inspection_date_raised']."',
												`qa_inspection_raised_by` = '".$jDataProjects['qa_inspection_raised_by']."',
												`qa_inspection_inspected_by` = '".$jDataProjects['qa_inspection_inspected_by']."',
												`qa_inspection_description` = \"".$jDataProjects['qa_inspection_description']."\",
												`qa_inspection_location` = '".$jDataProjects['qa_inspection_location']."',
												`created_by` = '".$jDataProjects['created_by']."',
												`created_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW(),
												`resource_type` = '".$jDataProjects['resource_type']."',
												`original_modified_date` = '".$jDataProjects['original_modified_date']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`ncr` = '".$jDataProjects['ncr']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataProjects['non_conformance_id'];
										$arr['global_id'] = $jDataProjects['non_conformance_id'];
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$orignalDate = $db->getRecordByKey($tableNameProjects, 'non_conformance_id', $jDataProjects['global_id'], 'original_modified_date');
									if($orignalDate[0]['original_modified_date'] < $jDataProjects['original_modified_date']){
										$updateQuery = "UPDATE ".$tableNameProjects." SET
													`task_id` = '".$jDataProjects['task_id']."',
													`location_id` = '".$jDataProjects['location_id']."',
													`qa_inspection_date_raised` = '".$jDataProjects['qa_inspection_date_raised']."',
													`qa_inspection_raised_by` = '".$jDataProjects['qa_inspection_raised_by']."',
													`qa_inspection_inspected_by` = '".$jDataProjects['qa_inspection_inspected_by']."',
													`qa_inspection_description` = \"".$jDataProjects['qa_inspection_description']."\",
													`qa_inspection_location` = '".$jDataProjects['qa_inspection_location']."',
													`last_modified_by` = '".$jDataProjects['last_modified_by']."',
													`last_modified_date` = NOW(),
													`original_modified_date` = '".$jDataProjects['original_modified_date']."',
													`resource_type` = '".$jDataProjects['resource_type']."',
													`project_id` = '".$jDataProjects['project_id']."',
													`ncr` = '".$jDataProjects['ncr']."',
													`is_deleted` = '".$jDataProjects['is_deleted']."'
												WHERE `non_conformance_id` = '".$jDataProjects['global_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'inspection_history_details.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								$tbNameProjects = explode('.', $file);
								$tableNameProjects = $tbNameProjects[0];
								$query = "INSERT INTO ".$tableNameProjects." SET
											`table_name` = '".$jDataProjects['table_name']."',
											`sql_operation` = '".$jDataProjects['sql_operation']."',
											`sql_query` = '".$jDataProjects['sql_query']."',
											`project_id` = \"".$jDataProjects['project_id']."\",
											`created_by` = '".$jDataProjects['created_by']."',
											`created_date` = NOW(),
											`resource_type` = '".$jDataProjects['resource_type']."',
											`last_modified_by` = '".$jDataProjects['created_by']."',
											`last_modified_date` = NOW();";
								mysql_query($query);
								$arr['id'] = $jDataProjects['id'];
								$arr['global_id'] = mysql_insert_id();
								$data[$tableNameProjects][] = $arr; 
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'table_history_details.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);

							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								$tbNameProjects = explode('.', $file);
								$tableNameProjects = $tbNameProjects[0];
								
								$sql_query = serialize(json_decode($jDataProjects['sql_query'], true));
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$query = "INSERT INTO ".$tableNameProjects." SET
												`primary_key` = '".$jDataProjects['primary_key']."',
												`table_name` = '".$jDataProjects['table_name']."',
												`sql_operation` = '".$jDataProjects['sql_operation']."',
												`sql_query` = '".$sql_query."',
												`project_id` = \"".$jDataProjects['project_id']."\",
												`created_by` = '".$jDataProjects['created_by']."',
												`created_date` = NOW(),
												`resource_type` = '".$jDataProjects['resource_type']."',
												`last_modified_by` = '".$jDataProjects['created_by']."',
												`last_modified_date` = NOW();";
									mysql_query($query);
									$arr['id'] = $jDataProjects['id'];
									$arr['global_id'] = mysql_insert_id();
									$data[$tableNameProjects][] = $arr; 
								}
							}
							$insertArray[] = $rowArray;
						}
//New inspection avidence Dated 03-07-2013
						if($file == 'qa_ncr_task_detail.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$query = "INSERT INTO ".$tableNameProjects." SET
												`task_detail_id` = '".$jDataProjects['task_detail_id']."',
												`task_id` = '".$jDataProjects['task_id']."',
												`raised_by` = '".$jDataProjects['raised_by']."',
												`comment` = '".$jDataProjects['comment']."',
												`created_by` = '".$jDataProjects['created_by']."',
												`created_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW(),
												`resource_type` = '".$jDataProjects['resource_type']."',
												`original_modified_date` = '".$jDataProjects['original_modified_date']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataProjects['task_detail_id'];
										$arr['global_id'] = $jDataProjects['task_detail_id'];
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here	
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$orignalDate = $db->getRecordByKey($tableNameProjects, 'task_detail_id', $jDataProjects['global_id'], 'original_modified_date');
									
									if($orignalDate[0]['original_modified_date'] < $jDataProjects['original_modified_date']){
										$updateQuery = "UPDATE ".$tableNameProjects." SET
												`task_detail_id` = '".$jDataProjects['task_detail_id']."',
												`task_id` = '".$jDataProjects['task_id']."',
												`raised_by` = '".$jDataProjects['raised_by']."',
												`comment` = '".$jDataProjects['comment']."',
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW(),
												`resource_type` = '".$jDataProjects['resource_type']."',
												`original_modified_date` = '".$jDataProjects['original_modified_date']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."'
											WHERE `task_detail_id` = '".$jDataProjects['global_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'qa_ncr_attachments.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$query = "INSERT INTO ".$tableNameProjects." SET
												`task_detail_id` = '".$jDataProjects['task_detail_id']."',
												`attachment_title` = '".$jDataProjects['attachment_title']."',
												`attachment_description` = '".$jDataProjects['attachment_description']."',
												`attachment_file_name` = '".$jDataProjects['attachment_file_name']."',
												`attachment_type` = '".$jDataProjects['attachment_type']."',
												`created_by` = '".$jDataProjects['created_by']."',
												`created_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW(),
												`resource_type` = '".$jDataProjects['resource_type']."',
												`original_modified_date` = '".$jDataProjects['original_modified_date']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataProjects['ncr_attachment_id'];
										$arr['global_id'] = mysql_insert_id();
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$orignalDate = $db->getRecordByKey($tableNameProjects, 'non_conformance_id', $jDataProjects['global_id'], 'original_modified_date');
									if($orignalDate[0]['original_modified_date'] < $jDataProjects['original_modified_date']){
										$updateQuery = "UPDATE ".$tableNameProjects." SET
												`task_detail_id` = '".$jDataProjects['task_detail_id']."',
												`attachment_title` = '".$jDataProjects['attachment_title']."',
												`attachment_description` = '".$jDataProjects['attachment_description']."',
												`attachment_file_name` = '".$jDataProjects['attachment_file_name']."',
												`attachment_type` = '".$jDataProjects['attachment_type']."',
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW(),
												`resource_type` = '".$jDataProjects['resource_type']."',
												`original_modified_date` = '".$jDataProjects['original_modified_date']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."'
											WHERE `ncr_attachment_id` = '".$jDataProjects['ncr_attachment_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
//New inspection avidence Dated 03-07-2013
						if($file == 'qa_itp_checklist.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$query = "INSERT INTO ".$tableNameProjects." SET
												`qa_itp_checklist_id` = '".$jDataProjects['qa_itp_checklist_id']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`location_id` = '".$jDataProjects['location_id']."',
												`sub_location_id` = '".$jDataProjects['sub_location_id']."',
												`sub_location1_id` = '".$jDataProjects['sub_location1_id']."',
												`sub_location2_id` = '".$jDataProjects['sub_location2_id']."',
												`location_tree` = '".$jDataProjects['location_tree']."',
												`location_depth` = '".$jDataProjects['location_depth']."',
												`qa_itp_type` = '".$jDataProjects['qa_itp_type']."',
												`check_list_type` = '".$jDataProjects['check_list_type']."',
												`status` = '".$jDataProjects['status']."',
												`sc_sign` = '".$jDataProjects['sc_sign']."',
												`hmb_sign` = '".$jDataProjects['hmb_sign']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."',
												`created_by` = '".$jDataProjects['created_by']."',
												`created_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW(),
												`original_modified_date` = '".$jDataProjects['original_modified_date']."',
												`resource_type` = '".$jDataProjects['resource_type']."';";
									if(mysql_query($query)){
										$arr['qa_itp_checklist_id'] = $jDataProjects['qa_itp_checklist_id'];
										$arr['global_id'] = $jDataProjects['qa_itp_checklist_id'];
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$orignalDate = $db->getRecordByKey($tableNameProjects, 'qa_itp_checklist_id', $jDataProjects['global_id'], 'original_modified_date');
									#echo 'WE ahoe';
									#echo $orignalDate[0]['original_modified_date'].' < '.$jDataProjects['original_modified_date'];
									if($orignalDate[0]['original_modified_date'] < $jDataProjects['original_modified_date']){
										$updateQuery = "UPDATE ".$tableNameProjects." SET
															`location_id` = '".$jDataProjects['location_id']."',
															`sub_location_id` = '".$jDataProjects['sub_location_id']."',
															`sub_location1_id` = '".$jDataProjects['sub_location1_id']."',
															`sub_location2_id` = '".$jDataProjects['sub_location2_id']."',
															`location_tree` = '".$jDataProjects['location_tree']."',
															`location_depth` = '".$jDataProjects['location_depth']."',
															`qa_itp_type` = '".$jDataProjects['qa_itp_type']."',
															`check_list_type` = '".$jDataProjects['check_list_type']."',
															`status` = '".$jDataProjects['status']."',
															`sc_sign` = '".$jDataProjects['sc_sign']."',
															`hmb_sign` = '".$jDataProjects['hmb_sign']."',
															`is_deleted` = '".$jDataProjects['is_deleted']."',
															`last_modified_by` = '".$jDataProjects['last_modified_by']."',
															`last_modified_date` = NOW(),
															`original_modified_date` = '".$jDataProjects['original_modified_date']."',
															`resource_type` = '".$jDataProjects['resource_type']."'
														WHERE
															`qa_itp_checklist_id` = '".$jDataProjects['qa_itp_checklist_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						if($file == 'qa_itp_checklist_status.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$query = "INSERT INTO ".$tableNameProjects." SET
												`qa_itp_checklist_status_id` = '".$jDataProjects['qa_itp_checklist_status_id']."',
												`qa_itp_checklist_id` = '".$jDataProjects['qa_itp_checklist_id']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`status` = '".$jDataProjects['status']."',
												`qa_itp_type` = '".$jDataProjects['qa_itp_type']."',
												`check_list_type` = '".$jDataProjects['check_list_type']."',
												`check_list_items_name` = '".$jDataProjects['check_list_items_name']."',
												`completion_date` = '".$jDataProjects['completion_date']."',
												`sc_sign` = '".$jDataProjects['sc_sign']."',
												`hmb_sign` = '".$jDataProjects['hmb_sign']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."',
												`created_by` = '".$jDataProjects['created_by']."',
												`created_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`last_modified_date` = NOW(),
												`original_modified_date` = '".$jDataProjects['original_modified_date']."',
												`resource_type` = '".$jDataProjects['resource_type']."';";
									if(mysql_query($query)){
										$arr['qa_itp_checklist_status_id'] = $jDataProjects['qa_itp_checklist_status_id'];
										$arr['global_id'] = $jDataProjects['qa_itp_checklist_status_id'];
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$orignalDate = $db->getRecordByKey($tableNameProjects, 'qa_itp_checklist_status_id', $jDataProjects['global_id'], 'original_modified_date');
									if($orignalDate[0]['original_modified_date'] < $jDataProjects['original_modified_date']){
										$updateQuery = "UPDATE ".$tableNameProjects." SET
															`qa_itp_checklist_id` = '".$jDataProjects['qa_itp_checklist_id']."',
															`qa_itp_type` = '".$jDataProjects['qa_itp_type']."',
															`check_list_items_name` = '".$jDataProjects['check_list_items_name']."',
															`completion_date` = '".$jDataProjects['completion_date']."',
															`sc_sign` = '".$jDataProjects['sc_sign']."',
															`hmb_sign` = '".$jDataProjects['hmb_sign']."',
															`status` = '".$jDataProjects['status']."',
															`is_deleted` = '".$jDataProjects['is_deleted']."',
															`created_by` = '".$jDataProjects['created_by']."',
															`created_date` = NOW(),
															`last_modified_by` = '".$jDataProjects['last_modified_by']."',
															`original_modified_date` = '".$jDataProjects['original_modified_date']."',
															`last_modified_date` = NOW(),
															`resource_type` = '".$jDataProjects['resource_type']."'
													WHERE
															`qa_itp_checklist_status_id` = '".$jDataProjects['qa_itp_checklist_status_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						//Dated : 14/11/2017 quality_checklist_topmenu
						if($file == 'qa_checklist.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$query = "INSERT INTO ".$tableNameProjects." SET
												`qa_checklist_id` = '".$jDataProjects['qa_checklist_id']."',
												`project_checklist_id` = '".$jDataProjects['project_checklist_id']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`location_id` = '".$jDataProjects['location_id']."',
												`sub_location_id` = '".$jDataProjects['sub_location_id']."',
												`sub_location1_id` = '".$jDataProjects['sub_location1_id']."',
												`sub_location2_id` = '".$jDataProjects['sub_location2_id']."',
												`location_tree` = '".$jDataProjects['location_tree']."',
												`location_depth` = '".$jDataProjects['location_depth']."',
												`status` = '".$jDataProjects['status']."',
												`sc_sign` = '".$jDataProjects['sc_sign']."',
												`contractor_sign` = '".$jDataProjects['contractor_sign']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`original_modified_date` = '".$jDataProjects['original_modified_date']."',
												`created_date` = NOW(),
												`created_by` = '".$jDataProjects['created_by']."',
												`resource_type` = '".$jDataProjects['resource_type']."',
												`sub_contractor_name` = '".$jDataProjects['sub_contractor_name']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataProjects['qa_checklist_id'];
										$arr['global_id'] = $jDataProjects['qa_checklist_id'];
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$orignalDate = $db->getRecordByKey($tableNameProjects, 'non_conformance_id', $jDataProjects['global_id'], 'original_modified_date');
									if($orignalDate[0]['original_modified_date'] < $jDataProjects['original_modified_date']){
										$updateQuery = "UPDATE ".$tableNameProjects." SET
												`project_checklist_id` = '".$jDataProjects['project_checklist_id']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`location_id` = '".$jDataProjects['location_id']."',
												`sub_location_id` = '".$jDataProjects['sub_location_id']."',
												`sub_location1_id` = '".$jDataProjects['sub_location1_id']."',
												`sub_location2_id` = '".$jDataProjects['sub_location2_id']."',
												`location_tree` = '".$jDataProjects['location_tree']."',
												`location_depth` = '".$jDataProjects['location_depth']."',
												`status` = '".$jDataProjects['status']."',
												`sc_sign` = '".$jDataProjects['sc_sign']."',
												`contractor_sign` = '".$jDataProjects['contractor_sign']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`original_modified_date` = '".$jDataProjects['original_modified_date']."',
												`created_date` = NOW(),
												`created_by` = '".$jDataProjects['created_by']."',
												`resource_type` = '".$jDataProjects['resource_type']."',
												`sub_contractor_name` = '".$jDataProjects['sub_contractor_name']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."'
											WHERE `qa_checklist_id` = '".$jDataProjects['qa_checklist_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
						//Dated : 14/11/2017 checklist_task_status 
						if($file == 'qa_checklist_task_status.json'){
							$jsonFileProjects = IMPORTFILEPATH.'/json/'.$file;
							$stringProjects = file_get_contents($jsonFileProjects);
							$jsonDataProjects = json_decode($stringProjects, true);
							$updateCount = 0;
							foreach($jsonDataProjects as $jDataProjects){
								$rowArray = array();
								if($jDataProjects['global_id'] == 0){//Add Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$query = "INSERT INTO ".$tableNameProjects." SET
												`task_status_id` = '".$jDataProjects['task_status_id']."',
												`qa_checklist_id` = '".$jDataProjects['qa_checklist_id']."',
												`checklist_task_id` = '".$jDataProjects['checklist_task_id']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`task_name` = '".$jDataProjects['task_name']."',
												`status` = '".$jDataProjects['status']."',
												`task_comment` = '".$jDataProjects['task_comment']."',
												`task_image` = '".$jDataProjects['task_image']."',
												`completion_date` = '".$jDataProjects['completion_date']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`original_modified_date` = '".$jDataProjects['original_modified_date']."',
												`created_date` = NOW(),
												`created_by` = '".$jDataProjects['created_by']."',
												`resource_type` = '".$jDataProjects['resource_type']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."';";
									if(mysql_query($query)){
										$arr['id'] = $jDataProjects['task_status_id'];
										$arr['global_id'] = $jDataProjects['task_status_id'];
										$data[$tableNameProjects][] = $arr; 
									}else{
										$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
									}
								}else{//Update Query Here
									$tbNameProjects = explode('.', $file);
									$tableNameProjects = $tbNameProjects[0];
									$orignalDate = $db->getRecordByKey($tableNameProjects, 'non_conformance_id', $jDataProjects['global_id'], 'original_modified_date');
									if($orignalDate[0]['original_modified_date'] < $jDataProjects['original_modified_date']){
										$updateQuery = "UPDATE ".$tableNameProjects." SET
												`qa_checklist_id` = '".$jDataProjects['qa_checklist_id']."',
												`checklist_task_id` = '".$jDataProjects['checklist_task_id']."',
												`project_id` = '".$jDataProjects['project_id']."',
												`task_name` = '".$jDataProjects['task_name']."',
												`status` = '".$jDataProjects['status']."',
												`task_comment` = '".$jDataProjects['task_comment']."',
												`task_image` = '".$jDataProjects['task_image']."',
												`completion_date` = '".$jDataProjects['completion_date']."',
												`last_modified_date` = NOW(),
												`last_modified_by` = '".$jDataProjects['last_modified_by']."',
												`original_modified_date` = '".$jDataProjects['original_modified_date']."',
												`created_date` = NOW(),
												`created_by` = '".$jDataProjects['created_by']."',
												`resource_type` = '".$jDataProjects['resource_type']."',
												`is_deleted` = '".$jDataProjects['is_deleted']."'
											WHERE `task_status_id` = '".$jDataProjects['task_status_id']."';";
										if(mysql_query($updateQuery)){
											$updateCount++;
										}else{
											$rowArray = array($sync_url, $zipName.'.zip', mysql_error(), $deviceType, $userId, "Now()", $userId, "Now()");
										}
									}
								}
							}
							$insertArray[] = $rowArray;
						}
					}
				}//While end here for all the files
				$insertArray = array_filter($insertArray);        
				if(!empty($insertArray)){
					$inserted = $db->bulkInsert($insertArray, 'sync_url, zip_file_name, mysql_error_number, deviceType, created_by, created_date, last_modified_by, last_modified_date', 'failed_query_sync');
				}
				$jsonIdes = json_encode($data);
				$output = array(
					'status' => true,
					'message' => 'Import Successfully Done !',
					'last_modified_date' => date("Y-m-d H:i:s"),
					'data' => $jsonIdes
				);
				echo '['.json_encode($output).']';
				die;
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
				'message' => 'Unable to upload 1 !',
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
