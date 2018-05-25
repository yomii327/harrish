<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

include 'includes/commanfunction.php';
$object = new COMMAN_Class();

//Code for Set web_report_checklist Permissions start here
if($_REQUEST['task'] == 'add_quality_checklist_permission'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);
	
	$userData = $object->selQRYMultiple('user_id,user_type', 'user', 'is_deleted = 0');
	
	foreach($userData as $userD){
		$isAllow = 0;
		echo $permissionQry = "INSERT INTO user_permission SET
									user_id = '".$userD['user_id']."',
									permission_name = 'web_menu_quality_checklist',
									is_allow = '".$isAllow."',
									created_by = '0',
									created_date = NOW()";
		mysql_query($permissionQry);
	}
}
die;

//Code for Set web_report_checklist Permissions start here
if($_REQUEST['task'] == 'add_checklist_permission'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);
	
	$userData = $object->selQRYMultiple('user_id,user_type', 'user', 'is_deleted = 0');
	
	foreach($userData as $userD){
		$isAllow = 0;
		if(isset($userD['user_type']) && $userD['user_type'] == 'manager'){
			$isAllow = 1;
		}

		echo $permissionQry = "INSERT INTO user_permission SET
									user_id = '".$userD['user_id']."',
									permission_name = 'web_report_checklist',
									is_allow = '".$isAllow."',
									created_by = '0',
									created_date = NOW()";
		mysql_query($permissionQry);
	}
}
die;

//Code for Set Permissions start here
if($_REQUEST['task'] == 'add_permission'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0');
	
	foreach($ids as $id){
		echo $permissionQry = "INSERT INTO user_permission SET
									user_id = '".$id['user_id']."',
									permission_name = 'web_message_board',
									is_allow = '0',
									created_by = '0',
									created_date = NOW()";
		mysql_query($permissionQry);
	}
}
die;

#drawing_register_module_one
if($_REQUEST['task'] == 'drawingRegister'){
	$selectQuery = "SELECT * FROM drawing_register_module_one WHERE is_deleted = 0";
	$selectData = mysql_query($selectQuery);
	while ($qryData = mysql_fetch_assoc($selectData)) {
		$ext = '';
		$id = $qryData['id'];
	    $pdf_name = $qryData['pdf_name'];
	    $dwg_name = $qryData['dwg_name'];
	    $img_name = $qryData['img_name'];
	    if(!empty($pdf_name)){
	    	$fNameArr = explode('.', $pdf_name);
	    	if(isset($fNameArr[1])){
	    		$file_ext = $fNameArr[1];
				if(strtolower($file_ext) == 'pdf'){
					$ext = 'PDF';
				}
			}
	    }elseif(!empty($dwg_name)){
	    	$fNameArr = explode('.', $dwg_name);
	    	if(isset($fNameArr[1])){
				$file_ext = $fNameArr[1];
				if(strtolower($file_ext) == 'dwg'){
					$ext = 'DWG';
				}
			}
	    }

	    $upQRY = "UPDATE drawing_register_module_one SET file_type = '".$ext."' WHERE id = ".$id;
		mysql_query($upQRY);
	}
}
die;

