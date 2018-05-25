<?php
$js_version = rand();
function FillSelectBox($field, $table, $where, $group, $reportSection){
	$q = "select $field from $table where $where GROUP BY $group";
	$q = mysql_query($q);
	$data = '';
	while($q1=mysql_fetch_array($q)){
		$data .='<option value="'.$q1[0].'"';
		if($reportSection == 'qc_section'){
			if($q1[0] == $_SESSION['ir']['projName'])
				$data .= 'selected="selected"';
		}
		if($reportSection == 'pm_section'){
			if($q1[0] == $_SESSION['pmr']['projName'])
				$data .= 'selected="selected"';
		}
		if($reportSection == 'qa_section'){
			if($q1[0] == $_SESSION['qar']['projNameQA'])
			$data .= 'selected="selected"';
		}
		if($reportSection == 'cl_section'){
			if($q1[0] == $_SESSION['clr']['projNameCL'])
				$data .= 'selected="selected"';
		}
		if($reportSection == 'non_conformance'){
			if($q1[0] == $_SESSION['noconf']['projNameNC'])
				$data .= 'selected="selected"';
		}
		$data .='>'.$q1[1].'</option>';
	}
	echo $data;
}
?>
<link rel="stylesheet" type="text/css" media="all" href="css/website.css" />
<script type="text/javascript">
var align = 'center';
var top = 30;
var width = 825;
var padding = 10;
var backgroundColor = '#FFFFFF';
var borderColor = '#333333';
var borderWeight = 4;
var borderRadius = 5;
var fadeOutTime = 300;
var disableColor = '#666666';
var disableOpacity = 40;
var loadingImage = 'images/loadingAnimation.gif';		//Use relative path from this page
window.onload = function(){
	new JsDatePick({ useMode:2, target:"DRF", dateFormat:"%d-%m-%Y" });
	new JsDatePick({ useMode:2, target:"DRT", dateFormat:"%d-%m-%Y" });
	new JsDatePick({ useMode:2, target:"FBDF", dateFormat:"%d-%m-%Y" });
	new JsDatePick({ useMode:2, target:"FBDT", dateFormat:"%d-%m-%Y" });
	new JsDatePick({ useMode:2, target:"DRFPM", dateFormat:"%d-%m-%Y" });
	new JsDatePick({ useMode:2, target:"DRTPM", dateFormat:"%d-%m-%Y" });
	new JsDatePick({ useMode:2, target:"FBDFPM", dateFormat:"%d-%m-%Y" });
	new JsDatePick({ useMode:2, target:"FBDTPM", dateFormat:"%d-%m-%Y" });
};
</script>
<style type="text/css">
fieldset.emailContainer{border: 1px solid #7F9DB9; padding: 15px;}
@import "css/jquery.datepick.css";
div#innerModalPopupDiv{color:black; }
table td{padding:5px;}
table{ margin-left:10px;}
#DRF, #DRT, #FBDF, #FBDT, #DRFPM, #DRTPM, #FBDFPM, #FBDTPM{ background:#FFF; cursor:default; height:20px; -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; }
.error-edit-profile { width: 220px; }
div#mainContainer{ color:#000; width:800px; margin:auto; min-height:70px;; max-height:650px; overflow-y:scroll;}
.buttonDiv img{ cursor:pointer; }
.pagination{ float: right; width:290px; height:28px; margin:10px 15px 0 0; text-align:right;}
.page_active{ background-image:url(images/grey.png); background-repeat:no-repeat; width:27px; height:22px; color:#000; display:block; float:left; text-align:center; margin:0 0px 0 5px; padding-top:2px; text-decoration:none;}
.page_deactive{ background-image:url(images/blue.png); background-repeat:no-repeat; width:27px; height:22px; color:#000; display:block; float:left; text-align:center; margin:0 0 0 5px;padding-top:2px;text-decoration:none;}
.page_deactive:hover{ background-image:url(images/grey.png); background-repeat:no-repeat; width:27px; height:22px; color:#000; display:block; float:left; text-align:center; margin:0 0 0 5px;padding-top:2px;text-decoration:none; cursor:pointer;}
table.collapse { border-collapse: collapse; border: 1pt solid black; }
table.collapse td { border: 1pt solid black; padding: 2px; font-family:Arial, Helvetica, sans-serif; font-size:10px;}
div#htmlContainer div.footer{display:none;}
</style>
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" />
<!-- Date Picker files start here -->
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<!-- Date Picker files -->
<script language="javascript" type="text/javascript" src="js/ajax.js?version=<?=$js_version?>"></script>
</head>
<body id="dt_example">
<script language="javascript" type="text/javascript"><!-- Page JS Functions -->
function startAjax1(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=locationPM && proID="+val,"ShowLocation1");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=issuedToPM && proID="+val,"ShowIssuedTo1");
}
function subLocate1(obj){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationPM && proID="+obj,"ShowSubLocation1");
}
function subLocate_sub(obj){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocation_subPM && proID="+obj,"ShowLocation_sub_id");
}
function sub_subLocate(obj){
	var subLocation = [];
	$('#subLocation :selected').each(function(i, selected){
		subLocation[i] = $(selected).val();
	});
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=sub_subLocation && proID="+subLocation+ " && pageName=reportTab","Sub_ShowSubLocation");
	if(subLocation[0] == ''){
		$("#sub_subLocation").html('<option value="">Select</option>');
	}
	$("#subSubLocation3").html('<option value="">Select</option>');
	$("#subSubLocation4").html('<option value="">Select</option>');
}

//Non Conformance
function startAjaxnoconfCL(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=locationnoconfCL&&proID="+val,"ShowLocationnoconfCL");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=issueToList&&proID="+val,"showIssuedToNoconf");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=checklist&&proID="+val,"checklists");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=raisedBynoconf&&proID="+val,"ShowRaisedBynoconf");
	$("#subLocationCLnoconf").html('<option value="">Select</option>');
	$("#sub_subLocationCLnoconf").html('<option value="">Select</option>');
	$("#SubLocation2noconf").html('<option value="">Select</option>');
	$("#SubLocation3noconf").html('<option value="">Select</option>');
}
function subLocate1noconfCL(obj){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationCLnoconf&&proID="+obj,"ShowSubLocationCLnoconf");
	$("#sub_subLocationCLnoconf").html('<option value="">Select</option>');
	$("#SubLocation2noconf").html('<option value="">Select</option>');
	$("#SubLocation3noconf").html('<option value="">Select</option>');
}
function subLocate2noconfCL(obj){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=sub_subLocationCLnoconf&&proID="+obj,"Sub_ShowSubLocationCLnoconf");
	$("#SubLocation2noconf").html('<option value="">Select</option>');
	$("#SubLocation3noconf").html('<option value="">Select</option>');
}
function subLocation1noconfCL(obj){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=SubLocation2noconf&&proID="+obj,"ShowSubLocation2CLnoconf");
	$("#SubLocation3noconf").html('<option value="">Select</option>');
}
function subLocate3noconf(obj){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=SubLocation3noconf&&proID="+obj,"ShowSubLocation3CLnoconf");
}
//End non conformance

function startAjax(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=location && proID="+val,"ShowLocation");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=inspecrBy && proID="+val,"ShowInspecrBy");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=issuedTo && proID="+val,"ShowIssuedTo");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=userRole&& proID="+val,"userRole");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=raisedBy&& proID="+val,"ShowRaisedBy");
	$("#subLocation").html('<option value="">Select</option>');
	$("#sub_subLocation").html('<option value="">Select</option>');
	$("#subSubLocation3").html('<option value="">Select</option>');
	$("#subSubLocation4").html('<option value="">Select</option>');
}
function subLocate(obj){
	var locations = [];
		$('#location :selected').each(function(i, selected){
  		locations[i] = $(selected).val();
		});
	if(locations[0] != undefined && locations[0] != ''){
		AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocation && proID="+locations+ " && pageName=reportTab","ShowSubLocation");
	}else{
		$("#subLocation").html('<option value="">Select</option>');
	}
	$("#sub_subLocation").html('<option value="">Select</option>');
	$("#subSubLocation3").html('<option value="">Select</option>');
	$("#subSubLocation4").html('<option value="">Select</option>');
}
function startAjaxQA(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=locationQA && proID="+val,"ShowLocationQA");
}
function subLocate1QA(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationQA1 && proID="+val,"ShowSubLocation1QA");
}
function subLocate2QA(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationQA2 && proID="+val,"ShowSubLocation2QA");
}
function subLocate3QA(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationQA3 && proID="+val,"ShowSubLocation3QA");
}
function changeScreen(screenID){
	switch (screenID){
		case "buttoon_progressMonitoring":
		<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
				document.getElementById('buttoon_progressMonitoringScreen').style.display = 'block';
				document.getElementById('buttoon_progressMonitoring').setAttribute("class", "buttoon_progressMonitoringActive");
		<?php }?>
		<?php if($_SESSION['web_report_quality_assuarance'] == 1){?>
				document.getElementById('buttoon_QATaskScreen').style.display = 'none';
				document.getElementById('buttoon_qatask').setAttribute("class", "buttoon_qatask inspections green_small");
		<?php }?>
		<?php if($_SESSION['web_report_checklist'] == 1){?>
				document.getElementById('buttoon_CLScreen').style.display = 'none';
				document.getElementById('buttoon_checklist').setAttribute("class", "buttoon_checklist");
		<?php } ?>
		<?php if($_SESSION['ww_is_company'] == 1){?>
				document.getElementById('buttoon_CompScreen').style.display = 'none';
				document.getElementById('button_companySummary').setAttribute("class", "button_companySummary");
		<?php } ?>
				document.getElementById('show_defect').innerHTML = '';
				document.getElementById('button_qualityControlScreen').style.display = 'none';
				document.getElementById('button_qualityControl').setAttribute("class", "button_qualityControl");

				document.getElementById('buttoon_qrcodeScreen').style.display = 'none';
				document.getElementById('buttoon_qrcode').setAttribute("class", "buttoon_qrcode");

				document.getElementById('buttoon_non_conformanceScreen').style.display = 'none';
				document.getElementById('buttoon_non_conformance').setAttribute("class", "buttoon_non_conformance green_small");
		break;

		case "button_qualityControl":
		<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
				document.getElementById('buttoon_progressMonitoringScreen').style.display = 'none';
				document.getElementById('buttoon_progressMonitoring').setAttribute("class", "buttoon_progressMonitoring");
		<?php } ?>
		<?php if($_SESSION['web_report_quality_assuarance'] == 1){?>
				document.getElementById('buttoon_QATaskScreen').style.display = 'none';
				document.getElementById('buttoon_qatask').setAttribute("class", "buttoon_qatask inspections green_small");
		<?php } ?>
		<?php if($_SESSION['web_report_checklist'] == 1){?>
				document.getElementById('buttoon_CLScreen').style.display = 'none';
				document.getElementById('buttoon_checklist').setAttribute("class", "buttoon_checklist inspections green_small");
		<?php } ?>
		<?php if($_SESSION['ww_is_company'] == 1){?>
				//document.getElementById('buttoon_CompScreen').style.display = 'none';
				document.getElementById('button_companySummary').setAttribute("class", "button_companySummary");
		<?php } ?>
				document.getElementById('show_defect').innerHTML = '';
				document.getElementById('button_qualityControlScreen').style.display = 'block';
				document.getElementById('button_qualityControl').setAttribute("class", "button_qualityControlActive inspections");

				document.getElementById('buttoon_qrcodeScreen').style.display = 'none';
				document.getElementById('buttoon_qrcode').setAttribute("class", "buttoon_qrcode inspections green_small");

				document.getElementById('buttoon_non_conformanceScreen').style.display = 'none';
				document.getElementById('buttoon_non_conformance').setAttribute("class", "buttoon_non_conformance green_small");
		break;

		case "buttoon_qatask":
		<?php if($_SESSION['web_report_quality_assuarance'] == 1){?>
				document.getElementById('buttoon_QATaskScreen').style.display = 'block';
				document.getElementById('buttoon_qatask').setAttribute("class", "buttoon_qataskActive inspections");
		<?php }?>
		<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
				document.getElementById('buttoon_progressMonitoringScreen').style.display = 'none';
				document.getElementById('buttoon_progressMonitoring').setAttribute("class", "buttoon_progressMonitoring inspections green_small");
		<?php }?>
		<?php if($_SESSION['web_report_checklist'] == 1){?>
				document.getElementById('buttoon_CLScreen').style.display = 'none';
				document.getElementById('buttoon_checklist').setAttribute("class", "buttoon_checklist inspections green_small");
		<?php } ?>
		<?php if($_SESSION['ww_is_company'] == 1){?>
				document.getElementById('buttoon_CompScreen').style.display = 'none';
				document.getElementById('button_companySummary').setAttribute("class", "button_companySummary inspections green_small");
		<?php } ?>
				document.getElementById('show_defect').innerHTML = '';
				document.getElementById('button_qualityControlScreen').style.display = 'none';
				document.getElementById('button_qualityControl').setAttribute("class", "button_qualityControl inspections green_small");

				document.getElementById('buttoon_qrcodeScreen').style.display = 'none';
				document.getElementById('buttoon_qrcode').setAttribute("class", "buttoon_qrcode inspections green_small");

				document.getElementById('buttoon_non_conformanceScreen').style.display = 'none';
				document.getElementById('buttoon_non_conformance').setAttribute("class", "buttoon_non_conformance inspections green_small");
		break;

		case "buttoon_checklist":
		<?php if($_SESSION['web_report_quality_assuarance'] == 1){?>
				document.getElementById('buttoon_QATaskScreen').style.display = 'none';
				document.getElementById('buttoon_qatask').setAttribute("class", "buttoon_qatask inspections green_small");
		<?php }?>
		<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
				document.getElementById('buttoon_progressMonitoringScreen').style.display = 'none';
				document.getElementById('buttoon_progressMonitoring').setAttribute("class", "buttoon_progressMonitoring inspections green_small");
		<?php }?>
		<?php if($_SESSION['web_report_checklist'] == 1){?>
				document.getElementById('buttoon_CLScreen').style.display = 'block';
				document.getElementById('buttoon_checklist').setAttribute("class", "buttoon_checklistActive inspections");
		<?php } ?>
		<?php if($_SESSION['ww_is_company'] == 1){?>
				// document.getElementById('buttoon_CompScreen').style.display = 'none';
				document.getElementById('button_companySummary').setAttribute("class", "button_companySummary inspections green_small");
		<?php } ?>
				document.getElementById('show_defect').innerHTML = '';
				document.getElementById('button_qualityControlScreen').style.display = 'none';
				document.getElementById('button_qualityControl').setAttribute("class", "button_qualityControl inspections green_small");

				document.getElementById('buttoon_qrcodeScreen').style.display = 'none';
				document.getElementById('buttoon_qrcode').setAttribute("class", "buttoon_qrcode inspections green_small");

				document.getElementById('buttoon_non_conformanceScreen').style.display = 'none';
				document.getElementById('buttoon_non_conformance').setAttribute("class", "buttoon_non_conformance inspections green_small");
		break;

		case "button_companySummary":
		<?php if($_SESSION['web_report_quality_assuarance'] == 1){?>
				document.getElementById('buttoon_QATaskScreen').style.display = 'none';
				document.getElementById('buttoon_qatask').setAttribute("class", "buttoon_qatask inspections green_small");
		<?php }?>
		<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
				document.getElementById('buttoon_progressMonitoringScreen').style.display = 'none';
				document.getElementById('buttoon_progressMonitoring').setAttribute("class", "buttoon_progressMonitoring inspections green_small");
		<?php }?>
		<?php if($_SESSION['web_report_checklist'] == 1){?>
				document.getElementById('buttoon_CLScreen').style.display = 'none';
				document.getElementById('buttoon_checklist').setAttribute("class", "buttoon_checklist inspections green_small");
		<?php } ?>
				document.getElementById('button_qualityControlScreen').style.display = 'none';
				document.getElementById('button_qualityControl').setAttribute("class", "button_qualityControl inspections green_small");

				document.getElementById('show_defect').innerHTML = '';
				document.getElementById('buttoon_CompScreen').style.display = 'block';
				document.getElementById('button_companySummary').setAttribute("class", "button_companySummaryActive inspections");

				document.getElementById('buttoon_qrcodeScreen').style.display = 'none';
				document.getElementById('buttoon_qrcode').setAttribute("class", "buttoon_qrcode");

				document.getElementById('buttoon_non_conformanceScreen').style.display = 'none';
				document.getElementById('buttoon_non_conformance').setAttribute("class", "buttoon_non_conformance inspections green_small");
		break;

		case "buttoon_qrcode":
		<?php if($_SESSION['web_report_quality_assuarance'] == 1){?>
				$('#buttoon_QATaskScreen').hide();
				$('#buttoon_qatask').attr("class", "buttoon_qatask inspections green_small");
		<?php }?>
		<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
				$('#buttoon_progressMonitoringScreen').hide();
				$('#buttoon_progressMonitoring').attr("class", "buttoon_progressMonitoring inspections green_small");
		<?php }?>
		<?php if($_SESSION['web_report_checklist'] == 1){?>
				$('#buttoon_CLScreen').hide();
				$('#buttoon_checklist').attr("class", "buttoon_checklist inspections green_small");
		<?php } ?>
		<?php if($_SESSION['ww_is_company'] == 1){?>
				$('#buttoon_CompScreen').show();
				$('#button_companySummary').attr("class", "button_companySummaryActive inspections");
		<?php } ?>
				$('#button_qualityControlScreen').hide();
				$('#button_qualityControl').attr("class", "button_qualityControl inspections green_small");

				$('#buttoon_non_conformanceScreen').hide();
				$('#buttoon_non_conformance').attr("class", "buttoon_non_conformance inspections green_small");

				$('#buttoon_qrcodeScreen').show();
				$('#buttoon_qrcode').attr("class", "buttoon_qrcodeActive inspections");
		break;

		case "buttoon_non_conformance":
		<?php if($_SESSION['web_report_quality_assuarance'] == 1){?>
				$('#buttoon_QATaskScreen').hide();
				$('#buttoon_qatask').attr("class", "buttoon_qatask inspections green_small");
		<?php }?>
		<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
				$('#buttoon_progressMonitoringScreen').hide();
				$('#buttoon_progressMonitoring').attr("class", "buttoon_progressMonitoring inspections green_small");
		<?php }?>
		<?php if($_SESSION['web_report_checklist'] == 1){?>
				$('#buttoon_CLScreen').hide();
				$('#buttoon_checklist').attr("class", "buttoon_checklist inspections green_small");
		<?php } ?>
		<?php if($_SESSION['ww_is_company'] == 1){?>
				$('#buttoon_CompScreen').show();
				$('#button_companySummary').attr("class", "button_companySummaryActive inspections");
		<?php } ?>
				$('#button_qualityControlScreen').hide();
				$('#button_qualityControl').attr("class", "button_qualityControl inspections green_small");

				$('#buttoon_qrcodeScreen').hide();
				$('#buttoon_qrcode').attr("class", "buttoon_qrcode inspections green_small");

				$('#buttoon_non_conformanceScreen').show();
				$('#buttoon_non_conformance').attr("class", "buttoon_non_conformanceActive inspections");
		break;

		default :
			document.getElementById('show_defect').innerHTML = '';
			document.getElementById('button_qualityControlScreen').style.display = 'block';
			document.getElementById('button_qualityControl').setAttribute("class", "button_qualityControlActive inspections");
	}
}
function startAjaxCL(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=locationCL && proID="+val,"ShowLocationCL");
}
function subLocate1CL(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationCL1 && proID="+val,"ShowSubLocationCL");
}
function subLocate2CL(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationCL2 && proID="+val,"Sub_ShowSubLocationCL");
}
function resetIds(){
	document.getElementById('projectError').style.display = 'none';
	document.getElementById('reportError').style.display = 'none';
	document.getElementById('issueToError').style.display = 'none';
<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
	document.getElementById('reportErrorPM').style.display = 'none';
	document.getElementById('projectPMError').style.display = 'none';
<? }?>
<?php if($_SESSION['web_report_quality_assuarance'] == 1){?>
	document.getElementById('projectQAError').style.display = 'none';
	document.getElementById('locationQAError').style.display = 'none';
	document.getElementById('subLocationQA1Error').style.display = 'none';
	document.getElementById('subLocationQA2Error').style.display = 'none';
	document.getElementById('reportErrorQA').style.display = 'none';
<? }?>
<?php if($_SESSION['web_report_checklist'] == 1){?>
	document.getElementById('projectErrorCL').style.display = 'none';
<? }?>
}
function startAjaxCpmp(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=userComp && proID="+val,"userComp");
}
</script>
<div id="container" style="width:99%;margin-top:25px;min-height:510px;" >
<?php if (isset($_SESSION["ww_is_builder"]) and $_SESSION["ww_is_builder"]== 1){
	$owner_id = $_SESSION['ww_builder_id'];
}else{
	$owner_id = $_SESSION['ww_is_company'];
}?>
<script language="javascript" type="text/javascript"><!-- Ajax JS Functions -->
function printDiv(){
	var divToPrint = document.getElementById('mainContainer');
//	var newWin = window.open('', 'PrintWindow', '', false);
	var newWin = window.open('', 'PrintWindow', '', false);
	newWin.document.open();
	newWin.document.write('<html><head><meta http-equiv="X-UA-Compatible" content="IE=8"><style>body { size:A4-landscape; }</style></head><body onload="window.print();"><style type="text/css">table.collapse { border-collapse: collapse; border: 1pt solid black; }table.collapse td { border: 1pt solid black; padding: 2px; }.pagination{display:none;}.buttonDiv{display:none;}.footer{display:block;}</style>'+divToPrint.innerHTML+'</body></html>');
	newWin.document.close();
//	setTimeout(function(){newWin.close();},10000);
}
function checkDates(date1, date2, element){
	var obj = date1.value;
	var obj1 =  date2.value;
	if(obj!='' || obj1!=''){
		if(obj=='' && obj1!=''){
			jAlert('Please Select Form Date First !');
			return false;
		}else{
			var fromDate = new Date(obj.substr(6,4), obj.substr(3,2), obj.substr(0,2));
			var toDate = new Date(obj1.substr(6,4), obj1.substr(3,2), obj1.substr(0,2));
			if((toDate.getTime() - fromDate.getTime()) < 0){jAlert(element+' To Date in Not Less Than Form Date !');return false;}
		}
	}
}
function pageScroll(limit){
	try{
		var params = '';
		var startWith = limit;
		var projName = document.getElementById('projName').value;
		var reportType = document.getElementById('reportType').value;
		var sortby = document.getElementById('sortby').value;
		//var location = document.getElementById('location').value;

		var location = "";
		for(var x=0; x<document.getElementById('location').length; x++){
				if (document.getElementById('location')[x].selected){

					if(location == ''){
					location = document.getElementById('location')[x].value;
					}else{
					location += "@@@"+document.getElementById('location')[x].value;
					}
				}
			}

		var subLocation = document.getElementById('subLocation').value;
	var sub_subLocation = document.getElementById('sub_subLocation').value;
	var searchKeyward = document.getElementById('searchKey').value;
		var status = document.getElementById('status').value;
		var inspectedBy = document.getElementById('inspectedBy').value;
	var issuedTo = '';
	for(var x=0; x<document.getElementById('issuedTo').length; x++){
		if (document.getElementById('issuedTo')[x].selected){
			if(issuedTo == ''){
				issuedTo = document.getElementById('issuedTo')[x].value;
			}else{
				issuedTo += "@@@"+document.getElementById('issuedTo')[x].value;
			}
		}
	}
		var priority = '';//document.getElementById('priority').value;
		var inspecrType = document.getElementById('inspecrType').value;
		var costAttribute = document.getElementById('costAttribute').value;
		var raisedBy = document.getElementById('raisedBy').value;
		var DRF = document.getElementById('DRF').value;
		var DRT = document.getElementById('DRT').value;
		var FBDF = document.getElementById('FBDF').value;
		var FBDT = document.getElementById('FBDT').value;

		if(projName == ''){ document.getElementById('projectError').style.display = 'block'; return false; }
		if(reportType == ''){ document.getElementById('reportError').style.display = 'block'; return false; }

		var dateChackRaised = checkDates(document.getElementById('DRF'), document.getElementById('DRT'), 'In Date Raised Field');
		var dateChackFixed = checkDates(document.getElementById('FBDF'), document.getElementById('FBDT'), 'In Fix By Date Field');
		if(dateChackRaised === false){	return false;	}
		if(dateChackFixed === false){	return false;	}

		params = "projName="+projName+"&location="+location+"&subLocation="+subLocation+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&priority="+priority+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&raisedBy="+raisedBy+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&startWith="+startWith+"&report_type="+reportType+"&searchKeyward="+searchKeyward+"&sub_subLocation="+sub_subLocation+"&sortby="+sortby+"&name="+Math.random();
		if(reportType == 'pdfDetail'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_detail.php?'+params, loadingImage);
		}else if(reportType == 'pdfDetailHD'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_detail_hd.php?'+params, loadingImage);
		}else if(reportType == 'pdfSummayWithOutImages' || reportType == 'pdfSummayWithImages'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_summary.php?'+params, loadingImage);
		}else if(reportType == 'csvReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_csv.php?'+params, loadingImage);
		}
	}catch(e){
		//alert(e.message);
	}
}
function emailPDF(){
//Get all the issue to email, name list here
	var htmlView = '';
	var projName = $('#projName').val();
	var issueToEmail = $('#emailIssueToData').html();
	var jsonResult = JSON.parse(issueToEmail);
	var reportType = jsonResult.reportType;
	var emailIds = new Array();
    for(var i=0; i<jsonResult.arrSize; i++){
		emailIds[i] = jsonResult[i].email;
	}
	htmlView += '<fieldset class="emailContainer"><legend>Email Report</legend><table width="50%" border="0" cellpadding="0" cellspacing="0" align="center"><tr><td width="16%" align="right">To</td><td width="3%">&nbsp;:&nbsp;</td><td width="81%"><textarea name="toEmail" id="toEmail" cols="30" rows="3">'+emailIds.join()+'</textarea></td></tr><tr><td align="right">Cc</td><td>&nbsp;:&nbsp;</td><td><input type="text" name="ccEmail" id="ccEmail" size="40" /></td></tr><tr><td align="right">Bcc</td><td>&nbsp;:&nbsp;</td><td><input type="text" name="bccEmail" id="bccEmail" size="40" /></td></tr><tr><td align="right">Subject</td><td>&nbsp;:&nbsp;</td><td><input type="text" name="subEmail" id="subEmail" size="40" value="'+reportType+'" /></td></tr><tr><td align="right">Attachment</td><td>&nbsp;:&nbsp;</td><td><div id="attachEmail">'+reportType.replace(" ","_")+'.pdf</div><input type="hidden" name="project_id" id="project_id" value="'+projName+'" /><input type="hidden" name="report_type" id="report_type" value="'+reportType+'" /></td></tr><tr><td align="right" valign="top">Message</td><td valign="top">&nbsp;:&nbsp;</td><td><textarea name="descEmail" id="descEmail" cols="30" rows="15"></textarea></td></tr><tr><td align="center" colspan="3"><img onClick="sendEmailPDF();" src="images/send.png" style="float:left;" /><img onClick="closePopup(300);" src="images/cancel.png" style="float:left;margin-left:50px;" /></td></tr></table></fieldset>';
	$('#mainContainer').html(htmlView);
}
function emailPDFSubCont(){
//Get all the issue to email, name list here
	var htmlView = '';
	var projName = $('#projName').val();
	var issueToEmail = $('#emailIssueToData').html();
	var jsonResult = JSON.parse(issueToEmail);
	var reportType = jsonResult.reportType;
	var emailIds = new Array();
	var issueToName = new Array();
	for(var i=0; i<jsonResult.arrSize; i++){
		emailIds[i] = jsonResult[i].email;
		issueToName[i] = jsonResult[i].issueToName;
	}
	htmlView += '<fieldset class="emailContainer"><legend>Email Report</legend><table width="50%" border="0" cellpadding="0" cellspacing="0" align="center"><tr><td align="center" colspan="4">Issue To List<input type="hidden" id="lupCount" value="'+jsonResult.arrSize+'" /><input type="hidden" id="issueToArr" value="'+issueToEmail+'" /></td></tr>';
    for(var i=0; i<jsonResult.arrSize; i++){
		htmlView += '<tr><td width="16%" align="right"><input type="checkbox" name="emailCheck" id="emailCheck'+i+'" value="1"  /></td><td width="3%">&nbsp;:&nbsp;</td><td width="81%"><div id="issueToName'+i+'">'+jsonResult[i].issueToName+'</div></td><td>';
		if(jsonResult[i].email != ''){
			htmlView += '<div><input type="hidden" name="newIssueToEmail[]" id="issueToEmail'+i+'" value="'+jsonResult[i].email+'" />('+jsonResult[i].email+')</div>';
		}else{
			htmlView += '<input type="text" name="newIssueToEmail[]" id="issueToEmail'+i+'" value="" />';
		}
		htmlView += '</td></tr>';
	}
	htmlView += '<tr><td align="right">Cc</td><td>&nbsp;:&nbsp;</td><td colspan="2"><input type="text" name="ccEmail" id="ccEmail" size="40" /></td></tr><tr><td align="right">Bcc</td><td>&nbsp;:&nbsp;</td><td colspan="2"><input type="text" name="bccEmail" id="bccEmail" size="40" /></td></tr><tr><td align="right">Subject</td><td>&nbsp;:&nbsp;</td><td colspan="2"><input type="text" name="subEmail" id="subEmail" size="40" value="'+reportType+'" /><input type="hidden" name="project_id" id="project_id" value="'+projName+'" /><input type="hidden" name="report_type" id="report_type" value="'+reportType+'" /></td></tr><tr><td align="right" valign="top">Message</td><td valign="top">&nbsp;:&nbsp;</td><td colspan="2"><textarea name="descEmail" id="descEmail" cols="30" rows="15"></textarea></td></tr><tr><td align="center" colspan="4"><img onClick="sendEmailPDFSubCont();" src="images/send.png" style="float:left;" /><img onClick="closePopup(300);" src="images/cancel.png" style="float:left;margin-left:50px;" /></td></tr></table></fieldset>';
	$('#mainContainer').html(htmlView);
}
function sendEmailPDFSubCont(){
		try{
			var lupCount = $('#lupCount').val();
			var issueToArr = new Array();
			var emailArr = new Array();
			var j=0;
			for(var i=0; i<lupCount; i++){
				if($('#emailCheck'+i).is(':checked')){
					issueToArr[j] = encodeURIComponent($('#issueToName'+i).html());
					emailArr[j] = encodeURIComponent($('#issueToEmail'+i).val());
				j++}
			}
			var cc = $('#ccEmail').val();
			var bcc = $('#bccEmail').val();
			var subject = $('#subEmail').val();

			var descEmail = $('#descEmail').val();

			issueToList = emailArr.join('@@@')+"###"+issueToArr.join('@@@');

			downloadPDF(function(fileURL){
				$('#reportURL').html(fileURL);
				var attachmentArr = $('#issueArr').text();
				var jsonResultAtt = JSON.parse(attachmentArr);
				var attachArr = new Array();
				var issueArr = new Array();
				for(var k=0; k<jsonResultAtt.arraySize; k++){
					issueArr[k] = encodeURIComponent(jsonResultAtt[k].issueToName);
					attachArr[k] = encodeURIComponent(jsonResultAtt[k].fileAttach);
				}
				params = "to="+emailArr.join()+"&cc="+cc+"&bcc="+bcc+"&subject="+subject+"&attachment="+attachArr.join()+"&descEmail="+descEmail+"&issueArr="+issueArr.join()+"&attachmentNew=Y&name="+Math.random();

				if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
				document.getElementById('spinner').innerHTML = '<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Sending Mail ..<br/>This may take several minutes.</div>';
				showProgress();

				xmlhttp.open("POST", 'send_report_mail_subCont.php', true);
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
			}, issueToList);
		}catch(e){
			alert(e.message);
		}
}
function sendEmailPDF(){
	var pdfOutPut = downloadPDF(function(fileURL){
		console.log(fileURL);
		$('#reportURL').html(fileURL);
		try{
			var to = $('#toEmail').val();
			var cc = $('#ccEmail').val();
			var bcc = $('#bccEmail').val();
			var subject = $('#subEmail').val();
			var attachment = $('a.view_btn').attr('href');
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
	});
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

function downloadPDF(callback, issueToList){
	callback = typeof callback !== 'undefined' ? callback : 0;
	issueToList = typeof issueToList !== 'undefined' ? issueToList : 0;

	document.getElementById('spinner').innerHTML = '<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.</div>';
	try{
		var params = '';
		var projName = document.getElementById('projName').value;
		var reportType = document.getElementById('reportType').value;
		var sortby = document.getElementById('sortby').value;
		//var location = document.getElementById('location').value;

		var location='';
		for(var x=0; x<document.getElementById('location').length; x++){
			if (document.getElementById('location')[x].selected){

				if(location == ''){
				location = document.getElementById('location')[x].value;
				}else{
				location += "@@@"+document.getElementById('location')[x].value;
				}
			}
		}
		var locationName = $("#location option:selected").text();

		//var subLocation = document.getElementById('subLocation').value;
		var subLocation = ''
		for(var x=0; x<document.getElementById('subLocation').length; x++){
			if (document.getElementById('subLocation')[x].selected){
				if(subLocation == ''){
					subLocation = document.getElementById('subLocation')[x].value;
				}else{
					subLocation += "@@@"+document.getElementById('subLocation')[x].value;
				}
			}
		}
		var subLocationName = $("#subLocation option:selected").text();

		//var sub_subLocation = document.getElementById('sub_subLocation').value;
		var sub_subLocation = ''
		for(var x=0; x<document.getElementById('sub_subLocation').length; x++){
			if (document.getElementById('sub_subLocation')[x].selected){
				if(sub_subLocation == ''){
					sub_subLocation = document.getElementById('sub_subLocation')[x].value;
				}else{
					sub_subLocation += "@@@"+document.getElementById('sub_subLocation')[x].value;
				}
			}
		}
		var sub_subLocationName = $("#sub_subLocation option:selected").text();

		//var sub_subLocation2
		var sub_subLocation2 = '';
		for(var x=0; x<document.getElementById('subSubLocation3').length; x++){
			if (document.getElementById('subSubLocation3')[x].selected){
				if(sub_subLocation2 == ''){
					sub_subLocation2 = document.getElementById('subSubLocation3')[x].value;
				}else{
					sub_subLocation2 += "@@@"+document.getElementById('subSubLocation3')[x].value;
				}
			}
		}
		var sub_subLocationName2 = $("#subSubLocation3 option:selected").text();

		//var sub_subLocation3
		var sub_subLocation3 = '';
		for(var x=0; x<document.getElementById('subSubLocation4').length; x++){
			if (document.getElementById('subSubLocation4')[x].selected){
				if(sub_subLocation3 == ''){
					sub_subLocation3 = document.getElementById('subSubLocation4')[x].value;
				}else{
					sub_subLocation3 += "@@@"+document.getElementById('subSubLocation4')[x].value;
				}
			}
		}
		var sub_subLocationName3 = $("#subSubLocation4 option:selected").text();

		var searchKeyward = document.getElementById('searchKey').value;

		//var status = document.getElementById('status').value;
		var status = ''
		for(var x=0; x<document.getElementById('status').length; x++){
			if (document.getElementById('status')[x].selected){
				if(status == ''){
					status = document.getElementById('status')[x].value;
				}else{
					status += "@@@"+document.getElementById('status')[x].value;
				}
			}
		}
		var inspectedBy = document.getElementById('inspectedBy').value;
	var issuedTo = '';
	for(var x=0; x<document.getElementById('issuedTo').length; x++){
		if (document.getElementById('issuedTo')[x].selected){
			if(issuedTo == ''){
				issuedTo = document.getElementById('issuedTo')[x].value;
			}else{
				issuedTo += "@@@"+document.getElementById('issuedTo')[x].value;
			}
		}
	}
	var priority = '';//document.getElementById('priority').value;
		var inspecrType = document.getElementById('inspecrType').value;
		var costAttribute = document.getElementById('costAttribute').value;
		var raisedBy = document.getElementById('raisedBy').value;
		var DRF = document.getElementById('DRF').value;
		var DRT = document.getElementById('DRT').value;
		var FBDF = document.getElementById('FBDF').value;
		var FBDT = document.getElementById('FBDT').value;

		if(projName == ''){ document.getElementById('projectError').style.display = 'block'; return false; }
		if(reportType == ''){ document.getElementById('reportError').style.display = 'block'; return false; }

		var dateChackRaised = checkDates(document.getElementById('DRF'), document.getElementById('DRT'), 'In Date Raised Field');
		var dateChackFixed = checkDates(document.getElementById('FBDF'), document.getElementById('FBDT'), 'In Fix By Date Field');

		if(dateChackRaised === false){	return false;	}
		if(dateChackFixed === false){	return false;	}

		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		showProgress();
		params = "projName="+projName+"&location="+location+"&subLocation="+subLocation+"&sub_subLocation="+sub_subLocation+"&sub_subLocation2="+sub_subLocation2+"&sub_subLocation3="+sub_subLocation3+"&locationName="+locationName+"&subLocationName="+subLocationName+"&sub_subLocationName="+sub_subLocationName+"&sub_subLocationName2="+sub_subLocationName2+"&sub_subLocationName3="+sub_subLocationName3+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&priority="+priority+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&raisedBy="+raisedBy+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&report_type=" + reportType+"&sortby="+sortby+"&searchKeyward="+searchKeyward+"&printPDF=Y&name="+Math.random();

		if(issueToList){
			params = params + "&issueToList="+issueToList;
		}

		if(reportType == 'pdfDetail'){
			var url = 'pdf/i_report_pdf_detail.php';
		}else if(reportType == 'pdfDetailHD'){
			var url = 'pdf/i_report_pdf_detail_hd.php';
		}else if(reportType == 'pdfSummayWithOutImages' || reportType == 'pdfSummayWithImages'){
			//var url = 'pdf/i_report_pdf_summary.php';
			callAjaxDownloadSR ('pdf/i_report_pdf_summary_notes.php', 1, params, callback, issueToList);
			return;
		}else if(reportType == 'pdfSummayWithoutNotes'){
			callAjaxDownloadSR ('pdf/i_report_pdf_summary_without_notes.php', 1, params, callback, issueToList);
			return;
		}else if(reportType == 'summaryCostImpact'){
			callAjaxDownloadSR ('pdf/i_report_pdf_summary_cost_impact.php', 1, params, callback, issueToList);
			return;
		}else if(reportType == 'subContractorReport'){
			callAjaxDownloadSR ('pdf/i_report_pdf_summary_sub_contractor.php', 1, params, callback, issueToList);
			return;
		}else if(reportType == 'pdfSummayWithGPS'){
			callAjaxDownloadSR ('pdf/i_report_pdf_summary_gps_details.php', 1, params, callback, issueToList);
			return;
		}else if(reportType == 'executiveReport'){
			var url = 'pdf/i_report_executive_report.php';
		}else if(reportType == 'issuedtoExecutiveReport'){
			var url = 'pdf/i_report_issuedto_executive_report.php';
		}else if(reportType == 'csvReport'){
			var url = 'pdf/i_report_csv.php';
		}
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				hideProgress();
				document.getElementById("mainContainer").style.overflow="visible";
				console.log(callback);
				if(callback){
					callback(xmlhttp.responseText);
				}else{
					document.getElementById("mainContainer").innerHTML=xmlhttp.responseText;
				}
			}
		}
		xmlhttp.send(params);
	}catch(e){
	//	alert(e.message);
	}
}
function callAjaxDownloadSR(url, page_no, params, callback, issueToList){
	try{
		document.getElementById('spinner').innerHTML = '<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.<br/>Creating first PDF</div>';
		if (window.XMLHttpRequest){ xmlhttp=new XMLHttpRequest(); }else{ xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
			params += "&page_no=" + page_no;
			xmlhttp.open("POST", url, true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.setRequestHeader("Content-length", params.length);
			xmlhttp.setRequestHeader("Connection", "close");
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					var str = xmlhttp.responseText;
					if (str.indexOf("current")>0)
					{
						var tmp1 = str.split("##");
						var tmp2 = tmp1[1].split(":");
						page_no = parseInt( tmp2[1]) + 1;
						var tmp2 = tmp1[0].split(":");
						document.getElementById('spinner').innerHTML = '<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.<br/>Creating PDF '+ (page_no) + ' of ' + tmp2[1] +  '</div>';
						callAjaxDownloadSR (url, page_no,params, callback, issueToList);
					}else{
						hideProgress();
						document.getElementById("mainContainer").style.overflow="visible";
						if(callback){
							callback(xmlhttp.responseText);
						}else{
							document.getElementById("mainContainer").innerHTML=xmlhttp.responseText;
						}
//						document.getElementById("mainContainer").innerHTML=xmlhttp.responseText;
					}
				}
			}
			xmlhttp.send(params);
	}catch(e){
		alert(e.message);
	}
}
function submitForm(){
	try{
		document.getElementById("show_defect").innerHTML = '';
		var params = '';
		var startWith = 0;
		var projName = document.getElementById('projName').value;
		var reportType = document.getElementById('reportType').value;
		var sortby = document.getElementById('sortby').value;

		//var location = document.getElementById('location').value;
		//Location validation when select sub-locations.
		if(document.getElementById('subLocation').value!=''){
			var loc = document.getElementById('location').value;
			if(loc == 'null' || loc == ''){
				$('#loc_reportError').fadeIn('fast').delay(2000).fadeOut('fast');
				return false;
			} else {
				$('#loc_reportError').fadeOut('fast');
			}
		};
        var location = "";
		for(var x=0; x<document.getElementById('location').length; x++){
			if (document.getElementById('location')[x].selected){
				if(location == ''){
					location = document.getElementById('location')[x].value;
				}else{
					location += "@@@"+document.getElementById('location')[x].value;
				}
			}
		}
		var locationName = $("#location option:selected").text();

		// var subLocation = document.getElementById('subLocation').value;
		var subLocation = ''
		for(var x=0; x<document.getElementById('subLocation').length; x++){
			if (document.getElementById('subLocation')[x].selected){
				if(subLocation == ''){
					subLocation = document.getElementById('subLocation')[x].value;
				}else{
					subLocation += "@@@"+document.getElementById('subLocation')[x].value;
				}
			}
		}
		var subLocationName = $("#subLocation option:selected").text();

		var sub_subLocation = ''
		for(var x=0; x<document.getElementById('sub_subLocation').length; x++){
			if (document.getElementById('sub_subLocation')[x].selected){
				if(sub_subLocation == ''){
					sub_subLocation = document.getElementById('sub_subLocation')[x].value;
				}else{
					sub_subLocation += "@@@"+document.getElementById('sub_subLocation')[x].value;
				}
			}
		}
		var sub_subLocationName = $("#sub_subLocation option:selected").text();

		var sub_subLocation = document.getElementById('sub_subLocation').value;

		var sub_subLocation2 = ''
		for(var x=0; x<document.getElementById('subSubLocation3').length; x++){
			if (document.getElementById('subSubLocation3')[x].selected){
				if(sub_subLocation2 == ''){
					sub_subLocation2 = document.getElementById('subSubLocation3')[x].value;
				}else{
					sub_subLocation2 += "@@@"+document.getElementById('subSubLocation3')[x].value;
				}
			}
		}
		var sub_subLocationName2 = $("#subSubLocation3 option:selected").text();

		//var sub_subLocation3
		var sub_subLocation3 = ''
		for(var x=0; x<document.getElementById('subSubLocation4').length; x++){
			if (document.getElementById('subSubLocation4')[x].selected){
				if(sub_subLocation3 == ''){
					sub_subLocation3 = document.getElementById('subSubLocation4')[x].value;
				}else{
					sub_subLocation3 += "@@@"+document.getElementById('subSubLocation4')[x].value;
				}
			}
		}
		var sub_subLocationName3 = $("#subSubLocation4 option:selected").text();

		var searchKeyward = document.getElementById('searchKey').value;
		var status = document.getElementById('status').value;
		var inspectedBy = document.getElementById('inspectedBy').value;
		var issuedTo = '';
		for(var x=0; x<document.getElementById('issuedTo').length; x++){
			if (document.getElementById('issuedTo')[x].selected){
				if(issuedTo == ''){
					issuedTo = document.getElementById('issuedTo')[x].value;
				}else{
					issuedTo += "@@@"+document.getElementById('issuedTo')[x].value;
				}
			}
		}
		var inspecrType = document.getElementById('inspecrType').value;
		var costAttribute = document.getElementById('costAttribute').value;
		var raisedBy = document.getElementById('raisedBy').value;
		var DRF = document.getElementById('DRF').value;
		var DRT = document.getElementById('DRT').value;
		var FBDF = document.getElementById('FBDF').value;
		var FBDT = document.getElementById('FBDT').value;

	if(reportType != 'healthCheckReport'){
		if(projName == ''){ document.getElementById('projectError').style.display = 'block'; return false; }
	}
	if(reportType == ''){ document.getElementById('reportError').style.display = 'block'; return false; }
	var dateChackRaised = checkDates(document.getElementById('DRF'), document.getElementById('DRT'), 'In Date Raised Field');
	var dateChackFixed = checkDates(document.getElementById('FBDF'), document.getElementById('FBDT'), 'In Fix By Date Field');
	if(dateChackRaised === false){	return false;	}
	if(dateChackFixed === false){	return false;	}

		params = "projName="+projName+"&location="+location+"&subLocation="+subLocation+"&sub_subLocation="+sub_subLocation+"&sub_subLocation2="+sub_subLocation2+"&sub_subLocation3="+sub_subLocation3+"&locationName="+locationName+"&subLocationName="+subLocationName+"&sub_subLocationName="+sub_subLocationName+"&sub_subLocationName2="+sub_subLocationName2+"&sub_subLocationName3="+sub_subLocationName3+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&priority="+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&raisedBy="+raisedBy+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&startWith="+startWith+"&report_type="+reportType+"&sortby="+sortby+"&searchKeyward="+searchKeyward+"&name="+Math.random();

//Set Data for remebarains start here
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		var url = 'ajaxFunctions.php?session_type=ir';

		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				console.log('Hello Word !');
			}
		}
		xmlhttp.send(params);
