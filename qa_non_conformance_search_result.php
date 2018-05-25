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
    $qiiQuery = 'SELECT qa_issued_to_name, qa_inspection_status FROM qa_issued_to_inspections WHERE non_conformance_id = (SELECT non_conformance_id FROM qa_inspections WHERE task_id ='. $nonConId .' AND project_id ='. $projectId .') AND is_deleted =0';    
    $iResult = mysql_query($qiiQuery) or die(mysql_error());
    $iNumRows = mysql_num_rows($iResult);
    if($iNumRows > 0){
        while($iRows = mysql_fetch_array($iResult)){
            $output['issuedto'][] = $iRows['qa_issued_to_name'];
            $output['status'][] = (!empty($iRows['qa_inspection_status'])) ? $iRows['qa_inspection_status'] : 'Open';
        }
    }
    return $output;
}

if(isset($_REQUEST['name'])){
    $_SESSION['qa'] = $_REQUEST;//Set Session for back implement and Remeber

    $projID = '';
    $where = '';

    $_SESSION['qaChecklistProId'] = '';
    $_SESSION['qaChecklistId'] = '';
    $_SESSION['qaChecklistLocationId'] = '';
    $_SESSION['qaChecklistSubLocationId'] = '';
    $_SESSION['qaChecklistSubLocationId1'] = '';
    $_SESSION['qaChecklistSubLocationId2'] = '';
    
    $aColumns = array('QA.qa_checklist_id', 'QA.created_date', 'QA.location_tree', 'QA.status', 'QA.project_id', 'QATS.task_status_id', 'QAI.qa_inspection_description', 'QATS.task_name', 'QATS.id');

    $sIndexColumn = "QA.project_id";
    $sTable = "qa_checklist as QA";
    $sJOIN ="JOIN `qa_checklist_task_status` as `QATS` ON  `QATS`.`qa_checklist_id` = `QA`.`qa_checklist_id` JOIN `qa_inspections` as `QAI` ON `QAI`.`task_id` = `QATS`.`task_status_id`";
    
    $locArray = array();
    if(!empty($_REQUEST['projId'])){
        $projID = $_REQUEST['projId'];
        $_SESSION['qaChecklistProId'] = $projID;
        $projectName = $_REQUEST['projectNameQA'];
        $where .= ' AND QA.project_id = '.$projID;
    }
    
    if(!empty($_REQUEST['checklist'])){
        $checklist = $_REQUEST['checklist'];        
        $_SESSION['qaChecklistId'] = $checklist;
        if($checklist != 'All'){
            $where .= ' AND QA.project_checklist_id = "'.$checklist.'"';
        }
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

    /*if(!empty($_REQUEST['status'])){
        $sJOIN .= 'JOIN qa_issued_to_inspections as qii on qii.non_conformance_id = QAI.non_conformance_id';
        $iwhere .= " AND qii.qa_inspection_status = '".$_REQUEST['status']."'";
    }*/

    if(!empty($_REQUEST['searchKeyword'])){
        $where .= ' AND QA.location_tree LIKE "%'.$_REQUEST['searchKeyword'].'%" ';
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

    //$sGroup = " "GROUP BY `QA`.`qa_checklist_id` ";

    if ($sWhere == ""){
        $sWhere = " WHERE QA.is_deleted = 0 ".$where;
    }

    $sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
        FROM $sTable
        $sJOIN
        $sWhere 
        $iWhere AND `QATS`.`status` = 'No'
        $sOrder
        $sLimit"; 
    #echo $sQuery; die();
    
    $rResult = $obj->db_query($sQuery ) or die('Mysql Error : '.mysql_error());

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
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );
    
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
    
    while ( $aRow = mysql_fetch_array( $rResult ) ){
        #print_r($aRow);
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
                    } else if ( $aColumns[$i] == 'QA.project_id' ){
                        //$rowID = $aRow[ $aColumns[$i] ];
                        $taskID = $aRow['task_status_id'];
                        $rowID = $qaID;
                        $action = '';
                        $action .= '<img class="action" src="images/view.png" id="viewRevision" title="view checklist" onclick="viewThis('.$taskID.', '.$projID.');" />&nbsp;&nbsp;&nbsp;';
                        $action .= '<img class="action" src="images/edit_right.png" id="editRevision" title="view checklist" onclick="editThis('.$taskID.', '.$projID.');" />&nbsp;&nbsp;&nbsp;';
                    }
                }
                $row[] = getStatusColorCode($status);
                $row[] = $date;
                $row[] = $task_name;
                $row[] = $locationTree;
                //$row[] = $description;
                $row[] = $status;
                $row[] = $issuedto;
                $row[] = $action;
                $output['aaData'][] = $row;
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
                    } else if ( $aColumns[$i] == 'QA.project_id' ){
                        //$rowID = $aRow[ $aColumns[$i] ];
                        $taskID = $aRow['task_status_id'];
                        $rowID = $qaID;
                        $action = '';
                        $action .= '<img class="action" src="images/view.png" id="viewRevision" title="view checklist" onclick="viewThis('.$taskID.', '.$projID.');" />&nbsp;&nbsp;&nbsp;';
                        $action .= '<img class="action" src="images/edit_right.png" id="editRevision" title="view checklist" onclick="editThis('.$taskID.', '.$projID.');" />&nbsp;&nbsp;&nbsp;';
                    }
                }
                $row[] = getStatusColorCode($status);
                $row[] = $date;
                $row[] = $task_name;
                $row[] = $locationTree;
                //$row[] = $description;
                $row[] = $status;
                $row[] = $issuedto;
                $row[] = $action;
                $output['aaData'][] = $row;
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
                    } else if ( $aColumns[$i] == 'QA.project_id' ){
                        //$rowID = $aRow[ $aColumns[$i] ];
                        $taskID = $aRow['task_status_id'];
                        $rowID = $qaID;
                        $action = '';
                        $action .= '<img class="action" src="images/view.png" id="viewRevision" title="view checklist" onclick="viewThis('.$taskID.', '.$projID.');" />&nbsp;&nbsp;&nbsp;';
                        $action .= '<img class="action" src="images/edit_right.png" id="editRevision" title="view checklist" onclick="editThis('.$taskID.', '.$projID.');" />&nbsp;&nbsp;&nbsp;';
                    }
                }
                $row[] = getStatusColorCode($status);
                $row[] = $date;
                $row[] = $task_name;
                $row[] = $locationTree;
                //$row[] = $description;
                $row[] = $status;
                $row[] = $issuedto;
                $row[] = $action;
                $output['aaData'][] = $row;
            }//End of closed.
        } else {
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
                } else if ( $aColumns[$i] == 'QA.project_id' ){
                    //$rowID = $aRow[ $aColumns[$i] ];
                    $taskID = $aRow['task_status_id'];
                    $rowID = $qaID;
                    $action = '';
                    $action .= '<img class="action" src="images/view.png" id="viewRevision" title="view checklist" onclick="viewThis('.$taskID.', '.$projID.');" />&nbsp;&nbsp;&nbsp;';
                    $action .= '<img class="action" src="images/edit_right.png" id="editRevision" title="view checklist" onclick="editThis('.$taskID.', '.$projID.');" />&nbsp;&nbsp;&nbsp;';
                }
            }

            $row[] = getStatusColorCode($status);
            $row[] = $date;
            $row[] = $task_name;
            $row[] = $locationTree;
            //$row[] = $description;
            $row[] = $status;
            $row[] = $issuedto;
            $row[] = $action;
            $output['aaData'][] = $row;
        }
    }   
    //echo "===>>>";  print_r($output); die();
   $output['iTotalRecords'] = count($output['aaData']);
   $output['iTotalDisplayRecords'] = count($output['aaData']);
    echo json_encode( $output );
    
}

function getStatusColorCode($statusData =''){
    $class = "";
    if(isset($statusData) && !empty($statusData)){
        $statusArr = explode(' > ', $statusData);
        if(in_array('Open', $statusArr)){
            $class = 'colOpen';
        }
        elseif(in_array('Fixed', $statusArr)){
            $class = 'colFixed';
        }
        elseif(in_array('Close', $statusArr)){
            $class =  'colClosed';
        }
    }
    return $class;
}

?>
