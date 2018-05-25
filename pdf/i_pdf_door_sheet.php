<?php
ob_start();
session_start();

include('../includes/commanfunction.php');
$obj = new COMMAN_Class();

$issued_to_add='';
if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}
elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}
$where = '';$groupBy = '';

	$loc = $object->db_query("select * from progress_monitoring as PM where project_id = '".$_REQUEST['projName']."' and is_deleted = '0'");
	if(!empty($_REQUEST['projName'])){
		$where=" and PM.project_id='".$_REQUEST['projName']."'";
	}
	
	if(!empty($_REQUEST['location'])){
		$postCount++;
		$where.=" and PM.location_id= '".$_REQUEST['location']."'";
	}
	
	if(!empty($_REQUEST['subLocation']) && empty($_REQUEST['subLocation_sub'])){
		$postCount++;
		$sublocations = $obj->subLocationsIdProgressMonitoring ($_REQUEST['subLocation'], ",");
	    $where.=" and (PM.sub_location_id in (".$sublocations."))";
	}
	if (!empty ($_REQUEST['subLocation_sub'])){
		$postCount++;
		$where.=" and PM.sub_location_id= '".$_REQUEST['subLocation_sub']."'";
	}

	if(!empty($_REQUEST['status'])){
		$postCount++;
		$where.=" and PM.status='".$_REQUEST['status']."'";
	}

	if($_REQUEST['DRF']!="" && $_REQUEST['DRT']!=""){
		$postCount++;
		$or.=" PM.start_date between '".date('Y-m-d', strtotime($_REQUEST['DRF']))."' and '".date('Y-m-d', strtotime($_REQUEST['DRT']))."'";
	}
	if($_REQUEST['DRF']!="" && $_REQUEST['FBDF']!=""){$or.=" and";}

	if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
		$postCount++;
		$or.=" PM.end_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
	}

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}

	if(!empty($_REQUEST['issuedToPM'])){
		$include_issue_to = ",issued_to_for_progress_monitoring ipm";
		$where.="  and ipm.issued_to_name='".$_REQUEST['issuedToPM']."' and PM.progress_id=ipm.progress_id and ipm.is_deleted=0";
	}

	$qi="select
		PM.progress_id,
		PM.project_id,
		PM.task,
		PM.status,
		PM.holding_point,
		PM.percentage, 
		PM.location_tree_name
	from
		progress_monitoring as PM $include_issue_to
	where
		PM.is_deleted = 0 $where group by PM.progress_id order by PM.progress_id, location_id, sub_location_id";
		
	$ri = mysql_query($qi);
	
	$queryCount = "SELECT COUNT(PM.progress_id) as totalCount FROM progress_monitoring as PM $include_issue_to WHERE PM.is_deleted = 0 $where";
	$resCount=mysql_query($queryCount);
	$countQuery = mysql_fetch_object($resCount);
	if($countQuery->totalCount > 0){
		$totalCount = $countQuery->totalCount;
	}
	
	$noInspection = mysql_num_rows($ri);
	$ajaxReplay = $noInspection.' Records';
	$noPages = ceil(($noInspection-40)/64 +1);
	if($noInspection > 0){
//Start PDF Creation here
		require('../fpdf/mc_table.php');
//Config Section
		class PDF extends PDF_MC_Table{
			function Header(){
				if($this->PageNo()!=1){
					$this->Cell(0, 10, $this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
					$this->ln();	
					$this->SetFont('times', 'B', 10);
					$header = array($this->d_location, "Status", "% Complete", "Issued To", "Sign Off", "Date", "Hacer Sign Off" , "Date");
					$w = $this->header_width();
					$this->SetWidths($w);
					$best_height = 17;
					$this->row($header, $best_height);
				}
			}
			
			function Footer(){
				$this->SetY(-15);
				$this->SetFont('times','B',10);
				$this->Cell(0, 10, "DefectID – Copyright Wiseworking 2012 / 2013", 0, 0, 'C');
			}
			
			function header_width(){
				return array(80, 40, 40, 45, 20, 20, 20, 20);
			}
		}
	//Top Header Sectiond Start Here
		$pdf = new PDF("L", "mm", "A4");
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		$pdf->SetTopMargin(20);
	
		$pdf->Image('../company_logo/logo.png', 235, 5, 'png', -100);
		$pdf->Ln(8);
		
		$pdf->SetFont('times', 'BU', 12);
		$pdf->Cell(40, 10, 'Door Sheet');		
		$pdf->Ln(6);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(26, 10, 'Project Name : ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(10, 10, $obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projName'], 'project_name'));	
		$pdf->Ln(5);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(11, 10, 'Date : ');	
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(20, 10, date('d/m/Y'));	
		$pdf->Ln(5);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(11, 10, 'Page : ');		
		
		$pdf->SetFont('times', '', 10);
		$pdf->Cell(8, 10, '1 of '.'{nb}');		
		$pdf->Ln(10);
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->Cell(25, 10, 'Report Filtered by :');	
		$pdf->Ln(8);
		$jk=0;
		
		$pdf->Cell(15, 10, '');	
		$jk=0;	
		
		if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Location Name: ');	
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['location'], 'location_title'));	
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
		}
		
		if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation']) && !empty($_REQUEST['subLocation_sub'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Location Name: ');		
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['location'], 'location_title'));
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
	
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Sub Location: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['subLocation'], 'location_title'));
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
			
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Sub Location 1: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['subLocation_sub'], 'location_title'));
				
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
		}else{
			if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
				$pdf->SetFont('times', 'B', 11);		
				$pdf->Cell(50, 10, 'Location Name: ');
				$pdf->SetFont('times', '', 10);
				$pdf->Cell(25, 10, $obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['location'], 'location_title'));
				
				$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
							
				$pdf->SetFont('times', 'B', 11);		
				$pdf->Cell(50, 10, 'Sub Location: ');
				$pdf->SetFont('times', '', 10);
				$pdf->Cell(25, 10, $obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['subLocation'], 'location_title'));
				
				$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
			}
		}

		if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Status: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $_REQUEST['status']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
		}
		
		if(!empty($_REQUEST['DRF']) && isset($_REQUEST['DRF']) || !empty($_REQUEST['DRT']) && isset($_REQUEST['DRT'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Date Raised: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $_REQUEST['DRF'].' to '.$_REQUEST['DRT']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
		}
		
		if(!empty($_REQUEST['FBDF']) && isset($_REQUEST['FBDF']) || !empty($_REQUEST['FBDT']) && isset($_REQUEST['FBDT'])){
			$pdf->SetFont('times', 'B', 11);		
			$pdf->Cell(50, 10, 'Date Raised: ');
			$pdf->SetFont('times', '', 10);
			$pdf->Cell(25, 10, $_REQUEST['FBDF'].' to '.$_REQUEST['FBDF']);
			
			$jk++; if($jk%2 ==0){	$pdf->ln(); $pdf->Cell(15, 10, '');	}		
		}
		
		$w = $pdf->header_width();
		$pdf->SetWidths($w);		
		$best_height = 17;
		
		$pdf->ln();
		
		$pdf->SetFont('times', 'B', 10);

		$currLoctree = '';
		$tr_arr = array();
		while ($row=mysql_fetch_assoc ($ri)){
			$isHolePoint = '';
			if($currLoctree != $row['location_tree_name']){
				$header = array($row['location_tree_name'], "Status", "% Complete", "Issued To", "Sign Off", "Date", "Hacer Sign Off" , "Date");
				$pdf->Row_QA_Wall_Chart($header);
				$pdf->d_location=$row['location_tree_name'];
			}$isHolePoint='';
			if($row['holding_point'] == 'Yes' || $row['holding_point'] == 'yes' || $row['holding_point'] == 'YES'){
				$isHolePoint = 'FILL##190,190,190~~';
			}
			$issueTOName = '';
			$issue = $obj->getRecordsSp('issued_to_for_progress_monitoring', 'progress_id', $row['progress_id'], 'issued_to_name');
			if(!empty($issue)){
				foreach($issue as $issueTo){
					$issueTOName = $issueTo['issued_to_name']."\n";
				}
			}
			$data = array($isHolePoint.$row['task'], $isHolePoint.$row['status'], $isHolePoint.$row['percentage'], $isHolePoint.$issueTOName, $isHolePoint, $isHolePoint, $isHolePoint, $isHolePoint);
			$pdf->Row_QA_Wall_Chart($data);
			$currLoctree = $row['location_tree_name'];
		}
	//print_r ($location_names);
		$file_name = 'Door_Sheet_Report'.microtime().'.pdf';
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
		$rply = 'File Size'.$fieSize;
		echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="closePopUp();" href="./report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';

}else{
	echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
}
?>