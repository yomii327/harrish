<?php
session_start();
require_once'includes/functions.php';
include('includes/commanfunction.php');
$object = new COMMAN_Class();

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
	$query = "select DISTINCT project_id from projects where is_deleted=0";
}
$noti_record_closed = $obj->db_query($query);
$tmp_arr = array();
while($project_id=$obj->db_fetch_assoc($noti_record_closed)){
	$tmp_arr[] = $project_id["project_id"];
}
$project_ids = join(",", $tmp_arr);

$inCaluseArr = array();
foreach($tmp_arr as $key=>$projID){
	$whereConUserRole = "";
	if(!empty($_SESSION['projUserRole'])){
		if($_SESSION['projUserRole'][$projID] != 'All Defect')
			$whereConUserRole = " AND inspection_raised_by = '".$_SESSION['projUserRole'][$projID]."'";
	}
	mysql_query('SET SESSION group_concat_max_len = 4294967295');
	$inspectionData = $object->selQRYMultiple('GROUP_CONCAT(inspection_id) AS insp, project_id', 'project_inspections', 'is_deleted = 0 AND project_id = ' . $projID . $whereConUserRole);
	
	$inCaluseArr[$projID] = $inspectionData[0]['insp'];
}
$inspCondition = "";
if(!empty($inCaluseArr))	$inspCondition = " AND i.inspection_id IN (".join(",", $inCaluseArr).")";

