<?php
session_start();
include('../includes/commanfunction.php');
$obj = new COMMAN_Class(); 

$maxNumber = 0;
$RS = mysql_query('SELECT markup_id FROM drawing_register_markups order by markup_id desc limit 0, 1');
if(mysql_num_rows($RS) > 0){
	while($ROW = mysql_fetch_assoc($RS)){
		$maxNumber =  $ROW['markup_id'];
	}
}
$maxNumber++;
// requires php5
//define('UPLOAD_DIR', '../attachment/');
define('UPLOAD_DIR', '../project_drawing_register_v1/markups/');
$img = $_POST['imgBase64'];
$img = str_replace('data:image/png;base64,', '', $img);
$img = str_replace(' ', '+', $img);
$data = base64_decode($img);
//$file = UPLOAD_DIR . uniqid() . '.png';
if(!empty($_POST['fileName'])){
	$file = $_POST['fileName']."_".$maxNumber.'.png';
}else{
	$file = uniqid() . '.png';	
}
$success = file_put_contents(UPLOAD_DIR.$file, $data);
if(file_exists(UPLOAD_DIR.$file) && $_POST['actionType'] == "sendCompose"){//Check if file exists or not
	if(copy(UPLOAD_DIR.$file, '../attachment/'.$file)){
		$_SESSION[$_SESSION['idp'].'_emailfile'] = array();
		$_SESSION[$_SESSION['idp'].'_orignalFileName'] = array();
	
		$_SESSION[$_SESSION['idp'].'_emailfile'][] = $file;
		$_SESSION[$_SESSION['idp'].'_orignalFileName'][] = $file;
	}
}
if($success){
	$insert_query = "INSERT INTO drawing_register_markups SET
		drawing_register_id	 = '".$_POST['drawingRegisterId']."',
		project_id = '".$_SESSION['idp']."',
		title = '".addslashes(trim($_POST['markupTitle']."_".$maxNumber))."',																		
		img_name = '".$file."',
		last_modified_date = NOW(),
		original_modified_date = NOW(),
		last_modified_by = '".$userId."',
		created_date = NOW(),
		created_by = '".$userId."'";
	mysql_query($insert_query);
}
echo $success ? UPLOAD_DIR.$file : 'Unable to save the file.';


?>