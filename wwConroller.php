<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();

if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }

require_once'includes/commanfunction.php';
$object = new COMMAN_Class();

$sect = $_REQUEST['sect'];

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
	$_SESSION['post_array']=$_POST;
		$fname=mysql_real_escape_string(trim($_POST['fname']));
		$compname=mysql_real_escape_string(trim($_POST['compname']));
		$email=mysql_real_escape_string(trim($_POST['email']));
		$mobile=mysql_real_escape_string(trim($_POST['mobile']));
		$bus_l1=mysql_real_escape_string(trim($_POST['bus_line1']));
		$bus_l2=mysql_real_escape_string(trim($_POST['bus_line2']));
		$bus_suburb=mysql_real_escape_string(trim($_POST['bus_suburb']));
		$bus_state=mysql_real_escape_string(trim($_POST['bus_state']));
		$bus_post=mysql_real_escape_string(trim($_POST['bus_post']));
		$bus_country=mysql_real_escape_string(trim($_POST['bus_country']));
		$bil_l1=mysql_real_escape_string(trim($_POST['bil_line1']));
		$bil_l2=mysql_real_escape_string(trim($_POST['bil_line2']));
		$bil_suburb=mysql_real_escape_string(trim($_POST['bil_suburb']));
		$bil_state=mysql_real_escape_string(trim($_POST['bil_state']));
		$bil_post=mysql_real_escape_string(trim($_POST['bil_post']));
		$bil_country=mysql_real_escape_string(trim($_POST['bil_country']));
	if($obj->isValidEmail($email)==false){
		$_SESSION['error']['email']='Invalid email address!';
		header("location:create_account.php");
	}elseif(!is_numeric($mobile)){
		$_SESSION['error']['mobile']='Invalid mobile no.!';
		header("location:create_account.php");
	}elseif(!is_numeric($bus_post)){
		$_SESSION['error']['bus_post']='Invalid post code!';
		header("location:create_account.php");
	}elseif(!is_numeric($bil_post)){
		$_SESSION['error']['bil_post']='Invalid post code!';
		header("location:create_account.php");
	}else{
		$q = "SELECT * FROM ".COMPANIES." WHERE comp_email = '$email'";
		
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			
			$_SESSION['error']['email']='Email id already exist!';
			header("location:create_account.php");
		}else{
			// insert into db.
			$date=Date('Y-m-d H:i:s');
			$verifyCode = md5($obj->rendom(15));
			$q = "INSERT INTO ".COMPANIES." (verify_code,comp_fullname,comp_name,comp_email,comp_mobile,comp_businessadd1,comp_billadd1,comp_businessadd2,comp_billadd2,				  comp_bussuburb,comp_billsuburb,comp_businessstate,comp_billstate,comp_businesspost,comp_billpost,comp_businesscountry,comp_billcountry,comp_created) VALUES('$verifyCode','$fname','$compname','$email','$mobile',				  '$bus_l1','$bil_l1','$bus_l2','$bil_l2','$bus_suburb','$bil_suburb','$bus_state','$bil_state','$bus_post','$bil_post','$bus_country','$bil_country','$date')";
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
			$obj->send_mail($detail); unset($_SESSION['post_array']);
			$_SESSION['success']='<p><span class="msg">Request send successfully!</span><br/><br/></p>';
			header("location:success.php");
		}
	}
}

