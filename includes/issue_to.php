<?php
include_once("commanfunction.php");
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);
 include('func.php');
//if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
 if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_c_id']) || $_SESSION['ww_c_id']!=1)){?>
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
 //$builder_id=$_SESSION['ww_builder_id'];
 $builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
 function normalise($string) {
	$string = str_replace("\r", "\n", $string);
	
	return $string;	
 }
 
if(isset($_FILES['csvFile']['tmp_name'])){ // Location/ subloaction import CSV file.
	if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name'])){
		$filename=$_FILES['csvFile']['name']; // Csv File name
		$file_ext=explode('.',$filename);
		$ext=$file_ext[1];
		if($ext=='csv' || $ext=='CSV'){
			$files=$_FILES['csvFile']['tmp_name'];		
			$fieldseparator = ","; // CSV file comma format
			$lineseparator = "\n";
			$csvfile = $files; //CSV file name
			$addauto = 1;
			$save = 1;
			$file = fopen($csvfile,"r");
			$size = filesize($csvfile); //check file record
			if(!$size) {
				echo "File is empty.\n";
				exit;
			}
			/*
			$err_msg = '';
			$topColumns = array("Contact Name","Company Name","Phone","Email","Tag 1","Tag 2","Tag 3","Tag 4");
			$columns = fgetcsv($file, 10000, ",");
			if($topColumns!='' && is_array($topColumns)){
				foreach($topColumns as $key=>$val){
					if(isset($columns[$key])){
						if($val!=$columns[$key]){
							$err_msg= 'Column name are not matched';
						}
					}else{
						$err_msg= 'Column name are not matched';
					}
				}
			}

			if(!$err_msg || $err_msg ==''){
			*/
				$lines = 0;
				$queries = "";
				$linearray = array();
				$fieldarray= array();
				$record='';
				while( ($data =  fgetcsv($file,1000,",")) != FALSE){
				      $numOfCols = count($data);
				      for ($index = 0; $index < $numOfCols; $index++){
						  $data[$index] = trim(stripslashes(normalise($data[$index])));
				      }
				      $fieldarray[] = $data;
				}
				fclose($file);
				$num=count($fieldarray);
				$count=0;
			
				$err_msg = '';
				$legalCharArray = array('0', '10', '13', '32', '34', '38', '39', '40', '41', '44', '45', '46', '47',
				'63', '60', '62', '58', '124', '125', '123', '61', '43', '95', '42', '94', '37', '36', '35', '33', '126', '96',
				'48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '59', 
				'64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80','81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93',
				'97', '98', '99', '100', '101', '102', '103', '104', '105', '106', '107', '108', '109', '110', '111', '112', '113', '114', '115', '116', '117', '118', '119', '120', '121', '122');
				for($g=1; $g<$num; $g++){
					$subCount = count($fieldarray[$g]);
					for($m=0;$m<$subCount; $m++){
						$string = $fieldarray[$g][$m];
						$strArray = str_split($string);
						$subSubCount  = count($strArray);
						for($b=0;$b<$subSubCount;$b++){
							$asciiVal = ord($strArray[$b]);
							if(!in_array($asciiVal, $legalCharArray)){
								$lineNoArray[] = $g+1;
							}
						}
					}
				}
				if(!empty($lineNoArray)){
					$err_msg = "Line no's ".join(', ', array_unique($lineNoArray))." contains some UNICODE characters. Please correct the CSV file and try again.";
				}
				if($err_msg != ''){ }else{
					for($i=1;$i<$num;$i++){ //read second line beacuse first line cover headings
						if(!empty($fieldarray[$i][1])){
							
							# Start:- Create query structure
							$name = $fieldarray[$i][1]; $fieldarray[$i][1]='';
							$companyName = !empty($fieldarray[$i][0])?$fieldarray[$i][0]:"NA"; $fieldarray[$i][0]='';
							$phone = $fieldarray[$i][2]; $fieldarray[$i][2]='';
							$email = $fieldarray[$i][3]; $fieldarray[$i][3]='';
							$trade = $fieldarray[$i][4]; $fieldarray[$i][4]='';
							
							$tags=implode(';',$fieldarray[$i]);
							$tags=trim($tags, ";");
							$drawTags = implode(";", array_map('trim', explode(";", $drawTags)));
							if ($tags != "")
								$tags = trim($tags) . ";";
							
							@$issue_to_query = "issue_to_name = '".addslashes(trim($name))."',
											company_name = '".addslashes(trim($companyName))."',
											issue_to_phone = '".addslashes(trim($phone))."',
											issue_to_email = '".addslashes(trim($email))."',
											tag = '".addslashes(trim($tags))."',
											trade = '".addslashes(trim($trade))."',
											last_modified_date = NOW(),
											last_modified_by = ".$builder_id.",
											created_date = NOW(),
											created_by = ".$builder_id;
							# End:- Create query structure		
							
							# Start:- Master Issue to Section
							$select = "SELECT id FROM master_issue_to WHERE issue_to_name = '".addslashes(trim($name))."' AND is_deleted=0";
							$issue = mysql_query($select);
							$row_data = mysql_num_rows($issue);
							if($row_data > 0){
								$row_data = mysql_fetch_row($issue);
								$oldMasterIssueToId = $row_data[0];
								# Start:- Master Issue to Contact Section
								$select = "SELECT * FROM master_issue_to_contact WHERE company_name = '".addslashes(trim($companyName))."' AND master_issue_id = '".$oldMasterIssueToId."' AND is_deleted = 0";
								$issue = mysql_query($select);
								$row_data = mysql_num_rows($issue);
								if($row_data > 0){

									$issueToData = mysql_fetch_row($issue);
									$oldMasterIssueToContactId = $issueToData[0];
									
									# Start:- Inspection Issue to Section
									$select = "SELECT issue_to_id FROM inspection_issue_to WHERE company_name = '".addslashes(trim($companyName))."' AND master_issue_id = '".$oldMasterIssueToId."' AND is_deleted = 0 AND project_id = '".$_SESSION['idp']."'";
									$issue = mysql_query($select);
									$row_data = mysql_num_rows($issue);
									if($row_data > 0){
										$record=count($fieldarray[$i][1]);//keep Duplicate Record list.
										if($record>0)
											$count=$count+1;
									}else{
										@$issue_to_query = "INSERT INTO inspection_issue_to SET
											master_contact_id = '".addslashes(trim($issueToData[0]))."',
											master_issue_id = '".addslashes(trim($issueToData[1]))."',
											project_id = '".$_SESSION['idp']."',											
											issue_to_name = '".addslashes(trim($issueToData[2]))."',
											company_name = '".addslashes(trim($issueToData[3]))."',
											issue_to_phone = '".addslashes(trim($issueToData[4]))."',
											issue_to_email = '".addslashes(trim($issueToData[5]))."',
											tag = '".addslashes(trim($issueToData[12]))."',
											trade = '".addslashes(trim($issueToData[14]))."',
											is_default = '".addslashes(trim($issueToData[15]))."',											
											last_modified_date = NOW(),
											last_modified_by = ".$builder_id.",
											created_date = NOW(),
											created_by = ".$builder_id;
											
										mysql_query($issue_to_query);
										$issueToId = mysql_insert_id();	
										
										$success='File uploaded successfully.';		
									}
									# End:- Inspection Issue to Section

								}else{
									mysql_query("INSERT INTO master_issue_to_contact SET master_issue_id = '".$oldMasterIssueToId."', ".$issue_to_query);
									$masterIssueToContactId = mysql_insert_id();	
									
									mysql_query("INSERT INTO inspection_issue_to SET project_id = '".$_SESSION['idp']."', master_issue_id = '".$oldMasterIssueToId."', master_contact_id = '".$masterIssueToContactId."', ".$issue_to_query);
									$issueToId = mysql_insert_id();

									$success='File uploaded successfully.';	
								}
								# End:- Master Issue to Contact Section
							}else{
								mysql_query("INSERT INTO master_issue_to SET ".$issue_to_query);
								$masterIssueToId = mysql_insert_id();
								
								mysql_query("INSERT INTO master_issue_to_contact SET is_default = 1, master_issue_id = '".$masterIssueToId."', ".$issue_to_query);
								$masterIssueToContactId = mysql_insert_id();
								
								mysql_query("INSERT INTO inspection_issue_to SET is_default = 1, project_id = '".$_SESSION['idp']."', master_issue_id = '".$masterIssueToId."', master_contact_id = '".$masterIssueToContactId."', ".$issue_to_query);
								$issueToId = mysql_insert_id();
								
								$success='File uploaded successfully.';
							}
							# End:- Master Issue to Section
																								
							/*
							$select = "SELECT id FROM master_issue_to WHERE issue_to_name = '".addslashes(trim($fieldarray[$i][1]))."' AND is_deleted = 0";
							$issue=mysql_query($select);
							$row_data=mysql_num_rows($issue);
							if($row_data > 0){
								$record=count($fieldarray[$i][1]);//keep Duplicate Record list.
								if($record>0)
									$count=$count+1;
							}else{

								mysql_query("INSERT INTO master_issue_to SET ".$issue_to_query);
								$masterIssueToId = mysql_insert_id();
								if(isset($companyName) && !empty($companyName)){
									mysql_query("INSERT INTO master_issue_to_contact SET is_default = 1, master_issue_id = '".$masterIssueToId."', ".$issue_to_query);
								}
								$success='File uploaded successfully.';
							}
							*/
						}
					}
					@mysql_close($con); //close db connection
					if(isset($count) && !empty($count)){
						$success = "Total $count Duplicate Records";
					}
				}
			//}
		}else{
			$err_msg= 'Please select .csv file.';
		}
	}else{
		$err_msg= 'Please select file.';
	}
}