//Set Data for remebarains end here

		if(reportType == 'pdfDetail'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_detail.php?'+params, loadingImage);
		}else if(reportType == 'pdfDetailHD'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_detail_hd.php?'+params, loadingImage);
		}else if(reportType == 'pdfSummayWithOutImages' || reportType == 'pdfSummayWithImages'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_summary_notes.php?'+params, loadingImage);
		}else if(reportType == 'pdfSummayWithoutNotes'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_summary.php?'+params, loadingImage);
		}else if(reportType == 'summaryCostImpact'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_summary_cost_impact.php?'+params, loadingImage);
		}else if(reportType == 'pdfSummayWithGPS'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_summary_gps_details.php?'+params, loadingImage);
		}else if(reportType == 'executiveReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_executive_report.php?'+params, loadingImage);
		}else if(reportType == 'issuedtoExecutiveReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_issuedto_executive_report.php?'+params, loadingImage);
		}else if(reportType == 'subContractorReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_summary_sub_contractor.php?'+params, loadingImage);
		}else if(reportType == 'healthCheckReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pdf/i_report_pdf_health_check_report.php?'+params, loadingImage);
		}else if(reportType == 'csvReport'){
			if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
			showProgress();
			params = "projName="+projName+"&location="+location+"&subLocation="+subLocation+"&sub_subLocation="+sub_subLocation+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&priority="+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&raisedBy="+raisedBy+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&report_type=" + reportType+"&sortby="+sortby+"&searchKeyward="+searchKeyward+"&printPDF=Y&name="+Math.random();
			var url = 'pdf/i_report_csv.php';

			xmlhttp.open("POST", url, true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.setRequestHeader("Content-length", params.length);
			xmlhttp.setRequestHeader("Connection", "close");
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					hideProgress();
					document.getElementById("show_defect").innerHTML=xmlhttp.responseText;
				}
			}
			xmlhttp.send(params);
		}
	}catch(e){
		//alert(e.message);
	}
}
function submitFormNoConf(){
	try{
		document.getElementById("show_defect").innerHTML = '';
		var params = '';
		var startWith = 0;
		var projName = document.getElementById('projNamenoconf').value;
		if(projName == '' || projName == undefined){
			document.getElementById('projectErrornoconf').style.display = 'block'; return false;
		}

		var reportType = document.getElementById('reportTypeQANew').value;
		if(reportType == '' || reportType == undefined){
			document.getElementById('reportErrorQANew').style.display = 'block'; return false;
		}

		var checklist = document.getElementById('checklist').value;
		if(checklist == '' || checklist == undefined){
			//document.getElementById('checklistError').style.display = 'block'; return false;
		}

		if(document.getElementById('subLocationCLnoconf').value!=''){
			var loc = document.getElementById('locationnoconfCL').value;
			if(loc == 'null' || loc == ''){
				$('#loc_reportError').fadeIn('fast').delay(2000).fadeOut('fast');
				return false;
			} else {
				$('#loc_reportError').fadeOut('fast');
			}
		};
        var location = "";
		for(var x=0; x<document.getElementById('locationnoconfCL').length; x++){
			if (document.getElementById('locationnoconfCL')[x].selected){
				if(location == ''){
					location = document.getElementById('locationnoconfCL')[x].value;
				}else{
					location += "@@@"+document.getElementById('locationnoconfCL')[x].value;
				}
			}
		}
		var locationName = $("#locationnoconfCL option:selected").text();

		var subLocation = ''
		for(var x=0; x<document.getElementById('subLocationCLnoconf').length; x++){
			if (document.getElementById('subLocationCLnoconf')[x].selected){
				if(subLocation == ''){
					subLocation = document.getElementById('subLocationCLnoconf')[x].value;
				}else{
					subLocation += "@@@"+document.getElementById('subLocationCLnoconf')[x].value;
				}
			}
		}
		var subLocationName = $("#subLocationCLnoconf option:selected").text();

		var sub_subLocation = ''
		for(var x=0; x<document.getElementById('sub_subLocationCLnoconf').length; x++){
			if (document.getElementById('sub_subLocationCLnoconf')[x].selected){
				if(sub_subLocation == ''){
					sub_subLocation = document.getElementById('sub_subLocationCLnoconf')[x].value;
				}else{
					sub_subLocation += "@@@"+document.getElementById('sub_subLocationCLnoconf')[x].value;
				}
			}
		}
		var sub_subLocationName = $("#sub_subLocationCLnoconf option:selected").text();
		var sub_subLocation = document.getElementById('sub_subLocationCLnoconf').value;
		var sub_subLocation2 = ''
		for(var x=0; x<document.getElementById('SubLocation2noconf').length; x++){
			if (document.getElementById('SubLocation2noconf')[x].selected){
				if(sub_subLocation2 == ''){
					sub_subLocation2 = document.getElementById('SubLocation2noconf')[x].value;
				}else{
					sub_subLocation2 += "@@@"+document.getElementById('SubLocation2noconf')[x].value;
				}
			}
		}
		var sub_subLocationName2 = $("#SubLocation2noconf option:selected").text();

		var sub_subLocation3 = ''
		for(var x=0; x<document.getElementById('SubLocation3noconf').length; x++){
			if (document.getElementById('SubLocation3noconf')[x].selected){
				if(sub_subLocation3 == ''){
					sub_subLocation3 = document.getElementById('SubLocation3noconf')[x].value;
				}else{
					sub_subLocation3 += "@@@"+document.getElementById('SubLocation3noconf')[x].value;
				}
			}
		}
		var sub_subLocationName3 = $("#SubLocation3noconf option:selected").text();

		var searchKeyword = document.getElementById('searchKey').value;
		var status = document.getElementById('statusnoconf').value;
		var raisedBynoconf = document.getElementById('raisedBynoconf').value;
		var issuedToNoconf = document.getElementById('showIssuedToNoconf').value;

		params = "projName="+projName+"&checklist="+checklist+"&location="+location+"&subLocation="+subLocation+"&sub_subLocation="+sub_subLocation+"&sub_subLocation2="+sub_subLocation2+"&sub_subLocation3="+sub_subLocation3+"&locationName="+locationName+"&subLocationName="+subLocationName+"&sub_subLocationName="+sub_subLocationName+"&sub_subLocationName2="+sub_subLocationName2+"&sub_subLocationName3="+sub_subLocationName3+"&status="+status+"&issuedTo="+issuedToNoconf+"&raisedBy="+raisedBynoconf+"&searchKeyword="+searchKeyword+"&name="+Math.random()+"&isChecklistname=yes";

		//Set Data for remebarains start here
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		var url = 'ajaxFunctions.php?session_type=noconf';

		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				console.log('Hello Word !');
			}
		}
		xmlhttp.send(params);
		//Set Data for remebarains end here
		if(reportType == 'nonConformance'){
			modalPopup(align, top, '1000', padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_nonconf.php?'+params, loadingImage);
		}else if(reportType == 'statusReport'){
			params = "projNameQA="+projName+"&locationQA="+location+"&subLocationQA1="+subLocation+"&subLocationQA2="+sub_subLocation+"&subLocationQA3="+sub_subLocation2+"&reportTypeQA="+reportType+"&name="+Math.random();
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_qa_status.php?'+params, loadingImage);
		}

	}catch(e){
		//alert(e.message);
	}
}
function clearDivCSV(){
	document.getElementById("show_defect").innerHTML = '';
}
function pageScrollPM(limit){
	try{
		var params = "";var url = '';
		var startWith = limit;
		var projNamePM = document.getElementById("projNamePM").value;
		var locationPM = document.getElementById("locationPM").value;
		var subLocationPM = document.getElementById("subLocationPM").value;
	var subLocation_sub = document.getElementById("subLocation_subPM").value;
		var issuedToPM = document.getElementById("issuedToPM").value;
		var statusPM = document.getElementById("statusPM").value;
		var DRFPM = document.getElementById("DRFPM").value;
		var DRTPM = document.getElementById("DRTPM").value;
		var FBDFPM = document.getElementById("FBDFPM").value;
		var FBDTPM = document.getElementById("FBDTPM").value;
		var reportTypePM = document.getElementById("reportTypePM").value;
<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
		if(projNamePM == ''){ document.getElementById('projectPMError').style.display = 'block'; return false;}
		if(reportTypePM == ''){ document.getElementById('reportErrorPM').style.display = 'block'; return false;}
<? }?>

var dateChackRaised = checkDates(document.getElementById('DRFPM'), document.getElementById('DRTPM'), 'In Start Date Field');
var dateChackFixed = checkDates(document.getElementById('FBDFPM'), document.getElementById('FBDTPM'), 'In End Date Field');
if(dateChackRaised === false){	return false;	}
if(dateChackFixed === false){	return false;	}

		params = "SearchInsp="+1+"&projName=" + projNamePM + "&location=" + locationPM + "&subLocation=" + subLocationPM + "&subLocation_sub=" + subLocation_sub + "&issuedToPM=" + issuedToPM +"&status=" + statusPM + "&DRF=" + DRFPM + "&DRT=" + DRTPM + "&FBDF=" + FBDFPM + "&FBDT=" + FBDTPM+"&startWith="+startWith+"&reportTypePM="+reportTypePM+"&name="+Math.random();

		if(reportTypePM == 'inCompleteWork'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_pdf_in_complete_works.php?'+params, loadingImage);
		}else if(reportTypePM == 'doorSheet'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_pdf_door_sheet.php?'+params, loadingImage);
		}
	}catch(e){
		//alert(e.message);
	}
}
function downloadPDFPM(){
	document.getElementById('spinner').innerHTML = '<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.</div>';
	try{
		var params = "";var url = '';
		var projNamePM = document.getElementById("projNamePM").value;
		var locationPM = document.getElementById("locationPM").value;
		var subLocationPM = document.getElementById("subLocationPM").value;
		var subLocation_sub = document.getElementById("subLocation_subPM").value;
		var issuedToPM = document.getElementById("issuedToPM").value;
		var statusPM = document.getElementById("statusPM").value;
		var DRFPM = document.getElementById("DRFPM").value;
		var DRTPM = document.getElementById("DRTPM").value;
		var FBDFPM = document.getElementById("FBDFPM").value;
		var FBDFPM = document.getElementById("FBDTPM").value;
		var reportTypePM = document.getElementById("reportTypePM").value;
<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
		if(projNamePM == ''){ document.getElementById('projectPMError').style.display = 'block'; return false;}
		if(reportTypePM == ''){ document.getElementById('reportErrorPM').style.display = 'block'; return false;}
<? }?>

var dateChackRaised = checkDates(document.getElementById('DRFPM'), document.getElementById('DRTPM'), 'In Start Date Field');
var dateChackFixed = checkDates(document.getElementById('FBDFPM'), document.getElementById('FBDTPM'), 'In End Date Field');
if(dateChackRaised === false){	return false;	}
if(dateChackFixed === false){	return false;	}

		params = "SearchInsp="+1+"&projName=" + projNamePM + "&location=" + locationPM + "&subLocation=" + subLocationPM + "&subLocation_sub=" + subLocation_sub + "&issuedToPM=" + issuedToPM +"&status=" + statusPM + "&DRF=" + DRFPM + "&DRT=" + DRTPM + "&FBDF=" + FBDFPM + "&FBDT=" + FBDFPM;

		if(reportTypePM == 'inCompleteWork'){
			url = 'pdf/i_pdf_in_complete_works.php';
		}else if(reportTypePM == 'doorSheet'){
			url = 'pdf/i_pdf_door_sheet.php';
		}else if(reportTypePM == 'wallChart'){
			url = 'pdf/i_report_wall_chart.php';
			params = params+"&uniqueId="+Math.random();
		}else if(reportTypePM == 'wallCharta3'){
			url = 'pdf/i_report_wall_chart_a3.php';
			params = params+"&uniqueId="+Math.random();
		}else if(reportTypePM == 'wallCharta3summary'){
			url = 'pdf/i_report_wall_chart_a3.php';
			params = params+"&uniqueId="+Math.random();
		}else if(reportTypePM == 'wallCharta3client'){
			url = 'pdf/i_report_wall_chart_a3_client.php';
			params = params+"&uniqueId="+Math.random();
		}

		showProgress();

		url = url+'?'+params+'&name='+Math.random();
		callAjaxDownloadPM(url, 1);
	}catch(e){
		//alert(e.message);
	}
}
function callAjaxDownloadPM(url, page_no){
	try{
		if (window.XMLHttpRequest){ xmlhttp=new XMLHttpRequest(); }else{ xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
		xmlhttp.open("GET", url + "&page_no=" + page_no, true);
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				var str = xmlhttp.responseText;
				if (str.indexOf("current")>0)
				{
					var tmp1 = str.split("##");
					var tmp2 = tmp1[1].split(":");
					page_no = parseInt( tmp2[1]) + 1;
					callAjaxDownloadPM (url, page_no);
				}else{
					hideProgress();
					document.getElementById("mainContainer").style.overflow="visible";
					document.getElementById("mainContainer").innerHTML=xmlhttp.responseText;
				}
			}
		}
		xmlhttp.send();
	}catch(e){
		//alert(e.message);
	}
}
function submitFormPM(){
	try{
		document.getElementById("show_defect").innerHTML = '';
		var params = "";var url = '';
		var startWith = 0;
		var projNamePM = document.getElementById("projNamePM").value;
		var locationPM = document.getElementById("locationPM").value;
		var subLocationPM = document.getElementById("subLocationPM").value;
	var subLocation_sub = document.getElementById("subLocation_subPM").value;
		var issuedToPM = document.getElementById("issuedToPM").value;
		var statusPM = document.getElementById("statusPM").value;
		var DRFPM = document.getElementById("DRFPM").value;
		var DRTPM = document.getElementById("DRTPM").value;
		var FBDFPM = document.getElementById("FBDFPM").value;
		var FBDTPM = document.getElementById("FBDTPM").value;
		var reportTypePM = document.getElementById("reportTypePM").value;
<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
		if(projNamePM == ''){ document.getElementById('projectPMError').style.display = 'block'; return false;}
		if(reportTypePM == ''){ document.getElementById('reportErrorPM').style.display = 'block'; return false;}
<? }?>

var dateChackRaised = checkDates(document.getElementById('DRFPM'), document.getElementById('DRTPM'), 'In Start Date Field');
var dateChackFixed = checkDates(document.getElementById('FBDFPM'), document.getElementById('FBDTPM'), 'In End Date Field');
if(dateChackRaised === false){	return false;	}
if(dateChackFixed === false){	return false;	}

		params = "SearchInsp="+1+"&projName=" + projNamePM + "&location=" + locationPM + "&subLocation=" + subLocationPM + "&subLocation_sub=" + subLocation_sub + "&issuedToPM=" + issuedToPM +"&status=" + statusPM + "&DRF=" + DRFPM + "&DRT=" + DRTPM + "&FBDF=" + FBDFPM + "&FBDT=" + FBDTPM+"&startWith="+startWith+"&name="+Math.random();

//Set Data for remebarains start here
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		var url = 'ajaxFunctions.php?session_type=pmr';

		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				console.log('Hello Word !');
			}
		}
		xmlhttp.send(params);
