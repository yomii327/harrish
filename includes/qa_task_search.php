<?php $builder_id=$_SESSION['ww_builder_id'];
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">
		window.location.href="<?=HOME_SCREEN?>";
	</script>
<?php }

function FillSelectBox($field, $table, $where, $group){
	$q = "SELECT $field FROM $table WHERE $where GROUP BY $group";
	$res = mysql_query($q);
	$rowCount4Sel = mysql_num_rows($res);
	while($q1 = mysql_fetch_array($res)){
		$selStr = '<option value="'.$q1[0].'"';
		if($rowCount4Sel == 1)
			$selStr .= ' selected="selected"';
		$selStr .= '>'.$q1[1].'</option>';
		echo $selStr;
	}
}
$projectName=''; 
$locationName='';
$subLocationQA1 = '';
$subLocationQA2 = '';
$subLocationQA3 = '';
#if(isset($_GET['bk']) && $_GET['bk'] == 'Y'){
	$projectName = $_SESSION['qa']['projNameQA'];
	$locationQA = $_SESSION['qa']['locationQA'];
	$subLocationQA1 = $_SESSION['qa']['subLocationQA1'];
	$subLocationQA2 = $_SESSION['qa']['subLocationQA2'];
	$subLocationQA3 = $_SESSION['qa']['subLocationQA3'];
