<?php
	session_start();
	include("./includes/functions.php");
	$obj = new DB_Class();
	include('./includes/commanfunction.php');
	$common = new COMMAN_Class();
	$project_id = $_SESSION['idp'];
	 // Start :- maped user section 
	 	$projectUsers = $common->selQRYMultiple('u.user_id, up.map_user_id, up.map_with', 'user as u Left Join user_projects as up on u.user_id = up.user_id  and up.is_deleted=0', 'u.user_id!="'.$_SESSION['ww_builder_id'].'" AND u.is_deleted=0 AND up.project_id="'.$_SESSION['idp'].'" order by u.user_name');
	 	$mapedAddressBook = array(); 	$mapedIssuedTo = array(); 
		if(isset($projectUsers) && !empty($projectUsers)){
			foreach($projectUsers as $puser){	
				if($puser['map_user_id']>0 && $puser['map_with']=="addressbook"){
					$mapedAddressBook[] = $puser['map_user_id'];	
				}
				if($puser['map_user_id']>0 && $puser['map_with']=="issuedto"){
					$mapedIssuedTo[] = $puser['map_user_id'];
				}
			}
		}
	 // End :- maped user section
	//Count issued to & add book users
	$totalIssuedTo = $common->selQRYMultiple('count(issue_to_id) as count', 'inspection_issue_to', 'is_deleted=0 AND project_id="'.$_SESSION['idp'].'" AND issue_to_name!="NA" AND issue_to_email!="" order by issue_to_name');
				
	$totalAddresBookUsers =  $common->selQRYMultiple('count(id) as count', 'pmb_address_book', 'is_deleted=0 AND project_id="'.$_SESSION['idp'].'" AND full_name != "" order by full_name');
	$totalUser = 0;
	if((isset($totalAddresBookUsers[0]['count']) && $totalAddresBookUsers[0]['count']>0) || (isset($totalIssuedTo[0]['count']) && $totalIssuedTo[0]['count']>0)){
		$totalUser = 1;
	}

	$aColumns = array( 'name', 'company_name', 'user_phone', 'user_email', 'id', 'type', 'activity', 'physical_address');
	
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	$sWhere = "";
	if(!empty($_REQUEST['companySearch'])){
		$sWhere .= " AND ab.company_name = '".$_REQUEST['companySearch']."'";
		$sWhere1 .= " AND iss.company_name = '".$_REQUEST['companySearch']."'";
		$sWhere2 .= " AND u.company_name = '".$_REQUEST['companySearch']."'";
	}
	if(!empty($_REQUEST['nameSearch'])){
		$sWhere .= " AND ab.full_name = '".$_REQUEST['nameSearch']."'";
		$sWhere1 .= " AND iss.issue_to_name = '".$_REQUEST['nameSearch']."'";
		$sWhere2 .= " AND u.user_fullname = '".$_REQUEST['nameSearch']."'";
	}
	if(!empty($_REQUEST['searchKeyword'])){
		$sWhere .= " AND ab.activity LIKE '%".$_REQUEST['searchKeyword']."%'";
		$sWhere1 .= " AND iss.activity LIKE '%".$_REQUEST['searchKeyword']."%'";
		$sWhere2 .= " AND u.activity LIKE '%".$_REQUEST['searchKeyword']."%'";
	}
	
	 $sQuery = "SELECT
						ab.full_name as name,
						ab.company_name as company,
						ab.user_phone as phone,
						ab.user_email as email,
						ab.activity as activity,
						IF(ab.id>0, 'adHoc', '') AS type,
						ab.physical_address,
						ab.id
					FROM
						pmb_address_book as ab
					where
						ab.project_id = ".$_SESSION['idp']." AND is_deleted = 0 ".$sWhere." AND ab.id NOT IN('".join("','", $mapedAddressBook)."')
			UNION
					SELECT
						iss.company_name as name,
						iss.issue_to_name as company,
						iss.issue_to_phone as phone,
						iss.issue_to_email as email,
						iss.activity as activity,
						IF(iss.issue_to_id > 0, 'issuedTo', '') AS type,
						iss.physical_address,
						iss.issue_to_id as id
					FROM
						inspection_issue_to as iss
					WHERE
						iss.project_id = ".$_SESSION['idp']." AND is_deleted = 0 ".$sWhere1." AND iss.issue_to_id NOT IN('".join("','", $mapedIssuedTo)."') AND iss.issue_to_name!='NA' AND iss.issue_to_email!=''
			UNION
					SELECT DISTINCT
						u.user_fullname as name,
						u.company_name as company,
						u.user_phone_no as phone,
						u.user_email as email,
						u.activity as activity,
						IF(u.user_id > 0, 'projectUser', '') AS type,
						u.physical_address,
						u.user_id as id
					FROM
						user_projects AS up, user AS u
					WHERE
						up.user_id = u.user_id AND up.project_id = ".$_SESSION['idp']." AND
						up.is_deleted = 0 AND u.is_deleted = 0 ".$sWhere2." AND u.user_id!='".$_SESSION['ww_builder_id']."'";
		$sCountQuery = $sQuery;
		$sQuery .= $sLimit;
	if(!empty($_REQUEST['sourceSearch'])){
		if($_REQUEST['sourceSearch'] == 'adHoc'){
			$sQuery = "SELECT
					ab.full_name as name,
					ab.company_name as company,
					ab.user_phone as phone,
					ab.user_email as email,
					IF(ab.id>0, 'adHoc', '') AS type,
					ab.physical_address,
					ab.id
				FROM
					pmb_address_book as ab
				where
					ab.project_id = ".$_SESSION['idp']." AND is_deleted = 0 ".$sWhere." AND ab.id NOT IN('".join("','", $mapedAddressBook)."')
				$sLimit";
		}
		if($_REQUEST['sourceSearch'] == 'issuedTo'){
			$sQuery = "SELECT
						iss.company_name as name,
						iss.issue_to_name as company,
						iss.issue_to_phone as phone,
						iss.issue_to_email as email,
						IF(iss.issue_to_id > 0, 'issuedTo', '') AS type,
						iss.physical_address,
						iss.issue_to_id as id
					FROM
						inspection_issue_to as iss
					WHERE
						iss.project_id = ".$_SESSION['idp']." AND is_deleted = 0 ".$sWhere1." AND iss.issue_to_id NOT IN('".join("','", $mapedIssuedTo)."') AND iss.issue_to_name!='NA' AND iss.issue_to_email!=''
				$sLimit";
		}
		if($_REQUEST['sourceSearch'] == 'projectUser'){
			$sQuery = "SELECT DISTINCT
						u.user_fullname as name,
						u.company_name as company,
						u.user_phone_no as phone,
						u.user_email as email,
						IF(u.user_id > 0, 'projectUser', '') AS type,
						u.physical_address,
						u.user_id as id
					FROM
						user_projects AS up, user AS u
					WHERE
						up.user_id = u.user_id AND up.project_id = ".$_SESSION['idp']." AND
						up.is_deleted = 0 AND u.is_deleted = 0 ".$sWhere2." AND u.user_id!='".$_SESSION['ww_builder_id']."'
				$sLimit";
		}
	}
	#echo $sQuery;die;
	$rResult = $obj->db_query($sQuery ) or die(mysql_error());
	
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];

	//$sQuery = "SELECT COUNT(".$sIndexColumn.")
	//	FROM   $sTable $sWhere";
	$rResultTotal = $obj->db_query( $sCountQuery) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultFilterTotal[0];
