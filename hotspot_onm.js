/**
 * Hotspot custom Javascript
 * **********************************/

$(window).trigger('resize');
var deadLockHotSpot = false;
var deadLockHotSpotShow = false;
var align = 'bigFrame';
var topModal = 100;
var width = 500;
var padding = 10;
var backgroundColor = '#FFFFFF';
var borderColor = '#333333';
var borderWeight = 4;
var borderRadius = 5;
var fadeOutTime = 300;
var disableColor = '#666666';
var disableOpacity = 40;
var loadingImage = 'images/loadingAnimation.gif';
var spinnerVisible = false;
var noOfLink = 0;
var rtcheck = 1;
var zoom_scale = 1;

$(document).ready(function() {

    $('#projName').change(function() {
        $('#projForm').submit();
    });
    
    $('.chapter-pdf').click(function() {
        $('.chapter-pdf').css({
            "font-weight": "",
            "color": "#fff"
        });
        $(this).css({
            "font-weight": "bold",
            "color": "blue"
        });
    });

    $(".zoomout").css({
        'background': '#ccc'
    });
    $("#editor_draw_erase").css({
        'background': '#ccc'
    });

    // Hide and Show Button 
    $('.onm_hideshow').toggle(function() {
        $('#top').hide();
        $(this).text("Show");
    }, function() {
        $('#top').show();
        $(this).text("Hide");
    });


    /** === Right click menu hotspot ======*/
    $.contextMenu({
        selector: '.rotateon',
        build: function($trigger, e) {
            return {
                callback: function(key, options) {
                    var operationID = $($trigger).attr('id');
                    switch (key) {
                        case "edit":
                            editHotspot(operationID);
                            break;

                        case "delete":
                            deleteHotspot(operationID);
                            break;

                        case "start":
                            rotatationActivate(operationID);
                            break;
                            
                        /*case "bookings":
                            bookingsHotspot(operationID);
                            break;*/

                    }
                },
                items: {
                    "edit": {
                        name: "Edit Hotspot",
                        icon: "Edit_Chapter"
                    },
                    "delete": {
                        name: "Delete Hotspot",
                        icon: "Delete_Chapter"
                    },
                    "start": {
                        name: "Rotation On",
                        icon: "onrotate"
                    },
                    /*"bookings": {
                        name: "Bookings",
                        icon: "Booking_Chapter"
                    },*/
                }
            };
        }
        //selector.toggleclass( "rotateoff" );
    });

    $.contextMenu({
        selector: '.rotateoff',
        build: function($trigger, e) {
            return {
                callback: function(key, options) {
                    var operationID = $($trigger).attr('id');
                    switch (key) {
                        case "edit":
                            editHotspot(operationID);
                            break;

                        case "delete":
                            deleteHotspot(operationID);
                            break;

                        case "stop":
                            rotatationDeactivate(operationID);
                            break;
                            
                        /*case "bookings":
                            bookingsHotspot(operationID);
                            break;*/

                    }
                },
                items: {
                    "edit": {
                        name: "Edit Hotspot",
                        icon: "Edit_Chapter"
                    },
                    "delete": {
                        name: "Delete Hotspot",
                        icon: "Delete_Chapter"
                    },
                    "stop": {
                        name: "Rotation Off",
                        icon: "offrotate"
                    },
                    /*"bookings": {
                        name: "Bookings",
                        icon: "Booking_Chapter"
                    },*/
                }
            };
        }
    });

    $(document).on('click', '.icon-onrotate', function() {
        $('.resizableTag').removeClass("rotateon");
        $('.resizableTag').addClass("rotateoff");
    }).on('click', '.icon-offrotate', function() {
        $('.resizableTag').removeClass("rotateoff");
        $('.resizableTag').addClass("rotateon");
    });

    $.contextMenu({
        selector: '.drawing',
        build: function($trigger, e) {
            return {
                callback: function(key, options) {
                    var operationID = $($trigger).attr('id');
                    switch (key) {
                        case "edit":
                            editHotspot(operationID);
                            break;

                        case "delete":
                            deleteHotspot(operationID);
                            break;
                        
                        /*case "bookings":
                            bookingsHotspot(operationID);
                            break;*/

                    }
                },
                items: {
                    "edit": {
                        name: "Edit Hotspot",
                        icon: "Edit_Chapter"
                    },
                    "delete": {
                        name: "Delete Hotspot",
                        icon: "Delete_Chapter"
                    },
                    /*"bookings": {
                        name: "Bookings",
                        icon: "Booking_Chapter"
                    },*/
                }
            };
        }
    });

    $.contextMenu({
        selector: '.circle',
        build: function($trigger, e) {
            return {
                callback: function(key, options) {
                    var operationID = $($trigger).attr('id');
                    switch (key) {
                        case "edit":
                            editHotspot(operationID);
                            break;

                        case "delete":
                            deleteHotspot(operationID);
                            break;
                            
                        /*case "bookings":
                            bookingsHotspot(operationID);
                            break;*/
                    }
                },
                items: {
                    "edit": {
                        name: "Edit Hotspot",
                        icon: "Edit_Chapter"
                    },
                    "delete": {
                        name: "Delete Hotspot",
                        icon: "Delete_Chapter"
                    },
                    /*"bookings": {
                        name: "Bookings",
                        icon: "Booking_Chapter"
                    },*/
                }
            };
        }
    });



    /** === Zoomin hotspot ======*/
    $(document).on('click', '.zoomin', function() {
        if (zoom_scale < 2) {
            zoom_scale = zoom_scale + 0.1;
        } else {
            $(this).css({
                'background': '#ccc'
            });
        }

        scale = 'scale(' + zoom_scale + ')';
        $('#pdfContent').css('transform-origin', 'left top 0');
        $('#pdfContent').css('webkitTransform', scale); // Chrome, Opera, Safari
        $('#pdfContent').css('msTransform', scale); // IE 9
        $('#pdfContent').css('transform', scale); // General
        
        $(".zoomout").css({
            'background': '#76c423'
        });
    });

    /** === Zoomin hotspot ======*/
    $(document).on('click', '.zoomout', function() {
        if (zoom_scale > 1) {
            zoom_scale = zoom_scale - 0.1;
        } else {
            $(this).css({
                'background': '#ccc'
            });
        }

        scale = 'scale(' + zoom_scale + ')';
        $('#pdfContent').css('transform-origin', 'left top 0');
        $('#pdfContent').css('webkitTransform', scale); // Chrome, Opera, Safari
        $('#pdfContent').css('msTransform', scale); // IE 9
        $('#pdfContent').css('transform', scale); // General

        $(".zoomin").css({
            'background': '#76c423'
        });
    });

    /** === Generated new hotspot icon	======*/
    $('#hotSpotCreate1', window.parent.document).click(function() {

        noOfLink = Math.round((new Date()).getTime() / 1000);
        id = 'draw_' + noOfLink;
        str_image_div = "<div id='" + id + "' noOfLink='" + noOfLink + "' class='resizableTag rectangle rotateon'><div class='innerResizableTag'></div></div>";
        $('#pdfContent').append(str_image_div); //Active condition Section Here

        // dragg hotspot
        $(".resizableTag").draggable({
            containment: "#jqx-widget-content-second",
            cursor: "crosshair",
            drag: function(evt, ui) {

                var canvasHeight = $('#pdfContent').height();
                var canvasWidth = $('#pdfContent').width();
                var zscale = $("#pdfContent").css('transform');
                zscale = zscale.split('(')[1].split(')')[0].split(',')[0];
                console.log(zscale);
                ui.position.top = Math.round(ui.position.top / zscale);
                ui.position.left = Math.round(ui.position.left / zscale);

                //don't let draggable to get outside of the canvas
                if (ui.position.left < 0)
                    ui.position.left = 0;
                if (ui.position.left + $(this).width() > canvasWidth)
                    ui.position.left = canvasWidth - $(this).width();
                if (ui.position.top < 0)
                    ui.position.top = 0;
                if (ui.position.top + $(this).height() > canvasHeight)
                    ui.position.top = canvasHeight - $(this).height();
            }
        });
        // resize hotspot
        $("#" + id).resizable();

        console.log(zoom_scale);
        var newLeft = $('#jqx-widget-content-second').scrollLeft() + 15;
        var newTop = $('#jqx-widget-content-second').scrollTop() + 15;
        var scale1 = zoom_scale.toFixed(2);
        newLeft1 = parseInt(newLeft / scale1);
        newTop1 = parseInt(newTop / scale1);
        console.log("left " + newLeft1 + "top " + newTop1);
        $("#" + id).css({
            left: newLeft1 + 'px'
        });
        $("#" + id).css({
            top: newTop1 + 'px'
        });


    });


    $('#hotSpotCreate2', window.parent.document).click(function() {
        noOfLink = Math.round((new Date()).getTime() / 1000);
        id = 'draw_' + noOfLink;
        str_image_div = "<div id='" + id + "' noOfLink='" + noOfLink + "' class='resizableTag circle'><div class='innerResizableTag'></div></div>";
        $('#pdfContent').append(str_image_div); //Active condition Section Here

        // dragg hotspot
        $('.resizableTag').draggable({
            containment: "#jqx-widget-content-second",
            cursor: "crosshair",
            drag: function(evt, ui) {

                var canvasHeight = $('#pdfContent').height();
                var canvasWidth = $('#pdfContent').width();
                var zscale = $("#pdfContent").css('transform');
                zscale = zscale.split('(')[1].split(')')[0].split(',')[0];
                console.log(zscale);
                ui.position.top = Math.round(ui.position.top / zscale);
                ui.position.left = Math.round(ui.position.left / zscale);

                //don't let draggable to get outside of the canvas
                if (ui.position.left < 0)
                    ui.position.left = 0;
                if (ui.position.left + $(this).width() > canvasWidth)
                    ui.position.left = canvasWidth - $(this).width();
                if (ui.position.top < 0)
                    ui.position.top = 0;
                if (ui.position.top + $(this).height() > canvasHeight)
                    ui.position.top = canvasHeight - $(this).height();
            }
        });
        // resize hotspot
        $("#" + id).resizable();

        console.log(zoom_scale);
        var newLeft = $('#jqx-widget-content-second').scrollLeft() + 15;
        var newTop = $('#jqx-widget-content-second').scrollTop() + 15;
        var scale1 = zoom_scale.toFixed(2);
        newLeft1 = parseInt(newLeft / scale1);
        newTop1 = parseInt(newTop / scale1);
        console.log("left " + newLeft1 + "top " + newTop1);
        $("#" + id).css({
            left: newLeft1 + 'px'
        });
        $("#" + id).css({
            top: newTop1 + 'px'
        });

    });

    /**========== Hotsport Image OnScreen ==========**/
    $("#hotSpotCreateContainer").show();
    $("#contentSplitter").hide();
    $(".percent").text(0 + "%");

    //$("#pdfContent").html(data);
    $("#pdfContent").show();
    $("#pdfContent").css('transform', 'scale(1)');


    /** === Dragg to generated hotspot ======*/
    $('.resizableTag').draggable({
        containment: "#jqx-widget-content-second",
        cursor: "crosshair",
        create: function() {
            var Islocked = $(this).attr('lock');
            //console.log(Islocked);
            if (Islocked == 1) {
                $(this).draggable("disable");
            }
        },
        drag: function(evt, ui) {

            var canvasHeight = $('#pdfContent').height();
            var canvasWidth = $('#pdfContent').width();
            var zscale = $("#pdfContent").css('transform');
            zscale = zscale.split('(')[1].split(')')[0].split(',')[0];
            console.log(zscale);
            ui.position.top = Math.round(ui.position.top / zscale);
            ui.position.left = Math.round(ui.position.left / zscale);

            //don't let draggable to get outside of the canvas
            if (ui.position.left < 0)
                ui.position.left = 0;
            if (ui.position.left + $(this).width() > canvasWidth)
                ui.position.left = canvasWidth - $(this).width();
            if (ui.position.top < 0)
                ui.position.top = 0;
            if (ui.position.top + $(this).height() > canvasHeight)
                ui.position.top = canvasHeight - $(this).height();
        },
        stop: function(evt, ui) {
			
            operationTag = $(this).attr('id');
            
            var hotspotID = $(this).data('internalid');
            var tagPosition = $('#' + operationTag).position();
            var tagWidth = $('#' + operationTag).width();
            var tagHeight = $('#' + operationTag).height();
            var tagPosLeft = (ui.position.left);
            var tagPosTop = (ui.position.top);
            console.log("hotspotID=" + hotspotID + ",hight=" + tagHeight + ",width=" + tagWidth + ",left=" + tagPosLeft + ",top=" + tagPosTop + ",hotspot_move=true");

            $.ajax({
                url: self + "dropzonePermit/saveManualHotspot",
                type: "POST",
                data: {
                    unexcelledID: Math.random(),
                    hotspotID: hotspotID,
                    operationTag: operationTag,
                    tagPosition: tagPosition,
                    tagWidth: tagWidth,
                    tagHeight: tagHeight,
                    tagPosLeft: tagPosLeft,
                    tagPosTop: tagPosTop,
                    hotspot_move: true
                },
                success: function(res) {
                    var jsonResult = JSON.parse(res);
                    if (jsonResult.status) {
                        console.log(jsonResult.msg);
                    }
                }
            });

        }
    });


    /** === Resize to generated hotspot	======*/
    $(".resizableTag").resizable({
        create: function() {
            var Islocked = $(this).attr('lock');
            //console.log(Islocked);
            if (Islocked == 1) {
                $(this).resizable("disable");
            }
        },
        stop: function() {
            var zscale = $("#pdfContent").css('transform');
            zscale = zscale.split('(')[1].split(')')[0].split(',')[0];
            console.log(zscale);

            var operationTag = $(this).attr('id');
            var hotspotID = $(this).data('internalid');
            var tagPosition = $('#' + operationTag).position();
            var tagWidth = $('#' + operationTag).width();
            var tagHeight = $('#' + operationTag).height();
            var tagPosLeft = Math.round(tagPosition.left / zscale);
            var tagPosTop = Math.round(tagPosition.top / zscale);
            console.log("hight" + tagHeight + "width" + tagWidth + "left" + tagPosLeft + "top" + tagPosTop);

            $.ajax({
                url: self + "dropzonePermit/saveManualHotspot",
                type: "POST",
                data: {
                    unexcelledID: Math.random(),
                    hotspotID: hotspotID,
                    operationTag: operationTag,
                    tagPosition: tagPosition,
                    tagWidth: tagWidth,
                    tagHeight: tagHeight,
                    tagPosLeft: tagPosLeft,
                    tagPosTop: tagPosTop,
                    hotspot_move: true
                },
                success: function(res) {
                    var jsonResult = JSON.parse(res);
                    if (jsonResult.status) {
                        console.log(jsonResult.msg);
                    }
                }
            });
        }
    });


    /** === Draw to generate hotspot ======*/
    var sketchpad = Raphael.sketchpad("editor", {
        height: '100%',
        width: '100%',
        editing: false // true is default
    });

    $("#editor_draw_erase").toggle(function() {
        $(this).css({
            'background': '#76c423'
        });
        sketchpad.editing(true);
    }, function() {
        $(this).css({
            'background': '#ccc'
        });
        sketchpad.editing(false);
    });

    $("#editor_undo").click(function() {
        sketchpad.undo();
    });

    sketchpad.pen().width(2);
    sketchpad.pen().opacity(0.8);

    var myPaint = new jscolor.color(document.getElementById('editor_fill'), {});
    $("#editor_fill").click(function() {
        var color = $('#editor_fill').css('background-color');
        lcolor = color.replace("rgb", "rgba");
        fcolor = lcolor.split(')')[0] + ', ' + 0.5 + ')';
        //console.log(fcolor);
        $('.resizableTag').each(function() {
            var fid = $(this).attr('id');
            fid = fid.split('_')[0];
            if (fid == 'draw') {
                $(this).attr('fill', fcolor);
            }
        });
        sketchpad.editing(false);

    });

    var path_arr = [];
    var id_arr = [];
    var dataarr = [];
    var dataarr1 = [];
    $('.jdata2').each(function(event) {
        // path_arr.push($(this).val());
        // id_arr.push($(this).attr('id'));
        dataarr = {
            id: $(this).attr('id'),
            class: "resizableTag drawing",
            "title": $(this).attr('title'),
            "type": "path",
            "path": $(this).val(),
            "fill": $(this).attr('color'),
            "stroke": "#666",
            "stroke-opacity": 0.8,
            "stroke-width": 1,
            "stroke-linecap": "round",
            "stroke-linejoin": "round"
        };
        dataarr1.push(dataarr);

    });
    //console.log(dataarr);

    sketchpad.strokes(dataarr1);

    /**========== End of Hotsport Image OnScreen ==========**/
});

