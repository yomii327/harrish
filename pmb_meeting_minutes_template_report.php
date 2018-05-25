<?php
ob_start();
session_start();

include('includes/commanfunction.php');
$object= new COMMAN_Class();
//$object = new COMMAN_Class();

$meetingId = $_GET['meetingId'];

$meetingData = $object->selQRYMultiple('project_id, location, meeting_number, meeting_location, title, description, date, time, type, created_date, created_by', 'meeting_details', 'is_deleted = 0 AND meeting_id = "'.$meetingId.'"'); 
//print_r($meetingData);
if(isset($meetingData) && !empty($meetingData)){
	$_SESSION['idp'] = $meetingData[0]['project_id'];
	setcookie('pmb_'.$_SESSION['ww_builder_id'], $meetingData[0]['project_id'], time()+864000);
	$meetingAttendeeData = $object->selQRYMultiple('attendee_id, attendees_name, company_name', 'meeting_attendees', 'is_deleted = 0 AND meeting_id = "'.$meetingId.'"'); 	
	$meetingItemData = $object->selQRYMultiple('item_id, item, details, by_whom, by_when, comments, is_highlighted', 'meeting_item_details', 'is_deleted = 0 AND meeting_id = "'.$meetingId.'"'); 	
	$userData = $object->selQRYMultiple('user_fullname', 'user', 'is_deleted = 0 AND user_id = "'.$meetingData[0]['created_by'].'"'); 		
}

$q = "SELECT project_id, project_name FROM user_projects WHERE user_id = '".$_SESSION['ww_builder_id']."' and project_id = '".$_SESSION['idp']."' and is_deleted = 0 GROUP BY project_name";
$res = mysql_query($q);
$prIDArr = array();
$outPutStr = "";
while($q1 = mysql_fetch_array($res)){
	$prIDArr[$q1[0]] = $q1[1];
}
//New Added code for project dropdown End here
$projectId = isset($_SESSION['idp'])?$_SESSION['idp']:0;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
<link href="style.css" rel="stylesheet" type="text/css" />
<style>
#htmlContainer table td{ height:20px; padding:2px; color:#000;}
#item_table td, #item_table th{border:1px #000000 solid;}
.itemHighLight { font-weight:bold;}
table { font-family:Helvetica !important; }
</style>
</head>
<body>
<div id="mainContainer">
	<div class="buttonDiv">
		<img onClick="printDiv();" src="images/print_btn.png" style="float:left;" />
		<img onClick="emailPDF();" src="images/email.png" style="float:left;margin-left:160px;" />
		<img onClick="downloadPDF();"src="images/download_btn.png" style="float:right;" />     
	</div><br clear="all" />
<br /><br />
	<div id="htmlContainer">

  <!--h1 style="color:#000; margin-left:10px;">AGENDA</h1-->
  <?php 
echo $_SESSION['idp'];
$logo = $object->selQRYMultiple('project_logo','projects','is_deleted = 0 AND project_id = '.$_SESSION['idp'].'', 'data');
//issue to data fetch here
if(file_exists('project_images/'.$logo[0]['project_logo']) && !empty($logo)){
	$logo_proj = 'project_images/'.$logo[0]['project_logo']; 	
}else{
	$logo_proj = 'company_logo/logo.png';
}  
?>
<img src="<?php echo $logo_proj ;?>" style="float:right"  />
<br />
<h1 style="background:#000000; color:#FFFFFF; padding:5px; display:block; clear:both; margin-top:70px; padding-left:20px;">MEETING MINUTES </h1>
    
  <!--h1 style="color:#000; margin-left:10px;">MEETING MINUTES</h1-->
<table width="98%" border="0" style=" margin:10px;" cellpadding="2" cellspacing="5">
  <tr>
    <td width="29%" height="22" valign="top" style="color:#999999; font-size:20px; font-weight:bold; padding-bottom:10px;" colspan="4"><?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name');?><br />
    <?php echo $meetingData[0]['location']; ?>
    &nbsp;</td>
    </tr>  
  <tr>
    <td width="24%" height="22" valign="top" colspan="4" style="color:#999999; font-size:20px; font-weight:bold;" ><?php echo $meetingData[0]['title']; ?>&nbsp; <br style="margin-bottom:15px;" /></td>
    </tr> 
  <tr>
    <td height="22" colspan="4" valign="top"><strong><?php echo $meetingData[0]['description']; ?></strong><br />
	<?php echo $meetingData[0]['meeting_location']; ?></td>
  </tr>      
  <tr>
    <td height="22" colspan="4" valign="top"><?php $d = strtotime(str_replace("00:00:00", $meetingData[0]['time'].":00", $meetingData[0]['date']));
	echo date('l d F Y', $d)." at ".date('h.ia', $d); ?>&nbsp;</td>
    </tr>
  <tr>
    <td colspan="4" valign="top"><strong>ATTENDEES:</strong>
<table width="65%" border="0" cellpadding="0" cellspacing="0" id="attendee_table" >
        <?php $i=0; if(isset($meetingAttendeeData) && !empty($meetingAttendeeData)){
							foreach($meetingAttendeeData as $key=>$meetAttendee){ $i++;
			?>
        <tr id="tr_<?php echo $i;?>">
          <td width="41%" ><?php echo $meetAttendee['attendees_name']; ?>&nbsp;</td>
          <td width="59%" ><?php echo $meetAttendee['company_name']; ?>&nbsp;</td>
        </tr>
        <?php } }?>
        </table>     
    &nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" valign="top">
      <table width="100%" border="0"  cellpadding="2" cellspacing="0" id="item_table" style="" >
        <tr>
          <th width="7%" height="30" align="left" bgcolor="#999999" style="color:#FFFFFF;">ITEM</th>
          <th width="30%" height="30" align="left" bgcolor="#999999" style="color:#FFFFFF;">DETAILS</th>
          <th width="10%" height="30" align="left" bgcolor="#999999" style="color:#FFFFFF;">BY WHOM</th>
          <th width="10%" height="30" align="left" bgcolor="#999999" style="color:#FFFFFF;">BY WHEN</th>
          <th width="30%" height="30" align="left" bgcolor="#999999" style="color:#FFFFFF;">COMMENTS</th>
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
    <td valign="top"><?php echo $userData[0]['user_fullname']."&nbsp;&nbsp; ".$object->dateChanger("-", ".", substr($meetingData[0]['created_date'], 0, 10)); ?>&nbsp;</td>
    <td width="9%" valign="top">&nbsp;&nbsp;</td>
    <td width="5%" valign="top">&nbsp;&nbsp;</td>
    <td width="57%" valign="top">&nbsp;&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
  </tr>
    <tr>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
    <td valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="4" valign="top" style="border-bottom:2px solid #999999;">&nbsp;</td>
    </tr>
  <tr>
    <td colspan="3" valign="top" style="color:#999999;">Wiseworker</td>
    <td align="right" valign="top" style="color:#999999;">jwilliams@wiseworking.com.au  &nbsp; &nbsp; &nbsp; www.wiseworking.com.au</td>
  </tr>
 </table>
  </div>

</div>
</body>
</html>
