<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();

$pro_id=$_GET['pro_id'];

if($_SESSION['ww_is_builder']==1){

	$builder_id = $_SESSION['ww_builder_id'];
	
	$q = "SELECT resp_id,resp_full_name FROM ".RESPONSIBLES." r 
		  JOIN ".PROJECTS." p ON r.project_id = p.project_id 
	      WHERE p.project_id = '$pro_id'";
	  
	// get all responsibles
	$r=mysql_query($q);
	
	if(mysql_num_rows($r)>0){
		$repairer_name = "<option value=''>Select</option>";
		
		while($f=mysql_fetch_assoc($r)){
			$repairer_name.="<option value='".$f['resp_id']."'>".stripslashes($f['resp_full_name'])."</option>";
		}
		echo "<select class='select_box' id='repairer_name' name='repairer_name'>".$repairer_name."</select>";
	}else{
		echo "<input type='text' id='repairer_name' name='repairer_name' value='' class='input_small' readonly='readonly' />";
	}

}elseif($_SESSION['ww_is_builder']==2){

	$resp_id = $_SESSION['ww_resp_id'];

	$q = "SELECT assign_id,assign_full_name FROM ".ASSIGN."
		  WHERE resp_id = '$resp_id'" ;
	  
	// get all assign to
	$r=mysql_query($q);
	
	if(($pro_id!='Select') && (mysql_num_rows($r)>0) ){
		$assign_to_name = "<option value=''>Select</option>";
		
		while($f=mysql_fetch_assoc($r)){
			$assign_to_name.="<option value='".$f['assign_id']."'>".stripslashes($f['assign_full_name'])."</option>";
		}
		echo "<select class='select_box' id='assign_to_name' name='assign_to_name'>".$assign_to_name."</select>";
	}else{
		echo "<input type='text' id='assign_to_name' name='assign_to_name' value='' class='input_small' readonly='readonly' />";
	}
}
?>