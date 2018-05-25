<?php
session_start();

set_time_limit(999999999999999);

include('../includes/commanfunction.php');
if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}
elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}

$obj = new COMMAN_Class();
$issued_to_add = '';

	if(!empty($_REQUEST['projName'])){
		$where=" and I.project_id='".$_REQUEST['projName']."'";
	}
   

    $contact_clause = '';
	if(!empty($_REQUEST['projName'])){
		$project_info = $obj->selQRYMultiple('defect_clause','projects','project_id = '.$_REQUEST['projName']);
		if(!empty($project_info)){
			$contact_clause = $project_info[0]['defect_clause'];
		}
	}
	
	/*if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
		$postCount++;
		$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['location'], ", ").")";
	}
	
	if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['sub_subLocation'])){
		$postCount++;
		$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['sub_subLocation'], ", ").")";
	}else{
		if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
			$postCount++;
			$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['subLocation'], ", ").")";
		}
	}
	*/

	$searchLocID = "";
	if(!empty($_REQUEST['location'])){
		$searchLocID = $_REQUEST['location'];
		#$where.=" and I.location_id in (".$obj->subLocationsId(, ", ").")";
	}
	if(!empty($_REQUEST['subLocation'])){
		$searchLocID = $_REQUEST['subLocation'];
		#$where.=" and I.location_id in (".$obj->subLocationsId(, ", ").")";
	}
	if(!empty($_REQUEST['sub_subLocation'])){
		$searchLocID = $_REQUEST['sub_subLocation'];
		#$where.=" and I.location_id in (".$obj->subLocationsId(, ", ").")";
	}

	if($searchLocID!=""){
		if(is_array($searchLocID)){
			$tempLocArr = array();
			for($g=0; $g<sizeof($searchLocID); $g++){
				$tempLocSecArr = explode(",", $obj->subLocationsId($searchLocID[$g], ","));
				$tempLocArr = array_merge($tempLocArr, $tempLocSecArr);
			}
			$where.=" AND I.location_id IN (".join(",", $tempLocArr).")";
		}else{
			$where.=" AND I.location_id IN (".$obj->subLocationsId($searchLocID, ",").")";
		}
	}
	
	if(!empty($_REQUEST['status'])){
		$postCount++;
		$where.=" and F.inspection_status='".$_REQUEST['status']."'";
	}
	
	if(!empty($_REQUEST['inspectedBy'])){
		$postCount++;
		$where.=" and I.inspection_inspected_by='".$_REQUEST['inspectedBy']."'";
	}
	
	if($_REQUEST['issuedTo']!=""){
		$postCount++;
		$isMul = explode('@@@', $_REQUEST['issuedTo']);
		$mulIssueToWhere = '';
		$mulIssueTo = '';
		$loopMul = count($isMul);
		for($g=0; $g<$loopMul; $g++){
			if($mulIssueToWhere == ""){
				$mulIssueTo = "'".$isMul[$g]."'";
				$mulIssueToWhere .= " (F.issued_to_name LIKE '".addslashes($isMul[$g])." (%' OR F.issued_to_name LIKE '".addslashes($isMul[$g])."') ";
			}else{
				$mulIssueTo .= ", '".$isMul[$g]."'";
				$mulIssueToWhere .= " OR  (F.issued_to_name LIKE '".addslashes($isMul[$g])." (%' OR F.issued_to_name LIKE '".addslashes($isMul[$g])."') ";
			}
		}
		$where.=" AND (".$mulIssueToWhere.") AND F.inspection_id = I.inspection_id";
	}
	
	if($_REQUEST['inspecrType']!=""){
		$postCount++;
		$where.=" and I.inspection_type='".$_REQUEST['inspecrType']."'";
	}
	
	if(!empty($_REQUEST['costAttribute'])){
		$postCount++;
		$where.=" and F.cost_attribute = '".$_REQUEST['costAttribute']."'";
	}
	
	if(!empty($_SESSION['userRole'])){
		if($_SESSION['userRole'] != 'All Defect'){
			$where.=" and I.inspection_raised_by = '".$_SESSION['userRole']."'";
		}else{
			$postCount++;
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and I.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
		}
	}else{
		$postCount++;
		if($_REQUEST['raisedBy'] != 'All Defect')
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and I.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
	}

	if($_REQUEST['DRF']!="" && $_REQUEST['DRT']!=""){
		$postCount++;
		$or.=" I.inspection_date_raised between '".date('Y-m-d', strtotime($_REQUEST['DRF']))."' and '".date('Y-m-d', strtotime($_REQUEST['DRT']))."'";
	}
	
	if($_REQUEST['DRF']!="" && $_REQUEST['FBDF']!=""){$or.=" and";}
	
	if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
		$postCount++;
		$or.=" F.inspection_fixed_by_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
	}
	
	if(!empty($_REQUEST['searchKeyward'])){
		$postCount++;
		$where.=" and I.inspection_location LIKE '%".$_REQUEST['searchKeyward']."%'";
	}
	

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
	
	$orderby = "";	
	if ($_REQUEST["sortby"]){
		if ($_REQUEST["sortby"] == "location_id")
			$orderby .= ", I.inspection_location";
		else if ($_REQUEST["sortby"] == "issued_to_name")
			$orderby .= ",  F.issued_to_name";
		else
			$orderby .= ", I.".$_REQUEST["sortby"];
	}

	$qi = "SELECT
		P.project_name AS Project,
		I.inspection_id AS InspectionId,
		I.location_id AS Location,
		I.inspection_date_raised AS DateRaised,
		I.inspection_inspected_by AS InspectedBy,
		I.inspection_type AS InspectonType,
		I.inspection_raised_by AS RaisedBy,
		I.inspection_issued_to AS issuetoName,
		I.inspection_fixed_by_date AS fixedbydate,
		I.inspection_status AS stats,
		I.inspection_description AS Description,
		I.inspection_notes AS Note,
		I.cost_attribute AS costAttribute,
		I.cost_attribute AS costImpactType,
		I.cost_attribute AS costImpactPrice
	FROM
		user_projects as P,
		project_inspections as I
	WHERE
		I.project_id = P.project_id AND I.is_deleted = '0' " . $where . " GROUP BY I.inspection_id " . $orderby;

