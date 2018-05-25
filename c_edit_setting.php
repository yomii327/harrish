<?php session_start();
$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class(); 
$builder_id = $_SESSION['ww_is_company'];

// Add new record
if(isset($_REQUEST["antiqueID"])){
	
	$comanQuery = "header_text_color = '".addslashes(trim($_POST['header_text_color']))."',
		header_bg_color = '".addslashes(trim($_POST['header_bg_color'])).",".addslashes(trim($_POST['header_bg_color'])).",".addslashes(trim($_POST['header_bg_color']))."',
		header_font_size = '".addslashes(trim($_POST['header_font_size']))."',
		button_font_size = '".addslashes(trim($_POST['button_font_size']))."',
		button_text_colour = '".addslashes(trim($_POST['button_text_colour']))."',
		button_bg_colour = '".addslashes(trim($_POST['button_bg_colour1'])).",".addslashes(trim($_POST['button_bg_colour1']))."',
		navigation_text_color = '".addslashes(trim($_POST['navigation_text_color']))."',
		navigation_bg_color = '".addslashes(trim($_POST['navigation_bg_color1'])).",".addslashes(trim($_POST['navigation_bg_color1']))."',
		navigation_font_size = '".addslashes(trim($_POST['navigation_font_size']))."',
		last_modified_date = NOW(),
		last_modified_by = ".$builder_id;
	
	if($_POST['id']>0){
		$outputArr = array('status'=> false, 'msg'=> 'Setting not updated please try again');
		$updateQRY = "UPDATE organisations_theme_settings SET ".$comanQuery." WHERE company_id = ". $_POST['id'];
		mysql_query($updateQRY);
		if(mysql_affected_rows() > 0){
			$outputArr = array('status'=> true, 'msg'=> 'Setting updated successfully!');
		}
	}
	echo json_encode($outputArr);					
}

