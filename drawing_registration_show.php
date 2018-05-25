<?php session_start();
include('includes/commanfunction.php');
$obj = new COMMAN_Class(); 
$drawData = array();
$permArr = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Architect', 'Concrete & PT', 'Lighting', 'Tenancy Fitout', 'Penthouse Architecture', 'Landscaping');

if($_REQUEST['attr1'] != ""){
	$pdfIDArr = array(0);
	
	$drawData = $obj->selQRYMultiple('id ', 'drawing_register', 'project_id = "'.$_SESSION['idp'].'" AND is_deleted = 0 AND attribute1 = "'.$_REQUEST['attr1'].'" AND attribute2 = "Document Transmittal" AND is_document_transmittal = 1');
	
	foreach($drawData as $dwData){
		$pdfIDArr[] = $dwData['id'];
	}
		
	$drawData = $obj->selQRYMultiple('dr.id, drr.id as drawingID, dr.title, drr.pdf_name, drr.dwg_name, drr.img_name, drr.revision_number, dr.revision, drr.revision_status, dr.is_approved_edit, dr.is_document_transmittal, drr.created_date',

	'drawing_register_revision AS drr INNER JOIN drawing_register AS dr ON dr.id = drr.drawing_register_id AND dr.is_deleted = 0 AND dr.id IN ( '.join(",", $pdfIDArr).' )',

	' drr.project_id = "'.$_SESSION['idp'].'" AND drr.project_id = "'.$_SESSION['idp'].'" AND drr.is_deleted = 0  AND drr.archieve_revision = 0 ORDER BY drr.created_date DESC, drr.id DESC ');
	
}else{
	$drawData = $obj->selQRYMultiple('dr.id, drr.id as drawingID, dr.title, drr.pdf_name, drr.dwg_name, drr.img_name, drr.revision_number, dr.revision, drr.revision_status, dr.is_approved_edit, dr.is_document_transmittal, drr.created_date',

	'drawing_register_revision AS drr INNER JOIN drawing_register AS dr ON dr.id = drr.drawing_register_id AND dr.is_deleted = 0 AND dr.id = '.$_REQUEST['pdfID'],

	' drr.project_id = "'.$_SESSION['idp'].'" AND drr.project_id = "'.$_SESSION['idp'].'" AND drr.is_deleted = 0  AND drr.archieve_revision = 0 ORDER BY drr.created_date DESC, drr.id DESC ');

}

