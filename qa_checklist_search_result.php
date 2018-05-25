<?php session_start();
require_once'includes/functions.php';
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$owner_id = $_SESSION['ww_builder_id'];

if(isset($_REQUEST['name'])){
	$_SESSION['qa'] = $_REQUEST;//Set Session for back implement and Remeber

	$projID = '';
	$where = '';

	$_SESSION['qaChecklistProId'] = '';
	$_SESSION['qaChecklistId'] = '';
	$_SESSION['qaChecklistLocationId'] = '';
	$_SESSION['qaChecklistSubLocationId'] = '';
	$_SESSION['qaChecklistSubLocationId1'] = '';
	$_SESSION['qaChecklistSubLocationId2'] = '';
	
	$locArray = array();
	if(!empty($_REQUEST['projId'])){
		$projID = $_REQUEST['projId'];
		$_SESSION['qaChecklistProId'] = $projID;
		$projectName = $_REQUEST['projectNameQA'];
		$where .= ' AND project_id = '.$projID;
	}
	
	if(!empty($_REQUEST['checklist'])){
		$checklist = $_REQUEST['checklist'];
		$_SESSION['qaChecklistId'] = $checklist;
		$where .= ' AND project_checklist_id = "'.$checklist.'"';
	}

	if(!empty($_REQUEST['location'])){
		$locArray[] = $_REQUEST['location'];
		$location = $_REQUEST['location'];
		$_SESSION['qaChecklistLocationId'] = $location;
		$where .= ' AND location_id = "'.$location.'"';
	}

	if(!empty($_REQUEST['subLocation'])){
		$locArray[] = $_REQUEST['subLocation'];
		$subLocation1 = $_REQUEST['subLocation'];
		$_SESSION['qaChecklistSubLocationId'] = $subLocation1;
		$where .= ' AND sub_location_id = "'.$subLocation1.'"';
	}
	
	if(!empty($_REQUEST['sub_subLocation'])){
		$locArray[] = $_REQUEST['sub_subLocation'];
		$subLocation2 = $_REQUEST['sub_subLocation'];
		$_SESSION['qaChecklistSubLocationId1'] = $subLocation2;
		$where .= ' AND sub_location1_id = "'.$subLocation2.'"';
	}
	
	if(!empty($_REQUEST['subSubLocation3'])){
		$locArray[] = $_REQUEST['subSubLocation3'];
		$subLocation3 = $_REQUEST['subSubLocation3'];
		$_SESSION['qaChecklistSubLocationId2'] = $subLocation3;
		$where .= ' AND sub_location2_id = "'.$subLocation3.'"';
	}

	if(!empty($_REQUEST['status'])){
		$where .= " AND status = '".$_REQUEST['status']."'";
	}

	if(!empty($_REQUEST['searchKeyword'])){
		$where .= ' AND location_tree LIKE "%'.$_REQUEST['searchKeyword'].'%" ';
	}
	
	$aColumns = array('qa_checklist_id', 'created_date', 'location_tree', 'status', 'project_id');
	$sIndexColumn = "project_id";
	$sTable = "qa_checklist";
	$sLimit = "";
	
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	if ( isset( $_GET['iSortCol_0'] ) ){
		$sOrder = "ORDER BY  ";
		// for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ){
		// 	if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ){
		// 		$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
		// 		 	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
		// 	}
		// }

		$sOrder = substr_replace( $sOrder, "", -2 );

		if ( $sOrder == "ORDER BY" ){
			$sOrder = "ORDER BY created_date DESC";
		}
	}
	
	$sWhere = "";
	if ( $_GET['sSearch'] != "" ){
		$sWhere = "WHERE is_deleted=0 AND project_id=".$projID." AND (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ){
			if ( $sWhere == "" ){
				$sWhere = "WHERE is_deleted = 0 AND project_id = ".$projID." AND ";
			}else{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	if ($sWhere == ""){
		$sWhere = " WHERE is_deleted = 0 ".$where;
	}
	
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit"; //echo $sQuery; die();
		
	$rResult = $obj->db_query($sQuery ) or die(mysql_error());

	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	$sQuery = "SELECT COUNT(".$sIndexColumn.") FROM $sTable $sWhere";
	$rResultTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultFilterTotal[0];
	
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	while ( $aRow = mysql_fetch_array( $rResult ) ){
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			if ( $aColumns[$i] == "qa_checklist_id" ){
				$qaID = $aRow[ $aColumns[$i] ];
			} else if ( $aColumns[$i] == "created_date" ){
				$date = date('d/m/Y', strtotime($aRow[ $aColumns[$i] ]));
			} else if ( $aColumns[$i] == "location_tree" ){
				$locationTree = $aRow[ $aColumns[$i] ];
			} else if ( $aColumns[$i] == "status" ){
				$status = $aRow[ $aColumns[$i] ];
			} else if ( $aColumns[$i] == 'project_id' ){
				//$rowID = $aRow[ $aColumns[$i] ];
				$rowID = $qaID;
				$action = '';
				$action = '<img class="action" src="images/view.png" id="viewRevision" title="view checklist" onclick="viewThis('.$rowID.', '.$projID.');" />&nbsp;&nbsp;&nbsp;';
				$action .= '<a href="?sect=edit_qc_task&selectedChecklistId='.$rowID.'" ><img class="action" src="images/edit_right.png" id="editRevision" title="edit checklist" /></a>&nbsp;&nbsp;&nbsp;';
				$action .= '<img class="action" src="images/delete.png"  id="editRevision" title="delete checklist" onclick="deleteThis('.$rowID.', '.$projID.')" />';				
			}
		}		
		//$row[] = '<input type="checkbox" class="approveDrawingReg" name="drawingID[]" id="drawingID" value="'.$qaID.'">';	
		$row[] = $date;
		$row[] = $locationTree;
		$row[] = $status;
		$row[] = $action;
		$output['aaData'][] = $row;
	}
	//echo "===>>>"; print_r($output); die();

	echo json_encode( $output );
}?>