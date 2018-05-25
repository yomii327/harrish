<?php
session_start();
//require_once'includes/functions.php';
require_once'includes/commanfunction.php';
$object= new COMMAN_Class();
// Remove cookies
$object->removeCookies();

unset($_SESSION['ww_is_builder']);
unset($_SESSION['ww_owner_id']);
unset($_SESSION['ww_owner_full_name']);
unset($_SESSION['ww_owner_user_name']);
unset($_SESSION['ww_owner_plain_pswd']);
unset($_SESSION['ww_owner_email']);
unset($_SESSION['ww_owner']);
unset($_SESSION['ww_logged_in_as']);
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>