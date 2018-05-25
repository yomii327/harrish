<?php
//if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){?>

<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}

include_once("includes/commanfunction.php");
$object = new COMMAN_Class();

$builder_id=$_SESSION['ww_builder_id'];

// get standard defects list created by current manager 
$qd=$obj->db_query("SELECT * FROM ".DEFECTSLIST." WHERE fk_b_id='$builder_id'");

// get all other managers created by comman company
$qs=$obj->db_query("SELECT b.user_id,b.user_fullname FROM ".BUILDERS." b 
					LEFT JOIN ".SUBBUILDERS." sb 
					ON b.user_id=sb.sb_id 
					WHERE b.fk_c_id = '".$_SESSION['ww_builder_fk_c_id']."' 
					AND b.user_id!='$builder_id' 
					GROUP BY b.user_id ");

$projectTypeList = $object->selQRYMultiple("project_type_name", "project_type_list", "is_deleted=0");
?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/edit_project.js"></script>
<script language="javascript" type="text/javascript">
function startAjax(){
	var protype=document.getElementById('protype').value;
	var name=document.getElementById('name').value;
	var line1=document.getElementById('line1').value;
	var suburb=document.getElementById('suburb').value;
	var state=document.getElementById('state').value;
	var postcode=document.getElementById('postcode').value;
	var country=document.getElementById('country').value;
	
	if(protype!='' && name!='' && line1!='' && suburb!='' && state!='' && postcode!='' && country!=''){
		document.getElementById('sign_in_process').style.visibility = 'visible';
		document.getElementById('sign_in_response').style.visibility = 'hidden';
		return true;
	}else{
		var err = '<span class="sign_emsg">* represent required fileds!<\/span><br/><br/>';
		document.getElementById('sign_in_response').innerHTML = err;
		return false;
	}
	
	document.getElementById('sign_in_process').style.visibility = 'visible';
	document.getElementById('sign_in_response').style.visibility = 'hidden';
	return true;
}

function stopAjax(success){
	var result = '';
	if(success == 0){
		result = '<span class="sign_emsg">* represent required fileds!<\/span><br/><br/>';
	}else if(success == 2){
		result = '<span class="sign_emsg">Invalid Associate To Name!<\/span><br/><br/>';
	}else if(success == 1){
		result = '<span class="sign_msg">Project added successfully!<\/span><br/><br/>';
		
		// reset form
		document.getElementById('protype').value='';
		document.getElementById('name').value='';
		document.getElementById('line1').value='';
		document.getElementById('line2').value='';
		document.getElementById('suburb').value='';
		document.getElementById('state').value='';
		document.getElementById('postcode').value='';
		document.getElementById('country').value='';
	}
	document.getElementById('sign_in_process').style.visibility = 'hidden';
	document.getElementById('sign_in_response').innerHTML = result;
	document.getElementById('sign_in_response').style.visibility = 'visible';	
	
	return true;
}
</script>
<!-- Ajax Post -->
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
</style>
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form action="ajax_reply.php" method="post"  enctype="multipart/form-data" name="editproject" id="editproject" >
		
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1 page-main-heading" style="background-image:url(images/add_projectt_hd.png);margin-top:-50px\9;">Add Project</div>
					<div id="sign_in_process" style="width:900px;"><br />Sending request...<br/>
						<img src="images/loader.gif" /><br/>
					</div>
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
							<tr>
								<td valign="top">Project Name <span class="req">*</span></td>
								<td><input name="name" type="text" class="input_small" id="name" /></td>
							</tr>
							<tr>
								<td nowrap="nowrap" valign="top">Project Type <span class="req">*</span></td>
								<td>
                                                                        <select name="protype" id="protype" class="select_box" style="margin-left:0px;">
                                                                             <option value="" selected="selected">select</option>
                                                                             <?php foreach($projectTypeList as $listData){ ?>
                                                                             	<option value="<?php echo $listData['project_type_name']; ?>"><?php echo $listData['project_type_name']; ?></option>
                                                                             <?php } ?>
                                                                         </select>
                                                                </td>
							</tr>

							<tr style=" <?php echo (isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company']==1)?'':'display:none;'?>">
								<td valign="top" nowrap="nowrap">Company Name <span class="req">*</span></td>
								<td valign="top">
									<?php $compname = isset($_SESSION['post_array']['compname'])?$_SESSION['post_array']['compname']:array();
									$getCompanyData = $object->selQRYMultiple('id, company_name', 'organisations', 'is_deleted = 0 '); ?>
									<select name="company" id="company" class="select_box" style="margin-left:0px;">
										<!--option value="">Select</option-->
										<?php	if(isset($getCompanyData)){ 
											foreach($getCompanyData as $company){	?>
												<option value="<?=$company['id']?>"
													<?php if(in_array($company['company_name'], $compname)){ ?>
														selected="selected"
													<?php } ?> >
													<?=$company['company_name']?>
												</option>
											<?php 	}
										}
										if((isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder']>0)){
										echo '<option value="'.$_SESSION['companyId'].'" selected="selected">'.$_SESSION['companyName'].'</option>';
										}
										?>
									</select>
									<?php if(isset($_SESSION['post_array']['compname']) && $_SESSION['post_array']['compname']==""){?>
										<lable htmlfor="fname" generated="true" class="error">
										<	div class="error-edit-profile">The company name field is required</div>
										</lable>
									<?php } ?>
								</td>
							</tr>

                                                        <?php
                                                        if(isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']==1){
                                                        ?>
                                                        <tr>
								<td nowrap="nowrap" valign="top">Manager User <span class="req">*</span></td>
								<td>
                                                                    <select name="builder[]" id="builder" multiple="" class="select_box" style="background-image: url('images/texarea_select_box.png');height: 76px;
    margin-left: 1px;width: 300px;">
                                                                             <option value="" selected="selected">select</option>
                                                                            <?php 
                                                                            // get all other managers created by comman company
                                                                                    $managerUsers=$obj->db_query("SELECT b.user_id,b.user_fullname FROM ".BUILDERS." b WHERE user_type = 'manager'  AND  b.is_deleted='0' GROUP BY b.user_id");	
                                                                                    $managerList=array();
                                                                                    while($managerList=mysql_fetch_assoc($managerUsers)){
                                                                                        ?>
                                                                                        <option value="<?php echo $managerList['user_id'] ?>"><?php echo $managerList['user_fullname']; ?></option>
                                                                                    <?php }?>

                                                                            
                                                                         </select>
                                                                </td>
							</tr>
                                                        <?php }  ?>
                                                        
							<tr>
								<td nowrap="nowrap" valign="top">Address Line 1 <span class="req">*</span></td>
								<td><input name="line1" type="text" class="input_small" id="line1" /></td>
							</tr>
							<tr>
								<td width="133" valign="top">Address Line 2</td>
								<td width="252"><input name="line2" type="text" class="input_small" id="line2" /></td>
							</tr>
							<tr>
								<td valign="top">Suburb <span class="req">*</span></td>
								<td><input name="suburb" type="text" class="input_small" id="suburb" /></td>
							</tr>
							<tr>
								<td valign="top">State <span class="req">*</span></td>
								<td><input name="state" type="text" class="input_small" id="state" /></td>
							</tr>							
							<tr>
								<td nowrap="nowrap" valign="top">Postcode <span class="req">*</span></td>
								<td><input name="postcode" type="text" class="input_small" id="postcode" /></td>
							</tr>
							<tr>
								<td valign="top">Country <span class="req">*</span></td>
								<td>
                                <select name="country" id="country" class="select_box" style="margin-left:2px;">
                                <option value="Australia">Australia</option>
                                <option value="Singapore">Singapore</option>
                                </select>
                                </td>
							</tr>

							<tr>
								<th colspan="2" align="left" valign="top" nowrap="nowrap">Project Manager</th>
							</tr>
							<tr>
								<td nowrap="nowrap" valign="top">Name</td>
								<td><input name="projectManager" type="text" class="input_small" id="projectManager" /></td>
							</tr>
							<tr>
								<td nowrap="nowrap" valign="top">Email</td>
								<td><input name="projManagerEmail" type="text" class="input_small" id="projManagerEmail" /></td>
							</tr>
							<tr>
								<th colspan="2" align="left" valign="top" nowrap="nowrap">Contact Person</th>
							</tr>
							<tr>
								<td nowrap="nowrap" valign="top">Name</td>
								<td><input name="contactPerson" type="text" class="input_small" id="contactPerson" /></td>
							</tr>
							<tr>
								<td nowrap="nowrap" valign="top">Email</td>
								<td><input name="contactPersonEmail" type="text" class="input_small" id="contactPersonEmail" /></td>
							</tr>

							<tr>
							  <td nowrap="nowrap" valign="top">Defect Clause</td>
							  <td><textarea name="defectClause" class="text_area_small" id="defectClause"></textarea></td>
						  	</tr>

						  	<tr>
							  <td nowrap="nowrap" valign="top">Email on Sync</td>
							  <td>Yes <input type="checkbox" name="allow_sync" id="allow_sync" value="Yes" /></td>
						  	</tr>

							<tr>
								<td nowrap="nowrap" valign="top">Sync project to ipad</td>
								<td>Yes <input type="checkbox" name="allow_sync_to_ipad" id="allow_sync_to_ipad" value="Yes" checked="checked"/></td>
							</tr>

							<tr>
								<td>&nbsp;</td>
								<td>
									<input type="hidden" value="add_project" name="sect" id="sect" />
									<!-- <input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/submit.png); border:none; width:111px;" /> -->
									<input name="button" type="submit" class="green_small" id="button" style="cursor:pointer;" value="Submit"/>
                                    
                                    <!-- <a href="javascript:void();" onclick="history.back();"><input name="passwordUpdate" type="button" class="submit_btn" id="button" value="" style="background-image:url(images/back_btn.png); width:111px; border:none;"></a> -->
                                    <input class="green_small" href="javascript:void();" onclick="history.back();" style="cursor:pointer; width:45px;" value="Back">
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="content_right">
					<div class="signin_form1">
						<!--<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
							<tr>
								<td valign="top">Standard Defects List</td>
								<td valign="top">
								<div class="list">
								<?php
								/*if($obj->db_num_rows($qd)>0){
									while($fd=$obj->db_fetch_assoc($qd)){
								?>
								<input name="defectList[]" type="checkbox" value="<?=$fd['dl_id']?>" />
								&nbsp;
								<label title="<?=stripslashes($fd['dl_title'])?>">
								<?php
								if(strlen($fd['dl_title'])>30){
									echo $obj->truncate_text(stripslashes($fd['dl_title']),27,' ...');
								}else{
									echo stripslashes($fd['dl_title']);
								}								
								?>
								</label>
								<br />
								<?php
									}
								}else{
								?>
								<a href="?sect=add_defects_list"><img src="images/add_new.png" style="border:none;" /></a>
								<?php }*/	?>
								</div>
								</td>
							</tr>-->
							<!--<tr>
								<td valign="top" nowrap="nowrap">Associate Other Manager(s)</td>
								<td valign="top">
								<div class="list">
								<?php
								
								//if($obj->db_num_rows($qs)>0){
									//while($fs=$obj->db_fetch_assoc($qs)){
								?>
								<input name="associateTo[]" type="checkbox" value="<?//=$fs['user_id']?>" />
								&nbsp;
								<label title="<? //= stripslashes($fs['user_fullname'])?>">
								<?php
								//if(strlen($fs['user_fullname'])>30){
									//echo $obj->truncate_text(stripslashes($fs['user_fullname']),27,' ...');
							//	}else{
								//	echo stripslashes($fs['user_fullname']);
								//}								
								?>
								</label>
								<br />
								<?php
									//}
							//	}else{
								?>
								No Manager(s) Found
								<?php //}	?>
								</div>
								</td>
							</tr>				
						</table>-->		
					</div>
				</div>
			</div>
		</form>
	</div>
</div>