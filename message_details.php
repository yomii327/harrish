<?php session_start();
ob_start();
if (!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])){?>
<script language="javascript" type="text/javascript">window.location.href = "<?= HOME_SCREEN ?>";</script>
<?php }
if (isset($_GET['projID']))
    $_SESSION['idp'] = $_GET['projID'];
if (!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])) {
    if (isset($_GET['byEmail']) && $_GET['byEmail'] >= 1) {
        $_SESSION['idp'] = $_GET['projID'];
        $pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        $_SESSION['inspViewPath'] = $pageURL;
    }
    $redirect = $pageURL;
    if (isset($_GET['byEmail']) && $_GET['byEmail'] == 1) {
        $redirect.= '/pms.php?sect=builder';
    } elseif (isset($_GET['byEmail']) && $_GET['byEmail'] == 2) {
        $redirect.= '/pms.php?sect=tenant';
    }?>
<script language="javascript" type="text/javascript">window.location.href = "<?= $redirect ?>";</script>
<?php }
$msg = '';
require_once('includes/class.phpmailer.php');
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$userId = $_SESSION['ww_builder']['user_id'];

//Assing Default message users
/* #$defaultUserArr = array('156' => 'peter.paritsi@crema.com.au', '157' => 'michael.guardiani@crema.com.au', '168' => 'jesse.rosenfeld@crema.com.au', '169' => 'Austin.Giordano@crema.com.au', '172' => 'alistair.souter@crema.com.au');
  $defaultUserArr = array('156' => 'peter.paritsi@crema.com.au', '157' => 'michael.guardiani@crema.com.au', '169' => 'Austin.Giordano@crema.com.au', '354' => 'jason.treasure@crema.com.au', '355' => 'joseph.frisina@crema.com.au');
  $defaultIdArr = array_keys($defaultUserArr); */
//Unset current user
/* if(in_array($_SESSION['ww_builder_id'], $defaultIdArr))		unset($defaultUserArr[$_SESSION['ww_builder_id']]); */

if (isset($_POST['message']) and $_POST['message'] !== '') {
	//echo '<pre>';print_r($_REQUEST);exit;
    $attahment1 = $_SESSION[$_SESSION['idp'].'_emailfile'];
    $pmbAttachment = $_SESSION[$_SESSION['idp'].'_pmbEmailfile'];
    //echo "MBATT<br><pre>";print_r($pmbAttachment);
    //print_r($attahment1); die;

//Filter Data Here
    /*
      if($_POST['messageType'] == 'Request For Information' && $_SESSION['idp'] == 212){
      foreach($defaultIdArr as $key=>$toUserID){//Check and set default user as a cc members
      if(!in_array($toUserID, $_POST['recipTo'])){//Check User in email
      if(!in_array($defaultUserArr[$toUserID], $_POST['recipCC'] ) && isset($defaultUserArr[$toUserID])){//Check User in email
      $_POST['recipCC'][] = $defaultUserArr[$toUserID];
      }
      }
      }
      } */
//Data Filteration Section Start Here
    function addDoubleQuotes($element) {
        return '"' . $element . '"';
    }

//Function fore adding double qoutations

    $ccFilterList = array_map("addDoubleQuotes", $_POST['recipCC']);
    $userDataCC = $object->selQRYMultiple('u.user_id, u.user_email', 'user AS u, user_projects AS up', 'u.is_deleted = 0 AND up.is_deleted = 0 AND up.user_id = u.user_id AND up.project_id = ' . $_SESSION['idp'] . ' AND u.user_email IN (' . join(",", $ccFilterList) . ')');
    $ccUsserIDArr = array();
    foreach ($userDataCC as $usrDt) {
        $ccUsserIDArr[$usrDt['user_email']] = $usrDt['user_id'];
    }
//Data Filteration Section End Here
	$messageData1 = explode('<input type="hidden">',$_POST['message']);
	#print_r($messageData1);die;
    $updateMessageID = 0;
    $projectId = $_SESSION['idp'];
    $from = $_SESSION['ww_builder_id'];
    $recipTo = !empty($_POST['recipTo']) ? $_POST['recipTo'] : $_POST['to_id'];
    $recipCC = $_POST['recipCC'];
    //$subject = empty($_POST['title']) ? 'no subject' : "RE : " . $_POST['title'];

if(!empty($_POST['title'])){ 
	$subject = "RE: WiseWorker:".base64_decode($_REQUEST['id'])."- : " . $_POST['title']; 
}else{
	$subject = 'No subject';
}

    $tags = ''; //$_POST['tags'];	

    $messageType = $_POST['messageType'];
    $refrenceNumber = $_POST['refrenceNumber'] . " " . $_POST['messageType'];
    $RFIstatus = "";
    if ($messageType == "Request For Information") {
        $RFIstatus = $_POST['RFIstatus'];
        $updateMessageID = $_POST['updateMessageID'];
        $rfiNumber = $_POST['rfiNumber'];

        $_POST['message'] = "<br>RFI #" . $_POST['rfiNumber'] . "<br><br>" . $_POST['message'];

        if ($RFIstatus == 'Closed')
            $closedDate = 'NOW()';
    }


    $messageDetails = $_POST['message'];
    $messgeId = 0;
    $thread_id = $_POST['thread_id'];
	$toEmailList = array();
	$ccEmailList = array();	
    $ccAddress = '';
    $toExtraAddress = '';
    if ($_POST['emailAttachedAjax'] == 1) {
        $attahment1 = $_SESSION[$_SESSION['idp'] . '_emailfile'];
    }
    if ($messageType != 'Request For Information') {
		$subject = $subject; //New Update
        //$subject = $messageType . ' ' . $subject; //New Update
    }
    // Remove old attachment if found any attachment
    if (isset($_POST['removeAttachment'])) {
        if (explode(',', str_replace(', ', ',', $_POST['removeAttachment']))) {
            $removeAttachments = explode(',', str_replace(', ', ',', $_POST['removeAttachment']));
            foreach ($removeAttachments as $attachID) {
                mysql_query("UPDATE `pmb_attachments` SET `is_deleted` = '1', last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '" . $userId . "' WHERE `attach_id` =" . $attachID);
            }
        } else {
            mysql_query("UPDATE `pmb_attachments` SET `is_deleted` = '1', last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '" . $userId . "' WHERE `attach_id` =" . $_POST['removeAttachment']);
        }
    }
    if (!empty($recipTo) && !empty($subject) && !empty($messageDetails)) {
        # Start :- Send Email 
		$mail = new PHPMailer(true);
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

        $path = 'http://' . str_replace('/', '', str_replace('http://', '', DOMAIN));

        //$mail->SetFrom('administrator@'.trim(DOMAIN, '/'), trim(DOMAIN, '/'));
        $mail->AddReplyTo('administrator@' . trim(DOMAIN, '/'), trim(DOMAIN, '/'));
        $mail->SetFrom('WiseworkingSystems@wiseworking.com.au', "WiseWorker");
        $mail->Subject = $subject;
        $mail->IsHTML(true);
        $byEmail = ($_SESSION['ww_builder']['user_type'] == "manager") ? 1 : 2;
        $msg = "<br/>  <br>" . $messageData1[0];
        
        #$userImage = $object->selQRYMultiple('user_id, user_signature', 'user', 'is_deleted  = 0 AND user_id = ' . $_SESSION['ww_builder_id'] . '');
        #$msg .="<img src='" . IMG_SRC . "user_images/" . $userImage[0]['user_signature'] . "'>";

        if (isset($recipCC)) {
            foreach ($recipCC as $cc) {
				$mail->AddBCC(trim($cc), '');
               // $mail->AddCC(trim($cc), '');
                $ccAddress.= ($ccAddress == '') ? $cc : ', ' . $cc;
				$ccEmailList[] = $cc;
            }
        }

        if (isset($recipTo)) {
            foreach ($recipTo as $to) {
                if (is_numeric($to)) {
                    $query = "select u.user_id, u.user_type, u.user_fullname as fullname, u.user_email as email from user as u where u.is_deleted=0 and u.user_id='" . $to . "'";
                    $getUserDetails = mysql_query($query);
                    while ($gUDetail = mysql_fetch_array($getUserDetails)) {
                        $mail->AddBCC($gUDetail['email'], ''); // To
						//$mail->AddAddress($gUDetail['email'], ''); // To
						$toEmailList[] = $gUDetail['email'];
                    }
                } elseif (!empty($to)) {
                    $mail->AddBCC($to, ''); // To
					//$mail->AddAddress($to, ''); // To
                    $toExtraAddress.= ($toExtraAddress == '') ? $to : ', ' . $to;
					$toEmailList[] = $to;
                }
            }
        }
//Message Attachments Start Here
        foreach ($attahment1 as $key => $value) {
            $arr = explode(".", $value);
            if($arr[1] == "zip" || $arr[1] == "exe"){
                //echo $arr[1];exit;
            }else{
                $mail->AddAttachment("attachment/" . $value);        
            }            
            //$mail->AddAttachment("attachment/" . $value);
        }   
		
		// .msg attachments
		if(isset($_REQUEST['filesArr']) && !empty($_REQUEST['filesArr'])){
			foreach($_REQUEST['filesArr'] as $files){
				$mail->AddAttachment("pmb_attachment/".$_SESSION['idp'].'/'.$files, $files);
			}	
		}  
		
		#PMB Attachment
		if(isset($pmbAttachment) && !empty($pmbAttachment)) {
			foreach($pmbAttachment as $key => $val) {
				$mail->AddAttachment("attachment/". $val);
			}
		}		
		   
//Message Attachments End Here
        # Save message in PMB
        if (get_magic_quotes_gpc())
            $messageDetails = stripslashes($messageDetails);

        $messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));
        $recipTo = !empty($_POST['recipTo']) ? $_POST['recipTo'] : array(0);
        if (isset($recipTo)) {
            foreach ($recipTo as $to) {
                if (is_numeric($to)) {
                    $messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageType, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, $thread_id, $refrenceNumber, $rfiNumber, "", $RFIstatus, $closedDate, $updateMessageID);
                    $messgeId = $messageBoard['messgeId'];
                } else {
                    $messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageType, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, $thread_id, $refrenceNumber, $rfiNumber, "", $RFIstatus, $closedDate, $updateMessageID);
                    $messgeId = $messageBoard['messgeId'];
                }
                $attahment1 = '';
            }
        }
