<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

session_start();

ob_start();
if (!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])) {
    ?>
    <script language="javascript" type="text/javascript">window.location.href = "<?= HOME_SCREEN ?>";</script>
<?php
}

$projectId = isset($_SESSION['idp']) ? $_SESSION['idp'] : 0;

$msg = '';
require_once('includes/class.phpmailer.php');
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$userId = $_SESSION['ww_builder']['user_id'];

if (isset($_POST['submit']) and $_POST['submit'] == 'add') {
    $from = $_SESSION['ww_builder_id'];
    $recipTo = !empty($_POST['recipTo']) ? $_POST['recipTo'] : 0;
    $recipCC = $_POST['recipCC'];
    $subject = empty($_POST['subject']) ? 'no subject' : $_POST['subject'];
    $tags = $_POST['tags'];

    $messageType = $_POST['messageType'];
    $messageDetails = $_POST['messageDetails'];
    $messgeId = (isset($_POST['composeId']) && !empty($_POST['composeId'])) ? $_POST['composeId'] : 0;

    $rfiNumber = $_POST['rfiNumber'];
	$toEmailList = array();
	$ccEmailList = array();
	
    $ccAddress = '';
    $toExtraAddress = '';
    if ($_POST['emailAttachedAjax'] == 1) {
        $attahment1 = $_SESSION[$_SESSION['idp'] . '_emailfile'];
        //echo $_SESSION['orignalFileName']; die;
    }
	$pmbAttachment = $_SESSION[$_SESSION['idp'].'_pmbEmailfile'];
	
    // Remove old attachment if found any attachment
    if (isset($_POST['removeAttachment'])) {
        if (explode(',', $_POST['removeAttachment'])) {
            $removeAttachments = explode(', ', $_POST['removeAttachment']);
            foreach ($removeAttachments as $attachID) {
                mysql_query("UPDATE `pmb_attachments` SET `is_deleted` = '1', last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '" . $userId . "' WHERE `attach_id` =" . $attachID);
            }
        } else {
            mysql_query("UPDATE `pmb_attachments` SET `is_deleted` = '1', last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '" . $userId . "' WHERE `attach_id` =" . $_POST['removeAttachment']);
        }
    }

    #$attahment1 = $object->upload_attahment('attachment1');
    //$messageBoard = $object->messageBoard($from, $recipTo, $recipCC, $subject, $messageType, $messageDetails, $attahment1);
    //print_r($messageBoard );
    if ((!empty($recipTo) && !empty($subject) && !empty($messageDetails)) || isset($_POST['saveDraft'])) {
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
        $msg = "<br/>Hello, <br>" . $messageDetails;

        if (isset($recipCC)) {
            foreach ($recipCC as $cc) {
				$mail->AddBCC($cc,''); // cc
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
						$mail->AddBCC($gUDetail['email'],''); // To
                       // $mail->AddAddress($gUDetail['email'], ''); // To
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
		
		#.msg attachments
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
        if (get_magic_quotes_gpc()) {
            $messageDetails = stripslashes($messageDetails);
        }
        $messageDetails = mysql_real_escape_string(nl2br(htmlentities($messageDetails, ENT_QUOTES, 'UTF-8')));
        $recipTo = !empty($_POST['recipTo']) ? $_POST['recipTo'] : array(0);
        if (isset($recipTo)) {

            foreach ($recipTo as $to) {
                if (is_numeric($to)) {
                    $messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageType, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, $_POST['threadId'], "", $rfiNumber);
                    $messgeId = $messageBoard['messgeId'];
                } else {
                    $messageBoard = $object->messageBoard($projectId, $from, $to, $subject, $messageType, $messageDetails, $attahment1, $messgeId, $toExtraAddress, $ccAddress, $tags, 0, $_POST['threadId'], "", $rfiNumber);
                    $messgeId = $messageBoard['messgeId'];
                }
                $attahment1 = '';
            }

            if (isset($_POST['oldMsgId']) && $messgeId > 0) {
                $attachemnts = $object->selQRYMultiple('attach_id, name, attachment_name, is_attached_email', 'pmb_attachments', 'message_id="' . $_POST['oldMsgId'] . '" and is_deleted=0');
                if (isset($attachemnts)) {
                    foreach ($attachemnts as $attachemnt) {
                        mysql_query('INSERT INTO pmb_attachments (message_id, name, attachment_name, project_id, is_attached_email, status, created_by, created_date, last_modified_by, last_modified_date) values("' . $messgeId . '", "' . $attachemnt['name'] . '", "' . $attachemnt['attachment_name'] . '", "' . $_SESSION['idp'] . '", "' . $attachemnt['is_attached_email'] . '", "0", ' . $_SESSION['ww_builder_id'] . ', NOW(), ' . $_SESSION['ww_builder_id'] . ', NOW())');
						if($attachemnt['is_attached_email'] == 1){
							$mail->AddAttachment("pmb_attachment/".$_SESSION['idp']."/". $attachemnt['attachment_name']);
						}else{
	                        $mail->AddAttachment("attachment/" . $attachemnt['attachment_name']);
						}
                    }
                }
            }

            # Set sending type
            mysql_query("update pmb_message set sending_type = 'forward', last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '" . $userId . "' where message_id = " . $messgeId);
        }

        $msg .= "<br/><br/>click here to access your message.<br>
						<a href='" . $path . "/pms.php?sect=message_details&id=" . base64_encode($messageBoard['thread_id']) . "&type=inbox&projID=" . $_SESSION['idp'] . "&byEmail=" . $byEmail . "' target='_blank'>" . $path . "/pms.php?sect=message_details&id=" . base64_encode($messageBoard['thread_id']) . "</a>";
        $msg .= "<br/><br/>Thanks,<br> DefectId customer care";
		
		
        $toCCAddress = empty($toEmailList)?'':'To : '.implode(', ', $toEmailList).'<br>';
		$toCCAddress.= empty($ccEmailList)?'':'Cc : '.implode(', ', $ccEmailList).'<br>';
		$toCCAddress.= '<hr><br><b>'.$mail->Subject.'</b><br><br>';
		$mail->MsgHTML($toCCAddress.$msg);	

        if (isset($_POST['save'])) {
            $result = $mail->Send();
        }
        $mail->ClearAddresses();
        $mail->ClearBCCs();
		$mail->ClearAllRecipients();
        $mail->ClearCustomHeaders();

        if ($_POST['composeId'] != 0 && isset($_POST['save'])) {
            mysql_query("update pmb_message set is_draft = 0, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '" . $userId . "' where message_id = " . $_POST['composeId']);
        }

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

        # Add custom entry in address book
        if (!empty($_POST['customEmailEntry'])) {
            if (explode(',', $_POST['customEmailEntry'])) {
                $customEmails = explode(', ', $_POST['customEmailEntry']);
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

        if (isset($_POST['save'])) {
            $redirectUrl = '?sect=sent_box&sm=1';
            header('Location:?sect=sent_box&sm=1');
        } else {
            $redirectUrl = '?sect=drafts&sm=1';
            header('Location:?sect=drafts&sm=1');
        }
        ?>
        <script language="javascript" type="text/javascript">window.location.href = "<?php echo $redirectUrl; ?>";</script>
        <?php
        //$_GET['msgid'] = $messgeId;
    } else {
        echo $messageBoard;
    }
}

function getattachment($mid) {
    $req1 = mysql_query('select attach_id, name, attachment_name from pmb_attachments where message_id="' . $mid . '" and is_deleted=0');
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
        <link rel="stylesheet" href="css/chosen.css">
        <style>
			.chzn-container-multi .chzn-choices {
				border-radius: 4px;
			}
            label { color:#000000;}
            .textEditer{
                color:#000;
                padding-left:10px;
            }
            .nicEdit-main{
                outline:none;
            }
            .Compose .error{
                margin-left:20px;
            }
            span.reqire{
                /*	display:block;*/
            }
            .chzn-drop{
                text-transform:capitalize;
            }
            #messageDetails{
                color:#000;
            }
            body{
                /*color: #000000;*/
            }

            /*.Compose .chzn-container-multi ul.chzn-choices li.search-choice
            {
             clear:none !important;
            }*/
            #imageName{
                float:left; margin-left:0px; margin-top:10px;  width: 550px;
            }
            #imageName span{
                padding-left:15px;
            }
            #imageName a{
                cursor:pointer; padding-right:5px;
            }

            #inboxData .odd  {
                color: #000;
            }

            #inboxData .even  {
                color: #000;
            }

            #outerModalPopupDiv {
                color: #000;
            }

            <?php if ($_GET['view'] == 'workflow') { ?>
                .list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
                .box1 { background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
                .link1 { background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
                a.link1:hover { background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
                .txt13 { border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }
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
                    margin: 0; padding: 0;
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

                .nav a.active,
                .nav a:active {
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
            <?php } ?>
        </style>
    <body>
        <?php
        $viewType = "";
        if (isset($_GET['view']) && $_GET['view'] == 'workflow') {
            $viewType = "&view=workflow";
            ?>

            <div id="leftNav" style="width:250px;float:left;">
                <?php include 'side_menu.php'; ?>
            </div>
        <?php } ?>
        <?php
        //code for showing tabs
        if ($_GET['view'] == 'workflow') {
            ?>
        <nav id="menu" style="margin-top:15px;margin-left:5px;">
            <ul class="nav">
                <li><a href="pms.php?sect=messages&view=workflow"   class="active">PMB</a></li>
                <li><a href="pms.php?sect=drawing_register&view=workflow">Drawing Register</a></li>
            </ul>
        </nav>

        <!-- we're done with the tabs, onto panes now -->

        <section id="tab_panes">
            <div class="tab_pane active">

            </div>

            <!-- we'll copy/paste the other panes -->
            <div class="tab_pane">

            </div>
        </section>	
        <?php } ?>
    <div class="GlobalContainer clearfix">
    <?php include 'message_side_menu.php'; ?>
        <div class="MailRight" <?php if ($_GET['view'] == 'workflow') {
            echo "style='width:64%;'";
        } ?>>
            <div class="MailRightHeader">
                <h2 style="color:#000000; margin-top:10px; margin-left:10px; float:left;">Forward Message</h2>
                <h3 style="color:#000000; margin-top:10px; margin-right:190px; float:right;">Project Name : <?php echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name') ?></h3>
            </div>
            <div class="Compose clearfix" style="color:#050505;">
                <?php
                $forwardData = array();
                $toList = array();
                $ccList = array();
                if (isset($_GET['msgid']) && $_GET['msgid'] != 0) {
                    $forwardData = $object->selQRYMultiple('m.message_id, um.user_id, um.type, m.title, m.message_id, m.sent_time, m.message, um.from_id, m.message_type, m.to_email_address, m.cc_email_address, m.tags, um.rfi_number, um.thread_id', 'pmb_user_message um , pmb_message m', 'm.message_id="' . $_GET['msgid'] . '" AND um.message_id = m.message_id AND um.type="sent"');

                    //$attachemnt=getattachment($_GET['msgid']);
                    $attachemnts = $object->selQRYMultiple('attach_id, name, attachment_name, is_attached_email', 'pmb_attachments', 'message_id="' . $_GET['msgid'] . '" and is_deleted=0');
                }
                ?>
                <form action="" method="post" enctype="multipart/form-data" id="compose">
                    <table style="margin-left:20px;">
                        <tr><td width="250" align="left" valign="top">
                                <label for="name">To<span class="reqire"> *</span></label>
                            </td><td width="816" align="left" valign="top" id="recipToSection" >
                                <?php
								$projectUsers = $object->selQRYMultiple('u.user_id, u.user_fullname, u.company_name, user_email, up.map_user_id, up.map_with', 'user as u Left Join user_projects as up on u.user_id = up.user_id  and up.is_deleted=0', 'u.user_id!="' . $_SESSION['ww_builder_id'] . '" AND u.is_deleted=0 AND up.project_id="' . $_SESSION['idp'] . '" order by u.user_name');
                                $projectIssues = $object->selQRYMultiple('issue_to_id, issue_to_name, company_name, issue_to_email ', 'inspection_issue_to', 'is_deleted=0 AND project_id="' . $_SESSION['idp'] . '" AND issue_to_name!="NA" AND issue_to_email!="" order by issue_to_name');

                                $projectAddresBookUsers = $object->selQRYMultiple('id, full_name, company_name, user_email', 'pmb_address_book', 'is_deleted=0 AND project_id="' . $_SESSION['idp'] . '" AND full_name != "" order by full_name');
                                $mapedIssuedTo = array();
                                $mapedAddressBook = array();
                                ?>

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
                                            ?>    
                                            <option value="<?php echo $puser['user_id']; ?>" <?php echo $select; ?>><?php
                                                if (!empty($puser['user_fullname']) && !empty($puser['company_name'])) {
                                                    echo strtolower($puser['user_fullname'] . " ( " . $puser['company_name'] . " )");
                                                } elseif (empty($puser['user_fullname']) && !empty($puser['company_name'])) {
                                                    echo strtolower("( " . $puser['company_name'] . " )");
                                                } else {
                                                    echo strtolower($puser['user_fullname']);
                                                }
                                                ?></option>
                                        <?php } ?>
                                    </optgroup>

                                    <optgroup label="Issued To">
                                            <?php
                                            foreach ($projectIssues as $pIssue) {
                                                $select = "";
                                                if (!in_array($pIssue['issue_to_id'], $mapedIssuedTo)) {
                                                    ?>    
                                                <option value="<?php echo $pIssue['issue_to_email']; ?>" <?php echo $select; ?>><?php
                                                    if (!empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])) {
                                                        echo strtolower($pIssue['company_name'] . " ( " . $pIssue['issue_to_name'] . " )");
                                                    } elseif (empty($pIssue['company_name']) && !empty($pIssue['issue_to_name'])) {
                                                        echo strtolower("( " . $pIssue['issue_to_name'] . " )");
                                                    } else {
                                                        echo strtolower($pIssue['company_name']);
                                                    }
                                                    ?></option>
                                            <?php }
                                        } ?>
                                    </optgroup>     

                                    <optgroup label="Ad hoc (External)">
                                            <?php
                                            foreach ($projectAddresBookUsers as $addresBookUsers) {
                                                $select = "";
                                                if (!in_array($addresBookUsers['id'], $mapedAddressBook)) {
                                                    ?>     
                                                <option value="<?php echo strtolower($addresBookUsers['user_email']); ?>" <?php echo $select; ?>><?php
                                                if (!empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])) {
                                                    echo strtolower($addresBookUsers['full_name'] . " ( " . $addresBookUsers['company_name'] . " )");
                                                } elseif (empty($addresBookUsers['full_name']) && !empty($addresBookUsers['company_name'])) {
                                                    echo strtolower("( " . $addresBookUsers['company_name'] . " )");
                                                } else {
                                                    echo strtolower($addresBookUsers['full_name']);
                                                }
                                                ?></option>                     
                                                <?php }
                                            } ?>
                                    </optgroup>           
                                </select>
                                <div class="error-edit-profile" style="display:none;"  id="recipToError">The to field is required.</div>
                                <div class="error-edit-profile" style="display:none;"  id="emailError">Invalide email format.</div>
                            </td>
                        </tr>
                        <tr><td align="left" valign="top">
                                <label for="name">CC</label>
                            </td><td align="left" valign="top">
                                <!--input type="text" name="recipCC" id="recipCC" /-->

                                <select name="recipCC[]" id="recipCC" style="width:350px;" multiple class="chzn-select chzn-custom-value" multiple > 
                                    <optgroup label="Project users">
                                            <?php
                                            foreach ($projectUsers as $puser) {
                                                $select = "";
                                                ?>    
                                            <option value="<?php echo $puser['user_email']; ?>" <?php echo $select; ?>><?php
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
                                                if (!in_array($pIssue['issue_to_id'], $mapedIssuedTo)) {
                                                    ?>      
                                                <option value="<?php echo $pIssue['issue_to_email']; ?>" <?php echo $select; ?>><?php
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
                                                if (!in_array($addresBookUsers['id'], $mapedAddressBook)) {
                                                    ?>     
                                                <option value="<?php echo strtolower($addresBookUsers['user_email']); ?>" <?php echo $select; ?>><?php
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
} ?>
                                    </optgroup>               
                                </select>
                            </td>
                        </tr>
                        <tr><td align="left" valign="top">
                                <label for="email">Subject <span class="reqire">*</span></label>
                            </td><td align="left" valign="top">
                                <input type="text" size="40" value="FW :<?php echo htmlentities($_POST['title'], ENT_QUOTES, 'UTF-8'); ?><?php if (isset($forwardData[0]['title'])) {
                                    echo $forwardData[0]['title'];
                                } ?>" id="subject" name="subject"  style="width: 323px; height:12px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%); border: 1px solid #AAAAAA; padding-left:5px; margin-left:10px;"/>
                                <div class="error-edit-profile" style="display:none;"  id="subjectError">The subject field is required.</div>
                            </td>
                        </tr>
                        <tr style=" <?php echo (isset($_GET['folderType']) && $_GET['folderType'] != "")?'display:none;':''; ?>">
                                <td align="left" valign="top">
                                    <label for="email">Message&nbsp;Type <span class="reqire">*</span></label>
                                </td>
                                <td align="left" valign="top">
                                    <?php
                                    if ($_SESSION['idp'] == 242 || $_SESSION['idp'] == 240 || $_SESSION['idp'] == 241) {
                                        #if($_SESSION['idp']==220){
                                        switch ($_SESSION['userRole']) {
                                            case 'All Defect':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Admin', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs', 'Purchaser Changes');
                                                $dontShow = array();
                                                break;

                                            case 'Builder':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Admin', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs', 'Purchaser Changes');
                                                $dontShow = array();
                                                break;


                                            case 'Architect':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes');
                                                $dontShow = array();
                                                break;


                                            case 'Structural Engineer':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction' => array(), 'Consultant Advice Notice', 'Design Changes');
                                                $dontShow = array();
                                                break;


                                            case 'Services Engineer':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes');
                                                $dontShow = array();
                                                break;


                                            case 'Superintendant':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Admin', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs', 'Purchaser Changes');
                                                $dontShow = array();
                                                break;


                                            case 'General Consultant':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes');
                                                $dontShow = array();
                                                break;


                                            case 'Building Surveyor':
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes');
                                                $dontShow = array();
                                                break;


                                            case 'Subcontractor - Tender':
                                                $msgType = array(); //array('General Correspondence' => array(), 'Document' => array('Document Transmittal'), 'Memorandum' => array(), 'Site Instruction' => array(), 'Architect / Superintendant Instruction' => array(), 'Consultant Advice Notice' => array(), 'Design Changes' => array(), 'Contract Admin' => array(), 'Recommendation' => array(), 'Tenders' => array(), 'Variation Claims' => array(), 'Progress Claims' => array(), 'Purchaser Changes' => array());
                                                $dontShow = array("Inspections", "Request For Information", "Meetings");
                                                break;

                                            case 'Sub Contractor':
                                                $msgType = array('General Correspondence', 'Site Instruction', 'Design Changes');
                                                $dontShow = array("Inspections", "Request For Information", "Meetings");
                                                break;


                                            default:
                                                $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Admin', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs', 'Purchaser Changes');
                                                break;
                                        }
                                    } else {
                                        $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Admin', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs', 'Purchaser Changes', 'Request For Information', 'Meetings');
                                    }
                                    if ($_SESSION['ww_builder']['user_type'] != "manager") {
                                        $msgType = array('General Correspondence', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect / Superintendant Instruction', 'Consultant Advice Notice', 'Design Changes', 'Contract Admin', 'Recommendation', 'Tenders', 'Variation Claims', 'Progress Claims', 'EOTs', 'NODs', 'Purchaser Changes', 'Request For Information', 'Meetings');
                                    }
                                    ?>
								<?php  if (isset($_GET['folderType']) && $_GET['folderType'] != "") {
                                    echo '<input name="messageType" id="messageType" value="' . $_GET['folderType'] . '" style="width: 323px; height:12px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%); border: 1px solid #AAAAAA; padding-left:5px; margin-left: 10px;" readonly = "readonly">';
                                 }else {?>
                                    <select name="messageType" id="messageType" style="width: 350px; height:28px; padding-top:4px; background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:2px; margin-left: 10px;">
                                        <!--option value="">Select</option-->
                                        <?php
                                        /* if($_SESSION['userRole'] == 'Architect'){
                                          #echo '<option value="Architect / Superintendant Instruction" selected="selected">Architect / Superintendant Instruction</option>';
                                          }elseif($_SESSION['userRole'] == 'Client'){
                                          #echo '<option value="Design Changes" selected="selected">Design Changes</option>';
                                          }elseif(isset($_GET['dcTrans']) && $_GET['dcTrans']=='Y'){
                                          #echo '<option value="Document Transmittal" selected="selected">Document Transmittal</option>';

                                          }else{ */
                                       // if (isset($_GET['folderType']) && $_GET['folderType'] != "") {
                                           // echo '<option value="' . $_GET['folderType'] . '">' . $_GET['folderType'] . '</option>';
                                       // } else {
                                            for ($i = 0; $i < sizeof($msgType); $i++) {
                                                ?>
                                                <option value="<?php echo $msgType[$i]; ?>"
                                                <?php if (!empty($comMessData) && $comMessData[0]['message_type'] == $msgType[$i]) {
                                                    echo 'selected="selected"';
                                                } ?> 
                                                <?php if (isset($_SESSION[$_SESSION['idp'] . '_remaimberData']) && $_SESSION[$_SESSION['idp'] . '_remaimberData']['messageType'] == $msgType[$i]) {
                                                    echo 'selected="selected"';
                                                } ?> ><?php echo $msgType[$i]; ?></option>
                                            <?php
                                            }
                                        //}
                                        //}
                                        ?>
                                    </select>
                                    <?php } ?>
                                    <div class="error-edit-profile" style="display:none;"  id="messageTypeError">The message type field is required.</div>
                                </td>
                            </tr>                        
                        <tr id="hiddenRow" style="display:none;">
                            <td align="left" valign="top">
                                <label for="email">
                                    RFI&nbsp;# (use whole numbers e.g 1,2,3,4) <span class="reqire">*</span>
                                </label>
                            </td>
                            <td align="left" valign="top">
                                <select name="RFInumber" id="RFInumber" style="width: 350px; height:28px; padding-top:4px; background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:2px; margin-left: 10px;">
                                    <option value="">Select</option>
                                    <option value="<?php echo $forwardData[0]['rfi_number']; ?>" selected="selected"><?php echo $forwardData[0]['rfi_number']; ?></option>
                                    <optgroup label="Current RFI">
                                        <option value="<?php echo ++$refNumberMsgCount[0]['refNumber']; ?>"><?php echo $refNumberMsgCount[0]['refNumber']; ?></option>
                                    </optgroup>
                                    </optgroup>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" valign="top">Tags</td>
                            <td align="left" valign="top" ><input type="text" size="40" value="<?php echo htmlentities($_POST['tags'], ENT_QUOTES, 'UTF-8'); ?><?php if (isset($forwardData[0]['tags'])) {
                                    echo $forwardData[0]['tags'];
                                } ?>" id="tags" name="tags" style="width: 323px; height:12px;background-image: linear-gradient(#EEEEEE 1%, #FFFFFF 15%);     border: 1px solid #AAAAAA; padding-left:5px;  margin-left:10px;" /></td>
                        </tr>
                        <tr><td align="left" valign="top">
                                <label for="message">Message <span class="reqire">*</span></label>
                            </td><td align="left" valign="top" class="textEditer">
								<?php  $messBox = ""; 
									if (!empty($forwardData)) {
										$searchArr = array("\\n", "<p>&nbsp;</p>", "&lt;p&gt;&amp;nbsp;&lt;/p&gt;", "<br />", "<br>", "<br />\\n");
										$replaceArr = array("", "", "", "", " ", " ");
                                        $messBox = '<pre>'.str_replace($searchArr, $replaceArr, nl2br($forwardData[0]['message']));
                                    }
								?>
								<div style="width: 80%;margin-right: 20%">
                                <textarea name="messageDetails"  id="messageDetails"><br><br><br><?php echo htmlentities($_POST['message'], ENT_QUOTES, 'UTF-8'); ?><?php echo $messBox; ?></textarea>
								<div class="error-edit-profile" style="display:none;"  id="messageDetailsError">The message field is required.</div>
								<input type="hidden" id="isEditor" value="false" />
								</div>
							</td>
		 </tr>
		 <tr><td align="left" valign="top">
            <label>Upload a file</label>
         </td>
			<td align="left" valign="top">
                <div style="overflow:hidden;cursor:pointer;width:36px;height:36px;float:left;color:#0000AA;margin-left:15px;">
                    <img src="images/compose_attachment.png" name="newFileToUpload" id="newFileToUpload" title="Select New File" onClick="addAttachment();" />
                    <!--input type="file" style="opacity: 0;width:40px;height:40px;margin-top: -40px;margin-left: 0px;font-size:35px;cursor: pointer !important;" id="attachment1" title="Select New File" name="attachment1" -->
                </div>
                <img src="images/attach-dr.png" name="chooseFormDR" id="chooseFormDR" title="Choose From Document Register" onClick="showDocumentRegisterFiles();" style="float:left;cursor:pointer;" />

                <img src="images/add_pmb.png" name="attachEmail" id="attachEmail" title="Add a PMB Message" onClick="attachEmails();" style="margin-left:5px;float:left;cursor:pointer;" />

                <img src="images/add_email.png" name="attachEmail" id="attachEmailNew" title="Add Email" onClick="addEmails();" style="margin-left:5px;float:left;cursor:pointer;" /><br><br><br>
				<!-- <div style="overflow:hidden;cursor:pointer;width:36px;height:36px;float:left;color:#0000AA;margin-left:15px;"> <img src="images/compose_attachment.png" name="newFileToUpload" id="newFileToUpload" title="Select New File" />
					<input type="file" style="opacity: 0;width:40px;height:40px;margin-top: -40px;margin-left: 0px;font-size:35px;cursor: pointer !important;" id="attachment1" title="Select New File" name="attachment1">
				</div>
				<a href="?sect=drawing_register_select&page=forward&msgid=<?php //echo isset($_GET['msgid']) ? $_GET['msgid'] : 0; ?>"  style="float:left;">
					<img src="images/compose_document.png" name="chooseFormDR" id="chooseFormDR" title="Choose From Document Register" onClick="showDocumentRegisterFiles();" />
				</a> -->
                <div id="imageName" >
                        <?php
                        //print_r($attachemnt);
                        //echo (isset($attachemnt[1]) && !empty($attachemnt[1]))?$attachemnt[1]:'';

                        if (isset($_SESSION[$_SESSION['idp'] . '_pmbEmailfile'])) {
                           foreach ($_SESSION[$_SESSION['idp'] . '_pmbEmailfile'] as $key => $val) {
                    
                                echo ' <span id="' . $key . '"><a style="color:#06C;" target="_blank" href="attachment/' . $val . '">' . $_SESSION[$_SESSION['idp'] . '_pmbEmailfile'][$key] . '</a>[<a style="color:red;" onclick="removePMBFiles(' . $key . ');">X</a>]</span>';
                            }
                        }

                        if (isset($attachemnts)) {  $i = 0;
                            foreach ($attachemnts as $attachemnt) {  $i++;
                                $type = explode('.', $attachemnt['attachment_name']);
								$isPmbAttach = ($attachemnt['is_attached_email'] == 1)?'pmb_attachment/'.$_SESSION['idp'].'/':'attachment/';
                                $type = end($type);
                                if (strpos('JPEG |jpeg |JPG |jpg |PNG |png |GIF |gif', $type) > 0) {
                                    echo ' <span id="' . $i . '"><a class="thickbox" style="color:#06C;" target="_blank" href="' .$isPmbAttach. $attachemnt['attachment_name'] . '">' . $attachemnt['name'] . '</a>[<a style="color:red;" onclick="removeMaessage(' . $i . ',' . $attachemnt['attach_id'] . ');">X</a>]</span>';
                                } else {
                                    echo '<span id="' . $i . '"><a style="color:#06C;" target="_blank" href="' .$isPmbAttach. $attachemnt['attachment_name'] . '">' . $attachemnt['name'] . '</a>[<a style="color:red;" onclick="removeMaessage(' . $i . ',' . $attachemnt['attach_id'] . ');">X</a>]</span>';
                                }
                            }
                        }
                        if (isset($_SESSION[$_SESSION['idp'] . '_emailfile'])) {   $i = 0;
                            foreach ($_SESSION[$_SESSION['idp'] . '_emailfile'] as $key => $val) {   $i++;
                                echo ' <span id="' . $i . '"><a style="color:#06C;" target="_blank" href="attachment/' . $val . '">' . $_SESSION[$_SESSION['idp'] . '_emailfile'][$key] . '</a>[<a style="color:red;" onclick="removeMaessage(' . $i . ',0);">X</a>]</span>';
                            }
                        }
                        ?>
                
                </div>
				<input type="hidden" name="emailAttachedAjax" id="emailAttachedAjax" value="0" />
			</td>
        </tr>
         <tr>
           <td align="left" valign="top">&nbsp;</td>
           <td align="left" valign="top">         
         </tr>
         <tr><td align="left" valign="top">
        	 <label>&nbsp;</label>
         </td><td align="left" valign="top" id="lastTd">
            <!--button type="submit" class="right">Send </button-->
            <input type="hidden" name="customEmailEntry" id="customEmailEntry" value="">
            <input type="hidden" name="removeAttachment" id="removeAttachment" value="">
			<input type="hidden" name="oldMsgId" value="<?php if (isset($forwardData[0]['message_id'])) {
        echo $forwardData[0]['message_id'];
    } else {
        echo 0;
    } ?>">
            <input type="hidden" id="threadId" name="threadId" value="<?php if (isset($forwardData[0]['thread_id'])) {
        echo $forwardData[0]['thread_id'];
    } else {
        echo 0;
    } ?>">
			<input type="hidden" name="rfiNumber" value="<?php if (isset($forwardData[0]['rfi_number'])) {
        echo $forwardData[0]['rfi_number'];
    } else {
        echo 0;
    } ?>">
			<input type="submit" value="" name="save" id="sendMessage" style="background:url(images/email_send.png) no-repeat;border:0;width:87px;height:37px;" />
            <input type="submit" value="" name="saveDraft" id="saveDraft" style="background:url(images/save_draft.png) no-repeat;border:0;width:113px;height:37px;" />
			 <input type="hidden" name="submit" value="add">
             </td>
        </tr>
	</table>
