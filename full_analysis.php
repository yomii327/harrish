<?php
session_start();

require_once'includes/functions.php';
$obj = new DB_Class();

require_once'includes/commanfunction.php';
$object = new COMMAN_Class();

$offset = isset($_REQUEST['count']) ? $_REQUEST['count'] : 0;
//echo $offset; die;
$limit = 3;
#$builder_id=$_SESSION['ww_builder_id'];


$builder_id = isset($_SESSION['ww_builder_id']) ? $_SESSION['ww_builder_id'] : '';
if($builder_id != ''){
	$where = 'user_id = $builder_id ';
}
$company = isset($_SESSION['ww_is_company']) ? $_SESSION['ww_is_company'] : '' ;
if($builder_id != ''){
	$query = "select DISTINCT project_id, project_name from user_projects where is_deleted=0 and user_id=$builder_id";
}else{
	$query = "select DISTINCT project_id, project_name from projects where is_deleted=0";
}
$noti_record_closed = $obj->db_query($query);
$tmp_arr = array();
$project_names = array();
while($project_id=$obj->db_fetch_assoc($noti_record_closed)){
	$tmp_arr[] = $project_id["project_id"];
	$project_names[$project_id["project_id"]] = $project_id["project_name"];
}
	$project_ids = join(",", $tmp_arr);
	$qs = "SELECT d.project_id, count(*) as pcount FROM project_inspections AS d where d.project_id in (" . $project_ids . ") and d.inspection_type!='Memo' group by d.project_id ORDER BY pcount DESC limit ".$offset.", ".$limit;
	
	$rs=$obj->db_query($qs);
	$proj_name= array();
	$close=array();
	$open=array();
	$pending=array();
	$fixed=array();
	$jqPloatProjID = array();
	while($f1=$obj->db_fetch_assoc($rs)){
		$proj_id = $f1["project_id"];
		$jqPloatProjID[] = $f1["project_id"];
		

$whereConUserRole = "";
if(!empty($_SESSION['projUserRole'])){
	if($_SESSION['projUserRole'][$proj_id] != 'All Defect')
		$whereConUserRole = " AND inspection_raised_by = '".$_SESSION['projUserRole'][$proj_id]."'";
}
		
		$query_i = "SELECT count(*) as inspection_count FROM project_inspections where project_id = $proj_id and is_deleted=0 and inspection_type!='Memo'".$whereConUserRole;
	
		$icount=$obj->db_query($query_i);
		$f2=$obj->db_fetch_assoc($icount);
		$i_counts = $f2["inspection_count"];
		
/*
		$query = "SELECT SUM(IF(d.inspection_status= 'Open',1,0)) AS open, SUM(IF(d.inspection_status='Pending',1,0)) AS pending, SUM(IF(d.inspection_status='Fixed',1,0)) AS fixed, SUM(IF(d.inspection_status='Closed',1,0)) AS closed FROM issued_to_for_inspections AS d, project_inspections pi where pi.inspection_id=d.inspection_id and d.project_id=$proj_id and pi.project_id=$proj_id and d.is_deleted=0 and pi.inspection_type!='Memo'".$whereConUserRole;
	*/
		$query = 'SELECT SUM(IF(d.inspection_status= "Open", 1, 0)) AS open,
							SUM(IF(d.inspection_status="Pending", 1, 0)) AS pending,
							SUM(IF(d.inspection_status="Fixed", 1, 0)) AS fixed,
							SUM(IF(d.inspection_status="Closed", 1, 0)) AS closed
						FROM 
							project_inspections AS pi inner join (
								SELECT inspection_status, inspection_id
									FROM
								issued_to_for_inspections
									WHERE
								project_id = '.$proj_id.' AND
								is_deleted = 0
									GROUP BY inspection_id
							) AS d ON pi.inspection_id = d.inspection_id WHERE pi.project_id = '.$proj_id.' AND pi.inspection_type != "Memo" AND pi.is_deleted = 0'.$whereConUserRole;

		$rs1=$obj->db_query($query);
		$f=$obj->db_fetch_assoc($rs1);
	
		$openF = intval($f['open']); // 100
		$closedF = intval($f['closed']); // 50
		$pendingF = intval($f['pending']); // 50
		$fixedF = intval($f['fixed']); // 50
		$total = $openF + $closedF + $pendingF + $fixedF;//200
		
		$openP = round ( $openF * 100 / $total );
		$closedP = round ( $closedF * 100 / $total );
		$pendingP = round ( $pendingF * 100 / $total );
		$fixedP = round ( $fixedF * 100 / $total );
		// print_r($f); r
		$open[]= $openP;
		$close[]=$closedP;
		$pending[]=$pendingP;
		$fixed[]=$fixedP;
		
		 $lenght=strlen($project_names[$proj_id]);
		 if($lenght>17){
			$proj_name_new=	 '"<a class=\"dashboradAnchor\" href=\"#\" onclick=\"newPageView(\'pms.php?sect=dashboard_detail&type=location&projID='.$proj_id.'\');\"><center>'.substr($project_names[$proj_id],0,17).'..<br/>' . $i_counts . ' inspections<center></a>"';
		}else{
			$proj_name_new='"<a class=\"dashboradAnchor\" href=\"#\" onclick=\"newPageView(\'pms.php?sect=dashboard_detail&type=location&projID='.$proj_id.'\');\"><center>'.$project_names[$proj_id].'<br/>' . $i_counts . ' inspections<center></a>"';	
		}
		$proj_name[] =  $proj_name_new;
	}
	
	$pr_name = implode(',',$proj_name);
	$open_proj = implode(',',$open);
	$pending_proj = implode(',',$pending);
	$fixed_proj = implode(',',$fixed);
	$close_proj = implode(',',$close); 
