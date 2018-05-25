<?php
session_start();
require_once'includes/functions.php';

$obj = new DB_Class();

echo 'Inspection Id <==> '.$select = "select distinct inspection_id from project_inspections where project_id=".$_REQUEST["project_id"]." order by inspection_id";

$result = mysql_query($select);

if(mysql_num_rows($result) > 0){
	while($rows = mysql_fetch_array($result)){
		$selectPloc = "select inspection_status, inspection_fixed_by_date, cost_attribute from project_inspections where inspection_id = '".$rows['inspection_id']."'";
		
		$result1 = mysql_query($selectPloc);
		$row1 = mysql_fetch_array($result1);
		
		if(mysql_num_rows($result1) > 0){
			$status = "Open";
			if (! empty( $row1['inspection_status']))
			{
				$status = $row1['inspection_status'];
			}
			echo 'Pareent Location Query => '. 	$insert = 'update issued_to_for_inspections set
								inspection_fixed_by_date = "'.$row1['inspection_fixed_by_date'].'", 
								cost_attribute = "'.$row1['cost_attribute'].'",
								inspection_status = "'.$status.'" where inspection_id='.$rows['inspection_id'];
			echo '<br /><br /><br /><br />';
			mysql_query($insert);
		}else{
			echo 'Failed on inspections';
		}
	}
}
?>