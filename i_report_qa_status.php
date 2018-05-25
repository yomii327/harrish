<?php
ob_start();
session_start();

include('includes/commanfunction.php');
$obj= new COMMAN_Class();

$mtime = microtime(); 
$mtime = explode(" ",$mtime); 
$mtime = $mtime[1] + $mtime[0]; 
$starttime = $mtime; 

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}
if(isset($_REQUEST['name'])){
	$projID = '';	$content ='';	$totalCount = 0; $where = '';
	
	$locArray = array();
	if(!empty($_REQUEST['projNameQA'])){
		$projID = $_REQUEST['projNameQA'];
		$projectName = $obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projNameQA'], 'project_name');
	}
		
	if(!empty($_REQUEST['searchKeyword'])){
		$where .= ' AND (task LIKE "%'.trim($_REQUEST['searchKeyword']).'%" OR status LIKE "%'.trim($_REQUEST['searchKeyword']).'%")';
	}

	if(!empty($_REQUEST['status'])){
		$where .= " AND status = '".$_REQUEST['status']."'";
	}
//Code for find Location title array 	
	$locData = $obj->selQRYMultiple('location_id, location_title', 'qa_task_locations', 'project_id = '.$projID.' AND is_deleted = 0 GROUP BY location_id ORDER BY location_id');

	$locArrayData = array();
	foreach($locData as $ldata){
		$locArrayData[$ldata['location_id']] = $ldata['location_title'];
	}
//Fetch data here	
	$taskData = $obj->selQRYMultiple('task_id, project_id, location_id, sub_location_id, GROUP_CONCAT(DISTINCT STATUS) as status, location_tree', 'qa_task_monitoring', 'project_id = '.$projID.' AND is_deleted = 0 '.$where.' GROUP BY sub_location_id ORDER BY sub_location_id');
	$row = $obj->selQRY('qa_num_sublocations', 'user_projects', 'project_id = "'.$projID.'" AND is_deleted = 0');
	$qa_num_sublocations = $row["qa_num_sublocations"];
	
	if(!empty($taskData)){
		$noInspection = count($taskData);	
	}else{
		$noInspection = 0;
	}
	$noPages = ceil(($noInspection-10)/15 +1);
	if($noInspection > 0){
		$html='<table width="98%" border="0" align="center">
			<tr>
				<td width="40%"></td><td width="60%" align="right" style="padding-right:20px;">
					<img src="company_logo/logo.png" height="40"  />
				</td>
			</tr>
			<tr>
				<td width="40%" style="font-size:14px"><u><b>Quality Assurance Status Report</b></u></td>
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
				<td style="font-size:12px"><strong>Page: </strong>1 of '.$noPages.'</td>
				<td>&nbsp;</td>
			</tr>
		</table>';
		$i = 0;$pageCount = 1;
		$locArKey = array();
		if(!empty($taskData )){
			$html .='<table width="98%" class="collapse" cellpadding="0" cellspaccing="0" align="center" border="0" style="">
				<tr>
					<td width="15%" style="font-size:12px"><b>Location</b></td>';
						for($j=1; $j<=$qa_num_sublocations; $j++){
							$html .='<td width="25%" style="font-size:12px"><b>Sublocation '.$j.'</b></td>';
						}
						$html .='<td width="10%" style="font-size:12px"><b>QA Completed</b></td>
					</tr>';
			foreach($taskData as $conData){
				$exLocData = explode(' > ', $conData['location_tree']);
				$locArKey = array_shift($exLocData);
				$lupCount = sizeof($exLocData);
				$pagebreak = 25;
				if($pageCount == 1){
					$pagebreakCount = 20;
					if($i == $pagebreakCount){
						$html .= '</table><div style="page-break-after: always;"></div><table width="98%" cellpadding="0" align="center" cellspacing="0" class="collapse">
						<tr>
							<td width="15%" style="font-size:12px"><b>Location</b></td>';
								for($j=1; $j<=$qa_num_sublocations; $j++){
									$html .='<td width="25%" style="font-size:12px"><b>Sublocation '.$j.'</b></td>';
								}
								$html .='<td width="10%" style="font-size:12px"><b>QA Completed</b></td>
							</tr>';
						$pageCount++;
						$pagebreakCount = $pagebreakCount + $pagebreak;	
					}
				}else{
					if($i == $pagebreakCount){
						$html .= '</table><div style="page-break-after: always;"></div><table width="98%" cellpadding="0" align="center" cellspacing="0" class="collapse">
						<tr>
							<td width="15%" style="font-size:12px"><b>Location</b></td>';
								for($j=1; $j<=$qa_num_sublocations; $j++){
									$html .='<td width="25%" style="font-size:12px"><b>Sublocation '.$j.'</b></td>';
								}
								$html .='<td width="10%" style="font-size:12px"><b>QA Completed</b></td>
							</tr>';
						$pageCount++;
						$pagebreakCount = $pagebreakCount + $pagebreak;	
					}
				}
				$html .='<tr>
					<td style="font-size:12px">'.$locArrayData[$locArKey].'</td>';
						for($j=1; $j<=$lupCount; $j++){
							$html .='<td style="font-size:12px">'.$locArrayData[$exLocData[$j-1]].'</td>';
						}
				$stData = explode(',', $conData['status']);
				if(in_array('', $stData) || in_array('NA', $stData)){
					$pos = true;
				}else{
					$pos = false;				
				}
				if ($pos !== false) {
					$imgTag = '';
				} else {
					$imgTag = '<label ><img src="images/tick.png" alt="no"  />';
				}
				$html .='<td align="center" style="font-size:12px">'.$imgTag.'</td>
				</tr>';
				$i++;
			}		
			$html .='</table>';
		}
		/*if ($i%2!=0){
			$html .= '<div class="footer" style="text-align:center;font-weight:bold;font-size:10px;';
			if($i==1){
				$html .= 'margin-top:110px;';
			}else{
				$html .= 'margin-top:5px;';
			}
			$html .= '" >DefectID, part of the Wiseworker Quality Management Ecosystem, helping the construction industry.<br />www.wiseworker.net</div>';
		}*/
		
		
		
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$endtime = $mtime; 
	$totaltime = ($endtime - $starttime); 
	$totaltime = number_format($totaltime, 2, '.', ''); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Untitled Document</title>
	<style>
		table.collapse td{
			padding:5px;
		}
	</style>
</head>
<body>
	<div id="mainContainer">
		<div class="buttonDiv">
			<span style="padding-left:25px;font-size:15px;">
				<?php echo $totalCount.' results ('.$totaltime.' seconds)';?>
			</span><br /><br />
			<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
			<img onClick="downloadPDFQA();"src="images/download_btn.png" style="float:right;" />
		</div><br clear="all" />
		<div id="htmlContainer">
			<?php echo $html;?>
		</div>
	</div>
</body>
</html>
<?php }else{?>
		<div style="margin-left:10px;color:#000;"><i>No Record Found</i></div>
<?php }
	}else{?>
		<div style="margin-left:10px;color:#000;"><i>No Record Found</i></div>
<?php }?>