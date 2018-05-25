<?php
ob_start();
session_start();

//Code for Calculate Execution Time
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$starttime = $mtime; 
//Code for Calculate Execution Time

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
	$projID = '';	$locationCL = '';	$subLocationCL = '';	$sub_subLocationCL = '';	$where = '';
	$totalCount = 0;
	$locArray = array();
	if(!empty($_REQUEST['projNameCL'])){
		$projID = $_REQUEST['projNameCL'];
		$projectName = $obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projNameCL'], 'project_name');
	}
	
	if(!empty($_REQUEST['locationCL'])){
		$locArray[] = $_REQUEST['locationCL'];
		$locationCL = $_REQUEST['locationCL'];
	}

	if(!empty($_REQUEST['subLocationCL'])){
		$locArray[] = $_REQUEST['subLocationCL'];
		$subLocationCL = $_REQUEST['subLocationCL'];
	}
	
	if(!empty($_REQUEST['sub_subLocationCL'])){
		$locArray[] = $_REQUEST['sub_subLocationCL'];
		$sub_subLocationCL = $_REQUEST['sub_subLocationCL'];
	}

	$locLupCount = sizeof($locArray);
	$locString = '';	$locStringSec = '';
	
	$locString = join(',', $locArray);

	if($locLupCount < 4 && $locLupCount == 3){//Till Sublocation 2 selected
		$locStringSec = $obj->subLocationsId($sub_subLocationCL, ', ');
		$searchSubLoc = $locStringSec;
		$where = ' AND lc.location_id IN ('.$searchSubLoc.')';
	}else if($locLupCount < 3 && $locLupCount == 2){//Till Sublocation 1 selected
		$locStringSec = $obj->subLocationsId($subLocationCL, ', ');
		$searchSubLoc = $locStringSec;
		$where = ' AND lc.location_id IN ('.$searchSubLoc.')';
	}else  if($locLupCount < 2 && $locLupCount == 1){//Till Root Location selected
		$locStringSec = $obj->subLocationsId($locationCL, ', ');	
		$searchSubLoc = $locStringSec;
		$where = ' AND lc.location_id IN ('.$searchSubLoc.')';
	}
	
//Code for find Location title array 	
	$locTitleArray = array();
	if($locString != ''){
		$locTArray = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'project_id = '.$projID.' AND location_id in ('.$locString.')');
		if(!empty($locTArray)){
			foreach($locTArray as $tArray){
				$locTitleArray[$tArray['location_id']] = $tArray['location_title'];
			}
		}
	}
//Main Query Part
$mainQuery = "SELECT
				lc.location_check_list_id,
				cl.check_list_items_name,
				lc.check_list_items_status,
				lc.location_id,
				loc.location_title
			FROM
				project_locations AS loc,
				check_list_items AS cl,
				location_check_list AS lc
			WHERE
				loc.location_id = lc.location_id AND
				lc.check_list_items_id = cl.check_list_items_id AND
				lc.project_id = ".$projID." AND
				loc.is_deleted = 0 AND
				cl.is_deleted = 0 AND
				lc.is_deleted = 0".$where;
				
$rs = mysql_query($mainQuery);
	
$noInspection  = mysql_num_rows($rs);
$noPages = ceil(($noInspection-1)/2 +1);
	
	require('../fpdf/mc_table.php');
//Config Section
	class PDF extends PDF_MC_Table{
		var $d_location;
		function Header(){
			if($this->PageNo()!=1){
				$this->Cell(0, 10, $this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
				$this->ln();	
				$this->SetFont('times', 'B', 12);
				$header = array('Checklist Name', 'Status');
				$w = $this->header_width();
				$this->SetWidths($w);		
				$best_height = 17;
				$this->row($header, $best_height);
			}
		}
		
		function Footer(){
			$this->SetY(-15);
			$this->SetFont('times','B',10);
			$this->Cell(0, 10, "DefectID - Copyright Wiseworking 2012 / 2013", 0, 0, 'C');
		}
		
		function header_width(){
			$arr = array(150, 49);
			return $arr;
		}
	}
	
	if($noInspection > 0){
		$pdf = new PDF("P", "mm", "A4");
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		$pdf->SetTopMargin(20);
	
		$pdf->Image('../company_logo/logo.png', 150, 5, 'png', -100);
		$pdf->Ln(8);
		
		$pdf->SetFont('times', 'BU', 12);
		$pdf->Cell(40, 10, 'Checklist Report');		
		$pdf->Ln(6);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(24, 10, 'Project Name : ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(30, 10, $projectName);	
		$pdf->Ln(5);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(10, 10, 'Date : ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(30, 10, date('d/m/Y'));	
		$pdf->Ln(5);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(27, 10, 'Checklist Items : ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(30, 10, $noInspection);	
		$pdf->Ln(5);	
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(10, 10, 'Page : ');		
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(30, 10, '1 of '.'{nb}');		
		$pdf->Ln(10);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(45, 10, 'Report Filtered by :');	
		$pdf->Ln(8);
		$jk=0;
		
		$pdf->Cell(15, 10, '');	
		
		$jk=0;	
		if(!empty($_REQUEST['locationCL']) && empty($_REQUEST['subLocationCL'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Location Name: ');	
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $locTitleArray[$locationCL]);	
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
		}
		
		if(!empty($_REQUEST['subLocationCL']) && !empty($_REQUEST['sub_subLocationCL'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Location Name: ');		
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $locTitleArray[$locationCL]);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
	
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Sub Location: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $locTitleArray[$subLocationCL]);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
			
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Sub Location 1: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $locTitleArray[$sub_subLocationCL]);
				
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
		}else{
			if(!empty($_REQUEST['locationCL']) && !empty($_REQUEST['subLocationCL'])){
				$pdf->SetFont('times', 'B', 11);		
				$pdf->Cell(50, 10, 'Location Name: ');
				$pdf->SetFont('times', '', 10);
				$pdf->Cell(25, 10, $locTitleArray[$locationCL]);
				
				$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
							
				$pdf->SetFont('times', 'B', 11);		
				$pdf->Cell(50, 10, 'Sub Location: ');
				$pdf->SetFont('times', '', 10);
				$pdf->Cell(25, 10, $locTitleArray[$subLocationCL]);
				
				$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
			}
		}
		$pdf->ln(10);
		
		#$pdf->Cell(199, 10, 'Garuav sharma', 1, 'C');	

	//Top Header Sectiond End Here	
		$pdf->SetFont('times', 'B', 12);
		$header = array('Checklist Items Name', 'Status');
		$w = $pdf->header_width();
		$pdf->SetWidths($w);		
		$best_height = 17;
		$pdf->row($header, $best_height);
		
		$currLocID = '';
		$location = '';

		while($row = mysql_fetch_assoc($rs)){
			if($currLocID != $row['location_id']){
				$pdf->SetFont('times', 'B', 10);
				$location = $obj->subLocations($row['location_id'], ' > ');
				$pdf->Cell(199, 5, $location, '1');	
				$pdf->ln();
			}
			$pdf->SetFont('times', '', 9);
			$dataArr = array($row['check_list_items_name'], $row['check_list_items_status']);
			$pdf->Row($dataArr);
			$currLocID = $row['location_id'];
		}
		
		$file_name = 'Checklist_Report_'.microtime().'.pdf';
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
		//PDF Creattion Section Start Here
		echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="closePopUp();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';
	}else{
		echo "No Record Found !";
	}
} ?>