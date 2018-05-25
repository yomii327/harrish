<?php
//ini_set('display_errors', 1);
$userId = $_SESSION['ww_builder_id'];

if(isset($_POST['submit'])){
	if(!empty($_POST['title']) && !empty($_POST['date']) && !empty($_POST['time'])){
		$meetingDate = $object->dateChanger("/", "-", $_POST['date']); //date('Y-m-d', strtotime($_POST['date']));
		$recurring_meeting = (isset($_POST['recurring_meeting']) && !empty($_POST['recurring_meeting']))?1:0;		
		if(isset($_POST['meetingId']) && $_POST['meetingId']==0){
			$newMeetingId = $object->getUniqueIDForPMB("meeting_id");
			$insert_meetingDetails = "INSERT INTO meeting_details SET
									meeting_id = '".$newMeetingId."',
									project_id = '".$_SESSION['idp']."',
									location = '".addslashes(trim($_POST['projectLocation']))."',																		
									meeting_number = '".addslashes(trim($_POST['meetingNumber']))."',
									meeting_location = '".addslashes(trim($_POST['meetingLocation']))."',				
									title = '".addslashes(trim($_POST['title']))."',
									description = '".addslashes(trim($_POST['description']))."',
									date = '".addslashes(trim($meetingDate))."',
									time = '".addslashes(trim($_POST['time']))."',
									number_days = '".addslashes(trim($_POST['number_days']))."',
									recurring_meeting = '".addslashes($recurring_meeting)."',
									type = 'Meeting Minutes',
									last_modified_date = NOW(),
									original_modified_date = NOW(),
									last_modified_by = '".$userId."',
									created_date = NOW(),
									created_by = '".$userId."'";
			mysql_query($insert_meetingDetails);
			$meetingId = $newMeetingId; //mysql_insert_id();	
			$_SESSION['meeting_add'] = 'Meeting added successfully.';
			
		}else{
			$meetingId = $_POST['meetingId'];
		 	$update_meetingDetails = "UPDATE meeting_details SET
									location = '".addslashes(trim($_POST['projectLocation']))."',												
									meeting_number = '".addslashes(trim($_POST['meetingNumber']))."',
									meeting_location = '".addslashes(trim($_POST['meetingLocation']))."',				
									title = '".addslashes(trim($_POST['title']))."',
									description = '".addslashes(trim($_POST['description']))."',
									date = '".addslashes(trim($meetingDate))."',
									time = '".addslashes(trim($_POST['time']))."',
									number_days = '".addslashes(trim($_POST['number_days']))."',
									recurring_meeting = '".addslashes($recurring_meeting)."',
									type = 'Meeting Minutes',
									last_modified_date = NOW(),
									original_modified_date = NOW(),
									last_modified_by = '".$userId."'
									WHERE 
									meeting_id = '".$meetingId."'";
			mysql_query($update_meetingDetails);		
			$_SESSION['meeting_add'] = 'Meeting updated successfully.';
		}

		// Attendee section
		if (isset($_POST["attendee"]) && !empty($_POST["attendee"])){
			foreach ($_POST["attendee"] as $k=>$v){
				$meetAttendId = $_POST['meetAttendId'][$k];
				$attendee = mysql_real_escape_string(trim($_POST['attendee'][$k]));
				$companyName = mysql_real_escape_string(trim($_POST['companyName'][$k]));
	
				if(!empty($attendee) && !empty($companyName) && $meetAttendId == 0){
					$insert_attendee = "INSERT INTO meeting_attendees SET
											project_id = '".$_SESSION['idp']."',
											meeting_id = '".$meetingId."',
											attendees_name = '".addslashes($attendee)."',
											company_name = '".addslashes($companyName)."',
											last_modified_date = NOW(),
											original_modified_date = NOW(),
											last_modified_by = '".$userId."',
											created_date = NOW(),
											created_by = '".$userId."',
											is_deleted = '0'";
					mysql_query($insert_attendee);
					
				}else if(!empty($attendee) && !empty($companyName) && $meetAttendId > 0){
					$update_attendee = "UPDATE meeting_attendees SET
											attendees_name = '".addslashes($attendee)."',
											company_name = '".addslashes($companyName)."',
											last_modified_date = NOW(),
											original_modified_date = NOW(),
											last_modified_by = '".$userId."'
											WHERE 
											attendee_id = '".$meetAttendId."' ";
					mysql_query($update_attendee);
				}
			}
		}

		// Item section
		if (isset($_POST["item"]) && !empty($_POST["item"])){
			foreach ($_POST["item"] as $k=>$v){
				$itemId = $_POST['itemId'][$k];
				$item = mysql_real_escape_string(trim($_POST['item'][$k]));
				$detail = mysql_real_escape_string(trim($_POST['detail'][$k]));
				$byWhom = mysql_real_escape_string(trim($_POST['byWhom'][$k]));
				$byWhen = !empty($_POST['byWhen'][$k])?$object->dateChanger("/", "-", $_POST['byWhen'][$k]):''; 
				$comments = mysql_real_escape_string(trim($_POST['comments'][$k]));				
				$isHighlighted = (isset($_POST['isHighlighted'][$k]) && !empty($_POST['isHighlighted'][$k]))?1:0;								
	
				if((!empty($item) || !empty($detail) || !empty($byWhom) || !empty($byWhen) || !empty($comments) ) && $itemId == 0){
					$newItemId = $object->getUniqueIDForPMB("item_id");
					$insert_item = "INSERT INTO meeting_item_details SET
											item_id = '".$newItemId."',
											project_id = '".$_SESSION['idp']."',
											meeting_id = '".$meetingId."',
											item = '".addslashes($item)."',
											details = '".addslashes($detail)."',
										 	by_whom = '".addslashes($byWhom)."',
											by_when = '".addslashes($byWhen)."',
											comments = '".addslashes($comments)."',
											is_highlighted = '".addslashes($isHighlighted)."',
											last_modified_date = NOW(),
											original_modified_date = NOW(),
											last_modified_by = '".$userId."',
											created_date = NOW(),
											created_by = '".$userId."',
											is_deleted = '0'";
					mysql_query($insert_item);
					
				}else if((!empty($item) || !empty($detail) || !empty($byWhom) || !empty($byWhen) || !empty($comments) ) && $itemId > 0){
					$update_item = "UPDATE meeting_item_details SET
											item = '".addslashes($item)."',
											details = '".addslashes($detail)."',
										 	by_whom = '".addslashes($byWhom)."',
											by_when = '".$byWhen."',
											comments = '".addslashes($comments)."',
											is_highlighted = '".addslashes($isHighlighted)."',
											last_modified_date = NOW(),
											original_modified_date = NOW(),
											last_modified_by = '".$userId."'
											WHERE 
											item_id = '".$itemId."' ";
					mysql_query($update_item);
				}
			}
		}
		
		?>
		<script language="javascript" type="text/javascript">
window.location.href="<?="?sect=meeting&folderType=".$_GET['folderType']; ?>";
</script>
	<?php
		header('location:?sect=meeting&folderType=Meeting');
	}else{
		$_SESSION['meeting_add_err'] = 'Meeting not added.';
	}
}

