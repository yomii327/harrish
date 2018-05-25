// JavaScript Document
function AjaxShow(method, file, retult){
	var xmlhttp;
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}else{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			$('#'+retult).html(xmlhttp.responseText);
		}
	}
	xmlhttp.open(method,file,true);
	xmlhttp.send();
}
//Filter Section Start Here
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
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=sub_subLocation && proID="+obj,"Sub_ShowSubLocation");
	if(obj == ''){
		$("#sub_subLocation").html('<option value="">Select</option>');
	}
	$("#subSubLocation3").html('<option value="">Select</option>');
	$("#subSubLocation4").html('<option value="">Select</option>');
}

function startAjax(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=location && proID="+val,"ShowLocation");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=inspecrBy && proID="+val,"ShowInspecrBy");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=issuedTo && proID="+val,"ShowIssuedTo");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=userRole&& proID="+val,"userRole");
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=raisedBy&& proID="+val,"ShowRaisedBy");
} 

function subLocate(obj){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocation && proID="+obj,"ShowSubLocation");
	if(obj == ''){
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

function startAjaxCL(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=locationCL && proID="+val,"ShowLocationCL");
}

function startAjaxQrCodeCL(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=locationQrCodeCL && proID="+val,"ShowLocationQrCodeCL");
}

function subLocate1CL(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationCL1 && proID="+val,"ShowSubLocationCL");
}

function subLocate1QrCodeCL(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationCL1QrCode && proID="+val,"ShowSubLocationCLQrCode");
}

function subLocate2CL(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationCL2 && proID="+val,"Sub_ShowSubLocationCL");
}

function subLocate2QrCodeCL(val){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=subLocationCL2QrCode && proID="+val,"Sub_ShowSubLocationCLQrCode");
}

/*function sub_subLocate(obj){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=sub_subLocation && proID="+obj,"Sub_ShowSubLocation");
}*/
function subLocation1QrCodeCL(obj){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=SubLocation2QrCode && proID="+obj,"ShowSubLocation2CLQrCode");
}

function subLocate3QrCode(obj){
	AjaxShow("POST","ajaxFunctions.php?reqReport=Y&&type=SubLocation3QrCode && proID="+obj,"ShowSubLocation3CLQrCode");
}

function startAjaxCpmp(val){
	AjaxShow("POST","ajaxFunctions.php?type=userComp && proID="+val,"userComp");
}
//Filter Section End Here

//Report Generation Section Start Here
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

function pageScroll(limit, totalCount){
	clearDivCSV();	
	try{
		var params = '';
		var startWith = limit;
		var projName = $('#projName').val();
		var projectName = $('#projName :selected').text();
		var reportType = $('#reportType').val();
		var sortby = $('#sortby').val();
		var location = $('#location').val();
		var locationName = $('#location :selected').text();
		var subLocation = $('#subLocation').val();
		var subLocationName = $('#subLocation :selected').text();
		var sub_subLocation = $('#sub_subLocation').val();
		var sub_subLocationName = $('#sub_subLocation :selected').text();
		var searchKeyward = $('#searchKey').val();
		var status = $('#status').val();
		var inspectedBy = $('#inspectedBy').val();
		var inspecrType = $('#inspecrType').val();
		var status = $('#status').val();
		var inspectedBy = $('#inspectedBy').val();
		var inspecrType = $('#inspecrType').val();
		var costAttribute = $('#costAttribute').val();
		var raisedBy = $('#raisedBy').val();
		var DRF = $('#DRF').val();
		var DRT = $('#DRT').val();
		var FBDF = $('#FBDF').val();
		var FBDT = $('#FBDT').val();
		var subLocation3 = $('#subSubLocation3').val();
		var subSubLocation3Arr = new Array();
		$("#subSubLocation3 option:selected").each(function(){
			var $this = $(this);
			if ($this.length) {
				var selText = $this.text();
				subSubLocation3Arr.push(selText);
				//console.log(selText);
			}
		});
		var subLocation3Name = subSubLocation3Arr;
		var subLocation4 = $('#subSubLocation4').val();
		var subSubLocation4Arr = new Array();
		$("#subSubLocation4 option:selected").each(function(){
			var $this = $(this);
			if ($this.length) {
				var selText = $this.text();
				subSubLocation4Arr.push(selText);
				//console.log(selText);
			}
		});
		var subLocation4Name = subSubLocation4Arr;
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
		
		if(projName == ''){ $('#projectError').show('fast'); return false; }
		if(reportType == ''){ $('#reportError').show('fast'); return false; }
		
		var dateChackRaised = checkDates(document.getElementById('DRF'), document.getElementById('DRT'), 'In Date Raised Field');
		var dateChackFixed = checkDates(document.getElementById('FBDF'), document.getElementById('FBDT'), 'In Fix By Date Field');
		if(dateChackRaised === false){	return false;	}
		if(dateChackFixed === false){	return false;	}
		
		params = "projectName="+projectName+"&projName="+projName+"&location="+location+"&locationName="+locationName+"&subLocationName="+subLocationName+"&sub_subLocationName="+sub_subLocationName+"&subLocation="+subLocation+"&sub_subLocation="+sub_subLocation+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&priority="+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&raisedBy="+raisedBy+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&startWith="+startWith+"&report_type="+reportType+"&sortby="+sortby+"&searchKeyward="+searchKeyward+"&totalCount="+totalCount+"&subLocation3="+subLocation3+"&subLocation3Name="+subLocation3Name+"&subLocation4="+subLocation4+"&subLocation4Name="+subLocation4Name+"&name="+Math.random();
		
		
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
		}else if(reportType == 'executiveReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_executive_report.php?'+params, loadingImage);
		}else if(reportType == 'issuedtoExecutiveReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_issuedto_executive_report.php?'+params, loadingImage);
		}else if(reportType == 'subContractorReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_summary_sub_contractor.php?'+params, loadingImage);
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
				$('#spinner').html('<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Sending Mail ..<br/>This may take several minutes.</div>');	
				showProgress();
				
				xmlhttp.open("POST", 'send_report_mail_subCont.php', true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.setRequestHeader("Content-length", params.length);
				xmlhttp.setRequestHeader("Connection", "close");
				xmlhttp.onreadystatechange=function(){
					if (xmlhttp.readyState==4 && xmlhttp.status==200){
						hideProgress();
						$("#mainContainer").style.overflow="visible";
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
			$('#spinner').html('<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Sending Mail ..<br/>This may take several minutes.</div>');	
			showProgress();
		
			params = "to="+to+"&cc="+cc+"&bcc="+bcc+"&subject="+subject+"&attachment="+attachment+"&descEmail="+descEmail+"&name="+Math.random();
			
			xmlhttp.open("POST", 'send_report_mail.php', true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.setRequestHeader("Content-length", params.length);
			xmlhttp.setRequestHeader("Connection", "close");
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					hideProgress();
					$("#mainContainer").css({overflow: 'visible'});
					$('#mainContainer').html(xmlhttp.responseText);
				}
			}
			xmlhttp.send(params);
		}catch(e){
		//	alert(e.message); 
		}
	});
}

