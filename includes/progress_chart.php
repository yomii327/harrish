<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();

$offset=$_REQUEST['count'];
//echo $offset; die;
$limit=1;
		  	$prg_m="SELECT p.project_name, pm .*, SUM(pm.status = 'Behind') AS Behind, SUM(pm.status = 'On Time') AS Timeo ,SUM(pm.status = 'Ahead') AS Ahead, SUM(pm.status = 'Complete') AS Complete FROM progress_monitoring_update AS pm LEFT JOIN user_projects AS p ON ( p.project_id = pm.project_id and user_id=$builder_id) ORDER BY Behind DESC limit $offset, $limit";
		  
		  $p_monis=$obj->db_query($prg_m);
           	$behind= array();
			$timeo=array();
			$ahead=array();
			$complete=array();
			$project_name=array();
			
		   while($p_moni=$obj->db_fetch_assoc($p_monis))
		   {
			
				$behind[]= $p_moni['Behind'];
				$timeo[]=$p_moni['Timeo'];
				$ahead[]=$p_moni['Ahead'];
				$complete[]=$p_moni['Complete'];
				$project_name[]='"'.$p_moni['project_name'].'"';
			}
			$behinds=implode(',',$behind); 
			$timeos=implode(',',$timeo); 
			$aheads=implode(',',$ahead); 
			$completes=implode(',',$complete); 
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
	
				
		var s6 = [<?php echo $aheads; ?>]; //For Issue to Total Project for each Issue to name
		var s7 = [<?php echo $behinds; ?>]; //For Issue to Total Project for each Issue to name
		var s8 = [<?php echo $timeos; ?>]; //For Issue to Total Project for each Issue to name
		var s9 = [<?php echo $completes; ?>]; //For Issue to Total Project for each Issue to name
		
		var ticks2 = [<?php echo $project_names; ?>];
		var plot3 = $.jqplot('chart3', [s6, s7,s8, s9], {
        // The "seriesDefaults" option is an options object that will
        // be applied to all series in the chart.
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {fillToZero: true,  barWidth:25},
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
					
                tickOptions: {formatString: '%d'}
            }
        },seriesColors: [ "#ff0000", "#00ff00","#FFFF00","#3399FF"]
    });
     });  
</script>
    </head>
<body style="margin:0px;padding:0px;">
<div id="chart3" style="width:395px; height:195px;"></div>
</body>
</html>


