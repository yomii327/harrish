<?php
error_reporting(0);
session_start();
require_once'includes/functions.php';
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$owner_id = $_SESSION['ww_builder_id'];
$phd='';
$myProjects='';
$ihd='';
$myInspections='';
$where='';$or='';
//print_r($_POST); die;
$precentageArr = array(0,5,15,25,35,45,55,65,75,85,95,100);
if(isset($_REQUEST['SearchInsp'])){

	$_SESSION['pm'] = $_REQUEST;//Set Session for back implement and Remeber
	setcookie($_SESSION['ww_builder_id'].'_pm', serialize($_REQUEST), time()+864000);

	$checkProstr = '';
	if(!empty($_REQUEST['projName'])){
		$where = " up.project_id='".$_REQUEST['projName']."' AND up.project_id = pm.project_id" ;
	}
	
	if(!empty($_REQUEST['location']) && empty($_REQUEST['sublocation'])){
		$where .= " AND (pm.location_id='".$_REQUEST['location']."')";
	}
	
	if(!empty($_REQUEST['sublocation']) && empty($_REQUEST['subLocation_sub'])){
		$sublocations = $object->subLocationsIdProgressMonitoring ($_REQUEST['sublocation'], ",");
	    $where .= " AND (pm.sub_location_id in (".$sublocations."))";
	}
	
	if (!empty ($_REQUEST['subLocation_sub'])){
#		$where .= " AND (pm.sub_location_id = ".$_REQUEST['subLocation_sub'].")";
		$sublocations1 = $object->subLocationsIdProgressMonitoring ($_REQUEST['subLocation_sub'], ",");
	    $where .= " AND (pm.sub_location_id in (".$sublocations1."))";
	}
	
	if(!empty($_REQUEST['status'])){
		$where .= " AND (pm.status='".$_REQUEST['status']."')";
	}
	
	if(!empty($_REQUEST['searchKeyword'])){
		$_REQUEST['searchKeyword'] = str_replace(array('%', '_'), array('\%', '\_'), $_REQUEST['searchKeyword']);
		$where .= " AND (pm.task LIKE '%".trim($_REQUEST['searchKeyword'])."%' OR pm.location_tree_name  LIKE '%".trim($_REQUEST['searchKeyword'])."%')";
	}
	
	if($_REQUEST['DRF']!=""){
		$sdate = date("Y-m-d", strtotime($_REQUEST['DRF'] . "00:00:00"));
		$where .= " AND (pm.start_date>='".$sdate."'";
	}

	if($_REQUEST['DRT']!=""){
		$sdate2 = date("Y-m-d", strtotime($_REQUEST['DRT'] . "00:00:00"));
		$where .= " AND pm.start_date<='".$sdate2."')";
	}
	
	if($_REQUEST['FBDF']!=""){
		$edate = date("Y-m-d", strtotime($_REQUEST['FBDF'] . "00:00:00"));
		$where .= " AND (pm.end_date>='".$edate."'";
	}
	
	if($_REQUEST['FBDT']!=""){
		$edate = date("Y-m-d", strtotime($_REQUEST['FBDT'] . "00:00:00"));
		$where .= " AND pm.end_date<='".$edate."')";
	}
	
	$group = "GROUP BY pm.sub_location_id ORDER BY pm.location_tree_name";

	$qi = "SELECT pm.sub_location_id FROM user_projects up, progress_monitoring pm WHERE";
	$query = $qi.$where . " AND pm.is_deleted = 0 AND up.is_deleted = 0 " .$group;

	$rset = mysql_query($query);
	if(mysql_num_rows($rset) > 0){?>
<ul id="optionBar"><li><label for="holeTask" align="absmiddle" style="vertical-align:top; line-height: 49px;"><input type="checkbox" name="holeTask" id="holeTask" style="margin: 20px 0 15px 30px; vertical-align:top" onclick="holeProjectChecked(this);">&nbsp;Select All Task
</label></li><li><select name="statusChange" id="statusChange" class="select_box" style="margin:11px 6px 4px 50px; width:160px;background-image:url(images/input_160.png);"><option value="">Select Action</option><option value="NA">NA</option><option value="In progress">In progress</option><option value="Behind">Behind</option><option value="Complete">Complete	</option><option value="Signed off">Signed off</option></select></li>
<li><input type="button" id="closeTask" class="green_small" style="margin:13px 0 0 50px;" value="Submit" onClick="closetaskids();"  /></li>
<li><input type="button" id="closeTask" class="green_small" style="margin:13px 0 0 50px;" value="Change start/Finish dates" onClick="bulkChangeDate();" /></li></ul>
<br clear="all" />
<form name="allTaskTable" id="allTaskTable">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
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
			<div id="accordion" style="float:left;width:980px;">
	<?php $highSelectBox = '';
	while ($row=mysql_fetch_array($rset)) {
		$location_title = "";
		$where = "";
		$parent_loc_id = $row['sub_location_id'];
		$location_title = $object->subLocationsProgressMonitoring($parent_loc_id, ' > ');
		$include_issue_to = "";
		if(!empty($_REQUEST['issuedTo'])){
			$include_issue_to = ", issued_to_for_progress_monitoring ipm";
			$where .= " AND ipm.issued_to_name = '".addslashes(trim($_REQUEST['issuedTo']))."' AND pm.progress_id = ipm.progress_id";
		}
		
		if($parent_loc_id == 0){
			$query = "SELECT pm.progress_id, pm.task, pm.start_date, pm.end_date, pm.percentage, pm.status FROM progress_monitoring pm $include_issue_to WHERE (pm.location_id = '$loc_id') AND pm.is_deleted = 0";
		}else{
			$query = "SELECT pm.progress_id, pm.task, pm.start_date, pm.end_date, pm.percentage, pm.status FROM progress_monitoring pm $include_issue_to WHERE (pm.sub_location_id = '$parent_loc_id') AND pm.is_deleted = 0";
		}
		
		if(!empty($_REQUEST['status'])){
			$where .= " and (pm.status='".$_REQUEST['status']."')";
		}
		
		if(!empty($_REQUEST['searchKeyword'])){
			$_REQUEST['searchKeyword'] = str_replace(array('%', '_'), array('\%', '\_'), $_REQUEST['searchKeyword']);
			$where .= " AND (pm.task LIKE '%".trim($_REQUEST['searchKeyword'])."%' OR pm.location_tree_name  LIKE '%".trim($_REQUEST['searchKeyword'])."%')";
		}
		
		if($_REQUEST['DRF']!=""){
			$sdate = date("Y-m-d", strtotime($_REQUEST['DRF'] . "00:00:00"));
			$where .= " and (pm.start_date>='".$sdate."'";
		}
		
		if($_REQUEST['DRT']!=""){
			$sdate2 = date("Y-m-d", strtotime($_REQUEST['DRT'] . "00:00:00"));
			$where .= " and pm.start_date<='".$sdate2."')";
		}
		
		if($_REQUEST['FBDF']!=""){
			$edate = date("Y-m-d", strtotime($_REQUEST['FBDF'] . "00:00:00"));
			$where .= " and (pm.end_date>='".$edate."'";
		}
		
		if($_REQUEST['FBDT']!=""){
			$edate = date("Y-m-d", strtotime($_REQUEST['FBDT'] . "00:00:00"));
			$where .= " and pm.end_date<='".$edate."')";
		}
		
		$query .= $where . " group by progress_id order by progress_id ";

		$rsq = mysql_query($query);

		$toggle = "display:none";
		if (mysql_num_rows($rsq) <= 0)
			continue; ?>
			<h3 class="location_header green_small" style="margin:0 0 0 0;float:center;cursor:pointer;text-align:left;width:960px;line-height:30px;" onclick="toggleFolder('f<?php echo $parent_loc_id?>');">
				<span style="text-decoration:none; color:#FFFFFF; font-weight:bold;font-size:medium;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;width:800px;display:block;">
					<?php echo $location_title?>
				</span>
				<div style="text-align:right;margin-left:850px; margin-top:-28px;z-index:55 !important;position:absolute;">Click to view</div>
			</h3>
			<table width="960px" align="center" border="0" cellpadding="5" cellspacing="0" bgcolor="#CCCCCC" id="f<?php echo $parent_loc_id?>" name="f<?php echo $parent_loc_id;?>" style="<?php echo $toggle?>;border:1px solid #000000;">
				<tr class="grey_header">
					<th style="color:#FFFFFF;text-align:left;font-size:medium;width:5%">
						<input type="checkbox" id="checkall<?=$parent_loc_id?>" onclick="toggleCheck(this, 'f<?php echo $parent_loc_id?>');">
						<?php if($highSelectBox == ''){ 
								$highSelectBox = 'checkall'.$parent_loc_id;
							}else{
								$highSelectBox .= ','.'checkall'.$parent_loc_id;
							} ?>
						<input type="checkbox" name="taskID[]" di id="" value="" style="display:none;" />
					</th>
					<th style="color:#FFFFFF;text-align:left">Task</th>
					<th style="color:#FFFFFF;text-align:left">Issue to name</th>
					<th style="color:#FFFFFF;text-align:left">Date</th>
					<tH style="color:#FFFFFF;text-align:left">Percentage</th>
					<th style="color:#FFFFFF;text-align:left">Status</th>
					<th style="color:#FFFFFF;text-align:left">Action</th>
				</tr>
	<?php while ($row1 = mysql_fetch_array($rsq)) {
		$start_date = date("d/m/Y", strtotime($row1['start_date'] . "00:00:00"));
		$end_date = date("d/m/Y", strtotime($row1['end_date'] . "00:00:00"));
		$issueToData = $object->selQRYMultiple('issued_to_name', 'issued_to_for_progress_monitoring', 'progress_id = '.$row1['progress_id'].' and is_deleted=0');
		if(isset($_REQUEST['issuedTo']) && !empty($_REQUEST['issuedTo'])){
			$issueToData = $object->selQRYMultiple('issued_to_name', 'issued_to_for_progress_monitoring', 'progress_id = '.$row1['progress_id'].' and is_deleted=0 and  issued_to_name = "'.addslashes(trim($_REQUEST['issuedTo'])).'"');
		}
		$issued_to_name = "";
		foreach($issueToData as $isData){
			if ($issued_to_name==""){
				$issued_to_name = $isData['issued_to_name'];
			}else{
				$issued_to_name .= "<br/>" . $isData['issued_to_name'];
			}	
		}
		$status = $row1["status"];
		$row_color = "";
		if ($status == "Complete"){
			$row_color = "complete";
		}else if ($status == "Behind"){
			$row_color = "behind";
		}else if ($status == "Signed off"){
			$row_color = "signed_off";
		}else if ($status == "In progress"){
			$row_color = "in_progress";
		}else if ($status == "NA"){
			$row_color = "na";
		}?>
				<tr id="tr_<?=(int)$row1['progress_id']?>" class="<?=$row_color?>">
					<td>
						<?php if($status == "Signed off"){
							$checkBox = '<input type="checkbox" name="taskID[]" id="'.$row1['progress_id'].'" value="'.$row1['progress_id'].'" onClick="checkUncheckParent(this, '.$parent_loc_id.');">';
							if($checkProstr == ''){$checkProstr = $row1['progress_id'];}else{$checkProstr .= ','.$row1['progress_id'];}
						}else{
							if($checkProstr == ''){$checkProstr = $row1['progress_id'];}else{$checkProstr .= ','.$row1['progress_id'];}
							$checkBox = '<input type="checkbox" name="taskID[]" id="'.$row1['progress_id'].'" value="'.$row1['progress_id'].'" onClick="checkUncheckParent(this, '.$parent_loc_id.');">';
						}
						echo $checkBox; ?>
					</td>
					<td><?php echo $row1['task']; ?></td>
					<td><?php echo $issued_to_name; ?></td>
					<td><?php echo $start_date.'<br>'.$end_date; ?></td>
					<td>
						<?php //echo $row1['percentage']; ?>
						<select name="list_pechange" id="list_pechange_<?=(int)$row1['progress_id']?>" class="pselect-box" onchange="setPercetanges(this.value, '<?=$row1['progress_id']?>');">
							<option value="">Select</option>
							<?php foreach($precentageArr as $iRows) {
								$selected = ($iRows==$row1['percentage'])?'selected="selected"':'';
								echo '<option value="'.$iRows.'" '. $selected .'>'.$iRows.'%</option>';
							} ?>
						</select>
					</td>
					<td><span id="cur_status_<?=(int)$row1['progress_id']?>"><?php echo $row1['status']; ?></span></td>
					<td><img src="images/edit.png" align="absmiddle" alt="Edit Task" style="cursor:pointer;" onclick="editThis(<?php echo $row1['progress_id'];?>);" /></td>
				</tr>
	<?php }?>
			</table>
			<br/><br/>
	<?php }?>
			</div>
			<div id="checkProstr" style="display:none;"><?=$checkProstr?></div>
			<div id="checkHighProstr" style="display:none;"><?=$highSelectBox?></div>
		</td>
	</tr>
	</table>
</form>
	<?php }else{
		echo 'No Record Found';
	} 
}?>
