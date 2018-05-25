<?php
//error_reporting(E_ALL);
ini_set('display_errors', '0');
	session_start();
#secho '<pre>';print_r($_SESSION);print_r($_REQUEST);$_GET['projectID'];die;

	include("./includes/functions.php");
	$obj = new DB_Class();

	include('./includes/commanfunction.php');
	$object = new COMMAN_Class();

//Fetch Drawing and its revision Data Start here
	$dwgRegData = $object->selQRYMultiple('GROUP_CONCAT(id) AS dwgRegIds, count(id) AS revisionCount', 'drawing_register_revision_module_one', 'project_id = '.$_REQUEST['projectID'].' AND is_deleted = 0 AND drawing_register_id = '.$_GET['pdfID']);
	$dwgRegIds = $_GET['pdfID'].','.$dwgRegData[0]['dwgRegIds'];
//Fetch Drawing and its revision Data End here

//Fetch User Data 
	mysql_query('SET SESSION group_concat_max_len = 4294967295');
	$userData = $object->selQRYMultiple('GROUP_CONCAT(DISTINCT user_id) AS userIds', 'user_projects', 'project_id = '.$_REQUEST['projectID'].' AND is_deleted = 0');

	$userNameData = $object->selQRYMultiple('user_id, user_fullname, company_name', 'user', 'user_id IN ('.$userData[0]['userIds'].') AND is_deleted = 0');
	$userNameArr = array();
	foreach($userNameData as $uNameData){
		$userNameArr[$uNameData['user_id']] = $uNameData['user_fullname'];
	}

	$aColumns = array("table_history_details.created_date", "user.user_fullname", "sql_query", "table_history_details.resource_type", "sql_operation", "table_name", "table_history_details.primary_key");
	
	$sIndexColumn = "id";
	
	$sTable = "table_history_details, user";
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	if ( isset( $_GET['iSortCol_0'] ) ){
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ){
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ){
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" ){
			$sOrder = "";
		}
	}

	$sWhere = "WHERE table_name = 'drawing_register_module_one' AND table_history_details.created_by = user.user_id AND table_history_details.is_deleted = 0 AND primary_key IN (".$dwgRegIds.") AND user.is_deleted = 0 AND project_id = ".$_GET['projectID'];
	
	$sOrder = "ORDER BY table_history_details.created_date ASC";

	$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit";
		
#echo $sQuery;die;
		
	$rResult = $obj->db_query($sQuery ) or die(mysql_error());

	$sQuery = "SELECT FOUND_ROWS()";
	
	$rResultFilterTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	$sQuery = "SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable $sWhere";
	$rResultTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	$outputStr = '';
	$inspData = $object->selQRY('dr.id, dr.project_id, dr.title, dr.pdf_name, dr.dwg_name, dr.img_name, dr.number, dr.revision, dr.comments, dr.tag, dr.attribute1, dr.attribute2, dr.resource_type, dr.is_approved, dr.status, dr.created_by, dr.created_date, drr.id, drr.pdf_name, drr.revision_number, drr.comments, drr.revision_status',
	
	'drawing_register_revision_module_one AS drr, drawing_register_module_one AS dr',
	
	'dr.id = drr.drawing_register_id AND
	dr.is_deleted = 0 AND
	drr.is_deleted = 0 AND
	dr.id = '.$_GET['pdfID'].' AND
	dr.project_id = '.$_GET['projectID']);
	
	if(!empty($inspData)){
		$isApprove = "No";
		if($inspData['is_approved']){
			$isApprove = "Yes";
		}
		$outputStr .= "Document Added with these Details<br />";
		$outputStr .= "Drawing Title: ".$inspData['title']."<br />
						Drawing Number: ".$inspData['number']."<br />
						Drawing Revision: ".$inspData['revision']."<br />
						Comments: ".$inspData['comments']."<br />
						Attribute 1: ".$inspData['attribute1']."<br />
						Attribute 2: ".str_replace('###', ', ', $inspData['attribute2'])."<br />
						Tag: ".$inspData['tag']."<br />
						Status: ".$inspData['status']."<br />
						Download on iPad: ".$isApprove;
	}

	$output['sEcho'] = 1;
	$output['iTotalRecords'] = 1;
	$output['iTotalDisplayRecords'] = 1;
#	$output['aaData'][];
	$arrOne = array(date('d/m/Y h:i:s a', strtotime($inspData['created_date'])), $userNameArr[$inspData['created_by']], $outputStr, $inspData['resource_type'], $inspData['primary_key']);
	
	while ( $aRow = mysql_fetch_array( $rResult ) ){
		$row = array();
		$qryData = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			if ( $aColumns[$i] == "table_history_details.created_date" ){
				$createdDate = date('d/m/Y h:i:s a', strtotime($aRow['created_date']));
			}
			if ( $aColumns[$i] == "user.user_fullname" ){
				$createdBy = $aRow['user_fullname'];
			}
			if ( $aColumns[$i] == "sql_query" ){
				$qryData = unserialize($aRow['sql_query']);
			}			
			if ( $aColumns[$i] == "table_history_details.primary_key" ){
				$qryData['primary_key']=$aRow['primary_key'];
			}
			if ( $aColumns[$i] == "table_history_details.resource_type" ){
				$resourceType = $aRow['resource_type'];
			}			
			if ( $aColumns[$i] == "sql_operation" ){
				$sqlOperation = $aRow['sql_operation'];
			}			
			if ( $aColumns[$i] == "table_name" ){
				$tableName = $aRow['table_name'];
			}			
		}
		$row[] = $createdDate;
		$row[] = $createdBy;
		$row[] = $qryData;
		$row[] = $resourceType;
		$row[] = $sqlOperation;
		$row[] = $tableName;
		$output['aaData'][] = $row;
	}

	$rowDescArr = array();
