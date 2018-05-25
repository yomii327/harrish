<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php'; ?>
<?php
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

</head>
<body id="dt_example">
<div id="container">
	
	
	
	
	
	<div style="margin-top:40px;">
		<?php if(isset($_SESSION['progress_add'])) { ?><div class="success_r" style="width:250px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['progress_add'];?></p><?php unset($_SESSION['progress_add']); } ?>
        
        <?php if(isset($_SESSION['progress_update'])) { ?><div class="success_r" style="width:250px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['progress_update'];?></p><?php unset($_SESSION['progress_update']); } ?>
       
       <?php if(isset($_SESSION['progress_task_del'])) { ?><div class="success_r" style="width:250px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['progress_task_del'];?></p><?php unset($_SESSION['progress_task_del']); } ?></div> 
       
        
       <a href="?sect=add_progress_task"><div class="add_new" style="margin-right:45px;margin-top:-35px;"></div></a>
		<div class="demo_jui" style="width:680px; float:left;" >
			<table width="680" cellpadding="0" cellspacing="0" border="0" class="display" id="example">
				<thead>
					<tr>
						<th nowrap="nowrap">Task</th>
						
                        <th>Location</th>
                        <th>Sub Location</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                       <th>Edit</th>
                            <th>Delete</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$q = "SELECT * FROM progress_monitoring pr LEFT JOIN project_locations l ON l.location_id= pr.location_id 
						  WHERE pr.project_id ='".$_SESSION['idp']."' and pr.is_deleted=0 order by progress_id DESC";
						$r=mysql_query($q);

						while($f=mysql_fetch_assoc($r))
						{
							// change date format
							$sdate=explode('-',$f['start_date']);
							$sdate=$sdate[2].'-'.$sdate[1].'-'.$sdate[0];
							$edate=explode('-',$f['end_date']);
							$edate=$edate[2].'-'.$edate[1].'-'.$edate[0];
							$sql="select location_title from project_locations where location_id=".$f['sub_location_id']."";
							$rows=mysql_query($sql);
							$rowData=mysql_fetch_array($rows);
							?>
							<tr class='gradeA'>
								<td><?php echo $f['task']; ?></td>
								<td><?php echo  $f['location_title']; ?></td>
								<td><?php echo $rowData['location_title']; ?></td>
								<td><?php echo $sdate; ?></td>
								<td><?php echo $edate; ?></td>
								<td align='center'>
											<a  title='Click to edit' href='?sect=edit_progress_task&id=<?php echo base64_encode($f['progress_id']); ?>'><img src='images/edit.png' border='none' /></a>
								</td>
								<td align='center'>
											<a  title='Click to delete' href='#' onclick="return deletechecked('Are you sure you want to delete this progress task?','?sect=progress_monitoring&id=<?php echo base64_encode($f['progress_id']);?>');"><img src='images/remove.png' border='none' /></a>
								</td>		
								</tr>
						<?php
                       
						}
					?>
					
					
					
				</tbody>
			</table>
		</div>
		<div class="spacer"></div>
	</div>
</div>
</body>
</html>
