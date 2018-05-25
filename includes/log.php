<style>
.info, .success {
	font-family:Arial, Helvetica, sans-serif;
	font-size:13px;
	border: 1px solid;
	margin: 10px 0px;
	padding:15px 10px 15px 50px;
	background-repeat: no-repeat;
	background-position: 10px center;
}
.info {
	color: #00529B;
	background-color: #BDE5F8;
	background-image: url('images/info.png');
	width:340px;
	text-align:center;
	margin:0 auto;
	text-shadow:none;
}
.success {
	color: #4F8A10;
	background-color: #DFF2BF;
	background-image:url('images/success.png');
	width:340px;
	text-align:center;
	margin:0 auto;
	text-shadow:none;
}
</style>
<?php
//http://localhost/wiseworker/defectid/pms.php?sect=log&type=apply_now&flag=1
if($_GET['type'] == 'apply_now'){
	if($_GET['flag'] == 1){
		$log = "<div class='success'>Congratulations, your account activated successfully!<br />An email with lognin information has sent to the builder.</div>";
	}else{
		$log = "<div class='info'>This account has already been activated!</div>";		
	}
}
?>
<div class="content_center">
	<div class="content_hd" style="background-image:url(images/account_confirmation.png);"></div>
	<div class="signin_form">
		<?=$log?>
	</div>
</div>