if(isset($_POST['assignSubmit'])){
	$_POST['assignIssueTo'] = (isset($_POST['assignIssueTo']) && !empty($_POST['assignIssueTo']))?$_POST['assignIssueTo']:array(0);
	
    $update="UPDATE inspection_issue_to SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE master_issue_id !=0 AND master_contact_id NOT IN(".implode(',',$_POST['assignIssueTo']).") AND project_id='".$_SESSION['idp']."'";
	mysql_query($update);
/*    $update="UPDATE inspection_issue_to_contact SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE master_issue_id NOT IN(".implode(',',$_POST['assignIssueTo']).") AND project_id='".$_SESSION['idp']."'";
	mysql_query($update);
*/	
	$projCurtIssueToData = $obj->selQRYMultiple('GROUP_CONCAT(master_contact_id) as ids', "inspection_issue_to", " is_deleted = '0' AND project_id='".$_SESSION['idp']."'");
	$oldIds = (isset($projCurtIssueToData[0]['ids']) && !empty($projCurtIssueToData[0]['ids']))?" AND contact_id NOT IN(".$projCurtIssueToData[0]['ids'].")":"";
	$issueToData = $obj->selQRYMultiple('*', "master_issue_to_contact", " is_deleted = '0' AND contact_id IN(".implode(',',$_POST['assignIssueTo']).") ".$oldIds." ");	

	foreach($issueToData as $issueTo){ 
		$issue_insert = "INSERT INTO inspection_issue_to SET
			project_id = '".$_SESSION['idp']."',
			master_issue_id = '".trim($issueTo['master_issue_id'])."',		
			master_contact_id = '".trim($issueTo['contact_id'])."',			
			issue_to_name = '".trim($issueTo['issue_to_name'])."',
			company_name = '".trim($issueTo['company_name'])."',
			issue_to_phone = '".trim($issueTo['issue_to_phone'])."',
			issue_to_email = '".trim($issueTo['issue_to_email'])."',
			tag = '".trim($issueTo['tag'])."',
			trade = '".trim($issueTo['trade'])."',
			activity = '".trim($issueTo['activity'])."',
			last_modified_date = NOW(),
			last_modified_by = ".$builder_id.",
			created_date = NOW(),
			created_by = ".$builder_id;
		mysql_query($issue_insert);
		$issueToId = mysql_insert_id();
		
	/*	$issue_contact_insert = "INSERT INTO issue_to_contact SET
			project_id = '".$_SESSION['idp']."',
			issue_to_id = '".trim($issueToId)."',		
			master_issue_id = '".trim($issueTo['id'])."',	
			issue_to_name = '".trim($issueTo['issue_to_name'])."',
			company_name = '".trim($issueTo['company_name'])."',
			issue_to_phone = '".trim($issueTo['issue_to_phone'])."',
			issue_to_email = '".trim($issueTo['issue_to_email'])."',
			tag = '".trim($issueTo['tag'])."',
			activity = '".trim($issueTo['activity'])."',
			is_default= '1',
			last_modified_date = NOW(),
			last_modified_by = ".$builder_id.",
			created_date = NOW(),
			created_by = ".$builder_id;
		mysql_query($issue_contact_insert);*/
	}	
	$sucMsg = 1; $_POST['assignIssueTo']='';

}

