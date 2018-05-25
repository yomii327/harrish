<!-- < 		? php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?> -->
<?php if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){ ?>
<script language="javascript" type="text/javascript">
	window.location.href="<?=HOME_SCREEN?>";
</script>
<?php }
include'data-table.php';

include_once("commanfunction.php");
$obj = new COMMAN_Class(); 
$builder_id = $_SESSION['ww_builder_id'];
if(isset($_REQUEST['id'])){
	$id=$_REQUEST['id'];
	$_SESSION['project_id']=$id;
}else
	$id = ''; 
	
$issueData = $obj->selQRYMultiple('issue_to_name, company_name', 'inspection_issue_to', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0 ORDER BY issue_to_name');
$issueToData = array();
foreach($issueData as $isData){
	if($isData['issue_to_name'] != ''){
		if($isData['company_name'] != ''){
			$issueToData[] = $isData['issue_to_name']." (".$isData['company_name'].")";
		}else{
			$issueToData[] = $isData['issue_to_name'];
		}
	}
}
?>
<script type="text/javascript" src="selectivizr-min.js"></script>
<script>
var checklistArray = new Array();
function removeElement(parentDiv, childDiv){
	if (childDiv == parentDiv) {
		alert("The parent div cannot be removed.");
	}else if(document.getElementById(childDiv)) {
		$.alerts.okButton = '&nbsp;Yes&nbsp;';
		$.alerts.cancelButton = '&nbsp;No&nbsp;';
		jConfirm('Do you want to delete Checklist Item ?', 'Delete Confirmation',function(result){
			if(result){
				var child = document.getElementById(childDiv);
				var parent = document.getElementById(parentDiv);
				parent.removeChild(child);
			}else{
				return false;
			}
		});
	}
}
var items=0;
function AddItem() {
	div=document.getElementById("items");
	button=document.getElementById("add");
	items++;
	newitem="<div id=\"newContainer\"><span id=\"newEle1\" class=\"newEle\"><input type=\"text\" name=\"checklist[]\" id=\"checklist[]\" onblur=\"checklistId(this, this.value);\" class=\"input_small\" style=\"margin:0px 6px 4px 5px; width:110px;background-image:url(images/input_120.png);\"></span><span id=\"newEle2\" class=\"newEle\"><select name=\"issueTo[]\" id=\"issueto"+items+"\" class=\"select_box\" style=\"margin:0px 6px 4px 8px; width:120px;background-image:url(images/input_120.png);\"  onchange=\"showHideTextBox(this, 'otherIssueTo"+items+"');\"><option value=\"\">Select</option><?php for($i=0; $i<sizeof($issueToData); $i++){?><option value=\"<?php echo htmlentities($issueToData[$i]);?>\"><?php echo htmlentities($issueToData[$i]);?></option><?php }?><option value=\"otherIssue\">other</option></select><span id=\"otherIssueTo"+items+"\" style=\"display:none;\" ><input name=\"otherIssueTo[]\" type=\"text\" class=\"input_small\" style=\"margin:0px 0px 4px -2px; width:110px;background-image:url(images/input_120.png);\"><img src=\"images/redCross.png\" id=\"issueTo2\" onclick=\"closeThis('issueto"+items+"', 'otherIssueTo"+items+"');\" /></span></span><span id=\"newEle3\" class=\"newEle\"><select name=\"checklistType[]\" id=\"checklistType\" class=\"select_box\" style=\"margin:0px 6px 4px -8px; width:120px;background-image:url(images/input_120.png);\"><option value=\"Defect\">Defect</option><option value=\"Incomplete Works\">Incomplete Works</option></select></span><span id=\"newEle4\" class=\"newEle\"><select name=\"holdePoint[]\" id=\"holdePoint\" class=\"select_box\" style=\"margin:0px 6px 4px -8px; width:100px;background-image:url(images/input_100.png);\"><option value=\"Yes\">Yes</option><option value=\"No\" selected=\"selected\">No</option></select></span><span id=\"newEle5\" class=\"newEle\"><input type=\"text\" name=\"tag[]\" id=\"tag[]\" class=\"input_small\" style=\"margin:0px 6px 4px -6px; width:110px;background-image:url(images/input_120.png);\"></span><span id=\"newEle6\" class=\"newEle\"><a href=\"javascript:\" id=\"delete\" onclick=\"removeElement('items','New_"+items+"');\"><img src=\"images/inspectin_delete.png\"></a></span></div>";
	newnode=document.createElement("span");
	newnode.setAttribute('id','New_'+items);
	newnode.innerHTML=newitem;
	div.insertBefore(newnode,button);
}


</script>
<?php 	
$err_msg='';
if(!isset($_SESSION['no_refresh'])){
	$_SESSION['no_refresh'] = "";
}
if(isset($_REQUEST['id'])){
	$update = 'UPDATE check_list_items SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = '.$_SESSION['ww_builder_id'].' WHERE check_list_items_id = "'.base64_decode($_REQUEST['id']).'"';
	mysql_query($update);

	$updateData = 'UPDATE inspection_check_list SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = '.$_SESSION['ww_builder_id'].' WHERE check_list_items_id = "'.base64_decode($_REQUEST['id']).'" AND project_id = "'.$_SESSION['idp'].'"';
	mysql_query($updateData);

	$selfoInspection = $obj->selQRYMultiple('insepection_check_list_id, inspection_id, check_list_items_status, is_deleted', 'inspection_check_list', 'project_id = "'.$_SESSION['idp'].'" ORDER BY check_list_items_status');
	if(!empty($selfoInspection)){
		$arrayNA = array();
		$insp4update = '';
		foreach($selfoInspection as $infections){
			if($infections['check_list_items_status'] == 'NA' && $infections['is_deleted'] == 0){
				$arrayNA[$infections['inspection_id']] = 1;	
			}else if ($infections['is_deleted'] == 0){
				if(array_key_exists($infections['inspection_id'], $arrayNA)){
				}else{
					if($insp4update == ''){
						$insp4update = $infections['inspection_id'];
					}else{
						$insp4update .= ', '.$infections['inspection_id'];
					}
				}
			}
		}
	}
	if($insp4update != ''){
		$updateQRY = "UPDATE issued_to_for_inspections SET
							inspection_status = 'Open',
							last_modified_date = NOW()
						WHERE
							inspection_id IN (".$insp4update.") AND
							project_id = '".$_SESSION['idp']."' AND
							inspection_status = 'Draft'";
		mysql_query($updateQRY);
	}
	$_SESSION['checklist_del']='Checklist items deleted successfully.';
	header('loaction:?sect=checklist');
}

if(isset($_POST['save'])){
	if($_POST['no_refresh'] == $_SESSION['no_refresh']){}else{
		$checkListExist = '';$dCount = 0;
		for($i=0; $i<sizeof($_POST['checklist']); $i++){
			if($_POST['checklist'][$i]!=''){
				$checkListExist = $obj->selQRYMultiple('check_list_items_name', 'check_list_items', 'check_list_items_name = "'.$_POST['checklist'][$i].'" AND project_id = '.$_SESSION['idp'].' AND check_list_items_option = "Quality Control" AND is_deleted = "0"');
				if($checkListExist[0]['check_list_items_name'] == ''){
					$issueTo = '';
					$check_list_items_name = trim(addslashes($_POST['checklist'][$i]));
					$check_list_items_tags = trim(addslashes($_POST['tag'][$i]));
					$check_list_items_tags = trim($check_list_items_tags, ";");
					$check_list_items_tags = implode(";", array_map('trim', explode(";", $check_list_items_tags)));
					if($check_list_items_tags != ""){
						$check_list_items_tags = $check_list_items_tags . ";";
					}
					$checklist_type = trim(addslashes($_POST['checklistType'][$i]));
					$holding_point = trim(addslashes($_POST['holdePoint'][$i]));
					if($_POST['otherIssueTo'][$i] != ''){
						$issueTo = $_POST['otherIssueTo'][$i];	
						$issueData = $obj->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');
						$issueToData = array();
						foreach($issueData as $isData){
							if($isData['issue_to_name'] != ''){
								$issueToData[] = $isData['issue_to_name'];
							}
						} 
						if(!in_array($issueTo, $issueToData)){
							$issueTo_insert = "INSERT INTO inspection_issue_to SET
												issue_to_name = '".addslashes(trim($issueTo))."',
												last_modified_date = NOW(),
												last_modified_by = '".$builder_id."',
												created_date = NOW(),
												created_by = '".$builder_id."',
												project_id = '".$_SESSION['idp']."'";
							mysql_query($issueTo_insert);
						}
					}else if(isset($_POST['issueTo'][$i]) && !empty($_POST['issueTo'][$i]) && $_POST['issueTo'][$i] != 'otherIssue'){
						$issueTo = addslashes(trim($_POST['issueTo'][$i]));	
					}
					if(trim($issueTo) == ''){
						$fixedDays = 0;
					}else{
						$fixedDays = 3;
					}
					$inssertChecklist="INSERT INTO check_list_items SET
									project_id = '".$_SESSION['idp']."',
									check_list_items_name = '".$check_list_items_name."',
									check_list_items_tags = '".$check_list_items_tags."',
									issued_to = '".addslashes(trim($issueTo))."',
									fix_by_days = ".$fixedDays.",
									checklist_type	 = '".$checklist_type."',
									holding_point = '".$holding_point."',
									created_by = '".$builder_id."',
									last_modified_by = '".$builder_id."',
									last_modified_date = NOW(),
									created_date = NOW()";
					mysql_query($inssertChecklist);			
				}else{
					$dCount++;
				}
			}else{
				$err_msg = 'Checklist item can not be empty!';
			}
		}
		if($err_msg == ''){
			$success = 'Checklist items inserted successfully !';
		}
		if($dCount != ''){
			#$success .= '<br />'.$dCount.' Duplicate Records';
		}
		$_SESSION['no_refresh'] = $_POST['no_refresh'];
	}
}

if(isset($_FILES['csvFile']['tmp_name'])){
	if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name'])){
		$filename = $_FILES['csvFile']['name']; // Csv File name
		$ext = end(explode('.',$filename));
		if($ext == 'csv' || $ext == 'CSV'){
			$files = $_FILES['csvFile']['tmp_name'];		
			$fieldseparator = ","; // CSV file comma format
			$lineseparator = "\n";
			$csvfile = $files; //CSV file name
			$addauto = 1;
			$save = 1;
			$file = fopen($csvfile,"r");
			$size = filesize($csvfile); //check file record
			if(!$size){
				echo "File is empty.\n";
				exit;
			}
			$lines = 0;
			$queries = "";
			$linearray = array();
			$fieldarray= array();
			$record = '';
			while(($data = fgetcsv($file, 1000, ",")) != FALSE){
			      $numOfCols = count($data);
			      for ($index = 0; $index < $numOfCols; $index++){
					  $data[$index] = trim(stripslashes($data[$index]));
			      }
			      $fieldarray[] = $data;
			}
			fclose($file);
#print_r($fieldarray);die;
			$num=count($fieldarray);
			$count=0;
			$err_msg = '';
			$legalCharArray = array('0', '10', '13', '32', '34', '38', '39', '40', '41', '44', '45', '46', '47', '63', '60', '62', '58', '124', '125', '123', '61', '43', '95', '42', '94', '37', '36', '35', '33', '126', '96', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '59',  '64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80','81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93', '97', '98', '99', '100', '101', '102', '103', '104', '105', '106', '107', '108', '109', '110', '111', '112', '113', '114', '115', '116', '117', '118', '119', '120', '121', '122', '127');
			for($g=1; $g<$num; $g++){
				$subCount = count($fieldarray[$g]);
				for($m=0;$m<$subCount; $m++){
					$string = $fieldarray[$g][$m];
					$strArray = str_split($string);
					$subSubCount  = count($strArray);
					for($b=0;$b<$subSubCount;$b++){
						$asciiVal = ord($strArray[$b]);
						if(!in_array($asciiVal, $legalCharArray)){
							$err_msg = 'CSV file contains UNICODE characters, please remove them and try again.';
						}
					}
				}
			}
			
			if(!empty($lineNoArray)){
				$err_msg = "Line no's ".join(', ', array_unique($lineNoArray))." contains some UNICODE characters. Please correct the CSV file and try again.";
			}
			$collist = $fieldarray[0];
			for ($i=2; $i<count($collist); $i++){
				if($collist[$i] == "Issue To"){
					break;
				}
			}
			$posIssueTo = $i;
			$num_tags = $i-1;
			$exData = $obj->selQRYMultiple('check_list_items_id, check_list_items_name', 'check_list_items', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');
			
			$issueData = $obj->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');
			$ceckData = array();
			foreach($exData as $existsData){
				$ceckData[] = $existsData['check_list_items_name'];
			}
			$issueToData = array();
			foreach($issueData as $isData){
				$issueToData[] = $isData['issue_to_name'];
			}
			if($err_msg != ''){ }else{
				for($i=1;$i<$num;$i++){ //read second line beacuse first line cover headings
					$rowArray = array();
					if(!empty($fieldarray[$i][0])){
						if(in_array($fieldarray[$i][0], $ceckData)){
							$record = count($fieldarray[$i][1]);//keep Duplicate Record list.
							if($record > 0){
								$count = $count+1;
							}	
						}else{
							$issueTo = $fieldarray[$i][1];
							$checklistType = $fieldarray[$i][2];
							$holdingPoint = $fieldarray[$i][3];
							if(!in_array(trim($issueTo), $issueToData) && $issueTo !=''){
								$inserQRY = "INSERT INTO inspection_issue_to SET 
												issue_to_name = '".addslashes(trim($issueTo))."',
												project_id = '".$_SESSION['idp']."',
												created_by = '".$builder_id."',
												created_date = NOW(),
												last_modified_by = '".$builder_id."',
												last_modified_date = NOW()";
								mysql_query($inserQRY);
								$issueToData[] = addslashes(trim($issueTo));
							}
							$lines++;
							$tags ='';
							$checklistName = $fieldarray[$i][0];
							for($j=4; $j<=$num_tags; $j++){
								if($tags == ''){
									$tags = $fieldarray[$i][$j];
								}else{
									$tags .= ';'.$fieldarray[$i][$j];
								}
							}
							$tags = trim($tags, ";");
							if($tags != ""){
								$tags = $tags . ";";
							}//Create Array for Bulk Inserttion
							if(trim($issueTo) == ''){
								$fixedDays = 0;
							}else{
								$fixedDays = 3;
							}
							$rowArray = array($_SESSION['idp'], addslashes(trim($checklistName)), addslashes(trim($tags)), $builder_id, "Now()", $builder_id, "Now()", addslashes(trim($issueTo)), $fixedDays, addslashes(trim($checklistType)), addslashes(trim($holdingPoint)));
						}
						$insertArray[] = $rowArray;
					}
				}

				$insertArray = array_filter($insertArray);        

				$insertArray = array_values($insertArray);        
				
				$inserted = $obj->bulkInsert($insertArray, 'project_id, check_list_items_name, check_list_items_tags, created_by, created_date, last_modified_by, last_modified_date, issued_to, fix_by_days, checklist_type, holding_point', 'check_list_items');				
				if($inserted){
					$success = 'Total '.$lines.' record(s) inserted.<br />Total '.$count.' Duplicate Records';
				}else{
					if(isset($count) && !empty($count)){
						$success = "Total ".$count." Duplicate Records";
					}
				}
			}
		}else{
			$err_msg= 'Please select .csv file.';
		}
	}else{
		$err_msg= 'Please select file.';
	}
}

?>
<div id="middle" style="padding-top:10px;">
	<div id="leftNav" style="width:250px;float:left;">
		<?php include 'side_menu.php';?>
	</div>
	<?php $id=base64_encode($_SESSION['idp']); $hb=base64_encode($_SESSION['hb']);  ?>
	<div id="rightCont" style="float:left;width:700px;">
		<div class="content_hd1" style="width:500px;margin-top:12px;">
			<font style="float:left;" size="+1">
				Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?>
			</font><br />
			<a href="?sect=add_project_detail&id=<?=$id;?>&hb=<?=$hb;?>" style="display: block;float: none;height: 35px;margin-left: 577px;margin-top: -25px;width: 87px;"> <img src="images/back_btn2.png" /></a>
		</div>
		<br clear="all" />
		<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top: -15px;margin-top: 0px\9;">
			<?php if((isset($success )) && (!empty($success ))) {
			if($success != ''){?>
				<div class="success_r" style="height:35px;width:300px;">
					<p><?=$success;?></p>
				</div>
			<?php }
			unset($success ); }
			if($err_msg != '') { ?>
				<div class="failure_r" style="height:35px;width:525px;">
					<p><?php echo $err_msg; ?></p>
				</div>
			<?php 	} ?>
		</div>
		<div class="big_container" style="width:722px;float:left;margin-top:-50px;" >
			<div style="border:1px solid #ffffff; margin:45px 20px 10px 10px;text-align:center;">
				<form action="?sect=checklist" id="csvChecklist" name="csvChecklist"  method="post" style="margin-top:10px;" enctype="multipart/form-data">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" >
						<tr>
							<td colspan="2" align="left">
								<a href="/csv/Checklist_Template.csv" style="text-decoration:none;color:#FFF;">
									<strong style="font-size:16px;">Click <u>here</u> to download CSV template</strong>
								</a>
							</td>
							<td>
								<input type="button"  class="submit_btn" onclick=location.href="checklist_export.php"  style="background:none;background-image:url(images/export_csv_btn.png); width:87px; height:30px;border:none;margin-left:15px;" />
							</td>
						</tr>
						<tr>
							<td style="color:white;" >Upload&nbsp;CSV&nbsp;File&nbsp;:</td>
							<td style="color:white;" align="left">
								<input type="file" name="csvFile" id="csvFile" value="" />
							</td>
							<td height="50px">
								<input type="button" style="background: url('images/import_csv_btn.png') repeat scroll 0 0 transparent;border: medium none;height: 30px;margin-left:15px;width: 87px;color:transparent; font-size:0px;"  name="location_csv" id="location_csv" value="Import CSV" onclick="validateSubmit();" />
							</td>
						</tr>
						<tr>
							<td colspan="4" style="color:white;">OR</td>
						</tr>
					</table>
				</form>
				<form action="?sect=checklist" id="addchecklist" name="addchecklist"  method="post" style="margin-top:10px;" >
					<table width="100%" border="0" cellspacing="0" cellpadding="0" >
						<tr>
							<td colspan="3" align="left" style="padding-bottom:15px;"></td>
						</tr>
						<tr>
							<td width="28%" valign="top" colspan="6">
							<div>
								<div class="checkListHeader" style="width: 130px;padding-left: 20px;">Checklist Name <span class="req">*</span></div>
								<div class="checkListHeader" style="width: 130px;">Issue To</div>
								<div class="checkListHeader" style="width: 130px;">Checklist Type</div>
								<div class="checkListHeader" style="width: 130px;">Hold Point</div>
								<div class="checkListHeader" style="width: 130px;">Location Tags<br /><em>Please seperate location by semicolon(;)</em></div>
							</div>
							</td>
						</tr>
						<tr>
							<td width="28%" valign="top" colspan="6">
							<div>
								<span id="newEle1" class="newEle">
									<input type="text" name="checklist[]" id="checklist[]" onblur="checklistId(this, this.value);" class="input_small" style="margin:0px 6px 4px 5px; width:110px;background-image:url(images/input_120.png);" />
								</span>
								<span id="newEle2" class="newEle">
									<select name="issueTo[]" id="issueTo" class="select_box" style="margin:0px 6px 4px 8px; width:120px;background-image:url(images/input_120.png);" onchange="showHideTextBox(this, 'otherIssueTo');">
										<option value="">Select</option>
										<?php for($i=0; $i<sizeof($issueToData); $i++){?>
											<option value="<?php echo htmlentities($issueToData[$i]);?>"><?php echo htmlentities($issueToData[$i]);?></option>	
										<?php }?>
										<option value="otherIssue">other</option>
									</select>
									<span id="otherIssueTo" style="display:none;" ><input name="otherIssueTo[]" type="text" class="input_small" id="issueTo1" style="width:120px;margin:0px 0px 4px -2px; width:110px;background-image:url(images/input_120.png);" /><img src="images/redCross.png" id="issueTo2" onclick="closeThis('issueTo', 'otherIssueTo');" /></span>
								</span>
								<span id="newEle3" class="newEle">
									<select name="checklistType[]" id="checklistType" class="select_box" style="margin:0px 6px 4px -8px; width:120px;background-image:url(images/input_120.png);">
										<option value="Defect">Defect</option>	
										<option value="Incomplete Works">Incomplete Works</option>
										<option value="quality">Quality</option>
									</select>
								</span>
								<span id="newEle4" class="newEle">
									<select name="holdePoint[]" id="holdePoint" class="select_box" style="margin:0px 6px 4px -8px; width:100px;background-image:url(images/input_100.png);">
										<option value="Yes">Yes</option>	
										<option value="No" selected="selected">No</option>	
									</select>	
								</span>
								<span id="newEle5" class="newEle">
									<input type="text" name="tag[]" id="tag[]" onblur="" class="input_small" style="margin:0px 6px 4px -5px; width:110px;background-image:url(images/input_120.png);" />
								</span>
								<span id="newEle6" class="newEle">
									<a href="javascript:" onclick="AddItem();"><img src="images/inspectin_add.png" /></a> 
								</span>
							</div>
							</td>
						</tr>
						<tr>
							<td colspan="6"><div id="items"></div></td>
						</tr>
						<tr>
							<td colspan="6"><input type="submit" class="save_btn" name="save" style="background-color:transparent; background-image:url(images/submit_btn.png); font-size:0px; border:none; height:29px; width:87px;margin:15px 0 15px 563px;"/>
								<input type="hidden" name="no_refresh" value="<?php echo uniqid(rand());?>" />
							</td>
						</tr>
					</table>
				</form>
			</div>
			<div class="big_container" style="width:722px;float:left;margin-left:10px;" >
				<?php include'checklist_item.php';?>
			</div>
			<div class="spacer"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
function validateSubmit(){
	var r = jConfirm('Do you want to upload "Checklist CSV" ?', null, function(r){
		if (r === true){
			document.forms["csvChecklist"].submit();	
		}else{
			return false;
		}
	});
}
function closeThis(id1, id2){
	document.getElementById(id1).style.display = 'block';
	document.getElementById(id2).style.display = 'none';
	document.getElementById(id1).value = '';
}
function showHideTextBox(obj, textID){
	if(obj.value == 'otherIssue'){
		obj.style.display = 'none';
		document.getElementById(textID).style.display = 'block';
	}
}
</script>
<style>
span.newEle{ float:left; }
span#newEle1{ margin-left:11px; }
span#newEle3{ margin-left:10px; -bracket-:hack(; margin-left:14px; ); }
span#newEle4{ margin-left:10px; -bracket-:hack(; margin-left:14px; ); }
span#newEle5{ margin-left:10px; -bracket-:hack(; margin-left:14px; ); }
span#newEle5 label{ color:#FFFFFF;}
span#newEle6{ margin:5px 0px; }
div.checkListHeader{float:left;color: #FFFFFF;text-align: left; }
</style>
