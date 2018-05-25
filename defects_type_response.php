<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();

$pro_id=$_GET['pro_id'];

$query="SELECT dl.dl_id,dl.dl_title FROM ".DEFECTSLIST." dl 
					LEFT JOIN ".PROJECTDEFECTS." pd ON dl.dl_id=pd.fk_dl_id 
					WHERE fk_p_id='$pro_id'";	
//echo $query; die;
$q=$obj->db_query($query);

if($obj->db_num_rows($q)>0){

	$defects_type = "<option value=''>Select</option>";
					
	while($f=$obj->db_fetch_assoc($q)){
		$defects_type.="<option value='".$f['dl_id']."'>".stripslashes($f['dl_title'])."</option>";
	}
	echo "<select class='select_box' id='defect_type' name='defect_type'>".$defects_type."</select>";
}else{
	echo "<input type='text' id='defect_type' name='defect_type' value='' class='input_small' readonly='readonly' />";
}
?>