//Set Data for remebarains end here

		if(reportTypePM == 'inCompleteWork'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_pdf_in_complete_works.php?'+params, loadingImage);
		}else if(reportTypePM == 'doorSheet'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_pdf_door_sheet.php?'+params, loadingImage);
		}else if(reportTypePM == 'wallChart'){
			params = params+"&uniqueId="+Math.random();
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'wallChart.php?'+params, loadingImage);
		}else if(reportTypePM == 'wallCharta3'){
			params = params+"&uniqueId="+Math.random();
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'wallChart_a3.php?'+params, loadingImage);
		}else if(reportTypePM == 'wallCharta3summary'){
			params = params+"&uniqueId="+Math.random();
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pdf/i_report_wall_chart_a3_summary.php?'+params, loadingImage);
		}else if(reportTypePM == 'wallCharta3client'){
			params = params+"&uniqueId="+Math.random();
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pdf/i_report_wall_chart_a3_client.php?'+params, loadingImage);
		}
	}catch(e){
		//alert(e.message);
	}
}
function validateAndSubmit(){
	try{
		var params = "";var url = '';
		var projNamePM = document.getElementById("projNamePM").value;
		var locationPM = document.getElementById("locationPM").value;
		var subLocationPM = document.getElementById("subLocationPM").value;
		var issuedToPM = document.getElementById("issuedToPM").value;
		var statusPM = document.getElementById("statusPM").value;
		var DRFPM = document.getElementById("DRFPM").value;
		var DRTPM = document.getElementById("DRTPM").value;
		var FBDFPM = document.getElementById("FBDFPM").value;
		var FBDTPM = document.getElementById("FBDTPM").value;
		var reportTypePM = document.getElementById("reportTypePM").value;
<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
		if(projNamePM == ''){ document.getElementById('projectPMError').style.display = 'block'; return false;}
		if(reportTypePM == ''){ document.getElementById('reportErrorPM').style.display = 'block'; return false;}
<? }?>

var dateChackRaised = checkDates(document.getElementById('DRFPM'), document.getElementById('DRTPM'), 'In Start Date Field');
var dateChackFixed = checkDates(document.getElementById('FBDFPM'), document.getElementById('FBDTPM'), 'In End Date Field');
if(dateChackRaised === false){	return false;	}
if(dateChackFixed === false){	return false;	}

		params = "SearchInsp="+1+"&projName=" + projNamePM + "&location=" + locationPM + "&subLocation=" + subLocationPM + "&issuedToPM=" + issuedToPM +"&status=" + statusPM + "&DRF=" + DRFPM + "&DRT=" + DRTPM + "&FBDF=" + FBDFPM + "&FBDT=" + FBDTPM;

		if(reportTypePM == 'inCompleteWork'){
			url = 'pdf/i_pdf_in_complete_works.php';
		}else if(reportTypePM == 'doorSheet'){
			url = 'pdf/i_pdf_door_sheet.php';
		}

		if (window.XMLHttpRequest){ xmlhttp=new XMLHttpRequest(); }else{ xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); }
		showProgress();

		url = url+'?'+params+'&name='+Math.random();

		xmlhttp.open("GET", url, true);
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				hideProgress();
				document.getElementById("show_defect").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.send(params);
	}catch(e){
		//alert(e.message);
	}
}
function submitFormQA(){
	try{
		document.getElementById("show_defect").innerHTML = '';
		var params = '';
		var startWith = 0;
		var projNameQA = document.getElementById('projNameQA').value;
		var reportTypeQA = document.getElementById('reportTypeQA').value;
		var locationQA = document.getElementById('locationQA').value;
		var subLocationQA1 = document.getElementById('subLocationQA1').value;
		var subLocationQA2 = document.getElementById('subLocationQA2').value;
		var subLocationQA3 = document.getElementById('subLocationQA3').value;

		if(projNameQA == ''){ document.getElementById('projectQAError').style.display = 'block'; return false; }
		if(reportTypeQA == ''){ document.getElementById('reportErrorQA').style.display = 'block'; return false;}
		if(reportTypeQA != 'statusReport' && reportTypeQA != 'wallchartReport'){
			if(locationQA == ''){ document.getElementById('locationQAError').style.display = 'block'; return false; }
		}

		params = "projNameQA="+projNameQA+"&locationQA="+locationQA+"&subLocationQA1="+subLocationQA1+"&subLocationQA2="+subLocationQA2+"&subLocationQA3="+subLocationQA3+"&reportTypeQA="+reportTypeQA+"&name="+Math.random();

//Set Data for remebarains start here
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		var url = 'ajaxFunctions.php?session_type=qar';

		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				console.log('Hello Word !');
			}
		}
		xmlhttp.send(params);
