<?php //include('includes/commanfunction.php');
//ini_set('display_errors',1);
$redirect = HOME_SCREEN;
if(!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])){ 
	if(isset($_GET['byEmail']) && $_GET['byEmail']>=1){
		$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
		if ($_SERVER["SERVER_PORT"] != "80"){
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		}else{
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		$_SESSION['inspViewPath'] = $pageURL;
	}
	if(isset($_GET['byEmail']) && $_GET['byEmail']==1){	
		$redirect.= '/pms.php?sect=builder';	
	}elseif(isset($_GET['byEmail']) && $_GET['byEmail']==2){
		$redirect.= '/pms.php?sect=tenant';	
	}
?>
<script language="javascript" type="text/javascript">window.location.href="<?=$redirect?>";</script>
<?php } ?>
<div class="big_container" style="width:100%;">
	<?php include'message_details.php';?>
</div>