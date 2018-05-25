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

var projId = $('#selected_project_id').val().trim();
var userRoll = $('#userRoll').val().trim();

$(document).ready(function() {
	
	$(".evedence_pop").click(function(){
		var taskId = $(this).closest('tr').find('.taskId').val();
		$("#evedence_button_"+taskId).show();
		$("#evedence_button_main_"+taskId).hide();
		$("#nonconf_button_"+taskId).hide();
		$("#nonconf_button_main_"+taskId).hide();

		var callUrlevd = 'qa_task_evedence.php?uniqueId='+Math.random()+'&projID='+ projId +'&taskId='+ taskId +'&userRoll='+userRoll;
		modalPopup(align, top1, 750, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, callUrlevd, loadingImage, function(){
			loadMultipleAttachment(projId, taskId);
		});
	});

	$(".nonconf_pop").click(function(){
		var location = $(".con_location").val().trim();
		var addShowSubLocation = $(".con_showSubLocation").val().trim();
		var addShowSubLocation1 = $(".con_showSubLocation1").val().trim();
		var addShowSubLocation2 = $(".con_showSubLocation2").val().trim();
		
		var taskId = $(this).closest('tr').find('.taskId').val();
		$("#nonconf_button_"+taskId).show();
		$("#nonconf_button_main_"+taskId).hide();
		$("#evedence_button_"+taskId).hide();
		$("#evedence_button_main_"+taskId).hide();

		var callUrlcnf = 'qa_task_non_confirmance.php?uniqueId='+Math.random()+'&projID='+ projId +'&taskId='+ taskId +'&userRoll='+userRoll+'&location='+location+'&addShowSubLocation='+addShowSubLocation+'&addShowSubLocation1='+addShowSubLocation1+'&addShowSubLocation2='+addShowSubLocation2;
		modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, callUrlcnf, loadingImage, nonconformCallback);	
	});

	$(".hide_no_and_yes").click(function(){
		$(this).closest('tr').find('.evedence_button').hide();
		$(this).closest('tr').find('.nonconf_button').hide();
	});

});

//Evidence signature box.
function evdSignatureBox(idText, idNo){
	addTaskEvedence(1);
	postData = ''; //{postid:1};
	var callUrl = 'evidence_signature.php?sign='+idNo+'&taskId='+idNo;
	modalPopup(align, 100, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, callUrl, loadingImage, InitThis);
}
//Trigger Evidence pop when signature box.
/*function triggerEvedence_pop(taskId){
	showProgress();
	setTimeout(
		function(){
			hideProgress();
			$("#evedence_button_"+taskId).show();
			$("#evedence_button_main_"+taskId).hide();
			$("#nonconf_button_"+taskId).hide();
			$("#nonconf_button_main_"+taskId).hide();

			var callUrlevd = 'qa_task_evedence.php?uniqueId='+Math.random()+'&projID='+ projId +'&taskId='+ taskId +'&userRoll='+userRoll;
			modalPopup(align, top1, 750, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, callUrlevd, loadingImage, function(){
				return false;
			});
		},
	1000);
}*/

