$(document).ready(function() {
	
	var validator = $("#managerEdit").validate({
	rules:
	{  
	   fullname:
	   {
	   		required: true
	   },
	   cname:
	   {
	   		required: true
	   },
	   username:
	   {
	   		required: true,
			minlength:4,
			maxlength:12
	   },
	   
	   memail:
	   {
	   		required: true,
			email:true
	   },
	   mobile:
	   {
	   		required: true
	   },
	   pwd:{
		   required: true,
		   minlength:6,
		   maxlength:12
		},
		rePwd:{
			required: true,
			equalTo: "#pwd"			
		},		
	},
	messages:
	{
		fullname:
		{
			required: '<div class="error-edit-profile">The fullname field is required</div>'
			//email: '<div class="error-edit-profile">The email is not valid format.</div>'
			
		},
		cname:
		{
			required: '<div class="error-edit-profile">The company name field is required.</div>'
			
		},
		username:
		{
			required: '<div class="error-edit-profile">The username field is required</div>',
			minlength: '<div class="error-edit-profile">Please enter atleast 4 characters</div>',
			maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'
			
		},
		
		memail:
		{
			required: '<div class="error-edit-profile">The email field is required</div>',
			email: '<div class="error-edit-profile">Invalid email address</div>'
			
		},
		mobile:
		{
			required: '<div class="error-edit-profile">The mobile field is required</div>'
			
		},
		pwd:{
			required: '<div class="error-edit-profile">The password field is required</div>',
			minlength: '<div class="error-edit-profile">Please enter at least 6 characters</div>',
			maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'
		},
		rePwd:{
			required: '<div class="error-edit-profile">The re password field is required</div>',
			equalTo: '<div class="error-edit-profile">The passwords you entered do not match. Please try again.</div>',
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