<?php $builder_id = isset($_SESSION['ww_builder_id']) ? $_SESSION['ww_builder_id'] : '';
	if($builder_id != ''){
		$where = 'user_id = $builder_id ';
	}
	$company = isset($_SESSION['ww_is_company']) ? $_SESSION['ww_is_company'] : '' ;

	//************************************************************************************/
				/* QUERY FOR FULL ANALYSIS CHART*/
	//************************************************************************************/
	if($builder_id != ''){
		$query = "SELECT DISTINCT project_id FROM user_projects WHERE is_deleted = 0 and user_id = ".$builder_id;
	}else{
		$query = "SELECT DISTINCT project_id FROM projects WHERE is_deleted = 0";
	}
	$noti_record_closed = $obj->db_query($query);
	$tmp_arr = array();
	while($project_id=$obj->db_fetch_assoc($noti_record_closed)){
		$tmp_arr[] = $project_id["project_id"];
	}
	$project_ids = join(",", $tmp_arr);

	if($builder_id != ''){
		if ($_SESSION['userRole'] == "Sub Contractor") die;
		$qs="SELECT DISTINCT d.project_id FROM project_inspections AS d WHERE d.project_id in (".$project_ids.") AND d.is_deleted = 0";
	}else{
		$qs="SELECT project_id FROM project_inspections WHERE project_id IN (".$project_ids.") AND is_deleted = 0 AND is_deleted = 0";
	}

	$rs = $obj->db_query($qs);
	$qc_total = mysql_num_rows ($rs);
	$tmp_arr = array();
	$qc_expand = "";
	while($project_id = $obj->db_fetch_assoc($rs)){
		$tmp_arr[] = $project_id["project_id"];
		if ($qc_expand=="") $qc_expand = $project_id["project_id"];
	}
	$project_ids = join(",", $tmp_arr);

	/* END CODE FOR QUALITY CONTROL TO*/
	//************************************************************************************/
	/* QUERY FOR ISSUE TO CHART*///************************************************************************************/

	if($builder_id != ''){
		$it_query_expand = "SELECT
			count(*) AS count, i.issue_to_name AS issued_to_name
		FROM
			inspection_issue_to AS i,
			user_projects AS up,
			issued_to_for_inspections AS isi
		WHERE
			up.user_id = ".$builder_id."
			AND up.project_id = i.project_id
			AND up.project_id = isi.project_id
			AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' GROUP BY issued_to_name ORDER BY count DESC LIMIT 0, 1";
			#$it_query_expand ="SELECT count(*) as count, issued_to_name FROM `issued_to_for_inspections` as i, user_projects as up where up.user_id=$builder_id and up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 group by issued_to_name order by count desc limit 0,1";
		}else{
		$it_query_expand = "SELECT
			count(*) AS count, i.issue_to_name AS issued_to_name
		FROM
			inspection_issue_to AS i,
			user_projects AS up,
			issued_to_for_inspections AS isi
		WHERE
			up.project_id = i.project_id
			AND up.project_id = isi.project_id
			AND i.is_deleted = 0 AND isi.is_deleted = 0 AND (isi.issued_to_name LIKE CONCAT(i.issue_to_name, ' (%') OR isi.issued_to_name LIKE i.issue_to_name) AND isi.issued_to_name != '' AND i.issue_to_name != '' GROUP BY issued_to_name ORDER BY count DESC LIMIT 0, 1";
			#	$it_query_expand ="SELECT count(*) as count, issued_to_name FROM `issued_to_for_inspections` as i, user_projects as up where up.project_id=i.project_id and i.is_deleted=0 and up.is_deleted=0 group by issued_to_name order by count desc limit 0,1";
		}
		#echo $it_query_expand;die;
		$it_rs_expd=$obj->db_query($it_query_expand);
		if($issu_expand=$obj->db_fetch_assoc($it_rs_expd)){
			$it_name_expand = $issu_expand["issued_to_name"];
		}
		/*END CODE FOR ISSUE TO */
		//************************************************************************************/
		/* QUERY FOR PROGRESS MONITORING CHART*/
		//************************************************************************************/
		//Chart for Progress Monitoring
		$pm_total = 0;
		$pm_expand_id = 0;
		if($_SESSION['web_menu_progress_monitoring'] == 1){
			if($builder_id != ''){
				$prg_m="SELECT count(DISTINCT pm.project_id) as pmcount FROM progress_monitoring AS pm, user_projects AS p where (user_id=$builder_id and  p.project_id = pm.project_id and p.is_deleted=0 and pm.is_deleted=0)";
			}else{
				$prg_m="SELECT count(DISTINCT pm.project_id) as pmcount FROM progress_monitoring AS pm, user_projects AS p where (p.project_id = pm.project_id and p.is_deleted=0 and pm.is_deleted=0)";
			}
			$pm_rs=$obj->db_query($prg_m);
			if($pm=$obj->db_fetch_assoc($pm_rs)){
				$pm_total = $pm["pmcount"];
			}
			if($builder_id != ''){
		 		$pm_expand = "SELECT p.project_name, p.project_id FROM progress_monitoring AS pm, user_projects AS p where (user_id=$builder_id and  p.project_id = pm.project_id and p.is_deleted=0 and pm.is_deleted=0)  group by pm.project_id order by p.project_name limit 0,1";
			}else{
		 		$pm_expand = "SELECT p.project_name, p.project_id FROM progress_monitoring AS pm, user_projects AS p where (p.project_id = pm.project_id and p.is_deleted=0 and pm.is_deleted=0)  group by pm.project_id order by p.project_name limit 0,1";
			}
			$rs_pm_expand=$obj->db_query($pm_expand);
			if($pm_f_expand=$obj->db_fetch_assoc($rs_pm_expand)){
				$pm_expand_id = $pm_f_expand["project_id"];
			}
		}
		/* END PHP CODE FOR PROGRESS MONITORING*/ ?>

