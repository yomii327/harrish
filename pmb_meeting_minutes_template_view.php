<?php 
/*//ini_set('display_errors', 1);
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
*/
$userId = $_SESSION['ww_builder_id'];


?>
<br />
<style>
#htmlContainer table td{ height:20px; padding:3px; padding-left:10px;  color:#000;}
#item_table td, #item_table th{border:1px #000000 solid;}
.itemHighLight { font-weight:bold;}
</style>
<a href="#" onclick="generatePrint(<?php echo $meetingId; ?>);" style="float:right;"><img style="border:none; width:63px; margin-right: 10px;" src="images/report_btn.png"></a>
  <!--h1 style="color:#000; margin-left:10px;">MEETING MINUTES</h1-->
<table width="98%" border="0" style=" margin:10px;" cellpadding="2" cellspacing="5">
  <tr>
    <td width="27%" height="22" valign="top" style="color:#999999;" ><strong>Project Name </strong></td>
    <td colspan="3" valign="top" style="color:#999999;"><?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name');?>&nbsp;</td>
    </tr>  
 <tr>
    <td width="27%" height="22" valign="top"  style="color:#999999;"><strong>Project Location</strong>&nbsp;</td>
    <td colspan="3" valign="top" style="color:#999999;"><?php echo $meetingData[0]['location']; ?>&nbsp;</td>
    </tr>       
  <tr>
    <td width="25%" height="22" valign="top" ><strong>Meeting number</strong></td>
    <td colspan="3" valign="top"><?php echo $meetingData[0]['meeting_number']; ?></td>
    </tr>      
  <tr>
    <td height="22" valign="top"><strong>Meeting Location </strong>&nbsp;</td>
    <td colspan="3" valign="top"><?php echo $meetingData[0]['meeting_location']; ?>&nbsp;</td>
    </tr>    
  <tr>
    <td width="25%" height="22" valign="top" style="color:#999999;" ><strong>Title </strong>&nbsp;</td>
    <td colspan="3" valign="top" style="color:#999999;" ><?php echo $meetingData[0]['title']; ?>&nbsp;</td>
    </tr> 
  <tr>
    <td height="22" valign="top"><strong>Description</strong></td>
    <td colspan="3" valign="top"><?php echo $meetingData[0]['description']; ?></td>
  </tr>   
  <tr>
    <td height="22" valign="top"><strong>Date </strong></td>
    <td width="9%" height="27" valign="top"><?php echo date('d/m/Y' ,strtotime($meetingData[0]['date'])); ?></td>
    <td width="6%" height="27" valign="top"><strong>Time </strong></td>
    <td width="60%" height="27" valign="top"><?php echo $meetingData[0]['time']; ?></td>
  </tr>
  <tr>
    <td colspan="4" valign="top"><strong>ATTENDEES:</strong>
      <table width="65%" border="0" cellpadding="0" cellspacing="0" id="attendee_table" >
        <tr>
          <th width="38%" align="left"  height="25" >Name </th>
          <th width="62%" align="left" height="25" >Company</th>
        </tr>
        <?php $i=0; if(isset($meetingAttendeeData) && !empty($meetingAttendeeData)){
							foreach($meetingAttendeeData as $key=>$meetAttendee){ $i++;
			?>
        <tr id="tr_<?php echo $i;?>2">
          <td height="20" ><?php echo $meetAttendee['attendees_name']; ?>&nbsp;</td>
          <td ><?php echo $meetAttendee['company_name']; ?>&nbsp;</td>
        </tr>
        <?php } }?>
      </table>      &nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" valign="top">
      <table width="100%" border="0"  cellpadding="2" cellspacing="0" id="item_table" style="" >
        <tr>
          <th width="7%" height="30" align="left" bgcolor="#999999" style="color:#FFFFFF;"><strong>ITEM</strong></th>
          <th width="30%" height="30" align="left" bgcolor="#999999" style="color:#FFFFFF;"><strong>DETAILS</strong></th>
          <th width="10%" height="30" align="left" bgcolor="#999999" style="color:#FFFFFF;"><strong>BY WHOM</strong></th>
          <th width="10%" height="30" align="left" bgcolor="#999999" style="color:#FFFFFF;"><strong>BY WHEN</strong></th>
          <th width="30%" height="30" align="left" bgcolor="#999999" style="color:#FFFFFF;"><strong>COMMENTS</strong></th>
          </tr>
        <?php $i=0; if(isset($meetingItemData) && !empty($meetingItemData)){
							foreach($meetingItemData as $key=>$meetingItem){ $i++;
							$is_highlighted = ($meetingItem['is_highlighted']==1)?"itemHighLight":"";
			?>
        <tr id="tr_<?php echo $i;?>" class="<?php echo $is_highlighted; ?>">
          <td height="22" ><?php echo $meetingItem['item']; ?>&nbsp;</td>
          <td ><?php echo $meetingItem['details']; ?>&nbsp;</td>
		 <td ><?php echo $meetingItem['by_whom']; ?>&nbsp;</td>          
		<td ><?php echo (isset($meetingItem['by_when']) && $meetingItem['by_when']!="0000-00-00")?$object->dateChanger("-", "/", $meetingItem['by_when']):'';?>&nbsp;</td>        
		<td ><?php echo $meetingItem['comments']; ?>&nbsp;</td>        
        </tr>
        <?php } }?>
        </table>&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">&nbsp;&nbsp;</td>
    <td valign="top">&nbsp;&nbsp;</td>
    <td valign="top">&nbsp;&nbsp;</td>
    <td valign="top">&nbsp;&nbsp;</td>
  </tr>	
  <tr>
    <td valign="top">&nbsp;</td>
    <td width="9%" valign="top">&nbsp;</td>
    <td width="6%" valign="top">&nbsp;</td>
    <td valign="top"><div id="reportURL" style="display:none;"></div>
    <a style="display: block;float: none;height: 35px; width: 87px; cursor:pointer;" href="<?php echo "?sect=meeting&folderType=".$_GET['folderType']; ?>">
		<img src="images/back_btn2.png">
	</a></td>
  </tr>  
 </table>  
 
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/thickbox.js"></script>
<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
<script type="text/javascript">
var align = 'center';
var top = 30;
var width = 825;
var padding = 10;
var backgroundColor = '#FFFFFF';
var borderColor = '#333333';
var borderWeight = 4;
var borderRadius = 5;
var fadeOutTime = 300;
var disableColor = '#666666';
var disableOpacity = 40;
var loadingImage = 'images/loadingAnimation.gif';	

