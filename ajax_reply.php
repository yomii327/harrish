<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();

if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }

include_once("includes/commanfunction.php");
$object = new COMMAN_Class(); 

$sect = $_POST['sect'];

//echo $sect; die;
function createPDF($html, $report){
	require_once("dompdf/dompdf_config.inc.php");
	$paper='a4';
	$orientation='portrait';
	
	if ( get_magic_quotes_gpc() )
	$html = stripslashes($html);
	
	$old_limit = ini_set("memory_limit", "94G");
	ini_set('max_execution_time', 3600); //300 seconds = 5 minutes
	
	$dompdf = new DOMPDF();
	$dompdf->load_html($html);
	$dompdf->set_paper($paper, $orientation);
	$dompdf->render();
	//$dompdf->stream("report.pdf");
	//exit(0);
	$output = $dompdf->output($report);
	
	// generate pdf in folder
	$d = 'report_pdf/'.$_SESSION['ww_builder_id'];
	if(!is_dir($d))
		mkdir($d);
	$tempFile = $d.'/'.$report;
	$fh = fopen($tempFile, 'w') or die("can't open file");
	$stringData = $output;
	fwrite($fh, $stringData);
	fclose($fh);
}

/* apply now for company account */
if($sect=='apply_now'){
	if(isset($_POST['bil_address']))
	{
		$_SESSION['bil_address']=$_POST['bil_address'];
		
		}
	$_SESSION['fname']=$fname=mysql_real_escape_string(trim($_POST['fname']));
	$_SESSION['compname']=$compname=mysql_real_escape_string(trim($_POST['compname']));
	$_SESSION['email']=$email=mysql_real_escape_string(trim($_POST['email']));
	$_SESSION['mobile']=$mobile=mysql_real_escape_string(trim($_POST['mobile']));
	$_SESSION['bus_l1']=$bus_l1=mysql_real_escape_string(trim($_POST['bus_line1']));
	$_SESSION['bus_l2']=$bus_l2=mysql_real_escape_string(trim($_POST['bus_line2']));
	$_SESSION['bus_suburb']=$bus_suburb=mysql_real_escape_string(trim($_POST['bus_suburb']));
	$_SESSION['bus_state']=$bus_state=mysql_real_escape_string(trim($_POST['bus_state']));
	$_SESSION['bus_post']=$bus_post=mysql_real_escape_string(trim($_POST['bus_post']));
	$_SESSION['bus_country']=$bus_country=mysql_real_escape_string(trim($_POST['bus_country']));
	$_SESSION['bil_l1']=$bil_l1=mysql_real_escape_string(trim($_POST['bil_line1']));
	$_SESSION['bil_l2']=$bil_l2=mysql_real_escape_string(trim($_POST['bil_line2']));
	$_SESSION['bil_suburb']=$bil_suburb=mysql_real_escape_string(trim($_POST['bil_suburb']));
	$_SESSION['bil_state']=$bil_state=mysql_real_escape_string(trim($_POST['bil_state']));
	$_SESSION['bil_post']=$bil_post=mysql_real_escape_string(trim($_POST['bil_post']));
	$_SESSION['bil_country']=$bil_country=mysql_real_escape_string(trim($_POST['bil_country']));
	
	if($obj->isValidEmail($email)==false){
		
		//$result = 1;
		//$_SESSION['error']=$result;
		$_SESSION['error']='<span class="emsg">Invalid email address!<\/span><br/><br/>';
		header("location:create_account.php");
	}elseif(!is_numeric($mobile)){
		
		//$result = 2;
		//$_SESSION['error']=$result;
		$_SESSION['error']='<span class="emsg">Invalid mobile no.!</span><br/><br/>';
		header("location:create_account.php");
	}elseif(!is_numeric($bus_post)){
		
		//$result = 3;
		//$_SESSION['error']=$result;
		$_SESSION['error']='<span class="emsg">Invalid post code!</span><br/><br/>';
		header("location:create_account.php");
	}elseif(!is_numeric($bil_post)){
		$_SESSION['error']='<span class="emsg">Invalid post code!</span><br/><br/>';
		//$result = 3;
		//$_SESSION['error']=$result;
		header("location:create_account.php");
	}else{
		$q = "SELECT * FROM ".COMPANIES." WHERE c_email = '$email'";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			
			$_SESSION['email_exits']='<div class="error-edit-profile">Email id already exist!</div>';
			//header("location:create_account.php");

			//$result = 0;
			//$_SESSION['error']=$result;
			header("location:create_account.php");
		}else{
			// insert into db.
			$verifyCode = md5($obj->rendom(15));

			$q = "INSERT INTO ".COMPANIES." (verify_code,c_full_name,c_comp_name,c_email,mobile,bus_l1,bil_l1,bus_l2,bil_l2,
				  bus_suburb,bil_suburb,bus_state,bil_state,bus_post,bil_post,bus_country,bil_country) VALUES('$verifyCode','$fname','$compname','$email','$mobile',
				  '$bus_l1','$bil_l1','$bus_l2','$bil_l2','$bus_suburb','$bil_suburb','$bus_state','$bil_state','$bus_post','$bil_post','$bus_country','$bil_country')";
			$obj->db_query($q);
			
			// send email to admin
			$detail = array();
			$detail['to']=$email;
			$detail['name']=APPS_NAME;
			$detail['from']=EMAIL;
			$detail['subject']="Apply for ".SITE_NAME.' | '.APPS_NAME." Company Account";
			$detail['msg']="<table width='100%'>
							<tr>
								<td colspan='4'><u><b>Personal Information</b></u></td>
							</tr>
							<tr>
								<td nowrap='nowrap' valign='top'>Full Name: </td>
								<td>$fname</td>
								<td nowrap='nowrap' valign='top'>Email Id: </td>
								<td>$email</td>
							</tr>
							<tr>
								<td nowrap='nowrap' valign='top'>Company Name: </td>
								<td>$compname</td>
								<td nowrap='nowrap' valign='top'>Mobile: </td>
								<td>$mobile</td>
							</tr>
							<tr>
								<td colspan='4'>&nbsp;</td>
							</tr>
							<tr>
								<td colspan='4'><u><b>Business Address</b></u></td>
							</tr>
							<tr>
								<td nowrap='nowrap' valign='top'>Line 1: </td>
								<td>$bus_l1</td>
								<td nowrap='nowrap' valign='top'>State: </td>
								<td>$bus_state</td>
							</tr>
							<tr>
								<td nowrap='nowrap' valign='top'>Line 2: </td>
								<td>$bus_l2</td>
								<td nowrap='nowrap' valign='top'>Suburb: </td>
								<td>$bus_suburb</td>
								
							</tr>
							<tr>
								<td nowrap='nowrap' valign='top'>Post Code: </td>
								<td>$bus_post</td>
								<td nowrap='nowrap' valign='top'>Country: </td>
								<td>$bus_country</td>
							</tr>
							<tr>
								<td colspan='4'>&nbsp;</td>
							</tr>
							<tr>
								<td colspan='4'><u><b>Billing Address</b></u></td>
							</tr>
							<tr>
								<td nowrap='nowrap' valign='top'>Line 1: </td>
								<td>$bil_l1</td>
								<td nowrap='nowrap' valign='top'>State: </td>
								<td>$bil_state</td>
							</tr>
							<tr>
								<td nowrap='nowrap' valign='top'>Line 2: </td>
								<td>$bil_l2</td>
								<td nowrap='nowrap' valign='top'>Suburb: </td>
								<td>$bil_suburb</td>
							</tr>
							<tr>
								<td nowrap='nowrap' valign='top'>Post Code: </td>
								<td>$bil_post</td>
								<td nowrap='nowrap' valign='top'>Country: </td>
								<td>$bil_country</td>
							</tr>
							<tr>
								<td colspan='4'>&nbsp;</td>
							</tr>
							<tr>
								<td colspan='4'>CONFIRM BY VISITING THE LINK BELOW</td>
							</tr>
							<tr>
								<td colspan='4'>".ACTIVATE."?code=".base64_encode($verifyCode)."&email=".base64_encode($email)."&type=".base64_encode('company')."</td>
							</tr>
						</table>";
			$obj->send_mail($detail);
			
			//echo 'manish'; die;
			//$result = 4;
			//$_SESSION['no']=4;
			//sleep(1);
			$_SESSION['success']='<p><span class="msg">Request send successfully!</span><br/><br/></p>';
			//$_SESSION['display']='style="display:none;"';
			header("location:success.php");
		}
	}
}

/* apply now for builder account */
elseif($sect=='b_apply_now'){
	$fname=mysql_real_escape_string(trim($_POST['fname']));
	$compname=mysql_real_escape_string(trim($_POST['compname']));
	$email=mysql_real_escape_string(trim($_POST['email']));
	$mobile=mysql_real_escape_string(trim($_POST['mobile']));
	$username=mysql_real_escape_string(trim($_POST['username']));
	$password=mysql_real_escape_string(trim($_POST['password']));	
	
	if($fname=='' || $compname=='' || $email=='' || $mobile=='' || $username=='' || $password==''){
		$result = 1;
	}elseif($obj->isValidEmail($email)==false){
		$result = 2;
	}elseif(!is_numeric($mobile)){
		$result = 3;
	}elseif(strlen($password)<6){
		$result = 4;
	}else{
		// check username
		$q = "SELECT * FROM ".BUILDERS." WHERE builder_user_name = '$username'";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$result = 5;
		}else{
			// check email id
			$q = "SELECT * FROM ".BUILDERS." WHERE builder_email = '$email'";
			if($obj->db_num_rows($obj->db_query($q)) > 0){
				$result = 6;
			}else{
				// insert into db.
				$verifyCode = md5($obj->rendom(15));
				$pswdmd5=md5($password);
	
				$q = "INSERT INTO ".BUILDERS." (fk_c_id,verify_code,active,builder_user_name,password,builder_plain_pswd,builder_full_name,comp_name,builder_email,mobile) 
					  VALUES('".$_SESSION['ww_c_id']."','$verifyCode','1','$username','$pswdmd5','$password','$fname','$compname','$email','$mobile')";
				$obj->db_query($q);
				
				$result = 7;
			}			
		}
	}
	sleep(1);
}
/* Project Configuration*/
elseif($sect=='project_configuration')
{
	echo 'Hi'; die;	
}
elseif($sect=='progress_monitoring')
{
	echo 'Hi'; die;	
}
/* company sign-in */ 
elseif($sect=='company_sign_in'){
	$password=md5(mysql_real_escape_string(trim($_POST['password'])));
	$user_name=mysql_real_escape_string(trim($_POST['username']));

	if($user_name=='' || $password==''){
		//$result = 1;
		$_SESSION['error']="The fields is required.";
		header("location:pms.php?sect=company");
	}else{
		$q = "SELECT * FROM ".COMPANIES." WHERE password = '$password' AND c_user_name = '$user_name' AND active = '1' ";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$f = $obj->db_fetch_assoc($obj->db_query($q));
			$_SESSION['ww_is_company'] = 1;
			$_SESSION['ww_c_id'] = $f['c_id'];
			$_SESSION['ww_c_full_name'] = $f['c_full_name'];
			$_SESSION['ww_c_comp_name'] = $f['c_comp_name'];
			$_SESSION['ww_c_user_name'] = $f['c_user_name'];
			$_SESSION['ww_c_plain_pswd'] = $f['c_plain_pswd'];
			$_SESSION['ww_c_email'] = $f['c_email'];
			$_SESSION['ww_logged_in_as']= $f['c_full_name'];
			$_SESSION['ww_company'] = $f;
			
//			$result = 2;
//			sleep(1);
			header("location:pms.php?sect=c_full_analysis");
		}else{
			//$result = 0;
			$_SESSION['error']="Invalid credentials, please try again!";
			header("location:pms.php?sect=company");
		}
	}
}

/* company remove the builder */
elseif($sect=='remove_builder'){

	$ww_c_id=$_SESSION['ww_c_id'];
	$b_id=base64_decode($_POST['b_id']);
	
	if($b_id==''){
		$result=2;
	}else{
		// if builder have project?
		if($obj->db_num_rows($obj->db_query("SELECT p.project_id FROM ".PROJECTS." p LEFT JOIN ".BUILDERS." b ON p.user_id=b.user_id WHERE p.user_id='$b_id'"))>0){
			$result=0;
		}else{
			// remove this builder
			$obj->db_query("DELETE FROM ".BUILDERS." WHERE user_id='$b_id'");
			$result=1;
		}
	}
	sleep(1);
}

