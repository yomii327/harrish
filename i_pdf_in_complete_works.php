<?php
ob_start();
session_start();
//Code for Calculate Execution Time
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$starttime = $mtime; 
//Code for Calculate Execution Time

include('includes/commanfunction.php');
$obj = new COMMAN_Class();

	$row = $obj->selQRY('pr_num_sublocations', 'user_projects','project_id = "'.$_REQUEST['projName'].'"');
	$pr_num_sublocations = $row["pr_num_sublocations"];


$issued_to_add='';
if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}

if(!isset($_REQUEST['startWith'])){$offset = 0;}else{$offset = $_REQUEST['startWith'];}
$limit = 100;

$report_type = $_REQUEST['report_type'];

$where = '';
if(empty($_REQUEST['location'])){
	$loc = $object->db_query("select distinct location_id from progress_monitoring where project_id = '".$_REQUEST['projName']."'");
	while($row = mysql_fetch_assoc($loc)){$locations[] = $row;}
	if(!empty($_REQUEST['projName'])){
		$where=" and PM.project_id='".$_REQUEST['projName']."'";
	}

	$locatioin_id = '';
	foreach($locations as $Location){
		if($locatioin_id == ''){
			$locatioin_id .= $Location['location_id'];
		}else{
			$locatioin_id .= ', '.$Location['location_id'];
		}
	}
	$where.=" and PM.location_id in (".$locatioin_id.")";
	if(!empty($_REQUEST['status'])){
		$postCount++;
		$where.=" and PM.status='".$_REQUEST['status']."'";
	}
	if($_REQUEST['DRF']!="" && $_REQUEST['DRT']!=""){
		$postCount++;
		$or.=" PM.start_date between '".date('Y-m-d', strtotime($_REQUEST['DRF']))."' and '".date('Y-m-d', strtotime($_REQUEST['DRT']))."'";
	}
	
	if($_REQUEST['DRF']!="" && $_REQUEST['FBDF']!=""){$or.=" and";}
	
	if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
		$postCount++;
		$or.=" PM.end_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
	}
	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}
	
}else{
	if(!empty($_REQUEST['projName'])){
		$where=" and PM.project_id='".$_REQUEST['projName']."'";
	}
	if(!empty($_REQUEST['location'])){
		$postCount++;
		$where.=" and PM.location_id= '".$_REQUEST['location']."'";
	}
	if(!empty($_REQUEST['subLocation']) && empty($_REQUEST['subLocation_sub'])){
		$postCount++;
		$sublocations = $obj->subLocationsIdProgressMonitoring ($_REQUEST['subLocation'], ",");
	    $where.=" and (PM.sub_location_id in (".$sublocations."))";
	}
	if (!empty ($_REQUEST['subLocation_sub'])){
		$postCount++;
		$where.=" and PM.sub_location_id= '".$_REQUEST['subLocation_sub']."'";
	}
	if(!empty($_REQUEST['status'])){
		$postCount++;
		$where.=" and PM.status='".$_REQUEST['status']."'";
	}
	if($_REQUEST['DRF']!="" && $_REQUEST['DRT']!=""){
		$postCount++;
		$or.=" PM.start_date between '".date('Y-m-d', strtotime($_REQUEST['DRF']))."' and '".date('Y-m-d', strtotime($_REQUEST['DRT']))."'";
	}
	
	if($_REQUEST['DRF']!="" && $_REQUEST['FBDF']!=""){$or.=" and";}
	
	if($_REQUEST['FBDF']!="" && $_REQUEST['FBDT']!=""){
		$postCount++;
		$or.=" PM.end_date between '".date('Y-m-d', strtotime($_REQUEST['FBDF']))."' and '".date('Y-m-d', strtotime($_REQUEST['FBDT']))."'";
	}
	if(!empty($or)&& !empty($where)){$where=$where." and (".$or.")";}

}
		  if(!empty($_REQUEST['issuedToPM']))
		  {
			$include_issue_to = ",issued_to_for_progress_monitoring ipm";
			$where.="  and ipm.issued_to_name='".$_REQUEST['issuedToPM']."' and PM.progress_id=ipm.progress_id and ipm.is_deleted=0";
		  }

	$qi="select
		PM.progress_id,
		PM.location_id,
		PM.sub_location_id,
		PM.task,
		PM.start_date,
		PM.end_date,
		PM.status,
		PM.percentage
	from
		progress_monitoring as PM $include_issue_to
	where
		PM.is_deleted = 0 $where group by PM.progress_id order by PM.progress_id LIMIT $offset , $limit";
	$ri = mysql_query($qi);


