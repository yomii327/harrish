<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }

require_once'includes/functions.php';
$obj = new DB_Class();

require_once'includes/commanfunction.php';
$object = new COMMAN_Class();

$offset = isset($_REQUEST['count']) ? $_REQUEST['count'] : 0;
//echo $offset; die;
$limit = 3;
#$builder_id=$_SESSION['ww_builder_id'];


$builder_id = isset($_SESSION['ww_builder_id']) ? $_SESSION['ww_builder_id'] : '';
if($builder_id != ''){
	$where = 'user_id = $builder_id ';
}
$company = isset($_SESSION['ww_is_company']) ? $_SESSION['ww_is_company'] : '' ;
if($builder_id != ''){
	$query = "select DISTINCT project_id, project_name from user_projects where is_deleted=0 and user_id=$builder_id";
}else{
	$query = "select DISTINCT project_id, project_name from projects where is_deleted=0";
}
$noti_record_closed = $obj->db_query($query);
$projIDS = array();
$project_names = array();
$inCaluseArr = array();
while($project_id=$obj->db_fetch_assoc($noti_record_closed)){
	$projIDS[] = $project_id["project_id"];
	$project_names[$project_id["project_id"]] = $project_id["project_name"];
	
	
	$whereConUserRole = "";
	if(!empty($_SESSION['projUserRole'])){
		if($_SESSION['projUserRole'][$projID] != 'All Defect')
			$whereConUserRole = " AND inspection_raised_by = '".$_SESSION['projUserRole'][$projID]."'";
	}
	$inspectionData = $object->selQRYMultiple('GROUP_CONCAT(inspection_id) AS insp, project_id', 'project_inspections', 'is_deleted = 0 AND project_id = ' . $projID . $whereConUserRole);
	
	$inCaluseArr[$projID] = $inspectionData[0]['insp'];
	
}

$inspCondition = "";
if(!empty($inCaluseArr))	$inspCondition = " AND inspection_id IN (".join(",", $inCaluseArr).") ";

$whereConUserRole = "";