if($_REQUEST['task'] == 'conCalender'){
	$table    = "public_holidays";
	$fileName = "csv/Project_Leave_2017_new.csv";
	$ignoreFirstRow = 1;
	$insertDataArr = array();
	if (($handle = fopen($fileName, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			if($ignoreFirstRow != 1){
				$d = explode("/",$data[0]);
				$data[0] = $d[2]."-".$d[1]."-".$d[0];
				$insertDataArr[] = "('".$data[0]."', '".$data[1]."', NOW(), 0, 0, NOW(), NOW())";
			 }
			 $ignoreFirstRow++;
		}
		fclose($handle);
	}
	
	$sql = "INSERT INTO ".$table." (`date`, `leave_type`, `created_date`, `created_by`, `last_modified_by`, `last_modified_date`, `original_modified_date`) values ".join(", ", $insertDataArr);
	mysql_query($sql);
	
	$defaultLeave = $object->selQRYMultiple('date, leave_type, reason, is_leave', 'public_holidays', 'is_deleted = 0 and  `date` >= "2016-01-01"');
	
	$projectData = $object->selQRYMultiple('project_id, created_by', 'projects', 'is_deleted = 0');
	
	foreach($projectData as $projData){
		$insertDataArr = array();
		foreach($defaultLeave as $val){
			$insertDataArr[] = '('.$projData['project_id'].', "'.$val['date'].'", "'.$val['leave_type'].'", "'.$val['reason'].'", "'.$val['is_leave'].'", NOW(), "'.$projData['created_by'].'", NOW(), "'.$projData['created_by'].'", NOW())';
		}
		
echo		$insertQuery = "INSERT INTO project_leave (project_id, date, leave_type, reason, is_leave, created_date, created_by, last_modified_date, last_modified_by, original_modified_date) values ".join(", ", $insertDataArr);
		
		mysql_query($insertQuery);	
	}
	
	echo "Done";
}


die;


// Assign all active project to selected user.
if($_REQUEST['task'] == 'assignProjectsToUser'){ echo 'AssignProjectsToUser started <br>';
	if(isset($_REQUEST['userId']) && $_REQUEST['userId']>0){
		$projects = $object->selQRYMultiple('*', 'projects', 'is_deleted = 0 AND project_id IN ( 48, 101, 102 ) ');
	
	#	print_r($projects);die;
		foreach($projects as $key => $proj){
			echo '<br>'.$key."  =  ";
			echo $projectQry = "INSERT INTO user_projects SET
						project_id = '".$proj['project_id']."',
						pro_code = '".$proj['pro_code']."',
						user_id = '".$_REQUEST['userId']."',
						project_name = '".$proj['project_name']."',
						project_type = '".$proj['project_type']."',
						project_address_line1 = '".$proj['project_address_line1']."',
						project_address_line2 = '".$proj['project_address_line2']."',
						project_suburb = '".$proj['project_suburb']."',
						project_state = '".$proj['project_state']."',
						project_postcode = '".$proj['project_postcode']."',
						project_country = '".$proj['project_country']."',
						resource_type = '".$proj['Webserver']."',
						is_deleted = '".$proj['is_deleted']."',
						is_pdf = '".$proj['is_pdf']."',
						last_modified_date = now(),
						last_modified_by = 0,
						created_date = now(),
						created_by = 0";
				
				mysql_query($projectQry);
		}
	}
	die('AssignProjectsToUser task finished');
} 

if($_REQUEST['task'] == 'issueToData_sd'){
//Projec wise Standard Defect Data
	$projData = $object->selQRYMultiple('project_id, standard_defect_id, issued_to', 'standard_defects', 'is_deleted =0 AND issued_to != ""');
	$projwiseStData = array();
	$projidsArr = array();
#	print_r($projData);
	foreach($projData as $pData){
		if(is_array($projwiseStData[$pData['project_id']])){
			$projwiseStData[$pData['project_id']][$pData['standard_defect_id']] = $pData['issued_to'];
		}else{
			$projidsArr[] = $pData['project_id'];
			$projwiseStData[$pData['project_id']] = array();
			$projwiseStData[$pData['project_id']][$pData['standard_defect_id']] = $pData['issued_to'];
		}
	}
	
//Projec wise IssueTo Data
	$issueToData = $object->selQRYMultiple('project_id, issue_to_name, company_name', 'inspection_issue_to', 'company_name != ""');
	$projwiseIsData = array();
#print_r($issueToData);
	foreach($issueToData as $iToData){
		if(is_array($projwiseIsData[$iToData['project_id']])){
			$projwiseIsData[$iToData['project_id']][$iToData['issue_to_name']] = $iToData['company_name'];
		}else{
			$projwiseIsData[$iToData['project_id']] = array();
			$projwiseIsData[$iToData['project_id']][$iToData['issue_to_name']] = $iToData['company_name'];			
		}
	}
	
//Start itrations here
	foreach($projwiseStData as $key=>$stData){
		foreach($stData as $keySt=>$valSt){
			if(	array_key_exists($valSt, $projwiseIsData[$key]) ){
				echo $upQRY = "UPDATE standard_defects SET issued_to = '".$valSt." ( ".$projwiseIsData[$key][$valSt]." )', last_modified_date = NOW() WHERE standard_defect_id = ".$keySt." AND project_id = ".$key;
				mysql_query($upQRY);
			}
		}
	}
}

if($_REQUEST['task'] == 'issueToData_checkList'){
//Projec wise Standard Defect Data
	$projData = $object->selQRYMultiple('project_id, check_list_items_id, issued_to', 'check_list_items', 'is_deleted =0 AND issued_to != ""');
	$projwiseStData = array();
#	print_r($projData);
	foreach($projData as $pData){
		if(is_array($projwiseStData[$pData['project_id']])){
			$projwiseStData[$pData['project_id']][$pData['check_list_items_id']] = $pData['issued_to'];
		}else{
			$projwiseStData[$pData['project_id']] = array();
			$projwiseStData[$pData['project_id']][$pData['check_list_items_id']] = $pData['issued_to'];
		}
	}
	
//Projec wise IssueTo Data
	$issueToData = $object->selQRYMultiple('project_id, issue_to_name, company_name', 'inspection_issue_to', 'company_name != ""');
	$projwiseIsData = array();
#print_r($issueToData);
	foreach($issueToData as $iToData){
		if(is_array($projwiseIsData[$iToData['project_id']])){
			$projwiseIsData[$iToData['project_id']][$iToData['issue_to_name']] = $iToData['company_name'];
		}else{
			$projwiseIsData[$iToData['project_id']] = array();
			$projwiseIsData[$iToData['project_id']][$iToData['issue_to_name']] = $iToData['company_name'];			
		}
	}
	
//Start itrations here
	foreach($projwiseStData as $key=>$stData){
		foreach($stData as $keySt=>$valSt){
			if(	array_key_exists($valSt, $projwiseIsData[$key]) ){
				echo $upQRY = "UPDATE check_list_items SET issued_to = '".$valSt." ( ".$projwiseIsData[$key][$valSt]." )', last_modified_date = NOW() WHERE check_list_items_id = ".$keySt." AND project_id = ".$key;
				mysql_query($upQRY);
			}
		}
	}
}

if($_REQUEST['task'] == 'issueToData_insp'){
//Projec wise Standard Defect Data
	$projData = $object->selQRYMultiple('project_id, issued_to_inspections_id, issued_to_name', 'issued_to_for_inspections', 'is_deleted =0 AND issued_to_name != ""');
	$projwiseStData = array();
#	print_r($projData);
	foreach($projData as $pData){
		if(is_array($projwiseStData[$pData['project_id']])){
			$projwiseStData[$pData['project_id']][$pData['issued_to_inspections_id']] = $pData['issued_to_name'];
		}else{
			$projwiseStData[$pData['project_id']] = array();
			$projwiseStData[$pData['project_id']][$pData['issued_to_inspections_id']] = $pData['issued_to_name'];
		}
	}
	
//Projec wise IssueTo Data
	$issueToData = $object->selQRYMultiple('project_id, issue_to_name, company_name', 'inspection_issue_to', 'company_name != ""');
	$projwiseIsData = array();
#print_r($issueToData);
	foreach($issueToData as $iToData){
		if(is_array($projwiseIsData[$iToData['project_id']])){
			$projwiseIsData[$iToData['project_id']][$iToData['issue_to_name']] = $iToData['company_name'];
		}else{
			$projwiseIsData[$iToData['project_id']] = array();
			$projwiseIsData[$iToData['project_id']][$iToData['issue_to_name']] = $iToData['company_name'];			
		}
	}
#	echo '<pre>';print_r($projwiseStData);print_r($projwiseIsData);die;
//Start itrations here
	foreach($projwiseStData as $key=>$stData){
		foreach($stData as $keySt=>$valSt){
			if(	array_key_exists($valSt, $projwiseIsData[$key]) ){
				echo $upQRY = "UPDATE issued_to_for_inspections SET issued_to_name = '".$valSt." ( ".$projwiseIsData[$key][$valSt]." )', last_modified_date = NOW() WHERE issued_to_inspections_id = ".$keySt." AND project_id = ".$key;
				mysql_query($upQRY);
			}
		}
	}
}

if($_REQUEST['task'] == 'issueToData'){
//Project Data from inspection_issue_to
	$projData = $object->selQRYMultiple('DISTINCT project_id', 'inspection_issue_to', 'is_deleted =0');
//Project Data from inspection_issue_to
	$masterIssueToArr = array();
	$masterIssueToData = $object->selQRYMultiple('id, issue_to_name', 'master_issue_to', 'is_deleted =0');
	foreach($masterIssueToData as $mData){
		$masterIssueToArr[$mData['id']] = $mData['issue_to_name'];
	}
//Project Data from inspection_issue_to
	$masterIssueToContactArr = array();
	$masterIssueToContactPersonArr = array();
	$masterIssueToContactData = $object->selQRYMultiple('contact_id, issue_to_name, company_name', 'master_issue_to_contact', 'is_deleted =0');
	foreach($masterIssueToContactData as $mData){
		$masterIssueToContactArr[$mData['contact_id']] = $mData['issue_to_name'];
		$masterIssueToContactPersonArr[$mData['contact_id']] = $mData['company_name'];
	}
	
	foreach($projData as $proData){
		echo '<h1>'.$proData['project_id'].'</h1>';
		$issueToData = $object->selQRYMultiple('issue_to_name, company_name, issue_to_phone, issue_to_email, last_modified_date, last_modified_by, created_date, created_by, resource_type, is_deleted, tag, activity, cast(GROUP_CONCAT(issue_to_id) as CHAR) AS issueID, issue_to_name, GROUP_CONCAT(company_name) AS companyName ', 'inspection_issue_to', 'project_id = '.$proData['project_id'].' AND is_deleted = 0 GROUP BY issue_to_name');
		foreach($issueToData as $iss2Data){
			$issueIDArr = explode(",", $iss2Data['issueID']);
			$companyNameArr = explode(",", $iss2Data['companyName']);
			if(sizeof($issueIDArr) > 1){
				$g = 0;
				foreach($issueIDArr as $key=>$val){$g++;
					$isDefault = 0;
					if($g == 0){
						$isDefault = 1;
						if(!in_array($iss2Data['issue_to_name'], $masterIssueToArr)){
echo '<br />'.							$issueToMasterQry = "INSERT INTO master_issue_to SET 
									issue_to_name = '".addslashes($iss2Data['issue_to_name'])."',
									company_name = '".addslashes($iss2Data['company_name'])."',
									issue_to_phone = '".addslashes($iss2Data['issue_to_phone'])."',
									issue_to_email = '".addslashes($iss2Data['issue_to_email'])."',
									last_modified_date = NOW(),
									last_modified_by = '".$iss2Data['last_modified_by']."',
									created_date = NOW(),
									created_by = '".$iss2Data['created_by']."',
									resource_type = '".$iss2Data['resource_type']."',
									is_deleted = '".$iss2Data['is_deleted']."',
									tag = '".$iss2Data['tag']."',
									activity = '".$iss2Data['activity']."'";
							mysql_query($issueToMasterQry);
							$masterIssueID = mysql_insert_id();
						}else{
							$masterIssueID = array_search($iss2Data['issue_to_name'], $masterIssueToArr);
						}
					}
					if(!in_array($iss2Data['issue_to_name'], $masterIssueToContactArr) && $iss2Data['company_name'] != ""){	
echo '<br />'.						$issueToContactQry = "INSERT INTO master_issue_to_contact SET 
								master_issue_id = '".$masterIssueID."',
								issue_to_name = '".addslashes($iss2Data['issue_to_name'])."',
								company_name = '".addslashes($iss2Data['company_name'])."',
								issue_to_phone = '".addslashes($iss2Data['issue_to_phone'])."',
								issue_to_email = '".addslashes($iss2Data['issue_to_email'])."',
								last_modified_date = NOW(),
								last_modified_by = '".$iss2Data['last_modified_by']."',
								created_date = NOW(),
								created_by = '".$iss2Data['created_by']."',
								resource_type = '".$iss2Data['resource_type']."',
								is_deleted = '".$iss2Data['is_deleted']."',
								tag = '".$iss2Data['tag']."',
								activity = '".$iss2Data['activity']."',
								is_default = ".$isDefault;
						mysql_query($issueToContactQry);
						$masterIssueContactID = mysql_insert_id();
					}else{
						$masterIssueContactID = array_search($iss2Data['issue_to_name'], $masterIssueToContactArr);
					}	
	//Update Query Here for issueto update
echo '<br />'.					$issueToUpdateQry = "UPDATE inspection_issue_to SET 
							master_issue_id = '".$masterIssueID."',
							master_contact_id = '".$masterIssueContactID."',
							is_default = ".$isDefault."
						WHERE
							issue_to_id = ".$issueIDArr[0];
					mysql_query($issueToUpdateQry);
				}
			}else{
				if(!in_array($iss2Data['issue_to_name'], $masterIssueToArr)){
echo '<br />'.					$issueToMasterQry = "INSERT INTO master_issue_to SET 
							issue_to_name = '".addslashes($iss2Data['issue_to_name'])."',
							company_name = '".addslashes($iss2Data['company_name'])."',
							issue_to_phone = '".addslashes($iss2Data['issue_to_phone'])."',
							issue_to_email = '".addslashes($iss2Data['issue_to_email'])."',
							last_modified_date = NOW(),
							last_modified_by = '".$iss2Data['last_modified_by']."',
							created_date = NOW(),
							created_by = '".$iss2Data['created_by']."',
							resource_type = '".$iss2Data['resource_type']."',
							is_deleted = '".$iss2Data['is_deleted']."',
							tag = '".$iss2Data['tag']."',
							activity = '".$iss2Data['activity']."'";
					mysql_query($issueToMasterQry);
					$masterIssueID = mysql_insert_id();
				}else{
					$masterIssueID = array_search($iss2Data['issue_to_name'], $masterIssueToArr);
				}	
				if(!in_array($iss2Data['issue_to_name'], $masterIssueToContactArr)){
echo '<br />'.					$issueToContactQry = "INSERT INTO master_issue_to_contact SET 
							master_issue_id = '".$masterIssueID."',
							issue_to_name = '".addslashes($iss2Data['issue_to_name'])."',
							company_name = '".addslashes($iss2Data['company_name'])."',
							issue_to_phone = '".addslashes($iss2Data['issue_to_phone'])."',
							issue_to_email = '".addslashes($iss2Data['issue_to_email'])."',
							last_modified_date = NOW(),
							last_modified_by = '".$iss2Data['last_modified_by']."',
							created_date = NOW(),
							created_by = '".$iss2Data['created_by']."',
							resource_type = '".$iss2Data['resource_type']."',
							is_deleted = '".$iss2Data['is_deleted']."',
							tag = '".$iss2Data['tag']."',
							activity = '".$iss2Data['activity']."',
							is_default = 1";
					mysql_query($issueToContactQry);
					$masterIssueContactID = mysql_insert_id();
				}else{
					$masterIssueContactID = array_search($iss2Data['issue_to_name'], $masterIssueToContactArr);
				}	

//Update Query Here for issueto update
echo '<br />'.				$issueToUpdateQry = "UPDATE inspection_issue_to SET 
						master_issue_id = '".$masterIssueID."',
						master_contact_id = '".$masterIssueContactID."',
						is_default = 1
					WHERE
						issue_to_id = ".$issueIDArr[0];
				mysql_query($issueToUpdateQry);
			}
		}
		#print_r($issueToData);die;
	}
}
die;


if($_REQUEST['task'] == 'fetch_files'){
	echo '<pre>';
	$inspArr = array(9731431494012, 9731431494192, 9731431494349, 9731431494478, 9731431495964, 9731431496150, 9731431496470, 9731431496623, 9731431496742, 9731431578348, 9731433134855, 9731434081515, 9731434082888, 9731434083564, 9731434083949, 9731434084757, 9731434085149, 9731434327280, 9731434327370, 9731434327922, 9731434328010, 9731434328126, 9731434328292, 9731434328833, 9731434329087, 9731434329924, 9731434330645, 9731434331780, 9731434417330, 9731434417370, 9731434417825, 9731434418008, 9731434418221, 9731434419488, 9731434420137, 9731434427956, 9731434428149, 9731434430003, 9731434430075, 9731434430427, 9731434430627, 9731434430758, 9731434430904, 9731434431536, 9731434431636, 9731434432278, 9731434432449, 9731434432524, 9731437457090, 9731437457223, 9731437457272, 9731437457404, 9731437457568, 9731437457633, 9731437457692, 9731437457938, 9731437458039, 9731437458365, 9731437458425, 9731437458502, 9731437458597, 9731441859307, 9731441859412, 9731441859599, 9731441860041, 9731441860138, 9731441860334, 9731441860426, 9731441860482, 9731441861514, 9731441861596, 9731441862008, 9731441862585, 9731441862787, 9731441862837, 9731441863180, 9731441863668, 9731441863720, 9731441863849, 9731441863912, 9731441864102, 9731441864417, 9731441864641, 9731441864732, 9731441864784, 9731441864909, 9731441864982, 9731441865217, 9731441865299, 9731441865387, 9731441865454, 9731441865538, 9731441865629, 9731441865680, 9731441865787, 9731441865934, 9731441866129, 9731441866523, 9731441866801, 9731441866920, 9731441867020, 9731441867095, 9731441867168, 9731441867925, 9731441868147, 9731441868723, 9731441944552, 9731441944828, 9731441945077, 9731441945167, 9731441945232, 9731441945544, 9731441945760, 9731449091613, 9731449091724, 9731449091832, 9731449091980, 9731449092092, 9731449092168, 9731449092268, 9731449092326, 9731449092408, 9731449092556, 9731449092682, 9731449092899, 9731449093003, 9731449093199, 9731449093378, 9731449093555, 9731449093832, 9731449093945, 9731449094058, 9731449094400, 9731449094766, 9731449094853, 9731449094903, 9731449095225, 9731449095342, 9731449095578, 9731449095903, 9731449096298, 9731449096664, 9731449096744, 9731449096834, 9731449097329, 9731449097458, 9731449097549, 9731449097649, 9731449097797, 9731449097899, 9731449097988, 9731449098094, 9731449098173, 9731449098900, 9731449099176, 9731449099655, 9731449099996, 9731449100163, 9731449100398, 9731449100621, 9731449100733, 9731449100895, 9731449101080, 9731449101208, 9731449101674, 9731449102060, 9731449102329, 9731449102507);
	
	
	$locData = $object->selQRYMultiple('i.location_id, l.location_title, i.inspection_id', 'project_inspections AS i, project_locations AS l', 'i.is_deleted = 0 AND i.inspection_id IN ('.join(',', $inspArr).') AND l.location_id = i.location_id', 'Y');
	
	$inspNameArr = array();
	foreach($locData as $lData){
		$inspNameArr[$lData['inspection_id']] = $lData['location_title'].' - '.$lData['inspection_id'];
	}

	$inspData = $object->selQRYMultiple('graphic_id, graphic_name, graphic_type, inspection_id', 'inspection_graphics', 'is_deleted = 0 AND inspection_id IN ('.join(',', $inspArr).')');
	$imgData = array(); $dwgData = array();
	foreach($inspData as $inData){
		//Stat Download files
		if($inData['graphic_type'] == 'drawing'){
			$dwgData[] = array($inData['graphic_name'], $inspNameArr[$inData['inspection_id']].'-'.$inData['graphic_name']);
		}else{
			$imgData[] = array($inData['graphic_name'], $inspNameArr[$inData['inspection_id']].'-'.$inData['graphic_name']);
		}
	}
	///var/www/defectid.com/buxton/inspections/copyfile
	$source = "./inspections/photo/";
	$destination = "./inspections/copyfile/";
	foreach($imgData as $img){
		echo $img.'<br />'.$source.$img[0].'+++++++++++'.$destination.$img[1];
		copy($source.$img[0], $destination.$img[1]);
	}
	
}die('Fetch files');


if($_REQUEST['task'] == 'updateIssuedTO'){echo '<pre>';
	$locData = $object->selQRYMultiple('issued_to_inspections_id, issued_to_name', 'issued_to_for_inspections', 'issued_to_name LIKE "% (%" and is_deleted = 0 order by issued_to_name,  last_modified_date ');
	foreach($locData as $lData){
		$issuedtoNameArr = explode(' (', $lData['issued_to_name']);
		echo $issuedtoNameArr[0];
		$updateQRY = "UPDATE `issued_to_for_inspections` SET  `issued_to_name` =  '".$issuedtoNameArr[0]."' WHERE `issued_to_inspections_id` = ".$lData['issued_to_inspections_id'];
		mysql_query($updateQRY);
		echo $updateQRY.'<br>';
	}
	
	#print_r($locData);
}die;

if($_REQUEST['task'] == 'location_tree'){
	$locData = $object->selQRYMultiple('location_id, location_title, location_parent_id', 'project_locations', 'is_deleted in (0, 1)');
	
	foreach($locData as $lData){
		$locIdTree = $object->subLocationsIDS($lData['location_id'], ' > ');
		$locNameTree = $object->subLocations($lData['location_id'], ' > ');
		echo $query = 'UPDATE project_locations SET location_id_tree = "'.$locIdTree.'", location_name_tree = "'.$locNameTree.'", last_modified_date = NOW() WHERE location_id = '.$lData['location_id'];
		mysql_query($query);
	}
}die;



if ($_REQUEST['task'] == 'correct_location_breadcrumb'){
	$locations = $object->selQRYMultiple('location_id, inspection_id', 'project_inspections', 'is_deleted=0');

	if(!empty($locations)){
		foreach($locations as $row){
			$location_id = $row["location_id"];
			$inspection_id = $row["inspection_id"];
			$locations = $object->subLocations($location_id, ' > ');
			$query = "UPDATE project_inspections SET
							inspection_location = '".$locations."',
							last_modified_date = NOW()
						WHERE
							inspection_id = ".$inspection_id." AND
							location_id = ".$location_id;
			mysql_query ($query);
		}
	}
}

//Code for Set Permissions start here
if($_REQUEST['task'] == 'permission'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	
	foreach($ids as $id){
		//for($i=0;$i<sizeof($managerPermissionArray);$i++){
			echo $permissionQry = "INSERT INTO user_permission SET
										user_id = '".$id['user_id']."',
										permission_name = 'web_report_sub_contractor_report',
										is_allow = '1',
										created_by = '0',
										created_date = NOW(),
										created_by = '0',
										created_date = NOW()";
			mysql_query($permissionQry);
		//}
	}
	die;
	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	
	foreach($ids as $id){
		for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
			echo $permissionQry = "INSERT INTO user_permission SET
										user_id = '".$id['user_id']."',
										permission_name = '".$keyInspectorPermissionArray[$i]."',
										is_allow = '".$inspectorPermissionArray[$keyInspectorPermissionArray[$i]]."',
										created_by = '0',
										created_date = NOW(),
										last_modified_by = '0',
										last_modified_date = NOW()";
			mysql_query($permissionQry);
		}
	}
}

