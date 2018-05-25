<?php include'data-table.php';
include('includes/commanfunction.php');
$object = new COMMAN_Class();

$projectName=''; $locationName='';
#if(isset($_GET['bk']) && $_GET['bk'] == 'Y'){
if($_GET['frm'] == "dsb") $_SESSION['qc'] = array(); 
if(isset($_GET['pid']) && $_GET['pid'] != "")$_SESSION['qc']['projName'] = $_GET['pid'];
if(isset($_GET['locid']) && $_GET['locid'] != "")$_SESSION['qc']['location'] = $_GET['locid'];
if(isset($_GET['sts']) && $_GET['sts'] != "")$_SESSION['qc']['status'] = $_GET['sts'];
if(isset($_GET['isst']) && $_GET['isst'] != "")$_SESSION['qc']['issuedTo'] = $_GET['isst'];
if(isset($_GET['spParam']) && $_GET['spParam'] != "")$_SESSION['qc']['spParam'] = $_GET['spParam'];
if(isset($_GET['DRF']) && $_GET['DRF'] != "")$_SESSION['qc']['DRF'] = $_GET['DRF'];
if(isset($_GET['DRT']) && $_GET['DRT'] != "")$_SESSION['qc']['DRT'] = $_GET['DRT'];
if(isset($_GET['FBDF']) && $_GET['FBDF'] != "")$_SESSION['qc']['FBDF'] = $_GET['FBDF'];
if(isset($_GET['FBDT']) && $_GET['FBDT'] != "")$_SESSION['qc']['FBDT'] = $_GET['FBDT'];
if(isset($_GET['cr']) && $_GET['cr'] != "")$_SESSION['qc']['cr'] = $_GET['cr'];
else$_SESSION['qc']['cr'] = "";
if(isset($_GET['diff']) && $_GET['diff'] != "")$_SESSION['qc']['diff'] = $_GET['diff'];
else$_SESSION['qc']['diff'] = "";