function downloadPDF(callback, issueToList){
	console.log(callback, issueToList);

	callback = typeof callback !== 'undefined' ? callback : 0;
	issueToList = typeof issueToList !== 'undefined' ? issueToList : 0;
	
	$('#spinner').html('<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.</div>');
	
	clearDivCSV();	
	try{
		var params = '';
		var projName = $('#projName').val();
		var projectName = $('#projName :selected').text();
		var reportType = $('#reportType').val();
		var sortby = $('#sortby').val();
		var location = $('#location').val();
		var locationName = $('#location :selected').text();
		var subLocation = $('#subLocation').val();
		var subLocationName = $('#subLocation :selected').text();
		var sub_subLocation = $('#sub_subLocation').val();
		var sub_subLocationName = $('#sub_subLocation :selected').text();
		var searchKeyward = $('#searchKey').val();
		var status = $('#status').val();
		var inspectedBy = $('#inspectedBy').val();
		var inspecrType = $('#inspecrType').val();
		var status = $('#status').val();
		var inspectedBy = $('#inspectedBy').val();
		var inspecrType = $('#inspecrType').val();
		var costAttribute = $('#costAttribute').val();
		var raisedBy = $('#raisedBy').val();
		var DRF = $('#DRF').val();
		var DRT = $('#DRT').val();
		var FBDF = $('#FBDF').val();
		var FBDT = $('#FBDT').val();
		var subLocation3 = $('#subSubLocation3').val();
		var subSubLocation3Arr = new Array();
		$("#subSubLocation3 option:selected").each(function(){
			var $this = $(this);
			if ($this.length) {
				var selText = $this.text();
				subSubLocation3Arr.push(selText);
				//console.log(selText);
			}
		});
		var subLocation3Name = subSubLocation3Arr;
		var subLocation4 = $('#subSubLocation4').val();
		var subSubLocation4Arr = new Array();
		$("#subSubLocation4 option:selected").each(function(){
			var $this = $(this);
			if ($this.length) {
				var selText = $this.text();
				subSubLocation4Arr.push(selText);
				//console.log(selText);
			}
		});
		var subLocation4Name = subSubLocation4Arr;
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
		
		if(projName == ''){ $('#projectError').show('fast'); return false; }
		if(reportType == ''){ $('#reportError').show('fast'); return false; }
		
		var dateChackRaised = checkDates(document.getElementById('DRF'), document.getElementById('DRT'), 'In Date Raised Field');
		var dateChackFixed = checkDates(document.getElementById('FBDF'), document.getElementById('FBDT'), 'In Fix By Date Field');
		if(dateChackRaised === false){	return false;	}
		if(dateChackFixed === false){	return false;	}
		
		params = "projectName="+projectName+"&projName="+projName+"&location="+location+"&locationName="+locationName+"&subLocationName="+subLocationName+"&sub_subLocationName="+sub_subLocationName+"&subLocation="+subLocation+"&sub_subLocation="+sub_subLocation+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&priority="+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&raisedBy="+raisedBy+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&report_type="+reportType+"&sortby="+sortby+"&searchKeyward="+searchKeyward+"&subLocation3="+subLocation3+"&subLocation3Name="+subLocation3Name+"&subLocation4="+subLocation4+"&subLocation4Name="+subLocation4Name+"&name="+Math.random();
		
		console.log(params);
		
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
		}else if(reportType == 'executiveReport'){
			var url = 'pdf/i_report_executive_report.php';
		}else if(reportType == 'issuedtoExecutiveReport'){
			var url = 'pdf/i_report_issuedto_executive_report.php';
		}else if(reportType == 'csvReport'){
			var url = 'pdf/i_report_csv.php';
		}
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		showProgress();
		
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
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
		alert(e.message); 
	}
}

