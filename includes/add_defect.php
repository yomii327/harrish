<?php if(!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])){ ?>
	<script language="javascript" type="text/javascript">
		window.location.href="<?=HOME_SCREEN?>";
	</script>
<?php }
if(isset($_POST['sessionBack']) && $_POST['sessionBack'] == 'Y'){
	$_SESSION['qc'] = $_POST;
}
$owner_id = $_SESSION['ww_builder']['user_id'];

include('includes/commanfunction.php');
$object= new COMMAN_Class();

$q = "SELECT * FROM user_projects WHERE user_id = '".$owner_id."' AND project_id = '".$_SESSION['idp']."'";
if($obj->db_num_rows($obj->db_query($q)) == 0){
?>
	<script language="javascript" type="text/javascript">
		window.location.href="<?=ACCESS_DENIED_SCREEN?>";
	</script>
<?php
}
$projectData = $obj->db_fetch_assoc($obj->db_query($q));
// get all Issue to 
$issueToSelect = $object->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'project_id = "'.$_SESSION['idp'].'" and is_deleted=0 group by issue_to_name  order by issue_to_name');?>
<!-- Ajax Post -->
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript" src="js/location_tree_jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<style type="text/css">
#locationsContainer, #attachments{ overflow-y:scroll; max-height:250px; min-height:200px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius:5px; border:1px solid; margin-top:15px; width:630px; }
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
.innerDiv{ float:left; border:1px solid red; width:120px; height:120px; margin:0px 0px 20px 30px; }
label.filebutton{ width:120px; height:40px; overflow:hidden; position:relative; }
label input { cursor: pointer; font-size: 7px; left: -1px; line-height: 0; margin: 0; opacity: 0; padding: 0; position: absolute; top: -2px; z-index: 999; filter:alpha(opacity=0);}
</style>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
<script language="javascript">
var align = 'center';									//Valid values; left, right, center
var top = 100; 											//Use an integer (in pixels)
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
	var validator = $("#addDefect").validate({
		rules:{
			defect_desc:{
				required: true
			},
			raisedBy:{
				required: true
			},
			location:{
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
			location:{
				required: '<div class="error-edit-profile">The location field is required</div>'
			},
			debug:true
		}
	});
	$("input.fixedByDate").each(function(){
		$(this).rules("add", {
			required: true,
			messages: {
				required: '<div class="error-edit-profile" style="width:120px;">The Fixed Date field is required</div>'
			}
		});			
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
		}
	});
});
$(function(){
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
			status2.html(response2);
			$('#removeImg3').show();
		}
	});
});
</script>
		<div class="content_hd" style="background-image:url(images/add_defect_hd.png);margin-top:0px\9;"></div>
		<br clear="all" />
		<?php if(isset($_SESSION['inspection_added'])) { ?>
		<div id="errorHolder" style="margin-left: 40px;margin-bottom: 20px;">
			<div class="success_r" style="height:35px;width:405px;"><p><?=$_SESSION['inspection_added'];?></p></div>
		</div>
		<?php unset($_SESSION['inspection_added']); } ?>
	<form action="ajax_reply.php" method="post" enctype="multipart/form-data" name="addDefect" id="addDefect">
<?php $q = "select location_id, location_title from project_locations where project_id = '".$_SESSION['idp']."' and location_parent_id = '0' and is_deleted = '0' order by location_title";
$re = mysql_query($q);
while($rw = mysql_fetch_array($re)){	$val[] = $rw;	}?>
		<div class="signin_form1" style="margin-top:-5px;margin-left:15px;">
			<font style="float:left;margin-left:25px;" size="+1">Project : <?=stripslashes($projectData['project_name'])?></font>
			<table width="100%" border="0" cellspacing="5" cellpadding="5">
				<tr>
					<td colspan="4">
					
<div id="locationsContainer"><br />
	<div style="margin-left:15px;float:left;">
		<span style="float:left;">Select Location <span class="req">*</span></span>
		<div id="location_exists" style="margin-left:15px;float:left;"></div><br />
	</div><br clear="all" />
