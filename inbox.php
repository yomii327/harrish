<?php  $userId = $_SESSION['ww_builder']['user_id'];
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');
	session_start();
	if(isset($_GET['id']) || !empty($_GET['id'])){
		$_SESSION['idp'] = base64_decode($_GET['id']);
		setcookie('pmb_'.$_SESSION['ww_builder_id'], $_GET['id'], time()+864000);
	}
	//New Added code for project dropdown Start here
	if(isset($_POST['projName']) && !empty($_POST['projName'])){
		$_SESSION['idp'] = $_POST['projName'];
	}
	$q = "SELECT project_id, project_name FROM user_projects WHERE user_id = '".$_SESSION['ww_builder_id']."' and is_deleted = 0 GROUP BY project_name";
	//$q = "SELECT project_id, project_name FROM user_projects WHERE company_id IN(".$_SESSION['companyId'].") and is_deleted = 0 GROUP BY project_name";
	$res = mysql_query($q);
	$prIDArr = array();
	$outPutStr = "";
	while($q1 = mysql_fetch_array($res)){
		if(!isset($_SESSION['idp']))
			$_SESSION['idp'] = $q1[0];
		$selectBox = '<option value="'.$q1[0].'"';
		$prIDArr[$q1[0]] = $q1[1];
		if(isset($_SESSION['idp']) && $_SESSION['idp'] != ""){
			if($_SESSION['idp'] == $q1[0]){
				$selectBox .= 'selected="selected"';
			}
		}	
		$selectBox .= '>'.$q1[1].'</option>';
		$outPutStr .= $selectBox;
	}
	//New Added code for project dropdown End here

	$projectId = isset($_SESSION['idp'])?$_SESSION['idp']:0;

	include('includes/commanfunction.php');
	$object = new COMMAN_Class();
	$obj = new DB_Class();
	$permArray = array('General Consultant', 'Architect', 'Structural Engineer', 'Services Engineer', 'Sub Contractor', 'Client');

	$permArrayTwo = array('General Consultant', 'Structural Engineer', 'Services Engineer', 'Sub Contractor');
	function getrow($id){
		$sql="select count(*) as num from pmb_message where from_user='".$id."' AND to_user='".$_SESSION['ww_builder_id']."' AND inbox_read=0 AND deleted = 0 ";
		$result=mysql_query($sql);
		$row=mysql_fetch_array($result);
		return $row['num'];
	}

	function unreadCount($id){
		$sql="select count(*) as num from pmb_user_message where thread_id='".$id."' AND user_id='".$_SESSION['ww_builder_id']."' AND type='inbox' AND inbox_read=0";
		$result=mysql_query($sql);
		$row=mysql_fetch_array($result);
		return $row['num'];
	}

	function threadCount($id){
		$sql = "select count(*) as num from pmb_user_message where type = 'inbox' AND thread_id='".$id."' AND user_id = '".$_SESSION['ww_builder_id']."' AND is_deleted='0' group by message_id";
		$result=mysql_query($sql);
		$row=mysql_fetch_array($result);
		//return $row['num'];
		return mysql_num_rows($result);
	}

	function getUserNameByEmailids($emailIDs){
		$tempArr = explode(',', str_replace(", ", ",", $emailIDs));
		array_walk($tempArr, 'inQueryData');
		//print_r($tempArr);
		$nameDataArr = array();
		$sql = 
			"SELECT
				ab.full_name as name,
				user_email as email
			FROM
				pmb_address_book as ab
			where
				user_email IN ('". join("','", $tempArr) ."')
			UNION
				SELECT
					iss.company_name as name,
					iss.issue_to_email as email
				FROM
					inspection_issue_to as iss
				WHERE
					issue_to_email IN ('". join("','", $tempArr) ."')";
	
		$result = mysql_query($sql);
		while($row = mysql_fetch_array($result)){
			$nameDataArr[] = $row['name'];
		}
		return join(", ", $nameDataArr);
	}

	if (isset($_REQUEST['action']) && $_REQUEST['action']=='delete'){
	    $thread_id = $_REQUEST['thread_id'];
		$to_id = $_SESSION['ww_builder_id'];
		$deleteQRY = "UPDATE pmb_user_message SET is_deleted = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE thread_id = '".$thread_id."' AND user_id = '".$to_id."'";
		echo $deleteQRY;
		
		mysql_query($deleteQRY);
		if($_REQUEST['type'] == 'insp')
			header('location:?sect=messages');
		else
			header('location:?sect=messages');
	}

	if (isset($_REQUEST['form_type']) && $_REQUEST['form_type']=='inbox') {
	    $to_id = $_SESSION['ww_builder_id']; 
		if(sizeof($_REQUEST['from'])>0) {
		for($i=0;$i<sizeof($_REQUEST['from']);$i++) {
		   $thread_id = $_REQUEST['from'][$i];
		  mysql_query("UPDATE pmb_user_message SET is_deleted = 1, last_modified_date = NOW(), original_modified_date = NOW(), last_modified_by = '".$userId."' WHERE thread_id = '".$thread_id."' AND user_id = '".$to_id."'");
	       header('location:?sect=messages');
		  }
		}
	}

	function getuserdetails($id){
	  $req1 = mysql_query('select user_id, user_name, user_fullname from user where user_id="'.$id.'"');
	   $row=mysql_fetch_array($req1);
	   return $row;
	}
