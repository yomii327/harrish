<?php
ob_start();
session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
#include('../includes/functions.php');
#$object= new DB_Class();
include('../includes/commanfunction.php');

$obj= new COMMAN_Class();

#require_once('html2pdf.class.php');

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}
elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}

	$issued_to_add = '';	$issToForInspWhere = '';
	$postCount = 0;
	$report_type = $_POST['report_type'];
	$images_path = "photo_detail/";
	$dimages_path = "drawing_detail/";
	//if ($report_type == "pdfDetailHD"){
	//	$images_path = "";
	//	$dimages_path = "";
	//}
	if(!empty($_REQUEST['projName'])){
		$where=" and I.project_id='".$_REQUEST['projName']."'";
	}
	
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
	
	/*if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
		//$postCount++;
		//$where.=" and I.location_id in (".$obj->subLocationsId($_REQUEST['location'], ", ").")";
		$locData = $obj->getLocationIdsOfAll($_REQUEST['location']);
		#print_r($locData);die;
		$where.=" and I.location_id in (".join(",", $locData) .")";
	}
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

	/*if(!empty($_REQUEST['status'])){
		$postCount++;
		$where.=" and F.inspection_status='".$_REQUEST['status']."'";
	}*/
	// 13/09/2016 updated
	if(!empty($_REQUEST['status']) && $_REQUEST['status']!='null'){
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
		$where.=" and I.inspection_inspected_by='".$_REQUEST['inspectedBy']."'";
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
		if($_REQUEST['raisedBy'] != 'All Defect')
			$postCount++;
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
		$locationRows = $obj->selQRYMultiple ("location_id", "project_locations", "project_id=".$_REQUEST['projName'] . " and location_title LIKE '%".$_REQUEST['searchKeyward']."%' and is_deleted=0" );
		$location_id_arr = array();
		foreach ($locationRows as $locationID)
		{
			$location_id_arr[] = $locationID["location_id"];	
		}
		$where.=" AND I.location_id in (".join(",", $location_id_arr) .")";
	}

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
	$orderby = "";	
	if ($_REQUEST["sortby"])
	{
		$orderby = "order by I." . $_REQUEST["sortby"];
	}
	if($_SESSION['userRole'] == 'Sub Contractor'){
		$where.=" and  F.issued_to_name = '".$_SESSION['userIssueTo']."'";
	}
	
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
		F.inspection_id as InspectionId_FOR,
		I.inspection_raised_by as RaisedBy,
		P.defect_clause as defect_clause
	FROM
		user_projects as P, issued_to_for_inspections as F,
		project_inspections as I
	WHERE
		I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.is_deleted = '0' and F.is_deleted = '0' and P.is_deleted = '0' $where group by I.inspection_id $orderby";		
	
$ri=mysql_query($qi);
$noInspection = mysql_num_rows($ri);
$ajaxReplay = $noInspection.' Records';
$noPages = $noInspection;
#$fi=mysql_fetch_assoc($ri);
/*$qi="SELECT *,r.resp_full_name FROM ".DEFECTS." d 
	Left JOIN ".RESPONSIBLES." r ON r.resp_id = d.resp_id 
	WHERE d.owner_id = '$owner_id' $where ";
*/

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

