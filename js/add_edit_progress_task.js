$(document).ready(function() {
	
	var validator = $("#add_editProgress").validate({
	rules:
	{  
	   task:
	   {
	   		required:true
	   },
	   loaction:
	   {
	   		required:true
	   },
	   sublocation:
	   {
	   		required:true
	   },
	   sdate:
	   {
	   		required: true
	   },
	   edate:
	   {
	   		required: true
	   }
	},
	messages:
	{
		task:
		{
			required: '<div class="error-edit-profile">The task field is required</div>'
		},
		loaction:
		{
			required: '<div class="error-edit-profile">The location field is required</div>'
			
		},
		sublocation:
		{
			required: '<div class="error-edit-profile">The sublocation field is required</div>'
		},
		
		sdate:
		{
			required: '<div class="error-edit-profile">The start date field is required</div>'
		},
		edate:
		{
			required: '<div class="error-edit-profile">The end date is required</div>'
		},
		debug:true
	}
	
	});
	
	
});
// JavaScript Document// JavaScript Document