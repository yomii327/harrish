<?php
//Header Secttion for include and objects 
include_once("servicesQurey.php");
$db = new QRY_Class();
//Header Secttion for include and objects 
if(isset($_REQUEST['uniqueid']) && $_REQUEST['uniqueid'] != ''){
	if(is_numeric($_REQUEST['uniqueid'])){
		$resource_type = 'Webserver';
	}else{
		$resource_type = 'iPad';
	}
 	$fs = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['feedback']))));
	$desc = $db->queryFilter(trim(strip_tags(addslashes($_REQUEST['description']))));
	if(!empty($fs) && !empty($desc) && !empty($resource_type)){
		$feed_insert = "insert into feedback (feedback_type, feedback_description, created_date, 	resource_type, last_modified_date) values ('".$fs."','".$desc."',now(), '".$resource_type."', now())";
		
		mysql_query($feed_insert);
		$lastId = mysql_insert_id();
		if($lastId != ''){
			if(is_numeric($_REQUEST['uniqueid'])){
				if($fs == 'Feedback'){
					echo 'Thanks for providing the feedback !';
				}else{
					echo 'Thanks for providing your query our team contact to you soon !';
				}
			}else{
				$output = array(
					'status' => true,
					'message' => 'Thanks for providing the feedback!',
					'data' => $lastId
				);
				echo '['.json_encode($output).']';
			}
		}else{
			if(is_numeric($_REQUEST['uniqueid'])){
				echo 'Please try again later !';
			}else{
				$output = array(
					'status' => false,
					'message' => 'Please try again later!',
					'data' => ''
				);
				echo '['.json_encode($output).']';
			}
		}
	}else{
		$output = array(
			'status' => false,
			'message' => 'Please provide all the information !',
			'data' => ''
		);
		echo '['.json_encode($output).']';	
	}
}else{
	$output = array(
		'status' => false,
		'message' => 'Please provide all the information !',
		'data' => ''
	);
	echo '['.json_encode($output).']';	
}
?>