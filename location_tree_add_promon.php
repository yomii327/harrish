<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_POST['uniqueId'])){
	$location = $_POST['location'];
	$locationId = $_POST['locationId'];
	
	$locationDepth = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'pr_num_sublocations');
	
	$locData = $obj->selQRYMultiple('location_title', 'project_monitoring_locations', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0 AND location_parent_id = '.$locationId);
	$locCheckArr = array();
	foreach($locData as $lData){
		$locCheckArr[] = $lData['location_title'];
	}
	if(!in_array($location, $locCheckArr)){	
		$q="INSERT INTO project_monitoring_locations SET
						project_id = ".$_SESSION['idp'].",
						location_title = '".addslashes($location)."',
						location_parent_id = '".$locationId."',
						created_date = now(),
						created_by = '".$_SESSION['ww_builder']['user_id']."',
						last_modified_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."'";
		$res = mysql_query($q);
		$insertedId = mysql_insert_id();
	
		$locNameTree = $obj->promon_sublocationParent($insertedId, ' > ');
		$isSpecial = sizeof(explode(" > ", $locNameTree));
		if($isSpecial == $locationDepth){
			$menu = "demo2";
			$nextStatus = false;
		}else{
			$menu = "demo1";
		}
		$query = 'UPDATE project_monitoring_locations SET location_tree_name = "'.$locNameTree.'", last_modified_date = NOW(), last_modified_by = '.$_SESSION['ww_builder_id'].' WHERE location_id = '.$insertedId;
		mysql_query($query);
		
		$location = $_POST['location'];
		echo '<ul ><li id="li_'.$insertedId.'"><span class="jtree-button '.$menu.'" id="'.$insertedId.'">'.addslashes($location).'</span></li></ul>';
	}else{
		echo 'Duplicate location';
	}
}
if(isset($_GET['location_id'])){
	if(strpos($_GET['location_id'], 'Id_')){
		$q="SELECT project_name, project_id FROM user_projects WHERE project_id = ".$_SESSION['idp']." AND user_id = '".$_SESSION['ww_builder']['user_id']."' AND is_deleted = 0";
		$res = mysql_query($q);
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_assoc($res);
			$projectName = $row['project_name'];
			$projectId = $row['project_id'];?>
			<fieldset class="roundCorner">
				<legend style="color:#000000;">Add Location Name</legend>
				<table width="100%" border="0">
					<tr>
						<td style="color:#000000;">Project Name</td>
						<td>
							<input type="text" name="location" id="location" value="<?php echo stripslashes($projectName);?>" readonly="readonly"  />
							<input type="hidden" name="locationId" id="locationId" value="<?php echo stripslashes($projectId);?>" readonly="readonly"  />
							<input type="hidden" name="checkProject" id="checkProject" value="Yes" readonly="readonly"  />
						</td>
					</tr>
					<tr>
						<td style="color:#000000;">Location Name</td>
						<td><input type="text" name="subLocation" id="subLocation" value=""  /></td>
					</tr>
					<tr>
						<td align="center" colspan="2" style="padding-top:20px;"><input type="button" name="submit" id="submit" value="Submit" onclick="addLocation()"  /></td>
					</tr>
				</table>
			</fieldset>
	<?php }else{ echo 'Sorry Try Again Later !';  }
	}else{
		$q="SELECT location_id, location_parent_id, location_title FROM project_monitoring_locations WHERE location_id = '".$_GET['location_id']."' AND is_deleted = 0";
		$res = mysql_query($q);
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_assoc($res);
			$locationName = $row['location_title'];
			$locationId = $row['location_id'];?>
		<fieldset class="roundCorner">
			<legend style="color:#000000;">Add Location Name</legend>
			<table width="100%" border="0">
				<tr>
					<td style="color:#000000;">Location Name</td>
					<td>
						<input type="text" name="location" id="location" value="<?php echo stripslashes($locationName);?>" readonly="readonly"  />
						<input type="hidden" name="locationId" id="locationId" value="<?php echo stripslashes($locationId);?>" readonly="readonly"  />
						<input type="hidden" name="checkProject" id="checkProject" value="No" readonly="readonly"  />
					</td>
				</tr>
				<tr>
					<td style="color:#000000;">Sub Location</td>
					<td>
						<input type="text" name="subLocation" id="subLocation" value=""  />
						<div class="error-edit-profile-red" style="width:100px;display:none;"  id="locationError">Location name require field</div>
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2" style="padding-top:20px;">
						<input type="button" class="green_small" name="submit" id="submit" value="Submit" onclick="addLocation()"  />
					</td>
				</tr>
			</table>
		</fieldset>
	<?php }else{ echo 'Sorry Try Again Later !';  } 
	}
}?>