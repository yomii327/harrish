<?php
#error_reporting(E_ALL);
#ini_set('display_errors', '1');

session_start();
ob_start();
if(!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])){ ?>
<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
$projectId = isset($_SESSION['idp'])?$_SESSION['idp']:0;

$msg ='';
require_once('includes/class.phpmailer.php');
include('includes/commanfunction.php');
require('fpdf/mc_table.php');

class PDF extends PDF_MC_Table{
	function Footer(){
		/*$this->SetY(-15);
		$this->SetFont('times','B',10);
		$this->Cell(0, 10, "Wiseworker- Copyright Wiseworking ".date('Y'), 0, 0, 'C');*/
	}
	function header_width(){
		return array(40, 30, 60, 60);
	}
}
$object = new COMMAN_Class();
$obj = new DB_Class();

$userId = $_SESSION['ww_builder']['user_id'];
$projectName = $object->selQRYMultiple('project_name, job_number', 'projects', 'is_deleted = 0 AND project_id = '.$_SESSION['idp']);
$CompanyName = $object->selQRYMultiple('user_fullname, company_name', 'user', 'is_deleted = 0 AND user_id = '.$userId);
$refNumberMsgCount = array();
$refNumberMsgCount = $object->selQRYMultiple('max(um.rfi_number) AS refNumber', 'pmb_message as m, pmb_user_message as um', 'um.project_id = '.$_SESSION['idp'].' AND m.is_draft = 0 AND um.is_deleted= 0 AND m.message_id = um.message_id');

$spCondtion = ' AND m.message_type = "General Correspondance"';
if(isset($_GET['folderType']) && $_GET['folderType'] != ""){
	$spCondtion = ' AND m.message_type = "'.$_GET['folderType'].'"';
}

$corNumCount = array();
$corNumCount = $object->selQRYMultiple('max(m.correspondence_number) AS correspondenceNumber', 'pmb_message as m, pmb_user_message as um', 'um.project_id = '.$_SESSION['idp'].' AND m.is_draft = 0 AND um.is_deleted= 0 AND m.message_id = um.message_id'.$spCondtion);

//Assing Default message users
/*#$defaultUserArr = array('156' => 'peter.paritsi@crema.com.au', '157' => 'michael.guardiani@crema.com.au', '168' => 'jesse.rosenfeld@crema.com.au', '169' => 'Austin.Giordano@crema.com.au', '172' => 'alistair.souter@crema.com.au');
$defaultUserArr = array('156' => 'peter.paritsi@crema.com.au', '157' => 'michael.guardiani@crema.com.au', '169' => 'Austin.Giordano@crema.com.au', '354' => 'jason.treasure@crema.com.au', '355' => 'joseph.frisina@crema.com.au');
$defaultIdArr = array_keys($defaultUserArr);
//Unset current user
if(in_array($_SESSION['ww_builder_id'], $defaultIdArr))		unset($defaultUserArr[$_SESSION['ww_builder_id']]);
*/
$imageCompose = '';
if(isset($_POST['image_compose'])){
	$imageCompose = $_POST['image_compose'];
}

if(isset($_POST['submit']) and $_POST['submit']=='add'){
	$messageTypeKey = $_POST['messageType'];
	if(explode("_", $_POST['messageType'])){
		$_POST['messageType'] = @end(explode("_", $messageTypeKey));
	}else{
		$_POST['messageType'] = $_POST['messageType'];
	}
	$toEmailList = array();
	$ccEmailList = array();
	#print_r($_POST);die;
	$messageData1 = explode('<input type="hidden">',$_POST['messageDetails']);
	#echo "<pre>";print_r($messageData1);
	//Code update for avoid condition for duplicate record for RFI messages Start Here
	if($_POST['messageType'] == 'Request For Information' && isset($_POST['save'])){//Code for by pass same RFI Number project wise
		#$rfiData = $object->selQRYMultiple('DISTINCT rfi_number', 'pmb_user_message', 'project_id = '.$projectId.' AND is_deleted = 0 AND rfi_number != 0 AND rfi_number = '.$_POST['RFInumber']);
		$rfiData = $object->selQRYMultiple('DISTINCT a.rfi_number', 'pmb_user_message as a, pmb_messages as b', 'a.project_id = '.$projectId.' AND b.message_type = "Request For Information" AND a.message_id = b.message_id AND a.is_deleted = 0 AND a.rfi_number != 0 AND a.rfi_number = '.$_POST['RFInumber']);
		if(isset($rfiData) && $rfiData[0]['rfi_number'] != "") {//By pass request here
			header('Location:?sect=sent_box&sm=1');
			echo '<script language="javascript" type="text/javascript">window.location.href="?sect=sent_box&sm=1";</script>';
		}
	}
	if($_POST['messageType'] == 'Consultant Advice Notice' && isset($_POST['save'])){//Code for by pass same RFI Number project wise
		$rfiData = $object->selQRYMultiple('DISTINCT a.rfi_number', 'pmb_user_message as a, pmb_messages as b', 'a.project_id = '.$projectId.' AND b.message_type = "Consultant Advice Notice" AND a.message_id = b.message_id AND a.is_deleted = 0 AND a.rfi_number != 0 AND a.rfi_number = '.$_POST['RFInumber']);
		if(isset($rfiData) && $rfiData[0]['rfi_number'] != ""){//By pass request here
			header('Location:?sect=sent_box&sm=1');
			echo '<script language="javascript" type="text/javascript">window.location.href="?sect=sent_box&sm=1";</script>';
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
	}*/
	#print_r($_POST['recipTo']);print_r($_POST['recipCC']);die;
	#echo '<pre>';print_r($_POST);print_r($_SESSION[$_SESSION['idp'].'_emailfile']);print_r($_SESSION[$_SESSION['idp'].'_orignalFileName']);die;

	//Data Filteration Section Start Here
	function addDoubleQuotes($element){	return '"'.$element.'"'; }//Function fore adding double qoutations
	$ccUsserIDArr = array();
	if(isset($_POST['recipCC']) && is_array($_POST['recipCC'])){
		$ccFilterList = array_map("addDoubleQuotes", $_POST['recipCC']);
		$userDataCC = $object->selQRYMultiple('u.user_id, u.user_email', 'user AS u, user_projects AS up', 'u.is_deleted = 0 AND up.is_deleted = 0 AND up.user_id = u.user_id AND up.project_id = '.$_SESSION['idp'].' AND u.user_email IN ('.join(",", $ccFilterList).')');

		if($userDataCC != false){
			foreach($userDataCC as $usrDt){
				$ccUsserIDArr[$usrDt['user_email']] = $usrDt['user_id'];
			}
		}
	}
	//Data Filteration Section End Here
	$from = $_SESSION['ww_builder_id'];
	$recipTo = !empty($_POST['recipTo']) ? $_POST['recipTo'] : 0;
	$recipCC = $_POST['recipCC'];
	$subject1 = $subject = empty($_POST['subject'])?'no subject':$_POST['subject'];
	$purchaserLocation = $_POST['purchaserLocation'];
	$tags = $_POST['tags'];	
	$companyTag = isset($_POST['companyTag'])?$_POST['companyTag']:'';	
	$messageType = $_POST['messageType'];
	
	$refrenceNumber = (isset($_POST['newDynamicGenRefrenceNumber']) && !empty($_POST['newDynamicGenRefrenceNumber'])) ? $_POST['newDynamicGenRefrenceNumber'] : $_POST['refrenceNumber']." ".$_POST['messageType'];
	$messagePlainDetails = $_POST['plainText'];
	$messgeId = (isset($_POST['composeId']) && !empty($_POST['composeId'])) ? $_POST['composeId'] : 0;

	$RFInumber = "";$RFIdescription = "";$RFIstatus = "";$fixedByDate = "";
	if($messageType == 'Request For Information' || $messageType == 'Consultant Advice Notice'){
		$RFInumber = $_POST['RFInumber'];
		$fixedByDate = $object->dateChanger('-', '-', $_POST['fixedByDate']);
		$RFIstatus = $_POST['RFIstatus'];
	}
	$correspondenceNumber = $_POST['correspondenceNumber'];	
	$messageDetails = $_POST['messageDetails'];
	$dateSent = date("Y-m-d h:i:s", strtotime($_POST['dateSent']) );
	$dateApproved = date("Y-m-d h:i:s", strtotime($_POST['dateApproved']) );
	$sent = $_POST['sent'];
	$approved = $_POST['approved'];
	$claimed = $_POST['claimed'];
	$certified = $_POST['certified'];
	$invoiced = $_POST['invoiced'];

	$rfi_reference = $_POST['rfi_reference'];
	/*if($messageType == 'Request For Information'){//New Updated Dated : 05-12-2013<br>
		$messageDetails = ' RFI #'.$RFInumber.'<br><br>'.$_POST['messageDetails'];
	}*/
	if($messageType == 'Request For Information'){ //New Updated Dated : 05-12-2013<br>				
		$html = '';	$usersTo = ''; $usersCC = '';
		if(isset($recipTo) && !empty($recipTo)){
			foreach($recipTo as $to){
				if(is_numeric($to)){
					$query = "select u.user_id, u.user_type, u.user_fullname as fullname, u.user_email as email, u.attachment_noti_type from user as u where u.is_deleted=0 and u.user_id='".$to."'";
					$getUserDetails = mysql_query($query);
					while($gUDetail = mysql_fetch_array($getUserDetails)){
						$usersTo.= empty($usersTo)?$gUDetail['fullname']:", ".$gUDetail['fullname'];
					}
				}elseif(!empty($to)){
					$sql = "SELECT ab.full_name as name, user_email as email, ab.attachment_noti_type FROM pmb_address_book as ab where user_email IN ('".$to."') UNION	SELECT iss.company_name as name, iss.issue_to_email as email FROM inspection_issue_to as iss WHERE issue_to_email IN ('".$to."')";
					$result = mysql_query($sql);
					$row = mysql_fetch_row($result);
					if($row>0){
						$usersTo.= empty($usersTo)?$row[0]:", ".$row[0];
					}else{
						$usersToArr = explode('@',$to);
						$usersTo.= empty($usersTo)?$usersToArr[0]:", ".$usersToArr[0];
					}
				}
			}
		}		

		if(isset($recipCC)){
			foreach($recipCC as $cc){
				if(!empty($cc)){
					$sql = "SELECT ab.full_name as name, user_email as email FROM pmb_address_book as ab where user_email IN ('".$cc."') UNION	SELECT iss.company_name as name, iss.issue_to_email as email FROM inspection_issue_to as iss WHERE issue_to_email IN ('".$cc."')";
					$result = mysql_query($sql);
					$row = mysql_fetch_row($result);
					if($row>0){
						$usersCC.= empty($usersCC)?$row[0]:", ".$row[0];
					}else{					
						$usersCCArr = explode('@',$cc);
						$usersCC.= empty($usersCC)?$usersCCArr[0]:", ".$usersCCArr[0];
					}
				}
			}
		}
		
		if(!empty($usersCC)){
			$float = 'float:left;';
		}else{
			$float = '';
		}	
		$message = explode('--',$_POST['messageDetails']);
		#print_r($_REQUEST);die;
		$imgPath = 'http://'.DOMAIN;
		$html = '<div style="color:black;border: 1px solid; padding:5px;"><div style="float:left;"><img width="" height="70" src="'.$imgPath.'images/logo.png" height="40"  /></div><div style="float:right;"><h3>WISEWORKING PTY LTD</h3><strong>416 High Street<br>Kew, Victoria, 3068</strong></div><br><br><br><br clear="both"><h1>REQUEST FOR INFORMATION</h1><br><div><span style="width:85px;float:left;"><b>TO: </b></span><span>'.$usersTo.'</span><hr><br><span style="width:85px;'.$float .'"><b>CC: </b></span><span>'.$usersCC.'</span><hr><br><span style="width:85px;float:left;"><b>FROM: </b></span><span>'.$_SESSION['ww_builder_full_name'].', WISEWORKING PTY LTD</span><hr><br><span style="width:85px;float:left;"><b>DATE: </b></span><span>'.date('j F Y').'</span><hr><br><span style="width:85px;float:left;"><b>RFI #: </b></span><span>'.$RFInumber.'</span><hr><br><span style="width:85px;float:left;"><b>PROJECT: </b></span><span>'.$projectName[0]['project_name'].'</span><hr><br><span style="width:90px;float:left;"><b>RESPOND BY: </b></span><span>&nbsp;'.date('j F Y',strtotime($_POST['fixedByDate'])).'</span><hr><br><span style="width:85px;float:left;"><b>SUBJECT: </b></span><span>'.$subject.'</span><hr><br><span style="width:85px;float:left;"><b>RFI Reference: </b></span><span>'.$rfi_reference.'</span><hr><br><div style="width:85px;"><big><b>SUMMARY: </b></big></div><br><div>'.$message[0].'</div><br><span style="width: 400px;float:left">'.$_SESSION['ww_builder_full_name'].'</span><span style="width:400px;">'.date('d/m/Y').'</span><br clear="both"><span style="width: 200px;float:left"><b>Issued By  </b></span><span style="width: 200px;float:left"><b>Signature</b></span><span style="width:400px;"><b>Date</b></span><br><!--hr><div><b>Action Taken (If Applicable)</b></div><br><br><hr><div><b>Actions to be taken:
</b></div><br><span style="float:left;width: 320px;">Action</span><span style="float:left;width: 100px;">Due Date</span><span style="width:400px;">Complete Date</span><br><br></div--><br><br></div>';
		$messageDetails = $html;
	}
	$ccAddress = '';
	$toExtraAddress = '';
	#Add attachment if founc any attachment.
	if($_POST['emailAttachedAjax'] == 1) {
		$attahment1 = $_SESSION[$_SESSION['idp'].'_emailfile'];
	}
	// Remove old attachment if found any attachment
	
	if(isset($_POST['removeAttachment'])){
		if(explode(',', str_replace(', ', ',', $_POST['removeAttachment']))){
			$removeAttachments = explode(',', str_replace(', ', ',', $_POST['removeAttachment']));
			foreach($removeAttachments as $attachID){
				$key = "";
				$key = array_search(trim($attachID), $_SESSION[$_SESSION['idp'].'_emailfile']);
				if($key != "")
					unset($_SESSION[$_SESSION['idp'].'_emailfile'][$key]);
				if(is_numeric($attachID)){
					$attDeleteQRY = "UPDATE `pmb_attachments` SET `is_deleted` = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE project_id = ".$_SESSION['idp']." AND `attach_id` =".$attachID;
					mysql_query($attDeleteQRY);
				}
			}
		}else{
			$key = "";
			$key = array_search(trim($attachID), $_SESSION[$_SESSION['idp'].'_emailfile']);
			if($key != "")
				unset($_SESSION[$_SESSION['idp'].'_emailfile'][$key]);
			if(is_numeric($attachID)){
				$attDeleteQRY = "UPDATE `pmb_attachments` SET `is_deleted` = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE project_id = ".$_SESSION['idp']." AND `attach_id` =".$attachID;
				mysql_query($attDeleteQRY);
			}
		}
	}
	
	if($messageType != 'Request For Information'){
		$subject1 = $subject;
		$subject = 'CN # '.$correspondenceNumber.":".$messageType.':'.$subject;//New Update
		//$subject = $subject;//New Update
		$subjectSave = $subject1;//New Update
	}else{
		$subject = $projectName[0]['project_name'].':RFI'.$RFInumber.':'. $messageType .':'.$subject;//New Updated Dated : 28-10-2014
		//$subject = 'RFI # '.$RFInumber." ".$subject;//New Updated Dated : 05-12-2013
	}
	
	if($messageType == 'Consultant Advice Notice'){
		$subject = 'RFI-'.$RFInumber.':'. $messageType .':'.$subject;//New Updated Dated : 28-10-2014
	}
	
	#echo 'h1->'.$subjectSave.'<-h1';die;
	//Code for New Document Transmittal Template Start Here
	
	if($messageType == 'Document Transmittal'){
		//Data Fetch Section Start Here
		$recipTo = !empty($_POST['recipTo'])?$_POST['recipTo']:array(0);
		if(isset($recipTo)){
			$toArray = array();
			foreach($recipTo as $to){
				$toArray[] = $to;
			}
		}//condition for to
		#print_r($toArray);die;
		$ts = '';
		$ij = 0;
		foreach($toArray as $tt){
			$ts .= "'".$tt."'";
			if($ij < sizeof($toArray)-1){
				$ts .=", ";
			}
			$ij++;
		}
		//echo $ts;die;
		//cc condition
		if(isset($recipCC)){
			$ccArray = array();
			foreach($recipCC as $cc){
				$ccArray[] = $cc;
			}
		}
		//condition for cc
		$tscc = '';
		$ijcc = 0;
		foreach($ccArray as $ccval){
			$tscc .= "'".$ccval."'";
			if($ijcc < sizeof($ccArray)-1){
				$tscc .=", ";
			}
			$ijcc++;
		}
		//cc condition end
		//condition for to<br>
		$ToData = $object->selQRYMultiple('user_id, company_name, user_name, user_fullname, user_email, attachment_noti_type', 'user', 'user_id IN('.$ts.')');
		$toArrayMainVal = array();
		foreach($ToData as $ToDataVal){
			$toArrayMainVal[$ToDataVal['user_id']] =  $ToDataVal; 
		}
		$usersTo = '';
		$companys = '';
		$ijz = 0;
		foreach($toArray as $toArrayVa){
			if(!empty($toArrayMainVal[$toArrayVa]) && isset($toArrayMainVal[$toArrayVa])){
				$usersTo .= $toArrayMainVal[$toArrayVa]['user_fullname'];
				$companys .= $toArrayMainVal[$toArrayVa]['company_name']."  ";  
				if($ijz < sizeof($toArrayMainVal)-1){
					$usersTo .=", ";
					$companys .=", ";
				}
				$ijz++;
			}else{
				$usersToArr = explode('@',$toArrayVa);
				$usersTo .= $usersToArr[0];
				if($ijz < sizeof($toArray)-1){
					$usersTo .=", ";
					$companys .=", ";
				}
				$ijz++;
			}
		}
		#echo $usersTo;die;
		//cc condition strats here
		#echo $tscc;
		$cccData = $object->selQRYMultiple('user_id, company_name, user_name, user_fullname, user_email', 'user', 'user_email IN('.$tscc.')');
		$toArrayMainValcc = array();
		foreach($cccData as $ToDataValcc){
			$toArrayMainValcc[$ToDataValcc['user_email']] =  $ToDataValcc; 
		}
		$usersTocc = '';
		$companyscc = '';
		$ijzcc = 0;
		#print_r($toArrayMainValcc);
		foreach($ccArray as $toArrayVacc){
			if(!empty($toArrayMainValcc[$toArrayVacc]) && isset($toArrayMainValcc[$toArrayVacc])){
				$usersTocc .= $toArrayMainValcc[$toArrayVacc]['user_fullname']."  ";
				$companyscc .= $toArrayMainValcc[$toArrayVacc]['company_name']."  ";  
				if($ijzcc < sizeof($toArrayMainValcc)-1){
					$usersTocc .=", ";
					$companyscc .=", ";
				}
				$ijzcc++;
			}else{
				$usersToArrcc = explode('@',$toArrayVa);
				$usersTocc .= $usersToArrcc[0];
				if($ijzcc < sizeof($toArraycc)-1){
					$usersTocc .=", ";
					$companyscc .=", ";
				}
				$ijz++;
			}
		}
		#echo $usersTocc;die;
		$docRegData = $object->selQRYMultiple('dr.id, dr.title, dr.number, dr.revision, dr.comments', 'drawing_register_module_one AS dr, drawing_register_revision_module_one AS drr', 'drr.id IN ('.join(',', $_SESSION[$_SESSION['idp'].'_dtReportids']).') AND drr.is_deleted = 0 AND dr.is_deleted = 0 AND drr.project_id = '.$_SESSION['idp'].' AND dr.project_id = '.$_SESSION['idp'].' AND drr.drawing_register_id = dr.id', 'test');
		
		$corNumCount = array();
		$corNumCount = $object->selQRYMultiple('max(m.correspondence_number) AS correspondenceNumber', 'pmb_message as m, pmb_user_message as um', 'um.project_id = '.$_SESSION['idp'].' AND m.is_draft = 0 AND um.is_deleted= 0 AND m.message_id = um.message_id AND m.message_type = "Document Transmittal"');
		
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
		$pdf->MultiCell(0, 5, $companyDetailsData[0]['comp_businessadd1'].', '.$companyDetailsData[0]['comp_businessadd2'].', '.$companyDetailsData[0]['comp_bussuburb'].', '.$companyDetailsData[0]['comp_businessstate'].', '.$companyDetailsData[0]['comp_businesscountry']);		
		$pdf->MultiCell(0, 5, 'P: '.$companyDetailsData[0]['comp_mobile']);		
		$pdf->Image('./company_logo/logo.png', 125, 5, 'png', -100);
		$pdf->Ln(3);
		
		$pdf->Line($pdf->GetX()-10, $pdf->GetY()-1, $pdf->GetX()+200, $pdf->GetY()-1);
		$pdf->SetFont('times', '', 16);
		$pdf->Cell(160, 5, 'TRANSMITTAL OF DOCUMENTS', 0, 0);		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(0, 5, 'Date: '.date('d/m/Y'), 0, 0);		
		$pdf->Ln();
		$pdf->Line($pdf->GetX()-10, $pdf->GetY()+2, $pdf->GetX()+200, $pdf->GetY()+2);
		$pdf->Ln(5);
		
		$pdf->Cell(100, 5, 'PROJECT: '.$projectName[0]['project_name'], 0, 0);
		$pdf->Cell(120, 5, 'PROJECT NUMBER: '.$projectName[0]['job_number'], 0, 0);
		$pdf->Ln();
		$pdf->Line($pdf->GetX()-10, $pdf->GetY(), $pdf->GetX()+200, $pdf->GetY());
		//To List Section
		$pdf->Ln(5);
		$pdf->SetFont('times', 'B', 10);
		$pdf->MultiCell(0, 5, 'To: '.$usersTo);		
		//CC List Section
		$pdf->MultiCell(0, 5, 'CC: '.$usersTocc);		
		$correspondenceNumber = ++$corNumCount[0]['correspondenceNumber'];
		$pdf->Ln(5);
		$pdf->SetFont('times', '', 8);
		$pdf->MultiCell(0, 5, 'REF. No. '.$correspondenceNumber);
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
		foreach($docRegData as $docData){
			$pdf->Row(array($docData['number'], $docData['revision'], $docData['title'], $docData['comments']));
		}
		$pdf->Ln(5);
		$pdf->SetFont('times', 'B', 8);
		$pdf->Cell(9, 5, 'FOR:', 0, 0);		
		$pdf->SetFont('times', 'BI', 8);
		$pdf->Cell(50, 5, $subject1, 0, 0);

		//File Output Section 
		$docTranFile = $_SESSION['idp']."_".rand().".pdf";
		$path = "./attachment/".$docTranFile;
		array_push($attahment1, $docTranFile);//Add in Attachment
		array_push($_SESSION[$_SESSION['idp'].'_emailfile'], $docTranFile);//Add in Attachment
		array_push($_SESSION[$_SESSION['idp'].'_orignalFileName'], "Document Transmittal.pdf");//Add in Attachment
		$pdf->Output($path);
	}
//print_r($_SESSION);
	//Code for New Document Transmittal Template End Here	
	if((!empty($recipTo) && !empty($subject) && !empty($messageDetails)) || isset($_POST['saveDraft'])){		
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
			
			//Message Attachments Start Here
			$attachmentSizeLimit = '15728640';	// Upload Size Limit upto 15MB[15728640 bytes]
			$attachmentSize = 0;
			$sizeExceed = 0;
			$unlinkFile = 0;
			$attachmentCount = count($attahment1);
			//print_r($attahment1);die;
			$val = "";
			$k = 1;
			if(isset($attahment1) && !empty($attahment1)){
				foreach($attahment1 as $key=>$value){
					//----------------Attachments check--------------------
					$size = filesize("attachment/".$value);
					$attachmentSize += $size;
					if($size > $attachmentSizeLimit)
					{
						$sizeExceed = 1;
						$aUrl = $value;
					}else{
						$aUrl = $value;
						$val .= "Attachment_".$k." url : <a href='http://" . $_SERVER['SERVER_NAME'].'/attachment/'.$value."'>Click here</a><br>";
					}
					//----------------/Attachments check--------------------										
					++$k;
				}
			}
			if(isset($_REQUEST['filesArr']) && !empty($_REQUEST['filesArr'])){
				foreach($_REQUEST['filesArr'] as $files){
					$mail->AddAttachment("pmb_attachment/".$_SESSION['idp'].'/'.$files, $files);
				}	
			}
			#echo $aUrl;die;
			//Message Attachments End Here
			$msgAttach = "";
			$attUrl = $val;
			#$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
			#$msgAttach .="<a href='".$path."/pms.php?sect=message_details&id=".base64_encode($messageBoard['thread_id'])."&type=inbox&projID=".$_SESSION['idp']."&byEmail=".$byEmail."' target='_blank'>".$path."/pms.php?sect=message_details&id=".base64_encode($messageBoard['thread_id'])."&type=inbox&projID=".$_SESSION['idp']."&byEmail=".$byEmail."</a>";		
			$msgAttach .= $attUrl;
			if($attachmentSize > $attachmentSizeLimit && $attachmentCount == 1){//Attachment Size less than Max Size and Attachment Count Equals 1				
				$attUrl = "Attachment url : <a href='http://" . $_SERVER['SERVER_NAME'].'/wiseworker/'.$aUrl."'>Click here</a><br>";
				$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));

				$mail->AddReplyTo("WiseworkingSystems@wiseworking.com.au", "WiseWorker - ".$projectName[0]['project_name']." - ".$_SESSION['ww_builder']['user_fullname']);
				$mail->SetFrom("WiseworkingSystems@wiseworking.com.au", "WiseWorker - ".$projectName[0]['project_name']." - ".$_SESSION['ww_builder']['user_fullname']);
				
				$mail->IsHTML(true);
				$byEmail = 3;
				/*			if($messageType == 'Request For Information')//New Updated Dated : 05-12-2013
				$msg .= ' RFI #'.$RFInumber.'<br><br>';*/
				$msg .= $messageDetails;
				#$userImage = $object->selQRYMultiple('user_id, user_signature', 'user', 'is_deleted  = 0 AND user_id = '.$_SESSION['ww_builder_id'].'');
				#$msg .= "<img src='".IMG_SRC."user_images/".$userImage[0]['user_signature']."'>";
				
				if(isset($recipCC)){
					foreach($recipCC as $cc){
						$mail->AddBCC(trim($cc), '');
						//$mail->AddCC(trim($cc), '');
						$ccAddress.= ($ccAddress=='')?$cc:', '.$cc;
						$ccEmailList.= ($ccEmailList=='')?$cc:', '.$cc;
					}
				}

				if(isset($recipTo) && !empty($recipTo)){
					foreach($recipTo as $to){
						if(is_numeric($to)){
							$query = "select u.user_id, u.user_type, u.user_fullname as fullname, u.user_email as email from user as u where u.is_deleted=0 and u.user_id='".$to."'";
							$getUserDetails = mysql_query($query);
							while($gUDetail = mysql_fetch_array($getUserDetails)){
								$mail->AddBCC($gUDetail['email'],''); // To
								//$mail->AddAddress($gUDetail['email'],''); // To
								$toEmailList[] = $gUDetail['email'];
							}
						} elseif(!empty($to)) {
							$mail->AddBCC(trim($to), '');
							//$mail->AddAddress($to,''); // To
							$toExtraAddress.= ($toExtraAddress=='')?$to:', '.$to;
							$toEmailList[] = $to;
						}
					}
				}
				
				if(get_magic_quotes_gpc()) {
					$messageDetails = stripslashes($messageDetails);
				}
			
				$messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));
				$recipTo = !empty($_POST['recipTo'])?$_POST['recipTo']:array(0);
				if(isset($recipTo)){
					if($_POST['composeId'] != 0 && isset($_POST['save'])){
						$messgeId = 0;//Forcfully create new message and delete old one due to draft issue.
					}					
					foreach($recipTo as $to){
						if(is_numeric($to)){
							$messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageTypeKey, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave, "", $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
							if(!empty($messageBoard))
								$messgeId = $messageBoard['messgeId'];
						}else{
							$messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageTypeKey, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave, "", $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
							if(!empty($messageBoard))
								$messgeId = $messageBoard['messgeId'];
						}
						$attahment1 = '';
					}
				}	

				//Added for display message for cc sections Start Here
				if(isset($ccUsserIDArr)){
					foreach($ccUsserIDArr as $ccInsertEmail=>$ccInsertId){
						$messageBoard = $object->messageBoard($projectId, $from, $ccInsertId, $subject, $messageTypeKey, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 1, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave, "", $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
						$messgeId = $messageBoard['messgeId'];
					}
				}	
				//Added for display message for cc sections End Here
				$msg .= $msgAttach."<br/><br/>click here to access your message.<br>
							<a href='".$path."/pms.php?sect=message_details&id=".base64_encode($messageBoard['thread_id'])."&type=inbox&projID=".$_SESSION['idp']."&byEmail=".$byEmail."' target='_blank'>".$path."/pms.php?sect=message_details&id=".base64_encode($messageBoard['thread_id'])."&type=inbox&projID=".$_SESSION['idp']."&byEmail=".$byEmail."</a>";
				#$msg .= "<br/><br/>Thanks,<br> DefectId customer care";
				
				$mail->Subject = "WiseWorker:".$messgeId."-".$subject;
				$msg .= empty($toEmailList)?'':'To : '.implode(', ', $toEmailList).'<br>';
				$msg .= empty($ccEmailList)?'':'Cc : '.implode(', ', $ccEmailList).'<br>';

				if(!empty($imageCompose)){
					// $imgAttach = 'attachment/'.$imageCompose;
					// $mail->addAttachment($imgAttach, 'Attachment');
					$mail->AddAttachment("attachment/".$imageCompose, $imageCompose);
				}
				$mail->MsgHTML($msg);			
			
				if(isset($_POST['save']) && !empty($messageBoard)){
				//if(isset($_POST['save'])){
					if(!$mail->Send()){
						echo $mail->ErrorInfo;
					}else{
						echo 'Mail sent';	
					}
				}
				$mail->ClearAddresses();
				$mail->ClearAllRecipients( ); // clear all
				$mail->ClearCustomHeaders();				
			} elseif(($attachmentSize > $attachmentSizeLimit) && ($attachmentCount > 1) && ($sizeExceed == 0)){//Mail Size Greater Than Max Size and Attachment Count Greater than 1 and Each Attachment Size Less Than Max Size
				$i1=1;
				do{					
					$attCount = 0;
					$attachmentSize1 = 0;
					$mailAttachment = array();
					foreach($attahment1 as $key=>$value){
						$size = filesize("attachment/".$value);						
						$attachmentSize1 += $size;						
						//----------------Adding Attachments-----------------
						if(($size <= $attachmentSizeLimit) && ($attachmentSize1 < $attachmentSizeLimit)){			
							$attachmentSize += $size;
							$mailAttachment[] = $value;
							unset($attahment1[$key]);
						}else{
							$attachmentSize1 -= $size;
						}
						//$attCount++;
					}
						//----------Adding Attachments---------------------
						//----------MULTIPLE TIMES MAILS SENT--------------

						if(isset($mailAttachment) && !empty($mailAttachment)){
						//while(empty($attahment1)){
						# Start :- Send Email 

							$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));

							$mail->AddReplyTo("WiseworkingSystems@wiseworking.com.au", "WiseWorker - ".$projectName[0]['project_name']." - ".$_SESSION['ww_builder']['user_fullname']);
							$mail->SetFrom("WiseworkingSystems@wiseworking.com.au", "WiseWorker - ".$projectName[0]['project_name']." - ".$_SESSION['ww_builder']['user_fullname']);
							

							$byEmail = 3;
							/*			if($messageType == 'Request For Information')//New Updated Dated : 05-12-2013
							$msg .= ' RFI #'.$RFInumber.'<br><br>';*/
							//$mail->IsHTML(true);
							//$msg .= $messageDetails;

							if(isset($recipCC)){
								foreach($recipCC as $cc){
									$mail->AddBCC(trim($cc), '');
									//$mail->AddCC(trim($cc), '');
									$ccAddress.= ($ccAddress=='')?$cc:', '.$cc;
								}
							}

							if(isset($recipTo) && !empty($recipTo)){
								foreach($recipTo as $to){
									if(is_numeric($to)){
										$query = "select u.user_id, u.user_type, u.user_fullname as fullname, u.user_email as email, u.attachment_noti_type from user as u where u.is_deleted=0 and u.user_id='".$to."'";
										$getUserDetails = mysql_query($query);
										while($gUDetail = mysql_fetch_array($getUserDetails)){
											#$mail->AddAddress($gUDetail['email'],''); // To
										}
									}elseif(!empty($to)){
										$mail->AddBCC($to,''); // To
										//$mail->AddAddress($to,''); // To
										$toExtraAddress.= ($toExtraAddress=='')?$to:', '.$to;
									}
								}
							}
							$toNotiArray = array();
							$toLinkArray = array();
							if(isset($recipTo) && !empty($recipTo)){
								foreach($recipTo as $to){
									if(is_numeric($to)){
									echo $query = "select u.user_id, u.user_type, u.user_fullname as fullname, u.user_email as email from user as u where u.is_deleted=0 and u.user_id='".$to."'";
										$getUserDetails = mysql_query($query);
										while($gUDetail = mysql_fetch_array($getUserDetails)){
											#$mail->AddAddress($gUDetail['email'],''); // To
											if($gUDetail['attachment_noti_type']==1){
												$toNotiArray[$gUDetail['user_id']] = $gUDetail['email'];		
											}else{
												$toLinkArray[$gUDetail['user_id']] = $gUDetail['email'];	
											}
										}
									}elseif(!empty($to)){
										#$mail->AddAddress($to,''); // To
										#$toExtraAddress.= ($toExtraAddress=='')?$to:', '.$to;
									echo 	$sql = "SELECT ab.full_name as name, user_email as email, ab.attachment_noti_type FROM pmb_address_book as ab where user_email IN ('".$to."') UNION	SELECT iss.company_name as name, iss.issue_to_email as email FROM inspection_issue_to as iss WHERE issue_to_email IN ('".$to."')";
										$result = mysql_query($sql);
										$row = mysql_fetch_row($result);
										if($row[3]==1){
											$toNotiArray[$gUDetail[2]] = $gUDetail[2];		
										}else{
											$toLinkArray[$gUDetail[2]] = $gUDetail[2];	
										}
									}
								}
							}
						
							//condition for document transmittal
							if($messageType!="Document Transmittal"){
								foreach($mailAttachment as $key=>$value){
									$ext = end(explode('.', $value));
									if($_SESSION[$_SESSION['idp'].'_orignalFileName'][$key] == "Document Transmittal.pdf"){
										if($ext == 'zip' || $ext == 'exe'){

										}else{
											$mail->AddAttachment("attachment/".$value, $_SESSION[$_SESSION['idp'].'_orignalFileName'][$key]);
										}
									}else{
										if($ext == 'zip' || $ext == 'exe'){

										}else{
											$mail->AddAttachment("attachment/".$value, $_SESSION[$_SESSION['idp'].'_orignalFileName'][$key].".".$ext);
										}
									}					
								}
								
							}
							if(get_magic_quotes_gpc()) {
								$messageDetails = stripslashes($messageDetails);
							}
						
							$messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));
							$recipTo = !empty($_POST['recipTo'])?$_POST['recipTo']:array(0);
							if(isset($recipTo) && $i1 == 1){
								if($_POST['composeId'] != 0 && isset($_POST['save'])){
									$messgeId = 0;//Forcfully create new message and delete old one due to draft issue.
								}					
								foreach($recipTo as $to){
									if(is_numeric($to)){
										$messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageTypeKey, $messageDetails, $mailAttachment, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave, "", $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
										if(!empty($messageBoard))
											$messgeId = $messageBoard['messgeId'];
									}else{
										$messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageTypeKey, $messageDetails, $mailAttachment, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave, "", $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
										if(!empty($messageBoard))
											$messgeId = $messageBoard['messgeId'];
									}
									$mailAttachment = '';
								}
							}	

							//Added for display message for cc sections Start Here
							if(isset($ccUsserIDArr) && $i1 == 1){
								foreach($ccUsserIDArr as $ccInsertEmail=>$ccInsertId){
									$messageBoard = $object->messageBoard($projectId, $from, $ccInsertId, $subject, $messageTypeKey, $messageDetails, $mailAttachment, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 1, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave, "", $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
									$messgeId = $messageBoard['messgeId'];
								}
							}	
							//Added for display message for cc sections End Here
							$msg .= $_POST['messageDetails'];
							$msg .= $msgAttach. "\n\nclick here to access your message.\n<a href='".$path."/pms.php?sect=message_details&id=".base64_encode($messageBoard['thread_id'])."&type=inbox&projID=".$_SESSION['idp']."&byEmail=".$byEmail."' target='_blank'>".$path."/pms.php?sect=message_details&id=".base64_encode($messageBoard['thread_id'])."&type=inbox&projID=".$_SESSION['idp']."&byEmail=".$byEmail."</a>";
							#$msg .= "\nThanks,\n DefectId customer care";
							
							$mail->Subject = "WiseWorker:".$messgeId."-".$subject;
							
							$m1 = $mail->MsgHTML($msg);	
							if(isset($_POST['save']) && !empty($messageBoard)){							
							//if(isset($_POST['save'])){
								//$result = $mail->Send();
								/*if(!$mail->Send()){
									echo $mail->ErrorInfo;
								}else{
									echo 'Mail sent';	
								}*/
								foreach($recipTo as $to){
									if(is_numeric($to)){
									$query = "select u.user_id, u.user_type, u.user_fullname as fullname, u.user_email as email from user as u where u.is_deleted=0 and u.user_id='".$to."'";
									$getUserDetails = mysql_query($query);
										while($gUDetail = mysql_fetch_array($getUserDetails)){
											$mail->AddBCC($gUDetail['email'],''); // To
											//$mail->AddAddress($gUDetail['email'],''); // To
											$emailPMB = $gUDetail['email'];
										}
									}elseif(!empty($to)){
										$mail->AddBCC($to,''); // To
										//$mail->AddAddress($to,''); // To
										$toExtraAddress.= ($toExtraAddress=='')?$to:', '.$to;
										$emailPMB = $to;
									}
									if(!empty($imageCompose)){
										// $imgAttach = 'attachment/'.$imageCompose;
										// $mail->addAttachment($imgAttach, 'Attachment');
										$mail->AddAttachment("attachment/".$imageCompose, $imageCompose);
									}
									if(!$mail->Send()){
									 $mail->ErrorInfo;
									 $toId = $_SESSION['ww_builder_id'];
									 $subjectPMB = "Email notification failed";
									 #$messageDetailsPMB = "Your message ".$mail->Subject." was not sent ".$mail->ErrorInfo."";
									 $messageDetailsPMB = "Delivery to the following recipient failed permanently:
		<br><br>
     <a href='mailto:".$emailPMB."'>".$emailPMB."</a><br><br>

Technical details of permanent failure:<br>
".DOMAIN." tried to deliver your message, but it was rejected by the server.<br><br>

The error that the other server returned was:<br>
".$mail->ErrorInfo."";
									 //$messageBoard = $object->messageBoard($projectId, $from, $toId, $subject, $messageType, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 1, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave);
									$messageBoard = $object->messageBoard($projectId, $toId, $toId, $subjectPMB, "General Correspondence", $messageDetailsPMB, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave,'testttt', $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
									#print_r($messageBoard);
									#		die;
									}else{
										echo 'Mail sent';	
									}
									//clearing email everytime
									$mail->ClearAddresses();
									$mail->ClearBCCs();
								}
								$mail->ClearAttachments();
								$msg='';
							}
							$mail->ClearAddresses();
							$mail->ClearAllRecipients();
							$mail->ClearCustomHeaders();
							//echo "Multiple Mail sent\n";
						}//}		
					//}
					$i1++;		
				}while(!empty($attahment1));
			//------------------------------------/MULTIPLE TIMES MAILS SENT-----------------------------------------
			}elseif($attachmentSize > $attachmentSizeLimit && $attachmentCount > 1 && $sizeExceed == 1){ //Mail Size Greater Than Max Size And Attachment Count Greater Than 1 And One of the Attachment Size is Greate than Max Size 
				#echo $attachmentCount;  die;
				$attUrl = array();
				$i=0;
				if(isset($attahment1) && !empty($attahment1)){
					if($messageType != "Document Transmittal"){
						foreach($attahment1 as $key=>$value){						
							//----------------Attachments check--------------------
							$size = filesize("attachment/".$value);
							$attachmentSize += $size;
							if($size > $attachmentSizeLimit)
							{							
								$attUrl[] = "attachment/".$value;
								$i++;
							}
							else
							{
								$ext = end(explode('.', $value));
								if($_SESSION[$_SESSION['idp'].'_orignalFileName'][$key] == "Document Transmittal.pdf"){
									if($ext == 'zip' || $ext == 'exe'){

									}else{
										$mail->AddAttachment("attachment/".$value, $_SESSION[$_SESSION['idp'].'_orignalFileName'][$key]);
									}
								}else{
									if($ext == 'zip' || $ext == 'exe'){

									}else{
										$mail->AddAttachment("attachment/".$value, $_SESSION[$_SESSION['idp'].'_orignalFileName'][$key].".".$ext);
									}
								}
							}
							//----------------/Attachments check-------------------- 								
						}
					}
				}
				$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
				
				$mail->AddReplyTo("WiseworkingSystems@wiseworking.com.au", "WiseWorker - ".$projectName[0]['project_name']." - ".$_SESSION['ww_builder']['user_fullname']);
				$mail->SetFrom("WiseworkingSystems@wiseworking.com.au", "WiseWorker - ".$projectName[0]['project_name']." - ".$_SESSION['ww_builder']['user_fullname']);
				
				$mail->IsHTML(true);
				$byEmail = 3;
				/*			if($messageType == 'Request For Information')//New Updated Dated : 05-12-2013
				$msg .= ' RFI #'.$RFInumber.'<br><br>';*/
				$msg .= $messageDetails;
				#$userImage = $object->selQRYMultiple('user_id, user_signature', 'user', 'is_deleted  = 0 AND user_id = '.$_SESSION['ww_builder_id'].'');
				#$msg .= "<img src='".IMG_SRC."user_images/".$userImage[0]['user_signature']."'>";
				
				if(isset($recipCC)){
					foreach($recipCC as $cc){
						$mail->AddBCC(trim($cc), '');
						//$mail->AddCC(trim($cc), '');
						$ccAddress.= ($ccAddress=='')?$cc:', '.$cc;
					}
				}

				if(isset($recipTo) && !empty($recipTo)){
					foreach($recipTo as $to){
						if(is_numeric($to)){
						$query = "select u.user_id, u.user_type, u.user_fullname as fullname, u.user_email as email from user as u where u.is_deleted=0 and u.user_id='".$to."'";
						$getUserDetails = mysql_query($query);
							while($gUDetail = mysql_fetch_array($getUserDetails)){
							#	$mail->AddAddress($gUDetail['email'],''); // To
							}
						}elseif(!empty($to)){
							$mail->AddBCC($to,''); // To
							//$mail->AddAddress($to,''); // To
							$toExtraAddress.= ($toExtraAddress=='')?$to:', '.$to;
						}
					}
				}
				if(get_magic_quotes_gpc()) {
					$messageDetails = stripslashes($messageDetails);
				}
			
				$messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));
				$recipTo = !empty($_POST['recipTo'])?$_POST['recipTo']:array(0);
				if(isset($recipTo)){
					if($_POST['composeId'] != 0){
						$messgeId = 0;//Forcfully create new message and delete old one due to draft issue.
					}					
					foreach($recipTo as $to){
						if(is_numeric($to)){
							$messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageTypeKey, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave,'testttt', $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
							if(!empty($messageBoard))
								$messgeId = $messageBoard['messgeId'];
						}else{
							$messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageTypeKey, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave,'testttt', $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
							if(!empty($messageBoard))
								$messgeId = $messageBoard['messgeId'];
						}
						$attahment1 = '';
					}
				}	

				//Added for display message for cc sections Start Here
				if(isset($ccUsserIDArr)){
					foreach($ccUsserIDArr as $ccInsertEmail=>$ccInsertId){
						$messageBoard = $object->messageBoard($projectId, $from, $ccInsertId, $subject, $messageTypeKey, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 1, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave,'testttt', $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
						$messgeId = $messageBoard['messgeId'];
					}
				}	
				//Added for display message for cc sections End Here
				$a = '';
				if($messageType != "Document Transmittal"){
				if(!empty($i)){
					for($j=0;$j<$i;$j++){
						$n = $j+1;
						$a = $attUrl[$j];
						$msg .= "Attachment_".$n." url : <a href='http://" . $_SERVER['SERVER_NAME'].'/wiseworker/'.$a."'>Click here</a><br>";
					}
				}
				}else{
					#$msg .= $msgAttach;	
						if(!empty($i)){
						for($j=0;$j<$i;$j++){
							$n = $j+1;
							$a = $attUrl[$j];
							$msg .= "Attachment_".$n." url : <a href='http://" . $_SERVER['SERVER_NAME'].'/wiseworker/'.$a."'>Click here</a><br>";
						}
					}
				}
				$msg .= "<br/><br/>click here to access your message.<br>
							<a href='".$path."/pms.php?sect=message_details&id=".base64_encode($messageBoard['thread_id'])."&type=inbox&projID=".$_SESSION['idp']."&byEmail=".$byEmail."' target='_blank'>".$path."/pms.php?sect=message_details&id=".base64_encode($messageBoard['thread_id'])."&type=inbox&projID=".$_SESSION['idp']."&byEmail=".$byEmail."</a>";
				#$msg .= "<br/><br/>Thanks,<br> DefectId customer care";
				
				$mail->Subject = "WiseWorker:".$messgeId."-".$subject;
				
				$mail->MsgHTML($msg);			

				if(isset($_POST['save']) && !empty($messageBoard)){				
					//$result = $mail->Send();
					/*if(!$mail->Send()){
						echo $mail->ErrorInfo;
					}else{
						echo 'Mail sent';	
					}*/
					foreach($recipTo as $to){
						if(is_numeric($to)){
						$query = "select u.user_id, u.user_type, u.user_fullname as fullname, u.user_email as email from user as u where u.is_deleted=0 and u.user_id='".$to."'";
						$getUserDetails = mysql_query($query);
							while($gUDetail = mysql_fetch_array($getUserDetails)){
								$mail->AddBCC($gUDetail['email'],''); // To
								//$mail->AddAddress($gUDetail['email'],''); // To
								$emailPMB = $gUDetail['email'];
							}
						}elseif(!empty($to)){
							$mail->AddBCC($to,''); // To
							//$mail->AddAddress($to,''); // To
							$toExtraAddress.= ($toExtraAddress=='')?$to:', '.$to;
							$emailPMB = $to;
						}
						if(!empty($imageCompose)){
							// $imgAttach = 'attachment/'.$imageCompose;
							// $mail->addAttachment($imgAttach, 'Attachment');
							$mail->AddAttachment("attachment/".$imageCompose, $imageCompose);
						}
						if(!$mail->Send()){
						$mail->ErrorInfo;
						$toId = $_SESSION['ww_builder_id'];
						$subjectPMB = "Email notification failed";
						#$messageDetailsPMB = "Your message ".$mail->Subject." was not sent ".$mail->ErrorInfo."";
						$messageDetailsPMB = "Delivery to the following recipient failed permanently:
								<br><br>
							 <a href='mailto:".$emailPMB."'>".$emailPMB."</a><br><br>

						Technical details of permanent failure:<br>
						".DOMAIN." tried to deliver your message, but it was rejected by the server.<br><br>

						The error that the other server returned was:<br>
						".$mail->ErrorInfo."";
						 //$messageBoard = $object->messageBoard($projectId, $from, $toId, $subject, $messageType, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 1, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave);
						$messageBoard = $object->messageBoard($projectId, $toId, $toId, $subjectPMB, "General Correspondence", $messageDetailsPMB, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave, "", $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
						#print_r($messageBoard);
						#		die;
						}else{
							echo 'Mail sent';	
						}
						//clearing email everytime
						$mail->ClearAddresses();
						$mail->ClearBCCs();
					}
				}
				$mail->ClearAddresses();
				$mail->ClearAllRecipients();
				$mail->ClearCustomHeaders();				
			} elseif($attachmentSize < $attachmentSizeLimit || isset($_POST['saveDraft'])) {
//------------------------------------SINGLE MAILS SENT-----------------------------------------
				#print_r($_POST);die;
				$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
				$external_email = $_POST["external_email"];

				if($external_email == 1){
					$recipTo = array($from);
					$to = $from;
				}
				
				$mail->AddReplyTo("WiseworkingSystems@wiseworking.com.au", "WiseWorker - ".$projectName[0]['project_name']." - ".$_SESSION['ww_builder']['user_fullname']);
				$mail->SetFrom("WiseworkingSystems@wiseworking.com.au", "WiseWorker - ".$projectName[0]['project_name']." - ".$_SESSION['ww_builder']['user_fullname']);
				
				$mail->IsHTML(true);
				$byEmail = 3;
				/*			if($messageType == 'Request For Information')//New Updated Dated : 05-12-2013
				$msg .= ' RFI #'.$RFInumber.'<br><br>';*/
				
				#$msg .= $messageDetails;
				$msg .= $messageData1[0];
				
				
				#$userImage = $object->selQRYMultiple('user_id, user_signature', 'user', 'is_deleted  = 0 AND user_id = '.$_SESSION['ww_builder_id'].'');
				#$msg .= "<img src='".IMG_SRC."user_images/".$userImage[0]['user_signature']."'>";
				#echo $msg;die;
				if(isset($recipCC)){
					foreach($recipCC as $cc){
						$mail->AddBCC(trim($cc), '');
						//$mail->AddCC(trim($cc), '');
						$ccAddress.= ($ccAddress=='')?$cc:', '.$cc;
						$ccEmailList[] = trim($cc);
					}
				}

				//new code
				$toNotiArray = array();
				$toLinkArray = array();
				if(isset($recipTo) && !empty($recipTo)){
					foreach($recipTo as $to){
						if(is_numeric($to)){ 
							$query = "select u.user_id, u.user_type, u.user_fullname as fullname, u.user_email as email, u.attachment_noti_type from user as u where u.is_deleted=0 and u.user_id='".$to."'";
							$getUserDetails = mysql_query($query);
							while($gUDetail = mysql_fetch_array($getUserDetails)){
								#print_r($gUDetail);die;
								#$mail->AddAddress($gUDetail['email'],''); // To
								if($gUDetail['attachment_noti_type']==1){
									$toNotiArray[$gUDetail['user_id']] = $gUDetail['email'];		
								}else{
									$toLinkArray[$gUDetail['user_id']] = $gUDetail['email'];	
								}
								$toEmailList[] = $gUDetail['email'];
							}
						}elseif(!empty($to)){
							$mail->AddBCC($to,''); // To
							//$mail->AddAddress($to,''); // To
							$toExtraAddress.= ($toExtraAddress=='')?$to:', '.$to;
							$toEmailList[] = $to;							
							
							$sql = "SELECT ab.full_name as name, user_email as email, ab.attachment_noti_type FROM pmb_address_book as ab where user_email IN ('".$to."') UNION	SELECT iss.company_name as name, iss.issue_to_email as email, iss.attachment_noti_type FROM inspection_issue_to as iss WHERE issue_to_email IN ('".$to."')";
							$result = mysql_query($sql);
							$row = mysql_fetch_row($result);
							#print_r($row);die;
							if($row[3]==1){
								$toNotiArray[$gUDetail[2]] = $gUDetail[2];		
							}else{
								$toLinkArray[$gUDetail[2]] = $gUDetail[2];	
							}
						}
					}
				}
				
				$attahment1 = $_SESSION[$_SESSION['idp'].'_emailfile'];
				$pmbAttachment = $_SESSION[$_SESSION['idp'].'_pmbEmailfile'];
				#print_r($pmbAttachment);
				#print_r($attahment1); die;
				#echo $messageType;die;
				if($messageType != "Document Transmittal"){
					#File attachment
					if(isset($attahment1) && !empty($attahment1)){						
						foreach($attahment1 as $key=>$value){
							$ext = end(explode('.', $value));
							if($_SESSION[$_SESSION['idp'].'_orignalFileName'][$key] == "Document Transmittal.pdf"){
								//$ext = end(explode('.', $files));
								if($ext == 'zip' || $ext == 'exe'){

								}else{
									$mail->AddAttachment("attachment/".$value, $_SESSION[$_SESSION['idp'].'_orignalFileName'][$key]);
								}
							}else{
								if($ext == 'zip' || $ext == 'exe'){

								}else{
									$mail->AddAttachment("attachment/".$value, $_SESSION[$_SESSION['idp'].'_orignalFileName'][$key].".".$ext);
									}
							}					
						}						
					}
					#PMB Attachment
					if(isset($pmbAttachment) && !empty($pmbAttachment)) {
						foreach($pmbAttachment as $key => $val) {
							$mail->AddAttachment("attachment/". $val);
						}
					}
				}
				if(get_magic_quotes_gpc()) {
					$messageDetails = stripslashes($messageDetails);
				}
			
				$messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));
				if($external_email != 1){
					$recipTo = !empty($_POST['recipTo'])?$_POST['recipTo']:array(0);
				}
				if(isset($recipTo)){
					if($_POST['composeId'] != 0 && isset($_POST['save'])){
						$messgeId = 0;//Forcfully create new message and delete old one due to draft issue.
					}
					foreach($recipTo as $to){ 
						if(is_numeric($to)){
							$messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageTypeKey, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave, "testt", $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
							if(!empty($messageBoard))
								$messgeId = $messageBoard['messgeId'];
						}else{
							$messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageTypeKey, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave, "testt", $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
							if(!empty($messageBoard))
								$messgeId = $messageBoard['messgeId'];
						}
						$attahment1 = '';
					}
				}	
				
				//Added for display message for cc sections Start Here
				if(isset($ccUsserIDArr)){
					foreach($ccUsserIDArr as $ccInsertEmail=>$ccInsertId){
						$messageBoard = $object->messageBoard($projectId, $from, $ccInsertId, $subject, $messageTypeKey, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 1, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave, "testt", $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
						$messgeId = $messageBoard['messgeId'];
					}
				}
				
				//for adding file of email section		
				#print_r($_REQUEST);
				if(!empty($_REQUEST['filesArr'])){
					$filesArray = explode(',',$_REQUEST['filesArr']);
					foreach($filesArray as $files){
						$ext = end(explode('.', $files));
						if($ext == 'zip' || $ext == 'exe'){

						}else{
							$mail->AddAttachment("pmb_attachment/".$_SESSION['idp'].'/'.$files, $files);
						}
					}	
				}
				#print_r($mail);die;
	
				//Added for display message for cc sections End Here
				if($messageType == "Document Transmittal"){
					$attahment1 = $_SESSION[$_SESSION['idp'].'_emailfile'];
					if(isset($attahment1) && !empty($attahment1)){
						foreach($attahment1 as $key=>$value){
							$ext = end(explode('.', $value));
							if($_SESSION[$_SESSION['idp'].'_orignalFileName'][$key] == "Document Transmittal.pdf"){
								if($ext == 'zip' || $ext == 'exe'){

								}else{
									$mail->AddAttachment("attachment/".$value, $_SESSION[$_SESSION['idp'].'_orignalFileName'][$key]);
								}
							}else{
								if($ext == 'zip' || $ext == 'exe'){

								}else{
									$mail->AddAttachment("attachment/".$value, $_SESSION[$_SESSION['idp'].'_orignalFileName'][$key].".".$ext);
								}
							}					
						}
					}	
					$msg .= $msgAttach;
				}
				
				if($messageType=='Variation Claims'){
					$msg = "Hello, <br><br>you have a new variation claim for the ".$projectName[0]['project_name']." project for the amount of  $".number_format($claimed).".";

					#$msg .="<br/>Kind Regards<br/><br/>";
				}
				
				if($messageType=='Progress Claims'){
					$msg = "Hello,<br/>You have a new progress claim for ".$CompanyName[0]['company_name']." for the ".$projectName[0]['project_name']." project for the amount of $".$claimed." dated ".$claimedDate.". For more information please see the full progress claim which is attached to this email.<br/>Thanks.";	
				}
				$msg .= "<br/><br/>click here to access your message.<br>
							<a href='".$path."/pms.php?sect=message_details&id=".base64_encode($messageBoard['thread_id'])."&type=inbox&projID=".$_SESSION['idp']."&byEmail=".$byEmail."' target='_blank'>".$path."/pms.php?sect=message_details&id=".base64_encode($messageBoard['thread_id'])."&type=inbox&projID=".$_SESSION['idp']."&byEmail=".$byEmail."</a>";
				#$msg .= "<br/><br/>Thanks,<br> DefectId customer care";
				
				$msg .= "<br/>Kind Regards.<br/>
					".$CompanyName[0]['user_fullname']."<br>
					".$CompanyName[0]['company_name'];
				
				#if($messageType=='Variation Claims'){
					$msg .= $messageData1[1];	
				#}
				
				$mail->Subject = "WiseWorker:".$messgeId."-".$subject;
				
				if(isset($_POST['save']) && !empty($messageBoard)){
					//$result = $mail->Send();
					#print_r($mail);die;
					foreach($recipTo as $to){
						if(is_numeric($to)){
						$query = "select u.user_id, u.user_type, u.user_fullname as fullname, u.user_email as email from user as u where u.is_deleted=0 and u.user_id='".$to."'";
						$getUserDetails = mysql_query($query);
							while($gUDetail = mysql_fetch_array($getUserDetails)){
								$mail->AddBCC($gUDetail['email'],''); // To
								//$mail->AddAddress($gUDetail['email'],''); // To
								$emailPMB = $gUDetail['email'];
							}
							
						}elseif(!empty($to)){
							$mail->AddBCC($to,''); // To
							//$mail->AddAddress($to,''); // To
							$toExtraAddress.= ($toExtraAddress=='')?$to:', '.$to;
							$emailPMB = $to;
						}
						
						$toCCAddress = empty($toEmailList)?'':'To : '.implode(', ', $toEmailList).'<br>';
						$toCCAddress.= empty($ccEmailList)?'':'Cc : '.implode(', ', $ccEmailList).'<br>';
						$toCCAddress.= '<hr><br><b>'.$mail->Subject.'</b><br><br>';
						$mail->MsgHTML($toCCAddress.$msg);
						if(!empty($imageCompose)){
							// $imgAttach = 'attachment/'.$imageCompose;
							// $mail->addAttachment($imgAttach, 'Attachment');
							$mail->AddAttachment("attachment/".$imageCompose, $imageCompose);
						}
						if(!$mail->Send()){
						 $mail->ErrorInfo;
						 $toId = $_SESSION['ww_builder_id'];
						 $subjectPMB = "Email notification failed";
						 #$messageDetailsPMB = "Your message ".$mail->Subject." was not sent ".$mail->ErrorInfo."";
						  $messageDetailsPMB = "Delivery to the following recipient failed permanently:
		<br><br>
     <a href='mailto:".$emailPMB."'>".$emailPMB."</a><br><br>

Technical details of permanent failure:<br>
".DOMAIN." tried to deliver your message, but it was rejected by the server.<br><br>

The error that the other server returned was:<br>
".$mail->ErrorInfo."";
						 //$messageBoard = $object->messageBoard($projectId, $from, $toId, $subject, $messageType, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 1, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave);
						$messageBoard = $object->messageBoard($projectId, $toId, $toId, $subjectPMB, "General Correspondence", $messageDetailsPMB, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation, $subjectSave, "testt", $dateSent, $dateApproved, $sent, $approved, $claimed, $certified, $invoiced,'','',$imageCompose, $rfi_reference);
						#print_r($messageBoard);
						#		die;
						}else{
							#echo 'Mail sent';	
						}
						//clearing email everytime
						$mail->ClearAddresses();
						$mail->ClearBCCs();
					}
						
					
				}
				$mail->ClearAllRecipients();
				$mail->ClearCustomHeaders();
				
				if(!empty($_POST['filesArr'])){
				$filesArr = explode(',',$_POST['filesArr']);
				if(sizeof($filesArr)>1){
				foreach($filesArr as $filesArrVal){
					 	 $query = "INSERT INTO pmb_attachments SET
									project_id = '".$_SESSION['idp']."',
									name = '".stripcslashes($filesArrVal)."',
									attachment_name = '".stripcslashes($filesArrVal)."',
									message_id = '".$messgeId."',
									last_modified_date = NOW(),
									last_modified_by = '".$_SESSION['ww_builder_id']."',
									created_date = NOW(),
									created_by = '".$_SESSION['ww_builder_id']."',
									is_attached_email = 1"; 
									mysql_query($query);
				}
				}else{
					$query = "INSERT INTO pmb_attachments SET
									project_id = '".$_SESSION['idp']."',
									name = '".stripcslashes($_POST['filesArr'][0])."',
									attachment_name = '".stripcslashes($_POST['filesArr'][0])."',
									message_id = '".$messgeId."',
									last_modified_date = NOW(),
									last_modified_by = '".$_SESSION['ww_builder_id']."',
									created_date = NOW(),
									created_by = '".$_SESSION['ww_builder_id']."',
									is_attached_email = 1"; 
									mysql_query($query);
				}
			}
			
				
//------------------------------------/SINGLE MAILS SENT-----------------------------------------
			}// End Of if...else... Condition For Sending Mail			
			
					