//Set Data for remebarains end here

		if(reportTypeQA == 'qaReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_qa.php?'+params, loadingImage);
		}else if(reportTypeQA == 'nonConformance'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_non_conformance.php?'+params, loadingImage);
		}else if(reportTypeQA == 'nonConformanceDetail'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pdf/i_report_non_conformance_detailed.php?'+params, loadingImage);
		}else if(reportTypeQA == 'statusReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_qa_status.php?'+params, loadingImage);
		}else if(reportTypeQA == 'wallchartReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_qa_wallchart.php?'+params, loadingImage);
		}
	}catch(e){
		//alert(e.message);
	}
}
function downloadPDFQA(){
	document.getElementById('spinner').innerHTML = '<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.</div>';
	try{
		var projNameQA = document.getElementById('projNameQA').value;
		var reportTypeQA = document.getElementById('reportTypeQA').value;
		var locationQA = document.getElementById('locationQA').value;
		var subLocationQA1 = document.getElementById('subLocationQA1').value;
		var subLocationQA2 = document.getElementById('subLocationQA2').value;
		var subLocationQA3 = document.getElementById('subLocationQA3').value;

		if(projNameQA == ''){ document.getElementById('projectQAError').style.display = 'block'; return false; }
		if(reportTypeQA == ''){ document.getElementById('reportErrorQA').style.display = 'block'; return false;}
		if(reportTypeQA != 'statusReport' && reportTypeQA != 'wallchartReport'){
			if(locationQA == ''){ document.getElementById('locationQAError').style.display = 'block'; return false; }
		}

		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}

		showProgress();
		params = "projNameQA="+projNameQA+"&locationQA="+locationQA+"&subLocationQA1="+subLocationQA1+"&subLocationQA2="+subLocationQA2+"&subLocationQA3="+subLocationQA3+"&name="+Math.random();
		var url = '';
		if(reportTypeQA == 'qaReport'){
			url = 'pdf/i_report_qa.php';
		}else if(reportTypeQA == 'nonConformance'){
			url = 'pdf/i_report_non_conformance.php';
		}else if(reportTypeQA == 'statusReport'){
			url = 'pdf/i_report_qa_status.php';
		}else if(reportTypeQA == 'wallchartReport'){
			url = 'pdf/i_qa_wallchart.php';
		}
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				hideProgress();
				document.getElementById("mainContainer").style.overflow="visible";
				document.getElementById("mainContainer").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.send(params);
	}catch(e){
		//alert(e.message);
	}
}
function downloadPDFCL(){
	document.getElementById('spinner').innerHTML = '<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.</div>';
	try{
		var params = '';
		var startWith = 0;
		var projNameCL = document.getElementById('projNameCL').value;
		var locationCL = document.getElementById('locationCL').value;
		var subLocationCL = document.getElementById('subLocationCL').value;
		var sub_subLocationCL = document.getElementById('sub_subLocationCL').value;

		if(projNameCL == ''){ document.getElementById('projectErrorCL').style.display = 'block'; return false; }

		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}

		showProgress();
		params = "projNameCL="+projNameCL+"&locationCL="+locationCL+"&subLocationCL="+subLocationCL+"&sub_subLocationCL="+sub_subLocationCL+"&name="+Math.random();
		var url = '';
		url = 'pdf/i_report_checklist.php';
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				hideProgress();
				document.getElementById("mainContainer").style.overflow="visible";
				document.getElementById("mainContainer").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.send(params);
	}catch(e){
		//alert(e.message);
	}
}
function submitFormCL(){
	try{
		document.getElementById("show_defect").innerHTML = '';
		var params = '';
		var startWith = 0;
		var projNameCL = document.getElementById('projNameCL').value;
		var locationCL = document.getElementById('locationCL').value;
		var subLocationCL = document.getElementById('subLocationCL').value;
		var sub_subLocationCL = document.getElementById('sub_subLocationCL').value;

		if(projNameCL == ''){ document.getElementById('projectErrorCL').style.display = 'block'; return false; }

		params = "projNameCL="+projNameCL+"&locationCL="+locationCL+"&subLocationCL="+subLocationCL+"&sub_subLocationCL="+sub_subLocationCL+"&name="+Math.random();

//Set Data for remebarains start here
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		var url = 'ajaxFunctions.php?session_type=clr';

		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				console.log('Hello Word !');
			}
		}
		xmlhttp.send(params);
