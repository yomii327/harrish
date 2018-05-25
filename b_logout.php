<?php
session_start();
//require_once'includes/functions.php';
require_once'includes/commanfunction.php';
$object= new COMMAN_Class();
// Remove cookies
$object->removeCookies();

unset($_SESSION['ww_is_builder']);
unset($_SESSION['ww_builder_id']);
unset($_SESSION['ww_builder_full_name']);
unset($_SESSION['ww_comp_name']);
unset($_SESSION['ww_builder_user_name']);
unset($_SESSION['ww_builder_plain_pswd']);
unset($_SESSION['ww_builder_email']);
unset($_SESSION['ww_builder']);
unset($_SESSION['ww_logged_in_as']);
unset($_SESSION['ww_builder_fk_c_id']);
session_destroy();
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>