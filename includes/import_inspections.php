<?php
require_once'includes/functions.php';
$object = new DB_Class();
session_start();
include_once("commanfunction.php");
$obj = new COMMAN_Class();

$createdBy = $_SESSION['ww_builder']['user_id'];

ini_set('auto_detect_line_endings', true);
include('func.php');
if(isset($_POST['sessionBack']) && $_POST['sessionBack'] == 'Y'){
	$_SESSION['qc'] = $_POST;
}
if((!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1) && (!isset($_SESSION['ww_is_company']) || $_SESSION['ww_is_company']!=1)){?>
<script language="javascript" type="text/javascript">window.location.href="<?=HOME_SCREEN?>";</script>
<?php
}
/*
#$insertInspectionGraphics = "INSERT INTO inspection_graphics inspection_id = '".."', project_id = '".$_SESSION['idp']."', graphic_type = '', graphic_name = '', created_date = 'NOW()', created_by = '".$createdBy."'";
*/
if(isset($_FILES['csvFile']['tmp_name'])){
#	if(isset($_POST['location_csv_x'])){
		if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name'])){
			$filename = $_FILES['csvFile']['name']; // Csv File name
			$file_ext = explode('.',$filename);
			$ext = $file_ext[1];
			if($ext == 'csv' || $ext == 'CSV'){
				$files=$_FILES['csvFile']['tmp_name'];		
				$csvfile = $files; //CSV file name
				$file = fopen($csvfile,"r");
				$size = filesize($csvfile); //check file record
				if(!$size) {
					echo "File is empty.\n";
					exit;
				}
				$fieldarray= array();

				while( ($data =  fgetcsv($file,1000,",")) != FALSE) 
				{
				      $numOfCols = count($data);
				      for ($index = 0; $index < $numOfCols; $index++)
				      {
					  $data[$index] = stripslashes(normalise($data[$index]));
				      }
				      $fieldarray[] = $data;
				}
				fclose($file);
				$num=count($fieldarray)-1;
				$count=0;
				//get location's ids
				//get inserted locations
				$location_ids = array();
				for($i=1;$i<=$num;$i++) //read second line beacuse first line cover headings
				{
					if(!empty($fieldarray[$i][0]))
					{
					    $location_title = $fieldarray[$i][0];
					    if (!isset ($location_ids[$location_title]))
					    {
							$location_ids[$location_title] = getLocationId($location_title, $_SESSION['idp'], $createdBy);
					    }
					}
				}
				$farr = array(); // set array for parent id
				$issued_ids = array();
				for($i=1; $i<=$num; $i++){ //read second line beacuse first line cover headings
					if(!empty(trim($fieldarray[$i][9]))){
						$filedcount = count($fieldarray[$i]);
						$date_raised =explode('/',$fieldarray[$i][1]);
						if(strlen($date_raised[2])==2){
							$date_raised[2]='20'.$date_raised[2];
						}
						$date_raised_str =$date_raised[2].'-'.$date_raised[1].'-'.$date_raised[0];
						//inspectionId generator Dated 27-08-2012
						$preInspectionID = $obj->getDataByKey('unique_inspectionid', 'is_deleted', '0', 'MAX(inspectionid)');
						if($preInspectionID){
							$inspectionID = $obj->selQRY('MAX(inspectionid) as inspectionid', 'unique_inspectionid', 'is_deleted = 0');
							$rs = $object->db_query("INSERT INTO unique_inspectionid SET
							inspectionid = '".++$inspectionID['inspectionid']."',
							project_id = '".$_SESSION['idp']."',
							user_id = '".$_SESSION['ww_builder']['user_id']."',
							last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
							created_date = NOW(),
							created_by = '".$_SESSION['ww_builder']['user_id']."'");
							if($rs){
								$newInspectionID = mysql_insert_id();
							}
						}else{
							$inspectionID = $obj->selQRY('MAX(inspection_id) as inspectionid', 'project_inspections', 'is_deleted = 1');
							$rs = $object->db_query("INSERT INTO unique_inspectionid SET
							inspectionid = '".$inspectionID['inspectionid']."',
							project_id = '".$_SESSION['idp']."',
							user_id = '".$_SESSION['ww_builder']['user_id']."',
							last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
							last_modified_date = NOW(),
							created_date = NOW(),
							created_by = '".$_SESSION['ww_builder']['user_id']."'");
							if($rs){
								$newInspectionID = mysql_insert_id();
							}
						}
						$checkListIDs = $obj->selQRYMultiple('check_list_items_id, project_id', 'check_list_items', 'project_id = '.$_SESSION['idp'].' AND is_deleted = 0');
						if(!empty($checkListIDs)){
							foreach($checkListIDs as $checkListId){
								$object->db_query("INSERT INTO inspection_check_list SET
								project_id = '".$_SESSION['idp']."',
								inspection_id = '".$newInspectionID."',
								check_list_items_id = '".$checkListId['check_list_items_id']."',
								check_list_items_status = 'NA',
								last_modified_date = NOW(),
								last_modified_by = '".$_SESSION['ww_builder']['user_id']."',
								created_date = NOW(),
								created_by = '".$_SESSION['ww_builder']['user_id']."'");
							}
						}
						//inspectionId generator Dated 27-08-2012
						$insertInspection = "INSERT INTO project_inspections SET
								inspection_id = '".$newInspectionID."',
								project_id = \"".addslashes(trim($_SESSION['idp']))."\",
								location_id = \"".addslashes(trim($location_ids[$fieldarray[$i][0]]))."\",
								inspection_inspected_by = \"".addslashes(trim($fieldarray[$i][2]))."\",
								inspection_date_raised = \"".addslashes(trim($date_raised_str))."\",
								inspection_raised_by = \"".addslashes(trim($fieldarray[$i][4]))."\",
								inspection_type = \"".addslashes(trim($fieldarray[$i][3]))."\",
								inspection_description = \"".addslashes(trim($fieldarray[$i][9]))."\",
								inspection_notes = \"".addslashes(trim($fieldarray[$i][10]))."\",
								inspection_location = \"".addslashes(trim($fieldarray[$i][0]))."\",
								last_modified_date = NOW(),
								created_date = NOW(),
								last_modified_by = \"".$createdBy."\",
								created_by = \"".$createdBy."\"";
						mysql_query($insertInspection);
						$newInspectionId = $newInspectionID;

						$issueTo = explode('>', $fieldarray[$i][5]);
						$fixedByDate = explode('>', $fieldarray[$i][6]);
						$costAttribute = explode('>', $fieldarray[$i][7]);
						$status = explode('>', $fieldarray[$i][8]);

						$issueToLoop = max(count($issueTo), count($fixedByDate), count($costAttribute), count($status));
						for($k=0; $k<$issueToLoop; $k++){
							$fixedByDate_a =explode('/',trim($fixedByDate[$k]));
							if(strlen($fixedByDate_a[2])==2){
								$fixedByDate_a[2]='20'.$fixedByDate_a[2];
							}
							$fixed_by_date_str =$fixedByDate_a[2].'-'.$fixedByDate_a[1].'-'.$fixedByDate_a[0];

							$insertInspectionIssueTo = "INSERT INTO issued_to_for_inspections SET
									inspection_id = \"".$newInspectionId."\",
									project_id = \"".$_SESSION['idp']."\",
									issued_to_name = \"".addslashes(trim($issueTo[$k]))."\",
									inspection_fixed_by_date = \"".addslashes(trim($fixed_by_date_str))."\",
									cost_attribute = \"".addslashes(trim($costAttribute[$k]))."\",
									inspection_status = \"".addslashes(trim($status[$k]))."\",
									last_modified_date = NOW(),
									created_date = NOW(),
									last_modified_by = \"".$createdBy."\",
									created_by = \"".$createdBy."\"";
							$issued_ids[trim($issueTo[$k])] = 1;
							mysql_query($insertInspectionIssueTo);
						}
					}
				}
				foreach ($issued_ids as $key => $value){
					$issueToCheck = $obj->getRecords('inspection_issue_to', 'issue_to_name', $key, 'project_id', $_SESSION['idp'], 'issue_to_id');
					if($issueToCheck == ''){
						$masterIssueContactID = $masterIssueID = 0;
						$isDefault = 1;
						$masterIssueTo = $obj->selQRYMultiple('*', 'master_issue_to', 'is_deleted=0 AND issue_to_name = "'.trim($key).'"');

						if(empty($masterIssueTo[0]['id'])){
		    				$issueToMasterQry = "INSERT INTO master_issue_to SET 
								issue_to_name = '".addslashes(trim($key))."',
								last_modified_date = NOW(),
								last_modified_by = '".$createdBy."',
								created_date = NOW(),
								created_by = '".$createdBy."',
								resource_type = 'Webserver',
								is_deleted = 0,
								tag = '',
								activity = '',
								trade =''";
							mysql_query($issueToMasterQry);
							$masterIssueID = mysql_insert_id();
							$issueToContactQry = "INSERT INTO master_issue_to_contact SET 
										master_issue_id = '".$masterIssueID."',
										issue_to_name = '".trim($key)."',
										last_modified_date = NOW(),
										last_modified_by = '".$createdBy."',
										created_date = NOW(),
										created_by = '".$createdBy."',
										resource_type = 'Webserver',
										is_deleted = 0,
										tag = '',
										activity = '',
										is_default = '".$isDefault."',
										trade =''";
								mysql_query($issueToContactQry);
								$masterIssueContactID = mysql_insert_id();

							$insertInspectionIssueTo = "INSERT INTO inspection_issue_to SET
								master_issue_id=".$masterIssueID.",
								master_contact_id=".$masterIssueContactID.",
								project_id = \"".$_SESSION['idp']."\",
								issue_to_name = \"".addslashes(trim($key))."\",
								last_modified_date = NOW(),
								created_date = NOW(),
								last_modified_by = \"".$createdBy."\",
								is_deleted = 0,
								created_by = \"".$createdBy."\"";
							mysql_query($insertInspectionIssueTo);
		    			}else{
		    				$dataId=0;
		    				foreach ($masterIssueTo as $key=>$value1) {
		    					if(!empty($value1['issue_to_email'])){
		    						$dataId=$key;
		    					}
		    				}

		    				$masterContact = $obj->selQRYMultiple('*', 'master_issue_to_contact', 'is_deleted=0 AND master_issue_id '.$masterIssueTo[$dataId]['id']);

		    				if(isset($masterContact[0]) AND !empty($masterContact[0])){
		    					$masterIssueContactID = $masterContact[0]['contact_id'];
		    				}else{
		    					$issueToContactQry = "INSERT INTO master_issue_to_contact SET 
									master_issue_id = '".$masterIssueTo[$dataId]['id']."',
									issue_to_name = \"".addslashes(trim($key))."\",
									company_name = '".$masterIssueTo[$dataId]['company_name']."',
									issue_to_phone = '".$masterIssueTo[$dataId]['issue_to_phone']."',
									issue_to_email = '".$masterIssueTo[$dataId]['issue_to_email']."',
									last_modified_date = NOW(),
									last_modified_by = '".$createdBy."',
									created_date = NOW(),
									created_by = '".$createdBy."',
									resource_type = 'Webserver',
									is_deleted = 0,
									tag = '".$masterIssueTo[$dataId]['tag']."',
									activity = '".$masterIssueTo[$dataId]['activity']."',
									is_default = '".$isDefault."',
									trade ='".$masterIssueTo[$dataId]['trade']."'";
								mysql_query($issueToContactQry);
								$masterIssueContactID = mysql_insert_id();
		    				}

		    				if(!empty($masterIssueTo[$dataId]['id']) && !empty($masterIssueContactID)){
				    			$insertInspectionIssueTo = "INSERT INTO inspection_issue_to SET
									master_issue_id=".$masterIssueTo[$dataId]['id'].",
									master_contact_id=".$masterIssueContactID.",
									issue_to_name = \"".addslashes(trim($key))."\",
									company_name = '".$masterIssueTo[$dataId]['company_name']."',
									issue_to_phone = '".$masterIssueTo[$dataId]['issue_to_phone']."',
									issue_to_email = '".$masterIssueTo[$dataId]['issue_to_email']."',
									project_id = \"".$_SESSION['idp']."\",
									last_modified_date = NOW(),
									created_date = NOW(),
									last_modified_by = \"".$createdBy."\",
									tag = '".$masterIssueTo[$dataId]['tag']."',
									activity = '".$masterIssueTo[$dataId]['activity']."',
									trade = '".$masterIssueTo[$dataId]['trade']."',
									is_default = '".$isDefault."',
									is_deleted = 0,
									created_by = \"".$createdBy."\"";
								mysql_query($insertInspectionIssueTo);
							}
			    		}
					}
				}
				
				@mysql_close($con); //close db connection
				
				$msg1= "$num record(s) inserted.";
			}else{
				$err_msg= 'Please select .csv file.';
			}
		}else{
			$err_msg= 'Please select file.';
		}
	}
?>
<div id="middle" style="padding-top:10px;">
	<div id="rightCont" style="float:left;width:700px;">
		<div class="content_hd1" style="width:500px;margin-top:12px;">
	  		<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font>
			<a style="float:right;"><button onclick="history.go(-1);" class="green_small">Back </button></a>
			
		</div>
		<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;">
			<?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { ?>
				<div class="success_r" style="height:35px;width:185px;"><p><?php echo $_SESSION['add_project'] ; ?></p></div>
			<?php unset($_SESSION['add_project']);} ?><?php if((isset($msg1)) && (!empty($msg1))) { ?>
				<div class="success_r" style="height:35px;width:185px;"><p><?php echo $msg1; ?></p></div>
			<?php }
				if((isset($err_msg)) && (!empty($err_msg))) { ?>
				<div class="failure_r" style="height:35px;width:185px;"><p><?php echo $err_msg; ?></p></div>
			<?php } ?>
		</div>
		<div class="content_container" style="float:left;width:690px;border:1px solid; text-align:center;margin-left:10px;margin-right:10px;height:80px;">
			<div style="width:722px; height:70px; float:left; margin-top:5px;">
        		<form method="post" name="csvLocation" id="csvLocation" enctype="multipart/form-data">
				<table width="690px" border="0" cellspacing="0" cellpadding="3">
					<tr>
						<td colspan="4" align="left">
							<a href="/csv/Import_Inspection_Template.csv" target="_blank" style="text-decoration:none;color:#FFF;"><strong style="font-size:16px;">Click here to download CSV template</a>
						</td>
					</tr>   
					<tr>
						<td width="185px;" align="left">&nbsp;</td>
						<td width="130px;">Upload&nbsp;CSV&nbsp;File&nbsp;:</td>
						<td width="240px;" align="left"><input type="file" name="csvFile" id="scvFile" value="" /></td>
						<td width="120px;" height="50px"><input style="height: 30px;margin-left: 0;" type="button" name="location_csv" id="location_csv" class="green_small" value="Import CSV" onclick="validateSubmit();"/></td>
					</tr>
				</table>
				</form>
			</div>
		</div>
		<div class="big_container" style="width:722px;float:left;" ><?php #include'csv_table.php';?></div>
	</div>
</div>

<?php
function normalise($string) {
       $string = str_replace("\r", "\n", $string);
       
       return $string;	
}
function getLocationId($location, $project_id,$createdBy)
{
    $locations = explode (">", $location);
    $parent_id = 0;
    for ($i=0;$i<count($locations);$i++)
    {
        $location_title = trim ($locations[$i]);
        $query = "select location_id from project_locations where project_id=".$project_id." and location_title='" . $location_title."' and location_parent_id=" . $parent_id . " and is_deleted=0";
        $rs = mysql_query($query);
        if(mysql_num_rows($rs) > 0)
        {
            $row = mysql_fetch_array ($rs);
            $parent_id = $row[0];
        }
        else{//if location not found add all locations
            for ($j=$i;$j<count($locations);$j++)
            {
                $query = "insert into project_locations set 	project_id=\"".$project_id."\",
								location_title=\"".addslashes(trim($locations[$j]))."\",
								location_parent_id=\"". $parent_id."\",
								last_modified_date = NOW(),
								created_date = NOW(),
								last_modified_by = \"".$createdBy."\",
								created_by = \"".$createdBy."\"";

                mysql_query($query);
                $parent_id = mysql_insert_id();
                
                $obj1 = new COMMAN_Class();
                $locIdTree = $obj1->subLocationsIDS($parent_id, ' > ');
				$locNameTree = $obj1->subLocations($parent_id, ' > ');
				$query = 'UPDATE project_locations SET location_id_tree = "'.$locIdTree.'", location_name_tree = "'.$locNameTree.'", last_modified_date = NOW() WHERE location_id = '.$parent_id;
				mysql_query($query);
            }
            break;
        }
    }
    return $parent_id;
}
?>
<script type="text/javascript">
function validateSubmit(){
	var r = jConfirm('Do you want to upload "Inspections CSV" ?', null, function(r){
		if (r === true){
			document.forms["csvLocation"].submit();	
		}else{
			return false;
		}
	});
}
</script>