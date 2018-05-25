<?php
#error_reporting(E_ALL);
//ini_set('display_errors', '1');
session_start();
ob_start();

if (!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])) {?>
<script language="javascript" type="text/javascript">window.location.href = "<?= HOME_SCREEN ?>";</script>
<?php }
$projectId = isset($_SESSION['idp']) ? $_SESSION['idp'] : 0;
#echo '<pre>';print_r($_SESSION);die;
$msg = '';
require_once('includes/class.phpmailer.php');
include('includes/commanfunction.php');
require('fpdf/mc_table.php');

class PDF extends PDF_MC_Table {
    function Footer() {
        /* $this->SetY(-15);
          $this->SetFont('times','B',10);
          $this->Cell(0, 10, "Wiseworker- Copyright Wiseworking ".date('Y'), 0, 0, 'C'); */
    }

    function header_width() {
        return array(40, 30, 60, 60);
    }
}

$object = new COMMAN_Class();
$obj = new DB_Class();
$userId = $_SESSION['ww_builder']['user_id'];
$projectName = $object->selQRYMultiple('project_name, job_number', 'projects', 'is_deleted = 0 AND project_id = '.$_SESSION['idp'], 'mytest1');

$spCondtion = ' AND m.message_type = "General Correspondence"';
if (isset($_GET['folderType']) && $_GET['folderType'] != "") {
    $spCondtion = ' AND m.message_type = "' . $_GET['folderType'] . '"';
}
$orderBy = " ORDER BY um.rfi_number DESC";
$refNumberMsgCount = array();
#$refNumberMsgCount = $object->selQRYMultiple('max(LTRIM(um.rfi_number)) AS refNumber', 'pmb_message as m, pmb_user_message as um', 'um.project_id = ' . $_SESSION['idp'] . ' AND m.is_draft = 0 AND um.is_deleted= 0 AND m.message_id = um.message_id' . $spCondtion);
#print_r($refNumberMsgCount);
$refNumberMsgCount = $object->selQRYMultiple('DISTINCT um.rfi_number', 'pmb_message as m, pmb_user_message as um', 'um.project_id = ' . $_SESSION['idp'] . ' AND m.is_draft = 0 AND um.is_deleted= 0 AND m.message_id = um.message_id AND um.rfi_number != "" ' . $spCondtion.$orderBy, 'mydemotest');
$refArr = array();
foreach($refNumberMsgCount as $refNumberMsgCountVal){
	$refArr[] = $refNumberMsgCountVal['rfi_number'];
}
//print_r($refArr);

if(in_array('NA', $refArr)) {
	$refCount = count($refArr);
	if($refCount == 1) {
		$maximumVal = 0; //$refArr[0];
	} else {
		$maximumVal = $refArr[1];
	}
} else {
	$maximumVal =  max($refArr);
}
$refNumberMsgCount1[0] = array('refNumber'=>$maximumVal);
#print_r($refNumberMsgCount1); 
#die;
$corNumCount = array();
$corNumCount = $object->selQRYMultiple('max(m.correspondence_number) AS correspondenceNumber', 'pmb_message as m, pmb_user_message as um', 'um.project_id = ' . $_SESSION['idp'] . ' AND m.is_draft = 0 AND um.is_deleted= 0 AND m.message_id = um.message_id' . $spCondtion);

//Assing Default message users
/* #$defaultUserArr = array('156' => 'peter.paritsi@crema.com.au', '157' => 'michael.guardiani@crema.com.au', '168' => 'jesse.rosenfeld@crema.com.au', '169' => 'Austin.Giordano@crema.com.au', '172' => 'alistair.souter@crema.com.au');
  $defaultUserArr = array('156' => 'peter.paritsi@crema.com.au', '157' => 'michael.guardiani@crema.com.au', '169' => 'Austin.Giordano@crema.com.au', '354' => 'jason.treasure@crema.com.au', '355' => 'joseph.frisina@crema.com.au');
  $defaultIdArr = array_keys($defaultUserArr);
  //Unset current user
  if(in_array($_SESSION['ww_builder_id'], $defaultIdArr))		unset($defaultUserArr[$_SESSION['ww_builder_id']]);
 */
