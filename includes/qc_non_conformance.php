<?php $builder_id=$_SESSION['ww_builder_id'];
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
$projNameArr = array();
$q = "SELECT project_id, project_name FROM user_projects WHERE user_id = '".$builder_id."' AND is_deleted = 0";
$res = mysql_query($q);
while($q1 = mysql_fetch_array($res)){
	$projNameArr[$q1[0]] = $q1[1];
}

$selectedProId 			= $_SESSION['qaChecklistProId'];
$selectedChecklistId 	= $_SESSION['qaChecklistId'];
$selectedLocationId 	= $_SESSION['qaChecklistLocationId'];
$selectedSubLocationId 	= $_SESSION['qaChecklistSubLocationId'];
$selectedSubLocationId1 = $_SESSION['qaChecklistSubLocationId1'];
$selectedSubLocationId2 = $_SESSION['qaChecklistSubLocationId2'];

if(!empty($selectedProId) && $selectedProId > 0){
	$query = "SELECT id,checklist_name FROM check_list_items_project WHERE project_id = $selectedProId AND is_deleted = 0";
	$result = mysql_query($query);
	$optChecklist = '<option value="All">All Select</option>';
	while($aRow = mysql_fetch_array($result)){
		$selected = '';
		if($aRow['id'] == $selectedChecklistId){
			$selected = 'selected="selected"';
		}
		$optChecklist .= "<option value=".$aRow['id']." " .$selected.">".$aRow['checklist_name']."</option>";
	}

	$query1 = "SELECT location_id,location_title FROM project_locations WHERE project_id = $selectedProId AND is_deleted = 0 AND location_parent_id = 0";
	$result1 = mysql_query($query1);
	$optLocation = '';
	while($aRow1 = mysql_fetch_array($result1)){
		$selected1 = '';
		if($aRow1['location_id'] == $selectedLocationId){
			$selected1 = 'selected="selected"';
		}
		$optLocation .= "<option value=".$aRow1['location_id']." " .$selected1.">".$aRow1['location_title']."</option>";
	}
}

	if(!empty($selectedLocationId)){
		$subLocationData = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'is_deleted = 0 AND location_parent_id = '.$selectedLocationId.' AND project_id = '.$selectedProId);
	}

	if(!empty($selectedSubLocationId)){
		$sub_subLocationData = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'is_deleted = 0 AND location_parent_id = '.$selectedSubLocationId.' AND project_id = '.$selectedProId);
	}

	if(!empty($selectedSubLocationId1)){
		$subSubLocationData3 = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'is_deleted = 0 AND location_parent_id = '.$selectedSubLocationId1.' AND project_id = '.$selectedProId);
	}

