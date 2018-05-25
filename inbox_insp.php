<?php 
$userId = $_SESSION['ww_builder']['user_id'];
session_start();
if(isset($_GET['id']) || !empty($_GET['id'])){
	$_SESSION['idp'] = base64_decode($_GET['id']);
	setcookie('pmb_'.$_SESSION['ww_builder_id'], $_GET['id'], time()+864000);
}
//New Added code for project dropdown Start here
if(isset($_POST['projName']) && !empty($_POST['projName'])){
	$_SESSION['idp'] = $_POST['projName'];
}
$q = "SELECT project_id, project_name FROM user_projects WHERE user_id = '".$_SESSION['ww_builder_id']."' and is_deleted = 0 GROUP BY project_name";
$res = mysql_query($q);
$prIDArr = array();
$outPutStr = "";
while($q1 = mysql_fetch_array($res)){
	if(!isset($_SESSION['idp']))
		$_SESSION['idp'] = $q1[0];
	$selectBox = '<option value="'.$q1[0].'"';
	$prIDArr[$q1[0]] = $q1[1];
	if(isset($_SESSION['idp']) && $_SESSION['idp'] != ""){
		if($_SESSION['idp'] == $q1[0]){
			$selectBox .= 'selected="selected"';
		}
	}	
	$selectBox .= '>'.$q1[1].'</option>';
	$outPutStr .= $selectBox;
}
//New Added code for project dropdown End here

$projectId = isset($_SESSION['idp'])?$_SESSION['idp']:0;

include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$permArray = array('General Consultant', 'Architect', 'Structural Engineer', 'Services Engineer', 'Sub Contractor', 'Client');

$permArrayTwo = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Sub Contractor');
function getrow($id){
	$sql="select count(*) as num from pmb_message where from_user='".$id."' AND to_user='".$_SESSION['ww_builder_id']."' AND inbox_read=0 AND deleted = 0 ";
	$result=mysql_query($sql);
	$row=mysql_fetch_array($result);
	return $row['num'];
}

function unreadCount($id){
	$sql="select count(*) as num from pmb_user_message where thread_id='".$id."' AND user_id='".$_SESSION['ww_builder_id']."' AND type='inbox' AND inbox_read=0";
	$result=mysql_query($sql);
	$row=mysql_fetch_array($result);
	return $row['num'];
}

function threadCount($id){
	$sql = "select count(*) as num from pmb_user_message where type = 'inbox' AND thread_id='".$id."' AND user_id = '".$_SESSION['ww_builder_id']."' AND is_deleted='0' group by message_id";
	$result=mysql_query($sql);
	$row=mysql_fetch_array($result);
	//return $row['num'];
	return mysql_num_rows($result);
}

function getUserNameByEmailids($emailIDs){
	$tempArr = explode(',', str_replace(", ", ",", $emailIDs));
	array_walk($tempArr, 'inQueryData');
//	print_r($tempArr);
	$nameDataArr = array();
	$sql = "SELECT
						ab.full_name as name,
						user_email as email
					FROM
						pmb_address_book as ab
					where
						user_email IN ('". join("','", $tempArr) ."')
			UNION
					SELECT
						iss.company_name as name,
						iss.issue_to_email as email
					FROM
						inspection_issue_to as iss
					WHERE
						issue_to_email IN ('". join("','", $tempArr) ."')";
	
	$result = mysql_query($sql);

	while($row = mysql_fetch_array($result)){
		$nameDataArr[] = $row['name'];
	}
	return join(", ", $nameDataArr);
}


if (isset($_REQUEST['action']) && $_REQUEST['action']=='delete') {
    $thread_id = $_REQUEST['thread_id'];
	$to_id = $_SESSION['ww_builder_id'];
	mysql_query("UPDATE pmb_user_message SET is_deleted = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE thread_id = '".$thread_id."' AND user_id = '".$to_id."'");
    header('location:?sect=messages');
}

if (isset($_REQUEST['form_type']) && $_REQUEST['form_type']=='inbox') {
    $to_id = $_SESSION['ww_builder_id']; 
	if(sizeof($_REQUEST['from'])>0) {
	for($i=0;$i<sizeof($_REQUEST['from']);$i++) {
	   $thread_id = $_REQUEST['from'][$i];
	  mysql_query("UPDATE pmb_user_message SET is_deleted = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE thread_id = '".$thread_id."' AND user_id = '".$to_id."'");
       header('location:?sect=messages');
	  }
	}
}

