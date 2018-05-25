<?php
session_start();
#error_reporting(E_ALL);
#ini_set('display_errors', '1');
require_once'includes/functions.php';
$obj = new DB_Class();

include('includes/commanfunction.php');
$object = new COMMAN_Class();

$offset=$_REQUEST['count'];
//echo $offset; die;
$limit=3;
#$builder_id=$_SESSION['ww_builder_id'];

$builder_id = isset($_SESSION['ww_builder_id']) ? $_SESSION['ww_builder_id'] : '';
if($builder_id != ''){
	$where = 'user_id = $builder_id ';
}
$company = isset($_SESSION['ww_is_company']) ? $_SESSION['ww_is_company'] : '' ;

if($builder_id != ''){
	$query = "select DISTINCT project_id from user_projects where is_deleted=0 and user_id=$builder_id";
}else{
	$query = "select DISTINCT project_id from user_projects where is_deleted=0";
}
$noti_record_closed = $obj->db_query($query);
$tmp_arr = array();
while($project_id=$obj->db_fetch_assoc($noti_record_closed)){
	$tmp_arr[] = $project_id["project_id"];
}
$project_ids = join(",", $tmp_arr);

$inCaluseArr = array();
foreach($tmp_arr as $key=>$projID){
	$whereConUserRole = "";
	if(!empty($_SESSION['projUserRole'])){
		if($_SESSION['projUserRole'][$projID] != 'All Defect')
			$whereConUserRole = " AND inspection_raised_by = '".$_SESSION['projUserRole'][$projID]."'";
	}
	mysql_query('SET SESSION group_concat_max_len = 4294967295');
	$inspectionData = $object->selQRYMultiple('GROUP_CONCAT(inspection_id) AS insp, project_id', 'project_inspections', 'is_deleted = 0 AND project_id = ' . $projID . $whereConUserRole);
	
	$inCaluseArr[$projID] = $inspectionData[0]['insp'];
}
$inspCondition = "";
if(!empty($inCaluseArr))	$inspCondition = " AND inspection_id IN (".trim(join(",", $inCaluseArr), ",").") ";

/******************************/
if($builder_id != ''){
	$issue = "SELECT count(*) AS count, i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE isi.project_id IN (".$project_ids.") AND i.project_id IN (".$project_ids.") AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' ".$inspCondition." GROUP BY issued_to_name ORDER BY count DESC";
}else{
	$issue = "SELECT count(*) AS count, i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE isi.project_id IN (".$project_ids.") AND i.project_id IN (".$project_ids.") AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' GROUP BY issued_to_name ORDER BY count DESC";
}
#echo $issue;die;
$i_rs=$obj->db_query($issue);
$it_total = mysql_num_rows($i_rs);	


/***********************************/
//$issueto=$obj->db_query($issue);
$issue_name= array();
$issue_close=array();
$issue_open=array();
$issue_pending=array();
$issue_fixed=array();

/*if($builder_id != ''){
	$query = "SELECT count(*) as count, issued_to_name FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0 ".$inspCondition." group by issued_to_name order by count desc limit $offset, $limit";
}else{
	$query = "SELECT count(*) as count, issued_to_name FROM `issued_to_for_inspections` as i where i.project_id in (" . $project_ids . ") and i.is_deleted=0 group by issued_to_name order by count desc limit $offset, $limit";
}*/

if($builder_id != ''){
	$query = "SELECT count(*) AS count, i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE isi.project_id IN (".$project_ids.") AND i.project_id IN (".$project_ids.") AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' ".$inspCondition." GROUP BY issued_to_name ORDER BY count DESC LIMIT ".$offset.", ".$limit;
}else{
	$query = "SELECT count(*) AS count, i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE isi.project_id IN (".$project_ids.") AND i.project_id IN (".$project_ids.") AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' GROUP BY issued_to_name ORDER BY count DESC LIMIT ".$offset.", ".$limit;
}
#echo $query;

$p_m = $obj->db_query($query);
$jqPloatissueToID = array();