/************rgb to hash***************/
function componentToHex(c) {
    var hex = c.toString(16);
    return hex.length == 1 ? "0" + hex : hex;
}

function rgbToHex(r, g, b) {
    return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}

function hexToRgb(hex) {
    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

/** ========== Save Hotsport(Shape/title/bgcolor/locked) ====================== **/
function saveHotspot(operationTag) {
    var ht_title = $('#title').val();
    if (ht_title == '') {
        jAlert("Please enter a title of hotspot");
        return false;
    }

    var frmData = new FormData(document.getElementById("addhotspotFrm"));
    frmData.append("uniquehotId", Math.random());
    frmData.append("dropzone_id", dpzoneId);

    var tagshape = $('#' + operationTag).attr('class').split(' ')[1];
    console.log(tagshape);
    showProgress();
    if (tagshape == 'drawing') {
		//Save data drawing
        $.ajax({
            url: self + "dropzonePermit/saveManualHotspot",
            type: "POST",
            data: frmData,
            processData: false, // tell jQuery not to process the data
            contentType: false, // tell jQuery not to set contentType
            success: function(res) {
                var jsonResult = JSON.parse(res);
                if (jsonResult.status) {
                    hideProgress();
                    
                    //Fill background color.
                    var rgbValArr = jsonResult.rgbVal;
                    var rgbaVal = hexToRgb(rgbValArr);
                    //$('#' + operationTag).css('fill', '#' + rgbValArr + ' !important');
                    $('#' + operationTag).attr('fill', 'rgba('+ rgbaVal.r +', '+ rgbaVal.g +', '+ rgbaVal.b +', 0.5)');
                    $('#' + operationTag).attr('stroke-opacity', '0.8');
                    
                    var newhotspotID = jsonResult.insertedID;
                    // update hotspot id
                    if (newhotspotID > 0) {
                        $('#' + operationTag).attr('id', 'draw_' + newhotspotID);
                    }
                    // show success message
                    jAlert(jsonResult.msg);
                    /*$('#outPutResultPara').text(jsonResult.msg);
                    $('#outPutResult').show('fast');
                    setTimeout(function() {
                        $('#outPutResult').hide('slow');
                    }, 3000);*/
                    closePopup(fadeOutTime);
                    hideProgress();
                } else {
                    jAlert('Data updation failed, try again later');
                }

            }
        });
    } else {
		//Save data Ractangle/Cricle
        $.ajax({
            url: self + "dropzonePermit/saveManualHotspot",
            type: "POST",
            data: frmData,
            processData: false, // tell jQuery not to process the data
            contentType: false, // tell jQuery not to set contentType
            success: function(res) {
                console.log(operationTag);
                var jsonResult = JSON.parse(res);
                if (jsonResult.status) {
                    hideProgress();
                    $('#' + operationTag + " .innerResizableTag").attr('data-internalid', jsonResult.insertedID);
                    $('#' + operationTag + " span").remove();
                    $('#' + operationTag + " .innerResizableTag").html(jsonResult.chData);

                    var rgbValArr = jsonResult.rgbVal;
                    $('#' + operationTag).css('background-color', '#' + rgbValArr + ' !important');
                    $('#' + operationTag).css('opacity', '0.8');

                    var isLocked = jsonResult.taglock;
                    $('#' + operationTag).attr("lock", isLocked);
                    if (isLocked == 1) {
                        $('#' + operationTag).draggable("disable");
                        $('#' + operationTag).resizable("disable");
                    } else {
                        console.log("enabled hotspot");
                        $('#' + operationTag).draggable("enable");
                        $('#' + operationTag).resizable("enable");
                    }
                    var newhotspotID = jsonResult.insertedID;
                    // update hotspot id
                    if (newhotspotID > 0) {
                        $('#' + operationTag).attr('id', 'draw_' + newhotspotID);
                    }
                    // show success message
                    jAlert(jsonResult.msg);
                    /*$('#outPutResultPara').text(jsonResult.msg);
                    $('#outPutResult').show();
                    setTimeout(function() {
                        $('#outPutResult').hide();
                    }, 3000);*/
                    closePopup(fadeOutTime);
                    hideProgress();
                    //location.reload();
                } else {
                    jAlert('Data updation failed, try again later');
                }

            }
        });
    }
}

function closePopup(val) {
    $('.tmask').hide();
    $('.tbox').hide();
}


function saveHotspotImage() {
    showProgress();
    var element = $('#pdfContent');
    //var getCanvas = '';
    html2canvas(element, {
        onrendered: function(canvas) {
            //$("#previewImage").append(canvas);
            //$("#previewImage").show();
            //getCanvas = canvas;

            var imgageData = canvas.toDataURL("image/png");
            // Now browser starts downloading it instead of just showing it
            //var newData = imgageData.replace(/^data:image\/png/, "data:application/octet-stream");
            //$("#btn-Convert-Html2Image").attr("download", "your_pic_name.png").attr("href", newData);

            //AJXA request call to save Hotspot image data.
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    //console.log(this.responseText);
                    var data = JSON.parse(this.responseText);
                    if (data.status == true) {
                        location.href = self + 'dropzonePermit/dropzonePermitSection/' + dpzoneId;
                    } else {
                        hideProgress();
                        jAlert('Hotspot image is not saving, please try again!');
                    }
                }
            }
            xhttp.open('POST', self + 'dropzonePermit/saveImageData', false);
            xhttp.setRequestHeader('Content-Type', 'application/upload');
            xhttp.send(imgageData);
        }
    });
}

