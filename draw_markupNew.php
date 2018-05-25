<?php
session_start();
# Get Drawing Regsiter project id from header.
$drProjectId = base64_encode($_SESSION['idp']);
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
        $pdfTitle =$getData['title'];

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

<link rel="stylesheet" type="text/css" href="css/markup/css/jquery-ui.css?i=<?php echo time();?>" />
<link rel="stylesheet" type="text/css" href="css/markup/css/manual_style.css?i=<?php echo time();?>" />
<link rel="stylesheet" type="text/css" href="css/markup/css/jquery.contextMenu.css?i=<?php echo time();?>" />
<link rel="stylesheet" type="text/css" href="css/markup/css/splitter_style.css?i=<?php echo time();?>" />
<link rel="stylesheet" type="text/css" href="js/markup/crop/jquery.cropbox.css?i=<?php echo time();?>">

<style type="text/css">
    .container { border: 1px solid #000000;overflow: auto;width: 100%;max-height: 800px;position: relative;}
    .pull-left { float: left; }
    .pull-right { float: right; }
    .heading p { font-size: 14px;}
    .pdfContent { width: 100%;}
    .pdfContent img { width: 100%;height: auto;cursor:crosshair;}
    .pdfContent a { opacity: 0.8; }
    #editor { position: absolute !important;z-index: 100;}
    #editor_fill{ border:2px solid black;background-image: url(images/paint.png) !important; background-repeat: no-repeat; background-position: center; width: 33px; height: 33px;}
    #hotSpotCreateContainer { float: left; width: 100%; clear: both; }
</style>

<script language="javascript" type="text/javascript" src="js/bootstrap.min.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/modal.popup_gs.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/jqxcore.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/jqxsplitter.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/jquery-ui-10.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/jquery.ui.touch-punch.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/jquery.contextMenu.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/manual_tree_hotspot.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/canvas/html2canvas9.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/canvas/html2canvas9.min.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/hotspot_onm2.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/sketch.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/jscolor/jscolor-new.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/crop/hammer.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/crop/jquery.mousewheel.js?i=<?php echo time();?>"></script>
<script language="javascript" type="text/javascript" src="js/markup/crop/jquery.cropbox.js?i=<?php echo time();?>"></script>

<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">

<div class="markup-container">
    <div class="page-title">
        <h1><img src="images/b_head_doccument_register.png" width="18px" height="18px" alt="image" /> Document Register- Draw Markup 
            <button class="green_small btn-back pull-right" style="margin-left: 8px;" onclick="confirmSave('<?=$drProjectId?>','<?=$_GET['prev']?>');">Cancel</button>
            <button class="green_small btn-back pull-right" id="saveHotspotImage">Save Markup</button>
            <!-- <button class="green_small btn-back pull-right" onclick="emailPNG('<?=$drProjectId?>');">Mail</button>  -->
        </h1>
        <div id="hotSpotCreateContainer">
            <button class="hotspot-button form_btn iconT" id="bt_text"> <img src="images/text.png" height="18"></button>
            <button class="hotspot-button form_btn iconT jscolor {valueElement:null,value:'ff0000'}" id="editor_fill"></button>
            <button class="hotspot-button form_btn iconT" id="hotSpotCreate1"> <img src="images/rectangle.png" height="18"></button>
            <button class="hotspot-button form_btn iconT" id="hotSpotCreate2"> <img src="images/circle.png" height="18"></button>
            <button class="hotspot-button form_btn iconT" id="editor_draw_erase"> <img src="images/pencil.png" height="18"></button>
        </div> <!-- /.hotSpotCreateContainer onclick="drawPencil()"  -->
    </div> 
    <div class="markup-section">
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
                        <canvas id="editor"></canvas>
                        <img id="hotspotimg" src="<?php echo $finalImg; ?>"  cropwidth="1000" cropheight="600"/>

                        <?php foreach($dropzoneCordinatesDetails as $cRows){
                            $draw = explode('_', $cRows['operation_tag']);
                            $drawId = $draw[1];
                            if($cRows['Ishape']=='drawing'){
                                $jsondata = $cRows['j_data'];
                        ?>
                            <input type="hidden" name="jdata2[]" id="draw_<?=$cRows['cordinates_id']?>" title="<?=$cRows['title']?>" class="jdata2" value="<?=$jsondata?>" color="#<?=$cRows['background_rgb_value']?>">
                        <?php } else { ?>
                            <div class="resizableTag <?=$cRows['Ishape']?> rotateon ui-resizable ui-draggable" data-internalid="<?=$cRows['cordinates_id']?>" id="draw_<?=$cRows['cordinates_id']?>" style="background: #ff0000 !important;
                                left:<?=$cRows['x_cordinates']?>px; 
                                top: <?=$cRows['y_cordinates']?>px;
                                width:<?=$cRows['tag_width']?>px;
                                height:<?=$cRows['tag_height']?>px; 
                                transform:rotate(<?=$cRows['degree']?>deg); 
                                opacity: 0.8;" 
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
                        if(file_exists($finalImg)){//http://harrishmcdev.defectid.com/draw_markup/uploads/1/1-1_image_3787112.jpg
                    ?>
                    <div id="pdfContent" class="pdfContent" style="transform-origin: left top 0px; transform: scale(1);" crossOrigin="Anonymous">
                        <img id="hotspotimg" src="<?php echo $finalImg; ?>" cropwidth="1000" cropheight="600"/>
                        <div id="editor"></div>
                    </div> <!-- /.pdfContent -->
                    <?php } } ?>
                </div>
            </div> <!-- /.mainSplitter -->
            </div> <!-- /.container -->
             <br /><br />
             <div class="button" style="margin-right: 10px;float: left;">
                <!-- <a class="green_small" href="?sect=drawing_register&type=pmb&id=<?php echo $drProjectId; ?>">Back</a> -->
            </div>
            <input type="hidden" id="pdfTitle" value="<?=$pdfTitle?>">
            <input type="hidden" id="fileId" value="<?=$_REQUEST['fileId']?>">
            
             
            <br /><br />
    </div>
</div> <!-- /.markup-container -->

<script type="text/javascript">
$(document).ready(function(){

    $('#hotspotimg').cropbox({
        width: 1000,
        height: 600
    }).on('cropbox', function( event, results ) {
    });          

    $("#bt_text").click(function() {        
        showProgress();
		html2canvas(document.querySelector('#pdfContent'), {useCORS: true}).then(function (canvas) {
		    try {
		          var imgageData = canvas.toDataURL("image/png");
		          //alert(imgageData);
                    $("#pdfContent").html('<img id="hotspotimg" src="'+imgageData+'"/><div id="editor"></div>');
                    // Now browser starts downloading it instead of just showing it                
                    //AJXA request call to save Hotspot image data.

                    var ua = navigator.userAgent,
                    event = (ua.match(/iPad/i)) ? "touchstart" : "click";
                    if(event == 'click'){
                        $(document).on(event, '#hotspotimg', function(e) {
                            var offset = $(this).offset();
                            var X = (e.pageX - offset.left);
                            var Y = (e.pageY - offset.top);
                               
                            var operationTag ="";
                            var tagWidth = tagHeight = tagPosLeft = tagshape =tagPosTop = degree = fcolor="";
                            
                            modalPopup(align, topModal, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'markup_text.php?X='+X+'&Y='+Y+'&hotspotFrmID='+Math.random()+'&operationTag='+operationTag+'&tagWidth='+tagWidth+'&tagHeight='+tagHeight+'&tagPosLeft='+tagPosLeft+'&tagPosTop='+tagPosTop+'&tagshape='+tagshape+'&degree='+degree+'&fcolor='+fcolor, loadingImage);           
                        });
                    }else{
                        var elem = document.getElementById("hotspotimg");
                        elem.addEventListener(event, handleStart, false);

                        function handleStart(e) {
                            e.preventDefault();
                            var touch = e.targetTouches[0];
                            X = touch.pageX;
                            Y = touch.pageY;
                            //alert('X: ' + X + ', Y: ' + Y);
                            var operationTag ="";
                            var tagWidth = tagHeight = tagPosLeft = tagshape =tagPosTop = degree = fcolor="";
                            
                            modalPopup(align, topModal, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'markup_text.php?X='+X+'&Y='+Y+'&hotspotFrmID='+Math.random()+'&operationTag='+operationTag+'&tagWidth='+tagWidth+'&tagHeight='+tagHeight+'&tagPosLeft='+tagPosLeft+'&tagPosTop='+tagPosTop+'&tagshape='+tagshape+'&degree='+degree+'&fcolor='+fcolor, loadingImage);
                        }
                    }                                                       
                hideProgress();                   
            } catch (err) {
		      console.log(err)
		      alert(err)
		    }
        }).catch(function onRejected(error) {
        	alert(error);
		});        
    });


    $("#editor_draw_erase").click(function() {
        showProgress();
        html2canvas(document.getElementById('pdfContent'), {useCORS: true}).then(function (canvas) {
		    try {
		        var imgageData = canvas.toDataURL("image/png");
                //console.log(imgageData);
                $("#pdfContent").html('<canvas id="editor" style="cursor: crosshair;" height="600px" width="1000px"></canvas><img id="hotspotimg" src="'+imgageData+'" cropwidth="1000" cropheight="600"/>');
                    $('#editor').sketch({defaultColor: "#ff0000"});
                    hideProgress();
            } catch (err) {
		      console.log(err)
		      alert(err)
		    }
        }).catch(function onRejected(error) {
        	alert(error);
		});       
    });

    $("#saveHotspotImage").click(function() {

        showProgress();
        var fileId = $('#fileId').val();
        var pdfTitle = $('#pdfTitle').val();

        html2canvas(document.getElementById('pdfContent'), {useCORS: true}).then(function (canvas) {
            try {
                var imgageData = canvas.toDataURL("image/png");// Now browser starts downloading it instead of just showing items                
                //AJXA request call to save Hotspot image data.
                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        //console.log(this.responseText);
                        var data = JSON.parse(this.responseText);
                        if (data.status == true) {                        
                            if('<?=$_GET['prev']?>' == 1){
                                saveAs(canvas.toDataURL(), data.downloadName+'.png');                                
                                window.location.href = '?sect=drawing_register&type=pmb&id=<?=$drProjectId?>';
                            }else{
                                //var markupData = fileId+','+pdfTitle+','+data.image_name+',1';

                                var selectedFileHolderIds = data.image_name+'###'+pdfTitle+'###1';

                                $.post("copy_file_toAttach_folder_v1.php", {fileIds:selectedFileHolderIds, uniqueId:Math.random()}).done(function(data) {
                                    var jsonResult = JSON.parse(data);  
                                    if(jsonResult.status){
                                        console.log(jsonResult.dataArr);
                                        <?php if(isset($_GET['page']) && $_GET['page']=='message_details'){ ?>
                                        window.location.href = "?sect=message_details&id=<?php echo $_GET['id'];?>&type=<?php echo $_GET['type'];?>&attached=Y";
                                        
                                        <?php }else if(isset($_GET['page']) && $_GET['page']=='forward'){ ?>
                                        window.location.href = "?sect=forward&msgid=<?php echo isset($_GET['msgid'])?$_GET['msgid']:0;?>&attached=Y";
                                        
                                        <?php }else{?>
                                        window.location.href = "?sect=compose&msgid=<?php echo isset($_GET['msgid'])?$_GET['msgid']:0;?>&folderType=<?php echo isset($_GET['folderType'])?$_GET['folderType']:'';?>&attached=Y";
                                        <?php }?>
                                    }
                                });

                                
                            }
                            hideProgress();                          
                        } else {
                            hideProgress();
                            jAlert('MarkUp image is not saved, please try again!');
                        }
                    }
                }
                var svg_name = '';
                xhttp.open('POST', 'imageSaveMail.php?fileId='+fileId+'&svg_name='+svg_name, false);
                xhttp.setRequestHeader('Content-Type', 'application/upload');
                xhttp.send(imgageData);
            } catch (err) {
              console.log(err)
              alert(err)
            }
        }).catch(function onRejected(error) {
            alert(error);
        });        
    });

});

function confirmSave(id,T){
    var msg = "Do you want to cancel? You will lose any unsaved changes.";
    var r = jConfirm(msg, null, function(r){
        if (r === true){
            if(T == 1){
                window.location.href = '?sect=drawing_register&type=pmb&id='+id;
            }else{
                window.location.href = '?sect=drawing_register_select&page=compose&msgid=0&folderType=';
            }
        }else{
            return false;
        }
    });
}

</script>
