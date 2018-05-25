<?php include'data-table.php';

function FillSelectBox($field,$table,$where){
	$q="select $field from $table where $where";
	//echo '<option value="$q">'.$q.'</option>';
	$q=mysql_query($q);
	while($q1=mysql_fetch_array($q)){
	echo '<option value="'.$q1[0].'">'.$q1[1].'</option>';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" type="text/css"/>
<title>Report</title>
<style type="text/css">
@import "css/jquery.datepick.css";
table td{padding:5px;}
table{ margin-left:10px;}
#DRF, #DRT, #FBDF, #FBDT{
	background:#FFF;
	cursor:default;
	height:20px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	border-radius: 6px;
}

</style>
<!-- Date Picker files start here -->
	<link type="text/css" rel="stylesheet" href="css/jscal2.css" />
	<link type="text/css" rel="stylesheet" href="css/border-radius.css" />
	<script src="js/jscal2.js"></script>
	<script src="js/en.js"></script>
<!-- Date Picker files -->
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>


</head>
<body id="dt_example">
<script language="javascript" type="text/javascript">
function startAjax(val){
	AjaxShow("POST","ajaxFunctions.php?type=location && proID="+val,"ShowLocation");
	AjaxShow("POST","ajaxFunctions.php?type=inspecrBy && proID="+val,"ShowInspecrBy");
	AjaxShow("POST","ajaxFunctions.php?type=issuedTo && proID="+val,"ShowIssuedTo");
	AjaxShow("POST","ajaxFunctions.php?type=priority && proID="+val,"ShowPriority");
} 
</script>
<div id="container">
<?php
$owner_id = $_SESSION['ww_owner_id'];
$phd='';
$myProjects='';
$ihd='';
$myInspections='';
/*
// get all projects related to this inspector
	$qp="SELECT *, p.id as project_id,r.resp_full_name FROM ".OWNERS." o 
	LEFT JOIN ".PROJECTS." p ON p.id = o.ow_project_id 
	LEFT JOIN ".RESPONSIBLES." r ON r.project_id = o.ow_project_id 
	LEFT JOIN ".BUILDERS." b ON p.user_id = b.m_id 
	WHERE o.id = '$owner_id' ";
	  
$phd="<th>Project Name</th>
	<!--th>Add Inspection</th-->";

$rp=mysql_query($qp);

while($fp=mysql_fetch_assoc($rp)){
	$myProjects.="<tr class='gradeA'>
	<td>".stripslashes($fp['pro_name'])."</td>
	<!--td align='center'>
		<a href='?sect=add_defect&id=".base64_encode($fp['project_id'])."'><img src='images/edit.png' border='none' /></a>
	</td-->
	</tr>";
}*/


// get all inspections logged by this inspector
$qi="SELECT *,r.resp_full_name FROM ".DEFECTS." d 
	LEFT JOIN ".PROJECTS." p ON p.id = d.project_id 
	LEFT JOIN ".RESPONSIBLES." r ON r.project_id = d.project_id 
	WHERE d.owner_id = '$owner_id'";
	$where='';$or='';
if(isset($_REQUEST['SearchInsp'])){
#	print_r($_POST);die();
	if(!empty($_POST['projName'])){$where=" and d.project_id='".$_POST['projName']."'";}
	if(!empty($_POST['location'])){$where.=" and d.area_room='".$_POST['location']."'";}
	if(!empty($_POST['status'])){$where.=" and d.status='".$_POST['status']."'";}
	if(!empty($_POST['inspectedBy'])){$where.=" and d.inspected_by='".$_POST['inspectedBy']."'";}
	if($_POST['issuedTo']!=""){$where.=" and r.resp_id='".$_POST['issuedTo']."'";}
	if($_POST['priority']!=""){$where.=" and d.priority='".$_POST['priority']."'";}
	if($_POST['inspecrType']!=""){$where.=" and d.defect_type_id='".$_POST['inspecrType']."'";}
	if($_POST['DRF']!=""){$or.=" d.create_date>='".$_POST['DRF']."'";}
	if($_POST['DRT']!=""){$or.=" and d.create_date<='".$_POST['DRT']."'";}
	if($_POST['FBDF']!=""){$or.=" or d.fixed_by_date>='".$_POST['FBDF']."'";}
	if($_POST['FBDT']!=""){$or.=" and d.fixed_by_date<='".$_POST['FBDT']."'";}	
	
if(!empty($or)&& !empty($where)){$where=$where." or (".$or.")";}

echo $qi="SELECT *,r.resp_full_name FROM ".DEFECTS." d 
	Left JOIN ".RESPONSIBLES." r ON r.resp_id = d.resp_id 
	WHERE d.owner_id = '$owner_id' $where ";	

$ihd="<th>Location</th>
	<th>Description</th>
	<th>Issue To y</th>
	<th>Status</th>
	<th>View</th>";
$ri=mysql_query($qi);
while($fi=mysql_fetch_assoc($ri)){
	// change date format
	$create_date = $fi['create_date'];
	$created_on = date("d/m/Y", strtotime($create_date));
	
	// change date format
	if($f['fixed_date']!='0000-00-00'){
		$fixed_on = $fi['fixed_date'];
	}else{
		$fixed_on = '';
	}
	
	$myInspections.="<tr class='gradeA'>
					<td>".stripslashes($fi['area_room'])."</td>
					<td>".$obj->truncate_text(stripslashes($fi['defect_desc']), 50)."</td>
					<td>".stripslashes($fi['resp_full_name'])."</td>
					<td>".stripslashes($fi['status'])."</td>
					<td align='center'>
						<a href='?sect=show_defect_photo&id=".base64_encode($fi['df_id'])."'><img src='images/d_photo.png' border='none' /></a>
					</td>
					</tr>";
}	

}	  
?>
	<div class="content_hd1" style="background-image:url(images/progress_monitoring_header.png);"></div>
<div class="search_multiple" style="border:1px solid; margin-bottom:70px;text-align:center;margin-left:10px;margin-right:10px;">
	<form action="pms.php?sect=i_defect" method="post">
			<table width="" cellpadding="0" cellspacing="5" border="0">
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Project Name <span class="reqire">*</span></td>
					<td colspan="2"><select name="projName"  class="select_box" onchange="startAjax(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
					  <option value="">Select</option>
					  <?php FillSelectBox("id, pro_name","pms_projects","id>='1'"); ?>
				    </select></td>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Location </td>
					<td colspan="2" id="ShowLocation"><select name="location"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					  <option value="">Select</option>
				    </select></td>
				</tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Status</td>
					<td colspan="2"><select name="status" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					  <option value="">Select</option>
					  <option value="Open">Open</option>
					  <option value="Pending">Pending</option>
					  <option value="In Progress">In Progress</option>
					  <option value="Closed">Closed</option>
				    </select></td>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Inspected By</td>
					<td colspan="2" id="ShowInspecrBy"><select name="inspectedBy" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					  <option value="">Select</option>
				    </select></td>
				</tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Issued To</td>
					<td colspan="2" id="ShowIssuedTo"><select name="issuedTo" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					  <option value="">Select</option>
				    </select></td>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Priority </td>
					<td colspan="2" id="ShowPriority"><select name="priority" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					  <option value="">Select</option>
				    </select></td>
				</tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Inspection Type</td>
					<td colspan="2">
                    <select name="inspecrType" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					  <option value="">Select</option>
					  <option value="0">Defect</option>
					  <option value="1">Damage</option>
					  <option value="2">Wear and Tear</option>
				    </select></td>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">&nbsp;</td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Date Raised</td>
					<td colspan="2" align="left" nowrap="nowrap" style="color:#FFFFFF;">From 
                     <input name="DRF" type="text" value="" size="7" id="DRF" readonly="readonly"/>
					 <script type="text/javascript">
					 	new Calendar({
							inputField: "DRF",
							dateFormat: "%d-%m-%Y",
							trigger: "DRF",
							onSelect: function() {
								var date = Calendar.intToDate(this.selection.get());
								this.hide();
							}
						});
					</script>
				    To 
				    <input name="DRT" type="text" id="DRT" size="7" />
					<script type="text/javascript">
						new Calendar({
							inputField: "DRT",
							dateFormat: "%d-%m-%Y",
							trigger: "DRT",
							onSelect: function() {
								var date = Calendar.intToDate(this.selection.get());
								this.hide();
							}
						});
					</script></td>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Fix By Dat</td>
					<td colspan="2" align="left" nowrap="nowrap" style="color:#FFFFFF;">From 
					<input name="FBDF" type="text" id="FBDF" size="7" />
					<script type="text/javascript">
						new Calendar({
							inputField: "FBDF",
							dateFormat: "%d-%m-%Y",
							trigger: "FBDF",
							onSelect: function() {
								var date = Calendar.intToDate(this.selection.get());
								this.hide();
							}
						});
					</script>
					To 
				    <input name="FBDT" type="text" id="FBDT" size="7" />
					<script type="text/javascript">
						new Calendar({
							inputField: "FBDT",
							dateFormat: "%d-%m-%Y",
							trigger: "FBDT",
							onSelect: function() {
								var date = Calendar.intToDate(this.selection.get());
								this.hide();
							}
						});
					</script></td>
				</tr>
				<tr>
					<td colspan="3" align="left">&nbsp;</td>
					<td align="left">
					<div id="report_timer" style="color:#FFFFFF;"></div>
					</td>
					<td>
					  <!--input type="hidden" value="create" name="sect" id="sect" /-->
						<input name="SearchInsp" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/search_btn_web.png); width:113px; height:46px;"  />
					</td>
				</tr>
	  </table>
	</form>
  </div>
  
  <div>
			<div class="demo_jui" <?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){ echo 'style="padding-left:10px;"'; }?>>
			<table <?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){ echo 'width="742"'; }else{echo 'width="980"';}?> cellpadding="0" cellspacing="0" border="0" class="display" id="example">
				<thead><?=$ihd?></thead>
				<tbody><?=$myInspections?></tbody>
			</table>
		</div>
		<div class="spacer"></div>
  </div>
  
</div>
</body>
</html>
