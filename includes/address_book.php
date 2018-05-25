<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
include('includes/commanfunction.php');
$object = new COMMAN_Class(); ?>
<link href="css/email.css" rel="stylesheet" type="text/css" />
<style>
.dataTables_wrapper{
    clear: both;
    margin-left: 10px;
    min-height: 302px;
    position: relative;
    width: 98%;
}
.sorting_1{
	padding-left:26px !important;	
}
tr.gradeA td{
	line-height:30px;
}
tr.gradeA td a{
	display:block;
	color:#000;
}
table.display tr.odd.gradeA{
    background-color:#ece5e5;
}
tr.odd.gradeA td.sorting_1{
    background-color:#ece5e5;
}	
table.display tr.even.gradeA{
    background-color:#f5f4f4;
}
tr.even.gradeA td.sorting_1{
    background-color:#f5f4f4;
}
#map, #editRevision, #delRevision {
	cursor:pointer;
}
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
</head><body id="dt_example">
<div class="demo_jui" style="width:100%; float:left;" >
	<?php 
$viewType = "";
if(isset($_GET['view']) && $_GET['view'] == 'workflow'){ 
		$viewType = "&view=workflow";	
	?>
		
		<div id="leftNav" style="width:250px;float:left;">
			<?php include 'side_menu.php';?>
		</div>
		<?php }?>
 <?php //code for showing tabs
	        if($_GET['view']=='workflow'){
	        ?>
				<nav id="menu" style="margin-top:15px;margin-left:5px;">
			  <ul class="nav">
				<li><a href="pms.php?sect=messages&view=workflow"   class="active">PMB</a></li>
				<li><a href="pms.php?sect=drawing_register&view=workflow">Drawing Register</a></li>
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
<div class="GlobalContainer clearfix">
	<?php include 'message_side_menu.php'; 
	
	$unionQuery = "SELECT
						ab.full_name as name,
						ab.company_name as company,
						ab.user_phone as phone,
						ab.user_email as email,
						IF(ab.id>0, 'adHoc', '') AS type,
						ab.id
					FROM
						pmb_address_book as ab
					where
						ab.project_id = ".$_SESSION['idp']." AND is_deleted = 0 
			UNION
					SELECT
						iss.company_name as name,
						iss.issue_to_name as company,
						iss.issue_to_phone as phone,
						iss.issue_to_email as email,
						IF(iss.issue_to_id > 0, 'issuedTo', '') AS type,
						iss.issue_to_id as id
					FROM
						inspection_issue_to as iss
					WHERE
						iss.project_id = ".$_SESSION['idp']." AND is_deleted = 0 AND iss.issue_to_name!='NA' AND iss.issue_to_email!=''
			UNION
					SELECT DISTINCT
						u.user_fullname as name,
						u.company_name as company,
						u.user_phone_no as phone,
						u.user_email as email,
						IF(u.user_id > 0, 'projectUser', '') AS type,
						u.user_id as id
					FROM
						user_projects AS up, user AS u
					WHERE
						up.user_id = u.user_id AND up.project_id = ".$_SESSION['idp']." AND
						up.is_deleted = 0 AND u.is_deleted = 0";
	$queryResult = mysql_query($unionQuery);
	$dataGrid = array();
	while($data=mysql_fetch_array($queryResult)){
		$dataGrid[] = $data;	
	}
	
	?>
	<div class="MailRight" <?php if($_GET['view']=='workflow'){echo "style='width:64%;'";}?>>
		<div class="MailRightHeader">
			<h3 style="color:#000000; margin-top:10px; margin-left:10px; ;"> Project Name : <?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?> </h3>
		</div>
	
		<table width="100%" border="0">
			<tr>
				<td style="color:#000000;padding-left:10px;" >Company Name</td>
				<td style="color:#000000;padding-left:10px;">Contact Name</td>
				<td style="color:#000000;padding-left:10px;">Source</td>
				<td style="color:#000000;padding-left:10px;">Search Keyword</td>
			</tr>
			<tr>
				<td>
					<select name="company[]" id="companySearch" class="select_box" style="width: 120px; background-image: url(images/input_120.png);margin-left:0px;"  />
						<option value="">Select</option>
						<?php $disCompanyName = array();
						foreach($dataGrid as $data){ 
							if($data['company'] != ''){
								if(!in_array($data['company'], $disCompanyName))
									$disCompanyName[] = $data['company'];
							}
						}
						asort($disCompanyName);
                        foreach($disCompanyName as $key=>$compVal){?>
						<option value="<?php echo $compVal; ?>"><?php echo strtolower($compVal);?></option>
						<?php  } ?>
					</select>
				</td>
				<td>
					<select name="name[]" id="nameSearch" class="select_box"  style="width: 120px; background-image: url(images/input_120.png);margin-left:0px;"  />
						<option value="">Select</option>
						<?php $disPersonName = array();
						foreach($dataGrid as $data){ 
							if($data['name'] != ''){
								if(!in_array($data['name'], $disPersonName))
									$disPersonName[] = $data['name'];
							}
						}
						asort($disPersonName);
						foreach($disPersonName as $key=>$personVal){?>
						<option value="<?php echo $personVal; ?>"><?php echo strtolower($personVal);?></option>
						<?php }?>
					</select>
				</td>
				<td>
					<select name="source[]" id="sourceSearch" class="select_box" style="width: 120px; background-image: url(images/input_120.png);margin-left:0px;"  />
					
						<option value="">Select</option>
						<option value="projectUser">Project User</option>
						<option value="issuedTo">Issued To</option>
						<option value="adHoc">Ad hoc (External)</option>
					</select>
				</td>
				<td>
					<input type="text" name="searchKeyword" id="searchKeyword" class="input_small" style="width:120px;
background-image: url(images/input_120.png);" />
				</td>
				<td>
					<!-- <img onClick="searchDrawPdf();" style="cursor:pointer;" src="images/search_draw_reg.png" alt="search" /> -->
					<a class="green_small" href="javascript:void(0)" onclick="searchDrawPdf();" style="cursor:pointer;" alt="search" />Search</a>&nbsp;&nbsp;<img onClick="resetSearch();" style="cursor:pointer;" src="images/reset_drw_search.png" title="Reset filter" align="top"  />
				</td>
				<td>
					<!-- <div class="add_new" style="float:right;margin:10px 24px 0px 0px;" onClick="addAddressUser();"></div> -->
					<a class="green_small" href="javascript:void(0)" onclick="addAddressUser();" style="float:right;margin:10px 24px 0px 0px;" alt="search" />Add New</a>
				</td>
			</tr>
		</table>

		<div style="margin:7px 0px 0px 0px;">
			<div id="drawingDisplay"> </div>
			<br clear="all" />
		</div>
		<div class="spacer"></div>
	</div>
</div>
<?php include'data-table.php'; ?>
<style>
table#drawingDefault{ color:#000000 !important; }
div.content_container{width:100% !important;}
</style>
<script type="text/javascript" src="js/email_comman.js"></script>
<script type="text/javascript">
var params = "";
var attrTabs = '<ul id="attributeTabs" style="margin-left:-30px;"><li class="Nav"><a href="javascript:searchDrawPdf(\'Project User\');"><span>Project User</span></a></li><li><a href="javascript:searchDrawPdf(\'Issued To\');"><span>Issued To</span></a></li><li><a href="javascript:searchDrawPdf(\'Ad hoc (External)\');"><span>Ad hoc (External)</span></a></li></ul>';

var responseText = '<table cellpadding="0" cellspacing="0" border="0" class="display" id="drawingDefault" width="100%"><thead><tr><th>Contact Name</th><th>Company Name</th><th>Contact Number</th><th>Email Address</th><th>Activity</th><th>Physical Address</th><th>Group</th><th>Action</th></tr></thead><tbody><tr><td colspan="8" class="dataTables_empty">Loading data from server</td></tr></tbody></table>';

$(document).ready(function() {
//	$('#SearchTabsHide').html(attrTabs);
//	$('#SearchTabsHide').show();
	$('#drawingDisplay').html(responseText);	
	
	$('#drawingDefault').dataTable( {
		"iDisplayLength": 100,
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "address_book_data_ajax.php?attrTab=Project User&name="+Math.random(),
		"bStateSave": true,
	} );
});

function searchDrawPdf(attrTab){
	attrTab = typeof attrTab !== 'undefined' ? attrTab : 0;
	var companySearch = $('#companySearch').val().trim();
	var nameSearch = $('#nameSearch').val().trim();
	var sourceSearch = $('#sourceSearch').val().trim();
	var searchKeyword = $('#searchKeyword').val().trim();
	
	params = "companySearch="+companySearch+"&nameSearch="+nameSearch+"&sourceSearch="+sourceSearch+"&searchKeyword="+searchKeyword;
	
	//Dynamic Tabs Crations End Here
//	$('#SearchTabsHide').html(attrTabs);
//	$('#SearchTabsHide').show();
	$('#drawingDisplay').html(responseText);	
	$('#drawingDefault').dataTable( {
		"iDisplayLength": 100,
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"bRetrieve": true,
		"sAjaxSource": "address_book_data_ajax.php?"+params,
		"bStateSave": true
	});
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
	$.getJSON("address_book_data_ajax.php?attrTab=Project User&name&"+params, null, function( json ){
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
function resetSearch(){
	$('#companySearch').val('');
	$('#nameSearch').val('');
	$('#sourceSearch').val('');
	$('#searchKeyword').val('');
}
</script>
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
</script>
