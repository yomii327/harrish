<?php session_start();
$builder_id=$_SESSION['ww_builder_id'];
 
include("./includes/functions.php");
$obj = new DB_Class();
include('./includes/commanfunction.php');
$common = new COMMAN_Class();

//Fetch Drawing and its previous markups data Start here
if(isset($_REQUEST["pdfID"])){
	$project_id = $_SESSION['idp'];
//Data Section End Here
	$sWhere = "";
	$aColumns = array( 'markup_id', 'title', 'created_date', 'img_name', 'drawing_register_id');
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "markup_id";
	
	/* DB table to use */
	$sTable = "drawing_register_markups";
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
			$sOrder = " ORDER BY created_date DESC";
		}
	}

	$spCon = "";
	
	if ( $_GET['sSearch'] != "" ){
		$sWhere = "WHERE is_deleted = 0 AND project_id = ". $project_id ." AND (";
		for ( $i=0 ; $i<(count($aColumns)-1) ; $i++ ){
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	for ( $i=0 ; $i<count($aColumns) ; $i++ ){
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ){
			if ( $sWhere == "" ){
				$sWhere = "WHERE is_deleted = 0 AND project_id = ".$project_id." AND ";
			}else{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
		}
	}
	
	if ($sWhere == ""){
		//$sWhere = "WHERE is_deleted = 0 AND project_id = ".$project_id." AND drawing_register_id = ".$_GET['pdfID']." ";
		$sWhere = "WHERE is_deleted = 0 AND project_id = ".$project_id."";
	}else{
		//$sWhere = " AND drawing_register_id = ".$_GET['pdfID']." ";
	}
	
	if(!isset($sOrder)){
		$sOrder = " ORDER BY created_date DESC";
	}
	
	$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere 
		$sOrder
		$sLimit";
	//echo $sQuery ;die;
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
	while($markup = mysql_fetch_array($rResult)) {
		$row =  array();
		$row[] = date('d/m/Y', strtotime($markup['created_date']));
		$row[] = $markup['title'];
		//$row[] = $markup['markup_id'];
		$row[] = '<img src="images/view.png" title="view" onclick="showMarkupImage(\''.base64_encode($markup['img_name']).'\', '.$_GET['pdfID'].', '.$_GET['projectID'].');" />';
		$output['aaData'][] = $row;
	}		
	echo json_encode( $output );
		
}
//Function Part End Here
?>