//Added for display message for cc sections Start Here
        if (isset($ccUsserIDArr)) {
            foreach ($ccUsserIDArr as $ccInsertEmail => $ccInsertId) {
                $messageBoard = $object->messageBoard($projectId, $from, $ccInsertId, $subject, $messageType, $messageDetails, "", $messgeId, $toExtraAddress, $ccAddress, $tags, 0, $thread_id, $refrenceNumber, $rfiNumber, "", $RFIstatus, $closedDate, $updateMessageID, 1);
                $messgeId = $messageBoard['messgeId'];
            }
        }
//Added for display message for cc sections End Here
        # Set sending type
        mysql_query("update pmb_message set sending_type = 'reply', last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '" . $userId . "' where message_id = " . $messgeId);

        $msg .= "<br/><br/>click here to access your message.<br>
						<a href='" . $path . "/pms.php?sect=message_details&id=" . base64_encode($messageBoard['thread_id']) . "&type=inbox&projID=" . $_SESSION['idp'] . "&byEmail=" . $byEmail . "' target='_blank'>" . $path . "/pms.php?sect=message_details&id=" . base64_encode($messageBoard['thread_id']) . "&type=inbox&projID=" . $_SESSION['idp'] . "&byEmail=" . $byEmail . "</a>";
        $msg .= "<br/><br/>Thanks,<br> DefectId customer care<br><br>";
        
        $msg .= $messageData1[1];
       # echo $msg;die;
	   
        $toCCAddress = empty($toEmailList)?'':'To : '.implode(', ', $toEmailList).'<br>';
		$toCCAddress.= empty($ccEmailList)?'':'Cc : '.implode(', ', $ccEmailList).'<br>';
		$toCCAddress.= '<hr><br><b>'.$mail->Subject.'</b><br><br>';
		$mail->MsgHTML($toCCAddress.$msg);	

        $result = $mail->Send();
        $mail->ClearAddresses();
        $mail->ClearAllRecipients();
		
		#Start:- Save .msg attachment
		if(!empty($_POST['filesArr'])){
			$filesArr = explode(',',$_POST['filesArr']);
			if(sizeof($filesArr)>1){
				foreach($filesArr as $filesArrVal){
					 $query = "INSERT INTO pmb_attachments SET
						project_id = '".$_SESSION['idp']."',
						name = '".stripcslashes($filesArrVal)."',
						attachment_name = '".stripcslashes($filesArrVal)."',
						message_id = '".$messgeId."',
						last_modified_date = NOW(),
						last_modified_by = '".$_SESSION['ww_builder_id']."',
						created_date = NOW(),
						created_by = '".$_SESSION['ww_builder_id']."',
						is_attached_email = 1"; 
						mysql_query($query);
				}
			}else{
				$query = "INSERT INTO pmb_attachments SET
					project_id = '".$_SESSION['idp']."',
					name = '".stripcslashes($_POST['filesArr'][0])."',
					attachment_name = '".stripcslashes($_POST['filesArr'][0])."',
					message_id = '".$messgeId."',
					last_modified_date = NOW(),
					last_modified_by = '".$_SESSION['ww_builder_id']."',
					created_date = NOW(),
					created_by = '".$_SESSION['ww_builder_id']."',
					is_attached_email = 1"; 
					mysql_query($query);
			}
		}
		#End:- Save .msg attachment

        if ($_POST['composeId'] != 0 && isset($_POST['save'])) {
            mysql_query("update pmb_message set is_draft = 0, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '" . $userId . "' where message_id = " . $_POST['composeId']);
        }

        # Add custom entry in address book
        if (!empty($_POST['customEmailEntry'])) {
            if (explode(',', str_replace(', ', ',', $_POST['customEmailEntry']))) {
                $customEmails = explode(',', str_replace(', ', ',', $_POST['customEmailEntry']));
            } else {
                $customEmails[] = $_POST['customEmailEntry'];
            }
            foreach ($customEmails as $newEmail) {
                $name = explode('@', $newEmail);
                $fullName = trim(addslashes($name[0]));
                $userEmail = trim(addslashes($newEmail));

                $inAddressBook = $object->selQRYMultiple('full_name', 'pmb_address_book', 'full_name="' . $fullName . '" AND user_email="' . $userEmail . '" AND project_id="' . $_SESSION['idp'] . '"');
                if (!isset($inAddressBook[0]['full_name'])) {
                    $inssertQRY = "INSERT INTO pmb_address_book SET
							project_id = '" . $_SESSION['idp'] . "',
							full_name = '" . $fullName . "',
							user_email = '" . $userEmail . "',
							last_modified_date = NOW(),
							last_modified_by = '" . $_SESSION['ww_builder_id'] . "',
							created_date = NOW(),
							created_by = '" . $_SESSION['ww_builder_id'] . "'";
                    mysql_query($inssertQRY);
                }
            }
        }

        if (isset($_POST['message'])) {
            unset($_POST['message']);
        }
        if (isset($_SESSION[$_SESSION['idp'] . '_emailfile'])) {
            unset($_SESSION[$_SESSION['idp'] . '_emailfile']);
        }
        if (isset($_SESSION[$_SESSION['idp'] . '_orignalFileName'])) {
            unset($_SESSION[$_SESSION['idp'] . '_orignalFileName']);
        }
		if (isset($_SESSION[$_SESSION['idp'] . '_pmbEmailfile'])) {
            unset($_SESSION[$_SESSION['idp'] . '_pmbEmailfile']);
        }
        if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData'])) {
            unset($_SESSION[$_SESSION['idp'] . '_remaimberData']);
        }


        header('Location:?sect=message_details&id=' . $_GET['id'] . '&type=' . $_GET['type']);
        ?>
<script language="javascript" type="text/javascript">window.location.href = "<?php echo '?sect=message_details&id=' . $_GET['id'] . '&type=' . $_GET['type']; ?>";</script>
<?php
        //$_GET['msgid'] = $messgeId;
    } else {
        echo $messageBoard;
    }
}
if (!isset($_GET['attached']) and $_GET['attached'] != 'Y') {
    if (isset($_SESSION[$_SESSION['idp'] . '_emailfile'])) {
        unset($_SESSION[$_SESSION['idp'] . '_emailfile']);
    }
    if (isset($_SESSION[$_SESSION['idp'] . '_orignalFileName'])) {
        unset($_SESSION[$_SESSION['idp'] . '_orignalFileName']);
    }
}

function getattachment($mid) {
    $req1 = mysql_query('select attach_id, name, attachment_name from pmb_attachments where message_id="' . $mid . '" and is_deleted=0');
    $row = mysql_fetch_array($req1);
    return $row;
}

function getuserdetails($id) {
    $req1 = mysql_query('select user_id, user_name, user_fullname, user_email from user where user_id="' . $id . '"');
    $row = mysql_fetch_array($req1);
    return $row;
}

if (sizeof($_GET) == 2 || !isset($_GET['attached'])) {
	unset($_SESSION[$_SESSION['idp'].'_orignalFileName']);
	unset($_SESSION[$_SESSION['idp'].'_emailfile']);
	unset($_SESSION[$_SESSION['idp'].'_remaimberData']);
	unset($_SESSION[$_SESSION['idp'].'_pmbEmailfile']);	
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<link href="css/email.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script type="text/javascript">
$(document).ready(function () {
//Add new Raised by click on save button
$('#sendMessage').click(function () {
    var recipTo = $('#recipToSection .default').val();
    var subject = $('#subject').val();
    var messageType = $('#messageType').val();
    //var messageDetails = $('.nicEdit-main').html();
    var messageDetails = editor.getData();
    var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (recipTo == 'Select recipient') {
        $('#recipToError').show();
        return false;
    } else if (subject == '') {
        $('#emailError').hide();
        $('#recipToError').hide();
        $('#subjectError').show();
        return false;
    } else if (messageType == '') {
        $('#subjectError').hide();
        $('#messageTypeError').show();
        return false;
    } else if (messageDetails == '<br>') {
        $('#messageTypeError').hide();
        $('#messageDetailsError').show();
        return false;
    } else {
        return true;
    }
});
});
</script>
<script type="text/javascript" src="js/thickbox.js"></script>
<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
<link rel="stylesheet" href="css/chosen.css">
<style>
label {
	color: #000000;
}
.textEditer div {
	color: #000;
}
#compose ul li {
	list-style-type: none;
}
h3 {
	border-bottom: 1px solid #CCCCCC;
	color: #000000;
	font-size: 16px;
	margin-left: 10px;
	margin-right: 10px;
	margin-top: 13px;
	padding-bottom: 10px;
}
#imageName {
	float: left;
	margin-left: 10px;
	width: 540px;
}
#imageName span {
	padding-left: 15px;
}
#imageName a {
	cursor: pointer;
	padding-right: 5px;
}
.chzn-container-multi .chzn-choices {
	margin-left: -10px;
	border-radius: 4px;
}

