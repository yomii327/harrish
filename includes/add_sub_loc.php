<?php include_once("includes/commanfunction.php");
$object = new COMMAN_Class(); 
if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){ ?>
	<script language="javascript" type="text/javascript">
		window.location.href="<?=HOME_SCREEN?>";
	</script>
<?php }
$builder_id = $_SESSION['ww_builder_id'];
//$q="SELECT * FROM ".PROJECTS." WHERE project_id = '".$_SESSION['idp']."' AND user_id = '$builder_id' ";
$q="SELECT * FROM projects WHERE project_id = '".$_SESSION['idp']."'  ";
if($obj->db_num_rows($obj->db_query($q)) == 0){ ?>
	<script language="javascript" type="text/javascript">
		window.location.href="<?=ACCESS_DENIED_SCREEN?>";
	</script>
<?php }
$f=$obj->db_fetch_assoc($obj->db_query($q));
$userRole='';
$ownerName='';
$userName='';
$password='';
$phone='';
$email='';
$cron_report_type = 'Summary Report Without Notes'; 
$allow_rfi='';

if(isset($_GET['mode'])){
	$userId = base64_decode($_GET['uId']);
	$userData = $object->selQRY('u.user_name, u.user_fullname, u.user_password, u.user_plainpassword, u.user_phone_no, u.user_email, up.user_role, up.issued_to,up.cron_report_type, up.allow_rfi', 'user as u, user_projects as up', 'up.project_id = "'.$_SESSION['idp'].'" AND u.user_id = "'.$userId.'" and u.is_deleted = 0 and up.is_deleted = 0 and u.user_id = up.user_id');
	//echo 'tst';print_r($userData);die;
	$userRole = $userData['user_role'];
	$issuedTo = $userData['issued_to'];
	$ownerName = $userData['user_fullname'];
	$userName = $userData['user_name'];
	$password = $userData['user_plainpassword'];
	$phone = $userData['user_phone_no'];
	$email = $userData['user_email'];
	$cron_report_type = $userData['cron_report_type'];	
	$allow_rfi = $userData['allow_rfi'];		
}?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/edit_sub_loc.js"></script>
<script language="javascript" type="text/javascript">
function startAjax(){
	var ownerName=document.getElementById('ownerName').value;
	var userName=document.getElementById('userName').value;
	var password=document.getElementById('password').value;
	var phone=document.getElementById('phone').value;
	var email=document.getElementById('email').value;
	
	if(ownerName!='' && userName!='' && password!='' && phone!='' && email!=''){
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
	}else if(success == 1){
		result = '<span class="sign_emsg">Invalid phone number!<\/span><br/><br/>';
	}else if(success == 2){
		result = '<span class="sign_emsg">Invalid email id!<\/span><br/><br/>';
	}else if(success == 3){
		result = '<span class="sign_emsg">Password must be greater than 8 characters!<\/span><br/><br/>';
	}else if(success == 4){
		result = '<span class="sign_emsg">Username already exists!<\/span><br/><br/>';
	}else if(success == 5){
		result = '<span class="sign_emsg">Email Id already exists!<\/span><br/><br/>';
	}else if(success == 6){
		result = '<span class="sign_msg">Inspactor added successfully!<\/span><br/><br/>';
	}else if(success == 7){
		result = '<span class="sign_msg">Inspactor added successfully!<\/span><br/><br/>';

		// reset form		
		document.getElementById('ownerName').value='';
		document.getElementById('userName').value='';
		document.getElementById('password').value='';
		document.getElementById('phone').value='';
		document.getElementById('email').value='';
		
	}
	document.getElementById('sign_in_process').style.visibility = 'hidden';
	document.getElementById('sign_in_response').innerHTML = result;
	document.getElementById('sign_in_response').style.visibility = 'visible';
	
	return true;
}
</script>
<!-- Ajax Post -->
<div class="content_center">
	<div class="content_hd" style=" <?php if(isset($_GET['mode'])){ echo 'background-image:url(images/edit_sub.png);'; }else{  echo 'background-image:url(images/add_new_inspactor.png);'; }?>height:56px;"></div>
	<form action="ajax_reply.php" method="post"  enctype="multipart/form-data" name="e_r_frm" id="e_r_frm" onsubmit="return checkIssueTo();" >
		<div class="signin_form" style="margin-top:0px;margin-top:-20px\9;">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15">
				<!--<tr>
					<td width="134" nowrap="nowrap" valign="top">Project Id</td>
					<td width="312" colspan="2">
						<input type="text" class="input_small" readonly="readonly" value="<?=$f['project_id']?>" />
					</td>
				</tr>-->
				<tr>
					<td width="134" nowrap="nowrap" valign="top" >Project Name</td>
					<td width="312" colspan="2">
						<input type="text" class="input_small" readonly="readonly" style="background-image:big_input_big_readonly.png;" value="<?=stripslashes($f['project_name'])?>" />
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top" >User Role</td>
					<td width="312" colspan="2">
						<select name="userRole" id="userRole" class="select_box" style="margin-left:0px;">
							<option value="All Defect" <?php if($userRole == 'All Defect'){ echo 'selected="selected"';}?> >All Defect</option>
							<option value="Builder" <?php if($userRole == 'Builder'){ echo 'selected="selected"';}?> >Builder</option>
							<option value="Architect" <?php if($userRole == 'Architect'){ echo 'selected="selected"';}?> >Architect</option>
							<option value="Structural Engineer" <?php if($userRole == 'Structural Engineer'){ echo 'selected="selected"';}?> >Structural Engineer</option>
							<option value="Services Engineer" <?php if($userRole == 'Services Engineer'){ echo 'selected="selected"';}?> >Services Engineer</option>
							<option value="Superintendant" <?php if($userRole == 'Superintendant'){ echo 'selected="selected"';}?> >Superintendant</option>
							<option value="General Consultant" <?php if($userRole == 'General Consultant'){ echo 'selected="selected"';}?> >General Consultant</option>
							<option value="Client" <?php if($userRole == 'Client'){ echo 'selected="selected"';}?> >Client</option>
							<option value="Purchaser" <?php if($userRole == 'Purchaser'){ echo 'selected="selected"';}?> >Purchaser</option>
							<option value="Sub Contractor" <?php if($userRole == 'Sub Contractor'){ echo 'selected="selected"';}?> >Sub Contractor</option>
						</select>
					</td>
				</tr>
				<tr id="issueToDropDown">
					<td width="134" nowrap="nowrap" valign="top" >Issue To</td>
					<td width="312" colspan="2">
						<?php $issueToData = $object->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');#print_r($issueToData);die;?>
						<select name="issueTo" id="issueTo" class="select_box" style="margin-left:0px;">
							<?php if(!empty($issueToData)){?>
									<option value="">-- Select --</option>	
							<?php foreach($issueToData as $isData){?>
									<option value="<?=$isData['issue_to_name']?>" <?php if($issuedTo == $isData['issue_to_name']){ echo 'selected="selected"';}?>><?=$isData['issue_to_name']?></option>	
							<?php }
							}else{?>
								<option value="">-- Select --</option>
							<?php }?>
						</select>
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Full Name <span class="req">*</span></td>
					<td width="312" colspan="2">
						<input name="ownerName" type="text" class="input_small" id="ownerName" value="<?php echo isset($_SESSION['post_array']['ownerName'])?$_SESSION['post_array']['ownerName']:$ownerName;?>" />
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Username <span class="req">*</span></td>
					<td width="312" colspan="2">
						<input name="userName" type="text" class="input_small" <?php if(isset($_GET['mode'])){?> readonly="readonly"<?php }?>   id="userName" value="<?php echo isset($_SESSION['post_array']['userName'])?$_SESSION['post_array']['userName']:$userName;?>" /> 
					<?php if(isset($_SESSION['add_inspector_user'])){ echo $_SESSION['add_inspector_user']; unset($_SESSION['add_inspector_user']);} ?>
                    </td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Password <span class="req">*
						</span><div style="font-size:9px;">(Minimum 6 characters)</div></td>
					<td width="312" colspan="2">
						<input name="password" type="password" class="input_small" id="password" value="<?=$password;?>" />
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Phone No. <span class="req">*
						</span><div style="font-size:9px;">(only numbers)</div></td>
					<td width="312" colspan="2">
						<input name="phone" type="text" class="input_small" id="phone"  value="<?php echo isset($_SESSION['post_array']['phone'])?$_SESSION['post_array']['phone']:$phone;?>" />
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Email Id <span class="req">*</span></td>
					<td width="312" colspan="2">
						<input name="email" type="text" class="input_small" id="email" value="<?php echo isset($_SESSION['post_array']['email'])?$_SESSION['post_array']['email']:$email;?>" /> 
                    <?php if(isset($_SESSION['add_inspector_email'])){ echo $_SESSION['add_inspector_email']; unset($_SESSION['add_inspector_email']);} ?>
                    </td>
				</tr>
				<tr>
                  	<td nowrap="nowrap" valign="top">
                    	<input type="radio" class="radiobtn" name="cron_report_type" id="cron_report_type" value="Large Image Report" <?php echo ( $cron_report_type == "Large Image Report")?"checked":''; ?> />
                  		Large Image Report
                    </td>					
					<td nowrap="nowrap" valign="top">
                    	<input type="radio" class="radiobtn" name="cron_report_type" id="cron_report_type" value="Summary Report Without Notes" <?php echo ($cron_report_type=="Summary Report Without Notes")?"checked":''; ?> />
						Summary Report Without Notes
                    </td>                  	
				</tr>
                <tr>
                  	<td nowrap="nowrap" valign="top">
                    	<input type="checkbox" class="radiobtn" name="allow_rfi" id="allow_rfi" value="1" <?php echo (isset($allow_rfi) && $allow_rfi=="1")?"checked":''; ?> />Allow RFI's
                    </td>					
					<td nowrap="nowrap" valign="top">&nbsp;
                    </td>                  	
				</tr>
				<tr>
					<td align="right" height="70">