function loadMultipleAttachment(projId, taskId) {
	var countNo1 = $("#attachmentCount").val();
	var btnUpload1 = $('#attachment');
	var btnUploadCurr1 = btnUpload1[btnUpload1.length - 1];
	var pdfID1 = 0;
	new AjaxUpload(btnUploadCurr1, {
		action: 'auto_pdf_file_upload.php?action=addAttachment&uniqueID='+Math.random()+'&fieldName=saveChecklistImageMulti&isMultiple=saveChecklistImageMulti',
		name: 'saveChecklistImageMulti',
		onSubmit: function(file, ext) {
			var countNo1 = $("#attachmentCount").val();
			if (countNo1 >= 20) {
				alert("No more files please!");
				return false;
			}
			//if (!(ext && /^(jpg|png|jpeg|gif|pdf)$/.test(ext))) {
			if (!(ext && /^(jpg|png|jpeg|gif|pdf)$/.test(ext))) {
				// extension is not allowed 
				alert('Only jpg,png,jpeg,gif and PDF files allowed');
				return false;
			}
			$("img.loader").show();
		},
		onComplete: function(file, response) {
			countNo1++;
			$("#attachmentCount").val(countNo1);
			//hideProgress();
			var $html = '<div id="attchImage_'+ countNo1 +'" class="attchImage">'+ response +'<img src="images/close_new.png" class="delImage" onClick="deleteAttachment(0, \'attchImage_'+ countNo1 +'\')" alt="delete image" /></div>';
			$('#responseAttachment').append($html);
			$("img.loader").hide();
		}
	});
	
	//image2(Evidence of Inspection)
	$(function(){
		console.log('Evidence signature, taskId: ' + taskId);
		var btnUpload=$('#evidence_sign');
		var status=$('#responseProjectManagerImage'+ taskId);
		new AjaxUpload(btnUpload, {
			action: 'auto_file_upload.php?action=evidence_sign&proid='+ projId +'&uniqueID='+Math.random(),
			name: 'evidence_sign',
			onSubmit: function(file, ext){
				if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
					// extension is not allowed
					$('#innerDiv_'+ taskId +' > label').hide();
					$('#evdRemove_'+ taskId).show('fast');
					status.html('<p>Only JPG, PNG or GIF files are allowed</p>');
					return false;
				}
				status.text('Uploading...');
				showProgress();
			},
			onComplete: function(file, response){
				hideProgress();
				$('#innerDiv_'+ taskId +' > label').hide();
				$('#evdRemove_'+ taskId).show('fast');
				status.html(response);
			}
		});
	});
}

function deleteAttachment(id,divId){
	if(id != 0){
		$.ajax({
			url: 'qa_task_evedence.php?deleteImg='+Math.random(),
			type: 'POST',
			data: {delImage:id},
			success: function(response) {
				$("#"+divId).remove();
			}
		});
	}else{
		$("#"+divId).remove();
	}
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

function addTaskEvedence(sbtCheck){
	showProgress();
	var filedata = new FormData(document.getElementById("addEvedenceForm"));
	
	$.ajax({
		url: 'qa_task_evedence.php?EveInsertId='+Math.random(),
		type: "POST",
		data: filedata,
		async : false,
		processData: false,  // tell jQuery not to process the data
		contentType: false   // tell jQuery not to set contentType

	}).done(function(data) {
		hideProgress();	
		var jsonResult = JSON.parse(data);
		if (jsonResult.status){
			console.log("evedence_button_"+jsonResult.taskId);
			if(sbtCheck==0){
				closePopup(300);
			}
		}else {
		    jAlert(jsonResult.msg);
		}
		
	});
}

function addTaskNonconfirmance(){
	showProgress();
	var filedata = new FormData(document.getElementById("addEvedenceForm"));
	
	$.ajax({
		url: 'qa_task_non_confirmance.php?InsertEvedenceId='+Math.random(),
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

/*function AddItem() {
	var hid = $('#hid').val();
	console.log(hid);

	if(hid)
	{
	addedRow=[];
	for(i=0;i<hid;i++){
	addedRow.push(i);
	}
	elementCount=i;
	$('#hid').val(i+1);	

	}else{
	addedRow.push(++elementCount);	
	}
	// elementCount = parseInt(hid)+parseInt(elementCount);
	// $('#hid').val(elementCount);	
	// console.log(addedRow);
	// console.log(elementCount);
	if(addedRow.length < 3){
	if(document.getElementById('hide_'+elementCount).style.display == 'none'){
	document.getElementById('hide_'+elementCount).style.display = 'table-row';
	}else{
	document.getElementById('hide_1').style.display = 'table-row';
	}
	}else{
	jAlert("You can't add more than 3 Issue To !");
	}
}*/

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



