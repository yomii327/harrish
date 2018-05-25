<?php
session_start();
set_time_limit(6000000000000000000);

include('../includes/commanfunction.php');
$obj = new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}
if (isset($_REQUEST["name"])){

	$userNameData = $obj->selQRYMultiple('user_id, user_fullname, company_name', 'user', 'is_deleted = 0');
	$userNameArr = array();
	foreach($userNameData as $uNameData){
		$userNameArr[$uNameData['user_id']] = $uNameData['user_fullname'];
	}

	$docData = array();
	#$attribute1Arr = array('"General"', '"Architectural"', '"Structure"', '"Services"', '"Shop Drawings"', '"Concrete & PT"', '"Civil / Landscaping"', '"ESD / Green Star"', '"Survey"', '"Shop Drawings"');
	$attribute1Arr = array('"General"', '"Architectural"', '"Structure"', '"Structural"', '"Mechanical"', '"Civil"', '"Electrical"', '"Hydraulics"', '"Fire Services"', '"Landscaping"', '"Spec., Sched. & Reports"', '"Models"',  '"Services"', '"Shop Drawings"', '"Concrete & PT"', '"Civil / Landscaping"', '"ESD / Green Star"', '"Survey"', '"Shop Drawings"');
	if($_SESSION['userRole'] == 'Architect'){
		$attribute1Arr = array('"Architectural"');
	}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Structural') || ($_SESSION['userRole'] == 'Structural Engineer')){
		$attribute1Arr = array('"Structure"');
	}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Services') || ($_SESSION['userRole'] == 'Services Engineer')){
		$attribute1Arr = array('"Services"');
	}

	if(isset($_GET["attribute1"]) && $_GET["attribute1"] != "All" && $_GET["attribute1"] != ''){
		$attribute1Arr = array("'".$_GET["attribute1"]."'");
	}
	#echo "<pre>"; print_r($attribute1Arr); die;
	$secOrderBy = "";
	if($_GET["sortBy"] != '' && isset($_GET["sortBy"])){
		if($_GET["sortBy"] == 'attribute2')
			$secOrderBy = ", dr.attribute2";
		else
			$secOrderBy = ", dr.number";
	}
	if($secOrderBy == "")
		$secOrderBy = ", dr.number";
	
	$sect = ", max(drr.id) AS revID";
	$orderBy = " ORDER BY dr.attribute1 ".$secOrderBy." asc";
	$groupBy = " GROUP BY dr.id";
	if(isset($_GET["revisionType"]) && !empty($_GET["revisionType"])){
		if($_GET["revisionType"] == 'complete'){
			$orderBy = " ORDER BY dr.attribute1 ".$secOrderBy." asc, drr.id desc";
			$groupBy = " ";
			$sect = " ";
		}
	}
	$docData = $obj->selQRYMultiple('dr.id, dr.number, dr.title, dr.pdf_name, dr.created_date, dr.is_approved, drr.revision_number, drr.created_date AS revisionAdded, dr.attribute1, dr.attribute2, dr.status, dr.last_modified_by'.$sect, 'drawing_register_revision_module_one AS drr, drawing_register_module_one AS dr', 'drr.is_deleted = 0 AND dr.is_deleted = 0 AND drr.drawing_register_id = dr.id AND dr.attribute1 IN ('.join(',', $attribute1Arr).') AND dr.is_document_transmittal = 0 AND dr.project_id = '.$_REQUEST['projID'].' AND dr.project_id = '.$_REQUEST['projID'].' '.$groupBy.' '.$orderBy);
	#$docData = $obj->selQRYMultiple('dr.id, dr.number, dr.title, dr.pdf_name, dr.created_date, dr.is_approved, drr.revision_number, drr.created_date AS revisionAdded, dr.attribute1, dr.attribute2, dr.status, dr.last_modified_by'.$sect, 'drawing_register_revision_module_one AS drr, drawing_register_module_one AS dr', 'drr.is_deleted = 0 AND dr.is_deleted = 0 AND drr.drawing_register_id = dr.id AND dr.is_document_transmittal = 0 AND dr.project_id = '.$_REQUEST['projID'].' AND dr.project_id = '.$_REQUEST['projID'].' '.$groupBy.' '.$orderBy);
	if($_GET["revisionType"] != 'complete'){
		$drIDArr = array();
		foreach($docData as $dData){
			$drIDArr[] = $dData['revID'];
		}
		$revisionNumberData = $obj->selQRYMultiple('id, revision_number, drawing_register_id', 'drawing_register_revision_module_one', 'id IN ('.join(',', $drIDArr).')');
		$curretnVerArr = array();
		foreach($revisionNumberData as $revData){
			$curretnVerArr[$revData['id']] = $revData['revision_number'];
		}
	}
	
	$noInspection = 0;

	if(is_array($docData)){
		$noInspection = sizeof($docData);
	}
