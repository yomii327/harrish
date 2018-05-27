<?php $builder_id=$_SESSION['ww_builder_id'];
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
$projNameArr = array();
$q = "SELECT project_id, project_name FROM user_projects WHERE user_id = '".$builder_id."' AND is_deleted = 0 GROUP BY project_name";
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
	$optChecklist = '';
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
table tr td{ color:#000; }
</style>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script language="javascript" type="text/javascript" src="js/quality_checklist_task.js"></script>
<main id="quality-check">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<div class="title">
					<h2><span class="orange-text">Quality</span> Checklist</h2>
				</div>
			</div>
		</div>
		<div class="content">
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

			<div class="search_multiple">
				<form name="qaSearchForm" id="qaSearchForm">		
				<!-- qa_itp_task_search -->
				<div id="qa_itp_task_search">
					<div class="row">
						<div class="col-md-6">
							<label for="projName"><h6>Project Name <span class="reqire">*</span></h6></label>
							<select name="projName" id="projName" class="form-control" onChange="getAllTask(this.value);">
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
							<div class="error-edit-profile" style="display:none;" id="projectQAError">The project field is required</div>
						</div>
						<div class="col-md-6">
							<label for="checklist"><h6>Checklist <span class="reqire">*</span></h6></label>
							<?php //$itpArr = array('Pre Pour' => 'Pre Pour ITP', 'Post Pour' => 'Post Pour ITP', 'Plastering' => 'Plastering ITP');?>
							<select name="checklist" id="checklist"  class="form-control" onChange="addChecklistInSession(this.value);">
								<option value="">Select</option>
								<?php echo $optChecklist; ?>
							</select>
							<div class="error-edit-profile" style="display:none;" id="checklistError">The Checklist field is required</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<label for="location"><h6>Location <span class="reqire">*</span></h6></label>
							<div id="showLocation">
								<select name="location" id="location" onchange="getSublocation(this.value);" class="form-control">
									<option value="">Select</option>
									<?php echo $optLocation; ?>
								</select>
							</div>
							<div class="error-edit-profile" style="display:none;" id="locationQAError">The Location field is required</div>
						</div>
						<div class="col-md-6">
							<label for="subLocation"><h6>Sub Location</h6></label>
							<div id="showSubLocation">
								<select name="subLocation" id="subLocation" onchange="getSublocation1(this.value)" class="form-control">
									<option value="">Select</option>
									<?php foreach($subLocationData as $subLocation) { ?>
										<option value="<?=$subLocation['location_id']?>" <?php if($subLocation['location_id'] == $selectedSubLocationId){echo 'selected="selected"';} ?>><?=$subLocation['location_title']?></option>
									<?php } ?>
								</select>
							</div>
							<div class="error-edit-profile" style="display:none;" id="subLocationQA1Error">The Sub Location 1 field is required</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<label for="sub_subLocation"><h6>Sub Location 1</h6></label>
							<div id="showSubLocation1">
								<select name="sub_subLocation" id="sub_subLocation" onchange="getSublocation2(this.value)" class="form-control">
									<option value="">Select</option>
									<?php foreach($sub_subLocationData as $subSubLocation) { ?>
										<option value="<?=$subSubLocation['location_id']?>" <?php if($subSubLocation['location_id'] == $selectedSubLocationId1){echo 'selected="selected"';} ?>><?=$subSubLocation['location_title']?></option>
									<?php } ?>
								</select>
							</div>
							<div class="error-edit-profile" style="display:none;" id="subLocationQA2Error">The Sub Location 2 field is required</div>
						</div>
						<div class="col-md-6">
							<label for="subSubLocation3"><h6>Sub Location 2</h6></label>
							<div id="showSubLocation2">
								<select name="subSubLocation3" id="subSubLocation3" onchange="getSublocation3(this.value)" class="form-control">
									<option value="">Select</option>
									<?php foreach($subSubLocationData3 as $subSubLocation3) { ?>
										<option value="<?=$subSubLocation3['location_id']?>" <?php if($subSubLocation3['location_id'] == $selectedSubLocationId2){echo 'selected="selected"';} ?>><?=$subSubLocation3['location_title']?></option>
								<?php } ?>
							</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<label for="status"><h6>Status</h6></label>
							<select name="status" id="status" class="form-control">
								<option value="">Select</option>
								<option value="Open" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Open'){ echo 'selected="selected"'; }}?> >Open</option>
								<option value="Closed" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Closed'){ echo 'selected="selected"'; }}?> >Closed</option>
								<!-- <option value="NA" < ?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'NA'){ echo 'selected="selected"'; }}?> >NA</option> -->
							</select>
						</div>
						<div class="col-md-6">
							<label for="searchKeyword"><h6>Search Keyword</h6></label>
							<div id="search_keyword">
								<input type="text" name="searchKeyword" id="searchKeyword" class="form-control"/>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div id="report_timer" style=""></div>
							<!--input type="hidden" value="create" name="sect" id="sect" /-->
							<input type="hidden" name="sessionBack" id="sessionBack" value="Y" />
							<!-- <input name="SearchInsp" type="button" class="submit_btn" id="button" value="" style="background-image:url(images/search_btn_web.png); width:113px; height:46px;" onClick="searchQualityChecklist();" /> -->
							<button name="SearchInsp" type="button" class="btn btn-default" id="button" onClick="searchQualityChecklist();"><i class="fas fa-search"></i> Search</button>
						</div>
					</div>
				</div><!-- /.qa_itp_task_search -->
				</form>
				<div class="demo_jui" id="show_inspection" ></div>
				<?php include'data-table.php';?>
				<div id="container_progress">&nbsp;</div>
				<div id="setSession"></div>
				<div id="userRole"></div>
			</div>
		</div>
	</div>
