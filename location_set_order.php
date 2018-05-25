<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
//Set Chapter Order Part Start Here
if (isset($_REQUEST['location_id'])) {
	$_pos = strpos($_REQUEST['location_id'], '_');
	if ($_pos === false)
		$chapterID = $_REQUEST['location_id'];
    else
		$chapterID = end(explode('_', $_REQUEST['location_id']));

    $chapterData = array();

    $chapterData = array();

    if ($_pos === false){
        //$chapterData = $obj->selQRYMultiple('chapter_id, chpter_parent_id, chapter_title', 'manual_chapter', 'chpter_parent_id = 0 AND is_deleted = 0 AND project_id = '.$_SESSION['project_id'].' AND version_id = '.$_SESSION['versionID'].' '.$searchCondition.' ORDER BY order_id, chapter_id');
        $chapterData = $obj->selQRYMultiple('location_id, location_parent_id, location_title', 'project_locations', 'location_parent_id = ' . $chapterID . '  AND is_deleted = 0 AND project_id = ' . $_SESSION['idp'] . ' ' . $searchCondition . ' ORDER BY order_id, location_id');                //echo "<pre>";
        //print_r($chapterData);
    }else{
        $chapterData = $obj->selQRYMultiple('location_id, location_parent_id, location_title', 'project_locations', 'location_parent_id = 0  AND is_deleted = 0 AND project_id = ' . $_SESSION['idp'] . ' ORDER BY order_id, location_id');
        //$chapterData = $obj->selQRYMultiple('chapter_id, chpter_parent_id, chapter_title', 'manual_chapter', 'chpter_parent_id = '.$chapterID.' AND is_deleted = 0 AND project_id = '.$_SESSION['project_id'].' AND version_id = '.$_SESSION['versionID'].' '.$searchCondition.' ORDER BY  chapter_id','Y');
        //echo "Ins side else";
    }
    if (!empty($chapterData)){?>
<fieldset class="roundCorner" style="color:#000">
		<legend style="color:#000000;">Set Order for Location</legend>
		<!--<h5>* Drag location name to adjust location order for wall chart report</h5>-->
		<div id="msgHolderDiv" class="success_r" style="display:none;width: 460px;"><p id="msgHolder"></p></div>
		<!-- <input type="button" class="submit_btn" name="Submit" style="background-image:url(images/save_min.png); font-size:0px; border:none; width:111px;margin:-15px 0 0 530px;height:29px;" onclick="saveOrderFunc()" /> -->
		<input type="button" class="green_small" name="Submit" style="cursor:pointer; border:none; width:100px;margin:0 0 0 530px;height:29px;" onclick="saveOrderFunc()" value="Save" />

		<ul id="sortable1" class="connectedSortable">
                <?php foreach ($chapterData as $chData) { ?>
                    <li class="ui-state-default" id="<?php echo $chData['location_id']; ?>" style="list-style:decimal; list-style-position:outside;text-align:left;"><?php echo $chData['location_title']; ?></li>
                <?php } ?>
            </ul>
		<!-- <input type="button" class="submit_btn" name="Submit" style="background-image:url(images/save_min.png); font-size:0px; border:none; width:111px;margin-left:530px;height:29px;" onclick="saveOrderFunc()" /> -->
		<input type="button" class="green_small" name="Submit" style="cursor:pointer; border:none; width:100px;margin:0 0 0 530px;height:29px;" onclick="saveOrderFunc()" value="Save" />
        <?php
        }else{
		echo 'No Data Found';
	}?>
    </fieldset>
<?php }
if(isset($_REQUEST['uniqueId'])){
	$projID = $_SESSION['idp'];
	$locId = $_POST['locId'];
	$locVal = $_POST['locVal'];
	
	$outputArr = array('status'=> false, 'msg'=> 'Location order update failed please try again later.');
	
	for($i=0; $i<sizeof($locId); $i++){
		$query = "UPDATE project_locations SET order_id = ".($i+1).", last_modified_date = NOW() WHERE project_id = ".$projID." AND is_deleted = 0 AND location_id in (".$locId[$i].")";
		mysql_query($query);
	}
	if(mysql_affected_rows() > 0)
		$outputArr = array('status'=> true, 'msg'=> 'Location order updated successfully.');
		
	echo json_encode($outputArr);
}
//Set Chapter Order Part End Here
//Set Files Order Part Start Here
if(isset($_REQUEST['title'])){
	$filesData = array();
	
	$filesData = $obj->selQRYMultiple('id, file_name, file_title', 'manual_chapter_data_v1', 'is_deleted = 0 AND chaper_id = '.$_REQUEST['chapterID'].' AND project_id = '.$_SESSION['project_id'].' AND version_id = '.$_SESSION['versionID'].' ORDER BY order_id');

	if(!empty($filesData)){?>
		<fieldset class="roundCorner">
		<legend style="color:#000000;">Set Order for Files</legend>
		<!--<h5>* Drag location name to adjust location order for wall chart report</h5>-->
		<div id="msgHolderDiv" class="success_r" style="display:none;width: 460px;"><p id="msgHolder"></p></div>
		<!-- <input type="button" class="submit_btn" name="Submit" style="background-image:url(images/save_min.png); font-size:0px; border:none; width:111px;margin:-15px 0 0 530px;height:29px;" onclick="saveOrderFilesFunc()" /> -->
		<input type="button" class="green_small" name="Submit" style="cursor:pointer; border:none; width:100px;margin:0 0 0 530px;height:29px;" onclick="saveOrderFilesFunc()" value="Save" />
		<ul id="sortable1" class="connectedSortable">
			<?php foreach($filesData as $flData){?>
				<li class="ui-state-default" id="<?php echo $flData['id'];?>" style="list-style:decimal; list-style-position:outside;text-align:left;"><?php echo $flData['file_title'];?></li>
			<?php }?>
		</ul>
		<!-- <input type="button" class="submit_btn" name="Submit" style="background-image:url(images/save_min.png); font-size:0px; border:none; width:111px;margin-left:530px;height:29px;" onclick="saveOrderFilesFunc()" /> -->
		<input type="button" class="green_small" name="Submit" style="cursor:pointer; border:none; width:100px;margin:0 0 0 530px;height:29px;" onclick="saveOrderFilesFunc()" value="Save" />
<?php }else{
		echo 'No Data Found';
}?>
	</fieldset>
<?php }
if(isset($_REQUEST['infrequentID'])){
	$projID = $_SESSION['project_id'];
	$locId = $_POST['locId'];
	$locVal = $_POST['locVal'];
	
	$outputArr = array('status'=> false, 'msg'=> 'File order update failed please try again later.');
	
	for($i=0; $i<sizeof($locId); $i++){
		$query = "UPDATE manual_chapter_data_v1 SET order_id = ".($i+1).", last_modified_date = NOW() WHERE project_id = ".$projID." AND is_deleted = 0 AND version_id = ".$_SESSION['versionID']." AND id in (".$locId[$i].")";
		mysql_query($query);
	}
	if(mysql_affected_rows() > 0)
		$outputArr = array('status'=> true, 'msg'=> 'File order updated successfully.');
		
	echo json_encode($outputArr);
}
//Set Files Order Part End Here?>