?>
<html>
<head>
<!--[if IE]><script language="javascript" type="text/javascript" src="../dist/excanvas.js"></script><![endif]-->
<link class="include" rel="stylesheet" type="text/css" href="dist/jquery.jqplot.min.css" />
<style type="text/css">
pre{ display:block;font:12px "Courier New", Courier, monospace; padding:10px; border:1px solid #bae2f0; background:#e3f4f9;	 margin:.5em 0; width:674px; }	
.graphic, #prevBtn, #nextBtn{ margin:0; padding:0; display:block; overflow:hidden; text-indent:-8000px; }
.dashboradAnchor{ cursor:pointer; color:#000000; text-decoration:none;z-index:3; }
canvas{z-index:1;}
div.jqplot-xaxis-tick{z-index:2;}
table.collapse { border-collapse: collapse; border: 1pt solid black; }
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
	var s4 = [<?php echo $fixed_proj; ?>];
	var projArr = [<?php echo join(",", $jqPloatProjID);?>];
	var ticks = [<?php echo $pr_name; ?>];
	// code for Full Analysis Chart
	var plot1 = $.jqplot('chart1', [s1, s3, s4, s2], {
		// The "seriesDefaults" option is an options object that will
		// be applied to all series in the chart.
		seriesDefaults:{
			renderer:$.jqplot.BarRenderer,
			rendererOptions: {fillToZero: true,barWidth:15}
		},
		axes: {
			xaxis: {
				renderer: $.jqplot.CategoryAxisRenderer,
				ticks: ticks
			},
			yaxis: {
				pad: 1.05,
				min: 0,
				max: 100,
				tickOptions: {formatString: '%d%', showMark: true,show: true,showLabel: true}
			},
			highlighter: { show: true }
		},
		seriesColors: [ "#ff0000","#FFFF00", "#00ff00","#3399ff"]
	});
	$('#chart1').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
		var statusType = "";
		var projectID = projArr[pointIndex];
		
		switch(seriesIndex){
			case 0:
				statusType = "Open";
			break;
;
			case 1:
				statusType = "Pending";
			break;

			case 2:
				statusType = "Fixed";
			break;

			case 3:
				statusType = "Closed";
			break;
		}
	<?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] != ""){?>
			window.top.location.href = "pms.php?sect=i_defect&frm=dsb&bk=Y&pid="+projectID+"&sts="+statusType;
	<?php }else{?>
			window.top.location.href = "pms.php?sect=c_defect&frm=dsb&bk=Y&pid="+projectID+"&sts="+statusType;
	<?php }?>
	}); 
});
function newPageView(uriLink){ window.top.location.href = uriLink; }
</script>
</head>
<body style="margin:0px;padding:0px;">
<div id="chart1" style="width:395px; height:195px;"></div>
</body>
</html>