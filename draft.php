<?php 
session_start();
$projectId = isset($_SESSION['idp'])?$_SESSION['idp']:0;
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$userId = $_SESSION['ww_builder']['user_id'];
$permArray = array('General Consultant', 'Architect', 'Structural Engineer', 'Services Engineer', 'Sub Contractor', 'Client');
$permArrayTwo = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Sub Contractor');
if (isset($_REQUEST['action']) && $_REQUEST['action']=='delete') {
    $id = $_REQUEST['id']; 
	
	 mysql_query("UPDATE pmb_user_message SET is_deleted = 1 WHERE user_message_id = '".$id."'");
    //mysql_query("UPDATE messages SET sent_deleted = 1 WHERE m_id = '".$id."'");
    //echo "Message succesfully deleted from your outbox.";
	 header('location:?sect=drafts');
}

if (isset($_REQUEST['form_type']) && $_REQUEST['form_type']=='draft') {
    if(sizeof($_REQUEST['messageID'])>0) {
	for($i=0;$i<sizeof($_REQUEST['messageID']);$i++) {
	   $id = $_REQUEST['messageID'][$i];
	   mysql_query("UPDATE pmb_user_message SET is_deleted = 1 WHERE user_message_id = '".$id."'");
       }
	  header('location:?sect=drafts');
	}
}

function getuserdetails($id){
  
   $req1 = mysql_query('select user_id, user_name from user where user_id="'.$id.'"');
   $row=mysql_fetch_array($req1);
   return $row;
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
		/*"aaSorting": [ [3,'desc'] ],*/
		"iDisplayLength": 100,
		"sPaginationType": "full_numbers",
		"aoColumns": [
			{"sType": "html"},
<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
			{"sType": "html"},
<?php }?>
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"}
		]
	});
});
function delMessage (id) {
	var r = jConfirm('Are you sure you want to delete this message?', null, function(r){ if(r==true){ window.location = '?sect=drafts&id='+id+'&action=delete'; } });	
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
	<?php if(isset($_GET['view']) && $_GET['view'] == 'workflow'){ ?>
		
		<div id="leftNav" style="width:250px;float:left;">
			<?php include 'side_menu.php';?>
		</div>
		<?php }?>
<div class="GlobalContainer clearfix">
<?php include 'message_side_menu.php'; ?>
<div class="MailRight" <?php if($_GET['view']=='workflow'){echo "style='width:65%;'";}?>>
	<div class="MailRightHeader">
    	<ul style="margin-left:-28px !important;">
		<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
        	<li><a href="#" onClick="deleteSelected('draft');"><img src="images/delete1.png" width="93" height="34" alt="Delete" /></a></li>
		<?php }?>
		<?php if(!in_array($_SESSION['userRole'], $permArrayTwo) || $_SESSION['idp'] == '242'){?>
			<li><a href="?sect=compose"><img src="images/compose.png" width="103" height="34" alt="Compose" /></a></li>
		<?php }?>

            <!--<li><a href=""><img src="images/reply.png" width="81" height="34" alt="Delete" /></a></li>-->
        </ul>
        <h3 style="color:#000000; margin-top:10px; margin-right:190px; float:right;">Project Name : <?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></h3>
    </div>
    <?php if(isset($_GET['sm']) && empty($_GET['sm'])){ ?>
			<div class="success_r" style="height:35px;width:93%; margin-left:15px; margin-bottom:10px; "><p>Message saved in draft successfully!</p></div>		
<?php } ?>    
	<?php
	
	$query = "SELECT um.user_id,
	                        user_message_id,
	                        um.from_id, 
							um.message_id, 
							um.thread_id,
							um.inbox_read, 
							m.title, 
							m.message, 
							m.message_type, 
							m.sent_time
							 
					FROM
						pmb_user_message as um,
						pmb_message m
					WHERE
						um.message_id = m.message_id AND
						m.is_draft = 1 AND
						um.is_deleted = 0 AND
						um.project_id = '".$projectId."' AND
						um.user_id = '".$_SESSION['ww_builder_id']."'
					GROUP BY
						um.message_id
					ORDER BY m.sent_time DESC";
					 
 $sentquery = mysql_query($query);
	
	
						  
		?>
     <form name="draft" action="" method="post" id="draft">
	 <table cellpadding="0" cellspacing="0" border="0" class="display" id="sentData" width="100%">

	<thead>
		<tr>
			<th width="2%"><input name="allbox" type="checkbox" value="" onClick="checkedAll('draft');" /></th>
			<th width="10%">To</th>
			<th>Subject</th>
			<th width="20%">Time</th>
		<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
			<th width="10%">Action</th>
		<?php }?>
		</tr>
	</thead>
	<tbody>
	<?php if(intval(mysql_num_rows($sentquery))>0) { ?>
	<?php //We display the list of unread messages
while($draft = mysql_fetch_array($sentquery)) { 
	$user=getuserdetails($draft['from_id']); ?>

		<tr class="even gradeA">
			<td><input name="messageID[]" type="checkbox" value="<?php echo $draft['user_message_id']; ?>" /></td>
			<td><a href="?sect=view_message&folderType=<?php echo $draft['message_type'];?>&id=<?php echo base64_encode($draft['user_message_id']); ?>&type=draft"><?php echo htmlentities($user['user_name'], ENT_QUOTES, 'UTF-8'); ?></a></td>
			<td><a href="?sect=view_message&folderType=<?php echo $draft['message_type'];?>&id=<?php echo base64_encode($draft['user_message_id']); ?>&type=draft"><?php echo htmlentities($draft['title'], ENT_QUOTES, 'UTF-8'); ?> </a></td>
			<td class="center"><?php echo date('d/m/Y H:i:s' ,strtotime($draft['sent_time'])); ?></td>
		<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
			<td class="center"><a onClick="delMessage(<?php echo $draft['user_message_id']; ?>);"  href="#"><img src="images/del.png" width="16" height="16" alt="Delete" title="Delete" /></a></td>
		<?php }?>
		</tr>
		
		
<?php } 
 } else { ?>
	<tr>
    	<td colspan="4" class="center">You have no messages.</td>
    </tr>
<?php } ?>
		
		
	</tbody>
</table>
<input type="hidden" name="form_type" value="draft">
</form>
     
</div>
</div>
<style>
div.content_container{
	width:100% !important;
}
</style>
