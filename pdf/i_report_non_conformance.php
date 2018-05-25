<?php
ob_start();
session_start();
//error_reporting(E_ALL);
//ini_set('display_errors',  '1');
include('../includes/commanfunction.php');
$obj= new COMMAN_Class();
if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}
if(isset($_REQUEST['name'])){
	$postCount = 0;
	$report_type = $_REQUEST['report_type'];
	$images_path = "photo_detail/";
	$dimages_path = "drawing_detail/";
	
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
		$locStringSec = $obj->subLocationsDepthQA($subLocationQA2,  ',  ');
		$searchSubLoc = $locStringSec;
	}else if($locLupCount < 3 && $locLupCount == 2){//Till Sublocation 1 selected
		$locStringSec = $obj->subLocationsDepthQA($subLocationQA1,  ',  ');
		$searchSubLoc = $locStringSec;
	}else  if($locLupCount < 2 && $locLupCount == 1){//Till Root Location selected
		$locStringSec = $obj->subLocationsDepthQA($locationQA,  ',  ');	
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
	$conformanceData = $obj->selQRYMultiple('t.task_id, t.comments, qn.non_conformance_id, qn.project_id, qn.location_id, qn.task_id, qn.qa_inspection_date_raised, qn.qa_inspection_raised_by, qn.qa_inspection_inspected_by, qn.qa_inspection_description, qn.qa_inspection_location',
	'qa_inspections as qn, qa_task_monitoring as t, qa_task_locations as loc',
	'qn.project_id = '.$projID.' AND qn.is_deleted = 0 AND qn.location_id IN ('.$searchSubLoc.') AND t.project_id = '.$projID.' AND t.is_deleted = 0 AND t.task_id = qn.task_id AND qn.location_id = loc.location_id AND loc.is_deleted = 0 ORDER BY qn.location_id');

	if(!empty($conformanceData)){
		$noInspection = count($conformanceData);	
	}else{
		$noInspection = 0;
	}
	$noPages = $noInspection;

	$ajaxReplay = $noInspection.' Records';
	if($noInspection > 0){
		require('../fpdf/fpdf.php');	
		
		class PDF extends FPDF{
			function Header(){// Page header
				if($this->PageNo()!=1){// Page number
					$this->Cell(0, 10, 'Page: '.$this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
					$this->ln();	
				}
			}
			function Footer(){// Position at 1.5 cm from bottom
				$this->SetY(-15);
				$this->SetFont('helvetica', 'B', 10);
				$this->Cell(10);
				$this->Cell(15, 4, 'DefectID,  part of the Wiseworker Quality Management Ecosystem,  helping the construction industry.', 0, 0);
				$this->Ln(5);
				$this->Cell(76);
				$this->Cell(60, 4, 'www.wiseworker.net', 0, 0);
			}
		}
		$pdf = new PDF();
		$pdf->AddPage();
		$pdf->AliasNbPages();
	
		$pdf->Image('../company_logo/logo.png',  150,  12,  -100);
		$pdf->Ln(8);
	
		$pdf->SetFont('times', 'BU', 12);
		$pdf->Cell(40, 10, 'Non Conformance Report');		
		$pdf->Ln(6);
	
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(26, 10, 'Project Name : ');	
	
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(10, 10, $projectName);	
		$pdf->Ln(5);
	
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(11, 10, 'Date : ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(20, 10, date('d/m/Y'));	
		$pdf->Ln(5);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(23, 10, 'Inspections : ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(20, 10, $noInspection);	
		$pdf->Ln(5);	
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(11, 10, 'Page : ');		
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(8, 10, '1 of '.'{nb}');		
		$pdf->Ln(10);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(25, 10, 'Report Filtered by :');	
		$pdf->Ln(8);
		$jk=0;

		$pdf->Cell(15, 10, '');	
		
		$x0 = $x = $pdf->GetX();
		$y = $pdf->GetY();
		
		if($locationQA != ''){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(30, 5, 'Location Name: ');	
			$pdf->SetFont('times', '', 10);
			$pdf->MultiCell(50, 5, $locArrayData[$locationQA], 0, 'L');
		}
		if($subLocationQA1 != ''){
			$pdf->SetXY($x+80, $y);
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(30, 5, 'Sub Location1 : ');	
			$pdf->SetFont('times', '', 10);
			$pdf->MultiCell(50, 5, $locArrayData[$subLocationQA1], 0, 'L');
			$pdf->ln(5);$pdf->Cell(15, 10, '');
		}
		$x = $pdf->GetX();
		$y = $pdf->GetY();
		if($subLocationQA2 != ''){
			$pdf->SetXY($x0, $y+5);
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(30, 5, 'Sub Location2 : ');	
			$pdf->SetFont('times', '', 10);
			$pdf->MultiCell(50, 5, $locArrayData[$subLocationQA2], 0, 'L');
		}
		if($subLocationQA3 != ''){
			$pdf->SetXY($x+80, $y+5);
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(30, 5, 'Sub Location3 : ');	
			$pdf->SetFont('times', '', 10);
			$pdf->MultiCell(50, 5, $locArrayData[$subLocationQA3], 0, 'L');
		}
		$pdf->ln();
		$first_time = 1;
		$i = 1;
		$pageCount = 1;
		$page_break = 1;
		
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
			if($first_time > 1){
				$pdf->AddPage();
			}	
			if($first_time == 1){
				$pdf->SetFont('Times', 'B', 10);
				$pdf->Cell(2);
				$pdf->Cell(15, 5, $pdf->PageNo().".", 0, 0);
				$pdf->Ln(5);	
				$pdf->SetFont('Times', 'BI', 10);
				// $pdf->SetFillColor(230, 230, 230); 
				$pdf->SetFillColor(190, 190, 190);
				$pdf->Cell(3);
			}else{
				$pdf->SetFont('Times', 'B', 10);		
				$pdf->Cell(2);
				$pdf->Cell(15, 5, $pdf->PageNo().".", 0, 0);
				$pdf->Ln(5);	
				//$pdf->SetY(0); //reset the Y to the original,  since we moved it down to write INVOICE
				$pdf->SetFont('Times', 'BI', 10);
				$pdf->SetFillColor(190, 190, 190); //Set background of the cell to be that grey color
				$pdf->Cell(3);
			}			
			$fill = true;
			$i = 0;
			$x0 = $x = $pdf->GetX();
			$y = $pdf->GetY();

			$yH = 5; //height of the row
//Location Title			
			$locString = stripslashes($conData['qa_inspection_location']);
			$pdf->SetXY($x, $y);
			$rCount = ceil(strlen($locString)/40)+1;
			if(strlen($locString) > 40){
				$pdf->Cell(35, $rCount*$yH, "", 'LRB', 2, '', $fill);
			}else{
				$pdf->Cell(35, $yH, "", 'LRB', 2, '', $fill);
			}
			$pdf->SetXY($x,  $y);
			if(strlen($locString) > 40){
				$pdf->MultiCell(35, $rCount*$yH, 'Location', 1, 'L');
			}else{
				$pdf->MultiCell(35, 5, 'Location', 1, 'L');
			}
//Location Value
			$pdf->SetFont('Times', 'B', 9);	
			$pdf->SetXY($x+35, $y);
			$pdf->MultiCell(67, 5, $locString, "TR", 'L');
//Description Title
			$pdf->SetFont('Times', 'BI', 10);
			$x = $x+35;
			$pdf->SetXY($x+67, $y);
			$pdf->Cell(93,  $yH,  "",  'LRB', 0, '', $fill);
			$pdf->SetXY($x + 67,  $y);
			$pdf->MultiCell(93, 5, 'Description ', 1, 'L');
//Date Raised Title
			if(strlen($locString) > 40){
				$y = $y+$rCount*$yH;
			}else{
				$y = $y+$yH;
			}
			$pdf->SetXY($x0, $y);
			$pdf->SetFont('Times', 'BI', 10);
			$pdf->SetFillColor(190, 190, 190);
			$pdf->Cell(35, 5, 'Date Raised',  1, 0, '', true); 
//Date Raised Value
			$pdf->SetFillColor(255, 255, 255); 
			$pdf->SetFont('Times', 'B', 9);
			$date_raised  = '';
			if($conData['qa_inspection_date_raised'] != '0000-00-00'){
				$date_raised = stripslashes(date("d/m/Y",  strtotime($conData['qa_inspection_date_raised'])));
			}
			$pdf->Cell(67, 5, $date_raised, 1, 0, '', true);
//Description Value
			if(strlen($locString) > 40){
				$y = $y-(($rCount-1)*$yH);
			}else{
				$y = $y+$yH;
			}
			$pdf->SetXY($x+67,  $y);
			if(strlen($locString) > 40){
				$pdf->MultiCell(93, ($rCount)*$yH, '', "R", 'LT');
			}else{
				$pdf->MultiCell(93, 15, '', "R", 'LT');
			}
			$pdf->SetXY($x+67,  $y);
			$pdf->MultiCell(93, 5, stripslashes(wordwrap($conData['qa_inspection_description'], 60, '\n')), "R", 'LT');
//Inspected By Title
			$y = $y+$yH;
			$pdf->SetXY($x0, $y);
			$pdf->SetFont('Times', 'BI', 10);
			$pdf->SetFillColor(190, 190, 190);
			$pdf->Cell(35, 5, 'Inspected By', 1, 0, '', true);
//Inspected By Value
			$pdf->SetFillColor(255, 255, 255); 
			$pdf->SetFont('Times', 'B', 8);
			$pdf->Cell(67, 5, stripslashes($conData['qa_inspection_inspected_by']), 1, 0, '', true);
//Raised By Title
			$y = $y+$yH;
			$pdf->SetXY($x0, $y);
			$pdf->SetFont('Times', 'BI', 10);
			$pdf->SetFillColor(190, 190, 190);
			$pdf->Cell(35, 5, 'Raised By', 1, 0, '', true);
//Raised By Value
			$pdf->SetFillColor(255, 255, 255); 
			$pdf->SetFont('Times', 'B', 8);
			$pdf->Cell(67, 5, stripslashes($conData['qa_inspection_raised_by']), 1, 0, '', true);
//Issued To Title
			$y = $y+$yH;
			$pdf->SetXY($x0, $y);
			$pdf->SetFont('Times', 'BI', 10);
			$pdf->SetFillColor(190, 190, 190);
			$pdf->Cell(35, 5, 'Issued To', 1, 0, '', true);
//Issued To Value
			$pdf->SetFillColor(255, 255, 255); 
			$pdf->SetFont('Times', 'B', 8);
			$pdf->Cell(67, 5, $issueToData_issueToName, 1, 0, '', true);
			
//Comments Title
			$pdf->SetFillColor(190, 190, 190);
			$pdf->SetFont('Times', 'BI', 10);
			$pdf->SetXY($x+67,  $y);
			$pdf->Cell(93,  $yH,  "",  'LRB', 0, '', true);
			$pdf->SetXY($x + 67,  $y);
			$pdf->MultiCell(93, 5, 'Comments ', 1, 'L');
			
//Fix by Date Title
			$y = $y+$yH;
			$pdf->SetXY($x0, $y);
			$pdf->SetFont('Times', 'BI', 10);
			$pdf->SetFillColor(190, 190, 190);
			$pdf->Cell(35, 5, 'Fix by Date', 1, 0, '', true);
//Fix by Date Value
			$pdf->SetFillColor(255, 255, 255); 
			$pdf->SetFont('Times', 'B', 8);
			$pdf->Cell(67, 5, $issueToData_fixedByDate, 1, 0, '', true);

//Comments Value
			$pdf->MultiCell(93, 15, '', "R", 'LT');
			$pdf->SetXY($x+67,  $y);
			$pdf->MultiCell(93, 5, stripslashes(wordwrap($conData['comments'], 60, '\n')), "R", 'LT');

//Cost Attribute Title
			$y = $y+$yH;
			$pdf->SetXY($x0, $y);
			$pdf->SetFont('Times', 'BI', 10);
			$pdf->SetFillColor(190, 190, 190);
			$pdf->Cell(35, 5, 'Cost Attribute', 1, 0, '', true);
//Cost Attribute Value
			$pdf->SetFillColor(255, 255, 255); 
			$pdf->SetFont('Times', 'B', 8);
			$pdf->Cell(67, 5, $issueToData_costAttribute, 1, 0, '', true);
//Status Title
			$y = $y+$yH;
			$pdf->SetXY($x0, $y);
			$pdf->SetFont('Times', 'BI', 10);
			$pdf->SetFillColor(190, 190, 190);
			$pdf->Cell(35, 5, 'Status', 1, 0, '', true);
//Status Value
			$pdf->SetFillColor(255, 255, 255); 
			$pdf->SetFont('Times', 'B', 8);
			$pdf->Cell(67, 5, $issueToData_status, 1, 0, '', true);
					
			$imageName = "";
			$drawingName = "";
			$y = $y+$yH;
			$pdf->SetXY($x0, $y);
			
			$images = $obj->selQRYMultiple('qa_graphic_name, qa_graphic_id', 'qa_graphics', 'non_conformance_id ='.$conData['non_conformance_id'].' AND project_id = '.$projID.' AND qa_graphic_type = "images" AND is_deleted = 0');

			if(isset($images[0]) && $images[0]['qa_graphic_name'] != ''){
				$obj->resizeImages('../inspections/photo/'.$images[0]['qa_graphic_name'], 320, 350, '../inspections/photo/photo_confom/'.$images[0]['qa_graphic_name']);
				if(file_exists('../inspections/photo/photo_confom/'.$images[0]['qa_graphic_name'])){
					$imageName = '../inspections/photo/photo_confom/'.$images[0]['qa_graphic_name'];
				}else{
					$imageName = '../inspections/photo/'.$images[0]['qa_graphic_name'];
				}
			}
			
			$drawing = $obj->selQRYMultiple('qa_graphic_name, qa_graphic_id', 'qa_graphics', 'non_conformance_id ='.$conData['non_conformance_id'].' AND project_id = '.$projID.' AND qa_graphic_type = "drawing" AND is_deleted = 0');
			
			if(isset($drawing[0]) && $drawing[0]['qa_graphic_name'] != ''){
				$obj->resizeImages('../inspections/drawing/'.$drawing[0]['qa_graphic_name'], 320, 350, '../inspections/drawing/drawing_confom/'.$drawing[0]['qa_graphic_name']);
				if(file_exists('../inspections/drawing/drawing_confom/'.$drawing[0]['qa_graphic_name'])){
					$drawingName = '../inspections/drawing/drawing_confom/'.$drawing[0]['qa_graphic_name'];
				}else{
					$drawingName = '../inspections/drawing/'.$drawing[0]['qa_graphic_name'];
				}
			}

			if($first_time==1){
				$pdf->SetFillColor(255, 255, 255); 			
				$pdf->Cell(102, 100, "", 1, 0, '', true);
				if($imageName != ""){
					$pdf->Image($imageName, 20, $pdf->GetY()+2);				
				}
				$pdf->Cell(93, 100, "", 1, 1, '', true);
				if($drawingName != ""){
					$pdf->Image($drawingName, 122, $pdf->GetY()-100+2);
				}
			}else{
				$pdf->SetFillColor(255, 255, 255); 			
				 $pdf->Cell(102, 100, "", 1, 0, '', true);
				if($imageName != ""){
					$pdf->Image($imageName, 20, $pdf->GetY()+2, -100);
				}
				$pdf->Cell(93, 100, "", 1, 1, '', true);
				if($drawingName != ""){
					$pdf->Image($drawingName, 120, $pdf->GetY()-100+2, -100);
				}
			}
			$i++; 
			$first_time++;
			$page_break++;
		}
		$file_name = 'Non_Conformance_'.microtime().'.pdf';
		$d = '../report_pdf/'.$owner_id;
		if(!is_dir($d))
			mkdir($d);
		if (file_exists($d.'/'.$file_name))
			unlink($d.'/'.$file_name);
		$tempFile = $d.'/'.$file_name;
		$pdf->Output($tempFile);
		$fieSize = filesize($tempFile);
		$fieSize = floor($fieSize/(1024));
		if ($fieSize > 1024){
			$fieSize = floor($fieSize/(1024)) . "Mbs";
		}else{
			$fieSize .= "Kbs";
		}
		$rply = $ajaxReplay.' '.$fieSize;
		echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="closePopUp();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';

	}else{
		echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
	}
}
?>