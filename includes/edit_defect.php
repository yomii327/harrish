<?php set_time_limit(36000000000000);
ob_start(); if(!isset($_SESSION['ww_is_builder']) && !isset($_SESSION['ww_is_company'])){ ?>
<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
$df_id = base64_decode($_GET['did']);
$pid = base64_decode($_GET['pid']); 
//echo $df_id;

$owner_id = $_SESSION['ww_owner_id'];	
$msg ='';
include('includes/commanfunction.php');
$object= new COMMAN_Class();
function searchArray($array, $val){
	foreach($array as $arr){
		if(in_array($val, $arr)){
			return true;
		}
	}
	return false;
} ?>
<style>
#locationsContainer{ overflow-y: scroll; max-height: 200px; min-height: 150px; border-radius:5px; -moz-border-radius: 5px;  -webkit-border-radius: 5px; border:1px solid; margin-top:15px; width:420px; }
table.gridtable { border-width: 1px; border-color: #FFF; border-collapse: collapse; }
table.gridtable td { border-width: 1px; padding: 8px; border-style: solid; border-color: #FFF; }
.clickableLines{ cursor:pointer; padding:5px; margin-right:5px;}
.clickableLines:hover{ background-color:#336FCE;padding:5px; margin-right:5px;color:#FFFFFF;}
#dropDown{ cursor:pointer; }
#discriptionHide{ display:none; height: 150px; overflow-y: scroll; position:absolute; background:#FFFFFF; border:1px solid #0BA4FF; width:420px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; z-index:1000; color:#000000; text-shadow:none; }
.issueTo{ border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; border:1px solid; width:150px; border-color:#FFFFFF; height:25px;}
ul.telefilms{list-style:none; cursor:pointer; font-size:15px;}
/*ul.telefilms li{height:15px;}*/
ul.telefilms li ul{list-style:none; line-height:30px;}
.jtree-arrow { padding-right: 5px; font-size: 15px; }

.avpw_is_fullscreen #avpw_controls {
    bottom: 8%;
    left: 14%;
    position: absolute;
    right: 14%;
    top: 55%;
}
/*.avpw_close_button {
    border: 2px solid #FFFFFF;
    border-radius: 15px 15px 15px 15px;
    cursor: pointer;
    height: 26px;
    margin: 30% -10px 0 0;
    position: absolute;
    right: 0;
    width: 26px;
}*/
#attachments{ overflow-y:scroll; max-height:250px; min-height:200px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius:5px; border:1px solid; margin-top:15px; width:630px; }
.innerDiv{ float:left; border:1px solid white; width:120px; height:120px; margin:0px 0px 20px 30px; }
label.filebutton{ width:120px; height:40px; overflow:hidden; position:relative; }
label input { cursor: pointer; font-size: 7px; left: -1px; line-height: 0; margin: 0; opacity: 0; padding: 0; position: absolute; top: -2px; z-index: 999; filter:alpha(opacity=0);}

#locationsContainer{ overflow-y: scroll; max-height: 200px; min-height: 150px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; border:1px solid; margin-top:15px; width:350px; }
table.gridtable { border-width: 1px; border-color: #FFF; border-collapse: collapse; }
table.gridtable td { border-width: 1px; padding: 8px; border-style: solid; border-color: #FFF; }
div#spinner{ display: none; width:100%; height: 100%; position: fixed; top: 0; left: 0; background:url(images/loadingAnimation.gif) no-repeat center #CCCCCC; text-align:center; padding:10px; font:normal 16px Tahoma, Geneva, sans-serif; border:1px solid #666; z-index:2; overflow: auto; opacity : 0.8; }
.clickableLines{ cursor:pointer; padding:5px; margin-right:5px;}
.clickableLines:hover{ background-color:#336FCE;padding:5px; margin-right:5px;color:#FFFFFF;}
#dropDown{ cursor:pointer; }
#discriptionHide{ display:none; height: 150px; overflow-y: scroll; position:absolute; background:#FFFFFF; border:1px solid #0BA4FF; width:290px; border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; z-index:1000; color:#000000; text-shadow:none; margin-top:10px; }
.issueTo{ border-radius:5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; border:1px solid; width:120px; border-color:#FFFFFF; height:25px; }
ul.telefilms{list-style:none; cursor:pointer; font-size:15px;}
/*ul.telefilms li{height:15px;}*/
ul.telefilms li ul{list-style:none; line-height:30px;}
.jtree-arrow { padding-right: 5px; font-size: 15px; }
#mainContainer{ color:#000; width:800px; margin:auto; min-height:70px;; max-height:650px; overflow-y:scroll;}
textarea[readonly="readonly"{
	background-image:url(images/text_detail_spl.png) !important;
	border:none;
	outline:none;
}
</style>
<!-- Ajax Post -->
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" /> 
<link href="style/css/ajax-uploader.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/location_tree_jquery.min.js"></script>
<script language="javascript" type="text/javascript" src="js/validate.js"></script>
<script language="javascript" type="text/javascript" src="js/ajax.js"></script>
<script type="text/javascript" src="http://feather.aviary.com/js/feather.js"></script>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>

<script type="text/javascript">
var deadLock = false;
$(document).ready(function(){
	var validator = $("#defect_form").validate({
		rules:{
			raisedBy:{
				required: true
			}
		},
		messages:{
			raisedBy:{
				required: '<div class="error-edit-profile">The raised by name field is required</div>'
			},
			debug:true
		}
	});
	
	$("#dropDown").click(function(){
		if ($("#discriptionHide").is(":hidden")){
			$("#discriptionHide").slideDown("slow");
		} else {
			$("#discriptionHide").hide("slow");
		}
	});
	/*$("li.clickableLines").click(function(){
		console.log(this.innerHTML);
		$("#description").val(this.innerHTML);
		$("#discriptionHide").hide("slow");
	});*/
});

function setDescription(str){
	console.log(str);
	$("#description").val(str);
	$("#discriptionHide").hide("slow");
}

var items=0;
var spinnerVisible = false;
function showProgress() {
	if (!spinnerVisible) {
		$("div#spinner").fadeIn("fast");
		spinnerVisible = true;
	}
};
function hideProgress() {
	if (spinnerVisible) {
		var spinner = $("div#spinner");
		spinner.stop();
		spinner.fadeOut("fast");
		spinnerVisible = false;
	}
};
function validateDelete(){
	var r = jConfirm('Do you want to delete Inspection ?', null, function(r){
		if (r==true){
			alert('Word');
		}else{
			return false;
		}
	});
}

  var newDrawing = 0;
   var imageURL='';
   var oldImageURL='';
   var featherEditor = new Aviary.Feather({
       apiKey: 'YXOfUCoDcEeninMRkHb04w',
       apiVersion: 2,
       tools: 'all',
       appendTo: '',
       onSave: function(imageID, newURL) {
           var img = document.getElementById(imageID);
           img.src = newURL;
		   imageURL = newURL;
       },
       onError: function(errorObj) {
           alert(errorObj.message);
       },
	   onClose: function(imageID) {
			saveImage(imageURL);
       }
	});
	function launchEditor(id, src) {
	   // alert(newDrawing);
	   if(id=="drawImage1" && newDrawing==0){ newDrawing=1;
	   	$.ajax({
		   type: "POST",
		   url: "auto_file_upload.php",
		   data: { newDrawing: newDrawing, src: src},
		   success: function(newSrc){ 
				if(newSrc!=0){
					var src = "inspections/drawing/"+newSrc; //alert(msg);
					$("#drawImage1").attr("src", src);
					$("#newDrawing").val(newSrc);
					$("#editDrawing").html('<input type="image" src="images/add_sketch.png" value="Edit photo" onclick="return launchEditor(\'drawImage1\', \''+src+'\');"/>');	
					oldImageURL = "http://<?php echo $_SERVER['HTTP_HOST']; ?>/"+src;
				   featherEditor.launch({
					   image: id,
					   url: "http://<?php echo $_SERVER['HTTP_HOST']; ?>/"+src
				   });	
				}
		   }
		});
	   }else{
		   oldImageURL = "http://<?php echo $_SERVER['HTTP_HOST']; ?>/"+src;
		   featherEditor.launch({
			   image: id,
			   url: "http://<?php echo $_SERVER['HTTP_HOST']; ?>/"+src
		   });
	   }
      return false;
	}
   
	function saveImage(imageURL) {
		$.ajax({
		   type: "POST",
		   url: "http://<?php echo $_SERVER['HTTP_HOST']; ?>/save_aviary.php",
		   data: { url: imageURL, oldImageURL: oldImageURL},
		   success: function(msg){ 
				alert(msg);
		   }
		});
	};
//----------------------------------------------------------------------
<?php if($_SESSION['userRole']!='Sub Contractor'){?>
$(function(){
	var btnUpload=$('#drawing');
	var status=$('#response_drawing');
		setCookie('drawing');
	new AjaxUpload(btnUpload, {
		action: 'auto_file_upload.php?action=drawing&uniqueID='+Math.random(),
		name: 'drawing',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
				// extension is not allowed 
				status.text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
			status.text('Uploading...');
			showProgress();
		},
		onComplete: function(file, response){
			hideProgress();
			status.html(response);
			$('#removeImg3').show();
			var src = $('#response_drawing img:first').attr('src');
			$('#response_drawing img:first').attr('id', 'drawImage1');							
			document.getElementById("editDrawing").style.display = "block";
			document.getElementById("editDrawing").innerHTML = '<input type="image" src="images/add_sketch.png" value="Edit photo" onclick="return launchEditor(\'drawImage1\', \''+src+'\');"/>';						
		}
	});
});
$(function(){
	var btnUpload1=$('#image1');
	var status1=$('#response_image_1');
		setCookie('photo');
	new AjaxUpload(btnUpload1, {
		action: 'auto_file_upload.php?action=imageOne&uniqueID='+Math.random(),
		name: 'image1',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
				// extension is not allowed 
				status.text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
			status1.text('Uploading...');
			showProgress();
		},
		onComplete: function(file, response){
			hideProgress();
			status1.html(response);
			$('#removeImg1').show('fast');
			var src = $('#response_image_1 img:first').attr('src');
			$('#response_image_1 img:first').attr('id', 'photoImage1');
			document.getElementById("editImage1").style.display = "block";
			document.getElementById("editImage1").innerHTML = '<input type="image" src="images/markup.png" value="Edit photo" onclick="return launchEditor(\'photoImage1\', \''+src+'\');"/>';
		}
	});
});
<?php } ?>
$(function(){
	var btnUpload2=$('#image2');
	var status2=$('#response_image_2');
		setCookie('photo');
	new AjaxUpload(btnUpload2, {
		action: 'auto_file_upload.php?action=imageTwo&uniqueID='+Math.random(),
		name: 'image2',
		onSubmit: function(file, ext){
			if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
				// extension is not allowed 
				status1.text('Only JPG, PNG or GIF files are allowed');
				return false;
			}
			status2.text('Uploading...');
			showProgress();
		},
		onComplete: function(file, response){
			hideProgress();
			status2.html(response);
			$('#removeImg2').show();
			var src = $('#response_image_2 img:first').attr('src');
			$('#response_image_2 img:first').attr('id', 'photoImage2');
			document.getElementById("editImage2").style.display = "block";
			document.getElementById("editImage2").innerHTML = '<input type="image" src="images/markup.png" value="Edit photo" onclick="return launchEditor(\'photoImage2\', \''+src+'\');"/>';
		}
	});
});
//----------------------------------------------------------------------
function setCookie(value){
	var exdays = 1;var c_name = 'path';
	var exdate = new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}	
</script>
<!-- Ajax Post -->
<!-- Date Picker Starts -->
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script language="javascript">
window.onload = function(){
		new JsDatePick({
			useMode:2,
			target:"fixedByDate_1",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"fixedByDate_2",
			dateFormat:"%d-%m-%Y"
		});
		new JsDatePick({
			useMode:2,
			target:"fixedByDate_3",
			dateFormat:"%d-%m-%Y"
		});
	};
</script>
<style>.fixedByDate{ background:#FFF; cursor:default; height:20px; -moz-border-radius: 6px; -webkit-border-radius: 6px; border-radius: 6px; }</style>
<!-- Date Picker Ends -->
<?php
if(isset($_POST['removeProject'])){
 	$df_id = $_POST['removeProject'];
	$projectId = $_POST['projectId'];
	
	$deleteQry = "UPDATE project_inspections SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = '".$df_id."' AND project_id = '".$projectId."'";
	mysql_query($deleteQry);

	$deleteQry1 = "UPDATE issued_to_for_inspections SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = '".$df_id."' AND project_id = '".$projectId."'";
	mysql_query($deleteQry1);

	$deleteQry2 = "UPDATE inspection_graphics SET is_deleted = 1, last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = '".$df_id."' AND project_id = '".$projectId."'";
	mysql_query($deleteQry2);


	if(isset($_SESSION['ww_is_company'])){?>
	<script language="javascript" type="text/javascript">window.location.href="?sect=c_defect&bk=Y&ms=<?=base64_encode('Deleted')?>";</script>	
<?php	}else
if(isset($_SESSION['ww_is_builder'])){?>
	<script language="javascript" type="text/javascript">window.location.href="?sect=i_defect&bk=Y&ms=<?=base64_encode('Deleted')?>";</script>	
<?php	} 
}
if(isset($_POST['button'])){

	$historyFlag = false;
	$recodArr = array();//Store record to create history table

	$description = $_POST['description'];
	$projectId = $_POST['projectId'];
	$raisedBy = $_POST['raisedBy'];
	$note = $_POST['note'];
	$df_id = $_POST['df_id'];
	$location = $_POST['location'];
	$locationTree = $_POST['locationTree'];
    $arrayColoumn = array('note','description', 'raisedBy');

//	print_r($_POST);
		$updateQry = "UPDATE project_inspections SET
						inspection_description = '".addslashes($description)."',
						inspection_notes = '".addslashes($note)."',
						inspection_raised_by = '".addslashes($raisedBy)."',
						location_id = '".$location."',
						last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
						last_modified_date = NOW(),
						original_modified_date = NOW(),
						inspection_location =  '".$locationTree."'
					WHERE
						inspection_id = '".$df_id."'";
	mysql_query($updateQry) or die(mysql_error());

	if(mysql_affected_rows() > 0){$historyFlag = true;}//Set Flag Value here
//Record Array to store data start here
		$recodArr['inspection_description'] = $description;
		$recodArr['inspection_notes'] = $note;
		$recodArr['inspection_raised_by'] = $raisedBy;
		$recodArr['inspection_location'] = $locationTree;
		if(!empty($_POST['inspectionGrid'])){
			$recodArr['inspection_grid'] = addslashes(trim($_POST['inspectionGrid']));
		}
		if(!empty($_POST['inspectionElement'])){
			$recodArr['inspection_element'] = addslashes(trim($_POST['inspectionElement']));
		}
		if(!empty($_POST['inspectionDiscipline'])){
			$recodArr['inspection_discipline'] = addslashes(trim($_POST['inspectionDiscipline']));
		}
		if(!empty($_POST['inspectionType'])){
			$recodArr['inspection_type'] = addslashes(trim($_POST['inspectionType']));
		}
		if(!empty($_POST['inspectionSignImage'])){
			$recodArr['inspection_sign_image'] = addslashes(trim($_POST['inspectionSignImage']));
		}
		if(!empty($_POST['inspectionDrawing'])){
			$recodArr['inspection_drawing'] = addslashes(trim($_POST['inspectionDrawing']));
		}
		if(!empty($_POST['inspectionLocationLatitude'])){	
			$recodArr['inspection_location_latitude'] = addslashes(trim($_POST['inspectionLocationLatitude']));
		}
		if(!empty($_POST['inspectionLocationLongitude'])){
			$recodArr['inspection_location_longitude'] = addslashes(trim($_POST['inspectionLocationLongitude']));
		}
//Record Array to store data end here
	
	$updateCheckList = "UPDATE inspection_check_list SET last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = '".$df_id."'";
	mysql_query($updateCheckList) or die(mysql_error());
	/*$updateImages = "UPDATE inspection_graphics SET last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id'].", original_modified_date = NOW() WHERE inspection_id = '".$df_id."'";
	mysql_query($updateImages) or die(mysql_error());*/
	
	//Images Update in inspection_graphics table
		 mysql_query("UPDATE `inspection_graphics` SET `is_deleted` = '1' WHERE inspection_id='".$df_id."' and graphic_type='images'");
		if(isset($_POST["photo"])){
			for($i=0; $i<sizeof($_POST["photo"]); $i++){
				$photoName = mysql_real_escape_string(trim($_POST['photo'][$i]));
				$select="select * from inspection_graphics where graphic_name='".addslashes(trim($photoName))."' AND inspection_id='".$df_id."' AND project_id='".$projectId."' and is_deleted=0";
				$inspGraph=mysql_query($select);
				$row_data=mysql_num_rows($inspGraph);
				$oldData = mysql_fetch_row($inspGraph);
				if($row_data > 0){
					$grahpicQuery = "UPDATE `inspection_graphics` SET `is_deleted` = '0' WHERE `graphic_id` ='".$oldData[0]."'";
					//mysql_query();
				}else{
					$grahpicQuery = "INSERT INTO inspection_graphics SET
									inspection_id = '".$df_id."',
									graphic_type = 'images',
									graphic_name = '".addslashes($photoName)."',
									created_date = NOW(),
									created_by = '".$_SESSION['ww_builder']['user_id']."',
									project_id = '".$projectId."'";
					//mysql_query($insertImage) or die(mysql_error());
				}
				mysql_query($grahpicQuery) or die(mysql_error());
			}
		}
		 mysql_query("UPDATE `inspection_graphics` SET `is_deleted` = '1' WHERE inspection_id='".$df_id."' and graphic_type='drawing'");		
		if(isset($_POST['drawing']) && !empty($_POST['drawing'])){
			$drawingName = mysql_real_escape_string(trim($_POST['drawing']));											
			$select="select * from inspection_graphics where graphic_name='".addslashes(trim($drawingName))."' AND inspection_id='".$df_id."' AND project_id='".$projectId."' and is_deleted=0 and graphic_type='drawing'";
			$inspGraph=mysql_query($select);
			$row_data=mysql_num_rows($inspGraph);
			$oldData = mysql_fetch_row($inspGraph);
			if($row_data > 0){
				mysql_query("UPDATE `inspection_graphics` SET `is_deleted` = '0' WHERE `graphic_id` ='".$oldData[0]."'");
			}else{
				$insertImageDrawing = "INSERT INTO inspection_graphics SET
											inspection_id = '".$df_id."',
											graphic_type = 'drawing',
											graphic_name = '".addslashes($drawingName)."',
											created_date = NOW(),
											created_by = '".$_SESSION['ww_builder']['user_id']."',
											project_id = '".$projectId."'";
					mysql_query($insertImageDrawing) or die(mysql_error());
			
			}
			//die;
		}
//Images Update in inspection_graphics table

     $arrayColoumnIuueTo =array('issueTo','fixedByDate','costAttribute','status','costImpact','costImpactPrice');		
	$toEmailArr=array();
	for($i=0; $i<sizeof($_POST["issue_to_id"]); $i++){
		$issue_to_id = '';$issueTo = '';
		$issue_to_id = $_POST['issue_to_id'][$i];
		$issueNameArr[] = $issueTo = $_POST['issueTo'][$i];
		$fixedByDateArr[] = $fixedByDate = date('Y-m-d', strtotime($_POST['fixedByDate'][$i]));
		$costaAttArr[]  = $costAttribute = $_POST['costAttribute'][$i];		
		$statusArr[] =  $status = $_POST['status'][$i];		
		$costImpactArr[] = $costImpact = $_POST['costImpact'][$i];		
		$costImpactPriceArr[] = $costImpactPrice = $_POST['costImpactPrice'][$i];		

		if ($status == "Closed"){
			$closed_date = date();
		}else{
			$closed_date = '0000-00-00';
		}
		#Get Issued to email address from inspection_issue_to table
		if($issueTo!=''){
			$issuedToFirstname = explode(" (",$issueTo);
			if(!empty($issuedToFirstname[0]) && !empty($projectId)){
				$issuedToQuery = "select issue_to_email from inspection_issue_to where project_id = ".$projectId." and issue_to_name LIKE  '".$issuedToFirstname[0]."' and is_deleted = '0'";
				$query = mysql_query($issuedToQuery);
				if(mysql_num_rows($query) > 0){
					while($rows = mysql_fetch_array($query)){
						$toEmailArr[] = $rows['issue_to_email'];
					}
				}
			}
		}
		#End Issued to email

		if($issue_to_id == '' && $issueTo != ''){
			$insertQryMul = "INSERT INTO issued_to_for_inspections SET
						issued_to_name = '".addslashes($issueTo)."',
						inspection_id = '".$df_id."',
						inspection_fixed_by_date = '".$fixedByDate."',
						last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
						last_modified_date = NOW(),
						created_by = '".$_SESSION['ww_builder']['user_id']."',
						created_date = NOW(),
						original_modified_date = NOW(),
						project_id = '".$projectId."',
						cost_attribute = '".$costAttribute."',
						inspection_status = '".$status."',
						cost_impact_type = '".$costImpact."',
						cost_impact_price = '".$costImpactPrice."',
						closed_date = '".$closed_date."'";
			#echo $insertQryMul.'<br>';
			mysql_query($insertQryMul);
		}else if($issueTo != ''){
             $emailstatus =1;
             $issuedToQuerycheck = "select issued_to_name,inspection_fixed_by_date,cost_attribute,inspection_status,	cost_impact_type,cost_impact_price from issued_to_for_inspections where issued_to_inspections_id = ".$issue_to_id." ";
				$querycheck = mysql_query($issuedToQuerycheck);
				if(mysql_num_rows($querycheck) > 0){
					
					$rowscheck = mysql_fetch_assoc($querycheck);
                    if($rowscheck['inspection_status'] !=$status){
                    	
						//if($rowscheck['inspection_status']=="Fixed"){
						  $emailstatus = 0;	 
						//}
                    }
					
				}
			$updateQryMul = "UPDATE issued_to_for_inspections SET
						issued_to_name = '".addslashes($issueTo)."',
						inspection_fixed_by_date = '".$fixedByDate."',
						last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
						last_modified_date = NOW(),
						original_modified_date = NOW(),
						cost_attribute = '".$costAttribute."',
						inspection_status = '".$status."',
						cost_impact_type = '".$costImpact."',
						cost_impact_price = '".$costImpactPrice."',
						send_email_status = ".$emailstatus.",
						closed_date = '".$closed_date."'
					WHERE
						issued_to_inspections_id = '".$issue_to_id."'";
			#echo $updateQryMul.'<br><br>';
			mysql_query($updateQryMul);
		}		
		if ($status == "Closed"){
			$recodArr['inspection_close'] = array('status' => 'Y', 'resource' => 'Web');
			$imageName = $projectId.'_sign_'.$df_id.'.png';
			copy('./images/master_signoff.png', './inspections/signoff/'.$imageName);
			$secUpdateQRY = "UPDATE project_inspections SET inspection_sign_image = '".$imageName."', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." WHERE inspection_id = ".$df_id." AND project_id = ".$projectId;
			mysql_query($secUpdateQRY);
		}
	}
	
	if(!empty($toEmailArr))	{
		for($i=0; $i<count($toEmailArr); $i++){
            $path = "http://".str_replace("/", "", str_replace("http://", "", DOMAIN));
			$msg = "<br/>Hello, ";
			$msg .= "<p>The inspection has updated.";
			$msg.= "</p><p><b>Project: </b>".$_POST['projectName'];					
			$msg.= "</p><p><b>Location: </b>".$locationTree;
			$msg.= "</p><p><b>Email: </b>".$toEmailArr[$i];
			$msg.= "</p><p><b>Description : </b>".$description;	
			//$msg.= "</p><p><b>Notes : </b>".$note;
			$link ='</p><p><a href="'.$path.'/pms.php?sect=show_defect_photo&id='.base64_encode($df_id).'&byEmail=2">'.$path.'/pms.php?sect=show_defect_photo&id='.base64_encode($df_id).'&byEmail=2</a>';
			$msg.= $link.'</p>';
			$to = $toEmailArr[$i];
			
			$subject = 'DefectID Customer Care';		
			//$emailResponse = $object->sendEmail($to, $subject, $msg);
			if($emailResponse['error']=='false') {
				die($emailResponse['message']);
			}

			#Insert record into table_history_details Table
			$insertRecTableHistory = "INSERT INTO table_history_details SET
													primary_key = '".$df_id."',
													table_name = 'project_inspections',
													sql_operation = 'UPDATE',
													sql_query = '".str_replace("'",'"',$updateQry)."',
													created_by = '".$_SESSION['ww_builder']['user_id']."',
													created_date = NOW(),
													last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
													last_modified_date = NOW(),
													is_deleted = 0,
													project_id = '".$projectId."',
													resource_type = 'Webserver',
													email_id = '".$toEmailArr[$i]."'
													
													";
			//mysql_query($insertRecTableHistory) or die(mysql_error());
							//email_body = '".$msg."'
			#Insert record into table_history_details Table
		}
	}
	
	//Record Array to store data start here
	$recodArr['issued_to_name'] = $issueNameArr;
	$recodArr['inspection_fixed_by_date'] = $fixedByDateArr;
	$recodArr['cost_attribute'] = $costaAttArr;
	$recodArr['inspection_status'] = $statusArr;
	$recodArr['cost_impact_type'] = $costImpactArr;
	$recodArr['cost_impact_price'] = $costImpactPriceArr;
	//Record Array to store data end here
	//Fetch Data for images 		
	if($historyFlag){$imagesArr = array();
		$imagesArr = $object->selQRYMultiple('graphic_type, graphic_name', 'inspection_graphics', 'is_deleted = 0 AND inspection_id = '.$df_id);
		if(!empty($imagesArr)){
			foreach($imagesArr as $key=>$imgArr){
				if($imgArr['graphic_type'] == 'images'){
					$recodArr['image'.++$key] = array('isupdate' => 'NO', 'type' => 'image', 'fileName' => $imgArr['graphic_name']);
				}else{
					$recodArr['image'.++$key] = array('isupdate' => 'NO', 'type' => 'drawing', 'fileName' => $imgArr['graphic_name']);
				}
			}
		}
	}
	//Insert Recored Here#print_r($imagesArr);print_r($recodArr);die;
	if($historyFlag){
	    $insertHistory = "INSERT INTO table_history_details SET
					primary_key = '".$df_id."',
					table_name = 'project_inspections',
					sql_operation = 'UPDATE',
					sql_query = '".serialize($recodArr)."',
					created_by = '".$_SESSION['ww_builder_id']."',
					created_date = NOW(),
					last_modified_by = '".$_SESSION['ww_builder_id']."',
					last_modified_date = NOW(),
					project_id = '".$projectId."'";
		mysql_query($insertHistory);	
	}
	
	if(isset($_SESSION['ww_is_company'])){?>
	<script language="javascript" type="text/javascript">window.location.href="?sect=c_defect&bk=Y&ms=<?=base64_encode('Updated')?>";</script>	
<?php } else {
		if(isset($_SESSION['ww_is_builder'])){?>
			<script language="javascript" type="text/javascript">window.location.href="?sect=i_defect&bk=Y&ms=<?=base64_encode('Updated')?>";</script>	
<?php	}
	}
}

$inspectionDetail = $object->selQRY('inspection_id, project_id, inspection_description, inspection_notes, inspection_raised_by, inspection_inspected_by, location_id, inspection_location', 'project_inspections', 'inspection_id = "'.$df_id.'"and is_deleted = 0');
$projectName = $object->getDataByKey('user_projects', 'project_id', $inspectionDetail['project_id'], 'project_name');

	$issToList = '';?>
<div class="content_center" style="margin-left:70px;margin-top:80px\9;">
	<div class="content_hd" style="background-image:url(images/edit_defect_hd.png);margin: -5px 0 -30px -80px;margin-top:-85px\9;"></div>
	<div class="signin_form1" style="margin-top:15px;margin-top:-25px\9;">
	<?php if($msg != ''){?>
		<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;">
			<div class="success_r" style="height:35px;width:405px;"><p><?php echo $msg; ?></p></div>
		</div>
	<?php }
	$q = "select location_id, location_title from project_locations where project_id = '".$inspectionDetail['project_id']."' and location_parent_id = '0' and is_deleted = '0' order by location_title";
$re = mysql_query($q);
while($rw = mysql_fetch_array($re)){	$val[] = $rw;	}?>
		<form action="" method="post" name="defect_form" id="defect_form">		
		<table width="470" border="0" align="left" cellpadding="0" cellspacing="15" style="border: 1px solid;width: 670px;">
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Project Name</td>
					<td width="312" colspan="2"><input type="text" name="projectName" readonly value="<?=$projectName?>" class="input_small" />
					<input type="hidden" name="projectId" id="projectId" value="<?=$inspectionDetail['project_id']?>"  />
					<? #print_r($_SESSION);?>
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Location</td>
					<td width="312" colspan="2">
					<?php $locations_exists = $object->subLocations($inspectionDetail['location_id'], ' > '); ?>
						<div id="location_exists"><?=stripslashes($locations_exists);?></div>
						<input type="hidden" name="locationTree" id="locationTree" value="<?=stripslashes($locations_exists);?>"  />
<div id="locationsContainer">
<?php $q = "select location_id, location_title from project_locations where project_id = ".$_SESSION['idp']." and location_parent_id = '0' and is_deleted = '0' order by location_title";
	$re = mysql_query($q);
	if(mysql_num_rows($re) > 0){
		echo '<ul class="telefilms">';
		while($locations = mysql_fetch_array($re)){
			echo '<li id="li_'.$locations['location_id'].'">';
			$data = $object->recurtion($locations['location_id'], $_SESSION['idp']);
			if($data!=''){
				echo '<span style="cursor:pointer;" class="jtree-arrow close"><img src="images/plus-icon.png"></span>';
			}
			echo '<span class="jtree-button demo1" id="'.$locations['location_id'].'">'.stripslashes($locations['location_title']).'</span>';
			echo '</li>';
		}
		echo '</ul>';
	}?>
</span>
</div>
<div class="contextMenu" id="myMenu2" style="display:none">
	<ul>
		<li id="select"><img src="images/add.png" align="absmiddle" width="14"  height="14"/> Select</li>
	</ul>
</div>
						<input type="hidden" name="location" id="location" value="<?=$inspectionDetail['location_id']?>" />
						<input type="hidden" name="locationChecklist" id="locationChecklist" value="<?=$lastLocation;?>"  />
					</td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Description<span class="req"></span></td>
					<td width="312" colspan="2"><textarea name="description" id="description" class="text_area_small" cols="33" rows="5" style="width:382px;background-image:url(images/texarea_select_box.png);height:137px;" ><?=$inspectionDetail['inspection_description']?></textarea><div style="position:absolute; margin-left: 420px;margin-top: -150px;"><img id="dropDown" src="images/downbox.png" border="0" style="background-color:none;" /></div>
					<div id="discriptionHide">
<?php //Update Dated 22-11-2012
$_SESSION['description'] = $inspectionDetail['inspection_description'];
$sdName = $object->selQRYMultiple('distinct(description), tag', 'standard_defects', 'project_id = "'.$inspectionDetail['project_id'].'"');
$tagArray = array();
foreach($sdName as $stdDefect){
	$tagData = $stdDefect['description'];
	$tagKey = explode(';', $stdDefect['tag']);
	if($stdDefect['tag'] == ''){
		$tagArray[][$stdDefect['tag']] = $tagData;
	}
	$tagKeyCount = sizeof($tagKey);
	for($i=0; $i<$tagKeyCount; $i++){
		if($tagKey[$i] != ''){
			$tagArray[][$tagKey[$i]] = $tagData;
		}
	}
}
$standardDefects = array();
foreach($tagArray as $tArray){
	$testKey = (string)key($tArray);
	$pos = strpos($locations_exists, $testKey);
	if($pos === false) {}else{
		if(!in_array($tArray[key($tArray)], $standardDefects)){
			$standardDefects[] = $tArray[key($tArray)];
		}
	}
	if(key($tArray) == ''){
		$standardDefects[] = $tArray[key($tArray)];
	}
}
#print_r($imagesData);die; ?>
						<ul id="standardDefect" style="list-style:none;margin-left:-30px;">	
					<?php if(!empty($standardDefects)){
							for($i=0; $i<sizeof($standardDefects); $i++){?>
								<li class="clickableLines" onClick="setDescription(this.innerHTML)"><?php echo $standardDefects[$i];?></li>
					<?php 	}
						}else{?>
							<li class="clickableLines">No One Standard Defect Found !</li>
					<?php }?>
						</ul>
					</div>
					</td>
				</tr>
				<tr 
                
				<?php
                 $_SESSION['note'] = $inspectionDetail['inspection_notes'];

				 if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>
					<td width="134" nowrap="nowrap" valign="top">Notes<span class="req"></span></td>
					<td width="312" colspan="2"><textarea name="note" id="note" class="text_area_small" cols="33" rows="5" style="width:382px;background-image:url(images/texarea_select_box.png);height:137px;"><?=$inspectionDetail['inspection_notes']?></textarea></td>
				</tr>
				<tr>
					<td width="134" nowrap="nowrap" valign="top">Raised By<span class="req">*</span></td>
					<td width="312" colspan="2">
					<? 
                     $_SESSION['raisedBy'] = $inspectionDetail['inspection_raised_by'];
					 if($_SESSION['userRole'] != 'All Defect'){?>
							<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
								<option value="">Select</option>
								<option value="<?=$_SESSION['userRole']?>" selected="selected" ><?=$_SESSION['userRole']?></option>
							</select>
						<?php }else{ ?>
							<select name="raisedBy" id="raisedBy" class="select_box" style="width:220px; background-image:url(images/selectSpl.png);margin-left:0px;">
								<option value="">Select</option>
								<option value="Builder" <?php if($inspectionDetail['inspection_raised_by']  == 'Builder'){ echo 'selected="selected"';}else{ if($inspectionDetail['inspection_raised_by'] == ''){ echo 'selected="selected"'; } }?> >Builder</option>
								<option value="Architect" <?php if($inspectionDetail['inspection_raised_by']  == 'Architect'){ echo 'selected="selected"';}?>>Architect</option>
								<option value="Structural Engineer" <?php if($inspectionDetail['inspection_raised_by']  == 'Structural Engineer'){ echo 'selected="selected"';}?>>Structural Engineer</option>
								<option value="Services Engineer" <?php if($inspectionDetail['inspection_raised_by']  == 'Services Engineer'){ echo 'selected="selected"';}?>>Services Engineer</option>
								<option value="Superintendant" <?php if($inspectionDetail['inspection_raised_by']  == 'Superintendant'){ echo 'selected="selected"';}?>>Superintendant</option>
								<option value="General Consultant" <?php if($inspectionDetail['inspection_raised_by']  == 'General Consultant'){ echo 'selected="selected"';}?>>General Consultant</option>
								<option value="Client" <?php if($inspectionDetail['inspection_raised_by']  == 'Client'){ echo 'selected="selected"';}?>>Client</option>
								<option value="Purchaser" <?php if($inspectionDetail['inspection_raised_by'] == 'Client'){ echo 'selected="selected"';}?>>Purchaser</option>
								<option value="Sub Contractor" <?php if($inspectionDetail['inspection_raised_by'] == 'Sub Contractor'){ echo 'selected="selected"';}?> >Sub Contractor</option>
							</select>						
						<?php } ?>
					</td>
				</tr>
				<tr>
					<?php $_SESSION['inspection_inspected_by'] = $inspectionDetail['inspection_inspected_by'];?>					

					<td width="134" nowrap="nowrap" valign="top">Inspected By</td>
					<td width="312" colspan="2">
						<input type="text" readonly value="<?=stripslashes($inspectionDetail['inspection_inspected_by']);?>" class="input_small"  />
					</td>
				</tr>
				 <tr>
					<td colspan="3">
					<?php
					$image1='';$image2='';$draw='';
					$images = $object->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$df_id.' AND is_deleted="0" AND graphic_type = "images" ORDER BY last_modified_date DESC LIMIT 0, 2');
								//print_r($images);
					$drawing = $object->selQRYMultiple('graphic_name', 'inspection_graphics', 'inspection_id = '.$df_id.' AND is_deleted = 0 AND graphic_type="drawing" and graphic_name!="" ORDER BY last_modified_date DESC LIMIT 0, 1');
					
						if($images[0]['graphic_name'] != ''){
							if($images[0]['graphic_name'] != ''){
								if(file_exists('inspections/photo/'.$images[0]['graphic_name'])){
									$image1 = 'inspections/photo/'.$images[0]['graphic_name'];
								}
							}
							if($images[1]['graphic_name'] != ''){
								if(file_exists('inspections/photo/'.$images[1]['graphic_name'])){
									$image2 = 'inspections/photo/'.$images[1]['graphic_name'];
								}
							}
						}
						if($drawing[0]['graphic_name'] != ''){
							if($drawing[0]['graphic_name'] != ''){
								if(file_exists('inspections/drawing/'.$drawing[0]['graphic_name'])){
									$draw = 'inspections/drawing/'.$drawing[0]['graphic_name'];
								}
							}
						}?>
						<div id="attachments" style="overflow:hidden;">
							<!--span style="margin-left:15px;float:left;">Attachment</span><br /-->
<?php $inspGraph['images']=isset($inspGraph['images'])?$inspGraph['images']:0;
$totalImages = count($inspGraph['images']); ?>                            
                            
                            <table width="" border="0" align="left" <?php echo ($_SESSION['ww_builder']['sub_user_type']=='Sub Contractor')?'style="visibility:hidden; "':''; ?>>
  <tr>
    <td width="170px" align="left" valign="middle">Attachment</td>
    <td width="150px" align="left" valign="middle"><div id="editImage1" style="" >
            <!--div id='injection_site'></div-->
            <?php if(!empty($images[0]['graphic_name']) && $_SESSION['userRole'] != 'Sub Contractor'){?>
	            <input type="image" onclick="return launchEditor('photoImage1', 'inspections/photo/<?php echo $images[0]['graphic_name'];?>');" value="Edit photo" src="images/markup.png">
            <?php }?>
            </div></td>
    <td width="150px" align="left" valign="middle" style=" <?php echo ($_SESSION['ww_builder']['user_type']=='inspector')?'display:none;':''; ?>">			<div id="editImage2" style="" >
            <?php if(!empty($images[1]['graphic_name']) && $_SESSION['userRole'] != 'Sub Contractor'){?>
  	            <input type="image" onclick="return launchEditor('photoImage2', 'inspections/photo/<?php echo $images[1]['graphic_name'];?>');" value="Edit photo" src="images/markup.png">
            <?php }?>
            <!--div id='injection_site'></div-->
            </div></td>
    <td width="150px" align="left" valign="middle"><div id="editDrawing" style="" >
			<?php if(!empty($drawing[0]['graphic_name']) && $_SESSION['userRole'] != 'Sub Contractor'){ ?>
   	            <input type="image" onclick="return launchEditor('drawImage1', 'inspections/drawing/<?php echo $drawing[0]['graphic_name'];?>');" value="Edit photo" src="images/add_sketch.png">
            <?php }else{
					if($_SESSION['userRole'] != 'Sub Contractor'){
				?>
             <input type="image" onclick="return launchEditor('drawImage1', 'inspections/drawing/default_draw_sketch.png');" value="Edit photo" src="images/add_sketch.png">
            <?php
					}
				}?>
            <!--div id='injection_site'></div-->
            </div></td>
  </tr>
</table>
		<div class="innerDiv"  style="margin-left:160px;" align="center" >
		<?php // echo $_SESSION['userRole'];?>
			<div style="height:120px;">
				<label class="filebutton" align="center">
				&nbsp;Browse Image 1
				<?php if($_SESSION['userRole']!='Sub Contractor'){?>
					<input type="file" id="image1" name="image1" onchange="uploadImage(this.id)" style="width:100px; height:100px;"/>
				<?php } ?>
				</label>
				<div id="response_image_1" style="width:120px;">
					<?php if($image1 != ''){?>
						<img src="<?=$image1?>" width="100" id="photoImage1" />
						<input type="hidden" name="photo[]" value="<?=$images[0]['graphic_name']?>"  />
					<?php }?>
				</div>
			</div>
			<img src="images/remove_img_btn.png" style="margin-top:10px;cursor:pointer; <?php echo ($_SESSION['userRole']=='Sub Contractor')?'visibility:hidden;':''; ?>" onclick="removeImages('response_image_1');" />
		</div>
		
		<div class="innerDiv"  align="center">
			<div style="height:120px;">
				<label class="filebutton" align="center">
				Browse Image 2
					<input type="file" id="image2" name="image2" onchange="uploadImage(this.id)" style="width:100px; height:100px;" />
				</label>
				<div id="response_image_2" style="width:120px;">
					<?php if($image2 != ''){?>
						<img src="<?=$image2?>" width="100" id="photoImage2" />
						<input type="hidden" name="photo[]" value="<?=$images[1]['graphic_name']?>"  />
					<?php }?>
				</div>
			</div>
			<img src="images/remove_img_btn.png" style="margin-top:10px;cursor:pointer;" onclick="removeImages('response_image_2');" />
		</div>
		
		<div class="innerDiv" align="center">
			<div style="height:120px;">
				<label class="filebutton" align="center">
					Add Sketch
					<?php if($_SESSION['userRole']!='Sub Contractor'){?>
					<input type="file" id="drawing" name="drawing" onchange="uploadImage(this.id)" style="width:100px; height:100px;" <?php echo ($_SESSION['userRole']=='Sub Contractor')?'disabled="disabled"':''; ?>/>
					<?php } ?>
				</label>
				<div id="response_drawing" style="width:120px;">
					<?php if($draw != ''){?>
						<img src="<?=$draw?>" width="100" id="drawImage1" />
						<input type="hidden" id="drawing" name="drawing" value="<?=$drawing[0]['graphic_name']?>"  />
					<?php }else{?>
                    <img width="100" height="90" style="margin-left:10px;margin-top:8px;" src="inspections/drawing/default_draw_sketch.png" id="drawImage1"><input type="hidden" value="" name="drawing" id="newDrawing">
					<?php } ?>                    
				</div>
			</div>
			<!--<img src="images/remove_img_btn.png" style="margin-top:10px;cursor:pointer; <?php // echo ($_SESSION['userRole']=='Sub Contractor')?'visibility:hidden;':''; ?>" onclick="removeImages('response_drawing');" />-->
		</div>
					</div>
					</td>
				</tr>
				<tr>
					<td colspan="3" nowrap="nowrap" valign="top">Issued To Detail</td>
				</tr>
				<tr>
                    <td colspan="3">
						<table width="70%" border="0" cellspacing="5" cellpadding="5">
							<tr>
								<?php if($_SESSION['userRole'] != 'Sub Contractor'){?>
									<td>&nbsp;</td>
								<?php }?>
								<td>Issued&nbsp;To</td>
								<td>Fix&nbsp;By&nbsp;Date</td>
								<td <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>Cost Attribute</td>
								<td>Status</td>
								<td <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>Cost Impact</td>
								<td <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>Cost Impact Price (In&nbsp;$)</td>
							</tr>
<?php 
//Update Dated 22-11-2012
$issueToName = $object->selQRYMultiple('issue_to_name, company_name, tag', 'inspection_issue_to', 'project_id = "'.$inspectionDetail['project_id'].'" and is_deleted=0 ORDER BY issue_to_name');
$issueTagArray = array();
foreach($issueToName as $issueName){
	if($issueName['company_name'] != ""){
		$issueTagData = $issueName['issue_to_name']." (".$issueName['company_name'].")";
	}else{
		$issueTagData = $issueName['issue_to_name']." (NA)";
	}
	
	$issueTagKey = explode(';', $issueName['tag']);
	if($issueName['tag'] == ''){
		$issueTagArray[][$issueName['tag']] = $issueTagData;
	}
	$issueTagKeyCount = sizeof($issueTagKey);
	for($i=0; $i<$issueTagKeyCount; $i++){
		if($issueTagKey[$i] != ''){
			$issueTagArray[][$issueTagKey[$i]] = $issueTagData;
		}
	}
}
$issueToSelect = array();
foreach($issueTagArray as $issueTArray){
	$testKeyIssue = (string)key($issueTArray);
	$pos = strpos($locations_exists, $testKeyIssue);
	if($pos === false) {}else{
		if(!in_array($issueTArray[key($issueTArray)], $standardDefects)){
			$issueToSelect[] = $issueTArray[key($issueTArray)];
		}
	}
	if(key($issueTArray) == ''){
		$issueToSelect[] = $issueTArray[key($issueTArray)];
	}
}
$issueToSelect = array_map('trim', $issueToSelect);

$issueToData = $object->selQRYMultiple('issued_to_inspections_id, issued_to_name, inspection_fixed_by_date, cost_attribute, inspection_status, cost_impact_type, cost_impact_price', 'issued_to_for_inspections', 'inspection_id = '.$df_id.' and is_deleted=0  group by issued_to_name order by issued_to_name');

$i=0;
$issueToRedundent = '';
foreach($issueToData as $issueTo){
	if($issueToRedundent == ''){
		$issueToRedundent .= '"'.$issueTo['issued_to_name'].'"';
	}else{
		$issueToRedundent .= ', "'.$issueTo['issued_to_name'].'"';
	}
}
#echo '<pre>';print_r($issueToSelect);print_r($issueToData);die;
	if(!empty($issueToData)){
		$sizePlus = sizeof($issueToData);
		for($m=0;$m<=2;$m++){$i++;
			$currentIssueToName = $issueToData[$m]['issued_to_name'];
			$inspectionFixedByDate = $issueToData[$m]['inspection_fixed_by_date']; 
			$inspectionStatus = $issueToData[$m]['inspection_status']; 
			$costAttribute = $issueToData[$m]['cost_attribute']; 
			$costImpactType = $issueToData[$m]['cost_impact_type']; 
			$costImpactPrice = $issueToData[$m]['cost_impact_price']; ?>

<?php		if($_SESSION['userRole'] == 'Sub Contractor'){?>

		<tr id="hide_<?=$m;?>" <?php if(!isset($issueToData[$m]['issued_to_inspections_id'])){echo 'style="display:none;"';}?>
		<?php if($_SESSION['userRole'] == 'Sub Contractor'){
			$issueToNameKey = "";
			if(substr_count($currentIssueToName, ' (') > 1){
				$isArr = explode(' (', $currentIssueToName);
				$rowName = array_pop($isArr);
				$isArr2Name = implode(' (', $isArr);
				$issueToNameKey = $isArr2Name;
				$remainName = trim($rowName, '(');
				$remIssseArr = explode(')', $remainName);
				$contactPersonName = $remIssseArr[0];
			}else{
				$isArr = explode(' (', $currentIssueToName);
				$remainName = trim($isArr[1], '(');
				$remIssseArr = explode(')', $remainName);
				$contactPersonName = $remIssseArr[0];
				$issueToNameKey = $isArr[0];	
			}
			
			if(trim($issueToNameKey) == trim($_SESSION['userIssueTo'])){echo 'style="display:table-row;"';}else{echo 'style="display:none;"';}
		}?>>
			<td width="33%" style="text-shadow:none;">
				<input type="hidden" name="issue_to_id[]" id="issue_to_id_<?php echo $i;?>" value="<?php echo $issueToData[$m]['issued_to_inspections_id'];?>" />
		<?php if(in_array($currentIssueToName, $issueToSelect)){?>
				<select name="disIssueTo" type="text" id="autocomplete_<?php echo $i;?>" class="issueTo" style="width:120px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;" disabled="disabled" >	
					<option value="">-- Select --</option>
				<?php for($k=0;$k<sizeof($issueToSelect);$k++){ ?>
					<option value="<?php echo $cValue = trim(stripslashes($issueToSelect[$k]))?>"<?php if($cValue == $currentIssueToName){echo 'selected="selected"'; #unset($issueToSelect[$k]);
					 }?>><?=$cValue?></option>		
				<?php }?>
				</select>
				<input name="issueTo[]" type="text" id="autocomplete_<?php echo $i;?>" class="issueTo" style="display:none;" value="<?=$currentIssueToName;?>" >	
		<?php }else{?>
				<select name="issueTo[]" type="text" id="autocomplete_<?php echo $i;?>" class="issueTo" style="width:120px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;" disabled="disabled">	
					<option value="">-- Select --</option>
				<?php for($k=0;$k<sizeof($issueToSelect);$k++){?>
					<option  value="<?php echo $cValue = trim(stripslashes($issueToSelect[$k]))?>"><?=$cValue?></option>		
				<?php #if($cValue == 'NA' && !isset($issueToData[$m]['issued_to_inspections_id'])){ echo 'selected="selected"';}?> 				<?php }
				 if(isset($issueToData[$m]['issued_to_inspections_id'])){?>
					<option value="<?=trim(stripslashes($currentIssueToName));?>" selected="selected" ><?=trim(stripslashes($currentIssueToName))?></option> 				 
				 <?php }?>
				</select>
				<?php if(isset($issueToData[$m]['issued_to_inspections_id'])){?>
					<input name="issueTo[]" type="text" id="autocomplete_<?php echo $i;?>" class="issueTo" style="display:none;" value="<?=$currentIssueToName;?>" >		
				 <?php }?>
		<?php }?>
			</td>
			<td width="33%" style="text-shadow:none;" align="center">
				<?php 
				if(isset($issueToData[$m]['issued_to_inspections_id'])){
					if($inspectionFixedByDate != '0000-00-00'){
						$fixedByDate = date('d-m-Y', strtotime($inspectionFixedByDate));
					}else{
						$fixedByDate = '';
					}
				}else{
					$fixedByDate = date('d-m-Y');
				}?>
				<input name="disFixdate" id="" class="fixedByDate" readonly size="10" value="<?php echo $fixedByDate;?>" disabled="disabled" />
				<input name="fixedByDate[]" id="fixedByDate_<?php echo $i;?>" class="fixedByDate" readonly size="10" value="<?php echo $fixedByDate;?>" style="display:none;" />
			</td>
			<td style="text-shadow:none;<?php if($_SESSION['userRole'] == 'Sub Contractor') echo "display:none;"?>" align="center">
				<select name="costAttribute[]" type="text" id="costAttribute_<?php echo $i;?>" class="issueTo" style="width:120px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">	
					<?php $costAttributeArr = array('None', 'Backcharge', 'Variation');
					foreach($costAttributeArr as $key=>$cosArr){?>
						<option <?php if($costAttribute == $cosArr){ echo 'selected="selected"';}?> value="<?=$cosArr?>"><?=$cosArr?></option>
					<?php }?>
				</select>
			</td>
			<td style="text-shadow:none;" align="center">
				<select name="status[]" id="status_<?php echo $i;?>" class="status" style="width:90px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">
					<?php $statusArr = array('Open', 'Pending', 'Fixed', 'Closed');
					if($_SESSION['userRole'] == 'Sub Contractor') 
						$statusArr = array('Open', 'Pending', 'Fixed');
				if($inspectionStatus != 'Closed'){
					foreach($statusArr as $key=>$stArr){?>
						<option <?php if($inspectionStatus == $stArr){ echo 'selected="selected"';}?> value="<?=$stArr?>"><?=$stArr?></option>
					<?php }
				}else{?>
					<option value="Closed">Closed</option>	
				<?php }?>
				</select>
			</td>
			<td <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>
				<select name="costImpact[]" id="costImpact_<?php echo $i;?>" class="status" style="width:80px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">
			<?php if($costAttribute == 'None'){?>
					<option <?php if($costImpactType == 'None'){ echo 'selected="selected"';}?> value="None">None</option>
			<?php }else{?>
					<option <?php if($costImpactType == 'None'){ echo 'selected="selected"';}?> value="None">None</option>
					<option <?php if($costImpactType == 'Low'){ echo 'selected="selected"';}?> value="Low">Low</option>
					<option <?php if($costImpactType == 'Medium'){ echo 'selected="selected"';}?> value="Medium">Medium</option>
					<option <?php if($costImpactType == 'High'){ echo 'selected="selected"';}?> value="High">High</option>
			<?php }?>
				</select>
			</td>
			<td <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>
				<input name="costImpactPrice[]" id="costImpactPrice_<?php echo $i;?>" class="fixedByDate" size="5" value="<?php echo $costImpactPrice;?>" onkeypress="return checkNo(event, 'costImpact_<?php echo $i;?>', this.id, this.value);" <?php if($costAttribute == 'None'){echo 'disabled="disabled"';}?> />
			</td>
		</tr>
		
<?php		}else{?>
		
		<tr id="hide_<?=$m;?>" <?php if(!isset($issueToData[$m]['issued_to_inspections_id'])){echo 'style="display:none;"';}?>>
			<td style="text-shadow:none;padding:5px;">
			<?php if($sizePlus == 3){}else{
				 if($m == ($sizePlus-1) && $m != 2){?>
					<img style="cursor:pointer;" onclick="AddItem();" src="images/inspectin_add.png" align="absmiddle" />
			<?php }else if(!isset($issueToData[$m]['issued_to_inspections_id'])){?>
					<img style="cursor:pointer;" onclick="removeElement('hide_<?=$m;?>');" src="images/inspectin_delete.png" align="absmiddle" />
			<?php } 
			}?>
			</td>
			<td width="33%" style="text-shadow:none;">
				<input type="hidden" name="issue_to_id[]" id="issue_to_id_<?php echo $i;?>" value="<?php echo $issueToData[$m]['issued_to_inspections_id'];?>" />
		<?php if(in_array($currentIssueToName, $issueToSelect)){?>
				<select name="issueTo[]" type="text" id="autocomplete_<?php echo $i;?>" class="issueTo" style="width:120px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">	
					<option value="">-- Select --</option>
				<?php for($k=0;$k<sizeof($issueToSelect);$k++){ ?>
					<option value="<?php echo $cValue = trim(stripslashes($issueToSelect[$k]))?>"<?php if($cValue == $currentIssueToName){echo 'selected="selected"'; #unset($issueToSelect[$k]);
					 }?>><?=$cValue?></option>		
				<?php }?>
				</select>
		<?php }else{?>
				<select name="issueTo[]" type="text" id="autocomplete_<?php echo $i;?>" class="issueTo" style="width:120px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">	
					<option value="">-- Select --</option>
				<?php for($k=0;$k<sizeof($issueToSelect);$k++){?>
					<option  value="<?php echo $cValue = trim(stripslashes($issueToSelect[$k]))?>"><?=$cValue?></option>		
				<?php #if($cValue == 'NA' && !isset($issueToData[$m]['issued_to_inspections_id'])){ echo 'selected="selected"';}?> 				<?php }
				 if(isset($issueToData[$m]['issued_to_inspections_id'])){?>
					<option value="<?=trim(stripslashes($currentIssueToName));?>" selected="selected" ><?=trim(stripslashes($currentIssueToName))?></option> 				 
				 <?php }?>
				</select>
		<?php }?>
			</td>
			<td width="33%" style="text-shadow:none;" align="center">
				<?php 
				if(isset($issueToData[$m]['issued_to_inspections_id'])){
					if($inspectionFixedByDate != '0000-00-00'){
						$fixedByDate = date('d-m-Y', strtotime($inspectionFixedByDate));
					}else{
						$fixedByDate = '';
					}
				}else{
					$fixedByDate = date('d-m-Y');
				}?>
				<input name="fixedByDate[]" id="fixedByDate_<?php echo $i;?>" class="fixedByDate" readonly size="10" value="<?php echo $fixedByDate;?>" />
			</td>
			<td style="text-shadow:none;<?php if($_SESSION['userRole'] == 'Sub Contractor') echo "display:none;"?>" align="center">
				<select name="costAttribute[]" type="text" id="costAttribute_<?php echo $i;?>" class="issueTo" style="width:120px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">	
					<?php $costAttributeArr = array('None', 'Backcharge', 'Variation');
					foreach($costAttributeArr as $key=>$cosArr){?>
						<option <?php if($costAttribute == $cosArr){ echo 'selected="selected"';}?> value="<?=$cosArr?>"><?=$cosArr?></option>
					<?php }?>
				</select>
			</td>
			<td style="text-shadow:none;" align="center">
				<select name="status[]" id="status_<?php echo $i;?>" class="status" style="width:90px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;	-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">
					<?php $statusArr = array('Open', 'Pending', 'Fixed', 'Closed');
					foreach($statusArr as $key=>$stArr){?>
						<option <?php if($inspectionStatus == $stArr){ echo 'selected="selected"';}?> value="<?=$stArr?>"><?=$stArr?></option>
					<?php }?>
				</select>
			</td>
			<td <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>
				<select name="costImpact[]" id="costImpact_<?php echo $i;?>" class="status" style="width:80px;background-color:#FFFFFF;background-repeat: no-repeat;color: #333;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 14px;height: 25px;border-radius:5px;-moz-border-radius:5px;	-webkit-border-radius: 5px;border-color: #FFFFFF;">
			<?php if($costAttribute == 'None'){?>
					<option <?php if($costImpactType == 'None'){ echo 'selected="selected"';}?> value="None">None</option>
			<?php }else{?>
					<option <?php if($costImpactType == 'None'){ echo 'selected="selected"';}?> value="None">None</option>
					<option <?php if($costImpactType == 'Low'){ echo 'selected="selected"';}?> value="Low">Low</option>
					<option <?php if($costImpactType == 'Medium'){ echo 'selected="selected"';}?> value="Medium">Medium</option>
					<option <?php if($costImpactType == 'High'){ echo 'selected="selected"';}?> value="High">High</option>
			<?php }?>
				</select>
			</td>
			<td <?php if($_SESSION['userRole'] == 'Sub Contractor') echo "style='display:none';"?>>
				<input name="costImpactPrice[]" id="costImpactPrice_<?php echo $i;?>" class="fixedByDate" size="5" value="<?php echo $costImpactPrice;?>" onkeypress="return checkNo(event, 'costImpact_<?php echo $i;?>', this.id, this.value);" <?php if($costAttribute == 'None'){echo 'disabled="disabled"';}?> />
			</td>
		</tr>
		
<?php 		}?>
		
	<?php }
	}else{ ?><tr><td colspan="7"><em>No One Issue to Found</em></td><tr><?php }?>
</table>
					</td>
				</tr>
				<tr><td colspan="3" >&nbsp;</td></tr>
				<tr>
					<td>
						<input name="backButton" type="button" class="green_small" id="backButton" value="Back" onclick="javascript:history.back();"  />
					</td>
					<td align="center">
<?php if($_SESSION['web_delete_inspection'] == 1){?>
						<input name="remove" type="button" class="green_small" value="Remove" id="remove" onclick="checkReturn();" />
<?php }?>
					</td>
					<td>
						<input type="hidden" value="<?=$df_id?>" name="df_id" id="df_id"  />
						<input name="button" type="submit" class="green_small" id="button" value="Submit" />
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>
<script type="text/javascript" src="js/jquery.contextmenu.r2.js"></script>
<link href="css/jquery-ui.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.tree_edit_inspections.js?1"></script>	
<script>
<?php if($_SESSION['web_close_inspection'] != 1){?>
$(".status").change(function(){
	if($(this).val() == 'Closed'){
		jAlert('You can\'t closed any inspection from here !');
		$(this).val('Open');
	}
});
<?php }?>
$("#autocomplete_1").change(function(){
	var arr = [ <?=$issueToRedundent;?> ];
	var valueIssueTo_1 = $("#autocomplete_1").val();
	var check = $.inArray(valueIssueTo_1, arr);
	if(check != (-1)){
		if(valueIssueTo_1 == arr[0]){}else{
			jAlert(valueIssueTo_1+' Already Selected !');
			$("#autocomplete_1").val(arr[0]);
		}
	}
});
$("#autocomplete_2").change(function(){
	var arr = [ <?=$issueToRedundent;?> ];
	var valueIssueTo_2 = $("#autocomplete_2").val();
	var check = $.inArray(valueIssueTo_2, arr);
	if(check != (-1)){
		if(valueIssueTo_2 == arr[1]){}else{
			jAlert(valueIssueTo_2+' Already Selected !');
			$("#autocomplete_2").val(arr[1]);
		}
	}
});
$("#autocomplete_3").change(function(){
	var arr = [ <?=$issueToRedundent;?> ];
	var valueIssueTo_3 = $("#autocomplete_3").val();
	var check = $.inArray(valueIssueTo_3, arr);
	if(check != (-1)){
		if(valueIssueTo_3 == arr[2]){}else{
			jAlert(valueIssueTo_3+' Already Selected !');
			$("#autocomplete_3").val(arr[2]);
		}
	}
});
$("#autocomplete_4").change(function(){
	var arr = [ <?=$issueToRedundent;?> ];
	var valueIssueTo_4 = $("#autocomplete_4").val();
	var check = $.inArray(valueIssueTo_4, arr);
	if(check != (-1)){
		if(valueIssueTo_4 == arr[3]){}else{
			jAlert(valueIssueTo_4+' Already Selected !');
			$("#autocomplete_4").val(arr[3]);		
		}
	}
});




$("#1autocomplete_1").change(function(){
	var arr = [ <?=$backissueToRedundent;?> ];
	var valueIssueTo_1 = $("#1autocomplete_1").val();
	var check = $.inArray(valueIssueTo_1, arr);
	if(check != (-1)){
		if(valueIssueTo_1 == arr[0]){}else{
			jAlert(valueIssueTo_1+' Already Selected !');
			$("#1autocomplete_1").val(arr[0]);
		}
	}
});
$("#2autocomplete_2").change(function(){
	var arr = [ <?=$backissueToRedundent;?> ];
	var valueIssueTo_2 = $("#2autocomplete_2").val();
	var check = $.inArray(valueIssueTo_2, arr);
	if(check != (-1)){
		if(valueIssueTo_2 == arr[1]){}else{
			jAlert(valueIssueTo_2+' Already Selected !');
			$("#2autocomplete_2").val(arr[1]);
		}
	}
});
$("#3autocomplete_3").change(function(){
	var arr = [ <?=$backissueToRedundent;?> ];
	var valueIssueTo_3 = $("#autocomplete_3").val();
	var check = $.inArray(valueIssueTo_3, arr);
	if(check != (-1)){
		if(valueIssueTo_3 == arr[2]){}else{
			jAlert(valueIssueTo_3+' Already Selected !');
			$("#autocomplete_3").val(arr[2]);
		}
	}
});
$("#4autocomplete_4").change(function(){
	var arr = [ <?=$backissueToRedundent;?> ];
	var valueIssueTo_4 = $("#4autocomplete_4").val();
	var check = $.inArray(valueIssueTo_4, arr);
	if(check != (-1)){
		if(valueIssueTo_4 == arr[3]){}else{
			jAlert(valueIssueTo_4+' Already Selected !');
			$("#4autocomplete_4").val(arr[3]);		
		}
	}
});








$(".status").change(function(){
	if($(this).val() == 'Draft'){
		jAlert('Sorry! check list is not completed yet, Inspection is Under drafting stage.');
		$(this).val('Draft');
	}
});

function checkReturn(){
	var r = jConfirm('Do you want to delete Inspection ?', null, function(r){
		if (r==true){
			var projId = document.getElementById('df_id');
			projId.name = 'removeProject';
			document.forms['defect_form'].submit();
		}
	});
}
var align = 'center';
var top = 100;
var width = 670;
var padding = 10;
var backgroundColor = '#FFFFFF';
var borderColor = '#333333';
var borderWeight = 4;
var borderRadius = 5;
var fadeOutTime = 300;
var disableColor = '#666666';
var disableOpacity = 40;
var loadingImage = 'images/loadingAnimation.gif';
var copyStatus = false;
var copyId = '';
var statusLoop = <?=$i;?>;

//Default Issue to array
<?php for($g=1; $g <= $i; $g++){ ?>
	var issueTo_<?=$g?> = '';
	var issueTo_selectd_<?=$g?> = $('#autocomplete_<?=$g?>').val();
	$('#autocomplete_<?=$g?> option').each(function(){
		if(this.value != 0){
			if(issueTo_<?=$g?> == ''){
				issueTo_<?=$g?> = this.value;
			}else{
				issueTo_<?=$g?> += ','+this.value;
			}
		}
	});
	$('#costImpact_<?=$g?>').change(function(){
		var priceVal = this.value;
		var costAttr = $('#costAttribute_<?=$g?>').val();
		if(costAttr == 'None'){
			jAlert('Sorry! Please select any other cost attribute type first, for editing this Issued To cost impact type.', 'Permission Alert');
			$('#costImpact_<?=$g?>').val('None');
			return false;
		}else{
			if(priceVal == 'None'){
				$('#costImpactPrice_<?=$g?>').val('0.00');
			}
			if(priceVal == 'Low'){
				$('#costImpactPrice_<?=$g?>').val('100.00');
			}
			if(priceVal == 'Medium'){
				$('#costImpactPrice_<?=$g?>').val('1000.00');
			}
			if(priceVal == 'High'){
				$('#costImpactPrice_<?=$g?>').val('10000.00');
			}
		}
	});
	
	$('#costAttribute_<?=$g?>').change(function(){
		if(this.value == 'None'){
			$('#costImpact_<?=$g?>').html('<option value="None">None</option>');
			$('#costImpactPrice_<?=$g?>').val('0.00');
			$('#costImpactPrice_<?=$g?>').attr('disabled', true);
		}else{
			$('#costImpactPrice_<?=$g?>').attr('disabled', false);
			$('#costImpact_<?=$g?>').html('<option value="None">None</option><option value="Low">Low</option><option value="Medium">Medium</option><option value="High">High</option>');
		}
	});
<?php }?>
//Default Issue to array
function checkNo(evt, alertID, obj, objVal){
	var charCode = (evt.which) ? evt.which : event.keyCode;
	if(charCode == 46){
		return true;
	}
	if (charCode > 31 && (charCode < 48 || charCode > 57)){
		return false;
	}
	if(objVal.length >= 11){
		if(objVal > 10000000){
			document.getElementById(obj).value = '10000000.00';
			jAlert("You can't enter more than 10000000 value");
			return false;
		}
	}
	return true;
}

function taggingIssueTo(newTree){
	if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	params = "LocationSearchString="+newTree+"&projectID=<?=$inspectionDetail['project_id'];?>&uniqueId="+Math.random();
	xmlhttp.open("POST", "tagedIssuetTo.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200){
			if(xmlhttp.responseText != ''){
				var resString = xmlhttp.responseText;
				var resSplitResult = resString.split("@@@");
				<?php for($g=1; $g <= $i; $g++){ ?>
//					resSplitResult = resSplitResult.toString(); 
	//				uniqueIssueToArray = resSplitResult.split(",");
					var issueToOption = '<option value="">-- Select --</option><option value="NA">NA</option>';
					for(i = 0; i < resSplitResult.length; i++){
						resSplitResult[i] = jQuery.trim(resSplitResult[i]);
						var tempString = '<option value="'+resSplitResult[i]+'"';
						if(issueTo_selectd_<?=$g?> == resSplitResult[i]){
							tempString += 'selected="selected"';
						}
						tempString += '>'+resSplitResult[i]+'</option>'; 
						issueToOption += tempString;
					}
					<?php if($_SESSION['userRole'] == 'Manager') { ?>
						document.getElementById('autocomplete_<?=$g?>').innerHTML = issueToOption;
					<?php } ?>
				<?php }?>
			}
			taggingStandardDefect(newTree);
		}
	}
	xmlhttp.send(params);
}

function taggingStandardDefect(newTree){
	if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	params = "LocationSearchString="+newTree+"&projectID=<?=$inspectionDetail['project_id'];?>&uniqueId="+Math.random();
	xmlhttp.open("POST", "tagedStandardDefect.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			if(xmlhttp.responseText != ''){
				var resString = xmlhttp.responseText;
				document.getElementById('standardDefect').innerHTML = resString;
			}
		}
	}
	xmlhttp.send(params);
}

var previousLocId = '';

$(document).ready(function() {
	$('ul.telefilms').tree({default_expanded_paths_string : '0/0/0,0/0/2,0/2/4'});
});
$('span.demo1').contextMenu('myMenu2', {
	bindings: {
		'select': function(t) {
			if(previousLocId != ''){
				$(previousLocId).css({ 'font-weight' :'normal', 'font-style':'normal', 'text-decoration':'none' });
			}
			$(t).css({ 'font-weight' :'bold', 'font-style':'italic', 'text-decoration':'underline' });
			previousLocId = t;
			$("#location").val(t.id);
			$("#locationChecklist").val(document.getElementById(t.id).innerHTML);
			try{
				if(window.XMLHttpRequest){
					xmlhttp=new XMLHttpRequest();
				}else{
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				showProgress();
				params = "locationId="+t.id+"&uniqueId="+Math.random();
				xmlhttp.open("POST", "reloadLocationExpand.php", true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.setRequestHeader("Content-length", params.length);
				xmlhttp.setRequestHeader("Connection", "close");
				xmlhttp.onreadystatechange=function(){
					if (xmlhttp.readyState==4 && xmlhttp.status==200){
						hideProgress();
						var newTree = xmlhttp.responseText;
						$('#location_exists').html(newTree);
						$('#locationTree').val(newTree);
						taggingIssueTo(newTree);
					}
				}
				xmlhttp.send(params);
			}catch(e){
			//	alert(e.message); 
			}
		}
	}
	});

var elementCount = 0;
var addedRow = new Array(); 
var existRow = new Array(); 
<?php for($l=0;$l<$sizePlus;$l++){?>
existRow[<?=$l;?>] = <?=$l;?>;
<?php }?>
//Add issue to row
function AddItem() {
	if(addedRow.length < 2){
		addedRow.push(++elementCount);
		if(document.getElementById('hide_'+elementCount).style.display == 'none'){
			document.getElementById('hide_'+elementCount).style.display = 'table-row';
		}else{
			if(elementCount == 1){
				document.getElementById('hide_2').style.display = 'table-row';
			}else{
				document.getElementById('hide_1').style.display = 'table-row';
			}
		}
	}else{
		jAlert("You can't add more than 3 Issue To !");
	}
}
//Remove issue to row
function removeElement(removeID){
	var r = jConfirm('Do you want to delete Issue To ?', null, function(r){
		if (r==true){
			--elementCount;
			document.getElementById(removeID).style.display = 'none';
			var idarr = removeID.split('_');
			$('#issueTo_'+idarr[1]).show();
			$('#issueTo_'+idarr[1]).val('NA');
			$('#otherIssueTo'+idarr[1]).hide();
			addedRow.pop();
		}
	});
}

var unique = function(origArr) {
	var issueToArray = new Array();
	issueToArray = origArr.split(',');
	var newArr = [], origLen = issueToArray.length, found, x, y;
    for ( x = 0; x < origLen; x++ ) {
        found = undefined;
        for ( y = 0; y < newArr.length; y++ ) {
            if ( issueToArray[x] === newArr[y] ) { 
              found = true;
              break;
            }
        }
        if (!found){
			newArr.push(issueToArray[x]);
		}
    }
	return newArr;
}

function removeImages(divId){
	if(divId=="response_drawing"){
		imgSrc = $('#drawImage1').attr('src');
		var imgDiv=document.getElementById(divId);
	}else{
		var imgDiv=document.getElementById(divId);
	  
		var imgSrc = imgDiv.childNodes[0].src;	
	}
	if(imgSrc != ''){
		var r = jConfirm('Do you want to delete Image ?', null, function(r){
			if (r==true){
				showProgress();	
				$.ajax({
					url: "remove_uploaded_file.php",
					type: "POST",
					data: "imageName="+imgSrc,
					success: function (res) {
						hideProgress();
					}
				});
				
				if(divId=="response_image_1"){
						document.getElementById("editImage1").style.display = "none";
						document.getElementById("editImage1").innerHTML = '';
						imgDiv.innerHTML = '';			
					}
					if(divId=="response_image_2"){
						document.getElementById("editImage2").style.display = "none";
						document.getElementById("editImage2").innerHTML = '';
						imgDiv.innerHTML = '';			
					}
					if(divId=="response_drawing"){
						 newDrawing=0;
						//document.getElementById("editDrawing").style.display = "none";
						//document.getElementById("editDrawing").innerHTML = '';
				//		var src = "inspections/drawing/default_draw_sketch.png";
						$("#response_drawing").html('<img width="100" height="90" style="margin-left:10px;margin-top:8px;" src="inspections/drawing/default_draw_sketch.png" id="drawImage1"><input type="hidden" value="" name="drawing" id="newDrawing">');
						$("#editDrawing").html('<input type="image" onclick="return launchEditor(\'drawImage1\', \'inspections/drawing/default_draw_sketch.png\');" value="Edit photo" src="images/add_sketch.png">');
						
				}				
			}
		});
	}
}

</script>
<style>
fieldset.permission { border:1px solid white; padding:15px; margin-top:30px; }
fieldset.permission legend { color:#FFFFFF; }
input[type=checkbox] { position: relative; cursor:pointer;}
label.label_check {cursor:pointer;}
</style>
