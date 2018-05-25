<?php
session_start();
set_time_limit(3000);
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_REQUEST['uniqueId'])){
	$locations_exists = $_REQUEST['LocationSearchString'];
	$sdName = $obj->selQRYMultiple('distinct(description), tag', 'standard_defects', 'project_id = "'.$_REQUEST['projectID'].'"');
	$tagArray = array();
	foreach($sdName as $stdDefect){
		$tagData = $stdDefect['description'];
		$tagKey = explode(';', $stdDefect['tag']);
		if($stdDefect['tag'] == ''){
			$tagArray[][$stdDefect['tag']] = $tagData;
		}
		$tagKeyCount = sizeof($tagKey);
		for($i=0; $i<$tagKeyCount; $i++){
			if($tagKey[$i] != ''){
				$tagArray[][$tagKey[$i]] = $tagData;
			}
		}
	}
	$standardDefects = array();
	foreach($tagArray as $tArray){
		$testKey = (string)key($tArray);
		$pos = strpos($locations_exists, $testKey);
		if($pos === false) {}else{
			if(!in_array($tArray[key($tArray)], $standardDefects)){
				$standardDefects[] = $tArray[key($tArray)];
			}
		}
		if(key($tArray) == ''){
			$standardDefects[] = $tArray[key($tArray)];
		}
	}
#print_r($imagesData);die;
	$selOption = '';
	if(!empty($standardDefects)){
		for($i=0; $i<sizeof($standardDefects); $i++){
			$selOption .= '<li class="clickableLines" onClick="setDescription(this.innerHTML)">'.$standardDefects[$i].'</li>';
		}
	}else{
		$selOption = '<li class="clickableLines" onClick="setDescription(this.innerHTML)">No One Standard Defect Found !</li>';
	}
	echo $selOption;
}?>
