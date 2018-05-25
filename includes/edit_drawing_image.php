<script type="text/javascript" src="js/thickbox.js"></script>
<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?#=HOME_SCREEN?>";
</script>
<?php }
session_start();
#print_r($_SESSION);
if(isset($_SESSION['err_msg'])){
	$err_msg = $_SESSION['err_msg'];
	unset($_SESSION['err_msg']);
}

$builder_id=$_SESSION['ww_builder_id'];

include_once("includes/commanfunction.php");

$drawingID = base64_decode($_GET['imgID']);

$obj = new COMMAN_Class(); 

if(isset($_POST['drawingID'])){
	if(isset($_POST['imageDelete'])){
		if(trim($_POST['drawingTitle']) != ''){
			$drawImageTitle = trim(addslashes($_POST['drawingTitle']));
			$drawDescription = trim(addslashes($_POST['drawingDescription']));
			$drawTags = trim(addslashes($_POST['drawingTags']));
			$drawTags = trim($drawTags, ";");
			$drawTags = implode(";", array_map('trim', explode(";", $drawTags)));
			if($drawTags != ""){
				$drawTags = trim($drawTags) . ";";
			}
			$imgArray = explode('/', $_POST['imageDelete']);
			$imgDataCount = sizeof($imgArray);
			unlink('./project_drawings/'.$_SESSION['idp'].'/'.$imgArray[$imgDataCount-2].'/'.$imgArray[$imgDataCount-1]);
			$fileOName = str_replace('thumb_', '', $imgArray[$imgDataCount-1]);
			unlink('./project_drawings/'.$_SESSION['idp'].'/'.$fileOName);
			
			$inssertQRY = "UPDATE draw_mgmt_images SET
							project_id = '".$_SESSION['idp']."',
							draw_mgmt_images_title = '".$drawImageTitle."',
							draw_mgmt_images_description = '".$drawDescription."',
							draw_mgmt_images_tags = '".$drawTags."',
							last_modified_by = '".$_SESSION['ww_builder_id']."',
							last_modified_date = NOW()
						WHERE
							draw_mgmt_images_id = '".$_POST['drawingID']."'";
			mysql_query($inssertQRY);			
		}else{
			$err_msg = 'Please fill drawing title';		
		}
		if($err_msg == ''){
			$_SESSION['add_inspector_success'] = 'Drawing image data updated successfully !';
			header('location:?sect=drawing_management');
		}
		if(isset($_FILES['drawingImage']['name']) && !empty($_FILES['drawingImage']['name'])){
			$filename = $_FILES['drawingImage']['name']; // Drawing File Name
			$file_ext = end(explode('.', $filename));
			$fil_ext_array = array('jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG');
			if(in_array($file_ext, $fil_ext_array)){
				if(trim($_POST['drawingTitle']) != ''){
					$drawImageTitle = trim(addslashes($_POST['drawingTitle']));
					$drawDescription = trim(addslashes($_POST['drawingDescription']));
					$drawTags = trim(addslashes($_POST['drawingTags']));
					$drawTags = trim($drawTags, ";");
					$drawTags = implode(";", array_map('trim', explode(";", $drawTags)));

					if($drawTags != ""){
						$drawTags = $drawTags . ";";
					}
					$inssertQRY = "UPDATE draw_mgmt_images SET
									project_id = '".$_SESSION['idp']."',
									draw_mgmt_images_title = '".$drawImageTitle."',
									draw_mgmt_images_description = '".$drawDescription."',
									draw_mgmt_images_tags = '".$drawTags."',
									last_modified_by = '".$_SESSION['ww_builder_id']."',
									last_modified_date = NOW()
								WHERE
									draw_mgmt_images_id = '".$_POST['drawingID']."'";
					mysql_query($inssertQRY);			
					$imageid = $_POST['drawingID'];
					$imageName = $imageid.'.'.$file_ext;
					$imageThumbName = 'thumb_'.$imageid.'.'.$file_ext;
					if(!is_dir('./project_drawings/'.$_SESSION['idp'])){
						@mkdir('./project_drawings/'.$_SESSION['idp'], 0777);
					}
					$obj->resizeImages($_FILES['drawingImage']['tmp_name'], 1600, 1600, './project_drawings/'.$_SESSION['idp'].'/'.$imageName);
#					move_uploaded_file($_FILES['drawingImage']['tmp_name'], './project_drawings/'.$_SESSION['idp'].'/'.$imageName);
					
					if(!is_dir('./project_drawings/'.$_SESSION['idp'].'/thumbnail')){
						@mkdir('./project_drawings/'.$_SESSION['idp'].'/thumbnail', 0777);
					}
					$obj->resizeImages('./project_drawings/'.$_SESSION['idp'].'/'.$imageName, 150, 150, './project_drawings/'.$_SESSION['idp'].'/thumbnail/'.$imageThumbName);
					$updateQRY = "UPDATE draw_mgmt_images SET
									draw_mgmt_images_name = '".$imageName."',
									draw_mgmt_images_thumbnail = '".$imageThumbName."',
									last_modified_date = NOW(),
									last_modified_by = '".$_SESSION['ww_builder_id']."'
								WHERE draw_mgmt_images_id = '".$imageid."'";
		
					mysql_query($updateQRY);
					unset($_SESSION['isRemoveDraw']);
				}else{
					$err_msg = 'Please fill drawing title';		
				}
			}else{
				$err_msg = 'Please select either ".jpg" or ".png" file';
			}
		}
	}else{
		if(isset($_FILES['drawingImage']['name']) && !empty($_FILES['drawingImage']['name'])){
			$filename = $_FILES['drawingImage']['name']; // Drawing File Name
			$file_ext = end(explode('.', $filename));
			$fil_ext_array = array('jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG');
			if(in_array($file_ext, $fil_ext_array)){
				if(trim($_POST['drawingTitle']) != ''){
					$drawImageTitle = trim(addslashes($_POST['drawingTitle']));
					$drawDescription = trim(addslashes($_POST['drawingDescription']));
					$drawTags = trim(addslashes($_POST['drawingTags']));
					$drawTags = trim($drawTags, ";");
					$drawTags = implode(";", array_map('trim', explode(";", $drawTags)));

					if($drawTags != ""){
						$drawTags = $drawTags . ";";
					}
					$inssertQRY = "UPDATE draw_mgmt_images SET
									project_id = '".$_SESSION['idp']."',
									draw_mgmt_images_title = '".$drawImageTitle."',
									draw_mgmt_images_description = '".$drawDescription."',
									draw_mgmt_images_tags = '".$drawTags."',
									last_modified_by = '".$_SESSION['ww_builder_id']."',
									last_modified_date = NOW()
								WHERE
									draw_mgmt_images_id = '".$_POST['drawingID']."'";
					mysql_query($inssertQRY);			
					$imageid = $_POST['drawingID'];
					$imageName = $imageid.'.'.$file_ext;
					$imageThumbName = 'thumb_'.$imageid.'.'.$file_ext;
					if(!is_dir('./project_drawings/'.$_SESSION['idp'])){
						@mkdir('./project_drawings/'.$_SESSION['idp'], 0777);
					}
					$obj->resizeImages($_FILES['drawingImage']['tmp_name'], 1600, 1600, './project_drawings/'.$_SESSION['idp'].'/'.$imageName);
#					move_uploaded_file($_FILES['drawingImage']['tmp_name'], './project_drawings/'.$_SESSION['idp'].'/'.$imageName);
					
					if(!is_dir('./project_drawings/'.$_SESSION['idp'].'/thumbnail')){
						@mkdir('./project_drawings/'.$_SESSION['idp'].'/thumbnail', 0777);
					}
					$obj->resizeImages('./project_drawings/'.$_SESSION['idp'].'/'.$imageName, 150, 150, './project_drawings/'.$_SESSION['idp'].'/thumbnail/'.$imageThumbName);
					$updateQRY = "UPDATE draw_mgmt_images SET
									draw_mgmt_images_name = '".$imageName."',
									draw_mgmt_images_thumbnail = '".$imageThumbName."',
									last_modified_date = NOW(),
									last_modified_by = '".$_SESSION['ww_builder_id']."'
								WHERE
									draw_mgmt_images_id = '".$imageid."'";
		
					mysql_query($updateQRY);
					unset($_SESSION['isRemoveDraw']);
				}else{
					$err_msg = 'Please fill drawing title';		
				}
			}else{
				$err_msg = 'Please select either ".jpg" or ".png" file';
			}
		}else{
			if(trim($_POST['drawingTitle']) == ''){
				$err_msg = 'Please select drawing images and fill drawing title';
			}else{
				$drawImageTitle = trim(addslashes($_POST['drawingTitle']));
				$drawDescription = trim(addslashes($_POST['drawingDescription']));
				$drawTags = trim(addslashes($_POST['drawingTags']));
				$drawTags = trim($drawTags, ";");
				$drawTags = implode(";", array_map('trim', explode(";", $drawTags)));

				if($drawTags != ""){
					$drawTags = $drawTags . ";";
				}
				$inssertQRY = "UPDATE draw_mgmt_images SET
								project_id = '".$_SESSION['idp']."',
								draw_mgmt_images_title = '".$drawImageTitle."',
								draw_mgmt_images_description = '".$drawDescription."',
								draw_mgmt_images_tags = '".$drawTags."',
								last_modified_date = NOW(),
								last_modified_by = '".$_SESSION['ww_builder_id']."'
							WHERE
								draw_mgmt_images_id = '".$_POST['drawingID']."'";
				mysql_query($inssertQRY);			
			}
		}
		if($err_msg == ''){
			$_SESSION['add_inspector_success'] = 'Drawing image uploaded and data updated successfully !';
			header('location:?sect=drawing_management');
		}
	}
}else{
	unset($_SESSION['isRemoveDraw']);
}
$drawData = $obj->selQRYMultiple('draw_mgmt_images_id, draw_mgmt_images_title, draw_mgmt_images_name, draw_mgmt_images_thumbnail, draw_mgmt_images_description, draw_mgmt_images_tags', 'draw_mgmt_images', 'is_deleted = 0 AND draw_mgmt_images_id = "'.$drawingID.'"'); ?>
<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
<div id="apply_now">
	<form action="?sect=edit_drawing_management&imgID=<?=$_GET['imgID']?>"  method="post"  enctype="multipart/form-data" name="edit_checklist" id="edit_checklist" onSubmit="return validateSubmit()" >
		<div class="content_container">
			<div class="content_left">
				<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top: -15px;margin-top: 0px\9;">
					<?php if((isset($_SESSION['add_inspector_success'])) && (!empty($_SESSION['add_inspector_success']))) {
						if($_SESSION['add_inspector_success'] != ''){?>
							<div class="success_r" style="height:35px;width:400px;"><p><?=$_SESSION['add_inspector_success'];?></p></div>		
					<?php   }
					unset($_SESSION['add_inspector_success']); 
					}
					if($err_msg != '') { ?>
						<div class="failure_r" style="height:35px;width:400px;"><p><?php echo $err_msg; ?></p></div>
				<?php 	} ?>
				</div>
				<div class="content_hd1" style="background-image:url(images/edit_drawing.png);margin-top:-50px\9;"></div>
				<div id="sign_in_response" style="width:900px;"></div>
				<div class="signin_form">
					<table width="700" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-40px;">
						<tr>
							<td valign="top">Drawing&nbsp;Image <span class="req">*</span></td>
							<td><span id="deleteIMG_<?=$drawData[0]['draw_mgmt_images_id']?>">
