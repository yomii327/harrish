<?php
$s = isset($_REQUEST['sect']) ? $_REQUEST['sect'] : '';
if(isset($_SESSION['ww_is_builder']))
	$f = $_SESSION['ww_is_builder'];
else
	$f = '';
?>
<?php
$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$pi = pathinfo($refering_url);
$u = str_replace('pms.php','',$pi['basename']);
?>
<link rel="stylesheet" type="text/css" href="menu_style.css">
<div id="top">
	<div class="header_container">
		<div class="logo"><img src="images/logo.png" border="none" alt="Logo" /></div>
	<?php if(isset($_SESSION['ww_logged_in_as'])){?>
	<div class="welcome">Hi, <?=$_SESSION['ww_logged_in_as']?><a href="o_logout.php"><img src="images/logout_btn.png" width="63" height="24"  align="absmiddle" hspace="11"/></a></div>
	<? }?>        
	</div>
	<!--Navigation-->
	</div>
</div>