//Set Data for remebarains end here


		modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_checklist.php?'+params, loadingImage);
	}catch(e){
		//alert(e.message);
	}
}
function submitFormComp(){
	try{
		document.getElementById("show_defect").innerHTML = '';
		var params = "";var url = '';
		var startWith = 0;
		var projNameComp = document.getElementById("projNameComp").value;
		var user = document.getElementById("user").value;
		var projStatusComp = document.getElementById("projStatusComp").value;

		if(projNameComp == ''){ document.getElementById('projectErrorComp').style.display = 'block'; return false; }

		params = "SearchInsp="+1+"&projName=" + projNameComp + "&user=" + user + "&projStatusComp=" + projStatusComp + "&startWith=" + startWith + "&name="+Math.random();

		modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pdf/company_project_summary.php?'+params, loadingImage);

	}catch(e){
		//alert(e.message);
	}
}
$('#locationQA').live("change", function(){
	$('select#subLocationQA2').html('<option value="">Select</option>');
	$('select#subLocationQA3').html('<option value="">Select</option>');
});
$('#subLocationQA1').live("change", function(){
	$('select#subLocationQA3').html('<option value="">Select</option>');
});
</script>
	<div id="containerTop" style="width:100%; margin:auto;">
		<div class="content_hd1" style="width:90px;float:left;"></div>
	<?php if($_SESSION['ww_is_company'] == 1){?>
		<div id="button_companySummary" class="button_companySummary" style="float:left;" onClick="changeScreen(this.id)"></div>
	<?php }?>

	<?php if($_SESSION['web_report_quality_control'] == 1){?>
		<!-- <div id="button_qualityControl" class="button_qualityControlActive" style="float:left;" onClick="changeScreen(this.id)"></div> -->
		<div id="button_qualityControl" class="button_qualityControlActive inspections" style="float:left;" onClick="changeScreen(this.id)">
		   <span>Inspection<br>Reports</span>
		</div>
	<?php }?>
	<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
		<div id="buttoon_progressMonitoring" class="buttoon_progressMonitoring" style="float:left;" onClick="changeScreen(this.id)"></div>
	<?php }?>
	<?php if($_SESSION['web_report_quality_assuarance'] == 1){?>
		<!-- <div id="buttoon_qatask" class="buttoon_qatask" style="float:left;" onClick="changeScreen(this.id)"></div> -->
		<div id="buttoon_qatask" class="buttoon_qatask inspections green_small" style="float:left;display: none;" onClick="changeScreen(this.id)">
		   <span>Quality Assurance<br>Report</span>
		</div>
	<?php }?>
	<?php if($_SESSION['web_report_checklist'] == 1){?>
		<!-- <div id="buttoon_checklist" class="buttoon_checklist" style="float:left;" onClick="changeScreen(this.id)"></div> -->
		<div id="buttoon_checklist" class="buttoon_checklist inspections green_small" style="float:left;" onClick="changeScreen(this.id)">
		   <span>Checklist<br>Reports</span>
		</div>
	<?php }?>
		<!-- <div id="buttoon_qrcode" class="buttoon_qrcode" style="float:left;" onClick="changeScreen(this.id)"></div>
		</div> -->
		<div id="buttoon_qrcode" class="buttoon_qrcode inspections green_small" style="float:left;" onClick="changeScreen(this.id)">
		   <span>QR Code<br>Reports</span>
		</div>

		<div id="buttoon_non_conformance" class="buttoon_non_conformance inspections green_small" style="float:left;" onClick="changeScreen(this.id)">
		   <!-- <span>Non<br>Conformance</span> -->
		   <span>Quality Assurance<br>Report</span>
		</div>
	<br clear="all" />

