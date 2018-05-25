<?php session_start();
if(!isset($_GET['bk']) && isset($_SESSION['pmb'])){
	unset($_SESSION['pmb']);
}
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$owner_id = $_SESSION['ww_builder_id'];
 ?>
<?php include'data-table.php'; ?>
<link rel="stylesheet" href="css/chosen.css">
<link href="css/email.css" rel="stylesheet" type="text/css" />
<style>
.GlobalContainer {
	color: #000;
}
table td {
	padding: 5px;
}
table {
	margin-left: 10px;
}
#DRF, #DRT, #FBDF, #FBDT {
	background: #FFF;
	cursor: default;
	height: 20px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	border-radius: 6px;
}
#show_defect {
	/*width:770px;*/
	margin-left: 20px;
}
.Compose .error {
	margin-left: 20px;
}
.chzn-drop {
	text-transform: capitalize;
}
.dataTables_wrapper {
	clear: both;
	margin-left: 10px;
	min-height: 302px;
	position: relative;
	width: 98%;
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
<!--script src="js/jquery.min.for.choosen.js" type="text/javascript"></script-->
<script src="js/chosen.jquery.js" type="text/javascript"></script>
<!-- Date Picker files start here -->
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"dateFrom",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"dateTo",
			dateFormat:"%d-%m-%Y"
		});
	};
