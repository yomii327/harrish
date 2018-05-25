<?php session_start();
$builder_id=$_SESSION['ww_builder_id'];
 
include("./includes/functions.php");
$obj = new DB_Class();
include('./includes/commanfunction.php');
$common = new COMMAN_Class();
if(isset($_GET['view']) && $_GET['view'] == 'workflow'){
	$view = '&view=workflow';
}else{
	$view = '';
}

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
function getUserNameByEmailidsTo($emailIDs){
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
						user_email IN ('". join("','", $tempArr) ."') Limit 1";
	
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
	$project_id = $_SESSION['idp'];
	$userNameIdArr = array();
	$unreadCountArr = array();
//Data Section Start Here
	$permArray = array('General Consultant', 'Architect', 'Structural Engineer', 'Services Engineer', 'Sub Contractor', 'Client');
	$userEmailData = $common->selQRYMultiple('DISTINCT u.user_id, u.user_name, u.user_fullname, u.user_email, up.project_id, up.project_name, u.user_type, u.user_fullname, u.recieve_email', 'user_projects AS up, user AS u', 'up.user_id = u.user_id AND up.project_id = '.$project_id.' AND up.is_deleted = 0 AND u.is_deleted = 0 AND is_pdf = 1 ORDER BY project_id');
	foreach($userEmailData as $uEmailData){
		$userNameIdArr[$uEmailData['user_id']] = $uEmailData['user_fullname'];
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
		$aColumns = array( 'm.claimed, m.certified, m.invoiced, m.date_sent, m.date_approved, m.sent, m.approved, um.thread_id', 'um.from_id', 'm.correspondence_number', 'm.title', 'm.message_type', 'm.sent_time', 'um.user_id', 'um.message_id', 'um.inbox_read', 'm.message', 'm.cc_email_address', 'um.user_message_id', 'um.rfi_number', 'um.rfi_description', 'm.rfi_status', 'GROUP_CONCAT(DISTINCT um.from_id) AS fromID', 'm.to_email_address'
		 );
	}elseif(isset($_GET['folderType']) && $_GET['folderType'] != "Request For Information"){
		$aColumns = array( 'm.claimed, m.certified, m.invoiced, m.date_sent, m.date_approved, m.sent, m.approved, um.thread_id', 'um.from_id', 'm.cc_email_address', 'um.rfi_number', 'um.rfi_description', 'm.rfi_status', 'um.user_id', 'um.message_id', 'um.inbox_read', 'm.correspondence_number', 'm.purchaser_location', 'm.title', 'm.message', 'm.message_type', 'm.sent_time', 'um.user_message_id', 'GROUP_CONCAT(DISTINCT um.from_id) AS fromID', 'm.to_email_address'
		 );
	}else{
		$aColumns = array( 'm.claimed, m.certified, m.invoiced, m.date_sent, m.date_approved, m.sent, m.approved, um.thread_id', 'um.user_id', 'm.title', 'um.message_id', 'um.from_id', 'um.inbox_read', 'm.title', 'm.message', 'm.message_type', 'm.sent_time', 'm.cc_email_address', 'um.user_message_id', 'um.rfi_number', 'um.rfi_description', 'm.rfi_status', 'GROUP_CONCAT(DISTINCT um.from_id) AS fromID', 'm.to_email_address'
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
		$extraWhere = '';
		if($_GET['reqfrom'] == 'ajax'){
			$spCon = "m.message_type LIKE '%".$_GET['folderType']."%' AND (um.type = 'inbox'  OR um.type = 'sent') AND ";
			
		}else{
			$spCon = "m.message_type = '".$_GET['folderType']."' AND (um.type = 'inbox'  OR um.type = 'sent') AND ";
		}
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

		$sWhere.= " AND (um.message_id = m.message_id AND
						".$spCon."
						m.is_draft = 0 AND
						um.user_id = '".$_SESSION['ww_builder_id']."')
						AND 
						(m.message_type IN ('General Correspondence', 'Document Transmittal', 'Document Transmittal_Document Register Updates', 'Memorandum', 'Site Instruction', 'Consultant', 'Consultant_Architect / Superintendant Instruction', 'Consultant_Consultant Advice Notice', 'Consultant_Design', 'Consultant_Design_Design Changes', 'Consultant_Design_Design Meeting', 'Contract Admin', 'Contract Admin_Client Side', 'Contract Admin_Client Side_Variation Claims', 'Contract Admin_Client Side_EOTs', 'Contract Admin_Client Side_NODs', 'Contract Admin_Client Side_Formal Correspondence', 'Contract Admin_Client Side_Meeting minutes', 'Contract Admin_Client Side_Superintendant Instruction', 'Contract Admin_Client Side_Purchaser Changes', 'Contract Admin_Client Side_Inspections', 'Contract Admin_Subcontractors', 'Contract Admin_Subcontractors_Variation Claims', 'Contract Admin_Subcontractors_NODs', 'Contract Admin_Subcontractors_Formal Correspondence', 'Contract Admin_Subcontractors_Subcontractor Meetings', 'Estimating', 'Estimating_Tenders', 'Estimating_Construction', '".$_GET['folderType']."')
						OR
						(m.message_type IN ('Request For Information') AND inbox_read = 0)
						)
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
		#print_r($inbox);
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

		if($inbox['to_email_address'] != ''){
			if($multipleUser == ""){
				$multipleUser = getUserNameByEmailidsTo($inbox['to_email_address']);
				#$multipleUser = $inbox['to_email_address'];
			}else{
				$multipleUser =  $multipleUser.', '.getUserNameByEmailidsTo($inbox['to_email_address']);
				#$multipleUser =  $multipleUser.', '.$inbox['to_email_address'];
			}
		}
		if($inbox['cc_email_address'] != ''){
			$inbox['cc_email_address'] = getUserNameByEmailids($inbox['cc_email_address']);
		}

	#	$user = getuserdetails($inbox['from_id']);
	#	$count = threadCount($inbox['thread_id']);//Need to update
	#	$unread = unreadCount($inbox['thread_id']);//Need to update
		$unread = $unreadCountArr[$inbox['thread_id']];
		$count = $threadCountArr[$inbox['thread_id']];
		
		$inputFrom = '<input name="from[]" type="checkbox" value="'.$inbox['thread_id'].'" />';
		$row[] = ($unread>0)?"<b>".$inputFrom."</b>":$inputFrom;
		if(explode("_", $_GET['folderType'])){
			$folderTypeArr = end(explode("_", $_GET['folderType']));
		}else{
			$folderTypeArr = $_GET['folderType'];
		}		
		if(isset($_GET['folderType']) && empty($_GET['folderType'])){
			if($_GET['reqfrom'] == 'ajax'){
				$messFrom = htmlentities($user['user_name'], ENT_QUOTES, 'UTF-8');
			}else{
				$messFrom = '<a href="?sect=message_details&folderType='.$_GET['folderType'].'&id='.base64_encode($inbox['thread_id']).'&type=inbox'.$view.'">'.htmlentities($user['user_name'], ENT_QUOTES, 'UTF-8').'</a>';
			}
			$row[] = ($unread>0)?"<b>".$messFrom."</b>":$messFrom;
		}else{
			if(strlen($multipleUser) > 50){
				$multipleUser = substr($multipleUser, 0, 50).'...';
			}
			if($_GET['reqfrom'] == 'ajax'){
				$messTo = htmlentities($multipleUser, ENT_QUOTES, 'UTF-8');
			}else{
				$messTo = '<a href="?sect=message_details&folderType='.$_GET['folderType'].'&id='.base64_encode($inbox['thread_id']).'&type=inbox'.$view.'">'.htmlentities($multipleUser, ENT_QUOTES, 'UTF-8').'</a>';
			}
			$row[] = ($unread>0)?"<b>".$messTo."</b>":$messTo;
			
			if($_GET['reqfrom'] == 'ajax'){
				$messCC = htmlentities($inbox['cc_email_address'], ENT_QUOTES, 'UTF-8');
			}else{
				$messCC = '<a href="?sect=message_details&folderType='.$_GET['folderType'].'&id='.base64_encode($inbox['thread_id']).'&type=inbox'.$view.'">'.htmlentities($inbox['cc_email_address'], ENT_QUOTES, 'UTF-8').'</a>';
			}
			$row[] = ($unread>0)?"<b>".$messCC."</b>":$messCC;
		}	
		
		if($folderTypeArr != "Request For Information"){
			$inboxData = htmlentities($inbox['title'], ENT_QUOTES, 'UTF-8'); 
			if(strlen($inboxData) > 80){
				$inboxData = substr($inboxData, 0, 80).'...';
			}
			if($count>1) {
				$inboxData.= '('.$count.')';
			}
			if(isset($_GET['folderType']) && $folderTypeArr=="Purchaser Changes"){
				$row[] = $inbox['purchaser_location'];
			}
			
			if(isset($_GET['folderType']) && $folderTypeArr=="Consultant Advice Notice"){
				$rfi_number = $inbox['rfi_number'];
				$row[] = ($unread>0)?"<b>".$rfi_number."</b>":$rfi_number;
				$rfi_desc = $inbox['rfi_description'];
				#$row[] = ($unread>0)?"<b>".$rfi_desc."</b>":$rfi_desc;
			}
			
			if($folderTypeArr=='Variation Claims'){ 
			//$row[] = '<a onclick="openDetailsPopup('.$inbox['correspondence_number'].', '.$inbox['message_id'].')" href="javascript:void(0)">'.$inbox['correspondence_number'].'</a>';
				$row[] = $inbox['correspondence_number'];
			
			}else if($folderTypeArr=='Progress Claims'){ 
			$row[] = '<a onclick="openDetailsPopupPC('.$inbox['correspondence_number'].', '.$inbox['message_id'].')" href="javascript:void(0)">'.$inbox['correspondence_number'].'</a>';
			}else{
			$row[] = $inbox['correspondence_number'];
			}
			if($_GET['reqfrom'] == 'ajax'){
				$subject = $inboxData;
			}else{
				$subject = '<a href="?sect=message_details&folderType='.$_GET['folderType'].'&id='.base64_encode($inbox['thread_id']).'&type=inbox'.$view.'" style="'.(($count>1)?'background:url(images/reply.png) 0 no-repeat;':'').' ">'.$inboxData.'</a>';
			}
			$row[] = ($unread>0)?"<b>".stripslashes($subject)."</b>":$subject;
			
		}else{
			$rfi_number = $inbox['rfi_number'];
			$row[] = ($unread>0)?"<b>".$rfi_number."</b>":$rfi_number;
			$rfi_desc = $inbox['rfi_description'];
			$row[] = ($unread>0)?"<b>".$rfi_desc."</b>":$rfi_desc;
			
			if($_GET['reqfrom'] != 'ajax'){
				$rfi_status = $inbox['rfi_status'];
				$row[] = ($unread>0)?"<b>".$rfi_status."</b>":$rfi_status;
			}
		}
		if(isset($_GET['folderType']) && empty($_GET['folderType'])){
			if(explode("_", $sentBox['message_type'])){
				$message_type = end(explode("_", $inbox['message_type']));
			}else{
				$message_type = $inbox['message_type'];
			}
			$row[] = ($unread>0)?"<b>".$message_type."</b>":$message_type;
		}
		$ARR = array('Contract Admin_Client Side_Variation Claims', 'Progress Claims');
		//$ARR = array('Progress Claims');
		if(!in_array($_GET['folderType'],$ARR)){
		#if($_GET['folderType']!='Variation Claims' || $_GET['folderType']!='Progress Claims'){	
			$sent_time = date('d/m/Y H:i:s' ,strtotime($inbox['sent_time']));
			$row[] = ($unread>0)?"<b>".$sent_time."</b>":$sent_time;
		}	
		if($folderTypeArr=='Variation Claims' && $_GET['reqfrom'] != 'ajax'){
			if($inbox['date_sent']!='1970-01-01 00:00:00'){
				$row[] = date('d-m-Y',strtotime($inbox['date_sent']));
			}else{
				$row[] = '';
			}
			#echo $inbox['date_approved'];
			/*if($inbox['date_approved']!='1970-01-01 00:00:00'){
				$row[] = date('d-m-Y',strtotime($inbox['date_approved']));
			}else{
				$row[] = '';
			}*/
			if($_GET['reqfrom'] != 'ajax'){
				$row[] = $common->moneyFormat($inbox['claimed']);
				//$row[] = $common->moneyFormat($inbox['approved']);
			}
		}
		if($folderTypeArr=='Progress Claims'){
			if($inbox['date_approved']!='1970-01-01 00:00:00'){
				if(strtotime($inbox['date_sent']) != '0000:00:00') {
					$row[] = date('d-m-Y',strtotime($inbox['date_sent']));
				} else {
					$row[] = 'NA';
				}
			}else{
				$row[] = '';
			}
			#echo $inbox['date_approved'];
			if($inbox['date_approved']!='1970-01-01 00:00:00'){
				if(strtotime($inbox['date_approved']) != '0000:00:00') {
					$row[] = date('d-m-Y',strtotime($inbox['date_approved']));
				} else {
					$row[] = 'NA';
				}
			}else{
				$row[] = '';
			}
			$row[] = $common->moneyFormat($inbox['claimed']);
			$row[] = $common->moneyFormat($inbox['certified']);
			$row[] = $common->moneyFormat($inbox['invoiced']);
		}	
		if(!in_array($_SESSION['userRole'], $permArray)){
			if($_GET['reqfrom'] == 'ajax'){
				$action = "<input type=\"radio\" name=\"radioSelectMail\" onclick=\"selectedEmail(".$inbox['thread_id'].")\" alt=\"Select Email\" title=\"Select Email\">";
			}else{
				$action = '<a onClick="delMessage('.$inbox['thread_id'].');" href="#" ><img src="images/del.png" width="16" height="16" alt="Delete" title="Delete" /></a>';
			}
			$row[] = ($unread>0)?"<b>".$action."</b>":$action;
		} 
		$output['aaData'][] = $row;
		
//	</tr>
	}		
	echo json_encode( $output );
		
}

