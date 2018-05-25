<?php session_start();

$builder_id=$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
require_once('includes/class.phpmailer.php');
$obj = new COMMAN_Class(); 

if(isset($_REQUEST["antiqueID"])){
	$recodArr['title'] = $drawingTitle = trim(addslashes($_POST['drawingTitle']));
	$recodArr['number'] = $drawingNumber = trim(addslashes($_POST['drawingNumber']));
	$recodArr['revision'] = $drawingRevision = trim(addslashes($_POST['drawingRevision']));
	$recodArr['comments'] = $drawingNotes = trim(addslashes($_POST['drawingNotes']));
	$recodArr['attribute1'] = $drawingAttribute1 = trim(addslashes($_POST['drawingattribute1']));
	#$recodArr['attribute2'] = $drawingAttribute2 = trim(addslashes($_POST['drawingattribute2']));
	$recodArr['attribute2'] = $drawingAttribute2 = trim(addslashes($_POST['drawingattribute2Multi']));
	$recodArr['un_approve_reson'] = $unApproveReson = trim(addslashes($_REQUEST['unApproveReson']));

	$recodArr['status'] = $pdfStatus = trim(addslashes($_POST['pdfStatus']));
	
	$tag = trim(addslashes($_POST['tag']));
	$tag = trim($tag, ";");
	$tag = implode(";", array_map('trim', explode(";", $tag)));
	if($tag != ""){	$tag = trim($tag).";";	}
	$recodArr['tag'] = $tag;
	$approved = 0;
	$approvedEdit = 0;
	if(isset($_POST['approved'])){	$approved = 1;	}
	if(isset($_POST['approved_edit'])){ $approvedEdit = $_POST['approved_edit']; }
	$recodArr['is_approved'] = $approved;
	$recodArr['is_approved_edit'] = $approvedEdit;

	$updateQRY = "UPDATE drawing_register SET
						title = '".$drawingTitle."',
						number = '".$drawingNumber."',
						comments = '".$drawingNotes."',
						revision = '".$drawingRevision."',
						attribute1 = '".$drawingAttribute1."',
						attribute2 = '".$drawingAttribute2."',
						tag = '".$tag."',
						is_approved = '".$approved."',
						is_approved_edit = '".$approvedEdit."',
						status = '".$pdfStatus."',
						last_modified_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						un_approve_reson = '".$unApproveReson."'
				WHERE
					id = '".$_POST['drawingID']."'";
	mysql_query($updateQRY);

	$insertHistory = "INSERT INTO table_history_details SET
				primary_key = '".$_POST['drawingID']."',
				table_name = 'drawing_register',
				sql_operation = 'UPDATE',
				sql_query = '".serialize($recodArr)."',
				created_by = '".$_SESSION['ww_builder_id']."',
				created_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				last_modified_date = NOW(),
				project_id = '".$_SESSION['idp']."'";
	mysql_query($insertHistory);		
	
	$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Data Updated Successfully !');

$userEmailData = $obj->selQRYMultiple('DISTINCT u.user_id, u.user_email, up.project_id, up.project_name, u.user_type, u.user_fullname, u.recieve_email', 'user_projects AS up, user AS u', 'up.user_id = u.user_id AND up.project_id = '.$_SESSION['idp'].' AND up.is_deleted = 0 AND u.is_deleted = 0 AND is_pdf = 1 AND u.user_id != '.$_SESSION['ww_builder_id'].' ORDER BY project_id');

	$userEmailArr = array();
	$projectName = $userEmailData[0]['project_name'];

	$senderEmailArr = array();
	foreach($userEmailData as $userData){
		if($userData['user_id'] == $_POST['emailRecipent']){//Fetch Recipent Email address and id
			$senderEmailArr = array('fullName' => $userData['user_fullname'], 'emailAdd' => $userData['user_email']);
		}
		if (is_array($userEmailArr[$userData['project_id']])){
			$userEmailArr[$userData['project_id']][] = $userData;
		}else{
			$userEmailArr[$userData['project_id']] = array();
			$userEmailArr[$userData['project_id']][] = $userData;
		}
	}
	
	$companyDetailsData = $obj->selQRYMultiple('trading_name, comp_email, comp_mobile, comp_businessadd1, comp_businessadd2, comp_bussuburb, comp_businessstate, comp_businesscountry, website', 'pms_companies', 'active = 1');
/*
if($approvedEdit != 2){
//Report Generation Start Here
	$docData = array();
	$attribute1Arr = array('"General"', '"Architectural"', '"Structure"', '"Services"', '"Concrete &amp; PT"', '"Lighting"', '"Tenancy Fitout"', '"Penthouse Architecture"', '"Landscaping"');

	$sect = ", max(drr.id) AS revID";
	$orderBy = " ORDER BY dr.attribute1, dr.attribute2, dr.title asc";
	$groupBy = " GROUP BY dr.id";

	$docData = $obj->selQRYMultiple('dr.id, dr.number, dr.title, dr.pdf_name, dr.created_date, dr.is_approved, drr.revision_number, drr.created_date AS revisionAdded, dr.attribute1, dr.attribute2, dr.status'.$sect, 'drawing_register_revision AS drr, drawing_register AS dr', 'drr.is_deleted = 0 AND dr.is_deleted = 0 AND drr.drawing_register_id = dr.id AND dr.attribute1 IN ('.join(',', $attribute1Arr).') AND dr.is_document_transmittal	 = 0 AND dr.project_id = '.$_SESSION['idp'].' AND dr.project_id = '.$_SESSION['idp'].' '.$groupBy.' '.$orderBy);

	$drIDArr = array();
	foreach($docData as $dData){
		$drIDArr[] = $dData['revID'];
	}
	$revisionNumberData = $obj->selQRYMultiple('id, revision_number, drawing_register_id', 'drawing_register_revision', 'id IN ('.join(',', $drIDArr).')');
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
		require('fpdf/mc_table.php');	
		class PDF extends PDF_MC_Table{
			function Footer(){
				$this->SetY(-15);
				$this->SetFont('times','B',10);
				$this->Cell(0, 10, 'DefectID – Copyright Wiseworking 2012 / 2013', 0, 0, 'C');
			}
			
			function header_width(){
				return array(30, 57, 20, 20, 15, 15, 20, 20);
			}
		}
		$pdf = new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		$pdf->SetTopMargin(20);
	
		$pdf->Image('./company_logo/logo.png', 145, 5, 'png', -100);
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
		$d = './report_pdf/'.$builder_id;
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
	$subject = 'Document '.$drawingTitle.' updated in project '.$projectName;
	//$messageType = 'Document Transmittal';
	$messageType = 'Document Transmittal';
	
	$messageDetails = 'Hello,
		A document has been updated on '.$projectName.' Project.
		Document Number : '.$drawingNumber.'
		Drawing Revision : '.$drawingRevision.'
		Attribute 1 : '.$drawingAttribute1.'
		Attribute 2 : '.str_replace('###', ', ', $drawingAttribute2).'<br/>
		Status : '.$pdfStatus.'<br/><br/><br />
		Document Uploaded By : '.$_SESSION['ww_builder_full_name'].'<br/>
		Thanks,<br>Wiseworker customer care';

	if(get_magic_quotes_gpc()){	$messageDetails = stripslashes($messageDetails);	}
	$messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));
	
	$messgeId = 0;
	$getThreadId = $obj->selQRYMultiple('pmb_thread_id', 'drawing_register', 'is_deleted = 0 AND id = "'.$_POST['drawingID'].'"'); 
 	$threadId = isset($getThreadId[0]['pmb_thread_id'])?$getThreadId[0]['pmb_thread_id']:0;

	foreach($userEmailArr[$_SESSION['idp']] as $userEmailArrNew){
		$messageBoardReturn = $obj->messageBoard($_SESSION['idp'], $_SESSION['ww_builder_id'], $userEmailArrNew['user_id'], $subject, $messageType, $messageDetails, '', $messgeId, '', '', '', 0, $threadId);
		
		$messgeId = $messageBoardReturn['messgeId'];
		$threadId = $messageBoardReturn['thread_id'];
		
#		if($userEmailArrNew['recieve_email'] == 1){
//Send Mail Start Here
			$mail = new PHPMailer();
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->SMTPSecure = "tls";                 // sets the prefix to the servier
			$mail->Host       = "pod51022.outlook.com";      // sets GMAIL as the SMTP server
			$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
			$mail->SMTPAuth   = true;                    // enable SMTP authentication
			$mail->Port       = 587;
			$mail->Username   = SMTPUSERNAME; //"smtp@fxbytes.com"; // SMTP account username
			$mail->Password   = SMTPPASSWORD; //"smtp*123";        // SMTP account password

			$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
		
			$mail->SetFrom('WiseworkingSystems@wiseworking.com.au', "Wiseworker");		
			$mail->AddReplyTo('WiseworkingSystems@wiseworking.com.au', "Wiseworker");		
//			$mail->AddReplyTo('administrator@defectid.com', 'defectid.com');
			//$mail->Subject = $subject;
			$mail->Subject = "Crema:".$messgeId."-".$subject;
			$mail->IsHTML(true);
			
			$msg = 'Hello,<br /> <br />
					A document has been updated on '.$projectName.' Project. <br /> <br />
					Document Number : '.$drawingNumber.'<br />
					Drawing Revision : '.$drawingRevision.'<br/>
					Attribute 1 : '.$drawingAttribute1.'<br/>
					Attribute 2 : '.str_replace('###', ', ', $drawingAttribute2).'<br/>
					Status : '.$pdfStatus.'<br/><br/><br />
					Document Uploaded By : '.$_SESSION['ww_builder_full_name'].'<br/>
					<br/>click here to access you message.<br>
					<a href="'.$path.'/pms.php?sect=message_details&id='.base64_encode($messageBoard['thread_id']).'&type=inbox&projID='.base64_encode($_SESSION['idp']).'&byEmail=3" target="_blank">'.$path.'/pms.php?sect=message_details&id='.base64_encode($messageBoard['thread_id']).'&type=inbox&projID='.base64_encode($_SESSION['idp']).'&byEmail=3</a><br /><br />
					Thanks,<br><br />';
											
			$msg .= $companyDetailsData[0]['trading_name'].'<br />
						E: '.$companyDetailsData[0]['comp_email'].'<br />
						P: '.$companyDetailsData[0]['comp_mobile'].'<br />
						A: '.$companyDetailsData[0]['comp_businessadd1'].', '.$companyDetailsData[0]['comp_businessadd2'].', '.$companyDetailsData[0]['comp_bussuburb'].', '.$companyDetailsData[0]['comp_businessstate'].', '.$companyDetailsData[0]['comp_businesscountry'].'<br />
						W: '.$companyDetailsData[0]['website'].'<br />';

			$mail->MsgHTML($msg);			
			$mail->AddAttachment("attachment/".$name);  
			$mail->AddAddress($userEmailArrNew['user_email'], $userEmailArrNew['user_fullname']); // To
			$mail->Send();
			$mail->ClearAddresses();
			$mail->ClearAllRecipients();
//Send Mail End Here
#		}
	}
	
	if($threadId!=0){
	 	$updateQRY = "update drawing_register SET pmb_thread_id = '".$threadId."'
		WHERE project_id = '".$_SESSION['idp']."' AND id='".$_POST['drawingID']."'";
		mysql_query($updateQRY);
	}
	
}*/
//Email and Message Board Entry End Here

	$output = '<div class="drawing_holder1" id="drawing_'.$_POST['drawingID'].'"><div style="text-align:center;font-size:14px;line-height:14px;">';
		$title = substr($drawingTitle, 0, 13);
		if(strlen($drawingTitle) > 13){
			$title = $title.'..';
		}
	$output .= $title;
	$output .= '</div><div style="height:90px;">';
	if($imageName != '' && file_exists('project_drawing_register/'.$_SESSION['idp'].'/'.$imageName)){
		$output .= '<img id="'.$pdfRegID.'" src="images/default_PDF_register_64x64.png" onclick="showRevisions(this.id);" />';
	}else{
		$output .= 'File Curropt !';
	}
	$output .= '</div>
	<div style="text-align:center;height:15px;" id="delete_'.$pdfRegID.'"><img class="action" src="images/add.png" id="addRevision" title="add new revision" onclick="addNewRegisterRevision('.$pdfRegID.');" /><img class="action" src="images/edit_right.png" id="editRevision" title="edit register"  onclick="editDrawingRegister('.$pdfRegID.');" /><img class="action" src="images/delete.png"  id="editRevision" title="delete register" onclick="removeImages('.$pdfRegID.', \'drawing_'.$pdfRegID.'\')" /></div></div>';

//UnApprove Reson Mail Start Here
#echo $approvedEdit;print_r($senderEmailArr);echo $_POST['emailSendFlag'];die;
if($approvedEdit == 2 && !empty($senderEmailArr) && $_POST['emailSendFlag'] != 0){
//UnApprove Reson Send Mail Start Here
	$subject = $drawingTitle." document not approved";
	$messageType = 'Document Transmittal';
	
	$messageDetails = 'Hello,
			The following drawing has not been approved:<br /><br />
			Document Number : '.$drawingNumber.'
			Drawing Revision : '.$drawingRevision.'
			Attribute 1 : '.$drawingAttribute1.'
			Attribute 2 : '.str_replace('###', ', ', $drawingAttribute2).'<br /><br />
			It was not approved with the following comments:<br /><br />'.$unApproveReson.'<br/>
			Thanks,<br>Wiseworker customer care';
	
	if(get_magic_quotes_gpc()){	$messageDetails = stripslashes($messageDetails);	}
	$messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));
	
	$messageBoardReturn = $obj->messageBoard($_SESSION['idp'], $_SESSION['ww_builder_id'], $_POST['emailRecipent'], $subject, $messageType, $messageDetails, '', $messgeId, '', '', '', 0, $threadId);
	
	$newThreadId = $messageBoardReturn['thread_id'];

	$mail = new PHPMailer();
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

	$mail->SetFrom('WiseworkingSystems@wiseworking.com.au', "Wiseworker");		
	$mail->AddReplyTo('WiseworkingSystems@wiseworking.com.au', "Wiseworker");		
