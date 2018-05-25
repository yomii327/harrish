<?php include'data-table.php';

include('includes/commanfunction.php');
$object = new COMMAN_Class();

function FillSelectBox($field, $table, $where, $group){
$q="select $field from $table where $where GROUP BY $group";
	//echo '<option value="$q">'.$q.'</option>';
	$q=mysql_query($q);
	while($q1=mysql_fetch_array($q)){
		echo '<option value="'.$q1[0].'">'.$q1[1].'</option>';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="style.css" type="text/css"/>
<title>Report</title>
<style type="text/css">

table td{padding:5px;}
table{ margin-left:10px;}
#DRF, #DRT, #FBDF, #FBDT{
	background:#FFF;
	cursor:default;
	height:20px;
	-moz-border-radius: 6px;
	-webkit-border-radius: 6px;
	border-radius: 6px;
}

</style>
<!-- Date Picker files start here -->
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
	window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"DRF",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"DRT",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"FBDF",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"FBDT",
			dateFormat:"%d-%m-%Y"
		});
	};
</script>
<!-- Date Picker files start here -->
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
</head>
<body id="dt_example">

<script type="text/javascript">
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
</script>
<style>
div#spinner{
    display: none;
    width:100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    background:url(images/loadingAnimation.gif) no-repeat center #CCCCCC;
    text-align:center;
    padding:10px;
    font:normal 16px Tahoma, Geneva, sans-serif;
    border:1px solid #666;
    z-index:2;
    overflow: auto;
	opacity : 0.8;
}
</style>
<script language="javascript" type="text/javascript">
function startAjax(val){
	AjaxShow("POST","ajaxFunctions.php?type=location && proID="+val,"ShowLocation");
	AjaxShow("POST","ajaxFunctions.php?type=inspecrBy && proID="+val,"ShowInspecrBy");
	AjaxShow("POST","ajaxFunctions.php?type=issuedTo && proID="+val,"ShowIssuedTo");
	AjaxShow("POST","ajaxFunctions.php?type=priority && proID="+val,"ShowPriority");
}
function subLocate(obj){
	AjaxShow("POST","ajaxFunctions.php?type=subLocation && proID="+obj,"ShowSubLocation");
}
function submitForm(){
	var projName = document.getElementById('projName').value;
	var location = document.getElementById('location').value;
	var subLocation = document.getElementById('subLocation').value;
	var status = document.getElementById('status').value;
	var inspectedBy = document.getElementById('inspectedBy').value;
	var issuedTo = document.getElementById('issuedTo').value;
	var priority = document.getElementById('priority').value;
	var inspecrType = document.getElementById('inspecrType').value;
	var costAttribute = document.getElementById('costAttribute').value;
	var DRF = document.getElementById('DRF').value;
	var DRT = document.getElementById('DRT').value;
	var FBDF = document.getElementById('FBDF').value;
	var FBDT = document.getElementById('FBDT').value;
if(projName == ''){ alert('Project Name Should be Selected !'); return false;}
	if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	showProgress();
	var url = 'i_defect_show_data.php';
	var params = "projName="+projName+"&location="+location+"&subLocation="+subLocation+"&status="+status+"&inspectedBy="+inspectedBy+"&issuedTo="+issuedTo+"&priority="+priority+"&inspecrType="+inspecrType+"&costAttribute="+costAttribute+"&DRF="+DRF+"&DRT="+DRT+"&FBDF="+FBDF+"&FBDT="+FBDT+"&name="+Math.random();
	xmlhttp.open("POST", url, true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			hideProgress();
			document.getElementById("show_defect").innerHTML=xmlhttp.responseText;
			oTable = $('#example').dataTable({
				"bJQueryUI": true,
				"sPaginationType": "full_numbers"
			});
			oTable = $('#example_1').dataTable({
				"bJQueryUI": true,
				"sPaginationType": "full_numbers"
			});
			oTable = $('#example_2').dataTable({
				"bJQueryUI": true,
				"sPaginationType": "full_numbers"
			});
		}
	}
	xmlhttp.send(params);
}
</script>

<div id="container" style="width:99%;margin-top:25px;min-height:510px;">
<?php
if ($_SESSION["ww_is_builder"] == 1)
{
	$owner_id = $_SESSION['ww_builder_id'];
}else{
	$owner_id = $_SESSION['ww_owner_id'];
}
#$owner_id = $_SESSION['ww_owner_id'];
$phd='';
$myProjects='';
$ihd='';
$myInspections='';
/*
// get all projects related to this inspector
	$qp="SELECT *, p.id as project_id,r.resp_full_name FROM ".OWNERS." o 
	LEFT JOIN ".PROJECTS." p ON p.id = o.ow_project_id 
	LEFT JOIN ".RESPONSIBLES." r ON r.project_id = o.ow_project_id 
	LEFT JOIN ".BUILDERS." b ON p.builder_id = b.m_id 
	WHERE o.id = '$owner_id' ";
	  
$phd="<th>Project Name</th>
	<!--th>Add Inspection</th-->";

$rp=mysql_query($qp);

while($fp=mysql_fetch_assoc($rp)){
	$myProjects.="<tr class='gradeA'>
	<td>".stripslashes($fp['pro_name'])."</td>
	<!--td align='center'>
		<a href='?sect=add_defect&id=".base64_encode($fp['project_id'])."'><img src='images/edit.png' border='none' /></a>
	</td-->
	</tr>";
}*/


