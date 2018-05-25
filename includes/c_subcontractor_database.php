<?php
error_reporting(0);
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);

$_SESSION['companyId'] = isset($_GET["cId"])?$_GET["cId"]:$_SESSION['companyId'];

// get company data by id
	$getData = $obj->selQRYMultiple('*', 'organisations', 'is_deleted = 0 AND id IN('.$_SESSION['companyId'].')');	
	$cmpName = '';
	if(!empty($getData)){
		foreach($getData as $key => $name){
			$cmpName .= !empty($cmpName)?(($key == sizeof($getData)-1)?' and ':', '):'';
			$cmpName .= $name['company_name'];
		}
	}
	
//include('func.php');
if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){
	
	if(isset($_GET['byEmail']) && $_GET['byEmail']>=1){
		$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		if ($_SERVER["SERVER_PORT"] != "80"){
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		$_SESSION['inspViewPath'] = $pageURL;
	}
?>
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

#circle {
    border-radius: 25px;
    height: 20px;
    margin-left: -5px;
    width: 20px;
}

#outerModalPopupDiv1{
	top:20px !important;
}

#outerModalPopupDiv2{
	top:15px !important;
}
</style>
<script type="text/javascript">
var deadlock = true;//File upload
var params = "";
var align = 'center';
var top1 = 50;
var width = 950;
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
	<div class="content_hd1" style="margin:10px 0 10px -10px; color: #ffffff; font-size: 24px;"><span style="float:left;"><img align="absmiddle" width="28" hspace="5" height="29" src="images/database.png"></span><?php echo $cmpName; ?> : Subcontractor Database</div>
    <a href="?sect=c_organisations" style="margin-top:-50px;" class="actionButton green_small">Back </a>
  	<br /><br />
  	<div class="search_multiple" style="border:1px solid; margin-bottom:20px;text-align:center;margin-left:10px;margin-right:10px;">
	<div id="searchDraw">
               <table width="100%" border="0">
                  <tbody>
                    <tr>
                     <td align="left" valign="top" style="padding-left:10px;">Trade</td>
                     <td align="left" valign="top" style="padding-left:10px;">Company Name</td>
                     <td align="left" valign="top" style="padding-left:10px;">Compliance <br />
                      (hold CRTL to select multiple)</td>
                     <td align="left" valign="top" style="padding-left:10px;">Search Keyword</td>
                     <td valign="top" style="padding-left:10px;">Reset<img onclick="refreshTable();" style="cursor:pointer; float:right;" src="images/reset_drw_search.png" title="Reset filter" align="top"  /></td>
                  </tr>
                  <tr>
                     <td valign="top">
                         <input type="text" name="trade" id="trade" class="inputBox" style="width: 90%;">
                     </td>
                     <td valign="top">
                         <input type="text" name="company_name" id="company_name" class="inputBox" style="width: 90%;">
                     </td>
                     <td valign="top">
                        <select name="compliance" id="compliance" class="inputBox  chzn-select" multiple="multiple" style="width: 90%; height:50px; margin-left:0px;">
                           <option value="">Select</option>
                           <option value="Yes">Yes</option>
                           <option value="No">No</option>
                        </select>
                     </td>
                     <td valign="top">
                          <input type="text" name="searchKeyword" id="searchKeyword" class="inputBox" style="width: 90%;">
                     </td>
                     <td valign="top">
                        <a href="javascript:void(0)" onclick="searchDraw();" class="actionButton green_small">Search</a>
                     </td>
                  </tr>
                  	
            </tbody></table>
      </div>
  </div>
	<div class="big_container" style="margin-left:10px;" >
    	<?php if(isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company'] == 1){ ?>
	    	<a href="javascript:void(0);" onclick="addEditRecord(0);">
				<div style=" float:left; background:url('images/add_new.png') !important; width:87px; height:24px; margin-bottom:2px; margin-top:0px !important;" class="add_new"></div>
			</a>
		<?php }?>
		<div class="success_r" id="messageDisplayDiv" style="display:none; float:left; width:300px; margin-left:20px; height:30px; margin-bottom:5px;">
			<p></p>
		</div>
		<?php include'data-table.php';?>
		<div id="container">
			<div class="demo_jui" style="width:99%" >
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="organSubContDb" width="100%">
					<thead>
						<tr>
							<th width="20%" nowrap="nowrap">Class</th>
                            <th width="6%" >Strategic Agreement</th>
							<th width="6%" >Compliance</th>
							<th width="13%" >Company Name</th>
							<th width="21%" >Contact Name </th>
							<!--th width="13%" >Contact Position</th-->
							<th width="21%" >Contact Phone</th>
							<th width="11%" >Contact Email</th>                            
							<th width="9%">Action</th>
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
            <br /><br /><br /><br />
		</div>
	</div>
