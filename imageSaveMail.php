<?php
session_start();

set_time_limit(600000000000000);
require_once('includes/property.php');
require_once('includes/class.phpmailer.php');
include("includes/functions.php");
$comm = new DB_Class();
include 'includes/commanfunction.php';
$object = new COMMAN_Class();


// baseFromJavascript will be the javascript base64 string retrieved of some way (async or post submited)
if(isset($_GET['type'])){
	if(isset($GLOBALS['HTTP_RAW_POST_DATA'])){

		$imageData=$GLOBALS['HTTP_RAW_POST_DATA'];
			
		// Remove the headers (data:,) part.
		// A real application should use them according to needs such as to check image type
		$filteredData=substr($imageData, strpos($imageData, ",")+1);

		// Need to decode before saving since the data we received is already base64 encoded
		$unencodedData=base64_decode($filteredData);

		// Save file. This example uses a hard coded filename for testing,
		// but a real application can specify filename in POST variable
		$name = time().'.svg';
		
		$fileName = "draw_markup/uploads/svg/".$name; // or image.jpg

		$fp = fopen($fileName, 'wb' );
		$fwrite = fwrite( $fp, $unencodedData);
		if($fwrite === false){
			
			$output = array("status"=>false, "image_name"=>$t, "image_size"=>'0 bytes');
		} else {
			$image = new Imagick();
			$image->setBackgroundColor(new ImagickPixel('transparent'));
			$image->readImageBlob(file_get_contents($fileName));
			//$image->resizeImage(1024, 768, imagick::FILTER_LANCZOS, 1); 
			$image->setImageFormat("png24");
			$t = time();
			$pngImg = "draw_markup/uploads/svg/".$t.".png";
			$image->writeImage($pngImg);
			unlink($filename);
			//$this->session->set_userdata('hotspot_image', $fileName);
			
			
			$output = array("status"=>true, "image_name"=>$t, "image_size"=>$fwrite);
		}
		fclose( $fp );
		echo json_encode($output);
	}	
}elseif(isset($_GET['hand'])){
	if(isset($GLOBALS['HTTP_RAW_POST_DATA'])){

		$imageData=$GLOBALS['HTTP_RAW_POST_DATA'];
		//$base = explode('#', $imageData);
		// Remove the headers (data:,) part.
		// A real application should use them according to needs such as to check image type
		$filteredData=substr($imageData, strpos($imageData, ",")+1);

		// Need to decode before saving since the data we received is already base64 encoded
		$unencodedData=base64_decode($filteredData);

		// Save file. This example uses a hard coded filename for testing,
		// but a real application can specify filename in POST variable
		$name = time().'.png';
		$fileName = "draw_markup/uploads/save_markup/".$name; // or image.jpg

		

		$fp = fopen($fileName, 'wb' );
		$fwrite = fwrite( $fp, $unencodedData);
		if($fwrite === false){
			$output = array("status"=>false, "image_name"=>$fileName, "image_size"=>'0 bytes');
		} else {
			//$this->session->set_userdata('hotspot_image', $fileName);
			$output = array("status"=>true, "image_name"=>$fileName, "image_size"=>$fwrite);
		}
		fclose( $fp );
		echo json_encode($output);
	}
}else{
	if(isset($GLOBALS['HTTP_RAW_POST_DATA'])){

		$imageData=$GLOBALS['HTTP_RAW_POST_DATA'];
			
		// Remove the headers (data:,) part.
		// A real application should use them according to needs such as to check image type
		$filteredData=substr($imageData, strpos($imageData, ",")+1);

		// Need to decode before saving since the data we received is already base64 encoded
		$unencodedData=base64_decode($filteredData);

		// Save file. This example uses a hard coded filename for testing,
		// but a real application can specify filename in POST variable
		$name = time().'.png';
		$fileName = "draw_markup/uploads/save_markup/".$name; // or image.jpg

		

		$fp = fopen($fileName, 'wb' );
		$fwrite = fwrite( $fp, $unencodedData);
		if($fwrite === false){
			$output = array("status"=>false, "image_name"=>$fileName, "image_size"=>'0 bytes');
		} else {
			//$svg_name = $_GET['svg_name'];
			if(isset($_GET['svg_name'])){
				$svg_name = "draw_markup/uploads/svg/".$_GET['svg_name'].".png";
				//echo '<img src="'.$image_1.'"><img src="'.$image_2.'">';
				
				$image = imagecreatefrompng($fileName);
				$w = imagesx($image);
				$h = imagesy($image);


				$frame = imagecreatefrompng($svg_name);

				imagecopy($image, $frame, 0, 0, 0, 0,$w,$h);

				# Save the image to a file
				$fileName = "draw_markup/uploads/save_markup/".time().".png"; 

				imagepng($image, $fileName);
			}
			
			if(isset($_GET['fileId'])){
				$fileId = $_GET['fileId'];

				$getRes = mysql_query("SELECT * FROM drawing_register_module_one WHERE id = ".$_REQUEST['fileId']."");
				$getData = mysql_fetch_assoc($getRes);
				 

				if(!empty($getData)){
					if($getData['file_type']=='PDF'){
						$pdfTitle = $getData['title'];
					}
				}

				$saveMarkUpImage = "INSERT INTO drawing_register_markups (`drawing_register_id`, `project_id`, `title`, `img_name`, `last_modified_date`, `original_modified_date`, `last_modified_by`, `created_date`, `created_by`, `resource_type`) VALUE ('".$fileId."', '".$_SESSION['idp']."', '".$pdfTitle."-".$_SESSION['ww_builder_user_name']."', '".$fileName."', NOW(), NOW(), '".$_SESSION['ww_builder_id']."', NOW(), '".$_SESSION['ww_builder_id']."', 'Webserver')";
				mysql_query($saveMarkUpImage) or mysql_error();
			}

			$output = array("status"=>true, "image_name"=>$fileName, "downloadName"=> $pdfTitle."-".$_SESSION['ww_builder_user_name'], "image_size"=>$fwrite);
		}
		fclose( $fp );
		echo json_encode($output);
	}
}
?>

<?php
#error_reporting(E_ALL);
#ini_set('display_errors', '1');

//print_r($_POST);
if(isset($_POST['to'])){
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

	$to = $_POST['to'];
	$cc = $_POST['cc'];
	$bcc = $_POST['bcc'];
	$subject = $_POST['subject'];
	$project_id = $_SESSION['idp'];
	$report_type = 'Markup Draw';
	
	$attachment = $_POST['attachment'];

	$descEmail = $_POST['descEmail'];
	$attachSize = filesize($attachment);
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
		$content .= 'Draw Image :';
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

}?>

