<?php
//echo '<pre>';print_r($GLOBALS);
#Set Hotspot image file name.
//$projId = $this->session->userdata('projId');
$fileName = 'draw_markup/uploads/draw_markup_'.substr(microtime(), -6, -1).rand(0,99).'.png';
$imageData=$GLOBALS['HTTP_RAW_POST_DATA'];
// Get the data
//$imageData=$_POST['data'];

// Remove the headers (data:,) part.
// A real application should use them according to needs such as to check image type
//echo $imageData;
$filteredData=substr($imageData, strpos($imageData, ",")+1);

// Need to decode before saving since the data we received is already base64 encoded
$unencodedData=base64_decode($filteredData);

// Save file. This example uses a hard coded filename for testing,
// but a real application can specify filename in POST variable
$fp = fopen($fileName, 'wb' );
$fwrite = fwrite( $fp, $unencodedData);

$output = array("status"=>true, "image_name"=>$fileName, "image_size"=>$fwrite.' bytes');
/*if($fwrite === false){
	$output = array("status"=>false, "image_name"=>$fileName, "image_size"=>'0 bytes');
} else {
	$this->session->set_userdata('hotspot_image', $fileName);
	$output = array("status"=>true, "image_name"=>$fileName, "image_size"=>$fwrite.' bytes');
}*/
fclose( $fp );

echo json_encode($output);
/*session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

$projectId = $_SESSION['idp'];
$operationTag = $_REQUEST['operationTag'];

#Extract hotsportId/Co-ordinate Id.
$hotspotID = explode('_', $operationTag);
$hotspotID = end($hotspotID);
$hotspotID = (!empty($hotspotID))?$hotspotID:0;


$hotspotData = $obj->getRecordByQuery("SELECT * FROM dropzone_permit_cordinates WHERE project_id = 74 AND is_deleted=0 AND cordinates_id = 1511519659");
*/
?>







