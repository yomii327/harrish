$(document).ready(function() {
	
	var validator = $("#e_r_frm").validate({
	rules:
	{  
	   ownerName:
	   {
	   		required: true,
			noSpecialChars:true
	   },
	   userName:
	   {
	   		required: true,
			noSpecialChars:true,
			minlength:4
			//maxlength:12
	   },
	   password:
	   {
	   		required: true,
			minlength:6,
			maxlength:12	
	   },
	   
	   phone:
	   {
	   		required: true,
			number:true,
			minlength :10
	   },
	   email:
	   {
	   		required: true,
			email:true
	   }
	},
	messages:
	{
		ownerName:
		{
			required: '<div class="error-edit-profile">The full name field is required</div>'
			//email: '<div class="error-edit-profile">The email is not valid format.</div>'
			
		},
		userName:
		{
			required: '<div class="error-edit-profile">The username field is required</div>',
			minlength: '<div class="error-edit-profile">Please enter at least 4 characters</div>'
			//maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'
			
		},
		password:
		{
			required: '<div class="error-edit-profile">The password field is required</div>',
			minlength: '<div class="error-edit-profile">Please enter at least 6 characters</div>',
			maxlength: '<div class="error-edit-profile">Please enter no more than 12 characters</div>'
			
		},
		
		phone:
		{
			required: '<div class="error-edit-profile">The phone no. field is required</div>',
			number:'<div class="error-edit-profile">Invalid phone no. Only numbers are allow</div>',
			minlength:'<div class="error-edit-profile">The phone no. be greater than 9 character</div>'
			
		},
		email:
		{
			required: '<div class="error-edit-profile">The email id field is required</div>',
			email: '<div class="error-edit-profile">Invalid email id format</div>'
			
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
	jQuery.validator.addMethod("noSpecialChars", function(value, element) {
      return this.optional(element) || /^[a-z0-9\_\ ]+$/i.test(value);
	  }, "<div class='error-edit-profile'>Special Character not allowed</div>");

	
});
// JavaScript Document// JavaScript Document