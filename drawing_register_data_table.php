<?php
	session_start();
	
	include("./includes/functions.php");
	$obj = new DB_Class();
	include('./includes/commanfunction.php');
	$common = new COMMAN_Class();
//Special condition for attribute "drawings"
/*$spConSubAttArr = array();
function subTitleArr($attOneEle){
	switch($attOneEle){
		case 'General' :
			$spConSubAttArr = array('"Drawings"', '"3D images"', '"Marketing"', '"Brief and overview"');
		break;
		
		case 'Architectural' :
			$spConSubAttArr = array('"Drawings"', '"Plans"', '"Elevations"', '"Sections"', '"RCP\'s"', '"Details"');
		break;
		
		case 'Structure' :
			$spConSubAttArr =  array('"Drawings"', '"Civil"', '"Site Inspection"', '"Project Advice Notice"');
		break;

		case 'Services' :
			$spConSubAttArr =  array('"Drawings"', '"Mechanical"', '"Electrical"', '"Hydraulic"', '"Fire"');
		break;


		case 'Tenancy Fitout' :
			$spConSubAttArr =  array('"Drawings"', '"Plans"', '"Elevations"', '"Sections"', '"RCP\'s"', '"Details"', '"Photos"');
		break;

		case 'Penthouse Architecture' :
			$spConSubAttArr =  array('"Drawings"', '"Plans"', '"Elevations"', '"Sections"', '"RCP\'s"', '"Details"', '"Photos"');
		break;

		case 'Landscaping' :
			$spConSubAttArr =  array('"Drawings"', '"Plans"', '"Elevations"', '"Sections"', '"RCP\'s"', '"Details"', '"Photos"');
		break;

		default :
			$spConSubAttArr =  array();
		break;
	}	
	return $spConSubAttArr;
}*/

$permArray = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Architect', 'Concrete & PT', 'Lighting', 'Tenancy Fitout', 'Penthouse Architecture', 'Landscaping');
	
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
		$sWhere = "WHERE is_deleted = 0 AND project_id = ".$project_id." AND is_document_transmittal = 0";
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

	//TO AVOID THE DUPLICATE SHOW OF DOCUMENT TRANSMITTALS
	$documentTransmittalArray = array();

	$sQuery = "SELECT FOUND_ROWS()";
	$rResultFilterTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	$sQuery = "SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable $sWhere";
	$rResultTotal = $obj->db_query( $sQuery) or die(mysql_error());
	$aResultTotal = mysql_fetch_array($rResultTotal);
	$iTotal = $aResultFilterTotal[0];


//Get Data for Document Trnasmittal Start Here
	$documentTrasData = array();
	if(isset($_GET["attrTab"]) && !empty($_GET["attrTab"]))
		$attrOne = $_GET["attrTab"];
		
	if(isset($_GET["attr1"]) && !empty($_GET["attr1"]))
		$attrOne = $_GET["attr1"];
		
	$documentTrasData = $common->selQRYMultiple('id, number, title, revision, comments, attribute1, attribute2, tag, status, is_approved, pdf_name, created_by, created_date, is_document_transmittal, is_approved_edit', 'drawing_register', 'is_deleted = 0 AND project_id = '.$project_id.' AND is_document_transmittal = 1 AND attribute1 = "'.$attrOne.'" ORDER BY created_date DESC LIMIT 0, 1');
	$incre = 0;
	if(!empty($documentTrasData))
		$incre = 1;
//Get Data for Document Trnasmittal Start Here	

	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal+$incre,
		"iTotalDisplayRecords" => $iFilteredTotal+$incre,
		"aaData" => array()
	);
	
	$attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete & PT', 'Lighting', 'Tenancy Fitout', 'Penthouse Architecture', 'Landscaping');
	
