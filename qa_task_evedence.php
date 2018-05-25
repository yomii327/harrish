<?php 
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
?>

<?php 
if(isset($_REQUEST['EveInsertId'])){
	//print_r($_FILES); print_r($_REQUEST); die;
	$time = time();
	//echo "<pre>";  print_r($_REQUEST); die;
	$insrted_id = 0;
	if($_REQUEST['task_detail_id'] > 0){
		$insrted_id = $_REQUEST['task_detail_id'];
		//$insrted_id = substr(time(), 0, 3) . substr(time(), 5) . rand(0, 999) . $_SESSION['ww_builder_id'];
		$updateQry = "UPDATE qa_ncr_task_detail SET
						task_id = '".$_REQUEST['taskId']."',
						raised_by = '".$_REQUEST['userRoll']."',
						comment = '".$_REQUEST['comment']."',
						project_id = '".$_REQUEST['projID']."',
						created_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						last_modified_date = NOW(),
						original_modified_date = NOW()
					WHERE
						task_detail_id = ".$_REQUEST['task_detail_id']; 
		mysql_query($updateQry) or die(mysql_error());
		
		# Insert evidence_sign.
		if(isset($_REQUEST['evidence_sign']) && !empty($_REQUEST['evidence_sign'])){
			$attchdata = "task_detail_id = '".$insrted_id."',
				attachment_title = 'Web_image',
				attachment_description = '',
				attachment_file_name = '".$_REQUEST['evidence_sign']."',
				attachment_type = 'evidence_signoff',
				project_id = '".$_REQUEST['projID']."',
				created_by = '".$_SESSION['ww_builder_id']."',
				created_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				last_modified_date = NOW(),
				original_modified_date = NOW()";
				$attsql = "INSERT INTO qa_ncr_attachments SET ".$attchdata;
			mysql_query($attsql);
		}

		if(isset($_FILES) && !empty($_FILES)){

			$attchment_name1 = basename($_FILES["image1"]["name"]);
			$attchment_name2 = basename($_FILES["image2"]["name"]);
			$uid = $_SESSION['ww_builder_id'];

			$target_dir = './inspections/ncr_files/';
			$ext1 = pathinfo($attchment_name1,PATHINFO_EXTENSION);
			$ext2 = pathinfo($attchment_name2,PATHINFO_EXTENSION);
			$image1 = $uid.'_image1_'.$attchment_name1;
			$image2 = $uid.'_image2_'.$attchment_name2;
			
			$img_id_arr = array();
			$img_id_arr[] = $_REQUEST['imageId1'];
			$img_id_arr[] = $_REQUEST['imageId2'];
			$id_img = 0;
			foreach ($_FILES as $key => $files) {
				if($files["name"]){
					if(!empty($img_id_arr[$id_img])){
						$attchdata = "task_detail_id = '".$_REQUEST['task_detail_id']."',
						attachment_description = '',
						attachment_file_name = '".$uid."_".$key."_".$files['name']."',
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						last_modified_date = NOW(),
						original_modified_date = NOW()";
						$attsql = "UPDATE  qa_ncr_attachments SET ".$attchdata. "WHERE ncr_attachment_id = ".$img_id_arr[$id_img];

						mysql_query($attsql);
					}else{
						$attchdata = "task_detail_id = '".$_REQUEST['task_detail_id']."',
						attachment_title = 'Web_image',
						attachment_description = '',
						attachment_file_name = '".$uid."_".$key."_".$files['name']."',
						attachment_type = 'evidence_image',
						project_id = '".$_REQUEST['projID']."',
						created_by = '".$_SESSION['ww_builder_id']."',
						created_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						last_modified_date = NOW(),
						original_modified_date = NOW()";
						$attsql = "INSERT INTO qa_ncr_attachments SET ".$attchdata;
				
						mysql_query($attsql);
					}
				}
				$id_img++;
			}

			move_uploaded_file($_FILES['image1']['tmp_name'], $target_dir.$image1);
			move_uploaded_file($_FILES['image2']['tmp_name'], $target_dir.$image2);
		}

		$output = array('status' => true, 'msg' => 'Evendence Added Sucessfully', 'taskId' => $_REQUEST['taskId'], 'updateid' => $_REQUEST['task_detail_id']);

	}else{
		#print_r($_REQUEST);die;
		$insrted_id = substr(time(), 0, 3) . substr(time(), 5) . rand(0, 999) . $_SESSION['ww_builder_id'];
		$inssertQRY = "INSERT INTO qa_ncr_task_detail SET
						task_detail_id = '".$insrted_id."',
						task_id = '".$_REQUEST['taskId']."',
						raised_by = '".$_REQUEST['userRoll']."',
						comment = '".$_REQUEST['comment']."',
						project_id = '".$_REQUEST['projID']."',
						created_by = '".$_SESSION['ww_builder_id']."',
						created_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						last_modified_date = NOW(),
						original_modified_date = NOW()";

		mysql_query($inssertQRY);
		
		# Insert evidence_sign.
		if(isset($_REQUEST['evidence_sign']) && !empty($_REQUEST['evidence_sign'])){
			$attchdata = "task_detail_id = '".$insrted_id."',
				attachment_title = 'Web_image',
				attachment_description = '',
				attachment_file_name = '".$_REQUEST['evidence_sign']."',
				attachment_type = 'evidence_signoff',
				project_id = '".$_REQUEST['projID']."',
				created_by = '".$_SESSION['ww_builder_id']."',
				created_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				last_modified_date = NOW(),
				original_modified_date = NOW()";
				$attsql = "INSERT INTO qa_ncr_attachments SET ".$attchdata;
			mysql_query($attsql);
		}
		
		if(isset($_FILES) && !empty($_FILES)){

			$attchment_name1 = basename($_FILES["image1"]["name"]);
			$attchment_name2 = basename($_FILES["image2"]["name"]);
			$uid = $_SESSION['ww_builder_id'];

			$target_dir = './inspections/ncr_files/';
			$ext1 = pathinfo($attchment_name1,PATHINFO_EXTENSION);
			$ext2 = pathinfo($attchment_name2,PATHINFO_EXTENSION);
			$image1 = $uid.'_image1_'.$attchment_name1;
			$image2 = $uid.'_image2_'.$attchment_name2;
			
			
			foreach ($_FILES as $key => $files) {
				
				if($files["name"]){
					$attchdata = "task_detail_id = '".$insrted_id."',
					attachment_title = 'Web_image',
					attachment_description = '',
					attachment_file_name = '".$uid."_".$key."_".$files['name']."',
					attachment_type = 'evidence_image',
					project_id = '".$_REQUEST['projID']."',
					created_by = '".$_SESSION['ww_builder_id']."',
					created_date = NOW(),
					last_modified_by = '".$_SESSION['ww_builder_id']."',
					last_modified_date = NOW(),
					original_modified_date = NOW()";
					$attsql = "INSERT INTO qa_ncr_attachments SET ".$attchdata; 
			
					mysql_query($attsql);
				}
				
			}
		
			// if (!is_dir('./inspections/ncr_files/') {
			// 		@mkdir('./inspections/ncr_files/', 0777);
			// }
			move_uploaded_file($_FILES['image1']['tmp_name'], $target_dir.$image1);
			move_uploaded_file($_FILES['image2']['tmp_name'], $target_dir.$image2);
						    
		}

		$output = array('status' => true, 'msg' => 'Evendence Added Sucessfully', 'taskId' => $_REQUEST['taskId'] );
		
	}

	if(!empty($_REQUEST['saveChecklistImageMulti'])){
		foreach($_REQUEST['saveChecklistImageMulti'] as $key1 => $file1) {
			if($file1){
				$attchdata1 = "task_detail_id = '".$insrted_id."',
				attachment_title = 'Web_image',
				attachment_description = '',
				attachment_file_name = '".$file1."',
				attachment_type = 'evidence_pdf',
				project_id = '".$_REQUEST['projID']."',
				created_by = '".$_SESSION['ww_builder_id']."',
				created_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."',
				last_modified_date = NOW(),
				original_modified_date = NOW()";
				$attsql1 = "INSERT INTO qa_ncr_attachments SET ".$attchdata1;
				mysql_query($attsql1);
				$last_insrted_id = mysql_insert_id();
				$file_name = explode('.',$file1);
				if($file_name[1] == "pdf" || $file_name[1] == "PDF"){
					generateImagesFromPDF($last_insrted_id,$file1);
				}
			}
		}
	}
	
	echo json_encode($output);
			
}

