<div class="navigation">
	<div id="nav">
		<ul>
	<?php
		if($s=='o_dashboard' || $s=='add_defect' || $s=='o_edit_defect' || $s=='o_defect' || $s=='i_report' || $s=='i_progress_monitor' || $s=='i_defect' || ($f==0&&$s=='show_defect_photo') || ($f==0&&$s=='show_sub_loc') ){
	?>
		    <li><a <?php if($_GET['sect'] == 'o_dashboard'){echo 'class="button1active"';}?> href="?sect=o_dashboard" class="button1"></a></li>
			<li ><a <?php if($_GET['sect'] == 'o_defect'){echo 'class="button2active"';}?> href="?sect=o_defect" class="button2"></a></li>				
			<li><a <?php if($_GET['sect'] == 'i_defect'){echo 'class="button3active"';}?>  href="?sect=i_defect" class="button3"></a></li>	
			<li><a <?php if($_GET['sect'] == 'i_report'){echo 'class="button4active"';}?>  href="?sect=i_report" class="button4"></a></li>
			<li><a <?php if($_GET['sect'] == 'i_progress_monitor'){echo 'class="button5active"';}?>  href="#" class="button5"></a></li>   
	<?php }elseif($s=='issue_photo'){ ?>
		<li style="position: relative; float: left; width: 48px; height: 48px; margin-right: 0px; left: 0px; top: 0px;">
			<a href="<?=$u?>"><img fmtooltip="Back" src="menu/index_data/back.png" alt="Back"></a>
		<div class="FMTooltipClass" style="position: absolute; z-index: 8; opacity: 0; display: none;">Back</div></li>
	<?php }else{ ?>
			<li style="position: relative; float: left; width: 48px; height: 48px; margin-right: 0px; left: 0px; top: 0px;">
				<a href="index.php"><img fmtooltip="Home" src="menu/index_data/home.png" alt="Home"></a>
			<div class="FMTooltipClass" style="position: absolute; z-index: 8; opacity: 0; display: none;">Home</div></li>
	<?php } ?>
		</ul>
	</div>
</div>