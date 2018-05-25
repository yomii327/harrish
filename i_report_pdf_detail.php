<?php
ob_start();
session_start();

//Code for Calculate Execution Time
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$starttime = $mtime; 
//Code for Calculate Execution Time
include('includes/commanfunction.php');
$obj= new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}
$limit = 19;
$toEmailIDS = array();$mulIssueTo = ''; $issToForInspWhere = '';
	$issued_to_add = '';
	if(!isset($_REQUEST['startWith'])){
		$offset = 0;
		#$limit = 1;
	}
	if($_REQUEST['startWith'] == 0){
		$offset = 0;
		#$limit = 1;
	}else{
		$offset = $_REQUEST['startWith'];
		$offsetPage = ceil($offset/2);
		#$limit = 2;
	}
	$postCount = 0;
	$report_type = $_REQUEST['report_type'];
	$images_path = "photo_detail/";
	$dimages_path = "drawing_detail/";
	if ($report_type == "pdfDetailHD"){
		$images_path = "";
		$dimages_path = "";
	}

	if(!empty($_REQUEST['projName'])){
		$where=" and I.project_id='".$_REQUEST['projName']."'";
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
	if(!empty($_REQUEST['status'])){
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
	$orderby = "";	
	if ($_REQUEST["sortby"])
	{
		$orderby = "order by I." . $_REQUEST["sortby"];
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
	if($_SESSION['userRole'] == 'Sub Contractor'){
		$where.=" and  F.issued_to_name = '".$_SESSION['userIssueTo']."'";
	}

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
	
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
		F.inspection_id as InspectionId_FOR,
		I.inspection_raised_by as RaisedBy,
		P.defect_clause as defect_clause
	FROM
		user_projects as P, issued_to_for_inspections as F,
		project_inspections as I
	WHERE
		I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.is_deleted = '0' and F.is_deleted = '0' and P.is_deleted = '0' $where group by I.inspection_id $orderby LIMIT $offset , $limit";

$ri=mysql_query($qi);
$queryCount = "SELECT count(I.inspection_id) FROM user_projects as P, issued_to_for_inspections as F, project_inspections as I WHERE I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.is_deleted = '0' and F.is_deleted = '0' and P.is_deleted = '0' $where group by I.inspection_id";

$resCount=mysql_query($queryCount);

if(mysql_num_rows($resCount) > 0){
	$totalCount = mysql_num_rows($resCount);
}
$noInspection = mysql_num_rows($ri);
$ajaxReplay = $totalCount.' Records';
	$noPages = ceil(($totalCount-1)/2 +1);
	
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
//issue to data fetch here
	$issueToDataQRY = "SELECT I.inspection_id as InspectionId FROM
			user_projects as P, issued_to_for_inspections as F,
			project_inspections as I
		WHERE
			I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.is_deleted = '0' and F.is_deleted = '0' and P.is_deleted = '0' $where group by I.inspection_id";
	$isToRes = mysql_query($issueToDataQRY);
	$toEmailIDS = array();
	$inspectID = '';
	if(mysql_num_rows($isToRes) > 0){
		while($isRow = mysql_fetch_array($isToRes)){
			if($inspectID == ''){
				$inspectID = $isRow['InspectionId'];
			}else{
				$inspectID .= ','.$isRow['InspectionId'];
			}
		}
	}
	$issueToNameStr = '';
	$isNameData = $obj->selQRYMultiple('issued_to_name', 'issued_to_for_inspections', 'inspection_id IN ('.$inspectID.') AND is_deleted = 0 AND project_id = '.$_REQUEST['projName'].' group by issued_to_name');
	foreach($isNameData as $isName){
		if($issueToNameStr == ''){
			$issueToNameStr = '"'.$isName['issued_to_name'].'"';
		}else{
			$issueToNameStr .= ',"'.$isName['issued_to_name'].'"';
		}
	}
	if($mulIssueTo != ''){
		$issueToNameStr = $mulIssueTo;
	}
	if($issueToNameStr != ""){
		$issueEmails = $obj->selQRYMultiple('distinct issue_to_email, issue_to_name', 'inspection_issue_to', 'project_id = '.$_REQUEST['projName'].' AND is_deleted = 0 AND issue_to_name IN ('.$issueToNameStr.')  group by issue_to_name');
	}
	foreach($issueEmails as $isEmail){
		if($isEmail['issue_to_email'] != '' && strpos($isEmail['issue_to_email'], '@') !== false){
			$toEmailIDS[] = array('email'=>$isEmail['issue_to_email'], 'issueToName'=>$isEmail['issue_to_name']);
		}
	}
	$toEmailIDS['arrSize'] = sizeof($toEmailIDS);
	$toEmailIDS['reportType'] = 'Internal Report';
#	print_r($toEmailIDS);die;
//issue to data fetch here

$html='<table width="98%" border="0" align="center">
			<tr>
				<td width="40%"></td><td width="60%" align="right" style="padding-right:20px;">';
			$html .='<img src="company_logo/logo.png" height="40"  /></td>
			</tr>
			<tr>
				<td width="40%" style="font-size:14px"><u><b>Internal Report</b></u></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Project Name: </strong>'.$obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projName'], 'project_name').'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Date: </strong>'.date('d/m/Y').'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Inspections: </strong>'.$totalCount.'</td>
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
		if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
			$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Location Name : </b></td>
			<td width="110" style="font-size:11px;" valign="top"">'.$locationSearchName.'</td>';$jk++;
		}
		if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['sub_subLocation'])){
			$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Location Name : </b></td>
			<td width="110" style="font-size:11px;" valign="top"">'.$locationSearchName.'</td>';$jk++;
			$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Sub Location 1 : </b></td>
			<td width="110" style="font-size:11px;" valign="top"">'.$subLocationSearchName.'</td>';$jk++;
			if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
			$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Sub Location 2 : </b></td>
			<td width="110" style="font-size:11px;" valign="top"">'.$subSubLocationSearchName.'</td>';$jk++;
			
			if(!empty($_REQUEST['sub_subLocationName2']) && !empty($_REQUEST['sub_subLocationName3'])){
				$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Sub Location 3 : </b></td>
				<td width="110" style="font-size:11px;" valign="top"">'.$_REQUEST['sub_subLocationName2'].'</td>';$jk++;
				$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Sub Location 4 : </b></td>
				<td width="110" style="font-size:11px;" valign="top"">'.$_REQUEST['sub_subLocationName3'].'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
			}
		}else{
			if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
				$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Location Name : </b></td>
				<td width="110" style="font-size:11px;" valign="top"">'.$locationSearchName.'</td>';$jk++;
				$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Sub Location Name : </b></td>
				<td width="110" style="font-size:11px;" valign="top"">'.$subLocationSearchName.'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
			}
		}
		if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
			$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Status : </b></td>
			<td width="110" style="font-size:11px;" valign="top"">'.$_REQUEST['status'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_REQUEST['inspectedBy']) && isset($_REQUEST['inspectedBy'])){
			$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Inspect By : </b></td>
			<td width="110" style="font-size:11px;" valign="top"">'.$_REQUEST['inspectedBy'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_REQUEST['issuedTo']) && isset($_REQUEST['issuedTo'])){
			$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Issue To : </b></td>
			<td width="110" style="font-size:11px;" valign="top"">'.str_replace("'", "", $mulIssueTo).'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_REQUEST['priority']) && isset($_REQUEST['priority'])){
			$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Priority : </b></td>
			<td width="110" style="font-size:11px;" valign="top"">'.$_REQUEST['priority'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_REQUEST['inspecrType']) && isset($_REQUEST['inspecrType'])){
			$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Inspection Type : </b></td>
			<td width="110" style="font-size:11px;" valign="top"">'.$_REQUEST['inspecrType'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_REQUEST['costAttribute']) && isset($_REQUEST['costAttribute'])){
			$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Cost Attribute : </b></td>
			<td width="110" style="font-size:11px;" valign="top"">'.$_REQUEST['costAttribute'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_REQUEST['raisedBy']) && isset($_REQUEST['raisedBy'])){
			$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Raised By : </b></td>
			<td width="110" style="font-size:11px;" valign="top"">'.$_REQUEST['raisedBy'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_REQUEST['DRF']) && isset($_REQUEST['DRF']) || !empty($_REQUEST['DRT']) && isset($_REQUEST['DRT']))
		{
			$html .= '<td width="140" style="font-size:11px;" valign="top""><b>Date Raised : </b></td>
			<td width="110" style="font-size:11px;" valign="top"">'.$_REQUEST['DRF'].' to '.$_REQUEST['DRT'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_REQUEST['FBDF']) && isset($_REQUEST['FBDF']) || !empty($_REQUEST['FBDT']) && isset($_REQUEST['FBDT']))
		{
		   $html .= '<td width="140" style="font-size:11px;" valign="top"""><b>Fixed By Date : </b></td>
		   <td width="110" style="font-size:11px;" valign="top""">'.$_REQUEST['FBDF'].' to '.$_REQUEST['FBDT'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		$html .= '</tr></table></td></tr></table>';
		$i=$offset+1;
		$pageCount = 1;
		if(isset($offsetPage)){
			$pageCount = $pageCount + $offsetPage;
		}
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
			if ((intval($i/2)*2) == $i && $i!=1){
				$pageCount++;
				$html .= '<div style="page-break-before: always;"></div>Page: </strong>'.$pageCount.' of '.$noPages . "<br/><br/>" . $i . ".";
			}else{
				$html .= "<br/><br/>" . $i . ".<br/>";
			}
	$html .='<table width="98%" class="collapse" cellpadding="0" cellspaccing="0" align="center" border="0" style="border-bottom:hidden;">
				<tr>
					<td width="50%" valign="top" cellspacing="0" cellpadding="0" style="border-bottom:hidden;border-right:hidden;">
						<table class="collapse" width="100%" border="0" style="margin-left:-3px;margin-top:-3px;border-bottom:hidden;">
							<tr>
								<td width="50%" style="background-color:#CCCCCC;"><i>Location</i></td>
								<td width="50%" style="font-size:11px;" valign="top"">'.stripslashes(wordwrap($obj->subLocations($fi[1], ' > '), 23, '<br />')).'</td>
							</tr>
							<tr>
								<td width="50%" style="background-color:#CCCCCC;"><i>Date&nbsp;Raised</i></td>
								<td width="50%" style="font-size:11px;" valign="top"">';
									if($fi[2] != '0000-00-00'){
										$html .= stripslashes(date("d/m/Y", strtotime($fi[2])));
									}
						$html .='</td>
							</tr>
							<tr>
								<td width="50%" style="background-color:#CCCCCC;"><i>Inspected&nbsp;By</i></td>
								<td width="50%" style="font-size:11px;" valign="top"">'.stripslashes($fi[3]).'</td>
							</tr>
							<tr>
								<td width="50%" style="background-color:#CCCCCC;"><i>Inspection&nbsp;Type</i></td>
								<td width="50%" style="font-size:11px;" valign="top"">'.stripslashes($fi[4]).'</td>
							</tr>
							<tr>
							<td width="50%" style="background-color:#CCCCCC;"><i>Raised&nbsp;By</i></td>
								<td width="50%" style="font-size:11px;" valign="top"">'.stripslashes($fi[13]).'</td>
							</tr>
							<tr>
							<td width="50%" style="background-color:#CCCCCC;"><i>Issued&nbsp;To</i></td>
								<td width="50%" style="font-size:11px;" valign="top"">'.$issueToData_issueToName.'</td>
							</tr>
							<tr>
								<td width="50%" style="background-color:#CCCCCC;"><i>Fix by Date</i></td>
								<td width="50%" style="font-size:11px;" valign="top"">'.$issueToData_fixedByDate.'</td>
							</tr>
							<tr>
								<td width="50%" style="background-color:#CCCCCC;"><i>Cost Attribute</i></td>
								<td width="50%" style="font-size:11px;" valign="top"">'.$issueToData_costAttribute.'</td>
							</tr>
							<tr>
								<td width="50%" style="background-color:#CCCCCC;"><i>Status</i></td>
								<td width="50%" style="font-size:11px;" valign="top"">'.$issueToData_status.'</td>
							</tr>
						</table>
					</td>
					<td width="50%" valign="top" cellspacing="0" cellpadding="0" style="border-bottom:hidden;">
						<table class="collapse" width="103%" border="0" style="margin-left:-8px;margin-top:-3px;border-right:hidden;">
							<tr>
								<td width="50%" style="background-color:#CCCCCC;height:24px;"><i>Description</i></td>
							</tr>
							<tr>
								<td width="50%" style="padding-left:3px;height:55px;font-size:11px;" valign="top">'.stripslashes(substr($fi[9], 0, 200));
									if(strlen($fi[9]) > 200){
										$html .='......';
									}
						$html .='</td>
							</tr>
							<tr>
								<td width="50%" style="background-color:#CCCCCC;height:24px;"><i>Notes</i></td>
							</tr>
							<tr>
								<td width="50%" style="padding-left:3px;border-bottom:hidden;font-size:11px;" valign="top;" >'.stripslashes(substr($fi[10], 0, 200));
									if(strlen($fi[10]) > 200){
										$html .='......';
									}
						$html .='</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<table width="101%" cellspacing="0" cellpadding="0" border="0" style="margin-left:-3px;margin-top:-5px;border-left:hidden;border-right:hidden;">
							<tr>
								<td align="center"  style="width:180px;height:130px;font-size:11px;">';
					$images = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$fi[11].' AND is_deleted = 0 AND graphic_type = "images" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 2');
					if(isset($images[0]) && $images[0]['graphic_name'] != ''){
						$obj->resizeImages('inspections/photo/'.$images[0]['graphic_name'], 200, 130, 'inspections/photo/photo_detail/'.$images[0]['graphic_name']);
						if(file_exists('inspections/photo/'.$images_path.$images[0]['graphic_name'])){
							$html .='<img src="inspections/photo/'.$images_path.$images[0]['graphic_name'].'" style="width:176px;height:126px" />';
						}else{
							$html .='<img src="inspections/photo/'.$images[0]['graphic_name'].'" style="width:176px;height:126px" />';
						}
					}
					$html .='</td>
							<td align="center" style="width:180px;height:130px;font-size:11px;">';
					if(isset($images[1]) && $images[1]['graphic_name'] != ''){
						$obj->resizeImages('inspections/photo/'.$images[1]['graphic_name'], 200, 130, 'inspections/photo/photo_detail/'.$images[1]['graphic_name']);
						if(file_exists('inspections/photo/'.$images_path.$images[1]['graphic_name'])){
							$html .='<img src="inspections/photo/'.$images_path.$images[1]['graphic_name'].'"  style="width:176px;height:126px" /></td>';
						}else{
							$html .='<img src="inspections/photo/'.$images[1]['graphic_name'].'"  style="width:176px;height:126px" /></td>';
						}
					}
					$drawing = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$fi[11].' AND is_deleted = 0 AND graphic_type = "drawing" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 1');
					$html .='<td align="center" style="width:180px;height:130px;font-size:11px;">';
					if(isset($drawing[0]) && $drawing[0]['graphic_name'] != ''){
						$obj->resizeImages('inspections/drawing/'.$drawing[0]['graphic_name'], 200, 130, 'inspections/drawing/drawing_detail/'.$drawing[0]['graphic_name']);
						if(file_exists('inspections/drawing/'.$dimages_path.$drawing[0]['graphic_name'])){
							$html .='<img src="inspections/drawing/'.$dimages_path.$drawing[0]['graphic_name'].'"  style="width:176px;height:126px" />';
						}else{
							$html .='<img src="inspections/drawing/'.$drawing[0]['graphic_name'].'"  style="width:176px;height:126px" />';
						}
					}
					$html .='</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>';
if ($i%2!=0){
	$html .= '<div class="footer" style="text-align:center;font-weight:bold;font-size:10px;';
	if($i==1){
		$html .= 'margin-top:110px;';
	}else{
		$html .= 'margin-top:5px;';
	}
	$html .= '" >DefectID – Copyright Wiseworking 2012 / 2013</div>';
}
	$i++;	}

	$html .= '<tr>
				</br>
				<td style="font-size:12px"><strong>Defect Clause: </strong>'.$defect_clause.'</td>
				<td>&nbsp;</td>
			</tr>';

