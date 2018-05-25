<?php
session_start();

set_time_limit(50000000);
error_reporting(0);

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif (isset($_SESSION['ww_is_company'])){
	$owner_id = "company";
}

$col = 26;
$rows = 57;
$where = '';
$wherePM= '';
$whereSubLocation = '';
$extraTable = '';
$groupBy = '';

$html_tds = array();

require_once'includes/commanfunction.php';
$obj = new COMMAN_Class();
$owner_id = $_SESSION['ww_builder_id'];

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
		$row = $obj->selQRY('pr_num_sublocations', 'user_projects','project_id = "'.$_REQUEST['projName'].'"');
		$pr_num_sublocations = $row["pr_num_sublocations"];
		$querySubLoc = $obj->selQRYMultiple('location_id, sub_location_id', 'progress_monitoring as pm'.$extraTable, 'pm.is_deleted = 0 AND pm.project_id = "'.$_REQUEST['projName'].'" '.$where.$groupBy.' order by pm.location_id, pm.sub_location_id');
		$querySubLoc = array_map("unserialize", array_unique(array_map("serialize", $querySubLoc)));
		$locationIds = ''; $subLocationIds = '';
		foreach($querySubLoc as $loca){
			if($locationIds == ''){
				$locationIds .= $loca['location_id'];
			}else{
				$locationIds .= ', '.$loca['location_id'];
			}
			if($subLocationIds == ''){
				$subLocationIds .= $loca['sub_location_id'];
			}else{
				$subLocationIds .= ', '.$loca['sub_location_id'];
			}
		}
		
		$queryLocTitle = $obj->selQRYMultiple('location_id, location_title', 'project_monitoring_locations', 'location_id IN ('.$locationIds.') and is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" order by location_id');
		foreach($queryLocTitle as $locTitle){
			$locationArray[$locTitle['location_id']] = $locTitle['location_title'];
		}
		$subLocationParentArray = array();
		$querySubLocTitle = $obj->selQRYMultiple('location_id, location_title, location_parent_id', 'project_monitoring_locations', 'location_id IN ('.$subLocationIds.') and is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" order by location_id');
		foreach($querySubLocTitle as $subLocTitle){
			$subLocationArray[$subLocTitle['location_id']] = $subLocTitle['location_title'];
			$subLocationParentArray[$subLocTitle['location_id']] = $subLocTitle['location_parent_id'];
		}
		$querySubLocTitle = $obj->selQRYMultiple('location_id, location_title', 'project_monitoring_locations', 'location_id IN ('.$subLocationIds.') and is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" order by location_id');
		foreach($querySubLocTitle as $subLocTitle){
			$subLocationArray[$subLocTitle['location_id']] = $subLocTitle['location_title'];
		}
		if ($pr_num_sublocations == 2)
		{
			foreach($subLocationParentArray as $key=>$value){
				if($subLocationIds == ''){
					$subLocationIds .= $value;
				}else{
					$subLocationIds .= ', '.$value;
				}
			}
			$querySubSubLocTitle = $obj->selQRYMultiple('location_id, location_title', 'project_monitoring_locations', 'location_id IN ('.$subLocationIds.') and is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" order by location_id');
			foreach($querySubSubLocTitle as $subLocTitle){
				$subsubLocationArray[$subLocTitle['location_id']] = $subLocTitle['location_title'];
			}
		}
		if(!empty($querySubLoc)){
			$i = 1;
			$pagecount = 0;
                        $tot_loc_count = 1;
			$html_location[$pagecount] = '<tr><td style="width:290px">Status</td><td style="width:60px;padding:2px;">'.date('d/M/Y').'</td>';
			$html_sub_location[$pagecount] = '<tr><td style="width:290px"><strong>TASK</strong></td><td style="width:60px">';
			if ($pr_num_sublocations == "2")
			{
				$html_sub_sub_location[$pagecount] = '<tr><td style="width:290px"><strong>TASK</strong></td><td style="width:60px">';
        			$html_sub_location[$pagecount] = '<tr><td style="width:290px">&nbsp;</td><td style="width:60px">';
			}
			foreach($querySubLoc as $subLoc){
                                $tot_loc_count++;
                                if (intval($i/$col) == ($i/$col))
				{
					$html_location[ $pagecount] .= '</tr>';
					$html_sub_location[ $pagecount] .= '</tr>';
					$pagecount++;
					$html_location[$pagecount] = '<tr>';
					$html_sub_location[$pagecount] = '<tr>';
					//$i = 1;
				}
				$subloc_id = $subLoc['sub_location_id'];
				$html_location[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$locationArray[$subLoc['location_id']].'</td>';
				$html_sub_location[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$subLocationArray[$subloc_id].'</td>';
				if ($pr_num_sublocations == "2")
				{
					$html_sub_sub_location[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$subsubLocationArray[$subLocationParentArray[$subloc_id]].'</td>';
				}
				//to get the position of the sub location in row
				$sub_location_position[$subLoc['sub_location_id']] = $i;
				$i++;
			}
		
			//if (intval($i/$col) != ($i/$col)){
                        $html_location[$pagecount] .= '</tr>';
                        $html_sub_location[$pagecount] .= '</tr>';
                        $html_sub_sub_location[$pagecount] .= '</tr>';
			//}
		}
		
		$queryTask = $obj->selQRYMultiple('DISTINCT task', 'progress_monitoring as pm'.$extraTable, 'pm.is_deleted = 0 AND pm.project_id = "'.$_REQUEST['projName'].'" '.$wherePM.' order by pm.progress_id');
		$vpagecount = 0;
		$k = 0;
	
		foreach($queryTask as $tasks){
			$queryTaskData = $obj->selQRYMultiple('pm.sub_location_id, pm.progress_id, pm.task, pm.start_date, pm.end_date, pm.percentage, pm.status', 'progress_monitoring as pm'.$extraTable, 'pm.task = "'.$tasks['task'].'" AND pm.is_deleted = 0 AND pm.project_id = "'.$_REQUEST['projName'].'" '.$wherePM.' order by pm.location_id, pm.sub_location_id');
			$i = 1;
			$hpagecount = 0;
			$k++;
			$vpagecount = intval($k / $rows);
			if (!isset($html_tds [$vpagecount]))
			{
				$html_tds [$vpagecount] = array();
			}
			if (!isset($html_tds [$vpagecount][$hpagecount]))
			{
				$html_tds [$vpagecount][$hpagecount] = "";
			}
//Issue to for progress report
			$issue_to_name = "";
			$issue_flag = true;
//Issue to for progress report
			$html_tds[$vpagecount] [$hpagecount] .= '<tr>';
                        //print_r($queryTaskData);
                        //die;
			foreach($queryTaskData as $taskData){
				if ($issue_flag){
					$html_tds[$vpagecount] [$hpagecount] .= '<td style="height:52px;padding-left:2px;"><div >'.$tasks['task'].'</div>';
					$queryIssuedToData = $obj->selQRYMultiple('issued_to_name', 'issued_to_for_progress_monitoring', 'progress_id = "'.$taskData['progress_id'].'" AND is_deleted = 0 order by issued_to_name');
					#print_r($queryIssuedToData);
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
                                                if (intval($i/$col)==($i/$col))
                                                {
                                                        $html_tds [$vpagecount] [$hpagecount] .= "</tr>";
                                                        $hpagecount++;
                                                        if (!isset($html_tds [$vpagecount] [$hpagecount]))
                                                        {
                                                                $html_tds [$vpagecount] [$hpagecount] = "";
                                                        }
                                                        $html_tds [$vpagecount] [$hpagecount] .= "<tr>";
                                                }
						$html_tds [$vpagecount] [$hpagecount] .= "<td style='height:52px;width:100px'><div>&nbsp;</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
						$i++;
					}
				}
				if (intval($i/$col)==($i/$col))
				{
					$html_tds [$vpagecount] [$hpagecount] .= "</tr>";
					$hpagecount++;
					if (!isset($html_tds [$vpagecount] [$hpagecount]))
					{
						$html_tds [$vpagecount] [$hpagecount] = "";
					}
					$html_tds [$vpagecount] [$hpagecount] .= "<tr>";
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
			}
			
			//if last locations are not in the task, then add empty tds
			if ($i!=$tot_loc_count){
                                $tmp = $i;
                                for ($j=$tmp; $j<$tot_loc_count;$j++){
                                        if (intval($i/$col)==($i/$col))
                                        {
                                                $html_tds [$vpagecount] [$hpagecount] .= "</tr>";
                                                $hpagecount++;
                                                if (!isset($html_tds [$vpagecount] [$hpagecount]))
                                                {
                                                        $html_tds [$vpagecount] [$hpagecount] = "";
                                                }
                                                $html_tds [$vpagecount] [$hpagecount] .= "<tr>";
                                        }
                                        $html_tds [$vpagecount] [$hpagecount] .= "<td style='height:52px;width:100px'><div>&nbsp;</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
                                        $i++;
                                }
                                $html_tds [$vpagecount] [$hpagecount] .= "</tr>";

				/*if ($hpagecount != $pagecount){
					for($j=$i; $j<$col*intval($i/$col); $j++){
						$html_tds [$vpagecount] [$hpagecount] .= "<td style='height:71px;width:100px;border-bottom:1px solid black'><div>&nbsp;</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
					}
					$html_tds [$vpagecount] [$hpagecount] .= '</tr>';
					for ($hpagecount=$hpagecount+1;$hpagecount<=$pagecount;$hpagecount++)
					{
						$html_tds [$vpagecount] [$hpagecount] .= '<tr>';
						for($j=0; $j<($col-1);$j++){
							$html_tds[$vpagecount][$hpagecount] .= "<td style='height:71px;width:100px;border-bottom:1px solid black'><div>&nbsp;</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
						}
						$html_tds [$vpagecount] [$hpagecount] .= '</tr>';
					}
				}else{
                                        $col_page = ($tot_loc_count - (intval($tot_loc_count/($col-1))*($col-1)));
                                        //$html_tds [$vpagecount] [$hpagecount] .= "($tot_loc_count - (intval($tot_loc_count/$col)*$col))p";
                                        //$html_tds [$vpagecount] [$hpagecount] .= "$col_page";
					for($j=($i - intval($i/$col)*$col)-1; $j<$col_page; $j++){
						$html_tds [$vpagecount] [$hpagecount] .= "<td style='height:52px;width:100px;border-bottom:1px solid black'><div>&nbsp;</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
					}
        				$html_tds [$vpagecount] [$hpagecount] .= '</tr>';
				}*/
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
		if ($pr_num_sublocations == "2")
		{
			$html .= $html_sub_sub_location[$i];
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
<?php /*$zipName = 'wall_chart_pdf'.microtime().'.zip';

if($obj->create_zip($zipFileArray, 'report_pdf/'.$owner_id.'/'.$zipName)){
	echo '<br clear="all" /><div style="margin-left:10px;">Wall Chart Report Generated <a href="report_pdf/'.$owner_id.'/'.$zipName.'" target="_blank" class="download_btn">&nbsp;</a></div>';
}else{
	echo '<br clear="all" /><div style="margin-left:10px;">Wall Chart Report Generation Fail Try Gain Later</div>';
}

function createPDF($html, $report, $owner_id){
	require_once("dompdf/dompdf_config.inc.php");
	$paper='a0';
	$orientation='portrait';
	
	if ( get_magic_quotes_gpc() )
	$html = stripslashes($html);
	
	$old_limit = ini_set("memory_limit", "94G");
	ini_set('max_execution_time', 3600); //300 seconds = 5 minutes
	
	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->set_paper($paper, $orientation);
	$dompdf->render();

	$output = $dompdf->output($report);
	
	$d = 'report_pdf/'.$owner_id;
	if(!is_dir($d))
		mkdir($d);
	if (file_exists($d.'/'.$report))
		unlink($d.'/'.$report);
	unlink($report);
	$tempFile = $d.'/'.$report;
	$fh = fopen($tempFile, 'w') or die("can't open file");
	$stringData = $output;
	fwrite($fh, $stringData);
	fclose($fh);
	
	return filesize($tempFile);
}
*/?>