/* company edit profile */
elseif($sect=='c_dashboard_edit'){

	
	$error= array();
	if(isset($_POST['fname']) && !empty($_POST['fname']))
	{
		$fname=mysql_real_escape_string(trim($_POST['fname']));
	}
	else
	{
		$_SESSION['err_fname']='<div class="error-edit-profile">The full name field is required.</div>';	
	}
	if(isset($_POST['compname']) && !empty($_POST['compname']))
	{
		$compname=mysql_real_escape_string(trim($_POST['compname']));
	}
	else
	{
		$_SESSION['err_compname']='<div class="error-edit-profile">The comapny name field is required.</div>';
	}
	if(isset($_POST['email']) && !empty($_POST['email']))
	{
		$email=mysql_real_escape_string(trim($_POST['email']));
	}
	else
	{
		$_SESSION['err_email']='<div class="error-edit-profile">The email field is required.</div>';
	}
	
	if(isset($_POST['mobile']) && !empty($_POST['mobile']))
	{
		$mobile=mysql_real_escape_string(trim($_POST['mobile']));
	}
	else
	{
		$_SESSION['err_mobile']='<div class="error-edit-profile">The mobile field is required.</div>';
	}
	if(isset($_POST['bus_line1']) && !empty($_POST['bus_line1']))
	{
		$bus_l1=mysql_real_escape_string(trim($_POST['bus_line1']));
	}
	else
	{
		$_SESSION['err_bus_line1']='<div class="error-edit-profile">The full name field is required.</div>';
	}
	if(isset($_POST['bus_line2']) && !empty($_POST['bus_line2']))
	{
		$bus_l2=mysql_real_escape_string(trim($_POST['bus_line2']));
	}
	if(isset($_POST['bus_suburb']) && !empty($_POST['bus_suburb']))
	{
		$bus_suburb=mysql_real_escape_string(trim($_POST['bus_suburb']));
	}
	else
	{
		$_SESSION['err_bus_suburb']='<div class="error-edit-profile">The suburb field is required.</div>';
	}
	if(isset($_POST['bus_state']) && !empty($_POST['bus_state']))
	{
		$bus_state=mysql_real_escape_string(trim($_POST['bus_state']));
	}
	else
	{
		$_SESSION['err_bus_state']='<div class="error-edit-profile">The state field is required.</div>';
	}
	if(isset($_POST['bus_post']) && !empty($_POST['bus_post']))
	{
		$bus_post=mysql_real_escape_string(trim($_POST['bus_post']));
	}
	else
	{
		$_SESSION['err_bus_post']='<div class="error-edit-profile">The post code field is required.</div>';
	}
	if(isset($_POST['bus_country']) && !empty($_POST['bus_country']))
	{
		$bus_country=mysql_real_escape_string(trim($_POST['bus_country']));
	}
	else
	{
		$_SESSION['err_bus_country']='<div class="error-edit-profile">The country field is required.</div>';
	}
	if(isset($_POST['bil_line1']) && !empty($_POST['bil_line1']))
	{
		$bil_l1=mysql_real_escape_string(trim($_POST['bil_line1']));
	}
	else
	{
		$_SESSION['err_bil_line1']='<div class="error-edit-profile">The billining address field is required.</div>';
	}
	if(isset($_POST['bil_line2']) && !empty($_POST['bil_line2']))
	{
		$bil_l2=mysql_real_escape_string(trim($_POST['bil_line2']));
	}
	if(isset($_POST['bil_suburb']) && !empty($_POST['bil_suburb']))
	{	
		$bil_suburb=mysql_real_escape_string(trim($_POST['bil_suburb']));
	}
	else
	{
		$_SESSION['err_bil_suburb']='<div class="error-edit-profile">The billing suburb field is required.</div>';
	}
	if(isset($_POST['bil_state']) && !empty($_POST['bil_state']))
	{
		$bil_state=mysql_real_escape_string(trim($_POST['bil_state']));
	}
	else
	{
		$_SESSION['err_bil_state']='<div class="error-edit-profile">The billing state field is required.</div>';
	}
	if(isset($_POST['bil_post']) && !empty($_POST['bil_post']))
	{
		$bil_post=mysql_real_escape_string(trim($_POST['bil_post']));
	}
	else
	{
		$_SESSION['err_bil_post']='<div class="error-edit-profile">The billing post code field is required.</div>';
	}
	if(isset($_POST['bil_country']) && !empty($_POST['bil_country']))
	{
		$bil_country=mysql_real_escape_string(trim($_POST['bil_country']));
	}
	else
	{
		$_SESSION['err_bil_country']='<div class="error-edit-profile">The billinig country field is required.</div>';
	}
	if(isset($_POST['username']) && !empty($_POST['username']))
	{
		$username=mysql_real_escape_string(trim($_POST['username']));
	}
	else
	{
		$_SESSION['err_username']='<div class="error-edit-profile">The username field is required.</div>';
	}
	if(isset($_POST['password']) && !empty($_POST['password']))
	{
		$pswd=mysql_real_escape_string(trim($_POST['password']));
	}
	else
	{
		$_SESSION['err_password']='<div class="error-edit-profile">The password field is required.</div>';
	}
	if(($_POST['password'] < 6) && !empty($_POST['password']))
	{
		$_SESSION['err_password_min']='<div class="error-edit-profile">The password minimum lenght should be 6 character long.</div>';
	}
	if(($_POST['password'] >16) && !empty($_POST['password']))
	{
		$_SESSION['err_password_min']='<div class="error-edit-profile">The password maximum lenght should be 16 character long.</div>';
	}
	

	
	
	if($fname=='' || $compname=='' || $email=='' || $mobile=='' || $bus_l1=='' || $bus_l2=='' || $bus_suburb=='' || 
		$bus_state=='' || $bus_post=='' || $bus_country=='' || $bil_l1=='' || $bil_l2=='' || $bil_suburb=='' || $bil_state=='' || 
		$bil_post=='' || $bil_country=='' || $username==''){
		
		$result = 1;		
	}elseif($obj->isValidEmail($email)==false){
		$result = 2;
	}elseif(!is_numeric($mobile)){
		$result = 3;
	}elseif(!is_numeric($bil_post)){
		$result = 4;
	}elseif(!is_numeric($bil_post)){
		$result = 5;
	}else{
		if($obj->db_num_rows($obj->db_query("SELECT * FROM ".COMPANIES." WHERE c_user_name='$username' AND c_id!='".$_SESSION['ww_c_id']."'")) > 0){
			$result = 6;
			$_SESSION['invaild_user']='<div class="error-edit-profile">Please select different username</div>';
			header('location:c_dashboard_edit.php');				
		}else{
			/*if(trim($_POST['password']!='')){
				$cplainpswd=mysql_real_escape_string(trim($_POST['password']));
				$password=md5($cplainpswd);
				
				$pswd=",password='$password',c_plain_pswd='$cplainpswd'";	
							
			}*/
			$q="UPDATE ".COMPANIES." SET
						c_user_name = '$username',
						c_full_name = '$fname',
						c_comp_name = '$compname',
						c_email = '$email',
						mobile = '$mobile',
						bus_l1 = '$bus_l1',
						bil_l1 = '$bil_l1',
						bus_l2 = '$bus_l2',
						bil_l2 = '$bil_l2',
						bus_suburb = '$bus_suburb',
						bil_suburb = '$bil_suburb',
						bus_state = '$bus_state',
						bil_state = '$bil_state',
						bus_post = '$bus_post',
						bil_post = '$bil_post',
						bus_country = '$bus_country',
						bil_country = '$bil_country'
				WHERE
					c_id = '".$_SESSION['ww_c_id']."'";
				
			$obj->db_query($q);
			
			$_SESSION['ww_logged_in_as']=$fname;
			$_SESSION['ww_c_full_name']=$fname;
			
			$result = 0;
			$_SESSION['success']='<div class="error-edit-profile">Update successfully!</div>';
			//header("location:create_account.php");
			header('location:pms.php?sect=c_dashboard');	
		}
	}
}

/* builder sign-in */
elseif($sect=='builder_sign_in'){
	$password=md5(mysql_real_escape_string(trim($_POST['password'])));
	$user_name=mysql_real_escape_string(trim($_POST['username']));

	if($user_name=='' || $password==''){
		//$result = 1;
		$_SESSION['error']="The fields is required.";
		header("location:pms.php?sect=builder");
	}else{
		$q = "SELECT * FROM ".BUILDERS." WHERE user_password = '$password' AND user_name = '$user_name' AND active = '1' ";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$f = $obj->db_fetch_assoc($obj->db_query($q));
			$_SESSION['ww_is_builder'] = 1;
			$_SESSION['ww_builder_id'] = $f['user_id'];
			$_SESSION['ww_builder_full_name'] = $f['user_fullname'];
			$_SESSION['ww_comp_name'] = $f['company_name'];
			$_SESSION['ww_builder_user_name'] = $f['user_name'];
			$_SESSION['ww_builder_plain_pswd'] = $f['user_plainpassword'];
			$_SESSION['ww_builder_email'] = $f['user_email'];
			$_SESSION['ww_logged_in_as']= $f['user_fullname'];
			$_SESSION['ww_builder'] = $f;
			$_SESSION['ww_builder_fk_c_id'] = $f['fk_c_id'];
//			$result = 2; //			sleep(1);
			header("location:pms.php?sect=b_full_analysis");
			
		}else{
			$_SESSION['error']="Invalid credentials, please try again!";
			header("location:pms.php?sect=builder");
			//$result = 0;
		}
	}
}

/* builder edit profile */
elseif($sect=='b_dashboard_edit'){
	$fname=mysql_real_escape_string(trim($_POST['fname']));
	$compname=mysql_real_escape_string(trim($_POST['compname']));
	$email=mysql_real_escape_string(trim($_POST['email']));
	$mobile=mysql_real_escape_string(trim($_POST['mobile']));
	$username=mysql_real_escape_string(trim($_POST['username']));
	
	if($fname=='' || $compname=='' || $email=='' || $mobile=='' || $username==''){		
		$result = 1;		
	}elseif($obj->isValidEmail($email)==false){
		$result = 2;
	}elseif(!is_numeric($mobile)){
		$result = 3;
	}else{
		if($obj->db_num_rows($obj->db_query("SELECT * FROM ".BUILDERS." WHERE user_name='$username' AND user_id!='".$_SESSION['ww_builder_id']."'")) > 0){
			$result = 4;
			$_SESSION['builder_invalid_user']='<div class="error-edit-profile">Please select different username</div>';				
		}else{
			/*if(trim($_POST['password']!='')){
				$cplainpswd=mysql_real_escape_string(trim($_POST['password']));
				$password=md5($cplainpswd);
				
				$pswd=",password='$password',builder_plain_pswd='$cplainpswd'";				
			}*/
			$q="UPDATE ".BUILDERS." SET user_name='$username',user_fullname='$fname',company_name='$compname',user_email='$email',user_phone_no='$mobile'
				 WHERE user_id='".$_SESSION['ww_builder_id']."'";
				
			$obj->db_query($q);
			
			$_SESSION['ww_logged_in_as']=$fname;
			$_SESSION['ww_builder_full_name']=$fname;
			
			$result = 0;
			$_SESSION['builder_success']='Update successfully!';
			header('location:pms.php?sect=b_dashboard');
		}
	}
}

/* owner/tenant sign-in */
elseif($sect=='tenant_sign_in'){
	$password=md5(mysql_real_escape_string(trim($_POST['password'])));
	$user_name=mysql_real_escape_string(trim($_POST['username']));
	
	if($user_name=='' || $password==''){
		//$result = 1;
		$_SESSION['error']="The fields is required.";
		header("location:pms.php?sect=tenant");
	}else{
		$q = "SELECT * FROM ".OWNERS." WHERE password = '$password' AND user_name = '$user_name' AND active = '1' ";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$f = $obj->db_fetch_assoc($obj->db_query($q));
			$_SESSION['ww_is_builder'] = 0;
			$_SESSION['ww_owner_id'] = $f['id'];
			$_SESSION['ww_owner_full_name'] = $f['owner_full_name'];
			$_SESSION['ww_owner_user_name'] = $f['user_name'];
			$_SESSION['ww_owner_plain_pswd'] = $f['plain_pswd'];
			$_SESSION['ww_owner_email'] = $f['email'];
			$_SESSION['ww_logged_in_as']= $f['owner_full_name'];
			$_SESSION['ww_owner'] = $f;
//			$result = 2; //			sleep(1);
			header("location:pms.php?sect=o_dashboard");
		}else{
			//$result = 0;
			$_SESSION['error']="Invalid credentials, please try again!";
			header("location:pms.php?sect=tenant");
		}
	}
}

