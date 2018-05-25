<?php
	session_start();

	if($_REQUEST['saveImage']){
		$projId = $_SESSION['qaChecklistProId'];
		//$elementId = $_SESSION['elementID'];
		$sign = $_REQUEST['sign'];
		$elementId = "image_name";
		$customName = (isset($_REQUEST['customName'])) ? $_REQUEST['customName'] : '';
		$oldImageName = $_SESSION['oldImageName'];

		define("IMPORTFILEPATH", 'inspections/ncr_files/');
		if(!is_dir(IMPORTFILEPATH)){
			@mkdir(IMPORTFILEPATH, 0777);
		}
		
		//$img = $this->input->get_post('imgBase64');
		$img = $_POST['imgBase64'];
		$img = str_replace('data:image/png;base64,', '', $img);
		$img = str_replace('[removed]', '', $img);
		$img = str_replace(' ', '+', $img);
		$data = base64_decode($img);
		//$file = IMPORTFILEPATH . uniqid() . '.png';
		$fileName = $projId.'_'.substr(microtime(), -6, -1).rand(0,99) . '.png';
		if($oldImageName != "newImage"){
			//$fileName = $oldImageName;
			@unlink(IMPORTFILEPATH.$oldImageName);
		}
		
		$success = file_put_contents(IMPORTFILEPATH . $fileName, $data);
		$returnId = "";
		if(strpos($elementId, "save")>-1){
			$returnId = str_replace("save", "photo", $elementId);
		}
		$name = "saveMeetingParticipantSignatureImage";
		$replaceName = str_replace(range(0,9),'',$elementId);
		if(strpos($elementId, "saveAttendeesSignatureImage")>-1){
			$d = explode("saveAttendeesSignatureImage", $elementId);
			$name = "saveAttendeesSignatureImage";
			
		}else if(strpos($elementId, $replaceName)>-1){
			$d = explode($replaceName, $elementId);
			$name = $replaceName;
		}else{
			$d = explode("saveMeetingParticipantSignatureImage", $elementId);
		}

		//$d = explode("saveMeetingParticipantSignatureImage", $elementId);
		if(isset($d[1]) && $d[1]>=0){
			if(!empty($customName)) {
				$name_att = $customName;
			} else {
				$name_att = $elementId.'['.$sign.']';
			}
			$output = '<img src="'.IMPORTFILEPATH.$fileName.'?'.time().'" width="145" height="50" style="margin:4px;" id="'.$returnId.'" /><input type="hidden" name="'.$name_att.'" value="'.$fileName.'" />';

		}else{
			$output = '<img src="'.IMPORTFILEPATH.$fileName.'?'.time().'" width="145" height="50" style="margin:4px;" id="'.$returnId.'" /><input type="hidden" name="'.$elementId.'" value="'.$fileName.'" />';
		}
		echo $output;
		//echo $success ? $fileName : 'Unable to save the file.';
		die;
	}

?>
