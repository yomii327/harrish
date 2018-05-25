<?php session_start();
require_once'includes/functions.php';
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$owner_id = $_SESSION['ww_builder_id'];

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

if(isset($_REQUEST['name'])){
    #echo "<pre>"; print_r($_REQUEST); die;
    $_SESSION['qa'] = $_REQUEST;//Set Session for back implement and Remeber

    $projID = '';
    $where = '';

    $_SESSION['qaChecklistProId'] = '';
    $_SESSION['qaChecklistId'] = '';
    $_SESSION['qaChecklistLocationId'] = '';
    $_SESSION['qaChecklistSubLocationId'] = '';
    $_SESSION['qaChecklistSubLocationId1'] = '';
    $_SESSION['qaChecklistSubLocationId2'] = '';
    
    $aColumns = array('QA.qa_checklist_id', 'QA.created_date', 'QA.location_tree', 'QA.status', 'QA.project_id', 'QA.project_checklist_id', 'QATS.task_status_id', 'QAI.qa_inspection_description', 'QATS.task_name', 'QATS.id','QAI.non_conformance_id','QAI.qa_inspection_inspected_by','QAI.qa_inspection_raised_by');

    $_SESSION['aColumns'] = $aColumns;

    $sIndexColumn = "QA.project_id";
    $sTable = "qa_checklist as QA";
    $sJOIN ="JOIN qa_checklist_task_status as QATS ON  QATS.qa_checklist_id = QA.qa_checklist_id JOIN qa_inspections as QAI ON QAI.task_id = QATS.task_status_id";
    
    $locArray = array();
    if(!empty($_REQUEST['projName'])){
        $projID = $_REQUEST['projName'];
        $_SESSION['qaChecklistProId'] = $projID;
        $projectName = $_REQUEST['projectNameQA'];
        $where .= ' AND QA.project_id = '.$projID;
    }
    
    if(!empty($_REQUEST['checklist'])){
        $checklist = $_REQUEST['checklist'];
        $_SESSION['qaChecklistId'] = $checklist;
        $where .= ' AND QA.project_checklist_id = "'.$checklist.'"';
    }

    if(!empty($_REQUEST['location'])){
        $locArray[] = $_REQUEST['location'];
        $location = $_REQUEST['location'];
        $_SESSION['qaChecklistLocationId'] = $location;
        $where .= ' AND QA.location_id = "'.$location.'"';
    }

    if(!empty($_REQUEST['subLocation'])){
        $locArray[] = $_REQUEST['subLocation'];
        $subLocation1 = $_REQUEST['subLocation'];
        $_SESSION['qaChecklistSubLocationId'] = $subLocation1;
        $where .= ' AND QA.sub_location_id = "'.$subLocation1.'"';
    }
    
    if(!empty($_REQUEST['sub_subLocation'])){
        $locArray[] = $_REQUEST['sub_subLocation'];
        $subLocation2 = $_REQUEST['sub_subLocation'];
        $_SESSION['qaChecklistSubLocationId1'] = $subLocation2;
        $where .= ' AND QA.sub_location1_id = "'.$subLocation2.'"';
    }
    
    if(!empty($_REQUEST['subSubLocation3'])){
        $locArray[] = $_REQUEST['subSubLocation3'];
        $subLocation3 = $_REQUEST['subSubLocation3'];
        $_SESSION['qaChecklistSubLocationId2'] = $subLocation3;
        $where .= ' AND QA.sub_location2_id = "'.$subLocation3.'"';
    }

    if(!empty($_REQUEST['status'])){
        $_SESSION['nonConfStatus'] = $_REQUEST['status'];
        //$sJOIN .= 'JOIN qa_issued_to_inspections as qii on qii.non_conformance_id = QAI.non_conformance_id';
        //$iwhere .= " AND qii.qa_inspection_status = '".$_REQUEST['status']."'";
    }
    
    if(!empty($_REQUEST['issuedTo'])){
        $_SESSION['nonConfIssuedTo'] = $_REQUEST['issuedTo'];
        $sJOIN .= ' JOIN qa_issued_to_inspections as qii on qii.non_conformance_id = QAI.non_conformance_id';
        $where .= " AND qii.qa_issued_to_name Like '%".trim($_REQUEST['issuedTo'])."%'";
    }

    if(isset($_REQUEST['searchKeyword']) && !empty($_REQUEST['searchKeyword'])){
        $where .= ' AND QA.location_tree LIKE "%'.$_REQUEST['searchKeyword'].'%" ';
    }

    if(isset($_REQUEST['raisedBy']) && !empty($_REQUEST['raisedBy'])){
        $where .= ' AND QAI.qa_inspection_raised_by = "'.$_REQUEST['raisedBy'].'"';
    }

    $sLimit = "";   
    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
        $sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
            mysql_real_escape_string( $_GET['iDisplayLength'] );
    }
    
    if ( isset( $_GET['iSortCol_0'] ) ){
        $sOrder = "ORDER BY  ";

        $sOrder = substr_replace( $sOrder, "", -2 );

        if ( $sOrder == "ORDER BY" ){
            $sOrder = "ORDER BY QA.created_date DESC";
        }
    }
    
    $sWhere = "";
    if ( $_GET['sSearch'] != "" ){
        $sWhere = "WHERE QA.is_deleted=0 AND QA.project_id=".$projID." AND (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ ){
            $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
    }
    
    for ( $i=0 ; $i<count($aColumns) ; $i++ ){
        if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ){
            if ( $sWhere == "" ){
                $sWhere = "WHERE QA.is_deleted = 0 AND QA.project_id = ".$projID." AND ";
            }else{
                $sWhere .= " AND ";
            }
            $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
        }
    }

    //$sGroup = " "GROUP BY QA.qa_checklist_id ";

    if ($sWhere == ""){
        $sWhere = " WHERE QA.is_deleted = 0 ".$where;
    }

    $_SESSION['nonConfWhere'] = $sWhere;

    $sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
        FROM $sTable
        $sJOIN
        $sWhere 
        $iWhere AND QATS.status='No'
        $sOrder
        $sLimit"; 
    #echo $sQuery; die();
    
    $_SESSION['non_conformance_query'] = $sQuery;

    $rResult = $obj->db_query($sQuery ) or die('Mysql Error : '.mysql_error().'<br/><br/>'. $sQuery);

    $rStatus = '';
    $chkStatus = 0;
    if(!empty($_REQUEST['status'])){
        $rStatus = $_REQUEST['status'];
        if($rStatus=='Open'){
            $chkStatus = 1;
        } elseif($rStatus=='Close'){
            $chkStatus = 2;
        } elseif($rStatus=='Fixed'){
            $chkStatus = 3;
        } 
    }
    
    $queryCount = "SELECT count(I.inspection_id) FROM user_projects as P, issued_to_for_inspections as F, project_inspections as I WHERE I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.is_deleted = '0' and F.is_deleted = '0' and P.is_deleted = '0' $sWhere group by I.inspection_id";

    $resCount=mysql_query($queryCount);
    if(mysql_num_rows($resCount) > 0){
        $totalCount = mysql_num_rows($resCount);
    }

    $noInspection = mysql_num_rows($rResult);
    $ajaxReplay = $noInspection.' Records';
    $noPages = ceil(($noInspection-14)/17 +1);


    $proName = $object->getDataByKey('user_projects', 'project_id', $_REQUEST['projName'], 'project_name');
    $_SESSION["nonConfProName"]=$proName;
    
    $htmlHeadVal ='<table width="100%"><tr><td width="40%"></td><td width="70%" align="right" style="padding-right:20px;"><img src="company_logo/logo.png" height="50"  /></td>
                </tr><tr><td width="40%" style="font-size:14px;"><u><b>Non Conformance Report</b></u></td><td>&nbsp;</td></tr><tr><td style="font-size:14px;"><strong>Project Name : </strong>'.$proName.'</td><td>&nbsp;</td></tr><tr><td style="font-size:14px;"><strong>Date : </strong>'.date('d / m / Y').'</td><td>&nbsp;</td></tr><tr><td style="font-size:14px"><strong>Inspections: </strong>'.$noInspection.'</td><td>&nbsp;</td></tr><tr><td style="font-size:14px;"><strong>Page : </strong>1 of '.$noPages.'</td><td>&nbsp;</td></tr></table>';

    $htmlHead ='<table width="98%" cellpadding="0" cellspacing="0" align="center" class="collapse">
        <tr><td widht="3%" style="font-size:10px" align="center">ID</td>
            <td width="12%" style="font-size:10px;" align="center"><strong>Location</strong></td>
            <td width="12%" style="font-size:10px;" align="center"><strong>Quality Checklist</strong></td>
            <td width="15%" style="font-size:10px;" align="center"><strong>Description</strong></td>
            <td width="6%" style="font-size:10px;" align="center"><strong>Inspected By</strong></td>
            <td width="6%" style="font-size:10px;" align="center"><strong>Date Raised</strong></td>
            <td width="6%" style="font-size:10px;" align="center"><strong>Raised&nbsp;By</strong></td>
            <td width="12%" style="font-size:10px;" align="center"><strong>Issued&nbsp;To</strong></td>
            <td width="10%" style="font-size:10px;" align="center"><strong>Fix By Date</strong></td>
            <td width="10%" style="font-size:10px;" align="center"><strong>Status</strong></td>
            <td width="10%" style="font-size:10px;" align="center"><strong>Image 1</strong></td>
            <td width="10%" style="font-size:10px;" align="center"><strong>Image 2</strong></td></tr>';

    $htmlHeadAppend ='<tr><td widht="3%" style="font-size:10px" align="center">ID</td>
            <td width="12%" style="font-size:10px;" align="center"><strong>Location</strong></td>
            <td width="15%" style="font-size:10px;" align="center"><strong>Description</strong></td>
            <td width="6%" style="font-size:10px;" align="center"><strong>Inspected By</strong></td>
            <td width="6%" style="font-size:10px;" align="center"><strong>Date Raised</strong></td>
            <td width="6%" style="font-size:10px;" align="center"><strong>Raised&nbsp;By</strong></td>
            <td width="12%" style="font-size:10px;" align="center"><strong>Issued&nbsp;To</strong></td>
            <td width="10%" style="font-size:10px;" align="center"><strong>Fix By Date</strong></td>
            <td width="10%" style="font-size:10px;" align="center"><strong>Status</strong></td>
            <td width="10%" style="font-size:10px;" align="center"><strong>Image 1</strong></td>
            <td width="10%" style="font-size:10px;" align="center"><strong>Image 2</strong></td></tr>';
    $dataCount = 0;
    while ( $aRow = mysql_fetch_array( $rResult ) ){
        #print_r($aRow);
        $non_conf_id = $aRow['non_conformance_id'];
        $atdata = $object->selQRYMultiple('qa_graphic_name,qa_graphic_id', 'qa_graphics', 'is_deleted = 0 AND non_conformance_id = '.$non_conf_id);
        $image1 = ''; $image2 = '';
        if(isset($atdata[0]['qa_graphic_name']) && !empty($atdata[0]['qa_graphic_name'])){
            $image1 = 'inspections/photo/'.$atdata[0]['qa_graphic_name'];
            if(!file_exists($image1)){
                $image1 = '';
            }
        }
        if(isset($atdata[1]['qa_graphic_name']) && !empty($atdata[1]['qa_graphic_name'])){
            $image2 = 'inspections/drawing/'.$atdata[1]['qa_graphic_name'];
            if(!file_exists($image2)){
                $image2 = '';
            }
        }
        $qualityChecklist = '';
        $statusArr = nonConfromanceIssuedto($aRow['task_status_id'], $aRow['project_id']);
        if(isset($rStatus) && !empty($rStatus)){
            if($chkStatus == 1 && in_array('Open', $statusArr['status'])){
                $dataCount++;
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
                $row[] = $non_conf_id;
                $row[] = $locationTree;
                $row[] = $qualityChecklist;
                $row[] = $description;
                $row[] = $inspectedBy;
                $row[] = $date;
                $row[] = $raisedBy;
                $row[] = $issuedto;
                $row[] = $fixedByDate;
                $row[] = $status;
                $row[] = $image1;
                $row[] = $image2;
                $img1 = $img2 = '';
                if(!empty($image1)){
                    $img1 = '<img src="'.$image1.'" style="width:110px;" />';
                }
                if(!empty($image2)){
                    $img2 = '<img src="'.$image2.'" style="width:110px;" />';
                }

                $htmlData .='<tr><td><font style="font-size:10px;">'.$non_conf_id.'</font></td>
                <td><font style="font-size:10px;">'.$locationTree.'</font></td>
                <td><font style="font-size:10px;">'.$qualityChecklist.'</font></td>
                <td><font style="font-size:10px;">'.$description.'</font></td>
                <td><font style="font-size:10px;">'.$inspectedBy.'</font></td>
                <td><font style="font-size:10px;">'.$date.'</font></td>
                <td><font style="font-size:10px;">'.$raisedBy.'</font></td>
                <td><font style="font-size:10px;">'.$issuedto.'</font></td>
                <td><font style="font-size:10px;">'.$fixedByDate.'</font></td>
                <td><font style="font-size:10px;">'.$status.'</font></td>
                <td valign="middle" style="height:50px;">'.$img1.'</td>
                <td valign="middle" style="height:50px;">'.$img2.'</td></tr>';
                /*
                $row[] = $task_name;
                */
                $output['aaData'][] = $row;
            }//End of opne
            if($chkStatus == 2 && in_array('Close', $statusArr['status'])){
                $dataCount++;
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
                $row[] = $non_conf_id;
                $row[] = $locationTree;
                $row[] = $qualityChecklist;
                $row[] = $description;
                $row[] = $inspectedBy;
                $row[] = $date;
                $row[] = $raisedBy;
                $row[] = $issuedto;
                $row[] = $fixedByDate;
                $row[] = $status;
                $row[] = $image1;
                $row[] = $image2;
                $img1 = $img2 = '';
                if(!empty($image1)){
                    $img1 = '<img src="'.$image1.'" style="width:110px;" />';
                }
                if(!empty($image2)){
                    $img2 = '<img src="'.$image2.'" style="width:110px;" />';
                }

                $htmlData .='<tr><td><font style="font-size:10px;">'.$non_conf_id.'</font></td>
                <td><font style="font-size:10px;">'.$locationTree.'</font></td>
                <td><font style="font-size:10px;">'.$qualityChecklist.'</font></td>
                <td><font style="font-size:10px;">'.$description.'</font></td>
                <td><font style="font-size:10px;">'.$inspectedBy.'</font></td>
                <td><font style="font-size:10px;">'.$date.'</font></td>
                <td><font style="font-size:10px;">'.$raisedBy.'</font></td>
                <td><font style="font-size:10px;">'.$issuedto.'</font></td>
                <td><font style="font-size:10px;">'.$fixedByDate.'</font></td>
                <td><font style="font-size:10px;">'.$status.'</font></td>
                <td valign="middle" style="height:50px;">'.$img1.'</td>
                <td valign="middle" style="height:50px;">'.$img2.'</td></tr>';
                /*
                $row[] = $task_name;
                */
                $output['aaData'][] = $row;
            }//End of closed.
            if($chkStatus == 3 && in_array('Fixed', $statusArr['status'])){
                $dataCount++;
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
                $row[] = $non_conf_id;
                $row[] = $locationTree;
                $row[] = $qualityChecklist;
                $row[] = $description;
                $row[] = $inspectedBy;
                $row[] = $date;
                $row[] = $raisedBy;
                $row[] = $issuedto;
                $row[] = $fixedByDate;
                $row[] = $status;
                $row[] = $image1;
                $row[] = $image2;
                $img1 = $img2 = '';
                if(!empty($image1)){
                    $img1 = '<img src="'.$image1.'" style="width:110px;" />';
                }
                if(!empty($image2)){
                    $img2 = '<img src="'.$image2.'" style="width:110px;" />';
                }

                $htmlData .='<tr><td><font style="font-size:10px;">'.$non_conf_id.'</font></td>
                <td><font style="font-size:10px;">'.$locationTree.'</font></td>
                <td><font style="font-size:10px;">'.$qualityChecklist.'</font></td>
                <td><font style="font-size:10px;">'.$description.'</font></td>
                <td><font style="font-size:10px;">'.$inspectedBy.'</font></td>
                <td><font style="font-size:10px;">'.$date.'</font></td>
                <td><font style="font-size:10px;">'.$raisedBy.'</font></td>
                <td><font style="font-size:10px;">'.$issuedto.'</font></td>
                <td><font style="font-size:10px;">'.$fixedByDate.'</font></td>
                <td><font style="font-size:10px;">'.$status.'</font></td>
                <td valign="middle" style="height:50px;">'.$img1.'</td>
                <td valign="middle" style="height:50px;">'.$img2.'</td></tr>';
                /*
                $row[] = $task_name;
                */
                $output['aaData'][] = $row;
            }//End of closed.
        } else if(empty($rStatus)){
            $dataCount++;
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
            $row[] = $non_conf_id;
            $row[] = $locationTree;
            $row[] = $qualityChecklist;
            $row[] = $description;
            $row[] = $inspectedBy;
            $row[] = $date;
            $row[] = $raisedBy;
            $row[] = $issuedto;
            $row[] = $fixedByDate;
            $row[] = $status;
            $row[] = $image1;
            $row[] = $image2;
            $img1 = $img2 = '';
            if(!empty($image1)){
                $img1 = '<img src="'.$image1.'" style="width:110px;" />';
            }
            if(!empty($image2)){
                $img2 = '<img src="'.$image2.'" style="width:110px;" />';
            }

            $htmlData .='<tr><td><font style="font-size:10px;">'.$non_conf_id.'</font></td>
            <td><font style="font-size:10px;">'.$locationTree.'</font></td>
            <td><font style="font-size:10px;">'.$qualityChecklist.'</font></td>
            <td><font style="font-size:10px;">'.$description.'</font></td>
            <td><font style="font-size:10px;">'.$inspectedBy.'</font></td>
            <td><font style="font-size:10px;">'.$date.'</font></td>
            <td><font style="font-size:10px;">'.$raisedBy.'</font></td>
            <td><font style="font-size:10px;">'.$issuedto.'</font></td>
            <td><font style="font-size:10px;">'.$fixedByDate.'</font></td>
            <td><font style="font-size:10px;">'.$status.'</font></td>
            <td valign="middle" style="height:50px;">'.$img1.'</td>
                <td valign="middle" style="height:50px;">'.$img2.'</td></tr>';
            
            $output['aaData'][] = $row;
        }
    }

    $htmlHeadAppend ='<tr><td widht="3%" style="font-size:10px" align="center">ID</td>
            <td width="12%" style="font-size:10px;" align="center"><strong>Location</strong></td>
            <td width="12%" style="font-size:10px;" align="center"><strong>Quality Checklist</strong></td>
            <td width="15%" style="font-size:10px;" align="center"><strong>Description</strong></td>
            <td width="6%" style="font-size:10px;" align="center"><strong>Inspected By</strong></td>
            <td width="6%" style="font-size:10px;" align="center"><strong>Date Raised</strong></td>
            <td width="6%" style="font-size:10px;" align="center"><strong>Raised&nbsp;By</strong></td>
            <td width="12%" style="font-size:10px;" align="center"><strong>Issued&nbsp;To</strong></td>
            <td width="10%" style="font-size:10px;" align="center"><strong>Fix By Date</strong></td>
            <td width="10%" style="font-size:10px;" align="center"><strong>Status</strong></td>
            <td width="10%" style="font-size:10px;" align="center"><strong>Image 1</strong></td>
            <td width="10%" style="font-size:10px;" align="center"><strong>Image 2</strong></td></tr>';

    $html .= $htmlHeadVal.$htmlHead.$htmlData.'</table>';


    
   

    //$commandString = 'wkhtmltopdf file:///home/fxbytes/Desktop/i_report_nonconf.php.html  test147.pdf';

    //$output = shell_exec($commandString);

    #echo "<pre>"; print_r($output['aaData']); die();
