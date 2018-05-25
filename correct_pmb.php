<?php
session_start();
set_time_limit(999999999999999999999);
error_reporting(E_ALL);
ini_set('display_errors', '1');

include 'includes/commanfunction.php';
$object = new COMMAN_Class();
if($_REQUEST['task'] == 'getDrawing_register'){
	$projWiseThreadData = array();
	$threadIdData = $object->selQRYMultiple('project_id, pmb_thread_id', 'drawing_register', 'pmb_thread_id != 0 AND is_deleted = 0 AND project_id = '.$_REQUEST['projectID'].' GROUP BY pmb_thread_id ORDER BY project_id');	
	foreach($threadIdData as $thData){
		if(is_array($projWiseThreadData[$thData['project_id']])){	
			$projWiseThreadData[$thData['project_id']][] = $thData['pmb_thread_id'];
		}else{
			$projWiseThreadData[$thData['project_id']] = array();
			$projWiseThreadData[$thData['project_id']][] = $thData['pmb_thread_id'];
		}
	}
	
echo join(', ', $projWiseThreadData[$_REQUEST['projectID']]);die;
	print_r($projWiseThreadData);
	
	die;
	$drawingData = $object->selQRYMultiple('dr.id, dr.title, dr.project_id, dr.number, dr.attribute1, dr.attribute2', 'drawing_register AS dr, user_projects AS up',
	
	'dr.attribute1 = "Lighting" AND
	
	is_document_transmittal = 0 AND up.is_deleted = 0 AND dr.is_deleted = 0 AND up.project_id = dr.project_id AND dr.pmb_thread_id = 0 AND up.user_id = '.$_REQUEST['userID'].' AND up.project_id = '.$_REQUEST['projectID'].' ORDER BY project_id');
	echo '<br />';
	foreach($drawingData as $drData){
		$messageData = $object->selQRYMultiple('m.message_id, dr.id, dr.attribute1, dr.attribute2, dr.title, dr.number, dr.revision, m.project_id, m.message',
	
			'drawing_register AS dr, pmb_message AS m',
			
			'dr.is_deleted = 0 AND
				m.is_deleted = 0 AND
				m.project_id = dr.project_id AND
				m.project_id = '.$_REQUEST['projectID'].' AND
				dr.project_id = '.$_REQUEST['projectID'].' AND
				dr.id = '.$drData['id'].' AND
				m.message LIKE "Hello,%" AND
				m.title LIKE "%Document Number : '.$drData['number'].'%" AND
				message_type = "Document Transmittal"
			GROUP BY m.message_id
			ORDER BY project_id');
		
		echo '<pre>';
		print_r($messageData);
		echo '</pre>';
	}
}

