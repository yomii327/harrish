// JavaScript Document
//Function Area Start Here
//Initial Settings If any attribute two added u need to add here only
var closeIdArr = new Array();

$('#approved_editNo').live("click", function(){ $('#unApproveReson').val(""); $('#unApproveResonHolder').show('slow'); });
$('#approved_editYes').live("click", function(){ $('#unApproveResonHolder').hide('fast'); $('#unApproveReson').val(""); });
$('#unApproveReson').live("change", function(){ $('#emailSendFlag').val(1); });

$('#drawingTitle').live("change", function(){ $('#emailSendFlagCom').val(1); });
$('#drawingNumber').live("change", function(){ $('#emailSendFlagCom').val(1); });
$('#drawingNotes').live("change", function(){ $('#emailSendFlagCom').val(1); });
$('#tag').live("change", function(){ $('#emailSendFlagCom').val(1); });
$('#pdfStatusDyna').live("change", function(){ $('#emailSendFlagCom').val(1); });
$('#projName').change(function(){$('#projForm').submit();});

var TabArr = new Array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Civil / Landscaping', 'ESD / Green Star', 'Survey', 'Markups');
function subTitleArr(arrayElement){
	switch(arrayElement){
		case 'General' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', '3D images', 'Marketing', 'Brief and overview');
		break;
		
		case 'Architectural' :
			var TabArrTwo = new Array('Lighting', 'Tenancy Fitout', 'Penthouse Architecture', 'Drawings', 'Specifications', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details');
		break;
		
		case 'Structure' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Civil', 'Site Inspection', 'Project Advice Notice');
		break;

		case 'Services' :
			var TabArrTwo = new Array('Acoustic', 'Drawings', 'Specifications', 'Reports &amp; Schedules', 'Mechanical', 'Electrical', 'Hydraulic', 'Fire', 'Lifts');
		break;
		
		case 'Concrete &amp; PT' :
			var TabArrTwo = new Array('Concrete Profile', 'Bottom Reinforcement', 'Top Reinforcement', 'Sheer Reinforcement', 'PT Design', 'Details', 'Site Inspection');
		break;
		
		case 'Concrete & PT' :
			var TabArrTwo = new Array('Concrete Profile', 'Bottom Reinforcement', 'Top Reinforcement', 'Sheer Reinforcement', 'PT Design', 'Details', 'Site Inspection');
		break;
		
		case 'Civil / Landscaping' :
			var TabArrTwo = new Array('Civil', 'Landscaping', 'Drawings', 'Specification', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos');
		break;
		
		case 'ESD / Green Star' :
			var TabArrTwo = new Array();
		break;
		
		case 'Survey' :
			var TabArrTwo = new Array('SOS');
		break;
				
		case 'Lighting' :
			var TabArrTwo = new Array('Drawings', 'Specification');
		break;

		case 'Tenancy Fitout' :
			var TabArrTwo = new Array('Drawings', 'Specification', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos');
		break;

		case 'Penthouse Architecture' :
			var TabArrTwo = new Array('Drawings', 'Specification', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos');
		break;
		
		case 'Shop Drawings' :
			var TabArrTwo = new Array('Precast', 'Structure', 'Structural Steel', 'Mechanical', 'Electrical', 'Fire', 'Hydraulics', 'Lifts', 'Facade', 'Joinery', 'Roof Access', 'Miscellaneous');
		break;
/*
		case 'Landscaping' :
			var TabArrTwo = new Array('Drawings', 'Specification', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos');
		break;
*/
		default :
			var TabArrTwo = new Array();
		break;
	}	
	return TabArrTwo;
}
var attrTabs = '<ul id="attributeTabs">';
	for (i=0; i<TabArr.length; i++){
		if(TabArr[i]=='Markups'){
			attrTabs += '<li class="Admin"><a href="javascript:showPreviousMarkups(0, 0);"><span>';
		}else{
			attrTabs += '<li class="Admin"><a href="javascript:searchDrawPdf(\''+TabArr[i]+'\');"><span>';
		}
		
		if(TabArr[i] == 'Penthouse Architecture'){
			attrTabs += 'P. Architecture';
		}else{
			attrTabs += TabArr[i];
		}
		attrTabs += '</span></a>';
		
		TabArrTwo = subTitleArr(TabArr[i]);
		
		/* // Hide Attribute 2
		attrTabs += '<ul class="admindrop">';
		for (j=0; j<TabArrTwo.length; j++){
//			TabArrTwo[j] = TabArrTwo[j].replace(/'/g, "\\'");
			attrTabs += '<li class="Admin"><span onclick="searchDrawPdfSecAttr(\''+TabArr[i]+'\', \''+TabArrTwo[j].replace(/'/g, "\\'")+'\');">'+TabArrTwo[j]+'</span></li>';
		}
		attrTabs += '</ul>';
		*/
	}
	attrTabs += '</ul>';

var responseText = '<form id="allTaskTable" name="allTaskTable"><table cellpadding="0" cellspacing="0" border="0" class="display" id="drawingDefault" width="100%"><thead><tr><th width="1px"></th><th width="10px"><input id="checkall" type="checkbox" onclick="toggleCheck(this);" /></th><th width="145px">Document Number</th><th width="170px">Document Description</th><th width="80px">Rev.</th><th width="160px">Trade</th><!--th>Attribute 2</th><th>Attribute 3</th><th>Tag</th><th>Status</th><th width="80px">Download on iPad</th-->';
responseText += '<!--th>Approved</th--><th width="100px">Uploaded Date</th><th width="50px">File Type</th><th width="45px">Action</th></tr></thead><tbody><tr><td colspan="8" class="dataTables_empty">Loading data from server</td></tr></tbody></table></from>';



function showRevisions(pdfID){
	modalPopup(align, top1, 690, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'estimate_document_registration_show_v1.php?pdfID='+pdfID, loadingImage);
}
function addNewRegister(){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_estimate_document_register_v1.php?&name='+Math.random(), loadingImage, afterLoadRegistration);
}
function addNewRegisterRevision(regID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_estimate_document_register_revision_v1.php?tableID='+regID+'&name='+Math.random(), loadingImage, afterLoad);
}
function editDrawingRegister(regID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_estimate_document_register_v1.php?tableID='+regID+'&name='+Math.random(), loadingImage, afterLoadwithoutFile);
}
function removeImages(regID, DivID, fileType){
	fileType = typeof fileType !== 'undefined' ? fileType : 0;
	if(fileType == 0)
		var msg = "You are about to delete the PDF and all revisions &minus; are  you sure you want to do this?";
	else
		var msg = "You are about to delete the PDF &minus; are  you sure you want to do this?";
	var r = jConfirm(msg, null, function(r){
		if (r === true){
			showProgress();	
			var curData = "tableID="+regID+"&name="+Math.random();
			if(fileType)
				curData = "tableID="+regID+"&fileType="+fileType+"&name="+Math.random();

			$.ajax({
				url: "estimate_remove_document_register_file_v1.php",
				type: "POST",
				data: curData,
				success: function (res) {
					hideProgress();
					$('#'+DivID).hide('slow');
					$('#'+DivID).html('');
					if(fileType){
						var divArr = DivID.split('_');
						var newDivID = 'drawing_'+(divArr[1]-1);
						$('#'+newDivID+' div.deleteHolder').show('fast');
//						addUploadFile();
					}
					RefreshTable();
				}
			});	
		}else{
			return false;
		}
	});
}
function removeDwgFile(regID, DivID, fileType){
	fileType = typeof fileType !== 'undefined' ? fileType : 0;
	if(fileType == 0)
		var msg = "You are about to delete the DWG file &minus; are  you sure you want to do this?";
	else
		var msg = "You are about to delete the DWG file &minus; are  you sure you want to do this?";
	var r = jConfirm(msg, null, function(r){
		if (r === true){
			showProgress();	
			var curData = "tableID="+regID+"&uniqueID="+Math.random();
			if(fileType)
				curData = "tableID="+regID+"&fileType="+fileType+"&uniqueID="+Math.random();

			$.ajax({
				url: "remove_drawing_register_file_v1.php",
				type: "POST",
				data: curData,
				success: function (res) {
					hideProgress();
					$('#anchoreDelete').hide('slow');
					$('#containerDelete').html('');
					if(fileType){
						var divArr = DivID.split('_');
						var newDivID = 'drawing_'+(divArr[1]-1);
						$('#'+newDivID+' div.deleteHolder').show('fast');
						addUploadFile();
					}
					RefreshTable();
				}
			});	
		}else{
			return false;
		}
	});
}
function updateDrawingRegisterData(){
	if($('#drawingTitle').val().trim() == ''){$('#errorDrawingTitle').show('slow');return false;}else{$('#errorDrawingTitle').hide('slow');}
	if($('#drawingNumber').val().trim() == ''){$('#errorDrawingNumber').show('slow');return false;}else{$('#errorDrawingNumber').hide('slow');}
	if($('#drawingTitle').val().trim() == $('#drawingNumber').val().trim()){$('#errorDrawingTitle1').show('slow');return false;}else{$('#errorDrawingTitle1').hide('slow');}
	if($('#drawingattribute2').val() != ""){
		var valueArr = new Array();
		var drawingattribute2Multiple = "";
		valueArr = $('#drawingattribute2').val();
		if(!valueArr){}else{
			valueArr = valueArr.filter(function(v){return v!==''});
			drawingattribute2Multiple = valueArr.join('###');
		}
		$('#drawingattribute2Multi').val(drawingattribute2Multiple);
	}
	showProgress();
	$.post('edit_estimate_document_register_v1.php?antiqueID='+Math.random(), $('#editDrawingForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			$('#outPutResultPara').text(jsonResult.msg);	
			$('#outPutResult').show('fast');	
			closePopup(300);
			setTimeout(function(){$('#outPutResult').hide('slow');	},3000);
		}else{
			jAlert('Data updation failed, try again later');
		}
		RefreshTable();
	});
}
function afterLoadRegistration(){
	$('select#drawingattribute1').change(function(){
		var currValue = $(this).val();
		var outputStr = '<option value="">Select</option>';
	//alert(currValue);
		TabArrTwo = subTitleArr(currValue);
		for (i=0; i<TabArrTwo.length; i++){
			outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
		}
		$('#drawingattribute2').html(outputStr);
	});
	var config = {
		support : ",application/pdf,application/x-download",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDivDrager",// Upload Area ID
		uploadUrl: "add_estimate_document_register_v1.php?antiqueID="+Math.random()// Server side upload url
	}
	deadlockPDF = true;
	var revision4check = "";	
	initMultiUploader(config);
}
function afterLoad(){
	$('select#drawingattribute1').change(function(){
		var currValue = $(this).val();
		var outputStr = '<option value="">Select</option>';

		TabArrTwo = subTitleArr(currValue);

		for (i=0; i<TabArrTwo.length; i++){
			outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
		}

		$('#drawingattribute2').html(outputStr);
	});
	var config = {
		support : ",application/pdf,application/x-download",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDivDrager",// Upload Area ID
		uploadUrl: "add_estimate_document_register_revision_v1.php?antiqueID="+Math.random()// Server side upload url
	}
	initMultiUploader(config);
	deadlockPDF = true;
	var revision4check = "";	
}
function afterLoadwithoutFile(){
	$('select#drawingattribute1').change(function(){
		var currValue = $(this).val();
		var outputStr = '<option value="">Select</option>';

		TabArrTwo = subTitleArr(currValue);

		for (i=0; i<TabArrTwo.length; i++){
			outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
		}

		$('#drawingattribute2').html(outputStr);
	});
}
$('select#drawingattribute1Default').change(function(){
	var currValue = $(this).val();
	var outputStr = '<option value="">Select</option>';

	TabArrTwo = subTitleArr(currValue);

	for (i=0; i<TabArrTwo.length; i++){
		outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
	}

	$('#drawingattribute2Default').html(outputStr);
});
function RefreshTable(){
	$.getJSON("estimate_document_register_data_table_v1.php?"+params, null, function( json ){
		table = $('#drawingDefault').dataTable();
		oSettings = table.fnSettings();
		table.fnClearTable(this);
		
		for (var i=0; i<json.aaData.length; i++){
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw(false);
//		table.fnDraw();
	});
}
function resetSearch(){
	$('#drawingattribute1Default').val('');
	$('#drawingattribute2Default').html('<option value="">Select</option>');
	$('#pdfStatus').val('');
	$('#searchKeyword').val('');

	var TabArr = new Array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Civil / Landscaping', 'ESD / Green Star', 'Survey');
	attrTabs = '<ul id="attributeTabs">';
	for (i=0; i<TabArr.length; i++){
		attrTabs += '<li class="Admin"><a href="javascript:searchDrawPdf(\''+TabArr[i]+'\');"><span>'+TabArr[i]+'</span></a>';
		
		TabArrTwo = subTitleArr(TabArr[i]);
		
		attrTabs += '<ul class="admindrop">';
		for (j=0; j<TabArrTwo.length; j++){
		//	TabArrTwo[j] = TabArrTwo[j].replace(/'/g, "\\'");
			attrTabs += '<li class="Admin"><span onclick="searchDrawPdfSecAttr(\''+TabArr[i]+'\', \''+TabArrTwo[j].replace(/'/g, "\\'")+'\');">'+TabArrTwo[j]+'</span></li>';
		}
		attrTabs += '</ul>';
	}
	attrTabs += '</ul>';
	
	var attribute1Arr = "General";
	
	$('#SearchTabsHide').html(attrTabs);
	$('#drawingDisplay').html(responseText);	
	var oTable = $('#drawingDefault').dataTable( {
		"iDisplayLength": 100,
		"aLengthMenu": [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "All"]
        ],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "estimate_document_register_data_table_v1.php?attr1="+encodeURIComponent(attribute1Arr)+"&name="+Math.random(),
		"bStateSave": true,
		"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 9] }]
	} );
//	oTable.fnSort( [ [1,'asc'] ] );
}
function bulkUploadRegisters(){
	modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_estimate_document_register_bulk_v1.php?&name='+Math.random(), loadingImage, bulkRegistration);
}
var mappingDocumentArr = {};//Global Array to store select element in 
var mappedDocArr = {};//Global Array to store select element to show again selected
function bulkRegistration(){
	var config = {
//		support : ",application/pdf,application/x-download",// Valid file formats
 		support: ",application/pdf,application/x-download,text/plain,text/csv,application/msword,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/png,image/jpg,image/jpeg,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword", // Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDiv",// Upload Area ID
		uploadUrl: "add_estimate_document_register_bulk_v1.php"// +new Date().getTime()Server side upload url
	}
	$('select#drawingattribute1').change(function(){
		var currValue = $(this).val();
		var outputStr = '<option value="">Select</option>';

		TabArrTwo = subTitleArr(currValue);

		for (i=0; i<TabArrTwo.length; i++){
			outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
		}
		$('.drawingattribute2js').html(outputStr);
	});
	mappingDocumentArr = {};
	mappedDocArr = {};
	initBulkUploader(config);
}
function addUploadFile(){
	var btnUpload = $('.replaceRev');
	var replaceRevisionID = $('#replaceRevisionID').val();
	var btnUploadCurr = btnUpload[btnUpload.length - 1];
	var pdfID = btnUploadCurr.title;	

	new AjaxUpload(btnUploadCurr, {
		action: 'auto_file_upload_v1.php?action=pdfFiles&replaceRevisionID='+replaceRevisionID+'&pdfID='+pdfID+'&uniqueID='+Math.random(),
		name: 'pdfFiles',
		onSubmit: function(file, ext){
			if (! (ext && /^(pdf|PDF)$/.test(ext))){ 
				jAlert('Only PDF files are allowed');
				return false;
			}
			showProgress();
		},
		onComplete: function(file, fileName){
			hideProgress();
			if(fileName == 'success')
				jAlert('Pdf Rplace Successfully');
			else
				jAlert('Error in file uploading please try again');
		}
	});
}
function approvedSelected(projectID){
	var t = document.getElementById('drawingDefault');
	var val1 = $(t.rows[0].cells[1]).text();  

	var taskArray = new Array();
	var projectID = projectID;
	var taskCount = document.allTaskTable.elements["drawingID[]"].length;
	if(taskCount === undefined){
		var drawingID = document.getElementById('drawingID');
		if(drawingID.checked){
			parts = drawingID.value.split(".");
			taskArray[0] = parts[0];
		}
	}else{
		for(var i=0; i<taskCount; i++){
			var drawingID = document.allTaskTable.elements["drawingID[]"][i];
			if(drawingID.checked){
				parts = drawingID.value.split(".");
				taskArray[i] = parts[0];
			}else{
				taskArray[i] = 0;
			}
		}
	}
//Filter Array
	var newArr = []; 
	for (var index in taskArray) {  if( taskArray[index] ) {  newArr.push( taskArray[index] ); }  }  
	taskArray = newArr.join();

	if(taskArray[0]==''){
		jAlert('please select any file as attachment.');
		return false;
	}
	showProgress();
	$.post("edit_drawing_register_v1.php", {fileIds:taskArray, uniqueId:Math.random()}).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			jAlert(jsonResult.msg);
			RefreshTable();
		}
	});	
}
function completeAttachment(projectID){
	var taskArray = new Array();
	var projectID = projectID;
	var taskCount = document.allTaskTable.elements["drawingID[]"].length;

	if(taskCount === undefined){
		var taskId = document.getElementById('drawingID');
		var taskId = document.allTaskTable.elements["drawingID[]"];
		
		if(taskId.checked){
			taskArray[0] = taskId.value;
		}
	}else{
		for(var i=0; i<taskCount; i++){
			var taskId = document.allTaskTable.elements["drawingID[]"][i];
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
	taskArray = newArr.join();

	if(taskArray[0]=='' || taskArray==''){
		jAlert('please select any file as attachment.');
		return false;
	}

	showProgress();
	$.post("copy_file_toAttach_folder_v1.php", {fileIds:taskArray, uniqueId:Math.random()}).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			window.location.href = "?sect=compose&msgid=0&attached=Y&dcTrans=Y";
		}
	});	
}
function downloadSelectedFiles(projectID){
	var taskArray = new Array();
	var projectID = projectID;
	var taskCount = document.allTaskTable.elements["drawingID[]"].length;

	if(taskCount === undefined){
		//var taskId = document.getElementById('drawingID');
		//var vals = []
		$('#allTaskTable input:checkbox[name="drawingID[]"]').each(function() {
			if (this.checked) {
				//vals.push(this.value);
				parts = this.value.split("###");
				fileArr = parts[0].split(".");
				fileExt = fileArr.pop();
				taskArray.push(fileArr.join("."));
			}
		});
		
	}else{
		for(var i=0; i<taskCount; i++){
			var taskId = document.allTaskTable.elements["drawingID[]"][i];
			if(taskId.checked){
				parts = taskId.value.split("###");
				fileArr = parts[0].split(".");
				fileExt = fileArr.pop();
				taskArray.push(fileArr.join("."));
//				taskArray.push(parts[0]);
//				taskArray[i] = parts[0];
			}else{
				taskArray.push(0);
//				taskArray[i] = 0;
			}
		}
	}
//Filter Array
	var newArr = []; 
	for (var index in taskArray) {  if( taskArray[index] ) {  newArr.push( taskArray[index] ); }  }  
	taskArray = newArr.join();
	
	if(taskArray[0] == '' || taskArray[0] === undefined){
		jAlert('please select any file for download.');
		return false;
	}

	var curstomName = '';	
	jPrompt('Zip File Name:', '', 'Download', function(r) {
		if( r ){ curstomName = r; }
		
		$.post("estimate_download_document_register_files_v1.php", {projectID:projectID, customName:curstomName, fileIds:taskArray, uniqueId:Math.random()}).done(function(data) {
			hideProgress();
			var jsonResult = JSON.parse(data);	
			if(jsonResult.status){
				document.location = jsonResult.url;
			}else{
				jAlert('Data updation failed, try again later');
			}
//			$('#popUpReportResponse').html(data);
		});	
	});
}
function drawingRegisterReport(projectID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'drawing_registration_report_v1.php?projID='+projectID+'&name='+Math.random(), loadingImage);
}
function drawingRegisterReportManager(projectID){
	modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'drawing_registration_report_manager_v1.php?projID='+projectID+'&name='+Math.random(), loadingImage, selectFilters);
}
function runRerporDrawingRegister(projectID){
	var revisionType = $('#revisioinReport').val().trim();
	var sortBy = $('#sortByReport').val().trim();
	showProgress();
	$.get("drawing_registration_report_v1.php", {projID:projectID, revisionType:revisionType, sortBy:sortBy, spectialCon:'Y', name:Math.random()}).done(function(data) {
		hideProgress();
		$('#mainContainer').html(data);
	});	
}
function selectFilters(){
	new JsDatePick({ useMode:2, target:"DAF", dateFormat:"%d-%m-%Y" });
	new JsDatePick({ useMode:2, target:"DAT", dateFormat:"%d-%m-%Y" });
}
function runRerpor(projectID){
	var userReport = $("#userReport").val().trim();
	var searchKeywordReport = $("#searchKeywordReport").val().trim();
	var DAF = $("#DAF").val().trim();
	var DAT = $("#DAT").val().trim();
	var companyReport = $("#companyReport").val().trim();
	showProgress();
	$.post("drawing_registration_report_manager_v1.php", {projID:projectID, userReport:encodeURIComponent(userReport), searchKeywordReport:searchKeywordReport, DAF:encodeURIComponent(DAF), DAT:encodeURIComponent(DAT), companyReport:encodeURIComponent(companyReport), uniqueID:Math.random()}).done(function(data) {
		hideProgress();
		$('#popUpReportResponse').html(data);
	});	
}
function clearDateRaised(){ $('#DAF, #DAT').val(''); }
function resetReportSearch(){ $('#userReport, #searchKeywordReport, #DAF, #DAT, #companyReport').val(''); }
function showHistory(pdfID, projID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'estimate_document_history_table_v1.php?&name='+Math.random(), loadingImage, function() {loadData(pdfID, projID);});
}
function loadData(pdfID, projID){
	$('#example_server').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "estimate_document_history_ajax_table_v1.php?pdfID="+pdfID+"&projectID="+projID,
		"bStateSave": true,
		"bFilter": false,
		"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 1, 2] }]
	});
}
function toggleCheck(obj){
	var checkedStatus = obj.checked;
	$('#drawingDefault tbody tr').find('td:first :checkbox').each(function () {
		if(!$(this).is(':disabled')){
			$(this).prop('checked', checkedStatus);
			var currentIndexArr = $(this).val().split("###");
			var currentIndex = currentIndexArr.pop();
			var index = $.inArray(currentIndex, closeIdArr);
			if(checkedStatus){
				if(index == -1)
					closeIdArr.push(currentIndex);
			}else{
				closeIdArr.splice(index, 1);
			}
			$(this).prop('checked', checkedStatus);
		}
	});
	console.log(closeIdArr);
}
function printDiv(){
	var divToPrint = document.getElementById('mainContainer'); 
//	var newWin = window.open('', 'PrintWindow', '', false);
	var newWin = window.open('', 'PrintWindow', '', false); 
	newWin.document.open(); 
	newWin.document.write('<html><head><meta http-equiv="X-UA-Compatible" content="IE=8"><style>body { size:A4-landscape; }</style></head><body onload="window.print();"><style type="text/css">table.collapse { border-collapse: collapse; border: 1pt solid black; }table.collapse td { border: 1pt solid black; padding: 2px; }.pagination{display:none;}.buttonDiv{display:none;}.footer{display:block;}</style>'+divToPrint.innerHTML+'</body></html>'); 
	newWin.document.close(); 
//	setTimeout(function(){newWin.close();},10000); 
}
function downloadPDF(projectID){
	var revisionType = $('#revisioinReport').val().trim();
	var sortBy = $('#sortByReport').val().trim();
	showProgress();
	$.get("pdf/drawing_registration_report_manager_v1.php", {projID:projectID, revisionType:revisionType, sortBy:sortBy, spectialCon:'Y', name:Math.random()}).done(function(data) {
		hideProgress();
		$('#mainContainer').html(data);
	});	
}
function clearDiv(){	$('#popUpReportResponse').html('');	}
function editRevisionImages(regID, regrevID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_estimate_document_register_revision_v1.php?regID='+regID+'&regrevID='+regrevID+'&name='+Math.random(), loadingImage, afterLoadRevision);
}
function afterLoadRevision(){
	var config = {
		support : ",application/pdf,application/x-download",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDivDrager",// Upload Area ID
		uploadUrl: "edit_estimate_document_register_revision_v1.php?antiqueID="+Math.random()// Server side upload url
	};
	initMultiUploaderRev(config);
	deadlockPDF = true;
	validator = true;
	var revision4check = "";	
}
function addNewRegisterDocumentTransmital(existDTID){
	existDTID = typeof existDTID !== 'undefined' ? existDTID : "";
	var attr1 = $('#drawingattribute1').val();
	attr1 = typeof attr1 !== 'undefined' ? attr1 : "";
	modalPopup_gs(align, 10, 700, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_estimate_document_register_document_transmital_v1.php?&name='+Math.random()+'&attr1='+encodeURIComponent(attr1)+'&existDTID='+existDTID, loadingImage, afterLoadRegistrationDocumentTransmital, 1);
}
function afterLoadRegistrationDocumentTransmital(){
	var config = {
		support : "image/jpg,image/png,image/bmp,image/jpeg,image/gif,application/pdf,application/x-download",// Valid file formats
		form: "addDrawingFormDocumentTransmital",// Form ID
		dragArea: "innerDivDocumentTransmital",// Upload Area ID
		uploadUrl: "add_estimate_document_register_document_transmital_v1.php?antiqueID="+Math.random()// Server side upload url
	}
	initMultiUploaderDT(config, 1);
	deadlockDocumentTransmital = true;
}
$('#disableButtonDiv').live( "click", function() {
	if($('#buttonFirstSubmit').is(':disabled')){
	  jAlert("Please upload a valid document transmittal"); // jQuery 1.3+	
	  return false; 
	}
});
function donloadThis(fileName, fileType, projectID){
	$("#downloadFrame").attr("src", "estimate_download_document_register_files_v1.php?projectID="+projectID+"&fileType="+fileType+"&fileName="+fileName+"&antiqueId="+Math.random());
}
var searchTable = '<table cellpadding="0" cellspacing="0" border="0" class="display" id="drawingDefaultSearch" width="100%"><thead><tr><th>id</th><th width="150px">Document Number</th><th width="300px">Document Description</th><th>Rev.</th><th>Trade</th><!--th>Attribute 2</th><th>Attribute 3</th><th>Tag</th><th>Status</th--><th width="70px">Action</th></tr></thead><tbody><tr><td colspan="8" class="dataTables_empty">Loading data from server</td></tr></tbody></table>';

function loadSearchForm(newDrawingID, rel, iVar){
	var secAttr = $(".bulkfiles[rel='"+rel+"']").find(".drawingattribute2js").val();
	var secAttrVal = "";
	if(!secAttr){}else{
		secAttr = secAttr.filter(function(v){return v!==''});
		secAttrVal = secAttr[0];
	}
	var currValue = $('#drawingattribute1Search').val();

	var outputStr = '<option value="">Select</option>';
	TabArrTwo = subTitleArr(currValue);
	for (i=0; i<TabArrTwo.length; i++){
		outputStr += '<option value="'+TabArrTwo[i]+'"';
		if(secAttrVal == TabArrTwo[i])	outputStr += 'selected="selected"';
		outputStr += '>'+TabArrTwo[i]+'</option>';
	}
	$('#drawingattribute2Search').html(outputStr);
//Dtattable Load Here
	$('#hiddenShowDivID').val(iVar);
	$('#hiddenNewDrawingID').val(newDrawingID);
	$('#searchResultBox').show();
	$('#searchResultBox').html(searchTable);
	if(!mappedDocArr){}else{
		var mappedKeyArr = Object.keys(mappedDocArr);
		$('#selectedDocName').html('');
		if($.inArray(newDrawingID, mappedKeyArr) > -1){
			var index = $.inArray(newDrawingID, mappedKeyArr);
			$('#selectedDocName').html(mappedDocArr[newDrawingID][1]);
		}
/*		var otpt = "";
		for (var key in mappedDocArr) {
			if(otpt == "")
				otpt = mappedDocArr[key][1];
			else
				otpt += ", " + mappedDocArr[key][1];
		
			$('#selectedDocName').append(otpt);
		}*/
	}

	oTable = $('#drawingDefaultSearch').dataTable( {
		"iDisplayLength": 10,
		"aLengthMenu": [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "All"]
        ],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "estimate_document_register_data_table_search_v1.php?selectID="+encodeURIComponent(newDrawingID)+"&dispID="+encodeURIComponent(iVar)+"&req=search&attr1="+encodeURIComponent(currValue)+"&attr2="+encodeURIComponent(secAttrVal)+"&name="+Math.random(),
		"bStateSave": true,
		/*"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			for (var key in mappedDocArr) {
				if(aData[0] == mappedDocArr[key][0])
					$(nRow).addClass('selectedDoc');
			}	
			return nRow;
		},*/
		"aoColumnDefs": [ { "bVisible": false, "aTargets": [ 0 ] }, { "bSortable": false, "aTargets": [ 4 ] }]
	} );
//	oTable.fnSort( [ [1,'asc'] ] );

}
function showMappingTable(newDrawingID, divRel, iVar){
	var attrVal = ""; //$('#drawingattribute1').val().trim();

	modalPopup_gs(align, 20, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'estimate_document_register_mapping_search_v1.php?&name='+Math.random()+'&attributeVal='+encodeURIComponent(attrVal), loadingImage, function(){loadSearchForm(newDrawingID, divRel, iVar);}, 2);
}
function resetSearchDynamic(){
	$('#drawingattribute1Search, #searchKeywordSearch').val('');
	$('#drawingattribute2Search').html('<option value="">Select</option>');
	$('#searchResultBox').html('');$('#searchResultBox').hide();
}


