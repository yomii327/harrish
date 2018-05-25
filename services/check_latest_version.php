<?php
$iPadVersion = '6.0.6';
$iPhoneVersion = '1.0.4';

$output = array(
	'status' => true,
	'data' => $iPadVersion,
	'iPadMessage' => 'New build version "'.$iPadVersion.'" is available.\nWould you like to download it?',
	'iPhoneMessage' => 'New build version '.$iPhoneVersion.' is available.\nWould you like to download it?',
	'iPadVersion' => $iPadVersion,
	'iPhoneVersion' => $iPhoneVersion,
	'iPadURL' => 'itms-services://?action=download-manifest&url=https://wiseworking.com.au/distribution/ipad/harrishmcdev(defectid)/V'.$iPadVersion.'/app.plist',
	'iPhoneURL' => 'itms-services://?action=download-manifest&url=https://wiseworking.com.au/distribution/iphone/defectid/v4/'.$iPhoneVersion.'/app.plist'
);
if(isset($_REQUEST['antiqueID']))
	echo json_encode($output);
else
	echo '['.json_encode($output).']';
die;?>