<link href="dashboard.css" rel="stylesheet" type="text/css" />
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
	.graphic, #prevBtn, #nextBtn{
		margin:0;
		padding:0;
		display:block;
		overflow:hidden;
		text-indent:-8000px;
	}
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
	.SycReport{
		float:left;
		border:1px solid #a6a6a6;
		l-webkit-border-radius:5px;
		-moz-border-radius:5px;
		border-radius:5px;
		width:157px;
		height:30px;
		overflow:hidden;
	}
	.SycReport select{
		width:190px;
		font-family:Verdana, Arial, Helvetica, sans-serif;
		color:#333333;
		font-size:14px;
		padding:0 10px 0 10px;
		height:30px;
		background-image:url(images/drop_point.png);
		background-position-x:133px;
		background-position-y:6px;
		background-repeat:no-repeat;
		border:0;
	  -webkit-appearance: none;
		background-color:transparent;
		outline:none;
		-moz-box-shadow:inset 0 0 20px #d9dada;
		-webkit-box-shadow:inset 0 0 20px #d9dada;
		box-shadow:inset 0 0 20px #d9dada;
		margin-left:0px;
	}
	@-moz-document url-prefix() {
		.SycReport select{
			background-position:133px;
		}
	}
</style>
<script language="javascript">
var spinnerVisible = false;
function showProgress() {
	if (!spinnerVisible) {
		$("div#spinner").fadeIn("fast");
		spinnerVisible = true;
	}
};
function hideProgress() {
	if (spinnerVisible) {
		var spinner = $("div#spinner");
		spinner.stop();
		spinner.fadeOut("fast");
		spinnerVisible = false;
	}
};

function projectValue(p_id,issu_name){
	var project_id_value=p_id;
	if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}else{
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	params = "project_id="+ project_id_value +"&issue_name="+issu_name;
	xmlhttp.open("POST", "issue_to_session.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			it_total=xmlhttp.response;
			alert(it_total);
		}
	}
	xmlhttp.send(params);
}

function session_destroy(){
	if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	}else{
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("POST", "issue_to_session_destroy.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			xmlhttp.response;
		}
	}
	xmlhttp.send();
}

