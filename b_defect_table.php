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
$q= "SELECT d.df_id,d.area_room,d.defect_type_id,d.defect_desc,d.priority,d.status,d.create_date,
			p.project_name,
			b.user_fullname,
			sb.sb_id,sb.fk_b_id,
			r.resp_full_name,
			dl.dl_title 
	FROM ".DEFECTS." d 
	LEFT JOIN ".OWNERS." o ON o.id = d.owner_id 
	LEFT JOIN ".PROJECTS." p ON p.project_id = d.project_id 
	LEFT JOIN ".BUILDERS." b ON p.user_id=b.user_id 
	LEFT JOIN ".SUBBUILDERS." sb ON p.project_id=sb.fk_p_id 
	LEFT JOIN ".RESPONSIBLES." r ON r.resp_id = d.resp_id 	   
	LEFT JOIN ".DEFECTSLIST." dl ON dl.dl_id = d.defect_type_id 
	WHERE (sb.fk_b_id='$builder_id' OR sb.sb_id='$builder_id' OR p.user_id='$builder_id') 
	GROUP BY d.df_id ";
	
$r=mysql_query($q);
//echo $q; die;
while($f=mysql_fetch_assoc($r)){
	// change date format
	$create_date = $f['create_date'];
	$created_on = date("d/m/Y", strtotime($create_date));
	
	if($f['sb_id']==$builder_id)$head_by=$f['user_fullname'];else $head_by='Own';
	
	if($f['fk_b_id']!=NULL){$hb=$f['fk_b_id'];}else{$hb=$builder_id;}
	
	$tr.="<tr class='gradeA'>
		<td>".$obj->truncate_text(stripslashes($f['project_name']), 10)."</td>
		<td>".$head_by."</td>
		<td>".$obj->truncate_text(stripslashes($f['area_room']), 10)."</td>
		<td>".$obj->truncate_text(stripslashes($f['defect_desc']), 10)."</td>
		<td>".$created_on."</td>
		<td>".$obj->truncate_text(stripslashes($f['resp_full_name']), 10)."</td>
		<td>".stripslashes($f['priority'])."</td>
		<td>".stripslashes($f['dl_title'])."</td>
		<td>".stripslashes($f['status'])."</td>
		<td align='center'>
			<a href='?sect=edit_defect&id=".base64_encode($f['df_id'])."&hb=".base64_encode($hb)."'><img src='images/edit.png' border='none' /></a>
		</td>
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
	
	<div class="search_multiple" style="border:1px solid; margin-bottom:70px;text-align:center;margin-left:10px;margin-right:10px;">
		<form action="ajax_reply.php" method="post" enctype="multipart/form-data" onsubmit="return startAjax();">
		<!--	<iframe id="create_target" name="create_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>-->
			<div id="create_process_pdf"><br />
				If it take more than 500 beats then <label style="cursor:pointer;" onClick="getPDF(<?=$_SESSION['ww_builder_id']?>)">click here</label>
				<br/>
				<img src="images/loader.gif" /><br/>
			</div>
			<div id="create_process_csv"><br />
				Please be careful it may take few minutes.
				<br/>
				<img src="images/loader.gif" /><br/>
			</div>
			<div id="create_response"></div>
			<table width="980" cellpadding="0" cellspacing="15" border="0">
				<tr>
					<td width="134" nowrap="nowrap" style="color:#FFFFFF;" valign="top">Project Name</td>
					<td width="312" colspan="2"><select class="select_box" id="pro_name" name="pro_name" onchange="getData()">
							<?=$pro_name?>
						</select>
					</td>
					<td width="134" nowrap="nowrap" style="color:#FFFFFF;" valign="top">Status</td>
					<td width="312" colspan="2">
						<select class="select_box" id="status" name="status">
							<option value="">Select</option>
							<option>Open</option>
							<option>Pending</option>
							<option>In Progress</option>
							<option>Closed</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" style="color:#FFFFFF;" valign="top">Type
					<div id="load_defects_type" style="float:right; margin-top:-12px; display:none;"><img src="images/load.gif"></div>
					</td>
					<td width="312" colspan="2">
					<div id="defects_type_div" style="width:290px;">
						<input type="text" id="defect_type" name="defect_type" value="" class="input_small" readonly="readonly" />
					</div>
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" style="color:#FFFFFF;"  valign="top">Issued To Contact Name
					<div id="load_repairer_name" style="float:right; margin-top:-12px; display:none;"><img src="images/load.gif"></div>
					</td>
					<td width="312" colspan="2">
					<div id="repairer_name_div" style="width:290px;">
						<input type="text" id="repairer_name" name="repairer_name" value="" class="input_small" readonly="readonly" />
					</div>
					</td>
					<td width="134" nowrap="nowrap" style="color:#FFFFFF;" valign="top">Report Type</td>
					<td width="312" colspan="2"><select class="select_box" id="report_type" name="report_type">
							<option>PDF</option>
							<option>CSV</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
					<td>
					<div id="report_timer" style="color:#FFFFFF;"></div>
					</td>
					<td>
						<input type="hidden" value="create" name="sect" id="sect" />
						<input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/create_report.png); width:150px; height:30px;" />
					</td>
				</tr>
			</table>
		</form>
	</div>
	
	
	<div>
		<div class="content_hd1" style="background-image:url(images/defects_hd.png);"></div>
		<div class="demo_jui">
			<table width="980" cellpadding="0" cellspacing="0" border="0" class="display" id="example">
				<thead>
					<tr>
						<th nowrap="nowrap">Project Name</th>
						<th>Head by</th>
						<th nowrap="nowrap">Location</th>
						<th>Description</th>
						<th>Raised on</th>
						<th>Issued To Contact Name</th>
						<th>Priority</th>
						<th>Type</th>
						<th>Status</th>
						<th>Edit</th>
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