if(isset($_REQUEST['id'])){

	$update='update inspection_issue_to set is_deleted=1,last_modified_date=now(),last_modified_by="'.$builder_id .'" where issue_to_id="'.base64_decode($_REQUEST['id']).'"';

	mysql_query($update);
	$_SESSION['issue_to_del']='Issued to deleted successfully.';
	header('loaction:?sect=issue_to');
	
}

// Delete issue to
if(isset($_REQUEST['issueToId'])){
	$update="UPDATE inspection_issue_to SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE issue_to_id = '".$_REQUEST['issueToId']."' AND project_id='".$_SESSION['idp']."'";
	mysql_query($update);
	
    //$update="UPDATE issue_to_contact SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."' WHERE issue_to_id = '".$_REQUEST['issueToId']."' AND project_id='".$_SESSION['idp']."'";
	//mysql_query($update);
	$_SESSION['issue_to_del']='Issued to deleted successfully.';
	header('loaction:?sect=issue_to');
	
?>
<script language="javascript" type="text/javascript">
window.location.href="?sect=issue_to";
</script>
<?php	}?>
<link type="text/css" href="js/multiselect/css/ui.multiselect.css" rel="stylesheet" />
<link type="text/css" href="js/multiselect/css/multiselect.css" rel="stylesheet" />
<style>
.box1 {
    background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent;
    border: 1px solid #0261A1;
    color: #000000;
    float: left;
    height: auto;
    width: 211px;
}
.link1 {
    background-image: url("images/blue_arrow.png");
    background-position: 175px 34%;
    background-repeat: no-repeat;
    color: #000000;
    display: block;
    height: 25px;
    text-decoration: none;
    width: 202px;
}
a.link1:hover {
    background-color: #015F9F;
    background-image: url("images/white_arrow.png");
    background-position: 175px 34%;
    background-repeat: no-repeat;
    color: #FFFFFF;
    display: block;
    height: 25px;
    text-decoration: none;
    width: 202px;
}
.txt13 {
    border-bottom: 1px solid #333333;
    color: #000000;
    font-size: 12px;
    font-weight: bold;
    height: 30px;
}
.demo_jui td{ text-align:left; }
#example_server td img{ cursor:pointer;}
</style>

