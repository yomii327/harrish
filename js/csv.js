$(document).ready(function() {
	var validator = $("#csvLocation").validate({
	rules:
	{  
	   csvFile:
	   {
	   		required: true,
			accept:"csv"
	   }
	},
	messages:
	{
		name:
		{
			required: '<div class="error-edit-profile">The project name field is required</div>',
			accept: '<div class="error-edit-profile">Please select .csv file</div>'
			
		},
		
		
		debug:true
	}
	
	});
	
});
// JavaScript Document// JavaScript Document