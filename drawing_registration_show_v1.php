<?php
session_start();

include('includes/commanfunction.php');
$obj = new COMMAN_Class(); 
$drawData = array();
$permArr = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Architect', 'Concrete & PT', 'Lighting', 'Tenancy Fitout', 'Penthouse Architecture', 'Landscaping');

$drawData = $obj->selQRYMultiple('dr.id, drr.id as drawingID, dr.title, drr.pdf_name, drr.dwg_name, drr.img_name, drr.revision_number, dr.revision, drr.revision_status, dr.is_approved_edit, dr.is_document_transmittal, dr.created_date as createdDate, drr.created_date, dr.file_type',

'drawing_register_revision_module_one AS drr INNER JOIN drawing_register_module_one AS dr ON dr.id = drr.drawing_register_id AND dr.is_deleted = 0 AND dr.id = '.$_REQUEST['pdfID'],

' drr.project_id = "'.$_SESSION['idp'].'" AND drr.project_id = "'.$_SESSION['idp'].'" AND drr.is_deleted = 0  AND drr.archieve_revision = 0 ORDER BY drr.created_date DESC, drr.id DESC ');
$revisionName = $drawData[0]['title']; 
#echo '<pre>';print_r($drawData);die;
?>
<style>
	ul.buttonHolder { margin: 0; padding: 0; }
	ul.buttonHolder > li {
		margin: 0 10px 0 0;	
		display: inline-block;
	}
