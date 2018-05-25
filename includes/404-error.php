<?php
$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$pi = pathinfo($refering_url);
$u = str_replace('pms.php','',$pi['basename']);
?>
<div class="content_center">
	<div class="content_hd" style="background-image:url(images/url_not_found.png);"></div>
	<div style="font-family:Arial, Helvetica, sans-serif; text-align:center;"><br /><br /><br /><br />Click <a href="<?=$u?>">here</a> to go back.</div>
</div>
