<?php
session_start();

require_once'includes/functions.php';
$obj = new DB_Class();
$builder_id=$_SESSION['ww_builder_id'];

	if(empty($_SESSION['companyId'])){
		$q = "SELECT
				up.*,
				up.is_deleted as archieve,
				p.allow_sync,
				p.project_is_synced,
				p.company_name as companyName
			FROM
				user_projects as up,
				user as u,
				projects as p
			WHERE ";
	                if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1){
	                  $q.= "u.user_id = $builder_id AND ";
	                }  
			
			$all_projects = $_POST['all_projects'];
			$is_deleted_check = (isset($all_projects) && $all_projects == 'check') ? '(0)' : '(0,2)';

			$q.="up.user_id = u.user_id AND
				p.project_id = up.project_id AND
				p.is_deleted IN ".$is_deleted_check." AND
				up.is_deleted IN ".$is_deleted_check." AND
				u.is_deleted = 0
			GROUP BY 
				up.project_id";	
	}else{
		$q = "SELECT
				up.*,
				up.is_deleted as archieve,
				p.allow_sync,
				p.project_is_synced,
				p.company_name as companyName
			FROM
				user_projects as up,
				user as u,
				projects as p
			WHERE ";
	            
	            if(!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1){
					//$q.= "up.company_id IN(".$_SESSION['companyId'].") AND ";
					$q.= "u.user_id = $builder_id AND ";
				}
			
			$all_projects = $_POST['all_projects'];
			$is_deleted_check = (isset($all_projects) && $all_projects == 'check') ? '(0)' : '(0,2)';

			$q.="up.user_id = u.user_id AND
				p.project_id = up.project_id AND
				p.is_deleted IN ".$is_deleted_check." AND
				up.is_deleted IN ".$is_deleted_check." AND
				u.is_deleted = 0
			GROUP BY 
				up.project_id";	
	}
	?>
		 <?php //echo $q; die; ?>
			<thead>
                <tr>
					<th nowrap="nowrap">Head by</th>
					<th>Project Name</th>
					<th>Project Type</th>
					<th>Company</th>
					<th>Address</th>
					<th>Suburb</th>
					<th>State</th>
					<th>Postcode</th>
					<th>Country</th>
					<th>Archive</th>
					<th>Email on Sync</th>
					<th>Sync Project To Ipad</th>
					<th>Detail</th>
                </tr>
            </thead>
			<tbody>
	<?php 	//wordwrap($f['description'],60,"<br />\n",true);
		$r=mysql_query($q);
		
		while($f=mysql_fetch_assoc($r)){
			//if($f['sb_id']==$builder_id)$head_by=stripslashes($f['user_fullname']);else $head_by='Own';
			if($f['archieve']==2){
				$f['archieve']="Yes";
			}else{
				$f['archieve']="No";
			}
			$head_by='own';
			echo "<tr class='gradeA'>
				<td>".$head_by."</td>
				<td>".wordwrap(stripslashes($f['project_name']),40,"<br />\n",true)."</td>
				<td>".stripslashes($f['project_type'])."</td>
				<td>".stripslashes($f['companyName'])."</td>
				<td>".wordwrap(stripslashes($f['project_address_line1']),40,"<br />\n",true)."</td>
				<td>".stripslashes($f['project_suburb'])."</td>
				<td>".stripslashes($f['project_state'])."</td>
				<td>".stripslashes($f['project_postcode'])."</td>
				<td>".stripslashes($f['project_country'])."</td>
				<td>".stripslashes($f['archieve'])."</td>
				<td>".stripslashes($f['allow_sync'])."</td>
				<td>".stripslashes($f['project_is_synced'])."</td>
				<td align='center'>
					<a  title='Click to see project details' href='?sect=add_project_detail&id=".base64_encode($f['project_id'])."&hb=".base64_encode($builder_id)."' style='float:left;'><img src='images/edit.png' border='none' /></a>
				</td>
			    </tr>";
		} ?>
			</tbody>
		
	<?php exit; ?>
