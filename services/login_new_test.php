<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();
//Header Secttion for include and objects 
if(isset($_REQUEST['check_login'])){
	$first_qry_lastId = '';
	$userName = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['username']))));
	$password = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['password']))));
	$first_login = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['first_login']))));
	if (!isset($_REQUEST['version'])){
		$output = array(
			'status' => false,
			'message' => 'Please upgrade the the new version. !',
			'data' => ''
		);
		echo '['.json_encode($output).']';
		die;
	}

	if ($_REQUEST['first_login'] == '1'){
		$first_qry = "INSERT INTO unique_id set created_date = now()";
		if(mysql_query($first_qry)){
			$first_qry_lastId = mysql_insert_id();
		}
	}

	$select = "user_name, user_fullname, user_phone_no, user_email, user_password, user_logo_image, company_name, user_type, last_modified_date, last_modified_by, created_date, created_by, resource_type, user_id as global_id, user_hash, company_id";
	
	$where = "user_name = '$userName' and user_password = '$password' and is_deleted=0";

	$id = $db->selQRY($select, 'user', $where);

	$comData = explode(',', $id['company_id']);
	$comDataArr='';
	$logoPath = '';
	if(isset($comData[0]) && !empty($comData[0])){
		$comDataVal = mysql_query("SELECT * FROM organisations WHERE is_deleted = 0 AND id = ".$comData[0]);
		if(mysql_num_rows($comDataVal) > 0){
			while($ROW = mysql_fetch_assoc($comDataVal)){
				$comDataArr[]= $ROW;
			}
			if(isset($comDataArr[0]['logo']) && !empty($comDataArr[0]['logo'])){
				$logoPath = "http://harrishmcdev.defectid.com/company_logo/".$comDataArr[0]['logo'];
				if(file_exists($logoPath)){
					$logoPath = '';
				}
			}
		}
	}
	#echo "<pre>"; print_r($logoPath); die;

	if($id)
		$id["company_name"] = "harrishmc";

	// $select = "DISTINCT project_id, project_name";
	// $where = "user_id = ".$id['global_id']." and is_deleted=0";
	// $projectId = $db->selQRYMultiple($select, 'user_projects', $where);
	// echo "<pre>"; print_r($projectId); die;

	$projectIdData = mysql_query("SELECT DISTINCT up.project_id, up.project_name FROM user_projects AS up JOIN projects AS p ON p.project_id=up.project_id WHERE up.user_id = ".$id['global_id']." AND up.is_deleted=0 AND p.is_deleted=0 AND p.project_is_synced = 'Yes'");
	$projectId = array();
	if(mysql_num_rows($projectIdData) > 0){
		while($ROW = mysql_fetch_assoc($projectIdData)){
			$projectId[]= $ROW;
		}
		//echo "<pre>"; print_r($projectId1); die;
	}
	//die;
	if($id){
		$companyLogo = $db->selQRY("company_logo", "pms_companies", "active = '1'");

		$id['user_logo_image'] = REPORTLOGO."/".$companyLogo['company_logo'];

		$rs = $db->selQRY("MAX( inspection_id ) AS count", "user_projects up, project_inspections pi", "up.user_id =" . $id["global_id"] . " AND up.project_id = pi.project_id");
		$count = 0;
		if ($rs["count"] != null){
			$count = $rs["count"];
		}
		$id['last_inspection_id'] = $count;
		if($first_qry_lastId != ''){
			$id['login_unique_id'] = $first_qry_lastId;
		}
		
		$id['projectIDs'] = $projectId;
		$id['logo_path'] = $logoPath;
		
		$output = array(
			'status' => true,
			'message' => 'Login Successfull !',
			'data' => $id
		);
		echo '['.json_encode($output).']';
	}else{
		$output = array(
			'status' => false,
			'message' => 'Login Fail !',
			'data' => ''
		);
		echo '['.json_encode($output).']';
	}
}
?>