if (isset($_POST['submit']) and $_POST['submit'] == 'add') {
//Code update for avoid condition for duplicate record for RFI messages Start Here
    if ($_POST['messageType'] == 'Request For Information' && isset($_POST['save'])) {//Code for by pass same RFI Number project wise
        $rfiData = $object->selQRYMultiple('DISTINCT rfi_number', 'pmb_user_message', 'project_id = ' . $projectId . ' AND is_deleted = 0 AND rfi_number != 0 AND rfi_number = ' . $_POST['RFInumber']);
        if (isset($rfiData) && $rfiData[0]['rfi_number'] != "") {//By pass request here
            header('Location:?sect=sent_box&sm=1');
            ?>
            <script language="javascript" type="text/javascript">window.location.href = "?sect=sent_box&sm=1";</script>
        <?php
        }
    }

//Code update for avoid condition for duplicate record for RFI messages Start Here
    /*
      if($_POST['messageType'] == 'Request For Information' && $_SESSION['idp'] == 212){
      foreach($defaultIdArr as $key=>$toUserID){//Check and set default user as a cc members
      if(!in_array($toUserID, $_POST['recipTo'])){//Check User in email
      if(!in_array($defaultUserArr[$toUserID], $_POST['recipCC'] ) && isset($defaultUserArr[$toUserID])){//Check User in email
      $_POST['recipCC'][] = $defaultUserArr[$toUserID];
      }
      }
      }
      } */
#print_r($_POST['recipTo']);print_r($_POST['recipCC']);die;
#echo '<pre>';print_r($_POST);print_r($_SESSION[$_SESSION['idp'].'_emailfile']);print_r($_SESSION[$_SESSION['idp'].'_orignalFileName']);die;
//Data Filteration Section Start Here
    function addDoubleQuotes($element) {
        return '"' . $element . '"';
    }

//Function fore adding double qoutations

    $ccFilterList = array_map("addDoubleQuotes", $_POST['recipCC']);
    $userDataCC = $object->selQRYMultiple('u.user_id, u.user_email', 'user AS u, user_projects AS up', 'u.is_deleted = 0 AND up.is_deleted = 0 AND up.user_id = u.user_id AND up.project_id = ' . $_SESSION['idp'] . ' AND u.user_email IN (' . join(",", $ccFilterList) . ')');
    $ccUsserIDArr = array();
    foreach ($userDataCC as $usrDt) {
        $ccUsserIDArr[$usrDt['user_email']] = $usrDt['user_id'];
    }
	//Data Filteration Section End Here
    $from = $_SESSION['ww_builder_id'];
    $recipTo = !empty($_POST['recipTo']) ? $_POST['recipTo'] : 0;
    $recipCC = $_POST['recipCC'];
    $subject1 = $subject = empty($_POST['subject']) ? 'no subject' : $_POST['subject'];
    $purchaserLocation = $_POST['purchaserLocation'];
    $tags = $_POST['tags'];
    $companyTag = $_POST['companyTag'];
    $messageType = $_POST['messageType'];
    $refrenceNumber = !empty($_POST['newDynamicGenRefrenceNumber']) ? $_POST['newDynamicGenRefrenceNumber'] : $_POST['refrenceNumber'] . " " . $_POST['messageType'];
    $messagePlainDetails = $_POST['plainText'];
    $messgeId = (isset($_POST['composeId']) && !empty($_POST['composeId'])) ? $_POST['composeId'] : 0;

    $RFInumber = "";
    $RFIdescription = "";
    $RFIstatus = "";
    $fixedByDate = "";
    if ($messageType == 'Request For Information') {
        $RFInumber = $_POST['RFInumber'];
        $fixedByDate = $object->dateChanger('-', '-', $_POST['fixedByDate']);
        $RFIstatus = $_POST['RFIstatus'];
    }
    $correspondenceNumber = $_POST['correspondenceNumber'];
    $messageDetails = $_POST['messageDetails'];
    /* if($messageType == 'Request For Information'){//New Updated Dated : 05-12-2013<br>
      $messageDetails = ' RFI #'.$RFInumber.'<br><br>'.$_POST['messageDetails'];
      } */
    if ($messageType == 'Request For Information') { //New Updated Dated : 05-12-2013<br>
        #print_r($_REQUEST);die;
        $html = '';
        $usersTo = '';
        $usersCC = '';
        if (isset($recipTo) && !empty($recipTo)) {
            foreach ($recipTo as $to) {
                if (is_numeric($to)) {
                    $query = "select u.user_id, u.user_type, u.user_fullname as fullname, u.user_email as email from user as u where u.is_deleted=0 and u.user_id='" . $to . "'";
                    $getUserDetails = mysql_query($query);
                    while ($gUDetail = mysql_fetch_array($getUserDetails)) {
                        $usersTo.= empty($usersTo) ? $gUDetail['fullname'] : ", " . $gUDetail['fullname'];
                    }
                } elseif (!empty($to)) {
                    $sql = "SELECT ab.full_name as name, user_email as email FROM pmb_address_book as ab where user_email IN ('" . $to . "') UNION	SELECT iss.company_name as name, iss.issue_to_email as email FROM inspection_issue_to as iss WHERE issue_to_email IN ('" . $to . "')";
                    $result = mysql_query($sql);
                    $row = mysql_fetch_row($result);
                    if ($row > 0) {
                        $usersTo.= empty($usersTo) ? $row[0] : ", " . $row[0];
                    } else {
                        $usersToArr = explode('@', $to);
                        $usersTo.= empty($usersTo) ? $usersToArr[0] : ", " . $usersToArr[0];
                    }
                }
            }
        }

        if (isset($recipCC)) {
            foreach ($recipCC as $cc) {
                if (!empty($cc)) {
                    $sql = "SELECT ab.full_name as name, user_email as email FROM pmb_address_book as ab where user_email IN ('" . $cc . "') UNION	SELECT iss.company_name as name, iss.issue_to_email as email FROM inspection_issue_to as iss WHERE issue_to_email IN ('" . $cc . "')";
                    $result = mysql_query($sql);
                    $row = mysql_fetch_row($result);
                    if ($row > 0) {
                        $usersCC.= empty($usersCC) ? $row[0] : ", " . $row[0];
                    } else {
                        $usersCCArr = explode('@', $cc);
                        $usersCC.= empty($usersCC) ? $usersCCArr[0] : ", " . $usersCCArr[0];
                    }
                }
            }
        }

        if (!empty($usersCC)) {
            $float = 'float:left;';
        } else {
            $float = '';
        }
        $message = explode('--', $_POST['messageDetails']);
        #print_r($_REQUEST);die;
        $imgPath = 'http://' . DOMAIN;
        $html = '<div style="color:black;border: 1px solid; padding:5px;"><div style="float:left;"><img width="" height="70" src="' . $imgPath . 'images/logo.png" height="40"  /></div><div style="float:right;"><h3>WISEWORKING PTY LTD</h3><strong>416 High Street<br>Kew, Victoria, 3068</strong></div><br><br><br><br clear="both"><h1>REQUEST FOR INFORMATION</h1><br><div><span style="width:85px;float:left;"><b>TO: </b></span><span>' . $usersTo . '</span><hr><br><span style="width:85px;' . $float . '"><b>CC: </b></span><span>' . $usersCC . '</span><hr><br><span style="width:85px;float:left;"><b>FROM: </b></span><span>' . $_SESSION['ww_builder_full_name'] . ', WISEWORKING PTY LTD</span><hr><br><span style="width:85px;float:left;"><b>DATE: </b></span><span>' . date('j F Y') . '</span><hr><br><span style="width:85px;float:left;"><b>RFI #: </b></span><span>' . $RFInumber . '</span><hr><br><span style="width:85px;float:left;"><b>PROJECT: </b></span><span>' . $projectName[0]['project_name'] . '</span><hr><br><span style="width:90px;float:left;"><b>RESPOND BY: </b></span><span>&nbsp;' . date('j F Y', strtotime($_POST['fixedByDate'])) . '</span><hr><br><span style="width:85px;float:left;"><b>SUBJECT: </b></span><span>' . $subject . '</span><hr><br><div style="width:85px;"><big><b>SUMMARY: </b></big></div><br><div>' . $message[0] . '</div><br><span style="width: 400px;float:left">' . $_SESSION['ww_builder_full_name'] . '</span><span style="width:400px;">' . date('d/m/Y') . '</span><br clear="both"><span style="width: 200px;float:left"><b>Issued By  </b></span><span style="width: 200px;float:left"><b>Signature</b></span><span style="width:400px;"><b>Date</b></span><br><!--hr><div><b>Action Taken (If Applicable)</b></div><br><br><hr><div><b>Actions to be taken:
</b></div><br><span style="float:left;width: 320px;">Action</span><span style="float:left;width: 100px;">Due Date</span><span style="width:400px;">Complete Date</span><br><br></div--><br><br></div>';
        $messageDetails = $html;
    }
    $ccAddress = '';
    $toExtraAddress = '';
    if ($_POST['emailAttachedAjax'] == 1) {
        $attahment1 = $_SESSION[$_SESSION['idp'] . '_emailfile'];
    }
    // Remove old attachment if found any attachment

    if (isset($_POST['removeAttachment'])) {
        if (explode(',', str_replace(', ', ',', $_POST['removeAttachment']))) {
            $removeAttachments = explode(',', str_replace(', ', ',', $_POST['removeAttachment']));
            foreach ($removeAttachments as $attachID) {
                $key = "";
                $key = array_search(trim($attachID), $_SESSION[$_SESSION['idp'] . '_emailfile']);
                if ($key != "")
                    unset($_SESSION[$_SESSION['idp'] . '_emailfile'][$key]);
                if (is_numeric($attachID)) {
                    $attDeleteQRY = "UPDATE `pmb_attachments` SET `is_deleted` = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '" . $userId . "' WHERE project_id = " . $_SESSION['idp'] . " AND `attach_id` =" . $attachID;
                    mysql_query($attDeleteQRY);
                }
            }
        } else {
            $key = "";
            $key = array_search(trim($attachID), $_SESSION[$_SESSION['idp'] . '_emailfile']);
            if ($key != "")
                unset($_SESSION[$_SESSION['idp'] . '_emailfile'][$key]);
            if (is_numeric($attachID)) {
                $attDeleteQRY = "UPDATE `pmb_attachments` SET `is_deleted` = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '" . $userId . "' WHERE project_id = " . $_SESSION['idp'] . " AND `attach_id` =" . $attachID;
                mysql_query($attDeleteQRY);
            }
        }
    }

    if ($messageType != 'Request For Information') {
        //$subject = 'CN # '.$correspondenceNumber." ".$messageType.' '.$subject;//New Update
        $subject = $subject; //New Update
    } else {
        $subject = $projectName[0]['project_name'] . '-RFI' . $RFInumber . '-' . $subject; //New Updated Dated : 28-10-2014
        //$subject = 'RFI # '.$RFInumber." ".$subject;//New Updated Dated : 05-12-2013
    }
//Code for New Document Transmittal Template Start Here
    if ($messageType == 'Document Transmittal') {
        //Data Fetch Section Start Here
        $recipTo = !empty($_POST['recipTo']) ? $_POST['recipTo'] : array(0);
        if (isset($recipTo)) {
            $toArray = array();
            foreach ($recipTo as $to) {
                $toArray[] = $to;
            }
        }//condition for to
        #print_r($toArray);die;
        $ts = '';
        $ij = 0;
        foreach ($toArray as $tt) {
            $ts .= "'" . $tt . "'";
            if ($ij < sizeof($toArray) - 1) {
                $ts .=", ";
            }
            $ij++;
        }
        //echo $ts;die;
        //cc condition
        if (isset($recipCC)) {
            $ccArray = array();
            foreach ($recipCC as $cc) {
                $ccArray[] = $cc;
            }
        }
        //condition for cc
        $tscc = '';
        $ijcc = 0;
        foreach ($ccArray as $ccval) {
            $tscc .= "'" . $ccval . "'";
            if ($ijcc < sizeof($ccArray) - 1) {
                $tscc .=", ";
            }
            $ijcc++;
        }
        //cc condition end
        //condition for to<br>
        $ToData = $object->selQRYMultiple('user_id, company_name, user_name, user_fullname, user_email', 'user', 'user_id IN(' . $ts . ')');
        $toArrayMainVal = array();
        foreach ($ToData as $ToDataVal) {
            $toArrayMainVal[$ToDataVal['user_id']] = $ToDataVal;
        }
        $usersTo = '';
        $companys = '';
        $ijz = 0;
        foreach ($toArray as $toArrayVa) {
            if (!empty($toArrayMainVal[$toArrayVa]) && isset($toArrayMainVal[$toArrayVa])) {
                $usersTo .= $toArrayMainVal[$toArrayVa]['user_fullname'];
                $companys .= $toArrayMainVal[$toArrayVa]['company_name'] . "  ";
                if ($ijz < sizeof($toArrayMainVal) - 1) {
                    $usersTo .=", ";
                    $companys .=", ";
                }
                $ijz++;
            } else {
                $usersToArr = explode('@', $toArrayVa);
                $usersTo .= $usersToArr[0];
                if ($ijz < sizeof($toArray) - 1) {
                    $usersTo .=", ";
                    $companys .=", ";
                }
                $ijz++;
            }
        }
        #echo $usersTo;die;
        //cc condition strats here
        #echo $tscc;
        $cccData = $object->selQRYMultiple('user_id, company_name, user_name, user_fullname, user_email', 'user', 'user_email IN(' . $tscc . ')');
        $toArrayMainValcc = array();
        foreach ($cccData as $ToDataValcc) {
            $toArrayMainValcc[$ToDataValcc['user_email']] = $ToDataValcc;
        }
        $usersTocc = '';
        $companyscc = '';
        $ijzcc = 0;
        #print_r($toArrayMainValcc);
        foreach ($ccArray as $toArrayVacc) {
            if (!empty($toArrayMainValcc[$toArrayVacc]) && isset($toArrayMainValcc[$toArrayVacc])) {
                $usersTocc .= $toArrayMainValcc[$toArrayVacc]['user_fullname'] . "  ";
                $companyscc .= $toArrayMainValcc[$toArrayVacc]['company_name'] . "  ";
                if ($ijzcc < sizeof($toArrayMainValcc) - 1) {
                    $usersTocc .=", ";
                    $companyscc .=", ";
                }
                $ijzcc++;
            } else {
                $usersToArrcc = explode('@', $toArrayVa);
                $usersTocc .= $usersToArrcc[0];
                if ($ijzcc < sizeof($toArraycc) - 1) {
                    $usersTocc .=", ";
                    $companyscc .=", ";
                }
                $ijz++;
            }
        }
        #echo $usersTocc;die;
        $docRegData = $object->selQRYMultiple('dr.id, dr.title, dr.number, dr.revision, dr.comments', 'drawing_register_module_one AS dr, drawing_register_revision_module_one AS drr', 'drr.id IN (' . join(',', $_SESSION[$_SESSION['idp'] . '_dtReportids']) . ') AND drr.is_deleted = 0 AND dr.is_deleted = 0 AND drr.project_id = ' . $_SESSION['idp'] . ' AND dr.project_id = ' . $_SESSION['idp'] . ' AND drr.drawing_register_id = dr.id', 'test');

        $corNumCount = array();
        $corNumCount = $object->selQRYMultiple('max(m.correspondence_number) AS correspondenceNumber', 'pmb_message as m, pmb_user_message as um', 'um.project_id = ' . $_SESSION['idp'] . ' AND m.is_draft = 0 AND um.is_deleted= 0 AND m.message_id = um.message_id AND m.message_type = "Document Transmittal"');

        $companyDetailsData = $object->selQRYMultiple('trading_name, comp_email, comp_mobile, comp_businessadd1, comp_businessadd2, comp_bussuburb, comp_businessstate, comp_businesscountry, website', 'pms_companies', 'active = 1');

        //Data Fetch Section End Here
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetTopMargin(20);
        //Comapany Logo and Head Section
        $pdf->SetFont('times', 'B', 18);
        $pdf->MultiCell(0, 5, $companyDetailsData[0]['trading_name']);

        $pdf->SetFont('times', 'B', 10);
        //$pdf->MultiCell(0, 5, 'A.C.N. 119 426 952');		
        $pdf->MultiCell(0, 5, $companyDetailsData[0]['comp_businessadd1'] . ', ' . $companyDetailsData[0]['comp_businessadd2'] . ', ' . $companyDetailsData[0]['comp_bussuburb'] . ', ' . $companyDetailsData[0]['comp_businessstate'] . ', ' . $companyDetailsData[0]['comp_businesscountry']);
        $pdf->MultiCell(0, 5, 'P: ' . $companyDetailsData[0]['comp_mobile']);
        $pdf->Image('./company_logo/logo.png', 125, 5, 'png', -100);
        $pdf->Ln(3);

        $pdf->Line($pdf->GetX() - 10, $pdf->GetY() - 1, $pdf->GetX() + 200, $pdf->GetY() - 1);
        $pdf->SetFont('times', '', 16);
        $pdf->Cell(160, 5, 'TRANSMITTAL OF DOCUMENTS', 0, 0);
        $pdf->SetFont('times', '', 10);
        $pdf->Cell(0, 5, 'Date: ' . date('d/m/Y'), 0, 0);
        $pdf->Ln();
        $pdf->Line($pdf->GetX() - 10, $pdf->GetY() + 2, $pdf->GetX() + 200, $pdf->GetY() + 2);
        $pdf->Ln(5);

        $pdf->Cell(100, 5, 'PROJECT: ' . $projectName[0]['project_name'], 0, 0);
        $pdf->Cell(120, 5, 'PROJECT NUMBER: ' . $projectName[0]['job_number'], 0, 0);
        $pdf->Ln();
        $pdf->Line($pdf->GetX() - 10, $pdf->GetY(), $pdf->GetX() + 200, $pdf->GetY());
        //To List Section
        $pdf->Ln(5);
        $pdf->SetFont('times', 'B', 10);
        $pdf->MultiCell(0, 5, 'To: ' . $usersTo);
        //CC List Section
        $pdf->MultiCell(0, 5, 'CC: ' . $usersTocc);
        $correspondenceNumber = ++$corNumCount[0]['correspondenceNumber'];
        $pdf->Ln(5);
        $pdf->SetFont('times', '', 8);
        $pdf->MultiCell(0, 5, 'REF. No. ' . $correspondenceNumber);
#		$pdf->Line($pdf->GetX()-10, $pdf->GetY(), $pdf->GetX()+200, $pdf->GetY());
#		$pdf->MultiCell(0, 5, 'ATTENTION:');
#		$pdf->Line($pdf->GetX()-10, $pdf->GetY(), $pdf->GetX()+200, $pdf->GetY());
        $pdf->MultiCell(0, 5, 'We forward the following documents:');

        $pdf->SetFont('times', 'B', 9);
        $header = array("REF. No.", "REV.", "DESCRIPTION", "REMARKS");
        $pdf->SetWidths(array(40, 30, 60, 60));
        $best_height = 17;
        $pdf->Row($header);
        $pdf->SetFont('times', '', 8);
        foreach ($docRegData as $docData) {
            $pdf->Row(array($docData['number'], $docData['revision'], $docData['title'], $docData['comments']));
        }
        $pdf->Ln(5);
        $pdf->SetFont('times', 'B', 8);
        $pdf->Cell(9, 5, 'FOR:', 0, 0);
        $pdf->SetFont('times', 'BI', 8);
        $pdf->Cell(50, 5, $subject1, 0, 0);

        //File Output Section 
        $docTranFile = $_SESSION['idp'] . "_" . rand() . ".pdf";
        $path = "./attachment/" . $docTranFile;
        array_push($attahment1, $docTranFile); //Add in Attachment
        array_push($_SESSION[$_SESSION['idp'] . '_emailfile'], $docTranFile); //Add in Attachment
        array_push($_SESSION[$_SESSION['idp'] . '_orignalFileName'], "Document Transmittal.pdf"); //Add in Attachment
        $pdf->Output($path);
    }
//Code for New Document Transmittal Template End Here
    if ((!empty($recipTo) && !empty($subject) && !empty($messageDetails)) || isset($_POST['saveDraft'])) {

 		# Start :- Send Email
		$mail = new PHPMailer(true);
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPSecure = 'tls'; //"ssl";	// sets the prefix to the servier
		$mail->Host = smtpHost; 	//pod51022.outlook.com      // sets GMAIL as the SMTP server
		$mail->SMTPDebug = 0;				// enables SMTP debug information (for testing)
		$mail->SMTPAuth = true;        		// enable SMTP authentication
		$smtpPort = smtpPort;
		if(!empty($smtpPort)){
			$mail->Port =  smtpPort; //587;
		}
		$mail->Username = smtpUsername; //"wiseworkingsales@gmail.com"; // SMTP account username
		$mail->Password = smtpPassword; //"Wiseworking123";   // SMTP account password
		$mail->isHTML(true);
		
		$mail->AddReplyTo("WiseworkingSystems@wiseworking.com.au", "WiseWorker - ".$projectName[0]['project_name']." - ".$_SESSION['ww_builder']['user_fullname']);
		$mail->SetFrom("WiseworkingSystems@wiseworking.com.au", "WiseWorker - ".$projectName[0]['project_name']." - ".$_SESSION['ww_builder']['user_fullname']);

		$path = 'http://' . str_replace('/', '', str_replace('http://', '', DOMAIN));
        $mail->IsHTML(true);
        $byEmail = 3;
        /*if($messageType == 'Request For Information')//New Updated Dated : 05-12-2013
		$msg .= ' RFI #'.$RFInumber.'<br><br>'; */
        $msg .= $messageDetails;

        if (isset($recipCC)) {
            foreach ($recipCC as $cc) {
                $mail->AddCC(trim($cc), '');
                $ccAddress.= ($ccAddress == '') ? $cc : ', ' . $cc;
            }
        }

        if (isset($recipTo) && !empty($recipTo)) {
            foreach ($recipTo as $to) {
                if (is_numeric($to)) {
                    $query = "select u.user_id, u.user_type, u.user_fullname as fullname, u.user_email as email from user as u where u.is_deleted=0 and u.user_id='" . $to . "'";
                    $getUserDetails = mysql_query($query);
                    while ($gUDetail = mysql_fetch_array($getUserDetails)) {
                        $mail->AddAddress($gUDetail['email'], ''); // To
                    }
                } elseif (!empty($to)) {
                    $mail->AddAddress($to, ''); // To
                    $toExtraAddress.= ($toExtraAddress == '') ? $to : ', ' . $to;
                }
            }
        }
//Message Attachments Start Here
        if (isset($attahment1) && !empty($attahment1)) {
            foreach ($attahment1 as $key => $value) {
                $ext = end(explode('.', $value));          
                if ($_SESSION[$_SESSION['idp'] . '_orignalFileName'][$key] == "Document Transmittal.pdf") {
                    if($ext == "zip" || $ext == "exe"){

                    }else{ 
                        $mail->AddAttachment("attachment/" . $value, $_SESSION[$_SESSION['idp'] . '_orignalFileName'][$key]);
                    }
                } else {
                    if($ext == "zip" || $ext == "exe"){
                    
                    }else{
                        $mail->AddAttachment("attachment/" . $value, $_SESSION[$_SESSION['idp'] . '_orignalFileName'][$key] . "." . $ext);
                    }
                }
            }
        }
//Message Attachments End Here
        # Save message in PMB
        if (get_magic_quotes_gpc()) {
            $messageDetails = stripslashes($messageDetails);
        }

        $messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));
        $recipTo = !empty($_POST['recipTo']) ? $_POST['recipTo'] : array(0);
        if (isset($recipTo)) {
            if ($_POST['composeId'] != 0 && isset($_POST['save'])) {
                $messgeId = 0; //Forcfully create new message and delete old one due to draft issue.
            }
            foreach ($recipTo as $to) {
                if (is_numeric($to)) {
                    $messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageType, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation);
                    if (!empty($messageBoard))
                        $messgeId = $messageBoard['messgeId'];
                }else {
                    $messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageType, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation);
                    if (!empty($messageBoard))
                        $messgeId = $messageBoard['messgeId'];
                }
                $attahment1 = '';
            }
        }
