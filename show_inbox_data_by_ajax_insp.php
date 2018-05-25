<?php session_start();
$builder_id=$_SESSION['ww_builder_id'];

#error_reporting(E_ALL);
#ini_set('display_errors', 1);
 
include("./includes/functions.php");
$obj = new DB_Class();
include('./includes/commanfunction.php');
$common = new COMMAN_Class();

//Function section start here
function getUserNameByEmailids($emailIDs){
	$tempArr = explode(',', str_replace(", ", ",", $emailIDs));
	array_walk($tempArr, 'inQueryData');
//	print_r($tempArr);
	$nameDataArr = array();
	$sql = "SELECT
						ab.full_name as name,
						user_email as email
					FROM
						pmb_address_book as ab
					where
						user_email IN ('". join("','", $tempArr) ."')
			UNION
					SELECT
						iss.company_name as name,
						iss.issue_to_email as email
					FROM
						inspection_issue_to as iss
					WHERE
						issue_to_email IN ('". join("','", $tempArr) ."')";
	
	$result = mysql_query($sql);

	while($row = mysql_fetch_array($result)){
		$nameDataArr[] = $row['name'];
	}
	return join(", ", $nameDataArr);
}
	
function inQueryData($str){
	return '"'.$str.'"';
} 
//Function section end here
	
if(isset($_REQUEST["name"])){
	$project_id = 0;
	if(isset($_SESSION['idp']))
		$project_id = $_SESSION['idp'];
		
#if($_REQUEST['SearchReq'] != "" && $_REQUEST['SearchReq'] != ""){
	$issuedToTable = "";	
	$where = "I.is_deleted = 0";
	if(!empty($_REQUEST['projName'])){$where.=" AND I.project_id='".$_REQUEST['projName']."'";}

	if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
		$where.=" and I.location_id in (".$common->subLocationsId($_REQUEST['location'], ", ").")";
	}
	
	if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['subSubLocation'])){
		$where.=" and I.location_id in (".$common->subLocationsId($_REQUEST['subSubLocation'], ", ").")";
	}else{
		if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
			$where.=" and I.location_id in (".$common->subLocationsId($_REQUEST['subLocation'], ", ").")";
		}
	}

	if(!empty($_REQUEST['status'])){
		$where.=" and I.inspection_id = F.inspection_id and F.inspection_status='".$_REQUEST['status']."'";
		$issuedToTable = " , issued_to_for_inspections as F";
	}
	
	if(!empty($_REQUEST['inspectedBy'])){
		$where.=" and I.inspection_inspected_by='".$_REQUEST['inspectedBy']."'";
	}
	
	if($_REQUEST['issuedTo']!=""){
		$where.="  and I.inspection_id = F.inspection_id and F.issued_to_name='".$_REQUEST['issuedTo']."' ";
		$issuedToTable = " , issued_to_for_inspections as F";
	}
	
	if($_REQUEST['inspecrType']!=""){$where.=" and I.inspection_type='".$_REQUEST['inspecrType']."'";}
	
	if(!empty($_REQUEST['costAttribute'])){
		$where.="  and I.inspection_id = F.inspection_id and F.cost_attribute = '".$_REQUEST['costAttribute']."'";
		$issuedToTable = " , issued_to_for_inspections as F";
	}
	
	if(!empty($_SESSION['userRole']) && $_SESSION['userRole'] != 'Sub Contractor'){
		if($_SESSION['userRole'] != 'All Defect'){
			$where.=" and I.inspection_raised_by = '".$_SESSION['userRole']."'";
		}else{
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and I.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
		}
	}else{
		if($_REQUEST['raisedBy'] != 'All Defect'){
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and I.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
		}
	}

	if($_SESSION['userRole'] == 'Sub Contractor'){
		$where.=" and I.inspection_id = F.inspection_id and  F.issued_to_name = '".$_SESSION['userIssueTo']."'";
		$issuedToTable = " , issued_to_for_inspections as F";
	}

	if($_REQUEST['DRF']!="" && $_REQUEST['DRT']!=""){
		$or.=" I.inspection_date_raised between '".date('Y-m-d', strtotime($_REQUEST['DRF']))."' and '".date('Y-m-d', strtotime($_REQUEST['DRT']))."'";
		$issuedToTable = " , issued_to_for_inspections as F";
		$where .= " and I.inspection_id = F.inspection_id ";
	}
	
	if($_REQUEST['DRF']!="" && $_REQUEST['FBDF']!=""){$or.=" and";}
	
	if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
		$or.=" F.inspection_fixed_by_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
		$issuedToTable = " , issued_to_for_inspections as F";
		$where .= " and I.inspection_id = F.inspection_id ";
	}

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "I.inspection_id";
	
	/* DB table to use */
	$sTable = "project_inspections as I" . $issuedToTable;

	$sLimit = "";

	$sQuery = "SELECT SQL_CALC_FOUND_ROWS I.inspection_id, I.inspection_location, I.check_list_item_id FROM $sTable WHERE $where group by I.inspection_id $sOrder $sLimit ";
