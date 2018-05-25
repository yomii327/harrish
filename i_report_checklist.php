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
$obj = new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}

if(isset($_REQUEST['name'])){
	$projID = '';	$locationCL = '';	$subLocationCL = '';	$sub_subLocationCL = '';	$where = '';
	$totalCount = 0;
	$locArray = array();
	if(!empty($_REQUEST['projNameCL'])){
		$projID = $_REQUEST['projNameCL'];
		$projectName = $obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projNameCL'], 'project_name');
	}
	
	if(!empty($_REQUEST['locationCL'])){
		$locArray[] = $_REQUEST['locationCL'];
		$locationCL = $_REQUEST['locationCL'];
	}

	if(!empty($_REQUEST['subLocationCL'])){
		$locArray[] = $_REQUEST['subLocationCL'];
		$subLocationCL = $_REQUEST['subLocationCL'];
	}
	
	if(!empty($_REQUEST['sub_subLocationCL'])){
		$locArray[] = $_REQUEST['sub_subLocationCL'];
		$sub_subLocationCL = $_REQUEST['sub_subLocationCL'];
	}

	$locLupCount = sizeof($locArray);
	$locString = '';	$locStringSec = '';
	
	$locString = join(',', $locArray);

	if($locLupCount < 4 && $locLupCount == 3){//Till Sublocation 2 selected
		$locStringSec = $obj->subLocationsId($sub_subLocationCL, ', ');
		$searchSubLoc = $locStringSec;
		$where = ' AND lc.location_id IN ('.$searchSubLoc.')';
	}else if($locLupCount < 3 && $locLupCount == 2){//Till Sublocation 1 selected
		$locStringSec = $obj->subLocationsId($subLocationCL, ', ');
		$searchSubLoc = $locStringSec;
		$where = ' AND lc.location_id IN ('.$searchSubLoc.')';
	}else  if($locLupCount < 2 && $locLupCount == 1){//Till Root Location selected
		$locStringSec = $obj->subLocationsId($locationCL, ', ');	
		$searchSubLoc = $locStringSec;
		$where = ' AND lc.location_id IN ('.$searchSubLoc.')';
	}
	
//Code for find Location title array 	
	$locTitleArray = array();
	if($locString != ''){
		$locTArray = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'project_id = '.$projID.' AND location_id in ('.$locString.')');
		if(!empty($locTArray)){
			foreach($locTArray as $tArray){
				$locTitleArray[$tArray['location_id']] = $tArray['location_title'];
			}
		}
	}
//Main Query Part
$mainQuery = "SELECT
				lc.location_check_list_id,
				cl.check_list_items_name,
				lc.check_list_items_status,
				lc.location_id,
				loc.location_title
			FROM
				project_locations AS loc,
				check_list_items AS cl,
				location_check_list AS lc
			WHERE
				loc.location_id = lc.location_id AND
				lc.check_list_items_id = cl.check_list_items_id AND
				lc.project_id = ".$projID." AND
				loc.is_deleted = 0 AND
				cl.is_deleted = 0 AND
				lc.is_deleted = 0".$where;
				
$rs = mysql_query($mainQuery);
	