$projectName = $_SESSION['qc']['projName'];
$cr = $_SESSION['qc']['cr'];
$diff = $_SESSION['qc']['diff'];
$locationName = $_SESSION['qc']['location'];
$sublocationName = $_SESSION['qc']['subLocation'];
$spParam = $_SESSION['qc']['spParam'];
#}?>
<script type="text/javascript"> var dropDownSelect;// = 10;</script>
<style type="text/css">
	table#frmSearchInspection { width: 100%; }
    table#frmSearchInspection tr td{ padding:5px; color: #000; text-align: left; }
    table#frmSearchInspection tr td:nth-child(1),
    table#frmSearchInspection tr td:nth-child(3){ width: 15%; text-align:right; }
    table#frmSearchInspection tr td:nth-child(2),
    table#frmSearchInspection tr td:nth-child(4){ width: 35%; padding-left: 10px; }
    table#frmSearchInspection tr td select { margin-left: 0; }
    #DRF, #DRT, #FBDF, #FBDT{ background:#FFF; cursor:default; height:20px; -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; }
    div#spinner{ display: none; width:100%; height: 100%; position: fixed; top: 0; left: 0; background:url(images/loadingAnimation.gif) no-repeat center #CCCCCC; text-align:center; padding:10px; font:normal 16px Tahoma, Geneva, sans-serif; border:1px solid #666; z-index:2; overflow: auto; opacity : 0.8; filter: alpha(opacity = 80); }
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
<!-- Date Picker files start here -->
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<!-- Date Picker files start here -->
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript">
	window.onload = function(){
    	new JsDatePick({
    		useMode:2,
    		target:"DRF",
    		dateFormat:"%d-%m-%Y"
    	});
    	new JsDatePick({
    		useMode:2,
    		target:"DRT",
    		dateFormat:"%d-%m-%Y"
    	});
    	new JsDatePick({
    		useMode:2,
    		target:"FBDF",
    		dateFormat:"%d-%m-%Y"
    	});
    	new JsDatePick({
    		useMode:2,
    		target:"FBDT",
    		dateFormat:"%d-%m-%Y"
    	});
    };
    
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

	function startAjax(val) {
		AjaxShow("POST", "ajaxFunctions.php?type=location && proID=" + val, "ShowLocation");
		AjaxShow("POST", "ajaxFunctions.php?type=inspecrBy && proID=" + val, "ShowInspecrBy");
		AjaxShow("POST", "ajaxFunctions.php?type=issuedToQC && proID=" + val, "ShowIssuedTo");
		AjaxShow("POST", "ajaxFunctions.php?type=sessions&& proID=" + val, "setSession");
		AjaxShow("POST", "ajaxFunctions.php?type=session&& proID=" + val, "setSession");
		AjaxShow("POST", "ajaxFunctions.php?type=userRole&& proID=" + val, "userRole");
		AjaxShow("POST", "ajaxFunctions.php?type=raisedBy&& proID=" + val, "ShowRaisedBy");
	}

	function subLocate(obj) {
		AjaxShow("POST", "ajaxFunctions.php?type=subLocation && proID=" + obj, "ShowSubLocation");
	}

	function sub_subLocate(obj) {
		AjaxShow("POST", "ajaxFunctions.php?type=sub_subLocation_qc && proID=" + obj, "SubShowSubLocation");
	}

	function setDropValue(val) {
		//AjaxShow("POST","ajaxFunctions.php?type=listDropVal&& proID="+val,"listDropVal");
	}
	var urlData = '';

	function submitForm(bStateSaveFlag) {
		var projName = document.getElementById('projName').value;
		var location = document.getElementById('location').value;
		var subLocation = document.getElementById('subLocation').value;
		var subSubLocation = document.getElementById('subSubLocation').value;
		var status = document.getElementById('status').value;
		var inspectedBy = document.getElementById('inspectedBy').value;
		var issuedTo = document.getElementById('issuedTo').value;
		//var priority = document.getElementById('priority').value;
		var inspecrType = document.getElementById('inspecrType').value;
		var costAttribute = document.getElementById('costAttribute').value;
		var raisedBy = document.getElementById('raisedBy').value;
		var DRF = document.getElementById('DRF').value;
		var DRT = document.getElementById('DRT').value;
		var FBDF = document.getElementById('FBDF').value;
		var FBDT = document.getElementById('FBDT').value;		
		var cr = '<?php echo $cr; ?>';
		var diff = '<?php echo $diff; ?>';
		if (projName == '') {
			jAlert('Project Name Should be Selected !');
			return false;
		}

		var dateChackRaised = checkDates(document.getElementById('DRF'), document.getElementById('DRT'), 'In Date Raised Field');
		var dateChackFixed = checkDates(document.getElementById('FBDF'), document.getElementById('FBDT'), 'In Fix By Date Field');
		if (dateChackRaised === false) {
			return false;
		}
		if (dateChackFixed === false) {
			return false;
		}

		if (window.XMLHttpRequest) {
			xmlhttp = new XMLHttpRequest();
		} else {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		//	showProgress();
		var url = 'i_defect_show_data.php';
		var params = "projName=" + projName + "&location=" + location + "&subLocation=" + subLocation + "&subSubLocation=" + subSubLocation + "&status=" + status + "&inspectedBy=" + inspectedBy + "&issuedTo=" + issuedTo + "&inspecrType=" + inspecrType + "&costAttribute=" + costAttribute + "&raisedBy=" + raisedBy + "&DRF=" + DRF + "&DRT=" + DRT + "&FBDF=" + FBDF + "&FBDT=" + FBDT + "&cr=" + cr + "&diff=" + diff;
		<?php if($_GET['from'] == "nb"){?>
		params += "&spParam=<?=$spParam?>";
		<?php }?>
		params += "&name=" + Math.random();

		var responseText = '<img id="closeInsp" src="images/bluk_close.png" style="display:none;margin:0 0 15px 10px;"  onClick="closeInspections();"  /><form name="inspectionTable" id="inspectionTable"><table width="100%" cellpadding="0" cellspacing="0" border="0" class="display" id="inspTable" name="inspectionTable"><thead><th>S.No.</th><th>S.No.</th><th>S.No.</th><th><input type="checkbox" id="checkall" onclick=toggleCheck(this);></th><th>Location</th><th>Fix by Date</th><th>Issue To</th><th>Description</th><th>Raised By</th><th>View</th></thead><tbody></tbody></table></from><br/><br/>';
		
		//var responseText = '<img id="closeInsp" src="images/bluk_close.png" style="display:none;margin:0 0 15px 10px;"  onClick="closeInspections();"  /><form name="inspectionTable" id="inspectionTable"><table width="970" cellpadding="0" cellspacing="0" border="0" class="display" id="inspTable" name="inspectionTable"><thead><th>SNo</th><th><input type="checkbox" id="checkall" onclick=toggleCheck(this);></th><th>Location</th><th>Fix by Date</th><th>Issue To</th><th>Description</th><th>Raised By</th><th>View</th></thead><tbody></tbody></table></from>';

		document.getElementById("show_defect").innerHTML = responseText;
		urlData = "i_defect_show_data_ajax.php?" + params; //Global variable
		oTable = $("#inspTable").dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bProcessing": true,
			"bServerSide": true,			
			"sAjaxSource": "i_defect_show_data_ajax.php?" + params,
			//"bStateSave": bStateSaveFlag,
			//"bStateSave": true,
			"sCookiePrefix": "inspTableCookie",
			"fnRowCallback": function(nRow, aData, iDisplayIndex) {
				if (aData[0] == 'colOpen') {
					$(nRow).addClass('colOpen');
				}
				if (aData[0] == 'colDraft') {
					$(nRow).addClass('colDraft');
				}
				if (aData[0] == 'colClosed') {
					$(nRow).addClass('colClosed');
				}
				if (aData[0] == 'colPending') {
					$(nRow).addClass('colPending');
				}
				if (aData[0] == 'colFixed') {
					$(nRow).addClass('colFixed');
				}
				if (aData[1] == 1) {
					$('#closeInsp').show('fast');
				}

				if ($.inArray(aData[2], closeIdArr) > -1)
					$(nRow).children().children('#inspid_' + aData[2]).prop('checked', true);

				if ($('#checkall').is(':checked')) {
					$('#checkall').prop('checked', false);
				}
				return nRow;
			},
			"aoColumnDefs": [{
				"bVisible": false,
				"aTargets": [0, 1, 2]
			}, {
				"bSearchable": false,
				"bSortable": false,
				"aTargets": [3]
			}],
			"aoColumns": [{ "bVisible": false}, { "bVisible": false}, { "bVisible": false}, null, null, null, null, null, null, null]
		});
	}

	function checkDates(date1, date2, element) {
		var obj = date1.value;
		var obj1 = date2.value;
		if (obj != '' || obj1 != '') {
			if (obj == '' && obj1 != '') {
				jAlert('Please Select Form Date First !');
				return false;
			} else {
				var fromDate = new Date(obj.substr(6, 4), obj.substr(3, 2), obj.substr(0, 2));
				var toDate = new Date(obj1.substr(6, 4), obj1.substr(3, 2), obj1.substr(0, 2));
				if ((toDate.getTime() - fromDate.getTime()) < 0) {
					jAlert(element + ' To Date in Not Less Than Form Date !');
					return false;
				}
			}
		}
	}
