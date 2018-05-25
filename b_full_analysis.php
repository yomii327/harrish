<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
?>
<div class="big_container" style="width:100%;" align="center">
	
	<?php include'b_project_analysis_table.php';?>
</div>
