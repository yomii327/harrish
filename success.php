<?php
session_start();
require_once'includes/functions.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=SITE_NAME?></title>
<link rel="icon" href="images/pms_favicon.gif" type="image/gif" >
<link href="style.css" rel="stylesheet" type="text/css" />
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>

<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/create_account.js"></script>
<script language="javascript" type="text/javascript">

/*
function startAjax(){
	var bus_l1=document.getElementById('bus_line1').value;
	var fname=document.getElementById('fname').value;
	var compname=document.getElementById('compname').value;
	var email=document.getElementById('email').value;
	var mobile=document.getElementById('mobile').value;
	
	var bus_l1=document.getElementById('bus_line1').value;
	var bus_l2=document.getElementById('bus_line2').value;
	var bus_suburb=document.getElementById('bus_suburb').value;
	var bus_state=document.getElementById('bus_state').value;
	var bus_post=document.getElementById('bus_post').value;
	var bus_country=document.getElementById('bus_country').value;
	
	var bil_l1=document.getElementById('bil_line1').value;
	var bil_l2=document.getElementById('bil_line2').value;
	var bil_suburb=document.getElementById('bil_suburb').value;
	var bil_state=document.getElementById('bil_state').value;
	var bil_post=document.getElementById('bil_post').value;
	var bil_country=document.getElementById('bil_country').value;
	
	if(fname!='' && compname!='' && email!='' && mobile!='' && bus_l1!='' && bus_suburb!='' && bus_state!='' && bus_post!='' &&
	   bus_country!='' && bil_l1!='' && bil_suburb!='' && bil_state!='' && bil_post!='' && bil_country!=''){
		document.getElementById('apply_now_process').style.visibility = 'visible';
		document.getElementById('apply_now_response').style.visibility = 'hidden';
		return true;
	}else{
		var err = '<span class="emsg">* represent required fileds!<\/span><br/><br/>';
		document.getElementById('apply_now_response').innerHTML = err;
		return false;
	}
	
	document.getElementById('apply_now_process').style.visibility = 'visible';
	document.getElementById('apply_now_response').style.visibility = 'hidden';
	return true;
}
*/
function stopAjax(success){
	
	var result = '';
	if(success == 0){
		result = '<span class="emsg">Email id already exist!<\/span><br/><br/>';
		
	}else if(success == 1){
		result = '<span class="emsg">Invalid email address!<\/span><br/><br/>';
	}else if(success == 2){
		result = '<span class="emsg">Invalid mobile no.!<\/span><br/><br/>';
	}else if(success == 3){
		result = '<p><span class="emsg">Invalid post code!<\/span><br/><br/><\/p>';
	}else if(success == 4){
		result = '<p><span class="msg">Request send successfully!<\/span><br/><br/><\/p>';
		document.getElementById('apply_now').style.display = 'none';
		document.getElementById('request_send').style.display = 'block';
	}
	document.getElementById('apply_now_process').style.visibility = 'hidden';
	document.getElementById('apply_now_response').innerHTML = result;
	document.getElementById('apply_now_response').style.visibility = 'visible';
	
	return true;
}

function billAddress(){
	if(document.getElementById('bil_address').checked == true){
		document.getElementById('bil_line1').value=document.getElementById('bus_line1').value;
		document.getElementById('bil_line2').value=document.getElementById('bus_line2').value;
		document.getElementById('bil_suburb').value=document.getElementById('bus_suburb').value;
		document.getElementById('bil_state').value=document.getElementById('bus_state').value;
		document.getElementById('bil_post').value=document.getElementById('bus_post').value;
		document.getElementById('bil_country').value=document.getElementById('bus_country').value;
	}else if(document.getElementById('bil_address').checked == false){
		document.getElementById('bil_line1').value='';
		document.getElementById('bil_line2').value='';
		document.getElementById('bil_suburb').value='';
		document.getElementById('bil_state').value='';
		document.getElementById('bil_post').value='';
		document.getElementById('bil_country').value='';
	}
}
</script>
<!-- Ajax Post -->
<style type="text/css">
.msg_class{
	display:block;
	}
.msg_er_class{
	display:none;
	}	
</style>
</head>
<body>
<?php unset($_SESSION['success']);
session_destroy();

 ?>

<?php include'includes/header.php';

?>
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="text-align:center; margin-top:150px; margin-bottom:50px;" class="msg_class">
		<img src="images/request_sent.png" />
       
	</div>
    
</div>
<?php include'includes/footer.php';?>
</body>
</html>