</script>
<div id="container" style="width:99%;margin-top:25px;min-height:510px;">
<?php
	if (isset($_SESSION["ww_is_builder"]) and $_SESSION["ww_is_builder"]== 1){
		$owner_id = $_SESSION['ww_builder_id'];
	}else{
		$owner_id = $_SESSION['ww_is_company'];
	}#$owner_id = $_SESSION['ww_owner_id'];
	$phd='';
	$myProjects='';
	$ihd='';
	$myInspections=''; ?>
<div class="content_hd1" style="background-image:url(images/defects_hd.png);"></div>
<br clear="all" />
<?php if(isset($_SESSION['inspection_added'])){?>
<div id="errorHolder" style="margin-left: 10px;margin-top:15px;margin-bottom:15px;">
	<div class="success_r" style="height:35px;width:405px;">
		<p><?=$_SESSION['inspection_added'];?></p>
	</div>
</div>
<?php unset($_SESSION['inspection_added']);}?>
<?php if(base64_decode($_GET['ms']) == 'Deleted'){?>
<div id="errorHolder" style="margin-left: 10px;margin-top:15px;margin-bottom:15px;">
	<div class="success_r" style="height:35px;width:405px;">
		<p>Inspection Deleted Successfully !</p>
	</div>
</div>
<?php }?>
<?php if(base64_decode($_GET['ms']) == 'Updated'){?>
<div id="errorHolder" style="margin-left: 10px;margin-top:15px;margin-bottom:15px;">
	<div class="success_r" style="height:35px;width:405px;">
		<p>Inspection Updated Successfully !</p>
	</div>
</div>
<?php }?>
<div class="search_multiple" style="border:1px solid;text-align:center;margin:10px 0px; padding: 10px 0px;">
	<form action="" method="post" name="defectSearch" id="defectSearch">
		<table id="frmSearchInspection" cellpadding="0" cellspacing="5" border="0">
			<tr>
				<td>Project Name <span class="reqire">*</span></td>
				<td colspan="2">
					<select name="projName" id="projName"  class="select_box" onChange="startAjax(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<?php if(isset($_SESSION['ww_is_company'])){
							$q = "SELECT project_id, project_name FROM user_projects WHERE is_deleted=0 GROUP BY project_name";
							$res = mysql_query($q);
							$prIDArr = array();
							$outPutStr = "";
							while($q1 = mysql_fetch_array($res)){
								$selectBox = '<option value="'.$q1[0].'"';
								$prIDArr[] = $q1[0];
								if(isset($_GET['bk']) && $_GET['bk'] == 'Y'){
									if($projectName == $q1[0]){
										$selectBox .= 'selected="selected"';
									}
								}	
								$selectBox .= '>'.$q1[1].'</option>';
								$outPutStr .= $selectBox;
							}
							echo '<option value="'.join(',', $prIDArr).'">All</option>'.$outPutStr;
							}else{
							$q="SELECT project_id, project_name FROM user_projects WHERE user_id = '".$owner_id."' and is_deleted = 0 GROUP BY project_name";
							$res = mysql_query($q);
							$prIDArr = array();
							$outPutStr = "";
							while($q1 = mysql_fetch_array($res)){
								$selectBox = '<option value="'.$q1[0].'"';
									$prIDArr[] = $q1[0];
									if($projectName == $q1[0]){
										$selectBox .= 'selected="selected"';
									}
									if($_SESSION['userRole'] == 'Sub Contractor')
										$selectBox .= 'selected="selected"';
								$selectBox .= '>'.$q1[1].'</option>';
								$outPutStr .= $selectBox;
							}
							echo '<option value="'.join(',', $prIDArr).'">All</option>'.$outPutStr;
							}?>
					</select>
				</td>
				<td>Location </td>
				<td colspan="2" id="ShowLocation">
					<select name="location" id="location"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Sub Location</td>
				<td colspan="2" id="ShowSubLocation">
					<select name="subLocation" id="subLocation"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td>Sub Location 1</td>
				<td colspan="2" id="SubShowSubLocation">
					<select name="subSubLocation" id="subSubLocation"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Status</td>
				<td colspan="2">
					<select name="status" id="status" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="Open" <?php if(isset($_SESSION['qc']['status'])){ if($_SESSION['qc']['status'] == 'Open'){ echo 'selected="selected"'; }}?>>Open</option>
						<option value="Pending" <?php if(isset($_SESSION['qc']['status'])){ if($_SESSION['qc']['status'] == 'Pending'){ echo 'selected="selected"'; }}?>>Pending</option>
						<option value="Fixed" <?php if(isset($_SESSION['qc']['status'])){ if($_SESSION['qc']['status'] == 'Fixed'){ echo 'selected="selected"'; }}?>>Fixed</option>
						<!--					  <option value="In Progress">In Progress</option>-->
						<option value="Closed" <?php if(isset($_SESSION['qc']['status'])){ if($_SESSION['qc']['status'] == 'Closed'){ echo 'selected="selected"'; }}?>>Closed</option>
					</select>
				</td>
				<td>Inspected By</td>
				<td colspan="2" id="ShowInspecrBy">
					<select name="inspectedBy" id="inspectedBy" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Issued To</td>
				<td colspan="2" id="ShowIssuedTo">
					<select name="issuedTo" id="issuedTo" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td>Inspection Type</td>
				<td colspan="2" id="ShowPriority">
					<select name="inspecrType" id="inspecrType" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="Issue" <?php if(isset($_SESSION['qc']['inspecrType'])){ if($_SESSION['qc']['inspecrType'] == 'Issue'){ echo 'selected="selected"'; }}?>>Issue</option>
						<option value="Defect"<?php if(isset($_SESSION['qc']['inspecrType'])){ if($_SESSION['qc']['inspecrType'] == 'Defect'){ echo 'selected="selected"'; }}?>>Defect</option>
						<option value="Warranty"<?php if(isset($_SESSION['qc']['inspecrType'])){ if($_SESSION['qc']['inspecrType'] == 'Warranty'){ echo 'selected="selected"'; }}?>>Warranty</option>
						<option value="Incomplete Works" <?php if(isset($_SESSION['qc']['inspecrType'])){ if($_SESSION['qc']['inspecrType'] == 'Incomplete Works'){ echo 'selected="selected"'; }}?>>Incomplete Works</option>
						<option value="Purchase Changes"<?php if(isset($_SESSION['qc']['inspecrType'])){ if($_SESSION['qc']['inspecrType'] == 'Purchase Changes'){ echo 'selected="selected"'; }}?>>Purchase Changes</option>
						<!--<option value="Progress Monitoring">Progress Monitoring</option>
							<option value="Other">Other</option>-->
					</select>
				</td>
			</tr>
			<tr>
				<td>Cost Attribute</td>
				<td colspan="2">
					<select name="costAttribute" id="costAttribute" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="None" <?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == 'None'){ echo 'selected="selected"'; }}?>>None</option>
						<option value="Backcharge" <?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == 'Backcharge'){ echo 'selected="selected"'; }}?>>Backcharge</option>
						<option value="Variation" <?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == 'Variation'){ echo 'selected="selected"'; }}?>>Variation</option>
					</select>
				</td>
				<td>Raised By</td>
				<td colspan="2" id="ShowRaisedBy">
					<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Date Raised</td>
				<td colspan="2">From 
					<input name="DRF" type="text" size="7" id="DRF" readonly value="<?php if(isset($_SESSION['qc']['DRF'])){ echo $_SESSION['qc']['DRF']; }?>" />
					To 
					<input name="DRT" type="text" id="DRT" size="7" readonly value="<?php if(isset($_SESSION['qc']['DRT'])){ echo $_SESSION['qc']['DRT']; }?>" />
					<a href="javascript:void();" title="Clear date raised"><img src="images/redCross.png" onClick="clearDateRaised();" /></a>
				</td>
				<td>Fix By Date</td>
				<td colspan="2">From 
					<input name="FBDF" type="text" id="FBDF" size="7" readonly value="<?php if(isset($_SESSION['qc']['FBDF'])){ echo $_SESSION['qc']['FBDF']; }?>" />
					To
					<input name="FBDT" type="text" id="FBDT" size="7" readonly value="<?php if(isset($_SESSION['qc']['FBDT'])){ echo $_SESSION['qc']['FBDT']; }?>" />
					<a href="javascript:void();" title="Clear fixed by date"><img src="images/redCross.png" onClick="clearFixedByDate();" /></a>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="2" align="left">
					<?php if($_SESSION['ww_c_user_name'] != 'company'){?>
					<input name="importInspection" onClick="submitImport();"  type="button" class="submit_btn" id="button" style="background-image:url(images/import_inspections_btn.png); width:207px; height:46px;margin-right:10px;float:left;" />
					<?php }?>
				</td>
				<td>&nbsp;</td>
				<td  colspan="2" >
					<!--input type="hidden" value="create" name="sect" id="sect" /-->
					<input name="SearchInsp" type="button" onClick="submitForm(false);" class="submit_btn" id="button" value="" style="background-image:url(images/search_btn_web.png); width:113px; height:46px;"  />
					<input type="hidden" name="sessionBack" id="sessionBack" value="Y" />
				</td>
			</tr>
		</table>
	</form>
