<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
if(isset($_GET['uniqueId'])){?>
	<fieldset class="roundCorner">
		<legend style="color:#000000;">Add Days</legend>
		<form action="" name="editTaskForm" id="editTaskForm">
		<!--h3 style="color:#000000;">Add days to all tasks which are not complete, N/A or signed off</h3!-->
		<table border="0" width="100%" class="simpleTable">
			<tr>
				<td>Add days to all tasks which are not complete,<br />N/A or signed off<span class="req">*</span></td>
				<td align="left">
					<select name="days" id="days">
						<?php for($r=1; $r<=100; $r++){?>
							<option value="<?php echo $r;?>"><?php echo $r;?></option>
						<?php }?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<input type="hidden" name="projectID" id="projectID" value="" />
					<input type="hidden" name="taskArray" id="taskArray" value="" />
				</td>
			</tr>
			<tr>
				<td colspan="3" align="center"><input type="button" onclick="submitBulkChangeDate();" id="submitEditTask" value="submit"  /></td>
			</tr>
		</table>
		</form>
	</fieldset>
<?php }

if(isset($_GET['antiqueID'])){
//Collect Leave Data Start Here
	$leaveData = $obj->selQRYMultiple('date, leave_type', 'project_leave', 'is_deleted = 0 AND project_id = '.$_REQUEST['projectID']);
	$leaveDateArr = array();
	foreach($leaveData as $lData){
		$leaveDateArr[$lData['date']] = $lData['leave_type'];
	}
//Collect Leave Data End Here
//Collect Leave Data Start Here
	$startEndData = $obj->selQRYMultiple('progress_id, start_date, end_date', 'progress_monitoring', 'is_deleted = 0 AND progress_id IN ('.$_REQUEST['taskArray'].')');
	$startEndArr = array();
	foreach($startEndData as $seData){
		$startEndArr[$seData['progress_id']] = array($seData['start_date'], $seData['end_date']);
	}
//Collect Leave Data End Here
	$incDayArr = array();
	foreach($startEndArr as $prsId => $seArr){
		//Start Date Here
		$dateStartRange = createDateRangeArray($seArr[0], date("Y-m-d", strtotime($seArr[0]." +".$_REQUEST['days']." day")));
		$startIncreaseDay = 0;
		foreach($dateStartRange as $sdrange){
			if(array_key_exists($sdrange.' 00:00:00', $leaveDateArr)){
				$startIncreaseDay++;
			}
			if(date('N', strtotime($sdrange)) == 7){
				$startIncreaseDay++;
			}
		}
		
		//End Date Here
		$dateEndRange = createDateRangeArray($seArr[1], date("Y-m-d", strtotime($seArr[1]." +".$_REQUEST['days']." day")));
		$endIncreaseDay = 0;
		foreach($dateEndRange as $edrange){
			if(array_key_exists($edrange.' 00:00:00', $leaveDateArr)){
				$endIncreaseDay++;
			}
			if(date('N', strtotime($edrange)) == 7){
				$endIncreaseDay++;
			}
		}
		
		$newStartDate = date("Y-m-d", strtotime($seArr[0]." +".($_REQUEST['days']+$startIncreaseDay)." day"));
		$newEndDate = date("Y-m-d", strtotime($seArr[1]." +".($_REQUEST['days']+$endIncreaseDay)." day"));

		$updateQRY = "UPDATE progress_monitoring SET
						start_date = '".$newStartDate."',
						end_date = '".$newEndDate."',
						last_modified_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						original_modified_date = NOW()
					WHERE
						progress_id IN (".$prsId.") AND
						status NOT IN ('NA', 'Complete', 'Signed off') AND
						project_id = ".$_REQUEST['projectID'];
		#echo $updateQRY;
		mysql_query($updateQRY);	
	}
	//echo '<pre>';print_r($leaveDateArr);print_r($startEndArr);die('We ahre');
	$data = array('status' => true, 'errorFlag' => false);
	echo json_encode($data);
}

function createDateRangeArray($strDateFrom, $strDateTo){
	$aryRange = array();

    $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
    $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

    if ($iDateTo >= $iDateFrom){
        array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
        while ($iDateFrom < $iDateTo){
            $iDateFrom += 86400; // add 24 hours
            array_push($aryRange, date('Y-m-d', $iDateFrom));
        }
    }
    return $aryRange;
}
?>