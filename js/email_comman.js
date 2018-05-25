// JavaScript Document

var align = 'center';
var topPopup = 30;
var width = 600;
var padding = 10;
var backgroundColor = '#FFFFFF';
var borderColor = '#333333';
var borderWeight = 4;
var borderRadius = 5;
var fadeOutTime = 300;
var disableColor = '#666666';
var disableOpacity = 40;
var loadingImage = 'images/loadingAnimation.gif';		//Use relative path from this page


function addAddressUser(){
	modalPopup(align, topPopup, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_addressBook_user.php?name='+Math.random(), loadingImage);	
}

function editAddressUser(userID, userType){
	console.log(userID, userType);
	modalPopup(align, topPopup, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_addressBook_user.php?name='+Math.random()+'&userid='+userID+'&usertype='+userType, loadingImage);	
}

function deleteAddressUser(userID){
	var r = jConfirm('Do you want to delete contact person ?', null, function(r){
		if(r == true){
			showProgress();
			$.post("delete_addressBook_user.php", {userid:userID, name:Math.random()}).done(function(data) {
				hideProgress();
				var jsonResult = JSON.parse(data);	
				if(jsonResult.status){
					jAlert(jsonResult.msg);	
					RefreshTable();
					$('#userID_'+userID).hide('slow');
				}else{
					jAlert(jsonResult.msg);	
				}
			});
		}
	});
}

function saveAddressBookUser(){//antiqueID
	var fullName = $('#fullName').val().trim();
	var userEmail = $('#userEmail').val().trim();
	if(fullName == ''){
		$('#errorFullName').show('slow');
		return false;
	}else{
		$('#errorFullName').hide('slow');
	}
	if(userEmail == ''){
		$('#errorUserEmail').show('slow');
		return false;
	}else{
		$('#errorUserEmail').hide('slow');
	}
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (!filter.test(userEmail)) {
		$('#errorUserEmailValid').show('slow');
		return false;
	}else{
		$('#errorUserEmailValid').hide('slow');
	}
	showProgress();
	$.post('add_addressBook_user.php?uniqueID='+Math.random(), $('#addUserForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			jAlert(jsonResult.msg);	
			var htmlCont = '<li id="userID_'+jsonResult.dataId+'">'+jsonResult.dataString+'</li>';
			$('#addressBookUserList').append(htmlCont);
			RefreshTable();
			closePopup(300);
		}else{
			jAlert(jsonResult.msg);	
		}																							
	});
}

function updateAddressBookUser(){//antiqueID
	var fullName = $('#fullName').val().trim();
	var userEmail = $('#userEmail').val().trim();
	if(fullName == ''){
		$('#errorFullName').show('slow');
		return false;
	}else{
		$('#errorFullName').hide('slow');
	}
	if(userEmail == ''){
		$('#errorUserEmail').show('slow');
		return false;
	}else{
		$('#errorUserEmail').hide('slow');
	}
	var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (!filter.test(userEmail)) {
		$('#errorUserEmailValid').show('slow');
		return false;
	}else{
		$('#errorUserEmailValid').hide('slow');
	}
	showProgress();
	$.post('edit_addressBook_user.php?uniqueID='+Math.random(), $('#addUserForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			jAlert(jsonResult.msg);
			var htmlCont = jsonResult.dataString;
			$('#userID_'+jsonResult.dataId).html(htmlCont);
			RefreshTable();
			closePopup(300);
		}else{
			jAlert(jsonResult.msg);	
		}																							
	});
}


function mapAssociateUser(userID, userType, totalUser){
	if(totalUser>0){
		//console.log(userID, userType);
		modalPopup(align, 250, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'map_addressBook_associate_user.php?name='+Math.random()+'&userid='+userID+'&usertype='+userType, loadingImage);	
	}else{
		jAlert("You do not have any user for mapping.");	
	}	
}

function saveMapAssociateUser(){
	showProgress();
	$.post('map_addressBook_associate_user.php?uniqueID='+Math.random(), $('#userMapForm').serialize()).done(function(data) {
		hideProgress();
//		console.log(data);
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			jAlert(jsonResult.msg);	
		/*	var htmlCont = '<li id="userID_'+jsonResult.dataId+'">'+jsonResult.dataString+'</li>';
			$('#addressBookUserList').append(htmlCont);
		*/
			RefreshTable();
			closePopup(300);
		}else{
			jAlert(jsonResult.msg);	
		}																							
	});
}