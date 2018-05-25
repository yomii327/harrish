$(document).ready(function() {
	
	var validator = $("#addissueto").validate({
	rules:
	{  
	   company_name:
	   {
	   		required: true
	   },
	   emailid:
	   {
			email:true   
		},
		contact_name:
		{
			alphanumeric:true
		},
		phone:
		{
			number:true	
		}
		
	},
	messages:
	{
		company_name:
		{
			required: '<div class="error-edit-profile">The company name field is required</div>'
			//email: '<div class="error-edit-profile">The email is not valid format.</div>'
			
		},
		emailid:
		{
			email: '<div class="error-edit-profile">The email is not valid format.</div>'
			
		},
		phone:
		{
			number: '<div class="error-edit-profile">The phone field  must be number.</div>'
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
	}, "<div class='error-edit-profile'>You can use only a-z A-Z 0-9 characters</div>");
	jQuery.validator.addMethod("mobile", function( value, element ) {
		return this.optional(element) || /^[ 0-9+-]+$/.test(value);
	}, "You can use only 0-9 - + characters");
	jQuery.validator.addMethod("login", function( value, element ) {
		return this.optional(element) || /^[A-Za-z0-9_.]+$/.test(value);
	}, "You can use only a-z A-Z 0-9 _ and . characters");
	
});
// JavaScript Document// JavaScript Document