function generateImagesFromPDF($taskId,$file1){
	$pdfAbsolutePath = 'inspections/ncr_files/'.$file1;
	$file_name = explode('.',$file1);
    $folder_name = 'inspections/ncr_files/pdf_images/'.$file_name[0];
    create_directory($folder_name);
    $im = new imagick($pdfAbsolutePath);
    $noOfPagesInPDF = $im->getNumberImages(); 
    if ($noOfPagesInPDF) { 
		for ($i = 0; $i < $noOfPagesInPDF; $i++) { 
			$url = $pdfAbsolutePath.'['.$i.']'; 
			$image = new Imagick($url);
			$image->setImageBackgroundColor('#ffffff');
			$image = $image->flattenImages();
			$image->setImageCompressionQuality(100);
			$image->setImageFormat("jpg"); 
			$image->writeImage($folder_name."/".($i+1).'-'.$taskId.'.jpg'); 
      	}
    }
}
function create_directory($path){
	if(!file_exists($path)){
		$old_umask = umask(0);
		@mkdir($path,0777,true);
		umask($old_umask);
	}
}

if(isset($_REQUEST['deleteImg'])){
	$output = array('status' => false, 'msg' => 'File not found');
	if(!empty($_POST['delImage'])){
		$attchdata = "is_deleted = 1,
					last_modified_by = '".$_SESSION['ww_builder_id']."',
					last_modified_date = NOW(),
					original_modified_date = NOW()";
		$attsql = "UPDATE  qa_ncr_attachments SET ".$attchdata. "WHERE ncr_attachment_id = ".$_POST['delImage'];
		mysql_query($attsql);
		$output = array('status' => true, 'msg' => 'File deleted Sucessfully');
	}
	echo json_encode($output);
}