</main>

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
			

			var responseText = '<form id="allTaskTable" name="allTaskTable"><a href="?sect=add_qc_task" style="float:right;margin-bottom:2px;" class="green_small">Add New</a><table cellpadding="0" cellspacing="0" border="0" class="display" id="qaDefaultChecklist" width="100%"><thead><tr><th width="25%">Date</th><th width="50%">Location</th><th width="15%">Status</th width="10%"><th>Action</th>';

			if(projId == ''){ $('#projectQAError').show(); return false; }else{ $('#projectQAError').hide(); }
			if(checklist == ''){ $('#checklistError').show(); return false; }else{ $('#checklistError').hide(); }
			if(location == ''){ $('#locationQAError').show(); return false; }else{ $('#locationQAError').hide(); }

			params = "SearchInsp=1&projId="+projId+"&checklist="+checklist+"&location="+location+"&subLocation="+subLocation+"&sub_subLocation="+sub_subLocation+"&subSubLocation3="+subSubLocation3+"&status="+status+"&searchKeyword="+searchKeyword+"&name="+Math.random();
			
			$('#container_progress').html(responseText);
			
			var oTable = $('#qaDefaultChecklist').dataTable( {
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bProcessing": true,
				"bServerSide": true,
				"iDisplayLength": 10,
				"sAjaxSource": "qa_checklist_search_result.php?"+ params,
				"bStateSave": true,
				"aoColumnDefs": [ {  "bSearchable": true, "bSortable": false, "aTargets": [ 0,1,2,3 ] }],
				"bFilter": false,
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

	function viewThis(qaChecklistId, projID){
		modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'qa_checklist_view.php?qaChecklistId='+qaChecklistId+'&projID='+projID+'&name='+Math.random(), loadingImage);
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

	function editThis(qaChecklistId, projID){
		width = 900;
		var callUrl = 'edit_checklist.php?qaChecklistId='+qaChecklistId+'&projID='+projID+'&uniqueId='+Math.random();
		modalPopup(align, 100, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, callUrl, loadingImage, evedenceNonconf);
	}

	function printDiv(){
		var divToPrint = document.getElementById('mainDivForPrint'); 
		var newWin = window.open('', 'PrintWindow', '', false); 
		newWin.document.open(); 
		newWin.document.write('<html><head><meta http-equiv="X-UA-Compatible" content="IE=8"><style>body { size:A4-landscape; }</style></head><body onload="window.print();"><style type="text/css">table.collapse { border-collapse: collapse; border: 1pt solid black; }table.collapse td { border: 1pt solid black; padding: 2px; }.pagination{display:none;}.buttonDiv{display:none;}.footer{display:block;}</style>'+divToPrint.innerHTML+'</body></html>'); 
		newWin.document.close(); 
	}

</script>

