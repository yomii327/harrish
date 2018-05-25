<?php
#error_reporting(E_ALL);
#ini_set('display_errors', '1');
session_start();
set_time_limit(600000000000000);
require_once('includes/property.php');
require_once('includes/class.phpmailer.php');

include 'includes/commanfunction.php';
$object = new COMMAN_Class();
$obj = new DB_Class();
$owner_id = $_SESSION['ww_builder_id'];	
if(isset($_REQUEST['name'])){
	$mail = new PHPMailer();
	$mail->IsSMTP(); // telling the class to use SMTP
	
	$mail->SMTPSecure = 'tls'; //"ssl";	// sets the prefix to the servier
	$mail->Host = smtpHost; 	//pod51022.outlook.com      // sets GMAIL as the SMTP server
	$mail->SMTPDebug = 0;				// enables SMTP debug information (for testing)
	$mail->SMTPAuth = true;        		// enable SMTP authentication
	$smtpPort = smtpPort;
	if(!empty($smtpPort)){
		$mail->Port =  smtpPort; //587;
	}
	$mail->Username = smtpUsername; //"wiseworkingsales@gmail.com"; // SMTP account username
	$mail->Password = smtpPassword; //"Wiseworking123";   // SMTP account password

	$to = $_REQUEST['to'];
	$cc = $_REQUEST['cc'];
	$bcc = $_REQUEST['bcc'];
	$subject = $_REQUEST['subject'];
	$project_id = $_REQUEST['project_id'];
	$report_type = $_REQUEST['report_type'];
	$attachment = $_REQUEST['attachment'];
	$descEmail = $_REQUEST['descEmail'];
	$attachSize = filesize($_REQUEST['attachment']);
	$mailSentTo = $to.', '.$cc.', '.$bcc;
	$isertQRY = "INSERT INTO email_history SET
					email_history_email = '".addslashes($mailSentTo)."',
					email_attachment_file = '".addslashes($attachment)."',
					email_content = '".addslashes($descEmail)."',
					mail_sent_time = NOW(),
					project_id = '".$project_id."',
					report_type = '".addslashes($report_type)."',
					created_date = NOW(),
					created_by = '".$owner_id."',
					last_modified_date = NOW(),
					last_modified_by = '".$owner_id."'";
	mysql_query($isertQRY);						
//Condition for send attachment or url
	$content = $descEmail;

	if($attachSize < 26214400){
		$mail->AddAttachment($attachment);
	}else{
		$content .= 'PDF Report :';
		$content .= '<a href="http://'.DOMAIN.$attachment.'" target="_blank">Click Here for report</a>';
	}
//Condition for send attachment or url
	$body = '<html><body>';
	$body .= $content;
	$body .= '<br />-- Thanks and Regards,<br />'.
					trim(DOMAIN, '/');
	$body .= '</body></html>';

	$mail->Subject = $subject;
	$mail->MsgHTML($body);

//Add To here
	$toArr = explode(',', $to);
	for($i=0;$i<sizeof($toArr);$i++){
		$mail->AddAddress(trim($toArr[$i]), '');//send to mail heree
	}
//Add CC here
	$ccArr = explode(',', $cc);
	for($i=0;$i<sizeof($ccArr);$i++){
		$mail->AddCC(trim($ccArr[$i]), '');//send to mail heree
	}

//Add BCC here
	$bccArr = explode(',', $bcc);
	for($i=0;$i<sizeof($bccArr);$i++){
		$mail->AddBCC(trim($bccArr[$i]), '');//send to mail heree
	}
	
	$mail->Send();
	$mail->ClearAddresses();
	echo 'Mail Sent Successsfully';
}else{
	header("HTTP/1.0 404 Not Found");
}?>