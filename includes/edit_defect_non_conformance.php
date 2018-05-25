<?php set_time_limit(36000000000000);
session_start();
ob_start(); if(!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])){ ?>
<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<script type="text/javascript" src="selectivizr-min.js"></script>
<?php }
$_SESSION['task_id'] = $str_id = base64_decode($_GET['id']);

$df_id = ''; //non_conformance_id
$pid = base64_decode($_GET['pid']);

$sql_qa = "SELECT non_conformance_id, qa_inspection_date_raised FROM qa_inspections WHERE task_id = '".$str_id."' and is_deleted=0";
$qa_r = $obj->db_query($sql_qa);
if($qa_r!=''){
	if(mysql_num_rows($qa_r)>0){
		$qa_row = mysql_fetch_assoc($qa_r);
		if(!empty($qa_row)){
			$df_id = $qa_row['non_conformance_id'];
			$non_conformance_id = $qa_row['non_conformance_id'];
			$dateRaised = $qa_row['qa_inspection_date_raised'];
		}
	}
}
$owner_id = $_SESSION['ww_owner_id'];	
$msg ='';
include('commanfunction.php');
$object= new COMMAN_Class();

$qaTaskDataArr = $object->selQRYMultiple('location_id, sub_location_id, task, status, comments, parent_task_id', 'qa_task_monitoring', 'task_id = "'.$_SESSION['task_id'].'" and is_deleted = 0');


/*$qa_ncr_sql = "SELECT task_detail_id, task_id, raised_by, comment, created_by FROM  qa_ncr_task_detail WHERE task_id = '".$_SESSION['task_id']."' and task_detail_id = '".$_SESSION['task_detail_id']."' and is_deleted=0"; 
$result_task = $obj->db_query($qa_ncr_sql);
if($obj->db_num_rows($result_task) > 0){ $ncr_task_detail_row = $obj->db_fetch_assoc($result_task); }*/
#echo $_SESSION['task_detail_id'];
$getQaNcrAttachmentsData = $object->selQRYMultiple('a.ncr_attachment_id, a.task_detail_id, a.attachment_title, a.attachment_description, a.attachment_file_name, a.attachment_type', 'qa_ncr_attachments as a, qa_inspections as b', 'a.is_deleted=0 AND a.ncr_non_conformance_id = b.non_conformance_id AND b.task_id='.$_SESSION['task_id'].'');
$attachedData = array();
$imageAttachData = array();
#print_r($getQaNcrAttachmentsData);die;
if(!empty($getQaNcrAttachmentsData)){
	foreach($getQaNcrAttachmentsData as $atData){
		if($atData['attachment_title'] == 'iPad_image' || $atData['attachment_title'] == 'Web_image'){
			$imageAttachData[] = $atData;
		}else{
			$attachedData[] = $atData;
		}
	}
}

