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

if(isset($_REQUEST['name'])){//Conditoin to check Location order in qatask
	$locIDs = $obj->selQRYMultiple('loc.location_id, loc.location_title, GROUP_CONCAT(distinct loc.location_id) AS locationIDs, task.excluded_location', 'qa_task_locations AS loc, qa_task_monitoring AS task', 'task.sub_location_id = loc.location_id AND loc.`project_id` = '.$_REQUEST['projNameQA'].' AND task.is_deleted = 0 AND loc.is_deleted = 0 GROUP BY loc.location_title ORDER BY task.subloc_order_wall_chart_report');	
	$locIDsZero = $obj->selQRYMultiple('loc.location_id, loc.location_title, GROUP_CONCAT(distinct loc.location_id) AS locationIDs, task.excluded_location', 'qa_task_locations AS loc, qa_task_monitoring AS task', 'task.sub_location_id = loc.location_id AND loc.`project_id` = '.$_REQUEST['projNameQA'].' AND task.is_deleted = 0 AND task.subloc_order_wall_chart_report = 0 AND loc.is_deleted = 0 GROUP BY loc.location_title ORDER BY task.subloc_order_wall_chart_report');	

if(sizeof($locIDs) == sizeof($locIDsZero)){
	$i=0;
	foreach($locIDsZero as $lozero){$i++;
		$query = "UPDATE qa_task_monitoring SET subloc_order_wall_chart_report = ".$i.", last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE project_id = ".$_REQUEST['projNameQA']." AND is_deleted = 0 AND sub_location_id in (".$lozero['locationIDs'].")";
		mysql_query($query);
	}
}//Conditoin to check Location order in qatask*/
	$projID = '';	$locationQA = '';	$subLocationQA1 = '';	$subLocationQA2 = '';	$subLocationQA3 = '';
	$totalCount = 0; $locArr = array(); $subLoc1Arr = array(); $subLoc2Arr = array(); $leafLocArr = array();
	$noInspection = 0;
	$locArray = array();
	if(!empty($_REQUEST['projNameQA'])){
		$projID = $_REQUEST['projNameQA'];
		$projectName = $obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projNameQA'], 'project_name');
	}
	
	if(!empty($_REQUEST['locationQA'])){
		$locArray[] = $_REQUEST['locationQA'];
		$locationQA = $_REQUEST['locationQA'];
	}

//Start code here
//Location Titles Array
	$locationTitles = $obj->selQRYMultiple('loc.location_id, loc.location_title', 'qa_task_locations AS loc', 'loc.project_id = '.$projID.' AND loc.is_deleted = 0');
	$lTitlesArray = array();
	foreach($locationTitles as $tData){
		$lTitlesArray[ $tData["location_id"]] = $tData["location_title"];
	}

//Last sub location array with order 
	$sublocIDs3 = $obj->selQRYMultiple('loc.location_id, loc.location_title, GROUP_CONCAT(distinct loc.location_id) AS locationIDs, cast(GROUP_CONCAT(distinct loc.location_parent_id) AS char) as parentLocIDs, task.subloc_order_wall_chart_report', 'qa_task_locations AS loc, qa_task_monitoring AS task', 'task.sub_location_id = loc.location_id AND loc.project_id = '.$projID.' AND task.is_deleted = 0 AND loc.is_deleted = 0 AND task.excluded_location = "NO" GROUP BY loc.location_title ORDER BY task.subloc_order_wall_chart_report');	

//Create array to put data for location columns end here
	$lastLocTitle = array();
	$whereLocidArr = array();
	foreach($sublocIDs3 as $lID){
		$lastLocTitle[$lID['subloc_order_wall_chart_report']] = $lID['location_title'];
		$whereLocidArr[] = $lID['locationIDs'];
	}
	$whereLocids = join(',', $whereLocidArr);
	
	$taskData = $obj->selQRYMultiple('sub_location_id, GROUP_CONCAT(DISTINCT STATUS) as status', 'qa_task_monitoring', 'project_id = '.$projID.' AND is_deleted = 0 AND sub_location_id IN ('.$whereLocids.') AND excluded_location = "NO"  GROUP BY sub_location_id ORDER BY subloc_order_wall_chart_report, sub_location_id');
	//echo '<pre>';print_r($taskData);
	
	$noInspection = count($taskData);
	
	if($noInspection > 0){
		$statusArr = array();
		foreach($taskData as $tData){
			$valArr = explode(',', $tData['status']);
			if(sizeof($valArr) == 1 && $valArr[0] == 'Yes'){
				$statusArr[$tData['sub_location_id']] = 'style="background-color:#008000;"';
			}else{
				$statusArr[$tData['sub_location_id']] = 'style="background-color:#FFFFFF;"';
			}
		}
	}
//To create multi-dimensional array of Locations
	$locationTreeOnIds = $obj->selQRYMultiple('location_tree', 'qa_task_monitoring', 'project_id = '.$projID.' AND is_deleted = 0 ORDER BY sub_location_id');
	$locationTitleArr = array();
	foreach($locationTreeOnIds as $lData){
		$locationTree = $lData["location_tree"];
		$lArray = explode (" > ", $locationTree);
		$l2Title = $lTitlesArray [$lArray[1]];
		$l3Title = $lTitlesArray [$lArray[2]];
		$l4Title = $lTitlesArray [$lArray[3]];
		if (! is_array ($locationTitleArr[ $l2Title])){
			$locationTitleArr[ $l2Title] = array();
			$locationTitleArr[ $l2Title] [$l3Title] = array();
			$locationTitleArr[ $l2Title] [$l3Title] [$l4Title] = $statusArr[$lArray[3]];
		}else{
			if (! is_array ($locationTitleArr[ $l2Title] [$l3Title])){
				$locationTitleArr[ $l2Title] [$l3Title] = array();
				$locationTitleArr[ $l2Title] [$l3Title] [$l4Title] = $statusArr[$lArray[3]];
			}else {
				$locationTitleArr[ $l2Title] [$l3Title] [$l4Title] = $statusArr[$lArray[3]];
			}
		}
	}	
#print_r($locationTitleArr);die;
	if($noInspection > 0){
		$topHeader = '<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="4">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="4" align="right"><img src="company_logo/logo.png" height="40"  /></td>
						</tr>
						<tr>
							<td colspan="4" style="font-size:14px;"><b><u>Quality Assurance Wall Chart Report</b></u></td>
						</tr>
						<tr>
							<td width="10%" style="font-size:14px;"><strong>Project&nbsp;Name</strong></td>
							<td>:&nbsp;</td>
							<td width="90%" >'.$projectName.'</td>
						</tr>
						
					</table>';
		$html = '<table width="98%" border="1" cellpadding="0" cellspacing="0">
					<tr>
						<td>&nbsp;</td>';
						$lastLocTitle = array_values($lastLocTitle);

						for($y=0;$y<sizeof($lastLocTitle);$y++){
							$html .= '<td><div class="horizontalDiv">'.$lastLocTitle[$y].'</div></td>';
						}
				$html .= '</tr>';
				$horLupCount = sizeof($lastLocTitle);
				ksort($locationTitleArr);
				$locationTitleArr = array_reverse($locationTitleArr);
				foreach ($locationTitleArr as $key=>$subLocationArray){

					$toprow = '<tr><td>'.$key.'</td><td colspan="'.$horLupCount.'"></td></tr>';
					
					ksort($subLocationArray);
					$toprowFlag = false;
					foreach ($subLocationArray as $key=>$value){
						$newrow = '<tr><td style="padding-left:20px;">'.$key.'</td>';
						$flag = false;
						for($y=0;$y<$horLupCount;$y++){
							if (array_key_exists ($lastLocTitle[$y], $value))
								$flag = true;
								
							$newrow .= '<td '.$value[$lastLocTitle[$y]] .'>&nbsp;</td>';
						}
						$newrow .= '</tr>';
						if ($flag)
						{
							$toprowFlag = true;
							$toprow .= $newrow;
						}
					}
					if ($toprowFlag)
					{
						$html .= $toprow;
					}
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
		<div id="mainContainer">
			<div class="buttonDiv">
				<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
				<img onClick="downloadPDFQA();"src="images/download_btn.png" style="float:right;" />
			</div><br clear="all" />
			<div id="htmlContainer">
				<?php echo $topHeader.$html;?>
			</div>
		</div>
		</body>
		</html>
<?php }else{
		$html = 'No Record Found !';
	}
}?>