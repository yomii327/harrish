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
	$tableRow = '';
	$isShow = false;
	$users = $obj->selQRYMultiple('up.project_id, up.project_name, GROUP_CONCAT(u.user_name SEPARATOR "\r\n") AS userNames, GROUP_CONCAT(u.user_fullname SEPARATOR "\n") AS userFullnames', 'user_projects AS up, user AS u', 'u.is_deleted = 0 AND up.is_deleted = 0 AND u.user_id = up.user_id GROUP BY up.project_id ORDER BY u.user_id DESC');
#echo '<pre>';print_r($users);
	require('../fpdf/mc_table.php');
//Config Section
	class PDF extends PDF_MC_Table{
		var $d_location;
		function Header(){
			if($this->PageNo()!=1){
				$this->ln();	
				$this->SetFont('times', 'B', 9);
				$header = array("Project Name", "User ID", "Full Name");
				$w = $this->header_width();
				$this->SetWidths($w);
				$best_height = 17;
				$this->row($header, $best_height);
			}
		}
		
		function Footer(){
			$this->SetY(-15);
			$this->SetFont('times','B',9);
			$this->Cell(0, 10, "DefectID - Copyright Wiseworking 2012 / 2013", 0, 0, 'C');
			$this->Cell(0, 10, $this->PageNo()." of ".' {nb}', 0, 0, 'L');
		}
		
		function header_width(){
			$arr = array(80, 50, 60);
			return $arr;
		}
	}

	$totalCount = 0;
	
	if(!empty($users)){
		$pdf = new PDF("P", "mm", "A4");
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		$pdf->Image('../company_logo/logo.png', 130, 5, 'png');
		$pdf->Ln(15);

		$pdf->SetFont('times', 'BU', 14);
		$pdf->Cell(0, 5, 'Project - User Report');
		$pdf->Ln(8);
		
		$pdf->SetFont('times', 'B', 12);
		$pdf->Cell(13, 5, 'Date :');
		
		$pdf->SetFont('times', '', 12);
		$pdf->Cell(130, 5, date('d/m/Y'));
		$pdf->Ln(8);
		
		$pdf->SetFont('times', 'B', 10);

		$header = array("Project Name", "User ID", "Full Name");
		$w = $pdf->header_width();
		$pdf->SetWidths($w);		
		$best_height = 17;
		$pdf->row($header, $best_height);
		$pdf->SetFont('times', '', 9);
		
		foreach($users as $usr){
			$pdf->Row(array($usr['project_name'], $usr['userNames'], $usr['userFullnames']));
		}
		
		$file_name = 'Project-user_Report_'.microtime().'.pdf';
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
		$rply = $fieSize;
		//PDF Creattion Section Start Here
			echo '<br clear="all" /><div style="margin-left:10px;color:black;">Project - User Report generated successfully size :'.$rply.' <a onClick="closePopUp();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';

	}else{ ?>
		<div style="margin-left:10px;color:#000;"><i>No Record Found</i></div>
<?php }
}?>
