<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();
$owner_id = $_SESSION['ww_builder_id'];
$phd='';
$myProjects='';
$ihd='';
$myInspections='';
$qi="SELECT pl.project_id, pl.location_id, pl.location_parent_id, pl.location_title, pm.start_date,pm.end_date ,pmu.status FROM user_projects up, project_locations pl, progress_monitoring pm,progress_monitoring_update pmu where";
	$where='';$or='';
			
  if(isset($_REQUEST['SearchInsp'])){
	if(!empty($_REQUEST['projName']))
	{
		$where=" up.project_id='".$_REQUEST['projName']."' and up.project_id = pl.project_id" ;
	}
	if(!empty($_REQUEST['location']) && empty($_REQUEST['sublocation']))
	{
		$where.=" and (pl.location_id='".$_REQUEST['location']."' or pl.location_parent_id='".$_REQUEST['location']."')";
	}
	if(!empty($_REQUEST['sublocation']))
	{
		
		$where.=" and (pl.location_id='".$_REQUEST['sublocation']."')";
	}
	if(!empty($_REQUEST['status']))
	{
		$where.=" and (up.project_type='".$_REQUEST['status']."')";
	}
	if($_REQUEST['DRF']!="")
	{
		$sdate=date("Y-m-d", strtotime($_REQUEST['DRF']));
		$where.=" and (pm.start_date>='".$sdate."'";
	}
	if($_REQUEST['DRT']!="")
	{
		$sdate2=date("Y-m-d", strtotime($_REQUEST['DRT']));
		$where.=" and pm.start_date<='".$sdate2."')";
	}
	if($_REQUEST['FBDF']!="")
	{
		$edate=date("Y-m-d", strtotime($_REQUEST['FBDF']));
		$where.=" and (pm.end_date>='".$edate."'";
	}
	if($_REQUEST['FBDT']!="")
	{
		$edate=date("Y-m-d", strtotime($_REQUEST['FBDT']));
		$where.=" and pm.end_date<='".$edate."')";
	}
	$group="GROUP by pl.location_id";
	$query=$qi.$where.$group;
	$rset=$obj->db_query($query);
	?>
       	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="center"><font color="red"><h3><?php echo $_SESSION["message"]; $_SESSION["message"] = "";?></h3></font></td>
          </tr>
          <tr>
            <td valign="top"><div id="accordion" style="float:left;width:980px;">
<?php
	
	while ($row=mysql_fetch_array($rset)) {
		$loc_id=$row['location_id'];
		$parent_loc_id = $row['location_parent_id'];
		$location = $row["location_title"];
		if($parent_loc_id == 0)
		{
			$query="select max(pmu.update_id),pm.task, pm.start_date, pm.end_date,pmu.percentage,pmu.status from progress_monitoring pm, progress_monitoring_update pmu where (pm.location_id='$loc_id') and (pm.progress_id=pmu.progress_id) group by pm.progress_id";
		}else{
			$query="select max(pmu.update_id)as update_id_p from progress_monitoring pm, progress_monitoring_update pmu where (pm.sub_location_id='$loc_id') and (pm.progress_id=pmu.progress_id) group by pm.progress_id";
		}
		$rsq=$obj->db_query($query);
		
		$toggle = "display:none";
		if (mysql_num_rows($rsq)<=0)
			continue;
		
		?>
	    <h3 class="location_header" style="margin:0 0 0 0;float:center;cursor:pointer;text-align:left;width:980px;" onclick="toggleFolder('f<?php echo $loc_id?>');"><span style="text-decoration:none; color:#FFFFFF; font-weight:bold;font-size:large"><?php echo $location?></span><div style="text-align:right;float:right;margin-right:10px; margin-top:2px;z-index:!important;">Click to view</div></h3>
                <table width="960px" align="center" border="0" cellpadding="5" cellspacing="0" bgcolor="#CCCCCC" id="f<?php echo $loc_id?>" name="f<?php echo $loc_id?>" style="<?php echo $toggle?>;border:1px solid #000000;">
		<tr class="grey_header">
			<th style="color:#FFFFFF;text-align:left">Task</th>
			<th style="color:#FFFFFF;text-align:left">Date</th>
			<tH style="color:#FFFFFF;text-align:left">Percentage</th>
			<th style="color:#FFFFFF;text-align:left">Status</th>
		</tr>
          
			<?php
            while ($row1=mysql_fetch_array($rsq)) {
				$update_id = $row1["update_id_p"];
				$query="select pm.task, pm.start_date, pm.end_date,pmu.percentage,pmu.status from progress_monitoring pm, progress_monitoring_update pmu where (pmu.update_id='$update_id') and (pm.progress_id=pmu.progress_id)";
				$rsqq = $obj->db_query($query);
				$row_task = mysql_fetch_array($rsqq);
               	$start_date=date("d/m/Y", strtotime($row_task['start_date']));
		$end_date=date("d/m/Y", strtotime($row_task['end_date']));
		$status = $row_task["status"];
		$row_color = "";
		if ($status == "Ahead")
		{
			$row_color = "#00ff00";
		}else if ($status == "Behind")
		{
			$row_color = "#ff0000";
		}else if ($status == "Complete")
		{
			$row_color = "#3399ff";
		}else if ($status == "On Time")
		{
			$row_color = "yellow";
		}

			   	?>
                 <tr>     

                <td style="background:<?php echo $row_color; ?>;"><?php echo $row_task['task']; ?></td>
                <td style="background:<?php echo $row_color; ?>;"><?php echo $start_date.'<br>'.$end_date; ?></td>
                <td style="background:<?php echo $row_color; ?>;"><?php echo $row_task['percentage']; ?></td>
                <td style="background:<?php echo $row_color; ?>;"><?php echo $row_task['status']; ?></td>
                </tr>
              
                <?php
            }
			?>
		  </table><br/><br/>	 
          <?php
	}
  }
	?>
</td></tr></table>
