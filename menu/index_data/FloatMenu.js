/**
 * @author alu
 */

var FloatMenu = {};

FloatMenu.menuClassName = "FloatMenuClass";
FloatMenu.menuObjects = [];
FloatMenu.menuOptions = [];

// ==== TOOLTIP CLASS ==== //
FloatMenu.Tooltip = function(){
	this.text;
	this.id = -1;
	this.element = null;
	this.opacity = 100;
	this.fadeTimer;
	this.fadeState = 0; // 0 - none , 1 - fading in , 2 - fading out
}

// ==== FADES ELEMENT TO THE TARGET OPACITY ==== //
FloatMenu.Tooltip.prototype.fadeTo = function( opacity , step ){
	var opacityStep = step;
	if( this.opacity > opacity ){
		opacityStep = -step;
	}

	if( opacityStep < 0){
		if( this.opacity+opacityStep > opacity ){
			this.setOpacity(this.opacity+opacityStep);
		}else{
			this.setOpacity(opacity);
			this.setFadeState(0);
		}
	}else{
		if( this.opacity+opacityStep < opacity ){
			this.setOpacity(this.opacity+opacityStep);
		}else{
			this.setOpacity(opacity);
			this.setFadeState(0);
		}
	}
	
	// Repeat moveToXY if destination is not reached
	if( this.fadeState != 0 ){
		
		var self = this;
		var anonymousFade = function(){
			self.fadeTo( opacity , step );
		}
		this.fadeTimer = setTimeout(anonymousFade, 1000/36);
	}
	
	if( this.opacity == 0 ){
		this.element.style.display = 'none';
	}else{
		this.element.style.display = 'block';
	}
}
// ============================================= //

FloatMenu.Tooltip.prototype.setOpacity = function(opacity){
	this.opacity = opacity;
	if( this.element != null ){
		this.element.style.opacity = opacity/100;
		this.element.style.filter = "alpha(opacity=" + opacity + ")";
	}
}

FloatMenu.Tooltip.prototype.setFadeState = function(state){
	this.fadeState = state;
}

FloatMenu.Tooltip.prototype.setText = function(text){
	this.text = text;
}

FloatMenu.Tooltip.prototype.setElement = function(element){
	this.element = element;
}

FloatMenu.Tooltip.prototype.setId = function(id){
	this.id = id;
}
// ======================= //

// ==== TILE CLASS ==== //
FloatMenu.MenuElement = function(){
	this.id = -1; 					// Place in a menu starting from zero.
	this.ownerId = -1; 				// Menu (container) index in FloatMenu.menuObjects
	this.element = null;			// <li> element
	this.width = 0;
	this.height = 0;
	this.xcoord = 0;				// represents style.left
	this.ycoord = 0;				// represents style.top
	this.defaultX = 0;				
	this.defaultY = 0;
	this.tooltip;
	this.timer;						// used to stop animation with clearTimout(timer)
	this.movingState = 0;			// 0 - default position, 
									// 1 - mouseOver movement , 
									// 2 - mouseOut movement, 
									// 3 - element is clicked
}

FloatMenu.MenuElement.prototype.setTooltip = function( tooltip ){
	this.tooltip = tooltip;
}

FloatMenu.MenuElement.prototype.setId = function( id ){
	this.id = id;
}

FloatMenu.MenuElement.prototype.setOwnerId = function( ownerId ){
	this.ownerId = ownerId;
}

FloatMenu.MenuElement.prototype.setDefaultPosition = function( defaultX , defaultY ){
	this.defaultX = defaultX;
	this.defaultY = defaultY;
}

FloatMenu.MenuElement.prototype.setElement = function( element ){
	this.element = element;
}

FloatMenu.MenuElement.prototype.setPosition = function (x , y){
	this.xcoord = x;
	this.ycoord = y;
	if( this.element != null ){
		this.element.style.left = x+'px';
		this.element.style.top = y+'px';
	}
}

FloatMenu.MenuElement.prototype.setSize = function( width , height ){
	this.width = width;
	this.height = height;
	if( this.element != null ){
		this.element.style.width = width+'px';
		this.element.style.height = height+'px';
	}
}