while($f1=$obj->db_fetch_assoc($p_m)){
	$jqPloatissueToID[] = '"'.addslashes($f1["issued_to_name"]).'"';
	$issued_to_name = $f1["issued_to_name"];

	if($builder_id != ''){
		$issue = "SELECT SUM( IF( i.inspection_status=  'Open', 1, 0 ) ) AS open, SUM(IF( i.inspection_status=  'Fixed', 1, 0 ) ) AS fixed , SUM(IF( i.inspection_status=  'Closed', 1, 0 ) ) AS closed, SUM( IF( i.inspection_status=  'Pending', 1, 0 ) ) AS pending FROM issued_to_for_inspections i WHERE i.project_id IN (" . $project_ids . ") AND i.issued_to_name LIKE '".addslashes(trim($issued_to_name))."%' AND i.is_deleted = 0 ".$inspCondition." ";
	}else{
		$issue = "SELECT SUM( IF( i.inspection_status=  'Open', 1, 0 ) ) AS open, SUM(IF( i.inspection_status=  'Fixed', 1, 0 ) ) AS fixed , SUM(IF( i.inspection_status=  'Closed', 1, 0 ) ) AS closed, SUM( IF( i.inspection_status=  'Pending', 1, 0 ) ) AS pending FROM  issued_to_for_inspections i WHERE i.project_id IN (" . $project_ids . ") AND  i.issued_to_name LIKE '".addslashes(trim($issued_to_name))."%' AND i.is_deleted=0";
	}

/* if($builder_id != ''){
		$issue="SELECT SUM( IF( i.inspection_status=  'Open', 1, 0 ) ) AS open, SUM(IF( i.inspection_status=  'Fixed', 1, 0 ) ) AS fixed , SUM(IF( i.inspection_status=  'Closed', 1, 0 ) ) AS closed, SUM( IF( i.inspection_status=  'Pending', 1, 0 ) ) AS pending FROM  issued_to_for_inspections i where i.project_id in (" . $project_ids . ") and i.issued_to_name='" .addslashes(trim($issued_to_name)) ."' and i.is_deleted=0 ".$inspCondition." ";
	}else{
		$issue="SELECT SUM( IF( i.inspection_status=  'Open', 1, 0 ) ) AS open, SUM(IF( i.inspection_status=  'Fixed', 1, 0 ) ) AS fixed , SUM(IF( i.inspection_status=  'Closed', 1, 0 ) ) AS closed, SUM( IF( i.inspection_status=  'Pending', 1, 0 ) ) AS pending FROM  issued_to_for_inspections i  where i.project_id in (" . $project_ids . ") and i.issued_to_name='" .addslashes(trim($issued_to_name)) ."'  and i.is_deleted=0";
	}*/
	
	$issueto=$obj->db_query($issue);

	if($issue_chart=$obj->db_fetch_assoc($issueto)){
		$openF = intval($issue_chart['open']); // 100
		$closedF = intval($issue_chart['closed']); // 50
		$pendingF = intval($issue_chart['pending']); // 50
		$fixedF = intval($issue_chart['fixed']); // 50
		$total = $openF + $closedF + $pendingF + $fixedF;//200
		
		@$openP = round ( $openF * 100 / $total );
		@$closedP = round ( $closedF * 100 / $total );
		@$pendingP = round ( $pendingF * 100 / $total );
		@$fixedP = round ( $fixedF * 100 / $total );
		// print_r($f); r
		$issue_open[]= $openP;
		$issue_close[]=$closedP;
		$issue_pending[]=$pendingP;
		$issue_fixed[]=$fixedP;
		// $issue_chart['issued_to_name'];
		$issu_name_i=str_replace('"','',$issued_to_name);
		
		$lenght=strlen($issu_name_i);
		if($lenght>17){
			$proj_name_new_it = '"<a class=\"dashboradAnchor\" href=\"#\" onclick=\"newPageView(\'pms.php?sect=dashboard_detail&type=issuedTo&issuedTo='.base64_encode(addslashes($issu_name_i)).'\');\"><center>'.substr(addslashes($issu_name_i), 0, 17).'..<br/>'.$total.' inspections<center></a>"';
		}else{
			$proj_name_new_it = '"<a class=\"dashboradAnchor\" href=\"#\" onclick=\"newPageView(\'pms.php?sect=dashboard_detail&type=issuedTo&issuedTo='.base64_encode(addslashes($issu_name_i)).'\');\"><center>'.addslashes($issu_name_i).'<br/>'.$total.' inspections<center></a>"';	
		}
		$issue_name[] =  $proj_name_new_it;
	}	 
}
$issue_names = implode(',',$issue_name); 
$issue_pendings = implode(',',$issue_pending); 
$issue_opens = implode(',',$issue_open); 
$issue_closes = implode(',',$issue_close);
$issue_fixed = implode(',',$issue_fixed);

?>
<html>
    <head>
	<!--[if IE]><script language="javascript" type="text/javascript" src="../dist/excanvas.js"></script><![endif]-->