//			$mail->AddReplyTo('administrator@defectid.com', 'defectid.com');
	//$mail->Subject = $subject;
	$mail->Subject = $drawingTitle." document not approved";
	$mail->IsHTML(true);
	
	$msg = 'Hello,<br /> <br />
			The following drawing has not been approved:<br /><br />
			Document Number : '.$drawingNumber.'<br />
			Drawing Revision : '.$drawingRevision.'<br/>
			Attribute 1 : '.$drawingAttribute1.'<br/>
			Attribute 2 : '.str_replace('###', ', ', $drawingAttribute2).'<br /><br />
			It was not approved with the following comments:<br /><br />'.$unApproveReson.'<br/>
			<br/>click here to access the full message.<br>
			<a href="'.$path.'/pms.php?sect=message_details&id='.base64_encode($messageBoard['thread_id']).'&type=inbox&projID='.base64_encode($_SESSION['idp']).'&byEmail=3" target="_blank">'.$path.'/pms.php?sect=message_details&id='.base64_encode($messageBoard['thread_id']).'&type=inbox&projID='.base64_encode($_SESSION['idp']).'&byEmail=3</a><br /><br />
			Thanks,<br><br />';
									
	$msg .= $companyDetailsData[0]['trading_name'].'<br />
				E: '.$companyDetailsData[0]['comp_email'].'<br />
				P: '.$companyDetailsData[0]['comp_mobile'].'<br />
				A: '.$companyDetailsData[0]['comp_businessadd1'].', '.$companyDetailsData[0]['comp_businessadd2'].', '.$companyDetailsData[0]['comp_bussuburb'].', '.$companyDetailsData[0]['comp_businessstate'].', '.$companyDetailsData[0]['comp_businesscountry'].'<br />
				W: '.$companyDetailsData[0]['website'].'<br />';

	$mail->MsgHTML($msg);			
	$mail->AddAttachment("attachment/".$name);  
	$mail->AddAddress($senderEmailArr['emailAdd'], $senderEmailArr['fullName']); // To
	$mail->Send();
	$mail->ClearAddresses();
	$mail->ClearAllRecipients();
	
	//Update email gone aur pmb id