var expand_IT=false;
var limit = 3;
var qc_end = 0;
var qc_total = '<?php echo $qc_total?>';
//alert(qc_total);
//to show previous QC
function prevQC(obj1){
	qc_end -= limit;
	if (qc_end <= 0){
		qc_end = 0;
		document.getElementById("qc_previous").style.display = "none";
	}
	if(obj1==true){
		document.getElementById("chart1").src = "/full_analysis_expand.php?count=" + qc_end;
	}else{
		document.getElementById("chart1").src = "/full_analysis.php?count=" + qc_end;
	}
	var end_l = qc_end + limit;
	if (end_l > qc_total){
		end_l = qc_total;
	}
	document.getElementById("qc_row").innerHTML = "Showing " + (qc_end+1) + " to " + (end_l) + " of " + qc_total;
	document.getElementById("qc_next").style.display = "";
}
/// to show next QC
function nextQC(obj1){
	try{
	qc_end += limit;
	if(obj1==true){
		document.getElementById("chart1").src = "/full_analysis_expand.php?count=" + qc_end;
	}
	else{
		document.getElementById("chart1").src = "/full_analysis.php?count=" + qc_end;
	}

	document.getElementById("qc_previous").style.display = "";
	var end_l = qc_end + limit;
	if (end_l > qc_total){
		end_l = qc_total;
		document.getElementById("qc_next").style.display = "none";
	}
	document.getElementById("qc_row").innerHTML = "Showing " + (qc_end+1) + " to " + (end_l) + " of " + qc_total;
	}catch(e){
		alert(e.message);
	}
}
//End changes for QC Prev and Back button
//////////////////////////PROGRESS MONITORING///////////////////////////
var pm_end = 0;
var pm_total = <?php echo $pm_total?>;
//to show previous PM
function prevPM(obj1){
	pm_end -= limit;
	if (pm_end <= 0){
		pm_end = 0;
		document.getElementById("pm_previous").style.display = "none";
	}
	if(obj1==true){
		document.getElementById("chart2_if").src = "/progress_chart_expand.php?count=" + pm_end;
	}
	else{
		document.getElementById("chart2_if").src = "/progress_chart.php?count=" + pm_end;
	}

	var end_l = pm_end + limit;
	if (end_l > pm_total){
		end_l = pm_total;
	}
<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
	document.getElementById("pm_row").innerHTML = "Showing " + (pm_end+1) + " to " + (end_l) + " of " + pm_total;
	document.getElementById("pm_next").style.display = "";
<?php }?>
}
/// to show next QC
function nextPM(obj1){
	try{
	pm_end += limit;
	//document.getElementById("chart2_if").src = "/progress_chart.php?count=" + pm_end;
	if(obj1==true){
		document.getElementById("chart2_if").src = "/progress_chart_expand.php?count=" + pm_end;
	}
	else{
		document.getElementById("chart2_if").src = "/progress_chart.php?count=" + pm_end;
	}

	document.getElementById("pm_previous").style.display = "";
	var end_l = pm_end + limit;
	if (end_l > pm_total){
		end_l = pm_total;
<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
		document.getElementById("pm_next").style.display = "none";
<?php }?>
	}
<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
	document.getElementById("pm_row").innerHTML = "Showing " + (pm_end+1) + " to " + (end_l) + " of " + pm_total;
<?php }?>
	}catch(e){
		alert(e.message);
	}
}

//end changes for expand PRO MON to
//////////////////////ISSUED TO/////////////////////