FloatMenu.MenuElement.prototype.attachEventManager = function( event ){
	
	var self = this;
	var anonymousFunction = function(){
		self.manageAnimation( event );
	}
	
	if( this.element.addEventListener ){
		this.element.addEventListener( event , anonymousFunction , false );
	}else if( this.element.attachEvent ){
		this.element.attachEvent( 'on'+event , anonymousFunction );				// Internet Explorer
	}
}

FloatMenu.MenuElement.prototype.manageActiveMenuElement = function(){
	var targetMenu = FloatMenu.menuObjects[this.ownerId];
	var activeMenuElement = null;
	
	// Deactivate another active menu element if it exists
	if( targetMenu.activeElementId >= 0 ){
		activeMenuElement = targetMenu.menuElements[targetMenu.activeElementId];
		
		activeMenuElement.movingState = 0;
		activeMenuElement.moveMenuElement( 2, 100, targetMenu.step);
	}
	
	// Stop animation for clicked element and go to the target position
	clearTimeout(this.timer);
	this.setPosition( this.defaultX+targetMenu.horizontalShift,
	 				  this.defaultY+targetMenu.verticalShift);
	
	clearTimeout(this.tooltip.fadeTimer);
	this.tooltip.setFadeState(2);
	this.tooltip.fadeTo(0, targetMenu.fadeStep );
	
	// Save active element id and set movingState to clicked (3)				  
	targetMenu.activeElementId = this.id;
	this.movingState = 3;
}

FloatMenu.MenuElement.prototype.manageAnimation = function( event ){
	var direction = 0;
	// Shifts element when mouse is over it
	if( event == 'mouseover'){
		direction = 1;
		this.moveMenuElement( direction , 100 , 
							  FloatMenu.menuObjects[this.ownerId].step );
	// Shifts element back if mouse is out
	}else if( event == 'mouseout' ){
		direction = 2;
		this.moveMenuElement( direction, 100 , 
							  FloatMenu.menuObjects[this.ownerId].step);
	// Shows active element if clicked
	}else if( event == 'click'){
		if(FloatMenu.menuObjects[this.ownerId].activeElementMode == 1) {
			this.manageActiveMenuElement();
		}
	}
}

FloatMenu.MenuElement.prototype.moveMenuElement = function( direction, movementPercentage , stepSize ){
	var toX = this.xcoord;
	var toY = this.ycoord;
	var step = stepSize;
	var targetMenu = FloatMenu.menuObjects[this.ownerId];
	if( direction == 1 ){
		if( this.movingState != 1 && this.movingState != 3){
			this.movingState = 1;
			
			clearTimeout(this.timer);
			var toX = this.defaultX+targetMenu.horizontalShift/100*movementPercentage;
			var toY = this.defaultY+targetMenu.verticalShift/100*movementPercentage;
			this.moveToXY(toX,toY,step);
			if( targetMenu.tooltipMode == 1 && this.tooltip){
				clearTimeout(this.tooltip.fadeTimer);
				this.tooltip.setFadeState(1);
				this.tooltip.fadeTo( 100 , targetMenu.fadeStep );
			}
		}
	}else if( direction == 2 && this.movingState != 3){
		if( this.movingState != 2 ){
			this.movingState = 2;
			clearTimeout(this.timer);
			var toX = this.defaultX;
			var toY = this.defaultY;
			this.moveToXY(toX,toY,step);
			if (targetMenu.tooltipMode == 1 && this.tooltip) {
				clearTimeout(this.tooltip.fadeTimer);
				this.tooltip.setFadeState(2);
				this.tooltip.fadeTo(0, targetMenu.fadeStep);
			}
		}
	}
}

FloatMenu.MenuElement.prototype.moveToXY = function( toX , toY , step ){
	var stepY = step;
	var stepX = step;
	
	if( toX < this.xcoord ){
		stepX = -step;
	}
	
	if( toY < this.ycoord ){
		stepY = -step;
	}
	
	var directionX = step/stepX;
	var directionY = step/stepY;

	// Moves menu element to its destination
	if( directionX*(this.xcoord+stepX) < Math.abs(toX) && di