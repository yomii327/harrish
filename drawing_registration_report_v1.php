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

	$userNameData = $obj->selQRYMultiple('user_id, user_fullname, company_name', 'user', 'is_deleted = 0');
	$userNameArr = array();
	foreach($userNameData as $uNameData){
		$userNameArr[$uNameData['user_id']] = $uNameData['user_fullname'];
	}

	$docData = array();
	#$attribute1Arr = array('"General"', '"Architectural"', '"Structure"', '"Services"', '"Shop Drawings"', '"Concrete & PT"', '"Civil / Landscaping"', '"ESD / Green Star"', '"Survey"', '"Shop Drawings"');
	$attribute1Arr = array('"General"', '"Architectural"', '"Structure"', '"Structural"', '"Mechanical"', '"Civil"', '"Electrical"', '"Hydraulics"', '"Fire Services"', '"Landscaping"', '"Spec., Sched. & Reports"', '"Models"',  '"Services"', '"Shop Drawings"', '"Concrete & PT"', '"Civil / Landscaping"', '"ESD / Green Star"', '"Survey"', '"Shop Drawings"');
	if($_SESSION['userRole'] == 'Architect'){
		$attribute1Arr = array('"Architectural"');
	}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Structural') || ($_SESSION['userRole'] == 'Structural Engineer')){
		$attribute1Arr = array('"Structure"');
	}elseif(($_SESSION['userRole'] == 'General Consultant' && $_SESSION['subUserRole'] == 'Services') || ($_SESSION['userRole'] == 'Services Engineer')){
		$attribute1Arr = array('"Services"');
	}

	if(isset($_GET["attribute1"]) && $_GET["attribute1"] != "All" && $_GET["attribute1"] != ''){
		$attribute1Arr = array("'".$_GET["attribute1"]."'");
	}
	#echo "<pre>"; print_r($attribute1Arr); die;
	$secOrderBy = "";
	if($_GET["sortBy"] != '' && isset($_GET["sortBy"])){
		if($_GET["sortBy"] == 'attribute2')
			$secOrderBy = ", dr.attribute2";
		else
			$secOrderBy = ", dr.number";
	}
	if($secOrderBy == "")
		$secOrderBy = ", dr.number";
	
	$sect = ", max(drr.id) AS revID";
	$orderBy = " ORDER BY dr.attribute1 ".$secOrderBy." asc";
	$groupBy = " GROUP BY dr.id";
	if(isset($_GET["revisionType"]) && !empty($_GET["revisionType"])){
		if($_GET["revisionType"] == 'complete'){
			$orderBy = " ORDER BY dr.attribute1 ".$secOrderBy." asc, drr.id desc";
			$groupBy = " ";
			$sect = " ";
		}
	}
	$docData = $obj->selQRYMultiple('dr.id, dr.number, dr.title, dr.pdf_name, dr.created_date, dr.is_approved, drr.revision_number, drr.created_date AS revisionAdded, dr.attribute1, dr.attribute2, dr.status, dr.uploaded_date, drr.last_modified_by'.$sect, 'drawing_register_revision_module_one AS drr, drawing_register_module_one AS dr', 'drr.is_deleted = 0 AND dr.is_deleted = 0 AND drr.drawing_register_id = dr.id AND dr.attribute1 IN ('.join(',', $attribute1Arr).') AND dr.is_document_transmittal = 0 AND dr.project_id = '.$_REQUEST['projID'].' AND dr.project_id = '.$_REQUEST['projID'].' '.$groupBy.' '.$orderBy);
	#$docData = $obj->selQRYMultiple('dr.id, dr.number, dr.title, dr.pdf_name, dr.created_date, dr.is_approved, drr.revision_number, drr.created_date AS revisionAdded, dr.attribute1, dr.attribute2, dr.status, dr.uploaded_date, drr.last_modified_by'.$sect, 'drawing_register_revision_module_one AS drr, drawing_register_module_one AS dr', 'drr.is_deleted = 0 AND dr.is_deleted = 0 AND drr.drawing_register_id = dr.id AND dr.is_document_transmittal = 0 AND dr.project_id = '.$_REQUEST['projID'].' AND dr.project_id = '.$_REQUEST['projID'].' '.$groupBy.' '.$orderBy);
	 #echo "<pre>";print_r($docData);
	if($_GET["revisionType"] != 'complete'){
		$drIDArr = array();
		foreach($docData as $dData){
			$drIDArr[] = $dData['revID'];
		}
		$revisionNumberData = $obj->selQRYMultiple('id, revision_number, drawing_register_id', 'drawing_register_revision_module_one', 'id IN ('.join(',', $drIDArr).')');
		$curretnVerArr = array();
		foreach($revisionNumberData as $revData){
			$curretnVerArr[$revData['id']] = $revData['revision_number'];
		}
	}
	
	$noInspection = 0;
	if(is_array($docData)){
		$noInspection = sizeof($docData);
	}
$logo = $obj->selQRYMultiple('project_logo','projects','is_deleted = 0 AND project_id = '.$_SESSION['idp'].'', 'data');