/* apply now for builder account 
elseif($sect=='b_apply_now'){
	$fname=mysql_real_escape_string(trim($_POST['fname']));
	$compname=mysql_real_escape_string(trim($_POST['compname']));
	$email=mysql_real_escape_string(trim($_POST['email']));
	$mobile=mysql_real_escape_string(trim($_POST['mobile']));
	$username=mysql_real_escape_string(trim($_POST['username']));
	$password=mysql_real_escape_string(trim($_POST['password']));	
	
	if($fname=='' || $compname=='' || $email=='' || $mobile=='' || $username=='' || $password==''){
		//$_SESSION['error']="* represent required fileds!"; //$result = 1;
	}elseif($obj->isValidEmail($email)==false){
		$_SESSION['error']['email']="Invalid email address!"; //=$result = 2;
	}elseif(!is_numeric($mobile)){
		$_SESSION['error']['mobile']="Invalid mobile no.!"; //=$result = 3;
	}elseif(strlen($password)<6){
		$_SESSION['error']['password']="Password must be greater than 6 characters!"; //=$result = 4;
	}else{
		// check username
		$q = "SELECT * FROM ".BUILDERS." WHERE user_name = '$username'";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$_SESSION['error']['username']="Username already exist!"; //=$result = 5;
		}else{
			// check email id
			$q = "SELECT * FROM ".BUILDERS." WHERE user_email = '$email'";
			if($obj->db_num_rows($obj->db_query($q)) > 0){
				$_SESSION['error']['email']="Email id already exist!"; //=$result = 6;
			}else{
				// insert into db.
				$verifyCode = md5($obj->rendom(15));
				$pswdmd5=md5($password);
				$date=Date('Y-m-d H:i:s');
				$q = "INSERT INTO ".BUILDERS." (fk_c_id,user_verifycode,active,user_name,user_password,user_plainpassword,user_fullname,company_name,user_email,user_phone_no,created_date, user_type) VALUES('".$_SESSION['ww_c_id']."','$verifyCode','1','$username','$pswdmd5','$password','$fname','$compname','$email','$mobile','$date','manager')";
					 // echo $q; die;
				$obj->db_query($q);
//New Permission Dated : 29/05/2012
				$newUserId = mysql_insert_id();

				$keyManagerPermissionArray = array_keys($managerPermissionArray);
				for($i=0;$i<sizeof($managerPermissionArray);$i++){
	 				$permissionQry = "INSERT INTO user_permission SET user_id = '".$newUserId."', permission_name = '".$keyManagerPermissionArray[$i]."', is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."', created_by = '".$_SESSION['ww_c_id']."', created_date = NOW()";
					mysql_query($permissionQry);
				}
//New Permission Dated : 29/05/2012
				header("location:pms.php?sect=c_builder");	
				unset($_SESSION['post_array']);
				exit;
				$_SESSION['error']="";//"Manager created successfully!"; //=$result = 7;
			}			
		}
	}$_SESSION['post_array']=$_POST;
header("location:pms.php?sect=c_add_builder");	
	sleep(1);
}*/
elseif($sect=='b_apply_now'){
	$companyId = implode(', ', $_POST['compname']);
	$getCompanyName = $object->selQRYMultiple("group_concat(company_name SEPARATOR ', ') AS compname", 'organisations', 'is_deleted = 0 AND id IN('.$companyId.') GROUP BY is_deleted ');

	$userType=mysql_real_escape_string(trim($_POST['userType']));
	$fname=mysql_real_escape_string(trim($_POST['fname']));
	$compname=mysql_real_escape_string(trim($getCompanyName[0]['compname']));
	$companyId=mysql_real_escape_string(trim($companyId));
	$email=mysql_real_escape_string(trim($_POST['email']));
	$mobile=mysql_real_escape_string(trim($_POST['mobile']));
	$username=mysql_real_escape_string(trim($_POST['username']));
	$password=mysql_real_escape_string(trim($_POST['password']));	
	$rePassword=mysql_real_escape_string(trim($_POST['rePassword']));
	$emailReceive = 0;
	if($userType != 'inspector'){
		$addProj = isset($_POST['addProject']) ? 1 : 0;
		$emailReceive = isset($_POST['emailReceive']) ? 1 : 0;
	}
	$messageBoard = isset($_POST['messageBoard']) ? 1 : 0;
	$menuProgressMonitoring = isset($_POST['menuProgressMonitoring']) ? 1 : 0;
	$menuQualityChecklist = isset($_POST['menuQualityChecklist']) ? 1 : 0;

	if($fname=='' || $compname=='' || $email=='' || $mobile=='' || $username=='' || $password=='' || $rePassword==''){
		//$_SESSION['error']="* represent required fileds!"; //$result = 1;
	}elseif($obj->isValidEmail($email)==false){
		$_SESSION['error']['email']="Invalid email address!"; //=$result = 2;
	}elseif(!is_numeric($mobile)){
		$_SESSION['error']['mobile']="Invalid mobile no.!"; //=$result = 3;
	}elseif(strlen($password)<6){
		$_SESSION['error']['password']="Password must be greater than 6 characters!"; //=$result = 4;
	}elseif($password != $rePassword){
		$_SESSION['error']['rePassword']="The re password field is equal to password!"; //=$result = 4;
	}else{
		// check username
		$q = "SELECT * FROM ".BUILDERS." WHERE user_name = '$username' and is_deleted = 0";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$_SESSION['error']['username']="Username already exist!"; //=$result = 5;
		}else{
			// check email id
			$q = "SELECT * FROM ".BUILDERS." WHERE user_email = '$email' and is_deleted = 0";
			if($obj->db_num_rows($obj->db_query($q)) > 0){
				$_SESSION['error']['email']="Email id already exist!"; //=$result = 6;
			}else{
				// insert into db.
				$verifyCode = md5($obj->rendom(15));
				$pswdmd5 = md5($password);
				$date = date('Y-m-d H:i:s');
				$q = "INSERT INTO user SET
								fk_c_id = '".$_SESSION['ww_c_id']."',
								user_verifycode = '".$verifyCode."',
								active = 1,
								user_name = '".$username."',
								user_password = '".$pswdmd5."',
								user_plainpassword = '".$password."',
								user_fullname = '".$fname."',
								company_id = '".$companyId."',
								company_name = '".$compname."',
								user_email = '".$email."',
								user_phone_no = '".$mobile."',
								created_date = NOW(),
								last_modified_date = NOW(),
								user_type = '".$userType."',
								recieve_email = '".$emailReceive."'";
					 // echo $q; die;
				$obj->db_query($q);
//New Permission Dated : 29/05/2012
				$newUserId = mysql_insert_id();
if($userType == 'manager'){
	$keyManagerPermissionArray = array_keys($managerPermissionArray);

	$projectWisePermissions = array('web_edit_inspection','web_delete_inspection','web_close_inspection','iPad_add_inspection','iPad_edit_inspection','iPad_delete_inspection','iPad_close_inspection','iPhone_add_inspection','iPhone_close_inspection', 'web_add_project');

	for($i=0;$i<sizeof($managerPermissionArray);$i++){
		if(in_array($keyManagerPermissionArray[$i], $projectWisePermissions)){
		}else{
			$permissionQry = "INSERT INTO user_permission SET
									user_id = '".$newUserId."',
									permission_name = '".$keyManagerPermissionArray[$i]."',
									is_allow = '".$managerPermissionArray[$keyManagerPermissionArray[$i]]."',
									created_by = '".$_SESSION['ww_c_id']."',
									created_date = NOW(),
									last_modified_by = '".$_SESSION['ww_c_id']."',
									last_modified_date = NOW()";
			mysql_query($permissionQry);
		}
	}
	$permissionQry_porj = "INSERT INTO user_permission SET
								user_id = '".$newUserId."',
								permission_name = 'web_add_project',
								is_allow = '".$addProj."',
								created_by = '".$_SESSION['ww_c_id']."',
								created_date = NOW(),
								last_modified_by = '".$_SESSION['ww_c_id']."',
								last_modified_date = NOW()";
	mysql_query($permissionQry_porj);

	if($messageBoard){
		$permissionQry1 = "UPDATE user_permission SET is_allow = '".$messageBoard."', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_c_id']." WHERE user_id = '".$newUserId."' AND permission_name = 'web_message_board'";
		mysql_query($permissionQry1);
	}
	if($menuProgressMonitoring){
		$permissionQry2 = "UPDATE user_permission SET is_allow = '".$menuProgressMonitoring."', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_c_id']." WHERE user_id = '".$newUserId."' AND permission_name = 'web_menu_progress_monitoring'";
		mysql_query($permissionQry2);	
	}
	if($menuQualityChecklist){
		$permissionQry3 = "UPDATE user_permission SET is_allow = '".$menuQualityChecklist."', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_c_id']." WHERE user_id = '".$newUserId."' AND permission_name = 'web_menu_quality_checklist'";
		mysql_query($permissionQry3);	
	}
}elseif($userType == 'inspector'){
	$keyManagerPermissionArray = array_keys($inspectorPermissionArray);

	$projectWisePermissions = array('web_edit_inspection','web_delete_inspection','web_close_inspection','iPad_add_inspection','iPad_edit_inspection','iPad_delete_inspection','iPad_close_inspection','iPhone_add_inspection','iPhone_close_inspection');

	for($i=0;$i<sizeof($inspectorPermissionArray);$i++){
		if(in_array($keyManagerPermissionArray[$i], $projectWisePermissions)){
		}else{
			$permissionQry = "INSERT INTO user_permission SET
									user_id = '".$newUserId."',
									permission_name = '".$keyManagerPermissionArray[$i]."',
									is_allow = '".$inspectorPermissionArray[$keyManagerPermissionArray[$i]]."',
									created_by = '".$_SESSION['ww_c_id']."',
									created_date = NOW(),
									last_modified_by = '".$_SESSION['ww_c_id']."',
									last_modified_date = NOW()";
			mysql_query($permissionQry);
		}
	}
}
//New Permission Dated : 29/05/2012
				header("location:pms.php?sect=c_builder");	
				unset($_SESSION['post_array']);
				exit;
				$_SESSION['error']="";//"Manager created successfully!"; //=$result = 7;
			}			
		}
	}$_SESSION['post_array']=$_POST;
	header("location:pms.php?sect=c_add_builder");	
	sleep(1);
}
/* company sign-in */ 
elseif($sect=='company_sign_in'){
	
	$password=md5(mysql_real_escape_string(trim($_POST['password'])));
	$user_name=mysql_real_escape_string(trim($_POST['username']));

	if(empty($_POST['username']) || empty($_POST['password'])){
		if(empty($_POST['username'])){ $_SESSION['error']['username']="The username field is required"; }
		if(empty($_POST['password'])){ $_SESSION['error']['password']="The password field is required"; $_SESSION['post_username']=$_POST['username']; }
		header("location:pms.php?sect=company");
	}else{
	 	$q = "SELECT * FROM ".COMPANIES." WHERE comp_password = '$password' AND comp_userName = '$user_name' AND active = '1' ";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$f = $obj->db_fetch_assoc($obj->db_query($q));
			$_SESSION['ww_is_company'] = 1; 
			$_SESSION['ww_c_id'] = $f['c_id'];
			$_SESSION['ww_c_full_name'] = $f['comp_fullname'];
			$_SESSION['ww_c_comp_name'] = $f['comp_name'];
			$_SESSION['ww_c_user_name'] = $f['comp_userName'];
			$_SESSION['ww_c_plain_pswd'] = $f['comp_plainpassword'];
			$_SESSION['ww_c_email'] = $f['comp_email'];
			$_SESSION['ww_logged_in_as']= $f['comp_fullname'];
			$_SESSION['ww_company'] = $f;
//New Sessions for Permissions
			$keyCompanyPermissionArray = array_keys($companyPermissionArray);
			for($i=0; $i<sizeof($companyPermissionArray); $i++){
				$_SESSION[$keyCompanyPermissionArray[$i]] = $companyPermissionArray[$keyCompanyPermissionArray[$i]]; 
			}
//New Sessions for Permissions

//			$result = 2;		sleep(1);
			if(isset($_SESSION['error'])){unset($_SESSION['error']);}
			header("location:pms.php?sect=c_full_analysis");
		}else{
			//$result = 0;
			$_SESSION['error']['message']="Invalid username or password, please try again!";
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
			$delete="DELETE FROM ".BUILDERS." WHERE user_id='$b_id'";
			//echo $delete; die;
			$obj->db_query($delete);
			$result=1;
			header("location:pms.php?sect=c_builder");	
			exit;
		}
	}
	sleep(1);
}

