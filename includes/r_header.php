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
	<div class="welcome">Hi, <?=$_SESSION['ww_logged_in_as']?><a href="r_logout.php"><img src="images/logout_btn.png" width="63" height="24"  align="absmiddle" hspace="11"/></a></div>
	<? }?>        
	</div>
	<!--Navigation-->
						<div id="nav">
								<ul>
<?php
if($s=='r_dashboard' || $s=='r_defect' || $s=='r_edit_defect' || $s=='assign_to' || $s=='add_assign_to' || $s=='edit_assign_to'){
?>
		<li><a href="?sect=r_dashboard"><img src="images/myprofile.png" width="28" height="29" hspace="5" align="absmiddle" />My Profile</a></li>
		<li><a href="?sect=r_defect"><img src="images/reports.png" width="23" height="29" hspace="5" align="absmiddle" />Reports</a></li>				
		<li><a href="?sect=assign_to"><img src="images/assign.png" width="23" height="29" hspace="5" align="absmiddle" />Assign To</a></li>
<?php
}elseif($s=='issue_photo'){
?>
	    		<li style="position: relative; float: left; width: 48px; height: 48px; margin-right: 0px; left: 0px; top: 0px;">
	    			<a href="<?=$u?>"><img fmtooltip="Back" src="menu/index_data/back.png" alt="Back"></a>
				<div class="FMTooltipClass" style="position: absolute; z-index: 8; opacity: 0; display: none;">Back</div></li>
<?php
}else{
?>
	    		<li style="position: relative; float: left; width: 48px; height: 48px; margin-right: 0px; left: 0px; top: 0px;">
	    			<a href="index.php"><img fmtooltip="Home" src="menu/index_data/home.png" alt="Home"></a>
				<div class="FMTooltipClass" style="position: absolute; z-index: 8; opacity: 0; display: none;">Home</div></li>
<?php } ?>	</ul>
    	</div>
		
	</div>
	

</div>