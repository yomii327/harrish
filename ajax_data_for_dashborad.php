<?php session_start();
$builder_id = $_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
require_once('includes/class.phpmailer.php');
$obj = new COMMAN_Class(); 

if(isset($_REQUEST["antiqueID"])){
	$con = "";
	$depthLocIds = "";
	$whereConUserRole = "";
	$locID = isset($_POST['locID']) ? $_POST['locID'] : "";
	$depthLocIds = $obj->subLocationsId($_POST['locID'], ",");

	if(!empty($_SESSION['projUserRole'])){
		if($_SESSION['projUserRole'][$_POST['projectID']] != 'All Defect')
			$whereConUserRole = " AND inspection_raised_by = '".$_SESSION['projUserRole'][$_POST['projectID']]."'";
	}
	
	if($depthLocIds != "")
		$con = " AND pi.location_id IN (".$depthLocIds.")";

	$inspStatusData = $obj->getRecordByQuery('SELECT SUM(IF(d.inspection_status= "Open", 1, 0)) AS open, SUM(IF(d.inspection_status="Pending", 1, 0)) AS pending, SUM(IF(d.inspection_status="Fixed", 1, 0)) AS fixed, SUM(IF(d.inspection_status="Closed", 1, 0)) AS closed FROM  project_inspections AS pi inner join (SELECT inspection_status, inspection_id FROM issued_to_for_inspections WHERE project_id = '.$_POST['projectID'].' AND is_deleted = 0 GROUP BY inspection_id  ) AS d ON pi.inspection_id = d.inspection_id WHERE pi.project_id = '.$_POST['projectID'].' AND pi.inspection_type != "Memo" AND pi.is_deleted = 0' . $con . $whereConUserRole);
	
	$open = isset($inspStatusData[0]['open']) ? $inspStatusData[0]['open'] : 0;
	$pending = isset($inspStatusData[0]['pending']) ? $inspStatusData[0]['pending'] : 0;
	$fixed = isset($inspStatusData[0]['fixed']) ? $inspStatusData[0]['fixed'] : 0;
	$closed = isset($inspStatusData[0]['closed']) ? $inspStatusData[0]['closed'] : 0;
	$total = $open + $pending + $fixed + $closed;
	$htmlData = '<tr class="oddDash">
			<th width="30%">Status</th>
			<th width="30%">Value</th>
		</tr>
		<tr class="evenDash" onClick="inspectionsList(\'Open\', \''.$locID.'\');" >
			<td class="clickable">Open</td>
			<td class="clickable">'.$open.'</td>
		</tr>
		<tr class="oddDash" onClick="inspectionsList(\'Pending\', \''.$locID.'\');">
			<td class="clickable">Pending</td>
			<td class="clickable">'.$pending.'</td>
		</tr>
		<tr class="evenDash" onClick="inspectionsList(\'Fixed\', \''.$locID.'\');">
			<td class="clickable">Fixed</td>
			<td class="clickable">'.$fixed.'</td>
		</tr>
		<tr class="oddDash" onClick="inspectionsList(\'Closed\', \''.$locID.'\');">
			<td class="clickable">Closed</td>
			<td class="clickable">'.$closed.'</td>
		</tr>
		<tr class="evenDash">
			<td><strong>Total</strong></td>
			<td><strong>'.$total.'</strong></td>
		</tr>';

	$outputArr = array('status'=> false, 'msg'=> 'No Result Found');

	if(!empty($inspStatusData))
		$outputArr = array('status'=> true, 'msg'=> 'No Result Found', 'htmlData'=> $htmlData);

	echo json_encode($outputArr);
}

