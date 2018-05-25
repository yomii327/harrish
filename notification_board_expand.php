<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();
include('includes/commanfunction.php');
$object = new COMMAN_Class();

function FillSelectBox($field, $table, $where, $group){
$q="select $field from $table where $where GROUP BY $group";
	$q=mysql_query($q);
	while($q1=mysql_fetch_array($q)){ ?>
		<option <?php if(isset($_POST['pmId'])) { if($_POST['pmId'] == $q1[0]){?> selected="selected" <?php }} ?> value="<?php echo $q1[0]; ?>"><?php echo $q1[1]; ?></option>
<?php }
}
function FillSelectBoxSync($field, $table, $where, $group){
	$q = "select $field from $table where $where GROUP BY $group";
	$q = mysql_query($q);
	while($q1=mysql_fetch_array($q)){?>
		<option value="<?php echo $q1[0]; ?>"><?php echo $q1[1]; ?></option>
<?php }
}

$builder_id = isset($_SESSION['ww_builder_id']) ? $_SESSION['ww_builder_id'] : '';
if($builder_id != ''){
	$where = 'user_id = $builder_id ';
}
$company = isset($_SESSION['ww_is_company']) ? $_SESSION['ww_is_company'] : '' ;
//Inspection Ids according to user Role
if($builder_id != ''){
	$query = "select DISTINCT project_id from user_projects where is_deleted=0 and user_id=$builder_id";
}else{
	$query = "select DISTINCT project_id from projects where is_deleted=0";
}
$rs = $obj->db_query($query);
$tmp_arr = array();
while($project_id=$obj->db_fetch_assoc($rs)){
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

//Count over due inspection Start Here
	if($builder_id != ''){
		$noti_b = "SELECT count(*) as due FROM issued_to_for_inspections as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and i.inspection_fixed_by_date <= now() and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." GROUP BY inspection_id";
	}else{
		$noti_b = "SELECT count(*) as due FROM issued_to_for_inspections as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.inspection_status !='Closed' and i.inspection_fixed_by_date <= now() and i.inspection_fixed_by_date!='0000-00-00' GROUP BY inspection_id";
	}
	//echo $noti_b; die;
	$noti_record=$obj->db_query($noti_b);
	if(mysql_num_rows($noti_record) > 0){
		$overdue_total = mysql_num_rows($noti_record);
	}else{
		$overdue_total = 0;
	}
//Count over due inspection End Here
//Count due in one day inspection Start Here
	if($builder_id != ''){
		$noti_one_day="SELECT count(*) as due_one FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() and inspection_fixed_by_date <= CURDATE() + INTERVAL 1 DAY and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." GROUP BY inspection_id";
	}else{
		$noti_one_day="SELECT count(*) as due_one FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() and inspection_fixed_by_date <= CURDATE() + INTERVAL 1 DAY and i.inspection_fixed_by_date!='0000-00-00' GROUP BY inspection_id";
	}
	$noti_record_one=$obj->db_query($noti_one_day);
	if(mysql_num_rows($noti_record_one) > 0){
		$overdue_one_day_total = mysql_num_rows($noti_record_one);
	}else{
		$overdue_one_day_total = 0;
	}
//Count due in one day inspection End Here
//Count due in seven day inspection Start Here
	if($builder_id != ''){
		$noti_seven_day="SELECT count(*) as due_seven FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 1 DAY and inspection_fixed_by_date <= CURDATE() + INTERVAL 7 DAY and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." GROUP BY inspection_id";
	}else{
		$noti_seven_day="SELECT count(*) as due_seven FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 1 DAY and inspection_fixed_by_date <= CURDATE() + INTERVAL 7 DAY and i.inspection_fixed_by_date!='0000-00-00' GROUP BY inspection_id";
	}
	
	$noti_record_seven=$obj->db_query($noti_seven_day);
	if(mysql_num_rows($noti_record_seven) > 0){
		$overdue_one_seven_total = mysql_num_rows($noti_record_seven);
	}else{
		$overdue_one_seven_total = 0;
	}	
//Count due in seven day inspection End Here
//Count due in fourteen day inspection Start Here
	if($builder_id != ''){
		$noti_14_day="SELECT count(*) as due_14 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 7 DAY and inspection_fixed_by_date <= CURDATE() + INTERVAL 14 DAY and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." GROUP BY inspection_id";
	}else{
		$noti_14_day="SELECT count(*) as due_14 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 7 DAY and inspection_fixed_by_date <= CURDATE() + INTERVAL 14 DAY and i.inspection_fixed_by_date!='0000-00-00' GROUP BY inspection_id";
	}
	
	$noti_record_14=$obj->db_query($noti_14_day);
	if(mysql_num_rows($noti_record_14) > 0){
		$overdue_14_days_total = mysql_num_rows($noti_record_14);
	}else{
		$overdue_14_days_total = 0;
	}
//Count due in fourteen day inspection End Here
//Count due in twentyone day inspection Start Here
	if($builder_id != ''){
		$noti_21_day="SELECT count(*) as due_21 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and user_id=$builder_id and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 14 DAY and inspection_fixed_by_date <= CURDATE() + INTERVAL 21 DAY and i.inspection_fixed_by_date!='0000-00-00' ".$inspCondition." GROUP BY inspection_id";
	}else{
		$noti_21_day="SELECT count(*) as due_21 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.inspection_status !='Closed' and inspection_fixed_by_date > CURDATE() + INTERVAL 14 DAY and inspection_fixed_by_date <= CURDATE() + INTERVAL 21 DAY and i.inspection_fixed_by_date!='0000-00-00'  GROUP BY inspection_id";
	}
	
	$noti_record_21=$obj->db_query($noti_21_day);
	if(mysql_num_rows($noti_record_21) > 0){
		$overdue_21_days_total = mysql_num_rows($noti_record_21);
	}else{
		$overdue_21_days_total = 0;
	}	
//Count due in twentyone day inspection End Here

//*Closed Section*/
if($builder_id != ''){
	$closed_query = "SELECT count(*) as closeed FROM project_inspections pi, issued_to_for_inspections i where pi.inspection_id = i.inspection_id AND i.project_id = pi.project_id AND i.project_id in (" . $project_ids . ") AND i.is_deleted=0 AND pi.is_deleted=0 AND i.inspection_status = 'Closed' AND i.inspection_fixed_by_date != '0000-00-00' ".$inspCondition." group by pi.inspection_id";
}else{
	$closed_query = "SELECT count(*) as closeed FROM project_inspections pi, issued_to_for_inspections i where pi.inspection_id = i.inspection_id AND i.project_id = pi.project_id AND i.project_id in (" . $project_ids . ") AND i.is_deleted=0 AND pi.is_deleted=0 AND i.inspection_status = 'Closed' AND i.inspection_fixed_by_date != '0000-00-00' group by pi.inspection_id";
}
$closed = $obj->db_query($closed_query);
$closed_total = mysql_num_rows ($closed);

//*Closed Section*/

//Count Average Time Start Here
	if($builder_id != ''){
		$noti_closed="SELECT
							DATEDIFF( i.closed_date, i.inspection_date_raised ) as difference
						FROM
							issued_to_for_inspections AS i
						WHERE
							i.project_id IN (". $project_ids .") AND
							i.is_deleted =0 AND
							i.inspection_status = 'Closed' AND
							i.closed_date != '0000-00-00'  ".$inspCondition." 
						GROUP BY
							i.inspection_id";
	}else{
		$noti_closed="SELECT
							DATEDIFF( closed_date, inspection_date_raised ) as difference
						FROM
							issued_to_for_inspections
						WHERE
							project_id IN (". $project_ids .") AND 
							is_deleted =0 AND
							inspection_status = 'Closed' AND
							closed_date != '0000-00-00'
						GROUP BY
							inspection_id";
	}
	$noti_record_closed=$obj->db_query($noti_closed);
	$overdue_closed_rows_total=mysql_num_rows($noti_record_closed);
	$new_value=0;
	while($overdue_closed=$obj->db_fetch_assoc($noti_record_closed)){
		$overdue_closed_total = $overdue_closed["difference"];
		$new_value+=$overdue_closed_total;
	}	
	
	$_SESSION['notificationData']['overdue_total'] = $overdue_total;
	$_SESSION['notificationData']['overdue_one_day_total'] = $overdue_one_day_total;
	$_SESSION['notificationData']['overdue_one_seven_total'] = $overdue_one_seven_total;
	$_SESSION['notificationData']['overdue_14_days_total'] = $overdue_14_days_total;
	$_SESSION['notificationData']['overdue_21_days_total'] = $overdue_21_days_total;
	$_SESSION['notificationData']['new_value'] = $new_value;
	$_SESSION['notificationData']['overdue_closed_rows_total'] = $overdue_closed_rows_total;
//Count Average Time End Here
//Count Cost Impact Start Here
	if($builder_id != ''){
		$costImpactData = "SELECT sum(cost_impact_price) as totalPrice FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and user_id = $builder_id  ".$inspCondition." ";
	}else{
		$costImpactData = "SELECT sum(cost_impact_price) as totalPrice FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0  ";
	}
	$costImpactPrice = $obj->db_query($costImpactData);
	if($costImpactPriceData=$obj->db_fetch_assoc($costImpactPrice)){
		$costTotalPrice = $costImpactPriceData["totalPrice"];
	}
//Count Cost Impact End Here
?>
<html>
<head>
<!--[if IE]><script language="javascript" type="text/javascript" src="../dist/excanvas.js"></script><![endif]-->
<style>
table.gridtable { border-width: 1px; border-color: #FFF; border-collapse: collapse; }
table.gridtable td { border-width: 1px; padding: 5px; border-style: solid; border-color: #FFF; font-size:14px; }
table.gridtable1 td { border-width: 1px; padding: 2px; border-style: solid; border-color: #FFF; font-size:14px; }
#chart12{}
td.clickable { cursor:pointer; }
td.clickable:hover { text-decoration:underline; }
</style>
<script type="text/javascript" src="js/location_tree_jquery.min.js"></script>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="clearfix">
	<div id="chart5" style="color: #015D9B;float: left;font-family: arial;font-size: 14px;font-weight: bold;height: 300px;margin-bottom: 10px;text-align: left;<?php if($_SESSION['ww_is_company'] == 1){echo 'width:820px;';}else{echo 'width:550px;';}?>"> <span>List of Due Inspections</span>
		<div id="chart5" style="height:280px;text-align:center;float:left; background: none repeat scroll 0 0 #FED439;border: 1px solid #C09601;padding:10px;margin-top: 10px;<?php if($_SESSION['ww_is_company'] == 1){echo 'width:820px;';}else{echo 'width:520px;';}?>">
			<table border="0" width="100%" cellspacing="0"  cellpadding="2" style="margin-top:0px;">
				<tr>
					<th colspan="3" width="40%" align="center"> <form method="post" name="pmchnage" id="pmchange" action="">
							<input type="hidden" value="" name="pmId" id="pmId">
							<strong style="font-weight:bold;font-family:Arial, Helvetica, sans-serif;font-size:12px;">Select Project</strong>
							<select name="projName" id="projName"  class="select_box" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;margin-left:0px;">
								<option value="">Select</option>
								<?php if($builder_id != ''){
		FillSelectBox("project_id, project_name", "user_projects", "user_id = '".$builder_id."' and is_deleted=0", "project_name");
	}else{
		FillSelectBox("project_id, project_name", "user_projects", "is_deleted=0", "project_name");
	}?>
							</select>
						</form></th>
				</tr>
				<tr>
					<th colspan="2" width="40%" align="left">Inspections Due</th>
					<th width="60%">&nbsp;</th>
				</tr>
				<tr>
					<td colspan="3" id="notificationData"><table width="100%" cellspacing="0"  cellpadding="2" style="margin-top:0px;" class="gridtable">
							<tr>
								<td width="35%" class="clickable" onClick="inspectionsList('overDue');"><img src="images/traffic-red.png" alt="Overdue">&nbsp;Overdue</td>
								<td width="15%" class="clickable" onClick="inspectionsList('overDue');"><?php echo  $overdue_total; ?></td>
								<td width="50%" rowspan="3" align="center" valign="middle">
									<h4 style="color:#000;">Average Time to close Inspections(days)<br/>
										<?php if($overdue_closed_rows_total!=0)
										echo $avg= round($new_value/$overdue_closed_rows_total);
									else
										echo '0'; ?>
									</h4></td>
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
									</h4></td>
							</tr>
							<tr>
								<td class="clickable" onClick="inspectionsList('dueIn21Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 21 day'));?>');"><img src="images/traffic-yellow.png" alt="Due in 21 days" align="absbottom">&nbsp;Due in 21 days</td>
								<td class="clickable" onClick="inspectionsList('dueIn21Day', '<?=date('d-m-Y')?>', '<?=date('d-m-Y', strtotime(date('d-m-Y') . ' + 21 day'));?>');"><?php echo  $overdue_21_days_total; ?></td>
							</tr>
							<tr>
								<td class="clickable" onClick="inspectionsList('Closed');"><img src="images/traffice-blue.png" alt="Closed" align="absbottom">&nbsp;Closed</td>
								<td class="clickable" onClick="inspectionsList('Closed');"><?php echo $closed_total; ?></td>
							</tr>
						</table></td>
				</tr>
			</table>
		</div>
	</div>
	<?php if($_SESSION['ww_is_company'] != 1){?>
	<div style="width:340px;float:left;">
		<div id="chart5" style="color: #015D9B;float: left;font-family: arial;font-size: 14px;font-weight: bold;min-height: 300px;margin-bottom: 10px;text-align: left;width:200px;margin-left:10px;margin-left:20px\9;"> <span>List of Last Synchronization</span><br clear="all" />
			<div id="chart5" style="width:310px; min-height:280px;height: 280px\9;max-height:280px;text-align:center;float:left;margin-left:3px;background: none repeat scroll 0 0 #FED439;border: 1px solid #C09601;padding:10px;margin-top: 10px;overflow:auto">
				<div>
					<form method="post" name="pmchnage" id="pmchange" action="">
						<input type="hidden" value="" name="pmId" id="pmId">
						<strong style="font-weight:bold;font-family:Arial, Helvetica, sans-serif;font-size:12px;color:#000000;">Select Project</strong>
						<select name="projName" id="projNameSync"  class="select_box" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;width: 220px;background-image: url(images/selectSpl.png);margin-left:0px;">
							<option value="">Select</option>
							<?php if($builder_id != ''){
			FillSelectBoxSync("project_id, project_name", "user_projects", "user_id = '".$builder_id."' and is_deleted=0", "project_name");
		}else{
			FillSelectBoxSync("project_id, project_name", "user_projects", "is_deleted=0", "project_name");
		}?>
						</select>
					</form>
				</div>
				<div id="syncHistory"></div>
			</div>
		</div>
	</div>
	<?php }?>
</div>
<script type="text/javascript">
$('#projNameSync').change(function(){
	var projId = $('#projNameSync').val();
	if(projId != ''){
		$.ajax({
			url: "get_sync_history_data.php",
			type: "POST",
<?php if($builder_id != ''){?>
			data: "projectId="+projId+"&uniqueId="+Math.random(),
<?php }else{?>
			data: "projectId="+projId+"&company=Y&uniqueId="+Math.random(),
<?php }?>
			success: function (res){
				$('#syncHistory').html(res);
			}
		});
	}else{
		$('#syncHistory').html('Please Select Project !');
	}
});

$('#projName').change(function(){
	var projId = $('#projName').val();
	if(projId != ''){
		$.ajax({
			url: "getNotificationData.php",
			type: "POST",
			data: "projectId="+projId+"&uniqueId="+Math.random(),
			success: function (res){
				$('#notificationData').html(res);
			}
		});
	}else{
		$.ajax({
			url: "getNotificationData.php",
			type: "POST",
			data: "projectId=&uniqueId="+Math.random(),
			success: function (res){
				$('#notificationData').html(res);
			}
		});
	}
});
function inspectionsList(statusType, fromDate, toDate){
	console.log(statusType, fromDate, toDate);
	fromDate = typeof fromDate !== 'undefined' ? fromDate : "";
	toDate = typeof toDate !== 'undefined' ? toDate : "";
	if($('#projName').val() != ""){
		var projectID = $('#projName').val();
	}else{
		var projectID = "<?=join(",", $porojIds);?>";		
	}
	<?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] != ""){?>
			parent.window.location.href = "pms.php?sect=i_defect&from=nb&frm=dsb&bk=Y&pid="+projectID+"&spParam="+statusType+"&FBDF="+fromDate+"&FBDT="+toDate;
	<?php }else{?>
			parent.window.location.href = "pms.php?sect=c_defect&from=nb&frm=dsb&bk=Y&pid="+projectID+"&spParam="+statusType+"&FBDF="+fromDate+"&FBDT="+toDate;
	<?php }?>
//	parent.window.location.href = "pms.php?sect=i_defect&from=nb&frm=dsb&bk=Y&pid="+projectID+"&spParam="+statusType+"&FBDF="+fromDate+"&FBDT="+toDate;
}
</script>
</body>
</html>
