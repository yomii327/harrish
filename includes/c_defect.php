<?php
if(!isset($_SESSION['ww_is_company'])){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
?>
<div class="big_container" style="width:100%;">
	<?php include'i_defect_table.php';?>
</div>
