<?php
session_start();

include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

#define('SOURCEPATH', '/var/www/fxdev/project_drawing_register/'.$_SESSION['idp']);
define('SOURCEPATH', './project_drawing_register/'.$_SESSION['idp']);
#define('DESTINATIONPATH', '/var/www/fxdev/attachment');
define('DESTINATIONPATH', './attachment');

if(isset($_POST['uniqueId'])){
	$attchArr = explode(',', $_POST['fileIds']);
	$attachFileArr = array();
	foreach($attchArr as $key=>$value){
		$fileNameArr = explode('###', $value);
		$originalFilName = end($fileNameArr);
		$ext = end(explode('.', $fileNameArr[0]));
		$newFileName = $_SESSION['idp'].'_'.rand().'.'.$ext;
		$attachFileArr[] = base64_encode($newFileName);

		copy(SOURCEPATH.'/'.$fileNameArr[0], DESTINATIONPATH.'/'.$newFileName);
		
		$_SESSION[$_SESSION['idp'].'_emailfile'][] = $newFileName;
		$_SESSION[$_SESSION['idp'].'_orignalFileName'][] = $originalFilName;
	}
#	print_r($_SESSION[$_SESSION['idp'].'_emailfile']);print_r($_SESSION[$_SESSION['idp'].'_orignalFileName']);die;
	$outputArr = array('status'=> true, 'msg'=> 'Attachment added successfully', 'dataArr'=> $attachFileArr);
	echo json_encode($outputArr);
}

if(isset($_POST['singleId'])){
	if($_SESSION[$_SESSION['idp'].'_emailfile'] == $_POST['fileName']){
		$deleteQRY = "UPDATE pmb_attachments SET is_deleted = 1 WHERE attach_id = ".$_POST['messageID'];
		mysql_query($deleteQRY);
		unset($_SESSION[$_SESSION['idp'].'_emailfile']);
	}
	$outputArr = array('status'=> true, 'msg'=> 'Attachment added successfully', 'dataArr'=> $attachFileArr);
	echo json_encode($outputArr);
}

if(isset($_REQUEST['antiqueId'])){
	$_SESSION[$_SESSION['idp'].'_remaimberData'] = $_POST;
	$outputArr = array('status'=> true, 'msg'=> 'Remeber Data successfully ', 'dataArr'=> $_SESSION[$_SESSION['idp'].'_remaimberData']);
	echo json_encode($outputArr);
}?>