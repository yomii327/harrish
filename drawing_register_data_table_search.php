<?php
	session_start();
	
	include("./includes/functions.php");
	$obj = new DB_Class();
	include('./includes/commanfunction.php');
	$common = new COMMAN_Class();

$permArray = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Architect');
	
	$project_id = $_SESSION['idp'];
	$aColumns = array('id', 'number', 'title', 'revision', 'comments', 'attribute1', 'attribute2', 'tag', 'status', 'is_approved', 'pdf_name', 'created_by', 'created_date', 'is_document_transmittal', 'is_approved_edit');
	
	$sIndexColumn = "id";
	
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
			$sOrder = "ORDER BY number";
		}
	}
	
	$sWhere = "";
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
		$sWhere = "WHERE is_deleted = 0 AND is_document_transmittal = 0 AND project_id = ".$project_id." ";
	}
	
	if(isset($_GET["attr1"]) && !empty($_GET["attr1"]))
		$sWhere .= " AND attribute1 = \"".$_GET["attr1"]."\"";
	if(isset($_GET["attr2"]) && !empty($_GET["attr2"]))
		$sWhere .= " AND attribute2 LIKE \"%".$_GET["attr2"]."%\"";
	if(isset($_GET["searchKey"]) && !empty($_GET["searchKey"]))
		$sWhere .= " AND ((title LIKE \"%".$_GET["searchKey"]."%\") OR (number LIKE \"%".$_GET["searchKey"]."%\") OR (revision LIKE \"%".$_GET["searchKey"]."%\") OR (comments LIKE \"%".$_GET["searchKey"]."%\"))";
		
	if(isset($_GET["onlyApproved"]) && !empty($_GET["onlyApproved"]))
		$sWhere .= " AND is_approved = 1";
		
	if(isset($_GET["attrTab"]) && !empty($_GET["attrTab"]))
		$sWhere .= " AND attribute1 = '".$_GET["attrTab"]."'";

	if(isset($_GET["secAttrTab"]) && !empty($_GET["secAttrTab"]))
		$sWhere .= " AND attribute2 LIKE \"%".$_GET["secAttrTab"]."%\"";

	if(isset($_GET["pdfStatus"]) && !empty($_GET["pdfStatus"]))
		$sWhere .= " AND status = \"".$_GET["pdfStatus"]."\"";

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
	
	$attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Lighting', 'Tenancy Fitout', 'Tenancy Fitout', 'Penthouse Architecture', 'Landscaping');
	
if(in_array($_SESSION['userRole'], $permArray) || $_SESSION['ww_builder']['user_type'] == 'manager'){
	$attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Lighting', 'Tenancy Fitout', 'Tenancy Fitout', 'Penthouse Architecture', 'Landscaping');
}

	
	if($_SESSION['userRole'] == 'Architect'){
		$attribute1Arr = array('Architectural');
	}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Structural') || ($_SESSION['userRole'] == 'Structural Engineer')){
		$attribute1Arr = array('Structure');
	}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Services') || ($_SESSION['userRole'] == 'Services Engineer')){
		$attribute1Arr = array('Services');
	}
	if($_SESSION['userRole'] == 'Lighting Consultant')	$attribute1Arr = array('Services');
	if($_SESSION['userRole'] == 'Tenancy Fitout')	$attribute1Arr = array('Tenancy Fitout');
	if($_SESSION['userRole'] == 'Penthouse Architecture')	$attribute1Arr = array('Penthouse Architecture');
	if($_SESSION['userRole'] == 'Landscaping')	$attribute1Arr = array('Landscaping');
	
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
				$attribute2 = str_replace('###', ', ', $aRow[ $aColumns[$i] ]);
			}
			else if ( $aColumns[$i] == "tag" ){
				$tag = $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] == "status" ){
				$status = $aRow[ $aColumns[$i] ];
			}
			else if ( $aColumns[$i] == "is_approved" ){
				$isApproved = 'No';
				if($aRow[ $aColumns[$i] ] == 1){
					$isApproved = 'Yes';
				}
			}
			else if ( $aColumns[$i] == "is_approved_edit" ){
				$isApprovedEdit = 'No';
				if($aRow[ $aColumns[$i] ] == 1){
					$isApprovedEdit = 'Yes';
				}
			}
			
			else if ( $aColumns[$i] == 'id' )
				$rowID = $aRow[ $aColumns[$i] ];
			
		}
		$row[] = $rowID;//0
		$row[] = $number;//1
		$row[] = $title;//2
		$row[] = $revision;//3
//		$row[] = $comments;
		$row[] = $attribute1;//4
		$row[] = $attribute2;//5
		$row[] = $tag;//6
		$row[] = $status;//7
		$row[] = '<img src="images/select_24.png" alt="Select Documet" onclick="selectedDocumet(\''.addslashes($_REQUEST['selectID']).'\', '.$_REQUEST['dispID'].', '.$rowID.', \''.$aRow['pdf_name'].'\', \''.addslashes($attribute1).' > '.addslashes($attribute2).' > '.addslashes($number).'\')" />';//8		
		//$row[] = '<img src="images/select_24.png" alt="Select Documet" onclick="selectedDocumet(\''.addslashes($_REQUEST['selectID']).'\', '.$_REQUEST['dispID'].', '.$rowID.', \''.addslashes($attribute1).' > '.addslashes($attribute2).' > '.addslashes($number).'\')" />';
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
?>