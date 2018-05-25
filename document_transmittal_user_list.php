<?php session_start();
$builder_id = $_SESSION['ww_builder_id'];
include_once("includes/commanfunction.php");
require_once('includes/class.phpmailer.php');
$obj = new COMMAN_Class(); 

if(isset($_REQUEST["singleID"])){
	$spCondtion = "";
	if(isset($_REQUEST['messageType']) && $_REQUEST['messageType'] != "")
		$spCondtion = ' AND m.message_type = "'.$_REQUEST['messageType'].'"';
	$corNumCount = array();
	$corNumCount = $obj->selQRYMultiple('max(m.correspondence_number) AS correspondenceNumber', 'pmb_message as m, pmb_user_message as um', 'um.project_id = '.$_SESSION['idp'].' AND m.is_draft = 0 AND um.is_deleted= 0 AND m.message_id = um.message_id'.$spCondtion);
	
	$outputArr = array('status'=> true, 'msg'=> '', 'data'=> ++$corNumCount[0]['correspondenceNumber']);
	echo json_encode($outputArr);
}
if(isset($_REQUEST["antiqueID"])){
	$appendID = $_POST['userListHolder'];
	$userEmailIDArr = array();
	foreach($_POST as $key=>$val){
		$userEmailIDArr[$key] = unserialize($val);
	}
	$outputArr = array('status'=> true, 'appendID'=> $appendID, 'userList'=> serialize($userEmailIDArr));
	echo json_encode($outputArr);
}

if(isset($_REQUEST["name"])){
	$userEmailData = $obj->selQRYMultiple('DISTINCT u.user_id, u.user_email, up.project_id, up.project_name, u.user_type, u.user_fullname, u.recieve_email', 
	'user_projects AS up, user AS u',
	'up.user_id = u.user_id AND up.project_id = '.$_SESSION['idp'].' AND up.is_deleted = 0 AND u.is_deleted = 0 AND is_pdf = 1 AND u.user_id != '.$_SESSION['ww_builder_id'].' ORDER BY project_id');?>
	<fieldset class="roundCorner" style="color:#000;">
		<legend style="color:#000000;">Email Notification User List</legend>
		<form name="addUserListForm" id="addUserListForm">
		<ul class="buttonHolder">
		<?php foreach($userEmailData as $userData){?>
			<li style="padding:7px;float:none;">
				<label for="<?=$userData['user_id'];?>" id="label<?=$userData['user_id'];?>">&nbsp;&nbsp;&nbsp;<input type="checkbox" name="<?=$userData['user_id'];?>" id="<?=$userData['user_id'];?>" value='<?=serialize(array($userData['user_email'], $userData['user_fullname']));?>' /><?=$userData['user_fullname'];?></label>
			</li>
		<?php }?>
		</ul>
		<br clear="all" /><br clear="all" /><br clear="all" />
		<ul class="buttonHolder">
			<li>
				<input type="button" name="button" class="green_small" id="buttonFirstSubmit" style="float:left;" onClick="submitSelectUserNotification();" value="Save"/>
				<input type="hidden" name="userListHolder" id="userListHolder" value="<?=$_REQUEST['userListHolder'];?>" />
			</li>
			<li>
				<a id="ancor" href="javascript:closePopup_gs(300, 2);" class="green_small">Back
				</a>
			</li>
		</ul>
		</form>
		<br clear="all" />
	</fieldset>
<?php }?>