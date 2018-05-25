<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
include'data-table.php'; 
include_once("commanfunction.php");

session_start();
$obj = new COMMAN_Class(); 
if(isset($_REQUEST['id'])) {
	$id=$_REQUEST['id'];
	$_SESSION['project_id'] = $id;
	$_SESSION['idp'] = base64_decode($_GET['id']);
} else {
	$id = ''; 
}

if(isset($_POST['projName']) && !empty($_POST['projName'])){
	$_SESSION['idp'] = $_POST['projName'];
}

$err_msg = '';//insert for Assign inspector
$permArray = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Architect');
$id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']);

//----------------------------
// right side box style.
$rightSide = 'float:left;';
if(isset($_GET['type']) && $_GET['type'] == 'pmb') {
	$rightSide = 'width: 80%; margin:0 auto;';
}
?>
	<div id="middle" <?php if($_GET['type'] == 'pmb'){?>class="gray_pmb" <? }?> style="padding-top:10px;">
  		<?php if(!isset($_GET['type']) && $_GET['type'] != 'pmb'){?>
		<div id="leftNav" style="width:22%;float:left;">
			<?php include 'side_menu.php';?>
		</div>
		<?php }?>
		<div id="rightCont" style="<?=$rightSide?>">
            <ul class="headerHolder">
	            <li style="width:350px;"><img src="images/document-register.png" name="" id=""  /></li>
            	<li style="text-align:right;padding-top:20px;">Project:</li>
                <li style="text-align:right;padding-top:10px;">
                	<form action="" name="projForm" id="projForm" method="post">
	                <select name="projName" id="projName"  class="select_box" onChange="startAjax(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
					<?php if(isset($_SESSION['ww_is_company'])){
							$q = "SELECT project_id, project_name FROM user_projects WHERE is_deleted=0 AND is_pdf=1 GROUP BY project_name";
							$res = mysql_query($q);
							$prIDArr = array();
							$outPutStr = "";
							while($q1 = mysql_fetch_array($res)){
								$selectBox = '<option value="'.$q1[0].'"';
								$prIDArr[] = $q1[0];
								if(isset($_SESSION['idp']) && $_SESSION['idp'] != ""){
									if($_SESSION['idp'] == $q1[0]){
										$selectBox .= 'selected="selected"';
									}
								}	
								$selectBox .= '>'.$q1[1].'</option>';
								$outPutStr .= $selectBox;
							}
							echo $outPutStr;
						}else{
							$q = "SELECT project_id, project_name FROM user_projects WHERE user_id = '".$_SESSION['ww_builder_id']."' and is_deleted = 0 AND is_pdf=1 GROUP BY project_name";
							$res = mysql_query($q);
							$prIDArr = array();
							$outPutStr = "";
							while($q1 = mysql_fetch_array($res)){
								$selectBox = '<option value="'.$q1[0].'"';
								$prIDArr[] = $q1[0];
								if(isset($_SESSION['idp']) && $_SESSION['idp'] != ""){
									if($_SESSION['idp'] == $q1[0]){
										$selectBox .= 'selected="selected"';
									}
								}	
								$selectBox .= '>'.$q1[1].'</option>';
								$outPutStr .= $selectBox;
							}
							echo $outPutStr;
						}?>
				    </select>
                    </form>
    			</li>
			</ul><br /><br clear="all" />
            <div class="content_hd1" style="width:500px;margin-top:12px;">
				<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font><br />
				<a <?php if(!isset($_GET['type']) && $_GET['type'] != 'pmb'){?>
				href="?sect=add_project_detail&id=<?=$id;?>&hb=<?=$hb;?>"
				<? }else{echo 'onclick="javascript:history.back(-1);"';}?> style="display: block;float: none;height: 35px;margin-left: 585px;<?php if(isset($_GET['type']) && $_GET['type'] == 'pmb'){echo 'margin-left: 863px;';}?>margin-top: -25px;width: 87px;">
					<img src="images/back_btn2.png" />
				</a>
			</div><br clear="all" />
			<div id="errorHolder" style="margin-left: 10px;margin-bottom:10px;">
				<div class="success_r" id="outPutResult" style="height:35px;width:400px;display:none;"><p id="outPutResultPara"></p></div>		
			</div>
			<div class="big_container" <?php if(!isset($_GET['type']) && $_GET['type'] != 'pmb'){?>style="width:900px;"<? } else {?>style="width:100%;"<? } ?>>
	    	<div id="searchDraw" style="border:1px solid #A5DDF9;padding:5px;">
				<table width="100%" border="0">
					<tr>
						<td style="padding-left:10px;color:#FFFFFF;" >Attribute 1</td>
						<td style="padding-left:10px;color:#FFFFFF;">Attribute 2</td>
						<td style="padding-left:10px;color:#FFFFFF;">Search Keyword</td>
						<td style="padding-left:10px;color:#FFFFFF;">Status</td>
					<?php $approveArr = array('General Consultant', 'Architect');
					if(!in_array($_SESSION['userRole'], $approveArr)){?>
						<td style="padding-left:10px;color:#FFFFFF;">Download on iPad</td>
					<?php }?>
                    	<td style="padding-left:10px;"><img onclick="resetSearch();" style="cursor:pointer; float:right;" src="images/reset_drw_search.png" title="Reset filter" align="top"  /></td>
					</tr>
					<tr>
						<td>
							<select name="drawingattribute1" id="drawingattribute1Default" class="select_box" style="width: 160px;
	background-image: url(images/input_160.png);margin-left:0px;"  />
								<option value="">Select</option>
							<?php $attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Lighting', 'Tenancy Fitout', 'Landscaping');

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
							<input type="text" name="searchKeyword" id="searchKeyword" class="input_small" style="width: 150px;
