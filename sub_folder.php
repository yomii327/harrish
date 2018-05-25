<?php 
session_start();
if(isset($_GET['id']) || !empty($_GET['id'])){
	$_SESSION['idp'] = base64_decode($_GET['id']);
	setcookie('pmb_'.$_SESSION['ww_builder_id'], $_GET['id'], time()+864000);
}
$projectId = isset($_SESSION['idp'])?$_SESSION['idp']:0;
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$userId = $_SESSION['ww_builder']['user_id'];
$permArray = array('General Consultant', 'Architect', 'Structural Engineer', 'Services Engineer', 'Sub Contractor', 'Client');
$permArrayTwo = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Sub Contractor');


function inQueryData($str){
	return '"'.$str.'"';
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

if (isset($_REQUEST['action']) && $_REQUEST['action']=='delete') {
    $thread_id = $_REQUEST['thread_id'];
	$to_id = $_SESSION['ww_builder_id'];
	$deleteQRY = "UPDATE pmb_user_message SET is_deleted = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE thread_id = '".$thread_id."' AND user_id = '".$to_id."'";
	mysql_query($deleteQRY);

	if($_REQUEST['type'] == 'insp')
		header('location:?sect=pmb_sub_folder&folderType=Request For Information');
	else
		header('location:?sect=pmb_sub_folder&folderType=Request For Information');
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
}?>
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
table.collapse { border-collapse: collapse; border: 1pt solid black; }
table.collapse tr, table.collapse td { border: 1pt solid black; padding: 2px; font-family:Arial, Helvetica, sans-serif; font-size:10px;}
#inboxDataRequest4info a{color:#000;}
</style>
<script type="text/javascript">
$(document).ready(function() {
	oTable = $('#inboxData').dataTable({
		"bJQueryUI": true,
		"bStateSave": false,
		/*"aaSorting": [ [3,'desc'] ],*/
		"iDisplayLength": 100,
		"sPaginationType": "full_numbers",
		"aoColumns": [
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
			{"sType": "html"},
<?php }?>
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"}
		]
	});
} );
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
	            <li>
					<a href="#" onclick="deleteSelected('inbox');" class="sideMenu mail">
						<i class="delete"></i>
						<span>Delete</span>
					</a>
				</li>
			<?php }?>
			<?php if(!in_array($_SESSION['userRole'], $permArrayTwo)){?>
                <li><a href="?sect=compose&amp;folderType=<?=$_GET['folderType'];?>" class="sideMenu mail">
						<i class="compose"></i>
						<span>Compose</span>
					</a>
				</li>
			<?php }?>
			<?php if(isset($_GET['folderType']) && $_GET['folderType'] != ""){?>
                <li>
						<!-- <img src="images/generate_reppmb_ort.png" alt="Generate Report" onClick="generateReport('<?=$_GET['folderType']?>');" style="cursor:pointer;" /> -->
						<a class="green_small" href="javascript:void(0)" onclick="generateReport('<?=$_GET['folderType']?>');" style="cursor:pointer; padding: 6px 10px;" alt="Generate Report" />Generate Report</a>
					</li>
			<?php }?>
				<li style="color:#000;">RFI Status:
					<select name="rfiStatus" id="rfiStatus" class="select_box" style="margin:0px 6px 4px 0px; width:120px;background-image:url(images/input_120.png);">
						<?php $rfiStatusArr = array('All', 'Open', 'Closed');
						foreach($rfiStatusArr as $key=>$rfiStatusVal){?>
							<option value="<?=$rfiStatusVal;?>"><?=$rfiStatusVal;?></option>
						<?php }?>
					</select>
				</li>
			</ul>
			<h3 style="color:#000000; margin-top:10px; margin-right:30px; float:right;">Project Name : <?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></h3>
		</div>
		<div id="dataTableRFI">
        <form name="inbox" action="" method="post" id="inbox">
		<?php if(isset($_GET['folderType']) && $_GET['folderType'] != "")
				echo '<h3 style="color:#000;padding-left:15px;">'.$_GET['folderType'].'</h3>';?>
			<table cellpadding="0" cellspacing="0" border="0" class="display" id="inboxDataRequest4info" width="100%">
				<thead>
					<tr>
						<th width="2%"><input name="allbox" type="checkbox" value="" onClick="checkedAll('inbox');" /></th>
						<th width="5%">RFI #</th>
						<th>RFI Description</th>
						<th width="15%">To</th>
						<th width="15%">CC</th>
						<th width="5%">Time</th>						
						<th width="5%">RFI Status</th>
                        <?php if(!in_array($_SESSION['userRole'], $permArray)){?>
                        	<th width="5%">Action</th>
						<?php }?>
					</tr>
				</thead>
                <tbody>
	                <tr>
						<td colspan="8">Fetching Data form server</td>
					</tr>
                </tbody>
			</table>
			<input type="hidden" name="form_type" value="inbox">
		</form>
		</div>
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