//Code for Insert row in projects table start here

if($_REQUEST['task'] == 'projects'){
	$projects = $object->selQRYMultiple('project_id, pro_code, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, created_date, created_by, resource_type, is_deleted', 'user_projects', 'is_deleted>=0');
	
#	print_r($projects);die;
	
	foreach($projects as $proj){
		echo $projectQry = "INSERT INTO projects SET
								pro_code = '".$proj['pro_code']."',
								project_id = '".$proj['project_id']."',
								project_name = '".$proj['project_name']."',
								project_type = '".$proj['project_type']."',
								project_address_line1 = '".$proj['project_address_line1']."',
								project_address_line2 = '".$proj['project_address_line2']."',
								project_suburb = '".$proj['project_suburb']."',
								project_state = '".$proj['project_state']."',
								project_postcode = '".$proj['project_postcode']."',
								project_country = '".$proj['project_country']."',
								is_deleted = '".$proj['is_deleted']."',
								created_date = NOW(),
								created_by = 0,
								last_modified_by = '0',
								last_modified_date = NOW()";
				mysql_query($projectQry);
	}
}

if($_REQUEST['task'] == 'default_issue_to'){
	$projects = $object->selQRYMultiple('project_id', 'projects', 'is_deleted>=0');
	
#	print_r($projects);die;
	
	foreach($projects as $proj){
		echo $projectQry = "INSERT INTO inspection_issue_to SET
								project_id = '".$proj['project_id']."',
								issue_to_name = 'NA',
								last_modified_date = NOW(),
								last_modified_by = 0,
								created_date = NOW(),
								is_deleted=0,
								created_by = 0";
				mysql_query($projectQry);
	}
}

