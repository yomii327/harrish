<?php
session_start();
#error_reporting(E_ALL);
#ini_set('display_errors', '1');
require_once'includes/functions.php';
$obj = new DB_Class();

include('includes/commanfunction.php');
$object = new COMMAN_Class();

$offset=$_REQUEST['count'];

$limit=3;

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
if(!empty($inCaluseArr))	$inspCondition = " AND inspection_id IN (".join(",", $inCaluseArr).") ";

function FillSelectBox($field, $table, $where, $group){
$q="select $field from $table where $where GROUP BY $group";
//echo '<option value="$q">'.$q.'</option>';
$q=mysql_query($q);
while($q1=mysql_fetch_array($q)){ ?>
	<option <?php if(isset($_REQUEST['projName'])) { if($_REQUEST['projName']== $q1[0]){?> selected="selected" <?php }} ?> value="<?php echo $q1[0]; ?>"><?php echo $q1[1]; ?></option>
<?php }
}
$jqPloatissueToID = array();
function FillSelectBoxIssue(){
//$issue_to_name="select $field from $table where $where GROUP BY $group";
///Selected Data in List
if($builder_id != ''){
	if(isset($_REQUEST['projName']) && !empty($_REQUEST['projName'])){
		$issue_to_name = "SELECT i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE isi.project_id = '".$_REQUEST['projName']."' AND i.project_id = '".$_REQUEST['projName']."' AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' GROUP BY issue_to_name ORDER BY issue_to_name";
		
		#$issue_to_name="SELECT issued_to_name FROM issued_to_for_inspections as i WHERE i.project_id ='".$_REQUEST['projName']."' AND i.is_deleted =0 GROUP BY issued_to_name";
	}else{
		$issue_to_name = "SELECT count(*) AS count, i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE isi.project_id IN (".$project_ids.") AND i.project_id IN (".$project_ids.") AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' ".$inspCondition." GROUP BY issue_to_name ORDER BY issue_to_name";
		
		#$issue_to_name="SELECT issued_to_name FROM issued_to_for_inspections as i WHERE i.project_id in (".$project_ids.") AND i.is_deleted =0 GROUP BY issued_to_name";
	} 
}else{
	if(isset($_REQUEST['projName']) && !empty($_REQUEST['projName'])){
		$issue_to_name = "SELECT i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE isi.project_id = '".$_REQUEST['projName']."' AND i.project_id = '".$_REQUEST['projName']."' AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' GROUP BY issue_to_name ORDER BY issue_to_name";
		
		#$issue_to_name="SELECT issued_to_name FROM issued_to_for_inspections as i WHERE i.project_id ='".$_REQUEST['projName']."' AND i.is_deleted =0 GROUP BY issued_to_name";
	}else{
		$issue_to_name = "SELECT count(*) AS count, i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE isi.project_id IN (".$project_ids.") AND i.project_id IN (".$project_ids.") AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' ".$inspCondition." GROUP BY issue_to_name ORDER BY issue_to_name";

		#$issue_to_name="SELECT issued_to_name FROM issued_to_for_inspections as i WHERE i.project_id in (".$project_ids.") AND i.is_deleted =0 GROUP BY issued_to_name";
	} 
}
if(isset($_REQUEST['issuedTo'])){
	$issue_name_ex=$_REQUEST['issuedTo'];	
}elseif(isset($_GET['it_name'])){
	$issue_name_ex=$_GET['it_name'];	
}
//echo '<option value="$q">'.$q.'</option>';

$issue_to_name_result=mysql_query($issue_to_name);
while($iss_to=mysql_fetch_array($issue_to_name_result)){
	$jqPloatissueToID[] = '"'.addslashes($f1["issued_to_name"]).'"'; ?>
	<option <?php if(isset($issue_name_ex)) { if($issue_name_ex == $iss_to[0]){?> selected="selected" <?php }} ?> value="<?php echo $iss_to[0]; ?>"><?php echo $iss_to[0]; ?></option>
<?php }
}

//$issueto=$obj->db_query($issue);
$issue_name = array();
$issue_close = array();
$issue_open = array();
$issue_pending = array();
$issue_fixed = array();
$where='';


if(isset($_REQUEST['issuedTo'])){
	$issue_name_ex=$_REQUEST['issuedTo'];	
}elseif(isset($_GET['it_name'])){
	$issue_name_ex=$_GET['it_name'];	
}
 
