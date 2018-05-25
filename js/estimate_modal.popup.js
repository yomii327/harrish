function modalPopup_gs(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, url, loadingImage, callback, gs){
	gs = typeof gs !== 'undefined' ? gs : 0;
	
	callback = typeof callback !== 'undefined' ? callback : 0;
	var popupDiv = document.getElementById('outerModalPopupDiv'+gs);
	if (popupDiv == null){
		containerid = "innerModalPopupDiv"+gs;
			
		var popupDiv = document.createElement('div');
		var popupMessage = document.createElement('div');	
		var blockDiv = document.createElement('div');
		var imageClose = document.createElement('img');

		popupDiv.setAttribute('id', 'outerModalPopupDiv'+gs);
		popupDiv.setAttribute('class', 'outerModalPopupDiv'+gs);
		
		popupMessage.setAttribute('id', 'innerModalPopupDiv'+gs);
		popupMessage.setAttribute('class', 'innerModalPopupDiv'+gs);

		document.body.appendChild(popupDiv);
		popupDiv.appendChild(popupMessage);
		var closeDiv = document.createElement('div');

		blockDiv.setAttribute('id', 'blockModalPopupDiv'+gs);
		blockDiv.setAttribute('class', 'blockModalPopupDiv'+gs);
//		blockDiv.setAttribute('onClick', 'closePopup_gs(' + fadeOutTime + ', ' + gs + ')');
		
		closeDiv.setAttribute('id', 'closeModalPopupDiv'+gs);
		closeDiv.setAttribute('class', 'closeModalPopupDiv'+gs);
		closeDiv.setAttribute('onClick', 'closePopup_gs(' + fadeOutTime + ', ' + gs + ')');
	
		imageClose.setAttribute('id', 'closeImage'+gs);
		imageClose.setAttribute('class', 'closeImage'+gs);
		imageClose.setAttribute('onClick', 'closePopup_gs(' + fadeOutTime + ', ' + gs + ')');
		imageClose.setAttribute('src', 'images/close.png');
	
		popupDiv.appendChild(closeDiv);
		closeDiv.appendChild(imageClose);
		
		document.body.appendChild(blockDiv);
	}
	
	if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)){ //test for MSIE x.x;
	 var ieversion=new Number(RegExp.$1) // capture x.x portion and store as a number
	   if(ieversion>6) {
		   getScrollHeight_gs(top, gs);
		}
	}else{
		getScrollHeight_gs(top, gs);
	}
	
	document.getElementById('outerModalPopupDiv'+gs).style.display='block';
	document.getElementById('outerModalPopupDiv'+gs).style.width = width + 'px';
	document.getElementById('outerModalPopupDiv'+gs).style.padding = borderWeight + 'px';
	document.getElementById('outerModalPopupDiv'+gs).style.background = borderColor;
	document.getElementById('outerModalPopupDiv'+gs).style.borderRadius = borderRadius + 'px';
	document.getElementById('outerModalPopupDiv'+gs).style.MozBorderRadius = borderRadius + 'px';
	document.getElementById('outerModalPopupDiv'+gs).style.WebkitBorderRadius = borderRadius + 'px';
	document.getElementById('outerModalPopupDiv'+gs).style.borderWidth = 0 + 'px';
	document.getElementById('outerModalPopupDiv'+gs).style.position = 'absolute';
	document.getElementById('outerModalPopupDiv'+gs).style.zIndex = 100;
	
	document.getElementById('innerModalPopupDiv'+gs).style.padding = padding + 'px';
	document.getElementById('innerModalPopupDiv'+gs).style.background = backgroundColor;
	document.getElementById('innerModalPopupDiv'+gs).style.borderRadius = (borderRadius - 3) + 'px';
	document.getElementById('innerModalPopupDiv'+gs).style.MozBorderRadius = (borderRadius - 3) + 'px';
	document.getElementById('innerModalPopupDiv'+gs).style.WebkitBorderRadius = (borderRadius - 3) + 'px';
	
	document.getElementById('blockModalPopupDiv'+gs).style.width = 100 + '%';
	document.getElementById('blockModalPopupDiv'+gs).style.border = 0 + 'px';
	document.getElementById('blockModalPopupDiv'+gs).style.padding = 0 + 'px';
	document.getElementById('blockModalPopupDiv'+gs).style.margin = 0 + 'px';
	document.getElementById('blockModalPopupDiv'+gs).style.background = disableColor;
	document.getElementById('blockModalPopupDiv'+gs).style.opacity = (disableOpacity / 100);
	document.getElementById('blockModalPopupDiv'+gs).style.filter = 'alpha(Opacity=' + disableOpacity + ')';
	document.getElementById('blockModalPopupDiv'+gs).style.zIndex = 99;
	document.getElementById('blockModalPopupDiv'+gs).style.position = 'fixed';
	document.getElementById('blockModalPopupDiv'+gs).style.top = 0 + 'px';
	document.getElementById('blockModalPopupDiv'+gs).style.left = 0 + 'px';
	
	document.getElementById('closeModalPopupDiv'+gs).style.width = 30+'px';
	document.getElementById('closeModalPopupDiv'+gs).style.height = 30+'px';
	document.getElementById('closeModalPopupDiv'+gs).style.position = 'absolute';
	document.getElementById('closeModalPopupDiv'+gs).style.zIndex = 100;
	document.getElementById('closeModalPopupDiv'+gs).style.top = -13 + 'px';
	document.getElementById('closeModalPopupDiv'+gs).style.right = -14 + 'px';
	document.getElementById('closeModalPopupDiv'+gs).style.cursor = 'pointer';

	if(align=="center") {
		document.getElementById('outerModalPopupDiv'+gs).style.marginLeft = (-1 * (width / 2)) + 'px';
		document.getElementById('outerModalPopupDiv'+gs).style.left = 50 + '%';
	} else if(align=="left") {
		document.getElementById('outerModalPopupDiv'+gs).style.marginLeft = 0 + 'px';
		document.getElementById('outerModalPopupDiv'+gs).style.left = 10 + 'px';
	} else if(align=="right") {
		document.getElementById('outerModalPopupDiv'+gs).style.marginRight = 0 + 'px';
		document.getElementById('outerModalPopupDiv'+gs).style.right = 10 + 'px';
	} else {
		document.getElementById('outerModalPopupDiv'+gs).style.marginLeft = (-1 * (width / 2)) + 'px';
		document.getElementById('outerModalPopupDiv'+gs).style.left = 50 + '%';
	}
	
	blockPage_gs(gs);

	var page_request = false;
	if (window.XMLHttpRequest) {
		page_request = new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		try {
			page_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				page_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) { }
		}
	} else {
		return false;
	}


	page_request.onreadystatechange=function(){
		if((url.search(/.jpg/i)==-1) && (url.search(/.jpeg/i)==-1) && (url.search(/.gif/i)==-1) && (url.search(/.png/i)==-1) && (url.search(/.bmp/i)==-1)) {
			pageloader_gs(page_request, containerid, loadingImage, callback);
		} else {
			imageloader_gs(url, containerid, loadingImage);
		}
	}

	page_request.open('GET', url, true);
	page_request.send(null);
	
}