<script type="text/javascript">
var deadlock = true;//File upload
var params = "";
var align = 'center';
var top1 = 100;
var width = 800;
var padding = 10;
var backgroundColor = '#FFFFFF';
var borderColor = '#333333';
var borderWeight = 4;
var borderRadius = 5;
var fadeOutTime = 300;
var disableColor = '#666666';
var disableOpacity = 40;
var loadingImage = 'images/loadingAnimation.gif';
var copyStatus = false;
var copyId = '';
</script>
<div id="middle" style="padding-top:10px;">
<div id="leftNav" style="width:250px;float:left;">
<?php include 'side_menu.php';?>
<?php $id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']);
 ?>
</div>
<div id="rightCont" style="float:left;width:700px;">
	<div class="content_hd1" style="width:500px;margin-top:12px;">
		<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font>
		<?php /*<a style="float:left;margin-top:-25px;width:87px;margin-left:590px;" href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>">
			<img src="images/back_btn2.png" style="border:none;" />
		</a>*/ ?>
		<a style="float:left;margin-top:-25px;margin-left:590px;" href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>" class="green_small">Back</a>
	</div><br clear="all" />
	<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top:-15px;margin-top:0px\9;">
		<?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { ?>
			<div class="success_r" style="height:35px;width:185px;"><p><?php echo $_SESSION['add_project'] ; ?></p></div>
		<?php unset($_SESSION['add_project']);} ?><?php if((isset($success)) && (!empty($success))) { ?>
			<div class="success_r" style="height:35px;width:670px;"><p><?php echo $success; ?></p></div>
		<?php }
			if((isset($err_msg)) && (!empty($err_msg))) { ?>
			<div class="failure_r" style="height:50px;width:520px;"><p><?php echo $err_msg; ?></p></div>
		<?php } ?>
	</div>
  	<div class="content_container" style="float:left;width:690px;text-align:center;margin-left:10px;margin-right:10px;">
    <div style="height:90px;;border:1px solid;text-align:center; width: 709px !important;">
			<form method="post" name="csvIssueto" id="csvIssueto" enctype="multipart/form-data" onSubmit="return checkImport()">
				<table border="0" cellspacing="0" cellpadding="3" width="100%">
					<tr>
						<td colspan="3" align="left"><a href="/csv/Issued_To_Template.csv" style="text-decoration:none;color:#FFF;"><strong style="font-size:16px;">Click <u>here</u> to download CSV template</strong></a></td>
						<td>
							<!-- <input type="button"  class="submit_btn" onclick=location.href="issue_export.php"  style="background:none;background-image:url(images/export_csv_btn.png); width:87px; height:30px;border:none;margin-left:15px;" /> -->
							<input type="button" class="green_small" onclick=location.href="issue_export.php"  style="cursor:pointer;margin-left:15px;" value="Export CSV" />
							</td>
						</td>
					</tr>   
					<tr>
						<td width="185px;" align="left">&nbsp;</td>
						<td width="130px;">Upload&nbsp;CSV&nbsp;File&nbsp;:</td>
						<td width="240px;" align="left"><input type="file" name="csvFile" id="csvFile" value="" /></td>
						<td width="120px;" height="50px">
						<!-- <input type="submit" style="background: url('images/import_csv_btn.png') repeat scroll 0 0 transparent;border: medium none;height: 30px;margin-left:15px;width: 87px;color:transparent; font-size:0px;"  name="location_csv" id="location_csv" value="Import CSV" /> -->
						<input type="submit" class="green_small" style="cursor:pointer;margin-left:15px;" name="location_csv" id="location_csv" value="Import CSV" />
					   </td>
					</tr>
				</table>
			</form>
			<br clear="all" />
		</div>
    <br clear="all" />
