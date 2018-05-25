<?php
session_start();
# Get Drawing Regsiter project id from header.
$drProjectId = base64_encode(0);
if(isset($pidArr) && !empty($pidArr)){
	$drProjectId = $pidArr[0];
}
# Let's check manager login.
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){ ?>
	<script language="javascript" type="text/javascript">
		window.location.href="<?=HOME_SCREEN?>";
	</script>
<?php } 
require_once'includes/functions.php';
include('includes/commanfunction.php');
$obj = new COMMAN_Class(); 

$getRes = mysql_query("SELECT * FROM drawing_register_module_one WHERE id = ".$_REQUEST['fileId']."");
$getData = mysql_fetch_assoc($getRes);
 
 $builderId = $_SESSION['ww_is_builder'];

if(!empty($getData)){
	if($getData['file_type']=='PDF'){
		$fileName =$getData['pdf_name'];

       	$pdfAbsolutePath = 'project_drawing_register_v1/'.$_SESSION['idp'].'/'.$fileName;
        $folder = 'draw_markup/uploads/';
        
        $dir = $folder.$builderId;
	    #Check dir/folder exists or not.	
		if(is_dir($dir) === false){
			mkdir($dir);
		    chmod($dir,0777);
		}

	    $imageFileName = $builderId.'_image_'.substr(microtime(), -6, -1).rand(0,99).'.jpg';

	    $im = new imagick($pdfAbsolutePath);
	    $noOfPagesInPDF = $im->getNumberImages(); 
	    if ($noOfPagesInPDF) { 
			for ($i = 0; $i < $noOfPagesInPDF; $i++) { 
				$url = $pdfAbsolutePath.'['.$i.']'; 
				$image = new Imagick($url);
				$image->setImageBackgroundColor('#ffffff');
				$image = $image->flattenImages();
				$image->setImageCompressionQuality(100);
				$image->setImageFormat("jpg"); 
				$image->writeImage($dir."/".($i+1).'-'.$imageFileName); 

				//$image->writeImage($dir."/".$imageFileName); 
	      	} 
	    }


	}elseif($getData['file_type']=='Image'){
        $imageFileName =$getData['img_name'];
	}
	else{
       $imageFileName =$getData['dwg_name'];
	}
    $finalImg = $dir.'/1-'.$imageFileName;


}

 
$dropzone_id = "150716122414";
$getResDrop = mysql_query("SELECT * FROM dropzone_permit_cordinates WHERE project_id = ".$_SESSION['idp']." AND dropzone_id=".$dropzone_id." ");
$dropzoneCordinatesDetails = mysql_fetch_assoc($getResDrop);
 

?>

<link rel="stylesheet" type="text/css" href="css/markup/css/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="css/markup/css/manual_style.css" />
<link rel="stylesheet" type="text/css" href="css/markup/css/jquery.contextMenu.css" />
<link rel="stylesheet" type="text/css" href="css/markup/css/splitter_style.css" />
<link rel="stylesheet" type="text/css" href="css/markup/css/jquery.alerts.css" />
<link rel="stylesheet" type="text/css" href="css/markup/css/jquery.timepicker.css" />
<script language="javascript" type="text/javascript" src="js/markup/jquery.hotspot.v1.7.1.js"></script>

<style type="text/css">
.container {
		border: 1px solid #000000;
		overflow: auto;
		width: 100%;
		max-height: 800px;
		position: relative;
	}
	.pull-left { float: left; }
	.pull-right { float: right; }
	.heading p {
		font-size: 14px;
	}
	.pdfContent { width: 100%; }
	.pdfContent img {
		width: 100%;
		height: auto;
		cursor:crosshair;
	}
	.pdfContent a { opacity: 0.8; }
</style>

<script language="javascript" type="text/javascript" src="js/markup/jqxcore.js"></script>
<script language="javascript" type="text/javascript" src="js/markup/jqxsplitter.js"></script>
<script language="javascript" type="text/javascript" src="js/markup/jquery-ui-10.js"></script>
<script language="javascript" type="text/javascript" src="js/markup/jquery.validate.min.js"></script>
<script language="javascript" type="text/javascript" src="js/markup/jquery.contextMenu.js"></script>
<script language="javascript" type="text/javascript" src="js/markup/manual_tree_hotspot.js"></script>
<script language="javascript" type="text/javascript" src="js/markup/canvas/html2canvas.js"></script>
<script language="javascript" type="text/javascript" src="js/markup/hotspot_onm.js"></script>
<script language="javascript" type="text/javascript" src="js/markup/hotspot/raphael.js"></script>
<script language="javascript" type="text/javascript" src="js/markup/hotspot/raphael.sketchpad.js"></script>
<script language="javascript" type="text/javascript" src="js/markup/jscolor/jscolor.js"></script>

