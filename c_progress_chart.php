<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();
$builder_id=$_SESSION['ww_builder_id'];
$offset=$_REQUEST['count'];
//echo $offset; die;
$limit=3;
//get list of project_name and Ids
//get list of last progress_id's and there status according to project name from progress_monitoring_updates table
$behind= array();
$timeo=array();
$ahead=array();
$complete=array();
$nostatus=array();
$project_name=array();
$signedoff = array();
$query = "SELECT p.project_name, p.project_id FROM progress_monitoring AS pm, user_projects AS p where (p.project_id = pm.project_id and p.is_deleted=0 and pm.is_deleted=0)  group by pm.project_id order by p.project_name limit $offset, $limit";
$p_m=$obj->db_query($query);
while($f1=$obj->db_fetch_assoc($p_m))
{
	     $project_id = $f1['project_id'];
	     $project_name1 = $f1['project_name'];
	     $prg_m="SELECT SUM( IF( status=  'In progress', 1, 0 ) ) AS ahead,SUM( IF( status=  'Behind', 1, 0 ) ) AS behind, SUM( IF( status=  'On Time', 1, 0 ) ) AS ontime, SUM( IF( status=  'Complete', 1, 0 ) ) AS complete, SUM( IF( status=  '', 1, 0 ) ) AS nostatus, SUM( IF( status=  'Signed off', 1, 0 ) ) AS signedoff FROM `progress_monitoring` where project_id=$project_id and is_deleted=0";
	     $p_monis=$obj->db_query($prg_m);
	     $behindF = 0;
	     $ontimeF = 0;
	     $aheadF= 0;
	     $completeF = 0;	     
	     $nostatusF = 0;	     
	     $signedoffF = 0;	     
	     
	     if($f=$obj->db_fetch_assoc($p_monis))
	     {
			  $aheadF = $f['ahead'];
			  $behindF = $f['behind'];
			  $completeF = $f['complete'];
			  $nostatusF = $f['nostatus'];
			  $signedoffF = $f['signedoff'];
	     }
	     $total = $behindF  + $aheadF + $completeF + $nostatusF + $signedoffF;//200

	     $behind[]= round ( $behindF * 100 / $total );
	     $ahead[]=round ( $aheadF * 100 / $total );
	     $complete[]=round ( $completeF * 100 / $total );
	     $nostatus[]=round ( $nostatusF * 100 / $total );
	     $signedoff[]=round ( $signedoffF * 100 / $total );
	     $project_name[] =  '"<center>'.$project_name1.'<br/>' . $total . ' tasks<center>"';
}
$behinds=implode(',',$behind); 
$aheads=implode(',',$ahead); 
$completes=implode(',',$complete); 
$nostatus1=implode(',',$nostatus); 
$signedoffs=implode(',',$signedoff); 

$project_names=implode(',',$project_name);
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
	
				
		var s6 = [<?php echo $behinds; ?>]; //For Issue to Total Project for each Issue to name
		var s7 = [<?php echo $completes; ?>]; //For Issue to Total Project for each Issue to name
		var s8 = [<?php echo $aheads; ?>]; //For Issue to Total Project for each Issue to name
		var s9 = [<?php echo $signedoffs; ?>]; //For Issue to Total Project for each Issue to name
		var s10 = [<?php echo $nostatus1; ?>]; //For Issue to Total Project for each Issue to name
		var ticks2 = [<?php echo $project_names; ?>];
		var plot3 = $.jqplot('chart3', [s6, s7,s8, s9, s10], {
        // The "seriesDefaults" option is an options object that will
        // be applied to all series in the chart.
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true,  barWidth:15},
			lineWidth: 1,
			
        },
        // Custom labels for the series are specified with the "label"
        // option on the series option.  Here a series option object
        // is specified for each series.
 
        axes: {
            // Use a category axis on the x axis and use our custom ticks.
            xaxis: {
                renderer: $.jqplot.CategoryAxisRenderer,
                ticks: ticks2,
				
				
            },
            // Pad the y axis just a little so bars can get close to, but
            // not touch, the grid boundaries.  1.2 is the default padding.
            yaxis: {
			
                pad: 1.05,
				min: 0, 
			  max: 100,				
                tickOptions: {formatString: '%d%'}
            }
        },seriesColors: [ "#ff0000", "#00ff00","#FFFF00","#3399FF","#E4E4E4"]
    });
     });  
</script>
    </head>
<body style="margin:0px;padding:0px;">
<div id="chart3" style="width:395px; height:195px;"></div>
</body>
</html>