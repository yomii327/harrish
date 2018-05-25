<?php session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

include 'includes/commanfunction.php';
$object = new COMMAN_Class();

if($_REQUEST['task'] == 'update_issueto'){
	//$inspectionIssueTo = $object->selQRYMultiple('*', 'inspection_issue_to', 'is_deleted=0 AND master_issue_id =0 AND master_contact_id = 0');
	$inspectionIssueTo = $object->selQRYMultiple('*', 'inspection_issue_to', 'is_deleted=0 AND master_issue_id = 0 AND master_contact_id = 0 AND issue_to_name != "NA" AND issue_to_name != ""');
	//SELECT *  FROM `inspection_issue_to` WHERE (`master_issue_id` = 0 OR `master_contact_id` = 0) AND `issue_to_name` != 'NA' AND `issue_to_name` != '' AND `is_deleted` = 0
	#echo $count."====<pre>"; print_r($inspectionIssueTo); die;
	echo "<pre>";
	if(!empty($inspectionIssueTo)){
		$i = 1;
		foreach ($inspectionIssueTo as $value) {
			$masterIssueContactID = $masterIssueID = 0;
			if(!empty($value['issue_to_name']) && $value['issue_to_name'] != "NA" && empty($value['master_issue_id'])){
				$issueToId = $value['issue_to_id'];
				$masterIssueTo = $object->selQRYMultiple('*', 'master_issue_to', 'is_deleted=0 AND issue_to_name = "'.$value['issue_to_name'].'"');
				#print_r($masterIssueTo);
    			if(empty($masterIssueTo[0]['id'])){
    				$issueToMasterQry = "INSERT INTO master_issue_to SET 
						issue_to_name = '".addslashes($value['issue_to_name'])."',
						company_name = '".addslashes($value['company_name'])."',
						issue_to_phone = '".addslashes($value['issue_to_phone'])."',
						issue_to_email = '".addslashes($value['issue_to_email'])."',
						last_modified_date = NOW(),
						last_modified_by = '".$value['last_modified_by']."',
						created_date = NOW(),
						created_by = '".$value['created_by']."',
						resource_type = '".$value['resource_type']."',
						is_deleted = '".$value['is_deleted']."',
						tag = '".$value['tag']."',
						activity = '".$value['activity']."',
						trade ='".$value['trade']."'";
					mysql_query($issueToMasterQry);
					$masterIssueID = mysql_insert_id();
					
					$issueToContactQry = "INSERT INTO master_issue_to_contact SET 
								master_issue_id = '".$masterIssueID."',
								issue_to_name = '".addslashes($value['issue_to_name'])."',
								company_name = '".addslashes($value['company_name'])."',
								issue_to_phone = '".addslashes($value['issue_to_phone'])."',
								issue_to_email = '".addslashes($value['issue_to_email'])."',
								last_modified_date = NOW(),
								last_modified_by = '".$value['last_modified_by']."',
								created_date = NOW(),
								created_by = '".$value['created_by']."',
								resource_type = '".$value['resource_type']."',
								is_deleted = '".$value['is_deleted']."',
								tag = '".$value['tag']."',
								activity = '".$value['activity']."',
								is_default = '".$value['is_default']."',
								trade ='".$value['trade']."'";
						mysql_query($issueToContactQry);
						$masterIssueContactID = mysql_insert_id();

					$issueToUpdateQry = "UPDATE inspection_issue_to SET master_issue_id = ".$masterIssueID.",master_contact_id = ".$masterIssueContactID." WHERE issue_to_id = ".$issueToId;
					mysql_query($issueToUpdateQry);
					echo $i."==Update==<br>"; $i++;
    			}else{
    				$dataId=0;
    				foreach ($masterIssueTo as $key1=>$value1) {
    					if(!empty($value1['issue_to_email'])){
    						$dataId=$key1;
    					}
    				}
    				$masterIssueID = $masterIssueTo[$dataId]['id'];
    				$masterIssueToCont = $object->selQRYMultiple('*', 'master_issue_to_contact', 'is_deleted=0 AND master_issue_id='.$masterIssueID.' AND issue_to_name = "'.$value['issue_to_name'].'"');
					if(empty($masterIssueToCont[0]['id'])){
						$isDefault = 1;
						$issueToContactQry = "INSERT INTO master_issue_to_contact SET 
							master_issue_id = '".$masterIssueID."',
							issue_to_name = '".addslashes(trim($key))."',
							company_name = '".$masterIssueTo[$dataId]['company_name']."',
							issue_to_phone = '".$masterIssueTo[$dataId]['issue_to_phone']."',
							issue_to_email = '".$masterIssueTo[$dataId]['issue_to_email']."',
							last_modified_date = NOW(),
							last_modified_by = '".$masterIssueTo[$dataId]['created_by']."',
							created_date = NOW(),
							created_by = '".$masterIssueTo[$dataId]['created_by']."',
							resource_type = 'Webserver',
							is_deleted = 0,
							tag = '".$masterIssueTo[$dataId]['tag']."',
							activity = '".$masterIssueTo[$dataId]['activity']."',
							is_default = '".$isDefault."',
							trade ='".$masterIssueTo[$dataId]['trade']."'";
						mysql_query($issueToContactQry);
						$masterIssueContactID = mysql_insert_id();
					}else{
						$dataId1=0;
						foreach ($masterIssueToCont as $key2=>$value2) {
							if(!empty($value2['issue_to_email'])){
								$dataId1=$key2;
							}
						}
		    			$masterIssueContactID = $masterIssueTo[$dataId1]['contact_id'];
					}

	    			$issueToUpdateQry1 = "UPDATE inspection_issue_to SET master_issue_id = ".$masterIssueID.",master_contact_id = ".$masterIssueContactID.",issue_to_email = '".$masterIssueTo[$dataId1]['issue_to_email']."' WHERE issue_to_id = ".$issueToId;
	    			mysql_query($issueToUpdateQry1);
	    			echo $i."==UPDATE==<br>"; $i++;
	    		}
			}
		}
	}else{
		echo "Data not found";
	}
}
die;

?>