<?php 
include_once("functions.php");
include_once("SimpleImage.php");

$object= new DB_Class();
class COMMAN_Class{
	function getDataByKey($table, $searchKey, $searchValue, $expValue){
		//echo "SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."' and is_deleted = 0";
		$RS = mysql_query("SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."' and is_deleted = 0");
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_array($RS)){
				return $ROW[0];
			}
		}else{
			return false;
		}
	}	

	function selQRY($select, $table, $where, $flag='No'){
		if($flag=='Yes'){
			echo "SELECT ".$select." FROM ".$table." WHERE ".$where;die;
		}
		$RS = mysql_query("SELECT ".$select." FROM ".$table." WHERE ".$where);
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				return $ROW;
			}
		}else{
			return false;
		}
	}	
	
	function selQRYMultiple($select, $table, $where){//,$tt
		//if($tt == 'TT'){echo "SELECT ".$select." FROM ".$table." WHERE ".$where; die;}
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
	
	function defthLocation($pId){
		$RS = mysql_query("SELECT location_title FROM ".PROJECTLOCATION." WHERE location_id = '".$pId."'");
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_array($RS)){
				return $ROW[0];
			}
		}else{
			return false;
		}
	}
	
	function getRecords($table, $searchKey, $searchValue, $searchKey1, $searchValue1, $expValue){
		//echo "SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."' and ".$searchKey1." = '".$searchValue1."' and is_deleted = '0'";
		$RS = mysql_query("SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."' and ".$searchKey1." = '".$searchValue1."' and is_deleted = '0'");
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				$values[]= $ROW;
			}
			return $values;
		}else{
			return false;
		}
	}
	
	function getRecordsSp($table, $searchKey, $searchValue, $expValue){
		$RS = mysql_query("SELECT ".$expValue." FROM ".$table." WHERE ".$searchKey." = '".$searchValue."' and is_deleted = '0'");
		if(mysql_num_rows($RS) > 0){
			while($ROW = mysql_fetch_assoc($RS)){
				$values[]= $ROW;
			}
			return $values;
		}else{
			return false;
		}
	}
	
	function getCat($catId){
		$qry = 'select location_id, location_parent_id from project_locations where location_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			#if($row->location_parent_id != 0){
				$path[] = $row->location_id;
				$path = array_merge($this->getCat($row->location_parent_id), $path);
/*			}else{
				$path = array_merge(array($row->location_id), $path);
			}*/
		}
		return $path;
	}

	function getCatIds($catId){
		$qry = 'select location_id from project_locations where location_parent_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		while($rows = mysql_fetch_array($res)){
			$path[] = $rows[0];
			$path = array_merge($this->getCatIds($rows[0]), $path);
		}
		return $path;
	}
	
	function getCatIdsExport($catId){
		$qry = 'select location_id from project_locations where location_parent_id ='.$catId.' and is_deleted=0';
		$res = mysql_query($qry);
		$path = '';
		while($rows = mysql_fetch_array($res)){
			$path .= $rows[0];
			$path .= " > " . $this->getCatIdsExport($rows[0]);
		}
		return $path;
	}

	function getCatIdsProgressMonitoring($catId){
		$qry = 'select location_id from project_monitoring_locations where location_parent_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		while($rows = mysql_fetch_array($res)){
			$path[] = $rows[0];
			$path = array_merge($this->getCatIdsProgressMonitoring($rows[0]), $path);
		}
		return $path;
	}
	
	function subLocations($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCat($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("project_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("project_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function subLocationsIDS($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCat($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $dp;
			}else{
				$breadcrumb .=  $saprater . $dp;
			}
		}
		return $breadcrumb;
	}

	function subLocationsProgressMonitoring($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatProgressMonitoring($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("project_monitoring_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("project_monitoring_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function getCatProgressMonitoring($catId){
		$qry = 'select location_id, location_parent_id from project_monitoring_locations where location_id ='.$catId . ' and is_deleted=0 and location_parent_id!=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			#if($row->location_parent_id != 0){
				$path[] = $row->location_id;
				$path = array_merge($this->getCatProgressMonitoring($row->location_parent_id), $path);
/*			}else{
				$path = array_merge(array($row->location_id), $path);
			}*/
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

	function subLocationsIdProgressMonitoring($subLocation, $saprater){
		$breadcrumb = $subLocation;
		$depth = $this->getCatIdsProgressMonitoring($subLocation);
		foreach($depth as $dp){
			$breadcrumb .= $saprater . $dp;
		}
		return $breadcrumb;
	}
	
	function imageExistsFolder($folderName, $fileName){
		$folder = opendir($folderName.'/');
#		echo $folderName.''.$fileName;
		$file_types = array("jpg", "jpeg", "gif", "png", "txt", "ico");
		while($file = readdir($folder)){
			if(in_array(substr(strtolower($file), strrpos($file,".") + 1), $file_types)){
				if($fileName == $file){
					return true;
				}else{
					return false;
				}
			}
		}
	}
	
	function resizeImages($resizeSource, $rWidth, $rHeight, $resizeDestination){
		$simpleImage = new SimpleImage();
		if(file_exists($resizeSource)){
			$simpleImage->load($resizeSource);
			if($simpleImage->getWidth() >= $rWidth){
				if($simpleImage->getWidth() < $rWidth){//Resize by Height
					$simpleImage->resizeToHeight($rHeight);
					if ($simpleImage->getWidth() > $rWidth)
					{
						$simpleImage->resizeToWidth($rWidth);
					}
					$simpleImage->save($resizeDestination, IMAGETYPE_PNG);
				}else{//Resize by widtht
					$simpleImage->resizeToWidth($rWidth);
					if ($simpleImage->getHeight() > $rHeight)
					{
						$simpleImage->resizeToHeight($rHeight);
					}
					$simpleImage->save($resizeDestination, IMAGETYPE_PNG);
				}
			}else{
				$simpleImage->save($resizeDestination, IMAGETYPE_PNG);
			}
		}
		return true;
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
				$tmp = explode("/", $file);
				$filename = $tmp[count($tmp)-1];
				$zip->addFile($file, $filename);
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
	
	function dateChanger($cDelimeter, $eDelimeter, $cDate){
		$pDate = explode($cDelimeter, $cDate);
		$rDate = $pDate[2].$eDelimeter.$pDate[1].$eDelimeter.$pDate[0];
		return $rDate;
	}
	
	function searchArray($array, $val){
		$response = 0;
		foreach($array as $arr){
			if($val == $arr['project_id']){
				$response = 1;
			}
		}
		return $response;
	}
	
	function checklistStatus($projectID, $inspectionID){
		$rs = mysql_query("SELECT i.insepection_check_list_id, i.check_list_items_status, c.check_list_items_id FROM inspection_check_list as i, check_list_items as c WHERE i.project_id = ".$projectID." AND i.inspection_id = ".$inspectionID." AND i.is_deleted = 0 AND c.is_deleted = 0 AND i.check_list_items_status = 'NA' AND c.check_list_items_id = i.check_list_items_id");
#		print_r($checkListItemData);
		if(mysql_num_rows($rs) > 0){
			return 1;
		}else{
			return 0;
		}
	}
	
	function checklist($projectID, $inspectionID){
		$RS = mysql_query("SELECT insepection_check_list_id FROM inspection_check_list WHERE project_id = ".$projectID." AND inspection_id = ".$inspectionID." AND is_deleted = 0");
		if(mysql_num_rows($RS) > 0){
			return 1;
		}else{
			return 0;
		}
	}

	function checklist4Project($projectID){
		$RS = mysql_query("SELECT check_list_items_id FROM check_list_items WHERE project_id = ".$projectID." AND is_deleted = 0");
		if(mysql_num_rows($RS) > 0){
			return 1;
		}else{
			return 0;
		}
	}
	
	function recurtion($locationID, $projectID){
		$data='';
		$location = $this->selQRYMultiple('location_id, location_title', 'project_locations', 'location_parent_id = "'.$locationID.'" and is_deleted = "0" and project_id = "'.$projectID.'" order by location_title');
		if(!empty($location)){
			foreach($location as $loc){
				$data .= '<ul><li id="li_'.$locations['location_id'].'"><span class="jtree-button demo1" id="'.$locations['location_id'].'">'.stripslashes($locations['location_title']).'</span>';
				$data .= '</li></ul>';
				return $data;
			}
		}
	}
	
	function resizeImagesGeneral($resizeSource, $rWidth, $rHeight, $resizeDestination){
		$simpleImage = new SimpleImage();
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

	function getParentChapter($subLocation, $saprater, $col){
		$breadcrumb = '';
		$depth = $this->getParentID($subLocation);
		foreach($depth as $dp){
			if($col != 'Title'){
				if ($breadcrumb == ""){
					$breadcrumb = $dp;
				}else{
					$breadcrumb .= $saprater . $dp;
				}
			}else{
				if ($breadcrumb == ""){
					$breadcrumb = $this->getDataByKey("manual_chapter", "chapter_id", $dp, "chapter_title");
				}else{
					$breadcrumb .= $saprater . $this->getDataByKey("manual_chapter", "chapter_id", $dp, "chapter_title");
				}
			}
		}
		return $breadcrumb;
	}

	function getParentID($catId){
		$qry = 'select chapter_id, chpter_parent_id from manual_chapter where chapter_id ='.$catId.' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			#if($row->location_parent_id != 0){
				$path[] = $row->chapter_id;
				$path = array_merge($this->getParentID($row->chpter_parent_id), $path);
/*			}else{
				$path = array_merge(array($row->location_id), $path);
			}*/
		}
		return $path;
	}
	
	function statusChecker($chapetID, $projID){
		$stData = $this->selQRYMultiple('id, authority', 'manual_chapter_data', 'authority = "No" AND project_id = '.$projID.' AND chaper_id = '.$chapetID.' AND is_deleted = 0');
		if(!empty($stData)){
			if($stData[0]['authority'] == 'No'){
				return true;
			}
		}
		return false;
	}

	function getChapterIds($chId, $projID){
		$qry = 'SELECT chapter_id FROM manual_chapter WHERE chpter_parent_id ='.$chId.' AND project_id = '.$projID.' AND is_deleted = 0';
		$res = mysql_query($qry);
		$path = array();
		while($rows = mysql_fetch_array($res)){
			$path[] = $rows[0];
			$path = array_merge($this->getChapterIds($rows[0], $projID), $path);
		}
		return $path;
	}

	function subChapterID($chapter, $projID, $saprater){
		$breadcrumb = $chapter;
		$depth = $this->getChapterIds($chapter, $projID);
		foreach($depth as $dp){
			$breadcrumb .= $saprater . $dp;
		}
		return $breadcrumb;
	}
	
	function bulkInsert($insertArray, $colString, $table){//$insertArray is multidimension indexed array
		$objj = new DB_Class();
		$loopCount = count(explode(',', $colString));
		$recCount = sizeof($insertArray);
		$valStr = '';
		$valString = '';
		$count = 0;
		for($j=0; $j<$recCount; $j++){
			for($i=0; $i<$loopCount; $i++){
				if($valStr == ''){
					$valStr = '"'. addslashes (trim($insertArray[$j][$i])).'"';
				}else{
					$valStr .= ', "'. addslashes (trim($insertArray[$j][$i])).'"';
				}
			}
			if($valString == ''){
				$valString = 'SELECT '.$valStr;
			}else{
				$valString .= ' UNION ALL SELECT '.$valStr;
			}
			$valStr = '';
			$count ++;
			if ($count == 500){
				$insertQRY = 'INSERT INTO '.$table.' ('.$colString.') '.$valString;
				$insertQRYNew = str_replace(array('"Now()",', '"now()",', '"NOW()",'), array('NOW(),', 'NOW(),', 'NOW(),'), $insertQRY);
				$res = $objj->db_query($insertQRYNew);
				if (!$res){//echo mysql_error();//die;
				}
				$count = 0;
				$valString = "";
			}
		}
		if ($count < 500 && $count > 0){
			$insertQRY = 'INSERT INTO '.$table.' ('.$colString.') '.$valString;
			$insertQRYNew = str_replace(array('"Now()",', '"now()",', '"NOW()",'), array('NOW(),', 'NOW(),', 'NOW(),'), $insertQRY);
			$res = $objj->db_query($insertQRYNew);
			if (!$res){//echo mysql_error();
			}
		}
		return true;
	}
	
	function checkInsetIfNotExistLoc($tableName, $colName, $val, $colName1, $val1, $expColName, $builderID, $proID, $locArray){
		$values = '';
		$QURY = "SELECT ".$expColName." FROM ".$tableName." WHERE project_id = ".$proID." AND ".$colName." = '".$val."' AND ".$colName1." = '".$val1."' AND is_deleted = 0 LIMIT 0, 1";
		$RES = mysql_query($QURY);
		if(mysql_num_rows($RES) > 0){
			while($ROW = mysql_fetch_assoc($RES)){
				$values = $ROW[$expColName];
			}
		}
		if($values != ''){
			$locId = $values;
		}else{
			$insertQRY = "INSERT INTO ".$tableName." SET
							project_id = ".$proID.",
							".$colName." = ".trim($val).",
							".$colName1." = '".addslashes(trim($val1))."',
							last_modified_date = NOW(),
							last_modified_by = '".$builderID."',
							created_date = NOW(),
							created_by = '".$builderID."'";
			mysql_query($insertQRY);
			$locId = mysql_insert_id();
		}
		if($locArray == ''){
			return $locId;
		}else{
			$path = array();
			for($m=0; $m<sizeof($locArray); $m++){
				$path[] = $locId;
				$locName = $locArray[$m];
				$currLcation[] = $this->checkInsetIfNotExistLoc($tableName, $colName, $locId, 'location_title', $locArray[$m], 'location_id', $builderID, $proID, $locIdArray);
				$path = array_merge($currLcation, $path);
				$currLcation = array();
			}
			return $path;	
		}
		
	}
	
	function recursiveInsertLocation($locationArray, $parentId, $proID, $builderID, $rowLocationTree){
		$values = '';
		for ($i=0; $i<sizeof($locationArray); $i++){
			$QURY = "SELECT location_id FROM project_monitoring_locations WHERE project_id = ".$proID." AND location_parent_id = ".$parentId." AND location_title = '".$locationArray[$i]."' AND is_deleted = 0 LIMIT 0, 1";
			$RES = mysql_query($QURY);
			$values = "";
			if(mysql_num_rows($RES) > 0){
				while($ROW = mysql_fetch_assoc($RES)){
					$values = $ROW['location_id'];
				}
			}
			if($values != ''){
				$parentId = $values;
			}else{
				$insertQRY = "INSERT INTO project_monitoring_locations SET
								project_id = ".$proID.",
								location_parent_id = ".$parentId.",
								location_title = '".$locationArray[$i]."',
								last_modified_date = NOW(),
								last_modified_by = '".$builderID."',
								created_date = NOW(),
								created_by = '".$builderID."'";
				mysql_query($insertQRY);
				$parentId = mysql_insert_id();
			}
			if ($rowLocationTree == "")
				$rowLocationTree = $parentId;
			else
				$rowLocationTree .= ' > ' . $parentId;
		}
		return $rowLocationTree;
	}
	
	function QAcheckInsetIfNotExistLoc($tableName, $colName, $val, $colName1, $val1, $expColName, $builderID, $proID, $locArray){
		$values = '';
		$QURY = "SELECT ".$expColName." FROM ".$tableName." WHERE project_id = ".$proID." AND ".$colName." = '".$val."' AND ".$colName1." = '".$val1."' AND is_deleted = 0 LIMIT 0, 1";
		$RES = mysql_query($QURY);
		if(mysql_num_rows($RES) > 0){
			while($ROW = mysql_fetch_assoc($RES)){
				$values = $ROW[$expColName];
			}
		}
		if($values != ''){
			$locId = $values;
		}else{
			$insertQRY = "INSERT INTO ".$tableName." SET
							project_id = ".$proID.",
							".$colName." = ".trim($val).",
							".$colName1." = '".addslashes(trim($val1))."',
							last_modified_date = NOW(),
							last_modified_by = '".$builderID."',
							created_date = NOW(),
							created_by = '".$builderID."'";
			mysql_query($insertQRY);
			$locId = mysql_insert_id();
		}
		if($locArray == ''){
			return $locId;
		}else{
			$path = array();
			for($m=0; $m<sizeof($locArray); $m++){
				$path[] = $locId;
				$locName = $locArray[$m];
				$currLcation[] = $this->checkInsetIfNotExistLoc($tableName, $colName, $locId, 'location_title', $locArray[$m], 'location_id', $builderID, $proID, $locIdArray);
				$path = array_merge($currLcation, $path);
				$currLcation = array();
			}
			return $path;	
		}
		
	}
	
	function QArecursiveInsertLocation($locationArray, $parentId, $proID, $builderID, $rowLocationTree){
		$values = '';
		for ($i=0; $i<sizeof($locationArray); $i++){
			$QURY = "SELECT location_id FROM qa_task_locations WHERE project_id = ".$proID." AND location_parent_id = ".$parentId." AND location_title = '".$locationArray[$i]."' AND is_deleted = 0 LIMIT 0, 1";
			$RES = mysql_query($QURY);
			$values = "";
			if(mysql_num_rows($RES) > 0){
				while($ROW = mysql_fetch_assoc($RES)){
					$values = $ROW['location_id'];
				}
			}
			if($values != ''){
				$parentId = $values;
			}else{
				$insertQRY = "INSERT INTO qa_task_locations SET
								project_id = ".$proID.",
								location_parent_id = ".$parentId.",
								location_title = '".$locationArray[$i]."',
								last_modified_date = NOW(),
								last_modified_by = '".$builderID."',
								created_date = NOW(),
								created_by = '".$builderID."'";
				mysql_query($insertQRY);
				$parentId = mysql_insert_id();
			}
			if ($rowLocationTree == "")
				$rowLocationTree = $parentId;
			else
				$rowLocationTree .= ' > ' . $parentId;
		}
		return $rowLocationTree;
	}
	
	function QAsubLocationsProgressMonitoring($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->QAgetCatProgressMonitoring($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("qa_task_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("qa_task_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function QAgetCatProgressMonitoring($catId){
		$qry = 'select location_id, location_parent_id from qa_task_locations where location_id ='.$catId . ' and is_deleted=0 and location_parent_id!=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			#if($row->location_parent_id != 0){
				$path[] = $row->location_id;
				$path = array_merge($this->QAgetCatProgressMonitoring($row->location_parent_id), $path);
/*			}else{
				$path = array_merge(array($row->location_id), $path);
			}*/
		}
		return $path;
	}

	function subLocationsDepthQA($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->QAlocationsDepth($subLocation);
		foreach($depth as $dp){
			if($breadcrumb == ''){
				$breadcrumb .= $dp;
			}else{
				$breadcrumb .= $saprater . $dp;
			}
		}
		return $breadcrumb;
	}
	
	function QAlocationsDepth($catId){
		$qry = 'SELECT location_id FROM qa_task_locations WHERE location_parent_id ='.$catId.' AND is_deleted=0 ';
		$res = mysql_query($qry);
		$path = array();
		while($rows = mysql_fetch_array($res)){
			$path[] = $rows[0];
			$path = array_merge($this->QAlocationsDepth($rows[0]), $path);
		}
		return $path;
	}
	
	function arrangeMultiDimensionArray($array, $order){
		$lupCount = sizeof($array); 
		$countArray = array();
		$sortArray = array();
		for($i=0; $i<$lupCount; $i++){
			$countArray[] = count($array[$i]);
		}
		if($order == 'DESC'){
			arsort($countArray);
		}else{
			asort($countArray);
		}
		$countArray = array_keys($countArray);
		$secLoop = count($countArray);
		for($j=0; $j<$secLoop; $j++){
			$sortArray[] = $array[$countArray[$j]];
		}
		return $sortArray;
	}
	
	function createFile($fileName, $fileContent, $path, $mode = 'a+'){
		$fh = fopen($path.$fileName, $mode) or die("can't open file");
		fwrite($fh, $fileContent);
		fclose($fh);
	}
	
	function subLocationsProgressMonitoring_update($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatProgressMonitoring_update($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("project_monitoring_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("project_monitoring_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function getCatProgressMonitoring_update($catId){
		$qry = 'SELECT location_id, location_parent_id FROM project_monitoring_locations WHERE location_id ='.$catId.' AND is_deleted = 0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			#if($row->location_parent_id != 0){
				$path[] = $row->location_id;
				$path = array_merge($this->getCatProgressMonitoring_update($row->location_parent_id), $path);
/*			}else{
				$path = array_merge(array($row->location_id), $path);
			}*/
		}
		return $path;
	}
	
	function ProMonrecursiveInsertLocation($locationArray, $parentId, $proID, $builderID, $rowLocationTree){
		$values = '';
		for ($i=0; $i<sizeof($locationArray); $i++){
			$QURY = "SELECT location_id FROM project_monitoring_locations WHERE project_id = ".$proID." AND location_parent_id = ".$parentId." AND location_title = '".$locationArray[$i]."' AND is_deleted = 0 LIMIT 0, 1";
			$RES = mysql_query($QURY);
			$values = "";
			if(mysql_num_rows($RES) > 0){
				while($ROW = mysql_fetch_assoc($RES)){
					$values = $ROW['location_id'];
				}
			}
			if($values != ''){
				$parentId = $values;
			}else{
				$insertQRY = "INSERT INTO project_monitoring_locations SET
								project_id = ".$proID.",
								location_parent_id = ".$parentId.",
								location_title = '".$locationArray[$i]."',
								last_modified_date = NOW(),
								last_modified_by = '".$builderID."',
								created_date = NOW(),
								created_by = '".$builderID."'";
				mysql_query($insertQRY);
				$parentId = mysql_insert_id();
			}
			if ($rowLocationTree == "")
				$rowLocationTree = $parentId;
			else
				$rowLocationTree .= ' > ' . $parentId;
		}
		return $rowLocationTree;
	}
	
	function locId2LocName($locationTree){
		$locationTreeName = '';
		$locationTreeArray = explode(' > ', $locationTree);
		$lupCount = sizeof($locationTreeArray);
		for($i=0; $i<$lupCount; $i++){
			if($locationTreeName == ''){
				$locationTreeName = $this->getDataByKey('project_monitoring_locations', 'location_id', $locationTreeArray[$i], 'location_title');
			}else{
				$locationTreeName .= ' > '.$this->getDataByKey('project_monitoring_locations', 'location_id', $locationTreeArray[$i], 'location_title');
			}
		}
		return $locationTreeName;
	}
	
	function subLocationsProgressMonitoring_ids($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatProgressMonitoring_update($subLocation);
		$breadcrumb = join($saprater, $depth);
		#print_r($depth);die;
		return $breadcrumb;
	}
	
	function validateMySqlDate($date, $mode='FULL'){
		$returnType = false;
		if($mode != 'FULL'){
			if(preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)){
				$returnType = true;
			}
		}else{
			if(preg_match("/^\d{4}-\d{2}-\d{2} [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/", $date)){
				$returnType = true;
			}
		}
		return $returnType;
	}	

	function justChildSubLocQA($subLocation){
		$childSubLoc = array();
		$subLoc = $this->selQRYMultiple('location_id', 'qa_task_locations', 'location_parent_id = '.$subLocation.' AND is_deleted = 0');
		foreach($subLoc as $sLoc){
			$childSubLoc[] = $sLoc['location_id'];
		}
		return $childSubLoc;
	}

	
	function QAsubLocationsProgressMonitoringWallchart($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->QAgetCatProgressMonitoringWallchart($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("qa_task_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("qa_task_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function QAgetCatProgressMonitoringWallchart($catId){
		$qry = 'select location_id, location_parent_id from qa_task_locations where location_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			#if($row->location_parent_id != 0){
				$path[] = $row->location_id;
				$path = array_merge($this->QAgetCatProgressMonitoringWallchart($row->location_parent_id), $path);
/*			}else{
				$path = array_merge(array($row->location_id), $path);
			}*/
		}
		return $path;
	}
	
	function recurtionProMon($locationID, $projectID){
		$data='';
		$location = $this->selQRYMultiple('location_id, location_title', 'project_monitoring_locations', 'location_parent_id = "'.$locationID.'" and is_deleted = "0" and project_id = "'.$projectID.'" order by location_title');
		if(!empty($location)){
			foreach($location as $loc){
				$data .= '<ul><li id="li_'.$locations['location_id'].'"><span class="jtree-button demo1" id="'.$locations['location_id'].'">'.stripslashes($locations['location_title']).'</span>';
				$data .= '</li></ul>';
				return $data;
			}
		}
	}
	
	function recurtionQA($locationID, $projectID){
		$data='';
		$location = $this->selQRYMultiple('location_id, location_title', 'qa_task_locations', 'location_parent_id = "'.$locationID.'" and is_deleted = "0" and project_id = "'.$projectID.'" order by location_title');
		if(!empty($location)){
			foreach($location as $loc){
				$data .= '<ul><li id="li_'.$locations['location_id'].'"><span class="jtree-button demo1" id="'.$locations['location_id'].'">'.stripslashes($locations['location_title']).'</span>';
				$data .= '</li></ul>';
				return $data;
			}
		}
	}
	
	function promon_sublocationParent($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatProgressMonitoringwithParent($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("project_monitoring_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("project_monitoring_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function promon_sublocationParentID($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatProgressMonitoringwithParent($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $dp;
			}else{
				$breadcrumb .= $saprater . $dp;
			}
		}
		return $breadcrumb;
	}
	
	function getCatProgressMonitoringwithParent($catId){
		$qry = 'select location_id, location_parent_id from project_monitoring_locations where location_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			$path[] = $row->location_id;
			$path = array_merge($this->getCatProgressMonitoringwithParent($row->location_parent_id), $path);
		}
		return $path;
	}
	
	function qa_sublocationParent($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatQualityAssuranceParent($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $this->getDataByKey("qa_task_locations", "location_id", $dp, "location_title");
			}else{
				$breadcrumb .= $saprater . $this->getDataByKey("qa_task_locations", "location_id", $dp, "location_title");
			}
		}
		return $breadcrumb;
	}
	
	function qa_sublocationParentID($subLocation, $saprater){
		$breadcrumb = '';
		$depth = $this->getCatQualityAssuranceParent($subLocation);
		foreach($depth as $dp){
			if ($breadcrumb == ""){
				$breadcrumb = $dp;
			}else{
				$breadcrumb .= $saprater . $dp;
			}
		}
		return $breadcrumb;
	}
	
	function getCatQualityAssuranceParent($catId){
		$qry = 'select location_id, location_parent_id from qa_task_locations where location_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_object($res);
			$path[] = $row->location_id;
			$path = array_merge($this->getCatQualityAssuranceParent($row->location_parent_id), $path);
		}
		return $path;
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
	
	# Remove cookies from browser
	function removeCookies($id = 0){
		$uid = ($id!=0)?$id:$_SESSION['ww_builder_id'];
		$skipCookie = array($uid.'_qc', $uid.'_ir', $uid.'_pmr', $uid.'_qar', $uid.'_clr'); 
		$projNameType = array('projName', 'projName', 'projName', 'projNameQA', 'projNameCL'); 
		//if($id==0){
			foreach($skipCookie as $key=>$cookieName){
				$projectName = "";
				if(isset($_COOKIE[$cookieName])){
					$qc = unserialize($_COOKIE[$cookieName]);
					$projectName = $qc[$projNameType[$key]];
					if(count($qc)>1){
						unset($_COOKIE[$cookieName]);
					}
					setcookie($cookieName, serialize(array($projNameType[$key]=>$projectName)), time()+864000);
				}			
			}
		//}
		if (isset($_SERVER['HTTP_COOKIE'])) {
			$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
			foreach($cookies as $cookie) {
				$parts = explode('=', $cookie);
				$name = trim($parts[0]);
				if(!in_array($name, $skipCookie) && 'PHPSESSID'!=$name){
					setcookie($name, '', time()-1000);
					setcookie($name, '', time()-1000, '/');
				}
			}
		}
	}


	# Start:- New location id functions sections
// Get all locations by project id
	function getAllLocatByProjId($projId = 0){
		$qry = 'SELECT location_id,	location_parent_id, location_title FROM project_locations WHERE project_id ='.$projId. ' AND is_deleted=0';
		$res = mysql_query($qry);
		$locationIdArr = array();
		$locationNameArr = array();
		while($rows = mysql_fetch_array($res)){
			//echo $rows['location_id']."<br>";
			$locationIdArr[$rows['location_parent_id']][] = $rows['location_id'];
			$locationNameArr[$rows['location_id']] = $rows['location_title'];
		}
		return array('locationIdArr' => $locationIdArr, 'locationNameArr' => $locationNameArr);
	}
	
// Get child location ids  array by location id
	function getLocatIdsByLocationArr($locationIdArr = array(), $catId = 0, $isFirstLoop = 1){
		//echo $catId."<br>";
		if($isFirstLoop == 1){
			$path = array($catId);
		}else{
			$path = array();
		}
		
		if(isset($locationIdArr[$catId])){
			//print_r($locationIdArr[$catId]);
			foreach($locationIdArr[$catId] as $locationId){
				$path[] = $locationId;
				$path = array_merge($this->getLocatIdsByLocationArr($locationIdArr, $locationId, 0), $path);
			}
		}
		return $path;
	}
	
// Get child location ids  array by multiple location ids
	function getMultiLocationIdsOfAll($locationIdArr = array(), $Location=""){
		$postCount++;
		$isMul = explode('@@@', $Location);
		#print_r($isMul);die;
		$mulIssueTo = '';
		$loopMul = count($isMul);
		$locations = array();
		$mainArray = array();
		for($g=0; $g<$loopMul; $g++){
		#echo $isMul[$g];die;
			$getData = $this->getLocatIdsByLocationArr($locationIdArr, $isMul[$g]);
			if(!empty($getData[0]) && isset($getData[0])){
				$locations[] =	$getData;
			}else{
				$locations[] = array($isMul[$g]);
			}
				
			$jk = 0;	
			#print_r($locations);
			foreach($locations as $locations1){
				for($jk=0;$jk<count($locations1);$jk++){
					if(!in_array($locations1[$jk],$mainArray)){
						$mainArray[] = $locations1[$jk];
					}
				}
			}
		}
		return $mainArray;
	}
	function getCatIdsNew($catId){
		$qry = 'select location_id from project_locations where location_parent_id ='.$catId . ' and is_deleted=0';
		$res = mysql_query($qry);
		$path = array();
		while($rows = mysql_fetch_array($res)){
			$path[] = $rows[0];
			$path = array_merge($this->getCatIds($rows[0]), $path);
			array_push($path, $catId);
		}
		return $path;
	}
	function getLocationIdsOfAll($Location=""){
		$postCount++;
		$isMul = explode('@@@', $Location);
		#print_r($isMul);die;
		$mulIssueTo = '';
		$loopMul = count($isMul);
		$locations = array();
		$mainArray = array();
		for($g=0; $g<$loopMul; $g++){
		#echo $isMul[$g];die;
			 $getData = $this->getCatIdsNew($isMul[$g]);
			#print_r($getData);
			if(!empty($getData[0]) && isset($getData[0])){
				$locations[] =	$getData;
			}else{
				$locations[] = array($isMul[$g]);
			}
				
			$jk = 0;	
			#print_r($locations);
			foreach($locations as $locations1){
					for($jk=0;$jk<count($locations1);$jk++){
						if(!in_array($locations1[$jk],$mainArray)){
							$mainArray[] = $locations1[$jk];
						}
				}
			}
		}
		return $mainArray;
	}
	# End:- New location id functions sections
	
	function compress($src, $fileName, $dst=''){
		if(substr($src,-1)==='/'){$src=substr($src,0,-1);}
		if(substr($dst,-1)==='/'){$dst=substr($dst,0,-1);}
        $path=strlen(dirname($src).'/');
		$filename = $fileName.'.zip';
		
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
	
	#Start:- Get all locations ids order by order_id
	function getLocationOrderBy($projectId){
		$proLocationArr = array();$output='';
		$plQuery = 'SELECT * FROM project_locations WHERE project_id='. $projectId .' AND is_deleted=0 ORDER BY order_id';
		$plResult = mysql_query($plQuery);
		if(mysql_num_rows($plResult)>0){
			while($pRows = mysql_fetch_array($plResult)) {
				$proLocationArr[] = array(
					'location_id'=>$pRows['location_id'],
					'location_parent_id'=>$pRows['location_parent_id'],
					'location_title'=>$pRows['location_title']
				);
			}			
			$output = $this->buildTree($proLocationArr);
		}
		return $output;
	}
	function buildTree(array $elements, $parentId = 0) {
		$branch = array();$result = array();
		foreach ($elements as $element) {
			if ($element['location_parent_id'] == $parentId) {
				$result[] = $element['location_id'];
				$children = $this->buildTree($elements, $element['location_id']);
				if ($children) {
					foreach($children as $chRows) {
						$result[] = $chRows;
					}
					$element['child'] = $children;
				}
				$branch[] = $element;
			}
		}
		return $result;	//For locations ids
		//return $branch; //For parent/child relation tree.
	}
	#End:- Get all locations ids order by

	#Send email.
	function sendEmails($to=array(), $subject, $message, $from=array(), $cc=array(), $bcc=array(), $attachmentList=array()){
		/*$mail = new PHPMailer();
		$mail->IsSendmail(); // telling the class to use SMTP
		$mail->IsHTML(true);*/
		
		$mail = new PHPMailer(true);
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPSecure = 'tls'; //"ssl";	// sets the prefix to the servier
		$mail->Host = smtpHost; 	//pod51022.outlook.com      // sets GMAIL as the SMTP server
		$mail->SMTPDebug = 0;				// enables SMTP debug information (for testing)
		$mail->SMTPAuth = true;        		// enable SMTP authentication
		$smtpPort = smtpPort;
		if(!empty($smtpPort)){
			$mail->Port =  smtpPort; //587;
		}
		$mail->Username = smtpUsername; //"wiseworkingsales@gmail.com"; // SMTP account username
		$mail->Password = smtpPassword; //"Wiseworking123";   // SMTP account password
		$mail->isHTML(true);
		#end config

		//To address and name
		if(is_array($to) && !empty($to)) {
			foreach($to as $rows) {
				$mail->AddAddress($rows,''); // To
			}
		}
		
		//From address.
		if(is_array($from) && !empty($from)) {
			foreach($from as $rows) {
				$mail->SetFrom($rows, '');
			}
		} else {
			$mail->SetFrom("WiseworkingSystems@wiseworking.com.au", "Wiseworking");
		}
		$mail->AddReplyTo("WiseworkingSystems@wiseworking.com.au", "Wiseworking");
		
		//Add BCC
		if(is_array($bcc) && !empty($bcc)) {
			foreach($bcc as $rows) {
				$mail->AddBCC($rows, ""); //To BCC
			}
		}
		
		//Add CCC
		if(is_array($cc) && !empty($cc)) {
			foreach($cc as $rows) {
				$mail->AddCC($rows, ""); //To CC
			}
		}
		
		#Add attachment.
		for($i=0;$i<sizeof($attachmentList);$i++){
			if(!empty($attachmentList[$i])){
				$mail->AddAttachment(trim($attachmentList[$i]), '');//send to mail heree
			}
		}
		
		$mail->Subject = $subject;
		//$message .= '<p style="background-color:#000; width:329px; padding: 5px 5px 0px;"><img src="http://harrishmc.defectid.com/images/logo_mail.png" alt="company logo"></p><br/>';
		
		$mail->MsgHTML($message);
		if(!$mail->Send()) {
			$result = array('error'=>'false', 'message'=> "Mailer Error: " . $mail->ErrorInfo);
		} else {
			$result = array('error'=>'true', 'message'=> "Message has been sent successfully");
		}
		$mail->ClearAddresses();
		return $result;
	}


	# Message board section
	function messageBoard($projectId, $fromId='', $toId='', $subject='', $messageType='', $messageDetails='', $attachment='', $messgeId=0, $toExtraAddress='', $ccAddress='', $tags='', $inspId = 0, $thread_id = 0, $refrenceNumber = "", $RFInumber = "", $fixedByDate = "", $RFIstatus = "", $closedDate = "", $updateMessageID = 0, $iscc = 0, $requestFrom = "", $correspondenceNumber = "", $companyTag = "", $purchaserLocation = "", $subjectDraft="", $debug="", $dateSent= "", $dateApproved="", $sent = "", $approved = "", $claimed = "", $certified = "", $invoiced = "", $toEmailHide = 0, $ccEmailHide = 0,$imageCompose='',$rfi_reference=""){
 	#if($debug=='testttt'){echo "fdsfd";die;}
 	#echo $subjectDraft;die;
 	$refNumberMsgCountNew = "";	
	if($messageType != "Request For Information"){
		$refNumberMsgCount = $this->selQRYMultiple('count(*) AS refNumber', 'pmb_message', 'project_id = '.$projectId.' AND message_type = "'.$messageType.'" AND is_deleted = 0');
		$refNumberMsgCountNew = ++$refNumberMsgCount[0]['refNumber'];
	}
	if($messageType != "Consultant Advice Notice"){
		$refNumberMsgCount = $this->selQRYMultiple('count(*) AS refNumber', 'pmb_message', 'project_id = '.$projectId.' AND message_type = "'.$messageType.'" AND is_deleted = 0');
		$refNumberMsgCountNew = ++$refNumberMsgCount[0]['refNumber'];
	}
	
	if($RFIstatus == "")	$RFIstatus = "Open";

#	echo $projectId."<br />".$fromId."<br />".$toId."<br />".$subject."<br />".$messageType."<br />".$messageDetails."<br />".$attachment."<br />".$messgeId."<br />".$toExtraAddress."<br />".$ccAddress."<br />".$tags."<br />".$inspId."<br />".$thread_id."<br />".$refrenceNumber."<br />".$RFInumber."<br />".$fixedByDate."<br />".$RFIstatus."<br />".$closedDate."<br />".$updateMessageID."<br />".$closedDate."<br />".$updateMessageID."<br />".$iscc;die;
	
		$messageDetails = str_replace('"', "'", $messageDetails);
		$isDraft = isset($_POST['saveDraft']) ? 1 : 0 ;
		if($isDraft==1){
			$subject = $subjectDraft;
		}
		#echo 'sbuject-->'.$subject;die;
		if(!empty($toId) || !empty($fromId) || !empty($subject)){
		$processReq = true;
			if($requestFrom == "Compose" && $subject == "Request For Information"){
				$rfiData = array();
				$rfiData = $this->selQRYMultiple('DISTINCT rfi_number', 'pmb_user_message', 'project_id = '.$projectId.' AND is_deleted = 0 AND rfi_number != 0 AND rfi_number = '.$RFInumber);
				if(!empty($rfiData) && $rfiData[0]['rfi_number'] != "")
					$processReq = false;				
			}
			if($requestFrom == "Compose" && $subject == "Consultant Advice Notice"){
				$rfiData = array();
				$rfiData = $this->selQRYMultiple('DISTINCT a.rfi_number', 'pmb_user_message as a, pmb_message as b', 'a.message_id = b.message _id AND b.message_type = "Consultant Advice Notice" AND a.project_id = '.$projectId.' AND a.is_deleted = 0 AND a.rfi_number != 0 AND a.rfi_number = '.$RFInumber);
				if(!empty($rfiData) && $rfiData[0]['rfi_number'] != "")
					$processReq = false;				
			}
			#echo "process req-->".$processReq;die;
			if($processReq){
			
				if($messgeId==0){
					 $newMessgeId = $this->getUniqueIDForPMB("message_id");
					  $messQRY = 'INSERT INTO pmb_message SET
											message_id = "'.$newMessgeId.'",
											inspection_id = "'.$inspId.'",
											message = "'.addslashes($messageDetails).'",
											message_type = "'.$messageType.'",
											title = "'.addslashes($subject).'",
											project_id = "'.$projectId.'",
											is_draft = "'.$isDraft.'",
											to_email_address = "'.$toExtraAddress.'",
											cc_email_address = "'.$ccAddress.'",
											tags = "'.$tags.'",
											sent_time = NOW(),
											created_date = NOW(),
											created_by = "'.$fromId.'",
											last_modified_date = NOW(),
											last_modified_by = "'.$fromId.'",
											rfi_fixed_by_date = "'.$fixedByDate.'",
											correspondence_number = "'.$correspondenceNumber.'",
											purchaser_location = "'.$purchaserLocation.'",
											company_tag = "'.$companyTag.'",
											rfi_status = "'.$RFIstatus.'",
											date_sent = "'.$dateSent.'",
											date_approved = "'.$dateApproved.'",
											sent = "'.$sent.'",
											approved = "'.$approved.'",
											claimed = "'.$claimed.'",
											certified = "'.$certified.'",
											invoiced = "'.$invoiced.'",
											to_email_hide = "'.$toEmailHide.'",
											cc_email_hide = "'.$ccEmailHide.'",
											image_compose = "'.$imageCompose.'",
											rfi_reference = "'.$rfi_reference.'"';
					mysql_query($messQRY);
					#if($debug == "testt"){echo $messQRY;die;}
					$messgeId = $newMessgeId; //mysql_insert_id();  
					
					if($thread_id==0){
						$newThread_id = $this->getUniqueIDForPMB("thread_id");
						$messThreadQRY = 'INSERT INTO pmb_message_thread SET 
												thread_id = "'.$newThread_id.'",
												inspection_id = "'.$inspId.'",
												time = NOW(),
												message_id = "'.$messgeId.'",
												project_id = "'.$projectId.'",
												created_date = NOW(),
												created_by = "'.$fromId.'",
												last_modified_date = NOW(),
												last_modified_by = "'.$fromId.'"';
						mysql_query($messThreadQRY);
						$thread_id = $newThread_id; //mysql_insert_id();
					}
					if($updateMessageID != 0){
						$messThreadQRY = 'UPDATE pmb_message SET
												rfi_status = "'.$RFIstatus.'",
												last_modified_date = NOW(),
												last_modified_by = "'.$fromId.'",
												correspondence_number = "'.$correspondenceNumber.'",
												company_tag = "'.$companyTag.'",
												rfi_closed_date = '.$closedDate.'
											WHERE
												message_id = '.$updateMessageID;
						mysql_query($messThreadQRY);
					}
				}else{
					$messageUpdateQRY = 'UPDATE pmb_message SET
												message = "'.addslashes($messageDetails).'",
												message_type = "'.$messageType.'",
												title = "'.addslashes($subject).'",
												to_email_address = "'.$toExtraAddress.'",
												cc_email_address = "'.$ccAddress.'",
												tags = "'.$tags.'",
												created_by = "'.$fromId.'",
												last_modified_date = NOW(),
												last_modified_by = "'.$fromId.'",
												rfi_fixed_by_date = "'.$fixedByDate.'",
												correspondence_number = "'.$correspondenceNumber.'",
												purchaser_location = "'.$purchaserLocation.'",
												company_tag = "'.$companyTag.'",
												rfi_status = "'.$RFIstatus.'",
												to_email_hide = "'.$toEmailHide.'",
												cc_email_hide = "'.$ccEmailHide.'",
												image_compose = "'.$imageCompose.'",
												rfi_reference = "'.$rfi_reference.'"
											WHERE
												message_id = '.$messgeId;
					mysql_query($messageUpdateQRY);
					$newRefrenceNumber = $refrenceNumber.' '.$refNumberMsgCountNew;
					$msgUpdateQRY = 'UPDATE pmb_user_message SET
											last_modified_date = NOW(),
											refrence_number = "'.addslashes($newRefrenceNumber).'", 
											rfi_number = "'.$RFInumber.'"
										WHERE
											message_id = '.$messgeId;
					mysql_query($msgUpdateQRY);
	
					if($thread_id==0){
						$thread_id = $this->getDataByKey('pmb_message_thread', 'message_id', $messgeId, 'thread_id');
					}
				}
				
				if($attachment != '') {
					foreach($attachment as $key => $val){
						$attachmentQRY = 'INSERT INTO pmb_attachments SET
														inspection_id = "'.$inspId.'",
														message_id = "'.$messgeId.'",
														name = "'.$_SESSION[$_SESSION['idp'].'_orignalFileName'][$key].'",
														attachment_name = "'.$val.'",
														project_id = "'.$projectId.'",
														status = 0,
														created_date = NOW(),
														created_by = "'.$fromId.'",
														last_modified_date = NOW(),
														last_modified_by = "'.$fromId.'"';
						mysql_query($attachmentQRY);
					}
				}	
				
				$userInbox = $this->selQRYMultiple('user_id', 'pmb_user_message', 'message_id="'.$messgeId.'" AND type="inbox" AND user_id="'.$toId.'" AND from_id="'.$fromId.'"');
				if(!isset($userInbox[0]['user_id'])){
					 $newRefrenceNumber = $refrenceNumber.' '.$refNumberMsgCountNew;
					 $userMessageQRY = 'INSERT INTO pmb_user_message SET
												inspection_id = "'.$inspId.'",
												user_id = "'.$toId.'",
												from_id = "'.$fromId.'",
												message_id = "'.$messgeId.'",
												type = "inbox",
												deleted = 0,
												thread_id = "'.$thread_id.'",
												project_id = "'.$projectId.'",
												created_date = NOW(),
												created_by = "'.$fromId.'",
												last_modified_date = NOW(),
												last_modified_by = "'.$fromId.'", 
												refrence_number = "'.addslashes($newRefrenceNumber).'", 
												rfi_number = "'.$RFInumber.'", 
												is_cc_user = "'.$iscc.'"';
					mysql_query($userMessageQRY);
					$insetedMsgID = mysql_insert_id();
				}
				
				$userSent = $this->selQRYMultiple('user_id', 'pmb_user_message', 'message_id="'.$messgeId.'" AND type="sent" AND user_id="'.$fromId.'" AND from_id="'.$toId.'"');
				if(!isset($userSent[0]['user_id'])){ 
					$newRefrenceNumber = $refrenceNumber.' '.$refNumberMsgCountNew;
					 $userMessageQRY = 'INSERT INTO pmb_user_message SET
													inspection_id = "'.$inspId.'",
													user_id = "'.$fromId.'",
													from_id = "'.$toId.'",
													message_id = "'.$messgeId.'",
													type = "sent",
													deleted = 0,
													thread_id = "'.$thread_id.'",
													project_id = "'.$projectId.'",
													created_date = NOW(),
													created_by = "'.$fromId.'",
													last_modified_date = NOW(),
													last_modified_by = "'.$fromId.'", 
													refrence_number = "'.addslashes($newRefrenceNumber).'", 
													rfi_number = "'.$RFInumber.'", 
													is_cc_user = "'.$iscc.'"';
					mysql_query($userMessageQRY);	
					$insetedMsgID = mysql_insert_id();
				}
				
				$respData['messgeId'] = $messgeId;
				$respData['thread_id'] = $thread_id;
			}else{
				$respData = array();
			}
		#	print_r($respData);die;
			return $respData;
			 
		} return false;
	}


	# Upload message board attachment	
	function upload_attahment($fileElementName) {
			$name='';
			//$fileElementName = 'attachment';
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
							$ext = explode('.', $_FILES[$fileElementName]['name']);
							$image['orignalFileName'] = $_FILES[$fileElementName]['name'];
							$image['newName'] = $_SESSION['idp'].'_'.substr(microtime(), -6, -1).'.'.$ext[1];
							if (move_uploaded_file($_FILES[$fileElementName]['tmp_name'], 'attachment/'.$image['newName'])) {
							@unlink($_FILES[$fileElementName]);
							}
							
					}
					return $image;
			
				}	
	
	# Estimate message board section
	function estimateMessageBoard($projectId, $fromId='', $toId='', $subject='', $messageType='', $messageDetails='', $attachment='', $messgeId=0, $toExtraAddress='', $ccAddress='', $tags='', $inspId = 0, $thread_id = 0, $refrenceNumber = "", $RFInumber = "", $fixedByDate = "", $RFIstatus = "", $closedDate = "", $updateMessageID = 0, $iscc = 0, $requestFrom = "", $correspondenceNumber = "", $companyTag = "", $purchaserLocation = "", $trade = ""){
	
	$refNumberMsgCountNew = "";	
	if($messageType != "Request For Information"){
		$refNumberMsgCount = $this->selQRYMultiple('count(*) AS refNumber', 'estimate_pmb_message', 'estimate_project_id = '.$projectId.' AND message_type = "'.$messageType.'" AND is_deleted = 0');
		$refNumberMsgCountNew = ++$refNumberMsgCount[0]['refNumber'];
	}
	if($RFIstatus == "")	$RFIstatus = "Open";

#	echo $projectId."<br />".$fromId."<br />".$toId."<br />".$subject."<br />".$messageType."<br />".$messageDetails."<br />".$attachment."<br />".$messgeId."<br />".$toExtraAddress."<br />".$ccAddress."<br />".$tags."<br />".$inspId."<br />".$thread_id."<br />".$refrenceNumber."<br />".$RFInumber."<br />".$fixedByDate."<br />".$RFIstatus."<br />".$closedDate."<br />".$updateMessageID."<br />".$closedDate."<br />".$updateMessageID."<br />".$iscc;die;
	
		$messageDetails = str_replace('"', "'", $messageDetails);
		$isDraft = isset($_POST['saveDraft']) ? 1 : 0 ;
		if(!empty($toId) || !empty($fromId) || !empty($subject)){
		$processReq = true;
			if($requestFrom == "Compose" && $subject == "Request For Information"){
				$rfiData = array();
				$rfiData = $this->selQRYMultiple('DISTINCT rfi_number', 'estimate_pmb_user_message', 'estimate_project_id = '.$projectId.' AND is_deleted = 0 AND rfi_number != 0 AND rfi_number = '.$RFInumber);
				if(!empty($rfiData) && $rfiData[0]['rfi_number'] != "")
					$processReq = false;				
			}
			if($processReq){
			
				if($messgeId==0){
					$newMessgeId = $this->getUniqueIDForPMB("message_id");
					$messQRY = 'INSERT INTO estimate_pmb_message SET
											message_id = "'.$newMessgeId.'",
											inspection_id = "'.$inspId.'",
											message = "'.addslashes($messageDetails).'",
											message_type = "'.$messageType.'",
											title = "'.addslashes($subject).'",
											estimate_project_id = "'.$projectId.'",
											is_draft = "'.$isDraft.'",
											to_email_address = "'.$toExtraAddress.'",
											cc_email_address = "'.$ccAddress.'",
											tags = "'.$tags.'",
											sent_time = NOW(),
											created_date = NOW(),
											created_by = "'.$fromId.'",
											last_modified_date = NOW(),
											last_modified_by = "'.$fromId.'",
											rfi_fixed_by_date = "'.$fixedByDate.'",
											correspondence_number = "'.$correspondenceNumber.'",
											purchaser_location = "'.$purchaserLocation.'",
											company_tag = "'.$companyTag.'",
											trade = "'.$trade.'",
											rfi_status = "'.$RFIstatus.'"'; 
					mysql_query($messQRY);
					$messgeId = $newMessgeId; //mysql_insert_id();  
					
					if($thread_id==0){
						$newThread_id = $this->getUniqueIDForPMB("thread_id");
						$messThreadQRY = 'INSERT INTO estimate_pmb_message_thread SET
												thread_id = "'.$newThread_id.'",
												inspection_id = "'.$inspId.'",
												time = NOW(),
												message_id = "'.$messgeId.'",
												estimate_project_id = "'.$projectId.'",
												created_date = NOW(),
												created_by = "'.$fromId.'",
												last_modified_date = NOW(),
												last_modified_by = "'.$fromId.'"';
						mysql_query($messThreadQRY);
						$thread_id = $newThread_id; //mysql_insert_id();
					}
					if($updateMessageID != 0){
						$messThreadQRY = 'UPDATE estimate_pmb_message SET
												rfi_status = "'.$RFIstatus.'",
												last_modified_date = NOW(),
												last_modified_by = "'.$fromId.'",
												correspondence_number = "'.$correspondenceNumber.'",
												company_tag = "'.$companyTag.'",
												rfi_closed_date = '.$closedDate.'
											WHERE
												message_id = '.$updateMessageID;
						mysql_query($messThreadQRY);
					}
				}else{
					$newRefrenceNumber = $refrenceNumber.' '.$refNumberMsgCountNew;
					$messageUpdateQRY = 'UPDATE estimate_pmb_message SET
												message = "'.addslashes($messageDetails).'",
												message_type = "'.$messageType.'",
												title = "'.addslashes($subject).'",
												to_email_address = "'.$toExtraAddress.'",
												cc_email_address = "'.$ccAddress.'",
												tags = "'.$tags.'",
												created_by = "'.$fromId.'",
												last_modified_date = NOW(),
												last_modified_by = "'.$fromId.'",
												rfi_fixed_by_date = "'.$fixedByDate.'",
												correspondence_number = "'.$correspondenceNumber.'",
												purchaser_location = "'.$purchaserLocation.'",
												company_tag = "'.$companyTag.'",
												rfi_status = "'.$RFIstatus.'"
											WHERE
												message_id = '.$messgeId;
					mysql_query($messageUpdateQRY);
	
					$msgUpdateQRY = 'UPDATE estimate_pmb_user_message SET
											last_modified_date = NOW(),
											refrence_number = "'.addslashes($newRefrenceNumber).'", 
											rfi_number = "'.$RFInumber.'"
										WHERE
											message_id = '.$messgeId;
					mysql_query($msgUpdateQRY);
	
					if($thread_id==0){
						$thread_id = $this->getDataByKey('estimate_pmb_message_thread', 'message_id', $messgeId, 'thread_id');
					}
				}
				
				if($attachment != '') {
					foreach($attachment as $key => $val){
						$oldAttachment = $this->selQRYMultiple('attach_id', 'estimate_pmb_attachments', 'message_id="'.$messgeId.'" AND name = "'.$_SESSION[$_SESSION['estProjId'].'_orignalFileName'][$key].'" AND estimate_project_id = "'.$projectId.'" AND attachment_name = "'.$val.'"');
						if(!isset($oldAttachment[0]['attach_id'])){
							$attachmentQRY = 'INSERT INTO estimate_pmb_attachments SET
												inspection_id = "'.$inspId.'",
												message_id = "'.$messgeId.'",
												name = "'.$_SESSION[$_SESSION['estProjId'].'_orignalFileName'][$key].'",
												attachment_name = "'.$val.'",
												estimate_project_id = "'.$projectId.'",
												status = 0,
												created_date = NOW(),
												created_by = "'.$fromId.'",
												last_modified_date = NOW(),
												last_modified_by = "'.$fromId.'"';
							mysql_query($attachmentQRY);
						}
					}
				}	
				
				$userInbox = $this->selQRYMultiple('user_id', 'estimate_pmb_user_message', 'message_id="'.$messgeId.'" AND type="inbox" AND user_id="'.$toId.'" AND from_id="'.$fromId.'"');
				if(!isset($userInbox[0]['user_id'])){
					$newRefrenceNumber = $refrenceNumber.' '.$refNumberMsgCountNew;
					$userMessageQRY = 'INSERT INTO estimate_pmb_user_message SET
												inspection_id = "'.$inspId.'",
												user_id = "'.$toId.'",
												from_id = "'.$fromId.'",
												message_id = "'.$messgeId.'",
												type = "inbox",
												deleted = 0,
												thread_id = "'.$thread_id.'",
												estimate_project_id = "'.$projectId.'",
												created_date = NOW(),
												created_by = "'.$fromId.'",
												last_modified_date = NOW(),
												last_modified_by = "'.$fromId.'", 
												refrence_number = "'.addslashes($newRefrenceNumber).'", 
												rfi_number = "'.$RFInumber.'", 
												is_cc_user = "'.$iscc.'"';
					mysql_query($userMessageQRY);
					$insetedMsgID = mysql_insert_id();
				}
				
				$userSent = $this->selQRYMultiple('user_id', 'estimate_pmb_user_message', 'message_id="'.$messgeId.'" AND type="sent" AND user_id="'.$fromId.'" AND from_id="'.$toId.'"');
				if(!isset($userSent[0]['user_id'])){
					$newRefrenceNumber = $refrenceNumber.' '.$refNumberMsgCountNew;
					$userMessageQRY = 'INSERT INTO estimate_pmb_user_message SET
													inspection_id = "'.$inspId.'",
													user_id = "'.$fromId.'",
													from_id = "'.$toId.'",
													message_id = "'.$messgeId.'",
													type = "sent",
													deleted = 0,
													thread_id = "'.$thread_id.'",
													estimate_project_id = "'.$projectId.'",
													created_date = NOW(),
													created_by = "'.$fromId.'",
													last_modified_date = NOW(),
													last_modified_by = "'.$fromId.'", 
													refrence_number = "'.addslashes($newRefrenceNumber).'", 
													rfi_number = "'.$RFInumber.'", 
													is_cc_user = "'.$iscc.'"';
					mysql_query($userMessageQRY);	
					$insetedMsgID = mysql_insert_id();
				}
				
				$respData['messgeId'] = $messgeId;
				$respData['thread_id'] = $thread_id;
			}else{
				$respData = array();
			}
		#	print_r($respData);die;
			return $respData;
			 
		} return false;
	}

	# Upload estimate message board attachment	
	function estimateUploadAttahment($fileElementName) {
		$name='';
		//$fileElementName = 'attachment';
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
				$ext = explode('.', $_FILES[$fileElementName]['name']);
				$image['orignalFileName'] = $_FILES[$fileElementName]['name'];
				$image['newName'] = $_SESSION['estProjId'].'_'.substr(microtime(), -6, -1).'.'.$ext[1];
				if (move_uploaded_file($_FILES[$fileElementName]['tmp_name'], 'estimate_attachment/'.$image['newName'])) {
				@unlink($_FILES[$fileElementName]);
				}
				
		}
		return $image;
	}

	# Get unique id for PMB
	function getUniqueIDForPMB($requiestId=""){
		#pmb_message
		if($requiestId == "message_id"){
			$preMessageID = $this->getDataByKey('unique_pmbmessageid', 'is_deleted', '0', 'MAX(unique_messageid_id)');
			if($preMessageID){
				$rs = mysql_query("INSERT INTO unique_pmbmessageid SET message_id='".++$preMessageID."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder_id']."', last_modified_by='".$_SESSION['ww_builder_id']."', created_date=NOW(), last_modified_date = NOW(), created_by='".$_SESSION['ww_builder_id']."'");
				if($rs){	$newMessageIDInsert = mysql_insert_id();	}
			}else{
				$messageID = $this->selQRY('MAX(message_id) as newMessageID', 'pmb_message', 'is_deleted = 1');
				$rs = mysql_query("INSERT INTO unique_pmbmessageid SET message_id='".$messageID['newMessageID']."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder_id']."', last_modified_by='".$_SESSION['ww_builder_id']."', created_date=NOW(), last_modified_date = NOW(), created_by='".$_SESSION['ww_builder_id']."'");
				if($rs){	$newMessageIDInsert = mysql_insert_id();	}
			}
		  return $newMessageIDInsert;
		}
		
		# pmb_message_thread
		if($requiestId == "thread_id"){
			$preThreadID = $this->getDataByKey('unique_pmbthreadid', 'is_deleted', '0', 'MAX(unique_threadid_id)');
			if($preThreadID){
				$rs = mysql_query("INSERT INTO unique_pmbthreadid SET thread_id='".++$preThreadID."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder_id']."', last_modified_by='".$_SESSION['ww_builder_id']."', created_date=NOW(), last_modified_date = NOW(), created_by='".$_SESSION['ww_builder_id']."'");
				if($rs){	$newThreadIDInsert = mysql_insert_id();	}
		  	}else{
				$threadID = $this->selQRY('MAX(thread_id) as newThreadID', 'pmb_message_thread', 'is_deleted = 1');
				$rs = mysql_query("INSERT INTO unique_pmbthreadid SET thread_id='".$threadID['newThreadID']."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder_id']."', last_modified_by='".$_SESSION['ww_builder_id']."', created_date=NOW(), last_modified_date = NOW(), created_by='".$_SESSION['ww_builder_id']."'");
				if($rs){	$newThreadIDInsert = mysql_insert_id();	}
			}  
			return $newThreadIDInsert;
		}
		
		# meeting_details
		if($requiestId == "meeting_id"){
			$preMeetingID = $this->getDataByKey('unique_pmbmeetingid', 'is_deleted', '0', 'MAX(unique_meetingid_id)');
			if($preMeetingID){
				$rs = mysql_query("INSERT INTO unique_pmbmeetingid SET meeting_id='".++$preMeetingID."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder_id']."', last_modified_by='".$_SESSION['ww_builder_id']."', created_date=NOW(), last_modified_date = NOW(), created_by='".$_SESSION['ww_builder_id']."'");
				if($rs){	$newMeetingIDInsert = mysql_insert_id();	}
		  	}else{
				$meetingID = $this->selQRY('MAX(meeting_id) as newMeetingID', 'meeting_details', 'is_deleted = 1');
				$rs = mysql_query("INSERT INTO unique_pmbmeetingid SET meeting_id='".$meetingID['newMeetingID']."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder_id']."', last_modified_by='".$_SESSION['ww_builder_id']."', created_date=NOW(), last_modified_date = NOW(), created_by='".$_SESSION['ww_builder_id']."'");
				if($rs){	$newMeetingIDInsert = mysql_insert_id();	}
			}  
			return $newMeetingIDInsert;
		}
		
		# meeting_item_details
		if($requiestId == "item_id"){
			$preItemID = $this->getDataByKey('unique_pmbmeetingitemid', 'is_deleted', '0', 'MAX(unique_meetingitemid_id)');
			if($preItemID){
				$rs = mysql_query("INSERT INTO unique_pmbmeetingitemid SET item_id='".++$preItemID."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder_id']."', last_modified_by='".$_SESSION['ww_builder_id']."', created_date=NOW(), last_modified_date = NOW(), created_by='".$_SESSION['ww_builder_id']."'");
				if($rs){	$newItemIDInsert = mysql_insert_id();	}
		  	}else{
				$itemID = $this->selQRY('MAX(item_id) as newItemID', 'meeting_item_details', 'is_deleted = 1');
				$rs = mysql_query("INSERT INTO unique_pmbmeetingitemid SET item_id='".$itemID['newItemID']."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder_id']."', last_modified_by='".$_SESSION['ww_builder_id']."', created_date=NOW(), last_modified_date = NOW(), created_by='".$_SESSION['ww_builder_id']."'");
				if($rs){	$newItemIDInsert = mysql_insert_id();	}
			}  
			return $newItemIDInsert;
		}				
	}

	function getAddressBook(){
		#print_r($_SESSION);die;	
		$unionQuery = "SELECT
							ab.full_name,
							ab.company_name as company,
							ab.user_phone as phone,
							ab.user_email,
							IF(ab.id>0, 'adHoc', '') AS type,
							ab.id,
							ab.attachment_noti_type	
						FROM
							pmb_address_book as ab
						where
							ab.project_id = ".$_SESSION['idp']." AND is_deleted = 0 
				UNION
						SELECT
							iss.company_name as name,
							iss.issue_to_name as company,
							iss.issue_to_phone as phone,
							iss.issue_to_email as email,
							IF(iss.issue_to_id > 0, 'issuedTo', '') AS type,
							iss.issue_to_id as id,
							iss.attachment_noti_type
						FROM
							inspection_issue_to as iss
						WHERE
							iss.project_id = ".$_SESSION['idp']." AND is_deleted = 0
				UNION
						SELECT DISTINCT
							u.user_fullname as name,
							u.company_name as company,
							u.user_phone_no as phone,
							u.user_email as email,
							IF(u.user_id > 0, 'projectUser', '') AS type,
							u.user_id as id,
							u.attachment_noti_type
						FROM
							user_projects AS up, user AS u
						WHERE
							up.user_id = u.user_id AND up.project_id = ".$_SESSION['idp']." AND 
							u.user_id != ".$_SESSION['ww_builder_id']." AND up.is_deleted = 0 AND u.is_deleted = 0";
		$queryResult = mysql_query($unionQuery);
		$dataGrid = array();
		while($data=mysql_fetch_array($queryResult)){
			$dataGrid[] = $data;	
		}
		return $dataGrid;
	}

	function save_email_status($module_type="",$to="", $cc="", $bcc="", $content="", $email_status="", $project_id = "", $subject = "", $attachment=""){
			 $query = "INSERT into email_status_table SET `module_type` = '".$module_type."',
														`to` = '".$to."',
														`cc` = '".$cc."',
														`bcc` = '".$bcc."',
														`content` = '".addslashes($content)."',
														`email_status` = '".$email_status."',
														`project_id`= '".$project_id."',
														`subject` = '".$subject."',
														`attachment` = '".$attachment."',
														`is_deleted`= '0',
														`last_modified_date` = 'NOW',
														`last_modified_by` = '".$_SESSION['ww_builder_id']."',
														`created_date`= 'NOW()',
														`created_by` = '".$_SESSION['ww_builder_id']."'";
		mysql_query($query);
		if(mysql_affected_rows()>0){
			return true;
		}	
	}

	function moneyFormat($num){
	    $explrestunits = "" ;
	    if(strlen($num)>3){
	        $lastthree = substr($num, strlen($num)-3, strlen($num));
	        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
	        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
	        $expunit = str_split($restunits, 2);
	        for($i=0; $i<sizeof($expunit); $i++){
	            // creates each of the 2's group and adds a comma to the end
	            if($i==0)
	            {
	                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
	            }else{
	                $explrestunits .= $expunit[$i].",";
	            }
	        }
	        $thecash = $explrestunits.$lastthree;
	    } else {
	        $thecash = $num;
	    }
	    return $thecash; // writes the final format where $currency is the currency symbol.
	}

	function sendEmail($subject = "", $message = "", $toDataList = array(), $fromDataList = array(), $attachmentList = array(), $ccDataList = array(), $bccDataList = array()) {
        #start config to php mailer
        if (!empty($subject) && isset($toDataList[0]['email']) && !empty($toDataList[0]['email'])) {
            // sending email
			require_once('class.phpmailer.php');
//			$this->load->helper('class_phpmailer_helper');
			$mail = new PHPMailer(true);
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->CharSet = 'UTF-8';
			$mail->Host = "smtp.office365.com"; //"smtp.gmail.com";      // sets GMAIL as the SMTP server
			$mail->SMTPAuth = true;                    // enable SMTP authentication
			$mail->Port = 587; //465;
			$mail->SMTPSecure = "tls";    //"ssl";                 // sets the prefix to the servier
			$mail->SMTPDebug = 0;                     // enables SMTP debug information (for testing)
			$mail->Username = SMTPUSERNAME; //"smtp@fxbytes.com"; // SMTP account username
			$mail->Password = SMTPPASSWORD; //"smtp*123";        // SMTP account password
			$mail->isHTML(true);
            #end config
			
            try {
                if (isset($fromDataList[0]['email']) && !empty($fromDataList[0]['email'])) {
                    $mail->SetFrom($fromDataList[0]['email'], "".$fromDataList[0]['fullname']."");
                    $mail->AddReplyTo($fromDataList[0]['email'], "".$fromDataList[0]['fullname']."");
                } else {
                    $from = "WiseworkingSystems@wiseworking.com.au";
                    $mail->SetFrom($from, "Wiseworking");
                }

                $mail->Subject = trim($subject);
                $mess = "<html><body>";
                $mess .= nl2br(trim($message));
                $mess .= "</body></html>";
                $mail->MsgHTML($mess);

                //Add To here
                for ($i = 0; $i < sizeof($toDataList); $i++) {
                    if (!empty($toDataList[$i]['email'])) {
                        $mail->AddAddress(trim($toDataList[$i]['email']), ''); //send to mail heree
                    }
                }

                //Add CC here
                for ($i = 0; $i < sizeof($ccDataList); $i++) {
                    if (!empty($ccDataList[$i]['email'])) {
                        $mail->AddCC(trim($ccDataList[$i]['email']), ''); //send to mail heree
                    }
                }

                //Add BCC here
                for ($i = 0; $i < sizeof($bccDataList); $i++) {
                    if (!empty($bccDataList[$i]['email'])) {
                        $mail->AddBCC(trim($bccDataList[$i]['email']), ''); //send to mail heree
                    }
                }

                //Add AddAttachment here
                for ($i = 0; $i < sizeof($attachmentList); $i++) {
                    if (!empty($attachmentList[$i])) {
                        $mail->AddAttachment(trim($attachmentList[$i]), ''); //send to mail heree
                    }
                }

                $result = $mail->Send();
                $mail->ClearAllRecipients();
                $mail->ClearAddresses();
                $mail->ClearAttachments();
                //unlink(trim($this->input->post('attachPath')));

                if ($result == FALSE) {
                    return "<h3 style='color:red;'>Failure, can not send mail.</h3>";
                } else {
                    return "<h3 style='color:green;'>Email sent successfully.</h3>";
                }
            } catch (Exception $e) {
                return $e->errorMessage();
                return "<h3 style='color:red;'>Failure, can not send mail.</h3>";
            }
        }
    }

}
?>