if((isset($_REQUEST['projName']) && !empty($_REQUEST['projName']))){
	$where=" i.project_id=".$_REQUEST['projName']."";
}else{
	$where=" i.project_id in (" . $project_ids .")" ;
}
if(isset($issue_name_ex) &&  !empty($issue_name_ex)){
	if ($where == "")
		$where.=" issued_to_name LIKE '".$issue_name_ex."%' ";
	else
		$where.=" and issued_to_name LIKE '".$issue_name_ex."%' ";
}

if($builder_id != ''){
	if(isset($_REQUEST['projName']) && !empty($_REQUEST['projName']) || (isset($issue_name_ex))){
		$issue="SELECT count(*) as count FROM `issued_to_for_inspections` as i where  $where and i.is_deleted=0 ".$inspCondition." group by issued_to_name";
		$i_rs=$obj->db_query($issue);
		if($issu=$obj->db_fetch_assoc($i_rs)){
			$it_total = $issu["count"];
		}
		$it_total = mysql_num_rows($i_rs);	
	} 
}else{
	if(isset($_REQUEST['projName']) && !empty($_REQUEST['projName']) || (isset($issue_name_ex))){
		$issue="SELECT count(*) as count FROM `issued_to_for_inspections` as i where $where and i.is_deleted=0 group by issued_to_name";
		$i_rs=$obj->db_query($issue);
		if($issu=$obj->db_fetch_assoc($i_rs)){
			$it_total = $issu["count"];
		}
		$it_total = mysql_num_rows($i_rs);	
	} 
}

if($builder_id != ''){
	if((isset($_REQUEST['projName']) && !empty($_REQUEST['projName']))  || (isset($issue_name_ex))){
		$query = "SELECT count(*) AS count, i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE ". $where ." AND isi.project_id IN (".$project_ids.") AND i.project_id IN (".$project_ids.") AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' ".$inspCondition." GROUP BY issue_to_name ORDER BY count DESC LIMIT ".$offset.", ".$limit;
		
		#$query = "SELECT count(*) as count, issued_to_name FROM `issued_to_for_inspections` as i where $where and i.is_deleted=0 ".$inspCondition." group by issued_to_name order by count desc limit $offset, $limit";
	}else{
		$query = "SELECT count(*) AS count, i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE isi.project_id IN (".$project_ids.") AND i.project_id IN (".$project_ids.") AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' ".$inspCondition." GROUP BY issue_to_name ORDER BY count DESC LIMIT ".$offset.", ".$limit;
			
		#$query = "SELECT count(*) as count, issued_to_name FROM `issued_to_for_inspections` as i where i.is_deleted=0 ".$inspCondition." group by issued_to_name order by count desc limit $offset, $limit";
	}
}else{
	if((isset($_REQUEST['projName']) && !empty($_REQUEST['projName']))  || (isset($issue_name_ex))){
		$query = "SELECT count(*) AS count, i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE ". $where ." AND isi.project_id IN (".$project_ids.") AND i.project_id IN (".$project_ids.") AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' GROUP BY issue_to_name ORDER BY count DESC LIMIT ".$offset.", ".$limit;
		#$query = "SELECT count(*) as count, issued_to_name FROM `issued_to_for_inspections` as i where $where and i.is_deleted=0 group by issued_to_name order by count desc limit $offset, $limit";
	}else{
		$query = "SELECT count(*) AS count, i.issue_to_name AS issued_to_name FROM inspection_issue_to AS i, issued_to_for_inspections AS isi WHERE isi.project_id IN (".$project_ids.") AND i.project_id IN (".$project_ids.") AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' GROUP BY issue_to_name ORDER BY count DESC LIMIT ".$offset.", ".$limit;
		#$query = "SELECT count(*) as count, issued_to_name FROM `issued_to_for_inspections` as i where i.is_deleted=0 group by issued_to_name order by count desc limit $offset, $limit";
	}
}