$logo = $obj->selQRYMultiple('project_logo','projects','is_deleted = 0 AND project_id = '.$_SESSION['idp'].'', 'data');
if(file_exists('../project_images/'.$logo[0]['project_logo']) && !empty($logo[0]['project_logo'])){
	$logo_proj = '../project_images/'.$logo[0]['project_logo']; 	
}else{
	$logo_proj = '../company_logo/logo.png';
}	
	if($noInspection > 0){
		$totalPage = 01;//Attachment images Count +1;
		require('../fpdf/mc_table.php');	
		class PDF extends PDF_MC_Table{
		/*	function Header(){
				if($this->PageNo()!=1){
					$this->Cell(0, 10, $this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
					$this->ln();	
					$this->SetFont('times', 'B', 10);
					$header = array("Document Title", "Document Type", "Date Added", "Status", "Revision No", "Revision Date", "Attribute 1", "Attribute 2");
					$w = $this->header_width();
					$this->SetWidths($w);
					$best_height = 17;
					$this->row($header, $best_height);
				}
			}*/
			
			function Footer(){
				$this->SetY(-15);
				$this->SetFont('times','B',10);
				$this->Cell(0, 10, 'DefectID – Copyright Wiseworking 2012 / 2013', 0, 0, 'C');
			}
			
			function header_width(){
				return array(28, 53, 18, 18, 13, 15, 18, 18, 18);
			}
		}
		$pdf = new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		$pdf->SetTopMargin(20);
	
		$pdf->Image($logo_proj , 145, 5, 'png', -140);
		$pdf->Ln(8);
		
		$pdf->SetFont('times', 'BU', 12);
		$pdf->Cell(40, 10, 'Document Register Report');		
		$pdf->Ln(6);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(26, 10, 'Project Name: ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(10, 10, $obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projID'], 'project_name'));	
		$pdf->Ln(5);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(11, 10, 'Date: ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(20, 10, date('d/m/Y'));	
		$pdf->Ln(5);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(27, 10, 'Total Document: ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(20, 10, $noInspection);	
		$pdf->Ln(5);	
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(11, 10, 'Page: ');		
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(8, 10, '1 of '.'{nb}');		
		$pdf->Ln(10);
/*		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(25, 10, 'Report Filtered by :');	
		$pdf->Ln(8);
		$jk=0;
		
		if(isset($_GET["userReport"]) && !empty($_GET["userReport"])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'User Name: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $_REQUEST['userReport']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
		}
	
		if(isset($_GET["searchKeywordReport"]) && !empty($_GET["searchKeywordReport"])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Search keyword: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $_REQUEST['searchKeywordReport']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
		}

		if(isset($_GET["DAF"]) && !empty($_GET["DAF"]) && isset($_GET["DAT"]) && !empty($_GET["DAT"])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Date Added: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $_REQUEST['DAF'].' to '.$_REQUEST['DAT']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
		}
		
		if(isset($_GET["companyReport"]) && !empty($_GET["companyReport"])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Company Name: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $_REQUEST['companyReport']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
		}

		if(isset($_GET["companyReport"]) && !empty($_GET["companyReport"])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Company Name: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $_REQUEST['companyReport']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
		}
*/
		$header = array("Document Number", "Document Title", "Document Type", "Date Added", "Status", "Revision No", "Revision Date", "Attribute 2", "Last Modified By");
		
		$w = $pdf->header_width();
		$pdf->SetWidths($w);		
		$best_height = 17;
//		$pdf->row($header, $best_height);

	//	$pdf->SetFont('times', '', 9);
		
		$oldAttribute1='';
		foreach ($docData as $doc){
			if(empty($oldAttribute1) && !empty($doc['attribute1'])){
				$oldAttribute1 = $doc['attribute1'];
				$pdf->SetFont('times', 'B', 10);
				$pdf->Cell(11, 10, $doc['attribute1']);	
				$pdf->Ln(10);
				$pdf->row($header, $best_height);
		
				$pdf->SetFont('times', '', 9);
				
			}else if($oldAttribute1 != $doc['attribute1']){
				$pdf->AddPage();
				$oldAttribute1 = $doc['attribute1'];
				$pdf->Ln(5);
				$pdf->SetFont('times', 'B', 10);
				$pdf->Cell(11, 10, $doc['attribute1']);	
				$pdf->Ln(10);
				$pdf->row($header, $best_height);
		
				$pdf->SetFont('times', '', 9);
			}
			
			
			$pdfExtension = end(explode('.', $doc['pdf_name']));
			$fileExt = 'PDF File';
			if(strtolower($pdfExtension) != 'pdf'){
				$fileExt = 'Drawing File';
			}
			$status = 'Not Approved';
			if($doc['is_approved'] == 1){
				$status = 'Approved';
			}

			$RevisionNumber = "";
			if($_GET["revisionType"] == 'complete'){
				$RevisionNumber = $doc['revision_number'];
			}else{
				$RevisionNumber = $curretnVerArr[$doc['revID']];
			}
			
			
			$pdf->Row(array(
						trim($doc['number']),
						trim($doc['title']),
						$fileExt,
						date('d/m/Y', strtotime($doc['created_date'])),
						$doc['status'],
						$RevisionNumber,
						date('d/m/Y', strtotime($doc['revisionAdded'])),
						str_replace("###", ",",$doc['attribute2']),
						$userNameArr[$doc['last_modified_by']]
					)
				);	
		}
//Title String Header
		$file_name = 'manager_drawing_report_'.microtime().'.pdf';
		$d = '../report_pdf/'.$owner_id;
		if(!is_dir($d))
			mkdir($d);
		
		if (file_exists($d.'/'.$file_name))
			unlink($d.'/'.$file_name);
		
		$tempFile = $d.'/'.$file_name;
		
		$pdf->Output($tempFile);
		
		$fieSize = filesize($tempFile);
		
		$fieSize = floor($fieSize/(1024));
		if ($fieSize > 1024){
			$fieSize = floor($fieSize/(1024)) . "Mbs";
		}else{
			$fieSize .= "Kbs";
		}
		$rply = 'Report Generatd '.$fieSize;
		echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="clearDiv();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';
	}else{
		echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
	}
}?>
