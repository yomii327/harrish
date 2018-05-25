<?php
session_start();
include_once("commanfunction.php");
$obj = new COMMAN_Class();

if($_SERVER['REQUEST_METHOD'] == "POST"){
    if(!isset($_REQUEST['type']) && $_REQUEST['type'] != 'deleteImg'){
        $selected_task_id = $_SESSION['selected_task_id'];

        $selectedProId = $_SESSION['qaChecklistProId'];
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
            $_SESSION['deleteImgData'] = '';
        }


        $source = $_FILES['file']['tmp_name'];
        $imgName = 'checklist_attachment'. substr(microtime(), -6, -1).rand(0,99) . '.jpeg';
        $destination = '../inspections/ncr_files/'.$imgName;
        
        if($_FILES['file']['error'] == 0) {
            $uploads = move_uploaded_file($source, $destination);

            $attchdata = "task_detail_id = '".$selected_task_id."',
            attachment_title = 'Web_image',
            attachment_description = '',
            attachment_file_name = '".$imgName."',
            attachment_type = 'checklist_attachment',
            project_id = '".$selectedProId."',
            created_by = '".$_SESSION['ww_builder_id']."',
            created_date = NOW(),
            last_modified_by = '".$_SESSION['ww_builder_id']."',
            last_modified_date = NOW(),
            original_modified_date = NOW()";
            $attsql = "INSERT INTO qa_ncr_attachments SET ".$attchdata; 

            mysql_query($attsql);

            echo $_POST['index'];
        } else {
            echo 'Your file have some error, please try again!';
        }
    }
}

if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'deleteImg'){
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
        $_SESSION['deleteImgData'] = '';
        echo "Update data successfully";
    }else{
        echo "Please upload data.";
    }
}

?>