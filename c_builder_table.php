<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php'; 
include_once("includes/commanfunction.php");
session_start();
$obj = new COMMAN_Class(); ?>
</head>
<body id="dt_example">
<div id="container">
<div class="content_hd1" style="background-image:url(images/builders_hd.png);"></div>
<a href="?sect=c_add_builder"><div class="add_new" style="margin:0 auto;"></div></a><br clear="all" />
    <div class="demo_jui">
<?php $q = "SELECT * FROM user WHERE is_deleted IN (0,2)";//echo $q; die;
		if(isset($_SESSION['user_update'])){ ?>
   <div align="center" class="success_r" style="width:250px;margin-bottom:4px;margin-left:325px;margin-top:-35px;"><p><?php echo $_SESSION['user_update'];?></p></div>
   <?php unset($_SESSION['user_update']); }
   		if(isset($_SESSION['user_remove'])){ ?>
   <div align="center" class="success_r" style="width:250px;margin-bottom:4px;margin-left:325px;margin-top:-35px;"><p><?php echo $_SESSION['user_remove'];?></p></div>
   <?php unset($_SESSION['user_remove']); } ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="display" id="example">
			<thead>
                <tr>
					<th nowrap="nowrap">User's Full Name</th>
					<th>Company Name</th>
					<th>Email Address</th>
					<th>Username</th>
					<th>Password</th>
					<th>User Type</th>
					<th>Status</th>
					<th>Action</th>
                </tr>
            </thead>
			<tbody>
	<?php $r=mysql_query($q);
		while($f=mysql_fetch_assoc($r)){
			echo "<tr class='gradeA'>
				<td>".stripslashes($f['user_fullname'])."</td>
				<td>".stripslashes($f['company_name'])."</td>
				<td>".stripslashes($f['user_email'])."</td>
				<td>".stripslashes($f['user_name'])."</td>
				<td>".stripslashes($f['user_plainpassword'])."</td>
				<td>".stripslashes($f['user_type'])."</td>"; ?>
				<td><?php echo (isset($f['is_deleted']) && $f['is_deleted']==0)?'Activated':'Deactivated'; ?></td>
				<?php
				echo "<td align='center'>
					<a  title='Click to edit Manager' href='?sect=c_remove_builder&id=".base64_encode($f['user_id'])."'><img src='images/edit.png' border='none' /></a>
				</td>
			    </tr>";
		} ?>
			</tbody> 
		</table>	
    </div>
    <div class="spacer"></div>
</div>
</body>
</html>
