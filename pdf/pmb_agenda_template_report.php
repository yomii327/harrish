<?php
//ini_set('display_errors', '1');
ob_start();
session_start();
set_time_limit(6000000000000000000);

include('../includes/commanfunction.php');
$object = new COMMAN_Class();

if (isset($_REQUEST["name"])){

	$meetingId = $_REQUEST['meetingId'];
	
	$meetingData = $object->selQRYMultiple('project_id, location, meeting_number, meeting_location, title, description, held_at, date, time, type, created_date, created_by', 'meeting_details', 'is_deleted = 0 AND meeting_id = "'.$meetingId.'"'); 
	//print_r($meetingData);
	if(isset($meetingData) && !empty($meetingData)){
		$_SESSION['idp'] = $meetingData[0]['project_id'];
		setcookie('pmb_'.$_SESSION['ww_builder_id'], $meetingData[0]['project_id'], time()+864000);
		$meetingAttendeeData = $object->selQRYMultiple('attendee_id, attendees_name, company_name', 'meeting_attendees', 'is_deleted = 0 AND meeting_id = "'.$meetingId.'"'); 	
		$meetingItemData = $object->selQRYMultiple('item_id, item, details, by_whom, by_when, comments, is_highlighted', 'meeting_item_details', 'is_deleted = 0 AND meeting_id = "'.$meetingId.'"'); 	
		$userData = $object->selQRYMultiple('user_fullname', 'user', 'is_deleted = 0 AND user_id = "'.$meetingData[0]['created_by'].'"'); 	
	}
	
	$q = "SELECT project_id, project_name FROM user_projects WHERE user_id = '".$_SESSION['ww_builder_id']."' and project_id = '".$_SESSION['idp']."' and is_deleted = 0 GROUP BY project_name";
	$res = mysql_query($q);
	$prIDArr = array();
	$outPutStr = "";
	while($q1 = mysql_fetch_array($res)){
		$prIDArr[$q1[0]] = $q1[1];
	}
	//New Added code for project dropdown End here
	$projectId = isset($_SESSION['idp'])?$_SESSION['idp']:0;	
	
	$noInspection = 0;

	if(is_array($meetingData)){
		$noInspection = sizeof($meetingData);
	}
	if($noInspection > 0){
		$totalPage = 01;//Attachment images Count +1;
		require('../fpdf/mc_table.php');	
		class PDF extends PDF_MC_Table{
		/*	function Header(){
				if($this->PageNo()!=1){
					$this->Cell(0, 10, $this->PageNo()." of ".' {nb}', 0, 0, 'L');		  
					$this->ln();	
					$this->SetFont('times', 'B', 11);
					$header = array("Document Title", "Document Type", "Date Added", "Status", "Revision No", "Revision Date", "Attribute 1", "Attribute 2");
					$w = $this->header_width();
					$this->SetWidths($w);
					$best_height = 17;
					$this->row($header, $best_height);
				}
			}*/
			
			function Footer(){
				$this->SetY(-15);
				$this->SetFillColor(153, 153, 153);
				$this->Cell(190, 1, '', 0, '', '', true);	
				$this->ln(2);
				
				$this->SetTextColor(170, 170, 170);
				$this->SetFont('times','',10);
				$this->Cell(80, 10, 'Wiseworker', 0, 0, '');
				$this->Cell(30, 10, '', 0, 0, 'C');
				$this->Cell(35, 10, 'jwilliams@wiseworking.com.au ', 0, 0, 'R');
				$this->Cell(40, 10, 'www.wiseworking.com.au', 0, 0, 'R');
				$this->SetTextColor(0, 0, 0);
			}
			
			function header_width(){
				return array(28, 53, 18, 18, 13, 15, 18, 18, 18);
			}
			
			function Row($data, $rowHeight = 5){
				//Calculate the height of the row
				//new code to fix 
				// 1. Image overflow
				// 2. page break
				// 3. and height of the row  
				$nb=0;
				$image_height=0;
				for($i=0;$i<count($data);$i++){
					if (strpos($data[$i], "IMAGE##") > -1){
						$ar = explode("##", $data[$i]);
						$image_height=$ar[2]+7; // we have added 2 for a margin
					}else{
						if (strpos($data[$i], "STATUS##") > -1){
							$tmp = explode("~~", $data[$i]);
							$value = $tmp[1];
							$tmp = explode("##", $tmp[0]);
							$status = $tmp[1];
							$nb=max($nb,$this->NbLines($this->widths[$i],$value));
						}else{
							$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
						}
					}
				}
				$h = $rowHeight * $nb;
				$h=max($h,$image_height);
				//end of new code
				//Issue a page break first if needed
				$this->CheckPageBreak($h);
				//Draw the cells of the row
				for($i=0;$i<count($data);$i++){
					$w=$this->widths[$i];
					$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
					//Save the current position
					$x = $this->GetX();
					$y = $this->GetY();
					//Draw the border
					//Print the text
					if (strpos($data[$i], "IMAGE##") > -1){
						$tmp = explode("##", $data[$i]);
						$data[$i] = $tmp[3];
						$this->Rect($x,$y,$w,$h);
						$this->MultiCell($w, 5, $this->Image($data[$i], $this->GetX()+1, $this->GetY()+1, $tmp[1], $tmp[2]),0,'C');
					}else if (strpos($data[$i], "STATUS##") > -1){
						$tmp = explode("~~", $data[$i]);
						$value = $tmp[1];
						$tmp = explode("##", $tmp[0]);
						$status = $tmp[1];
						if (strpos($data[$i], "H##") > -1)
							$status = $tmp[2];
						if($status == 'In progress'){
							$this->SetFillColor(255, 165, 00);	
						}
						else if($status == 'Behind'){
							$this->SetFillColor(255, 00, 00);
						}
						else if($status == 'Complete'){
							$this->SetFillColor(00, 128, 00);
						}
						else if($status == 'Signed off'){
							$this->SetFillColor(00, 00, 255);	
						}else{
							if (strpos($data[$i], "H##") > -1)
								$this->SetFillColor(200, 200, 200);
							else
								$this->SetFillColor(255, 255, 255);
						}
						$this->Rect($x,$y,$w,$h,"F");
						$this->MultiCell($w,$rowHeight,$value,1,'C', true);
					}
					else{
						if (strpos($data[$i], "H##") > -1 )
							{
							$tmp = explode("##", $data[$i]);
							$this->SetFillColor(200, 200, 200);
							$this->Rect($x,$y,$w,$h,"F");
							$this->MultiCell($w,$rowHeight,$tmp[1],0,$a, true);
							}
							else{
							$this->Rect($x,$y,$w,$h);
							$this->MultiCell($w,$rowHeight,$data[$i],0,$a);
							}
					}
					//$this->MultiCell($w,5,$data[$i],0,$a);	
					//Put the position to the right of the cell
					$this->SetXY($x+$w, $y);
				}
				//Go to the next line
				$this->Ln($h);
			}			
		}
		$pdf = new PDF();
		$pdf->AliasNbPages();
		$pdf->AddPage();
		
		$pdf->SetTopMargin(20);
	
		$pdf->Image('../company_logo/logo.png', 115, 5, 'png', -100);
		$pdf->Ln(14);
		
		$pdf->SetFillColor(0, 0, 0);
		$pdf->SetTextColor(252, 252, 252);
		$pdf->SetFont('Helvetica', 'B', 18);
		$pdf->Cell(190, 10, ' AGENDA', '', '', '', true);		
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Ln(14);
				
		$pdf->SetTextColor(153, 153, 153);
		$pdf->SetFont('Helvetica', 'B', 16);
		//$pdf->Cell(50, 10, 'Project Name: ');		
		//$pdf->SetFont('Helvetica', '', 11);
		$pdf->Cell(100, 10, $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name'));		
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Ln(6);
				
		$pdf->SetTextColor(153, 153, 153);
		$pdf->SetFont('Helvetica', 'B', 16);
		//$pdf->Cell(50, 10, 'Project Location: ');		
		//$pdf->SetFont('Helvetica', '', 11);
		$pdf->Cell(100, 10, $meetingData[0]['location']);		
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Ln(10);
				
		/*$pdf->SetFont('Helvetica', 'B', 11);
		$pdf->Cell(50, 10, 'Agenda for meeting number: ');		
		
		$pdf->SetFont('Helvetica', '', 11);
		$pdf->Cell(100, 10, $meetingData[0]['meeting_number']);		
		$pdf->Ln(8);
		
		$pdf->SetFont('Helvetica', 'B', 11);
		$pdf->Cell(50, 10, 'Meeting Description: ');		
		*/
		$pdf->SetFont('Helvetica', 'B', 11);
		$pdf->Cell(100, 10, $meetingData[0]['title']);		
		$pdf->Ln(5);
		
		$pdf->SetFont('Helvetica', '', 11);
		//$pdf->Cell(50, 10, 'Held at: ');		
		
		//$pdf->SetFont('Helvetica', '', 11);
		$pdf->Cell(100, 10, $meetingData[0]['held_at']);		
		$pdf->Ln(5);
		$pdf->Cell(100, 10, $meetingData[0]['meeting_location']);		
		$pdf->Ln(8);
				
		$pdf->SetFont('Helvetica', '', 11);
		//$pdf->Cell(50, 10, 'Date: ');		
		//$pdf->SetFont('Helvetica', '', 11);
		$d = strtotime(str_replace("00:00:00", $meetingData[0]['time'].":00", $meetingData[0]['date']));
		$pdf->Cell(30, 10, date('l d F Y', $d)." at ".date('h.ia', $d));		
		
		//$pdf->SetFont('Helvetica', 'B', 11);
		//$pdf->Cell(15, 10, 'Time: ');		
		
		//$pdf->SetFont('Helvetica', '', 11);
		//$pdf->Cell(40, 10, $meetingData[0]['time']);				
		$pdf->Ln(10);		
		
		$pdf->SetFont('Helvetica', 'B', 12);
		$pdf->Cell(100, 10, "ATTENDEES:");		
		$pdf->Ln(5);						
		
		/*$pdf->SetFont('Helvetica', 'B', 11);
		$pdf->Cell(50, 10, 'Name ');	
		$pdf->Cell(100, 10, 'Company ');			
		$pdf->Ln(6);		
		*/		
		$pdf->SetFont('Helvetica', '', 11);
		if(isset($meetingAttendeeData) && !empty($meetingAttendeeData)){
			foreach($meetingAttendeeData as $key=>$meetAttendee){		
				$pdf->Cell(50, 10, $meetAttendee['attendees_name']);				
				$pdf->Cell(100, 10, $meetAttendee['company_name']);				
				$pdf->Ln(5);	
			}
		}
		
		$pdf->Ln(8);
		$pdf->SetFillColor(153, 153, 153);
		$pdf->SetTextColor(252, 252, 252);
		
		$pdf->SetFont('Helvetica', 'B', 11);
		$pdf->Cell(20, 10, 'ITEM', 1, '', '', true);
		$pdf->Cell(60, 10, 'DETAILS', 1, '', '', true);
		$pdf->Cell(25, 10, 'BY WHOM', 1, '', '', true);	
		$pdf->Cell(25, 10, 'BY WHEN', 1, '', '', true);			
		$pdf->Cell(60, 10, 'COMMENTS', 1, '', '', true);	
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Ln(10);
		
		$header = array("ITEM ", "DETAILS", "BY WHOM", "BY WHEN", "COMMENTS");

		$pdf->SetWidths(array(20, 60, 25, 25, 60));		
		$best_height = 10;
		
		$pdf->SetFont('Helvetica', '', 11);
		if(isset($meetingItemData) && !empty($meetingItemData)){
			foreach($meetingItemData as $key=>$meetingItem){
				
				if($meetingItem['is_highlighted']==1){
					$pdf->SetFont('Helvetica', 'B', 11);
				}else{
					$pdf->SetFont('Helvetica', '', 11);					
				}
						
				$pdf->Row(array($meetingItem['item'], $meetingItem['details'], $meetingItem['by_whom'], ((isset($meetingItem['by_when']) && $meetingItem['by_when']!="0000-00-00")?$object->dateChanger("-", "/", $meetingItem['by_when']):''), $meetingItem['comments']), 8);
			}
		}
		$pdf->Ln(8);
		$pdf->Cell(strlen($userData[0]['user_fullname'])+20, 10, $userData[0]['user_fullname']);
		$pdf->Cell(50, 10, $object->dateChanger("-", ".", substr($meetingData[0]['created_date'], 0, 10)));

//Title String Header
		$file_name = 'agenda_report_'.microtime().'.pdf';
		$d = '../report_pdf/'.$owner_id;
		if(!is_dir($d))
			mkdir($d);
		
		if (file_exists($d.'/'.$file_name))
			unlink($d.'/'.$file_name);
		
		$tempFile = $d.'/'.$file_name;
		
		$pdf->Output($tempFile);
		
		$fieSize = filesize($tempFile);
		
		$fieSize = floor($fieSize/(1024));
		if ($fieSize > 1024){
			$fieSize = floor($fieSize/(1024)) . "Mbs";
		}else{
			$fieSize .= "Kbs";
		}
		$rply = 'Report Generated '.$fieSize;
		echo '<br clear="all" /><div style="margin-left:10px;">'.$rply.' <a onClick="clearDiv();" href="report_pdf/'.$owner_id.'/'.$file_name.'" target="_blank" class="view_btn"></a></div>';
	}else{
		echo '<br clear="all" /><div style="margin-left:10px;">No Record Found</div>';
	}
}?>