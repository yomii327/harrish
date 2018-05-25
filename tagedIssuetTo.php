<?php
session_start();
set_time_limit(3000);
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_REQUEST['uniqueId'])){
	$locations_exists = $_REQUEST['LocationSearchString'];
	$issueToName = $obj->selQRYMultiple('distinct(issue_to_name), tag', 'inspection_issue_to', 'project_id = "'.$_REQUEST['projectID'].'"');
	$issueTagArray = array();
	foreach($issueToName as $issueName){
		$issueTagData = $issueName['issue_to_name'];
		$issueTagKey = explode(';', $issueName['tag']);
		if($issueName['tag'] == ''){
			$issueTagArray[][$issueName['tag']] = $issueTagData;
		}
		$issueTagKeyCount = sizeof($issueTagKey);
		for($i=0; $i<$issueTagKeyCount; $i++){
			if($issueTagKey[$i] != ''){
				$issueTagArray[][$issueTagKey[$i]] = $issueTagData;
			}
		}
	}
	$issueToSelect = array();
	foreach($issueTagArray as $issueTArray){
		$testKey = (string)key($issueTArray);
		$pos = strpos($locations_exists, $testKey);
		if($pos === false) {}else{
			if(!in_array($issueTArray[key($issueTArray)], $standardDefects)){
				$issueToSelect[] = $issueTArray[key($issueTArray)];
			}
		}
		if(key($issueTArray) == ''){
			$issueToSelect[] = $issueTArray[key($issueTArray)];
		}
	}
	$selOption == '';
	for($k=0; $k<sizeof($issueToSelect); $k++){
		if($issueToSelect[$k] != 'NA'){
			if($selOption == ''){
				$selOption = $issueToSelect[$k];
			}else{
				$selOption .= '@@@'.$issueToSelect[$k];
			}
		}
	}
	echo $selOption;
}?>