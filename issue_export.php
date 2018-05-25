<?php
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

$fileName = 'Issue_TO_Tamplate'.microtime().'.csv';
header("Content-type: application/csv");
header("Content-Disposition: attachment; filename = Issue_TO_Export.csv");
header("Pragma: no-cache");
header("Expires: 0");	

$query = "SELECT MAX(Tags) as TotalTag FROM (SELECT *, LENGTH(replace(tag,';','f+')) - LENGTH(tag) AS Tags FROM master_issue_to WHERE is_deleted = 0) AS SubQueryAlias ORDER BY `SubQueryAlias`.`Tags`  ASC";
$result = mysql_query($query);
$max_tag = mysql_fetch_array($result);
$count = $max_tag['TotalTag']; 

$standard_query = "SELECT company_name, issue_to_name, issue_to_phone, issue_to_email, tag FROM master_issue_to WHERE is_deleted = 0 GROUP BY issue_to_name";

$export=mysql_query($standard_query);
$header = array('Contact Name', 'Company Name', 'Phone', 'Email');
for ($i = 0; $i <$count; $i++){
	array_push($header , 'Tag '.($i+1));
}
$output .= echocsv($header);


while($row = mysql_fetch_assoc($export)){
	$noTag = explode(';', $row['tag']);
	for($g=0; $g<sizeof($noTag); $g++){
		array_push($row, $noTag[$g]);
	}
	unset($row['tag']);
	$output .= echocsv($row);
}
print "$output";
?>