?>
<style>
#locationsContainer, #attachments{ overflow-y:scroll; max-height:250px; min-height:200px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius:5px; border:1px solid; margin-top:15px; width:595px; }
.innerDiv{ float:left; border:1px solid red; width:120px; height:120px; margin:0px 0px 20px 0px; }
label.filebutton{ width:120px; height:40px; overflow:hidden; position:relative; }
label input { cursor: pointer; font-size: 7px; left: -1px; line-height: 0; margin: 0; opacity: 0; padding: 0; position: absolute; top: -2px; z-index: 999; filter:alpha(opacity=0);}
#image1, #image2{ z-index:1;}
#locationsContainer{ overflow-y: scroll; max-height: 200px; min-height: 150px; border-radius:5px; -moz-border-radius: 5px;  -webkit-border-radius: 5px; border:1px solid; margin-top:15px; width:420px; }
table.gridtable { border-width: 1px; border-color: #FFF; border-collapse: collapse; }
table.gridtable td { border-width: 1px; padding: 8px; border-style: solid; border-color: #FFF; }
.clickableLines{ cursor:pointer; padding:5px; margin-right:5px;}
.clickableLines:hover{ background-color:#336FCE;padding:5px; margin-right:5px;color:#FFFFFF;}
#dropDown{ cursor:pointer; }
#discriptionHide{ display:none; height: 150px; overflow-y: scroll; position:absolute; background:#FFFFFF; border:1px solid #0BA4FF; width:300px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; z-index:1000; color:#000000; text-shadow:none; }
.issueTo{ border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; border:1px solid; width:150px; border-color:#FFFFFF; height:25px;}
ul.telefilms{list-style:none; cursor:pointer; font-size:15px;}
ul.telefilms li ul{list-style:none; line-height:30px;}
.jtree-arrow { padding-right: 5px; font-size: 15px; }
.fixedByDate{ background:#FFF; cursor:default; height:20px; -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; }
table#example_server{ color:#000000; }
#attachList{ height: 200px; overflow: auto; padding: 5px; }
#attachList, #innerDiv div.fileHolder{ background: none repeat scroll 0 0 #F3F3F3; border: 1px outset #333333; float: left; margin: 3px; padding: 5px; width: 166px; }
#attachList, #innerDiv span.fileHolder{ color: #990000; cursor: pointer; float: right; }

</style>
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript">
$(document).ready(function(){
		var validator = $("#edit_non_confirmance_inspection").validate({
		rules:{
			description:{
				required: true
			},
			raisedBy:{
				required: true
			}
		},
		messages:{
			description:{
				required: '<div class="error-edit-profile" style="margin-left:50px;">The description field is required</div>'
			},
			raisedBy:{
				required: '<div class="error-edit-profile">The raised by name field is required</div>'
			},
			
			debug:true
		}
	});
	
});
function removeElement(parentDiv, childDiv){
	if (childDiv == parentDiv) {
		alert("The parent div cannot be removed.");
	}else if(document.getElementById(childDiv)) {     
		var child = document.getElementById(childDiv);
		var parent = document.getElementById(parentDiv);
		parent.removeChild(child);
	}
}
var items=0;
function AddItem(count) {
	if(count < 3){
		div=document.getElementById("items");
		button=document.getElementById("add");
		items++;
		newitem="";
		newnode=document.createElement("span");
		newnode.setAttribute('id','New_'+items);
		newnode.innerHTML=newitem;
		div.insertBefore(newnode,button);
	}else{
		alert('You can\'t add more than three Sub Contractor one inspections !');
	}
}
var spinnerVisible = false;
function showProgress() {
	if (!spinnerVisible) {
		$("div#spinner").fadeIn("fast");
		spinnerVisible = true;
	}
};
function hideProgress() {
	if (spinnerVisible) {
		var spinner = $("div#spinner");
		spinner.stop();
		spinner.fadeOut("fast");
		spinnerVisible = false;
	}
};
function validateDelete(){
	var r = jConfirm('Do you want to delete Inspection ?', null, function(r){
		if (r==true){
			alert('Word');
		}else{
			return false;
		}
	});
}
</script>
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<style type="text/css" title="currentStyle">@import "datatable/examples_support/themes/smoothness/jquery-ui-1.8.17.custom.css";</style>
<script language="javascript">
window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"fixedByDate_1",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"fixedByDate_2",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"fixedByDate_3",
			dateFormat:"%d-%m-%Y"
		});
	};
</script>
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/location_tree_jquery.min.js"></script>
<link href="../style.css" rel="stylesheet" type="text/css" />
<?php
if(isset($_POST['button'])){
#echo "<pre>";print_r($_POST['button']);die;
$historyFlag = false;
$isUpdateMainRec = true;

	$recodArr = array();//Store record to create history table
	$description = $_POST['description'];
	$projectId = $_SESSION['projIdQA'];
	$raisedBy = $_POST['raisedBy'];
	$ncr = isset($_POST['ncr']) ? $_POST['ncr'] : '';
	$newNcrClosed = $ncrClosed = isset($_POST['ncrClosed']) ? $_POST['ncrClosed'] : 0;
	
	$updateQry = "UPDATE qa_inspections SET
					qa_inspection_description = '".addslashes($description)."',					  
					qa_inspection_raised_by = '".addslashes($raisedBy)."',
					last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
					last_modified_date = NOW(),
					original_modified_date = NOW(),
					ncr = '".$ncr."',
					ncr_closed = '".$ncrClosed."'
				WHERE
					non_conformance_id = '".$df_id."'";
	mysql_query($updateQry) or die(mysql_error());
	if(mysql_affected_rows() > 0){$historyFlag = true;}//Set Flag Value here
//Record Array to store data start here
		$recodArr['qa_inspection_description'] = $description;
		$recodArr['qa_inspection_raised_by'] = $raisedBy;
		$recodArr['ncr_closed'] = $ncrClosed;
		$recodArr['ncr'] = $ncr;
//Record Array to store data end here
	$updateImages = "UPDATE qa_graphics SET last_modified_date = NOW(), original_modified_date = NOW() WHERE non_conformance_id = '".$df_id."'";
	mysql_query($updateImages) or die(mysql_error());
	if(mysql_affected_rows() > 0){$historyFlag = true;}//Set Flag Value here
	// Image section
	#print_r($_SESSION['inspGraph']);print_r($_POST["photo"]); die;
	foreach($_SESSION['inspGraph'] as $val){
	#print_r($val);die;
		if($val['qa_graphic_type']=="images"){
			if(!in_array($val['graphic_name'], $_POST["photo"])){
				$updateImages = "UPDATE qa_graphics SET is_deleted='1', original_modified_date = NOW(), last_modified_date = NOW() WHERE non_conformance_id = '".$df_id."' "; 
				mysql_query($updateImages) or die(mysql_error());
				if(mysql_affected_rows() > 0){$historyFlag = true;}//Set Flag Value here
				@unlink('./inspections/photo/'.$val['qa_graphic_name']);
			}
		}
		if($val['qa_graphic_type']=="drawing"){
			if($val['qa_graphic_name']!=$_POST["drawing"] || empty($_POST["drawing"])){
				$updateImages = "UPDATE qa_graphics SET is_deleted='1', original_modified_date = NOW(), last_modified_date = NOW() WHERE non_conformance_id = '".$df_id."' ";
				mysql_query($updateImages) or die(mysql_error());
				if(mysql_affected_rows() > 0){$historyFlag = true;}//Set Flag Value here
				@unlink('./inspections/drawing/'.$val['qa_graphic_name']);
			}
		}
		
	}
	
	if(isset($_POST["photo"])){
	#echo "<pre>";print_r($_POST);die;
		for($i=0; $i<sizeof($_POST["photo"]); $i++){
			$photoName = mysql_real_escape_string(trim($_POST['photo'][$i]));
			$select="select * from qa_graphics where qa_graphic_name='".addslashes(trim($photoName))."' AND non_conformance_id='".$df_id."' AND project_id='".$projectId."' and is_deleted=0";
			$inspGraph=mysql_query($select);
			$row_data=mysql_num_rows($inspGraph);
			if($row_data > 0){
			}else{
				$insertImage = "INSERT INTO qa_graphics SET
								non_conformance_id = '".$df_id."',
								qa_graphic_type = 'images',
								qa_graphic_name = '".addslashes($photoName)."',
								created_date = NOW(),
								created_by = '".$_SESSION['ww_builder']['user_id']."',
								last_modified_date = NOW(),
								last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
								project_id = '".$projectId."'";
				mysql_query($insertImage) or die(mysql_error());
//Record Array to store data start here
		$recodArr['non_conformance_id'] = $df_id;
		$recodArr['image1'] = array('type'=>'image', 'fileName'=>$photoName); 
//Record Array to store data end here
			}
		}
	}
	if(isset($_POST['drawing']) && !empty($_POST['drawing'])){
		$drawingName = mysql_real_escape_string(trim($_POST['drawing']));											
		$select="select * from qa_graphics where qa_graphic_name='".addslashes(trim($drawingName))."' AND non_conformance_id='".$df_id."' AND project_id='".$projectId."' and is_deleted=0 and qa_graphic_type='drawing'";
		$inspGraph=mysql_query($select);
		$row_data=mysql_num_rows($inspGraph);
		if($row_data > 0){
		}else{
			$insertImageDrawing = "INSERT INTO qa_graphics SET
										non_conformance_id = '".$df_id."',
										qa_graphic_type = 'drawing',
										qa_graphic_name = '".addslashes($drawingName)."',
										created_date = NOW(),
										created_by = '".$_SESSION['ww_builder']['user_id']."',
										last_modified_date = NOW(),
										last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
										project_id = '".$projectId."'";
			mysql_query($insertImageDrawing) or die(mysql_error());
//Record Array to store data start here
		$recodArr['image2'] = array('type'=>'drawing', 'fileName'=>$drawingName); 
//Record Array to store data end here
		}
	}
	$attachments = $_POST['attachments'];
		$attachTitle = $_POST['attachTitle'];
		$attachDescription = $_POST['attachDescription'];
		$attachName = $_POST['attachName'];
		if(isset($df_id) && $df_id>0){	
			$attachedData = $object->selQRYMultiple('ncr_attachment_id, ncr_non_conformance_id, attachment_title, attachment_description, attachment_file_name, attachment_type', 'qa_ncr_attachments', 'ncr_non_conformance_id = "'.$df_id.'" and is_deleted=0');
			// File section
			foreach($attachedData as $val){
				if(!in_array($val['attachment_file_name'], $attachments)){
					$updateAttachment = "UPDATE qa_ncr_attachments SET
											is_deleted='1',
											project_id = '".$_SESSION['projIdQA']."',
											last_modified_date = NOW(),
											original_modified_date = NOW()
										WHERE ncr_attachment_id = '".$val['ncr_attachment_id']."'";
					mysql_query($updateAttachment) or die(mysql_error());
					if(mysql_affected_rows() > 0){$historyFlag = true;}//Set Flag Value here
					@unlink('./inspections/ncr_files/'.$val['attachment_file_name']);
				}
			}
		}		

		if(isset($attachments) && !empty($attachments)){
			$attTitleArr = array(); $attDesArr = array(); $attFileNameArr = array();
			foreach($attachments as $key=>$name){ //$name = trim($name);
				$name = trim($attachments[$key]);
				$attachedData = $object->selQRYMultiple('ncr_attachment_id', 'qa_ncr_attachments', 'attachment_file_name = "'.$name.'" and is_deleted = 0');
				if($attachedData[0]['ncr_attachment_id'] > 0){//for edit
				}else{
					$storVal = array();
				 	$qProject = "INSERT INTO qa_ncr_attachments SET 
										ncr_non_conformance_id = '$df_id',
										attachment_title = '$attachTitle[$key]',
										attachment_description = '$attachDescription[$key]',
										attachment_file_name = '$name',
										created_date = NOW(),
										project_id = '".$_SESSION['projIdQA']."',
										last_modified_date = NOW(),
										original_modified_date = now(),
										created_by = '$builder_id',
										last_modified_by = '$builder_id'";
					$obj->db_query($qProject);					
					$storVal = array('attachTitle'=>$attachTitle[$key], 'attachDesc'=>$attachDescription[$key], 'attachFileName'=>$name);
					$attArr[] = $storVal;
				}
			}
			//Record Array to store data start here
				$recodArr['attachment'] = $attArr;
			//Record Array to store data end here
		}
		unset($_SESSION['ncrAttachments']);
		$result = 0;
		sleep(1);
/*--------NCR add edit start here--------*/
	
	if($ncr != '') {
		// Start - Data prepare for insertion
		$ncrDate = (isset($_POST['ncrDate']) && !empty($_POST['ncrDate'])) ? $_POST['ncrDate'] : '';
		if($ncrDate != ''){
			$d = explode('-', $ncrDate);
			$ncrDate = $d[2]."-".$d[1]."-".$d[0];
		}else{
			$ncrDate = "NOW()";
		}
		$ncrDrawingText = mysql_real_escape_string(trim($_POST['ncrDrawingText']));

		$ncrLocation = mysql_real_escape_string(trim($_POST['ncrLocation']));

		$ncrType = $_POST['ncrType'];

		$ncrIdentifyingSource = $_POST['ncrIdentifyingSource'];

		$ncrIdentifyingContact = mysql_real_escape_string(trim($_POST['ncrIdentifyingContact']));

		$ncrNonConformanceDescription = mysql_real_escape_string(trim($_POST['ncrNonConformanceDescription']));

		$ncrIdentifiersViewOfNonConformance =  $_POST['ncrIdentifiersViewOfNonConformance'];	

		$ncrIdentifiersDescriptionOfRisk = mysql_real_escape_string(trim($_POST['ncrIdentifiersDescriptionOfRisk']));

		$ncrIdentifierRootCause = mysql_real_escape_string(trim($_POST['ncrIdentifierRootCause']));

		$ncrIdentifierSuggestedMeasure = mysql_real_escape_string(trim($_POST['ncrIdentifierSuggestedMeasure']));

		$ncrReceivedBy = mysql_real_escape_string(trim($_POST['ncrReceivedBy']));
	
		$ncrReceivedByDate = (isset($_POST['ncrReceivedByDate']) && !empty($_POST['ncrReceivedByDate'])) ? $_POST['ncrReceivedByDate'] : '00-00-0000';

		$d = explode('-', $ncrReceivedByDate);
		$ncrReceivedByDate = $d[2]."-".$d[1]."-".$d[0];
		
		$ncrRecipientsViewOfNonConformance =  isset($_POST['ncrRecipientsViewOfNonConformance'])?implode(", ", $_POST['ncrRecipientsViewOfNonConformance']):'';	

		$ncrRecipientDescriptionOfRisk = mysql_real_escape_string(trim($_POST['ncrRecipientDescriptionOfRisk']));

		$ncrRecipientRootCause = mysql_real_escape_string(trim($_POST['ncrRecipientRootCause']));

		$ncrRecipientSuggestedMeasure = mysql_real_escape_string(trim($_POST['ncrRecipientSuggestedMeasure']));

		$ncrCorrectionOfNonConformance =  $_POST['ncrCorrectionOfNonConformance'];	

		$ncrDetailedProcedure = mysql_real_escape_string(trim($_POST['ncrDetailedProcedure']));

		$ncrAuthorisedBy = mysql_real_escape_string(trim($_POST['ncrAuthorisedBy']));
	
		$ncrAuthorisedByDate = (isset($_POST['ncrAuthorisedByDate']) && !empty($_POST['ncrAuthorisedByDate'])) ? $_POST['ncrAuthorisedByDate'] : '00-00-0000';

		$d = explode('-', $ncrAuthorisedByDate);

		$ncrAuthorisedByDate = $d[2]."-".$d[1]."-".$d[0];

		$ncrResultOfReInspection =  isset($_POST['ncrResultOfReInspection']) ? implode(", ", $_POST['ncrResultOfReInspection']) : '';	
		// Data for 
		$ncrIssuedBy = mysql_real_escape_string(trim($_POST['ncrIssuedBy']));

		$ncrIssuedByDate = (isset($_POST['ncrIssuedByDate']) && !empty($_POST['ncrIssuedByDate'])) ? $_POST['ncrIssuedByDate'] : '00-00-0000';

		$d = explode('-', $ncrIssuedByDate);

		$ncrIssuedByDate = $d[2]."-".$d[1]."-".$d[0];

		$ncrIssuedToResponsibleParty = mysql_real_escape_string(trim($_POST['ncrIssuedToResponsibleParty']));

		$ncrIssuedToResponsiblePartyName = mysql_real_escape_string(trim($_POST['ncrIssuedToResponsiblePartyName']));

		$ncrIssuedToResponsiblePartyCompany = mysql_real_escape_string(trim($_POST['ncrIssuedToResponsiblePartyCompany']));

		$builder_id = $_SESSION['ww_builder']['user_id'];
		$complianceEvaluation = mysql_real_escape_string(trim($_POST['complianceEvaluation']));
		$project_id =  $projectId ;

		// End - Data prepare for insertion		
		if(isset($_POST['qa_ncr_id']) && $_POST['qa_ncr_id']>0){
			// Update records in qa_ncr table (date : 25/04/2013)
			$lastNcrId = $_POST['qa_ncr_id'];
				$qProject = "UPDATE qa_ncr SET
								project_id = '$project_id',
								date_raised = '$ncrDate',
								drawing_text = '$ncrDrawingText',
								location_id = '$ncrLocation',
								ncr_type = '$ncrType',
								identifying_source = '$ncrIdentifyingSource',
								identifying_contact = '$ncrIdentifyingContact',
								non_conformance_description = '$ncrNonConformanceDescription',
								identifiers_view_of_non_conformance = '$ncrIdentifiersViewOfNonConformance',
								identifiers_description_of_risk_project = '$ncrIdentifiersDescriptionOfRisk',
								identifiers_root_cause = '$ncrIdentifierRootCause',
								identifiers_suggested_measures = '$ncrIdentifierSuggestedMeasure',
								received_by = '$ncrReceivedBy',
								received_by_date = '$ncrReceivedByDate',
								recipients_view_of_non_conformance = '$ncrRecipientsViewOfNonConformance',
								recipients_description_of_risk_project = '$ncrRecipientDescriptionOfRisk',
								recipients_root_cause = '$ncrRecipientRootCause',
								recipients_suggested_measures = '$ncrRecipientSuggestedMeasure',
								correction_of_non_conformance = '$ncrCorrectionOfNonConformance',
								detailed_procedure = '$ncrDetailedProcedure',
								authorised_by = '$ncrAuthorisedBy',
								authorised_by_date = '$ncrAuthorisedByDate',
								result_of_re_inspection = '$ncrResultOfReInspection',
								original_modified_date = now(),
								last_modified_by = '$builder_id',
								last_modified_date = NOW(),
								compliance_evaluation = '$complianceEvaluation'
						WHERE
							ncr_non_conformance_id = '$lastNcrId'";
			$obj->db_query($qProject);
			if(mysql_affected_rows() > 0){$historyFlag = true;}//Set Flag Value here
		}else{
			$isUpdateMainRec = false;
			// Add records in qa_ncr table (date : 23/04/2013)
			$historyRecData = array();
			$historyRecData = $object->selQRYMultiple('id, primary_key, sql_query', 'table_history_details', 'primary_key = "'.$non_conformance_id.'" AND project_id = "'.$projectId.'" AND sql_operation = "INSERT" AND is_deleted = 0');
			if(!empty($historyRecData)){
				$dArr = unserialize($historyRecData[0]['sql_query']);
				if($dArr['qa_inspection_description'] != $description){$isUpdateMainRec = true;}
				if($dArr['qa_inspection_raised_by'] != $raisedBy){$isUpdateMainRec = true;}
				if($dArr['ncr'] != $ncr){$isUpdateMainRec = true;}
			}
			$qProject = "INSERT INTO qa_ncr SET
								non_conformance_id = '$non_conformance_id',
								project_id = '$project_id',
								date_raised = '$ncrDate',
								drawing_text = '$ncrDrawingText',
								location_id = '$ncrLocation',
								ncr_type = '$ncrType',
								identifying_source = '$ncrIdentifyingSource',
								identifying_contact = '$ncrIdentifyingContact',
								non_conformance_description = '$ncrNonConformanceDescription',
								identifiers_view_of_non_conformance = '$ncrIdentifiersViewOfNonConformance',
								identifiers_description_of_risk_project = '$ncrIdentifiersDescriptionOfRisk',
								identifiers_root_cause = '$ncrIdentifierRootCause',
								identifiers_suggested_measures = '$ncrIdentifierSuggestedMeasure',
								received_by = '$ncrReceivedBy',
								received_by_date = '$ncrReceivedByDate',
								recipients_view_of_non_conformance = '$ncrRecipientsViewOfNonConformance',
								recipients_description_of_risk_project = '$ncrRecipientDescriptionOfRisk',
								recipients_root_cause = '$ncrRecipientRootCause',
								recipients_suggested_measures = '$ncrRecipientSuggestedMeasure',
								correction_of_non_conformance = '$ncrCorrectionOfNonConformance',
								detailed_procedure = '$ncrDetailedProcedure',
								authorised_by = '$ncrAuthorisedBy',
								authorised_by_date = '$ncrAuthorisedByDate',
								result_of_re_inspection = '$ncrResultOfReInspection',
								created_date = now(),
								created_by = '$builder_id',
								last_modified_date = now(),
								last_modified_by = '$builder_id',
								compliance_evaluation = '$complianceEvaluation'";
			$obj->db_query($qProject);
			$lastNcrId = mysql_insert_id();	
		}
//Record Array to store data start here
		$recodArr['non_conformance_id'] = $non_conformance_id;
		$recodArr['date_raised'] = $ncrDate;
		$recodArr['drawing_text'] = $ncrDrawingText;
		$recodArr['ncr_type'] = $ncrType;
		$recodArr['identifying_source'] = $ncrIdentifyingSource;
		$recodArr['identifying_contact'] = $ncrIdentifyingContact;
		$recodArr['identifiers_view_of_non_conformance'] = $ncrIdentifiersViewOfNonConformance;
		$recodArr['identifiers_description_of_risk_project'] = $ncrIdentifiersDescriptionOfRisk;
		$recodArr['identifiers_root_cause'] = $ncrIdentifierRootCause;
		$recodArr['identifiers_suggested_measures'] = $ncrIdentifierSuggestedMeasure;
		$recodArr['correction_of_non_conformance'] = $ncrCorrectionOfNonConformance;
		$recodArr['detailed_procedure'] = $ncrDetailedProcedure;
		$recodArr['compliance_evaluation'] = $complianceEvaluation;
//Record Array to store data end here
		if(isset($_POST['qa_ncr_issued_id']) && $_POST['qa_ncr_issued_id']>0){
			// Update records in qa_ncr_issue_to table (date : 24/04/2013)		
			$qa_ncr_issued_id = $_POST['qa_ncr_issued_id'];
			$qProject = "UPDATE qa_ncr_issue_to SET
							ncr_non_conformance_id = '$lastNcrId',
							issued_by = '$ncrIssuedBy',
							issued_by_date = '$ncrIssuedByDate',
							issued_to_responsible_party = '$ncrIssuedToResponsibleParty',
							issued_to_responsible_party_name = '$ncrIssuedToResponsiblePartyName',
							issued_to_responsible_party_company = '$ncrIssuedToResponsiblePartyCompany',
							last_modified_by = now(),
							project_id = '".$projectId."',
							original_modified_date = now(),
							last_modified_by = '$builder_id'
						WHERE
							ncr_issue_to_id = '$qa_ncr_issued_id'";
			$obj->db_query($qProject);	
			if(mysql_affected_rows() > 0){$historyFlag = true;}//Set Flag Value here
		}else{
			// Add records in qa_ncr_issue_to table (date : 23/04/2013)
			$qProject = "INSERT INTO qa_ncr_issue_to SET
								ncr_non_conformance_id = '$lastNcrId',
								issued_by = '$ncrIssuedBy',
								issued_by_date = '$ncrIssuedByDate',
								issued_to_responsible_party = '$ncrIssuedToResponsibleParty',
								issued_to_responsible_party_name = '$ncrIssuedToResponsiblePartyName',
								issued_to_responsible_party_company = '$ncrIssuedToResponsiblePartyCompany',
								created_date = NOW(),
								project_id = '".$projectId."',
								original_modified_date = now(),
								created_by = '$builder_id',
								last_modified_date = NOW(),,
								last_modified_by = '$builder_id'";
			$obj->db_query($qProject);	
		}
//Record Array to store data start here
		$recodArr['ncr_non_conformance_id'] = $lastNcrId;
		$recodArr['issued_by'] = $ncrIssuedBy;
		$recodArr['issued_by_date'] = $ncrIssuedByDate;
		$recodArr['issued_to_responsible_party'] = $ncrIssuedToResponsibleParty;
		$recodArr['issued_to_responsible_party_name'] = $ncrIssuedToResponsiblePartyName;
		$recodArr['issued_to_responsible_party_company'] = $ncrIssuedToResponsiblePartyCompany;
//Record Array to store data end here
			
	}
	
/*--------NCR add edit end here--------*/
		
// End Image section
	$issueNameArr = array(); $fixedByDateArr = array(); $costaAttArr = array(); $statusArr = array();
	for($i=0;$i<sizeof($_POST["issue_to_id"]);$i++){
		$issue_to_id = $_POST['issue_to_id'][$i];
		$issueTo = $_POST['issueTo'][$i];
		$fixedByDate = date('Y-m-d', strtotime($_POST['fixedByDate'][$i]));
		$costAttribute = $_POST['costAttribute'][$i];		
		$status = $_POST['status'][$i];		
		if($ncrClosed == 1)
			$status = "Passed";
		if ($status == "Passed"){
			$closed_date = date();
			$ncrClosed = 1;
			$updateQry = "UPDATE qa_inspections SET ncr_closed = 1 WHERE non_conformance_id = '".$df_id."'";
			mysql_query($updateQry);
		}else{ $closed_date = '0000-00-00'; }
		$updateQryMul = "UPDATE qa_issued_to_inspections SET
					qa_issued_to_name = '".addslashes($issueTo)."',
					qa_inspection_fixed_by_date = '".$fixedByDate."',
					last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
					last_modified_date = NOW(),					
					original_modified_date = NOW(),
					qa_cost_attribute = '".$costAttribute."',
					qa_inspection_status = '".$status."'
				WHERE
					qa_issued_to_id = '".$issue_to_id."'";
		mysql_query($updateQryMul);
		
		if(mysql_affected_rows() > 0){$historyFlag = true;}//Set Flag Value here
		$issueNameArr[] = $issueTo;
		$fixedByDateArr[] = $_POST['fixedByDate'][$i];
		$costaAttArr[] = $costAttribute;
		$statusArr[] = $status;
		$_SESSION['inspection_added'] = 'Inspection Updated Successfully !';
	}
	if($ncrClosed == 1){
		$imageName = $projectId.'_sign_'.$_POST['project_inspection_id'].'.png';
		copy('./images/master_signoff.png', './inspections/signoff/'.$imageName);
		$updateQryMul = "UPDATE issued_to_for_inspections SET
			inspection_status = 'Passed',
			last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
			closed_date = '".$closed_date."',
			last_modified_date = NOW(),
			original_modified_date = NOW()
		WHERE
			inspection_id = '".$_POST['project_inspection_id']."'";
		mysql_query($updateQryMul);
		$secUpdateQRY = "UPDATE project_inspections SET inspection_sign_image = '".$imageName."', last_modified_date = NOW()  WHERE inspection_id = ".$_POST['project_inspection_id']." AND project_id = ".$projectId;
		mysql_query($secUpdateQRY);
//Update Attachments and set status Yes here for 
		$preInspectionID_insp = $object->getDataByKey('unique_qa_task_detailid', 'is_deleted', '0', 'MAX(unique_taskdetailid_id)');
		if($preInspectionID_insp){
			$inspectionID_insp = $object->selQRY('MAX(unique_taskdetailid_id) as inspectionid', 'unique_qa_task_detailid', 'is_deleted = 0');
			$rs = $obj->db_query("INSERT INTO unique_inspectionid SET inspectionid='".++$inspectionID_insp['inspectionid']."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder']['user_id']."', last_modified_by='".$_SESSION['ww_builder']['user_id']."', last_modified_date = NOW(), created_date=NOW(), created_by='".$_SESSION['ww_builder']['user_id']."'");
			if($rs){
				$newInspectionID_insp = mysql_insert_id();
			}
		}else{
			$inspectionID_insp = $object->selQRY('MAX(task_detail_id) as inspectionid', 'qa_ncr_task_detail', 'is_deleted = 1');
			$rs = $obj->db_query("INSERT INTO unique_qa_task_detailid SET taskdetailid='".$inspectionID_insp['inspectionid']."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder']['user_id']."', last_modified_by='".$_SESSION['ww_builder']['user_id']."', last_modified_date = NOW(), created_date=NOW(), created_by='".$_SESSION['ww_builder']['user_id']."'");
			if($rs){
				$newInspectionID_insp = mysql_insert_id();
			}
		}
		$q = "INSERT INTO qa_ncr_task_detail SET
					task_detail_id = '".$newInspectionID_insp."',
					task_id = '".$_SESSION['task_id']."',
					comment = '".addslashes($description)."',
					raised_by = '".addslashes($raisedBy)."',
					created_date = NOW(),
					project_id = '".$projectId."',
					created_by = '".$_SESSION['ww_builder']['user_id']."',
					last_modified_date = NOW(),
					original_modified_date = NOW(),
					last_modified_by = '".$_SESSION['ww_builder']['user_id']."'";
		$obj->db_query($q);
		$detailID = mysql_insert_id();
		$updateAttch = "UPDATE qa_ncr_attachments SET task_detail_id = '".$detailID."', last_modified_date = NOW() WHERE ncr_non_conformance_id = '".$lastNcrId."'";
		mysql_query($updateAttch);
		$updateTask = "UPDATE qa_task_monitoring SET status = 'Passed', last_modified_date = NOW() WHERE task_id = '".$_SESSION['task_id']."'";
		mysql_query($updateTask);
		
		//Update child node for update close permission
		$updateChild = "UPDATE qa_task_monitoring SET parent_task_id = 0, last_modified_date = NOW() WHERE parent_task_id = '".$_SESSION['task_id']."'";
		mysql_query($updateChild);
	}
	if($newNcrClosed == 0){
		$updateQry = "UPDATE qa_inspections SET ncr_closed = 0, last_modified_date = NOW() WHERE non_conformance_id = '".$df_id."'";
		mysql_query($updateQry);
		$updateTask = "UPDATE qa_task_monitoring SET status = 'Failed', last_modified_date = NOW() WHERE task_id = '".$_SESSION['task_id']."'";
		mysql_query($updateTask);
	}
	//Record Array to store data start here
		$recodArr['qa_issued_to_name'] = $issueNameArr;
		$recodArr['qa_inspection_fixed_by_date'] = $fixedByDateArr;
		$recodArr['qa_cost_attribute'] = $costaAttArr;
		$recodArr['qa_inspection_status'] = $statusArr;
		$recodArr['comments'] = $_POST['task_comment'];
		$recodArr['status'] = $_POST['task_status'];
	//Record Array to store data end here
//Record Array Insert Here
	if($historyFlag == true){//Set Flag Value here
		$historyData = array();
		$historyData = $object->selQRYMultiple('id, primary_key', 'table_history_details', 'primary_key = "'.$non_conformance_id.'" AND project_id = "'.$projectId.'" AND sql_query = \''.serialize($recodArr).'\' AND is_deleted = 0 ORDER BY created_date DESC LIMIT 1');
		if(!empty($historyData)){
			$dArr = unserialize($historyData[0]['sql_query']);
			if($dArr['ncr'] != $ncr){$ncrFlag = true;}
		}	
#		print_r($historyData);print_r($isUpdateMainRec);
		if(empty($historyData)){
			if($isUpdateMainRec){
				$insertHistory = "INSERT INTO table_history_details SET
							primary_key = '".$non_conformance_id."',
							table_name = 'qa_inspections',
							sql_operation = 'UPDATE',
							sql_query = '".serialize($recodArr)."',
							created_by = '".$_SESSION['ww_builder_id']."',
							created_date = NOW(),
							last_modified_by = '".$_SESSION['ww_builder_id']."',
							last_modified_date = NOW(),
							original_modified_date = NOW(),
							project_id = '".$projectId."'";
				mysql_query($insertHistory);	
			}

		}
	}
	if(isset($_SESSION['ww_is_company'])){?>
		<script language="javascript" type="text/javascript">window.location.href="?sect=qa_task_search&bk=Y&ms=<?=base64_encode('Updated')?>";</script>
<?php }else
		if(isset($_SESSION['ww_is_builder'])){?>
			<script language="javascript" type="text/javascript">window.location.href="?sect=qa_task_search&bk=Y&ms=<?=base64_encode('Updated')?>";</script>
<?php	} 
}

$inspecGraphDetails = $object->selQRYMultiple('qa_graphic_id, qa_graphic_type, qa_graphic_name', 'qa_graphics', 'is_deleted=0 and qa_graphic_name!="" and non_conformance_id='.$df_id);

$_SESSION['inspGraph'] = $inspecGraphDetails;

$inspGraph = array();
if(!empty($inspecGraphDetails)){
	foreach($inspecGraphDetails as $key=>$val){
		$inspGraph[$val['qa_graphic_type']][] = $val['qa_graphic_name'];
	}
}

$inspectionDetail = $object->selQRY('non_conformance_id, task_id, location_id, qa_inspection_date_raised, qa_inspection_raised_by, qa_inspection_inspected_by, qa_inspection_description, qa_inspection_location, resource_type, ncr, project_inspection_id, ncr_closed, is_attachments_required', 'qa_inspections', 'non_conformance_id = "'.$df_id.'" AND is_deleted = 0');
#echo "<pre>";print_r($inspectionDetail);die;
$projectId = $_SESSION['projIdQA'];
$projectName = $object->getDataByKey('user_projects', 'project_id', $projectId, 'project_name');
$issToList = '';?>
<div class="content_center" style="margin-left:70px;margin-top:80px\9;">
	<div class="content_hd" style="background-image:url(images/edit_defect_hd.png);margin: -5px 0 -30px -80px;margin-top:-85px\9;">
	<!--<img src="images/view_history.png" onclick="showHistory(<?=$df_id?>, <?=$str_id?>);" style="margin:15px 0 0 700px;cursor:pointer;" />-->
</div>
	<div class="signin_form1" style="margin-top:15px;margin-top:-25px\9;">
	<?php if($msg != ''){?>
		<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;">
			<div class="success_r" style="height:35px;width:405px;">
				<p><?php echo $msg; ?></p>
			</div>
		</div>
	<?php }
	$locations_exists = array(); ?>
		<form action="" method="post" name="edit_non_confirmance_inspection" id="edit_non_confirmance_inspection" onsubmit="return checkDuplicateIssue()  && checkAttachedValid() && checkImageValid();">
			<table width="470" border="0" align="left" cellpadding="0" cellspacing="15" style="border: 1px solid;width: 670px;">
				<tr>
					<td width="134" nowrap="nowrap" valign="top"><b>Project Name:</b> <?=$projectName?></td>
					<td width="312" colspan="2"><b>Task Name:</b> <?=$qaTaskDataArr[0]['task']?><span class="req"></span> </td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Location</td>
					<td width="312" colspan="2"> Description<span class="req"></span> </td>
				</tr>
				<tr>
					<td width="134" valign="top">
						<?php echo $object->QAsubLocationsProgressMonitoring($qaTaskDataArr[0]['sub_location_id'], ' > ');?>
						<input type="hidden" name="projectId" id="projectId" value="<?=$projectId;?>"  />
					</td>
					<td width="312" colspan="2">
						<textarea name="description" id="description" class="textareaBoxStyle" cols="33" rows="5" style="width:250px;" ><?=$inspectionDetail['qa_inspection_description']?>
</textarea>
						<div style="position:absolute; margin-left: 272px;margin-top: -73px;"><img id="dropDown" src="images/downbox.png" border="0" style="background-color:none;" /></div>
						<div id="discriptionHide">
							<?php $standardDefects = $object->selQRYMultiple('description', 'standard_defects', 'project_id = '.$_SESSION['projIdQA'].' and is_deleted=0 group by description order by description'); ?>
							<ul id="standardDefect" style="list-style:none;margin-left:-30px;">
								<?php if(!empty($standardDefects)){
							
							 $i=0; foreach($standardDefects as $des){$i++;?>
								<li class="clickableLines"><?php echo $des['description'];?></li>
								<?php 	}
						}else{?>
								<li class="clickableLines">No One Standard Defect Found !</li>
								<?php }?>
							</ul>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="4"><div id="attachments" style="overflow:hidden;">
							<!--span style="margin-left:15px;float:left;">Attachment</span><br /-->
							<?php $inspGraph['images']=isset($inspGraph['images'])?$inspGraph['images']:0;
$totalImages = count($inspGraph['images']); ?>
							<table width="100%" border="0" align="center">
								<tr>
									<td width="28%" align="left" valign="middle">Attachment</td>
									<td width="23%" align="left" valign="middle"><div id="editImage1" style="margin-left:50px;" >
											<div id='injection_site'></div>
											<?php if($totalImages>0 && !empty($inspGraph['images'][0])){?>
											<input type="image" onclick="return launchEditor('photoImage1', 'http://probuild.constructionid.com/inspections/photo/<?php echo $inspGraph['images'][0];?>');" value="Edit photo" src="images/markup.png">
											
											<?php }else{$totalImages=0;}?>
										</div></td>
									<td width="49%" align="left" valign="middle"><div id="editDrawing" style="margin-left:92px;" >
											<?php if(isset($inspGraph['drawing']) && !empty($inspGraph['drawing'][0])){ ?>
											<input type="image" onclick="return launchEditor('drawImage1', 'http://probuild.constructionid.com/inspections/drawing/<?php echo $inspGraph['drawing'][0];?>');" value="Edit photo" src="images/markup.png">
											<?php }else{unset($inspGraph['drawing']);}?>
											<div id='injection_site'></div>
										</div></td>
								</tr>
							</table>
							<div class="innerDiv"  style="margin-left:200px;" align="center" >
								<div style="height:120px;overflow:hidden;">
									<label class="filebutton" align="center"> &nbsp;Browse Image 1
									<input type="file" id="image1" name="image1" style="width:120px;height:120px;" />
									</label>
									<div id="response_image_1" style="width:120px;">
										<?php if($totalImages>0){?>
										<img width="100" height="90" id="photoImage1" style="margin-left:10px;margin-top:8px;" src="inspections/photo/<?php echo $inspGraph['images'][0];?>">
										<input type="hidden" class="image_qa" value="<?php echo $inspGraph['images'][0];?>" name="photo[]">
										<?php }?>
									</div>
									
								</div>
								
								<img id="removeImg1" src="images/remove_img_btn.png" style="margin-top:10px;cursor:pointer;<?php if($totalImages>0){}else{echo 'display:none;';}?>" onclick="removeImages('response_image_1', this.id, <?php if($totalImages>0){echo '1';}else{echo '0';}?>);" /> </div>
							<div class="innerDiv" align="center" style="margin: 0 0 20px 100px;">
								<div style="height:120px;overflow:hidden;" onclick="showPhotoLibrary(1, 1);">
									<label class="filebutton" align="center"> Browse Drawing
									<input type="hidden" id="drawing2" name="drawing2" value="" style="width:120px;height:120px; display:none;" />
									</label>
									<div id="response_drawing" style="width:120px;">
										<?php if(isset($inspGraph['drawing'])){?>
										<img width="100" height="90" id="drawImage1" style="margin-left:10px;margin-top:8px;" src="inspections/drawing/<?php echo $inspGraph['drawing'][0];?>">
										<input type="hidden" class="image_qa" value="<?php echo $inspGraph['drawing'][0];?>" name="drawing">
										<?php }?>
									</div>
								</div>
								<img id="removeImg3" src="images/remove_img_btn.png" style="margin-top:10px;cursor:pointer;<?php if(isset($inspGraph['drawing'])){}else{echo 'display:none;';}?>" onclick="removeImages('response_drawing', this.id, <?php if(isset($inspGraph['drawing'])){echo '1';}else{echo '0';}?>);" /> </div>
						</div></td>
				</tr>
				<tr>
				<td colspan="2">
					<div class="error-edit-profile_big" style="width:250px;display:none;"  id="imageError">Please select atleast one image or drawing</div>
				</td>
			</tr>
			<tr></tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Raised By<span class="req">*</span></td>
					<td width="312" colspan="2">
					<?php if($_SESSION['ww_is_company'] == 1){?>
						<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
								<option value="GPCL" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'GPCL'){ echo 'selected="selected"';}else{ if($inspectionDetail['qa_inspection_raised_by'] == ''){ echo 'selected="selected"'; } }?> >GPCL</option>
								<option value="Architect" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'Architect'){ echo 'selected="selected"';}?>>Architect</option>
								<option value="Structural Engineer" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'Structural Engineer'){ echo 'selected="selected"';}?>>Structural Engineer</option>
								<option value="Services Engineer" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'Services Engineer'){ echo 'selected="selected"';}?>>Services Engineer</option>
								<option value="Accreditation" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'Accreditation'){ echo 'selected="selected"';}?>>Accreditation</option>
								<option value="Consultant" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'Consultant'){ echo 'selected="selected"';}?>>Consultant</option>
								<option value="Independent Reviewer" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'Independent Reviewer'){ echo 'selected="selected"';}?>>Independent Reviewer</option>
								<option value="Stakeholders" <?php if($inspectionDetail['qa_inspection_raised_by'] == 'Stakeholders'){ echo 'selected="selected"';}?>>Stakeholders</option>
								<option value="Sub Contractor" <?php if($inspectionDetail['qa_inspection_raised_by'] == 'Sub Contractor'){ echo 'selected="selected"';}?> >Sub Contractor</option>
						</select>
						<?php }else{
							if($_SESSION['userRole'] != 'All Defect' && isset($_SESSION['userRole'])){?>
						<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
							<option value="">Select</option>
							<option value="<?=$_SESSION['userRole']?>" selected="selected" ><?=$_SESSION['userRole']?></option>
						</select>
						<?php }else{ ?>
							<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
								<option value="GPCL" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'GPCL'){ echo 'selected="selected"';}else{ if($inspectionDetail['qa_inspection_raised_by'] == ''){ echo 'selected="selected"'; } }?> >GPCL</option>
								<option value="Architect" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'Architect'){ echo 'selected="selected"';}?>>Architect</option>
								<option value="Structural Engineer" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'Structural Engineer'){ echo 'selected="selected"';}?>>Structural Engineer</option>
								<option value="Services Engineer" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'Services Engineer'){ echo 'selected="selected"';}?>>Services Engineer</option>
								<option value="Accreditation" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'Accreditation'){ echo 'selected="selected"';}?>>Accreditation</option>
								<option value="Consultant" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'Consultant'){ echo 'selected="selected"';}?>>Consultant</option>
								<option value="Independent Reviewer" <?php if($inspectionDetail['qa_inspection_raised_by']  == 'Independent Reviewer'){ echo 'selected="selected"';}?>>Independent Reviewer</option>
								<option value="Stakeholders" <?php if($inspectionDetail['qa_inspection_raised_by'] == 'Stakeholders'){ echo 'selected="selected"';}?>>Stakeholders</option>
								<option value="Sub Contractor" <?php if($inspectionDetail['qa_inspection_raised_by'] == 'Sub Contractor'){ echo 'selected="selected"';}?> >Sub Contractor</option>
							</select>						
						<?php } 
						}?>
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Issued To Detail</td>
					<td colspan="2">
						<table width="70%" border="0" cellspacing="0" cellpadding="0" class="gridtable" >
							<tr>
								<td>Issued&nbsp;To</td>
								<td>Fix&nbsp;By&nbsp;Date</td>
								<td>Cost Attribute</td>
								<td>Status</td>
							</tr>
							<?php 
