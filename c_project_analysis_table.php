<?php


$noti_b="SELECT count(*) as due FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0  and i.inspection_status !='Closed' and i.inspection_fixed_by_date <= now()";
//echo $noti_b; die;
$noti_record=$obj->db_query($noti_b);
if($overdue=$obj->db_fetch_assoc($noti_record)){
		$overdue_total = $overdue["due"];
	}
	



$noti_one_day="SELECT count(*) as due_one FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and i.inspection_status !='Closed' and inspection_fixed_by_date = now()";
$noti_record_one=$obj->db_query($noti_one_day);
if($overdue_one_day=$obj->db_fetch_assoc($noti_record_one)){
		$overdue_one_day_total = $overdue_one_day["due_one"];
	}
	
	
$noti_seven_day="SELECT count(*) as due_seven FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and i.inspection_status !='Closed' and inspection_fixed_by_date >= CURDATE() - INTERVAL 7 DAY and inspection_fixed_by_date <= CURDATE()";

$noti_record_seven=$obj->db_query($noti_seven_day);
if($overdue_seven_day=$obj->db_fetch_assoc($noti_record_seven)){
		$overdue_one_seven_total = $overdue_seven_day["due_seven"];
	}	
	
	
$noti_14_day="SELECT count(*) as due_14 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0 and i.inspection_status !='Closed' and inspection_fixed_by_date >= CURDATE() - INTERVAL 14 DAY and inspection_fixed_by_date <= CURDATE()";

$noti_record_14=$obj->db_query($noti_14_day);
if($overdue_14_day=$obj->db_fetch_assoc($noti_record_14)){
		$overdue_14_days_total = $overdue_14_day["due_14"];
	}		



$noti_21_day="SELECT count(*) as due_21 FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0  and i.inspection_status !='Closed' and inspection_fixed_by_date >= CURDATE() - INTERVAL 21 DAY and inspection_fixed_by_date <= CURDATE()";

$noti_record_21=$obj->db_query($noti_21_day);
if($overdue_21_day=$obj->db_fetch_assoc($noti_record_21)){
		$overdue_21_days_total = $overdue_21_day["due_21"];
	}	


$noti_closed="SELECT count(*) as total_row,Sum((To_days( i.closed_date ) - TO_DAYS( p.inspection_date_raised ))) as difference FROM `issued_to_for_inspections` as i, user_projects as up,project_inspections p where p.inspection_id=i.inspection_id and up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and i.is_deleted=0  and i.inspection_status ='Closed'";
//echo $noti_closed; die;
$noti_record_closed=$obj->db_query($noti_closed);
if($overdue_closed=$obj->db_fetch_assoc($noti_record_closed)){
		$overdue_closed_total = $overdue_closed["difference"];
		$overdue_closed_rows_total = $overdue_closed["total_row"]; 
	}	





//************************************************************************************/
			/* QUERY FOR FULL ANALYSIS CHART*/
//************************************************************************************/		
	$qs="SELECT COUNT(DISTINCT d.project_id) as count FROM project_inspections AS d, user_projects as p where ( p.project_id = d.project_id and p.is_deleted=0 and d.is_deleted=0)";
	$rs=$obj->db_query($qs);
	if($f=$obj->db_fetch_assoc($rs)){
		$qc_total = $f["count"];
	}
	
	
	/* END CODE FOR QUALITY CONTROL TO*/
//************************************************************************************/
			/* QUERY FOR ISSUE TO CHART*///************************************************************************************/
	$issue="SELECT count(*) as count FROM `issued_to_for_inspections` as i, `project_inspections` as pi, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 and pi.is_deleted=0 and pi.inspection_id=i.inspection_id group by issued_to_name";
 	$i_rs=$obj->db_query($issue);
	$it_total = mysql_num_rows($i_rs);
		
		/*END CODE FOR ISSUE TO */
//************************************************************************************/
			/* QUERY FOR PROGRESS MONITORING CHART*/