//Added for display message for cc sections Start Here
        if (isset($ccUsserIDArr)) {
            foreach ($ccUsserIDArr as $ccInsertEmail => $ccInsertId) {
                $messageBoard = $object->messageBoard($projectId, $from, $ccInsertId, $subject, $messageType, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 1, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation);
                $messgeId = $messageBoard['messgeId'];
            }
        }
//Added for display message for cc sections End Here
        $msg .= "<br/><br/>click here to access your message.<br>
						<a href='" . $path . "/pms.php?sect=message_details&id=" . base64_encode($messageBoard['thread_id']) . "&type=inbox&projID=" . $_SESSION['idp'] . "&byEmail=" . $byEmail . "' target='_blank'>" . $path . "/pms.php?sect=message_details&id=" . base64_encode($messageBoard['thread_id']) . "&type=inbox&projID=" . $_SESSION['idp'] . "&byEmail=" . $byEmail . "</a>";
        $msg .= "<br/><br/>Thanks,<br> DefectId customer care";

		$mail->Subject = "WiseWorker:" . $messgeId . "-" . $subject;
        
		$mail->MsgHTML($msg);

        if (isset($_POST['save']) && !empty($messageBoard)) {
            $result = $mail->Send();
        }
        $mail->ClearAddresses();
        $mail->ClearAllRecipients();
        $mail->ClearCustomHeaders();

        if ($_POST['composeId'] != 0 && isset($_POST['save']) && !empty($messageBoard)) {//Forcfully create new message and delete old one due to draft issue.
            mysql_query("UPDATE pmb_message SET is_draft = 0, is_deleted = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '" . $userId . "' WHERE message_id = " . $_POST['composeId']);
            mysql_query("UPDATE pmb_user_message SET is_deleted = 1, type = 'delete', last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '" . $userId . "' WHERE message_id = " . $_POST['composeId']);
        }
# Add custom entry in address book
        if (!empty($_POST['customEmailEntry'])) {
            if (explode(',', $_POST['customEmailEntry'])) {
                $customEmails = explode(', ', $_POST['customEmailEntry']);
            } else {
                $customEmails[] = $_POST['customEmailEntry'];
            }
            foreach ($customEmails as $newEmail) {
                $name = explode('@', $newEmail);
                $fullName = trim(addslashes($name[0]));
                $userEmail = trim(addslashes($newEmail));

                $inAddressBook = $object->selQRYMultiple('full_name', 'pmb_address_book', 'full_name="' . $fullName . '" AND user_email="' . $userEmail . '" AND project_id="' . $_SESSION['idp'] . '"');
                if (!isset($inAddressBook[0]['full_name'])) {
                    $inssertQRY = "INSERT INTO pmb_address_book SET
							project_id = '" . $_SESSION['idp'] . "',
							full_name = '" . $fullName . "',
							user_email = '" . $userEmail . "',
							last_modified_date = NOW(),
							last_modified_by = '" . $_SESSION['ww_builder_id'] . "',
							created_date = NOW(),
							created_by = '" . $_SESSION['ww_builder_id'] . "'";
                    mysql_query($inssertQRY);
                }
            }
        }

# Add custom entry in pmb_correspondences_tags
        if (!empty($_POST['customCompanyTag'])) {
            if (explode(',', $_POST['customCompanyTag'])) {
                $customCompanyTag = explode(', ', $_POST['customCompanyTag']);
            } else {
                $customCompanyTag[] = $_POST['customCompanyTag'];
            }
            foreach ($customCompanyTag as $newCompanyTag) {
                $cmpTag = trim(addslashes($newCompanyTag));

                $getCompanyTag = $object->selQRYMultiple('correspondences_tags', 'pmb_correspondences_tags', 'correspondences_tags="' . $cmpTag . '" AND project_id="' . $_SESSION['idp'] . '"');
                if (!isset($getCompanyTag[0]['correspondences_tags'])) {
                    $inssertQRY = "INSERT INTO pmb_correspondences_tags SET
							project_id = '" . $_SESSION['idp'] . "',
							correspondences_tags = '" . $cmpTag . "',
							last_modified_date = NOW(),
							last_modified_by = '" . $_SESSION['ww_builder_id'] . "',
							created_date = NOW(),
							created_by = '" . $_SESSION['ww_builder_id'] . "'";
                    mysql_query($inssertQRY);
                }
            }
        }
        if (isset($_POST['save']) && $_POST['RFIstatus'] != 'Draft') {
            header('Location:?sect=sent_box&sm=1');
            ?><script language="javascript" type="text/javascript">window.location.href = "?sect=sent_box&sm=1";</script><?php
        } else {//echo 'Location:?sect=drafts&sm=1'; 
            header('Location:?sect=drafts&sm=1');
            ?><script language="javascript" type="text/javascript">window.location.href = "?sect=drafts&sm=1";</script><?php
        }//$_GET['msgid'] = $messgeId;
    } else {
        echo $messageBoard;
    }
}

if (sizeof($_GET) == 2 || !isset($_GET['attached'])) {
	unset($_SESSION[$_SESSION['idp'].'_orignalFileName']);
	unset($_SESSION[$_SESSION['idp'].'_emailfile']);
	unset($_SESSION[$_SESSION['idp'].'_remaimberData']);
	unset($_SESSION[$_SESSION['idp'].'_pmbEmailfile']);	
}

