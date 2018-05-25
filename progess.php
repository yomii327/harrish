<?php 

session_start();
require_once'includes/functions.php';

$proID=isset($_REQUEST['proID'])?$_REQUEST['proID']:'';

if(isset($proID) && !empty($proID)){
	
	
	SearchInpection($proID,trim($_REQUEST['type']));
	
}
else
{
	echo '<select name="location" id="location" class="select_box" onchange="SubLoc(this.value);">
					  <option value="">Select</option>
				    </select>';

	}

function SearchInpection($proID,$type){
 
  switch($type){
	 
	case "location": $q="select location_id, location_title	from project_monitoring_locations where project_id='$proID' and location_parent_id=0 and is_deleted=0 GROUP BY location_title"; 
	$name='name="location" id="location" '; SelectListWithID($q,$name);
	break;

/*	case "location": $q="select area_room from ".DEFECTS." where project_id='$proID' GROUP BY area_room"; 
	$name='name="location"'; SelectListLocation($q,$name);
	break;*/
	case "sublocation": $s="select location_id, location_title	from project_monitoring_locations where location_parent_id 	='$proID' and is_deleted=0 GROUP BY location_title"; 
//	echo $s;
	$name='name="sublocation" id="sublocation" '; SelectSubLocation($s,$name);
	
	break;
	
	case "issuedTo": $q="select issue_to_name from inspection_issue_to where project_id='$proID' and is_deleted = '0' GROUP BY issue_to_name";
		//echo $q; die;
		$name='name="issuedTo" id="issuedTo"'; SelectListWithoutID($q,$name);
		break;
	
}

}

function SelectListWithID($q,$name){
	
	$obj = new DB_Class();
	$r=$obj->db_query($q);
	$data='<select '.$name.' class="select_box"  onchange="SubLoc(this.value);"' ;
	if(!isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] != 1){	
		$data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
	}
			$data .= '>
		  <option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.$row[0].'">'.$row[1].'</option>'; 
	}
	echo $data.='</select>';
}

function SelectSubLocation($s,$name){
	
	$obj = new DB_Class();
	$r=$obj->db_query($s);
	
	
		$data='<select '.$name.' onchange="toggle1(this.value);" class="select_box"';
		if(!isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] != 1){	
			$data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
		}
				$data .= '>
			  <option value="">Select</option>';
		while ($row=mysql_fetch_array($r)) {		  
			$data.='<option value="'.$row[0].'">'.$row[1].'</option>'; 
		}
		$data.='<option value="show1" id="other" >Other</option>';
		echo $data.='</select>';
	
}

function SelectListWithoutID($q,$name){
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
		$data.='<option value="'.$row[0].'">'.$row[0].'</option>'; 
	}
	echo $data.='</select>';
}

?>