/**/

	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
#	echo mysql_num_rows( $rResult ) ;die;
	while ( $aRow = mysql_fetch_array( $rResult ) ){
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			if ( $aColumns[$i] == "name" ){
				$name = $aRow["name"];
			}else if ( $aColumns[$i] == "company_name" ){
				$company = $aRow["company"];
			}else if ( $aColumns[$i] == "user_phone" ){
				$phone = $aRow["phone"];
			}else if ( $aColumns[$i] == "user_email" ){
				$email = $aRow["email"];
			}else if ( $aColumns[$i] == "id" ){
				$rowID = $aRow["id"];
			}else if ( $aColumns[$i] == "activity" ){
				$activity = $aRow["activity"];
			}else if ( $aColumns[$i] == "physical_address" ){
				$physicalAddress = $aRow["physical_address"];
			}else if ( $aColumns[$i] == "type" ){
				if($aRow["type"] == "projectUser"){
					$Type = "Project&nbsp;User";
					$action = '<img class="action" src="images/edit_right.png" id="editRevision" title="edit user"  onclick="editAddressUser('.$rowID.', \'projectUser\');" /> <img class="action" src="images/map_icon.png" id="map" title="Map It"  onclick="mapAssociateUser('.$rowID.', \'projectUser\', '.$totalUser.');" />';
				}elseif($aRow["type"] == "issuedTo"){
					$Type = "Issued&nbsp;To";
					$action = '<img class="action" src="images/edit_right.png" id="editRevision" title="edit user"  onclick="editAddressUser('.$rowID.', \'issuedTo\');" />';
				}elseif($aRow["type"] == "adHoc"){
					$Type = "Ad hoc (External)";
					$action = '<img class="action" src="images/edit_right.png" id="editRevision" title="edit user"  onclick="editAddressUser('.$rowID.', \'adHoc\');" />&nbsp;<img class="action" src="images/delete.png"  id="delRevision" title="delete user" onclick="deleteAddressUser('.$rowID.')" />';
				}
			}
		}
#		echo $name.'==='.$company.'==='.$phone.'==='.$email.'==='.$rowID.'==='.$action.'<br />';
		$row[] = $name;
		$row[] = $company;
		$row[] = $phone;
		$row[] = $email;
		$row[] = $activity;
		$row[] = $physicalAddress;
		$row[] = $Type;
		$row[] = $action;
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
?>