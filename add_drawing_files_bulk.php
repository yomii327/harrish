<?php session_start();

$builder_id=$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
require_once('includes/class.phpmailer.php');
$obj = new COMMAN_Class(); 

define('ATTCHMENTPATH', '/var/www/constructionid.com/vccc/project_drawing_register/'.$_SESSION['idp']);
define('ATTCHMENTCOPYPATH', '/var/www/constructionid.com/vccc/project_drawing_register/'.$_SESSION['idp']);

if(!isset($_SESSION['drRequsetCount']))	$_SESSION['drRequsetCount'] = 0;

if(isset($_REQUEST["antiqueID"])){
//Get Exsiting records Start Here
/*	$existDrawingData = array();
	$existDrawingData = $obj->selQRYMultiple('id, title, number, revision', 'drawing_register', 'is_document_transmittal = 0 AND is_deleted = 0 ORDER BY id');
	$existDrawingArr = array();
	if(!empty($existDrawingData)){
		foreach($existDrawingData as $exDrawData){
			$existDrawingArr[$exDrawData['number']] = array($exDrawData['id'], $exDrawData['revision']);
		}
	}*/
//Get Exsiting records End Here
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
		$tempArr = explode('-', $processFileName);
		$lastEle = array_pop($tempArr);
		$fileNameTitle = trim(implode('-', $tempArr));
		$revisionName = $lastEle ;
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

#echo $fetchKey;print_r($_POST);print_r($existDrawingArr);die;

	if(array_key_exists($fetchKey, $existDrawingArr)){
		$exDrawingArr = explode('####', $existDrawingArr[$fetchKey]);
		$exDrawingID = $exDrawingArr[0];
		$exDrawingRevIDArr = explode(".", $exDrawingArr[1]);
		$exDrawingRevID = $exDrawingRevIDArr[0];
		
		if(!is_dir('./project_drawing_register/'))	@mkdir('./project_drawing_register/', 0777);
		if(!is_dir('./project_drawing_register/'.$_SESSION['idp']))	@mkdir('./project_drawing_register/'.$_SESSION['idp'], 0777);
		$imageName = $exDrawingRevID.'.'.$file_ext;

		move_uploaded_file($_FILES['file']['tmp_name'], "project_drawing_register/".$_SESSION['idp']."/".$imageName);

		$updateQRY = "UPDATE drawing_register SET
						dwg_name = '".$imageName."'
					WHERE id = '".$exDrawingID."'";
		mysql_query($updateQRY);

		$secUpdateQRY = "UPDATE drawing_register_revision SET
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
	}else{
		 $inssertQRY = "INSERT INTO drawing_register SET
			project_id = '".$_SESSION['idp']."',
			title = '".addslashes($drawingTitle)."',
			number = '".addslashes($drawingNumber)."',
			revision = '".addslashes($revisionName)."',
			last_modified_date = NOW(),
			attribute1 = '".addslashes($drawingAttribute1)."',
			attribute2 = '".addslashes($drawingattribute2)."',
			status = '".addslashes($pdfStatus)."',
			last_modified_by = '".$_SESSION['ww_builder_id']."',
			created_date = NOW(),
			created_by = '".$_SESSION['ww_builder_id']."'"; 
		mysql_query($inssertQRY);
		$pdfRegID = mysql_insert_id(); 
	
	//Set session for uploaded file start Here
		$_SESSION['drRequsetCount']++;
		$_SESSION['drDataArr'] = array();
		$_SESSION['drDataArr'][] = $drawingNumber;
		$_SESSION['drDataArr'][] = $drawingTitle;
		$_SESSION['drDataArr'][] = $drawingRevision;
		$_SESSION['drDataArr'][] = $drawingAttribute1;
		$_SESSION['drDataArr'][] = $drawingattribute2;
		$_SESSION['drDataArr'][] = $pdfRegID;
		$_SESSION['finalDrDataArr'][] = $_SESSION['drDataArr'];
	//Set session for uploaded file end Here
		
	#File Upload Section
		$secondInsertQRY = "INSERT INTO drawing_register_revision SET
			project_id = '".$_SESSION['idp']."',
			revision_number = '".$revisionName."',
			drawing_register_id = '".$pdfRegID."',
			revision_status = '".$pdfStatus."',
			last_modified_date = NOW(),
			last_modified_by = '".$_SESSION['ww_builder_id']."',
			created_date = NOW(),
			created_by = '".$_SESSION['ww_builder_id']."'";
		mysql_query($secondInsertQRY);
		
		$imageid = mysql_insert_id();
		
		$imageName = $imageid.'.'.$file_ext;
	
		if(!is_dir('./project_drawing_register/'))	@mkdir('./project_drawing_register/', 0777);
		if(!is_dir('./project_drawing_register/'.$_SESSION['idp']))	@mkdir('./project_drawing_register/'.$_SESSION['idp'], 0777);

		move_uploaded_file($_FILES['file']['tmp_name'], "project_drawing_register/".$_SESSION['idp']."/".$imageName);
		
	#File Upload Section
		$updateQRY = "UPDATE drawing_register SET
						pdf_name = '".$imageName."', last_modified_date = NOW()
					WHERE id = '".$pdfRegID."'";
		mysql_query($updateQRY);
		$secUpdateQRY = "UPDATE drawing_register_revision SET
						pdf_name = '".$imageName."', last_modified_date = NOW()
					WHERE id = '".$imageid."'";
		mysql_query($secUpdateQRY);
		
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
					primary_key = '".$imageid."',
					table_name = 'drawing_register',
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
	$attribute1Arr = array('"General"', '"Architectural"', '"Structure"', '"Services"', '"Concrete &amp; PT"', '"Lighting"', '"Tenancy Fitout"', '"Penthouse Architecture"', '"Landscaping"');

	$sect = ", max(drr.id) AS revID";
	$orderBy = " ORDER BY dr.attribute1, dr.attribute2, dr.title asc";
	$groupBy = " GROUP BY dr.id";

	$docData = $obj->selQRYMultiple('dr.id, dr.number, dr.title, dr.pdf_name, dr.created_date, dr.is_approved, drr.revision_number, drr.created_date AS revisionAdded, dr.attribute1, dr.attribute2, dr.status'.$sect, 'drawing_register_revision AS drr, drawing_register AS dr', 'drr.is_deleted = 0 AND dr.is_deleted = 0 AND drr.drawing_register_id = dr.id AND dr.attribute1 IN ('.join(',', $attribute1Arr).') AND dr.is_document_transmittal	= 0 AND dr.project_id = '.$_SESSION['idp'].' AND dr.project_id = '.$_SESSION['idp'].' '.$groupBy.' '.$orderBy);

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
		$companyDetailsData = $obj->selQRYMultiple('trading_name, comp_email, comp_mobile, comp_businessadd1, comp_businessadd2, comp_bussuburb, comp_businessstate, comp_businesscountry, website', 'pms_companies', 'active = 1');
		
		$userEmailData = $obj->selQRYMultiple('DISTINCT u.user_id, u.user_email, up.project_id, up.project_name, u.user_type, u.user_fullname, u.recieve_email', 'user_projects AS up, user AS u', 'up.user_id = u.user_id AND up.project_id = '.$_SESSION['idp'].' AND up.is_deleted = 0 AND u.is_deleted = 0 AND is_pdf = 1 AND u.user_id != '.$_SESSION['ww_builder_id'].' ORDER BY project_id');
		$userEmailArr = array();
		$projectName = $userEmailData[0]['project_name'];
		foreach($userEmailData as $userData){
			if (is_array($userEmailArr[$userData['project_id']])){
				$userEmailArr[$userData['project_id']][] = $userData;
			}else{
				$userEmailArr[$userData['project_id']] = array();
				$userEmailArr[$userData['project_id']][] = $userData;
			}
		}
		$subject = 'Document added in project '.$projectName;
		$messageType = 'Document Transmittal';
	
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
			
			foreach($userEmailArr[$_SESSION['idp']] as $userEmailArrNew){
				$attahmentMsgb = "";
				$messageBoardReturn = $obj->messageBoard($_SESSION['idp'], $_SESSION['ww_builder_id'], $userEmailArrNew['user_id'], $subject, $messageType, $pmbMessageDetails, $attahmentMsgb, $messgeId);
				$messgeId = $messageBoardReturn['messgeId'];
				$threadId = $messageBoardReturn['thread_id'];
			}
			if($threadId!=0){
				$updateQRY = "update drawing_register SET pmb_thread_id = '".$threadId."'
				WHERE project_id = '".$_SESSION['idp']."' AND id='".$drawingDat[5]."'";
				mysql_query($updateQRY);
			}
		}

		foreach($userEmailArr[$_SESSION['idp']] as $userEmailArrNew){
#			if($userEmailArrNew['recieve_email'] == 1){
	//Send Mail Start Here
				$mail = new PHPMailer();
				$mail->IsSMTP(); // telling the class to use SMTP
				$mail->SMTPSecure = "tls";                 // sets the prefix to the servier
				$mail->Host       = smtpHost;      // sets GMAIL as the SMTP server
				$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
				$mail->SMTPAuth   = true;                    // enable SMTP authentication
				$smtpPort = smtpPort;
				if(!empty($smtpPort)){
					$mail->Port =  smtpPort; //587;
				}
				$mail->Username   = smtpUsername; //"smtp@fxbytes.com"; // SMTP account username
				$mail->Password   = smtpPassword; //"smtp*123";        // SMTP account password
	
				$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
			
				$mail->SetFrom('WiseworkingSystems@wiseworking.com.au', "Wiseworker");		
				$mail->AddReplyTo('WiseworkingSystems@wiseworking.com.au', "Wiseworker");		
	//			$mail->AddReplyTo('administrator@defectid.com', 'defectid.com');
				//$mail->Subject = $subject;
				$mail->Subject = "CremaDev:".$messgeId."-".$subject;
				$mail->IsHTML(true);
	
				$Document = 'Document';
				if(sizeof($_SESSION['finalDrDataArr']) > 1)
					$Document = 'Documents';

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
				<br/>click here to access you message.<br>
					<a href="'.$path.'/pms.php?sect=messages&id='.$_SESSION['idp'].'&byEmail=3" target="_blank">'.$path.'/pms.php?sect=messages&id='.$_SESSION['idp'].'&byEmail=3</a><br /><br /><br />
					Thanks,<br><br />';
						
				$msg .= $companyDetailsData[0]['trading_name'].'<br />
						E: '.$companyDetailsData[0]['comp_email'].'<br />
						P: '.$companyDetailsData[0]['comp_mobile'].'<br />
						A: '.$companyDetailsData[0]['comp_businessadd1'].', '.$companyDetailsData[0]['comp_businessadd2'].', '.$companyDetailsData[0]['comp_bussuburb'].', '.$companyDetailsData[0]['comp_businessstate'].', '.$companyDetailsData[0]['comp_businesscountry'].'<br />
						W: '.$companyDetailsData[0]['website'].'<br />';

				$mail->MsgHTML($msg);			
				$mail->AddAttachment("attachment/".$name);  
				$mail->AddAddress($userEmailArrNew['user_email'], $userEmailArrNew['user_fullname']); // To
				#$mail->Send();
				$mail->ClearAddresses();
				$mail->ClearAllRecipients( );
	//Send Mail End Here
#			}
		}

	if($threadId!=0){
	 	$updateQRY = "update drawing_register SET pmb_thread_id = '".$threadId."'
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
		<legend style="color:#000000;">Bulk Upload Drawing PDFs</legend>
		<form name="addDrawingForm" id="addDrawingForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top"  colspan="2" align="left">Drawing&nbsp;PDFs&nbsp;<span class="req">*</span></td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="innerDiv" id="innerDiv" style="height:300px;width:830px;overflow:auto;">
						<div align="center" style="font-size:12px;">Drop DWG Files Here</div>
					</div>
					<input type="file" name="multiUpload" id="multiUpload" multiple />
					<lable for="multiUpload" id="errorMultiUpload" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing PDF field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Attribute 1 <span class="req">*</span></td>
				<td align="left">
					<select name="drawingattribute1" id="drawingattribute1" class="select_box" style="margin-left:0px;"  />
				<?php $attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Lighting', 'Tenancy Fitout', 'Penthouse Architecture', 'Landscaping');
					if(in_array($_SESSION['userRole'], $permArray) || $_SESSION['ww_builder']['user_type'] == 'manager'){
						$attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Lighting', 'Tenancy Fitout', 'Penthouse Architecture', 'Landscaping');
					}
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
				<td colspan="2" align="center">
					<ul class="buttonHolder">
						<li>
							<input type="submit" name="button" class="submit_btn" id="buttonFirstSubmit" style="background-image:url(images/upload_btn.png);font-size:0px; border:none; width:111px;float:left;" />
							<input type="hidden" name="validationFlag" id="validationFlag" value="2" />
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
