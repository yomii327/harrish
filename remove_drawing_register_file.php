<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

session_start();
if(isset($_POST["imageName"])){
	$filePath = explode('/', $_POST['imageName']);
	$fileName = $filePath[sizeof($filePath) - 1];
	$folderName = $filePath[sizeof($filePath) - 2];
	unlink('./project_drawing_register/'.$_SESSION['idp'].'/'.$folderName.'/'.$fileName);
		
	$fileOName = str_replace('thumb_', '', $fileName);
	unlink('./project_drawing_register/'.$_SESSION['idp'].'/'.$fileOName);
	
	$_SESSION['isRemoveDraw'] = 'Y';
	$_SESSION['imgID'] = $_POST["imgID"];;
#	print_r($_SESSION);
}

if(isset($_POST["name"])){
	if(isset($_POST['fileType'])){
		$pdfFiles = $obj->selQRYMultiple('id, pdf_name', 'drawing_register_revision', 'project_id = "'.$_SESSION['idp'].'" AND is_deleted = 0 AND id = '.$_POST["fileType"]);		

		$filePath = 'project_drawing_register/'.$_SESSION['idp'].'/'.$pdfFiles[0]['pdf_name'];

		$delete = "UPDATE drawing_register_revision SET is_deleted = 1, last_modified_date = NOW() WHERE id = '".$_POST["fileType"]."'";
		mysql_query($delete);	
		
		unlink($filePath);
		
		$newVersionName = $obj->selQRYMultiple('pdf_name, revision_number', 'drawing_register_revision', 'project_id = "'.$_SESSION['idp'].'" AND is_deleted = 0 AND drawing_register_id = '.$_POST["tableID"].' ORDER BY id DESC LIMIT 0, 1');		
		
		$updateQRY = "UPDATE drawing_register SET pdf_name = '".$newVersionName[0]['pdf_name']."', revision = '".$newVersionName[0]['revision_number']."', last_modified_date = NOW() WHERE id = ".$_POST["tableID"];

		mysql_query($updateQRY);	

	}else{
		$delete = "UPDATE drawing_register SET is_deleted = 1, last_modified_date = NOW() WHERE id = '".$_POST["tableID"]."'";
		mysql_query($delete);	
		$pdfFiles = $obj->selQRYMultiple('id, pdf_name', 'drawing_register_revision', 'project_id = "'.$_SESSION['idp'].'" AND is_deleted = 0 AND drawing_register_id = '.$_POST["tableID"]);
		
		$deleteSecond = "UPDATE drawing_register_revision SET is_deleted = 1, last_modified_date = NOW() WHERE drawing_register_id = '".$_POST["tableID"]."'";
		mysql_query($deleteSecond);	
		foreach($pdfFiles as $pFiles){
			$filePath = 'project_drawing_register/'.$_SESSION['idp'].'/'.$pFiles['pdf_name'];
			unlink($filePath);
		}
	}
}
?>