//************************************************************************************/		
	//Chart for Progress Monitoring
		
	$prg_m="SELECT count(DISTINCT pm.project_id) as pmcount FROM progress_monitoring AS pm, user_projects AS p where ( p.project_id = pm.project_id and p.is_deleted=0 and pm.is_deleted=0)";
	
		  	$pm_rs=$obj->db_query($prg_m);
	if($pm=$obj->db_fetch_assoc($pm_rs)){
		//echo 'Hi'; die;
		$pm_total = $pm["pmcount"];
	}
		    
/* END PHP CODE FOR PROGRESS MONITORING*/

?>
<link href="dashboard.css" rel="stylesheet" type="text/css" /> 
<link href="maxChart/style/style.css" rel="stylesheet" type="text/css" />
<link class="include" rel="stylesheet" type="text/css" href="dist/jquery.jqplot.min.css" />
<style type="text/css">


	pre{
		display:block;
		font:12px "Courier New", Courier, monospace;
		padding:10px;
		border:1px solid #bae2f0;
		background:#e3f4f9;	
		margin:.5em 0;
		width:674px;
		}	
			
    /* image replacement */
        .graphic, #prevBtn, #nextBtn{
            margin:0;
            padding:0;
            display:block;
            overflow:hidden;
            text-indent:-8000px;
            }
    /* // image replacement */
			
</style>
<style type="text/css">
table.gridtable {
	border-width: 1px;
	border-color: #FFF;
	border-collapse: collapse;
}
table.gridtable td {
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #FFF;
}
</style>


</script>

<?php

?>

<script language="javascript">
var limit = 3;
var qc_end = 0;
var qc_total = <?php echo $qc_total?>;

//to show previous QC
function prevQC()
{
	qc_end -= limit;
	if (qc_end <= 0)
	{
		qc_end = 0;
		document.getElementById("qc_previous").style.display = "none";
	}
	document.getElementById("chart1").src = "/c_full_analysis.php?count=" + qc_end;
	var end_l = qc_end + limit;
	if (end_l > qc_total)
	{
		end_l = qc_total;
	}
	document.getElementById("qc_row").innerHTML = "Showing " + (qc_end+1) + " to " + (end_l) + " of " + qc_total;
	document.getElementById("qc_next").style.display = "";
}
/// to show next QC
function nextQC()
{
	try{
	qc_end += limit;
	document.getElementById("chart1").src = "/c_full_analysis.php?count=" + qc_end;
	document.getElementById("qc_previous").style.display = "";

	var end_l = qc_end + limit;
	if (end_l > qc_total)
	{
		end_l = qc_total;
		document.getElementById("qc_next").style.display = "none";
	}
	document.getElementById("qc_row").innerHTML = "Showing " + (qc_end+1) + " to " + (end_l) + " of " + qc_total;

	}catch(e){
		alert(e.message);
	}
}
//////////////////////////PROGRESS MONITORING///////////////////////////
var pm_end = 0;
var pm_total = <?php echo $pm_total?>;
//to show previous PM
function prevPM()
{
	pm_end -= limit;
	if (pm_end <= 0)
	{
		pm_end = 0;
		document.getElementById("pm_previous").style.display = "none";
	}
	document.getElementById("chart2_if").src = "/c_progress_chart.php?count=" + pm_end;
	var end_l = pm_end + limit;
	if (end_l > pm_total)
	{
		end_l = pm_total;
	}
	document.getElementById("pm_row").innerHTML = "Showing " + (pm_end+1) + " to " + (end_l) + " of " + pm_total;
	document.getElementById("pm_next").style.display = "";
}
/// to show next QC
function nextPM()
{
	try{
	pm_end += limit;
	document.getElementById("chart2_if").src = "/c_progress_chart.php?count=" + pm_end;
	document.getElementById("pm_previous").style.display = "";
	var end_l = pm_end + limit;
	if (end_l > pm_total)
	{
		end_l = pm_total;
		document.getElementById("pm_next").style.display = "none";
	}
	document.getElementById("pm_row").innerHTML = "Showing " + (pm_end+1) + " to " + (end_l) + " of " + pm_total;
	}catch(e){
		alert(e.message);
	}
}
//////////////////////ISSUED TO/////////////////////
var it_end = 0;
var it_total = <?php echo $it_total?>;
//to show previous QC
function prevIT()
{
	it_end -= limit;
	if (it_end <= 0)
	{
		it_end = 0;
		document.getElementById("it_previous").style.display = "none";
	}
	document.getElementById("chart3_if").src = "/c_issue_to_chart.php?count=" + it_end;
	var end_l = it_end + limit;
	if (end_l > it_total)
	{
		end_l = it_total;
	}
	document.getElementById("it_row").innerHTML = "Showing " + (it_end+1) + " to " + (end_l) + " of " + it_total;
	document.getElementById("it_next").style.display = "";
}
/// to show next QC
function nextIT()
{
	try{
	it_end += limit;
	document.getElementById("chart3_if").src = "/c_issue_to_chart.php?count=" + it_end;
	document.getElementById("it_previous").style.display = "";
	var end_l = it_end + limit;
	if (end_l > it_total)
	{
		end_l = it_total;
		document.getElementById("it_next").style.display = "none";
	}
	document.getElementById("it_row").innerHTML = "Showing " + (it_end+1) + " to " + (end_l) + " of " + it_total;
	}catch(e){
		alert(e.message);
	}
}