if($_REQUEST['task'] == 'set_project_permision'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	
	foreach($ids as $id){
		$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
		foreach($project_ids as $pId){
			for($i=0;$i<sizeof($managerPermissionArray);$i++){
				echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyManagerPermissionArray[$i]."',
											is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."',
											created_by = '0',
											project_id = '".$pId['project_id']."',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
				mysql_query($permissionQry);
			}
		}
		$project_ids = array();
	}
	
	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	
	foreach($ids as $id){
		$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
		foreach($project_ids as $pId){
			for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
				echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyInspectorPermissionArray[$i]."',
											is_allow = '".$inspectorPermissionArray[$keyInspectorPermissionArray[$i]]."',
											created_by = '0',
											project_id = '".$pId['project_id']."',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
				mysql_query($permissionQry);
			}
		}
	}
}

//Code for Set Permissions start here
if($_REQUEST['task'] == 'permission_project'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);	
	$projectWisePermissions = array(
		'web_edit_inspection',
		'web_delete_inspection',
		'web_close_inspection',
		'iPad_add_inspection',
		'iPad_edit_inspection',
		'iPad_delete_inspection',
		'iPad_close_inspection',
		'iPhone_add_inspection',
		'iPhone_close_inspection',
		'web_checklist'
	);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	
	foreach($ids as $id){
		for($i=0;$i<sizeof($managerPermissionArray);$i++){
			if(in_array($keyManagerPermissionArray[$i], $projectWisePermissions)){
				$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
				if($project_ids[0]['project_id'] != ''){
					foreach($project_ids as $pId){
						echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyManagerPermissionArray[$i]."',
											is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."',
											created_by = '0',
											project_id = '".$pId['project_id']."',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
	echo '<br />';
							mysql_query($permissionQry);
					}
				}
			}else{
				echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyManagerPermissionArray[$i]."',
											is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."',
											created_by = '0',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
echo '<br />';
#				mysql_query($permissionQry);
			}
		}
	}

	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	
	foreach($ids as $id){
		for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
			if(in_array($keyInspectorPermissionArray[$i], $projectWisePermissions)){
				$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
				if($project_ids[0]['project_id'] != ''){
					foreach($project_ids as $pId){
						echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyInspectorPermissionArray[$i]."',
											is_allow = '".$inspectorPermissionArray[$keyInspectorPermissionArray[$i]]."',
											created_by = '0',
											project_id = '".$pId['project_id']."',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
	echo '<br />';
						mysql_query($permissionQry);
					}
				}
			}else{
				echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$keyInspectorPermissionArray[$i]."',
											is_allow = '".$inspectorPermissionArray[$keyInspectorPermissionArray[$i]]."',
											created_by = '0',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
echo '<br />';
#				mysql_query($permissionQry);
			}
		}
	}
}

