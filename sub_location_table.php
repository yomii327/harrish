<?php include'data-table.php'; ?>
<?php include_once("includes/commanfunction.php");

session_start();
$obj = new COMMAN_Class(); 
if(isset($_REQUEST['id'])){
	$id=$_REQUEST['id'];
	$_SESSION['project_id']=$id;
}else
	$id = '';
	
# Login user id.
$userId = 0;
if(isset($_SESSION['ww_builder_id']) && !empty($_SESSION['ww_builder_id'])){
	$userId = $_SESSION['ww_builder_id'];
} elseif(isset($_SESSION['ww_is_company']) && !empty($_SESSION['ww_is_company'])){
	$userId = $_SESSION['ww_is_company'];
} else {
	$userId = 0;
}
?>
<script type="text/javascript">
function deletechecked(redirectURL){
	var r = jConfirm('Do you want to remove user from this project ?', null, function(r){ if(r==true){ window.location = redirectURL; } });
}
</script>
<?php 	
$err_msg='';
//insert for Assign inspector
if(!isset($_SESSION['no_refresh'])){
	$_SESSION['no_refresh'] = "";
}
if(isset($_GET['deleteId'])){
	$deleteId = base64_decode($_GET['deleteId']);
	$deleteQry = "UPDATE user_projects SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$userId." WHERE project_id = '".$_SESSION['idp']."' AND user_id = '".$deleteId."'";
	mysql_query($deleteQry);
	$permissionDeleteQry = "update user_permission set is_deleted=1, last_modified_date = NOW(), last_modified_by = ".$userId." WHERE project_id = '".$_SESSION['idp']."' AND user_id = '".$deleteId."'";
	mysql_query($permissionDeleteQry);
	$_SESSION['add_inspector_success'] = 'aW5zcGVjdG9yZSBkZWxldGVk';
}