?>
<script type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<link href="css/email.css" rel="stylesheet" type="text/css" />
<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
	
	var validator = $("#pmb_agenda_template_form").validate({
	rules:
	{  
		projectLocation:
	   {
	   		required: true
	   },
	   title:
	   {
	   		required: true
	   },
	   date:
	   {
	   		required: true
	   },
	   time:
	   {
	   		required: true
	   }	   	   
	},
	messages:
	{
		projectLocation:
		{
			required: '<div class="error-edit-profile">The project location field is required</div>'
		},
		title:
		{
			required: '<div class="error-edit-profile">The title field is required</div>'
		},
		date:
		{
			required: '<div class="error-edit-profile">The date field is required</div>'
		},
		time:
		{
			required: '<div class="error-edit-profile">The time field is required</div>'
		},				
		debug:true
	}
	
	});
	jQuery.validator.addMethod("alpha", function( value, element ) {
		return this.optional(element) || /^[a-zA-Z ]+$/.test(value);
	}, "Please use only alphabets (a-z or A-Z)");
	jQuery.validator.addMethod("numeric", function( value, element ) {
		return this.optional(element) || /^[0-9]+$/.test(value);
	}, "Please use only numeric values (0-9)");
	jQuery.validator.addMethod("alphanumeric", function( value, element ) {
		return this.optional(element) || /^[a-z A-Z0-9]+$/.test(value);
	}, "You can use only a-z A-Z 0-9 characters");
	jQuery.validator.addMethod("mobile", function( value, element ) {
		return this.optional(element) || /^[ 0-9+-]+$/.test(value);
	}, "You can use only 0-9 - + characters");
	jQuery.validator.addMethod("login", function( value, element ) {
		return this.optional(element) || /^[A-Za-z0-9_.]+$/.test(value);
	}, "You can use only a-z A-Z 0-9 _ and . characters");
	
});
// JavaScript Document// JavaScript Document

