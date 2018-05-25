<link rel="stylesheet" type="text/css" href="menu/index_data/FloatMenu.css">
<link rel="stylesheet" type="text/css" href="menu/index_data/FloatMenuExamples.css">
<!--<link rel="stylesheet" type="text/css" href="menu_style.css">-->
<link rel="stylesheet" type="text/css" href="menu_style_new.css">
<script type="text/javascript" src="menu/index_data/FloatMenu.js"></script>

<script type="text/javascript">
	FloatMenu.setOptions('floatMenu4',{elWidth:48,elHeight:48,hShift:0,vShift:-10,speedup:1,active:1,tooltip:1,fadeStep:10});
</script>
<?php
$s = isset($_REQUEST['sect']) ? $_REQUEST['sect'] : '';
if(isset($_SESSION['ww_is_builder']))
	$f = $_SESSION['ww_is_builder'];
else
	$f = '';
?>
<?php
$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$pi = pathinfo($refering_url);
$u = str_replace('pms.php','',$pi['basename']);
?>
<div id="top">
	<div class="header_container">
		<div class="logo"><img src="images/logo.png" border="none" alt="Logo" /></div>
        <?php if(isset($_SESSION['ww_logged_in_as'])){?>
	<div class="welcome">Hi, <?=$_SESSION['ww_logged_in_as']?><a href="b_logout.php"><img src="images/logout_btn.png" width="63" height="24"  align="absmiddle" hspace="11"/></a></div>
	<? }?>
	</div>
	<div id="nav">
								<ul>

			<li <?php if($s=='b_full_analysis') { ?> class="active_link" <?php } ?> ><a href="?sect=b_full_analysis" style="border:none;"><img src="images/health_check.png" width="28" height="29" hspace="5" align="absmiddle" />Health Check</a></li>
            
            <li <?php if($s=='b_dashboard'||$s=='b_dashboard_edit'){?> class="active_link" <?php }?>>
            <a href="?sect=b_dashboard" >
            <img src="images/myprofile.png" width="28" height="29" hspace="5" align="absmiddle" />My Profile</a>
            </li>
            
            <li <?php if($s=='b_project' || $s=='show_project' || $s=='add_project' || $s=='add_project_detail' || $s=='show_sub_loc' || $s=='add_sub_loc' || $s=='project_configuration'|| $s=='progress_monitoring'|| $s=='issue_to' || $s=='standard_defect' || $s=='add_progress_task' || $s=='edit_progress_task' || $s=='add_issue_to' || $s=='edit_issue_to'  || $s=='add_standard_defect' || $s=='edit_standard_defect') { ?> class='active_link' <?php } ?> >
            <a href="?sect=show_project" ><img src="images/project.png" width="33" height="27" hspace="5" align="absmiddle" />Projects</a>
            </li>
            
       
		
        <li <?php if($s=='i_report') { ?> class="active_link" <?php } ?>  ><a href="?sect=i_report"><img src="images/reports.png" width="23" height="29" hspace="5" align="absmiddle" />Reports</a></li>

	<li <?php if($s=='i_defect') { ?> class="active_link" <?php } ?> ><a href="?sect=i_defect"><img src="images/inspection.png" width="24" height="25" hspace="5" align="absmiddle" />Quality Control</a></li>
		
        <li <?php if($s=='b_progress') { ?> class="active_link" <?php } ?>><a href="?sect=b_progress" ><img src="images/progress_monitor.png" width="29" height="28" hspace="5" align="absmiddle" />Progress Monitoring</a></li>
                                       


    	</ul>
    	</div>
		
	</div>

</div>
	
	

</div>