</form>
</div>
</div>
</div>
<?php if (isset($_GET['attached']) and $_GET['attached'] == 'Y') { ?>
    	<script type="text/javascript">
                $(document).ready(function () {
                    $('#emailAttachedAjax').val(1);
                });
        </script>
<?php
} else {
    if (isset($_SESSION[$_SESSION['idp'] . '_emailfile'])) {
        unset($_SESSION[$_SESSION['idp'] . '_emailfile']);
    }
    if (isset($_SESSION[$_SESSION['idp'] . '_orignalFileName'])) {
        unset($_SESSION[$_SESSION['idp'] . '_orignalFileName']);
    }
}
?>

<script type="text/javascript" src="js/thickbox.js"></script>
<link rel="stylesheet" href="css/thickbox.css" type="text/css" media="screen" />
<!--script type="text/javascript" src="js/nicEdit.js"></script-->
<script type="text/javascript" src="ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
<script type="text/javascript">
            /*bkLib.onDomLoaded(function () {
                new nicEditor({iconsPath: 'js/nicEditorIcons.gif', buttonList: ['save', 'bold', 'italic', 'underline', 'left', 'center', 'right', 'justify', 'ol', 'ul', 'indent', 'outdent', 'forecolor', 'bgcolor']}).panelInstance('messageDetails');
            });*/
            /*======== CKEDITOR ==========*/
            var editor = CKEDITOR.replace( 'messageDetails', {
				uiColor: '#F7F7F7',
				toolbar: [
					[ 'Bold', 'Italic', 'Underline', '-', 'NumberedList', 'BulletedList', '-', "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock"],
					[ 'FontSize', 'TextColor', 'BGColor' ]
				],
			});
            /*======== CKEDITOR ==========*/            
            
            $(document).ready(function () {
				/*$('body a').click(function(e){
					if(e && e.preventDefault) {
						e.preventDefault();
					}
					var href = $(this).attr('href'),
						msg_focus = $('#msg_focus').val();
					if(msg_focus == 'false') {
						window.location = href;
					}
					return false;
				});*/
				
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


                $('#saveDraft').click(function () {
                    isFormSubmit = 1;
                    showProgress();
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
                        //	alert(fileName);
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
            });
            
            if($('#messageType').val() == 'Consultant Advice Notice') {
                $('#hiddenRow').show();
            }

            function removeMaessage(id, attachId) {
                $("#" + id).remove();
                var removeAttachId = $("#removeAttachment").val();
                if (attachId != 0) {
                    if (removeAttachId == "") {
                        $("#removeAttachment").val(attachId);
                    } else {
                        $("#removeAttachment").val(removeAttachId + ',' + attachId);
                    }
                }
            }
    </script>  
<style>div.content_container{ width:100% !important; }</style>
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
  document.getElementById("threadId").focus();
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

            function removeBulkAttachment(id) {

                /*alert(id);
                 //var testFxArr = Array();
                 console.log(testFxArr);
                 //delete testFxArr[0];
                 for (var key in testFxArr) {
                 if (testFxArr[key] == '0') {
                 testFxArr.splice(0, 1);
                 }
                 }
                 testFxArr.splice(0,testFxArr.length);
                 testFxArr.pop();*/
                //removeKey(testFxArr,0);
                for (var key in testFxArr) {
                    if (testFxArr[key] == '0') {
                        testFxArr.splice(0, 1);
                    }
                }
                var testFxArr = new Array();
                console.log(testFxArr);
                $('.divId_' + id).hide('slow');
                tempId = 0;
                this.all = [];
                self.all = [];
            }
            function removeKey(arrayName, key)
            {
                var x;
                var tmpArray = new Array();
                for (x in arrayName)
                {
                    if (x != key) {
                        tmpArray[x] = arrayName[x];
                    }
                }
                return tmpArray;
            }                                  
</script>
<?php if (isset($_GET['attached']) and $_GET['attached'] == 'Y') { ?>
<script type="text/javascript">
        $(document).ready(function () {
            $('#emailAttachedAjax').val(1);
        });
    </script>
<?php } ?>
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script src="js/jquery.min.for.choosen.js" type="text/javascript"></script>
<script src="js/chosen.jquery.js" type="text/javascript"></script>
<script type="text/javascript">
        var align = 'center';
            var top1 = 100;
            var width = 850;
            var padding = 10;
            var backgroundColor = '#FFFFFF';
            var borderColor = '#333333';
            var borderWeight = 4;
            var borderRadius = 5;
            var fadeOutTime = 300;
            var disableColor = '#666666';
            var disableOpacity = 40;
            var loadingImage = 'images/loadingAnimation.gif';
            var copyStatus = false;
            var copyId = '';
            var config = {
                '.chzn-select': {},
                '.chzn-select-deselect': {allow_single_deselect: false},
                '.chzn-select-width': {width: "95%"}
            }
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            }
      


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
                modalPopup(align, top1, 1000, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_attachment_email.php?&name=' + Math.random(), loadingImage, addMultipleAttachment);
                goTop();
            }
            
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

            function showDocumentRegisterFiles() {
                var input = $("<input>").attr("type", "hidden").attr("name", "messageDetail").val($('.nicEdit-main').html());
                $('#compose').append($(input));
                $.post("copy_file_toAttach_folder.php?antiqueId=" + Math.random(), $('#compose').serialize()).done(function (data) {
                    var jsonResult = JSON.parse(data);
                    if (jsonResult.status) {
                        console.log(jsonResult.dataArr);
                        window.location.href = "?sect=drawing_register_select&page=forward&msgid=0";
                    }
                });
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