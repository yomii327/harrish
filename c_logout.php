<?php
session_start();
require_once'includes/functions.php';
unset($_SESSION['ww_is_company']);
unset($_SESSION['ww_c_id']);
unset($_SESSION['ww_c_full_name']);
unset($_SESSION['ww_c_comp_name']);
unset($_SESSION['ww_c_user_name']);
unset($_SESSION['ww_c_plain_pswd']);
unset($_SESSION['ww_c_email']);
unset($_SESSION['ww_logged_in_as']);
unset($_SESSION['ww_company']);	
unset($_SESSION['post_array']);	
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>