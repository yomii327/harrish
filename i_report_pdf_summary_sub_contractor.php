<?php
ob_start();
session_start();

include('includes/commanfunction.php');

$obj = new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}
	$mulIssueTo = '';	 $issToForInspWhere = '';
	$issued_to_add='';
	$subwhere = '';
	$orderby = "ORDER BY F.issued_to_name";

	if(!isset($_REQUEST['startWith'])){$offset = 0;}else{$offset = $_REQUEST['startWith'];}
	
	$report_type = 'pdfSummayWithImages';
	$images_path = "photo_summary/";
	$dimages_path = "signoff_summary/";
	
	if ($report_type == "pdfSummayHD"){
		$images_path = "";
		//$dimages_path = "";
	}
	
	if(!empty($_REQUEST['searchKeyward'])){
		$postCount++;
		$locationRows = $obj->selQRYMultiple ("location_id", "project_locations", "project_id=".$_REQUEST['projName'] . " AND location_title LIKE '%".trim($_REQUEST['searchKeyward'])."%' AND is_deleted=0" );
		$location_id_arr = array();
		foreach ($locationRows as $locationID){
			$location_id_arr[] = $locationID["location_id"];	
		}
		$subwhere = join(",", $location_id_arr);
		//$where.=" AND (F.issued_to_name LIKE '%".trim($_REQUEST['searchKeyward'])."%'";
		//$splCon	.= "(F.issued_to_name LIKE '%".trim($_REQUEST['searchKeyward'])."%'";
			if($subwhere!=''){
				$where.= " AND I.location_id in (".$subwhere.")";
				$splCon	.= " AND I.location_id in (".$subwhere.")";
			}
		//$where.= ")";
		//$splCon	.= ")";
	}
	
	if(!empty($_REQUEST['projName'])){
		$where .=" AND I.project_id='".$_REQUEST['projName']."'";
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
	$finalHTML ='';
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
	$ajaxReplay = $noInspection.' Records';
	
	$noPages = ceil(($noInspection-14)/17 +1);
	
	if(mysql_num_rows($ri) > 0){
	
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
		$issueEmails = $obj->selQRYMultiple('distinct issue_to_name, issue_to_email', 'inspection_issue_to', 'project_id = '.$_REQUEST['projName'].' AND is_deleted = 0 AND issue_to_name IN ('.$issueToNameStr.')  group by issue_to_name');
	}
	
	foreach($issueEmails as $isEmail){
		$stArr = array();
		if($isEmail['issue_to_name'] != ''){
			$stArr['issueToName'] = $isEmail['issue_to_name'];
		}
		if($isEmail['issue_to_email'] != '' && strpos($isEmail['issue_to_email'], '@') !== false){
			$stArr['email'] = $isEmail['issue_to_email'];
		}else{
			$stArr['email'] = '';
		}
		$toEmailIDS[] = $stArr;
	}
	$toEmailIDS['arrSize'] = sizeof($toEmailIDS);
	$toEmailIDS['reportType'] = 'Summary Report for Sub Contractor';
#echo '<pre>';	print_r($toEmailIDS);die;
//issue to data fetch here
	
		$displayFlag = true;
		$html = '<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td width="40%"></td><td width="70%" align="right" style="padding-right:20px;"><img src="company_logo/logo.png" height="40"  /></td>
					</tr>';
			if($offset == 0){
				$html .= '
					<tr>
						<td width="40%" style="font-size:14px;"><u><b>Summary Report for Sub Contractor</b></u></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td  style="font-size:14px;"><strong>Project Name : </strong>'.$obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projName'], 'project_name').'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td  style="font-size:14px;"><strong>IssueTo Name : </strong>'.$firstIssueTo.'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="font-size:14px;"><strong>Date : </strong>'.date('d / m / Y').'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="font-size:14px"><strong>Inspections: </strong>'.$firstInspectionCount.'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="font-size:14px;"><strong>Report Filtered by : </strong></td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td style="padding-left:30px;" colspan="2"><table width="600" border="0"><tr>';
						$jk=0;
				if(!empty($_REQUEST['sortby']) && isset($_REQUEST['sortby'])){
					if($_REQUEST['sortby'] == 'location_id'){
						$sortby = 'Location';
					}else if($_REQUEST['sortby'] == 'inspection_date_raised'){
						$sortby = 'Date Raised';
					}else if($_REQUEST['sortby'] == 'issued_to_name'){
						$sortby = 'Issued To';
					}
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Sort By : </b></td>
					<td width="200" style="font-size:11px;" valign="top"">'.$sortby.'</td>';
					$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['searchKeyward']) && isset($_REQUEST['searchKeyward'])){
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Search keyword : </b></td>
					<td width="200" style="font-size:11px;" valign="top"">'.$_REQUEST['searchKeyward'].'</td>';
					$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}
	
				if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Location&nbsp;Name: </b></td>
						<td width="200" style="font-size:11px;" valign="top"">'.$locationSearchName.'</td>';$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['sub_subLocation'])){
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Location&nbsp;Name: </b></td>
					<td width="200" style="font-size:11px;" valign="top"">'.$locationSearchName.'</td>';$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Sub&nbsp;Location: </b></td>
					<td width="200" style="font-size:11px;" valign="top"">'.$subLocationSearchName.'</td>';$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Sub&nbsp;Location&nbsp;1: </b></td>
					<td width="200" style="font-size:11px;" valign="top"">'.$subSubLocationSearchName.'</td>';$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}else{
					if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
						$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Location&nbsp;Name: </b></td>
						<td width="200" style="font-size:11px;" valign="top"">'.$locationSearchName.'</td>';$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
						$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Sub&nbsp;Location: </b></td>
						<td width="200" style="font-size:11px;" valign="top"">'.$subLocationSearchName.'</td>';$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
					}
				}
				if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Status: </b></td>
					<td width="100" style="font-size:11px;" valign="top"">'.$_REQUEST['status'].'</td>';
					$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['inspectedBy']) && isset($_REQUEST['inspectedBy'])){
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Inspected By: </b></td>
					<td width="100" style="font-size:11px;" valign="top"">'.$_REQUEST['inspectedBy'].'</td>';
					$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['issuedTo']) && isset($_REQUEST['issuedTo'])){
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Issued To: </b></td>
					<td width="100" style="font-size:11px;" valign="top"">'.str_replace("'", "", $mulIssueTo).'</td>';
					$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['priority']) && isset($_REQUEST['priority'])){
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Priority: </b></td>
					<td width="100" style="font-size:11px;" valign="top"">'.$_REQUEST['priority'].'</td>';
					$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['inspecrType']) && isset($_REQUEST['inspecrType'])){
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Inspection Type: </b></td>
					<td width="100" style="font-size:11px;" valign="top"">'.$_REQUEST['inspecrType'].'</td>';
					$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['costAttribute']) && isset($_REQUEST['costAttribute'])){
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Cost Attribute: </b></td>
					<td width="100" style="font-size:11px;" valign="top"">'.$_REQUEST['costAttribute'].'</td>';
					$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['raisedBy']) && isset($_REQUEST['raisedBy'])){
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Raised By: </b></td>
					<td width="110" style="font-size:11px;" valign="top"">'.$_REQUEST['raisedBy'].'</td>';
					$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['DRF']) && isset($_REQUEST['DRF']) || !empty($_REQUEST['DRT']) && isset($_REQUEST['DRT'])){
					$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Date Raised: </b></td>
					<td width="200" style="font-size:11px;" valign="top"">'.$_REQUEST['DRF'].' to '.$_REQUEST['DRT'].'</td>';
					$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['FBDF']) && isset($_REQUEST['FBDF']) || !empty($_REQUEST['FBDT']) && isset($_REQUEST['FBDT'])){
				   $html .= '<td width="170" style="font-size:11px;" valign="top"""><b>Fixed By Date: </b></td>
				   <td width="200" style="font-size:11px;" valign="top""">'.$_REQUEST['FBDF'].' to '.$_REQUEST['FBDT'].'</td>';
					$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				$html .= '</tr></table></td></tr>';
			}
			$html .= '</table><br />
			<table width="98%" cellpadding="0" cellspacing="0" align="center" class="collapse">
			<tr><td widht="3%" style="font-size:10px" align="center">ID</td>
			<td widht="3%" style="font-size:10px" align="center">Inspecton Type</td>';
		if($report_type == "pdfSummayWithImages"){
			$html .= '<td width="17%" style="font-size:10px;" align="center"><strong>Location</strong></td>';
		}else{
			$html .= '<td width="20%" style="font-size:10px;" align="center"><strong>Location</strong></td>';
		}
		if($report_type == "pdfSummayWithImages"){
			$html .= '<td width="25%" style="font-size:10px;" align="center"><strong>Description</strong></td>';
		}else{
			$html .= '<td width="30%" style="font-size:10px;" align="center"><strong>Description</strong></td>';
		}
		$html .= '<td width="4%" style="font-size:10px;" align="center"><strong>Inspected By</strong></td>
				<td width="4%" style="font-size:10px;" align="center"><strong>Date Raised</strong></td>
				<td width="8%" style="font-size:10px;" align="center"><strong>Raised&nbsp;By</strong></td>
				<td width="4%" style="font-size:10px;" align="center"><strong>Fix By Date</strong></td>
				<td width="4%" style="font-size:10px;" align="center"><strong>Status</strong></td>';
				if($report_type == "pdfSummayWithImages"){
					$html .= '<td width="8%" style="font-size:10px;" align="center"><strong>Image 1</strong></td><td width="10%" style="font-size:10px;" align="center"><strong>Image 2</strong></td><td width="8%" style="font-size:10px;" align="center"><strong>Drawing</strong></td>
							<td width="8%" style="font-size:10px;" align="center"><strong>Sign&nbsp;Off</strong></td>';
				}
			$html .= '</tr>';
			$i=0;$j=1;
			$oldIssueToName = '';
			$defect_clause = '';
			while($fi=mysql_fetch_assoc($ri)){
				if(isset($fi['defect_clause']) && !empty($fi['defect_clause']) && empty($defect_clause)){
					$defect_clause = $fi['defect_clause'];
				}

				if ($oldIssueToName != $fi["IssueToName"] && $oldIssueToName != ''){
					$i=0;
					$html .='</tr></table><div class="footer" style="text-align:center;font-weight:bold;margin-top:5px;font-size:10px;" >DefectID – Copyright Wiseworking 2012 / 2013</div>
					<div style="page-break-before: always;"></div>';

					$html .= '<tr>
							</br>
							<td style="font-size:12px"><strong>Project Defect Clause: </strong>'.$defect_clause.'</td>
							<td>&nbsp;</td>
						</tr>';

					$html .= '<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td width="40%"></td><td width="70%" align="right" style="padding-right:20px;"><img src="company_logo/logo.png" height="40"  /></td>
						</tr>
						<tr>
							<td width="40%" style="font-size:14px;"><u><b>Summary Report for Sub Contractor</b></u></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td  style="font-size:14px;"><strong>Project Name : </strong>'.$obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projName'], 'project_name').'</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td  style="font-size:14px;"><strong>IssueTo Name : </strong>'.$fi["IssueToName"].'</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td style="font-size:14px;"><strong>Date : </strong>'.date('d / m / Y').'</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td style="font-size:14px"><strong>Inspections: </strong>'.$countArray[$fi["IssueToName"]].'</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td style="font-size:14px;"><strong>Report Filtered by : </strong></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td style="padding-left:30px;" colspan="2"><table width="600" border="0"><tr>';
							$jk=0;
							if(!empty($_REQUEST['sortby']) && isset($_REQUEST['sortby'])){
								if($_REQUEST['sortby'] == 'location_id'){
									$sortby = 'Location';
								}else if($_REQUEST['sortby'] == 'inspection_date_raised'){
									$sortby = 'Date Raised';
								}else if($_REQUEST['sortby'] == 'issued_to_name'){
									$sortby = 'Issued To';
								}
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Sort By : </b></td>
								<td width="200" style="font-size:11px;" valign="top"">'.$sortby.'</td>';
								$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							if(!empty($_REQUEST['searchKeyward']) && isset($_REQUEST['searchKeyward'])){
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Search keyword : </b></td>
								<td width="200" style="font-size:11px;" valign="top"">'.$_REQUEST['searchKeyward'].'</td>';
								$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
							}
				
							if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Location&nbsp;Name: </b></td>
									<td width="200" style="font-size:11px;" valign="top"">'.$locationSearchName.'</td>';$jk++;
							}
							if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['sub_subLocation'])){
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Location&nbsp;Name: </b></td>
								<td width="200" style="font-size:11px;" valign="top"">'.$locationSearchName.'</td>';$jk++;
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Sub&nbsp;Location: </b></td>
								<td width="200" style="font-size:11px;" valign="top"">'.$obj->getDataByKey('project_locations', 'location_id', $_REQUEST['subLocation'], 'location_title').'</td>';$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Sub&nbsp;Location&nbsp;1: </b></td>
								<td width="200" style="font-size:11px;" valign="top"">'.$obj->getDataByKey('project_locations', 'location_id', $_REQUEST['sub_subLocation'], 'location_title').'</td>';$jk++;
							}else{
								if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
									$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Location&nbsp;Name: </b></td>
									<td width="200" style="font-size:11px;" valign="top"">'.$locationSearchName.'</td>';$jk++;
									$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Sub&nbsp;Location: </b></td>
									<td width="200" style="font-size:11px;" valign="top"">'.$obj->getDataByKey('project_locations', 'location_id', $_REQUEST['subLocation'], 'location_title').'</td>';$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
								}
							}
							if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Status: </b></td>
								<td width="100" style="font-size:11px;" valign="top"">'.$_REQUEST['status'].'</td>';
								$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							if(!empty($_REQUEST['inspectedBy']) && isset($_REQUEST['inspectedBy'])){
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Inspected By: </b></td>
								<td width="100" style="font-size:11px;" valign="top"">'.$_REQUEST['inspectedBy'].'</td>';
								$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							if(!empty($_REQUEST['issuedTo']) && isset($_REQUEST['issuedTo'])){
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Issued To: </b></td>
								<td width="100" style="font-size:11px;" valign="top"">'.str_replace("'", "", $mulIssueTo).'</td>';
								$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							if(!empty($_REQUEST['priority']) && isset($_REQUEST['priority'])){
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Priority: </b></td>
								<td width="100" style="font-size:11px;" valign="top"">'.$_REQUEST['priority'].'</td>';
								$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							if(!empty($_REQUEST['inspecrType']) && isset($_REQUEST['inspecrType'])){
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Inspection Type: </b></td>
								<td width="100" style="font-size:11px;" valign="top"">'.$_REQUEST['inspecrType'].'</td>';
								$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							if(!empty($_REQUEST['costAttribute']) && isset($_REQUEST['costAttribute'])){
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Cost Attribute: </b></td>
								<td width="100" style="font-size:11px;" valign="top"">'.$_REQUEST['costAttribute'].'</td>';
								$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							if(!empty($_REQUEST['raisedBy']) && isset($_REQUEST['raisedBy'])){
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Raised By: </b></td>
								<td width="110" style="font-size:11px;" valign="top"">'.$_REQUEST['raisedBy'].'</td>';
								$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							if(!empty($_REQUEST['DRF']) && isset($_REQUEST['DRF']) || !empty($_REQUEST['DRT']) && isset($_REQUEST['DRT'])){
								$html .= '<td width="170" style="font-size:11px;" valign="top""><b>Date Raised: </b></td>
								<td width="200" style="font-size:11px;" valign="top"">'.$_REQUEST['DRF'].' to '.$_REQUEST['DRT'].'</td>';
								$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							if(!empty($_REQUEST['FBDF']) && isset($_REQUEST['FBDF']) || !empty($_REQUEST['FBDT']) && isset($_REQUEST['FBDT'])){
							   $html .= '<td width="170" style="font-size:11px;" valign="top"""><b>Fixed By Date: </b></td>
							   <td width="200" style="font-size:11px;" valign="top""">'.$_REQUEST['FBDF'].' to '.$_REQUEST['FBDT'].'</td>';
								$jk++; if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							$html .= '</tr></table></td></tr></table>
				<table width="98%" cellpadding="0" cellspacing="0" align="center" class="collapse">
					<tr><td widht="3%" style="font-size:10px" align="center">ID</td>
					<td widht="3%" style="font-size:10px" align="center">Inspecton Type</td>
						<td width="17%" style="font-size:10px;" align="center"><strong>Location</strong></td>';
						if($report_type == "pdfSummayWithImages"){
							$html .= '<td width="25%" style="font-size:10px;" align="center"><strong>Description</strong></td>';
						}else{
							$html .= '<td width="30%" style="font-size:10px;" align="center"><strong>Description</strong></td>';
						}
						$html .= '<td width="4%" style="font-size:10px;" align="center"><strong>Inspected By</strong></td>
						<td width="4%" style="font-size:10px;" align="center"><strong>Date Raised</strong></td>
						<td width="4%" style="font-size:10px;" align="center"><strong>Raised&nbsp;By</strong></td>
						<td width="8%" style="font-size:10px;" align="center"><strong>Fix By Date</strong></td>
						<td width="4%" style="font-size:10px;" align="center"><strong>Status</strong></td>';
						if($report_type == "pdfSummayWithImages"){
							$html .= '<td width="8%" style="font-size:10px;" align="center"><strong>Image 1</strong></td><td width="10%" style="font-size:10px;" align="center"><strong>Image 2</strong></td><td width="8%" style="font-size:10px;" align="center"><strong>Drawing</strong></td>
									<td width="8%" style="font-size:10px;" align="center"><strong>Sign&nbsp;Off</strong></td>';
						}
					$html .= '</tr>';
				}
				if ($i%$pageBreak == 0 && $i > ($pageBreak-1)){
					$html .='</tr></table><div class="footer" style="text-align:center;font-weight:bold;margin-top:5px;font-size:10px;" >DefectID – Copyright Wiseworking 2012 / 2013</div>
					<div style="page-break-before: always;"></div>
					<table width="98%" cellpadding="0" cellspacing="0" align="center" class="collapse">
					<tr><td widht="3%" style="font-size:10px" align="center">ID</td>
					<td widht="3%" style="font-size:10px" align="center">Inspecton Type</td>
						<td width="17%" style="font-size:10px;" align="center"><strong>Location</strong></td>';
						if($report_type == "pdfSummayWithImages"){
							$html .= '<td width="25%" style="font-size:10px;" align="center"><strong>Description</strong></td>';
						}else{
							$html .= '<td width="30%" style="font-size:10px;" align="center"><strong>Description</strong></td>';
						}
						$html .= '<td width="4%" style="font-size:10px;" align="center"><strong>Inspected By</strong></td>
						<td width="4%" style="font-size:10px;" align="center"><strong>Date Raised</strong></td>
						<td width="4%" style="font-size:10px;" align="center"><strong>Raised&nbsp;By</strong></td>
						<td width="8%" style="font-size:10px;" align="center"><strong>Fix By Date</strong></td>
						<td width="4%" style="font-size:10px;" align="center"><strong>Status</strong></td>';
						if($report_type == "pdfSummayWithImages"){
							$html .= '<td width="8%" style="font-size:10px;" align="center"><strong>Image 1</strong></td><td width="10%" style="font-size:10px;" align="center"><strong>Image 2</strong></td><td width="8%" style="font-size:10px;" align="center"><strong>Drawing</strong></td>
									<td width="8%" style="font-size:10px;" align="center"><strong>Sign&nbsp;Off</strong></td>';
						}
					$html .= '</tr>';
				}
				$i++;
				$html .= '<tr><td widht="3%" style="font-size:10px" align="center">'.$fi["InspectionId"].'</td>
				<td widht="3%" style="font-size:10px" align="center">'.$fi["InspectonType"].'</td>';
		
				$locations = $obj->subLocations($fi["Location"], ' > ');	
				if($report_type == "pdfSummayWithImages"){
					$html .= '<td width="17%"><font style="font-size:10px;">'.stripslashes(wordwrap($locations, 25, '<br />')).'</font></td>';
				}else{
					$html .= '<td width="20%"><font style="font-size:10px;">'.stripslashes(wordwrap($locations, 30, '<br />')).'</font></td>';
				}
				if($report_type == "pdfSummayWithImages"){
					$html .= '<td width="25%"><font style="font-size:10px;">'.stripslashes(wordwrap($fi["Description"], 18, "<br />")).'</font></td>';
				}else{
					$html .= '<td width="30%"><font style="font-size:10px;">'.stripslashes(wordwrap($fi["Description"], 123, "<br />")).'</font></td>';
				}
				$html .= '<td width="5%"><font style="font-size:10px;">'.stripslashes(wordwrap($fi["InspectedBy"], 15, "<br />", true)).'</font></td>
				<td width="5%" align="center"><font style="font-size:10px;">';
				if($fi["DateRaised"] != '0000-00-00'){
					$html .= stripslashes(date("d/m/Y", strtotime($fi["DateRaised"])));
				}
				$html .= '</font></td>
				<td width="4%"><font style="font-size:10px;">'.$fi["RaisedBy"].'</font></td>
				<td width="8%" align="center"><font style="font-size:10px;">'.$fi["FixedByDate"].'</font></td>
				<td width="4%" align="center"><font style="font-size:10px;">'.$fi["Status"].'</font></td>';
				if($report_type == "pdfSummayWithImages"){
					$images = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$fi["InspectionId"].' AND is_deleted = 0 AND graphic_type = "images" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 2');
					$html .='<td width="8%" valign="middle" align="center" style="height:50px;">';
					if(isset($images[0]) && $images[0]['graphic_name'] != ''){
						$obj->resizeImages('inspections/photo/'.$images[0]['graphic_name'], 105, 66, 'inspections/photo/photo_summary/'.$images[0]['graphic_name']);
						if(file_exists('inspections/photo/'.$images_path.$images[0]['graphic_name'])){
							$html .='<img src="inspections/photo/'.$images_path.$images[0]['graphic_name'].'" style="width:110px;" />';
						}else{
							if (file_exists('inspections/photo/'.$images[0]['graphic_name']))
								$html .='<img src="inspections/photo/'.$images[0]['graphic_name'].'" style="width:110px;" />';
						}
					}
					$html .='</td>';
					$html .='<td width="8%" valign="middle" align="center" style="height:50px;">';
					if(isset($images[1]) && $images[1]['graphic_name'] != ''){
						$obj->resizeImages('inspections/photo/'.$images[1]['graphic_name'], 105, 66, 'inspections/photo/photo_summary/'.$images[1]['graphic_name']);
						if(file_exists('inspections/photo/'.$images_path.$images[1]['graphic_name'])){
							$html .='<img src="inspections/photo/'.$images_path.$images[1]['graphic_name'].'" />';
						}else{
							if (file_exists('inspections/photo/'.$images[1]['graphic_name']))
								$html .='<img src="inspections/photo/'.$images[1]['graphic_name'].'" style="width:105px;" />';
						}
					}
					$html .='</td>';
					$drawing = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$fi["InspectionId"].' AND is_deleted = 0 AND graphic_type = "drawing" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 1');
					$html .='<td width="8%" valign="middle" align="center" style="height:50px;">';
					if(isset($drawing[0]) && $drawing[0]['graphic_name'] != ''){
						$obj->resizeImages('inspections/drawing/'.$drawing[0]['graphic_name'], 105, 66, 'inspections/drawing/drawing_summary/'.$drawing[0]['graphic_name']);
						if(file_exists('inspections/drawing/drawing_summary/'.$images_path.$drawing[0]['graphic_name'])){
							$html .='<img src="inspections/drawing/drawing_summary/'.$images_path.$drawing[0]['graphic_name'].'" />';
						}else{
							if (file_exists('inspections/drawing/'.$drawing[0]['graphic_name']))
								$html .='<img src="inspections/drawing/'.$drawing[0]['graphic_name'].'" style="width:105px;" />';
						}
					}
					$html .='</td>
					<td width="8%" valign="middle" align="center" style="height:50px;">';
					if($fi['signoff'] != '' && $fi['Status'] == 'Closed'){
						$obj->resizeImages('inspections/signoff/'.$fi['signoff'], 105, 30, 'inspections/signoff/signoff_summary/'.$fi['signoff']);
						if(file_exists('inspections/signoff/signoff_summary/'.$dimages_path.$fi['signoff'])){
							$html .='<img src="inspections/signoff/signoff_summary/'.$dimages_path.$fi['signoff'].'"/>';
						}else{
							if (file_exists('inspections/signoff/'.$fi['signoff']))
								$html .='<img src="inspections/signoff/'.$fi['signoff'].'" style="width:100px;"  />';
						}
					}
					$html .='</td>';
				}
				$html .='</tr>';

				$oldIssueToName = $fi["IssueToName"];
			}
			$html .= '</table><br /><br /><br />';

			$html .= '<tr>
						</br>
						<td style="font-size:12px"><strong>Project Defect Clause: </strong>'.$defect_clause.'</td>
						<td>&nbsp;</td>
					</tr>';
			
			$html .= '<div class="footer" style="text-align:center;font-weight:bold;font-size:10px;" >DefectID – Copyright Wiseworking 2012 / 2013</div><div style="page-break-before: always;"></div>';
$html .= '<div id="emailIssueToData" style="display:none;">'.json_encode($toEmailIDS).'</div>';
		$finalHTML .= $html;
#		$finalHTML .= 'Report for '.$isData['issued_to_name'].$html;
	}else{
		$finalHTML .= 'Report for '.$isData['issued_to_name'].'<div style="margin-left:10px;color:#000;"><i>No Record Found</i></div>';
	}
	
	if($displayFlag){?>
		<div id="mainContainer">
		<div class="buttonDiv">
			<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
			<img onClick="emailPDFSubCont();" src="images/email.png" style="float:left;margin-left:160px;" />
			<img onClick="downloadPDF();"src="images/download_btn.png" style="float:right;" />
		</div><br clear="all" />
		<?php $pageCount = $totalCount / $limit;?>
		<div class="pagination" <?php if($totalCount <= $limit){ echo 'style="display:none;" ';}?> >
		<?php $leftLimit = $offset - $limit;
			if($leftLimit < 0){ $leftLimit = 0; }else{ }?>
			<img id="previousImages" src="images/prev_icon.png" onclick="pageScroll(<? echo $leftLimit;?> );"
			<?php if($leftLimit < 0){ echo 'style="display:none;float:left;"'; }else{ echo 'style="float:left"';} ?> />
			<?php if($pageCount > 0){
				for($l=0; $l<$pageCount; $l++){?>
					<span <? if(($l*$limit) == $offset){
						echo 'class="page_active" ';
					}else{
						echo 'class="page_deactive" ';
					}
					if($l >= 5){
						echo 'style="display:none;" ';
					}
					?>
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
			<?php echo $finalHTML;?>
		</div>
		<?php $pageCount = $totalCount / $limit;?>
		<div class="pagination" <?php if($totalCount <= $limit){ echo 'style="display:none;" ';}?> >
			<?php $leftLimit = $offset - $limit;
			if($leftLimit < 0){ $leftLimit = 0; }else{ }?>
			<img id="previousImages" src="images/prev_icon.png" onclick="pageScroll(<? echo $leftLimit;?> );"
			<?php if($leftLimit < 0){ echo 'style="display:none;float:left;"'; }else{ echo 'style="float:left"';} ?> />
			<?php if($pageCount > 0){
			for($l=0; $l<$pageCount; $l++){?>
				<span <? if(($l*$limit) == $offset){
					echo 'class="page_active" ';
				}else{
					echo 'class="page_deactive" ';
				}
				if($l >= 5){
					echo 'style="display:none;" ';
				}
				?>
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
		</div><br clear="all" />
	</div>
	<?php }else{?>
		<div style="margin-left:10px;color:#000;"><i>No Record Found</i></div>		
	<?php	}?>
