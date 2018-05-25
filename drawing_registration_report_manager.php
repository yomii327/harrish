<?php
session_start();
set_time_limit(6000000000000000000);

include('./includes/commanfunction.php');
$obj = new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}

if (isset($_REQUEST["name"])){
	mysql_query('SET SESSION group_concat_max_len = 4294967295');
	$userData = $obj->selQRYMultiple('GROUP_CONCAT(DISTINCT user_id) AS userIds', 'user_projects', 'project_id = '.$_REQUEST['projID'].' AND is_deleted = 0');

	$userNameData = $obj->selQRYMultiple('user_id, user_fullname, company_name', 'user', 'user_id IN ('.$userData[0]['userIds'].') AND is_deleted = 0');
	$userNameArr = array();
	$compNameArr = array();
	foreach($userNameData as $uNameData){
		$userNameArr[$uNameData['user_id']] = $uNameData['user_fullname'];
		$compNameArr[] = $uNameData['company_name'];
	}
	$compNameArr = array_unique($compNameArr);
?>
	<table width="100%" border="0">
		<tr>
			<td style="padding-left:10px;" >User</td>
			<td style="padding-left:10px;">Keyword</td>
			<td style="padding-left:10px;">Date Added</td>
			<td style="padding-left:10px;">Company</td>
			<!--<td style="padding-left:10px;">Revision</td>-->
			<td style="padding-left:10px;"><img onclick="resetReportSearch();" style="cursor:pointer; float:right;" src="images/reset_drw_search.png" title="Reset filter" align="top"  /></td>
		</tr>
		<tr>
			<td>
				<select name="userReport" id="userReport" class="select_box" style="width: 120px;
background-image: url(images/input_120.png);margin-left:0px;"  />
					<option value="">Select</option>
				<?php foreach($userNameData as $uNameArr){?>
						<option value="<?=$uNameArr['user_id']?>"><?=$uNameArr['user_fullname']?></option>
				<?php }?>
				</select>
			</td>
			<td>
				<input type="text" name="searchKeywordReport" id="searchKeywordReport" class="input_small" style="width: 150px;
background-image: url(images/input_160.png);" />
			</td>
			<td>
				<input name="DRF" type="text" id="DAF" size="7" readonly="readonly" />
				&nbsp;&nbsp;&nbsp;&nbsp;
				<input name="DRF" type="text" id="DAT" size="7" readonly="readonly" />
				&nbsp;
				<a href="javascript:void();" title="Clear Added Date"><img src="images/redCross.png" onClick="clearDateRaised();" /></a>
			</td>
			<td>
				<select name="companyReport" id="companyReport" class="select_box" style="width: 120px;
background-image: url(images/input_120.png);margin-left:0px;"  />
					<option value="">Select</option>
				<?php foreach($compNameArr as $key=>$val){?>
						<option value="<?=$val?>"><?=$val?></option>
				<?php }?>
				</select>
			</td>
			<!--<td>
				<select name="revisioinReport" id="revisioinReport" class="select_box" style="width: 120px;
background-image: url(images/input_120.png);margin-left:0px;"  />
					<option value="current">Current Revision</option>
					<option value="complete">Complete Register</option>
				</select>
			</td>-->
			<td>
				 <img src="images/report_btn.png" onclick="runRerpor();" style="cursor:pointer; float:right; margin-left:10px;"  />
			</td>
		</tr>
	</table>
	<div id="popUpReportResponse" style="min-height:10px;width:100%;margin-top:15px;max-height:450px;overflow:auto;"></div>
<?php }

