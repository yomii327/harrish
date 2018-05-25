<?php error_reporting(0);
$builder_id=$_SESSION['ww_builder_id'];
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">
		window.location.href="<?=HOME_SCREEN?>";
	</script>
<?php }
function FillSelectBox($field, $table, $where, $group){
	$q="select $field from $table where $where GROUP BY $group";
	$q=mysql_query($q);
	while($q1=mysql_fetch_array($q)){
		echo '<option value="'.$q1[0].'">'.$q1[1].'</option>';
	}
}?>
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
#DRF, #DRT, #FBDF, #FBDT{ background:#FFF; cursor:default; height:20px; -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; }
</style>
<!-- Date Picker files start here -->
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.full.1.3.js"></script>
<script type="text/javascript">
window.onload = function(){
	Date1 = new JsDatePick({
		useMode:2,
		target:"DRF",
		dateFormat:"%d-%m-%Y"
	});
	Date2 = new JsDatePick({
		useMode:2,
		target:"DRT",
		dateFormat:"%d-%m-%Y"
	});
	Date3 = new JsDatePick({
		useMode:2,
		target:"FBDF",
		dateFormat:"%d-%m-%Y"
	});
	Date4 = new JsDatePick({
		useMode:2,
		target:"FBDT",
		dateFormat:"%d-%m-%Y"
	});
};
</script>
<!-- Date Picker files start here -->
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
function showProgress() {
	if (!spinnerVisible) {
		$("div#spinner").fadeIn("fast");
		spinnerVisible = true;
	}
};
function hideProgress() {
	if (spinnerVisible) {
		var spinner = $("div#spinner");
		spinner.stop();
		spinner.fadeOut("fast");
		spinnerVisible = false;
	}
};
</script>
</head>
<body id="dt_example">
<script language="javascript" type="text/javascript">
function startAjax(val){
	AjaxShow("POST","progress.php?type=location && proID="+val,"ShowLocation");
	AjaxShow("POST","progress.php?type=issuedTo && proID="+val,"ShowIssuedTo");
	AjaxShow("POST","progress.php?type=setSession && proID="+val,"setSession");
} 
function SubLoc(val){
	AjaxShow("POST","progress.php?type=sublocation && proID="+val,"ShowSubLocation");
} 
function SubLoc_sub(val){
	AjaxShow("POST","progress.php?type=SubLoc_sub&& proID="+val, "ShowSubLocation_sub_td");
} 
function toggleFolder (folderid){
	$("#"+folderid).toggle();
}

function checkDates(date1, date2, element){
	var obj = date1.value;
	var obj1 =  date2.value;
	if(obj!='' || obj1!=''){
		if(obj=='' && obj1!=''){
			jAlert('Please select '+element+' form date first !');
			return false;		
		}if(obj!='' && obj1==''){
			jAlert('Please select '+element+' to date !');
			return false;		
		}else{
			var fromDate = new Date(obj.substr(6,4), obj.substr(3,2), obj.substr(0,2));
			var toDate = new Date(obj1.substr(6,4), obj1.substr(3,2), obj1.substr(0,2));
			if((toDate.getTime() - fromDate.getTime()) < 0){jAlert(element+' To Date in Not Less Than Form Date !');return false;}
		}
	}
}

