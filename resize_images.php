<?php
include('includes/commanfunction.php');

$obj = new COMMAN_Class();

//$obj->resizeImages('inspections/photo/92_1349448553_10251349448553.png', 799, 799, 'inspections/photo/check_size/92_1349448553_10251349448553.png');

//die;
$images = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'is_deleted = 0');

for ($i=0; $i<count($images); $i++)
{
    echo $images[$i]['graphic_name'];
    $obj->resizeImages('inspections/photo/'.$images[$i]['graphic_name'], 799, 799, 'inspections/photo/check_size/'.$images[$i]['graphic_name']);
}

?>