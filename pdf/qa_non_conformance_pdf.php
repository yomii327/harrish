<?php
ob_start();
session_start();

include('../includes/commanfunction.php');
$obj = new COMMAN_Class();

include('../fpdf/commonRpt.php');
#echo "<pre>"; print_r($_SESSION); die;

$selectedProId          = $_SESSION['qaChecklistProId'];
$selectedChecklistId    = $_SESSION['qaChecklistId'];
$selectedLocationId     = $_SESSION['qaChecklistLocationId'];
$selectedSubLocationId  = $_SESSION['qaChecklistSubLocationId'];
$selectedSubLocationId1 = $_SESSION['qaChecklistSubLocationId1'];
$selectedSubLocationId2 = $_SESSION['qaChecklistSubLocationId2'];

if(isset($_REQUEST['qaChecklistId'])){
    $pro_name = $checklist_name = $locationData = '';
    if(!empty($selectedProId)){
        $pname="select project_name from user_projects where project_id=".$selectedProId." and is_deleted=0";
        $result_p=mysql_query($pname);
        $pro_name_data=mysql_fetch_array($result_p);
        $pro_name = $pro_name_data['project_name'];
    }

    if(!empty($selectedChecklistId)){
        $cQuery = "SELECT id,checklist_name FROM check_list_items_project WHERE id = $selectedChecklistId AND is_deleted = 0";
        $result_c=mysql_query($cQuery);
        $checlst_name_data=mysql_fetch_array($result_c);
        $checklist_name = $checlst_name_data['checklist_name'];
    }

    if(!empty($selectedLocationId)){
        $lQuery = "SELECT location_id,location_title FROM project_locations WHERE location_id = $selectedLocationId AND is_deleted = 0";
        $result_l = mysql_query($lQuery);
        $location_data=mysql_fetch_array($result_l);
        $locationData = $location_data['location_title'];

        if(!empty($selectedSubLocationId)){
            $lQuery1 = "SELECT location_id,location_title FROM project_locations WHERE location_id = $selectedSubLocationId AND is_deleted = 0";
            $result_l1 = mysql_query($lQuery1);
            $location_data1=mysql_fetch_array($result_l1);
            $locationData = $locationData . ' > ' . $location_data1['location_title'];

            if(!empty($selectedSubLocationId1)){
                $lQuery2 = "SELECT location_id,location_title FROM project_locations WHERE location_id = $selectedSubLocationId1 AND is_deleted = 0";
                $result_l2 = mysql_query($lQuery2);
                $location_data2=mysql_fetch_array($result_l2);
                $locationData = $locationData . ' > ' . $location_data2['location_title'];

                if(!empty($selectedSubLocationId2)){
                    $lQuery3 = "SELECT location_id,location_title FROM project_locations WHERE location_id = $selectedSubLocationId2 AND is_deleted = 0";
                    $result_l3 = mysql_query($lQuery3);
                    $location_data3=mysql_fetch_array($result_l3);
                    $locationData = $locationData . ' > ' . $location_data3['location_title'];                    
                }
            }
            
        }   
    }

    #echo $pro_name . '<<=pro_name==' . $checklist_name . '<<=checklist_name==' . $locationData . '<<=location_name=='; die;

	$taskImageArr = array();
	$taskPdfImageArr = array();
	$taskDataArr = array();
	$query = "SELECT project_name FROM projects WHERE project_id = '".$_REQUEST['projID']."' AND  is_deleted = 0";
    $q_data = mysql_query($query);
    while($pro_data = mysql_fetch_array($q_data)){
        $project_name = $pro_data['project_name'];
    }

    $q = "SELECT qc.id as qa_id,qc.qa_checklist_id, qc.created_date, qc.location_tree, qc.sc_sign, qc.contractor_sign, qcTask.task_name, qcTask.task_comment, qcTask.status, qcTask.completion_date, qcTask.task_status_id, qc.sub_contractor_name FROM qa_checklist AS qc JOIN qa_checklist_task_status AS qcTask ON qcTask.qa_checklist_id = qc.qa_checklist_id WHERE qc.qa_checklist_id = '".$_REQUEST['qaChecklistId']."' AND qc.is_deleted = 0 AND  qcTask.is_deleted = 0 AND qcTask.status = 'No'";

    $res = mysql_query($q);
    $qaChecklistId = $_REQUEST['qaChecklistId'];
    $projID = $_REQUEST['projID'];
    while($qData = mysql_fetch_array($res)){
        $locationName = $qData['location_tree'];
        $createdDate = date('d/m/Y', strtotime($qData['created_date']));
        $sc_sign = $qData['sc_sign'];
        $contractor_sign = $qData['contractor_sign'];
        $subContractorName = $qData['sub_contractor_name'];
        $dat = '';
        if(!empty($qData['completion_date']) && $qData['completion_date'] > 0 && $qData['status'] == 'Yes'){
            $date = strtotime($qData['completion_date']);
            $dat = date('d-m-Y', $date);
        }

        $taskDataArr[] = array(
            'task_status_id' => $qData['task_status_id'],
            'task_name' => str_replace("’", "'", $qData['task_name']),
            'comment_mandatory' => $qData['comment_mandatory'],
            'comment' => $qData['task_comment'],
            'completion_date' => $dat,
            'status' => $qData['status']     //$qData['task_status'],
        );
    }

    $sc_image = '';
    $contractor_image = '';
    if(!empty($sc_sign)){
        $sc_image_path = "../inspections/ncr_files/".$sc_sign;
        if(file_exists($sc_image_path)){
            $sc_image = $sc_image_path;
        }
    }

    if(!empty($contractor_sign)){
        $contractor_image_path = "../inspections/ncr_files/".$contractor_sign;
        if(file_exists($contractor_image_path)){
            $contractor_image = $contractor_image_path;
        }
    }

    if(!empty($taskDataArr)){
        foreach ($taskDataArr as $row) {
            if($row['status'] == 'Yes'){
                $q_img = "SELECT ncr_att.ncr_attachment_id, ncr_att.attachment_file_name, ncr_att.attachment_type FROM qa_ncr_task_detail AS td JOIN qa_ncr_attachments AS ncr_att ON ncr_att.task_detail_id = td.task_detail_id WHERE (ncr_att.attachment_type = 'evidence_image' OR ncr_att.attachment_type = 'evidence_pdf') AND td.task_id = '".$row['task_status_id']."' AND td.is_deleted = 0 AND  ncr_att.is_deleted = 0";
                $res_img = mysql_query($q_img);
                while($qImgData = mysql_fetch_array($res_img)){
                    $img = '';
                    if(!empty($qImgData['attachment_file_name'])){
                        $imgFile = "../inspections/ncr_files/".$qImgData['attachment_file_name'];
                        if(file_exists($imgFile)){
                            $img = $imgFile;
                        }
                    }
                    if(!empty($img)){
                        $imageData = explode('.', $qImgData['attachment_file_name']);
                        if(isset($imageData[1]) && !empty($imageData[1]) && ($imageData[1] == 'pdf' || $imageData[1] == 'PDF')){
                            $folder_name = '../inspections/ncr_files/pdf_images/'.$imageData[0];
                            $files_d = glob($folder_name."/*.jpg");
                            if(!empty($files_d)){
                                foreach($files_d as $file){
                                    $taskImageArr[$row['task_name']][]= $file;
                                    //$taskPdfImageArr[$row['task_name']][]= $file;
                                }
                            }
                        }else{
                            $taskImageArr[$row['task_name']][]= $img;
                        }
                    }
                }
            }else if($row['status'] == 'No'){
                $q_img1 = "SELECT qa_graphic_name, qa_graphic_type FROM qa_graphics WHERE task_id = '".$row['task_status_id']."' AND is_deleted = 0";
                $res_img1 = mysql_query($q_img1);
                while($qImgData1 = mysql_fetch_array($res_img1)){
                    $img1 = '';
                    if(!empty($qImgData1['qa_graphic_name'])){
                        $imgFile1 = '';
                        if($qImgData1['qa_graphic_type'] == 'images'){
                            $imgFile1 = "../inspections/photo/".$qImgData1['qa_graphic_name'];
                        }else if($qImgData1['qa_graphic_type'] == 'drawing'){
                            $imgFile1 = "../inspections/drawing/".$qImgData1['qa_graphic_name'];
                        }
                        if(file_exists($imgFile1)){
                            $img1 = $imgFile1;
                        }
                    }
                    if(!empty($img1)){
                        $taskImageArr[$row['task_name']][] = $img1;
                    }
                }
            }

            //  ncr attachment image get START
            $q_img2 = "SELECT ncr_attachment_id, attachment_file_name, attachment_type FROM qa_ncr_attachments WHERE attachment_type = 'checklist_attachment' AND task_detail_id = '".$row['task_status_id']."' AND is_deleted = 0";
            $res_img2 = mysql_query($q_img2);
            while($qImgData2 = mysql_fetch_array($res_img2)){
                $img2 = '';
                if(!empty($qImgData2['attachment_file_name'])){
                    $imgFile2 = "../inspections/ncr_files/".$qImgData2['attachment_file_name'];
                    if(file_exists($imgFile2)){
                        $img2 = $imgFile2;
                    }
                }
                if(!empty($img2)){
                	$imageData1 = explode('.', $qImgData2['attachment_file_name']);
                    if(isset($imageData1[1]) && !empty($imageData1[1]) && ($imageData1[1] == 'pdf' || $imageData1[1] == 'PDF')){
                        $folder_name1 = '../inspections/ncr_files/pdf_images/'.$imageData1[0];
                        $files_d1 = glob($folder_name1."/*.jpg");
                        if(!empty($files_d1)){
                            foreach($files_d1 as $file1){
                                $taskImageArr[$row['task_name']][]= $file1;
                                //$taskPdfImageArr[$row['task_name']][]= $file1;
                            }
                        }
                    }else{
                        $taskImageArr[$row['task_name']][]= $img2;
                    }
                    //$taskImageArr[$row['task_name']][]= $img2;
                }
            }
        }
    }

	$pdf = new PDF('P');
	$y=$pdf->GetY();
	$x=$pdf->GetX();
	$pdf->AddPage();
	$imgPath = "IMAGE##30##20##";
	$imgPath1 = "IMAGE##83##122##";
	$imgPath2 = "IMAGE##178##250##";
	
	$pdf->SetFont('Times','',9);
    $w = $pdf->header_width(array(20,50));
    $pdf->SetWidths($w);
    $printData = array("Date: ",$createdDate);
    $pdf->createTableWithFormating($printData, 50, array('B',''), array('F','F'), array('',''), '', '', 'L'); 

    $pdf->SetFont('Times','',9);
    $w = $pdf->header_width(array(20,50));
    $pdf->SetWidths($w);
    $printData = array("Project: ",$pro_name);
    $pdf->createTableWithFormating($printData, 50, array('B',''), array('F','F'), array('',''), '', '', 'L');

    $pdf->SetFont('Times','',9);
    $w = $pdf->header_width(array(20,50));
    $pdf->SetWidths($w);
    $printData = array("Checklist: ",$checklist_name);
    $pdf->createTableWithFormating($printData, 50, array('B',''), array('F','F'), array('',''), '', '', 'L');

    $pdf->SetFont('Times','',9);
    $w = $pdf->header_width(array(20,120));
    $pdf->SetWidths($w);
    $printData = array("Location: ",$locationName);
    $pdf->createTableWithFormating($printData, 120, array('B',''), array('F','F'), array('',''), '', '', 'L');
    
    $y_val = $pdf->GetY();

	$pdf->SetFont('Arial','B','12');
	$pdf->SetFillColor(252,255,255);
	$pdf->SetTextColor(0);
	$pdf->SetXY(160, 5);
	if(file_exists('../images/logo.png')){ 
		$pdf->Cell(60,20,$pdf->Image('../images/logo.png', $pdf->GetX(), $pdf->GetY()+3, 40, 12,'png'),0,1,'R');
	}
	
    $pdf->SetY($y_val+10);

	$oneColumnArr = array(190);
	$oneBgColorData = array('204, 204, 204');
	$w = $pdf->header_width($oneColumnArr);
	$pdf->SetWidths($w);
	$printData = array("Checklist Task");
	$pdf->createTableWithFormating($printData, 190, array('B'), array('DF'), $oneBgColorData, '', '', 'L');

	$pdf->SetFont('Times','',9);
	$fourColumnArr = array(70,12,20,88);
	$w = $pdf->header_width($fourColumnArr);
	$pdf->SetWidths($w);
	$printData = array("Task","Status","Date","Comment");
	$pdf->createTableWithFormating($printData, 88, array('B','B','B','B'), array('DF','DF','DF','DF'), array('','','',''), '', '', 'C');

	foreach($taskDataArr as $stVal){
		$printData = array($stVal['task_name'],$stVal['status'],$stVal['completion_date'],$stVal['comment']);
		$pdf->createTableWithFormating($printData, 88, array('','','',''), array('DF','DF','DF','DF'), array('','','',''), '', '', 'L');
    }

    $pdf->ln(5);


    $fourColumnArr = array(40,150);
    $w = $pdf->header_width($fourColumnArr);
    $pdf->SetWidths($w);
    $printData = array("Sub Contractor Name :",$subContractorName);
    $pdf->createTableWithFormating($printData, 150, array('B',''), array('DF','DF'), array('',''), '', '', 'L');

    $fourColumnArr = array(40,55,40,55);
    $w = $pdf->header_width($fourColumnArr);
    $pdf->SetWidths($w);
    if(!empty($sc_image)){
    	$sc_image = $imgPath.$sc_image;
    }
    if(!empty($contractor_image)){
    	$contractor_image = $imgPath.$contractor_image;
    }
    $printData = array("Sub Contractor Sign :",$sc_image,"Contractor Sign :",$contractor_image);
	$pdf->createTableWithFormating($printData, 55, array('B','','B',''), array('DF','DF','DF','DF'), array('','','',''), '', '', 'L');

	

	if(!empty($taskImageArr)){
        foreach ($taskImageArr as $key => $value) {
		    $pdf->AddPage();
		    $pdf->SetFont('Times','',10);
		    $w = $pdf->header_width(array(190));
		    $pdf->SetWidths($w);
            $pdf->createTableWithFormating(array($key), 190, array('B'), array('F'), array(''), '', '', 'L');
            $i = 1; $valLen = count($value);
            $arr = array();
        	foreach ($value as $rowData) {
        		$w = $pdf->header_width(array(190));
				$pdf->SetWidths($w);
        		$pdf->createTableWithFormating(array($imgPath2.$rowData), 190, array(''), array('DF'), array(''), '', '', 'L');
            }
        }
    }
	#$pdf->Output(); die;
	$pdf->Output('qa_non_conformance_report.pdf', 'D');
}
?>