<!--First Box-->
<?php include'data-table.php';?>
<div style="width:722px; float:left; margin-top:25px; ">
<?php if((isset($sucMsg)) && $sucMsg==1) { $sucMsg==0;
	echo '	<div class="success_r" style="height:35px;width:300px;"><p>Action performed successfully!</p></div>	';	
  }
  if((isset($_SESSION['issue_to_del'])) && !empty($_SESSION['issue_to_del'])) { unset($_SESSION['issue_to_del']);
	echo '	<div class="success_r" style="height:35px;width:300px;"><p>Record deleted successfully!</p></div>	';	
  } ?>
	<form method="post" name="issueToForm" id="issueToForm" action="">
		<table border="0" cellspacing="0" cellpadding="3">
			<tr>
				<td colspan="2" align="left">
					<!-- Start Multiselect section -->
					<?php $issueToProjData = $obj->selQRYMultiple('master_contact_id, issue_to_name, company_name', 'inspection_issue_to', " project_id = '".$_SESSION['idp']."' and is_deleted = '0' and issue_to_name != '' ORDER BY company_name, issue_to_name");?>
					<!-- Start Multiselect section -->
					<div class="multi-select-issuedto">
						<select id="assignIssueTo" multiple="multiple" name="assignIssueTo[]" style="display:none">
							<option value="">select</option>
							<?php if(isset($issueToProjData)){
								foreach($issueToProjData as $issueTo){
									if($issueTo['company_name'] != '') {
										echo '<option value="'.$issueTo['master_contact_id'].'" selected="selected">'.$issueTo['issue_to_name']." (".$issueTo['company_name'].')</option>';
									} else {
										echo '<option value="'.$issueTo['master_contact_id'].'" selected="selected">'.$issueTo['issue_to_name'].'</option>';
									}
								}
							}
							?>
						</select>
						<div class="section-left available">
							<span class="heading"><input type="text" onkeyup="searchIssuedTo()" id="issuedtoFilter" class="iFilter" /></span>
							<div id="issuedto-unselectd">
								<ul class="list available ui-droppable"></ul>
							</div>
						</div> <!-- /.section-left -->
						<div class="section-right selected">
							<span class="heading"><em><?php echo (isset($issueToProjData))?count($issueToProjData):0; ?></em> items selected</span>
							<div id="issuedto-selectd">
								<ul class="list available ui-droppable">
									<?php if(isset($issueToProjData)){
										foreach($issueToProjData as $issueTo){
											if($issueTo['company_name'] != '') { ?>
												<li class="ui-state-default ui-element ui-draggable" data-index="<?=$issueTo['master_contact_id']?>">
													<span class="ui-helper-hidden"></span>
													<a id="uiRemoveElement_<?=$issueTo['master_contact_id']?>" onClick="removeElement(this.id)" href="javascript:void(0);" class="title ui-remove-element"><?=$issueTo['company_name']." (".$issueTo['issue_to_name'].')'?></a>
													<a href="javascript:void(0);" class="ui-state-default ui-remove-element"><span class="ui-corner-all ui-icon ui-icon-minus"></span></a>
												</li>
											<?php } else { ?>
												<li class="ui-state-default ui-element ui-draggable" data-index="<?=$issueTo['master_contact_id']?>">
													<span class="ui-helper-hidden"></span>
													<a id="uiRemoveElement_<?=$issueTo['master_contact_id']?>" onClick="removeElement(this.id)" href="javascript:void(0);" class="title ui-remove-element"><?=$issueTo['issue_to_name']?></a>
													<a href="javascript:void(0);" class="ui-state-default ui-remove-element"><span class="ui-corner-all ui-icon ui-icon-minus"></span></a>
												</li>
									<?php
											}
										}
									} ?>
								</ul>
							</div>
						</div> <!-- /.section-right -->
						<div style="clear:both"></div>
						<div id="loader" style="display:none"></div>
					</div>
					<!-- /.multi-select-issuedto -->
                <!-- Start Multiselect section -->
                <?php /* $issueToProjData = $obj->selQRYMultiple('master_contact_id, issue_to_name, company_name', 'inspection_issue_to', " project_id = '".$_SESSION['idp']."' and is_deleted = '0' and issue_to_name != ''");
				
					$projIssueToArr = array();
					$selIssuedTo = array();
					foreach($issueToProjData as $issueTo){
						$selIssuedTo[] = $issueTo['master_contact_id']; 
						$projIssueToArr[$issueTo['master_contact_id']] = $issueTo['issue_to_name'];
					}
					
					$issueToData = $obj->selQRYMultiple('contact_id, issue_to_name, company_name', 'master_issue_to_contact', " is_deleted = '0' and issue_to_name != '' "); ?>
                <select id="assignIssueTo" class="multiselect" multiple="multiple" name="assignIssueTo[]">
                <?php foreach($issueToData as $issueTo){
						if(isset($projIssueToArr[$issueTo['contact_id']])){
							if($issueTo['company_name'] != '')
								echo '<option value="'.$issueTo['contact_id'].'" selected="selected">'.$issueTo['issue_to_name']." (".$issueTo['company_name'].')</option>';
							else
								echo '<option value="'.$issueTo['contact_id'].'" selected="selected">'.$issueTo['issue_to_name'].'</option>';
						}else{
							if($issueTo['company_name'] != '')
								echo '<option value="'.$issueTo['contact_id'].'">'.$issueTo['issue_to_name']." (".$issueTo['company_name'].')</option>';
							else
								echo '<option value="'.$issueTo['contact_id'].'">'.$issueTo['issue_to_name'].'</option>';
						}
					}
				?>
				</select>
                <!-- End Multiselect section -->*/ ?>
                </td>
			</tr>   
			<tr>
				<td colspan="2">
				<!-- <input type="submit" style="background-image:url(images/submit_btn.png); border:none; width:111px; float:right;  height: 29px;" value="" id="assignSubmit" class="submit_btn" name="assignSubmit"> -->
				<input type="submit" style="float:right;" value="Submit" id="assignSubmit" class="green_small" name="assignSubmit">
               </td>
			</tr>
		</table>
	</form>