</div>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/modal.popup_gs.js"></script>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
<script type="text/javascript" src="js/draw_on_canvas/JsCode.js"></script> 

<!-- Date picker --> 
<link rel="stylesheet" href="js/jquery-ui/jquery-ui.css">
<script src="js/jquery-ui/jquery-ui.js"></script> 

<!--script src="js/jquery.min.for.choosen.js" type="text/javascript"></script>
<script src="js/chosen.jquery.js" type="text/javascript"></script-->

<script type="text/javascript">
var oTable;
//$.noConflict();
$(document).ready(function() {
	oTable = $('#organSubContDb').dataTable( {
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "subcontractor_data_table.php",
		"iDisplayLength": 100,
		"bStateSave": true
	} );
	
	<?php
		if(isset($_GET['byEmail']) && $_GET['byEmail']>=1){
			echo 'addEditRecord('.$_GET['byEmail'].');
			$("#loadChecklist").trigger("click");
			$("#tab1").trigger("click"); ';
		}
	?>
});

// Refresh data table
function refreshTable(){
	$('#trade').val('');
	$('#company_name').val('');
	$('#compliance').val('');
	$('#searchKeyword').val('');
	oTable.fnClearTable();
  	oTable.fnDestroy();
	oTable = $('#organSubContDb').dataTable( {
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "subcontractor_data_table.php",
		"iDisplayLength": 100,
		"bStateSave": true,
	});
}

// Search filtered data.
function searchDraw(){
	var trade = $('#trade').val().trim();
	var company_name = $('#company_name').val().trim();
	var compliance = $('#compliance').val();
	var searchKeyword = $('#searchKeyword').val().trim();
	
	if(trade == 0 && company_name == '' && compliance == '' && searchKeyword == ''){
		jAlert('Please select any filter criteria to perform search operation ');
		return false;
	}
	var params = "trade="+encodeURIComponent(trade)+"&company_name="+encodeURIComponent(company_name)+"&compliance="+encodeURIComponent(compliance)+"&searchKeyword="+encodeURIComponent(searchKeyword)+"&name="+Math.random();
	oTable.fnClearTable();
  	oTable.fnDestroy();
  	oTable = $('#organSubContDb').dataTable( {
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "subcontractor_data_table.php?"+params,
		"iDisplayLength": 100,
		"bStateSave": true,
	});
}

// Add / Edit record
function addEditRecord(formId){
	goTop();
	modalPopup_gs(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_edit_organisations_subcontractor.php?formId='+formId+'&name='+Math.random(), loadingImage, function(){ 
		var checklistBtnClicked = 0;
		
		// Start:- Validation
		$("#editsubContForm").validate({
			rules:{  
			   'trade[]':{
					required: true
			   },
			   company_name:{
					required: true
			   },
			   company_phone:{
					required: true
			   },
			   contact_name:{
					required: true
			   },
			   contact_position:{
					required: true
			   },
			   email_address:{
					required: true,
					email: true
			   }			   
			},
			messages:{
				'trade[]':{
					required: '<div class="error-edit-profile">This field is required</div>'
					//email: '<div class="error-edit-profile">The email is not valid format.</div>'
				},
				company_name:{
					required: '<div class="error-edit-profile">The field is required.</div>'
					
				},		
				company_phone:{
					required: '<div class="error-edit-profile">This field is required</div>'
					
				},
				contact_name:{
					required: '<div class="error-edit-profile">This field is required</div>'
					
				},
				contact_position:{
					required: '<div class="error-edit-profile">This field is required</div>'
					
				},				
				email_address:{
					required: '<div class="error-edit-profile">This field is required</div>',
					email: '<div class="error-edit-profile">Invalid email id format</div>'
					
				}
				
			},
			submitHandler: function(form) {
				$('#submitBtnSubconForm').hide();
				$('#loadBtnSubconForm').show();
				
				$.ajax({
					url: form.action,
					type: form.method,
					dataType:'json',
					data: $(form).serialize(),
					success: function(response) {
						$('#submitBtnSubconForm').show();
						$('#loadBtnSubconForm').hide();
						
						if(response.status=='success'){
							refreshTable();
							$('#messageDisplayDiv').show();
							$('#messageDisplayDiv p').html(response.msg);
							
							if(checklistBtnClicked == 1){
								checklistBtnClicked = 0;
								$("#subContId").val(response.resId);	
								//$("#loadChecklist").trigger('click');
								
								// Tab window	
								var formId = $("#subContId").val();						
								width2 = 1000;
								modalPopup_gs(align, top, width2, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'subcontractor_checklist_tab.php?formId='+formId+'&name='+Math.random(), loadingImage, function(){
									$("#tab1").trigger('click');	
								}, 2);
								
								// Scroll form bottom to top
								$('html, body').animate({
										scrollTop:0
								}, 'slow');
								
							}else{
								closePopup_gs(300, 1);
								setTimeout(function(){$('#messageDisplayDiv').hide('slow');	},3000);
							}
							
						} else {
							$(".roundCorner #messageDisplayDiv").show();
							$(".roundCorner #message").html(response.msg);
							setTimeout(function(){$('.roundCorner #messageDisplayDiv').hide('slow');	},3000);
						}
					}            
				});
			},
		});
		// End:- Validation
		
		// Checklist
		$("#loadChecklist").on("click",function() {
			checklistBtnClicked = 1;
			$("#editsubContForm").submit();
		 });
		 
		// Chosen configuration  
		//loadChosen();
	}, 1);
}