<?php if ($_GET['view'] == 'workflow') {
?>  .list {
	border:1px solid;
	max-height:150px;
	-moz-border-radius:5px;
	border-radius:5px;
	padding:5px;
	overflow:auto;
}
.box1 {
	background: -moz-linear-gradient(center top, #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent;
	border: 1px solid #0261A1;
	color: #000000;
	float: left;
	height: auto;
	width: 211px;
}
.link1 {
	background-image: url("images/blue_arrow.png");
	background-position: 175px 34%;
	background-repeat: no-repeat;
	color: #000000;
	display: block;
	height: 25px;
	text-decoration: none;
	width: 202px;
}
a.link1:hover {
	background-color: #015F9F;
	background-image: url("images/white_arrow.png");
	background-position: 175px 34%;
	background-repeat: no-repeat;
	color: #FFFFFF;
	display: block;
	height: 25px;
	text-decoration: none;
	width: 202px;
}
.txt13 {
	border-bottom: 1px solid #333333;
	color: #000000;
	font-size: 12px;
	font-weight: bold;
	height: 30px;
}
		/* onto CSS now */
		@import url(http://fonts.googleapis.com/css?family=Open+Sans:600);
body {
	padding: 20px;
	background: whiteSmoke;
	font-family: 'Open Sans';
}
#menu {
text-align: center;
}
.nav {
list-style: none;
display: inline-block; /* for centering */
			/*border: 1px solid #b8b8b8;*/
			font-size: 14px;
margin: 0;
padding: 0;
}
.nav li {
border-left: 1px solid #b8b8b8;
float: left;
}
.nav li:first-child {
border-left: 0;
}
.nav a {
color: #2f2f2f;
padding: 0 20px;
line-height: 32px;
display: block;
text-decoration: none;
background: #fbfbfb;
background-image: linear-gradient(#fbfbfb, #f5f5f5);
}
.nav a:hover {
background: #fcfcfd;
background-image: linear-gradient(#fff, #f9f9f9);
}
.nav a.active,  .nav a:active {
background: #94CE06;
/*background-image: linear-gradient(#e8e8e8, #f5f5f5);*/
}


/* Tab Panes now */

#tab_panes {
max-width: 600px;
margin: 20px auto;
}
.tab_pane {
display: none;
}
.tab_pane.active {
display: block;
}
#tab_panes img {
max-width: 600px;
box-shadow: 0 0 5px rgba(0,0,0,0.5);
}
<?php
}
?>

.MailRight .ReadMail {
    border-bottom: 2px dashed #333333;
	margin:auto !important;
	padding:10px !important;
}
.MailRight h3{
	margin:auto !important;
	padding:10px !important;
}

.MailRight .even, .MailRight .even div{
	background:#FFFFFF !important;
}
.MailRight .odd, .MailRight .odd div{
	background:#f9f9f9 !important;
}
.gmail_quote, .y_msg_container, .qtdSeparateBR, .yahoo_quoted {
	display:none !important;
}
.MailLeft a:link, span.MsoHyperlink {
    text-decoration:none !important;
}
.Compose label {
	float: none;
	display: block;
	width: 100%;
}
#inboxData  {
    color: #000;
}
</style>
        <?php if (isset($_GET['view']) && $_GET['view'] == 'workflow') { ?>
        <div id="leftNav" style="width:250px;float:left;">
          <?php include 'side_menu.php'; ?>
        </div>
        <?php } ?>
        <?php
//code for showing tabs
if ($_GET['view'] == 'workflow') {?>
        <nav id="menu" style="margin-top:15px;margin-left:5px;">
          <ul class="nav">
            <li><a href="pms.php?sect=messages&view=workflow" class="active">PMB</a></li>
            <li><a href="pms.php?sect=drawing_register&view=workflow">Drawing Register</a></li>
          </ul>
        </nav>
        <!-- we're done with the tabs, onto panes now -->
        <section id="tab_panes">
          <div class="tab_pane active"> </div>
          <!-- we'll copy/paste the other panes -->
          <div class="tab_pane"> </div>
        </section>
        <?php	}
