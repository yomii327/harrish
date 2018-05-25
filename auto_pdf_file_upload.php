<?php
ini_set('display_errors',0);
session_start();
$path = $_COOKIE['path'];
$name='';$fileSize='';$output='';

include('includes/commanfunction.php');

$obj = new COMMAN_Class();

if(isset($_GET['uniqueID'])){
	# Add attachment by ajax
	if($_GET['action'] == 'addAttachment'){
		
		$fileElementName = isset($_GET['fieldName'])?$_GET['fieldName']:"attachFile";
		$oldName = isset($_GET['oldName'])?$_GET['oldName']:"newImage";
		$uploadSection = isset($_GET['upLocation'])?$_GET['upLocation']:"formPhoto";
		$isMultiple = isset($_GET['isMultiple'])?$_GET['isMultiple']:"";		
		$fileNamePrefix = isset($_GET['prefix'])?$_GET['prefix']:"";		
		$projId = isset($_SESSION['idp'])?$_SESSION['idp']:0;
		
		$ext = end(explode('.', $_FILES[$fileElementName]['name']));			
		$fileName = $_SESSION['companyId'].'_'.$projId.'_'.substr(microtime(), -6, -1).rand(0,99).$fileNamePrefix.'.'.$ext;			
		
		if($oldName!="newImage"){
			$fileName = $oldName;
		} 

		define("IMPORTFILEPATH", 'inspections/ncr_files/');
		
		if(!is_dir(IMPORTFILEPATH)){
			@mkdir(IMPORTFILEPATH, 0777);
		}
		
		$returnId = "";
		if(strpos($fileElementName, "save")>-1){
			$returnId = str_replace("save", "photo", $fileElementName); 
		}
		if(!empty($_FILES[$fileElementName]['error'])){
			switch($_FILES[$fileElementName]['error']){
				case '1':
					$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
					break;
				case '2':
					$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
					break;
				case '3':
					$error = 'The uploaded file was only partially uploaded';
					break;
				case '4':
					$error = 'No file was uploaded.';
					break;
	
				case '6':
					$error = 'Missing a temporary folder';
					break;
				case '7':
					$error = 'Failed to write file to disk';
					break;
				case '8':
					$error = 'File upload stopped by extension';
					break;
				case '999':
				default:
					$error = 'No error code avaiable.';
			}
		}elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none'){
			$error = 'No file was uploaded.';

/*		}else if($fileElementName == 'attachment1'){
			if(strpos('JPEG |jpeg |JPG |jpg |PNG |png |GIF |gif', $ext)>0){$isImage = 1;}else{$isImage=0;}
				
			$orignalFileName = $_FILES[$fileElementName]['name'];
			$outputArr = array('status'=> true, 'fileName'=> $orignalFileName, 'filePath'=>$fileName, 'isImage'=>$isImage, 'msg'=> 'Attachment added successfully');
			$output = json_encode($outputArr);		*/

		}elseif($returnId!=""){
			if($ext == 'pdf' || $ext == 'PDF'){
				move_uploaded_file($_FILES[$fileElementName]["tmp_name"], "inspections/ncr_files/".$fileName);
			}else{
				$reSizeImg = $obj->resizeImages($_FILES[$fileElementName]["tmp_name"], 799, 799, IMPORTFILEPATH.$fileName);
			}
		
			if(strpos($fileElementName, $isMultiple)>=0 && !empty($isMultiple)){
				$d = explode($isMultiple, $fileElementName);
			}
			if(isset($d[1]) && $d[1]>=0 && !empty($isMultiple)){
				if($ext == 'pdf' || $ext == 'PDF'){
					$output = '<img src="images/default_PDF_register_64x64.png" width="100" height="80" style="margin:4px;" id="'.$returnId.'" /><input type="hidden" name="'.$isMultiple.'['.$d[1].']" value="'.$fileName.'" />';
				}else{
					$output = '<img src="'.IMPORTFILEPATH.$fileName.'?'.time().'" width="100" height="80" style="margin:4px;" id="'.$returnId.'" /><input type="hidden" name="'.$isMultiple.'['.$d[1].']" value="'.$fileName.'" />';
				}
			}else{
				if($ext == 'pdf' || $ext == 'PDF'){
					$output = '<img src="'.IMPORTFILEPATH.$fileName.'?'.time().'" width="100" height="80" style="margin:2px;" id="'.$returnId.'" /><input type="hidden" name="'.$fileElementName.'" value="'.$fileName.'" />';
				}else{
					$output = '<img src="'.IMPORTFILEPATH.$fileName.'?'.time().'" width="100" height="80" style="margin:2px;" id="'.$returnId.'" /><input type="hidden" name="'.$fileElementName.'" value="'.$fileName.'" />';
				}
			}
			//$output = $fileName;
			@unlink($_FILES[$fileElementName]);
		
		}else{      
			$output = $fileName;
			@unlink($_FILES[$fileElementName]);
			
		}
		
		echo $output;

	}
}

?>