$p_m=$obj->db_query($query);
//echo $row_count=mysql_num_rows($p_m);
//if()
while($f1=$obj->db_fetch_assoc($p_m)){
    
	if((isset($_REQUEST['projName']) && !empty($_REQUEST['projName']))){
		$where=" and i.project_id=".$_REQUEST['projName']."";
	}else{
		$where=" and i.project_id in (" . $project_ids . ")" ;
	}
	
	$issued_to_name = $f1["issued_to_name"];
if($builder_id != ''){
     $issue="SELECT
	  			i.issued_to_name,
				SUM( IF( i.inspection_status=  'Open', 1, 0 ) ) AS open,
				SUM(IF( i.inspection_status=  'Fixed', 1, 0 ) ) AS fixed,
				SUM(IF( i.inspection_status=  'Closed', 1, 0 ) ) AS closed,
				SUM( IF( i.inspection_status=  'Pending', 1, 0 ) ) AS pending
			FROM
				issued_to_for_inspections i where i.issued_to_name LIKE '".$issued_to_name."%' 
			$where and
				i.is_deleted=0  ".$inspCondition." ";
}else{
     $issue="SELECT
	  			i.issued_to_name,
				SUM( IF( i.inspection_status=  'Open', 1, 0 ) ) AS open,
				SUM(IF( i.inspection_status=  'Fixed', 1, 0 ) ) AS fixed,
				SUM(IF( i.inspection_status=  'Closed', 1, 0 ) ) AS closed,
				SUM( IF( i.inspection_status=  'Pending', 1, 0 ) ) AS pending
			FROM
				issued_to_for_inspections i where i.issued_to_name LIKE '".$issued_to_name."%'
			$where and
				i.is_deleted=0";
}

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
		$issue_open[]= $openF;
		$issue_close[]=$closedF;
		$issue_pending[]=$pendingF;
		$issue_fixed[]=$fixedF;
		
		$issue_name[] =  '"<a class=\"dashboradAnchor\" href=\"#\" onclick=\"newPageView(\'pms.php?sect=dashboard_detail&type=issuedTo&issuedTo='.base64_encode(addslashes($issue_chart['issued_to_name'])).'\');\"><center>'.$issue_chart['issued_to_name'].'<br/>' . $total . ' inspections<br/>Open: ' . $openF .' Pending: ' . $pendingF . ' Fixed: ' . $fixedF . ' Closed: ' . $closedF . ' <center></a>"';
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
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script class="code" type="text/javascript">
var limit = 3;
var it_end = <?php echo $offset;?>;
var it_total = <?php echo $it_total?>;
//to show previous QC
function prevIT(obj1)
{
	//var div_objs = document.getElementsByClassName(div_ids);
	var project = document.getElementById("projName").value;
	var issueTo = document.getElementById("issuedTo").value;
	
	it_end -= limit;
	if (it_end <= 0)
	{
		it_end = 0;
		document.getElementById("it_previous").style.display = "none";
	}
	window.location.href="/issue_to_chart_expand.php?count=" + it_end+"&projName=" + project +"&issuedTo="+issueTo;
	
	//document.getElementById("chart3_if").src = "http://localhost/wiseworkers/defectid/issue_to_chart.php?count=" + it_end;
	var end_l = it_end + limit;
	if (end_l > it_total)
	{
		end_l = it_total;
	}
	document.getElementById("it_row").innerHTML = "Showing " + (it_end+1) + " to " + (end_l) + " of " + it_total;
	document.getElementById("it_next").style.display = "";
}
/// to show next QC
function nextIT(obj1)
{
	var project = document.getElementById("projName").value;
	var issueTo = document.getElementById("issuedTo").value;
	try{
	it_end += limit;
	window.location.href="/issue_to_chart_expand.php?count=" + it_end+"&projName=" + project +"&issuedTo="+issueTo;
	document.getElementById("it_previous").style.display = "block";
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
</script>
<script class="code" type="text/javascript">


$(document).ready(function(){
	if( it_end >=3)		document.getElementById("it_previous").style.display = "block";
	
	if (end_l == it_total){
		end_l = it_total;
		document.getElementById("it_next").style.display = "none";
	}
	var s3 = [<?php echo $issue_opens; ?>]; //For Issue to Total  Open Project for each Issue to name
	var s4 = [<?php echo $issue_closes; ?>]; //For Issue to Total Closed Project for each Issue to name
	var s5 = [<?php echo $issue_pendings; ?>]; //For Issue to Total Project for each Issue to name
	var s6 = [<?php echo $issue_fixed; ?>];
	var ticks1 = [<?php echo $issue_names; ?>];
	var issueToArr = [<?php echo join(",", $jqPloatissueToID);?>];
	var plot2 = $.jqplot('chart2', [s3, s5, s6, s4], {
		seriesDefaults:{
			renderer:$.jqplot.BarRenderer,
			rendererOptions: {fillToZero: true,barWidth:20}
		},
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
				tickOptions: {formatString: '%d'}
			}
		},seriesColors: [ "#ff0000","#FFFF00", "#00ff00","#3399ff"]
	});
	$('#chart2').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
		var statusType = "";
		var issuedTonew = "";
		if($("#issuedTo").attr("selectedIndex") != 0)
			issuedTonew = $("#issuedTo").val();
		else
			issuedTonew = "<?=$_GET['it_name'];?>";
		if(issuedTonew == "")	issuedTonew = "NA";

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
			window.top.location.href = "pms.php?sect=i_defect&frm=dsb&bk=Y&isst="+issuedTonew+"&sts="+statusType;
	<?php }else{?>
			window.top.location.href = "pms.php?sect=c_defect&frm=dsb&bk=Y&isst="+issuedTonew+"&sts="+statusType;
	<?php }?>

//		$('#info3').html('series: '+seriesIndex+', point: '+pointIndex+', data: '+data);
	}); 
});  
</script>
<script type="text/javascript">
	/*function changeProID(){
		var proid=document.issue_to_exp.projName.value;
		var issue_name=document.issue_to_exp.issuedTo.value;
		parent.projectValue(proid,issue_name);
	}*/
	function startAjax(val){
		AjaxShow("POST", "ajaxFunctionsfor_issue.php?type=issuedTo && proID="+val, "ShowIssuedTo");
	}
