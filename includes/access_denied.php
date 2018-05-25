<?php
$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$pi = pathinfo($refering_url);
$u = str_replace('pms.php','',$pi['basename']);
?>
<div class="content_center">
	<div class="content_hd" style="background-image:url(images/access_denied.png);"></div>
	<div style="font-family:Arial, Helvetica, sans-serif; text-align:center;"></div>
</div>
