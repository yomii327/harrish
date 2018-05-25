<?php $_SESSION['idp'] = $_SESSION['projIdQA'];
$owner_id = $_SESSION['ww_builder']['user_id'];
include('commanfunction.php');
if(!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])){ ?>
<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }?>
<style>
#attachList{ height: 200px; overflow: auto; padding: 5px; }
#attachList, #innerDiv div.fileHolder{ background: none repeat scroll 0 0 #F3F3F3; border: 1px outset #333333; float: left; margin: 3px; padding: 5px; width: 166px; }
#attachList, #innerDiv span.fileHolder{ color: #990000; cursor: pointer; float: right; }
#locationsContainer, #attachments{ overflow-y:scroll; max-height:250px; min-height:200px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius:5px; border:1px solid; margin-top:15px; width:595px; }
.innerDiv{ float:left; border:1px solid red; width:120px; height:120px; margin:0px 0px 0px 10px; }
label.filebutton{ width:120px; height:40px; overflow:hidden; position:relative; }
label input { cursor: pointer; font-size: 7px; left: -1px; line-height: 0; margin: 0; opacity: 0; padding: 0; position: absolute; top: -2px; z-index: 999; filter:alpha(opacity=0);}
#image1, #image2{ z-index:1;}
#locationsContainer{ overflow-y: scroll; max-height: 200px; min-height: 150px; border-radius:5px; -moz-border-radius: 5px;  -webkit-border-radius: 5px; border:1px solid; margin-top:15px; width:420px; }
table.gridtable { border-width: 1px; border-color: #FFF; border-collapse: collapse; }
table.gridtable td { border-width: 1px; padding: 8px; border-style: solid; border-color: #FFF; }
.clickableLines{ cursor:pointer; padding:5px; margin-right:5px; font-size:14px;}
.clickableLines:hover{ background-color:#336FCE;padding:5px; margin-right:5px;color:#FFFFFF; font-size:14px;}
#dropDown{ cursor:pointer; }
#discriptionHide{ display:none; height: 150px; overflow-y: scroll; position:absolute; background:#FFFFFF; border:1px solid #0BA4FF; width:300px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; z-index:1000; color:#000000; text-shadow:none; }
.issueTo{ border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; border:1px solid; width:150px; border-color:#FFFFFF; height:25px;}
ul.telefilms{list-style:none; cursor:pointer; font-size:15px;}
ul.telefilms li ul{list-style:none; line-height:30px;}
.jtree-arrow { padding-right: 5px; font-size: 15px; }
.fixedByDate{ background:#FFF; cursor:default; height:20px; -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; }

</style>
<?php $object= new COMMAN_Class();
$str_id = $_GET['id'];
if(isset($str_id) and $str_id!=""){
	$str_original_form = base64_decode($str_id);
	$str_temp_array = explode("**",$str_original_form);
	$_SESSION['task_id'] = $str_temp_array[0];
	$_SESSION['task_detail_id'] = $str_temp_array[1];
	$str_id = $str_temp_array[0];
}

$q = "SELECT user_role, project_name FROM user_projects WHERE user_id = '".$owner_id."' AND project_id = '".$_SESSION['projIdQA']."'";

if($obj->db_num_rows($obj->db_query($q))==0){?><script language="javascript" type="text/javascript">window.location.href="<?=ACCESS_DENIED_SCREEN?>";</script><?php }

$projectData = $obj->db_fetch_assoc($obj->db_query($q));
$_SESSION['userRole'] = $projectData['user_role'];

$descriptionArr = $object->selQRYMultiple('location_id, sub_location_id, task, is_image_require', 'qa_task_monitoring', 'project_id = '.$_SESSION['projIdQA'].' AND task_id = '.$str_id.' AND is_deleted = 0');

$qa_ncr_sql = "SELECT task_detail_id, task_id, raised_by, comment, created_by FROM  qa_ncr_task_detail WHERE task_id = '".$_SESSION['task_id']."' and task_detail_id = '".$_SESSION['task_detail_id']."' and is_deleted=0"; 
$result_task = $obj->db_query($qa_ncr_sql);
if($obj->db_num_rows($result_task) > 0){ $ncr_task_detail_row = $obj->db_fetch_assoc($result_task); }
#echo $_SESSION['task_detail_id'];
$getQaNcrAttachmentsData = $object->selQRYMultiple('ncr_attachment_id, task_detail_id, attachment_title, attachment_description, attachment_file_name, attachment_type', 'qa_ncr_attachments', 'task_detail_id = "'.$_SESSION['task_detail_id'].'" and is_deleted=0');
$attachedData = array();
$imageAttachData = array();
#print_r($getQaNcrAttachmentsData);
foreach($getQaNcrAttachmentsData as $atData){
	if($atData['attachment_title'] == 'iPad_image' || $atData['attachment_title'] == 'Web_image'){
		$imageAttachData[] = $atData;
	}else{
		$attachedData[] = $atData;
	}
}
#$attachedData = $getQaNcrAttachmentsData; die;
?>
<script language="javascript" type="text/javascript">
var contName = new Array(); var companyName = new Array(); 
var align = 'center';
var top = 100;
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
var copyId = ''
var attachCount =<?php if(isset($attachedData) && !empty($attachedData)){ echo count($attachedData); }else{ echo 0;} ?>;
/*$(function(){
	var btnUpload=$('#attachFile');
	var status=$('#response_attachFile');
	var showResult=$('#attachList');
	new AjaxUpload(btnUpload, {
		action: 'auto_file_upload.php?action=allfiles&uniqueID='+Math.random(),
		name: 'attachFile',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif|doc|docx|txt|text|xls|xlsx|pdf|csv|CSV|JPG|PNG|JPEG|GIF|DOC|DOCX|TXT|TEXT|XLS|XLSX|PDF)$/.test(ext))){ 
				// extension is not allowed 
				status.text('Only JPG, PNG, GIF, doc, docx, txt, xls, xlsx, PDF, CSV files are allowed');
				return false;
			}
			status.text('Uploading...');
			showProgress();
		},
		onComplete: function(file, fileName){
			hideProgress();
			var attachTitle = $("#attachTitle").val();
			var attachDescription = $("#attachDescription").val();
			status.html('');
			var response = '<div id="title_'+attachCount+'"><span onclick="removeFile(\''+fileName+'\','+attachCount+')">X</span>' + attachTitle+'</div>';
			
			var response2 = '<div id="saveBox_'+attachCount+'"><input type="hidden"  id="file_'+attachCount+'" name="attachments[]" value="'+fileName+'" /><input type="hidden" maxlength="50" id="attachTitle[]" class="textBoxStyle" name="attachTitle[]" value="'+attachTitle+'">  <textarea name="attachDescription[]" id="attachDescription[]" class="textareaBoxStyle" cols="25" rows="2">'+attachDescription+'</textarea></div>';
			
			showResult.append(response);
			$('#addAttachments').append(response2);
			$("#attachTitle").val('');
			$("#attachDescription").val('');
			document.getElementById('attachFile').disabled = true;
			//document.getElementById('attachTitle').focus();
			attachCount++;
//			$('#removeImg3').show();
		}
	});
});*/

function softRemoveFile(fileName, id){
	var r = jConfirm('Are you sure want to delete ?', null, function(r){
		if (r==true){
			$("#title_"+id).remove();
			$("#saveBox_"+id).remove();
			$("#view_"+id).remove();
		}else{
			return false;
		}
	});
}	

function removeFile(fileName, id){
	var r = jConfirm('Are you sure want to delete ?', null, function(r){
		if (r==true){
			var imgSrc = "/ncr_task_files/"+fileName;	
			showProgress();	
			$.ajax({
				url: "remove_uploaded_file.php",
				type: "POST",
				data: "imageName="+imgSrc,
				success: function (res) {
					hideProgress();
					$("#title_"+id).remove();
					$("#saveBox_"+id).remove();
				}
			});
		}else{
			return false;
		}
	});
}	

$(document).ready( function() {
	
	function unloadPopupBox() {	// TO Unload the Popupbox
		$('#blockModalPopupDiv').fadeOut("slow");
		$('#outerModalPopupDiv').fadeOut("slow");		
		$('#closeModalPopupDiv').fadeOut("slow");				
		$("#container").css({ // this is just for style		
			"opacity": "1"  
		}); 
	}	
	function loadPopupBox() {	// To Load the Popupbox
		$('#blockModalPopupDiv').fadeIn("slow");
		$('#outerModalPopupDiv').fadeIn("slow");		
		$('#closeModalPopupDiv').fadeIn("slow");				
		$("#container").css({ // this is just for style
			"opacity": "0.3"  
		}); 		
	}
});

function checkAttachTitle(){
	var showResult=$('#attachList');
	var attachTitle = $("#attachTitle").val();
	if(attachTitle==""){ jAlert("Please enter tile.");
		document.getElementById('attachFile').disabled = true;
		document.getElementById('attachTitle').focus();
		if(showResult.html()==""){
			showResult.html('No Attachment Found');
		}
	}else{
		document.getElementById('attachFile').disabled = false;
		if(showResult.html()=='No Attachment Found'){
			showResult.html('');
		}
	}
}
</script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	var validator = $("#edit_qa_ncr_task_detail").validate({
		rules:{
			defect_desc:{
				required: true
			},
			raisedBy:{
				required: true
			}
		},
		messages:{
			defect_desc:{
				required: '<div class="error-edit-profile" style="margin-left:50px;">The description field is required</div>'
			},
			raisedBy:{
				required: '<div class="error-edit-profile">The raised by name field is required</div>'
			},
			
			debug:true
		}
	});
});
var spinnerVisible = false;
function showProgress() { if (!spinnerVisible) { $("div#spinner").fadeIn("fast"); spinnerVisible = true; } };
function hideProgress(){ if (spinnerVisible) { var spinner = $("div#spinner"); spinner.stop(); spinner.fadeOut("fast"); spinnerVisible = false; } };

function checkImageValid(){
	var img_valid = $('#is_image_require').val();
	//var img_valid = 1;
	var imgCount = 0;
	$( ".image_qa" ).each(function( index ) {
		if($(this).val()!=''){
			imgCount++;
		}
	});
	if(img_valid==1){
		if(imgCount>=1){
			//$('#add_qa_ncr_task_detail').submit();	
			document.edit_qa_ncr_task_detail.submit();
		}else{
			jAlert('You must select atleast one image !');
		}	
	}else{
		//$('#add_qa_ncr_task_detail').submit();
		document.edit_qa_ncr_task_detail.submit();
	}
	
}
</script>
<div class="content_center" style="margin-left:70px;margin-top:16px;">
	<div class="content_hd" style="background-image:url(images/edit_qa_task.png);margin: -5px 0 -30px -80px;margin-top:-85px\9;"></div>
	<div class="signin_form1" style="margin-top:38px;width: 665px;">
		<?php if(isset($_SESSION['inspection_added'])) { ?>
		<div id="errorHolder" style="margin-left: 40px;margin-bottom: 20px;"><div class="success_r" style="height:35px;width:405px;"><p><?=$_SESSION['inspection_added'];?></p></div></div>
		<?php unset($_SESSION['inspection_added']); } ?>
		<form action="ajax_reply.php" method="post" enctype="multipart/form-data" name="edit_qa_ncr_task_detail" id="edit_qa_ncr_task_detail">
			<div class="signin_form1" style="margin-top:-31px; margin-left:15px;">
				<font style="float:left; margin-left:10px;" size="+1">Project :<?=stripslashes($projectData['project_name'])?></font>
		<?php if($_SESSION['ww_builder']['user_id'] == 130){?>
<!--				<img src="images/view_history.png" onclick="showHistory(<?=$f['inspection_id'];?>, <?=$f['project_id'];?>);" style="margin:-38px 0 0 460px;cursor:pointer;">-->
		<?php }?>
			</div>
			<table width="100%" border="0" cellspacing="5" cellpadding="5">
				<tr>
					<td colspan="4"><input type="hidden" name="location" id="location"  />
						<input type="hidden" name="locationChecklist" id="locationChecklist"  />
						<input type="hidden" name="id_url_value" value="<?php if(isset($_GET['id'])) { echo $_GET['id']; } ?>" id="locationChecklist"  />
					</td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top" width="110">Raised By <span class="req">*</span></td>
					<td nowrap="nowrap" valign="top">Description <span class="req">*</span></td>
				</tr>
				<tr>
					<td valign="top">
						<?php if($_SESSION['ww_is_company'] == 1){ $raisedByArr = array('Builder', 'Architect', 'Structural Engineer', 'Services Engineer', 'Accreditation', 'General Consultant', 'Independent Reviewer', 'Stakeholders', 'Sub Contractor'); ?>
						<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
							<option value="">Select</option>
							<?php foreach($raisedByArr as $key=>$raisedByVal){?>
								<option value="<?=$raisedByVal?>" <?php if($ncr_task_detail_row['raised_by']  == $raisedByVal){ echo 'selected="selected"';}?>><?=$raisedByVal?></option>
							<?php }?>
						</select>
						<?php }else{
							if($_SESSION['userRole'] != 'All Defect'){?>
						<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
							<option value="">Select</option>
							<option value="<?=$_SESSION['userRole']?>" selected="selected" >
							<?=$_SESSION['userRole']?>
							</option>
						</select>
						<?php }else{ $raisedByArr = array('Builder', 'Architect', 'Structural Engineer', 'Services Engineer', 'Accreditation', 'General Consultant', 'Independent Reviewer', 'Stakeholders', 'Sub Contractor'); ?>
						<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
							<option value="">Select</option>
							<?php foreach($raisedByArr as $key=>$raisedByVal){?>
								<option value="<?=$raisedByVal?>" <?php if($ncr_task_detail_row['raised_by']  == $raisedByVal){ echo 'selected="selected"';}?>><?=$raisedByVal?></option>
							<?php }?>
						</select>
						<?php } 
						}?>
					</td>
					<td colspan="3" align="left" valign="top"><textarea id="defect_desc" name="defect_desc" class="textareaBoxStyle" style="margin-top:0px;width:340px;"><?=$ncr_task_detail_row['comment']?></textarea>
						<div style="position:absolute; margin-left: 363px;margin-top: -73px;"><img id="dropDown" src="images/downbox.png" border="0" style="background-color:none;" /></div>
						<div id="discriptionHide">
							<?php $standardDefects = $object->selQRYMultiple('description', 'standard_defects', 'project_id = '.$_SESSION['projIdQA'].' and is_deleted=0 group by description order by description');
						if(!empty($standardDefects)){?>
							<ul style="list-style:none;margin-left:-30px;" id="standardDefect">
								<?php $i=0; foreach($standardDefects as $des){$i++;?>
								<li class="clickableLines"><?php echo $des['description'];?></li>
								<?php }?>
							</ul>
							<?php }else{?>
							<ul style="list-style:none;">
								<li>No One Standard Defect Found !</li>
							</ul>
							<?php }?>
						</div>
						<?php if(isset($_SESSION['post_array']['defect_desc']) && $_SESSION['post_array']['defect_desc']==""){?>
							<lable htmlfor="fname" generated="true" class="error">
								<div class="error-edit-profile">The defect description field is required</div>
							</lable>
						<?php unset($_SESSION['post_array']['defect_desc']);
						}?>
					</td>
				</tr>
				<tr>
					<td>Add Image Here</td>
					<td>
						<div class="imageHolder" align="center">
						<div class="innerDiv" align="center">
							<div style="height:120px;overflow:hidden;">
							<label class="filebutton" align="center">
								&nbsp;Browse Image
								<input type="file" id="image1" name="image1" style="width:120px;height:120px;" />
							</label>
							<? #echo "<pre>";print_r($imageAttachData);?>
							<div id="response_image_1" style="width:120px;">
							<?php 
							
							if(!empty($imageAttachData) && $imageAttachData[0]['attachment_file_name'] != "" && file_exists('inspections/ncr_files/'.$imageAttachData[0]['attachment_file_name'])){?>
							<img width="100" height="90" id="photoImage1" style="margin-left:10px;margin-top:8px;" src="inspections/ncr_files/<?=$imageAttachData[0]['attachment_file_name'];?>">
							<input type="hidden" value="<?=$imageAttachData[0]['attachment_file_name'];?>" class="image_qa" name="photo[]">
							<?php }?>
							</div>
								<input type="hidden" value="<?=$imageAttachData[0]['ncr_attachment_id'];?>" name="oldImageID[]">
							</div>
						</div>
						<div class="innerDiv" align="center">
							<div style="height:120px;overflow:hidden;">
							<label class="filebutton" align="center">
								&nbsp;Browse Image
								<input type="file" id="image2" name="image2" style="width:120px;height:120px;" />
							</label>
							<div id="response_image_2" style="width:120px;">
							<?php if(!empty($imageAttachData) && $imageAttachData[1]['attachment_file_name'] != "" && file_exists('inspections/ncr_files/'.$imageAttachData[1]['attachment_file_name'])){?>
							<img width="100" height="90" id="photoImage2" style="margin-left:10px;margin-top:8px;" src="inspections/ncr_files/<?=$imageAttachData[1]['attachment_file_name'];?>">
							<input type="hidden" value="<?=$imageAttachData[1]['attachment_file_name'];?>" class="image_qa" name="photo[]">
							<?php }?>
							</div>
								<input type="hidden" value="<?=$imageAttachData[1]['ncr_attachment_id'];?>" name="oldImageID[]">
							</div>
						</div>
						<div class="innerDiv" align="center">
							<div style="height:120px;overflow:hidden;">
							<label class="filebutton" align="center">
								&nbsp;Browse Image
								<input type="file" id="image3" name="image3" style="width:120px;height:120px;" />
							</label>
							<div id="response_image_3" style="width:120px;">
							<?php if(!empty($imageAttachData) && $imageAttachData[2]['attachment_file_name'] != "" && file_exists('inspections/ncr_files/'.$imageAttachData[2]['attachment_file_name'])){?>
							<img width="100" height="90" id="photoImage3" style="margin-left:10px;margin-top:8px;" src="inspections/ncr_files/<?=$imageAttachData[2]['attachment_file_name'];?>">
							<input type="hidden" value="<?=$imageAttachData[2]['attachment_file_name'];?>" class="image_qa" name="photo[]">
							<?php }?>
							</div>
								<input type="hidden" value="<?=$imageAttachData[2]['ncr_attachment_id'];?>" name="oldImageID[]">
							</div>
						</div>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" valign="top" align="center">
						<input type="button" value="Attach supporting documents if applicable." style="margin:5px; text-align:center; width:300px;height:60px;" id="attchmentBox"  onclick="bulkUploadRegisters();"/>
						<div id="addAttachments" style="display:none;">
						<?php if(isset($attachedData) && !empty($attachedData)){
							foreach($attachedData as $key=>$val){ 
								echo '<div id="saveBox_'.$key.'"><input type="hidden" value="'.$val['attachment_file_name'].'" name="attachments[]" id="file_0"><input type="hidden" value="'.$val['attachment_title'].'" name="attachTitle[]" class="textBoxStyle" id="attachTitle[]" maxlength="50">  <textarea rows="2" cols="25" class="textareaBoxStyle" id="attachDescription[]" name="attachDescription[]">'.$val['attachment_description'].'</textarea></div>';
							}
						 }?>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" nowrap="nowrap" style="padding:0px;" align="right">
						<input type="hidden" value="edit_qa_ncr_task_detail" name="sect" id="sect" />
						<input type="hidden" value="<?=$descriptionArr[0]['sub_location_id']?>" name="locationID" id="locationID" />
						<input type="hidden" value="<?=$object->QAsubLocationsProgressMonitoringWallchart($descriptionArr[0]['sub_location_id'], ' >')?>" name="qaInspectionLocation" id="qaInspectionLocation" />
						<div style="margin-left: 314px;position: absolute"> <a href="?sect=qa_task_search&bk=Y"><img src="images/back_btn.png" style="border:none;" /></a> </div>
						<input name="submit_action" type="submit" class="submit_btn" value="save" style="background-image:url(images/save.png); border:none; width:111px;color:transparent;height:44px;font-size:0px;" />
					
								
					</td>
				</tr>
			</table>
			
			<?php if($descriptionArr[0]['is_image_require']==1){?>
				<input type="hidden" value="1" name="is_image_require" id="is_image_require" />
			<?php }else{?>	
				<input type="hidden" value="0" name="is_image_require" id="is_image_require" />
			<?php }?>
		</form>
</div>
<br clear="all" />
<br/>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
<script>
$(function(){
	//Ajax Image Upload for Image one
	var btnUpload=$('#image1');
	var status=$('#response_image_1');
	new AjaxUpload(btnUpload, {
		action: 'auto_file_upload.php?action=imageTask&uniqueID='+Math.random(),
		name: 'imageFile',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
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
			$('#removeImg1').show('fast');
						
			var src = document.getElementById("photoImage1").src;
		}
	});
	
	//Ajax Image Upload for Image Two
	var btnUpload2 = $('#image2');
	var status2 = $('#response_image_2');
	new AjaxUpload(btnUpload2, {
		action: 'auto_file_upload.php?action=imageTask2&uniqueID='+Math.random(),
		name: 'imageFile2',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
				// extension is not allowed 
				status2.text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
			status2.text('Uploading...');
			showProgress();
		},
		onComplete: function(file, response){
			
			hideProgress();
			status2.html(response);
			$('#removeImg2').show('fast');
					
			var src2 = document.getElementById("photoImage2").src;
		}
	});

	//Ajax Image Upload for Image Three
	var btnUpload3 = $('#image3');
	var status3 = $('#response_image_3');
	new AjaxUpload(btnUpload3, {
		action: 'auto_file_upload.php?action=imageTask3&uniqueID='+Math.random(),
		name: 'imageFile3',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
				// extension is not allowed 
				status3.text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
			status3.text('Uploading...');
			showProgress();
		},
		onComplete: function(file, response){
			hideProgress();
			status3.html(response);
			$('#removeImg3').show('fast');
						
			var src3 = document.getElementById("photoImage3").src;
		}
	});
});
$("#dropDown").click(function () { if ($("#discriptionHide").is(":hidden")) { $("#discriptionHide").slideDown("slow"); }else{ $("#discriptionHide").hide("slow");} });
$("li.clickableLines").click(function(){ $("#defect_desc").val(this.innerHTML); $("#discriptionHide").hide("slow");});
</script>
<style>
fieldset.permission { border:1px solid white; padding:15px; margin-top:30px; }
fieldset.permission legend { color:#FFFFFF; }
input[type=checkbox] { position: relative; cursor:pointer;}
label.label_check {cursor:pointer;}
.bulkfiles {clear: both;border: 1px solid #ccc;background-color: #E4E4E4;padding: 3px;position: relative;margin: 3px;z-index: 1;width: 98%;cursor: default;height: 70px;}
div#innerModalPopupDiv, div#innerModalPopupDiv1{color:#000000;}
.approveDrawingReg{margin-left:0px;}
ul#filePanel li{float:left;}
ul#filePanel{list-style:none; margin:0px; padding:0px;}
/*.dataHolder > div{ margin-top: 5px;position: absolute;}*/
#revisionBox > textarea {
    margin-left: 23px;
}
#revisionBox > textarea {margin-left: 95px;margin-top: -35px;}
ul.buttonHolder {list-style:none;}
ul.buttonHolder li {float:left;margin-left:10px;}
ul.buttonHolder li #disableButtonDiv{height: 50px;left: -5px;position: relative;top: -5px;width: 111px;z-index: 9999;}
.roundCorner{border-radius: 5px;}
</style>
<script type="text/javascript" src="js/modal.popup.js"></script>
<script type="text/javascript" src="js/qa_attach_multiupload.js"></script>
<script type="text/javascript">
var fileCounter = 0;
<?php if(!empty($_SESSION['ncrAttachments'])){?>
	fileCounter = <?=sizeof($_SESSION['ncrAttachments']);?>;
<?php }?>
function bulkUploadRegisters(){
	modalPopup(align, top, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_qa_attach_bulk.php?&name='+Math.random(), loadingImage, bulkRegistration);
}

var mappingDocumentArr = {};//Global Array to store select element in 
var mappedDocArr = {};//Global Array to store select element to show again selected
function bulkRegistration(){
	var config = {
		support : ",application/pdf,application/x-download,application/zip,text/plain,image/png,image/jpg,image/jpeg",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDiv",// Upload Area ID
		uploadUrl: "add_qa_attach_bulk.php?antiqueID="+Math.random()// Server side upload url
	}
	mappingDocumentArr = {};
	mappedDocArr = {};
	initBulkUploader(config);	
}

function showHistory(inspID, projectID){
	modalPopup(align, top, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'task_detail_history_table.php', loadingImage, function() {loadData(inspID, projectID);});
}
function loadData(inspID, projectID){
	$('#example_server').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "task_detail_history_ajax_table.php?inspecionID="+inspID+"&projectID="+projectID,
		"bStateSave": true,
		"bFilter": false,
		"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 1, 2, 3 ] }]
	});
}
</script>