#echo $sQuery;
	$rResult = $obj->db_query($sQuery) or die(mysql_error());
	$inspArr = array();
	$locidsArr = array();
	$locInspArr = array();
	while($aRow = mysql_fetch_assoc($rResult)){	
		$inspArr[] = $aRow['inspection_id'];
		$locInspArr[$aRow['inspection_id']] = $aRow['inspection_location'];
	}
#}
#print_r($locInspArr);die;
	$userNameIdArr = array();
	$unreadCountArr = array();
//Data Section Start Here
	$permArray = array('General Consultant', 'Architect', 'Structural Engineer', 'Services Engineer', 'Sub Contractor', 'Client');
	
	$userEmailData = $common->selQRYMultiple('DISTINCT issue_to_id, issue_to_name, company_name, issue_to_email, project_id', 'inspection_issue_to', 'project_id = '.$project_id.' AND is_deleted = 0 ORDER BY project_id');
	
#$userEmailData = $common->selQRYMultiple('DISTINCT u.user_id, u.user_name, u.user_fullname, u.user_email, up.project_id, up.project_name, u.user_type, u.user_fullname, u.recieve_email', 'user_projects AS up, user AS u', 'up.user_id = u.user_id AND up.project_id = '.$project_id.' AND up.is_deleted = 0 AND u.is_deleted = 0 AND is_pdf = 1 ORDER BY project_id');
	
	foreach($userEmailData as $uEmailData){
		if($uEmailData['company_name'] != "")
			$userNameIdArr[$uEmailData['issue_to_id']] = $uEmailData['issue_to_name'] . ' ('.$uEmailData['company_name'].')';
		else
			$userNameIdArr[$uEmailData['issue_to_id']] = $uEmailData['issue_to_name'];
	}
 
	$unreadCountData = $common->selQRYMultiple('thread_id, count(*) AS num', 'pmb_user_message', 'user_id ='.$_SESSION['ww_builder_id'].' AND type = "inbox" AND inbox_read = 0 AND is_deleted = 0 AND project_id = '.$project_id.' GROUP BY thread_id');
	if(!empty($unreadCountData)){
		foreach($unreadCountData as $unCountData){
			$unreadCountArr[$unCountData['thread_id']] = $unCountData['num'];
		}
	}
	
	$threadCountData = $common->selQRYMultiple('thread_id, count(*) AS num', 'pmb_user_message', 'user_id ='.$_SESSION['ww_builder_id'].' AND type = "inbox" AND is_deleted = 0 AND project_id = '.$project_id.' GROUP BY message_id');
	if(!empty($unreadCountData)){
		foreach($threadCountData as $thCountData){
			$threadCountArr[$thCountData['thread_id']] = $thCountData['num'];
		}
	}