?>
    <div id="mainContainer" style="width: 100% !important">
        <?php if($dataCount>0){ ?>
        <div class="buttonDiv">
            <img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
            <img onClick="sendEmailViewPDFNonConf(<?php echo $_REQUEST['projName']; ?>)" src="images/email.png" style="float:left;margin-left:160px;" />
            <!-- <img onClick="downloadNonConfPDF();"src="images/download_btn.png" style="float:right;" /> -->
            <a href="pdf/i_report_nonconf_pdf.php?nonConf=1&addInEmail=0&projID=<?php echo $_REQUEST['projName']; ?>">
                <img alt="Print Screen" src="images/download_btn.png" style="float:right;margin:0px 0px 0 0;cursor:pointer;width: 125px;height: 55px;">
            </a>
        </div>
        
        <br clear="all" />
        <?php $pageCount = $totalCount / $limit;?>
        <div class="pagination" <?php if($totalCount <= $limit){ echo 'style="display:none;" ';}?> >
        <?php $leftLimit = $offset - $limit;
            if($leftLimit < 0){ $leftLimit = 0; }else{ }?>
            <img id="previousImages" src="images/prev_icon.png" onclick="pageScroll(<? echo $leftLimit;?> );"
            <?php if($leftLimit < 0){ echo 'style="display:none;float:left;"'; }else{ echo 'style="float:left"';} ?> />
            <?php if($pageCount > 0){
                for($l=0; $l<$pageCount; $l++){?>
                    <span <? if(($l*$limit) == $offset){
                        echo 'class="page_active" ';
                    }else{
                        echo 'class="page_deactive" ';
                    }
                    if($l >= 5){
                        echo 'style="display:none;" ';
                    }
                    ?>
                    onclick="pageScroll(<?php echo ($l*$limit); ?>)" ><?php echo ($l+1)?></span>
                <?php   }
                if($l >= 5){ ?>
                    <span><strong>.</strong></span>
                    <span><strong>.</strong></span>
                    <span><strong>.</strong></span>
                <? }
            }
            $rightLimit = $offset + $limit;
            if($rightLimit >= $totalCount){ $rightLimit = $totalCount; }else{ } ?>
            <img id="nextImages" src="images/next_icon.png" onclick="pageScroll(<?php echo $rightLimit;?>);"
            <?php if($rightLimit > $totalCount){ echo 'style="margin-left:5px;display:none;"'; }else{ echo 'style="margin-left:5px;"'; }?> />
        </div><br /><br /><br />
        <div id="mainDivForPrint">
            <?php echo $html;?>
        </div>
        <?php $pageCount = $totalCount / $limit;?>
        <div class="pagination" <?php if($totalCount <= $limit){ echo 'style="display:none;" ';}?> >
            <?php $leftLimit = $offset - $limit;
            if($leftLimit < 0){ $leftLimit = 0; }else{ }?>
            <img id="previousImages" src="images/prev_icon.png" onclick="pageScroll(<? echo $leftLimit;?> );"
            <?php if($leftLimit < 0){ echo 'style="display:none;float:left;"'; }else{ echo 'style="float:left"';} ?> />
            <?php if($pageCount > 0){
            for($l=0; $l<$pageCount; $l++){?>
                <span <? if(($l*$limit) == $offset){
                    echo 'class="page_active" ';
                }else{
                    echo 'class="page_deactive" ';
                }
                if($l >= 5){
                    echo 'style="display:none;" ';
                }
                ?>
                onclick="pageScroll(<?php echo ($l*$limit); ?>)" ><?php echo ($l+1)?></span>
                <?php   }
                if($l >= 5){ ?>
                    <span><strong>.</strong></span>
                    <span><strong>.</strong></span>
                    <span><strong>.</strong></span>
                <? }
            }
            $rightLimit = $offset + $limit;
            if($rightLimit >= $totalCount){ $rightLimit = $totalCount; }else{ } ?>
            <img id="nextImages" src="images/next_icon.png" onclick="pageScroll(<?php echo $rightLimit;?>);"
            <?php if($rightLimit > $totalCount){ echo 'style="margin-left:5px;display:none;"'; }else{ echo 'style="margin-left:5px;"'; }?> />
        </div><br clear="all" />
        <?php }else{?>
            <div style="margin-left:10px;color:#000;"><i>No Record Found</i></div>
        <?php }?>
    </div>
<?php }else{?>
    <div style="margin-left:10px;color:#000;"><i>No Record Found</i></div>
<? }?>
