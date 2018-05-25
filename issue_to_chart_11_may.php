<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();

$offset=$_REQUEST['count'];
//echo $offset; die;
$limit=3;
$builder_id=$_SESSION['ww_builder_id'];

$issueto=$obj->db_query($issue);
$issue_name= array();
$issue_close=array();
$issue_open=array();
$issue_pending=array();
$issue_fixed=array();

$query = "SELECT count(*) as count, issued_to_name FROM `issued_to_for_inspections` as i, user_projects as up where up.user_id=$builder_id and up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 group by issued_to_name order by count desc limit $offset, $limit";
$p_m=$obj->db_query($query);
while($f1=$obj->db_fetch_assoc($p_m))
{
    $issued_to_name = $f1["issued_to_name"];
    //$issue="SELECT SUM( IF( d.inspection_status=  'Open', 1, 0 ) ) AS open , SUM( IF( d.inspection_status=  'Closed', 1, 0 ) ) AS closed, SUM( IF( d.inspection_status=  'Pending', 1, 0 ) ) AS pending FROM project_inspections d where inspection_id in ($value)";
    /*$issue="SELECT i.issued_to_name, SUM( IF( d.inspection_status=  'Open', 1, 0 ) ) AS 
open , SUM( IF( d.inspection_status=  'Closed', 1, 0 ) ) AS closed, SUM( IF( d.inspection_status=  'Pending', 1, 0 ) ) AS pending FROM project_inspections d, issued_to_for_inspections i, user_projects up where i.issued_to_name='" . $issued_to_name ."' and i.inspection_id = d.inspection_id and up.user_id=$builder_id and up.project_id=i.project_id and d.project_id=up.project_id and i.is_deleted=0 and up.is_deleted=0 and d.is_deleted=0";


*/    

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
		
		$openP = round ( $openF * 100 / $total );
		$closedP = round ( $closedF * 100 / $total );
		$pendingP = round ( $pendingF * 100 / $total );
		$fixedP = round ( $fixedF * 100 / $total );
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
    </head>
<body style="margin:0px;padding:0px;">
<div id="chart2" style="width:395px; height:195px;"></div>
</body>
</html>