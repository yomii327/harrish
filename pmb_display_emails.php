<?php
//Get message type/folder type.
if(explode("_", $_REQUEST['folderType'])){
	$folderType = end(explode("_", $_REQUEST['folderType']));
}else{
	$folderType = $_REQUEST['folderType'];
}	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head></head>
<body id="dt_example">
<div id="container">
    <h3 style="color:#000000;">Emails</h3>
	<?php $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Request For Information', 'Site Instruction', 'Architect Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Adjustment', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'Meetings');?>
		Message Type : 
	<select name="ajaxmessageType" id="ajaxmessageType" style="width: 350px; height:28px; padding-top:4px; background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:2px; margin-left: 10px;">
		<?php for($i=0; $i<sizeof($msgType); $i++){?>
			<option value="<?php echo $msgType[$i];?>" <?php if($msgType[$i] === $folderType){echo 'selected="selected"';} ?>><?php echo $msgType[$i];?></option>
		<?php }?>
	</select> 
    <div class="ajaxEmailHolder" style="width:870px; float:left;margin-top:5px;" >
        <table cellpadding="0" cellspacing="0" border="0" class="display" id="inboxData" width="100%">
            <thead>
                <tr>
					<th width="5%">&nbsp;</th>
                    <th width="5%">To</th>
                    <th width="5%">CC</th>
                    <th width="5%">Cros No</th>
                    <th width="50%">Subject</th>
                    <th width="5%">Time</th>
                    <th width="5%">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="6" class="dataTables_empty">Loading data from server</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<br clear="all" />
</body>
</html>