// get all inspections logged by this inspector

?>
	<div class="content_hd1" style="background-image:url(images/defects_hd.png);"></div>
<div class="search_multiple" style="border:1px solid; margin-bottom:20px;text-align:center;margin-left:10px;margin-right:10px;">
	<form action="pms.php?sect=i_defect" method="post">
			<table width="" cellpadding="0" cellspacing="5" border="0">
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Project Name <span class="reqire">*</span></td>
					<td colspan="2" align="right"><select name="projName" id="projName"  class="select_box" onchange="startAjax(this.value);" style="width:220px;background-image:url(images/selectSpl.png);">
					  <option value="">Select</option>
					  <?php FillSelectBox("project_id, project_name","user_projects","user_id = '".$owner_id."' and is_deleted=0","project_name"); ?>
				    </select></td>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Location </td>
					<td colspan="2" id="ShowLocation" align="right"><select name="location" id="location"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
					  <option value="">Select</option>
				    </select></td>
				</tr>
				<tr>
				  <td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Sub Location</td>
				  <td colspan="2" align="right" id="ShowSubLocation"><select name="subLocation" id="subLocation"  class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
                    <option value="">Select</option>
                  </select></td>
				  <td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Status</td>
				  <td colspan="2" align="right"><select name="status" id="status" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
                    <option value="">Select</option>
                    <option value="Open">Open</option>
                    <option value="Pending">Pending</option>
                    <!--					  <option value="In Progress">In Progress</option>-->
                    <option value="Closed">Closed</option>
                  </select></td>
			  </tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Inspected By</td>
					<td colspan="2" id="ShowInspecrBy" align="right"><select name="inspectedBy" id="inspectedBy" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
                      <option value="">Select</option>
                    </select></td>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Issued To</td>
					<td colspan="2" id="ShowIssuedTo" align="right"><select name="issuedTo" id="issuedTo" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
                      <option value="">Select</option>
                    </select></td>
				</tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Priority</td>
					<td colspan="2" id="ShowPriority" align="right"><select name="priority" id="priority" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
                      <option value="">Select</option>
                    </select></td>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Inspection Type</td>
					<td colspan="2" align="right"><select name="inspecrType" id="inspecrType" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
                      <option value="">Select</option>
                      <option value="Issue">Issue</option>
                      <option value="Defect">Defect</option>
                      <option value="Warranty">Warranty</option>
<!--                      <option value="Incomplete Works">Incomplete Works</option>
                      <option value="Progress Monitoring">Progress Monitoring</option>-->
                      <option value="Other">Other</option>
                    </select></td>
				</tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Cost Attribute</td>
					<td colspan="2" align="right"><select name="costAttribute" id="costAttribute" class="select_box" style="width:220px;background-image:url(images/selectSpl.png);">
                      <option value="">Select</option>
                      <option value="None">None</option>
                      <option value="Back Charge">Back Change</option>
                      <option value="Variation">Variation</option>
                    </select></td>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">&nbsp;</td>
					<td colspan="2" align="right">&nbsp;</td>
				</tr>
				<tr>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Date Raised</td>
					<td colspan="2" align="left" nowrap="nowrap" style="color:#FFFFFF;">From 
						<input name="DRF" type="text" value="" size="7" id="DRF" readonly="readonly"/>
					To 
						<input name="DRT" type="text" id="DRT" size="7" />					</td>
					<td align="left" valign="top" nowrap="nowrap" style="color:#FFFFFF;">Fix By Date</td>
					<td colspan="2" align="left" nowrap="nowrap" style="color:#FFFFFF;">From 
						<input name="FBDF" type="text" id="FBDF" size="7" />
					To
						<input name="FBDT" type="text" id="FBDT" size="7" />					</td>
				</tr>
				<tr>
					<td colspan="3" align="left">&nbsp;</td>
					<td align="left">
					<div id="report_timer" style="color:#FFFFFF;"></div>					</td>
					<td>
					  <!--input type="hidden" value="create" name="sect" id="sect" /-->
						<input name="SearchInsp" type="button" onclick="submitForm();" class="submit_btn" id="button" value="" style="background-image:url(images/search_btn_web.png); width:113px; height:46px;"  />					</td>
				</tr>
	  </table>
	</form>
  </div>
<div>
	<div class="demo_jui" id="show_defect" <?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){ echo 'style="padding-left:10px;"'; }?>></div>
	<div class="spacer"></div>
  
</div><div id="spinner"></div>  
</body>
</html>