///next button handling at document load
$(document).ready(function(){
	var div_obj_close = document.getElementById("full_analysis");
	var iframe_obj_close = document.getElementById("chart1");
	iframe_obj_close.setAttribute("src", "about:blank");
	div_obj_close.style.width = "470px";
	div_obj_close.style.height = "345px";
	iframe_obj_close.style.width = "395px";
	iframe_obj_close.style.height = "195px";

	document.getElementById("chart1").src = "/full_analysis.php?count=0&pid=<?php echo $qc_expand; ?>";
	if (document.getElementById("chart2_if")!=null)
	document.getElementById("chart2_if").src = "/progress_chart.php?count=0";
	document.getElementById("chart3_if").src = "/issue_to_chart.php?count=0";
	document.getElementById("chart4_nb").src = "/notification_board.php";

	//QC
	if ((qc_end+limit) >= qc_total){
		document.getElementById("qc_next").style.display = "none";
	}
	var end_l = qc_end + limit;
	if (end_l > qc_total){
		end_l = qc_total;
	}
	document.getElementById("qc_row").innerHTML = "Showing " + (qc_end+1) + " to " + (end_l) + " of " + qc_total;

	//PM
	if ((pm_end+limit) >= pm_total){
<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
		document.getElementById("pm_next").style.display = "none";
<?php }?>
	}
	end_l = pm_end + limit;
	if (end_l > pm_total){
		end_l = pm_total;
	}
<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
	document.getElementById("pm_row").innerHTML = "Showing " + (pm_end+1) + " to " + (end_l) + " of " + pm_total;
<?php }?>
	//IT
});
</script>
<script language="javascript">
function expandIframe (div_id, iframe_id){
	try{
		var div_obj = document.getElementById(div_id);
		var iframe_obj = document.getElementById(iframe_id);
		iframe_obj.setAttribute("src", "about:blank");

		div_obj.style.width = "950px";
		div_obj.style.height = "450px";
		iframe_obj.style.width = "925px";
		iframe_obj.style.height = "360px";

		switch(div_id){
			case "full_analysis":
				document.getElementById("issue_to_summary").style.display = "none";
				document.getElementById("notification_board").style.display = "none";
				document.getElementById("chart1").src = "/full_analysis_expand.php?count=0&pid=<?php echo $qc_expand; ?>"
				document.getElementById("exp_pm").style.display = "none";
				document.getElementById("close_pm").style.display = "block";
				document.getElementById("QC").style.display = "none";
				document.getElementById("QCN").style.display = "none";
				expand_IT=true;
				<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
					document.getElementById("progress_monitor_summary").style.display = "none";
				<?php }?>
			break;

			case "progress_monitor_summary":
				document.getElementById("full_analysis").style.display = "none";
				document.getElementById("issue_to_summary").style.display = "none";
				document.getElementById("notification_board").style.display = "none";
				document.getElementById("chart2_if").src = "/progress_chart_expand.php?count=0&pm_expd=<?php echo $pm_expand_id;?>"
				document.getElementById("exp_pm1").style.display = "none";
				document.getElementById("close_pm1").style.display = "block";
				document.getElementById("PM").style.display = "none";
				document.getElementById("PME").style.display = "none";
				expand_IT=true;
			break;

			case "issue_to_summary":
				document.getElementById("full_analysis").style.display = "none";
				document.getElementById("notification_board").style.display = "none";
				document.getElementById(iframe_id).src = "/issue_to_chart_expand.php?count=0&it_name=<?php echo $it_name_expand; ?>";
				document.getElementById("exp_pm2").style.display = "none";
				document.getElementById("close_pm2").style.display = "block";
				//document.getElementById("IT").style.display = "none";
				expand_IT=true;
				<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
					document.getElementById("progress_monitor_summary").style.display = "none";
				<?php }?>
			break;
			case "notification_board":
				document.getElementById("full_analysis").style.display = "none";
				document.getElementById("issue_to_summary").style.display = "none";
				document.getElementById("exp_pm3").style.display = "none";
				document.getElementById("close_pm3").style.display = "block";
				document.getElementById("chart4_nb").src = "/notification_board_expand.php";
				expand_IT=true;
				<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
					document.getElementById("progress_monitor_summary").style.display = "none";
				<?php }?>
			break;
		}
	}catch(e){
		alert(e.message);
	}
}
function closeIframe(div_id_close, iframe_id_close){
	try{
		var div_obj_close = document.getElementById(div_id_close);
		var iframe_obj_close = document.getElementById(iframe_id_close);
//Delete content of iFrame Start Here
		document.getElementById(iframe_id_close).src = 'about:blank';
//Delete content of iFrame End Here
		iframe_obj_close.setAttribute("src", "about:blank");
		div_obj_close.style.width = "470px";
		div_obj_close.style.height = "345px";
		iframe_obj_close.style.width = "395px";
		iframe_obj_close.style.height = "195px";

		switch(div_id_close){
			case "full_analysis":
				document.getElementById("full_analysis").style.display = "block";
				document.getElementById("issue_to_summary").style.display = "block";
				document.getElementById("notification_board").style.display = "block";
				document.getElementById("chart1").src = "/full_analysis.php?count=0"
				document.getElementById("exp_pm").style.display = "block";
				document.getElementById("close_pm").style.display = "none";
				document.getElementById("QC").style.display = "block";
				document.getElementById("QCN").style.display = "table-cell";
				expand_IT=false;
				<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
					document.getElementById("progress_monitor_summary").style.display = "block";
				<?php }?>
			break;

			case "progress_monitor_summary":
				document.getElementById("full_analysis").style.display = "block";
				document.getElementById("issue_to_summary").style.display = "block";
				document.getElementById("notification_board").style.display = "block";
				document.getElementById("chart2_if").src = "/progress_chart.php?count=0"
				document.getElementById("exp_pm1").style.display = "block";
				document.getElementById("close_pm1").style.display = "none";
				document.getElementById("PM").style.display = "block";
				document.getElementById("PME").style.display = "table-cell";
				expand_IT=false;
				<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
					document.getElementById("progress_monitor_summary").style.display = "block";
				<?php }?>
			break;

			case "issue_to_summary":
				iframe_obj_close.style.width = "470px";
				iframe_obj_close.style.height = "248px";

				document.getElementById("full_analysis").style.display = "block";
				document.getElementById("issue_to_summary").style.display = "block";
				document.getElementById("notification_board").style.display = "block";
				document.getElementById(iframe_id_close).src = "/issue_to_chart.php?count=0"
				document.getElementById("exp_pm2").style.display = "block";
				document.getElementById("close_pm2").style.display = "none";
				//document.getElementById("IT").style.display = "block";
				expand_IT=false;
				<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
					document.getElementById("progress_monitor_summary").style.display = "block";
				<?php }?>
			break;

			case "notification_board":
				iframe_obj_close.style.width = "420px";
				iframe_obj_close.style.height = "270px";
				document.getElementById("full_analysis").style.display = "block";
				document.getElementById("issue_to_summary").style.display = "block";
				document.getElementById("notification_board").style.display = "block";
				document.getElementById("exp_pm3").style.display = "block";
				document.getElementById("close_pm3").style.display = "none";
				document.getElementById("chart4_nb").src = "/notification_board.php";
				expand_IT=false;
				<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
					document.getElementById("progress_monitor_summary").style.display = "block";
				<?php }?>
			break;
		}
	}catch(e){
		alert(e.message);
	}
}
</script>

