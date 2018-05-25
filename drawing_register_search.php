<?php session_start();
$builder_id=$_SESSION['ww_builder_id'];
 
include("./includes/functions.php");
$obj = new DB_Class();
include('./includes/commanfunction.php');
$common = new COMMAN_Class();

	
if(isset($_REQUEST["name"])){
	$sWhere = "";
		
	$project_id = $_SESSION['idp'];
	$aColumns = array( 'title', 'number', 'revision', 'comments', 'attribute1', 'attribute2', 'tag', 'id');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* DB table to use */
	$sTable = "drawing_register";

	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
		$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
			mysql_real_escape_string( $_GET['iDisplayLength'] );
	}

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
	
	if ( $_GET['sSearch'] != "" ){
		$sWhere = "WHERE is_deleted=0 AND project_id=". $project_id ." AND (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ){
			if ( $sWhere == "" ){
				$sWhere = "WHERE is_deleted = 0 AND pr.project_id = ".$project_id." AND ";
			}else{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	if ($sWhere == ""){
		$sWhere = "WHERE is_deleted = 0 AND project_id = ".$project_id." ";
	}

	if(isset($_POST["attr1"]) && !empty($_POST["attr1"]))
		$sWhere .= " AND attribute1 = '".$_POST["attr1"]."'";
	if(isset($_POST["attr2"]) && !empty($_POST["attr2"]))
		$sWhere .= " AND attribute2 = \"".$_POST["attr2"]."\"";
	if(isset($_POST["searchKey"]) && !empty($_POST["searchKey"]))
		$sWhere .= " AND ((title LIKE '%".$_POST["searchKey"]."%') OR (number LIKE '%".$_POST["searchKey"]."%') OR (revision LIKE '%".$_POST["searchKey"]."%') OR (comments LIKE '%".$_POST["searchKey"]."%'))";

	
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit";
	
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
	
	while ( $aRow = mysql_fetch_array( $rResult ) ){
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			if ( $aColumns[$i] == "title" ){
				$title = $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] == "number" ){
				$number = $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] == "revision" ){
				$revision = $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] == "comments" ){
				$comments = $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] == "attribute1" ){
				$attribute1 = $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] == "attribute2" ){
				$attribute2 = $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] == "tag" ){
				$tag = $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] == 'id' ){
				$rowID = $aRow[ $aColumns[$i] ];
				$action = '<img class="action" src="images/add.png" id="viewRevision" title="view revisions" onclick="showRevisions('.$rowID.');" />&nbsp;<img class="action" src="images/view.png" id="addRevision" title="add new revision" onclick="addNewRegisterRevision('.$rowID.');" /><br />
				<img class="action" src="images/edit_right.png" id="editRevision" title="edit register"  onclick="editDrawingRegister('.$rowID.');" />&nbsp;<img class="action" src="images/delete.png"  id="editRevision" title="delete register" onclick="removeImages('.$rowID.')" />';
			}
		}
		$row[] = $title;
		$row[] = $number;
		$row[] = $revision;
		$row[] = $comments;
		$row[] = $attribute1;
		$row[] = $attribute2;
		$row[] = $tag;
		$row[] = $action;
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
		
}?>