if($_REQUEST['task'] == 'set_userid_export'){
	$exp = $object->selQRYMultiple('export_files_id, path', 'exportData', 'created_date >= "1970-01-01 00:00:00"');
	foreach($exp as $exportData){
		$pathData = explode('/', $exportData['path']);
		echo $exportData['path'].'<br />';
echo 		$updateQry = 'UPDATE exportData SET userid = "'.$pathData[3].'", last_modified_date = NOW() WHERE export_files_id = "'.$exportData['export_files_id'].'"';
	mysql_query($updateQry);
	}
}

if($_REQUEST['task'] == 'set_single_permission'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);	
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	foreach($ids as $id){
		echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$_REQUEST['permission_name']."',
											is_allow = '".$managerPermissionArray[$_REQUEST['permission_name']]."',
											created_by = '0',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
echo '<br />';
				mysql_query($permissionQry);
	}

	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	
	foreach($ids as $id){
		echo $permissionQry = "INSERT INTO user_permission SET
											user_id = '".$id['user_id']."',
											permission_name = '".$_REQUEST['permission_name']."',
											is_allow = '".$inspectorPermissionArray[$_REQUEST['permission_name']]."',
											created_by = '0',
											created_date = NOW(),
											last_modified_by = '0',
											last_modified_date = NOW()";
echo '<br />';
				mysql_query($permissionQry);
	}
}

