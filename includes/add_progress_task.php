<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
require_once'includes/commanfunction.php';
$object = new COMMAN_Class();

$builder_id=$_SESSION['ww_builder_id'];

$row = $object->selQRY('pr_num_sublocations', 'user_projects', 'project_id = "'.$_SESSION['idp'].'"');
$pr_num_sublocations = $row["pr_num_sublocations"];

if(isset($_POST['button_x'])){
	$parentId = 0;//deside location parent for tree
	$locTree = '';//new location ids tree logic
	$locTreeArray = array();
	if(isset($_POST['task']) && !empty($_POST['task'])){
		$task = $_POST['task'];	
	}else{
		$task_err = '<div class="error-edit-profile">The task field is required</div>';
	}
	
	if(isset($_POST['loaction']) && !empty($_POST['loaction']) && $_POST['loaction'] != 'otherLoc1'){
		$parentId = $_POST['loaction'];
		$loaction = $_POST['loaction'];
		if($locTree == ''){
			$locTree = $_POST['loaction'];
		}else{
			$locTree .= ' > '.$_POST['loaction'];
		}
	}else if(isset($_POST['txtLocation']) && !empty($_POST['txtLocation'])){
		$otherloc = $_POST['txtLocation'];
		$locTreeArray[] = $_POST['txtLocation'];
	}
	
//recurrsively for sub locations 
	$subloc = '';$exLoc = '';
	for($i=0; $i<=sizeof($_POST['sublocation']); $i++){
		if(isset($_POST['sublocation'][$i]) && !empty($_POST['sublocation'][$i]) && $_POST['sublocation'][$i] != 'otherLoc'.($i+2)){
			if($_POST['sublocation'][$i] != ''){
				$subloc = $_POST['sublocation'][$i];
			}
			$parentId = $subloc;
			$loaction.$$i = $subloc;
			if($locTree == ''){
				$locTree = $subloc;
			}else{
				$locTree .= ' > '.$subloc;
			}
		}else{
			if($_POST['txtSublocation'][$i] != ''){
				$exLoc = $_POST['txtSublocation'][$i];
			}
			$sublocation.$$i = $exLoc;
			$locTreeArray[] = $exLoc;
		}	
	}

	if(isset($_POST['sdate']) && !empty($_POST['sdate'])){
		$sdate=$_POST['sdate'];	
		$date=explode('/',$sdate);
		$sdate=$date[2].'-'.$date[1].'-'.$date[0];
	}else{
		$sdate_err='<div class="error-edit-profile">The start date field is required</div>';
	}
	
	if(isset($_POST['edate']) && !empty($_POST['edate'])){
		$edate=$_POST['edate'];	
		$date=explode('/',$edate);
		$edate=$date[2].'-'.$date[1].'-'.$date[0];
	}else{
		$edate_err='<div class="error-edit-profile">The end date field is required</div>';
	}
	
	if(isset($_POST['issueto']) && !empty($_POST['issueto'])){
		$issueto = $_POST['issueto'];	
		$issue_to = explode(',',$issueto);
		$total_issue = count($issue_to);
	}
//Location tree create here
	if(empty($locTreeArray)){
	}else{
		$sublocTree = $object->QArecursiveInsertLocation($locTreeArray, $parentId, $_SESSION['idp'], $builder_id, $locTree);	
	}
	
	$locArray = explode(' > ', $locTree);
	$locID = $locArray[0];
	$subLocID = end($locArray);
	$rowLocationTreeNameInsert = $object->locId2LocName($locTree);
	if(isset($task) || isset($locID)  || isset($subLocID)|| isset($sdate) || isset($edate)){
		$percentage = '0%';
		$status = '';
		$progressInsert = "INSERT INTO progress_monitoring SET
								project_id = '".$_SESSION['idp']."',
								location_id = '".$locID."',
								sub_location_id = '".$subLocID."',
								task = '".addslashes(trim($task))."',
								start_date = '".$sdate."',
								end_date = '".$edate."',
								last_modified_date = NOW(),
								last_modified_by = '".$builder_id."',
								created_date = NOW(),
								created_by = '".$builder_id."',
								status = '".$status."',
								percentage = '".$percentage."',
								location_tree = '".$locTree."',
								location_tree_name = '".$rowLocationTreeNameInsert."',
								original_modified_date = NOW()";
		mysql_query($progressInsert);
		$progressID = mysql_insert_id();
		
		for($i=0;$i<$total_issue;$i++){
			$issuedToInsert = "INSERT INTO issued_to_for_progress_monitoring SET
									project_id = '".$_SESSION['idp']."',
									progress_id = '".$progress_id."',
									issued_to_name = '".addslashes(trim($issue_to[$i]))."',
									last_modified_date = NOW(),
									last_modified_by = '".$builder_id."',
									created_date = NOW(),
									created_by = '".$builder_id."'";
			mysql_query($issuedToInsert); 
			
			$select_isseu = "SELECT * FROM inspection_issue_to WHERR issue_to_name = '".$issue_to[$i]."' AND project_id = ".$_SESSION['idp']." AND is_deleted = 0";
			$result_issue = mysql_query($select_isseu);
			$issue_row = mysql_num_rows($result_issue);
			if($issue_row == 0){
				$issue_insert = "INSERT INTO inspection_issue_to SET
									issue_to_name = '".addslashes(trim($issue_to[$i]))."',
									last_modified_date = NOW(),
									last_modified_by = '".$builder_id."',
									created_date = NOW(),
									created_by = '".$builder_id."',
									project_id = '".$_SESSION['idp']."'";
				mysql_query($issue_insert);
			}		 
		}
		$_SESSION['progress_add']='Progress task added successfully.';
		header('location:?sect=progress_monitoring');
	}else{
		$_SESSION['issue_add_err']='Progress task not added.';
	}
}?>
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/add_edit_progress_task.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.full.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"sdate",
			dateFormat:"%d/%m/%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"edate",
			dateFormat:"%d/%m/%Y"
		});
		
	};
