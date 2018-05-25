<?php 

ob_start();

include_once("functions.php");

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

	function selQRYMultiple($select, $table, $where){
	//	echo "SELECT ".$select." FROM ".$table." WHERE ".$where.'<BR />';
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

				'/"/'

			);
			//$string = preg_replace(array("'"), "\\'", $string);
			
			return preg_replace($charArray, ' ', strip_tags(trim($string)));

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

	

	function updateImportTable($path, $userId, $deviceType){

		$rs = mysql_query("INSERT INTO `importData` (path, created_date, userid, device) values ('".$path."', now(), ".$userId.", '".$deviceType."')");

		return mysql_insert_id();

	}



	function createFile($fileName, $fileContent, $path){

		$fh = fopen($path.$fileName, 'a+') or die("can't open file");

		fwrite($fh, utf8_encode($fileContent));

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

		if(preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $date)){

			return true;

		}else{

			return false;

		}

	}

	

	function copyFilestoFolder($sFolder, $dFolder){
		$folder = opendir($sFolder.'/');
		$file_types = array("jpg", "jpeg", "gif", "png", "txt", "ico");
		#$indexFiles = array();//Store File if you want in this array
		while($file = readdir($folder)){
			if(in_array(substr(strtolower($file), strrpos($file,".") + 1), $file_types)){
				echo $sFolder.'/'.$file.'---'.$dFolder.'/'.$file;
				copy($sFolder.'/'.$file, $dFolder.'/'.$file);
			}
		}
		return true;
	}

}

?>