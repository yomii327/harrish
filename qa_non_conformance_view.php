<style type="text/css">
    .innerDiv {
        display: block;
        background-color: rgba(182, 255, 0, 0.13);
        border: 1px solid black;
        width: 120px;
        height: 120px;
        margin: 0;
        padding: 0;
    }
</style>

<?php session_start(); 
require_once'includes/functions.php';
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$owner_id = $_SESSION['ww_builder_id'];
$stArr = array('Yes', 'No', 'NA');
if(isset($_REQUEST['name'])){//Set Session for back implement and Remeber   
    
    $query = "SELECT project_name FROM projects WHERE project_id = '".$_REQUEST['projID']."' AND  is_deleted = 0";
    $q_data = mysql_query($query);
    while($pro_data = mysql_fetch_array($q_data)){
        $project_name = $pro_data['project_name'];
    }

    $q = "SELECT qc.id as qa_id,qc.qa_checklist_id, qc.location_tree, qc.sc_sign, qc.contractor_sign, qcTask.task_name, qcTask.task_comment, qcTask.status, qcTask.completion_date, qcTask.task_status_id, qc.sub_contractor_name FROM qa_checklist AS qc JOIN qa_checklist_task_status AS qcTask ON qcTask.qa_checklist_id = qc.qa_checklist_id WHERE qc.qa_checklist_id = '".$_REQUEST['qaChecklistId']."' AND qc.is_deleted = 0 AND  qcTask.is_deleted = 0 AND qcTask.status = 'No'";
    
    $res = mysql_query($q);
    $qaChecklistId = $_REQUEST['qaChecklistId'];
    $projID = $_REQUEST['projID'];
    while($qData = mysql_fetch_array($res)){
        $locationName = $qData['location_tree'];
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
            'task_name' => $qData['task_name'],
            'comment_mandatory' => $qData['comment_mandatory'],
            'comment' => $qData['task_comment'],
            'completion_date' => $dat,
            'status' => $qData['status']     //$qData['task_status'],
        );
    }

    $sc_image = '';
    $contractor_image = '';
    if(!empty($sc_sign)){
        $sc_image_path = "inspections/ncr_files/".$sc_sign;
        if(file_exists($sc_image_path)){
            $sc_image = $sc_image_path;
        }
    }

    if(!empty($contractor_sign)){
        $contractor_image_path = "inspections/ncr_files/".$contractor_sign;
        if(file_exists($contractor_image_path)){
            $contractor_image = $contractor_image_path;
        }
    }

    if(!empty($taskDataArr)){
        $taskImageArr = array();
        foreach ($taskDataArr as $row) {
            if($row['status'] == 'Yes'){
                $q_img = "SELECT ncr_att.ncr_attachment_id, ncr_att.attachment_file_name, ncr_att.attachment_type FROM qa_ncr_task_detail AS td JOIN qa_ncr_attachments AS ncr_att ON ncr_att.task_detail_id = td.task_detail_id WHERE (ncr_att.attachment_type = 'evidence_image' OR ncr_att.attachment_type = 'evidence_pdf') AND td.task_id = '".$row['task_status_id']."' AND td.is_deleted = 0 AND  ncr_att.is_deleted = 0";
                $res_img = mysql_query($q_img);
                while($qImgData = mysql_fetch_array($res_img)){
                    $img = '';
                    if(!empty($qImgData['attachment_file_name'])){
                        $imgFile = "inspections/ncr_files/".$qImgData['attachment_file_name'];
                        if(file_exists($imgFile)){
                            $img = $imgFile;
                        }
                    }
                    if(!empty($img)){
                        $imageData = explode('.', $qImgData['attachment_file_name']);
                        if(isset($imageData[1]) && !empty($imageData[1]) && ($imageData[1] == 'pdf' || $imageData[1] == 'PDF')){
                            $folder_name = 'inspections/ncr_files/pdf_images/'.$imageData[0];
                            $files_d = glob($folder_name."/*.jpg");
                            if(!empty($files_d)){
                                foreach($files_d as $file){
                                    $taskImageArr[$row['task_name']][]= $file;
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
                            $imgFile1 = "inspections/photo/".$qImgData1['qa_graphic_name'];
                        }else if($qImgData1['qa_graphic_type'] == 'drawing'){
                            $imgFile1 = "inspections/drawing/".$qImgData1['qa_graphic_name'];
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
                    $imgFile2 = "inspections/ncr_files/".$qImgData2['attachment_file_name'];
                    if(file_exists($imgFile2)){
                        $img2 = $imgFile2;
                    }
                }
                if(!empty($img2)){
                    $taskImageArr[$row['task_name']][]= $img2;
                }
            }
            //  ncr attachment image get START
        }
    }
    //echo $project_name ."=====" .$locationName. "<pre>"; print_r($taskImageArr); die();

    if(!empty($taskDataArr)){?>
    <div style="color:black;" id="qa-view-checklist">
        <ul class="buttonHolder">
            <a alt="Print Screen" class="green_small" style="float:right;margin:15px 15px 0 0;cursor:pointer;" onclick="javascript:printDiv();">Print</a>
            <a href="pdf/qa_non_conformance_pdf.php?qaChecklistId=<?php echo $_REQUEST['qaChecklistId'] . '&projID=' . $_REQUEST['projID']; ?>" alt="Print Screen" class="green_small" style="float:right;margin:15px 15px 0 0;cursor:pointer;">Download
            </a>
        </ul><br clear="all">
        <div id="mainDivForPrint">
            <div style="float:left;">
                <ul style="list-style:none;">
                    <li><h3><?php echo $checkListName;?></h3></li>
                    <li><strong>Project: </strong><?php echo $project_name;?></li>
                    <li><strong>Location: </strong><?php echo $locationName;?></li>
                </ul>
            </div>
            
            <div style="float:right;">
                <ul style="list-style:none;">
                    <li style="margin-bottom:10px;"><img width="210" height="60" src="images/logo.png"></li>
                    <!--li><strong>User Name: </strong>< ?php echo $chekListData[0]['user_fullname'];?></li -->
                    <!-- li><strong>Date: </strong>< ?php echo date('d/m/Y', strtotime($chekListData[0]['created_date']));?></li -->
                </ul>
            </div>
            <br clear="all">
            <?php #echo '<pre>';#print_r($checklistArr);die;?>
            <table width="99%" border="1" cellspacing="0" cellpadding="0" style="color:black;">
                <tr>
                    <th><strong>Task</strong></th>
                    <th><strong>Status</strong></th>
                    <th><strong>Date</strong></th>
                    <th><strong>Comment</strong></th>
                </tr>
                <?php foreach($taskDataArr as $stVal){?>
                <tr>
                    <td width="40%"><?=$stVal['task_name']?></td>
                    <td width="5%" style="text-align: center;"><?=$stVal['status']?></td>
                    <td width="10%" style="text-align: center;"><?=$stVal['completion_date']?></td>
                    <td width="45%"><?=$stVal['comment']?></td>
                </tr>
            <?php }
            ?>
            </table>
            <br/><br/>
            <table width="100%">
                <tr>
                    <td width="20%">Sub Contractor Name :</td>
                    <td width="35%">
                        <?php echo $subContractorName; ?>
                    </td>
                </tr>
                <tr>
                    <td width="15%">Sub Contractor Sign :</td>
                    <td width="35%">
                        <div class="innerDiv" style="border: 1px solid black; width: 120px; height: 120px;">
                            <?php if(!empty($sc_image)){?>
                            <img id="response_1" src="<?php echo $sc_image;?>" width="120" height="120">
                            <?php }?>
                        </div>
                    </td>
                    <td width="15%">Contractor Sign :</td>
                    <td width="35%">
                        <div class="innerDiv" style="border: 1px solid black; width: 120px; height: 120px;">
                            <?php if(!empty($contractor_image)){?>
                                <img id="response_1" src="<?php echo $contractor_image;?>" width="120" height="120">
                            <?php }?>
                        </div>
                    </td>
                </tr>
            </table>
            <br/>
            <style>
                ul#view_attachment{margin: 0; padding: 0;}
                ul#view_attachment li {display: inline-block; height: 180px; list-style: outside none none; margin: 15px 20px; padding: 0; width: 280px; border: 1px solid #000;}
                ul#view_attachment li > img { width: 100%; height: 180px; }

                ul#view_attachment1{margin: 0; padding: 0}
                ul#view_attachment1 li {display: inline-block; height: 80px; list-style: outside none none; margin: 15px 20px; padding: 0; width: 100px; border: 1px solid #000;}
                ul#view_attachment1 li > img { width: 100%; height: 80px; }

            </style>
            <?php if(!empty($taskImageArr)){ ?>
                <?php foreach ($taskImageArr as $key => $value) { ?>
                    <hr/>
                    <h3><?php echo $key; ?></h3>
                    <ul id="view_attachment" style="margin-left: 30px;">
                        <?php foreach ($value as $rowData) { ?>
                            <li>
                                <img src='<?php echo $rowData; ?>' alt="image" />
                            </li>
                        <?php }?>
                    </ul>
                <?php }?>
            <?php }?>

        </div>
		<br clear="all" />
    </div>
<?php }else{
        echo '<span style="color:#000">No Record Found !</span>';
    }
}

if(isset($_REQUEST['type'])){
    $qaChecklistId = $_REQUEST['qaChecklistId'];
    $projID = $_REQUEST['projectId'];
    $uQuery = 'UPDATE qa_checklist SET is_deleted = 1, last_modified_by = '.$_SESSION['ww_builder_id'].', last_modified_date = NOW(), original_modified_date = NOW() WHERE qa_checklist_id = '. $qaChecklistId .' AND project_id = '.$projID;
    $res = mysql_query($uQuery);
    echo true; die;
}

?>