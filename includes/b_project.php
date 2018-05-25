<?php
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
?>
<div id="middle" style="padding-top:120px; padding-bottom:80px;margin-left:250px">
	<div class="content_container"><a href="?sect=show_project">
		<div class="box" style="background-image:url(images/show_pro.png); width:376px; height:229px;"></div>
		</a> <!--<a href="?sect=add_project">
		<div class="box" style="float:right; background-image:url(images/add_pro.png); width:376px; height:229px;"></div>
		</a>--> </div>
</div>