#}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" type="text/css"/>
<title>Report</title>
<style type="text/css">
@import "css/jquery.datepick.css";
table td{padding:5px;}
table{ margin-left:10px;}
.dateCalender{ background:#FFF; cursor:default; height:20px; -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; }
#optionBar li{ float:left; list-style: none; }
ul#optionBar {margin-left:-50px;}
table.simpleTable td { color:#000000;}
.error-edit-profile-red{ background: url("images/bg-error-edit-profile-red.png") no-repeat scroll 0 0 transparent; color: #000; font-size: 11px; margin: 1px 0 2px 3px; padding: 10px 3px 8px 4px; width: 240px; text-shadow:none; }
.roundCorner {color:#000000;}
.roundCorner table tr td:nth-child(1){ text-align:right; }
.roundCorner table tr td:nth-child(2){ text-align:center; }
.roundCorner table tr td:nth-child(3){ text-align:left; }
</style>
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript">
var align = 'center';
var top1 = 100;
var width = 500;
var padding = 10;
var backgroundColor = '#FFFFFF';
var borderColor = '#333333';
var borderWeight = 4;
var borderRadius = 5;
var fadeOutTime = 300;
var disableColor = '#666666';
var disableOpacity = 40;
var loadingImage = 'images/loadingAnimation.gif';
var spinnerVisible = false;
</script>
</head>
<body id="dt_example">
<script language="javascript" type="text/javascript">
function startAjaxQA(val){
	AjaxShow("POST","ajaxFunctions.php?type=locationQA && proID="+val,"ShowLocationQA");
	AjaxShow("POST","ajaxFunctions.php?type=setSession && proID="+val,"setSession");
	AjaxShow("POST","ajaxFunctions.php?type=userRole && proID="+val,"userRole");
	//AjaxShow("POST","ajaxFunctions.php?type=issueToQA && proID="+val,"ShowIssuedTo");
}
function subLocate1QA(val){
	AjaxShow("POST","ajaxFunctions.php?type=subLocationQA1 && proID="+val,"ShowSubLocation1QA");
}
function subLocate2QA(val){
	AjaxShow("POST","ajaxFunctions.php?type=subLocationQA2 && proID="+val,"ShowSubLocation2QA");
}
function subLocate3QA(val){
	AjaxShow("POST","ajaxFunctions.php?type=subLocationQA3 && proID="+val,"ShowSubLocation3QA");
}
function subLocate3QACallback(val, valTwo){
//	console.log(val, valTwo);
//	AjaxShowMultipleCallBack("POST","ajaxFunctions.php?reqReport=Y&&type=adhocITRReport&&proID="+encodeURIComponent(locationName)+"&&parentID="+encodeURIComponent(locationParent)+"&&locID="+val,"ShowAhocitr");
	AjaxShowMultipleCallBack("POST", "ajaxFunctions.php?type=subLocationQA3 && proID="+val, "ShowSubLocation3QA", valTwo);
}/*
function subLocateitr(val){
	var locationName = $('#subLocationQA2 :selected').text();
	console.log(locationName);
	locationName = locationName.trim();
	var locationParent = $('#subLocationQA1').val();
	AjaxShow("POST","ajaxFunctions.php?type=adhocITR&&proID="+encodeURIComponent(locationName)+"&&parentID="+locationParent+"&&locID="+val,"ShowAhocitr");
}*/

function subLocateitr(val){
	var locationName = $('#subLocationQA2 :selected').text();
	locationName = locationName.trim();
	var locationParent = $('#subLocationQA1').val();
	console.log(locationName, locationParent, val);
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=adhocITRReport&&proID="+encodeURIComponent(locationName)+"&&parentID="+encodeURIComponent(locationParent)+"&&locID="+val,"ShowAhocitr");
}

function resetIds(){
	document.getElementById('projectQAError').style.display = 'none';
	document.getElementById('locationQAError').style.display = 'none';
}
function resetLoc(){
	$('select#subLocationQA1').html('<option value="">Select</option>');
	$('select#subLocationQA2').html('<option value="">Select</option>');
	$('select#subLocationQA3').html('<option value="">Select</option>');
	$('#ahocitr').val('');
	$('#searchKeyword').val('');
}
function toggleFolder(folderid){ $("#"+folderid).toggle(); }
function validateAndSubmit(){
	$("#errorHolder").hide();
	try{
		$("#container_progress").innerHTML = '';
		var params = '';
		var startWith = 0;
		var projNameQA = $('#projNameQA').val();
		var locationQA = $('#locationQA').val();
		var subLocationQA1 = $('#subLocationQA1').val();
		var subLocationQA2 = $('#subLocationQA2').val();
		var ahocitr = $('#ahocitr').val();
		var subLocationQA3 = $('#subLocationQA3').val();
		var sortBy = "";
		var searchKeyword = "";
		var status = $('#status').val();
		var issueTo = "";
		var sessionBack = $('#sessionBack').val();
		
		if(projNameQA == ''){ $('#projectQAError').show(); return false; }
		if(locationQA == ''){ $('#locationQAError').show(); return false; }

		params = "SearchInsp=1&projNameQA="+projNameQA+"&locationQA="+locationQA+"&subLocationQA1="+subLocationQA1+"&subLocationQA2="+subLocationQA2+"&subLocationQA3="+subLocationQA3+"&ahocitr="+ahocitr+"&sortBy="+sortBy+"&searchKeyword="+searchKeyword+"&status="+status+"&name="+Math.random();
		
		if (window.XMLHttpRequest){	xmlhttp=new XMLHttpRequest();	}else{	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");	}
		showProgress();
		var url = 'QA_ajax_result.php?' + params;
		xmlhttp.open("GET", url, true);
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				hideProgress();
				document.getElementById("container_progress").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.send(params);
	}catch(e){
		alert(e.message); 
	}
	if(subLocationQA3 != '' ){
		var projLocID = subLocationQA2;
		console.log(projLocID);
		$('#projLocationContainer').show('fast');
		/*
		$.post('ajax_save_project_locations.php?antiqueID='+Math.random(), {projLocID : projLocID, projectID : projNameQA}).done(function(data) {
			var jsonResult = JSON.parse(data);
			if(jsonResult.status){
				$('#activityHolder').html(jsonResult.data);	
			}
		});*/
	}else{
		$('#projLocationContainer').hide();
	}
}
function addTask(locationID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_task_qa_search.php?sub_location_id='+locationID+'&uniqueId='+Math.random(), loadingImage);
}
function addTaskSubmit(){
	if($.trim($('#task').val()) == ''){$('#taskError').show('slow'); return false;}else{$('#taskError').hide('slow');}
	showProgress();
	$.post('add_task_qa_search.php?antiqueID='+Math.random(), $('#addTaskForm').serialize()).done(function(data) {
		hideProgress();
		if(data != 'Duplicate task'){
			closePopup(300);
			validateAndSubmit();
		}else{
			jAlert('Duplicate task, task not added');
		}
	});
}
</script>
<br/>
<div class="content_hd1" style="background-image:url(images/quality_assurance.png);">&nbsp;</div>
<br clear="all" />
<?php if(isset($_SESSION['inspection_added'])){ ?>
	<div id="errorHolder" style="margin-left: 40px;margin-bottom: 20px;">
			<div class="success_r" style="height:35px;width:405px;"><p><?=$_SESSION['inspection_added'];?></p></div>
	</div>
<?php unset($_SESSION['inspection_added']); } ?>
<div id="errorHolderDynm" style="margin-left: 40px;margin-bottom: 20px; display:none;">
		<div class="success_r" style="height:35px;width:405px;"><p id="errorHolderDynmPara"></p></div>
</div>
<div class="search_multiple" style="border:1px solid; text-align:center;width:960px;margin-left: 20px;">
<form name="qaSearchForm" id="qaSearchForm">
	<table width="900" cellpadding="0" cellspacing="5" border="0">
		<tr>
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Project Name <span class="reqire">*</span></td>
			<td colspan="2">
				<select name="projNameQA" id="projNameQA" class="select_box" onChange="resetLoc();resetIds();startAjaxQA(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
                    <?php #if(isset($_GET['bk']) && $_GET['bk'] == 'Y'){
							$q = "SELECT project_id, project_name FROM user_projects WHERE user_id = '".$builder_id."' AND is_deleted = 0 GROUP BY project_name";
							$res = mysql_query($q);
							$rowCount4Sel = mysql_num_rows($res);
							while($q1 = mysql_fetch_array($res)){
								$selectBox = '<option value="'.$q1[0].'"';
								if($projectName == $q1[0]){
									$selectBox .= 'selected="selected"';
								}
								if($rowCount4Sel == 1){
									$selectBox .= 'selected="selected"';
									$autoProj = $q1[0];
								}
								$selectBox .= '>'.$q1[1].'</option>';
								echo $selectBox;
							}
						#}else{?>
					<?php #FillSelectBox("project_id, project_name", "user_projects", "project_id >='1' and user_id =".$builder_id." and is_deleted = 0", "project_name"); ?>
     <?php #} ?> 
				</select>
				<div class="error-edit-profile" style="width:220px;display:none;" id="projectQAError">The project field is required</div>
			</td>
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Area <span class="reqire">*</span></td>
			<td colspan="2">
				<div id="ShowLocationQA">
				<select name="locationQA" id="locationQA"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
				</select>
				</div>
				<div class="error-edit-profile" style="width:220px;display:none;" id="locationQAError">The Area field is required</div>
			</td>
		</tr>
		<tr>
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Activity</td>
			<td colspan="2">
				<div id="ShowSubLocation1QA">
				<select name="subLocationQA1" id="subLocationQA1"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
				</select>
				</div>
				<div class="error-edit-profile" style="width:220px;display:none;" id="subLocationQA1Error">The Activity field is required</div>
			</td>
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Level</td>
			<td colspan="2">
				<div id="ShowSubLocation2QA">
				<select name="subLocationQA2" id="subLocationQA2"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
				</select>
				</div>
				<div class="error-edit-profile" style="width:220px;display:none;" id="subLocationQA2Error">The Level field is required</div>
			</td>
		</tr>
		<tr>
			<!--<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">ITP</td>
			<td colspan="2" rowspan="2">
				<div id="ShowAhocitr">
				<select name="ahocitr" id="ahocitr" class="select_box"  multiple="multiple" style="width:220px;background-image:url(images/multiple_select_box.png);height: 76px;">
					<option value="">Select</option>
				</select>
				</div>
			</td>-->
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Task</td>
			<td colspan="2">
				<div id="ShowAhocitr"></div>
				<div id="ShowSubLocation3QA">
				<select name="subLocationQA3" id="subLocationQA3" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
				</select>
				</div>
			</td>
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Status</td>
			<td colspan="2">
				<select name="status" id="status" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<?php $statusArr = array('Passed', 'Failed', 'NA');?>
					<option value="">Select</option>
					<?php foreach($statusArr as $key=>$stVal){?>
						<option <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == $stVal){ echo 'selected="selected"'; }}?> value="<?=$stVal?>"><?=$stVal?></option>
					<?php }?>
				</select>
			</td>
		</tr>
		<tr id="projLocationContainer" style="display:none;">
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Activity Id</td>
			<td colspan="2" valign="bottom"><input type="text" style="width:180px; background-image:url(images/selectSpl.png); margin-left:0px; margin-left:60px;padding: 0 20px 0 20px;" class="input_small" id="projectLocation" name="projectLocation">&nbsp;<img src="images/save-small.png" title="Save Project Location" alt="Save Project Location" id="saveProjectLocation" onClick="saveProjectLoc();" align="absbottom"/></td>
			<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">&nbsp;<input type="hidden" name="isEditParam" id="isEditParam" value="0" /></td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td align="left" valign="top" colspan="4"><ul id="activityHolder"></ul></td>
		</tr>
		<tr>
			<td colspan="3" align="left">&nbsp;</td>
			<td align="left"><div id="report_timer" style="color:#FFFFFF;"></div></td>
			<td><input type="hidden" name="sessionBack" id="sessionBack" value="Y" />
				<input name="SearchInsp" type="button" class="submit_btn" id="button" value="" style="background-image:url(images/search_btn_web.png); width:113px; height:46px;" onClick="validateAndSubmit();" />
			</td>
		</tr>
		
	</table>
</form>
</div>
<br/>
<br/>
<div id="container_progress" style="width:980px;margin-top:-20px;">&nbsp;</div>
<script type="text/javascript">
function saveProjectLoc(){
	var locationQA = $('#locationQA').val();
	var subLocationQA1 = $('#subLocationQA1').val();
	var subLocationQA2 = $('#subLocationQA2').val();
	var projNameQA = $('#projNameQA').val();
//	var ahocitr = $('#ahocitr').val();
	var subLocationQA3 = $('#subLocationQA3').val();
	var projLocaiton = subLocationQA2;
		
	var parentLocation = $('#projectLocation').val();	
	var isEditParam = $('#isEditParam').val();	
//console.log(projLocaiton, parentLocation);
	$.post('ajax_save_project_locations.php?uniqueId='+Math.random(), {locationQA:locationQA, subLocationQA1:subLocationQA1, projLocaiton : projLocaiton, parentLocation : parentLocation, projectID : projNameQA, isEditParam:isEditParam, subLocationQA3:subLocationQA3}).done(function(data) {
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			console.log(jsonResult);
			
			setTimeout(validateAndSubmit, 3000);
		}
	});
}
function editActivity(activityID){
	console.log(activityID);
	$('#projectLocation').val($('#activity_'+activityID).text());
	$('#isEditParam').val(activityID);
}
function deleteActivity(activityID){
	console.log(activityID);
	$.post('ajax_save_project_locations.php?singleId='+Math.random(), {activityID : activityID, 'operation' : 'Delete'}).done(function(data) {
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			console.log(jsonResult);
			$('#activity_'+activityID).remove();
		}
	});
}


function closetaskids(){
	
	var r = jConfirm('Are you sure you want to close without review?', null, function(r){
		if (r==true){
				var taskArray = new Array();
				var projectID = document.getElementById('projNameQA').value;
				var taskCount = document.allTaskTable.elements["taskID[]"].length;
				if(taskCount === undefined){
					var taskId = document.getElementById('taskID');
					if(taskId.checked){
						taskArray = taskId.value;
					}
				}else{
					for(var i=0; i<taskCount; i++){
						var taskId = document.allTaskTable.elements["taskID[]"][i];
						if(taskId.checked){
							taskArray[i] = taskId.value;
						}else{
							taskArray[i] = 0;
						}
					}
				}
			//Filter Array
				var newArr = []; 
				for (var index in taskArray) {  if( taskArray[index] ) {  newArr.push( taskArray[index] ); }  }  
				taskArray = newArr;
			//Filter Array
				if(taskArray != ''){
					if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
					showProgress();
					params = "taskIDs="+taskArray+"&projectID="+projectID+"&strangeID="+Math.random();
					xmlhttp.open("POST", "inspection_close_bulk.php", true);
					xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
					xmlhttp.setRequestHeader("Content-length", params.length);
					xmlhttp.setRequestHeader("Connection", "close");
					xmlhttp.onreadystatechange=function(){
						if (xmlhttp.readyState==4 && xmlhttp.status==200){
							hideProgress();
							if(xmlhttp.responseText == 'Done'){
								jAlert('Selected task closed successfully !');
								validateAndSubmit();
							}else{
								jAlert('Error in updating record try after some time !');
								validateAndSubmit();
							}
						}
					}
					xmlhttp.send(params);
				}else{
					jAlert('You must select at least one task to perform this action !');
					document.getElementById('checkall').checked = false;
					toggleCheck(document.getElementById('checkall'));
				}
		}else{
				return false;
		}
	});
}
function toggleCheck(obj, tableID){
	var checkedStatus = obj.checked;
	$('#'+tableID+' tbody tr').find('td:first :checkbox').each(function () {
		if(!$(this).is(':disabled')){
			$(this).prop('checked', checkedStatus);
		}
	});
}
function holeProjectChecked(obj, tableID){
	var tableIdArray = tableID.split(",");
	var checkedStatus = obj.checked;
	for(i = 0; i < tableIdArray.length; i++){
		$('#f'+tableIdArray[i]+' tbody tr').find('td:first :checkbox').each(function () {
			if(!$(this).is(':disabled')){
				$(this).prop('checked', checkedStatus);
			}
		});
		$('#checkall_'+tableIdArray[i]).prop('checked', checkedStatus);
	}
}
function editThis(taskId, tableID){
	modalPopup(align, 100, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_task_qa_search.php?task_id='+taskId+'&tableID='+tableID+'&uniqueId='+Math.random(), loadingImage);
}
function editTaskSubmit(){
	showProgress();
	$.post('edit_task_qa_search.php?antiqueID='+Math.random(), $('#editTaskForm').serialize()).done(function(data) {
		hideProgress();
		if(data != 'Duplicate task'){
			var jsonResult = JSON.parse(data);	
			$('#status'+jsonResult.task_id).text(jsonResult.status);	
			$('#comment'+jsonResult.task_id).text(jsonResult.comments);	
			$("#errorHolderDynmPara").html('Task Data Edit Successfully');
			$("#errorHolderDynm").show();
			setTimeout(function(){$("#errorHolderDynm").hide('slow')}, 3000);
			closePopup(300);
			validateAndSubmit();
		}else{
			$("#errorHolderDynmPara").html('Duplicate task, task not updated');
			$("#errorHolderDynm").show();
			setTimeout(function(){$("#errorHolderDynm").hide('slow')}, 3000);
			closePopup(300);
		}
	});
}
function add_inspection_popup(task_id, status, str, existFlag){
	if(status=='Failed'){	return;	}
	var url = 'includes/add_defect_non_conformance.php';
	if(task_id!=""){
		var projNameQA = document.getElementById('projNameQA').value;
		if(existFlag)
			window.location = '?sect=edit_non_confirmance_inspection&id='+str;
		else
			window.location = '?sect=add_non_confirmance_inspection&id='+str;
	}
}
<?php
if($rowCount4Sel == 1){?>
	startAjaxQA(<?=$autoProj?>);
<?php }
if($projectName != ''){?>
	startAjaxQA('<?php echo $projectName;?>');
<?php if($locationQA != ''){?>
	subLocate1QA(<?php echo $locationQA;?>);
<?php }?>
<?php if($subLocationQA1 != ''){?>
	subLocate2QA(<?php echo $subLocationQA1;?>);
<?php }?>
<?php if($subLocationQA2 != ''){?>
//	subLocate3QA(<?php echo $subLocationQA2;?>);
//	setTimeout(subLocateitr(<?php echo $subLocationQA2;?>), 10000);
	
	subLocate3QACallback(<?php echo $subLocationQA2;?>, subLocateitrDyn);
	function subLocateitrDyn(){
		subLocateitr(<?php echo $subLocationQA2;?>);
	}
<?php } ?>
<?php if($subLocationQA2 != ''){?>
	//subLocateitr(<?=$subLocationQA2?>);
<?php } ?>
	window.setTimeout(validateAndSubmit, 6000);
<?php }?>

function addAdhocITRForm(){
	modalPopup(align, top1, 550, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_adhoc_itr.php?name='+Math.random(), loadingImage);
}
function addAdhocITR(){
	var elementNumber = $('#elementNumber').val().trim();
	var postFix = $("#subLocationQA2 option:selected").text().trim();
	var projectID = $("#projNameQA").val();
	var locationID = $("#locationQA").val();
	var locationParentID = $("#subLocationQA1").val();
	var subLocationID = $("#subLocationQA2").val();
	var newITRName = elementNumber+" "+postFix;
	showProgress();
	$.post("add_adhoc_itr.php", {newITRName:newITRName, projectID:projectID, locationID:locationID, locationParentID:locationParentID, subLocationID:subLocationID, uniqueId:Math.random()}).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
//			jAlert(jsonResult.msg);
			$("#ahocitr").append('<option value="'+jsonResult.locationData[0]+'" selected="selected">'+jsonResult.locationData[1]+'</option>');
			var option4SubLoc = "<option value=''>Select</option>";
			for(var i=0; i<jsonResult.subLocData.length; i++){
				option4SubLoc += '<option value="'+jsonResult.subLocData[i][0]+'">'+jsonResult.subLocData[i][1]+'</option>';
			}
			$("#subLocationQA3").html(option4SubLoc);
			validateAndSubmit();
			closePopup(300);
		}
	});	
}
$('#subLocationQA1, #subLocationQA2, #subLocationQA3').live("change", function(){
	$("#container_progress").html('');
});
</script>
<style>
li{ list-style:none; text-align:center; margin:5px; float:left; color:#000; }
li:last-child{ margin-right:0; }
.tool{ border:1px solid #bdc7b6; border-radius:2px; background: #fcfff4; /* Old browsers */ background: -moz-linear-gradient(top, #fcfff4 1%, #b3bead 100%); /* FF3.6+ */ background: -webkit-gradient(linear, left top, left bottom, color-stop(1%,#fcfff4), color-stop(100%,#b3bead)); /* Chrome,Safari4+ */ background: -webkit-linear-gradient(top, #fcfff4 1%,#b3bead 100%); /* Chrome10+,Safari5.1+ */ background: -o-linear-gradient(top, #fcfff4 1%,#b3bead 100%); /* Opera 11.10+ */ background: -ms-linear-gradient(top, #fcfff4 1%,#b3bead 100%); /* IE10+ */ background: linear-gradient(to bottom, #fcfff4 1%,#b3bead 100%); /* W3C */ filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcfff4', endColorstr='#b3bead',GradientType=0 ); /* IE6-9 */ }
.toll a{ display:block; height:24px; }
.tool a img{ margin-top:4px; padding:3px;}
</style>
<div id="setSession"></div><div id="userRole"></div>