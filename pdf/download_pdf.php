<?php


 // this method is used for the download file 

$filename = $_GET['filename'];
$file_extension = "pdf";
header("Content-Type: " . $contenttype);
header("Content-Disposition: attachment; filename=\"" . $filename . "\";");
readfile($filename);
exit();

?>