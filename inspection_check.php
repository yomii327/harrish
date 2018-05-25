<?php session_start();

include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
if(isset($_REQUEST['projectID'])){
	$projectID = $_REQUEST['projectID'];
	$inspectionID = $_REQUEST['inspectionID'];
	$checklistType = $_REQUEST['checklistType'];
	$locationName = $_REQUEST['locationName'];
	$valueFlag = false;
	
	if($checklistType == 'add'){
		$namePart = explode(' ', $locationName);
		$countNamePart = sizeof($namePart);
		if($countNamePart > 1){
			$isSD = array();
			for($i=0;$i<$countNamePart;$i++){
				$sdName = $obj->selQRYMultiple('distinct(check_list_items_name), check_list_items_id, check_list_items_tags', 'check_list_items', 'project_id = '.$projectID.' AND (check_list_items_tags LIKE "%'.$namePart[$i].'%" OR check_list_items_tags = "" OR check_list_items_tags IS NULL ) AND is_deleted = 0 AND check_list_items_option = "Quality Control"');
				$isSD = array_merge($isSD, $sdName);
			}
			$checkListData = array_map('unserialize', array_unique(array_map('serialize', $isSD)));
		}elseif($countNamePart == 1){
			$sdName = $obj->selQRYMultiple('distinct(check_list_items_name), check_list_items_id, check_list_items_tags', 'check_list_items', 'project_id = '.$projectID.' AND (check_list_items_tags LIKE "%'.$locationName.'%" OR check_list_items_tags = "" OR check_list_items_tags IS NULL ) AND is_deleted = 0 AND check_list_items_option = "Quality Control"');
			$checkListData = array_map('unserialize', array_unique(array_map('serialize', $sdName)));
		}
#		$checkListData = $obj->selQRYMultiple('check_list_items_id, check_list_items_name, check_list_items_tags', 'check_list_items', 'project_id = '.$projectID.' AND is_deleted = 0 ORDER BY check_list_items_id'); ?>
	<div style="max-height:370px;overflow-y:scroll;">
	<form name="checklistValues" id="checklistValues">
		<table width="100%" border="0" cellspacing="15">
		<? print_r($_SESSION['checkList']);
		
		?>
		<?php $i=0; if($checkListData[0]['check_list_items_name'] != ''){ foreach($checkListData as $checklist){ $i++;?>
			<tr>
				<td width="335" style="color:#000000;"><?php echo $checklist['check_list_items_name'];?></td>
				<td width="67">
					<label class="label_check" for="checklist_<?=$i;?>_1" id="label_checklist_<?=$i;?>_1" style="color:#000;">Yes
						<input type="checkbox" name="check_list_yes[]" value="YES"  onClick="changed(1, <?=$i;?>, this.id);" id="checklist_<?=$i;?>_1" 
						<? if($_SESSION['checkList'][$checklist['check_list_items_id']] == 'YES'){ echo 'checked="checked"'; $valueFlag = true;} ?> />
					</label>
				</td>
				<td width="45">
					<label class="label_check" for="checklist_<?=$i;?>_2" style="color:#000;" id="label_checklist_<?=$i;?>_2">No
						<input type="checkbox" name="check_list_no[]" value="NO" onClick="changed(2, <?=$i;?>, this.id);" id="checklist_<?=$i;?>_2" 
						<? if($_SESSION['checkList'][$checklist['check_list_items_id']] == 'NO'){ echo 'checked="checked"'; $valueFlag = true;} ?> />
					</label>
				</td>
				<td width="43">
					<label class="label_check" for="checklist_<?=$i;?>_3" style="color:#000;"  id="label_checklist_<?=$i;?>_3" >NA
						<input type="checkbox" name="check_list_na[]"  value="NA" onClick="changed(3, <?=$i;?>, this.id);" id="checklist_<?=$i;?>_3" 
						<? if($_SESSION['checkList'][$checklist['check_list_items_id']] == 'NA'){ echo 'checked="checked"'; $valueFlag = true;}
						if(!$valueFlag){	echo 'checked="checked"';	} ?>  />
					</label>
					<input type="hidden" name="checkListItemId[]" id="checkListItemId" value="<?=$checklist['check_list_items_id'];?>"  />
				</td>
			</tr>
		<?php } ?>
			<tr>
				<td colspan="5" >&nbsp;</td>
			</tr>
			<tr>
				<td align="right">
					<input type="sub" name="inspection_check" onclick="checklistAddSubmit(<?=$projectID?>);" style="background-color:transparent; background-image:url(images/save_btn.png); font-size:0px; border:none; height:34px; width:68px;cursor:pointer;" />
				</td>
				<td align="center" colspan="3">
					<img src="images/cancel_btn.png" onclick="closePopup(300);" />
				</td>
			</tr>
		<?php }else{?>
			<tr>
				<td colspan="2" align="left" style="color:#000000;">Record not found !</td>
			</tr>
		<?php } ?>
		</table>
	</form>
	</div>
	<?php }else if($checklistType == 'edit'){
		$insp_cheklist =$obj->selQRYMultiple('c.check_list_items_id, c.check_list_items_name, i.insepection_check_list_id, i.check_list_items_status', 'check_list_items as c, inspection_check_list as i', 'i.project_id = '.$projectID.' and inspection_id = '.$inspectionID.' and c.check_list_items_id = i.check_list_items_id and c.is_deleted = 0 and i.is_deleted = 0 AND c.check_list_items_option = "Quality Control" GROUP BY i.insepection_check_list_id'); ?>
	<div style="max-height:370px;overflow-y:scroll;">
		<form name="checklistValues" id="checklistValues">
		<table width="100%" border="0">
			<tr><td colspan="5" align="center" style="color:#000000;"><h3>Check List on Inspection</h3></td></tr>
			<tr><td colspan="5" align="center" style="color:#000000;">&nbsp;</td></tr>
			<?php $i=0; if($insp_cheklist[0]['check_list_items_name'] != ''){ foreach($insp_cheklist as $checklist){ $i++; ?>
				<tr>
					<td width="335" style="color:#000000;"><?php echo $checklist['check_list_items_name'];?></td>
					<td width="67">
						<label class="label_check<?php if($checklist['check_list_items_status']=='YES'){ echo ' c_on';}?>" for="checklist_<?=$i;?>_1" id="label_checklist_<?=$i;?>_1" style="color:#000;">Yes
							<input type="checkbox" name="check_list_yes[]" value="YES"  onClick="changed(1, <?=$i;?>, this.id);" id="checklist_<?=$i;?>_1" <?php if($checklist['check_list_items_status']=='YES'){?> checked="checked"<?php }?> />
						</label>
					</td>
					<td width="45">
						<label class="label_check<?php if($checklist['check_list_items_status']=='NO'){ echo ' c_on';}?>" for="checklist_<?=$i;?>_2" style="color:#000;" id="label_checklist_<?=$i;?>_2">No
							<input type="checkbox" name="check_list_no[]" value="NO" onClick="changed(2, <?=$i;?>, this.id);" id="checklist_<?=$i;?>_2" <?php if($checklist['check_list_items_status']=='NO'){?> checked="checked"<?php }?> />
						</label>
					</td>
					<td width="43">
						<label class="label_check<?php if($checklist['check_list_items_status']=='NA'){ echo ' c_on';}?>" for="checklist_<?=$i;?>_3" style="color:#000;"  id="label_checklist_<?=$i;?>_3" >NA
							<input type="checkbox" name="check_list_na[]"  value="NA" onClick="changed(3, <?=$i;?>, this.id);" id="checklist_<?=$i;?>_3" <?php if($checklist['check_list_items_status']=='NA'){?> checked="checked"<?php }?> />
						</label>
						<input type="hidden" name="checkListId[]" id="checkListId" value="<?=$checklist['insepection_check_list_id'];?>"  />
						<input type="hidden" name="checkListItemId[]" id="checkListItemId" value="<?=$checklist['check_list_items_id'];?>"  />
					</td>
				</tr>
			<?php $valueFlag = false;}?>
				<tr><td colspan="5" >&nbsp;</td></tr>
				<tr>
					<td align="right">
						<input type="button" name="inspection_check" onclick="checklistSubmit(<?=$projectID?>, <?=$inspectionID?>);" style="background-color:transparent; background-image:url(images/save_btn.png); font-size:0px; border:none; height:34px; width:68px;cursor:pointer;" />
					</td>
					<td align="center" colspan="3">
						<img src="images/cancel_btn.png" onclick="closePopup(300);" />
					</td>
				</tr>
			<?php }else{?>
				<tr><td colspan="2" align="left" style="color:#000000;">No Data Found !</td></tr>
			<?php }?>
		</table>
		</form>	
	</div>
	<?php } 
}else{
	echo 'Wrong URL !';
}?>
	