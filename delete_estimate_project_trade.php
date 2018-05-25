<?php session_start();

$builder_id=$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

if(isset($_POST["name"])){
	$query = "UPDATE estimate_trade_summary SET is_deleted = 1 WHERE trade_summary_id = ".$_POST["tradeId"];
	mysql_query($query);
	if(mysql_affected_rows() > 0){
		$outputArr = array('status'=> true, 'msg'=> 'Record Delete Successfully !');
	}else{
		$outputArr = array('status'=> false, 'msg'=> 'Record Not Deleted !');
	}
	echo json_encode($outputArr);
}?>