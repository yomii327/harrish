<?php
ob_start();
session_start();

require_once'../includes/functions.php';
include('../includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
include('../fpdf/commonRpt_nonConf.php');

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}
elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}

#echo "<pre>"; print_r($_SESSION); die;
if(isset($_REQUEST['nonConf'])){
	#echo "<pre>"; print_r($_SESSION['non_conformance_query']); die;
	$sQuery = $_SESSION['non_conformance_query'];
	$rResult = $obj->db_query($sQuery ) or die('Mysql Error : '.mysql_error());
	
	$rStatus = '';
	$chkStatus = 0;
	if(isset($_SESSION['nonConfStatus']) && !empty($_SESSION['nonConfStatus'])){
		$rStatus = $_SESSION['nonConfStatus'];
		if($rStatus=='Open'){
			$chkStatus = 1;
		} elseif($rStatus=='Close'){
			$chkStatus = 2;
		} elseif($rStatus=='Fixed'){
			$chkStatus = 3;
		} 
	}
	$sWhere = $_SESSION['nonConfWhere'];
	$aColumns = $_SESSION['aColumns'];
	#echo count($aColumns)."<<====<pre>=====>>"; print_r($aColumns); die;
	$queryCount = "SELECT count(I.inspection_id) FROM user_projects as P, issued_to_for_inspections as F, project_inspections as I WHERE I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.is_deleted = '0' and F.is_deleted = '0' and P.is_deleted = '0' $sWhere group by I.inspection_id";

	$resCount=mysql_query($queryCount);
	if(mysql_num_rows($resCount) > 0){
		$totalCount = mysql_num_rows($resCount);
	}

	$noInspection = mysql_num_rows($rResult);
	$ajaxReplay = $noInspection.' Records';
	$noPages = ceil(($noInspection-14)/17 +1);

	
	// $htmlHeadVal ='<table width="100%"><tr><td width="40%"></td><td width="70%" align="right" style="padding-right:20px;"><img src="company_logo/logo.png" height="50"  /></td>
	// 			</tr><tr><td width="40%" style="font-size:14px;"><u><b>Non Conformance Report</b></u></td><td>&nbsp;</td></tr><tr><td style="font-size:14px;"><strong>Project Name : </strong>'.$proName.'</td><td>&nbsp;</td></tr><tr><td style="font-size:14px;"><strong>Date : </strong>'.date('d / m / Y').'</td><td>&nbsp;</td></tr><tr><td style="font-size:14px"><strong>Inspections: </strong>'.$noInspection.'</td><td>&nbsp;</td></tr><tr><td style="font-size:14px;"><strong>Page : </strong>1 of '.$noPages.'</td><td>&nbsp;</td></tr></table>';

	
	$pdf = new PDF("L", "mm", "A4");
	$pdf->SetLeftMargin(4);
	$pdf->SetRightMargin(5);
	$y=$pdf->GetY();
	$x=$pdf->GetX();
	$pdf->AddPage();
	$imgPath = "IMAGE##24##30##";
	$imgPath1 = "IMAGE##83##122##";
	$imgPath2 = "IMAGE##178##250##";

	$pdf->Image('../company_logo/logo.png', 235, 5, -110);
	$pdf->Ln(8);
	
	$pdf->SetFont('times', 'BU', 12);
	$pdf->Cell(100, 10, 'Non Conformance Report');
	$pdf->Ln(6);
	
	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(26, 10, 'Project Name : ');
	
	$pdf->SetFont('times', '', 10);
	$pdf->Cell(100, 10, $_SESSION["nonConfProName"]);
	$pdf->Ln(5);
	
	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(11, 10, 'Date : ');	
	
	$pdf->SetFont('times', '', 10);
	$pdf->Cell(20, 10, date('d/m/Y'));	
	$pdf->Ln(5);
	
	$pdf->SetFont('times', 'B', 10);
	$pdf->Cell(23, 10, 'Inspections : ');

	$pdf->SetFont('times', '', 10);
	$pdf->Cell(20, 10, $noInspection);	
	$pdf->Ln(5);

	$y=$pdf->GetY();

	$pdf->SetY($y+10);



	$pdf->SetFont('Times','',9);
    $w = $pdf->header_width(array(24,26,24,30,24,22,22,26,21,18,26,26));
    $pdf->SetWidths($w);
    $printData = array("ID","Location","Quality Checklist","Description","Inspected By","Date Raised","Raised By","Issued To","Fix By Date","Status","Image 1","Image 2");
    $pdf->createTableWithFormating($printData, 26, array('B','B','B','B','B','B','B','B','B','B','B'), array('DF','DF','DF','DF','DF','DF','DF','DF','DF','DF','DF'), array('','','','','','','','','','',''), '', '', 'C');
	
	while ( $aRow = mysql_fetch_array( $rResult ) ){
		#print_r($aRow);
		$non_conf_id = $aRow['non_conformance_id'];
		$atdata = $object->selQRYMultiple('qa_graphic_name,qa_graphic_id', 'qa_graphics', 'is_deleted = 0 AND non_conformance_id = '.$non_conf_id);
		$image1 = ''; $image2 = '';
		if(isset($atdata[0]['qa_graphic_name']) && !empty($atdata[0]['qa_graphic_name'])){
			$image1 = '../inspections/photo/'.$atdata[0]['qa_graphic_name'];
			if(!file_exists($image1)){
				$image1 = '';
			}
		}
		if(isset($atdata[1]['qa_graphic_name']) && !empty($atdata[1]['qa_graphic_name'])){
			$image2 = '../inspections/drawing/'.$atdata[1]['qa_graphic_name'];
			if(!file_exists($image2)){
				$image2 = '';
			}
		}
		$qualityChecklist = '';
		$statusArr = nonConfromanceIssuedto($aRow['task_status_id'], $aRow['project_id']);
		if(isset($rStatus) && !empty($rStatus)){
			if($chkStatus == 1 && in_array('Open', $statusArr['status'])){
			    $row = array();
			    for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			      if ( $aColumns[$i] == "QA.qa_checklist_id" ){
			        $qaID = $aRow['qa_checklist_id'];
			      } else if ( $aColumns[$i] == "QA.created_date" ){
			        $date = date('d/m/Y', strtotime($aRow['created_date']));
			      } else if ( $aColumns[$i] == "QA.location_tree" ){
			        $locationTree = $aRow['location_tree'];
			      } else if ( $aColumns[$i] == "QA.status" ){
			        $status = implode(' > ', $statusArr['status']);
			      } else if ( $aColumns[$i] == "QAI.qa_inspection_description" ){
			        $description = $aRow['qa_inspection_description'];
			      } else if ( $aColumns[$i] == "QATS.task_name" ){
			        $task_name = $aRow['task_name'];
			      } else if ( $aColumns[$i] == 'QATS.id' ){
			        $issuedto = implode(' > ', $statusArr['issuedto']);
			        $fixedByDate = implode(' > ', $statusArr['fixed_by_date']);
			      } else if ( $aColumns[$i] == "QAI.qa_inspection_inspected_by" ){
			        $inspectedBy = $aRow['qa_inspection_inspected_by'];
			      } else if ( $aColumns[$i] == "QAI.qa_inspection_raised_by" ){
			        $raisedBy = $aRow['qa_inspection_raised_by'];
			      } else if ( $aColumns[$i] == "QA.project_checklist_id" ){
                    $qualityChecklist = getQualityChecklistName($aRow['project_id'], $aRow['project_checklist_id']);
                  }
			    }
			    #echo $non_conf_id.'===='.$locationTree.'===='.$description.'===='.$inspectedBy.'===='.$date.'===='.$raisedBy.'===='.$issuedto.'===='.$fixedByDate.'===='.$status.'===='.$image1.'===='.$image2; die;
			    if(!empty($image1)){
			          $image1 = $imgPath.$image1;
			      }
			      if(!empty($image2)){
			          $image2 = $imgPath.$image2;
			      }

				  $w = $pdf->header_width(array(24,26,24,30,24,22,22,26,21,18,26,26));
			      $pdf->SetWidths($w);
			      $printData = array($non_conf_id,$locationTree,$qualityChecklist,$description,$inspectedBy,$date,$raisedBy,$issuedto,$fixedByDate,$status,$image1,$image2);
			      $pdf->createTableWithFormating($printData, 26, array('','','','','','','','','','',''), array('DF','DF','DF','DF','DF','DF','DF','DF','DF','DF','DF'), array('','','','','','','','','','',''), '', '', 'C');
			}//End of opne
		  	if($chkStatus == 2 && in_array('Close', $statusArr['status'])){
			    $row = array();
			    for ( $i=0 ; $i<count($aColumns) ; $i++ ){
					if ( $aColumns[$i] == "QA.qa_checklist_id" ){
					$qaID = $aRow['qa_checklist_id'];
					} else if ( $aColumns[$i] == "QA.created_date" ){
					$date = date('d/m/Y', strtotime($aRow['created_date']));
					} else if ( $aColumns[$i] == "QA.location_tree" ){
					$locationTree = $aRow['location_tree'];
					} else if ( $aColumns[$i] == "QA.status" ){
					$status = implode(' > ', $statusArr['status']);
					} else if ( $aColumns[$i] == "QAI.qa_inspection_description" ){
					$description = $aRow['qa_inspection_description'];
					} else if ( $aColumns[$i] == "QATS.task_name" ){
					$task_name = $aRow['task_name'];
					} else if ( $aColumns[$i] == 'QATS.id' ){
					$issuedto = implode(' > ', $statusArr['issuedto']);
					$fixedByDate = implode(' > ', $statusArr['fixed_by_date']);
					} else if ( $aColumns[$i] == "QAI.qa_inspection_inspected_by" ){
					$inspectedBy = $aRow['qa_inspection_inspected_by'];
					} else if ( $aColumns[$i] == "QAI.qa_inspection_raised_by" ){
					$raisedBy = $aRow['qa_inspection_raised_by'];
					}else if ( $aColumns[$i] == "QA.project_checklist_id" ){
                    $qualityChecklist = getQualityChecklistName($aRow['project_id'], $aRow['project_checklist_id']);
					}
		    	}
			    #echo $non_conf_id.'===='.$locationTree.'===='.$description.'===='.$inspectedBy.'===='.$date.'===='.$raisedBy.'===='.$issuedto.'===='.$fixedByDate.'===='.$status.'===='.$image1.'===='.$image2; die;
			    if(!empty($image1)){
			          $image1 = $imgPath.$image1;
			      }
			      if(!empty($image2)){
			          $image2 = $imgPath.$image2;
			      }

			      $w = $pdf->header_width(array(24,26,24,30,24,22,22,26,21,18,26,26));
			      $pdf->SetWidths($w);
			      $printData = array($non_conf_id,$locationTree,$qualityChecklist,$description,$inspectedBy,$date,$raisedBy,$issuedto,$fixedByDate,$status,$image1,$image2);
			      $pdf->createTableWithFormating($printData, 26, array('','','','','','','','','','',''), array('DF','DF','DF','DF','DF','DF','DF','DF','DF','DF','DF'), array('','','','','','','','','','',''), '', '', 'C');
			}//End of closed.
		  	if($chkStatus == 3 && in_array('Fixed', $statusArr['status'])){
			    $row = array();
			    for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			      if ( $aColumns[$i] == "QA.qa_checklist_id" ){
			        $qaID = $aRow['qa_checklist_id'];
			      } else if ( $aColumns[$i] == "QA.created_date" ){
			        $date = date('d/m/Y', strtotime($aRow['created_date']));
			      } else if ( $aColumns[$i] == "QA.location_tree" ){
			        $locationTree = $aRow['location_tree'];
			      } else if ( $aColumns[$i] == "QA.status" ){
			        $status = implode(' > ', $statusArr['status']);
			      } else if ( $aColumns[$i] == "QAI.qa_inspection_description" ){
			        $description = $aRow['qa_inspection_description'];
			      } else if ( $aColumns[$i] == "QATS.task_name" ){
			        $task_name = $aRow['task_name'];
			      } else if ( $aColumns[$i] == 'QATS.id' ){
			        $issuedto = implode(' > ', $statusArr['issuedto']);
			        $fixedByDate = implode(' > ', $statusArr['fixed_by_date']);
			      } else if ( $aColumns[$i] == "QAI.qa_inspection_inspected_by" ){
			        $inspectedBy = $aRow['qa_inspection_inspected_by'];
			      } else if ( $aColumns[$i] == "QAI.qa_inspection_raised_by" ){
			        $raisedBy = $aRow['qa_inspection_raised_by'];
			      } else if ( $aColumns[$i] == "QA.project_checklist_id" ){
                    $qualityChecklist = getQualityChecklistName($aRow['project_id'], $aRow['project_checklist_id']);
                  }
			    }
			    #echo $non_conf_id.'===='.$locationTree.'===='.$description.'===='.$inspectedBy.'===='.$date.'===='.$raisedBy.'===='.$issuedto.'===='.$fixedByDate.'===='.$status.'===='.$image1.'===='.$image2; die;
			    if(!empty($image1)){
		          	$image1 = $imgPath.$image1;
		      	}
			    if(!empty($image2)){
			        $image2 = $imgPath.$image2;
			    }

			    $w = $pdf->header_width(array(24,26,24,30,24,22,22,26,21,18,26,26));
				$pdf->SetWidths($w);
				$printData = array($non_conf_id,$locationTree,$qualityChecklist,$description,$inspectedBy,$date,$raisedBy,$issuedto,$fixedByDate,$status,$image1,$image2);
				$pdf->createTableWithFormating($printData, 26, array('','','','','','','','','','',''), array('DF','DF','DF','DF','DF','DF','DF','DF','DF','DF','DF'), array('','','','','','','','','','',''), '', '', 'C');
			}//End of closed.
		}else if(empty($rStatus)){
		  	$row = array();
		    for ( $i=0 ; $i<count($aColumns) ; $i++ ){
				if ( $aColumns[$i] == "QA.qa_checklist_id" ){
					$qaID = $aRow['qa_checklist_id'];
				} else if ( $aColumns[$i] == "QA.created_date" ){
				$date = date('d/m/Y', strtotime($aRow['created_date']));
				} else if ( $aColumns[$i] == "QA.location_tree" ){
					$locationTree = $aRow['location_tree'];
				} else if ( $aColumns[$i] == "QA.status" ){
					$status = implode(' > ', $statusArr['status']);
				} else if ( $aColumns[$i] == "QAI.qa_inspection_description" ){
					$description = $aRow['qa_inspection_description'];
				} else if ( $aColumns[$i] == "QATS.task_name" ){
					$task_name = $aRow['task_name'];
				} else if ( $aColumns[$i] == 'QATS.id' ){
					$issuedto = implode(' > ', $statusArr['issuedto']);
					$fixedByDate = implode(' > ', $statusArr['fixed_by_date']);
				} else if ( $aColumns[$i] == "QAI.qa_inspection_inspected_by" ){
					$inspectedBy = $aRow['qa_inspection_inspected_by'];
				} else if ( $aColumns[$i] == "QAI.qa_inspection_raised_by" ){
					$raisedBy = $aRow['qa_inspection_raised_by'];
				} else if ( $aColumns[$i] == "QA.project_checklist_id" ){
                    $qualityChecklist = getQualityChecklistName($aRow['project_id'], $aRow['project_checklist_id']);
                }
		    }
	    	#echo $non_conf_id.'===='.$locationTree.'===='.$description.'===='.$inspectedBy.'===='.$date.'===='.$raisedBy.'===='.$issuedto.'===='.$fixedByDate.'===='.$status.'===='.$image1.'===='.$image2; die;
	    	if(!empty($image1)){
	        	$image1 = $imgPath.$image1;
		    }
		    if(!empty($image2)){
		        $image2 = $imgPath.$image2;
		    }

	    	$w = $pdf->header_width(array(24,26,24,30,24,22,22,26,21,18,26,26));
	      	$pdf->SetWidths($w);
	      	$printData = array($non_conf_id,$locationTree,$qualityChecklist,$description,$inspectedBy,$date,$raisedBy,$issuedto,$fixedByDate,$status,$image1,$image2);
	      	$pdf->createTableWithFormating($printData, 26, array('','','','','','','','','','',''), array('DF','DF','DF','DF','DF','DF','DF','DF','DF','DF','DF'), array('','','','','','','','','','',''), '', '', 'C');
		}
	}
	#echo $pdf->Output();die;
	$addInEmail = $_REQUEST['addInEmail'];
	
	if($addInEmail==1){
		$file_name = 'non_conformance_report'.microtime().'.pdf';
		$pdf->Output($file_name, 'F');
		$d = '../report_pdf/'.$owner_id;
		$fPath = 'report_pdf/'.$owner_id.'/'.$file_name;
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
		$data = array('error'=>false,'status'=>true,'fileName'=>$file_name,'filePath'=>$fPath,'fileSize'=>$fieSize,'projName'=>$_SESSION["nonConfProName"]);
		echo json_encode($data);
	}else{
		$pdf->Output('non_conformance_report.pdf', 'D');
		//$pdf->Output();
	}
}
/*  NON CONFORMANCE ISSUEDTO AND STATUS
 * *******************************************/