//entry for attachment of eml and msg file
//Date: 15-07-2015
				
			if($_POST['composeId'] != 0 && isset($_POST['save']) && !empty($messageBoard)){//Forcfully create new message and delete old one due to draft issue.
				mysql_query("UPDATE pmb_message SET is_draft = 0, is_deleted = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE message_id = ".$_POST['composeId']);
				mysql_query("UPDATE pmb_user_message SET is_deleted = 1, type = 'delete', last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE message_id = ".$_POST['composeId']);
			}
# Add custom entry in address book
			if(!empty($_POST['customEmailEntry'])){
				if(explode(',', $_POST['customEmailEntry'])){
					$customEmails = explode(', ', $_POST['customEmailEntry']);
				}else{
					$customEmails[] = $_POST['customEmailEntry'];
				}
				foreach($customEmails as $newEmail){
					$name =  explode('@', $newEmail);
					$fullName = trim(addslashes($name[0]));
					$userEmail = trim(addslashes($newEmail));
					
					$inAddressBook = $object->selQRYMultiple('full_name', 'pmb_address_book', 'full_name="'.$fullName.'" AND user_email="'.$userEmail.'" AND project_id="'.$_SESSION['idp'].'"');
					if(!isset($inAddressBook[0]['full_name'])){
						$inssertQRY = "INSERT INTO pmb_address_book SET
							project_id = '".$_SESSION['idp']."',
							full_name = '".$fullName."',
							user_email = '".$userEmail."',
							last_modified_date = NOW(),
							last_modified_by = '".$_SESSION['ww_builder_id']."',
							created_date = NOW(),
							created_by = '".$_SESSION['ww_builder_id']."'";
						mysql_query($inssertQRY);
					}
				}
			}