<?php	$filePath = 'project_drawings/'.$_SESSION['idp'].'/thumbnail/'.$drawData[0]['draw_mgmt_images_thumbnail'];
if(file_exists($filePath)){?>
<a href="<?='project_drawings/'.$_SESSION['idp'].'/'.$drawData[0]['draw_mgmt_images_name']?>" class="thickbox"><img src="<?=$filePath?>" alt="drawingImage" id="drawImage" /></a><br />
<img id="removeImg" src="images/replace_image.png" style="margin-top:10px;cursor:pointer;" onclick="removeImages('drawImage', 'deleteIMG_<?=$drawData[0]['draw_mgmt_images_id']?>', this.id);" />
<?php }else{
echo '<img src="images/noDrawing.jpg" id="noImage" name="" alt="No Image Found !"  />';
}?>
							</span></td>
						</tr>
						<tr>
							<td valign="top">&nbsp;</td>
							<td>
								<input type="file" name="drawingImage" id="drawingImage" style="display:none;" />

							</td>

						</tr>
						<tr>
							<td valign="top">Drawing&nbsp;Title <span class="req">*</span></td>
							<td>
								<input type="text" name="drawingTitle" id="drawingTitle" onblur="checklistId(this, this.value);" class="input_small" value="<?=$drawData[0]['draw_mgmt_images_title']?>" />
							</td>
						</tr>
						<tr>
							<td valign="top">Drawing&nbsp;Description</td>
							<td>
								<textarea name="drawingDescription" id="drawingDescription" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"><?=$drawData[0]['draw_mgmt_images_description']?></textarea>
							</td>
						</tr>
						<tr>
							<td valign="top">Drawing&nbsp;Tags</td>
							<td>
								<textarea name="drawingTags" id="drawingTags" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width:252px;height:45px;"><?=$drawData[0]['draw_mgmt_images_tags'];?></textarea><br />
								Please seperate location by semicolon(;)
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<input type="hidden" name="drawingID" id="drawingID" value="<?=$drawingID?>" />
<!--								<input name="button" type="image" class="submit_btn" id="button" value="submit" src="images/update.png" style="border:none; width:111px;" onclick="validateSubmit();"  />-->
								<input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/update.png);height:86px;font-size:0px; border:none; width:111px;float:left;" onclick="validateSubmit();"  />&nbsp;&nbsp;&nbsp;
								<a id="ancor" href="javascript:history.back();" onclick="yes">
									<img src="images/back_btn.png" style="border:none; width:111px;" />
								</a>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
