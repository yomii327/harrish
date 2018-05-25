<?php session_start();
include("./includes/functions.php");
$obj = new DB_Class();
include('./includes/commanfunction.php');
$common = new COMMAN_Class();


if($_GET['attrTab'] == 'Markups'){
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
		$action = '<img class="action" src="images/pmb_attach_24.png" id="viewRevision" title="Attach file"  onclick="attachFiles('.$markup['markup_id'].', \''.$markup['title'].'\', \''.$markup['img_name'].'\',1);" style="float:left;padding:0;" />
					<a href="'.$markup['img_name'].'" target="_blank"><img class="action" src="images/pmb_view_24.png" id="viewRevision" title="View File" style="float:left;padding:0;"  /></a>';
		
		$row =  array();
		$row[] = '';
		//$row[] = date('d/m/Y', strtotime($markup['created_date']));
		$row[] = $markup['title'];
		//$row[] = $markup['markup_id'];
		$row[] = '';
		$row[] = '';
		$row[] = '';
		$row[] = '';
		$row[] = '';
		$row[] = '';	

		$row[] = $action;
		//'<img src="images/view.png" title="view" onclick="showMarkupImage(\''.base64_encode($markup['img_name']).'\', '.$_GET['pdfID'].', '.$_GET['projectID'].');" />';
		$output['aaData'][] = $row;
	}		
	echo json_encode( $output );
}else{

	$permArray = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Architect', 'Tender', 'Lighting Consultant', 'Tenancy Fitout', 'Penthouse Architecture', 'Landscaping');

	$project_id = $_SESSION['idp'];
	$aColumns = array('id', 'pdf_name', 'number', 'title', 'revision', 'attribute1', 'attribute2', 'file_type', 'tag', 'status', 'is_approved', 'is_approved_edit', 'uploaded_date', 'created_by', 'created_date', 'is_document_transmittal', 'comments');

	$sIndexColumn = "id";

	$sTable = "drawing_register_module_one";

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

	if(isset($_GET["searchRevision"]) && !empty($_GET["searchRevision"]))
		$sWhere .= " AND ((revision LIKE \"%".$_GET["searchRevision"]."%\"))";
		
	if(isset($_GET["onlyApproved"]) && !empty($_GET["onlyApproved"]))
		$sWhere .= " AND is_approved = 1";
		
	if(isset($_GET["attrTab"]) && !empty($_GET["attrTab"]))
		$sWhere .= " AND attribute1 = '".$_GET["attrTab"]."'";

	if(isset($_GET["secAttrTab"]) && !empty($_GET["secAttrTab"]))
		$sWhere .= " AND attribute2 LIKE \"%".$_GET["secAttrTab"]."%\"";

	if(isset($_GET["pdfStatus"]) && !empty($_GET["pdfStatus"]))
		$sWhere .= " AND status = \"".$_GET["pdfStatus"]."\"";
		
	if($_SESSION['ww_builder']['user_type'] != "manager"){
	#	$sWhere .= " AND is_approved_edit = 1";
	}

	if(isset($_GET['fromDate']) && $_GET['fromDate']!=""  && isset($_GET['toDate']) && $_GET['toDate']!="") {
		$from = $_GET['fromDate'];
		$to = $_GET['toDate'];
		$sWhere .= " AND  (date(`uploaded_date`)  BETWEEN  '".$common->dateChanger('-','-',$from)."' AND '".$common->dateChanger('-','-',$to)."' )" ;   
	}

	$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit";

	#die($sQuery);

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
		"iTotalRecords" => $iTotal+1,
		"iTotalDisplayRecords" => $iFilteredTotal+1,
		"aaData" => array()
	);

	$attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Concrete & PT', 'Civil / Landscaping', 'ESD / Green Star', 'Survey');

	if($_SESSION['userRole'] == 'Architect'){
		$attribute1Arr = array('Architectural');
	}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Structural') || ($_SESSION['userRole'] == 'Structural Engineer')){
		$attribute1Arr = array('Structure');
	}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Services') || ($_SESSION['userRole'] == 'Services Engineer')){
		$attribute1Arr = array('Services');
	}
	if($_SESSION['userRole'] == 'Lighting Consultant')	$attribute1Arr = array('Services');
	if($_SESSION['userRole'] == 'Tenancy Fitout')	$attribute1Arr = array('Tenancy Fitout');

	if($_SESSION['idp']==242 || $_SESSION['idp']==243){
		$attribute1Arr = array('Architectural', 'Structural', 'Mechanical', 'Civil', 'Electrical', 'Hydraulics', 'Fire Services', 'Landscaping', 'Specifications, schedules and reports');
	}
	$userRole = $common->selQRYMultiple('user_role','user_projects', 'is_deleted = 0 AND project_id = '.$_SESSION['idp'].'');
	$userRoleValue = $userRole[0]['user_role'];
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
			else if ( $aColumns[$i] == "file_type" ){
				$attribute3 = $aRow[ $aColumns[$i] ];
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
				switch($aRow[$aColumns[$i]]){
					case 1:
						$isApprovedEdit = 'Yes';
					break;
					
					case 2:
						$isApprovedEdit = 'No';
					break;
						
					default:
						$isApprovedEdit = 'NA';
					break;
				}
			}
			else if ( $aColumns[$i] == "uploaded_date" ){
				if(strtotime($aRow[$aColumns[$i]]) > 0){
					$createdDate = date('d/m/Y', strtotime($aRow[$aColumns[$i]]));
				} else {
					$createdDate = '';
				}
			}
			else if ( $aColumns[$i] == 'id' ){
				$rowID = $aRow[ $aColumns[$i] ];
				
				if(isset($_REQUEST['typeFlag']) && $_REQUEST['typeFlag'] == 'attach'){
					if($_SESSION['userRole'] == 'All Defect'){
						//$action = '<img class="action" src="images/icon_history.png" id="viewHistory" title="view history" onclick="showHistory('.$rowID.', '.$project_id.');" />';
					}
						$action = '<button class="green_small" onclick="attachFiles('.$rowID.', \''.$aRow['title'].'\', \''.$aRow['pdf_name'].'\',0);" style="margin-bottom:2px;width:82px;font-size: 10px;"><img src="images/pmb_attach_24.png" class="action" id="viewRevision" title="Add" style="float:left;" width="16"/><span style="float:left;padding: 4px">Add</span></button>
						<button class="green_small" onclick="showRevisions('.$rowID.',0);" style="width:104px;font-size: 10px;"><img src="images/view.png" class="action" id="viewRevision" style="float: left;" title="Markup"/><span style="float:left;padding: 4px">Markup</span></button>';//<a href="/project_drawing_register_v1/'.$_SESSION['idp'].'/'.$aRow['pdf_name'].'" target="_blank"><img class="action" src="images/pmb_view_24.png" width="21" id="viewRevision" title="View File" style="float:left;padding:0;"  /></a>		
				}else{
					$action = '';
					if($_SESSION['userRole'] == 'All Defect'){
						$action = '<img class="action" src="images/icon_history.png" id="viewHistory" title="view history" onclick="showHistory('.$rowID.', '.$project_id.');" />';
					}
					
					$action .= '<img class="action" src="images/view.png" id="viewRevision" title="view revisions" onclick="showRevisions('.$rowID.',1);" />';
					if(isset($_REQUEST['req']) && $_REQUEST['req'] == 'pmb'){
						$row[] = $aRow["id"];
						$row[] = '<input type="checkbox" class="closeInspection" name="drawingID[]" id="drawingID_'.$aRow["id"].'" value="'.$aRow['pdf_name'].'###'.$aRow["title"].'###'.$aRow["id"].'" newvalue="'.$aRow['pdf_name'].'###'.$aRow["title"].'###'.$aRow["id"].'###'.$aRow["revision"].'###'.$aRow["number"].'">';
					}else{
						$row[] = $aRow["id"];
						$row[] = '<input type="checkbox" class="closeInspection" name="drawingID[]" id="drawingID_'.$aRow["id"].'" value="'.$aRow['pdf_name'].'###'.$aRow["title"].'###'.$aRow["id"].'" newvalue="'.$aRow['pdf_name'].'###'.$aRow["title"].'###'.$aRow["id"].'###'.$aRow["revision"].'###'.$aRow["number"].'">';	
					} 
					if((in_array($_GET['attrTab'], $attribute1Arr) || in_array($_GET['attr1'], $attribute1Arr)) && $userRoleValue!='Sub Contractor'){
						$action .= '<img class="action" src="images/add.png" id="addRevision" title="add new revision" onclick="addNewRegisterRevision('.$rowID.');" /><br />';
						if((in_array($_SESSION['userRole'], $permArray) && ($_SESSION['ww_builder_id'] == $aRow['created_by'] || $aRow['uploaded_date'] < '2013-10-04 00:00:00') ) || ($_SESSION['ww_builder']['user_type'] == 'manager' && $_SESSION['userRole'] != 'General Consultant')){
							$action .= '<img class="action" src="images/edit_right.png" id="editRevision" title="edit register"  onclick="editDrawingRegister('.$rowID.');" />&nbsp;';
							
							if($_SESSION['userRole'] != "Sub Contractor"){
								$action .= '<img class="action" src="images/delete.png"  id="editRevision" title="delete register" onclick="removeImages('.$rowID.')" />';				
							}
						}

						if($_SESSION['architectAttrTwo'] != $attribute2 && $_SESSION['userRole'] == 'Architect' && $_SESSION['architectAttrTwo'] != 'ALL'){//Condition for Archetect and attr2
							$action .= '';
						}
					}
					if($_SESSION['userRole'] == 'Tender'){//New Tender User condition Start
						if($_SESSION['userRole'] == 'All Defect'){
							$action = '<img class="action" src="images/icon_history.png" id="viewHistory" title="view history" onclick="showHistory('.$rowID.', '.$project_id.');" />';
						}
						$action .= '<img class="action" src="images/view.png" id="viewRevision" title="view revisions" onclick="showRevisions('.$rowID.',1);" />';
					}//New Tender User condition close
					
					if($_SESSION['userRole'] == 'Subcontractor - Tender'){//New Tender User condition Start
						$action = '<img class="action" src="images/icon_history.png" id="viewHistory" title="view history" onclick="showHistory('.$rowID.', '.$project_id.');" />';
						$action .= '<img class="action" src="images/view.png" id="viewRevision" title="view revisions" onclick="showRevisions('.$rowID.',1);" />';
					}//New Tender User condition close
				}
				
					if($aRow['is_document_transmittal'] == 1){
						if($_SESSION['userRole'] == 'All Defect'){
							$action = '<img class="action" src="images/icon_history.png" id="viewHistory" title="view history" onclick="showHistory('.$rowID.', '.$project_id.');" />';
						}
						$action .= '<img class="action" src="images/view.png" id="viewRevision" title="view revisions" onclick="showRevisions('.$rowID.',1);" />';
					}
			}
		}
	#		$row[] = '<input type="checkbox" class="approveDrawingReg" name="drawingID[]" id="drawingID" value="'.$rowID.'">';	
		$row[] = wordwrap($number, 25, '<br />', true);
		$row[] = wordwrap($title, 25, '<br />', true);
		$row[] = $revision;
	//		$row[] = $comments;
		$row[] = $attribute1;
		$row[] = $attribute2;
	if(isset($_REQUEST['req']) && $_REQUEST['req'] == 'pmb'){	
		$row[] = $attribute3;
	}
		$row[] = $tag;
	if(isset($_REQUEST['req']) && $_REQUEST['req'] == 'pmb'){	
		$row[] = $status;
	}
		$row[] = $isApproved;
		if($_SESSION['ww_builder']['user_type'] == 'manager'){
			$row[] = $isApprovedEdit;
		}else{
			$row[] = '';
		}
	if(isset($_REQUEST['req']) && $_REQUEST['req'] == 'pmb'){	
		$row[] = $createdDate;
	}
		$row[] = $action;
		$output['aaData'][] = $row;
	}

	//Get Data for Document Trnasmittal Start Here
	$documentTrasData = array();
	if(isset($_GET["attrTab"]) && !empty($_GET["attrTab"]))
		$attrOne = $_GET["attrTab"];
		
	if(isset($_GET["attr1"]) && !empty($_GET["attr1"]))
		$attrOne = $_GET["attr1"];
		
	$documentTrasData = $common->selQRYMultiple('id, number, title, revision, comments, attribute1, attribute2, file_type, tag, status, is_approved, pdf_name, created_by, uploaded_date, is_document_transmittal, is_approved_edit', 'drawing_register_module_one', 'is_deleted = 0 AND project_id = '.$project_id.' AND is_document_transmittal = 1 AND attribute1 = "'.$attrOne.'" ORDER BY uploaded_date DESC LIMIT 0, 1');

	if(!empty($documentTrasData)){
		$newRow = array();
	if(isset($_REQUEST['req']) && $_REQUEST['req'] == 'pmb'){	
		$newRow[] = "";
		$newRow[] = "";
	}
		$newRow[] = wordwrap($documentTrasData[0]['number'], 25, '<br />', true);
		$newRow[] = wordwrap($documentTrasData[0]['title'], 25, '<br />', true);
		$newRow[] = $documentTrasData[0]['revision'];
	//		$row[] = $comments;
		$newRow[] = $documentTrasData[0]['attribute1'];
		$newRow[] = $documentTrasData[0]['attribute2'];
	if(isset($_REQUEST['req']) && $_REQUEST['req'] == 'pmb'){
		$newRow[] = $documentTrasData[0]['file_type'];
	}
		$newRow[] = $documentTrasData[0]['tag'];
	if(isset($_REQUEST['req']) && $_REQUEST['req'] == 'pmb'){	
		$newRow[] = $documentTrasData[0]['status'];
	}
		$rowID = $documentTrasData[0]['id'];
		$isApproved = 'No';
		if($documentTrasData[0]['is_approved'] == 1)
			$isApproved = 'Yes';
		$newRow[] = $isApproved;

		$isApprovedEdit = 'No';
		if($aRow[ $aColumns[$i] ] == 1)
			$isApprovedEdit = 'Yes';		
		$newRow[] = $isApprovedEdit;
		if(isset($_REQUEST['req']) && $_REQUEST['req'] == 'pmb'){	
			if(strtotime($documentTrasData[0]['uploaded_date']) > 0){
				$newRow[] = date('d/m/Y', strtotime($documentTrasData[0]['uploaded_date']));
			} else {
				$newRow[] = '';
			}
		}
		if(isset($_REQUEST['typeFlag']) && $_REQUEST['typeFlag'] == 'attach'){
			$newRow[] = '<button class="green_small" onclick="showRevisions('.$rowID.',0);" style="width:104px;font-size: 10px;"><img src="images/view.png" class="action" id="viewRevision" style="float: left;" title="Markup"/><span style="float:left;padding: 4px">Markup</span></button>';
		}else{
			$newRow[] = '<img class="action" src="images/view.png" id="viewRevision" title="view revisions" onclick="showRevisions('.$rowID.',0);" />';
		}
		//<img class="action" src="images/view.png" id="viewRevision" title="view revisions" onclick="showRevisions('.$rowID.');" />';
		$output['aaData'][] = $newRow;
	}
	//Get Data for Document Trnasmittal Start Here
	echo json_encode( $output );

}

?>