if ($_GET['view'] == 'workflow') {
	$view = '&view=workflow';
} else {
	$view = '';
}?>
        <div class="GlobalContainer clearfix">
        <?php include 'message_side_menu.php'; ?>
        <div class="MailRight" <?php if ($_GET['view'] == 'workflow') {
                echo "style='width:64%;'";
            } ?>>
        	<a class="green_small" onClick="javascript:history.back(0);" style="float:right; margin:5px;cursor: pointer;">Back</a> <a class="green_small" onClick="printDiv();" style="float:right; margin:5px;cursor: pointer;">Print</a> 
        <!--<a href="?sect=<?php echo (isset($_GET['page']) && $_GET['page'] == 'details_search') ? $_GET['page'] . '&bk=Y' : 'messages'; ?>" onClick="javascript:history.back(0);"><img src="images/back.png" width="79" height="34" alt="Back" style="float:right; margin:5px;" /></a>-->
        <div id="mailBox">
        <h3 style="color:#000000; margin-top:10px; margin-right:80px; float:left; border-bottom:none;" >Project Name :
          <?php
            echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name');
            $refrenceNo = explode(' ', $projectName);
            if (is_array($refrenceNo)) {
                $refrenceNo = strtolower($refrenceNo[0]);
            } else {
                $refrenceNo = strtolower($projectName);
            }
            ?>
        </h3>
        <?php
            $subject = '';
            if (isset($_GET['id'])) {
                $id = base64_decode($_GET['id']);
                $query = "SELECT um.user_id,
							m.title, 
							m.message,
                            m.message_brief,
							m.message_type, 
							m.sent_time, 
							m.sending_type,
							m.cc_email_address, 
							m.to_email_address,
							m.tags,
                            um.from_id,
							um.user_message_id,
							um.type, 
							um.message_id, 
							um.thread_id,
							um.inbox_read,
							um.rfi_number, 
							m.sending_type, 
							m.rfi_status,
							m.company_tag as companyTag,
							m.purchaser_location
					FROM
						pmb_user_message as um,
						pmb_message m
					WHERE
						um.message_id = m.message_id AND
						um.thread_id = '" . $id . "' AND
						m.project_id = '" . $_SESSION['idp'] . "' AND
						um.project_id = '" . $_SESSION['idp'] . "' AND
						um.is_deleted=0
						Group by um.message_id ";
//echo $query;
                $threadquery = mysql_query($query);
                $from_id = '';
                $title = '';
                $messageId = 0;

                $userData = $object->selQRYMultiple('user_name, user_fullname, user_email, pmb_signature', 'user', 'is_deleted = 0 AND user_id = ' . $userId);
                $lupCount = 0;
                $messageListByThreadArr = array();
                $g = 0;
               
               	if (mysql_num_rows($threadquery) >= 1) { $flag = 0;
                    while ($thread = mysql_fetch_array($threadquery)) { 
						$flag = ($flag == 0)?1:0;
                        $messageId = $thread['message_id'];
#print_r($thread);
                        if ($g == 0) {
                            $messageTypeEx = $thread['message_type'];
                            $RFINumber = $thread['rfi_number'];
                        }$g++;
                        $messageListByThreadArr[$thread['message_id']] = $thread['sending_type'];
                        if ($lupCount == 0) {
                            $firstMessageID = $thread['message_id'];
                            $lupCount++;
                        }
                        if ($thread['type'] == 'inbox') {
                            $fromuser = getuserdetails($thread['from_id']);
                            $touser = getuserdetails($thread['user_id']);
                        } else {
                            $fromuser = getuserdetails($thread['user_id']);
                            $touser = getuserdetails($thread['from_id']);
                        }
//	  $attachemnt=getattachment($thread['message_id']);
                        $attachemnts = $object->selQRYMultiple('attach_id, name, attachment_name, is_attached_email', 'pmb_attachments', 'message_id="' . $thread['message_id'] . '" and is_deleted=0');
                        $from_id = $thread['from_id'];
                        $user_id = $thread['user_id'];
                        if ($thread['title'] !== '')
                            $title = $thread['title'];

                        if ($_GET['type'] == "inbox") {
                            $updteQRY = 'UPDATE pmb_user_message SET inbox_read = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = "' . $userId . '" WHERE user_id = "' . $_SESSION['ww_builder_id'] . '" AND thread_id = "' . $thread['thread_id'] . '" AND  type = "inbox"';
                            mysql_query($updteQRY);
                        }

                        if ($subject == '') {
                            $subject = $thread['title'];
                            echo '<h3 style="color:#666666; clear:both;">' . $thread['title'] . '</h3>';
                        }
                        ?>
                <!--div id='ReadMail'>
                  <div id='ReadMail1'-->
                  <?php if(explode("_", $thread['message_type'])){
							$folderType = end(explode("_", $thread['message_type']));
						}else{
							$folderType = $thread['message_type'];
						}
				  ?>
                    <div class="ReadMail <?php echo ($flag == 0)?'even':'odd'; ?>" style="color:#333333;">
                      <div class="ReadMailProperties"> <?php echo "<p style='color:#000;'><b>Ref. #: </b>" . $thread['rfi_number'] . "</p>"; ?> <strong><?php echo $fromuser['user_fullname']; ?></strong> <?php echo isset($thread['message_type']) ? $folderType : ''; ?> to
                        <?php
							$query = "SELECT from_id FROM pmb_user_message WHERE message_id = " . $thread['message_id'] . " AND is_deleted = 0 AND type = 'sent' AND is_cc_user = 0";
							$getUserList = mysql_query($query);
							if (isset($getUserList)) {
								$multipleUser = '';
								while ($userId = mysql_fetch_array($getUserList)) {
									if ($userId['from_id'] != 0) {
										$result = getuserdetails($userId['from_id']);
										if ($multipleUser == '') {
											$multipleUser.= $result['user_fullname'];
										} else {
											$multipleUser.= ', ' . $result['user_fullname'];
										}
									}
								}
							}
							if ($thread['to_email_address'] != '') {
								if ($multipleUser != "")
									$multipleUser = $multipleUser . ', ' . $thread['to_email_address'];
								else
									$multipleUser = $thread['to_email_address'];
							}
							if ($thread['cc_email_address'] != '') {
								$multipleUser = $multipleUser . ' <br /> CC : ' . $thread['cc_email_address'];
							}
							echo $multipleUser;
							?>
                            <ul>
                              <li>
                            <?php
                                 echo date('d/m/Y H:i:s', strtotime($thread['sent_time']));
        #				if($_SESSION['ww_builder']['user_type'] == "manager"){
                                 echo "<a href='?sect=forward&folderType=".$thread['message_type']."&msgid=" . $thread['message_id'] . $view . "' style='color:#000; font-weight:bold;'>Forward 						<img src='images/foeward.png' style='margin-left:10px;'></a>";
        #				}
                            ?>
	                          </li>
    	                    </ul>
                      </div>
                      <?php
						if ($thread['message_type'] == "Purchaser Changes") {
							echo "<p><b>Purchaser Location : </b>" . $thread['purchaser_location'] . "</p>";
						}
						if(!empty($thread['tags'])){
							echo "<p><b>Tags : </b>" . $thread['tags'] . "</p>";
						}
						if(!empty($thread['companyTag'])){
							echo "<p><b>Company Tag : </b>" . $thread['companyTag'] . "</p>";
						}

						if ($thread['message_type'] == "Request For Information" && $_SESSION['ww_builder']['user_type'] == "manager" && $thread['sending_type'] == 'original') { 	?>
                            <label for="To">RFI status :</label>
                            <select name="RFIstatus" id="RFIstatus" style="margin-left:10px;">
						 <?php $rfiStatusArr = array('Open', 'Pending', 'Fixed', 'Closed');
                                foreach ($rfiStatusArr as $key => $statusValue) {
                          ?>
                                    <option  value="<?= $statusValue ?>"
                                        <?php if ($thread['rfi_status'] == $statusValue) echo 'selected="selected"'; ?> >
                                        <?= $statusValue ?>
                                    </option>
                        <?php } ?>
                            </select> &nbsp;&nbsp;&nbsp;&nbsp; 
				            <img src="images/pmb_rfiStatus_update.png" class="" id="" onClick="updateStatus4RFI(<?= $thread['message_id'] ?>)" align="absbottom" /> <br clear="all">
           		<?php   }
						//New Update Status Dated : 28/10/2013 
						$strSearch = array('\n', '\r', '&lt;p&gt;&lt;/p&gt;', '&lt;p&gt; &lt;/p&gt;', '&lt;p&gt;  &lt;/p&gt;');
						$emailMessagesBrief = html_entity_decode(str_replace($strSearch, '', $thread['message_brief']));
						$emailMessages = html_entity_decode(str_replace($strSearch, '', $thread['message']));
						$doc = new DOMDocument();
						$doc->loadHTML($emailMessages);
						$emailMessages = $doc->saveHTML();
						echo '<div><div class="messageReadLess">'.$emailMessagesBrief.'</div>';
						echo '<div class="messageReadMore">'.$emailMessages.'</div></div>';
						//echo '<a href="javascript:void(0)" class="readMoreLink">read more</a>';
						#$userImage = $object->selQRYMultiple('user_id, user_signature', 'user', 'is_deleted  = 0 AND user_id = ' . $_SESSION['ww_builder_id'] . '');
						#echo "<img src='" . IMG_SRC . "user_images/" . $userImage[0]['user_signature'] . "'>";
                            
					if (!empty($attachemnts)) {
						$i = 0;
						foreach ($attachemnts as $attachemnt) {
							if ($attachemnt['is_attached_email'] == 1) {
								$type = explode('.', $attachemnt['attachment_name']);
								$type = end($type);
								if($type =='zip' || $type =='exe'){

								}else{
									if ($i == 0) {
										$i++;
										if (count($attachemnts) == 1) {
											echo '<br> <b>Attachment — </b> [ <a href="javascript:void(0);" style="color:#06C;" onClick="downloadSelectedFiles(' . $thread['message_id'] . ', \'' . $thread['title'] . '\');">Download all attachments</a> ]';
										} else
										if (count($attachemnts) > 1) {
											echo '<br> <b>Attachments — </b> [ <a href="javascript:void(0);" style="color:#06C;" onClick="downloadSelectedFiles(' . $thread['message_id'] . ', \'' . $thread['title'] . '\');">Download all attachments</a> ]';
										}
									}
								//$ext = end(explode('.', $attachemnt['name']));
								//echo 'Ar<pre>';print_r($ext);
								?>
									<p> <img src="images/attchment.png" width="16" height="14" />&nbsp;&nbsp;<?php echo $attachemnt['name']; ?>&nbsp;&nbsp;
							<?php	if (strpos('JPEG |jpeg |JPG |jpg |PNG |png |GIF |gif', $type) > 0) {    ?>
										[ <a href="pmb_attachment/<?php echo $_SESSION['idp'] . '/' . $attachemnt['attachment_name']; ?>" target="_blank" style="color:#06C;" class="thickbox" >View</a>] &nbsp;&nbsp;
							<?php	} else { ?>
										[ <a href="pmb_attachment/<?php echo $_SESSION['idp'] . '/' . $attachemnt['attachment_name']; ?>" target="_blank" style="color:#06C;" >View</a>] &nbsp;&nbsp;
							<?php	} ?>
					<?php
								}
							} else { //echo 'IMI';
								//$ext = end(explode('.', $attachemnt['name']));
								//echo 'Ar<pre>';print_r($ext);
								$type = explode('.', $attachemnt['attachment_name']);
								$type = end($type);
								if($type =='zip' || $type =='exe'){

								}else{
									if ($i == 0) {
										$i++;
										if (count($attachemnts) == 1) {
											echo '<br> <b>Attachment — </b> [ <a href="javascript:void(0);" style="color:#06C;" onClick="downloadSelectedFiles(' . $thread['message_id'] . ', \'' . $thread['title'] . '\');">Download all attachments</a> ]';
										} else
										if (count($attachemnts) > 1) {
											echo '<br> <b>Attachments — </b> [ <a href="javascript:void(0);" style="color:#06C;" onClick="downloadSelectedFiles(' . $thread['message_id'] . ', \'' . $thread['title'] . '\');">Download all attachments</a> ]';
										}
									}
								?>
									<p> <img src="images/attchment.png" width="16" height="14" />&nbsp;&nbsp;<?php echo $attachemnt['name']; ?>&nbsp;&nbsp;
							<?php	if (strpos('JPEG |jpeg |JPG |jpg |PNG |png |GIF |gif', $type) > 0) {	 ?>
										[ <a href="attachment/<?php echo $attachemnt['attachment_name']; ?>" target="_blank" style="color:#06C;" class="thickbox" >View</a>] &nbsp;&nbsp;
							<?php	} else { ?>
										[ <a href="attachment/<?php echo $attachemnt['attachment_name']; ?>" target="_blank" style="color:#06C;" >View</a>] &nbsp;&nbsp;
							<?php	} ?>
										[ <a href="download.php?attachment=<?php echo $attachemnt['attach_id']; ?>" target="_blank" style="color:#06C;">Download</a> ] </p>
	  <?php 					}
							}
						}
					}
					?>
            </div>
            <?php } ?>
          
          <?php //echo "</div>";
                    if($messageTypeEx=='Request For Information'){
						//echo "</div>";
					}
               ?>
          </div>
          <h3 style="color:#000; margin-left:10px;"><img src="images/reply.png" style="margin-right:10px;">Reply</h3>
          <div class="Compose clearfix">
            <?php
				$comMessData = array();
				$toList = array();
				$ccList = array();
				krsort($messageListByThreadArr);
				$flag = 0;
				if (isset($messageId) && $messageId != 0) {
					foreach ($messageListByThreadArr as $key => $sendType) {
						$comMessData = $object->selQRYMultiple('m.message_id, um.user_id, um.type, m.title, m.message_id, m.sent_time, m.message, um.from_id, m.message_type, m.to_email_address, m.cc_email_address, m.tags', 'pmb_user_message um , pmb_message m', 'm.message_id="' . $key . '" AND um.message_id = m.message_id AND um.type="' . $_GET['type'] . '" AND is_cc_user = 0');
						foreach ($comMessData as $val) {
							$toList[] = $val['from_id'];
							$toList[] = $val['user_id'];
						}

						if (!empty($comMessData[0]['to_email_address'])) {
							if (explode(',', str_replace(', ', ',', $comMessData[0]['to_email_address']))) {
								$toExtraList = explode(',', str_replace(', ', ',', $comMessData[0]['to_email_address']));
							} else {
								$toExtraList[] = $comMessData[0]['to_email_address'];
							}
							$toList = array_merge($toList, $toExtraList);
						}

						if (!empty($comMessData[0]['cc_email_address'])) {
							if (explode(',', str_replace(', ', ',', $comMessData[0]['cc_email_address']))) {
								$ccList = explode(',', str_replace(', ', ',', $comMessData[0]['cc_email_address']));
							} else {
								$ccList[] = $comMessData[0]['cc_email_address'];
							}
						}
						if ($sendType == 'reply' || $sendType == "original") {
							break;
						}
					}
				}
				
				$projectUsers = $object->selQRYMultiple('u.user_id, u.user_fullname, u.company_name, user_email, up.map_user_id, up.map_with', 'user as u Left Join user_projects as up on u.user_id = up.user_id  and up.is_deleted=0', 'u.user_id!="' . $_SESSION['ww_builder_id'] . '" AND u.is_deleted=0 AND up.project_id="' . $_SESSION['idp'] . '" order by u.user_name');

				$projectIssues = $object->selQRYMultiple('issue_to_id, issue_to_name, company_name, issue_to_email ', 'inspection_issue_to', 'is_deleted=0 AND project_id="' . $_SESSION['idp'] . '" AND issue_to_name!="NA" AND issue_to_email!="" order by issue_to_name');

				$projectAddresBookUsers = $object->selQRYMultiple('id, full_name, company_name, user_email', 'pmb_address_book', 'is_deleted=0 AND project_id="' . $_SESSION['idp'] . '" AND full_name != "" order by full_name');
				$mapedIssuedTo = array();
				$mapedAddressBook = array();
				?>
            <form action="" method="post" enctype="multipart/form-data" id="compose" name="compose">
              <ul>
                <li>
                  <label for="To">To:</label>
                  <select name="recipTo[]" id="recipTo" style="width:350px;" multiple class="chzn-select chzn-custom-value" multiple >
                  <optgroup label="Project users">
                  <?php
                                            foreach ($projectUsers as $puser) {
                                                if ($puser['map_user_id'] > 0 && $puser['map_with'] == "addressbook") {
                                                    $mapedAddressBook[] = $puser['map_user_id'];
                                                }
                                                if ($puser['map_user_id'] > 0 && $puser['map_with'] == "issuedto") {
                                                    $mapedIssuedTo[] = $puser['map_user_id'];
                                                }
                                                $select = "";
                                                if (in_array($puser['user_id'], $toList)) {
                                                    $select = "selected = 'selected'";
                                                    if (($key = array_search($puser['user_id'], $toList)) !== false)
                                                        unset($toList[$key]);
                                                }
                                                if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && in_array($puser['user_id'], $_SESSION[$_SESSION['idp'] . '_remaimberData']['recipTo'])) {
                                                    $select = "selected = 'selected'";
                                                }
                                                ?>
                  <option value="<?php echo $puser['user_id']; ?>" <?php echo $select; ?>>
                  <?php
                                                if (!empty($puser['user_fullname']) && !empty($puser['company_name'])) {
                                                    echo strtolower($puser['user_fullname'] . " ( " . $puser['company_name'] . " )");
                                                } elseif (empty($puser['user_fullname']) && !empty($puser['company_name'])) {
                                                    echo strtolower("( " . $puser['company_name'] . " )");
                                                } else {
                                                    echo strtolower($puser['user_fullname']);
                                                }
                                                ?>
                  </option>
                  <?php } ?>
                  </optgroup>
                  <optgroup label="Issued To">
                  <?php
                                                foreach ($projectIssues as $pIssue) {
                                                    $select = "";
                                                    if (in_array($pIssue['issue_to_email'], $toList)) {
                                                        $select = "selected = 'selected'";
                                                        if (($key = array_search($pIssue['issue_to_email'], $toList)) !== false)
                                                            unset($toList[$key]);
                                                    }
                                                    if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && in_array($pIssue['issue_to_email'], $_SESSION[$_SESSION['idp'] . '_remaimberData']['recipTo'])) {
                                                        $select = "selected = 'selected'";
                                                    }
                                                    if (!in_array($pIssue['issue_to_id'], $mapedIssuedTo)) {
                                                        ?>
                  <option value="<?php echo $pIssue['issue_to_email']; ?>" <?php echo $select; ?>>
                  <?php
                                                    if (!empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])) {
                                                        echo strtolower($pIssue['company_name'] . " ( " . $pIssue['issue_to_name'] . " )");
                                                    } elseif (empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])) {
                                                        echo strtolower("( " . $pIssue['issue_to_name'] . " )");
                                                    } else {
                                                        echo strtolower($pIssue['company_name']);
                                                    }
                                                    ?>
                  </option>
                  <?php }
                            } ?>
                  </optgroup>
                  <optgroup label="Ad hoc (External)">
                  <?php
                                                foreach ($projectAddresBookUsers as $addresBookUsers) {
                                                    $select = "";
                                                    if (in_array($addresBookUsers['user_email'], $toList)) {
                                                        $select = "selected = 'selected'";
                                                        if (($key = array_search($addresBookUsers['user_email'], $toList)) !== false)
                                                            unset($toList[$key]);
                                                    }
                                                    if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && in_array($addresBookUsers['user_email'], $_SESSION[$_SESSION['idp'] . '_remaimberData']['recipTo'])) {
                                                        $select = "selected = 'selected'";
                                                    }
                                                    if (!in_array($addresBookUsers['id'], $mapedAddressBook)) {
                                                        ?>
                  <option value="<?php echo strtolower($addresBookUsers['user_email']); ?>" <?php echo $select; ?>>
                  <?php
                                                    if (!empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])) {
                                                        echo strtolower($addresBookUsers['full_name'] . " ( " . $addresBookUsers['company_name'] . " )");
                                                    } elseif (empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])) {
                                                        echo strtolower("( " . $addresBookUsers['company_name'] . " )");
                                                    } else {
                                                        echo strtolower($addresBookUsers['full_name']);
                                                    }
                                                    ?>
                  </option>
                  <?php }
                                            }
                                            ?>
                  </optgroup>
                  </select>
                  <div class="error-edit-profile" style="display:none;"  id="recipToError">The to field is required.</div>
                  <div class="error-edit-profile" style="display:none;"  id="emailError">Invalide email format.</div>
                </li>
                <li>
                  <label for="CC">CC:</label>
                  <select name="recipCC[]" id="recipCC" style="width:350px;" multiple class="chzn-select chzn-custom-value" multiple >
                  <optgroup label="Project users">
                  <?php
        foreach ($projectUsers as $puser) {
            $select = "";
            if (in_array($puser['user_email'], $ccList)) {
                $select = "selected = 'selected'";
                if (($key = array_search($puser['user_email'], $ccList)) !== false)
                    unset($ccList[$key]);
            }
            if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && in_array($puser['user_email'], $_SESSION[$_SESSION['idp'] . '_remaimberData']['recipCC'])) {
                $select = "selected = 'selected'";
            }
            ?>
                  <option value="<?php echo $puser['user_email']; ?>" <?php echo $select; ?>>
                  <?php
            if (!empty($puser['user_fullname']) && !empty($puser['company_name'])) {
                echo strtolower($puser['user_fullname'] . " ( " . $puser['company_name'] . " )");
            } elseif (empty($puser['user_fullname']) && !empty($puser['company_name'])) {
                echo strtolower("( " . $puser['company_name'] . " )");
            } else {
                echo strtolower($puser['user_fullname']);
            }
            ?>
                  </option>
                  <?php } ?>
                  </optgroup>
                  <optgroup label="Issued To">
                  <?php
                                        foreach ($projectIssues as $pIssue) {
                                            $select = "";
                                            if (in_array($pIssue['issue_to_email'], $ccList)) {
                                                $select = "selected = 'selected'";
                                                if (($key = array_search($pIssue['issue_to_email'], $ccList)) !== false)
                                                    unset($ccList[$key]);
                                            }
                                            if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && in_array($pIssue['issue_to_email'], $_SESSION[$_SESSION['idp'] . '_remaimberData']['recipCC'])) {
                                                $select = "selected = 'selected'";
                                            }
                                            if (!in_array($pIssue['issue_to_id'], $mapedIssuedTo)) {
                                                ?>
                  <option value="<?php echo $pIssue['issue_to_email']; ?>" <?php echo $select; ?>>
                  <?php
                                                if (!empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])) {
                                                    echo strtolower($pIssue['company_name'] . " ( " . $pIssue['issue_to_name'] . " )");
                                                } elseif (empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])) {
                                                    echo strtolower(" ( " . $pIssue['issue_to_name'] . " )");
                                                } else {
                                                    echo strtolower($pIssue['company_name']);
                                                }
                                                ?>
                  </option>
                  <?php }
                                        } ?>
                  </optgroup>
                  <optgroup label="Ad hoc (External)">
                  <?php
                    foreach ($projectAddresBookUsers as $addresBookUsers) {
                        $select = "";
                        if (in_array($addresBookUsers['user_email'], $ccList)) {
                            $select = "selected = 'selected'";
                            if (($key = array_search($addresBookUsers['user_email'], $ccList)) !== false)
                                unset($ccList[$key]);
                        }
                        if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && in_array($addresBookUsers['user_email'], $_SESSION[$_SESSION['idp'] . '_remaimberData']['recipCC'])) {
                            $select = "selected = 'selected'";
                        }
                        if (!in_array($addresBookUsers['id'], $mapedAddressBook)) {
                            ?>
                  <option value="<?php echo strtolower($addresBookUsers['user_email']); ?>" <?php echo $select; ?>>
                  <?php
                        if (!empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])) {
                            echo strtolower($addresBookUsers['full_name'] . " ( " . $addresBookUsers['company_name'] . " )");
                        } elseif (empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])) {
                            echo strtolower("( " . $addresBookUsers['company_name'] . " )");
                        } else {
                            echo strtolower($addresBookUsers['full_name']);
                        }
                        ?>
                  </option>
                  <?php }
                }
                ?>
                  </optgroup>
                  </select>
                </li>
                <li class="textEditer">
                  <label for="message">Message:</label>
                  <div style="width: 50%;margin-right: 50%">
                  <textarea name="message"  id="message" style="height:110px;"><?php echo htmlentities($_POST['message'], ENT_QUOTES, 'UTF-8'); ?><?php if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && $_SESSION[$_SESSION['idp'] . '_remaimberData']['messageDetail'] != "") {
                    echo htmlentities($_SESSION[$_SESSION['idp'] . '_remaimberData']['messageDetail'], ENT_QUOTES, 'UTF-8');
                } else { ?><?php
                    if ($userData[0]['pmb_signature'] != "") {
                        echo '<br><br><br><input type="hidden">
						--<br>' . $userData[0]['pmb_signature'];
                    }
                }
                $userImage = $object->selQRYMultiple('user_id, user_signature', 'user', 'is_deleted  = 0 AND user_id = '.$_SESSION['ww_builder_id'].'');
				echo  "<img src='".IMG_SRC."user_images/".$userImage[0]['user_signature']."'>";
                ?>
                    </textarea>
                  <div class="error-edit-profile" style="display:none;"  id="messageDetailsError">The message field is required.</div>
                  <input type="hidden" id="isEditor" value="false" />
                  </div>
                </li>
                <li>
                  <label>Upload a file:</label>
                    <div style="overflow:hidden;cursor:pointer;width:36px;height:36px;float:left;color:#0000AA;margin-left:15px;">
                                        <img src="images/compose_attachment.png" name="newFileToUpload" id="newFileToUpload" title="Select New File" onClick="addAttachment();" />
                                        <!--input type="file" style="opacity: 0;width:40px;height:40px;margin-top: -40px;margin-left: 0px;font-size:35px;cursor: pointer !important;" id="attachment1" title="Select New File" name="attachment1" -->
                    </div>
                    <img src="images/attach-dr.png" name="chooseFormDR" id="chooseFormDR" title="Choose From Document Register" onClick="showDocumentRegisterFiles();" style="float:left;cursor:pointer;" />

                    <img src="images/add_pmb.png" name="attachEmail" id="attachEmail" title="Add a PMB Message" onClick="attachEmails();" style="margin-left:5px;float:left;cursor:pointer;" />

                    <img src="images/add_email.png" name="attachEmail" id="attachEmailNew" title="Add Email" onClick="addEmails();" style="margin-left:5px;float:left;cursor:pointer;" />






                  <!-- <div style="overflow:hidden;cursor:pointer;width:36px;height:36px;float:left;color:#0000AA;margin-left:15px;"> <img src="images/compose_attachment.png" name="newFileToUpload" id="newFileToUpload" title="Select New File" onClick="addAttachment();" /> 
                    <img src="images/compose_attachment.png" name="newFileToUpload" id="newFileToUpload" title="Select New File" />
                                        <input type="file" style="opacity: 0;width:40px;height:40px;margin-top: -40px;margin-left: 0px;font-size:35px;cursor: pointer !important;" id="attachment1" title="Select New File" name="attachment1">
                                         
                  </div>
                  <img src="images/compose_document.png" name="chooseFormDR" id="chooseFormDR" title="Choose From Document Register" onClick="showDocumentRegisterFiles();" style="float:left;cursor:pointer;" /> --><br><br>
                  <div id="imageName" >
                    <?php
                                    if (isset($_SESSION[$_SESSION['idp'] . '_pmbEmailfile'])) {
                                           foreach ($_SESSION[$_SESSION['idp'] . '_pmbEmailfile'] as $key => $val) {
                                    
                                                echo ' <span id="' . $key . '"><a style="color:#06C;" target="_blank" href="attachment/' . $val . '">' . $_SESSION[$_SESSION['idp'] . '_pmbEmailfile'][$key] . '</a>[<a style="color:red;" onclick="removePMBFiles(' . $key . ');">X</a>]</span>';
                                            }
                                        }
                                       // print_r($_SESSION[$_SESSION['idp'] . '_emailfile']);
                                    if (isset($_SESSION[$_SESSION['idp'] . '_emailfile'])) {
                                        $i = 0;
                                        foreach ($_SESSION[$_SESSION['idp'] . '_emailfile'] as $key => $val)
                                        {
                                            $i++;

                                                //echo ' <span id="' . $i . '"><a style="color:#06C;" target="_blank" href="attachment/' . $val . '">' . $_SESSION[$_SESSION['idp'] . '_orignalFileName'][$key] . '</a>[<a style="color:red;" onclick="removeMaessage(' . $i . ', 0, \'' . $val . '\');">X</a>]</span>';

                                            echo ' <span id="' . $i . '"><a style="color:#06C;" target="_blank" href="attachment/' . $val . '">' . $_SESSION[$_SESSION['idp'] . '_orignalFileName'][$key] . '</a>[<a style="color:red;" onclick="removeMaessage(' . $i . ',0);">X</a>]</span>';
                                        }
                                    }
                                    ?>
                  </div>
                  <br style="clear:both; "/>
                </li>
                <li id="lastTd">
                  <label>&nbsp;</label>
                  <input type="hidden" name="customEmailEntry" id="customEmailEntry" value="">
                  <input type="hidden" name="removeAttachment" id="removeAttachment" value="">
                  <input type="hidden" name="emailAttachedAjax" id="emailAttachedAjax" value="0" />
                  <input type="hidden" name="refrenceNumber" value="<?= $refrenceNo; ?>">
                  <input type="hidden" name="messageType" value="<?= $messageTypeEx; ?>">
                  <input type="hidden" name="rfiNumber" value="<?= $RFINumber; ?>">
                  <input type="hidden" name="to_id" value="<?php echo ($user_id != $userId) ? $user_id : $from_id; ?>" />
                  <input type="hidden" name="title" value="<?php echo trim(str_replace('RE :', '', $title)); ?>" />
                  <input type="hidden" name="thread_id" value="<?php echo $id ?>" />
                  <input type="hidden" name="updateMessageID" value="<?php echo $firstMessageID; ?>" />
                  <input type="hidden" name="submit" value="add">
                  <button type="submit" class="right" id="sendMessage" style="margin-left:400px;">Send </button>
                </li>
              </ul>
            </form>
          </div> <br><br><br><br>
		 <?php	} else { ?>
                  <div class="message" style="color:#333333; font-size:16px;">No thread Found.<br />
                  </div>
                  <?php //die;
           		}
		    } ?>
        </div>
