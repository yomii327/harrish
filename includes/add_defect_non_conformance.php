<?php
$_SESSION['idp'] = $_SESSION['projIdQA'];

$owner_id = $_SESSION['ww_builder']['user_id'];
include('commanfunction.php');
if(!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])){ ?>
	<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
$object= new COMMAN_Class();
echo $_SESSION['task_id'] = $str_id = base64_decode($_GET['id']);
$q = "SELECT user_role, project_name FROM user_projects WHERE user_id = '".$owner_id."' AND project_id = '".$_SESSION['projIdQA']."'";
if($obj->db_num_rows($obj->db_query($q)) == 0){ ?>
	<script language="javascript" type="text/javascript">window.location.href="<?=ACCESS_DENIED_SCREEN?>";</script>
<?php }
$projectData = $obj->db_fetch_assoc($obj->db_query($q));
$_SESSION['userRole'] = $projectData['user_role'];

$descriptionArr = $object->selQRYMultiple('location_id, sub_location_id, task, status, comments, is_image_require', 'qa_task_monitoring', 'project_id = '.$_SESSION['projIdQA'].' AND task_id = '.$str_id.' AND is_deleted = 0');

//$issueToSelect = $object->selQRYMultiple('issue_to_name, company_name', 'inspection_issue_to', 'project_id = "'.$_SESSION['projIdQA'].'" and is_deleted=0 group by issue_to_name  order by issue_to_name');
$issueToData = $object->selQRYMultiple('issue_to_name, company_name', 'inspection_issue_to', 'project_id = "'.$_SESSION['projIdQA'].'" AND is_deleted = 0 AND issue_to_name != "" GROUP BY issue_to_name');
foreach($issueToData as $isData){
	if($isData['company_name'] != ""){
		$issueToSelect[] = $isData['issue_to_name']." (".$isData['company_name'].")";
	}else{ 
		$issueToSelect[] = $isData['issue_to_name'];
	}
}
?>
<!-- Ajax Post -->
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript" src="js/location_tree_jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="http://feather.aviary.com/js/feather.js"></script>
<style type="text/css">
input[type=checkbox] { position: relative;  cursor:pointer; opacity:1; filter:alpha(opacity=100;)}
label.label_check {cursor:pointer;}
#locationsContainer, #attachments{ overflow-y:scroll; max-height:250px; min-height:200px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius:5px; border:1px solid; margin-top:15px; width:595px; }
table.gridtable{ border-width:1px; border-color:#FFF; border-collapse: collapse; }
table.gridtable td{ border-width:1px; padding:8px; border-style:solid; border-color:#FFF; }
div#spinner{ display:none; width:100%; height:100%; position:fixed; top:0; left:0; background:url(images/loadingAnimation.gif) no-repeat center #CCCCCC; text-align:center; padding:10px; font:normal 16px Tahoma, Geneva, sans-serif; border:1px solid #666; z-index:2; overflow:auto; opacity :0.8; }
.clickableLines{ cursor:pointer; padding:5px; margin-right:5px;}
.clickableLines:hover{ background-color:#336FCE; padding:5px; margin-right:5px; color:#FFFFFF; }
#dropDown{ cursor:pointer; }
#discriptionHide{ display:none; height:150px; overflow-y:scroll; position:absolute; background:#FFFFFF; border:1px solid #0BA4FF; width:440px; border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px; z-index:1000; color:#000000; text-shadow:none; margin-left:5px;}
.issueTo{ border-radius:5px; -moz-border-radius:5px; -webkit-border-radius:5px; border:1px solid; width:120px; border-color:#FFFFFF; height:25px; }
ul.telefilms{list-style:none; cursor:pointer; font-size:15px;}
/*ul.telefilms li{height:15px;}*/
ul.telefilms li ul{list-style:none; line-height:30px;}
.jtree-arrow { padding-right:5px; font-size:15px; }
.fixedByDate{ background:#FFF; cursor:default; height:20px; -moz-border-radius:6px; -webkit-border-radius:6px; border-radius:6px; }
.innerDiv{ float:left; border:1px solid red; width:120px; height:120px; margin:0px 0px 20px 100px; }
label.filebutton{ width:120px; height:40px; overflow:hidden; position:relative; }
label input { cursor: pointer; font-size: 7px; left: -1px; line-height: 0; margin: 0; opacity: 0; padding: 0; position: absolute; top: -2px; z-index: 999; filter:alpha(opacity=0);}
</style>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
<script type='text/javascript'>
   var imageURL='';
   var oldImageURL='';
   var featherEditor = new Aviary.Feather({
       apiKey: 'YXOfUCoDcEeninMRkHb04w',
       apiVersion: 2,
       tools: 'all',
       appendTo: '',
       onSave: function(imageID, newURL) {
           var img = document.getElementById(imageID);
           img.src = newURL;
		   imageURL = newURL;
       },
       onError: function(errorObj) {
           jAlert(errorObj.message);
       },
	   onClose: function(imageID) {
			saveImage(imageURL);
       }
   });
   function launchEditor(id, src) {
	   oldImageURL = src;
       featherEditor.launch({
           image: id,
           url: src
       });
      return false;
   }
  
	function saveImage(imageURL) {
		$.ajax({
		   type: "POST",
		  // url: "http://www.constructionid.com/save_aviary.php",
		   url: "http://probuild.constructionid.com/save_aviary.php",
		   data: { url: imageURL, oldImageURL: oldImageURL},
		   success: function(msg){ 
				jAlert(msg);
		   }
		});
	};   
var align = 'center';									//Valid values; left, right, center
var top1 = 100; 											//Use an integer (in pixels)
var width = 650; 										//Use an integer (in pixels)
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

var checkListArray = new Array();//User for checklist data blank
$(document).ready(function(){
	
	var validator = $("#add_non_confirmance_inspection").validate({
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
var temp_id = 1;
$(function(){
	var btnUpload=$('#image1');
	var status=$('#response_image_1');
	new AjaxUpload(btnUpload, {
		action: 'auto_file_upload.php?action=imageOne&uniqueID='+Math.random(),
		name: 'image1',
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
			document.getElementById("editImage1").style.display = "block";
			document.getElementById("editImage1").innerHTML = '<input type="image" src="images/markup.png" value="Edit photo" onclick="return launchEditor(\'photoImage1\', \''+src+'\');"/>';
		}
	});
});
$(function(){
	var btnUpload1=$('#image2');
	var status1=$('#response_image_2');
	new AjaxUpload(btnUpload1, {
		action: 'auto_file_upload.php?action=imageTwo&uniqueID='+Math.random(),
		name: 'image2',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
				// extension is not allowed 
				status1.text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
			status1.text('Uploading...');
			showProgress();
		},
		onComplete: function(file, response1){
			hideProgress();
			status1.html(response1);
			$('#removeImg2').show();
						
			var src = document.getElementById("photoImage2").src;
			document.getElementById("editImage2").style.display = "block";
			document.getElementById("editImage2").innerHTML = '<input type="image" src="images/markup.png" value="Edit photo" onclick="return launchEditor(\'photoImage2\', \''+src+'\');"/>';			
		}
	});
});
function checkNo(evt, alertID, obj, objVal){
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if(charCode == 46){
		return true;
	}
	if (charCode > 31 && (charCode < 48 || charCode > 57)){
		return false;
	}
	if(objVal.length >= 11){
		if(objVal > 10000000){
			document.getElementById(obj).value = '10000000.00';
			jAlert("You can't enter more than 10000000 value");
			return false;
		}
	}
	return true;
}


//Default Issue to array
$(document).ready(function() {
	var issueTo_0 = '';
	var issueTo_selectd_0 = $('#autocomplete_0').val();
	$('#autocomplete_0 option').each(function(){
		if(this.value != 0){
			if(issueTo_0 == ''){
				issueTo_0 = this.value;
			}else{
				issueTo_0 += ','+this.value;
			}
		}
	});

	var issueTo_1 = '';
	var issueTo_selectd_1 = $('#autocomplete_1').val();
	$('#autocomplete_1 option').each(function(){
		if(this.value != 0){
			if(issueTo_1 == ''){
				issueTo_1 = this.value;
			}else{
				issueTo_1 += ','+this.value;
			}
		}
	});

	var issueTo_2 = '';
	var issueTo_selectd_2 = $('#autocomplete_2').val();
	$('#autocomplete_2 option').each(function(){
		if(this.value != 0){
			if(issueTo_2 == ''){
				issueTo_2 = this.value;
			}else{
				issueTo_2 += ','+this.value;
			}
		}
	});
	
	
	


});


</script>
<div class="content_hd" style="background-image:url(images/add_nonconf.png);margin-top:0px\9;"></div>
<br clear="all" />
<?php if(isset($_SESSION['inspection_added'])) { ?>
<div id="errorHolder" style="margin-left: 40px;margin-bottom: 20px;">
	<div class="success_r" style="height:35px;width:405px;"><p><?=$_SESSION['inspection_added'];?></p></div>
</div>
<?php unset($_SESSION['inspection_added']); } ?>
<form action="ajax_reply.php" method="post" enctype="multipart/form-data" name="add_non_confirmance_inspection" id="add_non_confirmance_inspection" onsubmit="return checkDuplicateIssue(); && checkImageValid();">
	<div class="signin_form1" style="margin-top:-5px;margin-left:15px;"> <font style="float:left;margin-left:10px;" size="+1">Project :
		<?=stripslashes($projectData['project_name'])?>
		</font>
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
				<td valign="top"><? if($_SESSION['userRole'] != 'All Defect'){?>
					<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
						<option value="">Select</option>
						<option value="<?=$_SESSION['userRole']?>" selected="selected" ><?=$_SESSION['userRole']?></option>
					</select>
					<?php }else{ ?>
					<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
						<option value="GPCL" selected="selected" >GPCL</option>
						<option value="Architect" >Architect</option>
						<option value="Structural Engineer" >Structural Engineer</option>
						<option value="Services Engineer" >Services Engineer</option>
						<option value="Accreditation" >Accreditation</option>
						<option value="Consultant">Consultant</option>
						<option value="Independent Reviewer">Independent Reviewer</option>
						<option value="Stakeholders">Stakeholders</option>
						<option value="Sub Contractor">Sub Contractor</option>
					</select>
					<?php } ?>
				</td>
				<td colspan="3" align="left" valign="top">
					<textarea id="defect_desc" name="defect_desc" class="textareaBoxStyle" style="margin-top:0px;width:340px;"><?=$descriptionArr[0]['task']?></textarea>
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
					<?php } ?>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<div id="attachments" style="overflow:hidden;">
						<!--span style="margin-left:15px;float:left;">Attachment</span><br /-->
						<table width="100%" border="0" align="center">
							<tr>
								<td width="28%" align="left" valign="middle">Attachment</td>
								<td width="23%" align="left" valign="middle"><div id="editImage1" style=" margin-left:15px;" >
										<div id='injection_site'></div>
									</div></td>
								<td width="49%" align="left" valign="middle"><div id="editDrawing" style=" margin-left:105px;" >
										<div id='injection_site'></div>
									</div></td>
							</tr>
						</table>
						<div class="innerDiv"  style="margin-left:180px;" align="center" >
							<div style="height:120px;overflow:hidden;">
								<label class="filebutton" align="center"> &nbsp;Browse Image 1
								<input type="file" id="image1" name="image1" style="width:120px;height:120px;" />
								</label>
								<div id="response_image_1" style="width:120px;">&nbsp;</div>
								
							</div>
							
							<img id="removeImg1" src="images/remove_img_btn.png" style="margin-top:10px;cursor:pointer;display:none;" onclick="removeImages('response_image_1', this.id);" /> </div>
						<div class="innerDiv" align="center" style="margin: 0px 0px 20px 100px;">
							<div style="height:120px;overflow:hidden;" onclick="showPhotoLibrary(1, 1);">
								<label class="filebutton" align="center"> Browse Drawing
								<input type="hidden" id="drawing2" name="drawing2" value="" style="width:120px;height:120px; display:none;" />
								</label>
								<div id="response_drawing" style="width:120px;">&nbsp;</div>
							</div>
							<img id="removeImg3" src="images/remove_img_btn.png" style="margin-top:10px;cursor:pointer;display:none;" onclick="removeImages('response_drawing', this.id);" /> </div>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="error-edit-profile_big" style="width:250px;display:none;"  id="imageError">Please select atleast one image or drawing</div>
				</td>
			</tr>
			<tr>
				<td colspan="4" style="padding:5px;"><table width="75%" border="0" cellspacing="0" cellpadding="0" >
						<tr>
							<td width="331" valign="top" style="padding:5px;">Issued To <span class="req">*</span></td>
							<td width="243" valign="top" style="padding:5px;">Fix By Date <span class="req">*</span></td>
							<td width="184" valign="top" style="padding:5px;">Cost Attribute</td>
							<td width="213" valign="top" style="padding:5px;" align="center">Status</td>
						</tr>
						
						<tr>
							<td style="text-shadow:none;padding:5px;width:150px;"><div style="width:160px;"> <img style="cursor:pointer;" onclick="AddItem();" src="images/inspectin_add.png" align="absmiddle" />
									<select name="issueTo[]" type="text" id="issueTo_0" class="issueTo" onchange="checkIssueTo(this.value, 0);">
										<?php foreach($issueToSelect as $KEy=>$issueToName){?>
										<option value="<?php echo $cValue = trim(stripslashes($issueToName))?>"<?php if('NA' == $cValue){ echo 'selected="selected"'; }?>>
										<?=$cValue?>
										</option>
										<?php }?>
									</select>
								</div></td>
							<td style="text-shadow:none;padding:5px;">
								<input name="fixedByDate[]" id="fixedByDate_0" class="fixedByDate" readonly style="width:100px" />
								<div class="error-edit-profile" style="width:100px;display:none;"  id="fixedByError0">Please fill fixed by date</div>
							</td>
							<td style="text-shadow:none;padding:5px;">
								<select name="costAttribute[]" id="costAttribute_0" class="issueTo" style="width:110px">
									<option value="None"<?php if(isset($_SESSION['qa']['costAttribute'])){ if($_SESSION['qa']['costAttribute'] == 'None'){ echo 'selected="selected"'; } }?>>None</option>
									<option value="Backcharge"<?php if(isset($_SESSION['qa']['costAttribute'])){ if($_SESSION['qa']['costAttribute'] == 'Backcharge'){ echo 'selected="selected"'; } }?>>Backcharge</option>
									<option value="Variation"<?php if(isset($_SESSION['qa']['costAttribute'])){ if($_SESSION['qa']['costAttribute'] == 'Variation'){ echo 'selected="selected"'; } }?>>Variation</option>
								</select>
							</td>
							<td style="text-shadow:none;padding:5px;" align="center">
								<select name="status[]" id="status_0" class="issueTo"style="width:100px">
									<option value="Failed" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Failed'){ echo 'selected="selected"'; } }?>>Failed</option>
									<option value="Pending" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Pending'){ echo 'selected="selected"'; } }?>>Pending</option>
									<option value="Fixed" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Fixed'){ echo 'selected="selected"'; } }?>>Fixed</option>
									<option value="Passed" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Passed'){ echo 'selected="selected"'; } }?>>Passed</option>
									<!--option value="Draft" value="Failed"  onclick="return checkListCheck();" <?php // if(isset($_SESSION['qc']['status'])){ if($_SESSION['qc']['status'] == 'Draft'){?> selected="selected" <?php //  } } ?> >Draft</option-->
								</select>
							</td>
						</tr>
						<tr id="hide_1" style="display:none;">
							<td style="text-shadow:none;padding:5px;width:160px;">
							<img style="cursor:pointer;" onclick="removeElement('hide_1');" src="images/inspectin_delete.png" align="absmiddle" />
								<select name="issueTo[]" type="text" id="issueTo_1" class="issueTo" onchange="checkIssueTo(this.value, 1);">
									<?php foreach($issueToSelect as $KEy=>$issueToName){?>
									<option value="<?php echo $cValue = trim(stripslashes($issueToName))?>" <?php if('NA' == $cValue){ echo 'selected="selected"'; }?>>
									<?=$cValue?>
									</option>
									<?php }?>
								</select>
							</td>
							<td style="text-shadow:none;padding:5px;">
								<input name="fixedByDate[]" id="fixedByDate_1" class="fixedByDate" readonly  style="width:100px"/>
								<div class="error-edit-profile" style="width:100px;display:none;"  id="fixedByError1">Please fill fixed by date</div>
							</td>
							<td style="text-shadow:none;padding:5px;">
								<select name="costAttribute[]" id="costAttribute_1" class="issueTo" style="width:110px">
									<option value="None"<?php if(isset($_SESSION['qa']['costAttribute'])){ if($_SESSION['qa']['costAttribute'] == 'None'){ echo 'selected="selected"'; } }?>>None</option>
									<option value="Backcharge"<?php if(isset($_SESSION['qa']['costAttribute'])){ if($_SESSION['qa']['costAttribute'] == 'Backcharge'){ echo 'selected="selected"'; } }?>>Backcharge</option>
									<option value="Variation"<?php if(isset($_SESSION['qa']['costAttribute'])){ if($_SESSION['qa']['costAttribute'] == 'Variation'){ echo 'selected="selected"'; } }?>>Variation</option>
								</select>
							</td>
							<td style="text-shadow:none;padding:5px;" align="center">
								<select name="status[]" id="status_1" class="issueTo" style="width:100px">
									<option value="Failed" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Failed'){ echo 'selected="selected"'; } }?>>Failed</option>
									<option value="Pending" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Pending'){ echo 'selected="selected"'; } }?>>Pending</option>
									<option value="Fixed" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Fixed'){ echo 'selected="selected"'; } }?>>Fixed</option>
									<option value="Passed" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Passed'){ echo 'selected="selected"'; } }?>>Passed</option>
								</select>
							</td>
						</tr>
						<tr id="hide_2" style="display:none;">
							<td style="text-shadow:none;padding:5px;width:160px;">
								<img style="cursor:pointer;" onclick="removeElement('hide_2');" src="images/inspectin_delete.png" align="absmiddle" />
								<select name="issueTo[]" type="text" id="issueTo_2" class="issueTo" onchange="checkIssueTo(this.value, 2);">
									<?php foreach($issueToSelect as $KEy=>$issueToName){?>
									<option value="<?php echo $cValue = trim(stripslashes($issueToName))?>" <?php if('NA' == $cValue){ echo 'selected="selected"'; }?>>
									<?=$cValue?>
									</option>
									<?php }?>
								</select>
							</td>
							<td style="text-shadow:none;padding:5px;">
								<input name="fixedByDate[]" id="fixedByDate_2" class="fixedByDate" readonly  style="width:100px;" />
								<div class="error-edit-profile" style="width:100px;display:none;"  id="fixedByError2">Please fill fixed by date</div>
							</td>
							<td style="text-shadow:none;padding:5px;">
								<select name="costAttribute[]" id="costAttribute_2" class="issueTo" style="width:110px;">
									<option value="None"<?php if(isset($_SESSION['qa']['costAttribute'])){ if($_SESSION['qa']['costAttribute'] == 'None'){ echo 'selected="selected"'; } }?>>None</option>
									<option value="Backcharge"<?php if(isset($_SESSION['qa']['costAttribute'])){ if($_SESSION['qa']['costAttribute'] == 'Backcharge'){ echo 'selected="selected"'; } }?> >Backcharge</option>
									<option value="Variation"<?php if(isset($_SESSION['qa']['costAttribute'])){ if($_SESSION['qa']['costAttribute'] == 'Variation'){ echo 'selected="selected"'; } }?>>Variation</option>
								</select>
							</td>
							<td style="text-shadow:none;padding:5px;" align="center">
								<select name="status[]" id="status_2" class="issueTo" style="width:100px;">
									<option value="Failed" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Failed'){ echo 'selected="selected"'; } }?>>Failed</option>
									<option value="Pending" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Pending'){ echo 'selected="selected"'; } }?>>Pending</option>
									<option value="Fixed" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Fixed'){ echo 'selected="selected"'; } }?>>Fixed</option>
									<option value="Passed" <?php if(isset($_SESSION['qa']['status'])){ if($_SESSION['qa']['status'] == 'Passed'){ echo 'selected="selected"'; } }?>>Passed</option>
								</select>
							</td>
						</tr>
					</table></td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
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
				<td nowrap="nowrap" style="padding:0px;">&nbsp;</td>
				<td nowrap="nowrap" style="padding:0px;" align="left">
					<input type="hidden" value="add_non_confirmance_inspection" name="sect" id="sect" />
					<input type="hidden" value="<?=$descriptionArr[0]['comments']?>" name="task_comment" id="task_comment"  />
					<input type="hidden" value="<?=$descriptionArr[0]['status']?>" name="task_status" id="task_status"  />
					<input type="hidden" value="<?=$descriptionArr[0]['sub_location_id']?>" name="locationID" id="locationID" />
					<input type="hidden" value="<?=$object->QAsubLocationsProgressMonitoringWallchart($descriptionArr[0]['sub_location_id'], ' > ')?>" name="qaInspectionLocation" id="qaInspectionLocation" />
					<a href="?sect=qa_task_search&bk=Y"><img src="images/back_btn.png" style="border:none; width:111px;" /></a>
				</td>
				<td nowrap="nowrap" style="padding:0px;"><input name="submit_action" type="submit" class="submit_btn" value="save" style="background-image:url(images/save.png); border:none; width:111px;color:transparent;height:44px;font-size:0px;" />
				</td>
			</tr>
		</table>
		<?php unset($_SESSION['post_array']);?>
		<?php if($descriptionArr[0]['is_image_require']==1){?>
				<input type="hidden" value="1" name="is_image_require" id="is_image_require" />
			<?php }else{?>	
				<input type="hidden" value="0" name="is_image_require" id="is_image_require" />
			<?php }?>
	</div>
</form>
<style>
#locationsContainer, #attachments{ overflow-y:scroll; max-height:250px; min-height:200px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius:5px; border:1px solid; margin-top:15px; width:595px; }
.innerDiv{ float:left; border:1px solid red; width:120px; height:120px; margin:0px 0px 20px 0px; }
label.filebutton{ width:120px; height:40px; overflow:hidden; position:relative; }
label input { cursor: pointer; font-size: 7px; left: -1px; line-height: 0; margin: 0; opacity: 0; padding: 0; position: absolute; top: -2px; z-index: 999; filter:alpha(opacity=0);}
#image1, #image2{ z-index:1;}
#locationsContainer{ overflow-y: scroll; max-height: 200px; min-height: 150px; border-radius:5px; -moz-border-radius: 5px;  -webkit-border-radius: 5px; border:1px solid; margin-top:15px; width:420px; }
table.gridtable { border-width: 1px; border-color: #FFF; border-collapse: collapse; }
table.gridtable td { border-width: 1px; padding: 8px; border-style: solid; border-color: #FFF; }
.clickableLines{ cursor:pointer; padding:5px; margin-right:5px;}
.clickableLines:hover{ background-color:#336FCE;padding:5px; margin-right:5px;color:#FFFFFF;}
#dropDown{ cursor:pointer; }
#discriptionHide{ display:none; height: 150px; overflow-y: scroll; position:absolute; background:#FFFFFF; border:1px solid #0BA4FF; width:300px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; z-index:1000; color:#000000; text-shadow:none; }
.issueTo{ border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; border:1px solid; width:150px; border-color:#FFFFFF; height:25px;}
ul.telefilms{list-style:none; cursor:pointer; font-size:15px;}
ul.telefilms li ul{list-style:none; line-height:30px;}
.jtree-arrow { padding-right: 5px; font-size: 15px; }
.fixedByDate{ background:#FFF; cursor:default; height:20px; -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; }
table#example_server{ color:#000000; }
#attachList{ height: 200px; overflow: auto; padding: 5px; }
#attachList, #innerDiv div.fileHolder{ background: none repeat scroll 0 0 #F3F3F3; border: 1px outset #333333; float: left; margin: 3px; padding: 5px; width: 166px; }
#attachList, #innerDiv span.fileHolder{ color: #990000; cursor: pointer; float: right; }

</style>

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
<!--<div id="spinner" style="z-index:100000"><div style="margin-top:240px;color:#000000;font-weight:bold;">Please Wait....<br/>This may take several minutes.</div></div>-->
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
</script>
<script type="text/javascript" src="js/jquery.tree.js"></script>
<script type="text/javascript" src="js/jquery.contextmenu.r2.js"></script>
<script>
var previousLocId = '';
var elementCount = 0;
var addedRow = new Array(); 
$(document).ready(function(){
	$('ul.telefilms').tree({default_expanded_paths_string : '0/0/0,0/0/2,0/2/4'}); 
<?php if($_SESSION['qa']['location'] != ''){?>
	showProgress();	
	var locationID = <?php echo $_SESSION['qa']['subLocation'] == '' ? $_SESSION['qa']['location']  : $_SESSION['qa']['subLocation']?>;
	<?php if($_SESSION['qa']['subSubLocation']){?>
	var locationID = <?php echo $_SESSION['qa']['subSubLocation'];?>;
	<?php }?>
	$("#location").val(locationID);
	$.ajax({
		url: "reloadLocationExpand.php",
		type: "POST",
		data: "locationId="+locationID+"&uniqueId="+Math.random(),
		success: function (newTree) {
			hideProgress();
			document.getElementById('location_exists').innerHTML = newTree;
			$('#locationTree').val(newTree);
		}
	});
<?php }?>
	$('span.demo1').contextMenu('myMenu2', {
		bindings: {
			'select': function(t) {
				if(previousLocId != ''){
					$(previousLocId).css({ 'font-weight' :'normal', 'font-style':'normal', 'text-decoration':'none' });
				}
				$(t).css({ 'font-weight' :'bold', 'font-style':'italic', 'text-decoration':'underline' });
				previousLocId = t;
				$("#location").val(t.id);
				$("#locationChecklist").val(document.getElementById(t.id).innerHTML);
				if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
				showProgress();
				params = "locationId="+t.id+"&uniqueId="+Math.random();
				xmlhttp.open("POST", "reloadLocationExpand.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.setRequestHeader("Content-length", params.length);
				xmlhttp.setRequestHeader("Connection", "close");
				xmlhttp.onreadystatechange=function(){
					if (xmlhttp.readyState==4 && xmlhttp.status==200){
						hideProgress();
						var newTree = xmlhttp.responseText;
						$('#location_exists').html(newTree);
						var locArr = newTree.split(" > ");
						var locCount = locArr.length;
						if(locCount > 2){
							var locationName = locArr[locCount - 1];
							var parenetLocationName = locArr[locCount - 2];
						}else{
							var locationName = locArr[locCount - 1];
							var parenetLocationName = '';
						}	
						$('#locationTree').val(newTree);
						taggingIssueTo(locationName, parenetLocationName);
						taggingStandardDefect(locationName, parenetLocationName);
					}
				}
				xmlhttp.send(params);
			}
		}
	});
});
//Remove issue to row
function removeElement(removeID){
	var r = jConfirm('Do you want to delete Sub Contractor ?', null, function(r){
		if (r==true){
			--elementCount;
			document.getElementById(removeID).style.display = 'none';
			var idarr = removeID.split('_');
			$('#issueTo_'+idarr[1]).show();
			$('#issueTo_'+idarr[1]).val('NA');
			$('#otherIssueTo'+idarr[1]).hide();
			addedRow.pop();
		}
	});
}
//Add issue to row
function AddItem() {
	if(addedRow.length < 2){
		addedRow.push(++elementCount);
		if(document.getElementById('hide_'+elementCount).style.display == 'none'){
			document.getElementById('hide_'+elementCount).style.display = 'table-row';
		}else{
			document.getElementById('hide_1').style.display = 'table-row';
		}
	}else{
		jAlert("You can't add more than 3 Sub Contractor !");
	}
}
window.onload = function(){
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
};
function goBack(){
	var location = document.getElementById('location').value;
	var raisedBy = document.getElementById('raisedBy').value;
	var defect_desc = document.getElementById('defect_desc').value;
	if(location == '' && raisedBy == '' && defect_desc == ''){
		window.location.href="?sect=i_defect&bk=Y";
	}else{
		var r = jConfirm('Do you want to go back ?', null, function(r){
			if (r==true){
				window.location.href="?sect=i_defect&bk=Y";
			}
		});
	}
}
function checkIssueTo(checkValue, checkId){
	if(checkId == 0){
		if(document.getElementById('issueTo_1').value == checkValue || document.getElementById('issueTo_2').value == checkValue){
			if(checkValue != 'NA'){
				jAlert(checkValue+' Already Selected !');
				document.getElementById('issueTo_0').value = 'NA';
			}
		}
	}
	if(checkId == 1){
		if(document.getElementById('issueTo_0').value == checkValue || document.getElementById('issueTo_2').value == checkValue){
			if(checkValue != 'NA'){
				jAlert(checkValue+' Already Selected !');
				document.getElementById('issueTo_1').value = 'NA';
			}
		}
	}if(checkId == 2){
		if(document.getElementById('issueTo_0').value == checkValue || document.getElementById('issueTo_1').value == checkValue){
			if(checkValue != 'NA'){
				jAlert(checkValue+' Already Selected !');
				document.getElementById('issueTo_2').value = 'NA';
			}
		}
	}
}
function removeImages(divId, removeButtonId){
	var r = jConfirm('Are you sure, you want to delete Image?', null, function(r){
		if (r==true){
			var imgDiv=document.getElementById(divId);
			var imgSrc = imgDiv.childNodes[0].src;	
			showProgress();	
			$.ajax({
				url: "remove_uploaded_file.php",
				type: "POST",
				data: "imageName="+imgSrc,
				success: function (res) {
					hideProgress();
					imgDiv.innerHTML = '';		
					document.getElementById(removeButtonId).style.display = 'none';
					if(removeButtonId=="removeImg1"){
						document.getElementById("editImage1").style.display = "none";
						document.getElementById("editImage1").innerHTML = '';
					}
					if(removeButtonId=="removeImg2"){
						document.getElementById("editImage2").style.display = "none";
						document.getElementById("editImage2").innerHTML = '';
					}
					if(removeButtonId=="removeImg3"){
						document.getElementById("editDrawing").style.display = "none";
						document.getElementById("editDrawing").innerHTML = '';
					}
				}
			});
		}else{
			return false;
		}
	});
}
$(document).ready(function(){
	<?php if($_SESSION['qa']['subLocation'] != ''){?>
		$('#li_<?php echo $_SESSION['qa']['location'];?>').children("ul").show('slow');
	<?php }?>
	<?php if($_SESSION['qa']['subLocation'] != ''){?>
		$('#li_<?php echo $_SESSION['qa']['location'];?>').children("ul").show('slow');
		$('#li_<?php echo $_SESSION['qa']['subLocation'];?>').children("ul").show('slow');
	<?php }?>
});
$("#dropDown").click(function () {
	if ($("#discriptionHide").is(":hidden")) { $("#discriptionHide").slideDown("slow"); }else{ $("#discriptionHide").hide("slow");}
});
$(".clickableLines").live('click', (function(){$("#defect_desc").val(this.innerHTML); $("#discriptionHide").hide("slow");}));
function taggingIssueTo(locationName, parenetLocationName){
	if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	params = "LocationName="+locationName+"&parentLocationName="+parenetLocationName+"&&projectID=<?=$_SESSION['projIdQA'];?>&uniqueId="+Math.random();
	xmlhttp.open("POST", "tagedIssuetTo.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText != ''){
				var resString = xmlhttp.responseText;
				var resSplitResult = resString.split("@@@");
				for(var g=0; g<=2; g++){
					for(i = 0; i < resSplitResult.length; i++){
						var exists = false;
						$('#autocomplete_'+g+' option').each(function(){
							if (this.value == resSplitResult[i]) {
								exists = true;
							}
						});
						if(exists){}else{
							document.getElementById('autocomplete_'+g).innerHTML += '<option value="'+resSplitResult[i]+'">'+resSplitResult[i]+'</option>>'; 
						}
					}
				}
			}
		}
	}
	xmlhttp.send(params);
}
function taggingStandardDefect(locationName, parenetLocationName){
	if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	params = "LocationName="+locationName+"&parentLocationName="+parenetLocationName+"&projectID=<?=$_SESSION['projIdQA'];?>&uniqueId="+Math.random();
	xmlhttp.open("POST", "tagedStandardDefect.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText != ''){
				var resString = xmlhttp.responseText;
				$('#standardDefect').html(resString);
			}
		}
	}
	xmlhttp.send(params);
}
</script>
<?php unset($_SESSION['checkList']); ?>
<script type="text/javascript">
<?php if($_SERVER['HTTP_HOST']=="localhost"){?>
	var hostURL = "http://localhost/constructionid/";
<?php }else{ ?>
	//var hostURL = "http://www.constructionid.com/";
	var hostURL = "http://probuild.constructionid.com";
	
<?php }?>
function selectImages(divId, imgID, removeButtonId){
	var imgDiv = document.getElementById(divId);
	var imgSrc = imgDiv.src;	
	showProgress();	
	$.ajax({
		url: "copy_drawing_to_add_defect_file.php",
		type: "POST",
		data: "imageData="+imgSrc+"&imageID="+imgID,
		success: function (res) {
			hideProgress();
			document.getElementById("drawing2").value = res;
			document.getElementById("response_drawing").innerHTML = '<img src="inspections/drawing/'+res+'" width="100" height="90" style="margin-left:10px;margin-top:8px;" id="drawImage1"  /><input type="hidden" value="'+res+'" name="drawing">';
			var src = document.getElementById("drawImage1").src;
			document.getElementById("editDrawing").style.display = "block";
			document.getElementById("editDrawing").innerHTML = '<input type="image" src="images/markup.png" value="Edit photo" onclick="return launchEditor(\'drawImage1\', \''+src+'\');"/>';
			$('#removeImg3').show();
			closePopup(300);
			//imgDiv.style.display = 'none';
		}
	});	
}
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

var spinnerVisible = false;
function showProgress() {if (!spinnerVisible) {$("div#spinner").fadeIn("fast");spinnerVisible = true;}};
function hideProgress() {if (spinnerVisible) {var spinner = $("div#spinner");spinner.stop();spinner.fadeOut("fast");spinnerVisible = false;}};
function showPhotoLibrary(pID, imgID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_defect_drawing_management_show.php?pID='+pID+'&imageID='+imgID, loadingImage, addNewDrawing);
	document.getElementById("outerModalPopupDiv").style.top="550px";
}
function addNewDrawing(){
	var btnUpload2=$('#drawing');
	var status2=$('#response_drawing');
	new AjaxUpload(btnUpload2, {
		action: 'auto_file_upload.php?action=drawing&uniqueID='+Math.random(),
		name: 'drawing',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
				// extension is not allowed 
				status2.text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
			status2.text('Uploading...');
			showProgress();
		},
		onComplete: function(file, response2){
			hideProgress();
			closePopup(300);
			status2.html(response2);
			$('#removeImg3').show();
			
			var src = document.getElementById("drawImage1").src;
			document.getElementById("editDrawing").style.display = "block";
			document.getElementById("editDrawing").innerHTML = '<input type="image" src="images/markup.png" value="Edit photo" onclick="return launchEditor(\'drawImage1\', \''+src+'\');"/>';
			
		}
	});
}
function showPhoto(pID, imgID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_defect_display_draw_image.php?pID='+pID+'&imageID='+imgID, loadingImage);
	document.getElementById("outerModalPopupDiv").style.top="200px";
}
function folatingValCheck(evt, alertID, obj, objVal){
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if(charCode == 46){
		return true;
	}
	if (charCode > 31 && (charCode < 48 || charCode > 57)){
		return false;
	}
	return true;
}
function checkDuplicateIssue(){
	var status = true;
	var checkValue = $('#issueTo_0').val();
	if(checkValue == 'NA' && $('#hide_1').is(':visible') && $('#hide_2').is(':visible')){
		if($('#issueTo_1').val() == checkValue || $('#issueTo_2').val() == checkValue){
			jAlert("Sorry! Sub Contractor Name can't be same.\nPlease choose another name.");
			return false;
		}
	}else{
		if((checkValue == 'NA' && $('#hide_1').is(':visible')) || (checkValue == 'NA' && $('#hide_2').is(':visible'))){
			if($('#issueTo_1').val() == checkValue){
				jAlert("Sorry! Sub Contractor Name can't be same.\nPlease choose another name.");
				return false;
			}
		}
	}
	
	var checkValue = $('#issueTo_1').val();
	if(checkValue == 'NA' && $('#hide_1').is(':visible') && $('#hide_2').is(':visible')){
		if($('#issueTo_0').val() == checkValue || $('#issueTo_2').val() == checkValue){
			jAlert("Sorry! Sub Contractor Name can't be same.\nPlease choose another name.");
			return false;
		}
	}else{
		if((checkValue == 'NA' && $('#hide_0').is(':visible')) || (checkValue == 'NA' && $('#hide_2').is(':visible'))){
			if($('#issueTo_1').val() == checkValue){
				jAlert("Sorry! Sub Contractor Name can't be same.\nPlease choose another name.");
				return false;
			}
		}
	}
	
	var checkValue = $('#issueTo_2').val();
	if(checkValue == 'NA' && $('#hide_0').is(':visible') && $('#hide_1').is(':visible')){
		if($('#issueTo_0').val() == checkValue || $('#issueTo_1').val() == checkValue){
			jAlert("Sorry! Sub Contractor Name can't be same.\nPlease choose another name.");
			return false;
		}
	}else{
		if((checkValue == 'NA' && $('#hide_0').is(':visible')) || (checkValue == 'NA' && $('#hide_1').is(':visible'))){
			if($('#issueTo_1').val() == checkValue){
				jAlert("Sorry! Sub Contractor Name can't be same.\nPlease choose another name.");
				return false;
			}
		}
	}
	return status;
}

function checkImageValid(){
	var img_valid = $('#is_image_require').val();
	//var img_valid = 1;
	//alert(img_valid);return false;
	var imgCount = 0;
	$( ".image_qa" ).each(function( index ) {
		if($(this).val()!=''){
			imgCount++;
		}
	});
	if(img_valid==1){
		if(imgCount>=1){
			//$('#add_qa_ncr_task_detail').submit();	
			document.add_non_confirmance_inspection.submit();
		}else{
			//jAlert('You must select atleast one image !');
			$('#imageError').show();
			return false;
		}	
	}else{
		//$('#add_qa_ncr_task_detail').submit();
		document.add_non_confirmance_inspection.submit();
	}
	
}
//Validaion for blank raised by name..
$('#add_non_confirmance_inspection').submit(function(){
	var flagsubcheck = false;
	for(var g=0; g<=addedRow.length; g++){
		if($('#fixedByDate_'+g).val() == ''){
			$('#fixedByError'+g).show();
			flagsubcheck = true;
		}else{
			$('#fixedByError'+g).hide();
		}
	}
	if(flagsubcheck){
		return false;
	}
});
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

</script>