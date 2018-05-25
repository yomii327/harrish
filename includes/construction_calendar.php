<?php $baseurl='';
include_once("commanfunction.php");
$obj = new COMMAN_Class();

ini_set('auto_detect_line_endings', true);
 include('func.php');
if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
 $builder_id=$_SESSION['ww_builder_id'];
 function normalise($string) {
	$string = str_replace("\r", "\n", $string);
	
	return $string;	
}
# Import Csv File
if(isset($_FILES['csvFile']['tmp_name'])){ // Location/ subloaction import CSV file.
	$leaveArray = array("Annual leave", "Fixed long weekend", "Industrial", "Public holiday", "Raindays", "Rostered day off", "Picnic day");
	
	if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name'])){
		$filename=$_FILES['csvFile']['name']; // Csv File name
		$file_ext=explode('.',$filename);
		$ext=$file_ext[1];
		if($ext=='csv' || $ext=='CSV'){
			$files=$_FILES['csvFile']['tmp_name'];		
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
			/*while( ($line = fgets($file)) != FALSE) {
				$lines++;
				$line = trim($line," \t");
				$linearray = explode($fieldseparator,$line);
				$fieldarray[] = $linearray ;
				$linemysql = implode("','",$linearray);
			}//end foreach*/
			
			while( ($data =  fgetcsv($file,1000,",")) != FALSE){
			      $numOfCols = count($data);
			      for ($index = 0; $index < $numOfCols; $index++){
					  $data[$index] = stripslashes(normalise($data[$index]));
			      }
			      $fieldarray[] = $data;
			}
			fclose($file);
			$num=count($fieldarray);
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
					$noti_msg='';
				if($err_msg != ''){ }else{
					for($i=1;$i<$num;$i++){ //read second line beacuse first line cover headings
						$fieldarray[$i][0] = trim($fieldarray[$i][0]);
						$fieldarray[$i][1] = trim($fieldarray[$i][1]);
						//Check array value is not empty					
						if(!empty($fieldarray[$i][0]) && !empty($fieldarray[$i][1])){ //echo "<br>";
							//Check value in leave array is for correct leave insert in table
							$d = explode('/', $fieldarray[$i][0]);
							if(in_array($fieldarray[$i][1],$leaveArray) && isset($d[2])){
								$date = $d[2]."-".$d[1]."-".$d[0]." 00:00:00";
								$prleave_date = $date; $fieldarray[$i][0]='';
								$prleave_leave_type = addslashes($fieldarray[$i][1]); $fieldarray[$i][1]='';
								$prleave_reason = addslashes($fieldarray[$i][2]); $fieldarray[$i][2]='';
								$prleave_is_leave = 0;
								$project_id = $_SESSION['idp'];
								
								
								$select="select * from project_leave where date='".addslashes(trim($date))."' AND project_id=".$_SESSION['idp']." and is_deleted=0";
								
								$issue=mysql_query($select);
								$row_data=mysql_num_rows($issue);
								if($row_data > 0){
								//	$record=count($fieldarray[$i][1]);//keep Duplicate Record list.
								//	if($record>0){
								//		$count=$count+1;
								//	}
									$prleave_original_modified = date('Y-m-d H:i:s');
									$prleave_modified_by = $_SESSION['ww_builder_id'];	
									$updateQry = "UPDATE project_leave SET date='".$prleave_date."', leave_type='".$prleave_leave_type."', reason='".$prleave_reason."', is_leave='".$prleave_is_leave."', project_id='".$project_id."', original_modified_date='".$prleave_original_modified."', last_modified_by='".$prleave_modified_by."' where date='".addslashes(trim($date))."' AND project_id=".$_SESSION['idp']." and is_deleted=0";
											
									mysql_query($updateQry);
								}else{
									$prleave_created = date('Y-m-d H:i:s');
									$prleave_created_by = $_SESSION['ww_builder_id'];	
								
									@$insertQry = "Insert into project_leave (date, leave_type, reason, is_leave, project_id, created_date, created_by) values ('".$prleave_date."', '".$prleave_leave_type."', '".$prleave_reason."', '".$prleave_is_leave."', '".$project_id."', '".$prleave_created."', '".$prleave_created_by."')";
									mysql_query($insertQry);
									$success='File uploaded successfully.';
								}
							}else{
								$noti_msg.=empty($noti_msg)?"Following are the leave names those are incorrect. Please see the leave tamplates :- <br style='margin-bottom:10px;'>":'';
								$noti_msg.= $fieldarray[$i][1].'.<br>';
								
							}
						}
					}
					@mysql_close($con); //close db connection
					if(isset($count) && !empty($count))
					{
						$success="Total $count Duplicate Records";
					}
				}
		}//
		else
		{ 
			$err_msg= 'Please select .csv file.';
		}
	}
	else
	{
		$err_msg= 'Please select file.';
	}
}
?>
<div id="middle" style="padding-top:10px;">
<div id="leftNav" style="width:250px;float:left;">
	<?php include 'side_menu.php';?>
<?php $id=base64_encode($_SESSION['idp']);
$projId = $_SESSION['idp'];
$hb=base64_encode($_SESSION['hb']);
 ?>
</div>
<div id="rightCont" style="float:left;width:700px;">
	<div class="content_hd1" style="width:500px;margin-top:12px;">
		<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font>
		<!-- <a style="float:left;margin-top:-25px;width:87px;margin-left:590px;" href="?sect=add_project_detail&id=<?php //echo $id;?>&hb=<?php //echo $hb;?>">
			<img src="images/back_btn2.png" style="border:none;" />
		</a> -->
		<a href="?sect=add_project_detail&id=<?=$id;?>&hb=<?=$hb;?>" class="green_small" style="float:left;margin-top:-25px; margin-left:600px;z-index:100;">Back			
		</a>
	</div><br clear="all" />
	<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;margin-top:-15px;margin-top:0px\9; font-size:14px;">
		<?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { ?>
			<div class="success_r" style="height:35px;width:215px;"><p><?php echo $_SESSION['add_project'] ; ?></p></div>
		<?php unset($_SESSION['add_project']);} ?><?php if((isset($success)) && (!empty($success))) { ?>
			<div class="success_r" style="height:35px;width:215px;"><p><?php echo $success; ?></p></div>
		<?php }
			if((isset($err_msg)) && (!empty($err_msg))) { ?>
			<div class="failure_r" style="height:50px;width:520px;"><p><?php echo $err_msg; ?></p></div>
		<?php } 
			if((isset($noti_msg)) && (!empty($noti_msg))) {?>
			<div class="failure_r" style="height:auto;width:520px;background-image:none;"><p><?php echo $noti_msg; ?></p></div>
		<?php } ?>
	</div>
  	
