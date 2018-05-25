<?php


set_time_limit(360000);
include_once("servicesQurey.php");
$db = new QRY_Class();


$filename = 'json.zip';
header("Content-type: application/zip;\n");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".$filename);
header("Content-Disposition: attachment; filename=".$filename);
ob_end_flush();
@readfile($filename);









?>