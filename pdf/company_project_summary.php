<?php
ob_start();
ini_set('display_errors',1);

error_reporting(E_ALL);

session_start();
set_time_limit(6000000000000000000);

include('../includes/commanfunction.php');
$obj= new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}
	$limit = 19;
	$issued_to_add = '';
	if(!isset($_REQUEST['startWith'])){
		$offset = 0;
	}
	if($_REQUEST['startWith'] == 0){
		$offset = 0;
	}else{
		$offset = $_REQUEST['startWith'];
		$offsetPage = ceil($offset/2);
	}
	$postCount = 0;

if (isset($_REQUEST["name"])){
	$where = '';
	if(!empty($_REQUEST['user']) && $_REQUEST['user'] != 0){
		$where.=" AND up.user_id = ".$_REQUEST['user'];
	}
	if(!empty($_REQUEST['projStatusComp']) && isset($_REQUEST['projStatusComp'])){
		$where.=" AND up.is_deleted = ".$_REQUEST['projStatusComp'];
	}

	if($_REQUEST['projName'] != 0){
		$projName = $obj->selQRYMultiple('up.project_id, up.project_name, up.is_deleted, GROUP_CONCAT(u.user_name) AS username', 'user AS u, user_projects AS up', 'up.project_id = '.$_REQUEST['projName'].' AND u.user_id = up.user_id AND u.is_deleted = 0 AND up.is_deleted IN (0, 2) '.$where.' group by up.project_id ORDER BY up.project_name');
	}else{
		$projName = $obj->selQRYMultiple('up.project_id, up.project_name, up.is_deleted, GROUP_CONCAT(u.user_name) AS username', 'user AS u, user_projects AS up', 'u.user_id = up.user_id AND u.is_deleted = 0 AND up.is_deleted IN (0, 2) '.$where.' group by up.project_id ORDER BY up.project_name');
	}

	foreach($projName as $projName){
		$inspData = array();
		$projDetailArr[$projName['project_id']] = $projName['project_name'];
		$inspData = $obj->selQRYMultiple('SUM(IF(isi.inspection_status= "Open",1,0)) AS open,
						SUM(IF(isi.inspection_status="Closed",1,0)) AS closed,
						(SUM(IF(isi.inspection_status= "Open",1,0)) + SUM(IF(isi.inspection_status="Closed",1,0))) as overall',
						'issued_to_for_inspections AS isi, project_inspections pi',
						'pi.inspection_id = isi.inspection_id AND
						pi.project_id = '.$projName['project_id'].' AND
						isi.is_deleted = 0 AND
						pi.is_deleted = 0');

#		if($inspData[0]['overall'] != '')				
		$projDataArr[$projName['project_id']] = array('project_name' => $projName['project_name'], 'status' => $projName['is_deleted'], 'userName' => explode(',', $projName['username']), 'dataArr' => $inspData);
	}
#print_r($projDataArr);die;
	
	$noInspection = sizeof($projDataArr);
//Retrive Location Tree and Data Start Here
	$proLocArray = array();

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
		$pdf->Cell(40, 10, 'Company Project Summary Report');		
		$pdf->Ln(6);
		
		$pdf->ln();
		$first_time = 1;
		$pageCount = 1;
		$page_break = 1;
		$fill = true;
		$i = 0;
		$x0 = $x = $pdf->GetX();
		$y = $pdf->GetY();
	
		$x = 5;
		$pageCount = 0;$pageCount++;
						
		$yH = 5;
	//Title String Header
		$pdf->SetFont('Times', 'BI', 7);	//200
		$pdf->Cell(75, $yH, "Project Name", 1, 0, 'L');
		$pdf->Cell(20, $yH, "Total Inspections", 1, 0, 'C');
		$pdf->Cell(20, $yH, "Open Inspections", 1, 0, 'C');
		$pdf->Cell(20, $yH, "Closed Inspections", 1, 0, 'C');

		$pdf->Cell(40, $yH, "Users", 1, 0, 'C');
		$pdf->Cell(20, $yH, "Project Status", 1, 0, 'C');
		
		$pdf->Ln();
#		$totalDueInsp = 0;$totalDueAmt = 0;$totalInsp = 0;$totalOpenInsp = 0;$totalCloseInsp = 0;$totalPendingInsp = 0;
		foreach($projDataArr as $key=>$value){
			$newHeight = sizeof($value['userName']);
			$pdf->SetFont('Times', 'B', 8);
			$pdf->MultiCell(75, $yH*$newHeight, $value['project_name'], 1, 'L');
			
			$pdf->SetXY($pdf->GetX()+75, $pdf->GetY()-($yH*$newHeight));
			
			$pdf->SetFont('Times', '', 8);
			$pdf->Cell(20, $yH*$newHeight, $value['dataArr'][0]['overall'], 1, 0, 'C');
			$pdf->Cell(20, $yH*$newHeight, $value['dataArr'][0]['open'], 1, 0, 'C');
			$pdf->Cell(20, $yH*$newHeight, $value['dataArr'][0]['closed'], 1, 0, 'C');
			$xissue = $pdf->GetX();
			$yissue = $y1 = $pdf->GetY();
			for($i=0; $i<$newHeight; $i++){
				$pdf->SetXY($xissue, $yissue);
				$pdf->Cell(40, $yH, $value['userName'][$i], 1, 0, 'L');
				$yissue = $yissue+$yH;
			}
			if($value['status'] == 0){ $proStatus = 'Live'; }
			if($value['status'] == 2){ $proStatus = 'Archived'; }
			$pdf->SetXY($xissue+40, $y1);
			$pdf->Cell(20, $yH*$newHeight, $proStatus, 1, 0, 'C');
			$pdf->Ln();
		}

		$file_name = 'company_project_summary_report'.microtime().'.pdf';
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
		$rply = 'Report Generatd '.$fieSize;
		echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="closePopUp();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';
	}else{
		echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
	}
}
	
?>