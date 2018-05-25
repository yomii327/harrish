<?php
error_reporting(1);
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
	$fileName = 'Standard_defect_template_export.csv';

	header("Content-type: application/csv");
	header("Content-Disposition: attachment; filename=".$fileName);
	header("Pragma: no-cache");
	header("Expires: 0");
	
	$query = "SELECT MAX(Tags) as TotalTag FROM (SELECT *, LENGTH(replace(tag, ';', 'f+')) - LENGTH(tag) AS Tags FROM standard_defects WHERE project_id=".$_SESSION['idp']." AND is_deleted = 0) AS SubQueryAlias ORDER BY `SubQueryAlias`.`Tags` ASC";
	
	$result = mysql_query($query);
	$max_tag = mysql_fetch_array($result);
	$count = $max_tag['TotalTag']; 
	
	$standard_query = "SELECT description, tag, issued_to FROM standard_defects WHERE project_id = ".$_SESSION['idp']." AND is_deleted = 0";
	
	$export = mysql_query($standard_query);
	$header = array('Description', 'Issue To');
	for ($i=0; $i<$count; $i++){
		array_push($header , 'Tag '.($i+1));
	}
	$output .= echocsv($header);
	
	while($row = mysql_fetch_assoc($export)){
		$rowNew = array();
		$rowNew[] = $row['description'];
		$noTag = explode(';', $row['tag']);
		$rowNew[] = $row['issued_to'];
		for($g=0; $g<$count; $g++){
			array_push($rowNew, $noTag[$g]);
		}
		$output .= echocsv($rowNew);
	}
	
	print "$output";?>