$revisionName = $drawData[0]['title']; 
?>
<fieldset class="roundCorner">
	<legend style="color:#000000;">Revision's for : <?=$revisionName?></legend>
	<div id="drawingDisplay" style="margin:0 15px;max-height:500px;overflow:auto;">
	<?php $i=0;
	if(!empty($drawData)){
		foreach($drawData as $drawingData){$i++; 
			if($i != 1){?> <br clear="all" /><hr><br /> <?php }?>
			<div class="drawing_holder1" id="drawing_<?=$i?>" <?php if($_REQUEST['attr1'] != ""){echo 'style="width:580px;"';}?>>
<!-- Drawing Title Section -->
				<div style="float:left;text-align:left;font-size:12px;width:50%;">
					<?php $title = substr($drawingData['title'], 0, 160);
					if(strlen($drawingData['title']) > 160){
						$title = $title.'..';
					} echo $title; ?>
				</div>
<!-- Drawing Title Section -->
<!-- Drawing Status Section -->
				<div style="float:left;text-align:right;font-size:12px;width:50%;">
					<?='<strong>Rev # </strong>:'.$drawingData['revision_number']; ?>
				</div>
				<br clear="all" />
				<div style="text-align:center;font-size:12px;">
				<?php $status = substr($drawingData['revision_status'], 0, 15);
					if(strlen($drawingData['revision_status']) > 15){	$status = $status.'..'; }
					echo '<br /><div style="width:50%;float:left;text-align:left;"><i><b>Status: </b>'.$status.'</i></div>';
					echo '<div style="width:50%;float:left;text-align:right;"><i><b>Date: </b>'.date('d/m/Y', strtotime($drawingData['created_date'])).'</i></div>
					<br /><br />'; ?>
				</div>
<!-- Drawing Status Section -->
<!-- Drawing Register Images Section -->
				<div style="height:70px;">
					<?php #if($drawingData['pdf_name'] != '' && file_exists('project_drawing_register/'.$_SESSION['idp'].'/'.$drawingData['pdf_name'])){
							if($drawingData['pdf_name'] != ''){?>
							<a target="_blank" href="project_drawing_register/<?=$_SESSION['idp']?>/<?=$drawingData['pdf_name']?>" title="Click Here to Print">
								<img id="<?=$_SESSION['idp']?>_<?=$drawingData['pdf_name']?>" src="images/default_PDF_register_64x64.png"  />
							</a>
						<?php }?>
						<?php if($drawingData['dwg_name'] != ''){ ?>
							<a target="_blank" href="javascript:void(0)" title="Click Here to Print">
								<img id="<?=$_SESSION['idp']?>_<?=$drawingData['dwg_name']?>" src="images/default_DWG_register_64x64.png"  />
							</a>
						<?php }?>
						<?php if($drawingData['img_name'] != ''){ ?>
							<a target="_blank" href="javascript:void(0)" title="Click Here to Print">
								<img id="<?=$_SESSION['idp']?>_<?=$drawingData['img_name']?>" src="images/default_IMG_register_64x64.png"  />
							</a>
						<?php }?>
					<?php #}else{	echo 'File Curropt !';	}?>
				</div>
<!-- Drawing Register Images Section -->
<!-- Drawing PDF Action Section -->
				<?php $dis = 'display:none;'; if($i == 1){ $dis = 'display:block;'; }?>
				<?php if($drawingData['pdf_name'] != ''){ ?>
					<div style="float:left; width:35%;<?=$dis?>">
						<ul class="buttonHolder" style="margin-left:-50px;">
							<?php if(strtolower(end(explode('.', $drawingData['pdf_name']))) != 'dwg'){?>
							<li>
								<a target="_blank" href="project_drawing_register/<?=$_SESSION['idp']?>/<?=$drawingData['pdf_name']?>" title="Click Here to Print">Print</a>
							</li>
							<?php }?>
							<li>
								<a href="javascript:void(0);" onclick="donloadThis(<?=$drawingData['drawingID']?>, 'PDF')" title="Click Here to Download">Download</a>
							</li>
							<?php if(($_SESSION['ww_builder']['user_type'] == 'manager' && $drawingData['is_approved_edit'] == 0) || (in_array($_SESSION['userRole'], $permArr) && $drawingData['is_approved_edit'] == 0)){
								if($drawingData['is_document_transmittal'] == 0){?>
							<li>
								<a href="javascript:void(0);" onclick="editRevisionImages('<?=$drawingData['id']?>', '<?=$drawingData['drawingID']?>');">Edit</a>
							</li>
							<li>
								<a href="javascript:void(0);" onclick="removeImages('<?=$drawingData['id']?>', 'drawing_<?=$i?>', '<?=$drawingData['drawingID']?>');">Delete</a>
							</li>
							<?php }}?>					
						</ul>
					</div>
				<?php }?>
<!-- Drawing DWG Action Section -->
<!-- Drawing PDF Action Section -->
				<?php if($drawingData['dwg_name'] != ''){ ?>
					<div style="float:left; width:30%;<?=$dis?>">
						<ul class="buttonHolder" style="margin-left:-20px;">
						<?php if(strtolower(end(explode('.', $drawingData['pdf_name']))) != 'dwg'){?>
							<li style="width:25%;">
								<a href="javascript:void(0);" onclick="removeImages('<?=$drawingData['id']?>', 'drawing_<?=$i?>', '<?=$drawingData['drawingID']?>');">Delete</a>
							</li>				
						<?php }?>
							<li>
								<a href="javascript:void(0);" onclick="donloadThis(<?=$drawingData['drawingID']?>, 'DWG')" title="Click Here to Download">Download</a>
							</li>
						</ul>
					</div>
				<?php }?>
<!-- Drawing DWG Action Section -->
<!-- Drawing IMG Action Section -->
				<?php if($drawingData['img_name'] != ''){ ?>
					<div style="float:left; width:35%; <?=$dis?>">
						<ul class="buttonHolder" style="margin-left:-10px;">
							<li style="width:25%;">
								<a href="javascript:void(0);" onclick="removeImages('<?=$drawingData['id']?>', 'drawing_<?=$i?>', '<?=$drawingData['drawingID']?>');">Delete</a>
							</li>				
							<li>
								<a href="javascript:void(0);" onclick="donloadThis(<?=$drawingData['drawingID']?>, 'IMG')" title="Click Here to Download">Download</a>
							</li>
						</ul>
					</div>
				<?php }?>
<!-- Drawing IMG Action Section -->
				<!--<img src="images/view_history_small.png" onclick="showHistory(<?=$drawingData['drawingID'];?>, <?=$_SESSION['idp'];?>);" style="margin:-8px 0 0 -15px;cursor:pointer;">-->
			</div>
	<?php }
	}?>
	</div>
</fieldset>
<div id="historyviewer" style="display:none;"></div>
<div style="clear:both; height:1px"></div>