<?php //if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){?>
	<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }

include_once("includes/commanfunction.php");
$object = new COMMAN_Class();
#echo "<pre>";print_r($_POST); die;
$id=base64_decode($_REQUEST['id']);
$_SESSION['proj_id']=$id;

$builder_id=$_SESSION['ww_builder_id'];
//echo $q; die;
//$q="SELECT * FROM ".PROJECTS." WHERE project_id = '$id'"; 
if (isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company'] == 1) {
	$userId = $_SESSION['ww_is_company']; 
}else{
	$userId = $_SESSION['ww_builder_id']; 
}

$q = "SELECT p.* FROM user_projects as up, projects as p WHERE up.project_id = ".$id." AND p.project_id = up.project_id ";
if (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1) {
    $q.=" AND user_id = '$userId' ";
}

$projectTypeList = $object->selQRYMultiple("project_type_name", "project_type_list", "is_deleted=0");

if($obj->db_num_rows($obj->db_query($q)) == 0){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=ACCESS_DENIED_SCREEN?>";
</script>
<?php }
$b1=array();
$b2=array();
$b3=array();

$d1=array();
$d2=array();
$d3=array();
$f=$obj->db_fetch_assoc($obj->db_query($q));

// get builders list which were created by a common company
$qb=$obj->db_query("SELECT * FROM ".BUILDERS." 
					WHERE fk_c_id = '".$_SESSION['ww_builder_fk_c_id']."' 
					AND user_id!='$builder_id' ");
						
if($obj->db_num_rows($qb)>0){					
	
	$i=0;
	while($fb=$obj->db_fetch_assoc($qb)){
		$b1['user_id'][$i]=$fb['user_id'];
		$b1['cname'][$i]=$fb['user_fullname'];
		$b1['checked'][$i]="";
		$i++;
	}

	if($obj->db_num_rows($obj->db_query("SELECT * FROM ".SUBBUILDERS." WHERE fk_p_id = '".$id."' AND fk_b_id='$builder_id'"))>0){
		$qs=$obj->db_query("SELECT * FROM ".SUBBUILDERS." WHERE fk_p_id = '".$id."' AND fk_b_id='$builder_id'");
		
		$i=0;
		while($fs=$obj->db_fetch_assoc($qs)){
			$b2['id'][$i]=$fs['sb_id'];
			$i++;
		}
		
		// now map both arrays
		for($i=0;$i<count($b1['id']);$i++){	
			$flag=0;	
			for($j=0;$j<count($b2['id']);$j++){
			
				if($b1['id'][$i]==$b2['id'][$j]){
					$flag=1;
					break;
				}
			}
			if($flag==1){
				$b3['id'][$i]=$b1['id'][$i];
				$b3['cname'][$i]=$b1['cname'][$i];
				$b3['checked'][$i]="checked";
			}else{
				$b3['id'][$i]=$b1['id'][$i];
				$b3['cname'][$i]=$b1['cname'][$i];
				$b3['checked'][$i]="";
			}
		}
	}else{
		$b3=$b1;
	}	
}
///////////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////// Defects List ////////////////////////////
$qd=$obj->db_query("SELECT * FROM ".DEFECTSLIST." WHERE fk_b_id='$builder_id'");

if($obj->db_num_rows($qd)>0){	
	$i=0;
	while($fd=$obj->db_fetch_assoc($qd)){
		$d1['dl_id'][$i]=$fd['dl_id'];
		$d1['dl_title'][$i]=$fd['dl_title'];
		$d1['checked'][$i]="";
		$i++;
	}

	if($obj->db_num_rows($obj->db_query("SELECT * FROM ".PROJECTDEFECTS." WHERE fk_b_id='$builder_id' AND fk_p_id='$id'"))>0){
		$qd=$obj->db_query("SELECT * FROM ".PROJECTDEFECTS." WHERE fk_b_id='$builder_id' AND fk_p_id='$id'");
		$i=0;
		while($fd=$obj->db_fetch_assoc($qd)){
			$d2['fk_dl_id'][$i]=$fd['fk_dl_id'];
			$i++;
		}
		
		// now map both arrays
		for($i=0;$i<count($d1['dl_id']);$i++){	
			$flag=0;	
			for($j=0;$j<count($d2['fk_dl_id']);$j++){
			
				if($d1['dl_id'][$i]==$d2['fk_dl_id'][$j]){
					$flag=1;
					break;
				}
			}
			if($flag==1){
				$d3['dl_id'][$i]=$d1['dl_id'][$i];
				$d3['dl_title'][$i]=$d1['dl_title'][$i];
				$d3['checked'][$i]="checked";
			}else{
				$d3['dl_id'][$i]=$d1['dl_id'][$i];
				$d3['dl_title'][$i]=$d1['dl_title'][$i];
				$d3['checked'][$i]="";
			}
		}
	}else{
		$d3=$d1;
	}
}
	if(isset($_POST['update_x'])){
		$companyId = $_POST['company'];
		$getCompanyName = $object->selQRYMultiple("group_concat(company_name SEPARATOR ', ') AS compname", 'organisations', 'is_deleted = 0 AND id IN('.$companyId.') GROUP BY is_deleted ');
		#echo "<pre>"; print_r($getCompanyName); die;
		$builder_id = $_SESSION['ww_builder_id'];
		$pro_id=$_POST['pro_id'];
		$name=mysql_real_escape_string(trim($_POST['name']));
		$protype=mysql_real_escape_string(trim($_POST['protype']));
		$compname=mysql_real_escape_string(trim($getCompanyName[0]['compname']));
		$companyId=mysql_real_escape_string(trim($companyId));
		$line1=mysql_real_escape_string(trim($_POST['line1']));
		$line2=mysql_real_escape_string(trim($_POST['line2']));
		$suburb=mysql_real_escape_string(trim($_POST['suburb']));
		$state=mysql_real_escape_string(trim($_POST['state']));
		$postcode=mysql_real_escape_string(trim($_POST['postcode']));
		$country=mysql_real_escape_string(trim($_POST['country']));
		$projectManager = mysql_real_escape_string(trim($_POST['projectManager']));
		$projManagerEmail = mysql_real_escape_string(trim($_POST['projManagerEmail']));
		$contactPerson = mysql_real_escape_string(trim($_POST['contactPerson']));
		$contactPersonEmail = mysql_real_escape_string(trim($_POST['contactPersonEmail']));
		$defectClause = mysql_real_escape_string(trim($_POST['defectClause']));

		$allow_sync = mysql_real_escape_string(trim($_POST['allow_sync']));
		$allow_sync = (!empty($allow_sync))?$allow_sync:'No';

		$allow_sync_to_ipad = mysql_real_escape_string(trim($_POST['allow_sync_to_ipad']));
		$allow_sync_to_ipad = (!empty($allow_sync_to_ipad))?$allow_sync_to_ipad:'No';

		if(isset($_POST['associateTo']) && !empty($_POST['associateTo'])){
			$associateTo=$_POST['associateTo']; 
		}else{
				$associateTo=0;
		}
		if($protype=='' || $name=='' || $line1=='' || $suburb=='' || $state=='' || $postcode=='' || $country==''){
			$result = 0;
		}else{	
					$q = "UPDATE ".PROJECTS." SET
							project_name = '".$name."',	
							project_type = '".$protype."',
							project_address_line1 = '".$line1."',
							project_address_line2 = '".$line2."',
							company_id = '".$companyId."',
							company_name = '".$compname."',
							project_suburb = '".$suburb."',
							project_state = '".$state."',
							project_postcode = '".$postcode."',
							project_country = '".$country."',
							project_manager = '".$projectManager."',
							project_manager_email = '".$projManagerEmail."',
							contact_person = '".$contactPerson."',
							contact_person_email = '".$contactPersonEmail."',
							defect_clause = '".$defectClause."',
							allow_sync = '".$allow_sync."',
							project_is_synced = '".$allow_sync_to_ipad."',
							last_modified_date = NOW(),
							last_modified_by = ".$userId." 
						WHERE
							project_id = '".$pro_id."'";
				
				$obj->db_query($q);
			
		// Update records in Project New Introduce Table Dated : 28/05/2012
				$qProject = "UPDATE projects SET
								project_name = '".$name."',
								project_type = '".$protype."',
								project_address_line1 = '".$line1."',
								project_address_line2 = '".$line2."',
								company_id = '".$companyId."',
								company_name = '".$compname."',
								project_suburb = '".$suburb."',
								project_state = '".$state."',
								project_postcode = '".$postcode."',
								project_country = '".$country."',
								project_manager = '".$projectManager."',
								project_manager_email = '".$projManagerEmail."',
								contact_person = '".$contactPerson."',
								contact_person_email = '".$contactPersonEmail."',
								defect_clause = '".$defectClause."',
								allow_sync = '".$allow_sync."',
								project_is_synced = '".$allow_sync_to_ipad."',
								last_modified_date = NOW(),
								last_modified_by = ".$userId."
							WHERE
								project_id = '".$pro_id."'";
				$obj->db_query($qProject);
		// Update records in Project New Introduce Table Dated : 28/05/2012

			
			// if $defectList is set
				$obj->db_query("DELETE FROM ".PROJECTDEFECTS." WHERE fk_b_id='$builder_id' fk_p_id='$pro_id'");
		
			// if $associateTo is set
				$obj->db_query("DELETE FROM ".SUBBUILDERS." WHERE fk_p_id='$pro_id' AND fk_b_id='$builder_id'");
				if(sizeof($associateTo)>0)
				{			
				// if builder exists
					for($a=0; $a<sizeof($associateTo); $a++)
					{
						if($obj->db_num_rows($obj->db_query("SELECT user_id FROM ".BUILDERS." WHERE user_id='".$associateTo[$a]."'"))>0)
						{				
							$obj->db_query("INSERT INTO ".SUBBUILDERS." (fk_p_id,fk_b_id,sb_id) VALUES('$pro_id','$builder_id','".$associateTo[$a]."')");
						}
					}
				}
				$result = 1;
				$_SESSION['builder_seccuess_update']='Project updated successfully!';
				$id=$_SESSION['proj_id'];
				$hb=$_SESSION['hb'];
				$id=base64_encode($id);
				$hb=base64_encode($hb);
				header('location:pms.php?sect=add_project_detail&id='.$id.'&hb='.$hb);		
			}
		}

	
	if(isset($_POST['remove_x']))
	{
		
		$builder_id = $_SESSION['ww_builder_id'];
		$pro_id=$_POST['pro_id'];
		// check for managers
			$qm=$obj->db_num_rows($obj->db_query("SELECT bsb_id FROM ".SUBBUILDERS." WHERE fk_p_id='$pro_id' AND fk_b_id='$builder_id'"));
		// check for inspectors
			$qi=$obj->db_num_rows($obj->db_query("SELECT id FROM ".OWNERS." WHERE ow_project_id='$pro_id'"));
		// check for defects
			$qd=$obj->db_num_rows($obj->db_query("SELECT df_id FROM ".DEFECTS." WHERE project_id='$pro_id' AND status!='Closed'"));
		// check for trades
			$qt=$obj->db_num_rows($obj->db_query("SELECT resp_id FROM ".RESPONSIBLES." WHERE project_id='$pro_id' AND builder_id='$builder_id'"));
		
			if($qm > 0)
			{
				$result = 2;
				$_SESSION['error_project']='Project is associated with some other Managers.<br/>Please take it back!';
				header('location:sect=pms.php?sect=edit_project&id='.base64_decode($_SESSION['proj_id']));
			}
			elseif($qi > 0)
			{
				$result = 3;
				$_SESSION['error_project']='Project is associated with some Inspectors.<br/>Please take it back!';
				header('location:sect=pms.php?sect=edit_project&id='.base64_decode($_SESSION['proj_id']));
			}
			elseif($qt > 0)
			{
				$result = 4;
				$_SESSION['error_project']='Project is associated with some Trades.<br/>Please take it back!';
				header('location:sect=pms.php?sect=edit_project&id='.base64_decode($_SESSION['proj_id']));
			}
			elseif($qd > 0)
			{
				$result = 5;
				$_SESSION['error_project']='Project is related with Some Inspections.<br/>Please closed them!';
				header('location:sect=pms.php?sect=edit_project&id='.base64_decode($_SESSION['proj_id']));
			}
			else
			{
			// remove record from PROJECTS
				$update="UPDATE user_projects SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE project_id ='$pro_id'";
				//echo $update; die;
				$obj->db_query($update);
			
			// remove records from SUBBUILDERS
				$obj->db_query("update ".SUBBUILDERS." SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = '".$_SESSION['ww_builder_id']."' WHERE fk_p_id='$pro_id'");
			
			// remove records from OWNERS
				$obj->db_query("update ".OWNERS."  SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = '".$_SESSION['ww_builder_id']."' WHERE ow_project_id='$pro_id'");
			
			// remove records from DEFECTS
				$obj->db_query("update ".DEFECTS." SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = '".$_SESSION['ww_builder_id']."' WHERE project_id='$pro_id' AND status='Closed'");
			
			// remove records from RESPONSIBLES
				$obj->db_query("update ".RESPONSIBLES." SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = '".$_SESSION['ww_builder_id']."' WHERE project_id='$pro_id'");
			
			// remove records from Project New Introduce Table Dated : 28/05/2012
				//$obj->db_query("update projects is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE project_id = '$pro_id'",'Y');                                      
                               
				$obj->db_query("update projects SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = '".$_SESSION['ww_builder_id']."' WHERE project_id = '$pro_id'");
                                
			// remove records from Project New Introduce Table Dated : 28/05/2012
                                 
				$result = 6;
				$_SESSION['remove_project']='Project deleted successfully';
				header('location:pms.php?sect=show_project');	
				
			}
		}	


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
		result = '<span class="sign_emsg">*represent required fileds!<\/span><br/><br/>';
	}else if(success == 1){
		result = '<span class="sign_msg">Project update successfully!<\/span><br/><br/>';
	}else if(success == 2){
		result = '<span class="sign_emsg">Project is associated with some other Managers.<br/>Please take it back!<\/span><br/><br/>';
	}else if(success == 3){
		result = '<span class="sign_emsg">Project is associated with some Inspectors.<br/>Please take it back!<\/span><br/><br/>';
	}else if(success == 4){
		result = '<span class="sign_emsg">Project is associated with some Trades.<br/>Please take it back!<\/span><br/><br/>';
	}else if(success == 5){
		result = '<span class="sign_emsg">Project is related with Some Inspections.<br/>Please closed them!<\/span><br/><br/>';
	}else if(success == 6){
		result = '<span class="sign_msg">Project removed successfully!<\/span><br/><br/>';
	}
	
	document.getElementById('sign_in_process').style.visibility = 'hidden';
	document.getElementById('sign_in_response').innerHTML = result;
	document.getElementById('sign_in_response').style.visibility = 'visible';	
	
	return true;
}
</script>
<!--// Ajax Post -->
<!-- CSS -->
<style>
.defect_list{border:1px solid; -moz-border-radius:8px; border-radius:8px; overflow:auto; max-height:100px; padding:5px;}
.builder_list{border:1px solid; -moz-border-radius:8px; border-radius:8px; overflow:auto; max-height:245px; padding:5px;}
</style>
<!--// CSS -->
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form  method="post"  enctype="multipart/form-data" name="editproject" id="editproject">
		
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1 page-main-heading" style="background-image:url(images/edit_project.png);margin-top:-50px\9;">Edit Project</div>
					<div id="sign_in_process" style="width:900px;"><br />Sending request...<br/>
						<img src="images/loader.gif" /><br/>
					</div>
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
							
                            <?php 
								if(isset($_SESSION['error_project']))
								{
							
							?>
                            	<tr>
                                	<td colspan="2"><div class="success_r" style="width:250px;"><p><?php echo $_SESSION['error_project'];?></p></div></td>
                               <?php
								}
								?>
                            
                            
                            <tr>
								<td>Project Name <span class="req">*</span></td>
								<td><input name="name" type="text" class="input_small" id="name" value="<?=stripslashes($f['project_name'])?>" /></td>
							</tr>
							<tr>
								<td nowrap="nowrap">Project Type <span class="req">*</span></td>
								<td>
                                <select name="protype" id="protype" class="select_box" style="margin-left:0px;">
                                    	<option value="">select</option>
										<?php foreach($projectTypeList as $listData){ ?>
	                                     	<option value="<?php echo $listData['project_type_name']; ?>" <?php if($f['project_type']==$listData['project_type_name']) { ?> selected="selected" <?php } ?> ><?php echo $listData['project_type_name']; ?></option>
	                                    <?php } ?>
                                    </select>
                                
                               </td>
							</tr>

							<tr style=" <?php echo (isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company']==1)?'':'display:none;'?>">
								<td valign="top" nowrap="nowrap">Company Name <span class="req">*</span></td>
								<td valign="top">
									<?php $compname = isset($f['company_id'])?$f['company_id']:'';
									$getCompanyData = $object->selQRYMultiple('id, company_name', 'organisations', 'is_deleted = 0 '); ?>
									<select name="company" id="company" class="select_box" style="margin-left:0px;">
										<!--option value="">Select</option-->
										<?php	if(isset($getCompanyData)){ 
											foreach($getCompanyData as $company){	?>
												<option value="<?=$company['id']?>"
												<?php if($company['id'] == $compname){ ?>
													selected="selected"
												<?php } ?> >
												<?=$company['company_name']?></option>
											<?php 	}
										}
										if((isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder']>0) && empty($compname)){
										echo '<option value="'.$_SESSION['companyId'].'" selected="selected">'.$_SESSION['companyName'].'</option>';
										}
										?>
									</select>
									<?php if(isset($_SESSION['post_array']['compname']) && $_SESSION['post_array']['compname']==""){?>
										<lable htmlfor="fname" generated="true" class="error">
											<div class="error-edit-profile">The company name field is required</div>
										</lable>
									<?php } ?>
								</td>
							</tr>

							<tr>
								<td nowrap="nowrap">Address Line 1 <span class="req">*</span></td>
								<td><input name="line1" type="text" class="input_small" id="line1" value="<?=stripslashes($f['project_address_line1'])?>" /></td>
							</tr>
							<tr>
								<td width="133">Address Line 2</td>
								<td width="252"><input name="line2" type="text" class="input_small" id="line2" value="<?=stripslashes($f['project_address_line2'])?>" /></td>
							</tr>
							<tr>
								<td>Suburb <span class="req">*</span></td>
								<td><input name="suburb" type="text" class="input_small" id="suburb" value="<?=stripslashes($f['project_suburb'])?>" /></td>
							</tr>
							<tr>
								<td>State <span class="req">*</span></td>
								<td><input name="state" type="text" class="input_small" id="state" value="<?=stripslashes($f['project_state'])?>" /></td>
							</tr>							
							<tr>
								<td nowrap="nowrap">Postcode <span class="req">*</span></td>
								<td><input name="postcode" type="text" class="input_small" id="postcode" value="<?=stripslashes($f['project_postcode'])?>" /></td>
							</tr>
							<tr>
								<td>Country <span class="req">*</span></td>
								<td> <select name="country" id="country" class="select_box" style="margin-left:2px;">
                                <option value="Australia" <?php if($f['project_country']=='Australia') { ?> selected="selected" <?php } ?>>Australia</option>
                                <option value="Singapore" <?php if($f['project_country']=='Singapore') { ?> selected="selected" <?php } ?>>Singapore</option>
                                </select></td>
							</tr>

							<tr>
							  <th colspan="2" align="left" valign="top" nowrap="nowrap">Project Manager</th>
						  </tr>
							<tr>
							  <td nowrap="nowrap" valign="top">Name</td>
							  <td><input name="projectManager" type="text" class="input_small" id="projectManager" value="<?=stripslashes($f['project_manager'])?>"/></td>
						  </tr>
							<tr>
							  <td nowrap="nowrap" valign="top">Email</td>
							  <td><input name="projManagerEmail" type="text" class="input_small" id="projManagerEmail" value="<?=stripslashes($f['project_manager_email'])?>" /></td>
						  </tr>
							<tr>
							  <th colspan="2" align="left" valign="top" nowrap="nowrap">Contact Person</th>
						  </tr>
							<tr>
							  <td nowrap="nowrap" valign="top">Name</td>
							  <td><input name="contactPerson" type="text" class="input_small" id="contactPerson" value="<?=stripslashes($f['contact_person'])?>" /></td>
						  </tr>
							<tr>
							  <td nowrap="nowrap" valign="top">Email</td>
							  <td><input name="contactPersonEmail" type="text" class="input_small" id="contactPersonEmail" value="<?=stripslashes($f['contact_person_email'])?>" /></td>
						  	</tr>

						 	<tr>
							  <td nowrap="nowrap" valign="top">Defect Clause</td>
							  <td><textarea name="defectClause" class="text_area_small" id="defectClause"><?=stripslashes($f['defect_clause'])?></textarea></td>
						  	</tr>

						  	<tr>
							  <td nowrap="nowrap" valign="top">Email on Sync</td>
							  <td>Yes <input type="checkbox" name="allow_sync" id="allow_sync" value="Yes" <?php echo (isset($f['allow_sync']) && $f['allow_sync']=='Yes')?'checked="checked"':'';?> /></td>
						  	</tr>

						  	<tr>
							  <td nowrap="nowrap" valign="top">Sync project to ipad</td>
							  <td>Yes <input type="checkbox" name="allow_sync_to_ipad" id="allow_sync_to_ipad" value="Yes" <?php echo (isset($f['project_is_synced']) && $f['project_is_synced']=='Yes')?'checked="checked"':'';?> /></td>
						  	</tr>
						  
							<tr>
								
								<td colspan="2" >
									<span style="margin-left:90px;">
                                    <input type="hidden" value="<?=$id?>" name="pro_id" id="pro_id" />
									<input type="hidden" value="edit_project" name="sect" id="sect" />
									
                                    <!-- <input type="image"  value="Update" style="width:111px; height:45px; border:none;" name="update" id="update" src="images/update_btn.png"/> -->
								<input type="image"  value="Update" style="cursor: pointer;" name="update" id="update" class="green_small" />
<?php
//echo "<pre>";
//print_r($_SESSION);
if($_SESSION['web_edit_project'] == 1){?>
								<!-- <input type="image"  id="remove" value="Remove" style="width:111px; height:45px; border:none;" name="remove" src="images/remove_btn.png"/> -->
                                <input type="image"  id="remove" value="Remove" style="cursor: pointer;" name="remove" class="green_small"/>
<?php }?>
								 <!-- <a href="javascript:history.back();">
                    <img src="images/back_btn.png" style="border:none; width:111px;" /></a> -->
                    			<input type="image" value="Back" style="cursor: pointer;" class="green_small" onclick="javascript:history.back();" />

                               </span>
                                </td>
							</tr>
						</table>
					</div>
				</div>
				<div class="content_right" style="margin-left:30px;">
					<div class="signin_form1">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
							<!--<tr>
								<td width="100" valign="top" nowrap="nowrap">Standard Defects List</td>
								<td valign="top" style="border:0px solid;">
								<div class="defect_list">
								<?php 
								/*if(count($d3)>0){
									for($i=0;$i<count($d3['dl_id']);$i++){ ?>
					<input name="defectList[]" type="checkbox" value="<?=$d3['dl_id'][$i]?>" <? if($d3['checked'][$i]!=''){?>checked="<?=$d3['checked'][$i]?>"<? }?> />
										&nbsp;
										<label title="<?=stripslashes($d3['dl_title'][$i])?>">
										<?php
										if(strlen($d3['dl_title'][$i])>30){
											echo $obj->truncate_text(stripslashes($d3['dl_title'][$i]),27,' ...');
										}else{
											echo stripslashes($d3['dl_title'][$i]);
										}								
										?>
										</label>
										<br />
									<?php 
									}
								}else{
									echo "<a href='?sect=add_defects_list'><img src='images/add_new.png' style='border:none;' /></a>";
								} */?>
								</div>							
								</td>
							</tr>-->
							
							<!--<tr>
								<td valign="top" nowrap="nowrap">Associate Other Manager(s)</td>
								<td valign="top">
								<div class="builder_list">
								<?php 
								//if(count($b3)>0){
									//for($i=0;$i<count($b3['user_id']);$i++){ ?>
<input name="associateTo[]" type="checkbox" value="<? //=$b3['user_id'][$i]?>" <? //if($b3['checked'][$i]!=''){?>checked="<? //=$b3['checked'][$i]?>"<? // }?> />
										&nbsp;
										<label title="<?//=stripslashes($b3['cname'][$i])?>">
										<?php
										//if(strlen($b3['cname'][$i])>30){
											//echo $obj->truncate_text(stripslashes($b3['cname'][$i]),27,' ...');
										//}else{
											//echo stripslashes($b3['cname'][$i]);
										//}								
										?>
										</label>
										<br />
								<?php 
									//}
								//}else{
									//echo "No builder found";
								//} ?>
								</div>
								</td>
							</tr>-->
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>