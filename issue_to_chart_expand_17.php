<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();

$offset=$_REQUEST['count'];
//echo $offset; die;
$limit=3;


$builder_id=$_SESSION['ww_builder_id'];
include('includes/commanfunction.php');
$object = new COMMAN_Class();
function FillSelectBox($field, $table, $where, $group){
$q="select $field from $table where $where GROUP BY $group";
	//echo '<option value="$q">'.$q.'</option>';
	
	$q=mysql_query($q);
	while($q1=mysql_fetch_array($q)){
		?>
		<option <?php if(isset($_SESSION['project_id_issue'])) { if($_SESSION['project_id_issue'] == $q1[0]){?> selected="selected" <?php }} ?> value="<?php echo $q1[0]; ?>"><?php echo $q1[1]; ?></option>
	<?php
    }
}
function FillSelectBoxIssue($field, $table, $where, $group){
$issue_to_name="select $field from $table where $where GROUP BY $group";

	if(isset($_POST['issuedTo']))
	{
		$issue_name_ex=$_POST['issuedTo'];	
	}
	elseif(isset($_GET['it_name']))
	{
		$issue_name_ex=$_GET['it_name'];	
	}
	//echo '<option value="$q">'.$q.'</option>';
	$issue_to_name_result=mysql_query($issue_to_name);
	while($iss_to=mysql_fetch_array($issue_to_name_result)){
		?>
		<option <?php if(isset($issue_name_ex)) { if($issue_name_ex == $iss_to[0]){?> selected="selected" <?php }} ?> value="<?php echo $iss_to[0]; ?>"><?php echo $iss_to[0]; ?></option>
	<?php
    }
}

//$issueto=$obj->db_query($issue);
$issue_name= array();
$issue_close=array();
$issue_open=array();
$issue_pending=array();
$issue_fixed=array();
$where='';
if(isset($_POST['issuedTo']))
{
		$issue_name_ex=$_POST['issuedTo'];	
}
elseif(isset($_GET['it_name']))
{
		$issue_name_ex=$_GET['it_name'];	
}

if((isset($_SESSION['project_id_issue']) && !empty($_SESSION['project_id_issue'])))
{
	$where.=" and up.project_id=".$_SESSION['project_id_issue']." and i.project_id=".$_SESSION['project_id_issue']."";
}
else
{
	$where.=" and up.project_id=i.project_id" ;
}
if(isset($issue_name_ex) &&  !empty($issue_name_ex))
{
	$where.=" and  issued_to_name='".$issue_name_ex."' ";
}

if(isset($_SESSION['project_id_issue']) && !empty($_SESSION['project_id_issue']))
{
	
	$issue="SELECT count(*) as count FROM `issued_to_for_inspections` as i, `project_inspections` as pi, user_projects as up where  up.project_id=".$_SESSION['project_id_issue']." and i.project_id=".$_SESSION['project_id_issue']." and i.is_deleted=0 and up.is_deleted=0 and pi.is_deleted=0 and pi.inspection_id=i.inspection_id  and user_id=$builder_id group by issued_to_name";

 	$i_rs=$obj->db_query($issue);
	if($issu=$obj->db_fetch_assoc($i_rs)){
		$it_total = $issu["count"];
	}
	$it_total = mysql_num_rows($i_rs);	
	
	
} 
 
 
if((isset($_SESSION['project_id_issue']) && !empty($_SESSION['project_id_issue']))  || (isset($issue_name_ex)))
{
	//$project_id_new=$_POST['projName'];	
	
	$query = "SELECT count(*) as count, issued_to_name FROM `issued_to_for_inspections` as i, user_projects as up where 					up.user_id=$builder_id $where and i.is_deleted=0  and up.is_deleted=0 group by issued_to_name order by count desc limit $offset, $limit";
	
	
	

}
else
{
	$query = "SELECT count(*) as count, issued_to_name FROM `issued_to_for_inspections` as i, user_projects as up where 					up.user_id=$builder_id and up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 group by issued_to_name order by count desc limit $offset, $limit";

}


$p_m=$obj->db_query($query);
while($f1=$obj->db_fetch_assoc($p_m))
{
    $issued_to_name = $f1["issued_to_name"];
    $issue="SELECT i.issued_to_name, SUM( IF( i.inspection_status=  'Open', 1, 0 ) ) AS 
open ,SUM(IF( i.inspection_status=  'Fixed', 1, 0 ) ) AS fixed , SUM(IF( i.inspection_status=  'Closed', 1, 0 ) ) AS closed, SUM( IF( i.inspection_status=  'Pending', 1, 0 ) ) AS pending FROM project_inspections d, issued_to_for_inspections i, user_projects up where i.issued_to_name='" . $issued_to_name ."' and i.inspection_id = d.inspection_id and up.user_id=$builder_id and up.project_id=i.project_id and d.project_id=up.project_id and i.is_deleted=0 and up.is_deleted=0 and d.is_deleted=0";
//echo $issue; die;

$issueto=$obj->db_query($issue);

	if($issue_chart=$obj->db_fetch_assoc($issueto))
	{
		$openF = intval($issue_chart['open']); // 100
		$closedF = intval($issue_chart['closed']); // 50
		$pendingF = intval($issue_chart['pending']); // 50
		$fixedF = intval($issue_chart['fixed']); // 50
		$total = $openF + $closedF + $pendingF + $fixedF ;//200
		
		@$openP = round ( $openF * 100 / $total );
		@$closedP = round ( $closedF * 100 / $total );
		@$pendingP = round ( $pendingF * 100 / $total );
		@$fixedP = round ( $fixedF * 100 / $total );
		// print_r($f); r
		$issue_open[]= $openP;
		$issue_close[]=$closedP;
		$issue_pending[]=$pendingP;
		$issue_fixed[]=$fixedP;
		
		$issue_name[] =  '"<center>'.$issue_chart['issued_to_name'].'<br/>' . $total . ' inspections<center>"';
	}	 
}
$issue_names=implode(',',$issue_name); 
$issue_pendings=implode(',',$issue_pending); 
$issue_opens=implode(',',$issue_open); 
$issue_closes=implode(',',$issue_close);
$issue_fixed=implode(',',$issue_fixed);
if(empty($issue_names))
{
	$issue_names='"<center><br/> 0 inspections<center>"';;
}
if(empty($issue_pendings))
{
	$issue_pendings=0;
}