// Add / edit  subcontractor questions with category
function addEditQuestionsWithCategory(isSave){
	postData = '';
	if(isSave > 0){	 
		postData = $("#questWithCategForm").serialize();
		$('#submitBtnQWCForm').hide();
		$('#loadBtnQWCForm').show();
		
	}else{
		$('#questionsWithCategory').html('<img src="images/loadingAnimation.gif" style="margin-left:35%">');
	}

	$.ajax({
		url: 'add_edit_subcontractor_questions_with_category.php?name='+Math.random(),
		type: 'POST',
		data: postData,
		success: function(response) {
			$('#submitBtnQWCForm').show();
			$('#loadBtnQWCForm').hide();
			if(isSave == 0){	
				$('#questionsWithCategory').html(response);
				$(document).ready(function() {
					loadQWCFormJS();
				});

			}else{
				$('#questionsWithCategory').html(response);
				//showHide(1);
				$(document).ready(function() {
					loadQWCFormJS();
				});
				goTop();
				refreshTable();
				/*var response = JSON.parse(response);	
				if(response.status == true){
					goTop();
					$('#questWithCategMsg').show();
					$('#questWithCategMsg p').html(response.msg);
					$('#questWithCategMsg').slideUp(7000);
				}*/	
				$('#questWithCategMsg').slideUp(7000);
			}
		}            
	});
	
}

function loadQWCFormJS(){
		//showHide(1);
		// Date picker
		$( ".datePicker" ).datepicker({dateFormat: 'dd/mm/yy', gotoCurrent: true, changeMonth: true, changeYear: true, showOtherMonths: true, selectOtherMonths: true  });
		
		// Upload attachments
		var imageIds = $("#imageIds").val();
		if(imageIds!=''){
			imgIdArr = imageIds.split(',');
			for(i = 0; i<imgIdArr.length; i++){
				imgId = imgIdArr[i]; //alert(imgIdArr[i]);
				var btnUpload = $('#fileChecklistImage'+imgId);
				var oldName = $('#oldnameChecklistImage'+imgId).val();
				new AjaxUpload(btnUpload, {
					action: 'auto_file_upload.php?action=addAttachment&uniqueID='+Math.random()+'&fieldName=saveChecklistImage'+imgId+'&oldName='+oldName+'&isMultiple=saveChecklistImage',
					name: 'saveChecklistImage'+imgId,	
					divId: imgId,	
					onSubmit: function(file, ext, divId){
						if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
							// extension is not allowed 
							status.text('Only JPG, PNG or GIF files are allowed');
							return false;
						}
						$('#fileChecklistLoading'+divId).show(); 
						$('#responseChecklistImage'+divId).html('Uploading...');
					},
					onComplete: function(file, response, divId){
						$('#responseChecklistImage'+divId).html(response);
						$('#fileChecklistLoading'+divId).hide();
						$('#removeChecklistImage'+divId).show('fast');
					}
				});
			}
		}
		
		// Upload signature
		imgId = 0;
		var btnUpload = $('#fileLBSign'+imgId);
		var oldName = $('#oldnameLBSign'+imgId).val();
		new AjaxUpload(btnUpload, {
			action: 'auto_file_upload.php?action=addAttachment&uniqueID='+Math.random()+'&fieldName=saveLBSign'+imgId+'&oldName='+oldName+'&isMultiple=saveLBSign&upLocation=signoff',
			name: 'saveLBSign'+imgId,	
			divId: imgId,	
			onSubmit: function(file, ext, divId){
				if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
					// extension is not allowed 
					status.text('Only JPG, PNG or GIF files are allowed');
					return false;
				}
				$('#fileChecklistLoading'+divId).show(); 
				$('#responseLBSign'+divId).html('Uploading...');
			},
			onComplete: function(file, response, divId){
				$('#responseLBSign'+divId).html(response);
				$('#fileChecklistLoading'+divId).hide();
				$('#removeLBSign'+divId).show('fast');
			}
		});
	
}

