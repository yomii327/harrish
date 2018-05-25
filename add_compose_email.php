<?php session_start();
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

if(isset($_REQUEST["antiqueID"])){#echo "<pre>"; print_r($_REQUEST);print_r($_FILES);
	// read email in from stdin

#echo $_FILES['file']['tmp_name'].'/'.$_FILES['file']['name'];

#$fd = fopen("mimeparser/test/sample/a.eml", "r");


/*$filename = $_FILES['file']['name']; // Drawing File Name
	$recodArr['status'] = $pdfStatus = trim(addslashes($_POST['pdfStatus']));
	$fNameArr = explode('.', $filename);

	$file_ext = array_pop($fNameArr);
if(!is_dir('./pmb_attachment/'))	@mkdir('./pmb_attachment/', 0777);
		if(!is_dir('./pmb_attachment/'.$_SESSION['idp']))	@mkdir('./pmb_attachment/'.$_SESSION['idp'], 0777);
$imageName = mktime().'_'.$filename;
 move_uploaded_file($_FILES['file']['tmp_name'], './pmb_attachment/'.$_SESSION['idp'].'/'.$imageName);	
*/
#print_r($_REQUEST);die;
if($_REQUEST['external_correspondance']==1 && !empty($_REQUEST['external_correspondance']) && isset($_REQUEST['external_correspondance'])){
$fd = fopen($_FILES['file']['tmp_name'], "r");	
$email = "";
while (!feof($fd)) {
    $email .= fread($fd, 1024);
}
fclose($fd);

//create the email parser class
$mime=new mime_parser_class;
$mime->ignore_syntax_errors = 1;
$parameters=array(
	'Data'=>$email,
);
	
$mime->Decode($parameters, $decoded);
#echo "<pre>";print_r($decoded);
//get the name and email of the sender
$fromName = $decoded[0]['ExtractedAddresses']['from:'][0]['name'];
$fromEmail = $decoded[0]['ExtractedAddresses']['from:'][0]['address'];

//get the name and email of the recipient
$toEmail = $decoded[0]['ExtractedAddresses']['to:'][0]['address'];
$toName = $decoded[0]['ExtractedAddresses']['to:'][0]['name'];

//get the subject
$subject = $decoded[0]['Headers']['subject:'];

$removeChars = array('<','>');

//get the message id
$messageID = str_replace($removeChars,'',$decoded[0]['Headers']['message-id:']);

//get the reply id
$replyToID = str_replace($removeChars,'',$decoded[0]['Headers']['in-reply-to:']);


//---------------------- FIND THE BODY -----------------------//

//get the message body
if(substr($decoded[0]['Headers']['content-type:'],0,strlen('text/plain')) == 'text/plain' && isset($decoded[0]['Body'])){
	
	$body = $decoded[0]['Body'];

} elseif(substr($decoded[0]['Parts'][0]['Headers']['content-type:'],0,strlen('text/plain')) == 'text/plain' && isset($decoded[0]['Parts'][0]['Body'])) {
	
	$body = $decoded[0]['Parts'][0]['Body'];

} elseif(substr($decoded[0]['Parts'][0]['Parts'][0]['Headers']['content-type:'],0,strlen('text/plain')) == 'text/plain' && isset($decoded[0]['Parts'][0]['Parts'][0]['Body'])) {
	
	$body = $decoded[0]['Parts'][0]['Parts'][0]['Body'];

}else if(isset($decoded[0]['Body'])){
	$body = $decoded[0]['Body'];
}

//print out our data
#echo "
#Subject: $subject

#To: $toName $toEmail

#Body: $body

#";
//uploading file code here
$filename = $_FILES['file']['name']; // Drawing File Name
	$recodArr['status'] = $pdfStatus = trim(addslashes($_POST['pdfStatus']));
	$fNameArr = explode('.', $filename);

	$file_ext = array_pop($fNameArr);
if(!is_dir('./pmb_attachment/'))	@mkdir('./pmb_attachment/', 0777);
		if(!is_dir('./pmb_attachment/'.$_SESSION['idp']))	@mkdir('./pmb_attachment/'.$_SESSION['idp'], 0777);
$imageName = mktime().'_'.$filename;
 move_uploaded_file($_FILES['file']['tmp_name'], './pmb_attachment/'.$_SESSION['idp'].'/'.$imageName);	
$arrFiles[] = $imageName;
 $file = '"'.trim($imageName).'"';
$fileId = rand(1,199); 
$aBody = "<div id='div_$fileId'><a id='$fileId' class='thickbox' style='color:#06C;' target='_blank' href='pmb_attachment/".$_SESSION['idp']."/".$imageName."'>".$imageName."</a>[ <a id='remove_$fileId' style='color:red;'  onclick='removeFile(1, 0, $file, $fileId);'>X</a>]</div>";
#$aBody = "<a>$imageName</a>[ <a onclick='removeMaessage(1, 0, $file);'>X</a>]";
#$aBody = "<a href='pmb_attachments/".$_SESSION['idp']."/".$imageName."'"
$htmlBody = "<input type='hidden' name='filesArr[]' value='".$imageName."' id='filesArr_".$fileId."' class='fileArrclass'>";
$outputArr = array('status'=>true,'subject'=>$subject, 'to'=>$toEmail, 'body'=>$body,'anchor'=>$aBody, 'fileArr'=>$htmlBody);
}else{
$filename = $_FILES['file']['name']; // Drawing File Name
	$recodArr['status'] = $pdfStatus = trim(addslashes($_POST['pdfStatus']));
	$fNameArr = explode('.', $filename);

	$file_ext = array_pop($fNameArr);
if(!is_dir('./pmb_attachment/'))	@mkdir('./pmb_attachment/', 0777);
		if(!is_dir('./pmb_attachment/'.$_SESSION['idp']))	@mkdir('./pmb_attachment/'.$_SESSION['idp'], 0777);
$imageName = mktime().'_'.$filename;
 move_uploaded_file($_FILES['file']['tmp_name'], './pmb_attachment/'.$_SESSION['idp'].'/'.$imageName);
$file = '"'.trim($imageName).'"';
$fileId = rand(1,199); 		
$aBody = "<span id='div_$fileId'><a id='$fileId' class='thickbox' style='color:#06C;' target='_blank' href='pmb_attachment/".$_SESSION['idp']."/".$imageName."'>".$imageName."</a>[ <a style='color:red;' onclick='removeFile(1, 0, $file, $fileId);'>X</a>]</span>";
$arrFiles[] = $imageName;
$htmlBody = "<input type='hidden' name='filesArr[]' value='".join(',',$arrFiles)."' id='filesArr_".$fileId."'  class='fileArrclass'>";
$outputArr = array('status'=>false,'msg'=>'Please select external correspondance for adding email file content to compose section.','anchor'=>$aBody, 'fileArr'=>$htmlBody);	
}
echo json_encode($outputArr); 
die;
}
if(isset($_REQUEST["name"])){?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Bulk Upload Drawing PDFs</legend>
		<form name="addDrawingForm" id="addDrawingForm">
		<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
			<tr>
				<td valign="top"  colspan="2" align="left">Email&nbsp;File&nbsp;<span class="req">*</span></td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="innerDiv" id="innerDiv" style="height:300px;width:890px;overflow:auto;">
						<div align="center" style="font-size:12px;">Drop EML or MSG Files Here</div>
					</div>
					<input type="file" name="multiUpload" id="multiUpload" />
					<lable for="multiUpload" id="errorMultiUpload" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Drawing PDF field is required</div></lable>
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
							<input type="submit" name="button" class="submit_btn" id="buttonFirstSubmit" style="background-image:url(images/upload_btn.png);font-size:0px; border:none; width:111px;float:left;" />
							<input type="hidden" name="validationFlag" id="validationFlag" value="2" />
                            <input type="hidden" name="documentTransmittalID" id="documentTransmittalID" value="" />
							<input type="hidden" name="existingDcTransID" id="existingDcTransID" value="<?=$drawingData[0]["document_transmittal_id"];?>" />
                            <input type="hidden" name="drAttr" id="drAttr" value="" />
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