<?php if(!file_exists($filePath)){?>document.getElementById('drawingImage').style.display = 'block';<?php }?>
function removeImages(divId, imgID, removeButtonId){
	var r = jConfirm('Do you want to delete drawing image', null, function(r){
		if (r === true){
			showProgress();	
			var imgDiv = document.getElementById(divId);
			var imgSrc = imgDiv.src;	
			imgDiv.src = 'images/noDrawing.jpg';
			document.getElementById(imgID).innerHTML = '<img src="images/noDrawing.jpg" id="noImage" name="" alt="No Image Found !"  />';
			document.getElementById('drawingImage').style.display = 'block';
			document.getElementById('ancor').innerHTML += '<input type="hidden" name="imageDelete" id="imageDelete" value="'+imgSrc+'"  />';
			hideProgress();
		}else{
			return false;
		}
	});
}
function validateSubmit(){
	if($('#noImage').length){ imgDelete = document.getElementById('noImage').src; }else{ imgDelete = ''; }
	var isFile = document.getElementById('drawingImage').files.length;
	if(imgDelete != ''){
		if(isFile == 0){
			var r = jConfirm('Drawing image is not selected if you submit form so image is delete permanent do you want to continue ?', null, function(r){
				if (r === true){
					document.forms["edit_checklist"].submit();	
				}else{
					return false;
				}
			});
		}else{
			document.forms["edit_checklist"].submit();	
		}
	}else{
		document.forms["edit_checklist"].submit();	
	}
}
</script>