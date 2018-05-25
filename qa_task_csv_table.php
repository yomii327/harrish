<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php';
#include('commanfunction.php');
#$obj = new COMMAN_Class();
$tr='';
$pro_name='';
$sub_loc='';
$repairer_name='';

$builder_id=$_SESSION['ww_builder_id'];
//$q= "SELECT * FROM progress_monitoring where project_id =	".$builder_id."";



// get all projects
?>

<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" /> 
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>

<script language="javascript" type="text/javascript">
var oTable;
var dropDownSelect;// <?php if(isset($_SESSION['qc']['listDropVal'])){ echo $_SESSION['qc']['listDropVal'].';'; }else{ echo '10;'; }?>;

	$(document).ready(function() {
		oTable = $('#example_server').dataTable( {
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "qa_task_monitoring_data_table.php",
			"bStateSave": true
		} );
	} );

function setDropValue(val){
	//AjaxShow("POST","ajaxFunctions.php?type=listDropVal&& proID="+val,"listDropVal");
}

function deletechecked(messagedeactive,linkdeactive)
{
	$.alerts.okButton = '&nbsp;Yes&nbsp;';
	$.alerts.cancelButton = '&nbsp;No&nbsp;';
	jConfirm(messagedeactive,'Delete Confirmation',function(result){
		if(result)
		{
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
	
	// display loading image.
	document.getElementById('load_defects_type').style.display = 'block';
	document.getElementById('load_repairer_name').style.display = 'block';
	
	// get defects type for this project
	$("#defects_type_div").load('defects_type_response.php?pro_id='+pro_id);
	
	// get repairer for this project
	$("#repairer_name_div").load('repairer_name_response.php?pro_id='+pro_id);
	
	setTimeout("hideImg()",1000);
}
</script>

</head>
<body id="dt_example">
	<div id="container">
		<div>
		<?php if(isset($_SESSION['progress_add'])) { ?><div class="success_r" style="width:250px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['progress_add'];?></p><?php unset($_SESSION['progress_add']); } ?>
		<?php if(isset($_SESSION['progress_update'])) { ?><div class="success_r" style="width:250px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['progress_update'];?></p><?php unset($_SESSION['progress_update']); } ?>
		<?php if(isset($_SESSION['progress_task_del'])) { ?><div class="success_r" style="width:250px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['progress_task_del'];?></p><?php unset($_SESSION['progress_task_del']); } ?>
		</div> 
		<img class="setOrderImg" src="images/order_wallchart.png" style="margin-bottom:15px;cursor:pointer;" title="Set order for Wallchart Report" />
		<div class="demo_jui" style="width:680px; float:left;" >
		<table width="680" cellpadding="0" cellspacing="0" border="0" class="display" id="example_server">
			<thead>
				<tr>
					<th nowrap="nowrap" width="120px">QA Task</th>
					<th>Location</th>
					<th>Sub Location</th>
					<th>Status</th>
					<th>Comment</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="6" class="dataTables_empty">Loading data from server</td>
				</tr>
			</tbody>
		</table>
		</div>
		<div class="spacer"></div>
	</div>
</div><div id="listDropVal"></div>
</body>
</html>