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

$issued_to_add = '';
	$postCount = 0;
	$report_type = $_POST['report_type'];
	$images_path = "photo_detail/";
	$dimages_path = "drawing_detail/";
	if ($report_type == "pdfDetailHD"){
		$images_path = "";
		$dimages_path = "";
	}
	if(!empty($_REQUEST['projName'])){
		$where=" and I.project_id='".$_REQUEST['projName']."'";
	}
	if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
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

	if(!empty($_REQUEST['status'])){
		$postCount++;
		$where.=" and F.inspection_status='".$_REQUEST['status']."'";
	}
	if(!empty($_REQUEST['inspectedBy'])){
		$postCount++;
		$where.=" and I.inspection_inspected_by='".$_REQUEST['inspectedBy']."'";
	}
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
		$where.=" and I.inspection_location LIKE '%".$_REQUEST['searchKeyward']."%'";
/*		$locationRows = $obj->selQRYMultiple ("location_id", "project_locations", "project_id=".$_REQUEST['projName'] . " and location_title LIKE '%".$_REQUEST['searchKeyward']."%' and is_deleted=0" );
		$location_id_arr = array();
		foreach ($locationRows as $locationID)
		{
			$location_id_arr[] = $locationID["location_id"];	
		}
		$where.=" AND I.location_id in (".join(",", $location_id_arr) .")";*/
	}
	
	

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
	$orderby = "";	
	if ($_REQUEST["sortby"])
	{
		if($_REQUEST["sortby"] == 'location_id'){
			$orderby = "order by I." . $_REQUEST["sortby"];
		}else{
			$orderby = "order by F." . $_REQUEST["sortby"];	
		}
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
	I.inspection_raised_by as RaisedBy
	FROM
		user_projects as P, issued_to_for_inspections as F,
		project_inspections as I
	WHERE
		I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.is_deleted = '0' and F.is_deleted = '0' and P.is_deleted = '0' $where group by I.inspection_id $orderby";
		
$ri=mysql_query($qi);
$noInspection = mysql_num_rows($ri);
$ajaxReplay = $noInspection.' Records';
$noPages = ceil(($noInspection-1)/2 +1);
#$fi=mysql_fetch_assoc($ri);
/*$qi="SELECT *,r.resp_full_name FROM ".DEFECTS." d 
	Left JOIN ".RESPONSIBLES." r ON r.resp_id = d.resp_id 
	WHERE d.owner_id = '$owner_id' $where ";
*/
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
</style></head>
<body>
<script type="text/php">

        if ( isset($pdf) ) {

          $font = Font_Metrics::get_font("helvetica", "bold");
          $pdf->page_text(200, 820, "DefectID – Copyright Wiseworking 2012 / 2013", $font, 10, array(0,0,0));
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
			<tr>
				<td style="font-size:12px"><strong>Project Name: </strong>'.$obj->getDataByKey('user_projects', 'project_id', $_POST['projName'], 'project_name').'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Date: </strong>'.date('d/m/Y').'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Inspections: </strong>'.$noInspection.'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Page: </strong>1 of '.$noPages.'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Report filtered by: </strong></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="padding-left:30px;" colspan="2"><table width="510" border="0"><tr>';
				$jk=0;
	if(!empty($_POST['location']) && empty($_POST['subLocation'])){
		$html .= '<td width="140" style="font-size:11px;"><b>Location Name : </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['location'], 'location_title').'</td>';$jk++;
	}
	if(!empty($_POST['subLocation']) && !empty($_POST['sub_subLocation'])){
		$html .= '<td width="140" style="font-size:11px;"><b>Location Name : </b></td>
		<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['location'], 'location_title').'</td>';$jk++;
		$html .= '<td width="140" style="font-size:11px;"><b>Sub Location 1 : </b></td>
		<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['subLocation'], 'location_title').'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		$html .= '<td width="140" style="font-size:11px;"><b>Sub Location 2 : </b></td>
		<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['sub_subLocation'], 'location_title').'</td>';$jk++;
	}else{
		if(!empty($_POST['location']) && !empty($_POST['subLocation'])){
			$html .= '<td width="140" style="font-size:11px;"><b>Location Name : </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['location'], 'location_title').'</td>';$jk++;
			$html .= '<td width="140" style="font-size:11px;"><b>Sub Location Name : </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['subLocation'], 'location_title').'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
	}
/*	 	if(!empty($_POST['location']) && isset($_POST['location'])){
			$html .= '<td width="140" style="font-size:11px;"><b>Location Name : </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['location'], 'location_title').'</td>';$jk++;
		}*/
		if(!empty($_POST['status']) && isset($_POST['status'])){
			$html .= '<td width="140" style="font-size:11px;"><b>Status : </b></td>
			<td width="110" style="font-size:11px;">'.$_POST['status'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['inspectedBy']) && isset($_POST['inspectedBy'])){
			$html .= '<td width="140" style="font-size:11px;"><b>Inspect By : </b></td>
			<td width="110" style="font-size:11px;">'.$_POST['inspectedBy'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['issuedTo']) && isset($_POST['issuedTo'])){
			$html .= '<td width="140" style="font-size:11px;"><b>Issue To : </b></td>
			<td width="110" style="font-size:11px;">'.str_replace("'", "", $mulIssueTo).'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['priority']) && isset($_POST['priority'])){
			$html .= '<td width="140" style="font-size:11px;"><b>Priority : </b></td>
			<td width="110" style="font-size:11px;">'.$_POST['priority'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['inspecrType']) && isset($_POST['inspecrType'])){
			$html .= '<td width="140" style="font-size:11px;"><b>Inspection Type : </b></td>
			<td width="110" style="font-size:11px;">'.$_POST['inspecrType'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['costAttribute']) && isset($_POST['costAttribute'])){
			$html .= '<td width="140" style="font-size:11px;"><b>Cost Attribute : </b></td>
			<td width="110" style="font-size:11px;">'.$_POST['costAttribute'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_REQUEST['raisedBy']) && isset($_REQUEST['raisedBy'])){
			$html .= '<td width="140" style="font-size:11px;"><b>Raised By : </b></td>
			<td width="110" style="font-size:11px;">'.$_REQUEST['raisedBy'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['DRF']) && isset($_POST['DRF']) || !empty($_POST['DRT']) && isset($_POST['DRT']))
		{
			$html .= '<td width="140" style="font-size:11px;"><b>Date Raised : </b></td>
			<td width="110" style="font-size:11px;">'.$_POST['DRF'].' to '.$_POST['DRT'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_POST['FBDF']) && isset($_POST['FBDF']) || !empty($_POST['FBDT']) && isset($_POST['FBDT']))
		{
		   $html .= '<td width="140" style="font-size:11px;""><b>Fixed By Date : </b></td>
		   <td width="110" style="font-size:11px;"">'.$_POST['FBDF'].' to '.$_POST['FBDT'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		$html .= '</tr></table></td></tr></table>';
		$i=1;
		$pageCount = 1;
		while( $fi=mysql_fetch_row($ri)){

			$where = "";
			if(!empty($_POST['costAttribute'])){
				$where .= " and cost_attribute = '".$_POST['costAttribute']."'";
			}
			if($_POST['issuedTo']!=""){
				$where .= " and issued_to_name LIKE '".$_POST['issuedTo']."%'";
			}
			if(!empty($_POST['status'])){
				$where .= " and inspection_status = '".$_POST['status']."'";
			}
			if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
				$where.=" and inspection_fixed_by_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
			}
			$issueToData = $obj->selQRYMultiple('issued_to_name, inspection_fixed_by_date, cost_attribute, inspection_status', 'issued_to_for_inspections', 'inspection_id = '.$fi[11] . ' and is_deleted=0 ' . $where .' group by issued_to_name');

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

			if ((intval($i/2)*2) == $i && $i!=1){
				$pageCount++;
				$html .= '<div style="page-break-before: always;"></div>Page: </strong>'.$pageCount.' of '.$noPages . "<br/><br/>" . $i . ".";
			}else{
				$html .= "<br/><br/>" . $i . ".<br/>";
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
					<td colspan="3" rowspan="4" valign="top" style="font-size:10px;">&nbsp;'.stripslashes(wordwrap($fi[9], 90, '<br />&nbsp;')).'</td>
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
					<td colspan="3" style="background-color:#CCCCCC;width:300px;font-size:11px;"><i>&nbsp;Notes</i></td>
				</tr>
				<tr>
					<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Fix&nbsp;by&nbsp;Date</i></td>
					<td colspan="2" style="font-size:10px;">&nbsp;'.$issueToData_fixedByDate.'</td>
					<td colspan="3" rowspan="3" valign="top" style="font-size:10px;">&nbsp;'.stripslashes(wordwrap($fi[10], 90, '<br />&nbsp;')).'</td>
				</tr>
				<tr>
					<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Cost&nbsp;Attribute</i></td>
					<td colspan="2" style=" font-size:10px;">'.$issueToData_costAttribute.'</td>
				</tr>
				<tr>
					<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Status</i></td>
					<td colspan="2" style="font-size:10px;">&nbsp;'.$issueToData_status.'</td>
				</tr>
				<tr>
					<td colspan="2" align="center"  style="width:180px;height:130px">';
$images = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$fi[11].' AND is_deleted = 0 AND graphic_type = "images" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 2');
			if(isset($images[0]) && $images[0]['graphic_name'] != ''){
$obj->resizeImages('../inspections/photo/'.$images[0]['graphic_name'], 200, 130, '../inspections/photo/photo_detail/'.$images[0]['graphic_name']);
				if(file_exists('../inspections/photo/'.$images_path.$images[0]['graphic_name'])){
					$html .='<img src="../inspections/photo/'.$images_path.$images[0]['graphic_name'].'" style="width:176px;height:126px" />';
				}else{
					$html .='<img src="../inspections/photo/'.$images[0]['graphic_name'].'" style="width:176px;height:126px" />';
				}
			}
			$html .='</td><td colspan="2" align="center" style="width:180px;height:130px">';
			if(isset($images[1]) && $images[1]['graphic_name'] != ''){
$obj->resizeImages('../inspections/photo/'.$images[1]['graphic_name'], 200, 130, '../inspections/photo/photo_detail/'.$images[1]['graphic_name']);
				if(file_exists('../inspections/photo/'.$images_path.$images[1]['graphic_name'])){
					$html .='<img src="../inspections/photo/'.$images_path.$images[1]['graphic_name'].'"  style="width:176px;height:126px" /></td>';
				}else{
					$html .='<img src="../inspections/photo/'.$images[1]['graphic_name'].'"  style="width:176px;height:126px" /></td>';
				}
			}
$drawing = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$fi[11].' AND is_deleted = 0 AND graphic_type = "drawing" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 1');
			$html .='<td colspan="2" align="center" style="width:180px;height:130px">';
			if(isset($drawing[0]) && $drawing[0]['graphic_name'] != ''){
$obj->resizeImages('../inspections/drawing/'.$drawing[0]['graphic_name'], 200, 130, '../inspections/drawing/drawing_detail/'.$drawing[0]['graphic_name']);
				if(file_exists('../inspections/drawing/'.$dimages_path.$drawing[0]['graphic_name'])){
					$html .='<img src="../inspections/drawing/'.$dimages_path.$drawing[0]['graphic_name'].'"  style="width:176px;height:126px" />';
				}else{
					$html .='<img src="../inspections/drawing/'.$drawing[0]['graphic_name'].'"  style="width:176px;height:126px" />';
				}
			}
			$html .='</td>
				</tr>
			</table>';
	$i++;	}

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