<!-- Import / Export Project Leave -->
<div class="content_container" style="float:left;width:690px;border:1px solid; margin-bottom:10px;text-align:center;margin-left:10px;margin-right:10px;height:90px;">
<!--First Box-->
    <div style="width:722px; height:50px; float:left; margin-top:5px;">
        <form method="post" name="csvProjectLeave" id="csvProjectLeave" enctype="multipart/form-data"  onSubmit="return validateSubmit()">
            <table border="0" cellspacing="0" cellpadding="3" style="width:690px;">
                <tr>
                    <td colspan="3" align="left"><a href="csv/Project_Leave.csv" style="text-decoration:none;color:#FFF;"><strong style="font-size:16px;">Click <u>here</u> to download CSV template</strong></a></td>
                    <td>
                        <!--input type="button"  class="submit_btn" onclick='location.href="issue_export.php"'  style="background:none;background-image:url(images/export_csv_btn.png); width:87px; height:30px;border:none;margin-left:15px;" /--></td>
                    </td>
                </tr>   
                <tr>
                    <td width="185px;" align="left">&nbsp;</td>
                    <td width="130px;" style="font-size:12px">Upload&nbsp;CSV&nbsp;File&nbsp;:</td>
                    <td width="240px;" align="left"><input type="file" name="csvFile" id="csvFile" value="" /></td>
                    <td width="120px;" height="50px">
                    	<input type="button" style="height: 30px;margin-left:15px;" value="Import CSV"  name="location_csv" id="location_csv" class="green_small" value="Import CSV" onclick="validateSubmit();" />
                   </td>
                </tr>
            </table>
        </form>
    <br clear="all" />
    </div>
</div>
    
<!-- Start Calendar  --> 
<div style="margin-left:10px;">
<?php 
	$select = "prleave_id as plId, project_id as projId, date as plDate, leave_type as prLeave, reason as plReason, is_leave as plIsLeave";
	$table ="project_leave";
	$where = "is_deleted=0 and project_id=".$projId;
	$prLeaves = $obj->selQRYMultiple($select, $table, $where);
	
	$select = "auto_increment";
	$table ="information_schema.TABLES";
	$where = "TABLE_NAME ='project_leave'";
	$result = $obj->selQRY($select, $table, $where);
	$plNewId = $result['auto_increment'];
?>
      <!-- Include CSS for JQuery Frontier Calendar plugin (Required for calendar plugin) -->
      <link rel="stylesheet" type="text/css" href="js/project_leave_calendar/css/frontierCalendar/jquery-frontier-cal-1.3.2.css" />
      
      <!-- Include CSS for JQuery UI (Required for calendar plugin.) -->
      <link rel="stylesheet" type="text/css" href="js/project_leave_calendar/css/jquery-ui/smoothness/jquery-ui-1.8.1.custom.css" />
      
      <!--
Include JQuery Core (Required for calendar plugin)
** This is our IE fix version which enables drag-and-drop to work correctly in IE. See README file in js/jquery-core folder. **
--> 
      <script type="text/javascript" src="js/project_leave_calendar/js/jquery-core/jquery-1.4.2-ie-fix.min.js"></script> 
      
      <!-- Include JQuery UI (Required for calendar plugin.) --> 
      <script type="text/javascript" src="js/project_leave_calendar/js/jquery-ui/smoothness/jquery-ui-1.8.1.custom.min.js"></script> 
      
      <!-- Include jquery tooltip plugin (Not required for calendar plugin. Used for example.) --> 
      <script type="text/javascript" src="js/project_leave_calendar/js/jquery-qtip-1.0.0-rc3140944/jquery.qtip-1.0.js"></script> 
      
      <!--
	(Required for plugin)
	Dependancies for JQuery Frontier Calendar plugin.
    ** THESE MUST BE INCLUDED BEFORE THE FRONTIER CALENDAR PLUGIN. **
--> 
      <script type="text/javascript" src="js/project_leave_calendar/js/lib/jshashtable-2.1.js"></script> 
      
      <!-- Include JQuery Frontier Calendar plugin --> 
      <script type="text/javascript" src="js/project_leave_calendar/js/frontierCalendar/jquery-frontier-cal-1.3.2.min.js"></script> 
      <!-- Some CSS for our example. (Not required for calendar plugin. Used for example.)-->
      <style type="text/css" media="screen">
/*
Default font-size on the default ThemeRoller theme is set in ems, and with a value that when combined 
with body { font-size: 62.5%; } will align pixels with ems, so 11px=1.1em, 14px=1.4em. If setting the 
body font-size to 62.5% isn't an option, or not one you want, you can set the font-size in ThemeRoller 
to 1em or set it to px.
http://osdir.com/ml/jquery-ui/2009-04/msg00071.html
*/
body { font-size: 62.5%; }
.shadow {
	-moz-box-shadow: 3px 3px 4px #aaaaaa;
	-webkit-box-shadow: 3px 3px 4px #aaaaaa;
	box-shadow: 3px 3px 4px #aaaaaa;
	/* For IE 8 */
	-ms-filter: "progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#aaaaaa')";
	/* For IE 5.5 - 7 */
	filter: progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color='#aaaaaa');
}
#ui-datepicker-div, .ui-datepicker-buttonpane{ display:none;}
.JFrontierCal-Day-Cell div{word-wrap:break-word; width:10px;}
</style>
      <script type="text/javascript"> var plId=0; var TotalOffDay = new Array(); var today='';