<?php $i=0; if(!empty($val)){foreach($val as $locations){$i++;?>
	<ul class="telefilms"><!-- Use 'cookie1' as unique key to save cookie only for this tree -->
		<li id="li_<?php echo $locations['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations['location_id']?>" <?php if(isset($_SESSION['qc']['location']) && $_SESSION['qc']['subLocation']=='' ){ if($_SESSION['qc']['location'] == $locations['location_id']){ echo 'style="font-style: italic;font-weight: bold;text-decoration: underline;"'; } }?> ><?php echo $locations['location_title']?></span>
			<?php $q1 = "select location_id, location_title from project_locations where location_parent_id = '".$locations['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
				$re1 = mysql_query($q1);
				while($rw1 = mysql_fetch_array($re1)){	$val1[] = $rw1;	}
				if(!empty($val1)){foreach($val1 as $locations1){ ?>
				<ul>
					<li id="li_<?php echo $locations1['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations1['location_id']?>" <?php if(isset($_SESSION['qc']['location']) && isset($_SESSION['qc']['subLocation'])){ if($_SESSION['qc']['subLocation'] == $locations1['location_id']){ echo 'style="font-style: italic;font-weight: bold;text-decoration: underline;"'; } }?>><?php echo $locations1['location_title']?></span>
						<?php $q2 = "select location_id, location_title from project_locations where location_parent_id = '".$locations1['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
						$re2 = mysql_query($q2);
						while($rw2 = mysql_fetch_array($re2)){	$val2[] = $rw2;	}
						if(!empty($val2)){foreach($val2 as $locations2){ ?>
						<ul>
							<li id="li_<?php echo $locations2['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations2['location_id']?>"><?php echo $locations2['location_title']?></span>
							
								<?php $q3 = "select location_id, location_title from project_locations where location_parent_id = '".$locations2['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
								$re3 = mysql_query($q3);
								while($rw3 = mysql_fetch_array($re3)){	$val3[] = $rw3;	}
								if(!empty($val3)){foreach($val3 as $locations3){ ?>
								<ul>
									<li id="li_<?php echo $locations3['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations3['location_id']?>"><?php echo $locations3['location_title']?></span>
										<?php $q4 = "select location_id, location_title from project_locations where location_parent_id = '".$locations3['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
										$re4 = mysql_query($q4);
										while($rw4 = mysql_fetch_array($re4)){	$val4[] = $rw4;	}
										if(!empty($val4)){foreach($val4 as $locations4){ ?>
										<ul>
											<li id="li_<?php echo $locations4['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations4['location_id']?>" ><?php echo $locations4['location_title']?></span>
												<?php $q5 = "select location_id, location_title from project_locations where location_parent_id = '".$locations4['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
												$re5 = mysql_query($q5);
												while($rw5 = mysql_fetch_array($re5)){	$val5[] = $rw5;	}
												if(!empty($val5)){foreach($val5 as $locations5){ ?>
												<ul>
													<li id="li_<?php echo $locations5['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations5['location_id']?>" ><?php echo $locations5['location_title']?></span>
														<?php $q6 = "select location_id, location_title from project_locations where location_parent_id = '".$locations5['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
														$re6 = mysql_query($q6);
														while($rw6 = mysql_fetch_array($re6)){	$val6[] = $rw6;	}
														if(!empty($val6)){foreach($val6 as $locations6){ ?>
														<ul>
															<li id="li_<?php echo $locations6['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations6['location_id']?>" ><?php echo $locations6['location_title']?></span>
																<?php $q7 = "select location_id, location_title from project_locations where location_parent_id = '".$locations6['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																$re7 = mysql_query($q7);
																while($rw7 = mysql_fetch_array($re7)){	$val7[] = $rw7;	}
																if(!empty($val7)){foreach($val7 as $locations7){ ?>
																<ul>
																	<li id="li_<?php echo $locations7['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations7['location_id']?>" ><?php echo $locations7['location_title']?></span>
																		<?php $q8 = "select location_id, location_title from project_locations where location_parent_id = '".$locations7['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																		$re8 = mysql_query($q8);
																		while($rw8 = mysql_fetch_array($re8)){	$val8[] = $rw8;	}
																		if(!empty($val8)){foreach($val8 as $locations8){ ?>
																		<ul>
																			<li id="li_<?php echo $locations8['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations8['location_id']?>" ><?php echo $locations8['location_title']?></span>
																				<?php $q9 = "select location_id, location_title from project_locations where location_parent_id = '".$locations8['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																				$re9 = mysql_query($q9);
																				while($rw9 = mysql_fetch_array($re9)){	$val9[] = $rw9;	}
																				if(!empty($val9)){foreach($val9 as $locations9){ ?>
																				<ul>
																					<li id="li_<?php echo $locations9['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations9['location_id']?>" ><?php echo $locations9['location_title']?></span>
																						<?php $q10 = "select location_id, location_title from project_locations where location_parent_id = '".$locations9['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																							$re10 = mysql_query($q10);
																							while($rw10 = mysql_fetch_array($re10)){	$val10[] = $rw10;	}
																							if(!empty($val10)){foreach($val10 as $locations10){ ?>	
																							<ul>
																								<li id="li_<?php echo $locations10['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations10['location_id']?>" ><?php echo $locations10['location_title']?></span>
																									<?php $q11 = "select location_id, location_title from project_locations where location_parent_id = '".$locations10['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																									$re11 = mysql_query($q11);
																									while($rw11 = mysql_fetch_array($re11)){	$val11[] = $rw11;	}
																									if(!empty($val11)){foreach($val11 as $locations11){ ?>	
																									<ul>
																										<li id="li_<?php echo $locations11['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations11['location_id']?>" ><?php echo $locations11['location_title']?></span>
																											<?php $q12 = "select location_id, location_title from project_locations where location_parent_id = '".$locations11['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																											$re12 = mysql_query($q12);
																											while($rw12 = mysql_fetch_array($re12)){	$val12[] = $rw12;	}
																											if(!empty($val12)){foreach($val12 as $locations12){ ?>	
																											<ul>
																												<li id="li_<?php echo $locations12['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations12['location_id']?>" ><?php echo $locations12['location_title']?></span>
																												<?php $q13 = "select location_id, location_title from project_locations where location_parent_id = '".$locations12['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
																												$re13 = mysql_query($q13);
																												while($rw13 = mysql_fetch_array($re13)){	$val13[] = $rw13;	}
																												if(!empty($val13)){foreach($val13 as $locations13){ ?>	
																												<ul>
																													<li id="li_<?php echo $locations13['location_id']?>"><span class="jtree-button demo1" id="<?php echo $locations13['location_id']?>" ><?php echo $locations13['location_title']?></span>
																													</li>
																												</ul>
																											<?php }$val13 =array();}?>
																											</li>
																										</ul>
																									<?php }$val12 =array();}?>
																									</li>
																								</ul>
																							<?php }$val11 =array();}?>
																							</li>
																						</ul>
																					<?php }$val10 =array();}?>
																					</li>
																				</ul>
																			<?php }$val9 =array();}?>
																			</li>
																		</ul>
																	<?php }$val8 =array();}?>
																	</li>
																</ul>
															<?php }$val7 =array();}?>
															</li>
														</ul>
													<?php }$val6 =array();}?>
													</li>
												</ul>
											<?php }$val5 =array();}?>
											</li>
										</ul>
									<?php }$val4 =array();}?>
									</li>
								</ul>
							<?php }$val3 =array();}?>								
							</li>
						</ul>
					<?php }$val2 =array();}?>
					</li>
				</ul>
			<?php }$val1 =array();}?>
		</li>
	</ul>
<?php }$val=array();}else{?>
	<span style="margin:15px 0 0 15px;float:left;">No one location Found for this project.</span><br />
<?php }?>
</div>
	<input type="hidden" name="location" id="location"  />
	<input type="hidden" name="locationChecklist" id="locationChecklist"  />
<div class="contextMenu" id="myMenu2">
	<ul>
		<li id="select"><img src="images/add.png" align="absmiddle" width="14"  height="14"/> Select</li>
	</ul>
</div>					</td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top" width="110">Inspected By</td>
					<td>
						<input type="hidden" name="inspectedBy" id="inspectedBy" value="<?=$_SESSION['ww_logged_in_as']?>"  />
						<div style="background-color:#CCCCCC;-moz-border-radius:6px;-webkit-border-radius:6px;border-radius:6px;height: 27px;padding-top: 8px;padding-left: 10px;color:#030303;text-shadow:none;width:150px;behavior: url(css/PIE.htc);position:relative;"><?=$_SESSION['ww_logged_in_as']?></div>
					</td>
					
					
					<td nowrap="nowrap" valign="top" width="110">Raised By <span class="req">*</span></td>
					<td>
						<? if($_SESSION['userRole'] != 'All Defect'){?>
							<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
								<option value="">Select</option>
								<option value="<?=$_SESSION['userRole']?>" selected="selected" ><?=$_SESSION['userRole']?></option>
							</select>
						<?php }else{ ?>
							<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
								<option value="">Select</option>
								<option value="Builder">Builder</option>
								<option value="Architect">Architect</option>
								<option value="Structural Engineer">Structural Engineer</option>
								<option value="Services Engineer">Services Engineer</option>
								<option value="Superintendant">Superintendant</option>
								<option value="General Consultant">General Consultant</option>
								<option value="Client">Client</option>
								<option value="Purchaser">Purchaser</option>
							</select>						
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td nowrap="nowrap" width="110">Date Raised </td>
					<td>
						<div style="background-color:#CCCCCC;-moz-border-radius:6px;-webkit-border-radius:6px;border-radius:6px;height: 27px;padding-top: 8px;padding-left: 10px;color:#030303;text-shadow:none;behavior: url(css/PIE.htc);position:relative;"><?=date('d/m/Y')?></div>
						<input type="hidden" name="dateRaised" id="dateRaised" value="<?=date('d/m/Y')?>" />
						<input type="hidden" name="locationTree" id="locationTree" value=""  />
					</td>
					<td nowrap="nowrap">Inspection Type</td>
					<td colspan="2">
						<select class="select_box" id="defect_type" name="defect_type"  style="width:220px;background-image:url(images/selectSpl.png);margin-left:-5px;" >
							<option value="Issue"<?php if(isset($_SESSION['qc']['inspecrType'])){ if($_SESSION['qc']['inspecrType'] == 'Issue'){ echo 'selected="selected"'; } }?>>Issue</option>
							<option value="Defect" <?php if(isset($_SESSION['qc']['inspecrType'])){ if($_SESSION['qc']['inspecrType'] == 'Defect'){ echo 'selected="selected"'; }else{ echo 'selected="selected"'; } }?>>Defect</option>
							<option value="Warranty"<?php if(isset($_SESSION['qc']['inspecrType'])){ if($_SESSION['qc']['inspecrType'] == 'Warranty'){ echo 'selected="selected"'; } }?>>Warranty</option>
							<option value="Incomplete Works"<?php if(isset($_SESSION['qc']['inspecrType'])){ if($_SESSION['qc']['inspecrType'] == 'Incomplete Works'){ echo 'selected="selected"'; } }?>>Incomplete Works</option>
						</select>
					</td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top">Description <span class="req">*</span></td>
					<td colspan="3" align="left">
						<textarea id="defect_desc" name="defect_desc" style="background-image:url(images/text_detail_spl.png);width: 440px;height: 68px;font-family: Verdana, Arial, Helvetica, sans-serif;color:#333;font-size: 14px;background-color: transparent;background-repeat: no-repeat;border: none;padding:10px 20px 5px 20px;margin-left:5px;font-color:#000000;"></textarea><div style="position:absolute; margin-left: 486px;margin-top: -80px;"><img id="dropDown" src="images/downbox.png" border="0" style="background-color:none;" /></div>
					<div id="discriptionHide">
						<?php $standardDefects = $object->selQRYMultiple('description', 'standard_defects', 'project_id = '.$_SESSION['idp'].' and is_deleted=0 group by description order by description');
						if(!empty($standardDefects)){?>
							<ul style="list-style:none;margin-left:-30px;">
							<?php $i=0; foreach($standardDefects as $des){$i++;?>
								<li class="clickableLines"><?php echo $des['description'];?></li>
							<?php }?>
							</ul>
					<?php }else{?>
						<ul style="list-style:none;">
							<li class="clickableLines">No One Standard Defect Found !</li>
						</ul>
					<?php }?>
					</div>
						<?php if(isset($_SESSION['post_array']['defect_desc']) && $_SESSION['post_array']['defect_desc']==""){?>
							<lable htmlfor="fname" generated="true" class="error"><div class="error-edit-profile">The defect description field is required</div></lable>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td colspan="4">
						<div id="attachments" style="overflow:hidden;"><br />
							<span style="margin-left:15px;float:left;">Attachment</span><br />
			<div class="innerDiv"  style="margin-left:160px;" align="center" >
				<div style="height:120px;overflow:hidden;">
					<label class="filebutton" align="center">
					&nbsp;Browse Image 1
						<input type="file" id="image1" name="image1" style="width:120px;height:120px;" />
					</label>
					<div id="response_image_1" style="width:120px;">&nbsp;</div>
				</div>
				<img id="removeImg1" src="images/remove_img_btn.png" style="margin-top:10px;cursor:pointer;display:none;" onclick="removeImages('response_image_1', this.id);" />
			</div>
			
			<div class="innerDiv"  align="center">
				<div style="height:120px;overflow:hidden;">
					<label class="filebutton" align="center">
					Browse Image 2
						<input type="file" id="image2" name="image2" style="width:120px;height:120px;" />
					</label>
					<div id="response_image_2" style="width:120px;">&nbsp;</div>
				</div>
				<img id="removeImg2" src="images/remove_img_btn.png" style="margin-top:10px;cursor:pointer;display:none;" onclick="removeImages('response_image_2', this.id);" />
			</div>
			
			<div class="innerDiv" align="center">
				<div style="height:120px;overflow:hidden;">
					<label class="filebutton" align="center">
						Browse Drawing
						<input type="file" id="drawing" name="drawing" style="width:120px;height:120px;" />
					</label>
					<div id="response_drawing" style="width:120px;">&nbsp;</div>
				</div>
				<img id="removeImg3" src="images/remove_img_btn.png" style="margin-top:10px;cursor:pointer;display:none;" onclick="removeImages('response_drawing', this.id);" />
			</div>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="4" style="padding:5px;">
						<table width="100%" border="0" cellspacing="0" cellpadding="0" >
							<tr>
								<td width="135" style="padding:5px;">Issued To <span class="req">*</span></td>
								<td width="135" style="padding:5px;">Fix By Date <span class="req">*</span></td>
								<td width="135" style="padding:5px;">Cost Attribute</td>
								<td width="135" style="padding:5px;">Status</td>
							</tr>
							<tr>
								<td style="text-shadow:none;padding:5px;width:150px;">
								<img style="cursor:pointer;" onclick="AddItem();" src="images/inspectin_add.png" align="absmiddle" />
									<select name="issueTo[]" type="text" id="issueTo_0" class="issueTo" onchange="checkIssueTo(this.value, 0);">	
									<?php foreach($issueToSelect as $issueToName){?>
										<option value="<?php echo $cValue = trim(stripslashes($issueToName['issue_to_name']))?>"<?php if(isset($_SESSION['qc']['issuedTo'])){ if($_SESSION['qc']['issuedTo'] == $cValue){ echo 'selected="selected"'; }else{if($cValue == 'NA'){echo 'selected="selected"'; }}}?>><?=$cValue?></option>
									<?php }?>
									</select>
								</td>
								<td style="text-shadow:none;padding:5px;">
									<input name="fixedByDate[]" id="fixedByDate_0" class="fixedByDate" readonly="readonly" />
								</td>
								<td style="text-shadow:none;padding:5px;">
									<select name="costAttribute[]" id="costAttribute_0" class="issueTo">
										<option value="None"<?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == 'None'){ echo 'selected="selected"'; } }?>>None</option>
										<option value="Backcharge"<?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == 'Backcharge'){ echo 'selected="selected"'; } }?>>Backcharge</option>
										<option value="Variation"<?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == 'Variation'){ echo 'selected="selected"'; } }?>>Variation</option>
									</select>
								</td>
								<td style="text-shadow:none;padding:5px;" align="center">
									<select name="status[]" id="status_0" class="issueTo">
										<option value="Draft" value="Open"  onclick="return checkListCheck();" <?php if(isset($_SESSION['qc']['status'])){ if($_SESSION['qc']['status'] == 'Draft'){?> selected="selected" <?php  } } ?> >Draft</option>
									</select>
								</td>
							</tr>
							<tr id="hide_1" style="display:none;">
								<td style="text-shadow:none;padding:5px;width:150px;">
								<img style="cursor:pointer;" onclick="removeElement('hide_1');" src="images/inspectin_delete.png" align="absmiddle" />
									<select name="issueTo[]" type="text" id="issueTo_1" class="issueTo" onchange="checkIssueTo(this.value, 1);">	
									<?php foreach($issueToSelect as $issueToName){?>
										<option value="<?php echo $cValue = trim(stripslashes($issueToName['issue_to_name']))?>"<?php if(isset($_SESSION['qc']['issuedTo'])){ if($_SESSION['qc']['issuedTo'] == $cValue){ echo 'selected="selected"'; }else{if($cValue == 'NA'){echo 'selected="selected"'; }}}?>><?=$cValue?></option>
									<?php }?>
									</select>
								</td>
								<td style="text-shadow:none;padding:5px;">
									<input name="fixedByDate[]" id="fixedByDate_1" class="fixedByDate" readonly="readonly" />
								</td>
								<td style="text-shadow:none;padding:5px;">
									<select name="costAttribute[]" id="costAttribute_1" class="issueTo">
										<option value="None"<?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == 'None'){ echo 'selected="selected"'; } }?>>None</option>
										<option value="Backcharge"<?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == 'Backcharge'){ echo 'selected="selected"'; } }?>>Backcharge</option>
										<option value="Variation"<?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == 'Variation'){ echo 'selected="selected"'; } }?>>Variation</option>
									</select>
								</td>
								<td style="text-shadow:none;padding:5px;" align="center">
									<select name="status[]" id="status_1" class="issueTo">
										<option value="None" <?php if(isset($_SESSION['qc']['status'])){ if($_SESSION['qc']['status'] == 'Open'){ echo 'selected="selected"'; } }?>>Open</option>
										<option value="Backcharge" <?php if(isset($_SESSION['qc']['status'])){ if($_SESSION['qc']['status'] == 'Pending'){ echo 'selected="selected"'; } }?>>Pending</option>
										<option value="Variation" <?php if(isset($_SESSION['qc']['status'])){ if($_SESSION['qc']['status'] == 'Fixed'){ echo 'selected="selected"'; } }?>>Fixed</option>
										<option value="Variation" <?php if(isset($_SESSION['qc']['status'])){ if($_SESSION['qc']['status'] == 'Closed'){ echo 'selected="selected"'; } }?>>Closed</option>
									</select>
								</td>
							</tr>
							<tr id="hide_2" style="display:none;">
								<td style="text-shadow:none;padding:5px;width:150px;">
								<img style="cursor:pointer;" onclick="removeElement('hide_2');" src="images/inspectin_delete.png" align="absmiddle" />
									<select name="issueTo[]" type="text" id="issueTo_2" class="issueTo" onchange="checkIssueTo(this.value, 2);">	
									<?php foreach($issueToSelect as $issueToName){?>
										<option value="<?php echo $cValue = trim(stripslashes($issueToName['issue_to_name']))?>"<?php if(isset($_SESSION['qc']['issuedTo'])){ if($_SESSION['qc']['issuedTo'] == $cValue){ echo 'selected="selected"'; }else{if($cValue == 'NA'){echo 'selected="selected"'; }}}?>><?=$cValue?></option>
									<?php }?>
									</select>
								</td>
								<td style="text-shadow:none;padding:5px;">
									<input name="fixedByDate[]" id="fixedByDate_2" class="fixedByDate" readonly="readonly" />
								</td>
								<td style="text-shadow:none;padding:5px;">
									<select name="costAttribute[]" id="costAttribute_2" class="issueTo">
										<option value="None"<?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == 'None'){ echo 'selected="selected"'; } }?>>None</option>
										<option value="Backcharge"<?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == 'Backcharge'){ echo 'selected="selected"'; } }?>>Backcharge</option>
										<option value="Variation"<?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == 'Variation'){ echo 'selected="selected"'; } }?>>Variation</option>
									</select>
								</td>
								<td style="text-shadow:none;padding:5px;" align="center">
									<select name="status[]" id="status_2" class="issueTo">
										<option value="Draft" value="Open"  onclick="return checkListCheck();" <?php if(isset($_SESSION['qc']['status'])){ if($_SESSION['qc']['status'] == 'Draft'){?> selected="selected" <?php  } } ?> >Draft</option>
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td nowrap="nowrap" valign="top">Notes</td>
					<td colspan="3">
						<textarea class="text_area" id="defect_note" name="defect_note" style="background-image:url(images/text_detail_spl.png);width: 440px;height: 68px;font-family: Verdana, Arial, Helvetica, sans-serif;color:#333;font-size: 14px;background-color: transparent;background-repeat: no-repeat;border: none;padding:10px 20px 5px 20px;;margin-left:5px;"></textarea>
					</td>
				</tr>
				<tr>
					<td colspan="4">&nbsp;</td>
				</tr>
				<tr>
					<td nowrap="nowrap" style="padding:0px;">&nbsp;</td>
					<td nowrap="nowrap" style="padding:0px;" align="right">
						<input type="hidden" value="add_defect" name="sect" id="sect" />
						<input name="backButton" type="button" class="submit_btn" id="backButton" value="" style="background-image:url(images/back_btn.png); border:none; width:111px;height:44px;color:transparent;" onclick="goBack();"  />
					</td>
					<td nowrap="nowrap" style="padding:0px;">
						<input name="submit_action" type="submit" class="submit_btn" value="save_n_new" style="background-image:url(images/save_n_new.png); border:none; width:130px;height:44px;color:transparent;font-size:0px;" />
					</td>
					<td nowrap="nowrap" style="padding:0px;">
						<input name="submit_action" type="submit" class="submit_btn" value="save" style="background-image:url(images/save.png); border:none; width:111px;color:transparent;height:44px;font-size:0px;" />
					</td>
				</tr>
			</table>
			<?php unset($_SESSION['post_array']);?>
		</div>
	</form>