window.onload = function(){
<?php $i=0; if(isset($meetingItemData) && !empty($meetingItemData)){
		foreach($meetingItemData as $key=>$meetingItem){	
			echo 'new JsDatePick({
						useMode:2,
						target:"byWhen_'.$i.'",
						dateFormat:"%d/%m/%Y"
					});';
			$i++;
		} 
	}else{
?>
	new JsDatePick({
		useMode:2,
		target:"byWhen_0",
		dateFormat:"%d/%m/%Y"
	});
<?php } ?>
	new JsDatePick({
		useMode:2,
		target:"date",
		dateFormat:"%d/%m/%Y"
	});	
};


// Add more attendee items
var fields_add_page = 0;  var bid=0;
function addMoreItemInAttendee(){ 
	fields_add_page = parseInt(document.getElementById("totalAttendee").value);
	if(fields_add_page==0){ bid=1; }else{ bid = fields_add_page+1; }
	
	 var id = "input" + fields_add_page;
	 var divid = "divid_" + fields_add_page;
	 var input_id = "input_id_" + fields_add_page;
	 var dropdown_id = "dropdown_id_" + fields_add_page;
	 var companyName_id = "companyName_" + fields_add_page;	 
	 var tr_id = 'tr_'+fields_add_page;
	
	 var drop_down = "<input type='hidden' name='meetAttendId["+bid+"]' id='meetAttendId[]' value='0' /><div id='custmCMP_"+fields_add_page+"'>";
	//drop_down = "<input name='attendee["+bid+"]' type='text' class='input_small' id='"+dropdown_id+"' style='width:240px;' value='' size='30' maxlength='50' />";	
	var menuData = $("#dropdown_id_0").html();
	drop_down = "<select name='attendee["+bid+"]' id='"+dropdown_id+"' style='width:240px;' class='chzn-select chzn-custom-value'>"+(menuData.replace('selected="selected"', ""))+"</select>";
	$("table#attendee_table").append("<tr id='"+tr_id+"'><td><a href='javascript:void(0);' onclick='return removeMoreItemInAttendee("+fields_add_page+", 0)' ><img src='images/inspectin_delete.png'></a></td><td>"+drop_down+"</td><td><input name='companyName["+bid+"]' type='text' class='input_small' id='"+companyName_id+"' style='width:240px;' value='' size='30' maxlength='50' /></td></tr>");

   
	fields_add_page += 1;	
	document.getElementById("totalAttendee").value = fields_add_page;

	//Chosen section
	var config = {
	  '.chzn-select'           : {},
	  '.chzn-select-deselect'  : {allow_single_deselect:false},
	  '.chzn-select-width'     : {width:"95%"}
	}
	for (var selector in config) {
	  $(selector).chosen(config[selector]);
	}	
	
}

// Remove attendee item
function removeMoreItemInAttendee(id,val){
	var t=confirm("Do you want to delete this record?");
	if(t==true){
		if(val>0){
			$.ajax({
				url:  'meeting_section.php',
				type: "POST",
				data:  {"rmAttendeeId":val},
				success: function (res) {
				}
			});	
		}
		$('table#attendee_table tr#tr_'+id+'').remove();
		//$('#trp_'+id+'').remove();
	}
}

