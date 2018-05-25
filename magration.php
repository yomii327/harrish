<?php
session_start();
require_once'includes/functions.php';

$obj = new DB_Class();


$select = "select distinct location_id from progress_monitoring order by location_id";


$result = mysql_query($select);


while($rows = mysql_fetch_array($result)){
 	$selectPloc = "select * from project_locations where location_id = '".$rows['location_id']."' order by location_id";
	$result1 = mysql_query($selectPloc);
	$row1 = mysql_fetch_array($result1);
	
echo 'Pareent Location Query => '. 	$insert = 'insert into project_monitoring_locations set
					location_id = "'.$row1['location_id'].'", 
					project_id = "'.$row1['project_id'].'",
					location_parent_id = "'.$row1['location_parent_id'].'",
					location_title = "'.$row1['location_title'].'",
					last_modified_date = "'.$row1['last_modified_date'].'",
					last_modified_by = "'.$row1['last_modified_by'].'",
					created_by = "'.$row1['created_by'].'",
					created_date = "'.$row1['created_date'].'",
					resource_type = "'.$row1['resource_type'].'",
					is_deleted = "'.$row1['is_deleted'].'"';
echo '<br /><br /><br /><br />';
	mysql_query($insert);


	$selectSubLoc = "select distinct sub_location_id from progress_monitoring where location_id = '".$row1['location_id']."'";
	$res = mysql_query($selectSubLoc);
	
	while($rowSub = mysql_fetch_assoc($res)){
		
		$selectSubLocaQuery = "select * from project_locations where location_id = '".$rowSub['sub_location_id']."'";
		
		$resSubLoc = mysql_query($selectSubLocaQuery);
		$rowSubLoc = mysql_fetch_array($resSubLoc);
		
echo	'SubLocation Query =>'.	$insertSubLoc = 'insert into project_monitoring_locations set
					location_id = "'.$rowSubLoc['location_id'].'", 
					project_id = "'.$rowSubLoc['project_id'].'",
					
					location_parent_id = "'.$row1['location_id'].'",
					
					location_title = "'.$rowSubLoc['location_title'].'",
					last_modified_date = "'.$rowSubLoc['last_modified_date'].'",
					last_modified_by = "'.$rowSubLoc['last_modified_by'].'",
					created_by = "'.$rowSubLoc['created_by'].'",
					created_date = "'.$rowSubLoc['created_date'].'",
					resource_type = "'.$rowSubLoc['resource_type'].'",
					is_deleted = "'.$rowSubLoc['is_deleted'].'"';
					
echo	'<br /><br />';
			mysql_query($insertSubLoc);
	}
	echo	'<br /><h1>New Section </h1><br />';
}

?>