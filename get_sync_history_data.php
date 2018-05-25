<?php
session_start();
include_once("includes/commanfunction.php");
$obj = new COMMAN_Class();

$loginId = $_SESSION['ww_builder']['user_id'];
$tableRow = ''; 
if(isset($_POST['projectId'])){
	$userId = '';
	if($_POST['company'] == 'Y'){
		$projectId = $_POST['projectId'];	
		$users = $obj->selQRYMultiple('DISTINCT user_id', 'user_projects', 'project_id = "'.$projectId.'" AND is_deleted = 0 ORDER BY user_id DESC');
		
		foreach($users as $user){
			$syncDate = $obj->selQRYMultiple('u.user_fullname, e.created_date, e.device', 'exportData as e, user as u', 'e.created_date =  (SELECT created_date FROM exportData WHERE userid ='.$user['user_id'].' ORDER BY created_date DESC limit 0, 1) and u.user_id = e.userid');
			if(!empty($syncDate)){
				$date = date('d/m/Y', strtotime($syncDate[0]['created_date'])); 
				$tm = explode(' ', $syncDate[0]['created_date']);
				$time = date("g:i a", strtotime($tm[1]));
				$tableRow .= '<tr><td>'.$syncDate[0]['user_fullname'].'</td><td>'.$date.'<br />'.$time.'</td><td>'.$syncDate[0]['device'].'</td></tr>';
			}
		}
	}else{
		$projectId = $_POST['projectId'];	
		$users = $obj->selQRYMultiple('DISTINCT user_id', 'user_projects', 'project_id = "'.$projectId.'" AND user_id NOT IN ('.$loginId.') AND is_deleted = 0 ORDER BY user_id DESC');
		
		$syncDateLog = $obj->selQRYMultiple('u.user_fullname, e.created_date, e.device', 'exportData as e, user as u', 'e.created_date = (SELECT created_date FROM exportData WHERE userid = "'.$loginId.'" ORDER BY created_date DESC limit 0,1) and u.user_id = e.userid');
		
		if(!empty($syncDateLog)){
			$date = date('d/m/Y', strtotime($syncDateLog[0]['created_date'])); 
			$tm = explode(' ', $syncDateLog[0]['created_date']);
			$time = date("g:i a", strtotime($tm[1]));
			$tableRow .= '<tr><td>'.$syncDateLog[0]['user_fullname'].'</td><td>'.$date.'<br />'.$time.'</td><td>'.$syncDateLog[0]['device'].'</td></tr>';
		}
		foreach($users as $user){
			$syncDate = $obj->selQRYMultiple('u.user_fullname, e.created_date, e.device', 'exportData as e, user as u', 'e.created_date =  (SELECT created_date FROM exportData WHERE userid ='.$user['user_id'].' ORDER BY created_date DESC limit 0, 1) and u.user_id = e.userid');
			if(!empty($syncDate)){
				$date = date('d/m/Y', strtotime($syncDate[0]['created_date'])); 
				$tm = explode(' ', $syncDate[0]['created_date']);
				$time = date("g:i a", strtotime($tm[1]));
				$tableRow .= '<tr><td>'.$syncDate[0]['user_fullname'].'</td><td>'.$date.'<br />'.$time.'</td><td>'.$syncDate[0]['device'].'</td></tr>';
			}
		}
	}
}
if($tableRow != ''){
?>
<table width="260" border="0" cellspacing="0" cellpadding="0" class="gridtable">
	<tr>
		<td><strong>Manager&nbsp;/<br />Associate</strong></td>
		<td><strong>Sync Date</strong></td>
		<td><strong>Device Type</strong></td>
	</tr>
	<?=$tableRow;?>
</table>
<?php }else{?>
	<div>No one sync for this project</div>
<?php }?>