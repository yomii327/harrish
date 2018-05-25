<?php
//echo $_SESSION['ww_is_company']; die;
if(!isset($_SESSION['ww_is_builder'])){
	
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
?>
<div class="big_container" style="width:100%;">
	<?php include'i_report_table.php';?>
</div>
