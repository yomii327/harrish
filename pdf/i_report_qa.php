<?php
ob_start();
session_start();

//Code for Calculate Execution Time
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$starttime = $mtime; 
//Code for Calculate Execution Time

include('../includes/commanfunction.php');
$obj = new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}

if(isset($_REQUEST['name'])){
	$projID = '';	$locationQA = '';	$subLocationQA1 = '';	$subLocationQA2 = '';	$subLocationQA3 = '';	$content ='';
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
//Code for find Location title array 	

	$taskData = $obj->selQRYMultiple('task_id, project_id, location_id, sub_location_id, task, status, comments, signoff_image, created_date, last_modified_date', 'qa_task_monitoring', 'project_id = '.$projID.' AND is_deleted = 0 AND location_id = '.$locationQA.' AND sub_location_id IN ('.$searchSubLoc.') ORDER BY sub_location_id');

	$noInspection = count($taskData);
	
$topHeader = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
@charset "utf-8";
body{
	font-family : Trebuchet MS, Arial, serif;
	font-size: 10px;
	padding: 5px;
}
table.collapse {
	border-collapse: collapse;
	border: 1pt solid black;  
}
table.collapse td {
	border-collapse: collapse;
	border: 1pt solid black;
	padding: 2px;
}
</style>
</head>
<body>';
	if($noInspection > 0){
		$topHeader .= '<table width="555" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="4">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="4" align="right"><img src="../company_logo/logo.png" height="40"  /></td>
						</tr>
					</table><br /><br clear="all" />';
		
		$header1 = '<tr>
						<td align="center" width="139" style="font-size:14px;"><strong>PROJECT: </strong></td>
						<td colspan="3" align="center" style="font-size:14px;">'.$projectName.'</td>
					</tr>
					<tr>
						<td align="center" width="139" style="font-size:14px;"><strong>ACTIVITY: </strong></td>
						<td colspan="3" style="background:#C0C0C0;font-size:14px;" align="center"><strong>'.$locArrayData[$subLocationQA2].'</strong></td>
					</tr>';
		$header2 = '<tr>
						<td colspan="2" align="center" width="270" style="font-size:14px;"><strong>Room Number:</strong></td>
						<td colspan="2" align="center" width="270" style="font-size:14px;"><strong>Level:</strong></td>
					</tr>
					<tr>
						<td colspan="2" align="center" width="270" style="font-size:14px;">'.$locArrayData[$subLocationQA1].'</td>
						<td colspan="2" align="center" width="270" style="font-size:14px;">'.$locArrayData[$locationQA].'</td>
					</tr>';
					
		$header = '<table width="555" align="center" cellpadding="0" cellspacing="0" class="collapse">';
		$header .= $header1.$header2;
		$header .= '</table>';
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
				if(!empty($subLocDataArray)){
					$taskCount = 0;
					for($i=0; $i<sizeof($subLocids); $i++){
						$pageCount = 0;$pageCount++;
						$content .= '<table width="555" cellpadding="0" cellspacing="0" class="collapse" style="margin-top:5px;">
							<tr>
								<td colspan="2" width="193px" align="center" style="font-size:12px;"><strong>'.wordwrap(strtoupper($locArrayData[$subLocids[$i]]), 25, '<br />', true).'</strong></td>
								<td width="60px" align="center" style="font-size:12px;"><strong><strong>INSPECTION</strong></strong></td>
								<td width="60px" align="center" style="font-size:12px;"><strong>DATE</strong></td>
								<td width="60px" align="center" style="font-size:12px;"><strong>SIGN</strong></td>
								<td width="160px" colspan="3" align="center" style="font-size:12px;"><strong>COMMENTS</strong></td>
							</tr>';
						$subArray = $subLocDataArray[$subLocids[$i]];
						$taskLupCount = sizeof($subArray);
						for($j=0; $j<$taskLupCount; $j++){
							$pagebreak = 27;
							if($pageCount == 1){
								$pagebreakCount = 24;
								if($j == $pagebreakCount){
									$content .= '</table>
									<div style="page-break-after: always;"></div>
									<table width="555" cellpadding="0" cellspacing="0" class="collapse">
										<tr>
											<td colspan="2" width="193px" align="center" style="font-size:12px;"><strong>'.wordwrap(strtoupper($locArrayData[$subLocids[$i]]), 25, '<br />', true).'</strong></td>
											<td width="60px" align="center" style="font-size:12px;"><strong><strong>INSPECTION</strong></strong></td>
											<td width="60px" align="center" style="font-size:12px;"><strong>DATE</strong></td>
											<td width="60px" align="center" style="font-size:12px;"><strong>SIGN</strong></td>
											<td width="160px" colspan="3" align="center" style="font-size:12px;"><strong>COMMENTS / CORRECTIVE<br />ACTION</strong></td>
										</tr>';
									$pageCount++;
									$pagebreakCount = $pagebreakCount + $pagebreak;	
								}
							}else{
								if($j == $pagebreakCount){
									$content .= '</table>
									<div style="page-break-after: always;"></div>
									<table width="555" cellpadding="0" cellspacing="0" class="collapse">
										<tr>
											<td colspan="2" width="193px" align="center" style="font-size:12px;"><strong>'.wordwrap(strtoupper($locArrayData[$subLocids[$i]]), 25, '<br />', true).'</strong></td>
											<td width="60px" align="center" style="font-size:12px;"><strong><strong>INSPECTION</strong></strong></td>
											<td width="60px" align="center" style="font-size:12px;"><strong>DATE</strong></td>
											<td width="60px" align="center" style="font-size:12px;"><strong>SIGN</strong></td>
											<td width="160px" colspan="3" align="center" style="font-size:12px;"><strong>COMMENTS / CORRECTIVE<br />ACTION</strong></td>
										</tr>';
									$pageCount++;
									$pagebreakCount = $pagebreakCount + $pagebreak;
								}
							}
							$content .= '<tr>
								<td colspan="2" style="font-size:11px;height:30px;">'.wordwrap($subArray[$j][0], 60, '<br />', true).'</td>';
								if($subArray[$j][1] == 'NA'){
									$stt = 'N/A';
								}else{
									$stt = $subArray[$j][1];
								}
								$content .= '<td align="center" style="font-size:11px;">'.$stt.'</td>
								<td align="center" style="font-size:11px;">'.$subArray[$j][2].'</td>
								<td align="center" style="font-size:11px;">';
								if(isset($subArray[$j][3]) && $subArray[$j][3] != '' && $subArray[$j][1] == 'Yes'){
									$obj->resizeImages('../inspections/signoff/'.$subArray[$j][3], 100, 50, '../inspections/signoff/qa_signoff_report/'.$subArray[$j][3]);
									if(file_exists('../inspections/signoff/qa_signoff_report/'.$subArray[$j][3])){
										$content .= '<img src="../inspections/signoff/qa_signoff_report/'.$subArray[$j][3].'" style="height:50px;" />';
									}else{
										if (file_exists('../inspections/signoff/'.$subArray[$j][3])){
											$content .= '<img src="../inspections/signoff/'.$subArray[$j][3].'" style="height:50px;" />';
										}
									}
								}
								$content .= '</td>
								<td colspan="3" style="font-size:10px;">'.wordwrap($subArray[$j][4], 50, '<br />', true).'</td>
							</tr>';
						}
						$taskCount = $taskCount + $taskLupCount;
						$content .= '<tr>
							<td width="138px">&nbsp;</td>
							<td width="55px" align="center" style="font-size:14px;">Signed:</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td colspan="3">&nbsp;</td>
						</tr>
						<tr>
							<td style="font-size:14px;height:50px;">Spot Check - Foreman</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td colspan="3">&nbsp;</td>
						</tr>
						<tr>
							<td style="font-size:14px;height:50px;"">Spot Check - Foreman</td>
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
							<td colspan="2" style="height:55px;">&nbsp;</td>
							<td colspan="3" style="height:55px;">&nbsp;</td>
							<td colspan="3" style="height:55px;">&nbsp;</td>
						</tr>
					</table><div style="page-break-after: always;"></div>';
					}
				}
				$totalCount = $taskCount;
		$html = $topHeader.$header.$content;				
	}
#	echo $html ;die;
	$report = 'QA_Report_'.microtime().'.pdf';

	$fieSize = createPDF($html, $report, $owner_id);
	$fieSize = floor($fieSize/(1024));
	if ($fieSize > 1024){
		$fieSize = floor($fieSize/(1024)) . "Mbs";
	}else{
		$fieSize .= "Kbs";
	}
	$rply = $ajaxReplay.' '.$fieSize;
	
	
	echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="closePopUp();" href="./report_pdf/'.$owner_id.'/'.$report.'" target="_blank" class="view_btn"></a></div>';
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
