<?php
ob_start();
error_reporting(0);
session_start();
set_time_limit(6000000000000000000);

include('../includes/commanfunction.php');
$obj= new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";}
	
	$limit = 19;
	$issued_to_add = '';
	if(!isset($_REQUEST['startWith'])){
		$offset = 0;
	}
	if($_REQUEST['startWith'] == 0){
		$offset = 0;
	}else{
		$offset = $_REQUEST['startWith'];
		$offsetPage = ceil($offset/2);
	}
	$postCount = 0;

	$projID = ''; $location = ''; $subLocation1 = ''; $subLocation2 = ''; $filterLoc =''; $locStr = '';
	
	if(!empty($_REQUEST['projName'])){
		$projID = $_REQUEST['projName'];
		$projectName = $_REQUEST['projectName'];
		$where=" and pi.project_id='".$_REQUEST['projName']."'";
		$searchLoc = 0;
	}
	
	/*$searchLocID = "";	
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
	if(!empty($_REQUEST['subLocation3']) && strtolower($_REQUEST['subLocation3']) != 'null'){
		$searchLocID = explode(',', $_REQUEST['subLocation3']);
	}
	
	if(!empty($_REQUEST['subLocation4']) && strtolower($_REQUEST['subLocation4']) != 'null'){
		$searchLocID = explode(',', $_REQUEST['subLocation4']);
	}
	if($searchLocID!=""){
		if(is_array($searchLocID)){
			$tempLocArr = array();
			for($g=0; $g<sizeof($searchLocID); $g++){
				$tempLocSecArr = explode(",", $obj->subLocationsId($searchLocID[$g], ","));
				$tempLocArr = array_merge($tempLocArr, $tempLocSecArr);
			}
			$where.=" AND pi.location_id IN (".join(",", $tempLocArr).")";
		}else{
			$where.=" AND pi.location_id IN (".$obj->subLocationsId($searchLocID, ",").")";
		}
	}*/	
	/* Location
	 * ****************************************************************/	
	$searchLocID = "";
	if(!empty($_REQUEST['sub_subLocation3']) && !empty($_REQUEST['sub_subLocation2']) && !empty($_REQUEST['sub_subLocation']) && !empty($_REQUEST['subLocation']) && !empty($_REQUEST['location'])){
		$isMul = explode('@@@', $_REQUEST['sub_subLocation3']);
		$loopMul = count($isMul);
		$subLocationIds = '';
		if($loopMul>0){ 
			for($g=0; $g<$loopMul; $g++){
				if($subLocationIds != ''){ $subLocationIds.= ',';	}
				$subLocationIds.= $obj->subLocationsId($isMul[$g], ", ");
			}
		}else{
			$subLocationIds = $obj->subLocationsId($_REQUEST['sub_subLocation3'], ", ");
		}
		$searchLocID = $subLocationIds;
		$where .=" and I.location_id IN (".$subLocationIds.")";
		//$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['sub_subLocation3'], ", ").")";
	} elseif(empty($_REQUEST['sub_subLocation3']) && !empty($_REQUEST['sub_subLocation2']) && !empty($_REQUEST['sub_subLocation']) && !empty($_REQUEST['subLocation']) && !empty($_REQUEST['location'])){
		$isMul = explode('@@@', $_REQUEST['sub_subLocation2']);
		$loopMul = count($isMul);
		$subLocationIds = '';
		if($loopMul>0){ 
			for($g=0; $g<$loopMul; $g++){
				if($subLocationIds != ''){ $subLocationIds.= ',';	}
				$subLocationIds.= $obj->subLocationsId($isMul[$g], ", ");
			}
		}else{
			$subLocationIds = $obj->subLocationsId($_REQUEST['sub_subLocation2'], ", ");
		}
		$searchLocID = $subLocationIds;
		$where .=" and I.location_id IN (".$subLocationIds.")";
		//$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['sub_subLocation2'], ", ").")";
	} elseif(empty($_REQUEST['sub_subLocation3']) && empty($_REQUEST['sub_subLocation2']) && !empty($_REQUEST['sub_subLocation']) && !empty($_REQUEST['subLocation']) && !empty($_REQUEST['location'])){
		$subLocationIds = $obj->subLocationsId($_REQUEST['sub_subLocation'], ", ");
		$searchLocID = $subLocationIds;
		$where.=" and I.location_id in (". $subLocationIds .")";
	} elseif(empty($_REQUEST['sub_subLocation3']) && empty($_REQUEST['sub_subLocation2']) && empty($_REQUEST['sub_subLocation']) && !empty($_REQUEST['subLocation']) && !empty($_REQUEST['location'])){		
		$subLocationIds = $obj->subLocationsId($_REQUEST['subLocation'], ", ");
		$searchLocID = $subLocationIds;
		$where.=" and I.location_id in (". $subLocationIds .")";
	} elseif(empty($_REQUEST['sub_subLocation3']) && empty($_REQUEST['sub_subLocation2']) && empty($_REQUEST['sub_subLocation']) && empty($_REQUEST['subLocation']) && !empty($_REQUEST['location'])){
		$isMul = explode('@@@', $_REQUEST['location']);
		$loopMul = count($isMul);
		$subLocationIds = '';
		if($loopMul>0){ 
			for($g=0; $g<$loopMul; $g++){
				if($subLocationIds != ''){ $subLocationIds.= ',';	}
				$subLocationIds.= $obj->subLocationsId($isMul[$g], ", ");
			}
		}else{
			$subLocationIds = $obj->subLocationsId($_REQUEST['location'], ", ");
		}
		$searchLocID = $subLocationIds;
		$where .=" and I.location_id IN (".$subLocationIds.")";
		//$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['location'], ", ").")";
	}
	if($searchLocID!=""){
		if(is_array($searchLocID)){
			$tempLocArr = array();
			for($g=0; $g<sizeof($searchLocID); $g++){
				$tempLocSecArr = explode(",", $obj->subLocationsId($searchLocID[$g], ","));
				$tempLocArr = array_merge($tempLocArr, $tempLocSecArr);
			}
			$filterLoc = join(",", $tempLocArr);
		}else{
			$filterLoc = $obj->subLocationsId($searchLocID, ",");
		}
	}
	#echo $where;die;
	/* ****************************************************************/
	
	$queryLoc = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'location_parent_id = '.$searchLoc.' AND is_deleted = 0 AND project_id = "'.$projID.'" order by location_id');