function generatePrint(meetingId){
	try{
		//document.getElementById("show_defect").innerHTML = '';
	var params = '';
	params = "meetingId="+meetingId+"&name="+Math.random();
				
	modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pmb_meeting_minutes_template_report.php?'+params, loadingImage);

	}catch(e){
		//alert(e.message); 
	}
}	

function printDiv(){
	var divToPrint = document.getElementById('mainContainer'); 
//	var newWin = window.open('', 'PrintWindow', '', false);
	var newWin = window.open('', 'PrintWindow', '', false); 
	newWin.document.open(); 
	newWin.document.write('<html><head><meta http-equiv="X-UA-Compatible" content="IE=8"><style>body { size:A4-landscape; } #item_table td, #item_table th{border:1px #000000 solid;} .itemHighLight { font-weight:bold;} table { font-family:Helvetica !important; }</style></head><body onload="window.print();"><style type="text/css">table.collapse { border-collapse: collapse; border: 1pt solid black; }table.collapse td { border: 1pt solid black; padding: 2px; }.pagination{display:none;}.buttonDiv{display:none;}.footer{display:block;}</style>'+divToPrint.innerHTML+'</body></html>'); 
	newWin.document.close(); 
//	setTimeout(function(){newWin.close();},10000); 
}


function downloadPDF(callback){
	callback = typeof callback !== 'undefined' ? callback : 0;
	showProgress();
	$.ajax({
		url:  'pdf/pmb_meeting_minutes_template_report.php',
		type: "POST",
		data:  {"meetingId":<?php echo $meetingId; ?>, "name": Math.random()},
		success: function (data) {
			hideProgress();
			//console.log(callback);
			if(callback){
				callback(data);					
			}else{
				$('#mainContainer').html("<style>body {color:#000000; }</style>"+data);
			}
		}
	});	
}

