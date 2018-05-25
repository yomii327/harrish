<?php
session_start();
require_once'includes/functions.php';
include('includes/commanfunction.php');
$object = new COMMAN_Class();
$obj = new DB_Class();
$owner_id = $_SESSION['ww_builder_id'];
$phd='';
$myProjects='';
$ihd='';
$myInspections='';
$qi="SELECT pm.sub_location_id FROM user_projects up, progress_monitoring pm,issued_to_for_inspections as F where";
	$where='';$or='';
//print_r($_POST); die;			
  if(isset($_REQUEST['SearchInsp'])){
	 // print_r($_REQUEST); die;			
	if(!empty($_REQUEST['projName']))
	{
		$where=" up.project_id='".$_REQUEST['projName']."' and up.project_id = pm.project_id" ;
	}
	if(!empty($_REQUEST['location']) && empty($_REQUEST['sublocation']))
	{
		$where.=" and (pm.location_id='".$_REQUEST['location']."')";
	}
	if(!empty($_REQUEST['sublocation']))
	{
		
		$where.=" and (pm.sub_location_id='".$_REQUEST['sublocation']."')";
	}
	if(!empty($_REQUEST['status']))
	{
		$where.=" and (pm.status='".$_REQUEST['status']."')";
	}
	
	if($_REQUEST['DRF']!="")
	{
		$sdate=date("Y-m-d", strtotime($_REQUEST['DRF'] . "00:00:00"));
		$where.=" and (pm.start_date>='".$sdate."'";
	}
	if($_REQUEST['DRT']!="")
	{
		$sdate2=date("Y-m-d", strtotime($_REQUEST['DRT'] . "00:00:00"));
		$where.=" and pm.start_date<='".$sdate2."')";
	}
	if($_REQUEST['FBDF']!="")
	{
		$edate=date("Y-m-d", strtotime($_REQUEST['FBDF'] . "00:00:00"));
		$where.=" and (pm.end_date>='".$edate."'";
	}
	if($_REQUEST['FBDT']!="")
	{
		$edate=date("Y-m-d", strtotime($_REQUEST['FBDT'] . "00:00:00"));
		$where.=" and pm.end_date<='".$edate."')";
	}
	$group="GROUP by pm.sub_location_id";
       $query=$qi.$where . " and pm.is_deleted=0  and up.is_deleted=0 " .$group;
	//echo $query; die;
	$rset=$obj->db_query($query);
	?>
       	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
         <?php if(isset( $_SESSION["message"])) { ?>
          <tr>
            <td align="center"><font color="red"><h3><?php echo $_SESSION["message"];?></h3></font></td>
          </tr>
          <?php unset($_SESSION["message"]); } ?>
          <tr>
          
            <td valign="top">
            <div id="accordion" style="float:left;width:980px;">
<?php
	
	while ($row=mysql_fetch_array($rset)) {
		/*$loc_id=$row['location_id'];
		$parent_loc_id = $row['location_parent_id'];
		$location = $row["location_title"];*/
		$location_title = "";
		$where = "";
		$parent_loc_id = $row['sub_location_id'];
		$query1 = "select location_title from project_monitoring_locations where location_id=$parent_loc_id";
		$rset1=$obj->db_query($query1);
		$row1=mysql_fetch_array($rset1);
		  $location_title = $row1["location_title"];
		  $include_issue_to = "";
		  if(!empty($_REQUEST['issuedTo']))
		  {
			$include_issue_to = ",issued_to_for_progress_monitoring ipm";
			$where.="  and ipm.issued_to_name='".$_REQUEST['issuedTo']."' and pm.progress_id=ipm.progress_id";
		  }
		if($parent_loc_id == 0)
		{
			$query="select pm.progress_id,  pm.task, pm.start_date, pm.end_date,pm.percentage,pm.status from progress_monitoring pm $include_issue_to where (pm.location_id='$loc_id') and pm.is_deleted=0";
		}else{
			$query="select pm.progress_id, pm.task,  pm.start_date, pm.end_date,pm.percentage,pm.status from progress_monitoring pm $include_issue_to where (pm.sub_location_id='$parent_loc_id') and pm.is_deleted=0";
		}

//die;
		  if(!empty($_REQUEST['status']))
		  {
			  $where.=" and (pm.status='".$_REQUEST['status']."')";
		  }
		  
		
		  if($_REQUEST['DRF']!="")
		  {
			  $sdate=date("Y-m-d", strtotime($_REQUEST['DRF'] . "00:00:00"));
			  $where.=" and (pm.start_date>='".$sdate."'";
		  }
		  if($_REQUEST['DRT']!="")
		  {
			  $sdate2=date("Y-m-d", strtotime($_REQUEST['DRT'] . "00:00:00"));
			  $where.=" and pm.start_date<='".$sdate2."')";
		  }
		  if($_REQUEST['FBDF']!="")
		  {
			  $edate=date("Y-m-d", strtotime($_REQUEST['FBDF'] . "00:00:00"));
			  $where.=" and (pm.end_date>='".$edate."'";
		  }
		  if($_REQUEST['FBDT']!="")
		  {
			  $edate=date("Y-m-d", strtotime($_REQUEST['FBDT'] . "00:00:00"));
			  $where.=" and pm.end_date<='".$edate."')";
		  }
		  $query .= $where . " group by progress_id order by progress_id ";
			
		//echo $query; 
		//die;
		$rsq=$obj->db_query($query);
		
		$toggle = "display:none";
		if (mysql_num_rows($rsq)<=0)
			continue;
		
		?>
	    <h3 class="location_header" style="margin:0 0 0 0;float:center;cursor:pointer;text-align:left;width:980px;" onclick="toggleFolder('f<?php echo $parent_loc_id?>');"><span style="text-decoration:none; color:#FFFFFF; font-weight:bold;font-size:large"><?php echo $location_title?></span><div style="text-align:right;float:right;margin-right:10px; margin-top:2px;z-index:!important;">Click to view</div></h3>
                <table width="960px" align="center" border="0" cellpadding="5" cellspacing="0" bgcolor="#CCCCCC" id="f<?php echo $parent_loc_id?>" name="f<?php echo $parent_loc_id;?>" style="<?php echo $toggle?>;border:1px solid #000000;">
		<tr class="grey_header">
			<th style="color:#FFFFFF;text-align:left">Task</th>
              <th style="color:#FFFFFF;text-align:left">Issue to name</th>
			<th style="color:#FFFFFF;text-align:left">Date</th>
         
			<tH style="color:#FFFFFF;text-align:left">Percentage</th>
			<th style="color:#FFFFFF;text-align:left">Status</th>
		</tr>
          
			<?php
            while ($row1=mysql_fetch_array($rsq)) {
               	$start_date=date("d/m/Y", strtotime($row1['start_date'] . "00:00:00"));
		$end_date=date("d/m/Y", strtotime($row1['end_date'] . "00:00:00"));
		
		$issueToData = $object->selQRYMultiple('issued_to_name', 'issued_to_for_progress_monitoring', 'progress_id = '.$row1['progress_id'].' and is_deleted=0');
		
		if(isset($_REQUEST['issuedTo']) && !empty($_REQUEST['issuedTo'])){
			$issueToData = $object->selQRYMultiple('issued_to_name', 'issued_to_for_progress_monitoring', 'progress_id = '.$row1['progress_id'].' and is_deleted=0 and  issued_to_name = "'.$_REQUEST['issuedTo'].'"');
	
		}
		
		//print_r($issueToData);		
		$issued_to_name = "";
		foreach($issueToData as $isData)
		{
			//echo $isData['issued_to_name'];die;
			if ($issued_to_name=="")
			{
				$issued_to_name = $isData['issued_to_name'];
		
			}
			else
			{
				$issued_to_name .= "<br/>" . $isData['issued_to_name'];
			}	
		}
		
		$status = $row1["status"];
		$row_color = "";
		
		if ($status == "Complete")
		{
			$row_color = "#00ff00";
		}else if ($status == "Behind")
		{
			$row_color = "#ff0000";
		}else if ($status == "Signed off")
		{
			$row_color = "#3399ff";
		}else if ($status == "In progress")
		{
			$row_color = "yellow";
		}
		
		
		?>
                 <tr style="color:">     

                <td style="background:<?php echo $row_color; ?>;"><?php echo $row1['task']; ?></td>
                <td style="background:<?php echo $row_color; ?>;"><?php echo $issued_to_name; ?></td>
                <td style="background:<?php echo $row_color; ?>;"><?php echo $start_date.'<br>'.$end_date; ?></td>
                
                <td style="background:<?php echo $row_color; ?>;"><?php echo $row1['percentage']; ?></td>
                <td style="background:<?php echo $row_color; ?>;"><?php echo $row1['status']; ?></td>
                </tr>
              
                <?php
            }
			?>
		  </table><br/><br/>	 
          <?php
	}
  }
	?>
    </div>
</td></tr></table>
