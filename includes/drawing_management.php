<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
include'data-table.php'; 
include_once("commanfunction.php");

session_start();
$obj = new COMMAN_Class(); 
if(isset($_REQUEST['id'])){
	$id=$_REQUEST['id'];
	$_SESSION['project_id']=$id;
}else
	$id = ''; 
$err_msg='';
//insert for Assign inspector
if(!isset($_SESSION['no_refresh'])){
	$_SESSION['no_refresh'] = "";
}

if(isset($_POST['save'])){
	if($_POST['no_refresh'] == $_SESSION['no_refresh']){}else{
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
						$drawTags = trim($drawTags) . ";";
					}

					$inssertQRY = "INSERT INTO draw_mgmt_images SET
									project_id = '".$_SESSION['idp']."',
									draw_mgmt_images_title = '".$drawImageTitle."',
									draw_mgmt_images_description = '".$drawDescription."',
									draw_mgmt_images_tags = '".$drawTags."',
									last_modified_by = '".$_SESSION['ww_builder_id']."',
									created_date = NOW(),
									last_modified_date = NOW(),
									created_by = '".$_SESSION['ww_builder_id']."'";
		
					mysql_query($inssertQRY);			
					$imageid = mysql_insert_id();
#File Upload Section
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
#File Upload Section
					$updateQRY = "UPDATE draw_mgmt_images SET
									draw_mgmt_images_name = '".$imageName."',
									draw_mgmt_images_thumbnail = '".$imageThumbName."',
									last_modified_date = NOW(),
									last_modified_by = ".$_SESSION['ww_builder_id']."
								WHERE draw_mgmt_images_id = '".$imageid."'";
		
					mysql_query($updateQRY);
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
				$err_msg = 'Please select drawing image';
			}
		}
		if($err_msg == ''){
			$_SESSION['add_inspector_success'] = 'Drawing image uploaded successfully !';
		}
		$_SESSION['no_refresh'] = $_POST['no_refresh'];
	}
}
?>
	<div id="middle" style="padding-top:10px;">
		<div id="leftNav" style="width:250px;float:left;">
			<?php include 'side_menu.php';?>
		</div>
<?php $id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']);  ?>
		<div id="rightCont" style="float:left;width:700px;">
			<div class="content_hd1" style="width:500px;margin-top:12px;">
				<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font><br />
				<!-- <a href="?sect=add_project_detail&id=<?=$id;?>&hb=<?=$hb;?>" style="display: block;float: none;height: 35px;margin-left: 585px;margin-top: -25px;" class="green_small">Back
				</a> -->
				<a href="?sect=add_project_detail&id=<?=$id;?>&hb=<?=$hb;?>" class="green_small" style="float:left;margin-top:-25px; margin-left:600px;z-index:100;">Back			
				</a>
			</div><br clear="all" />
			<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top: -15px;margin-top: 0px\9;"><?php if((isset($_SESSION['add_inspector_success'])) && (!empty($_SESSION['add_inspector_success']))) {
		if($_SESSION['add_inspector_success'] != ''){?>
			<div class="success_r" style="height:35px;width:400px;"><p><?=$_SESSION['add_inspector_success'];?></p></div>		
<?php   }
		unset($_SESSION['add_inspector_success']); }
		if($err_msg != '') { ?>
			<div class="failure_r" style="height:35px;width:400px;"><p><?php echo $err_msg; ?></p></div>
<?php 	} ?>
			</div>
			<div class="big_container" style="width:722px;float:left;margin-top:-50px;" >
				<div style="border:1px solid #000; margin:45px 20px 10px 10px;text-align:center;">
					<form action="" id="drawingManagement" name="drawingManagement"  method="post" style="margin-top:10px;" enctype="multipart/form-data">
						<table width="100%" border="0" cellspacing="0" cellpadding="0" style="margin-left:15px;" >
							<tr>
								<td style="padding-bottom:10px;" align="left">Drawing&nbsp;Image <span class="req">*</span></td>
								<td style="padding-bottom:10px;" align="left">Drawing&nbsp;Title <span class="req">*</span></td>
							</tr>
							<tr>
								<td style="" align="left">
									<input type="file" name="drawingImage" id="drawingImage" value="" />
								</td>
								<td align="left">
									<input type="text" name="drawingTitle" id="drawingTitle" onblur="checklistId(this, this.value);" class="input_small"  />
								</td>
							</tr>
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td style="padding-bottom:10px;" align="left">Drawing&nbsp;Description</td>
								<td style="padding-bottom:10px;" align="left">Drawing&nbsp;Tags</td>
							</tr>
							<tr>
								<td align="left">
									<textarea name="drawingDescription" id="drawingDescription" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"></textarea>
								</td>
								<td align="left">
									<textarea name="drawingTags" id="drawingTags" class="text_area_small" cols="25" rows="2" style="background-image:url('images/texarea_select_box_small.png');width: 252px;height: 45px;"></textarea>
									<input type="hidden" name="no_refresh" id="no_refresh" value="<?php echo uniqid(rand());?>"  />
								</td>
							</tr>
							<tr>
								<td colspan="2" align="right" height="50px">
									<input class="green_small" type="submit" style="cursor:pointer;height:30px;margin-right:35px;" value="Submit"  name="save" id="save" />
								</td>
							</tr>
						</table>
					</form>
				</div>
				<div id="searchDraw" style="width:712px;float:left;margin-left:10px;height:50px;" >
				<table width="100%" border="0">
					<tr>
						<td style="">Search By Drawing Title&nbsp;:</td>
						<td><input type="text" name="searchStr" id="searchStr" class="input_small" value="" /></td>
						<td><a onclick="searchDrawImage('new');" style="cursor:pointer;" class="green_small" alt="search" />Search</a>&nbsp;&nbsp;<a onclick="searchDrawImage('clean');" class="green_small" style="cursor:pointer;" alt="search" >Back</a></td>
					</tr>
				</table>
				</div>
				<div class="big_container" style="width:712px;float:left;margin-left:10px;height:480px;max-height:480px;overflow:auto;" ><?php include'drawing_management_show.php';?></div>
				<div class="spacer"></div>
			</div>
		</div>
	</div>
<script type="text/javascript">
function searchDrawImage(opt){
	var searchStr = document.getElementById('searchStr').value;
	if(opt == 'clean'){
		document.getElementById('searchStr').value = '';
		var searchStr = '';
	}
	var searchStr = document.getElementById('searchStr').value;
	var responseArea = document.getElementById('drawingDisplay');
	showProgress();	
	$.ajax({
		url: "search_drawing_images.php",
		type: "POST",
		data: "searchSTR="+searchStr,
		success: function (res) {
			hideProgress();//start image display here
			responseArea.innerHTML = res;
		}
	});	
}
</script>