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
$companyDetailsData = $obj->selQRYMultiple('trading_name, comp_email, comp_mobile, CONCAT_WS(", ", NULLIF(comp_businessadd1, ""), NULLIF(comp_businessadd2, ""), NULLIF(comp_bussuburb, ""), NULLIF(comp_businessstate, ""), NULLIF(comp_businesspost, ""), NULLIF(comp_businesscountry, "")) AS companyAddress, website', 'pms_companies', 'active = 1');


if(isset($_REQUEST["antiqueID"])){ 
#print_r($_REQUEST);print_r($_FILES);die;
	#$emailUserList = unserialize($_REQUEST['emailUserList']);//Array for send emails
	$emailUserList = $obj->getAddressBook();
	//$ruleUserList = $obj->getRuleUsers('New Document Added');
	
	#print_r($emailUserList);die;
	$recodArr = array();//Store record to create history table
	$filename = $_FILES['file']['name']; // Drawing File Name
	$fileArr = explode('.', $filename);
	$file_ext = array_pop($fileArr);
	$defaultDrawingNumber = implode('.', $fileArr);

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
	$recodArr['title'] = $drawingTitle = trim(addslashes($_POST['drawingTitle']));
	$drawingNumber = trim(addslashes($_POST['drawingNumber']));
	$recodArr['revision'] = $drawingRevision = trim(addslashes($_POST['drawingRevision']));
	$recodArr['comments'] = $drawingNotes = trim(addslashes($_POST['drawingNotes']));
	$recodArr['attribute1'] = $drawingAttribute1 = trim(addslashes($_POST['drawingattribute1']));
	$recodArr['attribute2'] = $drawingAttribute2 = trim(addslashes($_POST['drawingattribute2']));
	$recodArr['drawingAttribute3'] = $drawingAttribute3;
	$recodArr['un_approve_reson'] = $unApproveReson = trim(addslashes($_REQUEST['unApproveReson']));
	$recodArr['status'] = $pdfStatus = trim(addslashes($_POST['pdfStatus']));
//Update DT Section Start Here
	if($drawingAttribute1 != $_POST['drAttr']){
		$qry = "UPDATE drawing_register_module_one SET attribute1 = '".$drawingAttribute1."' WHERE is_document_transmittal = 1 AND id = ".$_POST['documentTransmittalID'];
		mysql_query($qry);
	}
	
//Update DT Section Start Here
	$tag = trim(addslashes($_POST['tag']));
	$tag = trim($tag, ";");
	$tag = implode(";", array_map('trim', explode(";", $tag)));
	if($tag != ""){ $tag = trim($tag).";"; }
	$recodArr['tag'] = $tag;
	$approved = 0;
	$approvedEdit = 0;
	if(isset($_POST['approved'])){ $approved = 1; }
	if(isset($_POST['approved_edit'])){ $approvedEdit = $_POST['approved_edit']; }
	if($drawingNumber == ""){$drawingNumber = $defaultDrawingNumber;}
	$recodArr['is_approved'] = $approved;
	$recodArr['is_approved_edit'] = $approvedEdit;
	$recodArr['number'] = $drawingNumber;
	$projectName = $obj->getDataByKey('projects', 'project_id', $_SESSION['idp'], 'project_name');
	$drawingNumberData = $obj->selQRYMultiple('id, title, number, revision, status','drawing_register_module_one','number = "'.$drawingNumber.'" AND title = "'.$drawingTitle.'" AND is_deleted = 0 AND project_id = '.$_SESSION['idp'].'');
		//condition for supersedded
		//print_r($drawingNumberData);die;
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
		$pdfRegID = $drawingNumberData[0]['id'];
		$secUpdateQRY = "UPDATE drawing_register_revision_module_one SET pdf_name = '".$imageName."', last_modified_date = NOW() WHERE id = '".$imageid."'";
		mysql_query($secUpdateQRY);
		
		$outDataArr = array('insertedDwgID' => $drawingNumberData[0]['id'], 'insertedDwgRevID' => $imageid);
				
		//$outputArr = array('data'=>$_REQUEST,'status'=> true, 'msg'=> 'Drawing Register Revision Added Successfully!', 'msg'=> 'Drawing Register Revision Added Successfully !','sStatus'=>$sstatus,'fStatus'=>$fstatus);	
		//echo json_encode($outputArr);
	}else{
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
			is_approved_edit = '".$approvedEdit."',
			last_modified_date = NOW(),
			last_modified_by = '".$_SESSION['ww_builder_id']."',
			created_date = NOW(),
			uploaded_date = NOW(),
			created_by = '".$_SESSION['ww_builder_id']."',
			document_transmittal_id = '".$_REQUEST['documentTransmittalID']."',
			un_approve_reson = '".$unApproveReson."'";
		mysql_query($inssertQRY);
		$_SESSION['insertedDwgID'] = $pdfRegID = mysql_insert_id(); 
		
		$secondInsertQRY = "INSERT INTO drawing_register_revision_module_one SET
			project_id = '".$_SESSION['idp']."',
			drawing_register_id = '".$pdfRegID."',
			comments = '".$drawingNotes."',
			revision_number = '".$drawingRevision."',
			revision_status = '".$pdfStatus."',
			last_modified_date = NOW(),
			last_modified_by = '".$_SESSION['ww_builder_id']."',
			created_date = NOW(),
			created_by = '".$_SESSION['ww_builder_id']."'";  
		mysql_query($secondInsertQRY);
		
		$_SESSION['insertedDwgRevID'] = $imageid = mysql_insert_id();
		$outDataArr = array('insertedDwgID' => $pdfRegID, 'insertedDwgRevID' => $imageid);
		$imageName = $imageid.'.'.$file_ext;
		
		if(!is_dir('./project_drawing_register_v1/'))	@mkdir('./project_drawing_register_v1/', 0777);
		if(!is_dir('./project_drawing_register_v1/'.$_SESSION['idp']))	@mkdir('./project_drawing_register_v1/'.$_SESSION['idp'], 0777);
		move_uploaded_file($_FILES['file']['tmp_name'], "project_drawing_register_v1/".$_SESSION['idp']."/".$imageName);
		
		$updateQRY = "UPDATE drawing_register_module_one SET pdf_name = '".$imageName."', last_modified_date = NOW() WHERE id = '".$pdfRegID."'";
		mysql_query($updateQRY);
		$secUpdateQRY = "UPDATE drawing_register_revision_module_one SET pdf_name = '".$imageName."', last_modified_date = NOW() WHERE id = '".$imageid."'";
		mysql_query($secUpdateQRY);
		$recodArr['pdf_name'] = $imageName;
	
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
	
# Start:- Prepare data for email
	$emailData = serialize(array("docRegId" =>$pdfRegID, "docNumber" =>$drawingNumber, "docTitle" =>$drawingTitle, 'docRevision' =>$drawingRevision, 'attribute1' =>$drawingAttribute1, 'attribute2' =>str_replace('###', ', ', $drawingAttribute2), 'status' => $pdfStatus)); 		
	
	$instQuery = "INSERT INTO cron_for_pending_emails SET
		primary_key = '".$pdfRegID."',
		foreign_key = '0',
		project_id = '".$_SESSION['idp']."',
		section_name = 'new_document_added',
		data = '".$emailData."',
		is_new_record = '1',
		created_date = NOW(),
		created_by = '".$_SESSION['ww_builder_id']."'";
	mysql_query($instQuery);
# Start:- Prepare data for email

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
	
	$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Added Successfully !', 'dataArr'=> $outDataArr, 'fstatus'=>$fstatus, 'sstatus'=>$sstatus);
	echo json_encode($outputArr);
	//Email and Message Board Entry End Here
}

