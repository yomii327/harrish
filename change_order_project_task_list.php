<?php session_start();
$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

# Non Compliances List
/*public function showNonCompliances($meterScoreId=0, $msType=0){
  $this->loginExists();
  $data['message']= $this->session->userdata('message');
  $this->session->set_userdata('message','');
  $this->load->view('recordOfNonCompliancesSearchByAjax', $data);   
}*/

//echo "======= <pre>"; print_r($_POST['allTask']); die;
if(isset($_REQUEST["antiqueID"])){
  if(isset($_POST['allTask']) && !empty($_POST['allTask'])){
    $allTask = $_POST['allTask'];
    $order_id = 1;
    foreach ($allTask as $value) {
      $task_query = "order_id = '".$order_id."',
                last_modified_date = NOW(),
                original_modified_date = NOW(),
                last_modified_by = ".$builder_id;
      //echo "UPDATE qa_project_checklist_task SET ".$task_query." WHERE id=".$value;
      mysql_query("UPDATE qa_project_checklist_task SET ".$task_query." WHERE id=".$value);
      $order_id++;
    }
    //die();
    if(mysql_affected_rows() > 0){
      $outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Task Order Change successfully!','data'=>$_REQUEST['checklist_id']);
      $_SESSION['successMsg'] = $outputArr['msg'];
    }
    echo json_encode($outputArr); die();
  }
}

if(isset($_REQUEST["antiqueID"]) && isset($_REQUEST["deletetaskId"])){
  $update_task_query = "is_deleted = 1,
            last_modified_date = NOW(),
            original_modified_date = NOW(),
            last_modified_by = ".$builder_id;
    //echo "UPDATE qa_project_checklist_task SET ".$task_query." WHERE id=".$_POST['taskId']; die;
    mysql_query("UPDATE qa_project_checklist_task SET ".$update_task_query." WHERE id=".$_REQUEST["deletetaskId"]);
    
    if(mysql_affected_rows() > 0){
      $outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Task deleted successfully.','data'=>$_REQUEST["checklistId"]);
      $_SESSION['successMsg'] = $outputArr['msg'];
    }
  echo json_encode($outputArr); die();
}

// Load HTML form 
if(isset($_REQUEST["antiqueID"]) && isset($_REQUEST["checklistId"])){
  
  $taskData = $obj->selQRYMultiple('*', "qa_project_checklist_task", " is_deleted = '0' AND project_checklist_id = '".$_REQUEST["checklistId"]."' ORDER BY order_id asc");
  $allTask = $taskData;
  $taskData = $taskData[0];
  //echo "<pre>"; print_r($taskData); die;
?>
<style>
  body{
    color:#000000;
  }
</style>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <!--link rel="stylesheet" href="/resources/demos/style.css" -->
  <style>
  ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
  li { margin: 5px; padding: 5px; width: 150px; }
  </style>
  


  <fieldset class="roundCorner">
    <legend style="color:#000000;">Task Order</legend>
    <form name="addTaskForm" id="addTaskForm">
    <table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
      
      <!-- ul>
        <li id="draggable" class="ui-state-highlight">Drag me down</li>
      </ul -->
       
      <ul id="sortable" style="width:100%">
        <?php if(!empty($allTask)){ foreach ($allTask as $key => $value) {?>
          <li id="<?php  echo $value['id']; ?>"  style="list-style-type: decimal; list-style-position: outside; width:90%;" class="ui-state-default"><?php echo $value['task_name'] ?></li>
        <?php }} ?>
      </ul>

      <tr>
        <td>&nbsp;</td>
        <td>
          <input type="hidden" name="checklistId" value="<?php echo $taskData['project_checklist_id']; ?>" />
          <input type="button" name="button" class="green_small" value="Submit" id="button" style="float:left;" onclick="saveOrderFunc(<?php echo $taskData['project_checklist_id']; ?>);" />
                &nbsp;&nbsp;&nbsp; 
        </td>
      </tr>
    </table>
    </form>
    <br clear="all" />
  </fieldset>
<?php }?>