checked=false;

function delMessage (id) {
	var r = jConfirm('Are you sure want to delete this message?', null, function(r){ if(r==true){ window.location = '?sect=pmb_sub_folder&thread_id='+id+'&action=delete'; } });
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
		}
	}else{
		alert('Please select atleast one record');
		return false;
	}
}
 
function printDiv(){
	var divToPrint = document.getElementById('mainContainer'); 
//	var newWin = window.open('', 'PrintWindow', '', false);
	var newWin = window.open('', 'PrintWindow', '', false); 
	newWin.document.open(); 
	newWin.document.write('<html><head><meta http-equiv="X-UA-Compatible" content="IE=8"><style>body { size:A4-landscape; }</style></head><body onload="window.print();"><style type="text/css">table.collapse { border-collapse: collapse; border: 1pt solid black; }table.collapse td { border: 1pt solid black; padding: 2px; }.pagination{display:none;}.buttonDiv{display:none;}.footer{display:block;}</style>'+divToPrint.innerHTML+'</body></html>'); 
	newWin.document.close(); 
//	setTimeout(function(){newWin.close();},10000); 
}

function downloadPDF(){
	var messageType = '<?=$_GET['folderType']?>';
	var status = $('#rfiStatus1').val();
	var keyword = $("#searchKey").val(); 
	showProgress();
	$.get("pdf/pmb_rfi_extra_attachment.php", {messageType:messageType, status:status, keyword: keyword, name:Math.random()}).done(function(data) {
		hideProgress();
		$('#mainContainer').html(data);
	});	
}

function generateReport(messageType){
	console.log(messageType);
	var status = ''; 
	//modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pdf/pmb_rfi_extra_attachment.php?messageType='+messageType+'&name='+Math.random(), loadingImage);
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pmb_rfi_extra_attachment.php?messageType='+messageType+"&status="+status+'&name='+Math.random(), loadingImage);
}

function generateReportFilter(messageType, status){
	console.log(messageType);
	var keyword = $("#searchKey").val(); 
	var status = $("#rfiStatus1").val(); 
	//modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pdf/pmb_rfi_extra_attachment.php?messageType='+messageType+'&name='+Math.random(), loadingImage);
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pmb_rfi_extra_attachment.php?messageType='+messageType+"&status="+status+'&keyword='+keyword+'&name='+Math.random(), loadingImage);
}

$(document).ready(function(){
	var oTable = $('#inboxDataRequest4info').dataTable( {
		"iDisplayLength": 100,
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "show_inbox_data_by_ajax.php?folderType=<?php echo (isset($_GET['folderType']) && $_GET['folderType'] != "") ? $_GET['folderType'] : '';?>&req4inform="+Math.random(),
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
	<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
			{"sType": "html"},
	<?php }?>
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"}
		]
	} );
});
$("#rfiStatus").change(function (){
	var rfiStatus = $(this).val().trim();
	$('#dataTableRFI').html('');
	var tableHTMLCont = '<table cellpadding="0" cellspacing="0" border="0" class="display" id="inboxDataRequest4info" width="100%"><thead><tr><th width="2%"><input name="allbox" type="checkbox" value="" onClick="checkedAll(\'inbox\');" /></th><th width="5%">RFI #</th><th>RFI Description</th><th width="15%">To</th><th width="15%">CC</th><th width="5%">Time</th><th width="5%">RFI Status</th>';
	<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
    	tableHTMLCont += '<th width="5%">Action</th>';
	<?php }?>
	tableHTMLCont += '</tr></thead><tbody><tr><td colspan="8">Fetching Data form server</td></tr></tbody></table>';
	$('#dataTableRFI').html(tableHTMLCont);
	try{
		console.log('We are here');
		var oTable = $('#inboxDataRequest4info').dataTable({
			"iDisplayLength": 100,
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bProcessing": true,
			"bServerSide": true,
			"bRetrieve": true,
			"sAjaxSource": "show_inbox_data_by_ajax.php?folderType=<?php echo (isset($_GET['folderType']) && $_GET['folderType'] != "") ? $_GET['folderType'] : '';?>&rfiStatus="+rfiStatus+"&req4inform="+Math.random(),
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
		<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
				{"sType": "html"},
		<?php }?>
				{"sType": "html"},
				{"sType": "html"},
				{"sType": "html"}
			]
		});		
	}catch(e){
		console.log(e);	
	}
});
function getData(rfiStatus){
	var messageType = 'Request For Information';
	//modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pdf/pmb_rfi_extra_attachment.php?messageType='+messageType+'&name='+Math.random(), loadingImage);
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pmb_rfi_extra_attachment.php?messageType='+messageType+'&name='+Math.random()+'&status='+rfiStatus, loadingImage);

}
</script>
