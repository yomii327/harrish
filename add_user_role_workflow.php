<?php session_start();
$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 

// Add new contact
$project_id = $_SESSION['idp'];
// $project_id = 0;
$_SESSION['allvalue'] = array();
if(isset($_REQUEST["antiqueID"])){
	#echo "<pre>"; print_r($_POST); die;
	$outputArr = array();
	if(isset($_POST['status'])){
		$htmlentit = htmlentities($_POST['editor1']);
		$newDate = '';
		if(!empty($_POST['by_when'])){
			$originalDate = $_POST['by_when'];
			$newDate = date("Y-m-d", strtotime($originalDate));
		}
		//if(isset($_POST['assign']) && !empty($_POST['assign'])){
			$docStatusData = '';
			//if($_POST['status'] == 'Document status changed'){
			if(isset($_POST['status'])){
				$doc_status = $_POST['doc_status'];
				foreach ($doc_status as $row1) {
					if(!empty($docStatusData)){
						$docStatusData .= ',' .$row1;
					}else{
						$docStatusData = $row1;
					}
				}
			}

			$assign = $_POST['assign'];
			if(isset($assign) && !empty($assign)){
				foreach ($assign as $row) {
					$expData = explode("###",$row);
					if(!empty($expData[0]) && !empty($expData[1])){
						if(!empty($idsData)){
							$idsData .= ',' .$expData[0];
							$nameData .= ',' .$expData[1];
						}else{
							$idsData = $expData[0];
							$nameData = $expData[1];
						}
					}
				}
			}
			$idsData = 0;
			$nameData = 'All Users';

			$chapterIds = $_POST['checknox_val'];
			$chapIds = '';
			if(!empty($chapterIds)){
				$chapIds = implode(',',$chapterIds);
			}
			//echo "<pre>"; print_r($chapIds); die;

			$queryData = "user_ids = '".$idsData."',
				user_name = '".$nameData."',
				rule_name = '".addslashes(trim($_POST['rule_name']))."',
				status = '".addslashes(trim($_POST['status']))."',
				doc_status = '".$docStatusData."',
				user_role = '".addslashes(trim($_POST['user_role']))."',
				by_when = '".$newDate."',
				mail_action = '".addslashes(trim($_POST['action']))."',
				chapter_ids = '".$chapIds."',
				email_subject = '".addslashes(trim($_POST['subject']))."',
				emain_text = '".addslashes(trim($_POST['emailContent']))."',
				last_modified_date = NOW(),
				original_modified_date = NOW(),
				last_modified_by = ".$builder_id;
			
			if(isset($_REQUEST["option"]) && $_REQUEST["option"] == 0){
				$insQuery = "INSERT INTO user_role_workflow SET 
					project_id = ".$project_id.",
					created_date = NOW(),
					created_by = ".$builder_id.
					", ".$queryData;
				#echo $insQuery;die;
				mysql_query($insQuery) or die(mysql_error());
					
			}else{
				$id = '';
				$attsql = "UPDATE user_role_workflow SET ".$queryData. " WHERE id = ".$_POST['rule_id'];
				mysql_query($attsql);
			}
		//}
		//$checklistId = mysql_insert_id();
		if(mysql_affected_rows() > 0){
			$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Role added successfully!');
			$_SESSION['successMsg'] = $outputArr['msg'];
		}
		
	}else{
		//$_SESSION['issue_add_err']='Issued to not added.';
		$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Project Checklist to added successfully!');
	}
	echo json_encode($outputArr); die();	
}

if(isset($_REQUEST["delete"])){
	$attsql = "UPDATE user_role_workflow SET is_deleted = 1 WHERE id = ".$_REQUEST['delete'];
	mysql_query($attsql);
	$outputArr = array('status'=> true, 'error'=> false, 'msg'=> 'Role deleted successfully!');
	echo json_encode($outputArr); die();
}