if(isset($_REQUEST["uniqueID"])){
/*	$whereConUserRole = "";
	if(!empty($_SESSION['projUserRole'])){
		if($_SESSION['projUserRole'][$projID] != 'All Defect')
			$whereConUserRole = " AND inspection_raised_by = '".$_SESSION['projUserRole'][$projID]."'";
	}*/
	
	$inspStatusData = $obj->getRecordByQuery('SELECT SUM(IF(d.inspection_status= "Open", 1, 0)) AS open, SUM(IF(d.inspection_status="Pending", 1, 0)) AS pending, SUM(IF(d.inspection_status="Fixed", 1, 0)) AS fixed, SUM(IF(d.inspection_status="Closed", 1, 0)) AS closed FROM  project_inspections AS pi inner join (SELECT inspection_status, inspection_id FROM issued_to_for_inspections WHERE project_id IN ('.$_POST['projectID'].') AND is_deleted = 0 AND issued_to_name = "'.$_POST['issuedTo'].'" GROUP BY inspection_id  ) AS d ON pi.inspection_id = d.inspection_id WHERE pi.project_id IN ('.$_POST['projectID'].') AND pi.inspection_type != "Memo" AND pi.is_deleted = 0');
		
	$open = isset($inspStatusData[0]['open']) ? $inspStatusData[0]['open'] : 0;
	$pending = isset($inspStatusData[0]['pending']) ? $inspStatusData[0]['pending'] : 0;
	$fixed = isset($inspStatusData[0]['fixed']) ? $inspStatusData[0]['fixed'] : 0;
	$closed = isset($inspStatusData[0]['closed']) ? $inspStatusData[0]['closed'] : 0;
	$total = $open + $pending + $fixed + $closed;
	$htmlData = '<tr class="oddDash">
			<th width="30%">Status</th>
			<th width="30%">Value</th>
		</tr>
		<tr class="evenDash" onClick="inspectionsList(\'Open\', \''.$_POST['projectID'].'\');" >
			<td class="clickable">Open</td>
			<td class="clickable">'.$open.'</td>
		</tr>
		<tr class="oddDash" onClick="inspectionsList(\'Pending\', \''.$_POST['projectID'].'\');">
			<td class="clickable">Pending</td>
			<td class="clickable">'.$pending.'</td>
		</tr>
		<tr class="evenDash" onClick="inspectionsList(\'Fixed\', \''.$_POST['projectID'].'\');">
			<td class="clickable">Fixed</td>
			<td class="clickable">'.$fixed.'</td>
		</tr>
		<tr class="oddDash" onClick="inspectionsList(\'Closed\', \''.$_POST['projectID'].'\');">
			<td class="clickable">Closed</td>
			<td class="clickable">'.$closed.'</td>
		</tr>
		<tr class="evenDash">
			<td><strong>Total</strong></td>
			<td><strong>'.$total.'</strong></td>
		</tr>';

	$outputArr = array('status'=> false, 'msg'=> 'No Result Found');

	if(!empty($inspStatusData))
		$outputArr = array('status'=> true, 'msg'=> 'No Result Found', 'htmlData'=> $htmlData);

	echo json_encode($outputArr);
}

if(isset($_REQUEST["singleID"])){
	
	$inspStatusData = $obj->getRecordByQuery('SELECT SUM( IF( status = "In progress", 1, 0 ) ) AS ahead,SUM( IF( status = "Behind", 1, 0 ) ) AS behind, SUM( IF( status ="On Time", 1, 0 ) ) AS ontime, SUM( IF( status = "Complete", 1, 0 ) ) AS complete, SUM( IF( status = "", 1, 0 ) ) AS nostatus, SUM( IF( status = "Signed off", 1, 0 ) ) AS signedoff FROM `progress_monitoring` WHERE project_id = '.$_POST['projID'].' AND is_deleted = 0 AND location_id = '.$_POST['locID']);

		$behind = isset($inspStatusData[0]['behind']) ? $inspStatusData[0]['behind'] : 0;
		$ahead = isset($inspStatusData[0]['ahead']) ? $inspStatusData[0]['ahead'] : 0;
		$ontime = isset($inspStatusData[0]['ontime']) ? $inspStatusData[0]['ontime'] : 0;
		$complete = isset($inspStatusData[0]['complete']) ? $inspStatusData[0]['complete'] : 0;
		$signedoff = isset($inspStatusData[0]['signedoff']) ? $inspStatusData[0]['signedoff'] : 0;
		$nostatus = isset($inspStatusData[0]['nostatus']) ? $inspStatusData[0]['nostatus'] : 0;
		$total = $behind + $ahead + $ontime + $complete + $signedoff + $nostatus;
	$htmlData = '<tr class="oddDash">
			<th width="30%">Status</th>
			<th width="30%">Value</th>
		</tr>
		<tr class="evenDash" onClick="inspectionsList(\'Behind\');">
			<td class="clickable">Behind</td>
			<td class="clickable">'.$behind.'</td>
		</tr>
		<tr class="oddDash" onClick="inspectionsList(\'Complete\');">
			<td class="clickable">Complete</td>
			<td class="clickable">'.$complete.'</td>
		</tr>
		<tr class="evenDash" onClick="inspectionsList(\'In progress\');">
			<td class="clickable">In progress</td>
			<td class="clickable">'.$ahead.'</td>
		</tr>
		<tr class="oddDash" onClick="inspectionsList(\'Signed off\');">
			<td class="clickable"><a onClick="inspectionsList(\'Signed off\');" href="javascript:void(0);">Signed off</td>
			<td class="clickable"><a onClick="inspectionsList(\'Signed off\');" href="javascript:void(0);">'.$signedoff.'</td>
		</tr>
		<tr class="evenDash" onClick="inspectionsList(\'Not Started\');">
			<td class="clickable">Not Started</td>
			<td class="clickable">'.$nostatus.'</td>
		</tr>
		<tr class="oddDash">
			<td><strong>Total</strong></td>
			<td><strong>'.$total.'</strong></td>
		</tr>';

	$outputArr = array('status'=> false, 'msg'=> 'No Result Found');

	if(!empty($inspStatusData))
		$outputArr = array('status'=> true, 'msg'=> 'No Result Found', 'htmlData'=> $htmlData);

	echo json_encode($outputArr);
}?>