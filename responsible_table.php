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
	<div class="content_hd1" style="background-image:url(images/responsible.png);"></div>
	<? if($id!=''){?>
	<form method="post" action="?sect=add_responsible">
		<input type="hidden" value="<?=$id?>" name="id" id="id" />
		<input type="submit" class="add_new" style="padding-bottom:35px; margin:0 auto;" value="" />
	</form>
	<? }?>
	<div class="demo_jui">
		<?php
	// comes from builder
	if($id!=''){
		$builder_id = $_SESSION['ww_builder_id'];
		$q = "SELECT * FROM ".RESPONSIBLES." r 
			  LEFT JOIN ".PROJECTS." p ON p.project_id = r.project_id 
			  WHERE r.project_id = '$id' AND r.builder_id = '$builder_id' ";
		$hd = "<th>Project Name</th>
				<th>Issued To Contact Name</th>
				<th>Issued To Company</th>
				<th>Issued To Phone</th>
				<th>Issued To Email</th>
				<th>Action</th>";	  
	}else{
	?>
		<script>window.location.href="<?=SHOW_PROJECTS?>";</script>
		<?php	
	}	
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
				<td>".stripslashes($f['pro_name'])."</td>
				<td>".stripslashes($f['resp_full_name'])."</td>
				<td>".stripslashes($f['resp_comp_name'])."</td>				
				<td>".stripslashes($f['resp_phone'])."</td>
				<td>".stripslashes($f['resp_email'])."</td>
				<td align='center'>
					<a href='?sect=edit_responsible&id=".base64_encode($f['resp_id'])."'><img src='images/edit.png' border='none' /></a>
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