<div class="search_multiple" style="border:1px solid; margin-bottom:10px;text-align:center;margin-left:10px;margin-right:10px;">
<?php if($_SESSION['web_report_quality_control'] == 1){?>
		<table cellpadding="0" cellspacing="5" border="0" id="button_qualityControlScreen" style="display:block;width:900px;width:700px\9;">
		<form action="" name="form_qualityControl" id="form_qualityControl" target="_blank" method="post">
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="">Project Name <span class="reqire">*</span></td>
				<td colspan="2" valign="top">
					<select name="projName" id="projName"  class="select_box" onChange="resetIds();startAjax(this.value);" style="width:220px;background-image:url(images/selectSpl.png);background:#0987c8 url(images/selectSpl.png) no-repeat\9;">
						<option value="">Select</option>
						<?php if(isset($_SESSION['ww_is_company'])){
							FillSelectBox("project_id, project_name","user_projects","is_deleted=0 and project_id !=0","project_name", 'qc_section');
						}else{
							FillSelectBox("project_id, project_name","user_projects","user_id = '".$owner_id."' and is_deleted=0","project_name", 'qc_section');
						}?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;"  id="projectError">Please select project name</div>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="">Report Type <span class="reqire">*</span></td>
				<td colspan="2" valign="top">
					<select name="reportType" id="reportType"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);" onChange="resetIds();">
						<option value="">Select</option>
<?php if($_SESSION['web_report_detail_report'] == 1){?>
						<option value="pdfDetail" <?php if(isset($_SESSION['ir']['report_type'])){ if($_SESSION['ir']['report_type'] == 'pdfDetail'){ echo 'selected="selected"'; }}?>>Internal Report</option>
<?php }?>
<?php //if($_SESSION['web_report_detail_report_hd'] == 1){?>
						<option value="pdfDetailHD" <?php if(isset($_SESSION['ir']['report_type'])){ if($_SESSION['ir']['report_type'] == 'pdfDetailHD'){ echo 'selected="selected"'; }}?>>Large Image Report</option>
<?php //}?>
<?php if($_SESSION['web_report_summay_with_images'] == 1){
		if($_SESSION['userRole'] != 'Sub Contractor'){?>
	<option value="pdfSummayWithImages" <?php if(isset($_SESSION['ir']['report_type'])){ if($_SESSION['ir']['report_type'] == 'pdfSummayWithImages'){ echo 'selected="selected"'; }}?>>Summary Report with Notes</option>
<?php }?>
	<option value="pdfSummayWithoutNotes" <?php if(isset($_SESSION['ir']['report_type'])){ if($_SESSION['ir']['report_type'] == 'pdfSummayWithoutNotes'){ echo 'selected="selected"'; }}?>>Summary Report without Notes</option>
<?php }?>

<?php if($_SESSION['userRole'] != 'Sub Contractor'){?>
	<option value="summaryCostImpact" <?php if(isset($_SESSION['ir']['report_type'])){ if($_SESSION['ir']['report_type'] == 'summaryCostImpact'){ echo 'selected="selected"'; }}?>>Summary Report with Cost Impact</option>
<?php }?>

<?php //if($_SESSION['web_report_sub_contractor_report'] == 1){?>
<?php if($_SESSION['userRole'] != 'Sub Contractor'){?>
			<option value="subContractorReport" <?php if(isset($_SESSION['ir']['report_type'])){ if($_SESSION['ir']['report_type'] == 'subContractorReport'){ echo 'selected="selected"'; }}?>>Summary Report for Sub Contractors</option>
<?php }?>
<?php //}?>

<?php if($_SESSION['ww_builder']['user_type'] == 'manager'){?>
<option value="pdfSummayWithGPS" <?php if(isset($_SESSION['ir']['report_type'])){ if($_SESSION['ir']['report_type'] == 'pdfSummayWithGPS'){ echo 'selected="selected"'; }}?>>Summary Report with GPS details</option>
<?php } ?>

<?php //if($_SESSION['web_report_executive_report'] == 1){?>
<?php if($_SESSION['userRole'] != 'Sub Contractor'){?>
			<option value="executiveReport" <?php if(isset($_SESSION['ir']['report_type'])){ if($_SESSION['ir']['report_type'] == 'executiveReport'){ echo 'selected="selected"'; }}?>>Executive Report</option>
<?php }?>
<?php //}?>
<?php //if($_SESSION['web_report_issuedto_executive_report'] == 1){?>
<?php if($_SESSION['userRole'] != 'Sub Contractor'){?>
		<option value="issuedtoExecutiveReport" <?php if(isset($_SESSION['ir']['report_type'])){ if($_SESSION['ir']['report_type'] == 'issuedtoExecutiveReport'){ echo 'selected="selected"'; }}?>>Executive (Issued to) Report</option>
<?php }?>
<?php //}?>
<?php if(true){?>
	<option value="csvReport" <?php if(isset($_SESSION['ir']['report_type'])){ if($_SESSION['ir']['report_type'] == 'csvReport'){ echo 'selected="selected"'; }}?>>CSV Report</option>
<?php }?>
				</select>
					<div class="error-edit-profile" style="width:220px;display:none;" id="reportError">The report field is required</div>
				</td>
			</tr>
            <tr>
				<td align="left" valign="middle" nowrap="nowrap" style="">Sort By</td>
				<td colspan="2">
					<select name="sortby" id="sortby" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="location_id" <?php if(isset($_SESSION['ir']['sortby'])){ if($_SESSION['ir']['sortby'] == 'location_id'){ echo 'selected="selected"'; }}?>>Location</option>
						<option value="issued_to_name" <?php if(isset($_SESSION['ir']['sortby'])){ if($_SESSION['ir']['sortby'] == 'issued_to_name'){ echo 'selected="selected"'; }}?>>Issued To</option>
					</select>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="">Search Keyword</td>
				<td colspan="2">
					<input type="text" name="searchKey" id="searchKey" class="input_small" style="width: 220px;
background-image: url(images/selectSpl.png);margin-left:0px;margin-left:28px;padding: 0 20px 0 20px;"
				<?php if(isset($_SESSION['ir']['searchKeyward'])){ echo 'value="'.$_SESSION['ir']['searchKeyward'].'"'; }?> /></td>
			</tr>
			<tr>
				<!--<td align="left" valign="middle" nowrap="nowrap" style="">Location </td>
				<td colspan="2" id="ShowLocation">
					<select name="location" id="location"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>-->

				<td align="left" valign="top" nowrap="nowrap" style="" rowspan="2">Location</td>
				<td colspan="2" valign="top" rowspan="2">
					<div id="ShowLocation">
					<select name="location" id="location" class="select_box" multiple="multiple" size="2" style="width:220px;height:76px;background-image:url(images/multiple_select_box.png);">
						<option value="">Select</option>
					</select>
					</div>

				</td>

				<td align="left" valign="top" nowrap="nowrap" style="" rowspan="2">Issued To</td>
				<td colspan="2" valign="top" rowspan="2">
					<div id="ShowIssuedTo">
					<select name="issuedTo" id="issuedTo" class="select_box" multiple="multiple" size="2" style="width:220px;height:76px;background-image:url(images/multiple_select_box.png);">
						<option value="">Select</option>
					</select>
					</div>
					<div class="error-edit-profile" style="width:220px;display:none;" id="issueToError">Please select Issued to</div>
				</td>
			</tr>
			<tr></tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="">Sub Location</td>
				<td colspan="2" id="ShowSubLocation">
					<select name="subLocation" id="subLocation"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>

				<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 1</td>
				<td colspan="2" id="Sub_ShowSubLocation">
					<select name="sub_subLocation" id="sub_subLocation"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td style="">Sub Location 2</td>
				<td colspan="2" id="showSubLocation3">
					<select name="subSubLocation3" id="subSubLocation3"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td style="">Sub Location 3</td>
				<td colspan="2" id="showSubLocation4">
					<select name="subSubLocation4" id="subSubLocation4"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>

				<td align="left" valign="middle" nowrap="nowrap" style="">Status</td>
				<td colspan="2" id="">
					<select name="status" id="status" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="Open" <?php if(isset($_SESSION['ir']['status'])){ if($_SESSION['ir']['status'] == 'Open'){ echo 'selected="selected"'; }}?>>Open</option>
						<option value="Pending" <?php if(isset($_SESSION['ir']['status'])){ if($_SESSION['ir']['status'] == 'Pending'){ echo 'selected="selected"'; }}?>>Pending</option>
						<option value="Fixed" <?php if(isset($_SESSION['ir']['status'])){ if($_SESSION['ir']['status'] == 'Fixed'){ echo 'selected="selected"'; }}?>>Fixed</option>
						<option value="Closed" <?php if(isset($_SESSION['ir']['status'])){ if($_SESSION['ir']['status'] == 'Closed'){ echo 'selected="selected"'; }}?>>Closed</option>
					</select>
				</td>

				<td align="left" valign="middle" nowrap="nowrap" style="">Inspected By</td>
				<td colspan="2" id="ShowInspecrBy">
					<select name="inspectedBy" id="inspectedBy" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>

				<td align="left" valign="middle" nowrap="nowrap" style="">Inspection Type</td>
				<td colspan="2">
					<select name="inspecrType" id="inspecrType" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="Issue" <?php if(isset($_SESSION['ir']['inspecrType'])){ if($_SESSION['ir']['inspecrType'] == 'Issue'){ echo 'selected="selected"'; }}?>>Issue</option>
						<option value="Defect" <?php if(isset($_SESSION['ir']['inspecrType'])){ if($_SESSION['ir']['inspecrType'] == 'Defect'){ echo 'selected="selected"'; }}?>>Defect</option>
						<option value="Warranty" <?php if(isset($_SESSION['ir']['inspecrType'])){ if($_SESSION['ir']['inspecrType'] == 'Warranty'){ echo 'selected="selected"'; }}?>>Warranty</option>
						<option value="Incomplete Works" <?php if(isset($_SESSION['ir']['inspecrType'])){ if($_SESSION['ir']['inspecrType'] == 'Incomplete Works'){ echo 'selected="selected"'; }}?>>Incomplete Works</option>
						<option value="Purchase Changes" <?php if(isset($_SESSION['ir']['inspecrType'])){ if($_SESSION['ir']['inspecrType'] == 'Purchase Changes'){ echo 'selected="selected"'; }}?>>Purchase Changes</option>
					</select>
				</td>

				<td align="left" valign="middle" nowrap="nowrap" style="">Cost Attribute</td>
				<td colspan="2">
					<select name="costAttribute" id="costAttribute" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="None" <?php if(isset($_SESSION['ir']['costAttribute'])){ if($_SESSION['ir']['costAttribute'] == 'None'){ echo 'selected="selected"'; }}?>>None</option>
						<option value="Backcharge" <?php if(isset($_SESSION['ir']['costAttribute'])){ if($_SESSION['ir']['costAttribute'] == 'Backcharge'){ echo 'selected="selected"'; }}?>>Backcharge</option>
						<option value="Variation" <?php if(isset($_SESSION['ir']['costAttribute'])){ if($_SESSION['ir']['costAttribute'] == 'Variation'){ echo 'selected="selected"'; }}?>>Variation</option>
					</select>
				</td>
			</tr>
			<tr>

				<td align="left" valign="middle" nowrap="nowrap" style="">Raised By</td>
				<td colspan="2" id="ShowRaisedBy">
					<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="">Date Raised</td>
				<td colspan="2" align="left" nowrap="nowrap" style="">
				From
					<input name="DRF" type="text" id="DRF" size="7" readonly="readonly" <?php if(isset($_SESSION['ir']['DRF'])){ echo 'value="'.$_SESSION['ir']['DRF'].'"'; }?> />
				To
					<input name="DRT" type="text" id="DRT" size="7" readonly="readonly" <?php if(isset($_SESSION['ir']['DRT'])){ echo 'value="'.$_SESSION['ir']['DRT'].'"'; }?> />
					<a href="javascript:void();" title="Clear date raised"><img src="images/redCross.png" onClick="clearDateRaised();" /></a>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="">Fix By Date</td>
				<td colspan="2" align="left" nowrap="nowrap" style="">
				From
					<input name="FBDF" type="text" id="FBDF" size="7" readonly="readonly" <?php if(isset($_SESSION['ir']['FBDF'])){ echo 'value="'.$_SESSION['ir']['FBDF'].'"'; }?> />
				To
					<input name="FBDT" type="text" id="FBDT" size="7" readonly="readonly" <?php if(isset($_SESSION['ir']['FBDT'])){ echo 'value="'.$_SESSION['ir']['FBDT'].'"'; }?> />
					<a href="javascript:void();" title="Clear fixed by date"><img src="images/redCross.png" onClick="clearFixedByDate();" /></a>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="left">&nbsp;</td>
				<td>
					<div id="reportURL" style="display:none;"></div>
					<input name="SearchInsp" type="button" onClick="submitForm();" class="green_small" id="button" value="Run Report" style="height:40px;float: right;"  />
				</td>
			</tr>
		</form>
		</table>
