<?php
#error_reporting(E_ALL);
#ini_set('display_errors', '1');
session_start();
include('commanfunction.php');
$object = new COMMAN_Class();

$userId = $_SESSION['ww_builder']['user_id'];
$msg = "";
if(isset($_POST['saveSignature'])){
	$signature = $_POST['signature'];
	if(get_magic_quotes_gpc()) {
		$signature = stripslashes($signature);
	}
	$signature = mysql_real_escape_string(nl2br(htmlentities($signature, ENT_QUOTES, 'UTF-8')));
	$updateQRY = "UPDATE user SET
						pmb_signature = '".$signature."'
				WHERE
					user_id = ".$userId;
	mysql_query($updateQRY);
	$msg = "Signature update successfully";
}

$userData = $object->selQRYMultiple('user_name, user_fullname, user_email, pmb_signature', 'user', 'is_deleted = 0 AND user_id = '.$userId);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link href="css/email.css" rel="stylesheet" type="text/css" />
<style>
label { color:#000000;}
.textEditer{ color:#000; padding-left:10px; }
.nicEdit-main{ outline:none; }
.Compose .error{ margin-left:20px; }
span.reqire{
 /*	display:block;*/
}
.chzn-drop{ text-transform:capitalize; }
#messageDetails{ color:#000; }
#imageName{ float:left; margin-left:20px; margin-top:10px;  width: 410px; }
#imageName span{ padding-left:15px; }
#imageName a{ cursor:pointer; padding-right:5px; }
</style>
<body>
<div class="GlobalContainer clearfix">
<?php include 'message_side_menu.php'; ?>
	<div class="MailRight">
		<div class="MailRightHeader">
			<h2 style="color:#000000; margin-top:10px; margin-left:10px; float:left;">My Profile</h2>
			<h3 style="color:#000000; margin-top:10px; margin-right:190px; float:right;">Project Name : <?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></h3>
		</div>
		<?php if($msg != ""){?>
		<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;">
			<div class="success_r" id="outPutResult" style="height:35px;width:740px;">
				<p id="outPutResultPara"><?=$msg?></p>
			</div>		
		</div>
		<?php }?>
		<div class="Compose clearfix" style="color:#050505;">
		<form action="" method="post">
			<table style="margin-left:20px;" cellpadding="5" cellspacing="15">
				<tr>
					<td>User Name</td>
					<td><?=$userData[0]['user_name']?></td>
				</tr>
				<tr>
					<td>Full Name</td>
					<td><?=$userData[0]['user_fullname']?></td>
				</tr>
				<tr>
					<td>Email Address</td>
					<td><?=$userData[0]['user_email']?></td>
				</tr>
				<tr>
					<td>Signature</td>
					<td><textarea cols="15" rows="5" name="signature" id="signature"><?=$userData[0]['pmb_signature']?></textarea></td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<button type="submit" class="right" id="saveSignature" name="saveSignature" style="margin-left:400px;">Submit</button>
					</td>
				</tr>
			</table>
		</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="js/nicEdit.js"></script>
<script type="text/javascript">
	bkLib.onDomLoaded(function() {
	new nicEditor({iconsPath : 'js/nicEditorIcons.gif',buttonList : ['save','bold','italic','underline','left','center','right','justify','ol','ul','indent','outdent','forecolor','bgcolor']}).panelInstance('signature');
	});
</script>
<style>div.content_container{ width:100% !important; }</style>