//Data Section End Here
	$sWhere = "";

	if(isset($_GET['folderType']) && empty($_GET['folderType'])){
		$aColumns = array( 'um.thread_id', 'um.from_id', 'm.title', 'm.message_type', 'm.sent_time', 'um.user_id', 'um.message_id', 'um.inbox_read', 'm.message', 'm.inspection_id', 'um.user_message_id', 'um.rfi_number', 'um.rfi_description', 'm.rfi_status'
		, 'GROUP_CONCAT(DISTINCT um.from_id) AS fromID'
		 );
	}elseif(isset($_GET['folderType']) && $_GET['folderType'] != "Request For Information"){
		$aColumns = array( 'um.thread_id', 'um.from_id', 'm.inspection_id', 'um.rfi_number', 'um.rfi_description', 'm.rfi_status', 'um.user_id', 'um.message_id', 'um.inbox_read', 'm.title', 'm.message', 'm.message_type', 'm.sent_time', 'um.user_message_id'
		, 'GROUP_CONCAT(DISTINCT um.from_id) AS fromID'
		 );
	}else{
		$aColumns = array( 'um.thread_id', 'um.user_id', 'm.title', 'um.message_id', 'um.from_id', 'um.inbox_read', 'm.title', 'm.message', 'm.message_type', 'm.sent_time', 'm.inspection_id', 'um.user_message_id', 'um.rfi_number', 'um.rfi_description', 'm.rfi_status'
		, 'GROUP_CONCAT(DISTINCT um.from_id) AS fromID'
		 );
	}
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "um.user_id";
	
	/* DB table to use */
	$sTable = "pmb_user_message as um, pmb_message m";
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}

	if ( isset( $_GET['iSortCol_0'] ) && !empty( $_GET['iSortCol_0'] ) ){
		$sOrder = "ORDER BY ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ){
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ){
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" ){
			$sOrder = " ORDER BY m.sent_time DESC";
		}
	}

	$spCon = "um.type = 'inbox' AND um.inbox_read = 0 AND ";
	if(isset($_GET['folderType']) && !empty($_GET['folderType'])){
		$spCon = "m.message_type = '".$_GET['folderType']."' AND (um.type = 'inbox'  OR um.type = 'sent') AND ";
	}	
	
	if ( $_GET['sSearch'] != "" ){
		$sWhere = "WHERE um.is_deleted = 0 AND um.project_id = ". $project_id ." AND (";
		for ( $i=0 ; $i<(count($aColumns)-1) ; $i++ ){
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ){
			if ( $sWhere == "" ){
				$sWhere = "WHERE um.is_deleted = 0 AND um.project_id = ".$project_id." AND ";
			}else{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	if ($sWhere == ""){
		$sWhere = "WHERE um.is_deleted = 0 AND um.project_id = ".$project_id." ";
	}

	if($_REQUEST['SearchReq'] != "" && $_REQUEST['SearchReq'] != "" && !empty($inspArr)){
		$sWhere .= " and m.inspection_id IN (".join(',', $inspArr).")";
	}

	$sWhere.= " AND (um.message_id = m.message_id AND
						".$spCon."
						m.is_draft = 0 AND
						um.user_id = '".$_SESSION['ww_builder_id']."')
						AND 
						(m.message_type IN ('General Correspondance', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect Instruction', 'Design Changes', 'Document Updated', 'Contract Adjustment', 'Recommendation', 'Tenders', 'Variation Claim', 'Inspections')
						OR
						(m.message_type IN ('Request For Information') AND inbox_read = 0))
					GROUP BY
						um.thread_id";
	if(!isset($sOrder)){
		$sOrder = " ORDER BY m.sent_time DESC";
	}
	
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere 
		$sOrder
		$sLimit";
#echo $sQuery ;die;
	$rResult = $obj->db_query($sQuery ) or die(mysql_error());

	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	$sQuery = "SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable $sWhere";
	$rResultTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultFilterTotal[0];
	
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);

	//We display the list of read messages
	while($inbox = mysql_fetch_array($rResult)) {
		$row =  array();
		$multipleUser = '';

		$fromIDArr = explode(",", $inbox['fromID']);
		foreach($fromIDArr as $key=>$frmID){
			if($frmID != ""){
				if($multipleUser == ''){
					$multipleUser .= $userNameIdArr[$frmID];
				}else{
					$multipleUser .= ', '.$userNameIdArr[$frmID];
				}
			}
		}

		if($thread['to_email_address'] != ''){
			$multipleUser =  $multipleUser.', '.$thread['to_email_address'];
		}
	#	$user = getuserdetails($inbox['from_id']);
	#	$count = threadCount($inbox['thread_id']);//Need to update
	#	$unread = unreadCount($inbox['thread_id']);//Need to update
		$unread = $unreadCountArr[$inbox['thread_id']];
		$count = $threadCountArr[$inbox['thread_id']];
		
		$inputFrom = '<input name="from[]" type="checkbox" value="'.$inbox['thread_id'].'" />';
		$row[] = ($unread>0)?"<b>".$inputFrom."</b>":$inputFrom;
		
		if(isset($_GET['folderType']) && empty($_GET['folderType'])){
			$messFrom = '<a href="?sect=message_details&id='.base64_encode($inbox['thread_id']).'&type=inbox">'.htmlentities($user['user_name'], ENT_QUOTES, 'UTF-8').'</a>';
			$row[] = ($unread>0)?"<b>".$messFrom."</b>":$messFrom;
		}else{
			if(strlen($multipleUser) > 50){
				$multipleUser = substr($multipleUser, 0, 50).'...';
			}
			
			$messTo = '<a href="?sect=message_details&id='.base64_encode($inbox['thread_id']).'&type=inbox">'.htmlentities($multipleUser, ENT_QUOTES, 'UTF-8').'</a>';
			$row[] = ($unread>0)?"<b>".$messTo."</b>":$messTo;
			
			$messCC = '<a href="?sect=message_details&id='.base64_encode($inbox['thread_id']).'&type=inbox">'.$locInspArr[$inbox['inspection_id']].'</a>';
			$row[] = ($unread>0)?"<b>".$messCC."</b>":$messCC;
		}	
		
		if($_GET['folderType'] != "Request For Information"){
				$inboxData = htmlentities($inbox['title'], ENT_QUOTES, 'UTF-8'); 
				if(strlen($inboxData) > 80){
					$inboxData = substr($inboxData, 0, 80).'...';
				}
				if($count>1) {
					$inboxData.= '('.$count.')';
				}
			$subject = '<a href="?sect=message_details&id='.base64_encode($inbox['thread_id']).'&type=inbox" style="'.(($count>1)?'background:url(images/reply.png) 0 no-repeat;':'').' ">'.$inboxData.'</a>';
			$row[] = ($unread>0)?"<b>".$subject."</b>":$subject;
			
		}else{
			$rfi_number = $inbox['rfi_number'];
			$row[] = ($unread>0)?"<b>".$rfi_number."</b>":$rfi_number;
			$rfi_desc = $inbox['rfi_description'];
			$row[] = ($unread>0)?"<b>".$rfi_desc."</b>":$rfi_desc;
			$rfi_status = $inbox['rfi_status'];
			$row[] = ($unread>0)?"<b>".$rfi_status."</b>":$rfi_status;
		}
		if(isset($_GET['folderType']) && empty($_GET['folderType'])){
			$message_type = $inbox['message_type'];
			$row[] = ($unread>0)?"<b>".$message_type."</b>":$message_type;
		}
			$sent_time = date('d/m/Y H:i:s' ,strtotime($inbox['sent_time']));
			$row[] = ($unread>0)?"<b>".$sent_time."</b>":$sent_time;
			
		if(!in_array($_SESSION['userRole'], $permArray)){
			$action = '<a onClick="delMessage('.$inbox['thread_id'].');" href="#" ><img src="images/del.png" width="16" height="16" alt="Delete" title="Delete" /></a>';
			$row[] = ($unread>0)?"<b>".$action."</b>":$action;
		} 
		$output['aaData'][] = $row;
//	</tr>
	}		
	echo json_encode( $output );
		
}
?>