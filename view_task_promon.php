<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_GET['uniqueId'])){
	$taskData = $obj->getRecordByQuery('SELECT
		pm.progress_id AS progressID,
		pml.location_title AS location,
		pm.sub_location_id AS sublocation,
		pm.task AS task,
		pm.start_date AS startDate,
		pm.end_date AS endDate,
		pm.holding_point AS holdingPoint,
		group_concat(pmi.issued_to_name) AS issueTo
	FROM
		progress_monitoring AS pm
	LEFT JOIN
		issued_to_for_progress_monitoring as pmi on pm.progress_id = pmi.progress_id AND pmi.is_deleted = 0
	INNER JOIN
		project_monitoring_locations AS pml ON pm.location_id = pml.location_id
	WHERE
		pml.is_deleted = 0 AND pm.is_deleted = 0 AND pm.project_id = '.$_SESSION['idp'].' AND pm.progress_id = '.$_GET['progress_id'].' GROUP BY pm.progress_id');
	
	/*echo 'SELECT
		pm.progress_id AS progressID,
		pml.location_title AS location,
		pm.sub_location_id AS sublocation,
		pm.task AS task,
		pm.start_date AS startDate,
		pm.end_date AS endDate,
		pm.holding_point AS holdingPoint,
		group_concat(pmi.issued_to_name) AS issueTo
	FROM
		progress_monitoring AS pm
	LEFT JOIN
		issued_to_for_progress_monitoring as pmi on pm.progress_id = pmi.progress_id AND pmi.is_deleted = 0
	INNER JOIN
		project_monitoring_locations AS pml ON pm.location_id = pml.location_id
	WHERE
		pml.is_deleted = 0 AND pm.is_deleted = 0 AND pm.project_id = '.$_SESSION['idp'].' AND pm.progress_id = '.$_GET['progress_id'].' GROUP BY pm.progress_id';
	*/	
	if(!empty($taskData)){?>
		<fieldset class="roundCorner">
			<legend style="color:#000000;">View Task</legend>
			<table border="0" width="100%" cellpadding="0" cellspacing="0" class="collapse">
				<tr>
					<td width="30%">Task</td>
					<td width="70%" ><?=$taskData[0]['task']?></td>
				</tr>
				<tr>
					<td>Location</td>
					<td><?=$taskData[0]['location']?></td>
				</tr>
				<tr>
					<td>Sub Location</td>
					<td><?=$obj->subLocationsProgressMonitoring_update($taskData[0]['sublocation'], ' > ')?></td>
				</tr>
				<tr>
					<td>Issued To</td>
					<td><?=$taskData[0]['issueTo']?></td>
				</tr>
				<tr>
					<td>Start Date</td>
					<td><?php if($taskData[0]['startDate'] != '0000-00-00')
						echo date('d/m/Y', strtotime($taskData[0]['startDate']))?></td>
				</tr>
				<tr>
					<td>End Date</td>
					<td><?php if($taskData[0]['endDate'] != '0000-00-00')
						echo date('d/m/Y', strtotime($taskData[0]['endDate']))?></td>
				</tr>
				<tr>
					<td>Hold Point</td>
					<td><?=$taskData[0]['holdingPoint']?></td>
				</tr>	
			</table>
		</fieldset>
<?php }
}
?>