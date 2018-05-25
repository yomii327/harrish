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
	$users = $obj->selQRYMultiple('DISTINCT GROUP_CONCAT(user_id) AS userIDs', 'user_projects', 'is_deleted = 0 ORDER BY user_id DESC');
	$totalCount = 0;
	if($users[0]['userIDs'] != ''){
		$syncDate = $obj->selQRYMultiple('u.user_fullname, MAX(e.created_date) AS createDate, e.device', 'exportData AS e, user AS u', 'userid IN ('.$users[0]['userIDs'].') AND u.user_id = e.userid GROUP BY u.user_id ORDER BY e.created_date DESC');
		if(!empty($syncDate)){
			require('../fpdf/mc_table.php');
//Config Section
			class PDF extends PDF_MC_Table{
				var $d_location;
				function Header(){
					if($this->PageNo()!=1){
						$this->ln();	
						$this->SetFont('times', 'B', 9);
						$header = array("Manager / Associate", "Sync Date", "Device Type");
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
					$arr = array(80, 50, 50);
					return $arr;
				}
			}

			$pdf = new PDF("P", "mm", "A4");
			$pdf->AliasNbPages();
			$pdf->AddPage();
			
			$pdf->Image('../company_logo/logo.png', 130, 5, 'png');
			$pdf->Ln(15);

			$pdf->SetFont('times', 'BU', 14);
			$pdf->Cell(0, 5, 'Sync Report');
			$pdf->Ln(8);
			
			$pdf->SetFont('times', 'B', 12);
			$pdf->Cell(13, 5, 'Date :');
			
			$pdf->SetFont('times', '', 12);
			$pdf->Cell(130, 5, date('d/m/Y'));
			$pdf->Ln(8);
			
			$pdf->SetFont('times', 'B', 10);

			$header = array("Manager / Associate", "Sync Date", "Device Type");
			$w = $pdf->header_width();
			$pdf->SetWidths($w);		
			$best_height = 17;
			$pdf->row($header, $best_height);
			$pdf->SetFont('times', '', 9);
			
			foreach($syncDate as $syDate){$totalCount++;
				$isShow = true;
				$date = date('d/m/Y', strtotime($syDate['createDate'])); 
				$tm = explode(' ', $syDate['created_date']);
			
				$time = date("g:i a", strtotime($tm[1]));
			
				$pdf->Row(array($syDate['user_fullname'], $date."\n".$time, $syDate['device']));
			}
		}
	
		$file_name = 'Sync_Report_'.microtime().'.pdf';
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
			echo '<br clear="all" /><div style="margin-left:10px;color:black;">Sync Report generated successfully size :'.$rply.' <a onClick="closePopUp();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';

	}else{ ?>
		<div style="margin-left:10px;color:#000;"><i>No Record Found</i></div>
<?php }
}?>
