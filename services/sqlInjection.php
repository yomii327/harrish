<?php
//Remove all unathorized query from the search query 
function queryFilter($str){
	if(get_magic_quotes_gpc()){
		$str = stripslashes($str);
	}
	$removeWords = array("/delete/i", "/update/i","/union/i","/insert/i","/drop/i","/http/i","/--/i");
	$str = preg_replace($removeWords, "", $str);
	if (phpversion() >= '4.3.0'){
		$str = mysql_real_escape_string($str);
	}else{
		$str = mysql_escape_string($str);
	}
	return $str;
}
?>