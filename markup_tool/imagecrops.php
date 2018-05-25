<?php
ini_set("display_errors", 1);
ini_set('memory_limit', '1024M');
print_r($_POST);
// Create image instances
$image_width  = $_POST['img_width'];
$image_height = $_POST['img_height'];

$source_x = $_POST['source_x'];
$source_y = $_POST['source_y'];

$src = imagecreatefromjpeg($_POST['fileName']);
//$src = imagecreatefrompng($_POST['fileName']);
$dest = imagecreatetruecolor($image_width, $image_height);

// Copy
imagecopy($dest, $src, 0, 0, $source_x, $source_y, $image_width, $image_height);

// Output and free from memory
header('Content-Type: image/jpeg');
$success = imagejpeg($dest, "../".$_POST['newName']);
//$success = imagepng($dest, $_POST['fileName']);

imagedestroy($dest);
imagedestroy($src);
echo $success ? $_POST['fileName'] : 'Unable to save the file.';
/*
  // Create image instances
  $src = imagecreatefromjpeg('test.jpg');
  $dest = imagecreatetruecolor(100, 100);

  // Copy
  imagecopy($dest, $src, 392, 100, 492, 200, 100, 100);

  // Output and free from memory
  header('Content-Type: image/jpeg');
  imagejpeg($dest);

  imagedestroy($dest);
  imagedestroy($src); */
?>

