<?php session_start();
$js_version = rand();
#echo '<pre>';print_r($_SESSION);die;
/*if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){ ?><script language="javascript" type="text/javascript"> window.location.href="<?=HOME_SCREEN?>"; </script>
<?php }*/
#include'data-table.php';
include_once('includes/commanfunction.php');
$object = new COMMAN_Class();
if(isset($_REQUEST['id'])){
	$id=$_REQUEST['id'];
	$_SESSION['idp'] = base64_decode($_GET['id']);
}else{
	$id = ''; 
}
if(isset($_POST['projName']) && !empty($_POST['projName'])){
	$_SESSION['idp'] = $_POST['projName'];
}

$projectData = $object->selQRY('project_id,project_name, user_role', 'user_projects', 'project_id = '.$_SESSION['idp'].' AND user_id = '.$_SESSION['ww_builder_id']);
$_SESSION['project_name'] = $projectName = $projectData['project_name'];

?>
<!--script type="text/javascript"> var dropDownSelect;// = 10;</script>
<!--  Include assests css/js file start  -->
<!--link type="text/css" rel="stylesheet" href="css/splitter_style.css"/>	
<link type="text/css" rel="stylesheet" href="css/manual_style.css?version=<?=$js_version?>"/>

<link href="css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
<link href="jquery.alerts.css" rel="stylesheet" type="text/css" media="screen" /> 
<link href="css/jquery-ui.css" rel="stylesheet" type="text/css"/>

<script type="text/javascript" src="js/jqxcore.js"></script>
<script type="text/javascript" src="js/jqxsplitter.js"></script-->



<?php
$projectName=''; $locationName='';
if(isset($_GET['bk']) && $_GET['bk'] == 'Y'){
	$projectName = $_SESSION['discipline']['projName'];
	$locationName = $_SESSION['discipline']['location'];?>
<?php }else{
	unset($_SESSION['discipline']);
}
$id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']); ?>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<style>
table td{padding:5px;}
table{ margin-left:10px;}
div#spinner{ display: none; width:100%; height: 100%; position: fixed; top: 0; left: 0; background:url(images/loadingAnimation.gif) no-repeat center #CCCCCC; text-align:center; padding:10px; font:normal 16px Tahoma, Geneva, sans-serif; border:1px solid #666; z-index:2; overflow: auto; opacity : 0.8; filter: alpha(opacity = 80); }

.ul_button{
	margin: 0;
	padding: 0;
}
.ul_button li{
	margin: 0;
	padding: 0;
	display: inline-block;
	list-style: none;
}
img.action {
	width: 14px;
	height: 14px;
	float: left;
	margin: 0px 1px;
}

</style>
<!--link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>

<script type="text/javascript">
window.onload = function(){
	new JsDatePick({
		useMode:2,
		target:"DRF",
		dateFormat:"%Y-%m-%d"
	});
	new JsDatePick({
		useMode:2,
		target:"DRT",
		dateFormat:"%Y-%m-%d"
	});
	
};

function clearDateRaised(){
	$('#DRF, #DRT').val('');
}
</script-->


<?php
if (isset($_SESSION["ww_is_builder"]) and $_SESSION["ww_is_builder"]== 1){
	$owner_id = $_SESSION['ww_builder_id'];
}else{
	$owner_id = $_SESSION['ww_is_company'];
}#$owner_id = $_SESSION['ww_owner_id'];
$phd='';
$myProjects='';
$ihd='';
$myInspections='';
$accessPerArr = array('GPCL', 'GPCL - User', 'GPCL - Manager', 'All Defect');
?>
<div class="big_container" style="width:100%;">
	<div id="container">
		<?php /*<div class="content_hd1">
			<h1 class="page-title"><img src="images/email-detail.png" width="26" height="24" /> Workflow</h1>
		</div>*/ ?>
		<a href="#" onclick="addUserRolePopUp();" id="button"><div class="add_new" style="margin:0 auto;"></div></a>
		<br clear="all" />
		<div class="demo_jui">
			<table align="left" cellpadding="0" cellspacing="0" border="0" class="display" id="example_server" width="100%">
				<thead>
					<tr>
						<th width="20%" nowrap="nowrap">Rule Name</th>
						<th width="20%" nowrap="nowrap">Event</th>
						<th width="20%" nowrap="nowrap">Status</th>
						<th width="20%" nowrap="nowrap">Assign</th>
						<th width="10%">Action</th>
					</tr>
				</thead>
				<tbody>
				<tr>
					<td colspan="5" class="dataTables_empty">Loading data from server</td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<style type="text/css" title="currentStyle">
	@import "datatable/media/css/demo_table_jui.css";
	@import "datatable/examples_support/themes/smoothness/jquery-ui-1.8.16.custom.css";