if(isset($_REQUEST['uniqueId'])){ 
	$evdenceData = $obj->selQRY('*', 'qa_ncr_task_detail', 'is_deleted = 0 AND task_id = '.$_REQUEST['taskId']);
	$atdata = $obj->selQRYMultiple('ncr_attachment_id, attachment_title, attachment_file_name', 'qa_ncr_attachments', 'is_deleted = 0 AND attachment_type = "evidence_image" AND task_detail_id = '.$evdenceData['task_detail_id']);
	#print_r($atdata);
	$imageId1 = '';
	$imageId2 = '';
	$imageId3 = '';
	$task_detail_id = 0;
	$image1 = '';
	$image2 = '';
	$image3 = '';
	if($evdenceData){
		$task_detail_id = $evdenceData['task_detail_id'];
		if(!empty($atdata[0]['attachment_file_name'])){
			$image1 = 'inspections/ncr_files/'.$atdata[0]['attachment_file_name'];
			if(!file_exists($image1)){
				$image1 = '';
			}
		}
		
		if(!empty($atdata[1]['attachment_file_name'])){
			$image2 = 'inspections/ncr_files/'.$atdata[1]['attachment_file_name'];
			if(!file_exists($image2)){
				$image2 = '';
			}
		}
		
		$imageId1 = $atdata[0]['ncr_attachment_id'];
		$imageId2 = $atdata[1]['ncr_attachment_id'];
	}
	
	# Evidance signoff image.
	$signImgData = $obj->selQRYMultiple('ncr_attachment_id, attachment_file_name', 'qa_ncr_attachments', 'is_deleted = 0 AND attachment_type = "evidence_signoff" AND task_detail_id = '.$evdenceData['task_detail_id']);
	#sprint_r($signImgData);
	if($signImgData){
		if(!empty($signImgData[0]['attachment_file_name'])){
			$image3 = 'inspections/ncr_files/'.$signImgData[0]['attachment_file_name'];
			if(!file_exists($image3)){
				$image3 = '';
			}
		}
		$imageId3 = $atdata[0]['ncr_attachment_id'];
	}

	// $pdfdata = $obj->selQRYMultiple('ncr_attachment_id,attachment_file_name', 'qa_ncr_attachments', 'is_deleted = 0 AND attachment_type = "evidence_pdf" AND task_detail_id = '.$evdenceData['task_detail_id']);
	$formImages = $obj->selQRYMultiple("ncr_attachment_id AS imgId, attachment_file_name AS imgName ", "qa_ncr_attachments" , 
		"is_deleted = 0 AND attachment_type = 'evidence_pdf' AND task_detail_id = ".$evdenceData['task_detail_id']);
?>

<style>
	input[type='file']#image1,
    input[type='file']#image2 {
		display: block;
		width: 255px;
	}

	.innerModalPopupDiv4{
		background: #fff;
	}
	.attchImage{
		position:relative;
		float:left;margin-top:20px;
		margin-right:20px;
		width:108px
	}
	.attchImage .delImage{position:absolute;cursor:pointer;right:-10px;top:-8px;z-index:1}
	.loader{display:none;position:absolute;top:30%;left:50%;}