switch($_GET['type']){
	case 'location':
		$locData = $object->selQRYMultiple('location_id, location_title', 'project_locations', 'location_parent_id = 0 AND is_deleted = 0 AND project_id = '.$_GET['projID']);

		if(!empty($_SESSION['projUserRole'])){
			if($_SESSION['projUserRole'][$_GET['projID']] != 'All Defect')
				$whereConUserRole = " AND inspection_raised_by = '".$_SESSION['projUserRole'][$_GET['projID']]."'";
		}

		$inspStatusData = $object->getRecordByQuery('SELECT SUM(IF(d.inspection_status= "Open", 1, 0)) AS open, SUM(IF(d.inspection_status="Pending", 1, 0)) AS pending, SUM(IF(d.inspection_status="Fixed", 1, 0)) AS fixed, SUM(IF(d.inspection_status="Closed", 1, 0)) AS closed FROM  project_inspections AS pi inner join (SELECT inspection_status, inspection_id FROM issued_to_for_inspections WHERE project_id = '.$_GET['projID'].' AND is_deleted = 0 GROUP BY inspection_id  ) AS d ON pi.inspection_id = d.inspection_id WHERE pi.project_id = '.$_GET['projID'].' AND pi.inspection_type != "Memo" AND pi.is_deleted = 0'.$whereConUserRole);

		$open = isset($inspStatusData[0]['open']) ? $inspStatusData[0]['open'] : 0;
		$pending = isset($inspStatusData[0]['pending']) ? $inspStatusData[0]['pending'] : 0;
		$fixed = isset($inspStatusData[0]['fixed']) ? $inspStatusData[0]['fixed'] : 0;
		$closed = isset($inspStatusData[0]['closed']) ? $inspStatusData[0]['closed'] : 0;
		$total = $open + $pending + $fixed + $closed;
	break;

	case 'issuedTo':
		$inspStatusData = $object->getRecordByQuery('SELECT SUM(IF(d.inspection_status= "Open", 1, 0)) AS open, SUM(IF(d.inspection_status="Pending", 1, 0)) AS pending, SUM(IF(d.inspection_status="Fixed", 1, 0)) AS fixed, SUM(IF(d.inspection_status="Closed", 1, 0)) AS closed FROM  project_inspections AS pi inner join (SELECT inspection_status, inspection_id FROM issued_to_for_inspections WHERE project_id IN ('.join(",", $projIDS).') AND is_deleted = 0 AND issued_to_name = "'.base64_decode($_GET['issuedTo']).'" GROUP BY inspection_id  ) AS d ON pi.inspection_id = d.inspection_id WHERE pi.project_id IN ('.join(",", $projIDS).') AND pi.inspection_type != "Memo" AND pi.is_deleted = 0'.$inspCondition);

		$open = isset($inspStatusData[0]['open']) ? $inspStatusData[0]['open'] : 0;
		$pending = isset($inspStatusData[0]['pending']) ? $inspStatusData[0]['pending'] : 0;
		$fixed = isset($inspStatusData[0]['fixed']) ? $inspStatusData[0]['fixed'] : 0;
		$closed = isset($inspStatusData[0]['closed']) ? $inspStatusData[0]['closed'] : 0;
		$total = $open + $pending + $fixed + $closed;
	break;
	
	
	case 'promon':
		$locData = $object->selQRYMultiple('location_id, location_title', 'project_monitoring_locations', 'location_parent_id = 0 AND is_deleted = 0 AND project_id = '.$_GET['projID']);
		
		$inspStatusData = $object->getRecordByQuery('SELECT SUM( IF( status = "In progress", 1, 0 ) ) AS ahead,SUM( IF( status = "Behind", 1, 0 ) ) AS behind, SUM( IF( status ="On Time", 1, 0 ) ) AS ontime, SUM( IF( status = "Complete", 1, 0 ) ) AS complete, SUM( IF( status = "", 1, 0 ) ) AS nostatus, SUM( IF( status = "Signed off", 1, 0 ) ) AS signedoff FROM `progress_monitoring` WHERE project_id = '.$_GET['projID'].' AND is_deleted = 0');

		$behind = isset($inspStatusData[0]['behind']) ? $inspStatusData[0]['behind'] : 0;
		$ahead = isset($inspStatusData[0]['ahead']) ? $inspStatusData[0]['ahead'] : 0;
		$ontime = isset($inspStatusData[0]['ontime']) ? $inspStatusData[0]['ontime'] : 0;
		$complete = isset($inspStatusData[0]['complete']) ? $inspStatusData[0]['complete'] : 0;
		$signedoff = isset($inspStatusData[0]['signedoff']) ? $inspStatusData[0]['signedoff'] : 0;
		$nostatus = isset($inspStatusData[0]['nostatus']) ? $inspStatusData[0]['nostatus'] : 0;
		$total = $behind + $ahead + $ontime + $complete + $signedoff + $nostatus;
	break;
}
?>
<html>
<head>
<!--[if IE]><script language="javascript" type="text/javascript" src="../dist/excanvas.js"></script><![endif]-->
<link class="include" rel="stylesheet" type="text/css" href="dist/jquery.jqplot.min.css" />
<style type="text/css">
pre{ display:block;font:12px "Courier New", Courier, monospace; padding:10px; border:1px solid #bae2f0; background:#e3f4f9;	 margin:.5em 0; width:674px; }	
.graphic, #prevBtn, #nextBtn{ margin:0; padding:0; display:block; overflow:hidden; text-indent:-8000px; }
.dashboradAnchor{ cursor:pointer; color:#000000; text-decoration:none;z-index:3; }
canvas{z-index:1;}
div.jqplot-xaxis-tick{z-index:2;}
table.collapse { border-collapse: collapse; border: 1pt solid black; }
table.collapse td { border: 1pt solid #C7C6C6; padding: 10px; font-family: Arial, Helvetica, sans-serif; font-size: 17px !important; }
table.collapse th { border: 1pt solid #D6D4D4; padding: 10px; font-family: Arial, Helvetica, sans-serif; font-size: 16px !important; }
.oddDash{background-color:#C0C0C0;}
.evenDash{background-color:#EDEBEB;}
.labelHolder{ list-style:none; margin-left:0; }
.labelHolder li { float:left; width:250px; text-align:left; font-size:15px; font-weight:bold; }
.view_btn{ margin:0; }
div#outerModalPopupDiv{color:#000000;}
.colOpen{ background:#FF1717 !important; }
tr.colOpen td.sorting_1{ background:#FF1717 !important; }
.colDraft{ background:#A9A9A9 !important; }
tr.colDraft td.sorting_1{ background:#A9A9A9 !important; }
.colClosed{ background:#0066CC !important; }
tr.colClosed td.sorting_1{ background:#0066CC !important; }
.colPending{ background:#FFFF00 !important; }
tr.colPending td.sorting_1{ background:#FFFF00 !important; }
.colFixed{ background:#009900 !important; }
tr.colFixed td.sorting_1{ background:#009900 !important; }

td.clickable { cursor:pointer; }
td.clickable:hover { text-decoration:underline; }
</style>
</head>
<body style="margin:0px;padding:0px;">
<div class="first_box" id="full_analysis" style="text-align:center;width:99%;height:400px;">
<?php if($_GET['type'] == 'location'){?>
	<ul class="labelHolder">
		<li style="width:130px;">Project Name:</li>
		<li style="width:230px;"><?=$project_names[$_GET['projID']];?></li>
		<li style="width:150px;">Location Name:</li>
		<li><?php if(!empty($locData)){?>
			<select name="locationName" id="locationName" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);background:#0987c8 url(images/selectSpl.png) no-repeat\9;">
				<option value="">Entire Project</option>
		<?php foreach($locData as $lData){?>
				<option value="<?=$lData['location_id']?>"><?=$lData['location_title']?></option>
		<?php }?>
			</select>
		<?php }?></li>
		<li style="width:150px;">
			<a style="float:right;width:87px;" href="?sect=b_full_analysis">
				<img src="images/back_btn2.png" style="border:none;">
			</a>
		</li>
	</ul><br clear="all" /><hr /><br clear="all" />

	<table width="40%" border="0" class="collapse" id="projectwise_statusData" align="center">
		<tr class="oddDash">
			<th width="30%">Status</th>
			<th width="30%">Value</th>
		</tr>
		<tr class="evenDash" onClick="inspectionsList('Open');">
			<td class="clickable">Open</td>
			<td class="clickable"><?=$open?></td>
		</tr>
		<tr class="oddDash" onClick="inspectionsList('Pending');">
			<td class="clickable">Pending</td>
			<td class="clickable"><?=$pending?></td>
		</tr>
		<tr class="evenDash" onClick="inspectionsList('Fixed');">
			<td class="clickable">Fixed</td>
			<td class="clickable"><?=$fixed?></td>
		</tr>
		<tr class="oddDash" onClick="inspectionsList('Closed');">
			<td class="clickable">Closed</td>
			<td class="clickable"><?=$closed?></td>
		</tr>
		<tr class="evenDash">
			<td><strong>Total</strong></td>
			<td><strong><?=$total?></strong></td>
		</tr>
	</table>
<?php }?>
<?php if($_GET['type'] == 'issuedTo'){?>
	<ul class="labelHolder">
		<li style="width:150px;">Issued To Name:</li>
		<li style="width:210px;"><?=base64_decode($_GET['issuedTo']);?></li>
		<li style="width:140px;">Project Name:</li>
		<li><?php if(!empty($project_names)){?>
			<select name="projectName" id="projectName" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);background:#0987c8 url(images/selectSpl.png) no-repeat\9;">
				<option value="<?=join(",", $projIDS)?>">All</option>
		<?php foreach($project_names as $key=>$value){?>
				<option value="<?=$key?>"><?=$value?></option>
		<?php }?>
			</select>
		<?php }?></li>
		<li style="width:160px;">
			<a style="float:right;width:87px;" href="?sect=b_full_analysis">
				<img src="images/back_btn2.png" style="border:none;">
			</a>
		</li>
	</ul><br clear="all" /><hr /><br clear="all" />

	<table width="40%" border="0" class="collapse" id="projectwise_statusData" align="center">
		<tr class="oddDash">
			<th width="30%">Status</th>
			<th width="30%">Value</th>
		</tr>
		<tr class="evenDash" onClick="inspectionsList('Open');" >
			<td class="clickable">Open</td>
			<td class="clickable"><?=$open?></td>
		</tr>
		<tr class="oddDash" onClick="inspectionsList('Pending');">
			<td class="clickable">Pending</td>
			<td class="clickable"><?=$pending?></td>
		</tr>
		<tr class="evenDash" onClick="inspectionsList('Fixed');">
			<td class="clickable">Fixed</td>
			<td class="clickable"><?=$fixed?></td>
		</tr>
		<tr class="oddDash" onClick="inspectionsList('Closed');">
			<td class="clickable">Closed</td>
			<td class="clickable"><?=$closed?></td>
		</tr>
		<tr class="evenDash">
			<td><strong>Total</strong></td>
			<td><strong><?=$total?></strong></td>
		</tr>
	</table>
<?php }?>
<?php if($_GET['type'] == 'promon'){?>
	<ul class="labelHolder">
		<li style="width:130px;">Project Name:</li>
		<li style="width:230px;"><?=$project_names[$_GET['projID']];?></li>
		<li style="width:150px;">Location Name:</li>
		<li><?php if(!empty($locData)){?>
			<select name="locationName" id="locationName" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);background:#0987c8 url(images/selectSpl.png) no-repeat\9;">
				<option value="">Select</option>
		<?php foreach($locData as $lData){?>
				<option value="<?=$lData['location_id']?>"><?=$lData['location_title']?></option>
		<?php }?>
			</select>
		<?php }?></li>
		<li style="width:150px;">
			<a style="float:right;width:87px;" href="?sect=b_full_analysis">
				<img src="images/back_btn2.png" style="border:none;">
			</a>
		</li>
	</ul><br clear="all" /><hr /><br clear="all" />

	<table width="40%" border="0" class="collapse" id="projectwise_statusData" align="center">
		<tr class="oddDash">
			<th width="30%">Status</th>
			<th width="30%">Value</th>
		</tr>
		<tr class="evenDash" onClick="inspectionsList('Behind');">
			<td class="clickable">Behind</td>
			<td class="clickable"><?=$behind?></td>
		</tr>
		<tr class="oddDash" onClick="inspectionsList('Complete');">
			<td class="clickable">Complete</td>
			<td><?=$complete?></td>
		</tr>
		<tr class="evenDash" onClick="inspectionsList('In progress');">
			<td class="clickable">In progress</td>
			<td class="clickable"><?=$ahead?></td>
		</tr>
		<tr class="oddDash" onClick="inspectionsList('Signed off');">
			<td class="clickable">Signed off</td>
			<td><?=$signedoff?></td>
		</tr>
		<tr class="evenDash" onClick="inspectionsList('Not Started');">
			<td class="clickable">Not Started</td>
			<td class="clickable"><?=$nostatus?></td>
		</tr>
		<tr class="oddDash">
			<td><strong>Total</strong></td>
			<td><strong><?=$total?></strong></td>
		</tr>
	</table>
<?php }?>
</div>
<script class="code" type="text/javascript">
var align = 'center';
var top1 = 100;
var width = 900;
var padding = 10;
var backgroundColor = '#FFFFFF';
var borderColor = '#333333';
var borderWeight = 4;
var borderRadius = 5;
var fadeOutTime = 300;
var disableColor = '#666666';
var disableOpacity = 40;
var loadingImage = 'images/loadingAnimation.gif';
<?php if($_GET['type'] == 'location'){?>
$('#locationName').change(function(){
	showProgress();
	$.post('ajax_data_for_dashborad.php', { projectID:<?=$_GET['projID'];?>,  locID:$(this).val().trim(), antiqueID:Math.random() }).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			$('#projectwise_statusData').html(jsonResult.htmlData);	
		}else{
			jAlert(jsonResult.msg);
		}
	});
});
function inspectionsList(statusType, locID){
	var locationID = typeof locID !== 'undefined' ? locID : "";
	var projectID = <?=$_GET['projID'];?>;
	
	window.location.href = "pms.php?sect=i_defect&frm=dsb&bk=Y&pid="+projectID+"&locid="+locationID+"&sts="+statusType;
}
<?php }?>
<?php if($_GET['type'] == 'issuedTo'){?>
$('#projectName').change(function(){
	var issuedTo = "<?=base64_decode($_GET['issuedTo']);?>";
	showProgress();
	$.post('ajax_data_for_dashborad.php', { issuedTo:issuedTo,  projectID:$(this).val().trim(), uniqueID:Math.random() }).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			$('#projectwise_statusData').html(jsonResult.htmlData);	
		}else{
			jAlert(jsonResult.msg);
		}
	});
});
function inspectionsList(statusType, projID){
	var projectID = typeof projID !== 'undefined' ? projID : "";
	var issuedTo = "<?=base64_decode($_GET['issuedTo']);?>";
	
	window.location.href = "pms.php?sect=i_defect&frm=dsb&bk=Y&pid="+projectID+"&isst="+issuedTo+"&sts="+statusType;
}
<?php }?>
<?php if($_GET['type'] == 'promon'){?>
$('#locationName').change(function(){
	showProgress();
	$.post('ajax_data_for_dashborad.php', { projectID:<?=$_GET['projID'];?>,  locID:$(this).val().trim(), singleID:Math.random() }).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			$('#projectwise_statusData').html(jsonResult.htmlData);	
		}else{
			jAlert(jsonResult.msg);
		}
	});
});
function inspectionsList(statusType){
	var locationID = ($('#locationName').val() != "") ? $('#locationName').val().trim() : "";
	var projectID = <?=$_GET['projID'];?>;
	showProgress();
	$.post('set_session_for_dashboard.php', { projName:projectID, location:locationID, status:statusType, singleID:Math.random() }).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);
		if(jsonResult.status){
			window.location.href = "pms.php?sect=i_defect&bk=Y";
		}else{
			jAlert(jsonResult.msg);
		}
	});
}
<?php }?>
</script>
</body>
</html>