<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
</head>
<?php 
$img = base64_decode($_REQUEST['imgName']);
?>
<body id="dt_example">
<div id="container">
    <div class="demo_jui">
        <img src="<?=$img?>" width="830px">
    </div>
</div>
<br clear="all" />
</body>
</html>