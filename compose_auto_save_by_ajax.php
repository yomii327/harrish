<?php
#ini_set('display_errors',1);
session_start();
$projectId = isset($_SESSION['idp'])?$_SESSION['idp']:0;
$msg ='';
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$userId = $_SESSION['ww_builder']['user_id'];

$refNumberMsgCount = array();
#$refNumberMsgCount = $object->selQRYMultiple('max(um.rfi_number) AS refNumber', 'pmb_message as m, pmb_user_message as um', 'm.project_id = '.$_SESSION['idp'].' AND m.is_draft = 0 AND m.is_deleted= 0');

if(isset($_POST['submit']) and $_POST['submit']=='add') {
#print_r($_POST);die;
	$from = $_SESSION['ww_builder_id'];
	$recipTo = !empty($_POST['recipTo']) ? $_POST['recipTo'] : 0;
	$recipCC = !empty($_POST['recipCC']) ? $_POST['recipCC'] : 0;
	$subject = empty($_POST['subject'])?'no subject':$_POST['subject'];
	$purchaserLocation = $_POST['purchaserLocation'];
	$tags = $_POST['tags'];	
	$messageType = $_POST['messageType'];	
	$correspondenceNumber = $_POST['correspondenceNumber'];	
	$companyTag = $_POST['companyTag'];		
	$refrenceNumber = !empty($_POST['newDynamicGenRefrenceNumber']) ? $_POST['newDynamicGenRefrenceNumber'] : $_POST['refrenceNumber']." ".$_POST['messageType'];
#	$refrenceNumber = str_replace('Request For Information', '', $refrenceNumber);
	$messageDetails = $_POST['messageDetail'];
	$messgeId = (isset($_POST['composeId']) && !empty($_POST['composeId']))?$_POST['composeId']:0;
	
	$RFInumber = "";$RFIdescription = "";$RFIstatus = "";$fixedByDate = "";
	if($messageType == 'Request For Information'){
		$RFInumber = $_POST['RFInumber'];
		$fixedByDate = $object->dateChanger('-', '-', $_POST['fixedByDate']);
		$RFIstatus = $_POST['RFIstatus'];
		if($RFIstatus == "")
			$RFIstatus = "Draft";
	}

	$imageCompose = $_POST['image_compose'];
	$ccAddress = '';
	$toExtraAddress = '';
	$attahment1 = '';
	if($_POST['emailAttachedAjax'] == 1){ $attahment1 = $_SESSION[$_SESSION['idp'].'_emailfile']; }
	// Remove old attachment if found any attachment

	if(isset($_POST['removeAttachment'])){
		if(explode(',', str_replace(', ', ',', $_POST['removeAttachment']))){
			$removeAttachments = explode(',', str_replace(', ', ',', $_POST['removeAttachment']));
			foreach($removeAttachments as $attachID){
				$key = "";
				$key = isset($_SESSION[$_SESSION['idp'].'_emailfile'])?array_search(trim($attachID), $_SESSION[$_SESSION['idp'].'_emailfile']):'';
				if($key != "")
					unset($_SESSION[$_SESSION['idp'].'_emailfile'][$key]);
				if(is_numeric($attachID)){
					$attDeleteQRY = "UPDATE `pmb_attachments` SET `is_deleted` = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE project_id = ".$_SESSION['idp']." AND `attach_id` =".$attachID;
					mysql_query($attDeleteQRY);
				}
			}
		}else{
			$key = "";
			$key = array_search(trim($attachID), $_SESSION[$_SESSION['idp'].'_emailfile']);
			if($key != "")
				unset($_SESSION[$_SESSION['idp'].'_emailfile'][$key]);
			if(is_numeric($attachID)){
				$attDeleteQRY = "UPDATE `pmb_attachments` SET `is_deleted` = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE project_id = ".$_SESSION['idp']." AND `attach_id` =".$attachID;
				mysql_query($attDeleteQRY);
			}
		}
	}

	$_POST['saveDraft'] = 1;
	if(isset($_POST['saveDraft'])){
		# Start :- Send Email 
			if(isset($recipCC) && is_array($recipCC)){
				foreach($recipCC as $cc){
					$ccAddress.= ($ccAddress=='') ? $cc : ', '.$cc;
				}
			}

			if(isset($recipTo) && is_array($recipTo)){
				foreach($recipTo as $to){
					if(is_numeric($to)){
					}elseif(!empty($to)){
						$toExtraAddress.= ($toExtraAddress=='')?$to:', '.$to;
					}
				}
			}
//Message Attachments End Here
			# Save message in PMB
			if(get_magic_quotes_gpc()) {
				$messageDetails = stripslashes($messageDetails);
			}
			$messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));
			$recipTo = !empty($_POST['recipTo'])?$_POST['recipTo']:array(0);
			if(isset($recipTo)){
				foreach($recipTo as $to){
					if(is_numeric($to)){
						$messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageType, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation,'','','','','','','','','','','',$imageCompose);
						$messgeId = $messageBoard['messgeId'];
					}else{
						$messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageType, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, 0, $refrenceNumber, $RFInumber, $fixedByDate, $RFIstatus, "", 0, 0, "AutoSave", $correspondenceNumber, $companyTag, $purchaserLocation,'','','','','','','','','','','',$imageCompose);
						$messgeId = $messageBoard['messgeId'];
					}
					$attahment1 = '';
				}
			}	
			
		# Add custom entry in address book
			if(!empty($_POST['customEmailEntry'])){
				if(explode(',', $_POST['customEmailEntry'])){
					$customEmails = explode(', ', $_POST['customEmailEntry']);
				}else{
					$customEmails[] = $_POST['customEmailEntry'];
				}
				foreach($customEmails as $newEmail){
					$name =  explode('@', $newEmail);
					$fullName = trim(addslashes($name[0]));
					$userEmail = trim(addslashes($newEmail));
					
					$inAddressBook = $object->selQRYMultiple('full_name', 'pmb_address_book', 'full_name="'.$fullName.'" AND user_email="'.$userEmail.'" AND project_id="'.$_SESSION['idp'].'"');
					if(!isset($inAddressBook[0]['full_name'])){
						$inssertQRY = "INSERT INTO pmb_address_book SET
							project_id = '".$_SESSION['idp']."',
							full_name = '".$fullName."',
							user_email = '".$userEmail."',
							last_modified_date = NOW(),
							last_modified_by = '".$_SESSION['ww_builder_id']."',
							created_date = NOW(),
							created_by = '".$_SESSION['ww_builder_id']."'";
						mysql_query($inssertQRY);
					}
				}
			}
			
		# Add custom entry in pmb_correspondences_tags
			if(!empty($_POST['customCompanyTag'])){
				if(explode(',', $_POST['customCompanyTag'])){
					$customCompanyTag = explode(', ', $_POST['customCompanyTag']);
				}else{
					$customCompanyTag[] = $_POST['customCompanyTag'];
				}
				foreach($customCompanyTag as $newCompanyTag){
					$cmpTag = trim(addslashes($newCompanyTag));
					
					$getCompanyTag = $object->selQRYMultiple('correspondences_tags', 'pmb_correspondences_tags', 'correspondences_tags="'.$cmpTag.'" AND project_id="'.$_SESSION['idp'].'"');
					if(!isset($getCompanyTag[0]['correspondences_tags'])){
						$inssertQRY = "INSERT INTO pmb_correspondences_tags SET
							project_id = '".$_SESSION['idp']."',
							correspondences_tags = '".$cmpTag."',
							last_modified_date = NOW(),
							last_modified_by = '".$_SESSION['ww_builder_id']."',
							created_date = NOW(),
							created_by = '".$_SESSION['ww_builder_id']."'";
						mysql_query($inssertQRY);
					}
				}
			}		
			
		echo $messgeId;
	}
}
unset($_SESSION[$_SESSION['idp'].'_remaimberData']);
/*
unset($_SESSION[$_SESSION['idp'].'_emailfile']);
unset($_SESSION[$_SESSION['idp'].'_orignalFileName']);
*/
?>