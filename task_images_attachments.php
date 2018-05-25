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
    .uploadArea{ min-height:300px; height:auto; border:1px dotted #ccc; padding:10px; cursor:move; margin-bottom:10px; position:relative;}

    .uploadArea h1{
        color:#ccc; width:100%; z-index:0; text-align:center; vertical-align:middle; position:absolute; top:25px;
    }

    h5{
        background-color: #ddd;
        line-height: 26px;
        font-size: 13px;
        margin: 2px 0;
        padding: 4px 2px;
        width: 99%;
    }
    h5, h5 img {  float:left;  margin-right: 5px;   padding: 5px; }
    h5 img.close { float:  right; width:16px; height: 16px; display: none;}

    .buttonUpload { display:inline-block; padding: 4px 10px 4px; text-align: center; text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25); background-color: #0074cc; -webkit-border-radius: 4px;-moz-border-radius: 4px; border-radius: 4px; border-color: #e6e6e6 #e6e6e6 #bfbfbf; border: 1px solid #cccccc; color:#fff; float: right; }

    .progress img{ margin-top:7px; margin-left:24px; }


    #demoFiler1 > input {
        width: 95px;
    }
    .btn-upload {
		float: left;
		position: relative;
		/*width: 70px;*/
	}
	span#btnMultiUpload {
		background-color: #E9E9E9;
		border: 1px solid #888888;
		border-radius: 5px;
		padding: 4px 12px;
        cursor: pointer;
        position: absolute;
	}
    input[type='file']#multiUpload {
		cursor: pointer;
		left: 0;
		opacity: 0;
		position: absolute;
		top: 0;
		width: 70px;
	}
</style>

<?php session_start(); 
require_once'includes/functions.php';
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$owner_id = $_SESSION['ww_builder_id'];
//echo $_REQUEST['task_id'] . '<<=====' .$_REQUEST['checked_val'].'<<====='; die();
if(isset($_REQUEST['task_id'])){//Set Session for back implement and Remeber
    //echo "<pre>"; print_r($_SESSION['deleteImgData']); die();
    $_SESSION['deleteImgData'] = array();
    $_SESSION['selected_task_id'] = $_REQUEST['task_id'];
    $taskImageArr = array();
    $taskImageTableName = array();
    $taskImageId = array();
    if($_REQUEST['checked_val'] == 'Yes'){
        $q_img = "SELECT ncr_att.ncr_attachment_id, ncr_att.attachment_file_name, ncr_att.attachment_type FROM qa_ncr_task_detail AS td JOIN qa_ncr_attachments AS ncr_att ON ncr_att.task_detail_id = td.task_detail_id WHERE ncr_att.attachment_type = 'evidence_image' AND td.task_id = '".$_REQUEST['task_id']."' AND td.is_deleted = 0 AND  ncr_att.is_deleted = 0";
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
                $taskImageArr[]= $img;
                $taskImageTableName[] = 'qa_ncr_attachments';
                $taskImageId[] = $qImgData['ncr_attachment_id'];
            }
        }
        //echo "===>> <pre>"; print_r($taskImageId); die();
    }else if($_REQUEST['checked_val'] == 'No'){
        $q_img1 = "SELECT qa_graphic_id, qa_graphic_name, qa_graphic_type FROM qa_graphics WHERE task_id = '".$_REQUEST['task_id']."' AND is_deleted = 0";
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
                $taskImageArr[] = $img1;
                $taskImageTableName[] = 'qa_graphics';
                $taskImageId[] = $qImgData1['qa_graphic_id'];
            }
        }
    }

    //  ncr attachment image get START
    $q_img2 = "SELECT ncr_attachment_id, attachment_file_name, attachment_type FROM qa_ncr_attachments WHERE attachment_type = 'checklist_attachment' AND task_detail_id = '".$_REQUEST['task_id']."' AND is_deleted = 0";
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
            $taskImageArr[]= $img2;
            $taskImageTableName[] = 'qa_ncr_attachments';
            $taskImageId[] = $qImgData2['ncr_attachment_id'];
        }
    }
    //  ncr attachment image get START
    //echo "===>> <pre>"; print_r($taskImageArr); die();
    ?>
    
    <div style="color:black;" id="task-attachment">
        <div id="mainDivForPrint">
            <style>
                ul#view_attachment{margin: 25px 0 auto; padding: 0}
                ul#view_attachment li {display: inline-block; height: 120px; list-style: outside none none; margin: 15px 20px; padding: 0; width: 120px; border: 1px solid #000;}
                ul#view_attachment li .imgBox{position: relative;}
                ul#view_attachment li .imgBox img.imgMain { width: 100%; height: 120px; }
                ul#view_attachment li .imgBox span { position: absolute; right: -15px; top:-15px; }
            </style>
            
            <div id="dragAndDropFiles1" class="uploadArea">
                <h1>Drop Images Here</h1>
            </div>
            <form name="demoFiler1" id="demoFiler1" enctype="multipart/form-data">
				<div class="btn-upload">
                <input type="hidden" id="selected_task_id" name="selected_task_id" value="<?=$_REQUEST['task_id']?>" />
                <span id="btnMultiUpload">Browse</span>
                <input type="file" name="multiUpload" id="multiUpload" multiple />
                <?php if(!empty($taskImageArr)){ ?>
                    <ul id="view_attachment">
                        <?php $j=0; foreach ($taskImageArr as $row) { ?>
                                <li>
                                    <div class="imgBox">
                                        <img class="imgMain" src='<?php echo $row; ?>' alt="image" />
                                        <span><img id="removeImage<?=$taskImageId[$j]?>" class="del-image" onClick="deleteAttachmentImage(this.id,'<?=$taskImageTableName[$j]?>',<?=$taskImageId[$j]?>)" src="images/remove.png" alt="delete image" style="display:block" /></span>
                                    </div>
                                </li>
                        <?php $j++; } ?>
                    </ul>
                <?php }?>
                </div>
                <input type="submit" name="submitHandler" id="submitHandler" class="green_small" style="float: right;" value="Save" class="buttonUpload" />
            </form>
        </div>
		<br clear="all" />
    </div>

<?php }

    if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'delete'){
        $_SESSION['deleteImgData'][]=array('tableName'=>$_REQUEST['tableName'],'imgeId'=>$_REQUEST['imgId']);
        $data = array('status' => true, 'error' => false, 'msg' => 'Image deleted successfully');
        echo json_encode($data); die;
    }

    if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'updateAttachment'){
        $_SESSION['deleteImgData'];
        $deleteData = $_SESSION['deleteImgData'];
        if(!empty($deleteData)){
            foreach ($deleteData as $row) {
                $idName = '';
                $tableName = '';
                if($row['tableName'] == 'qa_ncr_attachments'){
                    $tableName = 'qa_ncr_attachments';
                    $idName = 'ncr_attachment_id';
                }else if($row['tableName'] == 'qa_graphics'){
                    $tableName = 'qa_graphics';
                    $idName = 'qa_graphic_id';
                }
                $deleteUpdate = "UPDATE ".$tableName." SET
                    last_modified_by = '".$_SESSION['ww_builder_id']."',
                    last_modified_date = NOW(),
                    is_deleted = 1
                    WHERE
                    is_deleted = 0 AND ".$idName." = '".$row['imgeId']."'";
                    mysql_query($deleteUpdate);
            }
        }
            
        $data = array('status' => true, 'error' => false, 'msg' => 'Data update successfully');
        echo json_encode($data);
    }

?>
