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
if (isset ($_POST['page_no']) && $_POST['page_no'] == 1)
{
	if(!is_dir($d))
	{
		mkdir($d,0777);
		chmod($d,0777);
	}else{
		rrmdir($d);
		mkdir($d,0777);
		chmod($d,0777);
	}
}
if (isset ($_POST['page_no']))
	$page_no = $_POST['page_no'];
else
	$page_no = 1;
	$report_type = 'pdfSummayWithImages';
	$images_path = "photo_summary/";
	$dimages_path = "signoff_summary/";
	//if ($report_type == "pdfSummayHD")
	//{
	//	$images_path = "";
		//$dimages_path = "";
	//}

	if(!empty($_POST['projName'])){
		if ($_POST['projName'] == "all")
		{
			if ($owner_id != "company")
				$where=" and P.user_id=" . $owner_id;
		}else{
			$where=" and I.project_id='".$_POST['projName']."'";
		}
	}
	if(!empty($_POST['location']) && empty($_POST['subLocation'])){
		$postCount++;
		$where.=" and I.location_id in (".$obj->subLocationsId($_POST['location'], ", ").")";
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

	if(!empty($_POST['status'])){
		$postCount++;
		$where.=" and F.inspection_status='".$_POST['status']."'";
	}
	if(!empty($_POST['inspectedBy'])){
		$postCount++;
		$where.=" and I.inspection_inspected_by='".$_POST['inspectedBy']."'";
	}
	if($_POST['issuedTo']!=""){
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
	}
	if($_POST['inspecrType']!=""){
		$postCount++;
		$where.=" and I.inspection_type='".$_POST['inspecrType']."'";
	}
	if(!empty($_POST['costAttribute'])){
		$postCount++;
		$where.=" and F.cost_attribute = '".$_POST['costAttribute']."'";
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

	if($_POST['DRF']!="" && $_POST['DRT']!=""){
		$postCount++;
		$or.=" I.inspection_date_raised between '".date('Y-m-d', strtotime($_POST['DRF']))."' and '".date('Y-m-d', strtotime($_POST['DRT']))."'";
	}
	
	if($_POST['DRF']!="" && $_POST['FBDF']!=""){$or.=" and";}
	
	if($_POST['FBDF']!="" && $_POST['FBDT']!=""){
		$postCount++;
		$or.=" F.inspection_fixed_by_date between '".date('Y-m-d', strtotime($_POST['FBDF']))."' and '".date('Y-m-d', strtotime($_POST['FBDT']))."'";
	}
	
	if(!empty($_REQUEST['searchKeyward'])){
		$postCount++;
		$locationRows = $obj->selQRYMultiple ("location_id", "project_locations", "project_id=".$_REQUEST['projName'] . " and location_title LIKE '%".$_REQUEST['searchKeyward']."%' and is_deleted=0" );
		$location_id_arr = array();
		foreach ($locationRows as $locationID)
		{
			$location_id_arr[] = $locationID["location_id"];	
		}
		$where.=" OR I.location_id in (".join(",", $location_id_arr) .")";
	}

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
	$orderby = "";	
	if ($_POST["sortby"])
	{
		if ($_REQUEST["sortby"] == "issued_to_name")
			$orderby = "order by F." . $_REQUEST["sortby"] . ", I.location_id";
		else
			$orderby = "order by I." . $_REQUEST["sortby"];
	}
	if($_SESSION['userRole'] == 'Sub Contractor'){
		$where.=" and  F.issued_to_name = '".$_SESSION['userIssueTo']."'";
	}
	
$qi="SELECT
		P.project_id as ProjectId,
		P.project_name as Project,
		I.location_id as Location,
		I.inspection_date_raised as DateRaised,
		I.inspection_inspected_by as InspectedBy,
		I.inspection_type as InspectionType,
		F.inspection_status as Status,
		F.issued_to_name as IssueToName,
		F.cost_attribute as CostAttribute,
		F.inspection_fixed_by_date as FixedByDate,
		I.inspection_description as Description,
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

$ri=mysql_query($qi);

$noInspection = mysql_num_rows($ri);
$ajaxReplay = $noInspection.' Records';
$noPages = ceil(($noInspection-4)/6 +1);

if($noInspection > 0){
	if ($_POST['projName'] == "all"){
		if ($onwer_id != "company")
			$query = "select project_id, project_name from user_projects where is_deleted=0 and user_id=" . $owner_id;
		else
			$query = "select project_id, project_name from user_projects where is_deleted=0";
		$res = mysql_query($query);
		$tmp = array();
		$project_names_arr = array();
		while($row=mysql_fetch_array($res))
		{
			$tmp[] = $row[1];
			$project_names_arr[$row[0]] = $row[1];
		}
		$projects_name = join (", ", $tmp);
	}else{
		$projects_name = $obj->getDataByKey('user_projects', 'project_id', $_POST['projName'], 'project_name');
	}
$html_tds = array();
$header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
@charset "utf-8";
body{
	font-family : Trebuchet MS, Arial, serif;
	padding: 5px;
	size:A4-landscape;
}
table.collapse {
	border-collapse: collapse;
	border: 1pt solid black;  
}
table.collapse td {
	border: 1pt solid black;
	padding: 2px;
}
</style>
</head>
<body>
<script type="text/php">

        if ( isset($pdf) ) {

          $font = Font_Metrics::get_font("helvetica", "bold");
          $pdf->page_text(182, 575, "DefectID, part of the Wiseworker Quality Management Ecosystem, helping the construction industry.", $font, 10, array(0,0,0));
          $pdf->page_text(370, 575, "", $font, 10, array(0,0,0));
        }
        </script>
<table width="792" border="" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td width="40%"></td><td width="70%" align="right" style="padding-right:20px;">
				<img src="../company_logo/logo.png" height="40"  /></td>
			</tr>';	
$html_tds[0] = $header . '	<tr>
				<td width="40%" style="font-size:14px;"><u><b>Summary Report</b></u></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td  style="font-size:10px;padding:0px;" colspan="2"><strong>Project Name : </strong>'.$projects_name.'</td>
			</tr>
			<tr>
				<td style="font-size:10px;"><strong>Date : </strong>'.date('d / m / Y').'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:10px;"><strong>Inspections : </strong>'.$noInspection.'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:10px;"><strong>Page : </strong>1 of '.$noPages.'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:10px;"><strong>Report Filtered by : </strong></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="padding-left:30px;" colspan="2"><table width="400" border="0"><tr>';
				$jk=0;
	if(!empty($_POST['location']) && empty($_POST['subLocation'])){
		$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Location Name: </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['location'], 'location_title').'</td>';$jk++;
	}
	if(!empty($_POST['subLocation']) && !empty($_POST['sub_subLocation'])){
		$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Location Name: </b></td>
		<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['location'], 'location_title').'</td>';$jk++;
		$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Sub Location 1: </b></td>
		<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['subLocation'], 'location_title').'</td>';$jk++;
		
		if($jk%2 !=0){$html_tds[0] .= '';}if($jk%2 ==0){$html_tds[0] .= '</tr><tr>';}
		
		$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Sub Location 2: </b></td>
		<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['sub_subLocation'], 'location_title').'</td>';$jk++;
	}else{
		if(!empty($_POST['location']) && !empty($_POST['subLocation'])){
			$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Location Name: </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['location'], 'location_title').'</td>';$jk++;
			$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Sub Location: </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_locations', 'location_id', $_POST['subLocation'], 'location_title').'</td>';$jk++; if($jk%2 !=0){$html_tds[0] .= '';}if($jk%2 ==0){$html_tds[0] .= '</tr><tr>';}
		}
	}
		if(!empty($_POST['status']) && isset($_POST['status'])){
			$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Status: </b></td>
			<td width="100" style="font-size:11px;">'.$_POST['status'].'</td>';
			$jk++; if($jk%2 !=0){$html_tds[0] .= '';}if($jk%2 ==0){$html_tds[0] .= '</tr><tr>';}
		}
		if(!empty($_POST['inspectedBy']) && isset($_POST['inspectedBy'])){
			$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Inspected By: </b></td>
			<td width="100" style="font-size:11px;">'.$_POST['inspectedBy'].'</td>';
			$jk++; if($jk%2 !=0){$html_tds[0] .= '';}if($jk%2 ==0){$html_tds[0] .= '</tr><tr>';}
		}
		if(!empty($_POST['issuedTo']) && isset($_POST['issuedTo'])){
			$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Issue To: </b></td>
			<td width="100" style="font-size:11px;">'.str_replace("'", "", $mulIssueTo).'</td>';
			$jk++; if($jk%2 !=0){$html_tds[0] .= '';}if($jk%2 ==0){$html_tds[0] .= '</tr><tr>';}
		}
		if(!empty($_POST['priority']) && isset($_POST['priority'])){
			$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Priority: </b></td>
			<td width="100" style="font-size:11px;">'.$_POST['priority'].'</td>';
			$jk++; if($jk%2 !=0){$html_tds[0] .= '';}if($jk%2 ==0){$html_tds[0] .= '</tr><tr>';}
		}
		if(!empty($_POST['inspecrType']) && isset($_POST['inspecrType'])){
			$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Inspection Type: </b></td>
			<td width="100" style="font-size:11px;">'.$_POST['inspecrType'].'</td>';
			$jk++; if($jk%2 !=0){$html_tds[0] .= '';}if($jk%2 ==0){$html_tds[0] .= '</tr><tr>';}
		}
		if(!empty($_POST['raisedBy']) && isset($_POST['raisedBy'])){
			$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Raised By: </b></td>
			<td width="110" style="font-size:11px;">'.$_POST['raisedBy'].'</td>';
			$jk++; if($jk%2 !=0){$html_tds[0] .= '';}if($jk%2 ==0){$html_tds[0] .= '</tr><tr>';}
		}
		if(!empty($_POST['costAttribute']) && isset($_POST['costAttribute'])){
			$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Cost Attribute: </b></td>
			<td width="100" style="font-size:11px;">'.$_POST['costAttribute'].'</td>';
			$jk++; if($jk%2 !=0){$html_tds[0] .= '';}if($jk%2 ==0){$html_tds[0] .= '</tr><tr>';}
		}
		if(!empty($_POST['DRF']) && isset($_POST['DRF']) || !empty($_POST['DRT']) && isset($_POST['DRT']))
		{
			$html_tds[0] .= '<td width="140" style="font-size:11px;"><b>Date Raised: </b></td>
			<td width="100" style="font-size:11px;">'.$_POST['DRF'].' to '.$_POST['DRT'].'</td>';
			$jk++; if($jk%2 !=0){$html_tds[0] .= '';}if($jk%2 ==0){$html_tds[0] .= '</tr><tr>';}
		}
		if(!empty($_POST['FBDF']) && isset($_POST['FBDF']) || !empty($_POST['FBDT']) && isset($_POST['FBDT']))
		{
		   $html_tds[0] .= '<td width="140" style="font-size:11px;""><b>Fixed By Date: </b></td>
		   <td width="100" style="font-size:11px;"">'.$_POST['FBDF'].' to '.$_POST['FBDT'].'</td>';
			$jk++; if($jk%2 !=0){$html_tds[0] .= '';}if($jk%2 ==0){$html_tds[0] .= '</tr><tr>';}
		}
		
		$html_tds[0] .= '</tr></table>
		<table width="754" cellpadding="0" cellspacing="0" align="left" class="collapse">
		<tr><td widht="10px" style="font-size:10px" align="center">ID</td><td widht="30px" style="font-size:10px" align="center">Inspection Type</td>';
		
		if ($_POST["projName"] == "all"){
			$html_tds[0] .= '<td width="45px" style="font-size:10px;" align="center"><strong>Project Name</strong></td>';
		}
		$html_tds[0] .= '<td style="width:95px;font-size:10px;" align="center"><strong>Location</strong></td>';
		if($report_type == "pdfSummayWithImages"){
			$html_tds[0] .= '<td style="width:150px;font-size:10px;" align="center"><strong>Description</strong></td>';
		}else{
			$html_tds[0] .= '<td style="width:200px;font-size:10px;" align="center"><strong>Description</strong></td>';
		}
		$html_tds[0] .= '<td style="width:40px;font-size:10px;" align="center"><strong>Inspected By</strong></td>
			<td style="width:30px;font-size:10px;" align="center"><strong>Date Raised</strong></td>
			<td style="width:40px;font-size:10px;" align="center"><strong>Raised&nbsp;By</strong></td>
			<td style="width:20px;font-size:10px;" align="center"><strong>Issued&nbsp;To</strong></td>
			<td style="width:30px;font-size:10px;" align="center"><strong>Fix By Date</strong></td>
			<td style="width:25px;font-size:10px;" align="center"><strong>Status</strong></td>';
