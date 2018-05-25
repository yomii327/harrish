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
#print_r($_POST);die;

	$obj = new COMMAN_Class();
	
	$issued_to_add='';
	if (isset($_SESSION["ww_is_builder"])){
		$owner_id = $_SESSION['ww_builder_id'];	
	}elseif (isset($_SESSION['ww_owner_id'])){
		$owner_id = $_SESSION['ww_owner_id'];
	}elseif ($_SESSION['ww_is_company']){
		$owner_id = "company";
	}
	
	if(!isset($_REQUEST['startWith'])){$offset = 0;}else{$offset = $_REQUEST['startWith'];}
	$limit = 150;
	
	$where = '';$groupBy = '';
	$loc = $object->db_query("select * from progress_monitoring as PM where project_id = '".$_REQUEST['projName']."' and is_deleted = '0'");

	if(!empty($_REQUEST['projName'])){
		$where=" and PM.project_id='".$_REQUEST['projName']."'";
	}
	if(!empty($_REQUEST['location'])){
		$postCount++;
		$where.=" and PM.location_id= '".$_REQUEST['location']."'";
	}
//if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
	//$groupBy = " GROUP BY sub_location_id";
//}
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

	if(!empty($_REQUEST['issuedToPM'])){
		$include_issue_to = ", issued_to_for_progress_monitoring ipm";
		$where.="  and ipm.issued_to_name = '".$_REQUEST['issuedToPM']."' and PM.progress_id = ipm.progress_id and ipm.is_deleted = 0";
	}
	$qi="select
				PM.progress_id,
				PM.project_id,
				PM.location_id,
				PM.sub_location_id,
				PM.task,
				PM.start_date,
				PM.end_date,
				PM.status,
				PM.holding_point,
				PM.percentage, 
				PM.location_tree_name
			from
				progress_monitoring as PM $include_issue_to
			where
				PM.is_deleted = 0 $where group by PM.progress_id order by PM.progress_id, location_id, sub_location_id LIMIT $offset , $limit";
		
	$ri = mysql_query($qi);
	
	$queryCount = "SELECT COUNT(PM.progress_id) as totalCount FROM progress_monitoring as PM $include_issue_to WHERE PM.is_deleted = 0 $where";
	$resCount=mysql_query($queryCount);
	$countQuery = mysql_fetch_object($resCount);
	if($countQuery->totalCount > 0){
		$totalCount = $countQuery->totalCount;
	}
	
	$noInspection = mysql_num_rows($ri);
	$ajaxReplay = $noInspection.' Records';
	$noPages = ceil(($noInspection-40)/64 +1);
	if($noInspection > 0){
		$currLoctree = '';
		$tr_arr = array();
		while ($row=mysql_fetch_assoc ($ri)){
			$isHolePoint = '';
			if($currLoctree != $row['location_tree_name']){
				$tr_arr[] = '<tr><td align="center" style="width:160px;font-size:11px;"><strong>'.$row['location_tree_name'].'</strong></td><td align="center" style="width:70px;font-size:11px;"><strong>Status</strong></td><td align="center" style="width:80px;font-size:11px;"><strong>% Complete</strong></td><td align="center" style="width:80px;font-size:11px;"><strong>Issued To</strong></td><td align="center" style="width:80px;font-size:11px;"><strong>Sign Off</strong></td><td align="center" style="width:80px;font-size:11px;"><strong>Date</strong></td><td align="center" style="width:80px;font-size:11px;"><strong>Hacer Sign Off</strong></td><td align="center" style="width:80px;font-size:11px;"><strong>Date</strong></td></tr>';
			}
			if($row['holding_point'] == 'Yes' || $row['holding_point'] == 'yes' || $row['holding_point'] == 'YES'){
				$isHolePoint = 'style="background-color:#808080;"';
			}
			$tr_arr[] = '<tr '.$isHolePoint.'><td>'.$row['task'].'</td><td>'.$row['status'].'</td><td>'.$row['percentage'].'</td>';
			$issue = $obj->getRecordsSp('issued_to_for_progress_monitoring', 'progress_id', $row['progress_id'], 'issued_to_name');
			$tr_arr[] .= '<td>';
			if(!empty($issue)){
				$tr_arr[] .= '<ul style="list-style:none;margin-left:-37px;">';
				foreach($issue as $issueTo){
					$tr_arr[] .= '<font style="font-size:12px;"><li>'.stripslashes(wordwrap($issueTo['issued_to_name'], 25, '<br />', true)).'</li></font>';
				}
				$tr_arr[] .= '</ul>';
			}
			$tr_arr[] .= '</td>';
			$tr_arr[] .= '<td></td><td></td><td></td><td></td></tr>';
			$currLoctree = $row['location_tree_name'];
		}
		//die;
	$html = '<table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
				<tr>
					<td width="40%"></td><td width="70%" align="right" style="padding-right:20px;">
						<img src="company_logo/logo.png" height="40"  />
					</td>
				</tr>';
	if($offset == 0){
		$html .= '<tr>
					<td width="40%" style="font-size:14px;"><u><b>Door Sheet</b></u></td>
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
					<td style="padding-left:30px;" colspan="2"><table width="500" border="0"><tr>';
					$jk=0;
			if(!empty($_REQUEST['location']) && empty($_REQUEST['subLocation'])){
				$html .= '<td width="140" style="font-size:11px;"><b>Location Name&nbsp;: </b></td>
					<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['location'], 'location_title').'</td>';
			}
			if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation']) && !empty($_REQUEST['subLocation_sub'])){
				$html .= '<td width="140" style="font-size:11px;"><b>Location Name&nbsp;: </b></td>
					<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['location'], 'location_title').'</td>';$jk++;
				$html .= '<td width="140" style="font-size:11px;"><b>Sub Location Name&nbsp;: </b></td>
					<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['subLocation'], 'location_title').'</td>';$jk++;
				$html .= '</tr><tr><td width="140" style="font-size:11px;"><b>Sub Location 1&nbsp;: </b></td>
					<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['subLocation_sub'], 'location_title').'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
			}else{
				if(!empty($_REQUEST['location']) && !empty($_REQUEST['subLocation'])){
				$html .= '<td width="140" style="font-size:11px;"><b>Location Name&nbsp;: </b></td>
					<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['location'], 'location_title').'</td>';$jk++;
				$html .= '<td width="140" style="font-size:11px;"><b>Sub Location Name&nbsp;: </b></td>
					<td width="110" style="font-size:11px;">'.$obj->getDataByKey('project_monitoring_locations', 'location_id', $_REQUEST['subLocation'], 'location_title').'</td>';$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
			}
			}
			if(!empty($_REQUEST['status']) && isset($_REQUEST['status'])){
				$html .= '<td width="140" style="font-size:11px;"><b>Status&nbsp;: </b></td>
				<td width="100" style="font-size:11px;">'.$_REQUEST['status'].'</td>';
				$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
			}
			if(!empty($_REQUEST['DRF']) && isset($_REQUEST['DRF']) || !empty($_REQUEST['DRT']) && isset($_REQUEST['DRT'])){
				$html .= '<td width="140" style="font-size:11px;"><b>Date Raised&nbsp;: </b></td>
				<td width="100" style="font-size:11px;">'.$_REQUEST['DRF'].' to '.$_REQUEST['DRT'].'</td>';
				$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
			}
			if(!empty($_REQUEST['FBDF']) && isset($_REQUEST['FBDF']) || !empty($_REQUEST['FBDT']) && isset($_REQUEST['FBDT'])){
			   $html .= '<td width="140" style="font-size:11px;""><b>Fixed By Date&nbsp;: </b></td>
			   <td width="100" style="font-size:11px;"">'.$_REQUEST['FBDF'].' to '.$_REQUEST['FBDT'].'</td>';
				$jk++; if($jk%2 !=0){$html .= '';}if($jk%2 ==0){$html .= '</tr><tr>';}
			}
			$html .= '</tr></table></td></tr>';
	}
	$html .= '</table><br />';
	$pageCount = 1;
	if(!empty($tr_arr)){
		if (count($tr_arr) > 0 && $pageCount!=1){
			$html .= '<div style="page-break-before: always;"></div>';
		}$pageCount++;
		$html .= '<table width="98%" class="collapse" cellpadding="0" cellspaccing="0" align="center">';
		for($i=0;$i<sizeof($tr_arr);$i++){
			$html .= $tr_arr[$i];
		}
		$html .= '</table>';
		if (count($tr_arr) > 0 && $pageCount!=1){
			$html .= '<div class="footer" style="text-align:center;font-weight:bold;font-size:10px;" >DefectID, part of the Wiseworker Quality Management Ecosystem, helping the construction industry.<br />www.wiseworker.net</div>';
		}
	}
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
		<div id="htmlContainer">
			<?php echo $html;?>
		</div>
		<br clear="all" />
	</div>
	<?php }else{?>
		<div style="margin-left:10px;color:#000;"><i>No Record Found</i></div>
	<? }?>