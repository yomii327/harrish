<?php include_once("commanfunction.php");
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);
include('func.php');
if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){?>
<script language="javascript" type="text/javascript">
	window.location.href = "<?=HOME_SCREEN?>";
</script>
<?php }
$builder_id = $_SESSION['ww_builder_id'];
function normalise($string) {
	$string = str_replace("\r", "\n", $string);
	return $string;	
}
if(isset($_FILES['csvFile']['tmp_name'])){ // Location/ subloaction import CSV file.
	if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name'])){
		$filename = $_FILES['csvFile']['name']; // Csv File name
		$ext = end(explode('.', $filename));
		if($ext == 'csv' || $ext == 'CSV'){
			$files = $_FILES['csvFile']['tmp_name'];		
			$fieldseparator = ","; // CSV file comma format
			$lineseparator = "\n";
			$csvfile = $files; //CSV file name
			$addauto = 1;
			$save = 1;
			/********************************/
			$file = fopen($csvfile,"r");
			$size = filesize($csvfile); //check file record
			
			if(!$size) {
				echo "File is empty.\n";
				exit;
			}
			$lines = 0;
			$queries = "";
			$linearray = array();
			$fieldarray= array();
			$record='';
			while( ($data = fgetcsv($file,1000,",")) != FALSE) {
				$numOfCols = count($data);
				for ($index = 0; $index < $numOfCols; $index++){
					$data[$index] = trim(stripslashes(normalise($data[$index])));
				}
				$fieldarray[] = $data;
			}
			fclose($file);
			$num = count($fieldarray);
			$count = 0;
//Find Special Character in CSV dated : 04/10/2012
			$err_msg = '';
			$legalCharArray = array('0', '10', '13', '32', '34', '38', '39', '40', '41', '44', '45', '46', '47', '63', '60', '62', '58', '124', '125', '123', '61', '43', '95', '42', '94', '37', '36', '35', '33', '126', '96', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '59',  '64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80','81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93', '97', '98', '99', '100', '101', '102', '103', '104', '105', '106', '107', '108', '109', '110', '111', '112', '113', '114', '115', '116', '117', '118', '119', '120', '121', '122');
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
			$collist = $fieldarray[0];
			for ($i=2; $i<count($collist); $i++){
				if($collist[$i] == "Issue To"){
					break;
				}
			}
			$posIssueTo = $i;
			$num_tags = $i-1;
			$exData = $obj->selQRYMultiple('standard_defect_id, description', 'standard_defects', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');
			$issueData = $obj->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');
			$descData = array();
			foreach($exData as $existsData){
				$descData[] = $existsData['description'];
			}
			$issueToData = array();
			foreach($issueData as $isData){
				$issueToData[] = $isData['issue_to_name'];
			}
			if($err_msg != ''){ }else{
				for($i=1; $i<$num; $i++) {//read second line beacuse first line cover headings
					$rowArray = array();
					if(!empty($fieldarray[$i][0])){
						if(in_array($fieldarray[$i][0], $descData)){
							$record = count($fieldarray[$i][1]);//keep Duplicate Record list.
							if($record > 0){
								$count = $count+1;
								continue;
							}
						}else{
							$issueTo = $fieldarray[$i][1];
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
							$desc = $fieldarray[$i][0];
							for($j=2; $j<=$num_tags; $j++){
								if($tags == ''){
									$tags = $fieldarray[$i][$j];
								}else{
									$tags .= ';'.$fieldarray[$i][$j];
								}
							}
							$tags = trim($tags, ";");
							$tag = implode(";", array_map('trim', explode(";", $tag)));
							if($tags != ""){
								$tags = trim($tags) . ";";
							}//Create Array for Bulk Inserttion
							if(trim($issueTo) == ''){
								$fixedDays = 0;
							}else{
								$fixedDays = 3;
							}
							$rowArray = array($_SESSION['idp'], addslashes(trim($desc)), addslashes(trim($tags)), $builder_id, "Now()", $builder_id, "Now()", addslashes(trim($issueTo)), $fixedDays);
						}
						$insertArray[] = $rowArray;
					}
				}
				$insertArray = array_filter($insertArray);
				$insertArray = array_values($insertArray);
				$inserted = $obj->bulkInsert($insertArray, 'project_id, description, tag, created_by, created_date, last_modified_by, last_modified_date, issued_to, fix_by_days', 'standard_defects');				
				if($inserted){
					$success = 'Total '.$lines.' record(s) inserted.<br />Total '.$count.' Duplicate Records';
				}else{
					if(isset($count) && !empty($count)){
						$success = "Total ".$count." Duplicate Records";
					}
				}
			}
		}else{
			$err_msg = 'Please select .csv file.';
		}
	}else{
		$err_msg = 'Please select file.';
	}
} ?>
<!-- Ajax Post -->
<!-- <link href="style.css" rel="stylesheet" type="text/css" /> -->
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
.box1 { background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
.link1 { background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
a.link1:hover { background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
.txt13 { border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }
</style>
<div id="middle" style="padding-top:10px;">
	<div id="leftNav" style="width:250px;float:left;">
		<?php include 'side_menu.php';?>
	</div>
	<?php $id=base64_encode($_SESSION['idp']); $hb=base64_encode($_SESSION['hb']); ?>
	<div id="rightCont" style="float:left;width:700px;">
		<div class="content_hd1" style="width:500px;margin-top:12px;">
			<font style="float:left;" size="+1">
				Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?>
			</font>
			<!-- <a style="float:left;margin-top:-25px;margin-left:8px; width:87px;margin-left:590px;" href="?sect=add_project_detail&id=<?php //echo $id;?>&hb=<?php //echo $hb;?>">
				<img src="images/back_btn2.png" style="border:none;" />
			</a> -->
			<a style="float:left;margin-top:-25px;margin-left:590px;" href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>" class="green_small">Back</a>
		</div>
		<br clear="all" />
		<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top:-15px;margin-top:0px\9;">
			<?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { ?>
				<div class="success_r" style="height:35px;width:185px;">
					<p><?php echo $_SESSION['add_project'] ; ?></p>
				</div>
			<?php unset($_SESSION['add_project']);} ?>
			<?php if((isset($success)) && (!empty($success))) { ?>
				<div class="success_r" style="height:45px;width:185px;">
					<p><?php echo $success; ?></p>
				</div>
			<?php }
			if((isset($err_msg)) && (!empty($err_msg))) { ?>
				<div class="failure_r" style="height:50px;width:520px;">
					<p><?php echo $err_msg; ?></p>
				</div>
			<?php } ?>
		</div>
		<div class="content_container" style="float:left;width:690px;border:1px solid; margin-bottom:10px;text-align:center;margin-left:10px;margin-right:10px;height:90px;">
			<div style="width:722px; height:50px; float:left; margin-top:5px;">
				<form method="post" name="csvIssueto" id="csvIssueto" enctype="multipart/form-data" onSubmit="return validateSubmit()">
					<table width="690px" border="0" cellspacing="0" cellpadding="3">
						<tr>
							<td colspan="3" align="left">
								<a href="/csv/Statndard_Defects_Template.csv" style="text-decoration:none;color:#FFF;"><strong style="font-size:16px;">Click <u>here</u> to download CSV template</strong></a>
							</td>
							<td>
								<!-- <input type="button"  class="submit_btn" onclick=location.href="standard_export.php"  style="background:none;background-image:url(images/export_csv_btn.png); width:87px; height:30px;border:none;margin-left:10px;" /> -->
								<input type="button" class="green_small" onclick=location.href="standard_export.php"  style="cursor:pointer;margin-left:15px;" value="Export CSV" />
							</td>
						</tr>
						<tr>
							<td width="185px;" align="left">&nbsp;</td>
							<td width="130px;">Upload&nbsp;CSV&nbsp;File&nbsp;:</td>
							<td width="240px;" align="left">
								<input type="file" name="csvFile" id="csvFile" value="" />
							</td>
							<td width="120px;" height="50px">
								<!-- <input type="button" style="background: url('images/import_csv_btn.png') repeat scroll 0 0 transparent;border: medium none;height: 30px;margin-left:10px;width: 87px;color:transparent;font-size:0px;"  name="location_csv" id="location_csv" value="Import CSV" onclick="validateSubmit();" /> -->
								<input type="submit" class="green_small" style="cursor:pointer;margin-left:15px;" name="location_csv" id="location_csv" value="Import CSV" onclick="validateSubmit();"/>
							</td>
						</tr>
					</table>
				</form>
				<br clear="all" />
			</div>
		</div>
		<div class="big_container" style="width:722px;float:left;margin-left:10px;" >
			<?php include'standard_defect_csv_table.php';?>
		</div>
	</div>
</div>
<script type="text/javascript">
function validateSubmit(){
	var r = jConfirm('Do you want to upload "Standard Defect CSV" ?', null, function(r){
		if (r === true){
			document.forms["csvIssueto"].submit();	
		}else{
			return false;
		}
	});
}
</script>