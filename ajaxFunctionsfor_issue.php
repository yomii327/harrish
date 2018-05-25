<?php 

session_start();
require_once'includes/functions.php';
$proID=isset($_REQUEST['proID'])?$_REQUEST['proID']:'';

if(isset($proID) && !empty($proID)){
	SearchInpection($proID,trim($_REQUEST['type']));
}else{
	SearchInpection_all();
}

function SearchInpection($proID,$type){
	switch($type){
		
		
		case "issuedTo": $q="select issued_to_name from ".PROJECTISSUETO." where project_id='$proID' and is_deleted = '0' GROUP BY issued_to_name";
		$name='name="issuedTo" id="issuedTo"'; SelectListWithoutID($q,$name);
		break;
		
		
	}
}
function SearchInpection_all()
{
	$q="select issued_to_name from issued_to_for_inspections where created_by=".$_SESSION['ww_is_builder']." and is_deleted = '0' GROUP BY issued_to_name";
		$name='name="issuedTo" id="issuedTo"'; SelectListWithoutID($q,$name);
	
	
}


function SelectListWithoutID($q,$name){
	$obj = new DB_Class();
	
	$r=$obj->db_query($q);
	$data = '<select '.$name.' class="select_box"';
	
	if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0 || isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1){	// inspector
		$data .= 'style="width:150px;font-family:Arial, Helvetica, sans-serif;font-size:12px;"';
	}
	else if(isset($_SESSION['ww_is_company'])){	// inspector
		$data .= 'style="width:150px;font-family:Arial, Helvetica, sans-serif;font-size:12px;"';
	}
	
			$data .= '>
		  <option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.$row[0].'">'.$row[0].'</option>'; 
	}
	echo $data.='</select>';
}

function SelectListWithID($q,$name){
	$obj = new DB_Class();
	$r=$obj->db_query($q);
	$data='<select '.$name.' class="select_box"';
		if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0 || isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1){	// inspector
		$data .= 'style="width:150px;font-family:Arial, Helvetica, sans-serif;font-size:12px;"';
	}else if(isset($_SESSION['ww_is_company'])){	// inspector
		$data .= 'style="width:150px;font-family:Arial, Helvetica, sans-serif;font-size:12px;"';
	}
	
			$data .= '>
		  <option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.$row[0].'">'.$row[1].'</option>'; 
	}
	echo $data.='</select>';
}

function SelectListWithIDSP($q,$name){
	$obj = new DB_Class();
	$r=$obj->db_query($q);
	$data='<select '.$name.' class="select_box"';
		if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0 || isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1){	// inspector
		$data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
	}else if(isset($_SESSION['ww_is_company'])){	// inspector
		$data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
	}
	
			$data .= '>
		  <option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.$row[0].'">'.$row[1].'</option>'; 
	}
	echo $data.='</select>';
}

?>
