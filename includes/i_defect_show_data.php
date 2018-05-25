<?php session_start();?>
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
<?php 
if ($_SESSION["ww_is_builder"] == 1)
{
	$owner_id = $_SESSION['ww_builder_id'];
}else{
	$owner_id = $_SESSION['ww_owner_id'];
}
$myInspections='';#$owner_id = $_SESSION['ww_owner_id'];
//include'data-table.php';
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
#$qi="SELECT *,r.resp_full_name FROM ".DEFECTS." d LEFT JOIN ".PROJECTS." p ON p.id = d.project_id  LEFT JOIN ".RESPONSIBLES." r ON r.project_id = d.project_id  WHERE d.owner_id = '$owner_id'";//Old Query

$where='';$or='';
if(isset($_POST['name'])){
	if(!empty($_POST['projName'])){$where=" and I.project_id='".$_POST['projName']."'";}

	if(!empty($_POST['location']) && empty($_POST['subLocation'])){
		$where.=" and I.location_id in (".$object->subLocationsId($_POST['location'], ", ").")";
	}
	if(!empty($_POST['location']) && !empty($_POST['subLocation'])){
		$where.=" and I.location_id in (".$object->subLocationsId($_POST['subLocation'], ", ").")";
	}

	if(!empty($_POST['status'])){$where.=" and I.inspection_status='".$_POST['status']."'";}
	if(!empty($_POST['inspectedBy'])){$where.=" and I.inspection_inspected_by='".$_POST['inspectedBy']."'";}
	if($_POST['issuedTo']!=""){$where.=" and I.inspection_issued_to='".$_POST['issuedTo']."'";}
	if($_POST['priority']!=""){$where.=" and I.inspection_priority='".$_POST['priority']."'";}
	if($_POST['inspecrType']!=""){$where.=" and I.inspection_type='".$_POST['inspecrType']."'";}
	if(!empty($_POST['costAttribute'])){$where.=" and I.cost_attribute = '".$_POST['costAttribute']."'";}
	if($_POST['DRF']!=""){$or.=" I.created_date>='".$_POST['DRF']."'";}
	if($_POST['DRT']!=""){$or.=" and I.created_date<='".$_POST['DRT']."'";}
	
	if($_POST['DRF']!=""){$or.=" or";}
	
	if($_POST['FBDF']!=""){$or.=" I.inspection_fixed_by_date>='".$_POST['FBDF']."'";}
	if($_POST['FBDT']!=""){$or.=" and I.inspection_fixed_by_date<='".$_POST['FBDT']."'";}	

	if(!empty($or)&& !empty($where)){$where=$where." or (".$or.")";}

echo $qi="SELECT
	P.project_name as Project,
	I.location_id as Location,
	I.created_date as DateRaised,
	I.inspection_inspected_by as InspectedBy,
	I.inspection_type as InspectonType,
	I.inspection_status as Status,
	I.inspection_priority as Priority,
	I.inspection_fixed_by_date as FixedByDate,
	I.inspection_description as Description,
	I.inspection_notes as Note,
	I.inspection_id as InspectionId
FROM
	user_projects as P,
	project_inspections as I
WHERE
	I.project_id = P.project_id and I.is_deleted = '0' $where";

	$ihd="<th>Location</th><th>Description</th><th>Issue To</th><th>Status</th><th>View</th>";
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
		$myInspections .= "<tr class='gradeA'>
				<td>";
				$locations = $object->subLocations($fi["Location"], ' > ');
				$myInspections .= stripslashes(wordwrap($locations, 25, '<br />'))."</td>
				<td>".$obj->truncate_text(stripslashes($fi['Description']), 50)."</td>
				<td>";
				$issue = $object->getRecordsSp('issued_to_for_inspections', 'inspection_id', $fi['InspectionId'], 'issued_to_name');
				if(!empty($issue)){
					$myInspections .= '<ul style="list-style:none;margin-left:-37px;">';
						foreach($issue as $issueTo){
							$myInspections .= '<li>'.stripslashes(wordwrap($issueTo['issued_to_name'], 15, '<br />', true)).'</li>';
					}
					$myInspections .= '</ul>';
				}
				$myInspections .= "</td>
				<td>".stripslashes($fi['Status'])."</td>
				<td align='center'><a href='pms.php?sect=show_defect_photo&id=".base64_encode($fi['InspectionId'])."'><img src='images/d_photo.png' border='none' /></a></td>
			</tr>";
	}
}
$response = '<table width="970" cellpadding="0" cellspacing="0" border="0" class="display" id="example"><thead>'.$ihd.'</thead><tbody>'.$myInspections.'</tbody></table></div>';
echo  $qi;
?>