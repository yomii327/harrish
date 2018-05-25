<?php if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">
		window.location.href="<?=HOME_SCREEN?>";
	</script>
<?php }
require_once'includes/commanfunction.php';
$object = new COMMAN_Class();

$builder_id=$_SESSION['ww_builder_id'];
if(isset($_REQUEST['id'])) {
	$id=base64_decode($_REQUEST['id']);
}


if(isset($_POST['button_x'])){
	if(isset($_POST['task']) && !empty($_POST['task'])){
		$task = $_POST['task'];	
	}else{
		$task_err = '<div class="error-edit-profile">The task field is required</div>';
	}

	if(isset($_POST['sdate']) && !empty($_POST['sdate'])){
		$sdate=$_POST['sdate'];	
		$date=explode('/',$sdate);
		$sdate=$date[2].'-'.$date[1].'-'.$date[0];
	}else{
		$sdate_err='<div class="error-edit-profile">The start date field is required</div>';
	}
	
	if(isset($_POST['edate']) && !empty($_POST['edate'])){
		$edate=$_POST['edate'];	
		$date=explode('/',$edate);
		$edate=$date[2].'-'.$date[1].'-'.$date[0];
	}else{
		$edate_err='<div class="error-edit-profile">The end date field is required</div>';
	}
	
	if(isset($_POST['issueto']) && !empty($_POST['issueto'])){
		$issueto_new=$_POST['issueto'];	
		$issue_to=explode(',',$issueto_new);
		$total_issue=count($issue_to);
	}else{
		$issueto_err='<div class="error-edit-profile">The issue to field is required</div>';
	}
	
	if(isset($task) || isset($sdate) || isset($edate)){
		$percentage='0%';
		$status='';
		$progress_update = "UPDATE  progress_monitoring SET task = '".addslashes(trim($task))."', start_date = '".$sdate."', end_date = '".$edate."', last_modified_date = NOW(), last_modified_by = ".$builder_id." WHERE progress_id = ".$id."";
		
#echo $progress_update; die;
		mysql_query($progress_update);
		
		$update_issue = "UPDATE issued_to_for_progress_monitoring SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE progress_id = ".$id."";			
		$issue_result=mysql_query($update_issue);
		mysql_query($update_issue);
#		echo $total_issue; die;
		if($total_issue>0){
			for($i=0;$i<$total_issue;$i++){
				$issued_to_for_progress_monitoring="INSERT INTO issued_to_for_progress_monitoring SET
									project_id = ".$_SESSION['idp'].",
									progress_id = ".$id.",
									issued_to_name = '".addslashes(trim($issue_to[$i]))."',
									last_modified_date = NOW(),
									last_modified_by = ".$builder_id.",
									created_date = NOW(),
									created_by = ".$builder_id;
				mysql_query($issued_to_for_progress_monitoring);

				$select_isseu="SELECT * FROM inspection_issue_to WHERE issue_to_name = '".$issue_to[$i]."' AND project_id = ".$_SESSION['idp']." AND is_deleted = 0";
				$result_issue=mysql_query($select_isseu);
				$issue_row=mysql_num_rows($result_issue);
				if($issue_row == 0){
					$issue_insert="INSERT INTO inspection_issue_to SET
											issue_to_name = '".addslashes(trim($issue_to[$i]))."',
											last_modified_date = NOW(),
											last_modified_by = ".$builder_id.",
											created_date = NOW(),
											created_by = ".$builder_id.",
											project_id= ".$_SESSION['idp'];	
					mysql_query($issue_insert);
				}
			}
		}
		$_SESSION['progress_update']='Progress task updated successfully.';
		header('location:?sect=progress_monitoring');
	}else{
		$_SESSION['issue_add_err']='Progress task not updated.';
	}
}

$p_sql = "SELECT progress_id, progress_id, task, start_date, end_date FROM progress_monitoring WHERE progress_id = ".$id." AND project_id = ".$_SESSION['idp']." AND is_deleted = 0";
$issu_name = "SELECT issued_to_name, issued_to_progress_monitoring_id  FROM issued_to_for_progress_monitoring WHERE (progress_id = ".$id." AND project_id = ".$_SESSION['idp']." AND is_deleted = 0)";