<?php }?>
<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
		<table cellpadding="0" cellspacing="5" border="0" id="buttoon_progressMonitoringScreen" style="display:none;width:900px;width:700px\9;">
		<form action="" name="form_progress_monitoring" id="form_progress_monitoring" target="_blank" method="post">
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="">Project Name <span class="reqire">*</span></td>
				<td colspan="2" valign="top">
					<select name="projName" id="projNamePM" class="select_box" onChange="resetIds();startAjax1(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<?php if(isset($_SESSION['ww_is_company'])){
							FillSelectBox("project_id, project_name","user_projects","is_deleted=0 and project_id !=0","project_name", 'pm_section');
						}else{
							FillSelectBox("project_id, project_name","user_projects","user_id = '".$owner_id."' and is_deleted=0","project_name", 'pm_section');
						}?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;" id="projectPMError">Please select project name</div>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="">Report Type <span class="reqire">*</span></td>
				<td colspan="2" valign="top" >
					<select name="reportType" id="reportTypePM"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);" onChange="resetIds();">
						<option value="">Select</option>
<?php if($_SESSION['web_report_onsite'] == 1){?>
						<option value="inCompleteWork" <?php if(isset($_SESSION['pmr']['reportTypePM'])){ if($_SESSION['pmr']['reportTypePM'] == 'inCompleteWork'){ echo 'selected="selected"'; }}?>>Onsite Status Report</option>
<?php }?>
<?php if($_SESSION['web_report_door_sheet'] == 1){?>
						<option value="doorSheet" <?php if(isset($_SESSION['pmr']['reportTypePM'])){ if($_SESSION['pmr']['reportTypePM'] == 'doorSheet'){ echo 'selected="selected"'; }}?>>Door Sheet</option>
<?php }?>
<?php if($_SESSION['web_report_wall_chart'] == 1){?>
						<option value="wallChart" <?php if(isset($_SESSION['pmr']['reportTypePM'])){ if($_SESSION['pmr']['reportTypePM'] == 'wallChart'){ echo 'selected="selected"'; }}?>>Wall Chart A0</option>
						<option value="wallCharta3" <?php if(isset($_SESSION['pmr']['reportTypePM'])){ if($_SESSION['pmr']['reportTypePM'] == 'wallCharta3'){ echo 'selected="selected"'; }}?>>Wall Chart A3 (Detailed)</option>
						<option value="wallCharta3summary" <?php if(isset($_SESSION['pmr']['reportTypePM'])){ if($_SESSION['pmr']['reportTypePM'] == 'wallCharta3summary'){ echo 'selected="selected"'; }}?>>Wall Chart A3 (Summary)</option>
						<option value="wallCharta3client" <?php if(isset($_SESSION['pmr']['reportTypePM'])){ if($_SESSION['pmr']['reportTypePM'] == 'wallCharta3client'){ echo 'selected="selected"'; }}?>>Wall Chart A3 (Client)</option>
<?php }?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;" id="reportErrorPM">The report field is required</div>
				</td>
			</tr>
			<tr id="row1">
				<td align="left" valign="top" nowrap="nowrap" style="">Location</td>
				<td colspan="2" id="ShowLocation1">
					<select name="location" id="locationPM"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="">Sub Location </td>
				<td colspan="2" id="ShowSubLocation1">
					<select name="subLocation" id="subLocationPM"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr id="row1">
				<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 1</td>
				<td colspan="2" id="ShowLocation_sub_id">
					<select name="subLocation_sub" id="subLocation_subPM"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="">Issue To</td>
				<td colspan="2" id="ShowIssuedTo1">
					<select name="issuedToPM" id="issuedToPM" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr id="row2">
				<td align="left" valign="top" nowrap="nowrap" style="">Current Status</td>
				<td colspan="2">
					<select name="status" id="statusPM" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="In progress" <?php if(isset($_SESSION['pmr']['status'])){ if($_SESSION['pmr']['status'] == 'In progress'){ echo 'selected="selected"'; }}?>>In progress</option>
						<option value="Behind" <?php if(isset($_SESSION['pmr']['status'])){ if($_SESSION['pmr']['status'] == 'Behind'){ echo 'selected="selected"'; }}?>>Behind</option>
						<option value="Complete" <?php if(isset($_SESSION['pmr']['status'])){ if($_SESSION['pmr']['status'] == 'Complete'){ echo 'selected="selected"'; }}?>>Complete	</option>
						<option value="Signed off" <?php if(isset($_SESSION['pmr']['status'])){ if($_SESSION['pmr']['status'] == 'Signed off'){ echo 'selected="selected"'; }}?>>Signed off</option>
					</select>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr id="row3">
				<td align="left" valign="middle" nowrap="nowrap" style="">Start Date</td>
				<td colspan="2" align="left" nowrap="nowrap" style="">
				From
					<input name="DRF" type="text" id="DRFPM" size="7" readonly="readonly"  <?php if(isset($_SESSION['pmr']['DRF'])){ echo 'value="'.$_SESSION['pmr']['DRF'].'"'; }?>/>
				To
					<input name="DRT" type="text" id="DRTPM" size="7" readonly="readonly"  <?php if(isset($_SESSION['pmr']['DRT'])){ echo 'value="'.$_SESSION['pmr']['DRT'].'"'; }?>/>
					<a href="javascript:void();" title="Clear start date"><img src="images/redCross.png" onClick="clearStartDate();" /></a>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="">End Date</td>
				<td colspan="2" align="left" nowrap="nowrap" style="">
				From
					<input name="FBDF" type="text" id="FBDFPM" size="7" readonly="readonly"  <?php if(isset($_SESSION['pmr']['FBDF'])){ echo 'value="'.$_SESSION['pmr']['FBDF'].'"'; }?>/>
				To
					<input name="FBDT" type="text" id="FBDTPM" size="7" readonly="readonly"  <?php if(isset($_SESSION['pmr']['FBDT'])){ echo 'value="'.$_SESSION['pmr']['FBDT'].'"'; }?>/>
					<a href="javascript:void();" title="Clear end date"><img src="images/redCross.png" onClick="clearEndDate();" /></a>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="left">&nbsp;</td>
				<td>
					<!-- <input name="SearchInsp" type="button" onClick="submitFormPM();" class="submit_btn" id="button" value="" style="background-image:url(images/btn_run_report.png); width:148px; height:40px;"  /> -->
					<input name="SearchInsp" type="button" onClick="submitFormPM();" class="green_small" id="button" value="Run Report" style="height:40px;float: right;"  />
				</td>
			</tr>
		</form>
		</table>
<?php }?>


<?php //if($_SESSION['web_report_progress_monitoring'] == 1){?>
		<table cellpadding="0" cellspacing="5" border="0" id="buttoon_non_conformanceScreen" style="display:none;width:900px;width:700px\9;">
		<form action="" name="form_non_conformance" id="form_non_conformance" target="_blank" method="post">
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="">Project Name <span class="reqire">*</span></td>
				<td colspan="2" valign="top">
					<select name="projNamenoconf" id="projNamenoconf"  class="select_box" onChange="resetIds();startAjaxnoconfCL(this.value);" style="width:220px;background-image:url(images/selectSpl.png);background:#0987c8 url(images/selectSpl.png) no-repeat\9;">
						<option value="">Select</option>
						<?php if(isset($_SESSION['ww_is_company'])){
							FillSelectBox("project_id, project_name","user_projects","is_deleted=0 and project_id !=0","project_name", 'cl_section');
						}else{
							FillSelectBox("project_id, project_name","user_projects","user_id = '".$owner_id."' and is_deleted=0","project_name", 'cl_section');
							if(isset($_SESSION['companyId']) && !empty($_SESSION['companyId'])){
								FillSelectBox("project_id, project_name","user_projects","company_id IN(".$_SESSION['companyId'].") and is_deleted=0","project_name", 'cl_section');
							}
						}?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;" id="projectErrornoconf">Please select project name</div>
				</td>


				<td align="left" valign="top" nowrap="nowrap" style="">Report Type <span class="reqire">*</span></td>
				<td colspan="2" valign="top">
					<select name="reportTypeQANew" id="reportTypeQANew"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);" onChange="resetIds();hideLoc(this.value);">
						<option value="">Select</option>
<!--
						<option value="statusReport" <?php /*if(isset($_SESSION['qar']['reportTypeQANew'])){ if($_SESSION['qar']['reportTypeQANew'] == 'statusReport'){ echo 'selected="selected"'; }} */?>>Status Report</option>
-->
						<option value="nonConformance" <?php if(isset($_SESSION['qar']['reportTypeQANew'])){ if($_SESSION['qar']['reportTypeQANew'] == 'nonConformance'){ echo 'selected="selected"'; }}?>>Non Conformance Report</option>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;" id="reportErrorQANew">The report field is required</div>
				</td>
			</tr>
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="">Checklist <span class="reqire"></span></td>
				<td colspan="2" id="checklists">
					<select name="checklist" id="checklist"  class="select_box" onChange="addChecklistInSession(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<?php echo $optChecklist; ?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;" id="checklistError">The Checklist field is required</div>
				</td>

				<td align="left" valign="middle" nowrap="nowrap" style="">Location </td>
				<td colspan="2" id="ShowLocationnoconfCL">
					<select name="locationnoconfCL" id="locationnoconfCL"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
            <tr>
            	<td align="left" valign="middle" nowrap="nowrap" style="">Sub Location</td>
				<td colspan="2" id="ShowSubLocationCLnoconf">
					<select name="subLocationCLnoconf" id="subLocationCLnoconf"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 1</td>
				<td colspan="2" id="Sub_ShowSubLocationCLnoconf">
					<select name="sub_subLocationCLnoconf" id="sub_subLocationCLnoconf"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
            <tr>
            	<td align="left" valign="middle" nowrap="nowrap" style="">Sub Location 2</td>
				<td colspan="2" id="ShowSubLocation2CLnoconf">
					<select name="SubLocation2noconf" id="SubLocation2noconf"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>

				<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 3</td>
				<td colspan="2" id="ShowSubLocation3CLnoconf">
					<select name="SubLocation3noconf" id="SubLocation3noconf"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>

			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="">Status</td>
				<td colspan="2" id="">
					<select name="statusnoconf" id="statusnoconf" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="Open" <?php if(isset($_SESSION['ir']['status'])){ if($_SESSION['ir']['status'] == 'Open'){ echo 'selected="selected"'; }}?>>Open</option>
						<option value="Fixed" <?php if(isset($_SESSION['ir']['status'])){ if($_SESSION['ir']['status'] == 'Fixed'){ echo 'selected="selected"'; }}?>>Fixed</option>
						<option value="Close" <?php if(isset($_SESSION['ir']['status'])){ if($_SESSION['ir']['status'] == 'Closed'){ echo 'selected="selected"'; }}?>>Closed</option>
					</select>
				</td>

				<td align="left" valign="middle" nowrap="nowrap" style="">Raised By</td>
				<td colspan="2" id="ShowRaisedBynoconf">
					<select name="raisedBynoconf" id="raisedBynoconf" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="">Issued To</td>
				<td colspan="2">
					<select id="showIssuedToNoconf" name="issuedTo" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td>&nbsp;</td>
				<td colspan="2">&nbsp;</td>
			</tr>
			<!-- <tr>
				<td align="left" valign="middle" nowrap="nowrap" style="">Date Raised</td>
				<td colspan="2" align="left" nowrap="nowrap" style="">
				From
					<input name="DRF" type="text" id="DRF" size="7" readonly="readonly" <?php if(isset($_SESSION['ir']['DRF'])){ echo 'value="'.$_SESSION['ir']['DRF'].'"'; }?> />
				To
					<input name="DRT" type="text" id="DRT" size="7" readonly="readonly" <?php if(isset($_SESSION['ir']['DRT'])){ echo 'value="'.$_SESSION['ir']['DRT'].'"'; }?> />
					<a href="javascript:void();" title="Clear date raised"><img src="images/redCross.png" onClick="clearDateRaised();" /></a>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="">Fix By Date</td>
				<td colspan="2" align="left" nowrap="nowrap" style="">
				From
					<input name="FBDF" type="text" id="FBDF" size="7" readonly="readonly" <?php if(isset($_SESSION['ir']['FBDF'])){ echo 'value="'.$_SESSION['ir']['FBDF'].'"'; }?> />
				To
					<input name="FBDT" type="text" id="FBDT" size="7" readonly="readonly" <?php if(isset($_SESSION['ir']['FBDT'])){ echo 'value="'.$_SESSION['ir']['FBDT'].'"'; }?> />
					<a href="javascript:void();" title="Clear fixed by date"><img src="images/redCross.png" onClick="clearFixedByDate();" /></a>
				</td>
			</tr> -->
			<tr>
				<td colspan="4" align="left">&nbsp;</td>
				<td>
					<div id="reportURL" style="display:none;"></div>
					<input name="SearchInsp" type="button" onClick="submitFormNoConf();" class="green_small" id="button" value="Run Report" style="height:40px;float: right;"  />
				</td>
			</tr>
		</form>
		</table>

<?php //}?>
<?php if($_SESSION['web_report_quality_assuarance'] == 1){?>
		<table cellpadding="0" cellspacing="5" border="0" id="buttoon_QATaskScreen" style="display:none;width:900px;width:700px\9;">
		<form action="" name="form_progress_monitoring" id="form_progress_monitoring" target="_blank" method="post">
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="">Project Name <span class="reqire">*</span></td>
				<td colspan="2" valign="top">
					<select name="projName" id="projNameQA" class="select_box" onChange="resetIds();startAjaxQA(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<?php if(isset($_SESSION['ww_is_company'])){
							FillSelectBox("project_id, project_name","user_projects","is_deleted=0 and project_id !=0","project_name", 'qa_section');
						}else{
							FillSelectBox("project_id, project_name","user_projects","user_id = '".$owner_id."' and is_deleted=0","project_name", 'qa_section');
						}?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;" id="projectQAError">Please select project name</div>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="">Report Type <span class="reqire">*</span></td>
				<td colspan="2" valign="top">
					<select name="reportTypeQA" id="reportTypeQA"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);" onChange="resetIds();hideLoc(this.value);">
						<option value="">Select</option>