background-image: url(images/input_160.png);" />
						</td>
						<td>
							<?php $stsArr = array("Tender", "Issued for Construction", "For Information");?>
							<select name="pdfStatus" id="pdfStatus" class="select_box" style="width: 160px;background-image: url(images/input_160.png);margin-left:0px;"  />
								<option value="">Select</option>
								<?php foreach($stsArr as $sArr){?>
									<option value="<?php echo $sArr;?>"><?php echo $sArr;?></option>								
								<?php }?>
							</select>
						</td>
					<?php if(!in_array($_SESSION['userRole'], $approveArr)){?>
						<td>
							<input type="checkbox" name="onlyApproved" id="onlyApproved" value="1" />
						</td>
					<?php }?>
						<td>
							<img onclick="searchDrawPdf();" style="cursor:pointer; float:right;" src="images/search_draw_reg.png" alt="search" /> 
						</td>
					</tr>
				</table>
			</div>
		<?php if($_SESSION['userRole'] != 'Tender'){?>
		<?php if($_SESSION['ww_builder']['user_type'] == 'manager'){ ?>
			<img src="images/add_new.png" onclick="addNewRegister();" style="float:right;margin:9px 5px 10px 0px;"  />
			<img src="images/bulk_upload.png" onclick="bulkUploadRegisters();" style="float:right;margin:9px 5px 10px 0px;"  />
		<?php } ?>
			<!--<img src="images/bulk-dwg.png" onclick="bulkUpdateDwg();" style="float:right;margin:9px 5px 10px 0px;"  />-->
			<img src="images/download_selected.png" onclick="downloadSelectedFiles();" style="float:right;margin:9px 5px 10px 0px;"  />
			<img src="images/document_register_btn.png" onclick="drawingRegisterReport();" style="float:right;margin:9px 5px 10px 0px;"  />
		<?php }else{?>
			<img src="images/download_selected.png" onclick="downloadSelectedFiles();" style="float:right;margin:9px 5px 10px 0px;"  />	
		<?php }?>
		<?php if($_SESSION['ww_builder']['user_type'] == 'manager'){ ?>
			 <img src="images/report_btn.png" onclick="drawingRegisterReportManager();"  style="float:right;margin:9px 5px 10px 0px;" />
		<?php } ?>

		<?php if($_SESSION['ww_builder']['user_type'] == 'manager'){ ?>
	   		<img src="images/delete_selected.png" onclick="deleteAllDrawing();" style="float:right;margin:9px 5px 10px 0px;"  />
		<?php } ?>
			<br clear="all" />
			<div class="SearchBox" <?php if(isset($_GET['type']) && $_GET['type'] == 'pmb'){?>style="width:965px;"<? }?>>
				<div class="SearchTabs" id="SearchTabsHide" style="width:1000px;display:none;<?php if(isset($_GET['type']) && $_GET['type'] == 'pmb'){echo 'left:-35px;width:1000px;'; }?>"></div>
			</div>
			<br /><br /><br clear="all" />
				<div>
					<div id="drawingDisplay"></div> 
					<br clear="all" />
				</div>
				<div class="spacer"></div>
			</div>
		</div>
	</div>
<script type="text/javascript">
var deadlock = true;//File upload
var params = "";
var align = 'center';
var top1 = 100;
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
//Initial Settings If any attribute two added u need to add here only
var TabArr = new Array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Lighting', 'Tenancy Fitout', 'Landscaping');
function subTitleArr(arrayElement){
	switch(arrayElement){
		case 'General' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', '3D images', 'Marketing', 'Brief and overview', 'Document Transmittal');
		break;
		
		case 'Architectural' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Document Transmittal');
		break;
		
		case 'Structure' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Civil', 'Document Transmittal', 'Site Inspection', 'Project Advice Notice');
		break;

		case 'Services' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Mechanical', 'Electrical', 'Hydraulic', 'Fire', 'Document Transmittal');
		break;
		
		case 'Concrete &amp; PT' :
			var TabArrTwo = new Array('Concrete Profile', 'Bottom Reinforcement', 'Top Reinforcement', 'Sheer Reinforcement', 'PT Design', 'Details', 'Document Transmittal', 'Site Inspection');
		break;
		
		case 'Concrete & PT' :
			var TabArrTwo = new Array('Concrete Profile', 'Bottom Reinforcement', 'Top Reinforcement', 'Sheer Reinforcement', 'PT Design', 'Details', 'Document Transmittal', 'Site Inspection');
		break;
				
<?php if(in_array($_SESSION['userRole'], $permArray) || $_SESSION['ww_builder']['user_type'] == 'manager'){?>
		case 'Autocad Files' :
			var TabArrTwo = new Array('Architectural', 'Services', 'Structure');
		break;
<?php }?>
		case 'Lighting' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Document Transmittal');
		break;

		case 'Tenancy Fitout' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos', 'Document Transmittal');
		break;

		case 'Penthouse Architecture' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos', 'Document Transmittal');
		break;

		case 'Landscaping' :
			var TabArrTwo = new Array('Drawings', 'Specifications', 'Reports &amp; Schedules', 'Plans', 'Elevations', 'Sections', 'RCP\'s', 'Details', 'Photos', 'Document Transmittal');
		break;

		default :
			var TabArrTwo = new Array();
		break;
	}	
	return TabArrTwo;
}