if($noInspection > 0){

$html='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
@charset "utf-8";
body{
	font-family : Trebuchet MS, Arial, serif;
}
table.collapse {
	border-collapse: collapse;
	border: 1pt solid black;
}
table.collapse td{
	border: 1pt solid black;
	padding: 2px;
}
.cst_filter_section td{
	padding: 2px 5px;
	line-height: 10px;
}
 #footer { position: fixed; left: 0px; bottom: -150px; right: 0px; height: 150px; background-color: #FFFFFF; color:#000000; text-align:center; font:helvetica; font-weight:bold; Font-size:14px;}
</style></head>
<body>
<script type="text/php">

        if ( isset($pdf) ) {

          $font = Font_Metrics::get_font("helvetica", "bold");
          $pdf->page_text(200, 820, "", $font, 10, array(0,0,0));
        }
        </script>
	<table width="555" border="0" align="center">
			<tr>
				<td width="30%"></td><td width="70%" align="right" style="padding-right:20px;">';
			$html .='<img src="../company_logo/logo.png" height="40"  /></td>
			</tr>
			<tr>
				<td width="40%" style="font-size:14px"><u><b>Internal Report</b></u></td>
				<td>&nbsp;</td>
			</tr>
			<tr class="cst_filter_section">
				<td width="60%"  style="font-size:12px"><strong>Project Name: </strong>'.$obj->getDataByKey('user_projects', 'project_id', $_POST['projName'], 'project_name').'</td>
				<td width="40%"  style="font-size:12px"><strong>Date: </strong>'.date('d/m/Y').'</td>
			</tr>
			<tr class="cst_filter_section">
				<td style="font-size:12px"><strong>Inspections: </strong>'.$noInspection.'</td>
				<td>&nbsp;</td>
			</tr>
			<tr class="cst_filter_section">
				<td style="font-size:12px"><strong>Page: </strong>1 of '.$noPages.'</td>
				<td>&nbsp;</td>
			</tr>
			<tr class="cst_filter_section">
				<td style="font-size:12px"><strong>Report filtered by: </strong></td>
				<td>&nbsp;</td>
			</tr>
			<tr class="cst_filter_section">
				<td style="padding-left:30px;" colspan="2"><table width="510" border="0"><tr>';
				$jk=0;
	if(!empty($_POST['location']) && empty($_POST['subLocation'])){
		$html .= '<td width="100" style="font-size:11px;" valign="top"><b>Location Name : </b></td>
			<td width="110" style="font-size:11px;" valign="top">'.$locationSearchName.'</td>';$jk++;
	}
	if(!empty($_POST['subLocation']) && !empty($_POST['sub_subLocation'])){
		$html .= '<td width="100" style="font-size:11px;" valign="top"><b>Location Name : </b></td>
		<td width="110" style="font-size:11px;">'.$locationSearchName.'</td>';$jk++;
		$html .= '<td width="100" style="font-size:11px;" valign="top"><b>Sub Location 1 : </b></td>
		<td width="110" style="font-size:11px;" valign="top">'.$subLocationSearchName.'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		$html .= '<td width="100" style="font-size:11px;" valign="top"><b>Sub Location 2 : </b></td>
		<td width="110" style="font-size:11px;" valign="top">'.$subSubLocationSearchName.'</td>';$jk++;
	}else{
		if(!empty($_POST['location']) && !empty($_POST['subLocation'])){
			$html .= '<td width="100" style="font-size:11px;" valign="top"><b>Location Name : </b></td>
			<td width="110" style="font-size:11px;" valign="top">'.$locationSearchName.'</td>';$jk++;
			$html .= '<td width="100" style="font-size:11px;" valign="top"><b>Sub Location Name : </b></td>
			<td width="110" style="font-size:11px;" valign="top">'.$subLocationSearchName.'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
	}
/*	 	if(!empty($_POST['location']) && isset($_POST['location'])){
			$html .= '<td width="140" style="font-size:11px;"><b>Location Name : </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['location'], 'location_title').'</td>';$jk++;
		}*/
		if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
			$html .= '<td width="100" style="font-size:11px;" valign="top"><b>Status : </b></td>
			<td width="110" style="font-size:11px;" valign="top">'.$_REQUEST['status'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['inspectedBy']) && isset($_POST['inspectedBy'])){
			$html .= '<td width="100" style="font-size:11px;"><b>Inspect By : </b></td>
			<td width="110" style="font-size:11px;">'.$_POST['inspectedBy'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['issuedTo']) && isset($_POST['issuedTo'])){
			$html .= '<td width="100" style="font-size:11px;" valign="top"><b>Issue To : </b></td>
			<td width="110" style="font-size:11px;" valign="top">'.str_replace("'", "", $mulIssueTo).'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		
		if(!empty($_POST['priority']) && isset($_POST['priority'])){
			$html .= '<td width="100" style="font-size:11px;"><b>Priority : </b></td>
			<td width="110" style="font-size:11px;">'.$_POST['priority'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['inspecrType']) && isset($_POST['inspecrType'])){
			$html .= '<td width="100" style="font-size:11px;"><b>Inspection Type : </b></td>
			<td width="110" style="font-size:11px;">'.$_POST['inspecrType'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['costAttribute']) && isset($_POST['costAttribute'])){
			$html .= '<td width="100" style="font-size:11px;"><b>Cost Attribute : </b></td>
			<td width="110" style="font-size:11px;">'.$_POST['costAttribute'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_REQUEST['raisedBy']) && isset($_REQUEST['raisedBy'])){
			$html .= '<td width="100" style="font-size:11px;"><b>Raised By : </b></td>
			<td width="110" style="font-size:11px;">'.$_REQUEST['raisedBy'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['DRF']) && isset($_POST['DRF']) || !empty($_POST['DRT']) && isset($_POST['DRT']))
		{
			$html .= '<td width="100" style="font-size:11px;"><b>Date Raised : </b></td>
			<td width="110" style="font-size:11px;">'.$_POST['DRF'].' to '.$_POST['DRT'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['FBDF']) && isset($_POST['FBDF']) || !empty($_POST['FBDT']) && isset($_POST['FBDT']))
		{
		   $html .= '<td width="100" style="font-size:11px;""><b>Fixed By Date : </b></td>
		   <td width="110" style="font-size:11px;"">'.$_POST['FBDF'].' to '.$_POST['FBDT'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		$html .= '</tr></table></td></tr></table>';
		$i=1;
		$pageCount = 1;
		$defect_clause = '';
		while( $fi=mysql_fetch_row($ri)){
			if(isset($fi[14]) && !empty($fi[14]) && empty($defect_clause)){
				$defect_clause = $fi[14];
			}

			$where = "";
			if(!empty($_POST['costAttribute'])){
				$where .= " and cost_attribute = '".$_POST['costAttribute']."'";
			}
			/*if($_POST['issuedTo']!=""){
				$where .= " and issued_to_name = '".$_POST['issuedTo']."'";
			}*/
			/*if($_REQUEST['issuedTo']!=""){
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
		#		$where.=" and F.issued_to_name='".$_REQUEST['issuedTo']."' and F.inspection_id = I.inspection_id";
				#$where.=" and F.issued_to_name IN (".$mulIssueTo.") and F.inspection_id = I.inspection_id";
				$where .= " and issued_to_name IN (".$mulIssueTo." )";
			}
			if(!empty($_POST['status'])){
				$where .= " and inspection_status = '".$_POST['status']."'";
			}*/
			if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
				$where.=" and inspection_fixed_by_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
			}
			$issueToData = $obj->selQRYMultiple('issued_to_name, inspection_fixed_by_date, cost_attribute, inspection_status', 'issued_to_for_inspections', 'inspection_id = '.$fi[11] . ' and is_deleted=0 ' . $where . $issToForInspWhere .' group by issued_to_name');

			$issueToData_issueToName = ""; $issueToData_fixedByDate= ""; $issueToData_status= "";$issueToData_costAttribute = "";
			if(!empty($issueToData)){
				foreach($issueToData as $issueData){
					if($issueToData_issueToName == ''){
						$issueToData_issueToName = stripslashes($issueData['issued_to_name']);
					}else{
						$issueToData_issueToName .= ' > '.stripslashes($issueData['issued_to_name']);
					}
	
					if($issueToData_fixedByDate == ''){
						$issueData['inspection_fixed_by_date'] != '0000-00-00' ? $issueToData_fixedByDate = stripslashes(date("d/m/Y", strtotime($issueData['inspection_fixed_by_date']))) : $issueToData_fixedByDate = '' ;
					}else{
						$issueData['inspection_fixed_by_date'] != '0000-00-00' ? $issueToData_fixedByDate .= ' > '.stripslashes(date("d/m/Y", strtotime($issueData['inspection_fixed_by_date']))) : $issueToData_fixedByDate = '' ;
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

			if ($i!=1){
				$pageCount++;
				$iHeight = "270px";
				$html .= '<div style="page-break-before: always;"></div>Page: </strong>'.$pageCount.' of '.$noPages . "<br/>" . $i . ".";
			}else{
				$iHeight = "270px";
				$html .= $i . ".<br/>";
			}
	$html .='<table width="555" class="collapse" cellpadding="0" cellspaccing="0" align="center">
				<tr>
					<td style="background-color:#CCCCCC;width:100px;font-size:11px;"><i>&nbsp;Location</i></td>
					<td colspan="2" style="font-size:10px;width:150px">'.stripslashes(wordwrap($obj->subLocations($fi[1], ' > '), 25, '<br />&nbsp;')).'</td>
					<td colspan="3" style="background-color:#CCCCCC;width:300px;font-size:11px;"><i>&nbsp;Description</i></td>
				</tr>
				<tr>
					<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Date&nbsp;Raised</i></td>
					<td colspan="2" style="font-size:10px;">&nbsp;';
					if($fi[2] != '0000-00-00'){
						$html .=stripslashes(date("d/m/Y", strtotime($fi[2])));
					}
					$html .='</td>
					<td colspan="3" rowspan="7" valign="top" style="font-size:10px;">&nbsp;'.stripslashes(wordwrap($fi[9], 90, '<br />&nbsp;')).'</td>
				</tr>
				<tr>
					<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Inspected&nbsp;By</i></td>
					<td colspan="2" style="font-size:10px;">&nbsp;'.stripslashes($fi[3]).'</td>
				</tr>
				<tr>
					<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Inspection&nbsp;Type</i></td>
					<td colspan="2" style="font-size:10px;">&nbsp;'.stripslashes($fi[4]).'</td>
				</tr>
				<tr>
					<td style="background-color:#CCCCCC;font-size:11px;"><i>Raised&nbsp;By</i></td>
					<td colspan="2" style=" font-size:10px;">&nbsp;'.stripslashes($fi[13]).'</td>
				</tr>
				<tr>
					<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Issued&nbsp;To</i></td>
					<td colspan="2" style=" font-size:10px;">&nbsp;'.$issueToData_issueToName.'</td>
				</tr>
				<tr>
					<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Fix&nbsp;by&nbsp;Date</i></td>
					<td colspan="2" style="font-size:10px;">&nbsp;'.$issueToData_fixedByDate.'</td>
				</tr>
				<tr>
					<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Status</i></td>
					<td colspan="2" style="font-size:10px;">&nbsp;'.$issueToData_status.'</td>
				</tr>
				<tr>
					<td colspan="3" align="center"  style="width:330px;height:'. $iHeight.'">';
$images = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$fi[11].' AND is_deleted = 0 AND graphic_type = "images" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 2');
			if(isset($images[0]) && $images[0]['graphic_name'] != ''){
$obj->resizeImages('../inspections/photo/'.$images[0]['graphic_name'], 350, 350, '../inspections/photo/photo_detail/'.$images[0]['graphic_name']);
				if(file_exists('../inspections/photo/'.$images_path.$images[0]['graphic_name'])){
					$html .='<img src="../inspections/photo/'.$images_path.$images[0]['graphic_name'].'"';
					if ($i==1)
					{
						$html .=' style="width:300px;height: ' . $iHeight . '"';
					}
					$html .='/>';
				}else{
					$html .='<img src="../inspections/photo/'.$images[0]['graphic_name'].'" style="width:300px;" />';
				}
			}
			$html .='</td><td colspan="3" align="center" style="width:330px;height:'. $iHeight.'">';
			if(isset($images[1]) && $images[1]['graphic_name'] != ''){
$obj->resizeImages('../inspections/photo/'.$images[1]['graphic_name'], 350, 350, '../inspections/photo/photo_detail/'.$images[1]['graphic_name']);
				if(file_exists('../inspections/photo/'.$images_path.$images[1]['graphic_name'])){
					$html .='<img src="../inspections/photo/'.$images_path.$images[1]['graphic_name'].'"';
					if ($i==1)
					{
						$html .=' style="width:300px;height: ' . $iHeight . '"';
					}
					$html .='/>';
				}else{
					$html .='<img src="../inspections/photo/'.$images[1]['graphic_name'].'"  style="width:300px;"/>';
				}
			}
$drawing = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$fi[11].' AND is_deleted = 0 AND graphic_type = "drawing" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 1');
			$html .='</td></tr><tr><td colspan="6" align="center" style="width:330px;height:'. $iHeight.'">';
			if(isset($drawing[0]) && $drawing[0]['graphic_name'] != ''){
$obj->resizeImages('../inspections/drawing/'.$drawing[0]['graphic_name'], 350, 350, '../inspections/drawing/drawing_detail/'.$drawing[0]['graphic_name']);
				if(file_exists('../inspections/drawing/'.$dimages_path.$drawing[0]['graphic_name'])){
					$html .='<img src="../inspections/drawing/'.$dimages_path.$drawing[0]['graphic_name'].'" ';
					if ($i==1)
					{
						$html .=' style="width:300px;height: ' . $iHeight . '"';
					}
					$html .='/>';
				}else{
					$html .='<img src="../inspections/drawing/'.$drawing[0]['graphic_name'].'"  style="width:300px;" />';
				}
			}
			$html .='</td>
				</tr>
			</table><div id="footer">
    <p class="page">DefectID © Copyright Wiseworking 2012 / 2013</p>
  </div>';
	$i++;	}

	$html .= '<table border="0" cellpadding="0" cellspaccing="0" width="100%"><tr>
				<td style="font-size:12px"><strong>Project Defect Clause: </strong>'.$defect_clause.'</td>
				<td>&nbsp;</td>
			</tr></table>';

$html .= '</body></html>';
#echo $html;die;
$report = 'Internal_Report_'.microtime().'.pdf';
#$report = 'Report_Detail.pdf';

$fieSize = createPDF($html, $report, $owner_id);
$fieSize = floor($fieSize/(1024));
if ($fieSize > 1024){
	$fieSize = floor($fieSize/(1024)) . "Mbs";
}else{
	$fieSize .= "Kbs";
}
$rply = $ajaxReplay.' '.$fieSize;


echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="closePopUp();" href="./report_pdf/'.$owner_id.'/'.$report.'" target="_blank" class="view_btn"></a></div>';

}else{
	echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
}

function createPDF($html, $report, $owner_id){
	require_once("../dompdf/dompdf_config.inc.php");
	$paper='a4';
	$orientation='portrait';
	
	if ( get_magic_quotes_gpc() )
	$html = stripslashes($html);
	
	$old_limit = ini_set("memory_limit", "94G");
	ini_set('max_execution_time', 3600); //300 seconds = 5 minutes
	
	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->set_paper($paper, $orientation);
	$dompdf->render();
	
	//$dompdf->stream("report.pdf");
	//exit(0);
	$output = $dompdf->output($report);
	// generate pdf in folder
	$d = '../report_pdf/'.$owner_id;
	if(!is_dir($d))
		mkdir($d);
	if (file_exists($d.'/'.$report))
		unlink($d.'/'.$report);
	$tempFile = $d.'/'.$report;
	$fh = fopen($tempFile, 'w') or die("can't open file");
	$stringData = $output;
	fwrite($fh, $stringData);
	fclose($fh);
	
	return filesize($tempFile);
}
?>
