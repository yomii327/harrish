<?php
session_start();
require_once'includes/functions.php';

$obj = new DB_Class();
$builder_id = $_SESSION['ww_builder_id'] ;
$noti_b = "SELECT count(*) as due FROM issued_to_for_inspections as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and i.inspection_fixed_by_date <= now() and i.inspection_fixed_by_date!='0000-00-00'";

$noti_record=$obj->db_query($noti_b);
if($overdue=$obj->db_fetch_assoc($noti_record)){
		$overdue_total = $overdue["due"];
	}
	



$noti_one_day="SELECT count(*) as due_one FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date >= CURDATE() - INTERVAL 1 DAY and inspection_fixed_by_date <=CURDATE() and i.inspection_fixed_by_date!='0000-00-00'";
$noti_record_one=$obj->db_query($noti_one_day);
if($overdue_one_day=$obj->db_fetch_assoc($noti_record_one)){
		$overdue_one_day_total = $overdue_one_day["due_one"];
	}
	
	
$noti_seven_day="SELECT count(*) as due_seven FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date >= CURDATE() and inspection_fixed_by_date < CURDATE() - INTERVAL 7 DAY and i.inspection_fixed_by_date!='0000-00-00'";

$noti_record_seven=$obj->db_query($noti_seven_day);
if($overdue_seven_day=$obj->db_fetch_assoc($noti_record_seven)){
		$overdue_one_seven_total = $overdue_seven_day["due_seven"];
	}	
	
	
$noti_14_day="SELECT count(*) as due_14 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date >= CURDATE()  and inspection_fixed_by_date < CURDATE() - INTERVAL 14 DAY  and i.inspection_fixed_by_date!='0000-00-00'";

$noti_record_14=$obj->db_query($noti_14_day);
if($overdue_14_day=$obj->db_fetch_assoc($noti_record_14)){
		$overdue_14_days_total = $overdue_14_day["due_14"];
	}		



$noti_21_day="SELECT count(*) as due_21 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date >= CURDATE()  and inspection_fixed_by_date < CURDATE() - INTERVAL 21 DAY  and i.inspection_fixed_by_date!='0000-00-00'";

$noti_record_21=$obj->db_query($noti_21_day);
if($overdue_21_day=$obj->db_fetch_assoc($noti_record_21)){
		$overdue_21_days_total = $overdue_21_day["due_21"];
	}	

$noti_closed="SELECT i.inspection_id, DATEDIFF( i.closed_date, p.inspection_date_raised ) as difference
FROM `issued_to_for_inspections` AS i, user_projects AS up, project_inspections p
WHERE p.inspection_id = i.inspection_id
AND p.project_id =i.project_id
AND up.project_id =i.project_id
AND i.is_deleted =0
AND up.is_deleted =0
AND p.is_deleted =0
AND user_id =$builder_id 
AND i.inspection_status = 'Closed'
AND i.closed_date != '0000-00-00'
GROUP BY i.inspection_id";

$noti_record_closed=$obj->db_query($noti_closed);
 $overdue_closed_rows_total=mysql_num_rows($noti_record_closed);
$new_value=0;
while($overdue_closed=$obj->db_fetch_assoc($noti_record_closed)){
		$overdue_closed_total = $overdue_closed["difference"];
		$new_value+=$overdue_closed_total;
	}	


?>
<html>
    <head>

   


    <style type="text/css">
table.gridtable {
	border-width: 1px;
	border-color: #FFF;
	border-collapse: collapse;
	
}
table.gridtable td {
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #FFF;
	font-size:12px;
}
#chart12{
	
	}
</style>
    </head>

<body>

		 <table border="0" width="350px" height="200px" cellspacing="0"  cellpadding="2" style="margin-top:0px;" class="gridtable">
         	 <tr>
                <th colspan="2" width="40%" align="left">Inspections Due</th>
                <th width="60%">&nbsp;</th>
             </tr>
             <tr>
                <td>Overdue</td>
                <td><?php echo  $overdue_total; ?></td>
                <td rowspan="5" align="center" valign="middle"><h2>Average Time to close Inspections(days)<br/><br/><?php if($overdue_closed_rows_total!=0)
				echo $avg= round($new_value/$overdue_closed_rows_total);
				else
				echo '0';?></h2></td>
             </tr>
             <tr>
                <td>Due in 1 day</td>
                <td><?php echo  $overdue_one_day_total; ?></td>
             </tr>
             <tr>
                <td>Due in 7 days</td>
               <td><?php echo  $overdue_one_seven_total; ?></td>
          	</tr>
             <tr>
                <td>Due in 14 days</td>
               <td><?php echo  $overdue_14_days_total; ?></td>
          	</tr>
             <tr>
                <td>Due in 21 days</td>
               <td><?php echo  $overdue_21_days_total; ?></td>
          	</tr>
          </table>
</div>
</body>

</html>