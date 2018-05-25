<?php
session_start();
include("./includes/functions.php");
include_once("includes/commanfunction.php");
$obj = new DB_Class();

$userId = $_SESSION['ww_builder']['user_id'];
define('QRFILEPATH', 'qrcode_images/'.$userId.'/');
define('QRCOMPLETEPATH', $_SERVER['DOCUMENT_ROOT']."/qrcode_images/".$userId."/");

# Grab user inputs.
$action = $_REQUEST['action'];

#Genratre QR Code.
if($action == 'generate') {
	$location_id = $_REQUEST['location_id'];
	# Get location string.
	$rResult = $obj->db_query('SELECT location_id,project_id,location_name_tree FROM project_locations WHERE location_id='. $location_id .' AND is_deleted=0') or die(mysql_error());
	$rCount = mysql_num_rows($rResult);
	if($rCount>0) {
		$rResult = mysql_fetch_assoc($rResult);
		$locString = $rResult['project_id'] .'_'. $rResult['location_id'];
		$locTree = $rResult['location_name_tree'];
		$fileName = $rResult['project_id'] .'_'. $rResult['location_id'].'_qrcode.png';
		$dbFileName = $rResult['project_id'] .'_'. $rResult['location_id'];
		
		$fileURL = 'http://chart.apis.google.com/chart?cht=qr&chs=350x350&chl='.urlencode($locString);
		$file_headers = @get_headers($fileURL);

		if($file_headers[0] != 'HTTP/1.1 404 Not Found') {
			$qrCode = file_get_contents($fileURL);
			if(!is_dir('qrcode_images')){
				@mkdir('qrcode_images', 0777);
			}
			if(!is_dir('qrcode_images/'.$userId)){
				@mkdir('qrcode_images/'.$userId, 0777);
			}
			if(file_exists(QRFILEPATH.$fileName)) {
				unlink(QRFILEPATH.$fileName);
			}
			
			$generateFile = file_put_contents(QRFILEPATH.$fileName, $qrCode);
			if($generateFile != FALSE) {
				
				$cmd = 'montage '.QRCOMPLETEPATH.$fileName.' -tile x1 -geometry 350x350+2+2 -pointsize 20 -set label "'.wordwrap(trim($locTree), 30, '\n', true).'" -background white '.QRCOMPLETEPATH.$fileName;

				shell_exec($cmd);
				
				# Update qr_code name into database.
				$uResult = $obj->db_query('UPDATE project_locations SET qr_code = "'. $dbFileName .'" WHERE location_id='. $location_id .' AND is_deleted=0') or die(mysql_error());
				
				$output = '<img src="'.QRFILEPATH.$fileName.'" title="'.$locTree.'" alt="'.$locTree.'" /><br/><br/><div style="width:100%;text-align:center;"><a href="javascript:void(0);" onclick="saveImages(\''.$fileName.'\');"><img src="images/_view_btn.png" alt="save button" /></a></div>';
			} else {
				$output = '<p>Error occurred when generating <b>QR Code</b> file.</p>';
			}
		}
		echo $output;
	} else {
		echo '<p>! Records Not Found !</p>';
	}
} elseif($action == 'download') {
	$file = $_REQUEST['filename'];
	header('Content-Description: File Transfer');
	header("Content-type: image/png");
	header("Content-disposition: attachment; filename= ".$file."");
	readfile('qrcode_images/'.$userId.'/'.$file);
	//unlink('qrcode_images/'.$userId.'/'.$file);
} else {
	echo '<p>! Records Not Found !</p>';
}

/* Omit PHP closing tags to help avoid accidental output */
