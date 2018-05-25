<?php
ob_start();
//echo dirname(__FILE__);die('WEweho');
include(dirname(__FILE__).'/../includes/commanfunction.php');
require(dirname(__FILE__).'/../fpdf/mc_table.php');
require_once(dirname(__FILE__).'/../includes/class.phpmailer.php');
$obj = new COMMAN_Class();

define("STOREFOLDER", dirname(__FILE__).'/deadlock', true);

if(!is_dir(STOREFOLDER)){
	@mkdir(STOREFOLDER, 0777);
}

//Function Section Start Here
function addQuote($str){
	return '"'.$str.'"';
}
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//Function Section End Here
$weekDays = array(0, 1, 2, 3, 4, 5, 6, 7);
@unlink(STOREFOLDER.'/open_insp_proj.txt');	 
if(in_array(date('w'), $weekDays)){
	if(!file_exists(STOREFOLDER.'/open_insp_proj.txt')){

		// GET current date
		$curDateData = $obj->getRecordByQuery('SELECT NOW() AS date');
		$curDate = substr($curDateData[0]['date'], 0, 10);//Current time from server

		$inspData = array();


		// get the check list data
		/*$checkListData = $obj->selQRYMultiple("qa_checklist_id, sc_sign ,created_date, location_tree, status, project_id","qa_checklist","is_deleted = 0   ORDER BY created_date DESC");

        $checkList = array(); // store check list data
       
        foreach ($checkListData as $listData) {
        	$checkList[$listData['qa_checklist_id']]['location'] = $listData['location_tree'];
        	$checkList[$listData['qa_checklist_id']]['sc_sign'] = $listData['sc_sign'];
        
        }
*/

		$inspData = $obj->selQRYMultiple("qcTask.qa_checklist_id , qcTask.task_status_id, qcTask.checklist_task_id,qc.sub_contractor_name,qai.non_conformance_id, qai.qa_issued_to_name as IssueToName, qai.qa_inspection_status as Status, qai.project_id as projectId, qins.qa_inspection_raised_by as RaisedBy, qins.qa_inspection_date_raised as DateRaised, qins.qa_inspection_description as Description, qai.qa_inspection_fixed_by_date as FixedByDate, qins.task_id, qa_issued_to_id as InspectionId, qai.created_by  as InspectedBy, qc.sc_sign, qc.location_tree",
	  	  "qa_checklist AS qc , qa_checklist_task_status AS qcTask, qa_inspections AS qins ,qa_issued_to_inspections AS qai",
	  	  " qcTask.qa_checklist_id = qc.qa_checklist_id  AND qins.task_id=qcTask.task_status_id AND qins.non_conformance_id = qai.non_conformance_id  AND  qc.is_deleted = 0 AND  qcTask.is_deleted = 0 AND qai.qa_inspection_status='Open'
		  	AND qai.qa_issued_to_name != ''
		  	AND qai.created_date LIKE '%".$curDate."%' 
		  	AND qins.is_deleted = 0 
		  	AND qai.is_deleted = 0 "

	  	  );

		if(!empty($inspData)){
			#Set cron status.
			$cronStatus = 'Start';
			$remark = '';
			
			//Assign Empty Array
			$inspIssuedToArr = $inspIdArr = $issedToArr = $issedToNameArr = $projIdArr = $projNameArr  = $locNameArr = array();
			$emailContentArr = array();//Store email 
			$inspLocArr = array();//Store email 
			$inspStatusArr = array();//Store email 
			$nonConIdArr = array();
			
			foreach($inspData as $inData){
				
				$Location = $inData['location_tree'];
				$inspIssuedToArr[$inData['projectId']][$inData['IssueToName']][] = $inData;
				$projIdArr[$inData['projectId']] = $inData['projectId'];
				$issedToNameArr[$inData['IssueToName']] = $inData['IssueToName'];
				$inspIdArr[$inData['InspectionId']] = $inData['InspectionId'];
				$nonConIdArr[$inData['non_conformance_id']] = $inData['non_conformance_id'];
				$emailContentArr[$inData['InspectionId']] = array($inData['InspectionId'], $inData['projectId'], $Location, $inData['DateRaised'], $inData['Description'], $inData['InspectedBy'], $inData['Note'], $inData['CostAttribute'], $inData['RaisedBy'], $inData['FixedByDate'], $inData['sc_sign']);
			}

            
		    #echo "<pre>";	print_r($issedToNameArr);
			
			//Get the Project List
			$projects = $obj->selQRYMultiple("project_id, project_name, project_manager, project_manager_email, contact_person, contact_person_email", "projects", "is_deleted = 0 AND project_id IN (".join(',', $projIdArr).")", 'N');

			foreach($projects as $projVal){
				$projNameArr[$projVal['project_id']] = array('projName'=> $projVal['project_name'], 'projManager'=> $projVal['project_manager'], 'projManagerEmail'=> $projVal['project_manager_email'], 'contPerson'=> $projVal['contact_person'], 'contPersonEmail'=> $projVal['contact_person_email']); 
			}

			//Get the Project user List
			$users = $obj->selQRYMultiple("project_id, user.user_id, user_fullname", "user_projects, user", "user_projects.user_id=user.user_id and user.is_deleted = 0 AND user_projects.is_deleted = 0 AND project_id IN (".join(',', $projIdArr).")", 'N');
			$userNameArr = array();
			foreach($users as $userVal){
				$userNameArr[$userVal['user_id']] = $userVal['user_fullname']; 
			}
			
			                
	        // get


			
			//Get the Project List
			$issuedTo = $obj->selQRYMultiple("project_id, company_name, issue_to_email AS email, CONCAT(issue_to_name, ' (', company_name, ')') AS issuedToName", "inspection_issue_to", "is_deleted = 0 AND issue_to_email !='' AND project_id IN (".join(',', $projIdArr).") AND CONCAT(issue_to_name, ' (', company_name, ')') IN ('".join('\',\'', $issedToNameArr)."')", 'YES');
			

			foreach($issuedTo as $issuedVal){
				if(!empty($issuedVal['company_name'])){
					$issedToArr[$issuedVal['project_id']][$issuedVal['issuedToName']] = array('name'=>$issuedVal['issuedToName'], 'email'=>$issuedVal['email']); 
				}else{
					$issedToArr[$issuedVal['project_id']][$issuedVal['issue_to_name']] = array('name'=>$issuedVal['issue_to_name'], 'email'=>$issuedVal['email']); 
				}
			}

			
			//Get Location List
			$locationData = array();
			//$locationData = $obj->selQRYMultiple("location_id, project_id, location_title, location_name_tree", "project_locations", "is_deleted = 0 AND project_id IN (".join(',', $projIdArr).")", 'N');	
			/*$locNameArr = array();
			foreach($locationData as $locData){
				$locNameArr[$locData['location_id']] = array($locData['location_title'], $locData['location_name_tree']);
			}*/
           //print_r($nonConIdArr);
			//print_r($locNameArr); die; 
			//Get images list

          // $atdata = $obj->selQRYMultiple('qa_graphic_name,qa_graphic_id', 'qa_graphics', 'is_deleted = 0 AND non_conformance_id = '.$evdenceData['non_conformance_id']);

			$imageArr = $obj->selQRYMultiple(
								//Select
								' qa_graphic_id as graphic_id,task_id ,non_conformance_id as inspection_id, qa_graphic_name as graphic_name,qa_graphic_type as graphic_type',
								//From
								'qa_graphics',
								//Where
								'is_deleted = 0 AND
									non_conformance_id IN ('.join(",", array_keys($nonConIdArr)).')
							ORDER BY
								original_modified_date, last_modified_date DESC', 'N');
			
			$imagesArr = array();
			$drawingsArr = array();

			foreach($imageArr as $img){
				if($img['graphic_type'] == "images"){
					if(is_array($imagesArr[$img['inspection_id']])){
						$imagesArr[$img['inspection_id']][] = $img; 
					}else{
						$imagesArr[$img['inspection_id']] = array(); 
						$imagesArr[$img['inspection_id']][] = $img; 
					}	
				}else if($img['graphic_type'] == "drawing"){
					if(is_array($drawingsArr[$img['inspection_id']])){
						$drawingsArr[$img['inspection_id']][] = $img; 
					}else{
						$drawingsArr[$img['inspection_id']] = array(); 
						$drawingsArr[$img['inspection_id']][] = $img; 
					}
				}
			}
			
			if(!empty($issedToArr)){
				// Setup PDF header / footer
				class PDF extends PDF_MC_Table{
						function Header(){
							if($this->PageNo()!=1){
								$this->Cell(0, 10, $this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
								$this->ln();	
								$this->SetFont('times', 'B', 10);
								$header = array("ID", "Location", "Description", "Notes", "Inspected By", "Date Raised", "Raised By", "Issused To" , "Fix By Date" , "Status" , "Image 1", "Image 2" , "Drawing" , "Sign Off");
								$w = $this->header_width();
								$this->SetWidths($w);
								$best_height = 17;
								$this->row($header, $best_height);
							}
						}
						
						function Footer(){
							$this->SetY(-15);
							$this->SetFont('times','B',10);
							$this->Cell(0, 10, "DefectID - Copyright Wiseworking 2016", 0, 0, 'C');
						}
						
						function header_width(){
							return array(12, 21, 35, 18, 14, 14, 16, 15, 13, 39, 39, 20);
						}
					}
				
				//Send Mail Start Here
				$mail = new PHPMailer(true);
				$mail->IsSMTP(); // telling the class to use SMTP
				$mail->SMTPSecure = 'tls'; //"ssl";	// sets the prefix to the servier
				$mail->Host = smtpHost; 	//pod51022.outlook.com      // sets GMAIL as the SMTP server
				$mail->SMTPDebug = 0;				// enables SMTP debug information (for testing)
				$mail->SMTPAuth = true;        		// enable SMTP authentication
				$smtpPort = smtpPort;
				if(!empty($smtpPort)){
					$mail->Port =  smtpPort; //587;
				}
				$mail->Username = smtpUsername; //"wiseworkingsales@gmail.com"; // SMTP account username
				$mail->Password = smtpPassword; //"Wiseworking123";   // SMTP account password
				$mail->IsHTML(true);	
								
				//$mail = new PHPMailer();
				//$mail->IsSendmail(); // telling the class to use SMTP
				$mail->AddReplyTo("noreply@defectid.com", "No Replay");
				$mail->SetFrom("noreply@defectid.com", "DefectID");
				//$mail->IsHTML(true);
				$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
				$mail->Subject = "ITPs summary report ";
				
				//ProjectId data
				//print_r($issedToArr); die;
				foreach($issedToArr as $projId => $issuedToData){
					if(!empty($projNameArr[$projId]['projManagerEmail'])){
						$mail->AddCC($projNameArr[$projId]['projManagerEmail'], $projNameArr[$projId]['projManager']);
						
						//$mail->AddCC("kumar.haresh@fxbytes.com", $projNameArr[$projId]['projManager']);
						
						$ccAddress[$projId] = array('projManagerEmail'=>$projNameArr[$projId]['projManagerEmail'], 'projManager' => $projNameArr[$projId]['projManager']);
					}
					if(!empty($projNameArr[$projId]['contPersonEmail'])){
						//$mail->AddCC($projNameArr[$projId]['contPersonEmail'], $projNameArr[$projId]['contPerson']);
						$ccAddress[$projId] = array('contPersonEmail'=>$projNameArr[$projId]['contPersonEmail'], 'contPerson' => $projNameArr[$projId]['contPerson']);
					}
					//print_r($issuedToData); die; 
					//IssuedTo data
					foreach($issuedToData as $issName => $issuedTo){
						$msg = "Hello,<br/> <br /> You have new Inspection(s) added in Project: ".$projNameArr[$projId]['projName'].".<br /> <br />";
						//$toAddress[] = array('email'=>$issuedTo['email'], 'name' => $issuedTo['name']);
						//$toAddress[] = array('email'=>'kumar.haresh@fxbytes.com', 'name' => $issuedTo['name']);
						//echo $issuedTo['email']; die;
						
						$mail->AddAddress($issuedTo['email'], $issuedTo['name']);
						
						//$mail->AddAddress('gupta.kratika@fxbytes.com', $issuedTo['name']); // To
						//echo '<br>'.$issuedTo['name']."__".$issuedTo['email'].'<br>';
						//print_r($inspIssuedToArr[$projId]);
						
						# Start:- PDF attachment section
						$noInspection = (isset($inspIssuedToArr[$projId][$issuedTo['name']])?sizeof($inspIssuedToArr[$projId][$issuedTo['name']]):0);
						//pdf generation code here
						//Top Header Sectiond Start Here
						$pdf = new PDF("L", "mm", "A4");
						$pdf->AliasNbPages();
						$pdf->AddPage();
						
						$pdf->SetTopMargin(20);
						
						$pdf->Image(dirname(__FILE__).'/../company_logo/logo.png', 135, 5, -100);
						$pdf->Ln(8);
						
						$pdf->SetFont('times', 'BU', 12);
						$pdf->Cell(40, 10, 'ITPs Report for subcontractor');		
						$pdf->Ln(6);
						
						$pdf->SetFont('times', 'B', 10);
						$pdf->Cell(26, 10, 'Project Name : ');	
						
						$pdf->SetFont('times', '', 10);
						$pdf->Cell(10, 10, $projNameArr[$projId]['projName']);	
						$pdf->Ln(5);
						
						$pdf->SetFont('times', 'B', 10);
						$pdf->Cell(26, 10, 'Sub Contractor : ');	
						
						$pdf->SetFont('times', '', 10);
						$pdf->Cell(10, 10, $issuedTo['name']);	
						$pdf->Ln(5);
						
						$pdf->SetFont('times', 'B', 10);
						$pdf->Cell(11, 10, 'Date : ');	
						
						$pdf->SetFont('times', '', 10);
						$pdf->Cell(20, 10, date('d/m/Y'));	
						$pdf->Ln(5);
						
						$pdf->SetFont('times', 'B', 10);
						$pdf->Cell(23, 10, 'Inspections : ');	
						
						$pdf->SetFont('times', '', 10);
						$pdf->Cell(20, 10, $noInspection);	
						$pdf->Ln(5);	
						
						$pdf->SetFont('times', 'B', 10);
						$pdf->Cell(11, 10, 'Page : ');		
						
						$pdf->SetFont('times', '', 10);
						$pdf->Cell(8, 10, '1 of '.'{nb}');		
						$pdf->Ln(10);
						//Top Header Sectiond End Here
						
						//Data Section Start Here
						$pdf->SetFont('times', 'B', 10);
						$header = array("ID", "Location", "Description", "Inspected By", "Date Raised", "Raised By", "Issused To", "Fix By Date", "Status", "Image 1", "Drawing", "Sign Off");
						$w = $pdf->header_width();
						$pdf->SetWidths($w);		
						$best_height = 17;
						$pdf->row($header, $best_height);
						
						$report_type = 'pdfSummayWithImages';
						$images_path = "photo_summary/";
						$dimages_path = "signoff_summary/";
						$pdf->SetFont('times', '', 9);	
						
						// Project Inspection data
						if(isset($inspIssuedToArr[$projId][$issuedTo['name']])){
							foreach($inspIssuedToArr[$projId][$issuedTo['name']] as $inspArr){
								
								$inspID = $inspArr['InspectionId'];
								$non_conformance_id = $inspArr['non_conformance_id'];

								$msg .= "Location: ".$inspArr['location_tree']."<br />
									Raised Date: ".date('d/m/Y', strtotime($inspArr['DateRaised']))."<br />
									Description: ".$inspArr['Description']."<br />
									Status: ".$inspArr['Status']."<br /><br />
									Inspected By: ".$userNameArr[$inspArr['InspectedBy']]."<br /><br />";
									
									$image0 = '';
									$image1 = '';
									$drawing_image='';
									$signoff_image='';
                                   
									if(isset($imagesArr[$non_conformance_id][0]) && $imagesArr[$non_conformance_id][0]['graphic_name']!= ''){
										$obj->resizeImages(dirname(__FILE__).'/../inspections/photo/'.$imagesArr[$non_conformance_id][0]['graphic_name'], 153, 100, dirname(__FILE__).'/../inspections/photo/photo_summary/'.$imagesArr[$non_conformance_id][0]['graphic_name']);
										if(file_exists(dirname(__FILE__).'/../inspections/photo/'.$images_path.$imagesArr[$non_conformance_id][0]['graphic_name'])){
											$image0 = "IMAGE##25##18##".dirname(__FILE__).'/../inspections/photo/'.$images_path.$imagesArr[$non_conformance_id][0]['graphic_name'];
										}else{
											if (file_exists(dirname(__FILE__).'/../inspections/photo/'.$imagesArr[$non_conformance_id][0]['graphic_name']))
												$image0 = "IMAGE##25##18##".dirname(__FILE__).'/../inspections/photo/'.$imagesArr[$non_conformance_id][0]['graphic_name'];
										}
									}
									
									/*if(isset($imagesArr[$inspID][1]) && $imagesArr[$inspID][1]['graphic_name'] != ''){
										$obj->resizeImages(dirname(__FILE__).'/../inspections/photo/'.$imagesArr[$inspID][1]['graphic_name'], 153, 100, dirname(__FILE__).'/../inspections/photo/photo_summary/'.$imagesArr[$inspID][1]['graphic_name']);
										if(file_exists(dirname(__FILE__).'/../inspections/photo/'.$images_path.$imagesArr[$inspID][1]['graphic_name'])){
											$image1 ="IMAGE##25##22##".dirname(__FILE__).'/../inspections/photo/'.$images_path.$imagesArr[$inspID][1]['graphic_name'];
										}else{
											if (file_exists(dirname(__FILE__).'/../inspections/photo/'.$imagesArr[$inspID][1]['graphic_name']))
												$image1 ="IMAGE##25##22##".dirname(__FILE__).'/../inspections/photo/'.$imagesArr[$inspID][1]['graphic_name'];
										}
									}*/

									if(isset($drawingsArr[$non_conformance_id][0]) && $drawingsArr[$non_conformance_id][0]['graphic_name'] != ''){
										$obj->resizeImages(dirname(__FILE__).'/../inspections/drawing/'.$drawingsArr[$non_conformance_id][0]['graphic_name'], 153, 100, dirname(__FILE__).'/../inspections/drawing/drawing_summary/'.$drawingsArr[$non_conformance_id][0]['graphic_name']);
										if(file_exists(dirname(__FILE__).'/../inspections/drawing/drawing_summary/'.$drawingsArr[$non_conformance_id][0]['graphic_name'])){
											$drawing_image ="IMAGE##25##18##".dirname(__FILE__).'/../inspections/drawing/drawing_summary/'.$drawingsArr[$non_conformance_id][0]['graphic_name'];
										}else{
											if (file_exists(dirname(__FILE__).'/../inspections/drawing/'.$drawingsArr[$non_conformance_id][0]['graphic_name']))
												$drawing_image ="IMAGE##25##18##".dirname(__FILE__).'/../inspections/drawing/'.$drawingsArr[$non_conformance_id][0]['graphic_name'];
										}
									}
								     
								   
									if(isset($inspArr['sc_sign']) && $inspArr['sc_sign'] != ''){
										$obj->resizeImages(dirname(__FILE__).'/../inspections/ncr_files/'.$inspArr['sc_sign'], 153, 100, dirname(__FILE__).'/../inspections/ncr_files/signoff_summary/'.$inspArr['sc_sign']);
										if(file_exists(dirname(__FILE__).'/../inspections/ncr_files/signoff_summary/'.$inspArr['sc_sign'])){
											$signoff_image = "IMAGE##18##18##".dirname(__FILE__).'/../inspections/ncr_files/signoff_summary/'.$inspArr['sc_sign'];
										}else{
											if (file_exists(dirname(__FILE__).'/../inspections/ncr_files/'.$inspArr['sc_sign']))
												$signoff_image ="IMAGE##18##18##".dirname(__FILE__).'/../inspections/ncr_files/'.$inspArr['sc_sign'];
										}
									}									
									
									$ploatArr = array(
										$inspArr['InspectionId'],
										$inspArr['location_tree'],
										//$checkList[$taskList[$inspArr['task_id']]]['location'],
										$inspArr['Description'],
										$userNameArr[$inspArr['InspectedBy']],
										stripslashes(date('d/m/Y', strtotime($inspArr['DateRaised']))),
										$inspArr['RaisedBy'],
										$issName,
										$inspArr['FixedByDate'],
										$inspArr['Status'],
										$image0,
										$drawing_image,
										$signoff_image
									);
									
									$pdf->Row($ploatArr);
								//Data Section End Here
							}
						}

						//PDF Creattion Section Start HereITPs
						$inDatale_name = 'Summary_Report_itps'.microtime().'.pdf';
						#$d = dirname(__FILE__).'/../report_pdf';
						$d = '../report_pdf';
						if(!is_dir($d))
							mkdir($d);
						
						if(!is_dir($d."/cron_pdf"))
							mkdir($d."/cron_pdf");
							
						if (file_exists($d."/cron_pdf/".$inDatale_name))
							unlink($d."/cron_pdf/".$inDatale_name);
						$tempFile = $d."/cron_pdf/".$inDatale_name;
						echo '<h3>'.$tempFile.'</h3>';					
						$pdf->Output($tempFile);
						
			
						//echo $pdf->Output();
						//die;
						$inDataeSize = filesize($tempFile);
						$inDataeSize = floor($inDataeSize/(1024));
						# End:- PDF attachment section
						echo $msg; 
						# End:- PDF attachment section	
						$mail->AddAttachment($tempFile);
						$mail->MsgHTML($msg);	
						if(!$mail->Send()){
							$cronStatus = 'Failed';
							$remark = 'Email address not found for this issued to name: '. $issuedTo['name'];
							echo $mail->ErrorInfo;
						} else {
							$cronStatus = 'Success';
							$remark = '';
							echo $issedToArr[$issName][0][2]." Email Sent!";
						}
						$mail->ClearAddresses();	
						$mail->ClearAttachments();	
					}
					$mail->ClearAllRecipients();
					$mail->clearAttachments();
				}
			} else {
				$cronStatus = 'Success';
				$remark = 'Issued to data not found';
			}
			
			#Insert cron data into history table.
			 $insertQRYCron = "INSERT INTO cron_history SET cron_name = 'daily_open_inspections_proj', update_table_name = '', update_table_value = '".addslashes(serialize($emailContentArr))."', to_address = '".addslashes(serialize($toAddress))."', cc_address = '".addslashes(serialize($ccAddress))."', frequency_of_cron = 'EVERY EVENING 6 AES', created_by = 0, created_date = NOW(), last_modified_by = 0, last_modified_date = NOW(), cron_status='". $cronStatus ."', remark='". $remark ."'";
			mysql_query($insertQRYCron) or die('<br>Error: '.mysql_error());
		}else{
			echo 'No Data Found'; 
		}
		unlink(STOREFOLDER.'/open_insp_proj.txt');	
	}else{
		file_put_contents('open_insp_proj_log.txt', date('Y-m-d h:i:s').'<===>Another one progess is going on please try again letter !<===>', FILE_APPEND);
	}
}else{
	file_put_contents('open_insp_proj_log.txt', date('Y-m-d h:i:s').'<===>Not a week day !<===>', FILE_APPEND);
}
