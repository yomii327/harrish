<?php session_start();

$owner_id = $builder_id = $_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
require_once('includes/class.phpmailer.php');
$obj = new COMMAN_Class(); 
require('fpdf/mc_table.php');	
class PDF extends PDF_MC_Table{
	function Footer(){
		$this->SetY(-15);
		$this->SetFont('times','B',10);
		$this->Cell(0, 10, "Wiseworker- Copyright Wiseworking ".date('Y'), 0, 0, 'C');
	}
	
	function header_width(){
		return array(28, 53, 18, 18, 13, 15, 18, 18, 18);
	}
}
#define('ATTCHMENTPATH', '/var/www/constructionid.com/vccc/project_drawing_register_v1/'.$_SESSION['idp']);
#define('ATTCHMENTCOPYPATH', '/var/www/constructionid.com/vccc/project_drawing_register_v1/'.$_SESSION['idp']);

if(!isset($_SESSION['drRequsetCount']))	$_SESSION['drRequsetCount'] = 0;

if(isset($_REQUEST["antiqueID"])){
	$existingDcTransID = $_REQUEST['existingDcTransID'];
	$emailUserList = unserialize($_REQUEST['emailUserList']);//Array for send emails
	$filename = $_FILES['file']['name']; // Drawing File Name
	$recodArr['status'] = $pdfStatus = trim(addslashes($_POST['pdfStatus']));
	$fNameArr = explode('.', $filename);
	$file_ext = array_pop($fNameArr);
	$processFileName = $tempFileName = implode('.', $fNameArr);
	$revisionName = "";
	if(strpos($processFileName, "[") !== false){
		if(strpos($processFileName, "]") !== false){
			$tempArr = explode('[', $processFileName);
			$lastEle = array_pop($tempArr);
			$fileNameTitle = trim(implode('[', $tempArr));
			$revisionNameArr = explode(']', $lastEle);
			$revisionName = $revisionNameArr[0];
		}
	}
	if(strpos($processFileName, "(") !== false){
		if(strpos($processFileName, ")") !== false){
			$tempArr = explode('(', $processFileName);
			$lastEle = array_pop($tempArr);
			$fileNameTitle = trim(implode('(', $tempArr));
			$revisionNameArr = explode(')', $lastEle);
			$revisionName = $revisionNameArr[0];
		}
	}
	if(strpos($processFileName, "{") !== false){
		if(strpos($processFileName, "}") !== false){
			$tempArr = explode('{', $processFileName);
			$lastEle = array_pop($tempArr);
			$fileNameTitle = trim(implode('{', $tempArr));
			$revisionNameArr = explode('}', $lastEle);
			$revisionName = $revisionNameArr[0];
		}
	}
	if($revisionName == ""){
		if(strpos($processFileName, "_") !== false){
			$tempArr = explode('_', $processFileName);
			$lastEle = array_pop($tempArr);
			$fileNameTitle = trim(implode('_', $tempArr));
			$revisionName = $lastEle ;	
		}else{
			$tempArr = explode('-', $processFileName);
			$lastEle = array_pop($tempArr);
			$fileNameTitle = trim(implode('-', $tempArr));
			$revisionName = $lastEle ;	
		}
	}
	
	$drawingTitle = $drawingNumber = $fileNameTitle;
	
	$fetchKey = $fileNameTitle.$revisionName;
	if(isset($_POST['isRemoved'][$fetchKey]) && !empty($_POST['isRemoved'][$fetchKey]) && $_POST['isRemoved'][$fetchKey]==1){
		$_SESSION['drRequsetCount']++;
		$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Delete Record Skipped Successfully !', 'msg'=> 'Drawing Registration Delete Record Skipped Successfully !');			
		echo json_encode($outputArr);
		return true;
	}		
//
	$existDrawingArr = json_decode($_POST['mappingDocumentArr'], true);
	
	if(isset($_POST['drawingattribute2js'][$fetchKey]) && !empty($_POST['drawingattribute2js'][$fetchKey]))
		$recodArr['attribute2'] = $drawingattribute2 = trim($_POST['drawingattribute2js'][$fetchKey]);

	if(isset($_POST['revisionNo'][$fetchKey]) && !empty($_POST['revisionNo'][$fetchKey]))
		$revisionName = trim($_POST['revisionNo'][$fetchKey]);

	if(isset($_POST['nameTitle'][$fetchKey]) && !empty($_POST['nameTitle'][$fetchKey]))
		$drawingNumber = trim($_POST['nameTitle'][$fetchKey]);		
	
	if(isset($_POST['description'][$fetchKey]) && !empty($_POST['description'][$fetchKey]))
		$drawingTitle = trim($_POST['description'][$fetchKey]);		
	
	$recodArr['attribute1'] = $drawingAttribute1 = trim(addslashes($_POST['drawingattribute1']));

	$drawingRevision = $revisionName;
//Update DT Section Start Here
	if($drawingAttribute1 != $_POST['drAttr']){
		$qry = "UPDATE drawing_register_module_one SET attribute1 = '".$drawingAttribute1."' WHERE is_document_transmittal = 1 AND id = ".$_POST['documentTransmittalID'];
		mysql_query($qry);
	}
//Update DT Section Start Here

#echo $fetchKey;print_r($_POST);print_r($existDrawingArr);die;
//If Document Transmittal not added so create blank file code Start Here
if(isset($_REQUEST['documentTransmittalID'])){
	if($existingDcTransID == ""){
		$inssertQRY = "INSERT INTO drawing_register_module_one SET
			project_id = '".$_SESSION['idp']."',
			title = '".$drawingTitle."',
			number = '".$drawingNumber."',
			revision = '".$drawingRevision."',
			comments = '".$drawingNotes."',
			attribute1 = '".$drawingAttribute1."',
			attribute2 = '".$drawingAttribute2."',
			file_type = '".$drawingAttribute3."',
			tag = '".$tag."',
			is_approved = '".$approved."',
			status = '".$pdfStatus."',
			last_modified_date = NOW(),
			last_modified_by = '".$_SESSION['ww_builder_id']."',
			created_date = NOW(),
			is_document_transmittal = 1,
			created_by = '".$_SESSION['ww_builder_id']."'";
		mysql_query($inssertQRY);
		$pdfRegID = mysql_insert_id(); 
	}else{
		$pdfRegID = $existingDcTransID; 
	}
#File Upload Section
	$secondInsertQRY = "INSERT INTO drawing_register_revision_module_one SET
		project_id = '".$_SESSION['idp']."',
		drawing_register_id = '".$pdfRegID."',
		title = '".$drawingTitle."',
		number = '".$drawingNumber."',
		comments = '".$drawingNotes."',
		revision_number = '".$drawingRevision."',
		revision_status = '".$pdfStatus."',
		last_modified_date = NOW(),
		last_modified_by = '".$_SESSION['ww_builder_id']."',
		created_date = NOW(),
		created_by = '".$_SESSION['ww_builder_id']."'";
	mysql_query($secondInsertQRY);
	$imageid = mysql_insert_id();
	
//	$imageName = $imageid.'.'.$file_ext;
	if(!is_dir('./project_drawing_register_v1/'))	@mkdir('./project_drawing_register_v1/', 0777);
	if(!is_dir('./project_drawing_register_v1/'.$_SESSION['idp']))	@mkdir('./project_drawing_register_v1/'.$_SESSION['idp'], 0777);
	$toArr = array();
	foreach($emailUserList as $key=>$userEmailArrNew){
		$toArr[] = $userEmailArrNew[0];
	}
	
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTopMargin(20);
	//Comapany Logo Section
	$pdf->Image('./company_logo/logo.png', 125, 5, 'png', -100);
	$pdf->Ln(8);
	
	//To List Section
	$pdf->SetFont('times', 'B', 12);
	$pdf->MultiCell(0, 5, 'To: '.implode(", ", $toArr));		
	
	//CC List Section
	$pdf->SetFont('times', 'B', 12);
	$pdf->MultiCell(0, 5, 'CC: ');		
	
	//Subject Line Section
	$pdf->Ln(10);
	$pdf->SetFont('times', '', 12);
	$pdf->MultiCell(0, 10, 'Subject: '.$projectName.'			Document Transmittal: '.$drawingNumber);	
	$pdf->Line($pdf->GetX()-2, $pdf->GetY(), $pdf->GetX()+195, $pdf->GetY());
	
	//Message Section Start Here
	//Date Section
	$pdf->SetFont('times', '', 10);
	$pdf->MultiCell(0, 10, 'Date: '.date('d/m/Y'));	
	$pdf->Ln(5);
	
	//Message Section 
	$pdf->MultiCell(0, 5, 'Please find attached the following documents:');	
	$pdf->MultiCell(0, 5, $drawingTitle);	
	$pdf->Ln(20);
	
	//Footer Section 
	$pdf->MultiCell(0, 5, 'Kind Regards,');	
	$pdf->MultiCell(0, 5, $_SESSION['ww_builder']['company_name']);	
	//Message Section End oHere
	$path = "project_drawing_register_v1/".$_SESSION['idp']."/".$imageid.".pdf";
	//Title String Header
	$pdf->Output($path);
	$fileName = $imageid.".pdf";
#File Upload Section
	$updateQRY = "UPDATE drawing_register_module_one SET
					title = '".$drawingTitle."',
					number = '".$drawingNumber."',
					pdf_name = '".$fileName."', last_modified_date = NOW()
				WHERE id = '".$pdfRegID."'";
	mysql_query($updateQRY);
	$secUpdateQRY = "UPDATE drawing_register_revision_module_one SET
					pdf_name = '".$fileName."', last_modified_date = NOW()
				WHERE id = '".$imageid."'";
	mysql_query($secUpdateQRY);
	
	$recodArr['pdf_name'] = $fileName;
	$insertHistory = "INSERT INTO table_history_details SET
				primary_key = '".$imageid."',
				table_name = 'drawing_register_module_one',
				sql_operation = 'INSERT',
				sql_query = '".serialize($recodArr)."',
				created_by = '".$_SESSION['ww_builder_id']."',
				created_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				last_modified_date = NOW(),
				project_id = '".$_SESSION['idp']."'";

	mysql_query($insertHistory);	
}
//If Document Transmittal not added so create blank file code End Here

	if(array_key_exists($fetchKey, $existDrawingArr)){
		$exDrawingArr = explode('####', $existDrawingArr[$fetchKey]);
		$exDrawingID = $exDrawingArr[0];
		$exDrawingRevIDArr = explode(".", $exDrawingArr[1]);
		$exDrawingRevID = $exDrawingRevIDArr[0];
		
		if(!is_dir('./project_drawing_register_v1/'))	@mkdir('./project_drawing_register_v1/', 0777);
		if(!is_dir('./project_drawing_register_v1/'.$_SESSION['idp']))	@mkdir('./project_drawing_register_v1/'.$_SESSION['idp'], 0777);
		$imageName = $exDrawingRevID.'.'.$file_ext;

		move_uploaded_file($_FILES['file']['tmp_name'], "project_drawing_register_v1/".$_SESSION['idp']."/".$imageName);

		$updateQRY = "UPDATE drawing_register_module_one SET
						dwg_name = '".$imageName."'
					WHERE id = '".$exDrawingID."'";
		mysql_query($updateQRY);

		$secUpdateQRY = "UPDATE drawing_register_revision_module_one SET
						dwg_name = '".$imageName."'
					WHERE id = '".$exDrawingRevID."'";
		mysql_query($secUpdateQRY);

		$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Added Successfully !', 'requestComplete'=> 'dwg');
#		echo json_encode($outputArr);
	//Set session for uploaded file start Here
		$_SESSION['drRequsetCount']++;
		$_SESSION['drDataArr'] = array();
		$_SESSION['drDataArr'][] = $drawingNumber;
		$_SESSION['drDataArr'][] = $drawingTitle;
		$_SESSION['drDataArr'][] = $drawingRevision;
		$_SESSION['drDataArr'][] = $drawingAttribute1;
		$_SESSION['drDataArr'][] = $drawingattribute2;
		$_SESSION['drDataArr'][] = $exDrawingID;
		$_SESSION['finalDrDataArr'][] = $_SESSION['drDataArr'];
	//Set session for uploaded file end Here
		$recodArr['pdf_name'] = $imageName;
	}

	$recodArr['title'] = $drawingTitle;
	$recodArr['number'] = $drawingNumber;
	$recodArr['revision'] = $revisionName;
	$recodArr['comments'] = '';
	$recodArr['status'] = '';
	$recodArr['tag'] = '';
	$recodArr['is_approved'] =  '';
	
	$insertHistory = "INSERT INTO table_history_details SET
					primary_key = '".$exDrawingRevID."',
					table_name = 'drawing_register_module_one',
					sql_operation = 'INSERT',
					sql_query = '".serialize($recodArr)."',
					created_by = '".$_SESSION['ww_builder_id']."',
					created_date = NOW(),
					last_modified_by = '".$_SESSION['ww_builder_id']."',
					last_modified_date = NOW(),
					project_id = '".$_SESSION['idp']."'";
	mysql_query($insertHistory);	

	$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Added Successfully !', 'msg'=> 'Drawing Registration Added Successfully !');	
#echo $_SESSION['drRequsetCount'].'+++++++'.$_REQUEST['totalRequestCount'];
//Send message here
	if($_SESSION['drRequsetCount'] == $_REQUEST['totalRequestCount']){
//Report Generation Start Here
	$docData = array();
	$attribute1Arr = array('"General"', '"Architectural"', '"Structure"', '"Services"', '"Concrete &amp; PT"', '"Civil / Landscaping"', '"ESD / Green Star"', '"Survey"');

	$sect = ", max(drr.id) AS revID";
	$orderBy = " ORDER BY dr.attribute1, dr.attribute2, dr.title asc";
	$groupBy = " GROUP BY dr.id";

	$userNameData = $obj->selQRYMultiple('user_id, user_fullname, company_name', 'user', 'is_deleted = 0');
	$userNameArr = array();
	foreach($userNameData as $uNameData){
		$userNameArr[$uNameData['user_id']] = $uNameData['user_fullname'];
	}

	$docData = $obj->selQRYMultiple('dr.id, dr.number, dr.title, dr.pdf_name, dr.created_date, dr.is_approved, drr.revision_number, drr.created_date AS revisionAdded, dr.attribute1, dr.attribute2, dr.status, drr.last_modified_by'.$sect, 'drawing_register_revision_module_one AS drr, drawing_register_module_one AS dr', 'drr.is_deleted = 0 AND dr.is_deleted = 0 AND drr.drawing_register_id = dr.id AND dr.attribute1 IN ('.join(',', $attribute1Arr).') AND dr.is_document_transmittal	= 0 AND dr.project_id = '.$_SESSION['idp'].' AND dr.project_id = '.$_SESSION['idp'].' '.$groupBy.' '.$orderBy);

	$drIDArr = array();
	foreach($docData as $dData){
		$drIDArr[] = $dData['revID'];
	}
	$revisionNumberData = $obj->selQRYMultiple('id, revision_number, drawing_register_id', 'drawing_register_revision_module_one', 'id IN ('.join(',', $drIDArr).')');
	$curretnVerArr = array();
	foreach($revisionNumberData as $revData){
		$curretnVerArr[$revData['id']] = $revData['revision_number'];
	}
	
	$noInspection = 0;

	if(is_array($docData)){
		$noInspection = sizeof($docData);
	}
	if($noInspection > 0){
		$totalPage = 01;//Attachment images Count +1;
		
		$pdf = new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		$pdf->SetTopMargin(20);
	
		$pdf->Image('./company_logo/logo.png', 125, 5, 'png', -100);
		$pdf->Ln(8);
		
		$pdf->SetFont('times', 'BU', 12);
		$pdf->Cell(40, 10, 'Document Register Report');		
		$pdf->Ln(6);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(26, 10, 'Project Name: ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(10, 10, $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name'));	
		$pdf->Ln(5);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(11, 10, 'Date: ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(20, 10, date('d/m/Y'));	
		$pdf->Ln(5);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(27, 10, 'Total Document: ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(20, 10, $noInspection);	
		$pdf->Ln(5);	
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(11, 10, 'Page: ');		
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(8, 10, '1 of '.'{nb}');		
		$pdf->Ln(10);

		$header = array("Document Number", "Document Title", "Document Type", "Date Added", "Status", "Revision No", "Revision Date", "Attribute 2", "Last Modified By");
		
		$w = $pdf->header_width();
		$pdf->SetWidths($w);		
		$best_height = 17;
//		$pdf->row($header, $best_height);

	//	$pdf->SetFont('times', '', 9);
		
		$oldAttribute1='';
		foreach ($docData as $doc){
			if(empty($oldAttribute1) && !empty($doc['attribute1'])){
				$oldAttribute1 = $doc['attribute1'];
				$pdf->SetFont('times', 'B', 10);
				$pdf->Cell(11, 10, $doc['attribute1']);	
				$pdf->Ln(10);
				$pdf->row($header, $best_height);
		
				$pdf->SetFont('times', '', 9);
				
			}else if($oldAttribute1 != $doc['attribute1']){
				$pdf->AddPage();
				$oldAttribute1 = $doc['attribute1'];
				$pdf->Ln(5);
				$pdf->SetFont('times', 'B', 10);
				$pdf->Cell(11, 10, $doc['attribute1']);	
				$pdf->Ln(10);
				$pdf->row($header, $best_height);
		
				$pdf->SetFont('times', '', 9);
			}
			
			
			$pdfExtension = end(explode('.', $doc['pdf_name']));
			$fileExt = 'PDF File';
			if(strtolower($pdfExtension) != 'pdf'){
				$fileExt = 'Drawing File';
			}
			$status = 'Not Approved';
			if($doc['is_approved'] == 1){
				$status = 'Approved';
			}

			$RevisionNumber = "";
			if($_GET["revisionType"] == 'complete'){
				$RevisionNumber = $doc['revision_number'];
			}else{
				$RevisionNumber = $curretnVerArr[$doc['revID']];
			}
			
			
			$pdf->Row(array(trim($doc['number']), trim($doc['title']), $fileExt, date('d/m/Y', strtotime($doc['created_date'])), $doc['status'], $RevisionNumber, date('d/m/Y', strtotime($doc['revisionAdded'])), str_replace("###", ",",$doc['attribute2']), $userNameArr[$doc['last_modified_by']]));	
		}
//Title String Header
		$file_name = 'manager_drawing_report_'.microtime().'.pdf';
		$d = "./report_pdf/".$owner_id;
		if(!is_dir($d))
			mkdir($d);
		
		if (file_exists($d.'/'.$file_name))
			unlink($d.'/'.$file_name);
		
		$tempFile = $d.'/'.$file_name;
		
		$pdf->Output($tempFile);
			
//Add file for email attachment 
	$name = $_SESSION['idp'].'_'.substr(microtime(), -6, -1).rand(0,99).'.pdf';
	$_SESSION[$_SESSION['idp'].'_orignalFileName'] = $_SESSION[$_SESSION['idp'].'_emailfile'] = array();
	$_SESSION[$_SESSION['idp'].'_emailfile'][] = $name;
	$_SESSION[$_SESSION['idp'].'_orignalFileName'][] = $file_name;
	copy($tempFile, 'attachment/'.$name);
	$attahmentMsgb = $_SESSION[$_SESSION['idp'].'_emailfile']; 
//Add file for email attachment 
	}
//Report Generation End Here
	
	//Email and Message Board Entry Start Here
		$companyDetailsData = $obj->selQRYMultiple('trading_name, comp_email, comp_mobile, comp_businessadd1, comp_businessadd2, comp_bussuburb, comp_businessstate, comp_businesscountry, website', 'pms_companies', 'active = 1');
		
		$subject = 'Document added in project '.$projectName;
		$messageType = 'Document';
	
		$messageDetails = 'Hello,<br />
			You have added '.sizeof($_SESSION['finalDrDataArr']).' new documents in '.$projectName.' Project <br />';
		foreach($_SESSION['finalDrDataArr'] as $drawingDat){
			$messageDetails .= 'Document Number : '.$drawingDat[0].'<br />
				Document Description : '.$drawingDat[1].'<br />
				Drawing Revision : '.$drawingDat[2].'<br/>
				Attribute 1 : '.$drawingDat[3].'<br/>
				Attribute 2 : '.str_replace('###', ', ', $drawingDat[4]).'<br/>
				Status : '.$pdfStatus.'<br/><br/>';
		}
		$messageDetails .= 'Document Uploaded By : '.$_SESSION['ww_builder_full_name'].'<br/><br/><br /><br/>
				Thanks,<br>Wiseworker customer care';	
	
		if(get_magic_quotes_gpc()){	$messageDetails = stripslashes($messageDetails);	}
		$messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));

		foreach($_SESSION['finalDrDataArr'] as $drawingDat){
			$subject = 'Document '.$drawingDat[1].' added in project '.$projectName;
			$messgeId = 0;	$threadId = 0;
			$pmbMessageDetails = 'Hello,<br />You have new document added in '.$projectName.' Project <br />';
			$pmbMessageDetails .= 'Document Number : '.$drawingDat[0].'<br />
					Drawing Revision : '.$drawingDat[2].'<br/>
					Attribute 1 : '.$drawingDat[3].'<br/>
					Attribute 2 : '.str_replace('###', ', ', $drawingDat[4]).'<br/>
					Status : '.$pdfStatus.'<br/><br/>';
			$pmbMessageDetails .= 'Document Uploaded By : '.$_SESSION['ww_builder_full_name'].'<br/><br/><br /><br/>
					Thanks,<br>Wiseworker customer care';	
		
			if(get_magic_quotes_gpc()){	$pmbMessageDetails = stripslashes($pmbMessageDetails);	}
			$pmbMessageDetails = mysql_real_escape_string(nl2br(htmlentities($pmbMessageDetails, ENT_QUOTES, 'UTF-8')));			
			
			foreach($emailUserList as $key=>$userEmailArrNew){
				$attahmentMsgb = "";
				$messageBoardReturn = $obj->messageBoard($_SESSION['idp'], $_SESSION['ww_builder_id'], $key, $subject, $messageType, $pmbMessageDetails, $attahmentMsgb, $messgeId);
				$messgeId = $messageBoardReturn['messgeId'];
				$threadId = $messageBoardReturn['thread_id'];
			}
			if($threadId!=0){
				$updateQRY = "update drawing_register_module_one SET pmb_thread_id = '".$threadId."'
				WHERE project_id = '".$_SESSION['idp']."' AND id='".$drawingDat[5]."'";
				mysql_query($updateQRY);
			}
		}

		//Send Mail Start Here
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

			$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
		
			$mail->SetFrom('WiseworkingSystems@wiseworking.com.au', "Wiseworking System");		
			//$mail->Subject = $subject;
			$mail->Subject = "WiseWorker:".$messgeId."-".$subject;
			$mail->IsHTML(true);
			$byEmail = 3;

			$msg = 'Hello,<br />
				You have '.sizeof($_SESSION['finalDrDataArr']).' new Document (s) added in '.$projectName.' Project <br />';
			foreach($_SESSION['finalDrDataArr'] as $drawingDat){
				$msg .= 'Document Number : '.$drawingDat[0].'<br />
					Drawing Revision : '.$drawingDat[2].'<br/>
					Attribute 1 : '.$drawingDat[3].'<br/>
					Attribute 2 : '.str_replace('###', ', ', $drawingDat[4]).'<br/>
					Status : '.$pdfStatus.'<br/><br/>';
			}
			$msg .= 'Document Uploaded By : '.$_SESSION['ww_builder_full_name'].'<br/><br/><br /><br/>
			<br/>Please click below to access your message.<br><a href="'.$path.'/pms.php?sect=message_details&id='.base64_encode($threadId).'&type=inbox&projID='.$_SESSION['idp'].'&byEmail='.$byEmail.'" target="_blank">'.$path.'/pms.php?sect=message_details&id='.base64_encode($threadId).'&type=inbox&projID='.$_SESSION['idp'].'&byEmail='.$byEmail.'</a><br /><br /><br />Thanks,<br><br />';
					
			$msg .= $companyDetailsData[0]['trading_name'].'<br />
					E: '.$companyDetailsData[0]['comp_email'].'<br />
					P: '.$companyDetailsData[0]['comp_mobile'].'<br />
					A: '.$companyDetailsData[0]['comp_businessadd1'].', '.$companyDetailsData[0]['comp_businessadd2'].', '.$companyDetailsData[0]['comp_bussuburb'].', '.$companyDetailsData[0]['comp_businessstate'].', '.$companyDetailsData[0]['comp_businesscountry'].'<br />
					W: '.$companyDetailsData[0]['website'].'<br />';

			$mail->MsgHTML($msg);			
			$mail->AddAttachment("attachment/".$name);  
		foreach($emailUserList as $key=>$userEmailArrNew){
			$mail->AddAddress($userEmailArrNew[0], $userEmailArrNew[1]); // To
/*if(!$mail->Send()){
	$obj->save_email_status('drawing_register', $userEmailArrNew[0],'', '', $msg, 0, $_SESSION['idp'], "WiseWorker:".$messgeId."-".$subject, "attachment/".$name); 
} else {
	$obj->save_email_status('drawing_register', $userEmailArrNew[0],'', '', $msg, 1, $_SESSION['idp'], "WiseWorker:".$messgeId."-".$subject, "attachment/".$name);
}*/
			$mail->ClearAddresses();
			$mail->ClearAllRecipients( );
		}
			/*$mail->Send();
			$mail->ClearAddresses();
			$mail->ClearAllRecipients( );*/
	//Send Mail End Here


	if($threadId!=0){
	 	$updateQRY = "update drawing_register_module_one SET pmb_thread_id = '".$threadId."'
		WHERE project_id = '".$_SESSION['idp']."' AND id='".$pdfRegID."'";
		mysql_query($updateQRY);
	}		
	//Email and Message Board Entry End Here
		unset($_SESSION['drRequsetCount']);
		unset($_SESSION['finalDrDataArr']);
		echo json_encode($outputArr);
	}
	#echo json_encode($outputArr);