if($report_type == "pdfSummayWithImages"){
	$html_tds[0] .= '<td style="width:80px;font-size:10px;" align="center"><strong>Image 1</strong></td><td style="width:80px;font-size:10px;" align="center"><strong>Image 2</strong></td><td style="width:80px;font-size:10px;" align="center"><strong>Drawing</strong></td><td style="width:100px;font-size:10px;" align="center"><strong>Sign Off</strong></td>';
}
	$html_tds[0] .= '</tr>';
		$i=1;
		$j=0;
		$k=1;
		$count = 0;
		$pagebreak = 13;
		$rowno = 0;
		if($report_type == "pdfSummayWithImages"){
			$pagebreak = 4;
		}
		$flagFirstPage = true;
		while($fi=mysql_fetch_assoc($ri)){
			$rowno++;
			$count++;
			if ($count == $pagebreak)
			{
				$count = 0;
				$i++;
				$k++;
				if ($i == 21)
				{
					$i = 1;
					$html_tds[$j] .= "</table></body></html>";
					$j++;
					$html_tds[$j] = $header;
					$html_tds[$j] .= '</table>' . $k . ' of ' . $noPages .'<table width="762" cellpadding="0" cellspacing="0" align="left" class="collapse">';
				}else{
					$html_tds[$j] .= '</table><div style="page-break-before: always;"></div>' . $k . ' of ' . $noPages .'<table width="762" cellpadding="0" cellspacing="0" align="left" class="collapse">';
				}
				if ($flagFirstPage)
				{
					$flagFirstPage = false;
					$pagebreak = 16;
					if($report_type == "pdfSummayWithImages"){
						$pagebreak = 6;
					}
				}
			$html_tds[$j] .= '<tr><td width="10px" style="font-size:10px;" align="center"><strong>ID</strong></td>
			<td width="30px" style="font-size:10px;" align="center"><strong>Inspection Type</strong></td>';
		if ($_POST["projName"] == "all")
		{
			$html_tds[$j] .= '<td width="45px" style="font-size:10px;" align="center"><strong>Project Name</strong></td>';
		}
		$html_tds[$j] .= '<td style="width:95px;font-size:10px;" align="center"><strong>Location</strong></td>';
	if($report_type == "pdfSummayWithImages"){
		$html_tds[$j] .= '<td style="width:155px;font-size:10px;" align="center"><strong>Description</strong></td>';
	}else{
		$html_tds[$j] .= '<td style="width:200px;font-size:10px;" align="center"><strong>Description</strong></td>';
	}
	$html_tds[$j] .= '<td style="width:40px;font-size:10px;" align="center"><strong>Inspected By</strong></td>
			<td style="width:30px;font-size:10px;" align="center"><strong>Date Raised</strong></td>
			<td style="width:40px;font-size:10px;" align="center"><strong>Raised&nbsp;By</strong></td>
			<td style="width:40px;font-size:10px;" align="center"><strong>Issued&nbsp;To</strong></td>
			<td style="width:30px;font-size:10px;" align="center"><strong>Fix By Date</strong></td>
			<td style="width:25px;font-size:10px;" align="center"><strong>Status</strong></td>';
if($report_type == "pdfSummayWithImages"){
	$html_tds[$j] .= '<td style="width:80px;font-size:10px;" align="center"><strong>Image 1</strong></td><td style="width:80px;font-size:10px;" align="center"><strong>Image 2</strong></td><td style="width:80px;font-size:10px;" align="center"><strong>Drawing</strong></td><td style="width:100px;font-size:10px;" align="center"><strong>Sign Off</strong></td>';
}
	$html_tds[$j] .= '</tr>';
			}
			$where = "";
			if(!empty($_POST['costAttribute'])){
				$where .= " and cost_attribute = '".$_POST['costAttribute']."'";
			}
			if($_POST['issuedTo']!=""){
				$postCount++;
				$isMul = explode('@@@', $_REQUEST['issuedTo']);
				$mulIssueToWhere = '';
				$mulIssueTo = '';
				$loopMul = count($isMul);
				for($g=0; $g<$loopMul; $g++){
					if($mulIssueToWhere == ""){
						$mulIssueTo = "'".$isMul[$g]."'";
						$mulIssueToWhere .= " (issued_to_name LIKE '".addslashes($isMul[$g])." (%' OR issued_to_name LIKE '".addslashes($isMul[$g])."') ";
					}else{
						$mulIssueTo .= ", '".$isMul[$g]."'";
						$mulIssueToWhere .= " OR (issued_to_name LIKE '".addslashes($isMul[$g])." (%' OR issued_to_name LIKE '".addslashes($isMul[$g])."') ";
					}
				}
				$where.=" AND (".$mulIssueToWhere.") AND inspection_id = inspection_id";
			}
			if(!empty($_POST['status'])){
				$where .= " and inspection_status = '".$_POST['status']."'";
			}
			if($_POST['FBDF']!="" && $_POST['FBDT']!=""){
				$where.=" and inspection_fixed_by_date between '".date('Y-m-d', strtotime($_POST['FBDF']))."' and '".date('Y-m-d', strtotime($_POST['FBDT']))."'";
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
		$html_tds[$j] .= '<tr><td width="10px" style="font-size:10px" align="center">'.$fi["InspectionId"].'</td>
		<td width="10px" style="font-size:10px" align="center">'.$fi["InspectionType"].'</td>';
		if ($_POST["projName"] == "all")
		{
			$html_tds[$j] .= '<td width="45px" style="font-size:10px;" align="center">'.$project_names_arr[$fi["ProjectId"]].'</td>';
		}
			$html_tds[$j] .= '<td>';
			$locations = $obj->subLocations($fi["Location"], '<br />');
	$html_tds[$j] .= '<font style="font-size:10px;">';
			if($report_type == "pdfSummayWithImages"){
				$html_tds[$j] .= stripslashes(wordwrap($locations, 25, '<br />'));
			}else{
				$html_tds[$j] .= stripslashes(wordwrap($locations, 35, '<br />'));
			}
	$html_tds[$j] .= '</font></td>
			<td><font style="font-size:10px;">';
			if($report_type == "pdfSummayWithImages"){
				$html_tds[$j] .= stripslashes(wordwrap($fi["Description"], 25, "<br />"));
			}else{
				$html_tds[$j] .= stripslashes(wordwrap($fi["Description"], 135, "<br />"));
			}
		$html_tds[$j] .= '</font>
			</td>
			<td><font style="font-size:10px;">'.stripslashes(wordwrap($fi["InspectedBy"], 15, "<br />", true)).'</font></td>
			<td align="center"><font style="font-size:10px;">';
			if($fi["DateRaised"] != '0000-00-00'){
				$html_tds[$j] .= stripslashes(date("d/m/y", strtotime($fi["DateRaised"])));
			}
			$html_tds[$j] .= '</font>
			</td>
			<td><font style="font-size:10px;">'.stripslashes($fi['RaisedBy']).'</font></td>
			<td><font style="font-size:10px;">'.$issueToData_issueToName.'</font></td>
			<td align="center"><font style="font-size:10px;">'.$issueToData_fixedByDate.'</font></td>
			<td align="center"><font style="font-size:10px;">'.$issueToData_status.'</font></td>';

if($report_type == "pdfSummayWithImages"){
				$images = $obj->getRecords('inspection_graphics', 'inspection_id', $fi["InspectionId"], 'graphic_type', 'images', 'graphic_name');
				$html_tds[$j] .='<td valign="middle" align="center" style="height:50px;">';
				if(isset($images[0]) && $images[0]['graphic_name'] != ''){
					$obj->resizeImages('../inspections/photo/'.$images[0]['graphic_name'], 153, 100, '../inspections/photo/photo_summary/'.$images[0]['graphic_name']);
					if(file_exists('../inspections/photo/'.$images_path.$images[0]['graphic_name'])){
						$html_tds[$j] .='<img src="../inspections/photo/'.$images_path.$images[0]['graphic_name'].'" style="width:80px;" />';
					}else{
						if (file_exists('../inspections/photo/'.$images[0]['graphic_name']))
							$html_tds[$j] .='<img src="../inspections/photo/'.$images[0]['graphic_name'].'" style="width:80px;" />';
					}
				}
				$html_tds[$j] .='</td>';
				$html_tds[$j] .='<td valign="middle" align="center" style="height:50px;">';
				if(isset($images[1]) && $images[1]['graphic_name'] != ''){
					$obj->resizeImages('../inspections/photo/'.$images[1]['graphic_name'], 153, 100, '../inspections/photo/photo_summary/'.$images[1]['graphic_name']);
					if(file_exists('../inspections/photo/'.$images_path.$images[1]['graphic_name'])){
						$html_tds[$j] .='<img src="../inspections/photo/'.$images_path.$images[1]['graphic_name'].'" style="width:80px;" />';
					}else{
						if (file_exists('../inspections/photo/'.$images[1]['graphic_name']))
							$html_tds[$j] .='<img src="../inspections/photo/'.$images[1]['graphic_name'].'" style="width:80px;" />';
					}
				}
				$html_tds[$j] .='</td>';
				$drawing = $obj->getRecords('inspection_graphics', 'inspection_id', $fi["InspectionId"], 'graphic_type', 'drawing', 'graphic_name');
				$html_tds[$j] .='<td valign="middle" align="center" style="height:50px;">';
				if(isset($drawing[0]) && $drawing[0]['graphic_name'] != ''){
					$obj->resizeImages('../inspections/drawing/'.$drawing[0]['graphic_name'], 153, 100, '../inspections/drawing/drawing_summary/'.$drawing[0]['graphic_name']);
					if(file_exists('../inspections/drawing/drawing_summary/'.$drawing[0]['graphic_name'])){
						$html_tds[$j] .='<img src="../inspections/drawing/drawing_summary/'.$drawing[0]['graphic_name'].'" style="width:80px;" />';
					}else{
						if (file_exists('../inspections/drawing/'.$drawing[0]['graphic_name']))
							$html_tds[$j] .='<img src="../inspections/drawing/'.$drawing[0]['graphic_name'].'" style="width:80px;" />';
					}
				}
				$html_tds[$j] .='</td>';
				$html_tds[$j] .='<td valign="middle" align="center" style="height:50px;">';
				if(isset($fi['signoff']) && $fi['signoff'] != ''){
					$obj->resizeImages('../inspections/signoff/'.$fi['signoff'], 153, 100, '../inspections/signoff/signoff_summary/'.$fi['signoff']);
					if(file_exists('../inspections/signoff/signoff_summary/'.$fi['signoff'])){
						$html_tds[$j] .='<img src="../inspections/signoff/signoff_summary/'.$fi['signoff'].'" style="width:80px;" />';
					}else{
						if (file_exists('../inspections/signoff/'.$fi['signoff']))
							$html_tds[$j] .='<img src="../inspections/signoff/'.$fi['signoff'].'" style="width:80px;" />';
					}
				}
				$html_tds[$j] .='</td>';
}
			$html_tds[$j] .='</tr>';
		}
	$html_tds[$j] .= '</table></body></html>';

	$flag = true;
	$page = 1;
	$zipFileArray = array();
	$dobreak = false;
	foreach($html_tds as $html_h){
		if ($page != $page_no)
		{
			$page++;
			continue;
		}
		createPDF($html_h, 'Report_Summary_'.$page.'.pdf', $owner_id);
		//$zipFileArray[] = '../report_pdf/'.$owner_id.'/wallChart_part'.$page.'.pdf';
		break;
	}
	$total_page = ceil($noPages/20);
	if ($page_no == $total_page)
	{
 		for ($i=1;$i<=$total_page; $i++)
		{
			$zipFileArray[] = '../report_pdf/'.$owner_id.'/Report_Summary_'.$i.'.pdf';
		}
		$zipName = 'Report_Summary_'.microtime().'.zip';
		
		if($obj->create_zip($zipFileArray, '../report_pdf/'.$owner_id.'/'.$zipName)){
			echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="closePopUp();" href="./report_pdf/'.$owner_id.'/'.$zipName.'" target="_blank" class="view_btn"></a></div>';
		}else{
			echo '<br clear="all" /><div style="margin-left:10px;">Summary Report generation failed, please try again later</div>';
		}
	}else{
		echo "total:" . $total_page . "##current:" . $page_no;	
	}
}else{
	echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
}

function createPDF($html, $report, $owner_id){

	require_once("../dompdf/dompdf_config.inc.php");
	$paper='a4';
	$orientation='landscape';
	
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
function rrmdir($dir) {
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