function save_img(data) {
    $.post([self + 'dropzonePermit/saveImageData'].join(), {
        data: data
    }).done(function(res) {
        console.log(res);
    });
}

function editHotspot(tagVal) {
	
	var tagshape = $('#' + tagVal).attr('class').split(' ')[1];
    if (tagshape == 'drawing') {
        var jsonData = $('#' + tagVal).attr("d");
        var fcolor = $('#' + tagVal).attr('fill');
        console.log('fcolor', fcolor);
        if (fcolor.indexOf('#') > -1) {
            fc = hexToRgb(fcolor);
            fcolor = 'rgba(' + fc.r + ',' + fc.g + ',' + fc.b + ',' + 0.5 + ')';
        }
        var postDetails = 'dpzoneId='+ dpzoneId +'&hotspotFrmID=' + Math.random() + '&operationTag=' + tagVal + '&tagshape=drawing&fcolor=' + fcolor + '&j_data=' + jsonData;
        TINY.box.show({
            url: self + 'dropzonePermit/manualHotspotUpdate',
            post: postDetails,
            width: 600,
            height: 210,
            opacity: 20,
            topsplit: 3,
            openjs: function() {
                loadJsColor()
            }
        });
    } else {
        var operationTag = tagVal;
        console.log('operationTagdddd :', operationTag);
        var fcolor = $('#' + tagVal).css('background-color');
        console.log('fcolor', fcolor);

        var zscale = $("#pdfContent").css('transform');
        zscale = zscale.split('(')[1].split(')')[0].split(',')[0];
        console.log(zscale);
        var tagPosition = $('#' + operationTag).position();
        var tagPosLeft = Math.round(tagPosition.left / zscale);
        var tagPosTop = Math.round(tagPosition.top / zscale);
        var tagWidth = $('#' + operationTag).width();
        var tagHeight = $('#' + operationTag).height();
        var degree = this.getRotationDegrees(operationTag);
        console.log("hight" + tagHeight + "width" + tagWidth + "left" + tagPosLeft + "top" + tagPosTop + "degree" + degree);

        // var postDetails = 'operationTag=' + operationTag + '&tagWidth=' + tagWidth + '&tagHeight=' + tagHeight + '&tagPosLeft=' + tagPosLeft + '&tagPosTop=' + tagPosTop + '&tagshape=' + tagshape + '&degree=' + degree + '&fcolor=' + fcolor;

        modalPopup(align, topModal, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'manualHotspotUpdate.php?hotspotFrmID='+Math.random()+'&operationTag='+operationTag, loadingImage);
        /*TINY.box.show({
            url: 'manualHotspotUpdate.php',
            post: postDetails,
            width: 600,
            height: 210,
            opacity: 20,
            topsplit: 3,
            openjs: function() {
                loadJsColor()
            }
        });*/

    }
}

