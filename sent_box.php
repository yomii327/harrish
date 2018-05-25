<?php 
session_start();
$projectId = isset($_SESSION['idp'])?$_SESSION['idp']:0;

include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$permArray = array('General Consultant', 'Architect', 'Structural Engineer', 'Services Engineer', 'Sub Contractor', 'Client');
$permArrayTwo = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Sub Contractor');
$userId = $_SESSION['ww_builder']['user_id'];


if (isset($_REQUEST['action']) && $_REQUEST['action']=='delete') {
    $id = $_REQUEST['id']; 
	
	mysql_query("UPDATE pmb_user_message SET is_deleted = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE thread_id = '".$id."'"); 
    //mysql_query("UPDATE messages SET sent_deleted = 1 WHERE m_id = '".$id."'");
    //echo "Message succesfully deleted from your outbox.";
	 header('location:?sect=sent_box');
}

if (isset($_REQUEST['form_type']) && $_REQUEST['form_type']=='sentbox') {
    if(sizeof($_REQUEST['messageID'])>0) {
	for($i=0;$i<sizeof($_REQUEST['messageID']);$i++) {
	   $id = $_REQUEST['messageID'][$i];
	   mysql_query("UPDATE pmb_user_message SET is_deleted = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE thread_id = '".$id."'");
       }
	  header('location:?sect=sent_box');
	}
}

function getuserdetails($id){
  
   $req1 = mysql_query('select user_id, user_name from user where user_id="'.$id.'"');
   $row=mysql_fetch_array($req1);
   return $row;
}

function threadCount($id){
	$sql="select count(*) as num from pmb_user_message where type = 'sent' AND thread_id='".$id."' AND is_deleted='0' group by message_id";
	$result=mysql_query($sql);
	$row=mysql_fetch_array($result);
	//return $row['num'];
	return mysql_num_rows($result);
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php'; ?>
<link href="css/email.css" rel="stylesheet" type="text/css" />
<style>
.dataTables_wrapper
{
    clear: both;
    margin-left: 10px;
    min-height: 302px;
    position: relative;
    width: 98%;
}
.sorting_1
{
	padding-left:26px !important;	
}
tr.gradeA td
{
	line-height:30px;
}
tr.gradeA td a
{
	display:block;
	color:#000;
}
table.display tr.odd.gradeA 
{
    background-color:#CCCCCC;
}
tr.odd.gradeA td.sorting_1
{
    background-color:#CCCCCC;
}
table.display tr.even.gradeA 
{
    background-color:#EAEAEA;
}
tr.even.gradeA td.sorting_1
{
    background-color:#EAEAEA;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
	oTable = $('#sentData').dataTable({
		"bJQueryUI": true,
		"bStateSave": false,		
		/*"aaSorting": [ [3,'asc'] ],*/
		"iDisplayLength": 100,
		"sPaginationType": "full_numbers",
		"aoColumns": [
			{"sType": "html"},
			{"sType": "html"},
<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
			{"sType": "html"},
<?php }?>
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"}
		]
	});
} );
function delMessage (id) {
	var r = jConfirm('Are you sure you want to delete this message?', null, function(r){ if(r==true){ window.location = '?sect=sent_box&id='+id+'&action=delete'; } });	
/*	if (confirm("Are you sure want to delete this?")) {
	   location.href='?sect=sent_box&id='+id+'&action=delete';
	 } else { 
	 } 
	 */
}
checked=false;
function checkedAll (frm) {
	var aa= document.getElementById(frm);
	 if (checked == false) {
           checked = true
      } else {
           checked = false
      }
	for (var i =0; i < aa.elements.length; i++) {
	    aa.elements[i].checked = checked;
	  }
 }
 
 function deleteSelected (frm) {
     var aa= document.getElementById(frm);
	 totalChecked=0;
	 for (var i =0; i < aa.elements.length; i++) {
	    var e = aa.elements[i];
	    if ((e.name != 'allbox') && (e.type=='checkbox')) {
	    if(eval(aa.elements[i].checked) == true) {
           totalChecked=totalChecked+1;
		 }  
		}
	  }
	  
	  if(totalChecked>0) {
	    if (confirm("Are you sure want to delete this?")) {
	       document.getElementById(frm).submit();
		   return true;
	     } else { } 
       } else {
	     alert('Please select atleast one record');
	     return false;
	   }
 }
</script>