#}?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" type="text/css"/>
<title>Report</title>
<style type="text/css">
@import "css/jquery.datepick.css";
table td { padding: 5px; }
table { margin-left: 10px; }
#inspTable {color:black;}
.dateCalender { background: #FFF; cursor: default; height: 20px; -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; }
#optionBar li { float: left; list-style: none; }
ul#optionBar { margin-left: -50px; }
table.simpleTable td { color: #000000; }
.error-edit-profile-red { background: url("images/bg-error-edit-profile-red.png") no-repeat scroll 0 0 transparent; color: #000; font-size: 11px; margin: 1px 0 2px 3px; padding: 10px 3px 8px 4px; width: 240px; text-shadow: none; }
.roundCorner { color: #000000; }
.roundCorner table tr td:nth-child(1) { text-align: right; }
.roundCorner table tr td:nth-child(2) { text-align: center; }
.roundCorner table tr td:nth-child(3) { text-align: left; }
.error-edit-nonconf {background: transparent url("images/bg-error-edit-profile.png") no-repeat scroll 0 0;color: #000;font-size: 11px;margin: 1px 0 2px 3px;padding: 10px 3px 8px 4px;text-shadow: none;width: auto;
}
table.collapse { border-collapse: collapse; border: 1pt solid black; }
table.collapse td { border: 1pt solid black; padding: 2px; font-family:Arial, Helvetica, sans-serif; font-size:10px;}
div#mainContainer{ color:#000; width:800px; margin:auto; min-height:70px;; max-height:650px; overflow-y:scroll;}

.colOpen{ background:#FF1717 !important; }
tr.colOpen td.sorting_1{ background:#FF1717 !important; }
.colDraft{ background:#A9A9A9 !important; }
tr..colDraft td.sorting_1{ background:#A9A9A9 !important; }
.colClosed{ background:#0066CC !important; }
tr.colClosed td.sorting_1{ background:#0066CC !important; }
.colPending{ background:#FFFF00 !important; }
tr.colPending td.sorting_1{ background:#FFFF00 !important; }
.colFixed{ background:#009900 !important; }
tr.colFixed td.sorting_1{ background:#009900 !important; }

</style>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script language="javascript" type="text/javascript" src="js/quality_checklist_task.js"></script>
</head>
<body id="dt_example">
<br/>
<!-- <div class="content_hd1" style="background-image:url(images/quality_checklist_big.png);">&nbsp;</div> -->
<h1><img width="30" hspace="10" height="30" align="absmiddle" src="images/non_conformace_icon.png">Non Conformances</h1>
<br clear="all" />
<?php if(isset($_SESSION['inspection_added'])){ ?>
<div id="errorHolder" style="margin-left: 40px;margin-bottom: 20px;">
	<div class="success_r" style="height:35px;width:405px;">
		<p>	
			<?=$_SESSION['inspection_added'];?>
		</p>
	</div>
</div>
<?php unset($_SESSION['inspection_added']); } ?>
<div id="errorHolderDynm" style="margin-left: 40px;margin-bottom: 20px; display:none;">
	<div class="success_r" style="height:35px;width:405px;">
		<p id="errorHolderDynmPara"></p>
	</div>
</div>
<div class="search_multiple" style="border:1px solid; text-align:center;width:960px;margin-left: 20px;">
	<form name="qaSearchForm" id="qaSearchForm">		
		<!-- qa_itp_task_search -->
		<div id="qa_itp_task_search">
			<table width="900" cellpadding="0" cellspacing="5" border="0">
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="">Project Name <span class="reqire">*</span></td>
					<td colspan="2">
						<select name="projName" id="projName" class="select_box" onChange="getAllTask(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
							<option value="">Select</option>
							<?php foreach($projNameArr as $projID=>$projName){
								$selectBox = '<option value="'.$projID.'"';
									if($projID == $selectedProId){
										$selectBox .= 'selected="selected"';
									}
									$selectBox .= '>'.$projName.'</option>';
								echo $selectBox;
							}?> 
						</select>
						<div class="error-edit-profile" style="width:220px;display:none;" id="projectQAError">The project field is required</div>
					</td>
					<td align="left" valign="top" nowrap="nowrap" style="">Checklist <span class="reqire">*</span></td>
					<td colspan="2">
						<?php //$itpArr = array('Pre Pour' => 'Pre Pour ITP', 'Post Pour' => 'Post Pour ITP', 'Plastering' => 'Plastering ITP');?>
						<select name="checklist" id="checklist"  class="select_box" onChange="addChecklistInSession(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
							<option value="">Select</option>
							<?php echo $optChecklist; ?>
						</select>
						<div class="error-edit-profile" style="width:220px;display:none;" id="checklistError">The Checklist field is required</div>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="">Location <!-- <span class="reqire">*</span> --></td>
					<td colspan="2">
						<div id="showLocation">
						<select name="location" id="location" onChange="getSublocation(this.value);" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
							<option value="">Select</option>
							<?php echo $optLocation; ?>
						</select>
						</div>
						<div class="error-edit-profile" style="width:220px;display:none;" id="locationQAError">The Location field is required</div>
					</td>
					<td align="left" valign="top" nowrap="nowrap" style="">Sub Location</td>
					<td colspan="2">
						<div id="showSubLocation">
						<select name="subLocation" id="subLocation" onChange="getSublocation1(this.value)" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
							<option value="">Select</option>
							<?php foreach($subLocationData as $subLocation) { ?>
								<option value="<?=$subLocation['location_id']?>" <?php if($subLocation['location_id'] == $selectedSubLocationId){echo 'selected="selected"';} ?>><?=$subLocation['location_title']?></option>
							<?php } ?>
						</select>
						</div>
						<div class="error-edit-profile" style="width:220px;display:none;" id="subLocationQA1Error">The Sub Location 1 field is required</div>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 1</td>
					<td colspan="2">
						<div id="showSubLocation1">
						<select name="sub_subLocation" id="sub_subLocation" onChange="getSublocation2(this.value)" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
							<option value="">Select</option>
							<?php foreach($sub_subLocationData as $subSubLocation) { ?>
								<option value="<?=$subSubLocation['location_id']?>" <?php if($subSubLocation['location_id'] == $selectedSubLocationId1){echo 'selected="selected"';} ?>><?=$subSubLocation['location_title']?></option>
							<?php } ?>
						</select>
						</div>
						<div class="error-edit-profile" style="width:220px;display:none;" id="subLocationQA2Error">The Sub Location 2 field is required</div>
					</td>
					<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 2</td>
					<td colspan="2">
						<div id="showSubLocation2">
						<select name="subSubLocation3" id="subSubLocation3" onChange="getSublocation3(this.value)" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
							<option value="">Select</option>
							<?php foreach($subSubLocationData3 as $subSubLocation3) { ?>
								<option value="<?=$subSubLocation3['location_id']?>" <?php if($subSubLocation3['location_id'] == $selectedSubLocationId2){echo 'selected="selected"';} ?>><?=$subSubLocation3['location_title']?></option>
							<?php } ?>
						</select>
						</div>
					</td>
				</tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="">Status</td>
					<td colspan="2">
						<select name="status" id="status" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
							<option value="">Select</option>
							<option value="Open" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Open'){ echo 'selected="selected"'; }}?> >Open</option>
							<option value="Fixed" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Fixed'){ echo 'selected="selected"'; }}?>>Fixed</option>
							<option value="Close" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Closed'){ echo 'selected="selected"'; }}?> >Closed</option>
							<!-- <option value="NA" < ?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'NA'){ echo 'selected="selected"'; }}?> >NA</option> -->
						</select>
					</td>
					<td align="left" valign="top" nowrap="nowrap" style="">Search Keyword</td>
					<td colspan="2">
						<div id="search_keyword">
							<input type="text" name="searchKeyword" id="searchKeyword" class="input_small" style="width:220px;background-image:url(images/selectSpl.png);">
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="3" align="left">&nbsp;</td>
					<td align="left"><div id="report_timer" style=""></div></td>
					<td>
					<input type="hidden" name="sessionBack" id="sessionBack" value="Y" />
					<input name="SearchInsp" type="button" class="green_small" id="button" value="Search"  onClick="searchQualityChecklist();" style="height:40px;"/>
					<input name="generateReport" id="generateReport" type="button" class="green_small" value="Non Conformance Report"  onClick="submitFormNoConfReport();" style="height:40px;float: right;display: none;"/>
					</td>
				</tr>
			</table>
		</div>
		<!-- /.qa_itp_task_search -->
	</form>
</div>
<div class="demo_jui" id="show_inspection" ></div>
<?php include'data-table.php';?>

<br/>
<br/>
<div id="container_progress" style="width:980px;margin-top:-20px;">&nbsp;</div>
<br/>
<br/>

<div id="setSession"></div>
<div id="userRole"></div>

<style type="text/css">
	table tr td{
	 color:#000;
	}
</style>
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		var proj = $('#projName').val();
		if(proj != '' && proj > 0){
			searchQualityChecklist();
		}


	});

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
	
	function getAllTask(proId){
		$.post('ajaxFunctions.php?type=getAllTask&proId='+proId).done(function(data) {
			var jsonResult = JSON.parse(data);
			$selectOpt = '<option value="">Select</option>';
			$("#checklist").html($selectOpt);
			$("#location").html($selectOpt);
			$("#subLocation").html($selectOpt);
			$("#sub_subLocation").html($selectOpt);
			$("#subSubLocation3").html($selectOpt);

			$("#checklist").html(jsonResult.data['checklistData']);
			$("#location").html(jsonResult.data['locationData']);
		});
	}

	function addChecklistInSession(checklistId){
		$.post('ajaxFunctions.php?type=addChecklistId&checklistId='+checklistId).done(function(data) {
			var jsonResult = JSON.parse(data);
		});
	}

	function getSublocation(locationId){
		$.post('ajaxFunctions.php?type=getAllSublocation&locationId='+locationId+'&locType=1').done(function(data) {
			var jsonResult = JSON.parse(data);
			$selectOpt = '<option value="">Select</option>';
			$("#sub_subLocation").html($selectOpt);
			$("#subSubLocation3").html($selectOpt);

			$("#subLocation").html(jsonResult.data);
		});
	}

	function getSublocation1(locationId){
		$.post('ajaxFunctions.php?type=getAllSublocation&locationId='+locationId+'&locType=2').done(function(data) {
			var jsonResult = JSON.parse(data);
			$selectOpt = '<option value="">Select</option>';
			$("#subSubLocation3").html($selectOpt);

			$("#sub_subLocation").html(jsonResult.data);
		});
	}

	function getSublocation2(locationId){
		$.post('ajaxFunctions.php?type=getAllSublocation&locationId='+locationId+'&locType=3').done(function(data) {
			var jsonResult = JSON.parse(data);
			$("#subSubLocation3").html(jsonResult.data);
		});
	}

	function getSublocation3(locationId){
		$.post('ajaxFunctions.php?type=getAllSublocation&locationId='+locationId+'&locType=4').done(function(data) {
			var jsonResult = JSON.parse(data);
		});
	}

	function searchQualityChecklist(){
		$("#errorHolder").hide();
		try{
			$("#container_progress").html('');
			var params = '';
			var startWith = 0;
			var projId = $('#projName').val();
			var checklist = $('#checklist').val();
			var location = $('#location').val();
			var subLocation = $('#subLocation').val();
			var sub_subLocation = $('#sub_subLocation').val();
			var subSubLocation3 = $('#subSubLocation3').val();
			var status = $('#status').val();
			var searchKeyword = $('#searchKeyword').val();
			

			var responseText = '<form id="allTaskTable" name="allTaskTable"><table cellpadding="0" cellspacing="0" border="0" class="display" id="qaDefaultChecklist" width="100%"><thead><tr><th width="5%"></th><th width="5%">Date</th><th width="10%">Task</th><th width="40%">Location</th><th width="8%">Status</th><th width="19%">Issued To</th><th width="8%">Action</th>';

			if(projId == ''){ $('#projectQAError').show(); return false; }else{ $('#projectQAError').hide(); }
			if(checklist == ''){ $('#checklistError').show(); return false; }else{ $('#checklistError').hide(); }
			/*if(location == ''){ $('#locationQAError').show(); return false; }else{ $('#locationQAError').hide(); }*/

			params = "SearchInsp=1&projId="+projId+"&checklist="+checklist+"&location="+location+"&subLocation="+subLocation+"&sub_subLocation="+sub_subLocation+"&subSubLocation3="+subSubLocation3+"&status="+status+"&searchKeyword="+searchKeyword+"&name="+Math.random();
			
			$('#container_progress').html(responseText);
			
			var oTable = $('#qaDefaultChecklist').dataTable( {
				"iDisplayLength": -1,
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bProcessing": true,
				"bServerSide": true,
				"bRetrieve": true,
				"sAjaxSource": "qa_non_conformance_search_result.php?"+ params,
				"lengthChange": false, 
				"aLengthMenu" : false,
				"bLengthChange": false,//Code to Hide Display dropdown
				"bStateSave": false,
				"aoColumnDefs": [ {  "bSearchable": true, "bSortable": false, "aTargets": [ 0,1,2,3,4,5 ] }],
				"bFilter": false,
				"bPaginate": false, 
				"sScrollY": "600", 
				"sScrollXInner": "400%", 
				"bScrollCollapse": true,
				"fnRowCallback": function(nRow, aData, iDisplayIndex) {
									if (aData[0] == 'colOpen') {
										$(nRow).addClass('colOpen');
									}									
									if (aData[0] == 'colClosed') {
										$(nRow).addClass('colClosed');
									}
									if (aData[0] == 'colFixed') {
										$(nRow).addClass('colFixed');
									}
									if (aData[1] == 1) {
										$('#closeInsp').show('fast');
									}
									return nRow;
								},
				"fnDrawCallback": function( oSettings ) {
			    	var dataLen = $(".action").length;
			    	if(dataLen !== undefined && dataLen >0){
			    		$("#generateReport").css("display","block");
			    	}else{
			    		$("#generateReport").css("display","none");
			    	}
			    },
			    "aoColumnDefs": [{
				"bVisible": false,
				"aTargets": [0]
				}]
			
			} );
			oTable.fnDraw();
		}catch(e){
			alert(e.message); 
		}
	}

	function addChecklist(projID, checklistId){
		width = 900;
		var location = $('#location').val();
		var subLocation = $('#subLocation').val();
		var sub_subLocation = $('#sub_subLocation').val();
		var subSubLocation3 = $('#subSubLocation3').val();

		var callUrl = 'add_checklist.php?projID='+ projID +'&checklistId='+ checklistId +'&uniqueId='+Math.random()+'&location='+location+'&subLocation='+subLocation+'&sub_subLocation='+sub_subLocation+'&subSubLocation3='+subSubLocation3;
		modalPopup(align, 100, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, callUrl, loadingImage, evedenceNonconf);
	}

	function deleteThis(qaChecklistId, projID){
		jConfirm('Are you sure, you want to delete this record?', 'Delete Confirmation', function(result){
			if(result) {
				$.post('qa_checklist_view.php?type=delete&qaChecklistId='+qaChecklistId+'&projectId='+projID).done(function(data) {
					//var jsonResult = JSON.parse(data);
					searchQualityChecklist();
				});

			}
		});
	}

	function viewThis(taskId, projID){
		width = 900;
		var callUrl = 'qa_task_non_confirmance_edit.php?taskId='+taskId+'&projID='+projID+'&view=view'+'&uniqueId='+Math.random();
		modalPopup(align, 100, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, callUrl, loadingImage, nonconformCallback);
	}

	function editThis(taskId, projID){
		width = 900;
		var callUrl = 'qa_task_non_confirmance_edit.php?taskId='+taskId+'&projID='+projID+'&uniqueId='+Math.random();
		modalPopup(align, 100, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, callUrl, loadingImage, nonconformCallback);
	}

	function printDiv(){
		var divToPrint = document.getElementById('mainDivForPrint'); 
		var newWin = window.open('', 'PrintWindow', '', false); 
		newWin.document.open(); 
		newWin.document.write('<html><head><meta http-equiv="X-UA-Compatible" content="IE=8"><style>body { size:A4-landscape; }</style></head><body onload="window.print();"><style type="text/css">table.collapse { border-collapse: collapse; border: 1pt solid black; }table.collapse td { border: 1pt solid black; padding: 2px; }.pagination{display:none;}.buttonDiv{display:none;}.footer{display:block;}</style>'+divToPrint.innerHTML+'</body></html>'); 
		newWin.document.close(); 
	}
	function closeClick(){
		$(".nonselect").click(function(){
			$('.JsDatePickBox').each(function() {
				$(this).hide();
			});
		});
	}
	function nonconformCallback(){
		closeClick();
		new JsDatePick({
			useMode:2,
			target:"fixedByDate_0",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"fixedByDate_1",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"fixedByDate_2",
			dateFormat:"%d-%m-%Y"
		});
	}

	function addTaskNoconfirmance(){
		var classLen = $('.issueToPluse:visible').length;
		var i =1;
		while (i <= classLen) {	 
			if(document.getElementById("Issuedto"+i).value  == ''){
				$('#errorIssueTo'+i).show('fast');
				return false; 
			}else{ 
				$('#errorIssueTo'+i).hide(); 
			}

			var j = i - 1;

			if(document.getElementById("fixedByDate_"+j).value  == ''){
				$('#errorDate'+i).show('fast');
				return false; 
			}else{ 
				$('#errorDate'+i).hide(); 
			}

			if(document.getElementById("Status"+i).value  == ''){
				$('#errorStatus'+i).show('fast');
				return false; 
			}else{ 
				$('#errorStatus'+i).hide(); 
			}
			i++;
		}

		showProgress();

		var filedata = new FormData(document.getElementById("addEvedenceForm"));
	
		$.ajax({
			url: 'qa_task_non_confirmance_edit.php?InsertEvedenceId='+Math.random(),
			type: "POST",
			data: filedata,
			async : false,
			processData: false,  // tell jQuery not to process the data
			contentType: false   // tell jQuery not to set contentType

		}).done(function(data) {
			hideProgress();	
			var jsonResult = JSON.parse(data);
			if (jsonResult.status){
				console.log("nonconf_button_"+jsonResult.taskId);
				closePopup(300);
				searchQualityChecklist();
			}else {
			    jAlert(jsonResult.msg);
			}
			
		});
	}
	var elementCount = 0;
	var addedRow = new Array();
	function removeElement(removeID){
		var r = jConfirm('Do you want to delete Issue To ?', null, function(r){
			if (r==true){
				elementCount--;
				document.getElementById(removeID).style.display = 'none';
				addedRow.pop();
			}
		});
	}
	function AddItem() {
		var classLen = $('.issueToPluse:visible').length;
		if(classLen < 3){
			if(document.getElementById('hide_'+classLen).style.display == 'none'){
				document.getElementById('hide_'+classLen).style.display = 'table-row';
			}else{
				document.getElementById('hide_1').style.display = 'table-row';
			}
		}else{
			jAlert("You can't add more than 3 Issue To !");
		}
	}

	function showImage1(input) {
		console.log(input);
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();

	        reader.onload = function (e) {
	            $('#response_1').attr('src', e.target.result);
	            $('.imageClass1').css('display','block');
	            $('#innerDiv0 > span').hide();
	            $('#removeImage0').css('display','block');
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}

	function showImage2(input) {

	    if (input.files && input.files[0]) {
	        var reader = new FileReader();

	        reader.onload = function (e) {
	            $('#response_2').attr('src', e.target.result);
	            $('.imageClass2').css('display','block');
	            $('#innerDiv1 > span').hide();
	            $('#removeImage1').css('display','block');
	        }

	        reader.readAsDataURL(input.files[0]);
	    }
	}

	function removeIssue(isseu_id,index){

		$.ajax({
			url: 'qa_task_non_confirmance.php?deleteEvedenceId='+Math.random()+'&isseu_id='+isseu_id,
			type: "GET",
			//data: jQuery.param({ "isseu_id": isseu_id}),
			async : false,
			processData: false,  // tell jQuery not to process the data
			contentType: false   // tell jQuery not to set contentType

			}).done(function(data) {
			hideProgress();	
			var jsonResult = JSON.parse(data);
			if (jsonResult.status){
			//closePopup(300);
			jAlert(jsonResult.msg);	
			$('#hide_'+isseu_id).remove();
			var hid = $('#hid').val();
			$('#hid').val(parseInt(hid)-1);
			}else {
			   jAlert(jsonResult.msg);
			}

		});
	}

	// ========GENERATE REPORT=======

	function submitFormNoConfReport(){
		$("#errorHolder").hide();
		try{
			$("#container_progress").html('');
			var params = '';
			var startWith = 0;
			var projId = $('#projName').val();
			var checklist = $('#checklist').val();
			var location = $('#location').val();
			var subLocation = $('#subLocation').val();
			var sub_subLocation = $('#sub_subLocation').val();
			var subSubLocation3 = $('#subSubLocation3').val();
			var status = $('#status').val();
			var searchKeyword = $('#searchKeyword').val();
			var sub_subLocation3 = '';
			var locationName = '';
			var subLocationName = '';
			var sub_subLocationName = '';
			var sub_subLocationName2 = '';
			var sub_subLocationName3 = '';
			var raisedBy = '';


			if(projId == ''){ $('#projectQAError').show(); return false; }else{ $('#projectQAError').hide(); }
			if(checklist == ''){ $('#checklistError').show(); return false; }else{ $('#checklistError').hide(); }
			/*if(location == ''){ $('#locationQAError').show(); return false; }else{ $('#locationQAError').hide(); }*/

			params = "projName="+projId+"&checklist="+checklist+"&location="+location+"&subLocation="+subLocation+"&sub_subLocation="+sub_subLocation+"&sub_subLocation2="+subSubLocation3+"&sub_subLocation3="+sub_subLocation3+"&locationName="+locationName+"&subLocationName="+subLocationName+"&sub_subLocationName="+sub_subLocationName+"&sub_subLocationName2="+sub_subLocationName2+"&sub_subLocationName3="+sub_subLocationName3+"&status="+status+"&raisedBy="+raisedBy+"&searchKeyword="+searchKeyword+"&name="+Math.random();
			modalPopup(align, top, '1000', padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_nonconf.php?'+params, loadingImage);
			
		}catch(e){
			alert(e.message); 
		}
	}

	function sendEmailViewPDFNonConf(proId){
		$.ajax({
			type: 'POST',
			url: "pdf/i_report_nonconf_pdf.php",
			data: 'nonConf=1&addInEmail=1&prj='+proId,
			dataType: "JSON",
			success: function(resultData){
				var filePath = resultData['filePath'];
				var htmlView = '';
				htmlView += '<fieldset class="emailContainer"><legend>Email Report</legend><table width="50%" border="0" cellpadding="0" cellspacing="0" align="center"><tr><td width="16%" align="right">To</td><td width="3%">&nbsp;:&nbsp;</td><td width="81%"><textarea name="toEmail" id="toEmail" cols="30" rows="3"></textarea></td></tr><tr><td align="right">Cc</td><td>&nbsp;:&nbsp;</td><td><input type="text" name="ccEmail" id="ccEmail" size="40" /></td></tr><tr><td align="right">Bcc</td><td>&nbsp;:&nbsp;</td><td><input type="text" name="bccEmail" id="bccEmail" size="40" /></td></tr><tr><td align="right">Subject</td><td>&nbsp;:&nbsp;</td><td><input type="text" name="subEmail" id="subEmail" size="40" value="Non Conformance Report" /></td></tr><tr><td align="right">Attachment</td><td>&nbsp;:&nbsp;</td><td><div id="attachEmail">non_conformance_report.pdf</div><input type="hidden" name="project_id" id="project_id" value="'+proId+'" /><input type="hidden" name="report_type" id="report_type" value="Non Conformance Report" /><input type="hidden" name="filePath" id="filePath" value="'+filePath+'" /></td></tr><tr><td align="right" valign="top">Message</td><td valign="top">&nbsp;:&nbsp;</td><td><textarea name="descEmail" id="descEmail" cols="30" rows="15"></textarea></td></tr><tr><td align="center" colspan="3"><img onClick="sendEmailPDFNonConf();" src="images/send.png" style="float:left;" /><img onClick="closePopup(300);" src="images/cancel.png" style="float:left;margin-left:50px;" /></td></tr></table></fieldset>';
				$('#mainContainer').html(htmlView);
			}
		});
	}

	function sendEmailPDFNonConf(){
		try{
			var to = $('#toEmail').val();
			if(to == '' || to == undefined){
				alert('Please Enter Email id');
				return false;
			}
			var cc = $('#ccEmail').val();
			var bcc = $('#bccEmail').val();
			var subject = $('#subEmail').val();
			var attachment = $('#filePath').val();
			var descEmail = $('#descEmail').val();
			
			if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
			document.getElementById('spinner').innerHTML = '<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Sending Mail ..<br/>This may take several minutes.</div>';	
			showProgress();
		
			params = "to="+to+"&cc="+cc+"&bcc="+bcc+"&subject="+subject+"&attachment="+attachment+"&descEmail="+descEmail+"&name="+Math.random();
			
			xmlhttp.open("POST", 'send_report_mail.php', true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.setRequestHeader("Content-length", params.length);
			xmlhttp.setRequestHeader("Connection", "close");
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					hideProgress();
					document.getElementById("mainContainer").style.overflow="visible";
					$('#mainContainer').html(xmlhttp.responseText);
				}
			}
			xmlhttp.send(params);
		}catch(e){
		//	alert(e.message); 
		}
	}
</script>
