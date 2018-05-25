<?php
set_time_limit(0);

$col = 50;
$rows = 36;
$pageBreakCount=36;
/*

session_start();
require_once'includes/functions.php';
$obj = new DB_Class();
$owner_id = $_SESSION['ww_builder_id'];
$phd='';
$myProjects='';
$ihd='';
$myInspections='';
$qi="SELECT pm.sub_location_id FROM user_projects up, progress_monitoring pm where";
	$where='';$or='';
			
if(isset($_REQUEST['SearchInsp'])){
	if(!empty($_REQUEST['projName']))
	{
		$where=" up.project_id='".$_REQUEST['projName']."' and up.project_id = pm.project_id" ;
	}
	if(!empty($_REQUEST['location']) && empty($_REQUEST['sublocation']))
	{
		$where.=" and (pm.location_id='".$_REQUEST['location']."')";
	}
	if(!empty($_REQUEST['sublocation']))
	{
		
		$where.=" and (pm.sub_location_id='".$_REQUEST['sublocation']."')";
	}
	if(!empty($_REQUEST['status']))
	{
		$where.=" and (pm.status='".$_REQUEST['status']."')";
	}
	if($_REQUEST['DRF']!="")
	{
		$sdate=date("Y-m-d", strtotime($_REQUEST['DRF'] . "00:00:00"));
		$where.=" and (pm.start_date>='".$sdate."'";
	}
	if($_REQUEST['DRT']!="")
	{
		$sdate2=date("Y-m-d", strtotime($_REQUEST['DRT'] . "00:00:00"));
		$where.=" and pm.start_date<='".$sdate2."')";
	}
	if($_REQUEST['FBDF']!="")
	{
		$edate=date("Y-m-d", strtotime($_REQUEST['FBDF'] . "00:00:00"));
		$where.=" and (pm.end_date>='".$edate."'";
	}
	if($_REQUEST['FBDT']!="")
	{
		$edate=date("Y-m-d", strtotime($_REQUEST['FBDT'] . "00:00:00"));
		$where.=" and pm.end_date<='".$edate."')";
	}
	$group="GROUP by pm.sub_location_id";
       $query=$qi.$where . " and pm.is_deleted=0  and up.is_deleted=0 " .$group;
	$rset=$obj->db_query($query);
/*	
	while ($row=mysql_fetch_array($rset)) {
		/*$loc_id=$row['location_id'];
		$parent_loc_id = $row['location_parent_id'];
		$location = $row["location_title"];
		$location_title = "";
		$where = "";
		$parent_loc_id = $row['sub_location_id'];
		$query1 = "select location_title from project_monitoring_locations where location_id=$parent_loc_id";
		$rset1=$obj->db_query($query1);
		$row1=mysql_fetch_array($rset1);
		  $location_title = $row1["location_title"];
		if($parent_loc_id == 0)
		{
			$query="select pm.progress_id, pm.task, pm.start_date, pm.end_date,pm.percentage,pm.status from progress_monitoring pm where (pm.location_id='$loc_id') and pm.is_deleted=0 ";
		}else{
			$query="select pm.progress_id, pm.task, pm.start_date, pm.end_date,pm.percentage,pm.status   from progress_monitoring pm where (pm.sub_location_id='$parent_loc_id') and pm.is_deleted=0";
		}

		  if(!empty($_REQUEST['status']))
		  {
			  $where.=" and (pm.status='".$_REQUEST['status']."')";
		  }
		  if($_REQUEST['DRF']!="")
		  {
			  $sdate=date("Y-m-d", strtotime($_REQUEST['DRF'] . "00:00:00"));
			  $where.=" and (pm.start_date>='".$sdate."'";
		  }
		  if($_REQUEST['DRT']!="")
		  {
			  $sdate2=date("Y-m-d", strtotime($_REQUEST['DRT'] . "00:00:00"));
			  $where.=" and pm.start_date<='".$sdate2."')";
		  }
		  if($_REQUEST['FBDF']!="")
		  {
			  $edate=date("Y-m-d", strtotime($_REQUEST['FBDF'] . "00:00:00"));
			  $where.=" and (pm.end_date>='".$edate."'";
		  }
		  if($_REQUEST['FBDT']!="")
		  {
			  $edate=date("Y-m-d", strtotime($_REQUEST['FBDT'] . "00:00:00"));
			  $where.=" and pm.end_date<='".$edate."')";
		  }
		  $query .= $where . " order by progress_id";
		$rsq=$obj->db_query($query);
		echo $query;

*/
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
<table style="width:2340" border="0" class="collapse" cellspacing="0" cellpadding="0">';
	
for($i=0; $i<=59;$i++){
	$html .= '<tr>
		<td rowspan="3" width="100">Ceiling Frame</td>
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
createPDF($html, 'wallChart'.microtime().'part2.pdf', 1);

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