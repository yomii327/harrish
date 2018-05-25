<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
include'data-table.php'; 
include_once("commanfunction.php");
session_start();

$obj = new COMMAN_Class(); 
if(isset($_REQUEST['id'])){
	$id=$_REQUEST['id'];
	$_SESSION['project_id']=$id;
	$_SESSION['idp'] = base64_decode($_GET['id']);
}else
	$id = ''; 
$err_msg = '';//insert for Assign inspector
$permArray = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Architect');
if(isset($_POST['projName']) && !empty($_POST['projName'])){
	$_SESSION['idp'] = $_POST['projName'];
}?>
	<input type="hidden" value="<?php echo $_SESSION['idp']?>" id="project_Id" name="project_Id">
<script type="text/javascript" src="js/page_dr.js?version=<?=$js_version?>"></script>
<style>
.roundCorner{ border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; }
.innerDivDrager{ color:#000000; width:620px; height:150px; }
.innerDiv{ color:#000000; float:left; border:1px solid red; width:300px; height:120px; float:left;}
div#innerModalPopupDiv, div#innerModalPopupDiv1{color:#000000;}
h3#uploaderBulk{font-size:10px;padding:0;margin:0;float:left;}
.bulkfiles {clear: both;border: 1px solid #ccc;background-color: #E4E4E4;padding: 3px;position: relative;margin: 3px;z-index: 1;width: 98%;cursor: default;height: 110px;}
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
	/*div#middle{background: url(images/gray_bg.png) center repeat-y !important;background-position-x: -435px!important;background-color:rgba(0, 0, 0, 0) !important;}*/

.tblHeader {
	position: absolute;
	width: 99%;	
}
table#drawingDefault { width: 100%; }
#drawingDefault tbody td{
	max-width: 103px;
	height: 15px;
	text-overflow: ellipsis;
	white-space: nowrap;
	overflow: hidden;
}
#drawingDefault thead {
	/*position: absolute;
	top: 33px;*/
}

	<?php if($_GET['view']!='workflow'){?>
	div#middle{background: <?php if(isset($_GET['sect']) && $_GET['sect'] != 'drawing_register'){?>url(images/gray_bg.png)<?php } ?> center repeat-y !important; background-position-x: -435px!important;background-color:#FFFFFF !important;}
	<?php } ?>
<?php if(isset($_GET['type']) && $_GET['type'] == 'pmb'){?>
	div.content_container{ width:100% !important; }
<?php }else{?>
	.SearchTabs li a span{background: url("images/selected_right_pc.png") no-repeat scroll right center rgba(0, 0, 0, 0) !important;}
	.SearchTabs li a span:hover{ background:url('images/active_right_pc.png') no-repeat scroll right center rgba(0, 0, 0, 0) !important;}
<?php }?>
.selectedDoc{ background:#FF1717 !important; }
tr.selectedDoc td.sorting_1{ background:#FF1717 !important; }
ul.headerHolder {list-style:none;}
ul.headerHolder li{float:left; width:230px;}

.actionButton{float:right; margin:9px 4px 10px 0px;cursor:pointer;}
/*#drawingDisplay tr th, #drawingDisplay tr td{word-break:break-all;}*/
table.display thead th div.DataTables_sort_wrapper {   padding-right: 16px;	}
.big_container{width:1200px !important;}
<?php if($_GET['view']=='workflow'){?>
	.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
.box1 { background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
.link1 { background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
a.link1:hover { background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
.txt13 { border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }
/* onto CSS now */
@import url(http://fonts.googleapis.com/css?family=Open+Sans:600);

body {
	padding: 20px;
	background: whiteSmoke;
	font-family: 'Open Sans';
}

#menu {
	text-align: center;
}

.nav {
	list-style: none;
	display: inline-block; /* for centering */
	/*border: 1px solid #b8b8b8;*/
	font-size: 14px;
	margin: 0; padding: 0;
}

.nav li {
	border-left: 1px solid #b8b8b8;
	float: left;
}
.nav li:first-child {
	border-left: 0;
}

.nav a {
	color: #2f2f2f;
	padding: 0 20px;
	line-height: 32px;
	display: block;
	text-decoration: none;
	
	background: #fbfbfb;
	background-image: linear-gradient(#fbfbfb, #f5f5f5);
}

.nav a:hover {
	background: #fcfcfd;
	background-image: linear-gradient(#fff, #f9f9f9);
}

.nav a.active,
.nav a:active {
	background: #94CE06;
	/*background-image: linear-gradient(#e8e8e8, #f5f5f5);*/
}


/* Tab Panes now */

#tab_panes {
	max-width: 600px;
	margin: 20px auto;
}

.tab_pane {
	display: none;
}
.tab_pane.active {
	display: block;
}

#tab_panes img {
	max-width: 600px;
	box-shadow: 0 0 5px rgba(0,0,0,0.5);
}
<?php }?>
</style>
	<?php if($_GET['view']=='workflow'){
		$width = "width:1154px;";
	 }?>	
	<div id="middle" <?php if($_GET['type'] == 'pmb'){?>class="gray_pmb" <? }?> style="padding-top:10px;margin-left: -100px;<?php echo $width;?>">
		<?php if(!isset($_GET['type']) && $_GET['type'] != 'pmb'){?>
		<div id="leftNav" style="width:250px;float:left;">
			<?php include 'side_menu.php';?>
		</div>
		<?php }?>
<?php $id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']); ?>
		<div id="rightCont"  style="background: none repeat scroll 0 0 gray;<?php if(!isset($_GET['type']) && $_GET['type'] != 'pmb'){ ?> width:700px;float:left;<? }else{?>width:980px;margin:0 auto;<?php }?> <?php if($_GET['view']=='workflow'){?>width:904px;<?php }?>">
	        <?php //code for showing tabs
	        if($_GET['view']=='workflow'){
	        ?>
				<nav id="menu" style="margin-top:15px;margin-left:5px;">
			  <ul class="nav">
				<li><a href="pms.php?sect=messages&view=workflow">PMB</a></li>
				<li><a href="pms.php?sect=drawing_register&view=workflow"  class="active">Drawing Register</a></li>
			 </ul>
			</nav>

			<!-- we're done with the tabs, onto panes now -->

			<section id="tab_panes">
				<div class="tab_pane active">
					
				</div>
				
				<!-- we'll copy/paste the other panes -->
				<div class="tab_pane">
					
				</div>
			</section>	
	        <?php } ?>
	        <?php  if($_GET['view']=='workflow'){ ?>
	        <div style="background:gray; ">
			<?php } ?>	
	        <ul class="headerHolder">
	            <li style="width:350px;"><img src="images/document-register.png" name="" id=""  /></li>
				<?php if(isset($_GET['type']) && $_GET['type'] == 'pmb'){?>
				<li style="text-align:right;padding-top:20px;color:#000000;">Project:</li>
                <li style="text-align:right;padding-top:10px;">
                	<form action="" name="projForm" id="projForm" method="post">
	                <select name="projName" id="projName" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					<?php if(isset($_SESSION['ww_is_company'])){
							$q = "SELECT project_id, project_name FROM user_projects WHERE is_deleted=0 and is_pdf = 1 GROUP BY project_name";
							$res = mysql_query($q);
							$prIDArr = array();
							$outPutStr = "";
							while($q1 = mysql_fetch_array($res)){
								if(!isset($_SESSION['idp']))
									$_SESSION['idp'] = $q1[0];
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
							$q = "SELECT project_id, project_name FROM user_projects WHERE user_id = '".$_SESSION['ww_builder_id']."' and is_deleted = 0 and is_pdf = 1 GROUP BY project_name";
							$res = mysql_query($q);
							$prIDArr = array();
							$outPutStr = "";
							while($q1 = mysql_fetch_array($res)){
								if(!isset($_SESSION['idp']))
									$_SESSION['idp'] = $q1[0];
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
                <?php }?>
			</ul><br /><br clear="all" />
			<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;">
				<div class="success_r" id="outPutResult" style="height:35px;width:400px;display:none;"><p id="outPutResultPara"></p></div>		
			</div>
			<div class="big_container" <?php if(!isset($_GET['type']) && $_GET['type'] != 'pmb'){?>style="width:722px;"<? }?>>
	    	
	    	<div id="searchDraw" style=" display:none;  background:gray;<?php if(!isset($_GET['type']) && $_GET['type'] != 'pmb'){?>width:690px;<? }?>border:1px solid <?php if($_GET['type'] == 'pmb'){echo '#818080';}?>;padding:5px;">
				<table width="100%" border="0">
					<tr>
						<td style="padding-left:10px;<?php if(!isset($_GET['type']) && $_GET['type'] != 'pmb'){echo '';}?>" >Attribute 1</td>
						<td style="padding-left:10px;<?php if(!isset($_GET['type']) && $_GET['type'] != 'pmb'){echo '';}?>">Attribute 2</td>
						<td style="padding-left:10px;<?php if(!isset($_GET['type']) && $_GET['type'] != 'pmb'){echo '';}?>">Search Keyword</td>
						<td style="padding-left:10px;<?php if(!isset($_GET['type']) && $_GET['type'] != 'pmb'){echo '';}?>">Status</td>
					<?php $approveArr = array('General Consultant', 'Architect');
					if(!in_array($_SESSION['userRole'], $approveArr)){?>
						<td style="padding-left:10px;<?php if(!isset($_GET['type']) && $_GET['type'] != 'pmb'){echo '';}?>">Download on iPad</td>
					<?php }?>
                    	<td style="padding-left:10px;"><img onclick="resetSearch();" style="cursor:pointer; float:right;" src="images/reset_drw_search.png" title="Reset filter" align="top"  /></td>
					</tr>
					<tr>
						<td>
							<select name="drawingattribute1" id="drawingattribute1Default" class="select_box" style="width: 120px;
	background-image: url(images/input_120.png);margin-left:0px;"  />
								<option value="">Select</option>
							<?php $attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Civil / Landscaping', 'ESD / Green Star', 'Survey');
							for($i=0;$i<sizeof($attribute1Arr);$i++){?>
								<option value="<?=$attribute1Arr[$i]?>" <? if($attribute1Arr[$i] == $drawData[0]['attribute1'])echo 'selected="selected"';?> ><?=$attribute1Arr[$i]?></option>
						<?php }?>
							</select>
						</td>
						<td>
							<select name="drawingattribute2" id="drawingattribute2Default" class="select_box" style="width: 120px;
	background-image: url(images/input_120.png);margin-left:0px;"  />
								<option value="">Select</option>
							</select>
						</td>
						<td>
							<input type="text" name="searchKeyword" id="searchKeyword" class="input_small" style="width: 150px;
background-image: url(images/input_160.png);" />
						</td>
						<td>
							<select name="pdfStatus" id="pdfStatus" class="select_box" style="width: 120px;
	background-image: url(images/input_120.png);margin-left:0px;"  />
								<option value="">Select</option>
								<option value="Tender">Tender</option>
								<option value="Issued for Construction">Issued for Construction</option>
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
			<?php  if($_GET['view']=='workflow'){ ?>
			</div>
			<?php }?>	
				</table>
			</div>
			<div id="toolbar">
		<?php if($_SESSION['userRole'] == 'Subcontractor - Tender'){?>
			<img src="images/download_selected.png" onclick="downloadSelectedFiles(<?=$_SESSION['idp']?>);" class="actionButton"  />
			<img src="images/document_register_btn.png" onclick="drawingRegisterReport(<?=$_SESSION['idp']?>);" class="actionButton"  />
		<?php }else{
			if($_SESSION['userRole'] != 'Tender'){?>
			<img src="images/add_new.png" onclick="addNewRegister();" class="actionButton"  />
			<img src="images/bulk_upload.png" onclick="bulkUploadRegisters();" class="actionButton"  />
			<img src="images/download_selected.png" onclick="downloadSelectedFiles(<?=$_SESSION['idp']?>);" class="actionButton"  />
		<?php if(isset($_GET['type']) && $_GET['type'] == 'pmb' && $_SESSION['userRole'] != 'General Consultant'){?>
            <img src="images/document_transmittal.png" onclick="completeAttachment(<?=$_SESSION['idp']?>);" class="actionButton"  />
		<?php }else{
				if($_SESSION['userRole'] != 'General Consultant'){?>
					<img src="images/approve.png" onclick="approvedSelected(<?=$_SESSION['idp']?>);" class="actionButton"  />
		<?php 	}
			}?>
			<img src="images/document_register_btn.png" onclick="drawingRegisterReport(<?=$_SESSION['idp']?>);" class="actionButton"  />
		<?php }else{?>
			<img src="images/download_selected.png" onclick="downloadSelectedFiles(<?=$_SESSION['idp']?>);" class="actionButton"  />	
		<?php }?>
		<?php if($_SESSION['ww_builder']['user_type'] == 'manager'){ ?>
			 <img src="images/report_btn.png" onclick="drawingRegisterReportManager(<?=$_SESSION['idp']?>);"  class="actionButton" />
	   <?php }?>
	   	 <!--<img src="images/approve.png" onclick="approvedSelected(<?=$_SESSION['idp']?>);" class="actionButton" />-->
			 <img src="images/approve.png" onclick="bulkApproval();" class="actionButton" />
             <img src="images/merge.png" class="actionButton" onclick="mergeSelectedData()" <?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){echo "style='display:none;'"; }?>>
		
	   <?php }?>
			 
		       <!--img src="images/pdf_markers.png" onclick="showPreviousMarkups(0, <?=$_SESSION['idp']?>);" class="actionButton" /-->
			 <br clear="all" />
			 <div class="SearchBox">
				<div class="SearchTabs" id="SearchTabsHide" style="width:780px;display:none;<?php if(isset($_GET['type']) && $_GET['type'] == 'pmb'){echo 'width:1250px';}?>"></div>
			</div>
			<div style="clear:both"></div>
			</div> <!-- /.toolbar -->
				<div>
					<div id="drawingDisplay" style="width:auto"></div> 
					<br clear="all" />
				</div>
				<div class="spacer"></div>
			</div>
		</div>
	</div>

<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
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

$(document).ready(function() {
	$('#SearchTabsHide').html(attrTabs);
	$('#SearchTabsHide').show();
	$('#drawingDisplay').html(responseText);	
	console.log(responseText);
	
	//scroll mouse.
	var offSet = 250;
	$(window).scroll(function(){
		if($(this).scrollTop() > offSet) {
			var params = {
				"position": "fixed",
				"top": '0',
				"z-index": '1',
				"border-bottom-width": '1px'
			};
			$('#toolbar').css(params);
			var headParams = {
				"position": "fixed",
				"width": "79%",
				"top": "101px"
			};
			$('.tblHeader').css(headParams);
			var theadParams = {
				"position": "fixed",
				//"top": "134px"
			};
			$('#drawingDefault thead').css(theadParams);
		} else {
			var params = {
				"position": 'initial',
				"top": 'auto',
				"z-index": '0',
				"border-bottom-width": '0'
			};
			$('#toolbar').css(params);
			var headParams = {
				"position": "absolute",
				"width": "100%",
				"top": "0"
			};
			$('.tblHeader').css(headParams);
			var theadParams = {
				"position": "relative",
				// "top": "33px"
			};
			$('#drawingDefault thead').css(theadParams);
		}
	});
	
	var attribute1Arr = "General";
	<?php if($_SESSION['userRole'] == 'Architect'){?>
			attribute1Arr = 'Architectural';
	<?php 	}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Structural') || ($_SESSION['userRole'] == 'Structural Engineer')){?>
			attribute1Arr = 'Structure';
	<?php 	}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Services') || ($_SESSION['userRole'] == 'Services Engineer')){?>
			attribute1Arr = 'Services';
	<?php 	}else{?>
			attribute1Arr = 'General';
	<?php 	}
	if($_SESSION['userRole'] == 'Lighting Consultant'){?>
		attribute1Arr = 'Lighting';
	<?php }?>
	<?php if($_SESSION['userRole'] == 'Tenancy Fitout'){?>
		attribute1Arr = 'Tenancy Fitout';
	<?php }
	if($_SESSION['userRole'] == 'Penthouse Architecture'){?>
		attribute1Arr = 'Penthouse Architecture';
	<?php }
	if($_SESSION['userRole'] == 'Landscaping'){?>
		attribute1Arr = 'Landscaping';
	<?php }?>
	<?php if($_SESSION['idp']==242){?>
		attribute1Arr = 'Architectural';
	<?php } ?>	
	 var oTable = $('#drawingDefault').dataTable( {
		"iDisplayLength": -1,
		"aLengthMenu": [
			[10, 25, 50, 100, -1],
			[10, 25, 50, 100, "All"]
        ],
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "drawing_register_data_table_v1.php?"+requestType+"attr1="+encodeURIComponent(attribute1Arr)+"&name="+Math.random(),
		"bStateSave": true,
		"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			console.log(aData[0]);
			if($.inArray(aData[0], closeIdArr) > -1)
				$(nRow).children().children('#drawingID_'+aData[0]).prop('checked', true);

			if($('#checkall').is(':checked')){
				$('#checkall').prop('checked', false);
			}
			return nRow;
		},
		"aoColumnDefs": [{  "bVisible": false, "aTargets": [ 0 ] }, { "bSortable": false, "aTargets": [ 0, 1, 13] }]
	} );
	oTable.fnDisplayStart( 0 );
	//	oTable.fnSort( [ [1,'asc'] ] );
	
	$('#drawingDefault_wrapper > div:first-child').addClass('tblHeader');
	$('.tblHeader').after('<div style="height: 33px;"></div>');
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
		//alert(1);
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
		"sAjaxSource": "drawing_register_data_table_v1.php?"+params,
		"bStateSave": true,
		"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			console.log(aData[0]);
			if($.inArray(aData[0], closeIdArr) > -1)
				$(nRow).children().children('#drawingID_'+aData[0]).prop('checked', true);

			if($('#checkall').is(':checked')){
				$('#checkall').prop('checked', false);
			}
			return nRow;
		},
		"aoColumnDefs": [{  "bVisible": false, "aTargets": [ 0 ] }, { "bSortable": false, "aTargets": [ 0, 1, 9] }]
	} );
	//oTable.fnSort( [ [1,'asc'] ] );
	oTable.fnDisplayStart( 0 );
	
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
		"sAjaxSource": "drawing_register_data_table_v1.php?"+params,
		"bStateSave": true,
		"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			console.log(aData[0]);
			if($.inArray(aData[0], closeIdArr) > -1)
				$(nRow).children().children('#drawingID_'+aData[0]).prop('checked', true);

			if($('#checkall').is(':checked')){
				$('#checkall').prop('checked', false);
			}
			return nRow;
		},
		"aoColumnDefs": [{  "bVisible": false, "aTargets": [ 0 ] }, { "bSortable": false, "aTargets": [ 0, 1, 9] }]
	} );
	oTable.fnDisplayStart( 0 );
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
$('select#drawingattribute1Default').change(function(){
	var currValue = $(this).val();
	var outputStr = '<option value="">Select</option>';

	TabArrTwo = subTitleArr(currValue);

	for (i=0; i<TabArrTwo.length; i++){
		outputStr += '<option value="'+TabArrTwo[i]+'">'+TabArrTwo[i]+'</option>';
	}

	$('#drawingattribute2Default').html(outputStr);
});

var requestType = "";
<?php if(isset($_GET['type']) && $_GET['type'] == 'pmb'){?>
	requestType = "req=pmb&";
<?php }?>

$('#projName').change(function(){$('#projForm').submit();});
</script>
<script type="text/javascript" src="js/modal.popup_gs.js"></script>
<script type="text/javascript" src="js/multiupload_v1.js"></script>
<script type="text/javascript" src="js/upload_v1.js"></script>
<script type="text/javascript" src="js/upload_DT.js"></script>
<script type="text/javascript" src="js/upload_edit_reivison_v1.js"></script>

<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<iframe id="downloadFrame" src="" style="display:none; visibility:hidden;"></iframe>

<!--script src="markup_tool/js/jquery.js" type="text/javascript"></script-->
<script src="markup_tool/js/react-with-addons.js" type="text/javascript"></script>
<script src="markup_tool/js/literallycanvas.js" type="text/javascript"></script>
<link rel="stylesheet" href="markup_tool/css/literallycanvas.css" type="text/css" />
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
<script>
// On to the interactiveness now :)

$(function() {
	$('.nav a').on('click', function() {
	
		var $el = $(this);
		var index = $('.nav a').index(this);
		var active = $('.nav').find('a.active');
		
		/* if a tab other than the current active
		tab is clicked */
		
		if ($('nav a').index(active) !== index) {
			
			// Remove/add active class on tabs
			active.removeClass('active');
			$el.addClass('active');
			
			
			// Remove/add active class on panes
			$('.tab_pane.active')
				.hide()
				.removeClass('active');
			$('.tab_pane:eq('+index+')')
				.fadeIn()
				.addClass('active');
			
			// we can also add some quick fading effects
			
			// now that's awesome! you got
			// fancy stylish css3 tabs for your
			// next project ;)
		}
	});

}());
function bulkApproval(){
	console.log('bulkApproval');
	var taskArray = new Array();
	var projectID = '<?php echo $_SESSION['idp']?>';
	var taskCount = document.allTaskTable.elements["drawingID[]"].length;

	if(taskCount === undefined){
		var taskId = document.getElementById('drawingID');
		var taskId = document.allTaskTable.elements["drawingID[]"]
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
console.log(taskArray);
	if(taskArray[0]=='' || taskArray==''){
		jAlert('please select any file as for approve.');
		return false;
	}

	showProgress();
	$.post("edit_drawing_register_v1.php", {fileIds:taskArray, singleId:Math.random()}).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			jAlert(jsonResult.msg);
			RefreshTable();
		}
	});	
}
</script>
<style>/*.big_container{width:1200px !important;}*/</style>
