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
	$aColumns = array( 'task', 'location_title', 'sub_location_id', 'start_date', 'end_date', 'progress_id');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "progress_id";
	
	/* DB table to use */
	$sTable = "progress_monitoring pr LEFT JOIN project_monitoring_locations l ON l.location_id= pr.location_id";
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
		$sOrder = "ORDER BY ";
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
	
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM $sTable $sWhere $sOrder $sLimit";
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
			else if ( $aColumns[$i] == "start_date" )
			{
#				date('d/m/Y', strtotime($aRow[ $aColumns[$i] ]));
				$sdate=explode('-',$aRow[ $aColumns[$i] ]);
				$sdate=$sdate[2].'/'.$sdate[1].'/'.$sdate[0];
			}
			if ( $aColumns[$i] == "end_date" )
			{
#				date('d/m/Y', strtotime($aRow[ $aColumns[$i] ]));
				$edate=explode('-',$aRow[ $aColumns[$i] ]);
				$edate=$edate[2].'/'.$edate[1].'/'.$edate[0];
			}
			else if ( $aColumns[$i] == "sub_location_id" )
			{
				$sublocation = $common->subLocationsProgressMonitoring_update($aRow[ $aColumns[$i] ], ' > ');
			}
			else if ( $aColumns[$i] == 'progress_id' ){
				$sql_issue="SELECT issued_to_name FROM issued_to_for_progress_monitoring where (progress_id=".$aRow[ $aColumns[$i] ]." and project_id=".$project_id." and is_deleted=0)";
				$issued= $obj->db_query($sql_issue);
				$issued_names='';
				while($issued_name=mysql_fetch_array($issued))
				{
					$issued_names.=$issued_name['issued_to_name'].', ';
				}
				if ($issued_names != "")
					$issued_names=substr($issued_names,0,(count($issued_names)-2));
				
				$action = "<a  title='Click to edit' href='?sect=edit_progress_task&id=".base64_encode($aRow[ $aColumns[$i] ])."'><img src='images/edit.png' border='none'  width='20' height='20'/></a><a  title='Click to delete' href='#'  onclick=\"return deletechecked('Are you sure you want to delete  this progress task?','?sect=progress_monitoring&id=".base64_encode($aRow[ $aColumns[$i] ])."');\"><img src='images/remove.png' border='none'  width='20' height='20'/></a>";
			}
		}
		$row[] = $task;
		$row[] = $location_title;
		$row[] = $sublocation;
		$row[] = $issued_names;
		$row[] = $sdate;
		$row[] = $edate;
		$row[] = $action;
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
?>
