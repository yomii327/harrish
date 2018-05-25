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

// Add new user in address book
function addNewUserInAddressBook(){
	modalPopup(align, topPopup, 800, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'add_user_in_estimate_addressBook.php?name='+Math.random(), loadingImage, loadCompAddressbook);	
}
function loadCompAddressbook(){
	$('#companyAddBook').dataTable( {
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "estimate_addressbook_data_table.php?reqFrom=projectwise",
		"iDisplayLength": 20,
		"bStateSave": true,
		"aoColumnDefs": [ {  "bVisible": false, "aTargets": [ 6 ] }, {  "bSearchable": false, "bSortable": false, "aTargets": [ 5 ] }],
	} );
}
// Save new user in address book
function saveNewUserInAddressBook(){//antiqueID
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
	$.post('add_user_in_estimate_addressBook.php?uniqueID='+Math.random(), $('#addUserForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			jAlert(jsonResult.msg);	
			refreshAddressBookTable();
			closePopup(300);
		}else{
			jAlert(jsonResult.msg);	
		}																							
	});
}

//  Edit user details in address book
function editAddressBookUser(userID, userType){
	console.log(userID, userType);
	modalPopup(align, topPopup, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'edit_estimate_addressBook_user.php?name='+Math.random()+'&userid='+userID+'&usertype='+userType, loadingImage);	
}

// Update user details in address book
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
	$.post('edit_estimate_addressBook_user.php?uniqueID='+Math.random(), $('#addUserForm').serialize()).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);	
		if(jsonResult.status){
			jAlert(jsonResult.msg);
			refreshAddressBookTable();
			closePopup(300);
		}else{
			jAlert(jsonResult.msg);	
		}																							
	});
}

// Delte user from address book
function deleteAddressBookUser(userID){
	var r = jConfirm('Do you want to delete contact person ?', null, function(r){
		if(r == true){
			showProgress();
			$.post("delete_estimate_addressBook_user.php", {userid:userID, name:Math.random()}).done(function(data) {
				hideProgress();
				var jsonResult = JSON.parse(data);	
				if(jsonResult.status){
					jAlert(jsonResult.msg);	
					refreshAddressBookTable();
					//$('#userID_'+userID).hide('slow');
				}else{
					jAlert(jsonResult.msg);	
				}
			});
		}
	});
}




function mapAssociateUser(userID, userType, totalUser){
	if(totalUser>0){
		//console.log(userID, userType);
		modalPopup(align, 250, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'estimate_map_addressBook_associate_user.php?name='+Math.random()+'&userid='+userID+'&usertype='+userType, loadingImage);	
	}else{
		jAlert("You do not have any user for mapping.");	
	}	
}

function saveMapAssociateUser(){
	showProgress();
	$.post('estimate_map_addressBook_associate_user.php?uniqueID='+Math.random(), $('#userMapForm').serialize()).done(function(data) {
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
