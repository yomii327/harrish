<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
} include'data-table.php'; ?>
<?php include_once("commanfunction.php");
$obj = new COMMAN_Class(); 

$builder_id = $_SESSION['ww_builder_id'];

if(isset($_REQUEST['id'])){
	$id=$_REQUEST['id'];
	$_SESSION['project_id']=$id;
}else
	$id = ''; 
?>
<script type="text/javascript" src="js/selectivizr-min.js"></script>
<style>
fieldset.permission { border:1px solid white; padding:15px; margin-top:30px; }
fieldset.permission legend { color:#000; }
input[type=checkbox] { position: absolute; left: -99999px; }
.has-js .label_check{ width:290px; background:url(images/check_box.gif); background-repeat:no-repeat; float: left;  font-family:Tahoma, Geneva, sans-serif; font-size:13px; text-align:left; padding-left: 25px; margin-right:5px; margin-top:10px; margin-left:8px; height: 18px; position:relative; }
.has-js label.c_on{ background:url(images/check_box.gif); 	background-position:0 -32px; background-repeat:no-repeat; }
.has-js .label_check input{ border:1px solid red; }
</style>
<?php 	
$err_msg='';
//insert for Assign inspector
if(!isset($_SESSION['no_refresh'])){
	$_SESSION['no_refresh'] = "";
}
if(isset($_POST['button'])){
	$selectedUser = $_POST['user'];
	if($selectedUser == ''){
		$err_msg = 'Please select user !';
	}else{
		$usersData = $obj->selQRYMultiple("permission_name, is_allow", "user_permission", "project_id = '".$_SESSION['idp']."' and is_deleted = 0  AND user_id = '".$selectedUser."' AND permission_name IN('web_edit_inspection', 'web_delete_inspection', 'web_close_inspection', 'iPad_add_inspection', 'iPad_edit_inspection', 'iPad_delete_inspection', 'iPad_close_inspection', 'iPhone_add_inspection', 'iPhone_close_inspection', 'iPhone_edit_inspection', 'iPhone_edit_inspection_partial', 'iPhone_delete_inspection') GROUP BY permission_name ORDER BY permission_name DESC");
	}
}
if(isset($_POST['Submit'])){
	$selectedUser = $_POST['selectedUser'];
	if($selectedUser == ''){
		$err_msg = 'Please select user !';
	}else{
#			print_r($_POST);die;
		//Update Code here permissions
		$resetPermissions = "UPDATE user_permission SET is_allow = 0, last_modified_by = ".$builder_id.", last_modified_date = NOW() WHERE project_id = '".$_SESSION['idp']."' and is_deleted = 0  AND user_id = '".$selectedUser."' AND permission_name IN('web_edit_inspection', 'web_delete_inspection', 'web_close_inspection', 'iPad_add_inspection', 'iPad_edit_inspection', 'iPad_delete_inspection', 'iPad_close_inspection', 'iPhone_add_inspection', 'iPhone_close_inspection', 'iPhone_edit_inspection', 'iPhone_edit_inspection_partial', 'iPhone_delete_inspection')"; 
		mysql_query($resetPermissions);
		unset($_POST['selectedUser']);
		unset($_POST['Submit']);
		foreach($_POST as $k=>$v){
			$updateQry = "UPDATE user_permission SET is_allow = '".$v."', last_modified_by = ".$builder_id.", last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE permission_name = '".$k."' AND project_id = '".$_SESSION['idp']."' and is_deleted = 0  AND user_id = '".$selectedUser."'";
			mysql_query($updateQry);
		}
		$_SESSION['add_inspector_success'] = base64_encode('update');
	//Update Code here permissions
	
		$usersData = $obj->selQRYMultiple("permission_name, is_allow", "user_permission", "project_id = '".$_SESSION['idp']."' and is_deleted = 0  AND user_id = '".$selectedUser."' AND permission_name IN('web_edit_inspection', 'web_delete_inspection', 'web_close_inspection', 'iPad_add_inspection', 'iPad_edit_inspection', 'iPad_delete_inspection', 'iPad_close_inspection', 'iPhone_add_inspection', 'iPhone_close_inspection', 'iPhone_edit_inspection', 'iPhone_edit_inspection_partial', 'iPhone_delete_inspection') GROUP BY permission_name ORDER BY permission_name DESC");
	}
}?>
	<div id="middle" style="padding-top:10px;">
		<div id="leftNav" style="width:250px;float:left;">
			<?php include 'side_menu.php';?>
		</div>
<?php $id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']);  ?>
		<div id="rightCont" style="float:left;width:700px;">
			<div class="content_hd1" style="width:500px;margin-top:12px;">
				<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font><br />
				<!-- <a href="?sect=add_project_detail&id=<?=$id;?>&hb=<?=$hb;?>" style="float:left;margin-top:-25px; width:87px;margin-left:586px;z-index:100;">
					<img src="images/back_btn2.png" style="display:block; border:none;" />
				</a> -->
				<a href="?sect=add_project_detail&id=<?=$id;?>&hb=<?=$hb;?>" class="green_small" style="float:left;margin-top:-25px; margin-left:600px;z-index:100;">Back			
				</a>
			</div><br clear="all" />
			<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top:-15px;margin-top: 0px\9;">
<?php if((isset($_SESSION['add_inspector_success'])) && (!empty($_SESSION['add_inspector_success']))) {
		if($_SESSION['add_inspector_success'] == 'dXBkYXRl'){?>
			<div class="success_r" style="height:35px;width:300px;"><p>Permission Update Successfully !</p></div>		
<?php   }
		unset($_SESSION['add_inspector_success']);}
		if($err_msg != '') { ?>
			<div class="failure_r" style="height:35px;width:185px;"><p><?php echo $err_msg; ?></p></div>
<?php 	} ?>
			</div>
			<div class="big_container" style="width:722px;float:left;margin-top:-50px;" >
<?php $users = $obj->selQRYMultiple('user_id', 'user_projects', 'project_id = "'.$_SESSION['idp'].'" and is_deleted = 0 ORDER BY user_id');
	$projectUser = '';
	foreach($users as $user){
		if($projectUser == ''){ $projectUser .= $user['user_id']; }else{ $projectUser .= ', '.$user['user_id']; }
	}
	$newUsers = $obj->selQRYMultiple('user_id, user_fullname, user_name', 'user', 'is_deleted = 0 and user_id IN ('.$projectUser.') AND user_id NOT IN ('.$_SESSION['ww_builder']['user_id'].')');
?>
				<div style="border:1px solid #000; margin:45px 20px 10px 10px;text-align:center;">
					<form action="" id="" class="" method="post" style="margin-top:10px;" >
						<table width="100%" border="0" cellspacing="0" cellpadding="0" >
							<tr>
								<td colspan="3" align="left" style="padding-bottom:15px;">
									<strong style="font-size:16px;">&nbsp;&nbsp;Assign Permission to User</strong>
								</td>
							</tr>
							<tr>
								<td width="30%" style="">List of Users</td>
								<td width="50%" valign="top">
									<select name="user" id="user" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);margin:0 0 7px 0;">
										<option value="">-- Select --</option>
