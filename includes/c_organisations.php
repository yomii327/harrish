<?php
error_reporting(0);
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);
//include('func.php');
if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php }
$builder_id=$_SESSION['ww_is_company'];
?>
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
.box1 { background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
.link1 { background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
a.link1:hover { background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
.txt13 { border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }
.action { cursor:pointer;	}
</style>
<script type="text/javascript">
var deadlock = true;//File upload
var params = "";
var align = 'center';
var top1 = 100;
var width = 800;
var padding = 10;
var backgroundColor = '#FFFFFF';
var borderColor = '#333333';
var borderWeight = 4;
var borderRadius = 5;
var fadeOutTime = 300;
var disableColor = '#666666';
var disableOpacity = 40;
var loadingImage = 'images/loadingAnimation.gif';
var copyStatus = false;
var copyId = '';
</script>
<div id="container">
	<div class="content_hd1" style="background-image:url(images/companies.png);margin:10px 0 10px -10px;"></div>
  	<br /><br />
	<div class="big_container" style="margin-left:10px;" >
    	<a href="javascript:addEditRecord(0);">
			<div style=" float:left; background:url('images/add_new.png') !important; width:87px; height:24px; margin-bottom:2px; margin-top:0px !important;" class="add_new"></div>
		</a>
		<div class="success_r" id="messageDisplayDiv" style="display:none; float:left; width:300px; margin-left:20px; height:30px; margin-bottom:5px;">
			<p></p>
		</div>
		<?php include'data-table.php';?>
		<div id="container">
			<div class="demo_jui" style="width:99%" >
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="example_server" width="100%">
					<thead>
						<tr>
							<th width="20%" nowrap="nowrap">Company Name</th>
							<th width="20%" >Address</th>
							<th width="15%" >Phone Number</th>
							<th width="17%" >Primary Contact </th>
							<!--th width="15%" >Quality Rating</th>
							<th width="13%" >Project Size</th-->
							<th width="8%">Action</th>
						</tr>
					</thead>
					<tbody>
					<tr>
						<td colspan="5" class="dataTables_empty">Loading data from server</td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="spacer"></div>
		</div>
	</div>
</div>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
<script type="text/javascript">
$(document).ready(function() {
	$('#example_server').dataTable( {
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "organisations_data_table.php",
		"iDisplayLength": 20,
		"bStateSave": true,
		"aoColumnDefs": [ {  "bSearchable": false, "bSortable": false, "aTargets": [ 4 ] }],
	} );
} );

// Add new section 
function addEditRecord(formId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'c_add_edit_organisations.php?formId='+formId+'&name='+Math.random(), loadingImage, function(){ 
		// Upload Logo
		$(function(){
			var btnUpload=$('#image1');
			var status=$('#response_image_1');
			new AjaxUpload(btnUpload, {
				action: 'auto_file_upload.php?action=otherLogo&uniqueID='+Math.random(),
				name: 'otherLogo',
				onSubmit: function(file, ext){
					if (! (ext && /^(jpg|png|jpeg)$/.test(ext))){ 
						// extension is not allowed 
						status.text('Only JPG, PNG or GIF files are allowed');
						return false;
					}
					status.text('Uploading...');
					showProgress();
				},
				onComplete: function(file, response){
					hideProgress();
					status.html(response);
					//$('#removeImg1').show('fast');
				}
			});
		});	
	
	});
}


function addEditRecordData(){
	if($('#company_name').val().trim() == ''){$('#errorCompanyName').show('slow');return false;}else{$('#errorCompanyName').hide('slow');}
	if(isNaN($('#phone').val().trim())){$('#errorPhone').show('slow');return false;}else{$('#errorPhone').hide('slow');}
	//if(isNaN($('#primary_contact').val().trim())){$('#errorPhoneContact').show('slow');return false;}else{$('#errorPhoneContact').hide('slow');}	
	
	showProgress();
	$.post('c_add_edit_organisations.php?antiqueID='+Math.random(), $('#submitForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			RefreshTable();
			$('#messageDisplayDiv').show();
			$('#messageDisplayDiv p').html(jsonResult.msg);
			closePopup(300);
			setTimeout(function(){$('#messageDisplayDiv').hide('slow');	},3000);
		}else{
			jAlert('Data updation failed, try again later');
		}
		RefreshTable();
	});
}

/* edit theme setting popup */
function editThemeSetting(formId) {
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'c_edit_setting.php?formId='+formId+'&name='+Math.random(), loadingImage, loadJs);
}
/* edit theme setting popup */

function editSettingData(){
	//if($('#header_text_color').val().trim() == ''){$('#errorCompanyName').show('slow');return false;}else{$('#errorCompanyName').hide('slow');}
	//if(isNaN($('#primary_contact').val().trim())){$('#errorPhoneContact').show('slow');return false;}else{$('#errorPhoneContact').hide('slow');}	
	
	showProgress();
	$.post('c_edit_setting.php?antiqueID='+Math.random(), $('#submitForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			RefreshTable();
			$('#messageDisplayDiv').show();
			$('#messageDisplayDiv p').html(jsonResult.msg);
			closePopup(300);
			setTimeout(function(){$('#messageDisplayDiv').hide('slow');	},3000);
		}else{
			jAlert('Data updation failed, try again later');
		}
		RefreshTable();
	});
}



