<?php
ob_start();
session_start();
set_time_limit(999999999999999999999999999999999999999999);

include 'includes/commanfunction.php';
$object = new COMMAN_Class();

//Function Section
$fileName = 'user_name_data.csv';
function echocsv($fields){
	$op = '';
	$separator = '';
	foreach ($fields as $field){
		if(preg_match('/\\r|\\n|,|"/', $field)){
			$field = '"' . str_replace( '"', '""', $field ) . '"';
		}
		$op .= $separator . $field;
		$separator = ',';
	}
	$op .= "\r\n";
	return $op;
}
	
if(isset($_POST['DRF'])){
	$_POST['DRF'] != "" ? $DRF = $object->dateChanger('-', '-', $_POST['DRF']) : $DRF = '';
	$_POST['DRT'] != "" ? $DRT = $object->dateChanger('-', '-', $_POST['DRT']) : $DRT = '';
	$where = "";
	if($DRF != "" && $DRT != ""){
		$where = " AND pi.inspection_date_raised BETWEEN '".$DRF."' AND '".$DRT."'";
	}
	$projNameArr = array();	$projInspCount = array();
	$inspectionCountData = $object->selQRYMultiple('p.project_id, p.project_name, count(*) AS pcount', 'projects AS p, project_inspections AS pi', 'pi.is_deleted = 0 AND p.is_deleted = 0 AND p.project_id = pi.project_id'.$where.' GROUP BY pi.project_id ');
	
#print_r($inspectionCountData );die;
	$insStatusArr = array();
	foreach($inspectionCountData as $inspCountData){
		$projNameArr[$inspCountData['project_id']] = $inspCountData['project_name'];
		$projInspCount[$inspCountData['project_id']] = $inspCountData['pcount'];
		
		$inspectionData = $object->selQRYMultiple('SUM(IF(d.inspection_status = "Open", 1, 0)) AS open,
							SUM(IF(d.inspection_status = "Pending", 1, 0)) AS pending,
							SUM(IF(d.inspection_status = "Fixed", 1, 0)) AS fixed,
							SUM(IF(d.inspection_status = "Closed", 1, 0)) AS closed,
							pi.project_id AS projID',
							
							'issued_to_for_inspections AS d, project_inspections pi',
							
							'pi.inspection_id = d.inspection_id AND
							d.project_id = '.$inspCountData['project_id'].' AND
							pi.project_id = '.$inspCountData['project_id'].' AND
							d.is_deleted = 0 AND
							pi.is_deleted = 0 AND
							pi.inspection_type != "Memo" '.$where.' GROUP BY pi.project_id');	
		if(!empty($inspectionData)){
			$insStatusArr[$inspectionData[0]['projID']] = array(
					$inspectionData[0]['open'],
					$inspectionData[0]['pending'],
					$inspectionData[0]['fixed'],
					$inspectionData[0]['closed']
			);
		}
	}
	
	$output = '';
	$header = array('Project Name', 'Inspection Count', 'Open', 'Pending', 'Fixed', 'Closed');
	$output .= echocsv($header);
	
	foreach($insStatusArr as $key=>$value){
		$output .= 	echocsv(array($projNameArr[$key], $projInspCount[$key], $value[0], $value[1], $value[2], $value[3]));
	}
	
	header("Content-type: application/csv");
	header("Content-Disposition: attachment; filename=".$fileName);
	header("Pragma: no-cache");
	header("Expires: 0");
	
	print $output; die;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript" src="js/location_tree_jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.alerts.js"></script>
<link rel="stylesheet" href="jquery.alerts.css" type="text/css" media="screen" />
<link rel="stylesheet" type="text/css" media="all" href="css/jsDatePick_ltr.min.css" />
<script type="text/javascript" src="js/jsDatePick.min.1.3.js"></script>
<script type="text/javascript">
window.onload = function(){
	new JsDatePick({
		useMode:2,
		target:"DRF",
		dateFormat:"%d-%m-%Y"
	});
	new JsDatePick({
		useMode:2,
		target:"DRT",
		dateFormat:"%d-%m-%Y"
	});
};
</script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Inspection Count Details</title>
</head>
<body>
	<form action="" name="inspectionCount" method="post" >
		<table width="50%" border="1" align="center" cellpadding="5" cellspacing="5" style="margin-top:50px;">
			<tr>
				<td colspan="2" align="center">Date Raised</td>
			<tr>
			<tr>
				<td>
					From 
					<input name="DRF" type="text" size="7" id="DRF" readonly value="<?php if(isset($_SESSION['qc']['DRF'])){ echo $_SESSION['qc']['DRF']; }?>" />
					To 
					<input name="DRT" type="text" id="DRT" size="7" readonly value="<?php if(isset($_SESSION['qc']['DRT'])){ echo $_SESSION['qc']['DRT']; }?>" />
					<a href="javascript:void();" title="Clear date raised"><img src="images/redCross.png" onClick="clearDateRaised();" /></a>
				</td>
				<td><input type="button" name="submitButton" value="Submit" onclick="submitForm()" /></td>
			</tr>
		</table>
	</form>
<script type="text/javascript">
function submitForm(){
	console.log('werw');
	if(document.getElementById('DRF') != 'undefined'){
		var dateChackRaised = checkDates(document.getElementById('DRF'), document.getElementById('DRT'), 'In Date Raised Field');			
		if(dateChackRaised === false){	return false;	}
	
	}
	document.forms["inspectionCount"].submit();
}
function checkDates(date1, date2, element){
	var obj = date1.value;
	var obj1 =  date2.value;
	if(obj!='' || obj1!=''){
		if(obj=='' && obj1!=''){
			jAlert('Please Select Form Date First !');
			return false;		
		}else{
			var fromDate = new Date(obj.substr(6,4), obj.substr(3,2), obj.substr(0,2));
			var toDate = new Date(obj1.substr(6,4), obj1.substr(3,2), obj1.substr(0,2));
			if((toDate.getTime() - fromDate.getTime()) < 0){jAlert(element+' To Date in Not Less Than Form Date !');return false;}
		}
	}
}
function clearDateRaised(){ $('#DRF, #DRT').val(''); }
</script>
</body>
</html>