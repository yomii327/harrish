<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

#Set login user/manager id.
$ownerId = '';
if((isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] == 1)) {
	$ownerId = $_SESSION['ww_builder_id'];
} elseif((isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] == 1)) {
	$ownerId = $_SESSION['ww_is_company'];
} else {
	$ownerId = 0;
}

#Save data into database.
if(isset($_REQUEST["action"]) && $_REQUEST["action"] == 'addUpdate'){ //Insert/update records
	$output = '';
	#Grab user input data.
	$scheduleId = $_REQUEST['scheduleId'];
	$projectId = $_REQUEST['projectId'];
	$function_name = $_REQUEST['function_name'];
	$time = $_REQUEST['time'];
	$start_day = $_REQUEST['start_day'];
	$start_month = $_REQUEST['start_month'];
	$start_year = $_REQUEST['start_year'];
	$start_week_day = $_REQUEST['start_week_day'];
	$status = $_REQUEST['status'];
	$startDate = $start_year.'-'.$start_month.'-'.$start_day;
	
	$time = explode(':', $time);
	$start_hour = $time[0];
	$start_minute = $time[1];
	
	if($scheduleId == 0) { //Insert new records.
		$query = 'INSERT INTO cron_schedule(project_id, function_name, start_hour, start_minute, start_date, start_week_day, status, created_date, created_by, last_modified_date, last_modified_by, resource_type, is_deleted) VALUES ("'. $projectId .'", "'. $function_name .'", '. $start_hour .', '. $start_minute .', "'. $startDate .'", '. $start_week_day .', "'. $status .'", NOW(), '. $ownerId .', NOW(), '. $ownerId .', "webserver", 0)';
		$result = mysql_query($query);
		if($result) {
			$output = array('status'=>'true', 'msg'=>'Your records successfully saved.');
		} else {
			$output = array('status'=>'false', 'msg'=>mysql_error());
		}
	} else { //Update record
		$query = 'UPDATE cron_schedule SET function_name="'. $function_name .'", start_hour='. $start_hour .', start_minute='. $start_minute .', start_date="'. $startDate .'", start_week_day='. $start_week_day .', status="'. $status .'", last_modified_date=NOW(), last_modified_by='. $ownerId .' WHERE id='. $scheduleId .' AND project_id='.$projectId;
		$result = mysql_query($query);
		if($result) {
			$output = array('status'=>'true', 'msg'=>'Your records successfully updated');
		} else {
			$output = array('status'=>'false', 'msg'=>mysql_error());
		}
	}
	echo json_encode($output);
} elseif(isset($_REQUEST["action"]) && $_REQUEST["action"]=='delete'){ //Delete records
	$scheduleId = $_REQUEST['schId'];
	$query = 'UPDATE cron_schedule SET is_deleted=1 WHERE id='. $scheduleId;
	$result = mysql_query($query);
	if($result) {
		$output = array('status'=>'true', 'msg'=>'Your records successfully deleted.');
	} else {
		$output = array('status'=>'false', 'msg'=>mysql_error());
	}
	echo json_encode($output);
} else {//Add new scheduled
	$scheduleId = $_REQUEST['id'];
	$projectId = $_REQUEST['project_id'];
	$title = ($scheduleId==0)?'Email Schedule':'Email Re-schedule';
	$weekArr = array('Everyday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
	$monthArr = array('All', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

	#Get Cron Schedule function data.
	$funQuery = "SELECT id, function_name, function_title FROM cron_schedule_functions WHERE is_deleted=0";
	$funCronData = mysql_query($funQuery);
	$funCronRecords = mysql_num_rows($funCronData);
	
	#Get Cron Scheduled data.
	$cronData = $time = '';
	if(isset($scheduleId) && !empty($scheduleId)) {
		$query = "SELECT function_name, start_hour, start_minute, start_date, start_week_day, status, id FROM cron_schedule WHERE is_deleted=0 AND id=". $scheduleId;
		$cronResult = mysql_query($query);
		$cronData = mysql_fetch_object($cronResult);
		$time = $cronData->start_hour.':'.$cronData->start_minute;
	}
	?>
	<style type="text/css">
		table#tblReschedule { width: 100%; }
		input[type=text]{
			background: #F3F3F3;
			border:1px solid #ccc;
			color: #000;
		}
		.inputbox,
		.selectbox {
			color:#000;
			background: #F3F3F3;
			border:1px solid #ccc;
			padding: 6px 15px 5px;
			margin: 0px;
			border-radius: 5px;
			width: 211px;
		}
		.sm { width: 100px; display: inline-block; }
	</style>
	<fieldset class="roundCorner">
		<legend style="color:#000000;"><?=$title?></legend>
		<form method="post" name="frmCronSchedule" id="frmCronSchedule">
			<table id="tblReschedule" border="0" align="left" cellpadding="0" cellspacing="15">
				<tr>
					<td valign="middle" align="right" width="20%">Select Task <span class="req">*</span></td>
					<td align="left" width="80%">
						<select id="function_name" name="function_name" class="selectbox">
							<option value="">Select</option>
							<?php while($fRows=mysql_fetch_object($funCronData)){
								$selected = (isset($cronData->function_name) && $fRows->function_name==$cronData->function_name)?'selected="selected"':'';
								echo '<option value="'. $fRows->function_name .'" '. $selected .'>'. ucwords($fRows->function_title) .'</option>';
							} ?>
						</select>
						<lable for="function_name" id="errorFunctionName" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The task field is required</div></lable>
					</td>
				</tr>
				<tr>
					<td valign="middle" align="right" width="20%">Time <span class="req">*</span></td>
					<td align="left" width="80%">
						<input type="text" name="time" id="timepicker" class="inputbox time ui-timepicker-input" style="width: 180px;" value="<?=(isset($time))?$time:''?>" />
						<lable for="time" id="errorTime" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The time field is required</div></lable>
					</td>
				</tr>
				<tr>
					<td valign="middle" align="right" width="20%">Day / Month</td>
					<td align="left" width="80%">
						<?php $startDate = explode('-', $cronData->start_date); ?>
						<select id="start_day" name="start_day" class="selectbox sm">
							<?php for($i=1;$i<=31;$i++) {
								$selected = (isset($startDate[2]) && $i==$startDate[2])?'selected="selected"':'';
								echo '<option value="'. $i .'" '. $selected .'>'. $i .'</option>';
							} ?>
						</select>&nbsp;/&nbsp;
						<select id="start_month" name="start_month" class="selectbox sm">
							<?php for($i=1;$i<=12;$i++) {
								$selected = (isset($startDate[1]) && $i==$startDate[1])?'selected="selected"':'';
								echo '<option value="'. $i .'" '. $selected .'>'. $monthArr[$i] .'</option>';
							} ?>
						</select>&nbsp;/&nbsp;
						<select id="start_year" name="start_year" class="selectbox sm">
							<?php for($i=date('Y');$i>=(date('Y')-3);$i--) {
								$selected = (isset($startDate[0]) && $i==$startDate[0])?'selected="selected"':'';
								echo '<option value="'. $i .'" '. $selected .'>'. $i .'</option>';
							} ?>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="middle" align="right" width="20%">Weekday</td>
					<td align="left" width="80%">
						<select id="start_week_day" name="start_week_day" class="selectbox">
							<?php for($i=0;$i<=7;$i++) {
								$selected = (isset($cronData->start_week_day) && $i==$cronData->start_week_day)?'selected="selected"':'';
								echo '<option value="'. $i .'" '. $selected .'>'. $weekArr[$i] .'</option>';
							} ?>
						</select>
					</td>
				</tr>
				<tr>
					<td valign="middle" align="right" width="20%">Status</td>
					<td align="left" width="80%">
						<select id="status" name="status" class="selectbox sm">
							<option value="active" <?php if(isset($cronData->status) && $cronData->status=='active'){echo 'selected="selected"';} ?>>Active</option>
							<option value="inactive" <?php if(isset($cronData->status) && $cronData->status=='inactive'){echo 'selected="selected"';} ?>>Inactive</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<!-- <input type="button" id="btnSubmit" name="btnSubmit" value="Submit" class="btn btn-default btn-mini" onClick="createSchedule(0);" /> -->
						<input type="button" id="btnSubmit" name="btnSubmit" value="Submit" class="green_small" onClick="createSchedule(0);" style="cursor:pointer;" />
						<input type="hidden" name="scheduleId" value="<?=$scheduleId?>" />
						<input type="hidden" name="projectId" value="<?=$projectId?>" />
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
<?php } ?>
