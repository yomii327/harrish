<?php
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

session_start();
if(isset($_POST["imageName"])){
	$filePath = explode('/', $_POST['imageName']);
	$fileName = $filePath[sizeof($filePath) - 1];
	$folderName = $filePath[sizeof($filePath) - 2];
	unlink('./project_drawings/'.$_SESSION['idp'].'/'.$folderName.'/'.$fileName);
		
	$fileOName = str_replace('thumb_', '', $fileName);
	unlink('./project_drawings/'.$_SESSION['idp'].'/'.$fileOName);
	
	$_SESSION['isRemoveDraw'] = 'Y';
	$_SESSION['imgID'] = $_POST["imgID"];;
	print_r($_SESSION);
}

if(isset($_POST["imageData"])){
	$delete = "UPDATE draw_mgmt_images SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE draw_mgmt_images_id = '".$_POST["imageID"]."'";
	mysql_query($delete);
	$filePath = explode('/', $_POST['imageData']);
	$fileName = $filePath[sizeof($filePath) - 1];
	$folderName = $filePath[sizeof($filePath) - 2];
	unlink('./project_drawings/'.$_SESSION['idp'].'/'.$folderName.'/'.$fileName);
		
	$fileOName = str_replace('thumb_', '', $fileName);
	unlink('./project_drawings/'.$_SESSION['idp'].'/'.$fileOName);
	
}
?>