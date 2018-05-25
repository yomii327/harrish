<?php
session_start();
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
$owner_id = $builder_id = $_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
require_once('includes/class.phpmailer.php');
require_once('mimeparser/rfc822_addresses.php');
require_once('mimeparser/mime_parser.php');
define('FPDF_FONTPATH',"./font/");
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

if(isset($_REQUEST["antiqueID"])) {
	$fileElementName = 'file';
	if(!empty($_FILES[$fileElementName]['error'])){
		switch($_FILES[$fileElementName]['error']){
			case '1':
				$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
				break;
			case '2':
				$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
				break;
			case '3':
				$error = 'The uploaded file was only partially uploaded';
				break;
			case '4':
				$error = 'No file was uploaded.';
				break;

			case '6':
				$error = 'Missing a temporary folder';
				break;
			case '7':
				$error = 'Failed to write file to disk';
				break;
			case '8':
				$error = 'File upload stopped by extension';
				break;
			case '999':
			default:
				$error = 'No error code avaiable.';
		}
	}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none'){
		$error = 'No file was uploaded.';
	}else{
		$ext = explode('.', $_FILES[$fileElementName]['name']);
		$name = $_SESSION['idp'].'_'.substr(microtime(), -6, -1).rand(0,99).'.'.end($ext);
		$orignalFileName = $_FILES[$fileElementName]['name'];
		$_SESSION[$_SESSION['idp'].'_emailfile'][] = $name;
		$_SESSION[$_SESSION['idp'].'_orignalFileName'][] = $_FILES[$fileElementName]['name'];
		if (move_uploaded_file($_FILES[$fileElementName]['tmp_name'], 'attachment/'.$name)) {
			@unlink($_FILES[$fileElementName]);
		}
	}
	$outputArr = array('status'=> true, 'uploadedImageName'=> $name, 'imageName'=> $orignalFileName, 'filePath'=> $name, 'msg'=> 'Attachment added successfully', 'rel' => $_POST['index']);
	echo json_encode($outputArr);
}
if(isset($_REQUEST["name"])) {
?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Multiple Attachment</legend>
		<form name="frmAddAttachment" id="frmAddAttachment" enctype="multipart/form-data">
			<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td valign="top"  colspan="2" align="left">Files&nbsp;<span class="req">*</span></td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="innerDiv" id="innerDiv" style="height:300px;width:890px;overflow:auto;">
							<div align="center" style="font-size:12px;">Drag & Drop files here...</div>
						</div>
						<input type="file" name="addAttachments" id="addAttachments" multiple style="width:94px;" />
						<input type="hidden" id="addAttachmentCount" name="addAttachmentCount" value="0" />
						<span id="fileCount">0 files selected</span>
						<lable for="multiUpload" id="errorMultiUpload" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing PDF field is required</div></lable>
					</td>
				</tr>
				
				<tr>
					<td colspan="2" align="center">
						<ul class="buttonHolder">
							<li>
								<input type="submit" name="button" class="submit_btn" id="buttonFirstSubmit" style="background-image:url(images/upload_btn.png);font-size:0px; border:none; width:111px;float:left;" />
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
