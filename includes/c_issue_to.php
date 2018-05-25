<?php
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);
include('func.php');
if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php }

$builder_id=$_SESSION['ww_is_company'];
function normalise($string) {
	$string = str_replace("\r", "\n", $string);
	return $string;	
}
if(isset($_REQUEST['id'])){
	$update = 'update master_issue_to set is_deleted=1,last_modified_date=now(),last_modified_by="'.$builder_id .'" where id="'.base64_decode($_REQUEST['id']).'"';
	mysql_query($update);
	$_SESSION['issue_to_del'] = 'Issued to deleted successfully.';

	header('loaction:?sect=c_issue_to');
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
								$select = "SELECT contact_id FROM master_issue_to_contact WHERE company_name = '".addslashes(trim($companyName))."' AND master_issue_id = '".$oldMasterIssueToId."' AND is_deleted = 0";
								$issue = mysql_query($select);
								$row_data = mysql_num_rows($issue);
								if($row_data > 0){
									$record=count($fieldarray[$i][1]);//keep Duplicate Record list.
									if($record>0)
										$count=$count+1;
								}else{
									mysql_query("INSERT INTO master_issue_to_contact SET master_issue_id = '".$oldMasterIssueToId."', ".$issue_to_query);
									$masterIssueToContactId = mysql_insert_id();	
									$success='File uploaded successfully.';		
								}
								# End:- Master Issue to Contact Section
							}else{
								mysql_query("INSERT INTO master_issue_to SET ".$issue_to_query);
								$masterIssueToId = mysql_insert_id();
								
								mysql_query("INSERT INTO master_issue_to_contact SET is_default = 1, master_issue_id = '".$masterIssueToId."', ".$issue_to_query);
								$masterIssueToContactId = mysql_insert_id();
								$success='File uploaded successfully.';
							}
							# End:- Master Issue to Section
																								
						}
					}
					@mysql_close($con); //close db connection
					if(isset($count) && !empty($count)){
						$success = "Total $count Duplicate Records";
					}
				}
		}else{
			$err_msg= 'Please select .csv file.';
		}
	}else{
		$err_msg= 'Please select file.';
	}
}

