<meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<style type="text/css">[ng-cloak]#splash{display:block!important}[ng-cloak]{display:none}#splash{display:none;position:absolute;top:45%;left:50%;width:6em;height:6em;overflow:hidden;border-radius:100%;z-index:0}@-webkit-keyframes fade{from{opacity:1}to{opacity:.2}}@keyframes fade{from{opacity:1}to{opacity:.2}}@-webkit-keyframes rotate{from{-webkit-transform:rotate(0deg)}to{-webkit-transform:rotate(360deg)}}@keyframes rotate{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}#splash::after,#splash::before{content:'';position:absolute;top:0;left:0;width:100%;height:100%}#splash::before{background:linear-gradient(to right,green,#ff0);-webkit-animation:rotate 2.5s linear infinite;animation:rotate 2.5s linear infinite}#splash::after{background:linear-gradient(to bottom,red,#00f);-webkit-animation:fade 2s infinite alternate,rotate 2.5s linear reverse infinite;animation:fade 2s infinite alternate,rotate 2.5s linear reverse infinite}#splash-spinner{position:absolute;width:100%;height:100%;z-index:1;border-radius:100%;box-sizing:border-box;border-left:.5em solid transparent;border-right:.5em solid transparent;border-bottom:.5em solid rgba(255,255,255,.3);border-top:.5em solid rgba(255,255,255,.3);-webkit-animation:rotate .8s linear infinite;animation:rotate .8s linear infinite}
    body, html
    {
        height: 100%;
    }
    #editor
    {
        height: calc(100% - 44px)!important;
    }
    #editor > div:first-child, #editor .md-dialog-backdrop
    {
        height: 100%!important;
    }
    .footer, .footer div
    {
        height: 44px!important;
    }
    .md-slider-wrapper
    {
        height: auto!important;
    }
    #editor #middle
    {
     width:100% !important;
     height: 100%!important;
    }
    #editor .content_container
    {
        width: 100%!important;
        height: 100%;
    }
    #bottom
    {
        position: fixed;
        bottom: 0;
    }
    .footerTop
    {
        display: none;
    }
    #editor .upload-file-dialog.save-dialog.email
    {
        max-width: 450px;
		width: 450px;
    }
	.pmsMain
	{
		height: 100%;
	}

	/*******20-12-17******/
	#left-sidebar{
		display:none;
		width:0px;
	}
	#editor #viewport{
		width:100% !important;
		margin-left:0 !important;
	}
	ul#editor-controlls{ margin-top:30px; z-index:2;}
	ul#editor-controlls ul.child_item{ display:none;}
	ul#editor-controlls li:hover{ color:#fff82b;}
	ul#editor-controlls li{
    cursor: pointer;
		float: left;
		list-style: none;
		padding-left: 10px;
		padding-right: 10px;
		text-transform: capitalize;
	}
	ul#editor-controlls li.item-main {
		background: #1db39a none repeat scroll 0 0;
		color: #fff;
		font-weight: bold;
		margin-left: 2px;
		padding: 10px 15px;
	}
	ul#editor-controlls li ul li {
		background: #1db39a none repeat scroll 0 0;
		color: #fff;
		font-weight: bold;
		margin-left: 2px;
		padding: 10px 15px;
	}
	.text-decoration-buttons .toolbar-btn{
		display: inline-block;
		padding: 5px;
		text-align: center;
    }
	/*******20-12-17******/
</style>
<link rel="stylesheet" href="assets/css/maine7ae.css">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900" rel="stylesheet" type="text/css">
<script>
	$(function(){
		$("ul#editor-controlls li.item-main").on("click",function(){
			$("ul#editor-controlls li.item-main").not(this).next("ul.child_item").hide("slow");
			$("ul#editor-controlls li.item-main").not(this).hide("slow");
			$(this).next("ul.child_item").show("slow");
		})
		$(".show_other").on("click",function(){
			$(this).closest(".child_item").hide("slow");
			$("ul#editor-controlls li.item-main").show("slow");
		})
	})