</script>
<!-- Date Picker files start here -->
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script language="javascript" type="text/javascript">
var urlData = '';
function submitForm(){
	var recipTo = $("#recipTo").val();
	var recipFrom = $("#recipFrom").val();
	var messageType = $("#messageType").val();
	var dateFrom = $("#dateFrom").val();
	var dateTo = $("#dateTo").val();
	var searchKey = $("#searchKey").val();	
	var tags = $("#tags").val();
	var companyTag = $("#companyTag").val();		
	var referenceNo = $("#referenceNo").val();			
		
//	if(projName == ''){ jAlert('Project Name Should be Selected !'); return false;}

	if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
//	showProgress();
	var url = 'show_details_search_show_ajax.php';
	var params = "recipTo="+recipTo+"&recipFrom="+recipFrom+"&messageType="+messageType+"&dateFrom="+dateFrom+"&dateTo="+dateTo+"&searchKey="+searchKey+"&tags="+tags+"&companyTag="+companyTag+"&referenceNo="+referenceNo+"&name="+Math.random();
	//alert(params);
	
	var responseText = '<form name="inspectionTable" id="inspectionTable"><table width="100%" cellpadding="0" cellspacing="0" border="0" class="display" id="example" name="inspectionTable"><thead><th width="16%">Reference No.</th><th width="10%">From</th><th width="10%">To</th><th width="20%">Subject</th><th width="34%">Message</th><th width="5%">Type</th><th>Time</th><th width="5%">Action</th></thead><tbody></tbody></table></from>';
	
	document.getElementById("show_defect").innerHTML=responseText;
	urlData = "show_details_search_show_ajax.php?"+params;//Global variable
	oTable = $("#example").dataTable({
		"bJQueryUI": true,
		"bPaginate": true,
		"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
		"iDisplayLength": -1,
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "show_details_search_show_ajax.php?"+params,
		"bStateSave": false,
		"sScrollY": "325px",
		"aaSorting": [ [6,'desc'] ],
		"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
			if (aData[0] == 'colOpen'){ $(nRow).addClass('colOpen'); }
			if (aData[0] == 'colDraft'){ $(nRow).addClass('colDraft'); }
			if (aData[0] == 'colClosed'){ $(nRow).addClass('colClosed'); }
			if (aData[0] == 'colPending'){ $(nRow).addClass('colPending'); }
			if (aData[0] == 'colFixed'){ $(nRow).addClass('colFixed'); }
			if (aData[1] == 1){ $('#closeInsp').show('fast'); }
			return nRow;
		},
		//"aoColumnDefs": [ {  "bSearchable": false, "bSortable": false, "aTargets": [ 2, 6 ] }],
		//"aoColumns": [{ "bVisible": true}, { "bVisible": true}, null, null, null, null, null]
		//"aoColumns": [null, null, null, null, null, null, null]
	});
}
</script>
</head><body id="dt_example">
<script type="text/javascript">
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
</script>
<style>
div#spinner{ display: none; width:100%; height: 100%; position: fixed; top: 0; left: 0; background:url(images/loadingAnimation.gif) no-repeat center #CCCCCC; text-align:center; padding:10px; font:normal 16px Tahoma, Geneva, sans-serif; border:1px solid #666; z-index:2; overflow: auto; opacity : 0.8; filter: alpha(opacity = 80); }
</style>
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
  <?php include 'message_side_menu.php';  ?>
  <div class="MailRight"  <?php if($_GET['view']=='workflow'){echo "style='width:64%;'";}?>>
    <div class="MailRightHeader">
      <h3 style="color:#000000; margin-top:10px; margin-left:10px; ;"> Project Name : <?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?> </h3>
    </div>
    <?php 
			$projectUsers = $object->selQRYMultiple('u.user_id, u.user_fullname, u.company_name, user_email', 'user as u Left Join user_projects as up on u.user_id = up.user_id  and up.is_deleted=0', 'u.is_deleted=0 AND up.project_id="'.$_SESSION['idp'].'" order by u.user_name');
			
			$projectIssues = $object->selQRYMultiple('issue_to_id, issue_to_name, company_name, issue_to_email ', 'inspection_issue_to', 'is_deleted=0 AND project_id="'.$_SESSION['idp'].'" AND issue_to_name!="NA" AND issue_to_email!="" order by issue_to_name');
			
			$projectAddresBookUsers =  $object->selQRYMultiple('id, full_name, company_name, user_email', 'pmb_address_book', 'is_deleted=0 AND project_id="'.$_SESSION['idp'].'" AND full_name != "" order by full_name');
			?>
    <form action="" method="post" name="defectSearch" id="defectSearch" >
      <table width="" border="0" cellpadding="5" cellspacing="5">
        <tr>
          <td width="" align="left" valign="top" nowrap="nowrap" style="">To </td>
          <td width="" colspan="2"  valign="top" id="ShowLocation"><select name="recipTo[]" id="recipTo" style="width:280px;" class="chzn-select" multiple >
            <optgroup label="Project users">
            <?php $toList = explode(',',$_SESSION['pmb']['recipTo']);
					foreach($projectUsers as $puser){	
					$select = "";
					if(in_array($puser['user_id'], $toList)){
						$select = "selected = 'selected'";
					}else if($puser['user_id'] == $_SESSION['pmb']['recipTo']){
						$select = "selected = 'selected'";
					}
				?>
            <option value="<?php echo $puser['user_id']; ?>" <?php echo $select; ?>>
            <?php if(!empty($puser['user_fullname']) && !empty($puser['company_name'])){
							echo strtolower($puser['user_fullname']." (".$puser['company_name'].")");
						}elseif(empty($puser['user_fullname']) && !empty($puser['company_name'])){
							echo strtolower("(".$puser['company_name'].")");
						}else{
							echo strtolower($puser['user_fullname']);
						}
					?>
            </option>
            <?php  } ?>
            </optgroup>
            <optgroup label="Issued To">
            <?php foreach($projectIssues as $pIssue){	
					$select = "";
					if(in_array($pIssue['issue_to_email'], $toList)){
						$select = "selected = 'selected'";
					}else if($pIssue['issue_to_email'] == $_SESSION['pmb']['recipTo']){
						$select = "selected = 'selected'";
					}
				?>
            <option value="<?php echo $pIssue['issue_to_email']; ?>" <?php echo $select; ?>>
            <?php if(!empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])){
							echo strtolower($pIssue['company_name']." (".$pIssue['issue_to_name'].")");
						}elseif(empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])){
							echo strtolower("(".$pIssue['issue_to_name'].")");
						}else{
							echo strtolower($pIssue['company_name']);
						}
					?>
            </option>
            <?php  } ?>
            </optgroup>
            <optgroup label="Ad hoc (External)">
            <?php foreach($projectAddresBookUsers as $addresBookUsers){	
					$select = "";
					if(in_array($addresBookUsers['user_email'], $toList)){
						$select = "selected = 'selected'";
					}else if($addresBookUsers['user_email'] == $_SESSION['pmb']['recipTo']){
						$select = "selected = 'selected'";
					}
				?>
            <option value="<?php echo strtolower($addresBookUsers['user_email']);?>" <?php echo $select; ?>>
            <?php if(!empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])){
							echo strtolower($addresBookUsers['full_name']." (".$addresBookUsers['company_name'].")");
						}elseif(empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])){
							echo strtolower("(".$addresBookUsers['company_name'].")");
						}else{
							echo strtolower($addresBookUsers['full_name']);
						}
					?>
            </option>
            <?php  } ?>
            </optgroup>
            </select></td>
          <td width="10%" align="left" valign="top" nowrap="nowrap" style="">From</td>
          <td colspan="2" valign="top" align="left" ><select name="recipFrom[]" id="recipFrom" style="width:280px;" multiple class="chzn-select" multiple >
            <?php foreach($projectUsers as $puser){	
					$select = "";
					$fromList = explode(',',$_SESSION['pmb']['recipFrom']);
					if(in_array($puser['user_id'], $fromList)){
						$select = "selected = 'selected'";
					}else if($puser['user_id'] == $_SESSION['pmb']['recipFrom']){
						$select = "selected = 'selected'";
					}
				?>
            <option value="<?php echo $puser['user_id']; ?>" <?php echo $select; ?>>
            <?php if(!empty($puser['user_fullname']) && !empty($puser['company_name'])){
							echo strtolower($puser['user_fullname']." (".$puser['company_name'].")");
						}elseif(empty($puser['user_fullname']) && !empty($puser['company_name'])){
							echo strtolower("(".$puser['company_name'].")");
						}else{
							echo strtolower($puser['user_fullname']);
						}
					?>
            </option>
            <?php  } ?>
            </select></td>
        </tr>
        <tr>
          <td align="left" valign="middle" nowrap="nowrap" style="">Type</td>
          <td colspan="2"  align="right" valign="top" id="ShowRaisedBy"><?php $searchKey = isset($_SESSION['pmb']['messageType'])?$_SESSION['pmb']['messageType']:''; ?>
            <select id="messageType" name="messageType" style="width: 280px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%); border: 1px solid #AAAAAA; padding-top: 3px; height:28px;" class="chzn-choices" >
              <option value="">Select</option>
              <option value="General Correspondance" <?php echo ($searchKey=='General Correspondance')?'selected':''; ?>>General Correspondance</option>
              <option value="Inspections" <?php echo ($searchKey=='Inspections')?'selected':''; ?>>Inspections</option>
              <option value="Client Advice" <?php echo ($searchKey=='Client Advice')?'selected':''; ?>>Client Advice</option>
              <option value="Client Correspondence" <?php echo ($searchKey=='Client Correspondence')?'selected':''; ?>>Client Correspondence</option>
              <option value="Consultants Advice" <?php echo ($searchKey=='Consultants Advice')?'selected':''; ?>>Consultants Advice</option>
              <option value="Document Transmittal" <?php echo ($searchKey=='Document Transmittal')?'selected':''; ?>>Document Transmittal</option>
              <option value="Memorandum" <?php echo ($searchKey=='Memorandum')?'selected':''; ?>>Memorandum</option>
              <option value="Memo" <?php echo ($searchKey=='Memo')?'selected':''; ?>>Memo</option>                            
              <option value="Request For Information" <?php echo ($searchKey=='Request For Information')?'selected':''; ?>>Request For Information</option>
              <option value="Site Instruction" <?php echo ($searchKey=='Site Instruction')?'selected':''; ?>>Site Instruction</option>
            </select></td>
          <td align="left" valign="middle" nowrap="nowrap" style="">Date </td>
          <td colspan="2" align="left" valign="middle" nowrap="nowrap" style="">&nbsp;&nbsp;From
            <input name="dateFrom" type="text" size="10" id="dateFrom" readonly value="<?php if(isset($_SESSION['pmb']['dateFrom'])){ echo $_SESSION['pmb']['dateFrom']; }?>" />
            To
            <input name="dateTo" type="text" id="dateTo" size="10" readonly value="<?php if(isset($_SESSION['pmb']['dateTo'])){ echo $_SESSION['pmb']['dateTo']; }?>" />
            <a href="javascript:void();" title="Clear date raised"><img src="images/redCross.png" onClick="clearDateRaised();" /></a></td>
        </tr>
        <tr>
          <td align="left" valign="middle" nowrap="nowrap" style="">Keywords</td>
          <td colspan="2" align="right" valign="top" id="ShowRaisedBy"><input type="text" style="width: 273px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);
    border: 1px solid #AAAAAA; padding-left:5px;" class="chzn-choices" id="searchKey" name="searchKey" value="<?php echo isset($_SESSION['pmb']['searchKey'])?$_SESSION['pmb']['searchKey']:''; ?>"></td>
          <td align="left" valign="middle" nowrap="nowrap" style="">Tags </td>
          <td  colspan="2" align="left" valign="top" id="ShowRaisedBy"><input type="text" style="width: 273px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);
    border: 1px solid #AAAAAA; padding-left:5px;" class="chzn-choices" id="tags" name="tags" value="<?php echo isset($_SESSION['pmb']['tags'])?$_SESSION['pmb']['tags']:''; ?>"></td>
        </tr>
        <tr>
          <td align="left" valign="middle" nowrap="nowrap" style="">Reference No.</td>
          <td colspan="2" align="right" valign="top" id="ShowRaisedBy"><input type="text" style="width: 273px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);
    border: 1px solid #AAAAAA; padding-left:5px;" class="chzn-choices" id="referenceNo" name="referenceNo" value="<?php echo isset($_SESSION['pmb']['referenceNo'])?$_SESSION['pmb']['referenceNo']:''; ?>"></td>
          <td align="left" valign="middle" nowrap="nowrap" style="">Company Tag </td>
          <td  colspan="2" align="left" valign="top" id="ShowRaisedBy">
          <?php 
				$projectIssueTos = $object->selQRYMultiple('issue_to_id, issue_to_name', 'inspection_issue_to', 'is_deleted=0 AND project_id="'.$_SESSION['idp'].'" AND issue_to_name!="NA" AND issue_to_name!="" GROUP BY issue_to_name ORDER BY issue_to_name');
				$correspondencesTags =  $object->selQRYMultiple('id, correspondences_tags', 'pmb_correspondences_tags', 'is_deleted=0 AND project_id="'.$_SESSION['idp'].'" GROUP BY correspondences_tags ORDER BY correspondences_tags'); 
				$companyTagArr = array();  ?>
		    <select name="companyTag" id="companyTag" style="width: 280px; height:25px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%); border: 1px solid #AAAAAA; padding-top: 3px; height:28px;" class="chzn-choices" >
            <option value="">Select</option>
           <?php echo $companyTag = isset($_SESSION['pmb']['companyTag'])?$_SESSION['pmb']['companyTag']:'';
			   	foreach($projectIssueTos as $issueTo){	$companyTagArr[] = $issueTo['issue_to_name'];   }   
				foreach($correspondencesTags as $tag){
					if(!in_array($tag['correspondences_tags'], $companyTagArr)){ $companyTagArr[] = $tag['correspondences_tags']; }
				}
				natcasesort($companyTagArr);
				foreach($companyTagArr as $tag){	$select = "";
					if($companyTag == $tag){	$select = 'selected="selected"';	}
					echo '<option value="'.$tag.'" '.$select.' >'.$tag.'</option>';
				}
			?>
		      </select></td>
        </tr>
        <tr>
          <td colspan="2" align="left" valign="top"></td>
          <td width="269" valign="top">&nbsp;</td>
          <td  colspan="2" valign="top" ><!--input type="hidden" value="create" name="sect" id="sect" /-->
            
            <input name="SearchInsp" type="button" onClick="submitForm();" class="green_small" value="Search" id="button" value="" style="float: right;"  />
            <input type="hidden" name="sessionBack" id="sessionBack" value="Y" /></td>
        </tr>
      </table>
    </form>
    <div>
      <div class="demo_jui" id="show_defect" <?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){ echo 'style="padding-left:10px;"'; }?>></div>
      <div class="spacer"></div>
      <br>
    </div>
    <div id="spinner"></div>
  </div>
</div>
<style>
table#drawingDefault{
	color:#000000 !important;
}
div.content_container{width:100% !important;}
#example td, #example th{ vertical-align:top; padding-top:10px;	}
#recipFrom_chzn, #recipTo_chzn {margin-left:0px;}
</style>
<script type="text/javascript">
    var config = {
      '.chzn-select'           : {},
      '.chzn-select-deselect'  : {allow_single_deselect:false},
      '.chzn-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }

$(document).keypress(function(e) {
    if(e.which == 13) {
		console.log('working');
		submitForm();
    }
});
$(document).ready(function() {
<?php if(isset($_GET['bk']) && isset($_SESSION['pmb'])){
	echo 'submitForm();';
}
?>	
});
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
