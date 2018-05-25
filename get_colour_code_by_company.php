<?php
	session_start();
	// include_once("includes/commanfunction.php");
	// $obj = new COMMAN_Class();
	#echo "<pre>"; print_r($_SESSION); die;
	$headerBgColor = 'bfed35';
	$headerBgColor1 = '89b616';
	$headerBgColor2 = '6c9503';

	$navigationBgColor = 'fcd43a';
	$navigationBgColor1 = 'daaf0a';
	$navigation_font_size = 12;
	$navigationTextColor ='';

	$btnBgColor = '98d207';
	$btnBgColor1 = '5c8904';
	$btnColor = 'FFF';
	$btnFontSize = 14;

	$headerFontSize = '';
	#echo "<pre>";print_r($_SESSION);
	if(isset($_SESSION['colorData']) && !empty($_SESSION['colorData'])){
		#$companyId = $_SESSION['companyId'];
		#echo 'SELECT * FROM organisations_theme_settings WHERE is_deleted = 0 AND company_id IN ('.$companyId.') LIMIT 1'; die;
		#$colorData = $obj->getRecordByQuery('SELECT * FROM organisations_theme_settings WHERE is_deleted = 0 AND company_id IN ('.$companyId.') LIMIT 1');
		$colorData = $_SESSION['colorData'];
		#echo "============>>>><pre>"; print_r($colorData); die;
		if(isset($colorData[0]['header_bg_color']) && !empty($colorData[0]['header_bg_color'])){
			$header_bg_color = $colorData[0]['header_bg_color'];
			#echo "<pre>"; print_r($colorData[0]['header_bg_color']); die;
			$header_bg_color = explode(',', $header_bg_color);
			if(isset($header_bg_color[0]) && !empty($header_bg_color[0])){
				$headerBgColor = $header_bg_color[0];
			}
			if(isset($header_bg_color[1]) && !empty($header_bg_color[1])){
				$headerBgColor1 = $header_bg_color[1];
			}
			if(isset($header_bg_color[2]) && !empty($header_bg_color[2])){
				$headerBgColor2 = $header_bg_color[2];
			}
		}

		if(isset($colorData[0]['header_font_size']) && !empty($colorData[0]['header_font_size'])){
			$headerFontSize = $colorData[0]['header_font_size'];
		}

		if(isset($colorData[0]['navigation_bg_color']) && !empty($colorData[0]['navigation_bg_color'])){
			$navigation_bg_color = $colorData[0]['navigation_bg_color'];
			#echo "<pre>"; print_r($colorData[0]['navigation_bg_color']); die;
			$navigation_bg_color = explode(',', $navigation_bg_color);
			if(isset($navigation_bg_color[0]) && !empty($navigation_bg_color[0])){
				$navigationBgColor = $navigation_bg_color[0];
			}
			if(isset($navigation_bg_color[1]) && !empty($navigation_bg_color[1])){
				$navigationBgColor1 = $navigation_bg_color[1];
			}
		}

		if(isset($colorData[0]['navigation_font_size']) && !empty($colorData[0]['navigation_font_size'])){
			$navigation_font_size = $colorData[0]['navigation_font_size'];
		}

		if(isset($colorData[0]['navigation_text_color']) && !empty($colorData[0]['navigation_text_color'])){
			$navigationTextColor = $colorData[0]['navigation_text_color'];
		}

		if(isset($colorData[0]['button_bg_colour']) && !empty($colorData[0]['button_bg_colour'])){
			$bg_color = $colorData[0]['button_bg_colour'];
			#echo "<pre>"; print_r($colorData[0]['navigation_bg_color']); die;
			$bg_color = explode(',', $bg_color);
			if(isset($bg_color[0]) && !empty($bg_color[0])){
				$btnBgColor = $bg_color[0];
			}
			if(isset($bg_color[1]) && !empty($bg_color[1])){
				$btnBgColor1 = $bg_color[1];
			}
		}

		if(isset($colorData[0]['button_text_colour']) && !empty($colorData[0]['button_text_colour'])){
			$btnColor = $colorData[0]['button_text_colour'];
		}

		if(isset($colorData[0]['button_font_size']) && !empty($colorData[0]['button_font_size'])){
			$btnFontSize = $colorData[0]['button_font_size'];
		}
	}

	$navigationBgColor1 = 808080;
	$tableRowBgColor = "d1cfcf";
	$tableEvenRowBgColor = "dfdfdf";

	#echo "<pre>"; print_r($colorData[0]); die;