<!--br clear="all" /-->
<!-- Issue to table section -->
<div class="big_container" style="width:722px; margin-top:0px;" >
<!-- <a href="#" onClick="addNewIssueTo();"><div style=" float:left; background:url('images/add_new_issue_to.png') !important; width:140px; height:24px; margin-bottom:2px; margin-top:0px !important;" class="add_new"></div></a> -->
<a href="#" onClick="addNewIssueTo();" style="float:left; margin-bottom:2px; margin-top:0px !important;" class="green_small">Add New Issue To</a>
<a href="#" class="green_small" style="float:left;margin-bottom:2px;margin-left:10px; display: none;" onclick=location.href="show_project_issue_to_contact_list_download.php">Contact List</a>
<?php //include'project_issueto_table.php';?>
	<div class="demo_jui" style="width:99%;" >
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="example_server" width="100%">
			<thead>
				<tr>
					<th width="60%" nowrap="nowrap">Company Name</th>
					<!--th width="32%">Contact Name</th>
					<th>Phone</th>
					<th>Email</th>
					<th>Tags</th-->
					<!--th width="30%">Trade</th-->
					<th width="15%">Action</th>
				</tr>
			</thead>
			<tbody>
			<tr>
				<td colspan="5" class="dataTables_empty">Loading data from server</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="spacer"></div>
