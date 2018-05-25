<?php
$s = isset($_REQUEST['sect']) ? $_REQUEST['sect'] : '';
if(isset($_SESSION['ww_is_builder']))
	$f = $_SESSION['ww_is_builder'];
else
	$f = '';

$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$pi = pathinfo($refering_url);
$u = str_replace('pms.php','',$pi['basename']);
?>
<div id="top">
	<div class="header_container">
		<div class="logo"><img src="images/logo.png" border="none" alt="Logo" align="absmiddle" style="margin-top:0px;" /></div>
        	<?php if(isset($_SESSION['ww_logged_in_as'])){?>
	<div class="welcome">Welcome <?=$_SESSION['ww_logged_in_as']?><a href="c_logout.php"><img src="images/logout_btn.png" width="63" height="24"  align="absmiddle" hspace="11"/></a></div>
	<? }?>
	</div>
	<!--Navigation-->
	<div id="nav" class="cwidth">
		<ul>
<?php if($s=='c_dashboard' || $s=='c_dashboard_edit' || $s=='c_builder' || $s=='c_add_builder' || $s=='log' || $s=='c_remove_builder' || $s=='c_full_analysis' || $s=='c_show_project_detail'  || $s=='b_progress' || $s=='c_report'|| $s=='c_changepassword' || $s=='c_defect' || $s='show_defect_photo'){ ?>			
			<li <?php if($s=='c_full_analysis') { ?> class="active_link" <?php } ?> ><a href="?sect=c_full_analysis" style="border:none;"><img src="images/health_check.png" width="28" height="29" hspace="5" align="absmiddle" />Health Check</a></li>
            
            <li <?php if($s=='b_full_analysis' || $s=='c_changepassword') { ?> class="active_link" <?php } ?>><a href="?sect=c_dashboard"><img src="images/myprofile.png" width="28" height="29" hspace="5" align="absmiddle" />My Profile</a></li>
            
			<li ><a href="?sect=c_builder"><img src="images/manager.png" width="28" height="29" hspace="5" align="absmiddle" />Users</a></li>

            <li <?php if($s=='b_project' || $s=='show_project' || $s=='add_project' || $s=='add_project_detail' || $s=='show_sub_loc' || $s=='add_sub_loc' || $s=='project_configuration'|| $s=='progress_monitoring'|| $s=='issue_to' || $s=='standard_defect' || $s=='add_progress_task' || $s=='edit_progress_task' || $s=='add_issue_to' || $s=='edit_issue_to'  || $s=='add_standard_defect' || $s=='edit_standard_defect') { ?> class='active_link' <?php } ?> ><a href="?sect=show_project&req=C" ><img src="images/project.png" width="33" height="27" hspace="5" align="absmiddle" />Projects</a></li>

			<li <?php if($s=='c_issue_to') { ?> class="active_link" <?php } ?> ><a href="?sect=c_issue_to"><img src="images/issuedTo.png" width="24" height="25" hspace="5" align="absmiddle" />Issued To</a></li>
			
			<li <?php if($s=='c_qa_checklist') { ?> class="active_link" <?php } ?> ><a href="?sect=c_qa_checklist"><img src="images/checklist.png" width="24" height="25" hspace="5" align="absmiddle" />QA Checklist</a></li>
			
			<li <?php if($s=='c_report') { ?> class="active_link" <?php } ?>  ><a href="?sect=c_report"><img src="images/reports.png" width="23" height="29" hspace="5" align="absmiddle" />Reports<!--<img src="images/down_arrow.png" width="8" height="5" hspace="3"  align="absmiddle"/>--></a></li>
			
			<li <?php if($s=='c_defect') { ?> class="active_link" <?php } ?> ><a href="?sect=c_defect"><img src="images/inspection.png" width="24" height="25" hspace="5" align="absmiddle" />Inspections</a></li>
			
			<li <?php if($s=='o_and_m_workflow') { ?> class="active_link" <?php } ?> ><a href="?sect=o_and_m_workflow"><img src="images/email-detail.png" width="24" height="25" hspace="5" align="absmiddle" />Workflow</a></li>

			<li <?php if($sect=='c_organisations') { ?> class="active_link" <?php } ?> >
                <a href="?sect=c_organisations">
                    <img src="images/c.png" width="28" height="29" hspace="5" align="absmiddle" />Companies
                </a>
            </li>
			
<?php }elseif($s=='issue_photo'){ ?>
			
			<li style="position: relative; float: left; width: 48px; height: 48px; margin-right: 0px; left: 0px; top: 0px;"><a href="<?=$u?>"><img fmtooltip="Back" src="menu/index_data/back.png" alt="Back"></a>
				<div class="FMTooltipClass" style="position: absolute; z-index: 8; opacity: 0; display: none;">Back</div>
			</li>
<?php }else{ ?>
			<li style="position: relative; float: left; width: 48px; height: 48px; margin-right: 0px; left: 0px; top: 0px;">
				<a href="index.php"><img fmtooltip="Home" src="menu/index_data/home.png" alt="Home"></a>
				<div class="FMTooltipClass" style="position: absolute; z-index: 8; opacity: 0; display: none;">Home</div>
			</li>
<?php } ?>
		</ul>
    </div>
</div>