<!--<div id="spinner" style="z-index:100000"><div style="margin-top:240px;color:#000000;font-weight:bold;">Please Wait....<br/>This may take several minutes.</div></div>-->
<script type="text/javascript" src="js/jquery.tree.js"></script>
<script type="text/javascript" src="js/jquery.contextmenu.r2.js"></script>
<script>
var previousLocId = '';
var elementCount = 0;
var addedRow = new Array(); 
$(document).ready(function(){
	$('ul.telefilms').tree({default_expanded_paths_string : '0/0/0,0/0/2,0/2/4'}); 
<?php if($_SESSION['qc']['location'] != ''){?>
	showProgress();	
	var locationID = <?php echo $_SESSION['qc']['subLocation'] == '' ? $_SESSION['qc']['location']  : $_SESSION['qc']['subLocation']?>;
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
function removeElement(removeID){
	var r = jConfirm('Do you want to delete Issue To ?', null, function(r){
		if (r==true){
			elementCount--;
			document.getElementById(removeID).style.display = 'none';
			addedRow.pop();
		}
	});
}
function AddItem() {
	addedRow.push(++elementCount);
	if(addedRow.length < 3){
		if(document.getElementById('hide_'+elementCount).style.display == 'none'){
			document.getElementById('hide_'+elementCount).style.display = 'table-row';
		}else{
			document.getElementById('hide_1').style.display = 'table-row';
		}
	}else{
		jAlert("You can't add more than 3 Issue To !");
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
		}
	});
}
$(document).ready(function(){
	<?php if($_SESSION['qc']['subLocation'] != ''){?>
		$('#li_<?php echo $_SESSION['qc']['location'];?>').children("ul").show('slow');
	<?php }?>
});
$("#dropDown").click(function () {
	if ($("#discriptionHide").is(":hidden")) { $("#discriptionHide").slideDown("slow"); }else{ $("#discriptionHide").hide("slow");}
});
$(".clickableLines").click(function(){ $("#defect_desc").val(this.innerHTML); $("#discriptionHide").hide("slow");});
function taggingIssueTo(locationName, parenetLocationName){
	if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	params = "LocationName="+locationName+"&parentLocationName="+parenetLocationName+"&&projectID=<?=$_SESSION['idp'];?>&uniqueId="+Math.random();
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
	params = "LocationName="+locationName+"&parentLocationName="+parenetLocationName+"&projectID=<?=$_SESSION['idp'];?>&uniqueId="+Math.random();
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
<style>
input[type=checkbox] { position: relative;  cursor:pointer; opacity:1; filter:alpha(opacity=100;)}
label.label_check {cursor:pointer;}
</style>
<? unset($_SESSION['checkList']); ?>