<?php
ob_start();
session_start();
include('../includes/commanfunction.php');
$obj = new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}

if(isset($_REQUEST['name'])){
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

//Location Titles Array
	$locationTitles = $obj->selQRYMultiple('loc.location_id, loc.location_title', 'qa_task_locations AS loc', 'loc.project_id = '.$projID.' AND loc.is_deleted = 0');
	$lTitlesArray = array();
	foreach($locationTitles as $tData){
		$lTitlesArray[ $tData["location_id"]] = $tData["location_title"];
	}

//Last sub location array with order 
	$sublocIDs3 = $obj->selQRYMultiple('loc.location_id, loc.location_title, GROUP_CONCAT(distinct loc.location_id) AS locationIDs, cast(GROUP_CONCAT(distinct loc.location_parent_id) AS char) as parentLocIDs, task.subloc_order_wall_chart_report', 'qa_task_locations AS loc, qa_task_monitoring AS task', 'task.sub_location_id = loc.location_id AND loc.project_id = '.$projID.' AND task.is_deleted = 0 AND loc.is_deleted = 0 AND task.excluded_location = "NO" GROUP BY loc.location_title ORDER BY task.subloc_order_wall_chart_report');	
#	echo '<pre>';print_r($sublocIDs3);

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
				$statusArr[$tData['sub_location_id']] = '0,255,0';
			}else{
				$statusArr[$tData['sub_location_id']] = '255,255,255';
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
		if (! is_array ($locationTitleArr[ $l2Title]))
		{
			$locationTitleArr[ $l2Title] = array();
			$locationTitleArr[ $l2Title] [$l3Title] = array();
			$locationTitleArr[ $l2Title] [$l3Title] [$l4Title] = $statusArr[$lArray[3]];
		}else{
			if (! is_array ($locationTitleArr[ $l2Title] [$l3Title]))
			{
				$locationTitleArr[ $l2Title] [$l3Title] = array();
				$locationTitleArr[ $l2Title] [$l3Title] [$l4Title] = $statusArr[$lArray[3]];
			}else {
				$locationTitleArr[ $l2Title] [$l3Title] [$l4Title] = $statusArr[$lArray[3]];
			}
		}
	}	

	if($noInspection > 0){
		
		//To deside the location count and width and font size on page...
		$locHeadCount = sizeof($lastLocTitle);
		if($locHeadCount >= 17){
			$col = 17;
			if ($locHeadCount == 17){
				$col = 18;
			}
			$colSize = 17;
			$fontSize = 5;
			$bHeight = 2;
			$best_height=10;
			$widthArray = array(20, 14);//status date locations
		}else if($locHeadCount == 16){
			$col = 17;
			$fontSize = 6;
			$bHeight = 3;
			$best_height=14;
			$widthArray = array(56, 44);//status date locations
		}else if($locHeadCount == 15){
			$col = 16;
			$colSize = 15;
			$fontSize = 7;
			$bHeight = 4;
			$best_height=18;
			$widthArray = array(62, 48);//status date locations
		}else if($locHeadCount == 14){
			$col = 14;
			$fontSize = 8;
			$bHeight = 5;
			$best_height=22;
			$widthArray = array(68, 52);//status date locations
		}else if($locHeadCount == 13){
			$col = 14;
			$fontSize = 9;
			$bHeight = 6;
			$best_height=26;
			$widthArray = array(74, 56);//status date locations
		}else if($locHeadCount == 12){
			$col = 13;
			$fontSize = 10;
			$bHeight = 7;
			$best_height=30;
			$widthArray = array(80, 60);//status date locations
		}else if($locHeadCount == 11){
			$col = 12;
			$fontSize = 11;
			$bHeight = 8;
			$best_height=34;
			$widthArray = array(86, 64);//status date locations
		}else if($locHeadCount == 10){
			$col = 11;
			$fontSize = 12;
			$bHeight = 9;
			$best_height=38;
			$widthArray = array(92, 68);//status date locations
		}else if($locHeadCount == 9){
			$col = 10;
			$fontSize = 13;
			$bHeight = 10;
			$best_height=42;
			$widthArray = array(98, 72);//status date locations
		}else if($locHeadCount == 8){
			$col = 9;
			$fontSize = 14;
			$bHeight = 11;
			$best_height=46;
			$widthArray = array(104, 76);//status date locations
		}if($locHeadCount <= 7){
			$col = 8;
			$fontSize = 15;
			$bHeight = 12;
			$best_height=50;
			$widthArray = array(110, 80);//status date locations
		}
	}

	if($noInspection > 0){
//Start PDF Creation here
		require('../fpdf/mc_table.php');
//Config Section
		class PDF extends PDF_MC_Table{
			function sublocation($locHeadCount, $widthArray, $lastLocTitle, $fontSize, $best_height){
				$this->locHeadCount = $locHeadCount;
				$this->widthArray = $widthArray;
				$this->lastLocTitle = $lastLocTitle;
				$this->fontSize = $fontSize;
				$this->best_height = $best_height;
			}
			
			function Header(){
				if($this->PageNo()!=1){
					$this->Cell(0, 10, $this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
					$this->ln();	
					$this->SetFont('times', 'B', 10);

					$locHeader = array('');
					
					for($y=0;$y<$this->locHeadCount;$y++){
						array_push($locHeaderArr, $this->lastLocTitle[$y]);
					}
					$w = $this->header_width($this->widthArray, $this->locHeadCount);
					$this->SetWidths($w);		
					$this->row($locHeaderArr, $this->best_height);
				}
			}
	
			function Footer(){
				$this->SetY(-15);
				$this->SetFont('times','B',10);
				$this->Cell(0, 10, 'DefectID – Copyright Wiseworking 2012 / 2013', 0, 0, 'C');
			}
			
			function header_width($widthArr, $lupCount){
				$arr = array($widthArr[0]);
				for($i=0; $i<$lupCount; $i++){
					array_push($arr, $widthArr[1]);
				}
				return $arr;
			}
		}
//Total width 2382
//Total height 3349
		$pdf = new PDF("P", "mm", "A0");
		$pdf->AliasNbPages();
		$pdf->AddPage();
//285
//Report Name Start Here
		$pdf->SetTopMargin(20);
	
		$pdf->Image('../company_logo/logo.png', 750, 5, 'png', -100);
		$pdf->Ln(8);
		
		$pdf->SetFont('times', 'BU', 12);
		$pdf->Cell(40, 10, 'Quality Assurance Wall Chart Report');		
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
		
		$pdf->Ln(15);		

		$locHeaderArr = array('');
		
		$pdf->SetFont('times', 'B', $fontSize);
		$lastLocTitle = array_values($lastLocTitle);
		$pdf->sublocation($locHeadCount, $widthArray, $lastLocTitle, $fontSize, $best_height);

		for($y=0;$y<$locHeadCount;$y++){
			array_push($locHeaderArr, $lastLocTitle[$y]);
		}
		
		$w = $pdf->header_width($widthArray, $locHeadCount);
		$pdf->SetWidths($w);		
#		$best_height = 17;
		$pdf->row($locHeaderArr, $best_height);
		$pdf->SetFont('times', '', $fontSize);
		
		$horLupCount = sizeof($lastLocTitle);
		ksort($locationTitleArr);
		$locationTitleArr = array_reverse($locationTitleArr);
		foreach ($locationTitleArr as $key=>$subLocationArray){
			
#			$toprow = $pdf->MultiCell($widthArray[0], 5, $key, 1);
			$toprow = $key;
#			$pdf->SetXY($x+$widthArray[0], $y);
#			$toprow .= $pdf->Cell($widthArray[1]*$locHeadCount, 5, '', 1, 1);
			
			ksort($subLocationArray);
			$toprowFlag = false;
			
			$secondRowArr = array();
			
			foreach ($subLocationArray as $key=>$value){
				$locValArr = array();
				array_push($locValArr, 'H##'.$key);
				$flag = false;
				for($y=0;$y<$horLupCount;$y++){
					if (array_key_exists ($lastLocTitle[$y], $value))
								$flag = true;
					if (empty ($value[$lastLocTitle[$y]])){
						array_push($locValArr, "FILL##255,255,255~~");
					}else{
						array_push($locValArr, "FILL##".$value[$lastLocTitle[$y]]."~~");
					}
				}
				if ($flag){
					$toprowFlag = true;
					$secondRowArr[] = $locValArr;
				}
			}
			if ($toprowFlag){
				$x = $pdf->GetX();
				$y = $pdf->GetY();
				$pdf->MultiCell($widthArray[0], 5, $toprow, 1);
				$pdf->SetXY($x+$widthArray[0], $y);
				$pdf->Cell($widthArray[1]*$locHeadCount, 5, '', 1, 1);
				for($m=0;$m<sizeof($secondRowArr);$m++){
					$pdf->Row_QA_Wall_Chart($secondRowArr[$m]);
				}
			}
		}
		$file_name = 'QA_Wall_Chart_Report'.microtime().'.pdf';
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
		$rply = 'File Size'.$fieSize;
	//PDF Creattion Section Start Here
		echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="closePopUp();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';
	}else{
		echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
	}
#echo '<pre>';print_r($locCheckExist);die;
}else{
	$html = 'No Record Found !';
}?>