<?php if(!empty($newUsers)){
		foreach($newUsers as $user){?><option value="<?=$user['user_id']?>" <?php if($selectedUser == $user['user_id']){ echo 'selected="selected"'; }?>><?php echo $user['user_fullname'].' ('.$user['user_name'].')';?></option>
	<?php }
	}?>
									</select>
								</td>
								<td width="20%">
									<input name="button" type="submit" class="green_small" id="button" value="Submit" style="border:none; margin-right:-37px;" />
									<input type="hidden" name="no_refresh" value="<?php echo uniqid(rand());?>"  />
								</td>
							</tr>
						</table>
					</form>
				</div>
				<div class="demo_jui" id="show_defect" style="margin-left:10px;width:692px;">
<?php if(!empty($usersData)){?>
	<div id="permissionContainer">
	<form action="" name="permissionUser" id="permissionUser" method="post">
		<fieldset class="permission">
			<legend>Web Permission</legend>
				<?php foreach($usersData as $permissionData){
						if(preg_match("/^web_/", $permissionData['permission_name'])){?>
						<label class="label_check<?php if($permissionData['is_allow'] == 1){ echo ' c_on';}?>" id="<?='label_'.$permissionData['permission_name']?>" for="<?=$permissionData['permission_name']?>"><?=ucwords(str_replace('_', ' ', $permissionData['permission_name']));?>
						<input name="<?=$permissionData['permission_name']?>" type="checkbox" id="<?=$permissionData['permission_name']?>"  value="1" <?php if($permissionData['is_allow'] == 1){ echo 'checked="checked"';}?> />
						</label>
				<?php 	}
					}?>
		</fieldset>
		<fieldset class="permission">
			<legend>iPad/iPhone Permission</legend>
				<?php foreach($usersData as $permissionData){
						if(preg_match("/^iPad_/", $permissionData['permission_name'])){ ?>
					<label class="label_check<?php if($permissionData['is_allow'] == 1){ echo ' c_on';}?>" id="<?='label_'.$permissionData['permission_name']?>" for="<?=$permissionData['permission_name']?>"><?=ucwords(str_replace('_', ' ', $permissionData['permission_name']));?>
					<input name="<?=$permissionData['permission_name']?>" type="checkbox" id="<?=$permissionData['permission_name']?>"  value="1" <?php if($permissionData['is_allow'] == 1){ echo 'checked="checked"';}?> />
					</label>
				<?php 	}
					}?>
				<br/><br/>
				<?php foreach($usersData as $permissionData){
						if(preg_match("/^iPhone_/", $permissionData['permission_name'])){ ?>
					<label class="label_check<?php if($permissionData['is_allow'] == 1){ echo ' c_on';}?>" id="<?='label_'.$permissionData['permission_name']?>" for="<?=$permissionData['permission_name']?>"> <?php   //to change the partial edit permission label
						if ($permissionData['permission_name'] == "iPhone_edit_inspection_partial")
							echo "Partial Edit";
						else
							echo ucwords(str_replace('_', ' ', $permissionData['permission_name']));
						?>
						<input name="<?=$permissionData['permission_name']?>" type="checkbox" id="<?=$permissionData['permission_name']?>"  value="1" <?php if($permissionData['is_allow'] == 1){ echo 'checked="checked"';}?> />
					</label>
				<?php 	}
					}?>

		</fieldset>

		<input type="hidden" name="selectedUser" value="<?=$selectedUser;?>"  /><br />
		<input type="submit" class="green_small" name="Submit" value="Save" style="border:none; margin:15px 0 15px 600px;"  />
	</form>
	</div>
<?php }?>
					
				</div>
				<div class="spacer"></div>
			</div>
		</div>
	</div>