</script>
    </head>
<body style="margin:0px;padding:0px;">
<div align="right" style="margin-top:-10px;">




<form method="post" name="issue_to_exp" id="issue_to_exp"  action="" >
	<table style="margin-top:5px;margin-top:10px\9;" >
		<tr>
    		<td style="font-weight:bold;font-family:Arial, Helvetica, sans-serif;font-size:12px;">
				Select Project
			</td>
			<td>
				<select name="projName" id="projName"  class="select_box" onChange="startAjax(this.value); " style="font-family:Arial, Helvetica, sans-serif;font-size:12px;">
					  <option value="">Select</option>
	<?php if($builder_id != ''){
		FillSelectBox("project_id, project_name", "user_projects", "user_id = '".$builder_id."' and is_deleted=0", "project_name");
	}else{
		FillSelectBox("project_id, project_name", "user_projects", "is_deleted=0", "project_name");
	}?>
               </select>
           </td>
           <td  style="font-weight:bold;font-family:Arial, Helvetica, sans-serif;font-size:12px;" >
           		Issued To
           </td>
		   <td  id="ShowIssuedTo" align="right">
           		<select name="issuedTo" id="issuedTo" class="select_box" style="width:150px;font-family:Arial, Helvetica, sans-serif;font-size:12px;" >
					<option value="">Select</option>
					<?php FillSelectBoxIssue(); ?>
				</select>
           </td> 
           <td>
                   <input type="submit" name="go" id="go" value="Go" >			
           </td>    
       </tr>
	</table>  
 </form>                  
</div>
<table border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;width:100%;width:95%\9;">
	<tr>
		<td width="30px" style="padding-bottom:70px;">
			<a href="#" onClick="prevIT()"><img src="images/btn_prev.gif" id="it_previous" style="display:none;" border="0" /></a>
		</td>
		<td>
			<div id="chart2" style="width:800px; height:255px;text-align:center;"></div>
			<table width="100%" border="0" cellspacing="0" cellpadding="2" style="font-size:11px;">
				<tr id="IT" style="display:block;">
					<td id="it_row"></td>
				</tr>
				<tr>
					<td align="right">
						<span style="width:20px;height:20px;height:10px\9;background:red;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Open 
						<span style="width:20px;height:20px;height:10px\9;background:yellow">&nbsp;&nbsp;&nbsp;&nbsp;</span> Pending 
						<span style="width:20px;height:20px;height:10px\9;background:#00ff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> Fixed 
						<span style="width:20px;height:20px;height:10px\9;background:#3399ff">&nbsp;&nbsp;&nbsp;&nbsp;</span> Closed
					</td>
				</tr>
			</table>
		</td>
		<td width="30px" style="padding-bottom:70px;"><a href="#" onClick="nextIT()"><img src="images/btn_next.gif" id="it_next" border="0" /></a></td>
	</tr>
</table>
<script class="code" type="text/javascript">
end_l = it_end + limit;
if (end_l > it_total){
	end_l = it_total;
}
document.getElementById("it_row").innerHTML = "Showing " + (it_end+1) + " to " + (end_l) + " of " + it_total;

function newPageView(uriLink){ window.top.location.href = uriLink; }
</script>
</body>
</html>