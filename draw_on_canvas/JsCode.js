// by Chtiwi Malek on CODICODE.COM

var mousePressed = false;
var lastX, lastY;
var canvas;
var ctx;
var idText;
var idNo;
function InitThis(iText, iNo) {
	idText = iText;
	idNo = iNo;
    canvas = document.getElementById('myCanvas');	
    ctx = document.getElementById('myCanvas').getContext("2d");

    $('#myCanvas').mousedown(function (e) {
        mousePressed = true;
        Draw(e.pageX - $(this).offset().left, e.pageY - $(this).offset().top, false);
    });

    $('#myCanvas').mousemove(function (e) {
        if (mousePressed) {
            Draw(e.pageX - $(this).offset().left, e.pageY - $(this).offset().top, true);
        }
    });

    $('#myCanvas').mouseup(function (e) {
        mousePressed = false;
    });

    $('#myCanvas').mouseleave(function (e) {
        mousePressed = false;
    });
}

function Draw(x, y, isDown) {
    if (isDown) {
        ctx.beginPath();
        ctx.strokeStyle = $('#selColor').val();
        ctx.lineWidth = $('#selWidth').val();
        ctx.lineJoin = "round";
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(x, y);
        ctx.closePath();
        ctx.stroke();
		ctx.fillStyle = "#FFFFFF";
    }
    lastX = x;
    lastY = y;
}

function clearArea() {
    // Use the identity matrix while clearing the canvas
    ctx.setTransform(1, 0, 0, 1, 0, 0);
    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
}

function save(sign){
	var dataURL = canvas.toDataURL("image/png");
	//console.log(dataURL);
    var idText = 'ProjectManager';
    var idNo = sign;
    //console.log(sign,'<<==sign=='); return false;
	$.ajax({
	  type: "POST",
	  url: "checklist_signature_check.php?saveImage=1"+"&sign="+sign,
	  //url: signatureImageAjaxUrl+"/swidpublice/swidTheme/draw_on_canvas/script.php",
	  data: { 
		 imgBase64: dataURL, elementID:"save"+idText+"Image"+idNo, oldImageName:$("#oldname"+idText+"Image"+idNo).val()
	  }
	}).done(function(response) {
        console.log(response, "#response"+idText+"Image"+idNo);
		$("#response"+idText+"Image"+idNo).html(response);
		$("#remove"+idText+"Image"+idNo).show('fast');
		closePopup(300);
	   //console.log('saved'+response); 
	  // If you want the file to be visible in the browser 
	  // - please modify the callback in javascript. All you
	  // need is to return the url to the file, you just saved 
	  // and than put the image in your browser.
	});
}

function evidenceSave(sign){
	var dataURL = canvas.toDataURL("image/png");
    var idText = 'ProjectManager';
    var idNo = sign;
    var taskId = sign;
	$.ajax({
	  type: "POST",
	  url: "checklist_signature_check.php?saveImage=1"+"&sign="+sign+'&customName=evidence_sign',
	  data: { 
			imgBase64: dataURL, elementID:"save"+idText+"Image"+idNo, oldImageName:$("#oldname"+idText+"Image"+idNo).val()
		}
	}).done(function(response) {
		//---------------------------------------------
		$("#evedence_button_"+taskId).show();
		$("#evedence_button_main_"+taskId).hide();
		$("#nonconf_button_"+taskId).hide();
		$("#nonconf_button_main_"+taskId).hide();
		
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

		var callUrlevd = 'qa_task_evedence.php?uniqueId='+Math.random()+'&projID='+ projId +'&taskId='+ taskId +'&userRoll='+userRoll;
		modalPopup(align, top1, 750, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, callUrlevd, loadingImage, function(){
			//---------------------------------------------
			console.log(response, "#response"+idText+"Image"+idNo);
			$("#response"+idText+"Image"+idNo).html(response);
			$("#remove"+idText+"Image"+idNo).show('fast');
		});
	});
}

