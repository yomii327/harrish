<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

include 'includes/commanfunction.php';
$object = new COMMAN_Class();

if(isset($_REQUEST['uesrID'])){
	$upQRY = "UPDATE user SET is_deleted = 0 WHERE is_deleted = 1 AND user_name = '".$_REQUEST['uesrID']."'";
	mysql_query($upQRY);
	if(mysql_affected_rows() > 0)
		echo 'Done';		
	else
		echo 'Please check user name';
}?>