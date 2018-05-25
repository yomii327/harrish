<?php
session_start();
require_once'includes/functions.php';

$obj = new DB_Class();

echo 'First Select Parent Location <==> '.$select = "select distinct location_id from progress_monitoring order by location_id";

$result = mysql_query($select);

if(mysql_num_rows($result) > 0){
	while($rows = mysql_fetch_array($result)){
		$selectPloc = "select * from project_locations where location_id = '".$rows['location_id']."' order by location_id";
		$result1 = mysql_query($selectPloc);
		$row1 = mysql_fetch_array($result1);
		if(mysql_num_rows($result1) > 0){
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
			
			
				echo 'First Select Sub Location <==> '.$selectSubLoc = "select distinct sub_location_id from progress_monitoring where location_id = '".$row1['location_id']."'";
				$res = mysql_query($selectSubLoc);
				
				if(mysql_num_rows($res) > 0){
					while($rowSub = mysql_fetch_assoc($res)){
						echo $rowSub['sub_location_id'].'-------------'.$selectSubLocaQuery = "select * from project_locations where location_id = '".$rowSub['sub_location_id']."'";
						echo '<br />';
						$resSubLoc = mysql_query($selectSubLocaQuery);
						
						if(mysql_num_rows($resSubLoc) > 0){
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
						}else{
							echo 'Fail Form Sub Location Data';						
						}
					}
				}else{
					echo 'Fail Form Sub Location ';
				}
				echo	'<br /><h1>New Section </h1><br />';
		}else{
			echo 'Fail Form Parent Location Data';		
		}
	}
}else{
	echo 'Fail Form Parent Location ';
}

?>