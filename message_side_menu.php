<?php  // Inbox Count------------------//
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();
$projectId = isset($_SESSION['idp']) ? $_SESSION['idp'] : 0;

	
	// Inbox Count------------------//
	// Sent Box Count------------------//
	$sentcountQry = "SELECT um.user_id,
	                        user_message_id,
	                        um.from_id, 
							um.message_id, 
							um.thread_id,
							um.inbox_read, 
							m.title, 
							m.message, 
							m.sent_time
					FROM
						pmb_user_message as um,
						pmb_message m
					WHERE
						um.message_id = m.message_id AND
						um.type = 'sent' AND
						m.is_draft = 0 AND
						um.is_deleted = 0 AND
						um.project_id = '".$projectId."' AND
						um.user_id = '".$_SESSION['ww_builder_id']."'";
	$rsSentcount = mysql_query($sentcountQry);
	$sentcount = mysql_num_rows($rsSentcount);
	// Sent Box Count------------------//
	// trash Box Count------------------//
	$trashcountQry = "SELECT um.user_id,
	                        user_message_id,
	                        um.from_id, 
							um.message_id, 
							um.thread_id,
							um.inbox_read, 
							m.title, 
							m.message, 
							m.sent_time
							 
					FROM
						pmb_user_message as um,
						pmb_message m
					WHERE
						um.message_id = m.message_id AND
						um.is_deleted = 1 AND
						um.type != 'delete' AND
						m.is_draft = 0 AND
						um.project_id = '".$projectId."' AND
						um.user_id = '".$_SESSION['ww_builder_id']."'";
	$rsTrashcount = mysql_query($trashcountQry);
	$trashcount = mysql_num_rows($rsTrashcount);



	$draftcountQry = "SELECT um.user_id,
	                        user_message_id,
	                        um.from_id, 
							um.message_id, 
							um.thread_id,
							um.inbox_read, 
							m.title, 
							m.message, 
							m.sent_time
					FROM
						pmb_user_message as um,
						pmb_message m
					WHERE
						um.message_id = m.message_id AND
						um.is_deleted = 0 AND
						m.is_draft = 1 AND
						um.project_id = '".$projectId."' AND
						um.user_id = '".$_SESSION['ww_builder_id']."'";
	$rsDraftcount = mysql_query($draftcountQry);
	$draftcount =''; // mysql_num_rows($rsDraftcount);
#	$addressBookData = $object->selQRYMultiple('id, full_name, user_email', 'pmb_address_book', 'is_deleted = 0 AND project_ID = '.$_SESSION['idp']);
		 // trash Box Count------------------// ?>
<?php if($_GET['view']=='workflow'){
			$style = "style='width:15%;float:left;background:#999999;'";
		}else{
			$style = "style='background:#999999;'";	
		}
		if($_GET['view']=='workflow'){
			$projectConfig = '&view=workflow';
		}else{
			$projectConfig = '';
		}
		?>
				 