/*	if(!empty($_REQUEST['status'])){
		$postCount++;
		$where.=" and F.inspection_status='".$_REQUEST['status']."'";
	}*/
	
	if(!empty($_REQUEST['inspectedBy'])){
		$postCount++;
		$where.=" and pi.inspection_inspected_by='".$_REQUEST['inspectedBy']."'";
	}
	
/*	if($_REQUEST['issuedTo']!=""){
		$postCount++;
		$isMul = explode('@@@', $_REQUEST['issuedTo']);
		$mulIssueTo = '';
		$loopMul = count($isMul);
		for($g=0; $g<$loopMul; $g++){
			if($mulIssueTo == ''){
				$mulIssueTo = "'".$isMul[$g]."'";
			}else{
				$mulIssueTo .= ", '".$isMul[$g]."'";
			}
		}
		$where.=" and isi.issued_to_name IN (".$mulIssueTo.") and isi.inspection_id = pi.inspection_id";
	}*/
	
	if($_REQUEST['issuedTo']!=""){
		$postCount++;
		$isMul = explode('@@@', $_REQUEST['issuedTo']);
		$mulIssueToWhere = '';
		$mulIssueTo = '';
		$loopMul = count($isMul);
		for($g=0; $g<$loopMul; $g++){
			if($mulIssueToWhere == ""){
				$mulIssueTo = "'".$isMul[$g]."'";
				$mulIssueToWhere .= " (isi.issued_to_name LIKE '".addslashes($isMul[$g])." (%' OR isi.issued_to_name LIKE '".addslashes($isMul[$g])."') ";
			}else{
				$mulIssueTo .= ", '".$isMul[$g]."'";
				$mulIssueToWhere .= " OR  (isi.issued_to_name LIKE '".addslashes($isMul[$g])." (%' OR isi.issued_to_name LIKE '".addslashes($isMul[$g])."') ";
			}
		}
		$where.=" AND (".$mulIssueToWhere.") AND isi.inspection_id = pi.inspection_id";
	}
	
	if($_REQUEST['inspecrType']!=""){
		$postCount++;
		$where.=" and pi.inspection_type='".$_REQUEST['inspecrType']."'";
	}
	
	if(!empty($_REQUEST['costAttribute'])){
		$postCount++;
		$where.=" and isi.cost_attribute = '".$_REQUEST['costAttribute']."'";
	}

	if(!empty($_SESSION['userRole'])){
		if($_SESSION['userRole'] != 'All Defect' && $_SESSION['userRole'] != "Sub Contractor"){
			$where.=" and pi.inspection_raised_by = '".$_SESSION['userRole']."'";
		}else{
			$postCount++;
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and pi.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
		}
	}else{
		$postCount++;
		if($_REQUEST['raisedBy'] != 'All Defect')
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and pi.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
	}

	if($_REQUEST['DRF']!="" && $_REQUEST['DRT']!=""){
		$postCount++;
		$or.=" pi.inspection_date_raised between '".date('Y-m-d', strtotime($_REQUEST['DRF']))."' and '".date('Y-m-d', strtotime($_REQUEST['DRT']))."'";
	}
	
	if($_REQUEST['DRF']!="" && $_REQUEST['FBDF']!=""){$or.=" and";}
	
	if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
		$postCount++;
		$or.=" isi.inspection_fixed_by_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
	}
	
	$orderby = "";	
	
	if ($_REQUEST["sortby"]){
		$orderby = "order by pi." . $_REQUEST["sortby"];
	}

	if(!empty($_REQUEST['searchKeyward'])){
		$postCount++;
		$locationRows = $obj->selQRYMultiple ("location_id", "project_locations", "project_id=".$_REQUEST['projName'] . " and location_title LIKE '%".$_REQUEST['searchKeyward']."%' and is_deleted=0" );
		$location_id_arr = array();
		foreach ($locationRows as $locationID){
			$location_id_arr[] = $locationID["location_id"];	
		}
		$where.=" and (isi.issued_to_name LIKE '%".$_REQUEST['searchKeyward']."%' OR pi.location_id in (".join(",", $location_id_arr) ."))";
	}

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
		$where .=  " and pi.inspection_type!='Memo'";

