<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php
//$start_session();
$tr='';
$pro_name='';
$sub_loc='';
$repairer_name='';

$builder_id=$_SESSION['ww_builder_id'];
$q = "SELECT * FROM check_list_items WHERE project_id = '".$_SESSION['idp']."' and is_deleted = 0";
$r=mysql_query($q);?>
<!-- Timer for reports -->
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
<!--// Timer for reports -->

<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" /> 
<script type="text/javascript" src="js/jquery.alerts.js"></script>

<script language="javascript" type="text/javascript">
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
<!-- Ajax Post -->
</head>
<body id="dt_example">
<div id="container">
	<!--<div class="content_hd1" style="background-image:url(images/create_reports.png);"></div>-->
	<div style="margin-top:40px;">
		<?php if(isset($_SESSION['checklist_edit'])) { ?><div class="success_r" style="width:280px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['checklist_edit'];?></p><?php unset($_SESSION['checklist_edit']); } ?>
	
	<?php if(isset($_SESSION['checklist_del'])) { ?><div class="success_r" style="width:280px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['checklist_del'];?></p><?php unset($_SESSION['checklist_del']); } ?></div>
        
		<div class="demo_jui" style="width:690px; float:left;" >
			<table width="690" cellpadding="0" cellspacing="0" border="0" class="display" id="checkListTable">
            	<thead>
                	<tr>
                    	<th nowrap="nowrap">Items Name</th>
                        <th nowrap="nowrap">Issue To</th>
                        <th nowrap="nowrap">Checklist Type</th>
                        <th nowrap="nowrap">Hold Point</th>
                    	<th nowrap="nowrap">Items Tags</th>
                        <th>Action</th>
    				</tr>
				</thead>
				<tbody>
			<?php
while($f=mysql_fetch_assoc($r)){?>
	<tr class='gradeA'>
		 <td><?php echo stripcslashes($f['check_list_items_name']);?></td>
 		 <td><?php echo stripcslashes($f['issued_to']);?></td>
 		 <td><?php echo ucwords(stripcslashes($f['checklist_type']));?></td>
 		 <td><?php echo stripcslashes($f['holding_point']);?></td>
		 <td><?php echo wordwrap(stripcslashes($f['check_list_items_tags']), 17, '<br />', true);?></td>
		 <td align='center'>
		 	<a  title='Click to edit' href='?sect=edit_checklist&id=<?php echo base64_encode($f['check_list_items_id']);?>'><img src='images/edit.png' border='none'  width="20" height="20"/></a>
			<a  title='Click to delete' href='#'  onclick="return deletechecked('Are you sure you want to delete this checklist item ?','?sect=checklist&id=<?php echo base64_encode($f['check_list_items_id']);?>');"><img src='images/remove.png' border='none'  width="20" height="20"/></a>
		</td>	
	</tr>
	<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="spacer"></div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	oTable = $('#checkListTable').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bStateSave": true
	});
} );
	
</script>
</body>
</html>