</style>
<fieldset class="roundCorner">
	<legend style="color:#000000;">Revision's for : <?=$revisionName?></legend>
	<div id="drawingDisplay" style="margin:0 15px;">
	<?php $i=0;
	if(!empty($drawData)){
		if($_SESSION['userRole'] == 'All Defect'){?>
    <br />
    	<!-- <img src="images/view_history_small.png" alt="show document history" style="float:right;margin-right:5px;" onclick="showHistory(< ?=$_REQUEST['pdfID']?>, < ?=$_SESSION['idp']?>);" /> -->
    	<a class="green_small" href="javascript:void(0)" onclick="showHistory(<?=$_REQUEST['pdfID']?>, <?=$_SESSION['idp']?>);" style="cursor:pointer;float:right;"  alt="show document history" />View History</a>
    <!--img src="images/previous_markups.jpg" alt="previous markups" style="float:right;margin-right:5px;" onclick="showPreviousMarkups(<?=$_REQUEST['pdfID']?>, <?=$_SESSION['idp']?>);" /-->
    <br /><br clear="all" />
	<?php }
		foreach($drawData as $drawingData){$i++; 
			$superseded = '';
			if($i != 1){ $superseded = ' <h3 style="color:red;"> (superceeded) </h3>'; ?> <br clear="all" /><hr><br /> <?php }?>
			<div class="drawing_holder1" id="drawing_<?=$i?>">
<!-- Drawing Title Section -->
				<div style="float:left;text-align:left;font-size:12px;width:80%;">
					<?php $title = substr($drawingData['title'], 0, 160);
					if(strlen($drawingData['title']) > 160){
						$title = $title.'..';
					} echo $title; ?>
				</div>
<!-- Drawing Title Section -->
<!-- Drawing Status Section -->
				<div style="float:left;text-align:right;font-size:12px;width:20%;">
					<?='<strong>Rev # </strong>:'.$drawingData['revision_number'].$superseded; ?>
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
					<?php if($drawingData['pdf_name'] != '' && file_exists('project_drawing_register_v1/'.$_SESSION['idp'].'/'.$drawingData['pdf_name'])){
							$file = end(explode('.',$drawingData['pdf_name']));
							if($drawingData['pdf_name'] != ''){
								if(strtolower($file) == 'pdf'){?>
                                	<a target="_blank" href="project_drawing_register_v1/<?=$_SESSION['idp']?>/<?=$drawingData['pdf_name']?>" title="Click Here to Print"><img id="<?=$_SESSION['idp']?>_<?=$drawingData['pdf_name']?>" src="images/default_PDF_register_64x64.png"  /></a>
							<?php }else if(strtolower($file) == 'cad'){?>
                            		<a target="_blank" href="javascript:void(0)" title="Click Here to Print" id="anchoreDelete"><img id="<?=$_SESSION['idp']?>_<?=$drawingData['dwg_name']?>" src="images/default_CAD_64x64.png"  /></a>
							<?php }else if(strtolower($file) == 'xls' || strtolower($file) == 'XLS'){?>
                            		<a target="_blank" href="javascript:void(0)" title="Click Here to Print" id="anchoreDelete"><img id="<?=$_SESSION['idp']?>_<?=$drawingData['dwg_name']?>" src="images/default_XLS_64x64.png"  /></a>
							<?php }else if(strtolower($file) == 'xlsx' || strtolower($file) == 'XLSX'){?>
                            		<a target="_blank" href="javascript:void(0)" title="Click Here to Print" id="anchoreDelete"><img id="<?=$_SESSION['idp']?>_<?=$drawingData['dwg_name']?>" src="images/xlsx_logo.png"  /></a>
							<?php }else if(strtolower($file) == 'doc' || strtolower($file) == 'DOC'){?>
                            		<a target="_blank" href="javascript:void(0)" title="Click Here to Print" id="anchoreDelete"><img id="<?=$_SESSION['idp']?>_<?=$drawingData['dwg_name']?>" src="images/doc_logo.png"  /></a>
							<?php }else if(strtolower($file) == 'docx' || strtolower($file) == 'DOCX'){?>
                            		<a target="_blank" href="javascript:void(0)" title="Click Here to Print" id="anchoreDelete"><img id="<?=$_SESSION['idp']?>_<?=$drawingData['dwg_name']?>" src="images/docx_logo.png"  /></a>
							<?php }else{?>
                            		<a target="_blank" href="javascript:void(0)" title="Click Here to Print" id="anchoreDelete"><img id="<?=$_SESSION['idp']?>_<?=$drawingData['dwg_name']?>" src="images/default_DWG_register_64x64.png"  /></a>
							<?php }
							}?>
					<?php }else{	echo 'File Curropt !';	}?>
				</div>
<!-- Drawing Register Images Section -->
<!-- Drawing PDF Action Section -->
				<?php $dis = 'display:none;'; if($i == 1){ $dis = 'display:block;'; }?>
				<?php if($drawingData['pdf_name'] != ''){
					$file = end(explode('.',$drawingData['pdf_name'])); ?>
					<div style="width:60%;<?=$dis?>">
						<ul class="buttonHolder">
							<?php if(strtolower(end(explode('.', $drawingData['pdf_name']))) != 'dwg'){?>
							<li>
								<a target="_blank" href="project_drawing_register_v1/<?=$_SESSION['idp']?>/<?=$drawingData['pdf_name']?>" title="Click Here to Print">Print</a>
							</li>
							<?php } ?>
							<li>
								<a href="javascript:void(0);" onclick="donloadThis(<?=$drawingData['drawingID']?>, 'PDF', <?=$_SESSION['idp']?>)" title="Click Here to Download">Download</a>
							</li>
							<?php if(($_SESSION['ww_builder']['user_type'] == 'manager' && $drawingData['is_approved_edit'] == 0) || (in_array($_SESSION['userRole'], $permArr) && $drawingData['is_approved_edit'] == 0)){
								if($drawingData['is_document_transmittal'] == 0){?>
							<li>
								<a href="javascript:void(0);" onclick="editRevisionImages('<?=$drawingData['id']?>', '<?=$drawingData['drawingID']?>');">Edit</a>
							</li>
							<li>
								<a href="javascript:void(0);" onclick="removeImages('<?=$drawingData['id']?>', 'drawing_<?=$i?>', '<?=$drawingData['drawingID']?>');">Delete</a>
							</li>							
							<?php }} ?>
							<?php if($file=='pdf' || $file=='PDF'){ ?>
							<li><a href="?sect=draw_markup&fileId=<?php echo $drawingData['id']; ?>&prev=<?=$_GET['prev']?>" style="color: #F00">Markup</a></li>
							<?php } ?>
						</ul>
					</div>
				<?php }?>
<!-- Drawing DWG Action Section -->
			</div>
	<?php }
	}?>
	</div>
</fieldset>
<div id="historyviewer" style="display:none;"></div>
<div style="clear:both; height:1px"></div>