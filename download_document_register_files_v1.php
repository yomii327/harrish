<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

#define('SOURCEPATH', '/var/www/fxdev/project_drawing_register_v1/'.$_SESSION['idp']);
define('SOURCEPATH', "./project_drawing_register_v1/".$_SESSION['idp']);

define('DESTINATIONPATH', "./document_register_download/".$_SESSION['ww_builder_id']."/document_register/");
define('EXPORTFILEPATH', "./document_register_download/".$_SESSION['ww_builder_id']);

define('ATTACHSOURCEPATH', "./attachment");
define('ATTACHDESTINATIONPATH', "./attachment_download/".$_SESSION['ww_builder_id']."/documents/");
define('ATTACHEXPORTFILEPATH', "./attachment_download/".$_SESSION['ww_builder_id']);

if(isset($_REQUEST['uniqueId'])){
//Default folder creatation start here
	if(!is_dir("./document_register_download")){
		mkdir("./document_register_download", 0777);
	}
	if(!is_dir("./document_register_download/".$_SESSION['ww_builder_id'])){
		mkdir("./document_register_download/".$_SESSION['ww_builder_id'], 0777);
	}
//Default folder creatation end here
	if(is_dir(DESTINATIONPATH)){//Remov previous Data
		$obj->recursive_remove_directory(DESTINATIONPATH);
	}
	
	mkdir(DESTINATIONPATH, 0777);
	$fieldIds = explode(",", $_REQUEST['fileIds']);
	
	$drawingRegData = $obj->selQRYMultiple('drr.id, dr.number, dr.title, dr.pdf_name', 'drawing_register_module_one AS dr, drawing_register_revision_module_one AS drr', 'drr.id IN ('.$_REQUEST['fileIds'].') AND dr.id = drr.drawing_register_id AND dr.is_deleted = 0 AND drr.is_deleted = 0');

	foreach($drawingRegData as $drRegData){
		$fileExt = end(explode('.', $drRegData['pdf_name']));
		$newFileName = substr(str_replace( array('\\', '/', '"', ':', '*', '?', '<', '>', '|'), ' ', $drRegData['number'].'-'.$drRegData['title']), 0, 150);
		$newFileName = preg_replace('/\s+/', ' ', $newFileName);
		$newFileName = str_replace("\r\n", " ", $newFileName);
		$newFileName = str_replace("\n", " ", $newFileName);
		$newFileName = str_replace("\r", " ", $newFileName);
		$newFileName = str_replace('\r\n', " ", $newFileName);
		$newFileName = str_replace('\n', " ", $newFileName);
		$newFileName = str_replace('\r', " ", $newFileName);
		copy(SOURCEPATH.'/'.$drRegData['pdf_name'], DESTINATIONPATH.'/'.$newFileName.'.'.$fileExt);
	}
	
	$zipFileName = $_SESSION['ww_builder_id'].'document_register'.rand();
	$obj->compress(DESTINATIONPATH, $zipFileName, EXPORTFILEPATH);
	//Copy file here for updated name
	$customName = 'document download '.date('Y-m-d').'.zip';
	if(isset($_REQUEST['customName']) && !empty($_REQUEST['customName'])){
		$customName = $_REQUEST['customName'].'.zip';
	}
	$filename = $zipFileName.'.zip';
		
	copy(EXPORTFILEPATH.'/'.$filename, EXPORTFILEPATH.'/'.$customName);
/*
	header("Content-type: application/zip;\n");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize(EXPORTFILEPATH.'/'.$customName)."\n");
	header("Content-Disposition: attachment; filename=".$customName);
	ob_end_flush();
	@readfile(EXPORTFILEPATH.'/'.$customName);
*/
#	echo '<br clear="all" /><div style="margin-left:10px;"><a onClick="closePopup(300);" href="'.EXPORTFILEPATH.'/'.$customName.'" target="_blank" class="download_btn"></a></div>';
	$outputArr = array('status'=> true, 'url'=> EXPORTFILEPATH.'/'.$customName);
	echo json_encode($outputArr);
}

