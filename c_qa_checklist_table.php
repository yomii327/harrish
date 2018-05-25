<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php';?>
<!-- Ajax Post -->
</head>
<body id="dt_example">
<div id="container">
	<!--<div class="content_hd1" style="background-image:url(images/create_reports.png);"></div>-->
	<div class="demo_jui" style="width:99%" >
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="example_server" width="100%">
			<thead>
				<tr>
					<th width="50%" nowrap="nowrap">Checklist Name</th>
					<th width="35%" nowrap="nowrap">Type</th>
					<th width="15%">Action</th>
				</tr>
			</thead>
			<tbody>
			<tr>
				<td colspan="5" class="dataTables_empty">Loading data from server</td>
			</tr>
			</tbody>
		</table>
	</div>
	<div class="spacer"></div>
</div>
</body>
</html>
<script type="text/javascript">
$(document).ready(function() {
	var oTable = $('#example_server').dataTable( {
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"iDisplayStart":0,
		"iDisplayLength":10,
		"sAjaxSource": "c_qa_checklist_data_table.php",
		"bStateSave": true,
		"aoColumnDefs": [ {  "bSearchable": false, "bSortable": false, "aTargets": [ 1 ] }],
		"bFilter": false,
	} );
	oTable.fnDraw();
} );
function deletechecked(messagedeactive,linkdeactive){
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