</div>
<div>
	<div id="show_defect" class="demo_jui" <?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){ echo 'style="padding-left:10px;"'; }?>></div>
	<div class="spacer"></div>
</div>
<div id="spinner"></div>
<div id="setSession"></div>
<div id="listDropVal"></div>
<div id="userRole"></div>
<?php #if(isset($_GET['bk']) && $_GET['bk'] == 'Y'){ ?>
<script language="javascript" type="text/javascript">
	var projectId = '';
	projectId = <?=$projectName;?>;
	AjaxShow("POST","ajaxFunctions.php?type=location && proID="+projectId,"ShowLocation");
	AjaxShow("POST","ajaxFunctions.php?type=inspecrBy && proID="+projectId,"ShowInspecrBy");
	AjaxShow("POST","ajaxFunctions.php?type=issuedToQC && proID="+projectId,"ShowIssuedTo");
	AjaxShow("POST","ajaxFunctions.php?type=sessions&& proID="+projectId,"setSession");
	AjaxShow("POST","ajaxFunctions.php?type=session&& proID="+projectId,"setSession");
	AjaxShow("POST","ajaxFunctions.php?type=userRole&& proID="+projectId,"userRole");
	AjaxShow("POST","ajaxFunctions.php?type=raisedBy&& proID="+projectId,"ShowRaisedBy");
	var locationId = '';
	<?php if($locationName != ''){?>
		locationId = <?=$locationName;?>;
		AjaxShow("POST","ajaxFunctions.php?type=subLocation && proID="+locationId, "ShowSubLocation");
	<?php }?>
	<?php if($sublocationName != ''){?>
		subLocationId = <?=$sublocationName;?>;
		AjaxShow("POST","ajaxFunctions.php?type=sub_subLocation_qc && proID="+subLocationId,"SubShowSubLocation");
	<?php }?>
	<?php if(isset($_GET['bk']) && $_GET['bk'] == 'Y'){ ?>
		window.setTimeout(function() {
			submitForm(true);
		}, 3000);
	<?php } ?>
