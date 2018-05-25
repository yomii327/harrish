jQuery.fn.tree = function(options) {
	if(options === undefined || options === null) options = {};
	var default_options = { 
		open_char : '<img src="images/minus-icon.png">',
		close_char : '<img src="images/plus-icon.png">',
		default_expanded_paths_string : '',
		only_one : false,
		animation : 'slow'
	};
	var o = {};
	jQuery.extend(o, default_options, options);
	//////////////////////////////////////////OPEN A CHILD///////////////////////////////////////////////////////////////
	jQuery.fn.open = function(animate,sam) { 
		<!-- ----------------------------------------IF ONE TIME AJAX HAS RUN ----------------------------------------->
		jQuery(this).parent().find('ul').each(function(){  
			jQuery(this).parent().children('ul').show(animate);
			jQuery(this).parent().children('span').removeClass('close'); 
			jQuery(this).parent().children('span').addClass('open');
			jQuery(this).parent().children('span .jtree-arrow').html(o.open_char);	
			return false();
		});
		<!-- ----------------------------------------ON ONREADY  -----------------------------------------> 
		if(sam=='ready'){ var a1=0;}else{
			var correct = jQuery(this).parent().attr("id");
			var arr=correct.split('_');
			var a1=arr['1'];
		}
		<!-- ----------------------------------------AJAX ----------------------------------------->
		if(deadLock){//alert('in Daeadlock conditon');	
		}else{
			var url="ajax.php";
			deadLock = true;
			showProgress();
			jQuery.ajax({
				type: "POST",
				url: url,
				data: "cetid="+a1,
				cache:false,
				success: function(data){ 
					hideProgress();
					if(a1==0){
						jQuery("#"+correct).html(data);
						deadLock = false;	
					}else{
						jQuery("#"+correct).append(data);
						jQuery('#'+correct).children('ul').show(animate); 
						jQuery('#'+correct).children('span').removeClass('close');
						jQuery('#'+correct).children('span').addClass('open');
						if(sam!='ready'){ 
							jQuery('#'+correct).children('span .jtree-arrow').html(o.open_char);	
						}else{
							jQuery('#'+correct).children('span .jtree-arrow').html(o.close_char); 	
						}
						deadLock = false;	
					}
					<!-------------------------SPAN (+) (-) ON CLICK OF CLASS jtree-arrow  ------------------------>
					if(jQuery(this).hasClass('jtree-arrow')) { 
						jQuery(this).parent().children('ul').show(animate);
						jQuery(this).removeClass('close');
						jQuery(this).addClass('open');
						if(sam!='ready'){
							(this).html(o.open_char);	
						}else{ 
							jQuery(this).html(o.close_char); 	
						}
					} 
					<!-----------------------SPAN (+) (-) ON CLICK OF CLASS jtree-button  ---------------------->				  
					if(jQuery(this).hasClass('jtree-button')) {
						jQuery(this).parent().children('span').removeClass('close'); 
						jQuery(this).parent().children('span').addClass('open');
						jQuery(this).parent().children('span .jtree-arrow').html(o.open_char);
					}	
					<!----------------------- Click function And Add data ---------------------------------->
					jQuery("span.jtree-arrow,close").click(function (){
						jQuery(this).click_event();
						return false();
					});
					jQuery("span.jtree-button,demo1,open").click(function (){
						var issiki =jQuery(this).attr('class');
						if(issiki=='jtree-button demo1'){
							jQuery(this).click_event();
							return false();													
						}else{
							jQuery(this).click_event();
							return false();
						}
					});		
					<!------------------------------------------- RIGHT CLICK MENU -------------------------------->
					$('span.demo1').contextMenu('myMenu2', {
						bindings: {
							'add': function(t) { 
								modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add.php?location_id='+t.id, loadingImage);
							},
							'edit': function(t) {
								modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit.php?location_id='+t.id, loadingImage);
							},
							'delete': function(t) {
								var r = jConfirm('Deleting the location(s) may cause all the defects linked to the locations to be orphaned as well as any un-synced issues from the iPads? Still do you want to delete location ?', null, function(r){
									if (r==true){
										if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
										showProgress();
										params = "location_id="+t.id+"&uniqueId="+Math.random();
										xmlhttp.open("POST", "location_tree_delete.php", true);
										xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
										xmlhttp.setRequestHeader("Content-length", params.length);
										xmlhttp.setRequestHeader("Connection", "close");
										xmlhttp.onreadystatechange=function(){
											if (xmlhttp.readyState==4 && xmlhttp.status==200){
												hideProgress();
												if(xmlhttp.responseText == 'Inspection Exist'){
													var r = jConfirm('Inspections added on this location. Do you want to delete location ?', null, function(r){
													if (r==true){
														if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
														showProgress();
														params = "location_id="+t.id+"&confirm=Y&uniqueId="+Math.random();
														xmlhttp.open("POST", "location_tree_delete.php", true);
														xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
														xmlhttp.setRequestHeader("Content-length", params.length);
														xmlhttp.setRequestHeader("Connection", "close");
														xmlhttp.onreadystatechange=function(){
															if (xmlhttp.readyState==4 && xmlhttp.status==200){
																hideProgress();
																if(xmlhttp.responseText == 'Location Deleted Successfully !'){
																	document.getElementById('li_'+t.id).style.display='none';
																	jAlert('Location Deleted Successfully !');
																}else{
																	jAlert(xmlhttp.responseText);
																}
															}
														}
														xmlhttp.send(params);
													}
												});
											}else{
												if (xmlhttp.readyState==4 && xmlhttp.status==200){
													if(xmlhttp.responseText == 'Location Deleted Successfully !'){
														document.getElementById('li_'+t.id).style.display='none';
														jAlert('Location Deleted Successfully !');
													}else{
														jAlert(xmlhttp.responseText);
													}
												}
											}
										}
									}
									xmlhttp.send(params);
								}
							});
						},
						'copy': function(t) {
							copyLocation(t.id);
						},
						'paste': function(t) {
							if(copyStatus === true){
								pasteLocation(copyId, t.id);
							}else{
								jAlert('Copy Location for Paste Here !');
							}
						},
						'setorder': function(t) {
							modalPopup(align, top1, 800, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_set_order.php?location_id='+t.id, loadingImage, callSortOrder);
						},
						'qrCode': function(t) {
							modalPopup(align, top1, 370, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_qrcode.php?location_id='+t.id+'&action=generate', loadingImage);
						},
					}
				});
				$('span.demo2').contextMenu('myMenu1', {
					bindings: {
						'add': function(t) {
							modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add.php?location_id='+t.id, loadingImage);
						},
						'paste': function(t) {
							if(copyStatus === true){
								pasteLocation(copyId, t.id);
							}else{
								jAlert('Copy Location for Paste Here !');
							}
						},
						'setorder': function(t) {
							modalPopup(align, top1, 800, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_set_order.php?location_id='+t.id, loadingImage, callSortOrder);
						},
					}
				});
					<!-------------------------------- RIGHT CLICK MENU -------------------------->
				} // AJAX SUCSESS FUNCTION CLOSE
			}); // AJAX CLOSE
		}
	};

	//////////////////////////////////////////CLOSE A CHILD///////////////////////////////////////////////////////////////
	jQuery.fn.close = function(animate) { 
		var iski=jQuery(this).attr('class');
		if(jQuery(this).hasClass('jtree-arrow') ) { 
			jQuery(this).parent().children('ul').hide(animate);
			jQuery(this).parent().children('span .jtree-button').removeClass('open');
			jQuery(this).removeClass('open');
			jQuery(this).addClass('close');
			jQuery(this).html(o.close_char);
			jQuery(this).parent().children('ul').hide();
		}
		if(jQuery(this).hasClass('jtree-button') && jQuery(this).hasClass('demo1')&& jQuery(this).hasClass('open') ) { 
			jQuery(this).parent().children('ul').hide(animate);
			jQuery(this).parent().children('span .jtree-arrow').removeClass('open');
			jQuery(this).parent().children('span .jtree-arrow').addClass('close');
			jQuery(this).parent().children('span .jtree-arrow').html(o.close_char);
			jQuery(this).removeClass('open');
		}else if(jQuery(this).hasClass('jtree-button')) {
			jQuery(this).parent().children('span .jtree-arrow').html(o.open_char);
			jQuery(this).parent().children('ul').show(animate);
		}
	};
	////////////////////CLICK FUNCTION ON <span class="jtree-arrow"></span/////////////////////////////////////////
	jQuery.fn.click_event = function(sam){ 
		if(sam=='ready'){ var ready='ready';}
		var button = jQuery(this); 
		if(button.hasClass('jtree-arrow')||button.hasClass('jtree-button')) {  
			if(button.hasClass('open')) { 
				button.close(o.animation);  
				if(o.only_one) button.closest('li').siblings().children('span.jtree-arrow').close(o.animation);
			}else if(button.hasClass('close')){ 
				button.open(o.animation,ready);  
				if(o.only_one) button.closest('li').siblings().children('span.jtree-arrow').close(o.animation);
			}else if(button.hasClass('demo1')){ 
				button.open(o.animation,ready);
				if(o.only_one) button.closest('li').siblings().children('span.jtree-arrow').close(o.animation);
			}
		}
	};
	///////////////////CLICK EVENT ON <span class="jtree-arrow"></span>/////////////////////////////////////////
	jQuery("span.jtree-arrow,close").click(function (){
		jQuery(this).click_event();  
		return false();
	});
	jQuery("span.jtree-button,demo1").click(function (){
		jQuery(this).click_event();
		return false();
	});
	$(document).ready(function (){
		jQuery("span.jtree-arrow,close").click_event('ready');
	});
}