</div>

<!-- Issue to table section -->
</div>
		</div>


</div>

<link type="text/css" href="js/multiselect/css/ui.multiselect.css" rel="stylesheet" />
<style>
.multiselect {width: 710px;	height: 200px;  }
.ui-multiselect div.list-container {
    border: 0 none;
    float: right !important;
    margin: 0;
    padding: 0;
}
.available, .selected{
	width:354px !important;
}
.ui-widget-header input{
	width:150px !important;
}

.add-all, .remove-all {
    background: none repeat scroll 0 0 #2070A5;
    border: 1px outset #2070A5;
    display: none !important;
    margin: 2px !important;
    padding: 5px !important;
}
</style>
<script type="text/javascript" src="js/multiselect/js/jquery-ui-1.8.custom.min.js"></script>
<!--script type="text/javascript" src="js/multiselect/js/plugins/tmpl/jquery.tmpl.1.1.1.js"></script>
<script type="text/javascript" src="js/multiselect/js/ui.multiselect.js"></script-->
<script type="text/javascript" src="js/multiselect/js/multiselect.js?time=<?php echo time();?>"></script>
<script type="text/javascript">
$(document).ready(function(){
//	$(function(){
		//$("#assignIssueTo").multiselect({ droppable: 'none' });
//	});
});

// Data table section
$(document).ready(function() {
	$('#example_server').dataTable( {
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "project_issue_to_data_table.php",
		"iDisplayLength": 20,
		"bStateSave": true,
		"aoColumnDefs": [ {  "bSearchable": true, "bSortable": false, "aTargets": [ 1 ] }],
	} );
} );

// Issue to section 
function addNewIssueTo(){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_issue_to_by_ajax.php?&name='+Math.random(), loadingImage);
}

function addNewIssueToData(){
	if($('#company_name').val().trim() == ''){$('#errorCompanyName').show('slow');return false;}else{$('#errorCompanyName').hide('slow');}
	if($('#contact_name').val().trim() == ''){$('#errorContactName').show('slow');return false;}else{$('#errorContactName').hide('slow');}
	showProgress();
	$.post('add_issue_to_by_ajax.php?antiqueID='+Math.random(), $('#addIssueToForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			window.location.href="?sect=issue_to";
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

// Issue to contact section 
function showIssueTo(issueToId){
	modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'show_project_issue_to_list_by_ajax.php?issueToId='+issueToId, loadingImage, function() {loadData(issueToId); });
}

function loadData(issueToId){
	$('#projectIssueToData').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "show_project_issue_to_list_table_by_ajax.php?issueToId="+issueToId,
		"bStateSave": true,
		"bFilter": false,
	});
}

