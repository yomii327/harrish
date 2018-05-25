<?PHP
session_start();

$msg ='';
include('includes/commanfunction.php');
 
 $req1 = mysql_query('select attach_id, name, attachment_name from pmb_attachments where attach_id="'.$_GET['attachment'].'"');
 $attachment=mysql_fetch_array($req1);

 // Define the path to file
   //$req1 = mysql_query('select attach_id, name, attachment_name from pmb_attachments where message_id="'.$mid.'"');
   //$row = mysql_fetch_array($req1);
    $file = 'attachment/'.$attachment['attachment_name'];
	if (file_exists($file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($attachment['name']).'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file);
		exit;
	}
	else
	{
	   echo 'No File Exist';
	   die();
	}
 

 ?>