//Update Dated 22-11-2012
$issueToName = $object->selQRYMultiple('distinct(qa_issued_to_name)', 'qa_issued_to_inspections', 'project_id = "'.$inspectionDetail['project_id'].'" and is_deleted=0');
$issueTagArray = array();

if(!empty($issueToName)){
	foreach($issueToName as $issueName){
		$issueTagData = $issueName['qa_issued_to_name'];
	}
}
$issueToSelect = array();
$issueToData = $object->selQRYMultiple('issue_to_name, company_name', 'inspection_issue_to', 'project_id = "'.$_SESSION['projIdQA'].'" AND is_deleted = 0 AND issue_to_name != "" GROUP BY issue_to_name');
foreach($issueToData as $isData){
	if($isData['company_name'] != ""){
		$issueToSelect[] = $isData['issue_to_name']." (".$isData['company_name'].")";
	}else{
		$issueToSelect[] = $isData['issue_to_name'];
	}
}
if(!empty($issueTagArray)){
	foreach($issueTagArray as $issueTArray){
		$testKeyIssue = (string)key($issueTArray);
		$pos = strpos($locations_exists, $testKeyIssue);
		if($pos === false) {}else{
			if(!in_array($issueTArray[key($issueTArray)], $standardDefects)){
				$issueToSelect[] = $issueTArray[key($issueTArray)];
			}
		}
		if(key($issueTArray) == ''){
			$issueToSelect[] = $issueTArray[key($issueTArray)];
		}
	}
}
$issueToData = $object->selQRYMultiple('qa_issued_to_id, qa_issued_to_name, qa_inspection_fixed_by_date, qa_cost_attribute, qa_inspection_status', 'qa_issued_to_inspections', 'non_conformance_id = '.$df_id.' and is_deleted = 0  group by qa_issued_to_name order by qa_issued_to_name');

