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
//	$delete = "UPDATE draw_mgmt_images SET is_deleted = 1 WHERE draw_mgmt_images_id = '".$_POST["imageID"]."'";
//	mysql_query($delete);
	$filePath = explode('/', $_POST['imageData']);
	$fileName = $filePath[sizeof($filePath) - 1];
	$folderName = $filePath[sizeof($filePath) - 2];
	$fileOName = str_replace('thumb_', '', $fileName);
	$ext = explode('.', $fileName);
	$newFileName = $_SESSION['idp'].'_'.date_timestamp_get(new DateTime()).'_'.$_SESSION['idp'].date_timestamp_get(new DateTime()).'.png';
#	$newFileName = $_SESSION['idp'].'_'.substr(microtime(), -6, -1).'_'.substr(microtime(), -10, -1).'.png'; //.$ext[1];
	$oldPath='./project_drawings/'.$_SESSION['idp'].'/'.$fileOName;
	$newPath='./inspections/drawing/'.$newFileName;
	copy($oldPath, $newPath);
	echo $newFileName;
	//echo $output = '<img src="inspections/drawing/'.$newFileName.'" width="100" height="90" style="margin-left:10px;margin-top:8px;"  /><input type="hidden" name="drawing[]" value="'.$newFileName.'" />';
	//unlink('./project_drawings/'.$_SESSION['idp'].'/'.$folderName.'/'.$fileName);
	
		

//	unlink('./project_drawings/'.$_SESSION['idp'].'/'.$fileOName);
	
}
?>