?>
<style type="text/css">
	<?php if(isset($_SESSION['colorData']) && !empty($_SESSION['colorData'])){ ?>
	.header{
	    height: 107px;
		background: #<?php echo $headerBgColor; ?>; /* Old browsers */
		background: -moz-linear-gradient(top,  #<?php echo $headerBgColor; ?> 0%, #<?php echo $headerBgColor1; ?> 59%, #<?php echo $headerBgColor2; ?> 100%); /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  #<?php echo $headerBgColor; ?> 0%,#<?php echo $headerBgColor1; ?> 59%,#<?php echo $headerBgColor2; ?> 100%); /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  #<?php echo $headerBgColor; ?> 0%,#<?php echo $headerBgColor1; ?> 59%,#<?php echo $headerBgColor2; ?> 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#<?php echo $headerBgColor; ?>', endColorstr='#<?php echo $headerBgColor2; ?>',GradientType=0 ); /* IE6-9 */
		border-top: 1px solid #e8f993;
	}

	.navigation{
	    height: 47px;
		background: #<?php echo $navigationBgColor; ?>; /* Old browsers */
		background: -moz-linear-gradient(top,  #<?php echo $navigationBgColor; ?> 0%, #<?php echo $navigationBgColor; ?> 100%); /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  #<?php echo $navigationBgColor; ?> 0%,#<?php echo $navigationBgColor; ?> 100%); /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  #<?php echo $navigationBgColor; ?> 0%,#<?php echo $navigationBgColor; ?> 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#<?php echo $navigationBgColor; ?>', endColorstr='#<?php echo $navigationBgColor; ?>',GradientType=0 ); /* IE6-9 */
		border-top: 1px solid #dcb10c;
	}

	#nav li a{
		color: #<?php echo $navigationTextColor . " !important"; ?>;
	}

	.ui-widget-header{
		background: #<?php echo $navigationBgColor1 . " !important"; ?>; /* Old browsers */
		background: -moz-linear-gradient(top,  #<?php echo $navigationBgColor1 . " !important"; ?> 0%, #<?php echo $navigationBgColor1 . " !important"; ?> 100%); /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  #<?php echo $navigationBgColor1 . " !important"; ?> 0%,#<?php echo $navigationBgColor1 . " !important"; ?> 100%); /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  #<?php echo $navigationBgColor1 . " !important"; ?> 0%,#<?php echo $navigationBgColor1 . " !important"; ?> 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	}

	#drawingDefault tr.odd, #drawingDefault tr.odd .sorting_1 {
		background: #<?php echo $tableRowBgColor . " !important"; ?>; /* Old browsers */
		background: -moz-linear-gradient(top,  #<?php echo $tableRowBgColor . " !important"; ?> 0%, #<?php echo $tableRowBgColor . " !important"; ?> 100%); /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  #<?php echo $tableRowBgColor . " !important"; ?> 0%,#<?php echo $tableRowBgColor . " !important"; ?> 100%); /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  #<?php echo $tableRowBgColor . " !important"; ?> 0%,#<?php echo $tableRowBgColor . " !important"; ?> 100%);
	}

	#projTable tr.odd td {
		background: #<?php echo $tableRowBgColor . " !important"; ?>; /* Old browsers */
		background: -moz-linear-gradient(top,  #<?php echo $tableRowBgColor . " !important"; ?> 0%, #<?php echo $tableRowBgColor . " !important"; ?> 100%); /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  #<?php echo $tableRowBgColor . " !important"; ?> 0%,#<?php echo $tableRowBgColor . " !important"; ?> 100%); /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  #<?php echo $tableRowBgColor . " !important"; ?> 0%,#<?php echo $tableRowBgColor . " !important"; ?> 100%);
	}

	#inboxData tr.odd td {
		background: #<?php echo $tableRowBgColor . " !important"; ?>; /* Old browsers */
		background: -moz-linear-gradient(top,  #<?php echo $tableRowBgColor . " !important"; ?> 0%, #<?php echo $tableRowBgColor . " !important"; ?> 100%); /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  #<?php echo $tableRowBgColor . " !important"; ?> 0%,#<?php echo $tableRowBgColor . " !important"; ?> 100%); /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  #<?php echo $tableRowBgColor . " !important"; ?> 0%,#<?php echo $tableRowBgColor . " !important"; ?> 100%);
	}

	/*===========*/

	#drawingDefault tr.even, #drawingDefault tr.even .sorting_1{
		background: #<?php echo $tableEvenRowBgColor . " !important"; ?>; /* Old browsers */
		background: -moz-linear-gradient(top,  #<?php echo $tableEvenRowBgColor . " !important"; ?> 0%, #<?php echo $tableEvenRowBgColor . " !important"; ?> 100%); /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  #<?php echo $tableEvenRowBgColor . " !important"; ?> 0%,#<?php echo $tableEvenRowBgColor . " !important"; ?> 100%); /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  #<?php echo $tableEvenRowBgColor . " !important"; ?> 0%,#<?php echo $tableEvenRowBgColor . " !important"; ?> 100%);
	}

	#projTable tr.even td {
		background: #<?php echo $tableEvenRowBgColor . " !important"; ?>; /* Old browsers */
		background: -moz-linear-gradient(top,  #<?php echo $tableEvenRowBgColor . " !important"; ?> 0%, #<?php echo $tableEvenRowBgColor . " !important"; ?> 100%); /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  #<?php echo $tableEvenRowBgColor . " !important"; ?> 0%,#<?php echo $tableEvenRowBgColor . " !important"; ?> 100%); /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  #<?php echo $tableEvenRowBgColor . " !important"; ?> 0%,#<?php echo $tableEvenRowBgColor . " !important"; ?> 100%);
	}

	#inboxData tr.even td {
		background: #<?php echo $tableEvenRowBgColor . " !important"; ?>; /* Old browsers */
		background: -moz-linear-gradient(top,  #<?php echo $tableEvenRowBgColor . " !important"; ?> 0%, #<?php echo $tableEvenRowBgColor . " !important"; ?> 100%); /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  #<?php echo $tableEvenRowBgColor . " !important"; ?> 0%,#<?php echo $tableEvenRowBgColor . " !important"; ?> 100%); /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  #<?php echo $tableEvenRowBgColor . " !important"; ?> 0%,#<?php echo $tableEvenRowBgColor . " !important"; ?> 100%);
	}

	#drawingDefault tbody tr td.dataTables_empty {
		/*display: block;
		height: 65px;*/
		padding-left: 0px;
		padding-right: 572px;
		width: 100%;
	}

	.ui-state-default{
		background: #d3d3d3 ;
		border: 1px solid #808080;
	}

	.ui-widget-header{
		border: 1px solid #808080;
	}

	#drawingDefault {
		background: #cccccc ;
		/*background-color: #eeeeee ;*/
		border: 1px solid #808080;
	}

	#toolbar{
		background: #cccccc ;
		border-bottom-color: #808080 ;
	}

	body{
		background: #cccccc ;
	}

	div#middle{
		background: #cccccc ;
	}

	.green_small {
	    margin-right: 6px;
	    padding: 3px 10px;
	    color: #<?php echo $btnColor; ?>;
	    border: 1px solid #435d01;
	    border-radius: 6px;
	    background: #<?php echo $btnBgColor; ?>;
	    background: -moz-linear-gradient(top, #<?php echo $btnBgColor; ?> 0%, #<?php echo $btnBgColor1; ?> 100%);
	    background: -webkit-linear-gradient(top, #<?php echo $btnBgColor; ?> 0%,#<?php echo $btnBgColor1; ?> 100%);
	    background: linear-gradient(to bottom, #<?php echo $btnBgColor; ?> 0%,#<?php echo $btnBgColor1; ?> 100%);
	    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#<?php echo $btnBgColor; ?>', endColorstr='#<?php echo $btnBgColor1; ?>',GradientType=0 );
	    /*-webkit-box-shadow: 0px 1px 0px 0px #000, inset 1px 1px 1px 0px rgba(179,247,2,1);
	    -moz-box-shadow: 0px 1px 0px 0px #000, inset 1px 1px 1px 0px rgba(179,247,2,1);
	    box-shadow: 0px 1px 0px 0px #000, inset 1px 1px 1px 0px rgba(179,247,2,1);*/
	    text-shadow: 1px 1px rgba(0, 0, 0, 0.66);
		/*font-family: 'Conv_MyriadPro-BoldIt';*/
		font-family:'Conv_Myriad_Pro-Semibold_It';
		font-size: <?php echo $btnFontSize; ?>px;
	    font-weight: bold;
		letter-spacing: 0.5px;
	}

	.left_btn2active, .left_btn3active, .left_btn4active, .left_btn5active, .left_btn6active, .left_btn7active, .left_btn8active, .left_btn9active, .left_btn10active, .left_btn11active, .left_btn13active, .left_btn14active, .left_btn15active, .left_btn16active, .left_btn21active {
		background: #<?php echo $btnBgColor; ?>;
	    border-radius: 10px;
	    border: 1px solid #5f5f5f;
	    vertical-align: middle;
	    display: table;
	    color: #<?php echo $btnColor; ?>;
	    font-family: 'Conv_MyriadPro-BoldIt';
	    text-shadow: 1px 1px 2px rgb(101, 101, 101);
	    font-size: 22px;
	    width: 232px;
	    height: 72px;
	    letter-spacing: normal;
		margin-bottom: 10px;
	}

	a.left_button:hover {
	    background: #<?php echo $btnBgColor; ?>;
	    color: #<?php echo $btnColor; ?>;
	    text-shadow: 1px 1px 2px rgb(101, 101, 101);
	    border-color: #5f5f5f;
	}

	.SearchTabs li:hover a, .SearchTabs li:hover a span, .Nav a, .Nav a span{
		background: #<?php echo $btnBgColor; ?> ;
	    border-top-right-radius: 5px;
	    border-top-left-radius: 5px;
	    color: #<?php echo $btnColor; ?>;
	}

	.archive {
		background: #<?php echo $btnBgColor; ?> url(images/archive_icon.png);
		background-repeat: no-repeat;
		background-position: 10px 10px;
	}

	.orange{
		border: 1px solid rgb(82, 64, 3);
		border-radius: 7px;
		background-image: -moz-linear-gradient( 90deg, rgb(248,121,8) 24%, rgb(230,158,23) 76%);
		background-image: -webkit-linear-gradient( 90deg, rgb(248,121,8) 24%, rgb(230,158,23) 76%);
		background-image: -ms-linear-gradient( 90deg, rgb(248,121,8) 24%, rgb(230,158,23) 76%);
		box-shadow: 0px 2px 0px 0px rgba(57, 185, 255, 0.004),inset 0px 1px 1px 0px rgba(255, 222, 0, 0.004);
	}

	.active_project{
		background: #3C2087 url(images/active_project_icon.png);
		background-repeat: no-repeat;
		background-position: 10px 10px;
		border-radius: 10px ;
	    cursor: pointer;
	    font-size: 20px ;
	    height: 70px;
	    padding: 10px 15px 10px 75px ;
	    color: #<?php echo $btnColor; ?>;
	    font-family: "Conv_Myriad_Pro-Semibold_It";
	    font-weight: bold;
	}

	.nav a.active,
	.nav a:active {
		background: #<?php echo $btnBgColor; ?>;
	}
	.attribute_active{
		background: #<?php echo $btnBgColor; ?>;
	}
	<?php }else{ ?>
	#top {
	    background-image: url("images/header_bg.gif");
	    background-repeat: repeat-x;
	    height: 155px;
	    width: 100%;
	}
	.header
	{
	    height: 107px;
	    background: #bfed35; /* Old browsers */
	    background: -moz-linear-gradient(top,  #bfed35 0%, #89b616 59%, #6c9503 100%); /* FF3.6-15 */
	    background: -webkit-linear-gradient(top,  #bfed35 0%,#89b616 59%,#6c9503 100%); /* Chrome10-25,Safari5.1-6 */
	    background: linear-gradient(to bottom,  #bfed35 0%,#89b616 59%,#6c9503 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#bfed35', endColorstr='#6c9503',GradientType=0 ); /* IE6-9 */
	    border-top: 1px solid #e8f993;
	}
	#top {
	    height: 155px;
	    width: 100%;
	    background-image: url(images/header_bg.gif);
	    background-repeat: repeat-x;
	}
	#middle {
	    background-color: #0378B5;
	    background-image: url("images/content_container.gif");
	}
	body {
	    background-color: #0378b5;
	}

	.navigation
	{
	    height: 47px;
	    background: #fcd43a; /* Old browsers */
	    background: -moz-linear-gradient(top,  #fcd43a 0%, #daaf0a 100%); /* FF3.6-15 */
	    background: -webkit-linear-gradient(top,  #fcd43a 0%,#daaf0a 100%); /* Chrome10-25,Safari5.1-6 */
	    background: linear-gradient(to bottom,  #fcd43a 0%,#daaf0a 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
	    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcd43a', endColorstr='#daaf0a',GradientType=0 ); /* IE6-9 */
	    border-top: 1px solid #dcb10c;
	}
	.green_small {
	    margin-right: 6px;
	    padding: 3px 10px;
	    color: #FFF;
	    border: 1px solid #435d01;
	    border-radius: 6px;
	    background: #98d207;
	    background: -moz-linear-gradient(top, #98d207 0%, #5c8904 100%);
	    background: -webkit-linear-gradient(top, #98d207 0%,#5c8904 100%);
	    background: linear-gradient(to bottom, #98d207 0%,#5c8904 100%);
	    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#98d207', endColorstr='#5c8904',GradientType=0 );
	    /*-webkit-box-shadow: 0px 1px 0px 0px #000, inset 1px 1px 1px 0px rgba(179,247,2,1);
	    -moz-box-shadow: 0px 1px 0px 0px #000, inset 1px 1px 1px 0px rgba(179,247,2,1);
	    box-shadow: 0px 1px 0px 0px #000, inset 1px 1px 1px 0px rgba(179,247,2,1);*/
	    text-shadow: 1px 1px rgba(0, 0, 0, 0.66);
	    /*font-family: 'Conv_MyriadPro-BoldIt';*/
	    font-family:'Conv_Myriad_Pro-Semibold_It';
	    font-size: 14px;
	    font-weight: bold;
	    letter-spacing: 0.5px;
	}
	input.green_small
	{
	    margin-top: -4px;
	}
	.attribute_active{
		background: #98d207;
	}
	<?php } ?>

</style>
