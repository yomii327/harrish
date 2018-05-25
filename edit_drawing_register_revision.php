<?php session_start();
$builder_id=$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
require_once('includes/class.phpmailer.php');
$obj = new COMMAN_Class(); 

define('ATTCHMENTPATH', '/var/www/constructionid.com/vccc/project_drawing_register/'.$_SESSION['idp']);
define('ATTCHMENTCOPYPATH', '/var/www/constructionid.com/vccc/project_drawing_register/'.$_SESSION['idp']);

if(isset($_REQUEST["antiqueID"])){
//Get pdfData Here
	$outDataArr = array();
	$pdfExData = $obj->selQRYMultiple('title, number, attribute1, attribute2, tag, is_approved', 'drawing_register', 'is_deleted = 0 AND id = '.$_POST['drawingID'].' AND project_id = '.$_SESSION['idp']);
	
	$recodArr['title'] = $drawingTitle = $pdfExData[0]['title'];
	$recodArr['number'] = $drawingNumber =  $pdfExData[0]['number'];
	$drawingAttribute1 = $recodArr['attribute1'] = $pdfExData[0]['attribute1'];
	$drawingAttribute2 = $recodArr['attribute2'] = $pdfExData[0]['attribute2'];
	$recodArr['tag'] = $pdfExData[0]['tag'];
	$recodArr['is_approved'] = $pdfExData[0]['is_approved'];

	$recodArr['revision'] = $drawingRevision = trim(addslashes($_POST['drawingRevision']));
	$recodArr['comments'] = $drawingNotes = trim(addslashes($_POST['drawingNotes']));
	$filename = $_FILES['file']['name']; // Drawing File Name
	$file_ext = end(explode('.', $filename));

	$recodArr['status'] = $pdfStatus = trim(addslashes($_POST['pdfStatus']));
	$attahmentMsgb = ""; 
	if(empty($_FILES)){
		$updateQRY = "UPDATE drawing_register_revision SET
				revision_number = '".$drawingRevision."',
				comments = '".$drawingNotes."',
				revision_status = '".$pdfStatus."',
				last_modified_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."'
			WHERE
				id = '".$_REQUEST['drawingRevID']."'";
		mysql_query($updateQRY);

		$recodArr['pdf_name'] = $imageName;
		
		$updateQRY = "UPDATE drawing_register SET
			revision = '".$drawingRevision."',
			comments = '".$drawingNotes."',
			status = '".$pdfStatus."',
			last_modified_date = NOW(),
			last_modified_by = '".$_SESSION['ww_builder_id']."'
		WHERE
			id = '".$_POST['drawingID']."'";
		mysql_query($updateQRY);

		$insertHistory = "INSERT INTO table_history_details SET
					primary_key = '".$imageid."',
					table_name = 'drawing_register',
					sql_operation = 'UPDATE',
					sql_query = '".serialize($recodArr)."',
					created_by = '".$_SESSION['ww_builder_id']."',
					created_date = NOW(),
					last_modified_by = '".$_SESSION['ww_builder_id']."',
					last_modified_date = NOW(),
					project_id = '".$_SESSION['idp']."'";
		mysql_query($insertHistory);	
	}else{
		if(strtolower($file_ext) == 'pdf'){
			$_SESSION['insertedDwgID'] = "";
			$_SESSION['insertedDwgRevID'] = "";
			$insertQRY = "INSERT INTO drawing_register_revision SET
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
	
			$_SESSION['insertedDwgRevID'] = $imageid = mysql_insert_id();
			$imageName = $imageid.'.'.$file_ext;
			if(!is_dir('./project_drawing_register/'.$_SESSION['idp']))
				@mkdir('./project_drawing_register/'.$_SESSION['idp'], 0777);
	
			move_uploaded_file($_FILES['file']['tmp_name'], './project_drawing_register/'.$_SESSION['idp'].'/'.$imageName);
			$updateQRY = "UPDATE drawing_register SET
						project_id = '".$_SESSION['idp']."',
						pdf_name = '".$imageName."',
						revision = '".$drawingRevision."',
						status = '".$pdfStatus."',
						comments = '".$drawingNotes."',
						last_modified_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."'
				WHERE
					id = '".$_POST['drawingID']."'";
			mysql_query($updateQRY);
	
			$_SESSION['insertedDwgID'] = $_POST['drawingID'];
	//Set Array for output data ids
			$outDataArr = array('insertedDwgID' => $_POST['drawingID'], 'insertedDwgRevID' => $imageid);
			
			$secUpdateQRY = "UPDATE drawing_register_revision SET
							pdf_name = '".$imageName."', last_modified_date = NOW()
						WHERE id = '".$imageid."'";
			mysql_query($secUpdateQRY);
			
			$secUpdateQRY = "UPDATE drawing_register_revision SET
							archieve_revision = 1, last_modified_date = NOW()
						WHERE id = '".$_REQUEST['drawingRevID']."'";
			mysql_query($secUpdateQRY);
			
			$recodArr['pdf_name'] = $imageName;
			
			$insertHistory = "INSERT INTO table_history_details SET
						primary_key = '".$imageid."',
						table_name = 'drawing_register',
						sql_operation = 'UPDATE',
						sql_query = '".serialize($recodArr)."',
						created_by = '".$_SESSION['ww_builder_id']."',
						created_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						last_modified_date = NOW(),
						project_id = '".$_SESSION['idp']."'";
			mysql_query($insertHistory);	

		//Report Generation Start Here
			$docData = array();
			$attribute1Arr = array('"General"', '"Architectural"', '"Structure"', '"Services"', '"Concrete &amp; PT"', '"Lighting"', '"Tenancy Fitout"', '"Penthouse Architecture"', '"Landscaping"');
		
			$sect = ", max(drr.id) AS revID";
			$orderBy = " ORDER BY dr.attribute1, dr.attribute2, dr.number asc";
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

			$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Revision Added Successfully !', 'dataArr'=> $outDataArr);
			echo json_encode($outputArr);
		}elseif(strtolower($file_ext) == 'dwg'){
			if(!is_dir('./project_drawing_register/'.$_SESSION['idp']))	@mkdir('./project_drawing_register/'.$_SESSION['idp'], 0777);
			$imageName = $_REQUEST['drawingRevID'].'.'.$file_ext;
			
			if(isset($_SESSION['insertedDwgRevID']) && 	$_SESSION['insertedDwgRevID'] != "")
				$imageName = $_SESSION['insertedDwgRevID'].'.'.$file_ext;
			
			move_uploaded_file($_FILES['file']['tmp_name'], "project_drawing_register/".$_SESSION['idp']."/".$imageName);
		
			$insertedDwgID = $_REQUEST['drawingID'];
			if(isset($_SESSION['insertedDwgID']) && $_SESSION['insertedDwgID'] != "")
				$insertedDwgID = $_SESSION['insertedDwgID'];
		
			$insertedDwgRevID = $_REQUEST['drawingRevID'];
			if(isset($_SESSION['insertedDwgRevID']) && 	$_SESSION['insertedDwgRevID'] != "")
				$insertedDwgRevID = $_SESSION['insertedDwgRevID'];
		
			$updateQRY = "UPDATE drawing_register SET
							dwg_name = '".$imageName."'
						WHERE id = '".$insertedDwgID."'";
			mysql_query($updateQRY);
			$secUpdateQRY = "UPDATE drawing_register_revision SET
							dwg_name = '".$imageName."'
						WHERE id = '".$insertedDwgRevID."'";
			mysql_query($secUpdateQRY);
			
			$outDataArr = array('insertedDwgID' => $insertedDwgID, 'insertedDwgRevID' => $insertedDwgRevID);
			
			$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Revision Added Successfully !', 'requestComplete'=> 'dwg');
			echo json_encode($outputArr);
		}else{
			if(!is_dir('./project_drawing_register/'.$_SESSION['idp']))	@mkdir('./project_drawing_register/'.$_SESSION['idp'], 0777);
			$imageName = $_REQUEST['drawingRevID'].'.'.$file_ext;
			
			if(isset($_SESSION['insertedDwgRevID']) && 	$_SESSION['insertedDwgRevID'] != "")
				$imageName = $_SESSION['insertedDwgRevID'].'.'.$file_ext;
			
			move_uploaded_file($_FILES['file']['tmp_name'], "project_drawing_register/".$_SESSION['idp']."/".$imageName);
		
			$insertedDwgID = $_REQUEST['drawingID'];
			if(isset($_SESSION['insertedDwgID']) && $_SESSION['insertedDwgID'] != "")
				$insertedDwgID = $_SESSION['insertedDwgID'];
		
			$insertedDwgRevID = $_REQUEST['drawingRevID'];
			if(isset($_SESSION['insertedDwgRevID']) && 	$_SESSION['insertedDwgRevID'] != "")
				$insertedDwgRevID = $_SESSION['insertedDwgRevID'];

			
			$updateQRY = "UPDATE drawing_register SET
							img_name = '".$imageName."'
						WHERE id = '".$insertedDwgID."'";
			mysql_query($updateQRY);
			$secUpdateQRY = "UPDATE drawing_register_revision SET
							img_name = '".$imageName."'
						WHERE id = '".$insertedDwgRevID."'";
			mysql_query($secUpdateQRY);
			
			unset($_SESSION['insertedDwgID']);
			unset($_SESSION['insertedDwgRevID']);

			$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Added Successfully !');
			//echo json_encode($outputArr);
		}
	}
	/*
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
	$subject = 'Document '.$drawingTitle.' updated in project '.$projectName;
	//$messageType = 'Document Transmittal';
	$messageType = 'Document Transmittal';
	
	$messageDetails = 'Hello,<br />
		A document has been updated on '.$projectName.' Project <br />
		Document Number : '.$drawingNumber.'<br />
		Drawing Revision : '.$drawingRevision.'<br/>
		Attribute 1 : '.$drawingAttribute1.'<br/>
		Attribute 2 : '.str_replace('###', ', ', $drawingAttribute2).'<br/>
		Status : '.$pdfStatus.'<br/><br/><br />
		Document Uploaded By : '.$_SESSION['ww_builder_full_name'].'<br/><br/><br /><br />
		Thanks,<br>Wiseworker customer care';

	if(get_magic_quotes_gpc()){	$messageDetails = stripslashes($messageDetails);	}
	$messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));
	
	$messgeId = 0;
	$getThreadId = $obj->selQRYMultiple('pmb_thread_id', 'drawing_register', 'is_deleted = 0 AND id = "'.$_POST['drawingID'].'"'); 
 	$threadId = isset($getThreadId[0]['pmb_thread_id'])?$getThreadId[0]['pmb_thread_id']:0;
		
	foreach($userEmailArr[$_SESSION['idp']] as $userEmailArrNew){
		$messageBoardReturn = $obj->messageBoard($_SESSION['idp'], $_SESSION['ww_builder_id'], $userEmailArrNew['user_id'], $subject, $messageType, $messageDetails, $attahmentMsgb, $messgeId, '', '', '', 0, $threadId);
		$attahmentMsgb = "";
		$messgeId = $messageBoardReturn['messgeId'];

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
			$mail->AddReplyTo('administrator@defectid.com', 'defectid.com');
			$mail->Subject = $subject;
			$mail->IsHTML(true);
			
			$msg = 'Hello,<br />
				A document has been updated on '.$projectName.' Project <br />
					Document Number : '.$drawingNumber.'<br />
					Drawing Revision : '.$drawingRevision.'<br/>
					Attribute 1 : '.$drawingAttribute1.'<br/>
					Attribute 2 : '.str_replace('###', ', ', $drawingAttribute2).'<br/>
					Status : '.$pdfStatus.'<br/><br/><br />
					Document Uploaded By : '.$_SESSION['ww_builder_full_name'].'<br/><br/>
					<br/>click here to access you message.<br>
					<a href="'.$path.'/pms.php?sect=message_details&id='.base64_encode($messageBoardReturn['thread_id']).'&type=inbox&projID='.base64_encode($_SESSION['idp']).'&byEmail=3" target="_blank">'.$path.'/pms.php?sect=message_details&id='.base64_encode($messageBoardReturn['thread_id']).'&type=inbox&projID='.base64_encode($_SESSION['idp']).'&byEmail=3</a><br /><br /><br />
					Thanks,<br><br />';
											
			$msg .= $companyDetailsData[0]['trading_name'].'<br />
						E: '.$companyDetailsData[0]['comp_email'].'<br />
						P: '.$companyDetailsData[0]['comp_mobile'].'<br />
						A: '.$companyDetailsData[0]['comp_businessadd1'].', '.$companyDetailsData[0]['comp_businessadd2'].', '.$companyDetailsData[0]['comp_bussuburb'].', '.$companyDetailsData[0]['comp_businessstate'].', '.$companyDetailsData[0]['comp_businesscountry'].'<br />
						W: '.$companyDetailsData[0]['website'].'<br />';

			$mail->MsgHTML($msg);			
			$mail->AddAttachment("attachment/".$name);  
			$mail->AddAddress($userEmailArrNew['user_email'], $userEmailArrNew['user_fullname']); // To
#			$mail->Send();
			$mail->ClearAddresses();
			$mail->ClearAllRecipients();
//Send Mail End Here
		#}
	}

	if($threadId!=0){
	 	$updateQRY = "UPDATE drawing_register SET pmb_thread_id = '".$threadId."'
		WHERE project_id = '".$_SESSION['idp']."' AND id='".$_POST['drawingID']."'";
		mysql_query($updateQRY);
	}	
//Email and Message Board Entry End Here
*/
	$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Revision Updated Successfully !');
	if(empty($outDataArr))
		echo json_encode($outputArr);
}
if(isset($_REQUEST["name"])){
	$drawData = $obj->selQRYMultiple('dr.id, drr.id as drawingrevID, drr.pdf_name, drr.revision_number, drr.comments, drr.revision_status', 'drawing_register_revision AS drr, drawing_register AS dr', 'dr.id = drr.drawing_register_id AND dr.is_deleted = 0 AND drr.id = '.$_REQUEST['regrevID'].' AND drr.project_id = "'.$_SESSION['idp'].'"', 'drr.project_id = "'.$_SESSION['idp'].'" AND drr.is_deleted = 0 ORDER BY drr.id'); ?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Edit Drawing Register Revision</legend>
		<form name="addDrawingForm" id="addDrawingForm" enctype="multipart/form-data">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top" align="left">Drawing&nbsp;PDF <span class="req">*</span></td>
				<td align="left" width="40%">
					<div class="innerDivDrager" id="innerDivDrager">
						<div>Drop File Here</div>
						<div class="innerDiv" id="innerDiv1" align="center">
							<span>PDF file upload here</span>
						</div>
						<div class="innerDiv" id="innerDiv2" align="center" style="margin-left:7px;">
							<span>DWG file upload here</span>
						</div>
						<div class="innerDiv" id="innerDiv3" align="center" style="margin-left:7px;">
							<span>Image file upload here</span>
						</div><br clear="all" />
						<input type="file" name="multiUpload" id="multiUpload" />
					</div><br />
					<lable for="multiUpload" id="errorMultiUpload" generated="true" class="error" style="display:none;margin-top:10px;"><div class="error-edit-profile">The Drawing PDF field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Document&nbsp;Revision</td>
				<td align="left">
					<input type="text" name="drawingRevision" id="drawingRevision" class="input_small" value="<?=$drawData[0]['revision_number']?>" maxlength="5" />
					<lable for="drawingRevision" id="errorDrawingRevision" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing Revision field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Comments / Notes</td>
				<td align="left">
					<textarea name="drawingNotes" id="drawingNotes" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"><?=$drawData[0]['comments']?></textarea>
					<input type="hidden" name="drawingID" id="drawingID" value="<?=$drawData[0]['id'];?>" />
					<input type="hidden" name="drawingRevID" id="drawingRevID" value="<?=$drawData[0]['drawingrevID'];?>" />
					<input type="hidden" name="validationFlag" id="validationFlag" value="3" />
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Status <span class="req">*</span></td>
				<td align="left">
					<?php $revStatusArr = array('Tender', 'Issued for Construction', 'For Information');?>
					<select name="pdfStatus" id="pdfStatusDyna" class="select_box" style="margin-left:0px;"  />
						<?php foreach($revStatusArr as $key=>$revStatus){?>
							<option value="<?=$revStatus?>" <?php if($drawData[0]['revision_status'] == $revStatus){ echo 'selected="selected"';}?>><?=$revStatus?></option>
						<?php }?>
					</select>
					<lable for="pdfStatus" id="errorPdfStatus" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Status field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="button" class="submit_btn" id="button" style="background-image:url(images/save.png);font-size:0px; border:none; width:111px;float:left;" />
					&nbsp;&nbsp;&nbsp; <a id="ancor" href="javascript:void(0);" onclick="showRevisions(<?=$_REQUEST['regID']?>);"> <img src="images/back_btn.png" style="border:none; width:111px;" /> </a>
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>