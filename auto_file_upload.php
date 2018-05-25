<?php
session_start();
$path = $_COOKIE['path'];
$name='';$fileSize='';$output='';
include('includes/commanfunction.php');
$obj = new COMMAN_Class();

if(isset($_GET['uniqueID'])){
	switch($_GET['action']){
		case 'imageOne':
			$fileElementName = 'image1';
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
			}else{
				#Create directory when not exists.
				$dir = 'inspections';
				if(!is_dir($dir)) {
					@mkdir($dir, 0777, true);
				}
				$dir = $dir.'/photo';
				if(!is_dir($dir)) {					
					@mkdir($dir, 0777, true);
				}
				$ext = explode('.', $_FILES[$fileElementName]['name']);
				$name = $_SESSION['idp'].'_'.time().'.'.$ext[1];
				if($obj->resizeImages($_FILES[$fileElementName]["tmp_name"], 799, 799, 'inspections/photo/'.$name)){
					$output = '<img src="inspections/photo/'.$name.'" width="100" height="90" style="margin-left:10px;margin-top:8px;"  /><input type="hidden" name="photo[]" value="'.$name.'" />';
					@unlink($_FILES[$fileElementName]);
				}	
			}
			echo $output;
		break;
		case 'imageTwo':
			$fileElementName = 'image2';
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
			}else{
				#Create directory when not exists.
				$dir = 'inspections';
				if(!is_dir($dir)) {
					@mkdir($dir, 0777, true);
				}
				$dir = $dir.'/photo';
				if(!is_dir($dir)) {
					@mkdir($dir, 0777, true);
				}
				$ext = explode('.', $_FILES[$fileElementName]['name']);
				$name = $_SESSION['idp'].'_'.time().'.'.$ext[1];
				if($obj->resizeImages($_FILES[$fileElementName]["tmp_name"], 799, 799, 'inspections/photo/'.$name)){
					$output = '<img src="inspections/photo/'.$name.'" width="100" height="90" style="margin-left:10px;margin-top:8px;"  /><input type="hidden" name="photo[]" value="'.$name.'" />';
					@unlink($_FILES[$fileElementName]);
				}	
			}
			echo $output;
		break;
		case 'drawing':
			$fileElementName = 'drawing';
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
			}else{
				#Create directory when not exists.
				$dir = 'inspections';
				if(!is_dir($dir)) {
					@mkdir($dir, 0777, true);
				}
				$dir = $dir.'/drawing';
				if(!is_dir($dir)) {
					@mkdir($dir, 0777, true);
				}
				$ext = explode('.', $_FILES[$fileElementName]['name']);
				$name = $_SESSION['idp'].'_'.time().'.'.$ext[1];
				if($obj->resizeImages($_FILES[$fileElementName]["tmp_name"], 799, 799, 'inspections/drawing/'.$name)){
					$output = '<img src="inspections/drawing/'.$name.'" width="100" height="90" style="margin-left:10px;margin-top:8px;"  /><input type="hidden" name="drawing" value="'.$name.'" />';
					@unlink($_FILES[$fileElementName]);
				}	
			}
			echo $output;
		break;

		case 'otherLogo':
			$fileElementName = 'otherLogo';
			$projId = $_REQUEST['projId'];

			//echo "<pre> =====>>"; print_r($_FILES); echo "<<<====="; die;

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
			}else{      
				$ext = end(explode('.', $_FILES[$fileElementName]['name']));
				#$name = $_SESSION['idp'].'_logo'.'.'.$ext;
				$name = mktime().'_logo'.'.'.$ext;
				// echo "<pre> =====>>"; print_r($name); die;
				if($obj->resizeImages($_FILES[$fileElementName]["tmp_name"], 350, 150, 'company_logo/'.$name)){
					$output = '<img src="company_logo/'.$name.'" width="100" height="90" style="margin-left:2px;margin-top:8px;" id="photoI'.substr($fileElementName,1).'" /><input type="hidden" name="logo" value="'.$name.'" />';
					@unlink($_FILES[$fileElementName]);
				}
			}
			echo $output;
		break;

		case "image0":
			$fileFieldName = 'image0';
			if(isset($_FILES[$fileFieldName])){
				$files = $_FILES[$fileFieldName];
				$error = $files['error'];
				switch($error) {
					case 1:
						echo 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case 2:
						echo 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case 3:
						echo 'The uploaded file was only partially uploaded';
						break;
					case 4:
						echo 'No file was uploaded';
						break;
					case 5:
						echo 'There is error, the file uploaded with failur';
						break;
					case 6:
						echo 'Missing a temporary folder';
						break;
					case 7:
						echo 'Failed to write file to disk';
						break;
					case 8:
						echo 'A PHP extension stopped the file upload';
						break;
					default :
						$ext = explode('.', $files['name']);
						$extArray = array('jpg', 'jpeg', 'png', 'gif','JPG', 'JPEG', 'PNG', 'GIF');
						$fileSize = $files['size'];
						if($fileSize > 2048000){
							echo "Uploaded file cross 2 Mb uploading limit ! " . $fileSize;
						} else {
							if(in_array($ext[1], $extArray)){
								$dirName = 'inspections/ncr_files';
								if(!is_dir($dirName)) {
									mkdir($dirName, 0777, true);
								}							
								$name = $_REQUEST['proid'].'_'.substr(microtime(), -6, -1).rand(0,99).'.'.$ext[1];
								$upload = move_uploaded_file($files['tmp_name'], $dirName."/".$name);
								if($upload) {								
									$output = '<img src="'.$dirName.'/'.$name.'" alt="image" />';
									$output .= '<input type="hidden" name="image_name[0]" value="'. $name .'" />';
								}
								echo $output;
							}else{
								echo "Uploaded file format not valid valid format is jpg, jpeg, png, gif!";
							}
						}
					break;
				}
			} else {
				echo 'No file was uploaded';
			}
		break;
		case "image1":
			$fileFieldName = 'image1';
			if(isset($_FILES[$fileFieldName])){
				$files = $_FILES[$fileFieldName];
				$error = $files['error'];
				switch($error) {
					case 1:
						echo 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case 2:
						echo 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case 3:
						echo 'The uploaded file was only partially uploaded';
						break;
					case 4:
						echo 'No file was uploaded';
						break;
					case 5:
						echo 'There is error, the file uploaded with failur';
						break;
					case 6:
						echo 'Missing a temporary folder';
						break;
					case 7:
						echo 'Failed to write file to disk';
						break;
					case 8:
						echo 'A PHP extension stopped the file upload';
						break;
					default :
						$ext = explode('.', $files['name']);
						$extArray = array('jpg', 'jpeg', 'png', 'gif','JPG', 'JPEG', 'PNG', 'GIF');
						$fileSize = $files['size'];
						if($fileSize > 2048000){
							echo "Uploaded file cross 2 Mb uploading limit ! " . $fileSize;
						} else {
							if(in_array($ext[1], $extArray)){
								$dirName = 'inspections/ncr_files';
								if(!is_dir($dirName)) {
									mkdir($dirName, 0777, true);
								}							
								$name = $_REQUEST['proid'].'_'.substr(microtime(), -6, -1).rand(0,99).'.'.$ext[1];
								$upload = move_uploaded_file($files['tmp_name'], $dirName."/".$name);
								if($upload) {								
									$output = '<img src="'.$dirName.'/'.$name.'" alt="image" />';
									$output .= '<input type="hidden" name="image_name[1]" value="'. $name .'" />';
								}
								echo $output;
							}else{
								echo "Uploaded file format not valid valid format is jpg, jpeg, png, gif!";
							}
						}
					break;
				}
			} else {
				echo 'No file was uploaded';
			}
		break;
		case "image2":
		case "image3":
			$fileFieldName = $_GET['action'];
			if(isset($_FILES[$fileFieldName])){
				$files = $_FILES[$fileFieldName];
				$error = $files['error'];
				switch($error) {
					case 1:
						echo 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case 2:
						echo 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case 3:
						echo 'The uploaded file was only partially uploaded';
						break;
					case 4:
						echo 'No file was uploaded';
						break;
					case 5:
						echo 'There is error, the file uploaded with failur';
						break;
					case 6:
						echo 'Missing a temporary folder';
						break;
					case 7:
						echo 'Failed to write file to disk';
						break;
					case 8:
						echo 'A PHP extension stopped the file upload';
						break;
					default :
						$ext = explode('.', $files['name']);
						$extArray = array('jpg', 'jpeg', 'png', 'gif','JPG', 'JPEG', 'PNG', 'GIF');
						$fileSize = $files['size'];
						if($fileSize > 2048000){
							echo "Uploaded file cross 2 Mb uploading limit ! " . $fileSize;
						} else {
							if(in_array($ext[1], $extArray)){
								$dirName = 'inspections/ncr_files';
								if(!is_dir($dirName)) {
									mkdir($dirName, 0777, true);
								}							
								$name = $_REQUEST['proid'].'_'.substr(microtime(), -6, -1).rand(0,99).'.'.$ext[1];
								$upload = move_uploaded_file($files['tmp_name'], $dirName."/".$name);
								if($upload) {								
									$output = '<img src="'.$dirName.'/'.$name.'" alt="image" />';
									$output .= '<input type="hidden" name="image_name[]" value="'. $name .'" />';
								}
								echo $output;
							}else{
								echo "Uploaded file format not valid valid format is jpg, jpeg, png, gif!";
							}
						}
					break;
				}
			} else {
				echo 'No file was uploaded';
			}
		break;

		case "imageCompose":
			$fileFieldName = 'imageCompose';
			if(isset($_FILES[$fileFieldName])){
				$files = $_FILES[$fileFieldName];
				$error = $files['error'];
				switch($error) {
					case 1:
						echo 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case 2:
						echo 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case 3:
						echo 'The uploaded file was only partially uploaded';
						break;
					case 4:
						echo 'No file was uploaded';
						break;
					case 5:
						echo 'There is error, the file uploaded with failur';
						break;
					case 6:
						echo 'Missing a temporary folder';
						break;
					case 7:
						echo 'Failed to write file to disk';
						break;
					case 8:
						echo 'A PHP extension stopped the file upload';
						break;
					default :
						$ext = explode('.', $files['name']);
						$extArray = array('jpg', 'jpeg', 'png', 'gif','JPG', 'JPEG', 'PNG', 'GIF');
						$fileSize = $files['size'];
						if($fileSize > 2048000){
							echo "Uploaded file cross 2 Mb uploading limit ! " . $fileSize;
						} else {
							if(in_array($ext[1], $extArray)){
								$dirName = 'attachment';
								if(!is_dir($dirName)) {
									mkdir($dirName, 0777, true);
								}							
								$name = $_SESSION['idp'].'_'.substr(microtime(), -6, -1).rand(0,99).'.'.end($ext);
								$upload = move_uploaded_file($files['tmp_name'], $dirName."/".$name);
								if($upload) {								
									$output = '<img src="'.$dirName.'/'.$name.'" alt="image" />';
									$output .= '<input type="hidden" name="image_compose" id="image_compose" value="'. $name .'" />';
								}
								echo $output;
							}else{
								echo "Uploaded file format not valid valid format is jpg, jpeg, png, gif!";
							}
						}
					break;
				}
			} else {
				echo 'No file was uploaded';
			}
		break;
		
		case "evidence_sign":
			$fileFieldName = 'evidence_sign';
			if(isset($_FILES[$fileFieldName])){
				$files = $_FILES[$fileFieldName];
				$error = $files['error'];
				switch($error) {
					case 1:
						echo 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case 2:
						echo 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case 3:
						echo 'The uploaded file was only partially uploaded';
						break;
					case 4:
						echo 'No file was uploaded';
						break;
					case 5:
						echo 'There is error, the file uploaded with failur';
						break;
					case 6:
						echo 'Missing a temporary folder';
						break;
					case 7:
						echo 'Failed to write file to disk';
						break;
					case 8:
						echo 'A PHP extension stopped the file upload';
						break;
					default :
						$ext = explode('.', $files['name']);
						$extArray = array('jpg', 'jpeg', 'png', 'gif','JPG', 'JPEG', 'PNG', 'GIF');
						$fileSize = $files['size'];
						if($fileSize > 2048000){
							echo "Uploaded file cross 2 Mb uploading limit ! " . $fileSize;
						} else {
							if(in_array($ext[1], $extArray)){
								$dirName = 'inspections/ncr_files';
								if(!is_dir($dirName)) {
									mkdir($dirName, 0777, true);
								}							
								$name = $_SESSION['idp'].'_'.substr(microtime(), -6, -1).rand(0,99).'.'.end($ext);
								$upload = move_uploaded_file($files['tmp_name'], $dirName."/".$name);
								if($upload) {								
									$output = '<img src="'.$dirName.'/'.$name.'" alt="image" />';
									$output .= '<input type="hidden" name="evidence_sign" id="evidence_sign" value="'. $name .'" />';
								}
								echo $output;
							}else{
								echo "Uploaded file format not valid valid format is jpg, jpeg, png, gif!";
							}
						}
					break;
				}
			} else {
				echo 'No file was uploaded';
			}
		break;
	}
}
?>
