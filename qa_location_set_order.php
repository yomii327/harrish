<?php include('includes/commanfunction.php'); 
$obj = new COMMAN_Class(); ?>
<div id="htmlContainer" style="color:#000000;">
	<h3>Location List</h3>
	<h5>* Drag location name to adjust location order for wall chart report</h5>
	
	<div id="msgHolderDiv" class="success_r" style="display:none;width: 460px;"><p id="msgHolder"></p></div>
	<input type="button" class="submit_btn" name="Submit" style="background-image:url(images/save_min.png); font-size:0px; border:none; width:111px;margin:-15px 0 0 530px;height:29px;" onclick="saveLocationOrder()" />
	<?php 
#SELECT loc.location_id, loc.location_title, cast(group_concat(distinct loc.location_id) AS CHAR) FROM `qa_task_locations` as loc, qa_task_monitoring as task WHERE task.sub_location_id = loc.location_id and  loc.location_title = 'FLEX TO CEILING' and  loc.`project_id` = 138  group by loc.location_title 
	$locIDs = $obj->selQRYMultiple('loc.location_id, loc.location_title, GROUP_CONCAT(distinct loc.location_id) AS locationIDs, task.excluded_location', 'qa_task_locations AS loc, qa_task_monitoring AS task', 'task.sub_location_id = loc.location_id AND loc.`project_id` = '.$_REQUEST['projectID'].' AND task.is_deleted = 0 AND loc.is_deleted = 0 GROUP BY loc.location_title ORDER BY task.subloc_order_wall_chart_report');	
#	echo '<pre>';print_r($subLocationArr);die;
?>
	<span style="float:right;"><strong>CHECK FOR DO NOT SHOW</strong></span>
	<br />
	<br clear="all" />
	<ul id="sortable1" class="connectedSortable">
		<?php foreach($locIDs as $locs){
			$isSelected = '';?>
			<li class="ui-state-default" id="<?php echo $locs['locationIDs'];?>" style="list-style:decimal; list-style-position:outside;"><?php echo $locs['location_title'];?>
			<?php if($locs['excluded_location'] == 'YES'){
				$isSelected = 'checked="checked"';
			}?>
			<input type="checkbox" name="excludedLoc" id="excludedLoc" value="1" style="float:right;" <?=$isSelected;?> /></li>
		<?php }?>
	</ul>
	<input type="button" class="submit_btn" name="Submit" style="background-image:url(images/save_min.png); font-size:0px; border:none; width:111px;margin:15px 0 0 530px;height:29px;" onclick="saveLocationOrder()" />
</div>
