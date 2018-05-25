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
	$projID = '';	$locationQA = '';	$subLocationQA1 = '';	$subLocationQA2 = '';	$subLocationQA3 = '';
	$totalCount = 0;
	
	$locArray = array();
	if(!empty($_REQUEST['projNameQA'])){
		$projID = $_REQUEST['projNameQA'];
		$projectName = $obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projNameQA'], 'project_name');
	}
	
	if(!empty($_REQUEST['locationQA'])){
		$locArray[] = $_REQUEST['locationQA'];
		$locationQA = $_REQUEST['locationQA'];
	}

	if(!empty($_REQUEST['subLocationQA1'])){
		$locArray[] = $_REQUEST['subLocationQA1'];
		$subLocationQA1 = $_REQUEST['subLocationQA1'];
	}
	
	if(!empty($_REQUEST['subLocationQA2'])){
		$locArray[] = $_REQUEST['subLocationQA2'];
		$subLocationQA2 = $_REQUEST['subLocationQA2'];
	}
	
	if(!empty($_REQUEST['subLocationQA3'])){
		$locArray[] = $_REQUEST['subLocationQA3'];
		$subLocationQA3 = $_REQUEST['subLocationQA3'];
	}
	$locLupCount = sizeof($locArray);
	$locString = '';	$locStringSec = '';

	if($locLupCount < 4 && $locLupCount == 3){//Till Sublocation 2 selected
		$locStringSec = $obj->subLocationsDepthQA($subLocationQA2, ', ');
		$searchSubLoc = $locStringSec;
	}else if($locLupCount < 3 && $locLupCount == 2){//Till Sublocation 1 selected
		$locStringSec = $obj->subLocationsDepthQA($subLocationQA1, ', ');
		$searchSubLoc = $locStringSec;
	}else  if($locLupCount < 2 && $locLupCount == 1){//Till Root Location selected
		$locStringSec = $obj->subLocationsDepthQA($locationQA, ', ');	
		$searchSubLoc = $locStringSec;
	}else if($locLupCount < 1 && $locLupCount == 0){//Only Project Selected
	
	}else{
		$searchSubLoc = $subLocationQA3;
	}
//Code for find Location title array 	
	for($i=0; $i<$locLupCount; $i++){
		if($locString == ''){
			$locString = $locArray[$i];
		}else{
			$locString .= ', '.$locArray[$i];
		}
	}
	if($locStringSec != ''){
		$locString = $locString. ', ' . $locStringSec;
	}
	
	$locData = $obj->selQRYMultiple('location_id, location_title', 'qa_task_locations', 'project_id = '.$projID.' AND location_id IN ('.$locString.') AND is_deleted = 0 GROUP BY location_id ORDER BY location_id');
	$locArrayData = array();
	foreach($locData as $ldata){
		$locArrayData[$ldata['location_id']] = $ldata['location_title'];
	}
//In Case sublocation 2 not selected (i.e. empty subLocationQA2)
//create array its sublocations
	$subLoc1[] = $subLocationQA1;
	$subLocArray1[] = $subLocationQA1;
	$subLocArray2 = array($subLocationQA2);
	if(empty($subLocationQA2) && !empty($subLocationQA1)){
		$subLocArray3 = array();
		$subLoc2Data = $obj->selQRYMultiple('location_id', 'qa_task_locations', 'project_id = '.$projID.' AND is_deleted = 0 AND location_parent_id = '.$subLocationQA1.' ORDER BY location_title');
		foreach($subLoc2Data as $sData){
			$subLoc3Data = $obj->selQRYMultiple('location_id', 'qa_task_locations', 'project_id = '.$projID.' AND is_deleted = 0 AND location_parent_id = '.$sData['location_id'].' ORDER BY location_title');
			$tempLocArr = array();
			foreach($subLoc3Data as $s3Data){
				$tempLocArr[] = $s3Data['location_id'];
			}
			$subLocArray3[$sData['location_id']] = $tempLocArr;
		}
		$subLocArray2 = array_keys($subLocArray3);
	}

//In Case sublocation 1 not selected (i.e. empty subLocationQA1)
//create array its sublocations and there sublocations array
	if(empty($subLocationQA1) && !empty($locationQA)){
		$subLocArray1 = array();
		$subLocArray2 = array();
		$subLoc1Data = $obj->selQRYMultiple('location_id', 'qa_task_locations', 'project_id = '.$projID.' AND is_deleted = 0 AND location_parent_id = '.$locationQA.' ORDER BY location_id');
		$subLoc2Arr = array();
		foreach($subLoc1Data as $s1Data){
			$subLoc2Data = $obj->selQRYMultiple('location_id', 'qa_task_locations', 'project_id = '.$projID.' AND is_deleted = 0 AND location_parent_id = '.$s1Data['location_id'].' ORDER BY location_id');
			$tempLocArr2 = array();
			foreach($subLoc2Data as $s2Data){
				$tempLocArr2[] = $s2Data['location_id'];
				$subLocArray2[] = $s2Data['location_id'];
			}
			$subLocArray1[$s1Data['location_id']] = $tempLocArr2;
		}
		$subLoc1 = array_keys($subLocArray1);
		
		for($i=0;$i<count($subLocArray2);$i++){
			$subLoc3Data = $obj->selQRYMultiple('location_id', 'qa_task_locations', 'project_id = '.$projID.' AND is_deleted = 0 AND location_parent_id = '.$subLocArray2[$i].' ORDER BY location_id');
			$tempLocArr3 = array();
			foreach($subLoc3Data as $s3Data){
				$tempLocArr3[] = $s3Data['location_id'];
			}
			$subLocArrayFinal[$subLocArray2[$i]] = $tempLocArr3;
		}
	}

