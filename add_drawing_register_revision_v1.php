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
		return array(30, 57, 20, 20, 15, 15, 20, 20);
	}
}
$companyDetailsData = $obj->selQRYMultiple('trading_name, comp_email, comp_mobile, CONCAT_WS(", ", NULLIF(comp_businessadd1, ""), NULLIF(comp_businessadd2, ""), NULLIF(comp_bussuburb, ""), NULLIF(comp_businessstate, ""), NULLIF(comp_businesspost, ""), NULLIF(comp_businesscountry, "")) AS companyAddress, website', 'pms_companies', 'active = 1');
		
if(isset($_REQUEST["antiqueID"])){//Get pdfData Here
	$existingDcTransID = $_REQUEST['existingDcTransID'];
	$emailUserList = unserialize($_REQUEST['emailUserList']);//Array for send emails
	$pdfExData = $obj->selQRYMultiple('title, number, attribute1, attribute2, tag, is_approved', 'drawing_register_module_one', 'is_deleted = 0 AND id = '.$_POST['drawingID'].' AND project_id = '.$_SESSION['idp']);
	$recodArr['title'] = $drawingTitle = $pdfExData[0]['title'];
	$recodArr['number'] = $drawingNumber = $pdfExData[0]['number'];
	$drawingAttribute1 = $recodArr['attribute1'] = $pdfExData[0]['attribute1'];
	$recodArr['attribute2'] = $pdfExData[0]['attribute2'];
	$recodArr['tag'] = $pdfExData[0]['tag'];
	$recodArr['is_approved'] = $pdfExData[0]['is_approved'];

	$recodArr['revision'] = $drawingRevision = trim(addslashes($_POST['drawingRevision']));
	$recodArr['comments'] = $drawingNotes = trim(addslashes($_POST['drawingNotes']));

	$filename = $_FILES['file']['name']; // Drawing File Name
	$file_ext = end(explode('.', $filename));
	
	$drawingAttribute3 = 'PDF';
	if(strtolower($file_ext) != 'pdf')
		$drawingAttribute3 = 'DWG';
	if(strtolower($file_ext) == 'cad')
		$drawingAttribute3 = 'CAD';
	if(strtolower($file_ext) == 'xls' || strtolower($file_ext) == 'XLS')
		$drawingAttribute3 = 'XLS';
	if(strtolower($file_ext) == 'xlsx' || strtolower($file_ext) == 'XLSX')
		$drawingAttribute3 = 'XLSX';
	if(strtolower($file_ext) == 'doc' || strtolower($file_ext) == 'DOC')
		$drawingAttribute3 = 'DOC';
	if(strtolower($file_ext) == 'docx' || strtolower($file_ext) == 'DOCX')
		$drawingAttribute3 = 'DOCX';
	
	$recodArr['attribute3'] = $drawingAttribute3;
//Update DT Section Start Here
	if($pdfExData[0]['attribute1'] != $_POST['drAttr']){
		$qry = "UPDATE drawing_register_module_one SET attribute1 = '".$drawingAttribute1."' WHERE is_document_transmittal = 1 AND id = ".$_POST['documentTransmittalID'];
		mysql_query($qry);
	}
//Update DT Section Start Here
	$recodArr['status'] = $pdfStatus = trim(addslashes($_POST['pdfStatus']));
$projectName = $obj->getDataByKey('projects', 'project_id', $_SESSION['idp'], 'project_name');
	
	$insertQRY = "INSERT INTO drawing_register_revision_module_one SET
			project_id = '".$_SESSION['idp']."',
			drawing_register_id = '".$_POST['drawingID']."',
			revision_number = '".$drawingRevision."',
			comments = '".$drawingNotes."',
			revision_status = '".$pdfStatus."',
			last_modified_date = NOW(),
			last_modified_by = '".$_SESSION['ww_builder_id']."',
			created_date = NOW(),
			created_by = '".$_SESSION['ww_builder_id']."'";
	mysql_query($insertQRY);
	$_SESSION['insertedDwgID'] = $_POST['drawingID'];
	$_SESSION['insertedDwgRevID'] = $imageid = mysql_insert_id();
//Set Array for output data ids
	$outDataArr = array('insertedDwgID' => $_POST['drawingID'], 'insertedDwgRevID' => $imageid);
	
	$imageName = $imageid.'.'.$file_ext;
	if(!is_dir('./project_drawing_register_v1/'))	@mkdir('./project_drawing_register_v1/', 0777);
	if(!is_dir('./project_drawing_register_v1/'.$_SESSION['idp']))	@mkdir('./project_drawing_register_v1/'.$_SESSION['idp'], 0777);

	move_uploaded_file($_FILES['file']['tmp_name'], './project_drawing_register_v1/'.$_SESSION['idp'].'/'.$imageName);
//Add file for email attachment 
	$name = $_SESSION['idp'].'_'.substr(microtime(), -6, -1).rand(0,99).'.'.$file_ext;
	$_SESSION[$_SESSION['idp'].'_orignalFileName'] = $_SESSION[$_SESSION['idp'].'_emailfile'] = array();
	$_SESSION[$_SESSION['idp'].'_emailfile'][] = $name;
	$_SESSION[$_SESSION['idp'].'_orignalFileName'][] = $drawingTitle;
	copy("project_drawing_register_v1/".$_SESSION['idp']."/".$imageName, 'attachment/'.$name);
//Add file for email attachment 
	$updateQRY = "UPDATE drawing_register_module_one SET
				project_id = '".$_SESSION['idp']."',
				pdf_name = '".$imageName."',
				revision = '".$drawingRevision."',
				status = '".$pdfStatus."',
				file_type = '".$drawingAttribute3."',
				comments = '".$drawingNotes."',
				is_approved_edit = 0,
				last_modified_date = NOW(),
				uploaded_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."'
		WHERE
			id = '".$_POST['drawingID']."'";
	mysql_query($updateQRY);
	
	$secUpdateQRY = "UPDATE drawing_register_revision_module_one SET pdf_name = '".$imageName."', last_modified_date = NOW() WHERE id = '".$imageid."'";
	mysql_query($secUpdateQRY);
	$recodArr['pdf_name'] = $imageName;
	
	$insertHistory = "INSERT INTO table_history_details SET
				primary_key = '".$imageid."',
				table_name = 'drawing_register_module_one',
				sql_operation = 'UPDATE',
				sql_query = '".serialize($recodArr)."',
				created_by = '".$_SESSION['ww_builder_id']."',
				created_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				last_modified_date = NOW(),
				project_id = '".$_SESSION['idp']."'";
	mysql_query($insertHistory);	

# Start:- Prepare data for email
	$emailData = serialize(array("docRegId" =>$imageid, "docNumber" =>$drawingNumber, "docTitle" =>$drawingTitle, 'docRevision' =>$drawingRevision, 'attribute1' =>$recodArr['attribute1'], 'attribute2' =>str_replace('###', ', ', $recodArr['attribute2']), 'status' => $pdfStatus)); 		
	
	$instQuery = "INSERT INTO cron_for_pending_emails SET
		primary_key = '".$imageid."',
		foreign_key = '0',
		project_id = '".$_SESSION['idp']."',
		section_name = 'new_document_revision_added',
		data = '".$emailData."',
		is_new_record = '1',
		created_date = NOW(),
		created_by = '".$_SESSION['ww_builder_id']."'";
	mysql_query($instQuery);

# Start:- Prepare data for email

//Report Generation Start Here
/*	$docData = array();
	$attribute1Arr = array('"General"', '"Architectural"', '"Structure"', '"Services"', '"Concrete &amp; PT"', '"Civil / Landscaping"', '"ESD / Green Star"', '"Survey"');

	$sect = ", max(drr.id) AS revID";
	$orderBy = " ORDER BY dr.attribute1, dr.attribute2, dr.number asc";
	$groupBy = " GROUP BY dr.id";

	$docData = $obj->selQRYMultiple('dr.id, dr.number, dr.title, dr.pdf_name, dr.created_date, dr.is_approved, drr.revision_number, drr.created_date AS revisionAdded, dr.attribute1, dr.attribute2, dr.status'.$sect, 'drawing_register_revision_module_one AS drr, drawing_register_module_one AS dr', 'drr.is_deleted = 0 AND dr.is_deleted = 0 AND drr.drawing_register_id = dr.id AND dr.attribute1 IN ('.join(',', $attribute1Arr).') AND dr.is_document_transmittal	 = 0 AND dr.project_id = '.$_SESSION['idp'].' AND dr.project_id = '.$_SESSION['idp'].' '.$groupBy.' '.$orderBy);

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
		$pdf->Cell(10, 10, $projectName);	
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

		$header = array("Document Number", "Document Title", "Document Type", "Date Added", "Status", "Revision No", "Revision Date", "Attribute 2");
		
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
			
			
			$pdf->Row(array(trim($doc['number']), trim($doc['title']), $fileExt, date('d/m/Y', strtotime($doc['created_date'])), $doc['status'], $RevisionNumber, date('d/m/Y', strtotime($doc['revisionAdded'])), str_replace("###", ",",$doc['attribute2'])));	
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
	
	$subject = 'Document '.$drawingTitle.' Added in project '.$projectName;
	$messageType = 'Document';
	
	$messageDetails = 'Hello,<br />
		You have a new Document revision added in '.$projectName.' Project <br />
		Document Number : '.$drawingNumber.'<br />
		Drawing Revision : '.$drawingRevision.'<br/>
		Attribute 1 : '.$recodArr['attribute1'].'<br/>
		Attribute 2 : '.str_replace('###', ', ', $recodArr['attribute2']).'<br/>
		Status : '.$pdfStatus.'<br/><br/><br />
		Document Uploaded By : '.$_SESSION['ww_builder_full_name'].'<br/><br/><br /><br />
		Thanks,<br>Wiseworker customer care';

	if(get_magic_quotes_gpc()){	$messageDetails = stripslashes($messageDetails);	}
	$messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));
	
	$messgeId = 0;
	$getThreadId = $obj->selQRYMultiple('pmb_thread_id', 'drawing_register_module_one', 'is_deleted = 0 AND id = "'.$_POST['drawingID'].'"'); 
	$threadId = isset($getThreadId[0]['pmb_thread_id'])?$getThreadId[0]['pmb_thread_id']:0;
	
	foreach($emailUserList as $key=>$userEmailArrNew){ #print_r($userEmailArrNew);die;
		$messageBoardReturn = $obj->messageBoard($_SESSION['idp'], $_SESSION['ww_builder_id'], $key, $subject, $messageType, $messageDetails, $attahmentMsgb, $messgeId, '', '', '', 0, $threadId);
		$attahmentMsgb = "";
		$messgeId = $messageBoardReturn['messgeId'];
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
		$mail->Subject = "wiseworker:".$messgeId."-".$subject;
		$mail->IsHTML(true);
		$byEmail = 3;
		$msg = 'Hello,<br />
			You have a new Document revision added in '.$projectName.' Project <br />
				Document Number : '.$drawingNumber.'<br />
				Drawing Revision : '.$drawingRevision.'<br/>
				Attribute 1 : '.$recodArr['attribute1'].'<br/>
				Attribute 2 : '.str_replace('###', ', ', $recodArr['attribute2']).'<br/>
				Status : '.$pdfStatus.'<br/><br/><br />
				Document Uploaded By : '.$_SESSION['ww_builder_full_name'].'<br/><br/>
				<br/>Please click below to access your message.<br><a href="'.$path.'/pms.php?sect=message_details&id='.base64_encode($threadId).'&type=inbox&projID='.$_SESSION['idp'].'&byEmail='.$byEmail.'" target="_blank">'.$path.'/pms.php?sect=message_details&id='.base64_encode($threadId).'&type=inbox&projID='.$_SESSION['idp'].'&byEmail='.$byEmail.'</a><br /><br /><br />Thanks,<br><br />';
				
		$msg .= $companyDetailsData[0]['trading_name'].'<br />
					E: '.$companyDetailsData[0]['comp_email'].'<br />
					P: '.$companyDetailsData[0]['comp_mobile'].'<br />
					A: '.$companyDetailsData[0]['companyAddress'].'<br />
					W: '.$companyDetailsData[0]['website'].'<br />';
		$content = file_get_contents('./email_template.php');
		$content = str_replace('domain_name',"Wiseworking" ,$content);
		$content = str_replace('noti_heading',"wiseworker:".$messgeId."-".$subject,$content);
		$content = str_replace('image_content','<img style="border: 0;-ms-interpolation-mode: bicubic;display: block;max-width:388px" alt="" width="259" height="77" src="'.$path.'/images/logo.png"  />',$content);
		$content = str_replace('noti_content',$msg ,$content);
		$msg = $content;
		$mail->MsgHTML($msg);			
		$mail->AddAttachment("attachment/".$name);  
		$mail->AddAddress($userEmailArrNew[0], $userEmailArrNew[1]); // To
		/*$mail->Send();
		$mail->ClearAddresses();
		$mail->ClearAllRecipients();*/
		//$mail->AddAddress($userEmailArrNew['user_email'], $userEmailArrNew['full_name']); // To
/*if(!$mail->Send()) {
	$nogood = true;
	$fstatus = "Mailer Error: " . $mail->ErrorInfo;
	$fstatus .=$userEmailArrNew['user_email'];
	$obj->save_email_status('drawing_register', $userEmailArrNew[0],'', '', $msg, 0, $_SESSION['idp'], 'Document '.$drawingTitle.' Added in project '.$projectName, "attachment/".$name); 
}else {
	$status = "Thank you! Your message has been sent to 20th Century Fox. Submit another?<br>";
	$status .=$userEmailArrNew['user_email'];  
	$obj->save_email_status('drawing_register', $userEmailArrNew[0],'', '', $msg, 1, $_SESSION['idp'], 'Document '.$drawingTitle.' Added in project '.$projectName, "attachment/".$name);
}*//*
			
		$mail->ClearAddresses();
		$mail->ClearAllRecipients();
		//Send Mail End Here
	}
	
	if($threadId != 0){
		$updateQRY = "update drawing_register_module_one SET pmb_thread_id = '".$threadId."'
		WHERE project_id = '".$_SESSION['idp']."' AND id='".$_POST['drawingID']."'";
		mysql_query($updateQRY);
	}*/

	//If Document Transmittal not added so create blank file code Start Here
	if($_REQUEST['documentTransmittalID'] == ""){
		$docTransData = array();
		$docTransData = $obj->selQRYMultiple('id', 'drawing_register_module_one', 'is_document_transmittal = 1 AND is_deleted = 0 AND attribute1 = "'.$drawingAttribute1.'" AND project_id = '.$_SESSION['idp']);
		
		if(empty($docTransData)){
			$inssertQRY = "INSERT INTO drawing_register_module_one SET
				project_id = '".$_SESSION['idp']."',
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
				uploaded_date = NOW(),
				is_document_transmittal = 1,
				created_by = '".$_SESSION['ww_builder_id']."'";
			mysql_query($inssertQRY);
			$pdfRegIDNew = mysql_insert_id(); 
		}else{
			$pdfRegIDNew = $docTransData[0]['id']; 
		}
		#File Upload Section
		#code commented by devd
		// $secondInsertQRY = "INSERT INTO drawing_register_revision_module_one SET
		// 	project_id = '".$_SESSION['idp']."',
		// 	drawing_register_id = '".$pdfRegIDNew."',
		// 	title = '".$drawingTitle."',
		// 	number = '".$drawingNumber."',
		// 	comments = '".$drawingNotes."',
		// 	revision_number = '".$drawingRevision."',
		// 	revision_status = '".$pdfStatus."',
		// 	last_modified_date = NOW(),
		// 	last_modified_by = '".$_SESSION['ww_builder_id']."',
		// 	created_date = NOW(),
		// 	created_by = '".$_SESSION['ww_builder_id']."'";
		// mysql_query($secondInsertQRY);
		// $docTransImageid = mysql_insert_id();
		// $docTransFileName = $docTransImageid.".pdf";
		// copy($tempFile, "project_drawing_register_v1/".$_SESSION['idp']."/".$docTransFileName);//Create File Here

		// $updateQRY = "UPDATE drawing_register_module_one SET pdf_name = '".$docTransFileName."', last_modified_date = NOW() WHERE id = '".$pdfRegIDNew."'";
		// mysql_query($updateQRY);
		// $secUpdateQRY = "UPDATE drawing_register_revision_module_one SET pdf_name = '".$docTransFileName."', last_modified_date = NOW() WHERE id = '".$docTransImageid."'";
		// mysql_query($secUpdateQRY);
		// $recodArr['pdf_name'] = $docTransFileName;
		// print_r($recodArr); die;
		// $insertHistory = "INSERT INTO table_history_details SET
		// 			primary_key = '".$imageid."',
		// 			table_name = 'drawing_register_module_one',
		// 			sql_operation = 'INSERT',
		// 			sql_query = '".serialize($recodArr)."',
		// 			created_by = '".$_SESSION['ww_builder_id']."',
		// 			created_date = NOW(),
		// 			last_modified_by = '".$_SESSION['ww_builder_id']."',
		// 			last_modified_date = NOW(),
		// 			project_id = '".$_SESSION['idp']."'";
		// mysql_query($insertHistory);	
	}
	//If Document Transmittal not added so create blank file code End Here
	
	$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Revision Added Successfully !', 'dataArr'=> $outDataArr);
	echo json_encode($outputArr);
	//Email and Message Board Entry End Here
}
if(isset($_REQUEST["name"])){
	$drawingData = $obj->selQRYMultiple('attribute1, attribute2, document_transmittal_id', 'drawing_register_module_one', 'is_deleted = 0 AND id = '.$_REQUEST["tableID"]);?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Add Document Register Revision</legend>
		<form name="addDrawingForm" id="addDrawingForm" enctype="multipart/form-data">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top" align="left">Drawing / Document File <span class="req">*</span></td>
				<td align="left" width="40%">
					<div class="innerDivDrager" id="innerDivDrager">
						<div class="innerDiv" id="innerDiv1" align="center">Drop File Here</div>
						<br clear="all" />
						<input type="file" name="multiUpload" id="multiUpload" />
					</div><br />
					<lable for="multiUpload" id="errorMultiUpload" generated="true" class="error" style="display:none;margin-top:10px;"><div class="error-edit-profile">The Drawing PDF field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Document&nbsp;Revision&nbsp;<span class="req">*</span></td>
				<td align="left">
					<input type="text" name="drawingRevision" id="drawingRevision" class="input_small" value="" maxlength="5" />
					<lable for="drawingRevision" id="errorDrawingRevision" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing Revision field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Comments / Notes</td>
				<td align="left">
					<textarea name="drawingNotes" id="drawingNotes" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"></textarea>
					<input type="hidden" name="drawingID" id="drawingID" value="<?=$_REQUEST["tableID"];?>" />
					<input type="hidden" name="existingDcTransID" id="existingDcTransID" value="<?=$drawingData[0]["document_transmittal_id"];?>" />
					<input type="hidden" name="drawingattribute1" id="drawingattribute1" value="<?=$drawingData[0]["attribute1"];?>" />
					<input type="hidden" name="drawingattribute2" id="drawingattribute2" value="<?=$drawingData[0]["attribute2"];?>" />
					<input type="hidden" name="validationFlag" id="validationFlag" value="1" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Status&nbsp;<span class="req">*</span></td>
				<td align="left">
					<?php $revStatusArr = array('Tender', 'Issued for Construction', 'For Information');?>
					<select name="pdfStatus" id="pdfStatusDyna" class="select_box" style="margin-left:0px;"  />
						<option value="">Select</option>
						<?php foreach($revStatusArr as $key=>$revStatus){?>
							<option value="<?=$revStatus?>"><?=$revStatus?></option>
						<?php }?>
					</select>
					<lable for="pdfStatus" id="errorPdfStatus" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Status field is required</div></lable>
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
						<input type="submit" name="button" class="green_small" id="buttonFirstSubmit" value="Save" style="float:left;" />
						<input type="hidden" name="validationFlag" id="validationFlag" value="2" />
                        <input type="hidden" name="documentTransmittalID" id="documentTransmittalID" value="" />
						<input type="hidden" name="existingDcTransID" id="existingDcTransID" value="<?=$drawingData[0]["document_transmittal_id"];?>" />
                        <input type="hidden" name="drAttr" id="drAttr" value="" />
					</li>
					<li>
						<!-- <img src="images/doccument_transmittal.png" style="border:none; width:111px;height:43px;" onclick="addNewRegisterDocumentTransmital(< ?=$drawingData[0]["document_transmittal_id"];?>);" /> -->
						<a class="green_small" href="javascript:void(0)" onclick="addNewRegisterDocumentTransmital(<?=$drawingData[0]["document_transmittal_id"];?>);" style="cursor:pointer;"  alt="doccument transmittal" />Doccument Transmittal</a>
					</li>
					<li>
						<!-- <a id="ancor" href="javascript:closePopup(300);">
							<img src="images/back_btn.png" style="border:none; width:111px;" />
						</a> -->
						<a class="green_small" href="javascript:closePopup(300);" style="cursor:pointer;width:111px;" alt="back" />Back</a>
					</li>
				</ul>
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>