// Load HTML form 
if(isset($_REQUEST["name"])){
	$users = $obj->selQRYMultiple('user_id', 'user_projects', 'project_id = "'.$_SESSION['idp'].'" and is_deleted = 0 ORDER BY user_id');
	$projectUser = '';
	if(!empty($users)){
		foreach($users as $user){
			if($projectUser == ''){ $projectUser .= $user['user_id']; }else{ $projectUser .= ', '.$user['user_id']; }
		}
	}
	//$newUsers = $obj->selQRYMultiple('user_id, user_fullname, user_name', 'user', 'is_deleted = 0 and user_id NOT IN ('.$projectUser.')');

	$allUsers = $obj->selQRYMultiple('distinct u.user_id, u.user_name, u.user_fullname, up.user_role', 'user_projects as up, user as u', 'u.user_id = up.user_id and u.is_deleted = 0 and up.is_deleted = 0 and u.user_id IN ('.$projectUser.') and project_id = "'.$_SESSION['idp'].'"');

	//$allUsers = $obj->selQRYMultiple('user_id, user_fullname, user_name', 'user', 'is_deleted = 0');
	//echo "<pre>"; print_r($allUsers); die;
	$user_ids = array();
    $rule_name = '';
    $status = '';
    $user_role = '';
    $by_when = '';
    $mail_action = '';
    $rule_id = '';
    $updateFlag = 0;
    $docStatus = array();
    $disabledFlag = '';
    $flagForHeading = 0;
    $chapter_ids = array();
    $all_chapter_ids = '';
    $email_subject = '';
	$emain_text = '';

	if(isset($_REQUEST["update"]) && $_REQUEST["update"] == 1){
		$flagForHeading = 1;
		$ruleDetail = $obj->selQRYMultiple('id, user_ids, rule_name, status, doc_status, user_role, by_when, mail_action, chapter_ids, email_subject, emain_text', 'user_role_workflow', 'is_deleted = 0 AND id = '.$_REQUEST["ruleId"]);
		if(!empty($ruleDetail)){
			$user_ids = explode(',', $ruleDetail[0]['user_ids']);
            $rule_id = $ruleDetail[0]['id'];
            $rule_name = $ruleDetail[0]['rule_name'];
            $status = $ruleDetail[0]['status'];
            $docStatus = explode(',', $ruleDetail[0]['doc_status']);
            $user_role = $ruleDetail[0]['user_role'];
            $all_chapter_ids = $ruleDetail[0]['chapter_ids'];
			$chapter_ids = explode(',', $ruleDetail[0]['chapter_ids']);
			$email_subject = $ruleDetail[0]['email_subject'];
			$emain_text = $ruleDetail[0]['emain_text'];
            
            if(!empty($ruleDetail[0]['by_when']) && $ruleDetail[0]['by_when'] > 0){
            	$date = strtotime($ruleDetail[0]['by_when']);
    			$by_when = date('d-m-Y', $date);
    		}
            
            $mail_action = $ruleDetail[0]['mail_action'];
            $updateFlag = 1;
		}
		if(isset($_REQUEST["viewRule"]) && $_REQUEST["viewRule"] == 1){
			$flagForHeading = 2;
			$disabledFlag = 'disabled';
		}
	}

	//$checkEvent = "SELECT id FROM user_role_workflow WHERE project_id = ".$_SESSION['idp']." AND status = ".$_REQUEST["checkEvent"]." AND is_deleted = 0 ";
	$eventData = array();
	$checkEvent = $obj->selQRYMultiple('id,status', 'user_role_workflow', 'project_id = "'.$_SESSION['idp'].'" and is_deleted = 0');
	if(!empty($checkEvent)){
		foreach($checkEvent as $event){
			$eventData[] = $event['status'];
		}
	}
	//echo '<pre>'; print_r($eventData); die;
/* ================================================================== */
$everArr = array('New Inspections Added' => 'New Inspections Added');
?>
<style>
body{
	color:#000000;
}

.popup_form{
	background: #f3f3f3 none repeat scroll 0 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 75%;
    height: 25px;
}
.popup_form_multi{
	background: #f3f3f3 none repeat scroll 0 0;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 75%;
}

ul.status_block{
	background-color: #f2f2f2;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: #8b8b8b;
    display: block;
    margin: 0;
    padding: 5px;
    width: 72%;
}
ul.status_block li{
	display: block;
    font-size: 14px;
    list-style: outside none none;
    margin: 0;
    padding: 1px 0;
}

fieldset.roundCorner {
	overflow-x: hidden;
	overflow-y: scroll;
	height: auto;
}

.chapterContainer{
 overflow-y: scroll;
 max-height: 200px; 
 min-height: 150px; 
 border-radius:5px; 
 -moz-border-radius: 5px;  
 -webkit-border-radius: 5px; 
 border:1px solid #000; 
 margin-top:15px; 
 width:405px; 
}
.req{
	font-size: 16px;
	font-weight: bold;
}

li.childShow > ul{
	display: block;
}

</style>
	<fieldset class="roundCorner">
		<?php if($flagForHeading == 0){ ?>
			<legend style="color:#000000;">Add User Rule</legend>
		<?php }else if($flagForHeading == 1){ ?>
			<legend style="color:#000000;">Edit User Rule</legend>
		<?php }else if($flagForHeading == 2){ ?>
			<legend style="color:#000000;">View User Rule</legend>
		<?php } ?>

		<form name="addUserRole" id="addUserRole">
			<table width="100%" border="0" align="left" cellpadding="0" cellspacing="15">
				<input type="hidden" id="rule_id" name="rule_id" value="<?=$rule_id?>" />
				<input type="hidden" id="all_chapter_ids" name="all_chapter_ids" value="<?=$all_chapter_ids?>" />
				<tr>
					<td width="20%" valign="top" align="left">Rule Name <span class="req"> <b> *</b></span></td>
					<td width="80%" align="left">
						<input name="rule_name" id="rule_name" value="<?php if(!empty($rule_name)){echo $rule_name;}?>" type="text" class="popup_form" <?php echo $disabledFlag; ?> >
						<lable for="rule_name" id="errorRuleName" generated="true" class="error" style="display:none;">
							<div class="error-edit-profile">The Rule Name field is required</div>
						</lable>
					</td>
				</tr>

				<tr>
					<td valign="top" align="left">Event <span class="req"> <b> *</b></span></td>
					<td align="left">
						<select name="status" id="status" class="popup_form" onchange="selectStatus(this.value)" <?php echo $disabledFlag; ?> >
							<option value="">-- Select --</option>
							<?php if(isset($everArr) && !empty($everArr)){
								foreach($everArr as $key=>$values){
									$selected = ($status==$key)?'selected="selected"':'';
									echo '<option value="'. $key .'" '. $selected .'>'. $values .'</option>';
								}
							} ?>
						</select>
						<lable for="status" id="errorStatus" generated="true" class="error" style="display:none;">
							<div class="error-edit-profile">The Event field is required</div>
						</lable>
					</td>
				</tr>
				<tr id="doc_status_show" <?php if($status !== 'Document status changed'){echo 'style=display:table-row';} ?> >
					<td valign="top" align="left">Status <span class="req"> <b> *</b></span></td>
					<td align="left">
						<?php if(!empty($disabledFlag)){ ?>
							<ul class="status_block">
								<li>
									<?php if(in_array('Uploaded',$docStatus)){echo 'Uploaded';} ?>
								</li>
								<li>
									<?php if(in_array('On Hold',$docStatus)){echo 'On Hold';} ?>
								</li>
								<li>
									<?php if(in_array('Rejected',$docStatus)){echo 'Rejected';} ?>
								</li>
								<li>
									<?php if(in_array('Reviewed',$docStatus)){echo 'Reviewed';} ?>
								</li>
								<li>
									<?php if(in_array('Complete',$docStatus)){echo 'Complete';} ?>
								</li>
							</ul>
						<?php }else{ ?>
							<select name="doc_status[]" id="doc_status" class="popup_form_multi" multiple <?php echo $disabledFlag; ?> >
								<option value="">-- Select --</option>
								<option value="Uploaded" <?php if(in_array('Uploaded',$docStatus)){echo 'selected=selected';} ?> >Uploaded</option>
								<option value="On Hold" <?php if(in_array('On Hold',$docStatus)){echo 'selected=selected';} ?> >On Hold</option>
								<option value="Rejected" <?php if(in_array('Rejected',$docStatus)){echo 'selected=selected';} ?> >Rejected</option>
								<option value="Reviewed" <?php if(in_array('Reviewed',$docStatus)){echo 'selected=selected';} ?> >Reviewed</option>
								<option value="Complete" <?php if(in_array('Complete',$docStatus)){echo 'selected=selected';} ?> >Complete / Signed Off</option>
							</select>
							<lable for="doc_status" id="errorDocStatus" generated="true" class="error" style="display:none;">
								<div class="error-edit-profile">The Status field is required</div>
							</lable>
						<?php } ?>
					</td>
				</tr>

				<tr>
					<td valign="top" align="left">Action <span class="req"> <b> *</b></span></td>
					<td align="left">
						<select name="action" id="action" class="popup_form" <?php echo $disabledFlag; ?> >
							<option value="">-- Select --</option>
							<option value="Email Notification" <?php if($mail_action == 'Email Notification'){echo 'selected=selected';} ?> >Email Notification</option>
						</select>
						<lable for="action" id="errorAction" generated="true" class="error" style="display:none;">
							<div class="error-edit-profile">The Action field is required</div>
						</lable>
					</td>
				</tr>

				<!-- START Email editor -->
				<tr>
					<td valign="top" align="left">Subject</td>
					<td align="left">
						<input name="subject" id="subject" value="<?php if($flagForHeading == 1 || $flagForHeading == 2){echo $email_subject; }else{ echo 'Document Added / Revised'; }?>" class="popup_form" type="text" <?php echo $disabledFlag; ?> >
						<lable for="action" id="errorAction" generated="true" class="error" style="display:none;">
							<div class="error-edit-profile">The Action field is required</div>
						</lable>
					</td>
				</tr>

				<tr>
					<td valign="top" align="left">Email</td>
					<td align="left">
						<textarea name="editor1" id="editor1" rows="10" cols="80" <?php echo $disabledFlag; ?>>
						<?php if($flagForHeading == 1 || $flagForHeading == 2){ 
                                echo $emain_text; 
                             }else{ ?>
                             
                           		Hello,<br/><br/>
                                A new document has been added to the <?php echo $_SESSION['project_name']; ?> project.<br/>
                            Log into the system using the link below.<br />
                            <?php $path = "http://".str_replace("/", "", str_replace("http://", "", DOMAIN)); ?>
                            <a href="<?php echo $path.'/pms.php?sect=b_full_analysis'; ?>"> <?php echo $path.'/pms.php?sect=b_full_analysis'; ?></a>
                        <?php }?>
			            </textarea><br />
                        <span style="color:red"><strong>Note:</strong> Please don't change the values inside [] as this is automatically generated on the email.</span>
					</td>
				</tr>
				<!-- END Email editor -->

				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="hidden" name="projectId" value="<?php echo $_SESSION['idp'];?>" />
						<?php if($updateFlag == 0){ ?>
							<input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/save.png);font-size:0px; border:none; width:111px;float:left;" onclick="submitUserRole(0);" />
						<?php }else if(empty($disabledFlag)){ ?>
							<input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/update.png);font-size:0px; border:none; width:111px;float:left;" onclick="submitUserRole(1);" />
						<?php } ?>
					</td>
				</tr>
			</table>
		</form>
		<br clear="all" />
                <br />
	</fieldset>
<?php }?>
