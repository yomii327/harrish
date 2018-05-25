<?php session_start();
require_once'includes/functions.php';
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {// last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time
    session_destroy();   // destroy session data in storage?>
<script language="javascript" type="text/javascript"> window.location.href="<?=HOME_SCREEN?>"; </script>
<?php }
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
$obj = new DB_Class(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="cache-control" content="max-age=0" />
  <meta http-equiv="cache-control" content="no-cache" />
  <meta http-equiv="expires" content="0" />
  <meta http-equiv="pragma" content="no-cache" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="format-detection" content="telephone=no">
  <link rel="icon" href="images/ww_favicon.gif" type="image/gif" />

  <title><?=SITE_NAME?></title>

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script type="text/javascript" src="js/vendors/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/location_tree_jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.ui.core.js"></script>
	<script type="text/javascript" src="js/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="js/jquery.ui.mouse.js"></script>
	<script type="text/javascript" src="js/jquery.ui.sortable.js"></script>
	<script type="text/javascript" src="js/modal.popup.js"></script>
	<script type="text/javascript" src="js/jquery.alerts.js"></script>
  <link rel="stylesheet" href="css/vendors/bootstrap.min.css" type="text/css" />
  <link rel="stylesheet" href="css/custom.css" type="text/css" />
	<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="style.css" type="text/css" />
	<!--[if IE]><script language="javascript" type="text/javascript" src="../dist/excanvas.js"></script><![endif]-->
	<!--[if IE]><link href="style_ie.css" rel="stylesheet" type="text/css" /><![endif]-->

	<link rel="stylesheet" href="css/menu_style.css?v=<?php echo time(); ?>" type="text/css" />
	<link rel="stylesheet" href="css/menu_style_new.css?v=<?php echo time(); ?>" type="text/css" />
	<link rel="stylesheet" href="style/css/ajax-uploader.css?v=<?php echo time(); ?>" type="text/css" />
	<link rel="stylesheet" href="css/jsDatePick_ltr.min.css" type="text/css" media="all"/>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans|Ubuntu" rel="stylesheet">
  <?php /*
  		$msie = strpos($_SERVER["HTTP_USER_AGENT"], 'MSIE') ? true : false;
  		$firefox = strpos($_SERVER["HTTP_USER_AGENT"], 'Firefox') ? true : false;
  		$safari = strpos($_SERVER["HTTP_USER_AGENT"], 'Safari') ? true : false;
  		$chrome = strpos($_SERVER["HTTP_USER_AGENT"], 'Chrome') ? true : false;
  		*/
  		//$chrome = strpos($_SERVER["HTTP_USER_AGENT"], 'Chrome') ? 'Chrome' : '';
  ?>
  <!--	<script type="text/javascript">function goto(pg){window.location.href=pg;}</script>
  	<script type="text/javascript" src="js/jquery.tools.min.js"></script>
  -->
  <style>
    #spinner {
      display: none;
      width:99%;
      height: 100%;
      position: fixed;
      top: 0;
      left: 0;
      background:url(images/loadingAnimation.gif) no-repeat center #CCCCCC;
      filter:alpha(opacity=80) !important;
      text-align:center;
      padding:10px;
      font:normal 16px Tahoma, Geneva, sans-serif;
      border:1px solid #666;
      z-index:1000000000000 !important;
      overflow: auto;
      opacity : 0.8;
    }
	</style>
