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
//echo $owner_id;

if(isset($_REQUEST['name'])){
	$projID = '';	$locationCL = '';	$subLocationCL = '';	$subLocation1CL = ''; $subLocation2CL = ''; $subLocation3CL = '';	$where = '';
	$totalCount = 0;
	$locArray = array();
	if(!empty($_REQUEST['projNameCL'])){
		$projID = $_REQUEST['projNameCL'];
		$projectName = $obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projNameCL'], 'project_name');
	}else{
		
		$projID = $_SESSION['idp'];
		$projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name');
	}
	
	if(!empty($_REQUEST['locationCL'])){
		$locArray[] = $_REQUEST['locationCL'];
		$locationCL = $_REQUEST['locationCL'];
	}

	if(!empty($_REQUEST['subLocationCL'])){
		$locArray[] = $_REQUEST['subLocationCL'];
		$subLocationCL = $_REQUEST['subLocationCL'];
	}
	
	if(!empty($_REQUEST['subLocation1CL'])){
		$locArray[] = $_REQUEST['subLocation1CL'];
		$subLocation1CL = $_REQUEST['subLocation1CL'];
	}
	
	if(!empty($_REQUEST['subLocation2CL'])){
		$locArray[] = $_REQUEST['subLocation2CL'];
		$subLocation2CL = $_REQUEST['subLocation2CL'];
	}
	
	if(!empty($_REQUEST['subLocation3CL'])){
		$locArray[] = $_REQUEST['subLocation3CL'];
		$subLocation3CL = $_REQUEST['subLocation3CL'];
	}
	
	$locLupCount = sizeof($locArray);
	$locString = '';	$locStringSec = '';
	
	$locString = join(',', $locArray);

	if($locLupCount < 6 && $locLupCount == 5){//Till Sublocation 3 selected
		$locStringSec = $obj->subLocationsId($subLocation3CL, ', ');
		$searchSubLoc = $locStringSec;
		$where = ' AND lc.location_id IN ('.$searchSubLoc.')';
	}else if($locLupCount < 5 && $locLupCount == 4){//Till Sublocation 2 selected
		$locStringSec = $obj->subLocationsId($subLocation2CL, ', ');
		$searchSubLoc = $locStringSec;
		$where = ' AND lc.location_id IN ('.$searchSubLoc.')';
	}else if($locLupCount < 4 && $locLupCount == 3){//Till Sublocation 1 selected
		$locStringSec = $obj->subLocationsId($subLocation1CL, ', ');
		$searchSubLoc = $locStringSec;
		$where = ' AND lc.location_id IN ('.$searchSubLoc.')';
	}else if($locLupCount < 3 && $locLupCount == 2){//Till Sublocation selected
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
	
	if($locString != ''){
		$whereLocation = " AND location_id in (".$locString.")";
	}
//Main Query Part
$mainQuery = "SELECT location_id, qr_code FROM project_locations WHERE project_id = ".$projID." AND qr_code!='' AND is_deleted = 0".$whereLocation."";
			
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
				<td width="40%" style="font-size:14px"><u><b>Qr Code Report</b></u></td>
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
		if(!empty($_REQUEST['locationCL'])){
			$html .= '<td width="150" style="font-size:11px;"><b>Location Name : </b></td>
			<td width="100" style="font-size:11px;">'.$locTitleArray[$locationCL].'</td>';$jk++;
		}
		if(!empty($_REQUEST['subLocationCL']) && !empty($_REQUEST['subLocation1CL']) && !empty($_REQUEST['subLocation2CL'])){
			$html .= '<td width="150" style="font-size:11px;"><b>Sub Location Name : </b></td>
			<td width="100" style="font-size:11px;">'.$locTitleArray[$subLocationCL].'</td>';$jk++;
			$html .= '<td width="150" style="font-size:11px;"><b>Sub Location 1 : </b></td>
			<td width="100" style="font-size:11px;">'.$locTitleArray[$subLocation1CL].'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
			$html .= '<td width="150" style="font-size:11px;"><b>Sub Location 2 : </b></td>
			<td width="100" style="font-size:11px;">'.$locTitleArray[$subLocation2CL].'</td>';$jk++;
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
				<td style="font-size:14px;" colspan="2"><strong>QR Code</strong></td>
			</tr>';
			$currLocID = '';
			$location = '';
		$i=0;$pageCount = 1;
		$pagebreakCount = 31;
		$qrCodeImg = '';
		while($row = mysql_fetch_assoc($rs)){
			$i++;
			$qrCodeImg = 'qrcode_images/'.$owner_id.'/'.$row['qr_code'];
			if($currLocID != $row['location_id']){$i++;
				$location = $obj->subLocations($row['location_id'], ' > ');
				$html .= '<tr><td colspan="2" style="font-size:12px;"><strong>'.$location.'</strong></td></tr>';
			}
			$html .= '<tr>
				<td style="font-size:12px;" colspan="2"><img src="'.$qrCodeImg.'" width="120px" height="120px"></td></tr>';
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
		<div id="mainContainer" style="overflow-y:auto;">
			<div class="buttonDiv">
			<table width="50%">
				<tr>
					<td><span style="font-size:15px;"><?php echo $noInspection.' results ('.$totaltime.' seconds)';?></span></td>
					<td><img onClick="downloadPDFQrCodeCL();"src="images/download_btn.png" /></td>
				</tr>
			</table>
				
				<!--<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />-->
				<!--<img onClick="downloadPDFCL();"src="images/download_btn.png" style="float:right;" />-->
				
			</div><br clear="all" />
			<div id="htmlContainer">
				<?php // echo $html;?>
			</div>
		</div>
		</body>
		</html>
<?php }else{
		echo "No Record Found !";
	}
} ?>