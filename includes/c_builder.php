<?php
if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
?>
<div class="big_container" style="width:100%;">
	<?php include'c_builder_table.php';?>
</div>