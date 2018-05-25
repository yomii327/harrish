<?php
//path to directory to scan
$images=array();
$directory = "png_img/";
$target = "jpg_img/";
 
//get all image files with a .jpg extension.
$images = glob($directory . "*.png");
$images=str_replace($directory,'',$images);
for($p=0;$p<sizeof($images);$p++){
	//echo $images[$p];
	echo "<br /><br />";
	
	$myfile = explode('.', $images[$p]);
	$e = 'jpg';
	$myFileName = $myfile[0] .'.'. $e;
	echo $photo = $target . $myFileName;
	move_uploaded_file($directory.$myfile[0], $photo);
	
	
	
/*	$myfile = explode('.', $n);
	$file = md5($this->rendom());
	$e = 'jpg';
	$myFileName = $file .'.'. $e;
	$photo = $d . $myFileName;
	move_uploaded_file($t, $photo);*/
}
?>