<div class="search_multiple row">
	<div class="col-md-6">
		<div class="first_box" id="full_analysis"> <!--Start Code For Full Analysis Box-->
			<div class="title">
				<div class="text">
					<h1>
						<img src="images/analysis_big.png" width="35" height="43" align="absmiddle" /><span class="orange-text">Inspections Summary</span>
					</h1>
				</div>
				<div class="expand">
					<a href="#" onclick="expandIframe('full_analysis','chart1')" style="text-decoration:none;" id="exp_pm">
						<img src="images/maximize.png" alt="Click to expand" title="Click to expand" border="0" />
					</a>
					<a href="#"  onclick="closeIframe('full_analysis','chart1')" style="text-decoration:none;display:none;margin-left:500px;margin-top:-10px;" id="close_pm" >
						<img src="images/close_new.png" alt="Click to close" title="Click to close" />
					</a>
        </div>
			</div>
			<div class="table-holder">
				<table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:0px;">
					<tr>
						<td width="30px" style="padding-bottom:70px;">
							<a href="#" onclick="prevQC(expand_IT)">
								<img src="images/btn_prev.gif" id="qc_previous" style="display:none;"/>
							</a>
						</td>
						<td>
							<iframe src="about:blank" id="chart1" style="width:400px; height:200px;margin:0px;padding:0px;border:0px;" frameBorder="0"></iframe>
							<table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;">
								<tr id="QC" style="display:block;" >
									<td id="qc_row" align="left" ></td>
								</tr>
								<tr>
									<td align="right">
										<span style="width:20px;height:20px;background:red;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Open
										<span style="width:20px;height:20px;background:yellow">&nbsp;&nbsp;&nbsp;&nbsp;</span> Pending
										<span style="width:20px;height:20px;background:#00ff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> Fixed
										<span style="width:20px;height:20px;background:#3399ff">&nbsp;&nbsp;&nbsp;&nbsp;</span> Closed
									</td>
								</tr>
							</table>
            </td>
            <td width="30px" id="QCN" style="padding-bottom:70px;">
							<a href="#" onclick="nextQC(expand_IT)"><img src="images/btn_next.gif" id="qc_next" border="0"  /></a>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
  <!--End Code For Full Analysis Box-->
  <!--Start Code For Issue To  Box-->
	<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
		<div class="col-md-6">
			<div class="first_box" id="progress_monitor_summary">
				<div class="title">
					<div class="text">
						<h1>
							<img src="images/progress_monitor_big.png" width="43" height="40" align="absmiddle" /> <span class="orange-text">Progress Monitor</span> Summary
						</h1>
					</div>
					<div class="expand">
						<a href="#" onclick="expandIframe('progress_monitor_summary','chart2_if')" style="text-decoration:none;" id="exp_pm1">
							<img src="images/maximize.png" alt="Click to expand" title="Click to expand" border="0"  />
						</a>
						<a href="#" onclick="closeIframe('progress_monitor_summary','chart2_if')" style="text-decoration:none;display:none;margin-left:450px;" id="close_pm1" >
							<img src="images/close_new.png" alt="Click to close" title="Click to close" />
						</a>
					</div>
				</div>
				<div class="table-holder">
					<table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:0px;">
						<tr>
							<td width="30px" style="padding-bottom:70px;">
								<a href="#" onclick="prevPM(expand_IT)">
									<img src="images/btn_prev.gif" id="pm_previous" style="display:none;"/>
								</a>
							</td>
							<td>
								<iframe src="about:blank" id="chart2_if" style="width:400px; height:200px;margin:0px;padding:0px;border:0px;" frameBorder="0"></iframe>
								<table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-top:5px;">
									<tr id="PM" style="display:block;">
										<td id="pm_row" align="left" ></td>
									</tr>
									<tr>
										<td align="left" style="font-size:10px;">
											<span style="width:20px;height:20px;background:#ff0000;">&nbsp;&nbsp;&nbsp;&nbsp;</span> Behind
											<span style="width:20px;height:20px;background:#00ff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> Complete
											<span style="width:20px;height:20px;background:#ffff00">&nbsp;&nbsp;&nbsp;&nbsp;</span> In progress
											<span style="width:20px;height:20px;background:#3399ff">&nbsp;&nbsp;&nbsp;&nbsp;</span> Signed off
											<span style="width:20px;height:20px;background:#E4E4E4">&nbsp;&nbsp;&nbsp;&nbsp;</span> Not Started
										</td>
									</tr>
								</table>
							</td>
							<td width="30px" id="PME"  style="padding-bottom:70px;">
								<a href="#" onclick="nextPM(expand_IT)">
									<img src="images/btn_next.gif" id="pm_next" border="0" style="display:block;" />
								</a>
							</td>
            </tr>
					</table>
				</div>
			</div>
		</div>
	<?php }?>
	<!--End Code For Issue To  Box-->
	<!--Start Code For Progress Task Monitoring Box-->
	<div class="col-md-6">
		<div class="first_box" id="issue_to_summary">
			<div class="title">
				<div class="text">
					<h1>
						<img src="images/Issued_to.png" width="43" height="40" align="absmiddle" /><span class="orange-text">Issue To</span> Summary <!-- (Trades and Contractors)-->
					</h1>
				</div>
				<div class="expand">
					<a href="#" onclick="expandIframe('issue_to_summary','chart3_if')" style="text-decoration:none;" id="exp_pm2">
						<img src="images/maximize.png" alt="Click to expand" title="Click to expand" border="0" />
					</a>
          <a href="#" onclick="closeIframe('issue_to_summary','chart3_if')" style="text-decoration:none;display:none;margin-left:500px;" id="close_pm2" >
						<img src="images/close_new.png" alt="Click to close" title="Click to close" />
					</a>
        </div>
			</div>
			<div class="table-holder">
				<iframe src="about:blank" id="chart3_if" style="width:470px; height:270px;250px\9;margin:0px;padding:0px;border:0px;" frameBorder="0"></iframe>
			</div>
		</div>
	</div>
	<!--End Code For Progress Task Monitoring Box-->

	<div class="col-md-6">
		<div class="first_box" id="notification_board">
			<div class="title">
				<div class="text">
					<h1>
						<img src="images/notification_board.png" width="43" height="40" align="absmiddle" /> <span class="orange-text">Notification</span> Board
					</h1>
				</div>
				<?php if(isset($_SESSION['ww_is_company'])){?>
					<div class="SycReport" style="margin:12px 0 0 15px;">
						<select name="reportType" id="reportType">
							<option value="">Select</option>
							<option value="syncReport">Sync Report</option>
							<option value="userReport">Project - User Report</option>
						</select>
					</div>
					<img src="images/generate_report.png" onclick="reportGenerate();" title="Generate Report" style="margin: 12px 0 0 12px;" />
					<?php $st = 'style="margin:-27px 25px 0 227px;font-size:14px;float:right;"';
				}else{
					$st = 'style="margin:-40px 25px 0 240px;font-size:14px;float:right;"';
				}?>
				<div class="expand">
					<a href="#" onclick="expandIframe('notification_board','chart4_nb')" style="text-decoration:none;" id="exp_pm3">
						<img src="images/maximize.png" alt="Click to expand" title="Click to expand"  border="0" />
					</a>
	        <a href="#" onclick="closeIframe('notification_board','chart4_nb')" style="text-decoration:none;display:none;margin-left:720px;margin-top:-20px;<?php if(!isset($_SESSION['ww_is_company'])){echo 'margin-top:0px;';}?>" id="close_pm3" >
						<img src="images/close_new.png" alt="Click to close" title="Click to close" />
					</a>
				</div>
			</div>
			<div class="table-holder">
				<table width="100%"  border="0" cellspacing="0" cellpadding="2" style="margin-top:0px;" class="gridtable">
					<tr>
						<td>
							<iframe src="about:blank" id="chart4_nb" style="width:455px; height:270px;height:240px\9;margin:0px;margin-top:-10px;padding:0px;border:0px;" frameBorder="0"></iframe>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>
<!--End  Code For All  Box-->

<script type="text/javascript">
var align = 'center';
var top1 = 30;
var width = 825;
var padding = 10;
var backgroundColor = '#FFFFFF';
var borderColor = '#333333';
var borderWeight = 4;
var borderRadius = 5;
var fadeOutTime = 300;
var disableColor = '#666666';
var disableOpacity = 40;
var loadingImage = 'images/loadingAnimation.gif';		//Use relative path from this page

function reportGenerate(){
	var reportType = $('#reportType').val();
	if(reportType == ''){
		jAlert('Please select Report Type');
		return false;
	}else{
		switch(reportType){
			case 'syncReport' :
				var url = 'pdf/i_report_sync.php?name='+Math.random();
			break;

			case 'userReport' :
				var url = 'pdf/i_report_project_wise_user.php?name='+Math.random();
			break;
		}
	}
	modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, url, loadingImage);
}
function closePopUp(){
	closePopup(300);
}
function showMultipleInbox(){
	modalPopup(align, top1, 500, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'inbox_multiple_project.php?name='+Math.random(), loadingImage);
}
</script>