function getattachment($mid) {
    $req1 = mysql_query('select attach_id, name, attachment_name from pmb_attachments where message_id="' . $mid . '" and is_deleted=0');
    $row = mysql_fetch_array($req1);
    return $row;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <link href="css/email.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="css/chosen.css">
        <style>
            label { color:#000000;}
            .textEditer{ color:#000; padding-left:10px; }
            .nicEdit-main{ outline:none; }
            .Compose .error{ margin-left:20px; }
            span.reqire{/*	display:block;*/}
            .chzn-drop{ text-transform:capitalize; }
            #messageDetails{ color:#000; }
            #imageName{ float:left; margin-left:20px; margin-top:10px;  width: 410px; }
            #imageName span{ padding-left:15px; }
            #imageName a{ cursor:pointer; padding-right:5px; }
			.chzn-container .chzn-results { float:left; width: 100%; }

            .upload-image { height: auto; }
        </style>
    </head>
    <body>
        <div class="GlobalContainer clearfix">
                    <?php include 'message_side_menu.php'; ?>
                    <?php
                    //$projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name');
                    $refrenceNo = explode(' ', $projectName);
                    if (is_array($refrenceNo)) {
                        $refrenceNo = strtolower($refrenceNo[0]);
                    } else {
                        $refrenceNo = strtolower($projectName);
                    }
                    ?>
            <div class="MailRight">
                <div class="MailRightHeader">
                    <h2 style="color:#000000; margin-top:10px; margin-left:10px; float:left;">Compose Message</h2>
                    <?php //if (isset($_GET['folderType']) && $_GET['folderType'] == "General Correspondence" && isset($_GET['attached']) && $_GET['attached'] == "Y") { ?>
                        <!-- <a href="pms.php?sect=messages&folderType=<  ?php echo $_GET['folderType']; ?>" style="cursor:pointer"><img src="images/back.png" width="79" height="34" alt="Back" style="float:right; margin:5px;" /></a> -->
                        <a href="pms.php?sect=messages&folderType=<?php echo $_GET['folderType']; ?>" class="sideMenu mail" style="float:right;">
                            <i class="back"></i>
                            <span>Back</span>
                        </a>
                    <?php //} ?>
                    <h3 style="color:#000000; margin-top:10px; margin-right:180px; float:right;">Project Name : <?= isset($projectName[0]) ? $projectName[0]['project_name'] : $projectName ?></h3>
                </div>
                <div class="Compose clearfix" style="color:#050505;">
                    <?php
                    $comMessData = array();
                    $toList = array();
                    $ccList = array();
                    if (isset($_GET['msgid']) && $_GET['msgid'] != 0) {
                        $comMessData = $object->selQRYMultiple('m.message_id, um.user_id, um.type, m.title, m.message_id, m.sent_time, m.message, um.from_id, m.message_type, m.to_email_address, m.cc_email_address, m.tags, um.rfi_number AS RFInumber, m.rfi_fixed_by_date AS fixedByDate, m.rfi_status AS RFIstatus, m.correspondence_number as correspondenceNumber, m.company_tag as companyTag, m.purchaser_location, m.to_email_hide, m.cc_email_hide, m.image_compose, m.rfi_reference', 'pmb_user_message um , pmb_message m', 'm.message_id="' . $_GET['msgid'] . '" AND um.message_id = m.message_id AND um.type="sent"');

                        foreach ($comMessData as $val) {
                            $toList[] = $val['from_id'];
                        }
                        if (!empty($comMessData[0]['to_email_address'])) {
                            if (explode(',', $comMessData[0]['to_email_address'])) {
                                $toExtraList = explode(', ', $comMessData[0]['to_email_address']);
                            } else {
                                $toExtraList[] = $comMessData[0]['to_email_address'];
                            }
                            $toList = array_merge($toList, $toExtraList);
                        }
                        if (!empty($comMessData[0]['cc_email_address'])) {
                            if (explode(',', $comMessData[0]['cc_email_address'])) {
                                $ccList = explode(', ', $comMessData[0]['cc_email_address']);
                            } else {
                                $ccList[] = $comMessData[0]['cc_email_address'];
                            }
                        }
                        $composeImage = '';
                        $imageCompose = '';
                        if (!empty($comMessData[0]['image_compose'])) {
                            $imageCompose = $comMessData[0]['image_compose'];
                            $imgCompose = "attachment/".$imageCompose;
                            if(file_exists($imgCompose)){
                                $composeImage = $imgCompose;
                            }
                        }

                        //$attachemnt=getattachment($_GET['msgid']);
                        $attachemnts = $object->selQRYMultiple('attach_id, name, attachment_name', 'pmb_attachments', 'message_id="' . $_GET['msgid'] . '" and is_deleted=0');
                    }
                    $userData = $object->selQRYMultiple('user_name, user_fullname, user_email, pmb_signature, user_signature', 'user', 'is_deleted = 0 AND user_id = ' . $userId);
                    ?>
                    <form action="" method="post" enctype="multipart/form-data" id="compose">
                        <table style="margin-left:20px;" cellpadding="5" cellspacing="15">
                            <tr>
                                <td width="400px" align="left" valign="top">
                                    <label for="name">External email<span class="reqire"> *</span></label>
                                </td>
                                <td><input type="checkbox" id="external_email" name="external_email" value="1" style="float:left;width:15px;margin-left:10px;" ></td>	
                            </tr>

                            <tr id="to_tr">
                                <td width="400px" align="left" valign="top">
                                    <label for="name">To<span class="reqire"> *</span></label>
                                </td>
                                <td width="700px" align="left" valign="top" id="recipToSection" >
                                            <?php
                                            $projectUsers = $object->selQRYMultiple('u.user_id, u.user_fullname, u.company_name, user_email, up.map_user_id, up.map_with', 'user as u Left Join user_projects as up on u.user_id = up.user_id  and up.is_deleted=0', 'u.user_id!="' . $_SESSION['ww_builder_id'] . '" AND u.is_deleted=0 AND up.project_id="' . $_SESSION['idp'] . '" order by u.user_name', 'test');
                                            #echo "<pre>";print_r($projectUsers);		
                                            $projectIssues = $object->selQRYMultiple('issue_to_id, issue_to_name, company_name, issue_to_email ', 'inspection_issue_to', 'is_deleted=0 AND project_id="' . $_SESSION['idp'] . '" AND issue_to_name!="NA" AND issue_to_email!="" order by issue_to_name');
                                            #echo "<pre>";print_r($projectIssues);
                                            $projectAddresBookUsers = $object->selQRYMultiple('id, full_name, company_name, user_email', 'pmb_address_book', 'is_deleted=0 AND project_id="' . $_SESSION['idp'] . '" AND full_name != "" order by full_name');
                                            $mapedIssuedTo = array();
                                            $mapedAddressBook = array();
                                            #echo "<pre>";print_r($projectUsers);print_r($projectIssues);echo "</pre>";
                                            ?>

                                    <select name="recipTo[]" id="recipTo" style="width:350px;" class="chzn-select chzn-custom-value" multiple> 
                                        <optgroup label="Project users">
                                            <?php
                                            foreach ($projectUsers as $puser) {
                                                if ($puser['map_user_id'] > 0 && $puser['map_with'] == "addressbook") {
                                                    $mapedAddressBook[] = $puser['map_user_id'];
                                                }
                                                if ($puser['map_user_id'] > 0 && $puser['map_with'] == "issuedto") {
                                                    $mapedIssuedTo[] = $puser['map_user_id'];
                                                }
                                                $select = "";
                                                if (in_array($puser['user_id'], $toList)) {
                                                    $select = "selected = 'selected'";
                                                }
                                                if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && in_array($puser['user_id'], $_SESSION[$_SESSION['idp'] . '_remaimberData']['recipTo'])) {
                                                    $select = "selected = 'selected'";
                                                }
                                                ?>
                                                <option value="<?php echo $puser['user_id']; ?>" <?php echo $select; ?>><?php
                                                    if (!empty($puser['user_fullname']) && !empty($puser['company_name'])) {
                                                        echo strtolower($puser['user_fullname'] . " ( " . $puser['company_name'] . " )");
                                                    } elseif (empty($puser['user_fullname']) && !empty($puser['company_name'])) {
                                                        echo strtolower("( " . $puser['company_name'] . " )");
                                                    } else {
                                                        echo strtolower($puser['user_fullname']);
                                                    }
                                                    ?></option><?php } ?>
                                        </optgroup>

                                        <optgroup label="Issued To">
                                            <?php
                                            foreach ($projectIssues as $pIssue) {
                                                $select = "";
                                                if (in_array($pIssue['issue_to_email'], $toList)) {
                                                    $select = "selected = 'selected'";
                                                }
                                                if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && in_array($pIssue['issue_to_email'], $_SESSION[$_SESSION['idp'] . '_remaimberData']['recipTo'])) {
                                                    $select = "selected = 'selected'";
                                                }
                                                #print_r($mapedIssuedTo);		
                                                if (!in_array($pIssue['issue_to_id'], $mapedIssuedTo)) {
                                                    ?>
                                                    <option value="<?php echo $pIssue['issue_to_email']; ?>" <?php echo $select; ?>><?php
                                                        if (!empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])) {
                                                            echo strtolower($pIssue['company_name'] . " ( " . $pIssue['issue_to_name'] . " )");
                                                        } elseif (empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])) {
                                                            echo strtolower("( " . $pIssue['issue_to_name'] . " )");
                                                        } else {
                                                            echo strtolower($pIssue['company_name']);
                                                        }
                                                        ?></option><?php }
                                                } ?>
                                        </optgroup>

                                        <optgroup label="Ad hoc (External)">
                                        <?php
                                        foreach ($projectAddresBookUsers as $addresBookUsers) {
                                            $select = "";
                                            if (in_array($addresBookUsers['user_email'], $toList)) {
                                                $select = "selected = 'selected'";
                                            }
                                            if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && in_array($addresBookUsers['user_email'], $_SESSION[$_SESSION['idp'] . '_remaimberData']['recipTo'])) {
                                                $select = "selected = 'selected'";
                                            }
                                            if (!in_array($addresBookUsers['id'], $mapedAddressBook)) {
                                                ?>
                                                <option value="<?php echo strtolower($addresBookUsers['user_email']); ?>" <?php echo $select; ?>><?php
                                            if (!empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])) {
                                                echo strtolower($addresBookUsers['full_name'] . " ( " . $addresBookUsers['company_name'] . " )");
                                            } elseif (empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])) {
                                                echo strtolower("( " . $addresBookUsers['company_name'] . " )");
                                            } else {
                                                echo strtolower($addresBookUsers['full_name']);
                                            }
                                            ?></option><?php }
                                        } ?>
                                        </optgroup>
                                    </select>
                                    <div class="error-edit-profile" style="display:none;"  id="recipToError">You have not entered any mail Recipients.</div>
                                    <div class="error-edit-profile" style="display:none;"  id="emailError">Invalide email format.</div>
                                </td>
                            </tr>
                            
                            <tr  id="cc_tr">
                                <td align="left" valign="top">
                                    <label for="name">CC</label>
                                </td>
                                <td align="left" valign="top">
                                    <!--input type="text" name="recipCC" id="recipCC" /-->
                                    <select name="recipCC[]" id="recipCC" style="width:350px;" multiple class="chzn-select chzn-custom-value" multiple > 
                                        <optgroup label="Project users">
                                                <?php
                                                foreach ($projectUsers as $puser) {
                                                    $select = "";
                                                    if (in_array($puser['user_email'], $ccList)) {
                                                        $select = "selected = 'selected'";
                                                    }
                                                    if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && in_array($puser['user_email'], $_SESSION[$_SESSION['idp'] . '_remaimberData']['recipCC'])) {
                                                        $select = "selected = 'selected'";
                                                    }
                                                    ?>
                                                <option value="<?php echo $puser['user_email']; ?>" <?php echo $select; ?>><?php
                                                if (!empty($puser['user_fullname']) && !empty($puser['company_name'])) {
                                                    echo strtolower($puser['user_fullname'] . " ( " . $puser['company_name'] . " )");
                                                } elseif (empty($puser['user_fullname']) && !empty($puser['company_name'])) {
                                                    echo strtolower("( " . $puser['company_name'] . " )");
                                                } else {
                                                    echo strtolower($puser['user_fullname']);
                                                }
                                                ?></option><?php } ?>
                                        </optgroup>

                                        <optgroup label="Issued To">
                                                <?php
                                                foreach ($projectIssues as $pIssue) {
                                                    $select = "";
                                                    if (in_array($pIssue['issue_to_email'], $ccList)) {
                                                        $select = "selected = 'selected'";
                                                    }
                                                    if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && in_array($pIssue['issue_to_email'], $_SESSION[$_SESSION['idp'] . '_remaimberData']['recipCC'])) {
                                                        $select = "selected = 'selected'";
                                                    }
                                                    if (!in_array($pIssue['issue_to_id'], $mapedIssuedTo)) {
                                                        ?>
                                                    <option value="<?php echo $pIssue['issue_to_email']; ?>" <?php echo $select; ?>><?php
                                                        if (!empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])) {
                                                            echo strtolower($pIssue['company_name'] . " ( " . $pIssue['issue_to_name'] . " )");
                                                        } elseif (empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])) {
                                                            echo strtolower(" ( " . $pIssue['issue_to_name'] . " )");
                                                        } else {
                                                            echo strtolower($pIssue['company_name']);
                                                        }
                                                        ?></option><?php }
                                                } ?>
                                        </optgroup>

                                        <optgroup label="Ad hoc (External)">
                                    <?php
                                    foreach ($projectAddresBookUsers as $addresBookUsers) {
                                        $select = "";
                                        if (in_array($addresBookUsers['user_email'], $ccList)) {
                                            $select = "selected = 'selected'";
                                        }
                                        if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && in_array($addresBookUsers['user_email'], $_SESSION[$_SESSION['idp'] . '_remaimberData']['recipCC'])) {
                                            $select = "selected = 'selected'";
                                        }
                                        if (!in_array($addresBookUsers['id'], $mapedAddressBook)) {
                                            ?>
                                                    <option value="<?php echo strtolower($addresBookUsers['user_email']); ?>" <?php echo $select; ?>><?php
                                            if (!empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])) {
                                                echo strtolower($addresBookUsers['full_name'] . " ( " . $addresBookUsers['company_name'] . " )");
                                            } elseif (empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])) {
                                                echo strtolower("( " . $addresBookUsers['company_name'] . " )");
                                            } else {
                                                echo strtolower($addresBookUsers['full_name']);
                                            }
                                            ?></option><?php }
                            } ?>
                                        </optgroup>
                                    </select>
                                </td>
                            </tr>
                            <tr id="purchaserLocationRow" style="display:none;">
                                <td align="left" valign="top">Purchaser Location</td>
                                <td align="left" valign="top" >
                                    <input type="text" size="40" value="<?php echo htmlentities($_POST['purchaserLocation'], ENT_QUOTES, 'UTF-8'); ?><?php if (isset($comMessData[0]['purchaser_location'])) {
                                        echo $comMessData[0]['purchaser_location'];
                                    } ?><?php if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && $_SESSION[$_SESSION['idp'] . '_remaimberData']['purchaserLocation'] != "") {
                                        echo $_SESSION[$_SESSION['idp'] . '_remaimberData']['purchaserLocation'];
                                    } ?>" id="purchaserLocation" name="purchaserLocation" style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;" />
                                </td>
                            </tr>        
                            <tr style=" <?php echo (isset($_GET['folderType']) && $_GET['folderType'] != "")?'display:none;':''; ?>">
                                <td align="left" valign="top">
                                    <label for="email">Message&nbsp;Type <span class="reqire">*</span></label>
                                </td>
                                <td align="left" valign="top">
                                    <?php
                                    if ($_SESSION['idp'] == 242 || $_SESSION['idp'] == 240 || $_SESSION['idp'] == 241) {
                                        #if($_SESSION['idp']==220){
                                        switch ($_SESSION['userRole']) {
                                            case 'All Defect':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Admin', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs', 'Purchaser Changes');
                                                $dontShow = array();
                                                break;

                                            case 'Builder':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Admin', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs', 'Purchaser Changes');
                                                $dontShow = array();
                                                break;


                                            case 'Architect':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes');
                                                $dontShow = array();
                                                break;


                                            case 'Structural Engineer':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction' => array(), 'Consultant Advice Notice', 'Design Changes');
                                                $dontShow = array();
                                                break;


                                            case 'Services Engineer':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes');
                                                $dontShow = array();
                                                break;


                                            case 'Superintendant':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Admin', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs', 'Purchaser Changes');
                                                $dontShow = array();
                                                break;


                                            case 'General Consultant':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes');
                                                $dontShow = array();
                                                break;


                                            case 'Building Surveyor':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes');
                                                $dontShow = array();
                                                break;


                                            case 'Subcontractor - Tender':
                                                $msgType = array(); //array('General Correspondence' => array(), 'Document' => array('Document Transmittal'), 'Memorandum' => array(), 'Site Instruction' => array(), 'Architect / Superintendant Instruction' => array(), 'Consultant Advice Notice' => array(), 'Design Changes' => array(), 'Contract Admin' => array(), 'Recommendation' => array(), 'Tenders' => array(), 'Variation Claims' => array(), 'Progress Claims' => array(), 'Purchaser Changes' => array());
                                                $dontShow = array("Inspections", "Request For Information", "Meetings");
                                                break;

                                            case 'Sub Contractor':
                                                $msgType = array('General Correspondence', 'Site Instruction', 'Design Changes');
                                                $dontShow = array("Inspections", "Request For Information", "Meetings");
                                                break;


                                            default:
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Admin', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs', 'Purchaser Changes');
                                                break;
                                        }
                                    } else {
                                        $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Admin', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs', 'Purchaser Changes', 'Request For Information', 'Meetings');
                                    }
                                    if ($_SESSION['ww_builder']['user_type'] != "manager") {
                                        $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Admin', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs', 'Purchaser Changes', 'Request For Information', 'Meetings');
                                    }
                                    ?>
								<?php  if (isset($_GET['folderType']) && $_GET['folderType'] != "") {
                                    echo '<input name="messageType" id="messageType" value="' . $_GET['folderType'] . '" style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%); border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;" readonly = "readonly">';
                                 }else {?>
                                    <select name="messageType" id="messageType" style="width: 350px; height:28px; padding-top:4px; background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:2px; margin-left: 10px;">
                                        <!--option value="">Select</option-->
                                        <?php
                                        /* if($_SESSION['userRole'] == 'Architect'){
                                          #echo '<option value="Architect / Superintendant Instruction" selected="selected">Architect / Superintendant Instruction</option>';
                                          }elseif($_SESSION['userRole'] == 'Client'){
                                          #echo '<option value="Design Changes" selected="selected">Design Changes</option>';
                                          }elseif(isset($_GET['dcTrans']) && $_GET['dcTrans']=='Y'){
                                          #echo '<option value="Document Transmittal" selected="selected">Document Transmittal</option>';

                                          }else{ */
                                       // if (isset($_GET['folderType']) && $_GET['folderType'] != "") {
                                           // echo '<option value="' . $_GET['folderType'] . '">' . $_GET['folderType'] . '</option>';
                                       // } else {
                                            for ($i = 0; $i < sizeof($msgType); $i++) {
                                                ?>
                                                <option value="<?php echo $msgType[$i]; ?>"
                                                <?php if (!empty($comMessData) && $comMessData[0]['message_type'] == $msgType[$i]) {
                                                    echo 'selected="selected"';
                                                } ?> 
                                                <?php if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && $_SESSION[$_SESSION['idp'] . '_remaimberData']['messageType'] == $msgType[$i]) {
                                                    echo 'selected="selected"';
                                                } ?> ><?php echo $msgType[$i]; ?></option>
                                            <?php
                                            }
                                        //}
                                        //}
                                        ?>
                                    </select>
                                    <?php } ?>
                                    <div class="error-edit-profile" style="display:none;"  id="messageTypeError">The message type field is required.</div>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" valign="top"> 
                                    <label for="email" id="subjectLevelChange">Subject <span class="reqire">*</span></label>
                                </td>
                                <td align="left" valign="top">
                                    <input type="text" size="40" value="<?php echo htmlentities($_POST['title'], ENT_QUOTES, 'UTF-8'); ?><?php if (isset($comMessData[0]['title'])) {
    echo str_replace($comMessData[0]['correspondenceNumber'] . " ", "", str_replace($comMessData[0]['message_type'] . " ", "", $comMessData[0]['title']));
} ?><?php if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && $_SESSION[$_SESSION['idp'] . '_remaimberData']['subject'] != "") {
    echo $_SESSION[$_SESSION['idp'] . '_remaimberData']['subject'];
} ?>" id="subject" name="subject"  style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;"/>
                                    <div class="error-edit-profile" style="display:none;"  id="subjectError">The subject field is required.</div>
                                </td>
                            </tr>
                            <tr id="hiddenRow" style="display:none;">
                                <td align="left" valign="top">
                                    <label for="email">RFI&nbsp;# (use whole numbers e.g 1,2,3,4) <span class="reqire">*</span></label>
                                </td>
                                <td align="left" valign="top">
                                    <!--select name="RFInumber" id="RFInumber" style="width: 350px; height:28px; padding-top:4px; background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%); border: 1px solid #AAAAAA; padding-left:2px; margin-left: 10px;"-->
                                    <select name="RFInumber" id="RFInumber" style="width:350px;" class="chzn-select chzn-custom-value" >
                                        <option value="">Select</option>
                                        <option value="NA">NA</option>
                                    <?php
                                    #print_r($refNumberMsgCount);
                                    $j = (int)$refNumberMsgCount1[0]['refNumber'];
                                    if (empty($refNumberMsgCount1[0]['refNumber'])) {
                                    echo ' <optgroup label="Current RFI">';
                                    ?>
                                        <option value="<?php echo ++$refNumberMsgCount1[0]['refNumber']; ?>"><?php echo $refNumberMsgCount1[0]['refNumber']; ?></option>
                                        <?php
                                        echo '</optgroup>';
                                    } else {
                                        echo ' <optgroup label="Current RFI">';
                                        ?>
                                        <option value="<?php echo ++$refNumberMsgCount1[0]['refNumber']; ?>"><?php echo $refNumberMsgCount1[0]['refNumber']; ?></option>
                                        <?php
                                        echo '</optgroup>';

                                        echo ' <optgroup label="Old RFI">';
                                        for ($i = $j; $i > 0;  --$i) {
                                            ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php
                                        }
                                        echo "</optgroup>";
                                    }
                                    ?>
                                    </select>
                                    <!--<input type="text" id="RFInumber" name="RFInumber" value="<? //if(empty($comMessData) && empty($_SESSION[$_SESSION['idp'].'_remaimberData'])){ echo ++$refNumberMsgCount[0]['refNumber']; }?><?php //if(isset($comMessData[0]['RFInumber'])){echo $comMessData[0]['RFInumber'];} ?><?php //if(isset($_SESSION[$_SESSION['idp'].'_remaimberData']) && $_SESSION[$_SESSION['idp'].'_remaimberData']['RFInumber'] != ""){echo $_SESSION[$_SESSION['idp'].'_remaimberData']['RFInumber'];} ?>" style="margin-left:10px;" />-->
                                    <div class="error-edit-profile" style="display:none;"  id="RFInumberError">The RFI number field is required.</div>
                                    <div class="error-edit-profile" style="display:none;"  id="RFInumberErrorZero">The RFI number can not be zero.</div>
                                </td>
                            </tr>
                            <tr id="hiddenRow1" style="display:none;">
                                <td align="left" valign="top">
                                    <label for="email">Date response required <span class="reqire">*</span></label>
                                </td>
                                <td align="left" valign="top">
                                    <input type="text" name="fixedByDate" id="fixedByDate" value="<?php if (isset($comMessData[0]['fixedByDate'])) {
    echo date('d-m-Y', strtotime($comMessData[0]['fixedByDate']));
} else {
    echo date('d-m-Y', strtotime("+3 days"));
} ?><?php if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && $_SESSION[$_SESSION['idp'] . '_remaimberData']['fixedByDate'] != "") {
                                            echo $_SESSION[$_SESSION['idp'] . '_remaimberData']['fixedByDate'];
                                        } ?>" readonly style="margin-left:10px; cursor:pointer;"  />
                                    <div class="error-edit-profile" style="display:none;"  id="fixedByDateError">The Date response required field is required.</div>
                                </td>
                            </tr>
                            <tr id="rfiReferenceRow" style="display:none;">
                                <td align="left" valign="top">RFI Reference</td>
                                <td align="left" valign="top" >
                                    <input type="text" size="40" value="<?php echo htmlentities($_POST['rfi_reference'], ENT_QUOTES, 'UTF-8'); ?><?php if (isset($comMessData[0]['rfi_reference'])) {
                                            echo $comMessData[0]['rfi_reference'];
                                        } ?><?php if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && $_SESSION[$_SESSION['idp'] . '_remaimberData']['rfi_reference'] != "") {
                                            echo $_SESSION[$_SESSION['idp'] . '_remaimberData']['rfi_reference'];
                                        } ?>" id="rfi_reference" name="rfi_reference" style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;" />
                                </td>
                            </tr>
                            
                            
                            <tr id="hideCorresNumber" style="display:none;">
                                <td align="left" valign="top">
                                    <label for="email">Correspondence&nbspNumber&nbsp# (use whole numbers e.g 1,2,3,4) <span class="reqire">*</span></label>
                                </td>
                                <?php //echo ""; print_r($_SESSION[$_SESSION['idp'] . '_remaimberData']); echo "===========<br>"; print_r($comMessData[0]); ?>
                                <td align="left" valign="top">
                                    <input type="text" id="correspondenceNumber" name="correspondenceNumber" value="<? if(empty($comMessData) && empty($_SESSION[$_SESSION['idp'].'_remaimberData'])){ echo ++$corNumCount[0]['correspondenceNumber']; }?><?php if (isset($comMessData[0]['correspondenceNumber'])) {
                                            echo $comMessData[0]['correspondenceNumber'];
                                        } ?><?php if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && $_SESSION[$_SESSION['idp'] . '_remaimberData']['correspondenceNumber'] != "") {
                                            echo $_SESSION[$_SESSION['idp'] . '_remaimberData']['correspondenceNumber'];
                                        } ?>" style="margin-left:10px; background: #ffffff"/>
                                </td>
                            </tr>        
                            <tr>
                                <td align="left" valign="top">Tags</td>
                                <td align="left" valign="top" >
                                    <input type="text" size="40" value="<?php echo htmlentities($_POST['tags'], ENT_QUOTES, 'UTF-8'); ?><?php if (isset($comMessData[0]['tags'])) {
                                            echo $comMessData[0]['tags'];
                                        } ?><?php if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && $_SESSION[$_SESSION['idp'] . '_remaimberData']['tags'] != "") {
                                            echo $_SESSION[$_SESSION['idp'] . '_remaimberData']['tags'];
                                        } ?>" id="tags" name="tags" style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;" />
                                </td>
                            </tr>
                            <tr style="display:none;">
                                <td align="left" valign="top">Company Tag</td>
                                <td align="left" valign="top" ><?php
                                        $projectIssueTos = $object->selQRYMultiple('issue_to_id, issue_to_name', 'inspection_issue_to', 'is_deleted=0 AND project_id="' . $_SESSION['idp'] . '" AND issue_to_name!="NA" AND issue_to_name!="" GROUP BY issue_to_name ORDER BY issue_to_name');

                                        $correspondencesTags = $object->selQRYMultiple('id, correspondences_tags', 'pmb_correspondences_tags', 'is_deleted=0 AND project_id="' . $_SESSION['idp'] . '" GROUP BY correspondences_tags ORDER BY correspondences_tags');
                                        $companyTagArr = array('');
                                        ?>
                                    <select name="companyTag" id="companyTag" style="width:350px;" class="chzn-select chzn-custom-value">
                                        <option value="">Select</option>
                                        <?php echo $companyTag = htmlentities($_POST['companyTag'], ENT_QUOTES, 'UTF-8'); ?><?php if (isset($comMessData[0]['companyTag'])) {
                                            $companyTag = $comMessData[0]['companyTag'];
                                        } ?><?php if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && $_SESSION[$_SESSION['idp'] . '_remaimberData']['companyTag'] != "") {
                                            $companyTag = $_SESSION[$_SESSION['idp'] . '_remaimberData']['companyTag'];
                                        } ?>
                                        <?php
                                        foreach ($projectIssueTos as $issueTo) {
                                            $companyTagArr[] = $issueTo['issue_to_name'];
                                        }
                                        foreach ($correspondencesTags as $tag) {
                                            if (!in_array($tag['correspondences_tags'], $companyTagArr)) {
                                                $companyTagArr[] = $tag['correspondences_tags'];
                                            }
                                        }
                                        natcasesort($companyTagArr);
                                        foreach ($companyTagArr as $tag) {
                                            $select = "";
                                            if ($companyTag == $tag) {
                                                $select = 'selected="selected"';
                                            }
                                            echo '<option value="' . $tag . '" ' . $select . ' >' . $tag . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="error-edit-profile" style="display:none;"  id="recipToError2">You have not entered any mail Recipients.</div>
                                    <div class="error-edit-profile" style="display:none;"  id="emailError2">Invalide email format.</div></td>
                            </tr>
                            <?php if(strpos($_REQUEST['folderType'], 'Variation Claims') > -1){	?>
                            <tr>
								<td>Date Sent</td>
								
								<td><input value="<?php echo $date; ?>" style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;"  type="text" size="7" id="date_sent" name="date_sent" globalnumber="835"></td>
                            </tr>
                            <tr style="display:none;">
								<td>Date Approved</td>
								<td><input style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;"  type="text" size="7" id="date_approved" name="date_approved" globalnumber="835"></td>
                            </tr>
							<tr>
								<td>$ Claimed</td>
								<td><input type="text" style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;" name="claimed" id="claimed" value="" size="40"></td>
                            </tr>                            
                            <tr style="display:none;">
								<td>$ Sent</td>
								<td><input type="text" style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;" name="sent" id="sent" value="" size="40"></td>
                            </tr>
                            <tr style="display:none;">
								<td>$ Approved</td>
								<td><input type="text" style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;" name="approved" id="approved" value="" size="40"></td>
                            </tr>
                            <?php }?>
                            
                            <?php if($_REQUEST['folderType']=='Progress Claims'){?>
                            <tr>
								<td>Date Sent</td>
								<?php 
								$date = date('d-m-Y');	
								?>
								<td><input value="<?php echo $date; ?>" style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;"  type="text" size="7" id="date_sent" name="date_sent" globalnumber="835"></td>
                            </tr>
                            <tr>
								<td>Date Approved</td>
								<td><input style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;"  type="text" size="7" id="date_approved" name="date_approved" globalnumber="835"></td>
                            </tr>
                            <tr>
								<td>$ Claimed</td>
								<td><input type="text" style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;" name="claimed" id="claimed" value="" size="40"></td>
                            </tr>
                            <tr>
								<td>$ Certified</td>
								<td><input type="text" style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;" name="certified" id="certified" value="" size="40"></td>
                            </tr>
                            <tr>
								<td>$ Invoiced</td>
								<td><input type="text" style="width: 343px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;" name="invoiced" id="invoiced" value="" size="40"></td>
                            </tr>
                            <?php }?>
                            <tr  id="msg_tr">
                                <td align="left" valign="top">
                                    <label for="message">Message 
                                     <span class="reqire">*</span></label>
                                </td>
                                <?php $comMessData1 = str_replace('\n', ' ', $comMessData[0]['message']); ?>
                                <td align="left" valign="top" class="textEditer">
									<div style="width: 80%;margin-right: 20%">
										<textarea name="messageDetails"  id="messageDetails">
											<?php echo htmlentities($_POST['message'], ENT_QUOTES, 'UTF-8'); ?><?php if (!empty($comMessData)) {
												echo $comMessData1;
											} ?><?php
												if (isset($_GET['dcTrans']) && $_GET['dcTrans'] == 'Y') {
													$msgText = 'Please find attached the following documents:<br>';
													if (isset($_SESSION[$_SESSION['idp'] . '_orignalFileName'])) {
														foreach ($_SESSION[$_SESSION['idp'] . '_orignalFileName'] as $key => $val) {
															$msgText .= mysql_real_escape_string(nl2br(htmlentities('&lt;' . $val . '&gt;', ENT_QUOTES, 'UTF-8'))) . '<br />';
														}
													}
													$msgText = str_replace('\n', ' ', $msgText);
													$userImage = $object->selQRYMultiple('user_id, user_signature', 'user', 'is_deleted  = 0 AND user_id = ' . $_SESSION['ww_builder_id'] . '');                                                    
													$msgText .= "<img src='" . IMG_SRC . "user_images/" . $userImage[0]['user_signature'] . "'>";
													echo $msgText;                                                    
												}
											?><?php
												if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && $_SESSION[$_SESSION['idp'] . '_remaimberData']['messageDetail'] != "") {
													echo htmlentities($_SESSION[$_SESSION['idp'] . '_remaimberData']['messageDetail'], ENT_QUOTES, 'UTF-8');
												} else {
													if ($userData[0]['pmb_signature'] != "" && empty($comMessData)) {
														echo '<br><br><br><input type="hidden">--<br>' . $userData[0]['pmb_signature'];
														if($userData[0]['user_signature'] !== '') {
															echo "<img src='" . IMG_SRC . "user_images/" . $userData[0]['user_signature'] . "'>";
														}
													}
												}
											$userData = $object->selQRYMultiple('user_name, user_fullname, user_email, pmb_signature', 'user', 'is_deleted = 0 AND user_id = ' . $_SESSION['ww_builder_id']);
										   # print_r($userData);die;
											?>
										</textarea>
										<div class="error-edit-profile" style="display:none;"  id="messageDetailsError">The message field is required.</div>
										<input type="hidden" id="isEditor" value="false" />
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td width="400px" align="left" valign="top">
                                    <label for="name">Add photo</label>
                                </td>
                                <td>
                                    <div class="upload-image">
                                        <?php $label='style="display:block"';
                                            $delimg='style="display:none"';
                                            if(file_exists($composeImage) && !empty($composeImage)){
                                                $label='style="display:none"';
                                                $delimg='style="display:block"';
                                            }
                                        ?>

                                        <div id="innerDiv_0" class="innerDiv">
                                            <label <?=$label?>>
                                            <span>Browse Image</span>
                                            <input type="file" name="imageCompose" id="imageCompose" />
                                            </label>
                                            <div class="response" id="responseProjectManagerImageCompose">
                                                <?php if(!empty($composeImage) && file_exists($composeImage)){ ?>
                                                    <img alt="image" src="<?php echo $composeImage.'?'.rand(0, 1000);?>">
                                                    <input type="hidden" value="<?php echo $imageCompose;?>" name="image_compose" id="image_compose" >
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <img id="removeProjectManagerImageCompose" class="del-image" onClick="deleteImage('innerDiv_0', this.id)" src="images/remove.png" alt="delete image" <?=$delimg?> />
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td align="left" valign="top">
                                    <label>Upload a file</label>
                                </td>
                                <td align="left" valign="top">
                                    <div style="overflow:hidden;cursor:pointer;width:36px;height:36px;float:left;color:#0000AA;margin-left:15px;">
										<img src="images/compose_attachment.png" name="newFileToUpload" id="newFileToUpload" title="Select New File" onClick="addAttachment();" />
                                        <!--input type="file" style="opacity: 0;width:40px;height:40px;margin-top: -40px;margin-left: 0px;font-size:35px;cursor: pointer !important;" id="attachment1" title="Select New File" name="attachment1" -->
                                    </div>
                                    <img src="images/attach-dr1.png" name="chooseFormDR" id="chooseFormDR" title="Choose From Document Register" onClick="showDocumentRegisterFiles();" style="float:left;cursor:pointer;" />

                                    <img src="images/add_pmb.png" name="attachEmail" id="attachEmail" title="Add a PMB Message" onClick="attachEmails();" style="margin-left:5px;float:left;cursor:pointer;" />

                                    <img src="images/add_email.png" name="attachEmail" id="attachEmailNew" title="Add Email" onClick="addEmails();" style="margin-left:5px;float:left;cursor:pointer;" />
                                    <?php #echo "Idp<br><pre>";print_r($_SESSION[$_SESSION['idp'] . '_emailfile']);?>
                                    <div id="imageName">
                                        <?php
                                        if (isset($_SESSION[$_SESSION['idp'] . '_emailfile'])) {
                                            $i = 0;
                                            foreach ($_SESSION[$_SESSION['idp'] . '_emailfile'] as $key => $val) {
                                                
                                                $i++;
                                                echo ' <span id="' . $i . '"><a style="color:#06C;" target="_blank" href="attachment/' . $val . '">' . $_SESSION[$_SESSION['idp'] . '_orignalFileName'][$key] . '</a>[<a style="color:red;" onclick="removeMaessage(' . $i . ', 0, \'' . $val . '\');">X</a>]</span>';
                                            }
                                        }
                                        if (isset($attachemnts)) {
                                            $i = 0;
                                            #echo "<pre>";print_r($attachemnts);
                                            foreach ($attachemnts as $attachemnt) {
                                                $i++;
                                                $type = explode('.', $attachemnt['attachment_name']);
                                                $type = end($type);
                                                $_SESSION[$_SESSION['idp'] . '_emailfile'][] = $attachemnt['attachment_name'];
                                                $_SESSION[$_SESSION['idp'] . '_orignalFileName'][] = $attachemnt['name'];
                                                if (strpos('JPEG |jpeg |JPG |jpg |PNG |png |GIF |gif', $type) > 0) {
                                                    echo ' <span id="' . $i . '"><a class="thickbox" style="color:#06C;" target="_blank" href="attachment/' . $attachemnt['attachment_name'] . '">' . $attachemnt['name'] . '</a>[<a style="color:red;" onclick="removeMaessage(' . $i . ',' . $attachemnt['attach_id'] . ',' . $attachemnt['attach_id'] . ');">X</a>]</span>';
                                                } else {
                                                    echo '<span id="' . $i . '"><a style="color:#06C;" target="_blank" href="attachment/' . $attachemnt['attachment_name'] . '">' . $attachemnt['name'] . '</a>[<a style="color:red;" onclick="removeMaessage(' . $i . ',' . $attachemnt['attach_id'] . ',' . $attachemnt['attach_id'] . ');">X</a>]</span>';
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                    <?php //print_r($_SESSION); ?>
                                    <input type="hidden" name="emailAttachedAjax" id="emailAttachedAjax" value="0" />
                                </td>
                            </tr>
                            <tr>
                                <td align="left" valign="top">&nbsp;</td>
                                <td align="left" valign="top">         
                            </tr>
                            <tr>
                                <td align="left" valign="top">
                                    <label>&nbsp;</label>
                                </td>
                                <td align="left" valign="top"  id="lastTd">
                                    <input type="hidden" name="customCompanyTag" id="customCompanyTag" value="">
                                    <input type="hidden" name="customEmailEntry" id="customEmailEntry" value="">
                                    <input type="hidden" name="removeAttachment" id="removeAttachment" value="">
                                    <input type="button" value="" name="save" id="sendMessage" style="background:url(images/email_send.png) no-repeat;border:0;width:87px;height:37px;" />
                                    <input type="button" value="" name="saveDraft" id="saveDraft" style="background:url(images/save_draft.png) no-repeat;border:0;width:113px;height:37px;" />
                                    <input type="hidden" name="refrenceNumber" value="<?= $refrenceNo; ?>">
                                    <input type="hidden" name="newDynamicGenRefrenceNumber" id="newDynamicGenRefrenceNumber" value="">
                                    <input type="hidden" name="submit" value="add">
                                    <input type="hidden" id="composeId" name="composeId" value="<?php if (isset($comMessData[0]['message_id'])) {
    echo $comMessData[0]['message_id'];
} else {
    echo 0;
} ?>">
                                    <input type="hidden" name="RFIstatus" id="RFIstatus" value="" />
                                    <input type="hidden" name="plainText" id="plainText" value="" />
                                    <input type="hidden" name="checkValidation" value="0" id="checkValidation">
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <br><br>
<?php if (isset($_GET['attached']) and $_GET['attached'] == 'Y') { ?>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#emailAttachedAjax').val(1);
                });
            </script>
<?php
} else {
    if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData'])) {
        unset($_SESSION[$_SESSION['idp'] . '_remaimberData']);
    }
}
?>
<script src="js/jquery.min.for.choosen.js" type="text/javascript"></script>
<script src="js/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
	var config = {
		'.chzn-select': {},
		'.chzn-select-deselect': {allow_single_deselect: false},
		'.chzn-select-width': {width: "95%"}
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}

	var align = 'center';
	var topModal = 100;
	var width = 900;
	var padding = 10;
	var backgroundColor = '#FFFFFF';
	var borderColor = '#333333';
	var borderWeight = 4;
	var borderRadius = 5;
	var fadeOutTime = 300;
	var disableColor = '#666666';
	var disableOpacity = 40;
	var loadingImage = 'images/loadingAnimation.gif';

	function attachEmails() {
		console.log('attachEmails');
		var messageType = '<?php echo (isset($_GET['folderType']) && $_GET['folderType'] != "") ? $_GET['folderType'] : 'General Correspondence'; ?>';
		modalPopup(align, topModal, 890, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pmb_display_emails.php?name=' + Math.random() + '&folderType=' + messageType, loadingImage, function () {
			loadData(<?= $_SESSION['idp'] ?>, messageType);
		});
		goTop();
	}
