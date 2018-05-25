<?php
set_time_limit(0);

$col = 50;
$rows = 36;
$pageBreakCount=36;

session_start();
require_once'includes/commanfunction.php';
$obj = new COMMAN_Class();
$owner_id = $_SESSION['ww_builder_id'];

$html_location = '<tr>';
$html_sub_location = '<tr>';

$parent_location = array();
$sub_location_position = array();
if(isset($_REQUEST['uniqueId'])){
	if(!empty($_REQUEST['projName'])){
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
		
		$querySubLoc = $obj->selQRYMultiple('location_parent_id, location_id, location_title', 'project_monitoring_locations', 'location_parent_id in ('.$commaParentLocations.') AND is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" order by location_id');
		if(!empty($querySubLoc)){
			$i = 0;
			foreach($querySubLoc as $subLoc){				$i++;
				$html_location .= '<td>'.$parent_location[$subLoc['location_parent_id']].'</td>';
				$html_sub_location .= '<td>'.$subLoc['location_id'].'-----'.$subLoc['location_title'].'</td>';
				$sub_location_position[$subLoc['location_id']] = $i;
			}
			$html_location .= '</tr>';
			$html_sub_location .= '</tr>';
		}
		$queryTask = $obj->selQRYMultiple('DISTINCT task', 'progress_monitoring', 'is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" order by sub_location_id');
		foreach($queryTask as $tasks){
			$queryTaskData = $obj->selQRYMultiple('sub_location_id, progress_id, task, start_date, end_date, percentage, status', 'progress_monitoring', 'task = "'.$tasks['task'].'" AND is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" group by sub_location_id order by sub_location_id');
			$i = 1;
			echo $tasks['task'].'<br />';
			foreach($queryTaskData as $taskData){
				if($tasks['task'] == $taskData['task']){
					$postion = $sub_location_position[$taskData['sub_location_id']];
	#echo $postion.'i ki value=>'.$i;
					if($postion != $i){
						$i++;
						for ($j=$i; $j<$postion;$j++){
							$i++;
							$html_tds .= "<td>&nbsp;</td>";
						}
					}
					$html_tds .= "<td>
									<div>".$taskData['progress_id']."</div><br />
									<div>".$taskData['end_date']."</div><br />
									<div";
									if($taskData['status'] == 'Ahead'){
										$html_tds .= " style='background-color:#0000FF;'";
									}
									if($taskData['status'] == 'Behind'){
										$html_tds .= " style='background-color:#FF0000;'";
									}
									if($taskData['status'] == 'Complete'){
										$html_tds .= " style='background-color:#008000;'";
									}
									if($taskData['status'] == 'On Time'){
										$html_tds .= " style='background-color:#FFA500;'";
									}
					$html_tds .= ">".$taskData['percentage']."</div>
								<td>";
					$i++;
				}else{
					$html_tds .= '</tr><tr>';
				}
			}
		}
	}
}

$html = '<table style="width:2340" border="0" class="collapse" cellspacing="0" cellpadding="0">';
$html .= $html_location;
$html .= $html_sub_location;
$html .= '<tr>'.$html_tds.'</tr>';
$html .= '</table>';
echo $html;



die;
$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Untitled Document</title>

<style>
@charset "utf-8";
body{
	font-family : Trebuchet MS, Arial, serif;
	padding: 5px;
}
table.collapse {
	border-collapse: collapse;
	border: 1pt solid black;  
}
table.collapse td {
	border: 1pt solid black;
	padding: 2px;
}
</style>
</head>
<body>
<table style="width:2340" border="0" class="collapse" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="29" align="center" style="border:0"><h2>Wall Chart Report</h2></td>
	</tr>
	<tr>
		<td colspan="3" style="border:0">&nbsp;</td>
		<td style="border:0" align="left" align="left">Behind</td>
		<td bgcolor="#FF0000" style="border:0">&nbsp;</td>
		<td style="border:0">&nbsp;</td>
		<td style="border:0" align="left" >In progress</td>
		<td bgcolor="#FFA500" style="border:0">&nbsp;</td>
		<td style="border:0">&nbsp;</td>
		<td style="border:0" align="left" >Complete</td>
		<td bgcolor="#008000" style="border:0">&nbsp;</td>
		<td style="border:0">&nbsp;</td>
		<td style="border:0" align="left">Signed Off</td>
		<td bgcolor="#0000FF" style="border:0">&nbsp;</td>
		<td colspan="14" style="border:0 0 1 0">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="29" align="center" style="border:0">&nbsp;</td>
	</tr>

	<tr>
		<td style="width:190px;">Status:</td>
		<td style="width:60px;" align="center">8/11/11</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>
		<td style="width:100px;" align="center">Ground&nbsp;Floor</td>

		<td style="width:100px;" align="center">First&nbsp;Floor</td>
		<td style="width:100px;" align="center">First&nbsp;Floor</td>
		<td style="width:100px;" align="center">First&nbsp;Floor</td>
		<td style="width:100px;" align="center">First&nbsp;Floor</td>
		<td style="width:100px;" align="center">First&nbsp;Floor</td>
		<td style="width:100px;" align="center">First&nbsp;Floor</td>
		<td style="width:100px;" align="center">First&nbsp;Floor</td>
		<td style="width:100px;" align="center">First&nbsp;Floor</td>
		<td style="width:100px;" align="center">First&nbsp;Floor</td>
		<td style="width:100px;" align="center">First&nbsp;Floor</td>
		<td style="width:100px;" align="center">First&nbsp;Floor</td>
		<td style="width:100px;" align="center">First&nbsp;Floor</td>
		<td style="width:100px;" align="center">First&nbsp;Floor</td>
	</tr>

	<tr>
		<td>TASK</td>
		<td>&nbsp;</td>
		<td>B5</td>
		<td>B7</td>
		<td>B8</td>
		<td>B9</td>
		<td>B10</td>
		<td>B6 (Prototype)</td>
		<td>B11</td>
		<td>B1</td>
		<td>B2</td>
		<td>B3</td>
		<td>B4</td>
		<td>B12</td>
		<td>B13</td>
		<td>Hallway/ Lobby</td>

		<td>B20</td>
		<td>B21</td>
		<td>B22</td>
		<td>B23</td>
		<td>B24</td>
		<td>B25</td>
		<td>B26</td>
		<td>B27</td>
		<td>B28</td>
		<td>B29</td>
		<td>B30</td>
		<td>B31</td>
		<td>B32</td>
	</tr>';