</script>
<?php #} ?>
<script type="text/javascript">
	function submitAddInsp() {
		var projName = document.getElementById('projName').value;
		if (projName == '') {
			jAlert('Project Name Should be Selected !');
			return false;
		} else {
			document.getElementById('defectSearch').action = '?sect=add_defect';
			document.forms["defectSearch"].submit();
			//		window.location.href="?sect=add_defect";
		}
	}

	function submitImport() {
		var projName = document.getElementById('projName').value;
		if (projName == '') {
			jAlert('Project Name Should be Selected !');
			return false;
		} else {
			document.getElementById('defectSearch').action = '?sect=import_inspections';
			document.forms["defectSearch"].submit();
			//	window.location.href="?sect=import_inspections&";
		}
	}
	var closeIdArr = new Array();

	function toggleCheck(obj) {
		var checkedStatus = obj.checked;
		$('#inspTable tbody tr').find('td:first :checkbox').each(function() {
			if (!$(this).is(':disabled')) {
				var index = $.inArray($(this).val(), closeIdArr);
				if (checkedStatus) {
					if (index == -1)
						closeIdArr.push($(this).val());
				} else {
					closeIdArr.splice(index, 1);
				}

				$(this).prop('checked', checkedStatus);
			}
		});
	}

	function closeInspections() {
		var inspectionArray = new Array();
		var projectID = document.getElementById('projName').value;
		var insepctionCount = document.inspectionTable.elements["inspectionID[]"].length;

		inspectionArray = closeIdArr;
		closeIdArr = [];
		//Filter Array
		var newArr = [];
		for (var index in inspectionArray) {
			if (inspectionArray[index]) {
				newArr.push(inspectionArray[index]);
			}
		}
		inspectionArray = newArr;
		//Filter Array
		if (inspectionArray != '') {
			if (window.XMLHttpRequest) {
				xmlhttp = new XMLHttpRequest();
			} else {
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			showProgress();
			params = "inspectionIDs=" + inspectionArray + "&projectID=" + projectID + "&uniqueId=" + Math.random();
			xmlhttp.open("POST", "inspection_close_bulk.php", true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.setRequestHeader("Content-length", params.length);
			xmlhttp.setRequestHeader("Connection", "close");
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					hideProgress();
					var jsonResult = JSON.parse(xmlhttp.responseText);
					if (jsonResult.status) {
						jAlert('Selected inspection closed successfully !');
						if (typeof oTable == 'undefined') {
							oTable = $("#inspTable").dataTable({
								"bJQueryUI": true,
								"sPaginationType": "full_numbers",
								"bProcessing": true,
								"bServerSide": true,
								"sAjaxSource": "i_defect_show_data_ajax.php?" + params,
								"bStateSave": false,
								"fnRowCallback": function(nRow, aData, iDisplayIndex) {
									if (aData[0] == 'colOpen') {
										$(nRow).addClass('colOpen');
									}
									if (aData[0] == 'colDraft') {
										$(nRow).addClass('colDraft');
									}
									if (aData[0] == 'colClosed') {
										$(nRow).addClass('colClosed');
									}
									if (aData[0] == 'colPending') {
										$(nRow).addClass('colPending');
									}
									if (aData[0] == 'colFixed') {
										$(nRow).addClass('colFixed');
									}
									if (aData[1] == 1) {
										$('#closeInsp').show('fast');
									}
									return nRow;
								},
								"aoColumnDefs": [{
									"bSearchable": false,
									"bSortable": false,
									"aTargets": [2, 8]
								}],
								"aoColumns": [{
									"bVisible": false
								}, {
									"bVisible": false
								}, null, null, null, null, null, null, null]
							});
						} else {
							oTable.fnClearTable(0);
							oTable.fnDraw();
						}
						document.getElementById('checkall').checked = false;
					} else {
						document.getElementById('checkall').checked = false;
						toggleCheck(document.getElementById('checkall'));
						jAlert('Error in updating record try after some time !');
					}
				}
			}
			xmlhttp.send(params);
		} else {
			jAlert('You must select at least one inspection to perform this action !');
			document.getElementById('checkall').checked = false;
			toggleCheck(document.getElementById('checkall'));
		}
	}
	$('#projName').change(function() {
		if (typeof oTable == 'undefined') {
			oTable = $("#inspTable").dataTable({
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": "i_defect_show_data_ajax.php?" + params,
				"bStateSave": false,
				"fnRowCallback": function(nRow, aData, iDisplayIndex) {
					if (aData[0] == 'colOpen') {
						$(nRow).addClass('colOpen');
					}
					if (aData[0] == 'colDraft') {
						$(nRow).addClass('colDraft');
					}
					if (aData[0] == 'colClosed') {
						$(nRow).addClass('colClosed');
					}
					if (aData[0] == 'colPending') {
						$(nRow).addClass('colPending');
					}
					if (aData[0] == 'colFixed') {
						$(nRow).addClass('colFixed');
					}
					if (aData[1] == 1) {
						$('#closeInsp').show('fast');
					}
					return nRow;
				},
				"aoColumnDefs": [{
					"bSearchable": false,
					"bSortable": false,
					"aTargets": [3, 7]
				}],
				"aoColumns": [{
					"bVisible": false
				}, {
					"bVisible": false
				}, null, null, null, null, null, null, null]
			});
		} else {
			oTable.fnClearTable(0);
		}
		$('#show_defect').text('');
	});

	$('.closeInspection').live("click", function() {
		var index = $.inArray($(this).val(), closeIdArr);
		if ($(this).is(':checked') === false) {
			if ($('#checkall').is(':checked'))
				$('#checkall').prop('checked', false);
		} else {
			var currentStat = true;
			$('#inspTable tbody tr').find('td:first :checkbox').each(function() {
				if (!$(this).is(':disabled'))
					if ($(this).is(':checked') === false)
						currentStat = false;
			});
			$('#checkall').prop('checked', currentStat);
		}

		if (index > -1) {
			closeIdArr.splice(index, 1);
			$(this).prop('checked', false);
		} else {
			closeIdArr.push($(this).val());
			$(this).prop('checked', true);
		}
	});

	function clearDateRaised() {
		document.getElementById('DRF').value = '';
		document.getElementById('DRT').value = '';
	}

	function clearFixedByDate() {
		document.getElementById('FBDF').value = '';
		document.getElementById('FBDT').value = '';
	}
</script>