function loadJsColor() {
    // initialize the new jscolor instance
    var ht_color = new jscolor.color(document.getElementById('ht_color'), {});
}

function deleteHotspot(tagVal) {
    console.log(tagVal);
    var r = jConfirm('Do you want to delete this hotspot ?', null, function(r) {
        if (r == true) {
            showProgress();
            $.post(self + 'dropzonePermit/deleteHotspotSection', {
                tagVal: tagVal,
                deleteID: Math.random()
            }).done(function(data) {
                hideProgress();
                var jsonResult = JSON.parse(data);
                if (jsonResult.status) {
                    $('#' + jsonResult.tagID).hide('slow');
                    $('#' + jsonResult.tagID).remove();
                } else {
					$('#' + jsonResult.tagID).hide('slow');
                    $('#' + jsonResult.tagID).remove();
                }
                jAlert(jsonResult.msg);
            });
        }
    });
}

/*function bookingsHotspot(tagVal){
	//split tag values.
	var tagArr = tagVal.split("_");
	var tagValues = tagArr[1];
	
	var formData = {};
	formData['dropzoneId'] = dpzoneId;
	formData['cordinatesId'] = tagValues;
	postData = $.param(formData); //serialize it
	TINY.box.show({
		url:self + 'dropzonePermit/dropzoneBooking',
		post:postData,
		width:900,
		height:550,
		opacity:20,
		topsplit:3,
		animate:true,
		openjs:function(){
			loadBookingsDataGrid(dpzoneId, tagValues)
		}
	});
}*/