#	echo '<pre>';print_r($output);

//Check condition here for Record start form the insertion
	$isDeleted = false;
/*	if(!empty($output)){
		$isDeleted = true;
		if($output['aaData'][0][4] != 'INSERT'){//Fetch Data form the Inseted recod from main table and put into history table and re arrange Data here.
			echo 'Special condition here';die;
			$inspData = $object->selQRY('dr.id, dr.project_id, dr.title, dr.pdf_name, dr.dwg_name, dr.img_name, dr.number, dr.revision, dr.comments, dr.tag, dr.attribute1, dr.attribute2, dr.resource_type, dr.is_approved, dr.status, dr.created_by, dr.created_date, drr.id, drr.pdf_name, drr.revision_number, drr.comments, drr.revision_status',
	
			'drawing_register_revision_module_one AS drr, drawing_register_module_one AS dr',
			
			'dr.id = drr.drawing_register_id AND
			dr.is_deleted = 0 AND
			drr.is_deleted = 0 AND
			dr.id = '.$_GET['pdfID'].' AND
			dr.project_id = '.$_GET['projectID']);

			$inspDataArr = array(
				'title' => $inspData['title'],
				'number' => $inspData['number'],
				'revision' => $inspData['revision'],
				'comments' => $inspData['comments'],
				'attribute1' => $inspData['attribute1'],
				'attribute2' => str_replace('###', ', ', $inspData['attribute2']),
				'status' => $inspData['status'],
				'tag' => $inspData['tag'],
				'is_approved' => $inspData['is_approved'],
				'pdf_name' => $inspData['pdf_name'],
			);
			
			$insertedDataArr = array(
				$inspData['created_date'],
				$userNameArr[$inspData['created_by']],
				$inspDataArr,
				$inspData['resource_type'],
				'INSERT',
				'drawing_register_module_one'
			);
			
//Re Arrange Data Here
			$newaaData = array();
			$newaaData[] = $insertedDataArr;
			for($i=0;$i<sizeof($output['aaData']);$i++){
				$newaaData[] = $output['aaData'][$i];
			}
			$output['aaData'] = $newaaData;
//Insert data in history table
			$insertQRY = "INSERT INTO table_history_details SET
									primary_key = '".$_GET['pdfID']."',
									table_name = 'drawing_register',
									sql_operation = 'INSERT',
									sql_query = '". serialize($inspDataArr)."',
									created_by = '".$_SESSION['ww_builder_id']."',
									created_date = '".$inspData['created_date']."',
									last_modified_by = '".$_SESSION['ww_builder_id']."',
									last_modified_date = '".$inspData['created_date']."',
									project_id = '".$_GET['projectID']."',
									resource_type = '".$inspData['resource_type']."'";
			mysql_query($insertQRY);
		}
	}*/

//Check condition here for Record start form the insertion
	for($i=1;$i<sizeof($output['aaData']);$i++){
		$cPointer = $i;
		$pPointer = $i-1;
		$rowDataArr = array();
		
		foreach($output['aaData'][$i][2] as $key=>$value){
			$rowDataArr[] = compair($key, $output['aaData'][$cPointer][2][$key], $output['aaData'][$pPointer][2][$key]);
		}
		$rowDataArr = array_values(array_filter($rowDataArr));
		$rowDescArr[] = join('<br />', $rowDataArr);
	}
	unset($output['aaData'][0]);
	//ReIndex Array Here
	$output['aaData'] = array_values($output['aaData']);
	$lupCount = sizeof($output['aaData']);
	
