$(document).ready(function() {
	
	var validator = $("#progress").validate({
	rules:
	{  
	   projName:
	   {
	   		required: true
	   },
	   location:
	   {
	   		required: true
	   }
	   
	   
	},
	messages:
	{
		projName:
		{
			required: '<div class="error-edit-profile">The project name name field is required</div>'
			//email: '<div class="error-edit-profile">The email is not valid format.</div>'
			
		},
		location:
		{
			required: '<div class="error-edit-profile">The location field is required.</div>'
			
		},
		
		
		debug:true
	}
	
	});
	
	
});
// JavaScript Document// JavaScript Document