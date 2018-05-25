<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php'; ?>
</head>
<body id="dt_example">
<div id="container">
<div class="content_hd1" style="background-image:url(images/defects_list_hd.png);"></div>
<a href="?sect=add_defects_list"><div class="add_new" style="margin:0 auto;"></div></a>
    <div class="demo_jui">
	<?php
	$builder_id = $_SESSION['ww_builder_id'];
	$q="SELECT * FROM ".DEFECTSLIST." WHERE fk_b_id='$builder_id' ";
	?>
		<table width="980" cellpadding="0" cellspacing="0" border="0" class="display" id="example">
			<thead>
                <tr>
					<th>Defect Title</th>
					<th>Edit</th>
                </tr>
            </thead>
			<tbody>
	<?php
		$r=mysql_query($q);
		while($f=mysql_fetch_assoc($r)){
			echo "<tr class='gradeA'>
				<td>".stripslashes($f['dl_title'])."</td>
				<td align='center'>
					<a href='?sect=edit_defects_list&id=".base64_encode($f['dl_id'])."'><img src='images/edit.png' border='none' /></a>
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