<?php if($_SESSION['web_report_qa_task'] == 1){?>
						<option value="qaReport" <?php if(isset($_SESSION['qar']['reportTypeQA'])){ if($_SESSION['qar']['reportTypeQA'] == 'qaReport'){ echo 'selected="selected"'; }}?>>QA Report</option>
<?php }?>
<?php if($_SESSION['web_report_non_conformance'] == 1){?>
						<option value="nonConformance" <?php if(isset($_SESSION['qar']['reportTypeQA'])){ if($_SESSION['qar']['reportTypeQA'] == 'nonConformance'){ echo 'selected="selected"'; }}?>>Non Conformance Report</option>
						<option value="nonConformanceDetail" <?php if(isset($_SESSION['qar']['reportTypeQA'])){ if($_SESSION['qar']['reportTypeQA'] == 'nonConformanceDetail'){ echo 'selected="selected"'; }}?>>Non Conformance Report (Detail)</option>
<?php }?>
<?php #if($_SESSION['web_report_qa_status'] == 1){?>
						<option value="statusReport" <?php if(isset($_SESSION['qar']['reportTypeQA'])){ if($_SESSION['qar']['reportTypeQA'] == 'statusReport'){ echo 'selected="selected"'; }}?>>Status Report</option>
<?php #}?>
<?php if($_SESSION['web_report_qa_wallchart'] == 1){?>
						<option value="wallchartReport" <?php if(isset($_SESSION['qar']['reportTypeQA'])){ if($_SESSION['qar']['reportTypeQA'] == 'wallchartReport'){ echo 'selected="selected"'; }}?>>Wallchart Report</option>
<?php }?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;" id="reportErrorQA">The report field is required</div>
				</td>
			</tr>
			<tr id="rowQA1">
				<td align="left" valign="top" nowrap="nowrap" style="">Location <span class="reqire">*</span></td>
				<td colspan="2">
					<div id="ShowLocationQA">
					<select name="locationQA" id="locationQA"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
					</div>
					<div class="error-edit-profile" style="width:220px;display:none;" id="locationQAError">The Location field is required</div>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 1</td>
				<td colspan="2">
					<div  id="ShowSubLocation1QA">
					<select name="subLocationQA1" id="subLocationQA1"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
					</div>
					<div class="error-edit-profile" style="width:220px;display:none;" id="subLocationQA1Error">The Sub Location 1 field is required</div>
				</td>
			</tr>
			<tr id="rowQA2">
				<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 2</td>
				<td colspan="2">
					<div  id="ShowSubLocation2QA">
					<select name="subLocationQA2" id="subLocationQA2"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
					</div>
					<div class="error-edit-profile" style="width:220px;display:none;" id="subLocationQA2Error">The Sub Location 2 field is required</div>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 3</td>
				<td colspan="2" id="ShowSubLocation3QA">
					<select name="subLocationQA3" id="subLocationQA3" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="left">&nbsp;</td>
				<td>
					<!-- <input name="SearchInsp" type="button" onClick="submitFormQA();" class="submit_btn" id="button" value="" style="background-image:url(images/btn_run_report.png); width:148px; height:40px;"  /> -->
					<input name="SearchInsp" type="button" onClick="submitFormQA();" class="green_small" id="button" value="Run Report" style="height:40px;float: right;"  />
				</td>
			</tr>
		</form>
		</table>
<?php }?>
<?php if($_SESSION['web_report_checklist'] == 1){?>
		<table cellpadding="0" cellspacing="5" border="0" id="buttoon_CLScreen" style="display:none;width:900px;width:700px\9;">
		<form action="" name="form_progress_monitoring" id="form_progress_monitoring" target="_blank" method="post">
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="">Project Name <span class="reqire">*</span></td>
				<td colspan="2" valign="top">
					<select name="projName" id="projNameCL"  class="select_box" onChange="resetIds();startAjaxCL(this.value);" style="width:220px;background-image:url(images/selectSpl.png);background:#0987c8 url(images/selectSpl.png) no-repeat\9;">
						<option value="">Select</option>
						<?php if(isset($_SESSION['ww_is_company'])){
							FillSelectBox("project_id, project_name","user_projects","is_deleted=0 and project_id !=0","project_name", 'cl_section');
						}else{
							FillSelectBox("project_id, project_name","user_projects","user_id = '".$owner_id."' and is_deleted=0","project_name", 'cl_section');
						}?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;"  id="projectErrorCL">Please select project name</div>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="">Location </td>
				<td colspan="2" id="ShowLocationCL">
					<select name="locationCL" id="locationCL"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
            <tr>
				<td align="left" valign="middle" nowrap="nowrap" style="">Sub Location</td>
				<td colspan="2" id="ShowSubLocationCL">
					<select name="subLocationCL" id="subLocationCL"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 1</td>
				<td colspan="2" id="Sub_ShowSubLocationCL">
					<select name="sub_subLocationCL" id="sub_subLocationCL"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="left">&nbsp;</td>
				<td>
					<!-- <input name="SearchInsp" type="button" onClick="submitFormCL();" class="submit_btn" id="button" value="" style="background-image:url(images/btn_run_report.png); width:148px; height:40px;"  /> -->
					<input name="SearchInsp" type="button" onClick="submitFormCL();" class="green_small" id="button" value="Run Report" style="height:40px;float: right;"  />
				</td>
			</tr>
		</form>
		</table>
<?php }?>

	<!-- QR Code Screen -->
	<table cellpadding="0" cellspacing="5" border="0" id="buttoon_qrcodeScreen" style="display:none;width:900px;width:700px;">
		<form action="" name="form_qr_monitoring" id="form_progress_monitoring" target="_blank" method="post">
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="">Project Name <span class="reqire">*</span></td>
				<td colspan="2" valign="top">
					<select name="projNameQRcode" id="projNameQRcode"  class="select_box" onChange="resetIds();startAjaxQrCodeCL(this.value);" style="width:220px;background-image:url(images/selectSpl.png);background:#0987c8 url(images/selectSpl.png) no-repeat\9;">
						<option value="">Select</option>
						<?php if(isset($_SESSION['ww_is_company'])){
							FillSelectBox("project_id, project_name","user_projects","is_deleted=0 and project_id !=0","project_name", 'cl_section');
						}else{
							FillSelectBox("project_id, project_name","user_projects","user_id = '".$owner_id."' and is_deleted=0","project_name", 'cl_section');
							if(isset($_SESSION['companyId']) && !empty($_SESSION['companyId'])){
								FillSelectBox("project_id, project_name","user_projects","company_id IN(".$_SESSION['companyId'].") and is_deleted=0","project_name", 'cl_section');
							}
						}?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;"  id="projectErrorQrCode">Please select project name</div>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="">Location </td>
				<td colspan="2" id="ShowLocationQrCodeCL">
					<select name="locationQrCodeCL" id="locationQrCodeCL"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
            <tr>
				<td align="left" valign="middle" nowrap="nowrap" style="">Sub Location</td>
				<td colspan="2" id="ShowSubLocationCLQrCode">
					<select name="subLocationCLQrCode" id="subLocationCLQrCode"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 1</td>
				<td colspan="2" id="Sub_ShowSubLocationCLQrCode">
					<select name="sub_subLocationCLQrCode" id="sub_subLocationCLQrCode"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="">Sub Location 2</td>
				<td colspan="2" id="ShowSubLocation2CLQrCode">
					<select name="SubLocation2QrCode" id="SubLocation2QrCode"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="">Sub Location 3</td>
				<td colspan="2" id="ShowSubLocation3CLQrCode">
					<select name="SubLocation3QrCode" id="SubLocation3QrCode"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="4" align="left">&nbsp;</td>
				<td>
					<!-- <input name="SearchQrCode" type="button" onClick="submitFormCLQrCode();" class="submit_btn" id="button" value="" style="background-image:url(images/btn_run_report.png); width:148px; height:40px;"  /> -->
					<input name="SearchInsp" type="button" onClick="submitFormCLQrCode();" class="green_small" id="button" value="Run Report" style="height:40px;float: right;"  />
				</td>
			</tr>
		</form>
	</table>
</div>
<div>
		<div class="demo_jui" id="show_defect" <?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){ echo 'style="padding-left:10px;"'; }?>></div>
		<div class="spacer"></div>
  </div>
</div>
<div id="userRole"></div>
<script type="text/javascript">
function closePopUp(){	closePopup(300);	}
function clearDateRaised(){
	document.getElementById('DRF').value = '';
	document.getElementById('DRT').value = '';
}
function clearFixedByDate(){
	document.getElementById('FBDF').value = '';
	document.getElementById('FBDT').value = '';
}
<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
function clearStartDate(){
	document.getElementById('DRFPM').value = '';
	document.getElementById('DRTPM').value = '';
}
function clearEndDate(){
	document.getElementById('FBDFPM').value = '';
	document.getElementById('FBDTPM').value = '';
}
function hideLoc(ob){
	if(ob == 'wallchartReport'){
		$('#rowQA1').hide();
		$('#rowQA2').hide();
	}else{
		$('#rowQA1').show();
		$('#rowQA2').show();
	}
}
<?php }?>
</script>
<script language="javascript" type="text/javascript">
//Checklist Section Start Here
<? if(isset($_SESSION['clr'])){
$projectNameCL = '';$projectNameCL = $_SESSION['clr']['projNameCL'];
$locationNameCL = '';$locationNameCL = $_SESSION['clr']['locationCL'];
$sublocationNameCL = '';$sublocationNameCL = $_SESSION['clr']['subLocationCL']; ?>
var projectIdCL = '';
projectIdCL = '<?=$projectNameCL;?>';
AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=locationCL && proID="+projectIdCL,"ShowLocationCL");
var locationIdCL = '';
<?php if($locationNameCL != ''){?>
	locationIdCL = <?=$locationNameCL;?>;
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationCL1 && proID="+locationIdCL,"ShowSubLocationCL");
<?php }?>
var subLocationIdCL = '';
<?php if($sublocationNameCL != ''){?>
	subLocationIdCL = <?=$sublocationNameCL;?>;
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationCL2 && proID="+subLocationIdCL,"Sub_ShowSubLocationCL");
<?php }
}?>
//Quality Assurance Section Start Here
<? if(isset($_SESSION['qar'])){
$projectNameQA = '';$projectNameQA = $_SESSION['qar']['projNameQA'];
$locationNameQA = '';$locationNameQA = $_SESSION['qar']['locationQA'];
$sublocationNameQA = '';$sublocationNameQA = $_SESSION['qar']['subLocationQA1'];
$subSublocationNameQA = '';$subSublocationNameQA = $_SESSION['qar']['subLocationQA2']; ?>
var projectIdQA = '';
projectIdQA = '<?=$projectNameQA;?>';
AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=locationQA && proID="+projectIdQA,"ShowLocationQA");
var locationIdQA = '';
<?php if($locationNameQA != ''){?>
	locationIdQA = <?=$locationNameQA;?>;
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationQA1 && proID="+locationIdQA,"ShowSubLocation1QA");
<?php }?>
var subLocationIdQA = '';
<?php if($sublocationNameQA != ''){?>
	subLocationIdQA = <?=$sublocationNameQA;?>;
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationQA2 && proID="+subLocationIdQA,"ShowSubLocation2QA");
<?php }?>
var subSubLocationIdQA = '';
<?php if($subSublocationNameQA != ''){?>
	subSubLocationIdQA = <?=$subSublocationNameQA;?>;
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationQA3 && proID="+subSubLocationIdQA,"ShowSubLocation3QA");
<?php }
}?>
//Progress Monitoring Section Start Here
<? if(isset($_SESSION['pmr'])){
$projectNamePM = '';$projectNamePM = $_SESSION['pmr']['projName'];
$locationNamePM = '';$locationNamePM = $_SESSION['pmr']['location'];
$sublocationNamePM = '';$sublocationNamePM = $_SESSION['pmr']['subLocation'];
$subSublocationNamePM = '';$subSublocationNamePM = $_SESSION['pmr']['subLocation_sub']; ?>
var projectIdPM = '';
projectIdPM = '<?=$projectNamePM;?>';
AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=locationPM && proID="+projectIdPM,"ShowLocation1");
AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=issuedToPM && proID="+projectIdPM,"ShowIssuedTo1");
var locationIdPM = '';
<?php if($locationNamePM != ''){?>
	locationIdPM = <?=$locationNamePM;?>;
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationPM && proID="+locationIdPM,"ShowSubLocation1");
<?php }?>
var subLocationIdPM = '';
<?php if($sublocationNamePM != ''){?>
	subLocationIdPM = <?=$sublocationNamePM;?>;
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocation_subPM && proID="+subLocationIdPM,"ShowLocation_sub_id");
<?php }?>
var subSubLocationIdPM = '';
<?php if($subSublocationNamePM != ''){?>
	subSubLocationIdPM = <?=$subSublocationNamePM;?>;
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=sub_subLocation && proID="+subSubLocationIdPM,"Sub_ShowSubLocation");
<?php }
}?>
//Inspection Report Section Start Here
<? if(isset($_SESSION['ir'])){
$projectName = '';$projectName = $_SESSION['ir']['projName'];
$locationName = '';$locationName = $_SESSION['ir']['location'];
$sublocationName = '';$sublocationName = $_SESSION['ir']['subLocation']; ?>
var projectId = '';
projectId = '<?=$projectName;?>';
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=location && proID="+projectId,"ShowLocation");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=inspecrBy && proID="+projectId,"ShowInspecrBy");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=issuedTo && proID="+projectId,"ShowIssuedTo");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=userRole&& proID="+projectId,"userRole");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=raisedBy&& proID="+projectId,"ShowRaisedBy");
var locationId = '';
<?php if($locationName != ''){?>
	locationId = <?=$locationName;?>;
	// AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocation && proID="+locationId,"ShowSubLocation");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocation && proID="+locationId+ " && pageName=reportTab","ShowSubLocation");
<?php }?>
var subLocationId = '';
<?php if($sublocationName != ''){?>
	subLocationId = <?=$sublocationName;?>;
	// AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=sub_subLocation && proID="+subLocationId,"Sub_ShowSubLocation");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=sub_subLocation && proID="+subLocationId+ " && pageName=reportTab","Sub_ShowSubLocation");
<?php }
}?>
</script>
</body>
</html>
