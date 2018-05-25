<?php include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 
session_start();

if(isset($_POST["searchSTR"])){
	$searchSTR = trim($_POST["searchSTR"]);
	$searchData = $obj->selQRYMultiple('draw_mgmt_images_id, draw_mgmt_images_title, draw_mgmt_images_name, draw_mgmt_images_thumbnail, draw_mgmt_images_description, draw_mgmt_images_tags', 'draw_mgmt_images', 'draw_mgmt_images_title LIKE "%'.$searchSTR.'%" AND is_deleted = 0 AND project_id = "'.$_SESSION['idp'].'"');
	if(!empty($searchData)){
		foreach($searchData as $drawingData){$i++; ?>
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
					<img id="drawing_images_<?=$drawingData['draw_mgmt_images_id']?>" src="project_drawings/<?=$_SESSION['idp'].'/thumbnail/'.$drawingData['draw_mgmt_images_thumbnail']?>" width="140" height="100%"  />
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
	<?php }
	}else{
		echo 'No Image Found !';
	}
}?>
<div style="clear:both; height:1px"></div>