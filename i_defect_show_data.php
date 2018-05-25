<?php 
error_reporting(1);
session_start();

if ($_SESSION["ww_is_builder"] == 1){
	$owner_id = $_SESSION['ww_builder_id'];
}else{
	$owner_id = $_SESSION['ww_owner_id'];
}
#$owner_id = $_SESSION['ww_owner_id'];
//include'data-table.php';

include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$myInspections ='';

$issued_to_add = '';
$where='';$or='';
if(isset($_POST['name'])){
$_SESSION['qc'] = $_POST;//Set Session for back implement
	if(!empty($_POST['projName'])){$where=" and I.project_id='".$_POST['projName']."'";}

	if(!empty($_POST['location']) && empty($_POST['subLocation'])){
		$where.=" and I.location_id in (".$object->subLocationsId($_POST['location'], ", ").")";
	}
	if(!empty($_REQUEST['subLocation']) && !empty($_REQUEST['subSubLocation'])){
		$postCount++;
		$where.=" and I.location_id in (".$object->subLocationsId($_REQUEST['subSubLocation'], ", ").")";
	}else{
		if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
			$postCount++;
			$where.=" and I.location_id in (".$object->subLocationsId($_REQUEST['subLocation'], ", ").")";
		}
	}
	if(!empty($_POST['status'])){$where.=" and F.inspection_status='".$_POST['status']."'";}
	if(!empty($_POST['inspectedBy'])){
		$where.=" and I.inspection_inspected_by='".$_POST['inspectedBy']."'";
	}
	if($_POST['issuedTo']!=""){
		$where.=" and F.issued_to_name='".$_POST['issuedTo']."' and F.inspection_id = I.inspection_id";
	}
	//if($_POST['priority']!=""){$where.=" and I.inspection_priority='".$_POST['priority']."'";}
	if($_POST['inspecrType']!=""){$where.=" and I.inspection_type='".$_POST['inspecrType']."'";}
	if(!empty($_POST['costAttribute'])){$where.=" and F.cost_attribute = '".$_POST['costAttribute']."'";}
	
	if(!empty($_SESSION['userRole'])){
		if($_SESSION['userRole'] != 'All Defect'){
			$where.=" and I.inspection_raised_by = '".$_SESSION['userRole']."'";
		}else{
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and I.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
		}
	}else{
		if($_REQUEST['raisedBy'] != 'All Defect'){
			if(!empty($_REQUEST['raisedBy'])){ $where.=" and I.inspection_raised_by = '".$_REQUEST['raisedBy']."'"; }
		}
	}


	if($_POST['DRF']!="" && $_POST['DRT']!=""){$or.=" I.inspection_date_raised between '".date('Y-m-d', strtotime($_POST['DRF']))."' and '".date('Y-m-d', strtotime($_POST['DRT']))."'";}
	
	if($_POST['DRF']!="" && $_POST['FBDF']!=""){$or.=" and";}
	
	if($_POST['FBDF']!="" && $_POST['FBDT']!=""){$or.=" F.inspection_fixed_by_date between '".date('Y-m-d', strtotime($_POST['FBDF']))."' and '".date('Y-m-d', strtotime($_POST['FBDT']))."'";}

	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
/*$qi="SELECT
	P.project_name as Project,
	I.location_id as Location,
	I.inspection_date_raised as DateRaised,
	I.inspection_inspected_by as InspectedBy,
	I.inspection_type as InspectonType,
	F.inspection_status as Status,
	F.issued_to_name as IssueToName,
	F.cost_attribute as CostAttribute,
	F.inspection_fixed_by_date as FixedByDate,
	I.inspection_description as Description,
	I.inspection_notes as Note,
	I.inspection_id as InspectionId,
	F.inspection_id as InspectionId_FOR
FROM
	user_projects as P, issued_to_for_inspections as F,
	project_inspections as I
WHERE
	I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.is_deleted = '0' $where group by I.inspection_id";*/
	
#echo '<pre>';
$qi="SELECT
	P.project_name as Project,
	L.location_id as Location,
	I.inspection_date_raised as DateRaised,
	I.inspection_inspected_by as InspectedBy,
	I.inspection_type as InspectonType,
	F.inspection_status as Status,
	F.issued_to_name as IssueToName,
	F.cost_attribute as CostAttribute,
	F.inspection_fixed_by_date as FixedByDate,
	I.inspection_description as Description,
	I.inspection_notes as Note,
	I.inspection_id as InspectionId,
	F.inspection_id as InspectionId_FOR,
	I.inspection_raised_by as RaisedBy