$queryCount = "SELECT COUNT(PM.progress_id) as totalCount FROM progress_monitoring as PM $include_issue_to WHERE PM.is_deleted = 0 $where";
$resCount=mysql_query($queryCount);
$countQuery = mysql_fetch_object($resCount);
if($countQuery->totalCount > 0){
	$totalCount = $countQuery->totalCount;
}
$noInspection = mysql_num_rows($ri);
$ajaxReplay = $noInspection.' Records';
$noPages = ceil(($noInspection-12)/14 +1);

if($noInspection > 0){

$html = '<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td width="40%"></td><td width="70%" align="right" style="padding-right:20px;">
					<img src="company_logo/logo.png" height="40"  />
				</td>
			</tr>';
if($offset == 0){
	$html .= '<tr>
				<td width="40%" style="font-size:14px;"><u><b>Onsite Status Report</b></u></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td  style="font-size:12px;"><strong>Project Name : </strong>'.$obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projName'], 'project_name').'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px;"><strong>Date : </strong>'.date('d / m / Y').'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px;"><strong>Page : </strong>1 of '.$noPages.'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px;"><strong>Report Filtered by : </strong></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="padding-left:30px;" colspan="2"><table width="400" border="0"><tr>';
				$jk=0;
	if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
		$html .= '<td width="140" style="font-size:11px;"><b>Location Name : </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['location'], 'location_title').'</td>';$jk++;
	}
	if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation']) && !empty($_REQUEST['subLocation_sub'])){
		$html .= '<td width="140" style="font-size:11px;"><b>Location Name : </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['location'], 'location_title').'</td>';$jk++;
		$html .= '<td width="140" style="font-size:11px;"><b>Sub Location Name : </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['subLocation'], 'location_title').'</td>';$jk++;
		$html .= '</tr><tr><td width="140" style="font-size:11px;"><b>Sub Location 1 : </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['subLocation_sub'], 'location_title').'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
	}else
	if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
		$html .= '<td width="140" style="font-size:11px;"><b>Location Name : </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['location'], 'location_title').'</td>';$jk++;
		$html .= '<td width="140" style="font-size:11px;"><b>Sub Location Name : </b></td>
			<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['subLocation'], 'location_title').'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
	}
	

		if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
			$html .= '<td width="140" style="font-size:11px;"><b>Status : </b></td>
			<td width="100" style="font-size:11px;">'.$_REQUEST['status'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_REQUEST['DRF']) && isset($_REQUEST['DRF']) || !empty($_REQUEST['DRT']) && isset($_REQUEST['DRT'])){
			$html .= '<td width="140" style="font-size:11px;"><b>Date Raised : </b></td>
			<td width="100" style="font-size:11px;">'.$_REQUEST['DRF'].' to '.$_REQUEST['DRT'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		if(!empty($_REQUEST['FBDF']) && isset($_REQUEST['FBDF']) || !empty($_REQUEST['FBDT']) && isset($_REQUEST['FBDT'])){
		   $html .= '<td width="140" style="font-size:11px;""><b>Fixed By Date : </b></td>
		   <td width="100" style="font-size:11px;"">'.$_REQUEST['FBDF'].' to '.$_REQUEST['FBDT'].'</td>';
			$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
		}
		
		$html .= '</tr></table></td></tr>';
}
	$html .= '</table><br />
		<table width="98%" cellpadding="0" cellspacing="0" align="center" class="collapse">
		<tr>
			<td style="width:100px;font-size:12px;" align="center"><strong>Location</strong></td>
			<td style="width:140px;font-size:12px;" align="center"><strong>Task</strong></td>
			<td style="width:50px;font-size:12px;" align="center"><strong>Issued To</strong></td>
			<td style="width:50px;font-size:12px;" align="center"><strong>Start Date</strong></td>
			<td style="width:50px;font-size:12px;" align="center"><strong>Finish Date</strong></td>
			<td style="width:50px;font-size:12px;" align="center"><strong>Status</strong></td>
			<td style="width:70px;font-size:12px;" align="center"><strong>% Complete</strong></td>
		</tr>';
		$i=1;$j=1;
		while($fi=mysql_fetch_assoc($ri)){$i++;
		$html .= '<tr>
			<td>';
				$html .='<ul style="list-style:none;margin-left:-37px;">';
					$loc = $obj->getDataByKey('project_monitoring_locations', 'location_id', $fi["location_id"], 'location_title');
					$html .='<li><font style="font-size:12px;padding-left:5px;">'.stripslashes(wordwrap($loc, 25, '<br />&nbsp;')).'</font></li>';
					if ($pr_num_sublocations == 2){
						$subLoc = $obj->selQRYMultiple('location_title, location_parent_id', 'project_monitoring_locations', 'location_id = '.$fi["sub_location_id"]);
						$subSubLoc = $obj->getDataByKey('project_monitoring_locations', 'location_id', $subLoc[0]['location_parent_id'], 'location_title');
						$html .='<li><font style="font-size:12px;padding-left:5px;">'.stripslashes(wordwrap($subSubLoc, 25, '<br />&nbsp;')).'</font></li>';					
						$html .='<li><font style="font-size:12px;padding-left:5px;">'.stripslashes(wordwrap($subLoc[0]['location_title'], 25, '<br />&nbsp;')).'</font></li>';					
					}else{
						$subLoc = $obj->getDataByKey('project_monitoring_locations', 'location_id', $fi["sub_location_id"], 'location_title');
						$html .='<li><font style="font-size:12px;padding-left:5px;">'.stripslashes(wordwrap($subLoc, 25, '<br />&nbsp;')).'</font></li>';
					}
				$html .='</ul>';
		$html .= '</td>
			<td><font style="font-size:12px;padding-left:5px;">'.stripslashes(wordwrap($fi["task"], 30, "<br />")).'</font></td>
			<td><font style="font-size:12px;padding-left:5px;">';
				$issue = $obj->getRecordsSp('issued_to_for_progress_monitoring', 'progress_id', $fi["progress_id"], 'issued_to_name');
				if(!empty($issue)){
					$html .='<ul style="list-style:none;margin-left:-37px;">';
					foreach($issue as $issueTo){
						$html .='<font style="font-size:12px;"><li>'.stripslashes(wordwrap($issueTo['issued_to_name'], 25, '<br />', true)).'</li></font>';
					}
					$html .='</ul>';
				}
				$html .='</td><td align="center"><font style="font-size:12px;padding-left:5px;">';
			if($fi["DateRaised"] != '0000-00-00'){
				$html .= stripslashes(date("d/m/Y", strtotime($fi["start_date"])));
			}
			$html .= '</font></td>
			<td align="center"><font style="font-size:12px;padding-left:5px;">';
			if($fi["FixedByDate"] != '0000-00-00'){
				$html .= stripslashes(date("d/m/Y", strtotime($fi["end_date"])));
			}
			$html .= '</font></td>
			<td><font style="font-size:12px;padding-left:5px;">'.stripslashes(wordwrap($fi["status"], 24, "<br />")).'</font></td>
			<td><font style="font-size:12px;padding-left:5px;">'.stripslashes(wordwrap($fi["percentage"], 15, "<br />", true)).'</font></td></tr>';
$pageBreak = 10;
if ($i%$pageBreak == 0){
	$html .='</tr></table><div class="footer" style="text-align:center;font-weight:bold;margin-top:5px;font-size:10px;" >DefectID, part of the Wiseworker Quality Management Ecosystem, helping the construction industry.<br />www.wiseworker.net</div>
	<div style="page-break-before: always;"></div>
	<table width="98%" cellpadding="0" cellspacing="0" align="center" class="collapse">
		<tr>
			<td style="width:100px;font-size:12px;" align="center"><strong>Location</strong></td>
			<td style="width:140px;font-size:12px;" align="center"><strong>Task</strong></td>
			<td style="width:50px;font-size:12px;" align="center"><strong>Issued To</strong></td>
			<td style="width:50px;font-size:12px;" align="center"><strong>Start Date</strong></td>
			<td style="width:50px;font-size:12px;" align="center"><strong>Finish Date</strong></td>
			<td style="width:50px;font-size:12px;" align="center"><strong>Status</strong></td>
			<td style="width:70px;font-size:12px;" align="center"><strong>% Complete</strong></td>
		</tr>';
}
		}
	$html .= '</table><br /><br /><br /><br />';
	$html .= '<div class="footer" style="text-align:center;font-weight:bold;margin-bottom:0;font-size:10px;" >DefectID, part of the Wiseworker Quality Management Ecosystem, helping the construction industry.<br />www.wiseworker.net</div>';