if(isset($_REQUEST['singleId'])){
//Default folder creatation start here
	if(!is_dir("./attachment_download")){
		mkdir("./attachment_download", 0777);
	}
	if(!is_dir("./attachment_download/".$_SESSION['ww_builder_id'])){
		mkdir("./attachment_download/".$_SESSION['ww_builder_id'], 0777);
	}
//Default folder creatation end here
	if(is_dir(ATTACHDESTINATIONPATH)){//Remov previous Data
		$obj->recursive_remove_directory(ATTACHDESTINATIONPATH);
	}
	
	mkdir(ATTACHDESTINATIONPATH, 0777);
	$attachemnts = array();
	$attachemnts = $obj->selQRYMultiple('attach_id, name, attachment_name', 'pmb_attachments', 'message_id="'.$_REQUEST['messageID'].'" AND is_deleted=0');
	if(!empty($attachemnts)){
		foreach($attachemnts as $attach){
			copy(ATTACHSOURCEPATH.'/'.$attach['attachment_name'], ATTACHDESTINATIONPATH.'/'.$attach['attachment_name']);
		}
	}
	
	$zipFileName = $_REQUEST['messageTitle'].rand();
	$obj->compress(ATTACHDESTINATIONPATH, $zipFileName, ATTACHEXPORTFILEPATH);
	
	$filename = $zipFileName.'.zip';

	echo '<br clear="all" /><div style="margin-left:10px;"><a onClick="closePopup(300);" href="'.ATTACHEXPORTFILEPATH.'/'.$filename.'" target="_blank" class="download_btn"></a></div>';
	
	$outputArr = array('status'=> true, 'msg'=> 'Attachment added successfully');
//	echo json_encode($outputArr);
}

if(isset($_REQUEST['antiqueId'])){
//Default folder creatation start here
	if(!is_dir("./document_register_single")){
		mkdir("./document_register_single", 0777);
	}
	if(!is_dir("./document_register_single/".$_SESSION['ww_builder_id'])){
		mkdir("./document_register_single/".$_SESSION['ww_builder_id'], 0777);
	}
	
	$drawingRegData = $obj->selQRYMultiple('drr.id, dr.number, dr.title, dr.pdf_name, dr.dwg_name, dr.img_name', 'drawing_register_module_one AS dr, drawing_register_revision_module_one AS drr', 'drr.id = '.$_REQUEST['fileName'].' AND dr.id = drr.drawing_register_id AND dr.is_deleted = 0 AND drr.is_deleted = 0');
	$contentHeader = "";
	foreach($drawingRegData as $drRegData){
		$newFileName = substr(str_replace( array('\\', '/', '"', ':', '*', '?', '<', '>', '|'), ' ', $drRegData['number'].'-'.$drRegData['title']), 0, 150);
		$newFileName = preg_replace('/\s+/', ' ', $newFileName);
		$newFileName = str_replace("\r\n", " ", $newFileName);
		$newFileName = str_replace("\n", " ", $newFileName);
		$newFileName = str_replace("\r", " ", $newFileName);
		$newFileName = str_replace('\r\n', " ", $newFileName);
		$newFileName = str_replace('\n', " ", $newFileName);
		$newFileName = str_replace('\r', " ", $newFileName);
		switch($_REQUEST['fileType']){
			case 'DWG':
				$contentHeader = 'application/dwg';
				$fileExt = end(explode('.', $drRegData['dwg_name']));
				copy(SOURCEPATH.'/'.$drRegData['dwg_name'], './document_register_single/'.$_SESSION['ww_builder_id'].'/'.$newFileName.'.'.$fileExt);		
			break;

			case 'IMG':
				$fileExt = strtolower(end(explode('.', $drRegData['img_name'])));
				switch( $fileExt ) {
					case "gif": $contentHeader = "image/gif"; break;
					case "png": $contentHeader = "image/png"; break;
					case "jpeg":
					case "jpg": $contentHeader = "image/jpg"; break;
					default:
				}
				
				copy(SOURCEPATH.'/'.$drRegData['img_name'], './document_register_single/'.$_SESSION['ww_builder_id'].'/'.$newFileName.'.'.$fileExt);		
			break;
			
			default :
				$contentHeader = 'application/pdf';
				$fileExt = end(explode('.', $drRegData['pdf_name']));
				copy(SOURCEPATH.'/'.$drRegData['pdf_name'], './document_register_single/'.$_SESSION['ww_builder_id'].'/'.$newFileName.'.'.$fileExt);
			break;
		}
	}
	$filePath = './document_register_single/'.$_SESSION['ww_builder_id'].'/'.$newFileName.'.'.$fileExt;
	
#	$outputArr = array('status'=> true, 'url'=> $filePath);
	
#	if(!in_array(strtolower($fileExt), array('dwg', 'pdf'))){
		$outputArr = array('status'=> true);
		ob_start();
		header('Content-Description: File Transfer');
		header('Content-Type: '.$contentHeader);
		header('Content-Disposition: attachment; filename="'.basename($filePath).'"'); //<<< Note the " " surrounding the file name
		header('Content-Transfer-Encoding: binary');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filePath));
		ob_end_flush();
		readfile($filePath);
#	}
#echo json_encode($outputArr);
}

?>