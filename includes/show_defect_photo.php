<?php session_start();
include('commanfunction.php');
$object = new COMMAN_Class();?>

<?php 
if(!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}

$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$pi = pathinfo($refering_url);
$u = str_replace('pms.php','',$pi['basename']);
?>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/thickbox.js"></script>
<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
<?php
$df_id=base64_decode($_GET['id']);

#if(isset($_SESSION['ww_is_builder'])){$builder_id = $_SESSION['ww_builder_id'];$q = "SELECT * FROM ".DEFECTS." d WHERE d.df_id = '$df_id'";}
$q = "SELECT * FROM  project_inspections d WHERE d.inspection_id = '$df_id' and d.is_deleted = '0' ";
if($obj->db_num_rows($obj->db_query($q)) == 0){?>
<script language="javascript" type="text/javascript">
window.location.href="<?=ACCESS_DENIED_SCREEN?>";
</script>
<?php
}
$f=$obj->db_fetch_assoc($obj->db_query($q));

// change inspection_date_raised format
if($f['inspection_date_raised']!='0000-00-00'){
	$inspection_date_raised = $f['inspection_date_raised'];
	$inspection_date_raised = date("d-m-Y", strtotime($inspection_date_raised));
}else{
	$inspection_date_raised = '';
}

// change fixed_by_date format
if($f['inspection_fixed_by_date']!='0000-00-00'){
	$fixed_date = $f['inspection_fixed_by_date'];
	$fixed_date = date("d/m/Y", strtotime($fixed_date));
}else{
	$fixed_date = '';
}
$imageStatus = false;
?>
<style type="text/css">
table.gridtable {
	
	
	border-width: 1px;
	border-color: #FFF;
	border-collapse: collapse;
	
}

