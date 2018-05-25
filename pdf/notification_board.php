<?php
session_start();
require_once'includes/functions.php';

unset($_SESSION['notificationData']);

$obj = new DB_Class();
#$builder_id = $_SESSION['ww_builder_id'] ;

$builder_id = isset($_SESSION['ww_builder_id']) ? $_SESSION['ww_builder_id'] : '';
if($builder_id != ''){
	$where = 'user_id = $builder_id ';
}
$company = isset($_SESSION['ww_is_company']) ? $_SESSION['ww_is_company'] : '' ;

if($builder_id != ''){
	$query = "select DISTINCT project_id from user_projects where is_deleted=0 and user_id=$builder_id";
}else{
	$query = "select DISTINCT project_id from user_projects where is_deleted=0";
}
$noti_record_closed = $obj->db_query($query);
$tmp_arr = array();
while($project_id=$obj->db_fetch_assoc($noti_record_closed)){
		$tmp_arr[] = $project_id["project_id"];
}
$project_ids = join(",", $tmp_arr);
if($builder_id != ''){
	$noti_b = "SELECT count(*) as due FROM issued_to_for_inspections as i where i.project_id in (" . $project_ids.") and i.is_deleted=0 and i.inspection_status !='Closed' and i.inspection_fixed_by_date <= now() and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}else{
	$noti_b = "SELECT count(*) as due FROM issued_to_for_inspections as i where i.project_id in (" . $project_ids.") and i.is_deleted=0 and i.inspection_status !='Closed' and i.inspection_fixed_by_date <= now() and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}

$noti_record=$obj->db_query($noti_b);
$overdue_total = mysql_num_rows ($noti_record);
if($builder_id != ''){
	$noti_one_day="SELECT count(*) as due_one FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0  and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() and inspection_fixed_by_date <= CURDATE()  + INTERVAL 1 DAY and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}else{
	$noti_one_day="SELECT count(*) as due_one FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0 and  i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() and inspection_fixed_by_date <= CURDATE()  + INTERVAL 1 DAY and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}

$noti_record_one=$obj->db_query($noti_one_day);
$overdue_one_day_total = mysql_num_rows ($noti_record_one);
/*if($overdue_one_day=$obj->db_fetch_assoc($noti_record_one)){
		$overdue_one_day_total = $overdue_one_day["due_one"];
	}*/
	
if($builder_id != ''){
	$noti_seven_day="SELECT count(*) as due_seven FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0  and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 1 DAY  and inspection_fixed_by_date <= CURDATE() + INTERVAL 7 DAY and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}else{
	$noti_seven_day="SELECT count(*) as due_seven FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0 and  i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 1 DAY  and inspection_fixed_by_date <= CURDATE() + INTERVAL 7 DAY and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}

$noti_record_seven=$obj->db_query($noti_seven_day);
$overdue_one_seven_total = mysql_num_rows ($noti_record_seven);
/*if($overdue_seven_day=$obj->db_fetch_assoc($noti_record_seven)){
		$overdue_one_seven_total = $overdue_seven_day["due_seven"];
	}	*/
	
if($builder_id != ''){
	$noti_14_day="SELECT count(*) as due_14 FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0  and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 7 DAY  and inspection_fixed_by_date <= CURDATE() + INTERVAL 14 DAY    and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}else{
	$noti_14_day="SELECT count(*) as due_14 FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0 and  i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 7 DAY  and inspection_fixed_by_date <= CURDATE() + INTERVAL 14 DAY    and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}

$noti_record_14=$obj->db_query($noti_14_day);
$overdue_14_days_total = mysql_num_rows ($noti_record_14);
/*if($overdue_14_day=$obj->db_fetch_assoc($noti_record_14)){
		$overdue_14_days_total = $overdue_14_day["due_14"];
	}		*/


if($builder_id != ''){
	$noti_21_day="SELECT count(*) as due_21 FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0  and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 14 DAY   and inspection_fixed_by_date < CURDATE() + INTERVAL 21 DAY   and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}else{
	$noti_21_day="SELECT count(*) as due_21 FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0 and  i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 14 DAY   and inspection_fixed_by_date < CURDATE() + INTERVAL 21 DAY   and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}

$noti_record_21=$obj->db_query($noti_21_day);
$overdue_21_days_total = mysql_num_rows ($noti_record_21);
/*if($overdue_21_day=$obj->db_fetch_assoc($noti_record_21)){
		$overdue_21_days_total = $overdue_21_day["due_21"];
	}	*/

if($builder_id != ''){
	$noti_closed="SELECT
						i.inspection_id,
						DATEDIFF( i.closed_date, p.inspection_date_raised ) as difference
				FROM
						`issued_to_for_inspections` AS i,
						user_projects AS up, project_inspections p
				WHERE
						p.inspection_id = i.inspection_id AND
						p.project_id =i.project_id AND
						up.project_id =i.project_id AND
						i.is_deleted =0 AND
						up.is_deleted =0 AND
						p.is_deleted =0 AND
						user_id =$builder_id AND
						i.inspection_status = 'Closed' AND
						i.closed_date != '0000-00-00'
				GROUP BY
						i.inspection_id";
}else{
	$noti_closed="SELECT
						i.inspection_id,
						DATEDIFF( i.closed_date, p.inspection_date_raised ) as difference
				FROM
						`issued_to_for_inspections` AS i,
						project_inspections p
				WHERE
						p.project_id in (" . $project_ids . ") and i.project_id in (" . $project_ids . ") AND
						i.is_deleted =0 AND
						p.is_deleted =0 AND
						p.inspection_id = i.inspection_id AND
						i.inspection_status = 'Closed' AND
						i.closed_date != '0000-00-00'
				GROUP BY
						i.inspection_id";
}
$noti_record_closed=$obj->db_query($noti_closed);
 $overdue_closed_rows_total=mysql_num_rows($noti_record_closed);
$new_value=0;
while($overdue_closed=$obj->db_fetch_assoc($noti_record_closed)){
		$overdue_closed_total = $overdue_closed["difference"];
		$new_value+=$overdue_closed_total;
	}	
//CostImpact
if($builder_id != ''){
	$costImpactData = "SELECT sum(cost_impact_price) as totalPrice FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0";
}else{
	$costImpactData = "SELECT sum(cost_impact_price) as totalPrice FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0";
}
$costImpactPrice = $obj->db_query($costImpactData);
if($costImpactPriceData=$obj->db_fetch_assoc($costImpactPrice)){
	$costTotalPrice = $costImpactPriceData["totalPrice"];
}
//CostImpact
?>
<html>
    <head>
<!--[if IE]><script language="javascript" type="text/javascript" src="../dist/excanvas.js"></script><![endif]-->
<style type="text/css">
table.gridtable { border-width: 1px; border-color: #FFF; border-collapse: collapse; }
table.gridtable td { border-width: 1px; padding: 8px; border-style: solid; border-color: #FFF; font-size:12px; }
#chart12{
	
	}
</style>
    </head>

<body>
	<div style="width:100%">
		<div id="chart5" style="color: #015D9B;float: left;font-family: arial;font-size: 14px;font-weight: bold;text-align: left;">
			<div id="chart5" style="text-align:center;float:left; background: none repeat scroll 0 0 #FED439;border: 1px solid #C09601;padding:10px;padding:5px\9;margin-top: -10px;">
				 <table border="0" cellspacing="0"  cellpadding="2" style="margin-top:0px;width:390px;width:370px\9;height:200px;height:150px\9;" class="gridtable">
					 <tr>
						<th colspan="2" width="40%" align="left">Inspections Due</th>
						<th width="60%">&nbsp;</th>
					 </tr>
					 <tr>
						<td>Overdue</td>
						<td><?php echo  $overdue_total; ?></td>
						<td rowspan="3" align="center" valign="middle"><h3>Average Time to close Inspections(days)<br/>
						<?php if($overdue_closed_rows_total!=0)
						echo $avg= round($new_value/$overdue_closed_rows_total);
						else
						echo '0';?></h3></td>
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
						<td rowspan="2" align="center" valign="middle"><h3>Total cost impact of Inspections (in $)<br/>
						<?=$costTotalPrice;?></h3></td>
					</tr>
					 <tr>
						<td>Due in 21 days</td>
					   <td><?php echo  $overdue_21_days_total; ?></td>
					</tr>
				  </table>
			</div>
		</div>
	</div>
</div>
</body>

</html>