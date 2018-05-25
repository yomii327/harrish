<?php
//Code for Calculate Execution Time
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$starttime = $mtime;
//Code for Calculate Execution Time

ob_start();
//echo dirname(__FILE__);die('WEweho');
include(dirname(__FILE__).'/../includes/commanfunction.php');
//require(dirname(__FILE__).'/../fpdf/mc_table.php');
require(dirname(__FILE__).'/../fpdf/fpdf.php');
require_once(dirname(__FILE__).'/../includes/class.phpmailer.php');
$obj = new COMMAN_Class();
$object = new DB_Class();

define("STOREFOLDER", dirname(__FILE__).'/deadlock', true);

	$postCount = 0;
	// $report_type = $_POST['report_type'];
	$images_path = "photo_detail/";
	$dimages_path = "drawing_detail/";

		$postCount++;
		$where = " and F.inspection_status='Open' AND date(I.created_date) = '".date('Y-m-d')."'";
		// $where = " and F.inspection_status='Open' AND date(I.created_date) = '2017-10-23'";


	$orderby = "";

	$qi="SELECT
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
		F.inspection_id as InspectionId_FOR,
		I.inspection_raised_by as RaisedBy,
		P.defect_clause as defect_clause,
		P.project_id as ProjectId,
		P.project_site_manager as SiteManager
	FROM
		user_projects as P, issued_to_for_inspections as F,
		project_inspections as I
	WHERE
		I.project_id = P.project_id and I.inspection_id = F.inspection_id and I.is_deleted = '0' and F.is_deleted = '0' and P.is_deleted = '0' $where group by I.inspection_id order by Project";

$ri=mysql_query($qi);
$noInspectionAll = mysql_num_rows($ri);
//$ajaxReplay = $noInspectionAll.' Records';
// $noPages = $noInspection;
$project_array = array();
while( $fi=mysql_fetch_row($ri)){
	$project_array[$fi[0]][] = $fi;
}
//get Location search name end here

