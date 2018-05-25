<?php
session_start();
require_once'includes/functions.php';
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$owner_id = $_SESSION['ww_builder_id'];
#echo '<pre>';print_r($_REQUEST);die;

$phd='';
$myProjects='';
$ihd='';
$myInspections='';
$where='';$or='';
if(isset($_REQUEST['name'])){
#echo '<pre>';print_r($_REQUEST);
	$projID = '';	$locationQA = '';	$subLocationQA1 = '';	$subLocationQA2 = '';	$subLocationQA3 = '';	$content ='';
	$totalCount = 0;$orderBy = "";
	
	$locArray = array();
	if(!empty($_REQUEST['projNameQA'])){
		$projID = $_REQUEST['projNameQA'];
		$projectName = $object->getDataByKey('user_projects', 'project_id', $_REQUEST['projNameQA'], 'project_name');
	}
	
	if(!empty($_REQUEST['locationQA'])){
		$locArray[] = $_REQUEST['locationQA'];
		$locationQA = $_REQUEST['locationQA'];
	}

	if(!empty($_REQUEST['subLocationQA1'])){
		$locArray[] = $_REQUEST['subLocationQA1'];
		$subLocationQA1 = $_REQUEST['subLocationQA1'];
	}
	
	if(!empty($_REQUEST['subLocationQA2'])){
		$locArray[] = $_REQUEST['subLocationQA2'];
		$subLocationQA2 = $_REQUEST['subLocationQA2'];
	}
	
	if(!empty($_REQUEST['subLocationQA3'])){
		$locArray[] = $_REQUEST['subLocationQA3'];
		$subLocationQA3 = $_REQUEST['subLocationQA3'];
	}

	if(!empty($_REQUEST['sortBy'])){
		if($_REQUEST['sortBy'] == 'status'){
			$orderBy = $_REQUEST['sortBy'].' DESC';
		}else{
			$orderBy = $_REQUEST['sortBy'];
		}
	}else{
			$orderBy = 'sub_location_id';
	}

	$locLupCount = sizeof($locArray);
	$locString = '';	$locStringSec = '';

	if($locLupCount < 4 && $locLupCount == 3){//Till Sublocation 2 selected
		$locStringSec = $object->subLocationsDepthQA($subLocationQA2, ', ');
		$searchSubLoc = $locStringSec;
	}else if($locLupCount < 3 && $locLupCount == 2){//Till Sublocation 1 selected
		$locStringSec = $object->subLocationsDepthQA($subLocationQA1, ', ');
		$searchSubLoc = $locStringSec;
	}else  if($locLupCount < 2 && $locLupCount == 1){//Till Root Location selected
		$locStringSec = $object->subLocationsDepthQA($locationQA, ', ');	
		$searchSubLoc = $locStringSec;
	}else if($locLupCount < 1 && $locLupCount == 0){//Only Project Selected
	
	}else{
		$searchSubLoc = $subLocationQA3;
	}
//Code for find Location title array 	
	for($i=0; $i<$locLupCount; $i++){
		if($locString == ''){
			$locString = $locArray[$i];
		}else{
			$locString .= ', '.$locArray[$i];
		}
	}
	if($locStringSec != ''){
		$locString = $locString. ', ' . $locStringSec;
	}
	$locData = $object->selQRYMultiple('location_id, location_title', 'qa_task_locations', 'project_id = '.$projID.' AND location_id IN ('.$locString.') AND is_deleted = 0 GROUP BY location_id ORDER BY location_id');
	$locArrayData = array();
	foreach($locData as $ldata){
		$locArrayData[$ldata['location_id']] = $ldata['location_title'];
	}
//Code for find Location title array 	
	$toggle = "display:none";
	$taskData = $object->selQRYMultiple('loc.location_title AS location, qt.task_id, qt.project_id, qt.location_id, qt.sub_location_id, qt.task, qt.status, qt.comments, qt.signoff_image, qt.created_date, qt.last_modified_date', 'qa_task_locations AS loc, qa_task_monitoring AS qt', 'qt.project_id = '.$projID.' AND qt.is_deleted = 0 AND qt.location_id = '.$locationQA.' AND qt.sub_location_id IN ('.$searchSubLoc.') AND loc.location_id = qt.sub_location_id ORDER BY '.$orderBy);
	$noInspection = count($taskData);
	if($noInspection > 0){ #print_r($taskData ); ?>
		<!--<h3 style="margin-left:25px;"><?#=$locArrayData[$locationQA].' > '.$locArrayData[$subLocationQA1].' > '.$locArrayData[$subLocationQA2]?></h3>-->
<label for="holeTask" align="absmiddle" style="vertical-align:top; line-height: 49px;">
	<input type="checkbox" name="holeTask" id="holeTask" style="margin: 20px 0 15px 30px; vertical-align:top" onclick="holeProjectChecked(this, '<?=$searchSubLoc?>');">
	&nbsp;check box and click "close" to close all tasks on project
</label>
<img id="closeTask" src="images/bluk_close.png" style="margin:0 0 0 470px;"  onClick="closetaskids();"  />
<form name="allTaskTable" id="allTaskTable">
	<table width="90%" border="0" cellspacing="0" cellpadding="0">
		<?php if(isset( $_SESSION["message"])) { ?>
			<tr>
			<td align="center">
				<font color="red">
					<h3><?php echo $_SESSION["message"];?></h3>
				</font>
			</td>
		</tr>
		<?php unset($_SESSION["message"]); } ?>
		<tr>
			<td valign="top">
				<div id="accordion" style="float:left;width:960px;">
					<?php $subLocDataArray = array();
					$dataArray = array();
					$lupCount = sizeof($taskData);
					for($i=0; $i<$lupCount; $i++){
						if($taskData[$i]['created_date'] != $taskData[$i]['last_modified_date'] && $taskData[$i]['status'] != ''){
							$disDate = date('d/m/Y', strtotime($taskData[$i]['last_modified_date']));
						}else{
							$disDate = '';
						}
						if($i==0){
							$dataArray[] = array($taskData[$i]['task_id'], $taskData[$i]['task'], $taskData[$i]['status'], $disDate, $taskData[$i]['signoff_image'], $taskData[$i]['comments']);
						}else{
							if($subLocID == $taskData[$i]['sub_location_id']){
								$dataArray[] = array($taskData[$i]['task_id'], $taskData[$i]['task'], $taskData[$i]['status'], $disDate, $taskData[$i]['signoff_image'], $taskData[$i]['comments']);
							}else{
								$subLocDataArray[$subLocID] = $dataArray;
								$dataArray = array();
								$dataArray[] = array($taskData[$i]['task_id'], $taskData[$i]['task'], $taskData[$i]['status'], $disDate, $taskData[$i]['signoff_image'], $taskData[$i]['comments']);
							}
						}
						$subLocID = $taskData[$i]['sub_location_id'];
					}
					$subLocDataArray[$subLocID] = $dataArray;
					$subLocids = array_keys($subLocDataArray);
					if(!empty($subLocDataArray)){
						for($i=0; $i<sizeof($subLocids); $i++){?>
							<h3 class="location_header" style="margin:0 0 0 0;float:center;cursor:pointer;text-align:left;width:960px;" onclick="toggleFolder('f<?php echo $subLocids[$i];?>');">
								<span style="text-decoration:none; color:#FFFFFF; font-weight:bold;font-size:large">
									<?php echo stripslashes(substr($locArrayData[$subLocids[$i]], 0, 70));
									if(strlen($locArrayData[$subLocids[$i]]) > 70){
										echo '...';
									}?>
								</span>
								<div style="text-align:right;float:right;margin-right:10px; margin-top:2px;z-index:!important;">Click to view</div>
							</h3>
							<table width="960px" class="locationGroup" align="center" border="0" cellpadding="5" cellspacing="0" bgcolor="#CCCCCC" id="f<?php echo $subLocids[$i];?>" name="f<?php echo $subLocids[$i];?>" style="<?=$toggle;?>;border:1px solid #000000;">
								<tr class="grey_header">
									<th style="color:#FFFFFF;text-align:left;font-size:medium;width:5%">
										<input type="checkbox" id="checkall_<?php echo $subLocids[$i];?>" onclick="toggleCheck(this, 'f<?php echo $subLocids[$i];?>');">
									</th>
									<th style="color:#FFFFFF;text-align:left;font-size:medium;width:40%">Task</th>
									<th style="color:#FFFFFF;text-align:left;font-size:medium;width:5%">Status</th>
									<th style="color:#FFFFFF;text-align:left;font-size:medium;width:50%">Comment</th>
								</tr>
							<?php $subArray = $subLocDataArray[$subLocids[$i]];
							$taskLupCount = sizeof($subArray);
							for($j=0; $j<$taskLupCount; $j++){?>
								<tr>
									<td style="color:#000000">
										<?php if($subArray[$j][2] == 'Yes'){
											$checkBox = '<input type="checkbox" name="taskID[]" id="taskID" value="'.$subArray[$j][0].'" disabled="disabled">';
										}else{
											$checkBox = '<input type="checkbox" name="taskID[]" id="taskID" value="'.$subArray[$j][0].'">';
										}
										echo $checkBox; ?>
									</td>
									<td style="color:#000000"><?php echo $subArray[$j][1]; ?></td>
									<td style="color:#000000"><?php echo $subArray[$j][2]; ?></td>
									<td style="color:#000000"><?php echo $subArray[$j][5]; ?></td>
								</tr>
							<?php }?>	
							</table><br/>
						<?php }
					}?>
				</div>
			</td>
		</tr>
	</table>
</form>
<?php }
}?>