<?php session_start();
	include("./includes/functions.php");
	$obj = new DB_Class();
	
	include_once("includes/commanfunction.php");
	$object = new COMMAN_Class(); 

	#Start:- Get insurance date
	$insDateArr = array();
	$checkListStatus = $object->selQRYMultiple("status_id, item_id, form_id, amount, expiry_date", "organisations_subcontractor_ques_with_cate_checklist_status", " is_deleted = 0 AND form_name = 'subcontractor_questions_with_category' AND item_id IN(14, 15, 16, 17, 18) AND company_id IN(".$_SESSION['companyId'].") ");	
	if($checkListStatus != false){
		foreach($checkListStatus as $key => $chData){
			$insDateArr[$chData['form_id']][$chData['item_id']] = $chData['expiry_date'];
		}
	}
	#End:- Get insurance date


	$aColumns = array('trade', 'strategic_agreement', 'compliance', 'company_name', 'contact_name', 'contact_position', 'phone', 'email_address', 'id');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* DB table to use */
	$sTable = "organisations_subcontractor_database";

	/* 
	 * Paging
	 */
	$sLimit = "";
    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
    {
      $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
      intval( $_GET['iDisplayLength'] );
    }
	//echo $_GET['iDisplayLength'];die;
	
	
	/*
	 * Ordering
	 */
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
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhere = "";
	if ( $_GET['sSearch'] != "" ){
		$sWhere = "WHERE is_deleted=0 AND (";
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
				$sWhere = "WHERE is_deleted=0 AND ";
			}else{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	if ($sWhere == ""){
		$sWhere = "WHERE is_deleted=0";
	}
	

	if(isset($_SESSION['companyId']) && !empty($_SESSION['companyId']))
		$sWhere .= " AND company_id IN (".$_SESSION['companyId'].") ";
		
	if(isset($_GET["trade"]) && !empty($_GET["trade"]))
		$sWhere .= " AND trade LIKE \"%".$_GET["trade"]."%\"";
		
	if(isset($_GET["compliance"]) && !empty($_GET["compliance"])){
		$compliance = str_replace(",", "', '", $_GET["compliance"]);
		$sWhere .= " AND compliance IN('".$compliance."')";
	}
	
	if(isset($_GET["searchKeyword"]) && !empty($_GET["searchKeyword"]))
		$sWhere .= " AND ((contact_name LIKE \"%".$_GET["searchKeyword"]."%\") OR (contact_position LIKE \"%".$_GET["searchKeyword"]."%\") OR (contact_type LIKE \"%".$_GET["searchKeyword"]."%\") OR (quotes LIKE \"%".$_GET["searchKeyword"]."%\"))";

	if(isset($_GET["company_name"]) && !empty($_GET["company_name"]))
		$sWhere .= " AND ((company_name LIKE \"%".$_GET["company_name"]."%\"))";
		
	$sWhere = $sWhere. "";
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit";

	$rResult = $obj->db_query($sQuery ) or die(mysql_error());

	/* Data set length after filtering */
	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "SELECT COUNT(".$sIndexColumn.") FROM $sTable $sWhere";
	$rResultTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);

	mysql_data_seek ($rResult, 0);
		
	while ( $aRow = mysql_fetch_array( $rResult ) ){
		$row = array();
		
		$id = ($aRow['id']);
		
	if($_SESSION['companyId'] == 3){
		if(isset($insDateArr[$id])){
			$curtDate = date('Y-m-d');
			$publicInsuDate = isset($insDateArr[$id]['14'])?$insDateArr[$id]['14']:'';
			$plantInsuDate = isset($insDateArr[$id]['15'])?$insDateArr[$id]['15']:'';
			$motorInsuDate = isset($insDateArr[$id]['16'])?$insDateArr[$id]['16']:'';
			$workInsuDate = isset($insDateArr[$id]['17'])?$insDateArr[$id]['17']:'';
			$otherInsuDate = isset($insDateArr[$id]['18'])?$insDateArr[$id]['18']:'';

			if(!empty($publicInsuDate) && $publicInsuDate != '0000-00-00'){
				$date1 = new DateTime($curtDate);
				$date2 = new DateTime($publicInsuDate);
				$dateDiff = $date1->diff($date2);
				$publicInsuDate = ($dateDiff->days >90)?1:'0';
			}else{
				$publicInsuDate = '0';
			}
			
			if(!empty($plantInsuDate) && $plantInsuDate != '0000-00-00'){
				$date1 = new DateTime($curtDate);
				$date2 = new DateTime($plantInsuDate);
				$dateDiff = $date1->diff($date2);
				$plantInsuDate = ($dateDiff->days >90)?1:'0';
			}else{
				$plantInsuDate = '0';
			}
			
			if(!empty($motorInsuDate) && $motorInsuDate != '0000-00-00'){
				$date1 = new DateTime($curtDate);
				$date2 = new DateTime($motorInsuDate);
				$dateDiff = $date1->diff($date2);
				$motorInsuDate = ($dateDiff->days >90)?1:'0';
			}else{
				$motorInsuDate = '0';
			}
			
			if(!empty($workInsuDate) && $workInsuDate != '0000-00-00'){
				$date1 = new DateTime($curtDate);
				$date2 = new DateTime($workInsuDate);
				$dateDiff = $date1->diff($date2);
				$workInsuDate = ($dateDiff->days >90)?1:'0';
			}else{
				$workInsuDate = '0';
			}
			
			if(!empty($otherInsuDate) && $otherInsuDate != '0000-00-00'){
				$date1 = new DateTime($curtDate);
				$date2 = new DateTime($otherInsuDate);
				$dateDiff = $date1->diff($date2);
				$otherInsuDate = ($dateDiff->days >90)?1:'0';
			}else{
				$otherInsuDate = '0';
			}
		}

		$approved = '<div id="circle" style="background:red;"></div>';
		$ins = $publicInsuDate.'_'.$plantInsuDate.'_'.$motorInsuDate.'_'.$workInsuDate;
		if($aRow['compliance'] == 'Yes' && !empty($publicInsuDate) && !empty($plantInsuDate) && !empty($motorInsuDate) && !empty($workInsuDate) && !empty($otherInsuDate)){
			$approved = '<div id="circle" style="background:green;"></div>';
			
		}else if($aRow['compliance'] == 'Yes'){
			$approved = '<div id="circle" style="background:yellow;"></div>';
		}
	}else{
		$approved = $aRow['compliance'];
	}
		$row[] = str_replace("###", ", ", $aRow['trade']);
		$row[] = $aRow['strategic_agreement'];
		$row[] = $approved;
		$row[] = $aRow['company_name'];
		$row[] = $aRow['contact_name'];
		//$row[] = $aRow['contact_position'];
		$row[] = $aRow['phone'];
		$row[] = $aRow['email_address'];		
		//if(isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company'] == 1){
		if((isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company'] == 1) || (isset($_SESSION['ww_builder']['user_type']) && $_SESSION['ww_builder']['user_type'] == 'manager')){
			$row[] = '<a href="javascript:void(0);" onclick="addEditRecord('.$id.');"><img class="action" src="images/edit_right.png" id="editRecord" title="edit record"  /></a>&nbsp;<img class="action" src="images/delete.png"  id="deleteRecord" title="delete record" onclick="deleteSubcontractor('.$id.');" />&nbsp;';
		} else { 
			$row[] = '<a href="pms.php?sect=view_subcontractor&id='.$id.'"><img class="action" src="images/view.png" id="viewRecord" title="View record"  /></a>';
		}
	
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
?>