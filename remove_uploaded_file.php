<?php
if(isset($_POST["imageName"])){
	$filePath = explode('/', $_POST['imageName']);
	$fileName = $filePath[sizeof($filePath) - 1];
	$folderName = $filePath[sizeof($filePath) - 2];
	unlink('./inspections/'.$folderName.'/'.$fileName);
}?>
