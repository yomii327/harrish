<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_POST['uniqueId'])){
	$location = $_POST['location'];
	$locationId = $_POST['locationId'];
	$q="UPDATE project_locations SET
			location_title = '".addslashes($location)."',
			last_modified_by = '".$_SESSION['ww_builder']['user_id']."', last_modified_date = NOW()
		WHERE
			location_id = '".$locationId."' AND is_deleted = 0";
	$res = mysql_query($q);
	
	$locNameTree = $obj->subLocations($locationId, ' > ');
	$locIdTree = $obj->subLocationsIDS($locationId, ' > ');
	$updateQRY = 'UPDATE project_locations SET location_id_tree = "'.$locIdTree.'", location_name_tree = "'.$locNameTree.'", last_modified_date = NOW(), last_modified_by = '.$_SESSION['ww_builder']['user_id'].'  WHERE location_id = '.$locationId;
	mysql_query($updateQRY);
	
	$sql = "SELECT location_id FROM `project_locations` WHERE `location_id_tree` LIKE '%".$locationId."%' AND `project_id`='".$_SESSION['idp']."' AND is_deleted = 0";
	$res = mysql_query($sql);

	if(mysql_num_rows($res) > 1){
		while($row=mysql_fetch_array($res)){
			$locIdTree = $obj->subLocationsIDS($row['location_id'], ' > ');
			$locNameTree = $obj->subLocations($row['location_id'], ' > ');
			$query = 'UPDATE project_locations SET location_id_tree = "'.$locIdTree.'", location_name_tree = "'.$locNameTree.'", last_modified_date = NOW(), last_modified_by = '.$_SESSION['ww_builder']['user_id'].'  WHERE location_id = '.$row['location_id'];
		    mysql_query($query);
		}
	}else{
		$sql = "SELECT location_id FROM `project_locations` WHERE `location_parent_id`  = ".$locationId." AND `project_id`='".$_SESSION['idp']."' AND is_deleted = 0";
		$res = mysql_query($sql);
		if(mysql_num_rows($res) > 0){
			while($row=mysql_fetch_array($res)){
				$locIdTree = $obj->subLocationsIDS($row['location_id'], ' > ');
				$locNameTree = $obj->subLocations($row['location_id'], ' > ');
				$query = 'UPDATE project_locations SET location_id_tree = "'.$locIdTree.'", location_name_tree = "'.$locNameTree.'", last_modified_date = NOW(), last_modified_by = '.$_SESSION['ww_builder']['user_id'].'  WHERE location_id = '.$row['location_id'];
				mysql_query($query);
			}
		}
	}
	
	echo $location = $_POST['location'];
//	$locationId = $_POST['locationId'];
}
if(isset($_GET['location_id'])){
	if(strpos($_GET['location_id'], 'Id_')){
		echo "Project Name Can't Update Here !";
	}else{
		$q="SELECT location_id, location_parent_id, location_title FROM project_locations WHERE location_id = '".$_GET['location_id']."' AND is_deleted = 0";
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
					<td><input type="text" name="locationName" id="locationName" value="<?php echo stripslashes($locationName);?>" />
					<input type="hidden" name="locationIdEdit" id="locationIdEdit" value="<?php echo stripslashes($locationId);?>" /></td>
				</tr>
				<tr>
					<td align="center" colspan="2" style="padding-top:20px;">
					<!-- <input type="submit" name="submit" id="submit" value="Submit" onclick="editLocation()"  /> -->
					<input type="submit" name="submit" id="submit" value="Submit" onclick="editLocation()" class="green_small" style="cursor: pointer;" />
					</td>
				</tr>
			</table>
		</fieldset>
	<?php }else{ echo 'Sorry Try Again Later !';  }
	}
} ?>