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


	header("Content-type: application/csv");
	header("Content-Disposition: attachment; filename=exports.csv");
	header("Pragma: no-cache");
	header("Expires: 0");	
	$fileName = 'Issue_TO_Tamplate'.microtime().'.csv';
	$query="SELECT issue_to_name as 'Contact Name',company_name as 'Company Name',issue_to_phone as 'Phone',issue_to_email as 'Email' FROM inspection_issue_to where project_id=".$_SESSION['idp']." and is_deleted=0";
	$export = mysql_query($query);
 	$count = mysql_num_fields($export);
	for ($i = 0; $i < $count; $i++) 
	{
		$header .= mysql_field_name($export, $i).",";
	}
	while($row = mysql_fetch_row($export)) 
	{
		$line = '';
		foreach($row as $value) 
		{
			if ((!isset($value)) || ($value == "")) 
			{
				$value = ",";
			}
			else
			{
				$value = str_replace('"', '""', $value);
				$value = '"' . $value . '"' . ",";
			}
			$line .= $value;
		}
		$data .= trim($line)."\n";
	}
	$data = str_replace("\r", "", $data);
	if ($data == "") 
	{
		$data = "\n(0) Records Found!\n";
	}
	print "$header\n$data";













?> 