function callAjaxDownloadSR(url, page_no, params, callback, issueToList){
	try{
		$('#spinner').html('<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.<br/>Creating first PDF</div>');
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
						$('#spinner').html('<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.<br/>Creating PDF '+ (page_no) + ' of ' + tmp2[1] +  '</div>');
						callAjaxDownloadSR (url, page_no,params, callback, issueToList);
					}else{
						hideProgress();
						$("#mainContainer").css({overflow: 'visible'});
						if(callback){
							callback(xmlhttp.responseText);					
						}else{
							$("#mainContainer").html(xmlhttp.responseText);
						}
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
		clearDivCSV();
		var params = '';
		var startWith = 0;
		var projName = $('#projName').val();
		var projectName = $("#projName option:selected").text();
		var reportType = $('#reportType').val();
		var sortby = $('#sortby').val();
		var location = $('#location').val();
		var locationName = $("#location option:selected").text();
		var subLocation = $('#subLocation').val();
		var subLocationName = $('#subLocation option:selected').text();
		var sub_subLocation = $('#sub_subLocation').val();
		var sub_subLocationName = $('#sub_subLocation option:selected').text();
		var searchKeyward = $('#searchKey').val();
		var status = $('#status').val();
		var inspectedBy = $('#inspectedBy').val();
		var issuedTo = '';
		for(var x=0; x < document.getElementById('issuedTo').length; x++){
			if (document.getElementById('issuedTo')[x].selected){
				if(issuedTo == ''){
					issuedTo = document.getElementById('issuedTo')[x].value;
				}else{
					issuedTo += "@@@"+document.getElementById('issuedTo')[x].value;
				}
			}
		}
		var subLocation3 = $('#subSubLocation3').val();
		var subSubLocation3Arr = new Array();
		$("#subSubLocation3 option:selected").each(function(){
			var $this = $(this);
			if ($this.length) {
				var selText = $this.text();
				subSubLocation3Arr.push(selText);
				//console.log(selText);
			}
		});
		var subLocation3Name = subSubLocation3Arr;
		var subLocation4 = $('#subSubLocation4').val();
		var subSubLocation4Arr = new Array();
		$("#subSubLocation4 option:selected").each(function(){
			var $this = $(this);
			if ($this.length) {
				var selText = $this.text();
				subSubLocation4Arr.push(selText);
				//console.log(selText);
			}
		});
		var subLocation4Name = subSubLocation4Arr;
		var inspecrType = $('#inspecrType').val();
		var costAttribute = $('#costAttribute').val();
		var raisedBy = $('#raisedBy').val();
		var DRF = $('#DRF').val();
		var DRT = $('#DRT').val();
		var FBDF = $('#FBDF').val();
		var FBDT = $('#FBDT').val();

	if(reportType == ''){ $('#reportError').show(); return false; }
	var dateChackRaised = checkDates(document.getElementById('DRF'), document.getElementById('DRT'), 'In Date Raised Field');
	var dateChackFixed = checkDates(document.getElementById('FBDF'), document.getElementById('FBDT'), 'In Fix By Date Field');
	if(dateChackRaised === false){	return false;	}
	if(dateChackFixed === false){	return false;	}
		
		params = "projName="+projName+"&projectName="+projectName+"&location="+location+"&locationName="+locationName+"&subLocation="+subLocation+"&subLocationName="+subLocationName+"&sub_subLocation="+sub_subLocation+"&sub_subLocationName="+sub_subLocationName+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&priority="+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&raisedBy="+raisedBy+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&startWith="+startWith+"&report_type="+reportType+"&sortby="+sortby+"&searchKeyward="+searchKeyward+"&subLocation3="+subLocation3+"&subLocation3Name="+subLocation3Name+"&subLocation4="+subLocation4+"&subLocation4Name="+subLocation4Name+"&name="+Math.random();

//Set Data for remebarains start here
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		var url = 'ajaxFunctions.php?session_type=ir';
		
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){//console.log('Hello Word !');
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
		}else if(reportType == 'executiveReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_executive_report.php?'+params, loadingImage);
		}else if(reportType == 'issuedtoExecutiveReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_issuedto_executive_report.php?'+params, loadingImage);
		}else if(reportType == 'subContractorReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_summary_sub_contractor.php?'+params, loadingImage);
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
					$("#show_defect").html(xmlhttp.responseText);
				}
			}
			xmlhttp.send(params);
		}else if(reportType == 'qrCodeReport'){
			modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_pdf_qrcode.php?'+params, loadingImage);
		}
	}catch(e){
		//alert(e.message); 
	}
}