///next button handling at document load
$(document).ready(function(){
	//QC
	if ((qc_end+limit) >= qc_total)
	{
		document.getElementById("qc_next").style.display = "none";
	}
	var end_l = qc_end + limit;
	if (end_l > qc_total)
	{
		end_l = qc_total;
	}
	document.getElementById("qc_row").innerHTML = "Showing " + (qc_end+1) + " to " + (end_l) + " of " + qc_total;
	
	//PM
	if ((pm_end+limit) >= pm_total)
	{
		document.getElementById("pm_next").style.display = "none";
	}
	end_l = pm_end + limit;
	if (end_l > pm_total)
	{
		end_l = pm_total;
	}
	document.getElementById("pm_row").innerHTML = "Showing " + (pm_end+1) + " to " + (end_l) + " of " + pm_total;
	
	//IT
	if ((it_end+limit) >= it_total)
	{
		document.getElementById("it_next").style.display = "none";
	}
	end_l = it_end + limit;
	if (end_l > it_total)
	{
		end_l = it_total;
	}
	document.getElementById("it_row").innerHTML = "Showing " + (it_end+1) + " to " + (end_l) + " of " + it_total;
});
</script>
<div class="search_multiple">
	<div class="first_box"> <!--Start Code For Full Analysis Box-->
		<h1><img src="images/analysis_big.png" width="35" height="43" align="absmiddle" /> Quality Control Summary</h1>
        
		 <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:0px;">
         <tr><td width="30px">
        <a href="#" onclick="prevQC()"><img src="images/btn_prev.gif" id="qc_previous" style="display:none"/></a>
          </td>
          <td>
		    <iframe src="/c_full_analysis.php?count=0" id="chart1" style="width:400px; height:200px;margin:0px;padding:0px;border:0px;"></iframe>
			<table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;">
				<tr>
					<td id="qc_row"></td>
				</tr>
				<tr>
					
					<td align="right"><span style="width:20px;height:20px;background:red;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Open <span style="width:20px;height:20px;background:yellow">&nbsp;&nbsp;&nbsp;&nbsp;</span> Pending <span style="width:20px;height:20px;background:#00ff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> Fixed <span style="width:20px;height:20px;background:#3399ff">&nbsp;&nbsp;&nbsp;&nbsp;</span> Closed </td>
				</tr>
			</table>
            </td>
            <td width="30px"><a href="#" onclick="nextQC()"><img src="images/btn_next.gif" id="qc_next"/></a></td>
            </tr>
            </table>
	</div>  
    <!--End Code For Full Analysis Box-->
    
     <!--Start Code For Issue To  Box-->
<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>