function addNewIssueToContact(issueToId){
//	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_issue_to_contact_by_ajax.php?issueToId='+issueToId+'&name='+Math.random(), loadingImage, function(){addNewIssueToContactData();});
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_project_issue_to_contact_by_ajax.php?issueToId='+issueToId+'&name='+Math.random(), loadingImage);

}

function addNewIssueToContactData(issueToId){
	if($('#contact_name').val().trim() == ''){$('#errorContactName').show('slow');return false;}else{$('#errorContactName').hide('slow');}
	showProgress();
	$.post('add_project_issue_to_contact_by_ajax.php?antiqueID='+Math.random(), $('#addContactForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			window.location.href="?sect=issue_to";
			showIssueTo(issueToId);
			//closePopup(300);
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

function editIssueToContact(contactId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_project_issue_to_contact_by_ajax.php?contactId='+contactId+'&name='+Math.random(), loadingImage);
}

function RefreshTable(){
	$.getJSON("project_issue_to_data_table.php?", null, function( json ){
		table = $('#example_server').dataTable();
		oSettings = table.fnSettings();
		table.fnClearTable(this);
		
		for (var i=0; i<json.aaData.length; i++){
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw();
	});
}

function updateIssueToContactData(issueToId){
	var isDefault = $("#isDefault").val();
	if($('#contact_name').val().trim() == ''){$('#errorContactName').show('slow');return false;}else{$('#errorContactName').hide('slow');}
	showProgress();
	$.post('edit_project_issue_to_contact_by_ajax.php?antiqueID='+Math.random(), $('#editContactForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			if(isDefault==1){
				RefreshTable();
			}
			showIssueTo(issueToId);
		//	closePopup(300);
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

function deleteIssueToNew(issueToId){
	var r = jConfirm('Do you want to remove this issue to?', null, function(r){ if(r==true){ window.location = '?sect=issue_to&issueToId='+issueToId; } });
}

function deleteIssueToContact(issueToId, contId){
	var r = jConfirm('Do you want to remove this record?', null, function(r){ if(r==true){ 
		modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'show_project_issue_to_list_by_ajax.php?issueToId='+issueToId+'&contId='+contId, loadingImage, function(){showIssueTo(issueToId);});
	}});
}


// Not use
function validateSubmit(){
	var r = jConfirm('Do you want to upload "Issue To CSV" ?', null, function(r){
		if (r === true){
			document.forms["csvIssueto"].submit();	
		}else{
			return false;
		}
	});
	return false;
}

function deletechecked(messagedeactive,linkdeactive){
	$.alerts.okButton = '&nbsp;Yes&nbsp;';
	$.alerts.cancelButton = '&nbsp;No&nbsp;';
	jConfirm(messagedeactive,'Delete Confirmation',function(result){
		if(result)
		{
			window.location = linkdeactive;
		}
	});
	$.alerts.okButton = '&nbsp;OK&nbsp;';
	$.alerts.cancelButton = '&nbsp;Cancel&nbsp;';
	return false;
}
/*
function getData(){
	var pro_id = document.getElementById('pro_name').value;
	document.getElementById('create_response').innerHTML='';
	
	// display loading image.
	document.getElementById('load_defects_type').style.display = 'block';
	document.getElementById('load_repairer_name').style.display = 'block';
	
	// get defects type for this project
	$("#defects_type_div").load('defects_type_response.php?pro_id='+pro_id);
	
	// get repairer for this project
	$("#repairer_name_div").load('repairer_name_response.php?pro_id='+pro_id);
	
	setTimeout("hideImg()",1000);
}*/

function checkImport(){
	val=document.getElementById("csvFile").value;
	if(val==""){
		alert("Please select file!");
		return false
	}else{
		
		n=val.search(/.csv/i);
		if(n<0){
			alert("Please provide only CSV file!");
			return false;
		}else{
			t=confirm('Do you want to upload "Issue To CSV" ?');
			if(t==true){
				return true;
			}else{
				return false;
			}
		}
	}
}
</script>