if(isset($_POST['button1'])){
	if($_POST['no_refresh'] == $_SESSION['no_refresh']){}else{
		$assignInspector = $_POST['assignInspector'];
		$assignInspectorRole = $_POST['assignInspectorRole'];
		if($assignInspector == ''){
			$err_msg = 'Please select user !';
		}else{
			$useType = $obj->getDataByKey('user', 'user_id', $assignInspector, 'user_type');
			#print_r($_SESSION);die;
			$projectData = $obj->selQRY('project_id, pro_code, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, resource_type, is_pdf', 'projects', 'project_id = "'.$_SESSION['idp'].'" AND is_deleted=0', 'No');
			#echo '<pre>ProData:';print_r($projectData);die;
			
			$userProjectData = $obj->selQRY('project_id, user_id', 'user_projects', 'project_id = "'.$_SESSION['idp'].'" AND user_id = "'.$assignInspector.'"');
			
			if($assignInspectorRole == 'Sub Contractor'){
				$issuedTo = $_POST['issueTo'];	
			}else{
				$issuedTo = '';
			}
			
			if(empty($userProjectData)){
				$pro_code = (isset($projectData['pro_code']) && !empty($projectData['pro_code'])) ? $projectData['pro_code'] : 0;
				$is_pdf = (isset($projectData['is_pdf']) && !empty($projectData['is_pdf'])) ? $projectData['is_pdf'] : 0;
				$insertQry = "INSERT INTO user_projects set
								project_id = '".$_SESSION['idp']."',
								user_id = '".$assignInspector."',
								pro_code = '".$pro_code."',
								project_name = '".$projectData['project_name']."',
								project_type = '".$projectData['project_type']."',
								project_address_line1 = '".$projectData['project_address_line1']."',
								project_address_line2 = '".$projectData['project_address_line2']."',
								project_suburb = '".$projectData['project_suburb']."',
								project_state = '".$projectData['project_state']."',
								project_postcode = '".$projectData['project_postcode']."',
								project_country = '".$projectData['project_country']."',
								created_date = now(),
								last_modified_by = '".$userId."',
								last_modified_date = now(),
								created_by = '".$userId."',
								user_role = '".$assignInspectorRole."',
								resource_type = '".$projectData['resource_type']."',
								issued_to = '".$issuedTo."',
								is_pdf = '".$is_pdf."'";
				#echo $insertQry;die;
				mysql_query($insertQry);
				if($useType == 'inspector'){
					$projectWisePermissions = array('web_edit_inspection','web_delete_inspection','web_close_inspection','iPad_add_inspection','iPad_edit_inspection','iPad_delete_inspection','iPad_close_inspection','iPhone_add_inspection','iPhone_close_inspection','iPhone_edit_inspection','iPhone_edit_inspection_partial','web_checklist');

					$keyInspectorPermisArray = array_keys($inspectorPermissionArray);

					for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
						if(in_array($keyInspectorPermisArray[$i], $projectWisePermissions)){
							if($keyInspectorPermisArray[$i] == 'iPhone_edit_inspection'){
								if($assignInspectorRole == 'Sub Contractor'){
									$permissionQry = "INSERT INTO user_permission (user_id, project_id, permission_name, is_allow, created_by, created_date, last_modified_by, last_modified_date) VALUES ('".$assignInspector."', '".$_SESSION['idp']."', 'iPhone_edit_inspection', 0, '".$_SESSION['ww_builder']['user_id']."', NOW(), '".$_SESSION['ww_builder']['user_id']."', NOW()), ('".$assignInspector."', '".$_SESSION['idp']."', 'iPhone_edit_inspection_partial', 1, '".$_SESSION['ww_builder']['user_id']."', NOW(), '".$_SESSION['ww_builder']['user_id']."', NOW())";
								}else{
									$permissionQry = "INSERT INTO user_permission (user_id, project_id, permission_name, is_allow, created_by, created_date, last_modified_by, last_modified_date) VALUES ('".$assignInspector."', '".$_SESSION['idp']."', 'iPhone_edit_inspection', '".$inspectorPermissionArray['iPhone_edit_inspection']."', '".$_SESSION['ww_builder']['user_id']."', NOW(), '".$_SESSION['ww_builder']['user_id']."', NOW()), ('".$assignInspector."', '".$_SESSION['idp']."', 'iPhone_edit_inspection_partial', '".$inspectorPermissionArray['iPhone_edit_inspection_partial']."', '".$_SESSION['ww_builder']['user_id']."', NOW(), '".$_SESSION['ww_builder']['user_id']."', NOW())";
								}
								mysql_query($permissionQry);
								$i++;
							}else{
								$permissionQry = "INSERT INTO user_permission SET
													user_id = '".$assignInspector."',
													project_id = '".$_SESSION['idp']."',
													permission_name = '".$keyInspectorPermisArray[$i]."',
													is_allow = '".$inspectorPermissionArray[$keyInspectorPermisArray[$i]]."',
													created_by = '".$_SESSION['ww_builder']['user_id']."',
													created_date = now(),
													last_modified_date = NOW(),
													last_modified_by = '".$userId."'";
								mysql_query($permissionQry);
							}
						}
					}
				}else{
					$projectWisePermissions = array('web_edit_inspection','web_delete_inspection','web_close_inspection','iPad_add_inspection','iPad_edit_inspection','iPad_delete_inspection','iPad_close_inspection','iPhone_add_inspection', 'iPhone_edit_inspection','iPhone_edit_inspection_partial','iPhone_close_inspection');
					$keyManagerPermissionArray = array_keys($managerPermissionArray);
					for($i=0;$i<sizeof($managerPermissionArray);$i++){
						if(in_array($keyManagerPermissionArray[$i], $projectWisePermissions)){
							$permissionQry = "INSERT INTO user_permission SET
												user_id = '".$assignInspector."',
												project_id = '".$_SESSION['idp']."',
												permission_name = '".$keyManagerPermissionArray[$i]."',
												is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."',
												created_by = '".$_SESSION['ww_builder']['user_id']."',
												created_date = now(),
												last_modified_date = NOW(),
												last_modified_by = '".$userId."'";
							mysql_query($permissionQry);
						}
					}
				}
				$_SESSION['add_inspector_success'] = 'c3VjY2Vzcw==';
				$_SESSION['no_refresh'] = $_POST['no_refresh'];
			} else {
				$updateQry = "UPDATE user_projects SET is_deleted = 0, issued_to = '".$issuedTo."', user_role = '".$assignInspectorRole."', last_modified_date = NOW(), last_modified_by = ".$userId." WHERE project_id = '".$_SESSION['idp']."' AND user_id = '".$assignInspector."'";
				#echo $updateQry;die;
				mysql_query($updateQry);
				$permissionUpdateQry = "UPDATE user_permission SET is_deleted = 0, last_modified_date = NOW(), last_modified_by = ".$userId." WHERE project_id = '".$_SESSION['idp']."' AND user_id = '".$assignInspector."'";
				mysql_query($permissionUpdateQry);
			}
		}
	}
}
//Delete Inspector Data
?>
	<div id="middle" style="padding-top:10px;">
		<div id="leftNav" style="width:250px;float:left;">
			<?php include 'side_menu.php';?>
		</div>
