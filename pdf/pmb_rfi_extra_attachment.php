<?php
ob_start();

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
if (isset($_REQUEST["name"])){
	$where = '';
	if(!empty($_REQUEST['status']) && $_REQUEST['status']!='All'){
		$where .= ' AND m.rfi_status = "'.$_REQUEST['status'].'"'; 
	}
	if(!empty($_REQUEST['keyword'])){ 
		$where .= ' AND (um.refrence_number LIKE "%'.$_REQUEST['keyword'].'%" OR um.rfi_number LIKE "%'.$_REQUEST['keyword'].'%")'; 
	}
	if($_SESSION['ww_builder_id']==168){
	$messageData = $obj->getRecordByQuery("SELECT um.user_id, um.thread_id,
					um.refrence_number,
					um.rfi_number,
					um.created_date,
					m.title as rfi_description,
					m.rfi_fixed_by_date,
					m.rfi_status,
					m.rfi_closed_date,
					m.title
			FROM
				pmb_user_message as um,
				pmb_message m
			WHERE
				um.message_id = m.message_id AND
				um.is_deleted = 0 AND
				m.is_draft = 0 AND
				um.project_id = '".$_SESSION['idp']."' AND
				m.message_type = '".$_REQUEST['messageType']."' AND
				um.rfi_number != '0'
				".$where."
			GROUP BY
				um.thread_id
			ORDER BY
				CAST(um.rfi_number AS UNSIGNED) ASC");
		}else{
		$messageData = $obj->getRecordByQuery("SELECT um.user_id, um.thread_id,
					um.refrence_number,
					um.rfi_number,
					um.created_date,
					m.title as rfi_description,
					m.rfi_fixed_by_date,
					m.rfi_status,
					m.rfi_closed_date,
					m.title
			FROM
				pmb_user_message as um,
				pmb_message m
			WHERE
				um.message_id = m.message_id AND
				um.is_deleted = 0 AND
				m.is_draft = 0 AND
				um.project_id = '".$_SESSION['idp']."' AND
				um.user_id = '".$_SESSION['ww_builder_id']."' AND
				m.message_type = '".$_REQUEST['messageType']."' AND
				um.rfi_number != '0'
				".$where."
			GROUP BY
				um.thread_id
			ORDER BY 
				CAST(um.rfi_number AS UNSIGNED) ASC");
			
		}		
#echo "<pre>";print_r($messageData);			
$threadArr = array();
foreach($messageData as $msgData){
	$threadArr[] = $msgData['thread_id'];
}
$userNameData = array();
$userData = $obj->selQRYMultiple('GROUP_CONCAT(DISTINCT u.user_fullname SEPARATOR ", ") AS users, um.thread_id', 'user AS u, pmb_user_message AS um', 'u.is_deleted = 0 AND um.from_id = u.user_id AND um.is_cc_user = 0 AND um.thread_id IN ('.join(',', $threadArr).') AND u.user_id != '.$_SESSION['ww_builder_id'].' GROUP BY thread_id');
foreach($userData as $usData){
	$userNameData[$usData['thread_id']] = $usData['users'];
}

	//			m.rfi_fixed_by_date != '' AND
	$noInspection = sizeof($messageData);
//Retrive Location Tree and Data Start Here

	if($noInspection > 0 && is_array($messageData)){
		require('../fpdf/mc_table.php');	
		class PDF extends PDF_MC_Table{
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
			function header_width(){
				return array(20, 20, 50, 25, 20, 15, 15	, 25);
			}
		}
	
		$pdf = new PDF();
		$pdf->AddPage();
		$pdf->AliasNbPages();
			$logo = $obj->selQRYMultiple('project_logo','projects','is_deleted = 0 AND project_id = '.$_SESSION['idp'].'', 'data');
		//issue to data fetch here
		#print_r($logo);die;
		if(file_exists('../project_images/'.$logo[0]['project_logo']) && !empty($logo[0]['project_logo'])){
			$logo_proj = '../project_images/'.$logo[0]['project_logo']; 	
		}else{
			$logo_proj = '../company_logo/logo.png';
		} 
		#echo $logo_proj;die; 
		$pdf->Image($logo_proj,  150,  12,  -150);
		$pdf->Ln(8);
	
		$pdf->SetFont('times', 'BU', 12);
		$pdf->Cell(40, 10, $_REQUEST['messageType'].' Register Report');		
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
		$pdf->SetFont('times', 'B', 11);

		$header = array("Correspondence No", "Date", "Subject", "To", "Due Date", "Lag",  "RFI Status", "Date closed out");					

		if($_REQUEST['status'] == "Closed"){
			$header = array("Correspondence No", "Date", "Subject", "To", "Due Date", "RFI Status", "Date closed out");
		}

	

		$w = $pdf->header_width();
		$pdf->SetWidths($w);		
		$best_height = 7;
		$pdf->row($header, $best_height);

#		$totalDueInsp = 0;$totalDueAmt = 0;$totalInsp = 0;$totalOpenInsp = 0;$totalCloseInsp = 0;$totalPendingInsp = 0;
		$pdf->SetFont('times', '', 10);
		foreach($messageData as $msgData){
			$exUserName = $userNameData[$msgData['thread_id']];
			
			if($msgData['created_date'] != '0000-00-00 00:00:00')
				$dateRaised = date('d/m/Y', strtotime($msgData['created_date']));
			
			if($msgData['rfi_fixed_by_date'] != '0000-00-00'){
				$fixedByDate = date('d/m/Y', strtotime($msgData['rfi_fixed_by_date']));
				
				if((strtotime(date("Y-m-d")) > strtotime($msgData['rfi_fixed_by_date'])) && $msgData['rfi_status'] != "Closed"){
					$diff = abs(strtotime(date("Y-m-d")) - strtotime($msgData['rfi_fixed_by_date']));

					$years = floor($diff / (365*60*60*24));
					$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
					$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
				}else{
					$days = '';
				}
				
			}
			$closedDate = '';
			if(($msgData['rfi_closed_date'] != '0000-00-00') && $msgData['rfi_status'] == "Closed"){
				$closedDate = date('d/m/Y', strtotime($msgData['rfi_closed_date']));	
			}

			if($_REQUEST['status'] != "Closed"){
				$pdf->Row( array( trim($msgData['rfi_number']), $dateRaised, trim($msgData['title']), $exUserName, $fixedByDate, $days, $msgData['rfi_status'], $closedDate ) );
			}else{
				$pdf->Row( array( trim($msgData['rfi_number']), $dateRaised, trim($msgData['title']), $exUserName, $fixedByDate, $msgData['rfi_status'], $closedDate ) );	
			}
				
		}

		$file_name = $_REQUEST['messageType'].' Register Report.pdf';
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
		$rply = 'Report Generated '.$fieSize;
		echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="closePopUp();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';
	}else{
		echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
	}
}
	
?>