var oTable = '';
	function loadData(projectID, messageType) { 
		console.log(projectID, messageType);
		 oTable = $('#inboxData').dataTable({
			"iDisplayLength": 100,
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bProcessing": true,
			"bServerSide": true,
			"bRetrieve": true,
			"sAjaxSource": "show_inbox_data_by_ajax.php?reqfrom=ajax&name=" + Math.random() + "&folderType=" + messageType,
			"bStateSave": true,
			"aoColumnDefs": [{"bVisible": false, "aTargets": [0]}]
					/*"aoColumns": [
					 {"sType": "html"},
					 {"sType": "html"},
					 {"sType": "html"},
					 {"sType": "html"},
					 {"sType": "html"},
					 {"sType": "html"}
					 ]*/
		});
	}
	
	$('#ajaxmessageType').live("change", function () {
		oTable.fnDestroy();
		loadData(<?= $_SESSION['idp'] ?>, $(this).val());
		
	});
	
	/*$('#ajaxmessageType').live("change", function () {
        oTable.fnDestroy();
        oTable = $('#inboxData').dataTable({
            "iDisplayLength": 100,
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "bProcessing": true,
            "bServerSide": true,
            "bRetrieve": true,
            "sAjaxSource": "show_inbox_data_by_ajax.php?reqfrom=ajax&name=" + Math.random() + "&folderType=" + $(this).val(),
            "bStateSave": true,
            "aoColumnDefs": [{"bVisible": false, "aTargets": [0]}]
                    /*"aoColumns": [
                     {"sType": "html"},
                     {"sType": "html"},
                     {"sType": "html"},
                     {"sType": "html"},
                     {"sType": "html"},
                     {"sType": "html"}
                     ]*//*
        });
        
    });*/
	
	function selectedEmail(thread_id) {
		var params = {
			'name':Math.random(),
			'thread_id': thread_id
		}                
		$.post(['attach_inbox_mail.php'].join(), params).done(function(data){
			var jsonResult = JSON.parse(data);
			var attachNo = $('#imageName').children().length + 1;
			$('#imageName').append(' <span id="' + attachNo + '"><a href="attachment/' + jsonResult.imageName + '" target="_blank" style="color:#06C;" class="thickbox" >' + jsonResult.imageName + '</a>[<a onclick="removeMaessage(' + attachNo + ', 0, \'' + jsonResult.imageName + '\');" style="color:red;">X</a>]</span>');
			closePopup(300); //close popup.
		})
	}
	
	
