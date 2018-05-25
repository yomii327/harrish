<?php
session_start();
	/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2010 - Allan Jardine
	 * License:   GPL v2 or BSD (3-point)
	 */
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	include("./includes/functions.php");
	$obj = new DB_Class();

//	$aColumns = array('issue_to_name', 'company_name', 'issue_to_phone', 'issue_to_email', 'tag','issue_to_id');
	$aColumns = array('rule_name', 'status', 'doc_status', 'user_name', 'id');
		
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* DB table to use */
	$sTable = "user_role_workflow";
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	
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
		$sWhere = "WHERE is_deleted=0 AND project_id = '".$_SESSION['idp']."' AND (";
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
		$sWhere = "WHERE is_deleted=0  AND project_id = '".$_SESSION['idp']."' ";
	}
	$sWhere = $sWhere. "GROUP BY rule_name";
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
	
	while ( $aRow = mysql_fetch_array( $rResult ) ){
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			if ( $aColumns[$i] == "id" ){
				$issue_to_id = $aRow[ $aColumns[$i] ];
				$rowID = $aRow[ $aColumns[$i] ];
				$row[] = '&nbsp;&nbsp;<img id="viewRule" class="action" src="images/view.png" title="View Rule" onclick="viewRuleNew('.$rowID.');"> &nbsp;&nbsp;&nbsp; <img class="action" src="images/edit_right.png" id="editChecklist" title="Edit Checklist" onclick="editRuleData('.$rowID.');" />&nbsp;&nbsp;&nbsp; <img class="action" src="images/delete.png" id="editRevision" title="Delete Checkist"" onclick="deleteRuleData('.$rowID.')" />';
			}else if ( $aColumns[$i] == 'by_when' ){
				$dat = '';
				if(!empty($aRow[ $aColumns[$i] ]) && $aRow[ $aColumns[$i] ] > 0){
	            	$date = strtotime($aRow[ $aColumns[$i] ]);
	    			$dat = date('d-m-Y', $date);
	    		}
				$row[] = $dat;
			}else if ( $aColumns[$i] == 'user_name' ){
				$userStr = '';
				if(!empty($aRow[ $aColumns[$i] ])){
					$str = explode(",",$aRow[ $aColumns[$i] ]);
					//echo "<pre>"; print_r($str);
					if(count($str) > 0){
						foreach ($str as $user) {
							if(!empty($userStr)){
								$userStr .= '</br>' . $user;
							}else{
								$userStr = $user;
							}
						}
					}
	    		}
				$row[] = $userStr;
			}else if ( $aColumns[$i] == 'doc_status' ){
				$docStatusStr = '';
				if(!empty($aRow[ $aColumns[$i] ])){
					$str1 = explode(",",$aRow[ $aColumns[$i] ]);
					//echo "<pre>"; print_r($str);
					if(count($str1) > 0){
						foreach ($str1 as $docStatus) {
							if(!empty($docStatusStr)){
								$docStatusStr .= '</br>' . $docStatus;
							}else{
								$docStatusStr = $docStatus;
							}
						}
					}
	    		}
				$row[] = $docStatusStr;
			}else if ( $aColumns[$i] != ' ' ){
				/* General output */
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
?>