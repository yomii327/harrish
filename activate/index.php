<?php
require_once'../includes/functions.php';
$obj = new DB_Class();

$flag = 0;
$path = pathinfo($_SERVER['REQUEST_URI']);
$path['basename'];

$code = base64_decode($_GET['code']);
if(base64_decode($_GET['type']) == 'company'){
	
	$q = "SELECT * FROM ".COMPANIES." WHERE verify_code  = '".$code."' AND active = 0";
	$obj->db_query($q);
	
	if($obj->db_num_rows($obj->db_query($q)) > 0){
			
		// create username & password.
		$username=$obj->rendom(7);
		$plain_pswd=$obj->rendom(9);
		$password = md5($plain_pswd);	
			
		$q = "UPDATE ".COMPANIES." SET active = '1', c_user_name = '$username', password = '$password', c_plain_pswd = '$plain_pswd' WHERE verify_code  = '".$code."'";
		$obj->db_query($q);
	
		// sent acount activation email
		$to = base64_decode($_GET['email']);
		$subject = "Account activation for ".SITE_NAME;
		$message = "<table width='50%'>
					<tr><td>Congratulations of creating your account on ".APPS_NAME."</td></tr>
					<tr>
						<td>Please keep this email for your records, as it contains an<br />
							important verification code that you may need should you ever<br />
							encounter problems or forget your password.</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>Do not share this information.</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>Verification code : ".$code."</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>Username : ".$username."</td></tr>
					<tr><td>Password : ".$plain_pswd."</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>You can login to your account at ".DOMAIN."</td></tr>
				</table>";
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// Additional headers
		$headers .= "From: ".APPS_NAME." <".EMAIL.">" . "\r\n";
		// Mail it
		mail($to, $subject, $message, $headers);
		
		$flag = "&flag=1";
	}
	
}elseif(base64_decode($_GET['type']) == 'builder'){

	$q = "SELECT * FROM ".BUILDERS." WHERE verify_code  = '".$code."' AND active = 0";
	$obj->db_query($q);
	
	if($obj->db_num_rows($obj->db_query($q)) > 0){
			
		// create username & password.
		$username=$obj->rendom(7);
		$plain_pswd=$obj->rendom(9);
		$password = md5($plain_pswd);	
			
		$q = "UPDATE ".BUILDERS." SET active = '1', builder_user_name = '$username', 
		password = '$password', builder_plain_pswd = '$plain_pswd' WHERE verify_code  = '".$code."'";
		
		$obj->db_query($q);
	
		// sent acount activation email
		$to = base64_decode($_GET['email']);
		$subject = "Account activation for ".SITE_NAME;
		$message = "<table width='50%'>
					<tr><td>Congratulations of creating your account on ".APPS_NAME."</td></tr>
					<tr>
						<td>Please keep this email for your records, as it contains an<br />
							important verification code that you may need should you ever<br />
							encounter problems or forget your password.</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>Do not share this information.</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>Verification code : ".$code."</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>Username : ".$username."</td></tr>
					<tr><td>Password : ".$plain_pswd."</td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>You can login to your account at ".DOMAIN."</td></tr>
				</table>";
		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		// Additional headers
		$headers .= "From: ".APPS_NAME." <".EMAIL.">" . "\r\n";
		// Mail it
		mail($to, $subject, $message, $headers);
		
		$flag = "&flag=1";
	}
	
}

header(REQ_SUCC.$flag);
exit;
?>