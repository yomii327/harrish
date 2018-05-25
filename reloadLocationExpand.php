<?php 
session_start();
set_time_limit(3000);
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_REQUEST['uniqueId'])){
	$locationId = $_REQUEST['locationId'];
	$locations_exists = $obj->subLocations($locationId, ' > ');
	echo $locations_exists;
}
?>