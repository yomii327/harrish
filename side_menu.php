<table width="100%" border="0" align="left" cellpadding="5" cellspacing="0">
	<tr>
	<td width="24%" align="left" valign="top">
		<!-- <a href="#" <?php //if($_GET['sect'] == 'o_dashboard'){echo 'class="left_btn1active"';}?>  class="left_btn1"><br />-->
		<a href="pms.php?sect=project_configuration" <?php if($_GET['sect'] == 'project_configuration'){echo 'class="left_btn2active"';}?> class="left_btn2 left_button" ><i></i><span>Location & Sub Location</span></a>
		<br />
		
		<a href="pms.php?sect=issue_to"  <?php if($_GET['sect'] == 'issue_to'){echo 'class="left_btn3active"';}?> class="left_btn3 left_button"><i></i><span>Issued To</span></a>
        <br />

        <a href="pms.php?sect=standard_defect" <?php if($_GET['sect'] == 'standard_defect'){echo 'class="left_btn4active"';}?>  class="left_btn4 left_button"><i></i><span>Standard Defect List</span></a>
        <br />

        <?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
        <a href="pms.php?sect=progress_monitoring" <?php if($_GET['sect'] == 'progress_monitoring'){echo 'class="left_btn5active"';}?> class="left_btn5 left_button" ><i></i><span>Progress Task Management</span></a>
        <br />
        <?php }?>

        <?php if(isset($_SESSION['web_menu_qa_task']) && $_SESSION['web_menu_qa_task'] == 1){?>
        <a style="display: none;" href="pms.php?sect=qa_task_monitoring" <?php if($_GET['sect'] == 'qa_task_monitoring'){echo 'class="left_btn11active"';}?> class="left_btn11 left_button" ><i></i><span>Quality Assurance</span>
        <!-- <br /> -->
        <?php }?>

        <a href="pms.php?sect=show_sub_loc" <?php if($_GET['sect'] == 'show_sub_loc'){echo 'class="left_btn6active"';}?> class="left_btn6 left_button" ><i></i><span>Associate Users</span></a>
        <br />

        <?php if($_SESSION['set_user_permission'] == 1 ){?>
        <a href="pms.php?sect=permissions" <?php if($_GET['sect'] == 'permissions'){echo 'class="left_btn7active"';}?> class="left_btn7 left_button" ><i></i><span>User Permission</span></a><br />
        <?php }?>


		<?php if($_SESSION['is_pdf']==1){?>
			<a href="pms.php?sect=drawing_register" <?php if($_GET['sect'] == 'drawing_register'){echo 'class="left_btn14active"';}?> class="left_btn14 left_button" ><i></i><span>Drawing Management</span></a><br />
		<?php }else{ 
			if(isset($_SESSION['web_drawingManagement_permission']) && $_SESSION['web_drawingManagement_permission'] == 1 && $_SESSION['is_pdf']==0){?>
				<a href="pms.php?sect=drawing_management" <?php if($_GET['sect'] == 'drawing_management'){echo 'class="left_btn10active"';}?> class="left_btn10 left_button"><i></i><span>Drawing Management</span></a><br />
			<?php }
		}?>

		<?php if(isset($_SESSION['web_menu_checklist']) && $_SESSION['web_menu_checklist'] == 1 && !isset($_SESSION['ww_is_company'])){?>
        <a href="pms.php?sect=qa_quality_assurance" <?php if($_GET['sect'] == 'qa_quality_assurance'){echo 'class="left_btn8active"';}?> class="left_btn8 left_button"><i></i><span>Quality Checklist</span></a><br />
        <?php }?>
		
		<?php #if($_SESSION['web_sync_permission'] == 1){?>
		<a href="pms.php?sect=sync_permission" <?php if($_GET['sect'] == 'sync_permission'){echo 'class="left_btn9active"';}?> class="left_btn9 left_button" ><i></i><span>Synchronization Permissions</span></a><br />
		<?php #}?>

		<?php //Start construction_calendar
		#if($_SESSION['web_sync_permission'] == 1){?>
		<a href="pms.php?sect=construction_calendar" <?php if($_GET['sect'] == 'construction_calendar'){echo 'class="left_btn13active"';}?> class="left_btn13 left_button" ><i></i><span>Construction Calendar</span></a><br />
		<?php #}?>

		<a href="pms.php?sect=cron_schedule" <?php if($_GET['sect'] == 'cron_schedule'){echo 'class="left_btn11active"';}?> class="left_btn11 left_button" ><i></i><span>Email Schedule</span></a><br />

		<?php if(isset($_SESSION['web_message_board']) && $_SESSION['web_message_board'] == 1){ ?>
			<a href="pms.php?sect=workflow" <?php if($_GET['sect'] == 'workflow'){echo 'class="left_btn21active"';}?> class="left_btn21 left_button" ><i></i><span>Workflow</span></a><br />
		<?php } ?>
	</td>
	</tr>
