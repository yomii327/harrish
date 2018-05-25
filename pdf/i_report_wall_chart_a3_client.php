<?php
session_start();

error_reporting(0);
set_time_limit(0);

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}else if(isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}else if(isset($_SESSION['ww_is_company'])){
	$owner_id = "company";
}
$d = '../report_pdf/'.$owner_id;
if (isset($_REQUEST["page_no"]) && $_REQUEST["page_no"] == 1){
	if(!is_dir($d)){
		mkdir($d,0777);
		chmod($d,0777);
	}else{
		rrmdir($d);
		mkdir($d,0777);
		chmod($d,0777);
	}
}

$page_no = $_REQUEST["page_no"];

$html_tds = array();

require_once'../includes/commanfunction.php';
$obj = new COMMAN_Class();
$owner_id = $_SESSION['ww_builder_id'];

$parent_location = array();
$sub_location_position = array();
if(isset($_REQUEST['uniqueId'])){
	if(!empty($_REQUEST['location'])){
		$where .= " and pm.location_id = '".$_REQUEST['location']."'";
		$wherePM .= " and pm.location_id = '".$_REQUEST['location']."'";
	}
	
	if(!empty($_REQUEST['subLocation']) && empty($_REQUEST['subLocation_sub'])){
		$sublocations = $obj->subLocationsIdProgressMonitoring ($_REQUEST['subLocation'], ",");
		$where .= " and (pm.sub_location_id in (".$sublocations."))";
		$wherePM .= " and (pm.sub_location_id in (".$sublocations."))";
	}
	
	if (!empty ($_REQUEST['subLocation_sub'])){
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
		$row = $obj->selQRY('pr_num_sublocations', 'user_projects','project_id = "'.$_REQUEST['projName'].'"');
		$pr_num_sublocations = $row["pr_num_sublocations"];
	
//Fetch all the data here and create ploating array
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
		//To deside the location count and width and font size on page...
		$locationCount = sizeof($querySubLoc);
		if($locationCount >= 17){
			$col = 17;
			if ($locationCount == 17)
			{
				$col = 18;
			}
			$colSize = 17;
			$fontSize = 5;
			$bHeight = 2;
			$best_height=10;
			$widthArray = array(31, 15);//status date locations
		}
		else if($locationCount == 16){
			$col = 17;
			$fontSize = 6;
			$bHeight = 3;
			$best_height=14;
			$widthArray = array(32, 15.8);//status date locations
		}
		else if($locationCount == 15){
			$col = 16;
			$colSize = 15;
			$fontSize = 6;
			$bHeight = 3;
			$best_height=14;
			$widthArray = array(36, 16.5);//status date locations
		}
		else if($locationCount == 14){
			$col = 14;
			$fontSize = 7;
			$bHeight = 2.7;
			$best_height=10;
			$widthArray = array(42, 17);//status date locations
		}
		else if($locationCount == 13){
			$col = 14;
			$fontSize = 7;
			$bHeight = 2.7;
			$best_height=10;
			$widthArray = array(45, 18);//status date locations
		}
		else if($locationCount == 12){
			$col = 13;
			$fontSize = 7;
			$bHeight = 3.0;
			$best_height=15;
			$widthArray = array(48, 19.5);//status date locations
		}
		else if($locationCount == 11){
			$col = 12;
			$fontSize = 7;
			$bHeight = 3.0;
			$best_height=15;
			$widthArray = array(49, 21);//status date locations
		}
		else if($locationCount == 10){
			$col = 11;
			$fontSize = 7;
			$bHeight = 3.0;
			$best_height=15;
			$widthArray = array(49, 23);//status date locations
		}
		else if($locationCount == 9){
			$col = 10;
			$fontSize = 7;
			$bHeight = 3.0;
			$best_height=15;
			$widthArray = array(53, 25);//status date locations
		}
		else if($locationCount == 8){
			$col = 9;
			$fontSize = 7;
			$bHeight = 3.0;
			$best_height=15;
			$widthArray = array(58, 28);//status date locations
		}
		else if($locationCount <= 7){
			$col = 8;
			$fontSize = 7;
			$bHeight = 3.0;
			$best_height=15;
			$widthArray = array(63, 31);//status date locations
		}

		$maxColumnPDF = count($querySubLoc);
		$colCount = $maxColumnPDF;
		$pageCount = 1;
		if($maxColumnPDF > $col){
			$pageCount = ceil($maxColumnPDF/$col);
			$colCount = $col;			
		}
//Locatin id string fetch here
 		$locationIds = join(', ', array_keys($locIds));
//Location title Data 
		$locationArray = array();
		$queryLocTitle = $obj->selQRYMultiple('location_id, location_title', 'project_monitoring_locations', 'location_id IN ('.$locationIds.') and is_deleted = 0 AND project_id = "'.$_REQUEST['projName'].'" order by location_id');
		foreach($queryLocTitle as $locTitle){
			$locationArray[$locTitle['location_id']] = $locTitle['location_title'];
		}
		
//--------------------------------------Location Array Creation End Here ----------------------------------------//		
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

#echo $fontSize.'<br />'.$bHeight.'<br />'.$best_height.'<br />'.$locWidth;die;
$taskIdsArr = array_keys($taskTitleArray);
//--------------------------------------TaskData Array End Here ----------------------------------------//	
//--------------------------------------Write PDF Start Here ----------------------------------------//		
require('../fpdf/mc_table.php');
//Config Section
		class PDF extends PDF_MC_Table{
			function sublocation($numSubLoc, $colCount, $locArray, $locTitleArr, $subLoc, $widthArr, $fontSize, $best_height){
				$this->locDepth = $numSubLoc;
				$this->colCount = $colCount;
				$this->locArr = $locArray;
				$this->locTitleArray = $locTitleArr;
				$this->subLocPos = $subLoc;
				$this->widthArr = $widthArr;
				$this->fontSize = $fontSize;
				$this->best_height = $best_height;
			}
			
			function Header(){
				if($this->PageNo()!=1){
					$this->Cell(0, 4, $this->PageNo()." of ".' {nb}', 0, 0, 'L');
					$this->ln();	
					$this->SetFont('times', 'B', $this->fontSize);

					$locHeader = array(date('d/M/Y'));
					for($i=0; $i<$this->colCount; $i++){
						array_push($locHeader, $this->locTitleArray[$this->locArr[$i]['location_id']]);
					}
					
					if($this->locDepth == 2){
						$header = array('');
						for($i=0; $i<$this->colCount; $i++){
							array_push($header, $this->locTitleArray[$this->locArr[$i]['sub_location1']]);
						}
					}
					if($this->locDepth == 3){
						$header = array('');
						for($i=0; $i<$this->colCount; $i++){
							array_push($header, $this->locTitleArray[$this->locArr[$i]['sub_location1']]);
						}
						$header1 = array('');
						for($i=0; $i<$this->colCount; $i++){
							array_push($header1, $this->locTitleArray[$this->locArr[$i]['sub_location2']]);
						}
					}
					if($this->locDepth == 4){
						$header = array('');
						for($i=0; $i<$this->colCount; $i++){
							array_push($header, $this->locTitleArray[$this->locArr[$i]['sub_location1']]);
						}
						$header1 = array('');
						for($i=0; $i<$this->colCount; $i++){
							array_push($header1, $this->locTitleArray[$this->locArr[$i]['sub_location2']]);
						}
						$header2 = array('');
						for($i=0; $i<$this->colCount; $i++){
							array_push($header2, $this->locTitleArray[$this->locArr[$i]['sub_location3']]);
						}
					}
					
					$subLocHeader = array('TASK');
					for($i=0; $i<$this->colCount; $i++){
						array_push($subLocHeader, $this->locTitleArray[$this->locArr[$i][$this->subLocPos]]);
					}
					$w = $this->header_width($this->widthArr, $this->colCount);
					$this->SetWidths($w);
										
					$this->row($locHeader, $this->best_height);//Location Header
					
					if($this->locDepth == 2){
						$this->row($header, $this->best_height);
					}
					if($this->locDepth == 3){
						$this->row($header, $this->best_height);
						$this->row($header1, $this->best_height);
					}
					if($this->locDepth == 4){
						$this->row($header, $this->best_height);
						$this->row($header1, $this->best_height);
						$this->row($header2, $this->best_height);
					}
					$this->row($subLocHeader, $this->best_height);//Sub Location Header
				}
			}
	
			function Footer(){
				$this->SetY(-15);
				$this->SetFont('times','B', $this->fontSize);
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

//Total width 286
/*-------------------- Desiding parameter for next paln start here ---------------
Smallest Size for fontSize=5; height=2; best_height=10; $widthArray = array(20, 12, 12, 12, 12, 12, count of locations);

Largest Size for fontSize=10; height=7; best_height=15; $widthArray = array(25, 17, 17, 17, 17, 17, count of locations);basically depends on locatoin count..
/*-------------------- Desiding parameter for next paln end here ---------------*/
		$pdf = new PDF("P", "mm", "A3");
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->sublocation($pr_num_sublocations, $colCount, $reIndexLoc, $locationArray, $subloc_id, $widthArray, $fontSize, $best_height);
//Report Name Start Here
		$pdf->SetFont('times', 'B', $fontSize+2);
		$pdf->Cell(285, 3, 'Wall Chart Report', 0, 0, 'C');	
		$pdf->Ln(5);
//Report Name End Here
		/*$pdf->SetFont('times', 'B', $fontSize);
		$pdf->Cell(31, 3, '', 0, 0, 'C');
		$pdf->Cell(28, 3, 'Behind', 0, 0, 'C');
		
		$pdf->SetFillColor(255, 00, 00);
		$pdf->Cell(5, 3, '', 0, 0, 'C', true);
		$pdf->Cell(23, 3, '', 0, 0, 'C');
	
		$pdf->Cell(28, 3, 'In progress', 0, 0, 'C');*/
		
		$pdf->SetFillColor(255, 165, 00);	
		$pdf->Cell(5, 3, '', 0, 0, 'C', true);
		$pdf->Cell(23, 3, '', 0, 0, 'C');
		
		$pdf->Cell(28, 3, 'Complete', 0, 0, 'C');
		
		$pdf->SetFillColor(00, 128, 00);	
		$pdf->Cell(5, 3, '', 0, 0, 'C', true);
		$pdf->Cell(23, 3, '', 0, 0, 'C');
	
		$pdf->Cell(28, 5, 'Signed Off', 0, 0, 'C');
		
		$pdf->SetFillColor(00, 00, 255);	
		$pdf->Cell(5, 3, '', 0, 0, 'C', true);
		$pdf->Cell(23, 3, '', 0, 0, 'C');
		$pdf->Ln(4);
//Count location name for headers
		$pdf->SetFont('times', 'B', $fontSize);
		$w = $pdf->header_width($widthArray, $colCount);
		$pdf->SetWidths($w);
		if(!empty($querySubLoc)){
			$i = 0;
			$pagecount = 0;
			$tot_loc_count = 1;
			$pos = 0;
			$sub_location_position[0] = array();
			
			$hLocVal = array();
			$hSubLocVal = array();
			if ($pr_num_sublocations == 2){
				$hSubLocVal1 = array();
			}
			if ($pr_num_sublocations == 3){
				$hSubLocVal1 = array();
				$hSubLocVal2 = array();
			}
			if ($pr_num_sublocations == 4){
				$hSubLocVal1 = array();
				$hSubLocVal2 = array();
				$hSubLocVal3 = array();
			}
			$hLocVal = array_merge($hLocVal, array(date('d/M/Y')));
			$hSubLocVal = array_merge($hSubLocVal, array('TASK'));
			if ($pr_num_sublocations == 2){
				$hSubLocVal1 = array_merge($hSubLocVal1, array(''));
			}
			if ($pr_num_sublocations == 3){
				$hSubLocVal1 = array_merge($hSubLocVal1, array(''));
				$hSubLocVal2 = array_merge($hSubLocVal2, array(''));
			}
			if ($pr_num_sublocations == 4){
				$hSubLocVal1 = array_merge($hSubLocVal1, array(''));
				$hSubLocVal2 = array_merge($hSubLocVal2, array(''));
				$hSubLocVal3 = array_merge($hSubLocVal3, array(''));
			}
			foreach($querySubLoc as $subLoc){
				$tot_loc_count++;
				if ($i == $col){//New Page horizontal
					$i = 0;
					$html_location[$pagecount] = $hLocVal;
					$html_sub_location[$pagecount] = $hSubLocVal;
					if ($pr_num_sublocations == 2){
						$html_sub_location1[ $pagecount] = $hSubLocVal1;
					}
					if ($pr_num_sublocations == 3){
						$html_sub_location1[ $pagecount] = $hSubLocVal1;
						$html_sub_location2[ $pagecount] = $hSubLocVal2;
					}
					if ($pr_num_sublocations == 4){
						$html_sub_location1[ $pagecount] = $hSubLocVal1;
						$html_sub_location2[ $pagecount] = $hSubLocVal2;
						$html_sub_location3[ $pagecount] = $hSubLocVal3;
					}
					///redefinign all arrays
					$hLocVal = array();
					$hSubLocVal = array();
					if ($pr_num_sublocations == 2){
						$hSubLocVal1 = array();
					}
					if ($pr_num_sublocations == 3){
						$hSubLocVal1 = array();
						$hSubLocVal2 = array();
					}
					if ($pr_num_sublocations == 4){
						$hSubLocVal1 = array();
						$hSubLocVal2 = array();
						$hSubLocVal3 = array();
					}
					$hLocVal = array_merge($hLocVal, array(date('d/M/Y')));
					$hSubLocVal = array_merge($hSubLocVal, array('TASK', ''));
					if ($pr_num_sublocations == 2){
						$hSubLocVal1 = array_merge($hSubLocVal1, array(''));
					}
					if ($pr_num_sublocations == 3){
						$hSubLocVal1 = array_merge($hSubLocVal1, array(''));
						$hSubLocVal2 = array_merge($hSubLocVal2, array(''));
					}
					if ($pr_num_sublocations == 4){
						$hSubLocVal1 = array_merge($hSubLocVal1, array(''));
						$hSubLocVal2 = array_merge($hSubLocVal2, array(''));
						$hSubLocVal3 = array_merge($hSubLocVal3, array(''));
					}

					$pagecount++;
					$sub_location_position[$pagecount] = array();
					$pos = 0;
				}
				
				for($g=1; $g<=$pr_num_sublocations; $g++){
					$subloc_id_id = $subLoc['sub_location'.$g];
				}
				array_push($hLocVal, $locationArray[$subLoc['location_id']]);
				array_push($hSubLocVal, $locationArray[$subloc_id_id]);
				
				$sub_location_position[$pagecount][$pos] = $subLoc['sub_location1'];
				
				if ($pr_num_sublocations == 2){
					array_push($hSubLocVal1, $locationArray[$subLoc['sub_location1']]);
					$sub_location_position[$pagecount][$pos] = $subLoc['sub_location2'];
				}
				if ($pr_num_sublocations == 3){
					array_push($hSubLocVal1, $locationArray[$subLoc['sub_location1']]);
					array_push($hSubLocVal2, $locationArray[$subLoc['sub_location2']]);
					$sub_location_position[$pagecount][$pos] = $subLoc['sub_location3'];
				}
				if ($pr_num_sublocations == 4){
					array_push($hSubLocVal1, $locationArray[$subLoc['sub_location1']]);
					array_push($hSubLocVal2, $locationArray[$subLoc['sub_location2']]);
					array_push($hSubLocVal3, $locationArray[$subLoc['sub_location3']]);
					$sub_location_position[$pagecount][$pos] = $subLoc['sub_location4'];
				}
				$i++;
				$pos++;
			}
			if (intval($i/$col) != ($i/$col)){//New Page horizontal
				$html_location[$pagecount] = $hLocVal;
				$html_sub_location[$pagecount] = $hSubLocVal;
				if ($pr_num_sublocations == 2){
					$html_sub_location1[ $pagecount] = $hSubLocVal1;
				}
				if ($pr_num_sublocations == 3){
					$html_sub_location1[ $pagecount] = $hSubLocVal1;
					$html_sub_location2[ $pagecount] = $hSubLocVal2;
				}
				if ($pr_num_sublocations == 4){
					$html_sub_location1[ $pagecount] = $hSubLocVal1;
					$html_sub_location2[ $pagecount] = $hSubLocVal2;
					$html_sub_location3[ $pagecount] = $hSubLocVal3;
				}
			}
//Put $row to array
		}
	//echo '<pre>';print_r($html_sub_location1[1]);die;
			//	print_r ($html_sub_location1[0]);
		$zipFileArray = array();
		for ($ik=0; $ik<=$pagecount; $ik++){
			//print_r ($html_location[$ik]);
			$pdf->Row_Wall_Chart_Summary($html_location[$ik], $bHeight);
			//echo $pr_num_sublocations;
			if ($pr_num_sublocations == 2){
				$pdf->Row_Wall_Chart_Summary($html_sub_location1[$ik], $bHeight);
			}
			if ($pr_num_sublocations == 3){
				$pdf->Row_Wall_Chart_Summary($html_sub_location1[$ik], $bHeight);				
				$pdf->Row_Wall_Chart_Summary($html_sub_location2[$ik], $bHeight);
			}
			if ($pr_num_sublocations == 4){
				$pdf->Row_Wall_Chart_Summary($html_sub_location1[$ik], $bHeight);				
				$pdf->Row_Wall_Chart_Summary($html_sub_location2[$ik], $bHeight);				
				$pdf->Row_Wall_Chart_Summary($html_sub_location3[$ik], $bHeight);								
			}
			$pdf->Row_Wall_Chart_Summary($html_sub_location[$ik], $bHeight);
			$pdf->SetFont('times', '', $fontSize);
			
			foreach($queryTask as $tasks){
				$hTaskVal = array();
				$task = $tasks["task"];
				$holding_point = $tasks["holding_point"];
				$queryIssuedToData = $obj->selQRYMultiple('issued_to_name', 'issued_to_for_progress_monitoring', 'progress_id = "'.$tasks['progress_id'].'" AND is_deleted = 0 order by issued_to_name');
				$issue_to_name = "";
				foreach($queryIssuedToData as $issue_to){
					if ($issue_to_name=="")
						$issue_to_name = $issue_to["issued_to_name"];
					else
						$issue_to_name .= ", ".$issue_to["issued_to_name"];
				}
				if ($holding_point == "Yes" || $holding_point == "yes" || $holding_point == "YES")
				{
					$hpoint_prefix = "H##";
					$pdf->SetFillColor(204, 204, 204);
				}else{
					$hpoint_prefix = "";
					$pdf->SetFillColor(255, 255, 255);
				}
				array_push($hTaskVal, $hpoint_prefix . "$task\n$issue_to_name");
				
				//array_push($hTaskVal, $hpoint_prefix."");
				for ($j=0; $j < count($sub_location_position[$ik]); $j++){
					
					$value = $valueArray[$task][$sub_location_position[$ik][$j]];
					if (empty($value)){
						array_push($hTaskVal, $hpoint_prefix."\n");
						continue;
					}
					if ($value[2]=="95%" || $value[2] == "100%")
					{
						array_push($hTaskVal, $hpoint_prefix."STATUS##" .$value[3] . "~~" .$value[2]);
					}else{
						array_push($hTaskVal, $hpoint_prefix."\n");
					}
				}
				$pdf->Row_Wall_Chart_Summary($hTaskVal, $bHeight);
				//$taskHtml[$i] = $hTaskVal;
			}
			$file_name = 'Wall_Chart_Report'.$ik.'.pdf';
			$zipFileArray[] = '../report_pdf/'.$owner_id.'/'.$file_name;
			$d = '../report_pdf/'.$owner_id;
			if(!is_dir($d))
				mkdir($d);
			if (file_exists($d.'/'.$file_name))
				unlink($d.'/'.$file_name);
			$tempFile = $d.'/'.$file_name;
			$pdf->Output($tempFile);
			
			for ($jj=0; $jj<($colCount); $jj++)
			{
				array_shift ($reIndexLoc);
			}
			$colCount = ($ik + 1) * $col;
			if (($maxColumnPDF - $col) >= $colCount)
			{
				$colCount = $col;
			}else{
				$colCount = $maxColumnPDF - $colCount;
			}
			$pdf = new PDF("P", "mm", "A3");
			$pdf->AliasNbPages();
			$pdf->AddPage();
			$pdf->sublocation($pr_num_sublocations, $colCount, $reIndexLoc, $locationArray, $subloc_id, $widthArray, $fontSize, $best_height);
			$pdf->SetFont('times', 'B', $fontSize);
			$w = $pdf->header_width($widthArray, $colCount);
			$pdf->SetWidths($w);
		}		
		$zipName = 'wall_chart_pdf'.microtime().'.zip';
		
		if($obj->create_zip($zipFileArray, '../report_pdf/'.$owner_id.'/'.$zipName))
		{
			echo '<br clear="all" /><div style="margin-left:10px;"><a onClick="closePopUp();" href="report_pdf/'.$owner_id.'/'.$zipName.'" target="_blank" class="view_btn"></a></div>';
		}
	//PDF Creattion Section Start Here
	}else{
		echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
	}
}
function rrmdir($dir){
	if (is_dir($dir)) {
		$objects = scandir($dir);
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
			}
		}
		reset($objects);
		rmdir($dir);
	}
}