function getuserdetails($id){
  $req1 = mysql_query('select user_id, user_name, user_fullname from user where user_id="'.$id.'"');
   $row=mysql_fetch_array($req1);
   return $row;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php'; ?>
<link href="css/email.css" rel="stylesheet" type="text/css" />
<style>
.dataTables_wrapper{ clear: both; margin-left: 10px; min-height: 302px; position: relative; width: 98%; }
.sorting_1{ padding-left:26px !important; }
tr.gradeA td{ line-height:30px; }
tr.gradeA td a{ display:block; color:#000; }
table.display tr.odd.gradeA{ background-color:#CCCCCC; }
tr.odd.gradeA td.sorting_1{ background-color:#CCCCCC; }
table.display tr.even.gradeA{ background-color:#EAEAEA; }
tr.even.gradeA td.sorting_1{ background-color:#EAEAEA; }
#inboxData a{color:#000;}
</style>
<script type="text/javascript">
$(document).ready(function() {
	oTable = $('#inboxData2').dataTable({
		"bJQueryUI": true,
		"bStateSave": false,
		//"aaSorting": [ [3,'desc'] ],
		"sPaginationType": "full_numbers",
		"iDisplayLength": 100,
		"aoColumns": [
			{"sType": "html"},
<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
			{"sType": "html"},
<?php }?>
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"}
		]
	});
} );
	
checked=false;

function delMessage(id) {
	var r = jConfirm('Are you sure want to delete this message?', null, function(r){ if(r==true){ window.location = '?sect=messages&thread_id='+id+'&action=delete&type=insp'; } });
/*	if (confirm("Are you sure want to delete this?")) {
	   location.href='?sect=messages&thread_id='+id+'&action=delete';
	 } else { 
	 } 
	 */
}
function checkedAll (frm1) {
	
	var aa= document.getElementById(frm1);
	 if (checked == false) {
           checked = true
      } else {
           checked = false
      }
	for (var i =0; i < aa.elements.length; i++) {
	    aa.elements[i].checked = checked;
	  }
 }
 
  function deleteSelected (frm) {
     var aa= document.getElementById(frm);
	 totalChecked=0;
	 for (var i =0; i < aa.elements.length; i++) {
	    var e = aa.elements[i];
	    if ((e.name != 'allbox') && (e.type=='checkbox')) {
	    if(eval(aa.elements[i].checked) == true) {
           totalChecked=totalChecked+1;
		 }  
		}
	  }
	  
	  if(totalChecked>0) {
	    if (confirm("Are you sure want to delete this?")) {
	       document.getElementById(frm).submit();
		   return true;
	     } else { } 
       } else {
	     alert('Please select atleast one record');
	     return false;
	   }
 }
 
 </script>
</head>
<body id="dt_example">
<div class="demo_jui" style="width:100%; float:left;" >
<div class="GlobalContainer clearfix">
	<?php include 'message_side_menu.php'; ?>
	<div class="MailRight">
		<div class="MailRightHeader">
			<ul style="margin-left:-28px !important;">
			<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
				<li><a href="#" onClick="deleteSelected('inbox');"><img src="images/delete1.png" width="93" height="34" alt="Delete" /></a></li>
			<?php }?>
			<?php if(isset($_GET['folderType']) && $_GET['folderType'] != ""){?>
					<li><img src="images/generate_reppmb_ort.png" alt="Generate Report" onClick="generateReport('<?=$_GET['folderType']?>');" style="cursor:pointer;" /></li>
			<?php }?>
            <li style="padding-top:5px;width:290px;"><span style="color:#000000; font-size:14px; font-weight:bold;">Project Name : <?php echo $projectName = $prIDArr[$_SESSION['idp']];?></span></li>
            <li><form action="" name="projForm" id="projForm" method="post">
	            <select name="projName" id="projName"  class="select_box" onChange="startAjax(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
            	<?php echo $outPutStr;?>
            </select>
            </form></li>
			</ul>
			<!--<h3 style="color:#000000; margin-top:10px; margin-right:30px; float:right;">Project Name : <?php #echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></h3>-->
		</div><br clear="all">
		<form action="" method="post" name="defectSearch" id="defectSearch">
		<table width="" cellpadding="0" cellspacing="5" border="0">
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="color:#000;">Location </td>
				<td colspan="2" id="ShowLocation" align="right"><select name="location" id="location"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);" onChange="subLocate(this.value);">
					<option value="">Select</option>
					<?php $q = "SELECT location_id, location_title FROM project_locations WHERE project_id = '".$_SESSION['idp']."' AND is_deleted = 0 AND location_parent_id = 0 GROUP BY location_title";
					$res = mysql_query($q);
					$prIDArr = array();
					$outPutStr = "";
					while($q1 = mysql_fetch_array($res)){
						$selectBox = '<option value="'.$q1[0].'"';
						$selectBox .= '>'.$q1[1].'</option>';
						$outPutStr .= $selectBox;
					}
					echo $outPutStr;?>
				</select></td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#000;">Sub Location</td>
				<td colspan="2" align="right" id="ShowSubLocation"><select name="subLocation" id="subLocation"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
				</select></td>
			</tr>
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="color:#000;">Sub Location 1</td>
				<td colspan="2" align="right" id="SubShowSubLocation"><select name="subSubLocation" id="subSubLocation"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
				</select></td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#000;">Status</td>
				<td colspan="2" align="right"><select name="status" id="status" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
					<?php $statusArr = array('Open', 'Pending', 'Fixed', 'Closed');
						foreach($statusArr as $key=>$statusVal){?>
							<option value="<?=$statusVal?>" <?php if(isset($_SESSION['qc']['status'])){ if($_SESSION['qc']['status'] == $statusVal){ echo 'selected="selected"'; }}?>><?=$statusVal?></option>
					<?php } ?>
				</select></td>
			</tr>
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="color:#000;">Inspected By</td>
				<td colspan="2" id="ShowInspecrBy" align="right"><select name="inspectedBy" id="inspectedBy" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
				</select></td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#000;">Issued To</td>
				<td colspan="2" id="ShowIssuedTo" align="right"><select name="issuedTo" id="issuedTo" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
				</select></td>
			</tr>
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="color:#000;">Inspection Type</td>
				<td colspan="2" id="ShowPriority" align="right"><select name="inspecrType" id="inspecrType" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
					<?php $inspTypeArr = array('Issue', 'Defect', 'Warranty', 'Incomplete Works', 'Purchase Changes');
						foreach($inspTypeArr as $key=>$inspTypeVal){?>
							<option value="<?=$inspTypeVal?>" <?php if(isset($_SESSION['qc']['inspecrType'])){ if($_SESSION['qc']['inspecrType'] == $inspTypeVal){ echo 'selected="selected"'; }}?>><?=$inspTypeVal?></option>
					<?php } ?>
				</select></td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#000;">Cost Attribute</td>
				<td colspan="2" align="right"><select name="costAttribute" id="costAttribute" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<option value="">Select</option>
					<?php $inspTypeArr = array('None', 'Backcharge', 'Variation');
						foreach($inspTypeArr as $key=>$inspTypeVal){?>
							<option value="<?=$inspTypeVal?>" <?php if(isset($_SESSION['qc']['costAttribute'])){ if($_SESSION['qc']['costAttribute'] == $inspTypeVal){ echo 'selected="selected"'; }}?>><?=$inspTypeVal?></option>
					<?php } ?>
				</select></td>
			</tr>
			<tr>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#000;">Raised By</td>
				<td colspan="2" id="ShowRaisedBy"  align="right">
					<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
						<option value="">Select</option>
					</select>
				</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#000;">&nbsp;</td>
				<td align="left" valign="middle" nowrap="nowrap" style="color:#000;">&nbsp;</td>
			</tr>
			<tr>
				<td align="left" valign="top" nowrap="nowrap" style="color:#000;">Date Raised</td>
				<td colspan="2" align="left" nowrap="nowrap" style="color:#000;">From 
					<input name="DRF" type="text" size="7" id="DRF" readonly value="<?php if(isset($_SESSION['qc']['DRF'])){ echo $_SESSION['qc']['DRF']; }?>" />
				To 
					<input name="DRT" type="text" id="DRT" size="7" readonly value="<?php if(isset($_SESSION['qc']['DRT'])){ echo $_SESSION['qc']['DRT']; }?>" />
					<a href="javascript:void();" title="Clear date raised"><img src="images/redCross.png" onClick="clearDateRaised();" /></a>
				</td>
				<td align="left" valign="top" nowrap="nowrap" style="color:#000;">Fix By Date</td>
				<td colspan="2" align="left" nowrap="nowrap" style="color:#000;">From 
					<input name="FBDF" type="text" id="FBDF" size="7" readonly value="<?php if(isset($_SESSION['qc']['FBDF'])){ echo $_SESSION['qc']['FBDF']; }?>" />
				To
					<input name="FBDT" type="text" id="FBDT" size="7" readonly value="<?php if(isset($_SESSION['qc']['FBDT'])){ echo $_SESSION['qc']['FBDT']; }?>" />
					<a href="javascript:void();" title="Clear fixed by date"><img src="images/redCross.png" onClick="clearFixedByDate();" /></a>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="left">&nbsp;
				
				</td>
				<td>&nbsp;</td>
				<td  colspan="2" align="right">
					<input name="SearchInsp" type="button" onClick="submitForm(false);" class="submit_btn" id="button" value="" style="background-image:url(images/search_btn_web.png); width:113px; height:46px;"  />
					<input type="hidden" name="sessionBack" id="sessionBack" value="Y" />
				</td>
			</tr>
		</table>
		</form>
		<?php $spCon = "um.type = 'inbox' AND um.inbox_read = 0 AND ";
		if(isset($_GET['folderType'])){
			$spCon = "m.message_type = '".$_GET['folderType']."' AND (um.type = 'inbox'  OR um.type = 'sent') AND ";
		}
		$orderBy = "";
		if($orderBy == ""){
			$orderBy = 'm.sent_time DESC';
		}?>
		<form name="inbox" action="" method="post" id="inbox">
		<?php if(isset($_GET['folderType']) && $_GET['folderType'] != "")
				echo '<h3 style="color:#000;padding-left:15px;">'.$_GET['folderType'].'</h3>';?>
			<div id="messageBoxHolder">
			<table cellpadding="0" cellspacing="0" border="0" class="display" id="inboxData" width="100%">
				<thead>
					<tr>
						<th width="2%"><input name="allbox" type="checkbox" value="" onClick="checkedAll('inbox');" /></th>
					<?php if(!isset($_GET['folderType'])){?>
						<th width="5%">From</th>
					<?php }else{?>
						<th width="5%">Issued To</th>
						<th width="5%">Location</th>
					<?php }?>	
						<th width="50%">Description</th>
					<?php if(!isset($_GET['folderType'])){?>
						<th>Message Type</th>
					<?php }?>	
						<th width="5%">Time</th>
					<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
						<th width="5%">Action</th>
					<?php }?>	
					</tr>
				</thead>
			</table>
			</div>
			<input type="hidden" name="form_type" value="inbox">
		</form>
	</div>
</div>
<style>
div.content_container{ width:100% !important; }
div.innerModalPopupDiv{	color:#000000; }
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

function generateReport(messageType){
	console.log(messageType);
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pdf/pmb_message_summary_report.php?messageType='+messageType+'&name='+Math.random(), loadingImage);
}

var oTable = $('#inboxData').dataTable( {
	"iDisplayLength": 100,
	"bJQueryUI": true,
	"sPaginationType": "full_numbers",
	"bProcessing": true,
	"bServerSide": true,
	"bRetrieve": true,
	"sAjaxSource": "show_inbox_data_by_ajax_insp.php?&name="+Math.random()+"&folderType=<?php echo (isset($_GET['folderType']) && $_GET['folderType'] != "")?$_GET['folderType']:''; ?>",
	"bStateSave": true,
<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
	"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 5] }],
<?php }else{?>
	"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 4] }],
<?php }?>
	"aoColumns": [
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
			{"sType": "html"}
<?php }?>
		]
} );
//oTable.fnSort( [ [1,'asc'] ] );
$('#projName').change(function(){ $('#projForm').submit(); });
var urlData = '';

function submitForm(){
try{
	var projName = <?=$_SESSION['idp'];?>;
	var location = document.getElementById('location').value;
	var subLocation = document.getElementById('subLocation').value;
	var subSubLocation = document.getElementById('subSubLocation').value;
	var status = document.getElementById('status').value;
	var inspectedBy = document.getElementById('inspectedBy').value;
	var issuedTo = document.getElementById('issuedTo').value;
	//var priority = document.getElementById('priority').value;
	var inspecrType = document.getElementById('inspecrType').value;
	var costAttribute = document.getElementById('costAttribute').value;
	var raisedBy = document.getElementById('raisedBy').value;
	var DRF = document.getElementById('DRF').value;
	var DRT = document.getElementById('DRT').value;
	var FBDF = document.getElementById('FBDF').value;
	var FBDT = document.getElementById('FBDT').value;

	var dateChackRaised = checkDates(document.getElementById('DRF'), document.getElementById('DRT'), 'In Date Raised Field');
	var dateChackFixed = checkDates(document.getElementById('FBDF'), document.getElementById('FBDT'), 'In Fix By Date Field');
	if(dateChackRaised === false){	return false;	}
	if(dateChackFixed === false){	return false;	}

	if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
//	showProgress();

	var params = "projName="+projName+"&location="+location+"&subLocation="+subLocation+"&subSubLocation="+subSubLocation+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&raisedBy="+raisedBy+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&SearchReq=Y&name="+Math.random();
	
	var htmlContent = '<table cellpadding="0" cellspacing="0" border="0" class="display" id="inboxData" width="100%"><thead><tr><th width="2%"><input name="allbox" type="checkbox" value="" onClick="checkedAll(\'inbox\');" /></th><?php if(!isset($_GET['folderType'])){?><th width="5%">From</th><?php }else{?><th width="5%">Issued To</th><th width="5%">Location</th><?php }?><th width="50%">Description</th><?php if(!isset($_GET['folderType'])){?><th>Message Type</th><?php }?><th width="5%">Time</th><?php if(!in_array($_SESSION['userRole'], $permArray)){?><th width="5%">Action</th><?php }?></tr></thead></table>';
	
	$('#messageBoxHolder').html(htmlContent);
	
	oTable = $("#inboxData").dataTable({
		"iDisplayLength": 100,
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "show_inbox_data_by_ajax_insp.php?"+params+"&name="+Math.random()+"&folderType=<?php echo (isset($_GET['folderType']) && $_GET['folderType'] != "")?$_GET['folderType']:''; ?>",
		"bStateSave": true,
	<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
		"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 5] }],
	<?php }else{?>
		"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 4] }],
	<?php }?>
		"aoColumns": [ {"sType": "html"}, {"sType": "html"}, {"sType": "html"}, {"sType": "html"}, {"sType": "html"},
	<?php if(!in_array($_SESSION['userRole'], $permArray)){?> {"sType": "html"} <?php }?> ]
	});
}catch(e){
	alert(e.message); 
}
}
function startAjax(val){
	AjaxShow("POST","ajaxFunctions.php?type=location && proID="+val,"ShowLocation");
	AjaxShow("POST","ajaxFunctions.php?type=inspecrBy && proID="+val,"ShowInspecrBy");
	AjaxShow("POST","ajaxFunctions.php?type=issuedToQC && proID="+val,"ShowIssuedTo");
	AjaxShow("POST","ajaxFunctions.php?type=sessions&& proID="+val,"setSession");
	AjaxShow("POST","ajaxFunctions.php?type=session&& proID="+val,"setSession");
	AjaxShow("POST","ajaxFunctions.php?type=userRole&& proID="+val,"userRole");
	AjaxShow("POST","ajaxFunctions.php?type=raisedBy&& proID="+val,"ShowRaisedBy");
}
function subLocate(obj){
	AjaxShow("POST","ajaxFunctions.php?type=subLocation && proID="+obj,"ShowSubLocation");
}
function sub_subLocate(obj){
	AjaxShow("POST","ajaxFunctions.php?type=sub_subLocation_qc && proID="+obj,"SubShowSubLocation");
}
function setDropValue(val){
	//AjaxShow("POST","ajaxFunctions.php?type=listDropVal&& proID="+val,"listDropVal");
}
function AjaxShow(method,file,retult){
	var xmlhttp;
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}else{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			document.getElementById(retult).innerHTML=xmlhttp.responseText;
		}
	}
	xmlhttp.open(method,file,true);
	xmlhttp.send();
}

function clearDateRaised(){
	$('#DRF, #DRT').val('');
}
function clearFixedByDate(){
	$('#FBDF, #FBDT').val('');
}
</script>
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"DRF",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"DRT",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"FBDF",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"FBDT",
			dateFormat:"%d-%m-%Y"
		});
	};

function checkDates(date1, date2, element){
	var obj = date1.value;
	var obj1 =  date2.value;
	if(obj!='' || obj1!=''){
		if(obj=='' && obj1!=''){
			jAlert('Please Select Form Date First !');
			return false;		
		}else{
			var fromDate = new Date(obj.substr(6,4), obj.substr(3,2), obj.substr(0,2));
			var toDate = new Date(obj1.substr(6,4), obj1.substr(3,2), obj1.substr(0,2));
			if((toDate.getTime() - fromDate.getTime()) < 0){jAlert(element+' To Date in Not Less Than Form Date !');return false;}
		}
	}
}
</script>