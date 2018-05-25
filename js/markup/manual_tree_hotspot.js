jQuery.fn.telefilmsHotspot = function(options) {
	var action_status = 0;
	var expanded_array = new Array();
	if(options === undefined || options === null) options = {};
	var default_options = { 
		open_char : '<img src="images/minus-icon.png">',
		close_char : '<img src="images/plus-icon.png">',
		default_expanded_paths_string : '',
		only_one : false,
		animation : 'fast'
	};
	var o = {};
	jQuery.extend(o, default_options, options);
	//////////////////////////////////////////OPEN A CHILD///////////////////////////////////////////////////////////////
	jQuery.fn.open = function(animate,sam) { 
		if(action_status > 0)	return false;
		<!-- ----------------------------------------IF ONE TIME AJAX HAS RUN ----------------------------------------->
		jQuery(this).parent().find('ul').each(function(){  
			jQuery(this).parent().children('ul').show(animate);
			jQuery(this).parent().children('span').removeClass('close'); 
			jQuery(this).parent().children('span').addClass('open');
			jQuery(this).parent().children('span.jtree-arrowHotspot').html(o.open_char);	
			return false;
		});
		<!-- ----------------------------------------ON ONREADY  -----------------------------------------> 
		var a1=0;
		if(sam=='ready'){ var a1=0;}else{
			var correct = jQuery(this).parent().attr("id");
			var arr=correct.split('_');
			var a1=arr['1'];
		}
		<!-- ----------------------------------------AJAX ----------------------------------------->
		if(deadLockHotSpot){//alert('in Daeadlock conditon');	
		}else{
			//alert(newlocations);
			$("#contentSplitter").show();
//			var newlocationsr = $('#'+locationsContained).val();
			var url="manual_ajax_chapter_tree.php?hotspotparentID="+Math.random();
			deadLockHotSpot = true;
			showProgress();
			jQuery.ajax({
				type: "POST",
				url: url,
				data: "cetid="+a1,
				cache:false,
				success: function(data){ 
					hideProgress();
					if(a1==0){
						//Check here data is present or not
						//jQuery("#ajaxManualTree"+correct).html(data);
						jQuery("#ajaxHotspotTree").html(data);
						deadLockHotSpot = false;	
					}else{
						if(expanded_array[correct]!=1){
							jQuery("#"+correct).append(data);
						}
						jQuery('#'+correct).children('ul').show(animate); 
						jQuery('#'+correct).children('span').removeClass('close');
						jQuery('#'+correct).children('span').addClass('open');
						if(sam!='ready'){
							jQuery('#'+correct).children('span.jtree-arrowHotspot').html(o.open_char);	
						}else{
							jQuery('#'+correct).children('span.jtree-arrowHotspot').html(o.close_char); 	
						}
						expanded_array[correct] = 1;
						deadLockHotSpot = false;	
					}
					<!-------------------------SPAN (+) (-) ON CLICK OF CLASS jtree-arrowManual  ------------------------>
					if(jQuery(this).hasClass('jtree-arrowHotspot')) { 
						jQuery(this).parent().children('ul').show(animate);
						jQuery(this).removeClass('close');
						jQuery(this).addClass('open');
						if(sam!='ready'){
							(this).html(o.open_char);	
						}else{
							jQuery(this).html(o.close_char); 	
						}
					} 
					<!-----------------------SPAN (+) (-) ON CLICK OF CLASS jtree-buttonManual  ---------------------->				  
					if(jQuery(this).hasClass('jtree-buttonHotspot')) {
						jQuery(this).parent().children('span').removeClass('close'); 
						jQuery(this).parent().children('span').addClass('open');
						jQuery(this).parent().children('span.jtree-arrowHotspot').html(o.open_char);
					}	
					<!----------------------- Click function And Add data ---------------------------------->
					jQuery("span.jtree-arrowHotspot, close").click(function (){
						jQuery(this).click_event();
						return false;
					});
					jQuery("span.jtree-buttonHotspot,demo1,open").click(function (){
						var issiki =jQuery(this).attr('class');
						if(issiki=='jtree-buttonHotspot demo1'){
							jQuery(this).click_event();
							return false;													
						}else{
							jQuery(this).click_event();
							return false;
						}
					});		
					<!------------------------------------------- RIGHT CLICK MENU -------------------------------->
					
					
					<!-------------------------------- RIGHT CLICK MENU -------------------------->
				} // AJAX SUCSESS FUNCTION CLOSE
			}); // AJAX CLOSE
		}
	};

	//////////////////////////////////////////CLOSE A CHILD///////////////////////////////////////////////////////////////
	jQuery.fn.close = function(animate) { 
		action_status = 1;
		var iski=jQuery(this).attr('class');
		if(jQuery(this).hasClass('jtree-arrowHotspot') ) { 
			jQuery(this).parent().children('ul').hide(animate);
			jQuery(this).parent().children('span .jtree-buttonHotspot').removeClass('open');
			jQuery(this).removeClass('open');
			jQuery(this).addClass('close');
			jQuery(this).html(o.close_char);
			jQuery(this).parent().children('ul').hide();
		}
		if(jQuery(this).hasClass('jtree-buttonHotspot') && jQuery(this).hasClass('demo1')&& jQuery(this).hasClass('open') ) { 
			jQuery(this).parent().children('ul').hide(animate);
			jQuery(this).parent().children('span.jtree-arrowHotspot').removeClass('open');
			jQuery(this).parent().children('span.jtree-arrowHotspot').addClass('close');
			jQuery(this).parent().children('span.jtree-arrowHotspot').html(o.close_char);
			jQuery(this).removeClass('open');
		}else if(jQuery(this).hasClass('jtree-buttonHotspot')) {
			jQuery(this).parent().children('span.jtree-arrowHotspot').html(o.open_char);
			jQuery(this).parent().children('ul').show(animate);
		}
	};
	////////////////////CLICK FUNCTION ON <span class="jtree-arrowManual"></span/////////////////////////////////////////
	jQuery.fn.click_event = function(sam){
		if (action_status > 0)
			return false;
		window.setTimeout(function(){action_status=0;},500);
		if(sam=='ready'){ var ready='ready';}
		var button = jQuery(this); 
		if(button.hasClass('jtree-arrowHotspot')||button.hasClass('jtree-buttonHotspot')) {  
			if(button.hasClass('open')) { 
				button.close(o.animation);  
				if(o.only_one) button.closest('li').siblings().children('span.jtree-arrowHotspot').close(o.animation);
			}else if(button.hasClass('close')){ 
				button.open(o.animation,ready);  
				if(o.only_one) button.closest('li').siblings().children('span.jtree-arrowHotspot').close(o.animation);
			}else if(button.hasClass('demo1')){ 
				button.open(o.animation,ready);
				if(o.only_one) button.closest('li').siblings().children('span.jtree-arrowHotspot').close(o.animation);
			}
		}
//New Ajaxian Logic Part Start Here		
		if(button.hasClass('jtree-arrowHotspot')) {
			button[button.hasClass('open') ? 'close' : 'open'](o.animation);
			if(o.only_one) button.closest('li').siblings().children('span.jtree-arrowHotspot').close(o.animation);
		}
//New Ajaxian Logic Part End Here				
	};
		


	jQuery("span.jtree-buttonHotspot,demo1").click(function (){
		jQuery(this).click_event();
		return false;
	});
	
	$(document).ready(function (){
		jQuery("span.jtree-arrowHotspot, close").click_event('ready');
	});
}