<?php $id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']);  ?>
		<div id="rightCont" style="float:left;width:700px;">
			<div class="content_hd1" style="width:500px;margin-top:12px;">
				<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font><!-- <br /> -->
				<!-- <a href="?sect=add_project_detail&id=<?php //echo $id;?>&hb=<?php //echo $hb;?>" class="green_small" style="float:left;margin-top:-25px; margin-left:590px;">Back</a> -->
				<a style="float:left;margin-top:-25px;margin-left:590px;" href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>" class="green_small">Back</a>
			</div>
			<br clear="all" />
			<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top:-15px;margin-top:0px\9;">
<?php if((isset($_SESSION['add_inspector_success'])) && (!empty($_SESSION['add_inspector_success']))) {
		if($_SESSION['add_inspector_success'] == 'dXBkYXRl'){?>
			<div class="success_r" style="height:35px;width:300px;"><p>User Update Successfully !</p></div>		
<?php   }
		if($_SESSION['add_inspector_success'] == 'c3VjY2Vzcw=='){?>
			<div class="success_r" style="height:35px;width:300px;"><p>User Added Successfully !</p></div>
<?php   }
		if($_SESSION['add_inspector_success'] == 'ZGVsZXRl'){?>
			<div class="success_r" style="height:35px;width:300px;"><p>User Deleted Successfully !</p></div>
<?php   }if($_SESSION['add_inspector_success'] == 'aW5zcGVjdG9yZSBkZWxldGVk'){?>
			<div class="success_r" style="height:35px;width:350px;"><p>User Remove Successfully from this Project !</p></div>
<?php   }
		unset($_SESSION['add_inspector_success']);}
		if($err_msg != '') { ?>
			<div class="failure_r" style="height:35px;width:185px;"><p><?php echo $err_msg; ?></p></div>
		<?php } ?>
			</div>
			<div class="big_container" style="width:722px;float:left;margin-top:-50px;" >
<?php $users = $obj->selQRYMultiple('user_id', 'user_projects', 'project_id = "'.$_SESSION['idp'].'" and is_deleted = 0 ORDER BY user_id');
	$projectUser = '';
	if(!empty($users)){
		foreach($users as $user){
			if($projectUser == ''){ $projectUser .= $user['user_id']; }else{ $projectUser .= ', '.$user['user_id']; }
		}
	}
	$newUsers = $obj->selQRYMultiple('user_id, user_fullname, user_name', 'user', 'is_deleted = 0 and user_id NOT IN ('.$projectUser.') ORDER BY user_fullname asc');
?>
				<div style="border:1px solid #000; margin:45px 20px 10px 10px;text-align:center;">
					<form action="?sect=show_sub_loc" id="" class="" method="post" style="margin-top:10px;" onsubmit="return checkIssueTo();" >
						<table width="100%" border="0" cellspacing="0" cellpadding="0" >
							<tr>
								<td colspan="3" align="left" style="padding-bottom:15px;">
									<strong style="font-size:16px;">&nbsp;&nbsp;Assign User to Project</strong>
								</td>
							</tr>
							<tr>
								<td width="30%" style="">List of Users</td>
								<td width="50%" valign="top">
									<select name="assignInspector" id="assignInspector" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);margin:0 0 7px 0;">
										<option value="">-- Select --</option>