<div class="first_box" style="margin-left:5px;">  
		<h1><img src="images/progress_monitor_big.png" width="43" height="40" align="absmiddle" /> Progress Monitor Summary</h1>
		 <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:0px;">
         <tr><td width="30px">
        <a href="#" onclick="prevPM()"><img src="images/btn_prev.gif" id="pm_previous" style="display:none"/></a>
          </td>
          <td>
		    <iframe src="/c_progress_chart.php?count=0" id="chart2_if" style="width:400px; height:200px;margin:0px;padding:0px;border:0px;"></iframe>
			<table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;">
				<tr>
					<td id="pm_row"></td>
				</tr>
		<tr>
			<td align="left" style="font-size:10px;"><span style="width:20px;height:20px;background:#ff0000;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Behind <span style="width:20px;height:20px;background:#00ff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> Complete <span style="width:20px;height:20px;background:#ffff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> In progress <span style="width:20px;height:20px;background:#3399ff">&nbsp;&nbsp;&nbsp;&nbsp;</span> Signed off  <span style="width:20px;height:20px;background:#E4E4E4">&nbsp;&nbsp;&nbsp;&nbsp;</span> Not Started </td>
		</tr>
			</table>
            </td>
            <td width="30px"><a href="#" onclick="nextPM()"><img src="images/btn_next.gif" id="pm_next"/></a></td>
            </tr>
            </table>
</div>

<?}?>
    <!--End Code For Issue To  Box-->   
    
    <!--Start Code For Progress Task Monitoring Box--> 
    
    <div class="first_box"> 
		<h1><img src="images/Issued_to.png" width="43" height="40" align="absmiddle" /> Issue To Summary (Trades and Contractors)</h1>
        	  <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;">
         <tr><td width="30px">
        <a href="#" onclick="prevIT()"><img src="images/btn_prev.gif" id="it_previous" style="display:none"/></a>
          </td>
          <td>
		    <iframe src="/c_issue_to_chart.php?count=0" id="chart3_if" style="width:400px; height:200px;margin:0px;padding:0px;border:0px;"></iframe>
		<table width="100%" border="0" cellspacing="0" cellpadding="2" style="font-size:11px;">
            <tr>
				<tr>
					<td id="it_row"></td>
				</tr>
				<tr>
					
					<td align="right"><span style="width:20px;height:20px;background:red;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Open <span style="width:20px;height:20px;background:yellow">&nbsp;&nbsp;&nbsp;&nbsp;</span> Pending <span style="width:20px;height:20px;background:#00ff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> Fixed <span style="width:20px;height:20px;background:#3399ff">&nbsp;&nbsp;&nbsp;&nbsp;</span> Closed </td>
                    
				</tr>
            </tr>
		</table>
         </td>
            <td width="30px"><a href="#" onclick="nextIT()"><img src="images/btn_next.gif" id="it_next"/></a></td>
            </tr>
            </table>
</div>
<!--End Code For Progress Task Monitoring Box--> 
<div class="first_box" style="margin-left:5px;">  
		<h1><img src="images/progress_monitor_big.png" width="43" height="40" align="absmiddle" /> Notification Board</h1>
		 <table width="80%" border="0" cellspacing="0" cellpadding="2" style="margin-top:0px;margin-left:20px" class="gridtable">
         	 <tr>
                <th colspan="2" width="40%" align="left">Inspections Due</th>
                <th width="60%">&nbsp;</th>
             </tr>
             <tr>
                <td>Overdue</td>
                <td><?php echo  $overdue_total; ?></td>
                <td rowspan="5" align="center" valign="middle"><h1 style="color:#000;">Average Time to close Inspections(days)<br/><br/><?php echo number_format($overdue_closed_total/$overdue_closed_rows_total, 2, '.', '');?></h1></td>
             </tr>
             <tr>
                <td>Due in 1 day</td>
                <td><?php echo  $overdue_one_day_total; ?></td>
             </tr>
             <tr>
                <td>Due in 7 days</td>
               <td><?php echo  $overdue_one_seven_total; ?></td>
          	</tr>
             <tr>
                <td>Due in 14 days</td>
               <td><?php echo  $overdue_14_days_total; ?></td>
          	</tr>
             <tr>
                <td>Due in 21 days</td>
               <td><?php echo  $overdue_21_days_total; ?></td>
          	</tr>
          </table>
</div>
  
</div>
    <br/>
    <br/>
<!--End  Code For All  Box--> 
