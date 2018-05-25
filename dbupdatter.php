<?php include_once 'includes/commanfunction.php';
$object= new COMMAN_Class();
$dbTables = array('fxdev', 'defectid', 'hacer', 'defectid_prebuild', 'pellicano', 'constructionid', 'constructionid_equiset', 'defectid_hamiltonmarino');
for($i=0; $i<sizeof($dbTables); $i++){
	$result = mysql_query("SHOW TABLES FROM ".$dbTables[$i]);
	echo '<h1>Database name =>'.$dbTables[$i].'</h1>'; 
	while ($row = mysql_fetch_row($result)) {
		$result4 = mysql_query("DESCRIBE $row[0]");
		echo '<h5>Table Name =>'.$row[0].'</h5>';
		while ($row4 = mysql_fetch_row($result4)) {
			if($row4[0] == 'last_modified_date' && $row4[5] != 'on update CURRENT_TIMESTAMP'){
#				echo '<pre>';print_r($row4).'<br />';
				echo "ALTER TABLE  ".$row[0]." CHANGE  `last_modified_date`  `last_modified_date` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP".'<br />';
			}
		}
	}
}?>