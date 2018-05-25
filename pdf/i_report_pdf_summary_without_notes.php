<?php
ob_start();
session_start();

#echo 		$_SERVER['DOCUMENT_ROOT'];die;
#include('../includes/functions.php');
include('../includes/commanfunction.php');
#$object= new DB_Class();
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
if(isset($_REQUEST['name'])){
	$locNameIDs = '';
	$subwhere = '';
	$splCon='';
	
	if(!empty($_REQUEST['projName'])){
		if ($_REQUEST['projName'] == "all"){
			if ($owner_id != "company")
				$where=" and P.user_id=" . $owner_id;
		}else{
			$where=" and I.project_id='".$_REQUEST['projName']."'";
		}
	}

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
	$searchLocID = "";

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
	
	/*if(!empty($_REQUEST['location'])){
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
			$where.=" AND I.location_id IN (".join(",", $tempLocArr).")";
		}else{
			$where.=" AND I.location_id IN (".$obj->subLocationsId($searchLocID, ",").")";
		}
	}*/
	if(!empty($_REQUEST['status'])){
		$postCount++;
		$where.=" and F.inspection_status='".$_REQUEST['status']."'";
	}
	
	if(!empty($_REQUEST['inspectedBy'])){
		$postCount++;
		$where.=" and I.inspection_inspected_by='".$_REQUEST['inspectedBy']."'";
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
		$where.=" and F.issued_to_name IN (".$mulIssueTo.") and F.inspection_id = I.inspection_id";
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
		if($_SESSION['userRole'] != 'All Defect' && $_SESSION['userRole'] != "Sub Contractor"){
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

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
	$orderby = "";	
	if ($_REQUEST["sortby"]){
		if ($_REQUEST["sortby"] == "issued_to_name")
			$orderby = "order by cn, F.issued_to_name, I.location_id";
		else if ($_REQUEST["sortby"] == "location_id")
			$orderby = "order by I.inspection_location";
		else
			$orderby = "order by I." . $_REQUEST["sortby"];
	}
	if($_SESSION['userRole'] == 'Sub Contractor'){
		$where.=" and  F.issued_to_name = '".$_SESSION['userIssueTo']."'";
	}
	$where .=  " and I.inspection_type!='Memo'";
	$qi="SELECT
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
		GROUP_CONCAT(distinct F.issued_to_name SEPARATOR ' > ') as issueName,
		GROUP_CONCAT(date_format(F.inspection_fixed_by_date,'%d/%m/%Y') SEPARATOR ' > ') fixedDate,
		GROUP_CONCAT(F.cost_attribute SEPARATOR ' > ') as costAttr,
		GROUP_CONCAT(F.inspection_status SEPARATOR ' > ') as satus,
		GROUP_CONCAT(F.cost_impact_type SEPARATOR ' > ') as costImpact,
		GROUP_CONCAT(F.cost_impact_price SEPARATOR ' > ') as costPrice,
		count(distinct F.issued_to_name) as cn,
		P.defect_clause as defect_clause
	FROM
		projects as P
		inner join project_inspections as I on I.project_id = P.project_id
		inner join issued_to_for_inspections as F on I.inspection_id = F.inspection_id
	WHERE
		I.is_deleted = '0' and
		F.is_deleted = '0' and
		P.is_deleted = '0'
	$where
	group by I.inspection_id
		$orderby";
$ri=mysql_query($qi);
/*$qi="SELECT
		P.project_id as ProjectId,
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
		I.inspection_raised_by as RaisedBy
	FROM
		user_projects as P, issued_to_for_inspections as F,
		project_inspections as I
	WHERE
		I.project_id = P.project_id and
		I.inspection_id = F.inspection_id and
		I.is_deleted = '0' and
		F.is_deleted = '0' and
		P.is_deleted = '0' $where group by I.project_id, I.inspection_id $orderby";

$ri = mysql_query($qi);*/

$noInspection = mysql_num_rows($ri);
$ajaxReplay = $noInspection.' Records';
$noPages = ceil(($noInspection-4)/6 +1);
if($noInspection > 0){
//Meta Data Section
	$locArrayData = array();
			
	if ($_REQUEST['projName'] == "all"){
		if ($onwer_id != "company"){
			$proData = $obj->selQRYMultiple('project_id, project_name', 'user_projects', 'is_deleted = 0 and user_id='.$owner_id);
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
	require('../fpdf/mc_table.php');
//Config Section
	class PDF extends PDF_MC_Table{
		
		function Header(){
			if($this->PageNo()!=1){
				$this->Cell(0, 10, $this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
				$this->ln();	
				$this->SetFont('times', 'B', 10);
				$header = array("ID", "Inspection Type", "Location", "Description", "Inspected By", "Date Raised", "Raised By", "Issused To" , "Fix By Date" , "Status" , "Image 1", "Image 2" , "Drawing" , "Sign Off");
				$w = $this->header_width();
				$this->SetWidths($w);
				$best_height = 17;
				$this->row($header, $best_height);
			}
		}
		
		function Footer(){
			$this->SetY(-15);
			$this->SetFont('times','B',10);
			$this->Cell(0, 10, 'DefectID � Copyright Wiseworking 2012 / 2013', 0, 0, 'C');
		}
		
		function header_width(){
			return array(12, 18, 23, 35, 18, 14, 14, 16, 15, 13, 29, 29, 29, 20);
		}
	}
//Top Header Sectiond Start Here
	$pdf = new PDF("L", "mm", "A4");
	$pdf->AliasNbPages();
	$pdf->AddPage();
	
	$pdf->SetTopMargin(20);

	$pdf->Image('../company_logo/logo.png', 235, 5, 'png', -170);
	$pdf->Ln(8);
	
	$pdf->SetFont('times', 'BU', 12);
	$pdf->Cell(40, 10, 'Summary Report without Notes');		
	$pdf->Ln(6);
	
	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(26, 10, 'Project Name : ');	
	
	$pdf->SetFont('times', '', 10);
	$pdf->Cell(10, 10, $projects_name);	
	$pdf->Ln(5);
	
	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(11, 10, 'Date : ');	
	
	$pdf->SetFont('times', '', 10);
	$pdf->Cell(20, 10, date('d/m/Y'));	
	$pdf->Ln(5);
	
	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(23, 10, 'Inspections : ');	
	
	$pdf->SetFont('times', '', 10);
	$pdf->Cell(20, 10, $noInspection);	
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
		$pdf->Cell(25, 10, $sortby);
		
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
	}
	
	if(!empty($_REQUEST['searchKeyward']) && isset($_REQUEST['searchKeyward'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Search keyword: ');
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['searchKeyward']);
		
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
	}

if(!empty($_REQUEST['location'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Location Name: ');	
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['locationName']);	
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}	
	}
	if(!empty($_REQUEST['subLocation'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Sub Location: ');	
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['subLocationName']);	
		$jk++; if($jk%2 ==0){	$pdf->ln();$pdf->Cell(15, 10, '');	}
	}
	if(!empty($_REQUEST['sub_subLocation'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Sub Location 1: ');	
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['sub_subLocationName']);	
		$jk++; if($jk%2 ==0){	$pdf->ln();$pdf->Cell(15, 10, '');	}
	}
	if(!empty($_REQUEST['subLocation3']) && $_REQUEST['subLocation3']!= 'null'){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Sub Location 2: ');	
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['subLocation3Name']);	
		$jk++; if($jk%2 ==0){	$pdf->ln();$pdf->Cell(15, 10, '');	}
	}
	if(!empty($_REQUEST['subLocation4']) && $_REQUEST['subLocation4']!= 'null'){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Sub Location 3: ');	
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['subLocation4Name']);	
		$jk++; if($jk%2 ==0){	$pdf->ln();$pdf->Cell(15, 10, '');	}
	}
	
	if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Status: ');
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['status']);
		
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');		}		
	}
	
	if(!empty($_REQUEST['inspectedBy']) && isset($_REQUEST['inspectedBy'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Inspected By: ');
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['inspectedBy']);
		
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
	}

	if(!empty($_REQUEST['issuedTo']) && isset($_REQUEST['issuedTo'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Issue To: ');
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(140, 10, str_replace("'", "", $mulIssueTo));
		
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
	}
	
	if(!empty($_REQUEST['priority']) && isset($_REQUEST['priority'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Priority: ');
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['priority']);	
		
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}						
	}
	
	if(!empty($_REQUEST['inspecrType']) && isset($_REQUEST['inspecrType'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Inspection Type: ');
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['inspecrType']);
		
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
	}
	
	if(!empty($_REQUEST['raisedBy']) && isset($_REQUEST['raisedBy'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Raised By: ');
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['raisedBy']);
		
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
	}
	
	if(!empty($_REQUEST['costAttribute']) && isset($_REQUEST['costAttribute'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Cost Attribute: ');
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['costAttribute']);
		
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
	}
	
	if(!empty($_REQUEST['DRF']) && isset($_REQUEST['DRF']) || !empty($_REQUEST['DRT']) && isset($_REQUEST['DRT'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Date Raised: ');
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['DRF'].' to '.$_REQUEST['DRT']);
		
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
	}
	$pdf->ln();$pdf->Cell(15, 10, '');
	if(!empty($_REQUEST['FBDF']) && isset($_REQUEST['FBDF']) || !empty($_REQUEST['FBDT']) && isset($_REQUEST['FBDT'])){
		$pdf->SetFont('times', 'B', 11);		
		$pdf->Cell(50, 10, 'Fixed By Date: ');
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(25, 10, $_REQUEST['FBDF'].' to '.$_REQUEST['FBDT']);
		
		$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
	}
	
	$pdf->ln();
	
//Top Header Sectiond End Here

//Data Section Start Here
	$pdf->SetFont('times', 'B', 10);
		
	$header = array("ID", "Inspection Type", "Location", "Description", "Inspected By", "Date Raised", "Raised By", "Issused To", "Fix By Date", "Status", "Image 1", "Image 2", "Drawing", "Sign Off");

	$w = $pdf->header_width();
	$pdf->SetWidths($w);		
	$best_height = 17;
	$pdf->row($header, $best_height);
	$report_type = 'pdfSummayWithImages';
	$images_path = "photo_summary/";
	$dimages_path = "signoff_summary/";

	$pdf->SetFont('times', '', 9);

	$defect_clause = '';
	while($fi=mysql_fetch_assoc($ri)){
		if(isset($fi['defect_clause']) && !empty($fi['defect_clause']) && empty($defect_clause)){
			$defect_clause = $fi['defect_clause'];
		}

		$locations = $obj->subLocations($fi["Location"], ' > ');
		$where = "";
		if(!empty($_REQUEST['costAttribute'])){
			$where .= " and cost_attribute = '".$_REQUEST['costAttribute']."'";
		}
		/*if($_REQUEST['issuedTo'] != ""){
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
			$where .= " and issued_to_name IN (".$mulIssueTo.")";
		}
		if(!empty($_REQUEST['status'])){
			$where .= " and inspection_status = '".$_REQUEST['status']."'";
		}*/
		if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
			$where .= " and inspection_fixed_by_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
		}
		
		$issueToData = $obj->selQRYMultiple('issued_to_name, inspection_fixed_by_date, cost_attribute, inspection_status, closed_date', 'issued_to_for_inspections', 'inspection_id = '.$fi['InspectionId'] . ' and is_deleted=0 ' . $where .' group by issued_to_name');
		$issueToData_issueToName = ""; $issueToData_fixedByDate= ""; $issueToData_status= "";$issueToData_costAttribute='';$issueToData_closedDate='';
		if(!empty($issueToData)){
			foreach($issueToData as $issueData){
				if($issueToData_issueToName == ''){
					$issueToData_issueToName = stripslashes($issueData['issued_to_name']);
				}else{
					$issueToData_issueToName .= ' > '.stripslashes($issueData['issued_to_name']);
				}
				
				if($issueToData_fixedByDate == ''){
					$issueData['inspection_fixed_by_date'] != '0000-00-00' ? $issueToData_fixedByDate = stripslashes(date("d/m/y", strtotime($issueData['inspection_fixed_by_date']))) : $issueToData_fixedByDate = '' ;
				}else{
					$issueData['inspection_fixed_by_date'] != '0000-00-00' ? $issueToData_fixedByDate .= ' > '.stripslashes(date("d/m/y", strtotime($issueData['inspection_fixed_by_date']))) : $issueToData_fixedByDate = '' ;
				}
				
				if($issueToData_closedDate == ''){
					$issueData['closed_date'] != '0000-00-00' ? $issueToData_closedDate = stripslashes(date("d/m/y", strtotime($issueData['closed_date']))) : $issueToData_closedDate = '' ;
				}else{
					$issueData['closed_date'] != '0000-00-00' ? $issueToData_closedDate .= ' > '.stripslashes(date("d/m/y", strtotime($issueData['closed_date']))) : $issueToData_closedDate = '' ;
				}
				
				if($issueToData_status == ''){
					$issueToData_status = stripslashes($issueData['inspection_status']);
				}else{
					$issueToData_status .= ' > '.stripslashes($issueData['inspection_status']);
				}

				if($issueToData_costAttribute == ''){
					$issueToData_costAttribute = stripslashes($issueData['cost_attribute']);
				}else{
					$issueToData_costAttribute .= ' > '.stripslashes($issueData['cost_attribute']);
				}
			}
		}

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
			
			if(isset($fi['signoff']) && $fi['signoff'] != ''){
				$obj->resizeImages('../inspections/signoff/'.$fi['signoff'], 153, 100, '../inspections/signoff/signoff_summary/'.$fi['signoff']);
				if(file_exists('../inspections/signoff/signoff_summary/'.$fi['signoff'])){
					$signoff_image = "IMAGE##18##18##".'../inspections/signoff/signoff_summary/'.$fi['signoff'];
				}else{
					if (file_exists('../inspections/signoff/'.$fi['signoff']))
						$signoff_image ="IMAGE##18##18##".'../inspections/signoff/'.$fi['signoff'];
				}
			}
		}

		$pdf->Row(array($fi['InspectionId'], $fi['InspectonType'], $locations, $fi['Description'], $fi['InspectedBy'], stripslashes(date("d/m/y", strtotime($fi["DateRaised"]))), stripslashes( $fi['RaisedBy']), $issueToData_issueToName, $issueToData_fixedByDate, $issueToData_status, $image0, $image1, $drawing_image, $signoff_image));
	}

	$pdf->Ln(5);
	$pdf->SetFont('times', 'B', 11);		
	$pdf->Cell(35, 10, 'Defect Clause: ');
	$pdf->SetFont('times', '', 10);
	//$pdf->Cell(25, 10, $defect_clause);
	$pdf->MultiCell(0, 5, $defect_clause);	
	$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}
	
//Data Section End Here

//PDF Creattion Section Start Here
	$file_name = 'Summary_Report_without_Notes'.microtime().'.pdf';
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
//PDF Creattion Section Start Here
	echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="closePopUp();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';
	}else{
		echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
	}
}

function rrmdir($dir){
	if (is_dir($dir)) {
		$objects = scandir($dir);
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
