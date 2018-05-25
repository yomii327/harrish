<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_POST['uniqueId'])){
	$location = $_POST['location'];
	$locationId = $_POST['locationId'];
	$parentID = $_POST['parent_id'];

	$locData = $obj->selQRYMultiple('location_title', 'project_monitoring_locations', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0 AND location_parent_id = '.$parentID);
	$locCheckArr = array();
	foreach($locData as $lData){
		$locCheckArr[] = $lData['location_title'];
	}
	if(!in_array($location, $locCheckArr)){	
		$q="UPDATE project_monitoring_locations SET
				location_title = '".addslashes($location)."',
				last_modified_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder']['user_id']."'
			WHERE
				location_id = '".$locationId."' AND is_deleted = 0";
		$res = mysql_query($q);
		$locNameTree = $obj->promon_sublocationParent($locationId, ' > ');
		$query = 'UPDATE project_monitoring_locations SET location_tree_name = "'.$locNameTree.'", last_modified_date = NOW(), last_modified_by = '.$_SESSION['ww_builder_id'].' WHERE location_id = '.$locationId;
		mysql_query($query);
		echo $location = $_POST['location'];
	}else{
		echo 'Duplicate location';
	}
}
if(isset($_GET['location_id'])){
	if(strpos($_GET['location_id'], 'Id_')){
		echo "Project Name Can't Update Here !";
	}else{
		$q="SELECT location_id, location_parent_id, location_title FROM project_monitoring_locations WHERE location_id = '".$_GET['location_id']."' AND is_deleted = 0";
		$res = mysql_query($q);
		if(mysql_num_rows($res) > 0){
			$row = mysql_fetch_assoc($res);
			$locationName = $row['location_title'];
			$locationId = $row['location_id'];?>
		<fieldset class="roundCorner">
			<legend style="color:#000000;">Edit Location Name</legend>
			<table width="100%" border="0">
				<tr>
					<td style="color:#000000;">Location Name</td>
					<td>
					<input type="text" name="locationName" id="locationName" value="<?php echo stripslashes($locationName);?>" />
					<div class="error-edit-profile-red" style="width:100px;display:none;"  id="locationError">Location name require field</div>
					<input type="hidden" name="locationIdEdit" id="locationIdEdit" value="<?php echo stripslashes($locationId);?>" />
					<input type="hidden" name="locationParentID" id="locationParentID" value="<?php echo $row['location_parent_id'];?>" />
					</td>
				</tr>
				<tr>
					<td align="center" colspan="2" style="padding-top:20px;"><input type="button" name="submit" id="submit" class="green_small" value="Submit" onclick="editLocation()"  />
                    
                    <input type="button" class="green_small" id="s1" onclick="closePopup(300)" value="Cancel">
                    </td>
				</tr>
			</table>
		</fieldset>
	<?php }else{ echo 'Sorry Try Again Later !';  }
	}
} ?>