</style>
<script type="text/javascript" language="javascript" src="datatable/media/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="ckeditor/ckeditor.js?version=<?=$js_version?>"></script>
	
<!--script type="text/javascript" src="js/jquery-ui-10.js"></script>
<script language="javascript" type="text/javascript" src="js/page_onm.js?version=<?=$js_version?>"></script>
<script type="text/javascript" src="js/search_onm.js?version=<?=$js_version?>"></script>
<script type="text/javascript" src="ckeditor/samples/js/sample.js?version=<?=$js_version?>"></script>
<script type="text/javascript" src="js/jquery.tree_old.js?version=<?=$js_version?>"></script>
<link type="text/css" rel="stylesheet" href="ckeditor/samples/css/samples.css?version=<?=$js_version?>"/>
<link rel="stylesheet" href="ckeditor/samples/toolbarconfigurator/lib/codemirror/neo.css"-->



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
	var flagForDisable = 0;
	
	$(document).ready(function() {
		var oTable = $('#example_server').dataTable( {
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bProcessing": true,
			"bServerSide": true,
			"iDisplayLength": 10,
			"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"sAjaxSource": "user_role_data_tables.php",
			"bStateSave": true,
			"aoColumnDefs": [ {  "bSearchable": true, "bSortable": false, "aTargets": [ 4 ] }],
			"bFilter": false,
		} );
		oTable.fnClearTable(0);
		//oTable.fnDraw();
	});


	function addUserRolePopUp(){
		flagForDisable = 0;
		width = 700;
		modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_user_role_workflow.php?&name='+Math.random(), loadingImage,calanderCallBack,submitUserRole);
	}

	function loadLocationJS1(){
		//$('ul.telefilmsHotspot').telefilmsHotspot({default_expanded_paths_string: ''});
		//$('ul.telefilms').tree({default_expanded_paths_string : '0/0/0,0/0/2,0/2/4'});
	}

	function toggleCheck(obj, tableID, PID, SPID){
		$('#selectAllCheckBox').prop('checked', false);
		SPID = typeof SPID !== 'undefined' ? SPID : '';
		var checkedStatus = obj.checked;
		$('#'+tableID+' ul').find('li:first :checkbox').each(function () {
			if(!$(this).is(':disabled')){
				$(this).prop('checked', checkedStatus);
			}
		});
	}
	
	function toggleCheckAll(obj, divID){
		var checkedStatus = obj.checked;
		$('#'+divID+' span ul').find('li:first :checkbox').each(function () {
			if(!$(this).is(':disabled')){
				$(this).prop('checked', checkedStatus);
			}
		});
	}

	function calanderCallBack(){
		loadLocationJS1();
		/*new JsDatePick({
			useMode:2,
			target:"by_when",
			dateFormat:"%d-%m-%Y"
		});*/
		var chapId = $("#all_chapter_ids").val();
		//console.log(chapId,'<<==chapId==');
		if(chapId != ''){
			$.post('add_user_role_workflow.php?parentChapter='+Math.random()+'&chapId='+chapId).done(function(data) {
				var jsonResult = JSON.parse(data);
				console.log(jsonResult,'<<==console length===');
				if(jsonResult.status){
					var chapIds = jsonResult.data;
					var dataLen = chapIds.length;
					for (var i = 0; i < dataLen; i++) {
						//console.log(chapIds[i],'<<==iiiii==');
						//expandTree(chapIds[i],flagForDisable);
						expandTreeNew(chapIds[i],flagForDisable);
					}
					//expandTree(386);
				}
			});
			//expandTree(246);
		}

		CKEDITOR.replace( 'editor1', {
			uiColor: '#F7F7F7',
			toolbar: [
				[ 'Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'Link'],
				[ 'FontSize', 'TextColor', 'BGColor' ]
			]
		});

		toggleCheck(obj='', tableID='', PID='', SPID='');
		toggleCheckAll(obj='', divID='');
	}

	function submitUserRole(opt){
		if($('#rule_name').val().trim() == ''){
			$('#errorRuleName').show('slow');return false;}else{$('#errorRuleName').hide('slow');
		}
		if($('#status').val().trim() == ''){
			$('#errorStatus').show('slow');return false;}else{$('#errorStatus').hide('slow');
		}
		var assign = '';
		/*var assign = [];
		$('#assign :selected').each(function(i, selected){
			if($(selected).val() != ''){
				assign[i] = $(selected).val();
			}
		});
		if(assign.length == 0){$('#errorAssign').show('slow');return false;}else{$('#errorAssign').hide('slow');}
		if($('#action').val().trim() == ''){$('#errorAction').show('slow');return false;}else{$('#errorAction').hide('slow');}*/

		if($("#doc_status_show").is(":visible")){
			var doc_status = [];
			$('#doc_status :selected').each(function(i, selected){
				if($(selected).val() != ''){
					doc_status[i] = $(selected).val();
				}
			});
			if(doc_status.length == 0){$('#errorDocStatus').show('slow');return false;}else{$('#errorDocStatus').hide('slow');}
		}
		var emailContent = CKEDITOR.instances.editor1.getData();

		//return false;
		showProgress();
		//console.log($('#addUserRole').serialize());

		var by_when = $('#by_when').val();
		var status = $('#status').val();
		var rule_id = $('#rule_id').val();
		var rule_name = $('#rule_name').val();
		var action = $('#action').val();
		var subject = $('#subject').val();

		var checknox_val = [];
		$('input[class="tree_checkbox"]:checked').each(function(j) {
			checknox_val[j] = this.value;
		});
		//console.log(checknox_val,'<<===cccccc======'); return false;
		
		var allData = $('#addUserRole').serialize();
		var ddd = {'assign':assign, 'by_when':by_when, 'emailContent':emailContent, 'doc_status':doc_status,'rule_name':rule_name,'status':status,'action':action, 'subject':subject,'checknox_val':checknox_val,'rule_id':rule_id};

		$.post('add_user_role_workflow.php?antiqueID='+Math.random()+'&option='+opt, ddd).done(function(data) {
			hideProgress();
			var jsonResult = JSON.parse(data);
			if(jsonResult.error){
				jAlert(jsonResult.msg);
			}else if(jsonResult.status){
				// window.location.href="?sect=o_and_m_workflow";
				window.location.href="?sect=workflow&tab=3";
			}else{
				jAlert('Data updation failed, try again later');
			}
			//RefreshTable();
		});
	}

	function editRuleData(id){
		flagForDisable = 0;
		width = 800;
		modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_user_role_workflow.php?&name='+Math.random()+'&update=1&ruleId='+id, loadingImage,calanderCallBack,submitUserRole);
	}

	function viewRuleNew(id){
		flagForDisable = 1;
		width = 800;
		modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_user_role_workflow.php?&name='+Math.random()+'&update=1&ruleId='+id+'&viewRule=1', loadingImage,calanderCallBack,submitUserRole);
	}

	function deleteRuleData(id){
		var r = jConfirm('Do you want to delete this Rule?', null, function(r){
			if(r==true){
				$.post('add_user_role_workflow.php?delete='+id).done(function(data) {
					hideProgress();
					var jsonResult = JSON.parse(data);	
					if(jsonResult.error){
						jAlert(jsonResult.msg);
					}else if(jsonResult.status){
						// window.location.href="?sect=o_and_m_workflow";
						window.location.href="?sect=workflow";
					}else{
						jAlert('Data updation failed, try again later');
					}
					//RefreshTable();
				});
			}
		});
	}

	function selectStatus(value){
		var divId = value.replace(" ", "").replace(" ", "").replace(" ", ""); 
		$("#cke_1_contents .cke_wysiwyg_frame .cke_editable").html($("#"+divId).html());
		CKEDITOR.instances.editor1.setData($("#"+divId).html()); 
		
		$("#errorAlreadyEvent").css('display','none');
		if($('#status option[value="'+value+'"]').hasClass("already-event")){
			$('#status').prop('selectedIndex',0);
			//$("#doc_status_show").hide();
			$("#errorStatus").hide();
			$("#errorAlreadyEvent").css('display','block');
			return false;
		}else{
			if(value == 'Document Status Changed'){
				//$("#doc_status_show").show();
			}else{
				//$("#doc_status_show").hide();
			}
		}
	}

	function expandTree(cId){
		var expanded_array = new Array();
		var animate = 'fast';
		var correct = 'li_'+cId;
		var sam = 'ready';
		var a1 = cId;
		var rule_id = $("#rule_id").val();
		var open_char = 'images/minus-icon.png';
		var close_char = 'images/plus-icon.png';
		var o = {};
		var url="workflow_ajax_chapter_tree.php?hotspotparentID="+Math.random()+"&rule_id="+rule_id+"&flagForDisable="+flagForDisable;
		deadLockHotSpot = true;
		showProgress();
		jQuery.ajax({
			type: "POST",
			url: url,
			data: "cetid="+a1,
			cache:false,
			success: function(data){
				hideProgress();
				if(a1==0){
					//Check here data is present or not
					//jQuery("#ajaxManualTree"+correct).html(data);
					jQuery("#ajaxHotspotTree").html(data);
					deadLockHotSpot = false;	
				}else{
					if(expanded_array[correct]!=1){
						jQuery("#"+correct).append(data);
					}
					jQuery('#'+correct).children('ul').show(animate); 
					jQuery('#'+correct).children('span').removeClass('close');
					jQuery('#'+correct).children('span').addClass('open');
					// if(sam!='ready'){
					// 	jQuery('#'+correct).children('span.jtree-arrowHotspot').find('img').attr('src',open_char);
					// }else{
					// 	jQuery('#'+correct).children('span.jtree-arrowHotspot').find('img').attr('src',close_char);	
					// }
					expanded_array[correct] = 1;
					deadLockHotSpot = false;	
				}

			} // AJAX SUCSESS FUNCTION CLOSE
		}); // AJAX CLOSE
	}

	function expandTreeNew(cId,flagForDisable){
		var correct = 'li_'+cId;
		var correct1 = 'ul_'+cId;
		$("#"+correct).find('span:first').removeClass('close').addClass('open');
		$("#"+correct).find('span:first').find('img').attr('src','images/minus-icon.png');
		$("."+correct1).css('display','block');
	}

</script>

<style>
#innerModalPopupDiv{color:#000000;}
ul.headerHolder { list-style:none; margin:0; padding:0; }
ul.headerHolder li { float:left; width:100px;}


ul.buttonHolder {list-style:none;}
ul.buttonHolder li {float:left;margin-left:10px;}
ul.buttonHolder li #disableButtonDiv{height: 50px;left: -5px;position: relative;top: -5px;width: 111px;z-index: 9999;}
ul#filePanel{list-style:none; margin:0px; padding:0px;}
ul#filePanel li{float:left;}
ul#saveSearchHolder{list-style: none;}
.manual_document_header{color:#000}

.btn-default { 
    background: rgba(0, 0, 0, 0) linear-gradient(to bottom, #b6ff00 1%, #95cf06 7%, #5b8704 99%, #74a104 100%) repeat scroll 0 0;    
    border: 1px solid #74a104;    
    border-radius: 10px;    
    box-shadow: 0 1px 0 rgba(0, 0, 0, 0.75);    
    color: #fff;    
    cursor: pointer;    
    display: inline-block;    
    font-family: "MyriadPro-SemiboldIt";    
    font-size: 14px;    
    font-weight: bold;    
    line-height: normal;    
    padding: 8px 20px;    
    text-decoration: none;    
    text-shadow: 0 1px 1px #000;
}

</style>

<!-- <script type="text/javascript" src="js/jquery-ui-10.js"></script>
<script type="text/javascript" src="js/jscolor/jscolor.js"></script> -->
<!--script type="text/javascript" src="js/jquery.contextMenu.js?version=<?=$js_version?>"></script>
<script type="text/javascript" src="js/o_and_m_workflow.js?version=<?=$js_version?>"></script>
<!-- <script type="text/javascript" src="js/workflow_tree.js?version<?=$js_version?>"></script> -->
<!-- <script type="text/javascript" src="js/hotspot/raphael.js?version<?=$js_version?>"></script>
<script type="text/javascript" src="js/hotspot/raphael.sketchpad.js?version<?=$js_version?>"></script> -->
