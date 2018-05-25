<?php include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 
session_start();

if(isset($_REQUEST["imageID"])){
	$searchData = $obj->selQRYMultiple('draw_mgmt_images_id, draw_mgmt_images_title	, draw_mgmt_images_name, draw_mgmt_images_name, draw_mgmt_images_description, draw_mgmt_images_tags', 'draw_mgmt_images', 'draw_mgmt_images_id = "'.$_REQUEST["imageID"].'" AND is_deleted = 0 AND project_id = "'.$_REQUEST["pID"].'"');
	foreach($searchData as $drawingData){
		if(file_exists('project_drawings/'.$_SESSION['idp'].'/'.$drawingData['draw_mgmt_images_name'])){ ?>
			<table width="100%" border="0">
				<tr>
					<td rowspan="3" width="80%" style="padding-right:15px; border-right:1px solid black;" valign="middle" align="center">
					<?php $imgSize = getimagesize('./project_drawings/'.$_SESSION['idp'].'/'.$drawingData['draw_mgmt_images_name']);
					$width = $imgSize[0];
					if($width >= 600){
						$width = '600';
					}?>
						<img src="<?='./project_drawings/'.$_SESSION['idp'].'/'.$drawingData['draw_mgmt_images_name']?>" width="<?=$width?>"  />
					</td>
					<td rowspan="3" width="20%" style="color:#000000;padding-left:15px;height:100px;" valign="top">
					<div>
						<strong>Title</strong>&nbsp;:<br /><?=wordwrap($drawingData['draw_mgmt_images_title'], 20, '<br />', true);?>
					</div><br />
					<div>
						<strong>Description</strong>&nbsp;:<br /><?=wordwrap($drawingData['draw_mgmt_images_description'], 20, '<br />', true);?>
					</div><br />
					<div>
						<strong>Tags</strong>&nbsp;:<br /><?=wordwrap($drawingData['draw_mgmt_images_tags'], 20, '<br />', true);?>
					</div>
					</td>
				</tr>
			</table>
	<?php }
	}
}?>