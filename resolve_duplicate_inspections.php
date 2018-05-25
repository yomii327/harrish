<?php
//Header Secttion for include and objects 
require_once'includes/functions.php';
$obj = new DB_Class();


$query = "SELECT COUNT( * ) AS count1, inspection_id FROM  `project_inspections`  WHERE  `project_id` =5 GROUP BY inspection_id ORDER BY count1 DESC";

$rs=$obj->db_query($query);

while($row = mysql_fetch_assoc($rs)){
    $count1 = $row["count1"];
    $inspection_id = $row["inspection_id"];
    if ($count1 > 1)
    {
        $query = "SELECT `inspection_description` ,  `inspection_location` FROM  `project_inspections`  WHERE  `inspection_id`=$inspection_id";
        $rs1=$obj->db_query($query);
        $rowcount = mysql_num_rows ($rs1);
        if($row1 = mysql_fetch_assoc($rs1)){
            echo $row1['inspection_location'] . $row1['inspection_description'] . "<br/>";
        }
    }
}

die;
$query = "SELECT max(inspection_id)as max_id FROM  `project_inspections`  WHERE  `project_id` =5";

$rs=$obj->db_query($query);

if($row = mysql_fetch_assoc($rs)){
    $new_inspection_id = $row["max_id"];
}

$query = "SELECT COUNT( * ) AS count1, inspection_id FROM  `project_inspections`  WHERE  `project_id` =5 GROUP BY inspection_id ORDER BY count1 DESC";

$rs=$obj->db_query($query);

while($row = mysql_fetch_assoc($rs)){
    $count1 = $row["count1"];
    $inspection_id = $row["inspection_id"];
    if ($count1 > 1)
    {
        $query = "SELECT inspection_id,location_id FROM  `project_inspections`  WHERE  `inspection_id`=$inspection_id limit 1";    
        $rs1=$obj->db_query($query);
        $rowcount = mysql_num_rows ($rs1);
        if($row1 = mysql_fetch_assoc($rs1)){
            $new_inspection_id++;
            $inspection_id = $row1["inspection_id"];
            $location_id = $row1["location_id"];
            echo $inspection_id . "-" . $location_id . "<br/>";
            echo $query = "UPDATE project_inspections SET inspection_id = ".$new_inspection_id.", last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id=$inspection_id and location_id=$location_id";
            $obj->db_query($query);
        }
    }
}


?>