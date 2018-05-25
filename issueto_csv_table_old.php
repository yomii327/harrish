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

$q = "SELECT * FROM inspection_issue_to WHERE project_id = '".$_SESSION['idp']."' and is_deleted=0";
$r=mysql_query($q);


// get all projects
$q ="SELECT p.project_id,p.project_name 
	 FROM ".PROJECTS." p LEFT JOIN ".SUBBUILDERS." sb ON p.project_id=sb.fk_p_id 
	 WHERE (sb.fk_b_id='$builder_id' OR sb.sb_id='$builder_id' OR p.user_id='$builder_id')";
//echo $q; die;	  
$r=mysql_query($q);
$pro_name="<option>Select</option>";
while($f=mysql_fetch_assoc($r)){
	$pro_name.="<option value=".$f['project_id'].">".stripslashes($f['project_name'])."</option>";
}
?>
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
		<?php if(isset($_SESSION['issue_edit'])) { ?><div class="success_r" style="width:250px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['issue_edit'];?></p><?php unset($_SESSION['issue_edit']); } ?>
		
		<?php if(isset($_SESSION['issue_add'])) { ?><div class="success_r" style="width:250px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['issue_add'];?></p><?php unset($_SESSION['issue_add']); } ?>
      
        
        <?php if(isset($_SESSION['issue_to_del'])) { ?><div class="success_r" style="width:250px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['issue_to_del'];?></p><?php unset($_SESSION['issue_to_del']); } ?></div>
        
        
        <a href="?sect=add_issue_to"><div class="add_new" style="margin-right:45px;margin-top:-35px;float:right"></div></a>
		<div class="demo_jui" style="width:680px; float:left;" >
			<table width="680" cellpadding="0" cellspacing="0" border="0" class="display" id="example">
				<thead>
					<tr>
						<th nowrap="nowrap">Contact Name</th>
						<th>Comapny Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Edit</th>
                        <th>Delete</th>
                       
					</tr>
				</thead>
				<tbody>
			<?php 		$q_issue = "SELECT * FROM inspection_issue_to WHERE project_id = '".$_SESSION['idp']."' and is_deleted=0";
$r=mysql_query($q_issue);
//echo $q; die;
while($f=mysql_fetch_assoc($r)){
	// change date format
	
	?>
	
	
	<tr class='gradeA'>
		
		<td><?php echo $f['issue_to_name'];?></td>
        <td><?php echo $f['company_name'];?></td>
		<td><?php echo $f['issue_to_phone'];?></td>
		<td><?php echo $f['issue_to_email'];?></td>
		<td align='center'>
					<a  title='Click to edit' href='?sect=edit_issue_to&id=<?php echo base64_encode($f['issue_to_id']);?>'><img src='images/edit.png' border='none' /></a>
		</td>
		<td align='center'>
					<a  title='Click to delete' href='#'  onclick="return deletechecked('Are you sure you want to delete this issued to?','?sect=issue_to&id=<?php echo base64_encode($f['issue_to_id']);?>');"><img src='images/remove.png' border='none' /></a>
		</td>	
		
	</tr>
	<?php
}
	?>			</tbody>
			</table>
		</div>
		<div class="spacer"></div>
	</div>
</div>
</body>
</html>
