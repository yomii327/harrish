<?php
//Header Secttion for include and objects 
include_once("sqlInjection.php");
include_once("servicesQurey.php");
$db = new QRY_Class();
//Header Secttion for include and objects 


if(isset($_REQUEST['check_login'])){
$first_qry_lastId = '';
	$userName = queryFilter(trim(strip_tags(addslashes($_REQUEST['username']))));
	$password = queryFilter(trim(strip_tags(addslashes($_REQUEST['password']))));
	$first_login = queryFilter(trim(strip_tags(addslashes($_REQUEST['first_login']))));
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
#	$password = md5($password);
	
	$select = "user_name, user_fullname, user_phone_no, user_email, user_password, user_logo_image, company_name, user_type, last_modified_date, last_modified_by, created_date, created_by, resource_type, user_id as global_id, user_hash";
	
	$where = "user_name = '$userName' and user_password = '$password' and is_deleted=0";
	$id = $db->selQRY($select, 'user', $where);

	$select = "project_id, project_name";
	$where = "user_id = ".$id['global_id']." and is_deleted=0";
	$projectId = $db->selQRYMultiple($select, 'user_projects', $where);
	if($id){
		$companyLogo = $db->selQRY("company_logo", "pms_companies", "active = '1'");

		$id['user_logo_image'] = "http://defectid.com/company_logo/".$companyLogo['company_logo'];

		$rs = $db->selQRY("MAX( inspection_id ) AS count", "user_projects up, project_inspections pi", "up.user_id =" . $id["global_id"] . " AND up.project_id = pi.project_id");
		$count = 0;
		if ($rs["count"] != null)
		{
			$count = $rs["count"];
		}
		$id['last_inspection_id'] = $count;
		if($first_qry_lastId != ''){
			$id['login_unique_id'] = $first_qry_lastId;
		}
		
		$id['projectIDs'] = $projectId;
		
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