function pageloader_gs(page_request, containerid, loadingImage, callback){
	document.getElementById(containerid).innerHTML = '<div align="center"><img src="' + loadingImage + '" border="0" /></div>';
	if (page_request.readyState == 4 && (page_request.status==200 || window.location.href.indexOf("http")==-1)) {
		document.getElementById(containerid).innerHTML=page_request.responseText;
		if(callback !== 'undefined'){
			setTimeout(callback, 10);
		}
	}
}

function imageloader_gs(url, containerid, loadingImage) {
	
	document.getElementById(containerid).innerHTML = '<div align="center"><img src="' + loadingImage + '" border="0" /></div>';
	document.getElementById(containerid).innerHTML='<div align="center"><img src="' + url + '" border="0" /></div>';
	
}

function blockPage_gs(gs) {
	
	var blockdiv = document.getElementById('blockModalPopupDiv'+gs);
	var height = screen.height;
	
	blockdiv.style.height = height + 'px';
	blockdiv.style.display = 'block';
//$('#mainContainer').tinyscrollbar();
}

function getScrollHeight_gs(top, gs) {
   
   var h = window.pageYOffset || document.body.scrollTop || document.documentElement.scrollTop;
           
	if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
		
		var ieversion=new Number(RegExp.$1);
		
		if(ieversion>6) {
			document.getElementById('outerModalPopupDiv'+gs).style.top = h + top + 'px';
		} else {
			document.getElementById('outerModalPopupDiv'+gs).style.top = top + 'px';
		}
		
	} else {
		document.getElementById('outerModalPopupDiv'+gs).style.top = h + top + 'px';
	}
	
}

function closePopup_gs(fadeOutTime, gs) {
	fade_gs('outerModalPopupDiv'+gs, fadeOutTime);
	document.getElementById('blockModalPopupDiv'+gs).style.display='none';
	var rem = document.getElementById('closeModalPopupDiv'+gs);
	rem.style.display = "none";
	rem.parentNode.removeChild(rem);;
}

function fade_gs(id, fadeOutTime) {
	
	var el = document.getElementById(id);
	
	if(el == null) {
		return;
	}
	
	if(el.FadeState == null) {
		
		if(el.style.opacity == null || el.style.opacity == '' || el.style.opacity == '1') {
			el.FadeState = 2;
		} else {
			el.FadeState = -2;
		}
	
	}
	
	if(el.FadeState == 1 || el.FadeState == -1) {
		
		el.FadeState = el.FadeState == 1 ? -1 : 1;
		el.fadeTimeLeft = fadeOutTime - el.fadeTimeLeft;
		
	} else {
		
		el.FadeState = el.FadeState == 2 ? -1 : 1;
		el.fadeTimeLeft = fadeOutTime;
		setTimeout("animateFade_gs(" + new Date().getTime() + ",'" + id + "','" + fadeOutTime + "')", 33);
	
	}  
  
}

function animateFade_gs(lastTick, id, fadeOutTime) {
	  
	var currentTick = new Date().getTime();
	var totalTicks = currentTick - lastTick;
	
	var el = document.getElementById(id);
	
	if(el.fadeTimeLeft <= totalTicks) {
	
		el.style.opacity = el.FadeState == 1 ? '1' : '0';
		el.style.filter = 'alpha(opacity = ' + (el.FadeState == 1 ? '100' : '0') + ')';
		el.FadeState = el.FadeState == 1 ? 2 : -2;
		document.body.removeChild(el);
		return;
	
	}
	
	el.fadeTimeLeft -= totalTicks;
	var newOpVal = el.fadeTimeLeft / fadeOutTime;
	
	if(el.FadeState == 1) {
		newOpVal = 1 - newOpVal;
	}
	
	el.style.opacity = newOpVal;
	el.style.filter = 'alpha(opacity = ' + (newOpVal*100) + ')';
	setTimeout("animateFade_gs(" + currentTick + ",'" + id + "','" + fadeOutTime + "')", 33);
}