// JavaScript I wrote to limit what types of input are allowed to be keyed into a textbox
var allowedSpecialCharKeyCodes = [46,8,37,39,35,36,9,116];
var numberKeyCodes = [44, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105];
var commaKeyCode = [];
var decimalKeyCode = [190,110, 109, 173, 189];

function currenciesOnly(event) {
	//alert(event.keyCode);
	var legalKeyCode =
	   (!event.shiftKey && !event.ctrlKey && !event.altKey )
			 &&
	   (jQuery.inArray(event.keyCode, allowedSpecialCharKeyCodes) >= 0
			  ||
		jQuery.inArray(event.keyCode, numberKeyCodes) >= 0
			 ||
		jQuery.inArray(event.keyCode, commaKeyCode) >= 0
			 ||
		jQuery.inArray(event.keyCode, decimalKeyCode) >= 0);
	 
    // Allow for page refresh
    if (!legalKeyCode && event.ctrlKey && event.keyCode == 116){
        legalKeyCode = true;
	}
	
    if (legalKeyCode === false)
       event.preventDefault();
}	
</script>


    <script type="text/javascript" src="js/thickbox.js"></script>
    <link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
    <!--script type="text/javascript" src="js/nicEdit.js"></script-->
    <script type="text/javascript" src="ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
    <script type="text/javascript">
		$(document).ready(function () {
			var currValue = $("#messageType").val();
            showHideForMessageType(currValue);
			
			//currenciesOnly
			$("#sent, #approved").live('keydown', currenciesOnly);
            var correspondenceNumberVal = '';
            $("#correspondenceNumber").keydown(function(e){
                correspondenceNumberVal = $("#correspondenceNumber").val();
            });
            $("#correspondenceNumber").keyup(function(e){
                var str = $("#correspondenceNumber").val();
                if(str !='' && str != undefined){
                    var intRegex = /^\d+$/;
                    var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;

                    if(!intRegex.test(str) || !floatRegex.test(str)) {
                       //alert('I am a number');
                        alert('Invalide number format.');
                        $("#correspondenceNumber").val(correspondenceNumberVal);
                    }
                }else{
                    $("#correspondenceNumber").val('');
                }
            });
        });
        
        $(function(){
            var pdfID = 0;
            var btnUpload=$('#imageCompose');
            var status=$('#responseProjectManagerImageCompose');
            new AjaxUpload(btnUpload, {
                action: 'auto_file_upload.php?action=imageCompose&proid=' + pdfID + '&uniqueID='+Math.random(),
                name: 'imageCompose',
                onSubmit: function(file, ext){
                    if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                        // extension is not allowed
                        $('#innerDiv_0 > label').hide();
                        $('#removeProjectManagerImageCompose').show('fast');
                        status.html('<p>Only JPG, PNG or GIF files are allowed</p>');
                        return false;
                    }
                    status.text('Uploading...');
                    showProgress();
                },
                onComplete: function(file, response){
                    hideProgress();
                    $('#innerDiv_0 > label').hide();
                    $('#removeProjectManagerImageCompose').show('fast');
                    status.html(response);
                }
            });
        });

        function deleteImage(innerDiv, delimg) {
            console.log(delimg);
            $('#'+innerDiv+' label').show('fast');
            $('#'+innerDiv+' .response').html('');
            $('#'+delimg).hide('fast');
        }
		
            var isAutoSaveActive = 0;
            var isFormSubmit = 0;
            /*bkLib.onDomLoaded(function () {
                new nicEditor({iconsPath: 'js/nicEditorIcons.gif', buttonList: ['save', 'bold', 'italic', 'underline', 'left', 'center', 'right', 'justify', 'ol', 'ul', 'indent', 'outdent', 'forecolor', 'bgcolor']}).panelInstance('messageDetails');
            });*/
            
            /*======== CKEDITOR ==========*/
            var editor = CKEDITOR.replace( 'messageDetails', {
				uiColor: '#F7F7F7',
				toolbar: [
					[ 'Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', '-', "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock"],
					[ 'FontSize', 'TextColor', 'BGColor' ]
				]
			});
			
            /*======== CKEDITOR ==========*/
            
            $(document).ready(function () {
                $('#external_email').click(function () {
                    if ($(this).prop('checked') == true) {
                        $('#recipTo_chzn ul li input').prop('disabled', true);
                        $('#recipCC_chzn ul li input').prop('disabled', true);
                        //$('#messageDetails').prop('disabled', true);
                        //$('#msg_tr').hide();

                        $('#sendMessage').css('background', 'url("images/save_new1.png") no-repeat scroll 0 0 rgba(0, 0, 0, 0)');
                        $('#sendMessage').css('width', '119px');
                        $('#sendMessage').css('height', '39px');
                        $("#checkValidation").val(1);
                        //$('#msg_tr').hide();
                        $('#cc_tr').hide();
                        $('#to_tr').hide();
						
						addAttachment();
                    } else {
                        $('#recipTo_chzn ul li input').prop('disabled', false);
                        $('#recipCC_chzn ul li input').prop('disabled', false);
                        $('#messageDetails').prop('disabled', false);
                        $('#msg_tr').show();
                        $('#sendMessage').css('background', 'url("images/email_send.png") no-repeat scroll 0 0 rgba(0, 0, 0, 0)');
                        $('#sendMessage').css('width', '87px');
                        $('#sendMessage').css('height', '37px');
                        $("#checkValidation").val(0);
                        $('#msg_tr').show();
                        $('#cc_tr').show();
                        $('#to_tr').show();
                    }
                });
                $('#fixedByDate').click(function () {
                    $('#fixedByDate').blur();
                });
                new JsDatePick({useMode: 2, target: "fixedByDate", dateFormat: "%d-%m-%Y"});


                //Add new Raised by click on save button
                $('#sendMessage').click(function () {
                    isFormSubmit = 1;
                    var recipTo = $('#recipTo').val();
                    var subject = $('#subject').val();
                    var RFInumber = $('#RFInumber').val();
                    var messageType = $('#messageType').val();
                    //var messageDetails = $('.nicEdit-main').html();
                    //var messageDetailsText = $('.nicEdit-main').contents();
                    var messageDetails = editor.getData();
                    var messageDetailsText = editor.getData();
                    var messageDetailsTextPDF = "";
					var dateSent = $('#date_sent').val();
					var dateApproved = $('#date_approved').val();
					var sent = $('#sent').val();
					var approved = $('#approved').val();
					var claimed = $('#claimed').val();
					var certified = $('#certified').val();
					var invoiced = $('#invoiced').val();

                    var rfi_reference = $('#rfi_reference').val();


                    /*for (l = 0; l < messageDetailsText.length; l++) {
                        if (messageDetailsText[l].nodeType == 3) {
                            if (messageDetailsTextPDF == "") {
                                if (messageDetailsText[l].textContent != '<br>' && messageDetailsText[l].textContent != "")
                                    messageDetailsTextPDF = messageDetailsText[l].textContent;
                            } else {
                                if (messageDetailsText[l].textContent != '<br>' && messageDetailsText[l].textContent != "")
                                    messageDetailsTextPDF += '<br>' + messageDetailsText[l].textContent;
                            }
                        } else {
                            console.log(messageDetailsText[l].textContent);
                            if (messageDetailsTextPDF == "") {
                                if (messageDetailsText[l].textContent != '<br>' && messageDetailsText[l].textContent != "")
                                    messageDetailsTextPDF = messageDetailsText[l].innerHTML;
                            } else {
                                if (messageDetailsText[l].textContent != '<br>' && messageDetailsText[l].textContent != "")
                                    messageDetailsTextPDF += '<br>' + messageDetailsText[l].innerHTML;
                            }
                        }
                    }*/
                    $('#plainText').val(messageDetailsTextPDF);
                    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
                    if (recipTo == 'Select' || recipTo === null) {
                        if ($('#checkValidation').val() == 0) {
                            $('#recipToError').show();
                            isFormSubmit = 0;
                            return false;
                        }
                    } else if (subject == '') {
                        $('#emailError').hide();
                        //$('#recipToError').hide();
                        $('#subjectError').show();
                        isFormSubmit = 0;
                        return false;
                    }
                    /*else if(RFInumber == ''){
                     $('#subjectError').hide();
                     $('#RFInumberError').show();
                     return false
                     }else if(RFInumber==0){
                     $('#RFInumberError').hide();
                     $('#RFInumberErrorZero').show();		
                     return false;
                     }*/
                    else if (messageType == '') {
                        if ($('#checkValidation').val() == 0) {
                            $('#subjectError').hide();
                            $('#messageTypeError').show();
                            isFormSubmit = 0;
                            return false;
                        }
                    } else if (messageDetails == '<br>') {
                        if ($('#checkValidation').val() == 0) {
                            $('#messageTypeError').hide();
                            $('#messageDetailsError').show();
                            isFormSubmit = 0;
                            return false;
                        }
                    } else {
                        if ($('#messageType').val() == 'Request For Information') {
                            if ($('#RFInumber').val() == "") {
                                $('#RFInumberError').show();
                                isFormSubmit = 0;
                                return false;
                            } else {
                                $('#RFInumberError').hide();
                            }
                            if ($('#fixedByDate').val() == "") {
                                $('#fixedByDateError').show();
                                isFormSubmit = 0;
                                return false;
                            } else {
                                $('#fixedByDateError').hide();
                            }
                            if ($('#RFInumber').val() == 0) {
                                $('#RFInumberErrorZero').show();
                                isFormSubmit = 0;
                                return false;
                            } else {
                                $('#RFInumberErrorZero').hide();
                            }
                            var newDynamicGenRefrenceNumber = 'Request for information # ' + $('#RFInumber').val() + ': ' + $('#subject').val();
                            $('#RFIstatus').val('Open');
                            $('#newDynamicGenRefrenceNumber').val(newDynamicGenRefrenceNumber);
                        }
                        if ($('#messageType').val() == 'Consultant Advice Notice') {
                            if ($('#RFInumber').val() == "") {
                                $('#subjectError').hide();
                                $('#RFInumberError').show();
                                isFormSubmit = 0;
                                return false;
                            } else {
                                $('#RFInumberError').hide();
                            }
                            if ($('#fixedByDate').val() == "") {
                                $('#fixedByDateError').show();
                                isFormSubmit = 0;
                                return false;
                            } else {
                                $('#fixedByDateError').hide();
                            }
                            if ($('#RFInumber').val() == 0) {
                                $('#RFInumberErrorZero').show();
                                isFormSubmit = 0;
                                return false;
                            } else {
                                $('#RFInumberErrorZero').hide();
                            }
                            var newDynamicGenRefrenceNumber = 'Request for information # ' + $('#RFInumber').val() + ': ' + $('#subject').val();
                            $('#RFIstatus').val('Open');
                            $('#newDynamicGenRefrenceNumber').val(newDynamicGenRefrenceNumber);
                        }


                        showProgress();
                        /*		if(isAutoSaveActive == 1){
                         showProgress();
                         }
                         */
                        //return true;
                    }
                    if (subject == '') {
                        $('#emailError').hide();
                        //$('#recipToError').hide();
                        $('#subjectError').show();
                        isFormSubmit = 0;
                        return false;
                    } else {
                        $('#subjectError').hide();
                    }
                    //----------------------------MAIL SEND CODE START-----------------------------------
                    var emailAttachedAjax = document.getElementById('emailAttachedAjax').value;
                    var recipTo = $('#recipTo').val();
                    var recipCC = $('#recipCC').val();
                    var subject = $('input[name=subject]').val();
                    var purchaserLocation = $('input[name=purchaserLocation]').val();
                    var tags = $('input[name=tags]').val();
                    var companyTag = $('input[name=companyTag]').val();
                    var messageType = $('#messageType').val();
                    var newDynamicGenRefrenceNumber = $('input[name=newDynamicGenRefrenceNumber]').val();
                    var plainText = $('input[name=plainText]').val();
                    var composeId = $('input[name=composeId]').val();
                    var RFInumber = $('#RFInumber').val();
                    var fixedByDate = $('input[name=fixedByDate]').val();
                    var RFIstatus = $('input[name=RFIstatus]').val();

                    var rfi_reference = $('input[name=rfi_reference]').val();

                    var correspondenceNumber = $('input[name=correspondenceNumber]').val();
                    if ($('#external_email').prop('checked') == true) {
                        var external_email = 1;
                    } else {
                        var external_email = 0;
                    }

                    var image_compose = $("#image_compose").val();
                    if(image_compose == undefined){
                        image_compose = '';
                    }

                    //var messageDetails = $('#messageDetails').val();
                    var removeAttachment = $('input[name=removeAttachment]').val();
                    var save = $('input[name=save]').val();
                    var filesArr = $('#filesArr').val();
                    var filesArray = new Array();
                    $('.fileArrclass').each(function (index) {
                        filesArray.push($(this).val());
                    });

                    $.ajax({
                        url: "compose_ajax.php",
                        data: {filesArr: filesArray, external_email: external_email, subject: subject, emailAttachedAjax: emailAttachedAjax, recipTo: recipTo, recipCC: recipCC, purchaserLocation: purchaserLocation, tags: tags, companyTag: companyTag, messageType: messageType, newDynamicGenRefrenceNumber: newDynamicGenRefrenceNumber, plainText: plainText, composeId: composeId, RFInumber: RFInumber, fixedByDate: fixedByDate, RFIstatus: RFIstatus, correspondenceNumber: correspondenceNumber, messageDetails: messageDetails, removeAttachment: removeAttachment, save: save, submit: 'add', dateSent:dateSent, dateApproved:dateApproved, sent:sent, approved:approved,claimed:claimed, certified:certified, invoiced:invoiced,image_compose: image_compose,rfi_reference: rfi_reference},
                        type: "post",
                        beforeSend: function () {
                            showProgress();
                        },
                        success: function (data) { 
                            console.log(data);
                            if ($('#checkValidation').val() == 1) {
                                jAlert('Mail Saved.');
                            } else {
                                jAlert('Mail Sent Successfully.');
                            }
                            $("#imageName").html('');
                            $('.nicEdit-main').html('')
                            $("#subject").val('');
                            $('#recipTo').val('');
                            $('#recipCC').val('');
                            $(".result-selected").each(function () {
                                var rId = $(this).attr('id');
                                $("#" + rId).removeClass('group-option result-selected');
                                $("#" + rId).addClass('active-result group-option');
                            });
                            //$(".search-field").html('<input class="default" type="text" style="width: 65px;" autocomplete="off" value="Select">');		
                            $('.search-choice').remove();
                            hideProgress();
                            //window.location = 'pms.php?sect=sent_box';
                            window.location = 'pms.php?sect=messages&folderType=' + messageType + '';
                        }

                    });

                    //----------------------------MAIL SEND CODE ENDS-----------------------------------
                });

                $('#saveDraft').click(function () {
                    isFormSubmit = 1;
                    $('#RFIstatus').val('Draft');
                    //showProgress();
                    //----------------------------MAIL SEND CODE START-----------------------------------
                    //showProgress();
                    var emailAttachedAjax = document.getElementById('emailAttachedAjax').value;
                    var recipTo = $('#recipTo').val();
                    var recipCC = $('#recipCC').val();
                    var subject = $('#subject').val();
                    var purchaserLocation = $('input[name=purchaserLocation]').val();
                    var tags = $('input[name=tags]').val();
                    var companyTag = $('input[name=companyTag]').val();
                    var messageType = $('input[name=messageType]').val();
                    var newDynamicGenRefrenceNumber = $('input[name=newDynamicGenRefrenceNumber]').val();
                    var plainText = $('input[name=plainText]').val();
                    var composeId = $('input[name=composeId]').val();
                    var RFInumber = $('input[name=RFInumber]').val();
                    var fixedByDate = $('input[name=fixedByDate]').val();
                    var RFIstatus = $('input[name=RFIstatus]').val();
                    var correspondenceNumber = $('input[name=correspondenceNumber]').val();
                    //var messageDetails = $('.nicEdit-main').html();
                    var messageDetails = editor.getData();
                    var removeAttachment = $('input[name=removeAttachment]').val();
                    var save = $('input[name=save]').val();

                    var rfi_reference = $('input[name=rfi_reference]').val();

                    var submit = "add";

                    var image_compose = $("#image_compose").val();
                    if(image_compose == undefined){
                        image_compose = '';
                    }
                    //alert(RFIstatus);    

                    $.ajax({
                        url: "compose_ajax.php",
                        type: "post",
                        beforeSend: function () {
                            showProgress();
                        },
                        data: {subject: subject, emailAttachedAjax: emailAttachedAjax, recipTo: recipTo, recipCC: recipCC, purchaserLocation: purchaserLocation, tags: tags, companyTag: companyTag, messageType: messageType, newDynamicGenRefrenceNumber: newDynamicGenRefrenceNumber, plainText: plainText, composeId: composeId, RFInumber: RFInumber, fixedByDate: fixedByDate, RFIstatus: RFIstatus, correspondenceNumber: correspondenceNumber, messageDetails: messageDetails, removeAttachment: removeAttachment, submit: submit, saveDraft: 'save',image_compose :image_compose,rfi_reference: rfi_reference},
                        success: function (data) {
                            hideProgress();
                            jAlert('Saved Successfully');
                            window.location = 'pms.php?sect=drafts';
                        }

                    });

                    //----------------------------MAIL SEND CODE ENDS-----------------------------------
                });

                var attachNo = <?php echo isset($attachemnts) ? count($attachemnts) : 0; ?>;
                var btnUpload = $('#attachment1');
                var btnUploadCurr = btnUpload[btnUpload.length - 1];
                var pdfID = 0;
                new AjaxUpload(btnUploadCurr, {
                    action: 'auto_file_upload.php?action=emailAttachment&pdfID=' + pdfID + '&uniqueID=' + Math.random(),
                    name: 'attachment1',
                    onSubmit: function (file, ext) {
                        showProgress();
                    },
                    onComplete: function (file, fileName) {
                        hideProgress();
                        console.log(file, fileName);
                        var jsonResult = JSON.parse(fileName);
                        if (jsonResult.status) {
                            attachNo++;
                            $('#emailAttachedAjax').val(1);
                            console.log(jsonResult.uploadedImageName);
                            $('#imageName').append(' <span id="' + attachNo + '"><a href="attachment/' + jsonResult.filePath + '" target="_blank" style="color:#06C;" class="thickbox" >' + jsonResult.imageName + '</a>[<a onclick="removeMaessage(' + attachNo + ', 0, \'' + jsonResult.uploadedImageName + '\');" style="color:red;">X</a>]</span>');
                            tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
                            imgLoader = new Image();// preload image
                            imgLoader.src = tb_pathToImage;
                        } else {
                            jAlert('Error in file uploading please try again');
                        }
                    }
                });

                if ($('#messageType').val() == 'Request For Information') {
                    $('#hiddenRow, #hiddenRow1').show();
                    $('#hideCorresNumber, #purchaserLocationRow').hide();
                    $('#subjectLevelChange').html('RFI Description <span class="reqire">*</span>');
                    $('#subjectError').html('The RFI Description field is required.');

                } else if ($('#messageType').val() == 'Consultant Advice Notice') {
                    $('#hiddenRow, #hiddenRow1').show();
                    //$('#hideCorresNumber, #purchaserLocationRow').hide();
                    //$('#subjectLevelChange').html('RFI Description <span class="reqire">*</span>');
                    //$('#subjectError').html('The RFI Description field is required.');
                    $('#hideCorresNumber').show();

                } else if ($('#messageType').val() == 'Purchaser Changes') {
                    $('#hiddenRow, #hiddenRow1').hide();
                    $('#hideCorresNumber, #purchaserLocationRow').show();
                    $('#subjectLevelChange').html('Subject <span class="reqire">*</span>');
                    $('#subjectError').html('The subject field is required.');

                } else if ($('#messageType').val() == 'Site Instruction') {
                    $('#rfiReferenceRow').show();

                } else {
                    $('#hiddenRow, #hiddenRow1, #purchaserLocationRow').hide();
                    $('#hideCorresNumber').show();
                    $('#subjectLevelChange').html('Subject <span class="reqire">*</span>');
                    $('#subjectError').html('The subject field is required.');
                }
            });

            function removeFile(file, reference, filename, flag) {
                r = jConfirm('Do you really want to delete this file?', null, function (r) {
                    if (r === true) {
                        $('#' + flag).remove();
                        $('#filesArr_' + flag).remove();
                        $('#div_' + flag).remove();
                        if ($('#external_email').prop('checked') == true) {
                            $('#attachEmailNew').show();
                        }
                    }
                });
            }

            function removeMaessage(id, attachId, fileNameId) {
                $("#" + id).remove();
                var removeAttachId = $("#removeAttachment").val();
                if (fileNameId != "" && isNaN(fileNameId) && attachId == 0) {
                    if (removeAttachId == "")
                        $("#removeAttachment").val(fileNameId);
                    else
                        $("#removeAttachment").val(removeAttachId + ',' + fileNameId);
                } else if (attachId != 0 && !isNaN(fileNameId)) {
                    if (removeAttachId == "")
                        $("#removeAttachment").val(attachId);
                    else
                        $("#removeAttachment").val(removeAttachId + ',' + attachId);
                }
                console.log('Finally Done ====' + $("#removeAttachment").val());
            }

            function showDocumentRegisterFiles() {
                //var areaText = editor.instances['messageDetails'].getData();
                //console.log(areaText);
                var messageDetails = editor.getData();
               
                var input = $("<input>").attr("type", "hidden").attr("name", "messageDetail").val(messageDetails);
              
                $('#compose').append($(input));
                //console.log($('#compose').serialize(),'<<===='); return false;
                $.post("copy_file_toAttach_folder.php?antiqueId=" + Math.random(), $('#compose').serialize()).done(function (data) {
                    var jsonResult = JSON.parse(data);
                    if (jsonResult.status) {
                        console.log(jsonResult.dataArr);
                        window.location.href = "?sect=drawing_register_select&page=compose&msgid=0&folderType=<?php echo $_GET['folderType'];?>";
                    }
                });
            }

            $('#messageType').change(function () {
			   var currValue = $(this).val();
               showHideForMessageType(currValue);
            });
			
			function showHideForMessageType(currValue){
				
                switch (currValue) {
					
                    case 'Request For Information' :
                        $('#hiddenRow, #hiddenRow1').show();
                        $('#hideCorresNumber, #purchaserLocationRow').hide();
                        $('#subjectLevelChange').html('RFI Description <span class="reqire">*</span>');
                        $('#subjectError').html('The RFI Description field is required.');
				        break;
                    case 'Purchaser Changes' :
                        $('#hiddenRow, #hiddenRow1').hide();
                        $('#hideCorresNumber, #purchaserLocationRow').show();
                        $('#subjectLevelChange').html('Subject <span class="reqire">*</span>');
                        $('#subjectError').html('The subject field is required.');
                        break;
                    case 'Contract Admin_Client Side_Purchaser Changes' :
                        $('#hiddenRow, #hiddenRow1').hide();
                        $('#hideCorresNumber, #purchaserLocationRow').show();
                        $('#subjectLevelChange').html('Subject <span class="reqire">*</span>');
                        $('#subjectError').html('The subject field is required.');
                        break;	
                    case 'Site Instruction' :
                        $('#rfiReferenceRow').show();
                        break;  					
                    default :
                        $('#hiddenRow, #hiddenRow1, #purchaserLocationRow').hide();
                        $('#hideCorresNumber').show();
                        $('#subjectLevelChange').html('Subject <span class="reqire">*</span>');
                        $('#subjectError').html('The subject field is required.');
                        break;
                }
                //Send Request for correspond numbers
                $.post("document_transmittal_user_list.php?singleID=" + Math.random(), {messageType: currValue}).done(function (data) {
                    var jsonResult = JSON.parse(data);
                    if (jsonResult.status) {
                        //console.log(jsonResult.data);
                        var correspondVal = $('#correspondenceNumber').val();
                        if(correspondVal == '' || correspondVal == undefined){
                            $('#correspondenceNumber').val(jsonResult.data);
                        }
                    }
                });
			}

            function autoSaveInDraft() {
                isAutoSaveActive = 1;
                var input = $("<input>").attr("type", "hidden").attr("name", "messageDetail").val($('.nicEdit-main').html());
                $('#compose').append($(input));
                $.post("compose_auto_save_by_ajax.php?antiqueId=" + Math.random(), $('#compose').serialize()).done(function (data) {
                    console.log(data);
                    $("#composeId").val(data);
                    isAutoSaveActive = 0;
                    if (isFormSubmit == 1) {
                        $('#compose').submit();
                    }
                }).fail(function (data) {
                    isAutoSaveActive = 0;
                    if (isFormSubmit == 1) {
                        $('#compose').submit();
                    }
                }
                );
            }
            var seconds = 0;
            setInterval(function () {
                if (isAutoSaveActive == 0 && isFormSubmit == 0) {
                    console.log(1);
                    autoSaveInDraft();
                } else {
                    console.log(isAutoSaveActive);
                }
                seconds = 0;
            }, 30000);
            setInterval(function () {
                seconds++; /*console.clear(); */
                //console.log(seconds);
            }, 1000);
            var deadlock = true;//File upload
            var params = "";
            var align = 'center';
            var top1 = 100;
            var width = 850;
            var padding = 10;
            var backgroundColor = '#FFFFFF';
            var borderColor = '#333333';
            var borderWeight = 4;
            var borderRadius = 5;
            var fadeOutTime = 300;
            var disableColor = '#666666';
            var disableOpacity = 40;
            var loadingImage = 'images/loadingAnimation.gif';
            var copyStatus = false;
            var copyId = '';
            
            function addAttachment() {
				modalPopup(align, top1, 1000, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_attachment_email.php?&name=' + Math.random(), loadingImage, addMultipleAttachment);
				goTop();
			}
			
			function addMultipleAttachment() {
				var config = {
                    support: ",message/rfc822,application/octet-stream, application/vnd.ms-outlook", // Valid file formats
                    form: "frmAddAttachment", // Form ID
                    dragArea: "innerDiv", // Upload Area ID
                    uploadUrl: "add_attachment_email.php?antiqueID="+ Math.random()// +new Date().getTime()Server side upload url
                }
                mappingDocumentArr = {};
                mappedDocArr = {};
                initMultipleAttachment(config);
			}

            function removeMultipleAttachment(id) {
                
                for (var key in testFxArr) {
                    if (testFxArr[key] == '0') {
                        testFxArr.splice(0, 1);
                    }
                }
                var testFxArr = new Array();
                console.log(testFxArr);
                $('.divId_' + id).remove();
                tempId = 0;
                this.all = [];
                self.all = [];
                //File count decrement
                var fCount = $("#addAttachmentCount").val();
                if(fCount > 0) {
                    fCount = parseInt(fCount) - 1;
                    $("#addAttachmentCount").val(fCount);
                    $("#fileCount").html(fCount +' files selected');
                }
            }

            function addEmails() {
                modalPopup(align, top1, 1000, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_compose_email.php?&name=' + Math.random(), loadingImage, bulkRegistration);
				goTop();
            }
            function bulkRegistration() {
                var config = {
                    support: ",message/rfc822,application/octet-stream, application/vnd.ms-outlook", // Valid file formats
                    form: "addDrawingForm", // Form ID
                    dragArea: "innerDiv", // Upload Area ID
                    uploadUrl: "add_compose_email.php"// +new Date().getTime()Server side upload url
                }
                mappingDocumentArr = {};
                mappedDocArr = {};
                initBulkUploader(config);
            }

            function removeBulkAttachment(id) {

                /*alert(id);
                 //var testFxArr = Array();
                 console.log(testFxArr);
                 //delete testFxArr[0];
                 for (var key in testFxArr) {
                 if (testFxArr[key] == '0') {
                 testFxArr.splice(0, 1);
                 }
                 }
                 testFxArr.splice(0,testFxArr.length);
                 testFxArr.pop();*/
                //removeKey(testFxArr,0);
                for (var key in testFxArr) {
                    if (testFxArr[key] == '0') {
                        testFxArr.splice(0, 1);
                    }
                }
                var testFxArr = new Array();
                console.log(testFxArr);
                $('.divId_' + id).hide('slow');
                tempId = 0;
                this.all = [];
                self.all = [];
            }
            function removeKey(arrayName, key)
            {
                var x;
                var tmpArray = new Array();
                for (x in arrayName)
                {
                    if (x != key) {
                        tmpArray[x] = arrayName[x];
                    }
                }
                return tmpArray;
            }
    </script> 
<script>
// Start:- Check form updated or not
var isUpdated = 0;
function myBlurFunction(url) { 
	isUpdated = $('#isEditor').val();

	$("#compose :text, :file, :checkbox, select, textarea").change(function() {
		if($("#compose").data("changed",true)){
			isUpdated = 1;
		}
	});
	
	if(isUpdated >= 1){ 
		jConfirm('Are you sure, you want to leave from message body?', 'Alert', function(r){
			if(r){
				isUpdated = 0;
				$('#isEditor').val(0);
				if(url != ''){
					window.location.href = url;
				}
				return true;
			} else {
				return false;
				//editor.focus();
			}
		});
	    //document.getElementById("compose").style.backgroundColor = "yellow";  
	}else{
		if(url != ''){
			window.location.href = url;
		}
		return true;
	  // document.getElementById("compose").style.backgroundColor = "grey";  		
	}
	//editor.focus();
	//$("#subject").focus();
}

window.onload = function() {
  document.getElementById("composeId").focus();
  myBlurFunction('');
};

/*$( "#compose" ).mouseleave(function() {
	editor.focus();
	$("#subject").focus();
	myBlurFunction();
}); */
$( ".MailLeft a" ).mouseover(function() {
	url = $(this).attr('href');
	if(url != 'javascript:void(0);'){
		$(this).attr('href', 'javascript:void(0);');
		$(this).attr('onclick', "myBlurFunction('"+url+"')");
	}
});

// Check check editor data
editor.on('contentDom', function( event ) {
  editor.document.on('keyup', function(event) {
    $('#isEditor').val(1);
	//alert('my keyup');
  });
}); 
// End:- Check form updated or not
</script>
    <script type="text/javascript" src="js/compose_multiupload.js"></script>
    <script type="text/javascript" src="js/add_multiple_attachment.js"></script>
    <style>div.content_container{ width:100% !important; }div#container{color:#000000;}</style>
    <style>
        .roundCorner{ border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; }
        .innerDivDrager{ color:#000000; width:620px; height:150px; }
        .innerDiv{ color:#000000; float:left; border:1px solid red; width:300px; height:120px; float:left;}
        div#innerModalPopupDiv, div#innerModalPopupDiv1{color:#000000;}
        h3#uploaderBulk{font-size:10px;padding:0;margin:0;float:left;}
        .bulkfiles {clear: both;border: 1px solid #ccc;background-color: #E4E4E4;padding: 3px;position: relative;margin: 3px;z-index: 1;width: 98%;cursor: default;height: 110px;}
        .bulkfilesdwg {clear: both;border: 1px solid #ccc;background-color: #E4E4E4;padding: 3px;position: relative;margin: 3px;z-index: 1;width: 98%;cursor: default;height: 50px;}
        .approveDrawingReg{margin-left:0px;}
        /*div#waterMark{color: #ccc;width: 100%;z-index: 0;text-align: center;vertical-align: middle;position: absolute;top: 25px;}*/
        table.collapse { border-collapse: collapse; border: 1pt solid black; }
        table.collapse tr, table.collapse td { border: 1pt solid black; padding: 2px; font-family:Arial, Helvetica, sans-serif; font-size:10px;}
        div#htmlContainer{overflow:auto;max-height:550px;}
        #revisionBox{ float:right; margin-right:5px;}
        h3#uploaderBulk img{ margin-top: -15px; padding-top: 9px; display: block; }
        h3#uploaderBulk span{ display: block; margin-left: 30px; margin-top: -18px; }
        .Admin ul{ background-image:url(images/tab_bg.png); position:absolute; border:1px solid #435D01; border-top-right-radius:0px; border-top-left-radius:0px; border-bottom-right-radius:5px; border-bottom-left-radius:5px; border-width:0 1px 1px; top:-9999px; left:-9999px; overflow:hidden; position:absolute; padding-left:0px; z-index:2; margin-top:-7px; }
        .Admin ul li{ list-style:none; float:left; }
        .Admin ul li span{ font-size:14px; display:block; padding:10px; color:#000000; height:14px !important; cursor:pointer; text-decoration:underline; }
        .Admin:hover ul.admindrop{ left:auto; top:auto; z-index:99999; display:block; overflow:hidden; }
        ul.buttonHolder {list-style:none;}
        ul.buttonHolder li {float:left;margin-left:10px;}
        ul.buttonHolder li #disableButtonDiv{height: 50px;left: -5px;position: relative;top: -5px;width: 111px;z-index: 9999;}
        ul#filePanel{list-style:none; margin:0px; padding:0px;}
        ul#filePanel li{float:left;}
        /*div#middle{background: url(images/gray_bg.png) center repeat-y !important;background-position-x: -435px!important;background-color:rgba(0, 0, 0, 0) !important;}*/
        div#middle{background: <?php if (isset($_GET['sect']) && $_GET['sect'] != 'drawing_register') { ?>url(images/gray_bg.png)<?php } ?> center repeat-y !important; background-position-x: -435px!important;background-color:#FFFFFF !important;}
<?php if (isset($_GET['type']) && $_GET['type'] == 'pmb') { ?>
            div.content_container{ width:100% !important; }
<?php } else { ?>
            .SearchTabs li a span{background: url("images/selected_right_pc.png") no-repeat scroll right center rgba(0, 0, 0, 0) !important;}
            .SearchTabs li a span:hover{ background:url('images/active_right_pc.png') no-repeat scroll right center rgba(0, 0, 0, 0) !important;}
<?php } ?>
        .selectedDoc{ background:#FF1717 !important; }
        tr.selectedDoc td.sorting_1{ background:#FF1717 !important; }
        ul.headerHolder {list-style:none;}
        ul.headerHolder li{float:left; width:230px;}
        /*.big_container{width:1200px !important;}*/
        .actionButton{float:right; margin:9px 4px 10px 0px;cursor:pointer;}
    </style>
<?php include'data-table.php'; ?>

<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"date_approved",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"date_sent",
			dateFormat:"%d-%m-%Y"
		});
		
	};
</script>