//Send message here
}
if(isset($_REQUEST["name"])){?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Bulk Upload Drawing Files</legend>
		<form name="addDrawingForm" id="addDrawingForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top"  colspan="2" align="left">Drawing&nbsp;Files&nbsp;<span class="req">*</span></td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="innerDiv" id="innerDiv" style="height:300px;width:830px;overflow:auto;">
						<div align="center" style="font-size:12px;">Drop DWG Files Here</div>
					</div>
					<input type="file" name="multiUpload" id="multiUpload" multiple style="display:none;" />
					<lable for="multiUpload" id="errorMultiUpload" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing PDF field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Attribute 1 <span class="req">*</span></td>
				<td align="left">
					<select name="drawingattribute1" id="drawingattribute1" class="select_box" style="margin-left:0px;"  />
				<?php $attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Civil / Landscaping', 'ESD / Green Star', 'Survey');
						if($_SESSION['userRole'] == 'Architect'){
							$attribute1Arr = array('Architectural');
						}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Structural') || ($_SESSION['userRole'] == 'Structural Engineer')){
							$attribute1Arr = array('Structure');
						}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Services') || ($_SESSION['userRole'] == 'Services Engineer')){
							$attribute1Arr = array('Services');
						}
						if($_SESSION['userRole'] == 'Lighting Consultant')	$attribute1Arr = array('Lighting');
						if($_SESSION['userRole'] == 'Tenancy Fitout')	$attribute1Arr = array('Tenancy Fitout');
						if($_SESSION['userRole'] == 'Penthouse Architecture')	$attribute1Arr = array('Penthouse Architecture');
						if($_SESSION['userRole'] == 'Landscaping')	$attribute1Arr = array('Landscaping');
						
						for($i=0;$i<sizeof($attribute1Arr);$i++){?>
							<option value="<?=$attribute1Arr[$i]?>" <? if($attribute1Arr[$i] == 'General')echo 'selected="selected"';?> ><?=$attribute1Arr[$i]?></option>
						<?php }?>
					</select>
				</td>
			</tr>
			<tr>
				<td valign="top" colspan="2">
					<a href="javascript:selectUserNotification('emailUserList');">Select users to send notifications to</a>
					<input type="hidden" name="emailUserList" id="emailUserList" value='' />
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
					<ul class="buttonHolder">
						<li>
							<input type="submit" name="button" class="submit_btn" id="buttonFirstSubmit" style="background-image:url(images/upload_btn.png);font-size:0px; border:none; width:111px;float:left;" />
							<input type="hidden" name="validationFlag" id="validationFlag" value="2" />
                            <input type="hidden" name="documentTransmittalID" id="documentTransmittalID" value="" />
							<input type="hidden" name="existingDcTransID" id="existingDcTransID" value="<?=$drawingData[0]["document_transmittal_id"];?>" />
                            <input type="hidden" name="drAttr" id="drAttr" value="" />
						</li>
						<li>
							<img src="images/doccument_transmittal.png" style="border:none; width:111px;height:43px;" onclick="addNewRegisterDocumentTransmital();" />
						</li>
						<li>
							<a id="ancor" href="javascript:closePopup(300);">
								<img src="images/back_btn.png" style="border:none; width:111px;" />
							</a>
						</li>
					</ul>
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>