var attrTabs = '<ul id="attributeTabs">';
	for (i=0; i<TabArr.length; i++){
		attrTabs += '<li class="Admin"><a href="javascript:searchDrawPdf(\''+TabArr[i]+'\');"><span>';
		if(TabArr[i] == 'Penthouse Architecture'){
			attrTabs += 'P. Architecture';
		}else{
			attrTabs += TabArr[i];
		}
		attrTabs += '</span></a>';
		
		TabArrTwo = subTitleArr(TabArr[i]);
		
		attrTabs += '<ul class="admindrop">';
		for (j=0; j<TabArrTwo.length; j++){
//			TabArrTwo[j] = TabArrTwo[j].replace(/'/g, "\\'");
			attrTabs += '<li class="Admin"><span onclick="searchDrawPdfSecAttr(\''+TabArr[i]+'\', \''+TabArrTwo[j].replace(/'/g, "\\'")+'\');">'+TabArrTwo[j]+'</span></li>';
		}
		attrTabs += '</ul>';
	}
	attrTabs += '</ul>';

var responseText = '<form id="allTaskTable" name="allTaskTable"><table cellpadding="0" cellspacing="0" border="0" class="display" id="drawingDefault" width="100%"><thead><tr><th><input type="checkbox" onclick="toggleCheck(this);" /></th><th width="70px">Document Number</th><th width="200px">Document Description</th><th>Rev.</th><th>Attribute 1</th><th>Attribute 2</th><th>Tag</th><th>Status</th><th width="80px">Download on iPad</th>';
<?php if($_SESSION['ww_builder']['user_type'] == 'manager'){?>
	responseText += '<th>Approved</th>';
<?php }?>
responseText += '<th>Action</th></tr></thead><tbody><tr><td colspan="8" class="dataTables_empty">Loading data from server</td></tr></tbody></table></from>';
function showRevisions(pdfID, attr1){
	console.log(pdfID, attr1);
	attr1 = typeof attr1 !== 'undefined' ? attr1 : '';
	
	modalPopup(align, top1, 690, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'drawing_registration_show.php?pdfID='+pdfID+'&attr1='+encodeURIComponent(attr1), loadingImage, addUploadFile);
}
function addNewRegister(){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_drawing_register.php?&name='+Math.random(), loadingImage, afterLoadRegistration);
}
function addNewRegisterRevision(regID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_drawing_register_revision.php?tableID='+regID+'&name='+Math.random(), loadingImage, afterLoad);
}
function editDrawingRegister(regID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_drawing_register.php?tableID='+regID+'&name='+Math.random(), loadingImage, afterLoadwithoutFile);
}
function removeImages(regID, DivID, fileType){
	fileType = typeof fileType !== 'undefined' ? fileType : 0;
	if(fileType == 0)
		var msg = "You are about to delete the PDF and all revisions &minus; are  you sure you want to do this?";
	else
		var msg = "You are about to delete the PDF &minus; are  you sure you want to do this?";
	var r = jConfirm(msg, null, function(r){
//	var r = jConfirm('You are about to delete the PDF and all revisions &minus; are  you sure you want to do this?', null, function(r){
		if (r === true){
			showProgress();	
			var curData = "tableID="+regID+"&name="+Math.random();
			if(fileType)
				curData = "tableID="+regID+"&fileType="+fileType+"&name="+Math.random();

			$.ajax({
				url: "remove_drawing_register_file.php",
				type: "POST",
				data: curData,
				success: function (res) {
					hideProgress();
					$('#'+DivID).hide('slow');
					$('#'+DivID).html('');
					if(fileType){
						var divArr = DivID.split('_');
						var newDivID = 'drawing_'+(divArr[1]-1);
						$('#'+newDivID+' div.deleteHolder').show('fast');
						addUploadFile();
					}
					RefreshTable();
				}
			});	
		}else{
			return false;
		}
	});
}
function updateDrawingRegisterData(){
	if($('#drawingTitle').val().trim() == ''){$('#errorDrawingTitle').show('slow');return false;}else{$('#errorDrawingTitle').hide('slow');}
	if($('#drawingNumber').val().trim() == ''){$('#errorDrawingNumber').show('slow');return false;}else{$('#errorDrawingNumber').hide('slow');}
	if($('#drawingTitle').val().trim() == $('#drawingNumber').val().trim()){$('#errorDrawingTitle1').show('slow');return false;}else{$('#errorDrawingTitle1').hide('slow');}
	if($('#drawingattribute2').val() != ""){
		var valueArr = new Array();
		var drawingattribute2Multiple = "";
		valueArr = $('#drawingattribute2').val();
		if(!valueArr){}else{
			valueArr = valueArr.filter(function(v){return v!==''});
			drawingattribute2Multiple = valueArr.join('###');
		}
		$('#drawingattribute2Multi').val(drawingattribute2Multiple);
	}
	showProgress();
	$.post('edit_drawing_register.php?antiqueID='+Math.random(), $('#editDrawingForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			$('#outPutResultPara').text(jsonResult.msg);	
			$('#outPutResult').show('fast');	
			closePopup(300);
			setTimeout(function(){$('#outPutResult').hide('slow');	},3000);
		}else{
			jAlert('Data updation failed, try again later');
		}
		RefreshTable();
	});
}
function afterLoadRegistration(){
	console.log('werwae dsf');
	$('select#drawingattribute1').change(function(){
		var currValue = $(this).val();
		var outputStr = '<option value="">Select</option>';
	//alert(currValue);
		TabArrTwo = subTitleArr(currValue);
		for (i=0; i<TabArrTwo.length; i++){
			outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
		}
		$('#drawingattribute2').html(outputStr);
	});
	var config = {
		//support : "image/jpg,image/png,image/bmp,image/jpeg,image/gif,application/pdf,application/x-download",// Valid file formats
		support : "application/pdf",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDivDrager",// Upload Area ID
		uploadUrl: "add_drawing_register.php?antiqueID="+Math.random()// Server side upload url
	}
	console.log(config);
	deadlockPDF = true;
	deadlockDWG = true;
	deadlockIMG = true;
	var revision4check = "";	
	initMultiUploader(config);
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
		//support : "image/jpg,image/png,image/bmp,image/jpeg,image/gif,application/pdf,application/x-download",// Valid file formats
		support : "application/pdf",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDivDrager",// Upload Area ID
		uploadUrl: "add_drawing_register_revision.php?antiqueID="+Math.random()// Server side upload url
	}
	initMultiUploader(config);
	deadlockPDF = true;
	deadlockDWG = true;
	deadlockIMG = true;
	var revision4check = "";	
}
function afterLoadwithoutFile(){
	$('select#drawingattribute1').change(function(){
		var currValue = $(this).val();
		var outputStr = '<option value="">Select</option>';

		TabArrTwo = subTitleArr(currValue);

		for (i=0; i<TabArrTwo.length; i++){
			outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
		}

		$('#drawingattribute2').html(outputStr);
	});
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
	
	var pdfStatus = $('#pdfStatus').val().trim();
		
	var searchKeyword = $('#searchKeyword').val().trim();
	var onlyApproved = 0;
	var onlyApprovedVal = $('#onlyApproved').val();
	if($('#onlyApproved').is(':checked')){
		onlyApproved = 1;
	}
	
	if(attrTab == 0 && drawingattribute1 == '' && drawingattribute2 == '' && searchKeyword == '' && pdfStatus == '' && onlyApproved == 0){
		jAlert('Please select any filter criteria to perform search operation ');
		return false;
	}
	//Dynamic Tabs Crations Start Here
	if(drawingattribute1 != ''){
		attrTabs = '<ul id="attributeTabs"><li class="Nav"><a href="javascript:searchDrawPdf(\''+drawingattribute1+'\');"><span>'+drawingattribute1+'</span></a></li></ul>';
	}
	//Dynamic Tabs Crations End Here
	<?php if(isset($_GET['type']) && $_GET['type'] == 'pmb'){?>
		params = "req=pmb&attr1="+encodeURIComponent(drawingattribute1)+"&attr2="+encodeURIComponent(drawingattribute2)+"&searchKey="+encodeURIComponent(searchKeyword)+"&pdfStatus="+encodeURIComponent(pdfStatus)+"&name="+Math.random();
	<?php }else{?>
		params = "attr1="+encodeURIComponent(drawingattribute1)+"&attr2="+encodeURIComponent(drawingattribute2)+"&searchKey="+encodeURIComponent(searchKeyword)+"&pdfStatus="+encodeURIComponent(pdfStatus)+"&name="+Math.random();
	<?php }?>
	if(attrTab != 0){
		<?php if(isset($_GET['type']) && $_GET['type'] == 'pmb'){?>
			params = "req=pmb&attrTab="+encodeURIComponent(attrTab)+"&attr1="+encodeURIComponent(drawingattribute1)+"&attr2="+encodeURIComponent(drawingattribute2)+"&searchKey="+encodeURIComponent(searchKeyword)+"&pdfStatus="+encodeURIComponent(pdfStatus)+"&name="+Math.random();
		<?php }else{?>
			params = "attrTab="+encodeURIComponent(attrTab)+"&attr1="+encodeURIComponent(drawingattribute1)+"&attr2="+encodeURIComponent(drawingattribute2)+"&searchKey="+encodeURIComponent(searchKeyword)+"&pdfStatus="+encodeURIComponent(pdfStatus)+"&name="+Math.random();
		<?php }?>
	}

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
		"sAjaxSource": "drawing_register_data_table.php?"+params,
		"bStateSave": true,
		"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 9] }]
	} );
	//oTable.fnSort( [ [1,'asc'] ] );
	
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
function searchDrawPdfSecAttr(attrTab, secAttrTab){

	attrTab = typeof attrTab !== 'undefined' ? attrTab : 0;
	secAttrTab = typeof secAttrTab !== 'undefined' ? secAttrTab : 0;

	var drawingattribute1 = $('#drawingattribute1Default').val().trim();
	var drawingattribute2 = $('#drawingattribute2Default').val().trim();
	
	var pdfStatus = $('#pdfStatus').val().trim();
		
	var searchKeyword = $('#searchKeyword').val().trim();
	var onlyApproved = 0;
	var onlyApprovedVal = $('#onlyApproved').val();
	if($('#onlyApproved').is(':checked')){
		onlyApproved = 1;
	}
	
	if(attrTab == 0 && drawingattribute1 == '' && drawingattribute2 == '' && searchKeyword == '' && pdfStatus == '' && onlyApproved == 0){
		jAlert('Please select any filter criteria to perform search operation ');
		return false;
	}
	//Dynamic Tabs Crations Start Here
	if(drawingattribute1 != ''){
		attrTabs = '<ul id="attributeTabs"><li class="Nav"><a href="javascript:searchDrawPdf(\''+drawingattribute1+'\');"><span>'+drawingattribute1+'</span></a></li></ul>';
	}
	//Dynamic Tabs Crations End Here
	<?php if(isset($_GET['type']) && $_GET['type'] == 'pmb'){?>
		params = "req=pmb&attr1="+encodeURIComponent(drawingattribute1)+"&attr2="+encodeURIComponent(drawingattribute2)+"&searchKey="+encodeURIComponent(searchKeyword)+"&pdfStatus="+encodeURIComponent(pdfStatus)+"&secAttrTab="+encodeURIComponent(secAttrTab)+"&name="+Math.random();
	<?php }else{?>
		params = "attr1="+encodeURIComponent(drawingattribute1)+"&attr2="+encodeURIComponent(drawingattribute2)+"&searchKey="+encodeURIComponent(searchKeyword)+"&pdfStatus="+encodeURIComponent(pdfStatus)+"&secAttrTab="+encodeURIComponent(secAttrTab)+"&name="+Math.random();
	<?php }?>
	if(attrTab != 0){
		<?php if(isset($_GET['type']) && $_GET['type'] == 'pmb'){?>
			params = "req=pmb&attrTab="+encodeURIComponent(attrTab)+"&attr1="+encodeURIComponent(drawingattribute1)+"&attr2="+encodeURIComponent(drawingattribute2)+"&searchKey="+encodeURIComponent(searchKeyword)+"&pdfStatus="+encodeURIComponent(pdfStatus)+"&secAttrTab="+encodeURIComponent(secAttrTab)+"&name="+Math.random();
		<?php }else{?>
			params = "attrTab="+encodeURIComponent(attrTab)+"&attr1="+encodeURIComponent(drawingattribute1)+"&attr2="+encodeURIComponent(drawingattribute2)+"&searchKey="+encodeURIComponent(searchKeyword)+"&pdfStatus="+encodeURIComponent(pdfStatus)+"&secAttrTab="+encodeURIComponent(secAttrTab)+"&name="+Math.random();
		<?php }?>
	}

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
		"sAjaxSource": "drawing_register_data_table.php?"+params,
		"bStateSave": true,
		"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 9] }]
	} );
	//oTable.fnSort( [ [1,'asc'] ] );
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
	$.getJSON("drawing_register_data_table.php?"+params, null, function( json ){
		table = $('#drawingDefault').dataTable();
		oSettings = table.fnSettings();
		table.fnClearTable(this);
		
		for (var i=0; i<json.aaData.length; i++){
			table.oApi._fnAddData(oSettings, json.aaData[i]);
		}
		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		table.fnDraw(false);
//		table.fnDraw();
	});
}
var requestType = "";
<?php if(isset($_GET['type']) && $_GET['type'] == 'pmb'){?>
	requestType = "req=pmb&";
<?php }?>
$(document).ready(function() {
	$('#SearchTabsHide').html(attrTabs);
	$('#SearchTabsHide').show();
	$('#drawingDisplay').html(responseText);	
	var attribute1Arr = "General";

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
		"sAjaxSource": "drawing_register_data_table.php?"+requestType+"attr1="+encodeURIComponent(attribute1Arr)+"&name="+Math.random(),
		"bStateSave": true,
		"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 9] }]
	} );
