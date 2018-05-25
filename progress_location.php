<?php 

session_start();
require_once'includes/functions.php';
$proID = isset($_REQUEST['proID']) ? $_REQUEST['proID'] : '';
$type = trim($_REQUEST['type']);
if(isset($proID) && !empty($proID)){
	SearchInpection($proID, $type);
}else{
	switch($type){
		case "location":
		$selectBox = '<SELECT name="location" id="location" class="select_box" onchange="SubLoc(this.value);"><option value="">Select</option></select>'; 
		break;

		case "sublocation1":
			$selectBox = '<SELECT name="sublocation[]" id="sublocation1" class="select_box" onchange="SubLoc1(this.value);"><option value="">Select</option></select>';
		break;
			
		case "sublocation2":
			$selectBox = '<SELECT name="sublocation[]" id="sublocation2" class="select_box" onchange="SubLoc2(this.value);"><option value="">Select</option></select>';
		break;
			
		case "sublocation3":
			$selectBox = '<SELECT name="sublocation[]" id="sublocation3" class="select_box" onchange="SubLoc3(this.value);"><option value="">Select</option></select>';
		break;
		
		//qa_task_locations
		case "qa_task_subLocation_sub1": 		
		$selectBox = '<SELECT name="sublocation1" id="sublocation1" class="select_box""><option value="">Select</option></select>';
		break;
		
		case "qa_task_subLocation_sub2": 		
		$selectBox = '<SELECT name="sublocation2" id="sublocation2" class="select_box""><option value="">Select</option></select>';
		break;
		
		
		case "qa_task_subLocation_sub3": 		
		$selectBox = '<SELECT name="sublocation3" id="sublocation3" class="select_box""><option value="">Select</option></select>';
		break;
		
	}
	echo $selectBox;
}

function SearchInpection($proID,$type){
	//echo  $type; die;
	switch($type){
		case "location": $q="SELECT location_id, location_title FROM project_monitoring_locations WHERE project_id='$proID' AND location_parent_id=0 AND is_deleted=0 GROUP BY location_title"; 
		$name='name="location" id="location" onchange="SubLoc(this.value);"'; SelectListWithID($q,$name);
		break;
		
		case "sublocation1":
			$s="SELECT location_id, location_title FROM project_monitoring_locations WHERE location_parent_id 	='$proID' AND is_deleted=0 GROUP BY location_title";
			$name='name="sublocation[]" id="sublocation1" onchange="SubLoc1(this.value);removeErrors();"';
			$text='value="otherLoc2"';
			SelectSubLocation($s, $name, $text);
		break;
			
		case "sublocation2":
			$s="SELECT location_id, location_title FROM project_monitoring_locations WHERE location_parent_id 	='$proID' AND is_deleted=0 GROUP BY location_title";
			$name='name="sublocation[]" id="sublocation2" onchange="SubLoc2(this.value);"';
			$text='value="otherLoc3"';
			SelectSubLocation($s, $name, $text);
		break;
			
		case "sublocation3":
			$s="SELECT location_id, location_title FROM project_monitoring_locations WHERE location_parent_id 	='$proID' AND is_deleted=0 GROUP BY location_title";
			$name='name="sublocation[]" id="sublocation3" onchange="SubLoc3(this.value);"';
			$text='value="otherLoc4"';
			SelectSubLocation($s, $name, $text);
		break;
		
		case "subLocation_sub": 
			$s="SELECT location_id, location_title FROM project_monitoring_locations WHERE location_parent_id ='$proID' AND is_deleted=0 GROUP BY location_title";
			$name='name="subLocation_sub" id="subLocation_sub" onchange="subLocate_other(this.value);"'; SelectSubSubLocation($s,$name);
		break;
		
		//qa_task_subLocation_sub	
		
		case "qa_task_subLocation_sub1": 
		$projectid = $_SESSION['idp'];
		$s="SELECT location_id, location_title FROM qa_task_locations WHERE location_parent_id ='$proID' AND project_id = '$projectid' AND is_deleted=0 GROUP BY location_title";
		$name='name="sublocation1" id="sublocation1" onchange="SubLoc2(this.value);"';
		SelectTaskSubSubLocation($s,$name);
		break;
		
		
		case "qa_task_subLocation_sub2": 
		$projectid = $_SESSION['idp'];
		$s="SELECT location_id, location_title FROM qa_task_locations WHERE location_parent_id ='$proID' AND project_id = '$projectid' AND is_deleted=0 GROUP BY location_title";
		$name='name="sublocation2" id="sublocation2" onchange="SubLoc3(this.value);"';
		SelectTaskSubSubLocation($s,$name);
		break;
		
		
		case "qa_task_subLocation_sub3": 
		$projectid = $_SESSION['idp'];
		$s="SELECT location_id, location_title FROM qa_task_locations WHERE location_parent_id ='$proID' AND project_id = '$projectid' AND is_deleted=0 GROUP BY location_title";
		$name='name="sublocation3" id="sublocation3" onchange="SubLoc4_other(this.value);"';
		SelectTaskSubSubLocation($s,$name);
		break;
	}
}

function SelectListWithID($q,$name){
	$obj = new DB_Class();
	$r=$obj->db_query($q);
	$data='<SELECT '.$name.' class="select_box" ' ;
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

function SelectSubLocation($s, $name, $text){
	$obj = new DB_Class();
	$r=$obj->db_query($s);
	$data='<SELECT '.$name.' class="select_box"';
	if(!isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] != 1){	
		$data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
	}
	$data .= '>
	<option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.$row[0].'">'.$row[1].'</option>'; 
	}
	$data.='<option '.$text.'>Other</option>';
	echo $data.='</select>';
}

function SelectSubSubLocation($s,$name){
	$obj = new DB_Class();
	$r=$obj->db_query($s);
	$data='<SELECT '.$name.' class="select_box"';
	if(!isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] != 1){	
		$data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
	}
	$data .= 'style="margin-left:0px;">
	<option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.$row[0].'">'.$row[1].'</option>'; 
	}
	$data.='<option value="other" id="other" >Other</option>';
	echo $data.='</select>';
}

function SelectTaskSubSubLocation($s,$name){
	$obj = new DB_Class();
	$r=$obj->db_query($s);
	$data='<SELECT '.$name.' class="select_box"';
	if(!isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] != 1){	
		$data .= 'style="width:220px;background-image:url(images/selectSpl.png);"';
	}
	$data .= 'style="margin-left:0px;">
	<option value="">Select</option>';
	while ($row=mysql_fetch_array($r)) {		  
		$data.='<option value="'.$row[0].'">'.$row[1].'</option>'; 
	}
	$data.='<option value="other" id="other" >Other</option>';
	echo $data.='</select>';
}
?>
