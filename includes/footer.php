<?php $refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	$pi = pathinfo($refering_url);
	// print_r($pi);
	$pi['basename'];
	$files_name=explode('?',$pi['basename']);
	$pms=$files_name[0];

	if(isset($_POST['sub_x'])){
		$feed_insert="insert into feedback (feedback_type,feedback_description) values ('".$_POST['fs']."','".$_POST['desc']."')";
		mysql_query($feed_insert);
	}
?>
<style> #supportSpan :hover{ cursor:pointer; } </style>
<script type="text/javascript">
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

$(document).ready(function() {
	var align = 'center';									//Valid values; left, right, center
	var top = 100; 											//Use an integer (in pixels)
	var width = 350; 										//Use an integer (in pixels)
	var padding = 10;										//Use an integer (in pixels)
	var backgroundColor = '#FFFFFF'; 						//Use any hex code
	//var source = 'rightClick.html'; 								//Refer to any page on your server, external pages are not valid e.g. http://www.google.co.uk
	var borderColor = '#333333'; 							//Use any hex code
	var borderWeight = 4; 									//Use an integer (in pixels)
	var borderRadius = 5; 									//Use an integer (in pixels)
	var fadeOutTime = 300; 									//Use any integer, 0 = no fade
	var disableColor = '#666666'; 							//Use any hex code
	var disableOpacity = 40; 								//Valid range 0-100
	var loadingImage = 'images/loadingAnimation.gif';		//Use relative path from this page
	$('#supportSpan').click(function() {
		document.getElementById('spinner').innerHTML = '';
		modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'feedback_form.php', loadingImage);
	});
});

function descchek(){
	var Feedback = document.getElementById("fs1");
	var Support = document.getElementById("fs2");
	if(Support.checked){ var fs = 'Support'; }else{ var fs = 'Feedback'; }
	var desc = document.getElementById("desc").value;
	if(desc==''){
		jAlert('The description field is required', 'Alert','');
		return false;
	}else{
		if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
		showProgress();
		params = "feedback="+fs+"&description="+desc+"&uniqueid="+Math.random();
		xmlhttp.open("POST", "services/feedSupport.php", true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlhttp.setRequestHeader("Content-length", params.length);
		xmlhttp.setRequestHeader("Connection", "close");
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				hideProgress();
				document.getElementById('feedbackFrm').innerHTML = xmlhttp.responseText;
				closePopup(5000);
			}
		}
		xmlhttp.send(params);
	}
}
function checkSessionFocus(){
	$.post('./check_session_live.php', {antiqueID:Math.random()}).done(function(data) {
		var jsonResult = JSON.parse(data);
		if(jsonResult.status){
			console.log(jsonResult.msg);
		}else{
			<?php if(isset($_GET['sect']) && $_GET['sect']!="forgot_password"){?>
			$('#loggedMessage').show();
			$('#loggedMessage').html('Your session has been expired. You will be redirected to login page automatically. If this page appears for more than 10 seconds, <a href="#">Click Here</a> to reload.');
			setTimeout(function(){window.location.href="<?=HOME_SCREEN?>";}, 10000);
			<?php } ?>
		}
	});
}
$(document).ready(function(){
	$.ajax({
		method: 'POST',
		url: './services/check_latest_version.php',
		data: { antiqueID:Math.random() },
		success: function(data){
			var jsonResult = JSON.parse(data);
			if(jsonResult.status){
				$('#versionNumberHolder').html('<b> Latest version: '+jsonResult.iPadVersion+'</b>');
			}
		},
		erro: function(data, errorThrown){
			console.log(errorThrown);
		}
	});
});

// Scroll form bottom to top
function goTop(){
	$('html, body').animate({
			scrollTop:0
	}, 'slow');
}
</script>

<div id="bottom">
	<footer>
		<div class="container">
			<div class="row">
				<div class="col-md-offset-2 col-md-8 col-sm-9">
					<div class="copy">
						<span>Copyright &copy; <?=date('Y')?> All rights reserved &#8212; <?=SITE_NAME?></span><span id="versionNumberHolder"></span>
					</div>
				</div>
				<div class="col-md-2 col-sm-3">
					<?php #if($pms=='pms.php') { ?>
						<span style="text-align: center; cursor:pointer;" id="supportSpan">Feedback &amp; Support</span>
					<?php #} ?>
				</div>
			</div>
		</div>
	</footer>
</div>
<span class="footerTop" onclick="goTop();">
	<i class="fas fa-arrow-up"></i>
</span>