function validateAndSubmit(flag){
	flag = typeof flag !== 'undefined' ? flag : 'NA';
	try{
		var params = "";
		var progName = document.getElementById("projName").value;
		var location = document.getElementById("location").value;
		var sublocation = document.getElementById("sublocation").value;
		var subLocation_sub = document.getElementById("SubLoc_sub").value;
		var status = document.getElementById("status").value;
		
		var searchKeyword = document.getElementById("searchKeyword").value;

		var DRF = document.getElementById("DRF").value;
		var DRT = document.getElementById("DRT").value;
		var FBDF = document.getElementById("FBDF").value;
		var FBDT = document.getElementById("FBDT").value;
		var issuedTo = document.getElementById('issuedTo').value;
		
		if(progName == ''){ jAlert('Project Name Should be Selected !'); return false;}
		if(location == ''){	jAlert('Location Name Should be Selected !');	return false;}
		
		var dateChackRaised = checkDates(document.getElementById('DRF'), document.getElementById('DRT'), 'In Start Date field');
		var dateChackFixed = checkDates(document.getElementById('FBDF'), document.getElementById('FBDT'), 'In End Date field');
		if(dateChackRaised === false){	return false;	}
		if(dateChackFixed === false){	return false;	}
		
		params = "SearchInsp="+1+"&projName=" + progName + "&location=" + location + "&sublocation=" + sublocation + "&subLocation_sub=" + subLocation_sub + "&status=" + status + "&issuedTo="+issuedTo + "&DRF=" + DRF + "&DRT=" + DRT + "&FBDF=" + FBDF + "&FBDT=" + FBDT + "&searchKeyword=" + encodeURIComponent(searchKeyword);
		if (window.XMLHttpRequest){
			xmlhttp=new XMLHttpRequest();
		}else{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		document.getElementById("container_progress").innerHTML = '';
		showProgress();
		var url = 'progress_ajax_result.php';
		
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
//		xmlhttp.open("GET", url, true);
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				hideProgress();
				if(xmlhttp.responseText == 'No Record Found'){
					jAlert(xmlhttp.responseText);
				}else{
					if(flag == 'Y' || flag == 'N'){
						if(flag == 'Y'){
							jAlert('Selected task status updated successfully !');
						}
						if(flag == 'N'){
							jAlert('Error in updating record try after some time !');
						}
					}
					document.getElementById("container_progress").innerHTML=xmlhttp.responseText;
				}
			}
		}
		xmlhttp.send(params);
	}catch(e){
		jAlert(e.message);
	}
}
function resetSelectBoxProj(){
	$('select#location').html('<option value="">Select</option>');
	$('select#sublocation').html('<option value="">Select</option>');
	$('select#SubLoc_sub').html('<option value="">Select</option>');
}
function resetSelectBoxLoc(){
	$('select#sublocation').html('<option value="">Select</option>');
	$('select#SubLoc_sub').html('<option value="">Select</option>');
}
function resetSelectBoxSubLoc(){
	$('select#SubLoc_sub').html('<option value="">Select</option>');
}
function bulkChangeDate(){	
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'update_bulkdate_promon_search.php?uniqueId='+Math.random(), loadingImage, callDate);
}
function submitBulkChangeDate(){
	var taskArray = new Array();
	var projectID = document.getElementById('projName').value;
	var taskCount = document.allTaskTable.elements["taskID[]"].length;
	if(taskCount === undefined){
		var taskId = document.getElementById('taskID');
		if(taskId.checked){
			taskArray[0] = taskId.value;
		}
	}else{
		for(var i=1; i<taskCount; i++){
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
	$('#taskArray').val(taskArray);
//Filter Array
	if(taskArray != ''){
		$.post('update_bulkdate_promon_search.php?antiqueID='+Math.random()+'&taskArray='+taskArray, $('#editTaskForm').serialize()).done(function(data) {
			hideProgress();
			var jsonResult = JSON.parse(data);	
			if(jsonResult.status){
				hideProgress();
				var jsonResult = JSON.parse(data);	
				jAlert('Task Data Edit Successfully');
				closePopup(300);
				validateAndSubmit();
			}else{
				jAlert('Duplicate task, task not updated');
			}
		});
	}else{
		jAlert('You must select at least one task to perform this action !');
		document.getElementById('checkall').checked = false;
		toggleCheck(document.getElementById('checkall'));
	}
}
function reSubmitDate(){
	var startDate = document.getElementById("startDate").value;
	var endDate = document.getElementById("endDate").value;
	if(startDate == "" || endDate == "" ){
		jAlert('Please select both date');
		return;
	}
	var dateChackRaised = checkDates(document.getElementById('startDate'), document.getElementById('endDate'), 'In Start Date field');
	if(dateChackRaised === false){	return false;	}
	
	var taskArray = new Array();
	var projectID = document.getElementById('projName').value;
	var taskCount = document.allTaskTable.elements["taskID[]"].length;
	if(taskCount === undefined){
		var taskId = document.getElementById('taskID');
		if(taskId.checked){
			taskArray[0] = taskId.value;
		}
	}else{
		for(var i=1; i<taskCount; i++){
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
		$.post('update_bulkdate_promon_search.php?bypass=Y&taskArray='+taskArray+'&antiqueID='+Math.random(), $('#editTaskForm').serialize()).done(function(data) {
			hideProgress();
			var jsonResult = JSON.parse(data);	
			jAlert('Task Data Edit Successfully');
			closePopup(300);
			validateAndSubmit();
		});
	}else{
		jAlert('You must select at least one task to perform this action !');
		document.getElementById('checkall').checked = false;
		toggleCheck(document.getElementById('checkall'));
	}
}</script>
<br/>
<?php
$projectName=''; 
$locationName='';
$subLocationQA1 = '';
$subLocationQA2 = '';
$subLocationQA3 = '';
$projectName = $_SESSION['pm']['projName'];
$locationName = $_SESSION['pm']['location'];
$sublocationName = $_SESSION['pm']['sublocation'];
$subSublocationName = $_SESSION['pm']['SubLoc_sub'];
?>

<div class="content_hd1" style="background-image:url(images/progress_monitoring_header.png);">&nbsp;</div><br clear="all" />
	<div class="search_multiple" style="border:1px solid; text-align:center;width:960px;margin-left: 20px;">
		<table width="900" cellpadding="0" cellspacing="5" border="0">
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="">Project Name <span class="reqire">*</span></td>
					<td colspan="2">
						<select name="projName" id="projName" class="select_box" onChange="startAjax(this.value);resetSelectBoxProj();" >
							<option value="">Select</option>
							<?php 
							if(!empty($_SESSION['ww_builder']['user_name']) && $_SESSION['ww_builder']['user_name'] == "jones"){
								$q = "SELECT project_id, project_name FROM user_projects WHERE is_deleted=0 GROUP BY project_name";
							}
							else{
								$q = "SELECT project_id, project_name FROM user_projects WHERE user_id = '".$builder_id."' AND is_deleted = 0 GROUP BY project_name";
							}							
							$res = mysql_query($q);
							while($q1 = mysql_fetch_array($res)){
								$selectBox = '<option value="'.$q1[0].'"';
								if($projectName == $q1[0]){
									$selectBox .= 'selected="selected"';
								}
								$selectBox .= '>'.$q1[1].'</option>';
								echo $selectBox;
							}#FillSelectBox("project_id,project_name", "user_projects", "project_id >='1' and user_id = ".$builder_id." and is_deleted = 0", "project_name"); 
							?>
						</select>
                    </td>
					<td align="left" valign="top" nowrap="nowrap" style="">Location <span class="reqire">*</span></td>
					<td colspan="2" id="ShowLocation">
						<select name="location" id="location" class="select_box" onChange="SubLoc(this.value);">
							<option value="">Select</option>
						</select>
					</td>
				</tr>
				<tr>
                
                	<td align="left" valign="top" nowrap="nowrap" style="">Sub Location </td>
					<td colspan="2" id="ShowSubLocation">
						<select name="sublocation" id="sublocation" class="select_box">
							<option value="">Select</option>
						</select>
					</td>
					<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 1</td>
					<td colspan="2" id="ShowSubLocation_sub_td">
						<select name="SubLoc_sub" id="SubLoc_sub" class="select_box">
							<option value="">Select</option>
						</select>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="">Current Status</td>
					<td colspan="2">
						<select name="status" id="status" class="select_box">
							<option value="">Select</option>
							<option value="NA" <?php if(isset($_SESSION['pm']['status'])){ if($_SESSION['pm']['status'] == 'NA'){ echo 'selected="selected"'; }}?>>NA</option>
							<option value="In progress" <?php if(isset($_SESSION['pm']['status'])){ if($_SESSION['pm']['status'] == 'In progress'){ echo 'selected="selected"'; }}?>>In progress</option>
							<option value="Behind" <?php if(isset($_SESSION['pm']['status'])){ if($_SESSION['pm']['status'] == 'Behind'){ echo 'selected="selected"'; }}?>>Behind</option>
							<option value="Complete" <?php if(isset($_SESSION['pm']['status'])){ if($_SESSION['pm']['status'] == 'Complete'){ echo 'selected="selected"'; }}?>>Complete	</option>
							<option value="Signed off" <?php if(isset($_SESSION['pm']['status'])){ if($_SESSION['pm']['status'] == 'Signed off'){ echo 'selected="selected"'; }}?>>Signed off</option>
						</select>
					</td>
                	<td align="left" valign="top" nowrap="nowrap" style="">Issue To </td>
					<td colspan="2"id="ShowIssuedTo" >
						<select name="issuedTo" id="issuedTo" class="select_box" >
							<option value="">Select</option>
						</select>
					</td>
				</tr>
				<tr>
                	<td align="left" valign="top" nowrap="nowrap" style="">Search Keyword</td>
					<td colspan="2" id="ShowSubLocation">
						<input type="text" name="searchKeyword" id="searchKeyword" 
						<?php if(isset($_SESSION['pm']['searchKeyword'])){ echo 'value="'.$_SESSION['pm']['searchKeyword'].'"'; }?>
						class="input_small" style="padding:0 5px 0 19px;" />
					</td>
                	<td align="left" valign="top" nowrap="nowrap" style="">&nbsp;</td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="">Start Date</td>
					<td colspan="2" align="left" nowrap="nowrap" style="">From 
						<input name="DRF" type="text" value="" size="7" id="DRF"
						<?php if(isset($_SESSION['pm']['DRF'])){ echo 'value="'.$_SESSION['pm']['DRF'].'"'; }?>
						 readonly="readonly"/>
						To 
						<input name="DRT" type="text" id="DRT" size="7" 
						<?php if(isset($_SESSION['pm']['DRT'])){ echo 'value="'.$_SESSION['pm']['DRT'].'"'; }?>
						readonly="readonly" /><a href="javascript:void();" title="Clear date raised"><img src="images/redCross.png" onClick="clearDateRaised();" /></a>
					</td>
					<td align="left" valign="top" nowrap="nowrap" style="">End Date</td>
					<td colspan="2" align="left" nowrap="nowrap" style="">From 
						<input name="FBDF" type="text" id="FBDF" size="7" 
						<?php if(isset($_SESSION['pm']['FBDF'])){ echo 'value="'.$_SESSION['pm']['FBDF'].'"'; }?>
						readonly="readonly" />
						To 
					    <input name="FBDT" type="text" id="FBDT" size="7" 
						<?php if(isset($_SESSION['pm']['FBDT'])){ echo 'value="'.$_SESSION['pm']['FBDT'].'"'; }?>
						readonly="readonly" /><a href="javascript:void();" title="Clear fixed by date"><img src="images/redCross.png" onClick="clearFixedByDate();" /></a>
		
					</td>
				</tr>
				<tr>
					<td colspan="3" align="left">&nbsp;</td>
					<td align="left">
					<div id="report_timer" style=""></div>
					</td>
					<td>
					  <!--input type="hidden" value="create" name="sect" id="sect" /-->
						<!-- <input name="SearchInsp" type="button" class="submit_btn" id="button" value="" style="background-image:url(images/search_btn_web.png); width:113px; height:46px;" onClick="validateAndSubmit();" /> -->
						<input name="SearchInsp" type="button" class="green_small" id="button" value="Search" style="height:46px;" onClick="validateAndSubmit();" />
					</td>
				</tr>
	  </table>
	</div>
<div id="container_progress" width="980px;">
&nbsp;
</div>
<div id="setSession"></div>
<script type="text/javascript">
function closetaskids(){
	var setStatus = document.getElementById('statusChange').value;
	if(setStatus == ''){
		jAlert('Please select status for update !');
		return false;
	}
	var taskArray = new Array();
	var projectID = document.getElementById('projName').value;
	var taskCount = document.allTaskTable.elements["taskID[]"].length;
	if(taskCount === undefined){
		var taskId = document.getElementById('taskID');
		if(taskId.checked){
			taskArray[0] = taskId.value;
		}
	}else{
		for(var i=1; i<taskCount; i++){
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
		params = "taskIDs="+taskArray+"&projectID="+projectID+"&setStatus="+setStatus+"&antiqueID="+Math.random();
		xmlhttp.open("POST", "inspection_close_bulk.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				if(xmlhttp.responseText == 'Done'){
					validateAndSubmit('Y');
				}else{
					validateAndSubmit('N');
				}
			}
		}
		xmlhttp.send(params);
	}else{
		jAlert('You must select at least one task to perform this action !');
		document.getElementById('checkall').checked = false;
		toggleCheck(document.getElementById('checkall'));
	}
}
function toggleCheck(obj, tableID){
	var checkedStatus = obj.checked;
	if($('#holeTask').attr('checked')){
		$('#holeTask').prop('checked', false);
	}
	$('#'+tableID+' tbody tr').find('td:first :checkbox').each(function () {
		if(!$(this).is(':disabled')){
			$(this).prop('checked', checkedStatus);
		}
	});
	checkSelectAllchecked();
}
function holeProjectUnchecked(obj){
	var checkProstr = document.getElementById('checkProstr').innerHTML;
	var checkBoxIdArray = checkProstr.split(",");
	var checkedStatus = false;
	for(i = 0; i < checkBoxIdArray.length; i++){
		$('#'+checkBoxIdArray[i]).prop('checked', checkedStatus);
	}
}
function holeProjectChecked(obj){
	var checkProstr = document.getElementById('checkProstr').innerHTML;
	var checkBoxIdArray = checkProstr.split(",");
	var checkedStatus = obj.checked;
	for(i = 0; i < checkBoxIdArray.length; i++){
		$('#'+checkBoxIdArray[i]).prop('checked', checkedStatus);
	}
	if(checkedStatus === false){
		highlightUnchecked();
	}else{
		highlightChecked();
	}
}
function highlightUnchecked(){
	var checkHighProstr = document.getElementById('checkHighProstr').innerHTML;
	var checkBoxIdArray = checkHighProstr.split(",");
	var checkedStatus = false;
	for(i = 0; i < checkBoxIdArray.length; i++){
		$('#'+checkBoxIdArray[i]).prop('checked', checkedStatus);
	}
}
function highlightChecked(){
	var checkHighProstr = document.getElementById('checkHighProstr').innerHTML;
	var checkBoxIdArray = checkHighProstr.split(",");
	var checkedStatus = true;
	for(i = 0; i < checkBoxIdArray.length; i++){
		$('#'+checkBoxIdArray[i]).prop('checked', checkedStatus);
	}
}
function clearDateRaised(){
	document.getElementById('DRF').value = '';
	document.getElementById('DRT').value = '';
}
function clearFixedByDate(){
	document.getElementById('FBDF').value = '';
	document.getElementById('FBDT').value = '';
}
function checkUncheckParent(checkID, tableID){
	if(!$(checkID).is(':checked')){
		if($('#checkall'+tableID).is(':checked')){
			$('#checkall'+tableID).prop('checked', false);
			checkSelectAllchecked();
		}
	}else{
		var checkstatus = true;
		$('#f'+tableID+' tbody tr').find('td:first :checkbox').each(function () {
			if(!$(this).is(':checked')){
				checkstatus = false;
			}
		});
		if(checkstatus){
			$('#checkall'+tableID).prop('checked', checkstatus);
			checkSelectAllchecked();
		}else{
			$('#checkall'+tableID).prop('checked', checkstatus);
		}
	}
}
function checkSelectAllchecked(){
	var checkstatus = true;
	var checkProstr = $('#checkHighProstr').text();
	var checkBoxIdArray = checkProstr.split(",");
	for(i = 0; i < checkBoxIdArray.length; i++){
		if(!$('#'+checkBoxIdArray[i]).is(':checked')){
			checkstatus = false;
		}
	}
	if(checkstatus){
		$('#holeTask').prop('checked', checkstatus);
	}else{
		$('#holeTask').prop('checked', checkstatus);
	}
}
function editThis(taskId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_task_promon_search.php?progress_id='+taskId+'&uniqueId='+Math.random(), loadingImage, callDate);
}
function editTaskSubmit(){
	var perVal = document.getElementById('pechange').value,
		progressID = document.getElementById('progressID').value,
		statuscurr = document.getElementById('statusEdit').value;
	progressID = parseInt(progressID);
	
	var status = '';
	var statusClass = 'na';
	switch(true){
		case (perVal == 95):
			status = 'Complete';
			statusClass = 'complete';
		break;
		case (perVal == 100):
			status = 'Signed off';
			statusClass = 'signed_off';
		break;
		case (perVal >= 0 && perVal < 95):
			if(statuscurr == 'Behind'){
				status = 'Behind';
				statusClass = 'behind';
			}else{
				status = 'In progress';
				statusClass = 'in_progress';
			}
		break;
		default:
			status = 'In progress';
			statusClass = 'in_progress';
	}
	
	showProgress();
	$.post('edit_task_promon_search.php?antiqueID='+Math.random(), $('#editTaskForm').serialize()).done(function(data) {
		hideProgress();
		if(data != 'Duplicate task'){
			var jsonResult = JSON.parse(data);	
			//console.log(jsonResult);
			if(jsonResult.errorFlag){
				var res = confirm(jsonResult.startDateError+'\n'+jsonResult.endDateError+'\nWould you like to continue ?');
				if(res === true){
					selectParcentage(progressID,perVal);
					$('#cur_status_'+progressID).html(status);
					$('#tr_'+progressID).attr('class', statusClass);
					reSubmit();
				}else{
					jAlert('Please Select New Date');
				}
			} else {
				selectParcentage(progressID,perVal);
				$('#cur_status_'+progressID).html(status);
				$('#tr_'+progressID).attr('class', statusClass);
				
				jAlert('Task Data Edit Successfully');
				closePopup(300);
			}
		}else{
			jAlert('Duplicate task, task not updated');
		}
	});
}
function selectParcentage(progressID,val) {
    var sel = document.getElementById('list_pechange_'+progressID);
    for(var i = 0, j = sel.options.length; i < j; ++i) {
		var selVal = parseInt(sel.options[i].innerHTML);
        if(selVal==val) {
			sel.selectedIndex = i;
			break;
        }
    }
}
function reSubmit(){
	$.post('edit_task_promon_search.php?antiqueID='+Math.random()+'&bypass=Y', $('#editTaskForm').serialize()).done(function(data) {
		hideProgress();
		if(data != 'Duplicate task'){
			var jsonResult = JSON.parse(data);	
			console.log(jsonResult);
			$('#per'+jsonResult.progress_id).text(jsonResult.percentage);	
			$('#status'+jsonResult.progress_id).text(jsonResult.status);	
			$('#row'+jsonResult.progress_id).css("background-color", jsonResult.row_color);	
			
			jAlert('Task Data Edit Successfully');
			closePopup(300);
		}else{
			jAlert('Duplicate task, task not updated');
		}
	});					
}

function setStatus(perVal){
	perVal = parseInt(perVal);
	var status = '';
	var statuscurr = $('#statusEdit').val();
	switch(true){
		case (perVal == 95):
			status = 'Complete';
		break;
		case (perVal == 100):
			status = 'Signed off';
		break;
		case (perVal >= 0 && perVal < 95):
			if(statuscurr == 'Behind')
				status = 'Behind';
			else
				status = 'In progress';
		break;
		default:
			status = 'In progress';
	}
	if(isNaN(perVal)){
		status = '';
	}
	$('#statusEdit').val(status);
}
function setPetchange(statusVal){
	var percentage = '';
	var currPer = parseInt($('#pechange').val());
	switch(statusVal){
		case 'Complete':
			percentage = 95;
		break;
		case 'Signed off':
			percentage = 100;
		break;
		case 'In progress':
			if(currPer == 0 || isNaN(currPer)){
				percentage = 25;
			}else{
				percentage = currPer;
			}
		break;
		case 'Behind':
			if(currPer == 95 || currPer == 100){
				percentage = 25;
			}else{
				percentage = currPer;
				if(isNaN(currPer)){
					percentage = 0;
				}
			}
		break;
		default:
			percentage = '';
	}
	$('#pechange').val(percentage);
}
function callDate(){
	$('#projectID').val($('#projName').val());
	new JsDatePick({
		useMode:2,
		target:"startDate",
		dateFormat:"%d/%m/%Y"
	});
	new JsDatePick({
		useMode:2,
		target:"endDate",
		dateFormat:"%d/%m/%Y"
	});
}
//Change task percentages.
function setPercetanges(perVal, progressId){
	//Start:-Update status according to percentages value.
	perVal = parseInt(perVal);
	progressId = parseInt(progressId);			
	var statuscurr = $('#cur_status_'+progressId).text();
	var status = '';
	var statusClass = 'na';
	switch(true){
		case (perVal == 95):
			status = 'Complete';
			statusClass = 'complete';
		break;
		case (perVal == 100):
			status = 'Signed off';
			statusClass = 'signed_off';
		break;
		case (perVal >= 0 && perVal < 95):
			if(statuscurr == 'Behind'){
				status = 'Behind';
				statusClass = 'behind';
			}else{
				status = 'In progress';
				statusClass = 'in_progress';
			}
		break;
		default:
			status = 'In progress';
			statusClass = 'in_progress';
	}
	if(isNaN(perVal)){
		status = '';
	}
	$('#cur_status_'+progressId).html(status);
	
	//End:-Update status according to percentages value.
	var params = {
		antiqueID: Math.random(),
		percentage: perVal,
		status: status,
		progress_id: progressId
	};	
	
	$('#spinner').show('fast');
	$.post(['b_progress_percentage.php'].join(), params).done(function(response){
		$('#spinner').hide('fast');
		var data = JSON.parse(response);
		if(data.status==true){
			$('#tr_'+progressId).attr('class', statusClass);
			jAlert(data.message);
		}
	});
}
</script>
<style>
	#optionBar li{ float:left; list-style: none; }
	ul#optionBar {margin-left:-50px;}
	table.simpleTable td { color:#000000;}
	.error-edit-profile-red{ background: url("images/bg-error-edit-profile-red.png") no-repeat scroll 0 0 transparent; color: #000; font-size: 11px; margin: 1px 0 2px 3px; padding: 10px 3px 8px 4px; width: 240px; text-shadow:none; }
	.pselect-box {
		background: #F1F1F1;
		border: 1px solid #DDDDDD;
		color: #000000;
		cursor:pointer;
	}
	.complete {background:#00ff00}
	.behind {background:#ff0000}
	.signed_off {background:#3399ff}
	.in_progress {background:#FFFF00}
	.na {background:#CCCCCC}
</style>
<script language="javascript" type="text/javascript">
var projectId = '';
projectId = <?=$projectName;?>;
AjaxShow("POST","progress.php?type=location && proID="+projectId,"ShowLocation");
AjaxShow("POST","progress.php?type=issuedTo && proID="+projectId,"ShowIssuedTo");
AjaxShow("POST","progress.php?type=setSession && proID="+projectId,"setSession");
var locationId = '';
<?php if($locationName != ''){?>
	locationId = <?=$locationName;?>;
	AjaxShow("POST","progress.php?type=sublocation && proID="+locationId,"ShowSubLocation");
<?php }?>
<?php if($sublocationName != ''){?>
	subLocationId = <?=$sublocationName;?>;
	AjaxShow("POST","progress.php?type=SubLoc_sub&& proID="+subLocationId, "ShowSubLocation_sub_td");
<?php }?>
</script>
