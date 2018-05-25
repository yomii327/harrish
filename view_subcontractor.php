<?php include('includes/commanfunction.php');
$object = new COMMAN_Class();
if(!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])){ ?>
<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
if(isset($_REQUEST['id']) && $_REQUEST['id'] != '') {
	$id = $_REQUEST['id'];
	$getData = $object->selQRYMultiple('*', 'organisations_subcontractor_database', 'is_deleted = 0 AND id = '.$_REQUEST['id']);	
	$formData = isset($getData[0])?$getData[0]:'';
} 
?>
<style type="text/css">
table.gridtable { border-width: 1px; border-color: #FFF; border-collapse: collapse;}
table.gridtable td { border-width: 1px; padding: 8px; border-style: solid; border-color: #FFF; }
.signin_form{font-size: 16px;}
</style>
<div id="middle" style="padding-bottom:80px;">
	<div id="apply_now">
		<div class="content_container">
			<div class="content_left">
				<div class="content_hd1" style="margin-top:-50px\9;font-size: 24px;color: #FFFFFF;"><span style="float:left;"><img align="absmiddle" width="28" hspace="5" height="29" src="images/database.png"></span> Subcontractor Detail</div>
				<div class="signin_form" style="margin-left:70px;width:670px">
					<div style="border:1px solid;float:left;">
					 <table width="800" border="0" align="left" cellpadding="0" cellspacing="15">
			              <tr>
			                <td valign="top">Trade </td>
			                <td><?php echo isset($formData['trade'])?$formData['trade']:''?></td>
			              </tr>
			              <tr>
			                <td nowrap="nowrap" valign="top">Strategic </td>
			                <td><?php echo isset($formData['strategic'])?$formData['strategic']:''?></td>
			              </tr>
			              <tr>
				                <td valign="top">Company Name </td>
				                <td><?php echo isset($formData['company_name'])?$formData['company_name']:''?></td>
			              </tr>
			               <tr>
				                <td valign="top">Contact Name </td>
				                <td><?php echo isset($formData['contact_name'])?$formData['contact_name']:''?></td>
			              </tr>
			               <tr>
				                <td valign="top">Contact Position </td>
				                <td><?php echo isset($formData['contact_position'])?$formData['contact_position']:''?></td>
			              </tr>
			               <tr>
				                <td valign="top">Contact Type </td>
				                <td><?php echo isset($formData['contact_type'])?$formData['contact_type']:''?></td>
			              </tr>
			              <tr>
			                <td nowrap="nowrap" valign="top">Company Phone </td>
			                <td><?php echo isset($formData['company_phone'])?$formData['company_phone']:''?></td>
			              </tr>
			              <tr>
			                <td width="133" valign="top">Company Fax</td>
			                <td width="252"><?php echo isset($formData['company_fax'])?$formData['company_fax']:''?></td>
			              </tr>
			               <tr>
			                <td width="133" valign="top">Phone</td>
			                <td width="252"><?php echo isset($formData['phone'])?$formData['phone']:''?></td>
			              </tr>
			              <tr>
			                <td width="133" valign="top">Street Address</td>
			                <td width="252"><?php echo isset($formData['street_address'])?$formData['street_address']:''?></td>
			              </tr>
			              <tr>
			                <td width="133" valign="top">Suburb</td>
			                <td width="252"><?php echo isset($formData['suburb'])?$formData['suburb']:''?></td>
			              </tr>
			               <tr>
			                <td width="133" valign="top">City</td>
			                <td width="252"><?php echo isset($formData['city'])?$formData['city']:''?></td>
			              </tr>
			              <tr>
			                <td valign="top">State </td>
			                <td><?php echo isset($formData['state'])?$formData['state']:''?></td>
			              </tr>
			              <tr>
			                <td nowrap="nowrap" valign="top">Postcode </td>
			                <td><?php echo isset($formData['postcode'])?$formData['postcode']:''?></td>
			              </tr>
			              <tr>
			                <td valign="top">Country </td>
			                <td><?php echo isset($formData['country'])?$formData['country']:''?></td>
			              </tr>
			              <tr>
			                <td valign="top">Email Address</td>
			                <td><?php echo isset($formData['email_address'])?$formData['email_address']:''?></td>
			              </tr>
			              <tr>
			                <td valign="top">Company List(s)</td>
			                <td><?php echo isset($formData['company_list'])?$formData['company_list']:''?></td>
			              </tr>
			              <tr>
			                <td valign="top">Contact List(s)</td>
			                <td><?php echo isset($formData['contact_list'])?$formData['contact_list']:''?></td>
			              </tr>
			              <tr>
			                <td valign="top">RFQs (12m)</td>
			                <td><?php echo isset($formData['rfqs'])?$formData['rfqs']:''?></td>
			              </tr>
			              <tr>
			                <td valign="top">Quotes (12m)</td>
			                <td><?php echo isset($formData['quotes'])?$formData['quotes']:''?></td>
			              </tr>
			              <tr>
			                <td valign="top">Quote Rate (%)</td>
			                <td><?php echo isset($formData['quote_rate'])?$formData['quote_rate']:''?></td>
			              </tr>
			              <tr>
			                <td>&nbsp;</td>
			                <td>
			                  <a href="javascript:void();" onclick="history.back();">
			                  <input name="passwordUpdate" type="button" class="green_small" id="button" value="Back">
			                  </a></td>
			              </tr>
			            </table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