//	oTable.fnSort( [ [1,'asc'] ] );
});
function resetSearch(){
	$('#drawingattribute1Default').val('');
	$('#drawingattribute2Default').html('<option value="">Select</option>');
	$('#pdfStatus').val('');
	$('#searchKeyword').val('');

	var TabArr = new Array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Lighting', 'Tenancy Fitout', 'Landscaping');
	<?php if(in_array($_SESSION['userRole'], $permArray) || $_SESSION['ww_builder']['user_type'] == 'manager'){?>
		var TabArr = new Array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Lighting', 'Tenancy Fitout', 'Landscaping');
	<?php }?>

	attrTabs = '<ul id="attributeTabs">';
	for (i=0; i<TabArr.length; i++){
		attrTabs += '<li class="Admin"><a href="javascript:searchDrawPdf(\''+TabArr[i]+'\');"><span>';
		if(TabArr[i] == 'Penthouse Architecture'){
			attrTabs += 'P. Architecture';
		}else{
			attrTabs += TabArr[i];
		}
		attrTabs += '</span></a>';
		
		TabArrTwo = subTitleArr(TabArr[i]);
		
		attrTabs += '<ul class="admindrop">';
		for (j=0; j<TabArrTwo.length; j++){
		//	TabArrTwo[j] = TabArrTwo[j].replace(/'/g, "\\'");
			attrTabs += '<li class="Admin"><span onclick="searchDrawPdfSecAttr(\''+TabArr[i]+'\', \''+TabArrTwo[j].replace(/'/g, "\\'")+'\');">'+TabArrTwo[j]+'</span></li>';
		}
		attrTabs += '</ul>';
	}
	attrTabs += '</ul>';
	
	var attribute1Arr = "General";
	
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
		"sAjaxSource": "drawing_register_data_table.php?attr1="+encodeURIComponent(attribute1Arr)+"&name="+Math.random(),
		"bStateSave": true,
		"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 9] }]
	} );