// Add more items in table
var add_row = 0;  var tid=0;
function addMoreItemInTable(){ 
	add_row = parseInt(document.getElementById("totalTableRow").value);
	if(add_row==0){ tid=1; }else{ tid = add_row+1; }
	
	var tr_id = 'tr_'+add_row;	
	var item_id = "item_" + add_row;
	var detail_id = "detail_" + add_row;
	var byWhom_id = "byWhom_" + add_row;
	var byWhen_id = "byWhen_" + add_row;	 
	var comments_id = "comments_" + add_row;
	var isHighlighted_id = "isHighlighted_" + add_row;	 

	$("table#item_table").append("<tr id='"+tr_id+"'><td><a href='javascript:void(0);' onclick='return removeMoreItemInTable("+add_row+", 0)' ><img src='images/inspectin_delete.png'></a></td><td><input type='hidden' name='itemId["+tid+"]' id='itemId[]' value='0' /><div id='custmCMP_"+add_row+"'><input name='item["+tid+"]' type='text' class='input_small' id='"+item_id+"' style='width:50px;' value='' /></td><td><input name='detail["+tid+"]' type='text' class='input_small' id='"+detail_id+"' style='width:240px;' value='' /></td><td><input name='byWhom["+tid+"]' type='text' class='input_small' id='"+byWhom_id+"' style='width:140px;' value='' /></td><td><input name='byWhen["+tid+"]' type='text' class='input_small' id='"+byWhen_id+"' style='width:140px;' value='' readonly /></td><td><input name='comments["+tid+"]' type='text' class='input_small' id='"+comments_id+"' style='width:240px;' value='' /></td><td><input name='isHighlighted["+tid+"]' type='checkbox' class='input_small' id='"+byWhen_id+"' style='width:20px;' value='1' /></td></tr>");
   
	add_row += 1;	
	document.getElementById("totalTableRow").value = add_row;
	new JsDatePick({
		useMode:2,
		target:byWhen_id,
		dateFormat:"%d/%m/%Y"
	});
}

// Remove items in table
function removeMoreItemInTable(id,val){
	var t=confirm("Do you want to delete this record?");
	if(t==true){	
		if(val>0){
			$.ajax({
				url:  'meeting_section.php',
				type: "POST",
				data:  {"rmItemId":val},
				success: function (res) {
				}
			});	
		}
		$('table#item_table tr#tr_'+id+'').remove();
		//$('#trp_'+id+'').remove();
	}
}
</script>
<style>
.input_small{
	height:25px;
	background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%); 
	border: 1px solid #AAAAAA; 
	padding-left:5px; 
}
#item_table .input_small{
	border: 0px solid #AAAAAA; 
	margin-left: 0px;
}
.input_small1 {	height:25px;
	background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%); 
	border: 1px solid #AAAAAA; 
	padding-left:5px; 
}
#item_table input {	border: 1px solid #666666 !important; background:#FFF !important; }
#attendee_table .input_small{ height:22px}
</style>

  <!--h1 style="color:#000; margin-left:10px;">MEETING MINUTES</h1-->
 <form action="" name="meetingForm" id="meetingForm" method="post" style="clear:both; <?php echo (isset($meetingId) && !empty($meetingId))?"display:none;":'';?>">
    <table width="100%" border="0" style="" cellpadding="5" cellspacing="5">
      <tr>
        <td width="13%" align="left" valign="top"><label for="email"><strong>Template Type </strong></label></td>
        <td colspan="3" align="left" valign="top"><select name="templateType" id="templateType" style="width: 322px; height:28px; padding-top:4px; background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%); border: 1px solid #AAAAAA; padding-left:2px; ">
           <option value="" >Select</option>
            <option value="Agenda" <?php echo (isset($_SESSION['templateType']) &&  $_SESSION['templateType']=="Agenda")?"selected":""; ?> >Agenda</option>
            <option value="Meeting Minutes" <?php echo (isset($_SESSION['templateType']) &&  $_SESSION['templateType']=="Meeting Minutes")?"selected":""; ?> >Meeting Minutes</option>
          </select><!--input type="submit" value="Go" name="submit" id="sendMessage" style="height: 30px;     margin-left: 10px; width: 50px;" /--></td>
      </tr>
      </table>
  </form> 
