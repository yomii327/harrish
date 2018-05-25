<?php
session_start();
require_once'includes/functions.php';
header("location:pms.php?sect=login");
if(isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company'] == 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=COMPANY_DASHBOARD?>";
</script>
<?php
}elseif(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=BUILDER_DASHBOARD?>";
</script>
<?php
}elseif(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=OWNER_DASHBOARD?>";
</script>
<?php
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=SITE_NAME?></title>
<link rel="icon" href="images/ww_favicon.gif" type="image/gif" >

<link href="style.css" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="style_ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<link href="menu_style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" language="javascript" src="datatable/media/js/jquery.js"></script>
</head>
<body>
<?php include'includes/header.php';?>
<div id="middle">
	<div class="content_container">
		<div class="login_hd" style="margin: 13px 0;"><img src="images/login_hd.png" alt="Click To Login" border="0" /></div>
		<table width="100%" align="left" border="0" style="margin-top:22px;">
			<tr>
				<td align="right"><a href="pms.php?sect=company"><img src="images/company_login.png" width="297" height="160" border="0" /></a></td>
				<td align="left"><a href="pms.php?sect=builder"><img src="images/developer_login.png" width="297" height="160" border="0" /></a></td>
			</tr>
			<tr>
				<td align="center" colspan="2"> <a href="pms.php?sect=tenant"><img src="images/tenant_login.png" width="297" height="160" border="0" /></a></td>
			</tr>
		</table>	
	</div>
</div>
<?php include'includes/footer.php';?>
</body>
</html>