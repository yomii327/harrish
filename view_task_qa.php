<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

if(isset($_GET['uniqueId'])){
	$taskData = $obj->getRecordByQuery('SELECT
			pm.task_id AS progressID,
			pml.location_title AS location,
			pm.sub_location_id AS sublocation,
			pm.task AS task,
			pm.status AS status,
			pm.comments AS comments
		FROM
			qa_task_locations AS pml 
		INNER JOIN
			qa_task_monitoring as pm ON pm.location_id = pml.location_id
		WHERE
			pml.is_deleted = 0 AND pm.is_deleted = 0 AND pm.project_id = '.$_SESSION['idp'].' AND pm.task_id = '.$_GET['task_id'].'
		GROUP BY pm.task_id');
	if(!empty($taskData)){?>
		<fieldset class="roundCorner">
			<legend style="color:#000000;">View Task</legend>
				<table border="0" width="100%" cellpadding="5" cellspacing="5" class="collapse">
					<tr>
						<td width="40%">Task</td>
						<td width="60%" ><?=$taskData[0]['task']?></td>
					</tr>
					<tr>
						<td>Location</td>
						<td><?=$taskData[0]['location']?></td>
					</tr>
					<tr>
						<td>Sub Location</td>
						<td><?=$obj->QAsubLocationsProgressMonitoring($taskData[0]['sublocation'], ' > ')?></td>
					</tr>
					<tr>
						<td>Status</td>
						<td><?=$taskData[0]['status']?></td>
					</tr>
					<tr>
						<td>Comments</td>
						<td><?=$taskData[0]['comments']?></td>
					</tr>	
				</table>
		</fieldset>
<?php }
}
?>