if (isset($_REQUEST["uniqueID"])){
	$sWhere = " ";

	$userIdArr = array();$userNameArr = array();$companyUserArr = array();
	$userData = $obj->selQRYMultiple('u.user_id, u.user_fullname, u.company_name', 'user AS u, user_projects AS up', 'u.is_deleted = 0 AND up.is_deleted = 0 AND u.user_id = up.user_id AND up.project_id = '.$_REQUEST['projID'].' ORDER BY u.company_name');

	foreach($userData as $user){
		$userNameArr[$user['user_id']] = $user['user_fullname'];
		if(is_array($companyUserArr[$user['company_name']])){
			$companyUserArr[$user['company_name']][] = $user['user_id'];
		}else{
			$companyUserArr[$user['company_name']] = array();
			$companyUserArr[$user['company_name']][] = $user['user_id'];
		}
	}
	
	if(isset($_POST["userReport"]) && !empty($_POST["userReport"])){
		$sWhere .= " AND dr.created_by = \"".$_POST["userReport"]."\"";
	}
	
	if(isset($_POST["searchKeywordReport"]) && !empty($_POST["searchKeywordReport"])){
		$sWhere .= " AND ((dr.title LIKE \"%".$_POST["searchKeywordReport"]."%\") OR (dr.number LIKE \"%".$_POST["searchKeywordReport"]."%\") OR (dr.revision LIKE \"%".$_POST["searchKeywordReport"]."%\") OR (dr.comments LIKE \"%".$_POST["searchKeywordReport"]."%\"))";
	}
	
	if(isset($_POST["DAF"]) && !empty($_POST["DAF"]) && isset($_POST["DAT"]) && !empty($_POST["DAT"])){
		$sWhere .= " AND dr.created_date BETWEEN \"".date('Y-m-d', strtotime($_REQUEST['DAF']))." 00:00:00"."\" AND \"".date('Y-m-d', strtotime($_REQUEST['DAT']))." 23:59:59"."\"";
	}
	
	if(isset($_POST["companyReport"]) && !empty($_POST["companyReport"])){
		$sWhere .= " AND dr.created_by IN (".join(',', $companyUserArr[$_POST["companyReport"]]).")";
	}
	$orderBy = " ORDER BY dr.id";
	$groupBy = "";
/*	if(isset($_POST["revision"]) && !empty($_POST["revision"])){
		if($_POST["revision"] == 'current'){
			$orderBy = " ORDER BY drr.created_date Desc";
			$groupBy = "GROUP BY dr.id";
		}
	}*/
	
	$docData = array();
	$docData = $obj->selQRYMultiple('dr.id, dr.title, dr.created_by as addedBy, drr.pdf_name, dr.created_date, drr.created_by as revisedBy, dr.is_approved, drr.revision_number, drr.created_date AS revisionAdded', 'drawing_register_revision AS drr, drawing_register AS dr', 'drr.is_deleted = 0 AND dr.is_deleted = 0 AND  drr.drawing_register_id = dr.id AND dr.project_id = '.$_REQUEST['projID'].' AND drr.project_id = '.$_REQUEST['projID'].$sWhere);
	
	$noInspection = 0;

	if(is_array($docData)){
		$noInspection = sizeof($docData);
	}
	if($noInspection > 0){
		$html = '<table width="98%" border="0" align="center">
			<tr>
				<td width="40%"></td>
				<td width="60%" align="right" style="padding-right:20px;">
					<img src="company_logo/logo.png" height="40"  />
				</td>
			</tr>
			<tr>
				<td width="40%" style="font-size:14px"><u><b>Document Register Report</b></u></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Project Name: </strong>'.$obj->getDataByKey('user_projects', 'project_id', $_REQUEST['projID'], 'project_name').'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Date: </strong>'.date('d/m/Y').'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:12px"><strong>Total Document: </strong>'.$noInspection.'</td>
				<td>&nbsp;</td>
			</tr>
		</table><br /><br /><br />';
		if(!empty($docData)){
			$html .= '<table width="98%" class="collapse" cellpadding="0" cellspaccing="0" align="center" border="0">
				<tr>
					<td style="font-size:12px;font-weight:bold;"><strong>Document Title</strong></td>
					<td style="font-size:12px;font-weight:bold;"><strong>Added By</strong></td>
					<td style="font-size:12px;font-weight:bold;"><strong>Document Type</strong></td>
					<td style="font-size:12px;font-weight:bold;"><strong>Date Added</strong></td>
					<td style="font-size:12px;font-weight:bold;"><strong>Revised By</strong></td>
					<!--td style="font-size:12px;font-weight:bold;"><strong>Status</strong></td-->
					<td style="font-size:12px;font-weight:bold;"><strong>Revision No</strong></td>
					<td style="font-size:12px;font-weight:bold;"><strong>Revision Date</strong></td>
				</tr>';
			foreach ($docData as $doc){
				$html .= '<tr>
					<td>'.$doc['title'].'</td>
					<td>'.$userNameArr[$doc['addedBy']].'</td>
					';
					$pdfExtension = end(explode('.', $doc['pdf_name']));
					$fileExt = 'PDF File';
					if(strtolower($pdfExtension) == 'dwg'){
						$fileExt = 'Drawing File';
					}
				$html .= '<td>'.$fileExt.'</td>
					<td>'.date('d/m/Y', strtotime($doc['created_date'])).'</td>';
					$status = 'Not Approved';
					if($doc['is_approved'] == 1){
						$status = 'Approved';
					}
				$html .= '<!--td>'.$status.'</td-->
					<td>'.$userNameArr[$doc['revisedBy']].'</td>
					<td>'.$doc['revision_number'].'</td>
					<td>'.date('d/m/Y', strtotime($doc['revisionAdded'])).'</td>
				</tr>';
			}
			$html .= '</table>';
		}?>
			<div id="mainContainer">
				<!--<div class="buttonDiv">
					<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
					<img onClick="downloadPDF();"src="images/download_btn.png" style="float:right;" />
				</div>-->
				<br clear="all"  />
				<div id="htmlContainer">
					<?php echo $html;?>
				</div>
			</div>
<?php	}else{
		echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
	}
}
?>