$i=0;
$issueToRedundent = '';

if(!empty($issueToData)){
	foreach($issueToData as $issueTo){
		if($issueToRedundent == ''){
			$issueToRedundent .= '"'.$issueTo['qa_issued_to_name'].'"';
		}else{
			$issueToRedundent .= ', "'.$issueTo['qa_issued_to_name'].'"';
		}
	}
}
if(!empty($issueToData)){
	foreach($issueToData as $issueTo){
		$i++;
		$currentIssueToName = $issueTo['qa_issued_to_name'];
		$inspectionFixedByDate = $issueTo['qa_inspection_fixed_by_date']; 
		$inspectionStatus = $issueTo['qa_inspection_status']; 
		$costAttribute = $issueTo['qa_cost_attribute'];  ?>
							<tr>
								<td width="33%" style="text-shadow:none;"><input type="hidden" name="issue_to_id[]" id="issue_to_id_<?php echo $i;?>" value="<?php echo $issueTo['qa_issued_to_id'];?>" />
									<?php if(in_array($currentIssueToName, $issueToSelect)){?>
									<select name="issueTo[]" type="text" id="autocomplete_<?php echo $i;?>" class="issueTo" style="width:120px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">
										<?php for($k=0;$k<sizeof($issueToSelect);$k++){?>
										<option value="<?php echo $cValue = trim(stripslashes($issueToSelect[$k]))?>"<?php if($issueToSelect[$k] == $currentIssueToName){echo 'selected="selected"'; unset($issueToSelect[$k]); }?>>
										<?=$cValue?>
										</option>
										<?php }?>
									</select>
									<?php }else{?>
									<select name="issueTo[]" type="text" id="autocomplete_<?php echo $i;?>" class="issueTo" style="width:120px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">
										<?php for($k=0;$k<sizeof($issueToSelect);$k++){?>
										<option value="<?php echo $cValue = trim(stripslashes($issueToSelect[$k]))?>">
										<?=$cValue?>
										</option>
										<?php }?>
										<option value="<?=trim(stripslashes($currentIssueToName));?>" selected="selected" >
										<?=trim(stripslashes($currentIssueToName))?>
										</option>
									</select>
									<?php }?>
								</td>
								<td width="33%" style="text-shadow:none;" align="center">
									<?php if($inspectionFixedByDate != '0000-00-00'){
										$fixedByDate = date('d-m-Y', strtotime($inspectionFixedByDate));
									}else{
										$fixedByDate = '';
									}?>
									<input name="fixedByDate[]" id="fixedByDate_<?php echo $i;?>" class="fixedByDate" readonly=s"readonly" size="10" value="<?php echo $fixedByDate;?>" />
								</td>
								<td style="text-shadow:none;" align="center">
									<select name="costAttribute[]" type="text" id="costAttribute_<?php echo $i;?>" class="issueTo" style="width:120px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">
										<option <?php if($costAttribute == 'None'){ echo 'selected="selected"';}?> value="None">None</option>
										<option <?php if($costAttribute == 'Backcharge'){ echo 'selected="selected"';}?> value="Backcharge">Backcharge</option>
										<option <?php if($costAttribute == 'Variation'){ echo 'selected="selected"';}?> value="Variation">Variation</option>
									</select>
								</td>
								<td style="text-shadow:none;" align="center">
									<select name="status[]" id="status_<?php echo $i;?>" class="status" style="width:90px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">
										<option <?php if($inspectionStatus == 'Failed'){ echo 'selected="selected"';}?> value="Failed">Failed</option>
										<option <?php if($inspectionStatus == 'Pending'){ echo 'selected="selected"';}?> value="Pending">Pending</option>
										<option <?php if($inspectionStatus == 'Fixed'){ echo 'selected="selected"';}?> value="Fixed">Fixed</option>
										<option <?php if($inspectionStatus == 'Passed'){ echo 'selected="selected"';}?> value="Passed">Passed</option>
									</select>
								</td>
							</tr>
							<?php }
	}else{ ?>
							<td colspan="4"><em>No One Sub Contractor Found</em></td>
								<?php }?>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="3" >&nbsp;</td>
				</tr>
				
				<tr>
					<td colspan="2" valign="top" align="center">
						<input type="button" value="Attach supporting documents if applicable." style="margin:5px; text-align:center; width:300px;height:60px;" id="attchmentBox"  onclick="bulkUploadRegisters(<?php echo $inspectionDetail['non_conformance_id']?>);"/>
						<div id="addAttachments" style="display:none;">
						<?php if(isset($attachedData) && !empty($attachedData)){
							#echo "<pre>";print_r($attachedData);die;
							foreach($attachedData as $key=>$val){ 
								echo '<div id="saveBox_'.$key.'" class="attach_'.$val['ncr_attachment_id'].'"><input type="hidden" value="'.$val['attachment_file_name'].'" name="attachments[]" id="file_0"><input type="hidden" value="'.$val['attachment_title'].'" name="attachTitle[]" class="textBoxStyle" id="attachTitle[]" maxlength="50">  <textarea rows="2" cols="25" class="textareaBoxStyle" id="attachDescription[]" name="attachDescription[]">'.$val['attachment_description'].'</textarea></div>';
							}
						 }?>
						</div>
					</td>
				</tr>
				<tr>
					<td></td>
					<td colspan="2">
					<div class="error-edit-profile_big" style="width:250px;display:none;"  id="attachError">Please add atleast one attachement</div>
				</td>
				</tr>
				<tr>
				<td colspan="3" >
					<?php if($inspectionDetail['ncr']=='1') {
						include("includes/edit_non_conformance.php");
					}?>
				</td>
				</tr>
				<?php if($inspectionDetail['ncr'] == '1' && $qaTaskDataArr[0]['parent_task_id'] == 0){
						if(!in_array($projectId, array(203, 223))){?>
				<tr>
					<td colspan="4">
						<input type="checkbox" name="ncrClosed" id="ncrClosed" value="1"
							<?php if ($inspectionDetail["ncr_closed"] == 1) echo 'checked="checked"'; ?>  >
						<label for="ncrClosed">NCR Closed</label>
					</td>
				</tr>
				<?php 	}
					  }?>
				<tr>
					<td align="right">
						<a href="?sect=qa_task_search&bk=Y">
							<img src="images/back_btn.png" style="border:none; width:111px;" />
						</a>
					</td>
					<td>
						<input type="hidden" value="<?=$df_id?>" name="df_id" id="df_id"  />
						<input type="hidden" value="<?=$qaTaskDataArr[0]['comments']?>" name="task_comment" id="task_comment"  />
						<input type="hidden" value="<?=$qaTaskDataArr[0]['status']?>" name="task_status" id="task_status"  />
						<input type="hidden" value="<?=$inspectionDetail["project_inspection_id"]?>" name="project_inspection_id" id="project_inspection_id"  />
						<input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/save.png); border:none; width:111px;" />
						<?php if($inspectionDetail['ncr']=='1') {
								$query = "select count(*) from qa_ncr where non_conformance_id=". $qaNcrData['non_conformance_id'] ." and is_deleted=0";
							$rs = mysql_query($query);
							if ($row=mysql_fetch_array($rs)){
								if ($row[0] > 0){ ?>
							<input name="button1" type="button"  class="submit_btn" style="background-image:url(images/ncr_generate_report.png); border:none; width:178px;" onclick="generateDetaildReport('non_conformance_id=<?php echo $qaNcrData['non_conformance_id'];?>&subLocationQA4=<?php echo $qaNcrData['location_id'];?>&projNameQA=<?php echo $projectId?>&name=')"  />
						<?php 	}
							}
						}?>
					</td>
				</tr>
			</table>
			<?php if($descriptionArr[0]['is_image_require']==1){?>
				<input type="hidden" value="1" name="is_image_require" id="is_image_require" />
			<?php }else{?>	
				<input type="hidden" value="0" name="is_image_require" id="is_image_require" />
			<?php }?>
			
			<?php if($inspectionDetail['is_attachments_required']==1){?>
				<input type="hidden" value="1" name="is_attachments_required" id="is_attachments_required" />
			<?php }else{?>	
				<input type="hidden" value="0" name="is_attachments_required" id="is_attachments_required" />
			<?php }?>
		</form>
	</div>