FROM
	user_projects as P, issued_to_for_inspections as F,
	project_inspections as I, project_locations as L
WHERE
	I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.location_id = L.location_id and L.is_deleted = '0' and I.is_deleted = '0' $where group by I.inspection_id";
#echo '</pre>';
#echo $qi; die;
	$ri=mysql_query($qi);?>
<table width="970" cellpadding="0" cellspacing="0" border="0" class="display" id="example">
    <thead>
        <th>Location</th>
        <th>Fix by Date</th>
        <th>Issue To</th>
        <th>Description</th>
        <th>Raised By</th>
        <th>View</th>
    </thead>
    <tbody>
<?php
	while($fi=mysql_fetch_assoc($ri)){

		$where = "";
		if(!empty($_REQUEST['costAttribute'])){
			$where .= " and cost_attribute = '".$_REQUEST['costAttribute']."'";
		}
		if(!empty($_REQUEST['issuedTo'])){
			$where .= " and issued_to_name = '".$_REQUEST['issuedTo']."'";
		}
		if(!empty($_POST['status'])){
			$where .= " and inspection_status = '".$_REQUEST['status']."'";
		}
		if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
			$where.=" and inspection_fixed_by_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
		}

		$issueToData = $object->selQRYMultiple('issued_to_name, inspection_fixed_by_date, cost_attribute, inspection_status', 'issued_to_for_inspections', 'inspection_id = '.$fi['InspectionId'] . ' and is_deleted=0 ' . $where .' group by issued_to_name');

		$issued_to_name = "";
		$fix_by_date = "";
		$status = "";
			foreach($issueToData as $isData){
				if ($status == ""){
					$status = $isData['inspection_status'];
				}else{
					$i_status = $isData['inspection_status'];//Pending
					if ($i_status == "Open"){
						$status = "Open";
					}elseif($i_status == "Pending" && ($status == "Fixed" || $status=="Closed") && $status!="Open" && $status!="Draft"){
						$status = 'Pending';
					}elseif ($i_status == "Fixed" && ($status == "Closed") && ($status!="Open" and $status!="Pending" and $status!="Draft")){
						$status = "Fixed";
					}elseif ($status!="Fixed" && ($status!="Open" and $status!="Pending" and $status!="Draft")){
						$status = "Closed";
					}
				}
				
				if($isData['inspection_fixed_by_date']!='0000-00-00'){
					$fixed_on = date("d-m-Y", strtotime($isData['inspection_fixed_by_date']));
				}else{
					$fixed_on = '';
				}
				
				if ($issued_to_name==""){
					$issued_to_name = $isData['issued_to_name'];
					$fix_by_date = $fixed_on;
				}else{
					$issued_to_name .= "<br/>" . $isData['issued_to_name'];
					$fix_by_date .= "<br/>" .  $fixed_on;
				}		
			}
        if($status == 'Open'){
			$status_color = 'style="background:#FF1717;"';
		}elseif($status == 'Draft'){
			$status_color =  'style="background:#A9A9A9;"';
		}elseif($status == 'Closed'){
			$status_color =  'style="background:#06C;"';
		}elseif($status == 'Pending'){
			$status_color =  'style="background:#FF0;"';
		}elseif($status == 'Fixed'){
			$status_color = 'style="background:#090;"';
		} 
		?>
		        <tr  class="gradeA" style="border:1px solid#999; padding: 0.4em;;">
                    <td <?php echo $status_color?>>
                        <?php $locations = $object->subLocations($fi["Location"], ' > ');
                        echo stripslashes(wordwrap($locations, 25, '<br />')); 	?>
                    </td>
                    <td <?php echo $status_color; ?> valign="top" >
                        <?php echo  $fix_by_date;?>
                    </td>
                    <td <?php echo $status_color;?>  valign="top">
                        <?php echo $issued_to_name;?>
                    </td>
                    <td <?php echo $status_color;?>  valign="top">
                        <?php echo $obj->truncate_text(stripslashes($fi['Description']), 50);?>
                    </td>
                     <td <?php echo $status_color;?>  valign="top">
                        <?php echo stripslashes($fi['RaisedBy']);?>
                    </td><td align='center' <?php echo $status_color;  ?> valign="top">
                        <a href='pms.php?sect=show_defect_photo&id=<?php echo base64_encode($fi['InspectionId']);?>'>
                            <img src='images/d_photo.png' border='none' />
                        </a>
                    </td>
                </tr>
<?	}
}else{
	echo 'Invalid Url';	
}?>
            </tbody>
        </table>
