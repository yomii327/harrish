<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php'; ?>
<?php
if(isset($_POST['id']))
	$id=$_POST['id'];
else
	$id = '';
?>
</head>
<body id="dt_example">
<div id="container">
	<div class="content_hd1" style="background-image:url(images/assign_to.png); height:82px;"></div>

	<form method="post" action="?sect=add_assign_to">
		<input type="hidden" value="<?=$id?>" name="id" id="id" />
		<input type="submit" class="add_new" style="padding-bottom:35px; margin:0 auto;" value="" />
	</form>

	<div class="demo_jui">
		<?php
	// comes from builder
	$resp_id = $_SESSION['ww_resp_id'];
	$q = "SELECT * FROM ".ASSIGN." WHERE resp_id = '$resp_id' ";
	$hd = "<th>Assign To Company</th>
			<th>Assign To Contact Name</th>
			<th>Assign To Phone</th>
			<th>Assign To Email</th>
			<th>Edit</th>";
	?>
		<table width="980" cellpadding="0" cellspacing="0" border="0" class="display" id="example">
			<thead>
				<tr>
					<?=$hd?>
				</tr>
			</thead>
			<tbody>
				<?php
		$r=mysql_query($q);
		while($f=mysql_fetch_assoc($r)){
			echo "<tr class='gradeA'>
				<td>".stripslashes($f['assign_comp_name'])."</td>
				<td>".stripslashes($f['assign_full_name'])."</td>
				<td>".stripslashes($f['assign_phone'])."</td>
				<td>".stripslashes($f['assign_email'])."</td>
				<td align='center'>
					<a href='?sect=edit_assign_to&id=".base64_encode($f['assign_id'])."'><img src='images/edit.png' border='none' /></a>
				</td>
			    </tr>";
		}
	?>
			</tbody>
		</table>
	</div>
	<div class="spacer"></div>
</div>
</body>
</html>