function clearDivCSV(){	$('#show_defect').html('');	}

function pageScrollPM(limit){
	try{
		var params = "";var url = '';
		var startWith = limit;
		var projNamePM = $("#projNamePM").val();
		var locationPM = $("#locationPM").val();
		var subLocationPM = $("#subLocationPM").val();
	var subLocation_sub = $("#subLocation_subPM").val();
		var issuedToPM = $("#issuedToPM").val();
		var statusPM = $("#statusPM").val();
		var DRFPM = $("#DRFPM").val();
		var DRTPM = $("#DRTPM").val();
		var FBDFPM = $("#FBDFPM").val();
		var FBDTPM = $("#FBDTPM").val();
		var reportTypePM = $("#reportTypePM").val();

		if(projNamePM == ''){ $('#projectPMError').show(); return false;}
		if(reportTypePM == ''){ $('#reportErrorPM').show(); return false;}

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
	$('#spinner').html('<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.</div>');
	try{
		var params = "";var url = '';
		var projNamePM = $("#projNamePM").val();
		var locationPM = $("#locationPM").val();
		var subLocationPM = $("#subLocationPM").val();
		var subLocation_sub = $("#subLocation_subPM").val();
		var issuedToPM = $("#issuedToPM").val();
		var statusPM = $("#statusPM").val();
		var DRFPM = $("#DRFPM").val();
		var DRTPM = $("#DRTPM").val();
		var FBDFPM = $("#FBDFPM").val();
		var FBDFPM = $("#FBDTPM").val();
		var reportTypePM = $("#reportTypePM").val();
		if(projNamePM == ''){ $('#projectPMError').show(); return false;}
		if(reportTypePM == ''){ $('#reportErrorPM').show(); return false;}

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
					$("#mainContainer").css({overflow: 'visible'});
					$("#mainContainer").html(xmlhttp.responseText);
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
		clearDivCSV();
		var params = "";var url = '';
		var startWith = 0;
		var projNamePM = $("#projNamePM").val();
		var locationPM = $("#locationPM").val();
		var subLocationPM = $("#subLocationPM").val();
	var subLocation_sub = $("#subLocation_subPM").val();
		var issuedToPM = $("#issuedToPM").val();
		var statusPM = $("#statusPM").val();
		var DRFPM = $("#DRFPM").val();
		var DRTPM = $("#DRTPM").val();
		var FBDFPM = $("#FBDFPM").val();
		var FBDTPM = $("#FBDTPM").val();
		var reportTypePM = $("#reportTypePM").val();
		if(projNamePM == ''){ $('#projectPMError').show(); return false;}
		if(reportTypePM == ''){ $('#reportErrorPM').show(); return false;}

var dateChackRaised = checkDates(document.getElementById('DRFPM'), document.getElementById('DRTPM'), 'In Start Date Field');
var dateChackFixed = checkDates(document.getElementById('FBDFPM'), document.getElementById('FBDTPM'), 'In End Date Field');
if(dateChackRaised === false){	return false;	}
if(dateChackFixed === false){	return false;	}
		
		params = "SearchInsp="+1+"&projName=" + projNamePM + "&reportTypePM=" + reportTypePM + "&location=" + locationPM + "&subLocation=" + subLocationPM + "&subLocation_sub=" + subLocation_sub + "&issuedToPM=" + issuedToPM +"&status=" + statusPM + "&DRF=" + DRFPM + "&DRT=" + DRTPM + "&FBDF=" + FBDFPM + "&FBDT=" + FBDTPM+"&startWith="+startWith+"&name="+Math.random();

//Set Data for remebarains start here
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		var url = 'ajaxFunctions.php?session_type=pmr';
		
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){//console.log('Hello Word !');
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
		var projNamePM = $("#projNamePM").val();
		var locationPM = $("#locationPM").val();
		var subLocationPM = $("#subLocationPM").val();
		var issuedToPM = $("#issuedToPM").val();
		var statusPM = $("#statusPM").val();
		var DRFPM = $("#DRFPM").val();
		var DRTPM = $("#DRTPM").val();
		var FBDFPM = $("#FBDFPM").val();
		var FBDTPM = $("#FBDTPM").val();
		var reportTypePM = $("#reportTypePM").val();
		if(projNamePM == ''){ $('#projectPMError').show(); return false;}
		if(reportTypePM == ''){ $('#reportErrorPM').show(); return false;}

var dateChackRaised = checkDates(document.getElementById('DRFPM'), document.getElementById('DRTPM'), 'In Start Date Field');
var dateChackFixed = checkDates(document.getElementById('FBDFPM'), document.getElementById('FBDTPM'), 'In End Date Field');
if(dateChackRaised === false){	return false;	}
if(dateChackFixed === false){	return false;	}
		
		params = "SearchInsp="+1+"&projName="+projNamePM+"&location="+locationPM+"&subLocation="+subLocationPM+"&issuedToPM="+issuedToPM+"&status="+statusPM+"&DRF="+DRFPM+"&DRT="+DRTPM+"&FBDF="+FBDFPM+"&FBDT="+FBDTPM+ +"&reportTypePM="+reportTypePM;

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
				$("#show_defect").html(xmlhttp.responseText);
			}
		}
		xmlhttp.send(params);
	}catch(e){
		//alert(e.message);
	}
}