if($builder_id != ''){
	$noti_b = "SELECT count(*) as due FROM issued_to_for_inspections as i where i.project_id in (" . $project_ids.") and i.is_deleted=0 and i.inspection_status !='Closed' and i.inspection_fixed_by_date <= now() and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." group by i.inspection_id";
}else{
	$noti_b = "SELECT count(*) as due FROM issued_to_for_inspections as i where i.project_id in (" . $project_ids.") and i.is_deleted=0 and i.inspection_status !='Closed' and i.inspection_fixed_by_date <= now() and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}

$noti_record=$obj->db_query($noti_b);
$overdue_total = mysql_num_rows ($noti_record);
if($builder_id != ''){
	$noti_one_day="SELECT count(*) as due_one FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0  and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() and inspection_fixed_by_date <= CURDATE()  + INTERVAL 1 DAY and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." group by i.inspection_id";
}else{
	$noti_one_day="SELECT count(*) as due_one FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0 and  i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() and inspection_fixed_by_date <= CURDATE()  + INTERVAL 1 DAY and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}

$noti_record_one=$obj->db_query($noti_one_day);
$overdue_one_day_total = mysql_num_rows ($noti_record_one);
/*if($overdue_one_day=$obj->db_fetch_assoc($noti_record_one)){
		$overdue_one_day_total = $overdue_one_day["due_one"];
	}*/
	
if($builder_id != ''){
	$noti_seven_day="SELECT count(*) as due_seven FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0  and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 1 DAY  and inspection_fixed_by_date <= CURDATE() + INTERVAL 7 DAY and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." group by i.inspection_id";
}else{
	$noti_seven_day="SELECT count(*) as due_seven FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0 and  i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 1 DAY  and inspection_fixed_by_date <= CURDATE() + INTERVAL 7 DAY and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}

$noti_record_seven=$obj->db_query($noti_seven_day);
$overdue_one_seven_total = mysql_num_rows ($noti_record_seven);
/*if($overdue_seven_day=$obj->db_fetch_assoc($noti_record_seven)){
		$overdue_one_seven_total = $overdue_seven_day["due_seven"];
	}	*/
	
if($builder_id != ''){
	$noti_14_day="SELECT count(*) as due_14 FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0  and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 7 DAY  and inspection_fixed_by_date <= CURDATE() + INTERVAL 14 DAY    and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." group by i.inspection_id";
}else{
	$noti_14_day="SELECT count(*) as due_14 FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0 and  i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 7 DAY  and inspection_fixed_by_date <= CURDATE() + INTERVAL 14 DAY    and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}

$noti_record_14=$obj->db_query($noti_14_day);
$overdue_14_days_total = mysql_num_rows ($noti_record_14);
/*if($overdue_14_day=$obj->db_fetch_assoc($noti_record_14)){
		$overdue_14_days_total = $overdue_14_day["due_14"];
	}		*/


if($builder_id != ''){
	$noti_21_day="SELECT count(*) as due_21 FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0  and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 14 DAY   and inspection_fixed_by_date <= CURDATE() + INTERVAL 21 DAY   and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." group by i.inspection_id";
}else{
	$noti_21_day="SELECT count(*) as due_21 FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0 and  i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 14 DAY   and inspection_fixed_by_date <= CURDATE() + INTERVAL 21 DAY   and i.inspection_fixed_by_date!='0000-00-00' group by i.inspection_id";
}

$noti_record_21=$obj->db_query($noti_21_day);
$overdue_21_days_total = mysql_num_rows ($noti_record_21);
/*if($overdue_21_day=$obj->db_fetch_assoc($noti_record_21)){
		$overdue_21_days_total = $overdue_21_day["due_21"];
	}	*/

//*Closed Section*/
if($builder_id != ''){
	$closed_query = "SELECT count(*) as closeed FROM project_inspections pi, issued_to_for_inspections i where pi.inspection_id = i.inspection_id AND i.project_id = pi.project_id AND i.project_id in (" . $project_ids . ") AND i.is_deleted=0 AND pi.is_deleted=0 AND i.inspection_status = 'Closed' AND i.inspection_fixed_by_date != '0000-00-00' ".$inspCondition." group by pi.inspection_id";
}else{
	$closed_query = "SELECT count(*) as closeed FROM project_inspections pi, issued_to_for_inspections i where pi.inspection_id = i.inspection_id AND i.project_id = pi.project_id AND i.project_id in (" . $project_ids . ") AND i.is_deleted=0 AND pi.is_deleted=0 AND i.inspection_status = 'Closed' AND i.inspection_fixed_by_date != '0000-00-00' group by pi.inspection_id";
}
#echo $closed_query;
$closed = $obj->db_query($closed_query);
$closed_total = mysql_num_rows ($closed);
#die;
//*Closed Section*/
if($builder_id != ''){
	$noti_closed="SELECT
						DATEDIFF( i.closed_date, i.inspection_date_raised ) as difference
				FROM
						`issued_to_for_inspections` AS i
				WHERE
						i.project_id in (" . $project_ids . ") AND
						i.is_deleted =0 AND
						i.inspection_status = 'Closed' AND
						i.closed_date != '0000-00-00' ".$inspCondition." 
				GROUP BY
						i.inspection_id";
}else{
	$noti_closed="SELECT
						DATEDIFF( closed_date, inspection_date_raised ) as difference
				FROM
						issued_to_for_inspections
				WHERE
						project_id in (" . $project_ids . ") AND
						is_deleted =0 AND
						inspection_status = 'Closed' AND
						closed_date != '0000-00-00' ";
				//GROUP BY
				//		i.inspection_id";
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
	$costImpactData = "SELECT sum(cost_impact_price) as totalPrice FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0 ".$inspCondition." ";
}else{
	$costImpactData = "SELECT sum(cost_impact_price) as totalPrice FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0  ";
}
$costImpactPrice = $obj->db_query($costImpactData);
if($costImpactPriceData=$obj->db_fetch_assoc($costImpactPrice)){
	$costTotalPrice = $costImpactPriceData["totalPrice"];
}
//CostImpact
//Emails Count Here

$projectIds = $object->selQRYMultiple('project_id', 'user_projects', 'user_id = '.$_SESSION['ww_builder_id'].' AND is_deleted = 0');
$porojIds = array();
foreach($projectIds as $proj){
	$porojIds[] = $proj['project_id'];
}
//Emails Count Here
?>
<html>
<head>
<!--[if IE]><script language="javascript" type="text/javascript" src="../dist/excanvas.js"></script><![endif]-->
<style type="text/css">
table.gridtable { border-width: 1px; border-color: #FFF; border-collapse: collapse; }
table.gridtable td { border-width: 1px; padding: 6px; border-style: solid; border-color: #FFF; font-size:12px; }
#chart12{}
table.gridtable td a { color:#000000; padding:5px 0; text-decoration:none; }
td.clickable { cursor:pointer; }
td.clickable:hover { text-decoration:underline; }
</style>
</head>
<body>
<div style="width:100%">
	<div id="chart5" style="color: #015D9B;float: left;font-family: arial;font-size: 14px;font-weight: bold;text-align: left;width:412px;">
		<div id="chart5" style="text-align:center;float:left; background: none repeat scroll 0 0 #FED439;border: 1px solid #C09601;padding:10px;padding:5px\9;">
			<table border="0" cellspacing="0"  cellpadding="2" style="margin-top:0px;width:390px;width:370px\9;height:150px;height:120px\9;" class="gridtable">
				<tr>
					<th colspan="2" width="40%" align="left">Inspections Due</th>
					<th width="60%">&nbsp;</th>
				</tr>
				<tr>
					<td class="clickable" onClick="inspectionsList('overDue');"><img src="images/traffic-red.png" alt="Overdue">&nbsp;Overdue</td>
					<td class="clickable" onClick="inspectionsList('overDue');"><?php echo  $overdue_total; ?></td>
					<td rowspan="3" align="center" valign="middle">
						<h4>Average Time to close Inspections(days)<br/>
							<?php if($overdue_closed_rows_total!=0)
							echo $avg= round($new_value/$overdue_closed_rows_total);
						else
							echo '0';?>
						</h4>
					</td>
				</tr>
				<tr>
					<td class="clickable" onClick="inspectionsList('dueIn1Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 1 day'));?>');"><img src="images/traffic-yellow.png" alt="Overdue" align="absbottom">&nbsp;Due in 1 day</td>
					<td class="clickable" onClick="inspectionsList('dueIn1Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 1 day'));?>');"><?php echo  $overdue_one_day_total; ?></td>
				</tr>
				<tr>
					<td class="clickable" onClick="inspectionsList('dueIn7Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 7 day'));?>');"><img src="images/traffic-yellow.png" alt="Due in 7 days" align="absbottom">&nbsp;Due in 7 days</a></td>
					<td class="clickable" onClick="inspectionsList('dueIn7Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 7 day'));?>');"><?php echo  $overdue_one_seven_total; ?></td>
				</tr>
				<tr>
					<td class="clickable" onClick="inspectionsList('dueIn14Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 14 day'));?>');"><img src="images/traffic-yellow.png" alt="Due in 14 days" align="absbottom">&nbsp;Due in 14 days</td>
					<td class="clickable" onClick="inspectionsList('dueIn14Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 14 day'));?>');"><?php echo  $overdue_14_days_total; ?></td>
					<td rowspan="3" align="center" valign="middle">
						<h4>Total cost impact of Inspections (in $)<br/>
								<?=$costTotalPrice;?>
						</h4>
					</td>
				</tr>
				<tr>
					<td class="clickable" onClick="inspectionsList('dueIn21Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 21 day'));?>');"><img src="images/traffic-yellow.png" alt="Due in 21 days" align="absbottom">&nbsp;Due in 21 days</td>
					<td class="clickable" onClick="inspectionsList('dueIn21Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 21 day'));?>');"><?php echo $overdue_21_days_total; ?></td>
				</tr>
				<tr>
					<td class="clickable" onClick="inspectionsList('Closed');"><img src="images/traffice-blue.png" alt="Closed" align="absbottom">&nbsp;Closed</td>
					<td class="clickable" onClick="inspectionsList('Closed');"><?php echo $closed_total; ?></td>
				</tr>
			</table>
		</div>
	</div>
</div>
<script src="js/jquery.tools.min.js"></script>
<script>
function redirectToMessage(){
	parent.window.location.href = "pms.php?sect=messages&id=<?=base64_encode($porojIds[0]);?>=&hb=<?=base64_encode($_SESSION['ww_builder_id'])?>";
}
function inspectionsList(statusType, fromDate, toDate){
	console.log(statusType, fromDate, toDate);
	fromDate = typeof fromDate !== 'undefined' ? fromDate : "";
	toDate = typeof toDate !== 'undefined' ? toDate : "";
	var projectID = "<?=join(",", $porojIds);?>";
	<?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] != ""){?>
			parent.window.location.href = "pms.php?sect=i_defect&from=nb&frm=dsb&bk=Y&pid="+projectID+"&spParam="+statusType+"&FBDF="+fromDate+"&FBDT="+toDate;
	<?php }else{?>
			parent.window.location.href = "pms.php?sect=c_defect&from=nb&frm=dsb&bk=Y&pid="+projectID+"&spParam="+statusType+"&FBDF="+fromDate+"&FBDT="+toDate;
	<?php }?>
//	parent.window.location.href = "pms.php?sect=i_defect&from=nb&frm=dsb&bk=Y&pid="+projectID+"&spParam="+statusType+"&FBDF="+fromDate+"&FBDT="+toDate;
}
var spinnerVisible = false;
function showProgress() {
	if (!spinnerVisible) {
		$("div#spinner").fadeIn("fast");
		spinnerVisible = true;
	}
};
function hideProgress() {
	if (spinnerVisible) {
		var spinner = $("div#spinner");
		spinner.stop();
		spinner.fadeOut("fast");
		spinnerVisible = false;
	}
};
</script>
</body>
</html>