function deleteUser(formId){
	var r = jConfirm('Do you want to delete this record ?', null, function(r){
		if(r == true){
			showProgress();
			$.post('c_add_edit_organisations.php', {deleteID:Math.random(), formId:formId}).done(function(data) {
				hideProgress();
				var jsonResult = JSON.parse(data);	
				if(jsonResult.status){
					RefreshTable();
					$('#messageDisplayDiv').show();
					$('#messageDisplayDiv p').html(jsonResult.msg);
					closePopup(300);
					setTimeout(function(){$('#messageDisplayDiv').hide('slow');	},3000);
				}else{
					jAlert('Data updation failed, try again later');
				}
				RefreshTable();
			});
		}else{
			return false;
		}
	});
}

function RefreshTable(){
	$.getJSON("organisations_data_table.php?", null, function( json ){
		table = $('#example_server').dataTable();
		oSettings = table.fnSettings();
		table.fnClearTable(this);
		
		for (var i=0; i<json.aaData.length; i++){
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw();
	});
}
function loadJs() {
	var header_text_color = new jscolor.color(document.getElementById('header_text_color'), {});
	var header_bg_color = new jscolor.color(document.getElementById('header_bg_color'), {});
	// var header_bg_color1 = new jscolor.color(document.getElementById('header_bg_color1'), {});
	// var header_bg_color2 = new jscolor.color(document.getElementById('header_bg_color2'), {});

	var button_text_colour = new jscolor.color(document.getElementById('button_text_colour'), {});
	var button_bg_colour1 = new jscolor.color(document.getElementById('button_bg_colour1'), {});
	//var button_bg_colour2 = new jscolor.color(document.getElementById('button_bg_colour2'), {});

	var navigation_text_color = new jscolor.color(document.getElementById('navigation_text_color'), {});
	var navigation_bg_color1 = new jscolor.color(document.getElementById('navigation_bg_color1'), {});
	//var navigation_bg_color2 = new jscolor.color(document.getElementById('navigation_bg_color2'), {});

}
function setSetting(type){
	if(type == 'header'){
		var header_text_color = $('#header_text_color').val();
		var header_bg_color = $('#header_bg_color').val();
		// var header_bg_color1 = $('#header_bg_color1').val();
		// var header_bg_color2 = $('#header_bg_color2').val();
		var header_font_size = $('#header_font_size').val();
		if(parseInt(header_font_size)>35){
			header_font_size = '35';
			$('#header_font_size').val('35');
		}
		
		//$(".header_setting").css("background", "#"+header_bg_color);
		$('.header_setting').css({background: "rgba(0, 0, 0, 0) linear-gradient(to bottom,#"+header_bg_color+" 0%, #"+header_bg_color+" 59%, #"+header_bg_color+" 100%) repeat scroll 0 0"});
		$('.header_setting').css({background: "rgba(0, 0, 0, 0) -moz-linear-gradient(top,#"+header_bg_color+" 0%, #"+header_bg_color+" 59%, #"+header_bg_color+" 100%) repeat scroll 0 0"});
		$('.header_setting').css({background: "rgba(0, 0, 0, 0) -webkit-linear-gradient(top,#"+header_bg_color+" 0%, #"+header_bg_color+" 59%, #"+header_bg_color+" 100%) repeat scroll 0 0"});



		$(".header_setting").css("font-size", header_font_size+"px");
		$(".header_setting").css("color", "#"+header_text_color);

	} else if(type == 'navigation') {
		var navigation_text_colour = $('#navigation_text_color').val();
		var navigation_bg_color1 = $('#navigation_bg_color1').val();
		//var navigation_bg_color2 = $('#navigation_bg_color2').val();
		var navigation_font_size = $('#navigation_font_size').val();
		if(parseInt(navigation_font_size)>35){
			navigation_font_size = '35';
			$('#navigation_font_size').val('35');
		}
	    $('.navigation_setting').css({background:"#"+navigation_bg_color1});
		$('.navigation_setting').css({background: "-moz-linear-gradient(top, #"+navigation_bg_color1+" 0%, #"+navigation_bg_color1+" 100%"});
		$('.navigation_setting').css({background: "linear-gradient(to bottom, #"+navigation_bg_color1+" 0%,#"+navigation_bg_color1+" 100%)"});
		$('.navigation_setting').css({filter: "progid:DXImageTransform.Microsoft.gradient( startColorstr='#"+navigation_bg_color1+"', endColorstr='#"+navigation_bg_color1+"',GradientType=0 )"});
		$(".navigation_setting").css("font-size", navigation_font_size+"px");
		$(".navigation_setting").css("color", "#"+navigation_text_colour);

	}else{
		var button_text_colour = $('#button_text_colour').val();
		var button_bg_colour1 = $('#button_bg_colour1').val();
		//var button_bg_colour2 = $('#button_bg_colour2').val();
		var button_font_size = $('#button_font_size').val();
		if(parseInt(button_font_size)>35){
			button_font_size = '35';
			$('#button_font_size').val('35');
		}
	    $('.button_setting').css({background:"#"+button_bg_colour1});
		$('.button_setting').css({background: "-moz-linear-gradient(top, #"+button_bg_colour1+" 0%, #"+button_bg_colour1+" 100%"});
		$('.button_setting').css({background: "linear-gradient(to bottom, #"+button_bg_colour1+" 0%,#"+button_bg_colour1+" 100%)"});
		$('.button_setting').css({filter: "progid:DXImageTransform.Microsoft.gradient( startColorstr='#"+button_bg_colour1+"', endColorstr='#"+button_bg_colour1+"',GradientType=0 )"});
		$(".button_setting").css("font-size", button_font_size+"px");
		$(".button_setting").css("color", "#"+button_text_colour);
	}
	
}
</script>
<script type="text/javascript" src="js/jscolor/jscolor.js"></script>
<style>.roundCorner{color:#000;}</style>