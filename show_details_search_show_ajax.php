<?php
session_start();
set_time_limit(60000000000);

	include("./includes/functions.php");
	$obj = new DB_Class();
	include('./includes/commanfunction.php');
	$common = new COMMAN_Class();
	$projectName = $common->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name');
	$refrenceNo = explode(' ',$projectName);
	if(is_array($refrenceNo)){
		$refrenceNo = strtolower($refrenceNo[0]);
	}else{
		$refrenceNo = strtolower($projectName);
	}
//Issued TO for Inspection table, should be combined only if required. Same for Location Table.	
if(isset($_REQUEST['name'])){
		
	$_SESSION['pmb'] = $_REQUEST;//Set Session for back implement and Remeber
	setcookie($_SESSION['ww_builder_id'].'_qc', serialize($_REQUEST), time()+864000);

//print_r($_POST['recipTo']);

	$where = " AND um.message_id = m.message_id AND um.project_id = '".$_SESSION['idp']."' ";
	if(isset($_REQUEST['messageType']) && !empty($_REQUEST['messageType'])){
		$where.=" AND m.message_type = '".$_REQUEST['messageType']."'";
	}
	
	if(isset($_REQUEST['searchKey']) && !empty($_REQUEST['searchKey'])){
		$where.=" and (m.title Like '%".$_REQUEST['searchKey']."%' or m.message Like '%".$_REQUEST['searchKey']."%') ";
	}
	
	if(isset($_REQUEST['tags']) && !empty($_REQUEST['tags'])){
		$where.=" and (m.tags Like '%".$_REQUEST['tags']."%' or m.tags Like '%".$_REQUEST['tags']."%') ";
	}

	if(isset($_REQUEST['companyTag']) && !empty($_REQUEST['companyTag'])){
		$where.=" and (m.company_tag Like '%".$_REQUEST['companyTag']."%' or m.company_tag Like '%".$_REQUEST['companyTag']."%') ";
	}
	
	if(isset($_REQUEST['referenceNo']) && !empty($_REQUEST['referenceNo'])){
		$_REQUEST['referenceNo'] = str_replace($refrenceNo,'',str_replace(" ",'',$_REQUEST['referenceNo']));
		$where.=" and um.user_message_id ='".$_REQUEST['referenceNo']."'";
	}
			
	if($_REQUEST['dateFrom']!="" && $_REQUEST['dateTo']!=""){
		$where.= " and m.sent_time between '".date('Y-m-d', strtotime($_REQUEST['dateFrom']))."' and '".date('Y-m-d', strtotime($_REQUEST['dateTo']))."'";
		
	}else if($_REQUEST['dateFrom']!="" && $_REQUEST['dateTo']==""){
		$where.= " and m.sent_time >= '".date('Y-m-d', strtotime($_REQUEST['dateFrom']))."'";
		
	}else if($_REQUEST['dateFrom']=="" && $_REQUEST['dateTo']!=""){
		$where.= " and m.sent_time <='".date('Y-m-d', strtotime($_REQUEST['dateTo']))."'";
	}
	
	$toIds =''; $toEmails ='';
	if(isset($_REQUEST['recipTo']) && !empty($_REQUEST['recipTo']) && $_REQUEST['recipTo']!='null'){
		$recipTo = explode(',', $_REQUEST['recipTo']);
		if(is_array($recipTo)){
			foreach($recipTo as $to){
				if(is_numeric($to)){
					if($toIds == ""){
						$toIds = $to;
					}else{
						$toIds.= ",".$to;
					}
				}elseif(!empty($to)){
					if($toEmails == ""){
						$toEmails = "m.to_email_address like '%".$to."%'";
					}else{
						$toEmails.= " or m.to_email_address like '%".$to."%'";
					}
				}
			}
		}else{
			if(is_numeric($_REQUEST['recipTo'])){
				if($toIds == ""){
					$toIds = $_REQUEST['recipTo'];
				}
			}elseif(!empty($_REQUEST['recipTo'])){
				if($toEmails == ""){
					$toEmails = "m.to_email_address like '%".$_REQUEST['recipTo']."%'";
				}
			}
		}
		if($toIds != '' && $toEmails != ''){
			$where.= " and (um.user_id in(".$toIds.") or ".$toEmails.")";
		}else if($toIds != ''){
			$where.= " and um.user_id in(".$toIds.")";
		}else if($toEmails != ''){
			$where.= " and (".$toEmails.")";
		}
	}
	
	$fromIds =''; $fromEmails ='';
	if(isset($_REQUEST['recipFrom']) && !empty($_REQUEST['recipFrom']) && $_REQUEST['recipFrom']!='null'){
		$recipFrom = explode(',', $_REQUEST['recipFrom']);
		if(is_array($recipFrom)){
			foreach($recipFrom as $from){
				if(is_numeric($from)){
					if($fromIds == ""){
						$fromIds = $from;
					}else{
						$fromIds.= ",".$from;
					}
				}
			}
		}else{
			if(is_numeric($_REQUEST['recipFrom'])){
				if($fromIds == ""){
					$fromIds = $_REQUEST['recipFrom'];
				}
			}
		}
		if($fromIds != ''){
			$where.= " and um.from_id in(".$fromIds.")";
		}
	}

//	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}

}

	$project_id = $_SESSION['idp'];
	
	$aColumns = array('um.user_message_id', 'um.from_id', 'um.user_id', 'm.title', 'm.message', 'm.sent_time', 'um.type', 'm.is_draft', 'um.thread_id');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "um.user_message_id";
	
	/* DB table to use */
	$sTable = "pmb_user_message as um, pmb_message m ";
	
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	if ( isset( $_GET['iSortCol_0'] ) ){
		$sOrder = "ORDER BY ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ){
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ){
				if ((intval( $_GET['iSortCol_'.$i] ) -1) == -1)
					$sOrder .= $aColumns[ 0]."
					".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
				else{
					$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) -1]."
					".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
				}
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" ){
			$sOrder = "ORDER BY m.sent_time desc";
		}
	}
	$sOrder;
	$sWhere = "";
	
	if ( $_GET['sSearch'] != "" ){
		$sWhere = "WHERE um.deleted = '0' ". $where ." and (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ){
			if ( $sWhere == "" ){
				$sWhere = "WHERE ";
			}else{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	if ($sWhere == ""){
		$sWhere = "WHERE um.deleted = '0' ". $where;
	}
	
	 $sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM $sTable $sWhere GROUP BY um.type, um.thread_id $sOrder $sLimit ";
	$rResult = $obj->db_query($sQuery) or die(mysql_error());
#die($sQuery);
	/* Data set length after filtering */
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "SELECT COUNT(".$sIndexColumn.") FROM $sTable $sWhere";
	$rResultTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultFilterTotal[0];

	//$iTotal = mysql_num_rows($rResult);
	
//Find Issue to name and location name data here
	
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);

//	$aColumns = array('um.user_message_id', 'um.from_id', 'um.user_id', 'm.title', 'm.message', 'm.sent_time', 'um.type', 'm.is_draft');
	while ( $aRow = mysql_fetch_array( $rResult ) ){
		$row = array();		
		$countThread = threadCount($aRow['thread_id'], $aRow['type']);
		$row[] = $refrenceNo." ".$aRow['user_message_id'];
		
		$row[] = ($aRow['from_id']!=0)?$common->getDataByKey('user', 'user_id', $aRow['from_id'], 'user_fullname', ''):'Other';
		$row[] = ($aRow['user_id']!=0)?$common->getDataByKey('user', 'user_id', $aRow['user_id'], 'user_fullname', ''):'Other';
		
		$row[] = strip_tags(html_entity_decode($aRow['title'], ENT_QUOTES, 'UTF-8')).(($countThread>1)?'('.$countThread.')':'');
		$aRow['message'] = strip_tags(html_entity_decode($aRow['message'], ENT_QUOTES, 'UTF-8'));
		if(strlen($aRow['title'])>30) {
			$row[] = substr($aRow['message'], 0,30)." ...";
		}else{
			$row[] = html_entity_decode($aRow['message'], ENT_QUOTES, 'UTF-8');			
		}
		if($aRow['is_draft']==1){
			$row[] = "draft";
		}else{
			$row[] = $aRow['type'];
		}
		$row[] = date('d/m/Y H:i:s' ,strtotime($aRow['sent_time']));
		
		if($aRow['is_draft']==1){
			$row[] = '<a href="?sect=view_message&id='.base64_encode($aRow['user_message_id']).'&type=draft&page=details_search"><img border="none" src="images/d_photo.png"></a>';
		}elseif($aRow['type']=='inbox'){
			$row[] = '<a href="?sect=message_details&id='.base64_encode($aRow['thread_id']).'&type=inbox&page=details_search"><img border="none" src="images/d_photo.png"></a>';
		}else{
			$row[] = '<a href="?sect=message_details&id='.base64_encode($aRow['thread_id']).'&type=sent&page=details_search"><img border="none" src="images/d_photo.png"></a>';
		}
		
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
	
function threadCount($id, $type){
	$sql="select count(*) as num from pmb_user_message where type = '".$type."' AND thread_id='".$id."' AND is_deleted='0' group by message_id";
	$result=mysql_query($sql);
	$row=mysql_fetch_array($result);
	//return $row['num'];
	return mysql_num_rows($result);
}	
?>