</div>
<style>
fieldset.permission { border:1px solid white; padding:15px; margin-top:30px; }
fieldset.permission legend { color:#FFFFFF; }
input[type=checkbox] { position: relative; cursor:pointer;}
label.label_check {cursor:pointer;}
.bulkfiles {clear: both;border: 1px solid #ccc;background-color: #E4E4E4;padding: 3px;position: relative;margin: 3px;z-index: 1;width: 98%;cursor: default;height: 70px;}
div#innerModalPopupDiv, div#innerModalPopupDiv1{color:#000000;}
.approveDrawingReg{margin-left:0px;}
ul#filePanel li{float:left;}
ul#filePanel{list-style:none; margin:0px; padding:0px;}
/*.dataHolder > div{ margin-top: 5px;position: absolute;}*/
#revisionBox > textarea {
    margin-left: 23px;
}
#revisionBox > textarea {margin-left: 95px;margin-top: -35px;}
ul.buttonHolder {list-style:none;}
ul.buttonHolder li {float:left;margin-left:10px;}
ul.buttonHolder li #disableButtonDiv{height: 50px;left: -5px;position: relative;top: -5px;width: 111px;z-index: 9999;}
.roundCorner{border-radius: 5px;}
</style>
<script type="text/javascript" src="js/modal.popup.js"></script>
<script type="text/javascript" src="js/qa_attach_multiupload.js"></script>
<script type="text/javascript">
var fileCounter = 0;
<?php if(!empty($_SESSION['ncrAttachments'])){?>
	fileCounter = <?=sizeof($_SESSION['ncrAttachments']);?>;
<?php }?>
function bulkUploadRegisters(non_confirmance_id){
	modalPopup(align, top, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_qa_attach_bulk.php?&name='+Math.random()+'&non_confarmance_id='+non_confirmance_id, loadingImage, bulkRegistration);
}

var mappingDocumentArr = {};//Global Array to store select element in 
var mappedDocArr = {};//Global Array to store select element to show again selected
function bulkRegistration(){
	var config = {
		support : ",application/pdf,application/x-download,application/zip,text/plain,image/png,image/jpg,image/jpeg",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDiv",// Upload Area ID
		uploadUrl: "add_qa_attach_bulk.php?antiqueID="+Math.random()// Server side upload url
	}
	mappingDocumentArr = {};
	mappedDocArr = {};
	initBulkUploader(config);	
}
</script>
<script type="text/javascript" src="js/jquery.contextmenu.r2.js"></script>
<link href="css/jquery-ui.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.tree.js"></script>
<script>
<?php if($_SESSION['web_close_inspection'] != 1){?>
$(".status").change(function(){
	if($(this).val() == 'Passed'){
		jAlert('You can\'t Passed any inspection from here !');
		$(this).val('Failed');
	}
});
<?php }?>
$("#autocomplete_1").change(function(){
	var arr = [ <?=$issueToRedundent;?> ];
	var valueIssueTo_1 = $("#autocomplete_1").val();
	var check = $.inArray(valueIssueTo_1, arr);
	if(check != (-1)){
		if(valueIssueTo_1 == arr[0]){}else{
			jAlert(valueIssueTo_1+' Already Selected !');
			$("#autocomplete_1").val(arr[0]);
		}
	}
});
$("#autocomplete_2").change(function(){
	var arr = [ <?=$issueToRedundent;?> ];
	var valueIssueTo_2 = $("#autocomplete_2").val();
	var check = $.inArray(valueIssueTo_2, arr);
	if(check != (-1)){
		if(valueIssueTo_2 == arr[1]){}else{
			jAlert(valueIssueTo_2+' Already Selected !');
			$("#autocomplete_2").val(arr[1]);
		}
	}
});
$("#autocomplete_3").change(function(){
	var arr = [ <?=$issueToRedundent;?> ];
	var valueIssueTo_3 = $("#autocomplete_3").val();
	var check = $.inArray(valueIssueTo_3, arr);
	if(check != (-1)){
		if(valueIssueTo_3 == arr[2]){}else{
			jAlert(valueIssueTo_3+' Already Selected !');
			$("#autocomplete_3").val(arr[2]);
		}
	}
});
$("#autocomplete_4").change(function(){
	var arr = [ <?=$issueToRedundent;?> ];
	var valueIssueTo_4 = $("#autocomplete_4").val();
	var check = $.inArray(valueIssueTo_4, arr);
	if(check != (-1)){
		if(valueIssueTo_4 == arr[3]){}else{
			jAlert(valueIssueTo_4+' Already Selected !');
			$("#autocomplete_4").val(arr[3]);		
		}
	}
});

$(".status").change(function(){
	if($(this).val() == 'Draft'){
		jAlert('Sorry! check list is not completed yet, Inspection is Under drafting stage.');
		$(this).val('Draft');
	}
});

function checkReturn(){
	var r = jConfirm('Do you want to delete Inspection ?', null, function(r){
		if (r==true){
			var projId = document.getElementById('df_id');
			projId.name = 'removeProject';
			document.forms['edit_non_confirmance_inspection'].submit();
		}
	});
}
var align = 'center';
var top1 = 100;
var width = 670;
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
var statusLoop = <?=$i;?>;

//Default Issue to array
<?php for($g=1; $g <= $i; $g++){ ?>
	var issueTo_<?=$g?> = '';
	var issueTo_selectd_<?=$g?> = $('#autocomplete_<?=$g?>').val();
	$('#autocomplete_<?=$g?> option').each(function(){
		if(this.value != 0){
			if(issueTo_<?=$g?> == ''){
				issueTo_<?=$g?> = this.value;
			}else{
				issueTo_<?=$g?> += ','+this.value;
			}
		}
	});
	$('#costImpact_<?=$g?>').change(function(){
		var priceVal = this.value;
		var costAttr = $('#costAttribute_<?=$g?>').val();
		if(costAttr == 'None'){
			jAlert('Sorry! Please select any other cost attribute type first, for editing this Issued To cost impact type.', 'Permission Alert');
			$('#costImpact_<?=$g?>').val('None');
			return false;
		}else{
			if(priceVal == 'None'){
				$('#costImpactPrice_<?=$g?>').val('0.00');
			}
			if(priceVal == 'Low'){
				$('#costImpactPrice_<?=$g?>').val('100.00');
			}
			if(priceVal == 'Medium'){
				$('#costImpactPrice_<?=$g?>').val('1000.00');
			}
			if(priceVal == 'High'){
				$('#costImpactPrice_<?=$g?>').val('10000.00');
			}
		}
	});
	
	$('#costAttribute_<?=$g?>').change(function(){
		if(this.value == 'None'){
			$('#costImpact_<?=$g?>').html('<option value="None">None</option>');
			$('#costImpactPrice_<?=$g?>').val('0.00');
			$('#costImpactPrice_<?=$g?>').attr('disabled', true);
		}else{
			$('#costImpactPrice_<?=$g?>').attr('disabled', false);
			$('#costImpact_<?=$g?>').html('<option value="None">None</option><option value="Low">Low</option><option value="Medium">Medium</option><option value="High">High</option>');
		}
	});
<?php }?>
//Default Issue to array
function checkNo(evt, alertID, obj, objVal){
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if(charCode == 46){
		return true;
	}
	if (charCode > 31 && (charCode < 48 || charCode > 57)){
		return false;
	}
	if(objVal.length >= 11){
		if(objVal > 10000000){
			document.getElementById(obj).value = '10000000.00';
			jAlert("You can't enter more than 10000000 value");
			return false;
		}
	}
	return true;
}

function taggingIssueTo(newTree){
	if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	params = "LocationSearchString="+newTree+"&projectID=<?=$inspectionDetail['project_id'];?>&uniqueId="+Math.random();
	xmlhttp.open("POST", "tagedIssuetTo.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			if(xmlhttp.responseText != ''){
				var resString = xmlhttp.responseText;
				var resSplitResult = resString.split("@@@");
				<?php for($g=1; $g <= $i; $g++){ ?>
//					resSplitResult = resSplitResult.toString(); 
	//				uniqueIssueToArray = resSplitResult.split(",");
					var issueToOption = '<option value="NA">NA</option>';
					for(i = 0; i < resSplitResult.length; i++){
						resSplitResult[i] = jQuery.trim(resSplitResult[i]);
						var tempString = '<option value="'+resSplitResult[i]+'"';
						if(issueTo_selectd_<?=$g?> == resSplitResult[i]){
							tempString += 'selected="selected"';
						}
						tempString += '>'+resSplitResult[i]+'</option>'; 
						issueToOption += tempString;
					}
					document.getElementById('autocomplete_<?=$g?>').innerHTML = issueToOption;
				<?php }?>
			}
			taggingStandardDefect(newTree);
		}
	}
	xmlhttp.send(params);
}

