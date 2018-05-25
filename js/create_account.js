$(document).ready(function() {
	
	var validator = $("#accountFrm").validate({
	rules:
	{  
	   fname:
	   {
	   		required: true,
			minlength:3,
			maxlength:25
			
	   },
	   compname:
	   {
	   		required: true,
			minlength:3,
			maxlength:50
			
	   },
	   bus_line1:
	   {
	   		required: true,
			minlength:3,
			maxlength:80
	   },
	   
	   bus_suburb:
	   {
	   		required: true
	   },
	   bus_state:
	   {
	   		required: true,
			minlength:3,
			maxlength:80
	   },
	   bus_post:
	   {
	   		required: true,
			number:true,
			maxlength:13
	   },
	   bus_country:
	   {
	   		required: true
	   },
	   email:
	   {
	   		required: true,
			email:true
	   },
	   mobile:
	   {
	   		required: true,
			number:true,
			maxlength:13
	   },
	   bil_line1:
	   {
	   		required: true,
			minlength:3,
			maxlength:80
	   },
	   
	   bil_suburb:
	   {
	   		required: true
	   },
	   bil_state:
	   {
	   		required: true,
			minlength:3,
			maxlength:80
	   },
	   bil_post:
	   {
	   		required: true,
			number:true,
			maxlength:13
	   },
	   bil_country:
	   {
		   required: true
	   },
	   username:
	   {
		   required: true
	   },
	   password:
	   {
		   required: true,
		   minlength:8,
			maxlength:35
	   }/*,
	   c_logo:
	   {
			accept:"jpg|JPG|JPEG|jpeg|png|PNG|bmp|BMP|gif|GIF"
		}*/
	   
	},
	messages:
	{
		fname:
		{
			required: '<div class="error-edit-profile">The full name field is required</div>',
			minlength:'<div class="error-edit-profile">The full name must be greater than 3 character</div>',
			maxlength:'<div class="error-edit-profile">The full name shuold be maximum 25 character</div>'
			
		},
		compname:
		{
			required: '<div class="error-edit-profile">The comapny name field is required.</div>',
			minlength:'<div class="error-edit-profile">The comapny name must be greater than 3 character</div>',
			maxlength:'<div class="error-edit-profile">The comapny name shuold be maximum 50 character</div>'
			
		},
		bus_line1:
		{
			required: '<div class="error-edit-profile">The address line 1 field is required</div>',
			minlength:'<div class="error-edit-profile">The address line 1 must be greater than 3 character</div>',
			maxlength:'<div class="error-edit-profile">The address line 1 shuold be maximum 80 character</div>'
			
		},
		
		bus_suburb:
		{
			required: '<div class="error-edit-profile">The Suburb field is required</div>'
			
		},
		bus_state:
		{
			required: '<div class="error-edit-profile">The state field is required</div>',
			minlength:'<div class="error-edit-profile">The state must be greater than 3 character</div>',
			maxlength:'<div class="error-edit-profile">The state shuold be maximum 80 character</div>'
			
		},
		bus_post:
		{
			required: '<div class="error-edit-profile">The post code field is required</div>',
			number:'<div class="error-edit-profile">Invalid post code only numbers are allow</div>',
			maxlength:'<div class="error-edit-profile">The post code shuold be maximum 13 character</div>'
			
		},
		bus_country:
		{
			required: '<div class="error-edit-profile">The country field is required</div>'
			
		},
		email:
		{
			required: '<div class="error-edit-profile">The email field is required</div>',
			email: '<div class="error-edit-profile">Invalid email format</div>'
			
			
		},
		mobile:
		{
			required: '<div class="error-edit-profile">The mobile field is required</div>',
			number:'<div class="error-edit-profile">Invalid mobile only numbers are allow</div>',
			maxlength:'<div class="error-edit-profile">The mobile shuold be maximum 13 character</div>'
			
		},
		bil_line1:
		{
			required: '<div class="error-edit-profile">The billing address field is required</div>',
			minlength:'<div class="error-edit-profile">The  billing address must be greater than 3 character</div>',
			maxlength:'<div class="error-edit-profile">The  billing address shuold be maximum 80 character</div>'
			
		},
		
		bil_suburb:
		{
			required: '<div class="error-edit-profile">The billing suburb field is required</div>'
			
		},
		bil_state:
		{
			required: '<div class="error-edit-profile">The billing state field is required</div>',
			minlength:'<div class="error-edit-profile">The billing state must be greater than 3 character</div>',
			maxlength:'<div class="error-edit-profile">The billing state shuold be maximum 80 character</div>'
			
		},
		bil_post:
		{
			required: '<div class="error-edit-profile">The billing post code field is required</div>',
			number:'<div class="error-edit-profile">Invalid billing post code only numbers are allow</div>',
			maxlength:'<div class="error-edit-profile">The billing post code shuold be maximum 13 character</div>'
			
		},
		bil_country:
		{
			required: '<div class="error-edit-profile">The billing country field is required</div>'
			
		},
		username:
		{
			required: '<div class="error-edit-profile">The username field is required</div>'
			
		},
		password:
		{
			required: '<div class="error-edit-profile">The password field is required</div>',
			minlength:'<div class="error-edit-profile">The password must be greater than 8 character</div>',
			maxlength:'<div class="error-edit-profile">The password shuold be maximum 35 character</div>'
			
		},
		/*c_logo:
		{
			accept:'<div class="error-edit-profile">Invalid image format</div>'
		},*/
		
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