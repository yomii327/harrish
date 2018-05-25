/*
 * jQuery UI Multiselect
 * ***************************************/
 
(function($){
	"use strict";
	
	loadIssueToData(0)
	$("#issuedto-unselectd").scroll(function(){
		if($("#loader").css('display') == 'none') {
			if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight){
				var limitStart = $("#issuedto-unselectd ul > li").length;
				loadIssueToData(limitStart);
			}
		}
	});
	
	function loadIssueToData(limitStart) {
		$('#loader').show('fast');
		var searchValue = $('#issuedtoFilter').val();
		var params = { limitStart: limitStart,searchValue:searchValue };
		$.post(['getIssuedToData.php'].join(), params).done(function(response){
			$("#issuedto-unselectd ul").append(response);
			$('#loader').hide('fast');
		});
	}
	
	//keyup events
	$('input[type="text"]#issuedtoFilter').keyup(function(e){
		if(e && e.preventDefault) {
			e.preventDefault();
		}
		var searchText = $(this).val().toLowerCase();
		$('#issuedto-unselectd > ul > li').each(function(){
			var currentLiText = $(this).text().toLowerCase(),
				showCurrentLi = currentLiText.indexOf(searchText) !== -1;
			$(this).toggle(showCurrentLi);
		});
		return false;
	});
})(jQuery);

/*	FUNCTIONS
 * ********************************************************************/

//Add Elements.
function addElement(elements){
	var $this = $('a#'+elements),
		issName = $this.text(),
		issuedto = $('select#assignIssueTo'),
		index = $this.parent().data('index');
	$this.parent().fadeOut('fast').remove();
	
	//Add element into selected issuedto
	$('#issuedto-selectd ul').append('<li class="ui-state-default ui-element ui-draggable" data-index="'+ index +'"><span class="ui-helper-hidden"></span><a id="uiRemoveElement_'+ index +'" onClick="removeElement(this.id)" href="javascript:void(0);" class="title ui-remove-element">'+ issName +'</a><a href="javascript:void(0);" class="ui-state-default"><span class="ui-corner-all ui-icon ui-icon-minus"></span></a></li>');
	
	//Add option elements for assign issuedto.
	if(issuedto.find('option[value="'+ index +'"]').length == 0){
		issuedto.append('<option value="'+ index +'" selected="selected">'+ issName +'</option>');
	}
	
	var selectedItem = $('#issuedto-selectd ul li').length
	$('.section-right span.heading > em').html(selectedItem);
}

//Remove elements.
function removeElement(elements){
	var $this = $('a#'+ elements),
		issName = $this.text(),
		issuedto = $('select#assignIssueTo'),
		index = $this.parent().data('index');
	$this.parent().fadeOut('fast').remove();
	
	//Remove element into selected issuedto
	$('#issuedto-unselectd ul').append('<li class="ui-state-default ui-element ui-draggable" data-index="'+ index +'"><span class="ui-helper-hidden"></span><a id="uiAddElement_'+ index +'" onClick="addElement(this.id)" href="javascript:void(0);" class="title ui-add-element">'+ issName +'</a><a href="javascript:void(0);" class="ui-state-default"><span class="ui-corner-all ui-icon ui-icon-plus"></span></a></li>');
	
	//Remove option elements for assign issuedto.
	if(issuedto.find('option[value="'+ index +'"]').length != 0){
		issuedto.find('option[value="'+ index +'"]').remove();
	}
	
	var selectedItem = $('#issuedto-selectd ul li').length
	$('.section-right span.heading > em').html(selectedItem);
}


function searchIssuedTo(){
        var limitStart =0;
        var searchValue = $('#issuedtoFilter').val();

       $('#loader').show('fast');
        var params = { limitStart: limitStart,searchValue:searchValue };
        $.post(['getIssuedToData.php'].join(), params).done(function(response){
            $("#issuedto-unselectd ul").html(response);
            $('#loader').hide('fast');
        });
   }
