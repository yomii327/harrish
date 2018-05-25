<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php'; include'includes/commanfunction.php'; 
$object = new COMMAN_Class();
?>
</head>
<body id="dt_example">

<div id="container">
<?php
$owner_id = $_SESSION['ww_owner_id'];

$phd='';
$myProjects='';
$ihd='';
$myInspections='';

// get all projects related to this inspector
#$qp="SELECT *, p.project_id as project_id FROM ".OWNERS." o LEFT JOIN ".PROJECTS." p ON p.project_id = o.ow_project_id LEFT JOIN ".BUILDERS." b ON p.user_id = b.user_id WHERE o.id = '$owner_id' ";
$qp="SELECT *, p.project_id as project_id FROM ".OWNERS." o LEFT JOIN ".PROJECTS." p ON p.project_id = o.ow_project_id LEFT JOIN ".BUILDERS." b ON p.user_id = b.user_id WHERE o.id = '$owner_id' ";
die;
$phd="<th>Project Name</th>
	<!--th>Add Inspection</th-->";

$rp=mysql_query($qp);

while($fp=mysql_fetch_assoc($rp)){
	$myProjects.="<tr class='gradeA'>
	<td>".stripslashes($fp['project_name'])."</td>
	<!--td align='center'>
		<a href='?sect=add_defect&id=".base64_encode($fp['project_id'])."'><img src='images/edit.png' border='none' /></a>
	</td-->
	</tr>";
}


// get all inspections logged by this inspector
#$qi="SELECT * FROM ".DEFECTS." d LEFT JOIN ".PROJECTS." p ON p.project_id = d.project_id WHERE d.owner_id = '$owner_id'";
$qi="SELECT * FROM ".DEFECTS." d LEFT JOIN ".PROJECTS." p ON p.project_id = d.project_id WHERE d.owner_id = '$owner_id'";
$ihd="<th>Project Name</th>
	<th>Location</th>
	<th>Description</th>
	<th>Date Raised</th>
	<th>Priority</th>
	<th>Status</th>
	<th>Date Closed</th>";

#$ihd="<th>Project Name</th><th>Location</th><th>Description</th><th>Date Raised</th><th>Priority</th><th>Status</th><th>Date Closed</th><th>Edit Inspection</th><th>View</th>";
	
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
					<td>".stripslashes($fi['project_name'])."</td>
					<td>".stripslashes($object->getDataByKey('project_locations', 'location_id', $fi['location_id'], 'location_title'))."</td>
					<td>".$obj->truncate_text(stripslashes($fi['defect_desc']), 10)."</td>
					<td>".$created_on."</td>
					<td>".stripslashes($fi['priority'])."</td>
					<td>".stripslashes($fi['status'])."</td>
					<td>".$fixed_on."</td>
					</tr>";

#$myInspections.="<tr class='gradeA'><td>".stripslashes($fi['pro_name'])."</td><td>".stripslashes($fi['area_room'])."</td><td>".$obj->truncate_text(stripslashes($fi['defect_desc']), 10)."</td><td>".$created_on."</td><td>".stripslashes($fi['priority'])."</td><td>".stripslashes($fi['status'])."</td><td>".$fixed_on."</td><td align='center'><a href='?sect=o_edit_defect&did=".base64_encode($fi['df_id'])."&pid=".base64_encode($fi['project_id'])."'><img src='images/edit.png' border='none' /></a></td><td align='center'><a href='?sect=show_defect_photo&id=".base64_encode($fi['df_id'])."'><img src='images/d_photo.png' border='none' /></a></td></tr>";

}		  
?>
	<div style="margin-bottom:100px;">
		<div class="content_hd1" style="background-image:url(images/pro_sub_loc.png);"></div>
			<div class="demo_jui" <?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){ echo 'style="padding-left:10px;"'; }?>>
			<table width="380px" cellpadding="0" cellspacing="0" border="0" class="display" id="example_1" style="margin-left:0px;" >
				<thead>
					<tr><?=$phd?></tr>
				</thead>
				<tbody><?=$myProjects?></tbody>
			</table>	
			</div>		
			<div class="spacer"></div>
	</div>
	<!------------------------------------------------------------------------------------------>
	<div>
		<div class="content_hd1" style="background-image:url(images/defects_hd.png);"></div>
		<div class="demo_jui" <?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){ echo 'style="padding-left:10px;"'; }?>>
			<table <?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){ echo 'width="742"'; }else{echo 'width="980"';}?> cellpadding="0" cellspacing="0" border="0" class="display" id="example_2">
				<thead>
					<tr><?=$ihd?></tr>
				</thead>
				<tbody><?=$myInspections?></tbody>
			</table>	
		</div>
		<div class="spacer"></div>
	</div>
</div>
</body>
</html>