function nonConfromanceIssuedto($nonConId=0, $projectId=0, $qiiStatus=''){
	$output = array();	
	$qiiQuery = 'SELECT qa_issued_to_name, qa_inspection_status,qa_inspection_fixed_by_date FROM qa_issued_to_inspections WHERE non_conformance_id = (SELECT non_conformance_id FROM qa_inspections WHERE task_id ='. $nonConId .' AND project_id ='. $projectId .') AND is_deleted =0';
	$iResult = mysql_query($qiiQuery) or die(mysql_error());
	$iNumRows = mysql_num_rows($iResult);
	if($iNumRows > 0){
		while($iRows = mysql_fetch_array($iResult)){
			$output['issuedto'][] = $iRows['qa_issued_to_name'];
			$output['fixed_by_date'][] =  date('d/m/Y', strtotime($iRows['qa_inspection_fixed_by_date']));
			$output['status'][] = (!empty($iRows['qa_inspection_status'])) ? $iRows['qa_inspection_status'] : 'Open';
		}
	}
	return $output;
}
/*  QUALITY CHECKLIST NAME
 * ********************************************/
function getQualityChecklistName($projectId=0, $qaChecklistId=0){
    $name = '';
    $query = 'SELECT checklist_name FROM check_list_items_project WHERE is_deleted=0 AND project_id='. $projectId .' AND id='. $qaChecklistId;
    $result = mysql_query($query);
    $numRows = mysql_num_rows($result);
    if($numRows > 0){
        $row = mysql_fetch_array($result);
        $name = $row['checklist_name'];
    }
    return $name;
}
?>