<body id="dt_example">
<div class="demo_jui" style="float:left;width:100%;" >
<div class="GlobalContainer clearfix">
<?php include 'message_side_menu.php'; ?>
<div class="MailRight">
	<div class="MailRightHeader">
    	<ul style="margin-left:-28px !important;">
		<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
        	<li><a href="#" onClick="deleteSelected('sentbox');"><img src="images/delete1.png" width="93" height="34" alt="Delete" /></a></li>
		<?php }?>
		<?php if(!in_array($_SESSION['userRole'], $permArrayTwo)){?>
			<!--li><a href="?sect=compose"><img src="images/compose.png" width="103" height="34" alt="Compose" /></a></li-->
		<?php }?>
            <!--<li><a href=""><img src="images/reply.png" width="81" height="34" alt="Delete" /></a></li>-->
        </ul>
        <h3 style="color:#000000; margin-top:10px; margin-right:190px; float:right;">Project Name : <?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></h3>
    </div>
    
    <?php if(isset($_GET['sm']) && empty($_GET['sm'])){ ?>
			<div class="success_r" style="height:35px;width:93%; margin-left:15px; margin-bottom:10px; "><p>Message sent successfully!</p></div>		
<?php } ?>
	<?php
	$spCon = "";
	if(isset($_GET['folderType'])){
		$spCon = "m.message_type = '".$_GET['folderType']."' AND ";
	}
	$query = "SELECT um.user_id,
	                        user_message_id,
	                        um.from_id, 
							um.message_id, 
							um.thread_id,
							um.inbox_read, 
							m.message_type,
							m.title, 
							m.message, 
							m.sent_time
							 
					FROM
						pmb_user_message as um,
						pmb_message m
					WHERE
						um.message_id = m.message_id AND
						um.type = 'sent' AND
						".$spCon."
						um.is_deleted = 0 AND
						m.is_draft = 0 AND
						um.project_id = '".$projectId."' AND
						um.user_id = '".$_SESSION['ww_builder_id']."'
					GROUP BY
						um.thread_id
					ORDER BY m.sent_time DESC";
					 
 $sentquery = mysql_query($query);
	
	
						  
		?>
     <form name="sentbox" action="" method="post" id="sentbox">
	 <table cellpadding="0" cellspacing="0" border="0" class="display" id="sentData" width="100%">
	 
	<thead>
		<tr>
			<th width="2%"><input name="allbox" type="checkbox" value="" onClick="checkedAll('sentbox');" /></th>
			<th width="5%">To</th>
			<th width="50%">Subject</th>
			<th>Message Type</th>
			<th width="5%">Time</th>
		<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
			<th width="5%">Action</th>
		<?php } ?>
		</tr>
	</thead>
	<tbody>
	<?php if(intval(mysql_num_rows($sentquery))>0) { ?>
	<?php //We display the list of read messages
          while($sentBox = mysql_fetch_array($sentquery)) {
			$user=getuserdetails($sentBox['from_id']);
			$count = threadCount($sentBox['thread_id']);
			if(explode("_", $sentBox['message_type'])){
				$folderTypeArr = end(explode("_", $sentBox['message_type']));
			}else{
				$folderTypeArr = $sentBox['message_type'];
			}
			
			?>
    	<tr class="even gradeA">
			<td><input name="messageID[]" type="checkbox" value="<?php echo $sentBox['thread_id']; ?>" /></td>
			<td><a href="?sect=message_details&id=<?php echo base64_encode($sentBox['thread_id']); ?>&type=sent"><?php echo htmlentities($user['user_name'], ENT_QUOTES, 'UTF-8'); ?> </a></td>
			<td><a href="?sect=message_details&id=<?php echo base64_encode($sentBox['thread_id']); ?>&type=sent" style=" <?php if($count>1) {?>background:url(images/reply.png) 0 no-repeat;<?php } ?> padding-left:20px;"><?php echo htmlentities($sentBox['title'], ENT_QUOTES, 'UTF-8'); ?> 
		    <?php if($count>1) {?>(<?php echo $count;?>) <?php } ?></a></td>
			<td class="center"><?php echo $folderTypeArr; ?></td>
			<td class="center"><?php echo date('d/m/Y H:i:s' ,strtotime($sentBox['sent_time'])); ?></td>
		<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
			<td class="center"><a onClick="delMessage(<?php echo $sentBox['thread_id']; ?>);" href="#" ><img src="images/del.png" width="16" height="16" alt="Delete" title="Delete" /></a></td>
		<?php } ?>
		</tr>
		<?php } ?>
		<?php } else { ?>
		<tr>
			<td colspan="4" class="center">You have no  messages.</td>
		</tr>
        <?php } ?>
		</tbody>
		</table>
<input type="hidden" name="form_type" value="sentbox">
</form>
     
</div>
</div>
<style>
div.content_container{
	width:100% !important;
}
</style>