//Code for find Location title array 	
	$taskData = $obj->selQRYMultiple('task_id, project_id, location_id, sub_location_id, task, status, comments, signoff_image, created_date, last_modified_date', 'qa_task_monitoring', 'project_id = '.$projID.' AND is_deleted = 0 AND location_id = '.$locationQA.' AND sub_location_id IN ('.$searchSubLoc.') ORDER BY task_id');
	$noInspection = count($taskData);
	
	if($noInspection > 0){
		$topHeader = '<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="4">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="4" align="right"><img src="company_logo/logo.png" height="40"  /></td>
						</tr>
					</table>';
		$g=0;
		for($k=0;$k<count($subLocArray2);$k++){
//Location Header for all locations
//To know the change in sub location 1
			$exSubLocid = $subLoc1[$g];
			if(empty($subLocationQA1) && !empty($locationQA)){
				if(!in_array($subLocArray2[$k], $subLocArray1[$exSubLocid])){
					$g++;
				}
			}
			$content = '';
			$header1[$k] = '<br /><br clear="all" /><table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="collapse">
			<tr>
				<td align="center" width="25%" style="font-size:14px;"><strong>PROJECT: </strong></td>
				<td colspan="3" align="center" style="font-size:14px;">'.$projectName.'</td>
			</tr>
			<tr>
				<td align="center" width="25%" style="font-size:14px;"><strong>ACTIVITY: </strong></td>
				<td colspan="3" style="background:#C0C0C0;font-size:14px;" align="center"><strong>'.$locArrayData[$subLocArray2[$k]].'</strong></td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="font-size:14px;"><strong>Room Number:</strong></td>
				<td colspan="2" align="center" width="50%" style="font-size:14px;"><strong>Level:</strong></td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="font-size:14px;">'.$locArrayData[$subLoc1[$g]].'</td>
				<td colspan="2" align="center" width="50%" style="font-size:14px;">'.$locArrayData[$locationQA].'</td>
			</tr>';
//Content part Here
			$subLocDataArray = array();
			$dataArray = array();
			$lupCount = sizeof($taskData);
			for($i=0; $i<$lupCount; $i++){
				if($taskData[$i]['created_date'] != $taskData[$i]['last_modified_date'] && $taskData[$i]['status'] != ''){
					$disDate = date('d/m/Y', strtotime($taskData[$i]['last_modified_date']));
				}else{
					$disDate = '';
				}
				if($i==0){
					$dataArray[] = array($taskData[$i]['task'], $taskData[$i]['status'], $disDate, $taskData[$i]['signoff_image'], $taskData[$i]['comments']);
				}else{
					if($subLocID == $taskData[$i]['sub_location_id']){
						$dataArray[] = array($taskData[$i]['task'], $taskData[$i]['status'], $disDate, $taskData[$i]['signoff_image'], $taskData[$i]['comments']);
					}else{
						$subLocDataArray[$subLocID] = $dataArray;
						$dataArray = array();
						$dataArray[] = array($taskData[$i]['task'], $taskData[$i]['status'], $disDate, $taskData[$i]['signoff_image'], $taskData[$i]['comments']);
					}
				}
				$subLocID = $taskData[$i]['sub_location_id'];
			}
			$subLocDataArray[$subLocID] = $dataArray;

			$subLocids = array_keys($subLocDataArray);
			if(empty($subLocationQA2) && !empty($subLocationQA1)){
				$subLocids = array_values($subLocArray3[$subLocArray2[$k]]);
			}
			if(empty($subLocationQA1) && !empty($locationQA)){
				$subLocids = array_values($subLocArrayFinal[$subLocArray2[$k]]);
			}
			if(!empty($subLocDataArray)){
				$taskCount = 0;
				for($i=0; $i<sizeof($subLocids); $i++){
					$pageCount = 0;$pageCount++;

					$content .= '<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0" class="collapse" style="margin-top:5px;">
						<tr>
							<td colspan="2" align="center" style="font-size:12px;"><strong>'.strtoupper($locArrayData[$subLocids[$i]]).'</strong></td>
							<td width="12%" align="center" style="font-size:12px;"><strong>INSPECTION</strong></td>
							<td width="12%" align="center" style="font-size:12px;"><strong>DATE</strong></td>
							<td width="12%" align="center" style="font-size:12px;"><strong>SIGN</strong></td>
							<td width="30%" colspan="3" align="center" style="font-size:12px;"><strong>COMMENTS</strong></td>
						</tr>';
					$subArray = $subLocDataArray[$subLocids[$i]];
					$taskLupCount = sizeof($subArray);
					for($j=0; $j<$taskLupCount; $j++){
						$pagebreak = 40;
						if($pageCount == 1){
							$pagebreakCount = 34;
							if($j == $pagebreakCount){
								$content .= '</table><table width="98%" cellpadding="0" align="center" cellspacing="0" class="collapse"><tr>
							<td colspan="2" align="center" style="font-size:12px;"><strong>'.strtoupper($locArrayData[$subLocids[$i]]).'</strong></td>
							<td width="12%" align="center" style="font-size:12px;"><strong>INSPECTION</strong></td>
							<td width="12%" align="center" style="font-size:12px;"><strong>DATE</strong></td>
							<td width="12%" align="center" style="font-size:12px;"><strong>SIGN</strong></td>
							<td width="30%" colspan="3" align="center" style="font-size:12px;"><strong>COMMENTS / CORRECTIVE <br />ACTION</strong></td>
						</tr>';
								$pageCount++;
								$pagebreakCount = $pagebreakCount + $pagebreak;	
							}
						}else{
							if($j == $pagebreakCount){
								$content .= '</table><table width="98%" cellpadding="0" align="center" cellspacing="0" class="collapse"><tr>
							<td colspan="2" align="center" style="font-size:12px;"><strong>'.strtoupper($locArrayData[$subLocids[$i]]).'</strong></td>
							<td width="12%" align="center" style="font-size:12px;"><strong>INSPECTION</strong></td>
							<td width="12%" align="center" style="font-size:12px;"><strong>DATE</strong></td>
							<td width="12%" align="center" style="font-size:12px;"><strong>SIGN</strong></td>
							<td width="30%" colspan="3" align="center" style="font-size:12px;"><strong>COMMENTS / CORRECTIVE <br />ACTION</strong></td>
						</tr>';
								$pageCount++;
								$pagebreakCount = $pagebreakCount + $pagebreak;
							}
						}
						$content .= '<tr>
							<td height="10" colspan="2" style="font-size:11px;">'.$subArray[$j][0].'</td>';
							if($subArray[$j][1] == 'NA'){
								$stt = 'N/A';
							}else{
								$stt = $subArray[$j][1];
							}
							$content .= '<td align="center" style="font-size:11px;">'.$stt.'</td>
							<td align="center" style="font-size:11px;">'.$subArray[$j][2].'</td>
							<td align="center" style="font-size:11px;">';
							if(isset($subArray[$j][3]) && $subArray[$j][3] != '' && $subArray[$j][1] == 'Yes'){
								$obj->resizeImages('inspections/signoff/'.$subArray[$j][3], 100, 50, 'inspections/signoff/qa_signoff_report/'.$subArray[$j][3]);
								if(file_exists('inspections/signoff/qa_signoff_report/'.$subArray[$j][3])){
									$content .= '<img src="inspections/signoff/qa_signoff_report/'.$subArray[$j][3].'" style="height:50px;" />';
								}else{
									if (file_exists('inspections/signoff/'.$subArray[$j][3])){
										$content .= '<img src="inspections/signoff/'.$subArray[$j][3].'" style="height:50px;" />';
									}
								}
							}
						$content .= '</td>
							<td colspan="3" style="font-size:11px;">'.$subArray[$j][4].'</td>
						</tr>';
					}
					$taskCount = $taskCount + $taskLupCount;
					$content .= '<tr>
						<td width="25%">&nbsp;</td>
						<td width="10%" align="center" style="font-size:14px;">Signed:</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td height="50" style="font-size:14px;">Spot Check - Foreman</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td height="50"  style="font-size:14px;">Spot Check - Foreman</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2" style="font-size:14px;">INSPECTION BY:</td>
						<td colspan="3" style="font-size:14px;">SIGNATURE:</td>
						<td colspan="3" style="font-size:14px;">DATE:</td>
					</tr>
					<tr>
						<td colspan="2" height="55">&nbsp;</td>
						<td colspan="3" height="55">&nbsp;</td>
						<td colspan="3" height="55">&nbsp;</td>
					</tr>
				</table>';
				
				}
			}
			$contentArray[$k] = $content;
			$totalCount = $totalCount + $taskCount;
		}
		$html = $topHeader;
		for($k=0;$k<count($subLocArray2);$k++){
			$html .= $header1[$k];
			$html .= $contentArray[$k];
		}
	
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
			<span style="padding-left:25px;font-size:15px;"><?php echo $totalCount.' results ('.$totaltime.' seconds)';?></span><br /><br />
			<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
			<img onClick="downloadPDFQA();"src="images/download_btn.png" style="float:right;" />
		</div><br clear="all" />
		<div id="htmlContainer">
			<?php echo $html;?>
		</div>
	</div>
	</body>
	</html>
<?php }else{
		echo $html = 'No Record Found !';
	}
}?>