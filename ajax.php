<?php session_start();
include_once("includes/commanfunction.php");
$_SESSION['pasteCount'] = 1;
$obj = new COMMAN_Class();

$location_query = "SELECT location_id, location_title FROM project_locations WHERE project_id = ".$_SESSION['idp']." AND location_parent_id = '".$_POST['cetid']."' AND is_deleted = '0' ORDER BY order_id, location_id, location_title";
if($_POST['cetid'] == 0){
	$class = 'telefilms';
}else{
	$class = '';
}
$res = mysql_query($location_query);
$isLocation = mysql_num_rows($res);
if($isLocation > 0){
	while($locations = mysql_fetch_array($res)){ ?>
	<ul  class="<?=$class?>" style="display:none" >
		<li id="li_<?=$locations['location_id']?>">
			<?php $data = $obj->recurtion($locations['location_id'], $_SESSION['idp']);
			if($data != ''){ ?>
				<span style="cursor:pointer;" class="jtree-arrow close"><img src="images/plus-icon.png"></span>
			<?php }?>
			<span class="jtree-button demo1" id="<?=$locations['location_id']?>"><?=stripslashes($locations['location_title'])?></span>
		</li>
	</ul>
<?php }
}?>