<?php
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);
include('func.php');
if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<?php }

$builder_id=$_SESSION['ww_is_company'];
function normalise($string) {
	$string = str_replace("\r", "\n", $string);
	return $string;	
}

//echo "<pre>"; print_r($_REQUEST['checklistId']); //die;

if(isset($_REQUEST['checklistId'])){
	$update = 'update c_check_list_items set chli_is_deleted=1,chli_modified=now(),chli_modified_by="'.$builder_id .'" where chli_id="'.$_REQUEST['checklistId'].'"';
	mysql_query($update);
	$_SESSION['checklist_del'] = 'Checklist deleted successfully.';

	header('loaction:?sect=c_qa_checklist');
}

// Delete Checklist
if(isset($_REQUEST['checklistId'])){
	$update = 'update c_check_list_items set chli_is_deleted=1,chli_modified=now(),chli_modified_by="'.$builder_id .'" where chli_id="'.base64_decode($_REQUEST['checklistId']).'"';
	mysql_query($update);	
	$_SESSION['checklist_delete'] = 'Issued to deleted successfully.';
	
?>
<script language="javascript" type="text/javascript">
window.location.href="?sect=c_qa_checklist";
</script>
<?php	
}
?>
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
.box1 { background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
.link1 { background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
a.link1:hover { background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
.txt13 { border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }
</style>
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
<div id="container">
	<div class="content_hd1" style="margin:10px 0 50px -10px;"><h1>QA Checklist</h1></div>
	<div id="errorHolder" style="margin-left: 10px;margin-top:-15px;margin-top:0px\9;">
	<?php if(isset($_SESSION['issue_edit'])) { ?>
		<div class="success_r" style="width:250px;">
			<p><?php echo $_SESSION['issue_edit'];?></p>
		</div>
	<?php unset($_SESSION['issue_edit']); } ?>
    
	<?php if(isset($_SESSION['issue_add'])) { ?>
		<div class="success_r" style="width:250px;">
			<p><?php echo $_SESSION['issue_add'];?></p>
		</div>
	<?php unset($_SESSION['issue_add']); } ?>
    
	<?php if(isset($_SESSION['issue_to_delete'])) { ?>
		<div class="success_r" style="width:250px;">
			<p><?php echo $_SESSION['issue_to_delete'];?></p>
		</div>
	<?php unset($_SESSION['issue_to_delete']); } ?>
	<?php if((isset($success)) && (!empty($success))) { ?>
		<div class="success_r" style="height:35px;width:185px;"><p><?php echo $success; ?></p></div>
	<?php }
	if((isset($err_msg)) && (!empty($err_msg))) { ?>
		<div class="failure_r" style="height:50px;width:520px;"><p><?php echo $err_msg; ?></p></div>
	<?php } ?>
	</div>
  	
	<div class="big_container" style="margin-left:10px;" >
    <a href="#" onClick="addNewChecklist();"><div style=" float:left; background:url('images/add_checklists.png') !important; width:84px; height:21px; margin-bottom:2px; margin-top:0px !important;" class="add_new"></div></a>
	<?php include'c_qa_checklist_table.php';?></div>
</div>

	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<style>
		#sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
		#sortable li { margin: 0 32px 4px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
	</style>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">

function addNewChecklist(){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_checklist_by_ajax.php?&name='+Math.random(), loadingImage);
}

function addNewIssueToData(){
	//if($('#type').val().trim() == ''){$('#errorChlType').show('slow');return false;}else{$('#errorChlType').hide('slow');}
	if($('#name').val().trim() == ''){$('#errorChlName').show('slow');return false;}else{$('#errorChlName').hide('slow');}
	//return false;
	showProgress();
	$.post('add_checklist_by_ajax.php?antiqueID='+Math.random(), $('#addChecklistForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			RefreshTable();
			closePopup(300);
//			window.location.href="?sect=c_issue_to";
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

function editChecklistData(checklistId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_checklist_by_ajax.php?checklistId='+checklistId+'&name='+Math.random(), loadingImage,function(){task_table(checklistId);});
}

function popupChecklistTask(checklistId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'popup_checklist_task_by_ajax.php?checklistId='+checklistId+'&name='+Math.random(), loadingImage,commentMandatory);
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

function editTaskData(taskId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_checklist_task_by_ajax.php?taskId='+taskId+'&antiqueID='+Math.random(), loadingImage,commentMandatory);
}

function updateChecklistData(checklistId){
	var isDefault = $("#isDefault").val();
	//if($('#type').val().trim() == ''){$('#errorChlType').show('slow');return false;}else{$('#errorChlType').hide('slow');}
	if($('#name').val().trim() == ''){$('#errorChlName').show('slow');return false;}else{$('#errorChlName').hide('slow');}
	showProgress();
	$.post('edit_checklist_by_ajax.php?antiqueID='+Math.random(), $('#editChecklistForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			RefreshTable();
			closePopup(300);
		}else{
			jAlert('Data updation failed, try again later');
		}
	});
}

function addTaskData(checklistId){
	var isDefault = $("#isDefault").val();
	if($('#task').val().trim() == ''){$('#errorTask').show('slow');return false;}else{$('#errorTask').hide('slow');}

	var comment_mandatory = $('input[name=comment_mandatory]:checked').val();
	if(comment_mandatory == 'Yes'){
		if($('#comment').val().trim() == ''){$('#errorComment').show('slow');return false;}else{$('#errorComment').hide('slow');}
	}

	showProgress();
	$.post('popup_checklist_task_by_ajax.php?antiqueID='+Math.random()+'&checklistId='+checklistId, $('#addTaskForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			editChecklistData(checklistId);
		}else{
			jAlert('Data updation failed, try again later');
		}
	});
}

function updateTaskData(taskId){
	if($('#task').val().trim() == ''){$('#errorTask').show('slow');return false;}else{$('#errorTask').hide('slow');}

	var comment_mandatory = $('input[name=comment_mandatory]:checked').val();
	if(comment_mandatory == 'Yes'){
		if($('#comment').val().trim() == ''){$('#errorComment').show('slow');return false;}else{$('#errorComment').hide('slow');}
	}
	
	showProgress();
	$.post('edit_checklist_task_by_ajax.php?antiqueID='+Math.random()+'&taskId='+taskId, $('#addTaskForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			editChecklistData(jsonResult.data);
		}else{
			jAlert('Data updation failed, try again later');
		}
	});
}

function deleteTask(taskId,checklistId){
	var r = jConfirm('Do you want to delete this Task?', null, function(r){ if(r==true){
		$.post('edit_checklist_task_by_ajax.php?antiqueID='+Math.random()+'&deletetaskId='+taskId+'&checklistId='+checklistId).done(function(data) {
			hideProgress();
			var jsonResult = JSON.parse(data);
			if(jsonResult.error){
				jAlert(jsonResult.msg);
			}else if(jsonResult.status){
				editChecklistData(jsonResult.data);
			}else{
				jAlert('Data updation failed, try again later');
			}
		});
	} });
}

function deleteChecklist(checklistId){
	var r = jConfirm('Do you want to delete this Checklist?', null, function(r){ if(r==true){ window.location = '?sect=c_qa_checklist&checklistId='+checklistId; } });
}

function viewTaskData(checklistId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'c_qa_task_table.php?checklistId='+checklistId, loadingImage,function(){task_table(checklistId);});
	//alert('al');
	//setTimeout(function(){task_table(checklistId);},1000);
}

function changeOrderTaskList(checklistId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'change_order_task_list.php?checklistId='+checklistId+'&antiqueID='+Math.random(), loadingImage,loadfunction);


	// $( function() {
 //    $( "#sortable" ).sortable({
 //      revert: true
 //    });
 //    $( "#draggable" ).draggable({
 //      connectToSortable: "#sortable",
 //      helper: "clone",
 //      revert: "invalid"
 //    });
 //    $( "ul, li" ).disableSelection();
 //  } );
}

function loadfunction(){
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
}

function task_table(checklistId) {
	var oTable = $('#task_server').dataTable( {
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"iDisplayLength": 10,
		"sAjaxSource": "c_qa_task_data_table.php?checklistId="+checklistId,
		"bStateSave": true,
		"aoColumnDefs": [ {  "bSearchable": false, "bSortable": false, "aTargets": [ 1 ] }],
		"fnRowCallback": function ( nRow, aData, iDisplayIndex ) {
			if (aData[2] == 'Yes'){ $(nRow).css('color','red'); }
           	return nRow;
        }
	} );
	oTable.fnDraw();
}

// Issue To Contact Section
/*function showChecklist(checklistId){
	modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'show_issue_to_list_by_ajax.php?issueToId='+issueToId, loadingImage, function() {loadData(issueToId); });
}*/

function loadData(issueToId){
	$('#issueToData').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "show_issue_to_list_table_by_ajax.php?issueToId="+issueToId,
		"bStateSave": true,
		"bFilter": false,
	});
}