<?php  $id=base64_encode($_SESSION['idp']);$hb=base64_encode($_SESSION['hb']);
if(isset($_GET['mode'])){?>
	<input type="hidden" name="mode" value="<?=$_GET['mode'];?>"  />
<?php }else{?>
	<input type="hidden" name="mode" value="add"  />
<?php }?>
					<input type="hidden" name="userId" value="<?=$userId;?>"  />
	                <input type="hidden" value="<?=$f['project_id']?>" name="proId" id="proId" />
					<input type="hidden" value="add_inspector" name="sect" id="sect" />
					<input name="button" type="submit" class="green_small" id="button" value="save"/>
					<!--<input name="button" type="submit" class="submit_btn" id="button" value="save_n_new" style="background-image:url(images/save_n_new.png); font-size:0px; width:131px; border:none;" />-->
					</td>
				<?php #if(isset($_GET['mode'])){ ?>
					<!--<td align="center">
						<input name="remove" type="submit" class="submit_btn" id="button" value="remove" style="background-image:url(images/remove_btn.png); font-size:0px; border:none; width:111px;" />
					</td>-->
				<?php #}
				unset($_SESSION['post_array']); ?>
					<td align="left">
						<a href="javascript:void();" onclick="history.back();" name="passwordUpdate" class="green_small" id="button">Back</a>
						
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>
<script type="text/javascript">
function checkIssueTo(){
	var userRole = $('#userRole').val();
	var issueTo = $('#issueTo').val();
	if(userRole != ''){
		if(userRole == 'Sub Contractor' && issueTo == ''){
			jAlert('Please select Issued to');
			return false;
		}
	}
}
$(document).ready(function (){
	var userRole = $('#userRole').val();
	if(userRole != ''){
		if(userRole == 'Sub Contractor'){
			document.getElementById('issueToDropDown').style.display = 'table-row';
		}else{
			document.getElementById('issueToDropDown').style.display = 'none';
		}
	}
});
$('#userRole').change(function (){
	var userRole = $('#userRole').val();
	if(userRole != ''){
		if(userRole == 'Sub Contractor'){
			document.getElementById('issueToDropDown').style.display = 'table-row';
		}else{
			document.getElementById('issueToDropDown').style.display = 'none';
		}
	}
});
</script>