#$query = sprintf($qi);
$result = mysql_query($qi);
$output = '';

$fileName = 'Report_'.microtime().'.csv';
$noofRecord = mysql_num_rows($result);


if($noofRecord > 0){
//Header Section Start Here
	$header = array('Project', 'Inspection Id', 'Location', 'Date Raised', 'Inspected By', 'Inspecton Type', 'Raised By', 'Issue To', 'Fixed By Date', 'Cost Attribute', 'Status', 'Description', 'Note', 'Cost Impact', 'Cost Impact Price');
    #echocsv(array_keys($row));
	$output .= echocsv($header);
//Header Section End Here
//Data Collection Start Here
	$inspArr = array();
	$locidsArr = array();
	while($row = mysql_fetch_assoc($result)){	
		$inspArr[] = $row['InspectionId'];
		$locidsArr[] = $row['Location'];
	}
	mysql_data_seek($result, 0);
	
	$locationTree = array();
	$locationTreeData = $obj->selQRYMultiple('location_id, location_title, location_name_tree', 'project_locations', 'is_deleted = 0 AND project_id IN ('.$_REQUEST['projName'].') AND location_id IN ('.join(',', $locidsArr).')');
	foreach($locationTreeData as $locData){
		$locationTree[$locData['location_id']] = $locData['location_name_tree'];
	}
	
	$issueToNameData = array();
	$issueToData = $obj->selQRYMultiple('inspection_id, GROUP_CONCAT(issued_to_name SEPARATOR " > ") AS issuetoName, GROUP_CONCAT(inspection_fixed_by_date SEPARATOR " > ") AS fixedbydate, GROUP_CONCAT(inspection_status SEPARATOR " > ") AS stats, GROUP_CONCAT(cost_attribute SEPARATOR " > ") AS costAttribute, GROUP_CONCAT(cost_impact_type SEPARATOR " > ") AS costImpactType, GROUP_CONCAT(cost_impact_price SEPARATOR " > ") AS costImpactPrice', 'issued_to_for_inspections', 'is_deleted = 0 AND project_id IN ('.$_REQUEST['projName'].') AND inspection_id IN ('.join(',', $inspArr).') GROUP BY inspection_id');
	foreach($issueToData as $issData){
		$issueToNameData[$issData['inspection_id']] = array($issData['issuetoName'], $issData['fixedbydate'], $issData['stats'], $issData['costAttribute'], $issData['costImpactType'], $issData['costImpactPrice']);
	}
//Data Collection End Here
}

//Data Ploating Start Here
$row = mysql_fetch_assoc($result);
if($row){
	while($row){
		$row['Location'] = $locationTree[$row['Location']];
		$row['issuetoName'] = $issueToNameData[$row['InspectionId']][0];
		$row['fixedbydate'] = $issueToNameData[$row['InspectionId']][1];
		$row['stats'] = $issueToNameData[$row['InspectionId']][2];
		$row['costAttribute'] = $issueToNameData[$row['InspectionId']][3];
		$row['costImpactType'] = $issueToNameData[$row['InspectionId']][4];
		$row['costImpactPrice'] = $issueToNameData[$row['InspectionId']][5];
		
		$output .= echocsv($row);
		$row = mysql_fetch_assoc($result);
	}

	 $clause1 =array("");
   $output .= echocsv($clause1);

   $clause =array("Project Contract Clause",$contact_clause);
   $output .= echocsv($clause);
   
//Data Ploating End Here
	if(!is_dir('../report_csv/'))	mkdir('../report_csv/');

	$d = '../report_csv/'.$owner_id;
	if(!is_dir($d))	mkdir($d);
	if (file_exists($d.'/'.$fileName)) unlink($d.'/'.$fileName);
	
	$tempFile = $d.'/'.$fileName;
	$fh = fopen($tempFile, 'w') or die("can't open file");
	$stringData = $output;
	fwrite($fh, $stringData);
	fclose($fh);
	
	$fieSize = filesize($tempFile);
	$fieSizeDisplay = floor($fieSize/(1024));
		
	if ($fieSizeDisplay > 1024){
		$fieSizeDisplay = floor($fieSizeDisplay/(1024)) . "Mbs";
	}else{
		if($fieSize < 1024){
			$fieSizeDisplay = $fieSize . "Bytes";
		}else{
			$fieSizeDisplay .= "Kbs";
		}
	}

	$rply = $noofRecord.' Records '. $fieSizeDisplay;
	echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="clearDivCSV();" href="./report_csv/'.$owner_id.'/'.$fileName.'" target="_blank" class="view_btn"></a></div>';
}
//Function Section Start Here
function echocsv($fields){
	$op = '';
	$separator = '';
	foreach ($fields as $field){
		if(preg_match('/\\r|\\n|,|"/', $field)){
			$field = '"' . str_replace( '"', '""', $field ) . '"';
		}
		$op .= $separator . $field;
		$separator = ',';
	}
	$op .= "\r\n";
	return $op;
}
//Function Section End Here
?>