/*
function deleteHotspotLink(chlinkId) {
    var r = jConfirm('Do you want to remove this link ?', null, function(r) {
        if (r == true) {
            var hotspotID = $("#hotspotID").val();
            showProgress();
            $.post('manual_hotshot_action.php', {
                hotspotChlinkId: chlinkId,
                hotspotID: hotspotID,
                deleteChapterLink: Math.random()
            }).done(function(data) {
                hideProgress();
                var jsonResult = JSON.parse(data);
                if (jsonResult.status) {
                    console.log(jsonResult.linkId);
                    $('#ch_' + jsonResult.linkId).hide('slow');
                    $('#ch_' + jsonResult.linkId).remove();
                } else {
                    jAlert(jsonResult.msg);
                }
            });
        }
    });
}*/


function getRotationDegrees(obj) {

    var el = document.getElementById(obj);
    var st = window.getComputedStyle(el, null);
    var tr = st.getPropertyValue("-webkit-transform") ||
        st.getPropertyValue("-moz-transform") ||
        st.getPropertyValue("-ms-transform") ||
        st.getPropertyValue("-o-transform") ||
        st.getPropertyValue("transform") ||
        "FAIL";

    // With rotate(30deg)...
    // matrix(0.866025, 0.5, -0.5, 0.866025, 0px, 0px)
    console.log('Matrix: ' + tr);
    if (tr == 'none') {
        angle = 0;
    } else {
        var values = tr.split('(')[1].split(')')[0].split(',');
        var a = values[0];
        var b = values[1];
        var c = values[2];
        var d = values[3];
        var scale = Math.sqrt(a * a + b * b);
        //console.log('Scale: ' + scale);

        // arc sin, convert from radians to degrees, round
        var sin = b / scale;
        // next line works for 30deg but not 130deg (returns 50);
        // var angle = Math.round(Math.asin(sin) * (180/Math.PI));
        var angle = Math.round(Math.atan2(b, a) * (180 / Math.PI));
    }

    return angle;
}