<script type="text/javascript">
$('#web_edit_inspection').click(function(){
	if($('#web_edit_inspection').is(':checked')){}else{
		if($('#web_close_inspection').is(':checked')){
			$('#web_close_inspection').attr('checked', false);
		}
		if($('#web_delete_inspection').is(':checked')){
			$('#web_delete_inspection').attr('checked', false);
		}
	}
});
$('#web_close_inspection').click(function(){
	if($('#web_close_inspection').is(':checked')){
		if($('#web_edit_inspection').is(':checked')){}else{
			$('#web_edit_inspection').attr('checked', true);
			$('#label_web_edit_inspection').addClass('c_on');
		} 
	}
});
$('#web_delete_inspection').click(function(){
	if($('#web_delete_inspection').is(':checked')){
		var r = jConfirm('Do you want to assign delete inspection permission ?', null, function(r){
			if (r==true){
				$('#web_delete_inspection').attr('checked', true);
				if($('#web_edit_inspection').is(':checked')){}else{
					$('#web_edit_inspection').attr('checked', true);
					$('#label_web_edit_inspection').addClass('c_on');
				} 
			}else{
				$('#web_delete_inspection').attr('checked', false);
				if($('#label_web_delete_inspection').hasClass('c_on')){
					$("#label_web_delete_inspection").removeClass("c_on");
				}
			}
		});
	}
});
$('#iPad_add_inspection').click(function(){ });
$('#iPad_edit_inspection').click(function(){
	if($('#iPad_edit_inspection').is(':checked')){
	}else{
		if($('#iPad_delete_inspection').is(':checked')){
			$('#iPad_delete_inspection').attr('checked', false);
		}
		if($('#iPad_close_inspection').is(':checked')){
			$('#iPad_close_inspection').attr('checked', false);
		}
	}
});
$('#iPad_delete_inspection').click(function(){
	if($('#iPad_delete_inspection').is(':checked')){
		var r = jConfirm('Do you want to assign delete inspection permission ?', null, function(r){
			if (r==true){
				$('#iPad_delete_inspection').attr('checked', true);
				if($('#iPad_edit_inspection').is(':checked')){}else{
					$('#iPad_edit_inspection').attr('checked', true);
					$('#label_iPad_edit_inspection').addClass('c_on');
				}
			}else{
				$('#iPad_delete_inspection').attr('checked', false);
				if($('#label_iPad_delete_inspection').hasClass('c_on')){
					$("#label_iPad_delete_inspection").removeClass("c_on");
				}
			}
		});
	}
});