var days= ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
$(document).ready(function(){	
	var clickDate = "";
	var selectedDate = "";	
	var clickAgendaItem = "";
	
	/**
	 * Initializes calendar with current year & month
	 * specifies the callbacks for day click & agenda item click events
	 * then returns instance of plugin object
	 */
	var jfcalplugin = $("#mycal").jFrontierCal({
		date: new Date(),
		dayClickCallback: myDayClickHandler,
		agendaClickCallback: myAgendaClickHandler,
		agendaDropCallback: myAgendaDropHandler,
		agendaMouseoverCallback: myAgendaMouseoverHandler,
//		applyAgendaTooltipCallback: myApplyTooltip,
		agendaDragStartCallback : myAgendaDragStart,
		agendaDragStopCallback : myAgendaDragStop,
		dragAndDropEnabled: false
	}).data("plugin");
	
	/**
	 * Do something when dragging starts on agenda div
	 */
	function myAgendaDragStart(eventObj,divElm,agendaItem){
		// destroy our qtip tooltip
		if(divElm.data("qtip")){
			divElm.qtip("destroy");
		}	
	};
	
	/**
	 * Do something when dragging stops on agenda div
	 */
	function myAgendaDragStop(eventObj,divElm,agendaItem){
		//alert("drag stop");
	};
	
	/**
	 * Custom tooltip - use any tooltip library you want to display the agenda data.
	 * for this example we use qTip - http://craigsworks.com/projects/qtip/
	 *
	 * @param divElm - jquery object for agenda div element
	 * @param agendaItem - javascript object containing agenda data.
	 */
	function myApplyTooltip(divElm,agendaItem){

		// Destroy currrent tooltip if present
		if(divElm.data("qtip")){
			divElm.qtip("destroy");
		}
		
		var displayData = "";
		
		var title = agendaItem.title;
		var startDate = agendaItem.startDate;
		var endDate = agendaItem.endDate;
		var allDay = agendaItem.allDay;
		var data = agendaItem.data;
		displayData += "<br><b>" + title+ "</b><br><br>";
		if(allDay){
			displayData += "(All day event)<br><br>";
		}else{
			//displayData += "<b>Starts:</b> " + startDate + "<br>" + "<b>Ends:</b> " + endDate + "<br><br>";
		}
		
		// Start:- show custom tool tip content
		for (var propertyName in data) {
		//	displayData += "<b>" + propertyName + ":</b> " + data[propertyName] + "<br>"
		}
		//
			displayData = "<b>Project Id &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> " + data['projId'] + "<br>";
			displayData += "<b>Project Leave:</b> " + data['prLeave'] + "<br>";
			displayData += "<b>Description :</b> " + data['plReason'] + "<br>";
			
		// End:- show custom tool tip content			
			
		// use the user specified colors from the agenda item.
		var backgroundColor = agendaItem.displayProp.backgroundColor;
		var foregroundColor = agendaItem.displayProp.foregroundColor;
		var myStyle = {
			border: {
				width: 5,
				radius: 10
			},
			padding: 10, 
			textAlign: "left",
			tip: true,
			name: "dark" // other style properties are inherited from dark theme		
		};
		if(backgroundColor != null && backgroundColor != ""){
			myStyle["backgroundColor"] = backgroundColor;
		}
		if(foregroundColor != null && foregroundColor != ""){
			myStyle["color"] = foregroundColor;
		}
		// apply tooltip
		divElm.qtip({
			content: displayData,
			position: {
				corner: {
					tooltip: "bottomMiddle",
					target: "topMiddle"			
				},
				adjust: { 
					mouse: true,
					x: 0,
					y: -15
				},
				target: "mouse"
			},
			show: { 
				when: { 
					event: 'mouseover'
				}
			},
			style: myStyle
		});

	};

	/**
	 * Make the day cells roughly 3/4th as tall as they are wide. this makes our calendar wider than it is tall. 
	 */
	jfcalplugin.setAspectRatio("#mycal",0.75);

	/**
	 * Called when user clicks day cell
	 * use reference to plugin object to add agenda item
	 */
	function myDayClickHandler(eventObj){
		// Get the Date of the day that was clicked from the event object
		var date = eventObj.data.calDayDate;
		// store date in our global js variable for access later
		clickDate = date.getFullYear() + "-" + (date.getMonth()+1) + "-" + date.getDate();
		// open our add event dialog
		today = new Date(date);	today.setDate(date.getDate());
//		if((date.getMonth()+1)>9){ dateM = (date.getMonth()+1);}else{ dateM = "0"+(date.getMonth()+1); }
//		if((date.getDate()+1)>9){ dateD = (date.getDate()+1);}else{ dateD = "0"+(date.getDate()+1); }
		if((date.getMonth()+1)>9){ dateM = (date.getMonth()+1);}else{ dateM = "0"+(date.getMonth()+1); }
		if((date.getDate()+1)>9){ dateD = (date.getDate());}else{ dateD = "0"+(date.getDate()); }
		
		var days= ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
		document.getElementById("selectedDate").innerHTML = days[date.getDay()]+", " + dateD + "/" + dateM + "/" + date.getFullYear();
		
		if(days[today.getDay()]=="Sunday"){
				 document.getElementById("leaveDay").innerHTML='<td width="38%" valign="top"><label>Working day<span class="req">*</span></label></td>                <td width="4%" valign="top">&nbsp;</td>                <td width="68%" valign="top"><input name="isLeave" type="checkbox" id="isLeave" value="1"  style="margin-bottom:12px;" /> <input name="leaveType" type="hidden" id="leaveType" value="Sunday" style="margin-top:20px;" /></td>';
		}else{
			 document.getElementById("leaveDay").innerHTML='<td width="38%" valign="top"><label>Non working day<span class="req">*</span></label></td>                <td width="4%" valign="top">&nbsp;</td>                <td width="68%" valign="top"><select id="leaveType" name="leaveType" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">                    <option value="">Add new leave</option> <option value="Annual leave">Annual leave</option> <option value="Fixed long weekend">Fixed long weekend</option>  <option value="Industrial">Industrial</option>  <option value="Public holiday">Public holiday</option><option value="Raindays">Raindays</option><option value="Rostered day off">Rostered day off</option> <option value="Picnic day">Picnic day</option> </select><input name="isLeave" type="hidden" id="isLeave" value="0" /></td>';
		}
//		alert(days[today.getDay()]);
		$('#add-event-form').dialog('open');
	};
	
	/**
	 * Called when user clicks and agenda item
	 * use reference to plugin object to edit agenda item
	 */
	function myAgendaClickHandler(eventObj){
		// Get ID of the agenda item from the event object
		var agendaId = eventObj.data.agendaId;		
		// pull agenda item from calendar
		var agendaItem = jfcalplugin.getAgendaItemById("#mycal",agendaId);
		clickAgendaItem = agendaItem;
		
		$("#display-event-form").dialog('open');
/*		if(clickAgendaItem.startDate>0){ alert(clickAgendaItem.startDate);
			
			var getDat = clickAgendaItem.data['plDate'].split(" ");
			getDat = getDat[0];
			selDate = new Date(getDat);
			selectedDate = days[selDate.getDay()]+", " + selDate.getDate() + "/" + (selDate.getMonth()+1) + "/" + selDate.getFullYear();
		document.getElementById("editSelectedDate").innerHTML = days[selDate.getDay()]+", " + selDate.getDate() + "/" + (selDate.getMonth()+1) + "/" + selDate.getFullYear();	
		}*/
		var days= ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
		var getDat = clickAgendaItem.startDate; //.toString();
		//getDat = getDat.split(" ");
		//clickAgendaItem.startDate
		selDate = new Date(getDat);
//		if((selDate.getMonth()+1)>9){ dateM = (selDate.getMonth()+1);}else{ dateM = "0"+(selDate.getMonth()+1); }
//		if((selDate.getDate()+1)>9){ dateD = (selDate.getDate()+1);}else{ dateD = "0"+(selDate.getDate()+1); }
		if((selDate.getMonth()+1)>9){ dateM = (selDate.getMonth()+1);}else{ dateM = "0"+(selDate.getMonth()+1); }
		if((selDate.getDate()+1)>9){ dateD = (selDate.getDate());}else{ dateD = "0"+(selDate.getDate()); }

		selectedDate = days[selDate.getDay()]+", " + dateD + "/" + dateM + "/" + selDate.getFullYear();
		document.getElementById("editSelectedDate").innerHTML = selectedDate;
//		alert(selectedDate);
	};

	/**
	 * Called when user drops an agenda item into a day cell.
	 */
	function myAgendaDropHandler(eventObj){
		// Get ID of the agenda item from the event object
		var agendaId = eventObj.data.agendaId;
		// date agenda item was dropped onto
		var date = eventObj.data.calDayDate;
		// Pull agenda item from calendar
		var agendaItem = jfcalplugin.getAgendaItemById("#mycal",agendaId);		
		alert("You dropped agenda item " + agendaItem.title + 
			" onto " + date.toString() + ". Here is where you can make an AJAX call to update your database.");
	};
	
	/**
	 * Called when a user mouses over an agenda item	
	 */
	function myAgendaMouseoverHandler(eventObj){
		var agendaId = eventObj.data.agendaId;
		var agendaItem = jfcalplugin.getAgendaItemById("#mycal",agendaId);
		//alert("You moused over agenda item " + agendaItem.title + " at location (X=" + eventObj.pageX + ", Y=" + eventObj.pageY + ")");
	};
	/**
	 * Initialize jquery ui datepicker. set date format to yyyy-mm-dd for easy parsing
	 */
	$("#dateSelect").datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
		changeMonth: true,
		changeYear: true,
		showButtonPanel: true,
//		dateFormat: 'yy-mm-dd'
		dateFormat: 'dd/mm/yy'
	});
	
	/**
	 * Set datepicker to current date
	 */
	$("#dateSelect").datepicker('setDate', new Date());
	/**
	 * Use reference to plugin object to a specific year/month
	 */
	$("#dateSelect").bind('change', function() {
		var selectedDate = $("#dateSelect").val();
		var dtArray = selectedDate.split("/");
		var year = dtArray[2];
		// jquery datepicker months start at 1 (1=January)		
		var month = dtArray[1];
		// strip any preceeding 0's		
		month = month.replace(/^[0]+/g,"")		
		var day = dtArray[0];
		// plugin uses 0-based months so we subtrac 1
		jfcalplugin.showMonth("#mycal",year,parseInt(month-1).toString());
	});	
	/**
	 * Initialize previous month button
	 */
	$("#BtnPreviousMonth").button();
	$("#BtnPreviousMonth").click(function() {
		jfcalplugin.showPreviousMonth("#mycal");
		// update the jqeury datepicker value
		var calDate = jfcalplugin.getCurrentDate("#mycal"); // returns Date object
		var cyear = calDate.getFullYear();
		// Date month 0-based (0=January)
		var cmonth = calDate.getMonth();
		var cday = calDate.getDate();
		// jquery datepicker month starts at 1 (1=January) so we add 1
//		$("#dateSelect").datepicker("setDate",cyear+"-"+(cmonth+1)+"-"+cday);
		$("#dateSelect").datepicker("setDate",cday+"/"+(cmonth+1)+"/"+cyear);
		return false;
	});
	/**
	 * Initialize next month button
	 */
	$("#BtnNextMonth").button();
	$("#BtnNextMonth").click(function() {
		jfcalplugin.showNextMonth("#mycal");
		// update the jqeury datepicker value
		var calDate = jfcalplugin.getCurrentDate("#mycal"); // returns Date object
		var cyear = calDate.getFullYear();
		// Date month 0-based (0=January)
		var cmonth = calDate.getMonth();
		var cday = calDate.getDate();
		// jquery datepicker month starts at 1 (1=January) so we add 1
//		$("#dateSelect").datepicker("setDate",cyear+"-"+(cmonth+1)+"-"+cday);		
		$("#dateSelect").datepicker("setDate",cday+"/"+(cmonth+1)+"/"+cyear);		
		return false;
	});
	
	/**
	 * Initialize delete all agenda items button
	 */
	$("#BtnDeleteAll").button();
	$("#BtnDeleteAll").click(function() {	
		jfcalplugin.deleteAllAgendaItems("#mycal");	
		return false;
	});		
	
	/**
	 * Initialize iCal test button
	 */
	$("#BtnICalTest").button();
	$("#BtnICalTest").click(function() {
		// Please note that in Google Chrome this will not work with a local file. Chrome prevents AJAX calls
		// from reading local files on disk.		
		jfcalplugin.loadICalSource("#mycal",$("#iCalSource").val(),"html");	
		return false;
	});	

	/**
	 * Initialize add event modal form
	 */
	$("#add-event-form").dialog({
		autoOpen: false,
		height: 400,
		width: 400,
		modal: true,
		buttons: {
			'Add Event': function() {

				var leaveType = jQuery.trim($("#leaveType").val());
				var leaveReason = jQuery.trim($("#leaveReason").val());
				var isLeave = jQuery.trim($("#isLeave").val());
				var isLeaveCheck = document.getElementById("isLeave").checked;
				if(leaveType == ""){
					alert("Please select non working day.");
				}else if(leaveType == "Sunday" && isLeaveCheck==false){
					alert("Please select working day.");
				}/*else if(leaveReason == ""){
					alert("Please enter a short reason description into the \"reason\" field.");
				}*/else{
				
					var startDate = $("#startDate").val();
					var startDtArray = startDate.split("-");
					var startYear = startDtArray[0];
					// jquery datepicker months start at 1 (1=January)		
					var startMonth = startDtArray[1];		
					var startDay = startDtArray[2];
					// strip any preceeding 0's		
					startMonth = startMonth.replace(/^[0]+/g,"");
					startDay = startDay.replace(/^[0]+/g,"");
					var startHour = jQuery.trim($("#startHour").val());
					var startMin = jQuery.trim($("#startMin").val());
					var startMeridiem = jQuery.trim($("#startMeridiem").val());
					startHour = parseInt(startHour.replace(/^[0]+/g,""));
					if(startMin == "0" || startMin == "00"){
						startMin = 0;
					}else{
						startMin = parseInt(startMin.replace(/^[0]+/g,""));
					}
					if(startMeridiem == "AM" && startHour == 12){
						startHour = 0;
					}else if(startMeridiem == "PM" && startHour < 12){
						startHour = parseInt(startHour) + 12;
					}

					var endDate = $("#endDate").val();
					var endDtArray = endDate.split("-");
					var endYear = endDtArray[0];
					// jquery datepicker months start at 1 (1=January)		
					var endMonth = endDtArray[1];		
					var endDay = endDtArray[2];
					// strip any preceeding 0's		
					endMonth = endMonth.replace(/^[0]+/g,"");

					endDay = endDay.replace(/^[0]+/g,"");
					var endHour = jQuery.trim($("#endHour").val());
					var endMin = jQuery.trim($("#endMin").val());
					var endMeridiem = jQuery.trim($("#endMeridiem").val());
					endHour = parseInt(endHour.replace(/^[0]+/g,""));
					if(endMin == "0" || endMin == "00"){
						endMin = 0;
					}else{
						endMin = parseInt(endMin.replace(/^[0]+/g,""));
					}
					if(endMeridiem == "AM" && endHour == 12){
						endHour = 0;
					}else if(endMeridiem == "PM" && endHour < 12){
						endHour = parseInt(endHour) + 12;
					}
					
							// Start save data in database
			
		//	var urlName = "<?php //echo $baseurl; ?>"+"constCalendar/projectLeave/";
			var urlName = "ajaxConstructionCalendar.php";
			data = 'ccDate='+startDate+'&ccLeave='+leaveType+'&ccReason='+leaveReason+'&ccIsLeave='+isLeave+'&projectLeave='+1+'&prLeaveId='+0;
			ajaxPost("POST",urlName,data,'plNewId');
		// End save data in database
					
					
					//alert("Start time: " + startHour + ":" + startMin + " " + startMeridiem + ", End time: " + endHour + ":" + endMin + " " + endMeridiem);

					// Dates use integers
					var startDateObj = new Date(parseInt(startYear),parseInt(startMonth)-1,parseInt(startDay),startHour,startMin,0,0);
					var endDateObj = new Date(parseInt(endYear),parseInt(endMonth)-1,parseInt(endDay),endHour,endMin,0,0);

					// add new event to the calendar
					jfcalplugin.addAgendaItem(
						"#mycal",
						leaveType,
						startDateObj,
						endDateObj,
						false,
						{   plId: $("#plNewId").val(),
							plDate: startDate,
							projId: <?php echo $projId; ?>,
							prLeave: leaveType,
							plReason: leaveReason,
							plIsLeave: isLeave
						},
						{
							backgroundColor: "#4894DE",
							foregroundColor: "#FFF"
						}
					);
					
					$(this).dialog('close');

				}
				
			},
			Cancel: function() {
				$(this).dialog('close');
			}
			
		},
		open: function(event, ui){
			// initialize start date picker
			$("#startDate").datepicker({
				showOtherMonths: true,
				selectOtherMonths: true,
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				dateFormat: 'yy-mm-dd'
			});
			// initialize end date picker
			$("#endDate").datepicker({
				showOtherMonths: true,
				selectOtherMonths: true,
				changeMonth: true,
				changeYear: true,
				showButtonPanel: true,
				dateFormat: 'yy-mm-dd'
			});
			// initialize with the date that was clicked
			$("#startDate").val(clickDate);
			$("#endDate").val(clickDate);
			// initialize color pickers
			/*$("#colorSelectorBackground").ColorPicker({
				color: "#333333",
				onShow: function (colpkr) {
					$(colpkr).css("z-index","10000");
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$("#colorSelectorBackground div").css("backgroundColor", "#" + hex);
					$("#colorBackground").val("#" + hex);
				}
			});
			//$("#colorBackground").val("#1040b0");		
			$("#colorSelectorForeground").ColorPicker({
				color: "#ffffff",
				onShow: function (colpkr) {
					$(colpkr).css("z-index","10000");
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$("#colorSelectorForeground div").css("backgroundColor", "#" + hex);
					$("#colorForeground").val("#" + hex);
				}
			});
			//$("#colorForeground").val("#ffffff");				
			*/
			// put focus on first form input element
			$("#leaveType").focus();
		},
		close: function() {
			// reset form elements when we close so they are fresh when the dialog is opened again.
			$("#startDate").datepicker("destroy");
			$("#endDate").datepicker("destroy");
			$("#startDate").val("");
			$("#endDate").val("");
			$("#startHour option:eq(0)").attr("selected", "selected");
			$("#startMin option:eq(0)").attr("selected", "selected");
			$("#startMeridiem option:eq(0)").attr("selected", "selected");
			$("#endHour option:eq(0)").attr("selected", "selected");
			$("#endMin option:eq(0)").attr("selected", "selected");
			$("#endMeridiem option:eq(0)").attr("selected", "selected");			
			$("#leaveType").val("");
			$("#leaveReason").val("");
			//$("#colorBackground").val("#1040b0");
			//$("#colorForeground").val("#ffffff");
		}
	});
	
	/**
	 * Initialize display event form.
	 */
	$("#display-event-form").dialog({
		autoOpen: false,
		height: 400,
		width: 400,
		modal: true,
		buttons: {		
			Cancel: function() {
				$(this).dialog('close');
			},
			'Edit': function() {
				if(clickAgendaItem != null){
					jfcalplugin.deleteAgendaItemById("#mycal",clickAgendaItem.agendaId);
					//alert("Make your own edit screen or dialog!");
					var leaveType = jQuery.trim($("#editLeaveType").val());
					var leaveReason = jQuery.trim($("#editLeaveReason").val());
					var isLeave = jQuery.trim($("#editIsLeave").val());
					var isLeaveCheck = document.getElementById("editIsLeave").checked;
					if(leaveType == ""){
						alert("Please select non working day.");
					}else if(leaveType == "Sunday" && isLeaveCheck==false){
						alert("Please select working day.");
					}/*else if(leaveReason == ""){
						alert("Please enter a short reason description into the \"reason\" field.");
					}*/else{
						//alert(clickAgendaItem.startDate);
						var urlName = "ajaxConstructionCalendar.php";
							data = 'prLeaveId='+clickAgendaItem.data['plId']+'&ccDate='+clickAgendaItem.data['plDate']+'&ccLeave='+leaveType+'&ccReason='+leaveReason+'&ccIsLeave='+isLeave+'&projectLeave='+1;
							ajaxPost("POST",urlName,data,'plNewId');
							// add new event to the calendar
						jfcalplugin.addAgendaItem(
							"#mycal",
							leaveType,
							clickAgendaItem.startDate,
							clickAgendaItem.startDate,
							false,
							{   plId: clickAgendaItem.data['plId'],
								plDate: clickAgendaItem.startDate,
								projId: <?php echo $projId; ?>,
								prLeave: leaveType,
								plReason: leaveReason,
								plIsLeave: isLeave
							},
							{
								backgroundColor: "#4894DE",
								foregroundColor: "#FFF"
							}
						);
				
						$(this).dialog('close');

					}
				}
			
			},
			'Delete': function() {
				if(confirm("Do you want to delete this holiday?")){
					if(clickAgendaItem != null){
						jfcalplugin.deleteAgendaItemById("#mycal",clickAgendaItem.agendaId);
						//jfcalplugin.deleteAgendaItemByDataAttr("#mycal","myNum",42);
						//var urlName = "<?php echo $baseurl; ?>"+"constCalendar/deleteProjectLeave/";
						//data = 'ccDate='+clickAgendaItem.data['plDate'];
						var urlName = "ajaxConstructionCalendar.php";
						data = 'prLeaveId='+clickAgendaItem.data['plId']+'&ccDate='+clickAgendaItem.data['plDate']+'&deleteProjectLeave='+1;
						ajaxPost("POST",urlName,data,'plNewId');
					}
					$(this).dialog('close');
				}
			}			
		},
/*		open: function(event, ui){
			if(clickAgendaItem != null){
				var agendaId = clickAgendaItem.agendaId;
				var title = clickAgendaItem.title;
				var startDate = clickAgendaItem.startDate;
				var endDate = clickAgendaItem.endDate;
				var allDay = clickAgendaItem.allDay;
				var data = clickAgendaItem.data;
				// in our example add agenda modal form we put some fake data in the agenda data. we can retrieve it here.
				$("#display-event-form").append(
					"<br><b>" + title+ "</b><br><br>"		
				);				
				if(allDay){
					$("#display-event-form").append(
						"(All day event)<br><br>"				
					);				
				}else{
					$("#display-event-form").append(
						//"<b>Starts:</b> " + startDate + "<br>" +
						//"<b>Ends:</b> " + endDate + "<br><br>"				
					);				
				}
			
				// Start:- show custom tool tip content
				/*for (var propertyName in data) {
					$("#display-event-form").append("<b>" + propertyName + ":</b> " + data[propertyName] + "<br>");
				}*/
		/*		$("#display-event-form").append("<b>Project Id &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</b> " + data['projId'] + "<br><br>");
				$("#display-event-form").append("<b>Project Leave:</b> " +data['prLeave'] + "<br><br>");
				$("#display-event-form").append("<b>Leave Reason:</b> " + data['plReason'] + "<br><br>");
			
		// End:- show custom tool tip content	
						
			}		
		},*/
// Start:- Edit form content
		open: function(event, ui){
			if(clickAgendaItem != null){
				var agendaId = clickAgendaItem.agendaId;
				var title = clickAgendaItem.title;
				var startDate = clickAgendaItem.startDate;
				var endDate = clickAgendaItem.endDate;
				var allDay = clickAgendaItem.allDay;
				var data = clickAgendaItem.data;
				var leaveT='';
				var leaveFormat = new Array("Annual leave","Fixed long weekend","Industrial", "Public holiday","Raindays","Rostered day off","Picnic day");
				var editLeaveDay='';
				if(clickAgendaItem.data['prLeave']=="Sunday"){
					editLeaveDay = '<td width="38%" valign="top"><label>Working day<span class="req">*</span></label></td>                <td width="4%" valign="top">&nbsp;</td>                <td width="68%" valign="top"><input name="editIsLeave" type="checkbox" id="editIsLeave" value="1" checked="checked" style="margin-bottom:12px;" /> <input name="editLeaveType" type="hidden" id="editLeaveType" value="Sunday" /></td>';
				}else{
					for(i=0; i<7; i++){
						if(leaveFormat[i]==data['prLeave']){
							leaveT+="<option value='"+leaveFormat[i]+"' selected='selected'>"+leaveFormat[i]+"</option>";
						}else{
							leaveT+="<option value='"+leaveFormat[i]+"'>"+leaveFormat[i]+"</option>";						
						}
					}
					editLeaveDay = '<td width="38%" valign="top"><label>Non working day<span class="req">*</span></label></td>                <td width="4%" valign="top">&nbsp;</td>                <td width="68%" valign="top"><select id="editLeaveType" name="editLeaveType" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">                    <option value="">Add new leave</option>'+leaveT+'</select><input name="editIsLeave" type="hidden" id="editIsLeave" value="0" /></td>';
				}
				
				$("#display-event-form").append("<h2 id='editSelectedDate'></h2><p class='validateTips' style='color:red;'>* Fields are mandatory.</p>  <form>    <fieldset>      <table style='width:100%; padding:5px;'>        <tr>"+editLeaveDay+" </tr>        <tr>          <td valign='top'><label>Description</label></td>          <td valign='top'>&nbsp;</td>          <td valign='top'><textarea name='editLeaveReason' rows='5' class='text ui-widget-content ui-corner-all' id='editLeaveReason' style='margin-bottom:12px; width:95%; padding: .4em;'>" + data['plReason'] + "</textarea><input type='hidden' name='getProjId' id='getProjId' value='"+ data['projId'] +"'/> </td>        </tr></table>      <table style='width:100%; padding:5px; display:none;'>        <tr>          <td><label>Start Date</label>            <input type='text' name='startDate' id='startDate' value='' class='text ui-widget-content ui-corner-all' style='margin-bottom:12px; width:95%; padding: .4em;'/></td>          <td>&nbsp;</td>          <td><label>Start Hour</label>            <select id='startHour' class='text ui-widget-content ui-corner-all' style='margin-bottom:12px; width:95%; padding: .4em;'>              <option value='12' SELECTED>12</option>              <option value='1'>1</option>              <option value='2'>2</option>              <option value='3'>3</option>              <option value='4'>4</option>              <option value='5'>5</option>              <option value='6'>6</option>              <option value='7'>7</option>              <option value='8'>8</option>              <option value='9'>9</option>              <option value='10'>10</option>              <option value='11'>11</option>            </select>          <td>          <td><label>Start Minute</label>            <select id='startMin' class='text ui-widget-content ui-corner-all' style='margin-bottom:12px; width:95%; padding: .4em;'>              <option value='00' SELECTED>00</option>              <option value='10'>10</option>              <option value='20'>20</option>              <option value='30'>30</option>              <option value='40'>40</option>              <option value='50'>50</option>            </select>          <td>          <td><label>Start AM/PM</label>            <select id='startMeridiem' class='text ui-widget-content ui-corner-all' style='margin-bottom:12px; width:95%; padding: .4em;'>              <option value='AM' SELECTED>AM</option>              <option value='PM'>PM</option>            </select></td>        </tr>        <tr>          <td><label>End Date</label>            <input type='text' name='endDate' id='endDate' value='' class='text ui-widget-content ui-corner-all' style='margin-bottom:12px; width:95%; padding: .4em;'/></td>          <td>&nbsp;</td>          <td><label>End Hour</label>            <select id='endHour' class='text ui-widget-content ui-corner-all' style='margin-bottom:12px; width:95%; padding: .4em;'>              <option value='12' SELECTED>12</option>              <option value='1'>1</option>              <option value='2'>2</option>              <option value='3'>3</option>              <option value='4'>4</option>              <option value='5'>5</option>              <option value='6'>6</option>              <option value='7'>7</option>              <option value='8'>8</option>              <option value='9'>9</option>              <option value='10'>10</option>              <option value='11'>11</option>            </select>          <td>          <td><label>End Minute</label>            <select id='endMin' class='text ui-widget-content ui-corner-all' style='margin-bottom:12px; width:95%; padding: .4em;'>              <option value='00' SELECTED>00</option>              <option value='10'>10</option>              <option value='20'>20</option>              <option value='30'>30</option>              <option value='40'>40</option>              <option value='50'>50</option>            </select>          <td>          <td><label>End AM/PM</label>            <select id='endMeridiem' class='text ui-widget-content ui-corner-all' style='margin-bottom:12px; width:95%; padding: .4em;'>              <option value='AM' SELECTED>AM</option>              <option value='PM'>PM</option>            </select></td>        </tr>      </table>    </fieldset>  </form>");
			}		
		},
// End:- Edit form content				
		close: function() {
			// clear agenda data
			$("#display-event-form").html("");
		}
	});	 

	/**
	 * Initialize our tabs
	 */
	$("#tabs").tabs({
		/*
		 * Our calendar is initialized in a closed tab so we need to resize it when the example tab opens.
		 */
		show: function(event, ui){
			if(ui.index == 1){
				jfcalplugin.doResize("#mycal");
			}
		}	
	});