$('select#drawingattribute1Search').live("change", function(){
	var currValue = $(this).val();
	var outputStr = '<option value="">Select</option>';

	TabArrTwo = subTitleArr(currValue);

	for (i=0; i<TabArrTwo.length; i++){
		outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
	}

	$('#drawingattribute2Search').html(outputStr);
});

function selectedDocumet(newDocKey, dispID, docID, pdfName, docName){//selectDrawingNameHolder

	$('#selectDrawingNameHolder_'+dispID).html('<img src="images/link.png" alt="Linked" style="margin:0 5px 0 5px;width:15px;" />&nbsp;<strong>'+docName+'</strong>');
	mappingDocumentArr[newDocKey] = docID+'####'+pdfName;
	mappedDocArr[newDocKey] = new Array(docID, docName);
	closePopup_gs(100, 2);
}

function bulkRegistrationDWG(){
	var config = {
		support : "",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDiv",// Upload Area ID
		uploadUrl: "add_drawing_files_bulk_v1.php?antiqueID="+Math.random()// Server side upload url
	}
	mappingDocumentArr = {};
	mappedDocArr = {};
	initbulkUploaderDWG(config);
}

function bulkUpdateDwg(){
	modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_drawing_files_bulk_v1.php?&name='+Math.random(), loadingImage, bulkRegistrationDWG);
}