</script>
<style type="text/css">
@import "css/jquery.datepick.css";
table td{padding:5px;}
table{ margin-left:10px;}
.error-edit-profile { width: 220px; }
</style>
<script language="javascript" type="text/javascript">
var IncreaseCount = <?=$pr_num_sublocations+1;?>;
<?php for($i=1; $i<=$pr_num_sublocations; $i++){?>
function SubLoc<?=$i?>(val){
	if(val==""){
		jAlert("Please select location");
		for(var i=<?=$i;?>; i<=<?=$pr_num_sublocations;?>; i++){
			document.getElementById('ShowSubLocation'+i).innerHTML = '<select name="sublocation[]" id="sublocation'+i+'"  class="select_box" style="margin-left:0px;" onchange="SubLoc'+(i+1)+'(this.value);"><option value="">Select</option></select>';
		}
		return false;
	}else{
		if(val == 'otherLoc<?=$i?>'){
		var lupStart = <?=($i-1);?>; 
		<?php if($i == 1){?>
			document.getElementById('divTxtLocation').style.display ='block';
			document.getElementById('ShowLocation').style.display ='none';	
			var lupStart = <?=$i;?>; 
		<?php }?>
			for(var i=lupStart; i<=<?=$pr_num_sublocations;?>; i++){
				document.getElementById('divTxtSublocation'+i).style.display ='block';
				document.getElementById('ShowSubLocation'+i).style.display ='none';
			}
		}else{
			AjaxShow("POST", "progress_location.php?type=sublocation<?=$i?>&&proID="+val, "sublocation<?=$i?>");
		}
	}
}
function resetSubLoc<?=$i?>(){
	for(var i=<?=$i?>; i<=<?=$pr_num_sublocations;?>; i++){
		if(i == 1){
			if(document.getElementById('textLocation').value == '' && document.getElementById('loaction').value == 'otherLoc1'){
				return false;
			}else{
				document.getElementById('ShowSubLocation'+i).style.display ='block';
				document.getElementById('divTxtSublocation'+i).style.display ='none';
				document.getElementById('sublocation'+i).value ='';
			}
		}else{
			if(document.getElementById('txtSublocation'+i).value == '' && document.getElementById('loaction').value == 'otherLoc'+(i-1)){
				return false;
			}else{
				document.getElementById('ShowSubLocation'+i).style.display ='block';
				document.getElementById('divTxtSublocation'+i).style.display ='none';
				document.getElementById('sublocation'+i).value ='';
			}
		}
	}
}
<?php }?>
function SubLoc<?=($pr_num_sublocations+1)?>(val){//No Aajax Request here
	if(val == 'otherLoc<?=($pr_num_sublocations+1)?>'){
		document.getElementById('ShowSubLocation<?=$pr_num_sublocations?>').style.display ='none';
		document.getElementById('divTxtSublocation<?=$pr_num_sublocations?>').style.display ='block';
	}
}
function resetLoc(){
	document.getElementById('ShowLocation').style.display ='block';
	document.getElementById('divTxtLocation').style.display ='none';
	document.getElementById('loaction').value ='';
	for(var i=1; i<=<?=$pr_num_sublocations;?>; i++){
		document.getElementById('ShowSubLocation'+i).style.display ='block';
		document.getElementById('divTxtSublocation'+i).style.display ='none';
		document.getElementById('sublocation'+i).value ='';
	}
}
function checkValues(){
	var flag = false;
	if(document.getElementById('textLocation').value == '' && document.getElementById('loaction').value == ''){
		document.getElementById('locationError').style.display ='block';
		flag = true;
	}else{
		document.getElementById('locationError').style.display ='none';
	}
	if(document.getElementById('txtSublocation1').value == '' && document.getElementById('sublocation1').value == ''){
		document.getElementById('subLocationError').style.display ='block';
		flag = true;
	}else{
		document.getElementById('locationError').style.display ='none';
	}
	if(flag){
		return false;
	}
}
function removeErrors(){
	if(document.getElementById('locationError').style.display == 'block'){
		document.getElementById('locationError').style.display = 'none';
	}
	if(document.getElementById('subLocationError').style.display == 'block'){
		document.getElementById('subLocationError').style.display = 'none';
	}
}
</script>
<!-- Ajax Post -->
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form  method="post"  enctype="multipart/form-data" name="add_editProgress" id="add_editProgress" onsubmit="checkValues()" >
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/hd_progress_task.png);margin-top:-50px\9;"></div>
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form" style="width:600px;">
						<table width="100%" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-60px;">
							<tr>
								<td>Task <span class="req">*</span></td>
								<td><input name="task" type="text" class="input_small" id="task" /></td>
							</tr>
							<tr>
								<td nowrap="nowrap">Location <span class="req">*</span></td>
								<td>
									<div id="ShowLocation" style="margin-top:10px;">
									<select name="loaction" id="loaction" class="select_box" style="margin-left:0px;" onchange="SubLoc1(this.value);" >
										<option value="" >Select</option>
										<?php $sql="select location_id,location_title from project_monitoring_locations where project_id=".$_SESSION['idp']." and is_deleted=0 and location_parent_id=0";
										$rs_m=mysql_query($sql);
										while($row=mysql_fetch_array($rs_m)){ ?>
											<option value="<?php echo $row['location_id'];?>"><?php echo $row['location_title'];?></option>
										<?php } ?>
											<option value="otherLoc1">Other</option>
									</select>
									</div>
									<div id="divTxtLocation" style="margin-top:10px;display:none;">
										<input name="txtLocation" type="text" class="input_small" id="textLocation" onkeydown="removeErrors()" />&nbsp;<a href="javascript:void(0);" title="Clear start date"><img src="images/redCross.png" onClick="resetLoc();" /></a>
										<div class="error-edit-profile" style="width: 220px; display: none;" id="locationError">The location field is required</div>
									</div>
								</td>
							</tr>
							<?php for($i=1; $i<=$pr_num_sublocations; $i++){?>
							<tr>
								<td nowrap="nowrap" >Sub Location <?=$i;?><?php if($i == 1){?><span class="req">*</span><?php }?></td>
								<td>
									<div id="ShowSubLocation<?=$i;?>" style="margin-top:10px;">
										<select name="sublocation[]" id="sublocation<?=$i;?>"  class="select_box" style="margin-left:0px;" onchange="SubLoc<?=$i+1;?>(this.value);<?php if($i == 1){echo 'removeErrors();';}?>">
											<option value="">Select</option>
											<option value="otherLoc<?=$i+1;?>" >Other</option>
										</select>
									</div>
									<div id="divTxtSublocation<?=$i;?>" style="margin-top:10px;display:none;">
										<input name="txtSublocation[]" type="text" class="input_small" id="txtSublocation<?=$i;?>" onkeydown="removeErrors()" />&nbsp;<a href="javascript:void(0);" title="Clear start date"><img src="images/redCross.png" onClick="resetSubLoc<?=$i;?>();" /></a>
									</div>
									<?php if($i == 1){?>
										<div class="error-edit-profile" style="width: 220px; display: none;" id="subLocationError">The sublocation field is required</div>
									<?php }?>
								</td>
							</tr>	
							<?php }?>
							<tr>
								<td width="133">Start Date<span class="req">*</span></td>
								<td width="252">
									<input name="sdate" type="text" class="input_small" id="sdate"  readonly="readonly"/>
								</td>
							</tr>
							<tr>
								<td>End Date <span class="req">*</span></td>
								<td>
									<input name="edate" type="text" class="input_small" id="edate" readonly="readonly" />
								</td>
							</tr>
							<tr>
								<td>Issue To</td>
								<td>
									<input name="issueto" type="text" class="input_small" id="issueto" />
								</td>
							</tr>							
							<tr>
								<td>&nbsp;</td>
								<td>
									<input type="hidden" value="add_project" name="sect" id="sect" />
									<input name="button" type="image" class="submit_btn" id="button" value="submit" src="images/save.png" style="border:none; width:111px;" />
									<a href="javascript:history.back();"><img src="images/back_btn.png" style="border:none; width:111px;" /></a>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>