table.gridtable td {
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #FFF;
	
}
</style>
<div id="middle" style="padding-bottom:80px;">
	<div id="apply_now">
		<div class="content_container">
			<div class="content_left">
				<div class="content_hd1" style="background-image:url(images/issue_detail.png);margin-top:-50px\9;"></div>
				<?php if(base64_decode($_GET['ms']) == 'Updated'){?>
					<div id="errorHolder" style="margin-left: 10px;margin-top:15px;">
						<div class="success_r" style="height:35px;width:405px;"><p>Inspection Updated Successfully !</p></div>
					</div>
				<?php }?>
				<div class="signin_form" style="margin-left:70px;width:670px">
					<div style="border:1px solid;float:left;">
						<table width="670" border="0" align="left" cellpadding="0" cellspacing="15">
						<tr>
							<td width="134" nowrap="nowrap">Project Name</td>
							<td width="312" colspan="2"><h3><?=stripslashes($object->getDataByKey('user_projects', 'project_id', $f['project_id'], 'project_name'))?></h3></td>
						</tr>
						<tr>
							<td width="134" nowrap="nowrap">Location</td>
							<td width="312" colspan="2"><?php
								$locations = $object->subLocations($f["location_id"], ' > ');
								echo stripslashes($locations);?></td>
						</tr>
						<tr>
                            <td width="134" nowrap="nowrap">GPS Location</td>
                            <td width="312" colspan="2">
                                <?="Latitude : ".stripslashes($f['inspection_latitude'])."&nbsp;&nbsp;&nbsp;&nbsp;"?>
                                <?="Longitude : ".stripslashes($f['inspection_longitude'])?>
                            </td>
                        </tr>
						<tr>
							<td width="134" nowrap="nowrap">Date Raised</td>
							<td width="312" colspan="2"><?=$inspection_date_raised?></td>
						</tr>
						<tr>
							<td nowrap="nowrap">Inspected By</td>
							<td><?=stripslashes($f['inspection_inspected_by'])?></td>
						</tr>
						<tr>
							<td nowrap="nowrap">Inspected Type</td>
							<td><?=stripslashes($f['inspection_type'])?></td>
						</tr>
						<tr>
							<td width="134" nowrap="nowrap" valign="top">Description</td>
							<td width="312" colspan="2"><?=stripslashes($f['inspection_description'])?></td>
						</tr>
                        <tr>
							<td width="134" nowrap="nowrap" valign="top">Photos</td>
							<td width="312" colspan="2">
								<div >
								<?php $images = $object->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$df_id.' AND is_deleted = 0 AND graphic_type = "images" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 2');
								$drawing = $object->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$df_id.' AND is_deleted = 0 AND graphic_type = "drawing" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 1');
							if(!empty($images)){
							foreach($images as $img){?>
								<span style="float:left;margin-right:15px;margin-top:5px;">
								<?php if(file_exists('inspections/photo/'.$img['graphic_name'])){?>
									<a href="inspections/photo/<?=$img['graphic_name']?>" class="thickbox"><img src="inspections/photo/<?=$img['graphic_name']?>" width="100" height="100" /></a>
								<?php }?>
									</span>
							<?php }}
							if(!empty($drawing)){
							foreach($drawing as $drw){?>
								<span style="float:left;margin-right:15px;margin-top:5px;">
								<?php if(file_exists('inspections/drawing/'.$drw['graphic_name'])){?>
									<a href="inspections/drawing/<?=$drw['graphic_name']?>" class="thickbox"><img src="inspections/drawing/<?=$drw['graphic_name']?>" width="100" height="100"  /></a>
								<?php }?>
									</span>
							<?php }}?>
								</div>
							</td>
						</tr>
						<tr>
							<td width="134" nowrap="nowrap" valign="top">Raised By</td>
							<td width="312" colspan="2"><?=stripslashes($f['inspection_raised_by'])?></td>
						</tr>
                        <tr>
							<td width="134" nowrap="nowrap" valign="top">Issued To Detail</td>
                            <td>
                            	<table class="gridtable" width="80%">
                                	
                                	<tr>
                                    	<td width="25%" >Issued&nbsp;To</td>
                                        <td width="25%">Fix&nbsp;by&nbsp;Date</td>
					<?php if($_SESSION['userRole'] != 'Sub Contractor'){?>
                                        <td width="25%">Cost Attribute </td>
					<?php }?>
                                        <td width="25%">Status</td>
					<?php if($_SESSION['userRole'] != 'Sub Contractor'){?>
                                        <td width="25%">Cost Impact</td>
                                        <td width="25%">Cost Impact Price</td>
					<?php }?>
                                    </tr>
                                    
                            <?php $issueToData = $object->selQRYMultiple('issued_to_name, inspection_fixed_by_date, cost_attribute, inspection_status, cost_impact_type, cost_impact_price', 'issued_to_for_inspections', 'inspection_id = '.$df_id.' and is_deleted=0  group by issued_to_name');?>
							
							<?php if(!empty($issueToData)){
									foreach($issueToData as $issueTo){?>
							<tr>		
								<td width="25%"><?php  echo $issueTo['issued_to_name']?></td>
								<td width="25%"><?php if($issueTo['inspection_fixed_by_date']!='0000-00-00'){
													$fixed_on = date("d-m-Y", strtotime($issueTo['inspection_fixed_by_date']));
												}else{
													$fixed_on = '';
												}
												echo $fixed_on; ?></td>
								<?php if($_SESSION['userRole'] != 'Sub Contractor'){?>
								<td width="25%"><?php  echo $issueTo['cost_attribute']?> </td>
								<?php }?>
								<td width="25%"><?php echo $issueTo['inspection_status'];
											if($issueTo['inspection_status'] == 'Closed' ){
												$imageStatus = true;
											}?></td>
								<?php if($_SESSION['userRole'] != 'Sub Contractor'){?>
								<td width="25%"><?php echo $issueTo['cost_impact_type'];?></td>
								<td width="25%"><?php echo '$&nbsp;'.$issueTo['cost_impact_price'];?></td>
								<?php }?>
							</tr>
							<?php	}?>
							<?php 
							}else{ ?>
								<td colspan="6"><em>No One Issue to Found</em></td>	
					  <?php }?>
							</table> 
						</td>
                        </tr>
                     <?php if($_SESSION['userRole'] != 'Sub Contractor'){?>
						<tr>
							<td width="134" nowrap="nowrap" valign="top">Notes</td>
							<td width="312" colspan="2"><?=stripslashes($f['inspection_notes'])?></td>
						</tr>						
			<?php }?>				
						<tr>
							<td width="134" nowrap="nowrap" valign="top">Sign Off</td>
							<td width="312" colspan="2">
							<?php if($imageStatus){
									if($f['inspection_sign_image'] != ''){
										if(file_exists('inspections/signoff/'.$f['inspection_sign_image'])){?>
											<a href="inspections/signoff/<?=$f['inspection_sign_image']?>" title="add a caption to title attribute / or leave blank" class="thickbox"><img src="inspections/signoff/<?=$f['inspection_sign_image']?>" width="150" /></a>
									<?php }
									}
								}?>
							</td>
						</tr>
						<tr>
							<td align="center">
<?php if(isset($_SESSION['ww_is_company'])){?>
	<a href="?sect=c_defect&bk=Y"><button class="green_small">Back</button></a>
<?php }else{?>
	<a href="?sect=i_defect&bk=Y"><button class="green_small">Back</button></a>
<?php }?>
							</td>
							<td align="center">
<?php if($_SESSION['web_edit_inspection'] == 1 || $_SESSION['userRole'] == 'Sub Contractor'){?>
								<a href="?sect=edit_defect&did=<?php echo base64_encode($f['inspection_id']);?>"><button class="green_small">Edit</button></a>
<?php }?>
							</td>
						</tr>
					</table></div>
				</div>
			</div>
			<!--<div class="content_right" style="margin-top:90px;width:285px;">
				<div class="signin_form1" style="background-image:url(images/photo_bg.png); background-repeat:no-repeat; width:285px; height:285px; text-align:center;">
					<a href="?sect=issue_photo&photo=<?=$f['photo']?>" title="Click for large view">
					<img src="<?=$f['photo']?>" class="issue_img" style="width:275px; height:275px; border:none; margin-top:5px;" />
					</a>
				</div>
			</div>-->
		</div>
	</div>
</div>
<? //print_r($f); ?>
<script type="text/javascript">
var align = 'center';
var top = 100;
var width = 670;
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
function ShowChecklist(pId, inspId){
	if(window.XMLHttpRequest){xmlhttp = new XMLHttpRequest();}else{xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");}
	modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'inspection_check.php?projectID='+pId+'&inspectionID='+inspId+'&checklistType=show&uniqueId='+Math.random(), loadingImage);
}

</script>