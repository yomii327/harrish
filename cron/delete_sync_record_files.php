<?php set_time_limit(3600);
require_once('../includes/property.php');
define("STOREFOLDER", './deadlock', true);
define("EXPORTFOLDERPATH", '../sync/Export/', true);//Export Foder Path
define("IMPORTFOLDERPATH", './sync/Import/', true);//Import Foder Path
if(!is_dir(STOREFOLDER))	@mkdir(STOREFOLDER, 0777);

if(!file_exists(STOREFOLDER.'/exportFolderDelete.txt')){
	$dbConn = mysql_connect(DEF_HOST, DEF_USER, DEF_PASSWORD) or die('Server Not Found 404');
	mysql_select_db(DEF_DBNAME, $dbConn);
	mysql_query("set time_zone='Australia/Melbourne'");
	
	$fh = fopen(STOREFOLDER.'/exportFolderDelete.txt', 'w') or die("can't open file");
	fwrite($fh, date('Y-m-d h:i:s a'));
	fclose($fh);
//Data collection part start here
	$curDateData = getRecordByQuery('SELECT NOW() AS date');
	$curDate = $curDateData[0]['date'];//Current time from server

	$historyData = selQRYMultiple('export_files_id, path, userid, exportDataType', 'exportData', 'created_date <= "2014-08-03 00:00:00"');
	if(isset($historyData)){
		foreach($historyData as $histData){
			$filePath1 = EXPORTFOLDERPATH.$histData['userid'].'/text_'.$histData['export_files_id'].'.zip';
			$filePath2 = EXPORTFOLDERPATH.$histData['userid'].'/signoff_'.$histData['export_files_id'].'.zip';
			$filePath3 = EXPORTFOLDERPATH.$histData['userid'].'/drawing_'.$histData['export_files_id'].'.zip';
			$filePath4 = EXPORTFOLDERPATH.$histData['userid'].'/image_'.$histData['export_files_id'].'.zip';
			$filePath5 = EXPORTFOLDERPATH.$histData['userid'].'/drawingmgmt_'.$histData['export_files_id'].'.zip';

			if (file_exists ($filePath1)){
				echo $filePath1;
				unlink($filePath1);
			//	die;
			}
			if (file_exists ($filePath2)){
				unlink($filePath2);
			//	die;
			}
			if (file_exists ($filePath3)){
				unlink($filePath3);
			//	die;
			}
			if (file_exists ($filePath4)){
				unlink($filePath4);
			//	die;
			}
			if (file_exists ($filePath5)){
				unlink($filePath5);
			//	die;
			}
		}
	}

	$historyData = selQRYMultiple('import_files_id, path, userid, importDataType', 'importData', 'created_date <= "2014-07-15 00:00:00"');
	
	if(isset($historyData)){
		foreach($historyData as $histData){
			$filePath = IMPORTFOLDERPATH.$histData['userid'].'/*'.$histData['import_files_id'].'.zip';
			//if (file_exists ($filePath))
			unlink($filePath);
		}
	}

	unlink(STOREFOLDER.'/exportFolderDelete.txt');	
}else{
	file_put_contents('exportFolderDelete_log.txt', date('Y-m-d h:i:s').'<===>Another one progess is going on please try again letter !<===>', FILE_APPEND);
}

//Function Section Start Here
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
function getRecordByQuery($query){
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
//Function Section End Here
?>