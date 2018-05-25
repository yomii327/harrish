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
	$projID = '';	$locationCL = '';	$subLocationCL = '';	$subLocation1CL = ''; $subLocation2CL = ''; $subLocation3CL = '';	$where = '';
	$totalCount = 0;
	$locArray = array();
	if(!empty($_REQUEST['projNameQrCode'])){
		$projID = $_REQUEST['projNameQrCode'];
		$projectName = $obj->getDataByKey('user_projects', 'project_id', $projID, 'project_name');
	}else{
		
		$projID = $_SESSION['prjQrId'];
		$projectName = $obj->getDataByKey('user_projects', 'project_id', $projID, 'project_name');
	}
	$chkFileStatus = 1;
	$where = "";
	if(!empty($_REQUEST['locationCL'])){
		$where.=" AND location_id IN (".$obj->subLocationsId($_REQUEST['locationCL'], ",").")";
	}
	if(!empty($_REQUEST['subLocationCL'])){
		$where = "";
		$where.=" AND location_id IN (".$obj->subLocationsId($_REQUEST['subLocationCL'], ",").")";
	}
	if(!empty($_REQUEST['subLocation1CL'])){
		$where = "";
		$where.=" AND location_id IN (".$obj->subLocationsId($_REQUEST['subLocation1CL'], ",").")";
	}
	if(!empty($_REQUEST['subLocation2CL'])){
		$where = "";
		$where.=" AND location_id IN (".$obj->subLocationsId($_REQUEST['subLocation2CL'], ",").")";
	}
	if(!empty($_REQUEST['subLocation3CL'])){
		$where = "";
		$where.=" AND location_id IN (".$obj->subLocationsId($_REQUEST['subLocation3CL'], ",").")";
	}
	
//Main Query Part
$mainQuery = "SELECT location_id, qr_code FROM project_locations WHERE project_id = ".$projID." AND qr_code!='' AND is_deleted = 0".$where;
$rs = mysql_query($mainQuery);
	
