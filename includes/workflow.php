<?php include_once("commanfunction.php");
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);
include('func.php');
if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){ ?>
<script language="javascript" type="text/javascript">
	window.location.href = "<?=HOME_SCREEN?>";
</script>
<?php }
if (isset($_SESSION['ww_is_company']) && $_SESSION['ww_is_company'] == 1) {
	$builder_id = $_SESSION['ww_is_company']; 
}else{
	$builder_id = $_SESSION['ww_builder_id']; 
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 1;
?>
<link href="style.css" rel="stylesheet" type="text/css" />
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}
.box1 { background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
.link1 { background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
a.link1:hover { background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
.txt13 { border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }
/* onto CSS now */
@import url(http://fonts.googleapis.com/css?family=Open+Sans:600);

body {
	/*padding: 20px; */
	background: whiteSmoke;
	font-family: 'Open Sans';
}

#menu {
	text-align: center;
}

.nav {
	list-style: none;
	display: inline-block; /* for centering */
	/*border: 1px solid #b8b8b8;*/
	font-size: 14px;
	margin: 0; padding: 0;
}

.nav li {
	border-left: 1px solid #b8b8b8;
	float: left;
}
.nav li:first-child {
	border-left: 0;
}

.nav a {
	color: #2f2f2f;
	padding: 0 20px;
	line-height: 32px;
	display: block;
	text-decoration: none;
	
	background: #fbfbfb;
	background-image: linear-gradient(#fbfbfb, #f5f5f5);
}

.nav a:hover {
	background: #fcfcfd;
	background-image: linear-gradient(#fff, #f9f9f9);
}

.nav a.active,
.nav a:active {
	background: #94CE06;
	/*background-image: linear-gradient(#e8e8e8, #f5f5f5);*/
}


/* Tab Panes now */

#tab_panes {
	max-width: 600px;
	margin: 20px auto;
}

.tab_pane {
	display: none;
}
.tab_pane.active {
	display: block;
}

#tab_panes img {
	max-width: 600px;
	box-shadow: 0 0 5px rgba(0,0,0,0.5);
}
</style>
<div id="middle" style="padding-top:10px;">
	<div id="leftNav" style="width:250px;float:left;">
		<?php include 'side_menu.php';?>
	</div>
	<?php $id=base64_encode($_SESSION['idp']); $hb=base64_encode($_SESSION['hb']); ?>
	<div id="rightCont" style="float:left;width:700px;">
		<div class="content_hd1" style="width:500px;margin-top:12px;">
			<font style="float:left;" size="+1">
				Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?>
			</font>
			<a style="float:left;margin-top:-25px;margin-left:8px;margin-left:590px;cursor: pointer;" class="green_small" href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>">Back</a>
		</div>
		<div class="content_container" style="float:left;width:690px;margin-bottom:10px;text-align:center;margin-left:10px;margin-right:10px;height:90px;">
			<nav id="menu">
			  <ul class="nav">
				<li><a href="javascript:void(0)">PMB</a></li>
				<li><a href="javascript:void(0)">Drawing Register</a></li>
				<li><a class="inspections_tab" href="javascript:void(0)">Inspections</a></li>
			 </ul>
			</nav>

			<!-- we're done with the tabs, onto panes now -->

			<section id="tab_panes">
				<div class="tab_pane">
					<?php //include 'messages.php';?>
				</div>
				
				<!-- we'll copy/paste the other panes -->
				<div class="tab_pane">
					<input type="checkbox" id="builder_check" name="builder_check"> Builder needs to approve documents to be visible<?php //include 'drawing_register_v1.php';?>
				</div>

				<div class="tab_pane">
					<?php include 'o_and_m_workflow.php'; ?>
				</div>
			</section>	
		</div>	
		</div>
		
	</div>
</div>

<script>
// On to the interactiveness now :)
$(document).ready(function(){

	var tab = <?php echo $tab; ?>;
	if(tab == 3){
		$('.inspections_tab').trigger('click');
	}
});
$(function() {

	$('.nav a').on('click', function() {
	
		var $el = $(this);
		var index = $('.nav a').index(this);
		var active = $('.nav').find('a.active');
		
		/* if a tab other than the current active
		tab is clicked */
		
		if ($('nav a').index(active) !== index) {
			
			// Remove/add active class on tabs
			active.removeClass('active');
			$el.addClass('active');
			
			
			// Remove/add active class on panes
			$('.tab_pane.active')
				.hide()
				.removeClass('active');
			$('.tab_pane:eq('+index+')')
				.fadeIn()
				.addClass('active');
			
			// we can also add some quick fading effects
			
			// now that's awesome! you got
			// fancy stylish css3 tabs for your
			// next project ;)
			
		}
	});

}());
</script>
