<?php
#error_reporting(E_ALL);
#ini_set('display_errors', '1');

require_once('includes/class.phpmailer.php');

include 'includes/commanfunction.php';
$object = new COMMAN_Class();
$obj = new DB_Class();

if($_REQUEST['task'] == 'summary'){
	$mail = new PHPMailer();
	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
	$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
	$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
	$mail->SMTPAuth   = true;                    // enable SMTP authentication
	$mail->Port       = 465;
	$mail->Username   = "smtp@fxbytes.com"; // SMTP account username
	$mail->Password   = "smtp*123";        // SMTP account password
	
	$summaryArray = array();$message = '';$mailSend = array();
	
	$lastDate = $object->selQRYMultiple('DATE(created_date) as lastDate', 'last_cron_time', 'id!=0 ORDER BY id DESC LIMIT 0, 1');
	if(empty($lastDate)){
		$obj->db_query("INSERT INTO last_cron_time SET created_date = NOW()");
	}else{
		$startDate = $lastDate[0]['lastDate']; 
		$endDate = date('Y-m-d');
		$projectId = $object->selQRYMultiple('project_id, project_name', 'projects', 'is_deleted = 0');
		if(!empty($projectId)){//Collect All the project here
			foreach($projectId as $pId){
				$insertCountArray = $object->selQRYMultiple('count(inspection_id) as insertCount', 'project_inspections', 'project_id = "'.$pId['project_id'].'" AND DATE(created_date) BETWEEN "'.$startDate.'" AND "'.$endDate.'"');
				if(!empty($insertCountArray)){ $insertCount = $insertCountArray[0]['insertCount']; }else{ $insertCount = 0; }				
				
				$updateCountArray = $object->selQRYMultiple('count(inspection_id) as updateCount', 'project_inspections', 'project_id = "'.$pId['project_id'].'" AND DATE(last_modified_date) BETWEEN "'.$startDate.'" AND "'.$endDate.'"');
				if(!empty($updateCountArray)){ $updateCount = $updateCountArray[0]['updateCount']; }else{ $updateCount = 0; }
				
				if($insertCount > 0 || $updateCount > 0){//Collect All the project insertCount and updateCount here 
					$summaryArray[$pId['project_id']] = array('project_id' => $pId['project_id'], 'insert_count' => $insertCount, 'update_count' => $updateCount, 'project_name' => $pId['project_name']);
				}
			}
		}
		$managerEmail = $object->selQRYMultiple("user_id, user_email, user_fullname", "user", "is_deleted = 0 AND user_type = 'manager' ORDER BY user_id");//Collect user to mail here
		if(!empty($managerEmail)){
			$i=0;
			foreach($managerEmail as $managers){
				$aknowledge = '';
				$projectsManager = $object->selQRYMultiple("project_id, user_id", "user_projects", "is_deleted = 0 AND user_id = '".$managers['user_id']."' ORDER BY project_id");
				$message = '';
				if(!empty($projectsManager)){//Create Message Text here
					foreach($projectsManager as $projManager){
						if($object->searchArray($summaryArray, $projManager['project_id'])){
							if($aknowledge == ''){//Add email addresszs
								$mailSendTo[$i]['sendTo'] = $managers['user_email'];
								$mailSendTo[$i]['senderName'] = $managers['user_fullname'];
								$aknowledge .= 'Hi '.$managers['user_fullname'].',<br /><br />
								Summary of Activity on '.date('d F Y').'<br /><br /><br />';	
							}
							$message .= 'Project name: '.$summaryArray[$projManager['project_id']]['project_name'].'<br /><br />';
							if($summaryArray[$projManager['project_id']]['insert_count'] > 0){
								$message .= $summaryArray[$projManager['project_id']]['insert_count'].' New Inspections added <a target="_blank" href="'.EMAILURL.'&pd='.base64_encode($summaryArray[$projManager['project_id']]['project_id']).'">(click here for details)</a><br />';
							}
							if($summaryArray[$projManager['project_id']]['update_count'] > 0){
								$message .= $summaryArray[$projManager['project_id']]['update_count'].' Inspections Altered <a target="_blank" href="'.EMAILURL.'&pd='.base64_encode($summaryArray[$projManager['project_id']]['project_id']).'">(click here for details)</a><br /><br /><br />';
							}
						}
					}
					$mailSendTo[$i]['sendMessage'] = $aknowledge.$message;//Add message here
				}
				$i++;
			}
		}
		
		foreach($mailSendTo as $mailSend){//Send Mail Here
			if($mailSend['sendMessage'] != ''){
				$subject = SITE_NAME;
				
				$body = '<html><body>';
				$body .= $mailSend['sendMessage'];
				$body .= '-- Thanks and Regards,<br />'.
								trim(DOMAIN, '/');
				$body .= '</body></html>';
#				echo $body ;
				$mail->SetFrom('administrator@'.trim(DOMAIN, '/'), trim(DOMAIN, '/'));
				$mail->AddReplyTo('administrator@'.trim(DOMAIN, '/'), trim(DOMAIN, '/'));
	
				$mail->Subject = $subject;
				$mail->MsgHTML($body);
#	echo $body;
				$mail->AddAddress($mailSend['sendTo'], $mailSend['senderName']);//send to mail heree
				
				$mail->Send();
				$mail->ClearAddresses();
				$insertRecord = "INSERT INTO email_history SET 
									email_history_email = '".$mailSend['sendTo']."(".$mailSend['senderName'].")',
									email_content = '".$body."',
									mail_sent_time = NOW(),
									created_date = NOW(),
									created_by = 0";
				mysql_query($insertRecord);
			}
		}
		$insertRecord = "INSERT INTO last_cron_time SET created_date = NOW()";
		mysql_query($insertRecord);
	}
}else{
	header("HTTP/1.0 404 Not Found");
}?>