<?php
	if(isset($prLeaves) && !empty($prLeaves)){
		foreach($prLeaves as $pleave){ $d=explode('-',substr($pleave['plDate'],0,10));
		echo 'jfcalplugin.addAgendaItem(
				"#mycal",
				"'.$pleave['prLeave'].'",
				new Date('.$d[0].','.($d[1]-1).','.$d[2].',0,0,0,0),
				new Date('.$d[0].','.($d[1]-1).','.$d[2].',0,0,0,0),
				false,
				{	
					plId: "'.$pleave['plId'].'",
					plDate: "'.$pleave['plDate'].'",
					projId: "'.$pleave['projId'].'",
					prLeave: "'.$pleave['prLeave'].'",
					plReason: "'.$pleave['plReason'].'",
					plIsLeave: "'.$pleave['plIsLeave'].'"
				},
				{
					backgroundColor: "#4894DE",
					foregroundColor: "#FFFFFF"
				}
			); ';
		}
	}
?>

/*// Set Data for calender
jfcalplugin.addAgendaItem(
	"#mycal",
	"Fxbytes Pvt. Ltd.",
	new Date(2013,01,13,20,0,0,0),
	new Date(2013,01,13,23,59,59,999),
	false,
	{
		fname: "Raj",
		lname: "Jat",
		leadReindeer: "Test",
		myExampleDate: new Date()
	},
	{
		backgroundColor: "#FF0000",
		foregroundColor: "#FFFFFF"
	}	
);
*/
});