function rotatationActivate(tagVal) {

    rtcheck = 1;
    $('#' + tagVal).draggable("disable");
    $('#' + tagVal).resizable("disable");
    // $(".icon-Rotate_Chapter span").text().replace("Remove Rotate");	

    $('#' + tagVal).mousedown(function(e) {
        //e.preventDefault(); // prevents the dragging
        if (e.which == 3) {
            rtcheck = 0;
            return false;
        } else {
            $(document).bind('mousemove', function(e2) {
                rotateOnMouse(e2, $('#' + tagVal));
            });
        }
    });

    $('#' + tagVal).mouseup(function(e) {
        $(document).unbind('mousemove');

        var degree = getRotationDegrees(tagVal);
        var hotspotID = $(this).data('internalid');
        console.log("degree=" + degree + ",hotspotID=" + hotspotID);
        $.ajax({
            url: self + "dropzonePermit/saveHotshotDegree",
            type: "POST",
            data: {
                updegreeID: Math.random(),
                operationTag: tagVal,
                degree: degree,
                hotspotID: hotspotID
            },
            success: function(res) {
                var jsonResult = JSON.parse(res);
                if (jsonResult.status) {
                    console.log(jsonResult.msg);
                }
            }
        });

    });

}

function rotatationDeactivate(tagVal) {

    $('#' + tagVal).draggable("enable");
    $('#' + tagVal).resizable("enable");

}

