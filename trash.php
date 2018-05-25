<?php 
session_start();
$projectId = isset($_SESSION['idp'])?$_SESSION['idp']:0;
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$userId = $_SESSION['ww_builder']['user_id'];
$permArray = array('General Consultant', 'Architect', 'Structural Engineer', 'Services Engineer', 'Sub Contractor', 'Client');
$permArrayTwo = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Sub Contractor');
if (isset($_REQUEST['action']) && !empty($_REQUEST['action'])) {
    $delId = $_REQUEST['delId'];
	$to_id = $_SESSION['ww_builder_id'];
	if($_REQUEST['action']=='restore'){
		mysql_query("UPDATE pmb_user_message SET is_deleted = 0, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE user_message_id = '".$delId."'");
	}else{
		mysql_query("UPDATE pmb_user_message SET type = 'delete', last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE user_message_id = '".$delId."'");
	}
    header('location:?sect=trash');
}


if (isset($_REQUEST['form_type']) && $_REQUEST['form_type']=='trash') {
    $to_id = $_SESSION['ww_builder_id']; 
	if(sizeof($_REQUEST['from'])>0) {
	for($i=0;$i<sizeof($_REQUEST['from']);$i++) {
	   $um_id = $_REQUEST['from'][$i];
	   
		if($_REQUEST['action']=='restore'){
			mysql_query("UPDATE pmb_user_message SET is_deleted = 0, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE user_message_id = '".$um_id."'");
		}else{
			mysql_query("UPDATE pmb_user_message SET type = 'delete', last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE user_message_id = '".$um_id."'");
		}	   
	  }
	}
    header('location:?sect=trash');
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
<?php if($_GET['view']=='workflow'){?>
	.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
.box1 { background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
.link1 { background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
a.link1:hover { background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
.txt13 { border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }
/* onto CSS now */
@import url(http://fonts.googleapis.com/css?family=Open+Sans:600);

body {
	padding: 20px;
	background: whiteSmoke;
	font-family: 'Open Sans';
}

#menu {
	text-align: center;
}

.nav {
	list-style: none;
	display: inline-block; /* for centering */
	/*border: 1px solid #b8b8b8;*/
	font-size: 14px;
	margin: 0; padding: 0;
}

.nav li {
	border-left: 1px solid #b8b8b8;
	float: left;
}
.nav li:first-child {
	border-left: 0;
}

.nav a {
	color: #2f2f2f;
	padding: 0 20px;
	line-height: 32px;
	display: block;
	text-decoration: none;
	
	background: #fbfbfb;
	background-image: linear-gradient(#fbfbfb, #f5f5f5);
}

.nav a:hover {
	background: #fcfcfd;
	background-image: linear-gradient(#fff, #f9f9f9);
}

.nav a.active,
.nav a:active {
	background: #94CE06;
	/*background-image: linear-gradient(#e8e8e8, #f5f5f5);*/
}


/* Tab Panes now */

#tab_panes {
	max-width: 600px;
	margin: 20px auto;
}

.tab_pane {
	display: none;
}
.tab_pane.active {
	display: block;
}

#tab_panes img {
	max-width: 600px;
	box-shadow: 0 0 5px rgba(0,0,0,0.5);
}
<?php }?>
</style>
<script type="text/javascript">
$(document).ready(function() {
	oTable = $('#trashData').dataTable({
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
			{"sType": "html"},	   
			{"sType": "html"}
		]
	});
} );

checked=false;

function restoreMessage (id) {
	var r = jConfirm('Are you sure want to restore this message?', null, function(r){ if(r==true){ window.location = '?sect=trash&delId='+id+'&action=restore'; } });
/*	if (confirm("Are you sure want to delete this?")) {
	   location.href='?sect=messages&thread_id='+id+'&action=delete';
	 } else { 
	 } 
	 */
}
function delMessage (id) {
	var r = jConfirm('Are you sure you want to permanently delete this message?', null, function(r){ if(r==true){ window.location = '?sect=trash&delId='+id+'&action=delete'; } });
/*	if (confirm("Are you sure want to delete this?")) {
	   location.href='?sect=messages&thread_id='+id+'&action=delete';
	 } else { 
	 } 
	 */
}

function checkedAll (frm1) {
	
	var aa= document.getElementById(frm1);
	 if (checked == false) {
           checked = true
      } else {
           checked = false
      }
	for (var i =0; i < aa.elements.length; i++) {
	    aa.elements[i].checked = checked;
	  }
 }

  function restoreSelected (frm) {
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
		var r = jConfirm("Are you sure want to restore selected message?", null, function(r){
			if(r==true){
				$("#action").val('restore');
				document.getElementById(frm).submit();
				return true;
			}
		});
	} else {
		jAlert('Please select atleast one record');
		return false;
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
		var r = jConfirm('Are you sure you want to permanently delete this message?', null, function(r){
			if(r==true){
				$("#action").val('delete');
				document.getElementById(frm).submit();
				return true;
			}
		});
	} else {
		jAlert('Please select atleast one record');
	return false;
}
 }
 
 </script>
<body id="dt_example">
<div class="demo_jui" style="width:100%; float:left;" >
<?php 
$viewType = "";
if(isset($_GET['view']) && $_GET['view'] == 'workflow'){ 
	$viewType = "&view=workflow";	
?>
	
	<div id="leftNav" style="width:250px;float:left;">
		<?php include 'side_menu.php';?>
	</div>
	<?php }?>
<?php //code for showing tabs
		if($_GET['view']=='workflow'){
		?>
			<nav id="menu" style="margin-top:15px;margin-left:5px;">
		  <ul class="nav">
			<li><a href="pms.php?sect=messages&view=workflow"   class="active">PMB</a></li>
			<li><a href="pms.php?sect=drawing_register&view=workflow">Drawing Register</a></li>
		 </ul>
		</nav>

		<!-- we're done with the tabs, onto panes now -->

		<section id="tab_panes">
			<div class="tab_pane active">
				
			</div>
			
			<!-- we'll copy/paste the other panes -->
			<div class="tab_pane">
				
			</div>
		</section>	
		<?php } ?>
<div class="GlobalContainer clearfix">
<?php include 'message_side_menu.php'; ?>
<div class="MailRight"  <?php if($_GET['view']=='workflow'){echo "style='width:64%;'";}?>>

   <div class="MailRightHeader">
    	<ul style="margin-left:-28px !important;">
		<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
        	<li><a href="#" onClick="restoreSelected('trash');"><img src="images/restore.png" width="93" height="34" alt="Restore" /></a></li>
            <li><a href="#" onClick="deleteSelected('trash');"><img src="images/delete1.png" width="93" height="34" alt="Delete" /></a></li>
		<?php }?>
		<?php if(!in_array($_SESSION['userRole'], $permArrayTwo)){?>
            <!--li><a href="?sect=compose"><img src="images/compose.png" width="103" height="34" alt="Compose" /></a></li-->
		<?php }?>
            <!--<li><a href="?sect=compose"><img src="images/reply.png" width="81" height="34" alt="Delete" /></a></li>-->
        </ul>
        <h3 style="color:#000000; margin-top:10px; margin-right:190px; float:right;">Project Name : <?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></h3>
    </div>
	
	<?php  $query="SELECT um.user_id,
	                        user_message_id,
	                        um.from_id, 
							um.message_id, 
							um.type,
							um.thread_id,
							um.inbox_read, 
							m.title, 
							m.message, 
							m.sent_time
					FROM
						pmb_user_message as um,
						pmb_message m
					WHERE
						um.message_id = m.message_id AND
						um.is_deleted = 1 AND
						um.type != 'delete' AND
						um.project_id = '".$projectId."' AND
						um.user_id = '".$_SESSION['ww_builder_id']."'
					ORDER BY
						m.sent_time DESC";
	        $trashquery = mysql_query($query); ?>

		<form name="trash" action="" method="post" id="trash">
    <table cellpadding="0" cellspacing="0" border="0" class="display" id="trashData" width="100%">
  
      <thead>
		<tr>
	        <th width="2%"><input name="allbox" type="checkbox" value="" onClick="checkedAll('trash');" /></th>
			<th width="10%">From</th>
			<th width="10%">To</th>
			<th>Subject</th>
			<th width="20%">Time</th>
		<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
            <th width="10%">Action</th>
		<?php }?>
		</tr>
	   </thead>
	   <tbody>
		<?php if(intval(mysql_num_rows($trashquery))>0) { ?>
		<?php 
	//We display the list of unread messages
while($trash = mysql_fetch_array($trashquery)) {
		if($trash['type']=='inbox') {
		   $fromuser=getuserdetails($trash['from_id']);
		   $touser=getuserdetails($trash['user_id']);
		 } else {
		   $fromuser=getuserdetails($trash['user_id']);
		   $touser=getuserdetails($trash['from_id']);
		 }
?>
	 <tr class="odd gradeA">
    		 <td><input name="from[]" type="checkbox" value="<?php echo $trash['user_message_id']; ?>" /></td>
			<td><a href="?sect=view_message&id=<?php echo base64_encode($trash['user_message_id'])?>&type=trash"><?php echo $fromuser['user_name']; ?></a></td>
			<td><?php echo $touser['user_name']; ?></td>
			<td><a href="?sect=view_message&id=<?php echo base64_encode($trash['user_message_id'])?>&type=trash"><?php echo htmlentities($trash['title'], ENT_QUOTES, 'UTF-8'); ?></a></td>
			<td align="center"><?php echo date('d/m/Y H:i:s' ,strtotime($trash['sent_time'])); ?></td>
		<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
            <td class="center"><a onClick="restoreMessage(<?php echo $trash['user_message_id']; ?>);" href="#" style="display:inline;" ><img src="images/restore_icon.png" width="16" height="16" alt="Restore" title="Restore" /></a>
            <a onClick="delMessage(<?php echo $trash['user_message_id']; ?>);" href="#" style="display:inline;" ><img src="images/del.png" width="16" height="16" alt="Restore" title="Restore" /></a>
			</td>
		<?php }?>
	 </tr>
<?php } //If there is no unread message we notice it
} else { ?>
	<tr>
    	<td colspan="5" class="center">You have no  messages.</td>
    </tr>
<?php } ?>
</tbody>
 </table>
 <input type="hidden" name="form_type" value="trash">
 <input type="hidden" name="action" id="action" value="">
 </form>
</div>
</div>
<style>
div.content_container{
	width:100% !important;
}
</style>
 <script>
// On to the interactiveness now :)

$(function() {

	$('.nav a').on('click', function() {
	
		var $el = $(this);
		var index = $('.nav a').index(this);
		var active = $('.nav').find('a.active');
		
		/* if a tab other than the current active
		tab is clicked */
		
		if ($('nav a').index(active) !== index) {
			
			// Remove/add active class on tabs
			active.removeClass('active');
			$el.addClass('active');
			
			
			// Remove/add active class on panes
			$('.tab_pane.active')
				.hide()
				.removeClass('active');
			$('.tab_pane:eq('+index+')')
				.fadeIn()
				.addClass('active');
			
			// we can also add some quick fading effects
			
			// now that's awesome! you got
			// fancy stylish css3 tabs for your
			// next project ;)
			
		}
	});

}());
</script>