function addNewIssueToContact(issueToId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_issue_to_contact_by_ajax.php?issueToId='+issueToId+'&name='+Math.random(), loadingImage);
}

function addNewIssueToContactData(issueToId){
	if($('#contact_name').val().trim() == ''){$('#errorContactName').show('slow');return false;}else{$('#errorContactName').hide('slow');}
	showProgress();
	$.post('add_issue_to_contact_by_ajax.php?antiqueID='+Math.random(), $('#addContactForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			showIssueTo(issueToId);
			//closePopup(300);
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

function editIssueToContact(contactId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_issue_to_contact_by_ajax.php?contactId='+contactId+'&name='+Math.random(), loadingImage);
}

function updateIssueToContactData(issueToId){
	var isDefault = $("#isDefault").val();
	if($('#contact_name').val().trim() == ''){$('#errorContactName').show('slow');return false;}else{$('#errorContactName').hide('slow');}
	showProgress();
	$.post('edit_issue_to_contact_by_ajax.php?antiqueID='+Math.random(), $('#editContactForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			if(isDefault==1){
				//RefreshTable();
			}
			showIssueTo(issueToId);
		//	closePopup(300);
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

function deleteIssueToContact(issueToId, contId){
	var r = jConfirm('Do you want to delete this record?', null, function(r){ if(r==true){ 
		modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'show_issue_to_list_by_ajax.php?issueToId='+issueToId+'&contId='+contId, loadingImage, function(){showIssueTo(issueToId);});
	}});
}

function RefreshTable(){
	$.getJSON("c_qa_checklist_data_table.php?", null, function( json ){
		table = $('#example_server').dataTable();
		oSettings = table.fnSettings();
		table.fnClearTable(this);
		
		for (var i=0; i<json.aaData.length; i++){
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw();
	});
}

function saveOrderFunc(checklist_id){
	var taskId = new Array;
	var i=0;

	$("#sortable").find('li').each(function() {
		taskId[i] = this.id;
		i++;
	});

	console.log(taskId);

	$.post('change_order_task_list.php?antiqueID='+Math.random()+'&checklist_id='+checklist_id,{allTask:taskId}).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			//alert(jsonResult.data);
			editChecklistData(jsonResult.data);
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});

}

</script>