// Load HTML form 
$header_bg_color = '';
if(isset($_REQUEST["name"])){
	// get form data by id
	$getData = $obj->selQRYMultiple('*', 'organisations_theme_settings', 'is_deleted = 0 AND company_id = '.$_REQUEST["formId"]);
	#echo "<pre>"; print_r($getData[0]['header_bg_color']); die;
	if(isset($getData[0]['header_bg_color']) && !empty($getData[0]['header_bg_color'])){
		$header_bg_color = $getData[0]['header_bg_color'];
		$header_bg_color = explode(',', $header_bg_color);
	}
	if(isset($getData[0]['button_bg_colour']) && !empty($getData[0]['button_bg_colour'])){
		$button_bg_colour = $getData[0]['button_bg_colour'];
		$button_bg_colour = explode(',', $button_bg_colour);
	}
	if(isset($getData[0]['navigation_bg_color']) && !empty($getData[0]['navigation_bg_color'])){
		$navigation_bg_color = $getData[0]['navigation_bg_color'];
		$navigation_bg_color = explode(',', $navigation_bg_color);
	}
	$formData = isset($getData[0])?$getData[0]:'';
?>
<style>
.header_setting{
 	background: -moz-linear-gradient(top,  #<?php echo $header_bg_color[0]; ?> 0%, #<?php echo $header_bg_color[0]; ?> 59%, #<?php echo $header_bg_color[0]; ?> 100%); /* FF3.6-15 */
	background: -webkit-linear-gradient(top,  #<?php echo $header_bg_color[0]; ?> 0%,#<?php echo $header_bg_color[0]; ?> 59%,#<?php echo $header_bg_color[0]; ?> 100%); /* Chrome10-25,Safari5.1-6 */
	background: linear-gradient(to bottom,  #<?php echo $header_bg_color[0]; ?> 0%,#<?php echo $header_bg_color[0]; ?> 59%,#<?php echo $header_bg_color[0]; ?> 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
}
.button_setting {
	margin-right: 6px;
    padding: 3px 10px;
    line-height: 26px;
    color: #FFF;
    border: 1px solid #435d01;
    border-radius: 6px;
    background: #<?php echo $button_bg_colour[0]; ?>;
    background: -moz-linear-gradient(top, #<?php echo $button_bg_colour[0]; ?> 0%, #<?php echo $button_bg_colour[0]; ?> 100%);
    background: -webkit-linear-gradient(top, #<?php echo $button_bg_colour[0]; ?> 0%,#<?php echo $button_bg_colour[0]; ?> 100%);
    background: linear-gradient(to bottom, #<?php echo $button_bg_colour[0]; ?> 0%,#<?php echo $button_bg_colour[0]; ?> 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#<?php echo $button_bg_colour[0]; ?>', endColorstr='#<?php echo $button_bg_colour[0]; ?>',GradientType=0 );
    -webkit-box-shadow: 0px 1px 0px 0px #000, inset 1px 1px 1px 0px rgba(179,247,2,1);
    -moz-box-shadow: 0px 1px 0px 0px #000, inset 1px 1px 1px 0px rgba(179,247,2,1);
    box-shadow: 0px 1px 0px 0px #000, inset 1px 1px 1px 0px rgba(179,247,2,1);
    text-shadow: 1px 1px rgba(0, 0, 0, 0.66);
	font-family:'Conv_Myriad_Pro-Semibold_It';
	font-size: 14px;
    font-weight: bold;
	letter-spacing: 0.5px;
}
.navigation_setting
{
    height: 47px;
	background: #<?php echo $navigation_bg_color[0]; ?>;
	background: -moz-linear-gradient(top,  #<?php echo $navigation_bg_color[0]; ?> 0%, #<?php echo $navigation_bg_color[0]; ?> 100%); 
	background: -webkit-linear-gradient(top,  #<?php echo $navigation_bg_color[0]; ?> 0%,#<?php echo $navigation_bg_color[0]; ?> 100%); 
	background: linear-gradient(to bottom,  #<?php echo $navigation_bg_color[0]; ?> 0%,#<?php echo $navigation_bg_color[0]; ?> 100%); 
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#<?php echo $navigation_bg_color[0]; ?>', endColorstr='#<?php echo $navigation_bg_color[0]; ?>',GradientType=0 ); 
	border-top: 1px solid #dcb10c;
}

.input_small{ padding: 0 5px 0 15px; }
.input_small2 {
    background-color: transparent;
    background-image: url("images/input_smal.png");
    background-repeat: no-repeat;
    border: medium none;
    color: #333333;
    font-family: Verdana,Arial,Helvetica,sans-serif;
    font-size: 14px;
    height: 33px;
    padding: 0 5px;
    width: 126px;
}
.innerDiv{ float:left; border:1px solid red; width:120px; height:120px; margin:0px 0px 20px 3px; }
label.filebutton{ width:120px; height:40px; overflow:hidden; position:relative; }
label input { cursor: pointer; font-size: 7px; left: -1px; line-height: 0; margin: 0; opacity: 0; padding: 0; position: absolute; top: -2px; z-index: 999; filter:alpha(opacity=0);}
</style>
<fieldset class="roundCorner">
	<legend style="color:#000000;">Edit Setting</legend>
	<form name="submitForm" id="submitForm">
	<table width="550" border="0" align="left" cellpadding="0" cellspacing="15">
		<tr>
			<td valign="top" align="left" width="50%">Header Text Color<span class="req">*</span></td>
			<td align="left">
				<input name="header_text_color" type="text" class="input_small header_color" onchange="setSetting('header')" id="header_text_color"  value="<?php echo isset($formData['header_text_color'])? $formData['header_text_color']:'#66ff00'?>" />
				<lable for="contact_name" id="errorCompanyName" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Header Color Name field is required</div></lable>
			</td>
			<td rowspan="3" width="50%">
				<span class="header_setting" style="color: #<?php echo isset($formData['header_text_color'])? $formData['header_text_color']:'#66ff00'?>;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: <?php echo isset($formData['header_font_size'])?$formData['header_font_size']:'10'?>px;margin: 4px 2px;cursor: pointer;">Header Text</button>
			</td>
			
		</tr>
		<tr>
			<td valign="top" align="left" width="50%">Header Background Color<span class="req">*</span></td>
			<td align="left">
				<input name="header_bg_color" type="text" class="input_small" onchange="setSetting('header')" id="header_bg_color"  value="<?php echo isset($header_bg_color[0])?$header_bg_color[0]:'66ff00'?>" />
				<!-- <input name="header_bg_color1" type="text" class="input_small1 header_bg_color1" onchange="setSetting('header')" id="header_bg_color1"  value="<          ?php echo isset($header_bg_color[1])?$header_bg_color[1]:'66ff00'?>" />
				<input name="header_bg_color2" type="text" class="input_small1 header_bg_color2" onchange="setSetting('header')" id="header_bg_color2"  value="<           ?php echo isset($header_bg_color[2])?$header_bg_color[2]:'66ff00'?>" /> -->
				<lable for="contact_name" id="errorCompanyName" generated="true" class="error" style="display:none;"><div class="error-edit-profile"></div></lable>
			</td>
		</tr>
		<tr>
			<td valign="top" align="left" width="50%">Header Font Size<span class="req"></span></td>
			<td align="left">
				<input name="header_font_size" type="text" class="input_small header_color"  onkeyup="setSetting('header')" id="header_font_size"  value="<?php echo isset($formData['header_font_size'])?$formData['header_font_size']:'10'?>" />
				<lable for="contact_name" id="errorCompanyName" generated="true" class="error" style="display:none;"><div class="error-edit-profile">/div></lable>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" align="left" width="50%">Navigation Text Color<span class="req">*</span></td>
			<td align="left">
				<input name="navigation_text_color" type="text" class="input_small header_color" onchange="setSetting('navigation')" id="navigation_text_color"  value="<?php echo isset($formData['navigation_text_color'])? $formData['navigation_text_color']:'#66ff00'?>" />
				<lable for="contact_name" id="errorCompanyName" generated="true" class="error" style="display:none;"><div class="error-edit-profile">The Header Color Name field is required</div></lable>
			</td>
			<td rowspan="3" width="50%">
				<span class="navigation_setting" style="color: #<?php echo isset($formData['navigation_text_color'])? $formData['navigation_text_color']:'#66ff00'?>;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: <?php echo isset($formData['navigation_font_size'])?$formData['navigation_font_size']:'10'?>px;margin: 4px 2px;cursor: pointer;">Navigation Text</button>
			</td>
			
		</tr>
		<tr>
			<td valign="top" align="left" width="50%">Navigation Background Color<span class="req">*</span></td>
			<td align="left">
				<input name="navigation_bg_color1" type="text" class="input_small navigation_bg_color1" onchange="setSetting('navigation')" id="navigation_bg_color1"  value="<?php echo isset($header_bg_color)?$navigation_bg_color[0]:'66ff00'?>" />
				<!-- <input name="navigation_bg_color2" type="text" class="input_small2 navigation_bg_color2" onchange="setSetting('navigation')" id="navigation_bg_color2"  value="< ?php echo isset($header_bg_color)?$navigation_bg_color[1]:'66ff00'?>" /> -->
			</td>
		</tr>
		<tr>
			<td valign="top" align="left" width="50%">Navigation Font Size<span class="req"></span></td>
			<td align="left">
				<input name="navigation_font_size" type="text" class="input_small header_color"  onkeyup="setSetting('navigation')" id="navigation_font_size"  value="<?php echo isset($formData['navigation_font_size'])?$formData['navigation_font_size']:'10'?>" />
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td valign="top" align="left">Button Text Color</td>
			<td align="left">
				<input name="button_text_colour" type="text" class="input_small" id="button_text_colour" onchange="setSetting('button')"  value="<?php echo isset($formData['button_text_colour'])?$formData['button_text_colour']:'66ff00'?>"/>
			</td>
			<td rowspan="3" width="50%">
				<button class="button_setting">Button Text</button>
			</td>
		</tr> 
		<tr>
			<td valign="top" align="left">Button Background Color</td>
			<td align="left">
				<input name="button_bg_colour1" type="text" class="input_small" id="button_bg_colour1" onchange="setSetting('button')" placeholder = "#FF0000" value="<?php echo isset($formData['button_bg_colour'])?$button_bg_colour[0]:'66ff00'?>"/>
				<!-- <input name="button_bg_colour2" type="text" class="input_small2" id="button_bg_colour2" onchange="setSetting('button')" placeholder = "#FF0000" value="<   ?php echo isset($formData['button_bg_colour'])?$button_bg_colour[1]:'66ff00'?>"/> -->
			</td>
		</tr>  
		<tr>
			<td valign="top" align="left">Button Font Size</td>
			<td align="left">
				<input name="button_font_size" type="text" class="input_small" id="button_font_size" onkeyup="setSetting('button')"  value="<?php echo isset($formData['button_font_size'])?$formData['button_font_size']:''?>" placeholder = "10"  />
			</td>
			
		</tr>        
		<tr>
			<td>&nbsp;</td>
			<td>
            <input type="hidden" name="id" id="id" value="<?php echo isset($formData['company_id'])?$formData['company_id']:0; ?>" />
			<input type="button" name="button" class="submit_btn" id="button" style="background-image:url(images/save.png);font-size:0px; border:none; width:111px;float:left;" onclick="editSettingData();" />
				&nbsp;&nbsp;&nbsp; <a id="ancor" href="javascript:closePopup(300);" onclick="yes"> <img src="images/back_btn.png" style="border:none; width:111px;" /> </a>
			</td>
		</tr>
	</table>
	</form>
	<br clear="all" />
</fieldset>

<?php } ?>