//UnApprove Reson Send Mail End Here
	$updateQryTh = "UPDATE drawing_register SET unapproved_pmb_thread_id = ".$newThreadId." WHERE id = '".$_POST['drawingID']."'";
	mysql_query($updateQryTh);
}
//UnApprove Reson Mail End Here

	echo json_encode($outputArr);
}
if(isset($_REQUEST["name"])){
	$drawData = array();
	$drawData = $obj->selQRYMultiple('id, title, pdf_name, number, revision, comments, attribute1, attribute2, tag, is_approved, status, is_approved_edit, un_approve_reson, created_by, unapproved_pmb_thread_id', 'drawing_register', 'is_deleted = 0 AND id = "'.$_REQUEST['tableID'].'"'); 
	
	if(!empty($drawData)){ ?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Edit Drawing Register</legend>
		<form name="editDrawingForm" id="editDrawingForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr style=" <?php echo ($drawData[0]['is_approved_edit'] == 1)?'display:none;':''; ?>">
				<td valign="top" align="left" width="50%">Document&nbsp;Title / Description <span class="req">*</span></td>
				<td align="left" width="50%">
					<textarea name="drawingTitle" id="drawingTitle" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"><?=$drawData[0]['title']?></textarea>
					<lable for="drawingTitle" id="errorDrawingTitle" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Document Description field is required</div></lable>
					<lable for="multiUpload" id="errorDrawingTitle1" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Document Description and Document Number can't be same.</div></lable>
				</td>
			</tr>
			<tr style=" <?php echo ($drawData[0]['is_approved_edit'] == 1)?'display:none;':''; ?>">
				<td valign="top" align="left">Document&nbsp;Number / Code <span class="req">*</span></td>
				<td align="left">
					<input type="text" name="drawingNumber" id="drawingNumber" class="input_small" value="<?=$drawData[0]['number']?>" />
					<lable for="drawingNumber" id="errorDrawingNumber" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Document Number field is required</div></lable>
				</td>
			</tr>
			<tr style=" <?php echo ($drawData[0]['is_approved_edit'] == 1)?'display:none;':''; ?>">
				<td valign="top" align="left">Drawing&nbsp;Revision</td>
				<td align="left">
					<input type="text" name="drawingRevision" id="drawingRevision" class="input_small" value="<?=$drawData[0]['revision']?>" maxlength="5" />
				</td>
			</tr>
			<tr style=" <?php echo ($drawData[0]['is_approved_edit'] == 1)?'display:none;':''; ?>">
				<td valign="top" align="left">Comments / Notes</td>
				<td align="left">
					<textarea name="drawingNotes" id="drawingNotes" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"><?=$drawData[0]['comments']?></textarea>
				</td>
			</tr>
			<tr style=" <?php echo ($drawData[0]['is_approved_edit'] == 1)?'display:none;':''; ?>">
				<td valign="top" align="left">Attribute 1 <span class="req">*</span></td>
				<td align="left">
					<select name="drawingattribute1" id="drawingattribute1" class="select_box" style="margin-left:0px;"  />
						<?php $attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Lighting', 'Tenancy Fitout', 'Landscaping');
						if(in_array($_SESSION['userRole'], $permArray) || $_SESSION['ww_builder']['user_type'] == 'manager'){
							$attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Lighting', 'Tenancy Fitout', 'Landscaping');
						}
						if($_SESSION['userRole'] == 'Lighting Consultant')	$attribute1Arr = array('Lighting');
						if($_SESSION['userRole'] == 'Tenancy Fitout')	$attribute1Arr = array('Tenancy Fitout');
						
						for($i=0;$i<sizeof($attribute1Arr);$i++){?>
						<option value="<?=$attribute1Arr[$i]?>" <? if(html_entity_decode($attribute1Arr[$i]) == $drawData[0]['attribute1'])echo 'selected="selected"';?> ><?=$attribute1Arr[$i]?></option>
						<?php }?>
					</select>
				</td>
			</tr>
			<tr style=" <?php echo ($drawData[0]['is_approved_edit'] == 1)?'display:none;':''; ?>">
				<td valign="top" align="left">Attribute 2</td>
				<td align="left">
					<select name="drawingattribute2" id="drawingattribute2" class="select_box" multiple="multiple" style="margin-left:0px;width:311px;height:60px;background-image:url(images/texarea_select_box_small.png);" />
						<option value="" <?php echo empty($attribute2Arr)?'selected="selected"':''; ?>>Select</option>
						<?php $attribute2Arr = array();
						if($drawData[0]['attribute1'] == 'General'){ $attribute2Arr = array('Drawings', 'Specifications', 'Reports & Schedules', '3D images', 'Marketing', 'Brief and overview', 'Document Transmittal'); }
						
						if($drawData[0]['attribute1'] == 'Architectural'){ $attribute2Arr = array('Drawings', 'Specifications', 'Reports & Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Document Transmittal');
							if($_SESSION['architectAttrTwo'] != 'ALL' && $_SESSION['architectAttrTwo'] != '' && $_SESSION['userRole'] == 'Architect')
								$attribute2Arr = array($_SESSION['architectAttrTwo']);
						}
						
						if($drawData[0]['attribute1'] == 'Structure'){ $attribute2Arr = array('Drawings', 'Specifications', 'Reports & Schedules', 'Civil', 'Document Transmittal', 'Site Inspection', 'Project Advice Notice'); }
						
						if($drawData[0]['attribute1'] == 'Services'){ $attribute2Arr = array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Mechanical', 'Electrical', 'Hydraulic', 'Fire', 'Document Transmittal'); }
						
						if($drawData[0]['attribute1'] == 'Concrete & PT'){ $attribute2Arr = array('Concrete Profile', 'Bottom Reinforcement', 'Top Reinforcement', 'Sheer Reinforcement', 'PT Design', 'Details', 'Document Transmittals'); }
						
						if($drawData[0]['attribute1'] == 'Lighting'){ $attribute2Arr = array('Drawings', 'Specifications', 'Document Transmittal'); }
						
						if($drawData[0]['attribute1'] == 'Tenancy Fitout'){ $attribute2Arr = array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos', 'Document Transmittal'); }
	
						//if($drawData[0]['attribute1'] == 'Penthouse Architecture'){ $attribute2Arr = array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos', 'Document Transmittal'); }
	
						if($drawData[0]['attribute1'] == 'Landscaping'){ $attribute2Arr = array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos', 'Document Transmittal'); }
						
						if(in_array($_SESSION['userRole'], $permArray) || $_SESSION['ww_builder']['user_type'] == 'manager'){
							if($drawData[0]['attribute1'] == 'Autocad Files'){ $attribute2Arr = array('Architectural', 'Services', 'Structure'); }
						}
						if(!empty($attribute2Arr)){
							$exData = explode('###', $drawData[0]['attribute2']);
							for($i=0;$i<sizeof($attribute2Arr);$i++){ ?>
							<option value="<?=$attribute2Arr[$i]?>" <? if(in_array($attribute2Arr[$i], $exData))echo 'selected="selected"';?> ><?=$attribute2Arr[$i]?></option>
							<?php }
						}?>
					</select>
				</td>
			</tr>
			<tr style=" <?php echo ($drawData[0]['is_approved_edit'] == 1)?'display:none;':''; ?>">
				<td valign="top" align="left">Tag</td>
				<td align="left">
					<input type="text" name="tag" id="tag" class="input_small" value="<?=$drawData[0]['tag']?>"  />
					<input type="hidden" name="drawingID" id="drawingID" value="<?=$_REQUEST['tableID']?>" />
				</td>
			</tr>
			<tr style=" <?php echo ($drawData[0]['is_approved_edit'] == 1)?'display:none;':''; ?>">
				<td valign="top" align="left">Status <span class="req">*</span></td>
				<td align="left">
					<?php $revStatusArr = array('Tender', 'Issued for Construction', 'For Information');?>
					<select name="pdfStatus" id="pdfStatusDyna" class="select_box" style="margin-left:0px;"  />
						<?php foreach($revStatusArr as $key=>$revStatus){?>
							<option value="<?=$revStatus?>" <?php if($drawData[0]['status'] == $revStatus){echo 'selected="selected"';}?>><?=$revStatus?></option>
						<?php }?>
					</select>
					<lable for="pdfStatus" id="errorPdfStatus" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Status field is required</div></lable>
				</td>
			</tr>
			<?php if($_SESSION['userRole'] != 'General Consultant' || $_SESSION['userRole'] != 'Architect'){?>
			<tr style=" <?php echo ($_SESSION['ww_builder']['user_type']=="manager")?"":"display:none;"; echo ($drawData[0]['is_approved_edit'] == 1)?'display:none;':''; ?>">
				<td valign="top" align="left">Download on iPad</td>
				<td align="left">
					<input  type="checkbox" name="approved" id="approved" value="1" <?php if($drawData[0]['is_approved'] == 1){ echo 'checked="checked"'; }?> />
				</td>
			</tr>
			<?php }?>
			<tr style=" <?php echo ($_SESSION['ww_builder']['user_type']=="manager") ? '' : 'display:none;';?>">
				<td valign="top" align="left">Approved</td>
				<td align="left">
                <?php $resonDisFlag = "display:none;";?>
					<input type="radio" name="approved_edit" id="approved_editYes" value="1" <?php if($drawData[0]['is_approved_edit'] == 1){ echo 'checked="checked"'; }?> />
					<label for="approved_editYes">&nbsp;Yes</label>
					<input type="radio" name="approved_edit" id="approved_editNo" value="2" <?php if($drawData[0]['is_approved_edit'] == 2){ echo 'checked="checked"'; $resonDisFlag = "";}?> />
                    <label for="approved_editNo">&nbsp;No</label>
					<input type="radio" name="approved_edit" id="approved_editNA" <?php if($drawData[0]['is_approved_edit'] == 0){ echo 'checked="checked"'; }?> />
					<label for="approved_editNA">&nbsp;NA</label>
				</td>
			</tr>
            <tr id="unApproveResonHolder" style=" <?=$resonDisFlag;?>">
				<td valign="top" align="left">Reason</td>
				<td align="left">
                	<textarea class="text_area_small" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;" rows="2" cols="25" id="unApproveReson" name="unApproveReson"><?=$drawData[0]['un_approve_reson']?></textarea>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
	                <input type="hidden" name="emailRecipent" id="emailRecipent" value="<?=$drawData[0]['created_by']?>" />
					<input type="hidden" name="emailSendFlag" id="emailSendFlag" value="0" />
					<input type="hidden" name="drawingattribute2Multi" id="drawingattribute2Multi" value="<?=$drawData[0]['attribute2']?>" />
					<input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/update.png);font-size:0px; border:none; width:111px;float:left;" onclick="updateDrawingRegisterData();" />
					&nbsp;&nbsp;&nbsp; <a id="ancor" href="javascript:closePopup(300);" onclick="yes"> <img src="images/back_btn.png" style="border:none; width:111px;" /> </a>
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }
}
if(isset($_REQUEST["uniqueId"])){
	$drawingIdsArr = explode(",", $_POST['fileIds']);
	$updateQRY = "UPDATE drawing_register SET
						is_approved = 1,
						last_modified_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."'
				WHERE
					id IN (".$_POST['fileIds'].")";
	mysql_query($updateQRY);
	
	$outputArr = array('status'=> false, 'msg'=> 'Record updated Successfully ');
	
	if(mysql_affected_rows() > 0){
		$outputArr = array('status'=> true, 'msg'=> 'Record updated Successfully ');
	}
	echo json_encode($outputArr);
}


if(isset($_REQUEST["deletedId"]) && isset($_POST['ids']))
{
	$updateQRY = "UPDATE drawing_register SET
						is_deleted = 1,
						last_modified_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."'
				WHERE
					id IN (".$_POST['ids'].")";

	mysql_query($updateQRY);
	
	$outputArr = 'Error occur while updating the records.';
	
	if(mysql_affected_rows() > 0)
	{
		$outputArr = 'Record updated Successfully.';
	}
	
	echo $outputArr;
}


?>