#echo $html;die;
//Code for Calculate Execution Time
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$endtime = $mtime; 
	$totaltime = ($endtime - $starttime); 
	$totaltime = number_format($totaltime, 2, '.', '');
//Code for Calculate Execution Time

?>
<div id="mainContainer">
	<div class="buttonDiv">
		<span style="padding-left:25px;font-size:15px;"><?php echo $totalCount.' results ('.$totaltime.' seconds)';?></span><br /><br />
		<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
		<img onClick="downloadPDFPM();"src="images/download_btn.png" style="float:right;" />
	</div><br clear="all" />
	<?php $pageCount = $totalCount / $limit;?>
	<div class="pagination" <?php if($totalCount <= $limit){ echo 'style="display:none;" ';}?>>
		<?php $leftLimit = $offset - $limit;
			if($leftLimit < 0){ $leftLimit = 0; }else{ }?>
		<img id="previousImages" src="images/prev_icon.png" onclick="pageScrollPM(<? echo $leftLimit;?> );"
		<?php if($leftLimit < 0){ echo 'style="display:none;float:left;"'; }else{ echo 'style="float:left"';} ?> />
		<?php if($pageCount > 0){
				for($l=0; $l<$pageCount; $l++){?>
					<span <? if(($l*$limit) == $offset){
						echo 'class="page_active" ';
					}else{
						echo 'class="page_deactive" ';
					}
					if($l >= 5){ echo 'style="display:none;" '; }
					?>
				onclick="pageScrollPM(<?php echo ($l*$limit); ?>)" ><?php echo ($l+1)?></span>
		<?php 	}
				if($l >= 5){ ?>
					<span><strong>.</strong></span>
					<span><strong>.</strong></span>
					<span><strong>.</strong></span>
			<? }
		}
		$rightLimit = $offset + $limit;
		if($rightLimit >= $totalCount){ $rightLimit = $totalCount; }else{ } ?>
		<img id="nextImages" src="images/next_icon.png" onclick="pageScrollPM(<?php echo $rightLimit;?>);"
		<?php if($rightLimit > $totalCount){ echo 'style="margin-left:5px;display:none;"'; }else{ echo 'style="margin-left:5px;"'; }?> />
	</div><br /><br /><br />
	<div id="htmlContainer">
		<?php echo $html;?>
	</div>
	<?php $pageCount = $totalCount / $limit;?>
	<div class="pagination" <?php if($totalCount <= $limit){ echo 'style="display:none;" ';}?>>
		<?php $leftLimit = $offset - $limit;
			if($leftLimit < 0){ $leftLimit = 0; }else{ }?>
		<img id="previousImages" src="images/prev_icon.png" onclick="pageScrollPM(<? echo $leftLimit;?> );"
		<?php if($leftLimit < 0){ echo 'style="display:none;float:left;"'; }else{ echo 'style="float:left"';} ?> />
		<?php if($pageCount > 0){
				for($l=0; $l<$pageCount; $l++){?>
					<span <? if(($l*$limit) == $offset){
						echo 'class="page_active" ';
					}else{
						echo 'class="page_deactive" ';
					}
					if($l >= 5){ echo 'style="display:none;" '; }
					?>
				onclick="pageScrollPM(<?php echo ($l*$limit); ?>)" ><?php echo ($l+1)?></span>
		<?php 	}
				if($l >= 5){ ?>
					<span><strong>.</strong></span>
					<span><strong>.</strong></span>
					<span><strong>.</strong></span>
			<? }
		}
		$rightLimit = $offset + $limit;
		if($rightLimit >= $totalCount){ $rightLimit = $totalCount; }else{ } ?>
		<img id="nextImages" src="images/next_icon.png" onclick="pageScrollPM(<?php echo $rightLimit;?>);"
		<?php if($rightLimit > $totalCount){ echo 'style="margin-left:5px;display:none;"'; }else{ echo 'style="margin-left:5px;"'; }?> />
	</div><br clear="all" />
</div>
<?php }else{?>
	<div style="margin-left:10px;color:#000;"><i>No Record Found</i></div>
<? }?>