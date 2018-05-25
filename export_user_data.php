<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

include 'includes/commanfunction.php';
$object = new COMMAN_Class();

//Function Section
$fileName = 'user_name_data.csv';
$output = '';
$header = array('Company', 'Full Name', 'User Name', 'Email', 'Contact No.');
$output .= echocsv($header);

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

$userData = $object->selQRYMultiple('Distinct user_id, company_name, user_fullname, user_name, user_email, user_phone_no', 'user', 'is_deleted in (0)');
foreach($userData as $uData){
	array_shift($uData);
	$output .= echocsv($uData);
}

header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=".$fileName);
header("Pragma: no-cache");
header("Expires: 0");

print $output; ?>