<?php
ob_start();
session_start();

#echo 		$_SERVER['DOCUMENT_ROOT'];die;
#include('../includes/functions.php');
include('../includes/commanfunction.php');
require('../fpdf/mc_table.php');

$obj = new COMMAN_Class();

//require_once('html2pdf.class.php');
$issued_to_add='';
if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}
elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}
$d = '../report_pdf/'.$owner_id;
if (isset ($_REQUEST['page_no']) && $_REQUEST['page_no'] == 1){
	if(!is_dir($d)){
		mkdir($d,0777);
		chmod($d,0777);
	}else{
		rrmdir($d);
		mkdir($d,0777);
		chmod($d,0777);
	}
}
if (isset ($_REQUEST['page_no']))
	$page_no = $_REQUEST['page_no'];
else
	$page_no = 1;
	$report_type = $_REQUEST['report_type'];
	$images_path = "photo_summary/";
	$dimages_path = "signoff_summary/";
	//if ($report_type == "pdfSummayHD")
	//{
	//	$images_path = "";
		//$dimages_path = "";
	//}
$issueEmailFile = array();
	
if(isset($_REQUEST['name'])){
	$locNameIDs = '';  $issToForInspWhere = '';
	$subwhere = '';
	$splCon='';
	$whereIssueTo = '';
	$mulIssueTo = '';
//send email Dated : 23-03-2013
	if(!empty($_REQUEST['issueToList'])){
		$tempArr = explode('###', $_REQUEST['issueToList']);
		$_REQUEST['issuedTo'] = $tempArr[1];
		$issueName = explode('@@@', $tempArr[1]);
		$issueEmail = explode('@@@', $tempArr[0]);
		for($p=0;$p<sizeof($issueName);$p++){
			$countQuery = $obj->selQRYMultiple('issue_to_id, issue_to_name', 'inspection_issue_to', 'project_id = "'.$_REQUEST['projName'].'" AND issue_to_name = "'.html_entity_decode($issueName[$p]).'" AND issue_to_email =  "'.html_entity_decode($issueEmail[$p]).'" AND is_deleted = 0 GROUP BY issue_to_name');
			if(empty($countQuery)){
				$insetQRY = "UPDATE inspection_issue_to SET issue_to_email = '".html_entity_decode($issueEmail[$p])."' WHERE project_id = '".$_REQUEST['projName']."' AND issue_to_name = '".html_entity_decode($issueName[$p])."' AND is_deleted = 0";
				mysql_query($insetQRY);
			}
		}
	}
//send email Dated : 23-03-2013
	$orderby = "ORDER BY F.issued_to_name";
	
	if(!empty($_REQUEST['searchKeyward'])){
		$postCount++;
		$locationRows = $obj->selQRYMultiple ("location_id", "project_locations", "project_id=".$_REQUEST['projName'] . " AND location_title LIKE '%".trim($_REQUEST['searchKeyward'])."%' AND is_deleted=0" );
		$location_id_arr = array();
		foreach ($locationRows as $locationID){
			$location_id_arr[] = $locationID["location_id"];	
		}
		$subwhere = join(",", $location_id_arr);
			if($subwhere!=''){
				$where.= " AND I.location_id in (".$subwhere.")";
				$splCon	.= " AND I.location_id in (".$subwhere.")";
			}
	}
	
	if(!empty($_REQUEST['projName'])){
		$where.=" AND I.project_id='".$_REQUEST['projName']."'";
	}
	
	/*if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
		//$postCount++;
		//$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['location'], ", ").")";
		$locData = $obj->getLocationIdsOfAll($_REQUEST['location']);
		#print_r($locData);die;
		$where.=" and I.location_id in (".join(",", $locData) .")";
	}
	// 13/09/2016 updated
	if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['sub_subLocation'])){
		$postCount++;
		$isMul = explode('@@@', $_REQUEST['sub_subLocation']);
		$loopMul = count($isMul);
		$subSubLocationIds = '';
		if($loopMul>0){ 
			for($g=0; $g<$loopMul; $g++){
				if($subSubLocationIds != ''){ $subSubLocationIds.= ',';	}
				$subSubLocationIds.= $obj->subLocationsId($isMul[$g], ", ");
			}
		}else{
			$subSubLocationIds = $obj->subLocationsId($_REQUEST['sub_subLocation'], ", ");
		}
		$where.=" and I.location_id in (".$subSubLocationIds.")";
	}else{
		// 13/09/2016 updated
		if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
			$postCount++;
			$isMul = explode('@@@', $_REQUEST['subLocation']);
			$loopMul = count($isMul);
			$subLocationIds = '';
			if($loopMul>0){ 
				for($g=0; $g<$loopMul; $g++){
					if($subLocationIds != ''){ $subLocationIds.= ',';	}
					$subLocationIds.= $obj->subLocationsId($isMul[$g], ", ");
				}
			}else{
				$subLocationIds = $obj->subLocationsId($_REQUEST['subLocation'], ", ");
			}
			$where.=" and I.location_id in (".$subLocationIds.")";
			//$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['subLocation'], ", ").")";
		}
	}*/
	/* Location
	 * ****************************************************************/	
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
		$where .=" and I.location_id IN (".$subLocationIds.")";
		//$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['sub_subLocation2'], ", ").")";
	} elseif(empty($_REQUEST['sub_subLocation3']) && empty($_REQUEST['sub_subLocation2']) && !empty($_REQUEST['sub_subLocation']) && !empty($_REQUEST['subLocation']) && !empty($_REQUEST['location'])){
		$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['sub_subLocation'], ", ").")";
	} elseif(empty($_REQUEST['sub_subLocation3']) && empty($_REQUEST['sub_subLocation2']) && empty($_REQUEST['sub_subLocation']) && !empty($_REQUEST['subLocation']) && !empty($_REQUEST['location'])){		
		$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['subLocation'], ", ").")";
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
		$where .=" and I.location_id IN (".$subLocationIds.")";
		//$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['location'], ", ").")";
	}
	#echo $where;die;
	/* ****************************************************************/

	// 13/09/2016 updated
	if(!empty($_REQUEST['status']) && $_REQUEST['status'] != 'null'){
		$postCount++;
		$isMul = explode('@@@', $_REQUEST['status']);
		$mulStatus = '';
		$loopMul = count($isMul);
		if($loopMul>0){ 
			for($g=0; $g<$loopMul; $g++){
				if($mulStatus == ''){
					$mulStatus = $isMul[$g];
					$where.=" and (F.inspection_status LIKE '%".$isMul[$g]."%' ";
					$issToForInspWhere.=" and (inspection_status LIKE '%".$isMul[$g]."%' ";
				}else{
					$mulStatus .= ", '".$isMul[$g]."'";
					$where.=" OR F.inspection_status LIKE '%".$isMul[$g]."%' ";
					$issToForInspWhere.=" OR inspection_status LIKE '%".$isMul[$g]."%' ";
				}
			}
			$where.=" ) "; $issToForInspWhere.= " ) "; 
		}else{
			$where.= " and F.inspection_status='".$_REQUEST['status']."'";
			$issToForInspWhere.= " and inspection_status='".$_REQUEST['status']."'";
		}
		$_REQUEST['status'] = str_replace("@@@", ", ", $_REQUEST['status']);
	}
	
	if(!empty($_REQUEST['inspectedBy'])){
		$postCount++;
		$where.=" AND I.inspection_inspected_by='".$_REQUEST['inspectedBy']."'";
	}
	
	// 13/09/2016 updated
	if($_REQUEST['issuedTo']!=""){
		$postCount++;
		$isMul = explode('@@@', $_REQUEST['issuedTo']);
		$mulIssueTo = '';
		$loopMul = count($isMul);
		for($g=0; $g<$loopMul; $g++){
			if($mulIssueTo == ''){
				$mulIssueTo = "'".$isMul[$g]."'";
				$where.= " and (F.issued_to_name LIKE '%".$isMul[$g]."%' ";
				$issToForInspWhere.= " and (issued_to_name LIKE '%".$isMul[$g]."%' ";
			}else{
				$mulIssueTo .= ", '".$isMul[$g]."'";
				$where.= " OR F.issued_to_name LIKE '%".$isMul[$g]."%' ";
				$issToForInspWhere.= " OR issued_to_name LIKE '%".$isMul[$g]."%' ";
			}
		}
		$where.=" ) "; $issToForInspWhere.= " ) "; 
#		$where.=" and F.issued_to_name='".$_REQUEST['issuedTo']."' and F.inspection_id = I.inspection_id";
		//$where.=" and F.issued_to_name IN (".$mulIssueTo.") and F.inspection_id = I.inspection_id";
		$where.=" and F.inspection_id = I.inspection_id";
	}
	
	if($_REQUEST['inspecrType']!=""){
		$postCount++;
		$where.=" AND I.inspection_type='".$_REQUEST['inspecrType']."'";
	}
	
	if(!empty($_REQUEST['costAttribute'])){
		$postCount++;
		$where.=" AND F.cost_attribute = '".$_REQUEST['costAttribute']."'";
	}
	
	if(!empty($_SESSION['userRole'])){
		if($_SESSION['userRole'] != 'All Defect' && $_SESSION['userRole'] != "Sub Contractor"){
			$where.=" AND I.inspection_raised_by = '".$_SESSION['userRole']."'";
		}else{
			$postCount++;
			if(!empty($_REQUEST['raisedBy'])){ $where.=" AND I.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
		}
	}else{
		$postCount++;
		if($_REQUEST['raisedBy'] != 'All Defect')
			if(!empty($_REQUEST['raisedBy'])){ $where.=" AND I.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
	}

	if($_REQUEST['DRF']!="" && $_REQUEST['DRT']!=""){
		$postCount++;
		$or.=" I.inspection_date_raised between '".date('Y-m-d', strtotime($_REQUEST['DRF']))."' AND '".date('Y-m-d', strtotime($_REQUEST['DRT']))."'";
	}
	
	if($_REQUEST['DRF']!="" && $_REQUEST['FBDF']!=""){$or.=" AND";}
	
	if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
		$postCount++;
		$or.=" F.inspection_fixed_by_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' AND '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
	}
	
	if(!empty($or)&& !empty($where)){$where = $where." AND (".$or.")";}
	
	if ($_REQUEST["sortby"]){
		if ($_REQUEST["sortby"] == "location_id")
			$orderby .= ", I.inspection_location";
		else if ($_REQUEST["sortby"] == "issued_to_name")
			$orderby .= ",  F.issued_to_name";
		else
			$orderby .= ", I.".$_REQUEST["sortby"];
	}
	if($_SESSION['userRole'] == 'Sub Contractor'){
		$where.=" and  F.issued_to_name = '".$_SESSION['userIssueTo']."'";
	}
	
	if($report_type == "pdfSummayWithImages"){$pageBreak = 5; $limit = ($pageBreak*10);}else{$pageBreak = 19; $limit = ($pageBreak*10);}
	
	$displayFlag = false;
	$finalHTML = '';
	$qi = "SELECT
		P.project_name as Project,
		I.location_id as Location,
		I.inspection_date_raised as DateRaised,
		I.inspection_inspected_by as InspectedBy,
		I.inspection_type as InspectonType,
		F.inspection_status as Status,
		F.issued_to_name as IssueToName,
		F.cost_attribute as CostAttribute,
		F.inspection_fixed_by_date as FixedByDate,
		I.inspection_description as Description,
		I.inspection_notes as Note,
		I.inspection_id as InspectionId,
		I.inspection_sign_image as signoff,
		F.inspection_id as InspectionId_FOR,
		I.inspection_raised_by as RaisedBy,
		P.defect_clause as defect_clause
	FROM
		projects as P, issued_to_for_inspections as F,
		project_inspections as I
	WHERE
		I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.is_deleted = '0' and F.is_deleted = '0' and P.is_deleted = '0' $where $orderby";
	$ri=mysql_query($qi);

	//get Location search name start here
$projectlocationRows = $obj->selQRYMultiple ("location_id, location_title", "project_locations", "project_id=".$_REQUEST['projName'] . " and is_deleted=0" );
		$location_name_arr = array();
		foreach ($projectlocationRows as $locationID)
		{
			$location_name_arr[$locationID["location_id"]] = $locationID["location_title"];	
		}
		
		//Location
		$locDataMake = explode('@@@',$_REQUEST['location']);
		foreach($locDataMake as $locDataMake){		
			$searchLocationName[] = $location_name_arr[$locDataMake];	
		}
		$locationSearchName =  join(', ',$searchLocationName);
		
		//Sub-Location
		$subLocDataMake = explode('@@@',$_REQUEST['subLocation']);
		$subLocationSearchName = '';
		foreach($subLocDataMake as $subLoc){	
			if($subLocationSearchName == ''){	
				$subLocationSearchName = $location_name_arr[$subLoc];	
			}else{
				$subLocationSearchName.= ", ".$location_name_arr[$subLoc];	
			}
		}
		
		//Sub-Sub-Location
		$subSubLocDataMake = explode('@@@',$_REQUEST['sub_subLocation']);
		$subSubLocationSearchName = '';
		foreach($subSubLocDataMake as $subSubLoc){		
			if($subSubLocationSearchName == ''){	
				$subSubLocationSearchName = $location_name_arr[$subSubLoc];	
			}else{
				$subSubLocationSearchName.= ", ".$location_name_arr[$subSubLoc];	
			}
		}

//get Location search name end here
	
	$countQuery = $obj->selQRYMultiple('count(I.inspection_id) as totalcount, F.issued_to_name', 'projects as P, issued_to_for_inspections as F, project_inspections as I', 'I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.is_deleted = "0" and F.is_deleted = "0" and P.is_deleted = "0" '.$where.' group by F.issued_to_name');
	$countArray = array();
	
#echo '<pre>';	print_r($countQuery);die;
	$firstIssueTo = $countQuery[0]['issued_to_name'];
	$firstInspectionCount = $countQuery[0]['totalcount'];
	foreach($countQuery as $cntQry){
		$countArray[$cntQry['issued_to_name']] = $cntQry['totalcount'];
	}
	$noPages = ceil(($noInspection-4)/6 +1);
	if(mysql_num_rows($ri) > 0){$displayFlag = true;
		class PDF extends PDF_MC_Table{
			function Header(){
				if($this->PageNo()!=1){
					$this->Cell(0, 10, $this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
					$this->ln();	
					$this->SetFont('times', 'B', 10);
					$header = array("ID", "Inspection Type", "Location", "Description", "Inspected By", "Date Raised", "Raised By", "Fix By Date", "Status", "Image 1", "Image 2", "Drawing", "Sign Off");
					$w = $this->header_width();
					$this->SetWidths($w);
					$best_height = 17;
					$this->row($header, $best_height);
				}
			}
			
			function Footer(){
				$this->SetY(-15);
				$this->SetFont('times','B',10);
				$this->Cell(0, 10, 'DefectID – Copyright Wiseworking 2012 / 2013', 0, 0, 'C');
			}
			
			function header_width(){
				return array(15, 20, 25, 35, 19, 14, 14, 14, 14, 30, 30, 30, 25);
			}
		}
		//Meta Data Section
		$locArrayData = array();
		if ($_REQUEST['projName'] == "all"){
			if ($onwer_id != "company"){
				$proData = $obj->selQRYMultiple('project_id, project_name', 'user_projects', 'is_deleted = 0 AND user_id='.$owner_id);
			}else{
				$proData = $obj->selQRYMultiple('project_id, project_name', 'user_projects', 'is_deleted = 0');
			}
			$projects_name = '';
			if($projects_name == ''){
				$projects_name = $proData['project_name'];
			}else{
				$projects_name .= ', '.$proData['project_name'];
			}
		}else{
			$projects_name = $obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projName'], 'project_name');
		}
	
		if($locNameIDs != ''){
			$locDataArray = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'location_id IN ('.$locNameIDs.') AND project_id = '.$_REQUEST['projName'].' AND is_deleted = 0');
			if(!empty($locDataArray)){
				foreach($locDataArray as $ldata){
					$locArrayData[$ldata['location_id']] = $ldata['location_title'];
				}
			}
		}
		//Meta Data Section
		//Start PDF Creation Here
		//Top Header Sectiond Start Here
		$pdf = new PDF("L", "mm", "A4");
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		$pdf->SetTopMargin(20);
	
		$pdf->Image('../company_logo/logo.png', 235, 5, 'png', -220);
		$pdf->Ln(8);
		
		$pdf->SetFont('times', 'BU', 12);
		$pdf->Cell(40, 10, 'Summary Report for Sub Contractor');		
		$pdf->Ln(6);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(26, 10, 'Project Name : ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(10, 10, $projects_name);	
		$pdf->Ln(5);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(26, 10, 'IssueTo Name : ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(10, 10, $firstIssueTo);	
		$pdf->Ln(5);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(11, 10, 'Date : ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(20, 10, date('d/m/Y'));	
		$pdf->Ln(5);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(23, 10, 'Inspections : ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(20, 10, $firstInspectionCount);	
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
		
		$jk=0;	
		if(!empty($_REQUEST['sortby']) && isset($_REQUEST['sortby'])){
			if($_REQUEST['sortby'] == 'location_id'){
				$sortby = 'Location';
			}else if($_REQUEST['sortby'] == 'inspection_date_raised'){
				$sortby = 'Date Raised';
			}else if($_REQUEST['sortby'] == 'issued_to_name'){
				$sortby = 'Issued To';
			}
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Sorted By: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $sortby);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
		}
		
		if(!empty($_REQUEST['searchKeyward']) && isset($_REQUEST['searchKeyward'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Search keyword: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $_REQUEST['searchKeyward']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
		}
	
		if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Location Name: ');	
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $locationSearchName);	
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
		}
		
		if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['sub_subLocation'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Location Name: ');		
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10,$locationSearchName);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
			$pdf->ln(); $pdf->Cell(15, 10, '');
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Sub Location: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $subLocationSearchName);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
			
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Sub Location 1: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $subSubLocationSearchName);
				
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
			
		}else{
			if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
				$pdf->SetFont('times', 'B', 11);		
				$pdf->Cell(50, 10, 'Location Name: ');
				$pdf->SetFont('times', '', 10);
				$pdf->Cell(50, 10, $locationSearchName);
				
				$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
				$pdf->ln(); $pdf->Cell(15, 10, '');			
				$pdf->SetFont('times', 'B', 11);		
				$pdf->Cell(50, 10, 'Sub Location: ');
				$pdf->SetFont('times', '', 10);
				$pdf->Cell(50, 10, $subLocationSearchName);
				
				$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
			}
		}
		
		if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Status: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $_REQUEST['status']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
		}
		
		if(!empty($_REQUEST['inspectedBy']) && isset($_REQUEST['inspectedBy'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Inspected By: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $_REQUEST['inspectedBy']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
		}
	
		
		
		if(!empty($_REQUEST['priority']) && isset($_REQUEST['priority'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Priority: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $_REQUEST['priority']);	
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}				
		}
		
		if(!empty($_REQUEST['inspecrType']) && isset($_REQUEST['inspecrType'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Inspection Type: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $_REQUEST['inspecrType']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
		}
		
		if(!empty($_REQUEST['raisedBy']) && isset($_REQUEST['raisedBy'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Raised By: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $_REQUEST['raisedBy']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
		}
		
		if(!empty($_REQUEST['costAttribute']) && isset($_REQUEST['costAttribute'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Cost Attribute: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $_REQUEST['costAttribute']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
		}
		
		if(!empty($_REQUEST['DRF']) && isset($_REQUEST['DRF']) || !empty($_REQUEST['DRT']) && isset($_REQUEST['DRT'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Date Raised: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $_REQUEST['DRF'].' to '.$_REQUEST['DRT']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
		}
		
		if(!empty($_REQUEST['FBDF']) && isset($_REQUEST['FBDF']) || !empty($_REQUEST['FBDT']) && isset($_REQUEST['FBDT'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Fixed By Date: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(50, 10, $_REQUEST['FBDF'].' to '.$_REQUEST['FBDT']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
		}
		if(!empty($_REQUEST['issuedTo']) && isset($_REQUEST['issuedTo'])){
			#$pdf->ln(); $pdf->Cell(15, 10, '');	
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Issue To: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(140, 10, str_replace("'", "", $mulIssueTo));
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
		}
		$pdf->ln();
	//Top Header Sectiond End Here
	
	//Data Section Start Here
		$pdf->SetFont('times', 'B', 10);
			
		$header = array("ID", "Inspection Type", "Location", "Description", "Inspected By", "Date Raised", "Raised By", "Fix By Date", "Status", "Image 1", "Image 2", "Drawing", "Sign Off");
	
		$w = $pdf->header_width();
		$pdf->SetWidths($w);		
		$best_height = 17;
		$pdf->row($header, $best_height);
		$report_type = 'pdfSummayWithImages';
		$images_path = "photo_summary/";
		$dimages_path = "signoff_summary/";
	
		$pdf->SetFont('times', '', 9);
		$oldIssueToName = '';
		$defect_clause = '';
		while($fi=mysql_fetch_assoc($ri)){
			if(isset($fi['defect_clause']) && !empty($fi['defect_clause']) && empty($defect_clause)){
				$defect_clause = $fi['defect_clause'];
			}

			if ($oldIssueToName != $fi["IssueToName"] && $oldIssueToName != ''){
				$pdf->Ln(5);
				$pdf->SetFont('times', 'B', 11);		
				$pdf->Cell(35, 10, 'Defect Clause: ');
				$pdf->SetFont('times', '', 10);
				$pdf->Cell(25, 10, $defect_clause);

				$file_name = 'Summary_Report_for_sub_contractor_'.str_replace(array(' ', '?', '/'), '_', $oldIssueToName).'.pdf';
				$d = '../report_pdf/'.$owner_id;
				$tempFile = $d.'/'.$file_name;
				$pdf->Output($tempFile);
				if(!empty($_REQUEST['issueToList'])){
					$displayFlag = false;
					$issueEmailFile[] = array('issueToName'=>$oldIssueToName, 'fileAttach'=>$tempFile);
				}else{
					$zipFileArray[] = $tempFile;
				}
				
				$pdf = new PDF("L", "mm", "A4");
				$pdf->AliasNbPages();
				$pdf->AddPage();
				
				$pdf->SetTopMargin(20);
			
				$pdf->Image('../company_logo/logo.png', 235, 5, 'png', -220);
				$pdf->Ln(8);
				
				$pdf->SetFont('times', 'BU', 12);
				$pdf->Cell(40, 10, 'Summary Report for Sub Contractor');		
				$pdf->Ln(6);
				
				$pdf->SetFont('times', 'B', 10);
				$pdf->Cell(26, 10, 'Project Name : ');	
				
				$pdf->SetFont('times', '', 10);
				$pdf->Cell(10, 10, $projects_name);	
				$pdf->Ln(5);
				
				$pdf->SetFont('times', 'B', 10);
				$pdf->Cell(26, 10, 'IssueTo Name : ');	
				
				$pdf->SetFont('times', '', 10);
				$pdf->Cell(10, 10, $fi["IssueToName"]);
				$pdf->Ln(5);
				
				$pdf->SetFont('times', 'B', 10);
				$pdf->Cell(11, 10, 'Date : ');	
				
				$pdf->SetFont('times', '', 10);
				$pdf->Cell(20, 10, date('d/m/Y'));	
				$pdf->Ln(5);
				
				$pdf->SetFont('times', 'B', 10);
				$pdf->Cell(23, 10, 'Inspections : ');	
				
				$pdf->SetFont('times', '', 10);
				$pdf->Cell(20, 10, $countArray[$fi["IssueToName"]]);	
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
				
				$jk=0;	
				if(!empty($_REQUEST['sortby']) && isset($_REQUEST['sortby'])){
					if($_REQUEST['sortby'] == 'location_id'){
						$sortby = 'Location';
					}else if($_REQUEST['sortby'] == 'inspection_date_raised'){
						$sortby = 'Date Raised';
					}else if($_REQUEST['sortby'] == 'issued_to_name'){
						$sortby = 'Issued To';
					}
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Sorted By: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $sortby);
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
				}
				
				if(!empty($_REQUEST['searchKeyward']) && isset($_REQUEST['searchKeyward'])){
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Search keyword: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $_REQUEST['searchKeyward']);
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
				}
			
				if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Location Name: ');	
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $locationSearchName);	
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
				}
				
				if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['sub_subLocation'])){
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Location Name: ');		
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $locationSearchName);
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
					$pdf->ln(); $pdf->Cell(15, 10, '');	
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Sub Location: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $locArrayData[$_REQUEST['subLocation']]);
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
					
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Sub Location 1: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $locArrayData[$_REQUEST['sub_subLocation']]);
						
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
					
				}else{
					if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
						$pdf->SetFont('times', 'B', 11);		
						$pdf->Cell(50, 10, 'Location Name: ');
						$pdf->SetFont('times', '', 10);
						$pdf->Cell(50, 10, $locationSearchName);
						
						$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
						$pdf->ln(); $pdf->Cell(15, 10, '');				
						$pdf->SetFont('times', 'B', 11);		
						$pdf->Cell(50, 10, 'Sub Location: ');
						$pdf->SetFont('times', '', 10);
						$pdf->Cell(50, 10, $locArrayData[$_REQUEST['subLocation']]);
						
						$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
					}
				}
				
				if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Status: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $_REQUEST['status']);
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
				}
				
				if(!empty($_REQUEST['inspectedBy']) && isset($_REQUEST['inspectedBy'])){
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Inspected By: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $_REQUEST['inspectedBy']);
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
				}
			
				
				
				if(!empty($_REQUEST['priority']) && isset($_REQUEST['priority'])){
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Priority: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $_REQUEST['priority']);	
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}				
				}
				
				if(!empty($_REQUEST['inspecrType']) && isset($_REQUEST['inspecrType'])){
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Inspection Type: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $_REQUEST['inspecrType']);
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
				}
				
				if(!empty($_REQUEST['raisedBy']) && isset($_REQUEST['raisedBy'])){
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Raised By: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $_REQUEST['raisedBy']);
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
				}
				
				if(!empty($_REQUEST['costAttribute']) && isset($_REQUEST['costAttribute'])){
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Cost Attribute: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $_REQUEST['costAttribute']);
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
				}
				
				if(!empty($_REQUEST['DRF']) && isset($_REQUEST['DRF']) || !empty($_REQUEST['DRT']) && isset($_REQUEST['DRT'])){
					$pdf->ln(); $pdf->Cell(15, 10, '');
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Date Raised: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $_REQUEST['DRF'].' to '.$_REQUEST['DRT']);
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
				}
				
				if(!empty($_REQUEST['FBDF']) && isset($_REQUEST['FBDF']) || !empty($_REQUEST['FBDT']) && isset($_REQUEST['FBDT'])){
					#$pdf->ln(); $pdf->Cell(15, 10, '');
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Fixed By Date: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(50, 10, $_REQUEST['FBDF'].' to '.$_REQUEST['FBDT']);
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
				}
				
				if(!empty($_REQUEST['issuedTo']) && isset($_REQUEST['issuedTo'])){
					$pdf->SetFont('times', 'B', 11);		
					$pdf->Cell(50, 10, 'Issue To: ');
					$pdf->SetFont('times', '', 10);
					$pdf->Cell(140, 10, str_replace("'", "", $mulIssueTo));
					
					$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
				}
				$pdf->ln();
				
				$pdf->SetFont('times', 'B', 10);
				
				$header = array("ID", "Inspection Type", "Location", "Description", "Inspected By", "Date Raised", "Raised By", "Fix By Date", "Status", "Image 1", "Image 2", "Drawing", "Sign Off");
				$w = $pdf->header_width();
				$pdf->SetWidths($w);		
				$best_height = 17;
				$pdf->row($header, $best_height);
			}
			$locations = $obj->subLocations($fi["Location"], ' > ');
			$image0 = "";
			$image1 = "";
			$drawing_image = "";
			$signoff_image = "";
			
			if($report_type == "pdfSummayWithImages"){
				$images = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$fi["InspectionId"].' AND is_deleted = 0 AND graphic_type = "images" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 2');
				if(isset($images[0]) && $images[0]['graphic_name'] != ''){
					$obj->resizeImages('../inspections/photo/'.$images[0]['graphic_name'], 153, 100, '../inspections/photo/photo_summary/'.$images[0]['graphic_name']);
					if(file_exists('../inspections/photo/'.$images_path.$images[0]['graphic_name'])){
						$image0 = "IMAGE##25##22##".'../inspections/photo/'.$images_path.$images[0]['graphic_name'];
					}else{
						if (file_exists('../inspections/photo/'.$images[0]['graphic_name']))
							$image0 = "IMAGE##25##22##".'../inspections/photo/'.$images[0]['graphic_name'];
					}
				}
				
				if(isset($images[1]) && $images[1]['graphic_name'] != ''){
					$obj->resizeImages('../inspections/photo/'.$images[1]['graphic_name'], 153, 100, '../inspections/photo/photo_summary/'.$images[1]['graphic_name']);
					if(file_exists('../inspections/photo/'.$images_path.$images[1]['graphic_name'])){
						$image1 ="IMAGE##25##22##".'../inspections/photo/'.$images_path.$images[1]['graphic_name'];
					}else{
						if (file_exists('../inspections/photo/'.$images[1]['graphic_name']))
							$image1 ="IMAGE##25##22##".'../inspections/photo/'.$images[1]['graphic_name'];
					}
				}
				$drawing = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$fi["InspectionId"].' AND is_deleted = 0 AND graphic_type = "drawing" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 1');
				if(isset($drawing[0]) && $drawing[0]['graphic_name'] != ''){
					$obj->resizeImages('../inspections/drawing/'.$drawing[0]['graphic_name'], 153, 100, '../inspections/drawing/drawing_summary/'.$drawing[0]['graphic_name']);
					if(file_exists('../inspections/drawing/drawing_summary/'.$drawing[0]['graphic_name'])){
						$drawing_image ="IMAGE##25##22##".'../inspections/drawing/drawing_summary/'.$drawing[0]['graphic_name'];
					}else{
						if (file_exists('../inspections/drawing/'.$drawing[0]['graphic_name']))
							$drawing_image ="IMAGE##25##22##".'../inspections/drawing/'.$drawing[0]['graphic_name'];
					}
				}
				
				if(isset($fi['signoff']) && $fi['signoff'] != '' && $fi['Status'] == 'Closed'){
					$obj->resizeImages('../inspections/signoff/'.$fi['signoff'], 153, 100, '../inspections/signoff/signoff_summary/'.$fi['signoff']);
					if(file_exists('../inspections/signoff/signoff_summary/'.$fi['signoff'])){
						$signoff_image = "IMAGE##18##18##".'../inspections/signoff/signoff_summary/'.$fi['signoff'];
					}else{
						if (file_exists('../inspections/signoff/'.$fi['signoff']))
							$signoff_image ="IMAGE##18##18##".'../inspections/signoff/'.$fi['signoff'];
					}
				}
			}
	
			$pdf->Row(array($fi['InspectionId'], $fi['InspectonType'], $locations, $fi['Description'], $fi['InspectedBy'], stripslashes(date("d/m/y", strtotime($fi["DateRaised"]))), stripslashes( $fi['RaisedBy']), stripslashes( $fi['FixedByDate']), stripslashes( $fi['Status']), $image0, $image1, $drawing_image, $signoff_image));
			$oldIssueToName = $fi["IssueToName"];
		}

		$pdf->Ln(5);
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(35, 10, 'Defect Clause: ');
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $defect_clause);
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
		
		$file_name = 'Summary_Report_for_sub_contractor_'.str_replace(array(' ', '?', '/'), '_', $oldIssueToName).'.pdf';
		$d = '../report_pdf/'.$owner_id;
		$tempFile = $d.'/'.$file_name;
		$pdf->Output($tempFile);
		if(!empty($_REQUEST['issueToList'])){
			$displayFlag = false;
			$issueEmailFile[] = array('issueToName'=>$oldIssueToName, 'fileAttach'=>$tempFile);
			$issueEmailFile['arraySize'] = sizeof($issueEmailFile);
		}else{
			$zipFileArray[] = $tempFile;
		}
	}else{
		echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
	}
	$zipName = 'sub_contractor_report'.microtime().'.zip';
	if($displayFlag){
		if($obj->create_zip($zipFileArray, '../report_pdf/'.$owner_id.'/'.$zipName)){
			$fieSize = filesize('../report_pdf/'.$owner_id.'/'.$zipName);
			$fieSize = floor($fieSize/(1024));
			if ($fieSize > 1024){
				$fieSize = floor($fieSize/(1024)) . "Mbs";
			}else{
				$fieSize .= "Kbs";
			}
			$rply = 'Zip Size '.$fieSize;
			echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="closePopUp();" href="./report_pdf/'.$owner_id.'/'.$zipName.'" target="_blank" class="view_btn"></a></div>';
		}else{
			echo '<br clear="all" /><div style="margin-left:10px;">Summary Report generation failed, please try again later</div>';
		}
	}else{
		echo '<div id="issueArr" style="display:none;">'.json_encode($issueEmailFile).'</div>';
	}
}
function rrmdir($dir){
	if (is_dir($dir)) {
		$objects = scANDir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
		reset($objects);
		rmdir($dir);
	}
}
?> 
