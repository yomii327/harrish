<?php session_start();

$builder_id=$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

if(isset($_POST["name"])){
	$query = "UPDATE estimate_pmb_address_book SET is_deleted = 1 WHERE id = ".$_POST["userid"];
	mysql_query($query);
	if(mysql_affected_rows() > 0){
		$outputArr = array('status'=> true, 'msg'=> 'User Delete Successfully !');
	}else{
		$outputArr = array('status'=> false, 'msg'=> 'User Not Deleted !');
	}
	echo json_encode($outputArr);
}?>