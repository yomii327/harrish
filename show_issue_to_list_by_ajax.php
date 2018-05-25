<?php session_start();
$builder_id = isset($_SESSION['ww_c_id'])?$_SESSION['ww_c_id']:$_SESSION['ww_builder_id'];
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();

// Delete issue to contact
if(isset($_REQUEST['contId'])){
	 $builder_id=$_SESSION['ww_builder_id'];
	 $update="UPDATE master_issue_to_contact SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE contact_id = '".$_REQUEST['contId']."' ";
	mysql_query($update);
	
    $update="UPDATE inspection_issue_to SET is_deleted=1,last_modified_date=now(),last_modified_by='".$builder_id."'
	 WHERE master_contact_id = '".$_REQUEST['contId']."'";
	mysql_query($update);		
	$_SESSION['successMsg'] = 'Issued to contact deleted successfully.';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link href="style.css" rel="stylesheet" type="text/css" />
<style>
body, #projectIssueToData{ color:#000000; }
</style>
</head>
<body id="dt_example">
<!-- <a href="#" onClick="addNewIssueToContact(<?php //echo trim($_REQUEST['issueToId']); ?>);"><div style="margin-right:10px;margin-bottom:10px; margin-top:0px;" class="add_new"></div></a> -->
<a href="javascript:addNewIssueToContact(<?php echo trim($_REQUEST['issueToId']); ?>);" style="margin-right:10px;margin-bottom:10px; margin-top:0px;float:right" class="green_small">Add New</a>
<div id="container">
	<h3 style="color:#000000;">Issue To Contacts</h3>
	<div class="demo_jui" style="width:870px; float:left;" >
		<table cellpadding="0" cellspacing="0" border="0" class="display" id="issueToData" width="100%">
			<thead>
    <tr>
      <th align="left" nowrap="nowrap">Company Name</th>
      <th align="left">Contact Name</th>
      <th align="left">Phone</th>
      <th align="left">Email</th>
      <th align="left">Tags</th>
      <th align="left">Action</th>
    </tr>
  </thead>
			<tbody>
				<tr>
					<td colspan="4" class="dataTables_empty">Loading data from server</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<br clear="all" />
</body>
</html>