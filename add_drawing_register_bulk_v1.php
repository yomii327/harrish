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

if(!isset($_SESSION['drRequsetCount']))	$_SESSION['drRequsetCount'] = 0;

if(isset($_REQUEST["antiqueID"])){ #print_r($_REQUEST);die;
	$existingDcTransID = $_REQUEST['existingDcTransID'];
	//$emailUserList = unserialize($_REQUEST['emailUserList']);//Array for send emails
	$emailUserList = $obj->getAddressBook();
	#print_r($emailUserList);die;
	#$notificationData = $obj->selQRYMultiple('id, attachment_noti_type', 'pmb_address_book', 'is_deleted = 0 AND project_id = '.$_SESSION['idp'].'');
	#$notiArr = array();
	#foreach($notificationData as $notificationDataVal){
#		$notiArr[$notificationDataVal['id']] = $notificationDataVal['attachment_noti_type']; 
#	}
	#print_r($notiArr);die;	
	$filename = $_FILES['file']['name']; // Drawing File Name
	$recodArr['status'] = $pdfStatus = trim(addslashes($_POST['pdfStatus']));
	$fNameArr = explode('.', $filename);

	$file_ext = array_pop($fNameArr);

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
	
	$processFileName = $tempFileName = implode('.', $fNameArr);
	$revisionName = "";
	if(strpos($processFileName, "[") !== false){
		if(strpos($processFileName, "]") !== false){
			$tempArr = explode('[', $processFileName);
			$lastEle = array_pop($tempArr);
			$documentTitle = array_pop($tempArr);
			$fileNameTitle = trim(implode('[', $tempArr));
			$revisionNameArr = explode(']', $lastEle);
			$revisionName = $revisionNameArr[0];
		}
	}
	if(strpos($processFileName, "(") !== false){
		if(strpos($processFileName, ")") !== false){
			$tempArr = explode('(', $processFileName);
			$lastEle = array_pop($tempArr);
			$documentTitle = array_pop($tempArr);
			$fileNameTitle = trim(implode('(', $tempArr));
			$revisionNameArr = explode(')', $lastEle);
			$revisionName = $revisionNameArr[0];
		}
	}
	if(strpos($processFileName, "{") !== false){
		if(strpos($processFileName, "}") !== false){
			$tempArr = explode('{', $processFileName);
			$lastEle = array_pop($tempArr);
			$documentTitle = array_pop($tempArr);
			$fileNameTitle = trim(implode('{', $tempArr));
			$revisionNameArr = explode('}', $lastEle);
			$revisionName = $revisionNameArr[0];
		}
	}
	if($revisionName == ""){
		if(strpos($processFileName, "_") !== false){
			$tempArr = explode('_', $processFileName);
			$lastEle = array_pop($tempArr);
			$documentTitle = array_pop($tempArr);
			$fileNameTitle = trim(implode('_', $tempArr));
			$revisionName = $lastEle ;	
		}else{
			$tempArr = explode('-', $processFileName);
			$lastEle = array_pop($tempArr);
			$documentTitle = array_pop($tempArr);
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
	#echo $fetchKey;print_r($_REQUEST);die;
	if(isset($_POST['drawingattribute2js'][$fetchKey]) && !empty($_POST['drawingattribute2js'][$fetchKey]))
		$recodArr['attribute2'] = $drawingattribute2 = trim($_POST['drawingattribute2js'][$fetchKey]);

	if(isset($_POST['revisionNo'][$fetchKey]) && !empty($_POST['revisionNo'][$fetchKey]))
		$revisionName = trim($_POST['revisionNo'][$fetchKey]);

	if(isset($_POST['nameTitle'][$fetchKey]) && !empty($_POST['nameTitle'][$fetchKey]))
		$drawingNumber = trim($_POST['nameTitle'][$fetchKey]);		
	
	if(isset($_POST['description'][$fetchKey]) && !empty($_POST['description'][$fetchKey]))
		$drawingTitle = trim($_POST['description'][$fetchKey]);		
	
	$recodArr['attribute1'] = $drawingAttribute1 = trim(addslashes($_POST['drawingattribute1']));

//Update DT Section Start Here
	if($drawingAttribute1 != $_POST['drAttr']){
		$qry = "UPDATE drawing_register_module_one SET attribute1 = '".$drawingAttribute1."' WHERE is_document_transmittal = 1 AND id = ".$_POST['documentTransmittalID'];
		mysql_query($qry);
	}
//Update DT Section Start Here

	$drawingRevision = $revisionName;

	if(array_key_exists($fetchKey, $existDrawingArr)){
		$exDrawingArr = explode('####', $existDrawingArr[$fetchKey]);
		$exDrawingID = $exDrawingArr[0];

		$insertQRY = "INSERT INTO drawing_register_revision_module_one SET
				project_id = '".$_SESSION['idp']."',
				drawing_register_id = '".$exDrawingID."',
				revision_number = '".$drawingRevision."',
				revision_status = '".$pdfStatus."',
				last_modified_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				created_date = NOW(),
				created_by = '".$_SESSION['ww_builder_id']."'";
		mysql_query($insertQRY);
		$imageid = mysql_insert_id();
		$imageName = $imageid.'.'.$file_ext;
		
		if(!is_dir('./project_drawing_register_v1/'))	@mkdir('./project_drawing_register_v1/', 0777);
		if(!is_dir('./project_drawing_register_v1/'.$_SESSION['idp']))	@mkdir('./project_drawing_register_v1/'.$_SESSION['idp'], 0777);

		move_uploaded_file($_FILES['file']['tmp_name'], './project_drawing_register_v1/'.$_SESSION['idp'].'/'.$imageName);
		$updateQRY = "UPDATE drawing_register_module_one SET
					project_id = '".$_SESSION['idp']."',
					pdf_name = '".$imageName."',
					revision = '".$drawingRevision."',
					status = '".$pdfStatus."',
					last_modified_date = NOW(),
					last_modified_by = '".$_SESSION['ww_builder_id']."'
			WHERE
				id = '".$exDrawingID."'";
		mysql_query($updateQRY);
		
		$secUpdateQRY = "UPDATE drawing_register_revision_module_one SET pdf_name = '".$imageName."', last_modified_date = NOW() WHERE id = '".$imageid."'";
		mysql_query($secUpdateQRY);

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
		 $drawingNumberData = $obj->selQRYMultiple('id, title, number, revision, status','drawing_register_module_one','number = "'.$drawingNumber.'" AND title = "'.$drawingTitle.'" AND is_deleted = 0 AND project_id = '.$_SESSION['idp'].'', 'YU');
		//condition for supersedded
		#print_r($drawingNumberData);
		if(!empty($drawingNumberData)){
			$nextRev = $drawingNumberData[0]['revision']+1;
			if($nextRev < 10){
				if(strlen($drawingNumberData[0]['revision'])){
					$rev =  '0' . $nextRev;
				}else{
					$rev = $nextRev;
				}
			}else{
				$rev = $nextRev;
			}
			 $insertQRY = "INSERT INTO drawing_register_revision_module_one SET
				project_id = '".$_SESSION['idp']."',
				drawing_register_id = '".$drawingNumberData[0]['id']."',
				revision_number = '".$rev."',
				revision_status = '".$drawingNumberData[0]['status']."',
				original_file_name = '".$filename."',
				last_modified_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				created_date = NOW(),
				created_by = '".$_SESSION['ww_builder_id']."'";
		mysql_query($insertQRY);
		$imageid = mysql_insert_id();
		$imageName = $imageid.'.'.$file_ext;
		
		if(!is_dir('./project_drawing_register_v1/'))	@mkdir('./project_drawing_register_v1/', 0777);
		if(!is_dir('./project_drawing_register_v1/'.$_SESSION['idp']))	@mkdir('./project_drawing_register_v1/'.$_SESSION['idp'], 0777);

		move_uploaded_file($_FILES['file']['tmp_name'], './project_drawing_register_v1/'.$_SESSION['idp'].'/'.$imageName);
		$updateQRY = "UPDATE drawing_register_module_one SET
					project_id = '".$_SESSION['idp']."',
					pdf_name = '".$imageName."',
					revision = '".$rev."',
					status = '".$pdfStatus."',
					last_modified_date = NOW(),
					last_modified_by = '".$_SESSION['ww_builder_id']."'
			WHERE
				id = '".$drawingNumberData[0]['id']."'";
		mysql_query($updateQRY);
		
		$secUpdateQRY = "UPDATE drawing_register_revision_module_one SET pdf_name = '".$imageName."', last_modified_date = NOW() WHERE id = '".$imageid."'";
		mysql_query($secUpdateQRY);		
		$outputArr = array('data'=>$_REQUEST,'status'=> true, 'msg'=> 'Drawing Register Revision Added Successfully!', 'msg'=> 'Drawing Register Revision Added Successfully !','sStatus'=>$sstatus,'fStatus'=>$fstatus);	
		echo json_encode($outputArr);
		}else{
		 $inssertQRY = "INSERT INTO drawing_register_module_one SET
			project_id = '".$_SESSION['idp']."',
			title = '".$drawingTitle."',
			number = '".$drawingNumber."',
			revision = '".$revisionName."',
			last_modified_date = NOW(),
			attribute1 = '".$drawingAttribute1."',
			attribute2 = '".$drawingattribute2."',
			file_type = '".$drawingAttribute3."',
			status = '".$pdfStatus."',
			last_modified_by = '".$_SESSION['ww_builder_id']."',
			created_date = NOW(),
			created_by = '".$_SESSION['ww_builder_id']."',
			uploaded_date = NOW(),
			document_transmittal_id = '".$_REQUEST['documentTransmittalID']."'";
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
		$_SESSION['drDataArr'][] = $drawingattribute3;
		$_SESSION['drDataArr'][] = $pdfRegID;
		$_SESSION['finalDrDataArr'][] = $_SESSION['drDataArr'];
	//Set session for uploaded file end Here
		
	#File Upload Section
		$secondInsertQRY = "INSERT INTO drawing_register_revision_module_one SET
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

		if(!is_dir('./project_drawing_register_v1/'))	@mkdir('./project_drawing_register_v1/', 0777);	
		if(!is_dir('./project_drawing_register_v1/'.$_SESSION['idp']))	@mkdir('./project_drawing_register_v1/'.$_SESSION['idp'], 0777);

		move_uploaded_file($_FILES['file']['tmp_name'], "project_drawing_register_v1/".$_SESSION['idp']."/".$imageName);
		
	#File Upload Section
		$updateQRY = "UPDATE drawing_register_module_one SET pdf_name = '".$imageName."', last_modified_date = NOW() WHERE id = '".$pdfRegID."'";
		mysql_query($updateQRY);
		$secUpdateQRY = "UPDATE drawing_register_revision_module_one SET pdf_name = '".$imageName."', last_modified_date = NOW() WHERE id = '".$imageid."'";
		mysql_query($secUpdateQRY);
		
		$recodArr['pdf_name'] = $imageName;
		}
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
	
		# Start:- Prepare data for email
		foreach($_SESSION['finalDrDataArr'] as $drawingDat){
			$emailData = serialize(array("docRegId" =>$drawingDat[6], "docNumber" =>$drawingDat[0], "docTitle" =>$drawingDat[1], 'docRevision' =>$drawingDat[2], 'attribute1' =>$drawingDat[3], 'attribute2' =>str_replace('###', ', ', $drawingDat[4]), 'status' => $pdfStatus)); 		
			
			$instQuery = "INSERT INTO cron_for_pending_emails SET
				primary_key = '".$drawingDat[6]."',
				foreign_key = '0',
				project_id = '".$_SESSION['idp']."',
				section_name = 'new_document_added',
				data = '".$emailData."',
				is_new_record = '1',
				created_date = NOW(),
				created_by = '".$_SESSION['ww_builder_id']."'";
			mysql_query($instQuery);
		}
		# Start:- Prepare data for email	
	
	/*//Report Generation Start Here
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
		#print_r($_SESSION['finalDrDataArr']);die;
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
//		$mail->AddReplyTo('WiseworkingSystems@wiseworking.com.au', "Wiseworker");		
//		$mail->AddReplyTo('administrator@defectid.com', 'defectid.com');
		//$mail->Subject = $subject;
		$mail->Subject = "wiseworker:".$messgeId."-".$subject;
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
		$aUrl = "attachment/".$name;
		$msg .= 'Document Uploaded By : '.$_SESSION['ww_builder_full_name'].'<br/><br/>';
		$msg .= "Attachments url : <a href='http://" . $_SERVER['SERVER_NAME'].'/'.$aUrl."'>Click here</a><br>";
		
		$msg .= '<br /><br/>
		<br/>Please click below to access your message.<br><a href="'.$path.'/pms.php?sect=message_details&id='.base64_encode($threadId).'&type=inbox&projID='.$_SESSION['idp'].'&byEmail='.$byEmail.'" target="_blank">'.$path.'/pms.php?sect=message_details&id='.base64_encode($threadId).'&type=inbox&projID='.$_SESSION['idp'].'&byEmail='.$byEmail.'</a><br /><br /><br />Thanks,<br><br />';
		
		
		
				
		$msg .= $companyDetailsData[0]['trading_name'].'<br />
				E: '.$companyDetailsData[0]['comp_email'].'<br />
				P: '.$companyDetailsData[0]['comp_mobile'].'<br />
				A: '.$companyDetailsData[0]['comp_businessadd1'].', '.$companyDetailsData[0]['comp_businessadd2'].', '.$companyDetailsData[0]['comp_bussuburb'].', '.$companyDetailsData[0]['comp_businessstate'].', '.$companyDetailsData[0]['comp_businesscountry'].'<br />
				W: '.$companyDetailsData[0]['website'].'<br />';
		$content = file_get_contents('./email_template.php');
		$content = str_replace('domain_name',"Wiseworking" ,$content);
		$content = str_replace('noti_heading',"wiseworker:".$messgeId."-".$subject,$content);
		$content = str_replace('image_content','<img style="border: 0;-ms-interpolation-mode: bicubic;display: block;max-width:388px" alt="" width="259" height="77" src="'.$path.'/images/logo.png"  />',$content);
		$content = str_replace('noti_content',$msg ,$content);
		
		$msg1 = 'Hello,<br />
			You have '.sizeof($_SESSION['finalDrDataArr']).' new Document (s) added in '.$projectName.' Project <br />';
		foreach($_SESSION['finalDrDataArr'] as $drawingDat){
			$msg1 .= 'Document Number : '.$drawingDat[0].'<br />
				Drawing Revision : '.$drawingDat[2].'<br/>
				Attribute 1 : '.$drawingDat[3].'<br/>
				Attribute 2 : '.str_replace('###', ', ', $drawingDat[4]).'<br/>
				Status : '.$pdfStatus.'<br/><br/>';
		}
		$aUrl = "attachment/".$name;
		$msg1 .= 'Document Uploaded By : '.$_SESSION['ww_builder_full_name'].'<br/><br/>';
		
		$msg1 .= '<br /><br/>
		<br/>Please click below to access your message.<br><a href="'.$path.'/pms.php?sect=message_details&id='.base64_encode($threadId).'&type=inbox&projID='.$_SESSION['idp'].'&byEmail='.$byEmail.'" target="_blank">'.$path.'/pms.php?sect=message_details&id='.base64_encode($threadId).'&type=inbox&projID='.$_SESSION['idp'].'&byEmail='.$byEmail.'</a><br /><br /><br />Thanks,<br><br />';
				
		$msg1 .= $companyDetailsData[0]['trading_name'].'<br />
				E: '.$companyDetailsData[0]['comp_email'].'<br />
				P: '.$companyDetailsData[0]['comp_mobile'].'<br />
				A: '.$companyDetailsData[0]['comp_businessadd1'].', '.$companyDetailsData[0]['comp_businessadd2'].', '.$companyDetailsData[0]['comp_bussuburb'].', '.$companyDetailsData[0]['comp_businessstate'].', '.$companyDetailsData[0]['comp_businesscountry'].'<br />
				W: '.$companyDetailsData[0]['website'].'<br />';
		$content1 = file_get_contents('./email_template.php');
		$content1 = str_replace('domain_name',"Wiseworking" ,$content1);
		$content1 = str_replace('noti_heading',"wiseworker:".$messgeId."-".$subject,$content1);
		$content1 = str_replace('image_content','<img style="border: 0;-ms-interpolation-mode: bicubic;display: block;max-width:388px" alt="" width="259" height="77" src="'.$path.'/images/logo.png"  />',$content1);
		$content1 = str_replace('noti_content',$msg1 ,$content1);
		
		$msg1 = $content1;
		//$mail->MsgHTML($msg);			
		//$mail->AddAttachment("attachment/".$name);  
		foreach($emailUserList as $key=>$userEmailArrNew){
			if($userEmailArrNew['attachment_noti_type']==1){
				$mail->MsgHTML($msg);			
			}else{
				$mail->MsgHTML($msg1);	
				$mail->AddAttachment("attachment/".$name); 		
			}
			#$mail->AddAddress($userEmailArrNew['user_email'], $userEmailArrNew['full_name']); // To
		//if($_SESSION['idp']!=242){
/*if(!$mail->Send()) {
	$nogood = true;
	$fstatus[] = "Mailer Error: " . $mail->ErrorInfo.$userEmailArrNew['user_email'];
	//saving email status if email is not sent
	//Date: 14 april 2015
	$obj->save_email_status('drawing_register', $userEmailArrNew['user_email'],'', '', $msg, 0, $_SESSION['idp'], "WiseWorker:".$messgeId."-".$subject, "attachment/".$name);
} else {
	#$status = "Thank you! Your message has been sent to 20th Century Fox. Submit another?<br>";
	$sstatus[] =$userEmailArrNew['user_email']; 
	$obj->save_email_status('drawing_register', $userEmailArrNew['user_email'],'', '', $msg, 1, $_SESSION['idp'], "WiseWorker:".$messgeId."-".$subject, "attachment/".$name); 
}*/
		//}
		/*	$mail->ClearAddresses();
			$mail->ClearAllRecipients( );
		}*/
		
		/*$mail->AddAddress("sharma.gaurav@fxbytes.com", "Gaurav Sharma"); 
		$mail->Send();
		$mail->ClearAddresses();
		$mail->ClearAllRecipients( );*/
		
	//Send Mail End Here
	/*	if($threadId!=0){
			$updateQRY = "update drawing_register_module_one SET pmb_thread_id = '".$threadId."'
			WHERE project_id = '".$_SESSION['idp']."' AND id='".$pdfRegID."'";
			mysql_query($updateQRY);
		}	*/
	//Email and Message Board Entry End Here
		unset($_SESSION['drRequsetCount']);
		unset($_SESSION['finalDrDataArr']);
	
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
					is_document_transmittal = 1,
					created_by = '".$_SESSION['ww_builder_id']."'";
				mysql_query($inssertQRY);
				$pdfRegIDNew = mysql_insert_id(); 
			}else{
				$pdfRegIDNew = $docTransData[0]['id']; 
			}
			#File Upload Section
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
		$outputArr = array('data'=>$_REQUEST,'status'=> true, 'msg'=> 'Drawing Register Added Successfully!', 'msg'=> 'Drawing Register Added Successfully !');	
		echo json_encode($outputArr);
	}
//Send message here
}

if(isset($_REQUEST['superAntiqueID'])){ #print_r($_REQUEST);die;
	$nameArray = $_POST['groupFilNameArr'];
	$numberArray = $_POST['groupFileNumberArr'];
	
	$DrawingRgisDatas = $obj->selQRYMultiple('mdr.id, mdr.project_id, mdr.title, mdr.pdf_name, mdr.number, mdr.revision', 'drawing_register_module_one as mdr', 'title IN ('.$nameArray.') AND number IN('.$numberArray.') AND mdr.is_deleted = 0 AND mdr.project_id = '.$_SESSION['idp'].' ORDER BY id DESC');
		$notFileID = array();
		foreach($DrawingRgisDatas as $DrRgisDatas){
			$notFileID[] = $DrRgisDatas['id'];
		}
		$valuesDrawingAjax = array();
		$datas = array();
		$nextRev = '';
		# print_r($DrawingRgisDatas);die;
		foreach ($DrawingRgisDatas as $DrawingRgisDatasVal){
			$nextRev = $DrawingRgisDatasVal['revision']+1;
			if($nextRev < 10){
				if(strlen($DrawingRgisDatasVal['revision'])){
					$rev =  '0' . $nextRev;
				}else{
					$rev = $nextRev;
				}
			}else{
				$rev = $nextRev;
			}
			$DrawingRgisDatasVal['newRevision'] = $rev;
			$valuesDrawingAjax[$DrawingRgisDatasVal['number'].'###'.$DrawingRgisDatasVal['title']] = $DrawingRgisDatasVal;
		}
		#print_r($datas);
		/*if($_REQUEST['type']==1){
			echo json_encode(array('data'=>$valuesDrawingAjax, 'newRev'=>$rev));
		}else{
			echo json_encode(array('data'=>$valuesDrawingAjax, 'newRev'=>$rev));
		}	*/
		#print_r($valuesDrawingAjax);
		if(!empty($existingNumberArr))
		$outputArr = array('status' => true, 'msg' => 'Document Number already exist', 'data' => $valuesDrawingAjax, 'newRev' => $rev, 'dataArr' => join(', ', $existingNumberArr));
	else
		$outputArr = array('status' => true, 'msg' => 'Document Number already exist', 'data' => $valuesDrawingAjax, 'newRev' => $rev);
		
	
	echo json_encode($outputArr);	
}

if(isset($_REQUEST["name"])){?>
	<style type="text/css">
		#innerModalPopupDiv{
			resize: both;
			overflow: auto;
		}
	</style>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Bulk Upload</legend>
		<form name="addDrawingForm" id="addDrawingForm" style="max-height:540px;">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top" align="left">Attribute 1 <span class="req">*</span></td>
				<td align="left" rowspan="2" valign="top">
	                <div style="float: right; margin-right: 20px;">
	                    <b>Documents Format</b>
                        <div style="overflow: auto; width: 235px; margin-top: 5px; border: 1px solid rgb(204, 204, 204); padding: 5px; height: 81px; background: rgb(204, 204, 204) none repeat scroll 0% 0%;">
                            Doc Title-Rev01 <br>
                            Doc Title_Rev01 <br>
                            Doc Title-Doc Number-Rev01 <br>
                            Doc Title_Doc Number_Rev01 <br>
                            Doc Title_Doc Number[Rev-01] <br>
                            Doc Title-Doc Number[Rev-01] <br>
                            Doc Title_Doc Number(Rev-01) <br>
                            Doc Title-Doc Number(Rev-01) <br>
                            Doc Title_Doc Number{Rev-01} <br>
                            Doc Title-Doc Number{Rev-01}
                    	</div>
                    </div>
					<select name="drawingattribute1" id="drawingattribute1" class="select_box" style="margin-left:0px;"  />
				<?php $attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Civil / Landscaping', 'ESD / Green Star', 'Survey', 'Shop Drawings');
				if($_SESSION['idp']==242){
					$attribute1Arr = array('Architectural', 'Structural', 'Mechanical', 'Civil', 'Electrical', 'Hydraulics', 'Fire Services', 'Landscaping', 'Specifications, schedules and reports', 'Models', 'Shop Drawings');
				}
				if($_SESSION['idp']==243){
					$attribute1Arr = array('Architectural', 'Structural', 'Mechanical', 'Civil', 'Electrical', 'Hydraulics', 'Fire Services', 'Landscaping', 'Specifications, schedules and reports');
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
				<td valign="bottom" align="left">Drawing / Document Files<span class="req">*</span></td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="innerDiv" id="innerDiv" style="height:230px;width:910px;overflow:auto;">
						<div align="center" style="font-size:12px;">Drop Files Here</div>
					</div>
					<input type="file" name="multiUpload" id="multiUpload" multiple="multiple" />
					<lable for="multiUpload" id="errorMultiUpload" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing PDF field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Status</td>
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
			<!--<tr>
				<td valign="top" colspan="2">
					<a href="javascript:selectUserNotification('emailUserList');">Select users to send notifications to</a>
					<input type="hidden" name="emailUserList" id="emailUserList" value='' />
				</td>
			</tr>-->
			<tr>
				<td colspan="2" align="center">
					<ul class="buttonHolder">
						<li>
							<input type="submit" name="button" class="green_small" id="buttonFirstSubmit"/>

							<input type="hidden" name="validationFlag" id="validationFlag" value="2" />
                            <input type="hidden" name="documentTransmittalID" id="documentTransmittalID" value="" />
							<input type="hidden" name="existingDcTransID" id="existingDcTransID" value="<?=$drawingData[0]["document_transmittal_id"];?>" />
                            <input type="hidden" name="drAttr" id="drAttr" value="" />
						</li>
						<!-- <li>
							<!-- <img src="images/doccument_transmittal.png" style="border:none; width:111px;height:43px;" onclick="addNewRegisterDocumentTransmital();" />
							<a class="green_small" href="javascript:void(0)" onclick="addNewRegisterDocumentTransmital();" style="cursor:pointer;"  alt="doccument transmittal" />Doccument Transmittal</a>
						</li> -->
						<li>
							<!-- <a id="ancor" href="javascript:closePopup(300);">
								<img src="images/back_btn.png" style="border:none; width:111px;" />
							</a> -->
							<a class="green_small" href="javascript:closePopup(300);" style="cursor:pointer;" alt="back" />Back</a>
						</li>
					</ul>
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>