if($_REQUEST['task'] == 'insert_sync_data'){
	$project_ids = $object->selQRYMultiple('project_id', 'projects', 'is_deleted IN (0, 2, 1)');
	count($project_ids);
	foreach($project_ids as $pId){	
echo		$ipadQuery = "INSERT INTO sync_permission SET no_of_days = '100000', status = '\'All Open\'', project_id = '".$pId['project_id']."', device_type = 'iPad', created_by = '0', created_date = NOW(), last_modified_date = NOW(), last_modified_by = '0'";
		mysql_query($ipadQuery);
		
echo		$iphoneQuery = "INSERT INTO sync_permission SET no_of_days = '100000', status = '\'All Open\'', project_id = '".$pId['project_id']."', device_type = 'iPhone', created_by = '0', created_date = NOW(), last_modified_date = NOW(), last_modified_by = '0'";
		mysql_query($iphoneQuery);
	}
}

if($_REQUEST['task'] == 'correct_standard'){
	$standard = $object->selQRYMultiple('standard_defect_id, tag', 'standard_defects', 'is_deleted = 0 and tag != ""');
	foreach($standard as $st){
echo		$qry = "UPDATE standard_defects SET tag = '".trim($st['tag'], ';').";', last_modified_date = NOW() WHERE standard_defect_id = ".$st['standard_defect_id'];
mysql_query($qry);

	}
}

