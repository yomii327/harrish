<div id="mainContainer">
	<fieldset class="emailContainer">
		<legend>Email Report</legend>
		<table width="50%" border="0" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td width="16%" align="right">To</td><td>&nbsp;:&nbsp;</td>
				<td width="81%">
					<textarea name="toEmail" id="toEmail" cols="30" rows="3"></textarea>
				</td>
			</tr>
			<tr>
				<td align="right">Cc</td><td>&nbsp;:&nbsp;</td>
				<td><input type="text" name="ccEmail" id="ccEmail" size="40" /></td>
			</tr>
			<tr>
				<td align="right">Bcc</td><td>&nbsp;:&nbsp;</td>
				<td><input type="text" name="bccEmail" id="bccEmail" size="40" /></td>
			</tr>
			<tr>
				<td align="right">Subject</td><td>&nbsp;:&nbsp;</td>
				<td><input type="text" name="subEmail" id="subEmail" size="40" value="" /></td>
			</tr>
			<tr>
				<td align="right">Attachment</td><td>&nbsp;:&nbsp;</td>
				<td><div id="attachEmail">markupImage.png</div></td>
			</tr>
			<tr>
				<td align="right" valign="top">Message</td><td>&nbsp;:&nbsp;</td>
				<td><textarea name="descEmail" id="descEmail" cols="30" rows="15"></textarea></td>
			</tr>
			<tr>
				<td align="center" colspan="3">
					<img onClick="sendEmailMarkup();" src="images/send.png" style="float:left;" />
					<img onClick="closePopup(300);" src="images/cancel.png" style="float:left;margin-left:50px;" />
				</td>
			</tr>
		</table>
	</fieldset>
</div>