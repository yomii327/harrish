<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
$builder_id=$_SESSION['ww_builder_id'];

// get standard defects list created by current manager 
$qd=$obj->db_query("SELECT * FROM ".DEFECTSLIST." WHERE fk_b_id='$builder_id'");

// get all other managers created by comman company

if(isset($_POST['button_x'])){
	if(isset($_POST['contact_name']) && !empty($_POST['contact_name'])){
		$issue_contact_name=$_POST['contact_name'];	
	}
	
	if(isset($_POST['company_name']) && !empty($_POST['company_name'])){
		$issue_company_name=$_POST['company_name'];	
	}else{
		$issue_company_name_err='<div class="error-edit-profile">The company name field is required</div>';
	}
	
	if(isset($_POST['phone']) && !empty($_POST['phone'])){
		$issue_phone=$_POST['phone'];	
	}
	
	if(isset($_POST['emailid']) && !empty($_POST['emailid'])){
		$issue_emailid=$_POST['emailid'];	
	}

	if(isset($_POST['tag']) && !empty($_POST['tag'])){
		$tag=$_POST['tag'];
		$tag=trim($tag, ";");
		$tag = implode(";", array_map('trim', explode(";", $tag)));
		if ($tag != ""){
			$tag = trim($tag) . ";";
		}
	}else{
		$tag='';
	}
	
	if(isset($issue_company_name)){
		$issue_update="update inspection_issue_to set 
							issue_to_name = '".addslashes(trim($issue_company_name))."',
							company_name = '".addslashes(trim($issue_contact_name))."',
							issue_to_phone = '".trim($issue_phone)."',
							issue_to_email = '".trim($issue_emailid)."',
							tag = '".addslashes(trim($tag))."',
							last_modified_date = now(),
							last_modified_by = ".$builder_id."
						where
							issue_to_id='".base64_decode($_REQUEST['id'])."'";
		mysql_query($issue_update);
		$_SESSION['issue_edit']='Issued to updated successfully.';
		header('location:?sect=issue_to');
	}else{
		$_SESSION['issue_add_err']='Issued to updated.';
	}
}

$qs=$obj->db_query("SELECT * FROM inspection_issue_to WHERE issue_to_id ='".base64_decode($_REQUEST['id'])."'"); 

$row=mysql_fetch_array($qs);

?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/add_edit_issued_csv.js"></script>

<!-- Ajax Post -->
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
</style>
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form  method="post"  enctype="multipart/form-data" name="addissueto" id="addissueto" >
		
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/hd_edit_issue.png);margin-top:-50px\9;"></div>
					
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-60px;">
							<?php if(isset($_SESSION['issue_add_err'])) { ?><tr><td colspan="2" align="center"><div class="failure_r" style="width:250px;margin:3px;margin-left:158px;"><p><?php echo $_SESSION['issue_add_err'];?></p><?php unset($_SESSION['issue_add_err']);  ?></div></td></tr> <?php }?>
                            
							
                            <tr>
								<td valign="top">Company Name <span class="req">*</span></td>
								<td><input name="company_name"  value="<?php echo $row['issue_to_name'] ;?>"  type="text" class="input_small" id="company_name" /></td>
							</tr>
                            <tr>
								<td nowrap="nowrap" valign="top">Contact Name </td>
								<td>
                               
                                	<input name="contact_name"  value="<?php echo $row['company_name'] ;?>" type="text" class="input_small" id="contact_name" />
                                </td>
							</tr>
                            
							<tr>
								<td nowrap="nowrap" valign="top">Phone</td>
								<td><input name="phone" type="text"  value="<?php echo $row['issue_to_phone'] ;?>"  class="input_small" id="phone" /></td>
							</tr>
							<tr>
								<td width="133" valign="top">Email</td>
								<td width="252"><input name="emailid"  value="<?php echo $row['issue_to_email'] ;?>"  type="text" class="input_small" id="emailid" /></td>
							</tr>
							<tr>
								<td valign="top">Tags</td>
								<td>
									<textarea name="tag" id="tag" class="text_area"><?php echo $row['tag'] ;?></textarea>
									<br/>
									Please seperate location by semicolon(;).
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<input type="hidden" value="add_project" name="sect" id="sect" />
									<input name="button" type="image" class="submit_btn" id="button" value="submit" src="images/update.png" style="border:none; width:111px;" />
                                    
                                      <a href="javascript:history.back();">
                    <img src="images/back_btn.png" style="border:none; width:111px;" /></a>
								</td>
							</tr>
						</table>
					</div>
				</div>
				
			</div>
		</form>
	</div>
</div>