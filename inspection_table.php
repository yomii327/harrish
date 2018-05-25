<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<?php include'data-table.php'; ?>
<?php
if(isset($_REQUEST['id']))
{
	$id=$_REQUEST['id'];
	$_SESSION['project_id']=$id;
}
else
	$id = '';
	
	

//echo $id; die;	
?>
</head>
<body id="dt_example">
<div id="container">


	<?php if($_SESSION['ww_is_builder']==1){?>
	<div class="content_hd1" style="background-image:url(images/inspectors.png);"></div>
	<?php }elseif($_SESSION['ww_is_builder']==0){?>
	<div class="content_hd1" style="background-image:url(images/pro_sub_loc.png);"></div>
	<?php } ?>
	<? if($id!=''){?>
	<form method="post" action="?sect=add_sub_loc">
		<input type="hidden" value="<?=$id?>" name="id" id="id" />
		<input type="submit" class="add_new" style="padding-bottom:35px; margin:0 auto;" value="" />
	</form>
	<? }?>
	<div class="demo_jui">
		<?php
	// comes from builder
	if($id!=''){
		$builder_id = $_SESSION['ww_builder_id'];
		$q = "SELECT *, o.id as ow_id, p.project_id as project_id FROM ".OWNERS." o 
			  LEFT JOIN ".PROJECTS." p ON p.project_id = o.ow_project_id 
			  WHERE p.project_id = '$id' AND p.user_id = '$builder_id' ";
			 
		$hd = "<th>Project Id</th>
				<th>Project Name</th>
				<th>Inspactor</th>
				<th>Phone</th>
				<th>Email</th>
				<th>Edit</th>";	  
	}elseif($_SESSION['ww_is_builder']==0){
		$owner_id = $_SESSION['ww_owner_id'];
		$q =  "SELECT *, p.id as project_id FROM ".OWNERS." o 
			  LEFT JOIN ".PROJECTS." p ON p.id = o.ow_project_id 
			  LEFT JOIN ".BUILDERS." b ON p.user_id = b.user_id 
			  WHERE o.id = '$owner_id' ";
			  
		$hd = "<th>Project Name</th>
				<th>Add Inspection</th>";
	}else{
	?>
		<script>window.location.href="<?=SHOW_PROJECTS?>";</script>
		<?php	
	}	
	?>
		<div style="margin-left:300px;margin-right:300px;text-align:center;"><?php if(isset($_SESSION['edit_remove_inspector']['success'])) { echo '<br><br><div class="success_r" style="height:45px;"><p>'.$_SESSION['edit_remove_inspector']['success'].'</p></div>' ; unset($_SESSION['edit_remove_inspector']['success']);}?></div>
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
			if($_SESSION['ww_is_builder']==1){
			echo "<tr class='gradeA'>
				<td>".$f['pro_code']."</td>
				<td>".stripslashes($f['pro_name'])."</td>
				<td>".stripslashes($f['owner_full_name'])."</td>
				<td>".stripslashes($f['phone'])."</td>
				<td>".stripslashes($f['email'])."</td>
				<td align='center'>
					<a href='?sect=edit_sub_loc&id=".base64_encode($f['ow_id'])."'><img src='images/edit.png' border='none' /></a>
				</td>
			    </tr>";
			}elseif($_SESSION['ww_is_builder']==0){
			echo "<tr class='gradeA'>
				<td>".stripslashes($f['project_name'])."</td>
				<td align='center'>
					<a href='?sect=add_defect&id=".base64_encode($f['project_id'])."'><img src='images/edit.png' border='none' /></a>
				</td>
			    </tr>";
			}
		}
	?>
			</tbody>
		</table>
	</div>
	<div class="spacer"></div>
</div>
</body>
</html>
