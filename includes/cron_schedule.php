<?php
include_once("commanfunction.php");
include'data-table.php'; 
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);
include('func.php');
if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){
?>
<script language="javascript" type="text/javascript">
	window.location.href = "<?=HOME_SCREEN?>";
</script>
<?php }
$builder_id = $_SESSION['ww_builder_id']; //Get login user id.
$projectId = $_SESSION['idp']; //Get selected project id.

#Get Cron Schedule data.
//$query = "SELECT csf.function_title, project_id, start_hour, start_minute, start_date, start_week_day, status, cs.id FROM cron_schedule AS cs RIGHT JOIN cron_schedule_functions AS csf ON csf.function_name = cs.function_name WHERE cs.project_id=". $projectId ." AND cs.is_deleted=0";
$query = "SELECT csf.function_title, project_id, start_hour, start_minute, start_date, start_week_day, status, cs.id FROM cron_schedule AS cs RIGHT JOIN cron_schedule_functions AS csf ON csf.function_name = cs.function_name WHERE cs.is_deleted=0";
$cronData = mysql_query($query);
$cronRecords = mysql_num_rows($cronData);

$weekArr = array('Everyday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
$monthArr = array('All', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
?>
<!-- Ajax Post -->
<!-- <link href="style.css" rel="stylesheet" type="text/css" /> -->
<link rel="stylesheet" type="text/css" href="css/jquery.timepicker.css"  />
<script type="text/javascript" src="js/jquery.timepicker.js"></script>
<script type="text/javascript" src="js/jquery.validation.js"></script>
<style type="text/css">
	.dataTables_info{width: 48%;}
	table.display{ color: #000; }
	table.display tr td { border-bottom: 1px solid #999999; }
	table.display tr:last-child td { border-bottom: none; }
	.dataTables_empty { color: #000; }
	button.sch {
		background : none;
		border: 0 none;
		cursor: pointer;
		height: 24px;
		margin: 2px 3px;
		padding: 0;
		text-indent: -9999px;
		width: 24px;
	}
	button.btn-edit { background: url("images/edit.png") no-repeat scroll 0 0 rgba(0, 0, 0, 0); }
	button.btn-delete { background: url("images/close_new.png") no-repeat scroll 0 0 rgba(0, 0, 0, 0); }
	.roundCorner { color: #000; border-radius: 5px; }
	.btn-sm { float: right; margin: 0 10px 5px 0; padding: 3px 10px; }
	span.intchk { display: inline-block; margin: 0 27px; }
	.ui-buttonset { margin-right: 0px; }
</style>
<div id="middle" style="padding-top:10px;">
	<div id="leftNav" style="width:250px;float:left;">
		<?php include 'side_menu.php';?>
	</div>
	<?php $id=base64_encode($projectId); $hb=base64_encode($_SESSION['hb']); ?>
	<div id="rightCont" style="float:left;width:700px;">
		<div class="content_hd1" style="width:500px;margin-top:12px;">
			<font style="float:left;" size="+1">
				Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $projectId, 'project_name')?>
			</font>
			<!-- <a style="float:left;margin-top:-25px;margin-left:8px; width:87px;margin-left:590px;" href="?sect=add_project_detail&id=<		?php echo $id;?>&hb=<		?php echo $hb;?>">
				<img src="images/back_btn2.png" style="border:none;" />
			</a> -->
			<a style="float:left;margin-top:-25px;margin-left:600px;cursor:pointer;" class="green_small" href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>">Back</a>
		</div>
		<div class="content_hd1">
			<h2 style="float:left;margin:0;"><i>Email Schedule:</i></h2>
			<button id="schAddNew" onClick="manageSchedule(<?=$projectId?>,0)" class="green_small" style="float: right; cursor: pointer;">Add New</button>
		</div>
		<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top:-15px;margin-top:0px\9;">
			<?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { ?>
				<div class="success_r" style="height:35px;width:185px;">
					<p><?php echo $_SESSION['add_project'] ; ?></p>
				</div>
			<?php unset($_SESSION['add_project']);} ?>
			<?php if((isset($success)) && (!empty($success))) { ?>
				<div class="success_r" style="height:45px;width:652px;">
					<p><?php echo $success; ?></p>
				</div>
			<?php }
			if((isset($err_msg)) && (!empty($err_msg))) { ?>
				<div class="failure_r" style="height:50px;width:520px;">
					<p><?php echo $err_msg; ?></p>
				</div>
			<?php } ?>
		</div>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="display" id="cronSchedule">
			<thead>
				<tr>
					<th width="30%">Task Name</th>
					<th width="10%">Time</th>
					<th width="20%">Start Date</th>
					<th width="15%">Weekday</th>
					<th width="12%">Status</th>
					<th width="13%">Action</th>
				</tr>
			</thead>
			<tbody>
			<?php if($cronRecords > 0) {
				while($rows=mysql_fetch_object($cronData)) {
					$hours = (!empty($rows->start_hour))?$rows->start_hour:'00';
					$minute = (!empty($rows->start_minute))?$rows->start_minute:'00';
					$time = $hours.':'.$minute;
					$startDate = (strtotime($rows->start_date)>0)?date('d-m-Y', strtotime($rows->start_date)):'None';
			?>
				<tr>
					<td><?=ucwords($rows->function_title)?></td>
					<td><?=$time?></td>
					<td><?=$startDate?></td>
					<td><?=$weekArr[$rows->start_week_day]?></td>
					<td><?=ucwords($rows->status)?></td>
					<td>
						<button class="sch btn-edit" onClick="manageSchedule(<?=$rows->project_id.','.$rows->id?>);">Edit</button>
						<button class="sch btn-delete" onClick="deleteSchedule(<?=$rows->project_id.','.$rows->id?>);">Delete</button>
					</td>
				</tr>
			<?php } } ?>
			</tbody>
		</table>
	</div> <!-- /.rightCont -->
</div>
<script type="text/javascript">
	$(document).ready(function() {
		oTable = $('#cronSchedule').dataTable({
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bStateSave": true
		});
	} );

	var align = 'center';
	var top = 100;
	var width = 800;
	var padding = 10;
	var disableColor = '#666666';
	var disableOpacity = 40;	
	var backgroundColor = '#FFFFFF';
	var borderColor = '#333333';
	var borderWeight = 4;
	var borderRadius = 5;
	var fadeOutTime = 300;
	var loadingImage = 'images/loadingAnimation.gif';
	
	//Create new cron schedule.
	function createSchedule(schId){
		var params = $('#frmCronSchedule').serializeArray();
		if(params[0].value===""){
			$('#errorFunctionName').show('fast');
			$('#errorTime').hide('fast');
		} else if(params[1].value===""){
			$('#errorFunctionName').hide('fast');
			$('#errorTime').show('fast');
		} else {
			showProgress();
			$.post('cron_manage_schedule.php?antiqueID='+Math.random()+'&action=addUpdate', params).done(function(data) {
				hideProgress();
				closePopup(300);
				var rs=JSON.parse(data);
				if(rs.status){
					jAlert(rs.msg, 'Alert', function(r){
						if(r) location.reload();
					});
				} else {
					jAlert(rs.msg, 'Alert', function(r){
						if(r) location.reload();
					});
				}
			});
		}
	}
	//Edit/Reschedule
	function manageSchedule(projectId,schId){
		modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'cron_manage_schedule.php?&name='+Math.random()+'&project_id='+ projectId +'&id='+schId, loadingImage,picTime);
	}	
	function picTime(){
		$('#timepicker').timepicker({
			'timeFormat': 'H:i'
		});
		$('#intervals').timepicker({
			timeFormat: 'H:i', //Time format
			interval: 15 //15 minutes
		});
	}
	//Delete schedule.
	function deleteSchedule(projectId,schId){
		jConfirm('Are you sure, you want to delete this records?', 'Alert', function(r){
			if(r){
				var params = {schId:schId};
				showProgress();
				$.post('cron_manage_schedule.php?antiqueID='+Math.random()+'&action=delete', params).done(function(data) {
					hideProgress();
					var rs=JSON.parse(data);
					if(rs.status){
						jAlert(rs.msg, 'Alert', function(r){
							if(r) location.reload();
						});
					} else {
						jAlert(rs.msg, 'Alert', function(r){
							if(r) location.reload();
						});
					}
				});
			}
		});
	}
</script>
