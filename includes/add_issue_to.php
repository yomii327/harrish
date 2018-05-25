<?php #if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){ ?>
<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
$builder_id = $_SESSION['ww_is_company'];
if(isset($_POST['button_x'])){
	$issue_company_name='';
	$issue_phone='';
	$issue_emailid='';
	$issue_contact_name='';
	$issue_to_tags='';
	if(isset($_POST['contact_name']) && !empty($_POST['contact_name']))
		$issue_contact_name=$_POST['contact_name'];	
		
	if(isset($_POST['company_name']) && !empty($_POST['company_name']))
		$issue_company_name=$_POST['company_name'];	
	else
		$issue_contact_name_err='<div class="error-edit-profile">The company name field is required</div>';

	if(isset($_POST['phone']) && !empty($_POST['phone']))
		$issue_phone=$_POST['phone'];	

	if(isset($_POST['emailid']) && !empty($_POST['emailid']))
		$issue_emailid=$_POST['emailid'];	

	if(isset($_POST['tag']) && !empty($_POST['tag'])){
		$issue_to_tags=$_POST['tag'];	
		$issue_to_tags = trim($issue_to_tags, ";");
		$issue_to_tags = implode(";", array_map('trim', explode(";", $issue_to_tags)));
		if ($issue_to_tags != "")
			$issue_to_tags = trim($issue_to_tags) . ";";
	}else{
		$issue_to_tags = '';
	}
	if(isset($issue_company_name)){
		$select = "SELECT id FROM master_issue_to WHERE issue_to_name = '".addslashes(trim($issue_company_name))."' AND is_deleted=0";
		$issue = mysql_query($select);
		$row_data = mysql_num_rows($issue);
		if($row_data > 0){
			$_SESSION['issue_add_err']='Duplicate record.';
		}else{
			$issue_insert = "INSERT INTO master_issue_to SET
								issue_to_name = '".addslashes(trim($issue_company_name))."',
								company_name = '".addslashes(trim($issue_contact_name))."',
								issue_to_phone = '".addslashes(trim($issue_phone))."',
								issue_to_email = '".addslashes(trim($issue_emailid))."',
								tag = '".addslashes(trim($issue_to_tags))."',
								last_modified_date = NOW(),
								last_modified_by = ".$builder_id.",
								created_date = NOW(),
								created_by = ".$builder_id;
			mysql_query($issue_insert);
			$_SESSION['issue_add'] = 'Issued to added successfully.';
			header('location:?sect=c_issue_to');
		}
	}else{
		$_SESSION['issue_add_err']='Issued to not added.';
	}
}
?>
<style>.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}</style>
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form  method="post"  enctype="multipart/form-data" name="addissueto" id="addissueto" >
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/hd_add_issue.png);margin-top:-50px\9;"></div>
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-60px;">
							<?php if(isset($_SESSION['issue_add_err'])) { ?>
							<tr>
								<td colspan="2" align="center">
									<div class="failure_r" style="width:250px;margin:3px;margin-left:158px;">
										<p><?php echo $_SESSION['issue_add_err'];?></p>
										<?php unset($_SESSION['issue_add_err']);  ?>
									</div>
								</td>
							</tr>
							<?php }?>
							<tr>
								<td valign="top">Company Name <span class="req">*</span></td>
								<td>
									<input name="company_name" type="text" class="input_small" id="company_name"  value="<?php if(isset($_POST['company_name']))echo $_POST['company_name']; ?>"/>
									<?php if(isset($issue_company_name_err)) { echo $issue_company_name_err; } ?>
								</td>
							</tr>
							<tr>
								<td nowrap="nowrap" valign="top">Contact Name </td>
								<td>
									<input name="contact_name" type="text" class="input_small" id="contact_name" value="<?php if(isset($_POST['contact_name']))echo $_POST['contact_name']; ?>" />
								</td>
							</tr>
							<tr>
								<td nowrap="nowrap" valign="top">Phone</td>
								<td>
									<input name="phone" type="text" class="input_small" id="phone"   value="<?php if(isset($_POST['phone']))echo $_POST['phone']; ?>"/>
								</td>
							</tr>
							<tr>
								<td width="133" valign="top">Email</td>
								<td width="252">
									<input name="emailid" type="text" class="input_small" id="emailid"   value="<?php if(isset($_POST['emailid']))echo $_POST['emailid']; ?>" />
								</td>
							</tr>
							<tr>
								<td valign="top">Tags</td>
								<td>
									<textarea name="tag" id="tag" class="text_area"><?php if(isset($_POST['tag']))echo $_POST['tag']; ?></textarea><br/>Please seperate location by semicolon(;). </td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<input type="hidden" value="add_project" name="sect" id="sect" />
									<input name="button" type="image" class="submit_btn" id="button" value="submit" src="images/save.png" style="border:none; width:111px;" />
									<a href="javascript:history.back();">
										<img src="images/back_btn.png" style="border:none; width:111px;" />
									</a>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/add_edit_issued_csv.js"></script>