$html .= '<div id="emailIssueToData" style="display:none;">'.json_encode($toEmailIDS).'</div>';
//Code for Calculate Execution Time
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$endtime = $mtime; 
	$totaltime = ($endtime - $starttime); 
	$totaltime = number_format($totaltime, 2, '.', '');
//Code for Calculate Execution Time
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>
</head>

<body>
<div id="mainContainer">
	<div class="buttonDiv">
		<span style="padding-left:25px;font-size:15px;"><?php echo $totalCount.' results ('.$totaltime.' seconds)';?></span><br /><br />
		<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
		<img onClick="emailPDF();" src="images/email.png" style="float:left;margin-left:160px;" />
		<img onClick="downloadPDF();"src="images/download_btn.png" style="float:right;" />
	</div><br clear="all" />
		<?php $pageCount = $totalCount / $limit;?>
	<div class="pagination" <?php if($totalCount <= $limit){ echo 'style="display:none;" ';}?> >
		<?php $leftLimit = $offset - $limit;
			if($leftLimit < 0){ $leftLimit = 0; }else{ }?>
			<img id="previousImages" src="images/prev_icon.png" onclick="pageScroll(<? echo $leftLimit;?>);"
			<?php if($limit == 2){ echo 'style="float:left"'; }elseif($leftLimit < 0){ echo 'style="display:none;float:left;"'; }else{ echo 'style="float:left"';} ?> />
			<?php if($pageCount > 0){
					for($l=0; $l<$pageCount; $l++){?>
						<span <? if(($l*$limit) == $offset){
							echo 'class="page_active" ';
						}else{
							echo 'class="page_deactive" ';
						}
						if($l >= 5){
							echo 'style="display:none;" ';
						}?>
						onclick="pageScroll(<?php echo ($l*$limit); ?>)" ><?php echo ($l+1)?></span>
			<?php 	}
					if($l >= 5){ ?>
						<span><strong>.</strong></span>
						<span><strong>.</strong></span>
						<span><strong>.</strong></span>
				<? }
				}
				$rightLimit = $offset + $limit;
				if($rightLimit >= $totalCount){ $rightLimit = $totalCount; }else{ } ?>
				<img id="nextImages" src="images/next_icon.png" onclick="pageScroll(<?php echo $rightLimit;?>);"
				<?php if($rightLimit > $totalCount){ echo 'style="margin-left:5px;display:none;"'; }else{ echo 'style="margin-left:5px;"'; }?> />
	</div><br /><br /><br />
	<div id="htmlContainer">
		<?php echo $html;?>
	</div>
		<?php $pageCount = $totalCount / $limit;?>
	<div class="pagination" <?php if($totalCount <= $limit){ echo 'style="display:none;" ';}?> >
		<?php $leftLimit = $offset - $limit;
			if($leftLimit < 0){ $leftLimit = 0; }else{ }?>
			<img id="previousImages" src="images/prev_icon.png" onclick="pageScroll(<? echo $leftLimit;?>);"
			<?php if($limit == 2){ echo 'style="float:left"'; }elseif($leftLimit < 0){ echo 'style="display:none;float:left;"'; }else{ echo 'style="float:left"';} ?> />
				<?php if($pageCount > 0){
					for($l=0; $l<$pageCount; $l++){?>
						<span <? if(($l*$limit) == $offset){
							echo 'class="page_active" ';
						}else{
							echo 'class="page_deactive" ';
						}
						if($l >= 5){
							echo 'style="display:none;" ';
						}?>
						onclick="pageScroll(<?php echo ($l*$limit); ?>)" ><?php echo ($l+1)?></span>
				<?php }
					if($l >= 5){ ?>
						<span><strong>.</strong></span>
						<span><strong>.</strong></span>
						<span><strong>.</strong></span>
				<? }
				}
				$rightLimit = $offset + $limit;
				if($rightLimit >= $totalCount){ $rightLimit = $totalCount; }else{ } ?>
				<img id="nextImages" src="images/next_icon.png" onclick="pageScroll(<?php echo $rightLimit;?>);"
				<?php if($rightLimit >= $totalCount){ echo 'style="margin-left:5px;display:none;"'; }else{ echo 'style="margin-left:5px;"'; }?> />
	</div><br clear="all" />
</div>
</body>
</html>
<?php }else{?>
	<div style="margin-left:10px;color:#000;"><i>No Record Found</i></div>
<? }?>
