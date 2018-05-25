<?php
error_reporting(1);
set_time_limit(3000);
session_start();

require_once'includes/commanfunction.php';
$obj = new COMMAN_Class();

function countChildren($startId) {
    $directDescendents = mysql_query("SELECT location_id FROM project_locations WHERE location_parent_id = $startId");
    $count = mysql_num_rows($directDescendents);
    while($row = mysql_fetch_array($directDescendents))
        $count += countChildren($row['location_id']);
    return $count;
}

//echo $numChildren = countChildren(11839); // Number of Children for 'B'

//die;

function echocsv($fields){
	$op = '';
	$separator = '';
	foreach ($fields as $field){
		if(preg_match('/\\r|\\n|,|"/', $field)){
			$field = '"' . str_replace( '"', '""', $field ) . '"';
		}
		$op .= $separator . $field;
		$separator = ',';
	}
	$op .= "\r\n";
	return $op;
}

$fileName = 'Project_location_template_export.csv';
$output = '';

/*	header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=".$fileName);
header("Pragma: no-cache");
header("Expires: 0");
*/
$header = array('Location','Sublocation', 'Sublocation 1', 'Sublocation 2', 'Sublocation 3');
$output .= echocsv($header);

$queryLoc = $obj->selQRYMultiple('location_id, location_title', 'project_locations', 'location_parent_id = 0 AND is_deleted = 0 AND project_id = "'.$_SESSION['idp'].'" order by location_id');
foreach($queryLoc as $locId)
{
    $pt = $obj->getCatIdsExport($locId["location_id"]);
    $a = explode(" > ", $pt);
    for ($i=0;$i<count($a);$i++)
    {
	if (empty($a[$i]))
	    continue;
	$locations = $obj->subLocations($a[$i], ' > ');
	
	$tmparr = explode(" > ", $locations);
	$sub_c = 1;
	$sub_arr = array();
	$sub_arr["Location"] = $tmparr[0];
	for ($j=1;$j<count($tmparr);$j++)
	{
	    $sub_arr["Sub Location " . $j] = $tmparr[$j];
	}
	$output .= echocsv($sub_arr);
    }

}
	    
$fileName = 'Project_location_template_export.csv';
header("Content-type: application/csv");
header("Content-Disposition: attachment; filename=".$fileName);
header("Pragma: no-cache");
header("Expires: 0");

print $output;
die;
?>