//	oTable.fnSort( [ [1,'asc'] ] );
}
function bulkUploadRegisters(){
	modalPopup(align, top1, 950, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_drawing_register_bulk.php?&name='+Math.random(), loadingImage, bulkRegistration);
}
var mappingDocumentArr = {};//Global Array to store select element in 
var mappedDocArr = {};//Global Array to store select element to show again selected
function bulkRegistration(){
	var config = {
		///support : "image/jpg,image/png,image/bmp,image/jpeg,image/gif,application/pdf,application/x-download",// Valid file formats
		support : ",application/pdf",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDiv",// Upload Area ID
		uploadUrl: "add_drawing_register_bulk.php?antiqueID="+Math.random()// Server side upload url
	}
	$('select#drawingattribute1').change(function(){
		var currValue = $(this).val();
		var outputStr = '<option value="">Select</option>';

		TabArrTwo = subTitleArr(currValue);

		for (i=0; i<TabArrTwo.length; i++){
			outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
		}
		$('.drawingattribute2js').html(outputStr);
	});
	mappingDocumentArr = {};
	mappedDocArr = {};
	initBulkUploader(config);
}
function addUploadFile(){
	var btnUpload = $('.replaceRev');
	var replaceRevisionID = $('#replaceRevisionID').val();
	var btnUploadCurr = btnUpload[btnUpload.length - 1];
	var pdfID = btnUploadCurr.title;	

	new AjaxUpload(btnUploadCurr, {
		action: 'auto_file_upload.php?action=pdfFiles&replaceRevisionID='+replaceRevisionID+'&pdfID='+pdfID+'&uniqueID='+Math.random(),
		name: 'pdfFiles',
		onSubmit: function(file, ext){
			if (! (ext && /^(pdf|PDF)$/.test(ext))){ 
				jAlert('Only PDF files are allowed');
				return false;
			}
			showProgress();
		},
		onComplete: function(file, fileName){
			hideProgress();
			if(fileName == 'success')
				jAlert('Pdf Rplace Successfully');
			else
				jAlert('Error in file uploading please try again');
		}
	});
}
function approvedSelected(){
	var t = document.getElementById('drawingDefault');
	var val1 = $(t.rows[0].cells[1]).text();  

	var taskArray = new Array();
	var projectID = '<?php echo $_SESSION['idp']?>';
	var taskCount = document.allTaskTable.elements["drawingID[]"].length;
	if(taskCount === undefined){
		var drawingID = document.getElementById('drawingID');
		if(drawingID.checked){
			parts = drawingID.value.split(".");
			taskArray[0] = parts[0];
		}
	}else{
		for(var i=0; i<taskCount; i++){
			var drawingID = document.allTaskTable.elements["drawingID[]"][i];
			if(drawingID.checked){
				parts = drawingID.value.split(".");
				taskArray[i] = parts[0];
			}else{
				taskArray[i] = 0;
			}
		}
	}
//Filter Array
	var newArr = []; 
	for (var index in taskArray) {  if( taskArray[index] ) {  newArr.push( taskArray[index] ); }  }  
	taskArray = newArr.join();

	if(taskArray[0] == '' || taskArray[0] === undefined){
		jAlert('please select any file as attachment.');
		return false;
	}
	showProgress();
	$.post("edit_drawing_register.php", {fileIds:taskArray, uniqueId:Math.random()}).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			jAlert(jsonResult.msg);
			RefreshTable();
		}
	});	
}
function completeAttachment(){
	var taskArray = new Array();
	var projectID = '<?php echo $_SESSION['idp']?>';
	var taskCount = document.allTaskTable.elements["drawingID[]"].length;

	if(taskCount === undefined){
		var taskId = document.getElementById('drawingID');
		if(taskId.checked){
			taskArray[0] = taskId.value;
		}
	}else{
		for(var i=0; i<taskCount; i++){
			var taskId = document.allTaskTable.elements["drawingID[]"][i];
			if(taskId.checked){
				taskArray[i] = taskId.value;
			}else{
				taskArray[i] = 0;
			}
		}
	}
//Filter Array
	var newArr = []; 
	for (var index in taskArray) {  if( taskArray[index] ) {  newArr.push( taskArray[index] ); }  }  
	taskArray = newArr.join();

	if(taskArray[0]==''){
		jAlert('please select any file as attachment.');
		return false;
	}

	showProgress();
	$.post("copy_file_toAttach_folder.php", {fileIds:taskArray, uniqueId:Math.random()}).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			window.location.href = "?sect=compose&msgid=0&attached=Y&dcTrans=Y";
		}
	});	
}
function downloadSelectedFiles(){
	var taskArray = new Array();
	var projectID = '<?php echo $_SESSION['idp']?>';
	var taskCount = document.allTaskTable.elements["drawingID[]"].length;

	if(taskCount === undefined){
		var taskId = document.getElementById('drawingID');
		if(taskId.checked){
			parts = taskId.value.split("###");
			fileArr = parts[0].split(".");
			fileExt = fileArr.pop();
			taskArray.push(fileArr.join("."));
		}
	}else{
		for(var i=0; i<taskCount; i++){
			var taskId = document.allTaskTable.elements["drawingID[]"][i];
			if(taskId.checked){
				parts = taskId.value.split("###");
				fileArr = parts[0].split(".");
				fileExt = fileArr.pop();
				taskArray.push(fileArr.join("."));
//				taskArray.push(parts[0]);
//				taskArray[i] = parts[0];
			}else{
				taskArray.push(0);
//				taskArray[i] = 0;
			}
		}
	}
//Filter Array
	var newArr = []; 
	for (var index in taskArray) {  if( taskArray[index] ) {  newArr.push( taskArray[index] ); }  }  
	taskArray = newArr.join();
	
	if(taskArray[0] == '' || taskArray[0] === undefined){
		jAlert('please select any file for download.');
		return false;
	}

	var curstomName = '';	
	jPrompt('Zip File Name:', '', 'Download', function(r) {
		if( r ){ curstomName = r; }
		
		$.post("download_document_register_files.php", {projectID:projectID, customName:curstomName, fileIds:taskArray, uniqueId:Math.random()}).done(function(data) {
			hideProgress();
			var jsonResult = JSON.parse(data);	
			if(jsonResult.status){
				document.location = jsonResult.url;
			}else{
				jAlert('Data updation failed, try again later');
			}
//			$('#popUpReportResponse').html(data);
		});	
	});
}
function drawingRegisterReport(){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'drawing_registration_report.php?projID='+<?=$_SESSION['idp']?>+'&name='+Math.random(), loadingImage, addUploadFile);
}
function drawingRegisterReportManager(){
	modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'drawing_registration_report_manager.php?projID='+<?=$_SESSION['idp']?>+'&name='+Math.random(), loadingImage, selectFilters);
}
function runRerporDrawingRegister(){
	var revisionType = $('#revisioinReport').val().trim();
	var sortBy = $('#sortByReport').val().trim();
	showProgress();
	$.get("drawing_registration_report.php", {projID:<?=$_SESSION['idp']?>, revisionType:revisionType, sortBy:sortBy, spectialCon:'Y', name:Math.random()}).done(function(data) {
		hideProgress();
		$('#mainContainer').html(data);
	});	
}
function selectFilters(){
	new JsDatePick({ useMode:2, target:"DAF", dateFormat:"%d-%m-%Y" });
	new JsDatePick({ useMode:2, target:"DAT", dateFormat:"%d-%m-%Y" });
}
function runRerpor(){
	var userReport = $("#userReport").val().trim();
	var searchKeywordReport = $("#searchKeywordReport").val().trim();
	var DAF = $("#DAF").val().trim();
	var DAT = $("#DAT").val().trim();
	var companyReport = $("#companyReport").val().trim();
	showProgress();
	$.post("drawing_registration_report_manager.php", {projID:<?=$_SESSION['idp']?>, userReport:encodeURIComponent(userReport), searchKeywordReport:searchKeywordReport, DAF:encodeURIComponent(DAF), DAT:encodeURIComponent(DAT), companyReport:encodeURIComponent(companyReport), uniqueID:Math.random()}).done(function(data) {
		hideProgress();
		$('#popUpReportResponse').html(data);
	});	
}
function clearDateRaised(){ $('#DAF, #DAT').val(''); }
function resetReportSearch(){ $('#userReport, #searchKeywordReport, #DAF, #DAT, #companyReport').val(''); }
function showHistory(pdfID, projID){
	var inHtml = '<div id="container"><h3 style="color:#000000;">Drawing Register History</h3><div class="demo_jui" style="width:780px; float:left;" ><table cellpadding="0" cellspacing="0" border="0" class="display" id="example_server" width="100%"><thead><tr><th width="15%">Date</th><th width="15%">User Name</th><th width="60%">Description</th></tr></thead><tbody><tr><td colspan="3" class="dataTables_empty">Loading data from server</td></tr></tbody></table></div></div>';
	$('#historyviewer').html(inHtml);
	$('#historyviewer').show();
	$('#example_server').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "drawing_history_ajax_table.php?pdfID="+pdfID+"&projectID="+projID,
		"bStateSave": true,
		"bFilter": false,
		"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 1, 2] }]
	});
}
function toggleCheck(obj){
	var checkedStatus = obj.checked;
	$('#drawingDefault tbody tr').find('td:first :checkbox').each(function () {
		if(!$(this).is(':disabled')){
			$(this).prop('checked', checkedStatus);
		}
		//$(this).prop('checked', checkedStatus);
	});
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
	var revisionType = $('#revisioinReport').val().trim();
	var sortBy = $('#sortByReport').val().trim();
	showProgress();
	$.get("pdf/drawing_registration_report_manager.php", {projID:<?=$_SESSION['idp']?>, revisionType:revisionType, sortBy:sortBy, spectialCon:'Y', name:Math.random()}).done(function(data) {
		hideProgress();
		$('#mainContainer').html(data);
	});	
}
function clearDiv(){	$('#popUpReportResponse').html('');	}
function editRevisionImages(regID, regrevID){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_drawing_register_revision.php?regID='+regID+'&regrevID='+regrevID+'&name='+Math.random(), loadingImage, afterLoadRevision);
}
function afterLoadRevision(){
	var config = {
		//support : "image/jpg,image/png,image/bmp,image/jpeg,image/gif,application/pdf,application/x-download",// Valid file formats
		support : "application/pdf",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDivDrager",// Upload Area ID
		uploadUrl: "edit_drawing_register_revision.php?antiqueID="+Math.random()// Server side upload url
	};
	initMultiUploaderRev(config);
	deadlockPDF = true;
	deadlockDWG = true;
	deadlockIMG = true;
	validator = true;
	var revision4check = "";	
}
function addNewRegisterDocumentTransmital(){
	var attr1 = $('#drawingattribute1').val();
	modalPopup_gs(align, 10, 700, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_drawing_register_document_transmital.php?&name='+Math.random()+'&attr1='+encodeURIComponent(attr1), loadingImage, afterLoadRegistrationDocumentTransmital, 1);
}
function afterLoadRegistrationDocumentTransmital(){
	var config = {
		//support : "image/jpg,image/png,image/bmp,image/jpeg,image/gif,application/pdf,application/x-download",// Valid file formats
		support : "application/pdf",// Valid file formats
		form: "addDrawingFormDocumentTransmital",// Form ID
		dragArea: "innerDivDocumentTransmital",// Upload Area ID
		uploadUrl: "add_drawing_register_document_transmital.php?antiqueID="+Math.random()// Server side upload url
	}
	initMultiUploaderDT(config, 1);
	deadlockDocumentTransmital = true;
}
$('#disableButtonDiv').live( "click", function() {
	if($('#buttonFirstSubmit').is(':disabled')){
	  jAlert("Please upload a valid document transmittal"); // jQuery 1.3+	
	  return false; 
	}
});
function donloadThis(fileName, fileType){
	var projectID = '<?php echo $_SESSION['idp']?>';
	$("#downloadFrame").attr("src", "download_document_register_files.php?projectID="+projectID+"&fileType="+fileType+"&fileName="+fileName+"&antiqueId="+Math.random());
}
var searchTable = '<table cellpadding="0" cellspacing="0" border="0" class="display" id="drawingDefaultSearch" width="100%"><thead><tr><th>id</th><th width="70px">Document Number</th><th width="200px">Document Description</th><th>Rev.</th><th>Attribute 1</th><th>Attribute 2</th><th>Tag</th><th>Status</th><th>Action</th></tr></thead><tbody><tr><td colspan="8" class="dataTables_empty">Loading data from server</td></tr></tbody></table>';

function loadSearchForm(newDrawingID, rel, iVar){
	var secAttr = $(".bulkfiles[rel='"+rel+"']").find(".drawingattribute2js").val();
	var secAttrVal = "";
	if(!secAttr){}else{
		secAttr = secAttr.filter(function(v){return v!==''});
		secAttrVal = secAttr[0];
	}
	var currValue = $('#drawingattribute1Search').val();

	var outputStr = '<option value="">Select</option>';
	TabArrTwo = subTitleArr(currValue);
	for (i=0; i<TabArrTwo.length; i++){
		outputStr += '<option value="'+TabArrTwo[i]+'"';
		if(secAttrVal == TabArrTwo[i])	outputStr += 'selected="selected"';
		outputStr += '>'+TabArrTwo[i]+'</option>';
	}
	$('#drawingattribute2Search').html(outputStr);
//Dtattable Load Here
	$('#hiddenShowDivID').val(iVar);
	$('#hiddenNewDrawingID').val(newDrawingID);
	$('#searchResultBox').show();
	$('#searchResultBox').html(searchTable);
	if(!mappedDocArr){}else{
		console.log(mappedDocArr);
		var mappedKeyArr = Object.keys(mappedDocArr);
		$('#selectedDocName').html('');
		if($.inArray(newDrawingID, mappedKeyArr) > -1){
			var index = $.inArray(newDrawingID, mappedKeyArr);
			$('#selectedDocName').html(mappedDocArr[newDrawingID][1]);
		}
/*		var otpt = "";
		for (var key in mappedDocArr) {
			if(otpt == "")
				otpt = mappedDocArr[key][1];
			else
				otpt += ", " + mappedDocArr[key][1];
		
			$('#selectedDocName').append(otpt);
		}*/
	}

	oTable = $('#drawingDefaultSearch').dataTable( {
		"iDisplayLength": 10,
		"aLengthMenu": [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "All"]
        ],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "drawing_register_data_table_search.php?selectID="+encodeURIComponent(newDrawingID)+"&dispID="+encodeURIComponent(iVar)+"&req=search&attr1="+encodeURIComponent(currValue)+"&attr2="+encodeURIComponent(secAttrVal)+"&name="+Math.random(),
		"bStateSave": true,
		/*"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			for (var key in mappedDocArr) {
				if(aData[0] == mappedDocArr[key][0])
					$(nRow).addClass('selectedDoc');
			}	
			return nRow;
		},*/
		"aoColumnDefs": [ { "bVisible": false, "aTargets": [ 0 ] }, { "bSortable": false, "aTargets": [ 8 ] }]
	} );
//	oTable.fnSort( [ [1,'asc'] ] );

}
function showMappingTable(newDrawingID, divRel, iVar){
	var attrVal = $('#drawingattribute1').val().trim();

	modalPopup_gs(align, 20, 850, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'drawing_register_mapping_search.php?&name='+Math.random()+'&attributeVal='+encodeURIComponent(attrVal), loadingImage, function(){loadSearchForm(newDrawingID, divRel, iVar);}, 2);
}
function resetSearchDynamic(){
	$('#drawingattribute1Search, #searchKeywordSearch').val('');
	$('#drawingattribute2Search').html('<option value="">Select</option>');
	$('#searchResultBox').html('');$('#searchResultBox').hide();
}
function searchDrawSearch(){
	var drawingattribute1Search = "";
	drawingattribute1Search = $('#drawingattribute1Search').val().trim();
	var drawingattribute2Search = $('#drawingattribute2Search').val().trim();
	var searchKeywordSearch = $('#searchKeywordSearch').val().trim();
	var hiddenNewDrawingID = ($('#hiddenNewDrawingID').val().trim() != "") ? $('#hiddenNewDrawingID').val().trim() : "";
	var iVar = ($('#hiddenShowDivID').val().trim() != "") ? $('#hiddenShowDivID').val().trim() : "";
	if(drawingattribute1Search == '' && drawingattribute2Search == '' && searchKeywordSearch == ''){
		jAlert('Please select any filter criteria to perform search operation ');
		return false;
	}
	$('#searchResultBox').show();
	$('#searchResultBox').html(searchTable);
	if(!mappedDocArr){}else{
		console.log(mappedDocArr);
		var mappedKeyArr = Object.keys(mappedDocArr);
		$('#selectedDocName').html('');
		if($.inArray(hiddenNewDrawingID, mappedKeyArr) > -1){
			var index = $.inArray(hiddenNewDrawingID, mappedKeyArr);
			$('#selectedDocName').html(mappedDocArr[hiddenNewDrawingID][1]);
		}
/*		var otpt = "";
		for (var key in mappedDocArr) {
			if(otpt == "")
				otpt = mappedDocArr[key][1];
			else
				otpt += ", " + mappedDocArr[key][1];
		
			$('#selectedDocName').append(otpt);
		}*/
	}
	
	var oTable = $('#drawingDefaultSearch').dataTable( {
		"iDisplayLength": 10,
		"aLengthMenu": [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "All"]
        ],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "drawing_register_data_table_search.php?selectID="+encodeURIComponent(hiddenNewDrawingID)+"&dispID="+encodeURIComponent(iVar)+"&req=search&attr1="+encodeURIComponent(drawingattribute1Search)+"&attr2="+encodeURIComponent(drawingattribute2Search)+"&searchKey="+encodeURIComponent(searchKeywordSearch)+"&name="+Math.random(),
		"bStateSave": true,
		/*"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			for (var key in mappedDocArr) {
				if(aData[0] == mappedDocArr[key][0])
					$(nRow).addClass('selectedDoc');
			}	
			return nRow;
		},*/
		"aoColumnDefs": [ { "bVisible": false, "aTargets": [ 0 ] }, { "bSortable": false, "aTargets": [ 8 ] }]
	} );
}
$('select#drawingattribute1Search').live("change", function(){
	var currValue = $(this).val();
	var outputStr = '<option value="">Select</option>';

	TabArrTwo = subTitleArr(currValue);

	for (i=0; i<TabArrTwo.length; i++){
		outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
	}

	$('#drawingattribute2Search').html(outputStr);
});

