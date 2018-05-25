<?php
session_start();
error_reporting(0);
set_time_limit(0);

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif (isset($_SESSION['ww_is_company'])){
	$owner_id = "company";
}

$col = 26;
$rows = 60;

$html_tds = array();

require_once'../includes/commanfunction.php';
$obj = new COMMAN_Class();
$owner_id = $_SESSION['ww_builder_id'];

$parent_location = array();
$sub_location_position = array();
if(isset($_REQUEST['uniqueId'])){

	if(!empty($_REQUEST['projName'])){
                //to get the top locations from the project
		$queryLoc = $obj->selQRYMultiple('location_id, location_title', 'project_monitoring_locations', 'location_parent_id = 0 AND is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" order by location_id');
		if(!empty($queryLoc)){
			$commaParentLocations = '';
			foreach($queryLoc as $pLocations){
				$parent_location[$pLocations['location_id']] = $pLocations['location_title'];
				if($commaParentLocations == ''){
					$commaParentLocations = $pLocations['location_id'];
				}else{
					$commaParentLocations .= ", ".$pLocations['location_id'];
				}
				
			}
		}
		
		//to get the sub locations of all locations
		$querySubLoc = $obj->selQRYMultiple('location_parent_id, location_id, location_title', 'project_monitoring_locations', 'location_parent_id in ('.$commaParentLocations.') AND is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" order by location_parent_id,location_id');
		if(!empty($querySubLoc)){
			$i = 1;
			$pagecount = 0;
			$html_location[$pagecount] = '<tr><td style="width:290px">Status</td><td style="width:60px;padding:2px;">'.date('d/M/Y').'</td>';
			$html_sub_location[$pagecount] = '<tr><td style="width:290px"><strong>TASK</strong></td><td style="width:60px">';
			foreach($querySubLoc as $subLoc){
				if (intval($i/$col) == 1)
				{
					$html_location[ $pagecount] .= '</tr>';
					$html_sub_location[ $pagecount] .= '</tr>';
					$pagecount++;
					$html_location[$pagecount] = '<tr>';
					$html_sub_location[$pagecount] = '<tr>';
					$i = 1;
				}
				$html_location[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$parent_location[$subLoc['location_parent_id']].'</td>';
				$html_sub_location[ $pagecount] .= '<td style="width:100px;padding:2px;">'.$subLoc['location_title'].'</td>';
				//to get the position of the sub location in row
				$sub_location_position[$subLoc['location_id']] = $i;
				$i++;
			}
		
			if (intval($i/$col) == 1){
				$html_location[$pagecount] = '</tr>';
				$html_sub_location[$pagecount] = '</tr>';
			}
		}
		
		$queryTask = $obj->selQRYMultiple('DISTINCT task', 'progress_monitoring', 'is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" order by progress_id');
		$vpagecount = 0;
		$k = 0;
	
		foreach($queryTask as $tasks){
			$queryTaskData = $obj->selQRYMultiple('sub_location_id, progress_id, task, start_date, end_date, percentage, status', 'progress_monitoring', 'task = "'.$tasks['task'].'" AND is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" order by location_id, sub_location_id');
				
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
                        
			foreach($queryTaskData as $taskData){
				if ($issue_flag){
					$html_tds[$vpagecount] [$hpagecount] .= '<td style="height:52px;padding-left:2px;"><div >'.$tasks['task'].'</div>';
					$queryIssuedToData = $obj->selQRYMultiple('issued_to_name', 'issued_to_for_progress_monitoring', 'progress_id = "'.$taskData['progress_id'].'" AND is_deleted = 0 order by issued_to_name');
					#print_r($queryIssuedToData);
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
					for ($j=$i; $j<$postion;$j++){
						$i++;
						$html_tds [$vpagecount] [$hpagecount] .= "<td style='height:52px;width:100px'><div>&nbsp;</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
					}
				}
				if (intval($i/$col)==1)
				{
					$html_tds [$vpagecount] [$hpagecount] .= "</tr>";
					$hpagecount++;
					if (!isset($html_tds [$vpagecount] [$hpagecount]))
					{
						$html_tds [$vpagecount] [$hpagecount] = "";
					}
					$html_tds [$vpagecount] [$hpagecount] .= "<tr>";
					$i = 1;
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
			/*if($blankCount > 0){
				if($blankCount > $col){
					for($j=$i; $j<$col;$j++){
						$html_tds[$vpagecount][$hpagecount] .= "<td style='height:52px;width:100px;border-bottom:1px solid black'><div>".$i."</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
					}
				}else{
					for($j=$i; $j<$blankCount;$j++){
						$html_tds[$vpagecount][$hpagecount] .= "<td style='height:52px;width:100px;border-bottom:1px solid black'><div>&nbsp;</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
					}
				}
			}*/
			
/*			for ($j=$i; $j<$col;$j++){
				$html_tds [$vpagecount] [$hpagecount] .= "<td style='height:52px;width:100px;border-bottom:1px solid black'><div>".$i."</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
			}
*/			//if last locations are not in the task, then add empty tds
			if (intval($i/$col) != 1){
				if ($hpagecount != $pagecount){
					for($j=$i; $j<$col; $j++){
						$html_tds[$vpagecount][$hpagecount] .= "<td style='height:52px;width:100px;border-bottom:1px solid black'><div>&nbsp;</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
					}
				}else{
					for($j=$i; $j<$postion; $j++){
						$i++;
						$html_tds [$vpagecount] [$hpagecount] .= "<td style='height:52px;width:100px'><div>&nbsp;</div><div style='border-bottom:1px solid black'>&nbsp;</div><div>&nbsp;</div></td>";
					}
				}
				$html_tds [$vpagecount] [$hpagecount] .= '</tr>';
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
            $html .= $html_sub_location[$i];
        }
        $html .= $html_h[$i];
        $html .= '</table>';
		createPDF($html, 'wallChart_part'.$page.'.pdf', $owner_id);
		$zipFileArray[] = '../report_pdf/'.$owner_id.'/wallChart_part'.$page.'.pdf';
		$page++;
		$html = "";
    }
    $flag = false;
}
$zipName = 'wall_chart_pdf'.microtime().'.zip';

if($obj->create_zip($zipFileArray, '../report_pdf/'.$owner_id.'/'.$zipName)){
	echo '<br clear="all" /><div style="margin-left:10px;">Wall Chart Report Generated <a href="report_pdf/'.$owner_id.'/'.$zipName.'" target="_blank" class="download_btn">&nbsp;</a></div>';
}else{
	echo '<br clear="all" /><div style="margin-left:10px;">Wall Chart Report Generation Fail Try Gain Later</div>';
}

function createPDF($html, $report, $owner_id){
	require_once("../dompdf/dompdf_config.inc.php");
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
	
	$d = '../report_pdf/'.$owner_id;
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
?>