<br />
 <form action="" method="post" enctype="multipart/form-data" name="pmb_meeting_minutes_template_form" id="pmb_meeting_minutes_template_form" >      
 <table width="98%" border="0" style=" border:1px solid #333; margin:10px;" cellpadding="5" cellspacing="5">
  <tr>
    <td width="24%" height="27" valign="top" ><strong>Project Name </strong></td>
    <td colspan="3" valign="top"><?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name');?></td>
    </tr>  
  <tr>
    <td width="24%" height="27" valign="top" ><strong>Project Location <span class="req">*</span></strong></td>
    <td colspan="3" valign="top"><input name="projectLocation" type="text" class="input_small1" id="projectLocation" style="width:310px; " value="<?php echo $meetingData[0]['location']; ?>" size="30" maxlength="50"/></td>
    </tr>   
  <tr>
    <td width="16%" height="27" valign="top" ><strong>Meeting number</strong></td>
    <td colspan="3" valign="top"><input name="meetingNumber" type="text" class="input_small1" id="meetingNumber" style="width:120px; " value="<?php echo $meetingData[0]['meeting_number']; ?>" size="30" maxlength="50"/></td>
    </tr>      
  <tr>
    <td height="27" valign="top" ><strong>Meeting Location </strong></td>
    <td colspan="3" valign="top"><input name="meetingLocation" type="text" class="input_small1" id="meetingLocation" style="width:310px; " value="<?php echo $meetingData[0]['meeting_location']; ?>" size="" maxlength=""/></td>
  </tr>    
  <tr>
    <td valign="top"><strong>Title <span class="req">*</span></strong></td>
    <td colspan="3" valign="top"><input type="text" size="40" value="<?php echo $meetingData[0]['title']; ?>" id="title" name="title"  class="input_small" style="width: 310px; "/></td>
    </tr>
  <tr>
    <td valign="top"><strong>Description <span class="req">*</span></strong></td>
    <td colspan="3" valign="top"><textarea type="text" size="40" id="description" name="description"  class="input_small" style="width: 306px; height:50px; "><?php echo $meetingData[0]['description']; ?></textarea></td>
    </tr>      
  <tr>
    <td valign="top"><strong>Date  <span class="req">*</span></strong></td>
    <td width="11%" valign="top"><input type="text" size="40" value="<?php echo isset($meetingData[0]['date'])?date('d/m/Y' ,strtotime($meetingData[0]['date'])):''; ?>" id="date" name="date"  class="input_small" style="width: 120px;" readonly/></td>
    <td width="8%" valign="top"><strong>Time  <span class="req">*</span></strong></td>
    <td width="64%" valign="top">
      <select style="width:96px; padding:5px 0px; height:28px" class="input_small" name="time" id="time">
        <?php $curtVal = isset($meetingData[0]['time'])?$meetingData[0]['time']:'';
            for($n=0; $n<24; $n++){ $sel=""; $k="";
                if($n<10){$k = "0".$n.":00"; }else{$k = $n.":00"; }
                if($n<10){$k2 = "0".$n.":30"; }else{$k2 = $n.":30"; }
                if($curtVal==$k){
                    echo "<option value='".$k."' selected='selected'>".$k."</option>";
                    echo "<option value='".$k2."' >".$k2."</option>";
                }elseif($curtVal==$k2){
                    echo "<option value='".$k."' >".$k."</option>";
                    echo "<option value='".$k2."' selected='selected'>".$k2."</option>";
                }else{
                    echo "<option value='".$k."'>".$k."</option>";
                    echo "<option value='".$k2."'>".$k2."</option>";
                }
            }
        ?>
        </select></td>
  </tr>
  <tr>
    <td valign=""><strong>Recurring Meeting:</strong></td>
    <td colspan="3" valign="top"><input name="recurring_meeting" type="checkbox" class="input_small" id="recurring_meeting" style="width:20px; " value="1"<?php echo (isset($meetingData[0]['recurring_meeting']) && $meetingData[0]['recurring_meeting'] == 1)?'checked':''; ?> /></td>    
  </tr> 
  <tr>
    <td valign="top"><strong>Days</strong></td>
    <td colspan="3" valign="top"><input type="text" size="10" value="<?php echo isset($meetingData[0]['number_days'])?$meetingData[0]['number_days']:''; ?>" id="number_days" name="number_days"  class="input_small" style="width: 120px;" /></td>
      </td>
  </tr>  
  <tr>
    <td colspan="4" valign="top"><strong>ATTENDEES:</strong></td>
  </tr>
  <tr>
    <td colspan="4" valign="top">
      <table width="98%%" border="0" cellpadding="2" id="attendee_table" >
        <tr>
          <th width="6%" align="left" valign="top"></th>
          <th width="29%" align="left" valign="top">Name </th>
          <th width="65%" align="left" valign="top">Company</th>
          </tr>
        <?php $i=0;  if(isset($meetingAttendeeData) && !empty($meetingAttendeeData)){
							foreach($meetingAttendeeData as $key=>$meetAttendee){ 	
			?>
        <tr id="tr_<?php echo $i;?>">
          <td><span id="input-id-2" >
            <?php if($i==0){?>
            <a href="javascript:void(0);" id="add_more_link" onclick="addMoreItemInAttendee();"> <img  src="images/inspectin_add.png"  /></a>
            <?php }else{  $rmAttendeeId=isset($meetAttendee['attendee_id'])?$meetAttendee['attendee_id']:0; ?>
            <a onclick="return removeMoreItemInAttendee(<?php echo $i.", ".$rmAttendeeId; ?>)" href="javascript:void(0);"><img src="images/inspectin_delete.png"></a>
            <?php } ?>
            </span></td>
          <td ><input type="hidden" name="meetAttendId[<?php echo $i; ?>]" id="meetAttendId_<?php echo $i;?>" value="<?php echo isset($meetAttendee['attendee_id'])?$meetAttendee['attendee_id']:'';?>" />
            <div id="custmCMP_<?php echo $i; ?>">
              <!--input name="attendee[<?php echo $i; ?>]" type="text" class="input_small" id="dropdown_id_<?php echo $i; ?>" style="width:240px; " value="<?php echo isset($meetAttendee['attendees_name'])?$meetAttendee['attendees_name']:'';?>" size="30" maxlength="50" /-->
              
				<select name="attendee[<?php echo $i; ?>]" id="dropdown_id_<?php echo $i; ?>"style="width:240px;" class="chzn-select chzn-custom-value"> 
	                <option>Select</option>
					<optgroup label="Project users">
					<?php foreach($projectUsers as $puser){	
							$select = ($meetAttendee['attendees_name'] == $puser['user_fullname'])?'selected="selected"':'';
					?>
						<option value="<?php echo $puser['user_fullname']; ?>" <?php echo $select; ?>><?php
						 if(!empty($puser['user_fullname']) && !empty($puser['company_name'])){
							echo strtolower($puser['user_fullname']." ( ".$puser['company_name']." )");
						}elseif(empty($puser['user_fullname']) && !empty($puser['company_name'])){
							echo strtolower("( ".$puser['company_name']." )");
						}else{
							echo strtolower($puser['user_fullname']);
						}?></option><?php  } ?>
					</optgroup>

					<optgroup label="Issued To">
						<?php foreach($projectIssues as $pIssue){
							$select = ($meetAttendee['attendees_name'] == $pIssue['company_name'])?'selected="selected"':'';
						?>
						<option value="<?php echo $pIssue['company_name']; ?>" <?php echo $select; ?>><?php 
						if(!empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])){
							echo strtolower($pIssue['company_name']." ( ".$pIssue['issue_to_name']." )");
						}elseif(empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])){
							echo strtolower("( ".$pIssue['issue_to_name']." )");
						}else{
							echo strtolower($pIssue['company_name']);
						}?></option><?php  } ?>
					</optgroup>
					
                    <optgroup label="Ad hoc (External)">
					<?php foreach($projectAddresBookUsers as $addresBookUsers){	
							$select = ($meetAttendee['attendees_name'] == $addresBookUsers['full_name'])?'selected="selected"':'';
					?>
						<option value="<?php echo $addresBookUsers['full_name'];?>" <?php echo $select; ?>><?php 
						if(!empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])){
							echo strtolower($addresBookUsers['full_name']." ( ".$addresBookUsers['company_name']." )");
						}elseif(empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])){
							echo strtolower("( ".$addresBookUsers['company_name']." )");
						}else{
							echo strtolower($addresBookUsers['full_name']);
						}?></option><?php } ?>
					</optgroup>
                	<?php if(isset($meetAttendee['attendees_name']) && empty($select)) {
						echo '<optgroup label="Custom Value">';
						echo '<option value="'.$meetAttendee['attendees_name'].'" selected="selected">'.$meetAttendee['attendees_name']." ( ".$meetAttendee['company_name']." )".'</option>';
						echo '</optgroup>';
					}?>                    
				</select>              
                
              </div></td>
          <td><input name="companyName[<?php echo $i; ?>]" type="text" class="input_small" id="companyName_<?php echo $i; ?>" style="width:240px; " value="<?php echo isset($meetAttendee['company_name'])?$meetAttendee['company_name']:'';?>" size="30" maxlength="50" /></td>
          </tr>
        <?php $i++; } }else{?>
        <tr>
          <td><a href="javascript:void(0);" id="add_more_link" onclick="addMoreItemInAttendee();"> <img  src="images/inspectin_add.png"  /></a></td>
          <td ><input type="hidden" name="meetAttendId[<?php echo $i; ?>]" id="meetAttendId_0" value="<?php echo isset($SDIssuetoId)?$SDIssuetoId:0;;?>" />
            <div id="custmCMP_0">
              <!--input name="attendee[0]" type="text" class="input_small" id="dropdown_id_0" style="width:240px; " value="" size="30" maxlength="50"/-->
				<select name="attendee[]" id="dropdown_id_0" style="width:240px;" class="chzn-select chzn-custom-value" > 
                	<option>Select</option>
					<optgroup label="Project users">
					<?php foreach($projectUsers as $puser){	
							$select = ($meetAttendee['attendees_name'] == $puser['user_fullname'])?'selected="selected"':'';
					?>
						<option value="<?php echo $puser['user_fullname']; ?>" <?php echo $select; ?>><?php
						 if(!empty($puser['user_fullname']) && !empty($puser['company_name'])){
							echo strtolower($puser['user_fullname']." ( ".$puser['company_name']." )");
						}elseif(empty($puser['user_fullname']) && !empty($puser['company_name'])){
							echo strtolower("( ".$puser['company_name']." )");
						}else{
							echo strtolower($puser['user_fullname']);
						}?></option><?php  } ?>
					</optgroup>

					<optgroup label="Issued To">
						<?php foreach($projectIssues as $pIssue){
							$select = ($meetAttendee['attendees_name'] == $pIssue['company_name'])?'selected="selected"':'';
						?>
						<option value="<?php echo $pIssue['company_name']; ?>" <?php echo $select; ?>><?php 
						if(!empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])){
							echo strtolower($pIssue['company_name']." ( ".$pIssue['issue_to_name']." )");
						}elseif(empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])){
							echo strtolower("( ".$pIssue['issue_to_name']." )");
						}else{
							echo strtolower($pIssue['company_name']);
						}?></option><?php  } ?>
					</optgroup>
					
                    <optgroup label="Ad hoc (External)">
					<?php foreach($projectAddresBookUsers as $addresBookUsers){	
							$select = ($meetAttendee['attendees_name'] == $addresBookUsers['full_name'])?'selected="selected"':'';
					?>
						<option value="<?php echo $addresBookUsers['full_name'];?>" <?php echo $select; ?>><?php 
						if(!empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])){
							echo strtolower($addresBookUsers['full_name']." ( ".$addresBookUsers['company_name']." )");
						}elseif(empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])){
							echo strtolower("( ".$addresBookUsers['company_name']." )");
						}else{
							echo strtolower($addresBookUsers['full_name']);
						}?></option><?php } ?>
					</optgroup>
                	     
				</select>              
              </div></td>
          <td><input name="companyName[0]" type="text" class="input_small" id="companyName_0" style="width:240px; " value="" size="30" maxlength="50"/></td>
          <?php    if(isset($perm['siteDiary']) && $perm['siteDiary']!=2)
							{	?>
          <?php } ?>
          </tr>
        <?php }?>
        </table>
      <input type="hidden" value="<?php echo ($i>0)?$i:1;?>" name="totalAttendee" id="totalAttendee"/>
      </td>
  </tr>
  <tr>
    <td colspan="4" valign="top">
      <table border="1"  cellpadding="2" cellspacing="0" id="item_table" >
        <tr>
          <th width="" height="30" bgcolor="#CCCCCC"></th>
          <th width="" height="30" bgcolor="#CCCCCC">ITEM</th>
          <th width="" height="30" bgcolor="#CCCCCC">DETAILS</th>
          <th width="" height="30" bgcolor="#CCCCCC">BY WHOM</th>
          <th width="" height="30" bgcolor="#CCCCCC">BY WHEN</th>
          <th width="" height="30" bgcolor="#CCCCCC">COMMENTS</th>
          <th width="" height="30" bgcolor="#CCCCCC">BOLD</th>
          </tr>
        <?php $i=0; if(isset($meetingItemData) && !empty($meetingItemData)){
							foreach($meetingItemData as $key=>$meetingItem){	
			?>
        <tr id="tr_<?php echo $i;?>">
          <td><span id="input-id-2" >
            <?php if($i==0){?>
            <a href="javascript:void(0);" id="add_more_link" onclick="addMoreItemInTable();"> <img  src="images/inspectin_add.png"  /></a>
            <?php }else{  $rmItemId=isset($meetingItem['item_id'])?$meetingItem['item_id']:0; ?>
            <a onclick="return removeMoreItemInTable(<?php echo $i.", ".$rmItemId; ?>)" href="javascript:void(0);"><img src="images/inspectin_delete.png"></a>
            <?php } ?>
            </span></td>
          <td ><input type="hidden" name="itemId[<?php echo $i; ?>]" id="itemId_<?php echo $i;?>" value="<?php echo isset($meetingItem['item_id'])?$meetingItem['item_id']:0;?>" />
            <div id="custmCMP_<?php echo $i; ?>">
              <input name="item[<?php echo $i; ?>]" type="text" class="input_small" id="item_<?php echo $i; ?>" style="width:50px; " value="<?php echo isset($meetingItem['item'])?$meetingItem['item']:'';?>"/>
              </div></td>
          <td><input name="detail[<?php echo $i; ?>]" type="text" class="input_small" id="detail_<?php echo $i; ?>" style="width:240px; " value="<?php echo isset($meetingItem['details'])?$meetingItem['details']:'';?>" /></td>
          <td><input name="byWhom[<?php echo $i; ?>]" type="text" class="input_small" id="byWhom_<?php echo $i; ?>" style="width:140px; " value="<?php echo isset($meetingItem['by_whom'])?$meetingItem['by_whom']:'';?>" /></td>
          <td><input name="byWhen[<?php echo $i; ?>]" type="text" class="input_small" id="byWhen_<?php echo $i; ?>" style="width:140px; " value="<?php echo (isset($meetingItem['by_when']) && $meetingItem['by_when']!="0000-00-00")?$object->dateChanger("-", "/", $meetingItem['by_when']):'';?>" readonly /></td>
          <td><input name="comments[<?php echo $i; ?>]" type="text" class="input_small" id="comments_<?php echo $i; ?>" style="width:240px; " value="<?php echo isset($meetingItem['comments'])?$meetingItem['comments']:'';?>" /></td>  
          <td><input name="isHighlighted[<?php echo $i; ?>]" type="checkbox" class="input_small" id="isHighlighted_<?php echo $i; ?>"style="width:20px; " value="1"  <?php echo (isset($meetingItem['is_highlighted']) && $meetingItem['is_highlighted']==1)?"checked":'';?> /></td>                                          
          </tr>
        <?php $i++; } }else{?>
        <tr>
          <td><a href="javascript:void(0);" id="add_more_link" onclick="addMoreItemInTable();"> <img  src="images/inspectin_add.png"  /></a></td>
          <td >
            <input type="hidden" name="itemId[<?php echo $i; ?>]" id="itemId_0" value="<?php echo isset($itemId)?$itemId:0;;?>" />
            <input name="item[0]" type="text" class="input_small" id="item_0" style="width:50px; " value="" />
            </td>
          <td><input name="detail[0]" type="text" class="input_small" id="detail_0" style="width:240px; " value="" /></td>
          <td><input name="byWhom[0]" type="text" class="input_small" id="byWhom_0" style="width:140px; " value="" /></td>
          <td><input name="byWhen[0]" type="text" class="input_small" id="byWhen_0" style="width:140px; " value="" readonly /></td>
          <td><input name="comments[0]" type="text" class="input_small" id="comments_0" style="width:240px; " value="" /></td>
          <td valign="top"><input name="isHighlighted[0]" type="checkbox" class="input_small" id="isHighlighted_0" style="width:20px; " value="1" /></td>
          </tr>
        <?php }?>
        </table>
      <input type="hidden" value="<?php echo ($i>0)?$i:1;?>" name="totalTableRow" id="totalTableRow"/>    
      </td>
  </tr>
  <tr>
    <td valign="top">&nbsp;</td>
    <td colspan="2" align="right" valign="top"><a style="float: none;height: 44px; width: 87px; cursor:pointer;" onclick="javascript:history.back(-1);"><img src="images/back_btn.png"></a></td>
    <td valign="top"><input type="hidden" value="<?php echo (isset($meetingId) && !empty($meetingId))?$meetingId:0;?>" name="meetingId" id="meetingId"/>      <input name="submit" type="submit" class="submit_btn" value="submit" style="background-image:url(images/<?php echo (isset($meetingId) && !empty($meetingId))?'update':'save';?>.png); border:none; width:130px;height:44px;color:transparent;font-size:0px;" /></td>
  </tr>
 </table>
</form>

<link rel="stylesheet" href="css/chosen.css">
<script src="js/jquery.min.for.choosen.js" type="text/javascript"></script>
<script src="js/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
var config = {
  '.chzn-select'           : {},
  '.chzn-select-deselect'  : {allow_single_deselect:false},
  '.chzn-select-width'     : {width:"95%"}
}
for (var selector in config) {
  $(selector).chosen(config[selector]);
}
</script>