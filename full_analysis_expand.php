<?php
session_start();
require_once'includes/functions.php';
$obj = new DB_Class();

$offset=$_REQUEST['count'];

$limit=3;
$max = 0;

$builder_id = isset($_SESSION['ww_builder_id']) ? $_SESSION['ww_builder_id'] : '';
if($builder_id != ''){
	$where = 'user_id = $builder_id ';
}
$company = isset($_SESSION['ww_is_company']) ? $_SESSION['ww_is_company'] : '' ;

include('includes/commanfunction.php');
$object = new COMMAN_Class();
$jqPloatProjID = array();
function FillSelectBox($field, $table, $where, $group){
$q="select $field from $table where $where GROUP BY $group";
	//echo '<option value="$q">'.$q.'</option>';
	//echo $_REQUEST['pmId'];
	if(isset($_REQUEST['pmId']))
	{
		$pid=$_REQUEST['pmId'];	
	}
	elseif(isset($_GET['pid']))
	{
		$pid=$_GET['pid'];	
	}
	$q=mysql_query($q);
	
	while($q1=mysql_fetch_array($q)){
		$jqPloatProjID[] = $q1[0];?>
		<option <?php  if(isset($pid)) { if($pid == $q1[0]){?> selected="selected" <?php }} ?> value="<?php echo $q1[0]; ?>"><?php echo $q1[1]; ?></option>
	<?php
    }
}

if(isset($_REQUEST['pmId']))
	{
		$pid=$_REQUEST['pmId'];	
	}
	elseif(isset($_GET['pid']))
	{
		$pid=$_GET['pid'];	
	}
/*$qs="SELECT p.project_id,p.project_name FROM project_inspections AS d, user_projects AS p where d.project_id=p.project_id and p.user_id=$builder_id and p.is_deleted=0  and d.is_deleted=0 group by d.project_id ORDER BY p.project_id limit $offset,$limit";*/
if($builder_id != ''){
	if(isset($pid) && !empty($pid)){
		$project_id_new = $pid;	
		$qs1 = "SELECT p.project_id,p.project_name, count(*) as pcount FROM project_inspections AS d, user_projects AS p where d.project_id=$project_id_new and p.project_id=$project_id_new and p.user_id=$builder_id and p.is_deleted=0 and d.is_deleted=0 and d.inspection_type!='Memo' group by d.project_id ORDER BY pcount DESC limit $offset,$limit";
	}else{
		$qs1="SELECT p.project_id,p.project_name, count(*) as pcount FROM project_inspections AS d, user_projects AS p where d.project_id=p.project_id and p.user_id=$builder_id and p.is_deleted=0  and d.is_deleted=0 and d.inspection_type!='Memo' group by d.project_id ORDER BY pcount DESC limit $offset,$limit";
	}
}else{
	if(isset($pid) && !empty($pid)){
		$project_id_new = $pid;	
		$qs1 = "SELECT p.project_id, p.project_name, count(*) as pcount FROM project_inspections AS d, user_projects AS p where d.project_id = $project_id_new and p.project_id = $project_id_new and p.is_deleted=0  and d.is_deleted=0 and d.inspection_type!='Memo' group by d.project_id ORDER BY pcount DESC limit $offset,$limit";
	}else{
		$qs1="SELECT p.project_id,p.project_name, count(*) as pcount FROM project_inspections AS d, user_projects AS p where d.project_id=p.project_id and p.is_deleted=0 and d.is_deleted=0 and d.inspection_type!='Memo' group by d.project_id ORDER BY pcount DESC limit $offset,$limit";
	}
}


$rs22=$obj->db_query($qs1);
$proj_name1= array();
$close1=array();
$open1=array();
$pending1=array();
$fixed1=array();

/*$query_i = "SELECT count(*) as ispection_count FROM issued_to_for_inspections AS d ,  user_projects AS p where  d.project_id=p.project_id and p.user_id=$builder_id and  d.is_deleted=0 group by inspection_id ";
 $icount=$obj->db_query($query_i);
  $i_counts=  mysql_num_rows($icount);*/

