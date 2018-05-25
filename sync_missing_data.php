<?php
include_once("includes/commanfunction.php");
include('includes/func.php');
ini_set('auto_detect_line_endings', true);

resizeImages();

function resizeImages()
{
	$rWidth = 105;
	$rHeight = 66;
	$result = mysql_query("select graphic_name, graphic_type from inspection_graphics ig, user_projects up where up.project_id=ig.project_id and up.is_deleted=0 and ig.is_deleted=0");
	while ($row = mysql_fetch_array($result))
	{
		if ($row[1] == "images")
		{
			$resizeSource = "/var/www/fxdev/inspections/photo/" . $row[0];
			$resizeDest = "/var/www/fxdev/inspections/photo/photo_summary/" . $row[0];
		}else{
			$resizeSource = "/var/www/fxdev/inspections/drawing/" . $row[0];
			$resizeDest = "/var/www/fxdev/inspections/drawing/drawing_summary/" . $row[0];
		}
		if (file_exists ($resizeDest))
		{
			unlink ($resizeDest);
		}
		$simpleImage = new SimpleImage();
		if(file_exists($resizeSource)){
			$simpleImage->load($resizeSource);
			if($simpleImage->getWidth() < $rWidth){//Resize by Height
				$simpleImage->resizeToHeight($rHeight);
				$simpleImage->save($resizeDest);
			}else{//Resize by widtht
				$simpleImage->resizeToWidth($rWidth);
				$simpleImage->save($resizeDest);
			}
		}
		echo $resizeDest . "<br/>";
	}
}
die;

#for Progress Monitoring
$file = fopen("/var/www/hacer/progress_monitoring_rxerri.csv","r");
$size = filesize($file); //check file record
//echo $file . '-' . $size;
//if(!$size) {
//	echo "File is empty.\n";
//	exit;
//}
$lines = 0;
$queries = "";
$linearray = array();
$fieldarray= array();
$record='';

//while( ($line = fgets($file)) != FALSE) 
while( ($data =  fgetcsv($file,1000,",")) != FALSE) 
{
      $numOfCols = count($data);
      for ($index = 0; $index < $numOfCols; $index++)
      {
	  $data[$index] = stripslashes(normalise($data[$index]));
      }
      $fieldarray[] = $data;
}
$num = count($fieldarray);
for($i=0;$i<$num;$i++) //read second line beacuse first line cover headings
{
	$progress_id = $fieldarray[$i][12];
	$percentage = $fieldarray[$i][15];
	$status = $fieldarray[$i][14];
	if ($status != "")
	{
		echo $query = "update progress_monitoring set percentage='$percentage', status='$status', last_modified_date = NOW(), last_modified_by = ".$_SESSION['ww_builder_id']." where progress_id = $progress_id";
		echo "<br/>";
		$result=mysql_query($query);
	}
	//echo "progress_id: $progress_id == percentage: $percentage == status: $status <br/>";	
}

function normalise($string) {
	$string = str_replace("\r", "\n", $string);
	
	return $string;	
}

?>