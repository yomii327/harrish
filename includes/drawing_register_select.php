<?php session_start(); 
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link href="css/email.css" rel="stylesheet" type="text/css" />
<style>
	.dataTables_wrapper{ clear: both; margin-left: 10px; min-height: 302px; position: relative; width: 98%; }
	.sorting_1{ padding-left:26px !important; }
	tr.gradeA td{ line-height:30px; }
	tr.gradeA td a{ display:block; color:#000; }
	table.display tr.odd.gradeA{ background-color:#ece5e5; }
	tr.odd.gradeA td.sorting_1{ background-color:#ece5e5; }
	table.display tr.even.gradeA{ background-color:#f5f4f4; }
	tr.even.gradeA td.sorting_1{ background-color:#f5f4f4; }
	.action{ cursor: pointer; }	
</style>
<script type="text/javascript" src="js/page_dr.js?version=<?=$js_version?>"></script>
<?php 
include'data-table.php'; 
include_once("commanfunction.php");

$object = new COMMAN_Class();  ?>
<div class="GlobalContainer clearfix">
	<?php include 'message_side_menu.php'; ?>
	<div class="MailRight">
		<div class="MailRightHeader">
			<h2 style="color:#000000; margin-top:10px; margin-left:10px; float:left;">Select document to attach in message</h2>
			<h3 style="color:#000000; margin-top:15px; margin-right:20px; float:right;">Project Name : <?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></h3>
		</div>
		<div class="clearfix" style="color:#050505;">
			<?php $comMessData = array();
			$toList = array();
			$ccList = array();
			if(isset($_GET['msgid']) && $_GET['msgid']!=0){
				$comMessData = $object->selQRYMultiple('m.message_id, um.user_id, um.type, m.title, m.message_id, m.sent_time, m.message, um.from_id, m.message_type, m.to_email_address, m.cc_email_address', 'pmb_user_message um , pmb_message m', 'm.message_id="'.$_GET['msgid'].'" AND um.message_id = m.message_id AND um.type="sent"');
			
				foreach($comMessData as $val){
					$toList[] = $val['from_id'];
				}
				if(!empty($comMessData[0]['to_email_address'])){
					if(explode(',', $comMessData[0]['to_email_address'])){
						$toExtraList = explode(', ', $comMessData[0]['to_email_address']);
					}else{
						$toExtraList[] = $comMessData[0]['to_email_address'];
					}
					$toList = array_merge($toList, $toExtraList);
				}
				if(!empty($comMessData[0]['cc_email_address'])){
					if(explode(',', $comMessData[0]['cc_email_address'])){
						$ccList = explode(', ', $comMessData[0]['cc_email_address']);
					}else{
						$ccList[] = $comMessData[0]['cc_email_address'];
					}
				}	
			
			}?>
			<ul id="selectedFileHolder"></ul>
			<img src="images/done.png" onClick="completeAttachment();" style="float:right;margin-right:60px; cursor:pointer;"/>
	    	<div id="searchDraw" style="padding:5px;">
				<table width="100%" border="0">
					<tr>
						<td style="color:#000000;padding-left:10px;" >Attribute 1</td>
						<td style="color:#000000;padding-left:10px;">Attribute 2</td>
						<td style="color:#000000;padding-left:10px;">Search Keyword</td>
					</tr>
					<tr>
						<td>
						<select name="drawingattribute1" id="drawingattribute1Default" class="select_box" style="width: 160px;
background-image: url(images/input_160.png);margin-left:0px;"  />
							<option value="">Select</option>
						<?php $attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Subcontractor', 'Markups');
	/*						if($_SESSION['userRole'] == 'Architect'){
								$attribute1Arr = array('Architectural');
							}elseif($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Structural'){
								$attribute1Arr = array('Structure');
							}elseif($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Services'){
								$attribute1Arr = array('Services');
							}*/
							for($i=0;$i<sizeof($attribute1Arr);$i++){?>
							<option value="<?=$attribute1Arr[$i]?>" <? if($attribute1Arr[$i] == $drawData[0]['attribute1'])echo 'selected="selected"';?> ><?=$attribute1Arr[$i]?></option>
						<?php }?>
						</select>
						</td>
						<td>
						<select name="drawingattribute2" id="drawingattribute2Default" class="select_box" style="width: 160px;
background-image: url(images/input_160.png);margin-left:0px;"  />
							<option value="">Select</option>
						</select>
						</td>
						<td>
						<input type="text" name="searchKeyword" id="searchKeyword" class="input_small" style="width: 220px;
background-image: url(images/selectSpl.png);" />
						</td>
						<td>
							<img onClick="searchDrawPdf();" style="cursor:pointer;" src="images/search_draw_reg.png" alt="search" />&nbsp;&nbsp;<img onClick="resetSearch();" style="cursor:pointer;" src="images/reset_drw_search.png" title="Reset filter" align="top"  />
						</td>
					</tr>
				</table>
			</div>
			
			<div class="SearchBox_pmb clearfix">
				<div class="SearchTabs_pmb" id="SearchTabsHide" style="display:none;"></div>
			</div>
			<div style="margin:7px 0px 0px 0px;">
				<div id="drawingDisplay">
				</div>
				<br clear="all" />
			</div>
			<div class="spacer"></div>
			<div id="selectedFileHolderIds" style="display:none;"></div>
		</div>
	</div>
</div>
<!-- Markup save image attach -->
<?php if(isset($_GET['ref'])){ 
	$decodedData = base64_decode($_GET['ref']);
	$attachArg = explode(',', $decodedData);
	$fileID = $attachArg[0];
	$fileTitle = $attachArg[1];
	$fileName = $attachArg[2];
	$fileType = $attachArg[3];
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			attachFiles("<?=$fileID?>", '<?=$fileTitle?>', '<?=$fileName?>', "<?=$fileType?>");
		});
	</script>
<?php }?>

<script type="text/javascript">
var top1 = 100;
var deadlock = true;//File upload
var params = "";
var align = 'center';
var top = 100;
var width = 850;
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

	var TabArr = new Array('General', 'Architectural', 'Structure', 'Services', 'Subcontractor', 'Markups');
	var markup = '';
function subTitleArr(arrayElement){
	switch(arrayElement){
		case 'General' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', '3D images', 'Marketing', 'Brief and overview', 'Document Transmittal');
		break;
		
		case 'Architectural' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Document Transmittal');
		break;
		
		case 'Structure' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Civil', 'Document Transmittal');
		break;
					
		case 'Services' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Mechanical', 'Electrical', 'Hydraulic', 'Fire', 'Document Transmittal', 'Lighting');
		break;
		
		case 'Subcontractor' :
			var TabArrTwo = new Array('Concrete Profile', 'Bottom Reinforcement', 'Top Reinforcement', 'Sheer Reinforcement', 'PT Design', 'Details', 'Document Transmittals');
		break;

		case 'Markups' :
			var TabArrTwo = new Array();
			var markup = 'Markups';
		break;
		
		default :
			var TabArrTwo = new Array();
		break;
	}	
	return TabArrTwo;
}

var attrTabs = '<ul id="attributeTabs">';
	for (i=0; i<TabArr.length; i++){
		attrTabs += '<li><a href="javascript:searchDrawPdf(\''+TabArr[i]+'\');"><span>'+TabArr[i]+'</span></a></li>';
	}
	attrTabs += '</ul>';

	var responseText = '<table cellpadding="0" cellspacing="0" border="0" class="display" id="drawingDefault" width="100%"><thead><tr><th width="70px">Drawing Number</th><th width="200px">Drawing Title</th><th>Rev.</th><!--th>Comments</th--><th>Attribute 1</th><th>Attribute 2</th><th>Tag</th><th width="40">Download on iPad</th><th width="40">Approved</th><th style="width:120px !important;">Action</th></tr></thead><tbody><tr><td colspan="8" class="dataTables_empty">Loading data from server</td></tr></tbody></table>';

function afterLoadRegistration(){
	$('select#drawingattribute1').change(function(){
		var currValue = $(this).val();
		var outputStr = '<option value="">Select</option>';

		TabArrTwo = subTitleArr(currValue);

		for (i=0; i<TabArrTwo.length; i++){
			outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
		}
		
		$('#drawingattribute2').html(outputStr);
	});
	var config = {
		support : ",application/pdf",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDiv",// Upload Area ID
		uploadUrl: "add_drawing_register.php?antiqueID="+Math.random()// Server side upload url
	}
	initMultiUploader(config);
	deadlock = true;
}
function afterLoad(){
	$('select#drawingattribute1').change(function(){
		var currValue = $(this).val();
		var outputStr = '<option value="">Select</option>';

		TabArrTwo = subTitleArr(currValue);

		for (i=0; i<TabArrTwo.length; i++){
			outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
		}
		$('#drawingattribute2').html(outputStr);
	});
	var config = {
		support : ",application/pdf",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDiv",// Upload Area ID
		uploadUrl: "add_drawing_register_revision.php?antiqueID="+Math.random()// Server side upload url
	}
	initMultiUploader(config);
	deadlock = true;
}
$('select#drawingattribute1Default').change(function(){
	var currValue = $(this).val();
	var outputStr = '<option value="">Select</option>';

	TabArrTwo = subTitleArr(currValue);

	for (i=0; i<TabArrTwo.length; i++){
		outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
	}
	$('#drawingattribute2Default').html(outputStr);
});

function searchDrawPdf(attrTab){
	attrTab = typeof attrTab !== 'undefined' ? attrTab : 0;

	var drawingattribute1 = $('#drawingattribute1Default').val().trim();
	var drawingattribute2 = $('#drawingattribute2Default').val().trim();
	var searchKeyword = $('#searchKeyword').val().trim();

	if(attrTab == 0 && drawingattribute1 == '' && drawingattribute2 == '' && searchKeyword == ''){
		jAlert('Please select any filter criteria to perform search operation ');
		return false;
	}
	//Dynamic Tabs Crations Start Here
	if(drawingattribute1 != ''){
		attrTabs = '<ul id="attributeTabs"><li class="Nav"><a href="javascript:searchDrawPdf(\''+drawingattribute1+'\');"><span>'+drawingattribute1+'</span></a></li></ul>';
	}
	//Dynamic Tabs Crations End Here
	
	params = "attr1="+encodeURIComponent(drawingattribute1)+"&attr2="+encodeURIComponent(drawingattribute2)+"&searchKey="+encodeURIComponent(searchKeyword)+"&name="+Math.random();
	if(attrTab != 0){
		params = "attrTab="+attrTab+"&attr1="+encodeURIComponent(drawingattribute1)+"&attr2="+encodeURIComponent(drawingattribute2)+"&searchKey="+encodeURIComponent(searchKeyword)+"&name="+Math.random();
	}
	
	/*if( attrTab == 'Markups'){
		var responseText = '<table cellpadding="0" cellspacing="0" border="0" class="display" id="drawingDefault" width="100%"><thead><tr><th width="70px">Drawing Number</th><th width="200px">Drawing Title</th><th>Rev.</th><!--th>Comments</th--><th>Attribute 1</th><th>Attribute 2</th><th>Tag</th><th width="80px">Download on iPad</th><th width="80px">Approved</th><th>Action</th></tr></thead><tbody><tr><td colspan="8" class="dataTables_empty">Loading data from server</td></tr></tbody></table>';
	}*/
	$('#SearchTabsHide').html(attrTabs);
	$('#SearchTabsHide').show();
	$('#drawingDisplay').html(responseText);	
	
	var oTable = $('#drawingDefault').dataTable( {
		"iDisplayLength": 100,
		"aLengthMenu": [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "All"]
        ],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "drawing_register_data_table_v1.php?typeFlag=attach&"+params,
		"bStateSave": true
	} );
//	oTable.fnSort( [ [0,'asc'] ] );
	if(attrTab != 0){
		$('ul#attributeTabs li').each(function(index) {
			if($(this).hasClass("Nav"))
				$(this).removeClass("Nav");
		});
		$('ul#attributeTabs li').each(function(index) {
			if($(this).text() == attrTab)
				$(this).addClass("Nav");
		});
	}
}

function RefreshTable(){
	$.getJSON("drawing_register_data_table_v1.php?typeFlag=attach&"+params, null, function( json ){
		table = $('#drawingDefault').dataTable();
		oSettings = table.fnSettings();
		table.fnClearTable(this);
		
		for (var i=0; i<json.aaData.length; i++){
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw();
	});
}
$(document).ready(function() {
	$('#SearchTabsHide').html(attrTabs);
	$('#SearchTabsHide').show();
	$('#drawingDisplay').html(responseText);	
	
	var oTable = $('#drawingDefault').dataTable( {
		"iDisplayLength": 100,
		"aLengthMenu": [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "All"]
        ],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "drawing_register_data_table_v1.php?typeFlag=attach&attr1=General&name="+Math.random(),
		"bStateSave": true
	} );
//	oTable.fnSort( [ [0,'asc'] ] );
});
function resetSearch(){
	$('#drawingattribute1Default').val('');
	$('#drawingattribute2Default').html('<option value="">Select</option>');
	$('#searchKeyword').val('');

	var TabArr = new Array('General', 'Architectural', 'Structure', 'Services', 'Subcontractor', 'Markups');
<?php #if($_SESSION['userRole'] == 'Architect'){?>
	//var TabArr = new Array('Architectural');
<?php #}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Structural') || ($_SESSION['userRole'] == 'Structural Engineer')){?>
	//var TabArr = new Array('Structure');
<?php #}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Services') || ($_SESSION['userRole'] == 'Services Engineer')){?>
	//var TabArr = new Array('Services');
<?php #}?>

	attrTabs = '<ul id="attributeTabs">';
	for (i=0; i<TabArr.length; i++){
		attrTabs += '<li><a href="javascript:searchDrawPdf(\''+TabArr[i]+'\');"><span>'+TabArr[i]+'</span></a></li>';
	}
	attrTabs += '</ul>';
	
	$('#SearchTabsHide').html(attrTabs);
	$('#drawingDisplay').html(responseText);	
	var oTable = $('#drawingDefault').dataTable( {
		"iDisplayLength": 100,
		"aLengthMenu": [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "All"]
        ],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "drawing_register_data_table_v1.php?typeFlag=attach&attr1=General&name="+Math.random(),
		"bStateSave": true
	} );
//	oTable.fnSort( [ [0,'asc'] ] );
}
function attachFiles(fileID, fileTitle, fileName, fileType){
	var ploat = true;
	var testString = 'li_'+fileID;
/*	if($("#selectedFileHolder").text().trim() != ''){
		jAlert('Single Attachment Supported');
		ploat = false;
	}
*/	$("#selectedFileHolder li").each(function(){
		if(this.id == testString){
			ploat = false;
		}
	});
	if($('#finalAttach').hide()){$('#finalAttach').show();}
	if(ploat){
		var html = '<li id="li_'+fileID+'">'+fileTitle+'<span onclick="removeSelectedFiles('+fileID+', \''+fileName+'\', \''+fileTitle+'\')">X</span></li>';
		$('#selectedFileHolder').append(html);
		if($('#selectedFileHolderIds').text().trim() == ''){
			$('#selectedFileHolderIds').append(fileName+'###'+fileTitle+'###'+fileType);
		}else{
			$('#selectedFileHolderIds').append(','+fileName+'###'+fileTitle+'###'+fileType);
		}
		
	}
}
function completeAttachment(){
	var selectedFileHolderIds = $('#selectedFileHolderIds').text().trim();
	if(selectedFileHolderIds != ''){
		showProgress();
		$.post("copy_file_toAttach_folder_v1.php", {fileIds:selectedFileHolderIds, uniqueId:Math.random()}).done(function(data) {
			hideProgress();
			var jsonResult = JSON.parse(data);	
			if(jsonResult.status){
				console.log(jsonResult.dataArr);
				<?php if(isset($_GET['page']) && $_GET['page']=='message_details'){ ?>
				window.location.href = "?sect=message_details&id=<?php echo $_GET['id'];?>&type=<?php echo $_GET['type'];?>&attached=Y";
				
				<?php }else if(isset($_GET['page']) && $_GET['page']=='forward'){ ?>
				window.location.href = "?sect=forward&msgid=<?php echo isset($_GET['msgid'])?$_GET['msgid']:0;?>&attached=Y";
				
				<?php }else{?>
				window.location.href = "?sect=compose&msgid=<?php echo isset($_GET['msgid'])?$_GET['msgid']:0;?>&folderType=<?php echo isset($_GET['folderType'])?$_GET['folderType']:'';?>&attached=Y";
				<?php }?>
			}
		});
	}else{
		 window.history.back();
	}
}
function removeSelectedFiles(fileID, fileName, fileTitle){
	$('#li_'+fileID).hide('slow', function() { $(this).remove(); });
	var replaceString = $('#selectedFileHolderIds').text().toString();
	var searchCount = replaceString.search(fileName+'###'+fileTitle+",");
	if(searchCount > -1){
		var newText = replaceString.replace(fileName+'###'+fileTitle+",", "");
	}else{
		var newText = replaceString.replace(fileName+'###'+fileTitle, "");
	}
	$('#selectedFileHolderIds').text(newText);
}
</script>
<script type="text/javascript" src="js/multiupload.js"></script>
<script type="text/javascript" src="js/upload.js"></script>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
<style>
.roundCorner{ border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; }
.innerDiv{ color:#000000; float:left; border:1px solid red; width:300px; height:120px; }
div#innerModalPopupDiv{color:#000000;}
h3#uploaderBulk{font-size:10px;padding:0;margin:0;width: 460px;float:left;}
.bulkfiles {clear: both;border: 1px solid #ccc;background-color: #E4E4E4;padding: 3px;position: relative;height: 25px;margin: 3px;z-index: 1;width: 97%;opacity: 0.6;cursor: default;}
div.content_container{ width:100% !important; }
ul#selectedFileHolder{ list-style:none; margin-left:-35px; width:90%;}
ul#selectedFileHolder li{ float:left;position: relative;margin: 3px 0 3px 5px;padding: 3px 5px 3px 5px;border: 1px solid #aaa;border-radius: 3px;background-color: #e4e4e4;}
ul#selectedFileHolder li span{ margin-left:5px; cursor:pointer;}
ul#selectedFileHolder > { margin-left:5px; cursor:pointer;}
#finalAttach{ float:right;position: relative;margin: 3px 15px 3px 5px;padding: 10px 20px 10px 20px;border: 1px solid #aaa;border-radius: 3px;background-color: #A6D82F; cursor:pointer;}
</style>