function rotateOnMouse(e, pw) {
    //console.log(rtcheck);
    if (rtcheck == 1) {
        var offset = pw.offset();
        var center_x = (offset.left) + (pw.width() / 2);
        var center_y = (offset.top) + (pw.height() / 2);
        var mouse_x = e.pageX;
        var mouse_y = e.pageY;
        var radians = Math.atan2(mouse_x - center_x, mouse_y - center_y);
        var degree = (radians * (180 / Math.PI) * -1) + 90;

        // var boxCenter=[pw.offset().left+pw.width()/2, pw.offset().top+pw.height()/2];
        // var degree = Math.atan2(e.pageX- boxCenter[0], - (e.pageY- boxCenter[1]) )*(180/Math.PI);     

        //console.log(degree);
        $(pw).css('-moz-transform', 'rotate(' + degree + 'deg)');
        $(pw).css('-webkit-transform', 'rotate(' + degree + 'deg)');
        $(pw).css('-o-transform', 'rotate(' + degree + 'deg)');
        $(pw).css('-ms-transform', 'rotate(' + degree + 'deg)');
    } else {
        return false;
    }

}

function updateInfo(val){
	alert(val);
}

function showProgress() {
    $('#loader').show('fast');
}

function hideProgress() {
    $('#loader').hide('fast');
}
