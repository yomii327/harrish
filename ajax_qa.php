<?php session_start();

include_once("includes/commanfunction.php");
$_SESSION['pasteCount'] = 1;

$obj = new COMMAN_Class();

$location_query = "SELECT location_id, location_title, location_tree_name FROM qa_task_locations WHERE project_id = ".$_SESSION['idp']." AND location_parent_id = '".$_POST['cetid']."' AND is_deleted = '0' ORDER BY location_title";

$locationDepth = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'qa_num_sublocations');

if($_POST['cetid'] == 0){
	$class = 'telefilms';
}else{
	$class = '';
}
$res = mysql_query($location_query);
$isLocation = mysql_num_rows($res);
if($isLocation > 0){
	$nextStatus = true;
	while($locations = mysql_fetch_array($res)){
	$isSpecial = sizeof(explode(" > ", $locations['location_tree_name']));
	if($isSpecial == $locationDepth+1){
		$menu = "demo2";
		$nextStatus = false;
	}else{
		$menu = "demo1";
	}?>
	<ul  class="<?=$class?>" style="display:none" >
		<li id="li_<?=$locations['location_id']?>">
			<?php $taskData = $obj->selQRYMultiple('task_id, task', 'qa_task_monitoring', 'sub_location_id = '.$locations['location_id'].' AND is_deleted= 0 AND project_id = '.$_SESSION['idp'].' ORDER BY task');
			if($nextStatus){$data = "";
				$data = $obj->recurtionQA($locations['location_id'], $_SESSION['idp']);
				if($data != ""){?>
					<span style="cursor:pointer;" class="jtree-arrow close"><img src="images/plus-icon.png"></span>
			<?php }
			}?>
			<span class="jtree-button <?=$menu?>" id="<?=$locations['location_id']?>"><?=stripslashes($locations['location_title'])?></span>
		</li>	
<?php	$task = '';
		if(!empty($taskData)){
			foreach($taskData as $tData){
				$task .= '<li id="li_'.$tData['task_id'].'" class="taskList"><span class="jtree-button demo3" id="'.$tData['task_id'].'"><img src="images/task_simbol.png">&nbsp;'.$tData['task'].'</span></li>';
			}
		}
		echo $task;?>
	</ul>
<?php }
}?>