function removeBulkAttachment(DivID){
	var r = jConfirm('Are you sure you want to remove this?', null, function(r){
		if (r === true){
			$("#divId_"+DivID).hide(300);
			$(".divId_"+DivID).hide(300);			
			$("#removeId_"+DivID).val(1);
		}else{
			return false;
		}
	});
}

function approvedSelected(projectID){
	var t = document.getElementById('drawingDefault');
	var val1 = $(t.rows[0].cells[1]).text();  

	var taskArray = new Array();
	var projectID = projectID;
	var taskCount = document.allTaskTable.elements["drawingID[]"].length;
	if(taskCount === undefined){
		var drawingID = document.getElementById('drawingID');
		if(drawingID.checked){
			parts = drawingID.value.split("###");
			taskArray[0] = parts[2];
		}
	}else{
		for(var i=0; i<taskCount; i++){
			var drawingID = document.allTaskTable.elements["drawingID[]"][i];
			if(drawingID.checked){
				parts = drawingID.value.split("###");
				taskArray[i] = parts[2];
			}else{
				taskArray[i] = 0;
			}
		}
	}
//Filter Array
	var newArr = []; 
	for (var index in taskArray) {  if( taskArray[index] ) {  newArr.push( taskArray[index] ); }  }  
	taskArray = newArr.join();

	if(taskArray[0] == ''  || taskArray[0] === undefined){
		jAlert('please select any file.');
		return false;
	}
	showProgress();
	$.post("edit_drawing_register_v1.php", {fileIds:taskArray, uniqueId:Math.random()}).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			jAlert(jsonResult.msg);
			RefreshTable();
		}
	});	
}