function submitFormQA(){
	try{
		clearDivCSV();
		var params = '';
		var startWith = 0;
		var projNameQA = $('#projNameQA').val();
		var reportTypeQA = $('#reportTypeQA').val();
		var locationQA = $('#locationQA').val();
		var subLocationQA1 = $('#subLocationQA1').val();
		var subLocationQA2 = $('#subLocationQA2').val();
		var subLocationQA3 = $('#subLocationQA3').val();
		
		if(projNameQA == ''){ $('#projectQAError').show(); return false; }
		if(reportTypeQA == ''){ $('#reportErrorQA').show(); return false;}
		if(reportTypeQA != 'statusReport' && reportTypeQA != 'wallchartReport'){
			if(locationQA == ''){ $('#locationQAError').show(); return false; }
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
			if (xmlhttp.readyState==4 && xmlhttp.status==200){//console.log('Hello Word !');
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
	$('#spinner').html('<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.</div>');
	try{
		var projNameQA = $('#projNameQA').val();
		var reportTypeQA = $('#reportTypeQA').val();
		var locationQA = $('#locationQA').val();
		var subLocationQA1 = $('#subLocationQA1').val();
		var subLocationQA2 = $('#subLocationQA2').val();
		var subLocationQA3 = $('#subLocationQA3').val();
		
		if(projNameQA == ''){ $('#projectQAError').show(); return false; }
		if(reportTypeQA == ''){ $('#reportErrorQA').show(); return false;}
		if(reportTypeQA != 'statusReport' && reportTypeQA != 'wallchartReport'){
			if(locationQA == ''){ $('#locationQAError').show(); return false; }
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
				$("#mainContainer").css({overflow: 'visible'});
				$("#mainContainer").html(xmlhttp.responseText);
			}
		}
		xmlhttp.send(params);
	}catch(e){
		//alert(e.message); 
	}
}

function downloadPDFCL(){
	$('#spinner').html('<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.</div>');
	try{
		var params = '';
		var startWith = 0;
		var projNameCL = $('#projNameCL').val();
		var locationCL = $('#locationCL').val();
		var subLocationCL = $('#subLocationCL').val();
		var sub_subLocationCL = $('#sub_subLocationCL').val();
		
		if(projNameCL == ''){ $('#projectErrorCL').show(); return false; }
		
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
				$("#mainContainer").css({overflow: 'visible'});
				$("#mainContainer").html(xmlhttp.responseText);
			}
		}
		xmlhttp.send(params);
	}catch(e){
		//alert(e.message); 
	}
}