?>

<?php include'data-table.php'; ?>
<link href="css/email.css" rel="stylesheet" type="text/css" />
<style>
.dataTables_wrapper{ clear: both; margin-left: 10px; min-height: 302px; position: relative; width: 98%; }
.sorting_1{ padding-left:26px !important; }
tr.gradeA td{ line-height:30px; }
tr.gradeA td a{ display:block; color:#000; }
table.display tr.odd.gradeA{ background-color:#CCCCCC; }
tr.odd.gradeA td.sorting_1{ background-color:#CCCCCC; }
table.display tr.even.gradeA{ background-color:#EAEAEA; }
tr.even.gradeA td.sorting_1{ background-color:#EAEAEA; }
#inboxData a{color:#000;}
<?php if($_GET['view']=='workflow'){?>
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

#menu { text-align: center; }

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

.nav li:first-child { border-left: 0; }

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

.tab_pane { display: none; }
.tab_pane.active { display: block; }

#tab_panes img {
	max-width: 600px;
	box-shadow: 0 0 5px rgba(0,0,0,0.5);
}
<?php }?>
</style>

<script type="text/javascript">
$(document).ready(function() {
	oTable = $('#inboxData2').dataTable({
		"bJQueryUI": true,
		"bStateSave": false,
		//"aaSorting": [ [3,'desc'] ],
		"sPaginationType": "full_numbers",
		"iDisplayLength": 100,
		"aoColumns": [
			{"sType": "html"},
<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
			{"sType": "html"},
<?php }?>
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"}
		]
	});
});
	
checked=false;

function delMessage (id) {
	var r = jConfirm('Are you sure you want to delete this message?', null, function(r){ if(r==true){ window.location = '?sect=messages&thread_id='+id+'&action=delete'; } });
/*	if (confirm("Are you sure want to delete this?")) {
	   location.href='?sect=messages&thread_id='+id+'&action=delete';
	 } else { 
	 } 
	 */
}

function checkedAll (frm1) {
	var aa= document.getElementById(frm1);
	 if (checked == false) {
           checked = true
      } else {
           checked = false
      }
	for (var i =0; i < aa.elements.length; i++) {
	    aa.elements[i].checked = checked;
	  }
 }
 
  function deleteSelected (frm) {
     var aa= document.getElementById(frm);
	 totalChecked=0;
	 for (var i =0; i < aa.elements.length; i++) {
	    var e = aa.elements[i];
	    if ((e.name != 'allbox') && (e.type=='checkbox')) {
	    if(eval(aa.elements[i].checked) == true) {
           totalChecked=totalChecked+1;
		 }  
		}
	  }
	  
	  if(totalChecked>0) {
	    if (confirm("Are you sure want to delete this?")) {
	       document.getElementById(frm).submit();
		   return true;
	     } else { } 
       } else {
	     alert('Please select atleast one record');
	     return false;
	   }
 }
 
 </script>

<main id="messages">
	<div class="container-fluid no-padding">
		<div class="demo_jui">
			<?php $viewType = "";
				if(isset($_GET['view']) && $_GET['view'] == 'workflow'){ 
					$viewType = "&view=workflow"; ?>
		
				<div id="leftNav">
					<?php include 'side_menu.php';?>
				</div>
			<?php }?>
 			<?php //code for showing tabs
	        	if($_GET['view']=='workflow'){
	        ?>
				<nav id="menu" style="margin-top:15px;margin-left:5px;">
			  		<ul class="nav">
						<li><a href="javascript:void(0)">PMB</a></li>
						<li><a href="javascript:void(0)">Drawing Register</a></li>
			 		</ul>
				</nav>

			<!-- we're done with the tabs, onto panes now -->
				<section id="tab_panes">
					<div class="tab_pane active"></div>
					<!-- we'll copy/paste the other panes -->
					<div class="tab_pane"></div>
				</section>	
	        <?php } ?>	

			<div class="GlobalContainer clearfix">
				<div class="row no-margin">
					<div class="col-md-3 no-padding">
						<?php include 'message_side_menu.php'; ?>
					</div>
					<div class="col-md-9">
						<div class="MailRight">
							<div class="MailRightHeader">
								<div class="row">
									<div class="col-md-8">
										<ul>
											<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
												<!-- <li><a href="#" onClick="deleteSelected('inbox');"><img src="images/delete1.png" width="93" height="34" alt="Delete" /></a></li> -->
												<li>
													<button onclick="deleteSelected('inbox');" class="btn btn-default">
														<i class="delete"></i>
														<span>Delete</span>
													</button>
												</li>
											<?php }?>
											<?php if(!in_array($_SESSION['userRole'], $permArrayTwo) || $_SESSION['idp'] == '242'){?>
												<!-- <li><a href="?sect=compose&folderType=<?=$_GET['folderType'].$viewType;?>"><img src="images/compose.png" width="103" height="34" alt="Compose" /></a></li> -->
												<li>
													<button class="btn btn-default">
														<a href="?sect=compose&amp;folderType=<?=$_GET['folderType'].$viewType;?>">
															<i class="compose"></i>
															<span>Compose</span>
														</a>
													</button>
												</li>
											<?php }?>
											<?php if(isset($_GET['folderType']) && $_GET['folderType'] != ""){?>
												<li>
													<!-- <img src="images/generate_reppmb_ort.png" alt="Generate Report" onClick="generateReport('< ?=$_GET['folderType']?>');" style="cursor:pointer;" /> -->
													<button class="btn btn-default" href="javascript:void(0)" onclick="generateReport('<?=$_GET['folderType']?>');" alt="Generate Report">Generate Report</button>
												</li>
											<?php }?>
											<li>
												<span style="color:#000000; font-size:14px; font-weight:bold;">Project Name : <?php echo $projectName = $prIDArr[$_SESSION['idp']];?></span>
											</li>
										</ul>
									</div>
									<div class="col-md-4">
										<form action="" name="projForm" id="projForm" method="post">
											<select name="projName" id="projName"  class="form-control" onChange="startAjax(this.value);">
												<?php echo $outPutStr;?>
            								</select>
            							</form>
            						</div>
            							<!--<h3 style="color:#000000; margin-top:10px; margin-right:30px; float:right;">Project Name : <?php #echo $projectName = $object->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></h3>-->
            						</div>
            						<?php $spCon = "um.type = 'inbox' AND um.inbox_read = 0 AND ";
            							if(isset($_GET['folderType'])){
            								$spCon = "m.message_type = '".$_GET['folderType']."' AND (um.type = 'inbox'  OR um.type = 'sent') AND ";
            							}
            							$orderBy = "";
            							if($orderBy == ""){ $orderBy = 'm.sent_time DESC'; }
										/*
										echo $query = "SELECT
											um.user_id, 
											um.message_id,
											um.from_id, 
											um.thread_id,
											um.inbox_read, 
											m.title, 
											m.message,
											m.message_type,
											m.sent_time,
											m.cc_email_address,
											um.user_message_id,
											um.rfi_number,
											um.rfi_description, 
											m.rfi_status
										FROM
											pmb_user_message as um,
											pmb_message m
										WHERE
											(um.message_id = m.message_id AND
											".$spCon."
											um.is_deleted = 0 AND
											m.is_draft = 0 AND
											um.project_id = '".$projectId."' AND
											um.user_id = '".$_SESSION['ww_builder_id']."')
											
											AND 
											(
											(m.message_type IN ('General Correspondance', 'Document Transmittal', 'Memorandum', 'Site Instruction', 'Architect Instruction', 'Design Changes', 'Document Updated')
											
											OR
											
											(m.message_type IN ('Request For Information') AND inbox_read = 0)))
										GROUP BY
											um.thread_id
										ORDER BY
											m.sent_time DESC";
						    			$inboxquery = mysql_query($query); */
					    			?>
					    			<form name="inbox" action="" method="post" id="inbox">
					    				<?php if(isset($_GET['folderType']) && $_GET['folderType'] != ""){
					    					if(explode("_", $_GET['folderType'])){
					    						$folderTypeArr = end(explode("_", $_GET['folderType']));
					    					}else{
					    						$folderTypeArr = $_GET['folderType'];
					    					}
					    					if($folderTypeArr == "Consultant Advice Notice"){
					    						echo '<table><tr><td><h3 style="color:#000;padding-left:15px;">'.$folderTypeArr.'</h3></td>: <td>Consultants advice in response to request for information. Please enter RFI number when composing a new CAN.</td></tr></table>';
					    					}else{
					    						echo '<h3 style="color:#000;padding-left:15px;">'.$folderTypeArr.'</h3>';
					    					}
					    				}?>
					    				<table cellpadding="0" cellspacing="0" border="0" class="display" id="inboxData" width="100%">
					    					<thead>
					    						<tr>
					    							<th width="2%"><input name="allbox" type="checkbox" value="" onClick="checkedAll('inbox');" /></th>
					    							<?php if(!isset($_GET['folderType'])){?>
					    								<th width="5%">From</th>
					    							<?php }else{?>
					    								<th width="5%">To</th>
					    								<th width="5%">CC</th>
					    							<?php }?>	
					    							<?php if(isset($_GET['folderType']) && $_GET['folderType']=="Purchaser Changes"){?>
					    								<th>Purchaser Location</th>
					    							<?php }?>
					    							<?php if(isset($_GET['folderType']) && $_GET['folderType']=="Consultant Advice Notice"){?>
					    								<th width="5%">RFI #</th>
					    							<?php }?>	 
					    							<?php if(strpos($_REQUEST['folderType'], 'Variation Claims') > -1){?>                        
					    								<th width="10%">Variation No.</th>
					    							<?php }else{?>
					    								<th width="10%">Correspondance Number</th>
					    							<?php } ?>	
					    							<th width="50%">Subject</th>
					    							<?php if(!isset($_GET['folderType'])){?>
					    								<th>Message Type</th>
					    							<?php }?>
				    							<?php $ARR = array('Contract Admin_Client Side_Variation Claims', 'Progress Claims');
													//$ARR = array('Progress Claims');
													if(!in_array($_GET['folderType'],$ARR)){?>   	
														<th width="5%">Time</th>
													<?php }?>	
													<?php if(strpos($_REQUEST['folderType'], 'Variation Claims') > -1){?>
														<th width="5%">Date Sent</th>
														<!--th width="5%">Date Approved</th-->
														<th width="5%">$ Claimed</th>
														<!--th width="5%">$ Approved</th-->
													<?php } ?>
													<?php if($_GET['folderType']=='Progress Claims'){?>
														<th width="5%">Date Sent</th>
														<th width="5%">Date Approved</th>
														<th width="5%">$ Claimed</th>
														<th width="5%">$ Certified</th>
														<th width="5%">$ Invoiced</th>
													<?php } ?>		
													<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
														<th width="5%">Action</th>
												<?php }?>	
												</tr>
											</thead>
											<!--tbody>
											<?php 
											/*if(intval(mysql_num_rows($inboxquery))>0) { //We display the list of read messages
											while($inbox = mysql_fetch_array($inboxquery)) {
											$query = "select from_id FROM pmb_user_message WHERE message_id=".$inbox['message_id']." and is_deleted=0 and type='sent'";
											$getUserList = mysql_query($query);
											$multipleUser = '';
											if(mysql_num_rows($getUserList) > 0){
												while($userId = mysql_fetch_array($getUserList)){
													if($userId['from_id'] != 0){				 
														$userNameToList = getuserdetails($userId['from_id']);
														if($multipleUser == ''){
															$multipleUser .= $userNameToList['user_fullname'];
														}else{
															$multipleUser .= ', '.$userNameToList['user_fullname'];
														}
													}
												}
											}
											if($thread['to_email_address'] != ''){
												$multipleUser =  $multipleUser.', '.$thread['to_email_address'];
											}
											if($inbox['cc_email_address'] != ''){
							#					$multipleUser =  $multipleUser.', '.$inbox['cc_email_address'];
												$inbox['cc_email_address'] = getUserNameByEmailids($inbox['cc_email_address']);
											}

											$user = getuserdetails($inbox['from_id']);
											$count = threadCount($inbox['thread_id']);
											$unread = unreadCount($inbox['thread_id']); ?>
												<tr class="odd gradeA" <?php if($unread>0) { ?> style="font-weight:bold;" <?php } ?>>
													<td><input name="from[]" type="checkbox" value="<?php echo $inbox['thread_id']; ?>" /></td>
												<?php if(!isset($_GET['folderType'])){?>
													<td><a href="?sect=message_details&id=<?php echo base64_encode($inbox['thread_id']); ?>&type=inbox"><?php echo htmlentities($user['user_name'], ENT_QUOTES, 'UTF-8'); ?> </a></td>
												<?php }else{?>
													<td><a href="?sect=message_details&id=<?php echo base64_encode($inbox['thread_id']); ?>&type=inbox"><?php echo htmlentities($multipleUser, ENT_QUOTES, 'UTF-8'); ?> </a></td>
													<td><a href="?sect=message_details&id=<?php echo base64_encode($inbox['thread_id']); ?>&type=inbox"><?php echo htmlentities($inbox['cc_email_address'], ENT_QUOTES, 'UTF-8'); ?> </a></td>
												<?php }?>	
												<?php if($_GET['folderType'] != "Request For Information"){?>
													<td><a href="?sect=message_details&id=<?php echo base64_encode($inbox['thread_id']); ?>&type=inbox" style=" <?php if($count>1) {?>background:url(images/reply.png) 0 no-repeat;<?php } ?> padding-left:20px;">
														<?php $inboxData = htmlentities($inbox['title'], ENT_QUOTES, 'UTF-8'); 
														if(strlen($inboxData) > 80){
															$inboxData = substr($inboxData, 0, 80).'...';
														}
														echo $inboxData;?>
														<?php if($count>1) {?>
														(<?php echo $count;?>)
														<?php } ?>
														</a></td>
												<?php }else{?>
													<td class="center"><?php echo $inbox['rfi_number']; ?></td>
													<td class="center"><?php echo $inbox['rfi_description']; ?></td>
													<td class="center"><?php echo $inbox['rfi_status']; ?></td>
												<?php }?>	
												<?php if(!isset($_GET['folderType'])){?>
													<td class="center"><?php echo $inbox['message_type']; ?></td>
												<?php }?>
													<td class="center"><?php echo date('d/m/Y H:i:s' ,strtotime($inbox['sent_time'])); ?></td>
												<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
													<td class="center"><a onClick="delMessage(<?php echo $inbox['thread_id']; ?>);" href="#" ><img src="images/del.png" width="16" height="16" alt="Delete" title="Delete" /></a></td>
												<?php } ?>	
												</tr>
												<?php } ?>
											<?php } else { ?>
												<tr>
													<td colspan="6" class="center">You have no  messages.</td>
												</tr>
												<?php }*/ ?>
											</tbody-->
										</table>
										<input type="hidden" name="form_type" value="inbox">
									</form>
									<input type="hidden" name="viewType" id="viewType" value="<?php echo $_REQUEST['view'];?>">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<style>
div.content_container{ width:100% !important; }
div.innerModalPopupDiv{	color:#000000; }
</style>
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
var deadlock = true;//File upload
var params = "";
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
var copyStatus = false;
var copyId = '';

function generateReport(messageType){
	console.log(messageType);
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'pdf/pmb_message_summary_report.php?messageType='+messageType+'&name='+Math.random(), loadingImage);
}

var oTable = $('#inboxData').dataTable( {
	"iDisplayLength": 100,
	"bJQueryUI": true,
	"sPaginationType": "full_numbers",
	"bProcessing": true,
	"bServerSide": true,
	"bRetrieve": true,
	"sAjaxSource": "show_inbox_data_by_ajax.php?&name="+Math.random()+"&folderType=<?php echo (isset($_GET['folderType']) && $_GET['folderType'] != "")?$_GET['folderType']:''; ?>&view=<?php echo $_REQUEST['view']?>",
	"bStateSave": true,
<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
	"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 6] }],
<?php }else if($_GET['folderType']=='Consultant Advice Notice'){?>
	"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 6] }],
<?php }else if(strpos($_REQUEST['folderType'], 'Variation Claims') > -1){ ?>
	"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 5] }],
<?php }else{?>
	"aoColumnDefs": [{ "bSortable": false, "aTargets": [ 0, 5] }],
<?php }?>
<?php if($_GET['folderType']=='Consultant Advice Notice'){?>
	"aoColumns": [
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
<?php }else if(strpos($_REQUEST['folderType'], 'Variation Claims') > -1){ ?>
	"aoColumns": [
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
<?php }else if($_GET['folderType']=='Progress Claims'){?>
	"aoColumns": [
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			
<?php }else{?>
	"aoColumns": [
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
			{"sType": "html"},
<?php }?>			
<?php if(isset($_GET['folderType']) && $_GET['folderType']=="Purchaser Changes"){?>			
			{"sType": "html"},
<?php }?>
<?php if(!in_array($_SESSION['userRole'], $permArray)){?>
			{"sType": "html"}
<?php }?>
		]
} );
//oTable.fnSort( [ [1,'asc'] ] );
$('#projName').change(function(){ $('#projForm').submit(); });

function openDetailsPopup(correspondenceNumber, messageId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'getVariationClaimsData.php?name='+Math.random()+'&corresponceNo='+correspondenceNumber+'&projectId=<?php echo $_SESSION['idp'];?>&messageType=<?php echo $_GET['folderType'];?>&messageId='+messageId, loadingImage, getDateFunction);	
}

function getDateFunction(){
		new JsDatePick({
			useMode:2,
			target:"date_approved",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"date_sent",
			dateFormat:"%d-%m-%Y"
		});
}

function openDetailsPopupPC(correspondenceNumber, messageId){
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'getProgressClaimsData.php?name='+Math.random()+'&corresponceNo='+correspondenceNumber+'&projectId=<?php echo $_SESSION['idp'];?>&messageType=<?php echo $_GET['folderType'];?>&messageId='+messageId, loadingImage, getDateFunctionPC);	
}

function getDateFunctionPC(){
		new JsDatePick({
			useMode:2,
			target:"date_approved",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"date_sent",
			dateFormat:"%d-%m-%Y"
		});
}


function getVariationClaimsSave(){
	var date_approved = $('#date_approved').val();
	var date_sent = $('#date_sent').val();
	var approved = $('#approved').val();
	var sent = $('#sent').val();
	var correspondance_number = $('#correspondance_number').val();
	var messageType = $('#messageType').val();
	var projectId = $('#projectId').val();
	$.ajax({
				url: "getVariationClaimsData.php?antiqueId="+Math.random(),
				type: "POST",
				data: {sent:sent, date_sent:date_sent, date_approved:date_approved, approved: approved, correspondance_number: correspondance_number,messageType:messageType, projectId:projectId},
				success: function (res) {
					 window.location.assign("?sect=messages&folderType=Variation Claims");
					
				}
			});	
}
function getProgressClaimsSave(){
	var date_approved = $('#date_approved').val();
	var date_sent = $('#date_sent').val();
	var claimed = $('#claimed').val();
	var certified = $('#certified').val();
	var invoiced = $('#invoiced').val();
	var correspondance_number = $('#correspondance_number').val();
	var messageType = $('#messageType').val();
	var messageId = $('#messageId').val();
	var projectId = $('#projectId').val();
	$.ajax({
				url: "getProgressClaimsData.php?antiqueId="+Math.random(),
				type: "POST",
				data: {messageId:messageId, invoiced:invoiced, claimed:claimed, date_sent:date_sent, date_approved:date_approved, certified: certified, correspondance_number: correspondance_number,messageType:messageType, projectId:projectId},
				success: function (res) {
					 window.location.assign("?sect=messages&folderType=Progress Claims");
					
				}
			});	
}

function clearDateSent(){
	$('#date_sent').val('');
}
function clearDateApproved(){
	$('#date_approved').val('');
}
</script>
<script>
// On to the interactiveness now :)



$(function() {

	$('.nav a').on('click', function() {
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
			$('.tab_pane:eq('+index+')')
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