// Add / edit  supplier subcontractor groups
function addEditSupplierSubGroups(isSave){
	postData = ''; 
	if(isSave > 0){	
		postData = $("#supSubGroupForm").serialize();
		$('#submitBtnSSGForm').hide();
		$('#loadBtnSSGForm').show();
	}else{
		$('#approval').html('<img src="images/loadingAnimation.gif" style="margin-left:35%">');
	}

	$.ajax({
		url: 'add_edit_subcontractor_approval.php?name='+Math.random(),
		type: 'POST',
		data: postData,
		success: function(response) {
			
			if(isSave == 0){	
				$('#approval').html(response);
				
			}else{
				$('#submitBtnSSGForm').show();
				$('#loadBtnSSGForm').hide();
				
				var response = JSON.parse(response);	
				//$("#backBtnSSGForm").trigger('click');
				$('#compliance').prop('selectedIndex',0);
				if($("#approved").is(":checked")){
					$('#compliance option[value="Yes"]').attr("selected","selected");
					
				}else{
					$('#compliance option[value="No"]').attr("selected","selected");
				}
				refreshTable();
				if(response.status == true){
					goTop();
					$('#supSubGroupMsg').show();
					$('#supSubGroupMsg p').html(response.msg);
					$('#supSubGroupMsg').slideUp(5000);
				}				
			}
			
		}            
	});
	
}

// Delete sub-contractor record
function deleteSubcontractor(formId){
	var r = jConfirm('Do you want to delete this record ?', null, function(r){
		if(r == true){
			showProgress();
			$.post('add_edit_organisations_subcontractor.php?sect=delete_subcontractor', {deleteID:Math.random(), formId:formId}).done(function(data) {
				hideProgress();
				var jsonResult = JSON.parse(data);	
				if(jsonResult.status){
					refreshTable();
					$('#messageDisplayDiv').show();
					$('#messageDisplayDiv p').html(jsonResult.msg);
					closePopup(300);
					setTimeout(function(){$('#messageDisplayDiv').hide('slow');	},3000);
				}else{
					jAlert('Data updation failed, try again later');
				}
				refreshTable();
			});
		}else{
			return false;
		}
	});
}

// Open tabs
function openTab(tabId, tabName) {
	if(tabName == "questionsWithCategory"){
		addEditQuestionsWithCategory(0);
	}
	if(tabName == "approval"){
		addEditSupplierSubGroups(0);
	}
	
	$(".tabcontent").hide();
	$( ".tablinks" ).each(function( index ) {
		id = $(this).attr("id");
		if(tabId == id){
			$("#"+tabName).show();
			$("#"+id).addClass('active');
		}else{
			$("#"+id).removeClass('active');
		}
	});
	
}

// Remove images
function removeImages(divId, imgId, removeButtonId){
	var imgDiv = document.getElementById(divId);
    var imgSrc = document.getElementById(imgId).src;	
	imgDiv.innerHTML = '';		
	document.getElementById(removeButtonId).style.display = 'none';
	var str = ""+imgId+"";
	var oldId=$('#'+str.replace('photo', 'id')).val();
	document.getElementById(divId).innerHTML = '<input type="hidden" name="delImage['+oldId+']" value="'+oldId+'">';
}

// Signature box
var signatureImageAjaxUrl = '<?php //echo base_url();?>';
function showSignatureBox(idText, idNo){ 
	goTop();
	width2 = 530;
	modalPopup_gs(align, top, width2, padding, disableColor, disableOpacity, "", borderColor, borderWeight, borderRadius, fadeOutTime, 'addSignatureSection.php', loadingImage, function(){
		InitThis(idText, idNo); 
	}, 3);
}

// Show / hide row
function showHide(id){
	$(".shRow").hide();
	$(".shImg").attr('src', 'images/plus.png');
	$(".sh"+id).toggle();
	$(".img"+id).attr('src', 'images/minus.png');
	goTop();
}

/*// load chosen
function loadChosen(){
	$.noConflict();
	var config = {
		'.chzn-select': {},
		'.chzn-select-deselect': {allow_single_deselect: false},
		'.chzn-select-width': {width: "95%"}
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
}*/
</script>
<style>.roundCorner{color:#000;}</style>