<!-- <script language="javascript" type="text/javascript" src="js/markup/hotspot/jquery.panzoom.js"></script> 
<script language="javascript" type="text/javascript" src="js/markup/hotspot/jquery.mousewheel.js"></script>  -->
<!-- <script language="javascript" type="text/javascript" src="js/markup/hotspot/wheelzoom.js"></script> -->
<div class="markup-container">
	<div class="page-title">
		<h1><img src="images/b_head_doccument_register.png" width="18px" height="18px" alt="image" /> Document Register- Draw Markup 
		     <!--href="?sect=drawing_register&type=pmb&id=<?php //echo $drProjectId; ?>" <div id="zoomout">Zoom Out</div> <div id="zoomIn" >Zoom In</div> -->
			<button class="green_small btn-back pull-right" onclick="confirmSave('<?=$drProjectId?>');">Cancel</button>
		</h1>
	</div> <!-- /.page-title background-image:url(<?php echo $finalImg; ?>); -->
	<div class="markup-section">
		<div id="hotSpotCreateContainer">
			<button class="zoomout hotspot-button form_btn">Reset</button >
			<button class="zoomin hotspot-button form_btn">Zoom In </button>
			<button class="hotspot-button form_btn iconT" id="hotSpotCreate1"> <img src="images/rectangle.png" height="18"></button>
			<button class="hotspot-button form_btn iconT" id="hotSpotCreate2"> <img src="images/circle.png" height="18"></button>
			<!-- <button class="hotspot-button form_btn iconT" id="editor_line"> <img src="images/circle1.png" height="18" alt="Line" ></button> --> 

			<button class="hotspot-button form_btn iconT" id="editor_undo"> <img src="images/undo.png" height="18" alt="Undo" ></button> 
			<button class="hotspot-button form_btn iconT" id="editor_draw_erase"> <img src="images/pencil.png" height="18"></button>
			<button class="hotspot-button form_btn iconT jscolor" id="editor_fill"> <img src="images/paint.png" height="18"></button>
		</div> <!-- /.hotSpotCreateContainer -->
	   <div class="container">
		

		<div id="mainSplitter">
				<div id="jqx-widget-content-first">
					<div style="border: none;" id="feedExpander">
						<div class="jqx-hideborder"></div>
						<div class="jqx-hideborder jqx-hidescrollbars">
							<div id="locationsContainer">								
							</div>
						</div>
					</div>
				</div>
				<div id="jqx-widget-content-second">
					<?php if(isset($dropzoneCordinatesDetails) && !empty($dropzoneCordinatesDetails)){ ?>
					<div id="pdfContent" class="pdfContent" style="transform-origin: left top 0px; transform: scale(1);">
						<div id="editor"></div>
						
						<img id='hotspotimg' src="<?=$finalImg?>" />
						<?php foreach($dropzoneCordinatesDetails as $cRows){
							$draw = explode('_', $cRows['operation_tag']);
							$drawId = $draw[1];
							if($cRows['Ishape']=='drawing'){
								$jsondata = $cRows['j_data'];
						?>
							<input type="hidden" name="jdata2[]" id="draw_<?=$cRows['cordinates_id']?>" title="<?=$cRows['title']?>" class="jdata2" value="<?=$jsondata?>" color="#<?=$cRows['background_rgb_value']?>">
						<?php } else { ?>
							<div class="resizableTag <?=$cRows['Ishape']?> rotateon ui-resizable ui-draggable" data-internalid="<?=$cRows['cordinates_id']?>" id="draw_<?=$cRows['cordinates_id']?>" style="
								left:<?=$cRows['x_cordinates']?>px; 
								top: <?=$cRows['y_cordinates']?>px;
								width:<?=$cRows['tag_width']?>px;
								height:<?=$cRows['tag_height']?>px; 
								transform:rotate(<?=$cRows['degree']?>deg); 
								background:#<?=$cRows['background_rgb_value']?> !important;
								opacity: 0.8" 
								nooflink="<?=$cRows['cordinates_id']?>" 
								rel="<?=$cRows['title']?>" 
								lock="<?=$cRows['is_locked']?>">
								<div class="innerResizableTag" rel="" data-internalid="draggable_<?=$cRows['cordinates_id']?>">
									<strong><?=$cRows['title']?></strong>
								</div>
							</div> <!-- /.resizableTag -->
						<?php }//End of IF.
						}//End of foreach. ?>
					</div> <!-- /.pdfContent -->
					
					<?php } else {
						if(file_exists($finalImg)){
					?>
					<div id="pdfContent" class="pdfContent" style="transform-origin: left top 0px; transform: scale(1);">
						<div id="editor"></div>
						<img id='hotspotimg' src="<?=$finalImg?>" />
					</div> <!-- /.pdfContent -->
					<?php } } ?>
				</div>
			</div> <!-- /.mainSplitter -->
			</div> <!-- /.container -->
			 <br /><br />
			 <div class="button" style="margin-right: 10px;float: left;">
				<a class="green_small" href="?sect=drawing_register&type=pmb&id=<?php echo $drProjectId; ?>">Back</a>
			</div>
			<input type="button" class="form_btn green_small" onClick="saveHotspotImage()" value="Save Markup" />	
			<br /><br />		
		


	</div>
</div> <!-- /.markup-container -->



<script type="text/javascript">
	
function confirmSave(id){
    var msg = "Do you want to cancel? You will lose any unsaved changes.";
    var r = jConfirm(msg, null, function(r){
        if (r === true){
            window.location.href = '?sect=drawing_register&type=pmb&id='+id;           
        }else{
            return false;
        }
    });
}

</script>
