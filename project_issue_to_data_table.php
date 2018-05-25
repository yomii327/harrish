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
	$aColumns = array('issue_to_name', 'master_issue_id');
		
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "master_issue_id";
	
	/* DB table to use */
	$sTable = "inspection_issue_to";
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
	$sWhere = $sWhere. "GROUP BY issue_to_name";
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
			if ( $aColumns[$i] == "master_issue_id" ){
				$issue_to_id = $aRow[ $aColumns[$i] ];
				$rowID = $aRow[ $aColumns[$i] ];
				//$row[] = '<img class="action" src="images/users.png" id="users" title="Users" onclick="showIssueTo('.$rowID.');" /><!--&nbsp;&nbsp;<img class="action" src="images/add_name.png" id="addIssueToContact" title="Add New Issue To Contact" onclick="addNewIssueToContact('.$rowID.');" /--><br />';

				$row[] = '&nbsp;&nbsp;&nbsp;&nbsp;<a class="action green_small" id="users" title="Users" onclick="showIssueTo('.$rowID.');" style="font-size:12px; float:left; cursor:pointer;">Users</a><!--&nbsp;&nbsp;&nbsp;<img class="action" src="images/add_name.png" id="addIssueToContact" title="Add New Issue To Contact" onclick="addNewIssueToContact('.$rowID.');" /--><br />';
				
				/*$row[] = "<a  title='Click to edit' href='?sect=edit_issue_to&id=".base64_encode($issue_to_id)."'>
					<img src='images/edit.png' border='none'  width='20' height='20'/>
				</a>
				<a  title='Click to delete' href='#'  onclick=\"return deletechecked('Are you sure you want to delete this issued to?','?sect=c_issue_to&id=".base64_encode($issue_to_id)."');\">
					<img src='images/remove.png' border='none' width='20' height='20'/>
				</a>";*/
			}else if ( $aColumns[$i] != ' ' ){
				/* General output */
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
?>