if(isset($_REQUEST['superAntiqueID'])){ #print_r($_REQUEST);die;
	$nameArray = $_POST['groupFilNameArr'];
	$numberArray = $_POST['groupFileNumberArr'];
	
$DrawingRgisDatas = $obj->selQRYMultiple('mdr.id, mdr.project_id, mdr.title, mdr.pdf_name, mdr.number, mdr.revision', 'drawing_register_module_one as mdr', 'title IN ('.$nameArray.') AND number IN('.$numberArray.') AND mdr.is_deleted = 0 AND mdr.project_id = '.$_SESSION['idp'].' ORDER BY id DESC', 'YU');
		$notFileID = array();
		foreach($DrawingRgisDatas as $DrRgisDatas){
			$notFileID[] = $DrRgisDatas['id'];
		}
		$valuesDrawingAjax = array();
		$datas = array();
		$nextRev = '';
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
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Add Document Register</legend>
		<form name="addDrawingForm" id="addDrawingForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="0">
			<tr>
				<td valign="top" align="left">Drawing / Document File<span class="req">*</span></td>
				<td align="left" width="40%">
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
					<div class="innerDivDrager" id="innerDivDrager">
						<div class="innerDiv" id="innerDiv1" align="center">Drop File Here</div>
						<br clear="all" />
						<input type="file" name="multiUpload" id="multiUpload" />
					</div><br />
					<lable for="multiUpload" id="errorMultiUpload" generated="true" class="error" style="display:none;margin-top:10px;"><div class="error-edit-profile">The Drawing PDF field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<table width="100%">
						<tr>
							<td valign="top" align="left">Document&nbsp;Title&nbsp;<span class="req">*</span></td>
							<td align="left">
								<textarea name="drawingTitle" id="drawingTitle" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"></textarea>
								<lable for="multiUpload" id="errorDrawingTitle" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Document Description field is required</div></lable>
								<lable for="multiUpload" id="errorDrawingTitle1" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Document Description and Document Number can't be same.</div></lable>
							</td>
							<td valign="top" align="left">Document&nbsp;Number&nbsp;<span class="req">*</span></td>
							<td align="left">
								<input type="text" name="drawingNumber" id="drawingNumber" class="input_small" value="" />
								<lable for="multiUpload" id="errorDrawingNumber" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Document Number field is required</div></lable>
							</td>
						</tr>
						<tr>
							<td valign="top" align="left">Document&nbsp;Revision </td>
							<td align="left">
								<input type="text" name="drawingRevision" id="drawingRevision" class="input_small" value="" maxlength="5" />
								<lable for="drawingRevision" id="errorDrawingRevision" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing Revision field is required</div></lable>
							</td>
							<td valign="top" align="left">Comments / Notes</td>
							<td align="left">
								<textarea name="drawingNotes" id="drawingNotes" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"></textarea>
							</td>
						</tr>
						<tr>
							<td valign="top" align="left">Attribute 1 <span class="req">*</span></td>
							<td align="left">
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
							<td valign="top" align="left">Attribute 2<br />
					<span style="font-size:10px;font-style: italic;">
						(Hold CNTL for multiple)
					</span>
					</td>
					<td align="left">
						<select name="drawingattribute2" id="drawingattribute2" class="select_box" multiple="multiple" style="margin-left:0px;width:311px;height:60px;background-image:url(images/texarea_select_box_small.png);" />
							<option value="">Select</option>
							<?php $attribute1Arr1 = array('Drawings', 'Specifications', 'Reports &amp; Schedules', '3D images', 'Marketing', 'Brief and overview', 'Document Transmittal');
							
								if($_SESSION['userRole'] == 'Architect'){
									
									$attribute1Arr1 = array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Document Transmittal');
									
									if($_SESSION['architectAttrTwo'] != 'ALL')
										$attribute1Arr1 = array($_SESSION['architectAttrTwo']);
										
								}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Structural') || ($_SESSION['userRole'] == 'Structural Engineer')){
									
									$attribute1Arr1 = array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Civil', 'Document Transmittal', 'Site Inspection');
									
								}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Services') || ($_SESSION['userRole'] == 'Services Engineer')){
									
									$attribute1Arr1 = array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Mechanical', 'Electrical', 'Hydraulic', 'Fire', 'Services specifications', 'Document Transmittal');
									
								}
								if($_SESSION['userRole'] == 'Lighting Consultant')
									$attribute1Arr1 = array('Drawings', 'Specification', 'Document Transmittal');
									
								if($_SESSION['userRole'] == 'Tenancy Fitout')
									$attribute1Arr1 = array('Drawings', 'Specification', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos', 'Document Transmittal');
									
								if($_SESSION['userRole'] == 'Penthouse Architecture')
									$attribute1Arr1 = array('Drawings', 'Specification', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos', 'Document Transmittal');
									
								if($_SESSION['userRole'] == 'Landscaping')
									$attribute1Arr1 = array('Drawings', 'Specification', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos', 'Document Transmittal');
									
								for($i=0;$i<sizeof($attribute1Arr1);$i++){?>
								<option value="<?=$attribute1Arr1[$i]?>"><?=$attribute1Arr1[$i]?></option>
							<?php }?>
						</select>
					</td>
						</tr>
						<tr>
							<td valign="top" align="left">Attribute 3</td>
							<td align="left">
								<select name="drawingattribute3" id="drawingattribute3" class="select_box" style="margin-left:0px;"  />
									<?php $attribute3Arr = array('PDF', 'DWG', 'CAD','XLS','XLSX','DOC','DOCX');
										for($i=0;$i<sizeof($attribute3Arr);$i++){?>
										<option value="<?=$attribute3Arr[$i]?>" <? if($attribute3Arr[$i] == 'PDF')echo 'selected="selected"';?> ><?=$attribute3Arr[$i]?></option>
									<?php }?>
								</select>
							</td>
							<td valign="top" align="left">Tag</td>
							<td align="left">
								<input type="text" name="tag" id="tag" class="input_small" value=""  />
							</td>	
						</tr>
						
					</table>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Status <span class="req">*</span></td>
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
			<?php $approveArr = array('General Consultant', 'Architect');
					if(!in_array($_SESSION['userRole'], $approveArr)){?>
			<tr style=" <?php echo ($_SESSION['ww_builder']['user_type']=="manager")?"":"display:none;"; ?>">
				<td valign="top" align="left">Download on iPad</td>
				<td align="left">
					<input type="checkbox" name="approved" id="approved" value="1"  />
				</td>
			</tr>
			<?php }?>
			<tr style=" <?php echo ($_SESSION['ww_builder']['user_type']=="manager") ? '' : 'display:none;';?>">
				<td valign="top" align="left">Approved</td>
				<td align="left">
					<input type="radio" name="approved_edit" id="approved_editYes" value="1"  />
					<label for="approved_editYes">&nbsp;Yes</label>
					<input type="radio" name="approved_edit" id="approved_editNo" value="2"  />
                    <label for="approved_editNo">&nbsp;No</label>
					<input type="radio" name="approved_edit" id="approved_editNA" value="0" checked="checked" />
					<label for="approved_editNA">&nbsp;NA</label>
				</td>
			</tr>
            <tr id="unApproveResonHolder" style="display:none;">
				<td valign="top" align="left">Reason</td>
				<td align="left">
                	<textarea class="text_area_small" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;" rows="2" cols="25" id="unApproveReson" name="unApproveReson"></textarea>
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
							<input type="submit" name="button" class="green_small" id="buttonFirstSubmit" style="float:left;" />
							<input type="hidden" name="validationFlag" id="validationFlag" value="2" />
							<input type="hidden" name="documentTransmittalID" id="documentTransmittalID" value="" />
							<input type="hidden" name="existingDcTransID" id="existingDcTransID" value="" />
                            <input type="hidden" name="drAttr" id="drAttr" value="" />
						</li>
						<!-- <li>
							<!--<img src="images/doccument_transmittal.png" style="border:none; width:111px;height:43px;" onclick="addNewRegisterDocumentTransmital();" />
							<a class="green_small" href="javascript:void(0)" onclick="addNewRegisterDocumentTransmital();" style="cursor:pointer;"  alt="doccument transmittal" />Doccument Transmittal</a>
						</li> -->
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