$('#iPad_close_inspection').click(function(){
	if($('#iPad_close_inspection').is(':checked')){
		if($('#iPad_edit_inspection').is(':checked')){}else{
			$('#iPad_edit_inspection').attr('checked', true);
		} 
	}
});

$('#iPhone_add_inspection').click(function(){
	if($('#iPhone_add_inspection').is(':checked')){
	}else{
		if($('#iPhone_close_inspection').is(':checked')){
			$('#iPhone_close_inspection').attr('checked', false);
		}
	}
});

$('#iPhone_close_inspection').click(function(){
	if($('#iPhone_close_inspection').is(':checked')){
		if($('#iPhone_add_inspection').is(':checked')){}else{
			$('#iPhone_add_inspection').attr('checked', true);
		} 
	}
});

$('#iPhone_close_inspection').click(function(){
	if($('#iPhone_close_inspection').is(':checked')){
		if($('#iPhone_add_inspection').is(':checked')){}else{
			$('#iPhone_add_inspection').attr('checked', true);
		} 
	}
});

$('#iPhone_edit_inspection').click(function(){
	if($('#iPhone_edit_inspection').is(':checked')){
		if($('#iPhone_edit_inspection_partial').is(':checked')){
			$('#iPhone_edit_inspection_partial').attr('checked', false);
		} 
	}
});
$('#iPhone_edit_inspection_partial').click(function(){
	if($('#iPhone_edit_inspection_partial').is(':checked')){
		if($('#iPhone_edit_inspection').is(':checked')){
			$('#iPhone_edit_inspection').attr('checked', false);
		} 
	}
});




    function setupLabel() {
        if ($('.label_check input').length) {
		    $('.label_check').each(function(){ 
                $(this).removeClass('c_on');
            });
            $('.label_check input:checked').each(function(){ 
                $(this).parent('label').addClass('c_on');
            });                
        };
    };
    $(document).ready(function(){
        $('body').addClass('has-js');
        $('.label_check').click(function(){
            setupLabel();
        });
        setupLabel(); 
    });
</script>