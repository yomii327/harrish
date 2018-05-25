$(document).ready(function() {
	
	var validator = $("#editproject").validate({
	rules:
	{  
	   name:
	   {
	   		required: true
	   },
	   protype:
	   {
	   		required: true
	   },
	   line1:
	   {
	   		required: true
	   },
	   
	   suburb:
	   {
	   		required: true
	   },
	   state:
	   {
	   		required: true
	   },
	   postcode:
	   {
	   		required: true
	   },
	   country:
	   {
	   		required: true
	   }
	},
	messages:
	{
		name:
		{
			required: '<div class="error-edit-profile">The project name field is required</div>'
			//email: '<div class="error-edit-profile">The email is not valid format.</div>'
			
		},
		protype:
		{
			required: '<div class="error-edit-profile">The project type field is required.</div>'
			
		},
		line1:
		{
			required: '<div class="error-edit-profile">The address line 1 field is required</div>'
			
		},
		
		suburb:
		{
			required: '<div class="error-edit-profile">The suburb field is required</div>'
			
		},
		state:
		{
			required: '<div class="error-edit-profile">The state field is required</div>'
			
		},
		postcode:
		{
			required: '<div class="error-edit-profile">The post code field is required</div>'
			
		},
		country:
		{
			required: '<div class="error-edit-profile">The country field is required</div>'
			
		},
		
		debug:true
	}
	
	});
	jQuery.validator.addMethod("alpha", function( value, element ) {
		return this.optional(element) || /^[a-zA-Z ]+$/.test(value);
	}, "Please use only alphabets (a-z or A-Z)");
	jQuery.validator.addMethod("numeric", function( value, element ) {
		return this.optional(element) || /^[0-9]+$/.test(value);
	}, "Please use only numeric values (0-9)");
	jQuery.validator.addMethod("alphanumeric", function( value, element ) {
		return this.optional(element) || /^[a-z A-Z0-9]+$/.test(value);
	}, "You can use only a-z A-Z 0-9 characters");
	jQuery.validator.addMethod("mobile", function( value, element ) {
		return this.optional(element) || /^[ 0-9+-]+$/.test(value);
	}, "You can use only 0-9 - + characters");
	jQuery.validator.addMethod("login", function( value, element ) {
		return this.optional(element) || /^[A-Za-z0-9_.]+$/.test(value);
	}, "You can use only a-z A-Z 0-9 _ and . characters");
	
});
// JavaScript Document// JavaScript Document