function taggingStandardDefect(newTree){
	if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	params = "LocationSearchString="+newTree+"&projectID=<?=$inspectionDetail['project_id'];?>&uniqueId="+Math.random();
	xmlhttp.open("POST", "tagedStandardDefect.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText != ''){
				var resString = xmlhttp.responseText;
				document.getElementById('standardDefect').innerHTML = resString;
			}
		}
	}
	xmlhttp.send(params);
}
var previousLocId = '';
$(document).ready(function() {
	$('ul.telefilms').tree({default_expanded_paths_string : '0/0/0,0/0/2,0/2/4'});
	$('span.demo1').contextMenu('myMenu2', {
	bindings: {
		'select': function(t) {
			if(previousLocId != ''){
				$(previousLocId).css({ 'font-weight' :'normal', 'font-style':'normal', 'text-decoration':'none' });
			}
			$(t).css({ 'font-weight' :'bold', 'font-style':'italic', 'text-decoration':'underline' });
			previousLocId = t;
			$("#location").val(t.id);
			$("#locationChecklist").val(document.getElementById(t.id).innerHTML);
			try{
				if(window.XMLHttpRequest){
					xmlhttp=new XMLHttpRequest();
				}else{
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				showProgress();
				params = "locationId="+t.id+"&uniqueId="+Math.random();
				xmlhttp.open("POST", "reloadLocationExpand.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.setRequestHeader("Content-length", params.length);
				xmlhttp.setRequestHeader("Connection", "close");
				xmlhttp.onreadystatechange=function(){
					if (xmlhttp.readyState==4 && xmlhttp.status==200){
						hideProgress();
						var newTree = xmlhttp.responseText;
						$('#location_exists').html(newTree);
						$('#locationTree').val(newTree);
						taggingIssueTo(newTree);
					}
				}
				xmlhttp.send(params);
			}catch(e){
			//	alert(e.message); 
			}
		}
	}
	});
});
var unique = function(origArr) {
	var issueToArray = new Array();
	issueToArray = origArr.split(',');
	var newArr = [], origLen = issueToArray.length, found, x, y;
    for ( x = 0; x < origLen; x++ ) {
        found = undefined;
        for ( y = 0; y < newArr.length; y++ ) {
            if ( issueToArray[x] === newArr[y] ) { 
              found = true;
              break;
            }
        }
        if (!found){
			newArr.push(issueToArray[x]);
		}
    }
	return newArr;
}
$("#dropDown").click(function () {
	if ($("#discriptionHide").is(":hidden")) { $("#discriptionHide").slideDown("slow"); }else{ $("#discriptionHide").hide("slow");}
});
$("li.clickableLines").click(function(){ $("#description").val(this.innerHTML); $("#discriptionHide").hide("slow");});
function checkDuplicateIssue(){
	var status = true;
	var checkValue = $('#issueTo_0').val();
	if(checkValue == 'NA' && $('#hide_1').is(':visible') && $('#hide_2').is(':visible')){
		if($('#issueTo_1').val() == checkValue || $('#issueTo_2').val() == checkValue){
			jAlert("Sorry! Sub Contractor Name can't be same.\nPlease choose another name.");
			return false;
		}
	}else{
		if((checkValue == 'NA' && $('#hide_1').is(':visible')) || (checkValue == 'NA' && $('#hide_2').is(':visible'))){
			if($('#issueTo_1').val() == checkValue){
				jAlert("Sorry! Sub Contractor Name can't be same.\nPlease choose another name.");
				return false;
			}
		}
	}
	
	var checkValue = $('#issueTo_1').val();
	if(checkValue == 'NA' && $('#hide_1').is(':visible') && $('#hide_2').is(':visible')){
		if($('#issueTo_0').val() == checkValue || $('#issueTo_2').val() == checkValue){
			jAlert("Sorry! Sub Contractor Name can't be same.\nPlease choose another name.");
			return false;
		}
	}else{
		if((checkValue == 'NA' && $('#hide_0').is(':visible')) || (checkValue == 'NA' && $('#hide_2').is(':visible'))){
			if($('#issueTo_1').val() == checkValue){
				jAlert("Sorry! Sub Contractor Name can't be same.\nPlease choose another name.");
				return false;
			}
		}
	}
	
	var checkValue = $('#issueTo_2').val();
	if(checkValue == 'NA' && $('#hide_0').is(':visible') && $('#hide_1').is(':visible')){
		if($('#issueTo_0').val() == checkValue || $('#issueTo_1').val() == checkValue){
			jAlert("Sorry! Sub Contractor Name can't be same.\nPlease choose another name.");
			return false;
		}
	}else{
		if((checkValue == 'NA' && $('#hide_0').is(':visible')) || (checkValue == 'NA' && $('#hide_1').is(':visible'))){
			if($('#issueTo_1').val() == checkValue){
				jAlert("Sorry! Sub Contractor Name can't be same.\nPlease choose another name.");
				return false;
			}
		}
	}
	return status;
}
</script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="http://feather.aviary.com/js/feather.js"></script>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
<script type='text/javascript'>
function removeImages(divId, removeButtonId, isEdit){
	var r = jConfirm('Are you sure, you want to delete Image?', null, function(r){
		if (r==true){
			var imgDiv=document.getElementById(divId);
			var imgSrc = imgDiv.childNodes[0].src;	
			showProgress();	
			if(isEdit==0){
				$.ajax({
				url: "remove_uploaded_file.php",
				type: "POST",
				data: "imageName="+imgSrc,
				success: function (res) {
					hideProgress();
					imgDiv.innerHTML = '';		
					document.getElementById(removeButtonId).style.display = 'none';
					if(removeButtonId=="removeImg1"){
						document.getElementById("editImage1").style.display = "none";
						document.getElementById("editImage1").innerHTML = '';
					}
					if(removeButtonId=="removeImg2"){
						document.getElementById("editImage2").style.display = "none";
						document.getElementById("editImage2").innerHTML = '';
					}
					if(removeButtonId=="removeImg3"){
						document.getElementById("editDrawing").style.display = "none";
						document.getElementById("editDrawing").innerHTML = '';
					}
				}
			});
			}else{
				hideProgress();
				imgDiv.innerHTML = '';		
				document.getElementById(removeButtonId).style.display = 'none';
				if(removeButtonId=="removeImg1"){
					document.getElementById("editImage1").style.display = "none";
					document.getElementById("editImage1").innerHTML = '';
				}
				if(removeButtonId=="removeImg2"){
					document.getElementById("editImage2").style.display = "none";
					document.getElementById("editImage2").innerHTML = '';
				}
				if(removeButtonId=="removeImg3"){
					document.getElementById("editDrawing").style.display = "none";
					document.getElementById("editDrawing").innerHTML = '';
				}		
			}
		}else{
			return false;
		}
	});
}
<?php if($_SERVER['HTTP_HOST']=="localhost"){?>
	var hostURL = "http://localhost/fxdev/";
<?php }else{ ?>
	//var hostURL = "http://www.golfingID.com/";
	var hostURL = "http://probuild.constructionid.com/";
<?php }?>
function selectImages(divId, imgID, removeButtonId){
	var imgDiv = document.getElementById(divId);
	var imgSrc = imgDiv.src;	
	showProgress();	
	$.ajax({
		url: "copy_drawing_to_add_defect_file.php",
		type: "POST",
		data: "imageData="+imgSrc+"&imageID="+imgID,
		success: function (res) {
			hideProgress();
			document.getElementById("drawing2").value = res;
			document.getElementById("response_drawing").innerHTML = '<img src="inspections/drawing/'+res+'" width="100" height="90" style="margin-left:10px;margin-top:8px;" id="drawImage1"  /><input type="hidden" value="'+res+'" name="drawing">';
			
			document.getElementById("removeImg3").setAttribute("onclick","removeImages('response_drawing', this.id, 0);")						
			var src = document.getElementById("drawImage1").src;
			document.getElementById("editDrawing").style.display = "block";
			document.getElementById("editDrawing").innerHTML = '<input type="image" src="images/markup.png" value="Edit photo" onclick="return launchEditor(\'drawImage1\', \''+src+'\');"/>';
			$('#removeImg3').show();
			closePopup(300);
			//imgDiv.style.display = 'none';
		}
	});	
}
function showPhotoLibrary(pID, imgID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_defect_drawing_management_show.php?pID='+pID+'&imageID='+imgID, loadingImage, addNewDrawing);
	document.getElementById("outerModalPopupDiv").style.top="550px";
}
function addNewDrawing(){
	var btnUpload2=$('#drawing');
	var status2=$('#response_drawing');
	new AjaxUpload(btnUpload2, {
		action: 'auto_file_upload.php?action=drawing&uniqueID='+Math.random(),
		name: 'drawing',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
				// extension is not allowed 
				status2.text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
			status2.text('Uploading...');
			showProgress();
		},
		onComplete: function(file, response2){
			hideProgress();
			closePopup(300);
			status2.html(response2);
			$('#removeImg3').show();
			
			document.getElementById("removeImg3").setAttribute("onclick","removeImages('response_drawing', this.id, 0);")						
			var src = document.getElementById("drawImage1").src;
			document.getElementById("editDrawing").style.display = "block";
			document.getElementById("editDrawing").innerHTML = '<input type="image" src="images/markup.png" value="Edit photo" onclick="return launchEditor(\'drawImage1\', \''+src+'\');"/>';
			
		}
	});
}
function showPhoto(pID, imgID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_defect_display_draw_image.php?pID='+pID+'&imageID='+imgID, loadingImage);
	document.getElementById("outerModalPopupDiv").style.top="200px";
}
var imageURL='';
var oldImageURL='';
var featherEditor = new Aviary.Feather({
   apiKey: 'YXOfUCoDcEeninMRkHb04w',
   apiVersion: 2,
   tools: 'all',
   appendTo: '',
   onSave: function(imageID, newURL) {
	   var img = document.getElementById(imageID);
	   img.src = newURL;
	   imageURL = newURL;
   },
   onError: function(errorObj) {
	   alert(errorObj.message);
   },
   onClose: function(imageID) {
	   if(imageURL!=''){
			saveImage(imageURL);
	   }
   }
});
function launchEditor(id, src) {
   oldImageURL = src;
   featherEditor.launch({
	   image: id,
	   url: src
   });
  return false;
}
function saveImage(imageURL) {
	showProgress();
	$.ajax({
	   type: "POST",
	   //url: "http://www.golfingID.com/save_aviary.php",
	   url: "http://probuild.constructionid.com/save_aviary.php",		   
	   data: { url: imageURL, oldImageURL: oldImageURL},
	   success: function(msg){ 
		   hideProgress();
			alert(msg);
			
	   }
	});
};   
var temp_id = 1;
$(function(){
	var btnUpload=$('#image1');
	var status=$('#response_image_1');
	new AjaxUpload(btnUpload, {
		action: 'auto_file_upload.php?action=imageOne&uniqueID='+Math.random(),
		name: 'image1',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
				// extension is not allowed 
				status.text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
			status.text('Uploading...');
			showProgress();
		},
		onComplete: function(file, response){
			hideProgress();
			status.html(response);
			$('#removeImg1').show('fast');
						
			document.getElementById("removeImg1").setAttribute("onclick","removeImages('response_image_1', this.id, 0);")			
			var src = document.getElementById("photoImage1").src;
			document.getElementById("editImage1").style.display = "block";
			document.getElementById("editImage1").innerHTML = '<input type="image" src="images/markup.png" value="Edit photo" onclick="return launchEditor(\'photoImage1\', \''+src+'\');"/>';
		}
	});
});
$(function(){
	var btnUpload1=$('#image2');
	var status1=$('#response_image_2');
	new AjaxUpload(btnUpload1, {
		action: 'auto_file_upload.php?action=imageTwo&uniqueID='+Math.random(),
		name: 'image2',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
				// extension is not allowed 
				status1.text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
			status1.text('Uploading...');
			showProgress();
		},
		onComplete: function(file, response1){
			hideProgress();
			status1.html(response1);
			$('#removeImg2').show();
			
			document.getElementById("removeImg2").setAttribute("onclick","removeImages('response_image_2', this.id, 0);")						
			var src = document.getElementById("photoImage2").src;
			document.getElementById("editImage2").style.display = "block";
			document.getElementById("editImage2").innerHTML = '<input type="image" src="images/markup.png" value="Edit photo" onclick="return launchEditor(\'photoImage2\', \''+src+'\');"/>';			
		}
	});
});	
function showHistory(nID, taskID){
	modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'non_conformance_history_table.php', loadingImage, function() {loadData(nID, taskID);});
}
function loadData(nID, taskID){
	$('#example_server').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "non_conformance_ajax_table.php?ncrID="+nID+"&taskID="+taskID,
		"bStateSave": true,
		"bFilter": false,
		"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 1, 2, 3 ] }]
	});
}
function generateDetaildReport(params)
{
	modalPopup(align, top1, 825, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pdf/i_report_non_conformance_detailed.php?'+params, loadingImage);
}
function checkImageValid(){
	var img_valid = $('#is_image_require').val();
	//var img_valid = 1;
	var imgCount = 0;
	$( ".image_qa" ).each(function( index ) {
		if($(this).val()!=''){
			imgCount++;
		}
	});
	if(img_valid==1){
		if(imgCount>=1){
			//$('#add_qa_ncr_task_detail').submit();	
			document.edit_non_confirmance_inspection.submit();
		}else{
			$('#imageError').show();
			return false;
		}	
	}else{
		//$('#add_qa_ncr_task_detail').submit();
		document.edit_non_confirmance_inspection.submit();
	}
	
}

function checkAttachedValid(){
	var attach_valid = $('#is_attachments_required').val();
	//var attach_valid = 1;
	console.log($("#addAttachments" ).children());
	console.log($("#addAttachments" ).children().length);
	var attachCount = $("#addAttachments" ).children().length;
	//var attachCount = 0;
	if(attach_valid==1){
		if(attachCount>=1){
			//$('#add_qa_ncr_task_detail').submit();	
			//document.edit_non_confirmance_inspection.submit();
		}else{
			$('#attachError').show();
			return false;
		}	
	}else{
		//$('#add_qa_ncr_task_detail').submit();
		//document.edit_non_confirmance_inspection.submit();
	}
}
function removeFile(fileName, id, type, main_id, pid){
	var r = jConfirm('Are you sure want to delete ?', null, function(r){
		if (r==true){
			//var imgSrc = "/ncr_task_files/"+fileName;	
			var imgSrc = "inspection/ncr_files/"+fileName;	
			showProgress();	
			$.ajax({
				url: "remove_uploaded_file.php",
				type: "POST",
				data: "imageName="+imgSrc+"&type="+type+"&main_id="+main_id+"&pid="+pid,
				success: function (res) {
					
					hideProgress();
					
					$("#title_"+id).remove();
					$("#saveBox_"+id).remove();
					alert(pid);
					$('.attach_'+pid).remove();
				}
			});
		}else{
			return false;
		}
	});
}	


</script>
<style>
div#outerModalPopupDiv{color:#000000;}
</style>
<?php include'data-table.php'; ?>