function searchDrawSearch(){
	var drawingattribute1Search = "";
	drawingattribute1Search = $('#drawingattribute1Search').val().trim();
	var drawingattribute2Search = $('#drawingattribute2Search').val().trim();
	var searchKeywordSearch = $('#searchKeywordSearch').val().trim();
	var hiddenNewDrawingID = ($('#hiddenNewDrawingID').val().trim() != "") ? $('#hiddenNewDrawingID').val().trim() : "";
	var iVar = ($('#hiddenShowDivID').val().trim() != "") ? $('#hiddenShowDivID').val().trim() : "";
	/*if(drawingattribute1Search == '' && drawingattribute2Search == '' && searchKeywordSearch == ''){
		jAlert('Please select any filter criteria to perform search operation ');
		return false;
	}*/
	$('#searchResultBox').show();
	$('#searchResultBox').html(searchTable);
	
	if(!mappedDocArr){}else{
		var mappedKeyArr = Object.keys(mappedDocArr);
		$('#selectedDocName').html('');
		if($.inArray(hiddenNewDrawingID, mappedKeyArr) > -1){
			var index = $.inArray(hiddenNewDrawingID, mappedKeyArr);
			$('#selectedDocName').html(mappedDocArr[hiddenNewDrawingID][1]);
		}
/*		var otpt = "";
		for (var key in mappedDocArr) {
			if(otpt == "")
				otpt = mappedDocArr[key][1];
			else
				otpt += ", " + mappedDocArr[key][1];
		
			$('#selectedDocName').append(otpt);
		}*/
	}
	
	var oTable = $('#drawingDefaultSearch').dataTable( {
		"iDisplayLength": 10,
		"aLengthMenu": [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "All"]
        ],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "estimate_document_register_data_table_search_v1.php?selectID="+encodeURIComponent(hiddenNewDrawingID)+"&dispID="+encodeURIComponent(iVar)+"&req=search&attr1="+encodeURIComponent(drawingattribute1Search)+"&attr2="+encodeURIComponent(drawingattribute2Search)+"&searchKey="+encodeURIComponent(searchKeywordSearch)+"&name="+Math.random(),
		"bStateSave": true,
		/*"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			for (var key in mappedDocArr) {
				if(aData[0] == mappedDocArr[key][0])
					$(nRow).addClass('selectedDoc');
			}	
			return nRow;
		},*/
		"aoColumnDefs": [ { "bVisible": false, "aTargets": [ 0 ] }, { "bSortable": false, "aTargets": [ 5 ] }]
	} );
}

