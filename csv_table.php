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
$q= "SELECT * FROM project_locations where project_id =	".$builder_id."";
//echo $q; die;	
$r=mysql_query($q);
//echo $q; die;
while($f=mysql_fetch_assoc($r)){
	// change date format
	
	
	
	
	
	$tr.="<tr class='gradeA'>
		<td>".$f['location_parent_id']."</td>
		
		<td>".$f['location_title']."</td>
		
		</tr>";
}

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
<script language="javascript" type="text/javascript">
function startAjax(){
	var pro_id = document.getElementById('pro_name').value;
	var repairer_name = document.getElementById('repairer_name').value;
	var defect_type = document.getElementById('defect_type').value;
	var status = document.getElementById('status').value;
	
	var report_type = document.getElementById('report_type').value;
	
	if(pro_id!='Select'){
		if(report_type=='PDF'){
			document.getElementById('create_process_pdf').style.visibility = 'visible';
			document.getElementById('create_process_csv').style.visibility = 'hidden';
		}else{
			document.getElementById('create_process_pdf').style.visibility = 'hidden';
			document.getElementById('create_process_csv').style.visibility = 'visible';
		}
			
		document.getElementById('create_response').style.visibility = 'hidden';
		
		document.getElementById('report_timer').innerHTML = '';
		stopCount1();
		timedCount('start');
		
		return true;
	}else{
		var err = '<span class="create_emsg">Please select Project Name!<\/span><br/><br/>';
		document.getElementById('create_response').innerHTML = err;
		return false;
	}
}

function stopAjax(success){
	var report_type = document.getElementById('report_type').value;
	var result = '';
	if(success == 0){
		result = '<span class="create_emsg">No matching record is found!<\/span><br/><br/>';
	}else if(report_type == 'CSV'){
		result = '<span class="create_msg">Click <a href="report_csv/<?=$_SESSION['ww_builder_id']?>/'+success+'.php" target="_blank">here<\/a> to download '+report_type+'<\/span><br/><br/>';
	}else{
		result = '<span class="create_msg">Click <a href="report_pdf/<?=$_SESSION['ww_builder_id']?>/'+success+'.pdf" target="_blank">here<\/a> to view '+report_type+'<\/span><br/><br/>';
	}
	
	document.getElementById('create_process_pdf').style.visibility = 'hidden';
	document.getElementById('create_process_csv').style.visibility = 'hidden';
		
	document.getElementById('create_response').innerHTML = result;
	document.getElementById('create_response').style.visibility = 'visible';
	
	clearTimeout(t);
	
	return true;
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

function hideImg(){
	document.getElementById('load_defects_type').style.display = 'none';
	document.getElementById('load_repairer_name').style.display = 'none';
}

function getPDF(myId){
	window.location.href="<?=HOME_SCREEN.'pms.php?sect=load_report'?>";	
}
</script>
<!-- Ajax Post -->
</head>
<body id="dt_example">
<div id="container">
	<!--<div class="content_hd1" style="background-image:url(images/create_reports.png);"></div>-->
	
	
	
	
	<div style="margin-top:50px;">
		<div class="content_hd1" style="background-image:url(images/location_header.png);"></div>
		<div class="demo_jui" style="width:680px; float:left;" >
			<table width="680" cellpadding="0" cellspacing="0" border="0" class="display" id="example">
				<thead>
					<tr>
						<th nowrap="nowrap">Location</th>
						<th>Sub Location</th>
					</tr>
				</thead>
				<tbody>
					<?=$tr?>
				</tbody>
			</table>
		</div>
		<div class="spacer"></div>
	</div>
</div>
</body>
</html>