<?php if(!empty($newUsers)){
		foreach($newUsers as $user){?><option value="<?=$user['user_id']?>"><?php echo $user['user_fullname'].' ('.$user['user_name'].')';?></option>
	<?php }
	}?>
									</select>
								</td>
								<td width="20%">&nbsp;</td>
							</tr>
							<tr>
								<td width="30%" style="">User Role</td>
								<td width="50%" valign="top">
									<select name="assignInspectorRole" id="assignInspectorRole" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);margin:0 0 7px 0;">
										<option value="All Defect">All Defect</option>
										<option value="Builder">Builder</option>
										<option value="Architect">Architect</option>
										<option value="Structural Engineer">Structural Engineer</option>
										<option value="Services Engineer">Services Engineer</option>
										<option value="Superintendant">Superintendant</option>
										<option value="General Consultant">General Consultant</option>
										<option value="Client">Client</option>
										<option value="Purchaser">Purchaser</option>
										<option value="Sub Contractor">Sub Contractor</option>
									</select>
								</td>
								<td width="20%">&nbsp;</td>
							</tr>
							<tr id="issueToDropDown" style="display:none;">
								<td width="30%" style="">Issue To</td>
								<td width="50%" valign="top">
									<?php $issueToData = $obj->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');#print_r($issueToData);die;?>
									<select name="issueTo" id="issueTo" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);margin:0 0 7px 0;">
										<?php if(!empty($issueToData)){?>
												<option value="">-- Select --</option>	
										<?php foreach($issueToData as $isData){?>
												<option value="<?=$isData['issue_to_name']?>"><?=$isData['issue_to_name']?></option>	
										<?php }
										}else{?>
											<option value="">-- Select --</option>
										<?php }?>
									</select>
								</td>
							</tr>
							</tr>
								<td width="30%">&nbsp;</td>
								<td width="50%">&nbsp;</td>
								<td width="20%">
									<input name="button1" class="green_small" type="submit" id="button1" value="Submit" style="border:none; margin-right:-15px;">
									<input type="hidden" name="no_refresh" value="<?php echo uniqid(rand());?>"  />
								</td>
							</tr>
						</table>
					</form>
				</div>
				<!-- <a href="?sect=add_sub_loc"><div class="add_new" style="margin:0 40px 0 0;float:right;"></div></a> -->
				<a style="margin:0 32px 0 0;float:right;" href="?sect=add_sub_loc" class="green_small">Add New</a>

				<div class="demo_jui" id="show_defect" style="margin-left:10px;width:692px;">
					<table width="690" cellpadding="0" cellspacing="0" border="0" class="display" id="userRoleTable">
<?php $inspectors = $obj->selQRYMultiple('distinct u.user_id, u.user_name, u.user_fullname, up.user_role', 'user_projects as up, user as u', 'u.user_id = up.user_id and u.is_deleted = 0 and up.is_deleted = 0 and u.user_id IN ('.$projectUser.') and project_id = "'.$_SESSION['idp'].'"');
	if(!empty($inspectors)){?>
						<thead>
							<th>User Login Name</th>
							<th>User Full Name</th>
							<th>User Role</th>
							<th style="width:10px;">Edit</th>
							<th style="width:10px;">Delete</th>
						</thead>
						<tbody>
		<?php foreach($inspectors as $inspector){?>
		        			<tr  class="gradeA" style="border:1px solid#999; padding: 0.4em;;">
								<td><?=stripslashes($inspector['user_name'])?></td>
								<td><?=stripslashes($inspector['user_fullname'])?></td>
								<td><?=stripslashes($inspector['user_role'])?></td>
								<td>
									<a href='pms.php?sect=add_sub_loc&mode=edit&uId=<?=base64_encode($inspector['user_id'])?>'><img src='images/edit.png' border='none' /></a>
								</td>
								<td>
									<form action="" method="post">
										<input type="hidden" name="userId" value="<?=stripslashes($inspector['user_id'])?>" />
										<input name="remove" type="button" class="submit_btn" id="button" onclick="deletechecked('?sect=show_sub_loc&deleteId=<?php echo base64_encode($inspector['user_id']);?>')" style="background-image:url(images/remove.png); border:none; width:25px; height:24px;">
									</form>
								</td>
							</tr>
		<?php }?>
						</tbody>
<?php }?>
					</table>
				</div>
				<div class="spacer"></div>
			</div>
		</div>
	</div>
<script type="text/javascript">
$('#assignInspectorRole').change(function (){
	var assignInspectorRole = $('#assignInspectorRole').val();
	if(assignInspectorRole != ''){
		if(assignInspectorRole == 'Sub Contractor'){
			document.getElementById('issueToDropDown').style.display = 'table-row';
		}else{
			document.getElementById('issueToDropDown').style.display = 'none';
		}
	}
});
function checkIssueTo(){
	var assignInspectorRole = $('#assignInspectorRole').val();
	var issueTo = $('#issueTo').val();
	if(assignInspectorRole != ''){
		if(assignInspectorRole == 'Sub Contractor' && issueTo == ''){
			jAlert('Please select Issued to');
			return false;
		}
	}
}
$(document).ready(function() {
	oTable = $('#userRoleTable').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": true
	});
} );
</script>
