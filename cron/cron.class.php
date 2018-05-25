<?php
class PDF extends PDF_MC_Table {
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
		return array(12, 21, 35, 20, 18, 14, 14, 16, 15, 13, 29, 29, 29, 20);
	}
}

/*=====================================================================*/
class Cron extends COMMAN_Class {
	private $output;
	private $day;
	protected $weekDays = array(0, 1, 2, 3, 4, 5, 6, 7);
	
	public function __construct(){
		$this->day = date('N'); //Numeric representation of the day of the week(1=Mon to 7=Sun).
	}
	
	public function groupify($data=''){
		$this->output = array();
		if(isset($data) && !empty($data)){
			foreach ($data as $item) {
				if (!isset($this->output[$item['projectId']])) {
					$this->output[$item['projectId']] = array();
				}
				$this->output[$item['projectId']][] = $item;
			}	
		}	
		return $this->output;
	}
	
	public function dailyFixedInspection($checkEmail=''){
		if(in_array($this->day, $this->weekDays)){
			if(!file_exists(STOREFOLDER.'/fixed_insp_proj.txt')){
				// GET current date
				$curDateData = $this->getRecordByQuery('SELECT NOW() AS date');
				$curDate = substr($curDateData[0]['date'], 0, 10);//Current time from server
				
				$inspData = array();
				$inspData = $this->selQRYMultiple("i.project_id as projectId,up.user_id, i.location_id as Location, i.inspection_date_raised as DateRaised, i.inspection_inspected_by as InspectedBy, i.inspection_type as InspectonType, iss.inspection_status as Status, iss.issued_to_name as IssueToName, iss.cost_attribute as CostAttribute, iss.inspection_fixed_by_date as FixedByDate, i.inspection_description as Description, i.inspection_notes as Note, i.inspection_id as InspectionId, iss.inspection_id as InspectionId_FOR, i.inspection_raised_by as RaisedBy,up.project_manager_email as pmemail, up.project_name as projectName,issued_to_inspections_id,up.contact_person_email as cpemail",
					"project_inspections as i, issued_to_for_inspections as iss, user_projects as up ",
					"i.is_deleted = 0 
						AND i.inspection_id = iss.inspection_id 
						AND i.project_id = up.project_id 
						AND iss.is_deleted = 0 
						AND iss.inspection_status = 'Fixed' 
						AND iss.send_email_status = 0 
						AND iss.issued_to_name != ''
						AND up.project_manager_email != ''
						AND up.is_deleted = 0 
					GROUP BY iss.issued_to_inspections_id" , "No");
					
				//AND i.project_id = '268' AND iss.created_date LIKE '%2016-12-15%'
				if(!empty($inspData)){
					#Set cron status/remark.
					$cronStatus = 'Start';
					$remark = '';
					$toAddress = $emailContentArr = $ccAddress = array();
				
					$res = $this->groupify($inspData);
					if(!empty($res)){
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
						$mail->AddReplyTo("WiseworkingSystems@wiseworking.com.au", "Wiseworking DefectID");
						$mail->SetFrom("WiseworkingSystems@wiseworking.com.au", "Wiseworking DefectID");

						//$mail->IsHTML(true);
						$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
						$mail->Subject = "Inspections fixed";
							
						foreach($res as $key=>$vals){
							$msg = "<br>Hello, "; 
							$msg.= "<p>Inspections have been marked as fixed and are ready for your review to close or reopen.";
							$issued_to_inspections = array();
							$ccMail = $tomail = '';
							foreach($vals as $val){
								$locations = $this->subLocations($val["Location"], ' > ');
								$msg.= "</p><p><b>Project : </b>".$val['projectName'];					
								$msg.= "</p><p><b>Location : </b>".stripslashes($locations);
								$msg.= "</p><p><b>Issued To : </b>".$val['IssueToName'];
								$msg.= "</p><p><b>Description : </b>".substr($val['Description'],0,30);					
								$msg.="</p><p>By clicking on the link below you can access view its details<br>
									<a href='".$path."/pms.php?sect=show_defect_photo&id=".base64_encode($val['InspectionId'])."&byEmail=1' target='_blank'>".$path."/pms.php?sect=show_defect_photo&id=".base64_encode($val['InspectionId'])."</a>";
								$msg .= "<br><hr>"; 
								$issued_to_inspections[] =$val['issued_to_inspections_id'];

								$tomail = $val['pmemail'];
								$ccmail = $val['cpemail'];
							}
							$msg .= "</p><br/>Thanks,<br> DefectId customer care";

							#Check enviorment Test or Live.
							if(isset($checkEmail) && !empty($checkEmail)){
								$tomail = $checkEmail;
							} else {
								if(!empty($ccmail)){
									$mail->AddCC($ccmail,'');
								}
							}
							#echo $tomail . "<<==TO || CC==>>".$ccmail . '</br>';
							
							$toAddress[] = $tomail;
							$mail->AddAddress($tomail, 'Defect ID');
							$mail->MsgHTML($msg);
							
							$emailContentArr[] = $msg;
							
							if(!$mail->Send()){
								$cronStatus = 'Failed';
								$remark = 'Email address not found for this issued to name: '. $val['IssueToName'];
								$this->output = $mail->ErrorInfo .'email not send';
							} else {
								$cronStatus = 'Success';
								$remark = '';
								$this->output = "Email Sent for daily fixed inspections!";
							}
							$mail->ClearAddresses();
							$mail->ClearCCs();
						}
					} else {
						$cronStatus = 'Success';
						$remark = 'No Data Found';
					}
					
					#Insert cron data into history table.
					$insertQRYCron = "INSERT INTO cron_history SET cron_name = 'daily_fixed_inspections_proj', update_table_name = '', update_table_value = '".addslashes(serialize($emailContentArr))."', to_address = '".addslashes(serialize($toAddress))."', cc_address = '".addslashes(serialize($ccAddress))."', frequency_of_cron = 'EVERY EVENING 6 AES', created_by = 0, created_date = NOW(), last_modified_by = 0, last_modified_date = NOW(), cron_status='". $cronStatus ."', remark='". $remark ."'";
					mysql_query($insertQRYCron) or die('<br>Error: '. mysql_error() .'<br>Function: dailyFixedInspection<br>Query :'. $insertQRYCron);
				} else {
					$this->output = "No Data Found";
				}			
				unlink(STOREFOLDER.'/fixed_insp_proj.txt');	
			} else {
				file_put_contents('fixed_insp_proj_log.txt', date('Y-m-d h:i:s').'<===>Another one progess is going on please try again letter !<===>', FILE_APPEND);
			}
		}else{
			file_put_contents('fixed_insp_proj_log.txt', date('Y-m-d h:i:s').'<===>Not a week day !<===>', FILE_APPEND);
		}
		return $this->output;
	}//End of dailyFixedInspection().
	
	public function dailyOpenInspection($checkEmail=''){
		if(in_array($this->day, $this->weekDays)){
			if(!file_exists(STOREFOLDER.'/open_insp_proj.txt')){
				// GET current date
				$curDateData = $this->getRecordByQuery('SELECT NOW() AS date');
				$curDate = substr($curDateData[0]['date'], 0, 10);//Current time from server

				$inspData = array();
				$inspData = $this->selQRYMultiple("i.project_id as projectId, i.location_id as Location, i.inspection_date_raised as DateRaised, i.inspection_inspected_by as InspectedBy, i.inspection_type as InspectonType, iss.inspection_status as Status, iss.issued_to_name as IssueToName, iss.cost_attribute as CostAttribute, iss.inspection_fixed_by_date as FixedByDate, i.inspection_description as Description, i.inspection_notes as Note, i.inspection_id as InspectionId, iss.inspection_id as InspectionId_FOR, i.inspection_raised_by as RaisedBy",
					"project_inspections as i, issued_to_for_inspections as iss",
						"i.is_deleted = 0 
						AND i.inspection_id = iss.inspection_id 
						AND iss.is_deleted = 0 
						AND iss.inspection_status = 'Open' 
						AND iss.issued_to_name != ''
						AND iss.created_date <= '".$curDate."'
					ORDER BY 	
						i.project_id, iss.issued_to_name, i.inspection_location"
					,"No");
				#echo '<pre>';print_r($inspData);die;
				
				if(!empty($inspData)){
					#Set cron status/remark.
					$cronStatus = 'Start';
					$remark = '';
					
					//Assign Empty Array
					$inspIssuedToArr = $inspIdArr = $issedToArr = $issedToNameArr = $projIdArr = $projNameArr  = $locNameArr = array();
					$emailContentArr = array();//Store email 
					$inspLocArr = array();//Store email 
					$inspStatusArr = array();//Store email 
					
					foreach($inspData as $inData){
						$inspIssuedToArr[$inData['projectId']][$inData['IssueToName']][] = $inData;
						$projIdArr[$inData['projectId']] = $inData['projectId'];
						$issedToNameArr[$inData['IssueToName']] = $inData['IssueToName'];
						$inspIdArr[$inData['InspectionId']] = $inData['InspectionId'];
						$emailContentArr[$inData['InspectionId']] = array($inData['InspectionId'], $inData['projectId'], $inData['Location'], $inData['DateRaised'], $inData['Description'], $inData['InspectedBy'], $inData['Note'], $inData['CostAttribute'], $inData['RaisedBy'], $inData['FixedByDate']);
					}
					#echo '<pre>';print_r($emailContentArr);die;
					
					#Start:- Get Inspection Issuedto data(Name/Email).
					$whereIssuedto = '';
					foreach($issedToNameArr as $iRows){
						$dataArr = explode('(', $iRows);
						$name = trim($dataArr[0]);
						$company = trim(str_replace(')', '', $dataArr[1]));
						$whereIssuedto = '(';
						if(isset($name) && !empty($name)){
							$whereIssuedto .= 'issue_to_name = "'. $name .'"';
						}
						if(isset($company) && !empty($company)){
							$whereIssuedto .= ' AND company_name = "'. $company .'"';
						}
						$whereIssuedto .= ')';
						#echo $whereIssuedto.'<br>';
						$issuedTo = $this->selQRYMultiple("project_id, company_name, issue_to_email AS email, CONCAT(issue_to_name, '(', company_name, ')') AS issuedToName", "inspection_issue_to", "is_deleted = 0 AND issue_to_email !='' AND project_id IN (".join(',', $projIdArr).") AND (". $whereIssuedto .")", 'No');
						foreach($issuedTo as $issuedVal){
							if(!empty($issuedVal['company_name'])){
								$issedToArr[$issuedVal['project_id']][$issuedVal['issuedToName']] = array('name'=>$issuedVal['issuedToName'], 'email'=>$issuedVal['email']);
							}
						}
					}
					#echo '<pre>';print_r($issedToArr);die;
					#End:- Get Inspection Issuedto data(Name/Email).
					
					//Get the Project List
					$projects = $this->selQRYMultiple("project_id, project_name, project_manager, project_manager_email, contact_person, contact_person_email", "projects", "is_deleted = 0 AND project_id IN (".join(',', $projIdArr).")", 'N');
					foreach($projects as $projVal){
						$projNameArr[$projVal['project_id']] = array('projName'=> $projVal['project_name'], 'projManager'=> $projVal['project_manager'], 'projManagerEmail'=> $projVal['project_manager_email'], 'contPerson'=> $projVal['contact_person'], 'contPersonEmail'=> $projVal['contact_person_email']); 
					}
					
					//Get Location List
					$locationData = array();
					$locationData = $this->selQRYMultiple("location_id, project_id, location_title, location_name_tree", "project_locations", "is_deleted = 0 AND project_id IN (".join(',', $projIdArr).")", 'N');	
					$locNameArr = array();
					foreach($locationData as $locData){
						$locNameArr[$locData['location_id']] = array($locData['location_title'], $locData['location_name_tree']);
					}
					
					//Get images list
					$imageArr = $this->selQRYMultiple('graphic_id, inspection_id, graphic_name, graphic_type','inspection_graphics','is_deleted = 0 AND inspection_id IN ('.join(",", array_keys($inspIdArr)).') ORDER BY original_modified_date, last_modified_date DESC', 'No');
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
						$mail->Subject = "Inspection summary report ";
						
						//ProjectId data
						foreach($issedToArr as $projId => $issuedToData){
							#Check enviorment Test or Live.
							if($checkEmail==''){
								if(!empty($projNameArr[$projId]['projManagerEmail'])){
									$mail->AddCC($projNameArr[$projId]['projManagerEmail'], $projNameArr[$projId]['projManager']);
									$ccAddress[$projId] = array('projManagerEmail'=>$projNameArr[$projId]['projManagerEmail'], 'projManager' => $projNameArr[$projId]['projManager']);
								}
								if(!empty($projNameArr[$projId]['contPersonEmail'])){
									$mail->AddCC($projNameArr[$projId]['contPersonEmail'], $projNameArr[$projId]['contPerson']);
									$ccAddress[$projId] = array('contPersonEmail'=>$projNameArr[$projId]['contPersonEmail'], 'contPerson' => $projNameArr[$projId]['contPerson']);
								}
							}
							
							//IssuedTo data
							foreach($issuedToData as $issName => $issuedTo){
								if(isset($inspIssuedToArr[$projId][$issuedTo['name']]) && !empty($inspIssuedToArr[$projId][$issuedTo['name']])){
									$noInspection = sizeof(isset($inspIssuedToArr[$projId][$issuedTo['name']])?$inspIssuedToArr[$projId][$issuedTo['name']]:0);
									
									$msg = "Hello,<br/> <br /> You have new Inspection(s) added in Project: ".$projNameArr[$projId]['projName'].".<br /> <br />";
									$toAddress[] = array('email'=>$issuedTo['email'], 'name' => $issuedTo['name']);
									
									#Check enviorment Test or Live.
									if($checkEmail==''){
										$mail->AddAddress($issuedTo['email'], $issuedTo['name']); // To
									} else {
										$mail->AddAddress($checkEmail, "Test Cron"); // To
									}
									
									# Start:- PDF attachment section								
									//pdf generation code here
									//Top Header Sectiond Start Here
									$pdf = new PDF("L", "mm", "A4");
									$pdf->AliasNbPages();
									$pdf->AddPage();
									$pdf->SetTopMargin(20);
									$pdf->Image(dirname(__FILE__).'/../company_logo/logo.png', 135, 5, -100);
									$pdf->Ln(8);
									
									$pdf->SetFont('times', 'BU', 12);
									$pdf->Cell(40, 10, 'Summary Report for subcontractor');		
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
									$header = array("ID", "Location", "Description", "Notes", "Inspected By", "Date Raised", "Raised By", "Issused To", "Fix By Date", "Status", "Image 1", "Image 2", "Drawing", "Sign Off");
									$w = $pdf->header_width();
									$pdf->SetWidths($w);		
									$best_height = 17;
									$pdf->row($header, $best_height);
									
									$report_type = 'pdfSummayWithImages';
									$images_path = "photo_summary/";
									$dimages_path = "signoff_summary/";
									$pdf->SetFont('times', '', 9);	
									
									// Project Inspection data
									foreach($inspIssuedToArr[$projId][$issuedTo['name']] as $inspArr){
										$inspID = $inspArr['InspectionId'];
										$msg .= "Location: ".$locNameArr[$inspArr['Location']][1]."<br />
											Raised Date: ".date('d/m/Y', strtotime($inspArr['DateRaised']))."<br />
											Description: ".$inspArr['Description']."<br />
											Status: ".$inspArr['Status']."<br /><br />
											Inspected By: ".$inspArr['InspectedBy']."<br /><br /><br /><br />";
											
											$image0 = '';
											$image1 = '';
											$drawing_image='';
											$signoff_image='';
											if(isset($imagesArr[$inspID][0]) && $imagesArr[$inspID][0]['graphic_name']!= ''){
												$this->resizeImages(dirname(__FILE__).'/../inspections/photo/'.$imagesArr[$inspID][0]['graphic_name'], 153, 100, dirname(__FILE__).'/../inspections/photo/photo_summary/'.$imagesArr[$inspID][0]['graphic_name']);
												if(file_exists(dirname(__FILE__).'/../inspections/photo/'.$images_path.$imagesArr[$inspID][0]['graphic_name'])){
													$image0 = "IMAGE##25##22##".dirname(__FILE__).'/../inspections/photo/'.$images_path.$imagesArr[$inspID][0]['graphic_name'];
												}else{
													if (file_exists(dirname(__FILE__).'/../inspections/photo/'.$imagesArr[$inspID][0]['graphic_name']))
														$image0 = "IMAGE##25##22##".dirname(__FILE__).'/../inspections/photo/'.$imagesArr[$inspID][0]['graphic_name'];
												}
											}
											
											if(isset($imagesArr[$inspID][1]) && $imagesArr[$inspID][1]['graphic_name'] != ''){
												$this->resizeImages(dirname(__FILE__).'/../inspections/photo/'.$imagesArr[$inspID][1]['graphic_name'], 153, 100, dirname(__FILE__).'/../inspections/photo/photo_summary/'.$imagesArr[$inspID][1]['graphic_name']);
												if(file_exists(dirname(__FILE__).'/../inspections/photo/'.$images_path.$imagesArr[$inspID][1]['graphic_name'])){
													$image1 ="IMAGE##25##22##".dirname(__FILE__).'/../inspections/photo/'.$images_path.$imagesArr[$inspID][1]['graphic_name'];
												}else{
													if (file_exists(dirname(__FILE__).'/../inspections/photo/'.$imagesArr[$inspID][1]['graphic_name']))
														$image1 ="IMAGE##25##22##".dirname(__FILE__).'/../inspections/photo/'.$imagesArr[$inspID][1]['graphic_name'];
												}
											}

											if(isset($drawingsArr[$inspID][0]) && $drawingsArr[$inspID][0]['graphic_name'] != ''){
												$this->resizeImages(dirname(__FILE__).'/../inspections/drawing/'.$drawingsArr[$inspID][0]['graphic_name'], 153, 100, dirname(__FILE__).'/../inspections/drawing/drawing_summary/'.$drawingsArr[$inspID][0]['graphic_name']);
												if(file_exists(dirname(__FILE__).'/../inspections/drawing/drawing_summary/'.$drawingsArr[$inspID][0]['graphic_name'])){
													$drawing_image ="IMAGE##25##22##".dirname(__FILE__).'/../inspections/drawing/drawing_summary/'.$drawingsArr[$inspID][0]['graphic_name'];
												}else{
													if (file_exists(dirname(__FILE__).'/../inspections/drawing/'.$drawingsArr[$inspID][0]['graphic_name']))
														$drawing_image ="IMAGE##25##22##".dirname(__FILE__).'/../inspections/drawing/'.$drawingsArr[$inspID][0]['graphic_name'];
												}
											}
										
											if(isset($inData['signoff']) && $inData['signoff'] != ''){
												$this->resizeImages(dirname(__FILE__).'/../inspections/signoff/'.$inData['signoff'], 153, 100, dirname(__FILE__).'/../inspections/signoff/signoff_summary/'.$inData['signoff']);
												if(file_exists(dirname(__FILE__).'/../inspections/signoff/signoff_summary/'.$inData['signoff'])){
													$signoff_image = "IMAGE##18##18##".dirname(__FILE__).'/../inspections/signoff/signoff_summary/'.$inData['signoff'];
												}else{
													if (file_exists(dirname(__FILE__).'/../inspections/signoff/'.$inData['signoff']))
														$signoff_image ="IMAGE##18##18##".dirname(__FILE__).'/../inspections/signoff/'.$inData['signoff'];
												}
											}									
											
											$ploatArr = array(
												$inspArr['InspectionId'],
												$locNameArr[$inspArr['Location']][1],
												$inspArr['Description'],
												$inspArr['Note'],
												$inspArr['InspectedBy'],
												stripslashes(date('d/m/Y', strtotime($inspArr['DateRaised']))),
												$inspArr['RaisedBy'],
												$issName,
												$inspArr['FixedByDate'],
												$inspArr['Status'],
												$image0,
												$image1,
												$drawing_image,
												$signoff_image
											);
											
											$pdf->Row($ploatArr);
										//Data Section End Here
									}
									
									//PDF Creattion Section Start Here
									$inDatale_name = 'Summary_Report_with_Notes'.microtime().'.pdf';
									#$d = dirname(__FILE__).'/../report_pdf';
									$d = '../report_pdf';
									if(!is_dir($d))
										mkdir($d);
									
									if(!is_dir($d."/cron_pdf"))
										mkdir($d."/cron_pdf");
										
									if (file_exists($d."/cron_pdf/".$inDatale_name))
										unlink($d."/cron_pdf/".$inDatale_name);
									$tempFile = $d."/cron_pdf/".$inDatale_name;
									
									$pdf->Output($tempFile);
									$inDataeSize = filesize($tempFile);
									$inDataeSize = floor($inDataeSize/(1024));
									# End:- PDF attachment section
									
									$mail->AddAttachment($tempFile);
									$mail->MsgHTML($msg);	
									if(!$mail->Send()){
										$cronStatus = 'Failed';
										$remark = 'Email address not found for this issued to name: '. $issuedTo['name'];
										$this->output = $mail->ErrorInfo;
									} else {
										$cronStatus = 'Success';
										$remark = '';
										$this->output = $issedToArr[$issName][0][2]." Email Sent for dailiy open inspection";
									}
									$mail->ClearAddresses();	
									$mail->ClearAttachments();
								}
							}
							$mail->ClearAllRecipients();
							$mail->clearAttachments();
						}
						
					} else {
						$cronStatus = 'Success';
						$remark = 'Issued to data not found';
					}					
					
					#Insert cron data into history table.
					#Comments 28-07-2017
					#Error: MySQL server has gone away
					/*$insertQRYCron = "INSERT INTO cron_history SET cron_name = 'daily_open_inspections_proj', update_table_name = '', update_table_value = '".addslashes(serialize($emailContentArr))."', to_address = '".addslashes(serialize($toAddress))."', cc_address = '".addslashes(serialize($ccAddress))."', frequency_of_cron = 'EVERY EVENING 6 AES', created_by = 0, created_date = NOW(), last_modified_by = 0, last_modified_date = NOW(), cron_status='". $cronStatus ."', remark='". $remark ."'";
					mysql_query($insertQRYCron) or die('<br>Error: '. mysql_error() .'<br>Function: dailyOpenInspection<br>Query :'. $insertQRYCron);*/
				}else{
					$this->output = 'No Data Found'; 
				}
				unlink(STOREFOLDER.'/open_insp_proj.txt');	
			}else{
				file_put_contents('open_insp_proj_log.txt', date('Y-m-d h:i:s').'<===>Another one progess is going on please try again letter !<===>', FILE_APPEND);
			}
		}else{
			file_put_contents('open_insp_proj_log.txt', date('Y-m-d h:i:s').'<===>Not a week day !<===>', FILE_APPEND);
		}
		return $this->output;
	}//End of dailyOpenInspection().
}
/* Omit PHP closing tags to help avoid accidental output */