# Add custom entry in pmb_correspondences_tags
			if(!empty($_POST['customCompanyTag'])){
				if(explode(',', $_POST['customCompanyTag'])){
					$customCompanyTag = explode(', ', $_POST['customCompanyTag']);
				}else{
					$customCompanyTag[] = $_POST['customCompanyTag'];
				}
				foreach($customCompanyTag as $newCompanyTag){
					$cmpTag = trim(addslashes($newCompanyTag));
					
					$getCompanyTag = $object->selQRYMultiple('correspondences_tags', 'pmb_correspondences_tags', 'correspondences_tags="'.$cmpTag.'" AND project_id="'.$_SESSION['idp'].'"');
					if(!isset($getCompanyTag[0]['correspondences_tags'])){
						$inssertQRY = "INSERT INTO pmb_correspondences_tags SET
							project_id = '".$_SESSION['idp']."',
							correspondences_tags = '".$cmpTag."',
							last_modified_date = NOW(),
							last_modified_by = '".$_SESSION['ww_builder_id']."',
							created_date = NOW(),
							created_by = '".$_SESSION['ww_builder_id']."'";
						mysql_query($inssertQRY);
					}
				}
			}

		#echo "0outt";die;
		if($_POST['RFIstatus']!='Draft'){
			header('Location:?sect=sent_box&sm=1'); ?><script language="javascript" type="text/javascript">window.location.href="?sect=sent_box&sm=1";</script><?php
		}else{//echo 'Location:?sect=drafts&sm=1'; 
			header('Location:?sect=drafts&sm=1'); ?><script language="javascript" type="text/javascript">window.location.href="?sect=drafts&sm=1";</script><?php
		}//$_GET['msgid'] = $messgeId;
	}else{
		echo $messageBoard; 
	}
}

//if(sizeof($_GET) == 2){
	unset($_SESSION[$_SESSION['idp'].'_emailfile']);
	unset($_SESSION[$_SESSION['idp'].'_remaimberData']);
	unset($_SESSION[$_SESSION['idp'].'_pmbEmailfile']);
//}

function getattachment($mid){
   $req1 = mysql_query('select attach_id, name, attachment_name from pmb_attachments where message_id="'.$mid.'" and is_deleted=0');
   $row=mysql_fetch_array($req1);
   return $row;
}
?>
