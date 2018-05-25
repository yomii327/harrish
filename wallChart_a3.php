<?php
session_start();

set_time_limit(50000000);
error_reporting(E_ALL);
ini_set('error_reporting', 1);

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif (isset($_SESSION['ww_is_company'])){
	$owner_id = "company";
}

require_once'includes/commanfunction.php';
$obj = new COMMAN_Class();
$owner_id = $_SESSION['ww_builder_id'];


$row = $obj->selQRY('pr_num_sublocations', 'user_projects','project_id = "'.$_REQUEST['projName'].'"');
$pr_num_sublocations = $row["pr_num_sublocations"];

$col = 10;//Next use to decide the per page count
$rows = 28;
if($pr_num_sublocations == 2){ $rows = 24; }
if($pr_num_sublocations == 3){ $rows = 22; }
if($pr_num_sublocations == 4){ $rows = 20; }
$where = '';
$wherePM= '';
$whereSubLocation = '';
$extraTable = '';
$groupBy = '';

$html_tds = array();

$parent_location = array();
$sub_location_position = array();
if(isset($_REQUEST['uniqueId'])){
	if(!empty($_REQUEST['location'])){
		$where .=" and pm.location_id = '".$_REQUEST['location']."'";
		$wherePM .=" and pm.location_id = '".$_REQUEST['location']."'";
	}
	if(!empty($_REQUEST['subLocation']) && empty($_REQUEST['subLocation_sub'])){
                $sublocations = $obj->subLocationsIdProgressMonitoring ($_REQUEST['subLocation'], ",");
                $where.= " and (pm.sub_location_id in (".$sublocations."))";
		$wherePM .= " and (pm.sub_location_id in (".$sublocations."))";
	}
	if (!empty ($_REQUEST['subLocation_sub']))
	{
                $where.= " and (pm.sub_location_id = ".$_REQUEST['subLocation_sub'].")";
		$wherePM .= " and (pm.sub_location_id = ".$_REQUEST['subLocation_sub'].")";
	}
	
	if(!empty($_REQUEST['issuedToPM'])){
		$where .=" and pmIssue.issued_to_name = '".$_REQUEST['issuedToPM']."'";
		$wherePM .=" and pmIssue.issued_to_name = '".$_REQUEST['issuedToPM']."' and pmIssue.progress_id = pm.progress_id";
		$extraTable .= " , issued_to_for_progress_monitoring as pmIssue";
		$groupBy = ' GROUP BY pm.location_id';
	}
	
	if(!empty($_REQUEST['status'])){
		$where .=" and pm.status = '".$_REQUEST['status']."'";
		$wherePM .=" and pm.status = '".$_REQUEST['status']."'";
		$groupBy = ' GROUP BY pm.location_id';
	}
	
	if(!empty($_REQUEST['DRF']) and !empty($_REQUEST['DRF'])){
		$where .=" and pm.start_date between '".date('Y-m-d', strtotime($_REQUEST['DRF']))."' and '".date('Y-m-d', strtotime($_REQUEST['DRT']))."'";
		$wherePM .=" and pm.start_date between '".date('Y-m-d', strtotime($_REQUEST['DRF']))."' and '".date('Y-m-d', strtotime($_REQUEST['DRT']))."'";
		$groupBy = ' GROUP BY pm.location_id';
	}
	
	if(!empty($_REQUEST['FBDF']) and !empty($_REQUEST['FBDT'])){
		$where .=" and pm.end_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
		$wherePM .=" and pm.end_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
		$groupBy = ' GROUP BY pm.location_id';
	}
	
	if(!empty($_REQUEST['projName'])){
//to get the top locations from the project
//--------------------------------------Location Array Creation Start Here ----------------------------------------//		
		$querySubLoc = array();
		$locIds = array();//Array for store the location for string

		$locData = $obj->selQRYMultiple('location_id, sub_location_id, location_tree', 'progress_monitoring as pm'.$extraTable, 'pm.is_deleted = 0 AND pm.project_id = "'.$_REQUEST['projName'].'" '.$where.$groupBy.' order by pm.location_id, pm.sub_location_id');
		foreach($locData as $lData){
			$locStrArray = explode(' > ', $lData['location_tree']);
			$temp['location_id'] = $locationId = $locStrArray[0];
			$locIds[$locStrArray[0]] = 1;
			for($g=1; $g<=$pr_num_sublocations; $g++){
				$temp['sub_location'.$g] = $locStrArray[$g];
				if (!empty($locStrArray[$g]))
					$locIds[$locStrArray[$g]] = 1;
			}
			$querySubLoc[] = $temp;
		}
//Create array as unique array
		$querySubLoc = array_map("unserialize", array_unique(array_map("serialize", $querySubLoc)));
//Reindex array
		$reIndexLoc = array_values($querySubLoc);

//To Get accual sub location position if multiple
		for($g=1; $g<=$pr_num_sublocations; $g++){
			$subloc_id = 'sub_location'.$g;
		}
		$subLocArray = array(); //Sublocation Array saprated
//Generate subloction id array
		foreach($querySubLoc as $subLoc){
			$subLocArray[] = $subLoc[$subloc_id];
		}

		$maxColumnPDF = count($querySubLoc);
		$colCount = $col;
		if($maxColumnPDF > $col){
			$pageCount = ceil($maxColumnPDF/$col);
			$colCount = $maxColumnPDF;
		}
		if ($maxColumnPDF > 0 && $pageCount==0)
		{
			$pageCount = 1;
		}
//Locatin id string fetch here
 		$locationIds = join(', ', array_keys($locIds));
//Location title Data 
		$locationArray = array();
		$queryLocTitle = $obj->selQRYMultiple('location_id, location_title', 'project_monitoring_locations', 'location_id IN ('.$locationIds.') and is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" order by location_id');
		foreach($queryLocTitle as $locTitle){
			$locationArray[$locTitle['location_id']] = $locTitle['location_title'];
		}
//---------------------------------Location Array Creation End Here ----------------------------------------//		
//--------------------------------------TaskData Array Creation Start Here ----------------------------------------//		
		$taskTitleArray = array();
		$taskData = $obj->selQRYMultiple('progress_id, task', 'progress_monitoring as pm'.$extraTable, 'pm.is_deleted = 0 AND pm.project_id = "'.$_REQUEST['projName'].'" '.$wherePM.' order by pm.progress_id');
		foreach($taskData as $tData){
			$taskTitleArray[$tData['progress_id']] = $tData['task'];
		}

		$queryTask = $obj->selQRYMultiple('Distinct task, progress_id, holding_point', 'progress_monitoring as pm'.$extraTable, 'pm.is_deleted = 0 AND pm.project_id = "'.$_REQUEST['projName'].'" '.$wherePM.' group by task order by pm.progress_id');

		$valueArray = array();///$valueArray[task name][location]
		foreach($queryTask as $tasks){
			$queryTaskData = $obj->selQRYMultiple('pm.progress_id, pm.location_id, pm.sub_location_id, pm.task, pm.start_date, pm.end_date, pm.percentage, pm.status', 'progress_monitoring as pm'.$extraTable, 'pm.task = "'. addslashes($tasks['task']).'" AND pm.is_deleted = 0 AND pm.project_id = "'.$_REQUEST['projName'].'" '.$wherePM.' order by pm.location_id, pm.sub_location_id');	
			foreach($queryTaskData as $qData){
				$valueArray[$tasks['task']][$qData['sub_location_id']] = array($qData['start_date'], $qData['end_date'], $qData['percentage'], $qData['status']);
			}
		}
		
		$taskIdsArr = array_keys($taskTitleArray);
//--------------------------------------TaskData Array End Here ----------------------------------------//	
		if(!empty($querySubLoc)){
			$i = 1;
			$pagecount = 0;
			$tot_loc_count = 1;
			$pos = 0;
			$sub_location_position[0] = array();
			$html_location[$pagecount] = '<tr><td style="width:290px">Status</td><td style="width:60px;padding:2px;">'.date('d/M/Y').'</td>';
			$html_sub_location[$pagecount] = '<tr><td style="width:290px"><strong>TASK</strong></td><td style="width:60px">';
			if ($pr_num_sublocations == 2){
				$html_sub_location1[$pagecount] = '<tr><td style="width:290px"><strong>TASK</strong></td><td style="width:60px">';
				$html_sub_location[$pagecount] = '<tr><td style="width:290px">&nbsp;</td><td style="width:60px">';
			}
			if ($pr_num_sublocations == 3){
				$html_sub_location2[$pagecount] = '<tr><td style="width:290px"><strong>TASK</strong></td><td style="width:60px">';
				$html_sub_location1[$pagecount] = '<tr><td style="width:290px">&nbsp;</td><td style="width:60px">';
				$html_sub_location[$pagecount] = '<tr><td style="width:290px">&nbsp;</td><td style="width:60px">';
			}
			if ($pr_num_sublocations == 4){
				$html_sub_location3[$pagecount] = '<tr><td style="width:290px"><strong>TASK</strong></td><td style="width:60px">';
				$html_sub_location2[$pagecount] = '<tr><td style="width:290px">&nbsp;</td><td style="width:60px">';
				$html_sub_location1[$pagecount] = '<tr><td style="width:290px">&nbsp;</td><td style="width:60px">';
				$html_sub_location[$pagecount] = '<tr><td style="width:290px">&nbsp;</td><td style="width:60px">';
			}
			foreach($querySubLoc as $subLoc){
				$tot_loc_count++;
				if (intval($i/$col) == ($i/$col)){
					$html_location[ $pagecount] .= '</tr>';
					$html_sub_location[ $pagecount] .= '</tr>';

					if ($pr_num_sublocations == 2){
						$html_sub_location1[ $pagecount] .= '</tr>';
					}
					if ($pr_num_sublocations == 3){
						$html_sub_location1[ $pagecount] .= '</tr>';
						$html_sub_location2[ $pagecount] .= '</tr>';
					}
					if ($pr_num_sublocations == 4){
						$html_sub_location1[ $pagecount] .= '</tr>';
						$html_sub_location2[ $pagecount] .= '</tr>';
						$html_sub_location3[ $pagecount] .= '</tr>';
					}

					$pagecount++;
					$sub_location_position[$pagecount] = array();
					$pos = 0;
					$html_location[$pagecount] = '<tr><td>STATUS</td><td>&nbsp;</td>';
					$html_sub_location[$pagecount] = '<tr><td>&nbsp;</td><td>&nbsp;</td>';
					if ($pr_num_sublocations == 1){
						$html_sub_location[$pagecount] = '<tr><td><strong>TASK</strong></td><td>&nbsp;</td>';
					}
					if ($pr_num_sublocations == 2){
						$html_sub_location1[ $pagecount] = '<tr><td><strong>TASK</strong></td><td>&nbsp;</td>';
					}
					if ($pr_num_sublocations == 3){
						$html_sub_location1[ $pagecount] = '<tr><td><strong>TASK</strong></td><td>&nbsp;</td>';
						$html_sub_location2[ $pagecount] = '<tr><td>&nbsp;</td><td>&nbsp;</td>';
					}
					if ($pr_num_sublocations == 4){
						$html_sub_location1[ $pagecount] = '<tr><td><strong>TASK</strong></td><td>&nbsp;</td>';
						$html_sub_location2[ $pagecount] = '<tr><td>&nbsp;</td><td>&nbsp;</td>';
						$html_sub_location3[ $pagecount] = '<tr><td>&nbsp;</td><td>&nbsp;</td>';
					}
				}
				for($g=1; $g<=$pr_num_sublocations; $g++){
					$subloc_id = $subLoc['sub_location'.$g];
				}
				$html_location[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$locationArray[$subLoc['location_id']].'</td>';
				$html_sub_location[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$locationArray[$subloc_id].'</td>';
				
				$sub_location_position[$pagecount][$pos] = $subLoc['sub_location1'];
				if ($pr_num_sublocations == 2){
					$html_sub_location1[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$locationArray[$subLoc['sub_location1']].'</td>';
					$sub_location_position[$pagecount][$pos] = $subLoc['sub_location2'];
				}
				if ($pr_num_sublocations == 3){
					$html_sub_location1[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$locationArray[$subLoc['sub_location1']].'</td>';
					$html_sub_location2[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$locationArray[$subLoc['sub_location2']].'</td>';
					$sub_location_position[$pagecount][$pos] = $subLoc['sub_location3'];
				}
				if ($pr_num_sublocations == 4){
					$html_sub_location1[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$locationArray[$subLoc['sub_location1']].'</td>';
					$html_sub_location2[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$locationArray[$subLoc['sub_location2']].'</td>';
					$html_sub_location3[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$locationArray[$subLoc['sub_location3']].'</td>';
					$sub_location_position[$pagecount][$pos] = $subLoc['sub_location4'];
				}
				$i++;
				$pos++;
			}
			$html_location[$pagecount] .= '</tr>';
			$html_sub_location[$pagecount] .= '</tr>';
			if ($pr_num_sublocations == 2){
				$html_sub_location1[ $pagecount] .= '</tr>';
			}
			if ($pr_num_sublocations == 3){
				$html_sub_location1[ $pagecount] .= '</tr>';
				$html_sub_location2[ $pagecount] .= '</tr>';
			}
			if ($pr_num_sublocations == 4){
				$html_sub_location1[ $pagecount] .= '</tr>';
				$html_sub_location2[ $pagecount] .= '</tr>';
				$html_sub_location3[ $pagecount] .= '</tr>';
			}
		}
	}
	echo '<style>@charset "utf-8";table.collapse { border-collapse: collapse; border: 1pt solid black; font-family:Arial Tahoma Sans-Serif;font-size:15.5px;} table.collapse td { border: 1pt solid black; }</style><div id="mainContainer" style="overflow-x:scroll;">
		<div class="buttonDiv">
		<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
		<img onClick="downloadPDFPM();"src="images/download_btn.png" style="float:right;" />
	</div><br clear="all" /><table style="width:2340;margin-bottom:20px;" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="28" align="center">
			<h3>Wall Chart Report</h3>
		</td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
		<td align="left">Behind</td>
		<td style="width:50px;background-color:#FF0000;">&nbsp;</td>
		<td>&nbsp;</td>
		<td align="left">In progress</td>
		<td style="width:50px;background-color:#FFA500">&nbsp;</td>
		<td>&nbsp;</td>
		<td align="left">Complete</td>
		<td style="width:50px;background-color:#008000">&nbsp;</td>
		<td>&nbsp;</td>
		<td align="left">Signed Off</td>
		<td style="width:50px;background-color:#0000FF">&nbsp;</td>
		<td>&nbsp;</td>
		<td colspan="12">&nbsp;</td>
	</tr>
</table>';
	
	/// ITERATE ALL TASKS for ALL LOCATION PAGES
	$taskHtml = array();
	$pagecount = 1;
	for ($i=0; $i<$pageCount; $i++)
	{
		if ($i>0)
		{
			echo "<br/>";
		}
		echo '<table width="842px" border="1" class="collapse" cellspacing="0" cellpadding="0">';
		echo $html_location[ $i];
		if ($pr_num_sublocations == 2){
			echo $html_sub_location1[ $i];
		}
		if ($pr_num_sublocations == 3){
			echo $html_sub_location1[ $i];
			echo $html_sub_location2[ $i];
		}
		if ($pr_num_sublocations == 4){
			echo $html_sub_location1[ $i];
			echo $html_sub_location2[ $i];
			echo $html_sub_location3[ $i];
		}
		echo $html_sub_location[ $i];
		foreach($queryTask as $tasks){
			$task = $tasks["task"];
			if (empty($task))
			{
				continue;
			}
			$holding_point = $tasks["holding_point"];
			$holding_style = "";
			$holding_style_s = "";
			$holding_style_e = "";
			if ($holding_point == "Yes" || $holding_point == "yes" || $holding_point == "YES")
			{
				$holding_style = "background:#bebebe;";
				$holding_style_s = "<strong><i>";
				$holding_style_e = "</strong></i>";
			}
			$taskHtml = "<tr style='".$holding_style."'>";
			$taskHtml .= '<td style="height:52px;padding-left:2px;"><div >'.$holding_style_s.$tasks['task'].$holding_style_e.'</div>';
			$tArray[] = $tasks['task'];
			$queryIssuedToData = $obj->selQRYMultiple('issued_to_name', 'issued_to_for_progress_monitoring', 'progress_id = "'.$tasks['progress_id'].'" AND is_deleted = 0 order by issued_to_name');
			$issue_to_name = "";
			foreach($queryIssuedToData as $issue_to){
				if ($issue_to_name=="")
					$issue_to_name = $issue_to["issued_to_name"];
				else
					$issue_to_name .= ", ".$issue_to["issued_to_name"];
			}
			$taskHtml .= '<div>'.$holding_style_s.$issue_to_name.$holding_style_e.'</div>';
			$taskHtml .= "</td><td style='width:60px;'><div style='border-bottom:1px solid black;padding:2px;'>".$holding_style_s."Start".$holding_style_e. "</div><div style='border-bottom:1px solid black;padding:2px;'>" . $holding_style_s . "Finish" . $holding_style_e . "</div><div style='padding:2px;'>" . $holding_style_s . "Status" . $holding_style_e . "</div></td>";

			for ($j=0; $j < count($sub_location_position[$i]); $j++)
			{
				$value = $valueArray[$task][$sub_location_position[$i][$j]];
				if (empty($value))
				{
					$taskHtml .= "<td style='height:52px;width:100px'>&nbsp;</td>";
					continue;
				}
				$taskHtml .= "<td style='height:52px;width:100px'> " . $holding_style_s . "
					<div style='border-bottom:1px solid black;padding-left:2px;'>".date('d-M-Y', strtotime($value[0]))."</div>
					<div style='border-bottom:1px solid black;padding-left:2px;'>".date('d-M-Y', strtotime($value[1]))."</div>
					<div style='text-align:center;";
					if($value[3] == 'In progress'){
						$taskHtml .= "background-color:#FFA500;'";
					}
					if($value[3] == 'Behind'){
						$taskHtml .= "background-color:#FF0000;'";
					}
					if($value[3] == 'Complete'){
						$taskHtml .= "background-color:#008000;'";
					}
					if($value[3] == 'Signed off'){
						$taskHtml .= "background-color:#0000FF;'";
					}
				$taskHtml .= "'>".$value[2]. $holding_style_e . "</div></td>";
			}
			$taskHtml .= "</tr>";
			echo $taskHtml;
		}
		echo "</table>";
	}
	echo "</div>";
	die;
	{
#echo '<pre>';echo 'html_sub_location1';print_r($html_sub_location1);echo 'html_sub_location2';print_r($html_sub_location2);echo 'html_sub_location3';print_r($html_sub_location3);

		$queryTask = $obj->selQRYMultiple('DISTINCT task', 'progress_monitoring as pm'.$extraTable, 'pm.is_deleted = 0 AND pm.project_id = "'.$_REQUEST['projName'].'" '.$wherePM.' order by pm.progress_id');
		$vpagecount = 0;
		$k = 0;
$statTaskArray = array();$i9 = 0;$j9 = 0;
		foreach($queryTask as $tasks){
			$queryTaskData = $obj->selQRYMultiple('pm.sub_location_id, pm.progress_id, pm.task, pm.start_date, pm.end_date, pm.percentage, pm.status', 'progress_monitoring as pm'.$extraTable, 'pm.task = "'.$tasks['task'].'" AND pm.is_deleted = 0 AND pm.project_id = "'.$_REQUEST['projName'].'" '.$wherePM.' order by pm.location_id, pm.sub_location_id');
			$i = 1;
			$hpagecount = 0;
			$k++;
			$vpagecount = intval($k / $rows);
			
			if (!isset($html_tds [$vpagecount])){
				$html_tds [$vpagecount] = array();
				$statTaskArray[] = $tArray;
				$tArray = array();
			}
			
			if (!isset($html_tds [$vpagecount][$hpagecount])){
				$html_tds [$vpagecount][$hpagecount] = "";
			}
//Issue to for progress report
			$issue_to_name = "";
			$issue_flag = true;
//Issue to for progress report
			$html_tds[$vpagecount] [$hpagecount] .= '<tr>';
			
			foreach($queryTaskData as $taskData){
				if ($issue_flag){
					$html_tds[$vpagecount] [$hpagecount] .= '<td style="height:52px;padding-left:2px;"><div >'.$tasks['task'].'</div>';
					$tArray[] = $tasks['task'];
					$queryIssuedToData = $obj->selQRYMultiple('issued_to_name', 'issued_to_for_progress_monitoring', 'progress_id = "'.$taskData['progress_id'].'" AND is_deleted = 0 order by issued_to_name');
					$issue_to_name = "";
					foreach($queryIssuedToData as $issue_to){
						if ($issue_to_name=="")
							$issue_to_name = $issue_to["issued_to_name"];
						else
							$issue_to_name .= ", ".$issue_to["issued_to_name"];
					}
					$html_tds[$vpagecount] [$hpagecount] .= '<div>'.$issue_to_name.'</div>';
					$html_tds[$vpagecount] [$hpagecount] .= "</td><td style='width:60px;'><div style='border-bottom:1px solid black;padding:2px;'>Start</div><div style='border-bottom:1px solid black;padding:2px;'>Finish</div><div style='padding:2px;'>Status</div></td>";
					$issue_flag = false;
				}

				$postion = $sub_location_position[$taskData['sub_location_id']];
				
				if($postion != $i){
					$tmp = $i;
					for ($j=$tmp; $j<$postion;$j++){
						if (intval($i/$col)==($i/$col)){
							$html_tds [$vpagecount] [$hpagecount] .= "</tr>";
							$hpagecount++;
							if (!isset($html_tds [$vpagecount] [$hpagecount])){
								$html_tds [$vpagecount] [$hpagecount] = "";
							}
							$html_tds [$vpagecount] [$hpagecount] .= "<tr><td>".$tArray[$i9]."</td>";
						}
						
						$html_tds [$vpagecount] [$hpagecount] .= "<td style='height:52px;width:100px'><div>&nbsp;</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
						$i++;
					}
				}
				
				if (intval($i/$col)==($i/$col)){
					$html_tds [$vpagecount] [$hpagecount] .= "</tr>";
					$hpagecount++;
					if (!isset($html_tds [$vpagecount] [$hpagecount])){
						$html_tds [$vpagecount] [$hpagecount] = "";
					}
					$html_tds [$vpagecount] [$hpagecount] .= "<tr><td>".$tArray[$i9]."</td>";
				}
				
				$html_tds [$vpagecount] [$hpagecount] .= "<td style='height:52px;width:100px'>
					<div style='border-bottom:1px solid black;padding-left:2px;'>".date('d-M-Y', strtotime($taskData['start_date']))."</div>
					<div style='border-bottom:1px solid black;padding-left:2px;'>".date('d-M-Y', strtotime($taskData['end_date']))."</div>
					<div style='text-align:center;";
					if($taskData['status'] == 'In progress'){
						$html_tds[$vpagecount] [$hpagecount] .= "background-color:#FFA500;'";
					}
					if($taskData['status'] == 'Behind'){
						$html_tds[$vpagecount] [$hpagecount] .= "background-color:#FF0000;'";
					}
					if($taskData['status'] == 'Complete'){
						$html_tds[$vpagecount] [$hpagecount] .= "background-color:#008000;'";
					}
					if($taskData['status'] == 'Signed off'){
						$html_tds[$vpagecount] [$hpagecount] .= "background-color:#0000FF;'";
					}
				$html_tds [$vpagecount] [$hpagecount] .= "'>".$taskData['percentage']."</div></td>";
				$i++;
				$l++;
			}
			//if last locations are not in the task, then add empty tds
			if ($i!=$tot_loc_count){
				$tmp = $i;
				for ($j=$tmp; $j<$tot_loc_count;$j++){
					if (intval($i/$col)==($i/$col)){
						$html_tds [$vpagecount] [$hpagecount] .= "</tr>";
						$hpagecount++;
						if (!isset($html_tds [$vpagecount] [$hpagecount])){
							$html_tds [$vpagecount] [$hpagecount] = "";
						}
						$html_tds [$vpagecount] [$hpagecount] .= "<tr><td>".$tArray[$i9]."</td>";
					}
					$html_tds [$vpagecount] [$hpagecount] .= "<td style='height:52px;width:100px'><div>&nbsp;</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
					$i++;
				}
				$html_tds [$vpagecount] [$hpagecount] .= "</tr>";
			}
		}
	}
}
$headerHtml = '<table style="width:2340;margin-bottom:20px;" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="28" align="center">
			<h3>Wall Chart Report</h3>
		</td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
		<td align="left">Behind</td>
		<td style="width:50px;background-color:#FF0000;">&nbsp;</td>
		<td>&nbsp;</td>
		<td align="left">In progress</td>
		<td style="width:50px;background-color:#FFA500">&nbsp;</td>
		<td>&nbsp;</td>
		<td align="left">Complete</td>
		<td style="width:50px;background-color:#008000">&nbsp;</td>
		<td>&nbsp;</td>
		<td align="left">Signed Off</td>
		<td style="width:50px;background-color:#0000FF">&nbsp;</td>
		<td>&nbsp;</td>
		<td colspan="12">&nbsp;</td>
	</tr>
</table>';
$footerHtml = '';

$html = "";
$htmlOutput = "";
$flag = true;
$page = 1;
$zipFileArray = array();

foreach($html_tds as $html_h){
    for ($i=0;$i<count($html_h);$i++){
		if($i != count($html_h)-1){
			$tableWidth = 'style="width:2340"';
		}else{
			$tableWidth = '';
		}
		if($page==1){	$html .= $headerHtml;	}else{	$html .= '<div style="height:70px;"></div>'; }
		$html .= '<style>@charset "utf-8";table.collapse { border-collapse: collapse; border: 1pt solid black; font-family:Arial Tahoma Sans-Serif;font-size:15.5px;} table.collapse td { border: 1pt solid black; }</style><table '.$tableWidth.' border="1" class="collapse" cellspacing="0" cellpadding="0">';
        if ($flag){
            $html .= $html_location[$i];
			if ($pr_num_sublocations == "2"){
				$html .= $html_sub_location1[$i];
			}
			if ($pr_num_sublocations == "3"){
				$html .= $html_sub_location1[$i];
				$html .= $html_sub_location2[$i];
			}
			if ($pr_num_sublocations == "4"){
				$html .= $html_sub_location1[$i];
				$html .= $html_sub_location2[$i];
				$html .= $html_sub_location3[$i];
			}
            $html .= $html_sub_location[$i];
        }
        $html .= $html_h[$i];
        $html .= '</table>';
		$htmlOutput .= $html;
#		createPDF($html, 'wallChart_part'.$page.'.pdf', $owner_id);
#		$zipFileArray[] = 'report_pdf/'.$owner_id.'/wallChart_part'.$page.'.pdf';
		$page++;
		$html = "";
    }
    $flag = false;
}?>
<div id="mainContainer" style="overflow-x:scroll;">
	<div class="buttonDiv">
		<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
		<img onClick="downloadPDFPM();"src="images/download_btn.png" style="float:right;" />
	</div><br clear="all" />
<?php echo $htmlOutput; ?></div>