function downloadPDFQrCodeCL(){
	$('#spinner').html('<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Generating Report<br/>This may take several minutes.</div>');
	try{
		var params = '';
		var startWith = 400;
		var projNameCL = $('#projName').val();
		var locationQrCodeCL = $('#locationQrCodeCL').val();
		var SublocationQrCodeCL = $('#subLocationCLQrCode').val();
		var Sublocation1QrCodeCL = $('#sub_subLocationCLQrCode').val();
		var Sublocation2QrCodeCL = $('#SubLocation2QrCode').val();
		var Sublocation3QrCodeCL = $('#SubLocation3QrCode').val();
		
		
		//if(projNameCL == ''){ $('#projectErrorCL').show(); return false; }
		
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		
		showProgress();
		params = "projNameCL="+projNameCL+"&locationCL="+locationQrCodeCL+"&subLocationCL="+SublocationQrCodeCL+"&subLocation1CL="+Sublocation1QrCodeCL+"&subLocation2CL="+Sublocation2QrCodeCL+"&subLocation3CL="+Sublocation3QrCodeCL+"&name="+Math.random();
		var url = '';
		url = 'pdf/i_report_qrcode.php';
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				hideProgress();
				$("#mainContainer").css({overflow: 'visible'});
				$("#mainContainer").html(xmlhttp.responseText);
			}
		}
		xmlhttp.send(params);
	}catch(e){
		//alert(e.message); 
	}
}

