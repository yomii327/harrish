<?php session_start();
include_once("commanfunction.php");
$_SESSION['pasteCount'] = 1;
$obj = new COMMAN_Class(); ?>
<style>
.roundCorner{ border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; }
/*.jtree-arrow { display:none; }
ul.telefilms{list-style-image:url(images/location_icon.png); cursor:pointer;}
ul.telefilms li ul{list-style-image:url(images/sub_location_icon.png);}*/
ul.telefilms{list-style:none; cursor:pointer; font-size:15px;}
/*ul.telefilms li{height:15px;}*/
ul.telefilms li ul{list-style:none; line-height:30px;}
ul.telefilms li {line-height:30px;}
.jtree-arrow { padding-right: 5px; font-size: 15px; }
.list{ border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto; }
.box1 { background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent; border: 1px solid #0261A1; color: #000000; float: left; height: auto; width: 211px; }
.link1 { background-image: url("images/blue_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #000000; display: block; height: 25px; text-decoration: none; width: 202px; }
a.link1:hover { background-color: #015F9F; background-image: url("images/white_arrow.png"); background-position: 175px 34%; background-repeat: no-repeat; color: #FFFFFF; display: block; height: 25px; text-decoration: none; width: 202px; }
.txt13 { border-bottom: 1px solid #333333; color: #000000; font-size: 12px; font-weight: bold; height: 30px; }

#sortable1{ list-style-type: none; margin: 0; padding: 0; width: 25%; }
#sortable1 li { line-height: 30px; margin: 9px -550px 7px 29px; padding-left: 3px; }
.highlight { border: 1px solid red; font-weight: bold; font-size: 45px; background-color: #333333; }
.default { background: #cedc98; border: 1px solid #DDDDDD; color: #333333; }
.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default { border: 1px solid #02548e; background: #cfe7f7 url(images/ui-bg_glass_75_cfe7f7_1x400.png) 50% 50% repeat-x; font-weight: normal; color: #02548e; }
</style>
<script type="text/javascript" src="js/jquery.tree.js?v=1"></script>
<script type="text/javascript" src="js/jquery.contextmenu.r2.js"></script>
<script src="js/jquery.ui.core.js"></script>
<script src="js/jquery.ui.widget.js"></script>
<script src="js/jquery.ui.mouse.js"></script>
<script src="js/jquery.ui.sortable.js"></script>
<script type="text/javascript">
var deadLock = false;

var align = 'center';
var top1 = 100;
var width = 500;
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

$(document).ready(function() {
	$('ul.telefilms').tree({default_expanded_paths_string : '0/0/0,0/0/2,0/2/4'});
	$('span.demo1').contextMenu('myMenu2', {
		bindings: {
			'add': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add.php?location_id='+t.id, loadingImage);
			},
			'edit': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit.php?location_id='+t.id, loadingImage);
			},
			'delete': function(t) {
				var r = jConfirm('Do you want to delete location ?', null, function(r){
					if (r==true){
						if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
						showProgress();
						params = "location_id="+t.id+"&uniqueId="+Math.random();
						xmlhttp.open("POST", "location_tree_delete.php", true);
						xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
						xmlhttp.setRequestHeader("Content-length", params.length);
						xmlhttp.setRequestHeader("Connection", "close");
						xmlhttp.onreadystatechange=function(){
							if (xmlhttp.readyState==4 && xmlhttp.status==200){
								hideProgress();
								if(xmlhttp.responseText == 'Inspection Exist'){
									var r = jConfirm('Inspections added on this location. Do you want to delete location ?', null, function(r){
										if (r==true){
											if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
											showProgress();
											params = "location_id="+t.id+"&confirm=Y&uniqueId="+Math.random();
											xmlhttp.open("POST", "location_tree_delete.php", true);
											xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
											xmlhttp.setRequestHeader("Content-length", params.length);
											xmlhttp.setRequestHeader("Connection", "close");
											xmlhttp.onreadystatechange=function(){
												if (xmlhttp.readyState==4 && xmlhttp.status==200){
													hideProgress();
													if(xmlhttp.responseText == 'Location Deleted Successfully !'){
														document.getElementById('li_'+t.id).style.display='none';
														//jAlert('Location Deleted Successfully !');
													}else{
														jAlert(xmlhttp.responseText);
													}
												}
											}
											xmlhttp.send(params);
										}
									});
								}else{
									if (xmlhttp.readyState==4 && xmlhttp.status==200){
										if(xmlhttp.responseText == 'Location Deleted Successfully !'){
											document.getElementById('li_'+t.id).style.display='none';
											//jAlert('Location Deleted Successfully !');
										}else{
											jAlert(xmlhttp.responseText);
										}
									}
								}
							}
						}
						xmlhttp.send(params);
					}
				});
			},
			'copy': function(t) {
				copyLocation(t.id);
			},
			'paste': function(t) {
				if(copyStatus === true){
					pasteLocation(copyId, t.id);
					//copyId = '';
					//copyStatus = false;
				}else{
					jAlert('Copy Location for Paste Here !');
				}
			},
			'setorder': function(t) {
				modalPopup(align, top1, 800, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_set_order.php?location_id='+t.id, loadingImage, callSortOrder);
			},
		}
	});
	$('span.demo2').contextMenu('myMenu1', {
		bindings: {
			'add': function(t) {
				modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add.php?location_id='+t.id, loadingImage);
			},
			'paste': function(t) {
				if(copyStatus === true){
					pasteLocation(copyId, t.id);
					//copyId = '';
					//copyStatus = false;
				}else{
					jAlert('Copy Location for Paste Here !');
				}
			},
			'setorder': function(t) {
				modalPopup(align, top1, 800, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_set_order.php?location_id='+t.id, loadingImage, callSortOrder);
			},
		}
	});
});

function saveImages(fileDownloaded) {
	window.location = 'location_qrcode.php?filename='+ fileDownloaded +'&action=download';
	closePopup(300);
}

function addLocation(){ 
	var location = document.getElementById('subLocation').value;
	var locationId = document.getElementById('locationId').value;
	var checkProject = document.getElementById('checkProject').value;
	if (location==""){	$('#error').hide(); return false;	}
	if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	showProgress();
	if(checkProject == 'Yes'){	params = "location="+location+"&locationId=0&uniqueId="+Math.random();	}
	if(checkProject == 'No'){	params = "location="+location+"&locationId="+locationId+"&uniqueId="+Math.random();		}
	xmlhttp.open("POST", "location_tree_add.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			hideProgress(); 
			if(xmlhttp.responseText==0)
			{
			  //jAlert('Duplicated Entry in Same Level!');
			  $('#error').show();
			  $('#error').html('<b>Duplicated Entry in Same Level!</b>');
			  return false;	
			}
			else
			{
			if(checkProject == 'No'){ jQuery("#li_"+locationId).append(xmlhttp.responseText); }
			if(checkProject == 'Yes'){ $('ul.telefilms:first').append(xmlhttp.responseText); }
			closePopup(fadeOutTime);
			$('#error').hide();
			}
			
			$('span.demo1').contextMenu('myMenu2', {
				bindings: {
					'add': function(t) {
						modalPopup(align, top1, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add.php?location_id='+t.id, loadingImage);
					},
					'edit': function(t) {
						modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_edit.php?location_id='+t.id, loadingImage);
					},
					'delete': function(t) {
						var r = jConfirm('Do you want to delete location ?', null, function(r){
							if (r==true){
								if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
								showProgress();
								params = "location_id="+t.id+"&uniqueId="+Math.random();
								xmlhttp.open("POST", "location_tree_delete.php", true);
								xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
								xmlhttp.setRequestHeader("Content-length", params.length);
								xmlhttp.setRequestHeader("Connection", "close");
								xmlhttp.onreadystatechange=function(){
									if (xmlhttp.readyState==4 && xmlhttp.status==200){
										hideProgress();
										if(xmlhttp.responseText == 'Inspection Exist'){
											var r = jConfirm('Inspections added on this location. Do you want to delete location ?', null, function(r){
												if (r==true){
													if(window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
													showProgress();
													params = "location_id="+t.id+"&confirm=Y&uniqueId="+Math.random();
													xmlhttp.open("POST", "location_tree_delete.php", true);
													xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
													xmlhttp.setRequestHeader("Content-length", params.length);
													xmlhttp.setRequestHeader("Connection", "close");
													xmlhttp.onreadystatechange=function(){
														if (xmlhttp.readyState==4 && xmlhttp.status==200){
															hideProgress();
															if(xmlhttp.responseText == 'Location Deleted Successfully !'){
																document.getElementById('li_'+t.id).style.display='none';
																//jAlert('Location Deleted Successfully !');
															}else{
																jAlert(xmlhttp.responseText);
															}
														}
													}
													xmlhttp.send(params);
												}
											});
										}else{
											if (xmlhttp.readyState==4 && xmlhttp.status==200){
												if(xmlhttp.responseText == 'Location Deleted Successfully !'){
													document.getElementById('li_'+t.id).style.display='none';
													//jAlert('Location Deleted Successfully !');
												}else{
													jAlert(xmlhttp.responseText);
												}
											}
										}
									}
								}
								xmlhttp.send(params);
							}
						});
					},
					'copy': function(t) {
						copyLocation(t.id);
					},
					'paste': function(t) {
						if(copyStatus === true){
							pasteLocation(copyId, t.id);
							//copyId = '';
							//copyStatus = false;
						}else{
							jAlert('Copy Location for Paste Here !');
						}
					},
					'setorder': function(t) {
						modalPopup(align, top1, 800, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_set_order.php?location_id='+t.id, loadingImage, callSortOrder);
					},
					'qrCode': function(t) {
						modalPopup(align, top1, 800, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_qrcode.php?location_id='+t.id, loadingImage);
					},
				}
			});
			$('span.demo2').contextMenu('myMenu1', {
				bindings: {
					'add': function(t) {
						modalPopup(align, top, width, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_tree_add.php?location_id='+t.id, loadingImage);
					},
					'paste': function(t) {
						if(copyStatus === true){
							pasteLocation(copyId, t.id);
							//copyId = '';
							//copyStatus = false;
						}else{
							jAlert('Copy Location for Paste Here !');
						}
					},
					'setorder': function(t) {
						modalPopup(align, top1, 800, padding, disableColor, disableOpacity, backgroundColor, borderColor, borderWeight, borderRadius, fadeOutTime, 'location_set_order.php?location_id='+t.id, loadingImage, callSortOrder);
					},
				}
			});
		}
	}
	xmlhttp.send(params);
}

function editLocation(){
	var location = document.getElementById('locationName').value;
	var locationId = document.getElementById('locationIdEdit').value;
	if (location==""){	return false;	}
	if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	showProgress();
	params = "location="+location+"&locationId="+locationId+"&uniqueId="+Math.random();
	xmlhttp.open("POST", "location_tree_edit.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			hideProgress();
			document.getElementById(locationId).innerHTML=xmlhttp.responseText;
			closePopup(fadeOutTime);
		}
	}
	xmlhttp.send(params);
}

function copyLocation(copyedId){
	if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	showProgress();
	params = "copyLocation="+copyedId+"&uniqueId="+Math.random();
	xmlhttp.open("POST", "location_copied_check.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			hideProgress();
			if(xmlhttp.responseText == 'Error'){
				jAlert("You can't copy sublocation !");
				copyStatus = false;
				copyId = '';
			}else{
				jAlert('Location Copied !');
				copyStatus = true;
				copyId = copyedId;
			}
		}
	}
	xmlhttp.send(params);
}

function pasteLocation(copyedId, pasteId){
	if (window.XMLHttpRequest){xmlhttp=new XMLHttpRequest();}else{xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");}
	showProgress();
	params = "copyLocation="+copyedId+"&pasteLocation="+pasteId+"&uniqueId="+Math.random();
	xmlhttp.open("POST", "location_paste_copied_location.php", true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			hideProgress();
			if(xmlhttp.responseText == 'Error'){
				jAlert("Please chose another location. You can't paste one location under itself !");
			}else{
				reloadLocationPart(<?php echo $_SESSION['idp'];?>);
			}
		}
	}
	xmlhttp.send(params);
}

function reloadLocationPart(projectId){location.reload();}
</script>
<?php ini_set('auto_detect_line_endings', true);
include('func.php');
//if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){ ?>
	<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php }
$builder_id = $_SESSION['ww_builder_id'];
if(isset($_FILES['csvFile']['tmp_name'])){ // Location/ subloaction import CSV file.
	if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name'])){
		$filename=$_FILES['csvFile']['name']; // Csv File name
		$file_ext=explode('.',$filename);
		$ext=$file_ext[1];
		if($ext=='csv' || $ext=='CSV'){
			
			$files=$_FILES['csvFile']['tmp_name'];		
			$databasetable = "project_locations"; // database table name
			$fieldseparator = ","; // CSV file comma format
			$lineseparator = "\n";
			$csvfile = $files; //CSV file name
			$addauto = 1;
			$save = 1;
			$file = fopen($csvfile,"r");
			$size = filesize($csvfile); //check file record
			if(!$size) {
				echo "File is empty.\n";
				exit;
			}
				$lines = 0;
				$queries = "";
				$linearray = array();
				$fieldarray= array();
				$record='';
				while( ($line = fgets($file)) != FALSE) 
				{
					
					$lines++;
					$line = trim($line," \t");
					//$line = str_replace("\n","",$line);					
					//$line = str_replace("\r","",$line);
					$linearray = explode($fieldseparator,$line);
					
					$fieldarray[] = $linearray ;
					$linemysql = implode("','",$linearray);
					//echo $linemysql; 
				}//end foreach
				fclose($file);
				$num=count($fieldarray); //count no of reco
				$count=0;
				
				//Find Special Character in CSV dated : 04/10/2012
$err_msg = '';
$legalCharArray = array('0', '10', '13', '32', '34', '38', '39', '40', '41', '44', '45', '46', '47',

'63', '60', '62', '58', '124', '125', '123', '61', '43', '95', '42', '94', '37', '36', '35', '33', '126', '96',

'48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '59', 

'64', '65', '66', '67', '68', '69', '70', '71', '72', '73', '74', '75', '76', '77', '78', '79', '80','81', '82', '83', '84', '85', '86', '87', '88', '89', '90', '91', '92', '93',

'97', '98', '99', '100', '101', '102', '103', '104', '105', '106', '107', '108', '109', '110', '111', '112', '113', '114', '115', '116', '117', '118', '119', '120', '121', '122');
				for($g=1; $g<$num; $g++){
					$subCount = count($fieldarray[$g]);
					for($m=0;$m<$subCount; $m++){
						$string = $fieldarray[$g][$m];
						$strArray = str_split($string);
						$subSubCount  = count($strArray);
						for($b=0;$b<$subSubCount;$b++){
							$asciiVal = ord($strArray[$b]);
							if(!in_array($asciiVal, $legalCharArray)){
								$lineNoArray[] = $g+1;
							}
						}
					}
				}
				if(!empty($lineNoArray)){
					$err_msg = "Line no's ".join(', ', array_unique($lineNoArray))." contains some UNICODE characters. Please correct the CSV file and try again.";
				}
				if($err_msg != ''){ }else{
					$farr = array(); // set array for parent id
					for($i=1;$i<$num;$i++){ //read second line beacuse first line cover headings
					
						$filedcount=count($fieldarray[$i]); 
						for($j=0;$j< $filedcount;$j++){
							
							$fieldvalue=trim($fieldarray[$i][$j]);
							
							if(!empty($fieldvalue)){
								if(isset($farr[$j-1]) && !empty($farr[$j-1]))
									$parent_id = $farr[$j-1];
								else
									$parent_id = 0; //set parent id 0 for first record
							
							
								$select="select * from project_locations where location_title='".$fieldvalue."' AND location_parent_id = $parent_id and project_id=".$_SESSION['idp']." and is_deleted=0";
								//echo $select; die;
								$result=mysql_query($select);
								  $rows=mysql_num_rows($result); 
								$rowdata=mysql_fetch_row($result);
								//echo $rows; die;
								if($rows>0){ // if exist,
								
									$pid=$rowdata[0];
									$farr[$j]=$pid;// get id
									
									//$record.='<br>'.$rowdata[3].'<br>';//keep Duplicate Record list.
										$record=count($rowdata[3]);//keep Duplicate Record list.
										if($record>0){
											$count=$count+1;
										}
								}else{
									$creatdate=date('Y-m-d H:i:s');	
									$insert="insert into project_locations (location_parent_id,project_id,location_title,last_modified_date,last_modified_by,created_date,	created_by) values ($parent_id,".$_SESSION['idp'].",'".$fieldvalue."',now(),".$builder_id.",now(),".$builder_id.")";
									
									
									mysql_query($insert);
									$id = mysql_insert_id();
									$farr[$j] = $id;
										  
										  // code by Harsh Start
										  $locIdTree =   $obj->subLocationsIDS($id, ' > ');
										  $locNameTree = $obj->subLocations($id, ' > ');
										  $query = 'UPDATE project_locations SET location_id_tree = "'.$locIdTree.'", location_name_tree = "'.$locNameTree.'" WHERE location_id = '.$id;
										  mysql_query($query);
	                                      // code by Harsh End
										 
								}
								$success='File uploaded successfully.';
							}// end If when record not found
						}
					}
					@mysql_close($con); //close db connection
						
					if(isset($count) && !empty($count)){
						$success="$count Duplicate Records ";
					}
					$msg1= "<br/>$lines record(s) inserted.";
				}
	}else{
		$err_msg= 'Please select .csv file.';
	}
	}else{
		$err_msg= 'Please select file.';
	}
}?>
<!--<h1 style="font-size:15px;"><img src="images/project_big.png" width="48" height="39" align="absmiddle" />Projects Configuration</h1><br />-->
<div id="middle" style="padding-top:10px;">
<div id="leftNav" style="width:250px;float:left;">
<?php include 'side_menu.php';
$id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']);?>
</div>
<div id="rightCont" style="float:left;width:700px;">
	<div class="content_hd1" style="width:500px;margin-top:12px;">
	  	<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font>
		<a class="green_small" href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>" style="float:left;margin-top:-25px;margin-left:586px;cursor: pointer;">Back</a>
	</div><br clear="all" />
	<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top:-15px;margin-top:0px\9;">
		<?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { ?>
			<div class="success_r" style="height:35px;width:185px;"><p><?php echo $_SESSION['add_project'] ; ?></p></div>
		<?php unset($_SESSION['add_project']);} ?><?php if((isset($success)) && (!empty($success))) { ?>
			<div class="success_r" style="height:35px;width:185px;"><p><?php echo $success; ?></p></div>
		<?php }
			if((isset($err_msg)) && (!empty($err_msg))) { ?>
			<div class="failure_r" style="height:50px;width:520px;"><p><?php echo $err_msg; ?></p></div>
		<?php } ?>
	</div>
	<div class="content_container" style="float:left;width:690px;border:1px solid; margin-bottom:50px;text-align:center;margin-left:10px;margin-right:10px;height:80px;">
	<!--First Box-->
	<div style="width:722px; height:50px; float:left; margin-top:5px;">
        <form method="post" name="csvLocation" id="csvLocation" enctype="multipart/form-data" >
			<table width="690px" border="0" cellspacing="0" cellpadding="3">
				<tr>
					<td colspan="3" align="left"><a href="/csv/Location_and_Sublocation_Template.csv" style="text-decoration:none;color:#FFF;"><strong style="font-size:16px;">Click <u>here</u> to download CSV template</strong></a></td>
               <td> 
               	<!-- <input type="button"  class="submit_btn" onclick=location.href="project_locations_export.php"  style="background:none;background-image:url(images/export_csv_btn.png); width:87px; height:30px;border:none;margin-left:0px;" /> -->
               	<input class="green_small" type="button" style="cursor: pointer; margin-left:0px;" value="Export CSV" onclick=location.href="project_locations_export.php">
               	</td>
				</tr>   
				<tr>
					<td width="185px;" align="left">&nbsp;</td>
					<td width="130px;">Upload&nbsp;CSV&nbsp;File&nbsp;:</td>
					<td width="240px;" align="left"><input type="file" name="csvFile" id="scvFile" value="" /></td>
					<td width="120px;" height="38px">
						<input id="location_csv" class="green_small" type="button" onclick="validateSubmit();" value="Import CSV" name="location_csv" style="cursor: pointer; margin-left: 0;">
						<!-- <input type="button" style="background: url('images/import_csv_btn.png') repeat scroll 0 0 transparent;border: medium none;height: 30px;margin-left: 0;width: 87px;color:transparent;font-size:0px;"  name="location_csv" id="location_csv" value="Import CSV" onclick="validateSubmit();" /> -->
					</td>	
				</tr>
			</table>
		</form>
	</div>
	</div>
	<div class="big_container" style="width:700px;float:left;margin-top:-50px;margin-left:30px;" >
<?php
//$q = "SELECT location_id, location_title FROM project_locations WHERE project_id = '".$_SESSION['idp']."' AND location_parent_id = 0 AND is_deleted = 0 ORDER BY order_id, location_id, location_title";
//$re = mysql_query($q);
//$isLocation = mysql_num_rows($re);
//while($rw = mysql_fetch_array($re)){	$val[] = $rw;	}
#print_r($val);die;?>
<div id="locationsContainer">
<span id="projectId_<?php echo 198?>">
<span class="jtree-button demo2" id="projectId_<?php echo $_SESSION['idp']?>" style="background-image: url('images/project.png');background-position: 0 15px;background-repeat: no-repeat;display: block;height: 30px;padding-left: 40px;padding-top: 9px;width: 90%;font-size:26px;cursor: pointer;"><?php echo $projectName?></span>
<?php $q = "SELECT location_id, location_title FROM project_locations WHERE project_id = '".$_SESSION['idp']."' AND location_parent_id = 0 AND is_deleted = 0 ORDER BY order_id, location_id, location_title";
	$re = mysql_query($q);
	$isLocation = mysql_num_rows($re);
	if(mysql_num_rows($re) > 0){
		echo '<ul class="telefilms">';
		while($locations = mysql_fetch_array($re)){
			echo '<li id="li_'.$locations['location_id'].'">';
			$data=$obj->recurtion($locations['location_id'], $_SESSION['idp']);
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
<div class="contextMenu" id="myMenu2">
	<ul>
		<li id="add"><img src="images/add.png" align="absmiddle" width="14"  height="14"/> Add</li>
		<li id="edit"><img src="images/edit_right.png"  align="absmiddle" width="16" height="16" /> Edit</li>
		<li id="delete"><img src="images/delete.png"  align="absmiddle" width="14" height="15" /> Delete</li>
		<li id="copy"><img src="images/copy.png"  align="absmiddle" width="14" height="15" /> Copy</li>
		<li id="paste"><img src="images/paste.png"  align="absmiddle" width="14" height="15" /> Paste</li>
		<li id="setorder"><img src="images/set-order.png"  align="absmiddle" width="14" height="15" /> Change Order</li>
		<li id="qrCode"><img src="images/qr_code.png"  align="absmiddle" width="14" height="15" /> Generate QR Code</li>
	</ul>
</div>
<div class="contextMenu" id="myMenu1">
	<ul>
		<li id="add"><img src="images/add.png" align="absmiddle" width="14"  height="14"/> Add</li>
	<?php if($isLocation > 0){?>
		<li id="paste"><img src="images/paste.png"  align="absmiddle" width="14" height="15" /> Paste</li>
	<?php }?>
		<li id="setorder"><img src="images/set-order.png"  align="absmiddle" width="14" height="15" /> Change Order</li>
	</ul>
</div>
</div>
</div>
<!--<div class="big_container" style="width:722px;float:left;margin-top:50px;" ><?php #include'csv_table.php';?></div>-->
</div>
<script type="text/javascript">
function validateSubmit(){
	var r = jConfirm('Importing the location(s) CSV may cause all the defects linked to the locations to be orphaned as well as ay un-synced issues from the iPads? Still Do You want to upload "Location CSV" ?', null, function(r){
		if (r === true){
			document.forms["csvLocation"].submit();	
		}else{
			return false;
		}
	});
}
function callSortOrder() {
	$("#sortable1").sortable({connectWith: ".connectedSortable"}).disableSelection();
}
function saveOrderFunc(){
	showProgress();
	var subLocid = new Array;
	var subLocVal = new Array;
	var i=0;
	$("#sortable1").find('li').each(function() {
		subLocid[i] = this.id;
		subLocVal[i] = $(this).text();
		i++;
	});
	$.post("location_set_order.php", {locId:subLocid, locVal:subLocVal, uniqueId:Math.random()}).done(function(data) {
		hideProgress();
		var jsonResult = JSON.parse(data);
		if(jsonResult.status){
			$('#msgHolderDiv').show();
			$('#msgHolder').text(jsonResult.msg);
			setTimeout(function(){$('#msgHolderDiv').hide('slow');	},3000);
		}else{
			$('#msgHolderDiv').show();
			$('#msgHolder').text(jsonResult.msg);
			setTimeout(function(){$('#msgHolderDiv').hide('slow');	},3000);
		}
		reloadLocationPart(<?php echo $_SESSION['idp'];?>);
	});
}
</script>