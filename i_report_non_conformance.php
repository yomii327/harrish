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
#	echo '<pre>';print_r($locData);print_r($locArrayData);die;
	$conformanceData = $obj->selQRYMultiple('t.task_id, t.comments, qn.non_conformance_id, qn.project_id, qn.location_id, qn.task_id, qn.qa_inspection_date_raised, qn.qa_inspection_raised_by, qn.qa_inspection_inspected_by, qn.qa_inspection_description, qn.qa_inspection_location',
	'qa_inspections as qn, qa_task_monitoring as t, qa_task_locations as loc',
	'qn.project_id = '.$projID.' AND qn.is_deleted = 0 AND qn.location_id IN ('.$searchSubLoc.') AND t.project_id = '.$projID.' AND t.is_deleted = 0 AND t.task_id = qn.task_id AND qn.location_id = loc.location_id AND loc.is_deleted = 0 ORDER BY qn.location_id');
	//echo $searchSubLoc;
#echo '<pre>'; print_r($conformanceData);die;
	if(!empty($conformanceData)){
		$noInspection = count($conformanceData);	
	}else{
		$noInspection = 0;
	}
	$noPages = $noInspection;

	if($noInspection > 0){
		$html='<table width="98%" border="0" align="center">
			<tr>
				<td width="40%"></td><td width="60%" align="right" style="padding-right:20px;">
					<img src="company_logo/logo.png" height="40"  />
				</td>
			</tr>
			<tr>
				<td width="40%" style="font-size:14px"><u><b>Non Conformance Report</b></u></td>
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
				<td style="font-size:12px"><strong>Inspections: </strong>'.$noInspection.'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Page: </strong>1 of '.$noPages.'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Report filtered by: </strong></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="padding-left:30px;" colspan="2">
					<table width="100%" border="0">
						<tr>';
							$jk=0;
							if($locationQA != ''){
								$html .= '<td width="50" style="font-size:11px;" valign="top"><b>Location Name : </b></td>
								<td width="110" style="font-size:11px;" valign="top">'.$locArrayData[$locationQA].'</td>';
								$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							if($subLocationQA1 != ''){
								$html .= '<td width="50" style="font-size:11px;" valign="top"><b>Sub Location1 : </b></td>
								<td width="110" style="font-size:11px;" valign="top">'.$locArrayData[$subLocationQA1].'</td>';
								$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							if($subLocationQA2 != ''){
								$html .= '<td width="50" style="font-size:11px;" valign="top"><b>Sub Location2 : </b></td>
								<td width="110" style="font-size:11px;" valign="top">'.$locArrayData[$subLocationQA2].'</td>';
								$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
							}
							if($subLocationQA3 != ''){
								$html .= '<td width="50" style="font-size:11px;" valign="top"><b>Sub Location3 : </b></td>
								<td width="110" style="font-size:11px;" valign="top">'.$locArrayData[$subLocationQA3].'</td>';
								$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
							}
						$html .= '</tr>
					</table>
				</td>
			</tr>
		</table>';
		$i = 1;
		foreach($conformanceData as $conData){
			$issueToData = $obj->selQRYMultiple('qa_issued_to_id, qa_issued_to_name, qa_inspection_fixed_by_date, qa_cost_attribute, qa_inspection_status', 'qa_issued_to_inspections', 'non_conformance_id = '.$conData['non_conformance_id'].' AND is_deleted = 0 AND project_id = '.$projID.' GROUP BY qa_issued_to_name');
			
			$issueToData_issueToName = ""; $issueToData_fixedByDate= ""; $issueToData_status= "";$issueToData_costAttribute = "";
			
			if(!empty($issueToData)){
				foreach($issueToData as $issueData){
					if($issueToData_issueToName == ''){
						$issueToData_issueToName = stripslashes($issueData['qa_issued_to_name']);
					}else{
						$issueToData_issueToName .= ' > '.stripslashes($issueData['qa_issued_to_name']);
					}
	
					if($issueToData_fixedByDate == ''){
						$issueData['qa_inspection_fixed_by_date'] != '0000-00-00' ? $issueToData_fixedByDate = stripslashes(date("d/m/Y", strtotime($issueData['qa_inspection_fixed_by_date']))) : $issueToData_fixedByDate = '' ;
					}else{
						$issueData['qa_inspection_fixed_by_date'] != '0000-00-00' ? $issueToData_fixedByDate .= ' > '.stripslashes(date("d/m/Y", strtotime($issueData['qa_inspection_fixed_by_date']))) : $issueToData_fixedByDate = '' ;
					}
				
					if($issueToData_status == ''){
						$issueToData_status = stripslashes($issueData['qa_inspection_status']);
					}else{
						$issueToData_status .= ' > '.stripslashes($issueData['qa_inspection_status']);
					}
					
					if($issueToData_costAttribute == ''){
						$issueToData_costAttribute = stripslashes($issueData['qa_cost_attribute']);
					}else{
						$issueToData_costAttribute .= ' > '.stripslashes($issueData['qa_cost_attribute']);
					}
				}
			}
			if ($i!=1){
				$pageCount++;
				$html .= '<div style="page-break-before: always;"></div>Page: </strong>'.$pageCount.' of '.$noPages . "<br/><br/>" . $i . ".";
			}else{
				$html .= "<br/><br/>" . $i . ".<br/>";
			}
			
			$html .='<table width="98%" class="collapse" cellpadding="0" cellspaccing="0" align="center" border="0" style="">
						<tr>
							<td width="10%" style="background-color:#CCCCCC;font-size:12px;"><i>Location</i></td>
							<td width="40%" style="font-size:11px;">'.$conData['qa_inspection_location'].'</td>
							<td width="" style="background-color:#CCCCCC;font-size:12px;"><i>Description</i></td>
						</tr>
						<tr>
							<td width="10%" style="background-color:#CCCCCC;font-size:12px;"><i>Date&nbsp;Raised</i></td>
							<td width="40%" style="font-size:11px;">';
								if($conData['qa_inspection_date_raised'] != '0000-00-00'){
									$html .= stripslashes(date("d/m/Y", strtotime($conData['qa_inspection_date_raised'])));
								}
						$html .='</td>
							<td width="" style="padding-left:3px;font-size:11px;" valign="top" rowspan="3">'.stripslashes(substr($conData['qa_inspection_description'], 0, 200));
							if(strlen($conData['qa_inspection_description']) > 200){
								$html .='......';
							}
						$html .='</td>
						</tr>
						<tr>
							<td width="10%" style="background-color:#CCCCCC;font-size:12px;"><i>Inspected&nbsp;By</i></td>
							<td width="40%" style="font-size:11px;">'.stripslashes($conData['qa_inspection_inspected_by']).'</td>
						</tr>
						<tr>
							<td width="10%" style="background-color:#CCCCCC;font-size:12px;"><i>Raised&nbsp;By</i></td>
							<td width="40%" style="font-size:11px;">'.stripslashes($conData['qa_inspection_raised_by']).'</td>
						</tr>
						<tr>
							<td width="10%" style="background-color:#CCCCCC;font-size:12px;"><i>Issued&nbsp;To</i></td>
							<td width="40%" style="font-size:11px;">'.$issueToData_issueToName.'</td>
							<td width="50%" style="background-color:#CCCCCC;font-size:12px;"><i>Comments</i></td>
						</tr>
						<tr>
							<td width="10%" style="background-color:#CCCCCC;font-size:12px;"><i>Fix by Date</i></td>
							<td width="40%" style="font-size:11px;">'.$issueToData_fixedByDate.'</td>
							<td width="50%" style="padding-left:3px;font-size:11px;" valign="top" rowspan="3">'.stripslashes(substr($conData['comments'], 0, 200));
							if(strlen($conData['comments']) > 200){
								$html .='......';
							}
						$html .='</td>
						</tr>
						<tr>
							<td width="10%" style="background-color:#CCCCCC;font-size:12px;"><i>Cost&nbsp;Attribute</i></td>
							<td width="40%" style="font-size:11px;">'.$issueToData_costAttribute.'</td>
						</tr>
						<tr>
							<td width="10%" style="background-color:#CCCCCC;font-size:12px;"><i>Status</i></td>
							<td width="50%" style="font-size:11px;">'.$issueToData_status.'</td>
						</tr>
						<tr>
							<td width="50%" align="center" valign="top" colspan="2">
							<div style="width:350px; height:370px;">';
							$images = $obj->selQRYMultiple('qa_graphic_name, qa_graphic_id', 'qa_graphics', 'non_conformance_id ='.$conData['non_conformance_id'].' AND project_id = '.$projID.' AND qa_graphic_type = "images" AND is_deleted = 0');
							
							if(isset($images[0]) && $images[0]['qa_graphic_name'] != ''){
								$obj->resizeImages('inspections/photo/'.$images[0]['qa_graphic_name'], 320, 350, 'inspections/photo/photo_confom/'.$images[0]['qa_graphic_name']);
								if(file_exists('inspections/photo/photo_confom/'.$images[0]['qa_graphic_name'])){
									$html .='<img src="inspections/photo/photo_confom/'.$images[0]['qa_graphic_name'].'" />';
								}else{
									$html .='<img src="inspections/photo/'.$images[0]['qa_graphic_name'].'" style="width:320px;height:350px" />';
								}
							}
							$html .='</div></td>
							<td width="50%" align="center" valign="top">
							<div style="width:350px; height:370px;">';
							$drawing = $obj->selQRYMultiple('qa_graphic_name, qa_graphic_id', 'qa_graphics', 'non_conformance_id ='.$conData['non_conformance_id'].' AND project_id = '.$projID.' AND qa_graphic_type = "drawing" AND is_deleted = 0');		
							if(isset($drawing[0]) && $drawing[0]['qa_graphic_name'] != ''){
								$obj->resizeImages('inspections/drawing/'.$drawing[0]['qa_graphic_name'], 320, 350, 'inspections/drawing/drawing_confom/'.$drawing[0]['qa_graphic_name']);
								if(file_exists('inspections/drawing/drawing_confom/'.$drawing[0]['qa_graphic_name'])){
									$html .='<img src="inspections/drawing/drawing_confom/'.$drawing[0]['qa_graphic_name'].'" />';
								}else{
									$html .='<img src="inspections/drawing/'.$drawing[0]['qa_graphic_name'].'" style="width:320px;height:350px" />';
								}
							}
						$html .='</div></td>
						</tr>
					</table>';
if ($i%2!=0){
	$html .= '<div class="footer" style="text-align:center;font-weight:bold;font-size:10px;';
	if($i==1){
		$html .= 'margin-top:110px;';
	}else{
		$html .= 'margin-top:5px;';
	}
	$html .= '" >DefectID, part of the Wiseworker Quality Management Ecosystem, helping the construction industry.<br />
www.wiseworker.net</div>';
}
		
	$i++;	}
		
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
			<?php #$pageCount = $totalCount / $limit;?>
		<div class="pagination" <?php if($totalCount <= $limit){ echo 'style="display:none;" ';}?> >
			<?php $leftLimit = $offset - $limit;
				if($leftLimit < 0){ $leftLimit = 0; }else{ }?>
				<img id="previousImages" src="images/prev_icon.png" onclick="pageScroll(<? echo $leftLimit;?>);"
				<?php if($limit == 2){ echo 'style="float:left"'; }elseif($leftLimit < 0){ echo 'style="display:none;float:left;"'; }else{ echo 'style="float:left"';} ?> />
				<?php if($pageCount > 0){
						for($l=0; $l<$pageCount; $l++){?>
							<span <? if(($l*$limit) == $offset){
								echo 'class="page_active" ';
							}else{
								echo 'class="page_deactive" ';
							}
							if($l >= 5){
								echo 'style="display:none;" ';
							}?>
							onclick="pageScroll(<?php echo ($l*$limit); ?>)" ><?php echo ($l+1)?></span>
				<?php 	}
						if($l >= 5){ ?>
							<span><strong>.</strong></span>
							<span><strong>.</strong></span>
							<span><strong>.</strong></span>
					<? }
					}
					$rightLimit = $offset + $limit;
					if($rightLimit >= $totalCount){ $rightLimit = $totalCount; }else{ } ?>
					<img id="nextImages" src="images/next_icon.png" onclick="pageScroll(<?php echo $rightLimit;?>);"
					<?php if($rightLimit > $totalCount){ echo 'style="margin-left:5px;display:none;"'; }else{ echo 'style="margin-left:5px;"'; }?> />
		</div><br /><br /><br />
		<div id="htmlContainer">
			<?php echo $html;?>
		</div>
			<?php #$pageCount = $totalCount / $limit;?>
		<div class="pagination" <?php if($totalCount <= $limit){ echo 'style="display:none;" ';}?> >
			<?php $leftLimit = $offset - $limit;
				if($leftLimit < 0){ $leftLimit = 0; }else{ }?>
				<img id="previousImages" src="images/prev_icon.png" onclick="pageScroll(<? echo $leftLimit;?>);"
				<?php if($limit == 2){ echo 'style="float:left"'; }elseif($leftLimit < 0){ echo 'style="display:none;float:left;"'; }else{ echo 'style="float:left"';} ?> />
					<?php if($pageCount > 0){
						for($l=0; $l<$pageCount; $l++){?>
							<span <? if(($l*$limit) == $offset){
								echo 'class="page_active" ';
							}else{
								echo 'class="page_deactive" ';
							}
							if($l >= 5){
								echo 'style="display:none;" ';
							}?>
							onclick="pageScroll(<?php echo ($l*$limit); ?>)" ><?php echo ($l+1)?></span>
					<?php }
						if($l >= 5){ ?>
							<span><strong>.</strong></span>
							<span><strong>.</strong></span>
							<span><strong>.</strong></span>
					<? }
					}
					$rightLimit = $offset + $limit;
					if($rightLimit >= $totalCount){ $rightLimit = $totalCount; }else{ } ?>
					<img id="nextImages" src="images/next_icon.png" onclick="pageScroll(<?php echo $rightLimit;?>);"
					<?php if($rightLimit >= $totalCount){ echo 'style="margin-left:5px;display:none;"'; }else{ echo 'style="margin-left:5px;"'; }?> />
		</div><br clear="all" />
	</div>
</body>
</html>
	<?php }else{?>
		<div style="margin-left:10px;color:#000;"><i>No Record Found</i></div>
<?php }
}?>