/* company edit profile */
elseif($sect=='c_dashboard_edit'){
	$_SESSION['post_array']=$_POST;
	$fname=mysql_real_escape_string(trim($_POST['fname']));
	$compname=mysql_real_escape_string(trim($_POST['compname']));
	$email=mysql_real_escape_string(trim($_POST['email']));
	$mobile=mysql_real_escape_string(trim($_POST['mobile']));
	$bus_l1=mysql_real_escape_string(trim($_POST['bus_line1']));
	$bus_l2=mysql_real_escape_string(trim($_POST['bus_line2']));
	$bus_suburb=mysql_real_escape_string(trim($_POST['bus_suburb']));
	$bus_state=mysql_real_escape_string(trim($_POST['bus_state']));
	$bus_post=mysql_real_escape_string(trim($_POST['bus_post']));
	$bus_country=mysql_real_escape_string(trim($_POST['bus_country']));
	$bil_l1=mysql_real_escape_string(trim($_POST['bil_line1']));
	$bil_l2=mysql_real_escape_string(trim($_POST['bil_line2']));
	$bil_suburb=mysql_real_escape_string(trim($_POST['bil_suburb']));
	$bil_state=mysql_real_escape_string(trim($_POST['bil_state']));
	$bil_post=mysql_real_escape_string(trim($_POST['bil_post']));
	$bil_country=mysql_real_escape_string(trim($_POST['bil_country']));
	$username=mysql_real_escape_string(trim($_POST['username']));
	/*if(isset($_FILES['c_logo']['name']) && !empty($_FILES['c_logo']['name']))
	{
		$filename=$_FILES['c_logo']['name']; // Csv File name
		$file_ext=explode('.',$filename);
		$ext=$file_ext[1];	
		if($ext=='jpg' || $ext=='JPG' || $ext=='JPEG'|| $ext=='jpeg'|| $ext=='png'|| $ext=='PNG' || $ext=='bmp'|| $ext=='BMP' ||  $ext=='gif' ||  $ext=='GIF')
		{
			$path=$_SERVER['DOCUMENT_ROOT'].'/company_logo/'; // Image Path
			//echo $path; die;
			$upload=move_uploaded_file($_FILES['c_logo']['tmp_name'],$path.$filename);
			$c_logo=$filename;
		}
		else
		{
				$_SESSION['error']['c_logo']='Invalid image format!';
				header("location:/pms.php?sect=c_dashboard_edit");
		}
	}
	else
	{
		
		$c_logo=$_POST['company_logo'];
		
	}*/
	
	if($fname=='' || $compname=='' || $email=='' || $mobile=='' || $bus_l1=='' || $bus_l2=='' || $bus_suburb=='' || 		$bus_state=='' || $bus_post=='' || $bus_country=='' || $bil_l1=='' || $bil_l2=='' || $bil_suburb=='' || $bil_state=='' ||  $bil_post=='' || $bil_country=='' || $username==''){
		
//		$result = 1;
	header("location:pms.php?sect=c_dashboard_edit");		
	}elseif($obj->isValidEmail($email)==false){
		$_SESSION['error']['email']='Invalid email address!';
		header("location:pms.php?sect=c_dashboard_edit");		
	}elseif(!is_numeric($mobile)){
		$_SESSION['error']['mobile']='Invalid mobile no.!';
		header("location:pms.php?sect=c_dashboard_edit");			
	}elseif(!is_numeric($bus_post)){
		$_SESSION['error']['bus_post']='Invalid post code!';
		header("location:pms.php?sect=c_dashboard_edit");			
	}elseif(!is_numeric($bil_post)){
		$_SESSION['error']['bil_post']='Invalid post code!';
		header("location:pms.php?sect=c_dashboard_edit");			
	}else{
		if($obj->db_num_rows($obj->db_query("SELECT * FROM ".COMPANIES." WHERE comp_userName='$username' AND c_id!='".$_SESSION['ww_c_id']."'")) > 0){
			$_SESSION['error']['username']="Please select different username!";//$result = 6;				
		}else{
			if(trim($_POST['password']!='')){
				$cplainpswd=mysql_real_escape_string(trim($_POST['password']));
				$password=md5($cplainpswd);
				$pswd=",password='$password',comp_plainpassword='$cplainpswd'";				
			}
/*			$q="UPDATE ".COMPANIES." SET comp_userName='$username',comp_fullname='$fname',comp_name='$compname',comp_email='$email',comp_mobile='$mobile',
				comp_businessadd1='$bus_l1',comp_billadd1='$bil_l1',comp_businessadd2='$bus_l2',comp_billadd2='$bil_l2',
				comp_bussuburb='$bus_suburb',comp_billsuburb='$bil_suburb',comp_businessstate='$bus_state',comp_billstate='$bil_state',
				comp_businesspost='$bus_post',comp_billpost='$bil_post',comp_businesscountry='$bus_country',comp_billcountry='$bil_country',company_logo='$c_logo' $pswd WHERE c_id='".$_SESSION['ww_c_id']."'";*/
				
				
				$q="UPDATE ".COMPANIES." SET comp_userName='$username',comp_fullname='$fname',comp_name='$compname',comp_email='$email',comp_mobile='$mobile',
				comp_businessadd1='$bus_l1',comp_billadd1='$bil_l1',comp_businessadd2='$bus_l2',comp_billadd2='$bil_l2',
				comp_bussuburb='$bus_suburb',comp_billsuburb='$bil_suburb',comp_businessstate='$bus_state',comp_billstate='$bil_state',
				comp_businesspost='$bus_post',comp_billpost='$bil_post',comp_businesscountry='$bus_country',comp_billcountry='$bil_country' $pswd WHERE c_id='".$_SESSION['ww_c_id']."'";
				
			$obj->db_query($q);
			$_SESSION['ww_logged_in_as']=$fname;
			$_SESSION['ww_c_full_name']=$fname;
			
			$_SESSION['success']='Profile Updated successfully.';
			header('location:pms.php?sect=c_dashboard');
		}
	}
}

