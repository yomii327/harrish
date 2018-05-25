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
$qs=$obj->db_query("SELECT b.user_id,b.user_fullname FROM ".BUILDERS." b 
					LEFT JOIN ".SUBBUILDERS." sb 
					ON b.user_id=sb.sb_id 
					WHERE b.fk_c_id = '".$_SESSION['ww_builder_fk_c_id']."' 
					AND b.user_id!='$builder_id' 
					GROUP BY b.user_id ");


if(isset($_POST['button_x']))
{
	if(isset($_POST['description']) && !empty($_POST['description']))
	{
		$description=$_POST['description'];	
	}
	else
	{
		$description_err='<div class="error-edit-profile">The description field is required</div>';
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
			$standard_insert="INSERT INTO standard_defects SET
									description = '".$description."',
									tag = '".$tag."',
									last_modified_date = NOW(),
									last_modified_by = ".$builder_id.",
									created_date = NOW(),
									created_by = ".$builder_id.",
									project_id = ".$_SESSION['idp'];
			//echo $standard_insert; die;
			
			mysql_query($standard_insert);
			$_SESSION['standard_defect_add']='Standard defect added successfully.';
			header('location:?sect=standard_defect');
			
		}
		else
		{
			$_SESSION['standard_defect_add_err']='Standard defect not added.';
		}
	}


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
<link href="../style.css" rel="stylesheet" type="text/css" />
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form  method="post"  enctype="multipart/form-data" name="add_edit_standard_defectFrm" id="add_edit_standard_defectFrm" >
		
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/hd_add_standard_defect.png);"></div>
					
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form">
						<table width="470" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-60px;">
							<tr>
								<td valign="top">Description <span class="req">*</span></td>
								<td>
                                	<textarea name="description" id="description" class="text_area"></textarea>
                              </td>
							</tr>
                            <tr>
								<td valign="top">Tag</td>
								<td>
                                	<textarea name="tag" id="tag" class="text_area"></textarea>
                                    <br/>
                                    Please seperate location by semicolon(;).
                                    
                              </td>
							</tr>
										
							
			  <tr>
								<td>&nbsp;</td>
								<td>
									<input type="hidden" value="add_project" name="sect" id="sect" />
									<input name="button" type="image" class="submit_btn" id="button" value="submit" src="images/save.png" style="border:none; width:111px;" />
								</td>
							</tr>
						</table>
					</div>
				</div>
				
			</div>
		</form>
	</div>
</div>