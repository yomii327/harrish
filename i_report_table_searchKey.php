<?php function FillSelectBox($field, $table, $where, $group){
		$q="select $field from $table where $where GROUP BY $group";
		$q=mysql_query($q);
		while($q1=mysql_fetch_array($q)){
			echo '<option value="'.$q1[0].'">'.$q1[1].'</option>';
		}
	}
	unset($_SESSION['qc']);
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
@import "css/jquery.datepick.css";
table td{padding:5px;}
table{ margin-left:10px;}
#DRF, #DRT, #FBDF, #FBDT, #DRFPM, #DRTPM, #FBDFPM, #FBDTPM{ background:#FFF; cursor:default; height:20px; -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; }
.error-edit-profile { width: 220px; }
div#mainContainer{ color:#000; width:800px; margin:auto; min-height:70px;; max-height:650px; overflow-y:scroll;}
.buttonDiv img{ cursor:pointer; }
.pagination{ float: right; width:290px; height:28px; margin:10px 15px 0 0; text-align:right;}
.page_active{ background-image:url(images/grey.png); background-repeat:no-repeat; width:27px; height:22px; color:#FFFFFF; display:block; float:left; text-align:center; margin:0 0px 0 5px; padding-top:2px; text-decoration:none;}
.page_deactive{ background-image:url(images/blue.png); background-repeat:no-repeat; width:27px; height:22px; color:#FFFFFF; display:block; float:left; text-align:center; margin:0 0 0 5px;padding-top:2px;text-decoration:none;}
.page_deactive:hover{ background-image:url(images/grey.png); background-repeat:no-repeat; width:27px; height:22px; color:#FFFFFF; display:block; float:left; text-align:center; margin:0 0 0 5px;padding-top:2px;text-decoration:none; cursor:pointer;}
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
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
</head>
<body id="dt_example">
<script language="javascript" type="text/javascript"><!-- Page JS Functions -->
function startAjax1(val){
	AjaxShow("POST","ajaxFunctions.php?type=locationPM && proID="+val,"ShowLocation1");
	AjaxShow("POST","ajaxFunctions.php?type=issuedToPM && proID="+val,"ShowIssuedTo1");
} 
function subLocate1(obj){
	AjaxShow("POST","ajaxFunctions.php?type=subLocationPM && proID="+obj,"ShowSubLocation1");
}
function subLocate_sub(obj){
	AjaxShow("POST","ajaxFunctions.php?type=subLocation_subPM && proID="+obj,"ShowLocation_sub_id");
}
function sub_subLocate(obj){
	AjaxShow("POST","ajaxFunctions.php?type=sub_subLocation && proID="+obj,"Sub_ShowSubLocation");
}

function startAjax(val){
	AjaxShow("POST","ajaxFunctions.php?type=location && proID="+val,"ShowLocation");
	AjaxShow("POST","ajaxFunctions.php?type=inspecrBy && proID="+val,"ShowInspecrBy");
	AjaxShow("POST","ajaxFunctions.php?type=issuedTo && proID="+val,"ShowIssuedTo");
AjaxShow("POST","ajaxFunctions.php?type=userRole&& proID="+val,"userRole");
AjaxShow("POST","ajaxFunctions.php?type=raisedBy&& proID="+val,"ShowRaisedBy");
} 
function subLocate(obj){
	AjaxShow("POST","ajaxFunctions.php?type=subLocation && proID="+obj,"ShowSubLocation");
}
function changeScreen(screenID){
	if(screenID == 'buttoon_progressMonitoring'){
		document.getElementById(screenID).setAttribute("class", "buttoon_progressMonitoringActive");
		document.getElementById('button_qualityControl').setAttribute("class", "button_qualityControl");
document.getElementById('show_defect').innerHTML = '';		
		document.getElementById('button_qualityControlScreen').style.display = 'none';
		document.getElementById('buttoon_progressMonitoringScreen').style.display = 'block';
	}
	if(screenID == 'button_qualityControl'){
		document.getElementById(screenID).setAttribute("class", "button_qualityControlActive");
		document.getElementById('buttoon_progressMonitoring').setAttribute("class", "buttoon_progressMonitoring");
document.getElementById('show_defect').innerHTML = '';	
		document.getElementById('buttoon_progressMonitoringScreen').style.display = 'none';
		document.getElementById('button_qualityControlScreen').style.display = 'block';
	}
}
function resetIds(){
	document.getElementById('projectError').style.display = 'none';
	document.getElementById('reportError').style.display = 'none';
<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
	document.getElementById('reportErrorPM').style.display = 'none';
	document.getElementById('projectPMError').style.display = 'none';
<? }?>
}
</script>
<div id="container" style="width:99%;margin-top:25px;min-height:510px;" >
<?php
if (isset($_SESSION["ww_is_builder"]) and $_SESSION["ww_is_builder"]== 1){
	$owner_id = $_SESSION['ww_builder_id'];
}else{
	$owner_id = $_SESSION['ww_is_company'];
}
$phd='';
$myProjects='';
$ihd='';
$myInspections='';
$pdfForm='';
// get all inspections logged by this inspector
#$qi="SELECT *,r.resp_full_name FROM ".DEFECTS." d 	LEFT JOIN ".PROJECTS." p ON p.id = d.project_id 	LEFT JOIN ".RESPONSIBLES." r ON r.project_id = d.project_id 	WHERE d.owner_id = '$owner_id'";
?>
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
		var location = document.getElementById('location').value;
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
	
		params = "projName="+projName+"&location="+location+"&subLocation="+subLocation+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&priority="+priority+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&raisedBy="+raisedBy+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&startWith="+startWith+"&report_type="+reportType+"&searchKeyward="+searchKeyward+"&sub_subLocation="+sub_subLocation+"&sortyby="+sortby+"&name="+Math.random();
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

function downloadPDF(){
	document.getElementById('spinner').innerHTML = '<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.</div>';
	try{
		var params = '';
		var projName = document.getElementById('projName').value;
		var reportType = document.getElementById('reportType').value;
		var sortby = document.getElementById('sortby').value;
		var location = document.getElementById('location').value;
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
	
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		showProgress();
		params = "projName="+projName+"&location="+location+"&subLocation="+subLocation+"&sub_subLocation="+sub_subLocation+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&priority="+priority+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&raisedBy="+raisedBy+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&report_type=" + reportType+"&sortby="+sortby+"&searchKeyward="+searchKeyward+"&printPDF=Y&name="+Math.random();
		if(reportType == 'pdfDetail'){
			var url = 'pdf/i_report_pdf_detail.php';	
		}else if(reportType == 'pdfDetailHD'){
			var url = 'pdf/i_report_pdf_detail_hd.php';	
		}else if(reportType == 'pdfSummayWithOutImages' || reportType == 'pdfSummayWithImages'){
			//var url = 'pdf/i_report_pdf_summary.php';
			callAjaxDownloadSR ('pdf/i_report_pdf_summary_notes.php', 1, params);
			return;
		}else if(reportType == 'pdfSummayWithoutNotes'){
			callAjaxDownloadSR ('pdf/i_report_pdf_summary.php', 1, params);
			return;
		}else if(reportType == 'summaryCostImpact'){
			callAjaxDownloadSR ('pdf/i_report_pdf_summary_cost_impact.php', 1, params);
			return;
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
				document.getElementById("mainContainer").innerHTML=xmlhttp.responseText;
			}
		}
		xmlhttp.send(params);
	}catch(e){
	//	alert(e.message); 
	}
}

function callAjaxDownloadSR(url, page_no, params){
	try{
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
						callAjaxDownloadSR (url, page_no,params);
					}else{
						hideProgress();
						document.getElementById("mainContainer").style.overflow="visible";
						document.getElementById("mainContainer").innerHTML=xmlhttp.responseText;
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
		var location = document.getElementById('location').value;
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
		
		params = "projName="+projName+"&location="+location+"&subLocation="+subLocation+"&sub_subLocation="+sub_subLocation+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&priority="+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&raisedBy="+raisedBy+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&startWith="+startWith+"&report_type="+reportType+"&sortby="+sortby+"&searchKeyward="+searchKeyward+"&name="+Math.random();
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
		
		params = "SearchInsp="+1+"&projName=" + projNamePM + "&location=" + locationPM + "&subLocation=" + subLocationPM + "&subLocation_sub=" + subLocation_sub + "&issuedToPM=" + issuedToPM +"&status=" + statusPM + "&DRF=" + DRFPM + "&DRT=" + DRTPM + "&FBDF=" + FBDFPM + "&FBDT=" + FBDTPM+"&startWith="+startWith+"&name="+Math.random();

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
		alert(e.message);
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

		if(reportTypePM == 'inCompleteWork'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_pdf_in_complete_works.php?'+params, loadingImage);
		}else if(reportTypePM == 'doorSheet'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_pdf_door_sheet.php?'+params, loadingImage);
		}else if(reportTypePM == 'wallChart'){
			params = params+"&uniqueId="+Math.random();
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'wallChart.php?'+params, loadingImage);
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
</script>
<style> .asdf{ visibility:visible } </style>
	<div id="containerTop" style="width:100%; margin:auto;">
		<div class="content_hd1" style="background-image:url(images/report_header.png); width:350px;float:left;"></div>
<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
	<?php if($_SESSION['web_report_quality_control'] == 1){?>	
			<div id="button_qualityControl" class="button_qualityControlActive" style="float:left;" onClick="changeScreen(this.id)"></div>	
	<?php }?>
		<div id="buttoon_progressMonitoring" class="buttoon_progressMonitoring" style="float:left;" onClick="changeScreen(this.id)"></div>
<?php }?>
	</div><br clear="all" />
	
<div class="search_multiple" style="border:1px solid; margin-bottom:10px;text-align:center;margin-left:10px;margin-right:10px;">
<?php if($_SESSION['web_report_quality_control'] == 1){?>	
		<table cellpadding="0" cellspacing="5" border="0" id="button_qualityControlScreen" style="display:block;width:900px;width:700px\9;">
		<form action="" name="form_qualityControl" id="form_qualityControl" target="_blank" method="post">
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Project Name <span class="reqire">*</span></td>
				<td colspan="2" valign="top">
					<select name="projName" id="projName"  class="select_box" onChange="resetIds();startAjax(this.value);" style="width:220px;background-image:url(images/selectSpl.png);background:#0987c8 url(images/selectSpl.png) no-repeat\9;">
						<option value="">Select</option>
						<?php if(isset($_SESSION['ww_is_company'])){
							FillSelectBox("project_id, project_name","user_projects","is_deleted=0","project_name");
						}else{
							FillSelectBox("project_id, project_name","user_projects","user_id = '".$owner_id."' and is_deleted=0","project_name");
						}?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;"  id="projectError">The project field is required</div>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Report Type <span class="reqire">*</span></td>
				<td colspan="2" valign="top">
					<select name="reportType" id="reportType"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);" onChange="resetIds();">
						<option value="">Select</option>
<?php if($_SESSION['web_report_detail_report'] == 1){?>
						<option value="pdfDetail">Internal Report</option>
<?php }?>
<?php //if($_SESSION['web_report_detail_report_hd'] == 1){?>
						<option value="pdfDetailHD">Large Image Report</option>
<?php //}?>
<?php #if($_SESSION['web_report_summay_with_out_images'] == 1){?>
						<!--<option value="pdfSummayWithOutImages">Summary Report No Images</option>-->
<?php #}?>
<?php if($_SESSION['web_report_summay_with_images'] == 1){?>
						<option value="pdfSummayWithImages">Summary Report with Notes</option>
						<option value="pdfSummayWithoutNotes">Summary Report without Notes</option>
<?php }?>
						<option value="summaryCostImpact">Summary Report with Cost Impact</option>
<?php if($_SESSION['web_report_csv'] == 1){?>
						<option value="csvReport">CSV Report</option>
<?php }?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;" id="reportError">The report field is required</div>
				</td>
			</tr>
            <tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Sort By</td>
				<td colspan="2">
					<select name="sortby" id="sortby" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="location_id">Location</option>
						<option value="inspection_date_raised">Date Raised</option>
					</select>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Search Keybord</td>
				<td colspan="2"><input type="text" name="searchKey" id="searchKey" class="input_small" style="width: 220px;
background-image: url(images/selectSpl.png);margin-left:0px;margin-left:28px;"  /></td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Location </td>
				<td colspan="2" id="ShowLocation">
					<select name="location" id="location"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;" rowspan="2">Issued To</td>
				<td colspan="2" id="ShowIssuedTo" valign="top" rowspan="2">
					<select name="issuedTo" id="issuedTo" class="select_box" multiple="multiple" size="2" style="width:220px;height:76px;background-image:url(images/multiple_select_box.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Sub Location</td>
				<td colspan="2" id="ShowSubLocation">
					<select name="subLocation" id="subLocation"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Sub Location 1</td>
				<td colspan="2" id="Sub_ShowSubLocation">
					<select name="sub_subLocation" id="sub_subLocation"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Status</td>
				<td colspan="2" id="">
					<select name="status" id="status" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="Open">Open</option>
						<option value="Pending">Pending</option>
						<option value="Fixed">Fixed</option>
						<option value="Closed">Closed</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Inspected By</td>
				<td colspan="2" id="ShowInspecrBy">
					<select name="inspectedBy" id="inspectedBy" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Inspection Type</td>
				<td colspan="2">
					<select name="inspecrType" id="inspecrType" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="Issue">Issue</option>
						<option value="Defect">Defect</option>
						<option value="Warranty">Warranty</option>
						<option value="Incomplete Works">Incomplete Works</option>
						<!--<option value="Progress Monitoring">Progress Monitoring</option>
						<option value="Other">Other</option>-->
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Cost Attribute</td>
				<td colspan="2">
					<select name="costAttribute" id="costAttribute" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<option value="None">None</option>
						<option value="Backcharge">Backcharge</option>
						<option value="Variation">Variation</option>
					</select>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Raised By</td>
				<td colspan="2" id="ShowRaisedBy">
					<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Date Raised</td>
				<td colspan="2" align="left" nowrap="nowrap" style="color:#FFFFFF;">
				From 
					<input name="DRF" type="text" id="DRF" size="7" readonly="readonly" />
				To 
					<input name="DRT" type="text" id="DRT" size="7" readonly="readonly" />
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Fix By Date</td>
				<td colspan="2" align="left" nowrap="nowrap" style="color:#FFFFFF;">
				From 
					<input name="FBDF" type="text" id="FBDF" size="7" readonly="readonly" />
				To 
					<input name="FBDT" type="text" id="FBDT" size="7" readonly="readonly" />
				</td>
			</tr>
			<tr>
				<td colspan="4" align="left">&nbsp;</td>
				<td><!--<input type="hidden" value="report" name="sect" id="sect" />-->
					<input name="SearchInsp" type="button" onClick="submitForm();" class="submit_btn" id="button" value="" style="background-image:url(images/btn_run_report.png); width:148px; height:40px;"  />
				</td>
			</tr>
		</form>
		</table>
<?php }?>
<?php if($_SESSION['web_report_progress_monitoring'] == 1){?>
		<table cellpadding="0" cellspacing="5" border="0" id="buttoon_progressMonitoringScreen" style="display:none;width:900px;width:700px\9;">
		<form action="pdf/i_report_pdf_summary.php" name="form_progress_monitoring" id="form_progress_monitoring" target="_blank" method="post">
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Project Name <span class="reqire">*</span></td>
				<td colspan="2" valign="top">
					<select name="projName" id="projNamePM" class="select_box" onChange="resetIds();startAjax1(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						<?php if(isset($_SESSION['ww_is_company'])){
							FillSelectBox("project_id, project_name","user_projects","is_deleted=0","project_name");
						}else{
							FillSelectBox("project_id, project_name","user_projects","user_id = '".$owner_id."' and is_deleted=0","project_name");
						}?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;" id="projectPMError">The project field is required</div>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Report Type <span class="reqire">*</span></td>
				<td colspan="2" valign="top" >
					<select name="reportType" id="reportTypePM"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);" onChange="resetIds();">
						<option value="">Select</option>