<link class="include" rel="stylesheet" type="text/css" href="dist/jquery.jqplot.min.css" />
<style type="text/css">
pre{ display:block; font:12px "Courier New", Courier, monospace; padding:10px; border:1px solid #bae2f0; background:#e3f4f9; margin:.5em 0; width:674px; }	
.graphic, #prevBtn, #nextBtn{ margin:0; padding:0; display:block; overflow:hidden; text-indent:-8000px; }
.dashboradAnchor{ cursor:pointer; color:#000000; text-decoration:none;z-index:3; }
canvas{z-index:1;}
div.jqplot-xaxis-tick{z-index:2;}
</style>
<script src="js/jquery.tools.min.js"></script>
<script class="include" type="text/javascript" src="dist/jquery.jqplot.js"></script>
 <script class="include" language="javascript" type="text/javascript" src="dist/plugins/jqplot.barRenderer.min.js"></script>
<script class="include" language="javascript" type="text/javascript" src="dist/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script class="include" language="javascript" type="text/javascript" src="dist/plugins/jqplot.pointLabels.min.js"></script>
<script class="code" type="text/javascript">
var limit = 3;
var it_end = <?php echo $offset;?>;
var it_total = <?php echo $it_total?>;
//to show previous QC
function prevIT(){
	//var div_objs = document.getElementsByClassName(div_ids);
	it_end -= limit;
	if (it_end <= 0){
		//it_end = 0;
		document.getElementById("it_previous").style.display = "none";
	}
	window.location.href="/issue_to_chart.php?count=" + it_end;
	//document.getElementById("chart2").src = "http://localhost/wiseworkers/defectid/issue_to_chart.php?count=" + it_end;
	
	var end_l = it_end + limit;
	if (end_l > it_total){
		end_l = it_total;
	}
	document.getElementById("it_row").innerHTML = "Showing " + (it_end+1) + " to " + (end_l) + " of " + it_total;
	document.getElementById("it_next").style.display = "";
}
/// to show next QC
function nextIT(){
	try{
	it_end += limit;
	window.location.href="/issue_to_chart.php?count=" + it_end;
	//document.getElementById("chart2").src = "http://localhost/wiseworkers/defectid/issue_to_chart.php?count=" + it_end;
	document.getElementById("it_previous").style.display = "block";
	var end_l = it_end + limit;
	if (end_l > it_total){
		end_l = it_total;
		document.getElementById("it_next").style.display = "none";
	}
	document.getElementById("it_row").innerHTML = "Showing " + (it_end+1) + " to " + (end_l) + " of " + it_total;
	}catch(e){
		alert(e.message);
	}
}
</script>
<script class="code" type="text/javascript">
$(document).ready(function(){
	if( it_end >=3){
		document.getElementById("it_previous").style.display = "block";
	}
	if (end_l == it_total){
		end_l = it_total;
		document.getElementById("it_next").style.display = "none";
	}
	
	var s3 = [<?php echo $issue_opens; ?>]; //For Issue to Total  Open Project for each Issue to name
	var s4 = [<?php echo $issue_closes; ?>]; //For Issue to Total Closed Project for each Issue to name
	var s5 = [<?php echo $issue_pendings; ?>]; //For Issue to Total Project for each Issue to name
	var s6 = [<?php echo $issue_fixed; ?>];
	
	var issueToArr = [<?php echo join(",", $jqPloatissueToID);?>];
	
	var ticks1 = [<?php echo $issue_names; ?>];
	var plot2 = $.jqplot('chart2', [s3, s5, s6, s4], {
        // The "seriesDefaults" option is an options object that will
        // be applied to all series in the chart.
		seriesDefaults:{
			renderer:$.jqplot.BarRenderer,
			rendererOptions: {fillToZero: true,barWidth:15}
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
	$('#chart2').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
		var statusType = "";
		var issuedTo = issueToArr[pointIndex];
		
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
			window.top.location.href = "pms.php?sect=i_defect&frm=dsb&bk=Y&isst="+issuedTo+"&sts="+statusType;
	<?php }else{?>
			window.top.location.href = "pms.php?sect=c_defect&frm=dsb&bk=Y&isst="+issuedTo+"&sts="+statusType;
	<?php }?>
//		$('#info3').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data);
	}); 
});  
function newPageView(uriLink){ window.top.location.href = uriLink; }
</script>
</head>
<body style="margin:0px;padding:0px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;margin-top:0px\9;">
		<tr>
			<td width="30px" style="padding-bottom:70px;">
				<a href="#" onClick="prevIT()"><img src="images/btn_prev.gif" id="it_previous" style="display:none;" border="0" /></a>
			</td>
			<td>
				<div id="chart2" style="width:390px; height:185px;"></div><div id="chart2" style="height:15px;"></div>
				<table width="100%" border="0" cellspacing="0" cellpadding="2" style="font-size:11px;">
					<tr>
						<tr style="font-size:11px;font-family:Verdana, Geneva, sans-serif;">
						<td id="it_row"></td>
					</tr>
					<tr>
						<td align="right" height="20px" style="font-size:11px;font-family:Verdana, Geneva, sans-serif;">
						<span style="width:20px;height:20px;height:10px\9;background:red;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Open 
						<span style="width:20px;height:20px;height:10px\9;background:yellow">&nbsp;&nbsp;&nbsp;&nbsp;</span> Pending 
						<span style="width:20px;height:20px;height:10px\9;background:#00ff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> Fixed 
						<span style="width:20px;height:20px;height:10px\9;background:#3399ff">&nbsp;&nbsp;&nbsp;&nbsp;</span> Closed
					</td>
				</tr>
			</table>
		</td>
        <td width="30px" align="center" style="padding-bottom:70px;"><a href="#" onClick="nextIT()"><img src="images/btn_next.gif" id="it_next" border="0" /></a></td>
	</tr>
</table>
</body>
<script class="code" type="text/javascript">
end_l = it_end + limit;
if (end_l > it_total){	end_l = it_total;	}

document.getElementById("it_row").innerHTML = "Showing " + (it_end+1) + " to " + (end_l) + " of " + it_total;

function newPageView(uriLink){ window.top.location.href = uriLink; }
</script>
</html>