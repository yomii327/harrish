<?php session_start();
	include("./includes/functions.php");
	$obj = new DB_Class();
	
	include_once("includes/commanfunction.php");
	$object = new COMMAN_Class(); 

	$aColumns = array('company_name', 'address', 'phone_number', 'primary_contact', 'id', 'subcontractor_database');
	
	/* aColumns count */
	$columnsCount = count($aColumns)-1;
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* DB table to use */
	$sTable = "organisations";

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
		$sWhere = "WHERE is_deleted=0 AND (";
		for ( $i=0 ; $i<$columnsCount ; $i++ ){
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<$columnsCount ; $i++ ){
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
		$sWhere = "WHERE is_deleted=0 AND company_name != '' ";
	}
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
	//$aColumns = array('company_name', 'address', 'phone_number', 'primary_contact', 'quality_rating', 'project_size', 'id');
	while ( $aRow = mysql_fetch_array( $rResult ) ){
		$row = array();
		for ( $i=0 ; $i<$columnsCount ; $i++ ){
			if ( $aColumns[$i] == "company_name" ){
				$companyName = $aRow['company_name'];
			} else 
			if ( $aColumns[$i] == "address" ){
				$address = $aRow['address'];
			} else 
			if ( $aColumns[$i] == "phone_number" ){
				$phoneNumber = $aRow['phone_number'];
			} else 
			if ( $aColumns[$i] == "primary_contact" ){
				$primaryContact = $aRow['primary_contact'];
			} else 
			if ( $aColumns[$i] == "id" ){
				$addrID = $aRow['id'];
			}
		}
		$actionBtn = '<img class="action" src="images/edit_right.png" id="editRecord" title="edit record" onclick="addEditRecord('.$addrID.');" />&nbsp;<img class="action" src="images/delete.png"  id="deleteRecord" title="delete record" onclick="deleteUser('.$addrID.');" />&nbsp;<img class="action" src="images/setting.png" id="" title="update setting" onclick="editThemeSetting('.$addrID.');" />';
		if($aRow['subcontractor_database'] == 'Yes'){
			$actionBtn .= '&nbsp;<a href="?sect=subcontractor_database&cId='.$addrID.'"><img class="action" src="images/database.png" title="Subcontractor/Supplier Database" width="16" height="16" /></a>';
		}
		$row[] = $companyName;
		$row[] = $address;
		$row[] = $phoneNumber;
		$row[] = $primaryContact;
		$row[] = $actionBtn;
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
?>
