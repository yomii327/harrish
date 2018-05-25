<?php
session_start();
include_once("includes/commanfunction.php");

$obj = new COMMAN_Class(); 
$drawData = $obj->selQRYMultiple('draw_mgmt_images_id, draw_mgmt_images_title, draw_mgmt_images_name, draw_mgmt_images_thumbnail', 'draw_mgmt_images', 'project_id	= "'.$_SESSION['idp'].'" AND is_deleted = 0');
?>
<div id="drawingDisplay" style="margin:15px;">
<?php $i=0;
foreach($drawData as $drawingData){$i++; ?>
		<div class="drawing_holder" id="drawing_<?=$i?>">
			<div style="text-align:center;font-size:16px;line-height:16px;">
				<?php $title = substr($drawingData['draw_mgmt_images_title'], 0, 13);
				if(strlen($drawingData['draw_mgmt_images_title']) > 13){
					$title = $title.'..';
				}
				echo $title; ?>
			</div>
			<div style="height:150px;">
<?php if(file_exists('project_drawings/'.$_SESSION['idp'].'/thumbnail/'.$drawingData['draw_mgmt_images_thumbnail'])){ ?>
				<a href="#" onclick="showPhoto('<?=$_SESSION['idp']?>', '<?=$drawingData['draw_mgmt_images_id']?>');">
					<img id="drawing_images_<?=$drawingData['draw_mgmt_images_id']?>" src="project_drawings/<?=$_SESSION['idp'].'/thumbnail/'.$drawingData['draw_mgmt_images_thumbnail']?>" width="140" height="100%" />
				</a>
<?php }else{?>
					<img id="drawing_images_<?=$drawingData['draw_mgmt_images_id']?>" src="images/noDrawing.jpg" width="140" />
<?php }?>
			</div>
			<div style="text-align:center;height:15px;" id="delete_<?=$drawingData['draw_mgmt_images_id']?>">
				<a href="?sect=edit_drawing_management&imgID=<?=base64_encode($drawingData['draw_mgmt_images_id']);?>">Edit</a>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="javascript:void(0);" onclick="removeImages('drawing_images_<?=$drawingData['draw_mgmt_images_id']?>', '<?=$drawingData['draw_mgmt_images_id']?>', 'drawing_<?=$i?>');">Delete</a>
			</div>
		</div>
<?php }?>
</div> 
<div style="clear:both; height:1px"></div>
<script type="text/javascript">
function removeImages(divId, imgID, removeButtonId){
	var r = jConfirm('Do you want to delete drawing image and it\'s data', null, function(r){
		if (r === true){
			var imgDiv = document.getElementById(divId);
			var imgSrc = imgDiv.src;	
			showProgress();	
			$.ajax({
				url: "remove_drawing_file.php",
				type: "POST",
				data: "imageData="+imgSrc+"&imageID="+imgID,
				success: function (res) {
					hideProgress();
					document.getElementById(removeButtonId).style.display = 'none';
					imgDiv.style.display = 'none';
				}
			});	
		}else{
			return false;
		}
	});
}

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
var copyId = '';

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
function showPhoto(pID, imgID){
	modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'display_draw_image.php?pID='+pID+'&imageID='+imgID, loadingImage);
}
</script>