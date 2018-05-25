<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');
error_reporting(0);

session_start();
set_time_limit(6000000000000000000);
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
		$where=" and pi.project_id='".$_REQUEST['projName']."'";
		$projectName = $obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projName'], 'project_name');
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
				$mulIssueToWhere .= " OR (isi.issued_to_name LIKE '".addslashes($isMul[$g])." (%' OR isi.issued_to_name LIKE '".addslashes($isMul[$g])."') ";
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
	if($_SESSION['userRole'] == 'Sub Contractor'){
		$where.=" AND (isi.issued_to_name LIKE '".$_SESSION['userIssueTo']." (%' OR isi.issued_to_name LIKE '".$_SESSION['userIssueTo']."') ";
	}

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
	
#echo $where;die;
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
	
	$where .=  " and pi.inspection_type!='Memo'";
	
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
	#echo '<pre>';print_r($proInsData);die;
	
	$ajaxReplay = $OverAllCount.' Records';
	$noPages = ceil(($totalCount-1)/2 +1);
	if($noInspection > 0){
	$html='<table width="98%" border="0" align="center">
			<tr>
				<td width="40%"></td><td width="60%" align="right" style="padding-right:20px;">';
			$html .='<img src="company_logo/logo.png" height="40"  /></td>
			</tr>
			<tr>
				<td width="40%" style="font-size:14px"><u><b>Executive Report</b></u></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Project Name: </strong>'.$projectName.'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Date: </strong>'.date('d/m/Y').'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Report filtered by: </strong></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="padding-left:30px;" colspan="2"><table width="510" border="0"><tr>';
				$jk=0;
				if(!empty($_REQUEST['location'])){
					$html .= '<td width="140" style="font-size:11px;"><b>Location Name: </b></td>
					<td width="110" style="font-size:11px;">'.$_REQUEST['locationName'].'</td>';$jk++;
				}
				if(!empty($_REQUEST['subLocation'])){
					$html .= '<td width="140" style="font-size:11px;"><b>Sub Location: </b></td>
					<td width="110" style="font-size:11px;">'.$_REQUEST['subLocationName'].'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['sub_subLocation'])){
					$html .= '<td width="140" style="font-size:11px;"><b>Sub Location 1: </b></td>
					<td width="110" style="font-size:11px;">'.$_REQUEST['sub_subLocationName'].'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['sub_subLocation2']) && $_REQUEST['sub_subLocation2']!='null'){
					$html .= '<td width="140" style="font-size:11px;"><b>Sub Location 2: </b></td>
					<td width="110" style="font-size:11px;">'.$_REQUEST['sub_subLocationName2'].'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['sub_subLocation3']) && $_REQUEST['sub_subLocation3']!='null'){
					$html .= '<td width="140" style="font-size:11px;"><b>Sub Location 3: </b></td>
					<td width="110" style="font-size:11px;">'.$_REQUEST['sub_subLocationName3'].'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				/*if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
					$html .= '<td width="140" style="font-size:11px;"><b>Status : </b></td>
					<td width="110" style="font-size:11px;">'.$_REQUEST['status'].'</td>';
					$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}*/
				if(!empty($_REQUEST['inspectedBy']) && isset($_REQUEST['inspectedBy'])){
					$html .= '<td width="140" style="font-size:11px;"><b>Inspect By : </b></td>
					<td width="110" style="font-size:11px;">'.$_REQUEST['inspectedBy'].'</td>';
					$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['issuedTo']) && isset($_REQUEST['issuedTo'])){
					$html .= '<td width="140" style="font-size:11px;"><b>Issue To : </b></td>
					<td width="110" style="font-size:11px;">'.str_replace("'", "", $mulIssueTo).'</td>';
					$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['priority']) && isset($_REQUEST['priority'])){
					$html .= '<td width="140" style="font-size:11px;"><b>Priority : </b></td>
					<td width="110" style="font-size:11px;">'.$_REQUEST['priority'].'</td>';
					$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['inspecrType']) && isset($_REQUEST['inspecrType'])){
					$html .= '<td width="140" style="font-size:11px;"><b>Inspection Type : </b></td>
					<td width="110" style="font-size:11px;">'.$_REQUEST['inspecrType'].'</td>';
					$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['costAttribute']) && isset($_REQUEST['costAttribute'])){
					$html .= '<td width="140" style="font-size:11px;"><b>Cost Attribute : </b></td>
					<td width="110" style="font-size:11px;">'.$_REQUEST['costAttribute'].'</td>';
					$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['raisedBy']) && isset($_REQUEST['raisedBy'])){
					$html .= '<td width="140" style="font-size:11px;"><b>Raised By : </b></td>
					<td width="110" style="font-size:11px;">'.$_REQUEST['raisedBy'].'</td>';
					$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['DRF']) && isset($_REQUEST['DRF']) || !empty($_REQUEST['DRT']) && isset($_REQUEST['DRT'])){
					$html .= '<td width="140" style="font-size:11px;"><b>Date Raised : </b></td>
					<td width="110" style="font-size:11px;">'.$_REQUEST['DRF'].' to '.$_REQUEST['DRT'].'</td>';
					$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				if(!empty($_REQUEST['FBDF']) && isset($_REQUEST['FBDF']) || !empty($_REQUEST['FBDT']) && isset($_REQUEST['FBDT'])){
				   $html .= '<td width="140" style="font-size:11px;""><b>Fixed By Date : </b></td>
				   <td width="110" style="font-size:11px;"">'.$_REQUEST['FBDF'].' to '.$_REQUEST['FBDT'].'</td>';
					$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
				}
				$html .= '</tr></table></td></tr></table>';
				$i=$offset+1;
				$pageCount = 1;
				if(isset($offsetPage)){
					$pageCount = $pageCount + $offsetPage;
				}
				if ((intval($i/2)*2) == $i && $i!=1){
					$pageCount++;
					$html .= '<div style="page-break-before: always;"></div>Page: </strong>'.$pageCount.' of '.$noPages . "<br/><br/>" . $i . ".";
				}else{
					$html .= "<br/><br/>" . $i . ".<br/>";
				}
				$html .='<table width="98%" class="collapse" cellpadding="0" cellspaccing="0" align="center" border="0">
					<tr>
						<td width="70%" align="center" style="background-color:#CCCCCC;"><i>Location</i></td>
						<td width="10%" align="center" style="background-color:#CCCCCC;"><i>Open</i></td>
						<td width="10%" align="center" style="background-color:#CCCCCC;"><i>Closed</i></td>
						<td width="10%" align="center" style="background-color:#CCCCCC;"><i>Pending</i></td>
					</tr>';
					$openCount = 0;$closeCount = 0;$penndingCount = 0;
					foreach($proInsData as $piData){
						$keyArray = array_keys($piData);
						$lupCont = sizeof($keyArray);
						for($i=0; $i<$lupCont; $i++){
							if($keyArray[$i]!=''){
								$html .='<tr>
								<td>'.$keyArray[$i].'</td>
								<td align="center">'.$piData[$keyArray[$i]][0]['open'].'</td>';
								$openCount=$openCount + $piData[$keyArray[$i]][0]['open'];
								$html .='<td align="center">'.$piData[$keyArray[$i]][0]['closed'].'</td>';
								$closeCount=$closeCount + $piData[$keyArray[$i]][0]['closed'];
								$html .='<td align="center">'.$piData[$keyArray[$i]][0]['pending'].'</td>';
								$penndingCount=$penndingCount + $piData[$keyArray[$i]][0]['pending'];
								$html .='<tr>';
							}
						}
					}
					$html .= '<tr>
						<td align="center" style="background-color:#CCCCCC;"><h3>Total</h3></td>
						<td align="center" style="background-color:#CCCCCC;">'.$openCount.'</td>
						<td align="center" style="background-color:#CCCCCC;">'.$closeCount.'</td>
						<td align="center" style="background-color:#CCCCCC;">'.$penndingCount.'</td>
					</tr>
				</table>';

				$html .= '<br /><br /><br /><tr>
							</br>
							<td style="font-size:12px"><strong>Project Defect Clause: </strong>'.$defect_clause.'</td>
							<td>&nbsp;</td>
						</tr>';
						
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
		<span style="padding-left:25px;font-size:15px;"><?php echo $OverAllCount.' results ('.$totaltime.' seconds)';?></span><br /><br />
		<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
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