$noInspection  = mysql_num_rows($rs);
$noPages = ceil(($noInspection-1)/2 +1);
	
	require('../fpdf/mc_table.php');
	//Config Section
	class PDF extends PDF_MC_Table{
		var $d_location;
		/*function Header(){
			if($this->PageNo()!=1){
				$this->Cell(0, 10, $this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
				$this->ln();	
				$this->SetFont('times', 'B', 12);
				$header = array('Qr Code');
				$w = $this->header_width();
				$this->SetWidths($w);		
				$best_height = 17;
				$this->row($header, $best_height);
			}
		}*/
		
		function Footer(){
			$this->SetY(-15);
			$this->SetFont('times','B',10);
			$this->Cell(0, 10, "DefectID - Copyright Wiseworking 2012 / 2013", 0, 0, 'C');
		}
		
		function header_width(){
			$arr = array(0);
			return $arr;
		}
	}
	$totalTime = '';
	/*if($chkFileStatus == 1)
	{*/	
		if($noInspection > 0)
		{
			$pdf = new PDF("P", "mm", "A4");
			$pdf->AliasNbPages();
			$pdf->AddPage();
			
			$pdf->SetTopMargin(20);
		
			$pdf->Image('../company_logo/logo.png', 115, 5, 'png', -120);
			$pdf->Ln(8);
			
			$pdf->SetFont('times', 'BU', 12);
			$pdf->Cell(40, 10, 'Qr Code Report');		
			$pdf->Ln(6);
			
			$pdf->SetFont('times', 'B', 10);
			$pdf->Cell(24, 10, 'Project Name : ');	
			
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(30, 10, $projectName);	
			$pdf->Ln(7);
			
			$pdf->SetFillColor(79,129,187);
			$pdf->Cell(190,0.2,'',1,1,'C');
			$pdf->Ln(7);
		/*$pdf->SetFont('times', 'B', 12);
		$header = array('');
		$w = $pdf->header_width();
		$pdf->SetWidths($w);		
		$best_height = 0;
		$pdf->row($header, $best_height);*/
		
		$currLocID = '';
		$location = '';
		$flag = 0;
		$i = 0;
		$imageName = '';
		$flagQrCode = 0;
		while($row = mysql_fetch_assoc($rs)){
			if($i%6==0 && $i!=0)
			{
				$pdf->AddPage();
			}
			
			$imageName = '../qrcode_images/'.$owner_id.'/'.$row['qr_code'].'_qrcode.png';
			
			if($flag == 0){
				// Colors of frame, background and text
				$pdf->SetDrawColor(79,129,187);
				$pdf->SetFillColor(79,129,187);
				$pdf->SetTextColor(79,129,187);
				// Thickness of frame (1 mm)
				$pdf->SetLineWidth(1);
				//$pdf->Cell(5);
				$x1 = $pdf->GetX();
				$pdf->SetX($x1);
				$pdf->Cell(5, 7, $projectName);	
				$pdf->SetX($x1+57);
				$pdf->Cell(25,7, $pdf->Image('../company_logo/logo.png',$pdf->GetX()+2,$pdf->GetY()+2,25,'png'), '','','C', false);	
				$pdf->SetX($x1);
				$imageName = '../qrcode_images/'.$owner_id.'/'.$row['qr_code'].'_qrcode.png';
				if (file_exists($imageName)) {
					$flagQrCode = 1;
					$pdf->Cell(85,75, $pdf->Image($imageName,$pdf->GetX()+15,$pdf->GetY()+10,50,'png'), 1,'','C', false);
				} else {
					$pdf->Cell(85,75,'No QR Code record is available',1,'','C');
				}		
				
				$flag = 1;
			}else{
				$pdf->SetX($pdf->GetX()+20);
				// Colors of frame, background and text
				$pdf->SetDrawColor(79,129,187);
				$pdf->SetFillColor(79,129,187);
				$pdf->SetTextColor(79,129,187);
				// Thickness of frame (1 mm)
				$pdf->SetLineWidth(1);	
				$x2 = $pdf->GetX();
				$pdf->SetX($x2);
				$pdf->Cell(5, 7, $projectName);
				$pdf->SetX($x2+57);
				$pdf->Cell(25,7, $pdf->Image('../company_logo/logo.png',$pdf->GetX()+2,$pdf->GetY()+2,25,'png'), '','','C', false);	
				$pdf->SetX($x2);
				$imageName = '../qrcode_images/'.$owner_id.'/'.$row['qr_code'].'_qrcode.png';
				if (file_exists($imageName)) {
					$flagQrCode = 1;
					$pdf->Cell(85,75, $pdf->Image($imageName,$pdf->GetX()+15,$pdf->GetY()+10,50,'png'), 1,'','C', false);
				} else {
					$pdf->Cell(85,75,'No QR Code record is available',1,'','C');
				}
					
				$flag = 0;
				$pdf->ln(80);
			}
			$i++;
		} 
		$file_name = 'QrCode_Report_'.microtime().'.pdf';
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
		$mtime = microtime(); 
		$mtime = explode(" ",$mtime); 
		$mtime = $mtime[1] + $mtime[0]; 
		$endtime = $mtime; 
		$totaltime = ($endtime - $starttime); 
		$totaltime = number_format($totaltime, 2, '.', '');
		if($flagQrCode==0)
		{
			echo '<div style="margin-left:10px; font-size:14px; width:400px;">No QR Code record is available. Kindly generate the QR Code over "Project Configuration".</div>';
		}else{
			echo '<div style="margin-left:10px; font-size:18px; color:#0378B5; width:100%; font-weight: bold;"><table width="100%">
			<tr>
				<td><span style="font-size:15px;">'.$noInspection.' results ('.$totaltime.' Seconds) '.$rply.'</span></td>
			</tr>
			<tr>
				<td><a onClick="closePopUp();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank"><img src="images/qr_code_report.png" width="180px" height="60px"></a></td>
			</tr>
		</table></div>';
		}
		
		
		}else{
			echo "No QR Code record is available. Kindly generate the QR Code over 'Project Configuration'.";
		}
	
} ?>