function emailPDF(){
//Get all the issue to email, name list here
	var htmlView = '';
	var projName = <?php echo $_SESSION['idp']; ?>;
	var reportType = 'Meeting Minutes Report';
	
	htmlView += '<style>#htmlContainer table td{ height:20px; padding:3px; padding-left:10px;  color:#000;}#item_table td, #item_table th{border:1px #000000 solid;}.itemHighLight { font-weight:bold;}div#mainContainer{ color:#000; width:800px; margin:auto; min-height:70px;; max-height:650px; overflow-y:scroll;}fieldset.emailContainer{border: 1px solid #7F9DB9; padding: 15px;}table td{padding:5px; vertical-align:top;}table{ margin-left:10px;}  #toEmail{ height:70px; } #ccEmail, #bccEmail, #subEmail{ height:20px; }</style><fieldset class="emailContainer"><legend>Email Report</legend><table width="80%" border="0" cellpadding="0" cellspacing="0" align="center"><tr><td width="16%" align="right">To</td><td width="3%">&nbsp;:&nbsp;</td><td width="81%"><textarea name="toEmail" id="toEmail" cols="30" rows="3"><?php echo $emailIdList; ?></textarea></td></tr><tr><td align="right">Cc</td><td>&nbsp;:&nbsp;</td><td><input type="text" name="ccEmail" id="ccEmail" size="40" /></td></tr><tr><td align="right">Bcc</td><td>&nbsp;:&nbsp;</td><td><input type="text" name="bccEmail" id="bccEmail" size="40" /></td></tr><tr><td align="right">Subject</td><td>&nbsp;:&nbsp;</td><td><input type="text" name="subEmail" id="subEmail" size="40" value="'+reportType+'" /></td></tr><tr><td align="right">Attachment</td><td>&nbsp;:&nbsp;</td><td><div id="attachEmail">'+reportType.replace(" ","_")+'.pdf</div><input type="hidden" name="project_id" id="project_id" value="'+projName+'" /><input type="hidden" name="report_type" id="report_type" value="'+reportType+'" /></td></tr><tr><td align="right" valign="top">Message</td><td valign="top">&nbsp;:&nbsp;</td><td><textarea name="descEmail" id="descEmail" cols="30" rows="15"></textarea></td></tr><tr><td align="center" colspan="3"><img onClick="sendEmailPDF();" src="images/send.png" style="float:left;" /><img onClick="closePopup(300);" src="images/cancel.png" style="float:left;margin-left:50px;" /></td></tr></table></fieldset>';
	$('#mainContainer').html(htmlView);
}

function sendEmailPDF(){
	var pdfOutPut = downloadPDF(function(fileURL){
		console.log(fileURL);
		$('#reportURL').html(fileURL);
		try{
			var to = $('#toEmail').val();
			var cc = $('#ccEmail').val();
			var bcc = $('#bccEmail').val();
			var subject = $('#subEmail').val();
			var attachment = $('a.view_btn').attr('href');
			var descEmail = $('#descEmail').val();
			
			if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
			document.getElementById('spinner').innerHTML = '<div class="reportMsg" style="margin-top:230px;margin-top:180px\9;color:#000000;font-weight:bold;z-index:1000000000000;">Sending Mail ..<br/>This may take several minutes.</div>';	
			showProgress();
		
			params = "to="+to+"&cc="+cc+"&bcc="+bcc+"&subject="+subject+"&attachment="+attachment+"&descEmail="+descEmail+"&name="+Math.random();
			
			xmlhttp.open("POST", 'send_report_mail.php', true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.setRequestHeader("Content-length", params.length);
			xmlhttp.setRequestHeader("Connection", "close");
			xmlhttp.onreadystatechange=function(){
				if (xmlhttp.readyState==4 && xmlhttp.status==200){
					hideProgress();
					document.getElementById("mainContainer").style.overflow="visible";
					$('#mainContainer').html("<style>body {color:#000000; }</style>"+xmlhttp.responseText);
				}
			}
			xmlhttp.send(params);
		}catch(e){
		//	alert(e.message); 
		}
	});
}
</script>