if(isset($_REQUEST["req4inform"])){
	$project_id = $_SESSION['idp'];
	$userNameIdArr = array();
	$unreadCountArr = array();

//Data Section Start Here
	$permArray = array('General Consultant', 'Architect', 'Structural Engineer', 'Services Engineer', 'Sub Contractor', 'Client');
	$userEmailData = $common->selQRYMultiple('DISTINCT u.user_id, u.user_name, u.user_fullname, u.user_email, up.project_id, up.project_name, u.user_type, u.user_fullname, u.recieve_email', 'user_projects AS up, user AS u', 'up.user_id = u.user_id AND up.project_id = '.$project_id.' AND up.is_deleted = 0 AND u.is_deleted = 0 AND is_pdf = 1 ORDER BY project_id');
	foreach($userEmailData as $uEmailData){
		$userNameIdArr[$uEmailData['user_id']] = $uEmailData['user_fullname'];
	}
 
	$unreadCountData = $common->selQRYMultiple('thread_id, count(*) AS num', 'pmb_user_message', 'user_id ='.$_SESSION['ww_builder_id'].' AND type = "inbox" AND inbox_read = 0 AND is_deleted = 0 AND project_id = '.$project_id.' GROUP BY thread_id');
	if(!empty($unreadCountData)){
		foreach($unreadCountData as $unCountData){
			$unreadCountArr[$unCountData['thread_id']] = $unCountData['num'];
		}
	}
	
	$threadCountData = $common->selQRYMultiple('thread_id, count(*) AS num', 'pmb_user_message', 'user_id ='.$_SESSION['ww_builder_id'].' AND type = "inbox" AND is_deleted = 0 AND project_id = '.$project_id.' GROUP BY message_id');
	if(!empty($threadCountData)){
		foreach($threadCountData as $thCountData){
			$threadCountArr[$thCountData['thread_id']] = $thCountData['num'];
		}
	}
//Data Section End Here
	$sWhere = "";
	
	$aColumns = array( 'um.user_id', 'um.message_id', 'um.from_id', 'um.thread_id', 'um.inbox_read', 'm.title', 'm.message', 'm.message_type', 'm.sent_time', 'm.cc_email_address', 'um.user_message_id', 'um.rfi_number', 'm.title AS rfi_description', 'm.rfi_status', 'GROUP_CONCAT(DISTINCT um.from_id) AS fromID');

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
			$sOrder = " ORDER BY rfi_number ASC";
		}
	}

	$spCon = "um.type = 'inbox' AND um.inbox_read = 0 AND ";
	
	if(isset($_GET['folderType']) && !empty($_GET['folderType'])){
#		$spCon = "m.message_type = '".$_GET['folderType']."' AND (um.type = 'inbox'  OR um.type = 'sent') AND ";
		$spCon = "m.message_type = '".$_GET['folderType']."' AND (um.type = 'inbox'  OR um.type = 'sent') AND ";
	}	
	
	if($_SESSION['ww_builder_id']==168){
	#	$spCon = "m.message_type = '".$_GET['folderType']."'";
	}
	if ( $_GET['sSearch'] != "" ){
		$sWhere = "WHERE um.is_deleted = 0 AND um.project_id = ". $project_id ." AND (";
		for ( $i=0 ; $i<(count($aColumns) -1); $i++ ){
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
	
//New RFI Status Condition Start Here
	if(isset($_GET['rfiStatus']) && !empty($_GET['rfiStatus']) && $_GET['rfiStatus'] != 'All'){
		$sWhere .= " AND m.rfi_status = '".$_GET['rfiStatus']."' ";
	}	
//New RFI Status Condition End Here
	if($_SESSION['ww_builder_id']!=168){
	$sWhere.= "AND (um.message_id = m.message_id AND
						".$spCon."
						m.is_draft = 0 AND
						um.user_id = '".$_SESSION['ww_builder_id']."')
					GROUP BY
						um.thread_id";
	}else{
	$sWhere.= "AND (um.message_id = m.message_id AND
						".$spCon."
						m.is_draft = 0)
					GROUP BY
						um.thread_id";	
	}					
	if(!isset($sOrder)){
		$sOrder = " ORDER BY CAST(rfi_number AS UNSIGNED) ASC";
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
#print_r($inbox);die;
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
		
		if($inbox['cc_email_address'] != ''){
			$inbox['cc_email_address'] = getUserNameByEmailids($inbox['cc_email_address']);
		}

		$unread = $unreadCountArr[$inbox['thread_id']];
		$count = $threadCountArr[$inbox['thread_id']];
		
//Data ploating start here
	//CheckBox
		$inputFrom = '<input name="from[]" type="checkbox" value="'.$inbox['thread_id'].'" />';
		$row[] = ($unread>0)?"<b>".$inputFrom."</b>":$inputFrom;//First Element

	//RFI number
		$row[] = $inbox['rfi_number'];
		
	//RFI Description
		$inboxData = htmlentities($inbox['title'], ENT_QUOTES, 'UTF-8'); 
		if(strlen($inboxData) > 80){	$inboxData = substr($inboxData, 0, 80).'...';	}
		if($count>1) {	$inboxData.= '('.$count.')';	}
		#$row[] = $inbox['correspondence_number'];
		$subject = '<a href="?sect=message_details&folderType='.$_GET['folderType'].'&id='.base64_encode($inbox['thread_id']).'&type=inbox" style="'.(($count>1)?'background:url(images/reply.png) 0 no-repeat;':'').' ">'.$inboxData.'</a>';
		$row[] = ($unread>0)?"<b>".stripslashes($subject)."</b>":$subject;//Fourth Element
		
		if(isset($_GET['folderType']) && empty($_GET['folderType'])){
			$messFrom = '<a href="?sect=message_details&folderType='.$_GET['folderType'].'&id='.base64_encode($inbox['thread_id']).'&type=inbox">'.htmlentities($user['user_name'], ENT_QUOTES, 'UTF-8').'</a>';
			$row[] = ($unread>0)?"<b>".$messFrom."</b>":$messFrom;//Second Element
		}else{
			if(strlen($multipleUser) > 50){
				$multipleUser = substr($multipleUser, 0, 50).'...';
			}
			
			$messTo = '<a href="?sect=message_details&folderType='.$_GET['folderType'].'&id='.base64_encode($inbox['thread_id']).'&type=inbox">'.htmlentities($multipleUser, ENT_QUOTES, 'UTF-8').'</a>';
			$row[] = ($unread>0)?"<b>".$messTo."</b>":$messTo;//Second Element
			
			$messCC = '<a href="?sect=message_details&folderType='.$_GET['folderType'].'&id='.base64_encode($inbox['thread_id']).'&type=inbox">'.htmlentities($inbox['cc_email_address'], ENT_QUOTES, 'UTF-8').'</a>';
			$row[] = ($unread>0)?"<b>".$messCC."</b>":$messCC;//Thired Element
		}	
		
	//Time
		/*if(isset($_GET['folderType']) && empty($_GET['folderType'])){
			$message_type = $inbox['message_type'];
			$row[] = ($unread>0)?"<b>".$message_type."</b>":$message_type;//Fifth Element
		}*/
			$sent_time = date('d/m/Y H:i:s' ,strtotime($inbox['sent_time']));
			$row[] = ($unread>0)?"<b>".$sent_time."</b>":$sent_time;//Sixth Element
	
	//Status
			$row[] = $inbox['rfi_status'];
	//Action
		if(!in_array($_SESSION['userRole'], $permArray)){
			$action = '<a onClick="delMessage('.$inbox['thread_id'].');" href="#" ><img src="images/del.png" width="16" height="16" alt="Delete" title="Delete" /></a>';
			$row[] = ($unread>0)?"<b>".$action."</b>":$action;//Seventh Element
		} 
	#	print_r($row);
		$output['aaData'][] = $row;
//	</tr>
	}		
	echo json_encode( $output );
		
}?>
