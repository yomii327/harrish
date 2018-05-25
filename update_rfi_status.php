<?php session_start();

$builder_id=$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
require_once('includes/class.phpmailer.php');
$obj = new COMMAN_Class(); 

if(isset($_REQUEST["antiqueID"])){
	$RFIstatus = $_POST['RFIstatus'];
	$modifiedID = $_POST['modifiedID'];
	$updateMessageID = $_POST['msgID'];
	$messThreadQRY = 'UPDATE pmb_message SET
							rfi_status = "'.$RFIstatus.'",
							last_modified_date = NOW(),
							last_modified_by = "'.$modifiedID.'",
							rfi_closed_date = NOW()
						WHERE
				message_id = '.$updateMessageID;
	mysql_query($messThreadQRY);
	$outputArr = array('status'=> false, 'msg'=> 'Record updated failed');
	
	if(mysql_affected_rows() > 0){
		$outputArr = array('status'=> true, 'msg'=> 'Record updated Successfully ');
	}
	echo json_encode($outputArr);
}?>