</script>



<body id="editor" ng-app="ImageEditor" ng-strict-di>
   <div id="splash" ng-cloak>
      <div id="splash-spinner"></div>
   </div>
   <div ng-controller="MainController" ng-cloak style="position:relative;height: 100%;" aria-hidden="false" class="clearfix">
      <section ng-hide="!loading" id="spinner">
         <md-progress-circular class="md-spinner" md-diameter="100" md-mode="indeterminate"></md-progress-circular>
      </section>


      <section id="viewport">
         <div id="mainContainer"></div>
         <ul id="editor-controlls">
			<li ng-controller="CropController" data-activates="basics" class="item-main">
				<div ng-click="cropper.start($event)">Crop</div>
			</li>
				<ul class="child_item" ng-controller="CropController" >
					<li ng-click="cropper.crop()" class="show_other">Done</li>
					<li ng-click="cropper.stop()" class="show_other">Cancel</li>
				</ul>


			<li ng-controller="TextController" data-activates="text" class="item-main">
				<div ng-click="fonts.paginator.filter('sans-serif')"><p ng-click="changeFont('Roboto Condensed', $event)"><span ng-click="changeFont('Roboto Condensed', $event)">Text</span></p></div>
			</li>
				<ul class="child_item"  ng-controller="TextController" >

					<li>
					<div class="md-slider">
                        <div class="slider-label">Font Size</div>
                        <md-slider aria-label="text size" step="1" ng-change="text.setProperty('fontSize', fontSize)" ng-model="fontSize" min="1" max="100"></md-slider>
                     </div>

                     <div class="text-styles">
                        <div class="text-style-toolbar" ed-text-align-buttons>
                           <i data-align="left" class="mdi mdi-format-align-left"></i>
                           <i data-align="center" class="mdi mdi-format-align-center active"></i>
                           <i data-align="right" class="mdi mdi-format-align-right"></i>
                        </div>
                        <div class="text-style-toolbar text-decoration-buttons" ed-text-decoration-buttons>
                           <div data-decoration="underline" class="underline toolbar-btn">U</div>
                           <div data-decoration="italic" class="italic toolbar-btn">I</div>
                        </div>
                     </div>
                     <div class="colorpicker">
                        <div class="colorpicker-label"><span>Color</span></div>
                        <input ed-color-picker="text.setProperty('fill', color)" type="text"/>
                     </div>
					</li>
					<li ng-click="finishAddingTextToCanvas()" class="show_other">Done</li>
					<li ng-click="cancelAddingTextToCanvas()" class="show_other">Cancel</li>
				</ul>


			<li ng-controller="DrawingController" data-activates="drawing" class="item-main" >
				<p ng-repeat="brush in drawing.availableBrushes">
						<span ng-click="changeBrush(brush, $event)"><span ng-click="changeBrush(brush, $event)">Draw</span></span>
					</p>
			</li>
				<ul class="child_item" ng-controller="DrawingController">
					<li>
						<div class="md-slider">
							<input ng-model="drawing.params.brushColor" ed-color-picker="drawing.setProperty('color', color)" type="text"/>
							<div class="slider-label">Brush Thickness</div>
							<md-slider aria-label="brush thickness" step="1" ng-model="drawing.params.brushWidth" ng-change="drawing.setProperty('width', drawing.params.brushWidth)" min="1" max="100"></md-slider>
						 </div>
					</li>

					<li ng-click="finishAddingDrawingsToCanvas()" class="show_other" >Done</li>
					<li ng-click="cancelAddingDrawingsToCanvas()" class="show_other" >Cancel</li>
				</ul>

			<li ng-controller="SimpleShapesController" data-activates="simple-shapes" class="item-main" id="simple-shapes">
				<div>Shapes</div>
			</li>
				<ul class="show_other child_item" ng-controller="SimpleShapesController" >
					<li data-name="{{ shape.name }}" ng-repeat="shape in shapes.available" ng-click="shapes.addToCanvas(shape)">
							<div class="shape-image" id="{{ shape.name }}">
								<div class="css-shape"></div>
							</div>
							<div class="shape-name show_other">{{ shape.displayName || shape.name }}</div>
					</li>
				</ul>

         </ul>
         <section id="top-panel" class="md-whiteframe-z1" ng-controller="TopPanelController">
            <md-button ng-click="openEmailDialog($event)" class="green_small md-primary md-raised">Email</md-button>
            <md-button ng-click="openSaveDialog($event)" class="green_small md-primary md-raised">Save</md-button>
            <button class="green_small btn-back pull-right" style="margin-left: 8px;" onclick="confirmSave('<?=$drProjectId?>');">Cancel</button>
         </section>

		<canvas ng-show="started" id="canvas" class="md-whiteframe-z2"></canvas>

	 </section>
		<div id="bottom-bar" ng-controller="ZoomController">
				 <section class="zoom-container md-whiteframe-z1">
					<div class="current-zoom">{{ getCurrentZoom() }}%</div>
					<div class="zoom-slider">
					   <md-slider aria-label="zoom" step="1" ng-model="zoom" ng-change="doZoom()"  min="{{ minScale }}" max="{{ maxScale }}"></md-slider>
					</div>
					<div class="action-icons">
					   <i ng-click="canvas.fitToScreen()" class="mdi mdi-filter-center-focus fit-to-screen">
						  <md-tooltip md-delay="200">Fit to screen</md-tooltip>
					   </i>
					   <i ng-click="canvas.zoom(1)" class="mdi mdi-fullscreen original-size">
						  <md-tooltip md-delay="200">Original size</md-tooltip>
					   </i>
					</div>
					<div ed-ie-slider-fix></div>
				 </section>
			</div>

      <script src="assets/js/scripts.mine7ae.js"></script>
      <script type="application/ng-template" id="gradient-sheet-template">
         <md-bottom-sheet class="bottom-sheet gradients-sheet">
             <div class="items-list" ed-pretty-scrollbar>
                 <div ng-repeat="g in shapes.gradients" ng-style="{background: 'url(assets/images/gradients/'+($index+1)+'.png)'}" ng-click="shapes.fillWithGradient($index+1)" class="item md-whiteframe-z1"></div>
             </div>
         </md-bottom-sheet>
      </script>
      <script type="application/ng-template" id="image-sheet-template">
         <md-bottom-sheet class="bottom-sheet images-sheet">
             <div class="upload-new" ng-click="showDialog($event)">
                 <i class="mdi mdi-cloud-upload"></i>
                 <span class="icon-label">Upload</span>
             </div>
             <div class="items-list" ed-pretty-scrollbar>
                 <div ng-repeat="t in shapes.textures track by $index" ng-style="{background: 'url(assets/images/textures/'+$index+'.png)'}" ng-click="shapes.fillWithImage('assets/images/textures/'+$index+'.png')" class="item md-whiteframe-z1"></div>
             </div>
         </md-bottom-sheet>
      </script>
      <script type="application/ng-template" id="texture-upload-dialog-template">
         <md-dialog class="upload-file-dialog">
             <md-input-container>
                 <label>Image URL</label>
                 <input type="text" ng-model="imageBgUrl" ng-change="shapes.fillWithImage(imageBgUrl)" ng-model-options="{ debounce: 500 }">
             </md-input-container>

             <h2><span>OR</span></h2>

             <label class="pretty-upload">
                 <input type="file" ed-file-uploader="shapes.fillWithImage" ed-close-after/>
                 <i class="mdi mdi-cloud-upload"></i>
                 <span class="upload-button-label">Upload From Computer</span>
             </label>
         </md-dialog>
      </script>
      <script type="application/ng-template" id="email-save-image-dialog">
         <md-dialog class="upload-file-dialog save-dialog email">
             <md-input-container>
                 <label>To : </label>
                 <textarea id="toEmail" rows="2" cols="20" ng-model="toEmail"></textarea>
             </md-input-container>
             <md-input-container>
                 <label>Cc : </label>
                 <input type="text" ng-model="ccEmail">
             </md-input-container>
             <md-input-container>
                 <label>Bcc : </label>
                 <input type="text" ng-model="bccEmail">
             </md-input-container>
             <md-input-container>
                 <label>Subject : </label>
                 <input type="text" ng-model="subjectEmail">
             </md-input-container>
             <md-input-container>
                 <label>Attachment : <?=$_SESSION['draw_markup']?></label>
             </md-input-container>
             <md-input-container>
                 <label>Message : </label>
                 <textarea id="msgEmail" rows="3" cols="30" ng-model="msgEmail"></textarea>
             </md-input-container>

             <md-button ng-click="sendEmail($event)" class="green_small md-raised md-primary">Send</md-button>

         </md-dialog>
      </script>
      <script type="application/ng-template" id="save-image-dialog">
         <md-dialog class="upload-file-dialog save-dialog">
             <md-input-container>
                 <label>File Name</label>
                 <input type="text" ng-model="imageName">
             </md-input-container>

             <div ng-if="imageType === 'jpeg'">
                 <div class="slider-label">Quality {{ imageQuality }}</div>
                 <md-slider aria-label="Angle" md-discrete ng-model="imageQuality" step="1" min="1" max="10" ></md-slider>
             </div>

             <p ng-if="imageType === 'json'">This will save a file with current pixie editor state so you can load it into pixie later.</p>

             <md-button ng-click="saveImage($event)" class="green_small md-raised md-primary">Save</md-button>

             <div class="demo-alert" ng-if="isDemo">Image saving is disabled on demo site.</div>
         </md-dialog>
      </script>
      <script type="application/ng-template" id="main-image-upload-dialog-template">
         <md-dialog class="upload-file-dialog">
             <div ng-show="openImageMode === 'open'">
                 <md-input-container>
                     <label>Image URL</label>
                     <input type="text" ng-model="openImageUrl" ng-change="showImagePreview(openImageUrl)" ng-model-options="{ debounce: 500 }">
                 </md-input-container>

                 <h2><span>OR</span></h2>

                 <label class="pretty-upload">
                     <input type="file" ed-file-uploader="showImagePreview"/>
                     <i class="mdi mdi-cloud-upload"></i>
                     <span class="upload-button-label">Upload From Computer</span>
                 </label>

                 <h2><span>OR</span></h2>

                 <div class="buttons" ng-show="!canOpenImage">
                     <md-button ng-click="openImageMode = 'create'" class="md-primary">Create New</md-button>
                     <md-button ng-init="openSampleImage('<?=$finalImg?>')">Sample</md-button>
                 </div>

                 <div ng-show="canOpenImage">
                     <div class="img-preview"></div>

                     <div class="buttons">
                         <md-button ng-click="openImage()" class="green_small md-primary md-raised">Open</md-button>
                         <md-button ng-click="closeUploadDialog()" class="green_small md-raised">Close</md-button>
                     </div>
                 </div>
             </div>

             <div class="new-canvas" ng-show="openImageMode === 'create'">
                 <md-input-container>
                     <label>Width</label>
                     <input min="1" max="5000" type="number" ng-model="canvasWidth">
                 </md-input-container>

                 <md-input-container>
                     <label>Height</label>
                     <input min="1" max="5000" type="number" ng-model="canvasHeight">
                 </md-input-container>

                 <div class="buttons">
                     <md-button ng-click="openImageMode = 'open'" class="green_small md-raised">Cancel</md-button>
                     <md-button ng-click="createNewCanvas(canvasWidth, canvasHeight)" class="green_small md-primary md-raised">Create</md-button>
                 </div>
             </div>
         </md-dialog>
      </script>

   </div>
</body>
<script type="text/javascript">
   $('#top').hide();
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