/* builder sign-in */
/*elseif($sect=='builder_sign_in'){
	$password=md5(mysql_real_escape_string(trim($_POST['password'])));
	$user_name=mysql_real_escape_string(trim($_POST['username']));

	if(empty($_POST['username']) || empty($_POST['password'])){
		if(empty($_POST['username'])){ $_SESSION['error']['username']="The username field is required"; }
		if(empty($_POST['password'])){ $_SESSION['error']['password']="The password field is required"; $_SESSION['post_username']=$_POST['username']; }
		header("location:pms.php?sect=builder");
	}else{
		$q = "SELECT * FROM ".BUILDERS." WHERE user_password = '$password' AND user_name = '$user_name' AND active = '1' and is_deleted=0 and user_type = 'manager'";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$f = $obj->db_fetch_assoc($obj->db_query($q));
			
			// Remove extra cookes 
			$object->removeCookies($f['user_id']);
			
			$_SESSION['ww_is_builder'] = 1;
			$_SESSION['ww_builder_id'] = $f['user_id'];
			$_SESSION['ww_builder_full_name'] = $f['user_fullname'];
			$_SESSION['ww_comp_name'] = $f['comp_name'];
			$_SESSION['ww_builder_user_name'] = $f['user_name'];
			$_SESSION['ww_builder_plain_pswd'] = $f['user_plainpassword'];
			$_SESSION['ww_builder_email'] = $f['user_email'];
			$_SESSION['ww_logged_in_as']= $f['user_fullname'];
			$_SESSION['ww_builder'] = $f;
			$_SESSION['ww_builder_fk_c_id'] = $f['fk_c_id'];
			$_SESSION['ww_builder_roll'] = $f['user_roll'];
//			$result = 2; //			sleep(1);
			if(isset($_SESSION['error'])){unset($_SESSION['error']);}
//New Permision Setup
	$permisions = $object->selQRYMultiple('permission_name, is_allow', 'user_permission', 'user_id = "'.$f['user_id'].'"');
	if(!empty($permisions)){
		foreach($permisions as $permission){
			$_SESSION[$permission['permission_name']] = $permission['is_allow'];	
		}
	}
//New Permision Setup
			$qs = "SELECT user_role, issued_to, project_id FROM  user_projects WHERE is_deleted = 0  AND user_id = '".$_SESSION['ww_builder_id']."'";
			$rs=$obj->db_query($qs);
			$projUserRole = array();
			while($f=$obj->db_fetch_assoc($rs)){
				$projUserRole[$f["project_id"]] = $f["user_role"];
				$_SESSION['userRole'] = $f["user_role"];
				if ($f["user_role"] == "Sub Contractor"){
					$_SESSION['userIssueTo'] = $f["issued_to"];
					break;
				}
			}
			$_SESSION['projUserRole'] = $projUserRole;
//Check Cookies are set or not and set it to session variables
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_qc']))
				$_SESSION['qc'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_qc']);
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_ir']))
				$_SESSION['ir'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_ir']);
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_pmr']))
				$_SESSION['pmr'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_pmr']);
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_qar']))
				$_SESSION['qar'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_qar']);
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_clr']))
				$_SESSION['clr'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_clr']);
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_pm']))
				$_SESSION['pm'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_pm']);
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_qa']))
				$_SESSION['qa'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_qa']);
//Check Cookies are set or not and set it to session variables
			if($_SESSION['userRole'] != 'Sub Contractor'){
			header("location:pms.php?sect=b_full_analysis");
			}else{
				header("location:pms.php?sect=i_defect");				
			}
		}else{
			$_SESSION['error']['message']="Invalid username or password, please try again!";
			header("location:pms.php?sect=builder");
			//$result = 0;
		}
	}
}*/

