<?php session_start();

$builder_id=$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
require_once('includes/class.phpmailer.php');
$obj = new COMMAN_Class(); 
#print_r($_SESSION);

#define('ATTCHMENTPATH', '/var/www/constructionid.com/vccc/project_drawing_register_v1/'.$_SESSION['idp']);
#define('ATTCHMENTCOPYPATH', '/var/www/constructionid.com/vccc/project_drawing_register_v1/'.$_SESSION['idp']);


if(isset($_REQUEST["antiqueID"])){
	$recodArr = array();//Store record to create history table
	$filename = $_FILES['file']['name']; // Drawing File Name
	$fileArr = explode('.', $filename);
	$file_ext = array_pop($fileArr);
	$defaultDrawingNumber = implode('.', $fileArr);

	$recodArr['title'] = $drawingTitle = trim(addslashes($_POST['drawingTitle']));
	$drawingNumber = trim(addslashes($_POST['drawingNumber']));
	$recodArr['revision'] = $drawingRevision = trim(addslashes($_POST['drawingRevision']));
	$recodArr['comments'] = $drawingNotes = trim(addslashes($_POST['drawingNotes']));
	$recodArr['attribute1'] = $drawingAttribute1 = trim(addslashes($_POST['drawingattribute1']));
	$recodArr['attribute2'] = $drawingAttribute2 = trim(addslashes($_POST['drawingattribute2']));

	$recodArr['status'] = $pdfStatus = trim(addslashes($_POST['pdfStatus']));
	
//Get DT Data Start Here
	$existingDcTransID = $_REQUEST['existingDcTransID'];	
	$docTrmData = array();
	$docTrmData = $obj->selQRYMultiple('id', 'drawing_register_module_one', 'project_id = '.$_SESSION['idp'].' AND attribute1 = "'.$drawingAttribute1.'" AND attribute2 = "Document Transmittal" AND is_deleted = 0');
	if(!empty($docTrmData)){
		$existingDcTransID = $docTrmData[0]['id'];
	}
//Get DT Data End Here

	$tag = trim(addslashes($_POST['tag']));
	$tag = trim($tag, ";");
	$tag = implode(";", array_map('trim', explode(";", $tag)));
	if($tag != ""){ $tag = trim($tag).";"; }
	$recodArr['tag'] = $tag;
	$approved = 0;
	if(isset($_POST['approved'])){ $approved = 1; }
	if($drawingNumber == ""){$drawingNumber = $defaultDrawingNumber;}
	$recodArr['is_approved'] = $approved;
	
	$recodArr['number'] = $drawingNumber;
	if($existingDcTransID == ""){
		$inssertQRY = "INSERT INTO drawing_register_module_one SET
			project_id = '".$_SESSION['idp']."',
			title = '".$drawingTitle."',
			number = '".$drawingNumber."',
			revision = '".$drawingRevision."',
			comments = '".$drawingNotes."',
			attribute1 = '".$drawingAttribute1."',
			attribute2 = '".$drawingAttribute2."',
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
	
	$imageName = $imageid.'.'.$file_ext;
#				$imageThumbName = 'thumb_'.$imageid.'.'.$file_ext;
	if(!is_dir('./project_drawing_register_v1/'))	@mkdir('./project_drawing_register_v1/', 0777);
	if(!is_dir('./project_drawing_register_v1/'.$_SESSION['idp']))	@mkdir('./project_drawing_register_v1/'.$_SESSION['idp'], 0777);

	move_uploaded_file($_FILES['file']['tmp_name'], "project_drawing_register_v1/".$_SESSION['idp']."/".$imageName);
#	move_uploaded_file($_FILES['drawingPDF']['tmp_name'], './project_drawing_register_v1/'.$_SESSION['idp'].'/'.$imageName);
	
#File Upload Section
	$updateQRY = "UPDATE drawing_register_module_one SET
					title = '".$drawingTitle."',
					number = '".$drawingNumber."',
					pdf_name = '".$imageName."', last_modified_date = NOW()
				WHERE id = '".$pdfRegID."'";
	mysql_query($updateQRY);
	$secUpdateQRY = "UPDATE drawing_register_revision_module_one SET
					pdf_name = '".$imageName."', last_modified_date = NOW()
				WHERE id = '".$imageid."'";
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
	
	$outputArr = array('status'=> true, 'msg'=> 'Drawing Registration Added Successfully !', 'dtID'=> $pdfRegID, 'dtAttr'=>$drawingAttribute1);
	echo json_encode($outputArr);
}

if(isset($_REQUEST["name"])){?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Add Document Transmittal</legend>
		<form name="addDrawingFormDocumentTransmital" id="addDrawingFormDocumentTransmital">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top" align="left" width="50%">Drawing&nbsp;PDF <span class="req">*</span></td>
				<td align="left" width="50%">
					<div class="innerDiv" id="innerDivDocumentTransmital" align="center" style="width:290px;">
						<span>Drop File Here</span>
					</div>
					<input type="file" name="multiUpload" id="multiUploadDocumentTransmital" />
					<lable for="multiUpload" id="errorMultiUploadDocumentTransmital" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing PDF field is required</div></lable>
				</td>
			</tr>
			<tr>
				<td valign="top" align="left">Drawing&nbsp;Title / Description <span class="req">*</span></td>
				<td align="left">
					<textarea name="drawingTitle" id="drawingTitleDocumentTransmital" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"></textarea>
					<lable for="multiUpload" id="errorDrawingTitle" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing Title field is required</div></lable>
                    <input type="hidden" name="existingDcTransID" id="existingDcTransID" value="<?=$_REQUEST['existDTID']?>" />
					<input type="hidden" name="drawingattribute1" id="drawingattribute1" value="<?=$_REQUEST['attr1']?>" />
					<input type="hidden" name="drawingattribute2" id="drawingattribute2" value="Document Transmittal" />
					<input type="hidden" name="pdfStatus" id="pdfStatus" value="Draft" />
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
				<ul class="buttonHolder">
					<li>
						<input type="submit" name="button" class="green_small" id="button" style="float:left;" />
						<input type="hidden" name="validationFlag" id="validationFlag" value="2" />
					</li>
					<li>
						<!-- <a id="ancor" href="javascript:closePopup_gs(300, 1);" >
							<img src="images/back_btn.png" style="border:none; width:111px;" />
						</a> -->
						<a class="green_small" href="javascript:closePopup_gs(300, 1);" style="cursor:pointer;" alt="back" />Back</a>
					</li>
				</ul>
				</td>
			</tr>
		</table>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>