/*function selectedDocumet(newDocKey, dispID, docID, docName){//selectDrawingNameHolder
	console.log(newDocKey, docID, docName);
	console.log('#selectDrawingNameHolder_'+newDocKey);
	
	$('#selectDrawingNameHolder_'+dispID).html('<img src="images/link.png" alt="Linked" style="margin:0 5px 0 5px;width:15px;" />&nbsp;<strong>'+docName+'</strong>');
	mappingDocumentArr[newDocKey] = docID;
	mappedDocArr[newDocKey] = new Array(docID, docName);
	closePopup_gs(100, 2);
}
*/

function selectedDocumet(newDocKey, dispID, docID, pdfName, docName){//selectDrawingNameHolder
	console.log(newDocKey, dispID, docID, pdfName, docName);
	
	$('#selectDrawingNameHolder_'+dispID).html('<img src="images/link.png" alt="Linked" style="margin:0 5px 0 5px;width:15px;" />&nbsp;<strong>'+docName+'</strong>');
	mappingDocumentArr[newDocKey] = docID+'####'+pdfName;
	mappedDocArr[newDocKey] = new Array(docID, docName);
	closePopup_gs(100, 2);
}

// DWG
function bulkRegistrationDWG(){
	var config = {
		support : "",// Valid file formats
		form: "addDrawingForm",// Form ID
		dragArea: "innerDiv",// Upload Area ID
		uploadUrl: "add_drawing_files_bulk.php?antiqueID="+Math.random()// Server side upload url
	}
	mappingDocumentArr = {};
	mappedDocArr = {};
	initbulkUploaderDWG(config);
}
function bulkUpdateDwg(){
	modalPopup(align, top1, 900, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_drawing_files_bulk.php?&name='+Math.random(), loadingImage, bulkRegistrationDWG);
}
function removeBulkAttachment(DivID){
	var r = jConfirm('Are you sure you want to remove this?', null, function(r){
		if (r === true){
			$("#divId_"+DivID).hide(300);
			$(".divId_"+DivID).hide(300);			
			$("#removeId_"+DivID).val(1);
		}else{
			return false;
		}
	});
}

$('#approved_editNo').live("click", function(){ $('#unApproveReson').val(""); $('#unApproveResonHolder').show('slow'); });
$('#approved_editYes').live("click", function(){ $('#unApproveResonHolder').hide('fast'); $('#unApproveReson').val(""); });
$('#unApproveReson').live("change", function(){ $('#emailSendFlag').val(1); });


$('#projName').change(function(){
	$('#projForm').submit();
});


//Code for deleting the drawing records ------------------------------------------
function deleteAllDrawing()
{
	var len = parseInt($('#drawingDefault tbody tr').find('td:first :checkbox:checked').length);

	if(len>0)
	{
		if(confirm("Are you sure, want to delete selected record/s ?"))
		{
			showProgress();	

			var curData = "";
			var ids = "";

			$('#drawingDefault tbody tr').find('td:first :checkbox:checked').each(function()
			{
				var ids = $(this).parent().parent().find(".delids").val();
				curData = curData + "," + ids;
			});

			curData = curData.substr(1);
			curData = {ids:curData};


			$.ajax({
				url: "edit_drawing_register.php?deletedId=1",
				type: "POST",
				data: curData,
				success: function (res) 
				{
					alert(res);

					hideProgress();
					
					RefreshTable();
				}
			});	
		}
	}
	else
	{
		alert("Please select atleast one record for deletion.");
	}
}

</script>
<script type="text/javascript" src="js/modal.popup_gs.js"></script>
<script type="text/javascript" src="js/multiupload.js"></script>
<script type="text/javascript" src="js/multiupload_dwg.js"></script>
<script type="text/javascript" src="js/upload.js"></script>
<script type="text/javascript" src="js/upload_DT.js"></script>
<script type="text/javascript" src="js/upload_edit_reivison.js"></script>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<style>
.roundCorner{ border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; }
.innerDivDrager{ color:#000000; width:620px; height:150px; }
.innerDiv{ color:#000000; float:left; border:1px solid red; width:200px; height:120px; float:left;}
div#innerModalPopupDiv, div#innerModalPopupDiv1{color:#000000;}
h3#uploaderBulk{font-size:10px;padding:0;margin:0;float:left;}
.bulkfiles {clear: both;border: 1px solid #ccc;background-color: #E4E4E4;padding: 3px;position: relative;margin: 3px;z-index: 1;width: 104%;cursor: default;height: 110px;}
.bulkfilesdwg {clear: both;border: 1px solid #ccc;background-color: #E4E4E4;padding: 3px;position: relative;margin: 3px;z-index: 1;width: 98%;cursor: default;height: 50px;}
.approveDrawingReg{margin-left:0px;}
/*div#waterMark{color: #ccc;width: 100%;z-index: 0;text-align: center;vertical-align: middle;position: absolute;top: 25px;}*/
table.collapse { border-collapse: collapse; border: 1pt solid black; }
table.collapse tr, table.collapse td { border: 1pt solid black; padding: 2px; font-family:Arial, Helvetica, sans-serif; font-size:10px;}
div#htmlContainer{overflow:auto;max-height:550px;}
#revisionBox{ float:right; margin-right:5px;}
h3#uploaderBulk img{ margin-top: -15px; padding-top: 9px; display: block; }
h3#uploaderBulk span{ display: block; margin-left: 30px; margin-top: -18px; }
.Admin ul{ background-image:url(images/tab_bg.png); position:absolute; border:1px solid #435D01; border-top-right-radius:0px; border-top-left-radius:0px; border-bottom-right-radius:5px; border-bottom-left-radius:5px; border-width:0 1px 1px; top:-9999px; left:-9999px; overflow:hidden; position:absolute; padding-left:0px; z-index:2; margin-top:-7px; }
.Admin ul li{ list-style:none; float:left; }
.Admin ul li span{ font-size:14px; display:block; padding:10px; color:#000000; height:14px !important; cursor:pointer; text-decoration:underline; }
.Admin:hover ul.admindrop{ left:auto; top:auto; z-index:99999; display:block; overflow:hidden; }
ul.buttonHolder {list-style:none;}
ul.buttonHolder li {float:left;margin-left:10px;}
ul.buttonHolder li #disableButtonDiv{height: 50px;left: -5px;position: relative;top: -5px;width: 111px;z-index: 9999;}
ul#filePanel{list-style:none; margin:0px; padding:0px;}
ul#filePanel li{float:left;}
.selectedDoc{ background:#FF1717 !important; }
tr.selectedDoc td.sorting_1{ background:#FF1717 !important; }
ul.headerHolder {list-style:none;}
ul.headerHolder li{float:left; width:230px;}
.content_container {width:1200px !important;}
#drawingDefault { overflow: inherit !important;}
#drawingDefault tbody { max-height: 400px !important; overflow:auto !important;}
</style>
<iframe id="downloadFrame" src="" style="display:none; visibility:hidden;"></iframe>