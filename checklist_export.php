<?php error_reporting(1);
session_start();
require_once'includes/functions.php';
$object= new DB_Class();
function echocsv($fields){
	$op = '';
	$separator = '';
	foreach ($fields as $field){
		if(preg_match('/\\r|\\n|,|"/', $field)){
			$field = '"' . str_replace( '"', '""', $field ) . '"';
		}
		$op .= $separator . $field;
		$separator = ',';
	}
	$op .= "\r\n";
	return $op;
}
	$fileName = 'Checklist_Tamplate'.microtime().'.csv';
	header("Content-type: application/csv");
	header("Content-Disposition: attachment; filename = Checklist_Export.csv");
	header("Pragma: no-cache");
	header("Expires: 0");	

	$query="select  max(Tags) as TotalTag from (select *, LENGTH(replace(check_list_items_tags,';','f+')) - LENGTH(check_list_items_tags) as Tags from check_list_items where project_id=".$_SESSION['idp']." and is_deleted=0 ) as SubQueryAlias ORDER BY `SubQueryAlias`.`Tags`  ASC";
	$result=mysql_query($query);
	$max_tag=mysql_fetch_array($result);
	$count= $max_tag['TotalTag']; 
	
	$standard_query="SELECT check_list_items_name, check_list_items_tags, issued_to, checklist_type, holding_point FROM check_list_items where project_id = ".$_SESSION['idp']." and is_deleted = 0";
	
	$export=mysql_query($standard_query);
	$header = array('Checklist Name', 'Issue To', 'Checklist Type', 'Hold Point');
	for ($i = 0; $i <$count; $i++){
		array_push($header , 'Tag '.($i+1));
	}
	$output .= echocsv($header);
	
	while($row = mysql_fetch_assoc($export)){
		$rowNew = array();
		$rowNew[] = $row['check_list_items_name'];
		$rowNew[] = $row['issued_to'];
		$rowNew[] = $row['checklist_type'];
		$rowNew[] = $row['holding_point'];
		$noTag = explode(';', $row['check_list_items_tags']);
		for($g=0; $g<$count; $g++){
			array_push($rowNew, $noTag[$g]);
		}
		$output .= echocsv($rowNew);
	}

	print "$output";?> 