#	print_r($output['aaData']);	print_r($rowDescArr);	die;
	if($lupCount > 0){
		for($i=0;$i<$lupCount;$i++){
			if($rowDescArr[$i] != "")
				$output['aaData'][$i][2] = $rowDescArr[$i];
			else{
				$output['aaData'][$i][2] = "No data gjouda";
				unset($output['aaData'][$i]);
			}
		}
	}

	$tempArr[] = $arrOne;
	if(!empty($output['aaData'])){
		foreach($output['aaData'] as $k=>$v){
			$tempArr[] = $v;
		}
	}

	$newOutputArr = array('sEcho' => ($output['sEcho']+$lupCount), 'iTotalRecords' => ($output['iTotalRecords']+$lupCount), 'iTotalDisplayRecords' => ($output['iTotalDisplayRecords']+$lupCount), 'aaData' => $tempArr);

	echo json_encode( $newOutputArr );

//Function Part Start Here
function compair($key, $cData, $pData){
	$returnStr = '';$keyName = '';
	if($cData != $pData){
#echo $key.'++++'.$cData.'++++'.$pData.'<br />';
		if($key == 'primary_key'){
			if($cData != $pData)
				$returnStr = '<strong>New Revision Added</strong>';
		}

		if($key == 'title'){
			if($cData != '' && $pData == '')
				$returnStr = 'Drawing Title added '.$cData;
			elseif($cData != $pData)
				$returnStr = 'Drawing Title updated from '.$pData.' to '.$cData;
		}

		if($key == 'title'){
			if($cData != '' && $pData == '')
				$returnStr = 'Drawing Title added '.$cData;
			elseif($cData != $pData)
				$returnStr = 'Drawing Title updated from '.$pData.' to '.$cData;
		}
		if($key == 'number'){
			if($cData != '' && $pData == '')
				$returnStr = 'Drawing Number added '.$cData;
			elseif($cData != $pData)
				$returnStr = 'Drawing Number updated from '.$pData.' to '.$cData;
		}
		if($key == 'revision'){
			if($cData != '' && $pData == '')
				$returnStr = 'Drawing Revision Type added '.$cData;
			elseif($cData != $pData)
				$returnStr = 'Drawing Revision updated from '.$pData.' to '.$cData;
		}
		if($key == 'comments'){
			if($cData != '' && $pData == '')
				$returnStr = 'Drawing Comments added '.$cData;
			elseif($cData != $pData)
				$returnStr = 'Drawing Comments updated from '.$pData.' to '.$cData;
		}
		if($key == 'attribute1'){
			if($cData != '' && $pData == '')
				$returnStr = 'Drawing Attribute1 added '.$cData;
			elseif($cData != $pData)
				$returnStr = 'Drawing Attribute1updated from '.$pData.' to '.$cData;
		}
		if($key == 'attribute2'){
			if($cData != '' && $pData == '')
				$returnStr = 'Drawing Attribute2 added '.$cData;
			elseif($cData != $pData)
				$returnStr = 'Drawing Attribute2 updated from '.$pData.' to '.$cData;
		}
		if($key == 'status'){
			if($cData != '' && $pData == '')
				$returnStr = 'Status added '.$cData;
			elseif($cData != $pData)
				$returnStr = 'Status updated from '.$pData.' to '.$cData;
		}
		if($key == 'tag'){
			if($cData != '' && $pData == '')
				$returnStr = 'Tag added '.$cData;
			elseif($cData != $pData)
				$returnStr = 'Tag updated from '.$pData.' to '.$cData;
		}
		if($key == 'is_approved'){
			if($cData != '' && $pData == ''){
				$approveStatus = 'No';
				if($cData == 1){$approveStatus = 'Yes';}
				$returnStr = 'Download on iPad added '.$approveStatus;
			}elseif($cData != $pData){
				$approveStatusPrevious = 'No';
				$approveStatus = 'No';
				if($cData == 1){$approveStatus = 'Yes';}
				if($pData == 1){$approveStatusPrevious = 'Yes';}
				$returnStr = 'Download on iPad updated from '.$approveStatusPrevious.' to '.$approveStatus;
			}
		}
/*		if($key == 'pdf_name'){
			if($cData != '' && $pData == '')
				$returnStr = 'Drawing Register revision added ';
			elseif($cData != $pData)
				$returnStr = 'Drawing Register revision added ';
		}*/
	}
	return $returnStr;
}
//Function Part End Here
?>