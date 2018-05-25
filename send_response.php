<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();

$user=$_GET['user'];

$key=$_GET['key'];
$field=$_GET['field'];
$type=$_GET['type'];

if($user=='builder'){
	$builder_id = $_SESSION['ww_builder_id'];
	$proId=$_GET['proId'];
		
	if($type=='responsible'){
		$q = "SELECT * FROM ".RESPONSIBLES." WHERE resp_id = '$key' ";  
	
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$f=$obj->db_fetch_assoc($obj->db_query($q));
			if($field=='ph'){
				echo "<input id='repairer_ph' name='repairer_ph' type='text' class='input_small' value=".stripslashes($f['resp_phone'])." readonly='readonly' />";
			}elseif($field=='email'){
				echo "<input id='repairer_email' name='repairer_email' type='text' class='input_small' value=".stripslashes($f['resp_email'])." readonly='readonly' />";
			}elseif($field=='comp'){
	echo "<input id='repairer_comp' name='repairer_comp' type='text' class='input_small' value=".str_replace(' ','&nbsp;',stripslashes($f['resp_comp_name']))." readonly='readonly' />";
			}
			
		}else{
			if($field=='ph'){
				echo "<input id='repairer_ph' name='repairer_ph' type='text' class='input_small' readonly='readonly' />";
			}elseif($field=='email'){
				echo "<input id='repairer_email' name='repairer_email' type='text' class='input_small' readonly='readonly' />";
			}elseif($field=='comp'){
				echo "<input id='repairer_comp' name='repairer_comp' type='text' class='input_small' readonly='readonly' />";
			}
		}
	}elseif($type=='assign'){
		if($field=='name'){
			$q = "SELECT * FROM ".ASSIGN." WHERE resp_id = '$key' ";
			$r=mysql_query($q);
			if(mysql_num_rows($r)>0){
				$assign_to_name = "<option value='Select'>Select</option>";
				while($f=mysql_fetch_assoc($r)){
					$assign_to_name.="<option value='".$f['assign_id']."'>".stripslashes($f['assign_full_name'])."</option>";
				}
				echo "<select class='select_box' id='assign_to_id' name='assign_to_id' onchange='getAssignToInfo()'>".$assign_to_name."</select>";
			}else{
				echo "<input type='text' id='assign_to_name' name='assign_to_name' value='' class='input_small' readonly='readonly' />";
			}
		}else{
			$q = "SELECT * FROM ".ASSIGN." WHERE assign_id = '$key' ";
			$r=mysql_query($q);
			$f=mysql_fetch_assoc($r);
			if($field=='ph'){			
				echo "<input id='assign_to_ph' name='assign_to_ph' type='text' class='input_small' value=".stripslashes($f['assign_phone'])." readonly='readonly' />";
			}elseif($field=='email'){
				echo "<input id='assign_to_email' name='assign_to_email' type='text' class='input_small' value=".stripslashes($f['assign_email'])." readonly='readonly' />";
			}elseif($field=='comp'){
				echo "<input id='assign_to_comp' name='assign_to_comp' type='text' class='input_small' value=".str_replace(' ','&nbsp;',stripslashes($f['assign_comp_name']))." />";
			}		
		}
	}
}elseif($user=='responsible'){
	$resp_id = $_SESSION['ww_resp_id'];
	$q = "SELECT * FROM ".ASSIGN." WHERE assign_id = '$key' AND resp_id = '$resp_id' ";
	$r=mysql_query($q);
	$f=mysql_fetch_assoc($r);
	if($field=='ph'){			
		echo "<input id='assign_to_ph' name='assign_to_ph' type='text' class='input_small' value=".stripslashes($f['assign_phone'])." readonly='readonly' />";
	}elseif($field=='email'){
		echo "<input id='assign_to_email' name='assign_to_email' type='text' class='input_small' value=".stripslashes($f['assign_email'])." readonly='readonly' />";
	}elseif($field=='comp'){
		echo "<input id='assign_to_comp' name='assign_to_comp' type='text' class='input_small' value=".str_replace(' ','&nbsp;',stripslashes($f['assign_comp_name']))." />";
	}
}
?>