$noInspection  = mysql_num_rows($rs);
$noPages = ceil(($noInspection-31)/43 +1);
	if($noInspection > 0){
		$html='<table width="98%" border="0" align="center">
			<tr>
				<td width="40%"></td>
				<td width="60%" align="right" style="padding-right:20px;">
					<img src="company_logo/logo.png" height="40"  />
				</td>
			</tr>
			<tr>
				<td width="40%" style="font-size:14px"><u><b>Checklist Report</b></u></td>
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
				<td style="font-size:12px"><strong>Checklist Items: </strong>'.$noInspection.'</td>
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
		if(!empty($_REQUEST['locationCL']) && empty($_REQUEST['subLocationCL'])){
			$html .= '<td width="150" style="font-size:11px;"><b>Location Name : </b></td>
			<td width="100" style="font-size:11px;">'.$locTitleArray[$locationCL].'</td>';$jk++;
		}
		if(!empty($_REQUEST['subLocationCL']) && !empty($_REQUEST['sub_subLocationCL'])){
			$html .= '<td width="150" style="font-size:11px;"><b>Location Name : </b></td>
			<td width="100" style="font-size:11px;">'.$locTitleArray[$locationCL].'</td>';$jk++;
			$html .= '<td width="150" style="font-size:11px;"><b>Sub Location 1 : </b></td>
			<td width="100" style="font-size:11px;">'.$locTitleArray[$subLocationCL].'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
			$html .= '<td width="150" style="font-size:11px;"><b>Sub Location 2 : </b></td>
			<td width="100" style="font-size:11px;">'.$locTitleArray[$sub_subLocationCL].'</td>';$jk++;
		}else{
			if(!empty($_REQUEST['locationCL']) && !empty($_REQUEST['subLocationCL'])){
				$html .= '<td width="150" style="font-size:11px;"><b>Location Name : </b></td>
				<td width="100" style="font-size:11px;">'.$locTitleArray[$locationCL].'</td>';$jk++;
				$html .= '<td width="150" style="font-size:11px;"><b>Sub Location Name : </b></td>
				<td width="100" style="font-size:11px;">'.$locTitleArray[$subLocationCL].'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
			}
		}
		$html .= '</tr></table></td></tr></table>';
		$html .= '<table width="98%" class="collapse" cellpadding="0" cellspaccing="0" align="center" border="0">
			<tr>
				<td style="font-size:14px;"><strong>Checklist Items Name</strong></td>
				<td style="font-size:14px;"><strong>Status</strong></td>
			</tr>';
			$currLocID = '';
			$location = '';
		$i=0;$pageCount = 1;
		$pagebreakCount = 31;
		while($row = mysql_fetch_assoc($rs)){
			$i++;
/*			if($pageCount == 1 && $i == $pagebreakCount){
				$html .= '</table><div style="page-break-after: always;"></div><table width="98%" cellpadding="0" align="center" cellspacing="0" class="collapse">
				<tr>
					<td style="font-size:14px;"><strong>Checklist Items Name</strong></td>
					<td style="font-size:14px;"><strong>Status</strong></td>
				</tr>';
				$pagebreakCount = 42;
				$pageCount++;
				$i=0;
			}else{
				if($i == $pagebreakCount){
					$html .= '</table><div style="page-break-after: always;"></div><table width="98%" cellpadding="0" align="center" cellspacing="0" class="collapse">
					<tr>
						<td style="font-size:14px;"><strong>Checklist Items Name</strong></td>
						<td style="font-size:14px;"><strong>Status</strong></td>
					</tr>';
					$pageCount++;
					$pagebreakCount=$pagebreakCount+$pagebreakCount;
				}
			}*/
			if($currLocID != $row['location_id']){$i++;
				$location = $obj->subLocations($row['location_id'], ' > ');
				$html .= '<tr><td colspan="2" style="font-size:12px;"><strong>'.$location.'</strong></td></tr>';
			}
			$html .= '<tr>
				<td style="font-size:12px;">'.$row['check_list_items_name'].'</td>
				<td style="font-size:12px;">'.$row['check_list_items_status'].'</td>
			</tr>';
			$currLocID = $row['location_id'];
		}
		$html .= '</table>';		
		$mtime = microtime(); 
		$mtime = explode(" ",$mtime); 
		$mtime = $mtime[1] + $mtime[0]; 
		$endtime = $mtime; 
		$totaltime = ($endtime - $starttime); 
		$totaltime = number_format($totaltime, 2, '.', '');?>
	
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Untitled Document</title>
		</head>
		<body>
		<div id="mainContainer">
			<div class="buttonDiv">
				<span style="padding-left:25px;font-size:15px;"><?php echo $noInspection.' results ('.$totaltime.' seconds)';?></span><br /><br />
				<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
				<img onClick="downloadPDFCL();"src="images/download_btn.png" style="float:right;" />
			</div><br clear="all" />
			<div id="htmlContainer">
				<?php echo $html;?>
			</div>
		</div>
		</body>
		</html>
<?php }else{
		echo "No Record Found !";
	}
} ?>