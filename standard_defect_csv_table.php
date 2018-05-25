<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php'; 
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
$builder_id = $_SESSION['ww_builder_id'];
if(isset($_REQUEST['id'])){
	$update = 'UPDATE standard_defects SET is_deleted = 1, last_modified_date = now(), last_modified_by = '.$builder_id .' WHERE standard_defect_id = "'.base64_decode($_REQUEST['id']).'"';
	mysql_query($update);
	$_SESSION['std_defect_delete'] = "Standard defect deleted successfully.";
	header('loaction:?sect=standard_defect');
}
?>
<script type="text/javascript">
var c=0;
var t;
var timer_is_on=0;
function stopCount1(){
    t=0; c=0;
	clearTimeout(t);
	clearTimeout(c);
}
function timedCount(){
	document.getElementById('report_timer').innerHTML = 'Beats : '+c;
	c=c+1;
	t=setTimeout("timedCount()",1000);
}
function doTimer(action){
	if(action='start'){
		if(!timer_is_on){
			timer_is_on=1;
			timedCount();
		}
	}else{
		
	}
}
</script>
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" /> 
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<script language="javascript" type="text/javascript">
function deletechecked(messagedeactive,linkdeactive){
	$.alerts.okButton = '&nbsp;Yes&nbsp;';
	$.alerts.cancelButton = '&nbsp;No&nbsp;';
	jConfirm(messagedeactive,'Delete Confirmation',function(result){
		if(result){
			window.location = linkdeactive;
		}
	});
	$.alerts.okButton = '&nbsp;OK&nbsp;';
	$.alerts.cancelButton = '&nbsp;Cancel&nbsp;';
	return false;
}
function getData(){
	var pro_id = document.getElementById('pro_name').value;
	document.getElementById('create_response').innerHTML='';
	document.getElementById('load_defects_type').style.display = 'block';
	document.getElementById('load_repairer_name').style.display = 'block';
	$("#defects_type_div").load('defects_type_response.php?pro_id='+pro_id);
	$("#repairer_name_div").load('repairer_name_response.php?pro_id='+pro_id);
	setTimeout("hideImg()",1000);
}

function hideImg(){
	document.getElementById('load_defects_type').style.display = 'none';
	document.getElementById('load_repairer_name').style.display = 'none';
}

function getPDF(myId){
	window.location.href="<?=HOME_SCREEN.'pms.php?sect=load_report'?>";	
}
</script>
</head>
<body id="dt_example">
<div id="container">
	<div style="margin-top:40px;">
		<?php if(isset($_SESSION['std_defect_edit'])) { ?>
			<div class="success_r" style="width:300px;margin:3px;margin-left:158px;">
				<p><?php echo $_SESSION['std_defect_edit'];?></p>
			</div>
		<?php unset($_SESSION['std_defect_edit']); } ?>
		<?php if(isset($_SESSION['standard_defect_add'])) { ?>
			<div class="success_r" style="width:300px;margin:3px;margin-left:158px;">
				<p><?php echo $_SESSION['standard_defect_add'];?></p>
			</div>
		<?php unset($_SESSION['standard_defect_add']); } ?>
        <?php if(isset($_SESSION['std_defect_delete'])) { ?>
			<div class="success_r" style="width:300px;margin:3px;margin-left:158px;">
				<p><?php echo $_SESSION['std_defect_delete'];?></p>
			</div>
		<?php unset($_SESSION['std_defect_delete']); } ?>
    	<!-- <a href="?sect=add_standard_defect"><div class="add_new" style="margin-right:44px;margin-top:-35px;"></div></a> -->
    	<a href="?sect=add_standard_defect" class="green_small" style="margin-right:44px;margin-top:-35px;float:right;">Add New</a>
		<div class="demo_jui" style="width:690px; float:left;" >
			<table width="690" cellpadding="0" cellspacing="0" border="0" class="display" id="stDefectTable">
				<thead>
					<tr>
						<th nowrap="nowrap">Description</th>
						<th>Issue To</th>
						<th>Tags</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
		<?php $sdData = $obj->selQRYMultiple('standard_defect_id, description, tag, issued_to', 'standard_defects', 'project_id = "'.$_SESSION['idp'].'" AND is_deleted=0');
			if(!empty($sdData)){
				foreach($sdData as $standData){?>
					<tr class='gradeA'>
						<td><?php echo wordwrap($standData['description'], 60, "<br />\n", true); ?></td>
						<td><?php echo wordwrap($standData['issued_to'], 60, "<br />\n", true); ?></td>
						<td><?php echo wordwrap($standData['tag'], 60, "<br />\n", true); ?></td>
						<td align='center'>
							<a  title='Click to edit' href='?sect=edit_standard_defect&id=<?php echo base64_encode($standData['standard_defect_id']);?>'><img src='images/edit.png' border='none'   width="20" height="20"/></a>
							<a  title='Click to delete' href='#'  onclick="return deletechecked('Are you sure you want to delete this standard defect?','?sect=standard_defect&id=<?php echo base64_encode($standData['standard_defect_id']);?>');"><img src='images/remove.png' border='none'  width="20" height="20" /></a>
						</td>	
					</tr>
			<?php }
			}?>
				</tbody>
			</table>
		</div>
		<div class="spacer"></div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	oTable = $('#stDefectTable').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": true
	});
} );
	
</script>
</body>

</html>