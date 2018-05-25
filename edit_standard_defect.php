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
$q_standard=$obj->db_query("SELECT * FROM standard_defects WHERE standard_defect_id ='".base64_decode($_REQUEST['id'])."'");

$standard=mysql_fetch_array($q_standard);
if(isset($_POST['button_x']))
{
	if(isset($_POST['description']) && !empty($_POST['description']))
	{
		$description=$_POST['description'];	
	}
	if(isset($_POST['tag']) && !empty($_POST['tag']))
	{
		$tag=$_POST['tag'];	
	}
	else
	{
		$tag='';	
	}
	if(isset($description))
		{
			$standard_defect_update = "UPDATE standard_defects SET
									description = '".$description."',
									tag = '".$tag."',
									last_modified_date = NOW(),
									last_modified_by = ".$builder_id."
							WHERE
								standard_defect_id = '".base64_decode($_REQUEST['id'])."'";
			//echo $standard_defect_update; die;
			
			mysql_query($standard_defect_update);
			$_SESSION['std_defect_edit']='Standard defect updated successfully.';
			header('location:?sect=standard_defect');
			
		}
		else
		{
			$_SESSION['std_defect_edit_err']='Standard defect  not updated.';
		}
}
// get all other managers created by comman company

?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/add_edit_standard_defect_csv.js"></script>

<!-- Ajax Post -->
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
</style>
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form  method="post"  enctype="multipart/form-data" name="add_edit_standard_defectFrm" id="add_edit_standard_defectFrm" >
		
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/hd_edit_standard_defect.png);"></div>
					
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-60px;">
							<?php if(isset($_SESSION['std_defect_edit_err'])) { ?>
                            <tr>
                            	<td colspan="2" align="center"><div class="failure_r" style="height:30px;width:300px;"><p><?php echo$_SESSION['std_defect_edit_err']; ?></p></div></td>
                             </tr>   
                            <?php unset($_SESSION['std_defect_edit_err']); } ?>
                            
                            <tr>
								<td valign="top">Description <span class="req">*</span></td>
								<td>
                                	<textarea name="description" id="description" class="text_area"><?php echo $standard['description'];?></textarea>
                              </td>
							</tr>	
                            
                            <tr>
								<td valign="top">Tag </td>
								<td>
                                	<textarea name="tag" id="tag" class="text_area"><?php echo $standard['tag'];?></textarea>
                               <br/>
                                    Please seperate location by semicolon(;).
                              </td>
							</tr>				
							
							<tr>
								<td>&nbsp;</td>
								<td>
									<input type="hidden" value="add_project" name="sect" id="sect" />
									<input name="button" type="image" class="submit_btn" id="button" value="submit" src="images/update.png" style="border:none; width:111px;" />
								</td>
							</tr>
						</table>
					</div>
				</div>
				
			</div>
		</form>
	</div>
</div>