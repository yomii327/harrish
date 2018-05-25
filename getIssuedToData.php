<?php
session_start();

include('includes/commanfunction.php');
$obj= new COMMAN_Class();

if (isset($_SESSION["ww_is_builder"])){
	$owner_id = $_SESSION['ww_builder_id'];	
}elseif (isset($_SESSION['ww_owner_id'])){
	$owner_id = $_SESSION['ww_owner_id'];
}elseif ($_SESSION['ww_is_company']){
	$owner_id = "company";
}

$limitStart = $_REQUEST['limitStart'];
$limitStart = (empty($limitStart))?$limitStart:($limitStart+1);
$limitEnd = 50;

#Get project issued to.
$issueToProjData = $obj->selQRYMultiple('master_contact_id, issue_to_name, company_name', 'inspection_issue_to', " project_id = '".$_SESSION['idp']."' and is_deleted = '0' and issue_to_name != '' ORDER BY company_name, issue_to_name");
$proIssuedToArr = array();
if(isset($issueToProjData) && !empty($issueToProjData)) {
	foreach($issueToProjData as $iRows) {
		$proIssuedToArr[] = $iRows['master_contact_id'];
	}
}

#Get all issued to data

$searchValue = $_REQUEST['searchValue'];
$search ='';
if($searchValue !="")
{
	$search = " AND (company_name LIKE '%$searchValue%' OR issue_to_name LIKE '%$searchValue%') ";

}

$issueToData = $obj->selQRYMultiple('contact_id, issue_to_name, company_name', 'master_issue_to_contact', " is_deleted = '0' and issue_to_name != ''  ".$search." ORDER BY company_name, issue_to_name LIMIT ". $limitStart. ','. $limitEnd);
$result = '';
foreach($issueToData as $issueTo) {
	if(!in_array($issueTo['contact_id'], $proIssuedToArr)) {
		if($issueTo['company_name'] !== ''){
			$issueToName =  $issueTo['company_name']." (".$issueTo['issue_to_name']. ')';
			$result .= '<li class="ui-state-default ui-element ui-draggable" data-index="'. $issueTo['contact_id'] .'"><span class="ui-helper-hidden"></span><a id="uiAddElement_'. $issueTo['contact_id'] .'" onClick="addElement(this.id)" href="javascript:void(0);" class="title ui-add-element">'. $issueToName .'</a><a href="javascript:void(0);" class="ui-state-default"><span class="ui-corner-all ui-icon ui-icon-plus"></span></a></li>';
		} else {
			$issueToName =  $issueTo['issue_to_name'];
			$result .= '<li class="ui-state-default ui-element ui-draggable" data-index="'. $issueTo['contact_id'] .'"><span class="ui-helper-hidden"></span><a id="uiAddElement_'. $issueTo['contact_id'] .'" onClick="addElement(this.id)" href="javascript:void(0);" class="title ui-add-element">'. $issueToName .'</a><a href="javascript:void(0);" class="ui-state-default"><span class="ui-corner-all ui-icon ui-icon-plus"></span></a></li>';
		}
	}
}
echo $result;

/* Omit PHP closing tags to help avoid accidental output */
