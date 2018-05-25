<?php include'data-table.php';
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$userId = $_SESSION['ww_builder']['user_id'];

if(isset($_POST['message']) and $_POST['message']!='') {
	$message = $_POST['message'];
	$to_id=$_POST['to_id'];
	$title=$_POST['title'];
	$attahment=$object->upload_attahment('attachment1');
	//We remove slashes depending on the configuration
	if(get_magic_quotes_gpc())
	{
		$message = stripslashes($message);
	}
	//We protect the variables
	$message = mysql_real_escape_string(nl2br(htmlentities($message, ENT_QUOTES, 'UTF-8')));
	
	//We send the message and we change the status of the discussion to unread for the recipient
	if(mysql_query('insert into pmb_message (to_user, from_user, title, message, sent_date)
	                values("'.$to_id.'", "'.$_SESSION['ww_builder_id'].'","'.$title.'", "'.$message.'", NOW())')) {
		if($attahment!='') {
			$id=mysql_insert_id();  
			mysql_query('insert into pmb_attachments (message_id, attachment_name, status)
						values("'.$id.'", "'.$attahment.'","0")');
			}			
			unset($_POST['message']);
?>

<div class="message">Your message has successfully been sent.<br />
</div>
<?php
	}
	else
	{
?>
<div class="message">An error occurred while sending the message.<br />
</div>
<?php
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action']=='sent_delete') {
    $id = $_REQUEST['id']; 
	$nxt = mysql_query('select to_user, from_user from messages where message_id = "'.$id.'"'); 
	$nxt = mysql_fetch_array($nxt);
	$nxt = mysql_query('select message_id from messages where message_id != "'.$id.'" AND ((from_user="'.$nxt['to_user'].'" AND to_user="'.$nxt['from_user'].'") OR (to_user="'.$nxt['to_user'].'" AND from_user="'.$nxt['from_user'].'"))'); 
	$nxtrow = mysql_fetch_array($nxt);
	$nxtID=$nxtrow['message_id'];
	mysql_query("UPDATE pmb_message SET sent_deleted = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE message_id = '".$id."'");
    //echo "Message succesfully deleted from your outbox.";
	 header('location:?sect=message_details&id='.$nxtID.'');
}

if (isset($_REQUEST['action']) && $_REQUEST['action']=='delete') {
    $id = $_REQUEST['id'];  
    mysql_query("UPDATE messages SET deleted = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE message_id = '".$id."'");
    //echo "Message succesfully deleted from your outbox.";
	 header('location:?sect=messages');
}

function getattachment($mid){
   $req1 = mysql_query('select attach_id, name, attachment_name from pmb_attachments where message_id = "'.$mid.'" and is_deleted=0');
   $row=mysql_fetch_array($req1);
   return $row;
}

function getuserdetails($id){
  
   $req1 = mysql_query('select user_id, user_name, user_fullname, user_email from user where user_id="'.$id.'"');
   $row=mysql_fetch_array($req1);
   return $row;
}

if(isset($_GET['id'])) {

//$id = intval($_GET['id']);
$id=base64_decode($_GET['id']);
//We get the title and the narators of the discussion

$req1 = mysql_query('select um.user_id, um.type, m.title, m.message_id, m.sent_time, m.message, um.from_id, m.message_type, m.cc_email_address, m.to_email_address, m.tags, um.user_message_id, m.company_tag as companyTag, m.purchaser_location from pmb_user_message um , pmb_message m where user_message_id="'.$id.'" AND um.message_id = m.message_id');

if(mysql_num_rows($req1)>=1) {
$dn1 = mysql_fetch_array($req1);
if($dn1['type']=='inbox') {
      $fromuser=getuserdetails($dn1['from_id']);
      $touser=getuserdetails($dn1['user_id']);
	} else {
	$fromuser=getuserdetails($dn1['user_id']);
    $touser=getuserdetails($dn1['from_id']);
	}
//$fromuser=getuserdetails($dn1['user_id']);
//$touser=getuserdetails($dn1['from_id']);
//$attachemnt=getattachment($dn1['message_id']);
$attachemnts = $object->selQRYMultiple('attach_id, name, attachment_name', 'pmb_attachments', 'message_id="'.$dn1['message_id'].'" and is_deleted=0');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link href="css/email.css" rel="stylesheet" type="text/css" />
<style>
label { color:#000000;}

h3 {
    border-bottom: 1px solid #CCCCCC;
    color: #000000;
    font-size: 16px;
    margin-left: 10px;
    margin-right: 10px;
    margin-top: 13px;
    padding-bottom: 10px;
}
ul li{ list-style-type:none;}
</style>
<script type="text/javascript" src="js/thickbox.js"></script>
<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />

<div class="GlobalContainer clearfix">
<?php include 'message_side_menu.php'; ?>
<div class="MailRight"> <a href="#" onclick="javascript:history.back(0);"><img src="images/back.png" width="79" height="34" alt="Back" style="float:right; margin:5px;" /></a>
<h3 style="color:#000000; margin-top:10px; margin-right:100px; float:right; border-bottom:none;" >Project Name : <?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name');
$refrenceNo = explode(' ',$projectName);
	if(is_array($refrenceNo)){
		$refrenceNo = strtolower($refrenceNo[0]);
	}else{
		$refrenceNo = strtolower($projectName);
	}
?></h3>
<?php if ($subject == '') {
        $subject = $dn1['title'];
        echo '<h3 style="color:#666666; clear:both;">' . $dn1['title'] . '</h3>';
    } ?>
	<div class="ReadMail" style="color:#333333;">
  <?php if(explode("_", $dn1['message_type'])){
			$folderType = end(explode("_", $dn1['message_type']));
		}else{
			$folderType = $dn1['message_type'];
		}
  ?>
	     <?php echo "<p style='color:#000;'><b>Ref. #: </b>".$refrenceNo." ".$dn1['user_message_id']."</p>"; ?>
		<div class="ReadMailProperties"> <strong><?php echo $fromuser['user_fullname']; ?></strong> <?php echo isset($dn1['message_type'])?$folderType:'';?> to <?php 
		//	if(isset($_GET['type']) && $_GET['type']=='sent'){
				
			 $query = "select from_id FROM pmb_user_message WHERE message_id=".$dn1['message_id']." and is_deleted=0 and type='sent' GROUP BY from_id";
				$getUserList = mysql_query($query);
				 if(isset($getUserList)){
					 $multipleUser = '';
					 while($userId = mysql_fetch_array($getUserList)){
						$result = getuserdetails($userId['from_id']);
						 if($multipleUser == ''){
							$multipleUser.= $result['user_fullname'];
						 }else{
							$multipleUser.= ', '.$result['user_fullname'];
						 }
					 }
				 }
				  if($dn1['to_email_address'] != ''){
					$multipleUser =  $multipleUser.', '.$dn1['to_email_address'];
				  }
				  if($dn1['cc_email_address'] != ''){
					$multipleUser =  $multipleUser.' <br />CC : '.$dn1['cc_email_address'];
				  }
				echo $multipleUser;
			//}else{
			//	echo $touser['user_fullname'];
			//}
		 ?>
			<ul>
				<li>
					<?php echo date('d/m/Y H:i:s' ,strtotime($dn1['sent_time'])); ?>
					<?php if($_GET['type'] == 'draft'){
							echo "<a href='?sect=compose&msgid=".$dn1['message_id']."&folderType=".$dn1['message_type']."'>Compose Again</a>";
					}?>
					
				</li>
			</ul>
		</div>
		<?php 
		if($dn1['message_type']=="Purchaser Changes"){echo "<p><b>Purchaser Location : </b>".$dn1['purchaser_location']."</p>"; }
		echo "<p><b>Tags : </b>".$dn1['tags']."</p>";
		echo "<p><b>Company Tag : </b>".$dn1['companyTag']."</p>";
		echo html_entity_decode(str_replace('\n','',$dn1['message'])); 
		
			if(isset($attachemnts)){ $i=0; 
				foreach($attachemnts as $attachemnt){
					if($i==0){ $i++;
						if(count($attachemnts)==1) {echo '<br> <b>Attachment —</b>';}else
						if(count($attachemnts)>1) {echo '<br> <b>Attachments —</b>';}
					}
		?>
        <p>
		<img src="images/attchment.png" width="16" height="14" />&nbsp;&nbsp;<?php echo $attachemnt['name']; ?>&nbsp;&nbsp;
		<?php $type = explode('.', $attachemnt['attachment_name']);
              $type = end($type);
              if(strpos('JPEG |jpeg |JPG |jpg |PNG |png |GIF |gif', $type)>0){
		?>
        [ <a href="attachment/<?php echo $attachemnt['attachment_name']; ?>" target="_blank" style="color:#06C;" class="thickbox" >View</a>] &nbsp;&nbsp;
        <?php }else{ ?>
        [ <a href="attachment/<?php echo $attachemnt['attachment_name']; ?>" target="_blank" style="color:#06C;" >View</a>] &nbsp;&nbsp;        
        <?php } ?>    
        [ <a href="download.php?attachment=<?php echo $attachemnt['attach_id']; ?>" target="_blank" style="color:#06C;">Download</a> ]
        </p>
		<?php }} ?>
	</div>
	<?php } else { ?>
	<div class="message" style="color:#999999; font-size:16px;">No Messages Found.<br />
	</div>
	<?php } } ?>
</div>
<style>
div.content_container{
	width:100% !important;
}

</style>