if($_REQUEST['task'] == 'tag_update'){
	$drawingTag = $object->selQRYMultiple('draw_mgmt_images_id, draw_mgmt_images_tags', 'draw_mgmt_images', 'is_deleted = 0 and draw_mgmt_images_tags != ""');
/*	function addSpaces($tagEle){ if($tagEle != ''){ return(" ".trim($tagEle)); } }*/
	function removeSpaces($tagEle){ if($tagEle != ''){ return(trim($tagEle)); } }
	foreach($drawingTag as $dtag){
		$drawTagsTemp = $dtag['draw_mgmt_images_tags'];
		$tagAr = explode(';', $drawTagsTemp);
		$spTagArr = array_map("removeSpaces", $tagAr);

		$drawTags = implode(';', $spTagArr);
		$drawTags = trim($drawTags, ";");
		if($drawTags != ""){
			$drawTags = $drawTags.';';
		}
		$qry = "UPDATE draw_mgmt_images SET draw_mgmt_images_tags = '".$drawTags."', last_modified_date = NOW() WHERE draw_mgmt_images_id = ".$dtag['draw_mgmt_images_id'];
echo	$qry.';';
echo '<br />';
		mysql_query($qry);

	}
}

if($_REQUEST['task'] == 'set_subcontractor_permission'){
	$userType = $_REQUEST['userType'];//manager or inspector
	$editPerm = $inspectorPermissionArray['iPhone_edit_inspection'];
	$parEditPerm = $inspectorPermissionArray['iPhone_edit_inspection_partial'];
	
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` = "'.$userType.'"');
	foreach($ids as $id){
		$proData = $object->selQRYMultiple('project_id, user_role', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
		foreach($proData as $pData){
			if($pData['user_role'] == 'Sub Contractor'){
				$permissionQry = "INSERT INTO user_permission (user_id, project_id, permission_name, is_allow, created_by, created_date, last_modified_by, last_modified_date) VALUES ('".$id['user_id']."', '".$pData['project_id']."', 'iPhone_edit_inspection', 0, 0, NOW(), 0, NOW()), ('".$id['user_id']."', '".$pData['project_id']."', 'iPhone_edit_inspection_partial', 1, 0, NOW(), 0, NOW())";
			}else{
				$permissionQry = "INSERT INTO user_permission (user_id, project_id, permission_name, is_allow, created_by, created_date, last_modified_by, last_modified_date) VALUES ('".$id['user_id']."', '".$pData['project_id']."', 'iPhone_edit_inspection', '".$editPerm."', 0, NOW(), 0, NOW()), ('".$id['user_id']."', '".$pData['project_id']."', 'iPhone_edit_inspection_partial', '".$parEditPerm."', 0, NOW(), 0, NOW())";
			}
			#echo $permissionQry;
			mysql_query($permissionQry);
		}
	}
}

if($_REQUEST['task'] == 'update_fixedbydays'){
	$tableName = $_REQUEST['tableName'];//manager or inspector
	if($tableName == 'check_list_items'){
		$tableData = $object->selQRYMultiple('check_list_items_id, issued_to', 'check_list_items', 'is_deleted IN (0, 1)');
		foreach($tableData as $tData){
			if(trim($tData['issued_to']) == ''){
echo				$qry = "UPDATE check_list_items SET fix_by_days = 0, last_modified_date = NOW() WHERE check_list_items_id = ".$tData['check_list_items_id'];
			}else{
	echo			$qry = "UPDATE check_list_items SET fix_by_days = 3, last_modified_date = NOW() WHERE check_list_items_id = ".$tData['check_list_items_id'];
			}
			mysql_query($qry);
		}
	}
	
	if($tableName == 'standard_defects'){
		$tableData = $object->selQRYMultiple('standard_defect_id, issued_to', 'standard_defects', 'is_deleted IN (0, 1)');
		foreach($tableData as $tData){
			if(trim($tData['issued_to']) == ''){
		echo		$qry = "UPDATE standard_defects SET fix_by_days = 0, last_modified_date = NOW() WHERE standard_defect_id = ".$tData['standard_defect_id'];
			}else{
			echo	$qry = "UPDATE standard_defects SET fix_by_days = 3, last_modified_date = NOW() WHERE standard_defect_id = ".$tData['standard_defect_id'];
			}
			mysql_query($qry);
		}
	}
}


if($_REQUEST['task'] == 'set_single_permission_projectwise'){
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "manager"');
	$keyManagerPermissionArray = array_keys($managerPermissionArray);	
	foreach($ids as $id){
		$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
		
		if($project_ids[0]['project_id'] != ''){
			foreach($project_ids as $pId){
				echo $permissionQry = "INSERT INTO user_permission SET
									user_id = '".$id['user_id']."',
									permission_name = '".$_REQUEST['permission_name']."',
									is_allow = '".$managerPermissionArray[$_REQUEST['permission_name']]."',
									created_by = '0',
									project_id = '".$pId['project_id']."',
									created_date = NOW(),
									last_modified_date = NOW()";
				echo '<br />';
				mysql_query($permissionQry);
			}
		}	
	}
	$ids = array();
	$keyInspectorPermissionArray = array_keys($inspectorPermissionArray);
	$ids = $object->selQRYMultiple('user_id', 'user', 'is_deleted = 0 and `user_type` =  "inspector"');
	foreach($ids as $id){
		$project_ids = $object->selQRYMultiple('project_id', 'user_projects', 'is_deleted = 0 and user_id = "'.$id['user_id'].'"');
		if($project_ids[0]['project_id'] != ''){
			foreach($project_ids as $pId){
				echo $permissionQry = "INSERT INTO user_permission SET
									user_id = '".$id['user_id']."',
									permission_name = '".$_REQUEST['permission_name']."',
									is_allow = '".$inspectorPermissionArray[$_REQUEST['permission_name']]."',
									created_by = '0',
									project_id = '".$pId['project_id']."',
									created_date = NOW(),
									last_modified_date = NOW()";
				echo '<br />';
				mysql_query($permissionQry);
			}
		}
	}
}

if ($_REQUEST['task'] == 'correct_location_breadcrumb_promon_name'){
	$locations = $object->selQRYMultiple('sub_location_id, progress_id', 'progress_monitoring', 'is_deleted IN (1, 0)');
	if(!empty($locations)){
		foreach($locations as $row){
			$location_id = $row["sub_location_id"];
			$progress_id = $row["progress_id"];
			$locationsStr = $object->subLocationsProgressMonitoring_update($location_id, ' > ');
echo			$query = "UPDATE progress_monitoring SET location_tree_name = '".$locationsStr."', last_modified_date = NOW() WHERE progress_id = ".$progress_id." AND sub_location_id = ".$location_id;
echo '<br />';
			mysql_query ($query);
		}
	}
}
if ($_REQUEST['task'] == 'correct_location_breadcrumb_promon_id'){
	$locations = $object->selQRYMultiple('sub_location_id, progress_id', 'progress_monitoring', 'is_deleted IN (1, 0)');
	if(!empty($locations)){
		foreach($locations as $row){
			$location_id = $row["sub_location_id"];
			$progress_id = $row["progress_id"];
			$locationsStr = $object->subLocationsProgressMonitoring_ids($location_id, ' > ');
echo			$query = "UPDATE progress_monitoring SET location_tree = '".$locationsStr."', last_modified_date = NOW() WHERE progress_id = ".$progress_id." AND sub_location_id = ".$location_id;
echo '<br />';
			mysql_query ($query);
		}
	}
}

if($_REQUEST['task'] == 'set_default_sync_locations'){
	$projects = $object->selQRYMultiple('project_id', 'projects', 'is_deleted>=0');
	foreach($projects as $pro){
		$updateQry = "UPDATE sync_permission SET
						location_ids = 'Select All',
						last_modified_date =  NOW()
					WHERE
						project_id = ".$pro['project_id'];
		mysql_query($updateQry);
	}
}

if($_REQUEST['task'] == 'location_tree_promon'){
	$locData = $object->selQRYMultiple('location_id, location_title, location_parent_id', 'project_monitoring_locations', 'is_deleted in (0, 1)');
	foreach($locData as $lData){
		$locNameTree = $object->promon_sublocationParent($lData['location_id'], ' > ');
		echo $query = 'UPDATE project_monitoring_locations SET location_tree_name = "'.$locNameTree.'", last_modified_date = NOW() WHERE location_id = '.$lData['location_id'];
		mysql_query($query);
	}
}
if($_REQUEST['task'] == 'location_tree_qa'){
	$locData = $object->selQRYMultiple('location_id, location_title, location_parent_id', 'qa_task_locations', 'is_deleted in (0, 1)');
	foreach($locData as $lData){
		$locNameTree = $object->qa_sublocationParent($lData['location_id'], ' > ');

		echo $query = 'UPDATE qa_task_locations SET location_tree_name = "'.$locNameTree.'", last_modified_date = NOW() WHERE location_id = '.$lData['location_id'];
		mysql_query($query);
	}
}

if($_REQUEST['task'] == 'default_leave'){
	$projects = $object->selQRYMultiple('project_id', 'projects', 'is_deleted>=0');

	$defaultLeave = $object->selQRYMultiple('date, leave_type, reason, is_leave', 'public_holidays', 'is_deleted = 0');

	foreach($projects as $pro){
	
		foreach($defaultLeave as $val){
echo			$insertQry = "INSERT INTO project_leave SET 
								project_id = '".$pro['project_id']."', 
								date = '".$val['date']."',
								leave_type = '".$val['leave_type']."',
								reason = '".$val['reason']."',
								is_leave = '".$val['is_leave']."',
								created_date = NOW(),
								last_modified_date = NOW(),
								created_by = 0,
								last_modified_by = 0";
			mysql_query($insertQry);
		}
	}
}

if($_REQUEST['task'] == 'project_drawing_thumbnail'){
	$object->resizeImages('./project_drawings/'.$_GET['project_id'].'/'.$_GET['file_name'], 150, 150, './project_drawings/'.$_GET['project_id'].'/thumbnail/thumb_'.$_GET['file_name']);
}
?>