<?php $s = isset($_REQUEST['sect']) ? $_REQUEST['sect'] : '';
	if(isset($_SESSION['ww_is_builder'])){
		$builder_id = $_SESSION['ww_builder_id'];
		$f = $_SESSION['ww_is_builder'];
		//Get project for drawing register
		$pQuery = 'SELECT project_id, project_name FROM user_projects WHERE user_id = '.$builder_id.' AND is_deleted = 0 AND is_pdf = 1 GROUP BY project_name';
		#echo $pQuery;
		$pResult = mysql_query($pQuery);
		while($pRow = mysql_fetch_assoc($pResult)) {
			$pidArr[] = base64_encode($pRow['project_id']);
			if(!isset($_SESSION['idp'])){
				$_SESSION['idp'] = $pRow['project_id'];
			}
		}
	} else {
		$f = '';
	}
	#echo "<pre>"; print_r($_SESSION); die;
	$refering_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	$pi = pathinfo($refering_url);
	$u = str_replace('pms.php','',$pi['basename']); ?>

<div id="top">
	<header>
		<div class="container no-padding">
			<div class="row">
				<div class="col-md-4 no-padding">
					<div class="logo">
						<a href="?sect=b_full_analysis">
							<img src="images/logo.png" border="none" alt="Logo" align="absmiddle"/>
						</a>
					</div>
				</div>
				<div class="col-md-8">
					<?php if(isset($_SESSION['ww_logged_in_as'])){?>
						<div class="welcome">
							<div class="header-icon"><i class="fas fa-user"></i></div>
							<div class="header-text">
								Welcome <span class="highlight"><?=$_SESSION['ww_logged_in_as']?></span>
							</div>
							<div class="logout">
								<a href="b_logout.php">
									<button type="button" class="btn btn-default"><i class="fas fa-sign-out-alt"></i> Log out</button>
								</a>
							</div>
						</div>
					<?php }?>
				</div>
			</div>
		</div>
	</header>

	<nav class="navbar-default navigation">
		<div class="container no-padding">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
				<div class="visible-mobile">
					<div class="logout">
						<a href="b_logout.php">
							<button type="button" class="btn btn-default"><i class="fas fa-sign-out-alt"></i> Log out</button>
						</a>
					</div>
				</div>
			</div>
			<div id="nav" class="navbar-collapse collapse">
				<ul class="navbar-nav">
					<?php if(isset($_SESSION['web_message_board']) && $_SESSION['web_message_board'] == 1){ ?>
						<li class="<?php if($s=='messages' || $s=='compose' || $s=='sent_box' || $s=='view_message' || $s=='message_details' || $s=='drafts' || $s=='trash' || $s=='address_book' || $s=='details_search'){echo 'active_link';}?>">
							<a href="<?php echo (isset($_SESSION['idp']) && $_SESSION['idp'] > 0)?'?sect=messages':'#';?>" onclick="NoactiveProjects('messages')" >
								<i class="fas fa-envelope"></i> Message Board
							</a>
						</li>
					<?php } ?>

					<?php if(!empty($pidArr)){?>
						<li <?php if($s=='drawing_register') { ?> class="active" <?php } ?>  >
							<a href="?sect=drawing_register&type=pmb&id=<?php echo $pidArr[0]; ?>">
								<i class="fas fa-file-alt"></i> Document Register
							</a>
						</li>
					<?php } ?>

					<?php if($_SESSION['web_menu_health_checkup'] == 1){
						if($_SESSION['userRole'] != 'Sub Contractor'){?>
							<li <?php if($s=='b_full_analysis') { ?> class="active" <?php } ?> >
								<a href="?sect=b_full_analysis" style="border:none;">
									<i class="fas fa-briefcase-medical"></i> Health Check
								</a>
							</li>
						<?php }
					}?>

					<?php #if($_SESSION['web_menu_my_profile'] == 1){?>
						<!--<li <?php if($s=='b_dashboard'||$s=='b_dashboard_edit'){?> class="active" <?php }?>><a href="?sect=b_dashboard" ><i class="fas fa-tachometer-alt"></i> My Profile</a></li>-->
					<?php #}?>

					<?php if($_SESSION['web_menu_projects'] == 1){?>
						<li <?php if($s=='b_project' || $s=='show_project' || $s=='add_project' || $s=='add_project_detail' || $s=='show_sub_loc' || $s=='add_sub_loc' || $s=='project_configuration'|| $s=='progress_monitoring'|| $s=='issue_to' || $s=='standard_defect' || $s=='add_progress_task' || $s=='edit_progress_task' || $s=='add_issue_to' || $s=='edit_issue_to'  || $s=='add_standard_defect' || $s=='edit_standard_defect') { ?> class='active' <?php } ?> >
							<a href="?sect=show_project" >
								<i class="fas fa-project-diagram"></i> Projects
							</a>
						</li>
					<?php }?>

					<?php if($_SESSION['web_menu_reports'] == 1){?>
						<li <?php if($s=='i_report') { ?> class="active" <?php } ?>  >
							<a href="?sect=i_report">
								<i class="fas fa-flag"></i> Reports
							</a>
						</li>
					<?php }?>

					<?php if($_SESSION['web_menu_quality_checklist'] == 1){?>
						<li <?php if($s=='qc_non_conformance') { ?> class="active" <?php } ?>>
							<a href="?sect=qc_non_conformance" >
								<i class="fas fa-exclamation-circle"></i> Non Conformances
							</a>
						</li>
					<?php } ?>

					<?php if($_SESSION['web_menu_quality_control'] == 1){?>
						<li <?php if($s=='i_defect') { ?> class="active" <?php } ?> >
							<a href="?sect=i_defect">
								<i class="fab fa-sistrix"></i> Inspections
							</a>
						</li>
					<?php }?>

					<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
						<li <?php if($s=='b_progress') { ?> class="active" <?php } ?>>
							<a href="?sect=b_progress" >
								<i class="fas fa-chart-line"></i> Progress Monitoring
							</a>
						</li>
					<?php }?>

					<?php if($_SESSION['web_menu_quality_checklist'] == 1){?>
						<li <?php if($s=='qc_task_search') { ?> class="active" <?php } ?>>
							<a href="?sect=qc_task_search" >
								<i class="fas fa-clipboard-check"></i> Quality Checklist
							</a>
						</li>
					<?php } ?>

					<?php if($_SESSION['web_menu_qa_task'] == 1){?>
						<li style="display: none;" <?php if($s=='qa_task_search') { ?> class="active" <?php } ?>>
							<a href="?sect=qa_task_search" >
								<i class="fas fa-clipboard-check"></i> Quality Assurance
							</a>
						</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</nav>

<script type="text/javascript">
	function NoactiveProjects(tab){
		var projId = <?php echo $_SESSION['idp']?>;
		//alert(projId);
		if(projId == 0 ){
			if(tab == 'drawing_register'){
				jAlert("You do not have any active project","Document Register");
			}
			if(tab == 'messages'){
				jAlert("You do not have any active project","Message Board");
			}
		}
		return true;
	}
</script>
