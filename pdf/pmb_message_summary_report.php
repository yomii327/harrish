<?php
ob_start();

session_start();
set_time_limit(6000000000000000000);

include('../includes/commanfunction.php');
$obj = new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])) {
    $owner_id = $_SESSION['ww_builder_id'];
} elseif (isset($_SESSION['ww_owner_id'])) {
    $owner_id = $_SESSION['ww_owner_id'];
} elseif ($_SESSION['ww_is_company']) {
    $owner_id = "company";
}
//echo "<pre>"; print_r($_REQUEST['messageType']); print_r($_GET);die;
if (isset($_REQUEST["name"])) {
    

    $messageData = $obj->getRecordByQuery("SELECT
				m.inspection_id,
				um.user_id,
				um.thread_id, 
				um.user_id, 
				um.refrence_number,
				m.title,
				m.to_email_address,
				m.message_id,
				m.correspondence_number,
				m.created_date,
				m.date_sent,
				m.date_approved,
				m.sent,
				m.approved,
				m.claimed,
				m.certified,
				m.invoiced,
				m.rfi_reference
			FROM
				pmb_user_message as um,
				pmb_message m
			WHERE
				um.message_id = m.message_id AND
				um.is_deleted = 0 AND
				m.is_draft = 0 AND
				um.project_id = '" . $_SESSION['idp'] . "' AND
				um.user_id = '" . $_SESSION['ww_builder_id'] . "' AND
				m.message_type = '" . $_REQUEST['messageType'] . "'
			GROUP BY
				um.thread_id
			ORDER BY
				m.sent_time DESC");
	#print_r($messageData);	die;
    $noInspection = sizeof($messageData);
    foreach($messageData as $msgData){
	$threadArr[] = $msgData['thread_id'];
	}
	$userNameData = array();
	$userData = $obj->selQRYMultiple('GROUP_CONCAT(DISTINCT u.user_fullname SEPARATOR ", ") AS users, um.thread_id', 'user AS u, pmb_user_message AS um', 'u.is_deleted = 0 AND um.from_id = u.user_id AND um.is_cc_user = 0 AND um.thread_id IN ('.join(',', $threadArr).') AND u.user_id != '.$_SESSION['ww_builder_id'].' GROUP BY thread_id');
	foreach($userData as $usData){
		$userNameData[$usData['thread_id']] = $usData['users'];
	}
	$typeArrr = array("General Correspondence", "Memorandum", "Site Instruction", "Architect Instruction", "Consultant Advice Notice", "Design Changes");
	#$userDataNew = $obj->selQRYMultiple(''); 
#print_r($userNameData);die;
    //Retrive Location Tree and Data Start Here
    $logo = $obj->selQRYMultiple('project_logo', 'projects', 'is_deleted = 0 AND project_id = ' . $_SESSION['idp'] . '', 'data');
    
    //if (file_exists('../project_images/' . $logo[0]['project_logo']) && !empty($logo[0]['project_logo'])) {
      // $logo_proj = '../project_images/' . $logo[0]['project_logo'];
	if(isset($_SESSION['userCompLogo']) && file_exists('../company_logo/'.$_SESSION['userCompLogo'])){
		$logo_proj = '../company_logo/' . $_SESSION['userCompLogo'];
		
    } else {
        $logo_proj = '../images/logo.png';
    }
    
    if ($noInspection > 0 && is_array($messageData)) {
        require('../fpdf/mc_table.php');

		if(explode("_", $_REQUEST['messageType'])){
			$messageTypeArr = explode("_", $_REQUEST['messageType']);
			$_REQUEST['messageType'] = end(explode("_", $_REQUEST['messageType']));
			if(isset($messageTypeArr[1]) && ($messageTypeArr[1] == "Client Side" || $messageTypeArr[1] == "Subcontractors")) {
				$_REQUEST['messageType'] = $messageTypeArr[1] . ' ' . $_REQUEST['messageType'];
			}
		}else{
			$_REQUEST['messageType'] = $_REQUEST['messageType'];
		}
        class PDF extends PDF_MC_Table {

            function Header() {// Page header
                if ($this->PageNo() != 1) {// Page number
                    $this->Cell(0, 10, 'Page: ' . $this->PageNo() . " of " . ' {nb}', 0, 0, 'L');
                    $this->ln();
                }
            }

            function Footer() {// Position at 1.5 cm from bottom
                $this->SetY(-15);
                $this->SetFont('helvetica', 'B', 10);
                $this->Cell(10);
                $this->Cell(15, 4, 'DefectID,  part of the Wiseworker Quality Management Ecosystem,  helping the construction industry.', 0, 0);
                $this->Ln(5);
                $this->Cell(76);
                $this->Cell(60, 4, 'www.wiseworker.net', 0, 0);
            }

            function header_width() {
                #if(in_array($_REQUEST['messageType'],$typeArrr)){
					if($_REQUEST['messageType']=='Variation Claims'){
						//return array(20, 20, 20, 50, 20, 20, 20, 22);
						return array(20, 20, 50, 60, 20, 22);
					}else if($_REQUEST['messageType']=='Progress Claims'){
						return array(20, 20, 20, 35, 20, 20, 20, 15, 25);
					}else if($_REQUEST['messageType']=='Site Instruction'){	
						return array(40, 20, 50, 35, 35);
					}else{	
						return array(50, 20, 50, 70);
					}
				/*}else{
					return array(50, 140);
				}*/
            }

        }

        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->AliasNbPages();
		$image_format = image_type_to_mime_type(exif_imagetype($logo_proj));;
		if($image_format == 'image/png'){
	        $pdf->Image($logo_proj, 140, 5, 45, 35, 'png');
			
		}else{
			$pdf->Image($logo_proj, 140, 5, 45);
		}
		
        $pdf->Ln(8);

        $pdf->SetFont('times', 'BU', 12);
        $pdf->Cell(40, 10, $_REQUEST['messageType'] . ' Register Report');
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
        $pageCount = 0;
        $pageCount++;

        $yH = 5;
        //Title String Header
        $pdf->SetFont('times', 'B', 11);
       # if(in_array($_REQUEST['messageType'],$typeArrr)){
			if($_REQUEST['messageType']=='Variation Claims'){
				//$header = array("Variation No", "Date", "Subject", "To", "Date Sent", "Date Approved", "$ Claimed", "$ Approved");
				$header = array("Variation No", "Date", "Subject", "To", "Date Sent", "$ Claimed");
				
			}else if($_REQUEST['messageType']=='Progress Claims'){
				$header = array("Variation No", "Date", "Subject", "To", "Date Sent", "Date Approved", "$ Claimed", "$ Certified", "$ Invoiced");
			}else if($_REQUEST['messageType']=='Site Instruction'){
				$header = array("Correspondence No", "Date", "Subject", "To", "RFI Reference");
			}else{
				$header = array("Correspondence No", "Date", "Subject", "To");
			}
		/*}else{
			$header = array("Correspondence No", "Description");	
		}*/
		$pdf->ln(5);
        $w = $pdf->header_width();
        $pdf->SetWidths($w);
        $best_height = 7;
        $pdf->row($header, $best_height);

        #$totalDueInsp = 0;$totalDueAmt = 0;$totalInsp = 0;$totalOpenInsp = 0;$totalCloseInsp = 0;$totalPendingInsp = 0;
        $pdf->SetFont('times', '', 10); $totSent = 0; $totApproved = 0;
        foreach ($messageData as $msgData) { 
			 #if(!in_array($_REQUEST['messageType'],$typeArrr)){
				if($_REQUEST['messageType']=='Variation Claims'){ 
					$totClaimed += $claimed = !empty($msgData['claimed'])?$msgData['claimed']:0; 
					//$pdf->Row(array(trim($msgData['correspondence_number']), date('d/m/Y', strtotime($msgData['created_date'])), trim($msgData['title']), ($userNameData[$msgData['thread_id']]?$userNameData[$msgData['thread_id']]:$msgData['to_email_address']), date('d-m-Y',strtotime($msgData['date_sent'])), date('d-m-Y',strtotime($msgData['date_approved'])), $claimed, $approved));
					$pdf->Row(array(trim($msgData['correspondence_number']), date('d/m/Y', strtotime($msgData['created_date'])), trim($msgData['title']), ($userNameData[$msgData['thread_id']]?$userNameData[$msgData['thread_id']]:$msgData['to_email_address']), date('d-m-Y',strtotime($msgData['date_sent'])), number_format($claimed)));
					
				}else if($_REQUEST['messageType']=='Progress Claims'){
					$pdf->Row(array(trim($msgData['correspondence_number']), date('d/m/Y', strtotime($msgData['created_date'])), trim($msgData['title']), ($userNameData[$msgData['thread_id']]?$userNameData[$msgData['thread_id']]:$msgData['to_email_address']), date('d-m-Y',strtotime($msgData['date_sent'])), date('d-m-Y',strtotime($msgData['date_approved'])), number_format($msgData['sent']), number_format($msgData['approved']), number_format($msgData['claimed']), number_format($msgData['certified']), number_format($msgData['invoiced'])));
					
				}else if($_REQUEST['messageType']=='Site Instruction'){	
					$pdf->Row(array(trim($msgData['correspondence_number']), date('d/m/Y', strtotime($msgData['created_date'])), trim($msgData['title']), ($userNameData[$msgData['thread_id']]?$userNameData[$msgData['thread_id']]:$msgData['to_email_address']), trim($msgData['rfi_reference'])));
				}else{	
					$pdf->Row(array(trim($msgData['correspondence_number']), date('d/m/Y', strtotime($msgData['created_date'])), trim($msgData['title']), ($userNameData[$msgData['thread_id']]?$userNameData[$msgData['thread_id']]:$msgData['to_email_address'])));
				}
			/*}else{
				$pdf->Row(array(trim($msgData['correspondence_number']),trim($msgData['title'])));
			}*/
        }
		if($_REQUEST['messageType']=='Variation Claims'){
			$pdf->SetFont('times', 'B', 11);
			$pdf->Row(array('', '', '', '', 'Total : ', number_format($totClaimed)));
			$pdf->SetFont('times', '', 11);
		}
		 
		/* $pdf->ln();
		 $pdf->ln();
				$pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(10);
                $pdf->Cell(2, 4, 'The sum of all $ figures: '.$noInspection , 0, 0);
        */
        $file_name = $_REQUEST['messageType'] . ' Register Report.pdf';
        $d = '../report_pdf/' . $owner_id;
        if (!is_dir($d))
            mkdir($d);

        if (file_exists($d . '/' . $file_name))
            unlink($d . '/' . $file_name);

        $tempFile = $d . '/' . $file_name;
        $pdf->Output($tempFile);
        $fieSize = filesize($tempFile);
        $fieSize = floor($fieSize / (1024));
        if ($fieSize > 1024) {
            $fieSize = floor($fieSize / (1024)) . "Mbs";
        } else {
            $fieSize .= "Kbs";
        }
        $rply = 'Report Generated ' . $fieSize;
        echo '<br clear="all" /><div style="margin-left:10px;">' . $rply . ' <a onClick="closePopUp();" href="report_pdf/' . $owner_id . '/' . $file_name . '" target="_blank" class="view_btn"></a></div>';
    } else {
        echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
    }
}
?>