if($noInspectionAll > 0){


	foreach ($project_array as $key => $values) {
		$arg = array();
		foreach ($values as $args) {
			$arg[$args[6]][] = $args;
		}
		// echo "<pre>";
		// print_r($arg);
		foreach ($arg as $issuedToDetails => $value) {
			$issuedToEmails = array();
			if(!empty($issuedToDetails)){
				$split1 = explode(" (", $issuedToDetails);
				if(!empty($split1)){
					$split2 = explode(")", $split1[1]);
				}
				if(!empty($split1) && !empty($split2)){
					$issueToEmail = $obj->selQRYMultiple('issue_to_email', 'inspection_issue_to', 'company_name = "'.stripslashes($split2[0]) . '" and issue_to_name = "'.stripslashes($split1[0]) . '" and is_deleted=0 group by issue_to_email');

					if(isset($issueToEmail) && !empty($issueToEmail)){

						foreach ($issueToEmail as $k => $email) {
							$issuedToEmails[] = $email['issue_to_email'];
						}
					}
				}
			}

			if(!empty($issuedToEmails)){

				$noInspection = count($value);
				$noPages = $noInspection;
				$ajaxReplay = $noInspection.' Records';

				$html='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<style>
				@charset "utf-8";
				body{
					font-family : Trebuchet MS, Arial, serif;
				}
				table.collapse {
					border-collapse: collapse;
					border: 1pt solid black;
				}
				table.collapse td{
					border: 1pt solid black;
					padding: 2px;
				}
				.cst_filter_section td{
					padding: 2px 5px;
					line-height: 10px;
				}
				 #footer { position: fixed; left: 0px; bottom: -150px; right: 0px; height: 150px; background-color: #FFFFFF; color:#000000; text-align:center; font:helvetica; font-weight:bold; Font-size:14px;}
				</style></head>
				<body>
				<script type="text/php">

		        if ( isset($pdf) ) {

		          $font = Font_Metrics::get_font("helvetica", "bold");
		          $pdf->page_text(200, 820, "", $font, 10, array(0,0,0));
		        }
		        </script>
				<table width="555" border="0" align="center">
					<tr>
						<td width="30%"></td><td width="70%" align="right" style="padding-right:20px;">';
					$html .='<img src="../company_logo/logo.png" height="40"  /></td>
					</tr></table>
					<table width="555" border="0" align="center">
					<tr>
						<td width="40%" style="font-size:14px"><u><b>Internal Report</b></u></td>
						<td>&nbsp;</td>
					</tr>
					<tr class="cst_filter_section">
						<td style="font-size:12px"><strong>Project Name: </strong>'.$key.'</td>
						<td>&nbsp;</td>
					</tr>
					<tr class="cst_filter_section">
						<td style="font-size:12px"><strong>Date: </strong>'.date('d/m/Y').'</td>
						<td>&nbsp;</td>
					</tr>
					<tr class="cst_filter_section">
						<td style="font-size:12px"><strong>Inspections: </strong>'.$noInspection.'</td>
						<td>&nbsp;</td>
					</tr></table>';
				$i=1;
				$pageCount = 1;
				// $issuedToEmails = array();
				$siteManager = "";
				#echo '<pre>';print_r($value);
				// while( $fi=mysql_fetch_row($ri)){
				foreach ($value as $k => $fi) {
					$siteManager = $fi[16];
					//get Location search name start here
					$projectlocationRows = $obj->selQRYMultiple ("location_id, location_title", "project_locations", "project_id=".$fi[15] . " and is_deleted=0" );
							$location_name_arr = array();
							foreach ($projectlocationRows as $locationID)
							{
								$location_name_arr[$locationID["location_id"]] = $locationID["location_title"];
							}

							$page_brk = ($i==1)?"":'style="page-break-before: always;"';
					$html .='<table width="555" border="0" align="center" '.$page_brk.'>

						<tr>
						<td style="padding-left:30px;" colspan="2"><table width="510" border="0"><tr>';
						$jk=0;


					$html .= '</tr></table></td></tr></table>';

					// echo $html;
					// die;
					$defect_clause = '';
					if(isset($fi[14]) && !empty($fi[14]) && empty($defect_clause)){
						$defect_clause = $fi[14];
					}

					$where = "";
					$where .= " and inspection_status = 'Open'";
					$issueToData = $obj->selQRYMultiple('issued_to_name, inspection_fixed_by_date, cost_attribute, inspection_status', 'issued_to_for_inspections', 'inspection_id = '.$fi[11] . ' and is_deleted=0 ' . $where .' group by issued_to_name');

					$issueToData_issueToName = ""; $issueToData_fixedByDate= ""; $issueToData_status= "";$issueToData_costAttribute = "";
					if(!empty($issueToData)){
						foreach($issueToData as $issueData){
							$issueData['issued_to_name'];
							$split = explode(" (", $issueData['issued_to_name']);
							stripslashes($split[0]);

							if($issueToData_issueToName == ''){
								$issueToData_issueToName = stripslashes($issueData['issued_to_name']);
							}else{
								$issueToData_issueToName .= ' > '.stripslashes($issueData['issued_to_name']);
							}

							if($issueToData_fixedByDate == ''){
								$issueData['inspection_fixed_by_date'] != '0000-00-00' ? $issueToData_fixedByDate = stripslashes(date("d/m/Y", strtotime($issueData['inspection_fixed_by_date']))) : $issueToData_fixedByDate = '' ;
							}else{
								$issueData['inspection_fixed_by_date'] != '0000-00-00' ? $issueToData_fixedByDate .= ' > '.stripslashes(date("d/m/Y", strtotime($issueData['inspection_fixed_by_date']))) : $issueToData_fixedByDate = '' ;
							}

							if($issueToData_status == ''){
								$issueToData_status = stripslashes($issueData['inspection_status']);
							}else{
								$issueToData_status .= ' > '.stripslashes($issueData['inspection_status']);
							}

							if($issueToData_costAttribute == ''){
								$issueToData_costAttribute = stripslashes($issueData['cost_attribute']);
							}else{
								$issueToData_costAttribute .= ' > '.stripslashes($issueData['cost_attribute']);
							}
						}
					}

					$iHeight = "270px";
					$html .= '<div></div>Page: </strong>'.$pageCount.' of '.$noPages . "<br/>" . $i . ".";
					$pageCount++;
					$html .='<table width="555" class="collapse" cellpadding="0" cellspaccing="0" align="center">
						<tr>
							<td style="background-color:#CCCCCC;width:100px;font-size:11px;"><i>&nbsp;Location</i></td>
							<td colspan="2" style="font-size:10px;width:150px">'.stripslashes(wordwrap($obj->subLocations($fi[1], ' > '), 25, '<br />&nbsp;')).'</td>
							<td colspan="3" style="background-color:#CCCCCC;width:300px;font-size:11px;"><i>&nbsp;Description</i></td>
						</tr>
						<tr>
							<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Date&nbsp;Raised</i></td>
							<td colspan="2" style="font-size:10px;">&nbsp;';
							if($fi[2] != '0000-00-00'){
								$html .=stripslashes(date("d/m/Y", strtotime($fi[2])));
							}
							$html .='</td>
							<td colspan="3" rowspan="7" valign="top" style="font-size:10px;">&nbsp;'.stripslashes(wordwrap($fi[9], 90, '<br />&nbsp;')).'</td>
						</tr>
						<tr>
							<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Inspected&nbsp;By</i></td>
							<td colspan="2" style="font-size:10px;">&nbsp;'.stripslashes($fi[3]).'</td>
						</tr>
						<tr>
							<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Inspection&nbsp;Type</i></td>
							<td colspan="2" style="font-size:10px;">&nbsp;'.stripslashes($fi[4]).'</td>
						</tr>
						<tr>
							<td style="background-color:#CCCCCC;font-size:11px;"><i>Raised&nbsp;By</i></td>
							<td colspan="2" style=" font-size:10px;">&nbsp;'.stripslashes($fi[13]).'</td>
						</tr>
						<tr>
							<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Issued&nbsp;To</i></td>
							<td colspan="2" style=" font-size:10px;">&nbsp;'.$issueToData_issueToName.'</td>
						</tr>
						<tr>
							<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Fix&nbsp;by&nbsp;Date</i></td>
							<td colspan="2" style="font-size:10px;">&nbsp;'.$issueToData_fixedByDate.'</td>
						</tr>
						<tr>
							<td style="background-color:#CCCCCC;font-size:11px;"><i>&nbsp;Status</i></td>
							<td colspan="2" style="font-size:10px;">&nbsp;'.$issueToData_status.'</td>
						</tr>
						<tr>
							<td colspan="3" align="center"  style="width:330px;height:'. $iHeight.'">';
					$images = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$fi[11].' AND is_deleted = 0 AND graphic_type = "images" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 2');
					if(isset($images[0]) && $images[0]['graphic_name'] != ''){
						$obj->resizeImages('../inspections/photo/'.$images[0]['graphic_name'], 320, 350, '../inspections/photo/photo_detail/'.$images[0]['graphic_name']);
						if(file_exists('../inspections/photo/'.$images_path.$images[0]['graphic_name'])){
							$html .='<img src="../inspections/photo/'.$images_path.$images[0]['graphic_name'].'"';
							if ($i==1)
							{
								$html .=' style="height: ' . $iHeight . '"';
							}
							$html .='/>';
						}else if(file_exists('../inspections/photo/'.$images[0]['graphic_name'])){
							$html .='<img src="../inspections/photo/'.$images[0]['graphic_name'].'" style="width:350px;" />';
						}else{

						}
					}
					$html .='</td><td colspan="3" align="center" style="width:330px;height:'. $iHeight.'">';
					if(isset($images[1]) && $images[1]['graphic_name'] != ''){
						$obj->resizeImages('../inspections/photo/'.$images[1]['graphic_name'], 320, 350, '../inspections/photo/photo_detail/'.$images[1]['graphic_name']);
						if(file_exists('../inspections/photo/'.$images_path.$images[1]['graphic_name'])){
							$html .='<img src="../inspections/photo/'.$images_path.$images[1]['graphic_name'].'"';
							if ($i==1)
							{
								$html .=' style="height: ' . $iHeight . '"';
							}
							$html .='/>';
						}else if(file_exists('../inspections/photo/'.$images[1]['graphic_name'])){
							$html .='<img src="../inspections/photo/'.$images[1]['graphic_name'].'"  style="width:350px;"/>';
						}else{

						}
					}
					$drawing = $obj->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$fi[11].' AND is_deleted = 0 AND graphic_type = "drawing" ORDER BY original_modified_date, last_modified_date DESC LIMIT 0, 1');
					if(isset($drawing[0]) && $drawing[0]['graphic_name'] != ''){
						$html .='</td></tr><tr><td colspan="6" align="center" style="width:330px;height:'. $iHeight.'">';
						$obj->resizeImages('../inspections/drawing/'.$drawing[0]['graphic_name'], 320, 350, '../inspections/drawing/drawing_detail/'.$drawing[0]['graphic_name']);
						if(file_exists('../inspections/drawing/'.$dimages_path.$drawing[0]['graphic_name'])){
							$html .='<img src="../inspections/drawing/'.$dimages_path.$drawing[0]['graphic_name'].'" ';
							if ($i==1)
							{
								$html .=' style="height: ' . $iHeight . '"';
							}
							$html .='/>';
						}else if(file_exists('../inspections/drawing/'.$drawing[0]['graphic_name'])){
							$html .='<img src="../inspections/drawing/'.$drawing[0]['graphic_name'].'"  style="width:350px;" />';
						}else{

						}
					}
					$html .='</td>
						</tr>
					</table>
					<div id="footer">
		    			<p class="page">DefectID © Copyright Wiseworking 2012 / 2013</p>
		  			</div>';
					$i++;

				}
				$html .= '<table border="0" cellpadding="0" cellspaccing="0" width="100%"><tr>
					<td style="font-size:12px"><strong>Project Defect Clause: </strong>'.$defect_clause.'</td>
					<td>&nbsp;</td>
				</tr></table>';

				$html .= '</body></html>';
				#echo $html;die;
				// echo "<pre>";
				// print_r($issuedToEmails);
				$issuedToEmails = array_filter(array_unique($issuedToEmails));
				$report = 'Internal_Report_'.microtime().'.pdf';
				#$report = 'Report_Detail.pdf';

				$fieSize = createPDF($html, $report, $owner_id);
				$fieSize = floor($fieSize/(1024));
				if ($fieSize > 1024){
					$fieSize = floor($fieSize/(1024)) . "Mbs";
				}else{
					$fieSize .= "Kbs";
				}
				$rply = $ajaxReplay.' '.$fieSize;
				$output_file ="../report_pdf/".$report;

				echo "<pre>";
				print_r($issuedToEmails);


				// die;

				# Send CSV file as attachment mail.
				$subject = 'HarrisHMC - '.$key;
				$message = 'Hello,<br><br>You have new Inspection(s) added in Project: '.$key.'<br><br>Regards,<br>'.$siteManager.'<br>Harris HMC.<br><br>HARRIS HMC RESERVES THE RIGHT TO ACTION OUTSTANDING ITEMS IF NOT CLOSED BY THE SCHEDULED DATE.';
				# To mail.
				$toDataList = array();

				//$toDataList[] = 'mahajan.ritesh@fxbytes.com';
				$toDataList = $issuedToEmails;

				# From mail
				$fromDataList = array();
				# Attachment list.
				$attachmentList = array();
				$attachmentList[] = $output_file;
				# CC mail
				$ccDataList = array();
				//$ccDataList[] = 'wagh.chetan@fxbytes.com';

				# BCC mail
				$bccDataList = array();
				// $bccDataList[] = 'mahajan.ritesh@fxbytes.com';
				$bccDataList[] = 'tiwari.devesh@fxbytes.com';
				// $bccDataList[] = 'lanjhesh.manmohan@fxbytes.com';
				// $bccDataList[] = 'shukla.atul@fxbytes.com';
				// $bccDataList[] = 'wagh.chetan@fxbytes.com';


				#Let's check to email array.
				if(isset($toDataList) && !empty($toDataList)){
					$mail = $obj->sendEmails($toDataList, $subject, $message, $fromDataList, $ccDataList, $bccDataList, $attachmentList);
					echo $mail['message'].'<br>';

					# Insert data cron_for_emails_send_today
					#echo '<br>=============Insert:-cron_for_emails_send_today==================<br>';
					foreach($toDataList as $email){
						$cesQuery = "INSERT INTO cron_for_emails_send_today SET
							project_id = ". $fi[15] .",
							section_name = 'daily_report_pdf_detail_hd_cron',
							email = '". $email ."',
							created_by = 0,
							created_date = NOW(),
							last_modified_by = 0,
							last_modified_date =NOW()
						";
						if(mysql_query($cesQuery)){
							$output['cron_for_emails_send_today'][] = $email;
						}
					}

					# Insert data cron_email_data
					#echo '<br>=============Insert:-cron_email_data==================<br>';
					$cedQuery = "INSERT INTO cron_email_data SET
						project_id = ". $fi[15] .",
						subject = '". $subject ."',
						content = '". serialize($mailbody) ."',
						attachment = '". $output_file ."',
						email_from = '',
						email_to = '". serialize($toDataList) ."',
						email_cc = '". serialize($ccDataList) ."',
						email_bcc = '". serialize($bccDataList) ."',
						email_status = 1,
						module_type = 'daily_report_pdf_detail_hd_cron',
						created_by = 0,
						created_date = NOW(),
						last_modified_by = 0,
						last_modified_date =NOW()
					";
					if(mysql_query($cedQuery)){
						$output['cron_email_data'] = 1;
					}

				}

			}

		}//die;
	}

	//Code for Calculate Execution Time
		$mtime = microtime();
		$mtime = explode(" ",$mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = ($endtime - $starttime);
		$totaltime = number_format($totaltime, 2, '.', '');
	//Code for Calculate Execution Time

	/* ************************************************ */
	/* 				CRON EMAIL HISTORY					*/
	/* ************************************************ */
	#echo '<br>=============Insert:-cron_email_history==================<br>';
	$cehQuery = "INSERT INTO cron_email_history SET
					cron_name = 'daily_report_pdf_detail_hd_cron',
					domain = '',
					start_time = '". $starttime ."',
					end_time = '". $endtime ."',
					time_diffrence = '". $totaltime ."',
					created_by = 0,
					created_date = NOW(),
					last_modified_by = 0,
					last_modified_date =NOW()
				";
	if(mysql_query($cehQuery)){
		$output['cron_email_history'] = 1;
	}


}else{
	echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
}

function createPDF($html, $report, $owner_id){
	require_once("../dompdf/dompdf_config.inc.php");
	$paper='a4';
	$orientation='portrait';

	if ( get_magic_quotes_gpc() )
	$html = stripslashes($html);

	$old_limit = ini_set("memory_limit", "94G");
	ini_set('max_execution_time', 3600); //300 seconds = 5 minutes

	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->set_paper($paper, $orientation);
	$dompdf->render();

	//$dompdf->stream("report.pdf");
	//exit(0);
	$output = $dompdf->output($report);
	// generate pdf in folder
	$d = '../report_pdf/'.$owner_id;
	if(!is_dir($d))
		mkdir($d);
	if (file_exists($d.'/'.$report))
		unlink($d.'/'.$report);
	$tempFile = $d.'/'.$report;
	$fh = fopen($tempFile, 'w') or die("can't open file");
	$stringData = $output;
	fwrite($fh, $stringData);
	fclose($fh);

	return filesize($tempFile);
}
//End of function.
?>

