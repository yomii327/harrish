<?php
//path to directory to scan
$images=array();
$directory = "report_pdf/".$_SESSION['ww_builder_id']."/";
 
//get all image files with a .jpg extension.
$images = glob($directory . "*.pdf");
$images=str_replace($directory,'',$images);
rsort($images,SORT_NUMERIC);
?>
<!-- styles needed by jScrollPane - include in your own sites -->
<link type="text/css" href="style/jquery.jscrollpane.css" rel="stylesheet" media="all" />
<style type="text/css" id="page-css">
/* Styles specific to this particular page */
.scroll-pane {
	width: 100%;
	height: 420px;
	overflow: auto;
}
</style>
<!-- the mousewheel plugin -->
<script type="text/javascript" src="script/jquery.mousewheel.js"></script>
<!-- the jScrollPane script -->
<script type="text/javascript" src="script/jquery.jscrollpane.min.js"></script>
<script type="text/javascript" id="sourcecode">
	$(function()
	{
		$('.scroll-pane').jScrollPane();
	});
</script>
<div class="content_center">
	<div class="content_hd" style="background-image:url(images/pdf_reports_folder.png);"></div>
	<div style="font-family:Arial, Helvetica, sans-serif; text-align:left; padding-top:20px; float:left; width:100%;">
		<div class="scroll-pane" style=" border:1px solid; background-color:#333333; padding-top:5px;">
			<?php for($p=0;$p<sizeof($images);$p++){ ?>
				<img src="images/pdf_icon.png" />
				<?="<a href='".$directory.$images[$p]."' target='_blank' style='color:#FFFFFF; text-decoration:none;'> Report_".$images[$p]."</a>";?>
				<br /><br />
			<?php } ?>
		</div>
	</div>
</div>