/* builder sign-in */
elseif($sect=='builder_sign_in'){
	$isIpad = isset($_REQUEST['isIpad'])?$_REQUEST['isIpad']:0; 
	if($isIpad == 1){
		$password = $_POST['username']= mysql_real_escape_string(trim($_REQUEST['password']));
		$user_name = $_POST['password'] = mysql_real_escape_string(trim($_REQUEST['username']));
		$redirectTo = isset($_REQUEST['redirectTo'])?$_REQUEST['redirectTo']:''; 
		$_SESSION['idp'] = isset($_REQUEST['projId'])?$_REQUEST['projId']:0; 
		
	}else{
		$password = md5(mysql_real_escape_string(trim($_POST['password'])));
		$user_name = mysql_real_escape_string(trim($_POST['username']));
	}
	

	if(empty($_POST['username']) || empty($_POST['password'])){
		if(empty($_POST['username'])){ $_SESSION['error']['username']="The username field is required"; }
		if(empty($_POST['password'])){ $_SESSION['error']['password']="The password field is required"; $_SESSION['post_username']=$_POST['username']; }
		
		header("location:pms.php?sect=login");
	}else{
		$q = "SELECT * FROM pms_companies WHERE comp_password = '$password' AND comp_userName = '$user_name' AND active = '1' ";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$f = $obj->db_fetch_assoc($obj->db_query($q));
			#$year = time() + 31536000;
			
			
			//Remember password functionality code goes
			$rememberPass =  $_REQUEST['remember'];
			if($rememberPass == 1){
				$year = time() + 31536000;
				$cookieData = serialize(array("uname"=>$_REQUEST['username'], "pass"=>$_REQUEST['password']));
				setcookie('remHammarProbSWId', $cookieData, $year);
			}else{
				if(isset($_COOKIE['remHammarProbSWId'])) {
					$past = time() - 100;
					setcookie('remHammarProbSWId', 'gone', $past);
				}
			}

			$_SESSION['ww_is_company'] = 1; 
			$_SESSION['ww_c_id'] = $f['c_id'];
			$_SESSION['ww_c_full_name'] = $f['comp_fullname'];
			$_SESSION['ww_c_comp_name'] = $f['comp_name'];
			$_SESSION['ww_c_user_name'] = $f['comp_userName'];
			$_SESSION['ww_c_plain_pswd'] = $f['comp_plainpassword'];
			$_SESSION['ww_c_email'] = $f['comp_email'];
			$_SESSION['ww_logged_in_as']= $f['comp_fullname'];
			$_SESSION['ww_company'] = $f;
//New Sessions for Permissions
			$keyCompanyPermissionArray = array_keys($companyPermissionArray);
			for($i=0; $i<sizeof($companyPermissionArray); $i++){
				$_SESSION[$keyCompanyPermissionArray[$i]] = $companyPermissionArray[$keyCompanyPermissionArray[$i]]; 
			}
//New Sessions for Permissions

//			$result = 2;		sleep(1);
			if(isset($_SESSION['error'])){unset($_SESSION['error']);}
			//header("location:pms.php?sect=c_full_analysis");
			header("location:pms.php?sect=c_builder");
		}else{
			$q = "SELECT * FROM ".BUILDERS." WHERE user_password = '$password' AND user_name = '$user_name' AND active = '1' and is_deleted=0";
			if($obj->db_num_rows($obj->db_query($q)) > 0){
				$f = $obj->db_fetch_assoc($obj->db_query($q));

				//Remember password functionality code goes
				$rememberPass =  $_REQUEST['remember']; 
				if($rememberPass == 1){
					$year = time() + 31536000;
					$cookieData = serialize(array("uname"=>$_REQUEST['username'], "pass"=>$_REQUEST['password']));
					setcookie('remHammarProbSWId', $cookieData, $year);
				}else{
					if(isset($_COOKIE['remHammarProbSWId'])) {
						$past = time() - 100;
						setcookie('remHammarProbSWId', 'gone', $past);
					}
				}

				$_SESSION['ww_is_builder'] = 1;
				$_SESSION['ww_builder_id'] = $f['user_id'];
				$_SESSION['ww_builder_full_name'] = $f['user_fullname'];
				$_SESSION['ww_comp_name'] = $f['comp_name'];
				$_SESSION['ww_builder_user_name'] = $f['user_name'];
				$_SESSION['ww_builder_plain_pswd'] = $f['user_plainpassword'];
				$_SESSION['ww_builder_email'] = $f['user_email'];
				$_SESSION['ww_logged_in_as']= $f['user_fullname'];
				$_SESSION['ww_builder'] = $f;
				$_SESSION['ww_builder_fk_c_id'] = $f['fk_c_id'];
				$_SESSION['ww_builder_roll'] = $f['user_roll'];
	//			$result = 2; //			sleep(1);
							//Get colour code from database
				$_SESSION['colorData'] = '';
				if(isset($f['company_id']) && !empty($f['company_id'])){
		            $colorData = $object->getRecordByQuery('SELECT * FROM organisations_theme_settings WHERE is_deleted = 0 AND company_id IN ('.$f['company_id'].') LIMIT 1');
		            if(!empty($colorData)){
		                $_SESSION['colorData'] = $colorData;
		            }
		        }
				if(isset($_SESSION['error'])){unset($_SESSION['error']);}
				//New Permision Setup
				$permisions = $object->selQRYMultiple('permission_name, is_allow', 'user_permission', 'user_id = "'.$f['user_id'].'"');
				if(!empty($permisions)){
					foreach($permisions as $permission){
						$_SESSION[$permission['permission_name']] = $permission['is_allow'];	
					}
				}
				//New Permision Setup
				$qs = "SELECT user_role, issued_to, project_id FROM  user_projects WHERE is_deleted = 0  AND user_id = '".$_SESSION['ww_builder_id']."'";
				$rs=$obj->db_query($qs);
				$projUserRole = array();
				while($f=$obj->db_fetch_assoc($rs)){
					$projUserRole[$f["project_id"]] = $f["user_role"];
					$_SESSION['userRole'] = $f["user_role"];
					if ($f["user_role"] == "Sub Contractor"){
						$_SESSION['userIssueTo'] = $f["issued_to"];
						break;
					}
				}
				$_SESSION['projUserRole'] = $projUserRole;
				
				if($isIpad == 1 && !empty($redirectTo)){
					$_SESSION['loginRedirectURL'] = "pms.php?sect=".str_replace('###', '&', $redirectTo)."";
				}

	//Check Cookies are set or not and set it to session variables
				if(!isset($_SESSION['requestedURL'])){
					if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_qc']))
						$_SESSION['qc'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_qc']);
					if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_ir']))
						$_SESSION['ir'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_ir']);
					if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_pmr']))
						$_SESSION['pmr'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_pmr']);
					if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_qar']))
						$_SESSION['qar'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_qar']);
					if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_clr']))
						$_SESSION['clr'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_clr']);
					if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_pm']))
						$_SESSION['pm'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_pm']);
					if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_qa']))
						$_SESSION['qa'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_qa']);
					if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_asr']))
						$_SESSION['asr'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_asr']);
		//Check Cookies are set or not and set it to session variables
					
					if($_SESSION['userRole'] != 'Sub Contractor'){
					if(!empty($_SESSION['loginRedirectURL'])){
						header("location:".$_SESSION['loginRedirectURL']."");
					}else{
						$uriLoc = "no_permission"; 
						if(!empty($permisions)){
							$query = "SELECT project_id FROM user_projects WHERE user_id = ".$_SESSION['ww_builder_id']." AND is_deleted = 0";
							$rs = mysql_query($query);
							$projectID = "";
							$porojIds = array();
							if(mysql_num_rows($rs) > 0){
								while($row = mysql_fetch_assoc($rs)){
									$porojIds[] = $row['project_id'];	
								}
							}
							if(count($porojIds) > 1){
								$projectID = $_COOKIE['pmb_'.$_SESSION['ww_builder_id']];
						
								if($projectID == ''){
									$projectID = base64_encode(end($porojIds));
								}
							}else{
								$projectID = base64_encode($porojIds[0]);
							}
							foreach($permisions as $permission){
								if($permission['permission_name'] == 'web_menu_health_checkup' && $permission['is_allow'] == 1){
									$uriLoc = 'b_full_analysis';
									break;
								}
								if($permission['permission_name'] == 'web_menu_pmb' && $permission['is_allow'] == 1){
									$uriLoc = 'inbox_insp&folderType=Inspections';
									break;
								}
								if($permission['permission_name'] == 'web_menu_projects' && $permission['is_allow'] == 1){
									$uriLoc = 'show_project';
									break;
								}
								if($permission['permission_name'] == 'web_menu_reports' && $permission['is_allow'] == 1){
									$uriLoc = 'i_report';
									break;
								}
								if($permission['permission_name'] == 'web_menu_quality_control' && $permission['is_allow'] == 1){
									$uriLoc = 'i_defect';
									break;
								}
								if($permission['permission_name'] == 'om_manual' && $permission['is_allow'] == 1){
									$uriLoc = 'project_manual&id='.$projectID;
									if(in_array($_SESSION['userRole'], array('GPCL', 'GPCL - User', 'GPCL - Manager', 'All Defect')))
										$uriLoc = 'dashboard_manual&id='.$projectID;
									break;
								}
								if($permission['permission_name'] == 'completion_report' && $permission['is_allow'] == 1){
									$uriLoc = 'completion_dashboard&id='.$projectID;
									if(in_array($_SESSION['userRole'], array('Independent Reviewer', 'Client', 'HoneywellFM', 'GPCL', 'GPCL - User', 'GPCL - Manager', 'All Defect', 'Department of Health', 'Peter Mac', 'Umowlai', 'Philip Chun and Associates', 'Plenary')))
										$uriLoc = 'completion_dashboard&id='.$projectID;
									break;
								}
							}
						}
						header("location:pms.php?sect=".$uriLoc);
					}	
				}else{
					if(!empty($_SESSION['loginRedirectURL'])){
						header("location:".$_SESSION['loginRedirectURL']."");
					}else{
						header("location:pms.php?sect=i_defect");				
					}	
				}
				}else{
					header("location:pms.php?sect=login");
				}
			}else{
				$_SESSION['error']['message']="Invalid username or password, please try again!";
				header("location:pms.php?sect=login");
				//$result = 0;
			}
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
		if($obj->db_num_rows($obj->db_query("SELECT * FROM ".BUILDERS." WHERE builder_user_name='$username' AND id!='".$_SESSION['ww_builder_id']."'")) > 0){
			$result = 4;				
		}else{
			if(trim($_POST['password']!='')){
				$cplainpswd=mysql_real_escape_string(trim($_POST['password']));
				$password=md5($cplainpswd);
				
				$pswd=",user_password='$password',user_plainpassword='$cplainpswd'";				
			}
			$q="UPDATE ".BUILDERS." SET
					 user_name = '".$username."',
					 user_fullname = '".$fname."',
					 company_name = '".$compname."',
					 user_email = '".$email."',
					 user_phone_no = '".$mobile."'
				$pswd
				WHERE user_id = '".$_SESSION['ww_builder_id']."'";
				
			$obj->db_query($q);
			
			$_SESSION['ww_logged_in_as']=$fname;
			$_SESSION['ww_builder_full_name']=$fname;
			
			$result = 0;
		}
	}
}

/* owner/tenant sign-in */
elseif($sect=='tenant_sign_in'){
	$password = md5(mysql_real_escape_string(trim($_POST['password'])));
	$user_name = mysql_real_escape_string(trim($_POST['username']));
	
	if(empty($_POST['username']) || empty($_POST['password'])){
		if(empty($_POST['username'])){ $_SESSION['error']['username']="The username field is required"; }
		if(empty($_POST['password'])){ $_SESSION['error']['password']="The password field is required"; $_SESSION['post_username']=$_POST['username']; }
		header("location:pms.php?sect=tenant");
	}else{
		$q = "SELECT * FROM user  WHERE user_password = '$password' AND user_name = '$user_name' AND active = '1' and user_type = 'inspector' and is_deleted = 0";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$f = $obj->db_fetch_assoc($obj->db_query($q));
			
			// Remove extra cookes 
			$object->removeCookies($f['user_id']);
			
			$_SESSION['ww_is_builder'] = 1;
			$_SESSION['ww_builder_id'] = $f['user_id'];
			$_SESSION['ww_builder_full_name'] = $f['user_fullname'];
			$_SESSION['ww_comp_name'] = $f['comp_name'];
			$_SESSION['ww_builder_user_name'] = $f['user_name'];
			$_SESSION['ww_builder_plain_pswd'] = $f['user_plainpassword'];
			$_SESSION['ww_builder_email'] = $f['user_email'];
			$_SESSION['ww_logged_in_as']= $f['user_fullname'];
			$_SESSION['ww_builder'] = $f;
			$_SESSION['ww_builder_fk_c_id'] = $f['fk_c_id'];
            

			//New Permision Setup
				$permisions = $object->selQRYMultiple('permission_name, is_allow', 'user_permission', 'user_id = "'.$f['user_id'].'"');
				if(!empty($permisions)){
					foreach($permisions as $permission){
						$_SESSION[$permission['permission_name']] = $permission['is_allow'];	
					}
				}
			//New Permision Setup
			if(isset($_SESSION['error'])){unset($_SESSION['error']);}

			$qs = "SELECT user_role, issued_to, project_id FROM  user_projects WHERE is_deleted = 0  AND user_id = '".$_SESSION['ww_builder_id']."'";
			$rs=$obj->db_query($qs);
			$projUserRole = array();
			while($f=$obj->db_fetch_assoc($rs)){
				$projUserRole[$f["project_id"]] = $f["user_role"];
				$_SESSION['userRole'] = $f["user_role"];
				if ($f["user_role"] == "Sub Contractor"){
					$_SESSION['userIssueTo'] = $f["issued_to"];
					break;
				}
			}
			$_SESSION['projUserRole'] = $projUserRole;
			//Check Cookies are set or not and set it to session variables
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_qc']))
				$_SESSION['qc'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_qc']);
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_ir']))
				$_SESSION['ir'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_ir']);
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_pmr']))
				$_SESSION['pmr'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_pmr']);
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_qar']))
				$_SESSION['qar'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_qar']);
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_clr']))
				$_SESSION['clr'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_clr']);
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_pm']))
				$_SESSION['pm'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_pm']);
			if(isset($_COOKIE[$_SESSION['ww_builder_id'].'_qa']))
				$_SESSION['qa'] = unserialize($_COOKIE[$_SESSION['ww_builder_id'].'_qa']);
			//Check Cookies are set or not and set it to session variables
			if($_SESSION['userRole'] != 'Sub Contractor'){
				header("location:pms.php?sect=b_full_analysis");
			}else{
				header("location:pms.php?sect=i_defect");				
			}
		}else{
			//$result = 0;
			$_SESSION['error']['message']="Invalid credentials, please try again!";
			header("location:pms.php?sect=tenant");
		}
	}
}

/* add project by builder */
elseif($sect=='add_project'){
	$name=mysql_real_escape_string(trim($_POST['name']));
	$protype=mysql_real_escape_string(trim($_POST['protype']));
	$line1=mysql_real_escape_string(trim($_POST['line1']));
	$line2=mysql_real_escape_string(trim($_POST['line2']));
	$suburb=mysql_real_escape_string(trim($_POST['suburb']));
	$state=mysql_real_escape_string(trim($_POST['state']));
	$postcode=mysql_real_escape_string(trim($_POST['postcode']));
	$country=mysql_real_escape_string(trim($_POST['country']));
	
	$associateTo=$_POST['associateTo'];

	if($protype=='' || $name=='' || $line1=='' || $suburb=='' || $state=='' || $postcode=='' || $country==''){
		$result = 0;
	}else{
		$builder_id = $_SESSION['ww_builder_id'];
		
		$q = "INSERT INTO ".PROJECTS." SET
					builder_id = '".$builder_id."',
					project_name = '".$name."',
					project_type = '".$protype."',
					project_address_line1 = '".$line1."',
					project_address_line2 = '".$line2."',
					project_suburb = '".$suburb."',
					project_state = '".$state."',
					post_code = '".$postcode."',
					project_country = '".$country."'";
		$obj->db_query($q);
		
		$lastProId = mysql_insert_id();	
		// set project unique id.	
		$pI = strlen($lastProId);
		$r = $obj->rendomNum(8);
		$c = substr($r,0,-$pI);
		$pro_code = $c.$lastProId;
			
		$q = "UPDATE ".PROJECTS." SET
						pro_code = '".$pro_code."',
						last_modified_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."'
					WHERE
						id = '".$lastProId."'";
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
		
		$result = 1;
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
		$defectList=$_POST['defectList'];
		$associateTo=$_POST['associateTo'];
		
		if($protype=='' || $name=='' || $line1=='' || $suburb=='' || $state=='' || $postcode=='' || $country==''){
			$result = 0;
		}else{	
			
			$q = "UPDATE ".PROJECTS." SET
							project_name = '".$name."',
							project_type = '".$protype."',
							project_address_line1 = '".$line1v."',
							project_address_line2 = '".$line2."',
							project_suburb = '".$suburb."',
							project_state = '".$state."',
							project_postcode = '".$postcode."',
							project_country = '".$country."',
							last_modified_date = NOW(),
							last_modified_by = '".$_SESSION['ww_builder_id']."'
				 WHERE project_id = '".$pro_id."'";
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
		}
	}elseif(isset($_POST['remove'])){
		
		// check for managers
		$qm=$obj->db_num_rows($obj->db_query("SELECT bsb_id FROM ".SUBBUILDERS." WHERE fk_p_id='$pro_id' AND fk_b_id='$builder_id'"));
		// check for inspectors
		$qi=$obj->db_num_rows($obj->db_query("SELECT id FROM ".OWNERS." WHERE ow_project_id='$pro_id'"));
		// check for defects
		$qd=$obj->db_num_rows($obj->db_query("SELECT df_id FROM ".DEFECTS." WHERE project_id='$pro_id' AND status!='Closed'"));
		// check for trades
		$qt=$obj->db_num_rows($obj->db_query("SELECT resp_id FROM ".RESPONSIBLES." WHERE project_id='$pro_id' AND builder_id='$builder_id'"));
		
		if($qm > 0){
			$result = 2;
		}elseif($qi > 0){
			$result = 3;
		}elseif($qt > 0){
			$result = 4;
		}elseif($qd > 0){
			$result = 5;
		}else{
			// remove record from PROJECTS
			$obj->db_query("DELETE FROM ".PROJECTS." WHERE project_id='$pro_id'");
			
			// remove records from SUBBUILDERS
			$obj->db_query("DELETE FROM ".SUBBUILDERS." WHERE fk_p_id='$pro_id'");
			
			// remove records from OWNERS
			$obj->db_query("DELETE FROM ".OWNERS." WHERE ow_project_id='$pro_id'");
			
			// remove records from DEFECTS
			$obj->db_query("DELETE FROM ".DEFECTS." WHERE project_id='$pro_id' AND status='Closed'");
			
			// remove records from RESPONSIBLES
			$obj->db_query("DELETE FROM ".RESPONSIBLES." WHERE project_id='$pro_id'");
			
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

/* add inspector for project by builder */
elseif($sect=='add_inspector'){
	$button=$_POST['button'];
	$full_name=mysql_real_escape_string(trim($_POST['ownerName']));
	$user_name=mysql_real_escape_string(trim($_POST['userName']));
	$password=mysql_real_escape_string(trim($_POST['password']));
	$proId=mysql_real_escape_string(trim($_POST['proId']));
	$phone=mysql_real_escape_string(trim($_POST['phone']));
	$email=mysql_real_escape_string(trim($_POST['email']));
	$ownerId = '';
	$owner_exist = 0;
	
	if($full_name=='' || $user_name=='' || $password=='' || $phone=='' || $email==''){
		$result = 0;
	}elseif(!is_numeric($phone)){
		$result = 1;	
	}elseif($obj->isValidEmail($email)==false){
		$result = 2;
	}elseif(strlen($password)<9){
		$result = 3;
	}else{
		// check username
		$q = "SELECT * FROM ".OWNERS." WHERE user_name = '$user_name'";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			$result = 4;
		}else{
			$phone=mysql_real_escape_string(trim($_POST['phone']));
			$email=mysql_real_escape_string(trim($_POST['email']));
			
			// check if inspector email already exist!
			$q = "SELECT * FROM ".OWNERS." WHERE email = '$email'";
			if($obj->db_num_rows($obj->db_query($q)) > 0){
				$result = 5;
			}else{
				// create inspector & alloted project to him.
				$verify_code = md5($obj->rendom(15));
				$plain=$password;
				$password=md5($plain);
				
				// add inspector
				$q = "INSERT INTO ".OWNERS." (active,verify_code,owner_full_name,email,user_name,password,plain_pswd,phone,ow_project_id)
				 VALUES('1','$verify_code','$full_name','$email','$user_name','$password','$plain','$phone','$proId')";
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

/* edit / remove inspector for project by builder */
elseif($sect=='edit_remove_inspector'){
	
	$builder_id=$_SESSION['ww_builder_id'];
	$owner_id=mysql_real_escape_string(trim($_POST['owner_id']));
	
	if($owner_id==''){
		$result=9;
	}else{
		// update inspector
		if(isset($_POST['edit'])){		
			$proId=mysql_real_escape_string(trim($_POST['proId']));		
			$full_name=mysql_real_escape_string(trim($_POST['ownerName']));
			$user_name=mysql_real_escape_string(trim($_POST['userName']));
			$password=mysql_real_escape_string(trim($_POST['password']));
			$phone=mysql_real_escape_string(trim($_POST['phone']));
			$email=mysql_real_escape_string(trim($_POST['email']));
			
			if($full_name=='' || $user_name=='' || $password=='' || $phone=='' || $email==''){
				$result = 0;
			}elseif(!is_numeric($phone)){
				$result = 1;	
			}elseif($obj->isValidEmail($email)==false){
				$result = 2;
			}elseif(strlen($password)<9){
				$result = 3;
			}else{
				// check username
				$q = "SELECT * FROM ".OWNERS." WHERE user_name = '$user_name' AND id != '$owner_id'";
				if($obj->db_num_rows($obj->db_query($q)) > 0){
					$result = 4;
				}else{
					// check email id
					$q = "SELECT * FROM ".OWNERS." WHERE email = '$email' AND id != '$owner_id'";
					$r = $obj->db_query($q);
					if($obj->db_num_rows($r) > 0){
						$result = 5;
					}else{
						// if project had already alloted?
						$f=$obj->db_fetch_assoc($r);
						if($f['ow_project_id']==$proId){
							$result = 6;
						}else{
							$plain=$password;
							$password=md5($plain);
			
							// edit inspector record
							$q = "UPDATE ".OWNERS." SET owner_full_name='$full_name', email='$email', user_name='$user_name', 
								  password='$password', plain_pswd='$plain', phone='$phone' WHERE id='$owner_id' ";
							$obj->db_query($q);
							
							$result = 7;							
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
	$project_id=mysql_real_escape_string(trim($_POST['project_id']));
	$fixed_by_date=mysql_real_escape_string(trim($_POST['fixed_by_date']));
	$owner_id=$_SESSION['ww_owner_id'];
	$defect_type=mysql_real_escape_string(trim($_POST['defect_type']));
	$priority=mysql_real_escape_string(trim($_POST['priority']));
	$repairer=mysql_real_escape_string(trim($_POST['repairer']));
	$area_room=mysql_real_escape_string(trim($_POST['area_room']));
	$defect_desc=mysql_real_escape_string(trim($_POST['defect_desc']));
	$defect_note=mysql_real_escape_string(trim($_POST['defect_note']));
	$inspected_by=mysql_real_escape_string(trim($_POST['inspected_by']));
	$create_date = date('Y-m-d');
	
	$photo = '';
	$destiny = 'defects_photo/';
	
	if($fixed_by_date=='' || $inspected_by=='' || $area_room=='' || $defect_desc==''){
		$result = 0;
	}else{
		if($_FILES["photo"]['tmp_name']!=''){
			$name = $_FILES["photo"]['name'];
			$tmp_name = $_FILES["photo"]['tmp_name'];
			$photo = $obj->upload_file($name, $tmp_name, $destiny);
		}
		
		// change date format for fixed_by_date
		$fixed_by_date = date("d/m/Y", strtotime($fixed_by_date));
	
		$dcode = $obj->rendomNum(8);
		
		$q = "INSERT INTO ".DEFECTS." (defect_code,owner_id,project_id,resp_id,area_room,defect_type_id,defect_desc,defect_note,photo,priority,
			 create_date,fixed_by_date,inspected_by)
			 VALUES('$dcode','$owner_id','$project_id','$repairer','$area_room','$defect_type','$defect_desc','$defect_note','$photo',
			 '$priority','$create_date','$fixed_by_date','$inspected_by')";
		$obj->db_query($q);
		
		$result = 1;
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
	
	$pro_id=mysql_real_escape_string($_POST['pro_name']);
	$resp_id=mysql_real_escape_string($_POST['repairer_name']);
	$status=mysql_real_escape_string($_POST['status']);
	$defect_type=mysql_real_escape_string($_POST['defect_type']);
	$report_type=mysql_real_escape_string($_POST['report_type']);
	
	$src_type='All';
	$src_status='All';
	$issued_to='All';
	
	//$w="p.id = '$pro_id' AND p.user_id = '$builder_id'";
	$w="p.project_id = '$pro_id' ";
	
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
				$pro_name = $f['pro_name'];
				
				// If Issued To Contact Name is selected
				if($resp_id!=''){
					$issued_to = $f['resp_full_name'];
				}
				
				// builder info
				$builder_full_name = $f['builder_full_name'];
				$mobile = $f['mobile'];
				$builder_email = $f['builder_email'];
			
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
					<? $csv_output .= $row['pro_name'] . ", ";?>
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
	
	if(empty($_POST['username']) || empty($_POST['password'])){
		if(empty($_POST['username'])){ $_SESSION['error']['username']="The username field is required"; }
		if(empty($_POST['password'])){ $_SESSION['error']['password']="The password field is required"; $_SESSION['post_username']=$_POST['username']; }
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
			if(isset($_SESSION['error'])){unset($_SESSION['error']);}
			header("location:pms.php?sect=r_dashboard");
		}else{
			//$result = 0;
			$_SESSION['error']['message']="Invalid credentials, please try again!";
			header("location:pms.php?sect=responsible");
		}
	}
}

/* create csv by responsable */
elseif($sect=='create_csv_responsible'){
	$resp_id=$_SESSION['ww_resp_id'];
	
	$pro_id=mysql_real_escape_string($_POST['pro_name']);
	$assign_id=mysql_real_escape_string($_POST['assign_to_name']);
	$status=mysql_real_escape_string($_POST['status']);
	$defect_type=mysql_real_escape_string($_POST['defect_type']);
	$report_type=mysql_real_escape_string($_POST['report_type']);
	
	$w="p.project_id = '$pro_id' AND d.resp_id = '$resp_id'";
	
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
	$_SESSION['error']['type']="failure_r";
	if($email==''){
		//$result=0;
		$_SESSION['error']['email']=$_POST['email']." ";
		header("location:pms.php?sect=forgot_password&type=$type");		
	}elseif($obj->isValidEmail($email)==false){
		//$result=1;
		$_SESSION['error']['message']="Invalid email format.";
		header("location:pms.php?sect=forgot_password&type=$type");		
	}else{
		$email=mysql_real_escape_string($email);
		
		// check if registered member?
		$tbl=COMPANIES;
		$whereCond="comp_email = '$email'";
		if($type=='1'){
			$tbl=BUILDERS; 
			$whereCond="user_email='$email' AND user_type='manager' AND is_deleted = 0";
			
		}elseif($type>1){
			$tbl="user";
			$whereCond="user_email='$email' AND user_type!='manager' AND is_deleted = 0";
			
		}
		
		$q="SELECT * FROM $tbl WHERE $whereCond";
		if($obj->db_num_rows($obj->db_query($q)) > 0){
			
			$f=$obj->db_fetch_assoc($obj->db_query($q));
			if($type=='1'){
				$fullname = $f['user_fullname'];
				$username = $f['user_name'];
				$id = $f['user_id'];
				
			}elseif($type>1){
				$fullname = $f['user_fullname'];
				$username = $f['user_name'];
				$id = $f['user_id'];
				
			}else{
				$fullname = $f['comp_fullname'];
				$username = $f['comp_userName'];
				$id = $f['c_id'];
			}
			
		//Send Mail Start Here			
		include('includes/class.phpmailer.php');
			
			$mail = new PHPMailer();
			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->SMTPSecure = 'tls'; //"ssl";	// sets the prefix to the servier
			$mail->Host = smtpHost; 	//pod51022.outlook.com      // sets GMAIL as the SMTP server
			$mail->SMTPDebug = 0;				// enables SMTP debug information (for testing)
			$mail->SMTPAuth = true;        		// enable SMTP authentication
			$smtpPort = smtpPort;
			if(!empty($smtpPort)){
				$mail->Port =  smtpPort; //587;
			}
			$mail->Username = smtpUsername; //"wiseworkingsales@gmail.com"; // SMTP account username
			$mail->Password = smtpPassword; //"Wiseworking123";   // SMTP account password

			$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
		
			$mail->SetFrom('WiseworkingSystems@wiseworking.com.au', "DefectID");
			$mail->SetFrom('WiseworkingSystems@wiseworking.com.au', "DefectID");
#				$mail->SetFrom('administrator@'.trim(DOMAIN, '/'), trim(DOMAIN, '/'));
#				$mail->AddReplyTo('administrator@'.trim(DOMAIN, '/'), trim(DOMAIN, '/'));
			$mail->Subject = "Reset Your Password";
			$mail->IsHTML(true);
			$path = 'http://'.str_replace('/', '', str_replace('http://', '', DOMAIN));
			$curtime = base64_encode(date('Y-m-d h:i:s'));
			$randomKey=substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',5)),0,10);
			$link = $path."/reset_password.php?ct=".$curtime."&token=".$randomKey."_".$id."_".$type;
			$msg = '<html><head> <title>Account Login Information</title></head><body>
				<h4>Hi '.(!empty($fullname)?$fullname:$username).',</h4>
				Changing your password is simple. Please use the link below within 2 hours.<br>
				<a href="'.$link.'" target="_parent">Reset Password</a> <br><br><br>
				<hr />
				<em>Regards From<br>
				<a href="'.$path.'" target="_parent">'.SITE_NAME.'</a></em>
				</body>	</html>	';			

			$mail->MsgHTML($msg);			

			$mail->AddAddress($email); // To
			$mail->Send();			
			$mail->ClearAddresses();
			$mail->ClearAllRecipients();
			
			//$result=2;	sleep(1);
			$_SESSION['error']['message']="We sent you reset password link on your email address i.e. ".$email;
			$_SESSION['error']['type']="success_r";
			header("location:pms.php?sect=forgot_password&type=$type");
		}else{
			//$result=3;
			$_SESSION['error']['message']="Email id is not exists!";
			header("location:pms.php?sect=forgot_password&type=$type");
		}		
	}
}

?>

<script language="javascript" type="text/javascript">window.top.window.stopAjax(<?php echo $result; ?>);</script>