$issue_q = $obj->db_query($issu_name);
$issu_rows = mysql_num_rows($issue_q);
$issue_to_str = '';
if($issu_rows > 0){
	while($iedit_rows = mysql_fetch_row($issue_q)){
			$issue_to_str .= $iedit_rows[0].',';
	}
}
$issue_to_str = substr($issue_to_str, 0, (count($issue_to_str)-2));
$qd=$obj->db_query($p_sql);
$pedit_rows = mysql_fetch_array($qd);
?>
<!-- Ajax Post -->
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="js/jquery_va.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/add_edit_progress_task.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>

<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.full.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"sdate",
			dateFormat:"%d/%m/%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"edate",
			dateFormat:"%d/%m/%Y"
		});
		
	};
</script>
<style type="text/css">
@import "css/jquery.datepick.css";
table td{padding:5px;}
table{ margin-left:10px;}
.error-edit-profile { width: 220px; }
#text_show {display:none;}
#sub_location_show{display:block;}
#sub_loc_hide{display:none;}
#sub_loc_hide_sub{display:none;}
</style>
<!-- Ajax Post -->
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
</style>
<div id="middle" style="padding-bottom:80px;">
	<div id="request_send" style="display:none; text-align:center; margin-top:150px; margin-bottom:50px;"> <img src="images/request_sent.png" /> </div>
	<div id="apply_now">
		<form  method="post"  enctype="multipart/form-data" name="add_editProgress" id="add_editProgress"  >
		
			<div class="content_container">
				<div class="content_left">
					<div class="content_hd1" style="background-image:url(images/hd_edit_progess_task.png);margin-top:-50px\9;"></div>
					
					<div id="sign_in_response" style="width:900px;"></div>
					<div class="signin_form">
<table width="470" border="0" align="left" cellpadding="0" cellspacing="15" style="margin-top:-60px;">
	<tr>
		<td valign="top">Task <span class="req">*</span></td>
		<td>
			<input name="task" type="text" class="input_small" id="task" value="<?php if(isset($pedit_rows['task']) && !empty($pedit_rows['task'])) echo $pedit_rows['task'] ; ?>" />
		</td>
	</tr>
	<tr>
		<td width="133" valign="top">Start Date<span class="req">*</span></td>
		<td width="252">
		<?php if(isset($pedit_rows['start_date']) && !empty($pedit_rows['start_date'])){
				$sdate=explode('-',$pedit_rows['start_date']);
				$sdate=$sdate[2].'/'.$sdate[1].'/'.$sdate[0];
			} ?>
			<input name="sdate" type="text" class="input_small" id="sdate"  value="<?php if(isset($sdate) && !empty($sdate)) echo $sdate ; ?>" readonly="readonly"/>
		</td>
	</tr>
	<tr>
		<td valign="top">End Date <span class="req">*</span></td>
		<td>
		<?php if(isset($pedit_rows['end_date']) && !empty($pedit_rows['end_date'])){
				$edate=explode('-',$pedit_rows['end_date']);
				$edate=$edate[2].'/'.$edate[1].'/'.$edate[0];
			} ?>
			<input name="edate" type="text" class="input_small" id="edate"  value="<?php if(isset($edate) && !empty($edate)) echo $edate ; ?>" readonly="readonly"/>
		</td>
	</tr>
	<tr>
		<td valign="top">Issue To</td>
		<td>
			<input name="issueto" type="text" class="input_small" id="issueto" value="<?php if(isset($issue_to_str) && !empty($issue_to_str)) echo $issue_to_str ; ?>" />
		</td>
	</tr>							
	<tr>
		<td>&nbsp;</td>
		<td>
			<input type="hidden" value="add_project" name="sect" id="sect" />
			<input name="button" type="image" class="green_small" id="button" value="Save"/>
			<a href="javascript:history.back();" class="green_small">Back</a>
		</td>
	</tr>
</table>
					</div>
				</div>
				
			</div>
		</form>
	</div>
</div>