if(in_array($_SESSION['userRole'], $permArray) || $_SESSION['ww_builder']['user_type'] == 'manager'){
	$attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete & PT', 'Lighting', 'Tenancy Fitout', 'Penthouse Architecture', 'Landscaping');
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

		if($aRow['is_document_transmittal'] == 1 && $documentTransmittalArray[$aRow["number"]]==1){
			continue;
		}
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
			
			else if ( $aColumns[$i] == 'id' ){
				$rowID = $aRow[ $aColumns[$i] ];
				if(isset($_REQUEST['typeFlag']) && $_REQUEST['typeFlag'] == 'attach'){
					$action = '<img class="action" src="images/pmb_attach_24.png" id="viewRevision" title="Attach file"  onclick="attachFiles('.$rowID.', \''.$aRow['title'].'\', \''.$aRow['pdf_name'].'\');" style="float:left;padding:0;" />
					<a href="/project_drawing_register/'.$_SESSION['idp'].'/'.$aRow['pdf_name'].'" target="_blank"><img class="action" src="images/pmb_view_24.png" id="viewRevision" title="View File" style="float:left;padding:0;"  /></a>';		
				}else{
					$action = '';
					$action = '<img class="action" src="images/view.png" id="viewRevision" title="view revisions" onclick="showRevisions('.$rowID.');" />';
					
					if(isset($_REQUEST['req']) && $_REQUEST['req'] == 'pmb'){
						$row[] = '<input type="checkbox" class="closeInspection" name="drawingID[]" id="drawingID" value="'.$aRow['pdf_name'].'###'.$aRow["title"].'">';
					}else{
						$row[] = '<input type="checkbox" class="closeInspection" name="drawingID[]" id="drawingID" value="'.$aRow['pdf_name'].'">';	
					}
					
					if(in_array($_GET['attrTab'], $attribute1Arr) || in_array($_GET['attr1'], $attribute1Arr)){
						if($_SESSION['ww_builder']['user_type'] == 'manager'){
							$action .= '<img class="action" src="images/add.png" id="addRevision" title="add new revision" onclick="addNewRegisterRevision('.$rowID.');" /><br />';
						}
						#if((in_array($_SESSION['userRole'], $permArray) && $_SESSION['ww_builder_id'] == $aRow['created_by'] && $aRow['is_approved'] == 0) || ($_SESSION['ww_builder']['user_type'] == 'manager' && $_SESSION['userRole'] != 'General Consultant')){
						//	echo $_SESSION['userRole'];
						if((in_array($_SESSION['userRole'], $permArray) && ($_SESSION['ww_builder_id'] == $aRow['created_by'] || $aRow['created_date'] < '2013-10-04 00:00:00') ) || ($_SESSION['ww_builder']['user_type'] == 'manager' && $_SESSION['userRole'] != 'General Consultant')){
							$action .= '<img class="action" src="images/edit_right.png" id="editRevision" title="edit register"  onclick="editDrawingRegister('.$rowID.');" />&nbsp;';
							
							if($_SESSION['userRole'] != "Sub Contractor"){
								$action .= '<img class="action" src="images/delete.png"  id="editRevision" title="delete register" onclick="removeImages('.$rowID.')" />';				
							}
						}

						if($_SESSION['architectAttrTwo'] != $attribute2 && $_SESSION['userRole'] == 'Architect' && $_SESSION['architectAttrTwo'] != 'ALL'){//Condition for Archetect and attr2
							$action = '<img class="action" src="images/edit_right.png" id="editRevision" title="edit register"  onclick="editDrawingRegister('.$rowID.');" />&nbsp;';
						}
					}
					if($_SESSION['userRole'] == 'Tender'){//New Tender User condition Start
						$action = '<img class="action" src="images/view.png" id="viewRevision" title="view revisions" onclick="showRevisions('.$rowID.');" />';
					}//New Tender User condition close
					
				}
				
				if($aRow['is_document_transmittal'] == 1){
					$documentTransmittalArray [$aRow["number"]] = 1;
					$action = '<img class="action" src="images/view.png" id="viewRevision" title="view revisions" onclick="showRevisions('.$rowID.');" />';
				}

				//Adding code for saving ids for deletion --------------------
				$action .= "<input type='hidden' name='hdn_ids[]' class='delids' value='".$rowID."' />";

			}
		}
/*		if($_SESSION['ww_builder']['user_type'] == 'manager' &&  $_REQUEST['typeFlag'] != 'attach'){
			$row[] = '<img class="action" src="images/pmb_history.png" id="showHistoryImg" title="Show History Document"  onclick="showHistory('.$rowID.', '.$_SESSION['idp'].');" style="float:left;padding:0;" />';
		}*/
#		$row[] = '<input type="checkbox" class="approveDrawingReg" name="drawingID[]" id="drawingID" value="'.$rowID.'">';	
		$row[] = $number;
		$row[] = $title;
		$row[] = $revision;
//		$row[] = $comments;
		$row[] = $attribute1;
		$row[] = $attribute2;
		$row[] = $tag;
		$row[] = $status;
		$row[] = $isApproved;
if($_SESSION['ww_builder']['user_type'] == 'manager'){
	$row[] = $isApprovedEdit;
}
		$row[] = $action;
		$output['aaData'][] = $row;
	}

	if(!empty($documentTrasData)){
		$newRow = array();
		$newRow[] = "";
		$newRow[] = $documentTrasData[0]['number'];
		$newRow[] = $documentTrasData[0]['title'];
		$newRow[] = $documentTrasData[0]['revision'];
//		$row[] = $comments;
		$newRow[] = $documentTrasData[0]['attribute1'];
		$newRow[] = $documentTrasData[0]['attribute2'];
		$newRow[] = $documentTrasData[0]['tag'];
		$newRow[] = $documentTrasData[0]['status'];
		$isApproved = 'No';
		if($documentTrasData[0]['is_approved'] == 1)
			$isApproved = 'Yes';
		$newRow[] = $isApproved;
		$isApprovedEdit = 'No';
		if($aRow[ $aColumns[$i] ] == 1)
			$isApprovedEdit = 'Yes';		
		if($_SESSION['ww_builder']['user_type'] == 'manager'){
			$newRow[] = $isApprovedEdit;
		}
		$newRow[] = '<img class="action" src="images/view.png" id="viewRevision" title="view revisions" onclick="showRevisions('.$rowID.', \''.$attrOne.'\');" />';
		$output['aaData'][] = $newRow;
	}
//Get Data for Document Trnasmittal Start Here

	echo json_encode( $output );
?>