<?php if($_SESSION['web_report_onsite'] == 1){?>
						<option value="inCompleteWork">Onsite Status Report</option>
<?php }?>
<?php if($_SESSION['web_report_door_sheet'] == 1){?>
						<option value="doorSheet">Door Sheet</option>
<?php }?>
<?php if($_SESSION['web_report_wall_chart'] == 1){?>
						<option value="wallChart">Wall Chart</option>
<?php }?>
					</select>
					<div class="error-edit-profile" style="width:220px;display:none;" id="reportErrorPM">The report field is required</div>
				</td>
			</tr>
			<tr id="row1">
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Location</td>
				<td colspan="2" id="ShowLocation1">
					<select name="location" id="locationPM"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Sub Location </td>
				<td colspan="2" id="ShowSubLocation1">
					<select name="subLocation" id="subLocationPM"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr id="row1">
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Sub Location 1</td>
				<td colspan="2" id="ShowLocation_sub_id">
					<select name="subLocation_sub" id="subLocation_subPM"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Issue To</td>
				<td colspan="2" id="ShowIssuedTo1">
					<select name="issuedToPM" id="issuedToPM" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
					</select>
				</td>
			</tr>
			<tr id="row2">
				<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Current Status</td>
				<td colspan="2">
					<select name="status" id="statusPM" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
						<option value="">Select</option>
						 <option value="In progress">In progress</option>
					  <option value="Behind">Behind</option>
					  <option value="Complete">Complete	</option>
                       <option value="Signed off">Signed off</option>
					</select>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr id="row3">
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">Start Date</td>
				<td colspan="2" align="left" nowrap="nowrap" style="color:#FFFFFF;">
				From 
					<input name="DRF" type="text" id="DRFPM" size="7" readonly="readonly" />
				To 
					<input name="DRT" type="text" id="DRTPM" size="7" readonly="readonly" />
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#FFFFFF;">End Date</td>
				<td colspan="2" align="left" nowrap="nowrap" style="color:#FFFFFF;">
				From 
					<input name="FBDF" type="text" id="FBDFPM" size="7" readonly="readonly" />
				To 
					<input name="FBDT" type="text" id="FBDTPM" size="7" readonly="readonly" />
				</td>
			</tr>
			<tr>
				<td colspan="4" align="left">&nbsp;</td>
				<td>
					<!--input type="hidden" value="create" name="sect" id="sect" /-->
					<input name="SearchInsp" type="button" onClick="submitFormPM();" class="submit_btn" id="button" value="" style="background-image:url(images/btn_run_report.png); width:148px; height:40px;"  />
				</td>
			</tr>
		</form>
		</table>
<?php }?>
  </div>
<div>
		<div class="demo_jui" id="show_defect" <?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){ echo 'style="padding-left:10px;"'; }?>></div>
		<div class="spacer"></div>
  </div>
</div>
<div id="userRole"></div>
<script type="text/javascript">
function closePopUp(){
	closePopup(300);
}
</script>
</body>
</html>