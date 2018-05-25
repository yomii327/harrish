<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();

$builder_id=$_SESSION['ww_builder_id'];

$offset=$_REQUEST['count'];
//echo $offset; die;
$limit=3;
	$qs="SELECT p.*, SUM(IF(d.inspection_status= 'Open',1,0)) AS open, SUM(IF(d.inspection_status='Closed',1,0)) AS closed, SUM(IF(d.inspection_status='Pending',1,0)) AS pending  FROM project_inspections AS d  LEFT JOIN user_projects AS p ON ( p.project_id= d.project_id and user_id=$builder_id) GROUP BY p.project_id  ORDER BY project_id limit $offset,$limit";
$rs=$obj->db_query($qs);

          $proj_name= array();
		  $close=array();
		  $open=array();
		  $pending=array();
		  while($f=$obj->db_fetch_assoc($rs)){
			
		 // print_r($f); 
		  $open[]= $f['open'];
		   $close[]=$f['closed'];
		   $pending[]=$f['pending'];
		 $proj_name[] =  '"'.$f['project_name'].'"';
		
		 }
		
		 $pr_name=implode(',',$proj_name); 
		$open_proj=implode(',',$open); 
		$close_proj=implode(',',$close); 
		$pending_proj=implode(',',$pending); 
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
		var s1 = [<?php echo $open_proj; ?>]; //For Full Analysis Total Open Project for each Project
		var s2 = [<?php echo $close_proj; ?>]; //For Full Analysis Total Closed Project for each Project
		var s3 = [<?php echo $pending_proj; ?>]; //For Full Analysis Total Pending Project for each Project
	   
    // Can specify a custom tick Array.
    // Ticks should match up one for each y value (category) in the series.
	
    	var ticks = [<?php echo $pr_name; ?>];
    	// code for Full Analysis Chart
		var plot1 = $.jqplot('chart1', [s1, s2, s3], {
		// The "seriesDefaults" option is an options object that will
		// be applied to all series in the chart.
		seriesDefaults:{
		    renderer:$.jqplot.BarRenderer,
		    rendererOptions: {fillToZero: true,barWidth:25}
		},
			
			
		// Custom labels for the series are specified with the "label"
		// option on the series option.  Here a series option object
		// is specified for each series.
	 
		axes: {
		    // Use a category axis on the x axis and use our custom ticks.
		    xaxis: {
			renderer: $.jqplot.CategoryAxisRenderer,
			ticks: ticks
		    },
				
		    // Pad the y axis just a little so bars can get close to, but
		    // not touch, the grid boundaries.  1.2 is the default padding.
		    yaxis: {
				
			pad: 1.05,
					min: 0, 
			tickOptions: {formatString: '%d', showMark: true,show: true,showLabel: true}
		    },
				
				  highlighter: { show: true }
		},seriesColors: [ "#ff0000", "#00ff00", "#CC3232"]
	    });
                });  
</script>
    </head>
<body style="margin:0px;padding:0px;">
<div id="chart1" style="width:395px; height:195px;"></div>
</body>
</html>