<script src="js/jquery.min.for.choosen.js" type="text/javascript"></script> 
<script src="js/chosen.jquery.js" type="text/javascript"></script> 
<script type="text/javascript">
	var align = 'center';
	var top1 = 100;
	var width = 800;
	var padding = 10;
	var backgroundColor = '#FFFFFF';
	var borderColor = '#333333';
	var borderWeight = 4;
	var borderRadius = 5;
	var fadeOutTime = 300;
	var disableColor = '#666666';
	var disableOpacity = 40;
	var loadingImage = 'images/loadingAnimation.gif';
	
	var config = {
		'.chzn-select': {},
		'.chzn-select-deselect': {allow_single_deselect: false},
		'.chzn-select-width': {width: "95%"}
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}

    var topModal = 100;
    var width = 900;

    function attachEmails() {
        console.log('attachEmails');
        var messageType = '<?php echo (isset($_GET['folderType']) && $_GET['folderType'] != "") ? $_GET['folderType'] : 'General Correspondence'; ?>';
        modalPopup(align, topModal, 890, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pmb_display_emails.php?name=' + Math.random() + '&folderType=' + messageType, loadingImage, function () {
            loadData(<?= $_SESSION['idp'] ?>, messageType);
        });
        goTop();
    }

    var oTable;

    function loadData(projectID, messageType) {
        console.log(projectID, messageType);
        oTable = $('#inboxData').dataTable({
            "iDisplayLength": 100,
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "bProcessing": true,
            "bServerSide": true,
            "bRetrieve": true,
            "sAjaxSource": "show_inbox_data_by_ajax.php?reqfrom=ajax&name=" + Math.random() + "&folderType=" + messageType,
            "bStateSave": true,
            "aoColumnDefs": [{"bVisible": false, "aTargets": [0]}]
                    /*"aoColumns": [
                     {"sType": "html"},
                     {"sType": "html"},
                     {"sType": "html"},
                     {"sType": "html"},
                     {"sType": "html"},
                     {"sType": "html"}
                     ]*/
        });
    }


    $('#ajaxmessageType').live("change", function () {
        oTable.fnDestroy();
        loadData(<?= $_SESSION['idp'] ?>, $(this).val());
        
    });

    function addEmails() {
                modalPopup(align, top1, 1000, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_compose_email.php?&name=' + Math.random(), loadingImage, bulkRegistration);
                goTop();
    }
    
    function bulkRegistration() {
        var config = {
            support: ",message/rfc822,application/octet-stream, application/vnd.ms-outlook", // Valid file formats
            form: "addDrawingForm", // Form ID
            dragArea: "innerDiv", // Upload Area ID
            uploadUrl: "add_compose_email.php"// +new Date().getTime()Server side upload url
        }
        mappingDocumentArr = {};
        mappedDocArr = {};
        initBulkUploader(config);
    }

	function addAttachment() {
		goTop();
		modalPopup(align, top1, 1000, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_attachment_email.php?&name=' + Math.random(), loadingImage, addMultipleAttachment);
	}
/*	function addAttachment() {
		modalPopup(align, top1, 1000, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_attachment_email.php?&name=' + Math.random(), loadingImage, addMultipleAttachment);
	}*/
	
	function addMultipleAttachment() {
		var config = {
			support: ",message/rfc822,application/octet-stream, application/vnd.ms-outlook", // Valid file formats
			form: "frmAddAttachment", // Form ID
			dragArea: "innerDiv", // Upload Area ID
			uploadUrl: "add_attachment_email.php?antiqueID="+ Math.random()// +new Date().getTime()Server side upload url
		}
		mappingDocumentArr = {};
		mappedDocArr = {};
		initMultipleAttachment(config);
	}

	function removeMultipleAttachment(id) {
		for (var key in testFxArr) {
			if (testFxArr[key] == '0') {
				testFxArr.splice(0, 1);
			}
		}
		var testFxArr = new Array();
		console.log(testFxArr);
		$('.divId_' + id).remove();
		tempId = 0;
		this.all = [];
		self.all = [];
		//File count decrement
		var fCount = $("#addAttachmentCount").val();
		if(fCount > 0) {
			fCount = parseInt(fCount) - 1;
			$("#addAttachmentCount").val(fCount);
			$("#fileCount").html(fCount +' files selected');
		}
	}                                  
</script>
<?php if (isset($_GET['attached']) and $_GET['attached'] == 'Y') { ?>
<script type="text/javascript">
		$(document).ready(function () {
			$('#emailAttachedAjax').val(1);
		});
	</script>
<?php } ?>
<!--script type="text/javascript" src="js/nicEdit.js"></script-->
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script> 
<script type="text/javascript">
	var align = 'center';
	var top1 = 100;
	var width = 800;
	var padding = 10;
	var backgroundColor = '#FFFFFF';
	var borderColor = '#333333';
	var borderWeight = 4;
	var borderRadius = 5;
	var fadeOutTime = 300;
	var disableColor = '#666666';
	var disableOpacity = 40;
	var loadingImage = 'images/loadingAnimation.gif';
	
	/*bkLib.onDomLoaded(function () {
		new nicEditor({iconsPath: 'js/nicEditorIcons.gif', buttonList: ['save', 'bold', 'italic', 'underline', 'left', 'center', 'right', 'justify', 'ol', 'ul', 'indent', 'outdent', 'forecolor', 'bgcolor']}).panelInstance('message');
	});*/
	
	/*======== CKEDITOR ==========*/
	var editor = CKEDITOR.replace( 'message', {
		uiColor: '#F7F7F7',
		toolbar: [
			[ 'Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', '-', "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock"],
			[ 'FontSize', 'TextColor', 'BGColor' ]
		]
	});
	/*======== CKEDITOR ==========*/
	
	$(document).ready(function () {
	
		var isFormSubmit = 0;
		//Add new Raised by click on save button
		$('#sendMessage').click(function () {
			var recipTo = $('#recipToSection .default').val();
			var subject = $('#subject').val();
			var messageType = $('#messageType').val();
			//var messageDetails = $('.nicEdit-main').html();
			var messageDetails = editor.getData();
			var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			if (recipTo == 'Select recipient') {
				$('#recipToError').show();
				return false;
			} else if (subject == '') {
				$('#emailError').hide();
				$('#recipToError').hide();
				$('#subjectError').show();
				return false;
			} else if (messageType == '') {
				$('#subjectError').hide();
				$('#messageTypeError').show();
				return false;
			} else if (messageDetails == '<br>') {
				$('#messageTypeError').hide();
				$('#messageDetailsError').show();
				return false;
			} else {
				showProgress();
				if (isFormSubmit == 0) {
					isFormSubmit = 1;
					return true;
				}
			}
		});
		var attachNo = <?php echo isset($attachemnts) ? count($attachemnts) : 0; ?>;
		var btnUpload = $('#attachment1');
		var btnUploadCurr = btnUpload[btnUpload.length - 1];
		var pdfID = 0;
		new AjaxUpload(btnUploadCurr, {
			action: 'auto_file_upload.php?action=emailAttachment&pdfID=' + pdfID + '&uniqueID=' + Math.random(),
			name: 'attachment1',
			onSubmit: function (file, ext) {
				showProgress();
			},
			onComplete: function (file, fileName) {
				hideProgress();
				console.log(file, fileName);
				var jsonResult = JSON.parse(fileName);
				if (jsonResult.status) {
					attachNo++;
					$('#emailAttachedAjax').val(1);
					$('#imageName').append(' <span id="' + attachNo + '"><a href="attachment/' + jsonResult.filePath + '" target="_blank" style="color:#06C;" class="thickbox" >' + jsonResult.imageName + '</a>[<a onclick="removeMaessage(' + attachNo + ', 0);" style="color:red;">X</a>]</span>');
					tb_init('a.thickbox, area.thickbox, input.thickbox');//pass where to apply thickbox
					imgLoader = new Image();// preload image
					imgLoader.src = tb_pathToImage;
				} else {
					jAlert('Error in file uploading please try again');
				}
			}
		});

		//Add new Raised by click on save button
		$('#sendMessage').click(function () {
			//var messageDetails = $('.nicEdit-main').html();
			var messageDetails = editor.getData();
			if (messageDetails == '<br>') {
				$('#messageTypeError').hide();
				$('#messageDetailsError').show();
				return false;
			} else {
				return true;
			}
		});

	});

	function removeFile(file, reference, filename, flag) {
		r = jConfirm('Do you really want to delete this file?', null, function (r) {
			if (r === true) {
				$('#' + flag).remove();
				$('#filesArr_' + flag).remove();
				$('#div_' + flag).remove();
				if ($('#external_email').prop('checked') == true) {
					$('#attachEmailNew').show();
				}
			}
		});
	}

	function removeMaessage(id, attachId, fileNameId) {
		$("#" + id).remove();
		var removeAttachId = $("#removeAttachment").val();
		if (fileNameId != "" && isNaN(fileNameId) && attachId == 0) {
			if (removeAttachId == "")
				$("#removeAttachment").val(fileNameId);
			else
				$("#removeAttachment").val(removeAttachId + ',' + fileNameId);
		} else if (attachId != 0 && !isNaN(fileNameId)) {
			if (removeAttachId == "")
				$("#removeAttachment").val(attachId);
			else
				$("#removeAttachment").val(removeAttachId + ',' + attachId);
		}
		console.log('Finally Done ====' + $("#removeAttachment").val());
	}

	/*function removeMaessage(id, attachId) {
		$("#" + id).remove();
		var removeAttachId = $("#removeAttachment").val();
		if (attachId != 0) {
			if (removeAttachId == "") {
				$("#removeAttachment").val(attachId);
			} else {
				$("#removeAttachment").val(removeAttachId + ',' + attachId);
			}
		}
	}*/

	function downloadSelectedFiles(messageID, messageTitle) {
		//console.log(messageID, messageTitle);
		modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'download_document_register_files.php?messageTitle=' + messageTitle + '&messageID=' + messageID + '&singleId=' + Math.random(), loadingImage);
	}

	function showDocumentRegisterFiles() {		
		//var input = $("<input>").attr("type", "hidden").attr("name", "messageDetail").val($('.nicEdit-main').html());
		var input = $("<input>").attr("type", "hidden").attr("name", "messageDetail").val(editor.getData());
		$('#compose').append($(input));
		$.post("copy_file_toAttach_folder.php?antiqueId=" + Math.random(), $('#compose').serialize()).done(function (data) {
			var jsonResult = JSON.parse(data);
			if (jsonResult.status) {
				console.log(jsonResult.dataArr);
				window.location.href = "?sect=drawing_register_select&page=message_details&id=<?php echo $_GET['id']; ?>&type=<?php echo $_GET['type']; ?>";
			}
		});
	}
	function updateStatus4RFI(msgID) {
		var selRFIstatus = $('#RFIstatus').val();
		jConfirm('Are you sure you wish to ' + selRFIstatus + ' this RFI?', null, function (r) {
			if (r === true) {
				showProgress();
				$.post('update_rfi_status.php?antiqueID=' + Math.random(), {RFIstatus: selRFIstatus, msgID: msgID, modifiedID:<?= $_SESSION['ww_builder_id'] ?>}).done(function (data) {
					hideProgress();
					var jsonResult = JSON.parse(data);
					if (jsonResult.status) {
						jAlert(jsonResult.msg);
					} else {
						jAlert('Data updation failed, try again later');
					}
				});
			}
		});

	}
	function printDiv(){
		var divToPrint = $('#mailBox').html(); 
		console.log(divToPrint);
	 //	var newWin = window.open('', 'PrintWindow', '', false);
		var newWin = window.open('', 'PrintWindow', '', false); 
		newWin.document.open(); 
		newWin.document.write('<html><head><meta http-equiv="X-UA-Compatible" content="IE=8"><style>@page {size: portrait;}</style></head><body onload="window.print();"><style type="text/css">table.collapse { border-collapse: collapse; border: 1pt solid black; }table.collapse td { border: 1pt solid black; padding: 2px; }.pagination{display:none;}.buttonDiv{display:none;}.footer{display:block;}</style>'+divToPrint+'</body></html>'); 
		newWin.document.close(); 
	//	setTimeout(function(){newWin.close();},10000); 
	}




    function removePMBFiles(key){

        $.post('attach_inbox_mail.php?delparam', {file_key: key}, function(data) {
            $("#" + key).remove();
        });
        
    }

    
    
    function selectedEmail(thread_id) {
        var params = {
            'name':Math.random(),
            'thread_id': thread_id
        }                
        $.post(['attach_inbox_mail.php'].join(), params).done(function(data){
            var jsonResult = JSON.parse(data);
            var attachNo = $('#imageName').children().length + 1;
            var jsonImage = jsonResult.imageName;
            $('#imageName').append(' <span id="' + attachNo + '"><a href="attachment/' + jsonResult.imageName + '" target="_blank" style="color:#06C;" class="thickbox" >' + jsonResult.imageName + '</a>[<a onclick="removeMaessage(' + attachNo + ', 0, \'' + jsonResult.imageName + '\');" style="color:red;">X</a>]</span>');
            closePopup(300); //close popup.
        })
    }
</script> 
<script>
	// On to the interactiveness now :)

	$(function () {

		$('.nav a').on('click', function () {

			var $el = $(this);
			var index = $('.nav a').index(this);
			var active = $('.nav').find('a.active');

			/* if a tab other than the current active
			 tab is clicked */

			if ($('nav a').index(active) !== index) {

				// Remove/add active class on tabs
				active.removeClass('active');
				$el.addClass('active');


				// Remove/add active class on panes
				$('.tab_pane.active')
						.hide()
						.removeClass('active');
				$('.tab_pane:eq(' + index + ')')
						.fadeIn()
						.addClass('active');

				// we can also add some quick fading effects

				// now that's awesome! you got
				// fancy stylish css3 tabs for your
				// next project ;)

			}
		});


	}());

</script> 
<script type="text/javascript">
$(document).ready(function(){
	//$('.messageReadMore').hide();
	$(".readMoreLink").click(function()
		{
			if($(this).parent().find(".messageReadLess").is(":visible"))
			{
		  
				$(this).html("read less");

				$(this).parent().find(".messageReadLess").hide();

				$(this).parent().find(".messageReadMore").slideDown();

			}
			else
			{
				
				
				$(this).html("read more");

				$(this).parent().find(".messageReadLess").show();

				$(this).parent().find(".messageReadMore").slideUp();
			}
					
		});

});
</script> 
<script type="text/javascript" src="js/compose_multiupload.js"></script> 
<script type="text/javascript" src="js/add_multiple_attachment.js"></script>
<style>
div.content_container{ width:100% !important; }div#container{color:#000000;}

.roundCorner{ border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; }
.innerDivDrager{ color:#000000; width:620px; height:150px; }
.innerDiv{ color:#000000; float:left; border:1px solid red; width:300px; height:120px; float:left;}
div#innerModalPopupDiv, div#innerModalPopupDiv1{color:#000000;}
h3#uploaderBulk{font-size:10px;padding:0;margin:0;float:left;}
.bulkfiles {clear: both;border: 1px solid #ccc;background-color: #E4E4E4;padding: 3px;position: relative;margin: 3px;z-index: 1;width: 98%;cursor: default;height: 110px;}
.bulkfilesdwg {clear: both;border: 1px solid #ccc;background-color: #E4E4E4;padding: 3px;position: relative;margin: 3px;z-index: 1;width: 98%;cursor: default;height: 50px;}
.approveDrawingReg{margin-left:0px;}
/*div#waterMark{color: #ccc;width: 100%;z-index: 0;text-align: center;vertical-align: middle;position: absolute;top: 25px;}*/
table.collapse { border-collapse: collapse; border: 1pt solid black; }
table.collapse tr, table.collapse td { border: 1pt solid black; padding: 2px; font-family:Arial, Helvetica, sans-serif; font-size:10px;}
div#htmlContainer{overflow:auto;max-height:550px;}
#revisionBox{ float:right; margin-right:5px;}
h3#uploaderBulk img{ margin-top: -15px; padding-top: 9px; display: block; }
h3#uploaderBulk span{ display: block; margin-left: 30px; margin-top: -18px; }
.Admin ul{ background-image:url(images/tab_bg.png); position:absolute; border:1px solid #435D01; border-top-right-radius:0px; border-top-left-radius:0px; border-bottom-right-radius:5px; border-bottom-left-radius:5px; border-width:0 1px 1px; top:-9999px; left:-9999px; overflow:hidden; position:absolute; padding-left:0px; z-index:2; margin-top:-7px; }
.Admin ul li{ list-style:none; float:left; }
.Admin ul li span{ font-size:14px; display:block; padding:10px; color:#000000; height:14px !important; cursor:pointer; text-decoration:underline; }
.Admin:hover ul.admindrop{ left:auto; top:auto; z-index:99999; display:block; overflow:hidden; }
ul.buttonHolder {list-style:none;}
ul.buttonHolder li {float:left;margin-left:10px;}
ul.buttonHolder li #disableButtonDiv{height: 50px;left: -5px;position: relative;top: -5px;width: 111px;z-index: 9999;}
ul#filePanel{list-style:none; margin:0px; padding:0px;}
ul#filePanel li{float:left;}
/*div#middle{background: url(images/gray_bg.png) center repeat-y !important;background-position-x: -435px!important;background-color:rgba(0, 0, 0, 0) !important;}*/
div#middle{background: <?php if (isset($_GET['sect']) && $_GET['sect'] != 'drawing_register') { ?>url(images/gray_bg.png)<?php } ?> center repeat-y !important; background-position-x: -435px!important;background-color:#FFFFFF !important;}
<?php if (isset($_GET['type']) && $_GET['type'] == 'pmb') { ?>
	div.content_container{ width:100% !important; }
<?php } else { ?>
	.SearchTabs li a span{background: url("images/selected_right_pc.png") no-repeat scroll right center rgba(0, 0, 0, 0) !important;}
	.SearchTabs li a span:hover{ background:url('images/active_right_pc.png') no-repeat scroll right center rgba(0, 0, 0, 0) !important;}
<?php } ?>
.selectedDoc{ background:#FF1717 !important; }
tr.selectedDoc td.sorting_1{ background:#FF1717 !important; }
ul.headerHolder {list-style:none;}
ul.headerHolder li{float:left; width:230px;}
/*.big_container{width:1200px !important;}*/
.actionButton{float:right; margin:9px 4px 10px 0px;cursor:pointer;}
</style>
<script>
// Start:- Check form updated or not
var isUpdated = 0;
function myBlurFunction(url) { 
	isUpdated = $('#isEditor').val();

	$("#compose :text, :file, :checkbox, select, textarea").change(function() {
		if($("#compose").data("changed",true)){
			isUpdated = 1;
		}
	});
	
	if(isUpdated >= 1){ 
		jConfirm('Are you sure, you want to leave from message body?', 'Alert', function(r){
			if(r){
				isUpdated = 0;
				$('#isEditor').val(0);
				if(url != ''){
					window.location.href = url;
				}
				return true;
			} else {
				return false;
				//editor.focus();
			}
		});
	    //document.getElementById("compose").style.backgroundColor = "yellow";  
	}else{
		if(url != ''){
			window.location.href = url;
		}
		return true;
	  // document.getElementById("compose").style.backgroundColor = "grey";  		
	}
	//editor.focus();
	//$("#subject").focus();
}

window.onload = function() {
  document.getElementById("removeAttachment").focus();
  myBlurFunction('');
};

$( ".MailLeft a" ).mouseover(function() {
	url = $(this).attr('href');
	if(url != 'javascript:void(0);'){
		$(this).attr('href', 'javascript:void(0);');
		$(this).attr('onclick', "myBlurFunction('"+url+"')");
	}
});

// Check check editor data
editor.on('contentDom', function( event ) {
  editor.document.on('keyup', function(event) {
    $('#isEditor').val(1);
	//alert('my keyup');
  });
}); 
// End:- Check form updated or not
</script>
<?php include'data-table.php'; ?>