$('.closeInspection').live("click", function() {
	var currentIndexArr = $(this).val().split("###");
	var currentIndex = currentIndexArr.pop();
	var index = $.inArray(currentIndex, closeIdArr);
	
	if($(this).is(':checked') === false){
		if($('#checkall').is(':checked'))
			$('#checkall').prop('checked', false);
	}else{
		var currentStat = true;
		$('#drawingDefault tbody tr').find('td:first :checkbox').each(function () {
			if(!$(this).is(':disabled'))
				if($(this).is(':checked') === false)
					currentStat = false;
		});
		$('#checkall').prop('checked', currentStat);
	}
	
	if(index > -1){
		closeIdArr.splice(index, 1);
		$(this).prop('checked', false);
	}else{
		closeIdArr.push(currentIndex);
		$(this).prop('checked', true);
	}
	console.log(closeIdArr);
});

function selectUserNotification(userListHolder){	
	modalPopup_gs(align, top1, 690, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'estimate_document_transmittal_user_list.php?userListHolder='+userListHolder+'&name='+Math.random(), loadingImage, 2);
}
function submitSelectUserNotification(){
	showProgress();
	$.post("estimate_document_transmittal_user_list.php?antiqueID="+Math.random(), $('#addUserListForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			$('#'+jsonResult.appendID).val(jsonResult.userList);
			closePopup_gs(300, 0);
		}
	});	
}
var lc, pos, dragId;
function showMarkupTool(fileName, markupTitle, drawingRegisterId){
	modalPopup_gs(align, 10, 1100, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'drawing_registration_show_markup_tool.php?fileName='+fileName+'&drawingRegisterId='+drawingRegisterId+'&name='+Math.random(), loadingImage, function(){		
	closePopup(300); var count = 0, imgWidth, imgHeight;

	/* Start:- Image croping section */
		$("#resizable").resizable({ containment: "parent" });
		$("#resizable").draggable({ containment: "parent" });

		var img_width = $('#crop_img').width();
		var img_height = $('#crop_img').height();
		//$("#parent_container").width(img_width);
		//$("#parent_container").height(img_height);

		/* Not use
		$('.cropImage').click(function() { 
			if(count>0){
				$('#crop_img').attr("src", $("#fileName").val()+"?"+Math.random());
			}
			$("#markupTool, .saveToMarkups, .cropImage, .sendCompose, .signature, .stamp").hide();
			$("#cropImageSection, .saveToCropedImage, .markupBackBtn").show();
		});	
		
		$('.markupBackBtn').click(function() {
			$("#markupTool, .saveToMarkups, .cropImage, .sendCompose, .signature, .stamp").show();
			$("#cropImageSection, .saveToCropedImage, .markupBackBtn").hide();
		});	
		*/
		
		// On click on email buttion
		$('.saveToCropedImage').click(function(e) {
			e.preventDefault();
			showProgress(); //count++;
			var position = $('#resizable').position();
			var position_img = $('#crop_img').position();
			position.top = position.top + $('#cropImageSection').scrollTop();
			position.left = position.left + $('#cropImageSection').scrollLeft();			
			$('#img_width').val($('#resizable').width());
			$('#img_height').val($('#resizable').height());
			$('#source_x').val(position.left-5);
			$('#source_y').val(position.top-55);		 
			 
		  	var datastring = $("#cropingForm").serialize();
			//var dataURL = lc.getImage().toDataURL();
			$.ajax({
			  type: "POST",
			  url: "markup_tool/imagecrops.php",
			  data: datastring,
			}).done(function(data) { hideProgress();
				$('.lc-clear ').trigger('click');
				$("#markupTool, .saveToMarkups, .sendCompose, .signature, .stamp").show();
				$("#cropImageSection, .saveToCropedImage, .markupBackBtn").hide();
				$('#crop_img').attr("src", "");
				$('#crop_img').attr("src", $("#newName").val()+"?"+Math.random());
				$('#fileName').val($("#newName").val()+"?"+Math.random());

				$("#resizable").css('top', '55px');
				$("#resizable").css('left', '13px');
				$("#resizable").css('width', '50px');
				$("#resizable").css('height', '50px');
				/*var newImage = new Image()
				newImage.src = ($("#fileName").val()+"?"+Math.random()); //'uploadImg/background.png';
				lc.saveShape(LC.createShape('Image', {x: 10, y: 10, image: newImage}));*/
				
				/* Start:- Markup section */
				$('.lc-clear ').trigger('click');
				lc = LC.init(document.getElementsByClassName('literally images-in-drawing')[0]);
				//lc.saveShape(LC.createShape('Text', {x:500, y: 250, text: 'Loading...'}));	
				showProgress();
				var newImage = new Image()
				newImage.src = ($("#fileName").val()+"?"+Math.random()); //'uploadImg/background.png';
				imgWidth = newImage.width;
				imgHeight = newImage.height;
				newImage.onload = function(){$('.lc-clear ').trigger('click');
					lc.saveShape(LC.createShape('Image', {x: 10, y: 10, image: newImage}));
					hideProgress();
				}
				
				$(".lc-Document canvas:last-child").attr("ondrop", "drop(event)");
				$(".lc-Document canvas:last-child").attr("ondragover", "allowDrop(event)");
			
				/* For export*/
				// On click on save to markups buttion
				//$('.saveToMarkups').click(function(e) {
				var start = 0; var actionType = '';
				$( ".saveToMarkups" ).on( "click", function(e) {
					start = 1;
					actionType = 'saveToMarkups';
					showProgress();
				});		
					
				$( ".sendCompose" ).on( "click", function(e) {
					start = 1;
					actionType = 'sendCompose';
					showProgress();
				});	
									
				$( "#spinner" ).on( "mousemove", function(e) {
				 // showProgress();
					if(start == 1){start = 0;
						e.preventDefault();
						var dataURL = lc.getImage().toDataURL();
						$.ajax({
						  type: "POST",
						  url: "markup_tool/script.php",
						  data: { imgBase64: dataURL, fileName:fileName, markupTitle:markupTitle, drawingRegisterId:drawingRegisterId, actionType : actionType  },
						}).done(function(data) { console.log(data); 
							if(actionType == 'sendCompose'){
								// On click on email buttion
								window.location.href = "pms.php?sect=compose&folderType=General Correspondance&msgid=0&attached=Y";
							}
							//window.open(lc.getImage().toDataURL()); 
							hideProgress(); 
							
							//alert("Markup image saved successfully."); 
						}).fail(function() { alert("Please try agian!"); })
					}
				});		

				/* End:- Markup section */				
				
			}).fail(function() { alert("Please try agian!"); })
			
		});					
	/* End:- Image croping section */
					
	}, 2);
}	

