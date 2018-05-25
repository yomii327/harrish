<?php
include '../includes/property.php';
session_start();

if(isset($_REQUEST["antiqueID"])){
	if(!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])){
		$outputArr = array('status'=> false, 'msg'=> 'Now Redirect from here');
	}else{
		$outputArr = array('status'=> true, 'msg'=> 'Session alive');
	}
	echo json_encode($outputArr);
}?>