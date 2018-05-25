<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php'; ?>
<?php
$tr='';
$pro_name='';

$resp_id = $_SESSION['ww_resp_id'];

$q="SELECT * FROM ".DEFECTS." d 
	  LEFT JOIN ".RESPONSIBLES." r ON d.resp_id = r.resp_id 
	  LEFT JOIN ".DEFECTSLIST." dl ON dl.dl_id = d.defect_type_id 
	  WHERE d.resp_id = '$resp_id'";
	  
$r=mysql_query($q);

while($f=mysql_fetch_assoc($r)){
	// change date format
	$create_date = $f['create_date'];
	$created_on = date("d/m/Y", strtotime($create_date));
	
	$tr.="<tr class='gradeA'>
		<td>".$obj->truncate_text(stripslashes($f['defect_desc']), 10)."</td>
		<td>".$created_on."</td>
		<td>".stripslashes($f['priority'])."</td>
		<td>".stripslashes($f['dl_title'])."</td>
		<td>".stripslashes($f['status'])."</td>
		<td align='center'>
			<a href='?sect=r_edit_defect&id=".base64_encode($f['df_id'])."'><img src='images/edit.png' border='none' /></a>
		</td>
		</tr>";
	$fk_b_id=$f['builder_id'];
}

// get all projects
$q = "SELECT project_id, project_name FROM ".PROJECTS." p
	  JOIN ".DEFECTS." d 
	  ON p.project_id = d.project_id 
	  WHERE d.resp_id = '$resp_id' 
	  GROUP BY p.project_name ";
	  
$r=mysql_query($q);
$pro_name="<option>Select</option>";
while($f=mysql_fetch_assoc($r)){
	$pro_name.="<option value=".$f['project_id'].">".stripslashes($f['project_name'])."</option>";
}
?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript">
function startAjax(){
	var pro_id = document.getElementById('pro_name').value;
	var assign_to_name = document.getElementById('assign_to_name').value;
	var defect_type = document.getElementById('defect_type').value;
	var status = document.getElementById('status').value;	
	
	if(pro_id!='Select'){
		document.getElementById('create_process').style.visibility = 'visible';
		document.getElementById('create_response').style.visibility = 'hidden';
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
		result = '<span class="create_emsg">No matching record found!<\/span><br/><br/>';
	}else if(report_type == 'CSV'){
		result = '<span class="create_msg">Click <a href="report_csv_responsible/report_'+success+'.php" target="_blank">here<\/a> to download '+report_type+'<\/span><br/><br/>';
	}else{
		result = '<span class="create_msg">Click <a href="report_pdf/report_'+success+'.pdf" target="_blank">here<\/a> to view '+report_type+'<\/span><br/><br/>';
	}
	document.getElementById('create_process').style.visibility = 'hidden';
	document.getElementById('create_response').innerHTML = result;
	document.getElementById('create_response').style.visibility = 'visible';
	
	return true;
}

function getData(){
	var pro_id = document.getElementById('pro_name').value;
	document.getElementById('create_response').innerHTML='';

	// display loading image.
	document.getElementById('load_defects_type').style.display = 'block';
	document.getElementById('load_assign_to_name').style.display = 'block';
	
	// get defects type for this project
	$("#defects_type_div").load('defects_type_response.php?pro_id='+pro_id);
	
	// get assign to for this project
	$("#assign_to_name_div").load('repairer_name_response.php?pro_id='+pro_id);
	setTimeout("hideImg()",1000);
}

function hideImg(){
	document.getElementById('load_defects_type').style.display = 'none';
	document.getElementById('load_assign_to_name').style.display = 'none';
}

</script>
<!-- Ajax Post -->
</head>
<body id="dt_example">
<div id="container">
	<div class="content_hd1" style="background-image:url(images/create_reports.png);"></div>
	
	<div class="search_multiple" style="border:1px solid; margin-bottom:70px;">
		<form action="wwConroller.php" method="post" target="create_target" enctype="multipart/form-data" onsubmit="return startAjax();">
			<iframe id="create_target" name="create_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>
			<div id="create_process"><br />
				Sending request...<br/>
				<img src="images/loader.gif" /><br/>
			</div>
			<div id="create_response"></div>
			<table width="980" cellpadding="0" cellspacing="15" border="0">
				<tr>
					<td width="134" nowrap="nowrap" style="color:#FFFFFF;">Project Name</td>
					<td width="312" colspan="2">
						<select class="select_box" id="pro_name" name="pro_name" onchange="getData()">
							<?=$pro_name?>
						</select>
					</td>
					<td width="134" nowrap="nowrap" style="color:#FFFFFF;">Status</td>
					<td width="312" colspan="2"><select class="select_box" id="status" name="status">
							<option value="">Select</option>
							<option>Open</option>
							<option>Pending</option>
							<option>In Progress</option>
							<option>Closed</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" style="color:#FFFFFF;">Type
					<div id="load_defects_type" style="float:right; margin-top:-12px; display:none;"><img src="images/load.gif"></div>
					</td>
					<td width="312" colspan="2">
					<div id="defects_type_div1" style="width:290px;">
						<input type="text" id="defect_type" name="defect_type" value="" class="input_small" readonly="readonly" />
					</div>
					</td>
					<!--<td width="134" nowrap="nowrap" style="color:#FFFFFF;">Type</td>
					<td width="312" colspan="2">
						<select class="select_box" id="defect_type" name="defect_type">
							<option value="">Select</option>
							<?php
							/*$qd=$obj->db_query("SELECT * FROM ".DEFECTSLIST." WHERE fk_b_id='$fk_b_id'");
							if($obj->db_num_rows($qd)>0){					
								while($fd=$obj->db_fetch_assoc($qd)){
								?>
									<option value="<?=$fd['dl_id']?>"><?=stripslashes($fd['dl_title'])?></option>
								<?php	
								}
							}*/
							?>
						</select>
					</td>-->
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" style="color:#FFFFFF;">Assign To Contact Name
					<div id="load_assign_to_name" style="float:right; margin-top:-12px; display:none;"><img src="images/load.gif"></div>
					</td>
					<td width="312" colspan="2">
					<div id="assign_to_name_div" style="width:290px;">
						<input type="text" id="assign_to_name" name="assign_to_name" value="" class="input_small" readonly="readonly" />
					</div>
					</td>
					<td width="134" nowrap="nowrap" style="color:#FFFFFF;">Report Type</td>
					<td width="312" colspan="2"><select class="select_box" id="report_type" name="report_type">
							<option>CSV</option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="4"></td>
					<td>
						<input type="hidden" value="create_csv_responsible" name="sect" id="sect" />
						<input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/create.png); width:75px; height:30px;" />
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
						<th>Description</th>
						<th>Raised on</th>
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