//Ajax
function ajaxPost(method,url,data,result){
	var xmlhttp; 
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	}else{// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
	    {
			if(xmlhttp.responseText!=""){
				document.getElementById(result).value = xmlhttp.responseText;
			}else{
				//document.getElementById(result).innerHTML='';
			}
			
		}
	}
	
	xmlhttp.open(method,url,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(data);
}



function daysInMonth(iMonth, iYear){
	var totalDay = 32 - new Date(iYear, iMonth, 32).getDate();
	for(i=0; i<totalDay; i++){
		today = new Date();	today.setDate(i);
		if(days[today.getDay()]=="Sunday"){
			TotalOffDay[i]=days[today.getDay()];
		}
	}
}

//daysInMonth(1, 2013);
//alert(TotalOffDay);

			
			
		//	var showDate = days[today.getDay()]+", " + day + "-" + month + "-" + obj.year;


</script>
      <p>&nbsp;</p>
      <div id="example" style="margin: auto; width:100%;">
        <div id="toolbar" class="ui-widget-header ui-corner-all" style="padding:3px; vertical-align: middle; white-space:nowrap; overflow: hidden;">
          <button id="BtnPreviousMonth">Previous Month</button>
          <button id="BtnNextMonth">Next Month</button>
          &nbsp;&nbsp;&nbsp;
          Date:
          <input type="text" id="dateSelect" size="20" readonly="readonly"/>
          &nbsp;&nbsp;&nbsp; 
          <!--button id="BtnDeleteAll">Delete All</button>
			<button id="BtnICalTest">iCal Test</button>
			<input type="text" id="iCalSource" size="30" value="extra/fifa-world-cup-2010.ics"/--> 
        </div>
        <br>
        
        <!--
		You can use pixel widths or percentages. Calendar will auto resize all sub elements.
		Height will be calculated by aspect ratio. Basically all day cells will be as tall
		as they are wide.
		-->
        <div id="mycal"><img src="<?php echo defaltPath;?>images/preload.gif" style="margin:20%" /></div>
      </div>
      <style>
	  #selectedDate, #editSelectedDate{ color:#039; text-align:center;}
	  </style>
      <!-- debugging-->
      <div id="calDebug"></div>
      <input type="hidden" value="<?php echo $plNewId; ?>" name="plNewId" id="plNewId" />
      <!-- Add event modal form -->
      <style type="text/css">
			//label, input.text, select { display:block; }
			fieldset { padding:0; border:0; margin-top:15px; }
			.ui-dialog .ui-state-error { padding: .3em; }
			.validateTips { border: 1px solid transparent; padding: 0.3em; }
		</style>
      <div id="add-event-form" title="Add New Leave" style="display:none;">
        <h2 id="selectedDate"></h2>
        <p class="validateTips" style="color:red;">* Fields are mandatory.</p>
        <form>
          <fieldset>
            <table style="width:100%; padding:5px;">
              <tr id="leaveDay">
                <td width="38%" valign="top"><label>Non working day<span class="req">*</span></label></td>
                <td width="4%" valign="top">&nbsp;</td>
                <td width="68%" valign="top"><select id="leaveType" name="leaveType" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
                    <option value="">Add new leave</option>
                    <option value="Annual leave">Annual leave</option>
                    <option value="Fixed long weekend">Fixed long weekend</option>                    
                    <option value="Industrial">Industrial</option>
                    <option value="Public holiday">Public holiday</option>
                    <option value="Raindays">Raindays</option>
                    <option value="Rostered day off">Rostered day off</option>
                    <option value="Picnic day">Picnic day</option>
                  </select>
                  <input name="isLeave" type="hidden" id="isLeave" value="0" />
                  </td>
              </tr>
              <tr>
                <td valign="top"><label>Description</label></td>
                <td valign="top">&nbsp;</td>
                <td valign="top"><textarea name="leaveReason" rows="5" class="text ui-widget-content ui-corner-all" id="leaveReason" style="margin-bottom:12px; width:95%; padding: .4em;"></textarea>
              </tr>
              <!--tr style="display:none;">
                <td colspan="3" align="left" valign="middle"><label>
                    <input name="isLeave" type="checkbox" class="text ui-widget-content ui-corner-all" id="leaveReason2" style="margin-bottom:10px; padding: .4em;" value="1" />
                    <span style="">Do you want to work on this holiday?</span></label></td>
              </tr-->
            </table>
            <table style="width:100%; padding:5px; display:none;">
              <tr>
                <td><label>Start Date</label>
                  <input type="text" name="startDate" id="startDate" value="" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;"/></td>
                <td>&nbsp;</td>
                <td><label>Start Hour</label>
                  <select id="startHour" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
                    <option value="12" SELECTED>12</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                  </select>
                <td>
                <td><label>Start Minute</label>
                  <select id="startMin" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
                    <option value="00" SELECTED>00</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                    <option value="50">50</option>
                  </select>
                <td>
                <td><label>Start AM/PM</label>
                  <select id="startMeridiem" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
                    <option value="AM" SELECTED>AM</option>
                    <option value="PM">PM</option>
                  </select></td>
              </tr>
              <tr>
                <td><label>End Date</label>
                  <input type="text" name="endDate" id="endDate" value="" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;"/></td>
                <td>&nbsp;</td>
                <td><label>End Hour</label>
                  <select id="endHour" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
                    <option value="12" SELECTED>12</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                  </select>
                <td>
                <td><label>End Minute</label>
                  <select id="endMin" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
                    <option value="00" SELECTED>00</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                    <option value="50">50</option>
                  </select>
                <td>
                <td><label>End AM/PM</label>
                  <select id="endMeridiem" class="text ui-widget-content ui-corner-all" style="margin-bottom:12px; width:95%; padding: .4em;">
                    <option value="AM" SELECTED>AM</option>
                    <option value="PM">PM</option>
                  </select></td>
              </tr>
            </table>
          </fieldset>
        </form>
      </div>
      <div id="display-event-form" title="View Leave"> </div>
      <p>&nbsp;</p>
</div>      
<!-- End Calendar  -->     
<script type="text/javascript">
function validateSubmit(){
	val=document.getElementById("csvFile").value;
	if(val==""){
		alert("Please select file!");
		return false
	}else{
		
		n=val.search(/.csv/i);
		if(n<0){
			alert("Please provide only CSV file!");
			return false;
		}else{
			t=confirm('Do you want to upload "Project Leave CSV" ?');
			if(t==true){
				document.forms["csvProjectLeave"].submit();	
				return true;
			}else{
				return false;
			}
		}
	}
}


</script>
