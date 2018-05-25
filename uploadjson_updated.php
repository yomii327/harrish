<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
//Header Secttion for include and objects 
	include_once("servicesQurey.php");
	$db = new QRY_Class();
	define("ZIPPASSWORD", '20W!seWork@r12');
	define("IMPORTFILEPATH", '../sync/Import');
	define("TEXTDESTINATIONPATH", '../Import');
//Header Secttion for include and objects 
	$userId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['userId']))));
	$globalId = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['globalId']))));
	$authHash = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['authHash']))));
	$lastModifiedDate = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['lastModifiedDate']))));
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
	if($db->hashAuth($globalId, $authHash)){}else{
		$output = array(
			'status' => false,
			'message' => 'User Authentication Fail',
			'data' => ''
		);
		echo '['.json_encode($output).']';
	}
if(isset($_REQUEST['upload_zip'])){
	$db->recursive_remove_directory(IMPORTFILEPATH.'/json');
	@mkdir(IMPORTFILEPATH.'/json/', 0777);
	$extension = array('application/octet-stream');
	
	//if((in_array($_FILES["uploadZip"]["type"], $extension))){
		if($_FILES["uploadZip"]["error"] > 0){ echo "Return Code: " . $_FILES["uploadZip"]["error"] . "<br />"; }else{
			if (file_exists(IMPORTFILEPATH .'/'. $_FILES["uploadZip"]["name"])){
				echo $_FILES["uploadZip"]["name"] . " already exists. ";
			}else{
				$zipName = $db->updateImportTable(IMPORTFILEPATH);
				if(move_uploaded_file($_FILES["uploadZip"]["tmp_name"], IMPORTFILEPATH .'/'. $zipName.'.zip')){
					if($db->extractZip(IMPORTFILEPATH .'/'. $zipName.'.zip', IMPORTFILEPATH)){
						$folder = opendir(IMPORTFILEPATH.'/json/');
						$file_types = array("json");
						while($file = readdir($folder)){
							if(in_array(substr(strtolower($file), strrpos($file,".") + 1), $file_types)){
								if($file == 'inspection_graphics.json'){
									$jsonFileGraphic = IMPORTFILEPATH.'/json/'.$file;
									$stringGraphic = file_get_contents($jsonFileGraphic);
									$jsonDataGraphic = json_decode($stringGraphic, true);
									$updateCount = 0;
									foreach($jsonDataGraphic as $jDataGraphic){
										if($jDataGraphic['global_id'] == 0){//Add Query Here
											$tbNameGraphic = explode('.', $file);
											$tableNameGraphic = $tbNameGraphic[0];
											$query = "INSERT INTO ".$tableNameGraphic." SET `graphic_id` = '',
														`inspection_id` = '".$jDataGraphic['inspection_id']."',
														`graphic_type` = '".$jDataGraphic['graphic_type']."',
														`graphic_title` = '".$jDataGraphic['graphic_title']."',
														`graphic_name` = '".$jDataGraphic['graphic_name']."',
														`last_modified_date` = '".$jDataGraphic['last_modified_date']."',
														`last_modified_by` = '".$jDataGraphic['last_modified_by']."',
														`created_date` = '".$jDataGraphic['created_date']."',
														`created_by` = '".$jDataGraphic['created_by']."',
														`resource_type` = '".$jDataGraphic['resource_type']."',
														`project_id` = '".$jDataGraphic['project_id']."',
														`is_deleted` = '".$jDataGraphic['is_deleted']."';";
											mysql_query($query);
											$arr['id'] = $jDataGraphic['graphic_id'];
											$arr['global_id'] = mysql_insert_id() ;
											$data[$tableNameGraphic][] = $arr; 
										}else{//Update Query Here
											$tbNameGraphic = explode('.', $file);
											$tableNameGraphic = $tbNameGraphic[0];
											$updateQuery = "UPDATE ".$tableNameGraphic." SET
															`inspection_id` = '".$jDataGraphic['inspection_id']."',
															`graphic_type` = '".$jDataGraphic['graphic_type']."',
															`graphic_title` = '".$jDataGraphic['graphic_title']."',
															`graphic_name` = '".$jDataGraphic['graphic_name']."',
															`last_modified_date` = '".$jDataGraphic['last_modified_date']."',
															`last_modified_by` = '".$jDataGraphic['last_modified_by']."',
															`created_date` = '".$jDataGraphic['created_date']."',
															`created_by` = '".$jDataGraphic['created_by']."',
															`resource_type` = '".$jDataGraphic['resource_type']."',
															`project_id` = '".$jDataGraphic['project_id']."',
															`is_deleted` = '".$jDataGraphic['is_deleted']."'
													WHERE `graphic_id` = '".$jDataGraphic['global_id']."';";
											mysql_query($updateQuery);
											$updateCount++;
										}
									}
									//$arr['updatecount'] = $updateCount;
									//$data[$tableNameGraphic][] = $arr;
								}
								if($file == 'issued_to_for_inspections.json'){
									$jsonFileInspection = IMPORTFILEPATH.'/json/'.$file;
									$stringInspection = file_get_contents($jsonFileInspection);
									$jsonDataInspection = json_decode($stringInspection, true);
									$updateCount = 0;
									foreach($jsonDataInspection as $jDataInspection){
										if($jDataInspection['global_id'] == 0){//Add Query Here
											$tbNameInspection = explode('.', $file);
											$tableNameInspection = $tbNameInspection[0];
											$query = "INSERT INTO ".$tableNameInspection." SET `issued_to_inspections_id` = '',
														`inspection_id` = '".$jDataInspection['inspection_id']."',
														`issued_to_name` = '".$jDataInspection['issued_to_name']."',
														`last_modified_date` = '".$jDataInspection['last_modified_date']."',
														`last_modified_by` = '".$jDataInspection['last_modified_by']."',
														`created_date` = '".$jDataInspection['created_date']."',
														`created_by` = '".$jDataInspection['created_by']."',
														`resource_type` = '".$jDataInspection['resource_type']."',
														`project_id` = '".$jDataInspection['project_id']."',
														`is_deleted` = '".$jDataInspection['is_deleted']."';";
											mysql_query($query);
											$arr['id'] = $jDataInspection['id'];
											$arr['global_id'] = mysql_insert_id() ;
											$data[$tableNameInspection][] = $arr; 
										}else{//Update Query Here
											$tbNameInspection = explode('.', $file);
											$tableNameInspection = $tbNameInspection[0];
											$updateQuery = "UPDATE ".$tableNameInspection." SET
														`inspection_id` = '".$jDataInspection['inspection_id']."',
														`issued_to_name` = '".$jDataInspection['issued_to_name']."',
														`last_modified_date` = '".$jDataInspection['last_modified_date']."',
														`last_modified_by` = '".$jDataInspection['last_modified_by']."',
														`created_date` = '".$jDataInspection['created_date']."',
														`created_by` = '".$jDataInspection['created_by']."',
														`resource_type` = '".$jDataInspection['resource_type']."',
														`project_id` = '".$jDataInspection['project_id']."',
														`is_deleted` = '".$jDataInspection['is_deleted']."'
													WHERE `issued_to_inspections_id` = '".$jDataInspection['global_id']."';";
											mysql_query($updateQuery);
											$updateCount++;
										}
									}
									//$arr['updatecount'] = $updateCount;
									//$data[$tableNameInspection][] = $arr;
								}
								if($file == 'progress_monitoring_update.json'){
									$jsonFileMonitoring = IMPORTFILEPATH.'/json/'.$file;
									$stringMonitoring = file_get_contents($jsonFileMonitoring);
									$jsonDataMonitoring = json_decode($stringMonitoring, true);
									$updateCount = 0;
									foreach($jsonDataMonitoring as $jDataMonitoring){
										if($jDataMonitoring['global_id'] == 0){//Add Query Here
											$tbNameMonitoring = explode('.', $file);
											$tableNameMonitoring = $tbNameMonitoring[0];
											$query = "INSERT INTO ".$tableNameMonitoring." SET `update_id` = '',
														`progress_id` = '".$jDataMonitoring['progress_id']."',
														`percentage` = '".$jDataMonitoring['percentage']."',
														`status` = '".$jDataMonitoring['status']."',
														`last_modified_date` = '".$jDataMonitoring['last_modified_date']."',
														`last_modified_by` = '".$jDataMonitoring['last_modified_by']."',
														`created_date` = '".$jDataMonitoring['created_date']."',
														`created_by` = '".$jDataMonitoring['created_by']."',
														`resource_type` = '".$jDataMonitoring['resource_type']."',
														`project_id` = '".$jDataMonitoring['project_id']."',
														`is_deleted` = '".$jDataMonitoring['is_deleted']."';";
											mysql_query($query);
											$arr['id'] = $jDataMonitoring['update_id'];
											$arr['global_id'] = mysql_insert_id() ;
											$data[$tableNameMonitoring][] = $arr; 
										}else{//Update Query Here
											$tbNameMonitoring = explode('.', $file);
											$tableNameMonitoring = $tbNameMonitoring[0];
											$updateQuery = "UPDATE ".$tableNameMonitoring." SET
														`progress_id` = '".$jDataMonitoring['progress_id']."',
														`percentage` = '".$jDataMonitoring['percentage']."',
														`status` = '".$jDataMonitoring['status']."',
														`last_modified_date` = '".$jDataMonitoring['last_modified_date']."',
														`last_modified_by` = '".$jDataMonitoring['last_modified_by']."',
														`created_date` = '".$jDataMonitoring['created_date']."',
														`created_by` = '".$jDataMonitoring['created_by']."',
														`resource_type` = '".$jDataMonitoring['resource_type']."',
														`project_id` = '".$jDataMonitoring['project_id']."',
														`is_deleted` = '".$jDataMonitoring['is_deleted']."'
													WHERE `update_id` = '".$jDataMonitoring['global_id']."';";
											mysql_query($updateQuery);
											$updateCount++;
										}
									}
									//$arr['updatecount'] = $updateCount;
									//$data[$tableNameMonitoring][] = $arr;
								}
								if($file == 'progress_monitoring.json'){
									$jsonFileMonitoring = IMPORTFILEPATH.'/json/'.$file;
									$stringMonitoring = file_get_contents($jsonFileMonitoring);
									$jsonDataMonitoring = json_decode($stringMonitoring, true);
									$updateCount = 0;
									foreach($jsonDataMonitoring as $jDataMonitoring){
										if($jDataMonitoring['global_id'] == 0){//Add Query Here
											$tbNameMonitoring = explode('.', $file);
											$tableNameMonitoring = $tbNameMonitoring[0];
											$query = "INSERT INTO ".$tableNameMonitoring." SET 
														`progress_id` = '".$jDataMonitoring['progress_id']."',
														`project_id` = '".$jDataMonitoring['project_id']."',
														`location_id` = '".$jDataMonitoring['location_id']."',
														`sub_location_id` = '".$jDataMonitoring['sub_location_id']."',
														`task` = '".$jDataMonitoring['task']."',
														`start_date` = '".$jDataMonitoring['start_date']."',
														`end_date` = '".$jDataMonitoring['end_date']."',
														`last_modified_date` = '".$jDataMonitoring['last_modified_date']."',
														`last_modified_by` = '".$jDataMonitoring['last_modified_by']."',
														`created_date` = '".$jDataMonitoring['created_date']."',
														`created_by` = '".$jDataMonitoring['created_by']."',
														`resource_type` = '".$jDataMonitoring['resource_type']."',
														`percentage` = '".$jDataMonitoring['percentage']."',
														`status` = '".$jDataMonitoring['status']."',
														`is_deleted` = '".$jDataMonitoring['is_deleted']."';";
											mysql_query($query);
											$arr['id'] = $jDataMonitoring['progress_id'];
											$arr['global_id'] = mysql_insert_id() ;
											$data[$tableNameMonitoring][] = $arr; 
										}else{//Update Query Here
											$tbNameMonitoring = explode('.', $file);
											$tableNameMonitoring = $tbNameMonitoring[0];
											$updateQuery = "UPDATE ".$tableNameMonitoring." SET
														`progress_id` = '".$jDataMonitoring['progress_id']."',
														`project_id` = '".$jDataMonitoring['project_id']."',
														`location_id` = '".$jDataMonitoring['location_id']."',
														`sub_location_id` = '".$jDataMonitoring['sub_location_id']."',
														`task` = '".$jDataMonitoring['task']."',
														`start_date` = '".$jDataMonitoring['start_date']."',
														`end_date` = '".$jDataMonitoring['end_date']."',
														`last_modified_date` = '".$jDataMonitoring['last_modified_date']."',
														`last_modified_by` = '".$jDataMonitoring['last_modified_by']."',
														`created_date` = '".$jDataMonitoring['created_date']."',
														`created_by` = '".$jDataMonitoring['created_by']."',
														`resource_type` = '".$jDataMonitoring['resource_type']."',
														`percentage` = '".$jDataMonitoring['percentage']."',
														`status` = '".$jDataMonitoring['status']."',
														`is_deleted` = '".$jDataMonitoring['is_deleted']."'
													WHERE `progress_id` = '".$jDataMonitoring['global_id']."';";
											mysql_query($updateQuery);
											$updateCount++;
										}
									}
									//$arr['updatecount'] = $updateCount;
									//$data[$tableNameMonitoring][] = $arr;
								}

								if($file == 'project_inspections.json'){
									$jsonFilePinspections = IMPORTFILEPATH.'/json/'.$file;
									$stringPinspections = file_get_contents($jsonFilePinspections);
									$jsonDataPinspections = json_decode($stringPinspections, true);
									$updateCount = 0;
									foreach($jsonDataPinspections as $jDataPinspections){
										if($jDataPinspections['global_id'] == 0){//Add Query Here
											$tbNamePinspections = explode('.', $file);
											$tableNamePinspections = $tbNamePinspections[0];
											$query = "INSERT INTO ".$tableNamePinspections." SET `inspection_id` = '".$jDataPinspections['inspection_id']."',
													`project_id` = '".$jDataPinspections['project_id']."',
													`location_id` = '".$jDataPinspections['location_id']."',
													`inspection_date_raised` = '".$jDataPinspections['inspection_date_raised']."',
													`inspection_status` = '".$jDataPinspections['inspection_status']."',
													`inspection_inspected_by` = '".$jDataPinspections['inspection_inspected_by']."',
													`inspection_fixed_by_date` = '".$jDataPinspections['inspection_fixed_by_date']."',
													`inspection_type` = '".$jDataPinspections['inspection_type']."',
													`inspection_map_address` = '".$jDataPinspections['inspection_map_address']."',
													`inspection_description` = '".$jDataPinspections['inspection_description']."',
													`inspection_priority` = '".$jDataPinspections['inspection_priority']."',
													`inspection_notes` = '".$jDataPinspections['inspection_notes']."',
													`inspection_sign_image` = '".$jDataPinspections['inspection_sign_image']."',
													`inspection_location` = '".$jDataPinspections['inspection_location']."',
													`cost_attribute` = '".$jDataPinspections['cost_attribute']."',
													`last_modified_date` = '".$jDataPinspections['last_modified_date']."',
													`last_modified_by` = '".$jDataPinspections['last_modified_by']."',
													`created_date` = '".$jDataPinspections['created_date']."',
													`created_by` = '".$jDataPinspections['created_by']."',
													`resource_type` = '".$jDataPinspections['resource_type']."',
													`is_deleted` = '".$jDataPinspections['is_deleted']."';";
											mysql_query($query);
											$arr['id'] = $jDataPinspections['inspection_id'];
											$arr['global_id'] = $jDataPinspections['inspection_id'] ;
											$data[$tableNamePinspections][] = $arr; 
										}else{//Update Query Here
											$tbNamePinspections = explode('.', $file);
											$tableNamePinspections = $tbNamePinspections[0];
											$updateQuery = "UPDATE ".$tableNamePinspections." SET
													`project_id` = '".$jDataPinspections['project_id']."',
													`location_id` = '".$jDataPinspections['location_id']."',
													`inspection_date_raised` = '".$jDataPinspections['inspection_date_raised']."',
													`inspection_status` = '".$jDataPinspections['inspection_status']."',
													`inspection_inspected_by` = '".$jDataPinspections['inspection_inspected_by']."',
													`inspection_fixed_by_date` = '".$jDataPinspections['inspection_fixed_by_date']."',
													`inspection_type` = '".$jDataPinspections['inspection_type']."',
													`inspection_map_address` = '".$jDataPinspections['inspection_map_address']."',
													`inspection_description` = '".$jDataPinspections['inspection_description']."',
													`inspection_priority` = '".$jDataPinspections['inspection_priority']."',
													`inspection_notes` = '".$jDataPinspections['inspection_notes']."',
													`inspection_sign_image` = '".$jDataPinspections['inspection_sign_image']."',
													`inspection_location` = '".$jDataPinspections['inspection_location']."',
													`cost_attribute` = '".$jDataPinspections['cost_attribute']."',
													`last_modified_date` = '".$jDataPinspections['last_modified_date']."',
													`last_modified_by` = '".$jDataPinspections['last_modified_by']."',
													`created_date` = '".$jDataPinspections['created_date']."',
													`created_by` = '".$jDataPinspections['created_by']."',
													`resource_type` = '".$jDataPinspections['resource_type']."',
													`is_deleted` = '".$jDataPinspections['is_deleted']."'
													WHERE `inspection_id` = '".$jDataPinspections['global_id']."';";
											mysql_query($updateQuery);
											$updateCount++;
										}
									}
									//$arr['updatecount'] = $updateCount;
									//$data[$tableNamePinspections][] = $arr;
								}
								if($file == 'project_locations.json'){
									$jsonFilePlocations = IMPORTFILEPATH.'/json/'.$file;
									$stringPlocations = file_get_contents($jsonFilePlocations);
									$jsonDataPlocations = json_decode($stringPlocations, true);
									$updateCount = 0;
									foreach($jsonDataPlocations as $jDataPlocations){
										if($jDataPlocations['global_id'] == 0){//Add Query Here
											$tbNamePlocations = explode('.', $file);
											$tableNamePlocations = $tbNamePlocations[0];
											$query = "INSERT INTO ".$tableNamePlocations." SET `location_id` = '',
														`location_parent_id` = '".$jDataPlocations['location_parent_id']."',
														`location_title` = '".$jDataPlocations['location_title']."',
														`last_modified_date` = '".$jDataPlocations['last_modified_date']."',
														`last_modified_by` = '".$jDataPlocations['last_modified_by']."',
														`created_date` = '".$jDataPlocations['created_date']."',
														`created_by` = '".$jDataPlocations['created_by']."',
														`resource_type` = '".$jDataPlocations['resource_type']."',
														`project_id` = '".$jDataPlocations['project_id']."',
														`is_deleted` = '".$jDataPlocations['is_deleted']."';";
											mysql_query($query);
											$arr['id'] = $jDataPlocations['location_id'];
											$arr['global_id'] = mysql_insert_id() ;
											$data[$tableNamePlocations][] = $arr; 
										}else{//Update Query Here
											$tbNamePlocations = explode('.', $file);
											$tableNamePlocations = $tbNamePlocations[0];
											$updateQuery = "UPDATE ".$tableNamePlocations." SET
														`location_parent_id` = '".$jDataPlocations['location_parent_id']."',
														`location_title` = '".$jDataPlocations['location_title']."',
														`last_modified_date` = '".$jDataPlocations['last_modified_date']."',
														`last_modified_by` = '".$jDataPlocations['last_modified_by']."',
														`created_date` = '".$jDataPlocations['created_date']."',
														`created_by` = '".$jDataPlocations['created_by']."',
														`resource_type` = '".$jDataPlocations['resource_type']."',
														`project_id` = '".$jDataPlocations['project_id']."',
														`is_deleted` = '".$jDataPlocations['is_deleted']."'
													WHERE `location_id` = '".$jDataPlocations['global_id']."';";
											mysql_query($updateQuery);
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
										if($jDataProjects['global_id'] == 0){//Add Query Here
											$tbNameProjects = explode('.', $file);
											$tableNameProjects = $tbNameProjects[0];
											$query = "INSERT INTO ".$tableNameProjects." SET `project_id` = '',
														`user_id` = '".$jDataProjects['user_id']."',
														`pro_code` = '',
														`project_name` = '".$jDataProjects['project_name']."',
														`project_type` = '".$jDataProjects['project_type']."',
														`project_address_line1` = '".$jDataProjects['project_address_line1']."',
														`project_address_line2` = '".$jDataProjects['project_address_line2']."',
														`project_suburb` = '".$jDataProjects['project_suburb']."',
														`project_state` = '".$jDataProjects['project_state']."',
														`project_postcode` = '".$jDataProjects['project_postcode']."',
														`project_country` = '".$jDataProjects['project_country']."',
														`last_modified_date` = '".$jDataProjects['last_modified_date']."',
														`last_modified_by` = '".$jDataProjects['last_modified_by']."',
														`created_date` = '".$jDataProjects['created_date']."',
														`created_by` = '".$jDataProjects['created_by']."',
														`resource_type` = '".$jDataProjects['resource_type']."',
														`is_deleted` = '".$jDataProjects['is_deleted']."';";
											mysql_query($query);
											$arr['id'] = $jDataProjects['project_id'];
											$arr['global_id'] = mysql_insert_id() ;
											$data[$tableNameProjects][] = $arr; 
										}else{//Update Query Here
											$tbNameProjects = explode('.', $file);
											$tableNameProjects = $tbNameProjects[0];
											$updateQuery = "UPDATE ".$tableNameProjects." SET
														`user_id` = '".$jDataProjects['user_id']."',
														`pro_code` = '',
														`project_name` = '".$jDataProjects['project_name']."',
														`project_type` = '".$jDataProjects['project_type']."',
														`project_address_line1` = '".$jDataProjects['project_address_line1']."',
														`project_address_line2` = '".$jDataProjects['project_address_line2']."',
														`project_suburb` = '".$jDataProjects['project_suburb']."',
														`project_state` = '".$jDataProjects['project_state']."',
														`project_postcode` = '".$jDataProjects['project_postcode']."',
														`project_country` = '".$jDataProjects['project_country']."',
														`last_modified_date` = '".$jDataProjects['last_modified_date']."',
														`last_modified_by` = '".$jDataProjects['last_modified_by']."',
														`created_date` = '".$jDataProjects['created_date']."',
														`created_by` = '".$jDataProjects['created_by']."',
														`resource_type` = '".$jDataProjects['resource_type']."',
														`is_deleted` = '".$jDataProjects['is_deleted']."'
													WHERE `project_id` = '".$jDataProjects['global_id']."';";
											mysql_query($updateQuery);
											$updateCount++;
										}
									}
									//$arr['updatecount'] = $updateCount;
									//$data[$tableNameProjects][] = $arr;
								}
							}
						}//While end here for all the files
						//Json output here
						//print_r($data);
						$jsonIdes = json_encode($data);
						$output = array(
							'status' => true,
							'message' => 'Import Successfully Done !',
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
						'message' => 'Unable to upload !',
						'data' => ''
					);
					echo '['.json_encode($output).']';
					die;
				};
			}
		}
	/*}else{
		$output = array(
			'status' => false,
			'message' => 'Invalid Uploaded Zip !',
			'data' => ''
		);
		echo '['.json_encode($output).']';die;
	}*/




}else{
	$output = array(
		'status' => false,
		'message' => 'Invalid Url !',
		'data' => ''
	);
	echo '['.json_encode($output).']';
}
?>