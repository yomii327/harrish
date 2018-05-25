<?php
ob_start();

session_start();
set_time_limit(6000000000000000000);


include('includes/commanfunction.php');
$obj= new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}
if (isset($_REQUEST["name"])){
//	print_r($_REQUEST);
/*	$userNameData = array();
	$userData = $obj->selQRYMultiple('up.user_id, u.user_fullname', 'user AS u, user_projects AS up', 'up.is_deleted = 0 AND u.is_deleted = 0 AND up.user_id = u.user_id AND up.project_id = '.$_SESSION['idp']);
	foreach($userData as $usData){
		$userNameData[$usData['user_id']] = $usData['user_fullname'];
	}*/
#	print_r($userNameData);die;
	$where = '';
	if(!empty($_REQUEST['status']) && $_REQUEST['status']!='All'){
		$where .= ' AND m.rfi_status = "'.$_REQUEST['status'].'"';
	}
	if(!empty($_REQUEST['keyword'])){ 
		$where .= ' AND (um.refrence_number LIKE "%'.$_REQUEST['keyword'].'%" OR um.rfi_number LIKE "%'.$_REQUEST['keyword'].'%")'; 
	}
	#echo $_SESSION['ww_builder_id'];die;
	if($_SESSION['ww_builder_id']==168){ 
	$messageData = $obj->getRecordByQuery("SELECT um.user_id, 
					um.thread_id,
					um.refrence_number,
					um.rfi_number,
					um.created_date,
					m.title as rfi_description,
					m.rfi_fixed_by_date,
					m.rfi_status,
					m.rfi_closed_date,
					m.title
			FROM
				pmb_user_message as um,
				pmb_message m
			WHERE
				um.message_id = m.message_id AND
				um.is_deleted = 0 AND
				m.is_draft = 0 AND
				um.project_id = '".$_SESSION['idp']."' AND
				m.message_type = '".$_REQUEST['messageType']."' AND
				um.rfi_number != '0'
				".$where."
			GROUP BY
				um.thread_id
			ORDER BY 
				CAST(um.rfi_number AS UNSIGNED) ASC", 'rdemo');
		}else{
		$messageData = $obj->getRecordByQuery("SELECT um.user_id, 
					um.thread_id,
					um.refrence_number,
					um.rfi_number,
					um.created_date,
					m.title as rfi_description,
					m.rfi_fixed_by_date,
					m.rfi_status,
					m.rfi_closed_date,
					m.title
			FROM
				pmb_user_message as um,
				pmb_message m
			WHERE
				um.message_id = m.message_id AND
				um.is_deleted = 0 AND
				m.is_draft = 0 AND
				um.project_id = '".$_SESSION['idp']."' AND
				um.user_id = '".$_SESSION['ww_builder_id']."' AND
				m.message_type = '".$_REQUEST['messageType']."' AND
				um.rfi_number != '0'
				".$where."
			GROUP BY
				um.thread_id
			ORDER BY
				um.rfi_number", 'rdemo');
			
		
		}
$threadArr = array();
//echo "<pre>";print_r($messageData);
foreach($messageData as $msgData){
	$threadArr[] = $msgData['thread_id'];
}

$userNameData = array();
$userData = $obj->selQRYMultiple('GROUP_CONCAT(DISTINCT u.user_fullname SEPARATOR ", ") AS users, um.thread_id, m.to_email_address', 'user AS u, pmb_user_message AS um, pmb_message as m', 'm.message_id = um.message_id AND u.is_deleted = 0 AND um.from_id = u.user_id AND um.is_cc_user = 0 AND um.thread_id IN ('.join(',', $threadArr).') AND u.user_id != '.$_SESSION['ww_builder_id'].' GROUP BY thread_id');
//echo "<pre>";print_r($userData);
foreach($userData as $usData){
	$userNameData[$usData['thread_id']] = $usData['users'];
}
/*echo "SELECT um.user_id, GROUP_CONCAT( DISTINCT um.from_id ) AS users, 
					um.refrence_number,
					um.rfi_number,
					um.created_date,
					m.title as rfi_description,
					m.rfi_fixed_by_date,
					m.rfi_status,
					m.rfi_closed_date
			FROM
				pmb_user_message as um,
				pmb_message m
			WHERE
				um.message_id = m.message_id AND
				um.is_deleted = 0 AND
				m.is_draft = 0 AND
				um.project_id = '".$_SESSION['idp']."' AND
				um.user_id = '".$_SESSION['ww_builder_id']."' AND
				m.message_type = '".$_REQUEST['messageType']."' AND
				um.rfi_number != 0 AND
				is_cc_user = 0
			GROUP BY
				um.thread_id
			ORDER BY
				um.rfi_number";die;*/
	//			m.rfi_fixed_by_date != '' AND
	$noInspection = sizeof($messageData);
//Retrive Location Tree and Data Start Here
	$logo = $obj->selQRYMultiple('project_logo','projects','is_deleted = 0 AND project_id = '.$_SESSION['idp'].'', 'data');
//issue to data fetch here
if(file_exists('project_images/'.$logo[0]['project_logo']) && !empty($logo[0]['project_logo'])){
	$logo_proj = 'project_images/'.$logo[0]['project_logo']; 	
}else{
	$logo_proj = 'company_logo/logo.png';
}  
	?>
	<fieldset>
						<legend>Search Filters:</legend>
							RFI Status:
								<select name="rfiStatus" id="rfiStatus1" class="select_box" style="margin:0px 6px 4px 0px; width:120px;background-image:url(images/input_120.png);">
									<?php $rfiStatusArr = array('All', 'Open', 'Closed');
									foreach($rfiStatusArr as $key=>$rfiStatusVal){?>
										<option value="<?=$rfiStatusVal;?>" <?php if($rfiStatusVal==$_REQUEST['status']){?> selected="selected" <?php }?>><?=$rfiStatusVal;?></option>
									<?php }?>
								</select>
								Search Keyword:<input type="text" style="width: 220px;background-image: url(images/selectSpl.png);margin-left:0px;margin-left:28px;padding: 0 20px 0 20px;" class="input_small" id="searchKey" name="searchKey" value="<?php echo ($_REQUEST['keyword']?$_REQUEST['keyword']:'');?>">
								<img style="cursor:pointer; position:absolute;" onclick="generateReportFilter('<?php echo $_REQUEST['messageType'];?>');" alt="Generate Report" src="images/generate_reppmb_ort.png">
					</fieldset>
						<br><br><br>
	<?php 
	if($noInspection > 0 && is_array($messageData)){
	
		$html = '<table width="98%" border="0" align="center">
			<tr>
				<td width="50%" style="font-size:14px" ><u><b>'.$_REQUEST['messageType'].' Register Report'.'</b></u></td>
				<td width="50%" align="right" style="padding-right:20px;">
					<img src="'.$logo_proj.'" height="40"  />
				</td>
			</tr>
		</table><br /><br /><br />';
		$add = '<td style="font-size:12px;font-weight:bold;"><strong>Lag</strong></td>';
		if($_REQUEST['status'] == "Closed"){
			$add = '';
		}

		if(!empty($messageData)){ 
			$html .= '<table width="98%" class="collapse" cellpadding="0" cellspaccing="0" align="center" border="0">
				<tr>
					<td style="font-size:12px;font-weight:bold;"><strong>Correspondence No</strong></td>
					<td style="font-size:12px;font-weight:bold;"><strong>Date</strong></td>
					<td style="font-size:12px;font-weight:bold;"><strong>Subject</strong></td>
					<td style="font-size:12px;font-weight:bold;"><strong>To</strong></td>
					<td style="font-size:12px;font-weight:bold;"><strong>Due Date</strong></td>
					'.$add.'
					<td style="font-size:12px;font-weight:bold;"><strong>RFI Status</strong></td>
					<td style="font-size:12px;font-weight:bold;"><strong>Date closed out</strong></td>
				</tr>';
			foreach($messageData as $msgData){
				if($msgData['created_date'] != '0000-00-00 00:00:00')
					$dateRaised = date('d/m/Y', strtotime($msgData['created_date']));
				
				if($msgData['rfi_fixed_by_date'] != '0000-00-00'){
					$fixedByDate = date('d/m/Y', strtotime($msgData['rfi_fixed_by_date']));
					
					if((strtotime(date("Y-m-d")) > strtotime($msgData['rfi_fixed_by_date'])) && $msgData['rfi_status'] != "Closed"){
						$diff = abs(strtotime(date("Y-m-d")) - strtotime($msgData['rfi_fixed_by_date']));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
					}else{
						$days = '&nbsp';
					}
				}
				$closedDate = '';
				if(($msgData['rfi_closed_date'] != '0000-00-00') && $msgData['rfi_status'] == "Closed"){
					$closedDate = date('d/m/Y', strtotime($msgData['rfi_closed_date']));	
				}
				
				

				$html .= '<tr>
					<td>'.trim($msgData['rfi_number']).'</td>
					<td>'.$dateRaised.'</td>
					<td>'.trim($msgData['title']).'</td>
					<td>'.$userNameData[$msgData['thread_id']].'</td>
					<td>'.$fixedByDate.'</td>';
				$addValue = '<td>'.$days.'</td>';
				if($_REQUEST['status'] != "Closed"){
					$html .= $addValue;
				}

				$html .= '<td>'.$msgData['rfi_status'].'</td>
					<td>'.$closedDate.'</td>';
				$html .= '</tr>';
				
			}				
		
			$html .= '</table>';
		}?>
					
	        <div id="mainContainer">
				<div class="buttonDiv">
					<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
					<img onClick="downloadPDF();"src="images/download_btn.png" style="float:right;" />
				</div>
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