for($i=0; $i<=55;$i++){
	$html .= '<tr>
		<td rowspan="2" width="100">Ceiling Frame</td>
		<td>Start</td>
		<td>26-Oct-11</td>
		<td>26-Oct-11</td>
		<td>26-Oct-11</td>
		<td>26-Oct-11</td>
		<td>26-Oct-11</td>
		<td>26-Oct-11</td>
		<td>26-Oct-11</td>
		<td>26-Nov-11</td>
		<td>26-Nov-11</td>
		<td>26-Nov-11</td>
		<td>26-Nov-11</td>
		<td>26-Nov-11</td>
		<td>26-Nov-11</td>
		<td>26-Nov-11</td>
		
		<td>26-Oct-11</td>
		<td>26-Oct-11</td>
		<td>26-Oct-11</td>
		<td>26-Oct-11</td>
		<td>26-Oct-11</td>
		<td>26-Oct-11</td>
		<td>26-Oct-11</td>
		<td>26-Nov-11</td>
		<td>26-Nov-11</td>
		<td>26-Nov-11</td>
		<td>26-Nov-11</td>
		<td>26-Nov-11</td>
		<td>26-Nov-11</td>
	</tr>

	<tr>
		<td>Finish</td>
		<td>3-Nov-11</td>
		<td>3-Nov-11</td>
		<td>3-Nov-11</td>
		<td>3-Nov-11</td>
		<td>3-Nov-11</td>
		<td>3-Nov-11</td>
		<td>3-Nov-11</td>
		<td>7-Nov-11</td>
		<td>7-Nov-11</td>
		<td>7-Nov-11</td>
		<td>7-Nov-11</td>
		<td>7-Nov-11</td>
		<td>7-Nov-11</td>
		<td>7-Nov-11</td>
		
		<td>3-Nov-11</td>
		<td>3-Nov-11</td>
		<td>3-Nov-11</td>
		<td>3-Nov-11</td>
		<td>3-Nov-11</td>
		<td>3-Nov-11</td>
		<td>3-Nov-11</td>
		<td>7-Nov-11</td>
		<td>7-Nov-11</td>
		<td>7-Nov-11</td>
		<td>7-Nov-11</td>
		<td>7-Nov-11</td>
		<td>7-Nov-11</td>
	</tr>
	
	<tr>
		<td>Issue To : Garuav</td>
		<td>Status</td>
		<td bgcolor="#009900">63%</td>
		<td bgcolor="#009900">63%</td>
		<td bgcolor="#009900">63%</td>
		<td bgcolor="#009900">63%</td>
		<td bgcolor="#009900">63%</td>
		<td bgcolor="#009900">63%</td>
		<td bgcolor="#0000FF">100%</td>
		<td bgcolor="#0000FF">100%</td>
		<td bgcolor="#CC6600">45%</td>
		<td bgcolor="#CC6600">45%</td>
		<td bgcolor="#CC6600">45%</td>
		<td bgcolor="#CC6600">45%</td>
		<td bgcolor="#CC6600">45%</td>
		<td bgcolor="#CC6600">45%</td>
		
		<td bgcolor="#009900">63%</td>
		<td bgcolor="#009900">63%</td>
		<td bgcolor="#009900">63%</td>
		<td bgcolor="#009900">63%</td>
		<td bgcolor="#009900">63%</td>
		<td bgcolor="#009900">63%</td>
		<td bgcolor="#0000FF">100%</td>
		<td bgcolor="#0000FF">100%</td>
		<td bgcolor="#CC6600">45%</td>
		<td bgcolor="#CC6600">45%</td>
		<td bgcolor="#CC6600">45%</td>
		<td bgcolor="#CC6600">45%</td>
		<td bgcolor="#CC6600">45%</td>
	</tr>';
}
$html .= '</table>
</body>
</html>';
echo $html;
createPDF($html, 'wallChart'.microtime().'part1.pdf', 1);

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
	//$dompdf->stream("report.pdf");
	//exit(0);
	$output = $dompdf->output($report);
	
	// generate pdf in folder
	$d = 'report_pdf/'.$owner_id;
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