//Retrive Location array with Title Start Here
	$locSting = $obj->subLocationsId($filterLoc, ", ");
	if($locStr != ''){
		$locSearch = $locStr.', '.$locSting;
	}else{
		$locSearch = $locSting;
	}
	$whreCon = '';
	if($locSearch != ''){
		$whreCon = 'location_id IN ('.$locSearch.') AND project_id = '.$projID.' AND is_deleted = 0';
	}else{
		$whreCon = 'project_id = '.$projID.' AND is_deleted = 0';
	}
	
	$locDataArray = $obj->selQRYMultiple('location_id, location_title', 'project_locations', $whreCon);
	$locArrayData = array();
	if(!empty($locDataArray)){
		foreach($locDataArray as $ldata){
			$locArrayData[$ldata['location_id']] = $ldata['location_title'];
		}
	}
	if($_SESSION['userRole'] == 'Sub Contractor'){
		$where.=" AND (isi.issued_to_name LIKE '".$_SESSION['userIssueTo']." (%' OR isi.issued_to_name LIKE '".$_SESSION['userIssueTo']."') ";
	}
//Retrive Location array with Title End Here

//Retrive Location Tree and Data Start Here
	$proLocArray = array();
	if(!empty($queryLoc)){
		$totalCount = sizeof($queryLoc);
		$noInspection = sizeof($queryLoc);
		foreach($queryLoc as $locId){
			$pt = $obj->getCatIdsExport($locId["location_id"]);
			$a = explode(" > ", $pt);
			$locArray = array();
			for ($i=0;$i<count($a);$i++){
				if (empty($a[$i])){ continue; }
				$childLoc = '';	
				$childLoc = $obj->getDataByKey('project_locations', 'location_parent_id', $a[$i], 'location_id');
				if($childLoc!=''){ continue; }
				$locations = $obj->subLocationsIDS($a[$i], ' > ');
				$tmparr = explode(" > ", $locations);
				$sub_c = 1;
				$sub_arr = array();
				$sub_arr[] = $tmparr[0];
				for ($j=1;$j<count($tmparr);$j++){
					$sub_arr[] = $tmparr[$j];
				}
				$locArray[] = $sub_arr;
			}
			$proLocArray[] = $locArray;
		}
	}

	$defect_clause = '';
	if(!empty($projID)){
		$project_info = $obj->selQRYMultiple('defect_clause','projects','project_id = '.$projID);
		if(!empty($project_info)){
			$defect_clause = $project_info[0]['defect_clause'];
		}
	}

	$htmlInner = '';
	if(!empty($proLocArray)){
		$OverAllCount = 0;
		foreach($proLocArray as $pLocArr){
			$rowCount = 0;
			$res = $obj->arrangeMultiDimensionArray($pLocArr, 'DESC');
			$locInsData = array();
			foreach($res as $re){
				
				$rowCount++;
				
				$secloc = sizeof($re)-2;
				$secLastLocId = $re[$secloc];
				$comIds = $obj->subLocationsId($secLastLocId, ", ");
				
				$inspectionData = $obj->selQRYMultiple('SUM(IF(isi.inspection_status= "Open",1,0)) AS open,
					SUM(IF(isi.inspection_status="Pending",1,0)) AS pending,
					SUM(IF(isi.inspection_status="Fixed",1,0)) AS fixed,
					SUM(IF(isi.inspection_status="Closed",1,0)) AS closed',
					'issued_to_for_inspections AS isi, project_inspections pi',
					'pi.inspection_id = isi.inspection_id AND
					isi.project_id = '.$projID.' AND
					isi.is_deleted = 0 AND
					pi.is_deleted = 0 AND
					pi.location_id IN ('.$comIds.')'.$where );
				
				$locTitleList = '';
				for($i=0; $i<=$secloc; $i++){
					if($locTitleList == ''){
						$locTitleList = $locArrayData[$re[$i]];
					}else{
						$locTitleList .= ' > '. $locArrayData[$re[$i]];
					}
				}
				$locInsData[$locTitleList] = $inspectionData;
			}
			$proInsData[] = $locInsData;
			$OverAllCount = $OverAllCount+$rowCount;
		}
	}
$ajaxReplay = $OverAllCount.' Records';
$noPages = ceil(($totalCount-1)/2 +1);
if($noInspection > 0){
	require('../fpdf/fpdf.php');	
	class PDF extends FPDF{
			function Header(){// Page header
				if($this->PageNo()!=1){// Page number
					$this->Cell(0, 10, 'Page: '.$this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
					$this->ln();	
				}
			}
			function Footer(){// Position at 1.5 cm from bottom
				$this->SetY(-15);
				$this->SetFont('helvetica', 'B', 10);
				$this->Cell(10);
				$this->Cell(15, 4, 'DefectID,  part of the Wiseworker Quality Management Ecosystem,  helping the construction industry.', 0, 0);
				$this->Ln(5);
				$this->Cell(76);
				$this->Cell(60, 4, 'www.wiseworker.net', 0, 0);
			}
		}

	$pdf = new PDF();
	$pdf->AddPage();
	$pdf->AliasNbPages();

	$pdf->Image('../company_logo/logo.png',  150,  12,  -170);
	$pdf->Ln(8);

	$pdf->SetFont('times', 'BU', 12);
	$pdf->Cell(40, 10, 'Executive Report');		
	$pdf->Ln(6);

	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(26, 10, 'Project Name : ');	

	$pdf->SetFont('times', '', 10);
	$pdf->Cell(10, 10, $projectName);	
	$pdf->Ln(5);

	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(11, 10, 'Date : ');	
	
	$pdf->SetFont('times', '', 10);
	$pdf->Cell(20, 10, date('d/m/Y'));	
	$pdf->Ln(5);

	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(11, 10, 'Page : ');		
	
	$pdf->SetFont('times', '', 10);
	$pdf->Cell(8, 10, '1 of '.'{nb}');		
	$pdf->Ln(10);
	
	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(25, 10, 'Report Filtered by :');	
	$pdf->Ln(8);
	$jk=0;

	$pdf->Cell(15, 10, '');	
	
	$x0 = $x = $pdf->GetX();
	$y = $pdf->GetY();
	
	$jk=0;	
	if(!empty($_REQUEST['location'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Location Name: ');	
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['locationName']);	
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	if(!empty($_REQUEST['subLocation'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Sub Location: ');	
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['subLocationName']);	
		$jk++; if($jk%2 ==0){	$pdf->ln();}
	}
	if(!empty($_REQUEST['sub_subLocation'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Sub Location 1: ');	
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['sub_subLocationName']);	
		$jk++; if($jk%2 ==0){	$pdf->ln();}
	}
	if(!empty($_REQUEST['sub_subLocation2']) && $_REQUEST['sub_subLocation2']!='null'){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Sub Location 2: ');	
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['sub_subLocationName2']);	
		$jk++; if($jk%2 ==0){	$pdf->ln();}
	}
	if(!empty($_REQUEST['sub_subLocation3']) && $_REQUEST['sub_subLocation3']!='null'){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Sub Location 3: ');	
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['sub_subLocationName3']);	
		$jk++; if($jk%2 ==0){	$pdf->ln();}
	}
	
/*	if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Status: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['status']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}*/
	
	if(!empty($_REQUEST['inspectedBy']) && isset($_REQUEST['inspectedBy'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Inspected By: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['inspectedBy']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}

	if(!empty($_REQUEST['issuedTo']) && isset($_REQUEST['issuedTo'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Issue To: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(140,10,str_replace("'", "", $mulIssueTo));
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	if(!empty($_REQUEST['priority']) && isset($_REQUEST['priority'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Priority: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['priority']);	
		$jk++; if($jk%2 ==0){	$pdf->ln();}					
	}
	
	if(!empty($_REQUEST['inspecrType']) && isset($_REQUEST['inspecrType'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Inspection Type: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['inspecrType']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	if(!empty($_REQUEST['raisedBy']) && isset($_REQUEST['raisedBy'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Raised By: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['raisedBy']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	if(!empty($_REQUEST['costAttribute']) && isset($_REQUEST['costAttribute'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Cost Attribute: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['costAttribute']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	if(!empty($_REQUEST['DRF']) && isset($_REQUEST['DRF']) || !empty($_REQUEST['DRT']) && isset($_REQUEST['DRT'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Date Raised: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['DRF'].' to '.$_REQUEST['DRT']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	if(!empty($_REQUEST['FBDF']) && isset($_REQUEST['FBDF']) || !empty($_REQUEST['FBDT']) && isset($_REQUEST['FBDT'])){
		$pdf->SetFont('times','B',11);		
		$pdf->Cell(50,10,'Fixed By Date: ');
		$pdf->SetFont('times','',10);
		$pdf->Cell(25,10,$_REQUEST['FBDF'].' to '.$_REQUEST['FBDT']);
		$jk++; if($jk%2 ==0){	$pdf->ln();}	
	}
	
	$pdf->ln();
	$first_time = 1;
	$pageCount = 1;
	$page_break = 1;
	$fill = true;
	$i = 0;
	$x0 = $x = $pdf->GetX();
	$y = $pdf->GetY();

	$x = 5;
	
	$pagebreakCount = 40;
		
	$pageCount = 0;$pageCount++;
					
	$yH = 5; //height of the row	
//Title String Header		
	$pdf->SetFillColor(190, 190, 190);	
	$pdf->SetXY($x, $y);
	$pdf->SetFont('Times', 'BI', 12);	
	$pdf->Cell(140, $yH, "Location", 1, 0, 'C', $fill);
	$pdf->Cell(20, $yH, "Open", 1, 0, 'C', $fill);
	$pdf->Cell(20, $yH, "Closed", 1, 0, 'C', $fill);
	$pdf->Cell(20, $yH, "Pending", 1, 0, 'C', $fill);
//Title String Header
	$pdf->SetFont('Times', '', 10);
	$openCount = 0;$closeCount = 0;$penndingCount = 0;
	$j = 0;
	foreach($proInsData as $piData){
		$keyArray = array_keys($piData);
		$lupCont = sizeof($keyArray);
		for($i=0; $i<$lupCont; $i++){
			if($keyArray[$i] != ''){
				$j++;
				$y = $y + $yH;
				$pdf->SetXY($x, $y);
				if($pageCount == 1){
					if($j == 40){
						$pdf->AddPage();
						$y = $pdf->GetY();
						$pdf->SetXY($x, $y);
						$pdf->SetFont('Times', 'BI', 12);	
						$pdf->Cell(140, $yH, "Location", 1, 0, 'C', $fill);
						$pdf->Cell(20, $yH, "Open", 1, 0, 'C', $fill);
						$pdf->Cell(20, $yH, "Closed", 1, 0, 'C', $fill);
						$pdf->Cell(20, $yH, "Pending", 1, 0, 'C', $fill);
						$pageCount++;
						$y = $y + $yH;
						$pdf->SetXY(5, $y);
					}
				}else{
					if(($j%$pagebreakCount) == 0){
						$pdf->AddPage();
						
						$y = $pdf->GetY();
						
						$pdf->SetXY($x, $y);
						$pdf->SetFont('Times', 'BI', 12);	
						$pdf->Cell(140, $yH, "Location", 1, 0, 'C', $fill);
						$pdf->Cell(20, $yH, "Open", 1, 0, 'C', $fill);
						$pdf->Cell(20, $yH, "Closed", 1, 0, 'C', $fill);
						$pdf->Cell(20, $yH, "Pending", 1, 0, 'C', $fill);
						$pageCount++;
						$y = $y + $yH;
						$pdf->SetXY(5, $y);
					}
				}
				
				$pdf->Cell(140, $yH, $keyArray[$i], 1, 0, 'L', false);
				$pdf->Cell(20, $yH, $piData[$keyArray[$i]][0]['open'], 1, 0, 'C', false);
				$openCount=$openCount + $piData[$keyArray[$i]][0]['open'];
				$pdf->Cell(20, $yH, $piData[$keyArray[$i]][0]['closed'], 1, 0, 'C', false);
				$closeCount=$closeCount + $piData[$keyArray[$i]][0]['closed'];
				$pdf->Cell(20, $yH, $piData[$keyArray[$i]][0]['pending'], 1, 0, 'C', false);
				$penndingCount=$penndingCount + $piData[$keyArray[$i]][0]['pending'];
			}
		}
	}
	
	$y = $y + $yH;
	$pdf->SetXY($x, $y);
	$pdf->SetFont('Times', 'B', 13);	
	$pdf->Cell(140, $yH, "Total", 1, 0, 'C', $fill);
	$pdf->SetFont('Times', 'B', 12);	
	$pdf->Cell(20, $yH, $openCount, 1, 0, 'C', $fill);
	$pdf->Cell(20, $yH, $closeCount, 1, 0, 'C', $fill);
	$pdf->Cell(20, $yH, $penndingCount, 1, 0, 'C', $fill);

	$pdf->Ln(10);
	$pdf->SetFont('times', 'B', 11);		
	$pdf->Cell(35, 10, 'Defect Clause: ');
	$pdf->SetFont('times', '', 10);
	$pdf->Cell(25, 10, $defect_clause);
	$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}

	$file_name = 'Executive_Repprt_'.microtime().'.pdf';
	$d = '../report_pdf/'.$owner_id;
	if(!is_dir($d))
		mkdir($d);
	
	if (file_exists($d.'/'.$file_name))
		unlink($d.'/'.$file_name);
	
	$tempFile = $d.'/'.$file_name;
	$pdf->Output($tempFile);
	$fieSize = filesize($tempFile);
	$fieSize = floor($fieSize/(1024));
	if ($fieSize > 1024){
		$fieSize = floor($fieSize/(1024)) . "Mbs";
	}else{
		$fieSize .= "Kbs";
	}
	$rply = $ajaxReplay.' '.$fieSize;
	echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="closePopUp();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';
}else{
	echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
}
?>