if(file_exists('project_images/'.$logo[0]['project_logo']) && !empty($logo[0]['project_logo'])){
	$logo_proj = 'project_images/'.$logo[0]['project_logo']; 	
}else{
	$logo_proj = 'company_logo/logo.png';
}
	if($noInspection > 0){
		$html = '<table width="98%" border="0" align="center">
			<tr>
				<td width="40%"></td>
				<td width="60%" align="right" style="padding-right:20px;">
					<img src="'.$logo_proj.'" height="40"  />
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
			$head= '<table width="98%" class="collapse" cellpadding="0" cellspaccing="0" align="center" border="0">
				<tr>
					<td style="font-size:12px;font-weight:bold;" width="10%"><strong>Document Number</strong></td>
					<td style="font-size:12px;font-weight:bold;" width="10%"><strong>Document Title</strong></td>
					<td style="font-size:12px;font-weight:bold;" width="10%"><strong>Document Type</strong></td>
					<td style="font-size:12px;font-weight:bold;" width="10%"><strong>Date Added</strong></td>
					<td style="font-size:12px;font-weight:bold;" width="10%"><strong>Status</strong></td>
					<td style="font-size:12px;font-weight:bold;" width="10%"><strong>Revision No</strong></td>
					<td style="font-size:12px;font-weight:bold;" width="10%"><strong>Revision Date</strong></td>
					<td style="font-size:12px;font-weight:bold;" width="10%"><strong>Attribute 2</strong></td>
					<td style="font-size:12px;font-weight:bold;" width="10%"><strong>Last Modified By</strong></td>
				</tr>';
				$oldAttribute1='';
			foreach ($docData as $doc){
				if(empty($oldAttribute1) && !empty($doc['attribute1'])){
					$oldAttribute1 = $doc['attribute1'];
					$html .= '<h4>'.$doc['attribute1'].'</h4>';			
					$html .= $head;
					
				}else if($oldAttribute1 != $doc['attribute1']){
					$oldAttribute1 = $doc['attribute1'];
					$html .= '</table><h4>'.$doc['attribute1'].'</h4>';			
					$html .= $head;
				}
				
				$html .= '<tr>
					<td>'.$doc['number'].'</td>
					<td>'.$doc['title'].'</td>';
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
				$html .= '<td>'.$doc['status'].'</td>';
				if($_GET["revisionType"] == 'complete'){
					$html .= '<td style="word-break:break-all;">'.$doc['revision_number'].'</td>';
				}else{
					$html .= '<td style="word-break:break-all;">'.$curretnVerArr[$doc['revID']].'</td>';
				}
				$html .= '<td>'.date('d/m/Y', strtotime($doc['revisionAdded'])).'</td>
					<td>'.str_replace("###", ",", $doc['attribute2']).'</td>
					<td>'.$userNameArr[$doc['last_modified_by']].'</td>
				</tr>';
			}
			$html .= '</table>';
		}
		#echo $html;
		?>
			<?php if($_GET['spectialCon']){?>
				<div class="buttonDiv">
					<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
					<img onClick="downloadPDF(<?=$_SESSION['idp']?>);"src="images/download_btn.png" style="float:right;" />
				</div>
				<br clear="all"  />
				<div id="htmlContainer">
					<?php echo $html;?>
				</div>
			<?php }else{?>
				<div id="static" style="text-align:center;margin-bottom:15px;border:1px solid black;padding:5px;">
					<table width="100%" border="0">
						<tr>
							<td>Revision :</td>
							<td>
								<select name="revisioinReport" id="revisioinReport" class="select_box" style="margin:5px 0 0 0;" />
									<option value="current" <? if($_GET["revisionType"] == 'current'){ echo 'selected="selected"'; }?>>Current Revision</option>
									<option value="complete" <? if($_GET["revisionType"] == 'complete'){ echo 'selected="selected"'; }?>>Complete Register</option>
								</select>
							</td>
							<td>&nbsp;</td>
							<td>Sorted By :</td>
							<td>
								<select name="sortByReport" id="sortByReport" class="select_box" style="margin:5px 0 0 0;" />
									<option value="drawingNumber" <? if($_GET["sortByReport"] == 'drawingNumber'){ echo 'selected="selected"'; }?>>Drawing Number</option>
									<option value="attribute2" <? if($_GET["sortByReport"] == 'attribute2'){ echo 'selected="selected"'; }?>>Attribute 2</option>
								</select>
							</td>
							<td>Discipline :</td>
							<td>
		                        <select name="drawattribute1" id="drawattribute1" class="select_box" style="margin:5px 0 0 0;" />
		                           	<option value="All">All</option>
		                           	<?php $attribute1Arr = array('General', 'Architectural', 'Structure', 'Services', 'Concrete &amp; PT', 'Civil / Landscaping', 'ESD / Green Star', 'Survey');
		                            for($i=0;$i<sizeof($attribute1Arr);$i++){?>
		                           		<option value="<?=$attribute1Arr[$i]?>"><?=$attribute1Arr[$i]?></option>
		                           	<?php }?>
		                        </select>
		                    </td>
						</tr>
						<tr>
							<td colspan="7">
								<!-- <img style="cursor:pointer; float:right; margin-left:10px;" onclick="runRerporDrawingRegister(<?=$_SESSION['idp']?>);" src="images/report_btn.png"> -->
								<a class="green_small" href="javascript:void(0)" onclick="runRerporDrawingRegister(<?=$_SESSION['idp']?>);" style="cursor:pointer;float:right; margin-left:10px;"  alt="report" />Report</a>
							</td>
						</tr>
					</table>
				</div>
				<div id="mainContainer">
					<div class="buttonDiv">
						<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
						<img onClick="downloadPDF(<?=$_SESSION['idp']?>);"src="images/download_btn.png" style="float:right;" />
					</div>
					<br clear="all"  />
					<div id="htmlContainer">
						<?php echo $html;?>
					</div>
				</div>
			<?php }?>
<?php	}else{
		echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
	}
}?>
<option 