function showPreviousMarkups(pdfID, projID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'drawing_previous_markups_table_v1.php?&name='+Math.random(), loadingImage, function() {loadPreviousMarkupsData(pdfID, projID);});
}
function loadPreviousMarkupsData(pdfID, projID){
	$('#example_server').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "drawing_previous_markups_ajax_table_v1.php?pdfID="+pdfID+"&projectID="+projID,
		"bStateSave": true,
		"bFilter": false,
		"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 1, 2] }]
	});
}

function showMarkupImage(imgName, pdfID, projID){
	modalPopup_gs(align, 10, 1100, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'drawing_previous_markups_image_view.php?imgName='+imgName+'&name='+Math.random(), loadingImage, function() {closePopup_gs(300);});
}


// Drag and Drop section
function allowDrop(ev) {
    ev.preventDefault();
}
function get_pos(ev){
    pos = [ev.pageX, ev.pageY];
   console.log(pos[0]+", "+pos[1]);
}
function drag(ev) {
	dragId = ev.target.id;
    ev.dataTransfer.setData("Text",ev.target.id);
}

function drop(ev) {
    ev.preventDefault();
    var img = document.getElementById(dragId);
	var newImage = new Image()
    newImage.src = img.src;
	mousePosX = ev.pageX - 172; //35;
	mousePosY = ev.pageY - 85; //62;
    console.log("X Axis : " + ev.pageX + " Y Axis : " + ev.pageY);
	lc.saveShape(LC.createShape('Image', {x: mousePosX - lc.position.x, y: mousePosY - lc.position.y, image: newImage}));
}
 
//Function Area End Here