</head>
<body>
<div class="pmsMain">
<div id="spinner" style="filter:alpha(opacity=40);"></div>
	<?php
	if(isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company'] == 1){	// company
		include'includes/c_header.php';
	}elseif(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 1){	// builder
		include'includes/b_header.php';
	}elseif(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){	// inspector
		include'includes/o_header.php';
	}elseif(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 2){ // trade
		include'includes/r_header.php';
	}else{	// default
		include'includes/header.php';
	}

	$widthVal = 1002; $flagForWidth = 0;
	if(isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company']==1){
		$widthVal = 1030;
		$flagForWidth = 1;
	}

	?>
	<div id="middle">
	<?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){	// inspector?>
		<div id="leftNavigation">
			<?php include'includes/o_navigation.php';?>
		</div>
	<?php } ?>
		<div <?php if(isset($_SESSION['ww_is_builder']) && $_SESSION['ww_is_builder'] == 0){ echo 'style="width:755px;float:left;background-position:left; background-image:url(images/v-divider.png); background-repeat:repeat-y; padding-left:10px;margin-top:0px; padding-top:20px; min-height:725px;"'; }elseif($flagForWidth==1){ echo 'style="width:1030px !important;"'; } ?>>
			<?php

			if(isset($_GET['sect']))
				$page = $_GET['sect'];
			else
				$page = 'index';

			switch($page){

				// General
				case'forgot_password':
					include'includes/forgot_password.php';
				break;

				case'log':
					include'includes/log.php';
				break;
				// ------------------------------------------------------

				// Company
				case'company':
					include'includes/company.php';
				break;

				case'c_show_project_detail':
					include'includes/c_show_project_detail.php';
				break;

				case'c_full_analysis':
					include'includes/c_full_analysis.php';
				break;

				case'c_dashboard':
					include'includes/c_dashboard.php';
				break;

				case'c_dashboard_edit':
					include'includes/c_dashboard_edit.php';
				break;

				case'c_builder':
					include'includes/c_builder.php';
				break;

				case'c_add_builder':
					include'includes/c_add_builder.php';
				break;

				case'c_remove_builder':
					include'includes/c_remove_builder.php';
				break;

				case'c_changepassword':
					include'includes/c_changepassword.php';
				break;

				case'c_report':
					include'includes/c_report.php';
				break;

				case'c_defect':
					include'includes/c_defect.php';
				break;
				// ------------------------------------------------------

				// Builder
				case'b_full_analysis':
					include'includes/b_full_analysis.php';
				break;

				case'b_show_project_detail':
					include'includes/b_show_project_detail.php';
				break;

				case'builder':
					include'includes/builder.php';
				break;

				case'b_dashboard':
					include'includes/b_dashboard.php';
				break;

				case'b_dashboard_edit':
					include'includes/b_dashboard_edit.php';
				break;

				case'b_project':
					include'includes/b_project.php';
				break;

				case'show_project':
					include'includes/show_project.php';
				break;

				case'add_project':
					include'includes/add_project.php';
				break;

				case'edit_project':
					include'includes/edit_project.php';
				break;

				case'add_project_detail':
					include'includes/add_project_detail.php';
				break;

				case'show_defects_list':
					include'includes/show_defects_list.php';
				break;

				case'add_defects_list':
					include'includes/add_defects_list.php';
				break;

				case'edit_defects_list':
					include'includes/edit_defects_list.php';
				break;

				case'show_sub_loc':
					include'includes/show_sub_loc.php';
				break;

				case'add_sub_loc':
					include'includes/add_sub_loc.php';
				break;

				case'edit_sub_loc':
					include'includes/edit_sub_loc.php';
				break;

				case'assign_to':
					include'includes/assign_to.php';
				break;

				case'add_assign_to':
					include'includes/add_assign_to.php';
				break;

				case'edit_assign_to':
					include'includes/edit_assign_to.php';
				break;

				case'show_responsible':
					include'includes/show_responsible.php';
				break;

				case'add_responsible':
					include'includes/add_responsible.php';
				break;

				case'edit_responsible':
					include'includes/edit_responsible.php';
				break;

				case'b_defect':
					include'includes/b_defect.php';
				break;

				case'issue_photo':
					include'includes/issue_photo.php';
				break;

				case'show_defect_photo':
					include'includes/show_defect_photo.php';
				break;

				case'edit_defect':
					include'includes/edit_defect.php';
				break;

				case'tenant':
					include'includes/tenant.php';
				break;

				case'load_report':
					include'load_report.php';
				break;
				case'project_configuration':
					include'includes/project_configuration.php';
				break;
/*Start construction_calendar*/

				case'construction_calendar':
					include'includes/construction_calendar.php';
				break;
/*End construction_calendar*/

				case'show_inspection':
					include'includes/show_inspection.php';
				break;
				case'progress_monitoring':
					include'includes/progress_monitoring.php';
				break;
				case'issue_to':
					include'includes/issue_to.php';
				break;

				case'c_issue_to':
					include'includes/c_issue_to.php';
				break;

				case'b_progress':
					include'includes/b_progress.php';
				break;

				case'standard_defect':
					include'includes/standard_defect.php';
				break;

				case'add_progress_task':
					include'includes/add_progress_task.php';
				break;

				case'edit_progress_task':
					include'includes/edit_progress_task.php';
				break;

				case'add_issue_to':
					include'includes/add_issue_to.php';
				break;

				case'edit_issue_to':
					include'includes/edit_issue_to.php';
				break;

				case'edit_standard_defect':
					include'includes/edit_standard_defect.php';
				break;

				case'add_standard_defect':
					include'includes/add_standard_defect.php';
				break;

				// ------------------------------------------------------

				// Tenant / Inspactor / Owner
				case'o_edit_defect':
					include'includes/o_edit_defect.php';
				break;

				case'o_dashboard':
					include'includes/o_dashboard.php';
				break;

				case'access_denied':
					include'includes/access_denied.php';
				break;

				case'add_defect':
					include'includes/add_defect.php';
				break;

				case'o_defect':
					include'includes/o_defect.php';
				break;

				case'i_defect':
					include'includes/i_defect.php';
				break;

				case'i_report'://Report
					include'includes/i_report.php';
				break;

				case'i_progress_monitor'://Progress Monitoring
					include'includes/i_progress.php';
				break;
				// ------------------------------------------------------

				// Repairer / Assign To / Responsible
				case'responsible':
					include'includes/responsible.php';
				break;

				case'r_dashboard':
					include'includes/r_dashboard.php';
				break;

				case'r_defect':
					include'includes/r_defect.php';
				break;

				case'r_edit_defect':
					include'includes/r_edit_defect.php';
				break;
	//GS
				case'permissions':
					include'includes/permissions_setting.php';
				break;

				case'checklist':
					include'includes/checklist.php';
				break;

				case'edit_checklist':
					include'includes/edit_checklist.php';
				break;

				case'inspection_checklist':
					include'includes/inspection_check.php';
				break;

	//GS
			case'import_inspections':
				include'includes/import_inspections.php';
			break;

			case'drawing_management':
				include'includes/drawing_management.php';
			break;

			case'edit_drawing_management':
					include'includes/edit_drawing_image.php';
			break;

			case'sync_permission':
				include'includes/sync_permission.php';
			break;

			case'project_manual':
				include'includes/manual_chapter.php';
			break;

			case'edit_project_manual':
				include'includes/edit_manual_chapter.php';
			break;

			case'manual_sub_chapter':
				include'includes/manual_sub_folder.php';
			break;

			case'edit_manual_sub_chapter':
				include'includes/edit_manual_sub_folder.php';
			break;

			case'manual_mangage_files':
				include'includes/manual_mangage_files.php';
			break;

			case'edit_manual_mangage_files':
				include'includes/edit_manual_mangage_files.php';
			break;


			case'search_chapter':
				include'chapter_search.php';
			break;

			case'manual_file_view':
				include'includes/manual_file_view.php';
			break;

			case'qa_task_monitoring':
				include'includes/qa_task_monitoring.php';
			break;

			case'add_qa_task':
				include'includes/add_qa_task.php';
			break;

			case'edit_qa_task':
				include'includes/edit_qa_task.php';
			break;

			case'qa_task_search':
				include'includes/qa_task_search.php';
			break;

			case'loc_level_sync_permission':
				include'includes/location_level_sync_permission.php';
			break;

			case'messages':
				include'includes/messages.php';
			break;

			case'drawing_register':
				if($_SESSION['drawingModuleType'][$checkProject] != 0){
		#			echo 'version 1';
					include'includes/drawing_register.php';
				}else{
		#			echo 'version 2';
					include'includes/drawing_register_v1.php';
				}
			break;

			case'login':
				include'includes/login.php';
			break;

			case'dashboard_detail':
				include'dashboard_detail.php';
			break;

			case 'pmb_my_profile':
				include'includes/pmb_my_profile.php';
			break;

			case 'pmb_sub_folder':
				include'includes/sub_folder.php';
			break;

			case'drafts':
				include'includes/draft.php';
			break;

			case'trash':
				include'includes/trash.php';
			break;

			case'inbox_insp':
				include'includes/inboxInsp.php';
			break;

			case'compose':
				include'includes/compose.php';
			break;

			case'sent_box':
				include'includes/sent_box.php';
			break;

			case'forward':
				include'includes/forward.php';
			break;

			case'address_book':
				include'includes/address_book.php';
			break;

			case'details_search':
				include'details_search.php';
			break;

			case 'drawing_register_select':
				include'includes/drawing_register_select.php';
			break;

			case 'event_calendar':
				include'includes/event_calendar.php';
			break;

			case'o_and_m_workflow':
				include'includes/o_and_m_workflow.php';
			break;

			case'message_details':
				include'includes/message_details.php';
			break;

			case'view_message':
				include'includes/view_message.php';
			break;

			case'cron_schedule':
				include'includes/cron_schedule.php';
			break;

			case 'workflow':
				include'includes/workflow.php';
			break;

			case 'c_organisations':
				include'includes/c_organisations.php';
			break;

			case 'subcontractor_database':
				include'includes/c_subcontractor_database.php';
			break;

			case 'add_edit_subcontractor':
				include'add_edit_subcontractor.php';
			break;

			case'c_qa_checklist':
				include'includes/c_qa_checklist.php';
			break;

			case'qa_quality_assurance':
				include'includes/qa_quality_assurance.php';
			break;

			case'qc_task_search':
				include 'includes/qc_task_search_new.php';
			break;

			case'add_qc_task':
				include'includes/add_checklist_new.php';
			break;

			case'edit_qc_task':
				include'includes/edit_checklist_new.php';
			break;

			case'qc_non_conformance':
				include 'includes/qc_non_conformance.php';
			break;

			case'draw_markup':
				include 'draw_markupNew.php';
			break;
			case'draw_markup1':
				include 'draw_markup1.php';
			break;
			case'draw_markup2':
				include 'draw_markup.php';
			break;
			case'draw_markupa':
				include 'draw_markupAngular.php';
			break;


			case'draw_markup_g':
				include 'draw_markup_g.php';
			break;

			// ------------------------------------------------------
			// Default
			default:
				include'includes/404-error.php';
			break;
			// ------------------------------------------------------
			}
			?>
		</div>
	</div>
	<?php include'includes/footer.php';
	require_once"get_colour_code_by_company.php";?>
</div>
</body>
</html>