// Delete issue to
if(isset($_REQUEST['issueToId'])){
	$update="UPDATE master_issue_to SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE id = '".$_REQUEST['issueToId']."'";
	mysql_query($update);
	
    $update="UPDATE master_issue_to_contact SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE master_issue_id = '".$_REQUEST['issueToId']."'";
	mysql_query($update);
	
    $update="UPDATE inspection_issue_to SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE master_issue_id = '".$_REQUEST['issueToId']."'";
	mysql_query($update);	
	
	$_SESSION['issue_to_delete'] = 'Issued to deleted successfully.';
	
?>
<script language="javascript" type="text/javascript">
window.location.href="?sect=c_issue_to";
</script>
<?php	
}
?>
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
.box1 { background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
.link1 { background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
a.link1:hover { background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
.txt13 { border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }
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
<div id="container">
	<div class="content_hd1" style="background-image:url(images/issued_header.png);margin:10px 0 10px -10px;"></div>
	<div id="errorHolder" style="margin-left: 10px;margin-top:-15px;margin-top:0px\9;">
	<?php if(isset($_SESSION['issue_edit'])) { ?>
		<div class="success_r" style="width:250px;">
			<p><?php echo $_SESSION['issue_edit'];?></p>
		</div>
	<?php unset($_SESSION['issue_edit']); } ?>
    
	<?php if(isset($_SESSION['issue_add'])) { ?>
		<div class="success_r" style="width:250px;">
			<p><?php echo $_SESSION['issue_add'];?></p>
		</div>
	<?php unset($_SESSION['issue_add']); } ?>
    
	<?php if(isset($_SESSION['issue_to_delete'])) { ?>
		<div class="success_r" style="width:250px;">
			<p><?php echo $_SESSION['issue_to_delete'];?></p>
		</div>
	<?php unset($_SESSION['issue_to_delete']); } ?>
	<?php if((isset($success)) && (!empty($success))) { ?>
		<div class="success_r" style="height:35px;width:185px;"><p><?php echo $success; ?></p></div>
	<?php }
	if((isset($err_msg)) && (!empty($err_msg))) { ?>
		<div class="failure_r" style="height:50px;width:520px;"><p><?php echo $err_msg; ?></p></div>
	<?php } ?>
	</div>
  	<div class="content_container" style="float:left;width:970px;border:1px solid;text-align:center; margin:10px;height:90px;">
<!--First Box-->
		<div style="height:50px;;">
			<form method="post" name="csvIssueto" id="csvIssueto" enctype="multipart/form-data" onSubmit="return validateSubmit()">
				<table border="0" cellspacing="0" cellpadding="3" width="100%">
					<tr>
						<td colspan="3" align="left"><a href="/csv/Issued_To_Template.csv" style="text-decoration:none;color:#FFF;"><strong style="font-size:16px;">Click <u>here</u> to download CSV template</strong></a></td>
						<td>
							<input type="button"  class="submit_btn" onclick=location.href="c_issue_export.php"  style="background:none;background-image:url(images/export_csv_btn.png); width:87px; height:30px;border:none;margin-left:15px;" /></td>
						</td>
					</tr>   
					<tr>
						<td width="185px;" align="left">&nbsp;</td>
						<td width="130px;">Upload&nbsp;CSV&nbsp;File&nbsp;:</td>
						<td width="240px;" align="left"><input type="file" name="csvFile" id="csvFile" value="" /></td>
						<td width="120px;" height="50px"><input type="button" style="background: url('images/import_csv_btn.png') repeat scroll 0 0 transparent;border: medium none;height: 30px;margin-left:15px;width: 87px;color:transparent; font-size:0px;"  name="location_csv" id="location_csv" value="Import CSV" onClick="validateSubmit();" />
					   </td>
					</tr>
				</table>
			</form>
			<br clear="all" />
		</div>
	</div>
	<div class="big_container" style="margin-left:10px;" >
    <a href="#" onClick="addNewIssueTo();"><div style=" float:left; background:url('images/add_new_issue_to.png') !important; width:140px; height:24px; margin-bottom:2px; margin-top:0px !important;" class="add_new"></div></a>
	<?php include'issueto_csv_table.php';?></div>
</div>
<script type="text/javascript">
function validateSubmit(){
	var r = jConfirm('Do you want to upload "Issue To CSV" ?', null, function(r){if (r === true){document.forms["csvIssueto"].submit();	}else{return false;}});
}

// Issue To section 

function addNewIssueTo(){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_issue_to_by_ajax.php?&name='+Math.random(), loadingImage);
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}


function addNewIssueToData(){
	if($('#company_name').val().trim() == ''){$('#errorCompanyName').show('slow');return false;}else{$('#errorCompanyName').hide('slow');}
	if($('#contact_name').val().trim() == ''){$('#errorContactName').show('slow');return false;}else{$('#errorContactName').hide('slow');}

	if($('#emailid').val().trim() == '')
	{
		$('#errorEmailId').show('slow');
		return false;
	}else{
		$('#errorEmailId').hide('slow');
        if (validateEmail($('#emailid').val().trim())) {
            $('#errorEmailIdValid').hide('slow');
        }else{
            $('#errorEmailIdValid').show('slow');
            return false;

        } 
		
	}

	showProgress();
	$.post('add_issue_to_by_ajax.php?antiqueID='+Math.random(), $('#addIssueToForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			RefreshTable();
			closePopup(300);
//			window.location.href="?sect=c_issue_to";
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

function editIssueToData(issueToId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_issue_to_by_ajax.php?issueToId='+issueToId+'&name='+Math.random(), loadingImage);
}

function updateIssueToData(issueToId){
	var isDefault = $("#isDefault").val();
	if($('#company_name').val().trim() == ''){$('#errorCompanyName').show('slow');return false;}else{$('#errorCompanyName').hide('slow');}
	showProgress();
	$.post('edit_issue_to_by_ajax.php?antiqueID='+Math.random(), $('#editCompanyForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			RefreshTable();
			closePopup(300);
		}else{
			jAlert('Data updation failed, try again later');
		}
	});
}

function deleteIssueTo(issueToId){
	var r = jConfirm('Do you want to delete this issue to?', null, function(r){ if(r==true){ window.location = '?sect=c_issue_to&issueToId='+issueToId; } });
}

// Issue To Contact Section
function showIssueTo(issueToId){
	modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'show_issue_to_list_by_ajax.php?issueToId='+issueToId, loadingImage, function() {loadData(issueToId); });
}

function loadData(issueToId){
	$('#issueToData').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "show_issue_to_list_table_by_ajax.php?issueToId="+issueToId,
		"bStateSave": true,
		"bFilter": false,
	});
}

function addNewIssueToContact(issueToId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_issue_to_contact_by_ajax.php?issueToId='+issueToId+'&name='+Math.random(), loadingImage);
}

function addNewIssueToContactData(issueToId){
	if($('#contact_name').val().trim() == ''){$('#errorContactName').show('slow');return false;}else{$('#errorContactName').hide('slow');}

	if($('#emailid').val().trim() == '')
	{
		$('#errorEmailId').show('slow');
		return false;
	}else{
		$('#errorEmailId').hide('slow');
        if (validateEmail($('#emailid').val().trim())) {
            $('#errorEmailIdValid').hide('slow');
        }else{
            $('#errorEmailIdValid').show('slow');
            return false;

        } 
		
	}


	showProgress();
	$.post('add_issue_to_contact_by_ajax.php?antiqueID='+Math.random(), $('#addContactForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.error){
			jAlert(jsonResult.msg);
		}else if(jsonResult.status){
			showIssueTo(issueToId);
			//closePopup(300);
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

function editIssueToContact(contactId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_issue_to_contact_by_ajax.php?contactId='+contactId+'&name='+Math.random(), loadingImage);
}

function updateIssueToContactData(issueToId){
	var isDefault = $("#isDefault").val();
	if($('#contact_name').val().trim() == ''){$('#errorContactName').show('slow');return false;}else{$('#errorContactName').hide('slow');}

	if($('#emailid').val().trim() == '')
	{
		$('#errorEmailId').show('slow');
		return false;
	}else{
		$('#errorEmailId').hide('slow');
        if (validateEmail($('#emailid').val().trim())) {
            $('#errorEmailIdValid').hide('slow');
        }else{
            $('#errorEmailIdValid').show('slow');
            return false;

        } 
		
	}


	showProgress();
	$.post('edit_issue_to_contact_by_ajax.php?antiqueID='+Math.random(), $('#editContactForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			if(isDefault==1){
				//RefreshTable();
			}
			showIssueTo(issueToId);
		//	closePopup(300);
		}else{
			jAlert('Data updation failed, try again later');
		}
		//RefreshTable();
	});
}

function deleteIssueToContact(issueToId, contId){
	var r = jConfirm('Do you want to delete this record?', null, function(r){ if(r==true){ 
		modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'show_issue_to_list_by_ajax.php?issueToId='+issueToId+'&contId='+contId, loadingImage, function(){showIssueTo(issueToId);});
	}});
}

function RefreshTable(){
	$.getJSON("issue_to_data_table.php?", null, function( json ){
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

</script>