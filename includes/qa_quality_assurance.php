<?php
include_once("commanfunction.php");
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);include('func.php');
if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){?>
<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
$builder_id=$_SESSION['ww_builder_id'];
if(isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company']==1){
	$builder_id = $_SESSION['ww_is_company'];
}
function normalise($string) {
	$string = str_replace("\r", "\n", $string);
	return $string;	
}

$project_id = $_SESSION['idp'];

// $projectIds = $obj->selQRYMultiple('id, project_id', "check_list_items_project", " is_deleted = '0'");
// foreach ($projectIds as $value) {
// 	$update="UPDATE qa_project_checklist_task SET project_id='".$value['project_id']."'WHERE project_checklist_id='".$value['id']."'";
// 	mysql_query($update);
// }

if(isset($_POST['assignSubmit'])){
	//echo "======="; die;
	//echo "<pre>"; print_r($_SESSION); die();
	$_POST['assignChecklist'] = (isset($_POST['assignChecklist']) && !empty($_POST['assignChecklist']))?$_POST['assignChecklist']:array(0);
	
    $update="UPDATE check_list_items_project SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE checklist_id !=0 AND checklist_id NOT IN(".implode(',',$_POST['assignChecklist']).") AND project_id='".$_SESSION['idp']."'";

	mysql_query($update);

	$projCurtChecklistData = $obj->selQRYMultiple('GROUP_CONCAT(checklist_id) as ids', "check_list_items_project", " is_deleted = '0' AND project_id='".$_SESSION['idp']."'");

	//echo "<pre>"; print_r($projCurtChecklistData); die;

	$oldIds = (isset($projCurtChecklistData[0]['ids']) && !empty($projCurtChecklistData[0]['ids']))?" AND chli_id NOT IN(".$projCurtChecklistData[0]['ids'].")":"";
	$checklistData = $obj->selQRYMultiple('*', "c_check_list_items", " chli_is_deleted = '0' AND chli_id IN(".implode(',',$_POST['assignChecklist']).") ".$oldIds." ");	
    //echo "<pre>"; print_r($checklistData); die;
	foreach($checklistData as $checklist){
		$checklist_insert = "INSERT INTO check_list_items_project SET
			checklist_id = '".trim($checklist['chli_id'])."',
			checklist_name = '".trim($checklist['chli_name'])."',
			type = '".trim($checklist['chli_type'])."',
			project_id = '".$_SESSION['idp']."',
			last_modified_date = NOW(),
			original_modified_date = NOW(),
			last_modified_by = ".$builder_id.",
			created_date = NOW(),
			created_by = ".$builder_id;

		mysql_query($checklist_insert);
		$checklistId = mysql_insert_id();

		$checklistTaskData = $obj->selQRYMultiple('*', "qa_checklist_task", "is_deleted = '0' AND checklist_id=".$checklist['chli_id']);

		foreach($checklistTaskData as $checklistTask){ 
			$checklistTask_insert = "INSERT INTO qa_project_checklist_task SET
				project_checklist_id = '".$checklistId."',
				project_id = ".$project_id.",
				task_name = '".trim($checklistTask['task_name'])."',
				task_status = '".$checklistTask['task_status']."',
				task_image = '',
				task_comment = '".trim($checklistTask['task_comment'])."',
				last_modified_date = NOW(),
				original_modified_date = NOW(),
				last_modified_by = ".$builder_id.",
				created_date = NOW(),
				created_by = ".$builder_id.",
				order_id = ".$checklistTask['order_id'];

			mysql_query($checklistTask_insert);
			$projectChecklistId = mysql_insert_id();
		}
	}	
	$sucMsg = 1; $_POST['assignChecklist']='';

}

if(isset($_REQUEST['checklistId'])){
	$update = 'update check_list_items_project set is_deleted=1,last_modified_date=now(),original_modified_date=now(),last_modified_by="'.$builder_id .'" where id="'.$_REQUEST['checklistId'].'"';
	mysql_query($update);
	$_SESSION['checklist_del'] = 'Project Checklist deleted successfully.';

	header('loaction:?sect=qa_quality_assurance');
}

if(isset($_REQUEST['id'])){
	$update='update inspection_issue_to set is_deleted=1,last_modified_date=now(),last_modified_by="'.$builder_id .'" where issue_to_id="'.base64_decode($_REQUEST['id']).'"';

	mysql_query($update);
	$_SESSION['issue_to_del']='Issued to deleted successfully.';
	header('loaction:?sect=issue_to');
	
}

// Delete issue to
if(isset($_REQUEST['issueToId'])){
	$update="UPDATE inspection_issue_to SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE issue_to_id = '".$_REQUEST['issueToId']."' AND project_id='".$_SESSION['idp']."'";
	mysql_query($update);
	
    $update="UPDATE issue_to_contact SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE issue_to_id = '".$_REQUEST['issueToId']."' AND project_id='".$_SESSION['idp']."'";
	mysql_query($update);
	$_SESSION['issue_to_del']='Issued to deleted successfully.';
	
?>
<script language="javascript" type="text/javascript">
window.location.href="?sect=issue_to";
</script>
<?php	}?>
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
.box1{ background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
.link1{ background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
a.link1:hover{ background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
.txt13{ border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }
.demo_jui td{ text-align:left; }
#example_server td img{ cursor:pointer;}
</style>

<div id="middle" style="padding-top:10px;">
<div id="leftNav" style="width:250px;float:left;">
<?php include 'side_menu.php';
$id=base64_encode($_SESSION['idp']);$hb=base64_encode($_SESSION['hb']);  ?>
</div>
<script type="text/javascript">
var deadlock = true;//File upload
var params = "";
var align = 'center';
var top1 = 100;
var width = 800;
var padding = 10;
var backgroundColor = '#FFFFFF';
var borderColor = '#333333';
var borderWeight = 4;
var borderRadius = 5;
var fadeOutTime = 300;
var disableColor = '#666666';
var disableOpacity = 40;
var loadingImage = 'images/loadingAnimation.gif';
var copyStatus = false;
var copyId = '';
</script>
<div id="rightCont" style="float:left;width:700px;">
	<div class="content_hd1" style="width:500px;margin-top:12px;">
		<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font>
		<!-- <a style="float:left;margin-top:-25px;width:87px;margin-left:590px;" href="?sect=add_project_detail&id=<?php //echo $id;?>&hb=<?php //echo $hb;?>">
			<img src="images/back_btn2.png" style="border:none;" />
		</a> -->
		<a href="?sect=add_project_detail&id=<?=$id;?>&hb=<?=$hb;?>" class="green_small" style="float:left;margin-top:-25px; margin-left:608px;z-index:100;">Back</a>
	</div><br clear="all" />
	<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top:-15px;margin-top:0px\9;">
		<?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { ?>
			<div class="success_r" style="height:35px;width:185px;"><p><?php echo $_SESSION['add_project'] ; ?></p></div>
		<?php unset($_SESSION['add_project']);} ?><?php if((isset($success)) && (!empty($success))) { ?>
			<div class="success_r" style="height:35px;width:185px;"><p><?php echo $success; ?></p></div>
		<?php }
			if((isset($err_msg)) && (!empty($err_msg))) { ?>
			<div class="failure_r" style="height:50px;width:520px;"><p><?php echo $err_msg; ?></p></div>
		<?php } ?>
	</div>
  	<div class="content_container" style="float:left;width:690px;text-align:center;margin-left:10px;margin-right:10px;">
<!--First Box-->
<?php include'data-table.php';?>
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" /> 
<div style="width:722px; float:left; margin:5px 0 10px 0; ">
<?php if((isset($sucMsg)) && $sucMsg==1) { $sucMsg==0;
	echo '	<div class="success_r" style="height:35px;width:300px;"><p>Action performed successfully!</p></div>	';	
  }
  if((isset($_SESSION['issue_to_del'])) && !empty($_SESSION['issue_to_del'])) { unset($_SESSION['issue_to_del']);
	echo '	<div class="success_r" style="height:35px;width:300px;"><p>Record deleted successfully!</p></div>	';	
  } ?>
	<form method="post" name="checklistForm" id="checklistForm" action="">
		<table border="0" cellspacing="0" cellpadding="3" style="width:748px;">
			<tr>
				<td colspan="2" align="left">
                <!-- Start Multiselect section -->
                	<!--link rel="stylesheet" href="js/multiselect/css/common.css" type="text/css" /-->


                <select id="assignChecklist" class="multiselect" multiple="multiple" name="assignChecklist[]">
                <?php //$issueToProjData = $obj->selQRYMultiple('master_contact_id, issue_to_name, company_name', 'inspection_issue_to', " project_id = '".$_SESSION['idp']."' and is_deleted = '0' and issue_to_name!='' ");
                $checklistProjData = $obj->selQRYMultiple('id,checklist_id,checklist_name', 'check_list_items_project', " project_id = '".$_SESSION['idp']."' and is_deleted = '0'");

					$projChecklistArr = array();
					foreach($checklistProjData as $checklistPro){
						$projChecklistArr[$checklistPro['checklist_id']] = $checklistPro['checklist_name'];
					}
					
					//$issueToData = $obj->selQRYMultiple('contact_id, issue_to_name, company_name', 'master_issue_to_contact', " is_deleted = '0' and issue_to_name!=''");
					$checklistData = $obj->selQRYMultiple('chli_id,chli_name', 'c_check_list_items', " chli_is_deleted = '0' and chli_name!=''",'PRP');
					$i=0;
					foreach($checklistData as $checklist){
						if(isset($projChecklistArr[$checklist['chli_id']])){
							echo '<option value="'.$checklist['chli_id'].'" selected="selected">'.$checklist['chli_name'].'</option>';
						}else{
							echo '<option value="'.$checklist['chli_id'].'">'.$checklist['chli_name'].'</option>';
						}
					}
				?>
				</select>
                <!-- End Multiselect section -->
                </td>
			</tr>   
			<tr>
				<td colspan="2"><input type="submit" style="float:right;height:29px;margin-right:50px;" value="Submit" id="assignSubmit" class="green_small" name="assignSubmit">
               </td>
			</tr>
		</table>
	</form>
<!--br clear="all" /-->
<!-- Issue to table section -->
<div class="big_container" style="width:722px; margin-top:0px;" >

<!-- <a href="#" onClick="addNewchecklist();"><div style=" float:left; background:url('images/add_quality_checklist.png') !important; width:160px; height:24px; margin-bottom:2px; margin-top:0px !important;" class="add_new"></div></a>-->
<a href="#" onClick="addNewchecklist();" style="float:left;margin-bottom:2px;" class="green_small">Add Quality Checklist</a>

 <?php //include'project_issueto_table.php';?>
	<div class="demo_jui" style="width:99%;" >
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="example_server" width="100%">
			<thead>
				<tr>
					<th width="80%" nowrap="nowrap">Checklist Name</th>
					<!--th width="32%">Contact Name</th>
					<th>Phone</th>
					<th>Email</th>
					<th>Tags</th-->
					<th width="20%">Action</th>
				</tr>
			</thead>
			<tbody>
			<tr>
				<td colspan="5" class="dataTables_empty">Loading data from server</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="spacer"></div>
</div>

<!-- Issue to table section -->
</div>
		</div>


</div>

<link type="text/css" href="js/multiselect/css/ui.multiselect.css" rel="stylesheet" />
<style>
.multiselect {width: 710px;	height: 200px;  }
.ui-multiselect div.list-container {
    border: 0 none;
    float: right !important;
    margin: 0;
    padding: 0;
}
.available, .selected{
	width:354px !important;
}
.ui-widget-header input{
	width:150px !important;
}

.add-all, .remove-all {
    background: none repeat scroll 0 0 #2070A5;
    border: 1px outset #2070A5;
    /*display: block;*/
	display:none !important;
    margin: 2px !important;
    padding: 5px !important;
}
</style>
<!-- <script type="text/javascript" src="js/multiselect/js/jquery-ui-1.8.custom.min.js"></script> -->
<script type="text/javascript" src="js/jquery-ui-10.js"></script>
<script type="text/javascript" src="js/multiselect/js/plugins/tmpl/jquery.tmpl.1.1.1.js"></script>
<script type="text/javascript" src="js/multiselect/js/ui.multiselect.js"></script>
<script type="text/javascript">
$(document).ready(function(){
//	$(function(){
		$("#assignChecklist").multiselect({ droppable: 'none' });
//	});
});

// Data table section
$(document).ready(function() {
	var oTable = $('#example_server').dataTable( {
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"iDisplayLength": 10,
		"sAjaxSource": "project_checklist_data_table.php",
		"bStateSave": true,
		"aoColumnDefs": [ {  "bSearchable": true, "bSortable": false, "aTargets": [ 1 ] }],
		"bFilter": false,
	} );
	oTable.fnDraw();
} );

function addNewchecklist(){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_project_checklist_by_ajax.php?&name='+Math.random(), loadingImage);
}

function addNewProjectChecklistData(){
	//if($('#type').val().trim() == ''){$('#errorChlType').show('slow');return false;}else{$('#errorChlType').hide('slow');}
	if($('#name').val().trim() == ''){$('#errorChlName').show('slow');return false;}else{$('#errorChlName').hide('slow');}
	//return false;
	showProgress();
	$.post('add_project_checklist_by_ajax.php?antiqueID='+Math.random(), $('#addChecklistForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			//RefreshTable();
			//closePopup(300);
			window.location.href="?sect=qa_quality_assurance";
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

// function RefreshTable(){
// 	$.getJSON("qa_quality_assurance.php?", null, function( json ){
// 		table = $('#example_server').dataTable();
// 		oSettings = table.fnSettings();
// 		table.fnClearTable(this);
		
// 		for (var i=0; i<json.aaData.length; i++){
// 			table.oApi._fnAddData(oSettings, json.aaData[i]);
// 		}
// 		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
// 		table.fnDraw();
// 	});
// }

function deleteProjectChecklist(checklistId){
	var r = jConfirm('Do you want to delete this Checklist?', null, function(r){ if(r==true){ window.location = '?sect=qa_quality_assurance&checklistId='+checklistId; } });
}

function editProjectChecklistData(checklistId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_project_checklist_by_ajax.php?checklistId='+checklistId+'&name='+Math.random(), loadingImage,function(){project_task_table(checklistId);});
}

function project_task_table(checklistId) {
	var oTable = $('#task_server').dataTable( {
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"iDisplayLength": 10,
		"sAjaxSource": "p_qa_task_data_table.php?checklistId="+checklistId,
		"bStateSave": true,
		"aoColumnDefs": [ {  "bSearchable": false, "bSortable": false, "aTargets": [ 1 ] }],
		"fnRowCallback": function ( nRow, aData, iDisplayIndex ) {
			if (aData[2] == 'Yes'){ $(nRow).css('color','red'); }
           	return nRow;
        }
	});
	oTable.fnDraw();
}

function updateProjectChecklistData(checklistId){
	var isDefault = $("#isDefault").val();
	//if($('#type').val().trim() == ''){$('#errorChlType').show('slow');return false;}else{$('#errorChlType').hide('slow');}
	if($('#name').val().trim() == ''){$('#errorChlName').show('slow');return false;}else{$('#errorChlName').hide('slow');}
	showProgress();
	$.post('edit_project_checklist_by_ajax.php?antiqueID='+Math.random(), $('#editChecklistForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			window.location.href="?sect=qa_quality_assurance";
		}else{
			jAlert('Data updation failed, try again later');
		}
	});
}

function addProjectChecklistTask(checklistId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_project_checklist_task.php?checklistId='+checklistId+'&name='+Math.random(), loadingImage,commentMandatory);
}

function commentMandatory(){
	$('input[type=radio][name=comment_mandatory]').change(function() {
		if($(this).val() == 'Yes'){
			//$("#commentMandatory").css('display','table-row');
			$("#commentStar").html('<b>*</b>');
		}else{
			$("#commentStar").html('');
		}
	});
}

function addProjectTaskData(checklistId){
	if($('#task').val().trim() == ''){$('#errorTask').show('slow');return false;}else{$('#errorTask').hide('slow');}
	
	var comment_mandatory = $('input[name=comment_mandatory]:checked').val();
	if(comment_mandatory == 'Yes'){
		if($('#comment').val().trim() == ''){$('#errorComment').show('slow');return false;}else{$('#errorComment').hide('slow');}
	}

	showProgress();
	$.post('add_project_checklist_task.php?antiqueID='+Math.random()+'&checklistId='+checklistId, $('#addTaskForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			editProjectChecklistData(checklistId);
		}else{
			jAlert('Data updation failed, try again later');
		}
	});
}

function deleteProjectTask(taskId,checklistId){
	var r = jConfirm('Do you want to delete this Task?', null, function(r){ if(r==true){
		$.post('edit_project_checklist_task_by_ajax.php?antiqueID='+Math.random()+'&deletetaskId='+taskId+'&checklistId='+checklistId).done(function(data) {
			hideProgress();
			var jsonResult = JSON.parse(data);
			if(jsonResult.error){
				jAlert(jsonResult.msg);
			}else if(jsonResult.status){
				editProjectChecklistData(jsonResult.data);
			}else{
				jAlert('Data updation failed, try again later');
			}
		});
	} });
}

function editTaskData(taskId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_project_checklist_task_by_ajax.php?taskId='+taskId+'&antiqueID='+Math.random(), loadingImage, commentMandatory);
}

function updateProjectTaskData(taskId){
	if($('#task').val().trim() == ''){$('#errorTask').show('slow');return false;}else{$('#errorTask').hide('slow');}
	
	var comment_mandatory = $('input[name=comment_mandatory]:checked').val();
	if(comment_mandatory == 'Yes'){
		if($('#comment').val().trim() == ''){$('#errorComment').show('slow');return false;}else{$('#errorComment').hide('slow');}
	}
	
	showProgress();
	$.post('edit_project_checklist_task_by_ajax.php?antiqueID='+Math.random()+'&taskId='+taskId, $('#addTaskForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			editProjectChecklistData(jsonResult.data);
		}else{
			jAlert('Data updation failed, try again later');
		}
	});
}

function changeOrderProjectTaskList(checklistId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'change_order_project_task_list.php?checklistId='+checklistId+'&antiqueID='+Math.random(), loadingImage,loadfunction);
}

function loadfunction(){
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
}

function saveOrderFunc(checklist_id){
	var taskId = new Array;
	var i=0;

	$("#sortable").find('li').each(function() {
		taskId[i] = this.id;
		i++;
	});
	console.log(taskId);
	$.post('change_order_project_task_list.php?antiqueID='+Math.random()+'&checklist_id='+checklist_id,{allTask:taskId}).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			//alert(jsonResult.data);
			editProjectChecklistData(jsonResult.data);
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

</script>
