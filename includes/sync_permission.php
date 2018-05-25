<?php if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){?>
<script language="javascript" type="text/javascript">
	window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
include'data-table.php'; ?>
<?php include_once("commanfunction.php");
$obj = new COMMAN_Class(); 
if(isset($_REQUEST['id'])){ $id=$_REQUEST['id']; $_SESSION['project_id']=$id; }else{$id = '';}
?>
<style>
table.gridtable { border-width: 1px; border-color: #000; border-collapse: collapse; }
table.gridtable td { border-width: 1px; padding: 8px; border-style: solid; border-color: #000; font-size:14px; }
fieldset.permission { border:1px solid #94c9e4; padding:5px; margin-top:30px; }
fieldset.permission legend { color:#000; }
.roundCorner{ border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; }
ul.telefilms{list-style:none; cursor:pointer; font-size:15px;}
ul.telefilms li ul{list-style:none; line-height:30px;}
.jtree-arrow { padding-right: 5px; font-size: 15px; }
#locationsContainer ul{margin:0px;} 
fieldset.permission { border:solid 1px #000; }
</style>
<script type="text/javascript" src="js/location_tree_jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.tree_old.js"></script>
<script type="text/javascript" src="js/jquery.contextmenu.r2.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('ul.telefilms').tree({default_expanded_paths_string : '0/0/0,0/0/2,0/2/4'});
});
</script>
<?php 	
$err_msg='';
//insert for Assign inspector
if(!isset($_SESSION['no_refresh'])){
	$_SESSION['no_refresh'] = "";
}
if($_POST['no_refresh'] == $_SESSION['no_refresh']){}else{
	if(isset($_POST['projectID'])){
		$existData = $obj->selQRYMultiple('location_ids, sync_permission_id, location_level', 'sync_permission', 'project_id = "'.$_SESSION['idp'].'" AND is_deleted = 0 AND device_type = "iPad"');
		foreach($existData as $locStr){
			$existLocID = $locStr['location_ids'];
#			$existLocIDs = $locStr['location_level'];
		}
		$locID = '';$locIDs = '';
		$keyArray1 = array();$keyArray2 = array();$keyArray3 = array();$locIDArray = array();
		
		if(isset($_POST['three_location_checkbox'])){$_POST['three_location_checkbox']=$_POST['three_location_checkbox'];}else{$_POST['three_location_checkbox']=array();}
		
		if(isset($_POST['two_location_checkbox'])){$_POST['two_location_checkbox']=$_POST['two_location_checkbox'];}else{$_POST['two_location_checkbox']=array();}
		
		if(isset($_POST['one_location_checkbox'])){$_POST['one_location_checkbox']=$_POST['one_location_checkbox'];}else{$_POST['one_location_checkbox']=array();}
		
		$locIDs = join(',', array_merge(array_keys($_POST['three_location_checkbox']), array_keys($_POST['two_location_checkbox']), array_keys($_POST['one_location_checkbox'])));

		$innerLocIds = '';

		foreach($_POST['three_location_checkbox'] as $key=>$value){
			if($innerLocIds == ''){
				$innerLocIds = $obj->subLocationsId($key, ',');
			}else{
				$innerLocIds .= ','.$obj->subLocationsId($key, ',');
			}
			/*if(array_key_exists($value, $_POST['two_location_checkbox'])){
				unset($_POST['three_location_checkbox'][$key]);
			}*/
		}
		/*foreach($_POST['two_location_checkbox'] as $key=>$value){
			if(array_key_exists($value, $_POST['one_location_checkbox'])){
				unset($_POST['two_location_checkbox'][$key]);
			}
		}*/
		$keyArray1 = array_keys($_POST['three_location_checkbox']);
		$keyArray2 = array_keys($_POST['two_location_checkbox']);
		$keyArray3 = array_keys($_POST['one_location_checkbox']);
		$finalArray = array_merge($keyArray1, $keyArray2, $keyArray3);
	
		$locID = join(',', $finalArray);

		if($innerLocIds != ''){
			$locID = $locIDs.','.$innerLocIds;
		}
		$locID = implode(',', array_unique(explode(',', $locID)));
		
		if(isset($_POST['selectAllCheckBox']) && $_POST['selectAllCheckBox'] == 'selectAll'){
			$locID = 'Select All';
		}
		
		if($existLocID == $locID){
			$_SESSION['add_inspector_success'] = 'TG9jYXRpb24=';
			$_SESSION['no_refresh'] = $_POST['no_refresh'];
		}else{
			$updateQuery = "UPDATE sync_permission SET location_ids = '".trim($locID)."', location_level = '".trim($locIDs)."', last_modified_date = NOW(), last_modified_by = '".$_SESSION['ww_builder_id']."' WHERE project_id = '".$_POST['projectID']."'";
			mysql_query($updateQuery);
			if(mysql_affected_rows() > 0){
				$_SESSION['add_inspector_success'] = 'TG9jYXRpb24=';
				$_SESSION['no_refresh'] = $_POST['no_refresh'];
			}
		}
	}

	if($_POST['deviceName'] == 'iPad'){
		$statusList = '';
		for($i=0; $i<sizeof($_POST['status']); $i++){
			if($statusList == ''){
				$statusList = "'".$_POST['status'][$i]."'";
			}else{
				$statusList .= ", '".$_POST['status'][$i]."'";
			}
		}
		$insQuery = "UPDATE sync_permission SET
							no_of_days = '".$_POST['days']."',
							status = \"".$statusList."\",
							last_modified_by = '".$_SESSION['ww_builder_id']."',
							last_modified_date = NOW()
					WHERE
						sync_permission_id = '".$_POST['sync_permission_id']."' AND
						device_type = '".$_POST['deviceName']."' AND 
						project_id = '".$_SESSION['idp']."' AND 
						is_deleted = 0";
		mysql_query($insQuery);
		$_SESSION['add_inspector_success'] = 'aVBhZFVwZGF0ZQ==';
		$_SESSION['no_refresh'] = $_POST['no_refresh'];
	}
	if($_POST['deviceName'] == 'iPhone'){
		$statusList = '';
		for($i=0; $i<sizeof($_POST['status']); $i++){
			if($statusList == ''){
				$statusList = "'".$_POST['status'][$i]."'";
			}else{
				$statusList .= ", '".$_POST['status'][$i]."'";
			}
		}
		$insQuery = "UPDATE sync_permission SET
							no_of_days = '".$_POST['days']."',
							status = \"".$statusList."\",
							last_modified_by = '".$_SESSION['ww_builder_id']."',
							last_modified_date = NOW()
					WHERE
						sync_permission_id = '".$_POST['sync_permission_id']."' AND
						device_type = '".$_POST['deviceName']."' AND 
						project_id = '".$_SESSION['idp']."' AND 
						is_deleted = 0";
		mysql_query($insQuery);
		$_SESSION['add_inspector_success'] = 'aVBob25lVXBkYXRl';
		$_SESSION['no_refresh'] = $_POST['no_refresh'];
	}
}
$syncPermissionData = $obj->selQRYMultiple('sync_permission_id, no_of_days, status, device_type', 'sync_permission', 'project_id = "'.$_SESSION['idp'].'" AND is_deleted = 0');

$locIdArray = array();
$locIdSelArray = array();

$locString = $obj->selQRYMultiple('location_ids, sync_permission_id, location_level', 'sync_permission', 'project_id = "'.$_SESSION['idp'].'" AND is_deleted = 0 AND device_type = "iPad"');

$locLevel = $locString[0]['location_ids']; 
$check = false;
foreach($locString as $locStr){
	if($locStr['location_ids'] == 'Select All'){
		$check = true;
	}
	$locIdArray = explode(',', $locStr['location_level']);
	for($i=0; $i<sizeof($locIdArray); $i++){
		$locIdSelArray[] = $locIdArray[$i];
	}
	$locIdArray = array();
}?>
	<div id="middle" style="padding-top:10px;">
		<div id="leftNav" style="width:250px;float:left;">
			<?php include 'side_menu.php'; ?>
		</div>
<?php $id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']);  ?>
		<div id="rightCont" style="float:left;width:700px;">
			<div class="content_hd1" style="width:500px;margin-top:12px;">
				<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font><br />
				<a href="?sect=add_project_detail&id=<?=$id;?>&hb=<?=$hb;?>" style="float:left;margin-top:-25px;margin-left:586px;z-index:100;" class="green_small">
					Back
				</a>
			</div><br clear="all" />
			<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top:-15px;margin-top: 0px\9;">
<?php if((isset($_SESSION['add_inspector_success'])) && (!empty($_SESSION['add_inspector_success']))) {
		if($_SESSION['add_inspector_success'] == 'aVBhZFVwZGF0ZQ=='){?>
			<div class="success_r" style="height:35px;width:400px;margin-bottom: 25px;"><p>iPad Synchronization Permission Update Successfully !</p></div>		
<?php   }else if($_SESSION['add_inspector_success'] == 'aVBob25lVXBkYXRl'){?>
			<div class="success_r" style="height:35px;width:400px;margin-bottom: 25px;"><p>iPhone Synchronization Permission Update Successfully !</p></div>		
<?php  }else if($_SESSION['add_inspector_success'] == 'TG9jYXRpb24='){?>
			<div class="success_r" style="height:35px;width:400px;margin-bottom: 25px;"><p>Location Synchronization Permission Update Successfully !</p></div>		
<?php  }unset($_SESSION['add_inspector_success']);}
		if($err_msg != '') { ?>
			<div class="failure_r" style="height:35px;width:185px;"><p><?php echo $err_msg; ?></p></div>
<?php 	} ?>
			</div>
			<div class="big_container" style="width:722px;float:left;margin-top:-40px;" >
<?php if(!empty($syncPermissionData)){$g=0;
		foreach($syncPermissionData as $syncPermission){ $g++;?>
			<fieldset class="permission">
				<legend><h3><?=$syncPermission['device_type'];?> Synchronization Permissions</h3></legend>
				<form action="" name="<?=$syncPermission['device_type'];?>_permission" id="<?=$syncPermission['device_type'];?>_permission" method="post">
				<table border="0" width="100%" cellspacing="0"  cellpadding="2">
					<tr>
						<td align="center" style="">No of Days</td>
						<td align="center" style="">Status of Inspections</td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<select name="days" id="days" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);background:#0987c8 url(images/selectSpl.png) no-repeat\9;">
								<option value="100000" selected="selected">Select All</option>
								<option value="10"<?php if($syncPermission['no_of_days'] == '10'){ echo 'selected="selected"'; }?>>10 Days</option>
							<?php for($i=1;$i<=6;$i++){?>
								<option value="<?=($i*30)?>" <?php if($syncPermission['no_of_days'] == ($i*30)){ echo 'selected="selected"'; }?> ><?=($i*30).' Days'?></option>	
							<?php }?>
							</select>
						</td>
						<td align="center" valign="top">
						<?php $statusArray = explode(', ', $syncPermission['status']); ?>
							<select name="status[]" id="status_<?=$g;?>" multiple="multiple" size="5" class="select_box" style="width:220px;height:76px;background-image:url(images/multiple_select_box.png);">
								<option value="ALL" <? if(in_array("'ALL'", $statusArray)){ echo 'selected="selected"'; } ?> >Select All</option>
								<option value="Open" <? if(in_array("'Open'", $statusArray)){ echo 'selected="selected"'; } ?> >Open</option>
								<option value="All Open" <? if(in_array("'All Open'", $statusArray)){ echo 'selected="selected"'; } ?> >All Open</option>
								<option value="Pending" <? if(in_array("'Pending'", $statusArray)){ echo 'selected="selected"'; } ?> >Pending</option>
								<option value="All Pending" <? if(in_array("'All Pending'", $statusArray)){ echo 'selected="selected"'; } ?> >All Pending</option>
								<option value="Fixed" <? if(in_array("'Fixed'", $statusArray)){ echo 'selected="selected"'; } ?> >Fixed</option>
								<option value="Closed" <? if(in_array("'Closed'", $statusArray)){ echo 'selected="selected"'; } ?> >Closed</option>
							</select><br />
<div id="selet_value_<?=$g;?>" style=" text-align:left;"><?=str_replace("'", '', $syncPermission['status']);?></div>
						</td>
					</tr>
				</table>
				<input type="hidden" name="sync_permission_id" value="<?=$syncPermission['sync_permission_id']?>"  />
				<input type="hidden" name="deviceName" value="<?=$syncPermission['device_type'];?>"  />
				<input type="button" class="green_small" value="Save" name="Submit" style="margin:5px 0 0 588px;height:29px;" onclick="<?=$syncPermission['device_type'];?>FormSubmit();">
				<input type="hidden" name="no_refresh" value="<?php echo uniqid(rand());?>"  />
				</form>
			</fieldset>
<?php  } 
	} ?>
			<fieldset class="permission">
				<legend><h3>Location Permissions</h3></legend>
				<form action="" name="locationTree" id="locationTree" method="post">
				<div id="locationContenter" class="big_container" style="width:670px;float:left;margin-left:30px;">
				<input type="checkbox" name="selectAllCheckBox" id="selectAllCheckBox" value="selectAll" onclick="toggleCheckAll(this, 'locationsContainer');"  <?php if($check){ echo 'checked="checked"'; }?>  />&nbsp;<span style="font-size:15px;">Select All Locations</span>
					<input type="button" class="green_small" value="Save" name="Submit" style="margin:-24px 0 0 558px;height:29px;" onclick="selectedLocationSubmit(this, 'locationContenter');" />
					<?php $q = "select location_id, location_title from project_locations where project_id = '".$_SESSION['idp']."' and location_parent_id = '0' and is_deleted = '0' order by location_title";
					$re = mysql_query($q);
					$isLocation = mysql_num_rows($re);
					while($rw = mysql_fetch_array($re)){	$val[] = $rw;	}#print_r($val);die;?>
					<div id="locationsContainer">
						<span id="projectId_<?php echo $_SESSION['idp']?>">
							<?php $i=0; if(!empty($val)){foreach($val as $locations){$i++;?>
							<ul class="telefilms"><!-- Location Level One -->
								<li id="li_<?php echo $locations['location_id']?>">
								<input type="checkbox" name="one_location_checkbox[<?=$locations['location_id']?>]" id="checkbox<?php echo $locations['location_id']?>"  value="0"
								
								<?php if($check){ echo 'checked="checked"'; }else{if(in_array($locations['location_id'], $locIdSelArray)){ echo 'checked="checked"'; }}?>
								
								onclick="toggleCheck(this, 'li_<?php echo $locations['location_id']?>');" /><span class="jtree-button demo1" id="<?php echo $locations['location_id']?>"><?php echo stripslashes($locations['location_title'])?></span>
									<?php $q1 = "select location_id, location_title from project_locations where location_parent_id = '".$locations['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
									$re1 = mysql_query($q1);
									while($rw1 = mysql_fetch_array($re1)){	$val1[] = $rw1;	}
									if(!empty($val1)){foreach($val1 as $locations1){ ?>
									<ul><!-- Location Level Two -->
										<li id="li_<?php echo $locations1['location_id']?>">
										<input type="checkbox" name="two_location_checkbox[<?=$locations1['location_id']?>]" id="checkbox<?php echo $locations1['location_id']?>"  value="<?=$locations['location_id']?>"
										
										<?php if($check){ echo 'checked="checked"'; }else{if(in_array($locations1['location_id'], $locIdSelArray)){ echo 'checked="checked"'; }}?>
										
										onclick="toggleCheck(this, 'li_<?php echo $locations1['location_id']?>', 'checkbox<?php echo $locations['location_id']?>');" /><span class="jtree-button demo1" id="<?php echo $locations1['location_id']?>"><?php echo stripslashes($locations1['location_title'])?></span>
											<?php $q2 = "select location_id, location_title from project_locations where location_parent_id = '".$locations1['location_id']."' and is_deleted = '0' and project_id = '".$_SESSION['idp']."' order by location_title";
											$re2 = mysql_query($q2);
											while($rw2 = mysql_fetch_array($re2)){	$val2[] = $rw2;	}
											if(!empty($val2)){foreach($val2 as $locations2){ ?>
											<ul><!-- Location Level Three -->
												<li id="li_<?php echo $locations2['location_id']?>">
												<input type="checkbox" name="three_location_checkbox[<?=$locations2['location_id']?>]" id="checkbox<?php echo $locations2['location_id']?>"  value="<?=$locations1['location_id']?>"
							
												<?php if($check){ echo 'checked="checked"'; }else{if(in_array($locations2['location_id'], $locIdSelArray)){ echo 'checked="checked"'; }}?> 
												
												onclick="toggleCheck(this, 'li_<?php echo $locations2['location_id']?>', 'checkbox<?php echo $locations1['location_id']?>', 'checkbox<?php echo $locations['location_id']?>');" /><span class="jtree-button demo1" id="<?php echo $locations2['location_id']?>"><?php echo stripslashes($locations2['location_title'])?></span> </li>
											</ul>
											<?php }$val2 =array();} ?>
										</li>
									</ul>
									<?php }$val1 =array();}?>
								</li>
							</ul>
							<?php }$val=array();}?>
						</span>
						</div>
					<input type="button" class="green_small" value="Save" name="Submit" style="margin:5px 0 0 558px;height:29px;" onclick="selectedLocationSubmit(this, 'locationContenter');"  />
				</div>
				<input type="hidden" name="projectID" value="<?=$_SESSION['idp'];?>"  />
				<input type="hidden" name="no_refresh" value="<?php echo uniqid(rand());?>"  />
				</form>
			</fieldset>
				<div class="demo_jui" id="show_defect" style="margin-left:10px;width:692px;">
				</div>
				<div class="spacer"></div>
			</div>
		</div>
	</div>
<script>
function iPadFormSubmit(){ document.forms['iPad_permission'].submit(); }
function iPhoneFormSubmit(){ document.forms['iPhone_permission'].submit(); }
$('#status_1').change(function(){
	var values = new String ($(this).val());
	var contentString = new Array();
	contentString = values.split(',');
	
	document.getElementById('selet_value_1').innerHTML = values;
	if(contentString.length > 1){
		if($.inArray("ALL", contentString) != -1){
			jAlert("you can't select 'Select All' option if any other is selected !");
			$(this).val("ALL");
			document.getElementById('selet_value_1').innerHTML = '';
		}
	}
	if($.inArray("Open", contentString) != -1){
		if($.inArray("All Open", contentString) != -1){
			jAlert("you can't select 'Open' and 'All Open' Both option !");
			$(this).val("ALL");
			document.getElementById('selet_value_1').innerHTML = '';
		}
	}
	if($.inArray("All Open", contentString) != -1){
		if($.inArray("Open", contentString) != -1){
			jAlert("you can't select 'Open' and 'All Open' Both option !");
			$(this).val("ALL");
			document.getElementById('selet_value_1').innerHTML = '';
		}
	}
	if($.inArray("Pending", contentString) != -1){
		if($.inArray("All Pending", contentString) != -1){
			jAlert("you can't select 'Pending' and 'All Pending' Both option !");
			$(this).val("ALL");
			document.getElementById('selet_value_1').innerHTML = '';
		}
	}
	if($.inArray("All Pending", contentString) != -1){
		if($.inArray("Pending", contentString) != -1){
			jAlert("you can't select 'Pending' and 'All Pending' Both option !");
			$(this).val("ALL");
			document.getElementById('selet_value_1').innerHTML = '';
		}
	}
});
$('#status_2').change(function(){
	var values = new String ($(this).val());
	var contentString = new Array();
	contentString = values.split(',');
	document.getElementById('selet_value_2').innerHTML = values;
	if(contentString.length > 1){
		if($.inArray("ALL", contentString) != -1){
			jAlert("you can't select 'Select All' option if any other is selected !");
			$(this).val("ALL");
			document.getElementById('selet_value_2').innerHTML = '';
		}
	}
	if($.inArray("Open", contentString) != -1){
		if($.inArray("All Open", contentString) != -1){
			jAlert("you can't select 'Open' and 'All Open' Both option !");
			$(this).val("ALL");
			document.getElementById('selet_value_2').innerHTML = '';
		}
	}
	if($.inArray("All Open", contentString) != -1){
		if($.inArray("Open", contentString) != -1){
			jAlert("you can't select 'Open' and 'All Open' Both option !");
			$(this).val("ALL");
			document.getElementById('selet_value_2').innerHTML = '';
		}
	}
	if($.inArray("Pending", contentString) != -1){
		if($.inArray("All Pending", contentString) != -1){
			jAlert("you can't select 'Pending' and 'All Pending' Both option !");
			$(this).val("ALL");
			document.getElementById('selet_value_2').innerHTML = '';
		}
	}
	if($.inArray("All Pending", contentString) != -1){
		if($.inArray("Pending", contentString) != -1){
			jAlert("you can't select 'Pending' and 'All Pending' Both option !");
			$(this).val("ALL");
			document.getElementById('selet_value_2').innerHTML = '';
		}
	}
});
function toggleCheck(obj, tableID, PID, SPID){
//Deselect select box first 
	$('#selectAllCheckBox').prop('checked', false);
	SPID = typeof SPID !== 'undefined' ? SPID : '';
	var checkedStatus = obj.checked;
//Check child checkboxes
	$('#'+tableID+' ul').find('li:first :checkbox').each(function () {
		if(!$(this).is(':disabled')){
			$(this).prop('checked', checkedStatus);
		}
	});
}
function selectedLocationSubmit(obj, divID){
	var i=0;
	$('#'+divID+' ul').find('li:first :checkbox').each(function () { if($(this).is(':checked')){ i++; } });
	if(i>0){ document.forms['locationTree'].submit(); }else{ jAlert('Please select at least one location'); }
	console.log(i);
}
function toggleCheckAll(obj, divID){
	var checkedStatus = obj.checked;
	$('#'+divID+' span ul').find('li:first :checkbox').each(function () {
		if(!$(this).is(':disabled')){
			$(this).prop('checked', checkedStatus);
		}
	});
}
/*
function check_parent(par_parentid){
	par_parentid = typeof par_parentid !== 'undefined' ? par_parentid : '';
	if (par_parentid == ''){
		return;
	}
	var parentid = $('#'+par_parentid).parent().get(0).id;
	var result = true;
	$('#'+parentid+' ul').find('li:first :checkbox').each(function () {
		if(!$(this).is(':checked')){
			result = false;
		}
	});
	if (result){
		$('#'+par_parentid).prop('checked', true);
	}
}
function check_super_parent(par_parentid){
	par_parentid = typeof par_parentid !== 'undefined' ? par_parentid : '';
	if (par_parentid == ''){
		return;
	}
	var parentid = $('#'+par_parentid).parent().get(0).id;
	var result = true;
	$('#'+parentid+' ul').find('li:first :checkbox').each(function () {
		if(!$(this).is(':checked')){
			result = false;
		}
	});
	if (result){
		$('#'+par_parentid).prop('checked', true);
	}
}*/
</script>