/* add project by builder */
elseif ($sect == 'add_project') {
    $companyId = $_POST['company'];
    $getCompanyName = $object->selQRYMultiple("group_concat(company_name SEPARATOR ', ') AS compname", 'organisations', 'is_deleted = 0 AND id IN('.$companyId.') GROUP BY is_deleted ');

    $name = mysql_real_escape_string(trim($_POST['name']));
    $protype = mysql_real_escape_string(trim($_POST['protype']));
    $compname=mysql_real_escape_string(trim($getCompanyName[0]['compname']));
    $companyId=mysql_real_escape_string(trim($companyId));
    $line1 = mysql_real_escape_string(trim($_POST['line1']));
    $line2 = mysql_real_escape_string(trim($_POST['line2']));
    $suburb = mysql_real_escape_string(trim($_POST['suburb']));
    $state = mysql_real_escape_string(trim($_POST['state']));
    $postcode = mysql_real_escape_string(trim($_POST['postcode']));
    $country = mysql_real_escape_string(trim($_POST['country']));
    $projectManager = mysql_real_escape_string(trim($_POST['projectManager']));
    $projManagerEmail = mysql_real_escape_string(trim($_POST['projManagerEmail']));
    $contactPerson = mysql_real_escape_string(trim($_POST['contactPerson']));
    $contactPersonEmail = mysql_real_escape_string(trim($_POST['contactPersonEmail']));
    $defectClause = mysql_real_escape_string(trim($_POST['defectClause']));
    $allow_sync = mysql_real_escape_string(trim($_POST['allow_sync']));
    $allow_sync = (!empty($allow_sync))?$allow_sync:'No';

    $allow_sync_to_ipad = mysql_real_escape_string(trim($_POST['allow_sync_to_ipad']));
    $allow_sync_to_ipad = (!empty($allow_sync_to_ipad))?$allow_sync_to_ipad:'No';
    
    //echo $_POST['associateTo'];
    if (isset($_POST['associateTo']) && !empty($_POST['associateTo'])) {
        $associateTo = $_POST['associateTo'];
    }
    if ($protype == '' || $name == '' || $line1 == '' || $suburb == '' || $state == '' || $postcode == '' || $country == '') {
        $result = 0;
    } else {
        if($_SESSION['ww_is_company'] === 1){
            $builder_id =  $_SESSION['ww_is_company'];
        }else{
            $builder_id = $_SESSION['ww_builder_id'];
            $_POST['builder'][] = $_SESSION['ww_builder_id'];
        }
        $creatdate=date('Y-m-d H:i:s'); 

        // Add records in Project New Introduce Table Dated : 28/05/2012
            $qProject = "INSERT INTO projects SET
                            project_name = '" . $name . "',
                                project_type = '" . $protype . "',
                                company_id = '".$companyId."',
                                company_name = '".$compname."',
                                project_address_line1 = '" . $line1 . "',
                                project_address_line2 = '" . $line2 . "',
                                project_suburb = '" . $suburb . "',
                                project_state = '" . $state . "',
                                project_postcode = '" . $postcode . "',
                                project_country = '" . $country . "',
                                project_manager = '".$projectManager."',
                                project_manager_email = '".$projManagerEmail."',
                                contact_person = '".$contactPerson."',
                                contact_person_email = '".$contactPersonEmail."',
                                defect_clause = '".$defectClause."',
                                allow_sync = '".$allow_sync."',
                                project_is_synced = '".$allow_sync_to_ipad."',
                                created_date = NOW(),
                                last_modified_date = NOW(),
                                is_pdf = 1,
                                created_by = ".$builder_id.",
                                last_modified_by = ".$builder_id;
        $obj->db_query($qProject);
        // Add records in Project New Introduce Table Dated : 28/05/2012
        $lastProId = mysql_insert_id(); 
        $_SESSION['idp']=$lastProId;
        
        ///uploading image as per new added project
        /*$newimagename = $lastProId.'_logo.png';
        copy('./project_images/'.$logo, './project_images/'.$newimagename);
        
        $queryProject = "UPDATE projects SET project_logo = '".$newimagename."', last_modified_date = NOW(),last_modified_by = ".$builder_id." WHERE project_id = ".$lastProId."";
        $obj->db_query($queryProject);*/

        foreach ($_POST['builder'] as $bid) {
             $builder_id = $bid;
            #Add default leave in project Start Here
            $select = "date, leave_type, reason, is_leave";
            $table = "public_holidays";
            $where = "is_deleted ='0'";
            $defaultLeave = $object->selQRYMultiple($select, $table, $where);
            
            foreach($defaultLeave as $val){
                $insertQry = "INSERT INTO project_leave SET 
                                    project_id = '" . $lastProId . "', 
                                    date = '" . $val['date'] . "',
                                    leave_type = '" . $val['leave_type'] . "',
                                    reason = '" . $val['reason'] . "',
                                    is_leave = '" . $val['is_leave'] . "',
                                    created_date = NOW(),
                                    last_modified_date = NOW(),
                                    created_by = '" . $builder_id . "',
                                    last_modified_by = '" . $builder_id . "'";
                $r=$obj->db_query($insertQry);
            }
            #Add default leave in project End Here
            //Sync Permission Dated : 03-09-2012 
            mysql_query("INSERT INTO sync_permission SET
                                no_of_days = '100000',
                                status = \"'ALL'\",
                                project_id = '" . $lastProId . "',
                                device_type = 'iPad',
                                created_by = '" . $builder_id . "',
                                created_date = NOW(),
                                last_modified_date = NOW(),
                                location_ids = 'Select All',
                                last_modified_by = '" . $builder_id . "'");
                                
            mysql_query("INSERT INTO sync_permission SET
                                no_of_days = '100000',
                                status = \"'ALL'\",
                                project_id = '" . $lastProId . "',
                                device_type = 'iPhone',
                                created_by = '" . $builder_id . "',
                                created_date = NOW(),
                                last_modified_date = NOW(),
                                location_ids = 'Select All',
                                last_modified_by = '" . $builder_id . "'");
        //Sync Permission Dated : 03-09-2012 
        //User Permission
            $projectWisePermissions = array(
                'web_edit_inspection',
                'web_delete_inspection',
                'web_close_inspection',
                'iPad_add_inspection',
                'iPad_edit_inspection',
                'iPad_delete_inspection',
                'iPad_close_inspection',
                'iPhone_add_inspection',
                'iPhone_close_inspection',
                'web_checklist'
            );
            $countPermission = count($projectWisePermissions);
            for($i=0; $i<$countPermission; $i++){
                $insertQry = "INSERT INTO user_permission SET
                                user_id = '".$builder_id."',
                                permission_name = '".$projectWisePermissions[$i]."',
                                is_allow = '".$managerPermissionArray[$projectWisePermissions[$i]]."',
                                created_by = '".$builder_id."',
                                last_modified_date = NOW(),
                                last_modified_by = '".$builder_id."',
                                created_date = NOW(),
                                project_id = '".$lastProId."'";
                mysql_query($insertQry);
            }

            //User Permission
            $issueToDefault = "INSERT INTO inspection_issue_to SET
                                    issue_to_name = 'NA',
                                    created_by = '".$builder_id."',
                                    project_id = '".$lastProId."',
                                    last_modified_date = NOW(),
                                    last_modified_by = '".$builder_id."',
                                    created_date = NOW()";
            $obj->db_query($issueToDefault);
            
            
             $q = "INSERT INTO ".PROJECTS." SET
                                project_id = '".$lastProId."',
                                user_id = '".$builder_id."',
                                project_name = '".$name."',
                                project_type = '".$protype."',
                                company_id = '".$companyId."',
                                company_name = '".$compname."', 
                                project_address_line1 = '".$line1."',
                                project_address_line2 = '".$line2."',
                                project_suburb = '".$suburb."',
                                project_state = '".$state."',
                                project_postcode = '".$postcode."',
                                project_country = '".$country."',
                                project_manager = '".$projectManager."',
                                project_manager_email = '".$projManagerEmail."',
                                contact_person = '".$contactPerson."',
                                contact_person_email = '".$contactPersonEmail."',
                                defect_clause = '".$defectClause."',
                                allow_sync = '".$allow_sync."',
                                project_is_synced = '".$allow_sync_to_ipad."',
                                is_pdf = 1,
                                created_date = NOW(),
                                last_modified_date = NOW(),
                                created_by = ".$builder_id.",
                                last_modified_by =".$builder_id;
                //echo $q; die;
                $obj->db_query($q);
        
                // set project unique id.   
                $pI = strlen($lastProId);
                $r = $obj->rendomNum(8);
                $c = substr($r,0,-$pI);
                $pro_code = $c.$lastProId;
                    
                $q = "UPDATE ".PROJECTS." SET pro_code = '".$pro_code."', last_modified_date = NOW(), last_modified_by = '".$_SESSION['ww_builder_id']."' WHERE project_id='$lastProId'";
                $obj->db_query($q);     
        
                // if $associateTo is set
                if(sizeof($associateTo)>0){
                    // if builder exists
                    for($a=0; $a<sizeof($associateTo); $a++){
                        if($obj->db_num_rows($obj->db_query("SELECT user_id FROM ".BUILDERS." WHERE user_id='".$associateTo[$a]."'"))>0){
                            $obj->db_query("INSERT INTO ".SUBBUILDERS." (fk_p_id,fk_b_id,sb_id) VALUES('$lastProId','$builder_id','".$associateTo[$a]."')");
                        }
                    }
                }

        }

        $result = 1;
        $_SESSION['add_project'] = 'Project added successfully.';
        header('location:pms.php?sect=project_configuration');
        sleep(1);
    }
}

/* edit project by builder */
elseif($sect=='edit_project'){
	$builder_id = $_SESSION['ww_builder_id'];
	$pro_id=$_POST['pro_id'];
	
	if(isset($_POST['edit'])){
	
		$name=mysql_real_escape_string(trim($_POST['name']));
		$protype=mysql_real_escape_string(trim($_POST['protype']));
		$line1=mysql_real_escape_string(trim($_POST['line1']));
		$line2=mysql_real_escape_string(trim($_POST['line2']));
		$suburb=mysql_real_escape_string(trim($_POST['suburb']));
		$state=mysql_real_escape_string(trim($_POST['state']));
		$postcode=mysql_real_escape_string(trim($_POST['postcode']));
		$country=mysql_real_escape_string(trim($_POST['country']));		
		if(isset($_POST['defectList']) && !empty($_POST['defectList']))
		{
			$defectList=$_POST['defectList']; 
		}
		else
		{
			
			$defectList=0;	
		}
		if(isset($_POST['associateTo']) && !empty($_POST['associateTo']))
		{
			$associateTo=$_POST['associateTo']; 
		}
		else
		{
				$associateTo=0;
		}
		
		
		
		
		if($protype=='' || $name=='' || $line1=='' || $suburb=='' || $state=='' || $postcode=='' || $country==''){
			$result = 0;
		}else{	
			
			$q = "UPDATE ".PROJECTS." SET
						project_name = '".$name."',	
						project_type = '".$protype."',
						project_address_line1 = '".$line1."',
						project_address_line2 = '".$line2."',
						project_suburb = '".$suburb."',
						project_state = '".$state."',
						project_postcode = '".$postcode."',
						project_country = '".$country."',
						last_modified_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."'
				 WHERE
				 		project_id = '".$pro_id."'";
			$obj->db_query($q);
			
			// if $defectList is set
			$obj->db_query("DELETE FROM ".PROJECTDEFECTS." WHERE fk_b_id='$builder_id' fk_p_id='$pro_id'");
			if(sizeof($defectList)>0){			
				// if builder exists
				for($a=0; $a<sizeof($defectList); $a++){
					if($obj->db_num_rows($obj->db_query("SELECT * FROM ".DEFECTSLIST." WHERE dl_id='".$defectList[$a]."'"))>0){
						
						$obj->db_query("INSERT INTO ".PROJECTDEFECTS." (fk_dl_id,fk_b_id,fk_p_id) 
						VALUES('".$defectList[$a]."','$builder_id','$pro_id')");
					}
				}
			}
			
			// if $associateTo is set
			$obj->db_query("DELETE FROM ".SUBBUILDERS." WHERE fk_p_id='$pro_id' AND fk_b_id='$builder_id'");
			if(sizeof($associateTo)>0){			
				// if builder exists
				for($a=0; $a<sizeof($associateTo); $a++){
					if($obj->db_num_rows($obj->db_query("SELECT user_id FROM ".BUILDERS." WHERE user_id='".$associateTo[$a]."'"))>0){				
						$obj->db_query("INSERT INTO ".SUBBUILDERS." (fk_p_id,fk_b_id,sb_id) VALUES('$pro_id','$builder_id','".$associateTo[$a]."')");
					}
				}
			}
			$result = 1;
			$_SESSION['builder_seccuess_update']='Project updated successfully!';
			$id=$_SESSION['proj_id'];
			$hb=$_SESSION['hb'];
			 
			//.base64_decode(); 
			$id=base64_encode($id);
			$hb=base64_encode($hb);
			
			 
			header('location:pms.php?sect=add_project_detail&id='.$id.'&hb='.$hb);		
		}
	}elseif(isset($_POST['remove'])){
		
		// check for managers
		/*$qm=$obj->db_num_rows($obj->db_query("SELECT bsb_id FROM ".SUBBUILDERS." WHERE fk_p_id='$pro_id' AND fk_b_id='$builder_id'"));
		// check for inspectors
		$qi=$obj->db_num_rows($obj->db_query("SELECT id FROM ".OWNERS." WHERE ow_project_id='$pro_id'"));
		// check for defects
		$qd=$obj->db_num_rows($obj->db_query("SELECT df_id FROM ".DEFECTS." WHERE project_id='$pro_id' AND status!='Closed'"));
		// check for trades
		$qt=$obj->db_num_rows($obj->db_query("SELECT resp_id FROM ".RESPONSIBLES." WHERE project_id='$pro_id' AND builder_id='$builder_id'"));
		
		if($qm > 0){
			$result = 2;
			$_SESSION['error_project']='Project is associated with some other Managers.<br/>Please take it back!';
			header('location:sect=pms.php?sect=edit_project&id='.base64_decode($_SESSION['proj_id']));
		}elseif($qi > 0){
			$result = 3;
			$_SESSION['error_project']='Project is associated with some Inspectors.<br/>Please take it back!';
			header('location:sect=pms.php?sect=edit_project&id='.base64_decode($_SESSION['proj_id']));
			
		}elseif($qt > 0){
			$result = 4;
			$_SESSION['error_project']='Project is associated with some Trades.<br/>Please take it back!';
			header('location:sect=pms.php?sect=edit_project&id='.base64_decode($_SESSION['proj_id']));
		}elseif($qd > 0){
			$result = 5;
			$_SESSION['error_project']='Project is related with Some Inspections.<br/>Please closed them!';
			header('location:sect=pms.php?sect=edit_project&id='.base64_decode($_SESSION['proj_id']));
		}else*/{
			// remove record from PROJECTS
			$update="UPDATE user_projects SET is_deleted = '1', last_modified_date=NOW() WHERE project_id ='$pro_id'";
			$obj->db_query($update);
			
			// remove records from SUBBUILDERS
			/*$obj->db_query("DELETE FROM ".SUBBUILDERS." WHERE fk_p_id='$pro_id'");
			
			// remove records from OWNERS
			$obj->db_query("DELETE FROM ".OWNERS." WHERE ow_project_id='$pro_id'");
			
			// remove records from DEFECTS
			$obj->db_query("DELETE FROM ".DEFECTS." WHERE project_id='$pro_id' AND status='Closed'");
			
			// remove records from RESPONSIBLES
			$obj->db_query("DELETE FROM ".RESPONSIBLES." WHERE project_id='$pro_id'");*/
			
			$result = 6;
		}
	}	
	sleep(1);
}

/* add defect list by builder */
elseif($sect=='add_defects_list'){
	$fk_b_id=$_SESSION['ww_builder_id'];
	$title=mysql_real_escape_string(trim($_POST['title']));
	
	if($title==''){
		$result=0;
	}else{
		// if already exists
		if($obj->db_num_rows($obj->db_query("SELECT dl_id FROM ".DEFECTSLIST." 
				WHERE fk_b_id='$fk_b_id' AND dl_title='$title' "))>0){
			$result=1;
		}else{
			
			$obj->db_query("INSERT INTO ".DEFECTSLIST." (fk_b_id,dl_title) VALUES('$fk_b_id','$title')");
			
			if(isset($_POST['save'])){
				$result=2;
			}else{
				$result=3;
			}
		}		
	}
	sleep(1);
}

/* edit/remove defect list by builder */
elseif($sect=='edit_remove_defect'){
	$fk_b_id=$_SESSION['ww_builder_id'];
	$title=mysql_real_escape_string(trim($_POST['title']));
	$dl_id=mysql_real_escape_string(trim($_POST['dl_id']));
	
	if($dl_id==''){
		$result=5;
	}else{
		if($title==''){
			$result=0;
		}else{
			if($obj->db_num_rows($obj->db_query("SELECT * FROM ".DEFECTSLIST." 
					WHERE dl_id='$dl_id' AND fk_b_id='$fk_b_id' "))<0){
				$result=1;
			}else{
			
				if(isset($_POST['edit'])){
					// if already exists
					if($obj->db_num_rows($obj->db_query("SELECT dl_id FROM ".DEFECTSLIST." 
							WHERE dl_id!='$dl_id' AND fk_b_id='$fk_b_id' AND dl_title='$title' "))>0){
						$result=2;
					}else{
						
						$obj->db_query("UPDATE ".DEFECTSLIST." SET dl_title='$title' WHERE dl_id='$dl_id'");
						
						$result=3;
					}
				}elseif(isset($_POST['remove'])){
				
					$obj->db_query("DELETE FROM ".DEFECTSLIST." WHERE dl_id='$dl_id'");
					$result=4;
				}
				
			}
		}
	}	
	sleep(1);
}
elseif($sect=='add_inspector'){
	if(isset($_POST['remove'])){
		$deleteQry = "UPDATE user SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = '".$_SESSION['ww_builder_id']."' WHERE user_id = '".$_POST['userId']."' AND user_type = 'inspector'";
		mysql_query($deleteQry);
		$ms = 'ZGVsZXRl';
		$_SESSION['add_inspector_success'] = $ms;	
		header('location:pms.php?sect=show_sub_loc');	
	}

	$button=$_POST['button'];
	if(isset($_POST['ownerName']) && !empty($_POST['ownerName'])){
		$full_name=mysql_real_escape_string(trim($_POST['ownerName']));
		$_SESSION['add_sub_loc']['full_name']=$full_name;
	}
	
	if(isset($_POST['userRole']) && !empty($_POST['userRole'])){
		$userRole = mysql_real_escape_string(trim($_POST['userRole']));
		$_SESSION['add_sub_loc']['userRole']=$userRole;
		if($_POST['userRole'] == 'Sub Contractor' && isset($_POST['issueTo']) && !empty($_POST['issueTo'])){
			$issueTo = mysql_real_escape_string(trim($_POST['issueTo']));
		}else{
			$issueTo = '';
		}
	}

	if(isset($_POST['userName']) && !empty($_POST['userName'])){
		$user_name=mysql_real_escape_string(trim($_POST['userName']));
		$_SESSION['add_sub_loc']['userName']=$user_name;
	}
	
	if(isset($_POST['password']) && !empty($_POST['password'])){	
		$password=mysql_real_escape_string(trim($_POST['password']));
		$_SESSION['add_sub_loc']['password']=$password;
	}
	
	if(isset($_POST['proId']) && !empty($_POST['proId'])){
		$proId=mysql_real_escape_string(trim($_POST['proId']));
		$_SESSION['add_sub_loc']['proId']=$proId;
	}
	
	if(isset($_POST['phone']) && !empty($_POST['phone'])){	
		$phone=mysql_real_escape_string(trim($_POST['phone']));
		$_SESSION['add_sub_loc']['phone']=$phone;
	}
	
	if(isset($_POST['email']) && !empty($_POST['email'])){
		$email=mysql_real_escape_string(trim($_POST['email']));
		$_SESSION['add_sub_loc']['email']=$email;
	}

	if(isset($_POST['cron_report_type']) && !empty($_POST['cron_report_type'])){
		$cron_report_type=mysql_real_escape_string(trim($_POST['cron_report_type']));
		$_SESSION['add_sub_loc']['cron_report_type']=$cron_report_type;
	}

	if(isset($_POST['allow_rfi']) && !empty($_POST['allow_rfi'])){
		$allow_rfi=mysql_real_escape_string(trim($_POST['allow_rfi']));
		$_SESSION['add_sub_loc']['allow_rfi']=$allow_rfi;
	}

	$ownerId = '';
	$owner_exist = 0;

	if($full_name=='' || $user_name=='' || $password=='' || $phone=='' || $email==''){
		$result = 0;
		//$_SESSION['add_inspector']='';
		//header('location:');
	}elseif(!is_numeric($phone)){
		$result = 1;	
		$_SESSION['add_inspector']='<div class="error-edit-profile">Invalid phone number!</div>';
		header('location:pms.php?sect=add_sub_loc');
	}elseif($obj->isValidEmail($email)==false){
		$result = 2;
		$_SESSION['add_inspector']='<div class="error-edit-profile">Invalid email id!</div>';
		header('location:pms.php?sect=add_sub_loc');
	}elseif(strlen($password)<6){
		$result = 3;
	}else{
		//echo 'check username'; die;
		$phone=mysql_real_escape_string(trim($_POST['phone']));
		$email=mysql_real_escape_string(trim($_POST['email']));
		$verify_code = md5($obj->rendom(15));
		$plain=$password;
		$password=md5($plain);
		if($_POST['mode'] != 'edit'){
			$q = "SELECT * FROM user WHERE user_name = '$user_name' and is_deleted = 0";
			$rsUser = $obj->db_query($q);
			if($obj->db_num_rows($rsUser) > 0){
				$userDataCheck = mysql_fetch_object($rsUser);
				$result = 4;
				$_SESSION['add_inspector_user']='<div class="error-edit-profile">Username already exists!</div>';
				$_SESSION['post_array']=$_POST;
				header('location:pms.php?sect=add_sub_loc');
			}else{
				$q = "SELECT * FROM user WHERE user_email = '$email' and is_deleted = 0";
				$rsEmail = $obj->db_query($q);
				if($obj->db_num_rows($rsEmail) > 0){
					$emailDataCheck = mysql_fetch_object($rsEmail);
					$result = 5;
					$_SESSION['add_inspector_email']='<div class="error-edit-profile">Email Id already exists!</div>';
					$_SESSION['post_array']=$_POST;
					header('location:pms.php?sect=add_sub_loc');
				}else{
					$managerData = $object->selQRY('company_name', 'user', 'user_id = "'.$_SESSION['ww_builder']['user_id'].'" and is_deleted = 0');
					$q = "INSERT INTO user SET
						active = '1',
						user_verifycode = '".addslashes($verify_code)."',
						user_fullname = '".addslashes($full_name)."',
						user_email = '".addslashes($email)."',
						user_name = '".addslashes($user_name)."',
						user_password = '".addslashes($password)."',
						user_plainpassword = '".addslashes($plain)."',
						company_name = '".addslashes($managerData['company_name'])."',
						user_type = 'inspector',
						created_date = NOW(),
						created_by = '".$_SESSION['ww_builder']['user_id']."',
						last_modified_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
						user_phone_no = '".$phone."'";
#echo $q; die;
					$obj->db_query($q);	
					$newUserId = mysql_insert_id();
					
					$projectData = $object->selQRY('project_id, pro_code, project_name, project_type, project_address_line1, project_address_line2, project_suburb, project_state, project_postcode, project_country, resource_type, is_pdf', 'projects', 'project_id = "'.$_POST['proId'].'" and is_deleted = 0');
					
					$insertQry = "INSERT INTO user_projects set
						project_id = '".$projectData['project_id']."',
						user_id = '".$newUserId."',
						pro_code = '".$projectData['pro_code']."',
						project_name = '".$projectData['project_name']."',
						project_type = '".$projectData['project_type']."',
						project_address_line1 = '".$projectData['project_address_line1']."',
						project_address_line2 = '".$projectData['project_address_line2']."',
						project_suburb = '".$projectData['project_suburb']."',
						project_state = '".$projectData['project_state']."',
						project_postcode = '".$projectData['project_postcode']."',
						project_country = '".$projectData['project_country']."',
						user_role = '".$userRole."',
						created_date = NOW(),
						last_modified_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
						created_by = '".$_SESSION['ww_builder']['user_id']."',
						resource_type = '".$projectData['resource_type']."',
						issued_to = '".$issueTo."',
						is_pdf = '".$projectData['is_pdf']."',
						allow_rfi = '".$allow_rfi."',
						cron_report_type = '".$cron_report_type."'";
					mysql_query($insertQry);
						
					$projectWisePermissions = array('web_edit_inspection','web_delete_inspection','web_close_inspection','iPad_add_inspection','iPad_edit_inspection','iPad_delete_inspection','iPad_close_inspection','iPhone_add_inspection','iPhone_close_inspection','iPhone_edit_inspection', 'iPhone_edit_inspection_partial','web_checklist');

					$keyInspectorPermisArray = array_keys($inspectorPermissionArray);
					
					for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
						if(in_array($keyInspectorPermisArray[$i], $projectWisePermissions)){
							if($keyInspectorPermisArray[$i] == 'iPhone_edit_inspection'){
								if($userRole == 'Sub Contractor'){
									$permissionQry = "INSERT INTO user_permission (user_id, project_id, permission_name, is_allow, created_by, created_date, last_modified_by, last_modified_date) VALUES ('".$newUserId."', '".$_SESSION['idp']."', 'iPhone_edit_inspection', 0, '".$_SESSION['ww_builder']['user_id']."', NOW(), '".$_SESSION['ww_builder']['user_id']."', NOW()), ('".$newUserId."', '".$_SESSION['idp']."', 'iPhone_edit_inspection_partial', 1, '".$_SESSION['ww_builder']['user_id']."', NOW(), '".$_SESSION['ww_builder']['user_id']."', NOW())";
								}else{
									$permissionQry = "INSERT INTO user_permission (user_id, project_id, permission_name, is_allow, created_by, created_date, last_modified_by, last_modified_date) VALUES ('".$newUserId."', '".$_SESSION['idp']."', 'iPhone_edit_inspection', '".$inspectorPermissionArray['iPhone_edit_inspection']."', '".$_SESSION['ww_builder']['user_id']."', NOW(), '".$_SESSION['ww_builder']['user_id']."', NOW()), ('".$newUserId."', '".$_SESSION['idp']."', 'iPhone_edit_inspection_partial', '".$inspectorPermissionArray['iPhone_edit_inspection_partial']."', '".$_SESSION['ww_builder']['user_id']."', NOW(), '".$_SESSION['ww_builder']['user_id']."', NOW())";
								}
								mysql_query($permissionQry);
								$i++;
							}else{
								$permissionQry = "INSERT INTO user_permission SET
													user_id = '".$newUserId."',
													project_id = '".$_SESSION['idp']."',
													permission_name = '".$keyInspectorPermisArray[$i]."',
													is_allow = '".$inspectorPermissionArray[$keyInspectorPermisArray[$i]]."',
													created_by = '".$_SESSION['ww_builder']['user_id']."',
													created_date = NOW(),
													last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
													last_modified_date = NOW()";
								mysql_query($permissionQry);
							}
						}else{
							$permissionQry = "INSERT INTO user_permission SET user_id = '".$newUserId."', permission_name = '".$keyInspectorPermisArray[$i]."', is_allow = '".$inspectorPermissionArray[$keyInspectorPermisArray[$i]]."', created_by = '".$_SESSION['ww_builder']['user_id']."', created_date = NOW(), last_modified_date=NOW(), last_modified_by='".$_SESSION['ww_builder']['user_id']."'";
							mysql_query($permissionQry);
						}
					}	
						
					/*$keyInspectorPermisArray = array_keys($inspectorPermissionArray);
					for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
						$permissionQry = "INSERT INTO user_permission SET user_id = '".$newUserId."', permission_name = '".$keyInspectorPermisArray[$i]."', is_allow = '".$inspectorPermissionArray[$keyInspectorPermisArray[$i]]."', created_by = '".$_SESSION['ww_builder']['user_id']."', created_date = NOW()";
						mysql_query($permissionQry);
					}*/
					
					$ms = 'c3VjY2Vzcw==';
					$result = 6;
					$id=$_SESSION['idp'];
					$hb=$_SESSION['hb'];
					$id=base64_encode($id);
					$hb=base64_encode($hb);
					//pms.php?sect=add_project_detail&id=Mw==&hb=MQ==
					$_SESSION['add_inspector_success'] = $ms;	
					header('location:pms.php?sect=show_sub_loc');	
				}
			}
		}else{
			$q = "SELECT * FROM user WHERE user_name = '$user_name' and is_deleted = 0";
			$rsUser = $obj->db_query($q);
			if($obj->db_num_rows($rsUser) > 0){
				$userDataCheck = mysql_fetch_object($rsUser);
				if($userDataCheck->user_name != $user_name){
					$result = 4;
					$_SESSION['add_inspector_user']='<div class="error-edit-profile">Username already exists!</div>';
					header('location:pms.php?sect=add_sub_loc&mode=edit&uId='.base64_encode($_POST['userId']));
				}else{
					$q = "SELECT * FROM user WHERE user_email = '$email' and is_deleted = 0";
					$rsEmail = $obj->db_query($q);
					if($obj->db_num_rows($rsEmail) > 0){
						$emailDataCheck = mysql_fetch_object($rsEmail);
						if($emailDataCheck->user_email != $email){
							$result = 5;
							$_SESSION['add_inspector_email']='<div class="error-edit-profile">Email Id already exists!</div>';
							header('location:pms.php?sect=add_sub_loc&mode=edit&uId='.base64_encode($_POST['userId']));
						}else{
							$q = "UPDATE user SET
										active = 1,
										user_verifycode = '".$verify_code."',
										user_fullname = '".$full_name."',
										user_email = '".$email."',
										user_name = '".$user_name."',
										user_password = '".$password."',
										user_plainpassword = '".$plain."',
										user_phone_no = '".$phone."',
										last_modified_date = NOW(),
										last_modified_by = '".$_SESSION['ww_builder_id']."'
								WHERE
										user_id = '".$_POST['userId']."'";
								//echo $q; die;
							$obj->db_query($q);
								
							$upQuery = "UPDATE user_projects SET
								user_role = '".$userRole."',
								cron_report_type = '".$cron_report_type."',
								allow_rfi = '".$allow_rfi."',
								issued_to = '".$issueTo."',
								last_modified_date = NOW(),
								last_modified_by = '".$_SESSION['ww_builder_id']."'
							WHERE
								user_id = '".$_POST['userId']."' AND
								project_id = '".$_SESSION['idp']."'";
							mysql_query($upQuery);
							if($userRole == 'Sub Contractor'){
								$uQry = "UPDATE user_permission SET
												is_allow = '0',
												last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
												last_modified_date = NOW()
											WHERE
												permission_name = 'iPhone_edit_inspection' AND
												user_id = '".$_POST['userId']."' AND
												project_id = '".$_SESSION['idp']."' AND
												is_deleted = 0";
								mysql_query($uQry);
							}
							$ms = 'dXBkYXRl';			
							$result = 6;
							$id=$_SESSION['idp'];
							$hb=$_SESSION['hb'];
							$id=base64_encode($id);
							$hb=base64_encode($hb);
							//pms.php?sect=add_project_detail&id=Mw==&hb=MQ==
							$_SESSION['add_inspector_success'] = $ms;	
							header('location:pms.php?sect=show_sub_loc');	
						}
					}else{
						$q = "UPDATE user SET
									active = 1,
									user_verifycode = '".$verify_code."',
									user_fullname = '".$full_name."',
									user_email = '".$email."',
									user_name = '".$user_name."',
									user_password = '".$password."',
									user_plainpassword = '".$plain."',
									user_phone_no = '".$phone."',
									last_modified_date = NOW(),
									last_modified_by = '".$_SESSION['ww_builder_id']."'
							WHERE user_id = '".$_POST['userId']."'";
							//echo $q; die;
						$obj->db_query($q);
$upQuery = "UPDATE user_projects SET
				user_role = '".$userRole."',
				cron_report_type = '".$cron_report_type."',
				allow_rfi = '".$allow_rfi."',
				issued_to = '".$issueTo."',
				last_modified_date = NOW(),
				last_modified_by = '".$_SESSION['ww_builder_id']."'
			WHERE
				user_id = '".$_POST['userId']."' AND
				project_id = '".$_SESSION['idp']."'";
	mysql_query($upQuery);
if($userRole == 'Sub Contractor'){
	$uQry = "UPDATE user_permission SET
					is_allow = 0,
					last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
					last_modified_date = NOW()
				WHERE
					permission_name = 'iPhone_edit_inspection' AND
					user_id = '".$_POST['userId']."' AND
					project_id = '".$_SESSION['idp']."' AND
					is_deleted = 0";
	mysql_query($uQry);
}
						$ms = 'dXBkYXRl';			
						$result = 6;
						$id=$_SESSION['idp'];
						$hb=$_SESSION['hb'];
						$id=base64_encode($id);
						$hb=base64_encode($hb);
						//pms.php?sect=add_project_detail&id=Mw==&hb=MQ==
						$_SESSION['add_inspector_success'] = $ms;	
						header('location:pms.php?sect=show_sub_loc');	
					}
				}
			}else{
				$q = "UPDATE user SET
							active = 1,
							user_verifycode = '".$verify_code."',
							user_fullname = '".$full_name."',
							user_email = '".$email."',
							user_name = '".$user_name."',
							user_password = '".$password."',
							user_plainpassword = '".$plain."',
							user_phone_no = '".$phone."',
							last_modified_date = NOW(),
							last_modified_by = '".$_SESSION['ww_builder_id']."'
						WHERE
							user_id = '".$_POST['userId']."'";
					//echo $q; die;
				$obj->db_query($q);
			
			$upQuery = "UPDATE user_projects SET
					user_role = '".$userRole."',
					allow_rfi = '".$allow_rfi."',
					cron_report_type = '".$cron_report_type."',
					issued_to = '".$issueTo."',
					last_modified_date = NOW(),
					last_modified_by = '".$_SESSION['ww_builder_id']."'
				WHERE
					user_id = '".$_POST['userId']."' AND
					project_id = '".$_SESSION['idp']."'";die;
mysql_query($upQuery);	
if($userRole == 'Sub Contractor'){
	$uQry = "UPDATE user_permission SET
					is_allow = '0',
					last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
					last_modified_date = NOW()
				WHERE
					permission_name = 'iPhone_edit_inspection' AND
					user_id = '".$_POST['userId']."' AND
					project_id = '".$_SESSION['idp']."' AND
					is_deleted = 0";
	mysql_query($uQry);
}
				$ms = 'dXBkYXRl';			
				$result = 6;
				$id=$_SESSION['idp'];
				$hb=$_SESSION['hb'];
				$id=base64_encode($id);
				$hb=base64_encode($hb);
				//pms.php?sect=add_project_detail&id=Mw==&hb=MQ==
				$_SESSION['add_inspector_success'] = $ms;	
				header('location:pms.php?sect=show_sub_loc');	
			}
		}
		sleep(1);
	}
}

/* add inspector for project by builder 
elseif($sect=='add_inspector'){
	
	
	$button=$_POST['button'];
	if(isset($_POST['ownerName']) && !empty($_POST['ownerName']))
	{
		$full_name=mysql_real_escape_string(trim($_POST['ownerName']));
		$_SESSION['add_sub_loc']['full_name']=$full_name;
		
	}
	
	if(isset($_POST['userName']) && !empty($_POST['userName']))
	{
		$user_name=mysql_real_escape_string(trim($_POST['userName']));
		$_SESSION['add_sub_loc']['userName']=$user_name;
	}
	if(isset($_POST['password']) && !empty($_POST['password']))
	{	
		$password=mysql_real_escape_string(trim($_POST['password']));
		$_SESSION['add_sub_loc']['password']=$password;
	}
	if(isset($_POST['proId']) && !empty($_POST['proId']))
	{
		$proId=mysql_real_escape_string(trim($_POST['proId']));
		$_SESSION['add_sub_loc']['proId']=$proId;
	}
	if(isset($_POST['phone']) && !empty($_POST['phone']))
	{	
		$phone=mysql_real_escape_string(trim($_POST['phone']));
		$_SESSION['add_sub_loc']['phone']=$phone;
	}
	if(isset($_POST['email']) && !empty($_POST['email']))
	{
		$email=mysql_real_escape_string(trim($_POST['email']));
		$_SESSION['add_sub_loc']['email']=$email;
	}
	$ownerId = '';
	$owner_exist = 0;
	
	if($full_name=='' || $user_name=='' || $password=='' || $phone=='' || $email==''){
		$result = 0;
	
		//$_SESSION['add_inspector']='';
		//header('location:');
	}elseif(!is_numeric($phone)){
		$result = 1;	
		$_SESSION['add_inspector']='<div class="error-edit-profile">Invalid phone number!</div>';
		header('location:pms.php?sect=add_sub_loc');
	}elseif($obj->isValidEmail($email)==false){
		$result = 2;
		$_SESSION['add_inspector']='<div class="error-edit-profile">Invalid email id!</div>';
		header('location:pms.php?sect=add_sub_loc');
	}elseif(strlen($password)<9){
		$result = 3;
	}else{
		//echo 'check username'; die;
		$q = "SELECT * FROM ".OWNERS." WHERE user_name = '$user_name'";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$result = 4;
			$_SESSION['add_inspector_user']='<div class="error-edit-profile">Username already exists!</div>';
			
		header('location:pms.php?sect=add_sub_loc');
		}else{
			$phone=mysql_real_escape_string(trim($_POST['phone']));
			$email=mysql_real_escape_string(trim($_POST['email']));
			
			//echo 'check if inspector email already exist!'; die;
			$q = "SELECT * FROM ".OWNERS." WHERE email = '$email'";
			if($obj->db_num_rows($obj->db_query($q)) > 0){
				$result = 5;
				$_SESSION['add_inspector_email']='<div class="error-edit-profile">Email Id already exists!</div>';
		header('location:pms.php?sect=add_sub_loc');
			}else{
				// create inspector & alloted project to him.
				$verify_code = md5($obj->rendom(15));
				$plain=$password;
				$password=md5($plain);
				
				// add inspector
				$q = "INSERT INTO ".OWNERS." (active,verify_code,owner_full_name,email,user_name,password,plain_pswd,phone,ow_project_id)
				 VALUES('1','$verify_code','$full_name','$email','$user_name','$password','$plain','$phone','$proId')";
				//echo $q; die;
				$obj->db_query($q);				
				
				if($button=='save'){
					
					$result = 6;
					$id=$_SESSION['idp'];
					$hb=$_SESSION['hb'];
					$id=base64_encode($id);
					$hb=base64_encode($hb);
				
					//pms.php?sect=add_project_detail&id=Mw==&hb=MQ==
					$_SESSION['add_inspector_success']='Inspactor added successfully!';	
					header('location:pms.php?sect=add_project_detail&id='.$id.'&hb='.$hb);	
				}elseif($button=='save_n_new'){
					
					$result = 7;
					$id=$_SESSION['proj_id'];
					$hb=$_SESSION['hb'];
					$id=base64_encode($id);
					$hb=base64_encode($hb);
					$_SESSION['add_inspector_success']='Inspactor added successfully!';	
					header('location:pms.php?sect=pro_code');
				}				
				sleep(1);
			}				
		}
	}
}*/

/* edit / remove inspector for project by builder */
elseif($sect=='edit_remove_inspector'){
	
	$builder_id=$_SESSION['ww_builder_id'];
	$owner_id=mysql_real_escape_string(trim($_POST['owner_id']));
	
	if($owner_id==''){
		$result=9;
	}else{
		// update inspector
		if(isset($_POST['edit'])){
			if(isset($_POST['proId']) && !empty($_POST['proId']))
			{	
				$proId=mysql_real_escape_string(trim($_POST['proId']));		
			}
			if(isset($_POST['ownerName']) && !empty($_POST['ownerName']))
			{
				$full_name=mysql_real_escape_string(trim($_POST['ownerName']));
			}
			if(isset($_POST['userName']) && !empty($_POST['userName']))
			{
				$user_name=mysql_real_escape_string(trim($_POST['userName']));
			}
			if(isset($_POST['password']) && !empty($_POST['password']))
			{
				$password=mysql_real_escape_string(trim($_POST['password']));
			}
			if(isset($_POST['phone']) && !empty($_POST['phone']))
			{
				$phone=mysql_real_escape_string(trim($_POST['phone']));
			}
			if(isset($_POST['email']) && !empty($_POST['email']))
			{
				$email=mysql_real_escape_string(trim($_POST['email']));
			}
			if($full_name=='' || $user_name=='' || $password=='' || $phone=='' || $email==''){
				$result = 0;
			}elseif(!is_numeric($phone)){
				$result = 1;
				$_SESSION['edit_remove_inspector']['phone']='<div class="error-edit-profile">Invalid phone number. Only numbers are allow</div>';
					$insp_ids=base64_encode($_SESSION['inpector_id']);
					header('location:pms.php?sect=edit_sub_loc&id='.$insp_ids);	
			}elseif($obj->isValidEmail($email)==false){
				$result = 2;
				$_SESSION['edit_remove_inspector']['email']='<div class="error-edit-profile">Invalid email id</div>';
					$insp_ids=base64_encode($_SESSION['inpector_id']);
					header('location:pms.php?sect=edit_sub_loc&id='.$insp_ids);
			}elseif(strlen($password)<9){
				$result = 3;
				$_SESSION['edit_remove_inspector']['username']='<div class="error-edit-profile">The password must be greater than 8 character</div>';
					$insp_ids=base64_encode($_SESSION['inpector_id']);
					header('location:pms.php?sect=edit_sub_loc&id='.$insp_ids);

			}else{
				// check username
				$q = "SELECT * FROM ".OWNERS." WHERE user_name = '$user_name' AND id != '$owner_id'";
				if($obj->db_num_rows($obj->db_query($q)) > 0){
					$result = 4;
					$_SESSION['edit_remove_inspector']['username']='<div class="error-edit-profile">Username already exists!</div>';
					$insp_ids=base64_encode($_SESSION['inpector_id']);
					header('location:pms.php?sect=edit_sub_loc&id='.$insp_ids);
				}else{
					// check email id
					$q = "SELECT * FROM ".OWNERS." WHERE email = '$email' AND id != '$owner_id'";
					$r = $obj->db_query($q);
					if($obj->db_num_rows($r) > 0){
						$result = 5;
						$_SESSION['edit_remove_inspector']['email']='<div class="error-edit-profile">Email already exists!</div>';
					$insp_ids=base64_encode($_SESSION['inpector_id']);
					header('location:pms.php?sect=edit_sub_loc&id='.$insp_ids);
					}else{
						// if project had already alloted?
						$f=$obj->db_fetch_assoc($r);
						if($f['ow_project_id']==$proId){
							$result = 6;
							$_SESSION['edit_remove_inspector']['already']='Already inspector of that project!';
					$insp_ids=base64_encode($_SESSION['inpector_id']);
					header('location:pms.php?sect=edit_sub_loc&id='.$insp_ids);
							
							
						}else{
							$plain=$password;
							$password=md5($plain);
			
							// edit inspector record
							$q = "UPDATE ".OWNERS." SET owner_full_name='$full_name', email='$email', user_name='$user_name', 
								  password='$password', plain_pswd='$plain', phone='$phone' WHERE id='$owner_id' ";
							$obj->db_query($q);
							
							$result = 7;
							$_SESSION['edit_remove_inspector']['success']='Inspector edited successfully!';
				
					header('location:pms.php?sect=show_sub_loc&id='.$_SESSION['project_id']);
														
						}
					}
				}
			}
		// remove inspector	
		}elseif(isset($_POST['remove'])){
			$obj->db_query("DELETE FROM ".OWNERS." WHERE id='$owner_id'");	// remove from owner table
			
			if($obj->db_num_rows($obj->db_query("SELECT df_id FROM ".DEFECTS." WHERE owner_id='$owner_id'"))>0)
				$obj->db_query("DELETE FROM ".DEFECTS." WHERE owner_id='$owner_id'");	// remove all defects submitted by this inspector
			
			$result=8;
		}
	}
	sleep(1);
}

/* add defect by owner */
elseif($sect=='add_defect'){
	$projectId = $_SESSION['idp'];
	$inspectedBy = mysql_real_escape_string(trim($_POST['inspectedBy'])); 
	$dateRaised = mysql_real_escape_string(trim($_POST['dateRaised']));
	$dateRaised = $dateRaised != '' ? $object->dateChanger('/', '-', $dateRaised) : '';
	$ownerId = $_SESSION['ww_builder']['user_id'];
	$defectType = mysql_real_escape_string(trim($_POST['defect_type']));
	$location = mysql_real_escape_string(trim($_POST['location']));
	$locationTree = mysql_real_escape_string(trim($_POST['locationTree']));
	$defectDesc = mysql_real_escape_string(trim($_POST['defect_desc']));
	$defectNote = mysql_real_escape_string(trim($_POST['defect_note']));
	$raisedBy = mysql_real_escape_string(trim($_POST['raisedBy']));

	$imagesRoot = 'inspections/';
	if($defectDesc=='' || $raisedBy==''){
		$_SESSION['post_array'] = $_POST;
		header('location:pms.php?sect=add_defect');
	}else{
//inspectionId generator Dated 27-08-2012
$preInspectionID = $object->getDataByKey('unique_inspectionid', 'is_deleted', '0', 'MAX(inspectionid)');
if($preInspectionID){
	$inspectionID = $object->selQRY('MAX(inspectionid) as inspectionid', 'unique_inspectionid', 'is_deleted = 0');
	$rs = $obj->db_query("INSERT INTO unique_inspectionid SET inspectionid='".++$inspectionID['inspectionid']."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder']['user_id']."', last_modified_by='".$_SESSION['ww_builder']['user_id']."', created_date=NOW(), created_by='".$_SESSION['ww_builder']['user_id']."', last_modified_date=NOW(), last_modified_by='".$_SESSION['ww_builder']['user_id']."'");
	if($rs){
		$newInspectionID = mysql_insert_id();
	}
}else{
	$inspectionID = $object->selQRY('MAX(inspection_id) as inspectionid', 'project_inspections', 'is_deleted = 1');
	$rs = $obj->db_query("INSERT INTO unique_inspectionid SET inspectionid='".$inspectionID['inspectionid']."', project_id='".$_SESSION['idp']."', user_id='".$_SESSION['ww_builder']['user_id']."', last_modified_by='".$_SESSION['ww_builder']['user_id']."', created_date=NOW(), created_by='".$_SESSION['ww_builder']['user_id']."', last_modified_date=NOW(), last_modified_by='".$_SESSION['ww_builder']['user_id']."'");
	if($rs){
		$newInspectionID = mysql_insert_id();
	}
}
$newInspectionID;
//inspectionId generator Dated 27-08-2012
		$q = "INSERT INTO project_inspections SET
					inspection_id = '".$newInspectionID."',
					project_id = '".$projectId."',
					location_id = '".$location."',
					inspection_inspected_by = '".$inspectedBy."',
					inspection_date_raised = '".$dateRaised."',
					inspection_type = '".$defectType."',
					inspection_description = '".$defectDesc."',
					inspection_notes = '".$defectNote."',
					inspection_sign_image = '',
					inspection_location = '".$locationTree."',
					inspection_raised_by = '".$raisedBy."',
					created_date = NOW(),
					created_by = '".$ownerId."',
					last_modified_date = NOW(),
					last_modified_by = '".$ownerId."'";
		$obj->db_query($q);
		$inspectionId = $newInspectionID;
		if($_SESSION['checkList']){
			$keyCheckList = array_keys($_SESSION['checkList']);
			for($i=0; $i<sizeof($_SESSION['checkList']); $i++){
				$insertQRY = "INSERT INTO inspection_check_list SET
									project_id = '".$projectId."',
									inspection_id = '".$inspectionId."',
									check_list_items_id = '".$keyCheckList[$i]."',
									check_list_items_status = '".$_SESSION['checkList'][$keyCheckList[$i]]."',
									created_date = NOW(),
									created_by = '".$ownerId."',
									last_modified_date = NOW(),
									last_modified_by = '".$ownerId."'";
				mysql_query($insertQRY);
			}
		}
		for($i=0; $i<sizeof($_POST["issueTo"]); $i++){
			$issueTo = mysql_real_escape_string(trim($_POST['issueTo'][$i]));
			$costAttribute = mysql_real_escape_string(trim($_POST['costAttribute'][$i]));
			$fixedByDate = mysql_real_escape_string(trim($_POST['fixedByDate'][$i]));
			$status = mysql_real_escape_string(trim($_POST['status'][$i]));

			if($fixedByDate != ''){
				$fixedByDate = date('Y-m-d', strtotime($fixedByDate));
				$selIssueTo = $object->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'issue_to_name = "'.$issueTo.'" AND is_deleted = 0');
				if(empty($selIssueTo)){
					$insertNewIssueTo = "INSERT INTO inspection_issue_to SET
											issue_to_name = '".addslashes($issueTo)."',
											last_modified_date = NOW(),
											last_modified_by = '".$ownerId."',
											created_date = NOW(),
											created_by = '".$ownerId."',
											project_id = '".$projectId."',
											is_deleted = '0'";
					$obj->db_query($insertNewIssueTo);
				}
				$insertIssueTo = "INSERT INTO issued_to_for_inspections SET
									inspection_id = '".$inspectionId."',
									issued_to_name = '".addslashes($issueTo)."',
									inspection_fixed_by_date = '".addslashes($fixedByDate)."',
									cost_attribute = '".addslashes($costAttribute)."',
									inspection_status = '".addslashes($status)."',
									created_date = NOW(),
									created_by = '".$ownerId."',
									last_modified_date = NOW(),
									last_modified_by = '".$ownerId."',
									project_id = '".$projectId."'";
				$obj->db_query($insertIssueTo);
			}
		}
		if(isset($_POST["photo"])){
			for($i=0; $i<sizeof($_POST["photo"]); $i++){
				$photoName = mysql_real_escape_string(trim($_POST['photo'][$i]));
				$insertImage = "INSERT INTO inspection_graphics SET
									inspection_id = '".$inspectionId."',
									graphic_type = 'images',
									graphic_name = '".addslashes($photoName)."',
									created_date = NOW(),
									created_by = '".$ownerId."',
									last_modified_date = NOW(),
									last_modified_by = '".$ownerId."',
									project_id = '".$projectId."'";
				$obj->db_query($insertImage);
			}
		}
		if(isset($_POST['drawing'])){
			$drawingName = mysql_real_escape_string(trim($_POST['drawing']));
			$insertImageDrawing = "INSERT INTO inspection_graphics SET
										inspection_id = '".$inspectionId."',
										graphic_type = 'drawing',
										graphic_name = '".addslashes($drawingName)."',
										last_modified_date = NOW(),
										last_modified_by = '".$ownerId."',
										created_date = NOW(),
										created_by = '".$ownerId."',
										project_id = '".$projectId."'";
			$obj->db_query($insertImageDrawing);
		}
		if($_POST['submit_action'] == 'save'){
			$_SESSION['inspection_added'] = 'Inspection added successfully!';	
			header('location:pms.php?sect=i_defect&bk=Y');
		}elseif($_POST['submit_action'] == 'save_n_new'){
			$_SESSION['inspection_added'] = 'Inspection added successfully!';	
			header('location:pms.php?sect=add_defect');
		}	
		sleep(1);		
	}
}

/* add defect by owner */
elseif($sect=='add_non_confirmance_inspection'){
	$projectId = $_SESSION['projIdQA'];
	$task_id = $_SESSION['task_id'];
	$ownerId = $_SESSION['ww_builder']['user_id'];
	$defectDesc = mysql_real_escape_string(trim($_POST['defect_desc']));
	$raisedBy = mysql_real_escape_string(trim($_POST['raisedBy']));
	
	$imagesRoot = 'inspections/';
	if($defectDesc=='' || $raisedBy==''){
		$_SESSION['post_array'] = $_POST;
		header('location:pms.php?sect=add_non_confirmance_inspection&id='.base64_encode($_SESSION['task_id']));
	}else{
		//inspectionId generator Dated 27-08-2012
		$preInspectionID = $object->getDataByKey('qa_unique_inspectionid', 'is_deleted', '0', 'MAX(qa_inspectionid)');
		if($preInspectionID){
			$inspectionID = $object->selQRY('MAX(qa_inspectionid) as qa_inspectionid', 'qa_unique_inspectionid', 'is_deleted = 0');
			$rs = $obj->db_query("INSERT INTO qa_unique_inspectionid SET qa_inspectionid='".++$inspectionID['qa_inspectionid']."', project_id='".$_SESSION['projIdQA']."', user_id='".$_SESSION['ww_builder']['user_id']."', last_modified_by='".$_SESSION['ww_builder']['user_id']."', created_date=NOW(), created_by='".$_SESSION['ww_builder']['user_id']."', last_modified_date=NOW(), last_modified_by='".$_SESSION['ww_builder']['user_id']."'");
			if($rs){
				$newInspectionID = mysql_insert_id();
			}
		}else{
			$inspectionID = $object->selQRY('MAX(inspection_id) as qa_inspectionid', 'project_inspections', 'is_deleted = 1');
			$rs = $obj->db_query("INSERT INTO qa_unique_inspectionid SET qa_inspectionid='".$inspectionID['qa_inspectionid']."', project_id='".$_SESSION['projIdQA']."', user_id='".$_SESSION['ww_builder']['user_id']."', last_modified_by='".$_SESSION['ww_builder']['user_id']."', created_date=NOW(), created_by='".$_SESSION['ww_builder']['user_id']."', last_modified_date=NOW(), last_modified_by='".$_SESSION['ww_builder']['user_id']."'");
			if($rs){
				$newInspectionID = mysql_insert_id();
			}
		}
		$newInspectionID;
		$recodArr = array();//Store record to create history table
		$NCR = isset($_POST["ncr"]) ? $_POST["ncr"] : '';
		//inspectionId generator Dated 27-08-2012
		$q = "INSERT INTO qa_inspections SET
					qa_inspection_date_raised = NOW(),
					location_id = ".$_POST['locationID'].",
					non_conformance_id = '".$newInspectionID."',
					project_id = '".$projectId."',
					task_id = '".$task_id."',
					qa_inspection_description = '".$defectDesc."',
					qa_inspection_raised_by = '".$raisedBy."',
					created_date = NOW(),
					original_modified_date = NOW(),
					qa_inspection_inspected_by = '".$_SESSION['ww_builder_user_name']."',
					qa_inspection_location = '".$_POST['qaInspectionLocation']."',
					created_by = '".$ownerId."',
					last_modified_date = NOW(),
					ncr = '".$NCR."',
					last_modified_by = '".$ownerId."'";
		$obj->db_query($q);
		$inspectionId = $newInspectionID;
//Record Array to store data start here
		$recodArr['qa_inspection_date_raised'] = 'NOW()';
		$recodArr['location_id'] = $_POST['locationID'];
		$recodArr['non_conformance_id'] = $newInspectionID;
		$recodArr['project_id'] = $projectId;
		$recodArr['task_id'] = $task_id;
		$recodArr['qa_inspection_description'] = $defectDesc;
		$recodArr['qa_inspection_raised_by'] = $raisedBy;
		$recodArr['qa_inspection_inspected_by'] = $_SESSION['ww_builder_user_name'];	
		$recodArr['qa_inspection_location'] = $_POST['qaInspectionLocation'];
		$recodArr['ncr'] = $NCR;
//Record Array to store data end here
		$issueToArr = array(); $costAttributeArr = array(); $fixedByDateArr = array(); $statusArr = array();
		for($i=0; $i<sizeof($_POST["issueTo"]); $i++){
			$issueTo = mysql_real_escape_string(trim($_POST['issueTo'][$i]));
			$costAttribute = mysql_real_escape_string(trim($_POST['costAttribute'][$i]));
			$fixedByDate = mysql_real_escape_string(trim($_POST['fixedByDate'][$i]));
			$status = mysql_real_escape_string(trim($_POST['status'][$i]));
			if($fixedByDate != ''){
				$issueToArr[] = $issueTo;
				$costAttributeArr[] = $costAttribute;
				$fixedByDateArr[] = $fixedByDate;
				$statusArr[] = $status;
			
				$fixedByDate = date('Y-m-d', strtotime($fixedByDate));
				$selIssueTo = $object->selQRYMultiple('issue_to_name', 'inspection_issue_to', 'issue_to_name = "'.$issueTo.'" AND is_deleted = 0');
				if(empty($selIssueTo)){
					$insertNewIssueTo = "INSERT INTO inspection_issue_to SET
											issue_to_name = '".addslashes($issueTo)."',
											created_date = NOW(),
											created_by = '".$ownerId."',
											last_modified_date = NOW(),
											last_modified_by = '".$ownerId."',
											project_id = '".$projectId."',
											is_deleted = '0'";
					$obj->db_query($insertNewIssueTo);
				}
				$insertIssueTo = "INSERT INTO qa_issued_to_inspections SET
											non_conformance_id = '".$newInspectionID."',
											task_id    = '".$task_id."',
											qa_issued_to_name = '".addslashes($issueTo)."',
											qa_inspection_fixed_by_date = '".addslashes($fixedByDate)."',
											qa_cost_attribute = '".addslashes($costAttribute)."',
											qa_inspection_status = '".addslashes($status)."',
											created_date = NOW(),
											original_modified_date = NOW(),
											last_modified_date = NOW(),
											last_modified_by = '".$ownerId."',
											created_by = '".$ownerId."',
											project_id = '".$projectId."',
											is_deleted = '0'";
				$obj->db_query($insertIssueTo);
			}
		}
//Record Array to store data start here
		$recodArr['qa_issued_to_name'] = $issueToArr;
		$recodArr['qa_inspection_fixed_by_date'] = $fixedByDateArr;
		$recodArr['qa_cost_attribute'] = $costAttributeArr;
		$recodArr['qa_inspection_status'] = $statusArr;
//Record Array to store data end here
		if(isset($_POST["photo"])){
			for($i=0; $i<sizeof($_POST["photo"]); $i++){
				$photoName = mysql_real_escape_string(trim($_POST['photo'][$i]));
				$insertImage = "INSERT INTO qa_graphics SET
									non_conformance_id = '".$newInspectionID."',
									task_id    = '".$task_id."',
									qa_graphic_type = 'images',
									qa_graphic_name = '".addslashes($photoName)."',
									created_date = NOW(),
									last_modified_date = NOW(),
									last_modified_by = '".$ownerId."',
									original_modified_date = NOW(),
									created_by = '".$ownerId."',
									project_id = '".$projectId."'";
				$obj->db_query($insertImage);
//Record Array to store data start here
		$recodArr['non_conformance_id'] = $newInspectionID;
		$recodArr['qa_graphic_type1'] = 'image';
		$recodArr['qa_graphic_name1'] = $photoName;
//Record Array to store data end here
			}
		}
		if(isset($_POST['drawing'])){
			$drawingName = mysql_real_escape_string(trim($_POST['drawing']));
			$insertImageDrawing = "INSERT INTO qa_graphics SET
										non_conformance_id = '".$newInspectionID."',
										task_id    = '".$task_id."',
										qa_graphic_type = 'drawing',
										qa_graphic_name = '".addslashes($drawingName)."',
										created_date = NOW(),
										original_modified_date = NOW(),
										last_modified_date = NOW(),
										last_modified_by = '".$ownerId."',
										created_by = '".$ownerId."',
										project_id = '".$projectId."'";
			$obj->db_query($insertImageDrawing);
//Record Array to store data start here
		$recodArr['qa_graphic_type2'] = 'drawing';
		$recodArr['qa_graphic_name2'] = $drawingName;
//Record Array to store data end here
		}
		if(isset($_SESSION['task_id']) and $_SESSION['task_id']!=''){
			$updte_TaskStatus = "UPDATE  qa_task_monitoring SET
									status = 'No',
									last_modified_date = NOW(),
									last_modified_by = '".$_SESSION['ww_builder_id']."'
								WHERE
									task_id = '".$task_id."'";
			$obj->db_query($updte_TaskStatus);
		}
		if($_POST['submit_action'] == 'save'){
			$_SESSION['inspection_added'] = 'Inspection added successfully!';	
//Record Array Insert Here
			$insertHistory = "INSERT INTO table_history_details SET
						primary_key = '".$newInspectionID."',
						table_name = 'qa_inspections',
						sql_operation = 'INSERT',
						sql_query = '".serialize($recodArr)."',
						created_by = '".$_SESSION['ww_builder_id']."',
						created_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						last_modified_date = NOW(),
						project_id = '".$projectId."'";
			mysql_query($insertHistory);	
			header('location:pms.php?sect=qa_task_search&bk=Y');
		}	
		sleep(1);		
	}
}


/* add qa ncr task detail */
elseif($sect=='add_qa_ncr_task_detail'){
	$projectId = $_SESSION['idp'];
	$task_id = $_SESSION['task_id'];
	$ownerId = $_SESSION['ww_builder']['user_id'];
	$defectDesc = mysql_real_escape_string(trim($_POST['defect_desc']));
	$raisedBy = mysql_real_escape_string(trim($_POST['raisedBy']));
	$str = $_POST['id_url_value'];
	if($defectDesc=='' || $raisedBy==''){
		$_SESSION['post_array'] = $_POST;		
		$_SESSION['inspection_added'] = 'Please check required field!';	
		header('location:pms.php?sect=add_qa_ncr_task_detail&id='.$str);
	}else{
		$q = "INSERT INTO qa_ncr_task_detail SET
					task_id    = '".$task_id."',
					comment = '".$defectDesc."',
					raised_by = '".$raisedBy."',
					created_date = NOW(),
					created_by = '".$ownerId."',
					last_modified_date = NOW(),
					last_modified_by = '".$ownerId."'";
		$obj->db_query($q);
		$task_detail_id = mysql_insert_id();
		if(isset($_SESSION['task_id']) and $_SESSION['task_id']!='')
		{
				$updte_TaskStatus = "UPDATE qa_task_monitoring SET
										status = 'Yes',
										last_modified_date = NOW(),
										last_modified_by = '".$_SESSION['ww_builder_id']."'
									WHERE
										task_id = '".$task_id."'";
				//$obj->db_query($updte_TaskStatus);
		}
		$attachments = '';
		$attachTitle = '';
		$attachDescription =  '';
		if(isset($_POST['attachments']))
		{
			$attachments = $_POST['attachments'];
			$attachTitle = $_POST['attachTitle'];
			$attachDescription = $_POST['attachDescription'];
		}
		if(isset($attachments) && !empty($attachments))
		{
			foreach($attachments as $key=>$name)
			{ 
					$name = trim($name);				
					$qProject = "INSERT INTO qa_ncr_task_detail_attachments SET 
										task_detail_id = '".$task_detail_id."',
										attachment_title = '".$attachTitle[$key]."',
										attachment_description = '".$attachDescription[$key]."',
										attachment_file_name = '".$name."',
										created_date = NOW(),
										original_modified_date = NOW(),
										created_by = '".$ownerId."',
										last_modified_by = '".$ownerId."'";
					$obj->db_query($qProject);		
			}
		}
		if($_POST['submit_action'] == 'save')
		{
			$_SESSION['inspection_added'] = 'Task Detail added successfully!';	
			header('location:pms.php?sect=qa_task_search&bk=Y');
		}elseif($_POST['submit_action'] == 'save_n_new'){
			$_SESSION['inspection_added'] = 'Task Detail added successfully!';				
			header('location:pms.php?sect=add_qa_ncr_task_detail&id='.$_SESSION['idp']);
		}	
		sleep(1);		
	}
}

/* edit qa ncr task detail */
elseif($sect=='edit_qa_ncr_task_detail'){
	$projectId = $_SESSION['idp'];
	$task_id = $_SESSION['task_id'];
	$ownerId = $_SESSION['ww_builder']['user_id'];
	$task_detail_id = $_SESSION['task_detail_id'];
	
	
	$defectDesc = mysql_real_escape_string(trim($_POST['defect_desc']));
	$raisedBy = mysql_real_escape_string(trim($_POST['raisedBy']));
	$str = $_POST['id_url_value'];
	if($defectDesc=='' || $raisedBy==''){
		$_SESSION['post_array'] = $_POST;		
		$_SESSION['inspection_added'] = 'Please check required field!';	
		header('location:pms.php?sect=edit_qa_ncr_task_detail&id='.$str);
	}else{
		$q = "UPDATE qa_ncr_task_detail SET
					task_id    = '".$task_id."',
					comment = '".$defectDesc."',
					raised_by = '".$raisedBy."',
					last_modified_date = NOW(),
					last_modified_by = '".$ownerId."'
				WHERE
					task_detail_id = '".$task_detail_id."'";
		$obj->db_query($q);		
		if(isset($_SESSION['task_id']) and $_SESSION['task_id']!='')
		{
				$updte_TaskStatus = "UPDATE qa_task_monitoring SET
										status = 'Yes',
										last_modified_date = NOW(),
										last_modified_by = '".$_SESSION['ww_builder_id']."'
									WHERE
										task_id = '".$task_id."'";
				//$obj->db_query($updte_TaskStatus);
		}
		$attachments = '';
		$attachTitle = '';
		$attachDescription =  '';
		if(isset($_POST['attachments']))
		{
			$attachments = $_POST['attachments'];
			
			
			$attachTitle = $_POST['attachTitle'];
			$attachDescription = $_POST['attachDescription'];
		}
		
		if($attachments=='')
		{
			$attachments = array();				
		}
		if(isset($task_detail_id) && $task_detail_id>0){
				
			$attachedData = $object->selQRYMultiple('ncr_attachment_id, attachment_title, attachment_description, attachment_file_name, attachment_type', 'qa_ncr_task_detail_attachments', 'task_detail_id = "'.$task_detail_id.'" and is_deleted=0');
			// File section
			if(!empty($attachedData))
			{
		
				foreach($attachedData as $val){
					if(!in_array($val['attachment_file_name'], $attachments))
					{
						$updateAttachment = "UPDATE qa_ncr_task_detail_attachments SET
												is_deleted='1',
												last_modified_date = NOW(),
												original_modified_date = NOW(),
												last_modified_by = ".$_SESSION['ww_builder_id']."
											WHERE task_detail_id = '".$task_detail_id."'					
						";
						mysql_query($updateAttachment) or die(mysql_error());
						@unlink('./inspections/ncr_task_files/'.$val['attachment_file_name']);
					}
				}
			}
			
			
			
		}		


		if(isset($attachments) && !empty($attachments)){
			
			foreach($attachments as $key=>$name){ 
			    $name = trim($name);
				$attachedData = $object->selQRYMultiple('ncr_attachment_id', 'qa_ncr_task_detail_attachments', 'attachment_file_name = "'.$name.'" and is_deleted=0');
				
				if($attachedData[0]['ncr_attachment_id']>0)
				{
					//for edit
				}else{
					$qProject = "INSERT INTO qa_ncr_task_detail_attachments SET 
										task_detail_id = '".$task_detail_id."',
										attachment_title = '".$attachTitle[$key]."',
										attachment_description = '".$attachDescription[$key]."',
										attachment_file_name = '".$name."',
										created_date = NOW(),
										original_modified_date = NOW(),
										created_by = '".$ownerId."',
										last_modified_by = '".$ownerId."'";
					$obj->db_query($qProject);					
				}
			}
		}
		
	
		/*if(isset($attachments) && !empty($attachments))
		{
			foreach($attachments as $key=>$name)
			{ 
					$name = trim($name);				
					$qProject = "INSERT INTO qa_ncr_task_detail_attachments SET 
										task_detail_id = '".$task_detail_id."',
										attachment_title = '".$attachTitle[$key]."',
										attachment_description = '".$attachDescription[$key]."',
										attachment_file_name = '".$name."',
										created_date = NOW(),
										original_modified_date = NOW(),
										created_by = '".$ownerId."'";
					$obj->db_query($qProject);		
			}
		}
		*/
		
		
		
		
		
		if($_POST['submit_action'] == 'save')
		{
			$_SESSION['inspection_added'] = 'Task Detail updated successfully!';	
			header('location:pms.php?sect=qa_task_search&bk=Y');
		}elseif($_POST['submit_action'] == 'save_n_new'){
			$_SESSION['inspection_added'] = 'Task Detail updated successfully!';				
			header('location:pms.php?sect=add_qa_ncr_task_detail&id='.$_SESSION['idp']);
		}	
		sleep(1);		
	}
}

/* edit defect by owner, inspector */
elseif($sect=='o_edit_defect'){
	$df_id = mysql_real_escape_string(trim($_POST['df_id']));
	$fixed_by_date=mysql_real_escape_string(trim($_POST['fixed_by_date']));
	$owner_id=$_SESSION['ww_owner_id'];
	$defect_type=mysql_real_escape_string(trim($_POST['defect_type']));
	$priority=mysql_real_escape_string(trim($_POST['priority']));
	$repairer=mysql_real_escape_string(trim($_POST['repairer']));
	$area_room=mysql_real_escape_string(trim($_POST['area_room']));
	$defect_desc=mysql_real_escape_string(trim($_POST['defect_desc']));
	$defect_note=mysql_real_escape_string(trim($_POST['defect_note']));
	$inspected_by=mysql_real_escape_string(trim($_POST['inspected_by']));
	
	
	$destiny = 'defects_photo/';
	
	if($fixed_by_date=='' || $inspected_by=='' || $area_room=='' || $defect_desc==''){
		$result = 0;
	}else{
		if($_FILES["photo"]['tmp_name']!=''){
			$name = $_FILES["photo"]['name'];
			$tmp_name = $_FILES["photo"]['tmp_name'];
			$photo = $obj->upload_file($name, $tmp_name, $destiny);
			
			$d_photo=",photo='$photo'";
		}else{
			$d_photo = '';
		}
		
		// change date format for fixed_by_date
		$fixed_by_date = date("d/m/Y", strtotime($fixed_by_date));
		
		$q ="UPDATE ".DEFECTS." SET resp_id='$repairer',area_room='$area_room',defect_type_id='$defect_type',defect_desc='$defect_desc',
			defect_note='$defect_note' $d_photo ,priority='$priority',fixed_by_date='$fixed_by_date',inspected_by='$inspected_by' 
			WHERE df_id='$df_id'";
		$obj->db_query($q);
		
		$result = 1;
		sleep(1);
	}
}

/* edit defect by builder */
elseif($sect=='edit_defect'){
	$df_id = mysql_real_escape_string(trim($_POST['df_id']));
	$builder_id = $_SESSION['ww_builder_id'];
	$status = mysql_real_escape_string($_POST['status']);
	$defect_note = mysql_real_escape_string(trim($_POST['defect_note']));
	$resp_id = mysql_real_escape_string($_POST['repairer_id']);
	$assign_to_id = mysql_real_escape_string($_POST['assign_to_id']);
	$defect_type = mysql_real_escape_string(trim($_POST['defect_type']));

	$assign_id=0;
	
	$fixed_by_date = $_POST['fixed_by_date'];
	// change date format for fixed_by_date
	$fixed_by_date = date("d/m/Y", strtotime($fixed_by_date));
	
	$fixed_date = $_POST['fixed_date'];
	if($fixed_date!=''){
	// change date format for fixed_date
		$fixed_date = date("d/m/Y", strtotime($fixed_date));
	}
	
	if($fixed_by_date=='' || $resp_id=='Select'){
		$result = 0;
	}else{
		// get assign_to in if set
		if($assign_to_id!='Select'){
			$assign_id=$assign_to_id;
		}
	
		$q = "UPDATE ".DEFECTS." SET resp_id='$resp_id',assign_id='$assign_id',defect_note='$defect_note',fixed_by_date='$fixed_by_date',
			status='$status',fixed_date='$fixed_date',defect_type_id='$defect_type' WHERE df_id='$df_id'";
		$obj->db_query($q);
		
		// send reminder to respondible
		// get responsible email id
		$q = "SELECT resp_email FORM ".RESPONSIBLES." WHERE resp_id='$resp_id'";
		$f = $obj->db_fetch_assoc($obj->db_query($q));
		
		$builder_email = $_SESSION['ww_builder_email'];
		$builder_full_name = $_SESSION['ww_builder_full_name'];
		
		$detail = array();
		$detail['to']=$f['resp_email'];
		$detail['name']=$builder_full_name;
		$detail['from']=$builder_email;
		$detail['subject']="New Issue from ".SITE_NAME;
		$detail['msg']="<table width='80%'>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<td>A new issue has assigned to you.</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>You can login to your account at ".HOME_SCREEN."</td></tr>
				</table>";
		$obj->send_mail($detail);
		
		$result = 4;
		sleep(1);
	}
}

/* create report by builder */
elseif($sect=='create'){
	$builder_id=$_SESSION['ww_builder_id'];
	
	$pro_id=mysql_real_escape_string($_POST['project_name']);
	$resp_id=mysql_real_escape_string($_POST['repairer_name']);
	$status=mysql_real_escape_string($_POST['status']);
	$defect_type=mysql_real_escape_string($_POST['defect_type']);
	$report_type=mysql_real_escape_string($_POST['report_type']);
	
	$src_type='All';
	$src_status='All';
	$issued_to='All';
	
	//$w="p.id = '$pro_id' AND p.user_id = '$builder_id'";
	$w="p.id = '$pro_id' ";
	
	if($resp_id!=''){
		$w.=" AND d.resp_id = '$resp_id'";
	}
	if($status!=''){
		$w.=" AND d.status = '$status'";
		
		$src_status=$status;
	}
	if($defect_type!=''){
		$w.=" AND d.defect_type_id = '$defect_type'";
		$fdt=$obj->db_fetch_assoc($obj->db_query("SELECT dl_title FROM ".DEFECTSLIST." WHERE dl_id = '$defect_type'"));		
		
		$src_type=$fdt['dl_title'];
	}
	
	$q = "SELECT * FROM ".DEFECTS." d 
		  LEFT JOIN ".OWNERS." o ON o.id = d.owner_id 
		  LEFT JOIN ".PROJECTS." p ON p.project_id = d.project_id 
		  LEFT JOIN ".BUILDERS." b ON b.user_id = p.user_id 
		  LEFT JOIN ".RESPONSIBLES." r ON r.resp_id = d.resp_id 		  
		  WHERE ".$w." ORDER BY o.owner_full_name";
	
	if($obj->db_num_rows($obj->db_query($q)) == 0){
		$result = 0;
	}else{
		// create file
		if($report_type=='PDF'){
			$r=$obj->db_query($q);
			$tr='';
			$c=1;
			$class='';
			include('resize_image.php');
			
			while($f=mysql_fetch_assoc($r)){
				// project info 
				$pro_name = $f['project_name'];
				
				// If Issued To Contact Name is selected
				if($resp_id!=''){
					$issued_to = $f['resp_full_name'];
				}
				
				// builder info
				$builder_full_name = $f['user_fullname'];
				$mobile = $f['user_phone_no'];
				$builder_email = $f['user_email'];
			
				// change create date format
				if($f['create_date']!='0000-00-00'){
					$create_date = $f['create_date'];
					$create_date = date("d/m/Y", strtotime($create_date));
				}else{
					$create_date = '';
				}
				// change fixed date format
				if($f['fixed_date']!='0000-00-00'){
					$fixed_date = $f['fixed_date'];
				}else{
					$fixed_date = '';
				}
				// change fixed by date format
				if($f['fixed_by_date']!='0000-00-00'){
					$fixed_by_date = $f['fixed_by_date'];
				}else{
					$fixed_by_date = '';
				}
				
				if($c%2){
					$class='even_row';
				}else{
					$class='odd_row';
				}
				
				// copy resize and move image to dompdf images dir
				if($f['photo']!=''){
					$destiny='dompdf/www/images/'.str_replace('defects_photo/','',$f['photo']);
					copy($f['photo'], $destiny);
					$image = new SimpleImage();
					$image->load($destiny);
					$image->resize(55,45);
					$image->save($destiny);
				}
				
				if($f['photo']!=''){
					$image = "<a href='".IMG_SRC.$f['photo']."' target='_blank'>
					<img src='dompdf/www/images/".str_replace('defects_photo/','',$f['photo'])."' />
					</a>";
				}else{
					$image = "<img src='dompdf/www/images/noimage.png' />";
				}
				
				$tr.="<tr class='".$class."'>
					<td>".$f['defect_code']."</td>
					<td>".$f['area_room']."</td>
					<td>".$f['defect_desc']."</td>
					<td align='center'>".$image."</td>
					<td>".$f['resp_full_name']."</td>
					<td align='center'>".$f['status']."</td>
					<td align='center'>".$create_date."</td>
					<td align='center'>".$fixed_by_date."</td>
					<td align='center'>".$fixed_date."</td>				
					<td>".$f['defect_note']."</td>				
				</tr>";
				
			$c++;
			}
			
			// report name
			$pdf_dir = "report_pdf/".$_SESSION['ww_builder_id']."/";
			$report = glob($pdf_dir . "*.pdf");
			rsort($report,SORT_NUMERIC);
			$no_of_pdf=sizeof($report)+1;
			$report = $no_of_pdf.'.pdf';
			
			/*$pro_id = $pro_id.rand(0,999999);
			$report = 'report_'.$pro_id.'.pdf';*/
			
			$body="<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN' 'http://www.w3.org/TR/html4/loose.dtd'>
			<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en'>
			<head>
			<link rel='STYLESHEET' href='print_static.css' type='text/css' />
			</head>
			<body>
			<div id='body'>
				<div id='section_header'> </div>
				<div id='content'>
					<div class='page' style='font-size: 7pt;'>
						<table style='width: 100%;' class='header'>
							<tr>
								<td><h1 style='text-align: left;'>
									".SITE_NAME."</h1></td>
								<td align='right'><img src='dompdf/www/images/logo.gif'/></td>
							</tr>
						</table>
						<table style='width: 100%; border-top: 1px solid black; border-bottom: 1px solid black; font-size: 8pt;'>
							<tr>
								<td><strong>Project : </strong>".$pro_name."</td>
								<td><strong>Type: </strong>".$src_type."</td>
							</tr>
							<tr>
								<td><strong>Issued To : </strong>".$issued_to."</td>
								<td><strong>Status: </strong>".$src_status."</td>
							</tr>
							<tr>
								<td colspan='10'><h2>Date :".date("d/m/Y")."</h2></td>
							</tr>
						</table>
						<table class='change_order_items'>							
							<tbody>
								<tr>
									<th>ID</th>
									<th>Sub Location</th>
									<th>Description</th>
									<th>Picture</th>
									<th>Trade Name</th>
									<th>Status</th>															
									<th>Date Created</th>
									<th>Fix By</th>
									<th>Date Closed</th>									
									<th>Note & Sign Off</th>
								</tr>
								".$tr."
								<tbody>
									<tr>
										<td colspan='10'>&nbsp;</td>
									</tr>
								</tbody>
							</tbody>
						</table>
					</div>
				</div>
			</div>";
			
			createPDF($body, $report);	
			
			$result = $no_of_pdf;
			
			sleep(1);
			
		}elseif($report_type=='CSV'){			
			
			$q = "SELECT * FROM ".DEFECTS." d 
				  LEFT JOIN ".RESPONSIBLES." r ON d.resp_id = r.resp_id 
				  LEFT JOIN ".OWNERS." o ON o.id = d.owner_id 
				  LEFT JOIN ".PROJECTS." p ON p.project_id = d.project_id 
				  LEFT JOIN ".BUILDERS." b ON b.user_id = p.user_id 
				  LEFT JOIN ".ASSIGN." a ON a.assign_id = d.assign_id 
				  WHERE ".$w." ORDER BY o.owner_full_name";
		  
			$r=$obj->db_query($q);
			$csv_output='';
			$csv_hdr='';
			
			if($obj->db_num_rows($r)>0){
			
				$csv_hdr="ID,Location,Sub Location,Description,Trade Name,Date Added,Required By,Date Closed,Status,Opened By,Closed By\n";
			
				while ($row=$obj->db_fetch_assoc($r)) {
					// change create date format
					if($row['create_date']!='0000-00-00'){
						$create_date = $row['create_date'];
						$create_date = date("d/m/Y", strtotime($create_date));
					}else{
						$create_date = '';
					}
					// change fixed date format
					if($row['fixed_date']!='0000-00-00'){
						$fixed_date = $row['fixed_date'];
					}else{
						$fixed_date = '';
					}
					// change fixed by date format
					if($row['fixed_by_date']!='0000-00-00'){
						$fixed_by_date = $row['fixed_by_date'];
					}else{
						$fixed_by_date = '';
					}
					
					if($f['photo']!=''){
						$image = "<img src='".$f['photo']."'/>";
					}else{
						$image = "<img src='defects_photo/noimage.png' />";
					}
				?> 
				<tr>
					<td align="left">
					<? $csv_output .= $row['defect_code'] . ", ";?>
					</td>
					<td align="left">
					<? $csv_output .= $row['project_name'] . ", ";?>
					</td>
					<td align="left">
					<? $csv_output .= str_replace(",","-",$row['area_room']) . ", ";?>
					</td>
					<td align="left">
					<? $csv_output .= $row['defect_desc'] . ", ";?>
					</td>			
					<td align="left">
					<? $csv_output .= $row['resp_full_name'] . ", ";?>
					</td>
					<td align="left">
					<? $csv_output .= $create_date . ", ";?>
					</td>
					 <td align="left">
					<? $csv_output .= $fixed_by_date . ", ";?>
					</td>
					<td align="left">
					<? $csv_output .= $fixed_date . ", ";?>
					</td>
					<td align="left">
					<? $csv_output .= $row['status'] . ", ";?>
					</td>
					<td align="left">
					<? $csv_output .= $row['owner_full_name'] . ", ";?>
					</td>			
					<td align="left">
					<? $csv_output .= $row['builder_full_name'] . "\n"; //ensure the last column entry starts a new line ?>
					</td>
				</tr>
				<?php
				}
		
				$csv_dir='report_csv/'.$_SESSION['ww_builder_id'].'/';
			
				if(!is_dir($csv_dir))
					mkdir($csv_dir);
			
				$report = glob($csv_dir . "*.csv");			
				rsort($report,SORT_NUMERIC);
				$no_of_csv=sizeof($report)+1;
			
				$filename = $no_of_csv.'.csv';			
				$csv_output = $csv_hdr.$csv_output;
			
				$fp = fopen($csv_dir.$filename, "w");
				fwrite($fp, $csv_output);
				fclose($fp);
			
				$fileType = 'application/csv';
				$fileData ="<?php
				header('Content-disposition: attachment; filename=".$filename."');
				header('Content-type: ".$fileType."');
				readfile('".$filename."');
				?>";		
				$phpFile = $csv_dir.$no_of_csv.'.php';
				$fh = fopen($phpFile, 'w') or die("can't open file");
				fwrite($fh, $fileData);
				fclose($fh);
		
				$result=$no_of_csv;
				
			}else{
				$result=0;
			}
			
			sleep(1);
		}
	}
}

/* add responsible by builder */
elseif($sect=='add_responsible'){
	$builder_id = $_SESSION['ww_builder_id'];
	$button=mysql_real_escape_string($_POST['button']);
	$proId=mysql_real_escape_string($_POST['proId']);
	$resp_comp_name=mysql_real_escape_string(trim($_POST['resp_comp_name']));
	$resp_full_name=mysql_real_escape_string(trim($_POST['resp_full_name']));
	$resp_user_name=mysql_real_escape_string(trim($_POST['userName']));
	$password=mysql_real_escape_string(trim($_POST['password']));
	$resp_phone=mysql_real_escape_string(trim($_POST['resp_phone']));
	$resp_email=mysql_real_escape_string(trim($_POST['resp_email']));
	
	if($resp_comp_name=='' || $resp_full_name=='' || $resp_user_name=='' || $password=='' || $resp_phone=='' || $resp_email==''){
		$result = 0;
	}elseif(!is_numeric($resp_phone)){
		$result = 1;	
	}elseif($obj->isValidEmail($resp_email)==false){
		$result = 2;
	}elseif(strlen($password)<9){
		$result = 3;
	}else{
		// check user name
		$q = "SELECT * FROM ".RESPONSIBLES." WHERE resp_user_name = '$resp_user_name'";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$result = 4;
		}else{
			// email already exist
			$q = "SELECT * FROM ".RESPONSIBLES." WHERE project_id = '$proId' AND builder_id = '$builder_id' AND resp_email = '$resp_email' ";
			if($obj->db_num_rows($obj->db_query($q)) > 0){
				$result = 5;
			}else{
				$verify_code = md5($obj->rendom(15));
				$plain=$password;
				$password=md5($plain);
					
				$q = "INSERT INTO ".RESPONSIBLES." (verify_code,project_id,builder_id,resp_full_name,resp_comp_name,resp_phone,resp_email, 
					  resp_user_name,password,plain_pswd) 
					  VALUES('$verify_code','$proId','$builder_id','$resp_full_name','$resp_comp_name','$resp_phone', 
					  '$resp_email','$resp_user_name','$password','$plain')";
				$obj->db_query($q);
					
				if($button=='save'){
					$result = 6;
				}elseif($button=='save_n_new'){
					$result = 7;
				}
				sleep(1);
			}
		}		
	}
}

/* edit responsible by builder */
elseif($sect=='edit_remove_responsible'){
	$builder_id = $_SESSION['ww_builder_id'];
	$resp_id=mysql_real_escape_string($_POST['resp_id']);
	
	if($resp_id==''){		
		$result=9;
	}else{	
		// update trades
		if(isset($_POST['edit'])){
			
			$project_id=mysql_real_escape_string($_POST['project_id']);
			$resp_comp_name=mysql_real_escape_string(trim($_POST['resp_comp_name']));
			$resp_full_name=mysql_real_escape_string(trim($_POST['resp_full_name']));
			$resp_user_name=mysql_real_escape_string(trim($_POST['userName']));
			$password=mysql_real_escape_string(trim($_POST['password']));
			$resp_phone=mysql_real_escape_string(trim($_POST['resp_phone']));
			$resp_email=mysql_real_escape_string(trim($_POST['resp_email']));
			
			if($resp_comp_name=='' || $resp_full_name=='' || $resp_user_name=='' || $password=='' || $resp_phone=='' || $resp_email==''){
				$result = 0;
			}elseif(!is_numeric($resp_phone)){
				$result = 1;	
			}elseif($obj->isValidEmail($resp_email)==false){
				$result = 2;
			}elseif(strlen($password)<9){
				$result = 3;
			}else{
				// check user name
				$q = "SELECT * FROM ".RESPONSIBLES." WHERE resp_user_name = '$resp_user_name' AND resp_id != '$resp_id'";
				if($obj->db_num_rows($obj->db_query($q)) > 0){
					$result = 4;
				}else{
					// if responsible email id already exist?
					$q = "SELECT * FROM ".RESPONSIBLES." WHERE project_id = '$project_id' AND builder_id = '$builder_id' 
						  AND resp_email = '$resp_email' AND resp_id != '$resp_id'";
					if($obj->db_num_rows($obj->db_query($q)) > 0){
						$result = 5;
					}else{
						$q = "UPDATE ".RESPONSIBLES." SET resp_full_name='$resp_full_name',resp_comp_name='$resp_comp_name',resp_phone='$resp_phone', 
							  resp_email='$resp_email' WHERE resp_id = '$resp_id'";
						$obj->db_query($q);
							  
						$result = 6;	  
					}
				}
			}			
		// remove trades
		}elseif(isset($_POST['remove'])){
			if($obj->db_num_rows($obj->db_query("SELECT df_id FROM ".DEFECTS." WHERE resp_id = '$resp_id'"))>0){
				$result=7;
				
			}else{
				$obj->db_query("DELETE FROM ".RESPONSIBLES." WHERE resp_id = '$resp_id'");
				$result=8;
			}
		}
	}
	sleep(1);
}

/* responsible sign-in */
elseif($sect=='responsible_sign_in'){
	$password=md5(mysql_real_escape_string(trim($_POST['password'])));
	$user_name=mysql_real_escape_string(trim($_POST['username']));
	
	if($user_name=='' || $password==''){
		//$result = 1;
		$_SESSION['error']="The fields is required.";
		header("location:pms.php?sect=responsible");
	}else{
		$q = "SELECT * FROM ".RESPONSIBLES." WHERE password = '$password' AND resp_user_name = '$user_name' AND active = '1' ";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$f = $obj->db_fetch_assoc($obj->db_query($q));
			$_SESSION['ww_is_builder'] = 2;
			$_SESSION['ww_resp_id'] = $f['resp_id'];
			$_SESSION['ww_resp_full_name'] = $f['resp_full_name'];
			$_SESSION['ww_resp_user_name'] = $f['resp_user_name'];
			$_SESSION['ww_plain_pswd'] = $f['plain_pswd'];
			$_SESSION['ww_resp_email'] = $f['resp_email'];
			$_SESSION['ww_resp_comp_name'] = $f['resp_comp_name'];			
			$_SESSION['ww_logged_in_as']= $f['resp_full_name'];
			$_SESSION['ww_resp'] = $f;
//			$result = 2; //			sleep(1);
			header("location:pms.php?sect=r_dashboard");
		}else{
			//$result = 0;
			$_SESSION['error']="Invalid credentials, please try again!";
			header("location:pms.php?sect=responsible");
		}
	}
}

/* create csv by responsable */
elseif($sect=='create_csv_responsible'){
	$resp_id=$_SESSION['ww_resp_id'];
	
	$pro_id=mysql_real_escape_string($_POST['project_name']);
	$assign_id=mysql_real_escape_string($_POST['assign_to_name']);
	$status=mysql_real_escape_string($_POST['status']);
	$defect_type=mysql_real_escape_string($_POST['defect_type']);
	$report_type=mysql_real_escape_string($_POST['report_type']);
	
	$w="p.id = '$pro_id' AND d.resp_id = '$resp_id'";
	
	if($assign_id!=''){
		$w.=" AND d.assign_id = '$assign_id'";
	}
	if($status!=''){
		$w.=" AND d.status = '$status'";
	}
	if($defect_type!=''){
		$w.=" AND d.defect_type_id = '$defect_type'";
	}
	
	$q = "SELECT * FROM ".DEFECTS." d 
		  LEFT JOIN ".RESPONSIBLES." r ON d.resp_id = r.resp_id 
		  LEFT JOIN ".PROJECTS." p ON p.project_id = d.project_id 
		  LEFT JOIN ".ASSIGN." a ON a.assign_id = d.assign_id 
		  WHERE ".$w." ORDER BY a.assign_full_name";
	  
		$r=$obj->db_query($q);
		$csv_output='';
		$csv_hdr='';
if($obj->db_num_rows($r)>0){
			$csv_hdr="Issued To Contact Name,Issued To Company,Issued To Phone,Issued To Email,Assign To Contact Name,Assign To Company,Assign To Phone,Assign To Email\n";

		while ($row=$obj->db_fetch_assoc($r)) {
		?> 
        <tr>
			<td align="left">
            <? $csv_output .= $row['resp_comp_name'] . ", ";?>
            </td>
			<td align="left">
            <? $csv_output .= $row['resp_full_name'] . ", ";?>
            </td>
			<td align="left">
            <? $csv_output .= $row['resp_phone'] . ", ";?>
            </td>
			<td align="left">
            <? $csv_output .= $row['resp_email'] . ", ";?>
            </td>
			<td align="left">
            <? $csv_output .= $row['assign_comp_name'] . ", ";?>
            </td>
			<td align="left">
            <? $csv_output .= $row['assign_full_name'] . ", ";?>
            </td>
			<td align="left">
            <? $csv_output .= $row['assign_phone'] . ", ";?>
            </td>
			<td align="left">
            <? $csv_output .= $row['assign_email'] . "\n ";?>
            </td>			
        </tr>
	<?php
	}
			$csv = rand(1,999999);			
			$filename = 'report_'.$csv.'.csv';			
			$csv_output = $csv_hdr.$csv_output;
			
			$fp = fopen('report_csv_responsible/'.$filename, "w");
			fwrite($fp, $csv_output);
			fclose($fp);
				
			$fileType = 'application/csv';
			$fileData ="<?php
			header('Content-disposition: attachment; filename=".$filename."');
			header('Content-type: ".$fileType."');
			readfile('".$filename."');
			?>";		
			$phpFile = 'report_csv_responsible/report_'.$csv.'.php';
			$fh = fopen($phpFile, 'w') or die("can't open file");
			fwrite($fh, $fileData);
			fclose($fh);
			
			$result=$csv;
	}else{
		$result=0;
	}
			
	sleep(1);
}

/* edit defect by responsible */
elseif($sect=='edit_defect_responsible'){
	$resp_id = $_SESSION['ww_resp_id'];
	$df_id = mysql_real_escape_string(trim($_POST['df_id']));
	$defect_note=mysql_real_escape_string(trim($_POST['defect_note']));
	$assign_to_id = mysql_real_escape_string($_POST['assign_to_id']);
	$defect_type = mysql_real_escape_string(trim($_POST['defect_type']));
	$status = mysql_real_escape_string($_POST['status']);
	
	$fixed_by_date = $_POST['fixed_by_date'];
	// change date format for fixed_by_date
	$fixed_by_date = date("d/m/Y", strtotime($fixed_by_date));
	
	$fixed_date = $_POST['fixed_date'];
	if($fixed_date!=''){
	// change date format for fixed_date
	$fixed_date = date("d/m/Y", strtotime($fixed_date));
	}
	
	if($fixed_by_date=='' || $assign_to_id=='Select'){
		$result = 0;
	}else{
		$q = "UPDATE ".DEFECTS." SET assign_id='$assign_to_id',defect_note='$defect_note',fixed_by_date='$fixed_by_date',
			status='$status',fixed_date='$fixed_date',defect_type_id='$defect_type' WHERE df_id='$df_id'";
			
		$obj->db_query($q);
		
		$result = 1;
		sleep(1);
	}
}

/* add assign-to by responsible*/
elseif($sect=='add_assign_to'){
	$resp_id = $_SESSION['ww_resp_id'];
	$button=mysql_real_escape_string($_POST['button']);
	$assign_to_comp_name=mysql_real_escape_string(trim($_POST['assign_to_comp_name']));
	$assign_to_full_name=mysql_real_escape_string(trim($_POST['assign_to_full_name']));
	$assign_to_phone=mysql_real_escape_string(trim($_POST['assign_to_phone']));
	$assign_to_email=mysql_real_escape_string(trim($_POST['assign_to_email']));
	
	if($assign_to_comp_name=='' || $assign_to_full_name=='' || $assign_to_phone=='' || $assign_to_email==''){
		$result = 0;
	}elseif(!is_numeric($assign_to_phone)){
		$result = 1;	
	}elseif($obj->isValidEmail($assign_to_email)==false){
		$result = 2;
	}else{
		// if assign_to already exist?
		$q = "SELECT * FROM ".ASSIGN." WHERE resp_id = '$resp_id' AND assign_email = '$assign_to_email' ";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$result = 3;
		}else{
			$verify_code = md5($obj->rendom(15));
			$assign_to_user_name=$obj->rendom(4).rand(99,900).$obj->rendom(4);
			$plain=$obj->rendom(8);
			$password=md5($plain);
				
			$resp_email = $_SESSION['ww_resp_email'];
			$resp_full_name = $_SESSION['ww_resp_full_name'];
		
			$detail = array();
			$detail['to']=$assign_to_email;
			$detail['name']=$resp_full_name;
			$detail['from']=$resp_email;
			$detail['subject']="Account activation for ".SITE_NAME;
			$detail['msg']="<table width='80%'>
						<tr><td>Congratulations on creating your account on ".SITE_NAME."</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>Please keep this email for your records, as it contains an
								important verification code that you may need should you ever
								encounter problems or forget your password.</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>Full Name: </td>
							<td>".$assign_to_full_name."</td>
						</tr>
						<tr>
							<td>Company Name: </td>
							<td>".$assign_to_comp_name."</td>
						</tr>
						<tr>
							<td>Phone: </td>
							<td>".$assign_to_phone."</td>
						</tr>
						<tr>
							<td>Email: </td>
							<td>".$assign_to_email."</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>Do not share this information.</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>Verification code : ".$verify_code."</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>Username : ".$assign_to_user_name."</td></tr>
						<tr><td>Password : ".$plain."</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>You can login to your account at ".HOME_SCREEN."</td></tr>
					</table>";
			$obj->send_mail($detail);
			
			$q = "INSERT INTO ".ASSIGN." (verify_code,resp_id,assign_full_name,assign_comp_name,assign_phone,assign_email, 
				  assign_user_name,password,plain_pswd) 
				  VALUES('$verify_code','$resp_id','$assign_to_full_name','$assign_to_comp_name','$assign_to_phone', 
				  '$assign_to_email','$assign_to_user_name','$password','$plain')";
			$obj->db_query($q);
				
			if($button=='save'){
				$result = 4;
			}elseif($button=='save_n_new'){
				$result = 5;
			}
			sleep(1);
		}
	}
}

/* edit assign-to by responsible */
elseif($sect=='edit_assign_to'){
	$resp_id = $_SESSION['ww_resp_id'];
	$assign_id=$_POST['assign_id'];
	$assign_to_comp_name=mysql_real_escape_string(trim($_POST['assign_to_comp_name']));
	$assign_to_full_name=mysql_real_escape_string(trim($_POST['assign_to_full_name']));
	$assign_to_phone=mysql_real_escape_string(trim($_POST['assign_to_phone']));
	$assign_to_email=mysql_real_escape_string(trim($_POST['assign_to_email']));
	
	if($assign_to_comp_name=='' || $assign_to_full_name=='' || $assign_to_phone=='' || $assign_to_email==''){
		$result = 0;
	}elseif(!is_numeric($assign_to_phone)){
		$result = 1;	
	}elseif($obj->isValidEmail($assign_to_email)==false){
		$result = 2;
	}else{
		// if assign_to email id already exist?
		$q = "SELECT * FROM ".ASSIGN." WHERE resp_id = '$resp_id' AND assign_email = '$assign_to_email' AND assign_id != '$assign_id'";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$result = 3;
		}else{
			$of = $obj->db_fetch_assoc($obj->db_query($q));
		
			$q = "UPDATE ".ASSIGN." SET assign_full_name='$assign_to_full_name', assign_comp_name='$assign_to_comp_name',
				  assign_phone='$assign_to_phone', assign_email='$assign_to_email' WHERE assign_id = '$assign_id' ";
			$obj->db_query($q);
			
			// send email to assign_to(repairer)
			/*$resp_email = $_SESSION['ww_resp_email'];
			$resp_full_name = $_SESSION['ww_resp_full_name'];
		
			$detail = array();
			$detail['to']=$assign_to_email;
			$detail['name']=$resp_full_name;
			$detail['from']=$resp_email;
			$detail['subject']="Account activation for ".SITE_NAME;
			$detail['msg']="<table width='80%'>
						<tr><td>Congratulations on creating your account on ".SITE_NAME."</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>Please keep this email for your records, as it contains an
								important verification code that you may need should you ever
								encounter problems or forget your password.</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>Full Name: </td>
							<td>".$assign_to_full_name."</td>
						</tr>
						<tr>
							<td>Company Name: </td>
							<td>".$assign_to_comp_name."</td>
						</tr>
						<tr>
							<td>Phone: </td>
							<td>".$assign_to_phone."</td>
						</tr>
						<tr>
							<td>Email: </td>
							<td>".$assign_to_email."</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>Do not share this information.</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>Verification code : ".$of['verify_code']."</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>Username : ".$of['assign_user_name']."</td></tr>
						<tr><td>Password : ".$of['plain_pswd']."</td></tr>
						<tr><td>&nbsp;</td></tr>
						<tr><td>You can login to your account at ".HOME_SCREEN."</td></tr>
					</table>";
			$obj->send_mail($detail);*/
			$result = 4;
			sleep(1);
		}		
	}
}

/* Forgot Password */
elseif($sect=='forgot_password'){
	$email=trim($_POST['email']);
	$type=mysql_real_escape_string(trim($_POST['type']));
	
	if($email==''){
		$result=0;
	}elseif($obj->isValidEmail($email)==false){
		$result=1;
	}else{
		$email=mysql_real_escape_string($email);
		
		// check if registered member?
		$tbl=BUILDERS;
		$w="builder_email='$email'";
		if($type=='2'){
			$tbl=OWNERS;
			$w="email='$email'";
		}if($type=='3'){
			$tbl=RESPONSIBLES;
			$w="resp_email='$email'";
		}
		
		$q="SELECT * FROM $tbl WHERE $w ";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			
			$f=$obj->db_fetch_assoc($obj->db_query($q));
			
			if($type=='1'){
				$user_name=$f['builder_user_name'];
				$pswd=$f['builder_plain_pswd'];
			}elseif($type=='2'){
				$user_name=$f['user_name'];
				$pswd=$f['plain_pswd'];
			}elseif($type=='3'){
				$user_name=$f['resp_user_name'];
				$pswd=$f['plain_pswd'];
			}
			
			// send email to admin
			$detail = array();
			$detail['to']=$email;
			$detail['name']=SITE_NAME;
			$detail['from']=EMAIL;
			$detail['subject']="Account Login Information";
			$detail['msg']="<table width='25%'>
							<tr>
								<td nowrap='nowrap' colspan='2'><u><b>Login Information</b></u></td>
							</tr>
							<tr>
								<td nowrap='nowrap'>Username: </td>
								<td>$user_name</td>
							</tr>
							<tr>
								<td nowrap='nowrap'>Password: </td>
								<td>$pswd</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td nowrap='nowrap' colspan='2'>You can login to your account at ".HOME_SCREEN."</td></tr>
							<tr><td>&nbsp;</td></tr>
							<tr><td nowrap='nowrap' colspan='2'><u><b>Regards From</b></u></td></tr>
							<tr><td nowrap='nowrap' colspan='2'>".SITE_NAME."</td></tr>
						</table>";
			$obj->send_mail($detail);
			
			$result=2;
			sleep(1);
		}else{
			$result=3;
		}		
	}
}

?>
<script language="javascript" type="text/javascript">window.top.window.stopAjax(<?php echo $result; ?>);</script>