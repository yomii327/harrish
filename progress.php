<?php 

session_start();
require_once'includes/functions.php';

$proID=isset($_REQUEST['proID'])?$_REQUEST['proID']:'';

if(isset($proID) && !empty($proID)){
	SearchInpection($proID,trim($_REQUEST['type']));
}else{
	$selectBox = '<select class="select_box" name="location" id="location" class="select_box" onchange="SubLoc(this.value);"><option value="">Select</option></select>';
	$type = trim($_REQUEST['type']);
	switch($type){
		
		case "sublocation": $selectBox = '<select class="select_box" name="sublocation" onChange="SubLoc_sub(this.value);" id="sublocation"><option value="">Select</option></select>';
		break;
		
		case "SubLoc_sub": $selectBox = '<select class="select_box" name="SubLoc_sub" onChange="SubLoc_sub(this.value);" id="SubLoc_sub"><option value="">Select</option></select>';
		break;

		case "issuedTo": $selectBox = '<select class="select_box" name="issuedTo" id="issuedTo"><option value="">Select</option></select>';
		break;
	}
	echo $selectBox;
}

function SearchInpection($proID,$type){
	switch($type){
		case "location": $q="select location_id, location_title	from project_monitoring_locations where project_id='$proID' and location_parent_id=0 and is_deleted=0 GROUP BY location_title"; 
		$name='name="location" id="location" '; SelectListWithID($q, $name);
		break;
	
		case "sublocation": $s="select location_id, location_title	from project_monitoring_locations where location_parent_id 	='$proID' and is_deleted=0 GROUP BY location_title"; 
		$name='name="sublocation" onChange="SubLoc_sub(this.value);resetSelectBoxSubLoc();" id="sublocation" '; SelectSubLocation($s, $name, 'sublocation');
		break;
		
		case "SubLoc_sub": $s="select location_id, location_title from project_monitoring_locations where location_parent_id 	='$proID' and is_deleted=0 GROUP BY location_title"; 
		$name='name="SubLoc_sub" id="SubLoc_sub" '; SelectSubLocation($s, $name, 'SubLoc_sub');
		break;
		
		case "issuedTo": $q="select issued_to_name from issued_to_for_progress_monitoring where project_id='$proID' and is_deleted = '0' GROUP BY issued_to_name";
		$name='name="issuedTo" id="issuedTo"'; SelectListWithoutID($q, $name);
		break;
		
		case "setSession":
		$_SESSION['projIdPM'] = $proID;
		break;
	}
}

function SelectListWithID($q, $name){
	$obj = new DB_Class();
	$r=$obj->db_query($q);
	$data='<select '.$name.' class="select_box"  onchange="SubLoc(this.value);resetSelectBoxLoc();"' ;
	if(!isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] != 1){	
	}
	$data .= '>
	<option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.urlencode($row[0]).'"';
		if($_SESSION['pm']['location'] == $row[0]){
			$data.='selected="selected"';
		}
		$data.='>'.$row[1].'</option>'; 
	}
	echo $data.='</select>';
}

function SelectSubLocation($s, $name, $sessionType){
	$obj = new DB_Class();
	$r = $obj->db_query($s);
	$data='<select '.$name.' class="select_box"';
	if(!isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] != 1){}
	$data .= '>
	<option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.urlencode($row[0]).'"';
		if($sessionType == 'sublocation'){
			if($_SESSION['pm']['sublocation'] == $row[0]){
				$data.='selected="selected"';
			}
		}
		if($sessionType == 'SubLoc_sub'){
			if($_SESSION['pm']['subLocation_sub'] == $row[0]){
				$data.='selected="selected"';
			}
		}	
		$data.='>'.$row[1].'</option>'; 
	}
	echo $data.='</select>';
}

function SelectListWithoutID($q, $name){
	$obj = new DB_Class();
	
	$r=$obj->db_query($q);
	$data = '<select '.$name.' class="select_box"';
	if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0 || isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1){	// inspector
		$data .= '';
	}
	else if(isset($_SESSION['ww_is_company'])){	// inspector
		$data .= '';
	}
	$data .= '>
	<option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.urlencode($row[0]).'"';
		if($_SESSION['pm']['issuedTo'] == $row[0]){
			$data.='selected="selected"';
		}
		$data.='>'.$row[0].'</option>'; 
	}
	echo $data.='</select>';
}?>