if(empty($issue_opens))
{
	$issue_opens=0;
}

if(empty($issue_closes))
{
	$issue_closes=0;
}

if(empty($issue_fixed))
{
	$issue_fixed=0;
}
?>
<html>
    <head>
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
<script src="js/jquery.tools.min.js"></script>
<script class="include" type="text/javascript" src="dist/jquery.jqplot.js"></script>
 <script class="include" language="javascript" type="text/javascript" src="dist/plugins/jqplot.barRenderer.min.js"></script>
<script class="include" language="javascript" type="text/javascript" src="dist/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script class="include" language="javascript" type="text/javascript" src="dist/plugins/jqplot.pointLabels.min.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script class="code" type="text/javascript">


$(document).ready(function(){
	
			var s3 = [<?php echo $issue_opens; ?>]; //For Issue to Total  Open Project for each Issue to name
			var s4 = [<?php echo $issue_closes; ?>]; //For Issue to Total Closed Project for each Issue to name
			var s5 = [<?php echo $issue_pendings; ?>]; //For Issue to Total Project for each Issue to name
			var s6 = [<?php echo $issue_fixed; ?>];
			var ticks1 = [<?php echo $issue_names; ?>];
			
			  var plot2 = $.jqplot('chart2', [s3, s5, s6, s4], {
        // The "seriesDefaults" option is an options object that will
        // be applied to all series in the chart.
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true,barWidth:20}
        },
        // Custom labels for the series are specified with the "label"
        // option on the series option.  Here a series option object
        // is specified for each series.
 
        axes: {
            // Use a category axis on the x axis and use our custom ticks.
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: ticks1
            },
            // Pad the y axis just a little so bars can get close to, but
            // not touch, the grid boundaries.  1.2 is the default padding.
            yaxis: {
			
                pad: 1.05,
				min: 0,
				max:100,
                tickOptions: {formatString: '%d%'}
            }
        },seriesColors: [ "#ff0000","#FFFF00", "#00ff00","#3399ff"]
    });
     });  
</script>
<script type="text/javascript">
	function changeProID(val)
	{
		var proid=document.issue_to_exp.projName.value;
		parent.projectValue(proid);
		
		
	}
	function startAjax(val)
	{
		
			AjaxShow("POST","ajaxFunctionsfor_issue.php?type=issuedTo && proID="+val,"ShowIssuedTo");
		
	//AjaxShow("POST","ajaxFunctions.php?type=priority && proID="+val,"ShowPriority");
	}

</script>
    </head>
    
<body style="margin:0px;padding:0px;">
<div align="right" style="margin-top:-10px;">




<form method="post" name="issue_to_exp" id="issue_to_exp" onSubmit="changeProID();" action="" >
	<table style="margin-top:5px;" >
		<tr>
    		<td style="font-weight:bold;font-family:Arial, Helvetica, sans-serif;font-size:12px;">
				Select Project
			</td>
			<td>
				<select name="projName" id="projName"  class="select_box" onChange="startAjax(this.value); " style="font-family:Arial, Helvetica, sans-serif;font-size:12px;">
					  <option value="">Select</option>
					<?php 
							FillSelectBox("project_id, project_name","user_projects","user_id = '".$builder_id."' and is_deleted=0","project_name");
						?>
               </select>
           </td>
           <td  style="font-weight:bold;font-family:Arial, Helvetica, sans-serif;font-size:12px;" >
           		Issued To
           </td>
		   <td  id="ShowIssuedTo" align="right">
           		<select name="issuedTo" id="issuedTo" class="select_box" style="width:150px;font-family:Arial, Helvetica, sans-serif;font-size:12px;">
                     <option value="">Select</option>
                      <?php 
							FillSelectBoxIssue("issued_to_name","issued_to_for_inspections","created_by= '".$builder_id."' and is_deleted=0","issued_to_name");
						?>
                </select>
           </td> 
           <td>
                   <input type="submit" name="go" id="go" value="Go" >			
           </td>    
       </tr>
	</table>  
 </form>                  
</div>
<div id="chart2" style="width:875px; height:300px;text-align:center;"></div>
</body>
</html>