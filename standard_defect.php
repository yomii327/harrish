<?php
include_once("commanfunction.php");
$obj = new COMMAN_Class();
ini_set('auto_detect_line_endings', true);
 include('func.php');
if(!isset($_SESSION['ww_is_builder']) || $_SESSION['ww_is_builder'] != 1){
?>
<script language="javascript" type="text/javascript">
window.location.href="<?=HOME_SCREEN?>";
</script>
<?php
}
 
 $builder_id=$_SESSION['ww_builder_id'];

if(isset($_POST['location_csv_x'])){ // Location/ subloaction import CSV file.

	if(isset($_FILES['csvFile']['name']) && !empty($_FILES['csvFile']['name']))
	{
		$filename=$_FILES['csvFile']['name']; // Csv File name
		$file_ext=explode('.',$filename);
		$ext=$file_ext[1];
		if($ext=='csv' || $ext=='CSV')
		{
			$path=$_SERVER['DOCUMENT_ROOT'].'/csv/temp/'; // CSV Path
			$upload=move_uploaded_file($_FILES['csvFile']['tmp_name'],$path.$filename);
			if($upload)
			{
				
				$fieldseparator = ","; // CSV file comma format
				$lineseparator = "\n";
				$csvfile = $path.$filename; //CSV file name
				$addauto = 1;
				$save = 1;
				/********************************/
				
				$file = fopen($csvfile,"r");
				
				$size = filesize($csvfile); //check file record
				
				if(!$size) {
				echo "File is empty.\n";
				exit;
				}
				//$csvcontent = fread($file,$size);
				//fclose($file);
				$lines = 0;
				$queries = "";
				$linearray = array();
				$fieldarray= array();
				$record='';
				//$csvcontent = str_replace("\r","\n",$csvcontent);
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
				$num=count($fieldarray);
				$count=0;
				
				for($i=1;$i<$num;$i++) //read second line beacuse first line cover headings
				{
					
					
					if(!empty($fieldarray[$i][0]))
					{
							
							$desc=$fieldarray[$i][0];
							$fieldarray[$i][0]='';
							$tags=implode(';',$fieldarray[$i]);
							
							$tags=ltrim($tags,";");
							
							
							$creatdate=date('Y-m-d H:i:s');	
							@$insert="INSERT INTO standard_defects SET
											project_id = ".$_SESSION['idp'].",
											description = '".$desc."',
											tag = '".$tags."',
											last_modified_date = NOW(),
											last_modified_by = ".$builder_id.",
											created_date = NOW(),
											created_by = ".$builder_id;
				
					//echo $insert; die;
							mysql_query($insert);
							$success='File uploaded successfully.';
						
					}
				}
				
				
				
				
						
				@mysql_close($con); //close db connection
				
				if(isset($count) && !empty($count))
				{
					$success="Total $count Duplicate Records";
				}
				//$success.= "<br/>Total $record Duplicate Records.";
				$msg1= "<br/>Total $lines record(s) inserted.";
		}
		else
		{
			$err_msg='Please try again';// If file not uploaded
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



<!-- Ajax Post -->
<link href="style.css" rel="stylesheet" type="text/css" />
<style>
.list{border:1px solid; max-height:150px; -moz-border-radius:5px; border-radius:5px; padding:5px; overflow:auto;}

.box1 {
    background: -moz-linear-gradient(center top , #FFFFFF 0%, #E5E5E5 100%) repeat scroll 0 0 transparent;
    border: 1px solid #0261A1;
    color: #000000;
    float: left;
    height: auto;
    width: 211px;
}
.link1 {
    background-image: url("images/blue_arrow.png");
    background-position: 175px 34%;
    background-repeat: no-repeat;
    color: #000000;
    display: block;
    height: 25px;
    text-decoration: none;
    width: 202px;
}
a.link1:hover {
    background-color: #015F9F;
    background-image: url("images/white_arrow.png");
    background-position: 175px 34%;
    background-repeat: no-repeat;
    color: #FFFFFF;
    display: block;
    height: 25px;
    text-decoration: none;
    width: 202px;
}
.txt13 {
    border-bottom: 1px solid #333333;
    color: #000000;
    font-size: 12px;
    font-weight: bold;
    height: 30px;
}

</style>


<div id="middle" style="padding-top:10px;">

<div id="leftNav" style="width:250px;float:left;">
<table width="100%" border="0" align="left" cellpadding="5" cellspacing="0">
		<tr>
				<td width="24%" align="left" valign="top">
                <!--<a href="#" <?php //if($_GET['sect'] == 'o_dashboard'){echo 'class="left_btn1active"';}?>  class="left_btn1"><br />-->
				</a><br /><a href="pms.php?sect=project_configuration" <?php if($_GET['sect'] == 'project_configuration'){echo 'class="left_btn2active"';}?> class="left_btn2" ><br />
				</a><br /><a href="pms.php?sect=issue_to"  <?php if($_GET['sect'] == 'issue_to'){echo 'class="left_btn3active"';}?> class="left_btn3"><br />
				</a><br /><a href="pms.php?sect=standard_defect" <?php if($_GET['sect'] == 'standard_defect'){echo 'class="left_btn4active"';}?>  class="left_btn4"><br />
				</a><br /><a href="pms.php?sect=progress_monitoring" <?php if($_GET['sect'] == 'progress_monitoring'){echo 'class="left_btn5active"';}?> class="left_btn5" ><br />
				</a>
                </td>
				<td width="40%" valign="top"></td>
				
				<td width="21%" valign="top"><!--<a href="#"><img src="images/add_btn.png" width="65" height="26" vspace="20" /></a><br />
						<a href="#"><img src="images/remove_btn.png" width="65" height="27" /></a>--></td>
				
				</tr>
</table>
<?php $id=base64_encode($_SESSION['idp']);
$hb=base64_encode($_SESSION['hb']);
 ?>
</div>
<div id="rightCont" style="float:left;width:700px;">
  <div class="content_hd1" style="width:500px;margin-top:12px;">
	<font style="float:left;" size="+1">Project Name : <?php echo $projectName = $obj->getDataByKey('user_projects', 'project_id', $_SESSION['idp'], 'project_name')?></font>
	<a style="float:left;margin-top:-25px;margin-left:8px;" href="?sect=add_project_detail&id=<?php echo $id;?>&hb=<?php echo $hb;?>"><img src="images/back_btn2.png" style="border:none; width:87px;margin-left:584px;" /></a>
	</div>
	<div id="errorHolder" style="margin-left: 10px;margin-bottom: 6px;">
		<?php if((isset($_SESSION['add_project'])) && (!empty($_SESSION['add_project']))) { ?>
			<div class="success_r" style="height:35px;width:185px;"><p><?php echo $_SESSION['add_project'] ; ?></p></div>
		<?php unset($_SESSION['add_project']);} ?><?php if((isset($success)) && (!empty($success))) { ?>
			<div class="success_r" style="height:35px;width:185px;"><p><?php echo $success; ?></p></div>
		<?php }
			if((isset($err_msg)) && (!empty($err_msg))) { ?>
			<div class="failure_r" style="height:35px;width:185px;"><p><?php echo $err_msg; ?></p></div>
		<?php } ?>
	</div>
  
  <div class="content_container" style="float:left;width:690px;border:1px solid; margin-bottom:10px;text-align:center;margin-left:10px;margin-right:10px;height:80px;">
<!--First Box-->
<div style="width:722px; height:50px; float:left; margin-top:5px;">
<form method="post" name="csvIssueto" id="csvIssueto" enctype="multipart/form-data">
	<table width="690px" border="0" cellspacing="0" cellpadding="3">
		<tr>
			<td colspan="4" align="left"><a href="/csv/statndard_defects.csv" style="text-decoration:none;color:#FFF;"><strong style="font-size:16px;">Click here to download CSV template</strong></a></td>
		</tr>   
		<tr>
			<td width="185px;" align="left">&nbsp;</td>
			<td width="130px;">Upload&nbsp;CSV&nbsp;File&nbsp;:</td>
			<td width="240px;" align="left"><input type="file" name="csvFile" id="csvFile" value="" /></td>
			<td width="120px;" height="50px"><input type="image" src="images/import_csv_btn.png"  name="location_csv" id="location_csv" value="Import CSV" style="margin-left:10px;" /></td>
		</tr>
	</table>
</form><br />
<br />
<br clear="all" />
</div>



<!--End First Box-->


<!--Second Box-->
<!--End Second Box-->

<!--Projecct Box-->
<!--End Project Box-->
		</div>
<div class="big_container" style="width:722px;float:left;margin-left:10px;" >
	<?php include'standard_defect_csv_table.php';?>
</div>
</div>
</div>