function submitFormCL(){
	try{
		clearDivCSV();
		var params = '';
		var startWith = 0;
		var projNameCL = $('#projNameCL').val();
		var locationCL = $('#locationCL').val();
		var subLocationCL = $('#subLocationCL').val();
		var sub_subLocationCL = $('#sub_subLocationCL').val();
		
		if(projNameCL == ''){ $('#projectErrorCL').show(); return false; }
		
		params = "projNameCL="+projNameCL+"&locationCL="+locationCL+"&subLocationCL="+subLocationCL+"&sub_subLocationCL="+sub_subLocationCL+"&name="+Math.random();
		
//Set Data for remebarains start here
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		var url = 'ajaxFunctions.php?session_type=clr';
		
		xmlhttp.open("POST", url, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){//console.log('Hello Word !');
			}
		}
		xmlhttp.send(params);
//Set Data for remebarains end here


		modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_checklist.php?'+params, loadingImage);
	}catch(e){
		//alert(e.message); 
	}
}

function submitFormCLQrCode(){
	try{
		var params = '';
		var startWith = 0;
		var projNameCL = $('#projName').val();
		var locationQrCodeCL = $('#locationQrCodeCL').val();
		var SublocationQrCodeCL = $('#subLocationCLQrCode').val();
		var Sublocation1QrCodeCL = $('#sub_subLocationCLQrCode').val();
		var Sublocation2QrCodeCL = $('#SubLocation2QrCode').val();
		var Sublocation3QrCodeCL = $('#SubLocation3QrCode').val();
		params = "projNameCL="+projNameCL+"&locationCL="+locationQrCodeCL+"&subLocationCL="+SublocationQrCodeCL+"&subLocation1CL="+Sublocation1QrCodeCL+"&subLocation2CL="+Sublocation2QrCodeCL+"&subLocation3CL="+Sublocation3QrCodeCL+"&name="+Math.random();
		modalPopup(align, top, 450, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'i_report_qrcode.php?'+params, loadingImage);
	}catch(e){
		//alert(e.message); 
	}
}

function submitFormComp(){
	try{
		clearDivCSV();
		var params = "";var url = '';
		var startWith = 0;
		var projNameComp = $("#projNameComp").val();
		var user = $("#user").val();
		var projStatusComp = $("#projStatusComp").val();

		if(projNameComp == ''){ $('#projectErrorComp').show(); return false; }

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

function closePopUp(){
	closePopUp(300);
}
//Report Generation Section End Here

//Rerport Print Code Here
function printDiv(){
	var divToPrint = document.getElementById('mainContainer'); 
	var newWin = window.open('', 'PrintWindow', '', false); 
	newWin.document.open(); 
	newWin.document.write('<html><head><meta http-equiv="X-UA-Compatible" content="IE=8"><style>body { size:A4-landscape; }</style></head><body onload="window.print();"><style type="text/css">table.collapse { border-collapse: collapse; border: 1pt solid black; }table.collapse td { border: 1pt solid black; padding: 2px; }.pagination{display:none;}.buttonDiv{display:none;}.footer{display:block;}</style>'+divToPrint.innerHTML+'</body></html>'); 
	newWin.document.close(); 
}


function subLocate3(obj){
	console.log($('#subSubLocation4').attr("name"));
	if(obj!=''){
		AjaxShow("POST","ajaxFunctions.php?type=subLocation3_qc&&proID="+obj,"showSubLocation3");
		$("#subSubLocation4").html('<option value="">Select</option>');
	}else{
		subLocate3_earlier();
		subLocate4_earlier();
	}
}
function subLocate3_earlier(){
	var str = '<select  multiple="multiple" name="subSubLocation3" id="subSubLocation3"  class="select_box" style="width:220px;background-image:url(images/multiple_select_box.png); height:76px;"><option value="">Select</option></select>';
	$('#showSubLocation3').html(str);
}
function subLocate4(obj){
	var doo = [];
	$("#subSubLocation3 :selected").map(function(i, el) {
		doo[i] =$(el).val();
	});
	obj = doo;
	if(obj!=''){
		AjaxShow("POST","ajaxFunctions.php?type=subLocation4_qc&&proID="+obj,"showSubLocation4");
	}else{
		subLocate4_earlier();
	}
}
function subLocate4_earlier(){
	var str = '<select  multiple="multiple" name="subSubLocation4" id="subSubLocation4"  class="select_box" style="width:220px;background-image:url(images/multiple_select_box.png); height:76px;"><option value="">Select</option></select>';	
	$('#showSubLocation4').html(str);
}