</table>
<?php /*
<table width="100%" border="0" align="left" cellpadding="5" cellspacing="0">
	<tr>
	<td width="24%" align="left" valign="top">
		<!-- <a href="#" <?php //if($_GET['sect'] == 'o_dashboard'){echo 'class="left_btn1active"';}?>  class="left_btn1"><br />-->
		</a><br /><a href="pms.php?sect=project_configuration" <?php if($_GET['sect'] == 'project_configuration'){echo 'class="left_btn2active"';}?> class="left_btn2" ><br />
		</a><br /><a href="pms.php?sect=issue_to"  <?php if($_GET['sect'] == 'issue_to'){echo 'class="left_btn3active"';}?> class="left_btn3"><br />
		</a><br /><a href="pms.php?sect=standard_defect" <?php if($_GET['sect'] == 'standard_defect'){echo 'class="left_btn4active"';}?>  class="left_btn4"><br />
		<?php if($_SESSION['web_menu_progress_monitoring'] == 1){?>
			</a><br /><a href="pms.php?sect=progress_monitoring" <?php if($_GET['sect'] == 'progress_monitoring'){echo 'class="left_btn5active"';}?> class="left_btn5" ><br />
		<?php }?>
		<?php if(isset($_SESSION['web_menu_qa_task']) && $_SESSION['web_menu_qa_task'] == 1){?>
			</a><br /><a style="display: none;" href="pms.php?sect=qa_task_monitoring" <?php if($_GET['sect'] == 'qa_task_monitoring'){echo 'class="left_btn11active"';}?> class="left_btn11" ><br />
		<?php }?>
		</a><br />
		<a href="pms.php?sect=show_sub_loc" <?php if($_GET['sect'] == 'show_sub_loc'){echo 'class="left_btn6active"';}?> class="left_btn6" ><br /></a><br />
		<?php if($_SESSION['set_user_permission'] == 1){?>
			<a href="pms.php?sect=permissions" <?php if($_GET['sect'] == 'permissions'){echo 'class="left_btn7active"';}?> class="left_btn7" ><br /></a><br />
		<?php }?>
		<?php if($_SESSION['is_pdf']==1){?>
			<a href="pms.php?sect=drawing_register" <?php if($_GET['sect'] == 'drawing_register'){echo 'class="left_btn14active"';}?> class="left_btn14" ><br />
			</a><br />
		<?php }else{ 
			if(isset($_SESSION['web_drawingManagement_permission']) && $_SESSION['web_drawingManagement_permission'] == 1 && $_SESSION['is_pdf']==0){?>
				<a href="pms.php?sect=drawing_management" <?php if($_GET['sect'] == 'drawing_management'){echo 'class="left_btn10active"';}?> class="left_btn10" ><br />
				</a><br />
			<?php }
		}?>
		<?php if(isset($_SESSION['web_menu_checklist']) && $_SESSION['web_menu_checklist'] == 1){?>
			<a href="pms.php?sect=qa_quality_assurance" <?php if($_GET['sect'] == 'qa_quality_assurance'){echo 'class="left_btn16active"';}?> class="left_btn16" ></a>
		<?php }?>
		<br>
		
		<?php #if($_SESSION['web_sync_permission'] == 1){?>
		<a href="pms.php?sect=sync_permission" <?php if($_GET['sect'] == 'sync_permission'){echo 'class="left_btn9active"';}?> class="left_btn9" ><br /></a><br />
		<?php #}?>

		<?php //Start construction_calendar
		#if($_SESSION['web_sync_permission'] == 1){?>
		<a href="pms.php?sect=construction_calendar" <?php if($_GET['sect'] == 'construction_calendar'){echo 'class="left_btn13active"';}?> class="left_btn13" ><br /></a><br />
		<?php #}?>

		<a href="pms.php?sect=cron_schedule" <?php if($_GET['sect'] == 'cron_schedule'){echo 'class="left_btn15active"';}?> class="left_btn15" ><br /></a><br />
		<!-- <a href="pms.php?sect=cron_schedule" <?php if($_GET['sect'] == 'cron_schedule'){echo 'class="left_btn15active"';}?> class="left_btn15 left_button" ><i></i><span>Cron Schedule</span></a><br /> -->
		<?php if(isset($_SESSION['web_message_board']) && $_SESSION['web_message_board'] == 1){ ?>
			<a href="pms.php?sect=workflow" <?php if($_GET['sect'] == 'workflow'){echo 'class="left_btn21active"';}?> class="left_btn21" ><br /></a><br />
		<?php } ?>
	</td>
	</tr>
</table>
*/?>
