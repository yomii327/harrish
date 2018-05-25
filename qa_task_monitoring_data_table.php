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
	include('./includes/commanfunction.php');
	$common = new COMMAN_Class();

	$project_id = $_SESSION['idp'];
	$aColumns = array( 'task', 'location_title', 'sub_location_id', 'status', 'comments', 'task_id');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "task_id";
	
	/* DB table to use */
	$sTable = "qa_task_monitoring pr LEFT JOIN qa_task_locations l ON l.location_id= pr.location_id";
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
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
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE pr.is_deleted=0 and pr.project_id=". $project_id ." and (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE pr.is_deleted=0  and pr.project_id=". $project_id ." and ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	if ($sWhere == "")
	{
		$sWhere = "WHERE pr.is_deleted=0  and pr.project_id=". $project_id . " ";
	}
	/*
	 * SQL queries
	 * Get data to display
	 */
	
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
	
	$rResult = $obj->db_query($sQuery ) or die(mysql_error());

	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable $sWhere
	";
	$rResultTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultFilterTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( $aColumns[$i] == "location_title" )
			{
				$location_title = $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] == "task" )
			{
				$task = $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] == "status" )
			{
				$status = $aRow[ $aColumns[$i] ];
			}
			if ( $aColumns[$i] == "comments" )
			{
				$comments = $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] == "sub_location_id" )
			{
				$sublocation = $common->QAsubLocationsProgressMonitoring($aRow[ $aColumns[$i] ], ' > ');
			}
			else if ( $aColumns[$i] == 'task_id' ){
				$action = "<a  title='Click to edit' href='?sect=edit_qa_task&id=".base64_encode($aRow[ $aColumns[$i] ])."'><img src='images/edit.png' border='none'  width='20' height='20'/></a><a  title='Click to delete' href='#'  onclick=\"return deletechecked('Are you sure you want to delete this quality assurance task?','?sect=qa_task_monitoring&id=".base64_encode($aRow[ $aColumns[$i] ])."');\"><img src='images/remove.png' border='none'  width='20' height='20'/></a>";
			}
		}
		$row[] = $task;
		$row[] = $location_title;
		$row[] = $sublocation;
		$row[] = $status;
		$row[] = $comments;
		$row[] = $action;
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
?>