while($f12=$obj->db_fetch_assoc($rs22)){
     $proj_id = $f12["project_id"];
	
	/*$query_i = "SELECT count(*) as ispection_count FROM issued_to_for_inspections AS d ,  user_projects AS p where  d.project_id=$proj_id and p.project_id=$proj_id and p.user_id=$builder_id and  d.is_deleted=0 group by inspection_id ";
 $icount=$obj->db_query($query_i);
  $i_counts=  mysql_num_rows($icount);
	*/

$whereConUserRole = "";
if(!empty($_SESSION['projUserRole'])){
	if($_SESSION['projUserRole'][$proj_id] != 'All Defect')
		$whereConUserRole = " AND pi.inspection_raised_by = '".$_SESSION['projUserRole'][$proj_id]."'";
}
	
	$query_i = "SELECT count(*) as inspection_count FROM project_inspections where project_id=$proj_id and is_deleted=0 and inspection_type!='Memo'".$whereConUserRole;
 $icount=$obj->db_query($query_i);
$f2=$obj->db_fetch_assoc($icount);
$i_counts = $f2["inspection_count"];
	
/*    $query = "SELECT SUM(IF(d.inspection_status= 'Open',1,0)) AS open, SUM(IF(d.inspection_status='Closed',1,0)) AS closed, SUM(IF(d.inspection_status='Pending',1,0)) AS pending  FROM project_inspections AS d where $proj_id= d.project_id and d.is_deleted=0";
	

	 $query12 = "SELECT SUM(IF(d.inspection_status= 'Open',1,0)) AS open, SUM(IF(d.inspection_status='Pending',1,0)) AS pending, SUM(IF(d.inspection_status='Fixed',1,0)) AS fixed, SUM(IF(d.inspection_status='Closed',1,0)) AS closed FROM issued_to_for_inspections AS d, project_inspections as pi where d.project_id=$proj_id  and d.is_deleted=0 and pi.is_deleted=0 and pi.inspection_id=d.inspection_id and pi.inspection_type!='Memo'".$whereConUserRole;
	 */
	// echo $query; die;
	 $query12 = 'SELECT SUM(IF(d.inspection_status= "Open", 1, 0)) AS open,
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
							
							
    $rs1=$obj->db_query($query12);
    $f1=$obj->db_fetch_assoc($rs1);

    $openF1 = intval($f1['open']); // 100
    $closedF1 = intval($f1['closed']); // 50
    $pendingF1 = intval($f1['pending']); // 50
	$fixedF1 = intval($f1['fixed']); // 50
    $total1 = $openF1 + $closedF1 + $pendingF1 + $fixedF1 + $draftF1;//200
    /*if ($total1 ==0)
      continue;*/
    
    // print_r($f); r
    $open1[]= $openF1;
    $close1[]=$closedF1;
    $pending1[]=$pendingF1;
	$fixed1[]=$fixedF1;
	$proj_name1[] =  '"<a class=\"dashboradAnchor\" href=\"#\" onclick=\"newPageView(\'pms.php?sect=dashboard_detail&type=location&projID='.$proj_id.'\');\"><center>'.$f12['project_name'].'<br/>' .  $i_counts . ' inspections<br/>Open: ' . $openF1 .' Pending: ' . $pendingF1 . ' Fixed: ' . $fixedF1 . ' Closed: ' . $closedF1 . ' <center></a>"';

}

$pr_name1=implode(',',$proj_name1); 
$open_proj1=implode(',',$open1); 
$close_proj1=implode(',',$close1); 
$pending_proj1=implode(',',$pending1); 
$fixed_proj1=implode(',',$fixed1); 

if(empty($pr_name1))
{
	$pname="select project_name from user_projects where project_id=".$pid." and is_deleted=0";
	$result_p=mysql_query($pname);
	$pro_name_new=mysql_fetch_array($result_p);
	$pr_name1='"<center>'.$pro_name_new['project_name'].'<br/> 0 inspections<center>"';;
}
if(empty($open_proj1))
{
	$open_proj1=0;
}

if(empty($close_proj1))
{
	$close_proj1=0;
}

if(empty($pending_proj1))
{
	$pending_proj1=0;
}

if(empty($fixed_proj1))
{
	$fixed_proj1=0;
}


?>
<html>
    <head>
	<!--[if IE]><script language="javascript" type="text/javascript" src="../dist/excanvas.js"></script><![endif]-->
<link class="include" rel="stylesheet" type="text/css" href="dist/jquery.jqplot.min.css" />
<style type="text/css">
pre{ display:block; font:12px "Courier New", Courier, monospace; padding:10px; border:1px solid #bae2f0; background:#e3f4f9;	 margin:.5em 0; width:674px; }	
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
</head>
<body style="margin:0px;padding:0px;">
<div align="right" >
<form method="post" name="pmchnage" id="pmchange" action="">
<input type="hidden" value="" name="pmId" id="pmId">
</form>
<strong style="font-weight:bold;font-family:Arial, Helvetica, sans-serif;font-size:12px;">Select Project</strong>
<select name="projName" id="projName"  class="select_box" onChange="changeProID(this.value);" style="font-family:Arial, Helvetica, sans-serif;font-size:12px;">
	<?php if($builder_id != ''){
		FillSelectBox("project_id, project_name", "user_projects", "user_id = '".$builder_id."' and is_deleted=0", "project_name");
	}else{
		FillSelectBox("project_id, project_name", "user_projects", "is_deleted=0", "project_name");
	}?>
</select>
</form>                    
</div>
<div id="chart1" style="width:875px; height:310px;text-align:center;"></div>
<script class="code" type="text/javascript">
$(document).ready(function(){
	var s12 = [<?php echo $open_proj1; ?>]; //For Full Analysis Total Open Project for each Project
	var s22 = [<?php echo $close_proj1; ?>]; //For Full Analysis Total Closed Project for each Project
	var s33 = [<?php echo $pending_proj1; ?>]; //For Full Analysis Total Pending Project for each Project
	var s44 = [<?php echo $fixed_proj1; ?>];
	var projArr = [<?php echo join(",", $jqPloatProjID);?>];
	var ticks12 = [<?php echo $pr_name1; ?>];
	// code for Full Analysis Chart
	var plot12 = $.jqplot('chart1', [s12, s33, s44, s22], {
		seriesDefaults:{
			renderer:$.jqplot.BarRenderer,
			rendererOptions: {fillToZero: true,barWidth:50}
		},
	 
		axes: {
			xaxis: {
				renderer: $.jqplot.CategoryAxisRenderer,
				ticks: ticks12
			},
			yaxis: {
				pad: 1.05,
				min: 0,
				tickOptions: {formatString: '%d', showMark: true,show: true,showLabel: true}
			},
			highlighter: { show: true }
		},seriesColors: [ "#ff0000","#FFFF00", "#00ff00","#3399ff"]
	});
	$('#chart1').bind('jqplotDataClick', function (ev, seriesIndex, pointIndex, data) {
		var statusType = "";
		if($("#projName").attr("selectedIndex") != 0)
			projectID = $("#projName").val();
		else
			projectID = <?=$_GET['pid'];?>;
	
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
function changeProID(val){
	document.getElementById("pmId").value=val;
	document.pmchnage.submit();
}
function newPageView(uriLink){ window.top.location.href = uriLink; }
</script>
</body>
</html>