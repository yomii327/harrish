<?php 
ob_start();
include_once("../includes/functions.php");
include_once("SimpleImage.php");

$object= new DB_Class();

class QRY_Class{
	function getRecordByKey($table, $searchKey, $searchValue, $expValue){
#echo "SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."'";
		$RS = mysql_query("SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."' and is_deleted=0");
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				$values[]= $ROW;
			}
			return $values;
		}else{
			return false;
		}
	}
	
	function getRecordByQry($query){
		#echo $query;
		$RS = mysql_query($query);
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				$values[]= $ROW;
			}
			return $values;
		}else{
			return false;
		}
	}

	function selQRYMultiple($select, $table, $where){
#echo "SELECT ".$select." FROM ".$table." WHERE ".$where;
		$RS = mysql_query("SELECT ".$select." FROM ".$table." WHERE ".$where);
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				$values[]= $ROW;
			}
			return $values;
		}else{
			return false;
		}
	}	

	function queryFilter($str){
		if(get_magic_quotes_gpc()){
			$str = stripslashes($str);
		}
		$removeWords = array("/delete/i", "/update/i","/union/i","/insert/i","/drop/i","/http/i","/--/i");
		$str = preg_replace($removeWords, "", $str);
		if (phpversion() >= '4.3.0'){
			$str = mysql_real_escape_string($str);
		}else{
			$str = mysql_escape_string($str);
		}
		return $str;
	}

	function selQRY($select, $table, $where){
//echo "SELECT ".$select." FROM ".$table." WHERE ".$where;
	$RS = mysql_query("SELECT ".$select." FROM ".$table." WHERE ".$where);
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				return $ROW;
			}
		}else{
			return false;
		}
	}	

	function hashAuth($gId){
		$authRs = mysql_query("SELECT user_id FROM `user` WHERE user_id = '".$gId."'");
		if(mysql_num_rows($authRs) > 0){
				return true;
		}else{
			return false;
		}
	}

	function dataFilter($string){
		//$string = urlencode ($string)
		if($string != ''){
			$charArray = array(
				'/<br \/>/',
				'/\\r\\n/',
				'/\\r/',
				'/\\n/',
				'/\\t/',
				'/\"/'
			);
			//$string = preg_replace(array("'"), "\\'", $string);
			return stripcslashes(preg_replace($charArray, ' ', strip_tags(trim($string))));
			/*$charArray = array(
				'/\'/'
			);
			return preg_replace($charArray, '\\\'', strip_tags(trim($string)));*/
		}
	}	
	
	function dataFilterUpload($string){
		if($string != ''){
			$charArray = array(
				'/<br \/>/',
				'/\\r\\n/',
				'/\\r/',
				'/\\n/',
				'/\\t/'
			);
			$str = preg_replace($charArray, ' ', strip_tags(trim($string)));
			return str_replace ("\"", "", $str);
			//return $str;
			/*$charArray = array(
				'/\'/'
			);
			return preg_replace($charArray, '\\\'', strip_tags(trim($string)));*/
		}
	}	

	function create_zip($files = array(), $destination = '', $overwrite = true) {
		if(file_exists($destination) && !$overwrite) { return false; }
		$valid_files = array();
		if(is_array($files)) {
			foreach($files as $file) {
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		if(count($valid_files)) {
			$zip = new ZipArchive();
			if($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			foreach($valid_files as $file) {
				$zip->addFile($file, $file);
			}
			//debug
			//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
			//close the zip -- done!
			$zip->close();
			return file_exists($destination);
		}else{
			return false;
		}
	}

	function recurse_zip($src, &$zip, $path){
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->recurse_zip($src . '/' . $file,$zip,$path);
                }else{
					$zip->addFile($src . '/' . $file,substr($src . '/' . $file,$path));
                }
            }
		}
		closedir($dir);
	}

	function compress($src, $fileName, $dst=''){
		if(substr($src,-1)==='/'){$src=substr($src,0,-1);}
		if(substr($dst,-1)==='/'){$dst=substr($dst,0,-1);}
        $path=strlen(dirname($src).'/');
		$filename = $fileName.'.zip';
		#$filename = substr($src, strrpos($src,'/')+1).'.zip';
		$dst=empty($dst)? $filename : $dst.'/'.$filename;
		@unlink($dst);
        $zip = new ZipArchive;
        $res = $zip->open($dst, ZipArchive::CREATE);
        if($res !== TRUE){
			echo 'Error: Unable to create zip file';
			exit;
		}
        if(is_file($src)){
			$zip->addFile($src, substr($src,$path));
		}else{
			if(!is_dir($src)){
				$zip->close();
				@unlink($dst);
				echo 'Error: File not found';
				exit;
			}
			$this->recurse_zip($src,$zip,$path);
		}
        $zip->close();
        return $dst;
	}

	function recursive_remove_directory($directory, $empty=FALSE){
		if(substr($directory,-1) == '/'){
			$directory = substr($directory,0,-1);
		}
		if(!file_exists($directory) || !is_dir($directory)){
			return FALSE;
		}elseif(is_readable($directory)){
			$handle = opendir($directory);
			while (FALSE !== ($item = readdir($handle))){
				if($item != '.' && $item != '..'){
					$path = $directory.'/'.$item;
					if(is_dir($path)){
						$this->recursive_remove_directory($path);
					}else{
						unlink($path);
					}
				}
			}
			closedir($handle);
			if($empty == FALSE){
				if(!rmdir($directory)){
					return FALSE;
				}
			}
		}
		return TRUE;
	}

	function updateExportTable($path, $userId, $deviceType){
		$rs = mysql_query("INSERT INTO `exportData` (path, created_date, userid, device) values ('".$path."', now(), ".$userId.", '".$deviceType."')");
		return mysql_insert_id();
	}

	function updateExportTable_new($path, $userId, $deviceType, $dataType){
		$rs = mysql_query("INSERT INTO `exportData` (path, created_date, userid, device, exportDataType) values ('".$path."', now(), ".$userId.", '".$deviceType."', '".$dataType."')");
		return mysql_insert_id();
	}

	function updateExportTable_updated($path, $userId, $deviceType, $dataType, $syncUrl){
		$rs = mysql_query("INSERT INTO `exportData` (path, created_date, userid, device, exportDataType, sync_url) values ('".$path."', now(), ".$userId.", '".$deviceType."', '".$dataType."', '".$syncUrl."')");
		return mysql_insert_id();
	}

	function updateImportTable($path, $userId, $deviceType){
		$rs = mysql_query("INSERT INTO `importData` (path, created_date, userid, device) values ('".$path."', now(), ".$userId.", '".$deviceType."')");
		return mysql_insert_id();
	}

	function updateImportTable_new($path, $userId, $deviceType, $dataType){
		$rs = mysql_query("INSERT INTO `importData` (path, created_date, userid, device, importDataType) values ('".$path."', now(), ".$userId.", '".$deviceType."', '".$dataType."')");
		return mysql_insert_id();
	}

	function updateImportTable_updated($path, $userId, $deviceType, $dataType, $syncUrl){
		$rs = mysql_query("INSERT INTO `importData` (path, created_date, userid, device, importDataType, sync_url) values ('".$path."', now(), ".$userId.", '".$deviceType."', '".$dataType."', '".$syncUrl."')");
		return mysql_insert_id();
	}

	function createFile($fileName, $fileContent, $path, $mode = 'a+'){
		$fh = fopen($path.$fileName, $mode) or die("can't open file");
		fwrite($fh, $fileContent);
		fclose($fh);
	}

	function emptyDirectory($directoryPath){
		$directory = dir($directoryPath);
		while((FALSE !== ($item = $directory->read())) && (!isset($directory_not_empty))){
			if($item != '.' && $item != '..'){
				$directory_not_empty = TRUE;
			}
		}
		$directory->close();
		if($directory_not_empty){
			return true;
		}else{
			return false;
		}
	}

	function extractZip($filePath, $extractPath){
		//$extractPath = Path Begain with current directory
		$zip = new ZipArchive;
		$res = $zip->open($filePath);
		if($res === TRUE){
			$zip->extractTo($extractPath);
			$zip->close();
			return true;
		}else{
			return false;
		}
	}

	function validateMySqlDate($date){
		if(strlen($date) > 19){
			$date = substr($date, -19);
			$dateArray = explode('-', $date);
			if(substr($dateArray[0], 0, 2) != 20){
				$dateArray[0] = '20'.substr($dateArray[0], 2, 2);
				$date = implode('-', $dateArray);
			}
		}
		if(preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $date)){
			return true;
		}else{
			return false;
		}
	}

	function copyFilestoFolder($sFolder, $dFolder, $width, $height){
		$folder = opendir($sFolder.'/');
		$file_types = array("jpg", "jpeg", "gif", "png", "txt", "ico");
		#$indexFiles = array();//Store File if you want in this array
		while($file = readdir($folder)){
			if(in_array(substr(strtolower($file), strrpos($file,".") + 1), $file_types)){
				#copy($sFolder.'/'.$file, $dFolder.'/'.$file);
				//$this->resizeImages($sFolder.'/'.$file, $width, $height, $dFolder.'/'.$file);
				$this->resizeImagesSunc($sFolder.'/'.$file, $width, $height, $dFolder.'/'.$file);
			}
		}
		return true;
	}

	function sendMail($to, $from, $message){
		$subject = 'Wiseworker| Defectld Inspection';
		$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
		// More headers
		$headers .= 'From: <$from>' . "\r\n";
		mail($to, $subject, $message, $headers);
	}
	
	function resizeImages($resizeSource, $rWidth, $rHeight, $resizeDestination){
		$simpleImage = new SimpleImage();
		if (file_exists($resizeDestination))
		{
			unlink($resizeDestination);
		}
		if(file_exists($resizeSource)){
			$simpleImage->load($resizeSource);
			
			if($simpleImage->getWidth() >= $rWidth){
				if($simpleImage->getWidth() < $rWidth){//Resize by Height
					$simpleImage->resizeToHeight($rHeight);
					if ($simpleImage->getWidth() > $rWidth)
					{
						$simpleImage->resizeToWidth($rWidth);
					}
					$simpleImage->save($resizeDestination);
				}else{//Resize by widtht
					$simpleImage->resizeToWidth($rWidth);
					if ($simpleImage->getHeight() > $rHeight)
					{
						$simpleImage->resizeToHeight($rHeight);
					}
					$simpleImage->save($resizeDestination);
				}
			}else{
				$simpleImage->save($resizeDestination);
			}
		}
		return true;
	}
	
	function resizeImagesSunc($resizeSource, $rWidth, $rHeight, $resizeDestination){
		$simpleImage = new SimpleImage();
		if(file_exists($resizeSource)){
			if (file_exists($resizeDestination))
			{
				unlink($resizeDestination);
			}
			$simpleImage->load($resizeSource);
			if($simpleImage->getWidth() >= $rWidth){
				if($simpleImage->getWidth() < $rWidth){//Resize by Height
					$simpleImage->resizeToHeight($rHeight);
					if ($simpleImage->getWidth() > $rWidth){
						$simpleImage->resizeToWidth($rWidth);
					}
					$simpleImage->save($resizeDestination);
					if(!file_exists($resizeDestination)){
						copy($resizeSource, $resizeDestination);
					}
				}else{//Resize by widtht
					$simpleImage->resizeToWidth($rWidth);
					if ($simpleImage->getHeight() > $rHeight){
						$simpleImage->resizeToHeight($rHeight);
					}
					$simpleImage->save($resizeDestination);
					if(!file_exists($resizeDestination)){
						copy($resizeSource, $resizeDestination);
					}
				}
			}else{
				copy($resizeSource, $resizeDestination);
#				$simpleImage->save($resizeDestination);
			}
		}
		return true;
	}
	
	function curPageURL($array){
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		if(is_array($array)){
			$pam = '';
			$pamKey = array_keys($array);
			for($i=0;$i<sizeof($pamKey);$i++){
				if($pam == ''){
					$pam = $pamKey[$i].'='.$array[$pamKey[$i]];
				}else{
					$pam .= '&'.$pamKey[$i].'='.$array[$pamKey[$i]];
				}
			}
			if($pam != ''){
				$pageURL = $pageURL.$pam;
			}
		}
		return $pageURL;
	}
	
	function bulkInsert($insertArray, $colString, $table){//$insertArray is multidimension indexed array
		$objj = new DB_Class();
		$loopCount = count(explode(',', $colString));
		$recCount = sizeof($insertArray);
		$valStr = '';
		for($j=0; $j<$recCount; $j++){
			for($i=0; $i<$loopCount; $i++){
				if($valStr == ''){
					$valStr = '"'.trim($insertArray[$j][$i]).'"';
				}else{
					$valStr .= ', "'.trim($insertArray[$j][$i]).'"';
				}
			}
			if($valString == ''){
				$valString = 'SELECT '.$valStr;
			}else{
				$valString .= ' UNION ALL SELECT '.$valStr;
			}
			$valStr = '';
		}
		$insertQRY = 'INSERT INTO '.$table.' ('.$colString.') '.$valString;
		$insertQRYNew = str_replace(array('"Now()",', '"now()",', '"NOW()",'), array('NOW(),', 'NOW(),', 'NOW(),'), $insertQRY);
		#echo $insertQRYNew;die;
		$res = $objj->db_query($insertQRYNew);
		if($res){	return true;	}else{	return false;	}
	}

	function getCurrentDateTime(){
		$rsInspectionInspectedBy = mysql_query("SELECT NOW() as date");
		if(mysql_num_rows($rsInspectionInspectedBy) > 0){
			$iPadQueryInspectionInspectedBy = '';
			if($rowInspectionInspectedBy = mysql_fetch_assoc($rsInspectionInspectedBy)){
				$date_lmd = $rowInspectionInspectedBy["date"];
			}
		}
		return $date_lmd;
	}
	
	function subLocations($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCat($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getRecordByKey("project_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getRecordByKey("project_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function getCat($catId){
		$qry = 'select location_id, location_parent_id from project_locations where location_id ='.$catId.' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			$path[] = $row->location_id;
			$path = array_merge($this->getCat($row->location_parent_id), $path);
		}
		return $path;
	}
	
	function subLocationsId($subLocation, $saprater){
		$breadcrumb = $subLocation;
		$depth = $this->getCatIds($subLocation);
		foreach($depth as $dp){
			$breadcrumb .= $saprater . $dp;
		}
		return $breadcrumb;
	}

	function getCatIds($catId){
		$qry = 'select location_id from project_locations where location_parent_id ='.$catId.' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		while($rows = mysql_fetch_array($res)){
			$path[] = $rows[0];
			$path = array_merge($this->getCatIds($rows[0]), $path);
		}
		return $path;
	}

}

?>