</style>

<fieldset class="Evdwindow">
	<legend style="color:#000000;">Evidence of Inspection</legend>
	<form name="addEvedenceForm" id="addEvedenceForm">
		<table width="650" border="0" align="left" cellpadding="0" cellspacing="15" style="color: #000;">
			
			<tr>
				<td align="left">&nbsp; Attachment  </td>
				<td align="left">
					<label class="filebutton" align="center">&nbsp;Browse Image 1
						<input id="image1" name="image1" type="file" onchange="showImage1(this)">
					</label>
					<input type="hidden" name="imageId1" value="<?=$imageId1?>" />
					<div class="upload-image">
						<div id="innerDiv0" class="innerDiv">
							<?php if(!empty($image1)){ ?>
								<img id="response_1" src="<?=$image1;?>" width="120" height="120">
							<?php } else{ ?>
								<span>Browse Image</span>
								<img id="response_1" class="imageClass1" width="120" height="120" style="display:none;">
							<?php } ?>
						</div>
					</div>
				</td>
				<td align="left">
					<label class="filebutton" align="center">&nbsp;Browse Image 2
						<input id="image2" name="image2" type="file" onchange="showImage2(this)">
					</label>
					<input type="hidden" name="imageId2" value="<?=$imageId2?>" />
					<div class="upload-image">
						<div id="innerDiv1" class="innerDiv">
							<?php if(!empty($image2)){ ?>
								<img id="response_2" src="<?=$image2;?>" width="120" height="120">
							<?php } else{ ?>
								<span>Browse Image</span>
								<img id="response_2"  class="imageClass2" width="120" height="120" style="display:none;">
							<?php } ?>
						</div>
					</div>
				</td>
			</tr>

			<tr>
				<td valign="top" align="left">Images/PDF Attachment <span class="req" id="commentStar"></span></td>
				<td align="left" colspan="2">
					<div class="attachment">
						<div class="filefields">
							<input type="file" name="attachment" id="attachment" />
						</div>
						<div style="clear:both"></div>
						<div id="responseAttachment">
							<?php $i=0;
							if(isset($formImages)) {
								foreach($formImages as $gallery) { ?>
									<div id="attchImage_<?php echo $i; ?>" class="attchImage">
										<input type="hidden" name="photoGalleryId[]" value="<?php echo $gallery['imgId']; ?>" />
										<?php $imageData = explode('.', $gallery['imgName']); 
										if(isset($imageData[1]) && !empty($imageData[1]) && ($imageData[1] == 'pdf' || $imageData[1] == 'PDF')){ ?>
											<img src="images/default_PDF_register_64x64.png" width="100" height="80" alt="images" />
										<?php }else{ ?>
											<img src="inspections/ncr_files/<?php echo $gallery['imgName'];?>" width="100" height="80" alt="images" />
										<?php } ?>
										<img src="images/close_new.png" class="delImage" onClick="deleteAttachment(<?php echo $gallery['imgId']; ?>, 'attchImage_<?php echo $i; ?>')" alt="delete image" />
									</div>
									<?php
									$i++;
								}
							}
							?>
						</div>
						<div style="clear:both;"></div>
						<img class="loader" src="images/preload.gif" alt="loader image" style="display: none;" />
						<input type="hidden" name="attachmentCount" id="attachmentCount" value="<?php echo $i; ?>" />
						<input type="hidden" name="item_id" id="item_id" value="<?php echo $_GET['item_id']; ?>" />
						<input type="hidden" value="<?php echo isset($_SESSION['formId'])?$_SESSION['formId']:0; ?>" name="subContId"  id="subContId"  />
					</div>
				</td>
			</tr>
			
			<tr>
				<td valign="top" align="left">Comment <span class="req" id="commentStar"><?php //if(!empty($evdenceData['comment'])){echo '*'; }?></span></td>
				<td align="left">
					<textarea name="comment" id="comment" class="text_area" ><?php if($evdenceData) { echo $evdenceData['comment']; }?></textarea>
					<lable for="comment" id="errorComment" generated="true" class="error" style="display:none;">
						<div class="error-edit-profile">comment field is required</div>
					</lable>
				</td>
				<td align="left">
					<span>Signature</span>
					<div class="upload-image">
						<div id="innerDiv_<?=$_REQUEST['taskId'];?>" class="innerDiv">
							<label style="display:block">
							<span>Browse Image</span>
							<input type="file" name="evidence_sign" id="evidence_sign" style="width:120px" />
							</label>
							<div class="response" id="responseProjectManagerImage<?=$_REQUEST['taskId'];?>">
								<?php if(isset($image3) && !empty($image3)) { ?>
								<img src="<?=$image3;?>" width="120" height="120" alt="sign image" />
								<?php } ?>
							</div>
						</div>
						<img id="evdRemove_<?=$_REQUEST['taskId'];?>" class="del-image" onClick="deleteImage('innerDiv_<?=$_REQUEST['taskId'];?>', this.id)" src="images/remove.png" alt="delete image" style="display:none" />
						<img class="signIcon" src="images/drawing_pen.png"  onclick="evdSignatureBox('ProjectManager', <?=$_REQUEST['taskId'];?>)" style="width:65px; height:65px;position: absolute;top: 30px;right: -55px;" />
					</div> <!-- /.upload-image -->
				</td>
			</tr>

			<tr>
				<td></td>
				<td>
	            	<input type="button" name="button" class="green_small" id="button" style="float:left;" onclick="addTaskEvedence(0);" value="Submit"/>
	            </td>
	            <td>
	            	<a id="ancor" onclick="closePopup(300);" style="border:none;" class="green_small">Back</a>
				</td>
					<input type="hidden" id="task_detail_id" name="task_detail_id" value="<?=$task_detail_id;?>">
					<input type="hidden" id="taskId" name="taskId" value="<?=$_REQUEST['taskId'];?>">
					<input type="hidden" id="userRoll" name="userRoll" value="<?=$_REQUEST['userRoll'];?>">
					<input type="hidden" id="projID" name="projID" value="<?=$_REQUEST['projID'];?>">
					<input type="hidden" id="chkID" name="chkID" value="<?=$_REQUEST['chkID'];?>">
			</tr>
				
		</table>
	</form>
	<br clear="all" />
</fieldset>

<?php } ?>