<div class="MailLeft" <?php echo $style;?>>
	<ul>
		<li id="inboxLi"><a href="?sect=messages<?php echo $projectConfig;?>" <?php if($_GET['sect'] == 'messages' || $_GET['type'] == 'inbox'){echo 'class="selected"';}?>>Inbox <?php $inboxcount = unreadCountFolder(""); if($inboxcount>0) {?>(<?php echo $inboxcount; ?>) <?php } ?></a>
			<ul style="margin-left:15px;">
				<?php //$subFolderArr = array('General Correspondance', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting'), 'Contract Adjustment', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims');
					#echo "<pre>";print_r($_SESSION);die;
					if($_SESSION['idp']==242 || $_SESSION['idp']==240  || $_SESSION['idp']==241){
					#if($_SESSION['idp']==220){
					switch($_SESSION['userRole']){
							case 'All Defect':
							$subFolderArr = array('General Correspondence' => array(),'Inspections' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')), 'Contract Admin' => array('Client Side' => array('Variation Claims', 'EOTs', 'NODs', 'Formal Correspondence', 'Meeting minutes', 'Superintendant Instruction', 'Purchaser Changes', 'Inspections'), 'Subcontractors' => array('Variation Claims', 'NODs', 'Formal Correspondence', 'Subcontractor Meetings')));
							$dontShow = array();
							break;
							
							case 'Builder':
							//$subFolderArr = array('General Correspondence' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')), 'Client Side' => array( 'Contract Admin', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs'), 'Recommendation' => array(), 'Estimating' => array('Tenders', 'Construction'), 'Purchaser Changes' => array());
							$subFolderArr = array('General Correspondence' => array(),'Inspections' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')), 'Contract Admin' => array('Client Side' => array('Variation Claims', 'EOTs', 'NODs', 'Formal Correspondence', 'Meeting minutes', 'Superintendant Instruction', 'Purchaser Changes', 'Inspections'), 'Subcontractors' => array('Variation Claims', 'NODs', 'Formal Correspondence', 'Subcontractor Meetings')));
							$dontShow = array();
							break;
							
							
							case 'Architect':
							$subFolderArr = array('General Correspondence' => array(),'Inspections' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')));
							$dontShow = array();
							break;
							
							
							case 'Structural Engineer':
							$subFolderArr = array('General Correspondence' => array(),'Inspections' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')));
							$dontShow = array();
							break;
							
							
							case 'Services Engineer':
							$subFolderArr = array('General Correspondence' => array(),'Inspections' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')));
							$dontShow = array();
							break;
							
							
							case 'Superintendant':
							#$subFolderArr = array('General Correspondence' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')), 'Client Side' => array( 'Contract Admin', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs'), 'Recommendation' => array(), 'Estimating' => array('Tenders', 'Construction'), 'Purchaser Changes' => array());
							$subFolderArr = array('General Correspondence' => array(),'Inspections' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')), 'Contract Admin' => array('Client Side' => array('Variation Claims', 'EOTs', 'NODs', 'Formal Correspondence', 'Meeting minutes', 'Superintendant Instruction', 'Purchaser Changes', 'Inspections'), 'Subcontractors' => array('Variation Claims', 'NODs', 'Formal Correspondence', 'Subcontractor Meetings')));
							$dontShow = array();
							break;
							
							
							case 'General Consultant':
							$subFolderArr = array('General Correspondence' => array(),'Inspections' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')));
							$dontShow = array();
							break;
							
							
							case 'Building Surveyor':
							$subFolderArr = array('General Correspondence' => array(),'Inspections' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')));
							$dontShow = array();
							break;
							
							
							case 'Subcontractor - Tender':
							$subFolderArr = array();//array('General Correspondence' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')), 'Client Side' => array( 'Contract Admin', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs'), 'Recommendation' => array(), 'Estimating' => array('Tenders', 'Construction'), 'Purchaser Changes' => array());
							$dontShow = array("Inspections", "Request For Information", "Meetings");
							break;
							
							case 'Sub Contractor':
							$subFolderArr = array('General Correspondence' => array(),'Inspections' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Design' => array('Design Changes', 'Design Meeting'));
							$dontShow = array("Inspections", "Request For Information", "Meetings");
							break;
							
							
							default:
								$subFolderArr = array('General Correspondence' => array(),'Inspections' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')), 'Contract Admin' => array('Client Side' => array('Variation Claims', 'EOTs', 'NODs', 'Formal Correspondence', 'Meeting minutes', 'Superintendant Instruction', 'Purchaser Changes', 'Inspections'), 'Subcontractors' => array('Variation Claims', 'NODs', 'Formal Correspondence', 'Subcontractor Meetings')), 'Estimating' => array('Tenders', 'Construction'));
							break;
					}
				}else{
					$subFolderArr = array('General Correspondence' => array(),'Inspections' => array(), 'Document Transmittal' => array('Document Register Updates'), 'Memorandum' => array(), 'Request For Information' => array(), 'Site Instruction' => array(), 'Consultant' => array('Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design' => array('Design Changes', 'Design Meeting')), 'Contract Admin' => array('Client Side' => array('Variation Claims', 'EOTs', 'NODs', 'Formal Correspondence', 'Meeting minutes', 'Superintendant Instruction', 'Purchaser Changes', 'Inspections'), 'Subcontractors' => array('Variation Claims', 'NODs', 'Formal Correspondence', 'Subcontractor Meetings')), 'Estimating' => array('Tenders', 'Construction'));
				}
					$idNo = 0;  $parentFolder = '';  
					foreach($subFolderArr as $folderName=>$subFolderArr){ $idNo++; $isFolder = 0;  $parentFolderName = '';
						if(in_array($folderName, $dontShow)){ $style = "display:none;";}else{$style = "";}
						$dynaLi = '<a style="display:inline;"';
						if($_GET['folderType'] == $folderName){
							$dynaLi .= 'class="selected"';
							$isFolder = 1;
						}
						$parentFolderName = $folderName;
						$pageName = ($folderName == 'Request For Information')?'pmb_sub_folder':'messages';
						$dynaLi .= 'href="?sect='.$pageName.'&folderType='.$folderName.$projectConfig.'" title="'.$folderName.'" >'.$folderName;
						$inboxcount = unreadCountFolder($folderName);
						if($inboxcount > 0) {
							$dynaLi .= ' ('.$inboxcount.')';
						}
						$dynaLi .= '</a>';
						$subDynaLi = ''; $subFolder = '';  $idNo2 = 0;
						if(!empty($subFolderArr)){
							foreach($subFolderArr as $key=>$subFolderName){    $isFolder2 = 0;
								if(in_array($subFolderName, $dontShow)){ $style = "display:none;";}else{$style = "";}
								if(is_array($subFolderName)){ $idNo2++; 
									$folderName = $key;
									$childFolderName = $parentFolderName.'_'.$folderName;
									foreach($subFolderName as $key=>$subFolderName2){
										$tempName = $parentFolderName.'_'.$folderName.'_'.$subFolderName2;
										if($_GET['folderType'] == $tempName || $_GET['folderType'] == $childFolderName){
											$isFolder = 1;
											$isFolder2 = 1;
										}
									}
									$subDynaLi .=  '<li style="'.$style.' margin-left: 6px;"><span '.(!empty($folderName)?' class="'.(($isFolder2 == 1)?'minusIcon':'plusIcon').'" id="parentId'.$idNo.$idNo2.'" onclick="showHide('.$idNo.$idNo2.', \'\');" ':'class="subFolder"').' ></span>';
									$subDynaLi .= '<a style="display:inline;"';
									
								}else{
									$folderName = $subFolderName;
									$subDynaLi .= '<li class="subFolderLi" style="'.$style.'"><a ';
								}
								$childFolderName = $parentFolderName.'_'.$folderName;
								if($_GET['folderType'] == $childFolderName){
									$subDynaLi .= 'class="selected"';
									$isFolder = 1;
								}
								
								$subDynaLi .= 'href="?sect=messages&folderType='.$childFolderName.$projectConfig.'" title="'.$folderName.'">'.$folderName;
								$inboxcount = unreadCountFolder($childFolderName);
								if($inboxcount > 0) {
									$subDynaLi .= ' ('.$inboxcount.')';
								}
								$subDynaLi .= '</a>';
							
								
								# Start:- Sub sub folder
								$subDynaLi2 = ''; $subFolder2 = '';
								if(is_array($subFolderName) && !empty($subFolderName)){
									foreach($subFolderName as $key=>$subFolderName2){
										$subChildFolderName = $parentFolderName.'_'.$folderName.'_'.$subFolderName2;
										$subDynaLi2 .= '<li class="subFolderLi" style="'.$style.'"><a ';
										if($_GET['folderType'] == $subChildFolderName){
											$subDynaLi2 .= 'class="selected"';
											$isFolder = 1;
											$isFolder2 = 1;
										}
										$subDynaLi2 .= 'href="?sect=messages&folderType='.$subChildFolderName.$projectConfig.'" title="'.$subFolderName2.'">'.$subFolderName2;
										$inboxcount = unreadCountFolder($subChildFolderName);
										if($inboxcount > 0) {
											$subDynaLi2 .= ' ('.$inboxcount.')';
										}
										$subDynaLi2 .= '</a></li>';
									}
									$subDynaLi2 .= '</ul>';
									
									$subFolder2= '<ul style="margin-left:15px; '.(($isFolder2==1)?'':'display:none;').' background:#999999; padding-left:15px; " id="childBox'.$idNo.$idNo2.'">';
								}
								# End:- Sub sub folder
								$subDynaLi .= $subFolder2.$subDynaLi2;
								if(is_array($subFolderName)){
									
									//$subDynaLi=  '<li style="'.$style.'"><span '.(!empty($folderName)?' class="'.(($isFolder2 == 1)?'minusIcon':'plusIcon').'" id="parentId'.$idNo.$idNo2.'" onclick="showHide('.$idNo.$idNo2.', \'\');" ':'class="subFolder"').' ></span>'.$subDynaLi.$subFolder2.$subDynaLi2;
							}
								$subDynaLi .= '</li>';
								
							}
							$subDynaLi .= '</ul>';
							
							
							$subFolder= '<ul style="margin-left:15px; '.(($isFolder==1)?'':'display:none;').' background:#999999; " id="childBox'.$idNo.'">';
						}
						
						$parentFolder = '<li style="'.$style.'"><span '.(!empty($subFolderArr)?' class="'.(($isFolder == 1)?'minusIcon':'plusIcon').'" id="parentId'.$idNo.'" onclick="showHide('.$idNo.', \'\');" ':'class="subFolder"').' ></span>';

						$dynaLi = $parentFolder.$dynaLi.$subFolder.$subDynaLi; 
						
						$dynaLi .= '</li>';
						echo $dynaLi; 
						if($isFolder == 1){
							echo '<script>showHide('.$idNo.'); </script>';
						}
						$dynaLi ='';
					}
					
					$style= "";
					/*if($_SESSION['idp']==242 || $_SESSION['idp']==240  || $_SESSION['idp']==241){
					#if($_SESSION['idp']==220){
						if(in_array("Inspections", $dontShow)){ $style = "display:none;";}else{$style = "";}
					}
					
					$dynaLi = '<li style="'.$style.'"><span class="subFolder"></span><a style="display:inline;" ';
					if($_GET['folderType'] == 'Inspections'){ $dynaLi .= 'class="selected"'; }
					$dynaLi .= 'href="?sect=inbox_insp&folderType=Inspections'.$projectConfig.'" title="Inspections">Inspections';
					$inboxcount = unreadCountFolder("Inspections");
					if($inboxcount > 0) { $dynaLi .= ' ('.$inboxcount.')'; }
					$dynaLi .= '</a></li>';
					*/
					if(in_array("Request For Information", $dontShow)){ $style1 = "display:none;";}else{$style1 = "";}
					$dynaLi .= '<li style="'.$style1.'"><span class="subFolder"></span><a style="display:inline;" ';
					if($_GET['folderType'] == 'Request For Information'){
						$dynaLi .= 'class="selected"';
					}
					$dynaLi .= 'href="?sect=pmb_sub_folder&folderType=Request For Information'.$projectConfig.'" title="Request For Information">Request For Information';
					$inboxcount = unreadCountFolder("Request For Information");
					if($inboxcount > 0) {
						$dynaLi .= ' ('.$inboxcount.')';
					}
					$dynaLi .= '</a></li>';
					
					
					/*$style2 = "";
					$idNo++; $isFolder = 0; 
					if($_SESSION['idp']==242 || $_SESSION['idp']==240  || $_SESSION['idp']==241){
					#if($_SESSION['idp']==220){
						if(in_array("Meetings", $dontShow)){ $style2 = "display:none;";	}else{	$style2 = "";	}
					}
					
					$parntDynaLi = ''; $subDynaLi = '';
					if($_GET['folderType'] == "Meetings"){
						$parntDynaLi .= 'class="selected"';
						$isFolder = 1;
					}
					$parntDynaLi .= 'href="?sect=meeting&folderType=Meetings'.$projectConfig.'" title="Meetings">Meetings</a>';
					$subDynaLi=  '<li class="subFolderLi"   style="'.$style2.'"><a ';
					if($_GET['folderType'] == "Meeting minutes"){
						$subDynaLi .= 'class="selected"';
						$isFolder = 1;
					}
					$subDynaLi .= 'href="?sect=messages&folderType=Meeting minutes'.$projectConfig.'" title="Meeting minutes">Meeting minutes</a></li></ul></li>';
					$parentFolder = '<li style="'.$style2.'"><span class="'.(($isFolder == 1)?'minusIcon':'plusIcon').'" id="parentId'.$idNo.'" onclick="showHide('.$idNo.', \'\');" ></span><a style="display:inline;" ';
					$subFolder= '<ul style="margin-left:15px; '.(($isFolder==1)?'':'display:none;').' background:#8E8E8E; " id="childBox'.$idNo.'">';
					$dynaLi .= $parentFolder.$parntDynaLi.$subFolder.$subDynaLi; 
					
					//$dynaLi .= '<li id="loc_'.$_SESSION['idp'].'" ></li>';		
										
					echo $dynaLi;	*/				
					
					/*$chapters = $obj->selQRYMultiple('chapter_id, chapter_title','pmb_subchapter', 'is_deleted = 0 AND project_id = '.$_SESSION['idp'].' AND parent_chapter_id = 0');
					/*$chaptersMain = $obj->selQRYMultiple('chapter_id, chapter_title, parent_chapter_id','pmb_subchapter', 'is_deleted = 0 AND project_id = '.$_SESSION['idp'].'');
					$mainArrayChapter = array();
					foreach($chaptersMain as $chaptersMainVal){
					   $mainArrayChapter[$chaptersMainVal['parent_chapter_id']][] = $chaptersMainVal;	
					}*/
				/*	if(!empty($chapters)){
					$dynaLi .= '<ul style="margin-left:15px;">';
						foreach($chapters as $chaptersVal){
							$dynaLi .= '<li class="subFolderLi" id="li_'.$chaptersVal['chapter_id'].'"><span  id="'.$chaptersVal['chapter_id'].'" class="jtree-button demo2"><a ';
							if($_GET['folderType'] == $chaptersVal['chapter_title']){$dynaLi .= 'class="selected"';}
							/*$dynaLi .= 'href="?sect=meeting&folderType='.$chaptersVal['chapter_title'].'" title="Subcontractor">'.$chaptersVal['chapter_title'].'</a></span></li>';	
							$chapters1 = $obj->selQRYMultiple('chapter_id, chapter_title','pmb_subchapter', 'is_deleted = 0 AND project_id = '.$_SESSION['idp'].' AND parent_chapter_id = '.$chaptersVal['chapter_id'].'');
								if(!empty($chapters1)){
								$dynaLi1 .= '<ul style="margin-left:15px;">';
									foreach($chapters1 as $chapters1Val){
										$dynaLi1 .= '<li class="subFolderLi" id="li_'.$chapters1Val['chapter_id'].'"><span  id="'.$chapters1Val['chapter_id'].'" class="jtree-button demo2"><a ';
										if($_GET['folderType'] == $chapters1Val['chapter_title']){$dynaLi .= 'class="selected"';}
										$dynaLi1 .= 'href="?sect=meeting&folderType='.$chapters1Val['chapter_title'].'" title="Subcontractor">'.$chapters1Val['chapter_title'].'</a></span></li>';	
										$chapters = $obj->selQRYMultiple('chapter_id, chapter_title','pmb_subchapter', 'is_deleted = 0 AND project_id = '.$_SESSION['idp'].' AND parent_chapter_id = '.$chapters1Val['chapter_id'].'');
									}
									echo $dynaLi1;
								}*/
					/*	}
						echo $dynaLi;
					}*/
					 ?>
                     
                     <!--li id="loc_222"><span class="minusIcon" id="101" onclick="showHide(101);"><a href="?sect=messages&amp;folderType=Subcontractor" title="Subcontractor" style="display:inline; ">Subcontractor</a></span>
			<ul  class="telefilms" style="margin-left: 15px; background: rgb(142, 142, 142) none repeat scroll 0% 0%;" id="childBox101"><li class="subFolderLi" id="li_51">
			<span id="51" class="jtree-button demo2">
				<a href="?sect=messages&amp;folderType=Test 1" title="Subcontractor">Test 1</a>
			</span><ul style="margin-left:15px;"><li class="subFolderLi" id="li_52">
						<span id="52" class="jtree-button demo2">
							<a href="?sect=messages&amp;folderType=Test 1.1" title="Subcontractor">Test 1.1</a>
						</span></li><li class="subFolderLi" id="li_53">
						<span id="53" class="jtree-button demo2">
							<a href="?sect=messages&amp;folderType=Test 1.2" title="Subcontractor">Test 1.2</a>
						</span></li></ul></li><li class="subFolderLi" id="li_54">
			<span id="54" class="jtree-button demo2">
				<a href="?sect=messages&amp;folderType=Test 2" title="Subcontractor">Test 2</a>
			</span><ul style="margin-left:15px;"><li class="subFolderLi" id="li_55">
						<span id="55" class="jtree-button demo2">
							<a href="?sect=messages&amp;folderType=Test 2.1" title="Subcontractor">Test 2.1</a>
						</span></li><li class="subFolderLi" id="li_56">
						<span id="56" class="jtree-button demo2">
							<a href="?sect=messages&amp;folderType=Test 2.2" title="Subcontractor">Test 2.2</a>
						</span></li></ul></li></ul></li-->
			</ul>
		</li>
		
        <!--<li id="sentLi"><a href="?sect=sent_box" <?php //if($_GET['sect'] == 'sent_box' || $_GET['type'] == 'sent'){echo 'class="selected"';}?>>Sent <?php ///if($sentcount<0) {?>(<?php //echo $sentcount; ?>) <?php //} ?></a></li>-->
		
        <li id="draftLi"><a href="?sect=drafts<?php echo $projectConfig;?>" <?php if($_GET['sect'] == 'drafts' || $_GET['type'] == 'draft'){echo 'class="selected"';}?>>Drafts <?php if($draftcount>0) {?>(<?php echo $draftcount; ?>) <?php } ?></a></li>
		
        <li id="trashLi"><a href="?sect=trash<?php echo $projectConfig;?>" <?php if($_GET['sect'] == 'trash' || $_GET['type'] == 'trash'){echo 'class="selected"';}?>>Trash <?php if($trashcount>0) {?>(<?php echo $trashcount; ?>) <?php } ?></a></li>
		
	</ul>		 
	<ul style="margin-top:10px;max-height:240px;overflow:auto;text-align:center;float: left;margin-left:10px;"> 
		<!--li>
        	<a href="?sect=event_calendar" class="sideMenu">Calendar</a>
		</li-->  
		<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
		<li style="margin-top:15px;">
			<a href="?sect=address_book<?php echo $projectConfig;?>" name="addressBook" id="addressBook" title="Click to go address book" class="green_small sideMenu_msgs">
					Address Book
			</a>
		</li>
		<?php }?>
		<li style="margin-top:15px;">
			<a href="?sect=details_search<?php echo $projectConfig;?>" class="green_small sideMenu_msgs">Detailed Search</a>
		</li>
		<li style="margin-top:15px;">
        	<a href="?sect=b_dashboard<?php echo $projectConfig;?>" class="green_small sideMenu_msgs">My Profile</a>
		</li>
    </ul>
</div>
<!--div class="contextMenu" id="myMenu0">
	<ul>
		<li id="add"><img src="images/add.png" align="absmiddle" width="14"  height="14"/> Add</li>
	</ul>
</div>
<div class="contextMenu" id="myMenu1">
	<ul>
		<li id="add"><img src="images/add.png" align="absmiddle" width="14"  height="14"/> Add</li>
		<li id="edit"><img src="images/edit_right.png" align="absmiddle" width="14"  height="14"/> Edit</li>
		<li id="delete"><img src="images/delete.png" align="absmiddle" width="14"  height="14"/> Delete</li>
	<?php if($isLocation > 0){?>
		<li id="paste"><img src="images/paste.png"  align="absmiddle" width="14" height="15" /> Paste</li>
	<?php }?>
	</ul>
</div>
<div class="contextMenu" id="myMenu3">
	<ul>
		<li id="edit"><img src="images/edit_right.png" align="absmiddle" width="14"  height="14"/> Edit</li>
		<li id="delete"><img src="images/delete.png" align="absmiddle" width="14"  height="14"/> Delete</li>
	<?php if($isLocation > 0){?>
		<li id="paste"><img src="images/paste.png"  align="absmiddle" width="14" height="15" /> Paste</li>
	<?php }?>
	</ul>
</div-->

<input type="hidden" value="<?php echo $_SESSION['userRole'];?>" id="userRole">
<input type="hidden" value="<?php echo $_SESSION['idp'];?>" id="projectId">
<style>fieldset.roundCorner{ color:#000000; }

.plusIcon {
	background: rgba(0, 0, 0, 0) url("images/add.png") no-repeat !important;
	background-position: left 0 top 0px !important;
	padding-left:15px;
	cursor:pointer;	
}
.minusIcon {
	background: rgba(0, 0, 0, 0) url("images/minus-icon.png") no-repeat  !important;
	background-position: left 0 top 0px !important;
	padding-left:15px;
	cursor:pointer;
}
.subFolder {
	background: rgba(0, 0, 0, 0) url("images/folder.png") no-repeat !important;
	background-position: left -2px top 0px !important;
	padding-left:15px;
	cursor:pointer;
}

#inboxLi ul li a { padding-left:20px !important;}
#inboxLi ul .subFolderLi a { padding-left:35px !important;}
#inboxLi ul .subFolderLi a .subFolderLi a { padding-left:35px !important;}
</style>

<!--script type="text/javascript" src="js/pmb.jquery.tree.js"></script-->
<!--script type="text/javascript" src="js/jquery.contextmenu.r2.js"></script-->
<!--<script type="text/javascript" src="js/location_tree_jquery.min.js"></script>-->
<script type="text/javascript">
// Show / hide folder
function showHide(id, extraClass){
	//alert(id);
	if(extraClass != ''){
		var className = $("#"+id).attr('class').trim();
		//alert(className);
		if(className == extraClass+' plusIcon'){
			$("#"+id).attr('class', extraClass+' minusIcon');
			$("#childBox"+id).show();		
			
		}else if(className == extraClass+' minusIcon'){
			$("#"+id).attr('class', extraClass+' plusIcon');
			$("#childBox"+id).hide();
		}
		
	}else{
		var className = $("#parentId"+id).attr('class');
		if(className == 'plusIcon'){
			$("#parentId"+id).attr('class', 'minusIcon');
			$("#childBox"+id).show();		
		}else{
			$("#parentId"+id).attr('class', 'plusIcon');
			$("#childBox"+id).hide();
		}
	}
}
	
/*function loadSubFolder(){
	var projectId = '<?php echo $_SESSION['idp'];?>';
	var folderType = "<?php echo isset($_GET['folderType'])?$_GET['folderType']:''; ?>";
	$.ajax({
	url: "location_tree_show.php",
	type: "POST",
	data: {folderType:folderType},
	success: function (res) {
			$('#loc_'+projectId).html(res);
			
				$('span.demo0').contextMenu('myMenu0', {
						bindings: {
							'add': function(t) {
								modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_pmb_add.php?location_id='+Math.random(), loadingImage);
							},
						}
				});
				$('span.demo2').contextMenu('myMenu1', {
					bindings: {
						'add': function(t) {
							modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_pmb_add.php?location_id='+Math.random()+'&id='+t.id, loadingImage);
						},
						'edit': function(t) { 
							modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_pmb_edit.php?location_id='+t.id, loadingImage);
						},
						'delete': function(t) {
							deleteSubchapter(t.id);
						},
						'paste': function(t) {
							if(copyStatus === true){
							//pasteLocation(copyId, t.id);
							//copyId = '';
							//copyStatus = false;
						}else{
							jAlert('Copy Location for Paste Here !');
						}
					},
				}
			});
				$('span.demo3').contextMenu('myMenu3', {
						bindings: {
							'edit': function(t) { 
								modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_pmb_edit.php?location_id='+t.id, loadingImage);
							},
							'delete': function(t) {
								deleteSubchapter(t.id);
							},
							'paste': function(t) {
								if(copyStatus === true){
								//pasteLocation(copyId, t.id);
								//copyId = '';
								//copyStatus = false;
							}else{
								jAlert('Copy Location for Paste Here !');
							}
						},
					}
				});
				
				searIdArr = searchText('<?php echo 'loc_'.$_SESSION['idp']; ?>', folderType);	

				if(folderType == 'Subcontractor'){ 
					$("#101 a").addClass('selected'); 
					$("#childBox101").show(); 
				}
				
				if(searIdArr['parentId'] != '' && searIdArr['parentId'] != undefined){	
					//alert(searIdArr['parentId']);
					$("#"+searIdArr['parentId']).show();	
					$("#101").attr('class', 'jtree-button demo0 minusIcon');	
					$("#"+searIdArr['parentId']).prev().attr('class', 'jtree-button demo2 minusIcon');	
					$("#childBox101").show();
				}
				if(searIdArr['parentId2'] != '' && searIdArr['parentId2'] != undefined){	
					//alert(searIdArr['parentId2']);
					$("#"+searIdArr['parentId2']).show();	
					$("#101").attr('class', 'jtree-button demo0 minusIcon');	
					$("#"+searIdArr['parentId2']).prev().attr('class', 'jtree-button demo2 minusIcon');	
					$("#childBox101").show();
				}
				if(searIdArr['childId'] != '' && searIdArr['childId'] != undefined){	
					//alert(searIdArr['childId']);
					$("#"+searIdArr['childId']).show();	
					$("#"+searIdArr['childId']).prev().attr('class', 'jtree-button demo2 minusIcon');					
					//alert($("#"+searIdArr['childId']).prev().text());
				}
		}
	});	
}

$(document).ready(function() {
	var flag = 0;
	if($('#projectId').val()=='240'  || $('#projectId').val()=='242'  || $('#projectId').val()=='241'){
	//if($('#projectId').val()=='220'){
		flag = 1;
		//loadSubFolder();
	}
	if(flag==0){
		loadSubFolder();
	}
	//$('ul.telefilms').tree({default_expanded_paths_string : '0/0/0,0/0/2,0/2/4'});
	$('span.demo0').contextMenu('myMenu0', {
		bindings: {
			'add': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_pmb_add.php?location_id='+Math.random(), loadingImage);
			},
		}
	});
	$('span.demo2').contextMenu('myMenu1', {
		bindings: {
			'add': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_pmb_add.php?location_id='+Math.random()+'&id='+t.id, loadingImage);
			},
			'edit': function(t) { 
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_pmb_edit.php?location_id='+t.id, loadingImage);
			},
			'delete': function(t) {
				deleteSubchapter(t.id);
			},
			'paste': function(t) {
				if(copyStatus === true){
					pasteLocation(copyId, t.id);
					//copyId = '';
					//copyStatus = false;
				}else{
					jAlert('Copy Location for Paste Here !');
				}
			},
		}
	});
	$('span.demo3').contextMenu('myMenu3', {
					bindings: {
						'edit': function(t) { 
							modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_pmb_edit.php?location_id='+t.id, loadingImage);
						},
						'delete': function(t) {
							deleteSubchapter(t.id);
						},
						'paste': function(t) {
							if(copyStatus === true){
							//pasteLocation(copyId, t.id);
							//copyId = '';
							//copyStatus = false;
						}else{
							jAlert('Copy Location for Paste Here !');
						}
					},
				}
			});
	
});

function deleteSubchapter(chapterId){
var r = jConfirm("Do you want to Delete subchapter?", null, function(r){
	if (r === true){
		$.ajax({
			url: "location_tree_pmb_edit.php?deleteId="+Math.random(),
			type: "POST",
			data: {chapterId:chapterId},
			success: function (res) {
				var obj = JSON.parse(res);
				if(obj.status){
					jAlert('Sub folder deleted successfully');
					loadSubFolder();
				}
			}
		});	
	}
});
}

function addLocation(){
	var locationname 	= $('#subLocation').val();
	var projectId 		= $('#projectId').val();
	var chapterId 		= $('#chapterId').val();
	$.ajax({
				url: "location_tree_pmb_add.php",
				type: "POST",
				data: {locationName: locationname,projectId:projectId, uniqueId:Math.random(), chapterId:chapterId},
				success: function (res) {
					$('#loc_'+projectId).append(res);
					closePopup(300);
					jAlert('Sub folder added successfully');
					loadSubFolder();
				}
			});	
}

function editLocationEdit(){
	var locationname 	= $('#subLocation').val();
	var projectId 		= $('#projectId').val();
	var chapterId 		= $('#chapterId').val();
	$.ajax({
				url: "location_tree_pmb_edit.php?uniqueId="+Math.random(),
				type: "POST",
				data: {locationName: locationname,projectId:projectId, uniqueId:Math.random(), chapterId:chapterId},
				success: function (res) {
					
					if(res.status){
						var newhtml = '<span class="jtree-button demo2" id="18"><a title="Subcontractor" href="?sect=messages&folderType='+locationname+'">'+locationname+'</a></span>';	
						$('#li_'+chapterId).html(newhtml);
					}
					closePopup(300);
					jAlert('Sub folder Updated successfully');
					loadSubFolder();
				}
			});	
}

function searchText(id, searchKey){ 
	var searIdArr = new Array();
	searIdArr['parentId'] = '';
	searIdArr['parentId2'] = '';
	searIdArr['subChildId'] = '';
	$('#'+id+' ul > li span a').each(function(){ 
		var childName = $(this).parent().parent().children().next().prop("tagName");
		if(childName != 'UL'){
			$(this).parent().parent().children().attr('class', 'jtree-button demo2 subFolder');
		}		
		 if (jQuery(this).text().search(new RegExp(searchKey, "i")) < 0) {
           // jQuery(this).hide();
			$(this).parent().parent().parent().hide();
        } else {
			var resTxt = $(this).text().trim(); 
			if(resTxt == searchKey){
				//alert($(this).text().trim());	
				if(childName != 'UL'){
					searIdArr['parentId'] = $(this).parent().parent().parent().parent().parent().attr('id');
					searIdArr['parentId2'] =  $(this).parent().parent().parent().attr('id');
					searIdArr['childId'] = $(this).parent().next().attr('id');
					//alert($(this).parent().attr('id'));
				}else{
					searIdArr['parentId'] = $(this).parent().parent().parent().attr('id');
					searIdArr['childId'] = $(this).parent().next().attr('id');
				}
				$(this).addClass('selected'); 
			}
			$(this).parent().parent().parent().hide();	
		}
    });   
	return searIdArr;
}*/
</script>	
<?php //Function section start here
function unreadCountFolder($folder=""){
	$conQRY = "";
	if($folder != ""){
		$conQRY = 'message_type LIKE "%'.$folder.'%" AND';
	$inboxcountQry = "SELECT
						COUNT(*) AS NUMS
					FROM
						pmb_user_message as um,
						pmb_message m
					WHERE
						um.message_id = m.message_id AND
						um.type = 'inbox' AND
						".$conQRY."
						m.is_draft = 0 AND
						um.is_deleted = 0 AND
						um.project_id = '".$_SESSION['idp']."' AND
						um.user_id = '".$_SESSION['ww_builder_id']."' AND
						inbox_read = 0
					GROUP BY
						m.title,
						m.message_type
					ORDER BY
						user_message_id DESC";
		$rsInboxcount = mysql_query($inboxcountQry);
		//$inboxcount = mysql_fetch_object($rsInboxcount);
		//return $inboxcount->NUMS;
		return  mysql_num_rows($rsInboxcount);
	}else{
		$inboxcountQry = "SELECT
						COUNT(*) AS NUMS
					FROM
						pmb_user_message as um,
						pmb_message m
					WHERE
						um.is_deleted = 0 AND um.project_id = ".$_SESSION['idp']." 
						AND (um.message_id = m.message_id AND
						um.type = 'inbox' AND um.inbox_read = 0 AND 
						m.is_draft = 0 AND
						um.user_id = '".$_SESSION['ww_builder_id']."')
						AND 
						(m.message_type IN ('General Correspondence','Inspections', 'Document Transmittal', 'Document Transmittal_Document Register Updates', 'Memorandum', 'Site Instruction', 'Consultant', 'Consultant_Architect / Superintendant Instruction', 'Consultant_Consultant Advice Notice', 'Consultant_Design', 'Consultant_Design_Design Changes', 'Consultant_Design_Design Meeting', 'Contract Admin', 'Contract Admin_Client Side', 'Contract Admin_Client Side_Variation Claims', 'Contract Admin_Client Side_EOTs', 'Contract Admin_Client Side_NODs', 'Contract Admin_Client Side_Formal Correspondence', 'Contract Admin_Client Side_Meeting minutes', 'Contract Admin_Client Side_Superintendant Instruction', 'Contract Admin_Client Side_Purchaser Changes', 'Contract Admin_Client Side_Inspections', 'Contract Admin_Subcontractors', 'Contract Admin_Subcontractors_Variation Claims', 'Contract Admin_Subcontractors_NODs', 'Contract Admin_Subcontractors_Formal Correspondence', 'Contract Admin_Subcontractors_Subcontractor Meetings', 'Estimating', 'Estimating_Tenders', 'Estimating_Construction')
						OR
						(m.message_type IN ('Request For Information') AND inbox_read = 0))
					GROUP BY
						um.thread_id";
		$rsInboxcount = mysql_query($inboxcountQry);
#		$inboxcount = mysql_fetch_object($rsInboxcount);
		return  mysql_num_rows($rsInboxcount);
	}
}


 //Function section end here
?>
