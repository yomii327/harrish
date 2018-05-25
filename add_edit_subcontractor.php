<?php 
if((!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){?>
<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
include_once("includes/commanfunction.php");
$object = new COMMAN_Class();
?>
<!-- Ajax Post -->

<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/edit_project.js?1"></script>

<!-- Ajax Post -->
<style>
.list {
	border: 1px solid;
	max-height: 150px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	padding: 5px;
	overflow: auto;
}
div.nicEdit-main, div.nicEdit-main div, div.nicEdit-main span {
	color: #FFFFFF !important;
	background-color: transparent !important;
}
.innerDiv {
	float: left;
	border: 1px solid red;
	width: 120px;
	height: 120px;
	margin: 0px 0px 20px 3px;
}
label.filebutton {
	width: 120px;
	height: 40px;
	overflow: hidden;
	position: relative;
}
label input {
	cursor: pointer;
	font-size: 7px;
	left: -2px;
	line-height: 0;
	margin: 0;
	opacity: 0;
	padding: 0;
	position: absolute;
	top: -2px;
	z-index: 999;
	filter: alpha(opacity=0);
}
.input_small { padding: 0 5px 0 18px;}
.failure_r {
    /*background-color: #eefcee;*/
    background-repeat: no-repeat;
    border: 1px solid red;
    border-radius: 5px;
    color: red;
    font-weight: bold;
    height: 35px;
    margin-bottom: 0;
    padding-left: 40px;
    text-align: left;
    text-shadow: none;
}
.failure_r p{
	margin-top: 5px !important;
}
</style>
<?php 
if(isset($_REQUEST['id']) && $_REQUEST['id'] != '') {
	$id = $_REQUEST['id'];
	$getData = $object->selQRYMultiple('*', 'organisations_subcontractor_database', 'is_deleted = 0 AND id = '.$_REQUEST['id']);	
	$formData = isset($getData[0])?$getData[0]:'';
	$label_text = 'Update';
} else {
	$label_text = 'Add';
}

?>
<div id="middle" style="padding-bottom:80px;">
  <div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
  <div id="apply_now">
    <form action="ajax_reply.php" method="post"  enctype="multipart/form-data" name="edit_subcontractor" id="edit_subcontractor" >
      <div class="content_container">
        <div class="content_left">
          <div class="content_hd1" style="margin-top:-50px\9;font-size: 24px;color: #FFFFFF;"><span style="float:left;"><img align="absmiddle" width="28" hspace="5" height="29" src="images/database.png"></span> <?php echo $label_text;?> Subcontractor</div>
          <div class="signin_form">
          <div class="failure_r" id="messageDisplayDiv" style="display:none; float:left; width:500px; margin-left:20px; height:30px; margin-bottom:5px;">
				<p id="message"></p>
			</div>
            <table width="600" border="0" align="left" cellpadding="0" cellspacing="15">
              <tr>
                <td valign="top">Trade <span class="req">*</span></td>
                <td><input name="trade" type="text" class="input_small" id="trade" value="<?php echo isset($formData['trade'])?$formData['trade']:''?>"/></td>
              </tr>
              <tr>
                <td nowrap="nowrap" valign="top">Strategic <span class="req">*</span></td>
                <td><select name="strategic" id="strategic" class="select_box" style="margin-left:0px;">
                    <option value="" selected="selected">select</option>
                    <?php $strategicArr = array('Yes', 'No');
								foreach($strategicArr as $key=>$strategicType){?>
					                    <option value="<?=$strategicType?>" <?php if(isset($formData['strategic']) && $formData['strategic'] == $strategicType){ echo 'selected';}?>>
					                    <?=$strategicType?>
					                    </option>
                    <?php }?>
                  </select></td>
              </tr>
              <tr>
	                <td valign="top">Company Name <span class="req">*</span></td>
	                <td><input name="company_name" type="text" class="input_small" id="company_name" value="<?php echo isset($formData['company_name'])?$formData['company_name']:''?>" /></td>
              </tr>
               <tr>
	                <td valign="top">Contact Name <span class="req">*</span></td>
	                <td><input name="contact_name" type="text" class="input_small" id="contact_name" value="<?php echo isset($formData['contact_name'])?$formData['contact_name']:''?>" /></td>
              </tr>
               <tr>
	                <td valign="top">Contact Position <span class="req">*</span></td>
	                <td><input name="contact_position" type="text" class="input_small" id="contact_position" value="<?php echo isset($formData['contact_position'])?$formData['contact_position']:''?>" /></td>
              </tr>
               <tr>
	                <td valign="top">Contact Type <span class="req">*</span></td>
	                <td>
		                <select name="contact_type" id="contact_type" class="select_box" style="margin-left:2px;">
			                <option value="" selected="selected">select</option>
		                    <option value="Estimating" <?php if(isset($formData['contact_type']) && $formData['contact_type'] == 'Estimating'){ echo 'selected';}?>>Estimating</option>
	                  </select>
                  </td>
              </tr>
              <tr>
                <td nowrap="nowrap" valign="top">Company Phone <span class="req">*</span></td>
                <td><input name="company_phone" type="text" class="input_small" id="company_phone" value="<?php echo isset($formData['company_phone'])?$formData['company_phone']:''?>" /></td>
              </tr>
              <tr>
                <td width="180" valign="top">Company Fax</td>
                <td width="245"><input name="company_fax" type="text" class="input_small" id="company_fax" value="<?php echo isset($formData['company_fax'])?$formData['company_fax']:''?>" /></td>
              </tr>
               <tr>
                <td width="180" valign="top">Phone</td>
                <td width="245"><input name="phone" type="text" class="input_small" id="phone" value="<?php echo isset($formData['phone'])?$formData['phone']:''?>" /></td>
              </tr>
              <tr>
                <td width="180" valign="top">Street Address</td>
                <td width="245"><input name="street_address" type="text" class="input_small" id="street_address" value="<?php echo isset($formData['street_address'])?$formData['street_address']:''?>" /></td>
              </tr>
              <tr>
                <td width="180" valign="top">Suburb</td>
                <td width="245"><input name="suburb" type="text" class="input_small" id="suburb" value="<?php echo isset($formData['suburb'])?$formData['suburb']:''?>" /></td>
              </tr>
               <tr>
                <td width="180" valign="top">City</td>
                <td width="245"><input name="city" type="text" class="input_small" id="city" value="<?php echo isset($formData['city'])?$formData['city']:''?>" /></td>
              </tr>
              <tr>
                <td valign="top">State <span class="req">*</span></td>
                <td><input name="state" type="text" class="input_small" id="state" value="<?php echo isset($formData['state'])?$formData['state']:''?>" /></td>
              </tr>
              <tr>
                <td nowrap="nowrap" valign="top">Postcode <span class="req">*</span></td>
                <td><input name="postcode" type="text" class="input_small" id="postcode" value="<?php echo isset($formData['postcode'])?$formData['postcode']:''?>" /></td>
              </tr>
              <tr>
                <td valign="top">Country <span class="req">*</span></td>
                <td><select name="country" id="country" class="select_box" style="margin-left:2px;">
                    <option value="Australia" <?php if(isset($formData['country']) && $formData['country'] == 'Australia'){ echo 'selected';}?>>Australia</option>
                    <option value="Singapore" <?php if(isset($formData['country']) && $formData['country'] == 'Singapore'){ echo 'selected';}?>>Singapore</option>
                  </select></td>
              </tr>
              <tr>
                <td valign="top">Email Address</td>
                <td><input name="email_address" type="text" class="input_small" id="email_address" value="<?php echo isset($formData['email_address'])?$formData['email_address']:''?>" /></td>
              </tr>
              <tr>
                <td valign="top">Company List(s)</td>
                <td><input name="company_list" type="text" class="input_small" id="company_list" value="<?php echo isset($formData['company_list'])?$formData['company_list']:''?>" /></td>
              </tr>
              <tr>
                <td valign="top">Contact List(s)</td>
                <td><input name="contact_list" type="text" class="input_small" id="contact_list" value="<?php echo isset($formData['contact_list'])?$formData['contact_list']:''?>" /></td>
              </tr>
              <tr>
                <td valign="top">RFQs (12m)</td>
                <td><input name="rfqs" type="text" class="input_small" id="rfqs" value="<?php echo isset($formData['rfqs'])?$formData['rfqs']:''?>" /></td>
              </tr>
              <tr>
                <td valign="top">Quotes (12m)</td>
                <td><input name="quotes" type="text" class="input_small" id="quotes" value="<?php echo isset($formData['quotes'])?$formData['quotes']:''?>" /></td>
              </tr>
              <tr>
                <td valign="top">Quote Rate (%)</td>
                <td><input name="quote_rate" type="text" class="input_small" id="quote_rate" value="<?php echo isset($formData['quote_rate'])?$formData['quote_rate']:''?>" /></td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>
                 <input type="hidden" value="add_edit_subcontractor" name="sect" id="sect" />
                 <input type="hidden" value="<?php echo isset($formData['id'])?$formData['id']:''?>" name="subcontractor_id"  />
                 <?php if(isset($formData['id']) && $formData['id'] != '') { ?> 
                 		 <input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/update.png); border:none; width:111px;" />
                 	<?php } else {  ?>
                 		 <input name="button" type="submit" class="submit_btn" id="button" value="" style="background-image:url(images/submit.png); border:none; width:111px;" />
                 	<?php } ?>
                 
                  <a href="javascript:void();" onclick="history.back();">
                  <input name="passwordUpdate" type="button" class="submit_btn" id="button" value="" style="background-image:url(images/back_btn.png); width:111px; border:none;">
                  </a></td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
